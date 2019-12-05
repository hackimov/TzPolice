<?
//=========================
	require_once('/home/sites/police/dbconn/dbconn2.php');
//=========================

if (isset($_REQUEST['sec'])) {
	$sec = $_REQUEST['sec'];
} else {
	$sec = 'undef';
}
$headder['autodel'] = 'Удаленные автоматически за последние 15 суток';
$headder['manualdel'] = 'Удаленные вручную за последние 60 суток';
$headder['visa'] = 'Взломанные';
$headder['udo'] = 'Заявки на УДО';
$headder['wantout'] = 'Кандидаты на освобождение';
$headder['all'] = 'Список каторжан';
$headder['undef'] = 'Каторжане с неопределенным сроком';
$headder['kpz'] = 'Отсидевшие в КПЗ более 28 дней';
$headder['udo_checked'] = 'УДО - ожидание оплаты';
//error_reporting(E_ALL);
if (AuthStatus==1 && (substr_count(AuthUserRestrAccess, '-prison-') > 0 || AuthUserGroup == 100)) {
	$see_stats = 2;
} elseif (AuthStatus==1 && AuthUserClan == 'police') {
	$see_stats = 1;
} else {
	$see_stats = 0;
}

?>
<h1>Статистика персонажей, отбывающих наказание на каторге</h1>
<?
if ($see_stats > 0) {
?>
<center>
<iframe src="direct_call/prison_actions.php" name="prison_editor" width="450" marginwidth="0" height="350" marginheight="0" align="middle" scrolling="auto"><br>
</iframe>
<br>
<hr>
<?
/*
  if ($see_stats == 2)
  {
?>
<b>Счета отделов:</b><br>
<?
$query = "SELECT `incoming` FROM `prison_depts` WHERE `dept` = '1' LIMIT 1;";
$rs = mysql_query($query) or die (mysql_error());
list($dept1) = mysql_fetch_row($rs);
$query = "SELECT `incoming` FROM `prison_depts` WHERE `dept` = '2' LIMIT 1;";
$rs = mysql_query($query) or die (mysql_error());
list($dept2) = mysql_fetch_row($rs);
$query = "SELECT `incoming` FROM `prison_depts` WHERE `dept` = '3' LIMIT 1;";
$rs = mysql_query($query) or die (mysql_error());
list($dept3) = mysql_fetch_row($rs);
echo ($dept[1].": ".$dept1." монет. <a href='direct_call/prison_actions.php?take_money=1' onClick=\"if(!confirm('Вы уверены?')) return false;\" target='prison_editor'>Снять деньги</a><br>");
echo ($dept[2].": ".$dept2." монет. <a href='direct_call/prison_actions.php?take_money=2' onClick=\"if(!confirm('Вы уверены?')) return false;\" target='prison_editor'>Снять деньги</a><br>");
echo ($dept[3].": ".$dept3." монет. <a href='direct_call/prison_actions.php?take_money=3' onClick=\"if(!confirm('Вы уверены?')) return false;\" target='prison_editor'>Снять деньги</a><br>");
   }
*/
?>

</center>
<script language="Javascript" type="text/javascript">
<!--
function ClBrd2(text){
    while(text.indexOf('<BR>')>=0) text = text.replace('<BR>','\015\012');
    if (window.clipboardData){window.clipboardData.setData("Text", text);alert ("Строка для обращения в приват в чате ТЗ добавлена в буфер обмена.");}
	else
		{
			var DummyVariable = prompt('Буфер обмена недоступен, копируйте отсюда =(',text);
		}
}

function add_pers() {
	if (nick = window.prompt("Введите ник персонажа", ""))
    	{
	if (!nick) alert("Необходимо указать ник персонажа!")
    else
    	{
            prison_editor.location.href = 'direct_call/prison_actions.php?sec=add&nick=' + nick;
	    }
        }
	}
//-->
</script>

<?php
	if ($chief=='1' || AuthUserGroup == 100)
		{
			echo '<a href="http://www.tzpolice.ru/direct_call/prison_actions_log.php" target="_blank">Лог действий</a> <br> '."\n";
		}
	echo '<a href="#" onClick="add_pers(); return false;">Добавить персонажа</a> | '."\n";
	echo '<a href="?act=prison_stats&sec=all">Полный список</a> <br> '."\n";
//----------------
	$SQL2 = 'SELECT COUNT(*) as `cnt` FROM `prison_chars` WHERE `term` < 0';
	$r = mysql_query($SQL2);
	$rs = mysql_fetch_assoc($r);
	echo '<a href="?act=prison_stats&sec=undef">C неуказанным сроком</a> ['.$rs['cnt'].'] | '."\n";
//----------------
	$SQL2 = 'SELECT COUNT(*) as `cnt` FROM `prison_chars` WHERE `term` > 0 AND (`term` = `collected` OR `term` < `collected`)';
	$r = mysql_query($SQL2);
	$rs = mysql_fetch_assoc($r);
	echo '<a href="?act=prison_stats&sec=wantout">На освобождение</a> ['.$rs['cnt'].'] | '."\n";
//----------------
	/*
	$SQL2 = 'SELECT COUNT(*) as `cnt` FROM `prison_chars` WHERE `want_udo` = 1';
	$r = mysql_query($SQL2);
	$rs = mysql_fetch_assoc($r);
	echo '<a href="?act=prison_stats&sec=udo">Заявки на УДО</a> ['.$rs['cnt'].'] | '."\n";

//----------------
	$SQL2 = 'SELECT COUNT(*) as `cnt` FROM `prison_chars` WHERE `want_udo` = 2';
	$r = mysql_query($SQL2);
	$rs = mysql_fetch_assoc($r);
	echo '<a href="?act=prison_stats&sec=udo_checked">УДО - ожидание оплаты</a> ['.$rs['cnt'].'] <br> '."\n";
	*/
//----------------

	$SQL2 = 'SELECT COUNT(*) as `cnt` FROM `prison_chars` WHERE `allow_udo` = 1';
	$r = mysql_query($SQL2);
	$rs = mysql_fetch_assoc($r);
	echo '<a href="?act=prison_stats&sec=visa">Взломанные</a> ['.$rs['cnt'].'] | '."\n";
//----------------
	$tttttt = time()-1296000; //60 days
	$SQL2 = "DELETE FROM `prison_manualdelete` WHERE `date`<'".$tttttt."'";
	$r = mysql_query($SQL2);
	$SQL2 = 'SELECT COUNT(*) as `cnt` FROM `prison_manualdelete`';
	$r = mysql_query($SQL2);
	$rs = mysql_fetch_assoc($r);
	echo '<a href="?act=prison_stats&sec=manualdel">Удаленные вручную</a> ['.$rs['cnt'].'] '."\n";
//----------------
	$kpz_28 = time()-2419200; //60 days
//	$SQL2 = "DELETE FROM `prison_autodelete` WHERE `date`<'".$tttttt."'";
//	$r = mysql_query($SQL2);
	$kpz_28_date = date('Y-m-d', $kpz_28);
	$SQL2 = "SELECT COUNT(*) as `cnt` FROM `prison_chars` WHERE `reason`=91 AND `add_date` < '".$kpz_28_date."'";
//	echo ($SQL2);
	$r = mysql_query($SQL2);
	$rs = mysql_fetch_assoc($r);
	echo '<a href="?act=prison_stats&sec=kpz">Просроченная КПЗ</a> ['.$rs['cnt'].']'."\n";
//----------------

	echo '<H1>'.$headder[$sec]."</H1>\n";
}

if ($see_stats == 2) {
	$bgstr[0]='background="i/bgr-grid-sand.gif"';
	$bgstr[1]='background="i/bgr-grid-sand1.gif"';
	$bg=0;
if ($sec == "autodel")
{
?>
[<a href="?act=prison_stats&sec=autodel">по дате</a>] [<a href="?act=prison_stats&sec=autodel&order=nick">по нику</a>]
<?
echo ("<center><table width='450' border=0 cellpadding=5 cellspacing=5><tr><td align='center' width='250' bgcolor=#F4ECD4><b>Ник</b></td><td align='center' width='100' bgcolor=#F4ECD4><b>Дата удаления</b></td><td align='center' width='100' bgcolor=#F4ECD4><b>Ресов на момент удаления</b></td></tr>");
$q = 'SELECT * FROM `prison_autodelete` ORDER BY `date` DESC';
if ($_REQUEST['order'] == "nick")
	{
		$q = 'SELECT * FROM `prison_autodelete` ORDER BY `nick`';
	}
$qq = mysql_query($q);
while ($r=mysql_fetch_array($qq))
	{
		$bg++;
		if ($bg>1){$bg=0;}
		echo ("<tr><td ".$bgstr[$bg]."><b>".$r['nick']."</b></td><td ".$bgstr[$bg]." align='center'>".date("d.m.Y H:i",$r['date'])."</td><td ".$bgstr[$bg]." align='center'>".$r['res']."</td></tr>");
	}
echo ("</table></center>");
}
elseif ($sec == "kpz")
	{
		echo ("<center><table width='450' border=0 cellpadding=5 cellspacing=5><tr><td align='center' width='250' bgcolor=#F4ECD4><a href='?act=prison_stats&sec=kpz&order=nick'>Ник</a></td><td align='center' width='100' bgcolor=#F4ECD4><a href='?act=prison_stats&sec=kpz'>Дата помещения на каторгу</a></td></tr>");
		$q = "SELECT * FROM `prison_chars` WHERE `reason`=91 AND `add_date` < '".$kpz_28_date."' ORDER BY `add_date` DESC";
		if ($_REQUEST['order'] == "nick")
			{
				$q = "SELECT * FROM `prison_chars` WHERE `reason`=91 AND `add_date` < '".$kpz_28_date."' ORDER BY `nick`";
			}
		$qq = mysql_query($q);
		while ($r=mysql_fetch_array($qq))
			{
				$bg++;
				if ($bg>1){$bg=0;}
				echo ("<tr><td ".$bgstr[$bg]."><a href='direct_call/prison_actions.php?nick=".$r['nick']."' target='prison_editor'>".$r['nick']."</a> <a href=\"#; return false;\" onClick=\"ClBrd2('".$r['nick']."')\">[C]</a></td><td ".$bgstr[$bg]." align='center'>".$r['add_date']."</td></tr>");
			}
echo ("</table></center>");
	}
elseif ($sec == "manualdel")
{
?>
[<a href="?act=prison_stats&sec=manualdel">по дате</a>] [<a href="?act=prison_stats&sec=manualdel&order=nick">по нику</a>]
<?
echo ("<center><table width='450' border=0 cellpadding=5 cellspacing=5><tr><td align='center' width='250' bgcolor=#F4ECD4><b>Ник</b></td><td align='center' width='100' bgcolor=#F4ECD4><b>Дата удаления</b></td><td align='center' width='100' bgcolor=#F4ECD4><b>Ресов на момент удаления</b></td></tr>");
$q = 'SELECT * FROM `prison_manualdelete` ORDER BY `date` DESC';
if ($_REQUEST['order'] == "nick")
	{
		$q = 'SELECT * FROM `prison_manualdelete` ORDER BY `nick`';
	}
$qq = mysql_query($q);
while ($r=mysql_fetch_array($qq))
	{
		$bg++;
		if ($bg>1){$bg=0;}
		echo ("<tr><td ".$bgstr[$bg]."><b>".$r['nick']." <a href=\"#; return false;\" onClick=\"ClBrd2('".$r['nick']."')\">[C]</a></b></td><td ".$bgstr[$bg]." align='center'>".date("d.m.Y H:i",$r['date'])."</td><td ".$bgstr[$bg]." align='center'>".$r['res']."</td></tr>");
	}
echo ("</table></center>");
}
else
{

	if ($sec=='all') {
		$late = time() - 1209600;
		$SQL2 = 'SELECT COUNT(*) as `cnt` FROM `prison_chars` WHERE `last_pay` > '.$late.' AND (`term` > 0 OR `term` = \'-1\')';
	}elseif ($sec=='wantout'){
		$SQL2 = 'SELECT COUNT(*) as `cnt` FROM `prison_chars` WHERE `term` > 0 AND (`term` = `collected` OR `term` < `collected`)';
	} elseif ($sec=='undef') {
		$SQL2 = 'SELECT COUNT(*) as `cnt` FROM `prison_chars` WHERE `term` < 0';
	} elseif ($sec=='udo') {
		$SQL2 = 'SELECT COUNT(*) as `cnt` FROM `prison_chars` WHERE `want_udo` = \'1\'';
	} elseif ($sec=='udo_checked') {
		$SQL2 = 'SELECT COUNT(*) as `cnt` FROM `prison_chars` WHERE `want_udo` = \'2\'';
	} elseif ($sec=='visa') {
		$SQL2 = 'SELECT COUNT(*) as `cnt` FROM `prison_chars` WHERE `allow_udo` = \'1\'';
	}
	//echo ($SQL2);
	$r=mysql_query($SQL2) or die(mysql_error());
	$rs = mysql_fetch_assoc($r);
	//echo($rs['cnt']);
	//$pages=ceil(mysql_num_rows($r)/$NewsPP);
	$pages=ceil($rs['cnt']/50);
	if($_REQUEST['p']>0) $p=$_REQUEST['p'];
	else $p=1;
	$LimitParam = $p*50-50;
	$query = 'SELECT `log_date`, `log_time` FROM `bot_prison_logs` ORDER BY `id` DESC LIMIT 1;';
	$rs = mysql_query($query) or die (mysql_error());
	list($last_date, $last_time) = mysql_fetch_row($rs);
	$ld = $last_date.' '.$last_time;
	$last_date = strtotime($ld);
?>
	<hr>Показаны данные по состоянию на <b><?=date("d.m.Y, H:i", $last_date)?></b> <br><br>
<?
	echo '<br><p align=right>Страницы: <b>';
	ShowPages($p, $pages, 15, 'act=prison_stats&sec='.$sec);
	echo '</b></p>';
?>
	<table width="95%"  border="0" cellspacing="3" cellpadding="2" align="center">
	  <tr>
	    <td align="center" bgcolor=#F4ECD4><b>ник</b></td>
<!--	    <td align="center" bgcolor=#F4ECD4><b>отдел</b></td> //-->
	    <td align="center" bgcolor=#F4ECD4><b>правонарушение</b></td>
	    <td align="center" bgcolor=#F4ECD4><b>срок</b></td>
<?
		if ($sec == 'udo') {
			#echo '<td align="center" bgcolor=#F4ECD4><b>% откопанного<BR>сумма для выхода<BR><SMALL>[пересчитан?]</SMALL></b></td>';
		}
?>
	  </tr>
<?

	if ($sec=='wantout'){
		$query = 'SELECT * FROM `prison_chars` WHERE `term` > 0 AND (`term` = `collected` OR `term` < `collected`) ORDER BY last_pay ASC, `nick`';
	} elseif ($sec=='undef') {
		$query = 'SELECT * FROM `prison_chars` WHERE `term` < 0 ORDER BY `reason`, `nick`';
	} elseif ($sec=='udo') {
		$query = 'SELECT * FROM `prison_chars` WHERE `want_udo` = 1 ORDER BY `last_pay` ASC, `nick`';
	} elseif ($sec=='udo_checked') {
		$query = 'SELECT * FROM `prison_chars` WHERE `want_udo` = 2 ORDER BY `last_pay` ASC, `nick`';
	} elseif ($sec=='visa') {		$query = 'SELECT * FROM `prison_chars` WHERE `allow_udo` = 1 ORDER BY `last_pay` ASC, `nick`';
	} else{
//($sec=='all')
		$query = 'SELECT * FROM `prison_chars` WHERE `last_pay` > '.$late.' AND (`term` > 0 OR `term` = \'-1\') ORDER BY `nick`';
	}
	$query .= ' LIMIT '.$LimitParam.', 50;';

	$rez = mysql_query($query) or die(mysql_error());
	$bg = 0;

	while (list($c_id, $c_nick, $c_term, $c_coll, $c_l_pay, $c_reas, $c_rem) = mysql_fetch_row($rez)) {
		echo '<tr>';
		$bg++;
		if ($bg>1) { $bg=0; }
		if (($c_coll == $c_term || $c_coll > $c_term) && $c_term > 0) {
			$c_nick2 = '<font color="green">'.$c_nick.'</font>';
		} elseif ($c_term < 0) {
			$c_nick2 = '<font color="red">'.$c_nick.'</font>';
		} else {
			$c_nick2 = $c_nick;
		}

		if ($see_stats == 2) {
			echo '<td '.$bgstr[$bg].'><a href="direct_call/prison_actions.php?nick='.$c_nick.'" target="prison_editor">'.$c_nick2.'</a> <a href="#; return false;" onClick="ClBrd2(\''.$c_nick.'\')">[C]</a> <a href="direct_call/prison_actions.php?del='.$c_nick.'" onClick="if(!confirm(\'Вы уверены?\')) return false;" target="prison_editor"><FONT COLOR="black">[X]</FONT></a>';
			if($sec=='udo'){
				echo ' <a href="direct_call/prison_actions.php?del_udo='.$c_nick.'" onClick="if(!confirm(\'Вы уверены?\')) return false;" target="prison_editor"><FONT COLOR="red">[отказ]</FONT></a>';
				echo ' <a href="direct_call/prison_actions.php?confirm_udo='.$c_nick.'" onClick="if(!confirm(\'Вы уверены?\')) return false;" target="prison_editor"><FONT COLOR="green">[ОК, ждём оплаты]</FONT></a>';
			}
			if($sec=='wantout'){
				echo ' <a href="direct_call/prison_actions.php?nodel='.$c_nick.'" onClick="if(!confirm(\'Вы уверены?\')) return false;" target="prison_editor"><FONT COLOR="red">[отказ]</FONT></a>';
			}
			echo '</td>';
		} else {
			echo '<td '.$bgstr[$bg].'>'.$c_nick2.'</td>';
		}
//		echo '<td align="center" '.$bgstr[$bg].'>'.$dept[$c_dept].'</td>';
		echo '<td align="center" '.$bgstr[$bg].'>'.$crime[$c_reas].'</td>';
		echo '<td align="center" '.$bgstr[$bg].'>'.$c_coll.' / '.$c_term.'</td>';
		if ($sec == 'udo') {
			$coll_percent = ($c_coll*100)/$c_term;
			$coll_percent = round($coll_percent);
			if($coll_percent<30) $coll_percent = '<FONT COLOR=red>'.$coll_percent.'%</FONT>';
			else $coll_percent = $coll_percent.'%';
			$summa = ceil(($c_term-$c_coll)*1.5);
			$message = "*** Отработано ".$c_coll." рес. *** УДО ".$summa." м.м.";
			echo '<td align="center" '.$bgstr[$bg].'><A href="javascript:{}" onClick="ClBrdUdo(\''.$message.'\');">'.$coll_percent.'</A><BR>'.round($summa).' м.м.</td>';
		}
		echo '</tr>';
	}
	mysql_close($link);

	echo '</table>';
	echo '<br><p align=right>Страницы: <b>';
	ShowPages($p, $pages, 15, 'act=prison_stats&sec='.$sec);
	echo '</b></p>';
}
}
?>