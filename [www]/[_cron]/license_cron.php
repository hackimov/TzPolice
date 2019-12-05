<?
//error_reporting(E_ALL);
//include "/home/sites/police/www/_modules/mysql.php";
include "/home/sites/police/www/_modules/functions.php";
include "/home/sites/police/dbconn/dbconn_persistent.php";
//error_reporting(E_ALL);
/*
$file = "/home/sites/police/bot_license/logs/wantcheck.txt";
$fp = fopen ($file, "r");
$bytes = filesize($file);
$buff = fread($fp, $bytes);
fclose ($fp);
unlink($file);
$buff = explode("\n",$buff);
foreach ($buff as $line)
	{
    	if (strlen($line) > 2)
        	{
				$query = "SELECT * FROM `law_checks` WHERE `nick` = '".$line."' AND `status` = '0' LIMIT 1;";
                $rs = mysql_query($query);
                if (mysql_num_rows($rs) == 0)
                	{
						$quer[$line] = "INSERT INTO `law_checks` SET `nick` = '".$line."', `urgent` = '0', `time1` = '".time()."', `payed` = '1', `payment` = '".time()."', `urg_cop` = 'NY+Xmas';";
                    }
//				echo ($query);
//				mysql_query($query) or die(mysql_error());
            }
    }
foreach ($quer as $line)
	{
 //   	echo ($line);
    	mysql_query($line);
    }
*/
foreach (glob("/home/sites/police/bot_license/logs/hist-*.txt") as $filename) {
    $fp = fopen ($filename, "r");
    $bytes = filesize($filename);
    $buffer = fread($fp, $bytes);
    fclose ($fp);
    unlink($filename);
//echo $buffer;
}
$buffer = explode("\n",$buffer);
//echo ("<pre>");
//print_r($buffer);
//echo ("</pre>");
$urgent_alarm = "";
foreach ($buffer as $line)
	{
		$valid = 0;
		$tst = explode("\t", $line);
		if ($tst[1] == 217)
			{
/*
				echo ("Sum: ".$tst[3]."<br>");
				echo ("Nick: ".$tst[4]."<br>");
				echo ("Date: ".$tst[0]."<br>");
*/
				$tmp = str_replace(".", "/", $tst[0]);
				$tmp = explode(" ", $tst[0]);
				$tmp2 = explode(".",$tmp[0]);
				$tmy = $tmp2[1]."/".$tmp2[0]."/".$tmp2[2];
				$tmd = $tmp[1]." ".$tmy;
				$date = strtotime($tmd);
				$paym = $date.$tst[4];
//				echo ("Parsed date: ".$date." (".date('d.m.Y H:i', $date).")<br><hr>");
/*				if (date("d.m.Y") == "12.07.2007")
                	{
	                    if ($tst[3] > 99)
	                        {
	                            $cop = addslashes($tst[6]);
	                            $urg = 2;
	                            $valid = 1;
	                            $mydate = time()-300;
	                            if ($date > $mydate) {$urgent_alarm .= $cop. " || Пришла оплата срочной проверки от персонажа [".$tst[4]."] (заявка подана ".date('d.m.Y H:i', $date).")\r\n";}
	                        }
	                    elseif ($tst[3] > 49)
	                        {
	                            $cop = '';
	                            $urg = 1;
	                            $valid = 1;
	                        }
	                    elseif ($tst[3] > 9)
	                        {
	                            $cop = '';
	                            $urg = 0;
	                            $valid = 1;
	                        }
	                    else
	                        {
	                            $valid = 0;
	                        }
                    }
                else
                	{
*/
	                    $reas = strtr(trim($tst[6]), "АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩ_ЫЬЭЮЯ", "абвгдеёжзийклмнопрстуфхцчшщъыьэюя");
                        $contest = 0;

	                    if ($reas == "анекдот 2008")
	                        {
	                            $valid = 1;
                        		$contest = 1;
                                $shownick = $tst[4];
	                        }
                        elseif ($reas == "анекдот 2008 аноним")
	                        {
	                            $valid = 1;
                        		$contest = 2;
                                $shownick = "Анонимный взнос";
	                        }

	                    if ($tst[3] > 299)
	                        {
	                            $cop = addslashes($tst[6]);
	                            $urg = 2;
	                            $valid = 1;
	                            $mydate = time()-180;
	                            if ($date > $mydate) {$urgent_alarm .= $cop. " || Пришла оплата срочной проверки от персонажа [".$tst[4]."] (заявка подана ".date('d.m.Y H:i', $date).")\r\n";}
	                        }
	                    elseif ($tst[3] > 99)
	                        {
	                            $cop = '';
	                            $urg = 1;
	                            $valid = 1;
	                        }
	                    elseif ($tst[3] > 9)
	                        {
	                            $cop = '';
	                            $urg = 0;
	                            $valid = 1;
	                        }
	                    else
	                        {
	                            $valid = 0;
	                        }
//                    }
//				echo ($valid."<br>");
				$query = "SELECT `id` FROM `law_checks` WHERE `payment` = '".$paym."' LIMIT 1;";
				$rs = mysql_query($query);
				if (mysql_num_rows($rs) > 0)
					{
						$valid = 0;
					}
				if ($valid)
					{
                    	if ($contest)
                        	{
                            	if ($contest == 2)
                                	{
                                    	$snnick = "<b>Анонимный взнос</b>";
                                    }
                                else
                                	{
	                                    $userinfo = GetUserInfo($shownick);
	                                    if (!$userinfo['error'] && $userinfo['level'] > 0)
	                                        {
	                                            if ($userinfo['man'] == 0)
	                                                {
	                                                    $pro = $userinfo['pro']."w";
	                                                }
	                                            else
	                                                {
	                                                    $pro = $userinfo['pro'];
	                                                }
	                                            if (strlen($userinfo['clan']) > 2)
	                                                {
	                                                    $snnick = "<img src='/_imgs/clans/".$userinfo['clan'].".gif' height='16' width='28'><b>".$userinfo['login']."</b> [".$userinfo['level']."] <img src='/_imgs/pro/i".$pro.".gif'>";
	                                                }
	                                            else
	                                                {
	                                                    $snnick = "<img src='/_imgs/clans/0.gif' height='16' width='28'><b>".$userinfo['login']."</b> [".$userinfo['level']."] <img src='/_imgs/pro/i".$pro.".gif'>";
	                                                }
	                                        }
	                                    else
	                                        {
                                            	$snnick = "<b>".$userinfo['login']."</b>";
                                            }
                                    }
                            	//$out99 = date("d.m.Y H:i", $date).": ".$snnick." - ".$tst[3]." мнт.<br>\r\n";
                                $out99 = $snnick." - ".$tst[3]." мнт.<br>\r\n";
								$fp = fopen ("/home/sites/police/www/anecdot2008/sponsors.txt", "a");
								fwrite ($fp, $out99);
								fclose ($fp);
								$query = "INSERT INTO `law_checks` SET `nick` = '".$tst[4]."', `urgent` = '".$urg."', `time1` = '".$date."', `payed` = '1', `payment` = '".$paym."', `urg_cop` = 'пожертвование анекдот 2008', `status`='100', checked_by='872';";
                                mysql_query($query) or die (mysql_error());
                            }
                        else
                        	{
								$query = "INSERT INTO `law_checks` SET `nick` = '".$tst[4]."', `urgent` = '".$urg."', `time1` = '".$date."', `payed` = '1', `payment` = '".$paym."', `urg_cop` = '".$cop."';";
								//echo ($query);
								//mysql_query($query) or die(mysql_error());
                                mysql_query($query);
                            }
					}
			}
	}
$mark[0] = "проверка пройдена. :congr: Соответствующую отметку Вы можете найти в истории персонажа.";
$mark[1] = "отказ в проверке - подача заявки раньше окончания испытательного срока. :nerv:";
$mark[2] = "отказ в проверке - нарушение правил подачи заявки. :nerv:";
$mark[3] = "проверка не пройдена, нарушение правил общения, испытательный срок ";
$mark[80] = "проверка не пройдена, персонаж отбывает административное наказание :sad:";
$mark[90] = "проверка не пройдена, хронические нарушения, каторга :cry:";
$mark[100] = "проверка не пройдена, на проверку подал не владелец персонажа :wow:";
$mark[110] = "проверка не пройдена, хронические нарушения, штраф :cry:";

$urgency[0] = "обычная";
$urgency[1] = "ускоренная 12 часов";
$urgency[2] = "срочная 1 час";

$expired = time() - 258000;
$query = "SELECT `id`, `nick`, `result`, `term`, `time2` FROM `law_checks` WHERE `time2` > '".$expired."';";
$rs = mysql_query($query) or die(mysql_error());
$result_log = "";
while(list($n_id, $n_nick, $n_res, $n_term, $n_time) = mysql_fetch_row($rs))
	{
    	if ($n_res == 3)
     		{
	            	$result_log[$n_nick] = date('d.m.Y H:i', $n_time)." ".$mark[$n_res].$n_term." суток. :sad: ";
			}
		else
			{
				$result_log[$n_nick] = date('d.m.Y H:i', $n_time)." ".$mark[$n_res];
			}
	if ($n_nick == "Don_Andre")
		{
			$result_log[$n_nick] .= " нуб чс +игнор!!!11111адынадын";
		}
   }
$expired = time() - 691200;
$query = "SELECT `nick`, `urgent`, `time1` FROM `law_checks` WHERE `time1` > '".$expired."' AND `status` = '0';";
$rs = mysql_query($query) or die(mysql_error());
while(list($n_nick, $n_urg, $n_time) = mysql_fetch_row($rs))
	{
            	$result_log[$n_nick] = "Ваша заявка (".$urgency[$n_urg].") принята ".date('d.m.Y H:i', $n_time)." и находится в очереди на проверку.  :smoke: ";
	}
$towrite = "";
foreach ($result_log as $key => $value) {
    $towrite .= $key." || ".$value."\r\n";
}
$filename = "/home/sites/police/bot_license/clients.txt";
$handle = fopen($filename, 'w');
fwrite($handle, $towrite);
fclose($handle);
chmod ($filename, 0777);
$filename = "/home/sites/police/bot_license/cops.txt";
$handle = fopen($filename, 'w');
fwrite($handle, $urgent_alarm);
fclose($handle);
chmod ($filename, 0777);
//mysql_close();
?>