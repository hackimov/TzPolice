<?
include("/home/sites/police/dbconn/dbconn.php");


// Индексатор (глюченый)
//$prefix = 3;
for ($prefix=2; $prefix<=3; $prefix++):
	for ($i=1; $i<=20; $i++):
		$url = 'http://www.timezero.ru/cgi-bin/forum.pl?a=D'.$prefix.'&b='.$i;
		$content = file_get_contents($url);
		preg_match_all ('%(<a href="forum.pl\?a=D'.$prefix.'&c=)(.*?)(".*?>)%is', $content, $links[$i]);
		preg_match_all ("/(<TD nowrap>)([0-9]{2}\.[0-9]{2}\.[0-9]{2}\s[0-9]{2}:[0-9]{2})(<BR>)/", $content, $date);

		$count = count($links[$i][2]);
		for ($j=0; $j<=$count-1; $j++):
			$id_tz = $links[$i][2][$j];
			$last_message = $date[2][$j];
			if (mysql_num_rows(mysql_query("SELECT `id_tz` FROM `galkoff_ip_base` WHERE `id_tz` = '$id_tz' AND `last_message` = '$last_message'"))==0):
				$content = file_get_contents('http://www.timezero.ru/cgi-bin/forum.pl?c='.$id_tz.'&a=D'.$prefix.'&z='.$id_tz);
				preg_match_all ("/(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])/", $content, $ip);

				$ip = implode(" ", $ip[0]);
				preg_match('%(<title>)(.*?)(<\/title>)%is', $content ,$title);
				$title = iconv('utf-8','WINDOWS-1251',$title[2]);
				$title = str_replace("Форум ",'',$title);
				if ($ip!=''):
					if (mysql_num_rows(mysql_query("SELECT `id_tz` FROM `galkoff_ip_base` WHERE `id_tz` = '$id_tz'"))==0):
						mysql_query("INSERT INTO `galkoff_ip_base` (`id`, `id_tz`, `name`, `ip`, `prefix`, `last_message`) VALUES ('', '$id_tz', '$title', '$ip', '$prefix', '$last_message')");
					else:
						$var = mysql_fetch_assoc(mysql_query("SELECT `id_tz` FROM `galkoff_ip_base` WHERE `id_tz` = '$id_tz'"));
						mysql_query("UPDATE `galkoff_ip_base` SET `ip` = '$ip', `last_message` = '$last_message' WHERE `id_tz` = '{$var['id_tz']}'");
					endif;
				endif;
			endif;
		endfor;
	endfor;
endfor;
?>