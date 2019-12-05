#!/usr/bin/php -q
<?
//error_reporting(E_ALL);
require ("/home/sites/police/www/_modules/functions.php");
include "/home/sites/police/dbconn/dbconn_persistent.php";
//$html = file_get_contents("http://www.timezero.ru/clans/list.ru.html");

//$regular = "<img src=\"\/i\/clans\/(.*)\.gif\" class=\"c\">";

//preg_match_all('/'.$regular.'/iSU', $html, $matches);

//print_r($matches);

$query = "SELECT * FROM `tzpolice_tz_clans`";
$res = mysql_query($query);
while ($d=mysql_fetch_array($res))
//foreach($matches[1] as $v)
	{
	$fname = trim($d['name']);
	$fname .= '.gif';
	$rf = str_replace(' ', '%20', $fname);
	$t = file_get_contents('http://www.timezero.ru/i/clans/'.$rf);
	$fname = str_replace('%20', ' ', $fname);
	unlink('/home/sites/police/www/_imgs/clans/'.$fname);
	$w = fopen('/home/sites/police/www/_imgs/clans/'.$fname, 'w');
	fwrite($w, $t);
	fclose($w);
	chgrp('/home/sites/police/www/_imgs/clans/'.$fname, 'police');
	chmod('/home/sites/police/www/_imgs/clans/'.$fname, 0666);
}

?>