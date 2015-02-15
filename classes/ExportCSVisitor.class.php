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

interface exportCSVisitors {
	public function getCSVDataFromTB($csvdoc);
}
class ExportCSVisitor extends DebugVisitor implements exportCSVisitors {
	protected $felement;
	protected $id;
	protected $dbconn;
	protected $db;	
	protected $setdate;
	protected $filename;
	protected $fp;
	
	protected $csvdoc;
	
	public function __construct($felement,$id) {
		$this->felement = $felement;
		$this->id 		= $id;
		
		$this->dbconn = new DBConn();
  		$this->dbexport = $this->dbconn->connectDBExport();
		
  		$this->setdate 	= date('Y-m-d');
  		
  		// set path and name to csv file
    	if ($this->id == '0') {
    		$this->csvdoc = "docs/{$this->felement}_{$this->setdate}.csv";
    	} else {
    		$this->csvdoc = "docs/{$this->felement}_{$this->id}.csv";
    	}		
	}
	
	public function getCSVDataFromTB($csvdoc) {
		$this->filename = $csvdoc;
		return $this->getCSVFromTB($this->filename);
	}
	
	public function saveAsCSV() {
		// save csv file
		return $this->save($this->csvdoc);
	}
	
	public function save($csvdoc) {
		$this->csvdoc 	= $csvdoc;
		$this->getCSVDataFromTB($this->csvdoc);
	}
    
    public function accept() {    	
    	// prepare data for processing
    	$this->saveAsCSV();
    	print "CSV <a href=\"{$this->csvdoc}\" target=\"_blank\">file</a> has been saved!<br/>";   	
	}
	
}
?>