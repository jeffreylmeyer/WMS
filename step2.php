<?php
//-------------------------------------------------
// image upload to XXXXwms.yourdomain.com
// created: 30-OCT-2013 by Jeffrey L Meyer
//----------------------------------------------------

require_once("wmsconfig.php");

$sb = isset($_REQUEST['sb']) ? $_REQUEST['sb'] : 0;
$text = isset($_REQUEST['text']) ? $_REQUEST['text'] : "MISC";
$yard = isset($_REQUEST['yard']) ? $_REQUEST['yard'] : DEFAULTYARD;
$weight = isset($_REQUEST['weight']) ? $_REQUEST['weight'] : "";
$lot_no = isset($_REQUEST['lot_no']) ? $_REQUEST['lot_no'] : "";
$pal_no = isset($_REQUEST['pal_no']) ? $_REQUEST['pal_no'] : "";
$barcode = isset($_REQUEST['barcode']) ? $_REQUEST['barcode'] : "";
$category = isset($_REQUEST['category']) ? $_REQUEST['category'] : "";

$lot_no = trim($lot_no,"\r\n\t ");

$HDIR = SITEHDIR;


// get first 2 characters of lot number to use for
// first 2 characters of image file names (stupid magento crap)

$lot1 = substr($lot_no,0,1);
$lot2 = substr($lot_no,1,1);

$uploaddir = UPLOADDIR; //"/home/wms/www/images/catalog";


if(strlen($lot_no) > 0){
	$uploaddir .= "/" . strtoupper($lot1) ;
	if(!is_dir($uploaddir)){
		mkdir($uploaddir, 0777);
		@chmod($uploaddir,0777);
	}
}

if(strlen($lot_no) > 1){
	$uploaddir .= "/" . strtoupper($lot2);
	if(!is_dir($uploaddir)){
		mkdir($uploaddir, 0777);
		@chmod($uploaddir,0777);
	}
}

if(!is_dir($uploaddir)){
	mkdir($uploaddir, 0777);
	@chmod($uploaddir,0777);
}



$CATS = array();
$msg = "";

if(!is_dir($uploaddir)){
	//create it
	mkdir($uploaddir,0777);
	@chmod($uploaddir,0777);	
}

//$uploaddir .= "/";

if (is_dir($uploaddir)) {
    if ($dh = opendir($uploaddir)) {
        while (($file = readdir($dh)) !== false) {
            //echo "filename: $file : filetype: " . filetype($dir . $file) . "\n";
            if(strpos($file, ".")===false && strpos($file,"..")===false && $file !=""){
            	$CATS[] = trim($file);
            }
        }
        closedir($dh);
    }else{
    	echo "Directory not opened: $uploaddir<br />";
    }
}else{
	echo "Directory not found: $uploaddir<br />";
}

if($sb == 0){
	sendForm("");
	exit(0);
}else{
	//process
	if($barcode == ""){
		sendForm("ERROR: BARCODE MISSING - Retry");
		exit(0);
	}
}


$err = 0;


if(isset($_FILES['topfile']['name']) && $_FILES['topfile']['name'] !=""){
list($fn, $ext) = explode(".", basename($_FILES['topfile']['name']));
$topfile = $uploaddir .  "/". strtoupper($lot_no)  . "-" . $barcode . "T." . strtolower($ext); //basename($_FILES['topfile']['name']);

echo "<div id='picrect' style='width:500px;'>";

if(move_uploaded_file($_FILES['topfile']['tmp_name'], $topfile)) {
    echo "<div style='width:220px;height:150px;display:inline;float:left'>
    	<b>Top View File<br />successfully uploaded.</b><br />
    	<img width=\"100\" src=\"https://wms.yourdomain.com/images/catalog/$lot1/$lot2" . str_replace($uploaddir,"",$topfile) . "\" />
    	</div>
    	";
} else {
    echo "Possible top view file error. Contact I.T. support immediately!<br />";
}
}else{
	$err = 1;
	$msg .= "Top View not selected.";
}

if(isset($_FILES['sidefile']['name']) && $_FILES['sidefile']['name'] !=""){
list($fn, $ext) = explode(".", basename($_FILES['sidefile']['name']));
$sidefile = $uploaddir .  "/" . strtoupper($lot_no) . "-" . $barcode . "S." . strtolower($ext); //basename($_FILES['sidefile']['name']);

if(move_uploaded_file($_FILES['sidefile']['tmp_name'], $sidefile)) {
    echo "<div style='width:220px;height:150px;display:inline;float:left'>
    	<b>Side View File<br />successfully uploaded.</b><br />
    	<img width=\"100\" src=\"https://wms.yourdomain.com/images/catalog/$lot1/$lot2" . str_replace($uploaddir,"",$sidefile) . "\" />
    	</div>
    	";
} else {
    echo "Possible side view file error. Contact I.T. support immediately!<br />";
}
}else{
	$err = 1;
	$msg.= "<br />Side View not selected";
}


echo "</div><br clear=\"all\" />";

if($err == 1){
	sendForm($msg);
	exit(0);
}

	
require_once("db_connect.php");
$ossdb = oss_connect();

if($err == 0){
	$sqlcmd = "UPDATE item_data SET x1='" . mysqli_real_escape_string($ossdb,str_replace("$HDIR/images/catalog","",$topfile)) . "', x2='" . mysqli_real_escape_string($ossdb,str_replace("$HDIR/images/catalog","",$sidefile)) . "' ";
	$sqlcmd .= "WHERE SKU = '$barcode'";
	if(!$rs = mysqli_query($ossdb, $sqlcmd)){
		echo "ERROR: " . mysqli_error($ossdb);
		exit(0);
	}
}

@mysqli_close($ossdb);


	list($nil,$p1,$p2,$img) = explode('/', $topfile);
		
		if(!is_dir(SITEHDIR . "/images/catalog/$p1/$p2/thumbs")){
			@mkdir(SITEHDIR ."/images/catalog/$p1/$p2/thumbs",0777);
		}
		
		$thumb = SITEHDIR . "/images/catalog/$p1/$p2/thumbs/" . $img;
		$timg = "images/catalog/$p1/$p2/thumbs/" . $img;
		
		if(file_exists($thumb)){
			@unlink($thumb);
		}

		$im= thumbnail($topfile,100); //SITEHDIR ."/images/catalog" . 
		imageToFile($im, $timg);

	list($nil,$p1,$p2,$img) = explode('/', $sidefile);
		
		if(!is_dir(SITEHDIR . "/images/catalog/$p1/$p2/thumbs")){
			@mkdir(SITEHDIR ."/images/catalog/$p1/$p2/thumbs",0777);
		}
		
		$thumb = SITEHDIR . "/images/catalog/$p1/$p2/thumbs/" . $img;
		$timg = "images/catalog/$p1/$p2/thumbs/" . $img;
		
		if(file_exists($thumb)){
			@unlink($thumb);
		}

		$im= thumbnail($sidefile,100); //SITEHDIR ."/images/catalog" . 
		imageToFile($im, $timg);
			
			
echo "<div style='clear:both;position:relative;margin-top:10px;height:120px;display:block;width:500px;float:left'>If you get successfully uploaded for both files, <br />you're done with this screen. <br />
<br />
<b>If upload failed, <a href=\"step2.php?text=$text&weight=$weight&yard=$yard&lot_no=$lot_no&pal_no=$pal_no&barcode=$barcode&category=$category\">click here to retry.</a></b>
<br />
<br />
	<a href=\"step3.php?item=$barcode&text=$text&weight=$weight&lot_no=$lot_no&pal_no=$pal_no\" target=\"_blank\"><img src=\"images/label-btn.jpg\" /></a><br />
	<b>Close label window after printing to return here...</b>

<br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
<a href=\"step1.php\">Click here to process another item</a>
</div>
";

//--------------------------------------------------------
function sendForm($msg="")
{
	global $CATS,$err,$barcode,$text,$weight,$lot_no,$pal_no,$category;

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

<div class="wmsmain">
<h2 style="color:#FF0000"><? echo $msg; ?></h2>Product ID: <?echo $barcode; ?><br />
<b>Take Top View and Side View Pictures and save to disk. <br />
Make sure you know which ones they are!</b>
<br />
<form enctype="multipart/form-data" action="step2.php" method="POST">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
    <input type="hidden" name="sb" value="1" />
    <input type="hidden" name="text" value="<? echo $text; ?>" />
        <input type="hidden" name="lot_no" value="<? echo $lot_no; ?>" />
        <input type="hidden" name="weight" value="<? echo $weight; ?>" />
        <input type="hidden" name="pal_no" value="<? echo $pal_no; ?>" />
        <input type="hidden" name="category" value="<? echo $category; ?>" />

    Top View PIC: <input name="topfile" type="file" <? if($err){echo "value=\"" . $_FILES['topfile']['tmp_name'] . "\"";} ?> /><br /><br />
    Side View PIC: <input name="sidefile" type="file"  <? if($err){echo "value=\"" . $_FILES['sidefile']['tmp_name'] . "\"";} ?> /><br />
    <br />
<h2>    NOTE: DO NOT CONTINUE UNTIL<br />AFTER SELECTING BOTH IMAGES</h2>

    <input type="hidden" value="<?echo $barcode; ?>" name="barcode" /><br /><br />
    <input type="submit" value="Continue (Send Files)" />
</form>
</div>
<?
}

function thumbnail($inputFileName, $maxSize = 100)
    {
        $info = getimagesize($inputFileName);
 
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
        $sourceImage = imagecreatefromstring(file_get_contents($inputFileName));
 
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
 
?>