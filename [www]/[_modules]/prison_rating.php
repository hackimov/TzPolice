<?
// модуль отключен 13.06.2014 решением нач. полиции [Supervisor]
exit();
?>

<h1>Рейтинг каторжан</h1>
<?
include ("_modules/prison_rating_table.php");
?>
</center>
<font size="-2">
<b>Внимание!</b><br><br>
Первые 3 места являются призовыми:
<br>
1 место - 10% от собранных за день ресурсов
<br>
2 и 3 места - 5% от собранных за день ресурсов.
</font>
<br><br>
<b>Зал трудовой славы</b> - 3 лучших результата за всю историю рейтинга:
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
<a href="#; return false" onClick="javascript:archWindow()">Архив рейтинга</a>