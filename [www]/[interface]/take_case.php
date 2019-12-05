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
if (!isset($_REQUEST["caseid"]))
{
    header("HTTP/1.1 200 OK");
    header("Content-type: text/html; charset=windows-1251");
	print <<<END
	<HTML><HEAD>
	<TITLE>Hack was attempted!!!</TITLE>
	</HEAD>
	<BODY>
		Попытка манипулирования информацией. Администратор извещен о ваших действиях. Ваш IP ($REMOTE_ADDR) и логин (AuthUserName) записаны.
	</BODY>
END;
	mail("it@tzpolice.ru","data manipulation attempt","User ".AuthUserName." from IP ".$REMOTE_ADDR." has not sent a POST method (or modified it) for take_case.php\nPossible data manipulation...");
	die;
}
	$result=mysql_query("SELECT investigator from case_main where case_id=".$_REQUEST["caseid"].";");
	if ($result&&mysql_num_rows($result))
	{//Case already taken
		$row=mysql_fetch_assoc($result);
		if ($row["investigator"]!="")
		{//case already taken... What to do?

		}
		else
		{
			$result=mysql_query("UPDATE case_main set investigator=\"".AuthUserName."\", date_case_taken=\"".date("ymdHi")."\" WHERE case_id=".$_REQUEST["caseid"].";");
		}
	}
header("HTTP/1.1 303 See other");
header("Location: http://www.tzpolice.ru/interface/show_own_cases.php");

?>