<?php

// определим дату и время записи с дробностью до 5 минут.

$t = date('H-i-s-m-d-Y', time());
$t = explode('-', $t);

$t[1] = (round($t[1]/5)) * 5;

$add = 0;

if ($t[1] == 60) {
	
	$t[1] == 55;
	$add = 300;
		
}

$t = mktime($t[0], $t[1], 0, $t[3], $t[4], $t[5]) + $add;

require_once("/home/sites/police/dbconn/dbconn.php");

// получаем список сайтов

$rating = file_get_contents("https://www.timezero.ru/rating/");
$matches = array();
preg_match_all('/<a href=\"(.*?)\" class=\"s\" target=\"_blank\">(.*?)<\/a>/', $rating, $matches, PREG_SET_ORDER);

// удаляем записи старше 180 дней, иначе засрем базу..

mysql_query("DELETE FROM tzpolice_siterating_screen WHERE time < ".($t-15552005));

for($i = 0; $i < 10; $i++) {
	
	$name =  $matches[$i][2];
	$url =   $matches[$i][1];
	
	$result = mysql_query("SELECT id FROM tzpolice_siterating_site WHERE url='".$url."'");
	
	
	if ($row = mysql_fetch_array($result)) {
		
		mysql_query("UPDATE tzpolice_siterating_site SET name='".$name."' WHERE url='".$url."'");
		$id = $row['id'];
		 
	} else {
		
		mysql_query("INSERT INTO tzpolice_siterating_site (name, url) VALUES ('".$name."', '".$url."')");
		$id = mysql_insert_id();
	}
	
	
	$result = mysql_query("SELECT * FROM tzpolice_siterating_screen WHERE site_id = ".$id." AND time = ".$t);
	
	if ($row = mysql_fetch_array($result)) {
		
		// запись уже есть, ничего не делаем
	
	} else {
		
		// получаем значение со счетчика
		$answ = file_get_contents("https://www.timezero.ru/cgi-bin/top.pl?r=".$url);
		// all=20&cnt=19
		if (preg_match('/all=(.*)&cnt=(.*)/', $answ, $cm)) {
			
			if ($cm[2] == '' ) $cm[2] = 0;
			mysql_query("INSERT INTO tzpolice_siterating_screen (time, site_id, counter) VALUES ('$t', '$id', '".$cm[2]."')");
			
		}
		
		
		
	}
		
}



?>