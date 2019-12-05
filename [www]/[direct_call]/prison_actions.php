<?

/*OPERATIONS
1 - Add
2 - Modify
3 - Deny UDO
4 - Grant UDO
5 - Let out
*/
//=========================
	require('/home/sites/police/dbconn/dbconn2.php');
	require('/home/sites/police/dbconn/dbconn.php');
//=========================
	require('../_modules/functions.php');
	require('../_modules/auth.php');


if (AuthStatus==1 && (substr_count(AuthUserRestrAccess, '-prison-') > 0 || AuthUserClan == 'police' || AuthUserGroup == 100)) {
	if (substr_count(AuthUserRestrAccess, '-prison-') > 0)
		$mega = 1;
	else
		$mega = 0;



?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<head>
  <title>You're not supposed to see this =)</title>
<LINK href="../_modules/tzpol_css.css" rel="stylesheet" type="text/css">
<? include('../_modules/java.php'); ?>
</head>
<body bgcolor="#EBDFB7" text="#455600" alink="#0D3AB4" link="#0D3AB4" vlink="#0D3AB4">
<script language="JavaScript" type="text/javascript">
var res = 0;
var SaveTpl = new Array();
SaveTpl[0] = "По итогам проверки отбытого срока вам засчитано «{R}» ресурсов.\nОстальные ресурсы не зачтены в связи с передачами:\n-\nСрок, назначенный органом правосудия, подлежит отработке ЛИЧНО Осужденным c использованием вещей из Арсенала Каторги. Остаточный срок смотрите в модуле каторжника.";
SaveTpl[1] = "По итогам проверки отбытого срока вам засчитано «{R}» ресурсов.\nОстальные ресурсы не зачтены в связи с мародерством:\n-\nСрок, назначенный органом правосудия, подлежит отработке ЛИЧНО Осужденным c использованием вещей из Арсенала Каторги. Остаточный срок смотрите в модуле каторжника.";
SaveTpl[2] = "";
function ClBrd(text){
    while(text.indexOf('<BR>')>=0) text = text.replace('<BR>','\015\012');
    if (window.clipboardData){window.clipboardData.setData("Text", text);}
    else {
                netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');
                var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
                if (!clip) return;
                var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
                if (!trans) return;
                trans.addDataFlavor('text/unicode');
                var str = new Object();
                var len = new Object();
                var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
                var copytext=text;
                str.data=copytext;
                trans.setTransferData("text/unicode",str,copytext.length*2);
                var clipid=Components.interfaces.nsIClipboard;
                if (!clip) return false;
                clip.setData(trans,null,clipid.kGlobalClipboard);
        }
}
function reloadReason(s,t,res) {	var v = (t)?s:s.value;
	var text = SaveTpl[v];
	text = text.replace(/{R}/,res);    document.getElementById('telegram').value = text;}
</script>
<table width="90%"  border="0" align="center" cellpadding="3" cellspacing="2">
  <tr>
    <td align="center">
    <form name="state_request" method="post" action="/direct_call/prison_actions.php">
      ник:
          <input type="text" name="nick" value="<?=$_REQUEST['nick']?>">
          <input type="submit" name="Submit" value="посмотреть">
    </form>
<?
	if ($_REQUEST['sec'] == 'add') {
		$query = 'SELECT * FROM `prison_chars` WHERE `nick` = "'.mysql_escape_string($_REQUEST['nick']).'" AND `term` > 0;';
		$tmp = mysql_query($query, $link) or die(mysql_error());
		if (mysql_num_rows($tmp) == 0) {
			$tmp_user = locateUser($_REQUEST['nick']);
			#print_r($tmp_user);
			// теперь на каторге профу каторжника не выдают =(
			//if ($tmp_user['pro'] != 13)
			//	die('<b>Персонаж <a href="javascript:{}" onClick="ClBrd(\''.$_REQUEST['nick'].'\');">'.$_REQUEST['nick'].'</a> не имеет профессии каторжника</b>');

			$query = 'DELETE FROM `prison_chars` WHERE nick = "'.mysql_escape_string($_REQUEST['nick']).'"';
			mysql_query($query, $link) or die(mysql_error());
			$query = 'INSERT INTO `prison_chars` SET `nick` = "'.mysql_escape_string($_REQUEST['nick']).'", `term` = "-1", `collected` = "0", `reason` = "-1", `remark` = "", `dept` = "-1", `add_date` = "'.date('Y-m-d', time()).'", `add_by` = "'.AuthUserName.'", `add_level` = "'.$tmp_user['level'].'";';
		//	$query = "INSERT INTO `prison_chars` (`id`, `nick`, `term`, `collected`, `last_pay`, `reason`, `remark`, `dept`, `add_date`, `add_by`, `add_level`) VALUES ('', '".$_REQUEST['nick']."', '-1', '0', '".time()."', '-1', '', '-1', '', '".AuthUserName."', '".$tmp_user['level']."');";
			mysql_query($query, $link) or die(mysql_error());
			$query = "INSERT INTO `prison_actions_log` SET `date`='".time()."', `cop`='".AuthUserName."', `prisoner`='".mysql_escape_string($_REQUEST['nick'])."', `operation`='1'";
			mysql_query($query,$link) or die(mysql_error());
			$new_man = 1;
		}
	}

	if (isset($_REQUEST['nick'])) {
		if (!$mega && !$new_man)
			$moreonly = 1;
		else
			$moreonly = 0;

		$query = 'SELECT * FROM `prison_chars` WHERE `nick` = \''.mysql_escape_string($_REQUEST['nick']).'\' LIMIT 1;';
		$rs = mysql_query($query, $link) or die (mysql_error());
		if (mysql_num_rows($rs) > 0) {
			 $row = mysql_fetch_assoc($rs);
			//print_r($row);
			 $id = $row['id'];
			 $nick = $row['nick'];
			 $term = $row['term'];
			 $collected = $row['collected'];
			 $last_pay = $row['last_pay'];
			 $reason = $row['reason'];
			 $remark = $row['remark'];
//			 $c_dept = $row['dept'];
			 $c_add = $row['add_date'];
			 $c_addby = $row['add_by'];
			 $c_addlevl = $row['add_level'];
			 $c_allowudo = $row['allow_udo'];
			 $c_wantudo = $row['want_udo'];
			 $c_answerudo = $row['answer_udo'];
			 $c_ratingres = $row['coll_by_rating'];

?>
<a href="javascript:{}" onClick="ClBrd('<?=$nick?>');">в буфер обмена</a> <? if ($mega) { ?> || <a href="?del=<?=$nick?>" onClick="if(!confirm('Вы уверены?')) return false;">удалить</a> <? } ?>
<form name="prison_char" method="post" action="/direct_call/prison_actions.php?step=2">
        <input type="hidden" name="id" value="<?=$id?>">
        <input type="hidden" name="prisoner" value="<?=$nick?>">
        <input type="hidden" name="step" value="2">
        <input type="hidden" name="new_dept" value="<?=$dept?>">
<?
			if ($_REQUEST['sec'] == 'add' && $new_man == 1) {
				echo '<input type="hidden" name="add_ok" value="1">';
			}
?>

    <br>
    Причина:
    <select name="reason" <? if ($moreonly) echo 'disabled'; ?>>
<?
			if ($reason == -1) {
				echo '<option value="-1" selected>УКАЖИТЕ ПРИЧИНУ!!! :mad:</option>';
			}
			foreach ($crime_list as $tarr)
				{
					foreach ($tarr as $key => $value)
						{
							echo '<option value="'.$key.'"';
							if($reason == $key) echo ' selected';
							echo '>'.$value.'</option>';
						}
				}

?>
    </select>
    <br>Взломан?:
<?
			echo '<select name="allow_udo" '.(($moreonly)?' DISABLED':'').'>';
			$udos = array('Нет', 'Да');
			for($i=0; $i<2; $i++){
				echo '<option value="'.$i.'"';
				if($c_allowudo == $i || !$c_allowudo && $i == 0) echo ' selected';
				echo '>'.$udos[$i].'</option>';
			}
			echo '</select>';
?>
    <BR>
    Срок:
    <input name="term" type="text" size="6" value="<?=$term?>">
<?
			if ($moreonly)
				echo '<input type="hidden" name="oldterm" value="'.$term.'">';
?>
    Собрано:
    <input name="collected" type="text" size="6" value="<?=$collected?>" <?if ($_REQUEST['sec'] == 'add') echo 'disabled';?> <?if ($moreonly) echo 'disabled';?>>
    <br>
	Призовые ресы: <b><?=$c_ratingres?></b> <font size="-2"><i>(эти ресурсы уже включены в количество в поле "Собрано")</i></font>
	<br><br>Примечание:<br>
    <textarea name="remark" cols="35" rows="5"><?=$remark?></textarea>
    <br>
    <input type="submit" name="Submit" value="изменить">
</form>
<?
		} else {
			echo 'Ошибка. Персонаж <a href="#; return false" onClick="ClBrd(\''.$_REQUEST['nick'].'\');">'.$_REQUEST['nick'].'</a> не найден.';
		}
	}

	if ($_REQUEST['step'] == '2') {
		
		//if ($_REQUEST['term'] < $_REQUEST['oldterm']) die ('<b>Новый срок меньше старого</b>');
		// пока коментим, на будущее - продумать включение модуля в новую систему доступа.
		
		if ($_REQUEST['add_ok']) {
			$query = 'UPDATE `prison_chars` SET `collected` = 0, `term` = \''.$_REQUEST['term'].'\', `remark` = \''.$_REQUEST['remark'].'\', `reason` = \''.$_REQUEST['reason'].'\', `allow_udo` = \''.$_REQUEST['allow_udo'].'\', add_date=\''.strftime("%Y-%m-%d").'\' WHERE `id` = \''.$_REQUEST['id'].'\' LIMIT 1;';
		} elseif ($_REQUEST['oldterm']) {
			$query = 'UPDATE `prison_chars` SET `term` = \''.$_REQUEST['term'].'\', `remark` = \''.$_REQUEST['remark'].'\' WHERE `id` = \''.$_REQUEST['id'].'\' LIMIT 1;';
		} else {
			$query = 'UPDATE `prison_chars` SET `collected` = \''.$_REQUEST['collected'].'\', `term` = \''.$_REQUEST['term'].'\', `remark` = \''.$_REQUEST['remark'].'\', `reason` = \''.$_REQUEST['reason'].'\', `allow_udo` = \''.$_REQUEST['allow_udo'].'\' WHERE `id` = \''.$_REQUEST['id'].'\' LIMIT 1;';
		}
		echo 'Информация персонажа <b>'.$_REQUEST['prisoner'].'</b> обновлена.';
		mysql_query($query, $link) or die(mysql_error());
		$query = "INSERT INTO `prison_actions_log` SET `date`='".time()."', `cop`='".AuthUserName."', `prisoner`='".mysql_escape_string($_REQUEST['prisoner'])."', `operation`='2'";
		mysql_query($query,$link) or die(mysql_error());
	}
    if (isset($_REQUEST['del'])) {
		if(!$mega) die('<b>Нехватает доступа</b>');
        $user = locateUser(trim($_REQUEST['del']));
        $userParam = formatUser($user);
        $user['login'] = trim($_REQUEST['del']);

		$q = "SELECT * FROM `prison_chars` WHERE `nick` = '".$_REQUEST['del']."';";
		$q2 = mysql_query($q);
		$r2 = mysql_fetch_array($q2);
		$qqq = "INSERT INTO `prison_manualdelete` SET `nick` = '".$_REQUEST['del']."', `res` = '".$r2['collected']."', `date` = '".time()."';";
		mysql_query($qqq,$link) or die (mysql_error());
		$query = 'DELETE FROM `prison_chars` WHERE `nick` = \''.$_REQUEST['del'].'\' LIMIT 1;';
		mysql_query($query, $link) or die(mysql_error());
		echo "<span style='color: green;'>Персонаж $userParam удален.</span>";
		$query = "INSERT INTO `prison_actions_log` SET `date`='".time()."', `cop`='".AuthUserName."', `prisoner`='".mysql_escape_string($_REQUEST['del'])."', `operation`='5'";
		mysql_query($query,$link) or die(mysql_error());
	}

	if (isset($_REQUEST['nodel'])) {
		if(!$mega) die('<b>Нехватает доступа</b>');

		$user = locateUser(trim($_REQUEST['nodel']));
        $userParam = formatUser($user);
        $user['login'] = trim($_REQUEST['nodel']);

        $prisonerInfo = mysql_query("SELECT * FROM prison_chars WHERE nick='".$user[login]."' LIMIT 1",$link);
        $prisonerInfo = mysql_fetch_array($prisonerInfo);

        if(!$prisonerInfo[nick]) {
        	echo "<span style='color: red;'>Персонаж $userParam не найден в списке заключённых. Возможно Вы уже удалили его.</span>";
        } else {
			$telegram = str_replace("\n\n","\n",$_REQUEST['telegram']);
			$telegram = str_replace("\n","\r",$telegram);
			$ttext = "По итогам проверки отбытого срока, Вам засчитаны все ресурсы требуемые для освобождения.";
			$ttext = ($telegram)?$telegram:$ttext;
			if(!isset($_REQUEST['ok'])) {
				echo "
				<form name='nodel' method='post' action='/direct_call/prison_actions.php'>
				<table cellspacing=3 cellpadding=3 border=0 width=100% style='font-size: 12px;'>
					<tr>
						<th>Отказ на освобождение</th>
					</tr>
					<tr>
						<td>$userParam</td>
					</tr>
					<tr>
						<td>Срок: <input name='term' type='text' value='".$prisonerInfo[term]."'> | отработано: <input name='realcol' type='text' value='".$prisonerInfo[collected]."'></td>
					</tr>
					<tr>
						<td>
						<select name='reason' onchange='reloadReason(this,0,".$prisonerInfo[collected].");'>
							<option value='0'>Шаблон: Обнаружена передача ресурсов</option>
							<option value='1'>Шаблон: Обнаружен факт марадёрства</option>
							<option value='2'>Шаблон: Ручной ввод</option>
						</select>

						</td>
					</tr>
					<tr>
						<td><textarea name='telegram' id='telegram' style='width: 100%; height: 150px;'>$ttext</textarea></td>
					</tr>
					<tr>
						<th><input type='submit' value='Сохранить и отправить'><input type='button' value='Отмена' onclick=\"window.location.href='/direct_call/prison_actions.php'\"></th>
					</tr>
					</table>
					<input name='nodel' type='hidden' value='".$user[login]."'>
					<input name='ok' type='hidden' value='1'>
	            </form>
	            <script>reloadReason(0,1,".$prisonerInfo[collected].");</script>
				";
			} else {				$realcol = $_REQUEST['realcol'];
				$need = $_REQUEST['term'];
                echo "<span style='color: green;'>Персонажу $userParam отказано в освобождении и обновлён срок [$need/$realcol].</span>";
				$query = "UPDATE `prison_chars` SET `collected` = '$realcol' WHERE `nick` = '".$user[login]."' LIMIT 1;";
				mysql_query($query, $link) or die(mysql_error());
				$query = "INSERT INTO `prison_actions_log` SET `date`='".time()."', `cop`='".AuthUserName."', `prisoner`='".mysql_escape_string($_REQUEST['del_udo'])."', `operation`='7'";
				mysql_query($query,$link) or die(mysql_error());
				send_sysmsg($user[login], $ttext, AuthUserName);

			}
		}
	}

	if (isset($_REQUEST['del_udo'])) {
		if(!$mega) die('<b>Нехватает доступа</b>');

		$user = locateUser(trim($_REQUEST['del_udo']));
		$user['login'] = trim($_REQUEST['del_udo']);
        $userParam = formatUser($user);

        $prisonerInfo = mysql_query("SELECT * FROM prison_chars WHERE nick='".$user[login]."' LIMIT 1",$link);
        $prisonerInfo = mysql_fetch_array($prisonerInfo);
        $prisonerInfo[need] = $prisonerInfo[term]-$prisonerInfo[collected];
        $prisonerInfo[money] = ceil($prisonerInfo[need]*1.5);

        if($prisonerInfo[want_udo] == 0) {
        	echo "<span style='color: red;'>Персонаж $userParam не подавал заявок, либо Вы её уже удалили.</span>";
        } else {
            $telegram = str_replace("\n\n","",$_REQUEST['telegram']);
            $telegram = str_replace("\n","\r",$telegram);
			$ttext = "";
			$ttext = ($telegram)?$telegram:$ttext;
			if(!isset($_REQUEST['ok'])) {
				echo "
				<form name='del_udo' method='post' action='/direct_call/prison_actions.php'>
				<table cellspacing=3 cellpadding=3 border=0 width=100% style='font-size: 12px;'>
					<tr>
						<th>Отмена заявки на УДО</th>
					</tr>
					<tr>
						<td>$userParam</td>
					</tr>
					<tr>
						<td>
						<select name='reason' onchange='reloadReason(this,0,".$prisonerInfo[collected].");'>
							<option value='0'>Шаблон: Обнаружена передача ресурсов</option>
							<option value='1'>Шаблон: Обнаружен факт марадёрства</option>
							<option value='2'>Шаблон: Ручной ввод</option>
						</select>

						</td>
					</tr>
					<tr>
						<td><textarea name='telegram' id='telegram' style='width: 100%; height: 150px;'>$ttext</textarea></td>
					</tr>
					<tr>
						<th><input type='submit' value='Подтвердить и отправить'><input type='button' value='Отмена' onclick=\"window.location.href='/direct_call/prison_actions.php'\"></th>
					</tr>
					</table>
					<input name='del_udo' type='hidden' value='".$user[login]."'>
					<input name='ok' type='hidden' value='1'>
	            </form>
	            <script>reloadReason(0,1,".$prisonerInfo[collected].");</script>
				";
			} else {				echo "<span style='color: green;'>Заявка на УДО от персонажа $userParam удалена.</span>";
				$query = 'UPDATE `prison_chars` SET `want_udo` = \'0\' WHERE `nick` = \''.$_REQUEST['del_udo'].'\' LIMIT 1;';
				mysql_query($query, $link) or die(mysql_error());
				$query = "INSERT INTO `prison_actions_log` SET `date`='".time()."', `cop`='".AuthUserName."', `prisoner`='".mysql_escape_string($_REQUEST['del_udo'])."', `operation`='3'";
				mysql_query($query,$link) or die(mysql_error());
				send_sysmsg($user[login], $ttext, AuthUserName);

			}
		}

	}
	if (isset($_REQUEST['confirm_udo'])) {
		if(!$mega) die('<b>Нехватает доступа</b>');
		$user = locateUser(trim($_REQUEST['confirm_udo']));
		$user['login'] = trim($_REQUEST['confirm_udo']);
        $userParam = formatUser($user);

        $prisonerInfo = mysql_query("SELECT * FROM prison_chars WHERE nick='".$user[login]."' LIMIT 1", $link);
        $prisonerInfo = mysql_fetch_array($prisonerInfo);
        $prisonerInfo[need] = $prisonerInfo[term]-$prisonerInfo[collected];
        $prisonerInfo[money] = ceil($prisonerInfo[need]*1.5);

        if($prisonerInfo[want_udo] == 2) {        	echo "<span style='color: red;'>Персонаж $userParam уже в списке ожидания оплаты УДО.</span>";
        } else {
            $telegram = str_replace("\n\n","",$_REQUEST['telegram']);
            $telegram = str_replace("\n","\r",$telegram);

			$ttext = "По итогам проверки отбытого срока вам засчитано «".$prisonerInfo[collected]."» ресурса(ов).\nДля освобождения Вы должны перевести «".$prisonerInfo[money]."» медных монет на персонажа Terminal 03.\nВ причинах перевода укажите, за кого осуществляется перевод. После перевода сообщите об этом сотруднику ОИН.";
			$ttext = ($telegram)?$telegram:$ttext;
			if(!isset($_REQUEST['ok'])) {				echo "
				<form name='confirm_udo' method='post' action='/direct_call/prison_actions.php'>
				<table cellspacing=3 cellpadding=3 border=0 width=100% style='font-size: 12px;'>
					<tr>
						<th>Подтверждение заявки на УДО</th>
					</tr>
					<tr>
						<td>$userParam</td>
					</tr>
					<tr>
						<td><textarea name='telegram' id='telegram' style='width: 100%; height: 150px;'>$ttext</textarea></td>
					</tr>
					<tr>
						<th><input type='submit' value='Подтвердить и отправить'><input type='button' value='Отмена' onclick=\"window.location.href='/direct_call/prison_actions.php'\"></th>
					</tr>
					</table>
					<input name='confirm_udo' type='hidden' value='".$user[login]."'>
					<input name='ok' type='hidden' value='1'>
	            </form>
				";
			} else {				$query = 'UPDATE `prison_chars` SET `want_udo` = \'2\' WHERE `nick` = \''.$_REQUEST['confirm_udo'].'\' LIMIT 1;';
				mysql_query($query, $link) or die(mysql_error());
				$query = "INSERT INTO `prison_actions_log` SET `date`='".time()."', `cop`='".AuthUserName."', `prisoner`='".mysql_escape_string($_REQUEST['confirm_udo'])."', `operation`='4'";
				mysql_query($query, $link) or die(mysql_error());				echo "<span style='color: green;'>Заявка на УДО от персонажа $userParam обработана, персонаж перемещен в список ожидания оплаты УДО.</span>";	            send_sysmsg($user[login], $ttext, AuthUserName);

			}
		}


	}
?>
    </td>
  </tr>
</table>
</body>
</html>
<?
}
mysql_close($link);
mysql_close($db);
?>