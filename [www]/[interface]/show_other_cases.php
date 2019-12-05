<?php
//require("/mnt/mirror/usr/home/sites/tzpoli/public_html/_modules/functions.php");
//require("/mnt/mirror/usr/home/sites/tzpoli/public_html/_modules/auth.php");

require("../_modules/functions.php");
require("../_modules/auth.php");
    if (!(Abs(AccessLevel)&AccessAdminOR))
    {
		header("HTTP/1.1 303 See other");
		header("Location: http://www.tzpolice.ru/");
		die;
    }
include "_ORheader.php";
	$result=mysql_query("select COUNT(case_id) as max_rows from case_main where investigator like '".$_REQUEST["investigator"]."';");
	if ($result&&mysql_num_rows($result))
	{
		$row=mysql_fetch_assoc($result);
	}
	print "<center><h1>Дела следователя ".$_REQUEST["investigator"].".</h1></center>На&nbsp;страницу&nbsp;";
	if (!isset($_GET["p"]))
	{
		$_GET["p"]=1;
	}
	if (!isset($_REQUEST["sort"]))
	{
		$_REQUEST["sort"]="b";
	}
	for ($i=1;$i<(int)($row["max_rows"]/15)+2;$i++)
	{
		if ($i==$_GET["p"])
		{
			print "[$i]";
		}
		else
		{
			print "<a href=\"show_other_cases.php?p=$i&investigator=".$_REQUEST["investigator"]."&sort=".$_REQUEST["sort"]."\">$i</a>&nbsp;";
		}
	}
//sort types:
//a = accuser
//b = date case begin
//t = date case taken
//c = date case closed
print "<table width=100% cellspacing=1 cellpadding=5 border=1><tr>";
print "<th width=1%>Отдел</th>";
print "<th width=1%>Дней расследуется</th>";
print "<th width=1%>".($_REQUEST["sort"]=="a"?"":"<a href= \"show_other_cases.php?p=1&investigator=".$_REQUEST["investigator"]."&sort=a\">")."Жертва".($_REQUEST["sort"]=="a"?"":"</a>")."</th>";
print "<th width=1%>".($_REQUEST["sort"]=="b"?"":"<a href= \"show_other_cases.php?p=1&investigator=".$_REQUEST["investigator"]."&sort=b\">")."Дата жалобы".($_REQUEST["sort"]=="b"?"":"</a>")."</th>";
print "<th width=1%>".($_REQUEST["sort"]=="t"?"":"<a href= \"show_other_cases.php?p=1&investigator=".$_REQUEST["investigator"]."&sort=t\">")."Дата взятия дела".($_REQUEST["sort"]=="t"?"":"</a>")."</th>";
print "<th width=1%>".($_REQUEST["sort"]=="c"?"":"<a href= \"show_other_cases.php?p=1&investigator=".$_REQUEST["investigator"]."&sort=c\">")."Дата закрытия дела".($_REQUEST["sort"]=="c"?"":"</a>")."</th>";
print "<th width=1%>Ссылка на тему с жалобой</th>";
print "<th width=1%>Текст жалобы</th>";
print "<th width=1%>Закрыть жалобу?</th>";
print "<th width=1%>Передать дело</th>";
print "<th width=1%>Статус закрытого дела</th>";
print "</tr>";
    $sortarr=array();
    $sortarr["a"]="accuser";
    $sortarr["b"]="date_case_begin desc";
    $sortarr["t"]="date_case_taken asc, date_case_begin desc";
    $sortarr["c"]="date_case_closed asc, date_case_begin desc";
	$result=mysql_query("select closure_type,accuser,UNIX_TIMESTAMP(date_case_begin) as _date_case_begin,UNIX_TIMESTAMP(date_case_taken) as _date_case_taken,UNIX_TIMESTAMP(date_case_closed) as _date_case_closed,((UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(DATE_CASE_TAKEN))/86400) as days_investigated,case_url,case_text,case_id,case_type from case_main where investigator=\"".$_REQUEST["investigator"]."\" order by ".$sortarr[$_REQUEST["sort"]]." LIMIT ".(($_GET["p"]-1)*15).",15;")or die("Invalid query: " . mysql_error());//do the command
	if ($result&&mysql_num_rows($result))
	{//we have cases with this investigator
		while ($row=mysql_fetch_assoc($result))
		{
			print "<tr".(($row["_date_case_closed"]>0)?" class=closed ":"").">\n";
			/*if ($row["_date_case_closed"]>0)
			{
            		$begintag="<FONT color=\"#999999\">\n";
            		$endtag="</FONT>\n";
            }
            else
            {
            	$begintag=$endtag="";
            }*/
			print "<td nowrap valign=top><b>".$ctype[$row["case_type"]]."</b></td>\n";
			print "<td nowrap valign=top>".($row["_date_case_closed"]>0?"ЗКР":ceil($row["days_investigated"]))."</td>\n";
			print "<td nowrap valign=top>".$row["accuser"]."</td>\n";
			print "<td nowrap valign=top>".date("d-m-Y H:i",$row["_date_case_begin"])."</td>\n";
			print "<td nowrap valign=top>".($row["_date_case_taken"]?date("d-m-Y H:i",$row["_date_case_taken"]):"&nbsp;")."</td>\n";
			print "<td nowrap valign=top>".($row["_date_case_closed"]?date("d-m-Y H:i",$row["_date_case_closed"]):"&nbsp;")."</td>\n";
//			$url_old = str_replace ("forum.pl", "forum3.pl", $row["case_url"]);
			$url_old = $row["case_url"];
			print "<td nowrap valign=top><a href=\"http://www.timezero.ru/cgi-bin/".$url_old."&m=1\" target=_blank>сюда</a></td>\n";
			print "<td valign=top><a onclick=\"javascript:if(l".$row["case_id"].".style.display=='none') l".$row["case_id"].".style.display=''; else l".$row["case_id"].".style.display='none';\" href=\"javascript:{}\">Текст жалобы</a><div id=\"l".$row["case_id"]."\" style=\"display:none\">".$row["case_text"]."</div></td>\n";
			print "<td align=center>";
			if ($row["_date_case_closed"]>0)
			{
				print "<FONT color=\"red\">Дело закрыто.</FONT>";
			}
			else
			{
			print "<a href=\"javascript:document.closecase.caseid.value=".$row["case_id"].";document.closecase.submit()\"><IMG SRC=\"/_imgs/subm.gif\" WIDTH=\"55\" HEIGHT=\"28\" BORDER=\"1\" ALT=\"Закрыть дело\"></a>";
			}
			print "</td>\n<td align=center>";
			print "<SELECT ONCHANGE=\"transfercase.investigator.value=this.value; transfercase.caseid.value=".$row["case_id"].";transfercase.submit();\" name=\"investigator".$row["case_id"]."\">";
			foreach ($investigators as $i) {
				print "<option value=\"".$i."\"".(isset($_REQUEST["investigator"])?($i==$_REQUEST["investigator"]?" selected":""):"").">".$i."</option>\n";
			}
			print "</SELECT>\n";
			print "<a href=\"javascript:transfercase.caseid.value=".$row["case_id"].";transfercase.investigator=this.investigator".$row["case_id"].".value;transfercase.submit();\"><IMG SRC=\"/_imgs/subm.gif\" WIDTH=\"55\" HEIGHT=\"28\" BORDER=\"1\" ALT=\"Передать дело\"></a>";
			print "</td>";
			print "<td nowrap valign=top>".$row["closure_type"]."</td>\n</tr>\n";
		}
	}
	print "<FORM name=\"closecase\" method=POST action=\"close_case.php\">\n<INPUT TYPE=\"hidden\" name=\"caseid\" value=\"1\">";
	print "</FORM>";
	print "<FORM name=\"transfercase\" method=POST action=\"transfer_case.php\">\n<INPUT TYPE=\"hidden\" name=\"caseid\" value=\"1\"><INPUT TYPE=\"hidden\" name=\"investigator\" value=\"\">";
	print" </table>\n";
	include "_ORfooter.php";
	print"</BODY>";
?>
