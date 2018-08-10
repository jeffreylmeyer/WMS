<?php
// Clears thumbnails from manager view so they can reload

$path = "/home/wms/www/images/catalog/L/C/thumbs/*";

`rm -rf $path`;

header("Location: index.php");
?>