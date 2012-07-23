<?php

class MaskedObject extends PackageIdObject
{
	private $ver_spec;
	private $wildcard;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function setVersionSpecifier ( $new_ver_spec )
	{
		$this->ver_spec = $new_ver_spec;
	}
	public function getVersionSpecifier ()
	{
		return $this->ver_spec;
	}
	
	public function setWildcard ( $is_wildcard_being )
	{
		$this->wildcard = (bool)$is_wildcard_being;
	}
	public function getWildcard ( )
	{
		return $this->wildcard;
	}
	
	public function isMasked ( $package_id )
	{
		$result = false;

		switch ( $this->ver_spec )
		{
			case 0:
				// if just category name and package name is only same.
				if ( strcmp( $this->getCategory(), $package_id->getCategory() ) == 0
						&& strcmp ( $this->getName(), $package_id->getName()) == 0 )
					$result = true;
				break;
			case 1:
				if ( strcmp( $this->getCategory(), $package_id->getCategory() ) == 0
						&& strcmp ( $this->getName(), $package_id->getName()) == 0 )
					if ( $this->getVersion()->compareTo($package_id->getVersion()) > 0 )
						$result = true;
				break;
			case 2:
				if ( strcmp( $this->getCategory(), $package_id->getCategory() ) == 0
						&& strcmp ( $this->getName(), $package_id->getName()) == 0 )
					if ( $this->getVersion()->compareTo($package_id->getVersion()) >= 0 )
						$result = true;
				break;
			case 3:
				if ( strcmp( $this->getCategory(), $package_id->getCategory() ) == 0
						&& strcmp ( $this->getName(), $package_id->getName()) == 0 )
				{
					// exactly same.
					if ( $this->getWildcard() === false )
					{
						if ( $this->getVersion()->compareTo($package_id->getVersion()) == 0 )
							$result = true;
					}
					else
					{
						$p = $this->getVersion()->getValidPosition();
						$v_upper_limit = new VersionObject();
						$v_upper_limit->setVersion($this->getVersion());
						switch ( $p )
						{
							case 8:
								$v_upper_limit->setMajor(
								$this->getVersion()->getMajor() + 1);
								break;
							case 7:
								$v_upper_limit->setMinor(
								$this->getVersion()->getMinor() + 1);
								break;
							case 6:
								$v_upper_limit->setMaintenance1(
								$this->getVersion()->getMaintenance1() + 1);
								break;
							case 5:
								$v_upper_limit->setMaintenance2(
								$this->getVersion()->getMaintenance2() + 1);
								break;
							case 4:
								$v_upper_limit->setAlphanum(
								$this->getVersion()->getAlphanum() + 1);
								break;
							case 3:
								$v_upper_limit->setSuffix(
								$this->getVersion()->getSuffix() + 1);
								break;
							case 2:
								$v_upper_limit->setSuffixRevision(
								$this->getVersion()->getSuffixRevision() + 1);
								break;
						}
					
						if ( $this->getVersion()->compareTo($package_id->getVersion()) <= 0
								&& $v_upper_limit->compareTo($package_id->getVersion()) > 0 )
							$result = true;	
					}
				}
				break;
			case 4:
				if ( strcmp( $this->getCategory(), $package_id->getCategory() ) == 0
						&& strcmp ( $this->getName(), $package_id->getName()) == 0 )
					if ( abs($this->getVersion()->compareTo($package_id->getVersion())) <= 1 )
						$result = true;
				break;
			case 5:
				if ( strcmp( $this->getCategory(), $package_id->getCategory() ) == 0
						&& strcmp ( $this->getName(), $package_id->getName()) == 0 )
					if  ( $this->getVersion()->compareTo($package_id->getVersion()) <= 0 )
						$result = true;
				break;
			case 6:
				if ( strcmp( $this->getCategory(), $package_id->getCategory() ) == 0
						&& strcmp ( $this->getName(), $package_id->getName()) == 0 )
					if ( $this->getVersion()->compareTo($package_id->getVersion()) < 0 )
						$result = true;
				break;
			default:
				throw new UnsupportedValueException($this->ver_spec);
		}
		
		return $result;
	}
}
?>