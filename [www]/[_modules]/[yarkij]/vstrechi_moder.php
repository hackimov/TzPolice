<?
//include ('connect.php');
include ('_modules/yarkij/db_lib.php');

if($_REQUEST['go_redakt'] && $_REQUEST['v_id'])
{
	sql("update vstrechi set city='$_REQUEST[city]', country='$_REQUEST[$country]', v_name='$_REQUEST[v_name]', v_author='$_REQUEST[v_author]',
		 coment='$_REQUEST[coment]', v_date=".mktime(0, 0, 0, $_REQUEST[v_month], $_REQUEST[v_day], $_REQUEST[v_year]).", v_new=$_REQUEST[v_new]  where id=$_REQUEST[v_id]");

}

$q = sql("select * from vstrechi where v_new=1");

echo '<table cellpadding="0" cellspacing="0" border="0">';
while($m_v_new = sql_a($q)){
  	echo "<tr>
	  		<td><a href=\"http://www.timezero.ru/cgi-bin/forum.pl?a=Z&c=$m_v_new[tz_id]\" target=_blank>
			  	".html_entity_decode($m_v_new[v_name],ENT_QUOTES, 'cp1251')."</a></td>
	  		<td>".html_entity_decode($m_v_new[v_author],ENT_QUOTES, 'cp1251')."</td>
	  		<td><a href=\"?act=meeting_moder&v_id=".$m_v_new[id]."\">Отредактировать</td>
	  	</tr>";
  } // while
echo '</table>';

if($_REQUEST['v_id'])
	$m_vstrecha = sql_a(sql("select * from vstrechi where id=".$_REQUEST['v_id']." and v_new=1"));
if(count($m_vstrecha))
{
?>
<p>
<form method="post">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td>Тема</td><td><input name="v_name" value="<?=$m_vstrecha["v_name"]?>"></td>
</tr>
<tr>
	<td>Страна</td><td><input name="country" value="<?=$m_vstrecha["country"]?>"></td>
</tr>
<tr>
	<td>Город</td><td><input name="city" value="<?=$m_vstrecha["city"]?>"></td>
</tr>
<tr>
	<td>Дата(день)</td><td><select name="v_day">
		<?
			for($i=1; $i<=31; $i++)
			{
				echo "<option name=\"$i\"";
				if($i==date("d",$m_vstrecha["v_date"])) echo " selected";
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
				if($i==date("m",$m_vstrecha["v_date"])) echo " selected";
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
				if($i==date("Y",$m_vstrecha["v_date"])) echo " selected";
				echo ">$i</option>";
			}
		?>
	</select></td>
</tr>
<tr>
	<td>Автор</td><td><input name="v_author" value="<?=$m_vstrecha["v_author"]?>"></td>
</tr>
<tr>
	<td>Комментарий</td><td><textarea name="coment"><?=nl2br($m_vstrecha["coment"])?></textarea></td>
</tr>
<tr>
	<td>Проверенно</td><td><input type="checkbox" name="v_new" value="0"></td>
</tr>
<tr>
	<td colspan="2" align="right"><input type="submit"></td>
</tr>
</table>
<input type="hidden" name="go_redakt" value="1">
</form>
<?}?>

