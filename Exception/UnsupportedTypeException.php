<?php
class UnsupportedTypeException extends Exception 
{
	private $type;
	private $info;
	
	public function __construct( $arg )
	{
		$this->type = $this->getTypeString($arg);
		$this->info = "Given type is not supported";
		parent::__construct($this->info.": ".$this->type, 1, null);
	}
	
	public function __toString()
	{
		return __CLASS__."[{$this->code}]: $this->message\n";
	}
	
	public function getCause ()
	{
		return $this->type;
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
	
	private function getTypeString ( $arg )
	{
		return TypeUtils::getType($arg);
	}
	
}
?>