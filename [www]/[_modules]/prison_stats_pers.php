<H1>������������ ���������� �����������</H1>
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
����� ����������: <b><?=$all?></b><br>
����� �� ������: <b><?=$active?></b><br>
������� � ������: <b>���������� ����������</b>
<br><hr>
��� ���������� ������ ������� ���������� �����<br>
�) ������ � <img src='_imgs/clans/admins.gif' border='0'><b>TimeZero</b> ������ �� ���� 3.21<br>
�) ������� � ���������� <b>Macromedia Flash</b>
<br>