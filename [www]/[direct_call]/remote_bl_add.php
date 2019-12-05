<html>
<head>
  <title>.::Черный список::.</title>
  <LINK href="http://www.tzpolice.ru/_modules/tzpol_css.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="100%"  border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td>
<?php
error_reporting(0);
require("../_modules/auth.php");

error_reporting(0);
require("../_modules/functions.php");
include "../_modules/mysql.php";
error_reporting(0);
$username = $_REQUEST['r_from'];
$sid = $_REQUEST['r_from_sid'];
$city = $_REQUEST['r_from_city'];
$cur_date = time();

if (strlen($sid) > 3 && isset($_REQUEST['s']))
	{

	$sock = @fsockopen("www.timezero.ru", 80, $er1, $er2, 5);
    if(@$sock)
    	{
    		$addr = "/cgi-bin/authorization.pl?login=".urlencode($username)."&ses=".$sid."&city=".$city;
			fputs($sock, "GET ".$addr." HTTP/1.0\r\n");
			fputs($sock, "Host: www.timezero.ru \r\n");
			fputs($sock, "Content-type: application/x-www-url-encoded \r\n");
			fputs($sock, "\r\n\r\n");
			$tmp_headers = "";
	    	while ($str = trim(fgets($sock, 4096))) $tmp_headers .= $str."\n";
			$tmp_body = "";
			while (!feof($sock)) $tmp_body .= fgets($sock, 4096);
			$tmp_pos1 = strpos($tmp_body, "about=\"");
        	if($tmp_pos1!==false)
            	{
			        $tmp_str1 = substr($tmp_body, 0, $tmp_pos1);
			        $tmp_str2 = substr($tmp_body, strpos($tmp_body, "\"", $tmp_pos1+8));
			        $tmp_body = $tmp_str1." ".$tmp_str2;
		        }
        }
        if (strpos($tmp_body, "OK"))
        	{
				$query = "SELECT `clan` FROM `site_users` WHERE `user_name` = '".$username."' LIMIT 1;";
                $res = mysql_query($query) or die (mysql_error());
                $d = mysql_fetch_array($res);
                if ($d['clan'] == 'police' || $d['clan'] == 'Military Police' || $d['clan'] == 'Police Academy' || $d['clan'] == 'Financial Academy')
                	{
	                    if(@$_REQUEST['s']) $section = $_REQUEST['s'];

	                    if ($section == "add" && @$_REQUEST['nick'])
	                        {
	                            $new_nick = $_REQUEST['nick'];
	                            $query = "SELECT `reason`, `nick` FROM `police_b_list` WHERE `nick` = '".$new_nick."' LIMIT 1;";
	                            $res = mysql_query($query) or die (mysql_error());
	                            if (mysql_num_rows($res) == 0)
	                                {
	                                    $userinfo = GetUserInfo($new_nick);
	                                    if ($userinfo['login'] !== "" && $userinfo["error"] !== "NOT_CONNECTED" && $userinfo["error"] !== "USER_NOT_FOUND" && $userinfo["error"] !== "ERROR_IN_USER_NAME" && $userinfo["level"] > 0) // && $userinfo["login"] == $new_nick
	                                        {
	                                            $query = "INSERT INTO `police_b_list` (`nick`, `level`, `pro`, `clan`, `reason`, `term`, `status`, `last_updated`, `payment`, `payment_till`, `no_payment_till`, `deleted_by`) VALUES ('".$userinfo['login']."', '".$userinfo['level']."', '".$userinfo['pro']."', '".$userinfo['clan']."', '', '', '0', '".$cur_date."', '', '', '', '' );";
	                                            mysql_query($query) or die("Ошибка обработки запроса. Попробуйте повторить попытку позже");
	                                            mysql_query("INSERT INTO `black_list` (`date`, `city`, `nick`, `level`, `pro`, `clan`, `status`) VALUES (NOW(), 0, '".$userinfo['login']."', '".$userinfo['level']."', '".$userinfo['pro']."', '".$userinfo['clan']."', 0);");
	                                            $nick = $userinfo['login'];
	                                        }
	                                    else
	                                        {
	                                             echo ("<pre>");
	                                             print_r($userinfo);
	                                             echo ("</pre>");
	                                             die("Ошибка обработки запроса. Попробуйте повторить попытку позже");
	                                        }
	                                }
	                            else
	                                {
	                                    list($reason, $nick) = mysql_fetch_row($res);
	                                    $userinfo = GetUserInfo($nick);
	                                    if ($userinfo["error"] !== "NOT_CONNECTED" && $userinfo["error"] !== "USER_NOT_FOUND" && $userinfo["error"] !== "ERROR_IN_USER_NAME") // && $userinfo["login"] == $new_nick
	                                        {
	                                            $query = "UPDATE `police_b_list` SET `level` = '".$userinfo['level']."', `pro` = '".$userinfo['pro']."', `clan` = '".$userinfo['clan']."', `last_updated` = '".$cur_date."', `status` = '0' WHERE `nick` = '".$nick."' LIMIT 1;";
	                                            mysql_query($query) or die("Ошибка обработки запроса. Попробуйте повторить попытку позже");
	                                            mysql_query("INSERT INTO `black_list` (`date`, `city`, `nick`, `level`, `pro`, `clan`, `status`) VALUES (NOW(), 0, '".$nick."', '".$userinfo['level']."', '".$userinfo['pro']."', '".$userinfo['clan']."', 0);");
	                                        }
	                                    else
	                                        {
	                                             die("Ошибка обработки запроса. Попробуйте повторить попытку позже");
	                                        }
	                                    $reason = $reason."|||";
	                                    $rsn = explode("|||", $reason);
	                                    $reason = $rsn[0];
	                                    $reason = str_replace("<br>", "\r\n", $reason);
	                                }

	                                echo ("<img src='http://www.tzpolice.ru/_imgs/clans/".$userinfo['clan'].".gif'> ".$userinfo['login']." [".$userinfo['level']."] <img src='../_imgs/pro/i".$userinfo['pro'].".gif'><br>");
	                            ?>
<OBJECT id="tz" codeBase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" height="1" width="1" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><PARAM NAME="movie" VALUE="http://tzpolice.ru/_imgs/auth.swf"><PARAM NAME="wmode" VALUE="transparent">
<embed src="http://tzpolice.ru/_imgs/auth.swf" wmode="transparent" width="1" height="1" swLiveConnect="true" id="tz" name="tz" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</OBJECT>
<script language="JavaScript1.2">
<!--
function tz_DoFSCommand(command, args) {
	var tmp = args.split("\t");
	if (command == "OK")
    	{
            cop = tmp[0];
			cop_sid = tmp[1];
			cop_city = tmp[2];
            document.getElementById('r_from').value = '' + tmp[0];
			document.getElementById('r_from_sid').value = '' + tmp[1];
			document.getElementById('r_from_city').value = '' + tmp[2];
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
	                            <form name="add" method="post" action="?section=do_add">
	                               <input name="s" type="hidden" value="do_add">
	                               <input name="nick" type="hidden" value="<?=$nick?>">
                                   <input name="r_from" type="hidden" value="">
	                               <input name="r_from_sid" type="hidden" value="">
	                               <input name="r_from_city" type="hidden" value="">
	                      Срок (дней):
	                      <input type="text" name="term" size="5" value="0"> (0 дней - бессрочно)
	                      <br>
	                      Примечание:<br>
	                      <textarea name="reason" cols="20" rows="7" wrap="VIRTUAL"><?=$reason?></textarea><br>
	                        <input type="submit" name="Submit" value="Добавить">
	                    </form>
	                            <?
	                        }
	                    //Adding char
	                    elseif ($section == "do_add" && @$_REQUEST['nick'] && @$_REQUEST['reason'])
	                        {
	                            $u_term = $_REQUEST['term'];
	                            if ($u_term > 0)
	                                {
	                                    $term = ($u_term * 60 * 60 * 24);
	                                    $u_term = $term + $cur_date;
	                                }
	                            else
	                                {
	                                    $u_term = 9999999999;
	                                }
	                            $u_nick = $_REQUEST['nick'];
	                            $add_ts = time();
	                            $u_reason = $_REQUEST['reason']."|||".$username;
	                            $u_reason = str_replace("\r\n", "<br>", $u_reason);
	                            $u_updated = time();
	                            $query = "UPDATE `police_b_list` SET `added` = '".$add_ts."', `reason` = '".$u_reason."', `term` = '".$u_term."', `status` = '0', `payment` = '', `no_payment_till` = '0', `payment_till` = '0' WHERE `nick` = '".$u_nick."' LIMIT 1;";
	                            mysql_query($query) or die(mysql_error());
	                            ?>
	                              Персонаж <b><?=$u_nick?></b> успешно добавлен в черный список
	                            <?
	                        }
	                       else
	                        {
	                            ?>
<OBJECT id="tz" codeBase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" height="1" width="1" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><PARAM NAME="movie" VALUE="http://tzpolice.ru/_imgs/auth.swf"><PARAM NAME="wmode" VALUE="transparent">
<embed src="http://tzpolice.ru/_imgs/auth.swf" wmode="transparent" width="1" height="1" swLiveConnect="true" id="tz" name="tz" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</OBJECT>
<script language="JavaScript1.2">
<!--
function tz_DoFSCommand(command, args) {
	var tmp = args.split("\t");
	if (command == "OK")
    	{
            cop = tmp[0];
			cop_sid = tmp[1];
			cop_city = tmp[2];
            document.getElementById('r_from').value = '' + tmp[0];
			document.getElementById('r_from_sid').value = '' + tmp[1];
			document.getElementById('r_from_city').value = '' + tmp[2];
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
                      <center><b>Внимание!</b><br><br>Вносить кого-либо в ЧС полиции могут только сотрудники БО!!!</center>
	                           <form name="add" method="post" action="">
	                               <input name="s" type="hidden" value="add">
	                               <input name="nick" type="text" value="Ник">
                                   <input name="r_from" type="hidden" value="">
	                               <input name="r_from_sid" type="hidden" value="">
	                               <input name="r_from_city" type="hidden" value="">
	                            <input type="submit" name="Submit" value="Добавить">
	                           </form>
	                            <?
	                        }
                    }
            }
            else
            {
            echo ("TZ AUTH FAILED    ".$tmp_body);
            }
}
else
{
?>
<OBJECT id="tz" codeBase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" height="1" width="1" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><PARAM NAME="movie" VALUE="http://tzpolice.ru/_imgs/auth.swf"><PARAM NAME="wmode" VALUE="transparent">
<embed src="http://tzpolice.ru/_imgs/auth.swf" wmode="transparent" width="1" height="1" swLiveConnect="true" id="tz" name="tz" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</OBJECT>
<script language="JavaScript1.2">
<!--
function tz_DoFSCommand(command, args) {
	var tmp = args.split("\t");
	if (command == "OK")
    	{
            cop = tmp[0];
			cop_sid = tmp[1];
			cop_city = tmp[2];
            document.getElementById('r_from').value = '' + tmp[0];
			document.getElementById('r_from_sid').value = '' + tmp[1];
			document.getElementById('r_from_city').value = '' + tmp[2];
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
                      <center><b>Внимание!</b><br><br>Вносить кого-либо в ЧС полиции могут только сотрудники БО!!!</center>
	                           <form name="add" method="post" action="">
	                               <input name="s" type="hidden" value="add">
	                               <input name="nick" type="text" value="Ник">
                                   <input name="r_from" type="hidden" value="">
	                               <input name="r_from_sid" type="hidden" value="">
	                               <input name="r_from_city" type="hidden" value="">
	                            <input type="submit" name="Submit" value="Добавить">
	                           </form>

<?
}
?>
</td>
  </tr>
</table>
</body>

</html>