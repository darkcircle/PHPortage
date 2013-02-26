<?php

class PortageTreeFetcher
{
	private $db;
	private $portage_parent_directory;
	private $temp_dbname;
	private $local_dbname;
	private $mask_checker;
	private $ebuild_array;
	
	function __construct ( $new_parent_directory = "/usr/portage" , $new_temp_dbname = "" , $new_local_dbname = "" )
	{
		$this->setPortageParentDirectory( $new_parent_directory );
		$this->setTempDBFilename ( $new_temp_dbname );
		$this->setLocalDBFilename ( $new_local_dbname );
		$this->ebuild_array = array();
	}

	public function setPortageParentDirectory ( $new_parent_directory )
	{
		if ( is_string($new_parent_directory) )
			$this->portage_parent_directory = $new_parent_directory;
		else if ( TypeUtils::isStringObject($new_parent_directory) )
			$this->portage_parent_directory = $new_parent_directory->toString();
		else
			throw new UnsupportedTypeException($new_parent_directory);
	}
	public function getPortageParentDirectory ( )
	{
		return $this->portage_parent_directory;
	}
	
	public function setTempDBFilename ( $new_db_filename )
	{
		$this->temp_dbname = $new_db_filename;
	}
	public function getTempDBFilename ( )
	{
		return $this->temp_dbname;
	}

	public function setLocalDBFilename ( $new_db_filename )
	{
		$this->local_dbname = $new_db_filename;
	}
	public function getLocalDBFilename ()
	{
		return $this->local_dbname;
	}
	
	private function initDatabase ( )
	{
		if ( strcmp($this->getPortageParentDirectory() , ""  ) == 0 ||  strcmp ( $this->getTempDBFilename() , "" ) == 0 )
			throw new FileNotFoundException("");
		
		// prepare drop table query
		$drop_table = new String("drop table if exists portagetree;vacuum;");
		
		// prepare create table query
		$q = new String( "create table if not exists portagetree" );
		$q->append("(");
		$q->append("category VARCHAR NOT NULL ,");
		$q->append("name VARCHAR NOT NULL ,");
		$q->append("version VARCHAR NOT NULL ,");
		$q->append("description VARCHAR,");
		$q->append("homepage VARCHAR,");
		$q->append("arch VARCHAR,");
		$q->append("license VARCHAR,");
		$q->append("masked BOOL NOT NULL");
		$q->append(");");
		
		if ( extension_loaded("sqlite"))
		{
			// code for manipulating sqlite database using procedure based
			// sqlite extension as default under php 5.4.0.
			
			// open database
			$this->db = sqlite_open($this->getTempDBFilename());
			if ( !$this-> db )
				throw new FileNotFoundException($this->getTempDBFilename());
			
			// remove table if exists
			$ok = sqlite_exec( $this->db , $drop_table->toString() );
			if ( !$ok )
				throw new SQLException($drop_table->toString() );
					
			// create table using query string
			$ok = sqlite_exec($this->db, $q->toString());
			if ( !$ok )
				throw new SQLException( $q->toString() );
			
			// do not close database until complete fetching portage tree from upstream.
		}
		else if ( extension_loaded("sqlite3"))
		{
			// code for manipulating sqlite database using sqlite3 extension
			// based on object-oriented. It is different from above case.
			
			// open database
			$this->db = new SQLite3($this->getTempDBFilename());
			if ( !$this-> db )
				throw new FileNotFoundException($this->getTempDBFilename());
			
			// remove table if exists
			$ok = $this->db->exec($drop_table->toString());
			if ( !$ok )
				throw new SQLException($drop_table->toString());
			
			// create table using query string
			$ok = $this->db->exec($q->toString());
			if ( !$ok )
				throw new SQLException ($q->toString());
			
			// do not close database until complete fetching portage tree from upstream.
		}
		else
			throw new ModuleNotLoadedException("sqlite or sqlite3");

	}

	public function fetchPortage ()
	{
		$this->initDatabase(); // open database and initialize table
		
		list ( $susec, $ssec ) = explode(" ",microtime());
		$stime = ($susec + $ssec);
		$this->collectPortageTreeElements();
		
		if ( extension_loaded ("sqlite") )
		{
			// code for manipulating sqlite database using procedure based
			// sqlite extension as default under php 5.4.0.	
			
			// sqlite3 optimization
			sqlite_exec($this->db, "pragma synchronous=off");
			sqlite_exec($this->db, "pragma journal_mode=memory");
			sqlite_exec($this->db, "pragma temp_store=memory");
			sqlite_exec($this->db, "begin transaction");
			
			// prepare statement.
			$insert_query = new String("insert into portagetree ( category, name, version, description, homepage, arch, license, masked )");
			
			// insert each row into database until finished
			print(" * Fetching portage tree data . . . ");
			$cnt = 0;
			$pre_percentage_length = 0;
			$percentage = new String();
			$total_cnt = count ( $this->ebuild_array );

			foreach ( $this->ebuild_array as &$ebdobj )
			{
				// TODO : need to check
				$values = new String(" values (");
				if ( $ebdobj->getCategory()->compareTo("") == 0 )
					$values->append("\"\"");
				else
					$values->append($ebdobj->getCategory()->toString());
				$values->append(", ");
				
				if ( $ebdobj->getName()->compareTo("") == 0 )
					$values->append("\"\"");
				else
					$values->append($ebdobj->getName()->toString());
				$values->append(", ");
				
				if ( strcmp ( $ebdobj->getVersion()->toString(), "" ) == 0 )
					$values->append("\"\"");
				else
					$values->append($ebdobj->getVersion()->toString());
				$values->append(", ");
				
				if ( $ebdobj->getDescription()->compareTo ( "" ) == 0 )
					$values->append( "\"\"");
				else
					$values->append($ebdobj->getDescription()->toString());
				$values->append(", ");
				
				if ( $ebdobj->getHomepage()->compareTo( "" ) == 0 )
					$values->append( "\"\"" );
				else
					$values->append($ebdobj->getHomepage()->toString());
				$values->append(", ");
				
				if ( strcmp ( $ebdobj->getArch()->toString(), "" ) == 0 )
					$values->append("\"\"");
				else
					$values->append($ebdobj->getArch()->toString());
				$values->append(", ");
				
				if ( strcmp ( $ebdobj->getLicense()->toString(), "" ) == 0 )
					$values->append("\"\"");
				else
					$values->append($ebdobj->getLicense()->toString());
				$values->append(", ");
				
				if ( (int)$ebdobj->getMasked()->getVal() == 0 )
					$values->append("0");
				else
					$values->append($ebdobj->getMasked()->getVal());
				$values->append(" );");
				
				$ok = sqlite_exec($this->db, $insert_query.$values->toString());
				if ( !$ok )
					throw new SQLException( $insert_query.$values->toString() );
				
				$cnt++;
				$percentage->setStr(sprintf("%d/%d [%.2f%%]",$cnt,$total_cnt,(($cnt/$total_cnt) * 100.0)));
				for ( $i = 0 ; $i < $pre_percentage_length ; $i++ )
					print("\010");
				print( $percentage );
				$pre_percentage_length = $percentage->length();
			}
			// commit transaction and finalize
			$ok = sqlite_exec($this->db, "commit transaction");
			
			if ( !$ok )
				throw new SQLException("commit transaction");
			
			// print "done."
			for ( $i = 0 ; $i < $pre_percentage_length ; $i++ )
				print("\010");
			$format = new String("%-");
			$format->append($pre_percentage_length."s\n");
			print(sprintf($format,"done."));
			
			// close database when the fetching portage tree is completed.
			sqlite_close($this->db); 
		}
		else if ( extension_loaded("sqlite3") )
		{
			// code for manipulating sqlite database using sqlite3 extension
			// based on object-orienced. It is different from above case.
			
			// sqlite3 optimization
			$this->db->exec("pragma synchronous=off");
			$this->db->exec("pragma journal_mode=memory");
			$this->db->exec("pragma temp_store=memory");
			$this->db->exec("begin transaction");
			
			// prepare statement and bind each variable into parameter			
			$insert_query = new String("insert into portagetree ( category, name, version, description, homepage, arch, license, masked )");
			$insert_query->append(" values (:cat,:name,:ver,:desc,:home,:arch,:lic,:mask);");
				
			$stmt = $this->db->prepare($insert_query->toString());
				
			$stmt->bindParam(":cat", $cat);
			$stmt->bindParam(":name", $name);
			$stmt->bindParam(":ver", $ver);
			$stmt->bindParam(":desc", $desc);
			$stmt->bindParam(":home", $home);
			$stmt->bindParam(":arch", $arch);
			$stmt->bindParam(":lic", $lic);
			$stmt->bindParam(":mask", $mask);
			
			// insert each row into database until finished
			print(" * Fetching portage tree data . . . ");
			$cnt = 0;
			$result = 0;
			$pre_percentage_length = 0;
			$percentage = new String();
			$total_cnt = count ( $this->ebuild_array );
			
			foreach ( $this->ebuild_array as &$ebdobj)
			{
				$cat = $ebdobj->getCategory()->toString();
				$name = $ebdobj->getName()->toString();
				$ver = $ebdobj->getVersion()->toString();
				$desc = $ebdobj->getDescription()->toString();
				$home = $ebdobj->getHomepage()->toString();
				$arch = $ebdobj->getArch()->toString();
				$lic = $ebdobj->getLicense()->toString();
				$mask = $ebdobj->getMasked()->getVal();
				
				$result = $stmt->execute();
				
				$cnt++;
				$percentage->setStr(sprintf("%d/%d [%.2f%%]",$cnt,$total_cnt,(($cnt/$total_cnt) * 100.0)));
				for ( $i = 0 ; $i < $pre_percentage_length ; $i++ )
					print("\010");
				print( $percentage );
				$pre_percentage_length = $percentage->length();
			}
			
			
			// commit transaction and finalize
			$ok = $this->db->exec("commit transaction");
			if ( !$ok )
				throw new SQLException("commit transaction");
			
			$ok = $result->finalize();
			if ( !$ok )
				throw new SQLException("(we cannot finalize.)");

			// print "done."
			for ( $i = 0 ; $i < $pre_percentage_length ; $i++ )
				print("\010");
			$format = new String("%-");
			$format->append($pre_percentage_length."s\n");
			print(sprintf($format,"done."));
			
			// close database when the fetching portage tree is completed.
			$this->db->close();
		}
		else
			throw new ModuleNotLoadedException("sqlite or sqlite3");
		
		list ( $eusec, $esec ) = explode( " ",microtime() );
		$etime = ( $eusec + $esec );
		
		print("     - Elapsed processing time : ".$this->getElapsedTimeString($stime, $etime)."\n\n");
		
		$this->finalizePortageDatabase();
	}
	
	private function collectPortageTreeElements ()
	{
		$category_array = scandir($this->portage_parent_directory);
		$this->mask_checker = new MaskedListObject($this->portage_parent_directory);
		$cnt_masked_package = 0;
		$cnt_pkg = 0;
		
		print(" * Collecting portage tree information . . . .");
		foreach ( $category_array as &$cat )
		{
			$category = "";
			$CATEGORY_DIR = "";
		
			if ( preg_match( "/[a-z1-9]{3,5}\-[a-z1-9]{2,}/" , $cat ) )
				$category = $cat;
			else
			{
				if ( strcmp($cat,"virtual") == 0 )
					$category = $cat;
				else continue;
			}
		
			$CATEGORY_DIR = $this->portage_parent_directory."/".$category;
			$pkg_array = scandir($CATEGORY_DIR);
		
			foreach ( $pkg_array as &$pkg )
			{
				$package = "";
				$PACKAGE_DIR = "";
					
				if ( !preg_match("/metadata\.xml/", $pkg)
						&& !preg_match("/[\.]{1,2}/", $pkg ) )
					$package = $pkg;
				else	continue;
					
				$PACKAGE_DIR = $CATEGORY_DIR."/".$package;
				$ebuild_array = scandir($PACKAGE_DIR);
					
				foreach ( $ebuild_array as &$ebd )
				{
					$ebuild = "";
		
					// accept ebuild file only.
					if ( preg_match("/\.ebuild$/", $ebd) )
						$ebuild = $ebd;
					else 	continue;
		
					$FULL_PATH = $PACKAGE_DIR."/".$ebuild;
		
					$pkgobj = $this->getPackageInfoObject ( $FULL_PATH, $category, $package, $ebuild );
						
					if ( $this->mask_checker->isMaskedPkg($pkgobj) )
					{
						$pkgobj->setMasked(true);
						$cnt_masked_package++;
					}
						
					array_push($this->ebuild_array, $pkgobj);
					
					if ( $cnt_pkg % 4 == 0 )
						print("\010-");
					else if ( $cnt_pkg % 4 == 1 )
						print("\010\\");
					else if ( $cnt_pkg % 4 == 2 )
						print("\010|");
					else if ( $cnt_pkg % 4 == 3 )
						print("\010/");
					
					$cnt_pkg++;
				}
			}
		}
		print("\010done.\n");
		print("     - Total ".$cnt_pkg." packages.\n");
		print("     - ".$cnt_masked_package." masked packages are being.\n\n");
	}
	
	public function getPackageInfoObject ( $filename, $category, $package, $ebuild )
	{
		$ebuild_obj = new EbuildObject( $filename );
	
		$pkgobj = new PackageInfoObject();
		$verobj = new VersionObject();
		$licobj = new LicenseObject();
		$arcobj = new ArchObject();

		$verobj->setVersion($this->getVersionString($package,$ebuild));
		$licobj->setLicense($ebuild_obj->getValue("LICENSE"));
		$arcobj->setArch($ebuild_obj->getValue("KEYWORDS"));
		
		$pkgobj->setCategory($category);
		$pkgobj->setName($package);
		$pkgobj->setVersion($verobj);
		$pkgobj->setLicense($licobj);
		$pkgobj->setArch($arcobj);
		
		if ( strcmp ($ebuild_obj->getValue("HOMEPAGE"), "\${BASE_URI}") == 0 )
			$pkgobj->setHomepage($ebuild_obj->getValue("BASE_URI"));	
		else 
			$pkgobj->setHomepage($ebuild_obj->getValue("HOMEPAGE"));
		
		$pkgobj->setDescription($ebuild_obj->getValue("DESCRIPTION"));
	
		return $pkgobj;
	}
	
	private function getVersionString( $package, $ebuild )
	{
		$s = new String();
		$p = new String();
		$s->setStr($ebuild);
		$p->setStr($package);
		return $s->substr($p->length() + 1, ($s->length() - $p->length() - 8));
	}
	
	private function getElapsedTimeString ( $stime, $etime )
	{
		$result = new String ();
		$elapsed_time = ($etime - $stime);
		
		if ( $elapsed_time >= 3600.0 )
		{
			$result->append(sprintf("%d:",round($elapsed_time / 3600)));
			$elapsed_time -= (round($elapsed_time / 3600) * 3600);
		}
		else
			$result->append("00:");
		
		if ( $elapsed_time >= 60.0 )
		{
			$result->append(sprintf("%d:",round($elapsed_time / 60)));
			$elapsed_time -= (round($elapsed_time / 60) * 60);
		}
		else
			$result->append("00:");
		
		if ( $elapsed_time >= 1.0 )
		{
			$result->append(sprintf("%d.",floor($elapsed_time)));
			$elapsed_time -= floor($elapsed_time);
		}
		else 
			$result->append("00.");
		$msec = new String( sprintf("%.3f",$elapsed_time) );
		$msec->setStr($msec->substr(2, $msec->length() - 2));
		$result->append($msec);
		
		return $result;
	}
	
	private function finalizePortageDatabase ()
	{
		print(" * Copying {$this->getTempDBFilename()} to {$this->getLocalDBFilename()} . . . ");
		copy($this->getTempDBFilename(),$this->getLocalDBFilename());
		print("done.\n\n");
		
		print(" * Portage database is ready\n\n");
	}
}

?>
