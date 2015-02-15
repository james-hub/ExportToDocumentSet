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

interface exportDocVisitors {
	public function writeHandler($exportdoc);
	public function loadDoc($doc);
	public function getDocDataFromTB($doc);	
}
class ExportDocVisitor extends DebugVisitor implements exportDocVisitors {
	protected $template;
	protected $templateval;
	protected $validtemplate;
	protected $id;
	protected $dbconn;
	protected $db;
	protected $dbexport;	
	protected $setdate;
	protected $file;
	protected $filename;
	protected $filesarr;
	protected $fp;	
	protected $doc;
	protected $exportdoc;
	protected $htmlpos;
	protected $opts;
	protected $context;
	protected $tbname;
	protected $validtbname;
	protected $validoc;
	protected $headerbodystartpos;
	protected $headerstartdoc;
	protected $headerbodyendpos;
	protected $headerenddoc;
	protected $headerdoc;
	protected $startpos;
	protected $replarr;
	protected $htmldoc;
	protected $filend;
	protected $validsql;
	
	public function __construct($template,$tbname,$id,$filend) {
		$this->template 	= $template;
		$this->validtbname 	= $tbname;
		$this->id 			= $id;
		$this->filend 		= $filend;
		$this->filesarr 	= glob("templates/*.*");
		
		$this->validsql = $this->preventFromSqlInjection($this->validtbname);
		$this->tbname 	= $this->validsql;
		
		$this->dbconn 	= new DBConn();
  		$this->dbexport = $this->dbconn->connectDBExport();
  		
  		// valid and set path and name to doc file
    	if (file_exists("templates/{$this->template}.htm")
    		|| file_exists("templates/{$this->template}.html")) {
    			if(is_array($this->filesarr) && count($this->filesarr) > 0) {
    				foreach ($this->filesarr as $this->templateval) {
    					$this->htmlpos 			= strpos(substr($this->templateval,10),'.');
    					$this->validtemplate 	= substr($this->templateval,10,$this->htmlpos);
    					if($this->validtemplate === $this->template) {
    						$this->doc = $this->templateval;    						
    					}
    				}     					
				} else {
					die("Sorry, file could not be loaded!");
				}
    	} else {
    		die("Sorry, template not found!");
    	}		
	}
	
	public function writeHandler($exportdoc) {
		$this->exportdoc = $exportdoc;	
		file_put_contents("docs/{$this->template}{$this->filend}",$this->exportdoc); 
	}
	
	public function loadDoc($doc) {
		$this->doc = $doc;
		$this->file = file_get_contents($this->doc,false,null);
		$this->exportdoc = $this->getDocDataFromTB($this->file);
		
		$this->replarr = array("</body>","</html>");
		
		// open/create doc file and write
		$this->headerbodystartpos = strpos($this->exportdoc[0],"<body");		
		$this->headerstartdoc = substr($this->exportdoc[0],$this->headerbodystartpos);
		
		$this->headerbodyendpos = strpos($this->headerstartdoc,">");
		$this->headerenddoc = substr($this->headerstartdoc,0,$this->headerbodyendpos+1);
		
		$this->htmldoc  = substr($this->exportdoc[0],0,$this->headerbodystartpos);
		$this->htmldoc .= str_replace($this->headerenddoc,$this->headerenddoc."<br clear=all style='page-break-before:always'>",$this->headerenddoc);
		
		if (is_array($this->exportdoc)) {			
			foreach ($this->exportdoc as $this->validoc) {
				$this->startpos = $this->headerbodystartpos+$this->headerbodyendpos;
				$this->htmldoc .= substr(str_replace($this->replarr,"",$this->validoc),$this->startpos+1);
			}
		}
		$this->htmldoc .= "</body></html>";
		$this->writeHandler($this->htmldoc);					
	}
	
	public function getDocDataFromTB($file) {
		$this->filename = $file;
		return $this->getDocFromTB($this->filename,$this->tbname,$this->id);
	}
    
    public function accept() {
    	// prepare data to save doc
    	$this->loadDoc($this->doc);
    	
    	// make sure doc file has been created
    	if (file_exists("docs/{$this->template}{$this->filend}")) {
    		print "Word Document <a href=\"docs/{$this->template}{$this->filend}\" target=\"_blank\">{$this->template}</a> hast been saved!";
    	} else {
    		die("Sorry, file could not be created!");
    	}    	
	}
	
}
?>