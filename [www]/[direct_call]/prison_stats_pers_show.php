<?
	Header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	Header('Cache-Control: no-cache, must-revalidate');
	Header('Pragma: no-cache');
	Header('Last-Modified: '.gmdate("D, d M Y H:i:s").'GMT');

	require('/home/sites/police/www/_modules/functions.php');

//=========================
	require_once('/home/sites/police/dbconn/dbconn2.php');
//=========================

	function anti_sql($text){
	// транслирем все кавычки
		$text = str_replace("'", "&#039;", $text);
		$text = str_replace("’", "&#039;", $text);
		$text = str_replace("\"", "&quot;", $text);
		$text = str_replace("`", "&#039;", $text);

		return $text;
	}

	$need_ref = 'http://'.$_SERVER['HTTP_HOST'].'/direct_call/prison_stats_pers_show.php';
	$cur_ref = $_SERVER['HTTP_REFERER'];
	error_reporting(0);
?>
<html>
<head>
  <title>You're not supposed to see this :crazy:</title>
  <LINK href="../_modules/tzpol_css.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#EBDFB7" text="#455600" alink="#0D3AB4" link="#0D3AB4" vlink="#0D3AB4">
<table width="100%"  border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td>
<?php
$nick = 0;
$sid = 0;
$city = 0;
if(@$_REQUEST['nick']) $nick = $_REQUEST['nick'];
if(@$_REQUEST['sid']) $sid = $_REQUEST['sid'];
if(@$_REQUEST['city']) $city = $_REQUEST['city'];
if(@$_REQUEST['level']) $level = $_REQUEST['level'];
if(@$_REQUEST['pro']) $pro = $_REQUEST['pro'];
if ($pro < 1) { $pro = '0'; }
if(@$_REQUEST['clan']) $clan = $_REQUEST['clan'];
if ($level > 0) {
	if (strlen($clan) > 1) {
		$show_nick = "<img src='../_imgs/clans/".$clan.".gif' border='0'><b>".$nick.'</b> ['.$level."]<img src='../_imgs/pro/i".$pro.".gif' border='0' style='vertical-align:text-bottom'>";
	} else {
		$show_nick = '<b>'.$nick.'</b> ['.$level."]<img src='../_imgs/pro/i".$pro.".gif' border='0' style='vertical-align:text-bottom'>";
	}
} else {
	$show_nick = '<b>'.$nick.'</b>';
}

if (strlen($sid) > 3 && $need_ref == $cur_ref) {
	$sock = @fsockopen("www.timezero.ru", 80, $er1, $er2, 5);
	if(@$sock) {
		$addr = "/cgi-bin/authorization.pl?login=".urlencode($nick)."&ses=".$sid."&city=".$city;
		fputs($sock, "GET ".$addr." HTTP/1.0\r\n");
		fputs($sock, "Host: www.timezero.ru \r\n");
		fputs($sock, "Content-type: application/x-www-url-encoded \r\n");
		fputs($sock, "\r\n\r\n");
		$tmp_headers = '';
		while ($str = trim(fgets($sock, 4096))) $tmp_headers .= $str."\n";
		$tmp_body = '';
		while (!feof($sock)) $tmp_body .= fgets($sock, 4096);
	}
	if (strpos($tmp_body, "OK")) {
		$query = 'SELECT `log_date`, `log_time` FROM `bot_prison_logs` ORDER BY `id` DESC LIMIT 1;';
		$rs = mysql_query($query) or die (mysql_error());
		list($last_date, $last_time) = mysql_fetch_row($rs);
		$ld = $last_date.' '.$last_time;
		$last_date = strtotime($ld);

		echo '<div align="left"><font size="-2">Показаны данные по состоянию на <b>'.date("d.m.Y, H:i", $last_date).'</b></font><br><br></div>';

		$nick = anti_sql($nick);

		$query = 'SELECT * FROM `prison_chars` WHERE `nick` = "'.$nick.'" LIMIT 1;';
		$rez = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($rez) > 0) {
		//	list($c_id, $c_nick, $c_term, $c_coll, $c_l_pay, $c_reas, $c_rem, $c_dept, $add_date, $add_by, $add_level, $allow_udo, $want_udo, $answer_udo, $coll_by_rating) = mysql_fetch_row($rez);
			$row = mysql_fetch_assoc($rez);
			$id = $row['id'];
			$nick = $row['nick'];
			$term = $row['term'];
			$coll = $row['collected'];
			$last_pay = $row['last_pay'];
			$reason = $row['reason'];
			$remark = $row['remark'];
//			$c_dept = $row['dept'];
			$c_add = $row['add_date'];
			$c_addby = $row['add_by'];
			$addlevl = $row['add_level'];
			$allow_udo = $row['allow_udo'];
			$want_udo = $row['want_udo'];
			$answerudo = $row['answer_udo'];
			$ratingres = $row['coll_by_rating'];

			echo '<div align="center">Статистика по персонажу '.$show_nick.'<br><br></div>';
			if ($reason > 0) {
				echo 'Причина отправки на каторгу: <b>'.$crime[$reason].'</b><BR>';
				echo 'Срок: <b>'.$term.'</b> ресурсов<BR>';
				echo 'Зачтено: <b>'.$coll.'</b> ресурсов<BR>';
				echo 'Дата последней сдачи ресурсов: <b>'.date('d.m.Y, H:i', $last_pay).'</b><BR>';
				if($allow_udo=='1'){
					#$need_udo = (($term*0.3)-$coll);
					$need_udo = 1;
					if($need_udo<=0){
						if(isset($_REQUEST['udo'])) {
							$query = 'UPDATE `prison_chars` SET `want_udo` = 1 WHERE `nick` = "'.$nick.'";';
							if(mysql_query($query, $link)){
								$want_udo = '1';
							}
						}
						echo 'Cумма для оплаты УДО: <b>'.round(($term-$coll)*1.5 ).'</b> м.м.<br>';
						if($want_udo=='1'){
							echo '<center><B>Заявка на УДО подана</b><BR><div style="font-size: 14px; color: red; text-decoration: blink;" align="center">Оплату по УДО отправлять <b>только после получения телеграммы</b> от сотрудника ОИН!!!</div></center>';
						} else {
							echo '<form method="post" action="http://'.$_SERVER['HTTP_HOST'].'/direct_call/prison_stats_pers_show.php">
	<input type="hidden" name="nick" value="'.$nick.'">
	<input type="hidden" name="sid" value="'.$sid.'">
	<input type="hidden" name="city" value="'.$city.'">
	<input type="hidden" name="level" value="'.$level.'">
	<input type="hidden" name="pro" value="'.$pro.'">
	<input type="hidden" name="clan" value="'.$clan.'">
	<input type="hidden" name="udo" value="1">
	<input type="hidden" value="Подать заявку на УДО">
</form>';
						}
					} else {
						#echo 'Не хватает для УДО: <b>'.$need_udo.'</b> ресурсов<BR>';
					}
				} else {
					if($reason == 90) {
						echo "<br>Обратитесь к сотруднику Администрации, для получения разрешения на выход по УДО. <br> После получения обратитесь к любому сотруднику ОИН.";
					} else {
						echo "<br>По Вашей статье УДО запрещено.";
					}
				}
			} else {
				echo 'Для указанного персонажа не определен срок заключения. Обратитесь к офицеру, ответственному за работу с каторгой, для исправления этой ошибки.';
			}
		} else {
			echo '<div align="center">Персонаж <b>'.$show_nick.'</b> не найден в списке заключенных.</div><br><br>Возможные причины: <ul><li>Персонаж еще не сдал ни одного ресурса<li>Персонаж попал на каторгу и сдавал ресурсы после последнего обновления статистики<li>Персонаж не был отправлен на каторгу =)</ul>';
		}
	} else {
?>
<div align="center">
<b>Ошибка авторизации!</b><br><br>
Войдите в игру персонажем с использованием версии игры <b>не ниже 3.21</b><br>Внимание! Авторизация с помощью модифицированного клиента как правило невозможна!<br><br>.<br><br>
<input type="button" value="Повторить попытку" onClick="javascript: location.reload(true)">
</div>
<?
	}
} else {
?>
<script language="Javascript" type="text/javascript">
<!--
function tz_DoFSCommand(command, args) {
	var tmp = args.split("\t");
	if (command == "OK"){
		document.view_pers.nick.value = tmp[0];
		document.view_pers.sid.value = tmp[1];
		document.view_pers.city.value = tmp[2];
		document.view_pers.level.value = tmp[3];
		document.view_pers.pro.value = tmp[4];
		document.view_pers.clan.value = tmp[5];
		document.view_pers.submit();
	} else {
		men = document.getElementById('reload');
		men.style.display='';
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
//-->
</script>
<OBJECT id="tz" codeBase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" height="1" width="1" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><PARAM NAME="movie" VALUE="../_imgs/auth3.swf"><PARAM NAME="wmode" VALUE="transparent">
<embed src="../_imgs/auth3.swf" wmode="transparent" width="1" height="1" swLiveConnect="true" id="tz" name="tz" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</OBJECT>
<div id="reload" style="display:none" align="center">
<b>Ошибка авторизации!</b><br><br>
Войдите в игру персонажем с использованием версии игры <b>не ниже 3.21</b><br>Внимание! Авторизация с помощью модифицированного клиента как правило невозможна!<br><br>
<input type="button" value="Повторить попытку" onClick="javascript: location.reload(true)">
</div>
<form name="view_pers" method="post" action="">
  <input type="hidden" name="nick" value="">
  <input type="hidden" name="sid" value="">
  <input type="hidden" name="city" value="">
  <input type="hidden" name="level" value="">
  <input type="hidden" name="pro" value="">
  <input type="hidden" name="clan" value="">
</form>
<?
}
mysql_close($link);
?>
	</td>
  </tr>
</table>
</body>
</html>