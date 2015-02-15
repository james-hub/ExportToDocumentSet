<?php
//error_reporting(0);
error_reporting(E_ALL);

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

interface exportExcelVisitors {
	public function getColumnDataWidth();	
	public function getReportTableSQLCounter($tbname);	
	public function getFieldsDataFromExcelDBTable();
	public function getDataRecordsFromExcelDBTable();
	
	public function setProperties(); 	// Excel-TopElement DocumentProperties
	public function setAuthor();
	public function setLastAuthor();
	public function setCreated();
	public function setLastSaved();
	public function setCompany();
	public function setVersion();
	public function setSettings(); 		// Excel-TopElement OfficeDocumentSettings
	public function setDownloadComponents();
	public function setLocationOfComponents();
	public function setWorkbook(); 		// Excel-TopElement ExcelWorkbook
	public function setWindowHeight();
	public function setWindowWidth();
	public function setWindowTop();
	public function setWindowTopY();
	public function setProtectStructure();
	public function setProtectWindows();
	public function setStyles(); 		// Excel-TopElement Styles
	public function setStyle();			// Excel-TopElement Style
	public function setAlignment();
	public function setBorders();
	public function setFont();
	public function setInterior();
	public function setNumberFormat();
	public function setProtection();
	public function setStyleHyperlink(); // Excel-TopElement Style Hyperlink
	public function setHyperlinkFont();
	public function setNewStyleNumberFormat(); // Excel-TopElement Style Extra NumberFormat
	public function setNewNumberFormat();
	public function setNewStyleFloatFormat(); // Excel-TopElement Style Float
	public function setNewFloatFormat();
	public function setNewStyleWrapFormat(); // Excel-TopElement Style Wrap for Text
	public function setNewWrapFormat();
	public function setNewStyleHeaderFormat(); // Excel-TopElement Style Header
	public function setNewHeaderFormat();
	
	
	public function setWorksheet(); 	// Excel-TopElement Worksheet
	public function setTable();
	public function setColumns();
	public function setTableOptions(); 	// Excel-TopElement WorksheetOptions
	public function setPageSetup(); 	// Excel-TopElement PageSetup
	public function setPageSetupHeader();
	public function setPageSetupFooter();
	public function setPageMargins();
	public function setSelected();
	public function setProtectObjects();
	public function setProtectScenarios();
}
class ExportExcelVisitor extends DebugVisitor implements exportExcelVisitors {
	protected $dbconn;
	protected $dbexpl;
	protected $db;
	
	protected $felement; // excel worksheet name
	protected $reportid; // id of report
	protected $docxls;
	protected $xlsdoc;
	protected $reportxls;
	protected $workbook;
	protected $docpropxls;
	protected $docsettxls;
	protected $docexcelxls;
	protected $docstylexls;
	protected $docstyle;
	protected $docworkxls;
	protected $docxlsheader;
	
	protected $author;
	protected $author_val;
	protected $created;
	protected $created_val;
	protected $company;
	protected $company_val;
	protected $height;
	protected $height_val;
	protected $width;
	protected $width_val;
	protected $top;
	protected $top_val;
	protected $protect;
	protected $protect_val;
	protected $download;
	protected $download_val;
	protected $location;
	protected $location_val;
	protected $alignement;
	protected $alignement_val;
	protected $border;
	protected $border_val;
	protected $font;
	protected $font_val;
	protected $interior;
	protected $interior_val;
	protected $numberformat;
	protected $numberformat_val;
	protected $protection;
	protected $protection_val;
	protected $selected;
	protected $selected_val;
	
	protected $style;
	protected $hyperlinks;
	protected $table;
	protected $column;
	protected $options;
	protected $page;
	protected $header;
	protected $footer;
	protected $pagemargins;
	protected $tbname;
	protected $validtbname;
	protected $counter;
	protected $counter1;
	protected $counter2;
	protected $countedfields;
	protected $savexml;
	protected $myname;
	protected $compname;
	protected $validsql;
	
	public function __construct($felement,$id,$myname,$compname,$tbname) {
		$this->felement 	= $felement;
		$this->id 			= $id;
		$this->myname 		= $myname;
		$this->compname		= $compname;
		$this->validtbname 	= $tbname;
		
		$this->workbook 	= 'Workbook';
		$this->validsql 	= $this->preventFromSqlInjection($this->validtbname);
		$this->tbname 		= $this->validsql;
		
		$this->setdate 		= date('Y-m-d');
		$this->setdatetime 	= date('Y-m-d H:i:s');
		
		$this->dbconn = new DBConn();
  		$this->dbexport = $this->dbconn->connectDBExport();
  		
  		// prepare field names
		$this->countedfields = $this->getTableFieldsCounter($this->tbname);
  		
  		// Pfad und Name zu Excel-Datei festlegen
    	if ($this->id == '0') {
    		$this->xlsdoc = "docs/{$this->felement}_{$this->setdate}.xls";
    	} else {
    		$this->xlsdoc = "docs/{$this->felement}_{$this->id}.xls";
    	}
  		
  		// prepare excel document for transmitting data
		$this->docxls = new DOMDocument('1.0');
		$this->docxls->formatOutput = true;
		$this->docxls->preserveWhiteSpace = false;
		
		// set root element
		$this->root = $this->docxls->createElement($this->workbook);
		$this->root->setAttribute('xmlns',"urn:schemas-microsoft-com:office:spreadsheet");
    	$this->root->setAttribute('xmlns:o',"urn:schemas-microsoft-com:office:office");
    	$this->root->setAttribute('xmlns:x',"urn:schemas-microsoft-com:office:excel");
    	$this->root->setAttribute('xmlns:ss',"urn:schemas-microsoft-com:office:spreadsheet");
    	$this->root->setAttribute('xmlns:html',"http://www.w3.org/TR/REC-html40");
		$this->docxls->appendChild($this->root);	
	}
	
	/* deliver data from db table for xls files */	
	public function getReportTableSQLCounter($tbname) {
		$this->tbname = $tbname;
		return $this->getTableSQLCounter($this->tbname);
	}
	
	public function getFieldsDataFromExcelDBTable() {
		return $this->getFieldsFromExcelDBTable($this->tbname);
	}
	
	public function getDataRecordsFromExcelDBTable() {
		return $this->getDataFromExcelDBTable($this->tbname);
	}
	
	public function getColumnDataWidth() {
		return $this->getColumnWidth($this->counter);
	}
	
	/* process data from db table and save as xls file for reports */
	public function setProperties() {    	
    	// set excel top element, which contains single records of DocumentProperties
    	$this->docpropxls = $this->docxls->createElement('DocumentProperties');
    	$this->docpropxls->setAttribute('xmlns',"urn:schemas-microsoft-com:office:office");
    }
    
    public function setAuthor() { 
    	$this->setProperties();
    	$this->root->appendChild($this->docpropxls);
    	
    	$this->author = $this->docxls->createElement('Author');
    	$this->author_val = $this->docxls->createTextNode($this->myname);
		$this->author->appendChild($this->author_val);
		$this->docpropxls->appendChild($this->author);
    }
    
    public function setLastAuthor() { 
    	$this->setAuthor();
    	$this->root->appendChild($this->docpropxls);
    	
    	$this->author = $this->docxls->createElement('LastAuthor');
    	$this->author_val = $this->docxls->createTextNode($this->myname);
		$this->author->appendChild($this->author_val);
		$this->docpropxls->appendChild($this->author);
    }
    
    public function setCreated() { 
    	$this->setLastAuthor();
    	$this->root->appendChild($this->docpropxls);
    	
    	$this->created = $this->docxls->createElement('Created');
    	$this->created_val = $this->docxls->createTextNode("$this->setdatetime");
		$this->created->appendChild($this->created_val);
		$this->docpropxls->appendChild($this->created);
    }
    
    public function setLastSaved() { 
    	$this->setCreated();
    	$this->root->appendChild($this->docpropxls);
    	
    	$this->created = $this->docxls->createElement('LastSaved');
    	$this->created_val = $this->docxls->createTextNode("$this->setdatetime");
		$this->created->appendChild($this->created_val);
		$this->docpropxls->appendChild($this->created);
    }
    
    public function setCompany() { 
    	$this->setLastSaved();
    	$this->root->appendChild($this->docpropxls);
    	
    	$this->company = $this->docxls->createElement('Company');
    	$this->company_val = $this->docxls->createTextNode($this->compname);
		$this->company->appendChild($this->company_val);
		$this->docpropxls->appendChild($this->company);
    }
    
    public function setVersion() { 
    	$this->setCompany();
    	$this->root->appendChild($this->docpropxls);
    	
    	$this->version = $this->docxls->createElement('Version');
    	$this->version_val = $this->docxls->createTextNode('10.6870');
		$this->version->appendChild($this->version_val);
		$this->docpropxls->appendChild($this->version);		
    }
    
    // set excel top elememnt, which contains single records of OfficeDocumentSettings
    public function setSettings() {
    	$this->setVersion();
    	$this->root->appendChild($this->docpropxls);
    	
    	$this->docsettxls = $this->docxls->createElement('OfficeDocumentSettings');
    	$this->docsettxls->setAttribute('xmlns',"urn:schemas-microsoft-com:office:office");
    }
    
    public function setDownloadComponents() {
    	$this->setSettings();
    	$this->root->appendChild($this->docsettxls);
    	
    	$this->download = $this->docxls->createElement('DownloadComponents');
		$this->docsettxls->appendChild($this->download);
    }
    
    public function setLocationOfComponents() {
    	$this->setDownloadComponents();
    	$this->root->appendChild($this->docsettxls);
    	
    	$this->location = $this->docxls->createElement('LocationOfComponents');
    	$this->location->setAttribute('HRef',"/");
		$this->docsettxls->appendChild($this->location);
		$this->root->appendChild($this->docsettxls);
    }    
    
    // set excel top element, which contains single records of ExcelWorkbook
    public function setWorkbook() {
    	$this->setLocationOfComponents();
    	$this->root->appendChild($this->docsettxls);
    	
    	$this->docexcelxls = $this->docxls->createElement('ExcelWorkbook');
    	$this->docexcelxls->setAttribute('xmlns',"urn:schemas-microsoft-com:office:excel");
    	$this->root->appendChild($this->docexcelxls);
    }
    
    public function setWindowHeight() {
    	$this->setWorkbook();
    	$this->root->appendChild($this->docexcelxls);
    	
    	$this->height = $this->docxls->createElement('WindowHeight');
    	$this->height_val = $this->docxls->createTextNode('13680');
		$this->height->appendChild($this->height_val);
		$this->docexcelxls->appendChild($this->height);
    }
    
    public function setWindowWidth() {
    	$this->setWindowHeight();
    	$this->root->appendChild($this->docexcelxls);
    	
    	$this->width = $this->docxls->createElement('WindowWidth');
    	$this->width_val = $this->docxls->createTextNode('28380');
		$this->width->appendChild($this->width_val);
		$this->docexcelxls->appendChild($this->width);
    }
    
    public function setWindowTop() {
    	$this->setWindowWidth();
    	$this->root->appendChild($this->docexcelxls);
    	
    	$this->top = $this->docxls->createElement('WindowTop');
    	$this->top_val = $this->docxls->createTextNode('240');
		$this->top->appendChild($this->top_val);
		$this->docexcelxls->appendChild($this->top);
    }
    
    public function setWindowTopY() {
    	$this->setWindowTop();
    	$this->root->appendChild($this->docexcelxls);
    	
    	$this->top = $this->docxls->createElement('WindowTopY');
    	$this->top_val = $this->docxls->createTextNode('30');
		$this->top->appendChild($this->top_val);
		$this->docexcelxls->appendChild($this->top);
    }
    
    public function setProtectStructure() {
    	$this->setWindowTopY();
    	$this->root->appendChild($this->docexcelxls);
    	
    	$this->protect = $this->docxls->createElement('ProtectStructure');
    	$this->protect_val = $this->docxls->createTextNode('False');
		$this->protect->appendChild($this->protect_val);
		$this->docexcelxls->appendChild($this->protect);
    }
    
    public function setProtectWindows() {
    	$this->setProtectStructure();
    	$this->root->appendChild($this->docexcelxls);
    	
    	$this->protect = $this->docxls->createElement('ProtectWindows');
    	$this->protect_val = $this->docxls->createTextNode('False');
		$this->protect->appendChild($this->protect_val);
		$this->docexcelxls->appendChild($this->protect);
		$this->root->appendChild($this->docexcelxls);
    }
    
    // set excel top element, which contains single records of Styles
    public function setStyles() {
    	$this->setProtectWindows();
    	$this->root->appendChild($this->docexcelxls);
    	
    	$this->docstylexls = $this->docxls->createElement('Styles');
    }
    
    // set excel top element, which contains single records of Style
    public function setStyle() {
    	$this->setStyles();
    	$this->root->appendChild($this->docstylexls);
    	
    	$this->docstyle = $this->docxls->createElement('Style');
    	$this->docstyle->setAttribute('ss:ID',"Default");
    	$this->docstyle->setAttribute('ss:Name',"Normal");
    	$this->docstylexls->appendChild($this->docstyle);
    }
    
    public function setAlignment() {
    	$this->setStyle();
    	$this->root->appendChild($this->docstyle);
    	
    	$this->alignement = $this->docxls->createElement('Alignment');
    	$this->alignement->setAttribute('ss:Vertical',"Top");
		$this->docstyle->appendChild($this->alignement);
		$this->root->appendChild($this->docstyle);		
    }
    
    public function setBorders() {
    	$this->setAlignment();
    	$this->root->appendChild($this->docstyle);
    	
    	$this->border = $this->docxls->createElement('Borders');
		$this->docstyle->appendChild($this->border);
		$this->root->appendChild($this->docstyle);
    }
    
    public function setFont() {
    	$this->setBorders();
    	$this->root->appendChild($this->docstyle);
    	
    	$this->font = $this->docxls->createElement('Font');
		$this->docstyle->appendChild($this->font);
		$this->root->appendChild($this->docstyle);
    }
    
    public function setInterior() {
    	$this->setFont();
    	$this->root->appendChild($this->docstyle);
    	
    	$this->interior = $this->docxls->createElement('Interior');
		$this->docstyle->appendChild($this->interior);
		$this->root->appendChild($this->docstyle);
    }
    
    public function setNumberFormat() {
    	$this->setInterior();
    	$this->root->appendChild($this->docstyle);
    	
    	$this->numberformat = $this->docxls->createElement('NumberFormat');
		$this->docstyle->appendChild($this->numberformat);
		$this->root->appendChild($this->docstyle);
    }
    
    public function setProtection() {
    	$this->setNumberFormat();
    	$this->root->appendChild($this->docstyle);
    	
    	$this->protection = $this->docxls->createElement('Protection');
		$this->docstyle->appendChild($this->protection);
		$this->docstylexls->appendChild($this->docstyle);
    }
    
    // set excel top element, which contains single records of StyleHyperlink
    public function setStyleHyperlink() {
    	$this->setProtection();
    	$this->root->appendChild($this->docstylexls);
    	
    	$this->hyperlinks = $this->docxls->createElement('Style');
    	$this->hyperlinks->setAttribute('ss:ID',"s18");
    	$this->hyperlinks->setAttribute('ss:Name',"Hyperlink");
    	$this->docstylexls->appendChild($this->hyperlinks);
    }
    
    public function setHyperlinkFont() {
    	$this->setStyleHyperlink();
    	$this->root->appendChild($this->hyperlinks);
    	
    	$this->fonts = $this->docxls->createElement('Font');
    	$this->fonts->setAttribute('ss:Color',"#0000FF");
    	$this->fonts->setAttribute('ss:Underline',"Single");
		$this->hyperlinks->appendChild($this->fonts);
		$this->docstylexls->appendChild($this->hyperlinks);
		$this->root->appendChild($this->docstylexls);		
    }
    
    public function setNewStyleNumberFormat() {
    	$this->setHyperlinkFont();
    	$this->root->appendChild($this->docstylexls);
    	
    	$this->stylenumberformat = $this->docxls->createElement('Style');
    	$this->stylenumberformat->setAttribute('ss:ID',"s26");
    	$this->docstylexls->appendChild($this->stylenumberformat);
    }
    
    public function setNewNumberFormat() {
    	$this->setNewStyleNumberFormat();
    	$this->root->appendChild($this->stylenumberformat);
    	
    	$this->newinteformat = $this->docxls->createElement('Interior');
    	$this->newinteformat->setAttribute('ss:Color',"#C0C0C0");
    	$this->newinteformat->setAttribute('ss:Pattern',"Solid");
    	
    	$this->newnumberformat = $this->docxls->createElement('NumberFormat');
    	$this->newnumberformat->setAttribute('ss:Format',"0");
    	
    	$this->stylenumberformat->appendChild($this->newinteformat);	
    	$this->stylenumberformat->appendChild($this->newnumberformat);
		$this->docstylexls->appendChild($this->stylenumberformat);
		$this->root->appendChild($this->docstylexls);		
    }
    
    public function setNewStyleFloatFormat() {
    	$this->setNewNumberFormat();
    	$this->root->appendChild($this->docstylexls);
    	
    	$this->stylefloatformat = $this->docxls->createElement('Style');
    	$this->stylefloatformat->setAttribute('ss:ID',"s25");
    	$this->docstylexls->appendChild($this->stylefloatformat);
    }
    
    public function setNewFloatFormat() {
    	$this->setNewStyleFloatFormat();
    	$this->root->appendChild($this->stylefloatformat);
    	
    	$this->newinteformat = $this->docxls->createElement('Interior');
    	$this->newinteformat->setAttribute('ss:Color',"#C0C0C0");
    	$this->newinteformat->setAttribute('ss:Pattern',"Solid");
    	
    	$this->newfloatformat = $this->docxls->createElement('NumberFormat');
    	$this->newfloatformat->setAttribute('ss:Format',"Fixed");
    	
    	$this->stylefloatformat->appendChild($this->newinteformat);   	
    	$this->stylefloatformat->appendChild($this->newfloatformat);
		$this->docstylexls->appendChild($this->stylefloatformat);
		$this->root->appendChild($this->docstylexls);
    }
    
    public function setNewStyleWrapFormat() {
    	$this->setNewFloatFormat();
    	$this->root->appendChild($this->docstylexls);
    	
    	$this->stylewrapformat = $this->docxls->createElement('Style');
    	$this->stylewrapformat->setAttribute('ss:ID',"s27");
    	$this->docstylexls->appendChild($this->stylewrapformat);
    }
    
    public function setNewWrapFormat() {
    	$this->setNewStyleWrapFormat();
    	$this->root->appendChild($this->stylewrapformat);
    	
    	$this->newwrapformat = $this->docxls->createElement('Alignment');
    	$this->newwrapformat->setAttribute('ss:Vertical',"Top");
    	$this->newwrapformat->setAttribute('ss:WrapText',"1");    	
    	$this->stylewrapformat->appendChild($this->newwrapformat);
		$this->docstylexls->appendChild($this->stylewrapformat);
		$this->root->appendChild($this->docstylexls);
    }
    
    public function setNewStyleHeaderFormat() {
    	$this->setNewWrapFormat();
    	$this->root->appendChild($this->docstylexls);
    	
    	$this->stylehdformat = $this->docxls->createElement('Style');
    	$this->stylehdformat->setAttribute('ss:ID',"s28");
    	$this->docstylexls->appendChild($this->stylehdformat);
    }
    
    public function setNewHeaderFormat() {
    	$this->setNewStyleHeaderFormat();
    	$this->root->appendChild($this->stylehdformat);
    	
    	$this->newhdformat = $this->docxls->createElement('Font');
    	$this->newhdformat->setAttribute('x:Family',"Swiss");
    	$this->newhdformat->setAttribute('ss:Bold',"1");
    	$this->newhdformat->setAttribute('ss:Italic',"1");
    	
    	$this->newinteformat = $this->docxls->createElement('Interior');
    	$this->newinteformat->setAttribute('ss:Color',"#CCCCFF");
    	$this->newinteformat->setAttribute('ss:Pattern',"Solid");
    	
    	$this->stylehdformat->appendChild($this->newhdformat);
    	$this->stylehdformat->appendChild($this->newinteformat);
		$this->docstylexls->appendChild($this->stylehdformat);
		$this->root->appendChild($this->docstylexls);
    }
    
    // set excel top element, which contains single records of Worksheet
    public function setWorksheet() {
    	$this->setNewHeaderFormat();
    	$this->root->appendChild($this->docstylexls);
    	
    	$this->docworkxls = $this->docxls->createElement('Worksheet');
    	$this->docworkxls->setAttribute('ss:Name',$this->felement);
    	$this->root->appendChild($this->docworkxls);
    }
    
    public function setTable() {
    	$this->setWorksheet();
    	$this->root->appendChild($this->docworkxls);
    	
    	$this->counter = $this->getReportTableSQLCounter($this->tbname);
    	$this->counter1 = $this->counter;
    	$this->counter2 = $this->counter+1;
    	
    	$this->table = $this->docxls->createElement('Table');
    	$this->table->setAttribute('ss:ExpandedColumnCount',"$this->counter1");
    	$this->table->setAttribute('ss:ExpandedRowCount',"$this->counter2");
    	$this->table->setAttribute('ss:DefaultColumnWidth',"60");
    	$this->table->setAttribute('x:FullColumns',"1");
    	$this->table->setAttribute('x:FullRows',"1");
    	$this->docworkxls->appendChild($this->table);
    }
    
    public function setColumns() {
    	$this->setTable();
    	$this->root->appendChild($this->table);
    	
    	$this->getColumnExcelWidth($this->countedfields);
    	
    	$this->column = $this->docxls->createElement('Column');
    	$this->column->setAttribute('ss:Index',"$this->counter1");
    	$this->column->setAttribute('ss:AutoFitWidth',"0");
    	$this->column->setAttribute('ss:Width',"154.5");
    	$this->table->appendChild($this->column);
    	$this->docworkxls->appendChild($this->table);	
    }
    
    public function setFieldsRow() {
    	$this->setColumns();
    	$this->root->appendChild($this->table);
    	
    	$this->docxlsheader = $this->docxls->createElement('Row');
    }
    
    public function setHeaderResults() {
    	$this->setFieldsRow();
    	$this->root->appendChild($this->docxlsheader);
    	
    	// set fields from db table
    	$this->getFieldsDataFromExcelDBTable();
    	
    	$this->table->appendChild($this->docxlsheader);
    	$this->docworkxls->appendChild($this->table);
    }
    
    public function setRecords() {
    	$this->setHeaderResults();
    	$this->root->appendChild($this->table);
    	
    	// set records from db table
    	$this->getDataRecordsFromExcelDBTable();
    	
    	$this->docworkxls->appendChild($this->table);
    }
    
    // set excel top element, which contains single records of WorksheetOptions
    public function setTableOptions() {
    	$this->setRecords();
    	$this->root->appendChild($this->docworkxls);
    	
    	$this->options = $this->docxls->createElement('WorksheetOptions');
    	$this->options->setAttribute('xmlns',"urn:schemas-microsoft-com:office:excel");
    	$this->docworkxls->appendChild($this->options);    	
    	$this->root->appendChild($this->docworkxls);
    }
    
    // set excel top element, which contains single records of PageSetup
    public function setPageSetup() {
    	$this->setTableOptions();
    	$this->root->appendChild($this->options);
    	
    	$this->page = $this->docxls->createElement('PageSetup');
    	$this->options->appendChild($this->page); 
    	$this->docworkxls->appendChild($this->options); 	
    	$this->root->appendChild($this->docworkxls);
    }
    
    public function setPageSetupHeader() {
    	$this->setPageSetup();
    	$this->root->appendChild($this->page);
    	
    	$this->header = $this->docxls->createElement('Header');
    	$this->header->setAttribute('x:Margin',"0.4921259845");
    	$this->page->appendChild($this->header); 
    	$this->docworkxls->appendChild($this->page); 	
    	$this->root->appendChild($this->docworkxls);
    }
    
    public function setPageSetupFooter() {
    	$this->setPageSetupHeader();
    	$this->root->appendChild($this->page);
    	
    	$this->footer = $this->docxls->createElement('Footer');
    	$this->footer->setAttribute('x:Margin',"0.4921259845");
    	$this->page->appendChild($this->footer); 
    	
    	$this->options->appendChild($this->page);
    	$this->docworkxls->appendChild($this->options);
    }
    
    public function setPageMargins() {
    	$this->setPageSetupFooter();
    	$this->root->appendChild($this->page);
    	
    	$this->pagemargins = $this->docxls->createElement('PageMargins');
    	$this->pagemargins->setAttribute('x:Bottom',"0.984251969");
    	$this->pagemargins->setAttribute('x:Left',"0.78740157499999996");
    	$this->pagemargins->setAttribute('x:Right',"0.78740157499999996");
    	$this->pagemargins->setAttribute('x:Top',"0.984251969");
    	$this->page->appendChild($this->pagemargins); 
    	
    	$this->options->appendChild($this->page);
    	$this->docworkxls->appendChild($this->options);
    	$this->root->appendChild($this->docworkxls);
    }
    
    public function setSelected() {    	
    	$this->setPageMargins();
    	$this->root->appendChild($this->options);
    	
    	$this->selected = $this->docxls->createElement('Selected');
    	$this->selected_val = $this->docxls->createTextNode('');
    	$this->selected->appendChild($this->selected_val);
    	$this->options->appendChild($this->selected); 
    	$this->docworkxls->appendChild($this->options); 	
    	$this->root->appendChild($this->docworkxls);
    }
    
    public function setProtectObjects() {    	
    	$this->setSelected();
    	$this->root->appendChild($this->options);
    	
    	$this->protect = $this->docxls->createElement('ProtectObjects');
    	$this->protect_val = $this->docxls->createTextNode('False');
    	$this->protect->appendChild($this->protect_val);
    	$this->options->appendChild($this->protect); 
    	$this->docworkxls->appendChild($this->options); 	
    	$this->root->appendChild($this->docworkxls);
    }
    
    public function setProtectScenarios() {    	
    	$this->setProtectObjects();
    	$this->root->appendChild($this->options);
    	
    	$this->protect = $this->docxls->createElement('ProtectScenarios');
    	$this->protect_val = $this->docxls->createTextNode('False');
    	$this->protect->appendChild($this->protect_val);
    	$this->options->appendChild($this->protect); 
    	$this->docworkxls->appendChild($this->options); 	
    	$this->root->appendChild($this->docworkxls);
    }
	
	public function saveAsExcel() {
		// save data as excel file
		$this->savexml = $this->docxls->saveXML();
		return $this->save($this->xlsdoc,$this->savexml);
	}
	
	public function save($xlsdoc,$savexml) {
		$this->xlsdoc = $xlsdoc;
		$this->savexml = $savexml;
		file_put_contents($this->xlsdoc,$this->savexml);
	}
    
    public function accept() {    	
    	// prepare data for processing    			
    	$this->setProtectScenarios();
    	$this->saveAsExcel();
    	print "<a href=\"{$this->xlsdoc}\" target=\"_blank\">Excel file has been saved!</a>";    	
	}	
	
}
?>