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

setcookie("last_primary",$P,time()+86400,"/",COOKIEDOMAIN);


$SUBCATS1 = getSubcat1(getPrimaryIDX($P));

$SUBS = "";

if(!empty($SUBCATS1)){
	foreach($SUBCATS1 AS $SUBC){
		if($SUBC != ""){
			$SUBS .= "<option value=\"$SUBC\">$SUBC</option>\n";
		}
	}
}


mysqli_close($ossm);
echo $SUBS; // return to calling script as text string



?>