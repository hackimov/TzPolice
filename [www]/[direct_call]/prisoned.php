<?


	Header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	Header('Cache-Control: no-cache, must-revalidate');
	Header('Pragma: no-cache');
	Header('Last-Modified: '.gmdate("D, d M Y H:i:s").'GMT');

	require('/home/sites/police/www/_modules/functions.php');
	require('/home/sites/police/www/_modules/auth.php');

//=========================
	require_once('/home/sites/police/dbconn/dbconn2.php');
//=========================
#if (AuthStatus==1 && (substr_count(AuthUserRestrAccess, '-prison-') > 0 || AuthUserClan == 'police' || AuthUserName=='Deorg')) {
	function anti_sql($text){
	// транслирем все кавычки
		$text = str_replace("'", "&#039;", $text);
		$text = str_replace("’", "&#039;", $text);
		$text = str_replace("\"", "&quot;", $text);
		$text = str_replace("`", "&#039;", $text);

		return $text;
	}
	#логи
	function imsg($subj) {
		$today = date('d.m.Y');
	    $logfile = "logs/prisoned-".date('d.m.y').".html";
		$file = fopen($logfile,'a');
	    fwrite($file,date("H:i:s").",".$subj."\n");
	    fclose($file);
	}


	if(!isset($_SERVER['HTTP_X_FLASH_VERSION'])) {
		$log = "Hack: ".$_REQUEST['login']."\nCookies: ";
		foreach($_COOKIE as $k => $v) {
			$log .= "$k => $v | ";
		}
		$log .= "\nIp: ".$_SERVER['HTTP_X_FORWARDED_FOR']."\nFlash: ".$_SERVER['HTTP_X_FLASH_VERSION']."\n\n";
		imsg($log);
		die("Попытка использования закрытой информации. Данные успешно сохранены.");
	}

	$nick = anti_sql(trim(strip_tags($_REQUEST['login'])));

	$lang = $_REQUEST['lang'];
	$query = 'SELECT `log_date`, `log_time` FROM `bot_prison_logs` ORDER BY `id` DESC LIMIT 1;';
	$rs = mysql_query($query) or die (mysql_error());
	list($last_date, $last_time) = mysql_fetch_row($rs);
	$ld = $last_date.' '.$last_time;
	$last_date = strtotime($ld);
if ($_SERVER['REMOTE_ADDR'] == '82.146.63.5' || $_SERVER['REMOTE_ADDR'] == '87.118.114.99')
	{
		$query = 'SELECT * FROM `prison_chars` WHERE `nick` = \'Big Brother\' LIMIT 1;';
	}
else
	{
		$query = 'SELECT * FROM `prison_chars` WHERE `nick` = \''.$nick.'\' LIMIT 1;';
	}
	$rez = mysql_query($query) or die(mysql_error());
	if (mysql_num_rows($rez) > 0) {
	//	list($c_id, $c_nick, $c_term, $c_coll, $c_l_pay, $c_reas, $c_rem, $c_dept, $add_date, $add_by, $add_level, $allow_udo, $want_udo, $answer_udo, $coll_by_rating) = mysql_fetch_row($rez);
		$row = mysql_fetch_assoc($rez);
		$id = $row['id'];
		$nick = $row['nick'];
		$term = $row['term'];
		$coll = $row['collected'];
		$last_pay = $row['last_pay'];
		$reason = $row['reason'];
		$remark = $row['remark'];
		$c_dept = $row['dept'];
		$c_add = $row['add_date'];
		$c_addby = $row['add_by'];
		$addlevl = $row['add_level'];
		$allow_udo = $row['allow_udo'];
		$want_udo = $row['want_udo'];
		$answerudo = $row['answer_udo'];
		$ratingres = $row['coll_by_rating'];

		if ($reason > 0) {
			if ($lang=='en') {
				$motive = $crime_en[$reason];
			} else {
				$motive = $crime[$reason];
			}

			$udo = $allow_udo;
			if($allow_udo=='1'){
				$need_udo = ($term*0.3)-$coll;
				if($need_udo<=0){
					$summa = ($term-$coll)*1.5;
					$udo .= '/'.round($summa).'/'.$want_udo;
				} else {
					$udo .= '/'.$need_udo;
				}
			} else {
				$udo .= '/0';
			}

			$str = '<ok date="'.$last_date.'" motive="'.$motive.'" res="'.$coll.'/'.$term.'/'.$last_pay.'" udo="'.$udo.'" />';

		} else {
			$str = '<error code="2" date="'.$last_date.'"/>';

		}

	} else {
		$str = '<error code="1" date="'.$last_date.'"/>';

	}

	echo iconv("cp1251", "UTF-8", $str);
	mysql_close($link);
#}
?>