<?php
require("../_modules/functions.php");
require("../_modules/auth.php");
$state[1] = '<font color="yellow"><b>ожидает проверки</b></font>';
$state[2] = '<font color="orange"><b>принята к исполнению</b></font>';
$state[3] = '<font color="green"><b>выполнена</b></font>';
$state[4] = '<font color="red"><b>отклонена</b></font>';
$bg = 0;
$bgstr[0]="#D0BD9D";
$bgstr[1]="#DBA951";
$nick = $_REQUEST['pn'];
$nick = str_replace("%20", " ", $nick);
$look_for = iconv('UTF-8', 'cp1251', $_REQUEST['cn']);
$sid = $_REQUEST['ps'];
$nick2 = str_replace(" ", "%20", $nick);
$city = $_REQUEST['pc'];
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
  <title>Заявки от персонажа <?=$look_for?></title>
  <LINK href="../_modules/tzpol_css.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#EBDFB7" text="#455600" alink="#0D3AB4" link="#0D3AB4" vlink="#0D3AB4">
<?
if (strlen($sid) > 3)
	{
	$sock = @fsockopen("www.timezero.ru", 80, $er1, $er2, 5);
    if(@$sock)
    	{
    		$addr = "/cgi-bin/authorization.pl?login=".$nick2."&ses=".$sid."&city=".$city;
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
        if (strpos($tmp_body, "OK") && (in_array($nick, $pers_adm) ||  in_array($nick, $pers_cops)))
        	{
//                $_RESULT = array("res" => $nick);
				$query = "SELECT * FROM `or_data` WHERE `user` = '".$look_for."'";
                $rs = mysql_query($query);
?>
<br>&nbsp;&nbsp;&nbsp; >> Заявки от персонажа <b><?=$look_for?></b><br><br>
<table width="450" border="0" align="center" cellpadding="3" cellspacing="3">
<tr bgcolor='#DBA951' align='center'><td>ник</td><td>дата</td><td align='center'>статус</td><td align='center'>примечания</td>
<?

                while ($tmp = mysql_fetch_array($rs))
                	{
                          echo ("<tr bgcolor='$bgstr[$bg]'><td><b>".$tmp['pers']."</b></td><td align='center'>".date("d.m.Y", $tmp['regtime'])."</td><td align='center'>".$state[$tmp['status']]."</td>");
                          if ($tmp['status'] > 2)
                          	{
                                $comm = stripslashes(str_replace("\n", "<br>", $tmp['answer']));
                            	echo ("<td>".$comm."</td></tr>");
                            }
                          else
                          	{
                            	echo ("<td>&nbsp;</td></tr>");
                            }
                          $bg++;
                          if ($bg > 1) {$bg = 0;}
                    }
?>
</table>
<hr>
<ul>
<li><b>ожидает проверки</b> - заявка принята, но еще не проверена полицейским
<li><b>принята к исполнению</b> - заявка проверена полицейским и отправлена для принятия решения администрации
<li><b>отклонена</b> - заявка проверена, но по указанной причине выполнена не будет
<li><b>выполнена</b> - заявка проверена, данные персонажа изменены на новые
</ul>

<?
            }
        else
        	{
				echo ("Кто такой не знаю и что тут делаешь не в курсе!");
            }
    }
?>
</body>
</html>