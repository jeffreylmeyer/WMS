<?php
// common funtions

//-------------------------------------------------
// get categories from magento export
function getCategories($ccat="")
{
	
	$catfile = fopen("categories.txt","r");
	if($catfile){
		while(!feof($catfile)){
			$buf = fgets($catfile,1024);
			$buf = trim($buf,"\r\n\t");
			
			if($buf != ""){
				if($ccat == $buf){
					echo "<option selected value=\"" . $buf . "\">$buf</option>\n";
				}else{
					echo "<option value=\"" . $buf . "\">$buf</option>\n";
				}
			}
		}
	}
	fclose($catfile);
}

//-----------------------------------------
function getZones($selyard="",$szone="")
{
	global $ossdb;

		
	$sqlcmd = "SELECT DISTINCT zone FROM bin_location_map WHERE yard='$selyard' ORDER BY zone ASC";
	if(!$srs = mysqli_query($ossdb, $sqlcmd)){
		exit("ERROR: " . mysqli_error($ossdb));
	}
	$rows = mysqli_num_rows($srs);
	if($rows > 0){
		while($rows > 0){
			$srd = mysqli_fetch_assoc($srs);
			if($srd['zone'] == $szone){
				echo "<option selected value=\"" . $srd['zone'] . "\">$srd[zone]</option>\n";
			}else{
				echo "<option value=\"" . $srd['zone'] . "\">$srd[zone]</option>\n";
			}
			$rows--;
		}
		mysqli_free_result($srs);
	}

}
//-----------------------------------------
function getYards($syard)
{
	global $ossdb;
	
	$sqlcmd = "SELECT DISTINCT yard FROM bin_location_map ORDER BY yard ASC";
	if(!$srs = mysqli_query($ossdb, $sqlcmd)){
		exit("ERROR: " . mysqli_error($ossdb));
	}
	$rows = mysqli_num_rows($srs);
	if($rows > 0){
		while($rows > 0){
			$srd = mysqli_fetch_assoc($srs);
			if($srd['yard'] == $syard){
				echo "<option selected value=\"" . $srd['yard'] . "\">$srd[yard]</option>\n";
			}else{
				echo "<option value=\"" . $srd['yard'] . "\">$srd[yard]</option>\n";
			}
			$rows--;
		}
		mysqli_free_result($srs);
	}

}
//--------------------------------------------------
function getEntityID($product_id)
{
	global $ossdb;
	
	$entity_id = 0;
	
	$sqlcmd = "SELECT entity_id FROM catalog_product_entity WHERE sku='$product_id'";
	if(!$srs = mysqli_query($ossdb, $sqlcmd)){
		exit("ERROR: " . mysqli_error($ossdb));
	}
	$rows = mysqli_num_rows($srs);
	if($rows >0){
		$srd = mysqli_fetch_row($srs);
		$entity_id = $srd[0];
		mysqli_free_result($srs);
	}
	return $entity_id;
}
//-----------------------------------------------------
function getOptionID($cur_val)
{
	global $ossdb;
	
	$option_id = -1;
	
	if(!$ors = mysqli_query($ossdb, "SELECT option_id FROM eav_attribute_option_value WHERE value='$cur_val' LIMIT 1")){
		exit("ERROR: " .mysqli_error($ossdb) . "<br /><br /> SELECT option_id FROM eav_attribute_option_value WHERE value='$cur_val' LIMIT 1");
	}
	$rows = mysqli_num_rows($ors);
	if($rows > 0){
		$ord = mysqli_fetch_assoc($ors);
		$option_id = $ord['option_id'];
		mysqli_free_result($ors);
	}
	return $option_id;
	
}
//-----------------------------------------------------
function getItemFromTemp($product_id)
{
	global $ossdb;
	//catalog_product_entity_varchar

	$entity_id = 0;
	
	$sqlcmd = "SELECT * FROM item_data WHERE sku='$product_id'";
	
	if(!$srs = mysqli_query($ossdb, $sqlcmd)){
		exit("ERROR: " . mysqli_error($ossdb));
	}
	$rows = mysqli_num_rows($srs);
	if($rows >0){
		$srd = mysqli_fetch_assoc($srs);
		mysqli_free_result($srs);
	}else{
		
		return array();
	}
	
	if(!empty($srd)){
	$ItemD['bin_loc'] = $srd['item_location'];
	$ItemD['bin_loc2'] = "";
	$ItemD['bin_loc3'] = "";
	
	if(strpos($ItemD['bin_loc'],"|")!==false){
		if(substr_count($ItemD['bin_loc'],"|") > 1){
			list($ItemD['bin_loc'],$ItemD['bin_loc2'],$ItemD['bin_loc3']) = explode("|",$ItemD['bin_loc']);
		}elseif(substr_count($ItemD['bin_loc'],"|") > 0){
			list($ItemD['bin_loc'],$ItemD['bin_loc2']) = explode("|",$ItemD['bin_loc']);
		}
	}
	
	$ItemD['costnf'] = $srd['costnf'];
	$ItemD['costf'] = $srd['costf'];
	$ItemD['description'] = $srd['description'];
	$ItemD['freight_class'] = 70;
	$ItemD['grade'] = $srd['grade'];
	$ItemD['height'] = $srd['height'];
	$ItemD['length'] = $srd['length'];
	$ItemD['lot_no'] = $srd['lot_no'];
	$ItemD['name'] = $srd['item_type'];
	$ItemD['pallet_no'] = $srd['pal_no'];
	$ItemD['price'] = $srd['sell'];
	$ItemD['short_description'] = $srd['item_type'];
	$ItemD['sku'] = $srd['SKU'];
	$ItemD['barcode'] = $srd['SKU'];
	$ItemD['image'] = $srd['x1'];
	$ItemD['small_image'] = $srd['x2'];
	$ItemD['weight'] = $srd['weight'];
	$ItemD['width'] = $srd['width'];
	$ItemD['yard'] = $srd['yard'];
	$ItemD['zone'] = $srd['zone'];
	$ItemD['color'] = $srd['color'];
	}else{
		$ItemD = array();
	}
	return $ItemD;
}

//-------------------------------------------------
function getItem($product_id,$codes=0)
{
	global $ossdb;
	//catalog_product_entity_varchar

	$entity_id = 0;
	return getItemFromTemp($product_id);

	$EntityD = array();
	$ItemD = array();



return $EntityD;
/*
	$ItemD['bin_loc'] = $EntityD[149];
	$ItemD['costnf'] = $EntityD["134"];
	$ItemD['cost'] = $EntityD["79"];
	$ItemD['description'] = $EntityD["72"];
	$ItemD['freight_class'] = $EntityD["140"];
	$ItemD['grade'] = getEAVAttribText($EntityD["146"]);
	$ItemD['height'] = $EntityD["137"];
	$ItemD['length'] = $EntityD["136"];
	$ItemD['lot_no'] = $EntityD["138"];
	$ItemD['name'] = $EntityD["71"];
	$ItemD['pallet_no'] = $EntityD["139"];
	$ItemD['price'] = $EntityD["75"];
	$ItemD['short_description'] = $EntityD["73"];
	$ItemD['sku'] = $EntityD["74"];
	$ItemD['barcode'] = $EntityD["74"];
	$ItemD['small_image'] = $EntityD["86"];
	$ItemD['weight'] = $EntityD["80"];
	$ItemD['width'] = $EntityD["135"];
	$ItemD['yard'] = getEAVAttribText($EntityD["147"]);
	$ItemD['zone'] = $EntityD["148"];
	return $ItemD;
	*/
}
//------------------------------
function getEAVAttribText($aid)
{
	global $ossdb;
	if(!$rs = mysqli_query($ossdb, "SELECT value FROM eav_attribute_option_value WHERE option_id='$aid'")){
		return "";
	}else{
		$rd = mysqli_fetch_assoc($rs);
		mysqli_free_result($rs);
		return $rd['value'];
	}

}
//---------------------------------------------
function getAttribText($aid)
{
	global $ossdb;
	if(!$rs = mysqli_query($ossdb, "SELECT frontend_label,attribute_code FROM eav_attribute WHERE attribute_id='$aid'")){
		return "";
	}else{
		$rd = mysqli_fetch_assoc($rs);
		mysqli_free_result($rs);
		return $rd['attribute_code'];// . "]" . $rd['frontend_label'];
		//return $rd['frontend_label'];
	}

}

//-------------------------------------------------
function saveItem($sku,$IA)
{
	global $ossdb;
	
	//mysqli_query($ossdb, "USE oss_temp");
	
	
	
	$sqlcmd = "UPDATE item_data SET ";
	foreach($IA as $key=>$val){
		$sqlcmd .= "$key='" . mysqli_real_escape_string($ossdb, $val) . "',";
	}
	$sqlcmd = rtrim($sqlcmd,",");
	$sqlcmd .= " WHERE SKU='$sku'";
	
	if(!$sr = mysqli_query($ossdb,$sqlcmd)){
		exit("ERROR: " . mysqli_error($ossdb));
	}

}
//--------------------------------------------------------------------------
function updateItemLocation($IA,$sku)
{
	global $ossdb;
	
	if(strpos($IA['bin_loc'],"|")===false){
	if(trim($IA['bin_loc2'])!=""){
		$IA['bin_loc'].="|".$IA['bin_loc2'];
		}
	if(trim($IA['bin_loc3'])!=""){
		$IA['bin_loc'].="|".$IA['bin_loc3'];
		}
		
	}
	$sqlcmd = "UPDATE item_data SET ";
	$sqlcmd .= "zone='$IA[zone]',item_location='$IA[bin_loc]'";


	$sqlcmd .= " WHERE SKU='$sku'";
	
	if(!$sr = mysqli_query($ossdb,$sqlcmd)){
		exit("ERROR: " . mysqli_error($ossdb));
	}

}

//-----------------------------------------------------
function confirmPalletLocT($ploc)
{
	global $ossdb;
	
	$sqlcmd = "SELECT status FROM bin_location_map WHERE yard='".DEFAULTYARD."' AND bin_loc='" . mysqli_real_escape_string($ossdb,$ploc) . "'";
	if(!$rs = mysqli_query($ossdb, $sqlcmd)){
		exit("ERROR: " . mysqli_error($ossdb));
	}
	$rows = mysqli_num_rows($rs);
	$status = "";
	if($rows > 0){
		$rd = mysqli_fetch_row($rs);
		$status = $rd[0];
		mysqli_free_result($rs);
	}
 return $status;
}
//----------------------------------------------------------------
function showSlots($syard="",$cur_loc = "")
{
	global $ossdb,$ITEM;
	if($cur_loc == "*"){
		$sqlcmd = "SELECT * FROM bin_location_map WHERE yard='$syard' AND status ='avail' OR (status='full' AND item_ref='$ITEM[barcode]') ORDER BY recid ASC";
	}else{		
		$sqlcmd = "SELECT * FROM bin_location_map WHERE yard='$syard' AND status ='avail' ORDER BY recid ASC";
	}
	if(!$rs = mysqli_query($ossdb, $sqlcmd)){
		exit("ERROR: Unable to pull location data - " . mysqli_error($ossdb));
	}
	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		while($rows > 0){
			$rd = mysqli_fetch_assoc($rs);
			if($rd['bin_loc'] == $cur_loc){
			}else{
				echo "<option value=\"$rd[bin_loc]\">$rd[bin_loc]</option>\n";
			}
			$rows--;
		}
		mysqli_free_result($rs);
	}

	//@mysqli_close($ossdb);
}

//----------------------------------------------------------------
function MoveOrder_showSlots($syard="",$zone, $cur_loc = "")
{
	global $ossdb,$ITEM;
	$avail = 0;
	
	$sqlcmd = "SELECT * FROM bin_location_map WHERE yard='$syard' AND zone='$zone' AND status ='avail' ORDER BY recid ASC";

	if(!$rs = mysqli_query($ossdb, $sqlcmd)){
		exit("ERROR: Unable to pull location data - " . mysqli_error($ossdb));
	}
	$rows = mysqli_num_rows($rs);
	$avail = $rows;
	if($rows > 0){
		while($rows > 0){
			$rd = mysqli_fetch_assoc($rs);
			
			echo "<option value=\"$rd[bin_loc]\">$rd[bin_loc]</option>\n";
			
			$rows--;
		}
		mysqli_free_result($rs);
	}

	return $avail;
}

//---------------------------------------------------------------
function logItemTransaction($transtype="",$IA)
{
	global $ossdb;
	return 1;
	
	
	$sqlcmd = "INSERT INTO item_transaction_log SET trans_type='$transtype',trans_date='" . date("Y-m-d H:i:s") . "',";
	
	foreach($IA as $key=>$val){
		$sqlcmd .= $key . "='" . mysqli_real_escape_string($ossdb, $val) . "',";
	}
	$sqlcmd = rtrim($sqlcmd,",");
	
	if(!$rs = mysqli_query($ossdb, $sqlcmd)){
		exit("ERROR: " . mysqli_error($ossdb) . "<br /><br />$sqlcmd");
	}
	return 1;
}

//-----------------------------------------------------------------------------
function sendMessage($IA,$message_type)
{
	global $ossdb;
	
	$subject = "";
	
	if(!isset($IA['bin_loc']) || $IA['bin_loc'] == ""){
		$IA['bin_loc'] = isset($IA['item_location']) ? $IA['item_location'] :"";
	}
	if($message_type == "MOVE"){
		$subject = "ITEM " . $IA['barcode'] . " MOVED";
		$amsg = $IA['barcode'] . " - Item has been moved by " . SITENAME . ".<br /><br />\n\n";
		$amsg .= "From: " . $IA['yard'] . " : " . $IA['zone'] . " : " . $IA['bin_loc'] . "<br />\n";
		$amsg .= "To New Location: " . $IA['newyard'] . " : " . $IA['newzone'] . " : " . $IA['newbin_loc'] . "<br />\n";
	}elseif($message_type == "XFER"){
		$subject = "ITEM " . $IA['barcode'] . " TRANSFER";
		$amsg = $IA['barcode'] . " - Item is being Transferred by " .  SITENAME . ".<br /><br />\n\n";
		$amsg .= "From: " . $IA['yard'] . " : " . $IA['zone'] . " : " . $IA['bin_loc'] . "<br />\n";
		$amsg .= "To New Location: " . $IA['newyard'] . " : " . $IA['newzone'] . " : " . $IA['newbin_loc'] . "<br />\n";
	}

		foreach($IA as $key=>$val){
			$amsg .= $key . " : " . $val . "<br />\n";
		}
			
//	$mailheader = "";
//	$mailheader = "MIME-Version: 1.0\n";
//	$mailheader .= "Content-type: text/html;\n";
//	$mailheader .= "CC: sales@overstockstone.com\n";

//	$mailheader .= "From: \"Lake City WMS\"<lcflwms@lcflwms.overstockstone.com>\n"; 

//	mail("it@overstockstone.com", $subject, $amsg, $mailheader);

	$sm = popen ("/usr/sbin/sendmail -t", "w");
	if($sm){
		fputs($sm, "X-Mailer: YOUR STONE CO. WMS v2.0 [en]\n"); 
		fputs($sm, "MIME-Version: 1.0\n");
		fputs($sm, "Content-type: text/html; charset=utf-8\n");
		fputs($sm, "Reply-to: " . SITEEMAIL . "\n");
		fputs($sm, "From: " . SITEEMAIL . "\n");
		fputs($sm, "To: wms@yourdomain.com\n");
		fputs($sm, "Subject: $subject\n\n");
		fputs($sm, "$amsg\n.");
		pclose($sm);
	}
	return 1;
}

function ImageCreateFromBMP($filename)
{
	//Create image from the BMP file
	//RETURN: = Image handle, or
	///		  = false if error
	if(!$f1 = fopen($filename, "rb"))
		return false;

	$FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1, 14));
	if($FILE['file_type'] != 19778)
		return false;

	$BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
		'/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
		'/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1, 40));

	$BMP['colors'] = pow(2, $BMP['bits_per_pixel']);

	if($BMP['size_bitmap'] == 0)
		$BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];

	$BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
	$BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
	$BMP['decal'] = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
	$BMP['decal'] -= floor($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
	$BMP['decal'] = 4 - (4 * $BMP['decal']);
	if($BMP['decal'] == 4)
		$BMP['decal'] = 0;

	$PALETTE = array();
	if($BMP['colors'] < 16777216)
		$PALETTE = unpack('V'.$BMP['colors'], fread($f1, $BMP['colors'] * 4));

	$IMG = fread($f1, $BMP['size_bitmap']);
	$VIDE = chr(0);

	$res = imagecreatetruecolor($BMP['width'], $BMP['height']);
	$P = 0;
	$Y = $BMP['height'] - 1;
	while($Y >= 0)
	{
		$X=0;
		while($X < $BMP['width'])
		{
			switch($BMP['bits_per_pixel'])
			{
			case 32:
				$COLOR = unpack("V", substr($IMG, $P, 4));
				break;
			case 24:
				$COLOR = unpack("V", substr($IMG, $P, 3).$VIDE);
				break;
			case 16:
				$COLOR = unpack("n", substr($IMG, $P, 2));
				$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				break;
			case 8:
				$COLOR = unpack("n", $VIDE.substr($IMG, $P, 1));
				$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				break;
			case 4:
				$COLOR = unpack("n", $VIDE.substr($IMG, floor($P), 1));
				if(($P * 2) % 2 == 0)
					$COLOR[1] = ($COLOR[1] >> 4);
				else
					$COLOR[1] = ($COLOR[1] & 0x0F);
				$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				break;
			case 1:
				$COLOR = unpack("n", $VIDE.substr($IMG, floor($P), 1));
				switch(($P * 8) % 8)
				{
				case 0:
					$COLOR[1] = $COLOR[1] >> 7;
					break;
				case 1:
					$COLOR[1] = ($COLOR[1] & 0x40) >> 6;
					break;
				case 2:
					$COLOR[1] = ($COLOR[1] & 0x20) >> 5;
					break;
				case 3:
					$COLOR[1] = ($COLOR[1] & 0x10) >> 4;
					break;
				case 4:
					$COLOR[1] = ($COLOR[1] & 0x8) >> 3;
					break;
				case 5:
					$COLOR[1] = ($COLOR[1] & 0x4) >> 2;
					break;
				case 6:
					$COLOR[1] = ($COLOR[1] & 0x2) >> 1;
					break;
				case 7:
					$COLOR[1] = $COLOR[1] & 0x1;
					break;
				}

				$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				break;
			default:
				return false;
				break;
			}

			imagesetpixel($res, $X, $Y, $COLOR[1]);

			$X++;
			$P += $BMP['bytes_per_pixel'];
		}

		$Y--;
		$P += $BMP['decal'];
	}

	fclose($f1);

	return $res;
}

//=========================================================
// new functions from category admin


//------------------------------------------------------------------------------
function getColorList($COL="")
{
	global $ossm;
	

$color= array();
if(strpos($COL,",")!==false){
	$color = explode(",", $COL);
}else{
 $color[] = $COL;
}



	if(!$rs = mysqli_query($ossm, "SELECT * FROM categories.colors ORDER BY color ASC")){
		exit("ERROR: " . mysqli_error($ossm));
	}

	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		while($rows > 0){
			$rd = mysqli_fetch_assoc($rs);
			if(in_array(stripslashes($rd['color']),$color)){
				echo "<input class='cbs' type='checkbox' name=\"color[]\" checked='checked' value='".urlencode(stripslashes($rd['color']))."' /><small>".stripslashes($rd['color'])."</small>&nbsp;&nbsp;";
			}else{
				echo "<input class='cbs' type='checkbox' name=\"color[]\"  value='".urlencode(stripslashes($rd['color']))."' /><small>".stripslashes($rd['color'])."</small>&nbsp;&nbsp;";				
			}
			$rows--;
		}
		mysqli_free_result($rs);
	}

}

function getKeywordList($pcat)
{
	global $ossm;
	
	$KWDS = "";
	
	if(!$rs = mysqli_query($ossm, "SELECT * FROM categories.projects WHERE keyname='" . strtolower($pcat) . "'")){
		exit("ERROR: " . mysqli_error($ossm));
	}

	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		while($rows > 0){
			$rd = mysqli_fetch_assoc($rs);
			$KWDS = stripslashes($rd['keylist']);
			$rows--;
		}
		mysqli_free_result($rs);
	}
	return $KWDS;
}

//----------------------------------------------------------------
function getKeywordSelectList()
{
	global $ossm;
	
	$KWDS = "";
	
	if(!$rs = mysqli_query($ossm, "SELECT * FROM categories.projects")){
		exit("ERROR: " . mysqli_error($ossm));
	}

	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		while($rows > 0){
			$rd = mysqli_fetch_assoc($rs);
			$KWDS .= "<option value=\"".urlencode(stripslashes($rd['keyname'])) . "\">$rd[keylist]</option>\n";
			$rows--;
		}
		mysqli_free_result($rs);
	}
	return $KWDS;
}
//------------------------------------------------------------------------------
function getPrimary()
{
	global $ossm;
	
	$PRIMARY = array();
	
	if(!$rs = mysqli_query($ossm, "SELECT * FROM categories.primary ORDER BY catname ASC")){
		exit("ERROR: " . mysqli_error($ossm));
	}

	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		while($rows > 0){
			$rd = mysqli_fetch_assoc($rs);
			$PRIMARY[] = stripslashes($rd['catname']);		
			$rows--;
		}
		mysqli_free_result($rs);
	}
	return $PRIMARY;
}

//-----------------------------------------------------
function getSubcat1($P)
{
	global $ossm;
	
	$SUBCAT1 = array();
	
	if(!$rs = mysqli_query($ossm, "SELECT * FROM categories.subcat_l1 WHERE pri_cat='$P' ORDER BY sname ASC")){
		exit("ERROR: " . mysqli_error($ossm));
	}

	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		while($rows > 0){
			$rd = mysqli_fetch_assoc($rs);
			$SUBCAT1[] = stripslashes($rd['sname']);		
			$rows--;
		}
		mysqli_free_result($rs);
	}
	return $SUBCAT1;
}


//-----------------------------------------------------
function getSubcat2($P1,$S1)
{
	global $ossm;
	
	$SUBCAT2 = array();
	
	if(!$rs = mysqli_query($ossm, "SELECT * FROM categories.subcat_l2 WHERE pri_cat='$P1' AND sub_cat='$S1' ORDER BY s2name ASC")){
		exit("ERROR: [getSubcat2($P1,$S1)] " . mysqli_error($ossm));
	}

	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		while($rows > 0){
			$rd = mysqli_fetch_assoc($rs);
			$SUBCAT2[] = stripslashes($rd['s2name']);		
			$rows--;
		}
		mysqli_free_result($rs);
	}
	return $SUBCAT2;
}

//-----------------------------------------------------
function getSubcat3($P,$S,$S2)
{
	global $ossm;
	
	$SUBCAT3 = array();
	
	if(!$rs = mysqli_query($ossm, "SELECT * FROM categories.subcat_l3 WHERE pri_cat='$P' AND sub_cat='$S' AND sub_cat2='$S2' ORDER BY s3name ASC")){
		exit("ERROR: " . mysqli_error($ossm));
	}

	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		while($rows > 0){
			$rd = mysqli_fetch_assoc($rs);
			$SUBCAT3[] = stripslashes($rd['s3name']);		
			$rows--;
		}
		mysqli_free_result($rs);
	}
	return $SUBCAT3;
}

//-----------------------------------------------------
function getSubcat4($P,$S,$S2,$S3)
{
	global $ossm;
	
	$SUBCAT4 = array();
	
	if(!$rs = mysqli_query($ossm, "SELECT * FROM categories.subcat_l4 WHERE pri_cat='$P' AND sub_cat='$S' AND sub_cat2='$S2' AND sub_cat3='$S3' ORDER BY s4name ASC")){
		exit("ERROR: " . mysqli_error($ossm));
	}

	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		while($rows > 0){
			$rd = mysqli_fetch_assoc($rs);
			$SUBCAT4[] = stripslashes($rd['s4name']);		
			$rows--;
		}
		mysqli_free_result($rs);
	}
	return $SUBCAT4;
}



//-------------------------------
function getPrimaryIDX($P)
{
	global $ossm;
	$P = rtrim($P,"\r\n\t ");

	$PRI = 0;
	
	if(!$rs = mysqli_query($ossm, "SELECT recid FROM categories.primary WHERE catname='" . mysqli_real_escape_string($ossm,$P) . "'")){
		exit("ERROR: [getPrimaryIDX($P)] " . mysqli_error($ossm));
	}

	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		$rd = mysqli_fetch_assoc($rs);
		$PRI = $rd['recid'];		

		mysqli_free_result($rs);
	}
	return $PRI;
}

//-------------------------------
function getSubcat1IDX($P1,$S1)
{
	global $ossm;
	$S1 = rtrim($S1,"\r\n\t ");

	$PRI = -1;
	
	if(!$rs = mysqli_query($ossm, "SELECT * FROM categories.subcat_l1 WHERE sname='" . mysqli_real_escape_string($ossm,$S1) . "' AND pri_cat='$P1'")){
		exit("ERROR: [getSubcat1IDX($S1)] " . mysqli_error($ossm));
	}

	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		$rd = mysqli_fetch_assoc($rs);
		$PRI = $rd['recid'];		

		mysqli_free_result($rs);
	}
	return $PRI;
}

//-------------------------------
function getSubcat2IDX($P1,$S1,$S2)
{
	global $ossm;
	$S2 = rtrim($S2,"\r\n\t ");

	$PRI = 0;
	
	if(!$rs = mysqli_query($ossm, "SELECT recid FROM categories.subcat_l2 WHERE s2name='" . mysqli_real_escape_string($ossm,$S2) . "' AND sub_cat='$S1' AND pri_cat='$P1' ")){
		exit("ERROR: [getSubcat2IDX($S1,$P1)] " . mysqli_error($ossm));
	}

	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		$rd = mysqli_fetch_assoc($rs);
		$PRI = $rd['recid'];		

		mysqli_free_result($rs);
	}
	return $PRI;
}

//-------------------------------
function getSubcat3IDX($P1,$S1,$S2,$S3)
{
	global $ossm;
	$S3 = rtrim($S3,"\r\n\t ");

	$PRI = 0;
	
	if(!$rs = mysqli_query($ossm, "SELECT recid FROM categories.subcat_l3 WHERE s3name='" . mysqli_real_escape_string($ossm,$S3) . "' AND sub_cat='$S1' AND pri_cat='$P1' AND sub_cat2='$S2'")){
		exit("ERROR: [getSubcat3IDX($P1,$S1,$S2,$S3)] " . mysqli_error($ossm));
	}

	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		$rd = mysqli_fetch_assoc($rs);
		$PRI = $rd['recid'];		

		mysqli_free_result($rs);
	}
	return $PRI;
}

//-------------------------------
function getSubcat4IDX($P1,$S1,$S2,$S3,$S4)
{
	global $ossm;
	$S4 = rtrim($S4,"\r\n\t ");

	$PRI = 0;
	
	if(!$rs = mysqli_query($ossm, "SELECT recid FROM categories.subcat_l4 WHERE s4name='" . mysqli_real_escape_string($ossm,$S4) . "' AND sub_cat='$S1' AND pri_cat='$P1' AND sub_cat2='$S2' AND sub_cat3='$S3'")){
		exit("ERROR: [getSubcat4IDX($P1,$S1,$S2,$S3,$S4)] " . mysqli_error($ossm));
	}

	$rows = mysqli_num_rows($rs);
	if($rows > 0){
		$rd = mysqli_fetch_assoc($rs);
		$PRI = $rd['recid'];		

		mysqli_free_result($rs);
	}
	return $PRI;
}
?>
