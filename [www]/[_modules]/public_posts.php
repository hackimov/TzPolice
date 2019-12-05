<h1>Дежурные полицейские</h1>

<?
//=======МОСКВА=============
$nm_post = "<img src='i/bullet-red-01a.gif' width='18' hspace='5'><b>New Moscow</b><br><br>\n";

$SQL="SELECT p.*, u.id AS UID, u.user_name AS Uname FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user WHERE p.city=1 ORDER BY id DESC LIMIT 1";
$r=mysql_query($SQL);
$d=mysql_fetch_array($r);
if(mysql_num_rows($r)==0 || $d['post_g'] >0) {
	$nm_post .= "Сейчас на посту никого.";
} else {
    $nm_post .= "Сейчас на посту: <A HREF=\"javascript:{}\" OnClick=\"ClBrd('private [".stripslashes($d["Uname"])."]');\" TITLE=\"private [".stripslashes($d["Uname"])."]\"><b>".$d["Uname"]."</b></A>";
}
?>
<!--<br><br><br><img src='i/bullet-red-01a.gif' width='18' hspace='5'><b>Шахты NewMoscow</b><br><br>-->
<?

/*
$SQL="SELECT p.*, u.id AS UID, u.user_name AS Uname FROM post03_reports p LEFT JOIN site_users u ON u.id=p.id_user ORDER BY id DESC LIMIT 1";
$r=mysql_query($SQL);
$d=mysql_fetch_array($r);
if(@$d['post_g']>0) {
	echo "Сейчас на посту никого.";
} else {
    echo "Сейчас на посту: <b>{$d['Uname']}</b>";
    }
*/

//=======ОАЗИС=============
$oa_post = "<img src='i/bullet-red-01a.gif' width='18' hspace='5'><b>Oasis city</b><br> <br>\n";
	
$SQL="SELECT p.*, u.id AS UID, u.user_name AS Uname FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user WHERE p.city=2 ORDER BY id DESC LIMIT 1";	
$r=mysql_query($SQL);
$d=mysql_fetch_array($r);
if(mysql_num_rows($r)==0 || $d['post_g'] >0) {
	$oa_post .= "Сейчас на посту никого.";
} else {
    $oa_post .= "Сейчас на посту: <A HREF=\"javascript:{}\" OnClick=\"ClBrd('private [".stripslashes($d["Uname"])."]');\" TITLE=\"private [".stripslashes($d["Uname"])."]\"><b>".$d["Uname"]."</b></A>";
}

//=======VAULT=============
$vc_post = "<img src='i/bullet-red-01a.gif' width='18' hspace='5'><b>Vault city</b><br> <br>\n";
	
$SQL="SELECT p.*, u.id AS UID, u.user_name AS Uname FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user WHERE p.city=4 ORDER BY id DESC LIMIT 1";	
$r=mysql_query($SQL);
$d=mysql_fetch_array($r);
if(mysql_num_rows($r)==0 || $d['post_g'] >0) {
	$vc_post .= "Сейчас на посту никого.";
} else {
    $vc_post .= "Сейчас на посту: <A HREF=\"javascript:{}\" OnClick=\"ClBrd('private [".stripslashes($d["Uname"])."]');\" TITLE=\"private [".stripslashes($d["Uname"])."]\"><b>".$d["Uname"]."</b></A>";
}


//=======ФОРУМ=============
$forum_post = "<img src='i/bullet-red-01a.gif' width='18' hspace='5'><b>Форум</b><br> <br>\n";

$SQL="SELECT p.*, u.id AS UID, u.user_name AS Uname FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user WHERE p.city=3 AND p.post_g=0";	
$r=mysql_query($SQL);
$c=0;
while ($d=mysql_fetch_array($r))
	{
		if ($c) {$nicks .= ", ";}
		$nicks .= "<A HREF=\"javascript:{}\" OnClick=\"ClBrd('private [".stripslashes($d["Uname"])."]');\" TITLE=\"private [".stripslashes($d["Uname"])."]\"><b>".$d["Uname"]."</b></A>";
		$c++;
	}
if($c == 0) {
	$forum_post .= "Сейчас на посту никого.";
} else {
    $forum_post .= "Сейчас на посту: ".$nicks;
}
/////////////////////////////
echo "<TABLE WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"1\" CELLPADDING=\"2\">\n";
echo " <TR>\n";
echo "  <TD VALIGN=\"top\">\n";
echo $nm_post;
echo "  </TD>\n";
echo "  <TD VALIGN=\"top\">\n";
echo $oa_post;
echo "  </TD>\n";
echo "  <TD VALIGN=\"top\">\n";
echo $vc_post;
echo "  </TD>\n";
echo "  <TD VALIGN=\"top\">\n";
echo $forum_post;
echo "  </TD>\n";
echo " </TR>\n";
echo "</TABLE>\n";
////////////////////////////
?>
<hr size=1>
<?

/****************************************************
*	 Lebedev Sergey (fantastish@rambler.ru)			*
****************************************************/
///////////////////////////////////////////////////////////////////////////////////
// Самая нужная функция =)
///////////////////////////////////////////////////////////////////////////////////
// ---------- Вывод полиции онлайн в настоящий момент-------------
	function police_online_view_999 ($connection, $db){
		$query = "SELECT `id`, `name` FROM `sd_depts`";
		$tmp = mysql_query($query, $connection);
		while ($rslt = mysql_fetch_array($tmp))
			{
				$dept[$rslt['id']] = $rslt['name'];
			}
//print_r($dept);
		$text = "<H1>Полицейские в игре</H1>";
		$sSQL = "SELECT `nick`, `status` FROM `cops_online` WHERE `logout`='0' AND (`status`='0' OR `status`='2') GROUP BY `nick` ORDER BY `nick` ASC";
//		$sSQL = "SELECT nick, status FROM `cops_online` WHERE `logout`='0' ORDER BY `status` ASC, `nick` ASC";
		$result = mysql_query($sSQL, $connection);
		$nrows=mysql_num_rows($result);
		if($nrows>0){
			$text .= "<DIV><SMALL>*Информация обновляется каждые 5 минут</SMALL></DIV>\n";
			$text .= "<TABLE WIDTH=\"95%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\" ALIGN=center>\n";
			
			$bgcolor[1]="#F5F5F5";
			$bgcolor[2]="#E4DDC5";
			$i=1;
			$i2=0;
			
			while($row = mysql_fetch_array($result)){
				$sSQL4 = "SELECT `dept` FROM `sd_cops` WHERE `name` = '".$row["nick"]."' LIMIT 1;";
				$result4 = mysql_query($sSQL4, $connection);
				$row4 = mysql_fetch_array($result4);
				
				$dept_users[$row4["dept"]][$row["nick"]] = $row["status"];
			}
			
			$cur_dept ="";
			ksort ($dept_users);
			reset ($dept_users);
			foreach ($dept_users AS $key=>$val){
				
				if($cur_dept!=$key){
					if($i>2) $i=1;
					
					$cur_dept = $key;
					
					if($dept[$key] == "Лицензионный отдел"){
						$dept[$key] = "<U>".$dept[$key]." (проверки на чистоту)</U>";
					}
					
					$text .= "<TR>\n";
					$text .= " <TD><img src='i/bullet-red-01a.gif' width='18' hspace='2'><B><i>".$dept[$key]."</i></B></TD>\n";
					$text .= "</TR>\n";
					$i=1;
				}
				
				foreach($val AS $k2=>$v2){
					$i2++;
					if($i>2) $i=1;
					$text .= "<TR BGCOLOR=\"".$bgcolor[$i]."\">\n";
					$sSQL3 = "SELECT `name`, `level`, `pro`, `sex` FROM `".$db["tz_users"]."` WHERE `name`='".$k2."'";
					$result3 = mysql_query($sSQL3, $connection);
					if(mysql_num_rows($result3)>0){
						$row3 = mysql_fetch_array($result3);
						$clan = "{_CLAN_}police{_/CLAN_}";
						$text .= " <TD VALIGN=top STYLE=\"PADDING-left:20px;\">".(($v2=="1")?"[Chat: Off] ":"")."".$clan." <A HREF=\"javascript:{}\" OnClick=\"ClBrd('private [".stripslashes($row3["name"])."]');\" TITLE=\"private [".stripslashes($row3["name"])."]\">".stripslashes($row3["name"])."</A> [".$row3["level"]."] {_PROF_}".$row3["pro"]."".(($row3["sex"]=="0")?"w":"")."{_/PROF_}</TD>\n";
					}else{
						$text .= " <TD VALIGN=top STYLE=\"PADDING-left:20px;\"><A HREF=\"javascript:{}\" OnClick=\"ClBrd('private [".stripslashes($k2)."]');\" TITLE=\"private [".stripslashes($k2)."]\">".stripslashes($k2)."</A></TD>\n";
					}
					$text .= "</TR>\n";
					$i++;
				}
			}
			$text .= "</TABLE>\n";
			$text .= "<CENTER>Всего: ".$i2." человек</CENTER>\n";
		}else{
			$text .= "<CENTER>никого нет</CENTER>";
		}
		return $text;
	}

// название текущего раздела
	$menu = "rating_arh";
	$path_to_php = "/_modules";
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
//	if(!eregi("www.",$HTTP_HOST)) Header("Location: http://www.".$SERVER_NAME.$REQUEST_URI."");
//	if(!eregi("www.",$SERVER_NAME)) $SERVER_NAME="www.".$SERVER_NAME;

// Получение глобальных переменных
	while (list ($key, $val) = each ($HTTP_GET_VARS)) $$key = $val;
	while (list ($key, $val) = each ($HTTP_POST_VARS)) $$key = $val;
///////////////////////////////////////////////////////////////////////////////////
// Открываем соединение с базой данных (если не открывается, тогда ничего не выводим, а просто выходим):
	//$connection = mysql_connect ($hostName, $userName, $password);
	$connection = $db;
// Выбираем базу данных (если выбрать не получается, то просто выходим):
//	$database = mysql_select_db ($databaseName, $connection);
///////////////////////////////////////////////////////////////////////////////////
// подключаем все что требуеца
	include_once ($DOCUMENT_ROOT.$path_to_php."/".$menu."/config.php");
	include_once ($DOCUMENT_ROOT.$path_to_php."/".$menu."/other.php");
	include_once ($DOCUMENT_ROOT.$path_to_php."/".$menu."/tz_plugins.php");
	
//error_reporting(E_ALL);
	$text = police_online_view_999 ($connection, $db);
	$text = tz_tag_remake($text);
	$text = change_pre_value($SERVER_NAME, $PHP_SELF, $path_to_images, $text);
	echo $text;
?>
<hr size=1>
<b>Уважаемые игроки!</b><br><br>
Убедительно просим Вас по вопросам модерации в первую очередь обращаться к дежурным полицейским, если таковые отсутствуют - то к сотрудникам отдела модерации. По профильным вопросам советуем сразу обращаться к сотрудникам соответствующих отделов.