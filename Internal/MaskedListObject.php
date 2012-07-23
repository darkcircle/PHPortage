<?php

class MaskedListObject 
{
	private $path;
	private $masked_list;
	
	public function __construct( $parent )
	{
		$this->path = $parent."/profiles/package.mask";
		$this->masked_list = array(); // object contained array.
		$this->loadMaskedList();
	}
	
	private function loadMaskedList ( )
	{
		$fresource = fopen ( $this->path , "r" );
		if ( !$fresource )
			throw new FileNotFoundException($this->path);
		
		while ( ( $buf = fgets( $fresource ) ) !== false )
		{
			$masked_element = new MaskedObject();
			$str = new String($buf);
			
			if ( preg_match ( "/^[\<\>]\=/" , $buf) )
			{
				$str->setStr($str->substr(0,$str->length() - 1)); // remove linebreak
				$spec = new String($str->substr(0,2));
				if ( $spec->equals("<=") )
					$masked_element->setVersionSpecifier(VersionSpecifier::LEQL);
				else if ( $spec->equals(">=") )
					$masked_element->setVersionSpecifier(VersionSpecifier::GEQL);

				$str->setStr( $str->substr(2, $str->length() - 2) );				
				$cat_pkg = $str->split("/");
				$masked_element->setCategory($cat_pkg[0]);
				
				$pkg_ver = new String($cat_pkg[1]);
				$pkg_ver_array = $pkg_ver->split("-");
				$pkgname = new String ( "" );
				$verstr = new String ( "" );
				$isCompletePkgname = false;
				
				foreach ( $pkg_ver_array as &$s )
				{
					if ( $isCompletePkgname )
						$verstr->append($s."-");
					else
					{
						if ( !( $s[0] >= "0" && $s[0] <= "9" ) )
							$pkgname->append($s."-");
						else 
						{
							$pkgname->setStr( $pkgname->substr ( 0, $pkgname->length() - 1 ) );
							$verstr->append($s."-");
							$isCompletePkgname = true;
						}
					}
				}
				$verstr->setStr($verstr->substr(0,$verstr->length() - 1));
				
				$masked_element->setName($pkgname);
				$masked_element->setVersion($verstr);
				
				$this->masked_list[$masked_element->getFullPackageName()] = $masked_element;
			}
			else if ( preg_match ( "/^[\=\<\>\~][a-z0-9]{3,5}\-/" , $buf) )
			{
				$str->setStr($str->substr(0,$str->length() - 1)); // remove linebreak
				$spec = new String ( $str->substr(0,1) );

				if ( $spec->equals("=") )
				{
					$masked_element->setVersionSpecifier(VersionSpecifier::EXEQ);
					if ( $str->charAt($str->length() - 1) == "*" )
					{
						$masked_element->setWildcard(true);
						$str->setStr($str->substr(0,$str->length() - 1));
					}
				}
				else if ( $spec->equals(">") )
					$masked_element->setVersionSpecifier(VersionSpecifier::GREA);
				else if ( $spec->equals("<") )
					$masked_element->setVersionSpecifier(VersionSpecifier::LESS);
				else if ( $spec->equals("~") )
					$masked_element->setVersionSpecifier(VersionSpecifier::RVBP);
				
				$str->setStr( $str->substr(1, $str->length() - 1) );
				$cat_pkg = $str->split("/"); // category and package 
				$masked_element->setCategory($cat_pkg[0]);
				$pkg_ver = new String($cat_pkg[1]);
				$pkg_ver_array = $pkg_ver->split("-");
				$pkgname = new String ( "" );
				$verstr = new String ( "" );
				
				$isCompletePkgname = false;

				foreach ( $pkg_ver_array as &$s )
				{
					if ( $isCompletePkgname )
					{
						$verstr->append($s."-");
					}
					else
					{
						if ( !( $s[0] >= "0" && $s[0] <= "9" ) )
							$pkgname->append($s."-");
						else
						{
							$pkgname->setStr( $pkgname->substr ( 0, $pkgname->length() - 1 ) );
							$verstr->append($s."-");
							$isCompletePkgname = (bool)true;
						}
					}
				}
				
				$verstr->setStr($verstr->substr(0,$verstr->length() - 1));
				
				$masked_element->setName($pkgname);
				$masked_element->setVersion($verstr);

				$this->masked_list[$masked_element->getFullPackageName()] = $masked_element;
			}
			else if ( preg_match("/^[a-z0-9\-]+/", $buf) )
			{
				$str->setStr($str->substr(0,$str->length() - 1)); // remove linebreak
				$masked_element->setVersionSpecifier(VersionSpecifier::WHOL);
				
				$cat_pkg = $str->split("/");
				$masked_element->setCategory($cat_pkg[0]);
				$masked_element->setName($cat_pkg[1]);
				$masked_element->setVersion("0");

				$this->masked_list[$masked_element->getFullPackageName()] = $masked_element;
			}
		}
	}
	
	public function isMaskedPkg ( $pkg )
	{
		$result = (bool)false;
		$t = new MaskedObject();
		
		if ( isset ( $this->masked_list[$pkg->getFullPackageName()] ) )
		{
			$t = $this->masked_list[$pkg->getFullPackageName()];
			if ( $t->isMasked($pkg) )
				$result = (bool)true;
		}
			
		return $result;
	}
}
?>