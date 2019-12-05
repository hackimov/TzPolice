<?php
require("../_modules/functions.php");
require("../_modules/auth.php");
    if (!(Abs(AccessLevel)&(AccessOR|AccessAdminOR)))
    {
		header("HTTP/1.1 303 See other");
		header("Location: http://tzpolice.ru/");
		die;
    }
    header("HTTP/1.1 200 OK");
    header("Content-type: text/html; charset=windows-1251");
    print <<<_HEADER
<HTML><HEAD>
<TITLE>Просмотр IP взломщиков (тестовый вариант)</TITLE> 
<meta content="text/html; charset=windows-1251" http-equiv="Content-type">
</HEAD>
<style type="text/css">
	body {font-size: 14px}
	.toobad {color:red}
	.bad {color:blue}
	.closed {color:#999999}
</style>
<BODY>
_HEADER;
	print "<center><h1>Таблица IP взломщиков</h1></center>";
	print "<table width=100% cellspacing=1 cellpadding=5 border=1>";
	print "<tr>";
	print "<th width=1%>IP</th>";
	print "<th width=1%>Ссылка на дело</th>";
	print "</tr>";
	$current_ip="";
	$result=mysql_query("SELECT vzlom_count.ip_count, vzlom.ip, vzlom.forum_link FROM vzlom, (SELECT count(ip) AS ip_count, ip FROM vzlom GROUP BY ip) AS vzlom_count WHERE vzlom.ip=vzlom_count.ip ORDER BY vzlom_count.ip_count DESC,vzlom.ip DESC;");
	if ($result&&mysql_num_rows($result))
	{//we have everything we need!
		while ($row=mysql_fetch_assoc($result))
		{
			print "<tr>";
			if ($current_ip<>$row["ip"])
			{
				print "<td ".($row["ip_count"]>5?"class=toobad":($row["ip_count"]>5?"class=bad":""))." rowspan=".$row["ip_count"].">".$row["ip"]."</td>";
				$current_ip=$row["ip"];
			}
			print "<td><a href=\"http://www.timezero.ru/cgi-bin/".$row["forum_link"]."&m=1\" target=_blank>Сюда</a></td>";
			print "</tr>";
		}
	}
?>