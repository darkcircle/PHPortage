<?php
class InvalidKeyValException extends Exception
{
	private $key;
	private $val;
	private $info;
	
	public function __construct( $key, $val )
	{
		$this->key = $key;
		$this->val = $val;
		$this->info = "Invalid key or val is being here";
		parent::__construct($this->info.": ".$this->getCause(), 5, null);
	}
	
	public function __toString()
	{
		return __CLASS__."[{$this->code}]: $this->message\n";
	}
	
	public function getCause()
	{
		$str = "\"{$this->key}\" => ";
		if ( is_string($this->val))
			$str .= "\"{$this->val}\"";
		else
			$str .= "{$this->val}";
		return $str;
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