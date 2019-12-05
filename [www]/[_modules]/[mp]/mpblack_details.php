<?php

define("MPLIB",true);
include("mp_library.php");
require_once('../functions.php');

$id = quote_smart($_POST['id']);

$id = iconv("UTF-8", "WINDOWS-1251", $id);

$r ="<table width=500><tr><td align=right><span id='closeaddform'>X</span></td></tr><tr><td>";

$result = mysql_query("SELECT logs FROM mp_black_list_persons WHERE login = '".$id."'");

if ($b = mysql_fetch_array($result)) {
	
	$logs = buildLogsForDisplay($b['logs']);
	$r .= $logs[0];

} else {
	$r .= ("Логов по персонажу ".$id." не обнаружено!");
	
}

$r .= "</td></tr></table>";

$r = iconv("UTF-8" ,"WINDOWS-1251", $r);
echo $r;

?>