<?php
//----------------------------------------
// configuration file for sites
//----------------------------------------
define("_CONFIG",1);
define("_VERSION","2.2.0");
define("SITEURL","https://wms.yourdomain.com");
define("SITEHDIR","/home/your_wms/www");
define("UPLOADDIR","/home/your_wms/www/images/catalog");
define("SITEDBID","yourwms");

define("DEFAULTYARD","YOUR_YARD_NAME"); // ex: JAXYARD
define("SITENAME","City, ST Yard"); // ex: Jacksonville, FL Yard
define("SITEEMAIL", "wms@yourdomain.com");
define("COOKIEDOMAIN","wms.yourdomain.com");
define("MAGENTO_STORE_CATEGORY","GET_NAME_FROM_MAGENTO");
define("MAGENTO_ROOT_CATEGORY","GET_NAME_FROM_MAGENT)");
define("SITEEXPORTDIR","/home/wms/www/mgr/exports"); // for csv export files, will need to be public
							// in order to access from browser
define("STARTSKU","100004000");
define("_USECAT",1);

//QuickBooks accounts
define("COSACCT", "440.010 - COS-Materials");
define("SALESACCT","330.010 - Sales-Materials");
define("INVACCT", "130.010 - Inventory");

$SITELOCALDBHOST = "localhost";
$SITELOCALDBUSER = "your_mysql_user";
$SITELOCALDBPWD = "your_mysql_password";
$SITEDBNAME = "mysql_database_name";
// magento admin
$SITEREMOTEDBUSER = "your_mysql_user_for_remote_access";  
$SITEREMOTEDBPWD = "your_mysql_pwd";


// NOTE: Magento CE direct connection is no longer used in this app
// all imports and changes had been done with eMagicOne Store Manager PRO for Magento
?>

