<h1>регистрация на сайте</h1>

<?php
if ($_REQUEST['doreg']) {

	if ($_REQUEST['sid'] == 'na') {
		echo 'Вы не вошли в игру своим персонажем';
	} else {
		
		$t_nick = trim($_REQUEST['r_nick']);
		$t_nick2 = urlencode($t_nick);
		$t_sid = $_REQUEST['r_sid'];
		$t_city = $_REQUEST['r_city'];
		if (strlen($t_sid) > 3) {

			$tmp_body = file_get_contents("https://www.timezero.ru/cgi-bin/authorization.pl?login=".$t_nick2."&ses=".$t_sid."&city=".$t_city);
			
			if (strpos($tmp_body, "OK")) {
				if(@$_REQUEST['r_nick'] && @$_REQUEST['PasswordRegister'] && @$_REQUEST['Password2Register']) {
					$UserRegister = $_REQUEST['r_nick'];
					$_UserRegister = trim(strtolower($UserRegister));
					
					if(in_array($_UserRegister, $DisabledRegisterNames)){
						echo '<div class=err>Пользователь "'.$UserRegister.'" уже зарегистрирован</div>';
						
					} elseif(!@$_REQUEST['email']) {
						echo '<div class=err>не указан e-mail</div>';
						
					} else {
						$UserEmail = $_REQUEST['email'];
						
						$UserEmail = trim(preg_replace("/[^\x20-\xFF]/","",@strval($UserEmail)));
						
						if (!preg_match("/^[a-z0-9_-]+(\.[a-z0-9_-]+)*@(([a-z0-9-]+\.)+(com|net|org|mil|edu|gov|arpa|info|biz|inc|name|[a-z]{2})|[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$/is",$UserEmail)) {
										
							echo '<div class=err>неправильный формат e-mail</div>';
							
						} else {
							
							if($_REQUEST['PasswordRegister']!=$_REQUEST['Password2Register'])
							echo '<div class=err>Пароль и подтверждение не совпадают</div>';
							
							else {
								$SQL='SELECT `id` FROM `site_users` WHERE `user_name`=\''.$_UserRegister.'\'';
								
								$r=mysql_query($SQL);
								if(mysql_num_rows($r)>0)
								echo '<div class=err>Пользователь "'.$UserRegister.'" уже зарегистрирован</div><div align=center>Если вы забыли свой пароль, то нажмите <a href="?act=user_remind">здесь</a></div>';
								else {
									$UserPass = $_REQUEST['PasswordRegister'];
									$UserPassMD5 = passhash($UserPass);
									
									$SQL = 'INSERT INTO `site_users` (`user_name`, `user_pass`, `user_email`) values (\''.$UserRegister.'\', \''.$UserPassMD5.'\', \''.$UserEmail.'\')';
									mysql_query($SQL);
									
									if(mysql_affected_rows()<1)
									echo '<div class=err>Не удалось зарегистрироваться. Сообщите администратору сайта.</div>';
									else
									echo '<div class=green>Вы удачно зарегистрировались.</div>';
									
								}
							}
						}
					}
				}
			}
		}
	}
}
?>

<script language="Javascript" type="text/javascript">
function tz_DoFSCommand(command, args) {
	var tmp = args.split("\t");
	if (command == "OK") {
		var pers_nick = '' + tmp[0];
		var pers_sid = '' + tmp[1];
		var pers_city = '' + tmp[2];
		var pers_level = '' + tmp[3];
		var pers_pro = '' + tmp[4];
		if (pers_pro == '') {pers_pro = '0';}
		var pers_clan = '' + tmp[5];
		if (pers_clan == '') {pers_clan = '0';}
		var pers_string = '<img src="/_imgs/clans/'+pers_clan+'.gif"><b>'+pers_nick+'</b> ['+pers_level+'] <img src="/_imgs/pro/i'+pers_pro+'.gif">';
		document.getElementById('whoshere').innerHTML=pers_string;
		document.getElementById('r_city').value=pers_city;
		document.getElementById('r_sid').value=pers_sid;
		document.getElementById('r_nick').value=pers_nick;
	} else {
		var pers_string = 'Похоже, вы не вошли в игру. Или сервер висит. Или я туплю... В общем попробуйте еще разок.';
		document.getElementById('whoshere').innerHTML=pers_string;
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
</script>
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="6" height="6" id="tz">
	<param name="movie" value="/authorization3.swf" />
	<param name="wmode" value="transparent" />
	<embed src="/authorization3.swf" wmode="transparent" width="6" height="6" swLiveConnect="true" id="tz" name="tz" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
	
<form name="view_pers" method="post">
<input type="hidden" name="doreg" value="1">
<input type="hidden" name="r_city" id="r_city" value="na">
<input type="hidden" name="r_sid" id="r_sid" value="na">
<input type="hidden" name="r_nick" id="r_nick" value="na">
<input name="act" type="hidden" value="register">
<div align=center>
<table>
<tr><td COLSPAN=2 ALIGN=center><div id="whoshere">Секундочку...</div></td></tr>
<tr><td>Ваш e-mail:</td><td>
<input name="email" type="text" value="<?=htmlspecialchars(@$_REQUEST['email'])?>">
</td></tr>
<tr><td>Желаемый пароль:<br><font size="-2">(не должен совпадать с паролем в ТЗ)</font></td><td>
<input name="PasswordRegister" type="password" value="<?=htmlspecialchars(@$_REQUEST['PasswordRegister'])?>">
</td></tr>
<tr><td>Повторите пароль:<br><font size="-2">(должен совпадать с первым паролем)</font></td><td>
<input name="Password2Register" type="password" value="<?=htmlspecialchars(@$_REQUEST['Password2Register'])?>">
</td></tr>
<tr><td colspan=2 align=center>
<input type="submit" value="Зарегистрироваться">
</td></tr>
</table>
</div>
</form>
<div align="left">
<br>
Если ваш ник не определился автоматически попробуйте выполнить следующую последовательность действий: <br><br>
1. Закройте клиент TimeZero.<br>
2. Запустите клиент TimeZero, зайдите в игру только одним персонажем, тем, от имени которого регистрируетесь на сайте.<br>
3. Скопируйте адрес "http://www.tzpolice.ru/?act=register" и вставьте в поле поиска клиента TZ (верхний-правый угол клиента).<br>
4. Нажмите кнопку поиска. На второй закладке клиента откроется страница регистрации на на нашем сайте, и в течении 5 секунд ваш ник определится.<br>
<br><br>
Если данная инструкция не решила проблемы при регистрации, обратитесь к сотрудникам полиции, они помогут вам зарегистрироваться.  

</div>