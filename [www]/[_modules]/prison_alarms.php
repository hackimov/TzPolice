<?php
$offset = $_REQUEST["day"];
$day = time();
$day = $day - (86400*$offset);
$dispday = date("d.m.Y",$day);
$file = "/home/sites/police/www/_modules/prison/".date("Ymd",$day).".txt";
if (is_file($file))
	{
    	$text = file_get_contents($file);
    }
else
	{
    	$text = "[b]����������� �� ���������� :yessir:[/b]";
    }
?>
<h1>���������� �� ������� <?=$dispday?></h1>
<form name="hist" action="" method="GET">
<input type="hidden" name="act" value="prison_alarm">
�������� ������ ��: <select name="day">
<option value="0" selected>�������</option>
<option value="1">�����</option>
<option value="2">���������</option>
<option value="3">3 ��� �����</option>
<option value="4">4 ��� �����</option>
<option value="5">5 ���� �����</option>
<option value="6">6 ���� �����</option>
<option value="7">������ �����</option>
</select>
<input type="submit" value="GO">
</form>
<hr>
<?
//$text2 =
ParseNews2($text);
//echo ($text2);
?>
<hr>
<font size="-2">����������� ��������� ����� �������� � ���������� �������� �� "���������", "����������", "����������" ��� "���������"</font>