<?php
require_once "../xhr_config.php";
require_once "../xhr_php.php";
require_once "../functions.php";
require_once "../auth.php";
$JsHttpRequest =& new Subsys_JsHttpRequest_Php("windows-1251");
$nick = $_REQUEST['n'];
$userinfo = GetInfoFromApi($nick);
$query = "SELECT `user` FROM `or_data` WHERE `pers` = '".$nick."' AND `status` < '3'";
$rs = mysql_query($query);
if (mysql_num_rows($rs) > 0)
	{
        $tmp = mysql_fetch_array($rs);
		$_RESULT = array("res" => "processed", "usr" => $tmp['user']);
    }
elseif ($userinfo["level"] > 0)
	{
		if ($userinfo["dismiss"] == '')
        	{
				$_RESULT = array("res" => "noblock");
            }
		else
        	{
				$_RESULT = array("res" => "blocked");
            }
    }
else
	{
		$_RESULT = array("res" => "nouser");
    }
?>
OK