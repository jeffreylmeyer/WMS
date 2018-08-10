<?php
// fix category, remove trailing |


require_once("db_connect.php");
$ossdb = oss_connect();

$sku = 0;


$sqlcmd = "SELECT SKU,category FROM item_data WHERE 1";
if(!$rs = mysqli_query($ossdb, $sqlcmd)){
	exit("ERROR: " . mysqli_error($ossdb));
}


$rows = mysqli_num_rows($rs);
echo "Rows: $rows\n";
sleep(5);

if($rows > 0){
	while($rows > 0){
		$rd = mysqli_fetch_assoc($rs);
		echo $rd['SKU'] . "\n";

	
		$cat = $rd['category'];
		$cat = rtrim($cat,'|');
		$sku = $rd['SKU'];
	
	if($cat != "" && $sku != ""){
		$sqlcmd = "UPDATE item_data SET category='$cat' WHERE SKU='$sku'";
		if(!$rs2 = mysqli_query($ossdb, $sqlcmd)){
			echo "Error: $sku : $cat\n";
		}else{
			echo "OK: $cat\n";
		}
	
	}	
	$rows--;
	}
	mysqli_free_result($rs);
}


@mysqli_close($ossdb);

echo "\n\nDone\n\n";



?>