<?php
class ArrayList
{
	private $array;

	public function __construct ( $newArray = null )
	{
		if ( is_null ( $newArray ) )
			$this->array = array();
		else if ( is_array ( $newArray ) )
			$this->setArray ( $newArray );
		else if ( TypeUtils::isArrayList ( $newArray ) )
			$this->array = $newArray->getRAWArray();
		else
			throw new UnsupportedTypeException( $newArray );
	}

	public function addObject ( $o )
	{
		if ( !is_object($o) )
		{
			if ( is_string ($o) )
			{
				$stro = new String ( $o );
				array_push ( $this->array, $stro );
			}
			else if ( is_int ( $o ) )
			{
				$into = new Integer ( $o );
				array_push ( $this->array, $into );
			}
			else if ( is_bool ( $o ) )
			{
				$boolo = new Boolean ( $o );
				array_push ( $this->array, $boolo );
			}
			else if ( is_float ( $o ) )
			{
				$floato = new Double ( $o );
				array_push ( $this->array, $floato );
			}
		}
		else
		{
			array_push ( $this->array, $o );
		}
	}

	public function getObject ( $idx )
	{
		return $this->array[$idx];
	}

	private function setArray ( $newArray )
	{
		if ( is_null( $newArray ) )
		{
			$this->array = $newArray;
			return;
		}
		else if ( !is_array( $newArray ) )
		{
			throw new UnsupportedTypeException ( $newArray );
		}
		else
		{
			if ( is_array ( $this->array ) && $this->length() == 0 )
			{
				$this->array = $newArray;
			}
			else if ( is_null ( $this->array ) )
			{
				$this->array = $newArray;
			}
			else
			{
				throw new AllocationRejectException ( );
			}
		}
	}

	public function toArray ( )
	{
		return $this->array;
	}

	public function length ( )
	{
		return count ( $this->array );
	}

	public function clear ( )
	{
		unset($this->array);
		$this->array = array();
	}

	public function isEmpty ( ) 
	{
		return ( count ( $this->array ) == 0 );
	}
}
?>
