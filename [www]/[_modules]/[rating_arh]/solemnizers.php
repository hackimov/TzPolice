<h1>Регистрация браков/разводов</h1>
<?
////////////////////////////////////////////////////////
$m_users=array('Amazonka', 'D i J i', 'Fribble', 'Invisible', 'shturm', 'Утопленница', 'MyJlbTuk');

////////////////////////////////////////////////////////

// название текущего раздела
	$menu = 'rating_arh';
	$path_to_php = '/_modules';
///////////////////////////////////////////////////////////////////////////////////
// Определяем полный путь к ROOT'у сервера [вырезаем возможный "/" из конца строки]:
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
// Наша ссылка будет всегда содержать WWW
	$HTTP_HOST = $HTTP_SERVER_VARS['HTTP_HOST'];

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
?>

<b>Полиция TimeZero проводит регистрацию и расторжение браков.</b><br>
<br>
Для того, чтобы официально оформить свои отношения, вам необходимо: <br>
1. Взаимное согласие жениха и невесты.<br> 
2. Приобрести свадебные кольца в любом цветочном магазине. (Кольцо, которое будет носить невеста, должно быть отправлено невесте, кольцо жениха - жениху. На церемонии необходимо держать кольца в руках.) <br>
3. Договориться с полицейским о проведении бракосочетания (браки, регистрируемые  сотрудниками полиции, БЕСПЛАТНЫ и проводятся без церемонии )<br>
4.  Расторжение брака стоит  1000мм. (оплачивает инициатор развода)<br>
<br>
Для того что бы совершить развод, вам необходимо:<br>
1. Согласие любого из состоящего в брачном союзе.<br>
2. Сообщить о своем желании уполномоченному полицейскому.<br>
<br>
Информацию о проведении свадебных церемоний вы можете прочитать по адресу <A HREF="http://stalkerz.ru/?a=pub&id=777" TARGET="_blank">http://stalkerz.ru/?a=pub&id=777</A>
<BR><BR>
Для быстрого проведения свадьбы необходимо:<BR>
1. Взять в руки кольца<BR>
2. Написать одному из ниже перечисленных полицейских согласие заключить брак.<BR><BR>
<B>Пример:</B><BR>
00:00 [игрок 1] pravate [игрок 2] private [полицейский] Здравствуйте. Пожените нас пожалуйста.<BR>
00:00 [игрок 2] pravate [игрок 1] private [полицейский] Я согласен(а).<BR>
<hr size=1>

<?php	
//error_reporting(E_ALL);
	
	$w_users = implode("' OR `nick` = '", $m_users);
	
/*	$sSQL = "SELECT `nick`, `status` FROM `mp_online` WHERE `logout`='0' AND (`nick` = '".$w_users."') ORDER BY `status` ASC, `nick` ASC";
	$result = mysql_query($sSQL, $connection);
	while($row = mysql_fetch_array($result)){
		$users[$row["nick"]] = $row["status"];
	}
*/	
	$sSQL = 'SELECT `nick`, `status` FROM `cops_online` WHERE `logout`=\'0\' AND (`nick` = \''.$w_users.'\') ORDER BY `status` ASC, `nick` ASC';
	$result = mysql_query($sSQL, $connection);
	while($row = mysql_fetch_assoc($result)){
		$users[$row['nick']] = $row['status'];
	}
	
	sort ($m_users);
	reset ($m_users);
	
	$nrows = count($m_users);
	
	if($nrows>0){
		$text .= "<DIV><SMALL>*Информация обновляется каждые 5 минут</SMALL></DIV>\n";
		$text .= "<TABLE WIDTH=\"85%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\" ALIGN=center>\n";
//		$text .= "<TR BGCOLOR=\"#E4DDC5\" ALIGN=\"center\">\n";
//		$text .= " <TD><B>Логин</B></TD>\n";
//		$text .= " <TD><B>Отдел</B></TD>\n";
//		$text .= "</TR>\n";
		$bgcolor[1]='#F5F5F5';
		$bgcolor[2]='#E4DDC5';
		$i=1;
		$i2=0;
		
		while($i2<$nrows){
			if($i>2) $i=1;
			$text .= '<TR BGCOLOR="'.$bgcolor[$i]."\">\n";
			
			$text .= " <TD VALIGN=top WIDTH=10>";
			if(array_key_exists($m_users[$i2],$users)){
				if($users[$m_users[$i2]]=='1'){
					$text .= "<FONT COLOR=\"red\"><B>ChatOff</B></FONT>";
				}else{
					$text .= "<FONT COLOR=\"green\"><B>OnLine</B></FONT>";
				}
			}else{
				$text .= 'OffLine';
			}
			$text .= "</TD>\n";
			
			$sSQL3 = 'SELECT `clan_id`, `name`, `pro`, `sex`, `level` FROM `'.$db['tz_users'].'` WHERE `name`=\''.$m_users[$i2].'\'';
			$result3 = mysql_query($sSQL3, $connection);
			if(mysql_num_rows($result3)>0){
				$row3 = mysql_fetch_assoc($result3);
				if($row3['clan_id']>0){
					$sSQL2 = 'SELECT `name` FROM `'.$db['tz_clans'].'` WHERE `id`=\''.$row3['clan_id'].'\'';
					$result2 = mysql_query($sSQL2, $connection);
					$row2 = mysql_fetch_assoc($result2);
					$clan = '{_CLAN_}'.trim($row2['name']).'{_/CLAN_}';
				}else{
					$clan = '';
				}

				$text .= ' <TD VALIGN=top>'.(($row['status']=='1')?'[Chat: Off] ':'').$clan.' <A HREF="javascript:{}" OnClick="ClBrd(\'private ['.stripslashes($row3['name']).']\');" TITLE="private ['.stripslashes($row3['name']).']">'.stripslashes($row3['name']).'</A> ['.$row3['level'].'] {_PROF_}'.$row3['pro'].(($row3['sex']=='0')?'w':'')."{_/PROF_}</TD>\n";
			}else{
				$text .= " <TD VALIGN=top><A HREF=\"javascript:{}\" OnClick=\"ClBrd('private [".stripslashes($m_users[$i2])."]');\" TITLE=\"private [".stripslashes($m_users[$i2])."]\">".stripslashes($m_users[$i2])."</A></TD>\n";
			}
			$text .= "</TR>\n";
			$i++;
			$i2++;
		}
		
		$text .= "</TABLE>\n";
		$text .= "<CENTER>Всего: ".$nrows." человек</CENTER>\n";
	
	}else{
		$text .= "<CENTER>никого нет</CENTER>";
	}

	$text = tz_tag_remake($text);
	$text = change_pre_value($SERVER_NAME, $PHP_SELF, $path_to_images, $text);
	
	echo $text;
?>
<hr size=1>
<b>Уважаемые игроки!</b><br><br>
В данный момент все бракосочетания проводятся без церемонии.