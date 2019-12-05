<?php

$in = array();

foreach($_GET as $k => $v) {

	$temp = mb_convert_encoding($v,"cp1251","utf8");
	$temp2 = mb_convert_encoding($temp,"utf8","cp1251");
	if($temp2 == $v) {
		$v = mb_convert_encoding($v,"cp1251","utf8");
	}

	$in[$k] = addslashes(htmlspecialchars(trim($v)));
	
}

print_r($in);


?>