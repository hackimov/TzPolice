<?php
//	$ctype = array();
//	$ctype[1] = "����";
//	$ctype[2] = "��";
	print "<HR>\n<CENTER>\n";
	print "<a href=\"javascript:show_h.action='show_free_cases.php';show_h.case_type.value=".(isset($_REQUEST["case_type"])?$_REQUEST["case_type"]:"1")."; show_h.submit();\">�������� ��������� ����</a>";
	print "<SELECT ONCHANGE=\"show_h.case_type.value=this.value; show_h.action='show_free_cases.php'; show_h.submit();\">";
	for ($i=1;$i<4;$i++)
	{
		print "<OPTION value=".$i." ".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]==$i?"selected":""):"").">".$ctype[$i]."</OPTION>";
	}
	print "<OPTION value=13 ".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]==13?"selected":""):"").">".$ctype[13]."</OPTION>";
	print "</SELECT>&nbsp;|&nbsp;";
	print <<<START
	<a href="show_own_cases.php">�������� ����������� ����</a>&nbsp;|&nbsp;
	<a href="my_stat.php">�������� ���� ����������</a>
START;
	print "&nbsp;|&nbsp;\n";
	print "<a href=\"javascript:show_h.action='warn_cases.php';show_h.case_type.value=".(isset($_REQUEST["case_type"])?$_REQUEST["case_type"]:"1")."; show_h.submit();\">���� &gt;20 ���� �</a>";
	print "<SELECT ONCHANGE=\"show_h.case_type.value=this.value; show_h.action='warn_cases.php'; show_h.submit();\">\n";
	for ($i=1;$i<4;$i++)
	{
		print "<OPTION value=".$i." ".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]==$i?"selected":""):"").">".$ctype[$i]."</OPTION>";
	}
	print "<OPTION value=13 ".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]==13?"selected":""):"").">".$ctype[13]."</OPTION>";
	print "</SELECT><br><br>\n";
	if (Abs(AccessLevel)&(AccessAdminOR))
	{
		print "&nbsp;|&nbsp;\n";
		print "<a href=\"javascript:show_h.action='all_stat.php';show_h.case_type.value=".(isset($_REQUEST["case_type"])?$_REQUEST["case_type"]:"1")."; show_h.submit();\">����� ����������</a>";
		print "<SELECT ONCHANGE=\"show_h.case_type.value=this.value; show_h.action='all_stat.php'; show_h.submit();\">\n";
		for ($i=1;$i<4;$i++)
		{
			print "<OPTION value=".$i." ".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]==$i?"selected":""):"").">".$ctype[$i]."</OPTION>";
		}
		print "<OPTION value=13 ".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]==13?"selected":""):"").">".$ctype[13]."</OPTION>";
		print "</SELECT>\n";
		if (sizeof($GLOBALS["investigators"])>0)
		{
			print "<a href=\"javascript:show_h.action='other_stat.php'; show_h.submit();\">�������� ���������� �����������</a> <SELECT ONCHANGE=\"show_h.investigator.value=this.value; show_h.action='other_stat.php'; show_h.submit();\">";
			foreach ($GLOBALS["investigators"] as $i) {
				print "<option value=\"".$i."\"".(isset($_REQUEST["investigator"])?($i==$_REQUEST["investigator"]?" selected":""):"").">".$i."</option>\n";
			}
			print "</SELECT>\n";
		}
		if (sizeof($GLOBALS["investigators"])>0)
		{
			print "&nbsp;|&nbsp;<a href=\"javascript:show_h.action='show_other_cases.php'; show_h.submit();\">�������� ���� �����������</a> <SELECT ONCHANGE=\"show_h.investigator.value=this.value; show_h.action='show_other_cases.php'; show_h.submit();\">";
			foreach ($GLOBALS["investigators"] as $i) {
				print "<option value=\"".$i."\"".(isset($_REQUEST["investigator"])?($i==$_REQUEST["investigator"]?" selected":""):"").">".$i."</option>\n";
			}
			print "</SELECT>\n";
		}
	}
print <<<END
</CENTER>
END;
?>