<h1>������ �� �������������� ��������� �������, ������������ ������ � �������� ��� ����������.</h1>
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
$filter = $filter - 2592000; // 30 �����
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
$filter = $filter - 2592000; // 30 �����
$query = "SELECT `id` FROM `or_data` WHERE `regtime` > '".$filter."' AND `silver` = '0'";
$rs = mysql_query($query);
$thirty_f = mysql_num_rows($rs);

$query = "SELECT `id` FROM `or_data` WHERE `regtime` > '".$filter."' AND `status` = '4' AND `silver` = '0'";
$rs = mysql_query($query);
$thirty_denied_f = mysql_num_rows($rs);

$filter = time();
$filter = $filter - 1209600; // 14 �����
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
				$err .= "��������� ���� ��� ��������� ��� ��������������! ������ �� �������������� �������� ��� ���� ���������� ��� ������ �� ����� ��������� <b>".$tmp['user']."</b>";
                $stop = 1;
			}
		$userinfo = GetInfoFromApi($r_nick);
        if ($userinfo["level"] > 0)
	        {
	            if ($userinfo["dismiss"] == '')
	                {
	                    $err .= "��������� �������� �� ������������! ���������� � ����������� ������� ��� ���������� ��������� � ����� ����������� ������ �� ������ � ��������� �������.<br><br>";
	                }
	            else
	                {
	                    $dismiss = $userinfo["dismiss"];
	                }
	        }
		if ($r_silver == 0 && ($r_regmail == '' || !preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $r_regmail)))
			{
            	$err .= "��������� ������������ ����� <b>���������������� e-mail</b>.<br><br>";
            }
		if ($r_newmail == '' || !preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $r_newmail))
			{
            	$err .= "��������� ������������ ����� <b>��������� e-mail</b>.<br><br>";
            }

        if ($r_nick == ''){$err .= "������� ��� ���������, ��� ������� �� ��������� ������������ ��������.<br><br>";}
        if ($r_last_visit == ''){$err .= "������� ����� ���������� ����� �� ������ ���������, ��� ������� �� ��������� ������������ ��������.<br><br>";}
        if ($r_ips == ''){$err .= "������� IP-����� (������) ���������, ��� ������� �� ��������� ������������ ��������.<br><br>";}
        if ($r_oldpass == '' && $r_silver == 0){$err .= "������� ������ ������ ���������, ��� ������� �� ��������� ������������ ��������.<br><br>";}
        if ($r_secretanswer == '' && $r_silver == 0){$err .= "������� ����� �� ��������� ������ ���������, ��� ������� �� ��������� ������������ ��������.<br><br>";}
        if ($r_from == ''){$err .= "��������� ������ �����������. ����������, ���������� ��������� ������ �����. ���� ������ �� �������� - ��������� � ����������� IT-������ �������.<br><br>";}
        if (strlen($err) < 3)
        	{
	            $query = "INSERT INTO `or_data` (`id`, `user`, `regtime`, `cop`, `cnumber`, `answer`, `status`, `pers`, `lastvisit`, `ips`, `old_email`, `new_email`, `old_psw`, `sec_answer`, `problem`, `userip`, `dismiss`, `silver`)
	            VALUES
	            ('', '".$r_from."', '".$r_time."', '', '', '', '1', '".$r_nick."', '".$r_last_visit."', '".$r_ips."', '".$r_regmail."', '".$r_newmail."', '".$r_oldpass."', '".$r_secretanswer."', '".$r_comment."', '".$userip."', '".$dismiss."', '".$r_silver."');";
                mysql_query($query) or die(mysql_error());
	            echo ("<b>�������. ���� ������ �������.</b>");
                $stop = 1;
            }
        else
        	{
				echo ("<b>��������, ��� ��������� ������ �������� ������:</b><br><br>");
                echo ($err);
            }
        }
        else
        {
				echo ("<b>��������, ��� ��������� ������ �������� ������:</b><br><br>");
                echo ("��������� ������ �����������. ����������, ���������� ��������� ������ �����. ���� ������ �� �������� - ��������� � ����������� IT-������ �������.");
        }
        }
        else
        {
				echo ("<b>��������, ��� ��������� ������ �������� ������:</b><br><br>");
                echo ("��������� ������ �����������. ����������, ���������� ��������� ������ �����. ���� ������ �� �������� - ��������� � ����������� IT-������ �������.");
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
			var pers_string = '������, �� �� ����� � ����. ��� ������ �����. ��� � �����... � ����� ���������� ��� �����.';
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

		document.getElementById('err_msgs').innerHTML = '��������� �Ĩ� �������� ���������. �� ���� ������ �� ������ ����� ���, ������� �� ����� ��� �� ����Ĩ�.';
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
								document.getElementById('err_msgs').innerHTML = '<hr size=1><b><font color="red" size="+1">��������! �������� �� ������������!</font></b><br>��� �������������� ������ ����������� ������ � ������� ������� ���������� � �������������� ������� ��� ���������� ���������. ����� ���������� ����������� ������ ������ �����.<hr size=1>';
                                document.getElementById('subm').style.display='none';
	                            timeout = setTimeout(loadNick, 10000);
							}
						else if (req.responseJS.res == 'nouser')
                        	{
								document.getElementById('err_msgs').innerHTML = '<hr size=1><b><font color="red" size="+1">��������! ��������� ���� ��� ��������� �� ������!</font></b><br>��������� � ������������ ����� ���� ���������. ���� ��� ������� ����� - ��������� ������� �����, ��������, ������ �� �� ��������.<hr size=1>';
                                document.getElementById('subm').style.display='none';
	                            timeout = setTimeout(loadNick, 10000);
							}
						else if (req.responseJS.res == 'processed')
                        	{
								document.getElementById('err_msgs').innerHTML = '<hr size=1><b><font color="red" size="+1">��������! ��������� ���� ��� ��������� ��� ��������������!</font></b><br>������ �� �������������� �������� ��� ��������� ���������� ��� ���� ������ �� ����� <b>'+ req.responseJS.usr +'</b><hr size=1>';
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
������ �� �������������� ��������� �������, ������������ ������ � �������� ��� ����������
<br>
���������� ����������� �� <b>Exposure passion</b><br>
�� ����� <a href='http://www.timezero.ru/cgi-bin/forum.pl?a=M2&c=141387856&m=1' target=_blank>http://www.timezero.ru/cgi-bin/forum.pl?a=M2&c=141387856&m=1</a>
</div>
<hr>
����������� ���������� � ���������� ����������� ���� ������. <b>���</b> �� ���� ������ ���� ���������!
<br>���� ��������� ������� ���������� ��� ������� �� ���� ����. ���� ����� ������ (� ������ ���������� �������) ���������� 1-2 ������. ����� ������ �������������� �������������� ��, � ����� � ��� ���� ����� ������ ����� ���� �������� ��� ��������������.
<br>������� ��������� ������ ������ �� �������������� �������� ��� �����������, ������������ � �����! ���� ��� �������� ������� � ��� �� ������������ - ���������� ���������� � ����������� ������� ��� ���������� ����������� ��������� � ����� ����������� ������.
<br><hr><center><b>��������!</b><br>� ��������� ������� ������� ����������� firewall ����� �������������� �����������.<br>���� ����������� �� ���������� - ���������� ��c������������ ��������� Internet Explorer.</center><hr>

<div id="err2" style="display:none" align="center">
<font color="red" size="+1"><b>������ �����������!</b></font><br><br>
��� ������ ������ ������� � ���� ����� ����������, ������� � ������ ������ ��������� ��� ����� ���������.<br>��������! ����������� � ������� ����������������� ������� ��� ������� ����������!<br><br>
<input type="button" value="��������� �������" onClick="javascript: location.reload(true)"><br><br>
</div>
<div align=center id='err_msgs'></div>
<form name="r_pers_req" method="post" action="">
	<input name="r_from" type="hidden" value="">
    <input name="r_from_sid" type="hidden" value="">
    <input name="r_from_city" type="hidden" value="">
    <input name="r_step" type="hidden" value="2">
  <table width="450" border="0" align="center" cellpadding="3" cellspacing="3">
      <tr><td COLSPAN=2 ALIGN=center><div id="whoshere">����������...</div></td></tr>
      <tr>
        <td>��� ����������� ���������</td>
        <td nowrap>
  <input name="r_nick" value="<?=@strip_tags(urldecode($_REQUEST['r_nick']))?>" style="width: 170px" type="text" id="r_nick" onBlur="loadNick()">
  <input type="button" value="���������" onclick="loadNick()" style="width: 79px">
  </td>
      </tr>
      <tr>
        <td>���� ���������� ��������� �� ����������� ������</td>
        <td><input name="r_last_visit" value="<?=strip_tags(urldecode($_REQUEST['r_last_visit']))?>" style="width: 250px" type="text" id="r_last_visit"></td>
      </tr>
      <tr>
        <td>������ IP �������, � ������� �� �������� ����� ���������� �� ������</td>
        <td><textarea name="r_ips" style="width: 250px" rows="5" wrap="VIRTUAL" id="r_ips"><?=strip_tags(urldecode($_REQUEST['r_ips']))?></textarea></td>
      </tr>
      <tr>
        <td>��������������� e-mail ���������</td>
        <td><input type="checkbox" id="nomail" name="nomail" value="1" onClick="sl('nomail');"> �� �����.<br>
        <div id="errnomail" style="display:none" align="center">
	    <font color="red"><b>��������!</b></font><br>
	    � ������, ���� �� �� ������� ��������������� e-mail ������ ���������, �� ������ ������������ �������� ��� ��� <u>������</u> �� ������������ ������. <a href="#comdetail">���������...</a>
	    </div>
        <input name="r_regmail" value="<?=strip_tags(urldecode($_REQUEST['r_regmail']))?>" style="width: 250px" type="text" id="r_regmail"></td>
      </tr>
      <tr>
        <td>����� e-mail</td>
        <td><input name="r_newmail" value="<?=strip_tags(urldecode($_REQUEST['r_newmail']))?>" style="width: 250px" type="text" id="r_newmail"></td>
      </tr>
      <tr>
        <td>������ ������ ���������</td>
        <td><input type="checkbox" id="nopass" name="nopass" value="1" onClick="sl('nopass');"> �� �����.<br>
        <div id="errnopass" style="display:none" align="center">
	    <font color="red"><b>��������!</b></font><br>
	    � ������, ���� �� �� ������� ������ ������ ������ ���������, �� ������ ������������ �������� ��� ��� <u>������</u> �� ������������ ������. <a href="#comdetail">���������...</a>
	    </div>
        <input name="r_oldpass" value="<?=strip_tags(urldecode($_REQUEST['r_oldpass']))?>" style="width: 250px" type="text" id="r_oldpass"></td>
      </tr>
      <tr>
        <td>����� �� ��������� ������ ��������� </td>
        <td><input type="checkbox" id="noanswer" name="noanswer" value="1" onClick="sl('noanswer');"> �� �����.<br>
        <div id="errnoanswer" style="display:none" align="center">
	    <font color="red"><b>��������!</b></font><br>
	    � ������, ���� �� �� ������� ����� �� ��������� ������ ������ ���������, �� ������ ������������ �������� ��� ��� <u>������</u> �� ������������ ������. <a href="#comdetail">���������...</a>
	    </div>
        <input name="r_secretanswer" value="<?=strip_tags(urldecode($_REQUEST['r_secretanswer']))?>" style="width: 250px" type="text" id="r_secretanswer"></td>
      </tr>
      <tr>
        <td>����������� (������� �������� �������������, ��� ������� ������� � �.�.) </td>
        <td><textarea name="r_comment" style="width: 250px" rows="5" wrap="VIRTUAL" id="r_comment"><?=strip_tags(urldecode($_REQUEST['r_comment']))?></textarea></td>
      </tr>
      <tr>
        <td colspan="2"><div align="center" id='subm' style="display:none">
          <input type="submit" name="sbm" id="sbm" value="���������">
        </div></td>
      </tr>
    </table>
    <br>
</form><hr>
<b>����������:</b>
<br><br>
����� ������� ������ (����������/������������): <b><?=$common_f?></b> / <b><?=$common?></b><br>
�� ��� �������� � �������������� (����������/������������): <b><?=$common_denied_f?></b> / <b><?=$common_denied?></b><br>
<br>
������� ������ �� ��������� 30 ���� (����������/������������): <b><?=$thirty_f?></b> / <b><?=$thirty?></b><br>
�� ��� �������� � �������������� (����������/������������): <b><?=$thirty_denied_f?></b> / <b><?=$thirty_denied?></b><br>
<br>
������� �������� � ������� (����������/������������): <b><?=$waiting_f?></b> / <b><?=$waiting?></b><br>
������� ������� ������������� (����������/������������): <b><?=$waiting2_f?></b> / <b><?=$waiting2?></b><br>
<?
//<br>
/*������� ����� ������������ ������: <b><?=gmdate("z ��., H �., i ���.",$mean)?></b>*/
?>
<br><hr>
<center><b>���� ������</b></center>
<div id='requests_list'></div>
<br><br><br>
<div id="macuta" style="display: none">
<a name="comdetail"></a>
�������������� �������� ��� ���������� ��� ������ ���� ��� ������ ������, ������������� � ����� ������, �������� ������ � �������������� ������� ������: <a href="http://www.timezero.ru/manual/sprice.ru.html" target="_blank">http://www.timezero.ru/manual/sprice.ru.html</a>.
</div>
<?
}
//<BR>http://www.timezero.ru/cgi-bin/authorization.pl?login='+pers_nick+'&ses='+pers_sid+'&city='+pers_city
?>