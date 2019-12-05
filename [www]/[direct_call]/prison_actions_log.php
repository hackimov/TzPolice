<html>
<head>
<title>Действия сотрудников ОИН</title>
<LINK href="/_modules/tzpol_css2.css" rel="stylesheet" type="text/css">
</head>
<body>
 <script language="JavaScript" type="text/JavaScript">
 <!--
 function hide(menu){
  var c=document.getElementById(menu);
  if (c.style.display != 'none') {
   c.style.display = 'none';
  }else{
   c.style.display = '';
  }
  return false;
 }
 //-->
 </script>
<?

//	set_time_limit(200);


$act_txt[1] = "Добавил на каторгу ";
$act_txt[2] = "Отредактировал данные ";
$act_txt[3] = "Отказал в УДО ";
$act_txt[4] = "Одобрил заявку на УДО ";
$act_txt[5] = "Выпустил с каторги ";


//=========================

//=========================
require('../_modules/functions.php');
require('../_modules/auth.php');

include('/home/sites/police/dbconn/dbconn2.php');

if (AuthStatus==1 && (substr_count(AuthUserRestrAccess, '-prison-') > 0 || AuthUserClan == 'police' || AuthUserGroup=='100')) {
	$auth = 1;
} else {	echo "Wood there.";
	exit;
}

		if(!isset($_REQUEST['time'])){
			$two_weeks = time()-3600*24*14;
			$time1 = mktime(0, 0, 0, date("m", $two_weeks), date("d", $two_weeks), date("Y", $two_weeks));
			$time2 = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
		}elseif(is_array($_REQUEST['time'])){
			$time1 = mktime(0, 0, 0, $_REQUEST['time'][1], $_REQUEST['time'][0], $_REQUEST['time'][2]);
			$time2 = mktime(23, 59, 59, $_REQUEST['time'][4], $_REQUEST['time'][3], $_REQUEST['time'][5]);
		}

//echo ($time1." - ".$time2);
//		if ($time>(time()-86400)) $time = time()-86400;

	//	корректировка
		$time = date("d:m:Y:H:i", $time1);
		$c_time1 = explode(':',$time);
		$time = date("d:m:Y:H:i", $time2);
		$c_time2 = explode(':',$time);
//		$time = mktime(0, 0, 0, $time[1], $time[0], $time[2]);

//echo ("<pre>");
//print_r($c_time2);
//echo ("</pre>");
		$x_value[0] = 0;

		echo ("<center><h1>Действия ОИН</h1></center>");

?>
<BR><DIV STYLE="WIDTH:100%;">
<FORM METHOD="GET" action="/direct_call/prison_actions_log.php">
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0">
<TR VALIGN="top">
<TD ALIGN="left"><B>Начало периода:&nbsp;</B>
<SELECT NAME="time[0]">
<?
		for($i=1;$i<=31;$i++){
			echo ("<OPTION VALUE='".$i."'".(($i==$c_time1[0])?' selected':'')." >".$i."\n");
		}
?>
</SELECT>-<SELECT NAME="time[1]">
<?
		for($i=1;$i<=12;$i++){
			echo ("<OPTION VALUE='".$i."'".(($i==$c_time1[1])?' selected':'')." >".$i."\n");
		}
?>
</SELECT>-<SELECT NAME="time[2]">
<?
		for($i=2010;$i<=date("Y");$i++){
			echo ("<OPTION VALUE='".$i."'".(($i==$c_time1[2])?' selected':'')." >".$i."\n");
		}
?>
</SELECT>
<B>Конец периода:&nbsp;</B>
<SELECT NAME="time[3]">
<?
		for($i=1;$i<=31;$i++){
			echo ("<OPTION VALUE='".$i."'".(($i==$c_time2[0])?' selected':'')." >".$i."\n");
		}
?>
</SELECT>-<SELECT NAME="time[4]">
<?
		for($i=1;$i<=12;$i++){
			echo ("<OPTION VALUE='".$i."'".(($i==$c_time2[1])?' selected':'')." >".$i."\n");
		}
?>
</SELECT>-<SELECT NAME="time[5]">
<?
		for($i=2010;$i<=date("Y");$i++){
			echo ("<OPTION VALUE='".$i."'".(($i==$c_time2[2])?' selected':'')." >".$i."\n");
		}
?>
</SELECT>
<BR>   <input type="submit" class="submit" value="Вывести >>">
</TD>
</TR>
</FORM>
</TABLE><BR>
<?
//	$today = date("Y-m-d",$time);
	$query = "SELECT DISTINCT `cop` FROM `prison_actions_log` WHERE `date` > '".$time1."' AND `date` < '".$time2."'";
//	echo ($query);
	$result = mysql_query($query) or die(mysql_error());
	while ($res = mysql_fetch_array($result))
		{
//			echo ($res['cop']);
			$query = "SELECT * FROM `prison_actions_log` WHERE `date` > '".$time1."' AND `date` < '".$time2."' AND `cop` = '".$res['cop']."'";
//			echo ($query);
			$rs = mysql_query($query);
			$actions[0]=0;
			$actions[1]=0;
			$actions[2]=0;
			$actions[3]=0;
			$actions[4]=0;
			$actions[5]=0;
			$fullog="";
			while ($c = mysql_fetch_array($rs))
				{
//					print_r($c);
					$actions[$c['operation']]++;
					$fullog .= "<br>".date("d.m.Y H:i", $c['date'])." - ".$act_txt[$c['operation']]."<b>[".$c['prisoner']."]</b>";
//					echo ($fullog);
				}
			echo ("<DIV onclick=\"hide('".$res['cop']."');\" STYLE=\"PADDING-TOP: 2px; cursor: pointer\"><b>".$res['cop']."</b> - добавлено ".$actions[1].", изменено ".$actions[2].", отклонено УДО ".$actions[3].", одобрено УДО ".$actions[4].", выпущено ".$actions[5].".");
			echo (" &raquo;&raquo;&raquo;</DIV><DIV ID='".$res['cop']."' style='display:none;'>".$fullog."</DIV><hr size=1>\r\n");
		}

?>
</body>
</html>