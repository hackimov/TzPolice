<?php
require('../functions.php');
require('../auth.php');
require ("fees_function.php");
/**
 * @author deadbeef
 * @copyright 2011
 */

$query = "SELECT * FROM `tzpolice_fees` WHERE `payed` < `summa` AND `prison`='0' LIMIT 40,5";
$res = mysql_query($query);
while ($row = mysql_fetch_array($res))
	{
		$message = "��������� ���������!\r�� ����������� �������� �� ����� �� �������� ��������� � ���������� ��� ������. ���� ��������� � ������ ��������� ��� ���� �������� - ������ ��������������� ��� ���������\r\r".date("d.m.Y H:i", $row['time'])." ��� ��� ������� ����� �� ����� ".$row['summa']." ��, �� ������ ������ �������� ".$row['payed']." ��. ������� ������: ".$row['text'].". ������ ������ ������ ���� ����������� ����� �������� ����� ������ �� ��������� [Terminal 02] � ������� ����� � ������� ��������� ����������. ���� � ��� �������� ������� �� ������ ������� ��������� - ���������� � ��������� [LEO-OO] � ���� ����.\r\r�������� ��������� �� ��������� ����������, ��-����� ������� ����� �������.";
		$q = "SELECT `name` FROM `tzpolice_tz_users` WHERE `id`='".$row['name_id']."' LIMIT 1;";
		$r = mysql_query($q);
		$r2 = mysql_fetch_array($r);
		fees_send_telegramm($r2['name'], $message);
		echo ($r2['name']." ".$message."<hr>");
		//fees_send_telegramm('������ ����', $message);
	}

?>