<?php
//------------------------------------------------------------------------
// Move Entire Order from bin to outgoing or shipped
// created: 21-JAN-2016 by Jeffrey L Meyer 
// 
//------------------------------------------------------------------------
require_once("wmsconfig.php");

require_once("functions.php");
require_once("db_connect.php");
$HDIR = SITEHDIR;
$msg = "";

$ossdb = oss_connect();

if(DEFAULTYARD =="YOUR_YARD_1"){
	mysqli_query($ossdb,"USE yard1DB");
}
if(DEFAULTYARD=="YOUR_YARD_2"){
	mysqli_query($ossdb,"USE yard2DB");
}
if(DEFAULTYARD=="YOUR_YARD_3"){
	mysqli_query($ossdb,"USE yard3DB");
}

$orderNo = isset($_REQUEST['order_no']) ? $_REQUEST['order_no'] : "";
$cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : "";


if($cmd==""){
	sendForm1("");
}

if($cmd == "moveorder"){
	MoveOrder($orderNo);
	echo "Order moved...";
	
	echo "\n\n<!-- <pre>" . print_r($_REQUEST,1) . "</pre> -->\n\n";
}

$err = 0;


@mysqli_close($ossdb);
exit(0);

//--------------------------------------------------------
// Displays the Order details
function sendForm1($msg="")
{
	global $ossdb,$orderNo;
	$ITEMS = array();
	$RowIDX = 0;
	$RowCNT = 0;
?>
	<style type="text/css">
h4 {
	font-size:11pt;
	font-weight:bold;
}
.wmsmain {
	background:#F0F0F0;
	font-family:Verdana,arial,helvetica,tahoma;
	font-size:10pt;
	color:#000000;
	margin:auto;
	padding-left:5px;
}
</style>
<? 
	require_once("header.php");
	
?>

<?
	$sql = "SELECT * FROM invoices WHERE order_no='$orderNo' ORDER BY item_SKU ASC";
	if(!$rs = mysqli_query($ossdb, $sql)){
		safeexit("Error querying order $orderNo");
	}
	$rows = mysqli_num_rows($rs);
	$RowCNT = $rows-1;
	
	if($rows > 0){
		while($rows > 0){
			$ITEMS[$RowIDX] = mysqli_fetch_assoc($rs);	
			$RowIDX++;
			$rows--;
		}
		mysqli_free_result($rs);
	}

	echo "<h3>Order Number: $orderNo</h3>";
	echo "Customer: " . $ITEMS[0]["customer_name"]. "<br /><br />";
?>
<style>
select {
	height:30px;
	font-size:12pt;
	background:#E0E0F0;
}
</style>
<form method="post" action="moveorder.php" name="sform">
<input type="hidden" name="order_no" id="order_no" value="<?=$orderNo?>" />
<input type="hidden" name="cmd" value="moveorder" />

<table border="1" cellpadding="3" cellspacing="0" width="100%" style="font-family:arial,helvetica,tahoma;font-size:13pt;font-weight:bold;background:#FEFEFE">
<tr><td>SKU</td><td>Current Loc</td><td>Move To Zone</td><td colspan='3' align='center'>New Location</td></tr>
<?


$ItemCnt=-1;

for($RowIDX=0; $RowIDX <= $RowCNT; $RowIDX++){
	if($ITEMS[$RowIDX]['item_Desc'] != "FREIGHT"){
		//check for multiple slots
		$slots=1;
		$ItemCnt++;
		$OITEM = getItem($ITEMS[$RowIDX]['item_SKU'],0);
		
		if(substr_count($ITEMS[$RowIDX]['item_location'],"|") == 1){ $slots=2;}
		if(substr_count($ITEMS[$RowIDX]['item_location'],"|") == 2){ $slots=3;}
	
	
		echo "<tr><td>" . $ITEMS[$RowIDX]["item_SKU"];
		?>
		
		 <input type="hidden" name="itemrows<?=$ItemCnt?>" value="<?=$ITEMS[$RowIDX]['item_SKU']?>" /></td><td>
		 
		 <?
		$ITEMS[$RowIDX]['item_location'] . "</td><td>";
		
		// show current slot
		echo "<small>" . $OITEM['zone'] . "|" . $OITEM['bin_loc'] . "|" . $OITEM['bin_loc2'] . "|" . $OITEM['bin_loc3'] . "</small></td><td>";
		//insert new slots
		?>
		
		<select size="1" name="itemzone<?=$ItemCnt?>" id="itemzone<?=$ItemCnt?>" onchange="zoneChange(<?=$ItemCnt?>)" />
			<option value="NA">(not used)</option>
			<option value="OutgoingN" <? if($OITEM['zone'] == "OutgoingN"){ echo " selected='selected'"; }?>>OutgoingN</option>
			<option value="OutgoingS" <? if($OITEM['zone'] == "OutgoingS"){ echo " selected='selected'"; }?>>OutgoingS</option>
			<option value="OutgoingE" <? if($OITEM['zone'] == "OutgoingE"){ echo " selected='selected'"; }?>>OutgoingE</option>
			<option value="OutgoingW" <? if($OITEM['zone'] == "OutgoingW"){ echo " selected='selected'"; }?>>OutgoingW</option>
			<option value="SHIPPED" <? if($OITEM['zone'] == "SHIPPED"){ echo " selected='selected'"; }?>>SHIPPED</option>
			<option value="PICKED_UP" <? if($OITEM['zone'] == "PICKED_UP"){ echo " selected='selected'"; }?>>PICKED_UP</option>
			<option value="TUMBLED" <? if($OITEM['zone'] == "TUMBLED"){ echo " selected='selected'"; }?>>TUMBLED (LC Only)</option>
		</select>
		</td><td>
		<select size="1" name="item_binloc<?=$ItemCnt?>" id="item_binloc<?=$ItemCnt?>" />
			<option value="<?=$OITEM['bin_loc']?>" selected='selected'><?=$OITEM['bin_loc']?></option>
		</select>
		</td><td>
		
		<select size="1" name="item_binloc2<?=$ItemCnt?>" id="item_binloc2<?=$ItemCnt?>" />
			<? if($OITEM['bin_loc2']!=""){?><option value="<?=$OITEM['bin_loc2']?>" selected='selected'><?=$OITEM['bin_loc2']?></option><?}?>
		</select>
		</td><td>	
		
		<select size="1" name="item_binloc3<?=$ItemCnt?>" id="item_binloc3<?=$ItemCnt?>" />
			<? if($OITEM['bin_loc3']!=""){?><option value="<?=$OITEM['bin_loc3']?>" selected='selected'><?=$OITEM['bin_loc3']?></option><?}?>
		</select>

		<?
		

		echo "&nbsp;</td></tr>\n";
	}
}

?>
</table>
<br clear="all" />
<input type="hidden" name="ItemCnt" value="<?=$ItemCnt?>" />
<input type="submit" name="submit" value="SAVE" style="height:40px;font-weight:bold" /><b>(No Confirmation, NonReversable)</b>
</form>
<script src="js/orderslots.js"></script>
<?
	echo "\n\n<!-- <pre>" . print_r($_REQUEST,1) . "</pre> -->\n\n";
	
}



//---------------------------------------------------------
function MoveOrder($OrderNumber)
{
	global $ossdb;
	
	
	
	$ItemCnt = isset($_REQUEST['ItemCnt']) ? $_REQUEST['ItemCnt'] : 0;
	
	$ITEMDATA['newyard'] = DEFAULTYARD;
	
	for($idx=0; $idx <= $ItemCnt; $idx++){
		$ITEMDATA['barcode'] = isset($_REQUEST["itemrows$idx"]) ? $_REQUEST["itemrows$idx"] : "";
		$ITEMDATA['newzone'] = isset($_REQUEST["itemzone$idx"]) ? $_REQUEST["itemzone$idx"] : "";
		$ITEMDATA['newbin_loc'] = isset($_REQUEST["item_binloc$idx"]) ? $_REQUEST["item_binloc$idx"] : "";
		$ITEMDATA['newbin_loc2'] = isset($_REQUEST["item_binloc2$idx"]) ? $_REQUEST["item_binloc2$idx"] : "";
		$ITEMDATA['newbin_loc3'] = isset($_REQUEST["item_binloc3$idx"]) ? $_REQUEST["item_binloc3$idx"] : "";
		
		if($ITEMDATA['newbin_loc'] !=""){

			// update order (invoices table) and update item_data	
		
			$sql = "UPDATE invoices SET item_location='$ITEMDATA[newbin_loc]";
			if($ITEMDATA['newbin_loc2'] != ""){
				$sql .= "|".$ITEMDATA['newbin_loc2'];
			}
			if($ITEMDATA['newbin_loc3'] != ""){
				$sql .= "|".$ITEMDATA['newbin_loc3'];
			}
			$sql.="',";
		
			if(substr($ITEMDATA['newzone'],0,7) == "Outgoin"){
				$sql .= "item_status='OUTGOING'";
			}
			if($ITEMDATA['newzone'] == "PICKED_UP"||$ITEMDATA['newzone'] == "SHIPPED"||$ITEMDATA['newzone'] == "TUMBLED"){
				$sql.="item_status='GONE' WHERE item_SKU='$ITEMDATA[barcode]'";
				mysqli_query($ossdb, $sql);
				sleep(1);
				mysqli_query($ossdb,"DELETE FROM invoice WHERE item_SKU='" . $ITEMDATA['barcode'] . "'");
			}else{
				$sql.=" WHERE order_no='$OrderNumber'";
				if(!$rs = mysqli_query($ossdb, $sql)){
					safeexit("MYSQL Error: " . mysqli_error($ossdb));
				}
			}
			echo "\n\n<!-- <pre>" . $sql . "</pre>\n\n -->\n\n";
			MoveItem($ITEMDATA);
		}
	}

}

//-----------------------------------------------------------------
function MoveItem($ITEM)
{
	global $ossdb;

	$MITEM = getItem($ITEM['barcode'],0);	// get magento data
	
	$TransferYN = "N";
	
	if($MITEM['yard'] != $ITEM['newyard']){
		$Transfer = "Y";
	}
	
	//$Entity_ID = getEntityID($ITEM['barcode']);
	
	$MITEM['sku'] = $ITEM['barcode'];
	
	if($ITEM['newbin_loc']==""){
		return 0;
	}
	
	// Update bin_location_map
		$sqlcmd = "UPDATE bin_location_map SET status='avail',item_ref='-' WHERE yard='$MITEM[yard]' AND zone='$MITEM[zone]' AND bin_loc='$MITEM[bin_loc]'";
		if(!$rs = mysqli_query($ossdb, $sqlcmd)){
			safeexit("ERROR: " . mysqli_error($ossdb) . "<br />\n$sqlcmd");
		}
		
	if($MITEM['bin_loc2'] != ""){
		$sqlcmd = "UPDATE bin_location_map SET status='avail',item_ref='-' WHERE yard='$MITEM[yard]' AND zone='$MITEM[zone]' AND bin_loc='$MITEM[bin_loc2]'";
		if(!$rs = mysqli_query($ossdb, $sqlcmd)){
			safeexit("ERROR: " . mysqli_error($ossdb) . "<br />\n$sqlcmd");
		}
	}
	if($MITEM['bin_loc3'] != ""){
		$sqlcmd = "UPDATE bin_location_map SET status='avail',item_ref='-' WHERE yard='$MITEM[yard]' AND zone='$MITEM[zone]' AND bin_loc='$MITEM[bin_loc3]'";
		if(!$rs = mysqli_query($ossdb, $sqlcmd)){
			safeexit("ERROR: " . mysqli_error($ossdb) . "<br />\n$sqlcmd");
		}
	}

	$EITEM = $MITEM;

	if($TransferYN == "Y"){
		// remove item from local inventory
		// Receiving yard will need to set new location once received
		// Location will be marked as XFER until actually received at other yard
		$EITEM['newbin_loc'] = $ITEM['newbin_loc'];
		$EITEM['newyard'] = $ITEM['newyard'];
		$EITEM['newzone'] = $ITEM['newzone'];
		sendMessage($EITEM,"XFER");
		//logItemTransaction("ITEM_XFER_FROM", $MITEM);
		$MITEM['bin_loc'] = $ITEM['newbin_loc'];
		$MITEM['yard'] = $ITEM['newyard'];
		$MITEM['zone'] = $ITEM['newzone'];
		$MITEM['status'] = 0;		
		//logItemTransaction("ITEM_XFER_TO", $MITEM);
	}else{
		$EITEM['newbin_loc'] = $ITEM['newbin_loc'];
		$EITEM['newyard'] = $ITEM['newyard'];
		$EITEM['newzone'] = $ITEM['newzone'];

		sendMessage($EITEM,"MOVE");
	//	logItemTransaction("ITEM_MOVE_FROM", $MITEM);
		$MITEM['bin_loc'] = $ITEM['newbin_loc'];
		$MITEM['bin_loc2'] = $ITEM['newbin_loc2'];
		$MITEM['bin_loc3'] = $ITEM['newbin_loc3'];
		
		if($ITEM['newbin_loc2']!=""){
			$MITEM['bin_loc'] .= "|" . $ITEM['newbin_loc2'];
		}
		if($ITEM['newbin_loc3']!=""){
			$MITEM['bin_loc'] .= "|" . $ITEM['newbin_loc3'];
		}
		
		$MITEM['yard'] = $ITEM['newyard'];
		$MITEM['zone'] = $ITEM['newzone'];
	//	logItemTransaction("ITEM_MOVE_TO", $MITEM);
		updateItemLocation($MITEM,$ITEM['barcode']);
	}
	
	//echo "<pre>" . print_r($MITEM,1) . "</pre>";
	//exit(0);
	
		
	if($TransferYN == "N" && $ITEM['newzone'] != "SHIPPED" && $ITEM['newzone'] != "PICKED_UP" && $ITEM['newzone'] != "TUMBLED"){
		$sqlcmd = "UPDATE bin_location_map SET status='full',item_ref='$ITEM[barcode]' WHERE yard='$ITEM[newyard]' AND zone='$ITEM[newzone]' AND bin_loc='$ITEM[newbin_loc]'";
		if(!$rs = mysqli_query($ossdb, $sqlcmd)){
			safeexit("ERROR: " . mysqli_error($ossdb) . "<br />\n$sqlcmd");
		}
		if($ITEM['newbin_loc2'] !=""){
			$sqlcmd = "UPDATE bin_location_map SET status='full',item_ref='$ITEM[barcode]' WHERE yard='$ITEM[newyard]' AND zone='$ITEM[newzone]' AND bin_loc='$ITEM[newbin_loc2]'";
			if(!$rs = mysqli_query($ossdb, $sqlcmd)){
				safeexit("ERROR: " . mysqli_error($ossdb) . "<br />\n$sqlcmd");
			}
		}
		if($ITEM['newbin_loc3'] !=""){
			$sqlcmd = "UPDATE bin_location_map SET status='full',item_ref='$ITEM[barcode]' WHERE yard='$ITEM[newyard]' AND zone='$ITEM[newzone]' AND bin_loc='$ITEM[newbin_loc3]'";
			if(!$rs = mysqli_query($ossdb, $sqlcmd)){
				safeexit("ERROR: " . mysqli_error($ossdb) . "<br />\n$sqlcmd");
			}
		}

	}
		// Update Magento Shopping Cart data
		// Mark as unavailable until transfer complete
/*		$magento_sql = "UPDATE overstockstone_magento.catalog_product_entity_varchar SET value='$ITEM[newzone]' WHERE entity_id='$Entity_ID' AND attribute_id='148'"; // new zone
		if(!$rs = mysqli_query($ossdb, $magento_sql)){
			exit("ERROR: " . mysqli_error($ossdb) . "<br />\n$magento_sql");
		}

		
		$magento_sql = "UPDATE overstockstone_magento.catalog_product_entity_varchar SET value='$ITEM[newbin_loc]' WHERE entity_id='$Entity_ID' AND attribute_id='149'"; // new bin_loc
		if(!$rs = mysqli_query($ossdb, $magento_sql)){
			exit("ERROR: " . mysqli_error($ossdb) . "<br />\n$magento_sql");
		}

		
		$magento_sql = "UPDATE overstockstone_magento.catalog_product_entity_int SET value='" . getOptionID($ITEM['newyard']) . "' WHERE entity_id='$Entity_ID' AND attribute_id='147'"; // new yard
		if(!$rs = mysqli_query($ossdb, $magento_sql)){
			exit("ERROR: " . mysqli_error($ossdb) . "<br />\n$magento_sql");
		}

	
	if($TransferYN == "Y"){
		$magento_sql = "UPDATE overstockstone_magento.catalog_product_entity_int SET value='0' WHERE entity_id='$Entity_ID' AND attribute_id='96'"; // disable product in shopping cart
		if(!$rs = mysqli_query($ossdb, $magento_sql)){
			exit("ERROR: " . mysqli_error($ossdb) . "<br />\n$magento_sql");
		}

	}
*/	
	
}

//-------------------------------------
function safeexit($msg)
{
	global $ossdb;
	@mysqli_close($ossdb);
	exit($msg);
}
?>