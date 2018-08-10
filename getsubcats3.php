<?php
//------------------------------------------------------
// get subcategory level 1 list
// called via Ajax/jQuery from add, edit, move forms

require_once("wmsconfig.php");
require_once("db_connect.php");
require_once("functions.php");

$ossm = ossm_connect();
mysqli_query($ossm,"USE categories");

$P = isset($_REQUEST['primarycat']) ? $_REQUEST['primarycat'] : ""; // name (not #)
$S = isset($_REQUEST['subcat1']) ? $_REQUEST['subcat1'] : ""; // name (not #)
$S2 = isset($_REQUEST['subcat2']) ? $_REQUEST['subcat2'] : ""; // name (not #)

if($P == ""){
	$P = isset($_COOKIE["last_primary"]) ? $_COOKIE["last_primary"]:"";
}
if($S == ""){
	$S = isset($_COOKIE["last_subcat"]) ? $_COOKIE["last_subcat"]:"";
}

setcookie("last_subcat",$S,time()+86400,"/",COOKIEDOMAIN);
setcookie("last_subcat2",$S,time()+86400,"/",COOKIEDOMAIN);

$PIDX = getPrimaryIDX($P);
$SUB1IDX = getSubcat1IDX($PIDX,$S);
$SUB2IDX = getSubcat2IDX($PIDX,$SUB1IDX,$S2);

$SUBCATS3 = getSubcat3($PIDX, $SUB1IDX, $SUB2IDX);

$SUBS = "";

if(!empty($SUBCATS3)){
	foreach($SUBCATS3 AS $SUBC){
		if($SUBC != ""){
			$SUBS .= "<option value=\"".urlencode($SUBC). "\">$SUBC</option>\n";
		}
	}
}

mysqli_close($ossm);
echo $SUBS; // return to calling script as text string


?>