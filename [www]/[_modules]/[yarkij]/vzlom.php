<?php
//include ('connect.php');
include ('_modules/yarkij/db_lib.php');

if(!($_REQUEST['show_vzlom'] || $_REQUEST['show_postr']))
{
if($_POST[go_add])
{
	sql("insert into vzlom (n_postr, ip_postr, n_vzlom, ip_vzlom, coment)
		values(\"$_POST[n_postr]\", \"$_POST[ip_postr]\", \"$_POST[n_vzlom]\", \"$_POST[ip_vzlom]\", \"$_POST[coment]\")");
	echo "Добавлено!!!";
}
?>
<style>
input{width: 150px;}
textarea{width:305px;}
</style>

<form action="" method="post">
<table cellpadding="0" cellspacing="5" border="0">
<tr>
	<td>&nbsp;</td>
	<td>Nick</td>
	<td>IP</td>
</tr>
<tr>
	<td>Пострадавший</td>
	<td><input type="text" name="n_postr"></td>
	<td><input type="text" name="ip_postr"></td>
</tr>
<tr>
	<td>Взломщик</td>
	<td><input type="text" name="n_vzlom"></td>
	<td><input type="text" name="ip_vzlom"></td>
</tr>
<tr>
	<td>Комментарий</td>
	<td colspan="2"><textarea name="coment"></textarea></td>
</tr>
<tr>
	<td colspan="3" align="right"><input type="submit"></td>
	<input type="hidden" name="go_add" value="1">
</tr>
</table>
</form>

<table cellpadding="5" cellspacing="0" border="0">
<tr>
	<td><a href="http://www.tzpolice.ru/?act=vzlom&show_vzlom=1" target="_blank">Взломщики</a></td>
	<td><a href="http://www.tzpolice.ru/?act=vzlom&show_postr=1" target="_blank">Пострадавшие</a></td>
</tr>
<tr>
<td>
<table cellpadding="5" cellspacing="0" border="1">
<?
$sql = sql("select n_postr, ip_postr, n_vzlom, ip_vzlom, coment, count(ip_vzlom) as kolvo from vzlom group by ip_vzlom order by kolvo desc limit 10");
while($m_tabl=sql_a($sql)){
	echo "<tr><td>$m_tabl[ip_vzlom]</td><td>$m_tabl[kolvo]</td></tr>";
} // while
?>
</table>
</td><td>
<table cellpadding="5" cellspacing="0" border="1">
<?
$sql = sql("select n_postr, ip_postr, n_vzlom, ip_vzlom, coment, count(ip_postr) as kolvo from vzlom group by ip_postr order by kolvo desc limit 10");
while($m_tabl=sql_a($sql)){
	echo "<tr><td>$m_tabl[ip_postr]</td><td>$m_tabl[kolvo]</td></tr>";
} // while
?>
</table>
</td>
</tr>
</table>
<?}
else{

?>
<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td></td>
</tr>
</table>
<?}?>