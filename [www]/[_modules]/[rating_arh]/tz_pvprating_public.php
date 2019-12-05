<?
/****************************************************
*	 Lebedev Sergey (fantastish@rambler.ru)			*
****************************************************/
///////////////////////////////////////////////////////////////////////////////////
// Определяем и корректируем основные пути и прочее
///////////////////////////////////////////////////////////////////////////////////
// название текущего раздела
	$menu = 'rating_arh';
	$path_to_php = '/_modules';

///////////////////////////////////////////////////////////////////////////////////
// Определяем полный путь к ROOT'у сервера [вырезаем возможный "/" из конца строки]:
if ($_REQUEST['kind'] == 'pvp')
	{
		$ratingtype = 'pvp';
	}
else
	{
		$ratingtype = '';
	}
// /home/sites/police/www
	$DOCUMENT_ROOT = ereg_replace ("/$", '', $HTTP_SERVER_VARS['DOCUMENT_ROOT']);
// Определяем как называется наш сервер:
	$SERVER_NAME = $HTTP_SERVER_VARS['SERVER_NAME'];
// Определяем имя файла из которого идет запуск:
	$PHP_SELF = basename (@$HTTP_SERVER_VARS['PHP_SELF']);
// Определяем строку запроса URL:
	$REQUEST_URI = $HTTP_SERVER_VARS['REQUEST_URI'];
// Опредляем текущую строку запроса (в броузере):
	$QUERY_STRING = $HTTP_SERVER_VARS['QUERY_STRING'];
// Опредляем "откуда" пришли (referer):
	if (isset ($HTTP_SERVER_VARS['HTTP_REFERER'])) {
		$HTTP_REFERER = $HTTP_SERVER_VARS['HTTP_REFERER'];
	} else {
		$HTTP_REFERER = '';
	}
// Определяем HOST:
	if (isset ($HTTP_X_FORWARDED_FOR)) {
		$host = @gethostbyaddr ($HTTP_X_FORWARDED_FOR);
	} else {
		$host = @gethostbyaddr ($REMOTE_ADDR);
	}

// Определяем IP:
	if(getenv('HTTP_X_FORWARDED_FOR') != '' ){
		$client_ip = (!empty($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ((!empty($HTTP_ENV_VARS['REMOTE_ADDR'])) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : $REMOTE_ADDR);

		$entries = explode(',', getenv('HTTP_X_FORWARDED_FOR'));
		reset($entries);
		while(list(, $entry) = each($entries)){
			$entry = trim($entry);
			if(preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list)){
				$private_ip = array('/^0\./', '/^127\.0\.0\.1/', '/^192\.168\..*/', '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/', '/^10\..*/', '/^224\..*/', '/^240\..*/');
				$found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
				if ($client_ip != $found_ip){
					$client_ip = $found_ip;
					break;
				}
			}
		}
	}else{
		$client_ip = (!empty($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ((!empty($HTTP_ENV_VARS['REMOTE_ADDR'])) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : $REMOTE_ADDR);
	}
	$ip = $client_ip;

// Наша ссылка будет всегда содержать WWW
	$HTTP_HOST = $HTTP_SERVER_VARS['HTTP_HOST'];
//	if(!eregi("www.",$HTTP_HOST)) Header("Location: http://www.".$SERVER_NAME.$REQUEST_URI."");
//	if(!eregi("www.",$SERVER_NAME)) $SERVER_NAME="www.".$SERVER_NAME;

// Получение глобальных переменных
	while (list ($key, $val) = each ($HTTP_GET_VARS)) $$key = $val;
	while (list ($key, $val) = each ($HTTP_POST_VARS)) $$key = $val;

//	require("/home/sites/police/dbconn/dbconn.php");

///////////////////////////////////////////////////////////////////////////////////
// Открываем соединение с базой данных (если не открывается, тогда ничего не выводим, а просто выходим):
	//$connection = mysql_connect ($hostName, $userName, $password);
	$connection = $db;
// Выбираем базу данных (если выбрать не получается, то просто выходим):
//	$database = mysql_select_db ($databaseName, $connection);

///////////////////////////////////////////////////////////////////////////////////
// подключаем все что требуеца
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/config.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/tz_rating_function.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/search.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/other.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/tz_plugins.php');

//	include_once ($DOCUMENT_ROOT.$path_to_php."/functions.php");

///////////////////////////////////////////////////////////////////////////////////
// Основаная часть раздела
///////////////////////////////////////////////////////////////////////////////////
//
// !!!!!!!!!!!!!определяем тут права на вывод инфы и прочее!!!!!!!
//
	if(!isset($action)) $action='view';

	$sSQL = 'SELECT `dept` FROM `sd_cops` WHERE `name` = "'.AuthUserName.'" LIMIT 1;';
	$result = mysql_query($sSQL);
	$row = mysql_fetch_assoc($result);
// 15 - Отдел исполнения наказаний
// 12 - Отдел прокачек
	$menu_dept_id = $row['dept'];

// полный доступ токо полисам и программистам )))
	if(AuthUserGroup == 100 || $menu_dept_id=="12") {
		$full_access=1;
	}else
		$full_access=0;

///////////////////////////////////////////////////////////////////////////////////

	if($action=='view'){
		$text = rating_screen_view ($full_access, $months_a, $menu, $cid, $time, $connection, $db, $id, $action, $max_level, $ratingtype.'rating_screen');

	}elseif($action=='add_to_check'){
		if($full_access==1){
		/*==========
			`status`='1' - Список отправленных на проверку
		===========*/
			$status = '1';

			$x_value=explode('|',$x_value);

			$i=1;
			$ft=0;
			$names=array();
			$array_size = sizeof($x_value);
			while($i<$array_size){
				if(trim($x_value[$i])!=''){
					$sSQL1 = 'SELECT COUNT(`id`) FROM `'.$db['rating_check'].'` WHERE `name_id`='.intval(trim($x_value[$i])).' AND `status` IN (1, 2)';
					$result1 = mysql_query($sSQL1, $connection);
					$row1 = mysql_fetch_array($result1);
					if($row1[0]>0){
						$names[] = intval(trim($x_value[$i]));

					}else{

						$sSQL = 'INSERT INTO '.$db['rating_check'].' SET `name_id`='.intval(trim($x_value[$i])).', `uid`='.AuthUserId.', `time`='.time().', `status`='.$status;

						if(mysql_query($sSQL, $connection))
							$ft++;
					}
				}
				$i++;
			}

			$text = title('Отправлено на проверку - '.$ft.' чаров');
			if(sizeof($names)>0){
				$text .= rating_add_recheck($connection, $db, $names, '0');
			}else{
				$text .= rating_users_on_check ('on_check', $connection, $db, $page);
			}
		}

	}elseif($action=='add_to_check2' || $action=='add_to_check3' || $action=='add_to_check4'){
		if($full_access==1){
		/*==========
			`status`='2' - Список проверяемых
			`status`='3' - Список проверенных: Без нарушений
			`status`='4' - Список проверенных: Прокачка
		===========*/
			if($action=='add_to_check2'){
				$status = '2';
			}elseif($action=='add_to_check3'){
				$status = '3';
			}elseif($action=='add_to_check4'){
				$status = '4';
			}

			$x_value = explode('|',$x_value);

			$i=1;
			$ft=0;
			$array_size = sizeof($x_value);
			while($i < $array_size){
				if(trim($x_value[$i])!=''){
				//	$sSQL = "SELECT * FROM `".$db["rating_check"]."` WHERE `id`='".intval(trim($x_value[$i]))."'";
				//	$result = mysql_query($sSQL, $connection);

			//		$sSQL = "UPDATE ".$db["rating_check"]." SET `uid`='".AuthUserId."', `time`='".time()."', `status`='".$status."' WHERE `id`='".intval(trim($x_value[$i]))."'";
					$sSQL = 'UPDATE '.$db['rating_check'].' SET `time`='.time().', `status`='.$status.' WHERE `id`='.intval(trim($x_value[$i]));

					if(mysql_query($sSQL,$connection))
						$ft++;
				}
				$i++;
			}

			if($action=='add_to_check2'){
				$text = title('Отправлено в список проверяемых - '.$ft.' чаров');
				$text .= rating_users_on_check ('check', $connection, $db, $page);
			}elseif($action=='add_to_check3'){
				$text = title('Отправлено в список проверенных: Без нарушений - '.$ft.' чаров');
				$text .= rating_users_on_check ('checked', $connection, $db, $page);
			}elseif($action=='add_to_check4'){
				$text = title('Отправлено в список проверенных: Прокачка - '.$ft.' чаров');
				$text .= rating_users_on_check ('checked2', $connection, $db, $page);
			}

		}

	}elseif($action=='on_check' || $action=='check' || $action=='checked' || $action=='checked2'){
		if($full_access==1){
			$text = rating_users_on_check ($action, $connection, $db, $page);
		}

	}elseif($action=='add_rechek'){
		if($full_access==1){
			$text = rating_add_recheck($connection, $db, $x_value, $ok);
		}

	}elseif($action=='user_edit'){
		if($full_access==1){
			$text = rating_users_activ ($connection, $db, $id, $ok, $x_value);
		}

	}elseif($action=='view2'){
		$text = rating_screen_view2 ($full_access, $months_a, $menu, $cid, $time, $connection, $db, $id, $action, $max_level, $ratingtype.'rating_screen');

	}elseif($action=='stars'){
		$text = rating_stars_view ($full_access, $months_a, $menu, $cid, $time, $connection, $db, $id, $action, $max_level, $ratingtype.'rating_screen');


	}elseif($action=='search'){
		if($full_access==1){
			$text = tzrating_search ($connection, $db, $id, $cid, $page, $ok, $x_value);
		}

	}elseif($action=='users_stats'){
		if ($full_access==1) {
			$text = all_tz_users_view ($prof_alt, $max_level, $connection, $db);
		}

	}elseif($action=='user_insert'){
		if($full_access==1){
			$text = rating_users_insert ($connection, $db, $id, $ok, $x_value);
		}

// Обновление глобальной базы по персам
	}elseif($action=='users_update'){
		if($full_access==1){
			$text = all_tz_users_update ($connection, $db);
		}

/*	}elseif($action=='test'){
		$sSQL2 = 'DELETE FROM `'.$db['rating_check'].'` WHERE id=1088;
		$result2 = mysql_query($sSQL2);
		$text = mysql_num_rows($result2);
*/
	}else{

	}

	if($full_access==1){
		$sSQL = 'SELECT COUNT(id) FROM `'.$db['rating_check'].'` WHERE `status`=1';
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_row($result);
		$nrows = $row[0];

		$sSQL = 'SELECT COUNT(id) FROM `'.$db['rating_check'].'` WHERE `status`=2';
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_row($result);
		$nrows2 = $row[0];

		$sSQL = 'SELECT COUNT(id) FROM `'.$db['rating_check'].'` WHERE `status`=3';
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_row($result);
		$nrows3 = $row[0];

		$sSQL = 'SELECT COUNT(id) FROM `'.$db['rating_check'].'` WHERE `status`=4';
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_row($result);
		$nrows4 = $row[0];

		$menu = "<SMALL>\n";
		$menu .= "<FORM METHOD=\"get\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\" name=\"select\">\n";

		$menu .= "<A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating\">Архив рейтинга</A>\n";
		$menu .= " | <A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating&kind=pvp\">Архив PVP-рейтинга</A>\n";
		$menu .= ' | <A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating&action=on_check">Отправленные на проверку ('.$nrows.")</A>\n";
		$menu .= ' | <A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating&action=check">Проверяемые ('.$nrows2.")</A>\n";
		$menu .= ' | <A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating&action=checked">Без нарушений ('.$nrows3.")</A>\n";
		$menu .= ' | <A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating&action=checked2">Прокачка ('.$nrows4.")</A><HR>\n";
		$menu .= "<A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating&action=user_insert\">Ручное Добавление на проверку</A>\n";
//		$menu .= " | <A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating&action=search\">Поиск по базе</A>\n";

		$menu .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"tzrating\">\n";
		$menu .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"search\">\n";
		$menu .= "<INPUT TYPE=\"hidden\" NAME=\"ok\" VALUE=\"1\">\n";
		$menu .= "| <B>Поиск:&nbsp;</B>\n";
		$menu .= "  <INPUT TYPE=\"text\" NAME=\"x_value[0]\" VALUE=\"\" SIZE=\"15\" style=\"width=150\" CLASS=\"text\">\n";
		$menu .= "  &nbsp;<INPUT TYPE=\"submit\" VALUE=\" Поиск \" CLASS=\"submit\">\n";
		$menu .= "<HR></FORM></SMALL>\n";

		$text = $menu.$text;
	}
///////////////////////////////////////////////////////////////////////////////////
// Обработка внутренних тегов
	$text = tz_tag_remake($text);
	$text = change_pre_value($SERVER_NAME, $PHP_SELF, $path_to_images, $text);

// ВЫВОД
	echo $text;

?>