<?php
class PHPortageXML extends PHPortage
{
	public function setDocType()
	{
		$this->agent->setDocType(DocType::XML);
	}
	
	public function generateStatusMessage( $e )
	{
		if ( !extension_loaded("dom") )
			throw new Exception ("dom module doesn't loaded. please make sure that installed or not.");
		
		$result = new String();
		
		$doc = new DOMDocument();
		
		$resultnode = $doc->createElement("result");
		$doc->appendChild($resultnode);
		
		$cod = $doc->createElement("code");
		$cod->appendChild($doc->createTextNode($e->getCode()));
		$resultnode->appendChild($cod);
		$mesg = $doc->createElement("message");
		$mesg->appendChild($doc->createTextNode($e->getInfo()));
		$resultnode->appendChild($mesg);
		$cause = $doc->createElement("cause");
		$cause->appendChild($doc->createTextNode($e->getCause()));
		$resultnode->appendChild($cause);
		$file = $doc->createElement("file");
		$file->appendChild($doc->createTextNode($e->getSourceFile()));
		$resultnode->appendChild($file);
		$line = $doc->createElement("line");
		$line->appendChild($doc->createTextNode($e->getLine()));
		$resultnode->appendChild($line);
				
		$result->setStr( $doc->saveXML() );
		
		return $result;
	}
}