<?
// ������ �������� 13.06.2014 �������� ���. ������� [Supervisor]
exit();
?>

<h1>������� ��������</h1>
<?
include ("_modules/prison_rating_table.php");
?>
</center>
<font size="-2">
<b>��������!</b><br><br>
������ 3 ����� �������� ���������:
<br>
1 ����� - 10% �� ��������� �� ���� ��������
<br>
2 � 3 ����� - 5% �� ��������� �� ���� ��������.
</font>
<br><br>
<b>��� �������� �����</b> - 3 ������ ���������� �� ��� ������� ��������:
<br><br>
<?
include ("_modules/prison_rating_top.php");
?>

<script>
var archWin = 0;
function archWindow()
	{
		if(archWin)
			{
				if(!archWin.closed) archWin.close();
			}
		win_left = 150;
		win_top = 150;
		archWin = open('/direct_call/prison_rating_archive.php', 'archive', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=both,resizable=no,width=450,height=450,scrollbars=1,left='+win_left+', top='+win_top+',screenX='+win_left+',screenY='+win_top);
	}
</script>
<hr size="1">
<br><br>
<a href="#; return false" onClick="javascript:archWindow()">����� ��������</a>