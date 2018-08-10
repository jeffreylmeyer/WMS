<?php
// generate actual csv file for importing into Excel
ob_start();

$startcode = isset($_REQUEST['startcode']) ? $_REQUEST['startcode'] : "";
$startcode = intval($startcode);

$qty = isset($_REQUEST['qty']) ? $_REQUEST['qty'] : 0;

$df = date("YmdHis");

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"gentemplate_".$df.".csv\"");

echo "Product ID,Name,Description,Cost,Qty,G,Weight (LBS),W\",L\",H\",Lot#\n";

for($idx = $startcode; $idx <= ($startcode + $qty);$idx++){
	echo "$idx,,,,1,,,,,\n";
}

ob_end_flush();
?>