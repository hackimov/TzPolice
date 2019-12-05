<?php

@$db=mysql_connect('192.168.253.6', 'tzpolice', 'DKobrA9zk7JRTxvQ');
mysql_select_db('tzpolice');
mysql_query('SET NAMES CP1251');

$querytext = "SELECT bl_nick, act, after_val FROM mp_black_list_journal ORDER BY date ASC";

$query = mysql_query($querytext);

while ($row = mysql_fetch_array($query)) {

	if ($row['act'] == 6 || $row['act'] == 9 || $row['act'] == 4) continue;
	
	$id = mb_strtoupper($row['bl_nick'], 'cp1251');
	
	$h4s[$id][nick] = $row['bl_nick'];
	
	if ($row['act'] == 2 || $row['act'] == 8) {
		
		$h4s[$id][is] = 0;
		$h4s[$id][us] = "";
		
	} else {
		
		$h4s[$id][is] = 1;
		$h4s[$id][us] = $row['after_val'];
		
	}
	
}

//print_r($h4s);

//exit();

echo "<table border=1><tr><td>Ник</td><td>Последнее условие выхода</td></tr>";

foreach($h4s as $key => $value) {
	
	if ($h4s[$key][is] == 0) continue;
	
	$querytext = "SELECT login FROM mp_black_list_persons WHERE login ='".$h4s[$key][nick]."'";
	$query = mysql_query($querytext);
	$count = mysql_num_rows($query);
	
	if ($count == 0) {
		
		echo "<tr><td>".$h4s[$key][nick]."</td><td>".$h4s[$key][us]."</td></tr>";
		
	}
	
	
}

echo "</table>";


//print_r($h4s);

?>