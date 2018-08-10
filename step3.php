<?php
// gen barcode

$text = isset($_REQUEST['text']) ? $_REQUEST['text'] : "";
$item = isset($_REQUEST['item']) ? $_REQUEST['item'] : "";

$lot_no = isset($_REQUEST['lot_no']) ? $_REQUEST['lot_no'] : "";
$pal_no = isset($_REQUEST['pal_no']) ? $_REQUEST['pal_no'] : 1;
$weight = isset($_REQUEST['weight']) ? $_REQUEST['weight'] : "";



?>

<html><head><title>CLOSE AFTER PRINTING</title>

  <script type="text/javascript" src="code39.js"></script>
    <script type="text/javascript" src="jQueryRotate.js"></script>
    <style type="text/css">
  	#barcode {font-weight: normal; font-style: normal; line-height:normal; sans-serif; font-size: 11pt}
  	.rt {
	  /*	-webkit-transform:rotate(90deg);
	  	-webkit-transform-origin:32% 45%;
	  	-webkit-transform:rotate(90deg);
	  	-ms-transform:rotate(90deg);
	  	*/
	  	display:inline-block;
	  	text-align:left;
	  	/*border-bottom:1px dashed;*/
	  	padding:0 3 0 4;
	  	margin-left:8px;
	  	position:relative;
    </style>

<div id="externalbox" style="width:2.5in;height:2.0in" class="rt">
<div style="height:40px;width:2.5in;font-size:11pt;font-weight:bold;padding-bottom:5px;font-family:arial;"><? echo ucwords(strtolower($text)); ?></div>
<br clear="all" /><br />
<div id="sku" style="height:40px;display:block"><? echo $item; ?></div>

<div style="margin:top:10px;height:20px;font-size:13pt;font-weight:bold;font-family:arial;"><br>LOT#: <?=strtoupper($lot_no);?>  PAL#: <?=$pal_no;?><br>WEIGHT: <? echo $weight; ?> Lbs.</div>
</div>


<?
	$llen = strlen($lot_no);
	$bclen = ceil($llen *1.5);
?>
<script type="text/javascript">
/* <![CDATA[ */
  function get_object(id) {
   var object = null;
   if (document.layers) {
    object = document.layers[id];
   } else if (document.all) {
    object = document.all[id];
   } else if (document.getElementById) {
    object = document.getElementById(id);
   }
   return object;
  }

	get_object("sku").innerHTML=DrawCode39Barcode(get_object("sku").innerHTML,0,7);

//get_object("lot_no").innerHTML=DrawCode39Barcode(get_object("lot_no").innerHTML,0,<? echo $bclen; ?>);

/* ]]> */



</script>

</body>
</html>

