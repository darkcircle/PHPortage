<?php
class PHPortageJSON extends PHPortage
{	
	public function setDocType()
	{
		$this->agent->setDocType(DocType::JSON);
	}
	
	public function generateStatusMessage( $e )
	{
		if ( !extension_loaded("json") )
			throw new ModuleNotLoadedException("json");
		$result = new String();

		$arr = array();
		$arr["code"] = $e->getCode();
		$arr["message"] = $e->getInfo();
		$arr["cause"] = $e->getCause();
		$arr["file"] = $e->getSourceFile();
		$arr["line"] = $e->getLine();
		
		$r = array();
		$r["result"] = $arr;
		
		$result->setStr(json_encode($r));

		return $result;
	}
}
?>
