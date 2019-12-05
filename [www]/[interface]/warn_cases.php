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
	include "_ORheader.php";
	if (!isset($_REQUEST["case_type"]))
	{
		$_REQUEST["case_type"]=1;
	}
	$result=mysql_query("SELECT count(case_id) as max_rows FROM case_main WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(DATE_CASE_TAKEN))>1728000 and date_case_closed=0 and date_case_taken!=0 and case_type".$_REQUEST["case_type"].";");
	if ($result&&mysql_num_rows($result))
	{
		$row=mysql_fetch_assoc($result);
	}
	print "<center><h1>Дела, задержанные более чем на 20 дней</h1></center><br>На&nbsp;страницу&nbsp;";
	if (!isset($_GET["p"]))
	{
		$_GET["p"]=1;
	}
	if (!isset($_REQUEST["sort"]))
	{
		$_REQUEST["sort"]="d";
	}
	for ($i=1;$i<(int)($row["max_rows"]/15)+2;$i++)
	{
		if ($i==$_GET["p"])
		{
			print "[$i] ";
		}
		else
		{
			print "<a href=\"warn_cases.php?p=$i&case_type=".$_REQUEST["case_type"]."&sort=".$_REQUEST["sort"]."\">$i</a> ";
		}
	}
//sort types:
//d = days investigated
//i = investigator
//a = accuser
//b = date case begin
//t = date case taken
print "<table width=100% cellspacing=1 cellpadding=5 border=1>\n";
print "<tr>\n";
print "	<th width=1%>".($_REQUEST["sort"]=="d"?"":"<a href= \"warn_cases.php?p=1&sort=d\">")."Дней расследуется".($_REQUEST["sort"]=="d"?"":"</a>")."</th>\n";
print "	<th width=1%>".($_REQUEST["sort"]=="i"?"":"<a href= \"warn_cases.php?p=1&sort=i\">")."Следователь".($_REQUEST["sort"]=="i"?"":"</a>")."</th>\n";
print "	<th width=1%>".($_REQUEST["sort"]=="a"?"":"<a href= \"warn_cases.php?p=1&sort=a\">")."Жертва".($_REQUEST["sort"]=="a"?"":"</a>")."</th>\n";
print "	<th width=1%>".($_REQUEST["sort"]=="b"?"":"<a href= \"warn_cases.php?p=1&sort=b\">")."Дата жалобы".($_REQUEST["sort"]=="b"?"":"</a>")."</th>\n";
print "	<th width=1%>".($_REQUEST["sort"]=="t"?"":"<a href= \"warn_cases.php?p=1&sort=t\">")."Дата взятия дела".($_REQUEST["sort"]=="t"?"":"</a>")."</th>\n";
print "	<th width=1%>Ссылка на тему с жалобой</th>\n";
print "</tr>\n";
    $sortarr=array();
    $sortarr["d"]="days_investigated desc";
    $sortarr["i"]="investigator";
    $sortarr["a"]="accuser";
    $sortarr["b"]="date_case_begin desc";
    $sortarr["t"]="date_case_taken asc, date_case_begin desc";
	$result=mysql_query("SELECT investigator,case_type,accuser,UNIX_TIMESTAMP(date_case_begin) as _date_case_begin,UNIX_TIMESTAMP(date_case_taken) as _date_case_taken,case_url,((UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(DATE_CASE_TAKEN))/86400) as days_investigated FROM case_main WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(DATE_CASE_TAKEN))>1728000 and date_case_closed=0  and date_case_taken!=0 and case_type=".$_REQUEST["case_type"]." order by ".$sortarr[$_REQUEST["sort"]]." LIMIT ".(($_GET["p"]-1)*15).",15;");
//	$result=mysql_query("SELECT investigator,case_type,accuser,UNIX_TIMESTAMP(date_case_begin) as _date_case_begin,UNIX_TIMESTAMP(date_case_taken) as _date_case_taken,case_url,DATEDIFF(NOW(),DATE_CASE_TAKEN) as days_investigated FROM case_main WHERE DATEDIFF(NOW(),DATE_CASE_TAKEN)>20 and date_case_closed=0 order by date_case_begin LIMIT ".(($_GET["p"]-1)*15).",15;");
	if ($result&&mysql_num_rows($result))
		{//we have free cases with no investigator
			while ($row=mysql_fetch_assoc($result))
			{
				print "<tr".($row["days_investigated"]>23?" class=".($row["days_investigated"]>26?"toobad":"bad"):"").">\n";
				print "<td nowrap valign=top><b>".ceil($row["days_investigated"])."</b></td>\n";
				print "<td nowrap valign=top><b>".$row["investigator"]."</b></td>\n";
				print "<td nowrap valign=top><b>".$row["accuser"]."</b></td>\n";
				print "<td nowrap valign=top><b>".date("d-m-Y H:i:s",$row["_date_case_begin"])."</b></td>\n";
				print "<td nowrap valign=top><b>".date("d-m-Y H:i:s",$row["_date_case_taken"])."</b></td>\n";
				print "<td nowrap valign=top><a href=\"http://www.timezero.ru/cgi-bin/".$row["case_url"]."&m=1\" target=_blank>сюда</a></td></tr>\n";
				print "</tr>\n";
			}
		}
	print" </table>\n";
	include "_ORfooter.php";
	print"</BODY>";
?>