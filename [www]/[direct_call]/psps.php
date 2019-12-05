<?
require("../_modules/functions.php");
require("../_modules/auth.php");
$need_ref = "http://".$_SERVER[HTTP_HOST]."/direct_call/psps.php";
$cur_ref = $_SERVER[HTTP_REFERER];
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
$nick2 = str_replace(" ", "%20", $nick);
//$nick = "Bac%20La%20Jane";
if(@$_REQUEST['sid']) $sid = $_REQUEST['sid'];
//$sid = "21113094091123724003";
if(@$_REQUEST['city']) $city = $_REQUEST['city'];
//$city = "city1.timezero.ru";
if(@$_REQUEST['level']) $level = $_REQUEST['level'];
if(@$_REQUEST['pro']) $pro = $_REQUEST['pro'];
if ($pro < 1) {$pro = "0";}
if(@$_REQUEST['clan']) $clan = $_REQUEST['clan'];
echo ("nick: ".$nick."<br>");
echo ("sid: ".$sid."<br>");
echo ("city: ".$city."<br><hr>");
if ($level > 0)
	{
        if (strlen($clan) > 1)
        	{
				$show_nick = "<img src='../_imgs/clans/".$clan.".gif' border='0'><b>".$nick."</b> [".$level."]<img src='../_imgs/pro/i".$pro.".gif' border='0' style='vertical-align:text-bottom'>";
			}
        else
        	{
		        $show_nick = "<b>".$nick."</b> [".$level."]<img src='../_imgs/pro/i".$pro.".gif' border='0' style='vertical-align:text-bottom'>";
            }
    }
else
	{
        $show_nick = "<b>".$nick."</b>";
    }
if (strlen($sid) > 3 && $need_ref == $cur_ref)
	{
    echo ("Requesting server... <br>");
	$sock = @fsockopen("www.timezero.ru", 80, $er1, $er2, 5);
    if(@$sock)
    	{
       	    echo ("Opened socket... <br>");
    		$addr = "/cgi-bin/authorization.pl?login=".$nick2."&ses=".$sid."&city=".$city;
			fputs($sock, "GET ".$addr." HTTP/1.0\r\n");
			fputs($sock, "Host: www.timezero.ru \r\n");
			fputs($sock, "Content-type: application/x-www-url-encoded \r\n");
			fputs($sock, "\r\n\r\n");
			$tmp_headers = "";
	    	while ($str = trim(fgets($sock, 4096))) $tmp_headers .= $str."\n";
			$tmp_body = "";
			while (!feof($sock)) $tmp_body .= fgets($sock, 4096);
            echo ("Received data... <br>");
			echo ("Data string: ".htmlspecialchars($tmp_body)."<br>");
            echo ("<script>alert('".$tmp_body."');</script>");
/*            $tmp_pos1 = strpos($tmp_body, "about=\"");
        	if($tmp_pos1!==false)
            	{
                	echo ("Warning: tmp_pos1!==false <br>");
			        $tmp_str1 = substr($tmp_body, 0, $tmp_pos1);
			        $tmp_str2 = substr($tmp_body, strpos($tmp_body, "\"", $tmp_pos1+8));
			        $tmp_body = $tmp_str1." ".$tmp_str2;
		        }
*/
        }
        if (strpos($tmp_body, "OK"))
        	{
                echo ("Rezult is OK... <br>");
                $query = "SELECT `l_date` FROM `prison_logs` ORDER BY `id` DESC LIMIT 1;";
	            $rs = mysql_query($query) or die (mysql_error());
	            list($last_date) = mysql_fetch_row($rs);
	            ?>
	            <div align="left"><font size="-2">Показаны данные по состоянию на <b><?=date("d.m.Y, H:i", $last_date)?></b></font><br><br></div>
                <?
	            $query = "SELECT * FROM `prison_chars` WHERE `nick` = '".$nick."' LIMIT 1;";
	            $rez = mysql_query($query) or die(mysql_error);
                if (mysql_num_rows($rez) > 0)
                	{
	                    list($c_id, $c_nick, $c_term, $c_coll, $c_l_pay, $c_reas, $c_rem, $c_dept) = mysql_fetch_row($rez);
	                    echo ("<div align='center'>Статистика по персонажу ".$show_nick."<br><br></div>");
	                    if ($c_reas > 0)
	                        {
	                            echo ("Причина отправки на каторгу: <b>".$crime[$c_reas]."</b><br>");
	                            echo ("Срок: <b>".$c_term."</b> ресурсов<br>");
	                            echo ("Зачтено: <b>".$c_coll."</b> ресурсов<br>");
	                            echo ("Дата последней сдачи ресурсов: <b>".date("d.m.Y, H:i", $c_l_pay)."</b>");
	                        }
                        else
                        	{
                            	echo ("Для указанного персонажа не определен срок заключения. Обратитесь к офицеру, ответственному за работу с каторгой, для исправления этой ошибки.");
                            }
                    }
                else
                	{
                    	echo ("<div align='center'>Персонаж <b>".$show_nick."</b> не найден в списке заключенных.</div><br><br>Возможные причины: <ul><li>Персонаж еще не сдал ни одного ресурса<li>Персонаж попал на каторгу и сдавал ресурсы после последнего обновления статистики<li>Персонаж не был отправлен на каторгу =)</ul>");
                    }
            }
        else
        	{
?>
<div align="center">
<font color="red" size="+2"><?=$tmp_body?></font>
<b>Ошибка авторизации!</b><br><br>
ФТАРОЙ ЭТАП<br><br>
Войдите в игру персонажем с использованием версии игры <b>не ниже 3.21</b><br>Внимание! Авторизация с помощью модифицированного клиента как правило невозможна!<br><br>.<br><br>
<input type="button" value="Повторить попытку" onClick="javascript: location.reload(true)">
</div>
<?
            }
	}
else
	{
?>
<script language="Javascript" type="text/javascript">
<!--
function tz_DoFSCommand(command, args) {
	var tmp = args.split("\t");
	if (command == "OK")
    	{
			document.view_pers.nick.value = tmp[0];
			document.view_pers.sid.value = tmp[1];
			document.view_pers.city.value = tmp[2];
            document.view_pers.level.value = tmp[3];
            document.view_pers.pro.value = tmp[4];
            document.view_pers.clan.value = tmp[5];
			document.view_pers.submit();
        }
	else
    	{
            alert ('nick = ' + tmp[0] + '\nsid = ' + tmp[1] + '\ncity = ' + tmp[2]);
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
<OBJECT id="tz" codeBase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" height="1" width="1" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><PARAM NAME="movie" VALUE="../_imgs/auth.swf"><PARAM NAME="wmode" VALUE="transparent">
<embed src="../_imgs/auth.swf" wmode="transparent" width="1" height="1" swLiveConnect="true" id="tz" name="tz" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
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
?>
	</td>
  </tr>
</table>
</body>
</html>