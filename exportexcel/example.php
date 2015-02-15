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

$felement 	= 'report'; // excel worksheet name
$id 		= '0'; // document id for naming file option (with id = 0 date will be used instead)
$myname 	= 'Firstname Lastname';
$compname 	= 'MyCompany';
$tbname		= 'tb_orders'; // try also tb_reports or tb_letters
$createxcel = new ExportExcelVisitor($felement,$id,$myname,$compname,$tbname);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"> <head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="Author" content="Claudio Biesele" />

<title>ExportToDocSet - Export Excel Example</title>
<link rel="stylesheet" href="../css/createdocs.css" type="text/css" />
</head>

<body>
<?php
if ($createxcel instanceof ExportExcelVisitor) {
	$createxcel->accept();
}
?>
</body>
</html>