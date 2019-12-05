<?php
//require("/home/sites/police/www/_modules/functions.php");
//require("/home/sites/police/www/_modules/auth.php");
require("../_modules/functions.php");
require("../_modules/auth.php");
    if (!(Abs(AccessLevel)&(AccessOR|AccessAdminOR)))
    {
		header("HTTP/1.1 303 See other");
		header("Location: http://www.tzpolice.ru/");
		die;
    }
if (!isset($_REQUEST["caseid"]))
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
	mail("it@tzpolice.ru","data manipulation attempt","User ".AuthUserName." from IP ".$REMOTE_ADDR." has not sent a POST method (or modified it) for close_case.php\nPossible data manipulation...");
	die;
}
	$result=mysql_query("UPDATE case_main set date_case_closed=\"".date("ymdHi")."\" WHERE case_id=".$_REQUEST["caseid"].";");
header("HTTP/1.1 303 See other");
header("Location: http://www.tzpolice.ru/interface/show_own_cases.php");

?>