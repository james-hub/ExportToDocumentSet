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

interface exportOdtVisitors {
	public function writeHandler($exportodt);
	public function loadOdt($odt);
	public function getOdtDataFromTB($odt);	
}
class ExportOdtVisitor extends DebugVisitor implements exportOdtVisitors {
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
	protected $odt;
	protected $exportodt;
	protected $htmlpos;
	protected $opts;
	protected $context;
	protected $tbname;
	protected $validtbname;
	protected $validodt;
	protected $headerbodystartpos;
	protected $headerstartodt;
	protected $headerbodyendpos;
	protected $headerendodt;
	protected $headerodt;
	protected $startpos;
	protected $replarr;
	protected $htmlodt;
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
  		
  		// valid and set path and name to odt file
    	if (file_exists("templates/{$this->template}.htm")
    		|| file_exists("templates/{$this->template}.html")) {
    			if(is_array($this->filesarr) && count($this->filesarr) > 0) {
    				foreach ($this->filesarr as $this->templateval) {
    					$this->htmlpos 			= stripos(substr($this->templateval,10),'.');
    					$this->validtemplate 	= substr($this->templateval,10,$this->htmlpos);
    					if($this->validtemplate === $this->template) {
    						$this->odt = $this->templateval;    						
    					}
    				}     					
				} else {
					die("Sorry, file could not be loaded!");
				}
    	} else {
    		die("Sorry, template not found!");
    	}		
	}
	
	public function writeHandler($exportodt) {
		$this->exportodt = $exportodt;
		file_put_contents("docs/{$this->template}{$this->filend}",$this->exportodt);
	}
	
	public function loadOdt($odt) {
		$this->odt = $odt;
		$this->file = file_get_contents($this->odt,false,null);
		$this->exportodt = $this->getOdtDataFromTB($this->file);
		
		$this->replarr = array("</body>","</html>");
		
		// open/create odt file and write it
		$this->headerbodystartpos = stripos($this->exportodt[0],"<body");		
		$this->headerstartodt = substr($this->exportodt[0],$this->headerbodystartpos);
		
		$this->headerbodyendpos = stripos($this->headerstartodt,">");
		$this->headerendodt = substr($this->headerstartodt,0,$this->headerbodyendpos+1);
		
		$this->htmlodt  = substr($this->exportodt[0],0,$this->headerbodystartpos);
		$this->htmlodt .= str_ireplace($this->headerendodt,$this->headerendodt."<br clear=all style='page-break-before:always'>",$this->headerendodt);
		
		if (is_array($this->exportodt)) {			
			foreach ($this->exportodt as $this->validodt) {
				$this->startpos = $this->headerbodystartpos+$this->headerbodyendpos;
				$this->htmlodt .= substr(str_ireplace($this->replarr,"",$this->validodt),$this->startpos+1);
			}
		}
		$this->htmlodt .= "</body></html>";
		$this->writeHandler($this->htmlodt);					
	}
	
	public function getOdtDataFromTB($file) {
		$this->filename = $file;
		return $this->getOdtFromTB($this->filename,$this->tbname,$this->id);
	}
    
    public function accept() {
    	// prepare data to save odt
    	$this->loadOdt($this->odt);
    	
    	// make sure odt file has been created
    	if (file_exists("docs/{$this->template}{$this->filend}")) {
    		print "Open Office Document <a href=\"docs/{$this->template}{$this->filend}\" target=\"_blank\">{$this->template}</a> hast been saved!";
    	} else {
    		die("Sorry, file could not be created!");
    	}    	
	}
	
}
?>