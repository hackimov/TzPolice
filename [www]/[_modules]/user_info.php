<h1>Пользователи</h1>

<?php
$bgstr[0]="background='i/bgr-grid-sand.gif'";
$bgstr[1]="background='i/bgr-grid-sand1.gif'";


if(AuthStatus==1 && (abs(AccessLevel) & AccessUsers)) {
if($_REQUEST['doreg']) {
	if(@$_REQUEST['r_nick'] && @$_REQUEST['PasswordRegister'] && @$_REQUEST['Password2Register']) {
		$UserRegister = $_REQUEST['r_nick'];
		$_UserRegister = trim(strtolower($UserRegister));

		if(in_array($_UserRegister, $DisabledRegisterNames)){
			$errs .= '<div class=err>Ник "'.$UserRegister.'" запрещён к регистрации.</div>';
        } elseif(!@$_REQUEST['email']) {
			$errs .= '<div class=err>не указан e-mail</div>';
		} else {
			$UserEmail = $_REQUEST['email'];

			if($_REQUEST['PasswordRegister']!=$_REQUEST['Password2Register'])
				$errs .= '<div class=err>Пароль и подтверждение не совпадают</div>';
			else {
				$SQL='SELECT `id` FROM `site_users` WHERE `user_name`=\''.$_UserRegister.'\'';

				$r=mysql_query($SQL);
				if(mysql_num_rows($r)>0)
					$errs .= '<div class=err>Пользователь "'.$UserRegister.'" уже есть в бд</div></div>';
				else {
					$UserPass = $_REQUEST['PasswordRegister'];
					$UserPassMD5 = passhash($UserPass);
					$SQL = 'INSERT INTO `site_users` (`user_name`, `user_pass`, `user_email`) values (\''.$UserRegister.'\', \''.$UserPassMD5.'\', \''.$UserEmail.'\')';
					mysql_query($SQL);
					if(mysql_affected_rows()<1)
						$errs .= '<div class=err>Не удалось зарегистрироваться. Сообщите администратору сайта.</div>';
					else
						$errs .= '<div class=green>Вы удачно зарегистрировались.</div>';

				}
			}
		}
	} else {
		$errs .= '<div class=err>Заполнены не все обязательные поля</div>';
	}
}
?>

 <form name="view_pers" method="post" onsubmit="if(!confirm('Уверены в точности вводимых данных?\nИспользуйте эту функцию только если админские АПИ лежат и нужен аккаунт на сайте.\nПродолжить создание?')) return false;">
<input type="hidden" name="doreg" value="1">
<input name="act" type="hidden" value="user_info">

<div align=center>
<table style='width: 100%;'  cellspacing='3' cellpadding='5'>
<th height='20' background='i/bgr-grid-sand.gif' style='font-size: 12px;' colspan=5><p>
<strong>Регистрация пользователя</strong>
</p><? echo $errs; ?></th>
<tr>
<td align=center>
<input name="r_nick" type="text" style='width: 150px;' value="Логин" onfocus="clear_field(this)" onblur="check_field(this)">
</td>
<td align=center>
<input name="email" type="text" style='width: 150px;' value="@mail" onfocus="clear_field(this)" onblur="check_field(this)">
</td>
<td align=center>
<input name="PasswordRegister" type="text" style='width: 150px;'  value="Пароль" onfocus="clear_field(this)" onblur="check_field(this)">
</td>
<td align=center>
<input name="Password2Register" type="text" style='width: 150px;'  value="Повторите пароль" onfocus="clear_field(this)" onblur="check_field(this)">
</td>
<td align=center>
<input type="submit" style='color: #008040; width: 150px;' value="Создать персонажа" style='' >
</td>
</tr>
</table>
</div>
</form>

<br><br>
<table width='100%' border='0' cellspacing='3' cellpadding='5'>
<tr>

<th height='20' background='i/bgr-grid-sand.gif' style='font-size: 12px;'><p>
<strong>Принудительное обновление инфы через API</strong>
 </p></th>

 <th height='20' background='i/bgr-grid-sand.gif' style='font-size: 12px;'><p>
<strong>Поиск пользователя</strong>
 </p></th>
  <th height='20' background='i/bgr-grid-sand.gif' style='font-size: 12px;'><p>
<strong>Выборка пользователей</strong>
 </p></th>
</tr>
<tr><form method="GET">
<th>



<input name="act" type="hidden" value="user_info">

<input name="UpdateUserName" type="text" value="">

 <input type="submit" value="Обновить">





</th>
</form>
<form method="GET">
<th>


<input name="act" type="hidden" value="user_info">

<input name="SearchUserName" type="text" value="">

 <input type="submit" value="Поиск">

</form>

</th>
<th style='font-size: 12px;'>

<div class='verdana11'><a href="?act=user_info&select=coins">По количеству условных монет</a></div>

<div class='verdana11'><a href="?act=user_info&select=news">По количеству новостей</a></div>

<div class='verdana11'><a href="?act=user_info&select=comments">По количеству комментариев</a></div>

</th>

</tr>

</table>



<?
	if(strlen(@$_REQUEST['UpdateUserName'])>2) {
		$SearchUserName=$_REQUEST['UpdateUserName'];

		$uinfo = TZConn2($SearchUserName, 1);
		#print_r($uinfo);
		if($uinfo['login']) {
			
			// локатор
			mysql_query("UPDATE locator SET pro='".$uinfo['pro']."', clan='".$uinfo['clan']."',lvl='".$uinfo['level']."',utime='".time()."' WHERE login='".$uinfo['login']."'");
			// пользователи сайта
            mysql_query("UPDATE site_users SET clan='".$uinfo['clan']."' WHERE user_name='".$uinfo['login']."'");
			// старая база данных игроков
			tz_users_update($uinfo);

		}
		$_REQUEST['SearchUserName'] = $SearchUserName;
        echo "<center>Обновление (".$uinfo['clan'].") <b>$SearchUserName</b>[".$uinfo['level']."] <img src='http://timezero.ru/i/i".$uinfo['pro'].".gif'>успешно завершено.<br></center>";

	}

	$ShowInfo=0;
	if(@$_REQUEST['UserId']>0) {
		$UserId=$_REQUEST['UserId'];
		$SQL="SELECT a.*,b.clan,b.lvl,b.pro,b.pvprank
		FROM site_users AS a LEFT JOIN locator AS b ON(a.user_name=b.login)
		WHERE a.id='$UserId'";
	} else if(!$_REQUEST['UserId'] && strlen(@$_REQUEST['SearchUserName'])>2) {
		$SearchUserName=strtolower($_REQUEST['SearchUserName']);
		$SQL="SELECT a.*,b.clan,b.lvl,b.pro,b.pvprank
		FROM site_users AS a LEFT JOIN locator AS b ON(a.user_name=b.login) WHERE a.user_name='$SearchUserName'";
	}

	if(@$_REQUEST['UserId']>0 || strlen(@$_REQUEST['SearchUserName'])>2) {
		if(@$_REQUEST['SetClan']) {
			$SetClan=$_REQUEST['SetClan'];
			mysql_query("UPDATE site_users SET clan='$SetClan' WHERE id='$UserId'");
		}
		if(@$_REQUEST['AddCoins']) {
			$AddCoins=$_REQUEST['AddCoins'];
			mysql_query("UPDATE site_users SET coins=coins+'$AddCoins' WHERE id='$UserId'");
		}
		if(@$_REQUEST['ShutUp']) {
			$ShutUp=$_REQUEST['ShutUp']+time();
			#echo "shut to $ShutUp <= ".time()." <hr>";
			mysql_query("UPDATE site_users SET banned='$ShutUp' WHERE id='$UserId'");
		}
		if(@$_REQUEST['ShutDown']) {
			$ShutDown=time();
			mysql_query("UPDATE site_users SET banned='$ShutDown' WHERE id='$UserId'");
		}
		if(@$_REQUEST['rights'] && (abs(AccessLevel) & AccessUsersAdmin)) {
			$rights=$_REQUEST['rights'];
			mysql_query("UPDATE site_users SET user_group='$rights' WHERE id='$UserId'");
		}
		if (isset($_REQUEST['SetAccess']) && (abs(AccessLevel) & AccessUsersAdmin)) {
			list($max) = mysql_fetch_array(mysql_query("SELECT MAX(id) FROM AccessLevels"));
			$access = 0;
			for ($i=1; $i<=$max; $i++) $access += $_REQUEST['al'.$i];
			mysql_query("UPDATE site_users SET AccessLevel='$access' WHERE id='$UserId'");
		}
		if (isset($_REQUEST['restricted']) && (abs(AccessLevel) & AccessUsersAdmin)) {
			$access = $_REQUEST['restricted'];
			mysql_query("UPDATE site_users SET restricted_area='$access' WHERE id='$UserId'");
		}
		if(@$_REQUEST['do'] == "changepass" && (abs(AccessLevel) & AccessUsersAdmin)) {
	        $pass1 = $_REQUEST['pass1'];
			$pass2 = $_REQUEST['pass2'];
			if (($pass1 !== $pass2) && strlen($pass1)<6) {
				echo ("Введенные пароли слишком короткие или не совпадают");
			} else {
				$newpass = passhash($pass1);
				$query = "UPDATE `site_users` SET `user_pass` = '".$newpass."', `user_remind` = '', `user_email` = 'none@nowhere.com' WHERE `id` = '".$_REQUEST['uid']."' LIMIT 1;";
				//echo ($query);
				mysql_query($query) or die (mysql_error());
				echo ("<b>Пароль успешно изменен</b><br><br><b>private [".$_REQUEST['nick4priv']."] Ваш пароль и email на сайте полиции изменены. Новый пароль: ".$pass2.". Новый ящик: none@nowhere.com (для смены ящика укажите этот адрес в поле \"текущий\")</b>");
			}

		}

		$r=mysql_query($SQL);

		if(mysql_num_rows($r)<1) {
			echo $mess['UserNotFound'];
		} else {



			$d=mysql_fetch_array($r);

			$clan=GetClan($d['clan']);

			$nick=GetUser($d['id'],$d['user_name'],AuthUserGroup);
			$nick2 = $d['user_name'];

?>



<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>

<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Пользователь: <?=$clan." ".$nick?>  </strong> </p></td>

</tr><tr><td>



<form method="GET">

<input name="UserId" type="hidden" value="<?=$d['id']?>">

<input name="act" type="hidden" value="user_info">

Принадлежность к клану: <select size="1" name="SetClan">

	<option value="no">--- нет ---</option>



<?

$clan_dir="_imgs/clans/";

$dd=opendir($clan_dir);

$counter = 0;

while(($CurFile=readdir($dd))!==false) if(is_file("$clan_dir/$CurFile")) {

	$FileType=GetImageSize("$clan_dir/$CurFile");

	$CurFile=explode(".",$CurFile);

	if($FileType[2]==1)

    	{

			$clans[$counter] = $CurFile[0];

            $counter++;

        }

}

sort($clans, SORT_STRING);

reset($clans);

while (list($key, $val) = each($clans))

	{
		echo "	<option value=\"{$val}\"";

		if($d['clan']=="$val") echo " selected";

		echo ">{$val}</option>";
	}



closedir($dd);

?>



</select>

<br><br>

Перечислить на счет персонажа <input name="AddCoins" type="text" value="0" size=2> монет (сейчас у персонажа: <b><?=$d['coins']?></b> мнт.)

<br><br>



<?


if($d['banned']>time()) {

	$MolDiff=$d['banned']-time();

	echo "Будет молчать еще <b>".gmdate("zд Hч iм sс",$MolDiff)."</b> &nbsp; <input name='ShutDown' type='checkbox' value=1> снять молчанку";

} else {

?>

Наложить сайтовую молчанку на

<select size="1" name="ShutUp" selected>

	<option value="0">-- выберите --</option>

	<option value="1800">30 мин</option>

	<option value="3600">1 час</option>

	<option value="10800">2 часа</option>

	<option value="21600">6 часов</option>

	<option value="43200">12 часов</option>

	<option value="86400">1 сутки</option>

	<option value="172800">2 суток</option>
    <option value="432000">5 суток</option>
    <option value="604800">7 суток</option>
    <option value="864000">10 суток</option>
    <option value="1728000">20 суток</option>
    <option value="2592000">30 суток</option>


</select>



<?

?>
<?

}

?>



<br><br>

<input type="submit" value="Обновить">

</form>



<p>

IP-адреса, с которых заходил пользователь: <b><?=$d['ips']?></b>

<br>

Время последнего захода: <b><?=date("d.m.Y H:i",$d['last_visit'])?></b>

<br>

Последняя молчанка:

<b><?=date("d.m.Y H:i",$d['banned'])?></b>

<br><br>

<b>Опубликованные новости (последние 5):</b>

<?

$res=mysql_query("SELECT id, news_title FROM news WHERE poster_id='".$d['id']."' ORDER BY news_date DESC LIMIT 5");

if(mysql_num_rows($res)<1) echo "<i>нет</i>";

else {

	while($dt=mysql_fetch_array($res)) echo "<br> &nbsp;&nbsp;<b>&raquo; </b><a href='?act=news_comments&IdNews=".$dt['id']."'>".$dt['news_title']."</a>";

}

?>



<?if(abs(AccessLevel) & AccessUsersAdmin) {?>

<tr><td >

<form method="GET">

<input name="UserId" type="hidden" value="<?=$d['id']?>">

<input name="act" type="hidden" value="user_info">

Уровень доступа пользователя (группа): <input name="rights" type="text" value="<?=$d['user_group']?>" size=2>

<input type="submit" value="Задать">

</form>

<form method="GET" onsubmit="if(!confirm('Внимание! Только для знающих, если не уверены как прописывать права, лучше не трогайте. Жмите `отмена`.')) return false;">

<input name="UserId" type="hidden" value="<?=$d['id']?>">

<input name="act" type="hidden" value="user_info">

Дополнительные права:  <input name="restricted" type="text" size="25" value="<?=$d['restricted_area']?>" >

<input type="submit" value="Задать" >

</form>





<form method="POST" action="?act=user_info">

<input name="UserId" type="hidden" value="<?=$d['id']?>">

<table>

<tr><td width=350 colspan=2 height='20' bgcolor=#F4ECD4><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong> Права пользователя: </strong></td></tr>



<?

$SQL = "SELECT id, AccessName, Name, AccessLevel FROM AccessLevels";

$a = mysql_query($SQL);

$np = 0;

while (list($id, $accname, $name, $level) = mysql_fetch_array($a)) {

if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

//if ($accname !== "AccessOPQueue" && $accname !== "AccessOP") // убираем из списка доступы по прокачкам
//{

?>

<tr><td <?=$bg?>><?=$name?></td><td  <?=$bg?>><input type=checkbox name=al<?=$id?> value=<?=$level.(abs($d['AccessLevel']) & abs($level)?' checked':'')?>></td></tr>

<?
//}
}

?>



<tr><td colspan=2 align=center><input type=submit name=SetAccess value="Установить"></td></tr>

</table>

</form>

<!--<a href="#;return false;" onclick="if(confirm('Удалить пользователя?')) top.location='?act=user_info&UserId=<?=$d['id']?>&delete=1'">Удалить пользователя</a>-->
<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>

<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Сменить пароль:  </strong> </p></td>

</tr><tr><td>

<form name="newpass" method="POST" action="?act=user_info&UserId=<?=$d['id']?>">
Новый пароль:<br>
<input type="password" name="pass1">
<br><br>
Новый пароль повторно:<br>
<input type="password" name="pass2">
<br><br>
<input type="submit" value="Изменить">
<input type="hidden" name="uid" value="<?=$d['id']?>">
<input type="hidden" name="nick4priv" value="<?=$nick2?>">
<input type="hidden" name="do" value="changepass">
<br><br>
<font size="-2">Минимальная длина пароля 6 символов.<br>
При смене пароля меняется и почтовый ящик учетной записи.<br>
Новый ящик - <b>none@nowhere.com</b> (сообщите об этом пользователю, т.к. для смены ящика ему придется указать старый)</font>
</form>
</td></tr>
</table>
</td></tr>

<?}?>



</td></tr>

</table>



<?

		}

}



if(strlen($_REQUEST['select'])>2) {



	switch($_REQUEST['select']) {

		case 'coins': $SQL="SELECT id,user_name,clan, coins AS val FROM site_users WHERE coins>0 ORDER BY coins DESC"; $StrHdr="Выборка по кол-ву монет"; break;

		case 'news': $SQL="SELECT u.id,u.user_name,u.clan,count(n.id) AS val FROM news n LEFT JOIN site_users u ON n.poster_id=u.id GROUP BY u.id ORDER BY val DESC"; $StrHdr="Выборка по кол-ву опубликованных новостей"; break;

        case 'comments': $SQL="SELECT u.id,u.user_name,u.clan,count(c.id) AS val FROM comments c LEFT JOIN site_users u ON c.id_user=u.id GROUP BY u.id ORDER BY val DESC"; $StrHdr="Выборка по кол-ву комментариев"; break;

	}



	$r=mysql_query($SQL);

    if(mysql_num_rows($r)<1) echo "<h2>Пользователи, соответствующие запросу, не найдены</h2>";

    else {



    	echo "<h2>$StrHdr</h2>";

    	echo "<div align=center><table width=300>";

        while($d=mysql_fetch_array($r)) {

			echo "

            	<tr>

                <td nowrap>".GetClan($d['clan']).GetUser($d['id'],$d['user_name'],AuthUserGroup)."</td>

                <td nowrap><b>".$d['val']."</b></td>

                </tr>

            ";

        }

        echo "</table></div>";



    }



}


} else {

	echo $mess['AccessDenied'];

}

?>