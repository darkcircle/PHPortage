<?php
class ArchList
{
	private $archfile;
	private $archlist;
	
	public function __construct()
	{
		$this->setArchFile("/usr/portage/profiles/arch.list");
		$this->prepareArchList();
	}
	
	private function setArchFile ( $new_arch_file )
	{
		$this->archfile = $new_arch_file; 
	}
	
	private function prepareArchList ()
	{
		$this->archlist = array();
		$str = new String();
		
		$f = fopen ( $this->archfile, "r" );
		if ( !$f )
			throw new FileNotFoundException($this->archfile);
		
		while ( ( $buf = fgets( $f ) ) !== false )
		{
			if ( !preg_match( "/^\n$/", $buf ) && !preg_match("/\#(\s|\S)+/", $buf) )
			{
				$str->setStr($buf);
				if ( $str->charAt($str->length() - 1) == "\n" )
					$str->setStr($str->substr(0, $str->length() - 1));
				array_push($this->archlist, $str->toString());
			}
		}
	}
	
	public function checkValidArch ( $arch_str )
	{
		$result = false;
		
		foreach ( $this->archlist as &$astr )
		{
			if ( strcmp ( $astr , $arch_str ) == 0 )
			{
				$result = true;
				break;
			}				
		}
		
		return $result;
	}

	public function getArchList ()
	{
		return $this->archlist;
	}
}

?>
