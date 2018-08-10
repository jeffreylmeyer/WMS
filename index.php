<?php
//--------------------------------------------------------------
// WMS V2
// 
// Modified: 26-JAN2014 by Jeffrey L Meyer
//--------------------------------------------------------------

require_once("wmsconfig.php");
require("ca.php");

if($user == "itdept" || $user == "osscorp" ){
	$MGR = 1;
	$iframe_height = "90%";
	$iframe_width = "100%";
}else{
	$iframe_height = "450";
	$iframe_width = "95%";
	$MGR=0;
}

$rn = rand(0,5000);
?>

<html><head><title>YOUR STONE CO - WMS V2.2.0 - <? echo SITENAME; ?></title>

<style type="text/css">
body {
	background:#F0B020;
}
</style>
<meta name="http-pragma" content="no-cache" />
<link href="css/redmond/jquery-ui-1.10.3.custom.css" rel="stylesheet">
	<script src="js/jquery-1.9.1.js"></script>
	<script src="js/jquery-ui-1.10.3.custom.js"></script>
</head>
<body>


<div style="float:left;width:66px;border-right:1px solid #F0F0F0">
	<a href="index.php" target="_top"><img src="images/wmslogo.jpg" vspace="5" /></a><br clear="all" />
	<a href="step1.php" target="main" title="Receive items" style="height:64px;width:64px;padding-bottom:5px"><img src="images/receive-icon.jpg" vspace="5" /></a><br clear="all" />
	
	<a href="movebin.php" target="main" title="Move item from one bin to another" style="height:64px;width:64px;padding-bottom:5px"><img src="images/pick-icon.jpg" vspace="5" /></a><br clear="all" />
<? if ($user == "ossm" || $user=="itdept"||$user="osscorp"){
?>	
	<a href="manager.php?r=<?=$rn?>" target="main" title="Manager Functions" style="height:64px;width:64px;padding-bottom:5px"><img src="images/review-icon.jpg" vspace="5" /></a><br clear="all" />
<?
}
?>	
	<a href="averaging.php" target="_blank" title="averaging Functions" style="height:64px;width:64px;padding-bottom:5px"><img src="images/avg-icon.png" vspace="5" /></a><br clear="all" />
	
		<a href="clear.php" target="_top" title="clear thumbs" style="height:64px;width:64px;padding-bottom:5px">Clear<br />Thumbs</a><br clear="all" />
		
	
</div>

</div>
<div style="float:left;width:90%">

<iframe bbgolor="#F0F0F0" scrollbars='both' width="<? echo $iframe_width; ?>" height="<? echo $iframe_height; ?>" src="intro.php" name="main" id="main" style="border:1px solid #008080;"></iframe>
<br />
<? echo "WMS Ver. " . _VERSION; ?>
</div>
<br clear="all" />
<? echo "Logged in as: $user - <small><i>To log out, close all browser windows";  ?>



</body>
</html>

<?

ob_end_flush();
?>