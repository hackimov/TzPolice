<?
//include ('connect.php');
include ('_modules/yarkij/db_lib.php');
?>

<table cellpadding="5" cellspacing="5" border="0">
<tr>
	<td>Фильтр</td>
	<td>Страна</td>
	<td>Город</td>
	<td>Дата</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><select name="country">
		<option value="">---</option>
		<?
		$q = sql("select distinct(country) from vstrechi order by country asc");
		while($m_co = sql_a($q))
		{
			echo '<option value="'.$m_co['country'].'"';
			if($_REQUEST['country'] == $m_co['country'])
				echo ' selected';
			echo '>'.$m_co['country'].'</option>';
		}
		?>
		</select></td>
	<td><select name="city">
		<option value="">---</option>
		<?
		$q2 = sql("select distinct(city) from vstrechi order by city asc");
		while($m_ci = sql_a($q2))
		{
			echo '<option value="'.$m_ci['city'].'"';
			if($_REQUEST['city'] == $m_co['city'])
				echo ' selected';
			echo '>'.$m_co['city'].'</option>';
		}
		?>
		</select></td>
	<td><select name="date">
		<option value="">---</option>
		</select></td>
</tr>
</table>
<table cellpadding="0" cellspacing="5" border="0">
<tr>
	<td>Дата</td>
	<td>Страна</td>
	<td>Город</td>
	<td>Тема</td>
	<td>Автор</td>
</tr>

<?
	$sql = "select * from vstrechi ";
	if($_REQUEST['city'] || $_REQUEST['country'])
	{
		if($_REQUEST['city'] && $_REQUEST['country'])
			$sql .= 'where city like %'.$_REQUEST['city'].'% && country like %'.$_REQUEST['country'].'%';
		elseif($_REQUEST['city'])
			$sql .= 'where city like %'.$_REQUEST['city'].'%';
		else
			$sql .= 'where country like %'.$_REQUEST['country'].'%';

	}
	$sql .=" order by v_date asc";
	$q3 = sql($sql);
	while($m_v = sql_a($q3))
	{
		echo '<tr>';
			echo '<td>'.date("d-m-Y", $m_v['v_date']).'</td>';
			echo '<td>'.$m_v['country'].'</td>';
			echo '<td>'.$m_v['city'].'</td>';
			echo '<td><a href="http://www.timezero.ru/cgi-bin/forum.pl?a=Z&c='.$m_v['tz_id'].'" target=_blank>'.$m_v['v_name'].'</a></td>';
			echo '<td>'.$m_v['v_author'].'</td>';
		echo '</tr>';
	}
?>
</table>