<?php
include "../_modules/mysql.php";
$sock = @fsockopen("tzpolice.ru", 80, $er1, $er2, 5);
if(@$sock) {
	fputs($sock, "GET /madimonster/items.xml HTTP/1.0\r\n");
	fputs($sock, "Host: tzpolice.ru \r\n");
	fputs($sock, "Content-type: application/x-www-url-encoded \r\n");
	fputs($sock, "\r\n\r\n");
	while (trim(fgets($sock, 4096))) 1;
	$set_name = "";
	$item_name = "";
	$item_res = array();
	for ($k=0; $k<8; $k++) $item_res[$k] = 0;
	$set_res = "";
	$in_use = 0;
	$in_set = 0;
	$quality = 0;
	$set_time = 0;
	$design = false;
	while (!feof($sock)) {
		$line = fgets($sock, 4096);
		if (preg_match("/txt=\"Item design\".*?res=\"(.*?)\".*?cost2=\"(.*?)\".*?quality=\"(.*?)\".*?s1=\"(.*?)\"/",$line,$m)) {
			$tmp = explode(",",$m[1]);
			foreach ($tmp as $r) {
				$a = explode("=",$r);
				$item_res[$a[0]-1] = $a[1];
			}
			$set_res = $m[4];
			$in_set = $m[3];
			$set_time = $m[2];
			$design = true;
		} elseif (preg_match("/txt=\"(.*?)\"/",$line,$m)) {
			$m[1] = str_replace("&amp;","&",$m[1]);
			if ($item_name == $m[1]) {
				if (preg_match("/ count=\"(.*?)\"/",$line,$n)) $in_use += $n[1];
				else $in_use++;
			} else {
				if (preg_match("/ count=\"(.*?)\"/",$line,$n)) $in_use = $n[1];
				else $in_use = 1;
			}
			if ($item_name != "" && $item_name != $m[1]) $set_name = substr($set_name,0,strrpos($set_name," "))." set";
			if ($set_name == "") $set_name = $m[1];
			$item_name = $m[1];
			if (preg_match("/maxquality=\"(.*?)\"/",$line,$m)) $quality = $m[1];
		} elseif (preg_match("/<\/O>/",$line,$m)) {
			if ($design) {
				echo "Чертеж <b>".$set_name."</b>: \n";
				$SQL = "SELECT id FROM ItemsReq WHERE item_name='".mysql_escape_string($set_name)."'";
				if ($d = mysql_fetch_array(mysql_query($SQL))) {
					echo "<font color=green><b>Обновляем</b></font><br>\n";
					$SQL = "UPDATE ItemsReq SET item_name='".$set_name."', item_res='".implode(",",$item_res)."', set_res='".$set_res."', in_use='".$in_use."', in_set='".$in_set."', quality='".(substr($set_name,-3)=="set"?0:$quality)."', set_time='".$set_time."' WHERE id='".$d['id']."'";
				} else {
					echo "<font color=red><b>Новый</b></font><br>\n";
					$SQL = "INSERT INTO ItemsReq VALUES ('','22','".$set_name."','".implode(",",$item_res)."','".$set_res."','".$in_use."','".$in_set."','".(substr($set_name,-3)=="set"?0:$quality)."','0','".$set_time."')";
				}
				mysql_query($SQL);
			}
			$set_name = "";
			$item_name = "";
			for ($k=0; $k<8; $k++) $item_res[$k] = 0;
			$set_res = "";
			$in_use = 0;
			$in_set = 0;
			$quality = 0;
			$set_time = 0;
			$design = 0;
		}
	}
}

?>