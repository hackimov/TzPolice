<H1>Управление компенсациями</H1>
<?
	$module = '';
	error_reporting(0);
	$link_add = '&user='.$_REQUEST['user'].'&sessid='.$_REQUEST['sessid'].'&city='.$_REQUEST['city'];

	if (!isset($_REQUEST['sessid']) && !isset($_REQUEST['user']) && !isset($_REQUEST['city'])) {
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function tz_DoFSCommand(command, args) {
	var tmp = args.split("\t");
	if (command == "OK")
		{
			//alert('Login: '+tmp[0]+', SID: '+tmp[1]+', City: '+tmp[2]+', Level: '+tmp[3]+', Prof: '+tmp[4]+', Clan: '+tmp[5]);
			var pers_nick = '' + tmp[0];
			var pers_sid = '' + tmp[1];
			var pers_city = '' + tmp[2];
			var url = 'http://<?=$_SERVER['HTTP_HOST']?>/?act=compens_add&sessid=' + pers_sid + '&user=' + pers_nick + '&city=' + pers_city;
			top.location = url;
		}
	else alert('Authorization error '+tmp[0]);
}
if (navigator.appName.indexOf("Microsoft") != -1) {// Hook for Internet Explorer.
	document.write('<script language=\"VBScript\"\>\n');
	document.write('On Error Resume Next\n');
	document.write('Sub tz_FSCommand(ByVal command, ByVal args)\n');
	document.write('	Call tz_DoFSCommand(command, args)\n');
	document.write('End Sub\n');
	document.write('</script\>\n');
}
//-->
</SCRIPT>
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="1" height="1" id="tz">
<param name="movie" value="authorization3.swf" />
<param name="wmode" value="transparent" />
<embed src="authorization3.swf" wmode="transparent" width="1" height="1" swLiveConnect="true" id="tz" name="tz" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
<?
	} else {
		$link_add = '&user='.$_REQUEST['user'].'&sessid='.$_REQUEST['sessid'].'&city='.$_REQUEST['city'];
		$nick = $_REQUEST['user'];
		$nick2 = str_replace(' ', '%20', $nick);
		$nick = str_replace('%20', ' ', $nick);
		$sesid = $_REQUEST['sessid'];
		$city = $_REQUEST['city'];

		if (strlen($sesid) > 3) {
			
			$tmp_body = file_get_contents('https://www.timezero.ru/cgi-bin/authorization.pl?login='.$nick2.'&ses='.$sesid.'&city='.$city);

			if (strpos($tmp_body, "OK")) {
				$cur_user = $nick;
				if (in_array($nick, $compens_cops)) {
					$iscop = true;
				} else {
					$iscop = false;
				}
			} else {
				$iscop = false;
			}
		} else {
			$iscop = false;
		}

		if ($iscop) {
			$fid = 'new';
			$fnick = '';
			$flink = '';
			$fsum = 0;
			$fnote = '';
			$link_add = '&user='.$_REQUEST['user'].'&sessid='.$_REQUEST['sessid'].'&city='.$_REQUEST['city'];
			if ($_REQUEST['do'] == 'pay') {
				$avail = round($_REQUEST['sum']);
				$remain = $avail;
				$sumpay = 0;
				$sumperc = 0;
				$module = '';
				$counter = 0;
				$query = 'SELECT * FROM `compens` WHERE `status`<2 AND `percent` >0 AND `pay` = \'1\' ORDER BY `percent` ASC, `date1` ASC';
				$r = mysql_query($query);
				if (mysql_num_rows($r) > 0) {
					echo '<form name="mark_pays" action="index.php?act=compens_add&do=save_paym" method="post"><input type="hidden" name="user" id="user" value="'.$_REQUEST['user'].'"><input type="hidden" name="sessid" id="sessid" value="'.$_REQUEST['sessid'].'"><input type="hidden" name="city" id="city" value="'.$_REQUEST['city'].'">';
					while ($res = mysql_fetch_array($r)) {
						$payment = round($res['percent']*$res['sum']);
						if ($remain > $payment){
							$remain = $remain-$payment;
							$q = "UPDATE `compens` SET `status` = '1' WHERE `id` = '".$res['id']."' LIMIT 1;";
							mysql_query($q);
							$counter++;

							if ($res['cell'] < 1) {$cell = 'не указана';} else {$cell = $res['cell'];}
							echo "<input type='checkbox' name='done[]' value='".$res['id']."'> ".$counter.'. Персонажу '.ParseNews3($res['vfull']).' - <b>'.$payment.'</b> м.м. <br>';
							$module .= $res['victim'].",".$payment.",компенсация<br>";
							$sumperc = $sumperc+ceil($payment*0.001);
							$sumpay = $sumpay+$payment;
						} else {
							$needed = $payment;
							break;
						}
					}

					if ($counter > 0) {
						echo '<br><input type="submit" value="Отметить выделенные как *Выплачено*"></form>';
						echo "<hr>Сумма выплат: <b>".$sumpay."</b> м.м.<br>";
						echo "<hr>Количество выплат: <b>".$counter."</b><br>";
						echo "<hr>Остаток средств: <b>".$remain."</b> м.м.<br>";
						echo "<hr>Для выплаты следующей по очереди компенсации необходимо <b>".$needed."</b> м.м.<br><br><br><a onclick=\"if(confirm('Разблокировать невыплаченные заявки на компенсации?')){top.location='?act=compens_add&do=unlock".$link_add."'}\" href='#;return false'>Выплата компенсаций завершена</a>";
					} else {
						echo '<hr>Указанной суммы <b>'.$avail.'</b> м.м. недостаточно для выплаты по заявке, стоящей первой в очереди (необходимо монет: <b>'.$needed.'</b>)<br>';
					}
				} else {
					echo 'Очередь на выплату компенсаций пуста!';
				}
			} elseif ($_REQUEST['do'] == 'continue_to_pay') {
				$sumpay = 0;
				$sumperc = 0;
				$module = '';
				$counter = 0;
				$query = 'SELECT * FROM `compens` WHERE `status`=\'1\' ORDER BY `percent` ASC, `date1` ASC';
				$r = mysql_query($query);
				if (mysql_num_rows($r) > 0) {
					echo '<form name="mark_pays" action="index.php?act=compens_add&do=save_paym" method="post"><input type="hidden" name="user" id="user" value="'.$_REQUEST['user'].'"><input type="hidden" name="sessid" id="sessid" value="'.$_REQUEST['sessid'].'"><input type="hidden" name="city" id="city" value="'.$_REQUEST['city'].'">';
					while ($res = mysql_fetch_array($r)) {
						$payment = round($res['percent']*$res['sum']);
						$counter++;
						if ($res['cell'] < 1) {$cell = "не указана";} else {$cell=$res['cell'];}
						echo "<input type='checkbox' name='done[]' value='".$res['id']."'> ".$counter.'. Персонажу '.ParseNews3($res['vfull']).' - <b>'.$payment.'</b> м.м. <br>';
						$module .= $res['victim'].','.$payment.',компенсация<br>';
						$sumperc = $sumperc+ceil($payment*0.001);
						$sumpay = $sumpay+$payment;
					}

					echo '<br><input type="submit" value="Отметить выделенные как *Выплачено*"></form>';
					echo '<hr>Сумма выплат: <b>'.$sumpay.'</b> м.м.<br>';
					echo "<hr>Количество выплат: <b>".$counter."</b><br><br><br><a onclick=\"if(confirm('Разблокировать невыплаченные заявки на компенсации?')){top.location='?act=compens_add&do=unlock".$link_add."'}\" href='#;return false'>Выплата компенсаций завершена</a>";
				} else {
					echo 'Очередь на выплату компенсаций пуста!';
				}

			} elseif ($_REQUEST['do'] == "save_paym")
				{
					$fucking_checkboxes = $_REQUEST['done'];
					$count = 0;
					foreach ($fucking_checkboxes as $cur_id) {
					$q = "UPDATE `compens` SET `status` = '2', `date2`='".time()."' WHERE `id` = '".$cur_id."' LIMIT 1;";
					mysql_query($q);
					$count++;
				}

				echo '<b>'.$count."</b> компенсаций отмечены как *Выплачено*.<br><br><a onclick=\"if(confirm('Разблокировать невыплаченные заявки на компенсации?')){top.location='?act=compens_add&do=unlock".$link_add."'}\" href='#;return false'>Выплата компенсаций завершена</a><br><a href='?act=compens_add&do=continue_to_pay'>Продолжить выплату запланированных компенсаций</a>";
			} elseif ($_REQUEST['do'] == "unlock") {
				$q = "UPDATE `compens` SET `status` = '0' WHERE `status` = '1'";
				mysql_query($q);
				echo "Все компенсации разблокированы";
			} else {
				if (isset($_REQUEST['id'])) {
					if ($_REQUEST['do'] == 'add' && $_REQUEST['id'] == 'new' && trim($_REQUEST['nick']) != '') {
						$vnick = $_REQUEST['nick'];
						$userinfo = GetUserInfo($vnick);
						if (!$userinfo["error"]) {
							if ($userinfo['man'] == 0) {
								$pro = $userinfo['pro'].'w';
							} else {
								$pro = $userinfo['pro'];
							}

							$_RESULT = array("res" => "OK");
							if ($userinfo['level'] > 0) {
								if (strlen($userinfo['clan']) > 2) {
									$vfull = '[pers clan='.$userinfo['clan'].' nick='.$userinfo['login'].' level='.$userinfo['level'].' pro='.$pro.']';
								} else {
									$vfull = '[pers clan=0 nick='.$userinfo['login'].' level='.$userinfo['level'].' pro='.$pro.']';
								}
							} else {
								$vfull = '[b]'.$vnick.'[/b]';
							}
						} else {
							$vfull = '[b]'.$vnick.'[/b]';
						}

						$cnick = $_REQUEST['cnick'];
						if (strlen($cnick) > 2) {
							$pay = 0;
							$userinfo = GetUserInfo($cnick);
							if (!$userinfo['error']) {
								if ($userinfo['man'] == 0) {
									$pro = $userinfo['pro'].'w';
								} else {
									$pro = $userinfo['pro'];
								}

								$_RESULT = array('res' => 'OK');
								if ($userinfo['level'] > 0) {
									if (strlen($userinfo['clan']) > 2) {
										$cfull = '[pers clan='.$userinfo['clan'].' nick='.$userinfo['login'].' level='.$userinfo['level'].' pro='.$pro.']';
									} else {
										$cfull = '[pers clan=0 nick='.$userinfo['login'].' level='.$userinfo['level'].' pro='.$pro.']';
									}
								} else {
									$cfull = '[b]'.$cnick.'[/b]';
								}
							} else {
								$cfull = '[b]'.$cnick.'[/b]';
							}
						} else {
							$pay = 1;
							$cfull='';
							$cnick='';
						}
						$vlink = $_REQUEST['link'];
						$vlink = str_replace("#tz", '', $vlink);
						$vlink = str_replace("&m=1", '', $vlink);
						$vlink .= '&b=';
						//echo ($vlink);
						$vlink1 = sub($vlink, "cgi-bin/forum.pl?", "&b=");
						//echo ($vlink1);
						if (strlen($vlink1) > 5) {
							$vlink2 = 'forum.pl?'.$vlink1;
/****** временно отключена проверка дел!!! */
//							$query = "SELECT * FROM `case_main` WHERE `case_url` = '".$vlink2."' LIMIT 1;";
							$query = "SELECT * FROM `case_main` WHERE `case_url` LIKE '%".$vlink2."%' LIMIT 1;";
							$rsl = mysql_query($query);
							if ((mysql_num_rows($rsl) > 0) || $nick=='Odrik') {
//*/
								$query = "SELECT * FROM `compens` WHERE `link` = '".$vlink1."' AND `victim`='".$vnick."' AND `debtor` = '".$cnick."'";
								$r = mysql_query($query);
								if (mysql_num_rows($r) > 0) {
									$rs = mysql_fetch_array($r);
									echo "<div align='center'><b>Компенсация по указанному <a href='http://www.timezero.ru/cgi-bin/forum.pl?".$rs['link']."' target='_blank'>делу</a> в размере <b>".$rs['sum']."</b> м.м. уже внесена в очередь на имя персонажа".ParseNews3($rs['vfull']).'</b></div>';
									$fid = 'new';
									$fnick = $vnick;
									$flink = $_REQUEST['link'];
									$fsum = $_REQUEST['sum'];
									$fnote = $_REQUEST['note'];
									$cnick = $_REQUEST['cnick'];

								} else {
									$vsum = round($_REQUEST['sum']);
									$vnote = trim(strip_tags($_REQUEST['note']));
									$userip = ipCheck();
									$hist = $res['history']."{sum=".$vsum.", ip=".$userip.", date=".time()."}";
									$query = "INSERT INTO `compens` (`id`, `victim`, `cop`, `link`, `note`, `sum`, `percent`, `vfull`, `date1`, `date2`, `status`, `debtor`, `debtorfull`,`pay`)
	                                        VALUES ('', '".$vnick."', '".$nick."', '".$vlink1."', '".$vnote."', '".$vsum."','0','".$vfull."','".time()."','0','0','".$cnick."','".$cfull."','".$pay."')";
									mysql_query($query) or die(mysql_error());
									echo ("<div align='center'><b>Очередь на компенсацию пополнена.</b></div>");
									if($_REQUEST['scheck']==1){
										$_REQUEST['scheck']=0;
										$fnick = $vnick;
										$flink = $_REQUEST['link'];
										$fsum = '0';
										$fnote = $_REQUEST['note'];
										$cnick = '';
									}else{
										$_REQUEST['scheck']=0;
										$fnick = '';
										$flink = '';
										$fsum = '0';
										$fnote = '';
										$cnick = '';
									}
								}
/****** временно отключена проверка дел!!! */
                            }
                        else
                        	{
		                    	echo '<div align="center"><b>Указанный топик не найден в списке дел!</b></div>';
                                $fid = 'new';
                                $fnick = $vnick;
                                $flink = $_REQUEST['link'];
                                $fsum = $_REQUEST['sum'];
                                $fnote = $_REQUEST['note'];
                                $cnick = $_REQUEST['cnick'];
                            }
//*/
                    }
                else
                	{
                    	echo '<div align="center"><b>Укажите корректную ссылку на топик! Ссылка должна быть полностью скопирована из адресной строки браузера.</b></div>';
                        $fid = 'new';
                        $fnick = $vnick;
                        $flink = $_REQUEST['link'];
                        $fsum = $_REQUEST['sum'];
                        $fnote = $_REQUEST['note'];
                        $cnick = $_REQUEST['cnick'];
                    }
            }
    	elseif ($_REQUEST['do'] == 'add' && trim($_REQUEST['nick']) != '')
        	{
            	$vnick = $_REQUEST['nick'];
                $userinfo = GetUserInfo($vnick);
                if (!$userinfo['error'])
	            {
	                if ($userinfo['man'] == 0)
	                    {
	                        $pro = $userinfo['pro'].'w';
	                    }
	                else
	                    {
	                        $pro = $userinfo['pro'];
	                    }
		            $_RESULT = array('res' => 'OK');
                    if ($userinfo['level'] > 0)
                    	{
		                    if (strlen($userinfo['clan']) > 2)
	    		                {
	            		        	$vfull = '[pers clan='.$userinfo['clan'].' nick='.$userinfo['login'].' level='.$userinfo['level'].' pro='.$pro.']';
	                    		}
			                else
	    		            	{
	            		        	$vfull = '[pers clan=0 nick='.$userinfo['login'].' level='.$userinfo['level'].' pro='.$pro.']';
	                    		}
                        }
                    else
                    	{
                        	$vfull = '[b]'.$vnick.'[/b]';
                        }
                }
            else
            	{
                	$vfull = '[b]'.$vnick.'[/b]';
                }



            	$cnick = $_REQUEST['cnick'];
                if (strlen($cnick) > 2)
                	{
                    	$pay = 0;
	                    $userinfo = GetUserInfo($cnick);
	                    if (!$userinfo['error'])
	                    {
	                        if ($userinfo['man'] == 0)
	                            {
	                                $pro = $userinfo['pro'].'w';
	                            }
	                        else
	                            {
	                                $pro = $userinfo['pro'];
	                            }
	                        $_RESULT = array("res" => "OK");
	                        if ($userinfo['level'] > 0)
	                            {
	                                if (strlen($userinfo['clan']) > 2)
	                                    {
	                                        $cfull = '[pers clan='.$userinfo['clan'].' nick='.$userinfo['login'].' level='.$userinfo['level'].' pro='.$pro.']';
	                                    }
	                                else
	                                    {
	                                        $cfull = '[pers clan=0 nick='.$userinfo['login'].' level='.$userinfo['level'].' pro='.$pro.']';
	                                    }
	                            }
	                        else
	                            {
	                                $cfull = '[b]'.$cnick.'[/b]';
	                            }
	                    }
	                else
	                    {
	                        $cfull = '[b]'.$cnick.'[/b]';
	                    }
                	}
                else
                	{
                    	$pay = 1;
                        $cfull='';
                    }

                $vlink = $_REQUEST['link'];
                $vlink = str_replace("#tz", '', $vlink);
				$vlink = str_replace("&m=1", '', $vlink);
                $vlink .= '&b=';
                //echo ($vlink);
                $vlink1 = sub($vlink, "cgi-bin/forum.pl?", "&b=");
                //echo ($vlink1);
           		$vlink2 = 'forum.pl?'.$vlink1;
                $vsum = round($_REQUEST['sum']);
                $vnote = trim(strip_tags($_REQUEST['note']));
                $query = "SELECT `victim`, `status`, `history` FROM `compens` WHERE `id`='".$_REQUEST['id']."' LIMIT 1;";
				$r = mysql_query($query);
                $res = mysql_fetch_array($r);
                $userip = ipCheck();
				$hist = $res['history'].'{sum='.$vsum.', cop='.$nick.', vict='.$vnick.', ip='.$userip.', date='.time().'}';
                $query = "UPDATE `compens` SET `history`='".$hist."', `pay`='".$pay."', `debtor`='".$cnick."', `debtorfull`='".$cfull."', `victim`='".$vnick."', `vfull`='".$vfull."', `link`='".$vlink1."', `note`='".$vnote."', `sum`='".$vsum."' WHERE `id` = '".$_REQUEST['id']."' LIMIT 1;";
                mysql_query($query) or die(mysql_error());
                echo ("<div align='center'><b>Очередь на компенсацию обновлена.</b></div>");
            }
        else
        	{
            	$query = "SELECT * FROM `compens` WHERE `id` = '".$_REQUEST['id']."' LIMIT 1;";
                $r = mysql_query($query);
                $res = mysql_fetch_array($r);
	            $fid = $res['id'];
	            $fnick = $res['victim'];
	            $flink = 'http://www.timezero.ru/cgi-bin/forum.pl?'.$res['link'];
	            $fsum = round($res['sum']);
	            $fnote = $res['note'];
                $cnick = $res['debtor'];
            }
    }
?>
<form name="add_com" method="post" action="">
<input name="act" type="hidden" value="compens_add">
<input name="do" type="hidden" value="add">
<input type="hidden" name="user" id="user" value="<?=$_REQUEST['user']?>">
<input type="hidden" name="sessid" id="sessid" value="<?=$_REQUEST['sessid']?>">
<input type="hidden" name="city" id="city" value="<?=$_REQUEST['city']?>">
<input name="id" type="hidden" value="<?=$fid?>">
<table width="450"  border="0" align="center" cellpadding="3" cellspacing="3">
<tr><td width="50%">Ник потерпевшего:</td><td><input name="nick" type="text" id="nick" size="32" value="<?=$fnick?>"></td></tr>
<tr><td>Ссылка на дело:</td><td><input name="link" type="text" id="link" size="32" value="<?=$flink?>"></td></tr>
<tr><td>Сумма компенсации:</td><td><input name="sum" type="text" id="sum" size="8" value="<?=$fsum?>"> м.м.</td></tr>
<tr><td>Ник осужденного*:</td><td><input name="cnick" type="text" id="cnick" size="32" value="<?=$cnick?>"></td></tr>
<tr><td>Примечание:</td><td><input name="note" type="text" id="note" size="32" value="<?=$fnote?>"></td></tr>
<tr><td>Не очищать поля:<BR><SMALL>(ник потерпевшего, ссылка на дело, примечание)</SMALL></td><td><input name="scheck" type="checkbox" id="scheck" value="1"></td></tr>
<tr align="center"><td colspan="2"><input type="submit" name="Submit" value="Сохранить"></td></tr>
</table>
* - укажите ник персонажа, совершившего преступление, за которое присуждается компенсация. Выплата компенсации по данному делу будет заморожена до тех пор, пока указанный персонаж не выйдет с каторги.
</form>
<hr>
<h1>Выплата компенсаций</h1>
<form name="start" method="post" action="">
<input name="act" type="hidden" value="compens_add">
<input type="hidden" name="user" id="user" value="<?=$_REQUEST['user']?>">
<input type="hidden" name="sessid" id="sessid" value="<?=$_REQUEST['sessid']?>">
<input type="hidden" name="city" id="city" value="<?=$_REQUEST['city']?>">
<input name="do" type="hidden" value="pay">
<table width="200"  border="0" align="center" cellpadding="3" cellspacing="3">
<tr><td width="150" nowrap>Доступная сумма:</td><td nowrap><input name="sum" type="text" id="sum" size="8"> м.м.</td></tr>
<tr align="center"><td colspan="2"><input type="submit" name="Submit" value="Начать выплату"></td></tr>
</table>
</form>
<?}
$query = "SELECT `id` FROM `compens` WHERE `status` = '1'";
$r = mysql_query($query);
if ($sumpay > 0)
	{
		echo("<br><br><br><b>Список для модуля:</b><br><hr><br>".$module);
		echo("<br><hr><br>Затраты на переводы (процент): <b>".$sumperc."</b> м.м.<br>Затраты на выплаты: <b>".$sumpay."</b> м.м.");
	}
if (mysql_num_rows($r) > 0)
	{
    	echo ("<center><b>Внимание!</b><br>Есть запланированные и невыплаченные <a href='?act=compens_add&do=continue_to_pay".$link_add."'>компенсации</a>!<br><br></center>");
    }
}
else
{
echo ($mess['AccessDenied']);
}
}
?>
