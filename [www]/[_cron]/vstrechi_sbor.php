#!/usr/bin/php -q
<?php
error_reporting(E_ALL);
error_reporting(0);
include "/home/sites/police/dbconn/dbconn_persistent.php";

//include ('connect.php');

include ('/home/sites/police/www/_cron/db_lib.php');


$pageOut = array();

 $fp = fsockopen('www.timezero.ru', 80, &$errno, &$errstr, 30);
	flush();
	fputs($fp, "GET http://www.timezero.ru/cgi-bin/forum.pl?a=Z HTTP/1.0\r\n\r\n");

	if(!$fp);
	else{
	while(!feof($fp))array_push($pageOut,fgets($fp, 1000));}
	fclose($fp);

//print_r($pageOut);
//foreach($pageOut as $key=>$value)
//	echo $key.' = '.trim(htmlspecialchars($value))."<br>\n";

$q = sql("select tz_id from vstrechi");
while($m_tmp = sql_a($q)){
//echo $m_tmp['tz_id'];
	$m_id [] = $m_tmp['tz_id'];
} // while

$i = -1;
foreach($pageOut as $key=>$value)
{
	if(strstr(trim($value), "<TR class=\"gr\">"))
	{
		$i++;

		$start = strpos($pageOut[$key+2], "?a=Z&c=")+7;
		$end = strpos($pageOut[$key+2], "\"", $start);
		$len = $end - $start;
		$v_id = substr($pageOut[$key+2],$start,$len);

		$start = strpos($pageOut[$key+2], "<a href=");
		$start = strpos($pageOut[$key+2], "\">", $start)+2;
		$end = strpos($pageOut[$key+2], "</a>");
		$len = $end - $start;
		$v_name = substr($pageOut[$key+2],$start,$len);

		$v_author = trim(strip_tags($pageOut[$key+3]));
		$start = strpos($v_author, " [");
		$v_author = substr($v_author,0,$start);


		$vstrecha[$i]['id'] = $v_id;
		$vstrecha[$i]['v_name'] = $v_name;
		$vstrecha[$i]['v_author'] = $v_author;

		if(!@in_array($v_id,$m_id))
		{
			$v_id = trim(addslashes(stripslashes(strip_tags($v_id))));
			$v_name = trim(addslashes(stripslashes(strip_tags($v_name))));
			$v_author = trim(addslashes(stripslashes(strip_tags($v_author))));

			sql("insert into vstrechi (tz_id, v_name, v_author, v_new)
				values($v_id, '$v_name', '$v_author', 1)");


		}
	}
}
echo '<pre>';
print_r($vstrecha);
echo '</pre>';
?>