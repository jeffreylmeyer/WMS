<?php
//---------------------------------------------------------------------------
// MS V2
// Pricing adjustment by percentage
// Created: 24-FEB-2015 by Jeffrey L Meyer
//---------------------------------------------------------------------------

exit("NOT FULLY TESTED - DO NOT USE");

require_once("wmsconfig.php");



$user = isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "itdept";
if($user != "ossm" && $user != "itdept" && $user != "osscorp"){
	exit("Manager login required.");
}

$STORECATEGORY = MAGENTO_STORE_CATEGORY;
$STORECODE = "osslc";

$HDIR = UPLOADDIR;

$edit = SITEEXPORTDIR;

$dt = date("Y-m-d_His");

$export_file = $edit . "/" . DEFAULTYARD . "-$dt" . "-PRICE-ADJUST.csv";


require_once("header.php");

require_once("db_connect.php");
$ossdb = oss_connect();

//$cmd = isset($_POST['cmd']) ? $_POST['cmd'] : "process";
//$cbs = isset($_POST['cbs']) ? $_POST['cbs'] : array();

$Markup = isset($argv[1]) ? $argv[1]:0;

if($Markup==0){
	$Markup = isset($_REQUEST['m']) ? $_REQUEST['m'] : 1;
}


$ITEM = array();


$sqlcmd = "SELECT * FROM item_data WHERE posted='Yes' ORDER BY SKU ASC";
if(!$rs = mysqli_query($ossdb, $sqlcmd)){
	exit("ERROR: " . mysqli_error($ossdb));
}
$rows = mysqli_num_rows($rs);


	if($rows >0){
	
		$fp = fopen($export_file,"w");

		
		//fputs($fp,"SKU,item_name,lot_no,pal_no,width,length,height,weight,bin_location,description,grade,costnf,costf,sell,img1,img2\n");
		fputs($fp,"SKU,Purchase Description,Lot #,Pallet #,weight,sqft,grade,Cost (Without freight),Cost,Price,New Price,Markup\n");
		
		while($rows > 0){
			$rd = mysqli_fetch_assoc($rs);
			
		
			
			if(getItem($rd['SKU'])){
			if($ITEM['costf'] <=0 && $ITEM['costnf'] <=0){ $ITEM['costf'] = 100.00;}
			$NewSell = $ITEM['costf'] * $Markup;
			if($NewSell <=0){ $NewSell = 100.00;}
//			$Gross = ($ITEM['costf']/$NewSell)*100*$Markup;
			
//			$P = $ITEM['sell'] - $ITEM['costf'];
//			$Margin = $P / $ITEM['costf'];
			
			$line = swapC($ITEM['SKU']) . ",\"" . stripslashes(swapC($ITEM['item_type'])) . "\",";
			$line .= swapC($ITEM['lot_no']) . ",";
			$line .= swapC($ITEM['pal_no']) . ",";
			$line .= $ITEM['weight'] . ",";	
			$line .= $ITEM['sqft'] . ",";	
			$line .= strtoupper($ITEM['grade']) . ",";
			$line .= $ITEM['costnf'] . ",";
			$line .= $ITEM['costf'] . ",";
			$line .= $ITEM['sell'] . "," . number_format($NewSell,2) . ",$Markup";//,$Gross,$Margin";
			fputs($fp,$line . "\n");
			}

			$rows--;
		}
		fclose($fp);
		mysqli_free_result($rs);
		}

	
	echo "<a href=\"" . str_replace(SITEHDIR,"", $export_file) . "\">Click Here to download CSV of new pricing.</a><br />";

 
 /*
 $sm = popen ("/usr/sbin/sendmail -t", "w");
	if($sm){
		fputs($sm, "X-Mailer: Overstock Stone WMS v2.0 [en]\n"); 
		fputs($sm, "MIME-Version: 1.0\n");
		fputs($sm, "Content-type: text/html; charset=utf-8\n");
		fputs($sm, "Reply-to: " . SITEEMAIL . "\n");
		fputs($sm, "From: " . SITEEMAIL . "\n");
		fputs($sm, "To: it@overstockstone.com\n");
		fputs($sm, "CC: sales@overstockstone.com\n");
		fputs($sm, "Subject: Items Posted from" . SITENAME . "\n\n");
		fputs($sm, "$export_file\n$export_file_qb\n.");
		pclose($sm);
	}
 */


//------------------------------------------
function swapC($txt)
{
	$txt = str_replace(",","-",$txt);
	$txt = str_replace("\""," inch",$txt);

	return $txt;
}

//-------------------------------------------------------
function getItem($itemid)
{
	global $ossdb,$ITEM;
	
	if(!$rs = mysqli_query($ossdb,"SELECT * FROM item_data WHERE SKU='$itemid'")){
		exit("ERROR: " . mysqli_error($ossdb));
	}
	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		$ITEM = mysqli_fetch_assoc($rs);
		mysqli_free_result($rs);
	}else{
		$ITEM = array();
		return 0;
	}
	return 1;
}
?>