<?php
    header("HTTP/1.1 200 OK");
    header("Content-type: text/html; charset=windows-1251");
    print <<<_HEADER
<HTML><HEAD>
<TITLE>Делопроизводство.</TITLE> 
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
	$ctype = array();
	$ctype[1] = "ОБЭП";
	$ctype[2] = "ОР";
	$ctype[3] = "Жалобы на Полицию";
	$ctype[4] = "Жалобы:ОМ+ЛО";
	$ctype[5] = "Жалобы:ОИН";
	$ctype[6] = "Жалобы:ЮО";
	$ctype[7] = "Жалобы:ОБЭП";
	$ctype[8] = "Жалобы:ОР";
	$ctype[9] = "Жалобы:ОКП";
	$ctype[10] = "Жалобы:МП";
	$ctype[11] = "Жалобы:нач. полиции";
	$ctype[12] = "Жалобы:ОКИР";
    $ctype[13] = "Жалобы";
	print "<CENTER>\n";
	print "<a href=\"javascript:show_h.action='show_free_cases.php';show_h.case_type.value=".(isset($_REQUEST["case_type"])?$_REQUEST["case_type"]:"1").";show_h.submit();\">Показать свободные дела</a>";
	print "<SELECT ONCHANGE=\"show_h.case_type.value=this.value; show_h.action='show_free_cases.php'; show_h.submit();\">";
	for ($i=1;$i<4;$i++)
	{
		print "<OPTION value=".$i." ".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]==$i?"selected":""):"").">".$ctype[$i]."</OPTION>";
	}
	print "<OPTION value=13 ".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]==13?"selected":""):"").">".$ctype[13]."</OPTION>";
	print "</SELECT>&nbsp;|&nbsp;";
print <<<START
	<a href="show_own_cases.php">Показать собственные дела</a>&nbsp;|&nbsp;
	<a href="my_stat.php">Показать свою статистику</a>
START;
	print "&nbsp;|&nbsp;\n";
	print "<a href=\"javascript:show_h.action='warn_cases.php';show_h.case_type.value=".(isset($_REQUEST["case_type"])?$_REQUEST["case_type"]:"1").";show_h.submit();\">Дела &gt;20 дней в</a>";
	print "<SELECT ONCHANGE=\"show_h.case_type.value=this.value; show_h.action='warn_cases.php'; show_h.submit();\">\n";
	for ($i=1;$i<4;$i++)
	{
		print "<OPTION value=".$i." ".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]==$i?"selected":""):"").">".$ctype[$i]."</OPTION>";
	}
	print "<OPTION value=13 ".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]==13?"selected":""):"").">".$ctype[13]."</OPTION>";
	print "</SELECT><br><br>\n";
	if (Abs(AccessLevel)&(AccessAdminOR))
	{
		$investigators = array();
		$result=mysql_query("SELECT investigator from case_main where investigator is not NULL group by investigator;");
		if ($result&&mysql_num_rows($result)) {
			while ($row=mysql_fetch_assoc($result))
				$investigators[] = $row["investigator"];
		}
		print "&nbsp;|&nbsp;\n";
		print "<a href=\"javascript:show_h.action='all_stat.php';show_h.case_type.value=".(isset($_REQUEST["case_type"])?$_REQUEST["case_type"]:"1")."; show_h.submit();\">Общая статистика</a>";
		print "<SELECT ONCHANGE=\"show_h.case_type.value=this.value; show_h.action='all_stat.php'; show_h.submit();\">\n";
		for ($i=1;$i<4;$i++)
		{
			print "<OPTION value=".$i." ".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]==$i?"selected":""):"").">".$ctype[$i]."</OPTION>";
		}
		print "<OPTION value=13 ".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]==13?"selected":""):"").">".$ctype[13]."</OPTION>";
		print "</SELECT>\n";
		if (sizeof($investigators)>0)
		{
			print "<a href=\"javascript:show_h.action='other_stat.php'; show_h.submit();\">Показать статистику следователя</a> <SELECT ONCHANGE=\"show_h.investigator.value=this.value; show_h.action='other_stat.php'; show_h.submit();\">";
			foreach ($investigators as $i) {
				print "<option value=\"".$i."\"".(isset($_REQUEST["investigator"])?($i==$_REQUEST["investigator"]?" selected":""):"").">".$i."</option>\n";
			}
			print "</SELECT>\n";
		}
		if (sizeof($investigators)>0)
		{
			print "&nbsp;|&nbsp;<a href=\"javascript:show_h.action='show_other_cases.php'; show_h.submit();\">Показать дела следователя</a> <SELECT ONCHANGE=\"show_h.investigator.value=this.value; show_h.action='show_other_cases.php'; show_h.submit();\">";
			foreach ($investigators as $i) {
				print "<option value=\"".$i."\"".(isset($_REQUEST["investigator"])?($i==$_REQUEST["investigator"]?" selected":""):"").">".$i."</option>\n";
			}
			print "</SELECT>\n";
		}
//
	}
	print "<FORM NAME=\"show_h\" METHOD=POST ACTION=\"\"><input type=\"hidden\" name=\"investigator\" value=\"".(isset($_REQUEST["investigator"])?$_REQUEST["investigator"]:$investigators[0])."\"><input type=\"hidden\" name=\"case_type\" value=1></FORM>";
print <<<END
</CENTER>
<HR>
END;
?>