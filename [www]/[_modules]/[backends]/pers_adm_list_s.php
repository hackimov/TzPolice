<?php
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
        if (strpos($tmp_body, "OK") && in_array($nick, $pers_adm))
        	{
?>
<form name="nicks" method="post" action="">
  <select name="select" size="1" id="select">
<?
$query = "SELECT `id`, `pers` FROM `or_data` WHERE `status` = '2' AND `silver` = '1' ORDER BY `id`";
$res = mysql_query($query);
while($ttmp = mysql_fetch_array($res))
	{
?>
    <option value="<?=$ttmp['id']?>"><?=$ttmp['pers']?></option>
<?}?>
  </select>
  <input name="cur_id" type="hidden" id="cur_id" value="0">
  <input type="button" name="Button" value="выбрать" onClick="showPers()">
</form>
<?
$_RESULT = array("res" => "ok");
            }
    }
?>