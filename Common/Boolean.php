<?php
class Boolean
{
	private $bool;
	
	function __construct ( $boolVal = false )
	{
		$this->setVal( $boolVal );
	}
	
	public function getClass()
	{
		return get_class($this);
	}
	
	public function setVal ( $bool )
	{
		if ( is_bool($bool) )
			$this->bool = $bool;
		else if ( is_int($bool) )
		{
			if ( $bool == 0 )
				$this->bool = false;
			else 
				$this->bool = true;
		}
		else if ( is_string($bool) )
		{
			if ( strcmp($bool,"true") == 0 
				|| strcmp($bool, "TRUE") == 0 )
				$this->bool = true;
			else if ( strcmp($bool,"false") == 0
				|| strcmp($bool, "FALSE") == 0 )
				$this->bool = false;
			else
				throw new UnsupportedValueException($bool);
		}
		else if ( TypeUtils::isStringObject($bool) 
			|| TypeUtils::isBooleanObject($bool) )
			$this->setVal($bool->toString());
		else
			throw new UnsupportedTypeException($bool);
	}
	
	public function getVal()
	{
		// integer. becuase if this has "false", this will return null.
		return $this->bool;
	}
	
	public function intVal ( )
	{
		return (int)$this->bool;
	}
	
	public function toString()
	{
		return $this->__toString();
	}
	
	public function __toString()
	{
		return ($this->bool)? "true":"false";
	}
}
?>