<?php
class Integer
{
	private $int;
	
	function __construct ( $int_val = 0 )
	{
		$this->setVal($int_val);
	}
	
	public function getClass()
	{
		return get_class($this);
	}
	
	public function setVal ( $int_val )
	{
		if ( is_int($int_val) )
			$this->int = $int_val;
		else if ( is_bool($int_val) )
			$this->int = (int)$int_val;
		else if ( TypeUtils::isIntegerObject($int_val) )
			$this->setVal($int_val->longValue());
		else if ( TypeUtils::isBooleanObject($int_val) )
			$this->setVal($int_val->intVal());
		else
			throw new UnsupportedTypeException($int_val);
	}
	
	public function floatValue ()
	{
		return floatval((float)$this->int * 1.0);
	}
	
	public function longValue ()
	{
		return $this->int; // long is default in php.
	}
	
	public function intValue ()
	{
		if ( $this->int > 0 )
			if ( $this->int % 4294967296 > 2147483647 )
				return ($this->int % 4294967296) - 4294967296;
			else
				return ($this->int % 2147483648);
		else
			if ( $this->int % 4294967296 < -2147483648 )
				return ($this->int % 4294967296) + 4294967296;
			else 
				return $this->int % 2147483649;
	}
	
	public function shortValue () 
	{
		if ( $this->int > 0 )
			if ( $this->int % 65536 > 32767 )
				return ($this->int % 65536 ) - 65536;
			else
				return ($this->int % 32768);
		else 
			if ( $this->int % 65536 < -32768 )
				return ($this->int % 65536) + 65536;
			else
				return ($this->int % 32769);
		
	}
	
	public function byteValue ()
	{
		if ( $this->int > 0 )
			if ( $this->int % 256 > 127 )
				return ( $this->int % 256 ) - 256;
			else
				return ( $this->int % 128 );
		else
			if ( $this->int % 256 < -128 )
				return  ( $this->int % 256 ) + 256;
			else 
				return ( $this->int % 129);
	}
	
	public function compareTo ( $anotherInteger )
	{
		if ( !is_int ($anotherInteger) &&
			!TypeUtils::isIntegerObject($anotherInteger) )
			throw new UnsupportedTypeException($anotherInteger);
		
		$val = 0;
		if ( is_int($anotherInteger) ) 
			$val = $anotherInteger;
		else if ( TypeUtils::isIntegerObject($anotherInteger) ) 
			$val = $anotherInteger->longVal();
		
		if ( $this->int > $val )
			return 1;
		else if ( $this->int < $val )
			return -1;
		else
			return 0;
	}
	
	public function equals ( $anotherInteger )
	{
		if ( !is_int($anotherInteger) && 
				!TypeUtils::isIntegerObject($anotherInteger) )
			return false;
		
		$val = 0;
		if ( is_int($anotherInteger) ) 
			$val = $anotherInteger;
		else if ( TypeUtils::isIntegerObject($anotherInteger) ) 
			$val = $anotherInteger->longVal();
		
		return ($this->int == $val);
	}
	
	public function __toString()
	{
		return strval($this->int);
	}
}

?>