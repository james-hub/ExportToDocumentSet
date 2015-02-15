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

$att 		= 'type'; // add on for attribute name (=>tablename)
$recordid	= 'id'; // table auto increment field name
$tablename	= "tb_orders"; // table name
$xmlfile 	= 'orders.xml'; // xml file name
$replaceid 	= '0'; // 0 = do not replace xml file / 1 = do replace xml file

$exportxml 	= new ExportFleXmlVisitor($att,$recordid,$tablename,$xmlfile,$replaceid);
$exportxml->accept();

?>