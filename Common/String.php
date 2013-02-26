<?php
class String
{
	private $str;
	
	public function __construct( $new_str = "" )
	{
		$this->setStr($new_str);
	}
	
	public function __call ( $name , $args )
	{
		if ( substr_compare("indexOf", $name, 0, 7, false) == 0 )
		{
			$cnt = count( $args );
			switch ( $cnt )
			{
				case 1:
					return $this->indexOf1($args[0]);
					break;
				case 2:
					return $this->indexOf2($args[0], $args[1]);
					break;
				default:
					throw new TooManyArgumentException($name,$args);
			}
		}	
		else if ( substr_compare("substr", $name, 0, 6, false) == 0 )
		{
			$cnt = count ( $args );
			switch ( $cnt )
			{
				case 1:
					return $this->substr1($args[0]);
					break;
				case 2:
					return $this->substr2($args[0],$args[1]);
					break;
				default:
					throw new TooManyArgumentException($name, $args);
			}
		}
		else if ( substr_compare("split", $name, 0, 5, false ) == 0 )
		{
			$cnt = count ( $args );
			switch ( $cnt )
			{
				case 1:
					return $this->split1($args[0]);
					break;
				case 2:
					return $this->split2($args[0],$args[1]);
					break;
				default:
					throw new TooManyArgumentException($name, $args);
			}
		}
	} 
	
	public function setStr ( $new_str )
	{
		if ( is_string ( $new_str ) )
			$this->str = $new_str;
		else if ( is_int ( $new_str ) || is_float ( $new_str ) )
			$this->str = strval ( $new_str );
		else if ( is_null ( $new_str ) )
			$this->str = "";
		else if ( is_bool ( $new_str ) )
		{
			$b = new Boolean();
			$b->setVal($new_str);
			$this->str = $b->toString();
		}
		else if ( TypeUtils::isIntegerObject($new_str) 
			|| TypeUtils::isStringObject($new_str) 
			|| TypeUtils::isBooleanObject($new_str) )
			$this->str = $new_str->toString();
		else
			throw new UnsupportedTypeException( $new_str );
	}
	
	public function append ( $new_str )
	{
		if ( is_string ( $new_str ) )
			$this->str .= $new_str;
		else if ( is_int ( $new_str ) || is_float( $new_str ) )
			$this->str .= strval ($new_str);
		else if ( is_bool ( $new_str ) )
		{
			$b = new Boolean();
			$b->setVal($new_str);
			$this->str .= $b->toString();
		}
		else if ( TypeUtils::isIntegerObject( $new_str) 
			|| TypeUtils::isStringObject($new_str) 
			|| TypeUtils::isBooleanObject($new_str) )
			$this->str .= $new_str->toString();
		else
			throw new UnsupportedTypeException( $new_str );
	}
	
	public function charAt ( $idx )
	{
		if ( $this->length() <= $idx )
			throw new UnsupportedValueException($idx);
		else
			return $this->str[$idx];
	}
	
	public function compareTo ( $anotherStr )
	{
		if ( is_string($anotherStr) )
			return strcmp ($this->str,$anotherStr);
		else if ( TypeUtils::isStringObject($anotherStr) ) 
			return strcmp ( $this->str, $anotherStr->toString() );
		else
			throw new UnsupportedTypeException( $anotherStr ); 
	}

	public function countChar( $char )
	{
		$result = 0;
		$cnt = $this->length();

		for ( $i = 0 ; $i < $cnt ; $i++ )
		{
			if ( $this->str[$i] == $char )
			   $result++;	
		}

		return $result;
	}
	
	public function equals ( $strobj )
	{
		if ( !is_string($strobj) 
			&& !TypeUtils::isStringObject($strobj) )
			return false;
		else
			return (strcmp( $this->str, $strobj ) == 0);
	}
	
	public function getClass ( )
	{
		return get_class($this);
	}

	public function indexOf1 ( $char )
	{
		$result = -1;

		$cnt = $this->length();
		for ( $i = 0 ; $i < $cnt ; $i++ )
		{
			if( $this->str[$i] == $char )
			{
				$result = $i;
				break;
			}
		}

		return $result;
	}

	public function indexOf2 ( $char, $charCnt )
	{
		$result = -1;

		$cnt = $this->length();
		$ccnt = 0;
		for ( $i = 0 ; $i < $cnt ; $i++ )
		{
			if( $this->str[$i] == $char )
				$ccnt++;

			if ( $ccnt == $charCnt )
			{
				$result = $i;
				break;
			}
		}

		return $result;
	}
	
	public function length ()
	{
		return strlen( $this->str );
	}
	
	public function replace ( $search, $replace )
	{
		return str_replace( $search, $replace, $this->str);
	}
	
	private function split1 ( $delimiter )
	{
		return explode( $delimiter , $this->str );
	}
	private function split2 ( $delimiter, $limit )
	{
		return explode( $delimiter , $this->str , $limit );
	}
	
	public function strcmp ( $anotherStr )
	{
		if ( !is_string($anotherStr) &&
			!TypeUtils::isStringObject($anotherStr) )
			throw new UnsupportedTypeException( $anotherStr );		
		else 
			return strcmp ( $this->str, $anotherStr );
	}
	
	private function substr1 ( $beginidx )
	{
		return substr ( $this->str , $beginidx );
	}
	private function substr2 ( $beginidx , $endidx )
	{
		return substr ( $this->str , $beginidx , $endidx );
	}
	
	public static function isAlphabet ( $alpha )
	{
		if ( strlen($alpha) == 1
			&& 
		( ( $alpha[0] >= 'a' && $alpha[0] <= 'z' )
		|| ( $alpha[0] >= 'A' && $alpha[0] <= 'Z' ) )
			)
			return true;
		else
			return false;
	}
	
	public function toString()
	{
		return $this->__toString();
	}
	
	public function __toString()
	{
		return $this->str;
	}
}
?>
