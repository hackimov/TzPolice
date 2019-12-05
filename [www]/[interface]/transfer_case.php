<?php
//require("/mnt/mirror/usr/home/sites/tzpoli/public_html/_modules/functions.php");
//require("/mnt/mirror/usr/home/sites/tzpoli/public_html/_modules/auth.php");

require("../_modules/functions.php");
require("../_modules/auth.php");
    if (!(Abs(AccessLevel)&(AccessOR|AccessAdminOR)))
    {
		header("HTTP/1.1 303 See other");
		header("Location: http://www.tzpolice.ru/");
		die;
    }
if (!isset($_POST["caseid"]))
{
    header("HTTP/1.1 200 OK");
    header("Content-type: text/html; charset=windows-1251");
	print <<<END
	<HTML><HEAD>
	<TITLE>Hack was attempted!!!</TITLE>
	</HEAD>
	<BODY>
		Попытка манипулирования информацией. Администратор извещен о ваших действиях. Ваш IP($REMOTE_ADDR) и логин(AuthUserLogin) записаны.
	</BODY>
END;
	mail("it@tzpolice.ru","data manipulation attempt","User ".AuthUserName." from IP ".$REMOTE_ADDR." has not sent a POST method (or modified it) for take_case.php\nPossible data manipulation...");
	die;
}
	$result=mysql_query("UPDATE case_main set investigator=\"".$_POST["investigator"]."\", date_case_taken=\"".date("ymdHi")."\" WHERE case_id=".$_POST["caseid"].";");
	error_log(date("d.m.Y H:i:s")."UPDATE case_main set investigator=\"".$_POST["investigator"]."\", date_case_taken=\"".date("ymdHi")."\" WHERE case_id=".$_POST["caseid"].";",3,"errlog.txt");
header("HTTP/1.1 303 See other");
header("Location: http://www.tzpolice.ru/interface/");

?>