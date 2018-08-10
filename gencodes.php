<?php
// generate template for barcodes
// not used in normal operations
function getcode()
{

require_once("db_connect.php");
$ossdb = oss_connect();

//if(!$rs = mysqli_query($ossdb, "USE overstockstone_magento")){
//	exit("ERROR: " . mysqli_error($ossdb));
//}

//$sqlcmd = "SELECT MAX(sku) FROM catalog_product_entity";
//if(!$rs = mysqli_query($ossdb, $sqlcmd)){
//	exit("ERROR: " . mysqli_error($ossdb));
//}

$sku = 0;

//$rows = mysqli_num_rows($rs);
//if($rows > 0){
//	$rd = mysqli_fetch_row($rs);
//	mysqli_free_result($rs);
//	$sku = $rd[0];	
//}

// check temp table to make sure we don't overwrite codes
//if(!$rs = mysqli_query($ossdb, "USE oss_temp")){
//	exit("ERROR: " . mysqli_error($ossdb));//
//}

$sqlcmd = "SELECT MAX(SKU) FROM item_data";
if(!$rs = mysqli_query($ossdb, $sqlcmd)){
	exit("ERROR: " . mysqli_error($ossdb));
}

$sku2 = 0;

$rows = mysqli_num_rows($rs);
if($rows > 0){
	$rd = mysqli_fetch_row($rs);
	mysqli_free_result($rs);
	$sku2 = $rd[0];	
}

if($sku2 > $sku){
	$sku = $sku2; // pick highest number
}

if($sku == 0){
	$sku = STARTSKU; // default from config
}
@mysqli_close($ossdb);

$sku++;

return $sku;
}


?>