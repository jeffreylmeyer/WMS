<?php
//--------------------------------------------------------------------------
//  WMS V2.1.1
// manager review of incoming items
//
// Last Modified: 30-JAN-2015 by Jeffrey L Meyer
//--------------------------------------------------------------------------
ini_set('display_errors',0);
ob_start();

$user = isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "";


//if($user != "ossm" && $user !="itdept" && $user !="osscorp" ){
//	$MGR=0;
//	exit("Manager login required.");
//}else{
//	$MGR=1;
//}
 
 
require_once("header.php");
require_once("wmsconfig.php");

$HDIR = SITEHDIR;
$SDIR = "/home/wms/www/s/media/catalog/product";

?>



<?

require_once("db_connect.php");
require_once("ca.php");
$ossdb = oss_connect();

$cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : "";
$hl = isset($_REQUEST['hl']) ? $_REQUEST['hl'] : "";
$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : "SKU";
$ordBY = isset($_REQUEST['ordBY']) ? $_REQUEST['ordBY'] : "ASC";
$qryf = isset($_REQUEST['qryf']) ? $_REQUEST['qryf'] : "";
$qry = isset($_REQUEST['qry']) ? $_REQUEST['qry'] : "";
$typeqf = isset($_REQUEST['typeq']) ? $_REQUEST['typeq'] : "eq";
$postedonly = isset($_REQUEST['postedonly']) ? $_REQUEST['postedonly'] : 0;
$skipshipped = isset($_REQUEST['skipshipped']) ? $_REQUEST['skipshipped'] : 0;



$qry_list = "
<select name='qryf' size=\"1\" style=\"font-size:8pt;font-family:arial,helvetica,tahoma\">
<option value='SKU'>SKU</option>
<option value='item_type'>Item Name</option>
<option value='lot_no'>Lot #</option>
<option value='pal_no'>Pallet #</option>
<option value='zone'>Zone</option>
<option value='item_location'>Slot</option>
<option value='weight'>Weight</option>
<option value='grade'>Grade</option>
<option selected value='description'>Description</option>
<option value='category'>Category</option>
</select>
";

$ordby = "
<select name=\"sort\" size=\"1\" style=\"font-size:8pt;font-family:arial,helvetica,tahoma\">
<option selected value='SKU'>SKU</option>
<option value='item_type'>Item Name</option>
<option value='lot_no'>Lot #</option>
<option value='pal_no'>Pallet #</option>
<option value='zone'>Zone</option>
<option value='item_location'>Slot</option>
<option value='weight'>Weight</option>
<option value='grade'>Grade</option>
<option value='description'>Description</option>
<option value='category'>Category</option>
</select>
";

$typeq = "<select name='typeq' size='1' style=\"font-size:8pt;font-family:arial,helvetica,tahoma\">
<option selected value='like'>contains</option>
<option value='eq'>Exactly =</option>
<option value='geq'>&gt;=</option>
<option value='leq'>&lt;=</option>
</select>
";

?>
<style type="text/css">
body {
	background:#FEC050;
}
</style>
<style type="text/css">
.trr {
	font-family:Verdana,Arial,helvetica,Tahoma;
	font-size:9pt;
}
#desc {
	width:250px;
	
}
#wt {
	width:100px;
}
#itemn {
	width:150px;
}
.cbClass {
}

.erroritem {
	border:3px solid #FF0000;
	border-radius:3px;
	background:#FFA0A0;
}
</style>
<script type="text/javascript">
 $(document).ready(function () {
        $("#checkall").click(function () {
            $(".cbClass").attr('checked', this.checked);
        });
    });
</script>

<?
$TOTAL_POUNDS = 0;
$TOTAL_TONS=0;
$TOTAL_SQFT = 0;
$MGR=1;
//-------------------------------------
if($cmd != "print"){

$qrystr = "";
foreach($_REQUEST as $key=>$val){
	$qrystr .=$key . "=" . $val . "&";
}
$qrystr = rtrim($qrystr,"&");
?>
<form method="post" action="manager.php">
<div style="float:left;width:90%;">
Query: <?=$qry_list;?>&nbsp;<?=$typeq;?>&nbsp;<input type="text" name="qry" size="40" maxlength="128" value="<?=$qry;?>" />&nbsp;
Order by:<?=$ordby;?>&nbsp;<input type="radio" name="ordBY" value="ASC" <?if($ordBY=="ASC" || $ordBY==""){ echo "checked='checked'";}?> />ASC<input type="radio" name="ordBY" <?if($ordBY=="DESC"){ echo "checked='checked'";}?> value="DESC" />DESC&nbsp;
<input type="checkbox" name="postedonly" value="1" />Show POSTED Items Only&nbsp;&nbsp;
<input type="checkbox" name="skipshipped" value="1" checked="checked" />Skip 'Shipped'&nbsp;&nbsp;
<input type="submit" value="Search"/>&nbsp;&nbsp;<a href="manager.php" target="_blank">Open In Blank Window</a> | <a href="manager.php?<?=$qrystr?>&cmd=print" target="_blank">Printable Version</a> 
</div>
</form>
<div>

<br clear="all" /><br />

<form method="post" name="mgrv" id="mgrv" action="postitems.php">

	<table class="trr" border="1" cellpadding="2" cellspacing="0" width="96%">

<?
	if($MGR==0){
	?>
	<tr bgcolor="#00F0A0"><td>&nbsp;</td><td align="center">Product ID<br />(SKU)</td><td>Lot #</td><td>Pal #</td><td>Item Name</td><td>W&quot;</td><td>L&quot;</td><td>H&quot;</td><td nobr='nobr'>Weight</td><td>Sq. Ft.</td><td>Yard</td><td>Zone</td><td>Bin Location</td><td>Top Image</td><td>Side Image</td><td>Category</td><td>Description</td><td width="10">G</td><td>Cost Exc. Freight</td><td>Cost W/Freight</td><td>Sell Price</td><td>Color</td><td>Date Rec'd</td><td>Operator</td></tr>

<?
	$fr1 = "<tr bgcolor='{HL}'><td align='center'><a name='{LINK}'></a>{CB}</td><td>{SKU}</td><td>{LOT}</td><td>{PAL}</td><td><div id='itemn'>{TYPE}</div></td><td>{W}</td><td>{L}</td><td>{H}</td><td nobr='nobr'><div id='wt'>{WT}</div></td><td>{SQFT}</td><td>{YARD}</td><td>{ZONE}</td><td>{BIN}</td><td>{TOPI}</td><td>{SIDEI}</td><td>{CAT}</td><td width='20%' >{DESC}</td><td>{G}</td><td>{COSTNF}</td><td>{COSTF}</td><td>{SELL}</td><td width='5%'>{COLOR}</td><td>{DATE}</td><td>{USER}</td></tr>\n
	";

}else{
	// managers view
?>
	<tr bgcolor="#00F0A0"><td><input type="checkbox" name="checkall" id="checkall" value="1" /></td><td align="center">Product ID<br />(SKU)</td><td>Lot #</td><td>Pal #</td><td>Item Name</td><td>W&quot;</td><td>L&quot;</td><td>H&quot;</td><td>Weight</td><td>Sq. Ft.</td><td>Yard</td><td>Zone</td><td>Bin Location</td><td>Top Image</td><td>Side Image</td><td>Category</td><td>Description</td><td width="10">G</td><td>Cost Exc. Freight</td><td>Cost W/Freight</td><td>Sell Price</td><td>Color</td><td>Date Rec'd</td><td>Operator</td></tr>

<?
	$fr1 = "<tr bgcolor='{HL}'><td align='center'><a name='{LINK}'></a>{CB}</td><td>{SKU}</td><td>{LOT}</td><td>{PAL}</td><td><div id='itemn'>{TYPE}</div></td><td>{W}</td><td>{L}</td><td>{H}</td><td><div id='wt'>{WT}</div></td><td>{SQFT}</td><td>{YARD}</td><td>{ZONE}</td><td>{BIN}</td><td>{TOPI}</td><td>{SIDEI}</td><td>{CAT}</td><td width='20%' ><div id='desc'>{DESC}</div></td><td>{G}</td><td>{COSTNF}</td><td>{COSTF}</td><td>{SELL}</td><td width='5%'>{COLOR}</td><td>{DATE}</td><td>{USER}</td></tr>\n
	";
	
}



if($postedonly ==1){
	$sqlcmd = "SELECT * FROM item_data WHERE posted ='Yes' ";
}else{
	$sqlcmd = "SELECT * FROM item_data WHERE posted !='Yes' ";
}
if($qryf != ""){
	$sqlcmd = "SELECT * FROM item_data WHERE 1 ";
}

if($skipshipped == 1){
	$sqlcmd .= " AND zone != 'SHIPPED' AND zone !='PICKED_UP' AND zone !='TUMBLED'";
}

if($qryf != ""){
	if($typeqf != "like"){
		if($typeqf == "eq"){
			$sqlcmd .= " AND $qryf = '$qry' ";
		}elseif($typeqf == "geq"){
			$sqlcmd .= " AND $qryf >= '$qry' ";
		}elseif($typeqf == "leq"){
			$sqlcmd .= " AND $qryf <= '$qry' ";
		}
	}else{
		$sqlcmd .= " AND $qryf LIKE '%$qry%' ";
	}
}

$sqlcmd .= " ORDER BY $sort $ordBY";

if(!$rs = mysqli_query($ossdb, $sqlcmd)){
	exit("ERROR: " . mysqli_error($ossdb) . " <br>\n$sqlcmd");
}
$rows = mysqli_num_rows($rs);


$drow ='	<tr bgcolor="#00F0A0"><td>&nbsp;</td><td align="center">Product ID<br />(SKU)</td><td>Lot #</td><td>Pal #</td><td>Item Name</td><td>W&quot;</td><td>L&quot;</td><td>H&quot;</td><td>Weight</td><td>Sq. Ft.</td><td>Yard</td><td>Zone</td><td>Bin Location</td><td>Top Image</td><td>Side Image</td><td>Category</td><td>Description</td><td>G</td><td>Cost Exc. Freight</td><td>Cost W/Freight</td><td>Sell Price</td><td>Color</td><td>Date Rec' . "'d</td><td>Operator</td></tr>
";

if($rows > 0){
 echo "<font style='color:8080E0;font-size:8pt;'>".$sqlcmd . "</font><br>\n";
 $tog = 0;
	while($rows > 0){
		$rd = mysqli_fetch_assoc($rs);
		$row = $fr1;
		if($hl == $rd['SKU']){
			$row = str_replace("{HL}","#80FF80", $row);
		}else{
			$row = str_replace("{HL}","#EEEEEE", $row);
		}
		
				
		$label = "<a href=\"step3.php?item=$rd[SKU]&text=".urlencode($rd['item_type'])."&weight=$rd[weight]&lot_no=$rd[lot_no]&pal_no=$rd[pal_no]\" target=\"_blank\"><br />
	<img src=\"images/label-btn.jpg\" /></a>";
	
		$row = str_replace("{LINK}",$rd['SKU'], $row);
		
		$tons = $rd['weight'] /2000;
		$tons = number_format($tons,3);
		
		$TOTAL_POUNDS += $rd['weight'];
		$TOTAL_TONS += $tons;
		$TOTAL_SQFT += $rd['sqft'];
		
		$row = str_replace("{CB}", "<input class=\"cbclass\" type='checkbox' name='cbs[$rd[SKU]]' value='$rd[SKU]'\">", $row);
		$row = str_replace("{SKU}", "<a href=\"edititem.php?sku=$rd[SKU]\">$rd[SKU]</a><br />$label", $row);
		$row = str_replace("{CAT}", stripslashes($rd['category']), $row);
		$row = str_replace("{TYPE}", stripslashes($rd['item_type']), $row);
		
		if($rd['lot_no'] != "" ){
			$row = str_replace("{LOT}", stripslashes($rd['lot_no']), $row);
		}else{
			$row = str_replace("{LOT}", "<div class='erroritem'>" . stripslashes($rd['lot_no']) . "</div>", $row);		
		}
		
		if($rd['pal_no'] == ""){
			$row = str_replace("{PAL}", "<div class='erroritem'>" . stripslashes($rd['pal_no']) . "</div>", $row);
		}else{
			$row = str_replace("{PAL}", stripslashes($rd['pal_no']), $row);
		}

		if($rd['width'] == "" || $rd['width'] ==0){
			$row = str_replace("{W}", "<div class='erroritem'>" . stripslashes($rd['width']) . "</div>", $row);
		}else{
			$row = str_replace("{W}", stripslashes($rd['width']), $row);
		}

		if($rd['length'] == "" ||$rd['length'] ==0){
			$row = str_replace("{L}", "<div class='erroritem'>" . stripslashes($rd['length']) . "</div>", $row);
		}else{
			$row = str_replace("{L}", stripslashes($rd['length']), $row);
		}
		
		if($rd['height'] == "" ||$rd['height'] ==0){
			$row = str_replace("{H}", "<div class='erroritem'>" . stripslashes($rd['height']) . "</div>", $row);
		}else{
			$row = str_replace("{H}", stripslashes($rd['height']), $row);
		}
		if($rd['weight'] == ""||$rd['weight'] ==0){
			$row = str_replace("{WT}", "<div class='erroritem'>" . stripslashes($rd['weight'])  . " Lbs.\n\n<br /><br />$tons Tons</div>", $row);
		}else{
			$row = str_replace("{WT}", stripslashes($rd['weight']) . " Lbs.\n\n<br /><br />$tons Tons", $row);
		}
		$row = str_replace("{YARD}", $rd['yard'], $row);
		$row = str_replace("{ZONE}", $rd['zone'], $row);
		$row = str_replace("{BIN}", $rd['item_location'], $row);
		$row = str_replace("{DESC}", str_replace("\n","<br />",stripslashes($rd['description'])), $row);
		$row = str_replace("{G}", $rd['grade'], $row);
		$row = str_replace("{DATE}", $rd['recd_date'], $row);
		$row = str_replace("{USER}", $rd['operator'], $row);

	$thumb="";
	  if(isset($rd['x1'])){
		if(strpos($rd['x1'],'/')!==false){
		list($nio,$p1,$p2,$img) = @explode('/', $rd['x1']);
		}
		if(!is_dir($HDIR . "/images/catalog/$p1")){
			@mkdir($HDIR . "/images/catalog/$p1",0777);

		}
		if(!is_dir($HDIR . "/images/catalog/$p1/$p2")){
			@mkdir($HDIR . "/images/catalog/$p1/$p2",0777);
		}
		if(!is_dir($HDIR . "/images/catalog/$p1/$p2/thumbs")){
			@mkdir($HDIR . "/images/catalog/$p1/$p2/thumbs",0777);
		}
	  
				
		
		$thumb = $HDIR . "/images/catalog/$p1/$p2/thumbs/" . $img;
		$timg = "/images/catalog/$p1/$p2/thumbs/" . $img;
	}	
		if(!file_exists($thumb)){
			//make_thumb("/home/rookwms/www/images/catalog" . $rd['x1'],$thumb,100,100);
			$im= thumbnail($HDIR ."/images/catalog" . $rd['x1'],100);
			imageToFile($im, $thumb);
			
			//   $im = thumbnail('temp.jpg', 100);
			//  imageToFile($im, 'temp-thumbnail.jpg');
 
		}
			$row = str_replace("{TOPI}", "<a title=\"Click to zoom\" href=\"".SITEURL."/images/catalog" . $rd['x1'] . "\" target=\"_blank\"><img src=\"".SITEURL."$timg\" width='100' /></a><br clear='all' />
			<a href=\"step2.php?text=".urlencode($rd['item_type'])."&weight=$rd[weight]&yard=$rd[yard]&lot_no=$rd[lot_no]&pal_no=$rd[pal_no]&barcode=$rd[SKU]&category=$rd[category]\">Reupload Images</a>", $row);
		
		
/*		$row = str_replace("{TOPI}", "<a title=\"Click to zoom\" href=\"".SITEURL."/images/catalog" . $rd['x1'] . "\" target=\"_blank\"><img src=\"".SITEURL."/images/catalog" . $rd['x1'] . "\" width='100' /></a><br clear='all' />
		<a href=\"step2.php?text=".urlencode($rd['item_type'])."&weight=$rd[weight]&yard=$rd[yard]&lot_no=$rd[lot_no]&pal_no=$rd[pal_no]&barcode=$rd[SKU]&category=$rd[category]\">Reupload Images</a>", $row);
*/
	if(isset($rd['x2'])){
		if(strpos($rd['x2'],'/')!==false){
		list($nio,$p1,$p2,$img) = explode('/', $rd['x2']);
		}
		
		if(!is_dir($HDIR . "/images/catalog/$p1/$p2/thumbs")){
			@mkdir($HDIR ."/images/catalog/$p1/$p2/thumbs",0777);
		}

				
		$thumb = $HDIR . "/images/catalog/$p1/$p2/thumbs/" . $img;
		$timg = "/images/catalog/$p1/$p2/thumbs/" . $img;
		}
		
		if(!file_exists($thumb)){
			//make_thumb("/home/rookwms/www/images/catalog" . $rd['x1'],$thumb,100,100);
			$im= thumbnail($HDIR ."/images/catalog" . $rd['x2'],100);
			imageToFile($im, $thumb);
			
			//   $im = thumbnail('temp.jpg', 100);
			//  imageToFile($im, 'temp-thumbnail.jpg');
 
		}
			$row = str_replace("{SIDEI}", "<a title=\"Click to zoom\" href=\"".SITEURL."/images/catalog" . $rd['x2'] . "\" target=\"_blank\"><img src=\"".SITEURL."$timg\" width='100' /></a><br clear='all' />
			<a href=\"step2.php?text=".urlencode($rd['item_type'])."&weight=$rd[weight]&yard=$rd[yard]&lot_no=$rd[lot_no]&pal_no=$rd[pal_no]&barcode=$rd[SKU]&category=$rd[category]\">Reupload Images</a>", $row);
				
	/*	$row = str_replace("{SIDEI}", "<a title=\"Click to zoom\" href=\"".SITEURL."/images/catalog" . $rd['x2'] . "\" target=\"_blank\"><img src=\"".SITEURL."/images/catalog" . $rd['x2'] . "\" width='100' /><br clear='all' />
		<a href=\"step2.php?text=".urlencode($rd['item_type'])."&weight=$rd[weight]&yard=$rd[yard]&lot_no=$rd[lot_no]&pal_no=$rd[pal_no]&barcode=$rd[SKU]&category=$rd[category]\">Reupload Images</a>", $row);
	*/	
		$row = str_replace("{COSTNF}", $rd['costnf'], $row);
		$row = str_replace("{COSTF}", $rd['costf'], $row);
		$row = str_replace("{SELL}", $rd['sell'], $row);
		$row = str_replace("{SQFT}", $rd['sqft'], $row);
		$row = str_replace("{SFPRICE}", $rd['price_per_sqft'], $row);				
		$row = str_replace("{COLOR}", $rd['color'], $row);
		
		echo $row;
		$tog++;
		if($tog ==5){
			echo $drow;
			$tog=0;
		}	
		
		$rows--;
	}
	
	$fr2 = "<tr ><td align='right' colspan='8'>TOTALS</td><td><div id='wt'>{WT}</div></td><td><b>{SQFT} SQFT</b></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td width='20%' >&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td width='5%'>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n
	";
	
	$row = str_replace("{SQFT}",  number_format($TOTAL_SQFT,3), $fr2);
	$row = str_replace("{WT}", "<div ><b>" . number_format($TOTAL_POUNDS,3)  . " Lbs.\n\n<br /><br />" . number_format($TOTAL_TONS,3) . " Tons</b></div>", $row);
	echo $row;
	?></fieldset><?
	mysqli_free_result($rs);
}
		
?>

<tr><td colspan="21">
<input type="hidden" name="cmd" value="process" />
<?
if($user =="itdept" || $user == "osscorp"){
?>
	<input type="image" src="images/export-btn.jpg" value="POST NOW" />&nbsp;<input type="checkbox" name="skippost" value="1" />Skip Post
<?
}else{
	//echo "Manager functions disabled...";
}
?>
	<br />
<br /><br />
</td>
</tr>
</table>
</form>

</div>

<? //end != print
}else{
	//----------------------------------------------------------------------
	// printable page
	$mdir = "/home/wms/www/images/catalog/";
	
	//$DIRS = array('/thumbs/');
	$S1L = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-";
	$S2L = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-";	
	$lsize = strlen($S1L);
/*	
	for($idx=0;$idx < $lsize;$idx++){
		$s1 = substr($S1L,$idx,1);
			for($sdx=0;$sdx < $lsize;$sdx++){
				$s2 = substr($S2L,$sdx,1);				
				$rdir = $mdir;					
				//read dir
				$rdir .= "$s1/$s2/thumbs/";
				if (is_dir($rdir)) {
				    if ($dh = opendir($rdir)) {
				        while (($file = readdir($dh)) !== false) {
				        	if($file != "." && $file != ".."){
	//				           fputs($fp, $rdir . $file . "\n");
							unlink("$rdir/$file");
					        }
				        }
				        closedir($dh);
				    }
				}
			}
	}
*/
	
	
?>
<table border="1" cellpadding="3" cellspacing="0">
<tr bgcolor="#00F0A0"><td align="center">Product ID<br />(SKU)</td><td>Lot #</td><td width="80">Pal #</td><td>Item Name</td><td>W&quot;</td><td>L&quot;</td><td>H&quot;</td><td nobr='nobr'>Weight</td><td>Sq. Ft.</td><td>Yard</td><td>Zone</td><td>Bin Location</td><td>Top Image</td><td>Side Image</td><td>Description</td><td>Sell Price</td><td>Rec'd/Modified</td></tr>

<?
$drow ='<tr bgcolor="#00F0A0"><td align="center">Product ID<br />(SKU)</td><td>Lot #</td><td width="80">Pal #</td><td>Item Name</td><td>W&quot;</td><td>L&quot;</td><td>H&quot;</td><td>Weight</td><td>Sq. Ft.</td><td>Yard</td><td>Zone</td><td>Bin Location</td><td>Top Image</td><td>Side Image</td><td>Description</td><td>Sell Price</td><td>Rec' . "'d/Modified</td></tr>
";

$hl1 = "#FFFFFF";
$hl2 = "#D0E0D0";
$hlx = 0;

	$fr1 = "<tr bgcolor='{HL}'><td>{SKU}</td><td>{LOT}</td><td>{PAL}</td><td><div id='itemn'>{TYPE}</div></td><td>{W}</td><td>{L}</td><td>{H}</td><td nobr='nobr'><div id='wt'>{WT}</div></td><td>{SQFT}</td><td>{YARD}</td><td>{ZONE}</td><td>{BIN}</td><td>{TOPI}</td><td>{SIDEI}</td><td width='25%' >{DESC}</td><td>{SELL}</td><td>{DATE}</td></tr>\n
	";
	
	if($qryf != ""){
	$sqlcmd = "SELECT * FROM item_data WHERE 1 ";
}

if($skipshipped == 1){
	$sqlcmd .= " AND zone != 'SHIPPED' AND zone !='PICKED_UP' ";
}

if($qryf != ""){
	if($typeqf != "like"){
		if($typeqf == "eq"){
			$sqlcmd .= " AND $qryf = '$qry' ";
		}elseif($typeqf == "geq"){
			$sqlcmd .= " AND $qryf >= '$qry' ";
		}elseif($typeqf == "leq"){
			$sqlcmd .= " AND $qryf <= '$qry' ";
		}
	}else{
		$sqlcmd .= " AND $qryf LIKE '%$qry%' ";
	}
}

$sqlcmd .= " ORDER BY $sort $ordBY";

//	$sql = "SELECT * FROM item_data WHERE zone NOT IN('PICKED_UP','SHIPPED','TUMBLED') AND zone NOT LIKE 'Outgoing%' ORDER BY SKU ASC";
	if(!$rs = mysqli_query($ossdb, $sqlcmd)){
		safeexit("ERROR: " . mysqli_error($ossdb));
	}
	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		while($rows > 0){
			$rd = mysqli_fetch_assoc($rs);
			
$row = $fr1;
		// greenbar effect
		if($hlx == 0){
			$row = str_replace("{HL}",$hl2, $row);
			$hlx=1;
		}else{
			$hlx=0;
			$row = str_replace("{HL}",$hl1, $row);
		}
		
			
		$row = str_replace("{SKU}",$rd['SKU'], $row);
		
		$tons = $rd['weight'] /2000;
		$tons = number_format($tons,3);
		$row = str_replace("{TYPE}", stripslashes($rd['item_type']), $row);
		
		if($rd['lot_no'] != "" ){
			$row = str_replace("{LOT}", stripslashes($rd['lot_no']), $row);
		}else{
			$row = str_replace("{LOT}", "<div class='erroritem'>" . stripslashes($rd['lot_no']) . "</div>", $row);		
		}
		
		if($rd['pal_no'] == ""){
			$row = str_replace("{PAL}", "<div class='erroritem'>" . stripslashes($rd['pal_no']) . "</div>", $row);
		}else{
			$row = str_replace("{PAL}", stripslashes($rd['pal_no']), $row);
		}

		if($rd['width'] == "" || $rd['width'] ==0){
			$row = str_replace("{W}", "<div class='erroritem'>" . stripslashes($rd['width']) . "</div>", $row);
		}else{
			$row = str_replace("{W}", stripslashes($rd['width']), $row);
		}

		if($rd['length'] == "" ||$rd['length'] ==0){
			$row = str_replace("{L}", "<div class='erroritem'>" . stripslashes($rd['length']) . "</div>", $row);
		}else{
			$row = str_replace("{L}", stripslashes($rd['length']), $row);
		}
		
		if($rd['height'] == "" ||$rd['height'] ==0){
			$row = str_replace("{H}", "<div class='erroritem'>" . stripslashes($rd['height']) . "</div>", $row);
		}else{
			$row = str_replace("{H}", stripslashes($rd['height']), $row);
		}
		if($rd['weight'] == ""||$rd['weight'] ==0){
			$row = str_replace("{WT}", "<div class='erroritem'>" . stripslashes($rd['weight'])  . " Lbs.\n\n<br /><br />$tons Tons</div>", $row);
		}else{
			$row = str_replace("{WT}", stripslashes($rd['weight']) . " Lbs.\n\n<br /><br />$tons Tons", $row);
		}
		$row = str_replace("{YARD}", $rd['yard'], $row);
		$row = str_replace("{ZONE}", $rd['zone'], $row);
		$row = str_replace("{BIN}", $rd['item_location'], $row);
		$row = str_replace("{DESC}", str_replace("\n","<br />",stripslashes($rd['description'])), $row);
		$row = str_replace("{DATE}", $rd['recd_date'], $row);
		

	$thumb="";
	  if(isset($rd['x1'])){
		if(strpos($rd['x1'],'/')!==false){
		list($nio,$p1,$p2,$img) = @explode('/', $rd['x1']);
		}
		if(!is_dir($HDIR . "/images/catalog/$p1")){
			@mkdir($HDIR . "/images/catalog/$p1",0777);
		}
		if(!is_dir($HDIR . "/images/catalog/$p1/$p2")){
			@mkdir($HDIR . "/images/catalog/$p1/$p2",0777);
		}
		if(!is_dir($HDIR . "/images/catalog/$p1/$p2/thumbs")){
			@mkdir($HDIR . "/images/catalog/$p1/$p2/thumbs",0777);
		}
	  
				
		
		$thumb = $HDIR . "/images/catalog/$p1/$p2/thumbs/" . $img;
		$timg = "/images/catalog/$p1/$p2/thumbs/" . $img;
		@unlink($thumb);
	}	
//		if(!file_exists($thumb)){
			//make_thumb("/home/rookwms/www/images/catalog" . $rd['x1'],$thumb,100,100);
			$im= thumbnail($HDIR ."/images/catalog" . $rd['x1'],120);
			imageToFile($im, $thumb);
			
			//   $im = thumbnail('temp.jpg', 100);
			//  imageToFile($im, 'temp-thumbnail.jpg');
 
//		}else{
			$row = str_replace("{TOPI}", "<img src=\"".SITEURL."$timg\" width='120' /><br clear='all' />", $row);
//		}
		
	if(isset($rd['x2'])){
		if(strpos($rd['x2'],'/')!==false){
		list($nio,$p1,$p2,$img) = explode('/', $rd['x2']);
		}
		
		if(!is_dir($HDIR . "/images/catalog/$p1/$p2/thumbs")){
			@mkdir($HDIR ."/images/catalog/$p1/$p2/thumbs",0777);
		}

				
		$thumb = $HDIR . "/images/catalog/$p1/$p2/thumbs/" . $img;
		$timg = "/images/catalog/$p1/$p2/thumbs/" . $img;
		}
		@unlink($thumb);
//		if(!file_exists($thumb)){
			//make_thumb("/home/rookwms/www/images/catalog" . $rd['x1'],$thumb,100,100);
			$im= thumbnail($HDIR ."/images/catalog" . $rd['x2'],120);
			imageToFile($im, $thumb);
			
			//   $im = thumbnail('temp.jpg', 100);
			//  imageToFile($im, 'temp-thumbnail.jpg');
 
//		}else{
			$row = str_replace("{SIDEI}", "<img src=\"".SITEURL."$timg\" width='100' /></a><br clear='all' />", $row);
//		}
		
		$row = str_replace("{SELL}", $rd['sell'], $row);
		$row = str_replace("{SQFT}", $rd['sqft'], $row);
		//$row = str_replace("{SFPRICE}", $rd['price_per_sqft'], $row);				
		
		echo $row;
		$tog++;
		if($tog ==5){
			echo $drow;
			$tog=0;
		}			
			
			
			$rows--;
		}
		mysqli_free_result($rs);
	}
	
	echo "</table>";
}
?>
<br clear="all" />
<br clear="all" />
<br clear="all" />
<br clear="all" />
<br clear="all" />
<br clear="all" />
<br clear="all" />
<br clear="all" />
<br clear="all" />
<br clear="all" />
<br clear="all" />
<?
@mysqli_close($ossdb);

ob_end_flush();
flush();

function make_thumb($src, $dest, $desired_width,$desired_h=100) {

  /* read the source image */
  $source_image = @imagecreatefromjpeg($src);
  $width = imagesx($source_image);
  $height = imagesy($source_image);


  /* find the "desired height" of this thumbnail, relative to the desired width  */
  $desired_height = $desired_h;

  /* create a new, "virtual" image */
  $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

  /* copy source image at a resized size */
  imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

  /* create the physical thumbnail image to its destination */
  imagejpeg($virtual_image, $dest);
}

 function thumbnail($inputFileName, $maxSize = 100)
    {
        $info = @getimagesize($inputFileName);
 
        $type = isset($info['type']) ? $info['type'] : $info[2];
 
        // Check support of file type
        if ( !(imagetypes() & $type) )
        {
            // Server does not support file type
            return false;
        }
 
        $width  = isset($info['width'])  ? $info['width']  : $info[0];
        $height = isset($info['height']) ? $info['height'] : $info[1];
 
        // Calculate aspect ratio
        $wRatio = $maxSize / $width;
        $hRatio = $maxSize / $height;
 
        // Using imagecreatefromstring will automatically detect the file type
        $sourceImage = @imagecreatefromstring(file_get_contents($inputFileName));
 
        // Calculate a proportional width and height no larger than the max size.
        if ( ($width <= $maxSize) && ($height <= $maxSize) )
        {
            // Input is smaller than thumbnail, do nothing
            return $sourceImage;
        }
        elseif ( ($wRatio * $height) < $maxSize )
        {
            // Image is horizontal
            $tHeight = ceil($wRatio * $height);
            $tWidth  = $maxSize;
        }
        else
        {
            // Image is vertical
            $tWidth  = ceil($hRatio * $width);
            $tHeight = $maxSize;
        }
 
        $thumb = imagecreatetruecolor($tWidth, $tHeight);
 
        if ( $sourceImage === false )
        {
            // Could not load image
            return false;
        }
 
        // Copy resampled makes a smooth thumbnail
        imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $tWidth, $tHeight, $width, $height);
        imagedestroy($sourceImage);
 
        return $thumb;
    }
 
    /**
     * Save the image to a file. Type is determined from the extension.
     * $quality is only used for jpegs.
     * Author: mthorn.net
     */
    function imageToFile($im, $fileName, $quality = 80)
    {
        if ( !$im || file_exists($fileName) )
        {
           return false;
        }
 
        $ext = strtolower(substr($fileName, strrpos($fileName, '.')));
 
        switch ( $ext )
        {
            case '.gif':
                imagegif($im, $fileName);
                break;
            case '.jpg':
            case '.jpeg':
                imagejpeg($im, $fileName, $quality);
                break;
            case '.png':
                imagepng($im, $fileName);
                break;
            case '.bmp':
                imagewbmp($im, $fileName);
                break;
            default:
                return false;
        }
 
        return true;
    }
 
 function safeexit($msg)
 {
 	global $ossdb;
 	@mysqli_close($ossdb);
 	exit($msg);
 }
?>
