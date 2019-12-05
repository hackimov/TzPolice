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
    	$text = "[b]Нарушителей не обнаружено :yessir:[/b]";
    }
?>
<h1>Нарушители на каторге <?=$dispday?></h1>
<form name="hist" action="" method="GET">
<input type="hidden" name="act" value="prison_alarm">
Показать список за: <select name="day">
<option value="0" selected>сегодня</option>
<option value="1">вчера</option>
<option value="2">позавчера</option>
<option value="3">3 дня назад</option>
<option value="4">4 дня назад</option>
<option value="5">5 дней назад</option>
<option value="6">6 дней назад</option>
<option value="7">неделю назад</option>
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
<font size="-2">Нарушителем считается любой персонаж с профессией отличной от "каторжник", "патрульный", "специалист" или "штурмовик"</font>