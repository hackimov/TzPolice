<?
/****************************************************
*	 Lebedev Sergey (fantastish@rambler.ru)			*
****************************************************/
//!!!!!!!!!! ������� ����� - ������ ������ �������, ��������� �������
// ---------- ������� �������� ������ ---------------
	function fees_send_telegramm($nick, $msg){

		$noerror=1;
	//	��� ���� || ��� ��������� || ����� ����������
		if (AuthStatus==1)
			{
				$message = "\n".AuthUserName." || ".$nick." || ".$msg."\n";
			}
		else
			{
				$message = "\n������ || ".$nick." || ".$msg."\n";
			}
	// �������� ������� ��������� - ������ ������ ������� ������ ����
	//	$message .= "Terminal PA || antispam\n";

		$filename = '/home/sites/police/bot_fees/alerts.txt';

		if (file_exists($filename)){
	//		echo "file_exists";
			chmod($filename, 0777);
		}

	// ��������� $filename � ������ "�������� � �����".
		if ($handle = fopen($filename, 'a')) {
		// ���������� � �������� ����.
			if (fwrite($handle, $message) === FALSE) {
//				$text = "�� �������� ���������� ������ � ���� (".$filename.")";
				$noerror=0;
			}
	//		$text = "�������� (".$message.") � ���� (".$filename.")";
			fclose($handle);
		}else{
//			$text = "�� ���� ������� ���� (".$filename.")";
			$noerror=0;
		}

		return $noerror;
	}

//-----------------------------------------------------------
	function fees_log_parse ($connection, $db){

		foreach (glob("/home/sites/police/bot_fees/logs/hist-*.txt") as $filename) {
	//		chmod($filename, 0777);
			print $filename."\n";
			$fp = fopen ($filename, "r");
			$bytes = filesize($filename);
			$buffer = fread($fp, $bytes);
			fclose ($fp);
			unlink($filename);
		}

	//	$buffer ==
	/*
	07.01.07 12:09     217     1     1650     �������     592737     ����� �� ���.���
	07.01.07 13:12     8     213.219.216.161     0.1 (TZPD)

	[0]�����	[1]��������(217=�������� ������)	[2]��_���	[3]�����	[4]�����������	[5]�����_��_�����	[6]����������
	*///	echo "1";
	//	print_r($buffer);

	// ����� ���������� ������ ����
		$st = 'SELECT `value` FROM `const` WHERE `script`=\'fees\' AND `name`=\'log_read_time\'';
		$sst=mysql_query($st,$connection);
		$r = mysql_fetch_assoc($sst);
		$log_read_time = $r['value'];
//	echo date("d.m.Y H:i", $log_read_time);
		$st = 'SELECT `value` FROM `const` WHERE `script`=\'fees\' AND `name`=\'log_hash\'';
		$sst=mysql_query($st,$connection);
		$r = mysql_fetch_assoc($sst);
		$log_hash = $r['value'];

		$buffer = trim($buffer);
	//	echo "�����";
	//	print_r($buffer);

		$log_time = $i = 0;

		if(strlen($buffer)>0){
		// ��������� �� ������
			$buffer = explode("\n", $buffer);
		//	echo "�����2";
		//	print_r($buffer);
			$telegrams = array();

			$buffer_size = sizeof($buffer);

			if($log_hash != '0'){
			//	echo sizeof($buffer);
				while( $i<$buffer_size && $log_hash != md5($buffer[$i]) ){
			//		echo $i." - ".md5($buffer[$i])." = ".$buffer[$i]."<BR>\n";
					$i++;
				}
				if( $i==$buffer_size && $log_hash != md5($buffer[$i-1]) ) $i=0;
				elseif($log_hash == md5($buffer[$i])) $i++;
			}
		//	echo $i;
		//	echo sizeof($buffer);
			if($i<$buffer_size){
		//		echo "rrr";
				for($i=$i; $i<=$buffer_size-1; $i++){
					$buffer[$i] = trim($buffer[$i]);
					if($buffer[$i]!=''){
						$str = explode("\t", $buffer[$i]);

					//	07.01.07 13:12
						$log_time = trim($str[0]);
						$log_time = explode(' ', $log_time);
						$log_time[0] = explode('.',$log_time[0]);
						$log_time[1] = explode(':',$log_time[1]);
						$log_time = mktime($log_time[1][0], $log_time[1][1], 0, $log_time[0][1], $log_time[0][0], $log_time[0][2]);

					// ��� ��������� ���� �������� �������
						if($str[1]=='217' && $log_time>=$log_read_time){
					//		echo $buffer[$i]."<BR>\n";
							$sssSQL1 = 'SELECT `id` FROM `'.$db['tz_users'].'` WHERE `name` = \''.trim($str[4]).'\'';
							$sssqc1 = mysql_query($sssSQL1,$connection);
							if(mysql_num_rows($sssqc1)>0){
								$row1 = mysql_fetch_assoc($sssqc1);

								$sSQL = 'SELECT `id`, `summa`, `payed` FROM `'.$db['fees'].'` WHERE (`name_id`=\''.$row1['id'].'\' AND `payed`<`summa` AND `prison`=\'0\') ORDER BY `time` ASC';
								$result = mysql_query($sSQL,$connection);
								if(mysql_num_rows($result)>0){
									$perevod = $perevod_copy = intval($str[3]);
									$tdolg=0;
									while($row = mysql_fetch_assoc($result)){
									// ���� �� �������� ������
										$dolg = $row['summa'] - $row['payed'];
										$tdolg = $tdolg + $dolg;
										if($perevod>0){
										// ������� ������ ��� ����� �����
											if($perevod<=$dolg){
												$payed = $row['payed'] + $perevod;
												$perevod = 0;
										// ������� ������ �����
											}elseif($perevod>$dolg){
												$payed = $row['payed'] + $dolg;
												$perevod = $perevod - $dolg;
											}

											$set = '`payed`=\''.$payed.'\'';
											$smSQL = 'UPDATE `'.$db['fees'].'` SET '.$set.' WHERE `id`=\''.$row['id'].'\';';
											mysql_query($smSQL, $connection);
										}
									}

									$tdolg = $tdolg - $perevod_copy;
							// ��������� ������ ��� �������� ��������� ��������
							// ����� �� ��������� �.�. �������� ����� ����� ����� �������� ���� ���������� ���������
									$telegrams[trim($str[4])] = $tdolg;

									if($tdolg>0){
										$message = "��������!!! ��� ������� �������.\r� ��� ��� ���� �����(�). ���������� ���������: ".$tdolg.' ���.';
									}else{
										$message = "��������!!! ��� ������� �������.\r��� ���� ������ ��������� ��������. ������� �� ��������������.";
									}
								}
							}
						}
					}

				}

				if(sizeof($telegrams)>0){
				//	$telegrams[trim($str[4])] = $tdolg;
					foreach($telegrams AS $name=>$tdolg){
						if($tdolg>0){
							$message = "��������!!! ��� ������� �������.\r� ��� ��� ���� �����(�). ���������� ���������: ".$tdolg." ���.";
						}else{
							$message = "��������!!! ��� ������� �������.\r��� ���� ������ ��������� ��������. ������� �� ��������������.";
						}

						if(fees_send_telegramm($name, $message)){
					//		$text .= "".$name." - <FONT COLOR=green><B>���������� ����������</B></FONT><BR>\n";
						}else{
						//	$text .= "".$name." - <FONT COLOR=red><B>���������� �� ����������</B></FONT><BR>\n";
						}
					}
				}

			//	��������� ��������� �������
				$str = explode("\t", $buffer[$buffer_size-1]);

			//	07.01.07 13:12
				$log_time = trim($str[0]);
					{
						$log_time = explode(' ', $log_time);
						$log_time[0] = explode('.',$log_time[0]);
						$log_time[1] = explode(':',$log_time[1]);
						$log_time = mktime($log_time[1][0], $log_time[1][1], 0, $log_time[0][1], $log_time[0][0], $log_time[0][2]);

						$sSQL = 'UPDATE `const` SET `value`="'.$log_time.'" WHERE `script`="fees" AND `name`="log_read_time"';
						mysql_query($sSQL);

					//	echo $buffer[sizeof($buffer)-1]." = ".md5($buffer[sizeof($buffer)-1]);
						$sSQL = 'UPDATE `const` SET `value`="'.md5($buffer[$buffer_size-1]).'" WHERE `script`="fees" AND `name`="log_hash"';
						mysql_query($sSQL);
					}
			}

		}

	}


// ---------- ���������� ���� � ���� ���������� ----------
	function fees_user_insert ($connection, $db, $ok, $x_value)
		{
			$text = title('���������� ���������');
			$ok = intval($ok);
			// ���� ����� �� ���� �� ����������
			if(!$ok)
				{
					$text .= '';
			// ��������� ���������� ����� � �.�.
				}
			elseif(($x_value[0]=='' || strlen(trim($x_value[0]))<2) && $ok)
				{
					$text .= '<CENTER><H3>����������, ������e ���</H3></CENTER>';
				}
			elseif((($x_value[1]=="2" && ($x_value[3]=='' || strlen(trim($x_value[3]))<2))) && $ok)
				{
					$text .= '<CENTER><H3>����������, ������e ������</H3></CENTER>';
				}
			elseif(($x_value[4]=='' || strlen(trim($x_value[4]))<2) && $ok)
				{
					$text .= '<CENTER><H3>����������, ������e �������</H3></CENTER>';
				}
			elseif(intval($x_value[2])=='' && $ok)
				{
					$text .= '<CENTER><H3>����������, ������e ����� ������</H3></CENTER>';
	// ������������
				}
			else
				{

					for($i=0;$i<=4;$i++)
						{
							$x_value[$i] = addslashes(trim($x_value[$i]));
						}
					$names=array();
					if($x_value[0]!='')
						{
					// ������� � ��
//							$tmp_page = fant_TZConn($x_value[0], 0);
                                          
                                          // Inviz 26.10.11
                                          $userinfo = GetUserInfo($x_value[0], 0);
                                          
							//$tmp_page = TZConn($x_value[0]);
					// ���� ��������� ���� � �� ������
							//if(!is_array($tmp_page))
								//{
								// ������ ����
									//$userinfo = fant_ParseUserInfo($tmp_page);
								// ���������/��������� ��������� ���� � ���� ����
									//fant_tz_users_update($userinfo);

								// �������� id ����
									$sSQL2 = 'SELECT `id`, `name` FROM `'.$db['tz_users'].'` WHERE `name`=\''.$userinfo['login'].'\'';
									//$sSQL2 = 'SELECT `id`, `name` FROM `'.$db['tz_users'].'` WHERE `name`=\''.$x_value[0].'\'';
									$result2 = mysql_query($sSQL2, $connection);
									$row2 = mysql_fetch_assoc($result2);
									if ($row2['id']>0)
										{
											$sSQL1 = 'SELECT * FROM `'.$db['fees'].'` WHERE `name_id`=\''.$row2['id'].'\' AND `prison`=\'0\' AND `payed`<`summa`';
											$result1 = mysql_query($sSQL1, $connection);
											if(mysql_num_rows($result1)>0 && $ok==1)
												{
													$text .= "<CENTER><H2>��� �����c� ������������ �����.</H2></CENTER>";

													$text .= "<TABLE WIDTH=\"100%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\">\n";
													$text .= "<TR BGCOLOR=\"#E4DDC5\" ALIGN=\"center\">\n";
													$text .= " <TD><B>�����</B></TD>\n";
													$text .= " <TD><B>��������</B></TD>\n";
													$text .= " <TD><B>�����</B></TD>\n";
													$text .= " <TD><B>��������</B></TD>\n";
													$text .= "</TR>\n";

													$bgcolor[1]='#F5F5F5';
													$bgcolor[2]='#E4DDC5';
													$i=1;
													while($rows = mysql_fetch_assoc($result1))
														{
															if($i>2) $i=1;
															$text .= '<TR BGCOLOR="'.$bgcolor[$i]."\">\n";
															$text .= ' <TD>{_PERS_}'.stripslashes($row2['name']).'{_/PERS_}<BR><SMALL>['.stripslashes(nl2br($rows['text']))."]</SMALL></TD>\n";
															$sSQL21 = 'SELECT `user_name` FROM `site_users` WHERE `id`=\''.$rows['uid'].'\'';
															$result21 = mysql_query($sSQL21, $connection);
															$row21 = mysql_fetch_assoc($result21);
															$text .= ' <TD ALIGN=center><NOBR>{_PERS_}'.$row21['user_name'].'{_/PERS_}</NOBR><SMALL><BR>['.date("d:m:Y H:i", $rows['time'])."]</SMALL></TD>\n";

															$text .= " <TD ALIGN=center>".stripslashes($rows['summa'])."</TD>\n";
															$text .= " <TD ALIGN=center>".stripslashes($rows['payed'])."</TD>\n";
															$text .= "</TR>\n";

															$i++;
														}
													$text .= "</TABLE>\n";
													$text .= '<CENTER><H2>���� ��� �� ����� �������� ��� ���� ����� - ������� ������ "��������" ��� ���!</H2></CENTER>';

													$ok=2;
												}
											elseif(($row1[0]>0 && $ok==2) || $row1[0]<1)
												{
													// ������������ ������������ ������� ��� - ��������� �����
													if($x_value[1]=='2')
														{
															$for_text = '������ '.$x_value[3];
														}
													else
														{
															$for_text = '������ 1.4.3';
														}

													$x_value[4] = htmlspecialchars($x_value[4]);

													if($x_value[4]!='')
														{
															$for_text = $for_text.' (�������: '.$x_value[4].')';
														}

													$sSQL = 'INSERT INTO `'.$db['fees'].'` SET `name_id`=\''.$row2['id'].'\', `text`=\''.$for_text.'\', `summa`=\''.intval($x_value[2]).'\', `payed`=\'0\', `uid`=\''.AuthUserId.'\', `time`=\''.time().'\'';

													if (mysql_query($sSQL, $connection))
														{
															$x_value[3] = str_replace('\\\"','&quot;',$x_value[3]);
															
															$text .= "<A HREF=\"javascript:{}\" OnClick=\"ClBrd2('___�����, ��. ".stripslashes($x_value[3]).", (�������: ".stripslashes($x_value[4])."), ".intval($x_value[2])." ��.');\" TITLE=\"����������� � ����� ������\">___�����, ��. ".stripslashes($x_value[3]).", (�������: ".stripslashes($x_value[4])."), ".intval($x_value[2])." ��.</A><BR>\n";
															$text .= "<A HREF=\"javascript:{}\" OnClick=\"ClBrd2('___�����, ��. ".stripslashes($x_value[3]).", ".intval($x_value[2])." ��.');\" TITLE=\"����������� � ����� ������\">___�����, ��. ".stripslashes($x_value[3]).", ".intval($x_value[2])." ��.</A><BR>\n";
															$text .= $userinfo['login'].' - <FONT COLOR=green><B>���������</B> - c����='.intval($x_value[2])."</FONT><BR>\n";
															
															

															$sSQL2 = 'SELECT `user_name` FROM `site_users` WHERE `id`=\''.AuthUserId.'\'';
															$result2 = mysql_query($sSQL2, $connection);
															$row2 = mysql_fetch_assoc($result2);

															if($x_value[1]=='2')
																{
																	$for_text = $x_value[3];
																	$message = '��������!!! � ������������ �� ������� \''.stripslashes($for_text).'\' ������ \'� �������� �������\' ��� ����������� ����� � ������� '.intval($x_value[2]).' ������ �����. ('.$row2['user_name'].").\r ���������: ".stripslashes($x_value[4]).".\r �������� ����������� ������ 'Terminal 02'. ������� ������ ���� ����������� � ������� ���� ����� � ������� �������� ���� ����������.\r";

																}
															else
																{
																	$message = '��������!!! ������� TimeZero ������� �������� ���������� � ���������. ������� �������������� ������ ���������� - ���, ��������, ������, � ��� ����� ����������� � ��������������� ('.$row2['user_name'].").\r ���������: ".stripslashes($x_value[4]).".\r � ������������ �� ������� '1.4.3' ������ '� �������� �������' ��� ����������� ����� � ������� ".intval($x_value[2])." ������ �����.\r �������� ����������� ������ 'Terminal 02'. ������� ������ ���� ����������� �� ������� ��� ����� ����� � ������� ��������� ����������.\r � ������ ������ �� ����� ���������� �������� ����� ������������ � ������������ � �������� TimeZero.";
																}

																	/*		if($x_value[4]!=""){
																			$for_text = $x_value[4];
																			$message .= "\r\r�������: ".stripslashes($for_text).".";
																		}
																	*/
															$message .= "\r ��� ������ �� �������, � ����� ��������� � ������������� ����, ��� ����� �������� ��������� �� ������� �� ������� 1 ������ = 1 ������ ������ ��.";
															//$message .= "\r � ������������ �� ������� '1.1.2' ��������� ���������� ��������� � ������� ������ ���� � ������� ��������� ����������, � ������ ������������ �������� ����� ������������.";
															$message .= "\r ***��������!!!*** ��� �������� ������ �������������� ����������� � ��� �����! ���� �� ��������� �������� ������� ��� ������ ������ � �� �������� ������������� ������� ����������� � ������� 1 ���� - �������� �� ���� ���������� ������� �� ��������� �������� �� ������� �� �������� ������!!!";

															if(fees_send_telegramm($userinfo['login'], $message))
																{
																	$text .= $userinfo['login']." - <FONT COLOR=green><B>���������� ����������</B></FONT><BR>\n";
																}
															else
																{
																	$text .= $userinfo['login']." - <FONT COLOR=red><B>���������� �� ����������</B></FONT><BR>\n";
																}
															$ok=1;
															$x_value=array();

														}
													else
														{
															$text .= $userinfo['login']." - <FONT COLOR=red><B>������ ��� ����������</B></FONT><BR>\n";
														}
												}
											else
												{
													$text .= $userinfo['login']." - <FONT COLOR=red><B>������ ��� ����������</B></FONT><BR>\n";
												}
										}
									else
										{
											$text .= $userinfo['login']." - <FONT COLOR=red><B>������ ��� ����������</B></FONT><BR>\n";
										}
							//	}
							//else
							//	{
							//		$text .= $x_value[0].' - <FONT COLOR=red><B>������: '.$tmp_page['error']."</B></FONT><BR>\n";
							//	}
						}
					else
						{
							$text .= $x_value[0]." - <FONT COLOR=red><B>������ ���</B></FONT><BR>\n";
						}
				}
			for($i=0;$i<=4;$i++)
				{
					$x_value[$i] = stripslashes($x_value[$i]);
				}
			$form .= "<FORM METHOD=\"post\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\" NAME=\"frm\">\n";
			$form .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"fees\">\n";
			$form .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"user_insert\">\n";
			if($ok==2)
				{
					$form .= "<INPUT TYPE=\"hidden\" NAME=\"ok\" VALUE=\"2\">\n";
				}
			else
				{
					$form .= "<INPUT TYPE=\"hidden\" NAME=\"ok\" VALUE=\"1\">\n";
				}
			$form .= "<TABLE WIDTH=\"95%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\" ALIGN=\"center\">\n";

	// 	$x_value[0]
			$form .= " <TR>\n";
			$form .= "  <TD ALIGN=\"right\"><B>���:*&nbsp;</B></TD>\n";
			$form .= "  <TD ALIGN=\"left\"><INPUT SIZE=\"30\" TYPE=\"text\" NAME=\"x_value[0]\" VALUE=\"".$x_value[0]."\" CLASS=\"text\"></TD>\n";
			$form .= " </TR>\n";

	// 	$x_value[1]

/*		$form .= " <TR>\n";
		$form .= "  <TD ALIGN=\"right\"><B>������ �:*&nbsp;</B></TD>\n";
		$form .= "  <TD ALIGN=\"left\"><INPUT TYPE=\"radio\" NAME=\"x_value[1]\" VALUE=\"1\" CLASS=\"text\"".(($x_value[1]==1 || !isset($x_value[1]) || empty($x_value[1]))?" CHECKED":"")."><B>1.4.3<B></TD>\n";
		$form .= " </TR>\n";
*/			$form .= " <TR>\n";
			$form .= "  <TD ALIGN=\"right\"><B>������ �:*&nbsp;</B></TD>\n";
	//	$form .= "  <TD ALIGN=\"left\"><INPUT TYPE=\"radio\" NAME=\"x_value[1]\" VALUE=\"2\" CLASS=\"text\"".(($x_value[1]==2)?" CHECKED":"")."><INPUT TYPE=\"text\" NAME=\"x_value[3]\" VALUE=\"".$x_value[3]."\" SIZE=\"30\" CLASS=\"text\"></TD>\n";
			$form .= "  <TD ALIGN=\"left\"><INPUT TYPE=\"radio\" NAME=\"x_value[1]\" VALUE=\"2\" CLASS=\"text\" CHECKED><INPUT TYPE=\"text\" NAME=\"x_value[3]\" VALUE=\"".$x_value[3]."\" SIZE=\"30\" CLASS=\"text\"></TD>\n";
			$form .= " </TR>\n";
			$form .= " <TR>\n";
			$form .= "  <TD ALIGN=\"right\"><B>�������:*&nbsp;</B></TD>\n";
			$form .= "  <TD ALIGN=\"left\"><INPUT TYPE=\"text\" NAME=\"x_value[4]\" VALUE=\"".$x_value[4]."\" SIZE=\"30\" CLASS=\"text\" maxlength=\"240\">(max 240 ��������)</TD>\n";
			$form .= " </TR>\n";
	// 	$x_value[2]
			$form .= " <TR>\n";
			$form .= "  <TD ALIGN=\"right\"><B>����� ������:*&nbsp;</B></TD>\n";
			$form .= "  <TD ALIGN=\"left\"><INPUT TYPE=\"text\" NAME=\"x_value[2]\" VALUE=\"".$x_value[2]."\" SIZE=\"30\" CLASS=\"text\"></TD>\n";
			$form .= " </TR>\n";

			$form .= "</TABLE>\n";
			$form .= "<BR>\n";
			$form .= "<CENTER><INPUT TYPE=\"submit\" VALUE=\" �������� \" CLASS=\"submit\"></CENTER></FORM>\n";

			$text = $text.$form;

			return $text;
		}

// ---------- ������ ������ ������ ----------
	function fees_pay ($connection, $db, $id){

		$text = title('������ ������������� ������ ������');
		$id = intval($id);
		$sSQL = 'UPDATE `'.$db['fees'].'` SET `payed` = `summa` WHERE `id`=\''.$id.'\'';

		if (mysql_query($sSQL, $connection)){
			$text .= "<FONT COLOR=green><B>������ ������ ������������</B></FONT><BR>\n";
		}else{
			$text .= "<FONT COLOR=red><B>������ ��� ������������� ������</B></FONT><BR>\n";
		}
		return $text;
	}

// ---------- �������� ������ ----------
	function fees_delete ($connection, $db, $id){

		$text = title('�������� ������');
		$id = intval($id);
		$sSQL = 'DELETE FROM `'.$db['fees'].'` WHERE `id`=\''.$id.'\'';

		if (mysql_query($sSQL, $connection)){
			$text .= "<FONT COLOR=green><B>����� ������</B></FONT><BR>\n";
		}else{
			$text .= "<FONT COLOR=red><B>������ ��� ��������</B></FONT><BR>\n";
		}

		return $text;
	}

// ---------- �������� �� ������� ----------
	function fees_send2prison ($connection, $db, $ok, $id){

		$text = title('�������� �� �������');

		$text .= "<script language=\"JavaScript\">\n";
		$text .= "<!--\n";
		$text .= "function nck(vl){\n";
		$text .= "while(vl.indexOf('<BR>')>=0) {vl = vl.replace('<BR>','\015\012');}\n";
		$text .= "if (window.clipboardData){window.clipboardData.setData('Text', vl)} else {var DummyVariable = prompt('���������� ��� ������ � ����������� �� ��� ��������� � ���� ��:',vl)}}\n";
		$text .= "-->\n";
		$text .= "</script>\n";

		$sSQL1 = 'SELECT * FROM `'.$db['fees'].'` WHERE `id`=\''.$id.'\'';
		$result1 = mysql_query($sSQL1, $connection);
		$row1 = mysql_fetch_assoc($result1);
		$srok = $row1['summa'] - $row1['payed'];

		$sSQL2 = 'SELECT `name`, `level` FROM `'.$db['tz_users'].'` WHERE `id`=\''.$row1['name_id'].'\'';
		$result2 = mysql_query($sSQL2, $connection);
		$row2 = mysql_fetch_assoc($result2);

/*
	���� tzpolice_test, ������� prison_chars
	������� � dbconn2.php
	$link = mysql_connect();
*/
		require_once('/home/sites/police/dbconn/dbconn2.php');
/*
	id - �������������
	nick - ��� �����������
	term - ����
	collected - ������� ����������, ����
	last_pay - ���� ��������� ����� �����, ����
	reason - ������� ��������, ���� ������, 59
	remark - ����������, "�������� ������������� - ��������� ������ ��"
	dept - �����, 2
	add_date - ���� �������� � ������ ��������, timestamp [ 2007-02-17 ]
	add_by - ��� ��������, "�������"
	add_level - ������� �� ������ ����������
	allow_udo - �������� ����� �� ���, 0
	want_udo - ������ ����� �� ���, 0
	answer_udo - ����� �� ������� ���, �����
	coll_by_rating - �������� �� �������, 0
*/
		$sSQL3 = "SELECT * FROM `prison_chars` WHERE `nick`='".$row2["name"]."'";
		$result3 = mysql_query($sSQL3, $link);
	// ��� �����
		if(mysql_num_rows($result3)>0){
			if(!$ok){
				$text .= '<CENTER><H2>'.$row2['name']." - ��� �� �������</H2>\n";
				$row3 = mysql_fetch_assoc($result3);
				$text .= '<CENTER><H2>'.$row3['collected'].'/'.$row3['term'].' �����, '.$row3['reason'].' - '.$row3['remark'].', '.$row3['add_by'].' ['.$row3['add_date']."]</H2>\n";
				$text .= '<INPUT TYPE="button" CLASS="submit" OnClick="location.href=\'{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=fees&action=send2prison&id='.$id."&ok=1'\" value=\"��������� � ��������� ����� �����\"><BR>\n";
				$text .= '<INPUT TYPE="button" CLASS="submit" OnClick="location.href=\'{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=fees&action=send2prison&id='.$id."&ok=2'\" value=\"��������� � �� ����������� ����� �����\">";
				$text .= "</CENTER>\n";
			}elseif($ok=='1'){
			//	$sSQL = "UPDATE `".$db["fees"]."` SET `prison`='1', `uid`='".AuthUserId."' WHERE `id`='".$id."'";
				$sSQL = 'UPDATE `'.$db['fees'].'` SET `prison`=\'1\' WHERE `id`=\''.$id.'\'';

				if (mysql_query($sSQL, $connection)){
					$row3 = mysql_fetch_assoc($result3);
					$fsrok = $row3['term'] + $srok;
					$sSQL = 'UPDATE `prison_chars` SET `term`=\''.$fsrok.'\' WHERE `id`=\''.$row3['id'].'\'';
					if (mysql_query($sSQL, $link)){
						$text .= $row2['name']." - <FONT COLOR=green><B>�����</B></FONT><BR>\n";
						$text .= '<INPUT TYPE="button" CLASS="submit" OnClick="ClBrd2(\'1.5.4 �� ������� ������. +'.$srok.' �����\');" value="����������� � �����: \'1.5.4 �� ������� ������. +'.$srok." �����'\"><BR>\n";

					}else{
						$text .= "<FONT COLOR=red><B>������ ��� ����������2</B></FONT><BR>\n";
					}
				}else{
					$text .= "<FONT COLOR=red><B>������ ��� ����������</B></FONT><BR>\n";
				}
			}elseif($ok=='2'){
			//	$sSQL = "UPDATE `".$db["fees"]."` SET `prison`='1', `uid`='".AuthUserId."' WHERE `id`='".$id."'";
				$sSQL = 'UPDATE `'.$db['fees'].'` SET `prison`=\'1\' WHERE `id`=\''.$id.'\'';

				if (mysql_query($sSQL, $connection)){
					$text .= $row2['name']." - <FONT COLOR=green><B>�����</B></FONT><BR>\n";
					$text .= "<INPUT TYPE=\"button\" CLASS=\"submit\" OnClick=\"ClBrd2('1.5.4 �� ������� ������. +0 �����');\" value=\"����������� � �����: '1.5.4 �� ������� ������. +0 �����'\"><BR>\n";
				}else{
					$text .= "<FONT COLOR=red><B>������ ��� ����������</B></FONT><BR>\n";
				}
			}
	// ��� �� ����� ))
		}else{
		//	$sSQL = "UPDATE `".$db["fees"]."` SET `prison`='1', `uid`='".AuthUserId."' WHERE `id`='".$id."'";
			$sSQL = 'UPDATE `'.$db['fees'].'` SET `prison`=\'1\' WHERE `id`=\''.$id.'\'';

			if (mysql_query($sSQL, $connection)){
				$sSQL = 'INSERT INTO `prison_chars` SET `nick`=\''.$row2['name'].'\', `term`=\''.$srok.'\', `collected`=\'0\', `last_pay`=\'0\', `reason`=\'67\', `remark`=\'�������� ������������� - ��������� ������ ��\', `dept`=\'2\', `add_date`=\''.date("Y-m-d",time()).'\', `add_by`=\'�������\', `add_level`=\''.$row2['level'].'\', `want_udo`=\'0\', `answer_udo`=\'\', `coll_by_rating`=\'0\'';
				if (mysql_query($sSQL, $link)){
					$text .= $row2['name']." - <FONT COLOR=green><B>�����</B></FONT><BR>\n";
					$text .= '<INPUT TYPE="button" CLASS="submit" OnClick="ClBrd2(\'1.5.4 �� ������� ������. '.$srok.' �����\');" value="����������� � �����: \'1.5.4 �� ������� ������. '.$srok." �����'\"><BR>\n";
				}else{
					$text .= "<FONT COLOR=red><B>������ ��� ����������2</B></FONT><BR>\n";
				}
			}else{
				$text .= "<FONT COLOR=red><B>������ ��� ����������</B></FONT><BR>\n";
			}
		}

		return $text;
	}


// ---------- �������� � ���� ----------
	function fees_send2block ($connection, $db, $ok, $id){

		$text = title('�������� � ����');

		$text .= "<script language=\"JavaScript\">\n";
		$text .= "<!--\n";
		$text .= "function nck(vl){\n";
		$text .= " window.clipboardData.setData(\"Text\",vl);\n";
		$text .= "}\n";
		$text .= "-->\n";
		$text .= "</script>\n";

		$sSQL1 = 'SELECT * FROM `'.$db['fees'].'` WHERE `id`=\''.$id.'\'';
		$result1 = mysql_query($sSQL1, $connection);
		$row1 = mysql_fetch_assoc($result1);
		$srok = $row1['summa'] - $row1['payed'];

		$sSQL2 = 'SELECT `name`, `level` FROM `'.$db['tz_users'].'` WHERE `id`=\''.$row1['name_id'].'\'';
		$result2 = mysql_query($sSQL2, $connection);
		$row2 = mysql_fetch_assoc($result2);

	//	$sSQL = "UPDATE `".$db["fees"]."` SET `prison`='2', `uid`='".AuthUserId."' WHERE `id`='".$id."'";
		$sSQL = 'UPDATE `'.$db['fees'].'` SET `prison`=\'2\' WHERE `id`=\''.$id.'\'';

		if (mysql_query($sSQL, $connection)){
			$text .= $row2['name']." - <FONT COLOR=green><B>������ � ����</B></FONT><BR>\n";
			$text .= '<INPUT TYPE="button" CLASS="submit" OnClick="ClBrd2(\'�� ������� ������. '.$srok.' �����\');" value="����������� � �����: \'�� ������� ������. '.$srok." �����'\"><BR>\n";

		}else{
			$text .= "<FONT COLOR=red><B>������ ��� ����������</B></FONT><BR>\n";
		}

		return $text;
	}


// ---------- ���������� ���� � ���� ���������� ----------
	function fees_user_update ($id, $connection, $db, $ok, $x_value){

		$text = title('�������� ������');

		$sSQL = 'SELECT u.name AS name, f.text AS text, f.summa AS summa FROM `'.$db['tz_users'].'` AS u, `'.$db['fees'].'` AS f WHERE f.id=\''.$id.'\' AND f.name_id=u.id';
		$result = mysql_query($sSQL, $connection);
		$row = mysql_fetch_assoc($result);

		if($ok!=1){
			$x_value[1] = $row['text'];
			$x_value[2] = $row['summa'];
		}

		$form .= "<FORM METHOD=\"post\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\" NAME=\"frm\">\n";
		$form .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"fees\">\n";
		$form .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"user_update\">\n";
		$form .= "<INPUT TYPE=\"hidden\" NAME=\"ok\" VALUE=\"1\">\n";
		$form .= "<INPUT TYPE=\"hidden\" NAME=\"id\" VALUE=\"".$id."\">\n";
		$form .= "<TABLE WIDTH=\"95%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\" ALIGN=\"center\">\n";

	// 	$x_value[0]
		$form .= " <TR>\n";
		$form .= "  <TD ALIGN=\"right\"><B>���:*&nbsp;</B></TD>\n";
		$form .= '  <TD ALIGN=\"left\">{_PERS_}'.$row['name']."{_/PERS_}</TD>\n";
		$form .= " </TR>\n";

	// 	$x_value[1]
		$form .= " <TR>\n";
		$form .= "  <TD ALIGN=\"right\"><B>�������:&nbsp;</B></TD>\n";
		$form .= "  <TD ALIGN=\"left\"><INPUT TYPE=\"text\" NAME=\"x_value[1]\" VALUE=\"".htmlspecialchars(stripslashes($x_value[1]))."\" SIZE=\"30\" CLASS=\"text\" MAXLENGHT=\"250\">(max 250 ��������)</TD>\n";
		$form .= " </TR>\n";
	// 	$x_value[2]
		$form .= " <TR>\n";
		$form .= "  <TD ALIGN=\"right\"><B>����� ������:*&nbsp;</B></TD>\n";
		$form .= "  <TD ALIGN=\"left\"><INPUT TYPE=\"text\" NAME=\"x_value[2]\" VALUE=\"".$x_value[2]."\" SIZE=\"30\" CLASS=\"text\"></TD>\n";
		$form .= " </TR>\n";

		$form .= "</TABLE>\n";
		$form .= "<BR>\n";
		$form .= "<CENTER><INPUT TYPE=\"submit\" VALUE=\" ��������� \" CLASS=\"submit\"></CENTER></FORM>\n";

		$ok = intval($ok);
	// ���� ����� �� ���� �� ����������
		if(!$ok){
			$text .= $form;
		}elseif(($x_value[1]=='' || strlen(trim($x_value[1]))<2) && $ok){
			$text .= '<CENTER><H3>����������, ������e �������</H3></CENTER>'.$form;
		}elseif(intval($x_value[2])=='' && $ok){
			$text .= '<CENTER><H3>����������, ������e ����� ������</H3></CENTER>'.$form;
	// ������������
		}else{

			for($i=0;$i<=2;$i++){
				$x_value[$i] = addslashes(trim($x_value[$i]));
			}

			$sSQL = 'UPDATE `'.$db['fees'].'` SET `text`=\''.$x_value[1].'\', `summa`=\''.intval($x_value[2]).'\', `uid`=\''.AuthUserId.'\' WHERE `id`=\''.$id.'\'';

			if (mysql_query($sSQL, $connection)){
				$text .= "<A HREF=\"javascript:{}\" OnClick=\"ClBrd2('___�����, ��. ".$x_value[0].", ".$x_value[1].", ".$x_value[2]." ��.');\" TITLE=\"����������� � ����� ������\">___�����, ��. ".$x_value[0].", ".$x_value[1].", ".$x_value[2]." ��.</A><BR>\n";
				$text .= $row['name'].' - <FONT COLOR=green><B>���������</B> - c����='.intval($x_value[2])."</FONT><BR>\n";
			}else{
				$text .= "<FONT COLOR=red><B>������ ��� ����������</B></FONT><BR>\n";
			}
		}

		return $text;
	}


// ---------- ����� ������� -------------
	function fees_users_view ($action, $connection, $db, $page, $full_access){

		$sSQL = 'SELECT * FROM `'.$db['fees'].'` WHERE ';
		if($action=='view2'){
			$text = title('����������');
			$sSQL .= '`payed`=`summa` AND `prison`=\'0\' ORDER BY time DESC';
		}elseif($action=='view3'){
			$text = title('� ��������');
			$sSQL .= '`time`<'.(time()-604800).' AND `payed`<`summa` AND `prison`=\'0\' ORDER BY time DESC';
		}elseif($action=='view4'){
			$text = title('������������ �� �������');
			$sSQL .= '`prison`=\'1\' ORDER BY time DESC';
		}elseif($action=='view5'){
			$text = title('������������ � ����');
			$sSQL .= '`prison`=\'2\' ORDER BY time DESC';
		}else{
		//	$action=="view"
			$text = title('������ ����������');
			$sSQL .= '`time`>'.(time()-604800).' AND `payed`<`summa` AND `prison`=\'0\' ORDER BY time DESC';
		}

		$result = mysql_query($sSQL,$connection);
		$nrows = mysql_num_rows($result);
		if($nrows>0){
			$text .= "<BR>\n";
			list($sql, $ttext) = @list_all_pages($nrows, $page, '30', $sSQL, 'act=fees&action='.$action);
			$result=mysql_query($sql, $connection);

			$text .= '<CENTER>�����: '.$nrows." �������</CENTER>\n";

			$text .= "<script language=\"JavaScript\">\n";
			$text .= "<!--\n";
			$text .= "function nck(vl){\n";
			$text .= " window.clipboardData.setData(\"Text\",vl);\n";
			$text .= "}\n";
			$text .= "-->\n";
			$text .= "</script>\n";

			$text .= $ttext;

			$text .= "<TABLE WIDTH=\"100%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\">\n";
			$text .= "<TR BGCOLOR=\"#E4DDC5\" ALIGN=\"center\">\n";
			$text .= " <TD><B>�����</B></TD>\n";
			$text .= " <TD><B>��������</B></TD>\n";
			$text .= " <TD><B>�����</B></TD>\n";
			$text .= " <TD><B>��������</B></TD>\n";
			if($full_access==2 && ( $action != 'view2' && $action != 'view4' && $action != 'view5') ){
				$text .= ' <TD>&nbsp;</TD>';
			}
			$text .= "</TR>\n";

			$bgcolor[1] = '#F5F5F5';
			$bgcolor[2] = '#E4DDC5';
			$i=1;
			while($row = mysql_fetch_assoc($result)){

				if($i>2) $i=1;
				//}
			//	if($row["time2"]>time()) $bgcolor="green";
				$text .= '<TR BGCOLOR="'.$bgcolor[$i]."\">\n";

				$sSQL2 = 'SELECT * FROM `'.$db['tz_users'].'` WHERE `id`=\''.$row['name_id'].'\'';
				$result2 = mysql_query($sSQL2, $connection);
				$row2 = mysql_fetch_assoc($result2);

				if($row2['clan_id']>0){
					$sSQL3 = 'SELECT * FROM `'.$db['tz_clans'].'` WHERE `id`=\''.$row2['clan_id'].'\'';
					$result3 = mysql_query($sSQL3, $connection);
					$row3 = mysql_fetch_assoc($result3);
					$clan = '{_CLAN_}'.trim($row3['name']).'{_/CLAN_}';
				}else{
					$clan = '';
				}

		//		$text .= " <TD ALIGN=\"center\">".date("d:m:Y H:i", $row["time"])."</TD>\n";

				$text .= ' <TD>'.$clan.' <A HREF="javascript:{}" OnClick="ClBrd2(\''.stripslashes($row2['name']).'\');" TITLE="����������� ��� � ����� ������">'.stripslashes($row2['name']).'</A> ['.$row2['level'].'] {_PROF_}'.$row2['pro'].(($row2['sex']=='0')?'w':'').'{_/PROF_}<BR><SMALL>['.stripslashes(nl2br($row['text']))."]</SMALL></TD>\n";

				$sSQL2 = 'SELECT `user_name` FROM `site_users` WHERE `id`=\''.$row['uid'].'\'';
				$result2 = mysql_query($sSQL2, $connection);
				$row2 = mysql_fetch_assoc($result2);
				$text .= ' <TD ALIGN=center><NOBR>{_PERS_}'.$row2['user_name'].'{_/PERS_}</NOBR><SMALL><BR>['.date("d:m:Y H:i", $row['time'])."]</SMALL></TD>\n";

				$text .= ' <TD ALIGN=center>'.stripslashes($row['summa'])."</TD>\n";
				$text .= ' <TD ALIGN=center>'.stripslashes($row['payed'])."</TD>\n";
				if($full_access==2 && ( $action != 'view2' && $action != 'view4' && $action != 'view5') ){
					$text .= " <TD ALIGN=center><SMALL><NOBR>\n";
					$text .= '<A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=fees&action=user_update&id='.$row['id']."\" TITLE=\"�������������\">[ ��� ]</A>\n";
					$text .= "<A HREF=\"#\" TITLE=\"�������\" OnClick=\"javascript:if (confirm('�� ������������� ������ �������?')) { location.href='{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=fees&action=delete&id=".$row['id']."' }\">[ ���� ]</A>\n";
					$text .= "<A HREF=\"#\" TITLE=\"��������\" OnClick=\"javascript:if (confirm('�� �������?')) { location.href='{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=fees&action=payed&id=".$row['id']."' }\">[ ������� ]</A>\n";
					$text .= "</NOBR><BR><NOBR>\n";
					$text .= "<A HREF=\"#\" TITLE=\"��������� �� �������\" OnClick=\"javascript:if (confirm('�� �������?')) { location.href='{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=fees&action=send2prison&id=".$row['id']."' }\">[ �� ������� ]</A>\n";
					$text .= "</NOBR><BR><NOBR>\n";
					$text .= "<A HREF=\"#\" TITLE=\"��������� �� ����\" OnClick=\"javascript:if (confirm('�� �������?')) { location.href='{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=fees&action=send2block&id=".$row['id']."' }\">[ � ���� ]</A>\n";
					$text .= "</NOBR></SMALL></TD>\n";
				}
				$text .= "</TR>\n";

				$i++;
			}
			$text .= "</TABLE>\n";

			$text .= $ttext;
		}else{
			$text .= "<CENTER>������ ���</CENTER>";
		}


		return $text;
	}

//===========================================================
// -------����� ��� ������ �� ��������---------
	function fees_search ($connection, $db, $id, $cid, $page, $ok, $name){

		$name = trim(urldecode($name));

		$form = "<FORM METHOD=\"get\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\" name=\"select\">\n";
		$form .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"fees\">\n";
		$form .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"search\">\n";
		$form .= "<INPUT TYPE=\"hidden\" NAME=\"ok\" VALUE=\"1\">\n";
		$form .= "<TABLE WIDTH=\"20%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\" CLASS=\"size-10\">\n";
		$form .= " <TR VALIGN=\"top\">\n";
		$form .= "  <TD ALIGN=\"right\"><B>�����:&nbsp;</B></TD>\n";
		$form .= "  <TD><INPUT TYPE=\"text\" NAME=\"name\" VALUE=\"".$name."\" SIZE=\"15\" style=\"width=200\" CLASS=\"text\"></TD>\n";
		$form .= "  <TD ALIGN=left>&nbsp;<INPUT TYPE=\"submit\" VALUE=\" ����� \" CLASS=\"submit\"></TD>\n";
		$form .= " </TR>\n";

		$form .= "</TABLE></FORM>\n";

	// ���� ����� �� ���� �� ����������
		if($ok!=1){
			$text .= $form;
	// ��������� ����� ��� ��
		}else{
			$text .= $form.'<HR><CENTER><H4>���������:</H4></CENTER>';

		// ��������� ������� ��� ������ �� ��
			if(isset($name) && $name!='0' && $name!=''){
	//==============================================
		//		$name = catalogue_clear_text($name);
	//==============================================

				$sssSQL1 = 'SELECT `id` FROM `'.$db['tz_users'].'` WHERE `name` = \''.mysql_escape_string($name).'\' ORDER BY `name` ASC';

				$sssqc1 = mysql_query($sssSQL1, $connection);
				$nrows = mysql_num_rows($sssqc1);

			}else
				$nrows=0;


			if($nrows>0){
				$row1 = mysql_fetch_assoc($sssqc1);

				$sSQL = 'SELECT * FROM `'.$db['fees'].'` WHERE (`name_id`=\''.$row1['id'].'\') ORDER BY time DESC';
				$result = mysql_query($sSQL,$connection);
				$nrows = mysql_num_rows($result);
				if($nrows>0){
					list($sql, $ttext) = @list_all_pages($nrows, $page, '30', $sSQL, 'act=fees&action=search&name='.$name);
					$result=mysql_query($sql, $connection);

					$text .= '<B><CENTER>����� ������� �� �������: '.$nrows." �������</CENTER><BR></B>\n";

					$text .= "<script language=\"JavaScript\">\n";
					$text .= "<!--\n";
					$text .= "function nck(vl){\n";
					$text .= " window.clipboardData.setData(\"Text\",vl);\n";
					$text .= "}\n";
					$text .= "-->\n";
					$text .= "</script>\n";

					$text .= $ttext;

					$text .= "<TABLE WIDTH=\"100%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\">\n";
					$text .= "<TR BGCOLOR=\"#E4DDC5\" ALIGN=\"center\">\n";
					$text .= " <TD><B>�����</B></TD>\n";
					$text .= " <TD><B>��������</B></TD>\n";
					$text .= " <TD><B>�����</B></TD>\n";
					$text .= " <TD><B>��������</B></TD>\n";
					$text .= "</TR>\n";

					$bgcolor[1] = '#F5F5F5';
					$bgcolor[2] = '#E4DDC5';
					$i=1;
					while($row = mysql_fetch_assoc($result)){

						if($i>2) $i=1;
					//	if($row["time2"]>time()) $bgcolor="green";
						$text .= '<TR BGCOLOR="'.$bgcolor[$i]."\">\n";

						$sSQL2 = 'SELECT * FROM `'.$db["tz_users"].'` WHERE `id`=\''.$row['name_id'].'\'';
						$result2 = mysql_query($sSQL2, $connection);
						$row2 = mysql_fetch_assoc($result2);

						if($row2['clan_id']>0){
							$sSQL3 = 'SELECT * FROM `'.$db['tz_clans'].'` WHERE `id`=\''.$row2['clan_id'].'\'';
							$result3 = mysql_query($sSQL3, $connection);
							$row3 = mysql_fetch_assoc($result3);
							$clan = '{_CLAN_}'.trim($row3['name'])."{_/CLAN_}";
						}else{
							$clan = '';
						}

				//		$text .= " <TD ALIGN=\"center\">".date("d:m:Y H:i", $row["time"])."</TD>\n";

						$text .= ' <TD>'.$clan." <A HREF=\"javascript:{}\" OnClick=\"ClBrd2('".stripslashes($row2['name'])."');\" TITLE=\"����������� ��� � ����� ������\">".stripslashes($row2['name']).'</A> ['.$row2['level'].'] {_PROF_}'.$row2['pro'].(($row2['sex']=='0')?'w':'').'{_/PROF_}<BR><SMALL>['.stripslashes(nl2br($row['text']))."]</SMALL></TD>\n";

						$sSQL2 = 'SELECT * FROM `site_users` WHERE `id`=\''.$row['uid'].'\'';
						$result2 = mysql_query($sSQL2, $connection);
						$row2 = mysql_fetch_assoc($result2);
						$text .= ' <TD ALIGN=center><NOBR>{_PERS_}'.$row2['user_name'].'{_/PERS_}</NOBR><SMALL><BR>['.date("d:m:Y H:i", $row['time'])."]</SMALL></TD>\n";

						$text .= ' <TD ALIGN=center>'.stripslashes($row['summa'])."</TD>\n";
						$text .= ' <TD ALIGN=center>'.stripslashes($row['payed'])."</TD>\n";
						$text .= "</TR>\n";

						$i++;
					}

					$text .= "</TABLE>\n";

					$text .= $ttext;
				}else{
					$text .= "<BR><center><B>������� �� �������</B></center>\n";
				}
			}else{
				$text .= "<BR><center><B>��� �� ������</B></center>\n";
			}
		}

		return $text;
	}

// ---------- ������� ������ --------------
	function catalogue_clear_text($text){

		$text = stripslashes($text);
		$text = str_replace ('(', ' ', $text);
		$text = str_replace (')', ' ', $text);
		$text = str_replace ('[', ' ', $text);
		$text = str_replace (']', ' ', $text);
		$text = str_replace ('{', ' ', $text);
		$text = str_replace ('}', ' ', $text);

// $document ������ ��������� HTML-��������.
// ����� ����� ������� ���� HTML, ������� javascript
// � ������ ������������. ����� ��������� ������� ��������
// HTML �������������� � �� ��������� �����������.
$search = array ("'<script[^>]*?>.*?</script>'si",  // ���������� javascript
                 "'([\r\n])[\s]+'",                 // ���������� ������ ������������
                 "'&(quot|#34);'i",                 // ���������� html-��������
                 "'&(amp|#38);'i",
                 "'&(lt|#60);'i",
                 "'&(gt|#62);'i",
                 "'&(nbsp|#160);'i",
                 "'&(iexcl|#161);'i",
                 "'&(cent|#162);'i",
                 "'&(pound|#163);'i",
                 "'&(copy|#169);'i",
                 "'&#(\d+);'e");                    // ����������� ��� php

$replace = array ('',
                  "\\1",
                  "\"",
                  "&",
                  "<",
                  ">",
                  " ",
                  chr(161),
                  chr(162),
                  chr(163),
                  chr(169),
                  "chr(\\1)");

		$text = preg_replace ($search, $replace, $text);
	//	$text = preg_replace ("/[^(\w)|(\x7F-\xFF)|(\s)]/", " ", $text);
		$text = ereg_replace (" +", " ", $text);
		$text = trim($text);


		return $text;
	}


?>