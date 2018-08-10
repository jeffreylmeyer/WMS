<?php
// HQ image display
// created: 05-MAY-2015
//
// updated 06-22-2016 by Jeffrey meyer for new site


$sku = isset($_REQUEST['sku']) ? $_REQUEST['sku'] : "";
$c = isset($argv[1]) ? $argv[1] : "";

if($c == "create"){
	createIndex();
	exit(0);
}

if($sku == ""){
	exit(0);
}

$hqdir = "/home/wms/www/images/catalog";

$fndstr = "";

if(file_exists("index.txt")){
	$fp = fopen("index.txt","r");
	if($fp){
		while(!feof($fp)){
			$buf = fgets($fp,2048);
			$buf = trim($buf,"\r\n\t");
			if($buf != ""){
				if(strpos($buf,$sku) !== false && strpos($buf,"T.") !== false){
					$fndstr = $buf;
					break;
				}
			}
		}	
		fclose($fp);
	}
}
if($fndstr != ""){
	header("Location:https://wms.yourdomain.com/" . $fndstr );
}else{
	echo "Sorry, image not found for SKU $sku";
	createIndex();
}

//echo $url;

flush();
exit(0);


//----------------------------------------------------
function createIndex()
{
// reads subdirectories for image storage to create an index
// based off the SKU------S.jpg image names
// modify to suit your particular media directory
	$DIRS = array('images/catalog/');
	$S1L = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-";
	$S2L = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-";	
	
	$fp = fopen("index.txt","w");
	if($fp){
		foreach($DIRS as $dir){

			for($idx=0;$idx < 37;$idx++){
				$s1 = substr($S1L,$idx,1);
				for($sdx=0;$sdx < 37;$sdx++){
					$s2 = substr($S2L,$sdx,1);				
					$rdir = $dir;					
					//read dir
					$rdir .= "$s1/$s2/";
					if (is_dir($rdir)) {
					    if ($dh = opendir($rdir)) {
					        while (($file = readdir($dh)) !== false) {
					        	if($file != "." && $file != ".."){
					        	  if(strpos($file,"S.")!==false){
					        	  //	@unlink($rdir . $file);
					        	  }else{
						           fputs($fp, $rdir . $file . "\n");
						          }
						        }
					        }
					        closedir($dh);
					    }
					}

				}
			
			}
		}
	
		fclose($fp);
		@chmod("index.txt",0777);
	}

}

?>