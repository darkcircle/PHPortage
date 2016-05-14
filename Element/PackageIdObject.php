<?php
class PackageIdObject
{
	protected $category; // string
	protected $name; // string
	protected $version; // objects

	public function __construct()
	{
		;
	}

	public function setCategory ( $new_category )
	{
		$this->category = new String($new_category);
	}
	public function getCategory ( )
	{
		return $this->category; // object
	}
	
	public function setName ( $new_name )
	{
		$this->name = new String( $new_name );
	}
	public function getName ()
	{
		return $this->name; // object
	}
	
	public function getFullPackageName ()
	{
		return $this->getCategory()->toString()."/".$this->getName()->toString();
	}
	
	public function setVersion ($new_version)
	{
		$this->version = new VersionObject();
		$this->version->setVersion($new_version);
	}
	public function getVersion ()
	{
		return $this->version; // object
	}
	
	public function toString()
	{
		return $this->__toString(); // string
	}

	public function __toString()
	{
		return $this->getFullPackageName()."-".$this->getVersion()->toString();
	}
}
?>
