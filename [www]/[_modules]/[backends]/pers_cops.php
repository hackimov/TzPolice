<?php
$_RESULT = array("res" => "ok");
$state[1] = '������� ��������';
$state[2] = '������� � ����������';
$state[3] = '���������';
$state[4] = '���������';
$kinds[0] = '<font color="yellow">����������</font>';
$kinds[1] = '<font color="blue">������������</font>';
$bg = 0;
$bgstr[0]="#D0BD9D";
$bgstr[1]="#DBA951";
require_once "../xhr_config.php";
require_once "../xhr_php.php";
require_once "../functions.php";
require_once "../auth.php";
$JsHttpRequest =& new Subsys_JsHttpRequest_Php("windows-1251");
$nick = $_REQUEST['cop_n'];
$nick2 = str_replace(" ", "%20", $nick);
$sid = $_REQUEST['cop_s'];
$city = $_REQUEST['cop_c'];
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
        if (strpos($tmp_body, "OK") && in_array($nick, $pers_cops))
        	{
$id = $_REQUEST['id'];
$query = "SELECT * FROM `or_data` WHERE `id` = '".$id."' LIMIT 1;";
$rs = mysql_query($query);
$tmp = mysql_fetch_array($rs);
$query = "SELECT `id` FROM `or_data` WHERE `user` = '".$tmp['user']."'";
$rs = mysql_query($query);
$usr_count = mysql_num_rows($rs);
$query = "SELECT `id` FROM `or_data` WHERE `pers` = '".$tmp['pers']."'";
$rs = mysql_query($query);
$pers_count = mysql_num_rows($rs);
$ips = stripslashes(str_replace("\n", "<br>", $tmp['ips']));
$problem = stripslashes(str_replace("\n", "<br>", $tmp['problem']));
if ($tmp['status'] == '1')
	{
?>
<br><br>
<center>������ �� ��������� <b><?=stripslashes($tmp['pers'])?></b></center>
<table width="100%" border="0" cellspacing="3" cellpadding="3" align="center">
  <tr>
    <td bgcolor="#D0BD9D" width="50%">��� ������</td>
    <td bgcolor="#DBA951"><b><?=$kinds[$tmp['silver']]?></b></td>
  </tr>
  <tr>
    <td bgcolor="#D0BD9D" width="50%">���������</td>
    <td bgcolor="#DBA951"><a href="#; return false;" onClick="pr('<?=stripslashes($tmp['user'])?>')"><b><?=stripslashes($tmp['user'])?></b></a> (������: <?=$usr_count?>)</td>
  </tr>
  <tr>
    <td bgcolor="#D0BD9D" width="50%">IP-����� ��� ������ ������</td>
    <td bgcolor="#DBA951"><b><?=$tmp['userip']?></td>
  </tr>
  <tr>
    <td bgcolor="#D0BD9D">���� ������ </td>
    <td bgcolor="#DBA951"><?=date("d.m.Y",$tmp['regtime'])?></td>
  </tr>
  <tr>
    <td bgcolor="#D0BD9D">��� ����� </td>
    <td bgcolor="#DBA951"><b><?=stripslashes($tmp['pers'])?></b> (������: <?=$pers_count?>)</td>
  </tr>
  <tr>
    <td bgcolor="#D0BD9D">������� ����� (�� ����)</td>
    <td bgcolor="#DBA951"><?=$tmp['dismiss']?></td>
  </tr>
  <tr>
    <td bgcolor="#D0BD9D">���� ���������� ����� �� ������ </td>
    <td bgcolor="#DBA951"><?=stripslashes($tmp['lastvisit'])?></td>
  </tr>
  <tr>
    <td bgcolor="#D0BD9D">������ "������" IP-�������</td>
    <td bgcolor="#DBA951"><?=$ips?></td>
  </tr>
  <tr>
    <td bgcolor="#D0BD9D">��������������� e-mail</td>
    <td bgcolor="#DBA951"><?=stripslashes($tmp['old_email'])?></td>
  </tr>
  <tr>
    <td bgcolor="#D0BD9D">�������� e-mail</td>
    <td bgcolor="#DBA951"><?=stripslashes($tmp['new_email'])?></td>
  </tr>
  <tr>
    <td bgcolor="#D0BD9D">����� �� ��������� ������ </td>
    <td bgcolor="#DBA951"><?=stripslashes($tmp['sec_answer'])?></td>
  </tr>
  <tr>
    <td bgcolor="#D0BD9D">�����������</td>
    <td bgcolor="#DBA951"><?=stripslashes($tmp['problem'])?></td>
  </tr>
  <tr>
    <td bgcolor="#D0BD9D">���������� (��� ������ ��� ���������� �������������� ����� ���������)</td>
    <td bgcolor="#DBA951"><form name="comm" method="post" action="">
  <textarea name="comment" style="width: 100%" rows="5" wrap="VIRTUAL" id="r_comment"></textarea>
  <input type="hidden" name="cur_id" id="cur_id" value="<?=$id?>">
  </form></td>
  </tr>
</table>

<center><a href="#; return false;" onClick="javascript: accept();">�������</a> || <a href="#; return false;" onClick="javascript: deny();">���������</a></center>
<hr>
� ���� "���������" ���������� "(������: X)" ��������� �� ���������� ������, �������� �� ����� ������� ���������<br>
� ���� "��� �����" ���������� "(������: X)" ��������� �� ���������� ������, �������� ��� �������������� �������� ��� ������ ����������<br>
<?
}
else
{
echo ("������ ������ ��� ����������");
}


            }
		else
        	{
            	echo ("������ �����������. ������� � ���� ����� ����������. ��������, ��� �������� ������ � ������ ������.");
            }
    }
else
	{
    	echo ("������ �����������. ������� � ���� ����� ����������.");
    }
?>