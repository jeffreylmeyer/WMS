<img src="images/intro.png" />

<?php
// show open invoices not pulled here
// .invoices table is fed from Magento CE Remote at CORP Office when Magento Invoices are imported

require_once("db_connect.php");
$oss = oss_connect();


@mysqli_query($oss, "DELETE FROM invoices WHERE item_status='GONE'");
@mysqli_query($oss, "DELETE FROM invoices WHERE item_SKU LIKE 'FREIGHT%'");

sleep(1);

$sql = "SELECT DISTINCT order_no,customer_name,customer_pickup,item_status FROM invoices WHERE item_status in('OUTGOING','NEWORDER','NEEDS TO BE PULLED') ORDER BY recid ASC";

$ttl_rows = 0;

echo "<br clear='all' /><b>Pending Pulls</b><br /><br />\n\n";

echo "<table border='1' cellpadding='5' cellspacing='0' width='100%'>
	<tr><td><b>Order #</b></td><td><b>Customer</b></td><td><b>Will P/U</b></td><td><b>Status</b></td></tr>
";


if(!$rs = mysqli_query($oss, $sql)){
	echo "Error querying orders or no orders found.";
}else{
	$rows = mysqli_num_rows($rs);
	$ttl_rows = $rows;

	if($rows > 0){
		while($rows > 0){
			$rd = mysqli_fetch_assoc($rs);
			echo "<tr><td><a href='moveorder.php?order_no=$rd[order_no]'>".$rd['order_no']."</a></td><td>".$rd['customer_name']."</td><td>";
			if($rd['customer_pickup']=="1"){
				echo "Yes</td><td>".$rd['item_status']."</td></tr>";
			}else{
				echo "No</td><td>".$rd['item_status']."</td></tr>";
			}
				
			$rows--;
		}
		mysqli_free_result($rs);
	}
}
echo "</table>\n\n";

@mysqli_close($oss);

?>	