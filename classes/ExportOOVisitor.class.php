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

interface exportOOVisitors {
	public function setScripts();
	public function setFontFaceDecls();
	public function setStyleArial();
	public function setStyleLucidaSansUnicode();
	public function setStyleMangal();
	public function setStyleTahoma();
	
	public function setAutomaticStyles();
	public function setColumnTableStyle();
	public function setColumnTablePropStyle();
	public function setRowStyle();
	public function setRowPropTableStyle();
	public function setCellHeaderStyle();
	public function setCellHeaderPropTableStyle();
	public function setCellFirstRowStyle();
	public function setCellFirstRowPropTableStyle();
	public function setCellWrapStyle();
	public function setCellWrapPropTableStyle();
	public function setCellTopStyle();
	public function setCellTopPropTableStyle();
	
	public function setDefaultStyle();
	public function setTablePropStyle();
	public function setBody();
	public function setSpreadSheet();
	public function setTable();
	public function setOfficeForms();
	public function setFieldsTable();
	public function setHeaderResults();
	
	public function saveAsOOods();
	public function save($oodoc,$savexml);
	public function accept();
}
class ExportOOVisitor extends DebugVisitor implements exportOOVisitors {
	protected $felement;
	protected $i;
	protected $id;
	protected $tbname;
	protected $validtbname;
	protected $dbconn;
	protected $db;	
	protected $setdate;
	protected $file;
	protected $oodocument;
	protected $root;
	
	protected $zip;
	protected $savexml;
	protected $docos;
	protected $oodoc;
	
	protected $docscripts;
	protected $fontface;
	protected $docstyle;
	protected $autostyles;
	protected $docpropstyle;
	protected $docolstyle;
	protected $docellstyle;
	protected $docellpropstyle;
	protected $officestyles;
	protected $doctxtstyle;
	protected $doctxtpropstyle;
	protected $docwrapstyle;
	protected $docwrappropstyle;
	protected $doctopstyle;
	protected $doctoppropstyle;
	protected $docfrstyle;
	protected $docfrpropstyle;
	
	protected $docbody;
	protected $docsheet;
	protected $doctable;
	protected $docforms;
	protected $doccol;
	protected $docrows;
	protected $docrowheader;
	protected $headerdoc;
	
	protected $fieldtype;
	protected $settype;
	protected $validsql;
	
	public function __construct($felement,$id,$tbname) {
		$this->felement 	= $felement;
		$this->id 			= $id;
		$this->validtbname 	= $tbname;
		
		$this->oodocument	= 'office:document-content';
		$this->validsql 	= $this->preventFromSqlInjection($this->validtbname);
		$this->tbname 		= $this->validsql;
		
		$this->setdate 		= date('Y-m-d');
		$this->setdatetime 	= date('Y-m-d H:i:s');
		
		$this->dbconn = new DBConn();
  		$this->dbexport = $this->dbconn->connectDBExport();
  		
  		// sert path and name to and for ods file
    	if ($this->id == '0') {
    		$this->oodoc 		= "docs/{$this->felement}_{$this->setdate}.ods";
    		$this->headerdoc 	= "{$this->felement}_{$this->setdate}.ods";
    	} else {
    		$this->oodoc 		= "docs/{$this->felement}_{$this->id}.ods";
    		$this->headerdoc 	= "{$this->felement}_{$this->id}.ods";
    	}
    	
    	if(!($this->file = tempnam("/tmp", "zip"))) {
    		return false;
    	}

    	$this->zip = new ZipArchive;
    	if(!$this->zip->open($this->file, ZipArchive::CREATE|ZipArchive::OVERWRITE)) {
    		return false;
    	}

    	if(!$this->zip->addEmptyDir("META-INF")) {
    		return false;
    	}

    	if(!$this->zip->addFromString("META-INF/manifest.xml", '<?xml version="1.0" encoding="UTF-8"?>
    		<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0">
    		<manifest:file-entry manifest:media-type="application/vnd.oasis.opendocument.spreadsheet" manifest:version="1.2" manifest:full-path="/"/>
    		<manifest:file-entry manifest:media-type="application/vnd.sun.xml.ui.configuration" manifest:full-path="Configurations2/"/>
    		<manifest:file-entry manifest:media-type="text/xml" manifest:full-path="content.xml"/></manifest:manifest>')) {
    		return false;
 		}
 		if(!$this->zip->addFromString("mimetype", "application/vnd.oasis.opendocument.spreadsheet")) {
 			return false;
 		}
 		
 		// prepare ods document for delivering data
		$this->docos = new DOMDocument('1.0','UTF-8');
		//$this->docos->formatOutput = true; // only for developing purposes!!!
		$this->docos->preserveWhiteSpace = false;
		
		// set ods root element
		$this->root = $this->docos->createElement($this->oodocument);
		$this->root->setAttribute('xmlns:office',"urn:oasis:names:tc:opendocument:xmlns:office:1.0");
    	$this->root->setAttribute('xmlns:style',"urn:oasis:names:tc:opendocument:xmlns:style:1.0");
    	$this->root->setAttribute('xmlns:text',"urn:oasis:names:tc:opendocument:xmlns:text:1.0");
    	$this->root->setAttribute('xmlns:table',"urn:oasis:names:tc:opendocument:xmlns:table:1.0");
    	$this->root->setAttribute('xmlns:draw',"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0");
    	$this->root->setAttribute('xmlns:fo',"urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0");
    	$this->root->setAttribute('xmlns:xlink',"http://www.w3.org/1999/xlink");
    	$this->root->setAttribute('xmlns:meta',"urn:oasis:names:tc:opendocument:xmlns:meta:1.0");
    	$this->root->setAttribute('xmlns:number',"urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0");
    	$this->root->setAttribute('xmlns:presentation',"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0");
    	$this->root->setAttribute('xmlns:svg',"urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0");
    	$this->root->setAttribute('xmlns:chart',"urn:oasis:names:tc:opendocument:xmlns:chart:1.0");
    	$this->root->setAttribute('xmlns:dr3d',"urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0");
    	$this->root->setAttribute('xmlns:math',"http://www.w3.org/1998/Math/MathML");
    	$this->root->setAttribute('xmlns:form',"urn:oasis:names:tc:opendocument:xmlns:form:1.0");
    	$this->root->setAttribute('xmlns:script',"urn:oasis:names:tc:opendocument:xmlns:script:1.0");
    	$this->root->setAttribute('xmlns:dom',"http://www.w3.org/2001/xml-events");
    	$this->root->setAttribute('xmlns:xforms',"http://www.w3.org/2002/xforms");
    	$this->root->setAttribute('xmlns:xsd',"http://www.w3.org/2001/XMLSchema");
    	$this->root->setAttribute('xmlns:xsi',"http://www.w3.org/2001/XMLSchema-instance");
    	$this->root->setAttribute('xmlns:of',"urn:oasis:names:tc:opendocument:xmlns:of:1.2");
    	$this->root->setAttribute('xmlns:xhtml',"http://www.w3.org/1999/xhtml");
    	$this->root->setAttribute('xmlns:grddl',"http://www.w3.org/2003/g/data-view#");
    	$this->root->setAttribute('xmlns:field',"urn:openoffice:names:experimental:ooo-ms-interop:xmlns:field:1.0");
    	$this->root->setAttribute('office:version',"1.2");
    	$this->root->setAttribute('grddl:transformation',"http://docs.oasis-open.org/office/1.2/xslt/odf2rdf.xsl");
		$this->docos->appendChild($this->root);
	}
	
	/* deliver data from db table for table records */
	public function getFieldsDataFromOODBTable() {
		return $this->getFieldsFromOODBTable($this->tbname);
	}
	
	public function getDataRecordsFromOODBTable() {
		return $this->getDataFromOODBTable($this->tbname);
	}
	
	/* process data from db table and save as ods file */
	public function setScripts() {    	
    	$this->docscripts = $this->docos->createElement('office:scripts');
    	$this->root->appendChild($this->docscripts);
    }
    
    public function setFontFaceDecls() { 
    	$this->setScripts();
    	
    	$this->fontface = $this->docos->createElement('office:font-face-decls');
		$this->root->appendChild($this->fontface);
    }
    
    public function setStyleArial() {
    	$this->setFontFaceDecls();
    	$this->root->appendChild($this->fontface);
    	
    	$this->docstyle = $this->docos->createElement('style:font-face');
    	$this->docstyle->setAttribute('style:name',"Arial");
    	$this->docstyle->setAttribute('svg:font-family',"Arial");
    	$this->docstyle->setAttribute('style:font-family-generic',"swiss");
    	$this->docstyle->setAttribute('style:font-style',"italic");
    	$this->docstyle->setAttribute('style:font-pitch',"variable");
    	$this->fontface->appendChild($this->docstyle);
		$this->root->appendChild($this->fontface);
    }
    
    public function setStyleLucidaSansUnicode() {
    	$this->setStyleArial();
    	$this->root->appendChild($this->fontface);
    	
    	$this->docstyle = $this->docos->createElement('style:font-face');
    	$this->docstyle->setAttribute('style:name',"Lucida Sans Unicode");
    	$this->docstyle->setAttribute('svg:font-family',"&apos;Lucida Sans Unicode&apos;");
    	$this->docstyle->setAttribute('style:font-family-generic',"system");
    	$this->docstyle->setAttribute('style:font-pitch',"variable");
    	$this->fontface->appendChild($this->docstyle);
		$this->root->appendChild($this->fontface);
    }
    
    public function setStyleMangal() {
    	$this->setStyleLucidaSansUnicode();
    	$this->root->appendChild($this->fontface);
    	
    	$this->docstyle = $this->docos->createElement('style:font-face');
    	$this->docstyle->setAttribute('style:name',"Mangal");
    	$this->docstyle->setAttribute('svg:font-family',"Mangal");
    	$this->docstyle->setAttribute('style:font-family-generic',"system");
    	$this->docstyle->setAttribute('style:font-pitch',"variable");
    	$this->fontface->appendChild($this->docstyle);
		$this->root->appendChild($this->fontface);
    }
    
    public function setStyleYaHei() {
    	$this->setStyleMangal();
    	$this->root->appendChild($this->fontface);
    	
    	$this->docstyle = $this->docos->createElement('style:font-face');
    	$this->docstyle->setAttribute('style:name',"Microsoft YaHei");
    	$this->docstyle->setAttribute('svg:font-family',"&apos;Microsoft YaHei&apos;");
    	$this->docstyle->setAttribute('style:font-family-generic',"system");
    	$this->docstyle->setAttribute('style:font-pitch',"variable");
    	$this->fontface->appendChild($this->docstyle);
		$this->root->appendChild($this->fontface);
    }
    
    public function setStyleTahoma() {
    	$this->setStyleYaHei();
    	$this->root->appendChild($this->fontface);
    	
    	$this->docstyle = $this->docos->createElement('style:font-face');
    	$this->docstyle->setAttribute('style:name',"Tahoma");
    	$this->docstyle->setAttribute('svg:font-family',"Tahoma");
    	$this->docstyle->setAttribute('style:font-family-generic',"system");
    	$this->docstyle->setAttribute('style:font-pitch',"variable");
    	$this->fontface->appendChild($this->docstyle);
		$this->root->appendChild($this->fontface);
    }
    
    public function setAutomaticStyles() { 
    	$this->setStyleTahoma();
    	
    	$this->autostyles = $this->docos->createElement('office:automatic-styles');
		$this->root->appendChild($this->autostyles);
    }
    
    public function setColumnTableStyle() {
    	$this->setAutomaticStyles();
    	$this->root->appendChild($this->autostyles);
    	
    	$this->counter = $this->getTableFieldsCounter($this->tbname);    	
    	$this->settype = $this->getTableType($this->tbname);
    	
    	for ($this->i = 1; $this->i <= $this->counter; $this->i++) {    	
    		$this->docolstyle = $this->docos->createElement('style:style');
    		$this->docolstyle->setAttribute('style:name',"co{$this->i}");
    		$this->docolstyle->setAttribute('style:family',"table-column");
    		$this->autostyles->appendChild($this->docolstyle);
			$this->root->appendChild($this->autostyles);
			
			$this->fieldtype 	= $this->settype[$this->i-1];
			$this->width 		= number_format($this->getTableOOMinWidth($this->tbname,$this->fieldtype), 2, '.', '');
			
			$this->docpropstyle = $this->docos->createElement('style:table-column-properties');
    		$this->docpropstyle->setAttribute('fo:break-before',"auto");
    		$this->docpropstyle->setAttribute('style:column-width',"{$this->width}mm");
    		$this->docolstyle->appendChild($this->docpropstyle);
    		$this->autostyles->appendChild($this->docolstyle);
			$this->root->appendChild($this->autostyles);
    	}
    }
    
    public function setColumnTablePropStyle() {
    	$this->setColumnTableStyle();
    }
    
    public function setRowStyle() {
    	$this->setColumnTablePropStyle();
    	$this->root->appendChild($this->autostyles);
    	
    	$this->docolstyle = $this->docos->createElement('style:style');
    	$this->docolstyle->setAttribute('style:name',"ro1");
    	$this->docolstyle->setAttribute('style:family',"table-row");
    	$this->autostyles->appendChild($this->docolstyle);
		$this->root->appendChild($this->autostyles);
    }
    
    public function setRowPropTableStyle() {
    	$this->setRowStyle();
    	$this->root->appendChild($this->docolstyle);
    	
    	$this->docpropstyle = $this->docos->createElement('style:table-row-properties');
    	$this->docpropstyle->setAttribute('style:row-height',"0.453cm");
    	$this->docpropstyle->setAttribute('fo:break-before',"auto");
    	$this->docpropstyle->setAttribute('style:use-optimal-row-height',"true");
    	$this->docolstyle->appendChild($this->docpropstyle);
    	$this->autostyles->appendChild($this->docolstyle);
		$this->root->appendChild($this->autostyles);
    }
    
    public function setDefaultStyle() {
    	$this->setRowPropTableStyle();
    	$this->root->appendChild($this->autostyles);
    	
    	$this->docolstyle = $this->docos->createElement('style:style');
    	$this->docolstyle->setAttribute('style:name',"ta1");
    	$this->docolstyle->setAttribute('style:family',"table");
    	$this->docolstyle->setAttribute('style:master-page-name',"Default");
    	$this->autostyles->appendChild($this->docolstyle);
		$this->root->appendChild($this->autostyles);
    }
    
    public function setTablePropStyle() {
    	$this->setDefaultStyle();
    	$this->root->appendChild($this->docolstyle);
    	
    	$this->docpropstyle = $this->docos->createElement('style:table-properties');
    	$this->docpropstyle->setAttribute('table:display',"true");
    	$this->docpropstyle->setAttribute('style:writing-mode',"lr-tb");
    	$this->docolstyle->appendChild($this->docpropstyle);
    	$this->autostyles->appendChild($this->docolstyle);
		$this->root->appendChild($this->autostyles);
    }
    
    public function setCellHeaderStyle() {
    	$this->setTablePropStyle();
    	$this->root->appendChild($this->autostyles);
    	
    	$this->docellstyle = $this->docos->createElement('style:style');
    	$this->docellstyle->setAttribute('style:name',"ce1");
    	$this->docellstyle->setAttribute('style:family',"table-cell");
    	$this->docellstyle->setAttribute('style:parent-style-name',"Default");
    	$this->autostyles->appendChild($this->docellstyle);
		$this->root->appendChild($this->autostyles);
    }
    
    public function setCellHeaderPropTableStyle() {
    	$this->setCellHeaderStyle();
    	$this->root->appendChild($this->docellstyle);
    	
    	$this->docellpropstyle = $this->docos->createElement('style:table-cell-properties');
    	$this->docellpropstyle->setAttribute('fo:background-color',"#99ccff");
    	$this->docellpropstyle->setAttribute('style:vertical-align',"top");
    	
    	$this->doctxtpropstyle = $this->docos->createElement('style:text-properties');
    	$this->doctxtpropstyle->setAttribute('fo:font-style',"italic"); 
    	$this->doctxtpropstyle->setAttribute('fo:font-weight',"bold");
    	$this->doctxtpropstyle->setAttribute('style:font-weight-asian',"bold");
    	$this->doctxtpropstyle->setAttribute('style:font-weight-complex',"bold");
    	
    	$this->docellstyle->appendChild($this->doctxtpropstyle);
    	$this->docellstyle->appendChild($this->docellpropstyle);
    	$this->autostyles->appendChild($this->docellstyle);
		$this->root->appendChild($this->autostyles);
    }
    
    public function setCellFirstRowStyle() {
    	$this->setCellHeaderPropTableStyle();
    	$this->root->appendChild($this->autostyles);
    	
    	$this->docfrstyle = $this->docos->createElement('style:style');
    	$this->docfrstyle->setAttribute('style:name',"ce2");
    	$this->docfrstyle->setAttribute('style:family',"table-cell");
    	$this->docfrstyle->setAttribute('style:parent-style-name',"Default");
    	$this->autostyles->appendChild($this->docfrstyle);
		$this->root->appendChild($this->autostyles);
    }
    
    public function setCellFirstRowPropTableStyle() {
    	$this->setCellFirstRowStyle();
    	$this->root->appendChild($this->docfrstyle);
    	
    	$this->docfrpropstyle = $this->docos->createElement('style:table-cell-properties');
    	$this->docfrpropstyle->setAttribute('style:vertical-align',"top");
    	$this->docfrpropstyle->setAttribute('fo:background-color',"#e6e6e6");
    	
    	$this->docfrstyle->appendChild($this->docfrpropstyle);    	
    	$this->autostyles->appendChild($this->docfrstyle);
		$this->root->appendChild($this->autostyles);
    }
    
    public function setCellWrapStyle() {
    	$this->setCellFirstRowPropTableStyle();
    	$this->root->appendChild($this->autostyles);
    	
    	$this->docwrapstyle = $this->docos->createElement('style:style');
    	$this->docwrapstyle->setAttribute('style:name',"ce3");
    	$this->docwrapstyle->setAttribute('style:family',"table-cell");
    	$this->docwrapstyle->setAttribute('style:parent-style-name',"Default");
    	$this->autostyles->appendChild($this->docwrapstyle);
		$this->root->appendChild($this->autostyles);
    }
    
    public function setCellWrapPropTableStyle() {
    	$this->setCellWrapStyle();
    	$this->root->appendChild($this->docwrapstyle);
    	
    	$this->docwrappropstyle = $this->docos->createElement('style:table-cell-properties');
    	$this->docwrappropstyle->setAttribute('style:vertical-align',"top");
    	$this->docwrappropstyle->setAttribute('fo:wrap-option',"wrap");
    	
    	$this->docwrapstyle->appendChild($this->docwrappropstyle);    	
    	$this->autostyles->appendChild($this->docwrapstyle);
		$this->root->appendChild($this->autostyles);
    }
    
    public function setCellTopStyle() {
    	$this->setCellWrapPropTableStyle();
    	$this->root->appendChild($this->autostyles);
    	
    	$this->doctopstyle = $this->docos->createElement('style:style');
    	$this->doctopstyle->setAttribute('style:name',"ce4");
    	$this->doctopstyle->setAttribute('style:family',"table-cell");
    	$this->doctopstyle->setAttribute('style:parent-style-name',"Default");
    	$this->autostyles->appendChild($this->doctopstyle);
		$this->root->appendChild($this->autostyles);
    }
    
    public function setCellTopPropTableStyle() {
    	$this->setCellTopStyle();
    	$this->root->appendChild($this->doctopstyle);
    	
    	$this->doctoppropstyle = $this->docos->createElement('style:table-cell-properties');
    	$this->doctoppropstyle->setAttribute('style:vertical-align',"top");
    	
    	$this->doctopstyle->appendChild($this->doctoppropstyle);    	
    	$this->autostyles->appendChild($this->doctopstyle);
		$this->root->appendChild($this->autostyles);
    }
    
    public function setBody() {
    	$this->setCellTopPropTableStyle();
    	$this->root->appendChild($this->autostyles);
    	
    	$this->docbody = $this->docos->createElement('office:body');
    	$this->autostyles->appendChild($this->docbody);
    	$this->root->appendChild($this->autostyles);
    }
    
    public function setSpreadSheet() {
    	$this->setBody();
    	$this->root->appendChild($this->docbody);
    	
    	$this->docsheet = $this->docos->createElement('office:spreadsheet');
    	$this->root->appendChild($this->docsheet);
    }
    
    public function setTable() {
    	$this->setSpreadSheet();
    	$this->root->appendChild($this->docsheet);
    	
    	$this->doctable = $this->docos->createElement('table:table');
    	$this->doctable->setAttribute('table:name',"{$this->felement}");
    	$this->doctable->setAttribute('table:style-name',"ta1");
    	$this->doctable->setAttribute('table:print',"false");
    	$this->docsheet->appendChild($this->doctable);
    	$this->root->appendChild($this->docsheet);    	
    }
    
    public function setOfficeForms() {
    	$this->setTable();
    	$this->root->appendChild($this->docsheet);
    	
    	$this->docforms = $this->docos->createElement('office:forms');
    	$this->docforms->setAttribute('form:automatic-focus',"false");
    	$this->docforms->setAttribute('form:apply-design-mode',"false");
    	$this->doctable->appendChild($this->docforms);
    }
    
    public function setFieldsTable() {
    	$this->setOfficeForms();
    	$this->root->appendChild($this->doctable);
    	
    	$this->docrowheader = $this->docos->createElement('table:table-row');
    }
    
    public function setHeaderResults() {
    	$this->setFieldsTable();
    	$this->root->appendChild($this->docrowheader);
    	
    	// set fields from db table
    	$this->getFieldsDataFromOODBTable();
    	
    	$this->doctable->appendChild($this->docrowheader);
    	$this->docsheet->appendChild($this->doctable);
    	$this->docbody->appendChild($this->docsheet);
    	$this->root->appendChild($this->docbody);
    }
	
    public function setRecords() {
    	$this->setHeaderResults();
    	$this->root->appendChild($this->doctable);
    	
    	// set records from db table
    	$this->getDataRecordsFromOODBTable();
    	   	
    	$this->docsheet->appendChild($this->doctable);
    	$this->docbody->appendChild($this->docsheet);
    	$this->root->appendChild($this->docbody);
    }
    
	public function saveAsOOods() {
		$this->setRecords();
		
		// ods-Datei abspeichern
		$this->savexml = $this->docos->saveXML();
		return $this->save($this->oodoc,$this->savexml);
	}
	
	public function save($oodoc,$savexml) {
		$this->oodoc 	= $oodoc;
		$this->savexml 	= $savexml;
		
		if(!$this->zip->addFromString("content.xml", $this->savexml)) {
			return false;
		}

		$this->zip->close();
		//file_put_contents($this->oodoc,$this->savexml);
		header("Content-type: application/octet-stream");
 		header("Content-Disposition: attachment; filename=\"{$this->headerdoc}\"");
 		header("Pragma: no-cache");
		header("Expires: 0");
		readfile($this->file);
 		unlink($this->file);
		return true;
	}
    
    public function accept() {    	
    	// prepare data for processing       			
    	$this->saveAsOOods();    	  	
	}
	
}
?>