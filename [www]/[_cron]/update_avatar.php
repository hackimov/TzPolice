#!/usr/bin/php -q
<?php

error_reporting(E_ALL);
//error_reporting(0);

$file = file('https://www.timezero.ru/tzupd2.ini');

foreach ($file as $num => $row)
{
	if (strpos($row, 'avatar')) {
		
		$tmp = explode(' ', $row);
		$remote_file = "https://game.timezero.ru/".trim($tmp[1]);
		$img_contetn = file_get_contents($remote_file);
		
		if (substr($img_contetn, 6, 4) == 'JFIF') {
			
			$fp=fopen ("/home/sites/police/www/".trim($tmp[1]), "w+");
			fwrite($fp, $img_contetn);
			fclose($fp);
			
		}
		
	}
}

?>