<?

// название текущего раздела
	$menu = 'rating_arh';
	$path_to_php = '/_modules';
///////////////////////////////////////////////////////////////////////////////////
// Определяем полный путь к ROOT'у сервера [вырезаем возможный "/" из конца строки]:
// /home/sites/police/www
	$DOCUMENT_ROOT = ereg_replace ("/$", "", $HTTP_SERVER_VARS['DOCUMENT_ROOT']);
// Определяем как называется наш сервер:
	$SERVER_NAME = $HTTP_SERVER_VARS['SERVER_NAME'];
// Определяем имя файла из которого идет запуск:
	$PHP_SELF = basename (@$HTTP_SERVER_VARS['PHP_SELF']);
// Определяем строку запроса URL:
	$REQUEST_URI = $HTTP_SERVER_VARS['REQUEST_URI'];
// Опредляем текущую строку запроса (в броузере):
	$QUERY_STRING = $HTTP_SERVER_VARS['QUERY_STRING'];
// Наша ссылка будет всегда содержать WWW
	$HTTP_HOST = $HTTP_SERVER_VARS['HTTP_HOST'];

//-----------------------------------------------------------------------------
// Получаем все наши переменные (на случай, если в php.ini "register_globals = Off"):

	while (list ($key, $val) = each ($HTTP_GET_VARS)) $$key = $val;
	while (list ($key, $val) = each ($HTTP_POST_VARS)) $$key = $val;

// дополнительно:
	if (isset ($HTTP_GET_FILES['img'])) { $img = $HTTP_GET_FILES['img']; }
	if (isset ($HTTP_POST_FILES['img'])) { $img = $HTTP_POST_FILES['img']; }


///////////////////////////////////////////////////////////////////////////////////
// Открываем соединение с базой данных (если не открывается, тогда ничего не выводим, а просто выходим):
	//$connection = mysql_connect ($hostName, $userName, $password);
	$connection = $db;
// Выбираем базу данных (если выбрать не получается, то просто выходим):
//	$database = mysql_select_db ($databaseName, $connection);
///////////////////////////////////////////////////////////////////////////////////
// подключаем все что требуеца
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/config.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/other.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/tz_plugins.php');

// начальники
//	$sSQL = "SELECT `chief` FROM `sd_cops` WHERE `name` = '".mysql_escape_string(AuthUserName)."' LIMIT 1;";
//	$result = mysql_query($sSQL);
//	if(mysql_num_rows($result)>0){
//		$row = mysql_fetch_assoc($result);
//		$chief = $row['chief'];
//	}else{
//		$chief = '0';
//	}
//echo $chief;
//------- топ лист по наложению молчанок -------------
	function toplist ($x_value, $months_a, $time, $connection, $cops_action){

/*		$query = 'SELECT `id`, `name` FROM `sd_depts` ORDER BY `name` ASC';
		$tmp = mysql_query($query, $connection);
		while ($rslt = mysql_fetch_assoc($tmp)){
		 	$dept[$rslt['id']] = $rslt['name'];

		 	$list_depts .= '<OPTION VALUE=\''.$rslt['id'].'\''.(($rslt['id']==$x_value[2])?' selected':'').' CLASS="select">'.$rslt['name']."\n";
		}
*/
		if(!isset($time)){
			$time = $time2 = time()-86000;
		}elseif(is_array($time)){
			$time2 = mktime(23, 59, 0, $time[4], $time[3], $time[5]);
			$time = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
		}

		if ($time>time()) $time = time();
		if ($time2>time()) $time2 = time();
		if ($time>$time2) $time = $time2;
		if ($time2<$time) $time2 = time();

	//	корректировка
		$time = date("d:m:Y:H:i", $time);
		$c_time = $time = explode(':',$time);
		$time = mktime(0, 0, 0, $time[1], $time[0], $time[2]);

		$time2 = date("d:m:Y:H:i", $time2);
		$c_time2 = $time2 = explode(':',$time2);
		$time2 = mktime(23, 59, 0, $time2[1], $time2[0], $time2[2]);

		$x_value[0] = 0;

		$text = title('Вспомнить всё<BR>'.date("d.m.Y", $time).' 00:00 - '.date("d.m.Y", $time2).' 23:59');

		$text .= "<BR><DIV STYLE=\"WIDTH:100%;\">\n";

		$text .= "<FORM METHOD=\"GET\">\n";
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"personal_silents\">\n";
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"top\">\n";
		$text .= "<TABLE WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\">\n";
		$text .= " <TR VALIGN=\"top\">\n";
		$text .= "  <TD ALIGN=\"left\"><B>Дата c:&nbsp;</B>\n";
		$text .= "   <SELECT NAME=\"time[0]\">\n";
		for($i=1;$i<=31;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[0])?' selected':'')." CLASS='select'>".$i."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[1]\">\n";
		for($i=1;$i<=12;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[1])?' selected':'')." CLASS='select'>".$months_a[$i]."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[2]\">\n";
		for($i=2007;$i<=date("Y");$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[2])?' selected':'')." CLASS='select'>".$i."\n";
		}
		$text .= "   </SELECT>&nbsp;<B>00:00</B>\n";

		$text .= "  <B>&nbsp;-&nbsp;по:&nbsp;</B>\n";
		$text .= "   <SELECT NAME=\"time[3]\">\n";
		for($i=1;$i<=31;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time2[0])?' selected':'')." CLASS='select'>".$i."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[4]\">\n";
		for($i=1;$i<=12;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time2[1])?' selected':'')." CLASS='select'>".$months_a[$i]."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[5]\">\n";
		for($i=2007;$i<=date("Y");$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time2[2])?' selected':'')." CLASS='select'>".$i."\n";
		}
		$text .= "   </SELECT>&nbsp;<B>23:59\n";
		$text .= "<BR>   <input type=\"submit\" class=\"submit\" value=\"Вывести >>\">\n";
		$text .= "  </TD>\n";
		$text .= " </TR>\n";
/*
		$text .= " <TR VALIGN=\"top\">\n";
		$text .= "  <TD ALIGN=\"left\"><B>Действие:&nbsp;</B>\n";
		$text .= "<SELECT NAME=\"x_value[0]\">\n";
		$text .= '<OPTION VALUE="0"'.(($x_value[0]==0)?' selected':'').' CLASS="select"> -ВСЕ- ';
		foreach($cops_action AS $id=>$name){
			$text .= '<OPTION VALUE="'.$id.'"'.(($id==$x_value[0])?' selected':'').' CLASS="select">'.$name."\n";
		}
		$text .= "   </SELECT>";
		$text .= "  <B>С подробностями:&nbsp;</B><input NAME=\"x_value[1]\" type=\"checkbox\" class=\"submit\" value=\"1\">&nbsp;&nbsp;\n";

		$text .= "  </TD>\n";
		$text .= " </TR>\n";

		$text .= " <TR VALIGN=\"top\">\n";
		$text .= "  <TD ALIGN=\"left\"><B>Отдел:&nbsp;</B>\n";
		$text .= "   <SELECT NAME=\"x_value[2]\">\n";
		$text .= "<OPTION VALUE='0' CLASS=\"select\"> -- Все -- \n";
		$text .= $list_depts;
		$text .= "   </SELECT>\n";
		$text .= "  </TD>\n";
		$text .= " </TR>\n";
*/
		$text .= "</FORM>\n";
		$text .= "</TABLE><BR>\n";

		$text .= " <script language=\"JavaScript\" type=\"text/JavaScript\">\n";
		$text .= " <!--\n";
		$text .= " function hide(menu){\n";
		$text .= "  var c=document.getElementById(menu);\n";
		$text .= "  if (c.style.display != 'none') {\n";
		$text .= "   c.style.display = 'none';\n";
		$text .= "  }else{\n";
		$text .= "   c.style.display = '';\n";
		$text .= "  }\n";
		$text .= "  return false;\n";
		$text .= " }\n";
		$text .= " //-->\n";
		$text .= " </script>\n";

		$where_date = '`time` >= '.($time-1).' AND `time` <= '.($time2+1);
//		if($x_value[0] != 0){
//			$where_date .= ' AND `action` = \''.$x_value[0].'\'';
//		}
		$text .= "<DIV ALIGN=left>\n";
/*		$SQL = "SELECT `tzpolice_tz_users`.name AS `cop`, COUNT(`cops_actions`.`action`) AS `cnt`, `cops_actions`.`cop_id` AS `cop_id` FROM `cops_actions`, `tzpolice_tz_users` WHERE `tzpolice_tz_users`.`id`='".AuthUserId."' AND `cops_actions`.`cop_id`=`tzpolice_tz_users`.`id` AND ".$where_date." GROUP BY `cops_actions`.`cop_id` ORDER BY `cnt` DESC;";
		$sssqc = mysql_query($SQL, $connection);
		$i = 1;
		$total = 0;
		while($row = mysql_fetch_assoc($sssqc)){

			if($x_value[2]!=0){
				$sSQL4 = 'SELECT `dept` FROM `sd_cops` WHERE `name` = \''.$row['cop'].'\' LIMIT 1;';
				$result4 = mysql_query($sSQL4, $connection);
				$row4 = mysql_fetch_assoc($result4);
			}else{
				$row4['dept'] = 'xz';
			}
			if(($x_value[2]>0 && $row4['dept']==$x_value[2]) || $x_value[2]==0){

				if($x_value[1]==1) {

//
*/
					$text .= "{_PERS_}".AuthUserName."{_/PERS_}";

					$SQL="SELECT `id` FROM `tzpolice_tz_users` WHERE `name`='".AuthUserName."' LIMIT 1;";

					$result = mysql_query($SQL);
					$tmp = mysql_fetch_array($result);
					$tz_user_id = $tmp[0];
					//$tz_user_id = 217227;
					//echo ($tz_user_id."<br>");



					$SQL2 = 'SELECT `tzpolice_tz_users`.name AS `user`, `cops_actions`.* FROM  `cops_actions`, `tzpolice_tz_users` WHERE `cops_actions`.`user_id`=`tzpolice_tz_users`.`id` AND '.$where_date.' AND `cop_id`=\''.$tz_user_id.'\' ORDER BY `time` DESC;';
//					echo ($SQL2);
					$sssqc2 = mysql_query($SQL2, $connection);
					$total=0;
//					$total2=[];
					$text .= "<DIV onclick=\"hide('wannasee');\" STYLE=\"PADDING-TOP: 2px; cursor: hand\"><A HREF=\"javascript:{}\">А поподробнее?</A></DIV>\n";
					$text .= "<DIV ID='wannasee' style='display:none;'>\n";
					while($row2 = mysql_fetch_assoc($sssqc2)){
						//('.$row2['user_id'].')
						$text .= date("d/m/Y H:i", $row2['time']).' - '.$cops_action[$row2['action']].' ('.$row2['action_time'].') - {_PERS_}'.$row2['user'].'{_/PERS_} - '.$row2['text']."<BR>\n";
						$total++;
						if ($row2['action']==175 && $row2['action_time']==0)
							{
								$total2[909]=$total2[909]+1;
							}
						else
							{
								$total2[$row2['action']]=$total2[$row2['action']]+1;
							}

					}

					$text .= "</DIV>\n";

//				} else {
//					$text .= $i.'. {_PERS_}'.$row['cop'].'{_/PERS_} ['.$row['cnt'].']<BR>';
//				}
//				$i++;
		$text .= '______________<BR>ВСЕГО: '.$total;
//		print_r($total2);
		foreach ($total2 as $key => $value)
			{
				$text .= '<BR>'.$cops_action[$key].': '.$value;
			}
//		$text .= "</DIV>\n";

		return $text;
	}

/////////////////////////////////////////////////////////////////////
	if(!isset($action)) $action = 'top';

	if($action == 'upload'){
//		if($full_access==1){
//			$text = silents_log_upload ($ok, $x_value, $img, $DOCUMENT_ROOT, $connection);
//		}

	}elseif($action == 'top'){
//		if($full_access==1){
			$text = toplist ($x_value, $months_a, $time, $connection, $cops_action);
		}
//
//	}else{
//
//	}
///////////////////////////////////////////////////////////////////

//	if($full_access==1){
//		$menu = "<SMALL>\n";

//	$menu .= "<A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=silents&action=upload\">Загрузка лога</A>\n";
//		$menu .= "<A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=silents&action=top\">Топы</A>\n";
//		$menu .= "<HR>\n";

//		$menu .= "</SMALL>\n";

//		$text = $menu.$text;
//	}

	$text = tz_tag_remake($text);
	$text = change_pre_value($SERVER_NAME, $PHP_SELF, $path_to_images, $text);

	echo '<h1>Шо вчера было?</h1>';
	echo $text;
?>
