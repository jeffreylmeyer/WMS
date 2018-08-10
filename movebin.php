<?php
//------------------------------------------------------------------------
// Move Item from one bin to another
// created: 21-NOV-2013 by Jeffrey L Meyer
// Modified: 26-JAN-2014 by Jeffrey L Meyer (To add config CONSTANTS)
//------------------------------------------------------------------------
require_once("wmsconfig.php");

require_once("functions.php");
require_once("db_connect.php");
//require_once("ca.php");
$ossdb = oss_connect();

$cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : "";


$ITEM = array();
$ITEM['barcode'] = isset($_REQUEST['barcode']) ? $_REQUEST['barcode'] : ""; // scanned for item to move

// Old Location Data
$ITEM['yard'] = isset($_REQUEST['yard']) ? $_REQUEST['yard'] : DEFAULTYARD;	
$ITEM['zone'] = isset($_REQUEST['zone']) ? $_REQUEST['zone'] : "";
$ITEM['bin_loc'] = isset($_REQUEST['item_location']) ? $_REQUEST['item_location'] : "";

$ITEM['bin_loc2'] = "";
$ITEM['bin_loc3'] = "";

if(strpos($ITEM['bin_loc'],"|")!==false){
	list($ITEM['bin_loc'],$ITEM['bin_loc2'],$ITEM['bin_loc3']) = explode("|", $ITEM['bin_loc']);
}


// New Location Data
$ITEM['newyard'] = isset($_REQUEST['newyard']) ? $_REQUEST['newyard'] : "";	
$ITEM['newzone'] = isset($_REQUEST['newzone']) ? $_REQUEST['newzone'] : "";
$ITEM['newbin_loc'] = isset($_REQUEST['newbin_loc']) ? $_REQUEST['newbin_loc'] : "";
$ITEM['newbin_loc2'] = isset($_REQUEST['newbin_loc2']) ? $_REQUEST['newbin_loc2'] : "";
$ITEM['newbin_loc3'] = isset($_REQUEST['newbin_loc3']) ? $_REQUEST['newbin_loc3'] : "";


$ITEM['weight'] = isset($_REQUEST['weight']) ? $_REQUEST['weight'] : "";
$ITEM['lot_no'] = isset($_REQUEST['lot_no']) ? $_REQUEST['lot_no'] : "";
$ITEM['pallet_no'] = isset($_REQUEST['pallet_no']) ? $_REQUEST['pallet_no'] : "";

$ITEM['category'] = isset($_REQUEST['category']) ? $_REQUEST['category'] : "";

$ITEM['lot_no'] = trim($ITEM['lot_no'],"\r\n\t ");

$HDIR = SITEHDIR;

$msg = "";

if($cmd == "moveitem"){
	Moveitem();
	echo "Item moved...";
}

if($ITEM['barcode'] == "" || $cmd=='ck'){
	sendForm1("");
	exit(0);
}else{
	if($ITEM['barcode'] == ""){
		sendForm1("ERROR: ITEM # MISSING - Retry");
		exit(0);
	}
}

$err = 0;




if($cmd == "scanned"){
	sendForm2($msg);
}

if($cmd == "confirm"){
	$msg ="Pick/Move Item";
	// confirmed is correct product, proceed with move data
	sendForm3($msg);
	
}

@mysqli_close($ossdb);

echo "<br clear=\"all\" /><br /><br /><br /><br /><br /><br />
<a href=\"intro.php\">Click here for main menu</a>
";

//--------------------------------------------------------
function sendForm1($msg="")
{
	global $ITEM;
	
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
<? require_once("header.php");
?>

<script src="js/slots.js"></script>

<div class="wmsmain">
	<h2 style="color:#FF0000"><? echo $msg; ?></h2>
	<form method="post" action="movebin.php">
	<input type="hidden" name="cmd" value="scanned" />
	
	<b>Scan Pallet Label Barcode or Type Number In</b><br />
	Product ID: <input type="text" name="barcode" id="barcode" value="<?=$ITEM['barcode'];?>" /><br />
	<br />
	<input type="submit" value="Lookup" />
	</form>

</div>
<script>
	$(document).ready(function(){
		$("input#barcode").focus();
	});
</script>
<?
	echo "\n\n<!-- <pre>" . print_r($_REQUEST,1) . "</pre> -->\n\n";
	
	
}

//--------------------------------------------------------
function sendForm2($msg="")
{
	global $err,$ITEM,$cmd;

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
	
	float:left;
}
</style>
<script type="text/javascript"> 

function stopRKey(evt) { 
  var evt = (evt) ? evt : ((event) ? event : null); 
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;} 
} 

document.onkeypress = stopRKey; 


</script>

<? require_once("header.php");
?>
<script src="js/slots.js"></script>


<div class="wmsmain">
<h2 style="color:#FF0000"><? echo $msg; ?></h2>Product ID: <?echo $ITEM['barcode']; ?><br />
<br />
<? 

$ITEMDATA = getItem($ITEM['barcode']);

if(!empty($ITEMDATA)){
//echo "<pre>" . print_r($ITEMDATA,1) . "</pre><br /><br />"; 

//foreach($ITEMDATA as $key=>$val){
//	echo getAttribText("$key") . ":$val<br />";
//}
?>
<h3>Please confirm you have the <br />correct item selected</h3>

<div id="leftc" style="float:left;width:450px;">
<?
	echo "Name: <b>".$ITEMDATA['name']."</b><br />
		Lot#: <b>$ITEMDATA[lot_no]</b> Pallet#: <b>$ITEMDATA[pallet_no]</b><br />
		Yard: <b>$ITEMDATA[yard]</b> <br />Zone: <b>$ITEMDATA[zone]</b> Slot: <b>$ITEMDATA[bin_loc]</b><br />
		&nbsp;&nbsp;&nbsp; Extra Slot 1: <b>$ITEMDATA[bin_loc2]</b>  Extra Slot 2: <b>$ITEMDATA[bin_loc3]</b><br />
		W: $ITEMDATA[width] L: $ITEMDATA[length] H: $ITEMDATA[height]<br />
		Weight: $ITEMDATA[weight] LBS<br /><br />
		Description: <blockquote>$ITEMDATA[description]</blockquote>
		</div>
		<div id='img' style='display:inline;float:left;width:120px;height:120px'><img width='120' src='".SITEURL."/images/catalog" . $ITEMDATA['image'] . "' />
		<img vspace='5' width='120' src='".SITEURL."/images/catalog" . $ITEMDATA['small_image'] . "' />
		<a href=\"step2.php?text=".urlencode($ITEMDATA['name'])."&weight=$ITEMDATA[weight]&yard=$ITEMDATA[yard]&lot_no=$ITEMDATA[lot_no]&pal_no=$ITEMDATA[pallet_no]&barcode=$ITEMDATA[barcode]\">Reupload Images</a>
		</div>
		<br clear='all' />
		<b>Is This Correct Item?</b>
	";

?>
<form name="confirmation" id="confirmation" enctype="multipart/form-data" action="movebin.php" method="POST">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
    <input type="hidden" name="cmd" value="confirm" />
    <input type="hidden" value="<?echo $ITEM['barcode']; ?>" name="barcode" /><br /><br />
        <input type="image" src="images/yes-btn.jpg" name="yesbtn" id="yesbtn" value="Yes" onclick="javascript:document.confirmation.submit();"/>
    <a href="movebin.php?cmd=ck&barcode=<?=$ITEM['barcode'];?>"><img src="images/no-btn.jpg" /></a>
    <br /><br />
</form>
</div>
<?
}else{
	echo "Item not on file at this location. Make sure code is correct. If this is a new item, use the Receive XFER form.";
}
}


//--------------------------------------------------------
function sendForm3($msg="")
{
	global $err,$ITEM;

?>
<style type="text/css">
h4 {
	font-size:11pt;
	font-weight:bold;
}
.wmsmain {
	background:#FFA0A0;
	font-family:Verdana,arial,helvetica,tahoma;
	font-size:10pt;
	color:#000000;
	margin:auto;
	padding-left:5px;
	float:left;
}
</style>

<? require_once("header.php");
?>

<script src="js/slots.js"></script>

<script type="text/javascript"> 

function stopRKey(evt) { 
  var evt = (evt) ? evt : ((event) ? event : null); 
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;} 
} 

document.onkeypress = stopRKey; 

$(function(){
	$("div#nextbtn").click(function(){
	if(confirm('Are you sure you wish to move this item?')){
		document.mconfirm.submit();
	}
	});
	return false;
});

</script>


<div class="wmsmain">
<h2 style="color:#FF0000"><? echo $msg; ?></h2>Product ID: <?echo $ITEM['barcode']; ?><br />
<br />
<? 

$ITEMDATA = getItem($ITEM['barcode']);

//echo print_r($ITEMDATA,1) . "<br /><br />"; 
//foreach($ITEMDATA as $key=>$val){
//	echo getAttribText("$key") . ":$val<br />";
//}
?>

<div id="leftc" style="float:left;width:500px;">
<?
	echo "Name: <b>".stripslashes($ITEMDATA['name'])."</b><br />
		Lot#: <b>$ITEMDATA[lot_no]</b> Pallet#: <b>$ITEMDATA[pallet_no]</b><br />
		Yard: <b>$ITEMDATA[yard]</b> Zone: <b>$ITEMDATA[zone]</b> Slot: <b>$ITEMDATA[bin_loc]</b><br />
		&nbsp;&nbsp;&nbsp; Extra Slot 1: <b>$ITEMDATA[bin_loc2]</b>  Extra Slot 2: <b>$ITEMDATA[bin_loc3]</b><br />
		W: <b>$ITEMDATA[width]</b>&quot; L: <b>$ITEMDATA[length]</b>&quot; H: <b>$ITEMDATA[height]</b>&quot;<br />
		Weight: <b>$ITEMDATA[weight]</b> LBS<br /><br />
		Description: <blockquote><b>".stripslashes($ITEMDATA['description'])."</></blockquote><br />
		</div>
		<div id='img' style='display:inline;float:left;width:120px;height:120px'><img width='120' src='".SITEURL."/images/catalog" . $ITEMDATA['image'] . "' />
		<img vspace='5' width='120' src='".SITEURL."/images/catalog" . $ITEMDATA['small_image'] . "' />
		</div>
		<br clear='all' />

	";
?>
<form name="mconfirm" id="mconfirm" enctype="multipart/form-data" action="movebin.php" method="POST">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
    <input type="hidden" name="cmd" value="moveitem" />
    <input type="hidden" value="<?echo $ITEM['barcode']; ?>" name="barcode" id="barcode"/><br /><br />
    <input type="hidden" name="yard" value="<?=$ITEMDATA['yard'];?>" />
    <input type="hidden" name="zone" value="<?=$ITEMDATA['zone'];?>" />
    <input type="hidden" name="bin_loc" value="<?=$ITEMDATA['bin_loc'];?>" />
    <input type="hidden" name="weight" value="<?=$ITEMDATA['weight'];?>" />
    <input type="hidden" name="lot_no" value="<?=$ITEMDATA['lot_no'];?>" />
    <input type="hidden" name="pallet_no" value="<?=$ITEMDATA['pallet_no'];?>" />

    <br />

Enter New Pallet Location:<br />Yard: <? echo $ITEM['yard']; ?>
<!-- &nbsp;&nbsp;&nbsp;&nbsp;Yard: <select name="newyard" size="1">	<? //getYards($ITEM['yard']); ?></select> -->

<input type="hidden" name="newyard" value="<? echo $ITEM['yard']; ?>" ?>

&nbsp;Zone: <select name="newzone" size="1" id='zone'>
	<? getZones($ITEM['yard'],$ITEM['zone']); ?>
</select>

&nbsp;Slot: <select name="newbin_loc" size="1" id="pallet_loc">
	<?
	if($ITEM['bin_loc'] !=""){
		echo "<option value='$ITEM[bin_loc]' selected'>$ITEM[bin_loc]</option>";
	}
	 showSlots($ITEM['yard'],$ITEM['bin_loc']); ?>
</select>
<br /><br />
&nbsp;&nbsp;&nbsp; Extra Slot 1: <select name="newbin_loc2" size="1" id="pallet_loc2">
<option value="">(not used)</option>
	<?
	if($ITEM['bin_loc2'] !=""){
		echo "<option value='$ITEMDATA[bin_loc2]' selected'>$ITEMDATA[bin_loc2]</option>";
	}
	 showSlots($ITEM['yard'],"*"); ?>
</select>
&nbsp;Extra Slot 2: <select name="newbin_loc3" size="1" id="pallet_loc3">
<option value="">(not used)</option>
	<?
	if($ITEM['bin_loc3'] !=""){
		echo "<option value='$ITEMDATA[bin_loc3]' selected'>$ITEMDATA[bin_loc3]</option>";
	}
	 showSlots($ITEM['yard'],"*"); ?>
</select>

<br clear="all" /><br />
    <div name="nextbtn" id="nextbtn" style="width:60px"><img src="images/next-btn.jpg" /></div>

    <br /><br />
</form>
</div>
<?
}

//-----------------------------------------------------------------
function MoveItem()
{
	global $ossdb,$ITEM;

	$MITEM = getItem($ITEM['barcode'],0);	// get magento data
	
	$TransferYN = "N";
	
	if($MITEM['yard'] != $ITEM['newyard']){
		$Transfer = "Y";
	}
	
	//$Entity_ID = getEntityID($ITEM['barcode']);
	
	$MITEM['sku'] = $ITEM['barcode'];
	
	// Update bin_location_map
		$sqlcmd = "UPDATE bin_location_map SET status='avail',item_ref='-' WHERE yard='$MITEM[yard]' AND zone='$MITEM[zone]' AND bin_loc='$MITEM[bin_loc]'";
		if(!$rs = mysqli_query($ossdb, $sqlcmd)){
			exit("ERROR: " . mysqli_error($ossdb) . "<br />\n$sqlcmd");
		}
		
	if($MITEM['bin_loc2'] != ""){
		$sqlcmd = "UPDATE bin_location_map SET status='avail',item_ref='-' WHERE yard='$MITEM[yard]' AND zone='$MITEM[zone]' AND bin_loc='$MITEM[bin_loc2]'";
		if(!$rs = mysqli_query($ossdb, $sqlcmd)){
			exit("ERROR: " . mysqli_error($ossdb) . "<br />\n$sqlcmd");
		}
	}
	if($MITEM['bin_loc3'] != ""){
		$sqlcmd = "UPDATE bin_location_map SET status='avail',item_ref='-' WHERE yard='$MITEM[yard]' AND zone='$MITEM[zone]' AND bin_loc='$MITEM[bin_loc3]'";
		if(!$rs = mysqli_query($ossdb, $sqlcmd)){
			exit("ERROR: " . mysqli_error($ossdb) . "<br />\n$sqlcmd");
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
		//logItemTransaction("ITEM_MOVE_FROM", $MITEM);
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
		//logItemTransaction("ITEM_MOVE_TO", $MITEM);
		updateItemLocation($MITEM,$ITEM['barcode']);
	}
	
	//echo "<pre>" . print_r($MITEM,1) . "</pre>";
	//exit(0);
	
		
	if($TransferYN == "N" && $ITEM['newzone'] != "SHIPPED" && $ITEM['newzone'] != "PICKED_UP" && $ITEM['newzone'] != "TUMBLED"){
		$sqlcmd = "UPDATE bin_location_map SET status='full',item_ref='$ITEM[barcode]' WHERE yard='$ITEM[newyard]' AND zone='$ITEM[newzone]' AND bin_loc='$ITEM[newbin_loc]'";
		if(!$rs = mysqli_query($ossdb, $sqlcmd)){
			exit("ERROR: " . mysqli_error($ossdb) . "<br />\n$sqlcmd");
		}
		if($ITEM['newbin_loc2'] !=""){
			$sqlcmd = "UPDATE bin_location_map SET status='full',item_ref='$ITEM[barcode]' WHERE yard='$ITEM[newyard]' AND zone='$ITEM[newzone]' AND bin_loc='$ITEM[newbin_loc2]'";
			if(!$rs = mysqli_query($ossdb, $sqlcmd)){
				exit("ERROR: " . mysqli_error($ossdb) . "<br />\n$sqlcmd");
			}
		}
		if($ITEM['newbin_loc3'] !=""){
			$sqlcmd = "UPDATE bin_location_map SET status='full',item_ref='$ITEM[barcode]' WHERE yard='$ITEM[newyard]' AND zone='$ITEM[newzone]' AND bin_loc='$ITEM[newbin_loc3]'";
			if(!$rs = mysqli_query($ossdb, $sqlcmd)){
				exit("ERROR: " . mysqli_error($ossdb) . "<br />\n$sqlcmd");
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

?>