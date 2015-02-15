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

interface readFile {
	public function readCSVFromFile();
}
class ReadFiles implements readFile {
	protected $felement;
	protected $getid;
	protected $id;
	protected $dbconn;
	protected $db;	
	protected $setdate;
	protected $filename;
	protected $handle;
	protected $row;
	protected $data;
	protected $sethtml;
	
	public function __construct($felement,$setdate,$id) {
		$this->felement = $felement;
		$this->setdate	= $setdate;
		$this->id 		= $id;
  		
  		// set path to and name for csv file
    	if ($this->id == '0') {
    		$this->filename = "docs/{$this->felement}_{$this->setdate}.csv";
    	} else {
    		$this->filename = "docs/{$this->felement}_{$this->id}.csv";
    	}
	}
	
	public function readCSVFromFile() {
		// do we have a valid file
		if (file_exists($this->filename)) {
			$this->sethtml = "<table width=\"200\" border=\"1\" cellspacing=\"2\" cellpadding=\"0\">";
			// set row counter to 1
    		$this->row = 1;
			if (($this->handle = fopen($this->filename, "r")) !== FALSE) {
    			while (($this->data = fgetcsv($this->handle, 1000, ",")) !== FALSE) {
        			$this->num = count($this->data);
        			$this->sethtml .= "<tr>";
        			// add more rows to counter
        			$this->row++;
        			for ($this->c=0; $this->c < $this->num; $this->c++) {
        				if($this->row ==  '2') {
            				$this->sethtml .= "<td>".ucfirst($this->data[$this->c])."</td>";
        				} else {
        					$this->sethtml .= "<td>".$this->data[$this->c]."</td>";
        				}
        			}
        			$this->sethtml .= "</tr>";
    			}
    			fclose($this->handle);
			}
			$this->sethtml .= "</table>";
    		print $this->sethtml;
		} else {
			print "CSV file does not exist!<br/>";
		}
    }
}
?>