<?php
class EbuildObject 
{
	private $file;
	private $kvarray;
	
	function __construct( $filename )
	{
		$this->file = fopen( $filename, "r" );
		if ( !$this->file )
			throw new FileNotFoundException($this->file);
		$this->kvarray = array();
		
		$this->initKeyValue();
	}
	
	private function initKeyValue()
	{
		$sbuf = new String();
		$str = new String();
		while ( ( $buf = fgets($this->file) ) !== false )
		{
			$sbuf->setStr($buf);
			$str->setStr("");
			$kv = array();
			if ( preg_match("/^\t?[A-Z]+\=/", $sbuf->toString()))
			{
				if ( $sbuf->countChar("\"") == 2 )
				{
					$str->setStr($buf);
					$str->setStr($str->replace("\t",""));
					$kv = $str->split("=");
					$this->kvarray[$kv[0]] = 
						substr($kv[1],1,strlen($kv[1]) - 3);
				}
				else if ( $sbuf->countChar("\"") == 1 )
				{
					do
					{
						$str->append($buf);
						$buf = fgets($this->file);
						$sbuf->setStr($buf);

						if ( $sbuf->indexOf("\"") != -1 ) break;
					}
					while ( !preg_match("/^[A-Z\_]+\=\"[\s\S]*\"$/",$buf) );
	
					$str->append($buf);
					$kv = $str->split("=", 2);
					$str->setStr(substr($kv[1],1,strlen($kv[1]) - 3));
					$this->kvarray[$kv[0]] = $str->replace("\n"," ");
				}
			}
		}
	}
	
	public function getValue ( $key )
	{
		if ( isset($this->kvarray[$key]) === true )
		{
			$s = new String($this->kvarray[$key]);
			return $s->replace("\n", "");
		}
		else
			return "";
	}

	public function getKeyValArray( )
	{
		return $this->kvarray;
	}
}
?>
