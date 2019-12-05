<?
$exclude[] = "Terminal Forpost";
// status 0 = chat on, 1 = chat off

//include "/home/sites/police/dbconn/dbconn.php";
include "/home/sites/police/dbconn/dbconn_persistent.php";
//error_reporting(E_ALL);
$res = mysql_query("SELECT * FROM `online_forpost` WHERE `logout` < 1");
$curdate = date("Y-m-d");
$curtime = time();
$count = 0;
while ($result = mysql_fetch_array($res))
	{
	        $curcops[$result['nick']]['login'] = $result['login'];
	        $curcops[$result['nick']]['date'] = $result['date'];
	        $tempvar = $result['date'];
	        $curcops[$result['nick']]['id'] = $result['id'];
	        $curcops[$result['nick']]['status'] = $result['status'];
	        $curcops[$result['nick']]['done'] = 0;
	        $count++;
	}

if ($tempvar > 0 && $tempvar !== $curdate)
	{
	foreach ($curcops as $key => $tmp)
		{
			$tmptime = $curtime-43200;
			$tmpdate = date("Y-m-d",$tmptime);
			$tmpdate .= " 23:59:59";
			$tmptimestamp = strtotime($tmpdate);
			$query = "UPDATE `online_forpost` SET `logout`='".$tmptimestamp."' WHERE `id` = '".$tmp['id']."' LIMIT 1;";
			mysql_query($query);
			$tmptimestamp = $tmptimestamp+60;
			$query = "INSERT INTO `online_forpost` SET `nick` = '".$key."', `login` = '".$tmptimestamp."', `date` = '".$curdate."', `status` = '".$tmp['status']."'";
			mysql_query($query);
		}
	}
foreach (glob("/home/sites/police/bots/forpost_ads/logs/forpost-*.txt") as $filename)
	{
		$curptime = strptime($filename, "/home/sites/police/bots/forpost_ads/logs/forpost-%Y%m%d%H%M%S.txt");
		$curptime = mktime($curptime['tm_hour'], $curptime['tm_min'], $curptime['tm_sec'], 1+$curptime["tm_mon"], $curptime['tm_yday'], 1900+$curptime['tm_year']);
		$fp = fopen ($filename, "r");
		$bytes = filesize($filename);
		$buffer = fread($fp, $bytes);
		fclose ($fp);
		unlink($filename);
		$cops_list = explode(",",$buffer);
		foreach ($cops_list as $cop)
			{
				$arr = explode(":",$cop);
				$nick = $arr[0];
				$loc = $arr[1];
				if ($nick !== "" && !in_array($nick,$exclude))
//				if ($nick !== "")
					{
		//		if (strpos($arr[1],"+"))
			// --------- „ат выключен -----------
						if ($loc[strlen($loc)-1] == "+")
							{
								$offline_cops[$nick] = 0;
								$online_cops[$nick] = 0;
								$chatoff_cops[$nick] = 1;
							}
						else
							{
								$loc = str_replace("+","",$loc);
			// --------- ќффЋайн -----------								if ($loc=="0")									{										$offline_cops[$nick] = 1;
										$online_cops[$nick] = 0;
										$chatoff_cops[$nick] = 0;
									}
								else
									{
										$offline_cops[$nick] = 0;
										$online_cops[$nick] = 1;
										$chatoff_cops[$nick] = 0;
									}							}
					}
			//////////////////////////////////
				if ($tempvar > 0 && $tempvar !== $curdate)
					{
						$query = "INSERT INTO `online_forpost` SET `nick` = '".$nick."', `login` = '".time()."', `logout`='".(time()+1)."', `date` = '".$curdate."', `status` = '0'";
						mysql_query($query);
					}
			//////////////////////////////////
			}
	}
foreach ($online_cops as $key => $value)
	{
		if ($curcops[$key]['login'] > 0 && $value == 1)
			{
				if ($curcops[$key]['status'] == 1 && $curcops[$key]['done'] == 0)
					{
						$query = "UPDATE `online_forpost` SET `logout`='".$curtime."' WHERE `id` = '".$curcops[$key]['id']."' LIMIT 1;";
						mysql_query($query);
						$query = "INSERT INTO `online_forpost` SET `nick` = '".$key."', `login` = '".$curtime."', `date` = '".$curdate."', `status` = '0'";
						mysql_query($query);
						$curcops[$key]['done'] = 1;			
					}
				elseif ($value == 1)
					{
						$curcops[$key]['done'] = 1;
					}
			}	
		elseif ($curcops[$key]['login'] < 1000 && $value == 1 && $curcops[$key]['done'] == 0)
			{
				$query = "INSERT INTO `online_forpost` SET `nick` = '".$key."', `login` = '".$curtime."', `date` = '".$curdate."', `status` = '0'";
				mysql_query($query);
				$curcops[$key]['done'] = 1;
			}
	}

foreach ($chatoff_cops as $key => $value)
	{
		if ($curcops[$key]['login'] > 0 && $value == 1)
			{
				if (($curcops[$key]['status'] == 0) && $curcops[$key]['done'] == 0)
					{
						$query = "UPDATE `online_forpost` SET `logout`='".$curtime."' WHERE `id` = '".$curcops[$key]['id']."' LIMIT 1;";
						mysql_query($query);
						$query = "INSERT INTO `online_forpost` SET `nick` = '".$key."', `login` = '".$curtime."', `date` = '".$curdate."', `status` = '1'";
						mysql_query($query);
			 			$curcops[$key]['done'] = 1;
				
					}
				elseif ($value == 1)
					{
						$curcops[$key]['done'] = 1;
					}
			}
		elseif ($curcops[$key]['login'] < 1000 && $value == 1 && $curcops[$key]['done'] == 0)
			{
				$query = "INSERT INTO `online_forpost` SET `nick` = '".$key."', `login` = '".$curtime."', `date` = '".$curdate."', `status` = '1'";
				mysql_query($query);
				$curcops[$key]['done'] = 1;
			}
		}
foreach($curcops as $tmp)
	{
		if($tmp['done'] == 0)
			{
				$query = "UPDATE `online_forpost` SET `logout`='".$curtime."' WHERE `id` = '".$tmp['id']."' LIMIT 1;";
				mysql_query($query);
			}
	}
?>
