#!/usr/bin/php -q
<?php
//error_reporting(E_ALL);
// */17 * * * * /usr/bin/php /home/sites/police/www/_modules/rating_arh/cron_users_update.php
	set_time_limit (120);
// Игнорировать STOP:
//	ignore_user_abort(true);

	$DOCUMENT_ROOT = '/home/sites/police/www';
	$path_to_php = '/_modules/rating_arh';

	require('/home/sites/police/dbconn/dbconn_persistent.php');
	$connection = $db;
// Открываем соединение с базой данных (если не открывается, тогда ничего не выводим, а просто выходим):
//	$connection = mysql_connect ($hostName, $userName, $password);
// Выбираем базу данных (если выбрать не получается, то просто выходим):
//	$database = mysql_select_db ($databaseName, $connection);
	
	include_once ($DOCUMENT_ROOT.'/_modules/functions.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/config.php');
//	include_once ($DOCUMENT_ROOT.$path_to_php.'/function.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/other.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/tz_plugins.php');

//ЛИМИТЫ НЕ УВЕЛИЧИВАТЬ!	
	$limit = '250';
	
	if(date('H', time())>4 && date('H', time())<11){
		$limit = '400';
	}
	if(date('H', time())>14 && date('H', time())<19){
		$limit = '100';
	}

	$sSQL = 'SELECT `name`, `level` FROM `'.$db['tz_users'].'` ORDER BY `upd_time` ASC LIMIT '.$limit.';';
// все кто не появлялся больше 3 дней
//	$sSQL = "SELECT * FROM `".$db["tz_users"]."` WHERE upd_time<".(time()-259200)." ORDER BY upd_time ASC LIMIT ".$limit.";";
	$result = mysql_query($sSQL,$connection);
	
//	$text = "";
	while($row = mysql_fetch_assoc($result)){
//		$text .= $row["name"]." [".$row["level"]."]";
		
//		$tmp_page = fant_TZConn($row['name'], 0);
          
                // Inviz 26.10.11
                $userinfo = GetUserInfo($row['name'], 0);
		/*$tmp_page = TZConn($row['name'], 0); 
		if(!is_array($tmp_page)){  
			$dq = 'DELETE FROM `data_cache` WHERE `user_name`="'.$row['name'].'";';
			mysql_query($dq);
			$dq = 'INSERT INTO `data_cache` (`user_name`, `data_str`, `lastupdate`) VALUES ("'.$row['name'].'", "'.$tmp_page.'", "'.time().'");';
			mysql_query($dq);
			$userinfo = fant_ParseUserInfo($tmp_page);
			fant_tz_users_update($userinfo);
			
//			$text .= " - Update";
		}elseif($tmp_page['error'] == 'USER_NOT_FOUND' || $tmp_page['error'] == 'ERROR_IN_USER_NAME'){
			$set = '`upd_time` = '.time();
			$query = 'UPDATE `tzpolice_tz_users` SET '.$set.' WHERE `name`="'.$row['name'].'";';
			mysql_query($query);
//			$text .= " - <B>".$tmp_page["error"]."</B>";
		}else{
//			$text .= " - <B>".$tmp_page["error"]."</B>";
		}*/
		
//		$text .= "<BR>\n";
	}
	
//	echo $text;
	mysql_close ($connection);
	
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
