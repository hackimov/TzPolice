<h1>Просмотр архива логов УМО</h1>
<?php
$btype[1] = "plant";
$btype[2] = "lab";
$btype[3] = "cell";
$btype[4] = "truck";

if(AuthStatus==1 && AuthUserName!="" && (AuthUserGroup == 100)) {
if (!isset($_REQUEST['interval']))
	{
    	$intrvl = "week";
    }
else
	{
    	$intrvl = $_REQUEST['interval'];
    }
if ($intrvl == "interval")
	{
		$per_start = $_REQUEST['day1'].".".$_REQUEST['month1'].".".$_REQUEST['year1'];
		$per_end = $_REQUEST['day2'].".".$_REQUEST['month2'].".".$_REQUEST['year1'];
    }
$cur_time = time();
$week_day = date("w", $cur_time);
$week_day = $week_day - 1;
if ($week_day < 0) {$week_day = 0;}
$week = date("W", $cur_time);
$hour = date("G", $cur_time);
$minute = date("i", $cur_time);
$second = date("s", $cur_time);
$today_start = $cur_time - ($hour*3600 + $minute*60 + $second);
$today_end = $today_start + 86400;
$week_start = $cur_time - 604800;
$week_end = $cur_time;
$bgstr[0]="background='i/bgr-grid-sand.gif'";
$bgstr[1]="background='i/bgr-grid-sand1.gif'";
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function period(obj){
	men = document.getElementById('per');
  if (obj.options[obj.selectedIndex].value == "interval")
  	{
		if(men.style.display=='none') men.style.display='';
	}
  else
  	{
		if(men.style.display=='') men.style.display='none';
	}
}
//-->
</script>
<center><form name="stat" method="POST" action="?act=law_stats">
	  <div align="center">Отчет за:
	      <select name="interval" onChange="period(this)">
            <option value="today" selected>сегодня</option>
            <option value="week">7 дней</option>
            <option value="interval">период</option>
          </select>
      </div>
    <div id="per" style="display:none" align="center">
					c<br>
	            <select name="day1">
                	<option value="1" selected>1</option>
						<?
                        for ($i = 2; $i <= 31; $i++) {
						    echo ("<option value=\"$i\">$i</option>");
							}
                        ?>
               </select>
				<select name="month1">
                       	<option value="1" selected>Января</option><option value="2">Февраля</option><option value="3">Марта</option><option value="4">Апреля</option><option value="5">Мая</option>
                        <option value="6">Июня</option><option value="7">Июля</option><option value="8">Августа</option><option value="9">Сентября</option><option value="10">Октября</option>
                        <option value="11">Ноября</option><option value="12">Декабря</option>
					  </select>
				<select name="year1">
                <option value="2005" selected>2005</option>
                </select>
					<br>по<br>
	            <select name="day2">
						<?
                        for ($i = 1; $i <= 30; $i++) {
						    echo ("<option value=\"$i\">$i</option>");
							}
                        ?>
                	<option value="31" selected>31</option>
               </select>
				<select name="month2">
                       	<option value="1">Января</option><option value="2">Февраля</option><option value="3">Марта</option><option value="4">Апреля</option><option value="5">Мая</option>
                        <option value="6">Июня</option><option value="7">Июля</option><option value="8">Августа</option><option value="9">Сентября</option><option value="10">Октября</option>
                        <option value="11">Ноября</option><option value="12" selected>Декабря</option>
					  </select>
				<select name="year2">
                <option value="2005" selected>2005</option>
                </select>
	</div><br>
	Статистика по:<br>
<br>
  <select name="select">
<?
//Plants
  $query = "SELECT * FROM `buildings` WHERE `type` = 1";
  $result = mysql_query($query) or die (mysql_error());
  if (mysql_num_rows($result) > 0 ) {
?>
<option value="label">*** Заводы ***</option>
<option value="label"> </option>
<?
  while ($row = mysql_fetch_assoc($result)) {
?>
<option value="<?=$row['id']?>"><?=$row['name']?></option>
<?
  }
?><option value="label"> </option><?
}
//Labs
  $query = "SELECT * FROM `buildings` WHERE `type` = 2";
  $result = mysql_query($query) or die (mysql_error());
  if (mysql_num_rows($result) > 0 ) {
?>
<option value="label">*** Лаборатории ***</option>
<option value="label"> </option>
<?
  while ($row = mysql_fetch_assoc($result)) {
?>
<option value="<?=$row['id']?>"><?=$row['name']?></option>
<?
  }
?><option value="label"> </option><?
}
//Cells
  $query = "SELECT * FROM `buildings` WHERE `type` = 3";
  $result = mysql_query($query) or die (mysql_error());
  if (mysql_num_rows($result) > 0 ) {
?>
<option value="label" >*** Ячейки ***</option>
<option value="label"> </option>
<?
  while ($row = mysql_fetch_assoc($result)) {
?>
<option value="<?=$row['id']?>"><?=$row['name']?></option>
<?
  }
?><option value="label"> </option><?
}
//Trucks
  $query = "SELECT * FROM `buildings` WHERE `type` = 4";
  $result = mysql_query($query) or die (mysql_error());
  if (mysql_num_rows($result) > 0 ) {
?>
<option value="label">*** Грузовики ***</option>
<option value="label"> </option>
<?
  while ($row = mysql_fetch_assoc($result)) {
?>
<option value="<?=$row['id']?>"><?=$row['name']?></option>
<?
  }
}
?>
  </select>
    <br><input type="submit" name="Submit" value="Показать">
</form>
</center>
<?
if ($_REQUEST['Submit'] == 'Показать' && $_REQUEST['select'] !== 'label')
	{
		$t1 = $_REQUEST['year1']."-".$_REQUEST['month1']."-".$_REQUEST['day1']." 00:00:00";
        $per_start = strtotime($t1);
		$t2 = $_REQUEST['year2']."-".$_REQUEST['month2']."-".$_REQUEST['day2']." 23:59:59";
        $per_end = strtotime($t2);
        if ($per_start == "" || $per_end == "")
		    {
        		?>
          <script>
            alert("Неверная дата!");
            top.location='?act=law_stats';
          </script>
		        <?
		    }
        if ($intrvl == 'today')
        	{
				$dt = "WHERE `time2` > '".$today_start."'";
                $tx = "за сегодня";
            }
        elseif ($intrvl == 'week')
        	{
				$dt = "WHERE `time2` > '".$week_start."' AND `time2` < '".$week_end."'";
                $tx = "за 7 дней";
            }
        elseif ($intrvl == 'interval')
        	{
				$dt = "WHERE `time2` > '".$per_start."' AND `time2` < '".$per_end."'";
                $tx = "за период с ".date('d M Y, H:i:s', $per_start)." по ".date('d M Y, H:i:s', $per_end);
            }



		$query = "SELECT `type` FROM `buildings` WHERE `id` = '".$_REQUEST['select']."';";
        $rs = mysql_query($query) or die(mysql_error());
        list ($ctype) = mysql_fetch_row($rs);
		$query = "SELECT * FROM `logs_".$btype[$ctype]."` WHERE `id_build` = '".$_REQUEST['select']."';";
        $res = mysql_query($query);
        if (mysql_num_rows($res) > 0)
        	{
	            switch ($ctype)
	                {
	                    case 1:
                        while ($cur = mysql_fetch_array($res, MYSQL_ASSOC))
                        	{
                            	
                            }
	                }
            }
        echo ("<center><b>Статистика проверок ".$tx."</b></center>");
		if (mysql_num_rows($rs) > 0)
        	{
		        $count = 0;
				while(list($n_id) = mysql_fetch_row($rs))
                	{
                        if ($n_id !== "")
                        	{
		                        $query = "SELECT `user_name` FROM `site_users` WHERE `id` = '".$n_id."' LIMIT 1;";
//								echo ($query);
                		        $rz = mysql_query($query) or die(mysql_error());
                        		$temp = mysql_fetch_row($rz);
		                        $emp[$count]['nick'] = $temp[0];
        		                $emp[$count]['id'] = $n_id;
                		        $query = "SELECT `id` FROM `law_checks` ".$dt." AND `checked_by` = '".$n_id."';";
                        		$rz = mysql_query($query) or die(mysql_error());
								$emp[$count]['chks'] = mysql_num_rows($rz);

                                $query = "SELECT `id` FROM `law_checks` ".$dt." AND `urgent` = '0' AND `payed` = '1' AND `checked_by` = '".$n_id."' AND `status` = '100';";
						        $rz = mysql_query($query) or die(mysql_error());
                                $emp[$count]['chks_reg'] = mysql_num_rows($rz);
						        $query = "SELECT `id` FROM `law_checks` ".$dt." AND `urgent` = '1' AND `payed` = '1' AND `checked_by` = '".$n_id."' AND `status` = '100';";
							    $rz = mysql_query($query) or die(mysql_error());
                                $emp[$count]['chks_12h'] = mysql_num_rows($rz);
						        $query = "SELECT `id` FROM `law_checks` ".$dt." AND `urgent` = '2' AND `payed` = '1' AND `checked_by` = '".$n_id."' AND `status` = '100';";
						        $rz = mysql_query($query) or die(mysql_error());
                                $emp[$count]['chks_1h'] = mysql_num_rows($rz);

        		                $count++;
							}
                    }
                echo ("Количество проверок по сотрудникам:<br>");
                for ($i = 0; $i < $count; $i++)
                	{
						echo (" <b>".$emp[$i]['nick']."</b> - <b>".$emp[$i]['chks']."</b> (обычных - ".$emp[$i]['chks_reg'].", 12 часов - ".$emp[$i]['chks_12h'].", 1 час - ".$emp[$i]['chks_1h'].")<br>");
                    }
                echo ("<br>Всего проверок - <b>".$urg_all."</b> (на сумму <b>".$mnt."</b> монет)");
                echo ("<br>Обычных проверок - <b>".$urg0."</b> (на сумму <b>".($urg0*10)."</b> монет)");
                echo ("<br>Срочных проверок (12 часов) - <b>".$urg1."</b> (на сумму <b>".($urg1*50)."</b> монет)");
                echo ("<br>Срочных проверок (1 час) - <b>".$urg2."</b> (на сумму <b>".($urg2*100)."</b> монет)");
//                echo ("<br>Срочных проверок (2 часа) - <b>".$urg3."</b> (на сумму <b>".($urg3*100)."</b> монет)");
            }
        else
        	{
            	echo ("В указанный период проверок не зарегистрировано");
            }
	}
else
	{


		if ($_REQUEST['det_chk'] == 'all')
        	{
            	$chk = "";
            }
        else
        	{
            	$chk = " AND `urgent` = '".$_REQUEST['det_chk']."'";
            }
		$t1 = $_REQUEST['year1']."-".$_REQUEST['month1']."-".$_REQUEST['day1']." 00:00:00";
        $per_start = strtotime($t1);
		$t2 = $_REQUEST['year2']."-".$_REQUEST['month2']."-".$_REQUEST['day2']." 23:59:59";
        $per_end = strtotime($t2);
        if ($per_start == "" || $per_end == "")
		    {
        		?>
          <script>
            alert("Неверная дата!");
            top.location='?act=law_stats';
          </script>
		        <?
		    }
        if ($intrvl == 'today')
        	{
				$dt = "WHERE `time2` > '".$today_start."'";
                $tx = "за сегодня";
            }
        elseif ($intrvl == 'week')
        	{
				$dt = "WHERE `time2` > '".$week_start."' AND `time2` < '".$week_end."'";
                $tx = "за 7 дней";
            }
        elseif ($intrvl == 'interval')
        	{
				$dt = "WHERE `time2` > '".$per_start."' AND `time2` < '".$per_end."'";
                $tx = "за период с ".date('d M Y, H:i:s', $per_start)." по ".date('d M Y, H:i:s', $per_end);
            }
	        $query = "SELECT `user_name` FROM `site_users` WHERE `id` = '".$_REQUEST['det_id']."' LIMIT 1;";
    	    $rz = mysql_query($query) or die(mysql_error());
        	$temp = mysql_fetch_row($rz);
			$u_nick = $temp[0];
    	    $u_id = $_REQUEST['det_id'];
        	echo ("<center><b>Статистика проверок ".$tx.", произведенных сотрудником ".$u_nick."</b></center><br><br>");
	        $query = "SELECT `nick`, `urgent`, `time1`, `time2` FROM `law_checks` ".$dt." AND `checked_by` = '".$_REQUEST['det_id']."'".$chk." ORDER BY `time2`;";
    	    $rs = mysql_query($query) or die(mysql_error());
            ?>
            <table width="90%"  border="0" align="center" cellpadding="3" cellspacing="2">
              <tr align="center">
			    <td bgcolor=#F4ECD4><b>Ник</b></td>
			    <td bgcolor=#F4ECD4><b>Дата заявки</b></td>
			    <td bgcolor=#F4ECD4><b>Тип заявки</b></td>
			    <td bgcolor=#F4ECD4><b>Дата проверки</b></td>
			    <td bgcolor=#F4ECD4><b>До конца срока</b></td>
			  </tr>
            <?
            $count = 0;
            if (mysql_num_rows($rs) > 0)
            {
            $cnt = 1;
            while(list($c_nick, $c_urg, $c_time1, $c_time2) = mysql_fetch_row($rs))
            	{
                    $pers[$cnt]['nick'] = $c_nick;
                    $pers[$cnt]['req'] = date('d.m.Y H:i', $c_time1);
                    $pers[$cnt]['urg'] = $c_urg;
                    $pers[$cnt]['done'] = date('d.m.Y H:i', $c_time2);
                    $temp = $remain[$c_urg]-($c_time2-$c_time1);
                    $quer = "SELECT SEC_TO_TIME(".$temp.");";
                    $rz = mysql_query($quer);
                    list($remn) = mysql_fetch_row($rz);
                    $pers[$cnt]['left'] = $remn;
                    $sort[$cnt] = $temp;
                    $cnt++;
                 }
                asort($sort);
                reset($sort);
				while (list($key, $val) = each($sort))
                	{
                    	echo("<tr>");
	                    echo ("<td ".$bg[$count].">".$pers[$key]['nick']."</td>");
					    echo ("<td ".$bg[$count].">".$pers[$key]['req']."</td>");
					    echo ("<td ".$bg[$count].">".$urgency[$pers[$key]['urg']]."</td>");
					    echo ("<td ".$bg[$count].">".$pers[$key]['done']."</td>");
                	    echo ("<td ".$bg[$count].">".$pers[$key]['left']."</td>");
                    	echo("</tr>");
	                    $count++;
    	                if ($count > 1)
        	            	{
            	            	$count = 0;
                	        }
                    }
                ?></table><?
            }
        else
        	{
            	echo ("В указанный период проверок не зарегистрировано");
            }















    }

?>
<?
}
else
{
echo $mess['AccessDenied'];
}
?>