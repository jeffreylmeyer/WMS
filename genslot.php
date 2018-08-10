<?php
// generate slots
// you will have to modify code each time you wish to generate
// slots for a bin map so you do not add ones that already exist
// these appear in the dropdowns on the add item, edit pages
// It is designed to setup a yard or warehouse map prior to adding product
/*
 Example
	from shell type:
	/bin/sh genslot.php A1 200
	
	for row A1 and 200 slots in that row
	will generate A1-1 to A1-200 for slots
	Once in there they will appear in the slot dropdowns on the add,edit and transfer pages

*/
require_once("db_connect.php");
$ossdb = oss_connect();

$SECTIONS = array('D Section'); // list of sections (large area of rows)
//$ROWS = array('A1','A2','A3','A4','A5','B1','B2','B3','B4','B5','C1','C2','C3','C4','C5');

$row = isset($argv[1]) ? $argv[1] : 'D1';
$slots = isset($argv[2]) ? $argv[2] : 0;




$fp = fopen("/home/wms/www/exports/locations.csv","w");
if($fp){
	fputs($fp,"Status,Code,Description,Empty,Type,Zone,Length,Width,Height,Customer,Disabled\n");
	foreach($SECTIONS as $section){


		$loc=strtoupper($row);
	
		for($idx = 1; $idx <= $slots; $idx++){
			mysqli_query($ossdb, "INSERT INTO bin_location_map SET yard='YOUR_YARD_NAME',zone='$section',bin_loc='$row" . "-" . $idx . "'");
			echo "$row" . "-" . $idx . "\n";
			fputs($fp,"Empty,YOUR_YARD_NAME" . "-" . $section . "-$row" . "-$idx,LC Bin,Yes,Storage,LC Storage,48,48,48,,No\n");
		}

	}

fclose($fp);
}
@mysqli_close($ossdb);

echo "<a href=\"exports/locations.csv\">Click here for csv</a>";

?>