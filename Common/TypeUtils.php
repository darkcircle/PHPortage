<?php
class TypeUtils 
{
	public static function isStringObject ( $strobj )
	{
		return ( is_object($strobj) && strcmp ( get_class($strobj), "String" ) == 0 ); 
	}
	public static function isIntegerObject( $intobj )
	{
		return ( is_object($intobj) && strcmp ( get_class($intobj), "Integer" ) == 0 );
	}
	public static function isBooleanObject ( $boolobj )
	{
		return ( is_object($boolobj) && strcmp ( get_class($boolobj), "Boolean") == 0 );
	}
	public static function isVersionObject ( $verobj )
	{
		return ( is_object($verobj) && strcmp ( get_class($verobj), "VersionObject") == 0 );
	}
	public static function isDateObject ( $dateobj )
	{
		return ( is_object($dateobj) && strcmp ( get_class($dateobj), "DateObject") == 0 );
	}
	public static function isArrayList ( $arraylistobj )
	{
		return ( is_object($arraylistobj) && strcmp ( get_class($arraylistobj), "ArrayList" ) == 0 );
	}
	public static function getType ( $arg )
	{
		if ( is_object($arg) )
			return "object(".get_class($arg).")";
		else
			return gettype($arg);
	} 
}
?>
