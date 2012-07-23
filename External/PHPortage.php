<?php
class PHPortage {

	protected $http_gvar;
	protected $agent;
	protected $db_filename;
	
	protected $keyword_is_ready;
	protected $similarity_is_ready;
	protected $limitnor_is_ready;
	protected $targetarch_is_ready;
	protected $livebuild_is_ready;
	protected $latestversiononly_is_ready;
	protected $showmaskedversion_is_ready;
	

	public function __construct( $db_filename = "" )
	{
		$this->setDBFilename($db_filename);
	}
	
	public function initHttpGetVars( $get_vars )
	{
		$result = new String();
		
		$r = $this->isArrayGetVars ($get_vars);
		if ( $r->compareTo( "" ) != 0 )
			return $r;
		
		$r = $this->checkPreparedKeyVal ($get_vars);
		if ( $r->compareTo( "" ) != 0 )
			return $r;
		
		return $result;
	}
	
	protected function setDBFilename ( $db_filename )
	{
		$this->db_filename = $db_filename;
	}

	protected function isArrayGetVars( $get_vars )
	{
		$result = new String();
		if ( !is_array($get_vars) )
		{
			$e = new UnsupportedTypeException($get_vars);
			$result->setStr($this->generateStatusMessage($e));
		}
		else 
			$result->setStr("");

		return $result;
	}
	
	protected function checkPreparedKeyVal ( $get_vars )
	{
		$result = new String();
		
		$alist = new ArchList();
		$str = new String();
		
		foreach ( $get_vars as $key => $val )
		{
			$str->setStr($key);
			if ( $str->compareTo("k") == 0 )
				$this->keyword_is_ready = true;
			else if ( $str->compareTo("similarity") == 0 )
				$this->similarity_is_ready = true;
			else if ( $str->compareTo("limit") == 0 )
				$this->limitnor_is_ready = true;
			else if ( $str->compareTo("arch") == 0 )
				$this->targetarch_is_ready = true;
			else if ( $str->compareTo("livebuild") == 0 )
				$this->livebuild_is_ready = true;
			else if ( $str->compareTo("latestonly") == 0 )
				$this->latestversiononly_is_ready = true;
			else if ( $str->compareTo("showmasked") == 0 )
				$this->showmaskedversion_is_ready = true;
			else
			{
				$e = new InvalidKeyValException($key,$val);
				$result->setStr($this->generateStatusMessage($e));
				return $result;
			}
		}
		
		// critical!
		if ( (int)$this->keyword_is_ready != 1 )
		{
			$e = new InvalidKeyValException("k", "" );
			$result->setStr($this->generateStatusMessage($e));
			return $result;
		}
		
		foreach ( $get_vars as $key => $val )
		{
			$str->setStr($key);
			if ( $str->compareTo("livebuild") == 0
				|| $str->compareTo("latestonly") == 0
				|| $str->compareTo("showmasked") == 0)
			{
				$str->setStr($val);
				if ( $str->compareTo("true") != 0 
					&& $str->compareTo("false") != 0 )
				{
					$e = new InvalidKeyValException($key, $val);
					$result->setStr($this->generateStatusMessage($e));
					return $result;
				}
			}
			else if ( $str->compareTo("k") == 0 )
			{
				if ( intval($val) != 0 && !is_string($val) )
				{
					$e = new InvalidKeyValException($key, $val);
					$result->setStr($this->generateStatusMessage($e));
					return $result;
				}
			}
			else if ( $str->compareTo("similarity") == 0 )
			{
				$str->setStr($val);
				if ( !( $str->compareTo("exact") == 0 || $str->compareTo("similar") == 0 ) )
				{
					$e = new InvalidKeyValException($key, $val);
					$result->setStr($this->generateStatusMessage($e));
					return $result;
				}
			}
			else if ( $str->compareTo("limit") == 0 )
			{
				if ( intval($val) == 0 )
				{
					$e = new InvalidKeyValException($key, $val);
					$result->setStr($this->generateStatusMessage($e));
					return $result;
				}
			}
			else if ( $str->compareTo("arch") == 0 )
			{
				$str->setStr($val);
				if ( !$alist->checkValidArch($str->toString()) )
				{
					$e = new InvalidKeyValException($key, $val);
					$result->setStr($this->generateStatusMessage($e));
					return $result;
				}
			}
		}
		// no problem
		$this->http_gvar = $get_vars;
		
		return $result;
	}

	public function execute( )
	{
		$result = new String();
		
		try
		{
			$this->agent = new SearchAgent($this->db_filename);
		}
		catch ( Exception $e )
		{
			$result->setStr($this->generateStatusMessage($e));
			return $result->toString();	
		}
		
		$this->setDocType();
		$str = new String();
		
		if ( $this->similarity_is_ready )
		{
			$str->setStr($this->http_gvar["similarity"]);
			if ( $str->compareTo("exact") == 0 )
				$this->agent->setKeywordSimilarity(KeywordSimilarity::EXACT);
			else if ( $str->compareTo("similar") == 0 )
				$this->agent->setKeywordSimilarity(KeywordSimilarity::SIMILAR);
		}
		if ( $this->limitnor_is_ready )
		{
			$ival = intval($this->http_gvar["limit"]);
			$this->agent->setLimitNumberOfResult($ival);
		}
		if ( $this->targetarch_is_ready )
		{
			$str->setStr($this->http_gvar["arch"]);
			$this->agent->setTargetArch($str);
		}
		if ( $this->latestversiononly_is_ready )
		{
			$str->setStr($this->http_gvar["latestonly"]);
			if ( $str->compareTo("true") == 0 || $str->compareTo("false") == 0 )
				$this->agent->setLatestVersionOnly($str);
		}
		if ( $this->livebuild_is_ready )
		{
			$str->setStr($this->http_gvar["livebuild"]);
			if ( $str->compareTo("true") == 0 || $str->compareTo("false") == 0 )
				$this->agent->setIncludeLivebuild($str);
		}
		if ( $this->showmaskedversion_is_ready )
		{
			$str->setStr($this->http_gvar["showmasked"]);
			if ( $str->compareTo("true") == 0 || $str->compareTo("false") == 0 )
				$this->agent->setShowMaskedPackage($str);
		}
		
		try
		{
			$result->setStr($this->agent->getResultContent($this->http_gvar["k"]));
		}
		catch ( Exception $e )
		{
			$result->setStr($this->generateStatusMessage($e));
		}
		
		return $result->toString();
	}
	
	protected function setDocType ( ) 
	{
		// TODO : inherit this class and implement this method
	}
			
	public function generateStatusMessage( $e )
	{
		$result = new String();

		return $result;
	}
}
?>