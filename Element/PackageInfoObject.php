<?php
class PackageInfoObject
{
	
	private $pkgid; // object
	private $description; // string
	private $homepage; // string
	private $arch; // object
	private $license; // object
	private $masked; // boolean (object)

	public function __construct()
	{
		$this->pkgid = new PackageIdObject();
		$this->masked = new Boolean();
		$this->masked->setVal(false);
	} 
	
	function __call($method_name, $args)
	{
		if (substr_compare("PackageInfo", $method_name, 0, 7, false) == 0)
		{
			$cnt = count($args);
			switch ( $cnt )
			{
				case 4:
					$this->PackageInfo1($args[0], $args[1],$args[2], $args[3]);
					break;
				case 7:
					$this->PackageInfo2($args[0], $args[1],$args[2], $args[3], $args[4], $args[5], $args[6]);
					break;
				case 8:
					$this->PackageInfo3($args[0], $args[1],$args[2], $args[3], $args[4], $args[5], $args[6], $args[7]);
					break;
				default:
					throw new TooManyArgumentException($name, $args);
			}
		}
	}

	private function PackageInfo1 ( $category, $name, $version,  $description )
	{
		$this->Package2 ( $category, $name , $version, $description, "" , "" ); // false as default.
	}
	private function PackageInfo2 ( $category, $name, $version, $description, $homepage, $arch, $license )
	{
		$this->Package3 ( $category, $name, $version, $description, $homepage, $arch, $license, false);
	}
	private function PackageInfo3 ( $category, $name , $version, $description, $homepage, $arch, $license, $masked )
	{
		$this->setCategory($category);
		$this->setName($name);
		$this->setVersion($version);
		$this->setDescription($description);
		$this->setHomepage($homepage);
		$this->setArch($arch);
		$this->setLicense($license);
		$this->setMasked($masked);
	}

	public function getClass ()
	{
		return get_class($this);
	}
	
	public function setCategory ( $new_category )
	{
		$this->pkgid->setCategory($new_category);
	}
	public function getCategory ( )
	{
		return $this->pkgid->getCategory();
	}

	public function setName ( $new_name )
	{
		$this->pkgid->setName($new_name);
	}
	public function getName ()
	{
		return $this->pkgid->getName(); // object
	}

	public function getFullPackageName ()
	{
		return $this->pkgid->getFullPackageName();
	}

	public function setVersion ($new_version)
	{
		$this->pkgid->setVersion($new_version);
	}
	public function getVersion ()
	{
		return $this->pkgid->getVersion(); // object
	}

	public function setDescription ( $new_description )
	{
		$this->description = new String($new_description);
	}
	public function getDescription ( )
	{
		return $this->description; // object
	}
	
	public function setHomepage ( $new_homepage )
	{
		$this->homepage = new String( "" );
		if ( is_string($new_homepage) )
			$hpg = $new_homepage;
		else if ( TypeUtils::isStringObject($new_homepage) )
			$hpg = $new_homepage->toString(); 
		else
			throw new UnsupportedTypeException($new_homepage);

		
		if ( preg_match("/(https?|ftp|file|gopher)\:\/\/\/?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-\_]+)*(\/[a-zA-Z0-9\.\-\_\&\=\?]+)*\/?/", $hpg ))
			$this->homepage->setStr($hpg);
		else if ( preg_match("/[a-zA-Z0-9\-\_]+(\.[[a-zA-Z]+)+\/?/", $hpg))
			$this->homepage->setStr("http://".$hpg);
		else 
			$this->homepage->setStr("");
			//throw new Exception ("URI format is invalid");
	} 
	public function getHomepage ()
	{
		return $this->homepage; // object
	}

	public function setArch ( $new_arch )
	{
		$this->arch = new ArchObject();
		$this->arch->setArch($new_arch->toString());
	}
	public function getArch ( )
	{
		return $this->arch; // object
	}
	public function isContainedArch( $arch )
	{
		return $this->arch->isAvailbleArch( $arch ); // boolean
	}

	public function setLicense ( $new_license )
	{
		$this->license = new LicenseObject(); 
		$this->license->setLicense($new_license);
	}
	public function getLicense ( )
	{
		return $this->license; // object
	}
	public function isAvailableLicense ( $license )
	{
		return $this->license->isAvailableLicense( $license ); // boolean
	}

	public function setMasked ( $new_masked_flag )
	{
		$this->masked->setVal($new_masked_flag); 
	}
	public function getMasked ( )
	{
		return $this->masked; // object
	}
	
	public function toString()
	{
		return $this->__toString(); // string
	}

	public function __toString()
	{
		return $this->pkgid->toString();
	}
}
?>
