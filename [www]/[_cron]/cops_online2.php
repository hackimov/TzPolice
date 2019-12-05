#!/usr/bin/php -q
<?

//if (!isset($_GET['test'])) {
//	exit();
//}

$exclude[] = 'Al-Kabul Guard';
$exclude[] = 'Oasis Guard';
$exclude[] = 'NewMoscow Guard';
$exclude[] = 'Jerusalem Guard';
$exclude[] = 'Nevacity Guard';
$exclude[] = 'hate Ati';
$exclude[] = 'Постовой (Оазис)';
$exclude[] = 'Terminal POLICE';
$exclude[] = 'NevaCity';
$exclude[] = 'OldJerusalem Guard';
$exclude[] = 'NevaCity Guard';
$exclude[] = 'Начальник ПА';
$exclude[] = 'Главбух';

$city['cp'] = '0/0';    // центр
$city['auc'] = '1/359';  //  аук



// status 0 = chat on, 1 = chat off, 2 = Out of city cops

//include "/home/sites/police/dbconn/dbconn.php";
//include ('/home/sites/police/dbconn/dbconn_persistent.php');

require_once ('/home/sites/police/www/_modules/functions.php');
require_once ('/home/sites/police/www/_modules/rating_arh/tz_plugins.php');

//error_reporting(E_ALL);
$res = mysql_query('SELECT * FROM `cops_online` WHERE `logout` < 1');

$curdate = date('Y-m-d');
$curtime = time();

//echo ($curdate);
$count = 0;

//echo ("Current cops <hr><hr><hr>");
	while ($result = mysql_fetch_array($res)) {
        $curcops[$result['nick']]['login'] = $result['login'];
        $curcops[$result['nick']]['date'] = $result['date'];
        $tempvar = $result['date'];
        $curcops[$result['nick']]['id'] = $result['id'];
        $curcops[$result['nick']]['status'] = $result['status'];
        $curcops[$result['nick']]['done'] = 0;
        $count++;
    }

//echo ("Killing last day <hr><hr><hr>");
	if ($tempvar > 0 && $tempvar !== $curdate) {
		foreach ($curcops as $key => $tmp)
        	{
	            $tmptime = $curtime-43200;
	            $tmpdate = date('Y-m-d',$tmptime);
	            $tmpdate .= ' 23:59:59';
	            $tmptimestamp = strtotime($tmpdate);
	            $query = 'UPDATE `cops_online` SET `logout`=\''.$tmptimestamp.'\' WHERE `id` = \''.$tmp['id'].'\' LIMIT 1;';
				mysql_query($query);
		//		echo $query."<hr>";
				$tmptimestamp++;
				$query = 'INSERT INTO `cops_online` SET `nick` = \''.$key.'\', `login` = \''.$tmptimestamp.'\', `date` = \''.$curdate.'\', `status` = \''.$tmp['status'].'\'';
				mysql_query($query);
		//		echo $query."<hr>";
            }
    }
/*
echo ("<pre>");
print_r($curcops);
echo ("</pre>");
*/
	foreach (glob('/home/sites/police/bot/logs/police-*.txt') as $filename) {

		$curptime = strptime($filename, '/home/sites/police/bot/logs/police-%Y%m%d%H%M%S.txt');
		$curptime = mktime($curptime['tm_hour'], $curptime['tm_min'], $curptime['tm_sec'], 1+$curptime["tm_mon"], $curptime['tm_yday'], 1900+$curptime['tm_year']);
	//	print_r($curptime);
		$fp = fopen ($filename, 'r');
		$bytes = filesize($filename);
		$buffer = fread($fp, $bytes);
		fclose ($fp);
		unlink($filename);
	//	echo $buffer;
		$cops_list = explode(',', $buffer);
		foreach ($cops_list as $cop) {
			$arr = explode(':',$cop);
			$nick = $arr[0];
			$loc = $arr[1];
			if ($nick !== '' && !in_array($nick,$exclude)) {
				// --------- ОффЛайн -----------
				if ($loc=='0') {
					$offline_cops[$nick] = 1;
					$online_cops[$nick] = 0;
					$chatoff_cops[$nick] = 0;
					$outofcity_cops[$nick] = 0;
				} else {
					$param_online_cop = explode('/', $loc);  // [0] - X  [1] - Y  [2] - server   [3] - status
					$where_cop[$nick] = $param_online_cop[0].'/'.$param_online_cop[1];
					
					// --------- Чат выключен -----------
					if ($param_online_cop[3] == '2') {
						$offline_cops[$nick] = 0;
						$online_cops[$nick] = 0;
						$chatoff_cops[$nick] = 1;
						$outofcity_cops[$nick] = 0;
					} else {
						
					// --------- Онлайн -----------
						$loc2 = $param_online_cop[0].'/'.$param_online_cop[1]; // чат включен, перс на локе поста
						if (in_array($loc2,$city) && $param_online_cop[2] == '1') {
							$offline_cops[$nick] = 0;
							$online_cops[$nick] = 1;
							$chatoff_cops[$nick] = 0;
							$outofcity_cops[$nick] = 0;
						// --------- Вне зоны поста -----------
						} else {   // чат включен, но где перс неизвестно.
							$offline_cops[$nick] = 0;
							$online_cops[$nick] = 0;
							$chatoff_cops[$nick] = 0;
							$outofcity_cops[$nick] = 1;
						}
					}
				}

		/*		if ($offline_cops[$nick] == 1) {
					$tmp_page = fant_TZConn($nick, 0);
					$userinfo = fant_ParseUserInfo($tmp_page);
					if($userinfo['online'] == '1'){
						$offline_cops[$nick] = 0;
						$online_cops[$nick] = 1;
					}
				}
		*/
			//////////////////////////////////
				if ($tempvar > 0 && $tempvar !== $curdate) {
					$query = 'INSERT INTO `cops_online` SET `nick` = \''.$nick.'\', `login` = \''.time().'\', `logout`=\''.(time()+1).'\', `date` = \''.$curdate.'\', `status` = \'0\'';
					mysql_query($query);

				}
			//////////////////////////////////
			}
		}

//	echo ("Online cops <hr><hr><hr>");
		foreach ($online_cops as $key => $value) {

			if ($curcops[$key]['login'] > 0 && $value == 1) {

				if (($curcops[$key]['status'] == 1 || $curcops[$key]['status'] == 2) && $curcops[$key]['done'] == 0) {
					$query = "UPDATE `cops_online` SET `logout`='".$curtime."' WHERE `id` = '".$curcops[$key]['id']."' LIMIT 1;";
					mysql_query($query);
					//echo $query."<hr>";
					$query = "INSERT INTO `cops_online` SET `nick` = '".$key."', `login` = '".$curtime."', `date` = '".$curdate."', `status` = '0'";
					mysql_query($query);
					//echo $query."<hr>";
					$curcops[$key]['done'] = 1;

				} elseif ($value == 1) {
					$curcops[$key]['done'] = 1;
				}

			} elseif ($curcops[$key]['login'] < 1000 && $value == 1 && $curcops[$key]['done'] == 0) {
				$query = "INSERT INTO `cops_online` SET `nick` = '".$key."', `login` = '".$curtime."', `date` = '".$curdate."', `status` = '0'";
				mysql_query($query);
				//echo $query."<hr>";
				$curcops[$key]['done'] = 1;
			}
		}

//	echo ("Chatoff cops <hr><hr><hr>");
		foreach ($chatoff_cops as $key => $value) {

			if ($curcops[$key]['login'] > 0 && $value == 1) {

				if (($curcops[$key]['status'] == 0 || $curcops[$key]['status'] == 2) && $curcops[$key]['done'] == 0) {
					//echo $curcops[$key]['login'];
					$query = "UPDATE `cops_online` SET `logout`='".$curtime."' WHERE `id` = '".$curcops[$key]['id']."' LIMIT 1;";
					mysql_query($query);
					//echo $query."<hr>";
					$query = "INSERT INTO `cops_online` SET `nick` = '".$key."', `login` = '".$curtime."', `date` = '".$curdate."', `status` = '1'";
					mysql_query($query);
					//echo $query."<hr>";
					$curcops[$key]['done'] = 1;

				} elseif ($value == 1) {
					$curcops[$key]['done'] = 1;
				}

			} elseif ($curcops[$key]['login'] < 1000 && $value == 1 && $curcops[$key]['done'] == 0) {
				$query = "INSERT INTO `cops_online` SET `nick` = '".$key."', `login` = '".$curtime."', `date` = '".$curdate."', `status` = '1'";
				mysql_query($query);
				//echo $query."<hr>";
				$curcops[$key]['done'] = 1;
			}
		}

//	echo ("Out of city cops <hr><hr><hr>");
		foreach ($outofcity_cops as $key => $value) {
			if ($curcops[$key]['login'] > 0 && $value == 1) {
				if (($curcops[$key]['status'] == 0 || $curcops[$key]['status'] == 1) && $curcops[$key]['done'] == 0) {
					//echo $curcops[$key]['login'];
					$query = "UPDATE `cops_online` SET `logout`='".$curtime."' WHERE `id` = '".$curcops[$key]['id']."' LIMIT 1;";
					mysql_query($query);
					//echo $query."<hr>";
					$query = "INSERT INTO `cops_online` SET `nick` = '".$key."', `login` = '".$curtime."', `date` = '".$curdate."', `status` = '2'";
					mysql_query($query);
					//echo $query."<hr>";
					$curcops[$key]['done'] = 1;

				} elseif ($value == 1) {
					$curcops[$key]['done'] = 1;
				}

			} elseif ($curcops[$key]['login'] < 1000 && $value == 1 && $curcops[$key]['done'] == 0) {
				$query = "INSERT INTO `cops_online` SET `nick` = '".$key."', `login` = '".$curtime."', `date` = '".$curdate."', `status` = '2'";
				mysql_query($query);
				//echo $query."<hr>";
				$curcops[$key]['done'] = 1;
			}
		}

//	echo ("Offline cops <hr><hr><hr>");
		foreach($curcops as $tmp) {

			if($tmp['done'] == 0) {

				$query = "UPDATE `cops_online` SET `logout`='".$curtime."' WHERE `id` = '".$tmp['id']."' LIMIT 1;";
				mysql_query($query);
				//echo $query."<hr>";
			}
		}

	//============ Автодроп с поста если чатофф, оффлайн или вне купола ===============
    
		$query="SELECT p.post_t, p.id_user, p.city, u.user_name FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user WHERE p.post_g=0 ORDER BY p.city, p.post_t";
		$rs = mysql_query($query);

		if (mysql_num_rows($rs) > 0) {

			$users = $users_online = $users_offline = $users_chatoff = $users_outofcity = $user_city = array();

			while ($cur_st = mysql_fetch_array($rs)) {
				$users[$cur_st['user_name']] = $cur_st['id_user'];
				$user_city[$cur_st['user_name']] = $cur_st['city'];
			}

			foreach ($online_cops AS $key => $value) {
				if ($value == 1) {
					// проверим, на своем ли посту копчег...
					// 3 - форум. 5 - аук. 1 - ЦП.
					if (($user_city[$key] == 5 && $where_cop[$key] == $city['auc']) || ($user_city[$key] == 1 && $where_cop[$key] == $city['cp'])) {
						echo('=1=');
						$users_online[] = $key;   // коп на том посту который заявлен на сайте
					} else {
						echo('=2=');
						$users_outofcity[] = $key;   // коп не на своем посту = свалил в самоволку цопака...
					}
				}
			}

			foreach ($offline_cops AS $key => $value) {
				if ($value == 1) {
					$users_offline[] = $key;
				} else {   // форум
					if ($user_city[$key] == 3)
						$users_online[] = $key;  // счтиаем онлайн если на посту форума и перс не офф.
				}
			}

			foreach ($chatoff_cops AS $key => $value) {
				if ($value == 1)
					$users_chatoff[] = $key;
			}

			foreach ($outofcity_cops AS $key => $value) {
				if ($value == 1)
					$users_outofcity[] = $key;
			}

			foreach($users AS $name => $user_id){

				if ( !in_array($name, $users_online) ) {

					if ( in_array($name, $users_offline) )
						$reason = "На посту отсутствует (статус: OffLine)";
					elseif ( in_array($name, $users_chatoff) )
						$reason = "На посту отсутствует (статус: ChatOff)";
					elseif ( in_array($name, $users_outofcity) )
						$reason = "На посту отсутствует (статус: Out of Post-Zone)";
					else
						$reason = "На посту отсутствует (статус неопределен)";

			//		$reason = str_replace("\n", "<br>", $reason);

					$query="SELECT `id` FROM `posts_report` WHERE `post_g` = '0' AND ".$curtime."-`post_t`>300 AND `id_user` = '".$user_id."' LIMIT 1;";
					$rs = mysql_query($query);

					if (mysql_num_rows($rs) > 0) {

						$r = mysql_fetch_array($rs);

						$query = "UPDATE `posts_report` SET `comment` = '".$reason." <i>(Terminal 02)</i>', `post_g` = '".$curtime."' WHERE `id` = '".$r['id']."' LIMIT 1;";

						mysql_query($query);

			//================= И шлём телегу снятому ======================
						$message = "Я тебя снял с поста! Причина: ".date("d.m.Y H:i", $curptime)." - ".$reason.". Твой любимый, Terminal 02";
						$message = "\nКсакеп || ".$name." || ".$message."\n";

						$filename = "/home/sites/police/bot_fees/alerts.txt";

						if (file_exists($filename)){
							//		echo "file_exists";
							chmod($filename, 0777);
						}

					// Открываем $filename в режиме "дописать в конец".
						if ($handle = fopen($filename, 'a')) {
						// Записываем в открытый файл.
							if (fwrite($handle, $message) === FALSE) {
				//				$text = "Не возможно произвести запись в файл (".$filename.")";
				//				$noerror=0;
							}
					//		$text = "Записали (".$message.") в файл (".$filename.")";
							fclose($handle);
						}
			//=============================================================
					}
				}
			}
		}  

	//========================================================================

	}

?>