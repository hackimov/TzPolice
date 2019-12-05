<?php

//require("/home/sites/police/www/_modules/functions.php");
//require("/home/sites/police/www/_modules/auth.php");

require("../_modules/functions.php");
require("../_modules/auth.php");
    if (!(Abs(AccessLevel)&(AccessOR|AccessAdminOR)))
    {
		header("HTTP/1.1 303 See other");
//		header("Location: http://www.tzpolice.ru/");
		header("Location: http://police.timezero.ru/");
		die;
    }
include "_ORheader.php";
	$result=mysql_query("select COUNT(case_id) as max_rows from case_main where investigator is NULL and case_type=".(isset($_REQUEST["case_type"])?$_REQUEST["case_type"]:"1").";");
	if ($result&&mysql_num_rows($result))
	{
		$row=mysql_fetch_assoc($result);
	}
	print "<center><h1>Невзятые дела ".(isset($_REQUEST["case_type"])?$ctype[$_REQUEST["case_type"]]:$ctype[1]).".</h1></center>";
	if (!isset($_REQUEST["p"]))
	{
		$_REQUEST["p"]=1;
	}
	if (!isset($_REQUEST["case_type"]))
	{
		$_REQUEST["case_type"]=1;
	}
	if (!isset($_REQUEST["sort"]))
	{
		$_REQUEST["sort"]="b";
	}
	if ($_REQUEST["case_type"]>2)
	{
		print "Фильтр: <SELECT ONCHANGE=\"show_h.case_type.value=this.value; show_h.action='show_free_cases.php'; show_h.submit();\">\n";
		for ($i=3;$i<count($ctype)+1;$i++)
		{
			print "<OPTION value=".$i.(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]==$i?" selected":""):"").">".$ctype[$i]."</OPTION>";
		}
		print "</SELECT>";
	}
	print "<br>На&nbsp;страницу&nbsp;";
	for ($i=1;$i<(int)($row["max_rows"]/15)+2;$i++)
	{
		if ($i==$_REQUEST["p"])
		{
			print "[$i]";
		}
		else
		{
			print "<a href=\"show_free_cases.php?p=$i&case_type=".$_REQUEST["case_type"]."&sort=".$_REQUEST["sort"]."\">$i</a>&nbsp;";
		}
	}
//sort types:
//a = accuser
//b = date case begin
print "<table width=100% cellspacing=1 cellpadding=5 border=1>";
print "<tr>";
print "	<th width=1%>Отдел</th>";
print "	<th width=1%>".($_REQUEST["sort"]=="a"?"":"<a href= \"show_free_cases.php?p=1&case_type=".$_REQUEST["case_type"]."&sort=a\">")."Жертва</th>";
print "	<th width=1%>".($_REQUEST["sort"]=="b"?"":"<a href= \"show_free_cases.php?p=1&case_type=".$_REQUEST["case_type"]."&sort=b\">")."Дата жалобы</th>";
print "	<th width=1%>Ссылка на тему с жалобой</th>";
print "	<!--<th>Текст жалобы</th>-->";
print "<th width=1%>Взять жалобу?</th>";
if ($_REQUEST["case_type"]>2)
{
	print "<th width=1%>Перенос жалобы</th>";
}
print "</tr>";
    $sortarr=array();
    $sortarr["a"]="accuser";
    $sortarr["b"]="date_case_begin desc";
	$result=mysql_query("select accuser,UNIX_TIMESTAMP(date_case_begin) as _date_case_begin,UNIX_TIMESTAMP(date_case_taken) as _date_case_taken,UNIX_TIMESTAMP(date_case_closed) as _date_case_closed,case_url,case_id,case_type from case_main where investigator is NULL and case_type=".(isset($_REQUEST["case_type"])?$_REQUEST["case_type"]:"1")." order by ".$sortarr[$_REQUEST["sort"]]." LIMIT ".(($_REQUEST["p"]-1)*15).",15;")or die("Invalid query: " . mysql_error());//do the command
	if ($result&&mysql_num_rows($result))
	{//we have free cases with no investigator
		while ($row=mysql_fetch_assoc($result))
		{
			$hours_since_begin=(time()-$row["_date_case_begin"])/3600;
			print "<tr".($hours_since_begin>72?" class=toobad":($hours_since_begin>60?" class=bad":"")).">\n";
			print "<td nowrap valign=top><b>".$ctype[$row["case_type"]]."</b></td>\n";
			print "<td nowrap valign=top>".$row["accuser"]."</td>\n";
			print "<td nowrap valign=top>".date("d-m-Y H:i",$row["_date_case_begin"])."</td>\n";
//			$url_old = str_replace ("forum.pl", "forum3.pl", $row["case_url"]);
			$url_old = $row["case_url"];
			print "<td nowrap valign=top><a href=\"http://www.timezero.ru/cgi-bin/".$url_old."&m=1\" target=_blank>сюда</a></td>\n";
			//print "<td><a onclick=\"javascript:if(l".$row["case_id"].".style.display=='none') l".$row["case_id"].".style.display=''; else l".$row["case_id"].".style.display='none';\" href=\"javascript:{}\">Текст жалобы</a><div id=\"l".$row["case_id"]."\" style=\"display:none\">".$row["case_text"]."</div></td>\n";
			print "<td align=center><a href=\"javascript:document.takecase.caseid.value=".$row["case_id"].";document.takecase.submit()\" title=\"Взять дело\"><IMG SRC=\"/_imgs/subm.gif\" WIDTH=\"55\" HEIGHT=\"28\" BORDER=\"1\" ALT=\"Взять дело\"></a></td>";
			if ($_REQUEST["case_type"]>2)
			{
				print "<td nowrap valign=top>";
				print "<SELECT ONCHANGE=\"transferwhine.case_type.value=this.value;transferwhine.caseid.value=".$row["case_id"].";transferwhine.submit();\">\n";
				for ($i=3;$i<count($ctype)+1;$i++)
				{
					print "<OPTION value=".$i.(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]==$i?" selected":""):"").">".$ctype[$i]."</OPTION>";
				}
				print "</SELECT>";
			}
			print "</tr>\n";
		}
	}
	print "<FORM name=\"takecase\" method=POST action=\"take_case.php\">\n<INPUT TYPE=\"hidden\" name=\"caseid\" value=\"1\"></FORM>";
	print "<FORM name=\"transferwhine\" method=POST action=\"transfer_whine.php\">\n<INPUT TYPE=\"hidden\" name=\"caseid\" value=\"1\">\n<INPUT TYPE=\"hidden\" name=\"case_type\" value=\"1\">\n<INPUT TYPE=\"hidden\" name=\"returnback\" value=\"show_free_cases.php?p=".$_REQUEST["p"]."&case_type=".$_REQUEST["case_type"]."&sort=".$_REQUEST["sort"]."\"></FORM>";
	print" </table>\n";
	include "_ORfooter.php";
	print"</BODY>";
?>
