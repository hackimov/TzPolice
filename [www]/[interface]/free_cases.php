<?php
require("../_modules/functions.php");
require("..//_modules/auth.php");

//require("../_modules/functions.php");
//require("../_modules/auth.php");
    if (!(Abs(AccessLevel)&AccessAdminOR))
    {
		header("HTTP/1.1 303 See other");
		header("Location: http://tzpolice.ru/");
		die;
    }
print <<<MAINBODY
<center><h1>Невзятые дела.</h1></center>
<table width=100% cellspacing=1 cellpadding=5 border=1>
<tr>
	<th>Отдел</th>
	<th>Жертва</th>
	<th>Дата жалобы</th>
	<th>Ссылка на тему с жалобой</th>
</tr>
MAINBODY;
	$ctype = array();
	$ctype[1] = "ОБЭП";
	$ctype[2] = "ОР";
	$ctype[3] = "Жалобы";
	$result=mysql_query("select case_type,accuser,UNIX_TIMESTAMP(date_case_begin) as _date_case_begin,UNIX_TIMESTAMP(date_case_taken) as _date_case_taken,UNIX_TIMESTAMP(date_case_closed) as _date_case_closed,case_url,case_text,case_id from case_main where investigator is NULL order by case_type, date_case_begin desc;")or die("Invalid query: " . mysql_error());//do the command
	if ($result&&mysql_num_rows($result))
	{//we have free cases with no investigator
		while ($row=mysql_fetch_assoc($result))
		{
//			$url_old = str_replace ("forum.pl", "forum3.pl", $row["case_url"]);
			$url_old = $row["case_url"];
			print "<tr>\n";
			print "<td nowrap valign=top><b>".$ctype[$row["case_type"]]."</b></td>\n";
			print "<td nowrap valign=top>".$row["accuser"]."</td>\n";
			print "<td nowrap valign=top>".date("d-m-Y H:i",$row["_date_case_begin"])."</td>\n";
			print "<td nowrap valign=top><a href=\"http://www.timezero.ru/cgi-bin/".$url_old."\" target=_blank>".$begintag."сюда".$endtag."</a></td>\n";
			print "</tr>\n";
		}
	}
	print" </table>\n";
	print"</BODY>";
?>
