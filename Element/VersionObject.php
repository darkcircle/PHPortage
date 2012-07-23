<?php
class VersionObject
{
	var $version; // string
	var $valid_position; // major 8 ~ release 1, 0 is not specified.
	
	var $major; // integer
	var $minor; // integer
	var $maintenance1; // integer
	var $maintenance2; // integer
	var $alphanumeric; // string
	var $suffix; // long integer (can be included patch version)
	var $sufrev; // if some enumerable number 
	var $pkgrelease; // integer (must be integer.)
	
	public function __construct( $new_version = null )
	{
		if ( is_string($new_version) )
			$v = new String($new_version);
		else if ( TypeUtils::isVersionObject( $new_version ) )
			$v = new String($new_version->toString());
		else 
			throw new UnsupportedTypeException($new_version);

		if ( $v->equals("") ) 
		{
			$version = "";
			$valid_position = 0;
		}
		else
			$this->setVersionString( $v->toString() );
	}
	
	public function setVersion ( $new_version )
	{			
		if (is_string( $new_version) )
			$this->setVersionString($new_version);			
		else if ( TypeUtils::isStringObject($new_version )
			|| TypeUtils::isVersionObject($new_version) )
			$this->setVersionString($new_version->toString());
		else
			throw new UnsupportedTypeException($new_version);
	}
	
	
	public function getArrayOfVersion ( )
	{
		$verarray = array($major);
		if ( $minor != null )
			array_push($verarray, $minor);
		if ( $maintenance1 != null )
			array_push($verarray, $maintenance1);
		if ( $maintenance2 != null )
			array_push($verarray , $maintenance2);

		return $verarray;
	}
	
	public function getClass ()
	{
		return get_class($this);
	}
	
	private function setVersionString ( $version_string )
	{
		$this->version = $version_string;
		
		
		// separate gentoo specific release
		$ver_and_release = explode ("-", $version_string);
		// separate version suffix
		$ver_and_suffix = explode ("_", $ver_and_release[0]);
		
		$verstr = $ver_and_suffix[0];	
		$verarray = explode (".", $verstr);
		
		$i = 0;
		
		// assign each version into each local variable respectivelly.
		for (  ; $i < count($verarray) ; $i++  )
		{
			if ( $i == 0 ) $this->major = intval($verarray[$i]);
			else if ( $i == 1 ) $this->minor = intval($verarray[$i]);
			else if ( $i == 2 ) $this->maintenance1 = intval($verarray[$i]);
			else if ( $i == 3 ) $this->maintenance2 = intval($verarray[$i]);
		}
		for ( ; $i < 4 ; $i++ )
		{
			// zero fill when slot is empty
			if ( $i == 1 ) $this->minor = intval(0);
			else if  ( $i == 2 ) $this->maintenance1 = intval(0);
			else if ( $i == 3 ) $this->maintenance2 = intval(0);
		}
		
		$this->valid_position = 9 - count($verarray);
		
		// alphanumeric char.
		if ( String::isAlphabet($verstr[strlen($verstr) - 1]) )
		{
			$this->alphanumeric = $verstr[strlen($verstr) - 1];
			$verstr = substr($verstr, 0, strlen($verstr) - 1);
			$this->valid_position = 4;
		}
		else
			$this->alphanumeric = '0'; // 0, A~Z, a~z
		
		// suffix : alpha < beta < pre < rc < (none) < patch(number)
		if ( count ($ver_and_suffix) == 2 )
		{
			$new_suffix = $ver_and_suffix[1];
			if ( preg_match ( "/(alpha)[0-9]*/", $new_suffix ) )
			{
				$this->suffix = 1;
				if ( count ( preg_split ( "/alpha/", $new_suffix, -1, PREG_SPLIT_NO_EMPTY) ) >= 1 )
				{
					$a = preg_split ( "/alpha/", $new_suffix, -1, PREG_SPLIT_NO_EMPTY);
					$this->sufrev = intval( $a[0] );
					$this->valid_position = 2;
				}
				else 
				{
					$this->sufrev = 0;
					$this->valid_position = 3;
				}
			}
			else if ( preg_match ( "/(beta)[0-9]*/", $new_suffix ) )
			{
				$this->suffix = 2;
				if ( count ( preg_split ( "/beta/", $new_suffix, -1, PREG_SPLIT_NO_EMPTY) ) >= 1 )
				{
					$a = preg_split ( "/beta/", $new_suffix, -1, PREG_SPLIT_NO_EMPTY);
					$this->sufrev = intval( $a[0] );
					$this->valid_position = 2;
				}
				else
				{
					$this->sufrev = 0;
					$this->valid_position = 3;
				}
			}
			else if ( strcmp ( "/(pre)[0-9]*/", $new_suffix ) )
			{
				$this->suffix = 3;
				if ( count ( preg_split ( "/pre/", $new_suffix, -1, PREG_SPLIT_NO_EMPTY) ) >= 1 )
				{
					$a = preg_split ( "/pre/", $new_suffix, -1, PREG_SPLIT_NO_EMPTY);
					$this->sufrev = intval( $a[0] );
					$this->valid_position = 2;
				}
				else
				{
					$this->sufrev = 0;
					$this->valid_position = 3;
				}
			}
			else if ( strcmp ( "/(rc)[0-9]*/", $new_suffix ) )
			{
				$this->suffix = 4;
				if ( count ( preg_split ( "/rc/", $new_suffix, -1, PREG_SPLIT_NO_EMPTY) ) >= 1 )
				{
					$a = preg_split ( "/rc/", $new_suffix, -1, PREG_SPLIT_NO_EMPTY);
					$this->sufrev = intval( $a[0] );
					$this->valid_position = 2;
				}
				else
				{
					$this->sufrev = 0;
					$this->valid_position = 3;
				}
			}
			else if ( preg_match ("/(p)[0-9]+/", $new_suffix) )
			{
				$this->suffix = 6;
				if ( count ( preg_split ( "/p/", $new_suffix, -1, PREG_SPLIT_NO_EMPTY) ) >= 1 )
				{
					$a = preg_split ( "/p/", $new_suffix, -1, PREG_SPLIT_NO_EMPTY);
					$this->sufrev = intval( $a[0] );
					$this->valid_position = 2;
				}
				else
				{
					$this->sufrev = 0;
					$this->valid_position = 3;
				}
			}
		}
		else if ( count ($ver_and_suffix) == 1 )
		{
			$this->suffix = 5;
			$this->sufrev = 0;
		}
			
		// release versions
		if ( count($ver_and_release) == 2 )
		{
			$new_release = $ver_and_release[1];
			
			if ( strpos ($new_release, "r") == 0 )
				 // if alphabet character  'r' is being. 
				$this->pkgrelease = intval(substr($new_release,1,strlen($new_release - 1)));
			else
				// almost there is no possibility of this case.
				$this->pkgrelease = $new_release;
			$this->valid_position = 1;
		}
		else
			$this->pkgrelease = 0;
	}
	
	public function setMajor ( $major )
	{
		$this->major = $major;
	}
	public function getMajor ()
	{
		return $this->major;
	}
	
	public function setMinor ( $minor )
	{
		$this->minor = $minor;
	}
	public function getMinor ()
	{
		return $this->minor;
	}
	
	public function setMaintenance1 ( $maintenance1 )
	{
		$this->maintenance1 = $maintenance1;
	} 
	public function getMaintenance1 () 
	{
		return $this->maintenance1;
	}
	
	public function setMaintenance2 ( $maintenance2 )
	{
		$this->maintenance2 = $maintenance2;
	}
	public function getMaintenance2 ()
	{
		return $this->maintenance2;
	}
	
	public function setAlphanum ( $alphanumeric )
	{
		$this->alphanumeric = $alphanumeric;
	}
	public function getAlphanum ()
	{
		return $this->alphanumeric;
	}
	
	public function setSuffix ( $suffix )
	{
		$this->suffix = $suffix;
	}
	public function getSuffix ()
	{
		return $this->suffix;
	}
	
	public function setSuffixRevision ( $sufrev ) 
	{
		$this->sufrev = $sufrev;
	}
	public function getSuffixRevision ()
	{
		return $this->sufrev;
	}
	
	public function setRelease ( $pkgrelease )
	{
		$this->pkgrelease = $pkgrelesae;
	}
	public function getRelease ()
	{
		return $this->pkgrelease;
	}
	
	public function getValidPosition ()
	{
		return $this->valid_position;
	}
	
	// Major -> Minor -> Maintenance1 -> Maintenance2 -> Release -> !
	public function compareTo ($anotherVersionObj)
	{
		if ( TypeUtils::isVersionObject($anotherVersionObj) ) 
		{
			$obj = new VersionObject($anotherVersionObj);
			
			$anotherMajorVer = $anotherVersionObj->getMajor();
			$thisMajorVer = $this->getMajor();
			
			if 	( $thisMajorVer > $anotherMajorVer  ) return 8;
			else if ( $thisMajorVer < $anotherMajorVer  ) return -8;
			else
			{
				$anotherMinorVer = $anotherVersionObj->getMinor();
				$thisMinorVer = $this->getMinor();
				
				if ( $thisMinorVer > $anotherMinorVer ) return 7;
				else if ( $thisMinorVer < $anotherMinorVer ) return -7;
				else 
				{
					$anotherMaintenance1= $anotherVersionObj->getMaintenance1();
					$thisMaintenance1 = $this->getMaintenance1();
					
					if ( $thisMaintenance1 > $anotherMaintenance1  ) return 6;
					else if ($thisMaintenance1 < $anotherMaintenance1 ) return -6;
					else
					{
						$anotherMaintenance2 = $anotherVersionObj->getMaintenance2();
						$thisMaintenance2=  $this->getMaintenance2();
						
						if ( $thisMaintenance2 > $anotherMaintenance2 ) return 5;
						else if ( $thisMaintenance2 < $anotherMaintenance2 ) return -5;
						else 
						{
							$anotherAlphanum = $anotherVersionObj->getAlphanum();
							$thisAlphanum = $this->getAlphanum();
							$cmp = strcmp ( $thisAlphanum, $anotherAlphanum);
							
							if ( $cmp > 0 ) return 4;
							else if ( $cmp < 0 ) return -4; 
							else 
							{
								$anotherSuffix = $anotherVersionObj->getSuffix();
								$thisSuffix = $this->getSuffix();
								
								if ( $thisSuffix > $anotherSuffix ) return 3;
								else if ( $thisSuffix < $anotherSuffix ) return -3;
								else
								{
									$anotherSufrev = $anotherVersionObj->getSuffixRevision();
									$thisSufrev = $this->getSuffixRevision();
									
									if ( $thisSufrev > $anotherSufrev ) return 2;
									else if ( $thisSufrev < $anotherSufrev ) return -2;
									else
									{	
										$anotherRelease = $anotherVersionObj->getRelease();
										$thisRelease = $this->getRelease();
										
										if ( $thisRelease > $anotherRelease ) return 1;
										else if ( $thisRelease < $anotherRelease ) return -1;
										else return 0;
									}
								}
							}
						}
					}
				}
			}
		}
		else
			throw new UnsupportedTypeException($anotherVersionObj);	
	}
	
	public function toString()
	{
		return $this->__toString();
	}
	
	public function __toString()
	{
		return $this->version;
	}
}
