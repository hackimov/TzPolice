#!/usr/bin/php -q
<?
	include ('/home/sites/police/www/_modules/functions.php');
//	error_reporting(E_ALL);
	include_once ('/home/sites/police/www/_modules/rating_arh/tz_plugins.php');

foreach (glob("/home/sites/police/bot/logs/myhist-police-*.txt") as $filename) {
	$fp = fopen ($filename, 'r');
	$bytes = filesize($filename);
	$buffer = fread($fp, $bytes);
	fclose ($fp);

	$buffer = explode("\n", $buffer);
	#echo ("<pre>");
	#print_r($buffer);
	#echo ("</pre>");

	$urgent_alarm = '';
	$deleted = 0;
	$buf_size = sizeof($buffer);
	for ($i=0; $i<$buf_size; $i++) {
	// 13.10.07 01:31	178	177	stan79	1440	¬ейнемейнен	http://www.timezero.ru/cgi-bin/forum.pl?a=T&amp;amp;c=108372806&amp;amp;m=1
		$tst = explode("\t", $buffer[$i]);
	/*	0=врем€
		1=копэкшн == 178
		2=код действи€
			175-наложена чатова€ молча,
			176-сн€та чатова€ молча,
			177-наложена форумна€ молча,
			178-сн€та форумна€ молча,
			179-на каторгу,
			180-с каторги,
			181-в блок,
			182-из бока.
			183-постановка чару в дело чистоты перед законом
		3=ник копа
		4=врем€ действи€ (молчанки и тд)
		5=ник
		6=причина
	*/
		if ($tst[1] == '178') {
			$tmp = explode(' ', $tst[0]);
			$tmp[0] = explode(".",$tmp[0]);
			$tmp[1] = explode(":",$tmp[1]);
			$time = mktime($tmp[1][0], $tmp[1][1], 0, $tmp[0][1], $tmp[0][0], $tmp[0][2]);

		// ”даление записей (так называемый откат :) )
			if(!$deleted){
				$query = 'DELETE FROM `cops_actions` WHERE `time` >= '.$time;
				$rs = mysql_query($query, MYSQL_DB_CONNECTION);
				$deleted = 1;
			}

		// ѕолучаем id_name копа
			$row = GetPersParamsByName(trim($tst[3]));
			$cop_id = $row['id'];

		// ѕолучаем id_name юзера
			$row = GetPersParamsByName(trim($tst[5]));
			$user_id = $row['id'];

		// в логах кака€ хрень со спешалчаром, вобщим амперсанд дважды нормализуем
			$reas = str_replace('&amp;', '&', $tst[6]);
			$reas = str_replace('&amp;', '&', $reas);
			$reas = trim($reas);

			$query = 'INSERT INTO `cops_actions` SET `time` = \''.$time.'\', `action` = \''.$tst[2].'\', `cop_id` = \''.$cop_id.'\', `action_time` = \''.mysql_escape_string($tst[4]).'\', `user_id` = \''.$user_id.'\', `text` = \''.mysql_escape_string($reas).'\';';
            #echo "$query<hr>";
			mysql_query($query, MYSQL_DB_CONNECTION);
		}
	}
	error_log(date("d.m.Y H:i:s")." Finished working at ".$filename." log.\n",3,"cops_access_log.txt");
	unlink($filename);
}

mysql_close(MYSQL_DB_CONNECTION);

?>