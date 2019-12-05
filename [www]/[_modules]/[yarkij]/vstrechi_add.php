<?
//include ('connect.php');
include ('_modules/yarkij/db_lib.php');
?>

<form method="post">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td>Ссылка TimeZero</td><td><input name="tz_id"></td>
</tr>
<tr>
	<td>Тема</td><td><input name="v_name"></td>
</tr>
<tr>
	<td>Страна</td><td><input name="country"></td>
</tr>
<tr>
	<td>Город</td><td><input name="city"></td>
</tr>
<tr>
	<td>Дата(день)</td><td><select name="v_day">
		<?
			for($i=1; $i<=31; $i++)
			{
				echo "<option name=\"$i\"";
				if($i==date("d")) echo " selected";
				echo ">$i</option>";
			}
		?>
	</select></td>
</tr>
<tr>
	<td>Дата(месяц)</td><td><select name="v_month">
		<?
			for($i=1; $i<=12; $i++)
			{
				echo "<option name=\"$i\"";
				if($i==date("m")) echo " selected";
				echo ">$i</option>";
			}
		?>
	</select></td>
</tr>
<tr>
	<td>Дата(год)</td><td><select name="v_year">
		<?
			for($i=date("Y")-1; $i<=date("Y")+1; $i++)
			{
				echo "<option name=\"$i\"";
				if($i==date("Y")) echo " selected";
				echo ">$i</option>";
			}
		?>
	</select></td>
</tr>
<tr>
	<td>Автор</td><td><input name="v_author"></td>
</tr>
<tr>
	<td>Комментарий</td><td><textarea name="coment"></textarea></td>
</tr>
<tr>
	<td colspan="2" align="right"><input type="submit"></td>
</tr>
</table>
<input type="hidden" name="go_add" value="1">
</form>
<?
if($go_add)
{
	$city = addslashes(stripslashes(strip_tags($_REQUEST[city])));
	$country = addslashes(stripslashes(strip_tags($_REQUEST[country])));
	$v_name = addslashes(stripslashes(strip_tags($_REQUEST[v_name])));
	$v_author = addslashes(stripslashes(strip_tags($_REQUEST[v_author])));
	$coment = addslashes(stripslashes(strip_tags($_REQUEST[coment])));
	$tz_id = addslashes(stripslashes(strip_tags($_REQUEST[tz_id])));

	$start = strpos($tz_id, "?a=Z&c=")+7;
	$end = strpos($tz_id, "\"", $start);
	$len = $end - $start;
	$tz_id = 0 + substr($tz_id,$start,$len);

	sql("insert into vstrechi (city, country, v_name, v_author, coment, v_date, v_new, tz_id)
		 values('$city','$country','$v_name','$v_author','$coment', ".mktime(0, 0, 0, $v_month, $v_day, $v_year).", 1, $tz_id)");

}

?>