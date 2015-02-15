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

interface processFile {
	public function processCSVFromFile();
	public function quoteData($values);
}
class ProcessFiles implements processFile {
	protected $felement;
	protected $getid;
	protected $id;
	protected $dbconn;
	protected $db;	
	protected $setdate;
	protected $filename;
	protected $max_line_len;
	
	protected $handle;
	protected $row;
	protected $data;
	protected $columns;
	protected $column;
	protected $query_prefix;
	protected $query;	
	protected $key;
	protected $values;
	protected $value;
	protected $success;
	
	public function __construct($felement,$setdate,$id) {
		$this->felement = $felement;
		$this->setdate	= $setdate;
		$this->id 		= $id;
  		
		// set connection to database
		$this->dbconn = new DBConn();
  		$this->dbexport = $this->dbconn->connectDBExport();
  		
  		$this->max_line_len = '10000';
		
  		// set path and file name for csv file
    	if ($this->id == '0') {
    		$this->filename = "docs/{$this->felement}_{$this->setdate}.csv";
    	} else {
    		$this->filename = "docs/{$this->felement}_{$this->id}.csv";
    	}
	}
	
	public function processCSVFromFile() {
	
    	if (($this->handle = fopen("$this->filename", "r")) !== FALSE) { 
        	$this->columns = fgetcsv($this->handle, $this->max_line_len, ",");
        	
        	$this->query_prefix = "INSERT INTO tb_orders (".join(",",$this->columns).")\nVALUES";
         	while (($this->data = fgetcsv($this->handle, $this->max_line_len, ",")) !== FALSE) {
            	while (count($this->data)<count($this->columns))
                	array_push($this->data, NULL); 
            		$this->query = "$this->query_prefix (".join(",",$this->quoteData($this->data)).");";
            		$this->dbexport->query($this->query); 
        	} 
        	fclose($this->handle);
        	if($this->dbexport->affected_rows > '0') {
        		$this->success = "Data from CSV file could be transferred!<br/>";
        	}  else {
        		$this->success = "Data from CSV file could not be transferred!<br/>";
        	}
    	} else {
    		$this->success = "Data from CSV file could not be transferred!<br/>";
    	}
    	print $this->success;
	}
	
	public function quoteData($values) {
		$this->values = $values;
		
		if (is_array($this->values)) {
    		foreach ($this->values as $this->key=>$this->value) {
    			if($this->key == '0') {
    				$this->values[$this->key] = 'null';
    			} else {
            		$this->values[$this->key] = "'" . $this->dbexport->real_escape_string($this->value) . "'"; 
    			}
        	}
		}        	
    	return $this->values; 
	}

}
?>