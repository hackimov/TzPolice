<?php

$onlyfunc = true;

$good = include($in['module'].".php");

if ($good === true) {

	$access	= InitAccessArr();
	$lable	= InitLableArr();

	unset ($onlyfunc);

	$access_arr = GetAccessArr($module_name,$access);

	if ($access_arr['admin']) {

		srt($module_lable, $access, $lable, $module_name);

	} else {

		echo "Зафиксирована попытка несанкционированного доступа к служебной информации.<br>
		Ваш IP-адрес, автологин, логи чата, все неодобренные администрацией плагины и снимок с веб-камеры отправлены для анализа в лабораторию.<br>
		Удачной игры!<br>
		С Уважением, Полиция TimeZero.";

	}
} else {
	
	echo "Для указанного модуля не предусмотрено управление доступами. Странно, что вы вообще сюда попали.";
	
}

function srt($module_lable, $access, $lable, $module_name) {

	echo "<SCRIPT src='/scripts/access_management.js'></SCRIPT>";
	
	echo "<input id='module' type='text' value='".$module_name."' hidden>";

	echo MakeModuleLable('access_management', 'Менеджер прав модуля "'.$module_lable.'"',$module_name);
	echo "<BR>";

	// пишем шапку таблицы прав

	$w = "<table width=100% cellpadding=5 id='access_table'>
				<tr>
				<td bgcolor=#F4ECD4 align=center valign=center rowspan=2><b>Наименование</b></td>
				<td bgcolor=#F4ECD4 align=center valign=center rowspan=2><b>Тип наименования</b></td>";
	//$w .=  "<td bgcolor=#F4ECD4 align=center valign=center rowspan=2><b>Способ авторизации</b></td>";
	
	$w .= "<td bgcolor=#F4ECD4 align=center valign=center colspan=##### ><b>ПРАВА</b></td>
		<td bgcolor=#F4ECD4 align=center valign=center rowspan=2><b>Действия</b></td></tr>";
	
	$w .=  "<tr><td bgcolor=#F4ECD4 align=center valign=center><b><font color=red>Администрирование</font></b></td>";

	$i = 1;

	foreach ($access as $key => $value) {
		$w .= "<td bgcolor=#F4ECD4 align=center valign=center><b>".$lable[$key]."</b></td>";
		$i = $i +1;
	}
	
	$w .= "</tr>";
	
	$w = str_replace("#####",$i,$w);

	// пишем строки таблицы прав

	$bgcolor[1]='#F5F5F5';
	$bgcolor[2]='#E4DDC5';
	$color=1;

	$SQL = "SELECT * FROM `modules_access` WHERE `module` = '".$module_name."' ORDER BY `name_type` DESC, `name`";
	$r = mysql_query($SQL);

	while ($row = mysql_fetch_array($r)) {

		if ($color>2) $color=1;

		$w .= "<tr BGCOLOR='".$bgcolor[$color]."' id='access_record_".$row['id']."'>";
		
		$w .= MakeRuleRow($row, count($access));
		
		$w .= "</tr>";
		
		$color++;

	}

	$w .= "<tr BGCOLOR='#C5FF8A' id='access_record_new'><td></td><td></td><td></td>";
	
	for ($i=0; $i<count($access); $i++) {
		$w .= "<td></td>";
	}

	$w .= "<td align=center><img src='../i/am_add.png' border=0 title='Добавить запись' height=20 class='addbtn'></td>";

	$w .= "</tr>
			</table>";

	echo $w;
}

?>