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

$felement 	= 'report'; // office document name
$id 		= '0'; // document id for naming file option (with id = 0 date will be used instead)
$tbname		= 'tb_orders'; // try also tb_reports or tb_letters
$createoo = new ExportOOVisitor($felement,$id,$tbname);
?>
<?php
if ($createoo instanceof ExportOOVisitor) {
	$createoo->accept();
}
?>