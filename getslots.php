<?php
// called via Ajax/jQuery from add, edit, move forms

require_once("db_connect.php");
$oss = oss_connect();

$options = "";

$szone = isset($_REQUEST['zone']) ? $_REQUEST['zone']:"";
$sku = isset($_REQUEST['sku']) ? $_REQUEST['sku']:"";

if($szone !=""){
	$sqlcmd = "SELECT bin_loc FROM bin_location_map WHERE yard='YOUR_YARD_NAME' AND zone='$szone' AND status='avail' OR (status='full' AND item_ref='$sku') ORDER BY recid ASC";
	if(!$srs = mysqli_query($oss, $sqlcmd)){
		exit("ERROR: " . mysqli_error($oss));
	}
	$rows = mysqli_num_rows($srs);
	if($rows > 0){

		while($rows > 0){
			$srd = mysqli_fetch_assoc($srs);
			$options .= "<option value=\"" . $srd['bin_loc'] . "\">$srd[bin_loc]</option>\n";

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