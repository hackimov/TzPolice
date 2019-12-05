<?php
	$image_id = isset($_GET["id"]) ? $_GET["id"] : false;
	$filename = '/home/sites/police/www/i/newfotos/thumbs/'.$image_id.'.jpg';
//	echo ($filename);
	if ($image_id === false) {
		header("HTTP/1.1 500 Internal Server Error");
		echo "No ID";
		exit(0);
	}
	header("Content-type: image/jpeg") ;
	header("Content-Length: ".strlen($filename));
	readfile ($filename);
	exit(0);
?>