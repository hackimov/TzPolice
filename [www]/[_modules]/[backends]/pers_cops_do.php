<?php
$state[1] = '������� ��������';
$state[2] = '������� � ����������';
$state[3] = '���������';
$state[4] = '���������';
$bg = 0;
$bgstr[0]="#D0BD9D";
$bgstr[1]="#DBA951";
require_once "../xhr_config.php";
require_once "../xhr_php.php";
require_once "../functions.php";
require_once "../auth.php";
$JsHttpRequest =& new Subsys_JsHttpRequest_Php("windows-1251");
$_RESULT = array("res" => "ok");
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
$status = $_REQUEST['status'];
$comment = addslashes(strip_tags($_REQUEST['comment']));
$curtime = time();
if ($status == "4")
	{
    	$extra = ", `lastvisit` = '0', `ips` = '0', `old_email` = '0', `new_email` = '0', `old_psw` = '0', `sec_answer` = '0'";
    }
else
	{
    	$extra = "";
    }
$query = "UPDATE `or_data` SET `ans_time` = '".$curtime."', `status` = '".$status."', `cop` = '".$nick."', `answer` = '".$comment."'".$extra." WHERE `id` = '".$id."' LIMIT 1;";
$rs = mysql_query($query);
$query = "SELECT * FROM `or_data` WHERE `id` = '".$id."' LIMIT 1;";
$rs = mysql_query($query);
$tmp = mysql_fetch_array($rs);
$message = "\n".stripslashes($tmp['user'])." || ���� ������ �� �������������� �������� ��� ���������� ".stripslashes($tmp['pers'])." ����������. ".stripslashes($tmp['answer'])."\n";
$filename = '/home/sites/police/bot/send-police.txt';
if (file_exists($filename)){
	chmod($filename, 0777);
}
if ($handle = fopen($filename, 'a')) {
	if (fwrite($handle, $message) === FALSE) {
		$noerror=0;
	}
	fclose($handle);
}
?>
������ ����������!
<?
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