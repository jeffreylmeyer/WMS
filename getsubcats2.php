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
$S = isset($_REQUEST['subcat1']) ? urldecode($_REQUEST['subcat1']) : ""; // name (not #)

if($P == ""){
	$P = isset($_COOKIE["last_primary"]) ? $_COOKIE["last_primary"]:"";
}

setcookie("last_subcat",$S,time()+86400,"/",COOKIEDOMAIN);

$PIDX = getPrimaryIDX($P);
$SIDX = getSubcat1IDX($PIDX,$S);

$SUBCATS2 = getSubcat2($PIDX, $SIDX);

echo "($S) $PIDX $SIDX " . print_r($SUBCATS2,1);

$SUBS = "";

if(!empty($SUBCATS2)){
	foreach($SUBCATS2 AS $SUBC){
		if($SUBC != ""){
			$SUBS .= "<option value=\"".urlencode($SUBC) . "\">".str_replace('±','+/-',$SUBC)."</option>\n";
		}
	}
}

mysqli_close($ossm);
echo $SUBS; // return to calling script as text string



?>