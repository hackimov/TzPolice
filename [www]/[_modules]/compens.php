<H1>Компенсации пострадавшим от взлома и мошенничества</H1>
<div id='navigation_error'></div>
<!--
<hr><center><b>ВНИМАНИЕ!</b><br>Обращаем Ваше внимание на то, что работа с использованием встроенного браузера TimeZero как правило невозможна!</center><hr>
-->
<script>
if (navigator.userAgent.indexOf ("Opera") != -1)
document.write("<br><br><b>Внимание!</b><br>По всей вероятности Вы используете браузер <b>Opera</b>, авторизация невозможна! Воспользуйтесть браузерами <b>Internet Explorer</b> или <b>Mozilla Firefox</b><br><br><br>");
</script>
<?
error_reporting(0);
$nick = strip_tags(urldecode($_REQUEST['user']));
$nick2 = strip_tags(urlencode($nick));
$nick = str_replace("%20", " ", $nick);
$sesid = strip_tags($_REQUEST['sessid']);
$city = strip_tags($_REQUEST['city']);

// защита xss
$filter = array("=", "(", ")");
$nick = str_replace ($filter, "|", $nick);
$sesid = str_replace ($filter, "|", $sesid);
$city = str_replace ($filter, "|", $city);

$me = strip_tags($_REQUEST['me']);

if (isset($_REQUEST['unblock']))
{
	$unblock = filter_input(INPUT_POST, 'unblock', FILTER_SANITIZE_NUMBER_INT);
	$query = "SELECT * FROM `compens` WHERE `id`='".$unblock."' LIMIT 1;";
	$res = mysql_query($query);
	if (mysql_num_rows($res) == 0)
	{
		echo ("<font color='red'><b>Указанная компенсация не обнаружена в базе!</b></font><br><br><br>");
	}
	else
	{
		$compens = mysql_fetch_array($res);
		$userinfo = GetUserInfo($compens['debtor']);
		if ($userinfo['level'] > 0)
		{
			if (strlen($userinfo['clan']) > 2)
			{
				$cfull = "[pers clan={$userinfo['clan']} nick={$userinfo['login']} level={$userinfo['level']} pro={$userinfo['pro']}]";
			}
			else
			{
				$cfull = "[pers clan=0 nick={$userinfo['login']} level={$userinfo['level']} pro={$userinfo['pro']}]";
			}
			$query = "UPDATE `compens` SET `debtorfull` = '".$cfull."' WHERE `debtor` = '".$userinfo['login']."'";
			mysql_query($query);
		}
		$str = explode(".",$userinfo['regday']);
		$regday = mktime(0,0,0,$str[1],$str[0],$str[2]);
		if ($userinfo['pro'] !== '13' && $userinfo['level'] > 0 && $regday < ($compens['date1']-432000)) //дополнительное условие - должник зарегистрирован не менее чем за 5 дней до момента внесения компенсации в базу
		{
			$query = "UPDATE `compens` SET `pay` = '1' WHERE `id` = '".$compens['id']."' LIMIT 1;";
			mysql_query($query);
			echo ("<font color='green'><b>Ваша компенсация перемещена в очередь на выплату!</b></font><br><br><br>");
		}
		else
		{
			if ($regday > ($compens['date1']-432000))
			{
				echo ("Персонаж ".ParseNews3($cfull)." не является персонажем, который был признан виновным по Вашему делу. Компенсация аннулируется в связи с исчезновением должника из базы ТЗ. Вы можете обратиться к сотруднику отдела исполнения наказаний за комментариями.");
				$query = "UPDATE `compens` SET `status` = '3' WHERE `id` = '".$compens['id']."' LIMIT 1;";
				mysql_query($query);
			}
			else
			{
				echo ("<font color='red'><b>Извините, но указанный персонаж не вышел с каторги!</b></font><br>Возможно, данное сообщение связано с отсутствием связи с сервером ТЗ. Попробуйте повторить попытку позже.<br><br><br>");
			}
		}
	}
}

if (strlen($sesid) > 3)
{
	
	$tmp_body = file_get_contents('https://www.timezero.ru/cgi-bin/authorization.pl?login='.$nick2.'&ses='.$sesid.'&city='.$city);
	
	if (strpos($tmp_body, "OK"))
	{
		$cur_user = $nick;
		if (in_array($nick, $compens_cops2))
		{
			$iscop = true;
		}
		else
		{
			$iscop = false;
		}
	}
	else
	{
		$cur_user = "nofuckinguserfoundsonocredentialsgiven"; // I don't think there will ever exist a character with this kind of nick :crazy:
	}
}
else
{
	$cur_user = "nofuckinguserfoundsonocredentialsgiven"; // I don't think there will ever exist a character with this kind of nick :crazy:
}

$q_cond="";
if ($me == 1 && $cur_user !== "nofuckinguserfoundsonocredentialsgiven")
{
	$me = 1;
	$q_cond = " AND `victim` = '".$cur_user."'";
}
else
{
	$me = 0;
}
if (strlen($_REQUEST['sn']) > 2)
{
	$q_cond = " AND `victim` = '".addslashes($_REQUEST['sn'])."'";
}
if (strlen($_REQUEST['sn2']) > 2)
{
	$q_cond = " AND `debtor` = '".addslashes($_REQUEST['sn2'])."'";
}
$cur_user = "";
if (isset($_REQUEST['sec'])) {$sec = strip_tags($_REQUEST['sec']);}
else {$sec='queue';}
if($_REQUEST['p']>0) $p=strip_tags($_REQUEST['p']);
else $p=1;
$caseonpage = "30";
if ($sec=="payed_out"){
	$query = "SELECT COUNT(`id`) AS cnt FROM `compens` WHERE `status` = '2'".$q_cond;
}
else {
	$query = "SELECT COUNT(`id`) AS cnt FROM `compens` WHERE `status` < '2'".$q_cond;
}
$r = mysql_query($query);
$q = mysql_fetch_array($r);
$quan = $q['cnt'];
$pages = ceil($quan/$caseonpage);
$LimitParam=$p*$caseonpage-$caseonpage;
$q_limit = " LIMIT ".$LimitParam.",".$caseonpage;




if (!isset($_REQUEST['sessid']) && !isset($_REQUEST['user']) && !isset($_REQUEST['city']))
{
	?>
	<OBJECT id="tz" codeBase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" height="1" width="1" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><PARAM NAME="movie" VALUE="/_imgs/auth3.swf"><PARAM NAME="wmode" VALUE="transparent">
	<embed src="/_imgs/auth3.swf" wmode="transparent" width="1" height="1" swLiveConnect="true" id="tz" name="tz" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</OBJECT>
	<script language="JavaScript" type="text/javascript">
	<!--
	var timeout = null;
	function tz_DoFSCommand(command, args) {
		var tmp = args.split("\t");
		if (command == "OK")
		{
			var pers_nick = '' + tmp[0];
			var pers_sid = '' + tmp[1];
			var pers_city = '' + tmp[2];
			var url = 'http://<?=$_SERVER['HTTP_HOST']?>/?act=compens&sessid=' + pers_sid + '&user=' + pers_nick + '&city=' + pers_city;
			top.location = url;
			document.getElementById('navigation_error').innerHTML = '<hr size=1><b>ВНИМАНИЕ!!!</b><br>Если вы все еще не видите своих компенсаций - перейдите по этой <a href='+url+'>ссылке</a><hr size=1>';
		}
	}
	if (navigator.appName.indexOf("Microsoft") != -1) {// Hook for Internet Explorer.
		document.write('<script language=\"VBScript\"\>\n');
		document.write('On Error Resume Next\n');
		document.write('Sub tz_FSCommand(ByVal command, ByVal args)\n');
		document.write('	Call tz_DoFSCommand(command, args)\n');
		document.write('End Sub\n');
		document.write('</script\>\n');
	}
	// -->
	</script>
	<?
	if ($cur_user == "nofuckinguserfoundsonocredentialsgiven")
	{
		$plink = "act=compens&sec=".$sec;
	}
	else
	{
		$plink = "act=compens&sec=".$sec."&user=".$nick."&sessid=".$sesid."&city=".$city;
		if ($me == 1) {$plink .= "&me=1";}
		if (strlen($_REQUEST['sn']) > 2) {$plink .= "&sn=".(strip_tags($_REQUEST['sn']))."";}
		$link_add = "&user=".$nick."&sessid=".$sesid."&city=".$city;
		
	}
	$sq = mysql_query("SELECT COUNT(`id`) AS cnt FROM `compens` WHERE `status` < '2'");
	$sr = mysql_fetch_array($sq);
	$queue_length = $sr['cnt']; //Заявок в очереди
	$sq = "SELECT COUNT(`id`) as `number`, SUM(`percent`) as `percent` FROM `compens` WHERE `status` < '2' AND `percent` > '0'";
	$sr = mysql_query($sq);
	$srs = mysql_fetch_array($sr);
	$sum_perc = $srs['percent'] + ($queue_length-$srs['number']);
	$sq = "SELECT SUM(`sum`) as `summa` FROM `compens` WHERE `status` < '2'";
	$sr = mysql_query($sq);
	$srs = mysql_fetch_array($sr);
	$queue_sum = round($srs['summa']*($sum_perc/$queue_length)); //Приблизительная сумма выплат в очереди
	$sq = mysql_query("SELECT COUNT(`id`) AS cnt FROM `compens` WHERE `status` = '2'");
	$sr = mysql_fetch_array($sq);
	$payed_out_length = $sr['cnt']; //Выплачено заявок
	$sq = "SELECT SUM(`sum`) as `summa`, SUM(`percent`) as `percent` FROM `compens` WHERE `status` = '2'";
	$sr = mysql_query($sq);
	$srs = mysql_fetch_array($sr);
	$payed_out_sum = round($srs['summa']*($srs['percent']/$payed_out_length)); //Приблизительная сумма совершенных выплат
	?>
	<script language="JavaScript" type="text/javascript">
	function line1(count,value,sum,nick,flink,linkname,cell,pay)
	{
		if (pay == 1 && cell > 0)
		{
			var cnt = count;
		}
		else
		{
			var cnt = "--";
		}
		var txt = "";
		var val = Math.round(value*100);
		txt = '<tr bgcolor="#E8D395"><td nowrap>'+cnt+'</td><td>'+nick+'</td><td align="center"><a href="http://www.timezero.ru/cgi-bin/forum.pl?'+flink+'" target="_blank">'+linkname+'</a></td><td align="center">'+val+'%</td><td align="center">'+sum+'</td></tr>';
		return txt;
	}
	<? if ($iscop) { ?>
		function line3(id,count,value,sum,nick,flink,linkname,cell,status,debtor,pay)
		{
			if (pay == 1 && cell > 0)
			{
				var cnt = count;
			}
			else
			{
				var cnt = "--";
			}
			var txt = "";
			var val = Math.round(value*100);
			if (status == 1){pref = '<font color="red"><b>!</b></font> ';}
			else{pref = '';}
			if (debtor == "") {var showdeb = "";} else {var showdeb = '<br><i>'+debtor+'</i>';}
			txt = '<tr bgcolor="#E8D395"><td nowrap>'+cnt+'</td><td>'+pref+nick+' [<a href="?act=compens&payed='+id+'<?=$link_add?>" onClick="if (!confirm(\'Вы уверены?\')) {return false;}"><b>оплачено</b></a>] [<a href="?act=compens_add&id='+id+'<?=$link_add?>" target="_blank"><b>изменить</b></a>]</td align="center"><td align="center"><a href="http://www.timezero.ru/cgi-bin/forum.pl?'+flink+'&m=1" target="_blank">'+linkname+'</a></td><td align="center">'+val+'%</td><td align="center">'+sum+' '+showdeb+'</td></tr>';
			return txt;
		}
		<?}?>
	</script>
	<br><a href="?act=compens&sec=payed_out&me=<?=$me?>&user=<?=$nick?>&sessid=<?=$sesid?>&city=<?=$city?>">Выплаченные компенсации</a><br>
	<a href="?act=compens&sec=queue&me=<?=$me?>&user=<?=$nick?>&sessid=<?=$sesid?>&city=<?=$city?>">Очередь компенсаций</a>
	<hr>
	Компенсаций в очереди: <b><?=$queue_length?></b> на сумму около <b><?=$queue_sum?></b> м.м.<br>
	Компенсаций выплачено: <b><?=$payed_out_length?></b> на сумму около <b><?=$payed_out_sum?></b> м.м.<br>
	<hr>
	<br><p align=right>Страницы: <b>
	<?=ShowPages($p,$pages,5,$plink);?>
	</b></p>
	<table width="100%"  border="0" cellspacing="3" cellpadding="3" align="center">
	<tr bgcolor="#948559">
	<td width="1" nowrap align="center"><b>№</b></td>
	<td align="center"><b>Потерпевший</b></td>
	<td align="center"><b>Дата</b></td>
	<td align="center"><b>К выплате</b></td>
	<td align="center"><b>Сумма</b></td>
	<!--        <td align="center"><b>Ячейка</b></td> -->
	</tr>
	<script language="JavaScript" type="text/javascript">
	var show="";
	<?
	if ($sec=="payed_out") $query = "SELECT * FROM `compens` WHERE `status`='2'".$q_cond." ORDER BY `date2`".$q_limit;
	//else  $query = "SELECT * FROM `compens` WHERE `status`<'2'".$q_cond." ORDER BY `pay` DESC, `percent` ASC, ISNULL(`cell`), `date1` ASC".$q_limit;
	else  $query = "SELECT * FROM `compens` WHERE `status`<'2'".$q_cond." ORDER BY (`percent` = '0') ASC, `pay` DESC, `percent` ASC, `date1` ASC".$q_limit;
	$r = mysql_query($query) or die(mysql_error());
	$count = 1 + (($p-1)*$caseonpage);
	while ($row = mysql_fetch_array($r))
	{
		$usrfull = ParseNews3($row['vfull']);
		$usrfull = str_replace("'", "\"", $usrfull);
		if ($row['percent'] > 0)
		{
			$cursum = round($row['percent']*$row['sum']);
			$pay = $row['pay'];
		}
		else
		{
			$cursum = $row['sum'];
			$pay = 0;
		}
		$link = str_replace("'", "\'", $row['link']);
		if ($iscop)
		{
			$debtorfull = ParseNews3($row['debtorfull']);
			$debtorfull = str_replace("'", "\"", $debtorfull);
			$str = "show += line3(".$row['id'].",".$count.",".$row['percent'].",".$cursum.",'".$usrfull."','".$link."','".date('d.m.Y',$row['date1'])."','".$row['cell']."',".$row['status'].",'".$debtorfull."',".$row['pay'].");
";
		}
		else
		{
			$str = "show += line1(".$count.",".$row['percent'].",".$cursum.",'".$usrfull."','".$link."','".date('d.m.Y',$row['date1'])."','".$row['cell']."',".$pay.");
";
		}
		echo ($str);
		$count ++;
	}
	?>
	document.write(show);
	</script>
	</table>
	<br><p align=right>Страницы: <b>
	<?=ShowPages($p,$pages,5,$plink);?>
	</b></p>
	<?
}
else
{
	if (isset($_REQUEST['newval']) && $_REQUEST['id'] > 0)
	{
		$userip = ipCheck();
		
		if (strlen($sesid) > 3)
		{
			
			$tmp_body = file_get_contents('https://www.timezero.ru/cgi-bin/authorization.pl?login='.$nick2.'&ses='.$sesid.'&city='.$city);
			
			if (strpos($tmp_body, "OK"))
			{
				$cur_user = $nick;
				if (in_array($nick, $compens_cops))
				{
					$iscop = true;
				}
				else
				{
					$iscop = false;
				}
				$query = "SELECT `victim`, `status`, `history` FROM `compens` WHERE `id`='".addslashes($_REQUEST['id'])."' LIMIT 1;";
				$r = mysql_query($query);
				$res = mysql_fetch_array($r);
				if (strtolower(trim($nick)) == strtolower(trim($res['victim'])) && $res['status'] == 0)
				{
					// Protection against too smart guys
					$tmp = $_REQUEST['newval'];
					$tmp = round($tmp*100);
					if ($tmp < 5) {$tmp=5;}
					if ($tmp > 100) {$tmp=100;}
					$tmp = round($tmp/5);
					$tmp = ($tmp*5)/100;
					// End protection
					$cell = addslashes(strip_tags($_REQUEST['newcell']));
					$hist = $res['history']."{perc=".$tmp.", cell=".$cell.", ip=".$userip.", date=".time()."}";
					$query = "UPDATE `compens` SET `percent`='".$tmp."', `cell`='".$cell."', `history`='".$hist."' WHERE `id`='".addslashes($_REQUEST['id'])."' LIMIT 1;";
					mysql_query($query) or die(mysql_error());
				}
			}
		}
	}
	elseif (isset($_REQUEST['payed']) && $_REQUEST['payed'] > 0 && $iscop)
	{
		
		$query = "UPDATE `compens` SET `status`='2', `date2`='".time()."' WHERE `id`='".addslashes($_REQUEST['payed'])."' LIMIT 1;";
		mysql_query($query);
		
	}
	
	if (strlen($sesid) > 3)
	{
		$tmp_body = file_get_contents('https://www.timezero.ru/cgi-bin/authorization.pl?login='.$nick2.'&ses='.$sesid.'&city='.$city);
		
		if (strpos($tmp_body, "OK"))
		{
			$cur_user = $nick;
			if (in_array($nick, $compens_cops))
			{
				$iscop = true;
			}
			else
			{
				$iscop = false;
			}
		}
		else
		{
			$cur_user = "nofuckinguserfoundsonocredentialsgiven"; // I don't think there will ever exist a character with this kind of nick :crazy:
		}
	}
	else
	{
		$cur_user = "nofuckinguserfoundsonocredentialsgiven"; // I don't think there will ever exist a character with this kind of nick :crazy:
	}
	$query = mysql_query("SELECT COUNT(`id`) AS cnt FROM `compens` WHERE `status` = '1'");
	$r = mysql_fetch_array($query);
	if ($r['cnt'] > 0)
	{
		echo ("<b>Внимание!</b><br>В данный момент происходит выплата компенсаций, часть очереди заблокирована и недоступна для редактирования!<br><br>");
	}
	if ($cur_user !== "nofuckinguserfoundsonocredentialsgiven")
	{
		$query = mysql_query("SELECT COUNT(`id`) AS cnt FROM `compens` WHERE `status` < '2' AND `victim` = '".$cur_user."'");
		$r = mysql_fetch_array($query);
		$mine = $r['cnt'];
		if ($mine > 0)
		{
			if ($me == 1)
			{
				$q_cond .= " AND `victim` = '".$cur_user."'";
				?>
				<a href="?act=compens&sec=<?=$sec?>&user=<?=$nick?>&sessid=<?=$sesid?>&city=<?=$city?>">Все компенсации</a>
				<?
			}
			else
			{
				?>
				<a href="?act=compens&sec=<?=$sec?>&me=1&user=<?=$nick?>&sessid=<?=$sesid?>&city=<?=$city?>">Только мои компенсации (<?=$mine?>)</a>
				<?
			}
		}
		else
		{
			echo ("<font color='#808080'><b>Мои компенсации (0)</b></font>");
		}
	}
	if ($cur_user == "nofuckinguserfoundsonocredentialsgiven")
	{
		$plink = "act=compens&sec=".$sec;
		$link_add = "";
	}
	else
	{
		$plink = "act=compens&sec=".$sec."&user=".$nick."&sessid=".$sesid."&city=".$city;
		if ($me == 1) {$plink .= "&me=1";}
		if (strlen($_REQUEST['sn']) > 2) {$plink .= "&sn=".strip_tags($_REQUEST['sn'])."";}
		$link_add = "&user=".$nick."&sessid=".$sesid."&city=".$city;
	}
	$sq = mysql_query("SELECT COUNT(`id`) AS cnt FROM `compens` WHERE `status` < '2'");
	$sr = mysql_fetch_array($sq);
	$queue_length = $sr['cnt']; //Заявок в очереди
	$sq = "SELECT COUNT(`id`) as `number`, SUM(`percent`) as `percent` FROM `compens` WHERE `status` < '2' AND `percent` > '0'";
	$sr = mysql_query($sq);
	$srs = mysql_fetch_array($sr);
	//echo($queue_length."<br>");
	//echo($srs['number']."<br>");
	$sum_perc = $srs['percent'] + ($queue_length-$srs['number']);
	//echo($sum_perc);
	$sq = "SELECT SUM(`sum`) as `summa` FROM `compens` WHERE `status` < '2'";
	$sr = mysql_query($sq);
	$srs = mysql_fetch_array($sr);
	$queue_sum = round($srs['summa']*($sum_perc/$queue_length)); //Приблизительная сумма выплат в очереди
	$sq = mysql_query("SELECT COUNT(`id`) AS cnt FROM `compens` WHERE `status` = '2'");
	$sr = mysql_fetch_array($sq);
	$payed_out_length = $sr['cnt']; //Выплачено заявок
	$sq = "SELECT SUM(`sum`) as `summa`, SUM(`percent`) as `percent` FROM `compens` WHERE `status` = '2'";
	$sr = mysql_query($sq);
	$srs = mysql_fetch_array($sr);
	$payed_out_sum = round($srs['summa']*($srs['percent']/$payed_out_length)); //Приблизительная сумма совершенных выплат
	?>
	<script language="JavaScript" type="text/javascript">
	var extra = '?<?=$plink?>';
	function line1(count,value,sum,nick,flink,linkname,cell,pay)
	{
		if (pay == 1 && cell > 0)
		{
			var cnt = count;
		}
		else
		{
			var cnt = "--";
		}
		var txt = "";
		var val = Math.round(value*100);
		//		txt = '<tr bgcolor="#E8D395"><td nowrap>'+cnt+'</td><td>'+nick+'</td><td align="center"><a href="http://www.timezero.ru/cgi-bin/forum.pl?'+flink+'" target="_blank">'+linkname+'</a></td><td align="center">'+val+'%</td><td align="center">'+sum+'</td><td align="center">'+cell+'</td></tr>';
		txt = '<tr bgcolor="#E8D395"><td nowrap>'+cnt+'</td><td>'+nick+'</td><td align="center"><a href="http://www.timezero.ru/cgi-bin/forum.pl?'+flink+'" target="_blank">'+linkname+'</a></td><td align="center">'+val+'%</td><td align="center">'+sum+'</td></tr>';
		return txt;
	}
	function line2(id,count,value,sum,nick,flink,linkname,cell,status,debtor,pay)
	{
		var txt = "";
		var val = Math.round(value*100);
		if (status == 0) {ststr = "";} else {ststr = " disabled";}
		if (debtor == "") {var showdeb = "";}
		if (pay == 1 && cell > 0)
		{
			var cnt = count;
			if(debtor !== "") {var showdeb = '<hr><i>'+debtor+'</i>';}
		}
		else
		{
			var cnt = "--";
			if(debtor !== "") {var showdeb = '<hr><i>'+debtor+'</i><br><input name="unblock" type="button" onClick="if (!confirm(\'Вы уверены, что данный персонаж вышел с каторги?\')) {return false;} else {window.location.href=\''+extra+'&unblock='+id+'\'}" value="разблокировать">';}
		}
		txt = '<tr bgcolor="#E8D395"><td nowrap>'+cnt+'</td><td><font color="blue">'+nick+'</font></td><td align="center"><a href="http://www.timezero.ru/cgi-bin/forum.pl?'+flink+'" target="_blank">'+linkname+'</a></td><td align="center"><select name="percent'+id+'" size="1" id="percent'+id+'" onChange="UpdDisp(this.options[this.selectedIndex].value,\''+id+'\','+sum+');"'+ststr+'>';
		for (i=1; i<=20; i++)
		{
			var prc = i*5;
			var prc2 = prc/100;
			if (prc2 == value)
			{
				txt += '<option value="'+prc2+'" selected>'+prc+'%</option>';
			}
			else
			{
				txt += '<option value="'+prc2+'">'+prc+'%</option>';
			}
		}
		if (value > 0)
		{
			var showsum = Math.round(sum*value);
		}
		else
		{
			var showsum = sum;
		}
		txt +='</select><input name="ok" type="button" onClick="if (!confirm(\'Сохранить желаемый процент выплаты?\')) {return false;} else {UpdateBase(\''+id+'\');}" value="OK"'+ststr+'></td><td align="center"><div id="paym'+id+'"><b>'+showsum+'</div> '+showdeb+'</b></td></tr>';
		return txt;
	}
	<? if ($iscop) { ?>
		function line3(id,count,value,sum,nick,flink,linkname,cell,status,debtor,pay)
		{
			if (pay == 1 && cell > 0)
			{
				var cnt = count;
			}
			else
			{
				var cnt = "--";
			}
			var txt = "";
			var val = Math.round(value*100);
			if (status == 1){pref = '<font color="red"><b>!</b></font> ';}
			else{pref = '';}
			if (debtor == "") {var showdeb = "";} else {var showdeb = '<br><i>'+debtor+'</i>';}
			txt = '<tr bgcolor="#E8D395"><td nowrap>'+cnt+'</td><td>'+pref+nick+' [<a href="?act=compens&payed='+id+'<?=$link_add?>" onClick="if (!confirm(\'Вы уверены?\')) {return false;}"><b>оплачено</b></a>] [<a href="?act=compens_add&id='+id+'<?=$link_add?>" target="_blank"><b>изменить</b></a>]</td align="center"><td align="center"><a href="http://www.timezero.ru/cgi-bin/forum.pl?'+flink+'&m=1" target="_blank">'+linkname+'</a></td><td align="center">'+val+'%</td><td align="center">'+sum+' '+showdeb+'</td></tr>';
			return txt;
		}
		<?}?>
	function UpdDisp(value,id,sum)
	{
		var newsum = Math.round(sum*value);
		tid = 'paym'+id;
		document.getElementById(tid).innerHTML = '<b>'+newsum+'</b>';
	}
	function UpdateBase(id)
	{
		sid = 'percent'+id;
		cid = 'cell'+id;
		var newval = document.getElementById(sid).options[document.getElementById(sid).selectedIndex].value;
		document.getElementById("newval").value = newval;
		document.getElementById("id").value = id;
		document.getElementById("hiddenform").submit();
	}
	</script>
	<form id="hiddenform" name="hiddenform" method="post" action="index.php?<?=$plink?>">
	<input type="hidden" name="act" id="user" value="compens">
	<input type="hidden" name="user" id="user" value="<?=$nick?>">
	<input type="hidden" name="sessid" id="sessid" value="<?=$sesid?>">
	<input type="hidden" name="city" id="city" value="<?=$city?>">
	<input type="hidden" name="newval" id="newval" value="1">
	<input type="hidden" name="newcell" id="newcell" value="75000">
	<input type="hidden" name="id" id="id" value="0">
	<input type="hidden" name="p" id="p" value="<?=$p?>">
	</form>
	<br><a href="?act=compens&sec=payed_out&me=<?=$me?>&user=<?=$nick?>&sessid=<?=$sesid?>&city=<?=$city?>">Выплаченные компенсации</a><br>
	<a href="?act=compens&sec=queue&me=<?=$me?>&user=<?=$nick?>&sessid=<?=$sesid?>&city=<?=$city?>">Очередь компенсаций</a>
	<hr>
	Компенсаций в очереди: <b><?=$queue_length?></b> на сумму около <b><?=$queue_sum?></b> м.м.<br>
	Компенсаций выплачено: <b><?=$payed_out_length?></b> на сумму около <b><?=$payed_out_sum?></b> м.м.<br>
	<hr>
	<?
	if ($iscop)
	{
		$link222 = eregi_replace("&sn=".$_REQUEST['sn']."", "", $plink);
		$link222 = eregi_replace("&sn2=".$_REQUEST['sn2']."", "", $link222);
		echo "<form name=\"search\" method=\"post\" action=\"index.php?".$link222."\">\n";
		echo "Поиск по нику: <input type=\"text\" name=\"sn\" value=\"".addslashes($_REQUEST['sn'])."\">\n";
		echo "Поиск по должнику: <input type=\"text\" name=\"sn2\" value=\"".addslashes($_REQUEST['sn2'])."\">\n";
		echo "<input type=\"submit\" value=\"OK\">\n";
		echo "</form>\n";
	}
	?>
	<br><p align=right>Страницы: <b>
	<?=ShowPages($p,$pages,5,$plink);?>
	</b></p>
	<table width="100%"  border="0" cellspacing="3" cellpadding="3" align="center">
	<tr bgcolor="#948559">
	<td width="1" nowrap align="center"><b>№</b></td>
	<td align="center"><b>Потерпевший</b></td>
	<td align="center"><b>Дата</b></td>
	<td align="center"><b>К выплате</b></td>
	<td align="center"><b>Сумма</b></td>
	</tr>
	<script language="JavaScript" type="text/javascript">
	var show="";
	<?
	if ($sec=="payed_out") $query = "SELECT * FROM `compens` WHERE `status`='2'".$q_cond." ORDER BY `date2`".$q_limit;
	else  $query = "SELECT * FROM `compens` WHERE `status`<'2'".$q_cond." ORDER BY (`percent` = '0') ASC, `pay` DESC, `percent` ASC, `date1` ASC".$q_limit;
	$r = mysql_query($query) or die(mysql_error());
	$count = 1 + (($p-1)*$caseonpage);
	while ($row = mysql_fetch_array($r))
	{
		if ($sec=="payed_out")
		{
			$usrfull = ParseNews3($row['vfull']);
			$usrfull = str_replace("'", "\"", $usrfull);
			$cursum = round($row['percent']*$row['sum']);
			$link = str_replace("'", "\'", $row['link']);
			$str = "show += line1(".$count.",".$row['percent'].",".$cursum.",'".$usrfull."','".$link."','".date('d.m.Y',$row['date2'])."','".$row['cell']."');
";
			echo ($str);
		}
		elseif ($iscop)
		{
			$usrfull = ParseNews3($row['vfull']);
			$debtorfull = ParseNews3($row['debtorfull']);
			$usrfull = str_replace("'", "\"", $usrfull);
			$debtorfull = str_replace("'", "\"", $debtorfull);
			if ($row['percent'] > 0)
			{
				$cursum = round($row['percent']*$row['sum']);
			}
			else
			{
				$cursum = $row['sum'];
			}
			$link = str_replace("'", "\'", $row['link']);
			$str = "show += line3(".$row['id'].",".$count.",".$row['percent'].",".$cursum.",'".$usrfull."','".$link."','".date('d.m.Y',$row['date1'])."','".$row['cell']."',".$row['status'].",'".$debtorfull."',".$row['pay'].");
";
			//                $str = str_replace("'", "\'", $str);
			echo ($str);
		}
		elseif (($me == 1 && $cur_user !== "nofuckinguserfoundsonocredentialsgiven") || (strlen($_REQUEST['sn'])>2))
		{
			$query2 = "SELECT `id` FROM `compens` WHERE `status`<'2' AND `percent`<='".($row['percent'])."' AND `id`<>'".$row['id']."' AND `debtor` = ''";
			$rs2 = mysql_query($query2);
			$c2 = mysql_num_rows($rs2);
			if ($c2 > 0)
			{
				$query2 = mysql_query("SELECT COUNT(`id`) AS cnt FROM `compens` WHERE `status`<'2' AND (`percent`<='".($row['percent'])."' OR `date1`<'".$row['date1']."') AND `id`<>'".$row['id']."' AND `debtor`='' AND `cell`>'0'");
				$rs2 = mysql_fetch_array($query2);
				//		                echo ($query2."\n");
				$c2 = $rs2['cnt'] + 1;
			}
			else
			{
				$c2 = $c2+1;
			}
			$usrfull = ParseNews3($row['vfull']);
			$debtorfull = ParseNews3($row['debtorfull']);
			$usrfull = str_replace("'", "\"", $usrfull);
			$debtorfull = str_replace("'", "\"", $debtorfull);
			$link = str_replace("'", "\'", $row['link']);
			if ($me == 1 && $cur_user !== "nofuckinguserfoundsonocredentialsgiven")
			{
				$str = "show += line2(".$row['id'].",'~ ".$c2."',".$row['percent'].",".$row['sum'].",'".$usrfull."','".$link."','".date('d.m.Y',$row['date1'])."','".$row['cell']."',".$row['status'].",'".$debtorfull."',".$row['pay'].");
";
			}
			else
			{
				$str = "show += line1('~ ".$c2."',".$row['percent'].",".$row['sum'].",'".$usrfull."','".$link."','".date('d.m.Y',$row['date1'])."','".$row['cell']."',".$row['pay'].");
";
			}
			echo ($str);
		}
		elseif (strtolower($cur_user) == strtolower($row['victim']))
		{
			$usrfull = ParseNews3($row['vfull']);
			$debtorfull = ParseNews3($row['debtorfull']);
			$usrfull = str_replace("'", "\"", $usrfull);
			$debtorfull = str_replace("'", "\"", $debtorfull);
			$link = str_replace("'", "\'", $row['link']);
			$str = "show += line2(".$row['id'].",".$count.",".$row['percent'].",".$row['sum'].",'".$usrfull."','".$link."','".date('d.m.Y',$row['date1'])."','".$row['cell']."',".$row['status'].",'".$debtorfull."',".$row['pay'].");
";
			//				$str = str_replace("'", "\'", $str);
			echo ($str);
		}
		else
		{
			$usrfull = ParseNews3($row['vfull']);
			$usrfull = str_replace("'", "\"", $usrfull);
			if ($row['percent'] > 0)
			{
				$cursum = round($row['percent']*$row['sum']);
			}
			else
			{
				$cursum = $row['sum'];
			}
			$link = str_replace("'", "\'", $row['link']);
			$str = "show += line1(".$count.",".$row['percent'].",".$cursum.",'".$usrfull."','".$link."','".date('d.m.Y',$row['date1'])."','".$row['cell']."',".$row['pay'].");
";
			//                $str = str_replace("'", "\'", $str);
			echo ($str);
		}
		$count ++;
	}
	?>
	document.write(show);
	</script>
	</table>
	<br><p align=right>Страницы: <b>
	<?=ShowPages($p,$pages,5,$plink);?>
	</b></p>
	<?
	if ($me == 1 || strlen($_REQUEST['sn'])>2)
	{
		?>
		<b>Внимание!</b> В данном разделе очередность заявки рассчитывается приблизительно и может использоваться лишь для приближенной оценки места в очереди.
		<?
	}
}
?>

<hr>
<font size="-2">Примечание.<br>В связи с необходимостью округления сумм выплат до целых значений, существует вероятность ошибки в отображаемых данных. Ошибка в каждом конкретном случае не превышает значения в 1 м.м. и может превышать это значение только в разделе статистики.</font>
<hr>
<b>Краткая шпаргалка</b><br><br>
<br> <b>1.</b> Сервис использует регистрацию ТЗ, поэтому для выполнения всех действий Вам необходимо быть залогиненным в игре.
<br> <b>2.</b> После того как следователь закрыл дело, в течении трех дней сумма должна быть перенесена на сайт. Проследить за этим должны Вы сами, в случае отсутствия Вашей компенсации через 3 дня после закрытия дела не стесняйтесь напомнить о себе следователю.
<br> <b>3.</b> Когда сумма внесена Вы должны указать процент, который желаете получить. Согласившись на понижение процента вы тем самым передвигаете вашу заявку к началу списка.
<br> Соглашаться получить меньше или ждать полной выплаты решать вам самим. Процент можно изменить в любое время любое число раз. После смены вы автоматически увидите текущее местоположение Вашей заявки в общей очереди.
<br> <b>4.</b> Если под суммой указан ник персонажа, это проценты. Они подлежат компенсации только после того, как указанный персонаж выйдет с каторги. Для разблокировки такой записи нажмите кнопку под ником. Если должник освобожден то ник из строчки пропадет и заявка  подставится в очередь выплат.