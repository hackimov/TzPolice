<?php

require("../_modules/functions.php");
require("../_modules/auth.php");
    if (!(Abs(AccessLevel)&AccessAdminOR))
    {
		header("HTTP/1.1 303 See other");
		header("Location: http://www.tzpolice.ru/");
		die;
    }

	$result=mysql_query("DELETE from case_main where case_url='forum.pl?a=D4&c=278734303';");
	
?>
