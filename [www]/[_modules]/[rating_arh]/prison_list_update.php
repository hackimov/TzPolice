<?php
//error_reporting(E_ALL);
// */17 * * * * /usr/bin/php /home/sites/police/www/_modules/rating_arh/cron_users_update.php
	set_time_limit (0);
// Игнорировать STOP:
//	ignore_user_abort(true);

	$DOCUMENT_ROOT = '/home/sites/police/www';
	$path_to_php = '/_modules/rating_arh';

//	require('/home/sites/police/dbconn/dbconn_persistent.php');
//	$connection = $db;
// Открываем соединение с базой данных (если не открывается, тогда ничего не выводим, а просто выходим):
//	$connection = mysql_connect ($hostName, $userName, $password);
// Выбираем базу данных (если выбрать не получается, то просто выходим):
//	$database = mysql_select_db ($databaseName, $connection);
	
//	include_once ($DOCUMENT_ROOT.'/_modules/functions.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/config.php');
//	include_once ($DOCUMENT_ROOT.$path_to_php.'/function.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/other.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/tz_plugins.php');

//=========================
	include("/home/sites/police/dbconn/dbconn2.php");
//=========================
	$sSQL = 'SELECT * FROM `prison_chars`;';
	$result = mysql_query($sSQL, $link);
	include "/home/sites/police/www/_modules/functions.php";

/*
//ЛИМИТЫ НЕ УВЕЛИЧИВАТЬ!	
	$limit = '250';
	
	if(date('H', time())>4 && date('H', time())<11){
		$limit = '750';
	}
	if(date('H', time())>14 && date('H', time())<19){
		$limit = '50';
	}
*/
//	$sSQL = 'SELECT name, level FROM `'.$db['tz_users'].'` ORDER BY `upd_time` ASC LIMIT '.$limit.';';
// все кто не появлялся больше 3 дней
//	$sSQL = "SELECT * FROM `".$db["tz_users"]."` WHERE upd_time<".(time()-259200)." ORDER BY upd_time ASC LIMIT ".$limit.";";
//	$result = mysql_query($sSQL,$connection);
	
//	$text = "";
	while($row = mysql_fetch_assoc($result)){
//		$text .= $row["name"]." [".$row["level"]."]";
		echo $row['nick'].' ['.$row['add_level'].'] ['.$row['level'].']';
		
                // Inviz 26.10.11
                $userinfo = GetUserInfo($row['nick'], 0);
		//$tmp_page = fant_TZConn($row['nick'], 0);
	//	print_r($tmp_page);
		//$userinfo = fant_ParseUserInfo($tmp_page);

		//if(!is_array($tmp_page)){
			if(intval($userinfo['pro']) != 13){
				$sql = 'DELETE FROM `prison_chars` WHERE `nick`="'.$row['nick'].'";';
				mysql_query($sql, $link);
				echo ' - Delete - '.$prof_alt[$userinfo['pro']];
			
			}else{
				$sql = 'UPDATE `prison_chars` SET level='.$userinfo['level'].' WHERE id='.$row['id'];
				mysql_query($sql, $link);
				echo ' - Update - ['.$userinfo['level'].']';
			}
			
		//}elseif($tmp_page['error'] == 'USER_NOT_FOUND' || $tmp_page['error'] == 'ERROR_IN_USER_NAME'){
		//	$sql = 'DELETE FROM `prison_chars` WHERE `nick`="'.$row['nick'].'";';
		//	mysql_query($sql, $link);
		//	echo ' - Delete - '.$tmp_page['error'];
//			$text .= " - <B>".$tmp_page["error"]."</B>";
			
		//}else{
//			$text .= " - <B>".$tmp_page["error"]."</B>";
		//	echo ' - Error';
		//}
		
//		$text .= "<BR>\n";
		echo "<BR>\n";
	}
	
//	echo $text;
	mysql_close ($link);
	
/*	
	$message = $text;
	
	$headers = "From: TZPOLICE.RU<fantastish2001@mail.ru>\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-Type: text/html; charset=windows-1251\n";
	$headers .= "X-Mailer: TZPOLICE.RU\n";
	$headers .= "X-Priority: 3\n";
	$headers .= "Return-Path: <fantastish2001@mail.ru>\n";
	$headers .= "Content-Transfer-Encoding: 8bit\n";
		
	$subj = "тзполис-юзерапдейт-крон: ".date("d/m/Y H:i", time());
	$subj = '=?koi8-r?B?'.base64_encode(convert_cyr_string($subj, "w","k")).'?=';

	mail("fantastish2001@mail.ru", $subj, $message, $headers);
*/
?>
