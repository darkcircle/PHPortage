<?php
class ModuleNotLoadedException extends Exception 
{
	private $module_name;
	private $info;
	
	public function __construct( $module_name )
	{
		$this->module_name = $module_name;
		$this->info = "Module is not loaded";
		parent::__construct($this->info.": ".$this->module_name, 4, null);
	}
	
	public function __toString()
	{
		return __CLASS__."[{$this->code}]: $this->message\n";
	}
	
	public function getCause ()
	{
		return $this->module_name;
	}
	
	public function getInfo ()
	{
		return $this->info;
	}
	
	public function getSourceFile ()
	{
		$filename = preg_split("/".preg_replace("/\//","\\/",getcwd())."\//",
				$this->getFile(),
				-1,
				PREG_SPLIT_NO_EMPTY);
	
		return $filename[count($filename) - 1];
	}
}
?>