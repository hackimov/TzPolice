<?

$exclude[] = "Terminal MP";
$city[] = "0/359";
$city[] = "0/0";
$city[] = "0/1";
$city[] = "1/359";
$city[] = "1/0";
$city[] = "1/1";
$city[] = "359/0";
$city[] = "56/33";
$city[] = "55/33";
$city[] = "56/34";
$city[] = "55/34";
$city[] = "24/313";
$city[] = "25/313";
$city[] = "24/314";
$city[] = "25/314";

// status 0 = chat on, 1 = chat off
//error_reporting(E_ALL);
error_reporting(0);
//include "/home/sites/police/dbconn/dbconn.php";
include "/home/sites/police/dbconn/dbconn_persistent.php";
//error_reporting(E_ALL);
$res = mysql_query("SELECT * FROM `mp_online` WHERE `logout` < 1");
$curdate = date("Y-m-d");
//echo ($curdate);
$count = 0;
//echo ("Current cops <hr><hr><hr>");
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
//echo ("Killing last day <hr><hr><hr>");
if ($tempvar > 0 && $tempvar !== $curdate)
	{
		foreach ($curcops as $key => $tmp)
        	{
	            $tmptime = time()-43200;
	            $tmpdate = date("Y-m-d",$tmptime);
	            $tmpdate .= " 23:59:59";
	            $tmptimestamp = strtotime($tmpdate);
	            $query = "UPDATE `mp_online` SET `logout`='".$tmptimestamp."' WHERE `id` = '".$tmp['id']."' LIMIT 1;";
				mysql_query($query);
				echo $query."<hr>";
				$tmptimestamp++;
				$query = "INSERT INTO `mp_online` SET `nick` = '".$key."', `login` = '".$tmptimestamp."', `date` = '".$curdate."', `status` = '".$tmp['status']."'";
				mysql_query($query);
				echo $query."<hr>";
            }
    }
/*
echo ("<pre>");
print_r($curcops);
echo ("</pre>");
*/
foreach (glob("/home/sites/police/bots/bot_mp/logs/mp-*.txt") as $filename) {
	        $fp = fopen ($filename, "r");
	        $bytes = filesize($filename);
	        $buffer = fread($fp, $bytes);
	        fclose ($fp);
	        unlink($filename);
	      	echo $buffer;
	        $cops_list = explode(",",$buffer);
	        foreach ($cops_list as $cop)
	            {
	                $arr = explode(":",$cop);
	                $nick = $arr[0];
	                $loc = $arr[1];
	                if ($nick !== "" && !in_array($nick,$exclude))
	                    {
//	                        if (strpos($arr[1],"+"))
			                if ($loc[strlen($loc)-1] == "+")
			                	{
	                                $chatoff_cops[$nick] = 1;
	                                $online_cops[$nick] = 0;
	                                $offline_cops[$nick] = 0;
	                                $outofcity_cops[$nick] = 0;
	                            }
	                        else
	                            {
	                            	$loc = str_replace("+","",$loc);
	                            	if ($loc=="0")
	                            		{	                            			$offline_cops[$nick] = 1;
	                            			$online_cops[$nick] = 0;
	                                		$chatoff_cops[$nick] = 0;
	                                		$outofcity_cops[$nick] = 0;	                            		}
	                            	else
	                            		{						$loc2 = substr($loc, 0, -4); // нахрена нам материк?	                            			if (in_array($loc2,$city))
	                            				{	                            				$offline_cops[$nick] = 0;
			                            		$online_cops[$nick] = 1;
	    		                            		$chatoff_cops[$nick] = 0;
	            		                    		$outofcity_cops[$nick] = 0;	                            				}
	                            			else
	                            				{	                            				$offline_cops[$nick] = 0;
			                            		$online_cops[$nick] = 0;
	    		                            		$chatoff_cops[$nick] = 0;
	            		                    		$outofcity_cops[$nick] = 1;	                            				}	                            		}
	                            }
					//////////////////////////////////
							if ($tempvar > 0 && $tempvar !== $curdate) {
							
								$query = "INSERT INTO `mp_online` SET `nick` = '".$nick."', `login` = '".time()."', `logout`='".(time()+1)."', `date` = '".$curdate."', `status` = '0'";
								mysql_query($query);
							
							}
					//////////////////////////////////

	                    }
	            }
	        //echo ("Online cops <hr><hr><hr>");
	        foreach ($online_cops as $key => $value)
	            {
	                if ($curcops[$key]['login'] > 0 && $value == 1)
	                    {
	                        if (($curcops[$key]['status'] == 1 || $curcops[$key]['status'] == 2) && $curcops[$key]['done'] == 0)
	                            {
	                                $query = "UPDATE `mp_online` SET `logout`='".time()."' WHERE `id` = '".$curcops[$key]['id']."' LIMIT 1;";
	                                mysql_query($query);
	                                //echo $query."<hr>";
	                                $query = "INSERT INTO `mp_online` SET `nick` = '".$key."', `login` = '".time()."', `date` = '".$curdate."', `status` = '0'";
	                                mysql_query($query);
	                                //echo $query."<hr>";
	                                $curcops[$key]['done'] = 1;
	                            }
                            elseif ($value == 1)
                            	{
									$curcops[$key]['done'] = 1;
                                }
	                    }
	                elseif ($curcops[$key]['login'] < 1000 && $value == 1 && $curcops[$key]['done'] == 0)
	                    {
	                        $query = "INSERT INTO `mp_online` SET `nick` = '".$key."', `login` = '".time()."', `date` = '".$curdate."', `status` = '0'";
	                        mysql_query($query);
	                        //echo $query."<hr>";
	                        $curcops[$key]['done'] = 1;
	                    }
	            }
	        //echo ("Chatoff cops <hr><hr><hr>");
	        foreach ($chatoff_cops as $key => $value)
	            {
	                if ($curcops[$key]['login'] > 0 && $value == 1)
	                    {
	                        if (($curcops[$key]['status'] == 0 || $curcops[$key]['status'] == 2) && $curcops[$key]['done'] == 0)
	                            {
	                                //echo $curcops[$key]['login'];
	                                $query = "UPDATE `mp_online` SET `logout`='".time()."' WHERE `id` = '".$curcops[$key]['id']."' LIMIT 1;";
	                                mysql_query($query);
	                                //echo $query."<hr>";
	                                $query = "INSERT INTO `mp_online` SET `nick` = '".$key."', `login` = '".time()."', `date` = '".$curdate."', `status` = '1'";
	                                mysql_query($query);
	                                //echo $query."<hr>";
	                                $curcops[$key]['done'] = 1;
	                            }
                            elseif ($value == 1)
                            	{
                                	$curcops[$key]['done'] = 1;
                                }
	                    }
	                elseif ($curcops[$key]['login'] < 1000 && $value == 1 && $curcops[$key]['done'] == 0)
	                    {
	                        $query = "INSERT INTO `mp_online` SET `nick` = '".$key."', `login` = '".time()."', `date` = '".$curdate."', `status` = '1'";
	                        mysql_query($query);
	                        //echo $query."<hr>";
	                        $curcops[$key]['done'] = 1;
	                    }
	            }
	        //echo ("Out of city cops <hr><hr><hr>");
	        foreach ($outofcity_cops as $key => $value)
	            {
	                if ($curcops[$key]['login'] > 0 && $value == 1)
	                    {
	                        if (($curcops[$key]['status'] == 0 || $curcops[$key]['status'] == 1) && $curcops[$key]['done'] == 0)
	                            {
	                                //echo $curcops[$key]['login'];
	                                $query = "UPDATE `mp_online` SET `logout`='".time()."' WHERE `id` = '".$curcops[$key]['id']."' LIMIT 1;";
	                                mysql_query($query);
	                                //echo $query."<hr>";
	                                $query = "INSERT INTO `mp_online` SET `nick` = '".$key."', `login` = '".time()."', `date` = '".$curdate."', `status` = '2'";
	                                mysql_query($query);
	                                //echo $query."<hr>";
	                                $curcops[$key]['done'] = 1;
	                            }
                            elseif ($value == 1)
                            	{
                                	$curcops[$key]['done'] = 1;
                                }
	                    }
	                elseif ($curcops[$key]['login'] < 1000 && $value == 1 && $curcops[$key]['done'] == 0)
	                    {
	                        $query = "INSERT INTO `mp_online` SET `nick` = '".$key."', `login` = '".time()."', `date` = '".$curdate."', `status` = '2'";
	                        mysql_query($query);
	                        //echo $query."<hr>";
	                        $curcops[$key]['done'] = 1;
	                    }
	            }
	        //echo ("Offline cops <hr><hr><hr>");
	        foreach($curcops as $tmp)
	            {
	                if($tmp['done'] == 0)
	                    {
	                        $query = "UPDATE `mp_online` SET `logout`='".time()."' WHERE `id` = '".$tmp['id']."' LIMIT 1;";
	                        mysql_query($query);
	                        //echo $query."<hr>";
	                    }
	        }
        }
?>
