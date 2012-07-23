<?php
class ArchObject 
{
	private $arch_array; // as array. each element type is string
	private $bunch_of_the_string; // ... 

	public function __construct()
	{
		; 
	}
	
	public function setArch ( $new_arch )
	{
		if ( is_string ( $new_arch ) )
		{
			$this->arch_array = explode(" ", $new_arch );
			$this->bunch_of_the_string = $new_arch;
		}
		else if ( TypeUtils::isStringObject($new_arch) 
			|| TypeUtils::isArchObject($new_arch) )
			$this->setArchArray($new_arch->toString());
		else
			throw new UnsupportedTypeException($new_arch);
	}
	
	public function isAvailbleArch ( $keyword )
	{
		$result = new Boolean();
		foreach ( $this->arch_array as &$val )
		{
			if ( strcmp($val, $keyword) == 0 )
			{
				$result->setVal(true);
				break;
			}
		}
		
		return $result;
	}
	
	public function addArch ( $new_arch )
	{
		array_push($this->arch_array, $new_arch);
	}
	
	public function getArchArray ()
	{
		return $this->arch_array;
	}
	
	public function toString()
	{
		return $this->__toString();
	}
	
	public function __toString ()
	{
		return $this->bunch_of_the_string;
	} 
}
?>