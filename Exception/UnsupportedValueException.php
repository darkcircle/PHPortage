<?php
class UnsupportedValueException extends Exception
{
	private $value;
	private $info;
	
	public function __construct( $value )
	{
		$this->value = $value;
		$this->info = "Given value is not supported";
		parent::__construct($this->info.": ".$this->value, 6, null);
	}
	
	public function __toString()
	{
		return __CLASS__."[{$this->code}]: $this->message\n";
	}
	
	public function getCause()
	{
		return $this->value;
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