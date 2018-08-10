<?php
// review data
require_once("wmsconfig.php");

$HDIR = SITEDIR;

require_once("db_connect.php");
$ossdb = oss_connect();

$SKU = isset($_REQUEST['SKU']) ? $_REQUEST['SKU'] : "";

if($SKU == ""){
	exit("ERROR: Missing SKU Code");
}

$sqlcmd = "SELECT * FROM item_data WHERE SKU='" . mysqli_real_escape_string($ossdb, $SKU) . "'";
if(!$rs = mysqli_query($ossdb, $sqlcmd)){
	exit("ERROR: " . mysqli_error($ossdb));
}

$rows = mysqli_num_rows($rs);
if($rows > 0){
	$rd = mysqli_fetch_assoc($rs);
	
	foreach($rd as $key=>$val){
		if($key == "x1"){
			// show images
			echo "Top View = <img width=\"120\" src=\"" . str_replace("$HDIR/","",$val) . "\" /><br clear=\"all\" />";
		}elseif($key == "x2"){
			// show images
			echo "Side View = <img width=\"120\" src=\"" . str_replace("$HDIR/","",$val) . "\" /><br clear=\"all\" />";
		}else{
			echo "<small>$key = $val</small><br />";
		}
	}
	mysqli_free_result($rs);
}else{
	echo "$SKU Not found...<br />";
}

@mysqli_close($ossdb);

echo "<br /><br /><a href=\"step1.php\">Click here to process another new item</a>";

?>