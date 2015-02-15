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

$tbname 	= "tb_letters"; // table name
$template 	= 'testproject'; // odt template name
$id 		= '0'; // 0 = all records / not 0 = specific record(s) / x = serial letter
$filend		= ".odt"; // alternative option .rtf
$createdoc = new ExportOdtVisitor($template,$tbname,$id,$filend);

if ($createdoc instanceof ExportOdtVisitor) {
	$createdoc->accept();
}
?>