<?
require("functions.php");
require("auth.php");
if ($_REQUEST['step'] == 1)
	{
        $error = "";
		$urg[0] = 10;
        $urg[1] = 50;
        $urg[2] = 100;
        $log_string = uencode($_REQUEST['log_string'], "w");
        $req_nick = uencode($_REQUEST['nickname'], "w");
        $cop_nick = uencode($_REQUEST['cop_nick'], "w");
        if (strlen($req_nick) < 3)
        	{
				$error .= "������� ��� ���!<br><br>";
            }
        $req_reas = $_REQUEST['reason'];
    	$log = ">>>start<<< ".$log_string.", >>>end<<<";
        $log = str_replace("����� �� �����", "", $log);
		$log_time = sub($log, "start<<< ", " ��������");
        $log_who = sub($log, "�������� \\'", "\\' �������");
        $log_where = sub($log, "�� ���� ", " ");
        if ($log_where == 0)
        	{
				$log_where = sub($log, "�� ���� ", " ");
            }
        $log_quant = sub($log, "�� �����: ", ",");
        $req_log = $log_time."|||".$log_who."|||".$log_where."|||".$log_quant;
        if ($log_quant == 10)
        	{
				$req_urg = 0;
            }
        elseif ($log_quant == 50)
        	{
				$req_urg = 1;
            }
        elseif ($log_quant == 100)
        	{
				$req_urg = 2;
            }
        else
        	{
                $error .= "��������� ����� ������� �� ������������� ��������� �� �� ���� �� �����, ��������������� ������������ ������� �������<br><br>���������, ��� �� ������� ������ ������ �� ����� ������. ����������, ��� ���������� �� ������� �� ����� ������������, �� ���������� ��� ������������� ������� ����<br><br>";
            }
        if ($req_urg == 2 && strlen($cop_nick) < 4)
        	{
                $error .= "�������������� ������������ � ����������� ������������� ������ � ���������� ������� ��������. ���� �������������� ��� ���������� - �� �������� ������� ��� ���������� � ��������������� ���� ������<br><br>";
            }
        $userinfo = GetUserInfo($req_nick);
        $query = "SELECT `id` FROM `law_checks` WHERE `payment` = '".$req_log."' LIMIT 1;";
		$double_req = time() - 259200;
        $tmp = mysql_query($query);
        if (mysql_num_rows($tmp) > 0)
        	{
				$error .= "��������� ������ ��� ��� ������ � ������������<br><br>";
            }
        $query = "SELECT `id` FROM `law_checks` WHERE `nick` = '".$req_nick."' AND `time1` > '".$double_req."' AND (`result` < 1 OR `result` > 1 );";
        $tmp = mysql_query($query);
        if (mysql_num_rows($tmp) > 0)
			{
				$error .= "��������� �������� ��� ������� ������. ����� �������� ������ ������ �� ����� <b>3</b> ����<br><br>";
            }

        if ($userinfo["level"] > 0 && $userinfo["level"] < 4)
			{
            	$error .= "�������� ������ �� �������� ����� ��������� ������� � <font color='red'>����������</font> ������<br><br>";
            }
        if ($log_where !== "84257")
			{
            	$error .= "�� �������� ������ �� �������� ����. ������ �� �������� ����������� �� ���� <b>84257</b><br><br>";
            }
        if ($log_quant < $urg[$_REQUEST['urgency']])
        	{
            	$error .= "�� �������� �������� �����. ������ �� ��������� ��� �������� ���������� <b>".$urg[$_REQUEST['urgency']]."</b> ������ �����<br><br>";
            }
        $req_type[0] = "";
        $req_type[1] = "";
        $req_type[2] = "";
        $req_type[$_REQUEST['urgency']] = " selected";
        $req_rs[0] = "";
        $req_rs[1] = "";
        $req_rs[2] = "";
        $req_rs[$_REQUEST['reason']] = " selected";
        $req_time = time();
        if ($error == "")
        	{
            	$query = "INSERT INTO `law_checks` (`id`, `nick`, `urgent`, `reason`, `status`, `result`, `term`,
                	`time1`, `time2`, `processed_by`, `checked_by`, `payed`, `payment`, `urg_cop`)
                    VALUES
                    ('', '".$req_nick."', '".$req_urg."', '".$req_reas."', '0', '0', '0', '".$req_time."', '', '', '', '0', '".$req_log."', '".$cop_nick."');";
				mysql_query($query) or die(mysql_error());
                echo ("<br><center><b>�������. ���� ������ �������.</b></center>");
            }
        else
        	{
            	$old_log = str_replace("\\", "", $log_string);
            	echo ("<br><center><b><font color='red' size='+1'>������</font><br><br><br>".$error."</b><br><br>");
                echo ("<form name='lr'>
                <table width='90%'  border='0' align='center' cellpadding='5' cellspacing='0'>
				  <tr>
				    <td>
				      ���:
				      <select name='urgency' onChange='urg(this)'>
				        <option value='0'".$req_type[0].">�������</option>
				        <option value='1'".$req_type[1].">������� (12 �����)</option>
        <option value='2'".$req_type[2].">������� (1 ���)</option>
      </select></td>
    <td>
      �������:
      <select name='reason'>
        <option value='0'".$req_rs[0].">���������� � ����</option>
        <option value='1'".$req_rs[1].">��������� �����������</option>
        <option value='2'".$req_rs[2].">������� ������������</option>
      </select></td>
    <td>
      ���:
      <input type='text' name='nickname' value='".$req_nick."'>
</td>
  </tr>
</table>

<div id='urg'");
if ($cop_nick == 0) {$tmp = " selected";} else {$tmp = "";}
if ($_REQUEST['urgency'] < 2) {echo (" style='display:none'");}
echo (" align='center'>
      ��������� ��, � ������� ���������� �������������� � ��������:
      <select name='cop_nick'>
		<option value='0'".$tmp.">���</option>");
$SQL = 'SELECT name FROM sd_cops WHERE dept=18';
$result = mysql_query($SQL) or die (mysql_error());
  if (mysql_num_rows($result) > 0 ) {
     while (list($name) = mysql_fetch_row($result))
     	{
        	if ($cop_nick == $name) {$tmp = " selected";} else {$tmp = "";}
		    echo("<option value='".$name."'".$tmp.">".$name."</option>");
        }
  	}
echo ("</select>
</div>
<div align='center'><br>
	������ �� ����� ������ � ��������:<br>
    <textarea name='log_string' cols='90' rows='3' wrap='VIRTUAL'>".$old_log."</textarea>
    <br>
    <input type='button' value='���������' onClick='lr_form_subm()'>
</div>
</form>");
            }
    }
//echo ("<pre>");
//print_r($_REQUEST);
//echo ("</pre>");
?>