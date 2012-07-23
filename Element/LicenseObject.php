<?php
class LicenseObject
{
	private $license_array;
	private $license_string;
	
	public function __construct()
	{
		$this->setLicense( "" );
	}
	
	public function setLicense ( $license )
	{
		if ( is_string ( $license ) )
		{
			$this->license_string = $license;
			if ( strcmp( $license , "" ) == 0 )
				$this->license_array = array ( "" );
			else
				$this->license_array = explode( " " , $license );
		}
		else if ( TypeUtils::isStringObject( $license )
			|| TypeUtils::isLicenseObject( $license ) )
			$this->setLicense($license->toString());
		else
			throw new UnsupportedTypeException($license); 
	}
	
	public function isAvailableLicense ( $keyword )
	{
		$result = new Boolean();
		foreach ( $this->license_array as &$val )
		{
			if ( strcmp($val, $keyword) == 0 )
			{
				$result->setVal(true);
				break;
			}
		}
	
		return $result;
	}
	
	public function addLicense ( $new_arch )
	{
		array_push($this->license_array, $new_arch);
	}
	
	public function toString()
	{
		return $this->__toString();
	}
	
	public function __toString ()
	{
		return $this->license_string;
	}
}
?>