<H1>Персональная статистика каторжанина</H1>
<center>
<iframe src="direct_call/prison_stats_pers_show.php" name="view" width="450" marginwidth="0" height="280" marginheight="0" align="middle" scrolling="auto"></iframe>
</center>
<?
//=========================
	require_once("/home/sites/police/dbconn/dbconn2.php");
//=========================

	$late = time() - 604800;
	$query = "SELECT `id` FROM `prison_chars` WHERE `last_pay` > '".$late."' AND (`term` > '0' OR `term` = '-1')";
	$rez = mysql_query($query, $link);
	$active = mysql_num_rows($rez);
	$query = "SELECT `id` FROM `prison_chars` WHERE `term` > '0' OR `term` = '-1'";
	$rez = mysql_query($query, $link);
	$all = mysql_num_rows($rez);
	mysql_close($link);
?>
<br><br>
Всего осужденных: <b><?=$all?></b><br>
Вышли на работу: <b><?=$active?></b><br>
Погибло в неволе: <b>информация уточняется</b>
<br><hr>
Для корректной работы ресурса необходимо иметь<br>
а) Доступ к <img src='_imgs/clans/admins.gif' border='0'><b>TimeZero</b> версии не ниже 3.21<br>
б) Браузер с поддержкой <b>Macromedia Flash</b>
<br>