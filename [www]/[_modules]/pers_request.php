<h1>Заявки на восстановление утерянных паролей, электронного адреса и контроля над персонажем.</h1>
<?
//error_reporting(E_ALL);
$query = 'SELECT `id` FROM `or_data` WHERE `status` = 1 AND `silver` = 1';
$rs = mysql_query($query);
$waiting = mysql_num_rows($rs);

$query = "SELECT `id` FROM `or_data` WHERE `status` = '2' AND `silver` = '1'";
$rs = mysql_query($query);
$waiting2 = mysql_num_rows($rs);

$query = "SELECT `id` FROM `or_data` WHERE `silver` = '1'";
$rs = mysql_query($query);
$common = mysql_num_rows($rs);

$query = "SELECT `id` FROM `or_data` WHERE `status` = '4' AND `silver` = '1'";
$rs = mysql_query($query);
$common_denied = mysql_num_rows($rs);

$filter = time();
$filter = $filter - 2592000; // 30 суток
$query = "SELECT `id` FROM `or_data` WHERE `regtime` > '".$filter."' AND `silver` = '1'";
$rs = mysql_query($query);
$thirty = mysql_num_rows($rs);

$query = "SELECT `id` FROM `or_data` WHERE `regtime` > '".$filter."' AND `status` = '4' AND `silver` = '1'";
$rs = mysql_query($query);
$thirty_denied = mysql_num_rows($rs);

$query = "SELECT `id` FROM `or_data` WHERE `status` = '1' AND `silver` = '0'";
$rs = mysql_query($query);
$waiting_f = mysql_num_rows($rs);

$query = "SELECT `id` FROM `or_data` WHERE `status` = '2' AND `silver` = '0'";
$rs = mysql_query($query);
$waiting2_f = mysql_num_rows($rs);

$query = "SELECT `id` FROM `or_data` WHERE `silver` = '0'";
$rs = mysql_query($query);
$common_f = mysql_num_rows($rs);

$query = "SELECT `id` FROM `or_data` WHERE `status` = '4' AND `silver` = '0'";
$rs = mysql_query($query);
$common_denied_f = mysql_num_rows($rs);

$filter = time();
$filter = $filter - 2592000; // 30 суток
$query = "SELECT `id` FROM `or_data` WHERE `regtime` > '".$filter."' AND `silver` = '0'";
$rs = mysql_query($query);
$thirty_f = mysql_num_rows($rs);

$query = "SELECT `id` FROM `or_data` WHERE `regtime` > '".$filter."' AND `status` = '4' AND `silver` = '0'";
$rs = mysql_query($query);
$thirty_denied_f = mysql_num_rows($rs);

$filter = time();
$filter = $filter - 1209600; // 14 суток
$query = "SELECT `regtime`, `ans_time` FROM `or_data` WHERE `regtime` > '".$filter."' AND (`status` = '3' OR `status` = '4')";

$count = 0;
$mean = 0;
$rs = mysql_query($query);
while (list($rtime, $atime) = mysql_fetch_row($rs))
	{
		$count++;
        $mean = $mean + ($atime - $rtime);
    }
@$mean = floor($mean/$count);
if ($_REQUEST['r_step'] == 2 && $_REQUEST['r_from'])
	{
        $err = "";
        $t_nick = $_REQUEST['r_from'];
        $t_nick2 = str_replace(" ", "%20", $t_nick);
		$t_sid = $_REQUEST['r_from_sid'];
        $t_city = $_REQUEST['r_from_city'];
if (strlen($t_sid) > 3)
	{
    $userip = ipCheck();
	$sock = @fsockopen("www.timezero.ru", 80, $er1, $er2, 5);
    if(@$sock)
    	{
    		$addr = '/cgi-bin/authorization.pl?login='.$t_nick2.'&ses='.$t_sid.'&city='.$t_city;
			fputs($sock, 'GET '.$addr." HTTP/1.0\r\n");
			fputs($sock, "Host: www.timezero.ru \r\n");
			fputs($sock, "Content-type: application/x-www-url-encoded \r\n");
			fputs($sock, "\r\n\r\n");
			$tmp_headers = '';
	    	while ($str = trim(fgets($sock, 4096))) $tmp_headers .= $str."\n";
			$tmp_body = '';
			while (!feof($sock)) $tmp_body .= fgets($sock, 4096);
			$tmp_pos1 = strpos($tmp_body, 'about="');
        	if($tmp_pos1!==false)
            	{
			        $tmp_str1 = substr($tmp_body, 0, $tmp_pos1);
			        $tmp_str2 = substr($tmp_body, strpos($tmp_body, '"', $tmp_pos1+8));
			        $tmp_body = $tmp_str1.' '.$tmp_str2;
		        }
        }
        if (strpos($tmp_body, "OK"))
        	{
        $r_silver = 0;
        if ($_REQUEST['noanswer'] == "1") {$r_silver = 1;}
        if ($_REQUEST['nomail'] == "1") {$r_silver = 1;}
        if ($_REQUEST['nopass'] == "1") {$r_silver = 1;}
		$r_from = addslashes(strip_tags($_REQUEST['r_from']));
		$r_nick = addslashes(strip_tags($_REQUEST['r_nick']));
		$r_last_visit = addslashes(strip_tags($_REQUEST['r_last_visit']));
        $r_ips = addslashes(strip_tags($_REQUEST['r_ips']));
        $r_regmail = addslashes(strip_tags($_REQUEST['r_regmail']));
        $r_newmail = addslashes(strip_tags($_REQUEST['r_newmail']));
        $r_oldpass = addslashes(strip_tags($_REQUEST['r_oldpass']));
        $r_secretanswer = addslashes(strip_tags($_REQUEST['r_secretanswer']));
        $r_comment = addslashes(strip_tags($_REQUEST['r_comment']));
        $r_time = time();
		$query = "SELECT `user` FROM `or_data` WHERE `pers` = '".$r_nick."' AND `status` < '3'";
		$rs = mysql_query($query);
		if (mysql_num_rows($rs) > 0)
			{
	            $tmp = mysql_fetch_array($rs);
	            $_RESULT = array("usr" => $tmp['user']);
				$err .= "Введенный вами ник персонажа уже обрабатывается! Заявка на восстановление контроля над этим персонажем уже подана от имени персонажа <b>".$tmp['user']."</b>";
                $stop = 1;
			}
		$userinfo = GetInfoFromApi($r_nick);
        if ($userinfo["level"] > 0)
	        {
	            if ($userinfo["dismiss"] == '')
	                {
	                    $err .= "Указанный персонаж не заблокирован! Обратитесь к сотрудникам полиции для блокировки персонажа с целью минимизации ущерба от взлома и повторите попытку.<br><br>";
	                }
	            else
	                {
	                    $dismiss = $userinfo["dismiss"];
	                }
	        }
		if ($r_silver == 0 && ($r_regmail == '' || !preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $r_regmail)))
			{
            	$err .= "Проверьте правильность ввода <b>регистрационного e-mail</b>.<br><br>";
            }
		if ($r_newmail == '' || !preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $r_newmail))
			{
            	$err .= "Проверьте правильность ввода <b>желаемого e-mail</b>.<br><br>";
            }

        if ($r_nick == ''){$err .= "Укажите ник персонажа, над которым Вы пытаетесь восстановить контроль.<br><br>";}
        if ($r_last_visit == ''){$err .= "Укажите время последнего входа до взлома персонажа, над которым Вы пытаетесь восстановить контроль.<br><br>";}
        if ($r_ips == ''){$err .= "Укажите IP-адрес (адреса) персонажа, над которым Вы пытаетесь восстановить контроль.<br><br>";}
        if ($r_oldpass == '' && $r_silver == 0){$err .= "Укажите старый пароль персонажа, над которым Вы пытаетесь восстановить контроль.<br><br>";}
        if ($r_secretanswer == '' && $r_silver == 0){$err .= "Укажите ответ на секретный вопрос персонажа, над которым Вы пытаетесь восстановить контроль.<br><br>";}
        if ($r_from == ''){$err .= "Произошла ошибка авторизации. Пожалуйста, попробуйте отправить заявку снова. Если ошибка не исчезнет - свяжитесь с сотрудником IT-отдела полиции.<br><br>";}
        if (strlen($err) < 3)
        	{
	            $query = "INSERT INTO `or_data` (`id`, `user`, `regtime`, `cop`, `cnumber`, `answer`, `status`, `pers`, `lastvisit`, `ips`, `old_email`, `new_email`, `old_psw`, `sec_answer`, `problem`, `userip`, `dismiss`, `silver`)
	            VALUES
	            ('', '".$r_from."', '".$r_time."', '', '', '', '1', '".$r_nick."', '".$r_last_visit."', '".$r_ips."', '".$r_regmail."', '".$r_newmail."', '".$r_oldpass."', '".$r_secretanswer."', '".$r_comment."', '".$userip."', '".$dismiss."', '".$r_silver."');";
                mysql_query($query) or die(mysql_error());
	            echo ("<b>Спасибо. Ваша заявка принята.</b>");
                $stop = 1;
            }
        else
        	{
				echo ("<b>Извините, при обработке заявки возникли ошибки:</b><br><br>");
                echo ($err);
            }
        }
        else
        {
				echo ("<b>Извините, при обработке заявки возникли ошибки:</b><br><br>");
                echo ("Произошла ошибка авторизации. Пожалуйста, попробуйте отправить заявку снова. Если ошибка не исчезнет - свяжитесь с сотрудником IT-отдела полиции.");
        }
        }
        else
        {
				echo ("<b>Извините, при обработке заявки возникли ошибки:</b><br><br>");
                echo ("Произошла ошибка авторизации. Пожалуйста, попробуйте отправить заявку снова. Если ошибка не исчезнет - свяжитесь с сотрудником IT-отдела полиции.");
        }
    }
if ($stop !== 1)
	{
		/*
		<OBJECT id="tz" codeBase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" height="1" width="1" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><PARAM NAME="movie" VALUE="http://www.tzpolice.ru/_imgs/auth.swf"><PARAM NAME="wmode" VALUE="transparent">
<embed src="http://www.tzpolice.ru/_imgs/auth.swf" wmode="transparent" width="1" height="1" swLiveConnect="true" id="tz" name="tz" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</OBJECT>
*/
?>
<SCRIPT src='_modules/xhr_js.js'></SCRIPT>
<script language="JavaScript1.2">
<!--
var timeout = null;
function sl(n){
	var n2 = 'err'+n;
    if (document.getElementById(n2).style.display == 'none')
    	{
		    document.getElementById(n2).style.display = '';
            document.getElementById('macuta').style.display = '';
        }
    else
    	{
		    document.getElementById(n2).style.display = 'none';
        }
}
function tz_DoFSCommand(command, args) {
	var tmp = args.split("\t");
	if (command == "OK")
    	{
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
	        var req2 = new Subsys_JsHttpRequest_Js();
            var req3 = new Subsys_JsHttpRequest_Js();
	        req2.onreadystatechange = function()
	            {
	                if (req2.readyState == 4)
	                    {
                           if (req2.responseJS)
                           {
                            if (req2.responseJS.res == 'nousernamewasdetectedsonoactionshouldbemade')
                            	{
                                    document.getElementById('err2').style.display='';
	                        	}
                            else
                            	{
                                	document.getElementById('r_from').value=req2.responseJS.res;
                                    document.getElementById('r_from_sid').value=pers_sid;
                                    document.getElementById('r_from_city').value=pers_city;
                                    document.getElementById('subm').style.display='';
                                    document.getElementById('requests_list').innerHTML=req2.responseText;
                                }
                           }
	                    }
	            }
	        req2.caching = false;
	        req2.open('POST', '_modules/backends/pers_request_auth.php', true);
	        req2.send({ pn: pers_nick, ps: pers_sid, pc: pers_city });
        }
	else
    	{
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


function loadNick()
	{

		document.getElementById('err_msgs').innerHTML = 'ПОДОЖДИТЕ ИДЁТ ПРОВЕРКА ПЕРСОНАЖА. НЕ НАДО ТЫКАТЬ НА КНОПКУ МНОГО РАЗ, БЫСТРЕЕ ОТ ЭТОГО ОНА НЕ ПРОЙДЁТ.';
		document.getElementById('subm').style.display='none';
		var query = '' + document.getElementById('r_nick').value;
		var req = new Subsys_JsHttpRequest_Js();
		req.onreadystatechange = function()
        	{
				if (req.readyState == 4)
                	{
                        if (req.responseJS) {
                        if (req.responseJS.res == 'noblock')
                        	{
								document.getElementById('err_msgs').innerHTML = '<hr size=1><b><font color="red" size="+1">Внимание! Персонаж не заблокирован!</font></b><br>Для предотвращения тяжких последствий взлома в срочном порядке обратитесь к представителям полиции для блокировки персонажа. После блокировки попытайтесь подать заявку снова.<hr size=1>';
                                document.getElementById('subm').style.display='none';
	                            timeout = setTimeout(loadNick, 10000);
							}
						else if (req.responseJS.res == 'nouser')
                        	{
								document.getElementById('err_msgs').innerHTML = '<hr size=1><b><font color="red" size="+1">Внимание! Введенный вами ник персонажа не найден!</font></b><br>Убедитесь в правильности ввода ника персонажа. Если имя введено верно - повторите попытку позже, возможно, сервер ТЗ не отвечает.<hr size=1>';
                                document.getElementById('subm').style.display='none';
	                            timeout = setTimeout(loadNick, 10000);
							}
						else if (req.responseJS.res == 'processed')
                        	{
								document.getElementById('err_msgs').innerHTML = '<hr size=1><b><font color="red" size="+1">Внимание! Введенный вами ник персонажа уже обрабатывается!</font></b><br>Заявка на восстановление контроля над указанным персонажем уже была подана от имени <b>'+ req.responseJS.usr +'</b><hr size=1>';
                                document.getElementById('subm').style.display='none';
	                            timeout = setTimeout(loadNick, 10000);
							}
                        else
                        	{
								document.getElementById('err_msgs').innerHTML = '';
                                document.getElementById('subm').style.display='';
	                            if (timeout) clearTimeout(timeout);
                            }
                        }
					}
            }
		req.caching = false;
		req.open('POST', '_modules/backends/pers_request.php', true);
		req.send({ n: query });
	}
function clearErr()
	{
		document.getElementById('err').innerHTML = '';
	}
//-->
</script>
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="1" height="1" id="tz">
<param name="movie" value="authorization3.swf" />
<param name="wmode" value="transparent" />
<embed src="authorization3.swf" wmode="transparent" width="1" height="1" swLiveConnect="true" id="tz" name="tz" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
<div style='color: #800000; font-size: 20px; font-family: Verdana, Arial;' align=center>
Заявки на восстановление утерянных паролей, электронного адреса и контроля над персонажем
<br>
направлять телеграммой на <b>Exposure passion</b><br>
по форме <a href='http://www.timezero.ru/cgi-bin/forum.pl?a=M2&c=141387856&m=1' target=_blank>http://www.timezero.ru/cgi-bin/forum.pl?a=M2&c=141387856&m=1</a>
</div>
<hr>
Внимательно отнеситесь к заполнению приведенной ниже анкеты. <b>ВСЕ</b> ее поля должны быть заполнены!
<br>Срок обработки запроса составляет как правило до пяти дней. Срок смены данных (в случае правильных ответов) составляет 1-2 недели. Смена данных осуществляется Администрацией ТЗ, в связи с чем срок смены данных может быть увеличен без предупреждения.
<br>Полиция принимает только заявки на восстановление контроля над персонажами, находящимися в блоке! Если Ваш персонаж взломан и еще не заблокирован - немедленно обратитесь к сотрудникам полиции для блокировки взломанного персонажа с целью минимизации ущерба.
<br><hr><center><b>Внимание!</b><br>В отдельных случаях неверно настроенный firewall может препятствовать авторизации.<br>Если авторизация не происходит - попробуйте воcпользоваться браузером Internet Explorer.</center><hr>

<div id="err2" style="display:none" align="center">
<font color="red" size="+1"><b>Ошибка авторизации!</b></font><br><br>
Для подачи заявки войдите в игру любым персонажем, который в данный момент находится под Вашим контролем.<br>Внимание! Авторизация с помощью модифицированного клиента как правило невозможна!<br><br>
<input type="button" value="Повторить попытку" onClick="javascript: location.reload(true)"><br><br>
</div>
<div align=center id='err_msgs'></div>
<form name="r_pers_req" method="post" action="">
	<input name="r_from" type="hidden" value="">
    <input name="r_from_sid" type="hidden" value="">
    <input name="r_from_city" type="hidden" value="">
    <input name="r_step" type="hidden" value="2">
  <table width="450" border="0" align="center" cellpadding="3" cellspacing="3">
      <tr><td COLSPAN=2 ALIGN=center><div id="whoshere">Секундочку...</div></td></tr>
      <tr>
        <td>ник взломанного персонажа</td>
        <td nowrap>
  <input name="r_nick" value="<?=@strip_tags(urldecode($_REQUEST['r_nick']))?>" style="width: 170px" type="text" id="r_nick" onBlur="loadNick()">
  <input type="button" value="Проверить" onclick="loadNick()" style="width: 79px">
  </td>
      </tr>
      <tr>
        <td>дата последнего посещения до обнаружения взлома</td>
        <td><input name="r_last_visit" value="<?=strip_tags(urldecode($_REQUEST['r_last_visit']))?>" style="width: 250px" type="text" id="r_last_visit"></td>
      </tr>
      <tr>
        <td>список IP адресов, с которых вы заходили своим персонажем до взлома</td>
        <td><textarea name="r_ips" style="width: 250px" rows="5" wrap="VIRTUAL" id="r_ips"><?=strip_tags(urldecode($_REQUEST['r_ips']))?></textarea></td>
      </tr>
      <tr>
        <td>регистрационный e-mail персонажа</td>
        <td><input type="checkbox" id="nomail" name="nomail" value="1" onClick="sl('nomail');"> Не помню.<br>
        <div id="errnomail" style="display:none" align="center">
	    <font color="red"><b>Внимание!</b></font><br>
	    В случае, если Вы не помните регистрационный e-mail Вашего персонажа, Вы можете восстановить контроль над ним <u>ТОЛЬКО</u> на коммерческой основе. <a href="#comdetail">Подробнее...</a>
	    </div>
        <input name="r_regmail" value="<?=strip_tags(urldecode($_REQUEST['r_regmail']))?>" style="width: 250px" type="text" id="r_regmail"></td>
      </tr>
      <tr>
        <td>новый e-mail</td>
        <td><input name="r_newmail" value="<?=strip_tags(urldecode($_REQUEST['r_newmail']))?>" style="width: 250px" type="text" id="r_newmail"></td>
      </tr>
      <tr>
        <td>старый пароль персонажа</td>
        <td><input type="checkbox" id="nopass" name="nopass" value="1" onClick="sl('nopass');"> Не помню.<br>
        <div id="errnopass" style="display:none" align="center">
	    <font color="red"><b>Внимание!</b></font><br>
	    В случае, если Вы не помните старый пароль Вашего персонажа, Вы можете восстановить контроль над ним <u>ТОЛЬКО</u> на коммерческой основе. <a href="#comdetail">Подробнее...</a>
	    </div>
        <input name="r_oldpass" value="<?=strip_tags(urldecode($_REQUEST['r_oldpass']))?>" style="width: 250px" type="text" id="r_oldpass"></td>
      </tr>
      <tr>
        <td>ответ на секретный вопрос персонажа </td>
        <td><input type="checkbox" id="noanswer" name="noanswer" value="1" onClick="sl('noanswer');"> Не помню.<br>
        <div id="errnoanswer" style="display:none" align="center">
	    <font color="red"><b>Внимание!</b></font><br>
	    В случае, если Вы не помните ответ на секретный вопрос Вашего персонажа, Вы можете восстановить контроль над ним <u>ТОЛЬКО</u> на коммерческой основе. <a href="#comdetail">Подробнее...</a>
	    </div>
        <input name="r_secretanswer" value="<?=strip_tags(urldecode($_REQUEST['r_secretanswer']))?>" style="width: 250px" type="text" id="r_secretanswer"></td>
      </tr>
      <tr>
        <td>комментарий (краткое описание произошедшего, что желаете сменить и т.д.) </td>
        <td><textarea name="r_comment" style="width: 250px" rows="5" wrap="VIRTUAL" id="r_comment"><?=strip_tags(urldecode($_REQUEST['r_comment']))?></textarea></td>
      </tr>
      <tr>
        <td colspan="2"><div align="center" id='subm' style="display:none">
          <input type="submit" name="sbm" id="sbm" value="Отправить">
        </div></td>
      </tr>
    </table>
    <br>
</form><hr>
<b>Статистика:</b>
<br><br>
Всего принято заявок (бесплатных/коммерческих): <b><?=$common_f?></b> / <b><?=$common?></b><br>
Из них отказано в удовлетворении (бесплатных/коммерческих): <b><?=$common_denied_f?></b> / <b><?=$common_denied?></b><br>
<br>
Принято заявок за последние 30 дней (бесплатных/коммерческих): <b><?=$thirty_f?></b> / <b><?=$thirty?></b><br>
Из них отказано в удовлетворении (бесплатных/коммерческих): <b><?=$thirty_denied_f?></b> / <b><?=$thirty_denied?></b><br>
<br>
Ожидает проверки у полиции (бесплатных/коммерческих): <b><?=$waiting_f?></b> / <b><?=$waiting?></b><br>
Ожидает решения администрации (бесплатных/коммерческих): <b><?=$waiting2_f?></b> / <b><?=$waiting2?></b><br>
<?
//<br>
/*Среднее время рассмотрения заявки: <b><?=gmdate("z дн., H ч., i мин.",$mean)?></b>*/
?>
<br><hr>
<center><b>Ваши заявки</b></center>
<div id='requests_list'></div>
<br><br><br>
<div id="macuta" style="display: none">
<a name="comdetail"></a>
Восстановление контроля над персонажем без знания всех его личных данных, запрашиваемых в форме заявки, возможно только с использованием платной услуги: <a href="http://www.timezero.ru/manual/sprice.ru.html" target="_blank">http://www.timezero.ru/manual/sprice.ru.html</a>.
</div>
<?
}
//<BR>http://www.timezero.ru/cgi-bin/authorization.pl?login='+pers_nick+'&ses='+pers_sid+'&city='+pers_city
?>