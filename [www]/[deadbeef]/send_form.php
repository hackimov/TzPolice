<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Untitled Document</title>
<style type="text/css">
<!--
.table {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #003366;
}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>
</head>
<body>
<?
error_reporting(0);
//error_reporting(E_ALL);
$hotels['inturist1'] = "��������, �����������";
$hotels['inturist2'] = "��������, �����������";
$hotels['inturist3'] = "��������, ��������, �����������";
$hotels['inturist4'] = "��������, ����";
$hotels['turist1'] = "������, �����������";
$hotels['turist2'] = "������, �����������";
$hotels['turist3'] = "������, ��������";
$hotels['turist4'] = "������, ����";
$hotels['elbrus1'] = "�������, �����������";
$hotels['elbrus2'] = "�������, �����������";
$hotels['elbrus3'] = "�������, ���� �����������";
$hotels['elbrus4'] = "�������, ���� �����������";
if($_REQUEST['do'] == "send")
	{
		$message = "��������������� �����\n\n******************************\n\n";
        $message .= "�.�.�.: ".$_REQUEST['surname']." ".$_REQUEST['name']." ".$_REQUEST['fath_name']."\n\n";
        $message .= "�����������: ".$_REQUEST['org']."\n".$_REQUEST['org_address']."\n\n";
        $message .= "���.: ".$_REQUEST['phone']."\n e-mail: ".$_REQUEST['mail']."\n ����: ".$_REQUEST['fax']."\n\n\n";
		if ($_REQUEST['a15min']) {$message .= "������ �� ��������� ���������: ".$_REQUEST['a15min_title']."\n";}
		if ($_REQUEST['a10min']) {$message .= "��������� �� ������� �����: ".$_REQUEST['a10min_title']."\n";}
		if ($_REQUEST['need_soft']) {$message .= "��������� ��: ".$_REQUEST['soft_needed']."\n";}
        if ($_REQUEST['want_hotel']) {$message .= "\n\n ����� ������������� ����� � ���������: ".$hotels[$_REQUEST['radiobutton']]."\n\n";}
        $message .= "\n ���� �������: ".$_REQUEST['arrive_date']."\n ����� �������: ".$_REQUEST['arrive_place']."\n ���� �������: ".$_REQUEST['leave_date']."\n ����� �������: ".$_REQUEST['leave_place']."\n ������ ����������: ".date('d.m.Y, H:i')."\n******************************";


// In case any of our lines are larger than 70 characters, we should use wordwrap()
$message = wordwrap($message, 70);
$headers = 'From: webmaster@tzpolice.ru' . "\r\n" .
   'Reply-To: noreply@tzpolice.ru' . "\r\n" .
   'X-Mailer: PHP/' . phpversion() . "\r\n" .
   'Content-Type: text/plain; charset=windows-1251';
// Send
mail('deti@stv.runnet.ru', '����������� ���������� 21-22 ��������', $message, $headers);
//mail('deadbeef@tzpolice.ru', '����������� ���������� 21-22 ��������', $message, $headers);
echo ("<div align='center' class='table'><b>�������!</b><br>���� ������ ����������.<br><br><br></div>");
    }

?>
<br><br>
<center>
<table width="550" border="0" cellpadding="0" cellspacing="0" class="table">
	<tr><td align="center"><form id="form1" name="form1" method="post" action="?do=send">
		<p><strong>���������������  �����</strong><br />
			��������� ��������������� ������-������������  �����������<br />
			<em>��������� � ���� � ��������������  ������������ �������:</em><br />
			<em>��������, �������, ������������</em><br />
			<strong>21-22  �������� 2006 ����</strong>		</p>
		<p>&nbsp;</p>
		<table width="450" border="0" cellpadding="0" cellspacing="0" class="table">
	<tr><td>�������</td><td><input name="surname" type="text" id="surname" /></td></tr><tr><td>���</td><td><input name="name" type="text" id="name" /></td></tr><tr><td>��������</td><td><input name="fath_name" type="text" id="fath_name" /></td></tr><tr><td>�����������</td><td><input name="org" type="text" id="org" /></td></tr><tr><td>����� ����������� </td><td><textarea name="org_address" rows="3" wrap="virtual" id="org_address"></textarea></td></tr><tr><td>���������� ������� </td><td><input name="phone" type="text" id="phone" /></td></tr>
				<tr>
					<td>e-mail</td>
					<td><input name="mail" type="text" id="mail" /></td>
				</tr>
				<tr>
					<td>����</td>
					<td><input name="fax" type="text" id="fax" /></td>
				</tr>
			</table>
				<b><br />
				����� �������</b> <br />
				<table width="450" border="0" cellpadding="0" cellspacing="0" class="table">
					<tr>
						<td><input name="a15min" type="checkbox" id="a15min" value="a15min" />
							������ �� ��������� ��������� (15 ���.). ���� �������: </td>
						<td><input name="a15min_title" type="text" id="a15min_title" /></td>
					</tr>
					<tr>
						<td><input name="a10min" type="checkbox" id="a10min" value="a10min" />
							��������� �� ������� ����� (�� 10 ���.). ���� ���������: </td>
						<td><input name="a10min_title" type="text" id="a10min_title" /></td>
					</tr>
					<tr>
						<td><input name="need_soft" type="checkbox" id="need_soft" value="need_soft" />
							��� ������� ��������� ��������� ����������� �����������:</td>
						<td><textarea name="soft_needed" rows="5" wrap="virtual" id="soft_needed"></textarea></td>
					</tr>
				</table>
				<br />
				<br />
				<b>
				<input name="want_hotel" type="checkbox" id="want_hotel" value="want_hotel" />
				����� ������������� ��� ����� � ���������</b>:
				<br />
				<br />
				<p><strong>��������� ���������, ��. �. ������, 42. </strong><br />
						<strong>���. ��������������:  946-946.</strong><br />
					���.������, ����� � �����:  ������-�����, ������, ����� �������, ����, �������, ������, ����, ��������,  �����. ������� ������ � ���������. ����� � 25% �� ��������� ������, �� ������������  �������, �� 10-14 ����.</p>
				<table border="1" cellpadding="3" cellspacing="1" bordercolor="#333333" class="table">
					<tr>
						<td width="207" valign="top"><p align="center">��� ������</p></td>
						<td width="207" valign="top"><p align="center">��������� ����� (�    ���.)</p></td>
						<td width="180" valign="top"><p align="center">�����</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>�����������</p></td>
						<td width="207" valign="top"><p align="center">950</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="inturist1" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>�����������</p></td>
						<td width="207" valign="top"><p align="center">1200</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="inturist2" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>��������, �����������</p></td>
						<td width="207" valign="top"><p align="center">1500</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="inturist3" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>����</p></td>
						<td width="207" valign="top"><p align="center">�� 2800 �� 3200</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="inturist4" />
						</p></td>
					</tr>
				</table>
				<p><strong>��������� �������,  ��. ������, 273. </strong><br />
					<strong>���. ��������������:  36-00-04</strong></p>
				<p>���. ������: ����, ��������,  ��������. ����� � 30% �� ��������� ������, �� 15 ����.<br />
				</p>
				<table border="1" cellpadding="3" cellspacing="1" bordercolor="#333333" class="table">
					<tr>
						<td width="207" valign="top"><p align="center">��� ������</p></td>
						<td width="207" valign="top"><p align="center">���������� ����� (� ����.)</p></td>
						<td width="180" valign="top"><p align="center">�����</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>�����������</p></td>
						<td width="207" valign="top"><p align="center">�� 497 �� 907</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="turist1" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>�����������</p></td>
						<td width="207" valign="top"><p align="center">�� 760 �� 1200</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="turist2" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>��������</p></td>
						<td width="207" valign="top"><p align="center">2300</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="turist3" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>����</p></td>
						<td width="207" valign="top"><p align="center">3500</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="turist4" />
						</p></td>
					</tr>
				</table>
				<br />
				<p><strong>��������� ��������,  ��.��������, 43. </strong><br />
						<strong>���. ��������������:  27-04-00.</strong></p>
				<p>����� � 25% �� ��������� ������,  ���������� ����������� ����� �� ����������� (�������� 10 ����). </p>
				<table border="1" cellpadding="3" cellspacing="1" bordercolor="#333333" class="table">
					<tr>
						<td width="207" valign="top"><p align="center">��� ������</p></td>
						<td width="207" valign="top"><p align="center">��������� ����� (�    ���.)</p></td>
						<td width="180" valign="top"><p align="center">�����</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>�����������</p></td>
						<td width="207" valign="top"><p align="center">�� 230 �� 1000</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="elbrus1" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>�����������</p></td>
						<td width="207" valign="top"><p align="center">�� 420 �� 480</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="elbrus2" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>���� �����������</p></td>
						<td width="207" valign="top"><p align="center">1000</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="elbrus3" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>���� �����������</p></td>
						<td width="207" valign="top"><p align="center">1200</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="elbrus4" />
						</p></td>
					</tr>
				</table>
				<br />
				<br />
				<table width="560" border="0" cellpadding="0" cellspacing="0" class="table">
					<tr>
						<td width="208" valign="top"><p>����    �������</p></td>
						<td width="352" valign="top"><p>
							<input name="arrive_date" type="text" id="arrive_date" />
						</p></td>
					</tr>
					<tr>
						<td width="208" valign="top"><p>�����    � ����� ��������</p></td>
						<td width="352" valign="top"><p>
							<input name="arrive_place" type="text" id="arrive_place" />
						</p></td>
					</tr>
					<tr>
						<td width="208" valign="top"><p>����    �������</p></td>
						<td width="352" valign="top"><p>
							<input name="leave_date" type="text" id="leave_date" />
						</p></td>
					</tr>
					<tr>
						<td width="208" valign="top"><p>�����    � ����� �������</p></td>
						<td width="352" valign="top"><p>
							<input name="leave_place" type="text" id="leave_place" />
						</p></td>
					</tr>
				</table>
				<p align="left"><i>* ������������ ����� � ���������  ����� �������������� ������ ��� ��������� ����������� ��������������� �����.<br />
					������  �� �������� ������ ������ ����������� ������� �� ������.<br />
				��� ����������� ������� � ��������  ���������� ����������� ������ ����, ����� � ����� �������� � ������� ������  �������� ���������������.</i></p>
				<input type="submit" name="Submit" value="���������" />
				<br />
		</form>
		</td>
	</tr>
</table>
</center>
</body>
</html>