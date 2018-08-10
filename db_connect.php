<?php
//--------------------------------------------------------------------------------------------
// Database connection functions
// Created: 15-SEP-2010 by Jeffrey L. Meyer
// Copyright 2010 Jeffrey L. Meyer
// Embedded code - licensed to anyone who needs it
//
// Modified: 26-JAN-2014 by Jeffrey L Meyer (To add CONSTANTS [from wmsconfig.php] for login)
//--------------------------------------------------------------------------------------------
if(!defined("_CONFIG")){
	require_once("wmsconfig.php");
}
if(!defined("THISDBSERVER")){
	define("THISDBSERVER", SITEDBID);
}


function oss_connect()
{
	global $SITELOCALDBHOST,$SITELOCALDBUSER,$SITELOCALDBPWD,$SITEDBNAME;
	
	$DBlink = mysqli_connect("localhost",$SITELOCALDBUSER,$SITELOCALDBPWD, $SITEDBNAME);
	$retry = 5;
	if(!$DBlink){
	echo mysqli_connect_error();
		while($retry > 0 && !$DBlink = mysqli_connect("localhost",$SITELOCALDBUSER,$SITELOCALDBPWD, $SITEDBNAME)){
			echo mysqli_connect_error();
			sleep(5);
			$retry--;
		}
	}
	if(!$DBlink){

		exit("Could not connect to [local-oss] database (from db_connect.php on " . THISDBSERVER . ").");
	}

	return $DBlink;
}

function ossm_connect()
{
	global $SITEREMOTEDBUSER, $SITEREMOTEDBPWD;
	
	$DBlink = mysqli_connect("localhost", $SITEREMOTEDBUSER, $SITEREMOTEDBPWD, "categories");
	$retry = 5;
	if(!$DBlink){
		echo mysqli_connect_error();
		while($retry > 0 && !$DBlink = mysqli_connect("localhost", $SITEREMOTEDBUSER, $SITEREMOTEDBPWD, "categories")){
			echo mysqli_connect_error();
			sleep(5);
			$retry--;
		}
	}
	if(!$DBlink){

		exit("Could not connect to [oss-main] database (from db_connect.php on " . THISDBSERVER . ").");
	}

	return $DBlink;
}

?>