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
  
function __autoload($classname)
{   
	@require_once('../includes/setpath.php');
	$path = setConfPath();
	$classStartPath = "{$path}/classes/Configuration.class.php";
    @require_once($classStartPath);	
	
    $loadpath = "{$path}/classes";
    $load_config = new Configuration();
	$load_config->loadConfig($classname,$loadpath);
}

/* create csv file */
$setdate 	= null;
$felement 	= 'orders'; // csv file name
$id 		= '1'; // document id for naming file option (with id = 0 date will be used instead)
$createcsv = new ExportCSVisitor($felement,$id);

/* read csv file */
//$id = '0'; // document wll have date for naming file option
//$setdate = date('Y-m-d'); // use date to find and read csv file
$readfile = new ReadFiles($felement,$setdate,$id);
/* add more records to csv file */
$processfile = new ProcessFiles($felement,$setdate,$id);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"> <head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="Author" content="Claudio Biesele" />

<title>Create Docs Handlers - Create CSV Example</title>
<link rel="stylesheet" href="../css/createdocs.css" type="text/css" />
</head>

<body>
<?php
if ($createcsv instanceof ExportCSVisitor) {
	$createcsv->accept();
}
if ($readfile instanceof ReadFiles) {
	$readfile->readCSVFromFile();
}
if ($processfile instanceof ProcessFiles) {
	// use this feature only if you want to add more data to csv file
	//$processfile->processCSVFromFile();
}
?>
</body>
</html>