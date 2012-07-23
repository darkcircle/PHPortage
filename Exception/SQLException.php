<?php
class SQLException extends Exception
{
	private $query;
	private $info;
	
	public function __construct( $query )
	{
		$this->query = query;
		$this->info = "Failed to run query";
		parent::__construct($this->info.": ".$query,3,null);
	}
	
	public function __toString()
	{
		return __CLASS__."[{$this->code}]: $this->message\n";
	}
	
	public function getCause ()
	{
		return $this->query;
	}
	
	public function getInfo()
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