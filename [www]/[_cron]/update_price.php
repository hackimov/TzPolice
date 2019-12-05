#!/usr/bin/php -q
<?php
//include "/home/sites/police/www/_modules/mysql.php";
include "/home/sites/police/dbconn/dbconn_persistent.php";
$res = array();
$SQL = "SELECT name FROM ItemsCost";
$r = mysql_query($SQL);
while ($d = mysql_fetch_array($r)) {
	$res[$d['name']] = array();
	$res[$d['name']]['summ'] = 0;
	$res[$d['name']]['count'] = 0;
}
$sock = @fsockopen("www.timezero.ru", 80, $er1, $er2, 5);
if(@$sock) {
	fputs($sock, "GET /res.xml HTTP/1.0\r\n");
	fputs($sock, "Host: www.timezero.ru \r\n");
	fputs($sock, "Content-type: application/x-www-url-encoded \r\n");
	fputs($sock, "\r\n\r\n");
	while (trim(fgets($sock, 4096))) 1;
	while (!feof($sock)) {
		if (preg_match("/name=\"(.*?)\" cost=\"(.*?)\"/",fgets($sock, 4096),$matches)) {
			if ($matches[2]>0) {
				$res[$matches[1]]['summ'] += $matches[2];
				$res[$matches[1]]['count']++;
			}
		}
	}
	foreach (array_keys($res) as $resname) {
		if ($res[$resname]['count']>0) {
			$SQL = "UPDATE ItemsCost SET cost='".round($res[$resname]['summ']/$res[$resname]['count'],2)."' WHERE name='".$resname."'";
			mysql_query($SQL);
		}
	}
}
?>