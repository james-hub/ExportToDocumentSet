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

class Configuration {
	protected $load_config;
	protected $classname;
	protected $directorys;
	protected $directory;
	protected $loadpath;
	
	public function loadConfig($classname,$loadpath) {
		$this->classname	= $classname;
		$this->loadpath		= $loadpath;

		if ($this->loadpath != null) {
			/* set more class directories like this:
			   $this->loadpath/ corresponds path/classes
			  "$this->loadpath/commands/",
              "$this->loadpath/controller/",
              "$this->loadpath/filters/" */
			$this->directorys = array(
            			"$this->loadpath/"
       					);
       	}        
        
        //for each directory
        foreach($this->directorys as $this->directory)
        {
            //see if the file exsists
            if(file_exists(realpath($this->directory.$this->classname . '.class.php')))
            {
                @require_once($this->directory.$this->classname . '.class.php');
            }
        }
	}
}

?>