<?
include ('/home/sites/police/dbconn/dbconn_persistent.php');
// ---------- функция отправки телеги ---------------
function bl_send_telegramm($nick, $msg)
	{
		$noerror=1;
		$message = "\n0xDEADBEEF || ".$nick." || ".$msg."\n";
		$filename = '/home/sites/police/bot_fees/alerts.txt';
		if (file_exists($filename))
			{
				chmod($filename, 0777);
			}
		if ($handle = fopen($filename, 'a'))
			{
				if (fwrite($handle, $message) === FALSE)
					{
						$noerror=0;
					}
				fclose($handle);
			}
		else
			{
				$noerror=0;
			}
		return $noerror;
	}

//-----------------------------------------------------------
function bl_log_parse()
	{
		foreach (glob("/home/sites/police/bot_blacklist/logs/hist-*.txt") as $filename)
			{
				print $filename."\n";
				$fp = fopen ($filename, "r");
				$bytes = filesize($filename);
				$buffer = fread($fp, $bytes);
				fclose ($fp);
				unlink($filename);
			}
		$st = 'SELECT `value` FROM `const` WHERE `script`=\'blacklist2010\' AND `name`=\'log_read_time\'';
		$sst=mysql_query($st) or die(mysql_error());
		$r = mysql_fetch_assoc($sst);
		$log_read_time = $r['value'];
		echo ($log_read_time."<br>");
		$st = 'SELECT `value` FROM `const` WHERE `script`=\'blacklist2010\' AND `name`=\'log_hash\'';
		$sst=mysql_query($st) or die(mysql_error());
		$r = mysql_fetch_assoc($sst);
		$log_hash = $r['value'];
		echo ($log_hash);
		$buffer = trim($buffer);
		$log_time = $i = 0;
		if(strlen($buffer)>0)
			{
				$buffer = explode("\n", $buffer);
				$telegrams = array();
				$buffer_size = sizeof($buffer);
				if($log_hash != '0')
					{
						while ($i<$buffer_size && $log_hash != md5($buffer[$i]) )
							{
								$i++;
							}
						if ($i==$buffer_size && $log_hash != md5($buffer[$i-1]) ) $i=0;
						elseif($log_hash == md5($buffer[$i])) $i++;
					}
				if($i<$buffer_size)
					{
						for($i=$i; $i<=$buffer_size-1; $i++)
							{
								$buffer[$i] = trim($buffer[$i]);
								if($buffer[$i]!='')
									{
										$str = explode("\t", $buffer[$i]);
					//	07.01.07 13:12
										$log_time = trim($str[0]);
										$log_time = explode(' ', $log_time);
										$log_time[0] = explode('.',$log_time[0]);
										$log_time[1] = explode(':',$log_time[1]);
										$log_time = mktime($log_time[1][0], $log_time[1][1], 0, $log_time[0][1], $log_time[0][0], $log_time[0][2]);

					// нам интересны токо входящие платежи
										if($str[1]=='217' && $log_time>=$log_read_time)
											{
												echo (date("d.m.Y H:i",$log_time)." ".date("d.m.Y H:i",$log_read_time));
												$sSQL = "SELECT `payment` FROM `police_b_list2` WHERE `nick`='".trim($str[4])."' AND `status`='0' LIMIT 1;";
												$result = mysql_query($sSQL);
												if(mysql_num_rows($result)>0)
													{
														$perevod = $perevod_copy = intval($str[3]);
														$row = mysql_fetch_assoc($result);
														$dolg = $row['payment'];
														if($perevod>0)
															{
																$remain = $dolg - $perevod;
																	if ($remain > 0)
																		{
																			$smSQL = "SELECT `reason` FROM `police_b_list2` WHERE `nick`='".trim($str[4])."' LIMIT 1;";
																			$res = mysql_query($smSQL);
																			$temp = mysql_fetch_array($res);
																			$set = "`payment`='".$remain."', `reason`='".$temp['reason']."Зачтено в оплату ЧС ".$perevod." монет ".date("d.m.Y H:i")."|||Terminal 01/|/'";
																			$smSQL = "UPDATE `police_b_list2` SET ".$set." WHERE `nick`='".trim($str[4])."' LIMIT 1;";
																			mysql_query($smSQL);
																			bl_send_telegramm(trim($str[4]),"Ваш платеж в оплату выхода из ЧС Полиции в размере ".$perevod." мнт принят, для выхода из ЧС осталось оплатить ".$remain." мнт.");													
																		}
																	else
																		{
																			$set = "`payment`='0', `status`='3', `del_rsn`='Зачтено в оплату ЧС ".$perevod." монет ".date("d.m.Y H:i")."', `deleted_by`='Terminal 01', `rem_date`='".time()."'";
																			$smSQL = "UPDATE `police_b_list2` SET ".$set." WHERE `nick`='".trim($str[4])."' LIMIT 1;";
																			mysql_query($smSQL);
																			bl_send_telegramm(trim($str[4]),"Ваш платеж в оплату выхода из ЧС Полиции в размере ".$perevod." мнт принят, Вы исключены из ЧС Полиции!");													
																		}
															}																										
													}
											}
								}
						}
					}

			}
			//	сохраняем последнюю строчку
				$str = explode("\t", $buffer[$buffer_size-1]);

			//	07.01.07 13:12
				$log_time = trim($str[0]);
				if ($log_time > 10)
					{
						$log_time = explode(' ', $log_time);
						$log_time[0] = explode('.',$log_time[0]);
						$log_time[1] = explode(':',$log_time[1]);
						$log_time = mktime($log_time[1][0], $log_time[1][1], 0, $log_time[0][1], $log_time[0][0], $log_time[0][2]);
		
						$sSQL = 'UPDATE `const` SET `value`="'.$log_time.'" WHERE `script`="blacklist2010" AND `name`="log_read_time"';
						mysql_query($sSQL);
		
					//	echo $buffer[sizeof($buffer)-1]." = ".md5($buffer[sizeof($buffer)-1]);
						$sSQL = 'UPDATE `const` SET `value`="'.md5($buffer[$buffer_size-1]).'" WHERE `script`="blacklist2010" AND `name`="log_hash"';
						mysql_query($sSQL);
					}
			}
bl_log_parse();
?>