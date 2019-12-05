<?php

require_once('_modules/functions.php');
require_once('_modules/auth.php');

if (AuthStatus==1 && AuthUserGroup == 100) {
	
	$SQL = "SELECT * FROM site_users WHERE user_group > 1 OR AccessLevel > 1 OR restricted_area <> 0";	
	$query = mysql_query($SQL);
	
	echo("<table border=1><tr><td>Логин</td><td>Уровень доступа</td><td>Набор прав</td><td>Дополнительные права</td></tr>");
	
	
	while ($row = mysql_fetch_array($query)) {
		
		$anc = "<a href='?act=user_info&UserId=".$row['id']."' target='_blank'>".$row['user_name']."</a>";
		echo("<tr><td>$anc</td><td>".$row['user_group']."</td><td>".$row['AccessLevel']."</td><td>".$row['restricted_area']."</td></tr>");
	
	}

	echo("</table>");
	
}


?>