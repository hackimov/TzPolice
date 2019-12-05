<?php
    function form_print($where)
    {
		$today=getdate();
		$months=array("������","�������","����","������","���","����","����","������","��������","�������","������","�������");
		print "<input type=text value=\"".(isset($_REQUEST["day_date_".$where])?$_REQUEST["day_date_".$where]:($where=="to"?date("t"):"01"))."\" size=1 maxlength=2 name=\"day_date_".$where."\"><select name=\"month_date_".$where."\">";
		for ($i=1;$i<=12;$i++)
		{
			print "<option value=".sprintf("%02d",$i).(isset($_REQUEST["month_date_".$where])?($_REQUEST["month_date_".$where]==$i?" selected":""):($today["mon"]==$i?" selected":"")).">".$months[$i-1]."</option>";
		}
		print "</select><select name=\"year_date_".$where."\">";
		for ($i=2005;$i<=date('Y');$i++)
		{
			print "<option value=".$i.(isset($_REQUEST["year_date_".$where])?($_REQUEST["year_date_".$where]==$i?" selected":""):($today["year"]==$i?" selected":"")).">".$i."</option>";
		}
		print "</select>";
	}
    function print_period($from)
    {
		$error_found=0;
		if (isset($_REQUEST["day_date_from"]))
		{
			if (!is_numeric($_REQUEST["day_date_from"]))
			{
				$error_found=1;
				print "<font color=\"RED\">������</font>: ".$_REQUEST["day_date_from"]." �� �������� ������!<br>";
			}
			else
			{
				if (!checkdate($_REQUEST["month_date_from"],$_REQUEST["day_date_from"],$_REQUEST["year_date_from"]))
				{
					$error_found=1;
					print "<font color=\"RED\">������</font>: ".$_REQUEST["day_date_from"]." ������ ������������� ����� ���� � ���� ������!<br>";
				}
			}
			if (!is_numeric($_REQUEST["day_date_to"]))
			{
				$error_found=1;
				print "<font color=\"RED\">������</font>: ".$_REQUEST["day_date_to"]." �� �������� ������!<br>";
			}
			else
			{
				if (!checkdate($_REQUEST["month_date_to"],$_REQUEST["day_date_to"],$_REQUEST["year_date_to"]))
				{
					$error_found=1;
					print "<font color=\"RED\">������</font>: ".$_REQUEST["day_date_to"]." ������ ������������� ����� ���� � ���� ������!<br>";
				}
			}
		}
		print "<FORM METHOD=POST ACTION=\"$from.php\" name=\"$from\">";
		if (isset($_REQUEST["case_type"]))
		{
			print "<input type=\"hidden\" name=\"case_type\" value=".$_REQUEST["case_type"].">";
		}
		if (isset($_REQUEST["investigator"]))
		{
			print "<input type=\"hidden\" name=\"investigator\" value=\"".$_REQUEST["investigator"]."\">";
		}
		print "<center>������&nbsp;�";
		form_print("from");
		print "&nbsp;��&nbsp;";
		form_print("to");
		print "</center><br>";
		print "<center><input type=\"submit\" value=\"������������� ����������\"><center></FORM>";
		if ((!isset($_REQUEST["day_date_from"]))||$error_found)
		{
			include "_ORfooter.php";
			print"</BODY>";
			die;
		}
		print "<hr>";
    }
    function print_stat_time($timestamp,$start_arg="")
    {
		if ($timestamp>0)
		{
			$date_format=getdate($timestamp);
			print $start_arg.$date_format["yday"]."��. ".$date_format["hours"]."�. ".$date_format["minutes"]."�.<br>";
		}
		else
		{
			print $start_arg." 0��. 00�. 00�.<br>";
		}
    }
?>