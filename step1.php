<?php
//------------------------------------------------------------------
// WMS Version 2.0
// created: Dec, 2013 by Jeffrey L Meyer, IT Director
//
//------------------------------------------------------------------
require_once("wmsconfig.php");

$stone_type = isset($_POST['stone_type']) ? strtoupper($_POST['stone_type']) : "";
$description = isset($_POST['description']) ? ucfirst($_POST['description']) : "";
$description2 = isset($_POST['description2']) ? ucfirst($_POST['description2']) : "";

$category = isset($_POST['category']) ? $_POST['category'] : "";
$color = isset($_POST['color']) ? $_POST['color'] : "";
$colors="";
if(is_array($color)){
	foreach($color as $clr){
		$colors .= urldecode($clr) . ",";
	}
}else{
	$colors = $color;
}
$colors = rtrim($colors,",");

$PrimaryCatT = isset($_REQUEST['primary_cat']) ? urldecode($_REQUEST['primary_cat']) :"";
$SubCat1T = isset($_REQUEST['subcat1']) ? urldecode($_REQUEST['subcat1']):"";
$SubCat2T = isset($_REQUEST['subcat2']) ? urldecode($_REQUEST['subcat2']):"";
$SubCat3T = isset($_REQUEST['subcat3']) ? urldecode($_REQUEST['subcat3']):"";
$SubCat4T = isset($_REQUEST['subcat4']) ? urldecode($_REQUEST['subcat4']):"";

$sqft = isset($_REQUEST['sqft']) ? urldecode($_REQUEST['sqft']):"";



$lot_no = isset($_POST['lot_no']) ? strtoupper($_POST['lot_no']) : "";
$pal_no = isset($_POST['pal_no']) ? $_POST['pal_no'] : "";

if(_USECAT == 1){
if($stone_type == ""){
	$stone_type = $SubCat1T . " " . $SubCat2T . " " . $SubCat3T . " " . $SubCat4T;
}
if($description == ""){
	$description = $stone_type . " - \n";
}
}

$description .= $description2 . "\n";

$width = isset($_POST['width']) ? $_POST['width'] : "";
$height = isset($_POST['height']) ? $_POST['height'] : "";
$length = isset($_POST['length']) ? $_POST['length'] : "";
$weight = isset($_POST['weight']) ? $_POST['weight'] : "";
$yard = isset($_POST['yard']) ? $_POST['yard'] : DEFAULTYARD;

//$SKU = isset($_POST['SKU']) ? $_POST['SKU'] : "";

$grade = isset($_POST['grade']) ? $_POST['grade'] : "A";
$pallet_loc = isset($_POST['pallet_loc']) ? $_POST['pallet_loc'] : "";
$pallet_loc2 = isset($_POST['pallet_loc2']) ? $_POST['pallet_loc2'] : "";
$pallet_loc3 = isset($_POST['pallet_loc3']) ? $_POST['pallet_loc3'] : "";
$pallet_locT = isset($_POST['pallet_locT']) ? strtoupper($_POST['pallet_locT']) : "";
$pallet_loc2T = isset($_POST['pallet_loc2T']) ? strtoupper($_POST['pallet_loc2T']) : "";
$pallet_loc3T = isset($_POST['pallet_loc3T']) ? strtoupper($_POST['pallet_loc3T']) : "";



$zone = isset($_POST['zone']) ? $_POST['zone'] : "";

$operator = isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "admin";
$c = isset($_POST['c']) ? $_POST['c'] : "";

require_once("functions.php");
require_once("gencodes.php");

require_once("db_connect.php");
$ossdb = oss_connect();
$ossm = ossm_connect();

$keywordsC = isset($_POST['keywords']) ? urldecode($_POST['keywords']) : "";
$XkeywordsC = isset($_POST['xkeywords']) ? urldecode($_POST['xkeywords']) : "";

// pull keywords from category.projects
if($keywordsC != ""){
	$keywords = getKeywordList(mysqli_real_escape_string($ossm,$keywordsC));
}else{
	$keywords = getKeywordList(mysqli_real_escape_string($ossm,$PrimaryCatT));
}
$keywords .= "," . $XkeywordsC;


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

//exit($PrimaryCatT . "<br>" .$keywords . "<br><br>$keywordsC");

$SKU = isset($_POST['SKU']) ? $_POST['SKU'] : getcode();
$msg = "";

if($c == ""){
	sendForm("");
	exit(0);
}
	
if($lot_no == ""
	|| $pal_no == ""
	|| $width == ""
	|| $length == ""
	|| $height == ""
	|| $weight == ""
	|| $SKU == ""
	|| ($pallet_loc == "" && $pallet_locT=="")
	){
		sendForm("<h3 style=\"color:#FF4040\">ERROR: ALL Fields MUST Be Filled Out</h3>");
		exit(0);
	}

if($keywords == ""){
	$msg = "<h3 style=\"color:#FF4040\">No project types selected...</h3>";
	$c = "";
	sendForm($msg);
}

if($pallet_locT != ""){
if(confirmPalletLocT($pallet_locT) == "" ){
	$msg = "<h3 style=\"color:#FF4040\">ERROR: Invalid slot entered [ $pallet_locT ] ...</h3>";
	sendForm($msg);
	$c = "";
}elseif(confirmPalletLocT($pallet_locT) == "full"){
	$msg = "<h3 style=\"color:#FF4040\">ERROR: Slot [ $pallet_locT ] is showing as already occupied...check location.</h3>";
	sendForm($msg);
	$c = "";
}elseif(confirmPalletLocT($pallet_locT) == "avail"){
	$pallet_loc = $pallet_locT;
}
}

$zoneT = substr($zone,0,1);
$locT = substr($pallet_locT,0,1);

if($zoneT != $locT){
	if($locT == "A"){
		$zone = "A Section";
	}elseif($locT == "B"){
		$zone = "B Section";
	}elseif($locT == "C"){
		$zone = "C Section";
	}elseif($locT=="D"){
		$zone="D Section";
	}elseif($locT == "N"){
		$zone = "OutgoingN";
	}elseif($locT == "S"){
		$zone = "OutgoingS";
	}elseif($locT == "E"){
		$zone = "OutgoingE";
	}elseif($locT == "W"){
		$zone = "OutgoingW";
	}
}

if($pallet_loc2T != ""){
if(confirmPalletLocT($pallet_loc2T) == "" ){
	$msg = "<h3 style=\"color:#FF4040\">ERROR: Invalid slot entered [ $pallet_loc2T ] ...</h3>";
	sendForm($msg);
	$c = "";
}elseif(confirmPalletLocT($pallet_loc2T) == "full"){
	$msg = "<h3 style=\"color:#FF4040\">ERROR: Slot [ $pallet_loc2T ] is showing as already occupied...check location.</h3>";
	sendForm($msg);
	$c = "";
}elseif(confirmPalletLocT($pallet_loc2T) == "avail"){
	$pallet_loc2 = $pallet_loc2T;
}
}

$zoneT = substr($zone,0,1);
$loc2T = substr($pallet_loc2T,0,1);

if($zoneT != $loc2T){
	if($loc2T == "A"){
		$zone = "A Section";
	}elseif($loc2T == "B"){
		$zone = "B Section";
	}elseif($loc2T == "C"){
		$zone = "C Section";
	}elseif($loc2T=="D"){
		$zone="D Section";
	}elseif($loc2T == "N"){
		$zone = "OutgoingN";
	}elseif($loc2T == "S"){
		$zone = "OutgoingS";
	}elseif($loc2T == "E"){
		$zone = "OutgoingE";
	}elseif($loc2T == "W"){
		$zone = "OutgoingW";
	}
}

if($pallet_loc3T != ""){
if(confirmPalletLocT($pallet_loc3T) == "" ){
	$msg = "<h3 style=\"color:#FF4040\">ERROR:Invalid slot entered [ $pallet_loc3T ] ...</h3>";
	sendForm($msg);
	$c = "";
}elseif(confirmPalletLocT($pallet_loc3T) == "full"){
	$msg = "<h3 style=\"color:#FF4040\">ERROR:Slot [ $pallet_loc3T ] is showing as already occupied...check location.</h3>";
	sendForm($msg);
	$c = "";
}elseif(confirmPalletLocT($pallet_loc3T) == "avail"){
	$pallet_loc3 = $pallet_loc3T;
}
}

$zoneT = substr($zone,0,1);
$loc3T = substr($pallet_loc3T,0,1);

if($zoneT != $loc3T){
	if($loc3T == "A"){
		$zone = "A Section";
	}elseif($loc3T == "B"){
		$zone = "B Section";
	}elseif($loc3T == "C"){
		$zone = "C Section";
	}elseif($loc3T=="D"){
		$zone="D Section";
	}elseif($loc3T == "N"){
		$zone = "OutgoingN";
	}elseif($loc3T == "S"){
		$zone = "OutgoingS";
	}elseif($loc3T == "E"){
		$zone = "OutgoingE";
	}elseif($loc3T == "W"){
		$zone = "OutgoingW";
	}
}
if($c != ""){
	//foreach($_REQUEST as $key=>$val){
	//	echo "$key = " . urldecode($val) . "\n<br>";
	//}
//	echo print_r($_REQUEST,1);
//	exit(0);
	processData();
	$category = "$PrimaryCatT|$SubCat1T|$SubCat2T|$SubCat3T|$SubCat4T";
	
	?>
	<script type="text/javascript">
		var url = 'step2.php?text=<? echo $stone_type; ?>&weight=<? echo $weight; ?>&yard=<? echo $yard; ?>&lot_no=<? echo $lot_no; ?>&pal_no=<? echo $pal_no; ?>&barcode=<? echo $SKU; ?>&category=<? echo $category; ?>';

		window.location.href=url;
	</script>
	
	<?
}
	@mysqli_close($ossdb);
	@mysqli_close($ossm);
?>




<?
function sendForm($msg)
{
	global $stone_type,$colors,$lot_no,$width,$length,$height,$weight,$pallet_loc,$pallet_loc2,$pallet_loc3,$pallet_locT,$pallet_loc2T,$pallet_loc3T,$SKU,$description,$yard,$zone,$pal_no,$ossdb,$ossm;
	global $PrimaryCat,$SubCat1,$SubCat2,$SubCat3,$SubCat4,$description2,$sqft;
		global $PrimaryCatT,$SubCat1T,$SubCat2T,$SubCat3T,$SubCat4T;

	if($msg != ""){
		$msg = "<h4>NOTE: On error you may have to reselect categories and colors.</h4>" . $msg;
	}
	// Pull list of categories and subcategories from Magento

?>
<html><head><title>YOUR STONE CO - WMS V2</title>

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


			var PrimaryCatPRE = "<? echo addslashes(urldecode($PrimaryCatT)); ?>";
			var SubCat1PRE = "<? echo addslashes(urldecode($SubCat1T)); ?>";
			var SubCat2PRE = "<? echo addslashes(urldecode($SubCat2T)); ?>";
			var SubCat3PRE = "<? echo addslashes(urldecode($SubCat3T)); ?>";
			var SubCat4PRE = "<? echo addslashes(urldecode($SubCat4T)); ?>";

</script>
<? require_once("header.php");
?>
<script src="js/slots.js"></script>
<script src="js/listitems.js"></script>
<script src="js/formcheck.js"></script>

</head>
<body>
<style type="text/css">
h4 {
	font-size:11pt;
	font-weight:bold;
}
.b1 {
	width:40px;
	float:right;
	top:10px;
	position:relative;
	display:inline;
}
.wmsmain {
	background:#F0F0F0;
	font-family:Verdana,arial,helvetica,tahoma;
	font-size:10pt;
	color:#000000;
	padding-left:5px;
	float:left;
	width:95%;
}
#colors {
	height:50px;
}
.cbs {
	width:20px;
	height:20px;
	padding:3px;
}
</style>

<div class="wmsmain">
<? if($msg != ""){echo $msg; }?>
<b>START - Step 1 of 2</b>  (<i>ALL Fields MUST be filled out</i>) -- New Product ID: <? echo "<b>$SKU</b>"; ?><br />
<br />

<form method="post" action="step1.php" name="step1" id="step1" enctype="multipart/form-data">
<input type="hidden" name="c" value="save" />
<!-- categories here -->
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
					echo "<option selected value=\"" . urlencode($PRI) . "\">" .  stripslashes($PRI) . "</option>\n";
				}else{
					echo "<option value=\"" . urlencode($PRI) . "\">" .  stripslashes($PRI) . "</option>\n";
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
					echo "<option selected value=\"" . urlencode($SUB1) . "\">" .  stripslashes($SUB1) . "</option>\n";
				}else{
					echo "<option value=\"" . urlencode($SUB1) . "\">" .  stripslashes($SUB1) . "</option>\n";
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
					echo "<option selected value=\"" . urlencode($SUB2) . "\">" .  stripslashes($SUB2) . "</option>\n";
				}else{
					echo "<option value=\"" . urlencode($SUB2) . "\">" .  stripslashes($SUB2) . "</option>\n";
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
					echo "<option selected value=\"" . urlencode($SUB3) . "\">" .  stripslashes($SUB3) . "</option>\n";
				}else{
					echo "<option value=\"" . urlencode($SUB3) . "\">" .  stripslashes($SUB3) . "</option>\n";
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
					echo "<option selected value=\"" . urlencode($SUB4) . "\">" .  stripslashes($SUB4) . "</option>\n";
				}else{
					echo "<option value=\"" . urlencode($SUB4) . "\">" .  stripslashes($SUB4) . "</option>\n";
				}
			}
		}
		
		?>
		</select>
	</div>
</div>


<br clear="all" />
Color: <!-- <br /> <select id="colors" name="color" multiple size="5">
	<option value="">(Select Color)</option>-->
	<? getColorList(); ?>
<!-- </select>
-->
<br clear="all" />

<?
if(_USECAT==0){
	echo "<br />Enter Stone Type (Short name - e.g. PA Blue Sawed - 128 chars max):<br /> 
	<input class=\"ti\" type=\"text\" name=\"stone_type\" value=\"$stone_type\" size=\"60\" maxlength=\"128\" /><br />

	Enter Description of stone:<br />
	<textarea name=\"description\" rows=\"6\" cols=\"60\">$description</textarea>
<br />
	";
}else{
	echo "<input class=\"ti\" type=\"hidden\" name=\"stone_type\" value=\"$stone_type\" size=\"50\" maxlength=\"50\" />
	<input type=\"hidden\" name=\"description\" value=\"$description\" />
<br />
	Enter Dimensional Info of stone (if any):<br />
	<textarea name=\"description2\" rows=\"6\" cols=\"60\">$description2</textarea>
<br />
If stone is pattern cut, enter total Sq.Ft. on pallet: <input type=\"text \" name=\"sqft\" value=\"$sqft\" size=\"8\" maxlength=\"10\" />
<br /><br />
";
}
?>

Select Project Types: <br /><select name="keywords" size="1">
<option value="">(Select Project Types Used For)</option>
<?
	echo getKeywordSelectList(); 
?>
</select>
<br clear="all" />
<br />
Additional Project types: <input class="ti" type="text" name="xkeywords" size="60" maxlength="256" /> <small>(comma separated keyword/keyphrases)</small>
<br /><br />
Enter Lot #:&nbsp;<input class="ti" type="text" name="lot_no" value="<?echo $lot_no;?>" size="12" maxlength="7" /> (Max. 7 Chars)
&nbsp;Enter Pallet #:&nbsp;<input class="ti" type="text" name="pal_no" value="<? echo $pal_no;?>" size="10" maxlength="10" /> <small><i>(from P.O. or Tag)</i></small>
<br />
<br />
Enter Pallet Dimensions (Inches):<br />
<b>W</b>: <input class="ti" type="text" name="width" value="<?echo $width;?>" size="10" maxlength="10" />
&nbsp;<b>L</b>: <input class="ti" type="text" name="length" value="<?echo $length;?>" size="10" maxlength="10" />
<b>H</b>: <input class="ti" type="text" name="height" value="<?echo $height;?>" size="10" maxlength="10" />&nbsp;&nbsp;Weight (Pounds): <input class="ti"  type="text" name="weight" value="<?echo $weight;?>" size="10" maxlength="10" /><br />

<br />
Enter Pallet Location:<br />
&nbsp;&nbsp;&nbsp;&nbsp;Yard: <select name="yard" size="1">
	<? getYards($yard); ?>
</select>

&nbsp;Zone: <select name="zone" size="1" id="zone">
	<? getZones($yard,$zone); ?>
</select>

&nbsp;Slot: <select name="pallet_loc" size="1" id="pallet_loc">
	<?
	if($pallet_loc !=""){
		echo "<option value='$pallet_loc' selected'>$pallet_loc</option>";
	}
	 showSlots($yard,$pallet_loc); ?>
</select>

<input type="text" name="pallet_locT" size="6" maxlength="10" value="<? echo $pallet_locT; ?>" onchange="javascript:step1.pallet_loc.disabled=true;" /> 

<br /><br />&nbsp;&nbsp;
&nbsp;Extra Slot 2: <select name="pallet_loc2" size="1" id="pallet_loc2">
	<option value="">(not used)</option>
	<?
	if($pallet_loc2 !=""){
		echo "<option value='$pallet_loc2' selected'>$pallet_loc2</option>";
		
	}
		showSlots($yard,$pallet_loc);
		 
	?>
</select>

<input type="text" name="pallet_loc2T" size="6" maxlength="10" value="<? echo $pallet_loc2T; ?>" onchange="javascript:step1.pallet_loc2.disabled=true;" /> 

&nbsp;Extra Slot 3: <select name="pallet_loc3" size="1" id="pallet_loc3">
	<option value="">(not used)</option>
	<?
	if($pallet_loc3 !=""){
		echo "<option value='$pallet_loc3' selected'>$pallet_loc3</option>";
	}
	 showSlots($yard,$pallet_loc); ?>
</select>

<input type="text" name="pallet_loc3T" size="6" maxlength="10" value="<? echo $pallet_loc3T; ?>" onchange="javascript:step1.pallet_loc3.disabled=true;" /> 

<br /><br />
Select Product Grade (Quality): <select name="grade" size="1">
	<option value="A" selected>A (default)</option>
	<option value="B">B</option>
	<option value="C">C</option>
	</select>
	<br />
	

<input type="hidden" id="barcode" name="barcode" value="<?echo $SKU;?>" />
<br />


<input type="image" value="NEXT" id="step1nextbtn" name="step1nextbtn"  src="images/next-btn.jpg" />
</form>
</div>
<br /><br />
<script type="text/javascript">
	$(function(){
		$("#catpanel").show();
				$("#primary_cat").show();
		//$("#subcat1").html("");
		$("#subcat1").show();
		//$("#subcat2").html("");
		$("#subcat2").show();
		//$("#subcat3").html("");
		$("#subcat3").show();
		//$("#subcat4").html("");
		$("#subcat4").show();

	});
</script>
</body>
</html>
<?

}

@mysqli_close($ossdb);

//--------------------------------------------------------------
function processData()
{
	global $stone_type,$lot_no,$width,$length,$height,$weight,$pallet_loc,$pallet_loc2,$pallet_loc3,$keywords,$SKU,$description,$grade,$operator,$ossdb,$yard,$zone,$pal_no,$category;
	global $PrimaryCatT, $SubCat1T,$SubCat2T,$SubCat3T,$SubCat4T,$colors,$sqft,$description2;
	global $PrimaryCat, $SubCat1,$SubCat2,$SubCat3,$SubCat4;
	
	$category = "";
	if($PrimaryCatT != ""){
		$category .= $PrimaryCatT;
	}
	if($SubCat1T != "" && $SubCat1T != "none" && $SubCat1T !="(Select Subcategory - Level 1)"){
		$category .= "|$SubCat1T";
	}
	if($SubCat2T != "" && $SubCat2T != "none" && $SubCat2T !="(Select Subcategory - Level 2)"){
		$category .= "|$SubCat2T";
	}
	if($SubCat3T != "" && $SubCat3T != "none" && $SubCat3T !="(Select Subcategory - Level 3)"){
		$category .= "|$SubCat3T";
	}
	if($SubCat4T != "" && $SubCat4T != "none" && $SubCat4T !="(Select Subcategory - Level 4)"){
		$category .= "|$SubCat4T";
	}
	
	$pallet_loc1 = $pallet_loc;
	
	if($pallet_loc2 != ""){
		$pallet_loc .= "|" . $pallet_loc2;
	}
	if($pallet_loc3 != ""){
		$pallet_loc .= "|" . $pallet_loc3;
	}

if(_USECAT == 1){
//if($stone_type == ""){
	$stone_type = $SubCat1T . " " . $SubCat2T . " " . $SubCat3T . " " . $SubCat4T;
//}
//if($description == ""){
	$description = $stone_type . " - \n$description2 - \n";
	
//}
}
	
//	$KEYWORDS = getKeywordList($PrimaryCatT);
	$description .= " \n** Uses: $keywords **\n"; //\nKeywords: " . str_replace("|", ", ",$category) . " **\n";
	
	$sqlcmd = "INSERT INTO item_data SET SKU='" . mysqli_real_escape_string($ossdb, $SKU) . "', ";
	$sqlcmd .= "item_type='" . mysqli_real_escape_string($ossdb, $stone_type) . "', ";
	$sqlcmd .= "lot_no='" . mysqli_real_escape_string($ossdb, $lot_no) . "', ";
	$sqlcmd .= "width='" . mysqli_real_escape_string($ossdb, $width) . "', ";
	$sqlcmd .= "length='" . mysqli_real_escape_string($ossdb, $length) . "', ";
	$sqlcmd .= "height='" . mysqli_real_escape_string($ossdb, $height) . "', ";
	$sqlcmd .= "weight='" . mysqli_real_escape_string($ossdb, $weight) . "', ";
	$sqlcmd .= "item_location='" . mysqli_real_escape_string($ossdb, $pallet_loc) . "', ";
	$sqlcmd .= "description='" . mysqli_real_escape_string($ossdb, $description) . "', ";
	$sqlcmd .= "status='new', ";
	$sqlcmd .= "operator='" . mysqli_real_escape_string($ossdb, $operator) . "', ";
	$sqlcmd .= "recd_date='" . date("Y-m-d H:i:s") . "', ";
	$sqlcmd .= "x1='" . mysqli_real_escape_string($ossdb, "") . "', "; // image
	$sqlcmd .= "x2='" . mysqli_real_escape_string($ossdb, "") . "', "; // image
	$sqlcmd .= "grade='" . mysqli_real_escape_string($ossdb, $grade) . "',";
	$sqlcmd .= "yard='" . mysqli_real_escape_string($ossdb, $yard) . "',";
	$sqlcmd .= "zone='" . mysqli_real_escape_string($ossdb, $zone) . "',";
	$sqlcmd .= "sqft='$sqft',";
	$sqlcmd .= "pal_no='" . mysqli_real_escape_string($ossdb, $pal_no) . "',category='" . mysqli_real_escape_string($ossdb,$category) . "',color='$colors'";	
	
	if(!$rs = mysqli_query($ossdb, $sqlcmd)){
		safeexit("ERROR ADDING ITEM: " . mysqli_error($ossdb));
	}
	sleep(1);
	if($zone != "SHIPPED" && $zone !="TUMBLED" && $zone != "PICKED_UP"){
	if(!$rq = mysqli_query($ossdb, "UPDATE bin_location_map SET status='full',item_ref='" . mysqli_real_escape_string($ossdb,$SKU) . "' WHERE bin_loc='" .	mysqli_real_escape_string($ossdb, $pallet_loc1) . "' AND yard='$yard' AND zone='$zone'")){
		safeexit("ERROR UPDATING bin_loc: " . mysqli_error($ossdb));
	}
	sleep(1);
	if($pallet_loc2 != ""){
	if(!$rq = mysqli_query($ossdb, "UPDATE bin_location_map SET status='full',item_ref='" . mysqli_real_escape_string($ossdb,$SKU) . "' WHERE bin_loc='" .	mysqli_real_escape_string($ossdb, $pallet_loc2) . "' AND yard='$yard' AND zone='$zone'")){
		safeexit("ERROR UPDATING bin_loc2: " . mysqli_error($ossdb));
	}
	}
	sleep(1);
	if($pallet_loc3 != ""){
	if(!$rq = mysqli_query($ossdb, "UPDATE bin_location_map SET status='full',item_ref='" . mysqli_real_escape_string($ossdb,$SKU) . "' WHERE bin_loc='" .	mysqli_real_escape_string($ossdb, $pallet_loc3) . "' AND yard='$yard' AND zone='$zone'")){
		safeexit("ERROR UPDATING bin_loc3: " . mysqli_error($ossdb));
	}
	}
	}

}
//--------------------------------------------------------
function safeexit($msg)
{
	global $ossdb,$ossm;
	echo $msg;
	
	$fp = fopen("/home/lcflwms/logs/wms-error.log","a");
	if($fp){
		fputs($fp, $msg . "\n");
		fclose($fp);
	}
	@mysqli_close($ossdb);
	@mysqli_close($ossm);	
	exit($msg);
}
