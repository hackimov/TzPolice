<?
	//error_reporting(E_ALL);
	require("/home/sites/police/www/_modules/auth.php");
	require("/home/sites/police/www/_modules/functions.php");
	error_reporting(E_ALL);
	
//	$query = "SELECT * FROM `site_users` WHERE `clan` = 'police' OR `clan` = 'Police Academy' OR `clan` = 'Financial Academy' OR `clan` = 'Military Police' OR `clan` = 'Tribunal';";
	$query = "SELECT * FROM `site_users` WHERE `clan` = 'police' OR `clan` = 'Police Academy' OR `clan` = 'Tribunal';";
	$res = mysql_query($query);
	while ($tmp = mysql_fetch_assoc($res)) {
		$new_lvl = intval($tmp['AccessLevel']);
		
		if ($tmp['clan'] == 'police') {
			//if (abs($tmp['AccessLevel']) & AccessShop) {
			//	$a = 1;
			//} else {
			//	$new_lvl = $new_lvl + AccessShop;
			//}
			
			if (abs($tmp['AccessLevel']) & AccessAuthUser) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccessAuthUser;
			}
			
			if (abs($tmp['AccessLevel']) & AtackReport) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AtackReport;
			}
			
			if (abs($tmp['AccessLevel']) & AccessForum) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccessForum;
			}
			
			if (abs($tmp['AccessLevel']) & AccessPolice) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccessPolice;
			}
			
			if (abs($tmp['AccessLevel']) & AccesInnerNews) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccesInnerNews;
			}
			
		} elseif($tmp['clan'] == 'Police Academy') {
			//if (abs($tmp['AccessLevel']) & AccessShop) {
			//	$a = 1;
			//} else {
			//	$new_lvl = $new_lvl + AccessShop;
			//}
			
			if (abs($tmp['AccessLevel']) & AccessAuthUser) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccessAuthUser;
			}
			
			if (abs($tmp['AccessLevel']) & AccessPolice) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccessPolice;
			}
			
			if (abs($tmp['AccessLevel']) & AccessForum) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccessForum;
			}
			
			if (abs($tmp['AccessLevel']) & AccesInnerNews) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccesInnerNews;
			}
			
		} elseif($tmp['clan'] == 'Financial Academy') {
			/*if (abs($tmp['AccessLevel']) & AccessShop) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccessShop;
			}
			
			if (abs($tmp['AccessLevel']) & AccessAuthUser) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccessAuthUser;
			}
			
			if (abs($tmp['AccessLevel']) & AccessForum) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccessForum;
			}
			
			if (abs($tmp['AccessLevel']) & AccessForumUMO) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccessForumUMO;
			}
			
			if (abs($tmp['AccessLevel']) & AccesInnerNews) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccesInnerNews;
			}*/
			
		} elseif($tmp['clan'] == 'Military Police') {
			/*if (abs($tmp['AccessLevel']) & AccessShop) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccessShop;
			}
			
			if (abs($tmp['AccessLevel']) & AccessAuthUser) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccessAuthUser;
			}
			
			if (abs($tmp['AccessLevel']) & AtackReport) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AtackReport;
			}
			
			if (abs($tmp['AccessLevel']) & AccessForum) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccessForum;
			}
			
			if (abs($tmp['AccessLevel']) & AccesInnerNews) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccesInnerNews;
			}
			
			if (abs($tmp['AccessLevel']) & AccessPolice) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccessPolice;
			}*/
			
		} elseif($tmp['clan'] == 'Tribunal') {
			//if (abs($tmp['AccessLevel']) & AccessShop) {
			//	$a = 1;
			//} else {
			//	$new_lvl = $new_lvl + AccessShop;
			//}
			
			if (abs($tmp['AccessLevel']) & AccessAuthUser) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccessAuthUser;
			}
			
			if (abs($tmp['AccessLevel']) & AccessForum) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccessForum;
			}
			
			if (abs($tmp['AccessLevel']) & AccesInnerNews) {
				$a = 1;
			} else {
				$new_lvl = $new_lvl + AccesInnerNews;
			}
		}
		if($new_lvl != abs($tmp['AccessLevel']) ){
			$query = "UPDATE `site_users` SET `AccessLevel` = '".$new_lvl."' WHERE `id` = '".$tmp['id']."' LIMIT 1;";
			mysql_query($query);
		}
	}

?>