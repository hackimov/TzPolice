<?
require("../_modules/functions.php");
require("../_modules/auth.php");
?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<head>
  <title>You're not supposed to see this =)</title>
<LINK href="../_modules/tzpol_css.css" rel="stylesheet" type="text/css">
<?include("../_modules/java.php")?>
</head>
<body bgcolor="#EBDFB7" text="#455600" alink="#0D3AB4" link="#0D3AB4" vlink="#0D3AB4">
<table width="90%"  border="0" align="center" cellpadding="3" cellspacing="2">
  <tr>
    <td align="center">
    <form name="state_request" method="post" action="">
      ���:
          <input type="text" name="nick">
          <input type="submit" name="Submit" value="����������">
    </form>
<? if (isset($_REQUEST['nick']))
//error_reporting(E_ALL);
	{
		$mark[0] = "<font color='green'>����, �������.</font>";
		$mark[1] = "����� � �������� - ������ ������ ������ ��������� ���. �����.";
		$mark[2] = "����� � �������� - ��������� ������ ������ ������.";
		$mark[3] = "<font color='red'>����� � �������, ��������� ������ �������, ������������� ���� ";
	        $mark[80] = "<font color='red'>����� � �������, �������� �������� ���������������� ���������</font>";
		$mark[90] = "<font color='red'>����� � �������, ����������� ���������, �������</font>";
		$mark[100] = "<font color='red'>�������� �� ��������, �� �������� ����� �� �������� ���������</font>";
		$mark[110] = "<font color='red'>����� � �������, ����������� ���������, �����</font>";
        	$urgency[0] = "�������";
		$urgency[1] = "<b>������� 12 �����</b>";
		$urgency[2] = "<b>������� 1 ���</b>";
		$query = "SELECT `result`, `term`, `time1`, `time2`, `status`, `payed`, `urgent` FROM `law_checks` WHERE `nick` = '".strip_tags($_REQUEST['nick'])."' ORDER BY `time1` DESC LIMIT 1;";
        $rs = mysql_query($query) or die (mysql_error());
        if (mysql_num_rows($rs) > 0)
        	{
		        list($res, $term, $time1, $time2, $status, $payed, $urg) = mysql_fetch_row($rs);
                if ($status < 100)
		        	{
        		    	if ($payed == 0) {$st = "������� ������������� �������";}
            			else {$st = "��������� � ������� �� ��������";}
		            }
                else
                	{
	                    if ($res == 3)
    	                	{
			                    $st = $mark[$res]."<b>".$term."</b> �����.</font>";
            	            }
                	    else
                    		{
                        		$st = $mark[$res];
	                        }
                    }
                if ($time2 > $time1)
                	{
                    	$time = $time2;
                    }
                else
                	{
                    	$time = $time1;
                    }
                echo ("<b>".strip_tags($_REQUEST['nick'])."</b><br>");
                echo ("������: ".$st."<br>");
                echo ("����: ".date("d.m.Y, H:i", $time)."<br>");
                if (AuthStatus==1 && (substr_count(AuthUserRestrAccess, "law_check") > 0 || substr_count(AuthUserRestrAccess, "law_control") > 0))
					{
		                echo ("<i>���������: ".$urgency[$urg]."</i><br>");
                    }

            }
        else
        	{
            	echo ("������ �� �������� ��������� <b>".strip_tags($_REQUEST['nick'])."</b> �� �������.");
            }
    }
?>

    </td>
  </tr>
</table>
</body>
</html>