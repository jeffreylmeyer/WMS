<?php
// called via Ajax/jQuery from add, edit, move forms

require_once("db_connect.php");
$oss = oss_connect();

$options = "";

$syard = isset($_REQUEST['yard']) ? $_REQUEST['yard']:"";
$sku = isset($_REQUEST['sku']) ? $_REQUEST['sku']:"";

if($syard !=""){
	$sqlcmd = "SELECT DISTINCT zone FROM bin_location_map WHERE yard='$syard' AND status='avail' OR (status='full' AND item_ref='$sku') ORDER BY recid ASC";
	if(!$srs = mysqli_query($oss, $sqlcmd)){
		exit("ERROR: " . mysqli_error($oss));
	}
	$rows = mysqli_num_rows($srs);
	if($rows > 0){

		while($rows > 0){
			$srd = mysqli_fetch_assoc($srs);
			$options .= "<option value=\"" . $srd['zone'] . "\">$srd[zone]</option>\n";

			$rows--;
		}
		mysqli_free_result($srs);
	}
	@mysqli_close($oss);
	echo $options;
}else{
	echo "";
}
?>