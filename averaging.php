<?php
//--------------------------------------------------------------
// WMS V2
// 
// Modified: 26-JAN-2014 by Jeffrey L Meyer
//--------------------------------------------------------------

require_once("wmsconfig.php");
require_once("ca.php");
require_once("db_connect.php");

$ossdb = oss_connect();
$efile = SITEEXPORTDIR . "/YOUR_YARD_averages.csv";

if($user == "itdept" || $user == "osscorp" ){
// desktop view
	$MGR = 1;
	$iframe_height = "90%";
	$iframe_width = "100%";
}else{
	// smaller frame set to fit 7" portable Windows tablet
	$iframe_height = "450";
	$iframe_width = "95%";
	$MGR=0;
}

?>

<html><head><title>YOUR STONE CO. - WMS V2.2.0 - <? echo SITENAME; ?></title>

<style type="text/css">
body {
	background:#FEFEFE;
}
</style>
<meta name="http-pragma" content="no-cache" />
<link href="css/redmond/jquery-ui-1.10.3.custom.css" rel="stylesheet">
	<script src="js/jquery-1.9.1.js"></script>
	<script src="js/jquery-ui-1.10.3.custom.js"></script>
</head>
<body>

<h2>Average Costing</h2>

<table border="1" cellpadding="2" cellspacing="0" width="100%">
<tr>
	<td>Code</td><td>Item Name</td><td>Avg. Bare Cost</td><td>Avg. Cost<br />With FRT</td><td>On Hand</td><td>U/M</td>
</tr>

<?
	$UITEMS = array();
//	$UITEMS = getNames();
getNames();


$fp = fopen($efile, "w");
	if(!$fp){
		exit("Error creating export file...$efile");
	}
	fputs($fp,"YOUR_YARD_NAME,,,,,\nCode,Item Name,Avg. Cost,Avg. Cost W/FRT,On Hand,U/M\n");
	
foreach($UITEMS as $INAME){
//	$ta = @split('(',$INAME,2);
	getAvg($INAME);
}

fclose($fp);

echo "<br /><br /><a href='" . SITEURL . "/mgr/exports/YOUR_YARD_NAME_averages.csv'>Click to download csv</a>";
?>

</table>


</body>
</html>

<?
@mysqli_close($ossdb);

//-----------------------------------------------------------
function getNames()
{
	global $UITEMS,$ossdb;
	
	$sql = "SELECT DISTINCT category FROM oss_inv_lc.item_data  WHERE 1 AND item_location NOT IN('RST','LTL','GONE','NEW_SKU','XFER') AND zone NOT IN('TUMBLED','SHIPPED','PICKED_UP') AND zone NOT LIKE 'Outgoing%' ORDER BY item_type ASC";
	if(!$rs = mysqli_query($ossdb, $sql)){
		exit(mysqli_error($ossdb));
	}
	
	$rows = mysqli_num_rows($rs);
	if($rows >0){
		while($rows > 0){
			$rd = mysqli_fetch_assoc($rs);
				if(!in_array($rd['category'],$UITEMS)){
					$UITEMS[] = rtrim($rd['category'],"\r\n\t ");
				}
			$rows--;
		}
		mysqli_free_result($rs);
	}

}

function getAvg($cname)
{
	global $ossdb,$fp;
	
	$iArr = array();
	
	$rowd = "<tr>";

	
	$sql = "SELECT costnf,costf,weight,sqft,code FROM oss_inv_lc.item_data WHERE category like '$cname%' AND zone NOT IN('TUMBLED','SHIPPED','PICKED_UP') AND item_location NOT IN('RST','LTL','GONE','NEW_SKU','XFER') AND zone NOT LIKE 'Outgoing%' ORDER BY SKU ASC";

	$OnHand = 0;	
	if(!$rs = mysqli_query($ossdb, $sql)){
		exit(mysqli_error($ossdb));
	}
	$tons = 1;
	$UM = " TONS";
	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		while($rows > 0){
			$rd = mysqli_fetch_assoc($rs);
			
			if($rd['sqft'] >0){
				$acost = $rd['costnf'] / $rd['sqft'];
				$OnHand += $rd['sqft'];
				$tons=0;
			}else{
				$acost = $rd['costnf'] / ($rd['weight']/2000);
				$OnHand += $rd['weight']; // LBS
				$tons=1;
			}
			$iArr[] = $acost;
			$rows--;
		}
		mysqli_free_result($rs);
	}
	$avg_cost = number_format(average($iArr),4);

	$iArr=array();
	if(!$rs = mysqli_query($ossdb, $sql)){
		exit(mysqli_error($ossdb));
	}
	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		while($rows > 0){
			$rd = mysqli_fetch_assoc($rs);
			$code=$rd['code'];
			if($rd['sqft'] >0){
				$acost = $rd['costf'] / $rd['sqft'];
			}else{
				$acost = $rd['costf'] / ($rd['weight']/2000);
			}
			$iArr[] = $acost;
			$rows--;
		}
		mysqli_free_result($rs);
	}
	$avg_costF = number_format(average($iArr),4);

	if($tons){
		$OnHand_F = number_format($OnHand/2000,4);
		$UM = "TONS";
	}else{
		$OnHand_F = number_format($OnHand,4);
		$UM = "SF";
	}
	$rowd .= "<td>$code</td><td>$cname</td><td align='right'>\$ $avg_cost</td><td align='right'><b>\$ $avg_costF</b></td><td align='right'><font color='blue'>$OnHand_F</font></td><td>$UM</td></tr>";
	echo $rowd;
	$rowd = "";		
	fputs($fp, $code . "," . str_replace(",", "-", $cname) . "," . str_replace(",","",$avg_cost) . "," . str_replace(",","",$avg_costF) . "," . str_replace(",","",$OnHand_F) . ",$UM\n");

}

function average($arr) {
 if(!empty($arr)){ return array_sum($arr) / count($arr);}else{return 0;}
}

?>