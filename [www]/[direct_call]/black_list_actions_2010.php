<html>
<head>
  <title>.::Черный список::.</title>
  <LINK href="../_modules/tzpol_css.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td>
<?php
require('../_modules/functions.php');
require('../_modules/auth.php');

error_reporting(0);
if(AuthUserClan=='Tribunal' || AuthUserClan=='police' || AuthUserGroup == 100)
{
//writeBL();
/*mysql_connect("localhost", "user", "user");
$mysql_db = "tzpolice";
mysql_select_db($mysql_db);
*/
if(@$_REQUEST['s']) $section = $_REQUEST['s'];
//else die("Ошибочный запрос. Попробуйте еще раз");

?>
<script language="Javascript" type="text/javascript">
<!--
           function reload_opener() {
            opener.document.location.reload();
			window.close();
            }
//-->
</script>
<?


$cur_date = time();
$f_date = date ('Y-m-d', $cur_date);

//Showing add form
if ($section == 'add' && @$_REQUEST['nick'])
	{
		$new_nick = $_REQUEST['nick'];
		$query = 'SELECT `reason`, `nick` FROM `police_b_list2` WHERE `nick` = \''.$new_nick.'\' LIMIT 1;';
        $res = mysql_query($query) or die (mysql_error());
        if (mysql_num_rows($res) == 0)
        	{
		    	$userinfo = GetUserInfo($new_nick);
				if ($userinfo['login'] !== '' && $userinfo['error'] !== 'NOT_CONNECTED' && $userinfo['error'] !== 'USER_NOT_FOUND' && $userinfo['error'] !== 'ERROR_IN_USER_NAME' && $userinfo['level'] > 0) // && $userinfo["login"] == $new_nick
					{
				        	$query = 'INSERT INTO `police_b_list2` (`nick`, `level`, `pro`, `clan`, `reason`, `term`, `status`, `last_updated`, `payment`, `payment_till`, `no_payment_till`, `deleted_by`) VALUES (\''.$userinfo['login'].'\', \''.$userinfo['level'].'\', \''.$userinfo['pro'].'\', \''.$userinfo['clan'].'\', \'\', \'\', \'0\', \''.$cur_date.'\', \'\', \'\', \'\', \'\' );';
//						echo ($query);
						mysql_query($query) or die('Ошибка обработки запроса. Попробуйте повторить попытку позже');
						//Insert by Madimonster
//						mysql_query('INSERT INTO `black_list2` (`date`, `city`, `nick`, `level`, `pro`, `clan`, `status`) VALUES (NOW(), 0, \''.$userinfo['login'].'\', \''.$userinfo['level'].'\', \''.$userinfo['pro'].'\', \''.$userinfo['clan'].'\', 0);');
						//End
						//writeBL();
			                        $nick = $userinfo['login'];
					}
				else
                	{
    					 echo '<pre>';
                         print_r($userinfo);
                         echo '</pre>';
                    	 die('Ошибка обработки запроса. Попробуйте повторить попытку позже');
                    }
            }
        else
        	{
        		list($reason, $nick) = mysql_fetch_row($res);
		    	$userinfo = GetUserInfo($nick);    	
				if ($userinfo['error'] !== 'NOT_CONNECTED' && $userinfo['error'] !== 'USER_NOT_FOUND' && $userinfo['error'] !== 'ERROR_IN_USER_NAME') // && $userinfo["login"] == $new_nick
					{
	        	        $query = 'UPDATE `police_b_list2` SET `level` = \''.$userinfo['level'].'\', `pro` = \''.$userinfo['pro'].'\', `clan` = \''.$userinfo['clan'].'\', `last_updated` = \''.$cur_date.'\', `status` = \'0\' WHERE `nick` = \''.$nick.'\' LIMIT 1;';
						mysql_query($query) or die('Ошибка обработки запроса. Попробуйте повторить попытку позже');
						//Insert by Madimonster
//						mysql_query("INSERT INTO `black_list2` (`date`, `city`, `nick`, `level`, `pro`, `clan`, `status`) VALUES (NOW(), 0, '".$nick."', '".$userinfo['level']."', '".$userinfo['pro']."', '".$userinfo['clan']."', 0);");
						//End
						//writeBL();
                    }
                else
                	{
	                     die('Ошибка обработки запроса. Попробуйте повторить попытку позже');
                    }
               	$reason = $reason.'|||';
		        $rsn = explode('|||', $reason);
        		$reason = $rsn[0];
                $reason = str_replace('<br>', "\r\n", $reason);
            }

            echo '<img src="../_imgs/clans/'.$userinfo['clan'].'.gif"> '.$userinfo['login'].' ['.$userinfo['level'].'] <img src="../_imgs/pro/i'.$userinfo['pro'].'.gif"><br>';
?>

        <form name="add" method="post" action="">
	       <input name="s" type="hidden" value="do_add">
	       <input name="nick" type="hidden" value="<?=$nick?>">
<!--  Срок (дней):
  <input type="text" name="term" size="5" value="0"> (0 дней - бессрочно)
  <br> -->
  Примечание:<br>
  <textarea name="reason" cols="30" rows="7" wrap="VIRTUAL"></textarea><br />
    <input type="submit" name="Submit" value="Добавить">
</form>

<?
  }

//Adding char
if ($section == 'do_add')// && @$_REQUEST['nick'] && @$_REQUEST['term'] && @$_REQUEST['reason'])
	{
//error_reporting (E_ALL);
    	//echo ($_REQUEST['nick']."<br>".$_REQUEST['term']."<br>".$_REQUEST['reason']."<br>");
		$u_term = $_REQUEST['term'];
		if ($u_term > 0)
        	{
//                echo ($u_term);
				$term = ($u_term * 60 * 60 * 24);
                $u_term = $term + $cur_date;
//                echo ($u_term);
            }
        else
        	{
            	$u_term = 9999999999;
            }
        $u_nick = $_REQUEST['nick'];
		$query = 'SELECT * FROM `police_b_list2` WHERE `nick` = \''.$u_nick.'\' LIMIT 1;';
        $res = mysql_fetch_array(mysql_query($query)) or die (mysql_error());
        $add_ts = time();
        $u_reason = $_REQUEST['reason'].'|||'.AuthUserName;
        $u_reason = str_replace("\r\n", '<br>', $u_reason);
		$u_reason = $u_reason.'/|/'.$res['reason'];
		$u_updated = time();
		$payment = $res['payment'] + $res['level']*100;
        $query = 'UPDATE `police_b_list2` SET `added` = \''.$add_ts.'\', `reason` = \''.$u_reason.'\', `term` = \''.$u_term.'\', `status` = \'0\', `payment` = \''.$payment.'\', `no_payment_till` = \'0\', `payment_till` = \'0\' WHERE `nick` = \''.$u_nick.'\' LIMIT 1;';
        //echo ($query);
        mysql_query($query) or die(mysql_error());
	//writeBL();
        ?>
          Персонаж <b><?=$u_nick?></b> успешно добавлен в черный список<!--<br><br><a href="JavaScript:reload_opener();">ОК</a>-->
        <?
	}

//Removing char
if ($section == 'del' && @$_REQUEST['nick'] && (abs(AccessLevel) & AccessBlackList))
	{
		if (@$_REQUEST['step'] == '2')
			{
			        $del_reason = $_REQUEST['reason'];
        			$del_reason = str_replace("\r\n", "<br>", $del_reason);

					$query = "UPDATE `police_b_list2` SET `del_rsn` = '".$del_reason."', `rem_date` = '".time()."', `deleted_by` = '".AuthUserName."', `status` = '3' WHERE `nick` = '".$_REQUEST['nick']."' LIMIT 1;";
			        mysql_query($query);
				//writeBL();
				//Insert by Madimonster
//				mysql_query('DELETE FROM `black_list` WHERE `nick` = \''.$_REQUEST['nick'].'\';');
				//End
				?>
			          Персонаж <b><?=$_REQUEST['nick']?></b> успешно удален из черного списка
			        <?
			}
		else
			{
	?>
	Удаление персонажа <b><?=$_REQUEST['nick']?></b> из ЧС полиции <br><br><br>
        <form name="remove" method="post" action="">
	       <input name="s" type="hidden" value="del">
	       <input name="step" type="hidden" value="2">
	       <input name="nick" type="hidden" value="<?=$_REQUEST['nick']?>">
  Причина удаления из ЧС:
<br>
<textarea name="reason" cols="30" rows="7" wrap="VIRTUAL"></textarea>
<br />
    <input type="submit" name="Submit" value="ОК">
</form>
			<?
			}
    }

//Editing char
if ($section == "edit" && @$_REQUEST['nick'] && (abs(AccessLevel) & AccessBlackList))
	{
		$u_nick = $_REQUEST['nick'];
    	$userinfo = GetUserInfo($u_nick);
		if ($userinfo['error'] !== 'NOT_CONNECTED' && $userinfo['error'] !== 'USER_NOT_FOUND' && $userinfo['error'] !== 'ERROR_IN_USER_NAME') // && $userinfo["login"] == $u_nick
			{
		    	$u_updated = time();
		        $query = "UPDATE `police_b_list2` SET `level` = '".$userinfo['level']."', `pro` = '".$userinfo['pro']."', `clan` = '".$userinfo['clan']."', `last_updated` = '".$u_updated."' WHERE `nick` = '".$u_nick."' LIMIT 1;";
		        mysql_query($query);
			//writeBL();
		    }
		$query = 'SELECT `level`, `clan`, `pro`, `reason`, `payment` FROM `police_b_list2` WHERE `nick` = \''.$u_nick.'\' LIMIT 1;';
        $res = mysql_query($query);
        list($level, $clan, $pro, $reason, $payment) = mysql_fetch_row($res);
        $rsn = explode('/|/', $reason);
		foreach ($rsn as $key => $value)
                	{
                		$t_rsn = explode("|||", $value);
						if (strlen($t_rsn[0]) > 1)
							{
								$out_rsn .= strip_tags($t_rsn[0], '<br>')."<br>";
							}
					}
        $reason = str_replace('<br>', "\r\n", $out_rsn);
        echo '<img src="../_imgs/clans/'.$clan.'.gif"> '.$u_nick.' ['.$level.'] <img src="../_imgs/pro/i'.$pro.'.gif"><br>';
        ?>
<form name="edit_char" method="post" action="">
<!--
  Новый срок (дней):

  <input type="text" name="term" size="5" value="10"> (0 дней - бессрочно)

    <br>
  Изменить срок*
  <input type="checkbox" name="ch_term" value="checkbox">
  <br>
-->  
  Сумма: <input name="pay" type="text" id="pay" value="<?=$payment?>"><br>
  Примечание:<br>
  <textarea name="reason" cols="30" rows="7" wrap="VIRTUAL"><?=$reason?></textarea>
  <input name="s" type="hidden" id="s" value="do_edit">
  <input name="nick" type="hidden" id="nick" value="<?=$u_nick?>">
  <input name="old_term" type="hidden" id="old_term" value="<?=$term?>">
  <br>
  <input type="submit" name="Submit" value="Изменить">
<!--<br><br><em>*Указанное значение будет отсчитываться от текущего времени </em></p>-->
</form>
        <?
    }

//Save changes to char
if ($section == "do_edit" && (abs(AccessLevel) & AccessBlackList))// && @$_REQUEST['nick'] && @$_REQUEST['term'] && @$_REQUEST['old_term'] && @$_REQUEST['reason'])
	{
		$u_pay = $_REQUEST['pay'];
        $u_nick = $_REQUEST['nick'];
		$query = 'SELECT `reason` FROM `police_b_list2` WHERE `nick` = \''.$u_nick.'\' LIMIT 1;';
        $res = mysql_query($query);
        list($reason) = mysql_fetch_row($res);
        $reason = $reason.'|||';
        $rsn = explode('|||', $reason);
        $u_reason = $_REQUEST['reason'].'|||'.AuthUserName;
        $u_reason = str_replace("\r\n", '<br>', $u_reason);
        $query = "UPDATE `police_b_list2` SET `reason` = '".$u_reason."', `payment` = '".$u_pay."' WHERE `nick` = '".$u_nick."' LIMIT 1;";
        mysql_query($query);
	//writeBL();
        ?>
          Запись персонажа <?=$u_nick?> была успешно отредактирована<!--<br><br><a href="JavaScript:reload_opener();">ОК</a>-->
        <?
    }
}
else
{
echo ('Access denied...');
}
?>
</td>
  </tr>
</table>
</body>
</html>