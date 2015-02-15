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

class DBConn {

	protected $dblink;
	
	function connectDBExport()
	{		
		$this->dblink  = mysqli_init();
		@$this->dblink->real_connect('localhost', 'root', '', 'db_exportdata');
		if (mysqli_connect_errno()) { 
			die("<font size=\"2\" color=\"red\" face=\"Arial,Helvetica,Geneva,Swiss,SunSans-Regular\">
								Access denied.</font>"); }
    		return $this->dblink;
	}

}
?>