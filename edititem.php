<?php
//---------------------------------------------------------------------------------
// Edit Item
// created:13-NOV-2013 by Jeffrey L Meyer
// Modified: 26-JAN-2014 by Jeffrey L Meyer (To add CONSTANTS from wmsconfig.php)
//----------------------------------------------------------------------------------
require_once("wmsconfig.php");
ini_set('display_errors',0);

ob_start();
$msg = "";

require_once("header.php");
require_once("db_connect.php");
require_once("functions.php");

$ossdb = oss_connect();
$ossm = ossm_connect();

$cmd = isset($_POST['cmd']) ? $_POST['cmd'] : "";
$SKU = isset($_REQUEST['sku']) ? $_REQUEST['sku'] : "";

$PrimaryCatT = isset($_REQUEST['primary_cat']) ? urldecode($_REQUEST['primary_cat']):"";
$SubCat1T = isset($_REQUEST['subcat1']) ? urldecode($_REQUEST['subcat1']):"";
$SubCat2T = isset($_REQUEST['subcat2']) ? urldecode($_REQUEST['subcat2']):"";
$SubCat3T = isset($_REQUEST['subcat3']) ? urldecode($_REQUEST['subcat3']):"";
$SubCat4T = isset($_REQUEST['subcat4']) ? urldecode($_REQUEST['subcat4']):"";
$stone_type = isset($_REQUEST['stone_type']) ? $_REQUEST['stone_type'] : "";
$description = isset($_REQUEST['description']) ? $_REQUEST['description'] : "";

$CatChanged = isset($_REQUEST['catchanged']) ? $_REQUEST['catchanged'] : 0;

$PRICING = array();

$PRICING['costnfton'] = isset($_REQUEST['costnfton']) ? $_REQUEST['costnfton'] : "";
$PRICING['costfton'] = isset($_REQUEST['costfton']) ? $_REQUEST['costfton'] : "";
$PRICING['sellton'] = isset($_REQUEST['sellton']) ? $_REQUEST['sellton'] : "";
$PRICING['priceper'] = isset($_REQUEST['priceper']) ? $_REQUEST['priceper'] : "TON";
$PRICING['recid'] = isset($_REQUEST['recid']) ? $REQUEST['recid'] : 0;


$color = isset($_POST['color']) ? $_POST['color'] : "";

if(is_array($color)){
	foreach($color as $clr){
		$colors .= urldecode($clr) . ",";
	}
}else{
	$colors = $color;
}
$colors = rtrim($colors,",");

$OLDCATEGORY = isset($_REQUEST['category']) ? str_replace(" /-","+/-", $_REQUEST['category']) : "";
$OLDCATEGORY = str_replace('±',"+/-", $OLDCATEGORY);

$KEYWORDS = getKeywordList($PrimaryCatT);


$stone_type = str_replace('|'," ",$OLDCATEGORY); //$SubCat1T . " " . $SubCat2T . " " . $SubCat3T . " " . $SubCat4T;


if(strpos($description,"Uses") ===false){
	$description .= "\n** Uses: $KEYWORDS **";
}

$ITEM = array();
$ITEM['SKU'] = $SKU;
$ITEM['item_type'] = $stone_type;
$ITEM['description'] = $description;
$ITEM['lot_no'] = isset($_POST['lot_no']) ? $_POST['lot_no'] : "";
$ITEM['pal_no'] = isset($_POST['pal_no']) ? $_POST['pal_no'] : "";
$ITEM['width'] = isset($_POST['width']) ? $_POST['width'] : "";
$ITEM['height'] = isset($_POST['height']) ? $_POST['height'] : "";
$ITEM['length'] = isset($_POST['length']) ? $_POST['length'] : "";
$ITEM['weight'] = isset($_POST['weight']) ? $_POST['weight'] : "";
$ITEM['yard'] = isset($_POST['yard']) ? $_POST['yard'] : DEFAULTYARD;
$ITEM['zone'] = isset($_POST['zone']) ? $_POST['zone'] :"";
$ITEM['grade'] = isset($_POST['grade']) ? $_POST['grade'] : "A";
$ITEM['item_location'] = isset($_POST['item_location']) ? $_POST['item_location'] : "";
$ITEM['operator'] = isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "admin";
$ITEM['costf'] = isset($_POST['costf']) ? $_POST['costf'] :"";
$ITEM['costnf'] = isset($_POST['costnf']) ? $_POST['costnf'] :"";
$ITEM['sell'] = isset($_POST['sell']) ? $_POST['sell'] :"";
$ITEM['sqft'] = isset($_POST['sqft']) ? $_POST['sqft'] :"";
$ITEM['price_per_sqft'] = isset($_POST['price_per_sqft']) ? $_POST['price_per_sqft'] :"N/A";
$ITEM['sell'] = isset($_POST['sell']) ? $_POST['sell'] :"";
$ITEM['color'] = $colors;

if($ITEM['price_per_sqft'] == "N/A" && $ITEM['sell'] > 0){
	$sfp = number_format($ITEM['sell'] / $ITEM['sqft'],2);
}




if($cmd == "save"){

	// check for updated categories/subcategories
	if($SubCat1T == "none" || $SubCat1T =="(Select Subcategory - Level 1)"){ $SubCat1T = ""; }
	if($SubCat2T == "none" || $SubCat2T =="(Select Subcategory - Level 2)"){ $SubCat2T = ""; }
	if($SubCat3T == "none" || $SubCat3T =="(Select Subcategory - Level 3)"){ $SubCat3T = ""; }
	if($SubCat4T == "none" || $SubCat4T =="(Select Subcategory - Level 4)"){ $SubCat4T = ""; }
	
	$NEWCATEGORY = "$PrimaryCatT|$SubCat1T|$SubCat2T|$SubCat3T|$SubCat4T";
	if($NEWCATEGORY != $OLDCATEGORY && $CatChanged == 1){
		$ITEM['category'] = $NEWCATEGORY;
	}else{
		$ITEM['category'] = $OLDCATEGORY;
	}	

if(_USECAT == 1){
	
	list($PrimaryCatT,$SubCat1T,$SubCat2T,$SubCat3T,$SubCat4T) = explode("|",$ITEM['category']);
	
	$ITEM['item_type'] = $SubCat1T . " " . $SubCat2T . " " . $SubCat3T . " " . $SubCat4T;
	
	$KEYWORDS = getKeywordList($PrimaryCatT);
	if($ITEM['description'] == ""){
		$ITEM['description'] = $ITEM['item_type'] . "\n<br />Uses: $KEYWORDS";
	}
}

if(strpos($ITEM['description'],"Uses:")===false){
	$ITEM['description'] .= "\n<br />Uses: $KEYWORDS";
}

	saveItem($SKU,$ITEM);
	savePricing($ITEM,$PRICING);
	@mysqli_close($ossdb);

	echo "<script>window.location.href='manager.php#" . $SKU . "';</script>";
	ob_end_flush();
	exit(0);
}else{
//pull data and send form
$sqlcmd = "SELECT * FROM item_data WHERE SKU='$SKU'";



if(!$rs = mysqli_query($ossdb, $sqlcmd)){
	exit("ERROR: " . mysqli_error($ossdb));
}
$rows = mysqli_num_rows($rs);
if($rows > 0){
	$ITEM = mysqli_fetch_assoc($rs);
	mysqli_free_result($rs);
}
sendForm($msg, $ITEM, $PRICING);		

}


@mysqli_close($ossdb);
ob_end_flush();

//--------------------------------------------------------------
function sendForm($msg,$ITEM,$PRICING)
{
	global $ossdb;
	mysqli_query("USE YOUR_DATABASE_NAME");
	
	list($PrimaryCatT,$SubCat1T,$SubCat2T,$SubCat3T,$SubCat4T) = explode("|",stripslashes($ITEM['category']));
	
		
	if(is_string($PrimaryCatT)){
		$PrimaryCat = getPrimaryIDX($PrimaryCatT);
	}
	if(is_string($SubCat1T)){
		$SubCat1 = getSubcat1IDX($PrimaryCat, $SubCat1T);
	}
	if(is_string($SubCat2T)){
		$SubCat2 = getSubcat2IDX($PrimaryCat, $SubCat1, $SubCat2T);
	}
	if(is_string($SubCat3T)){
		$SubCat3 = getSubcat3IDX($PrimaryCat, $SubCat1, $SubCat2, $SubCat3T);
	}
	if(is_string($SubCat4T)){
		$SubCat4 = getSubcat4IDX($PrimaryCat, $SubCat1, $SubCat2, $SubCat3, $SubCat4T);
	}

	if($PrimaryCat == 0){
		$PrimaryCat = isset($_COOKIE['last_primary']) ? $_COOKIE['last_primary'] : 1;
	}

	if($SubCat1 == 0){
		$SubCat1 = isset($_COOKIE['last_subcat']) ? $_COOKIE['last_subcat'] : 1;
	}
	if($SubCat2 == 0){
		$SubCat2 = isset($_COOKIE['last_subcat2']) ? $_COOKIE['last_subcat2'] : 1;
	}
	if($SubCat3 == 0){
		$SubCat3 = isset($_COOKIE['last_subcat3']) ? $_COOKIE['last_subcat3'] : 1;
	}
	if($SubCat4 == 0){
		$SubCat4 = isset($_COOKIE['last_subcat4']) ? $_COOKIE['last_subcat4'] : 1;
	}
	
	if(_USECAT == 1){

		$ITEM['item_type'] = $SubCat1T . " " . $SubCat2T . " " . $SubCat3T . " " . $SubCat4T;

	if($ITEM['description'] == ""){
		$ITEM['description'] = $ITEM['item_type'];
	}
	}
	$KEYWORDS = getKeywordList($PrimaryCatT);
	if(strpos($ITEM['description'],"Uses:") ===false){
		$ITEM['description'] .= "\n<br>Uses: $KEYWORDS";
	}


if(!isset($PRICING['sellton']) || $PRICING['sellton'] <=0){

	$sqlcmd = "SELECT * FROM pricing WHERE lot_no='" . trim($ITEM['lot_no']) . "' AND category='" . trim($ITEM['category']) . "'";
	if(!$rs = mysqli_query($ossdb,$sqlcmd)){
		echo "ERROR: " . mysqli_error($ossdb) ."<br />$sqlcmd";
	}
	$rows = mysqli_num_rows($rs);
	if($rows >0){
		$rd = mysqli_fetch_assoc($rs);
		$PRICING['recid'] = $rd['recid'];
		$PRICING['costnfton'] = $rd['costnf'];
		$PRICING['costfton'] = $rd['costf'];
		$PRICING['sellton'] = $rd['sell'];
		$PRICING['priceper'] = $rd['priceper'];
		mysqli_free_result($rs);
	}else{
	echo "No rows selected for query: $sqlcmd<br />";
	}
}
?>

<html><head><title>YOUR STONE CO. - WMS V2.2.0 - Edit item</title>

<style type="text/css">
body {
	background:#F0F0F0;
}
</style>
<meta name="http-pragma" content="no-cache" />


<script type="text/javascript"> 

function stopRKey(evt) { 
  var evt = (evt) ? evt : ((event) ? event : null); 
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;} 
} 

document.onkeypress = stopRKey; 



			var PrimaryCatPRE = "<? echo addslashes($PrimaryCatT); ?>";
			var SubCat1PRE = "<? echo addslashes($SubCat1T); ?>";
			var SubCat2PRE = "<? echo addslashes($SubCat2T); ?>";
			var SubCat3PRE = "<? echo addslashes($SubCat3T); ?>";
			var SubCat4PRE = "<? echo addslashes($SubCat4T); ?>";
			var catchanged = 0;

</script>
<script src="js/listitems.js"></script>

<? require_once("header.php");
?>
</head>
<body>
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
#cattext {
	
	float:left;
	font-size:10pt;
	height:30px;
}
#catbtn {
	width:80px;
	float:left;
	heightL30px;
	
}
#colors {
	height:50px;
}
</style>
<div class="wmsmain">
<? if($msg != ""){echo $msg; }?>
<b>EDIT ITEM</b>
<br />
<br />

<form method="post" action="edititem.php" name="editfrm" id="editfrm" enctype="multipart/form-data" accept-charset="utf-8">
<input type="hidden" name="cmd" value="save" />
<input type="hidden" name="category" value="<? echo stripslashes($ITEM['category']); ?>" />
<input type="hidden" name="catchanged" id="catchanged" value="0" />

<!-- categories here -->
<div id="catmain">
	<div id="cattext">Current Category/Subcategories:<br />
	 <b><font color="red"><? echo stripslashes($ITEM['category']); ?></font></b></div>
	<div id="catbtn"><img src="images/catbtn.jpg" /></div>

</div>
<br clear="all" />
<div id="catpanel">


	<div id="primarycats">
	<font class="lgt">Primary Category</font>
	<br clear="all" />
		<select name="primary_cat" id="primary_cat" size="1">
		
		<?
			if($PrimaryCat == 0){
				$nsel = "selected";
			}else{
				$nsel = "";
			}
		?>
		<option value="none" <? echo $nsel; ?>>(Select Primary Category)</option>
		<?
		foreach(getPrimary() AS $PRI){
			if($PRI !=""){
				if(getPrimaryIDX($PRI) == $PrimaryCat){
					echo "<option selected value='" . urlencode($PRI) . "'>" .  stripslashes($PRI) . "</option>\n";
				}else{
					echo "<option value='" . urlencode($PRI) . "'>" .  stripslashes($PRI) . "</option>\n";
				}
			}
		}
		
		?>
		
		</select>
		
	</div>
	
	<div id="subcats_l1">
	<font class="lgt">Subcategory 1</font>
	<br clear="all" />
		<select name="subcat1" id="subcat1" size="1">
		<?
		if($SubCat1 == 0){
				$nsel = "selected";
			}else{
				$nsel = "";
			}
		?>
		<option <? echo $nsel; ?> value="none">(Select Subcategory Level 1)</option>
		<?
		foreach(getSubcat1($PrimaryCat) AS $SUB1){
			if($SUB1 !=""){
				if(getSubcat1IDX($PrimaryCat,$SUB1) == $SubCat1){
					echo "<option selected value='" . urlencode($SUB1) . "'>" .  stripslashes($SUB1) . "</option>\n";
				}else{
					echo "<option value='" . urlencode($SUB1) . "'>" .  stripslashes($SUB1) . "</option>\n";
				}
			}
		}
		
		?>
		</select>
		
		
	</div>
	
	<div id="subcats_l2">
	<font class="lgt">Subcategory 2</font>
	<br clear="all" />
	<select name="subcat2" id="subcat2" size="1">
		<?
		if($SubCat2 == 0){
				$nsel = "selected";
			}else{
				$nsel = "";
			}
			?>
			<option <? echo $nsel; ?> value="none">(Select Subcategory Level 2)</option>
		<?
		foreach(getSubcat2($PrimaryCat,$SubCat1) AS $SUB2){
			if($SUB2 !=""){
				if(getSubcat2IDX($PrimaryCat,$SubCat1,$SUB2) == $SubCat2){
					echo "<option selected value='" . urlencode($SUB2) . "'>" .  stripslashes($SUB2) . "</option>\n";
				}else{
					echo "<option value='" . urlencode($SUB2) . "'>" .  stripslashes($SUB2) . "</option>\n";
				}
			}
		}
		
		?>
		</select>
	</div>

	<div id="subcats_l3">
	<font class="lgt">Subcategory 3</font>
	<br clear="all" />
	<select name="subcat3" id="subcat3" size="1">
	<?
		if($SubCat3 == 0){
				$nsel = "selected";
			}else{
				$nsel = "";
			}
			?>
		<option <? echo $nsel; ?> value="none">(Select Subcategory Level 3)</option>
		<?
		foreach(getSubcat3($PrimaryCat,$SubCat1,$SubCat2) AS $SUB3){
			if($SUB3 !=""){
				if(getSubcat3IDX($PrimaryCat,$SubCat1,$SubCat2,$SUB3)==$SubCat3){
					echo "<option selected value='" . urlencode($SUB3) . "'>" .  stripslashes($SUB3) . "</option>\n";
				}else{
					echo "<option value='" . urlencode($SUB3) . "'>" .  stripslashes($SUB3) . "</option>\n";
				}
			}
		}
		
		?>
		</select>
	</div>
	
	<div id="subcats_l4">
	<font class="lgt">Subcategory 4</font>
	<br clear="all" />
	<select name="subcat4" id="subcat4" size="1">
	<?
		if($SubCat4 == 0){
				$nsel = "selected";
			}else{
				$nsel = "";
			}
			?>
		<option <? echo $nsel; ?> value="none">(Select Subcategory Level 4)</option>
		<?
		foreach(getSubcat4($PrimaryCat,$SubCat1,$SubCat2,$SubCat3) AS $SUB4){
			if($SUB4 !=""){
				if(getSubcat4IDX($PrimaryCat,$SubCat1,$SubCat2,$SubCat3,$SUB4)==$SubCat4){
					echo "<option selected value='" . urlencode($SUB4) . "'>" .  stripslashes($SUB4) . "</option>\n";
				}else{
					echo "<option value='" . urlencode($SUB4) . "'>" .  stripslashes($SUB4) . "</option>\n";
				}
			}
		}
		
		?>
		</select>
	</div>
	<div style="margin-right:30px"><a href="edititem.php?sku=<? echo $ITEM['SKU']; ?>"><img src="images/cancel.jpg" align="right" /></a></div>
</div> <!-- /catpanel -->


<br clear="all" />


<?

	echo "<br />Enter Stone Type (Short name - e.g. PA Blue Sawed - 128 chars max):<br /> 
	<input class=\"ti\" type=\"text\" name=\"stone_type\" value=\"$ITEM[item_type]\" size=\"60\" maxlength=\"128\" /><br />

	Enter Description of stone:<br />
	<textarea name=\"description\" rows=\"6\" cols=\"60\">$ITEM[description]</textarea>
<br />
<br />
If stone is pattern cut, enter total Sq.Ft. on pallet: <input type=\"text \" name=\"sqft\" id='sqft' value=\"$ITEM[sqft]\" size=\"8\" maxlength=\"10\" />
<input type='hidden' name='price_per_sqft' value='$ITEM[price_per_sqft]' />
<br /><br />
";

?>


Color:
	<? getColorList($ITEM['color']); ?>

<br clear="all" />

<br />
Enter Lot #:&nbsp;<input class="ti" type="text" name="lot_no" value="<?echo $ITEM['lot_no'];?>" size="10" maxlength="7" />&nbsp;Enter Pallet # <input class="ti" type="text" name="pal_no" value="<?echo $ITEM['pal_no'];?>" size="10" maxlength="10" /><small><i>(from P.O. or Tag)</i></small><br />
<br />

Enter Pallet Dimensions (Inches):<br />
<b>W</b>: <input class="ti" type="text" name="width" value="<?echo $ITEM['width'];?>" size="10" maxlength="10" />&nbsp;<b>L</b>: 
<input class="ti" type="text" name="length" value="<?echo $ITEM['length'];?>" size="10" maxlength="10" />
<b>H</b>: <input class="ti" type="text" name="height" value="<?echo $ITEM['height'];?>" size="10" maxlength="10" /><br />

<br />
Enter Pallet Weight (Pounds): <input class="ti" type="text" name="weight" id="weight" value="<?echo $ITEM['weight'];?>" size="10" maxlength="10" /><br />

<br />
Pallet Location: (to move, use Pick/Move)<br />

&nbsp;&nbsp;&nbsp;&nbsp;Yard: <?=$ITEM['yard']; ?>&nbsp;Zone: <?=$ITEM['zone']; ?> Slot: <? echo $ITEM['item_location']; ?>

<input type="hidden" name="yard" value="<?echo $ITEM['yard']; ?>" />
<input type="hidden" name="zone" value="<?echo $ITEM['zone']; ?>" />
<input type="hidden" name="item_location" value="<?echo $ITEM['item_location']; ?>" />
<br /><br />
Select Product Grade (Quality): <select name="grade" size="1">
	<option value="A" <? if($ITEM['grade']=="A"){ echo "selected";}?>>A</option>
	<option value="B" <? if($ITEM['grade']=="B"){ echo "selected";}?>>B</option>
	<option value="C" <? if($ITEM['grade']=="C"){ echo "selected";}?>>C</option>
	</select>
	<br /><br />
	
Product ID: <? echo "<b>$ITEM[SKU]</b>"; ?><br /><br />

<div style="width:98%;height:110px;border:1px solid #000080">
<div style="width:500px;display:inline;float:left">
Enter "Per Pallet" Cost Without Freight: <input type="text" name="costnf" id="costnf" value="<? echo $ITEM['costnf']; ?>" size="10" maxlength="10" /> (3 decimals)<br />
Enter "Per Pallet" Cost With Freight:  <input type="text" name="costf" id="costf" value="<? echo $ITEM['costf']; ?>" size="10" maxlength="10" /> ( 3 decimals)<br />
Enter "per Pallet" Selling Price:  <input type="text" name="sell" id="sell" value="<? echo $ITEM['sell']; ?>" size="10" maxlength="10" /> (2 decimals, rounded)<br />
</div>
<div style="width:300px;border:1px solid #008000;display:inline;float:left">
	Cost NF: <input type="text" name="costnfton" id="costnfton" size="10" value="<? echo $PRICING['costnfton']; ?>" /><br />
	Cost w/F: <input type="text" name="costfton" id="costfton" size="10" value="<? echo $PRICING['costfton']; ?>" />&nbsp;&nbsp;
	<select name="priceper" id="priceper" size="1">
	<option value="TON" <? if($PRICING['priceper']=="TON"){echo " selected=\"selected\"";}?>>TON</option>
	<option value="SQFT" <? if($PRICING['priceper']=="SQFT"){echo " selected=\"selected\"";}?>>SQFT</option>
	</select><br />
	Sell price: <input type="text" name="sellton" id="sellton" size="10" value="<? echo $PRICING['sellton']; ?>" /><br />
	
	<input type="button" name="calcbtn" id="calcbtn" value="Calc & Insert" />
</div>
</div>
<input type="hidden" name="recid" value="<?=$PRICING['recid'];?>" />

<input type="hidden" name="sku" id="sku" value="<?echo $ITEM['SKU'];?>" />
<br clear="all" /><br />
<input type="hidden" name="cmd" value="save" />
<input type="button" name="savebtn1" id="savebtn1" value="SAVE ITEM" />
</form>

</div>

<script type="text/javascript">
	$(function(){
		$("#calcbtn").click(function(){
		 	var nfcost = $("#costnfton").val();
		 	var fcost = $("#costfton").val();
		 	var sellp = $("#sellton").val();
		 	var sper = $("#priceper").val();
		 	var ssqft = "";
		 	var weight = $("input#weight").val();
		 	var wtons = (weight/2000).toFixed(3);
		 	
		 	var costnfT = "";
		 	var costfT = "";
		 	var sellT = "";
			if(sper == 'SQFT'){
				ssqft = $("input#sqft").val();
				costnfT =(ssqft*nfcost).toFixed(2);
				$("input#costnf").val(costnfT);
				costfT = (ssqft*fcost).toFixed(2);
				$("input#costf").val(costfT);
				sellT = (ssqft*sellp).toFixed(2);
				$("input#sell").val(sellT);
			}else{
				costnfT = (wtons*nfcost).toFixed(2);
				$("input#costnf").val(costnfT);
				costfT = (wtons*fcost).toFixed(2);
				$("input#costf").val(costfT);
				sellT = (wtons*sellp).toFixed(2);
				$("input#sell").val(sellT);
				//alert(wtons);
			
			}	 	

		});
	});
</script>

<?
} // sendForm()

@mysqli_close($ossdb);
@mysqli_close($ossm);

//-------------------------------------------------------
function savePricing($ITEMS,$PRICINGS)
{
	global $ossdb;
	
	if($PRICINGS['recid'] != ""){
		$sqlcmd = "REPLACE INTO pricing SET recid='$PRICINGS[recid]',lot_no='$ITEMS[lot_no]',category='$ITEMS[category]',costnf='$PRICINGS[costnfton]',costf='$PRICINGS[costfton]',sell='$PRICINGS[sellton]',priceper='$PRICINGS[priceper]'";
	}else{
		$sqlcmd = "INSERT INTO pricing SET lot_no='$ITEMS[lot_no]',category='$ITEMS[category]',costnf='$PRICINGS[costnfton]',costf='$PRICINGS[costfton]',sell='$PRICINGS[sellton]',priceper='$PRICINGS[priceper]'";
	}
	mysqli_query($ossdb,$sqlcmd);
}

?>
