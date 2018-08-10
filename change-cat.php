<?php
//---------------------------------------------------------------------------
// Overstock Stone, Inc. WMS V2
// post items from command line
// Modified: 26-JAN-2014 by Jeffrey L Meyer
//---------------------------------------------------------------------------

// THIS IS NOT USED, TYPICALLY, but it can be modified to change a products
// category if it was initially imported incorrectly

require_once("wmsconfig.php");


require_once("db_connect.php");
$ossdb = oss_connect();


if(!$rs = mysqli_query($ossdb,"SELECT * FROM item_data WHERE (category LIKE 'Flagstone|Mexico%')")){
	exit("ERROR: " . mysqli_error($ossdb));
}

$rows = mysqli_num_rows($rs);
if($rows > 0){
	while($rows > 0){
		$rd = mysqli_fetch_assoc($rs);
		
		echo $rd['SKU'] . "\n";
/*		
		$rd['item_type'] = str_replace('Brown, Tan, Black', 'Multiblend', $rd['item_type']);
		$rd['description'] = str_replace('Brown, Tan, Black', 'Multiblend', $rd['description']);
		$rd['category'] = str_replace('Brown, Tan, Black', 'Multiblend', $rd['category']);
		
		OKLAHOMA TUMBLED MILL ENDS - BROWN, TAN BLACK
		OKLAHOMA TUMBLED MILL ENDS - BROWN, TAN, BLACK
		Building Stone|Oklahoma|Tumbled Mill Ends - Brown, Tan Black|
*/		

		$rd['item_type'] = str_replace('Mexico Buff -', 'Utah Buff Argillite Slate -', $rd['item_type']);
		$rd['description'] = str_replace('Mexico Buff -', 'Utah Buff Argillite Slate -', $rd['description']);
		$rd['description'] = str_replace('Flagstone Mexico Buff -', 'Utah Buff Argillite Slate -', $rd['description']);		
		$rd['category'] = str_replace('Flagstone|Mexico|Buff -', 'Flagstone|Utah|Buff Argillite Slate -', $rd['category']);
		
		$rd['item_type'] = str_replace('Mexico Rose -', 'Utah Rose Argillite Slate -', $rd['item_type']);
		$rd['description'] = str_replace('Mexico Rose -', 'Utah Rose Argillite Slate -', $rd['description']);
		$rd['description'] = str_replace('Flagstone Mexico Rose -', 'Utah Rose Argillite Slate -', $rd['description']);
		$rd['category'] = str_replace('Flagstone|Mexico|Rose -', 'Flagstone|Utah|Rose Argillite Slate -', $rd['category']);

		$rd['item_type'] = str_replace('Mexico Pastel Blend -', 'Utah Pastel Blend Argillite Slate -', $rd['item_type']);
		$rd['description'] = str_replace('Mexico Pastel Blend -', 'Utah Pastel Blend Argillite Slate -', $rd['description']);
		$rd['description'] = str_replace('Flagstone Mexico Pastel Blend -', 'Utah Pastel Blend Argillite Slate -', $rd['description']);
		$rd['category'] = str_replace('Flagstone|Mexico|Pastel Blend -', 'Flagstone|Utah|Pastel Blend Argillite Slate -', $rd['category']);
		
/*		$rd['item_type'] = str_ireplace('OKLAHOMA TUMBLED MILL ENDS - BROWN, TAN BLACK', 'Oklahoma Tumbled Mill Ends - Multiblend', $rd['item_type']);
		$rd['item_type'] = str_ireplace('OKLAHOMA TUMBLED MILL ENDS - BROWN, TAN, BLACK', 'Oklahoma Tumbled Mill Ends - Multiblend', $rd['item_type']);
		$rd['description'] = str_ireplace('OKLAHOMA TUMBLED MILL ENDS - BROWN, TAN BLACK', 'Oklahoma Tumbled Mill Ends - Multiblend', $rd['description']);
		$rd['description'] = str_ireplace('OKLAHOMA TUMBLED MILL ENDS - BROWN, TAN, BLACK', 'Oklahoma Tumbled Mill Ends - Multiblend', $rd['description']);		
		$rd['category'] = str_ireplace('Building Stone|Oklahoma|Tumbled Mill Ends - Brown, Tan Black|', 'Building Stone|Oklahoma|Tumbled Mill Ends - Multiblend|', $rd['category']);
		$rd['category'] = str_ireplace('Building Stone|Oklahoma|Tumbled Mill Ends - Brown, Tan, Black|', 'Building Stone|Oklahoma|Tumbled Mill Ends - Multiblend|', $rd['category']);		
*/		
		echo "item_type: $rd[item_type] | description: $rd[description] | category: $rd[category]\n\n";

		mysqli_query($ossdb,"UPDATE item_data SET item_type='$rd[item_type]',description='$rd[description]',category='$rd[category]' WHERE SKU='$rd[SKU]'");
		$rows--;
	}
	mysqli_free_result($rs);
}else{
	echo "No matches found...";
}

mysqli_close($ossdb);

echo "\n\ndone\n\n";

?>