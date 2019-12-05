<?php
	//error_reporting(E_ALL);
	include "/home/sites/police/dbconn/dbconn_persistent.php";
	$SQL = "SELECT `id`, `file`, `nick` FROM `fotos_main` ORDER BY `id` LIMIT 150;";
	$r = mysql_query($SQL);
	while ($d = mysql_fetch_array($r)) {
		if (is_file('../i/fotos/'.$d['file'])) {
		//	echo ("She's alive!!!<br>");
			
		} else {
			$query = "DELETE FROM `fotos_main` WHERE `id` = '".$d['id']."' LIMIT 1;";
		//	echo $query;
		//	echo '<br>';
			mysql_query($query);
			$query = "SELECT * FROM `fotos_main` WHERE `nick` = '".$d['nick']."';";
		//	echo $query;
		//	echo '<br>';
			$f = mysql_query($query);
			if (mysql_num_rows($f) == 0) {
				$query = "DELETE FROM `fotos_users` WHERE `nick` = '".$d['nick']."' LIMIT 1;";
				echo ($query);
				mysql_query($query);
				echo ("<br>");
			}
		}
	}
	
?>