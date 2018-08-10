<?php
//---------------------------------------------------------------------------
// WMS V2
// post items
// Modified: 18-FEB-2015 by Jeffrey L Meyer
//
// creates export .csv files for importing into QuickBooks and Magento CE
//---------------------------------------------------------------------------
require_once("wmsconfig.php");
ini_set("display_errors",0);

$skip_post = isset($_REQUEST['skippost']) ? $_REQUEST['skippost'] : 0;

$user = isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "";
if($user != "ossm" && $user != "itdept" && $user != "osscorp"){
	exit("Manager login required.");
}

$STORECATEGORY = MAGENTO_STORE_CATEGORY;
$STORECODE = "osslc";

$HDIR = UPLOADDIR;

$edit = SITEEXPORTDIR;

$dt = date("Y-m-d_His");

$export_file = $edit . "/" . DEFAULTYARD . "-$dt" . "-MAG.csv";
$export_file_qb = $edit . "/" . DEFAULTYARD . "-$dt" . "-QB.csv";

require_once("header.php");

require_once("db_connect.php");
$ossdb = oss_connect();

$cmd = isset($_POST['cmd']) ? $_POST['cmd'] : "";
$cbs = isset($_POST['cbs']) ? $_POST['cbs'] : array();

$ITEM = array();
$TYPE = "qbmagento";

$root_category = MAGENTO_ROOT_CATEGORY;

if($cmd == 'process'){
 if($TYPE == "qbmagento"){
	if(!empty($cbs)){
	
		$fp = fopen($export_file,"w");
		$fp2 = fopen($export_file_qb,"w");
		
		//fputs($fp,"SKU,item_name,lot_no,pal_no,width,length,height,weight,bin_location,description,grade,costnf,costf,sell,img1,img2\n");
		fputs($fp,"_attribute_set,Item Name,Purchase Description,Lot #,Pallet #,width,length,height,weight,sqft,yard,zone,bin_location,Sales Description,grade,Color,Cost (Without freight),Cost,Price,Small Image,Thumbnail,Categories Path,Quantity,COGS Account,Income Account,Asset Account,Cost Per SqFt,Sold By\n");
		
		fputs($fp2,"_attribute_set,Item Name,Purchase Description,Lot #,Pallet #,width,length,height,weight,sqft,yard,zone,bin_location,Sales Description,grade,Color,Cost (Without freight),Cost,Price,Thumbnail,Categories Path,Quantity,COGS Account,Income Account,Asset Account,Sales Tax Code\n");
		foreach($cbs as $key=>$val){
			//echo $val . "<br />";
			$qbline = "";
			
			if(getItem($val)){
			$line = /*swapC($ITEM['SKU']) .*/ "'OSSD'," . swapC($ITEM['SKU']) . ",\"" . stripslashes(swapC($ITEM['item_type'])) . "\",";
			$line .= swapC($ITEM['lot_no']) . ",";
			$line .= swapC($ITEM['pal_no']) . ",";
			$line .= $ITEM['width'] . ",";
			$line .= $ITEM['length'] . ",";
			$line .= $ITEM['height'] . ",";
			$line .= $ITEM['weight'] . ",";	
			$line .= $ITEM['sqft'] . ",";
			$line .= $ITEM['yard'] . ",";	
			$line .= $ITEM['zone'] . ",";	
			$line .= swapC($ITEM['item_location']) . ",";
			$line .= "\"".strip_tags(stripslashes(swapC($ITEM['description']))) . "\n(see Additional Info for Size/Weight/Color/SKU)\n\",";
			$line .= strtoupper($ITEM['grade']) . ",";
			$line .= "\"" . strtolower($ITEM['color']) . "\",";
			$line .= number_format($ITEM['costnf'],2,'.','') . ",";
			$line .= number_format($ITEM['costf'],2,'.','') . ",";
			$line .= number_format($ITEM['sell'],2,'.','') . ",";
			$i1 = strtoupper($ITEM['x1']);
			$i2 = strtoupper($ITEM['x2']);			
			$i1 = str_replace(".JPG",".jpg",$i1);
			$i2 = str_replace(".JPG",".jpg",$i2);			
			$line .= '"' . str_replace("/images/catalog","", str_replace("$HDIR","", $i1)) . "\","; //top view
			$line .= '"' . str_replace("/images/catalog","",str_replace("$HDIR","", $i2)) . "\",";
			//$line .= '""'; // . str_replace("/images/catalog","",str_replace("$HDIR","", $i2)) . "\",";
		$line .= '"' . $STORECATEGORY . "|" . stripslashes($ITEM['category']);// . "\",";
		
			list($pri,$sub1,$sub2,$sub3,$sub4) = @explode("|", stripslashes($ITEM['category']));
			$line .= "||$STORECATEGORY|$pri";
			if($sub1 != ""){
				$line .= "||$STORECATEGORY|$pri|$sub1";
			}
			if($sub2 !=""){
				$line .= "||$STORECATEGORY|$pri|$sub1|$sub2";
			}
			if($sub3 != ""){
				$line .= "||$STORECATEGORY|$pri|$sub1|$sub2|$sub3";
			}
			if($sub4 != ""){
				//$line .="||$pri|$sub1|$sub2|$sub3|$sub";
			}
			$line .= "\",";

			$line .= "1," . COSACCT . "," . SALESACCT . "," . INVACCT;
			
			if($ITEM['sqft'] >0){
				$cpsf = number_format($ITEM['sell'] / $ITEM['sqft'],2);
				$line .=",$cpsf,SQFT";
			}else{
				$line .=",,PALLET";
			}
			


			$line = trim($line,"\r\n\t");
			$line = str_replace("\n","",$line);

		//--------------------- QB ES Specific
			$qbline = "'OSSD'," . swapC($ITEM['SKU']) . ",\"" . stripslashes(swapC($ITEM['item_type'])) . "\",";
			$qbline .= swapC($ITEM['lot_no']) . ",";
			$qbline .= swapC($ITEM['pal_no']) . ",";
			$qbline .= $ITEM['width'] . ",";
			$qbline .= $ITEM['length'] . ",";
			$qbline .= $ITEM['height'] . ",";
			$qbline .= $ITEM['weight'] . ",";	
			$qbline .= $ITEM['sqft'] . ",";
			$qbline .= $ITEM['yard'] . ",";	
			$qbline .= $ITEM['zone'] . ",";	
			$qbline .= swapC($ITEM['item_location']) . ",";
			$qbline .= "\"".strip_tags(stripslashes(swapC($ITEM['description']))) . "\",";
			$qbline .= strtoupper($ITEM['grade']) . ",";
			$qbline .= "\"" . strtolower($ITEM['color']) . "\",";
			$qbline .= number_format($ITEM['costnf'],2,'.','') . ",";
			$qbline .= number_format($ITEM['costf'],2,'.','') . ",";
			$qbline .= number_format($ITEM['sell'],2,'.','') . ",";
			$i1 = strtoupper($ITEM['x1']);
			$i2 = strtoupper($ITEM['x2']);			
			$i1 = str_replace(".JPG",".jpg",$i1);
			$i2 = str_replace(".JPG",".jpg",$i2);			
			$qbline .= '"' . str_replace("/images/catalog","", str_replace("$HDIR","", $i1)) . "\","; //top view

			$qbline .= '"' . $pri . "\","; 

			$qbline .= "0," . COSACCT . "," . SALESACCT . "," . INVACCT . ",Tax";


			$qbline = trim($qbline,"\r\n\t");
			$qbline = str_replace("\n","",$qbline);
		//--------------			
			fputs($fp, $line . "\n");
			fputs($fp2, $qbline . "\n");
			$line = "";
			$qbline = "";
			}

			if($skip_post == 0){
				if(!$ur = mysqli_query($ossdb, "UPDATE item_data SET posted='Yes' WHERE SKU='$ITEM[SKU]'")){
					echo("Unable to set posted status in database...");
				}
			}
			
		}
		fclose($fp);
		fclose($fp2);
		
	}
	echo "<a href=\"" . str_replace(SITEHDIR,"", $export_file) . "\">Click Here to download CSV for importing into Magento via Store Manager PRO.</a><br />";
	echo "<a href=\"" . str_replace(SITEHDIR,"", $export_file_qb) . "\">Click Here to download CSV for importing into QuickBooks ES 2014</a>";
 }elseif($TYPE == "magento"){

 $LINEHEADER = "sku,_store,_attribute_set,_type,_category,_root_category,_product_websites,bin_loc,color,cost,costnf,country_of_manufacture,created_at,custom_design,custom_design_from,custom_design_to,custom_layout_update,description,enable_googlecheckout,freight_class,gallery,gift_message_available,grade,has_options,height,image,image_label,length,lot_no,manufacturer,media_gallery,meta_description,meta_keyword,meta_title,minimal_price,msrp,msrp_display_actual_price_type,msrp_enabled,must_ship_freight,name,news_from_date,news_to_date,options_container,page_layout,pallet_no,price,required_options,short_description,small_image,small_image_label,special_from_date,special_price,special_to_date,status,tax_class_id,thumbnail,thumbnail_label,updated_at,url_key,url_path,visibility,weight,width,yard,zone,qty,min_qty,use_config_min_qty,is_qty_decimal,backorders,use_config_backorders,min_sale_qty,use_config_min_sale_qty,max_sale_qty,use_config_max_sale_qty,is_in_stock,notify_stock_qty,use_config_notify_stock_qty,manage_stock,use_config_manage_stock,stock_status_changed_auto,use_config_qty_increments,qty_increments,use_config_enable_qty_inc,enable_qty_increments,is_decimal_divided,_links_related_sku,_links_related_position,_links_crosssell_sku,_links_crosssell_position,_links_upsell_sku,_links_upsell_position,_associated_sku,_associated_default_qty,_associated_position,_tier_price_website,_tier_price_customer_group,_tier_price_qty,_tier_price_price,_group_price_website,_group_price_customer_group,_group_price_price,_media_attribute_id,_media_image,_media_lable,_media_position,_media_is_disabled";

 		$fp = fopen($export_file,"w");
		fputs($fp,$LINEHEADER ."\n");
		
		foreach($cbs as $key=>$val){
			//echo $val . "<br />";
			if(getItem($val)){
			$line = swapC($ITEM['SKU']) . ",,OSSD,simple,"; 	// sku,_store,_attribute_set,_type
			$line .= swapC($ITEM['category']) . ",$root_category,";	// _category,_root_category
			$line .= "base," . swapC($ITEM['item_location']) . ",,$ITEM[costf],$ITEM[costnf],US,";		// _product-websites,color,cost,costnf,country_of_manufacture
			$line .= date("Y-m-d H:i:s") . ",default/modern,,,,";	// created_at,custom_design,custom_design_from,custom_design_to,custom_layout_update
			$line .= swapC($ITEM['description']) . ",1,70,,," . strtoupper($ITEM['grade']) . ",0,";	// description,enable_googlecheckout,freight_class,gallery,gift_message_available,grade,has_options
			$line .= $ITEM['height'] . ",";				// height
			$line .= str_replace("$HDIR/",SITEURL . "/", $ITEM['x2']) . ",,"; // image,image_label
			$line .= $ITEM['length'] . ",";				// length
			$line .= swapC($ITEM['lot_no']) . ",,,,,,,,Use config,Use config,1,";	// lot_no,manufacturer,media_gallery,meta_description,meta_keyword,meta_title,minimal_price,msrp,msrp_display_actual_price_type,msrp_enabled,must_ship_freight
			$line .= swapC($ITEM['item_type']) . ",,,Block after Info Column,,";		// name,news_from_date,news_to_date,options_container,page_layout
			$line .= $ITEM['pallet_no'] . ",";			// pallet_no,
			$line .= $ITEM['sell'] . ",";				// price
			$line .= "0," . swapC($ITEM['item_type']) . ",";		// required_options,short_description,
			$line .= str_replace("$HDIR/",SITEURL . "/", $ITEM['x1']) . ",,,,,";	// small_image,small_image_label,special_from_date,special_price,special_to_date
			$line .= "1,2,";					// status,tax_class_id,
			$line .= str_replace("$HDIR/",SITEURL . "/", $ITEM['x1']) . ",," . date("Y-m-d H:i:s") . ",";	// thumbnail,thumbnail_label,updated_at
			$line .= swapC($ITEM['SKU']) . "," . swapC($ITEM['SKU']) . ".html,4,";	// url_key,url_path,visibility
			$line .= $ITEM['weight'] . "," . $ITEM['width'] . "," . swapC($ITEM['yard']) . "," . swapC($ITEM['zone']) . ",1,0,1,0,0,0,0,0,0,0,";	// weight,width,qty,min_qty,use_config_min_qty,is_qty_decimal,backorders,use_config_backorders,min_sale_qty,use_config_min_sale_qty,max_sale_qty,use_config_max_sale_qty
			$line .= "1,0,0,1,";					// is_in_stock,notify_stock_qty,use_config_notify_stock_qty,manage_stock
			$line .= ",,,,,,,,,"; // use_config_manage_stock,stock_status_changed_auto,use_config_qty_increments,qty_increments,use_config_enable_qty_inc,enable_qty_increments,is_decimal_divided,_links_related_sku,
			$line .= ",,,,,,,,,,,,,,,,"; // _links_related_position,_links_crosssell_sku,_links_crosssell_position,_links_upsell_sku,_links_upsell_position,_associated_sku,_associated_default_qty,_associated_position,_tier_price_website,_tier_price_customer_group,_tier_price_qty,_tier_price_price,_group_price_website,_group_price_customer_group,_group_price_price,
			
			$line .= "," .  str_replace("$HDIR/",SITEURL . "/", $ITEM['x1']) . ",,1,0\n";	// _media_attribute_id,_media_image,_media_lable,_media_position,_media_is_disabled
			
			fputs($fp, $line);
			$line = "";
			}
			
			
		}
		fclose($fp);
	echo "<a target=\"_blank\" href=\"" . str_replace("$HDIR/",SITEURL . "/", $export_file) . "\">Click Here to download CSV for importing into Magento.</a>";		
}else{
	echo "No export type set...";
}
 
 $sm = popen ("/usr/sbin/sendmail -t", "w");
	if($sm){
		fputs($sm, "X-Mailer: YOUR STONE CO WMS v2.0 [en]\n"); 
		fputs($sm, "MIME-Version: 1.0\n");
		fputs($sm, "Content-type: text/html; charset=utf-8\n");
		fputs($sm, "Reply-to: " . SITEEMAIL . "\n");
		fputs($sm, "From: " . SITEEMAIL . "\n");
		fputs($sm, "To: wms@yourdomain.com\n");
		fputs($sm, "Subject: Items Posted from" . SITENAME . "\n\n");
		fputs($sm, "$export_file\n$export_file_qb\n.");
		pclose($sm);
	}
 
}

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