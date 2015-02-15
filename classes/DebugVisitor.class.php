<?php
error_reporting(0);
//error_reporting(E_ALL);

/**
 *
 * ExportToDocumentSet
 *
 * @author Claudio Biesele, <info@fastproject.ch>
 *
 * @copyright 	free
 *
 * Generated 04.10.2012
 *
 */

interface Visitors {
	public function preventFromSqlInjection($validtbname);
	
	public function getCSVFromTB($filename);
	public function getTableFields($tbname);
	public function getTableFieldsCounter($tbname);
	public function getTableOOMinWidth($tbname,$fieldtype);
	public function getTableExcelMinWidth($tbname,$fieldtype);
	public function getTableSQL($tbname);
	public function getTableSQLCounter($tbname);
	
	public function getFieldsFromOODBTable($tbname);
	public function getDataFromOODBTable($tbname);

	public function getFieldsFromExcelDBTable($tbname);
	public function getDataFromExcelDBTable($tbname);
	public function getColumnExcelWidth($counter);
	
	public function getXmlExportFields($tablename);
	public function getXmlExportIds($tablename,$recordid);
	public function updateXmlFile($cmpres,$recordid,$tablename);
	public function addDataToXmlFile();
	
	public function countAllDocRecords();
	public function countSingleDocRecords($id);
	public function getDocFromTB($filename,$tbname,$id);
	
	public function getOdtFromTB($filename,$tbname,$id);
}

class DebugVisitor implements Visitors {
	protected $dbconn;
	protected $db;
	protected $res;
	protected $row;
	protected $rows;
	protected $line;
	protected $comma;
	protected $handle;
	protected $data;
	protected $num;
	protected $c;
	protected $tbname;
	protected $fields;
	protected $fieldrecs;
	protected $fieldstype;
	protected $fieldtype;
	protected $fieldsarr;
	protected $field;

	protected $links;
	protected $linksrepl;
	protected $path;
	protected $pathrepl;
	protected $linkxml;
	protected $reportxml;
	protected $key;
	protected $val;

	protected $id;
	protected $reportid;
	protected $memberid;
	protected $missionid;
	protected $prioid;
	protected $missiondate;
	protected $title;
	protected $txt;
	protected $sendto;
	protected $fdate;
	protected $ldate;

	protected $number;
	protected $filename;
	protected $fp;
	protected $showhtml;
	protected $firstval;
	protected $secondval;
	protected $counter;
	protected $countarr;
	protected $value;
	protected $setwidth;
	protected $docoo;
	protected $docos;
	protected $width;
	protected $doctextcont;
	
	protected $inttype;
	protected $calcwidth;
	protected $vartype;
	protected $floattype;
	protected $texttype;
	protected $datetimetype;
	protected $datetype;
	protected $textnode;
	
	protected $tablename;
	protected $idarr;
	protected $cmpres;
	protected $recordid;
	protected $container;
	protected $fieldopen;
	protected $toreplarr;
	protected $replwitharr;
	protected $replacements;
	protected $replaceit;
	protected $prepfilearr;
	
	protected $counted;
	protected $count;
	protected $pagebreak;
	protected $i;
	protected $lettersender;
	protected $address;
	protected $datumtxt;
	protected $content;
	protected $dear;
	protected $changepath;
	protected $validtbname;
	protected $forbidden;
	protected $notallowed;

	private function __construct() {
		// ---
	}
	
	public function preventFromSqlInjection($validtbname) {
		$this->validtbname = $validtbname;
		
		$this->forbidden = array(";","/*","1=1"," '1' OR '1' = '1' ","NULL","DELETE FROM","WHERE","\n","\r");
		
		foreach ($this->forbidden as $this->notallowed) {
			if(stristr($this->validtbname,$this->notallowed)) {				
				exit();
			}
		}
		return $this->validtbname;
	}

	public function getCSVFromTB($filename) {
		$this->filename = $filename;
		$this->fp 		= fopen($this->filename, "w");

		$this->res = $this->dbexport->query("SELECT * FROM tb_orders");

		// fetch a row and write the column names out to the file
		$this->row = $this->res->fetch_assoc();
		$this->line = "";
		$this->comma = "";
		foreach($this->row as $this->key=>$this->val) {
			$this->line .= $this->comma . '"' . str_replace('"', '""', $this->key) . '"';
			$this->comma = ",";
		}
		$this->line .= "\n";
		fputs($this->fp, $this->line);

		if (file_exists($this->filename)) {
			// loop through the actual data
			while($this->row = $this->res->fetch_assoc()) {

				$this->line = "";
				$this->comma = "";
				foreach($this->row as $this->val) {
					$this->line .= $this->comma . '"' . str_replace('"', '""', $this->val) . '"';
					$this->comma = ",";
				}
				$this->line .= "\n";
				fputs($this->fp, $this->line);
			}
		}
		$this->dbexport->close();
		fclose($this->fp);
	}

	public function getTableFields($tbname) {
		$this->tbname = $tbname;
		// prepare field names
		$this->fields = $this->dbexport->query("SHOW FIELDS FROM $this->tbname");
		return $this->fields;
	}
	
	public function getTableFieldsCounter($tbname) {
		$this->tbname = $tbname;
		// prepare field names for counting
		$this->res = $this->dbexport->query("SHOW FIELDS FROM $this->tbname");
		while ($this->rows = $this->res->fetch_assoc()) {
			$this->row[] = $this->rows;
		}
		$this->counter = count($this->row);
		return $this->counter;
	}
	
	public function getTableType($tbname) {
		$this->tbname 	= $tbname;
		
		// prepare type names for calculating width of column
		$this->res = $this->dbexport->query("SHOW FIELDS FROM $this->tbname");
		
		while ($this->field = $this->res->fetch_assoc()) {
			$this->fields		= $this->field["Type"];
    		$this->fieldstype[] = $this->fields;
		}
		return $this->fieldstype;
	}
	
	public function getTableOOMinWidth($tbname,$fieldtype) {
		$this->tbname 	= $tbname;
		$this->fieldtype= $fieldtype;

		$this->inttype 		= 'int';
		$this->vartype 		= 'varchar';
		$this->floattype	= 'float';
		$this->texttype		= 'text';
		$this->datetimetype	= 'datetime';
		$this->datetype		= 'date';
		
		// prepare field names for calculating width of column
		$this->restype = $this->dbexport->query("SHOW FIELDS FROM $this->tbname 
										      WHERE Type LIKE '%$this->fieldtype%'");
		
		while ($this->field = $this->restype->fetch_assoc()) {
			$this->fields = $this->field["Field"];
		}
		// prepare int field name for ORDER BY
		$this->resid = $this->dbexport->query("SHOW FIELDS 
											   FROM $this->tbname 
											   WHERE Extra = 'auto_increment' 
											   AND Type LIKE '%int%';");
		
		while ($this->resint = $this->resid->fetch_assoc()) {
			$this->orderint = $this->resint["Field"];
		}
		// prepare length values for columnss
		$this->resfield = $this->dbexport->query("SELECT DISTINCT LENGTH(MAX($this->fields)) 
												AS widthstr 
												FROM $this->tbname 
												ORDER BY $this->orderint DESC");
		
		while ($this->row = $this->resfield->fetch_assoc()) {
			if(strpos($this->fieldtype,$this->inttype) !== false) {
				$this->calcwidth = (int)$this->row['widthstr']+14;
			}
			if(strpos($this->fieldtype,$this->vartype) !== false) {
				$this->calcwidth = (int)$this->row['widthstr']+40;
			}
			if($this->fieldtype == $this->floattype) {
				$this->calcwidth = 20;
			}
			if($this->fieldtype == $this->texttype) {
				$this->calcwidth = (int)$this->row['widthstr']+80;
			}
			if($this->fieldtype == $this->datetimetype) {
				$this->calcwidth = 36;
			}
			if($this->fieldtype == $this->datetype) {
				$this->calcwidth = 20;
			}
			if ($this->calcwidth != null && $this->calcwidth > 0) {
				$this->width = $this->calcwidth;
			}
		}		
		return $this->width;
	}

	public function getTableExcelMinWidth($tbname,$fieldtype) {
		$this->tbname 	= $tbname;
		$this->fieldtype= $fieldtype;

		$this->inttype 		= 'int';
		$this->vartype 		= 'varchar';
		$this->floattype	= 'float';
		$this->texttype		= 'text';
		$this->datetimetype	= 'datetime';
		$this->datetype		= 'date';
		
		// prepare field names for calculating width of column
		$this->restype = $this->dbexport->query("SHOW FIELDS FROM $this->tbname 
										      WHERE Type LIKE '%$this->fieldtype%'");
		
		while ($this->field = $this->restype->fetch_assoc()) {
			$this->fields = $this->field["Field"];
			
		}
		// prepare int field name for ORDER BY
		$this->resid = $this->dbexport->query("SHOW FIELDS 
											   FROM $this->tbname 
											   WHERE Extra = 'auto_increment' 
											   AND Type LIKE '%int%';");
		
		while ($this->resint = $this->resid->fetch_assoc()) {
			$this->orderint = $this->resint["Field"];
		}
		
		// prepare length values for columnss
		$this->resfield = $this->dbexport->query("SELECT DISTINCT LENGTH(MAX($this->fields)) 
												AS widthstr 
												FROM $this->tbname 
												ORDER BY $this->orderint DESC");
		
		while ($this->row = $this->resfield->fetch_assoc()) {
			if(strpos($this->fieldtype,$this->inttype) !== false) {
				$this->calcwidth = round($this->row['widthstr']+40);
			}
			if(strpos($this->fieldtype,$this->vartype) !== false) {
				$this->calcwidth = round($this->row['widthstr']+100);				
			}
			if($this->fieldtype == $this->floattype) {
				$this->calcwidth = round($this->row['widthstr']+40);
			}
			if($this->fieldtype == $this->texttype) {				
				$this->calcwidth = round($this->row['widthstr']+300);				
			}
			if($this->fieldtype == $this->datetimetype) {
				$this->calcwidth = round($this->row['widthstr']+80);
			}
			if($this->fieldtype == $this->datetype) {
				$this->calcwidth = round($this->row['widthstr']+60);
			}
			if ($this->calcwidth != null && $this->calcwidth > 0) {
				$this->width = $this->calcwidth;
			}
		}		
		return $this->width;
	}

	public function getTableSQL($tbname) {
		$this->tbname = $tbname;
		// prepare record values
		$this->data = $this->dbexport->query("SELECT * FROM $this->tbname");
		return $this->data;
	}

	public function getTableSQLCounter($tbname) {
		$this->tbname = $tbname;

		// count record values
		$this->res = $this->dbexport->query("SELECT * FROM $this->tbname ");
		while ($this->rows = $this->res->fetch_assoc()) {
			$this->row[] = $this->rows;
		}
		$this->counter = count($this->row);
		return $this->counter;
	}

	public function getFieldsFromOODBTable($tbname) {
		$this->tbname = $tbname;
    	$i = '1';
    	
		// prepare field names
		$this->fields = $this->getTableFields($this->tbname);

    	while ($this->row = $this->fields->fetch_assoc()) {
    		$field = ucfirst($this->row['Field']);
    		
    		$this->doccol = $this->docos->createElement('table:table-column');
    		$this->doccol->setAttribute('table:style-name',"co{$i}");
    		$this->doccol->setAttribute('table:default-cell-style-name',"Default");
    		$this->doctable->appendChild($this->doccol);		
    		
    		$this->doccell = $this->docos->createElement('table:table-cell');
    		$this->doccell->setAttribute('table:style-name',"ce1");
    		$this->doccell->setAttribute('office:value-type',"string");    		
    		
    		$this->doctext = $this->docos->createElement('text:p');
    		$this->doctext_val = $this->docos->createTextNode($field);
			$this->doctext->appendChild($this->doctext_val);
			
			$this->doccell->appendChild($this->doctext);
			$this->docrowheader->appendChild($this->doccell);
			$i++;
    	}
	}
	
	public function getDataFromOODBTable($tbname) {
    	$this->tbname 	= $tbname;
    	
    	// prepare sql
    	$this->data = $this->getTableSQL($this->tbname);
		// prepare field names
		$this->fieldrecs = $this->getTableFields($this->tbname);
		
		// prepare table fields for loop
    	while ($this->field = $this->fieldrecs->fetch_assoc()) {
    		$this->fields 		= $this->field["Field"];
    		$this->fieldsarr[] 	= $this->fields;
    	}
    	
    	// get records for loop
    	while($this->row = $this->data->fetch_assoc()) {
    		$this->docrows = $this->docos->createElement('table:table-row');
    		$this->docrows->setAttribute('table:style-name',"ro1");
    		
    		foreach($this->fieldsarr as $this->key=>$this->value) {
    			// valid integer records
    			if(preg_match("/^[0-9]+$/", $this->row[$this->value])) {
    				$this->doccell = $this->docos->createElement('table:table-cell');
    				$this->doccell->setAttribute('table:style-name',"ce2");
    				$this->doccell->setAttribute('office:value-type',"float");
    				$this->doccell->setAttribute('office:value',"{$this->row[$this->value]}");
    			
    				$this->doctext = $this->docos->createElement('text:p');
    				$this->doctext_val = $this->docos->createTextNode($this->row[$this->value]);
					$this->doctext->appendChild($this->doctext_val);
				
					$this->doccell->appendChild($this->doctext);
					$this->docrows->appendChild($this->doccell);
    			} 
    			// valid float records
    			elseif(preg_match("/^[0-9]+\.[0-9]+/", $this->row[$this->value])) {
    				$this->firstval = sprintf("%01.2f", $this->row[$this->value]);
    				$this->secondval = str_replace(".", ",", sprintf("%01.2f", $this->row[$this->value]));
    				
    				$this->doccell = $this->docos->createElement('table:table-cell');
    				$this->doccell->setAttribute('table:style-name',"ce2");
    				$this->doccell->setAttribute('office:value-type',"float");
    				$this->doccell->setAttribute('office:value',"{$this->firstval}");
    			
    				$this->doctext = $this->docos->createElement('text:p');
    				$this->doctext_val = $this->docos->createTextNode("{$this->secondval}");
					$this->doctext->appendChild($this->doctext_val);
				
					$this->doccell->appendChild($this->doctext);
					$this->docrows->appendChild($this->doccell);
    			} 
    			// valid http records
				elseif(preg_match("/^www./", $this->row[$this->value])) {
					$this->firstval = htmlspecialchars(utf8_encode(stripslashes($this->row[$this->value])));
    				
    				$this->doccell = $this->docos->createElement('table:table-cell');
    				$this->doccell->setAttribute('table:style-name',"ce4");
    				$this->doccell->setAttribute('office:value-type',"string");    		
    		
    				$this->doctextcont = $this->docos->createElement('text:p');
    				$this->doctext = $this->docos->createElement('text:a');
    				$this->doctext->setAttribute("xlink:type", "simple");
    				$this->doctext->setAttribute("xlink:href", "http://".$this->firstval);
    				
    				$this->doctext_val = $this->docos->createTextNode($this->firstval);
					$this->doctext->appendChild($this->doctext_val);
			
					$this->doctextcont->appendChild($this->doctext);
					$this->doccell->appendChild($this->doctextcont);
					$this->docrows->appendChild($this->doccell);
				}
				// valid email records
				elseif(preg_match("/@/", $this->row[$this->value])) {
					$this->firstval = htmlspecialchars(utf8_encode(stripslashes($this->row[$this->value])));
    				
    				$this->doccell = $this->docos->createElement('table:table-cell');
    				$this->doccell->setAttribute('table:style-name',"ce4");
    				$this->doccell->setAttribute('office:value-type',"string");    		
    		
    				$this->doctextcont = $this->docos->createElement('text:p');
    				$this->doctext = $this->docos->createElement('text:a');
    				$this->doctext->setAttribute("xlink:type", "simple");
    				$this->doctext->setAttribute("xlink:href", "mailto:".$this->firstval);
    				
    				$this->doctext_val = $this->docos->createTextNode($this->firstval);
					$this->doctext->appendChild($this->doctext_val);
			
					$this->doctextcont->appendChild($this->doctext);
					$this->doccell->appendChild($this->doctextcont);
					$this->docrows->appendChild($this->doccell);
				}
    			// valid string records
    			else {
    				$this->firstval = htmlspecialchars(utf8_encode(stripslashes($this->row[$this->value])));
    				
    				$this->doccell = $this->docos->createElement('table:table-cell');
    				$this->doccell->setAttribute('table:style-name',"ce3");
    				$this->doccell->setAttribute('office:value-type',"string");
    		
    				$this->doctext = $this->docos->createElement('text:p');
    				$this->doctext_val = $this->docos->createTextNode($this->firstval);
					$this->doctext->appendChild($this->doctext_val);
			
					$this->doccell->appendChild($this->doctext);
					$this->docrows->appendChild($this->doccell);
    			}	
    		}
    		$this->doctable->appendChild($this->docrows);
    	}
    }
	
	public function getFieldsFromExcelDBTable($tbname) {
		$this->tbname = $tbname;

		// prepare field names
		$this->fields = $this->getTableFields($this->tbname);

		while ($this->row = $this->fields->fetch_assoc()) {
			$field = ucfirst($this->row['Field']);

			$this->doccell = $this->docxls->createElement('Cell');
			$this->doccell->setAttribute('ss:StyleID',"s28");
			$this->table->appendChild($this->doccell);

			$this->docdata = $this->docxls->createElement('Data');
			$this->docdata->setAttribute('ss:Type',"String");
			$this->docdata_val = $this->docxls->createTextNode($field);
			$this->docdata->appendChild($this->docdata_val);

			$this->doccell->appendChild($this->docdata);
			$this->docxlsheader->appendChild($this->doccell);
		}
	}

	public function getDataFromExcelDBTable($tbname) {
		$this->tbname = $tbname;

		// prepare sql
		$this->data = $this->getTableSQL($this->tbname);
		// prepare field names
		$this->fieldrecs = $this->getTableFields($this->tbname);
		// prepare field types
		$this->settype	= $this->getTableType($this->tbname);
    	
		$this->inttype 		= 'int';
		$this->vartype 		= 'varchar';
		$this->floattype	= 'float';
		$this->texttype		= 'text';
		$this->datetimetype	= 'datetime';
		$this->datetype		= 'date';

		// prepare table fields for loop
		while ($this->field = $this->fieldrecs->fetch_assoc()) {
			$this->fields 		= $this->field["Field"];
			$this->fieldsarr[] 	= $this->fields;
		}

		// get records for loop
		while($this->row = $this->data->fetch_assoc()) {
			$this->docrows = $this->docxls->createElement('Row');

			foreach($this->fieldsarr as $this->key=>$this->value) {

				// valid integer records
				if(preg_match("/^[0-9]+$/", $this->row[$this->value])) {
					$this->doccell = $this->docxls->createElement('Cell');
					$this->doccell->setAttribute('ss:StyleID',"s26");
					$this->table->appendChild($this->doccell);

					$this->docdata = $this->docxls->createElement('Data');
					$this->docdata->setAttribute('ss:Type',"Number");
					$this->docdata_val = $this->docxls->createTextNode($this->row[$this->value]);
					$this->docdata->appendChild($this->docdata_val);

					$this->doccell->appendChild($this->docdata);
					$this->docrows->appendChild($this->doccell);
				}
				// valid email records
				elseif(preg_match("/@/", $this->row[$this->value])) {
					$this->doccell = $this->docxls->createElement('Cell');
					$this->doccell->setAttribute('ss:StyleID',"s18");
					$this->doccell->setAttribute('ss:HRef',"mailto:{$this->row[$this->value]}");
					$this->table->appendChild($this->doccell);

					$this->docdata = $this->docxls->createElement('Data');
					$this->docdata->setAttribute('ss:Type',"String");
					$this->docdata_val = $this->docxls->createTextNode($this->row[$this->value]);
					$this->docdata->appendChild($this->docdata_val);

					$this->doccell->appendChild($this->docdata);
					$this->docrows->appendChild($this->doccell);
				}
				// valid http records
				elseif(preg_match("/^www./", $this->row[$this->value])) {					
					$this->doccell = $this->docxls->createElement('Cell');
					$this->doccell->setAttribute('ss:StyleID',"s18");
					$this->doccell->setAttribute('ss:HRef',"http://{$this->row[$this->value]}");
					$this->table->appendChild($this->doccell);					

					$this->docdata = $this->docxls->createElement('Data');
					$this->docdata->setAttribute('ss:Type',"String");
					$this->docdata_val = $this->docxls->createTextNode($this->row[$this->value]);
					$this->docdata->appendChild($this->docdata_val);

					$this->doccell->appendChild($this->docdata);
					$this->docrows->appendChild($this->doccell);
				}
				// valid float records
				else if(preg_match("/^[0-9]+\.[0-9]+/", $this->row[$this->value])) {
					$this->doccell = $this->docxls->createElement('Cell');
					$this->doccell->setAttribute('ss:StyleID',"s25");
					$this->table->appendChild($this->doccell);

					$this->docdata = $this->docxls->createElement('Data');
					$this->docdata->setAttribute('ss:Type',"Number");
					$this->docdata_val = $this->docxls->createTextNode(number_format($this->row[$this->value], 2, '.', ''));
					$this->docdata->appendChild($this->docdata_val);

					$this->doccell->appendChild($this->docdata);
					$this->docrows->appendChild($this->doccell);
				}
				// valid string records
				else {
					if($this->settype[$this->key] == $this->texttype) {
						$this->doccell = $this->docxls->createElement('Cell');
						$this->doccell->setAttribute('ss:StyleID',"s27");
						$this->table->appendChild($this->doccell);

						$this->docdata = $this->docxls->createElement('Data');
						$this->docdata->setAttribute('ss:Type',"String");
						$this->docdata_val = $this->docxls->createTextNode(utf8_encode($this->row[$this->value]));
						$this->docdata->appendChild($this->docdata_val);

						$this->doccell->appendChild($this->docdata);
						$this->docrows->appendChild($this->doccell);
					} else {
						$this->doccell = $this->docxls->createElement('Cell');
						$this->table->appendChild($this->doccell);

						$this->docdata = $this->docxls->createElement('Data');
						$this->docdata->setAttribute('ss:Type',"String");
						$this->docdata_val = $this->docxls->createTextNode(utf8_encode($this->row[$this->value]));
						$this->docdata->appendChild($this->docdata_val);

						$this->doccell->appendChild($this->docdata);
						$this->docrows->appendChild($this->doccell);
					}
				}
			}
			$this->table->appendChild($this->docrows);
		}
	}

	public function getColumnExcelWidth($counter) {
		$this->counter = $counter;
		
		// prepare field types
		$this->settype	= $this->getTableType($this->tbname);
		
		$this->basicparam 	= '4';
    	
		$this->inttype 		= 'int';
		$this->vartype 		= 'varchar';
		$this->floattype	= 'float';
		$this->texttype		= 'text';
		$this->datetimetype	= 'datetime';
		$this->datetype		= 'date';
		
		for ($this->i = 1; $this->i <= $this->counter; $this->i++) {
			if(strpos($this->settype[$this->i-1],$this->inttype) !== false) {
				$this->width = $this->getTableExcelMinWidth($this->tbname,$this->inttype);
			}
			if(strpos($this->settype[$this->i-1],$this->vartype) !== false) {
				$this->width = $this->getTableExcelMinWidth($this->tbname,$this->vartype);
			}
			if(strpos($this->settype[$this->i-1],$this->floattype) !== false) {
				$this->width = $this->getTableExcelMinWidth($this->tbname,$this->floattype);
			}
			if($this->settype[$this->i-1] == $this->texttype) {
				$this->width = $this->getTableExcelMinWidth($this->tbname,$this->texttype);
			}
			if($this->settype[$this->i-1] == $this->datetimetype) {
				$this->width = $this->getTableExcelMinWidth($this->tbname,$this->datetimetype);
			}
			if($this->settype[$this->i-1] == $this->datetype) {
				$this->width = $this->getTableExcelMinWidth($this->tbname,$this->datetype);
			}
			$this->column = $this->docxls->createElement('Column');
    		$this->column->setAttribute('ss:AutoFitWidth',"0");
    		$this->column->setAttribute('ss:Width',"{$this->width}");
    		$this->column->setAttribute('ss:StyleID',"s27");
    		$this->table->appendChild($this->column);
    		$this->docworkxls->appendChild($this->table);
		}	
	}
	
	public function getXmlExportFields($tablename) {
		$this->tablename = $tablename;
		
		$this->res = $this->dbexport->query("SHOW FIELDS FROM {$this->tablename}");
		while ($this->row = $this->res->fetch_assoc()) {
			$this->fieldsarr[] = strtolower($this->row["Field"]);
		}
		return $this->fieldsarr;		
	}
	
	public function getXmlExportIds($tablename,$recordid) {
		$this->tablename = $tablename;
		$this->recordid		 = $recordid;

		$this->res = $this->dbexport->query("SELECT * FROM {$this->tablename}");
		while ($this->row = $this->res->fetch_assoc()) {
			$this->idarr[] = $this->row[$this->recordid];
		}
		return $this->idarr;
	}
	
	public function updateXmlFile($cmpres,$recordid,$tablename) {
		$this->cmpres 		= $cmpres;
		$this->recordid 	= $recordid;
		$this->tablename= $tablename;
		
		$this->res = $this->dbexport->query("SELECT * FROM $this->tablename WHERE $this->recordid IN ($this->compareres)");
		while ($this->row = $this->res->fetch_assoc()) {
			// add container element to every record
			$this->container = $this->sxe->addChild('container');
			// add record to container
			foreach ($this->fieldopen as $this->key=>$this->value) {
				$this->container->addChild($this->fieldopen[$this->key], $this->row[$this->value]);
			}
		}
	}
	
	public function addDataToXmlFile() {		
		$this->res = $this->dbexport->query("SELECT * FROM {$this->tablename}");
		while ($this->row = $this->res->fetch_assoc()) {
			// add container element to every record
			$this->container = $this->sxe->addChild('container');
			// add record to container
			foreach ($this->fieldopen as $this->key=>$this->value) {
				$this->container->addChild($this->fieldopen[$this->key], $this->row[$this->value]);
			}
		}
	}
	
	public function countAllDocRecords() {
		$this->res = $this->dbexport->query("SELECT COUNT(letter_id) AS counter FROM {$this->tbname}");
		while ($this->row = $this->res->fetch_assoc()) {
			$this->counted = $this->row["counter"];
		}
		return $this->counted;
	}
	
	public function countSingleDocRecords($id) {
		$this->id = $id;
		
		$this->res = $this->dbexport->query("SELECT COUNT(letter_id) AS counter FROM {$this->tbname} 
											WHERE letter_id IN ($this->id)");
		while ($this->row = $this->res->fetch_assoc()) {
			$this->counted = $this->row["counter"];
		}
		return $this->counted;
	}
	
	public function getDocFromTB($filename,$tbname,$id) {
		$this->filename 	= $filename;
		$this->tbname		= $tbname;
		$this->id			= $id;
		
		$this->changepath	= "src=\"../templates/"; // offline use
		//$this->changepath	= "src=\"http://www.fastproject.ch/ExportToDocSet/exportdoc/templates/"; // online use
		
		if ($this->id == '0' && $this->id != 'x') {
			$this->count = $this->countAllDocRecords();
			if($this->count > '0') {
				$this->res = $this->dbexport->query("SELECT l.*,s.letter_sender,a.letter_address 
													FROM {$this->tbname} l 
													INNER JOIN tb_sender s
													ON l.sender_id = s.sender_id
													INNER JOIN tb_addresses a
													ON l.address_id = a.address_id");
				$this->i = '1';
				while ($this->row = $this->res->fetch_assoc()) {
					if ($this->i < $this->count) {
						$this->pagebreak = "<p class=MsoNormal style='page-break-before:always'>&nbsp;</p>";
					}
					if ($this->i == $this->count) {
						$this->pagebreak = null;
					}
					$this->toreplarr 	= array("src=\"","xsender","xurl","xdate","xaddress","xemail","xconcerns","xdear","xcontent","xsalutation","xownname","xattachments");
					$this->replwitharr 	= array("{$this->changepath}",
									"{$this->row["letter_sender"]}",
									"{$this->row["letter_url"]}",
									"{$this->row["letter_date"]}",
									"{$this->row["letter_address"]}",
									"{$this->row["letter_email"]}",
									"{$this->row["letter_concerns"]}",
									"{$this->row["letter_dear"]}",
									"{$this->row["letter_content"]}",
									"{$this->row["letter_greetings"]}",
									"{$this->row["letter_ownames"]}",
									"{$this->row["letter_attachments"]}");
					$this->prepfilearr[] = str_replace($this->toreplarr,$this->replwitharr,$this->filename).$this->pagebreak;
					$this->i++;		
				}
				return $this->prepfilearr;
			} else {
				die("Sorry, data could not be loaded!");
			}
		} elseif ($this->id != '0' && $this->id != 'x') {
			$this->count = $this->countSingleDocRecords($this->id);
			if($this->count > '0') {			
				$this->res = $this->dbexport->query("SELECT l.*,s.letter_sender,a.letter_address 
													FROM {$this->tbname} l 
													INNER JOIN tb_sender s
													ON l.sender_id = s.sender_id
													INNER JOIN tb_addresses a
													ON l.address_id = a.address_id
													WHERE l.letter_id IN ($this->id)");
				$this->i = '1';
				while ($this->row = $this->res->fetch_assoc()) {
					if ($this->i < $this->count && $this->count != '0') {
						$this->pagebreak = "<p class=MsoNormal style='page-break-before:always'>&nbsp;</p>";
					}
					if ($this->i == $this->count) {
						$this->pagebreak = null;
					}
					$this->toreplarr 	= array("src=\"","xsender","xurl","xdate","xaddress","xemail","xconcerns","xdear","xcontent","xsalutation","xownname","xattachments");
					$this->replwitharr 	= array("{$this->changepath}",
									"{$this->row["letter_sender"]}",
									"{$this->row["letter_url"]}",
									"{$this->row["letter_date"]}",
									"{$this->row["letter_address"]}",
									"{$this->row["letter_email"]}",
									"{$this->row["letter_concerns"]}",
									"{$this->row["letter_dear"]}",
									"{$this->row["letter_content"]}",
									"{$this->row["letter_greetings"]}",
									"{$this->row["letter_ownames"]}",
									"{$this->row["letter_attachments"]}");
					$this->prepfilearr[] = str_replace($this->toreplarr,$this->replwitharr,$this->filename).$this->pagebreak;
					$this->i++;
				}
				return $this->prepfilearr;
			} else {
				die("Sorry, data could not be loaded!");
			}
		} else {
			$this->count = $this->countAllDocRecords();
			if($this->count > '0') {			
				$this->res = $this->dbexport->query("SELECT l.letter_id,l.sender_id,
													l.letter_date,l.address_id,l.letter_email,
													l.letter_concerns,l.letter_dear,ll.letter_content,
													l.letter_greetings,l.letter_ownames,
													l.letter_attachments,l.letter_url,
													s.letter_sender,a.letter_address
													FROM {$this->tbname} l
													INNER JOIN tb_sender s
													ON l.sender_id = s.sender_id
													INNER JOIN tb_addresses a
													ON l.address_id = a.address_id
                          							INNER JOIN tb_serialletter ll
													ON ll.serial_id = '1'");
				$this->i = '1';
				while ($this->row = $this->res->fetch_assoc()) {
					if ($this->i < $this->count && $this->count != '0') {
						$this->pagebreak = "<p class=MsoNormal style='page-break-before:always'>&nbsp;</p>";
					}
					if ($this->i == $this->count) {
						$this->pagebreak = null;
					}
					$this->toreplarr 	= array("src=\"","xsender","xurl","xdate","xaddress","xemail","xconcerns","xdear","xcontent","xsalutation","xownname","xattachments");
					$this->replwitharr 	= array("{$this->changepath}",
									"{$this->row["letter_sender"]}",
									"{$this->row["letter_url"]}",
									"{$this->row["letter_date"]}",
									"{$this->row["letter_address"]}",
									"{$this->row["letter_email"]}",
									"{$this->row["letter_concerns"]}",
									"{$this->row["letter_dear"]}",
									"{$this->row["letter_content"]}",
									"{$this->row["letter_greetings"]}",
									"{$this->row["letter_ownames"]}",
									"{$this->row["letter_attachments"]}");
					$this->prepfilearr[] = str_replace($this->toreplarr,$this->replwitharr,$this->filename).$this->pagebreak;
					$this->i++;
				}
				return $this->prepfilearr;
			} else {
				die("Sorry, data could not be loaded!");
			}
		}
	}
	
	public function getOdtFromTB($filename,$tbname,$id) {
		$this->filename 	= $filename;
		$this->tbname		= $tbname;
		$this->id			= $id;
		
		$this->changepath	= "src=\"../templates/"; // offline use
		//$this->changepath	= "src=\"http://www.fastproject.ch/ExportToDocSet/exportodt/templates/"; // online use
		
		if ($this->id == '0' && $this->id != 'x') {
			$this->count = $this->countAllDocRecords();
			if($this->count > '0') {
				$this->res = $this->dbexport->query("SELECT l.*,s.letter_sender,a.letter_address 
													FROM {$this->tbname} l 
													INNER JOIN tb_sender s
													ON l.sender_id = s.sender_id
													INNER JOIN tb_addresses a
													ON l.address_id = a.address_id");
				$this->i = '1';
				while ($this->row = $this->res->fetch_assoc()) {
					$this->lettersender 	= str_ireplace("<br/>","<br>",$this->row["letter_sender"]);
					$this->datumtxt 		= str_ireplace("<br/>","<br>",$this->row["letter_date"]);
					$this->address 			= str_ireplace("<br/>","<br>",$this->row["letter_address"]);
					$this->dear 			= str_ireplace("<br/>","<br>",$this->row["letter_dear"]);					
					$this->content 			= str_ireplace("<br/>","<br>",$this->row["letter_content"]);
					
					if ($this->i < $this->count) {
						$this->pagebreak = "<p style=\"page-break-before: always\"><br><br></p>";
					}
					if ($this->i == $this->count) {
						$this->pagebreak = null;
					}
					$this->toreplarr 	= array("src=\"","xsender","xurl","xdate","xaddress","xemail","xconcerns","xdear","xcontent","xsalutation","xownname","xattachments");
					$this->replwitharr 	= array("{$this->changepath}",
									"{$this->lettersender}",
									"{$this->row["letter_url"]}",
									"{$this->datumtxt}",
									"{$this->address}",
									"{$this->row["letter_email"]}",
									"{$this->row["letter_concerns"]}",
									"{$this->dear}",
									"{$this->content}",
									"{$this->row["letter_greetings"]}",
									"{$this->row["letter_ownames"]}",
									"{$this->row["letter_attachments"]}");
					$this->prepfilearr[] = str_ireplace($this->toreplarr,$this->replwitharr,$this->filename).$this->pagebreak;
					$this->i++;		
				}
				return $this->prepfilearr;
			} else {
				die("Sorry, data could not be loaded!");
			}
		} elseif ($this->id != '0' && $this->id != 'x') {
			$this->count = $this->countSingleDocRecords($this->id);
			if($this->count > '0') {			
				$this->res = $this->dbexport->query("SELECT l.*,s.letter_sender,a.letter_address 
													FROM {$this->tbname} l 
													INNER JOIN tb_sender s
													ON l.sender_id = s.sender_id
													INNER JOIN tb_addresses a
													ON l.address_id = a.address_id
													WHERE l.letter_id IN ($this->id)");
				$this->i = '1';
				while ($this->row = $this->res->fetch_assoc()) {
					$this->lettersender 	= str_ireplace("<br/>","<br>",$this->row["letter_sender"]);
					$this->datumtxt 		= str_ireplace("<br/>","<br>",$this->row["letter_date"]);
					$this->address 			= str_ireplace("<br/>","<br>",$this->row["letter_address"]);
					$this->dear 			= str_ireplace("<br/>","<br>",$this->row["letter_dear"]);					
					$this->content 			= str_ireplace("<br/>","<br>",$this->row["letter_content"]);					
					
					if ($this->i < $this->count && $this->count != '0') {
						$this->pagebreak = "<p style=\"page-break-before: always\"><br><br></p>";
					}
					if ($this->i == $this->count) {
						$this->pagebreak = null;
					}
					$this->toreplarr 	= array("src=\"","xsender","xurl","xdate","xaddress","xemail","xconcerns","xdear","xcontent","xsalutation","xownname","xattachments");
					$this->replwitharr 	= array("{$this->changepath}",
									"{$this->lettersender}",
									"{$this->row["letter_url"]}",
									"{$this->datumtxt}",
									"{$this->address}",
									"{$this->row["letter_email"]}",
									"{$this->row["letter_concerns"]}",
									"{$this->dear}",
									"{$this->content}",
									"{$this->row["letter_greetings"]}",
									"{$this->row["letter_ownames"]}",
									"{$this->row["letter_attachments"]}");
					$this->prepfilearr[] = str_ireplace($this->toreplarr,$this->replwitharr,$this->filename).$this->pagebreak;
					$this->i++;
				}
				return $this->prepfilearr;
			} else {
				die("Sorry, data could not be loaded!");
			}
		} else {
			$this->count = $this->countAllDocRecords();
			if($this->count > '0') {
				$this->res = $this->dbexport->query("SELECT l.letter_id,l.sender_id,
													l.letter_date,l.address_id,l.letter_email,
													l.letter_concerns,l.letter_dear,ll.letter_content,
													l.letter_greetings,l.letter_ownames,
													l.letter_attachments,l.letter_url,
													s.letter_sender,a.letter_address
													FROM {$this->tbname} l
													INNER JOIN tb_sender s
													ON l.sender_id = s.sender_id
													INNER JOIN tb_addresses a
													ON l.address_id = a.address_id
                          							INNER JOIN tb_serialletter ll
													ON ll.serial_id = '1'");
				$this->i = '1';
				while ($this->row = $this->res->fetch_assoc()) {
					$this->lettersender 	= str_ireplace("<br/>","<br>",$this->row["letter_sender"]);
					$this->datumtxt 		= str_ireplace("<br/>","<br>",$this->row["letter_date"]);
					$this->address 			= str_ireplace("<br/>","<br>",$this->row["letter_address"]);
					$this->dear 			= str_ireplace("<br/>","<br>",$this->row["letter_dear"]);					
					$this->content 			= str_ireplace("<br/>","<br>",$this->row["letter_content"]);
					
					if ($this->i < $this->count) {
						$this->pagebreak = "<p style=\"page-break-before: always\"><br><br></p>";
					}
					if ($this->i == $this->count) {
						$this->pagebreak = null;
					}
					$this->toreplarr 	= array("src=\"","xsender","xurl","xdate","xaddress","xemail","xconcerns","xdear","xcontent","xsalutation","xownname","xattachments");
					$this->replwitharr 	= array("{$this->changepath}",
									"{$this->lettersender}",
									"{$this->row["letter_url"]}",
									"{$this->datumtxt}",
									"{$this->address}",
									"{$this->row["letter_email"]}",
									"{$this->row["letter_concerns"]}",
									"{$this->dear}",
									"{$this->content}",
									"{$this->row["letter_greetings"]}",
									"{$this->row["letter_ownames"]}",
									"{$this->row["letter_attachments"]}");
					$this->prepfilearr[] = str_ireplace($this->toreplarr,$this->replwitharr,$this->filename).$this->pagebreak;
					$this->i++;		
				}
				return $this->prepfilearr;
			} else {
				die("Sorry, data could not be loaded!");
			}
		}	
	}

}
?>