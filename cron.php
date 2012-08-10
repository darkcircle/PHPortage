#!/usr/bin/php
<?php
	/*
	 * do not change this code.
	 * completed to implement
	 */
	include ( "Include.inc" );
	
	$fetch = new PortageTreeFetcher($PARENT_DIR, $TEMP_DBNAME, $LOCAL_DBNAME);
	$fetch->fetchPortage();
	
?>
