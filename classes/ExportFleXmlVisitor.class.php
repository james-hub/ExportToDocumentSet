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

interface exportFleXmlVisitors {
	public function getXmlFileData($att,$recordid,$replaceid);
	public function getFieldsFromData();
	public function getReadyForUpdateData($xmlres2,$xmlres1);
	public function getUpdateData($cmpres);
	public function getAddData();
	
	public function saveAsXML();
	public function accept();
}
class ExportFleXmlVisitor extends DebugVisitor implements exportFleXmlVisitors {
	protected $dbconn;
	protected $db;
	
	protected $setdate;
	protected $setdatetime;
	
	protected $sxe;
	protected $value;
	protected $sxexml;
	protected $sxexmlarr;
	
	protected $att;
	protected $recordid;
	protected $tablename;
	protected $validtbname;
	protected $xmlfile;
	protected $attribute;
	protected $xmlres1;
	protected $xmlres2;
	protected $xmlres3;
	
	protected $resstr;
	protected $compareres;
	protected $cmpres;
	protected $fieldopen;
	protected $replaceid;
	protected $created;
	protected $validsql;
	
	public function __construct($att,$recordid,$tablename,$xmlfile,$replaceid) {
		$this->att 			= $att;
		$this->recordid 	= $recordid;
		$this->validtbname	= $tablename;
		$this->xmlfile		= $xmlfile;
		$this->replaceid	= $replaceid;
		$this->created		= "created";
		
		$this->validsql 	= $this->preventFromSqlInjection($this->validtbname);
		$this->tablename 	= $this->validsql;
		
		$this->setdatetime 	= date('Y-m-d H:i:s');
		
		$this->dbconn = new DBConn();
  		$this->dbexport = $this->dbconn->connectDBExport();
  		
  		$this->fieldopen	= $this->getFieldsFromData();
  		$this->xmlres1 		= $this->getXmlFileData($att,$recordid,$replaceid);
		$this->xmlres2 		= $this->getIdsFromData();
		
		if(count($this->xmlres1) != '0') {
			$this->cmpres	= $this->getReadyForUpdateData($this->xmlres2,$this->xmlres1);
			$this->getUpdateData($this->cmpres);
		} else {
			$this->getAddData();
		}
	}
	
	/* deliver data from xml file */
	public function getXmlFileData($att,$recordid,$replaceid) {
		$this->replaceid = $replaceid;
		
		// open old xml file to add new content if necesario
		if (file_exists("docs/{$this->xmlfile}") && $this->replaceid == '0') {
			$this->sxe = new SimpleXMLElement("docs/{$this->xmlfile}", NULL, true);
			// add attribute if necesario
			$this->attribute = $this->sxe->attributes()->$att;
			if($this->attribute == null) {
				$this->sxe->addAttribute($this->att, $this->tablename);
				$this->sxe->addAttribute($this->created, $this->setdatetime);
			}
			
			// get all ids from xml file
			foreach ($this->sxe->container as $value) {
				$this->sxexmlarr[] = $value->$recordid;
			}
			return $this->sxexmlarr;
		} elseif (file_exists("docs/{$this->xmlfile}") && $this->replaceid == '1') {
			// delete existing file
			@unlink("docs/{$this->xmlfile}");
			// create new xml file
			@file_put_contents("docs/{$this->xmlfile}",'<?xml version="1.0" encoding="UTF-8"?><root></root>');
			$this->sxe = new SimpleXMLElement("docs/{$this->xmlfile}", NULL, true);
			
			// add attribute if necesario
			$this->attribute = $this->sxe->attributes()->$att;
			if($this->attribute == null) {
				$this->sxe->addAttribute($this->att, $this->tablename);
				$this->sxe->addAttribute($this->created, $this->setdatetime);
			}
			return $this->sxe;
		} else {
			// create new xml file
			@file_put_contents("docs/{$this->xmlfile}",'<?xml version="1.0" encoding="UTF-8"?><root></root>');
			$this->sxe = new SimpleXMLElement("docs/{$this->xmlfile}", NULL, true);
			
			// add attribute if necesario
			$this->attribute = $this->sxe->attributes()->$att;
			if($this->attribute == null) {
				$this->sxe->addAttribute($this->att, $this->tablename);
				$this->sxe->addAttribute($this->created, $this->setdatetime);
			}			
			return $this->sxe;
		}		
	}
	
	/* deliver data from db table for xml export */
	public function getFieldsFromData() {
		return $this->getXmlExportFields($this->tablename);
	}
	
	public function getIdsFromData() {
		return $this->getXmlExportIds($this->tablename,$this->recordid);
	}
	
	public function getReadyForUpdateData($xmlres2,$xmlres1) {
		$this->xmlres2 = $xmlres2;
		$this->xmlres1 = $xmlres1;
		
		$this->xmlres3 = array_diff($this->xmlres2,$this->xmlres1);
		if (count($this->xmlres3) > '0') {
			foreach ($this->xmlres3 as $this->value) {
				$this->resstr .= $this->value.",";
			}
			$this->compareres = substr($this->resstr,0,-1);
		} else {
			$this->compareres = '0';
		}
		return $this->compareres;
	}
	
	public function getUpdateData($cmpres) {
		$this->cmpres = $cmpres;
		return $this->updateXmlFile($this->cmpres,$this->recordid,$this->tablename);
	}
	
	public function getAddData() {
		return $this->addDataToXmlFile();
	}
	
	public function saveAsXML() {
		@file_put_contents("docs/{$this->xmlfile}",$this->sxe->asXML());
	}
    
    public function accept() {    	
    	// save xml file
    	$this->saveAsXML();
    	if (file_exists("docs/{$this->xmlfile}")) {
    		print "Xml <a href=\"docs/{$this->xmlfile}\" target=\"_blank\">file</a> has been saved!";
    	} else {
    		die("Sorry, file could not be created!");
    	}    	
	}
	
}
?>