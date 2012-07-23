<?php
class FileNotFoundException extends Exception 
{
	private $filename;
	private $info;
	
	public function __construct( $filename )
	{
		$this->filename = filename;
		$this->info = "File not found";
		parent::__construct($this->info.": ".$this->filename,2,null);
	}
	
	public function __toString()
	{
		return __CLASS__."[{$this->code}]: $this->message\n";
	}
	
	public function getCause ()
	{
		return $this->filename;
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