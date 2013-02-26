<?php

class SearchAgent
{
	private $db;
	private $db_filename;
	
	private $doc_type;
	private $keyword_similarity;
	private $target_arch;
	private $limit_number_of_result;
	private $actual_number_of_result; // internal use only!
	private $latest_version_only;
	private $include_livebuild;
	private $show_masked_package;
	
	public function __construct( $new_db_filename = "" )
	{		
		// default setting
		$this->setDBFilename($new_db_filename);
		$this->setDocType(DocType::UNDECIDED);
		$this->setKeywordSimilarity(KeywordSimilarity::EXACT);
		$this->setLimitNumberOfResult(-1); // infinity
		$this->setTargetArch(""); // show all target
		$this->setLatestVersionOnly(true); // just latest version only 
		$this->setIncludeLivebuild(false); // except live build
		$this->setShowMaskedPackage(false); // except masked package
	}
	
	public function setDBFilename ( $new_db_filename )
	{
		if ( is_string($new_db_filename) || TypeUtils::isStringObject($new_db_filename) )
			$this->db_filename = new String($new_db_filename);
		else
			throw new Exception ( "Cannot accept given type" );
	}
	public function getDBFilename( )
	{
		return $this->db_filename; // object
	}
	
	public function setDocType( $new_doc_type )
	{
		if ( is_int ( $new_doc_type ) )
		{
			$this->doc_type = new Integer();
			$this->doc_type->setVal($new_doc_type);
		}
		else
			throw new Exception ( "Cannot accept given type" );
	}
	public function getDocType ()
	{
		return $this->doc_type; // object
	}
	
	public function setKeywordSimilarity ( $new_keyword_similarity )
	{
		if ( is_int ( $new_keyword_similarity ) )
			$this->keyword_similarity = new Integer($new_keyword_similarity);
		else
			throw new Exception ( "Cannot accept given type" );
	}
	public function getKeywordSimilarity ( )
	{
		return $this->keyword_similarity; // object
	}
	
	public function setTargetArch ( $new_target_arch )
	{
		if ( is_string($new_target_arch) || TypeUtils::isStringObject($new_target_arch))
			$this->target_arch = new String( $new_target_arch );
		else
			throw new Exception ( "Cannot accept given type" );
	} 
	public function getTargetArch ()
	{
		return $this->target_arch; // object
	}
	
	public function setLimitNumberOfResult ( $nor )
	{
		if ( is_int( $nor ) || TypeUtils::isIntegerObject($nor) )
			$this->limit_number_of_result = new Integer($nor);
		else 
			throw new Exception ( "Cannot accept given type" );
	}
	public function getLimitNumberOfResult ( )
	{
		return $this->limit_number_of_result; // object
	}
	
	private function setActualNumberOfResult ( $nor )
	{
		if ( is_int( $nor ) || TypeUtils::isIntegerObject($nor) )
			$this->actual_number_of_result = new Integer($nor);
		else
			throw new Exception ( "Cannot accept given type" );
	}
	private function getActualNumberOfResult ()
	{
		return $this->actual_number_of_result;
	}
	
	public function setLatestVersionOnly ( $latest_version_only )
	{
		if ( is_bool($latest_version_only) || is_int($latest_version_only) 
			|| TypeUtils::isBooleanObject($latest_version_only) 
			|| TypeUtils::isStringObject($latest_version_only) )
			$this->latest_version_only = new Boolean($latest_version_only);
		else
			throw new Exception ( "Cannot accept given type" );
	}
	public function getLatestVersionOnly ()
	{
		return $this->latest_version_only; // object
	}
	
	public function setIncludeLivebuild ( $livebuild )
	{
		if ( is_bool($livebuild) || is_int($livebuild)
			|| TypeUtils::isBooleanObject($livebuild)
			|| TypeUtils::isStringObject($livebuild) ) 
			$this->include_livebuild = new Boolean($livebuild);
		else
			throw new Exception ( "Cannot accept given type" );
	}
	public function getIncludeLivebuild ( )
	{
		return $this->include_livebuild; // object
	}
	
	public function setShowMaskedPackage ( $show_masked_package )
	{
		if ( is_bool($show_masked_package) || is_int($show_masked_package)
			|| TypeUtils::isBooleanObject($show_masked_package)
			|| TypeUtils::isStringObject($show_masked_package) ) 
			$this->show_masked_package = new Boolean($show_masked_package);
		else
			throw new Exception ( "Cannot accept given type" );
	}
	public function getShowMaskedPackage()
	{
		return $this->show_masked_package; // object;
	}
	
	public function getResultContent ( $keyword )
	{
		$result = new String();
		
		if ( strcmp ( strval($keyword), "" ) == 0 )
			$result->setStr( "Keyword doesn't specified." );
		
		if ( $this->getDocType()->intValue() == DocType::XML )
			$result->setStr($this->getXMLContent($keyword));
		else if ( $this->getDocType()->intValue() == DocType::JSON )
			$result->setStr($this->getJSONContent($keyword));
		else 
			$result->setStr("document type is not decided or unknown request type code: ".$this->getDocType());
		
		return $result;
	}
	
	private function getXMLContent ( $keyword )
	{
		$result = new String();
		
		if ( !extension_loaded("dom") )
			throw new ModuleNotLoadedException("dom");
		
		$rr = $this->getCommonAssociativeArray($keyword);
		$rarray = $rr["result"];

		$ss_params = "type=\"text/xsl\" href=\"xml/phportage.xsl\"";
		$xml_stylesheet = new DOMProcessingInstruction("xml-stylesheet", $ss_params);

		$doc = new DOMDocument();
		$doc->appendChild($xml_stylesheet);
		$doc->encoding = "UTF-8";
		$doc->formatOutput = true;
		
		$resultnode = $doc->createElement( "result" ); 
		$resultnode->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
		$resultnode->setAttribute("xsi:noNamespaceSchemaLocation","xml/phportage.xsd");
		$doc->appendChild($resultnode);
		
		foreach ( $rarray as $k => $v )
		{
			if ( strcmp ( $k , "packages" ) != 0 )
			{ 
				$options = $doc->createElement($k);
				$options->appendChild($doc->createTextNode($v));
				$resultnode->appendChild($options);
			}
			else
			{
				$packages = $doc->createElement($k);
				foreach ( $v as &$velement) {
					$pkg = $doc->createElement("pkg");
					
					foreach( $velement as $pk => $pv)
					{
						if ( strcmp ( $pk, "masked" ) == 0 )
						{
							if ( intval($pv) == 1 )
							{
								$node = $doc->createElement($pk);
								$node->setAttribute("value","true");
								$pkg->appendChild($node);
							}
						}
						else if ( strcmp( $pk, "arch" ) == 0 )
						{
							if ( count($pv) != 0 )
							{
								$arches = $doc->createElement($pk);
								foreach( $pv as $ak => $av)
								{
									$arch = $doc->createElement("archelement");
									$arch->setAttribute("name", $av);
									$arches->appendChild($arch);
								}
								$pkg->appendChild($arches);
							}
						}
						else
						{
							$node = $doc->createElement($pk);
							$node->appendChild($doc->createTextNode($pv));
							$pkg->appendChild($node);
						}
					}
					$packages->appendChild($pkg);
				}
				$resultnode->appendChild($packages);
			} 
		}
		
		$result->setStr ( $doc->saveXML()."\n" );
		
		
		return $result;
	} 
	
	private function getJSONContent ( $keyword )
	{
		$result = new String();
		
		if ( !extension_loaded("json") )
			throw new ModuleNotLoadedException("json");
		
		$result->setStr(json_encode($this->getCommonAssociativeArray($keyword)));
		return $result; // object
	}
	
	private function getCommonAssociativeArray ( $keyword )
	{
		$result = array();
		$rarray = array();
		$selected = $this->selectRecords($keyword);
		
		$rarray["code"] = 0;
		$rarray["msg"] = "Success";
		$rarray["keyword"] = $keyword;
		
		// user-specified limit number of result 
		if ( $this->getLimitNumberOfResult()->intValue() == -1 )
			$rarray["limitnumofres"] = "infinity";
		else if ( $this->getLimitNumberOfResult()->intValue() > 0 )
			$rarray["limitnumofres"] = $this->getLimitNumberOfResult()->intValue();
		else
			throw new InvalidKeyValException("limit", 0);
		
		// number of actual result
		$rarray["actualnumofres"] = $this->getActualNumberOfResult()->intValue();
		
		// keyword similarity
		$cc = $this->getKeywordSimilarity()->intValue();
		if ( $cc == KeywordSimilarity::EXACT )
			$rarray["keyword_similarity"] = "exact";
		else if ( $cc == KeywordSimilarity::SIMILAR )
			$rarray["keyword_similarity"] = "similar";
		else
			$rarray["keyword_similarity"] = "undecided";
		
		// get specified target architecture
		if ( $this->getTargetArch()->compareTo( "" ) != 0 )
			$rarray["targetarch"] = $this->getTargetArch()->toString();
		else
			$rarray["targetarch"] = "all";
		
		// get setting whether It shows latest version only or not.
		if ( (int)$this->getLatestVersionOnly()->intVal() == 1 )
			$rarray["latestversiononly"] = "true";
		else
			$rarray["latestversiononly"] = "false";
		
		// get setting whether it shows including live build version(9999) or not.
		if ( (int)$this->getIncludeLivebuild()->intVal() == 1 )
			$rarray["livebuild"] = "true";
		else
			$rarray["livebuild"] = "false";
		
		// get setting whether it shows including masked packages or not.
		if ( (int)$this->getShowMaskedPackage()->intVal() == 1 )
			$rarray["maskedpackage"] = "true";
		else
			$rarray["maskedpackage"] = "false";
			
			
		$rarray["packages"] = $selected;
		$result["result"] = $rarray;
		
		return $result;
	}
	
	private function selectRecords( $keyword )
	{
		if ( $this->getDBFilename()->compareTo( "" ) == 0 )
			throw new FileNotFoundException("");

		// initialize array. explicitly
		$result = array();
		// buffer for filtering redundant data
		$filter_lvonly = array();
		
		if ( $this->getLimitNumberOfResult()->intValue() > 0 )
			$limit = $this->getLimitNumberOfResult()->intValue();
		
		// prepare select query
		$query_string = new String("select * from portagetree where");
		if ( $this->getKeywordSimilarity()->intValue() == 0 ) // similar
			$query_string->append(" name like '%".$keyword."%'");
		else if ( $this->getKeywordSimilarity()->intValue() == 1 ) // exact
			$query_string->append(" name='".$keyword."'");
		else
			throw new SQLException($query_string->toString());
		
		if ( $this->getTargetArch()->compareTo("") != 0 )
			$query_string->append(" and arch like '%".$this->getTargetArch()->toString()."%'");
		
		if ( $this->getShowMaskedPackage()->intVal() != 1 )
			$query_string->append(" and masked!=1");
		
		if ( $this->getIncludeLivebuild()->intVal() == 0 )
			$query_string->append(" except select * from portagetree where version like '%9999%'");
		

		$query_string->append(";");
		// print($query_string."\n");
		
		if ( extension_loaded("sqlite") )
		{
			// open database
			$this->db = sqlite_open($this->getDBFilename());
			if ( !$this->db )
				throw new FileNotFoundException($this->getDBFilename());
			
			// fetch result to array
			while ( $a = sqlite_fetch_array($this->db, $query_string->toString()) )
			{
				$t = array();
				$arch = new ArchObject();
				$arch->setArch($a['arch']);
				
				$t['category'] = $a['category'];
				$t['name'] = $a['name'];
				$t['version'] = $a['version'];
				$t['description'] = $a['description'];
				$t['homepage'] = $a['homepage'];
				$t['license'] = $a['license'];
				$t['arch'] = $arch->getArchArray();
				$t['masked'] = $a['masked'];
				
				array_push($filter_lvonly,$t);
			}
		}
		else if ( extension_loaded("sqlite3"))
		{
			// open database
			$this->db = new SQLite3($this->getDBFilename());
			if ( !$this->db )
				throw new FileNotFoundException($this->getDBFilename());
			
			// fetch result to array
			$sqlite3_result = $this->db->query($query_string->toString());
			
			while ( $arr = $sqlite3_result->fetcharray(SQLITE3_ASSOC) )
			{
				$t = array();
				$arch = new ArchObject();
				$arch->setArch($arr['arch']);
				
				$t['category'] = $arr['category'];
				$t['name'] = $arr['name'];
				$t['version'] = $arr['version'];
				$t['description'] = $arr['description'];
				$t['homepage'] = $arr['homepage'];
				$t['license'] = $arr['license'];
				$t['arch'] = $arch->getArchArray();
				$t['masked'] = $arr['masked'];
				
				array_push($filter_lvonly,$t);
			}
		}
		else
			throw new ModuleNotLoadedException("sqlite or sqlite3");
		
		
		if ( $this->getLatestVersionOnly()->getVal() )
		{
			$cnt = count ($filter_lvonly);
			$selected_index = 0;
			
			for ( $i = 0 ; $i < $cnt ; )
			{
				if ( $i == $cnt - 1 )
				{
					array_push($result, $filter_lvonly[$i]);
					break;
				}

				$selected_index = $i;
				
				for ( $j = $i + 1 ; $j < $cnt ; $j++ )
				{
					$name = new String($filter_lvonly[$i]['name']);
					if ( $name->compareTo($filter_lvonly[$j]['name']) == 0 )
					{
						$another_version = new VersionObject($filter_lvonly[$selected_index]['version']);
						$current_version = new VersionObject($filter_lvonly[$j]['version']);
						if ( $current_version->compareTo($another_version) > 1 )
							$selected_index = $j;
						
						if ( $j == $cnt - 1 )
						{
							array_push($result, $filter_lvonly[$selected_index]);
							break;
						}
					}
					else
					{
						array_push($result, $filter_lvonly[$selected_index]);
						$i = $selected_index = $j;
						break;
					}
					// need for test.
					// if ( count($result) <= $limit ) break;
				}
				if ( isset($limit) )
					if ( count($result) == $limit ) break;
				
				if ( ( $i == $cnt - 1 || $j == $cnt - 1 ) 
					|| ( $i == $cnt - 2 && $j == $cnt - 1 ) ) break;
			}
			
		}
		else
			$result = $filter_lvonly;
		
		$this->setActualNumberOfResult(count($result));
		
		return $result;
	}
}
?>
