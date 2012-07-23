<?php
class TooManyArgumentException
{
	private $name;
	private $args;
	private $info;
	
	public function __construct( $name, $args )
	{
		$this->name = $name;
		$this->args = $args;
		$this->info = "Too many argument";
		parent::__contstuct($this->info.": ".$this->getCause(),7,null);
	}
	
	public function __toString()
	{
		return __CLASS__."[{$this->code}]: $this->message\n";
	}
	
	public function getCause () 
	{
		return $name." method has ".$this->args." arguments";
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