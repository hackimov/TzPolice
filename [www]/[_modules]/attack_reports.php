<h1>Отчетность по нападениям</h1>



<?php

$bgstr[0]="background='i/bgr-grid-sand.gif'";

$bgstr[1]="background='i/bgr-grid-sand1.gif'";

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

?>

<center>

<table border="0" cellspacing="0" background="i/bgr-grid-sand.gif" cellpadding="10" style="BORDER: 1px #957850 solid;">

<tr><td>

<table width="100%"><tr><td align="center"><font color="red"><b>Внимание!</b></font><br></td></tr></table>

1. Раздел предназначен только для офицеров боевого отдела и курсантов Академии, приписанных к боевому отделу.<br>

2. По завершении боя укажите в данном разделе объект нападения, лог боя, персонажей, вмешавшихся в бой для помощи объекту нападения.<br>

3. Теперь можно зарегистрировать несколько нападений сразу. Если Вы регистрируете менее 5 нападений - просто не пишите ничего в оставшихся полях.<br>

</td></tr>

</table>

</center>

<?

if ($_REQUEST['sec'] == "history")

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

            top.location='?act=attack_reports';

          </script>

		        <?

		    }

    }



if(AuthStatus==1 && AuthUserName!="" && (AuthUserClan=='police' || AuthUserClan=='Military Police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy')) {

/*

        if (AuthUserClan == 'police')

        	{

            	$pm_status = 0;

            }

        elseif (AuthUserClan == 'Police Academy')

        	{

            	$pm_status = 1;

            }

        else

        	{

            	$pm_status = 100;

            }

        echo ($pm_status);

*/

if (!isset($_REQUEST['step']))

	{

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

<form name="add_report" method="post" action="?act=attack_reports">

<table width="90%"  border="1"  bordercolor="#333333" align="center" cellpadding="3" cellspacing="2">

  <tr>

    <td>



  <input type="hidden" name="step" value="2">

  Объект нападения <br>

  <input type="text" name="attacked1">

  <br>

  Номер боя

  <br>

  <input type="text" name="log1">

  <br>

 Помощники объекта *<br>

 <textarea name="assistants1" rows="3" wrap="VIRTUAL"></textarea>

 <br>

  Примечания<br>

 <textarea name="notice1" rows="3" wrap="VIRTUAL"></textarea>



    </td>

    <td>



  Объект нападения <br>

  <input type="text" name="attacked2">

  <br>

  Номер боя

  <br>

  <input type="text" name="log2">

  <br>

 Помощники объекта *<br>

 <textarea name="assistants2" rows="3" wrap="VIRTUAL"></textarea>

 <br>

  Примечания<br>

 <textarea name="notice2" rows="3" wrap="VIRTUAL"></textarea>



    </td>

    <td>



  Объект нападения <br>

  <input type="text" name="attacked3">

  <br>

  Номер боя

  <br>

  <input type="text" name="log3">

  <br>

 Помощники объекта *<br>

 <textarea name="assistants3" rows="3" wrap="VIRTUAL"></textarea>

 <br>

  Примечания<br>

 <textarea name="notice3" rows="3" wrap="VIRTUAL"></textarea>



    </td>

    <td>



  Объект нападения <br>

  <input type="text" name="attacked4">

  <br>

  Номер боя

  <br>

  <input type="text" name="log4">

  <br>

 Помощники объекта *<br>

 <textarea name="assistants4" rows="3" wrap="VIRTUAL"></textarea>

 <br>

  Примечания<br>

 <textarea name="notice4" rows="3" wrap="VIRTUAL"></textarea>



    </td>

    <td>



  Объект нападения <br>

  <input type="text" name="attacked5">

  <br>

  Номер боя

  <br>

  <input type="text" name="log5">

  <br>

 Помощники объекта *<br>

 <textarea name="assistants5" rows="3" wrap="VIRTUAL"></textarea>

 <br>

  Примечания<br>

 <textarea name="notice5" rows="3" wrap="VIRTUAL"></textarea>



    </td>

  </tr>

</table>

  <br><center>

  <input type="submit" name="Submit" value="Записать" style="width:150px"></center></form>





* Укажите ник(и) сражавшихся на стороне объекта нападения. Если ников несколько - вводите <b>каждый</b> из них <b>с новой строки</b>.



<?

	}

if ($_REQUEST['step'] == 2)

	{

		for ($xyz = 1; $xyz<6; $xyz++)

        {

        $cur_log = "log".$xyz;

        $cur_attacked = "attacked".$xyz;

        $cur_assistants = "assistants".$xyz;

        $cur_notice = "notice".$xyz;

        if (strlen($_REQUEST[$cur_log]) > 2)

        {

		$attacked = $_REQUEST[$cur_attacked];

        $attacker = AuthUserName;

        $log = $_REQUEST[$cur_log];

        if (strlen($_REQUEST[$cur_assistants]) > 2)

        	{

            	$tmp = $_REQUEST[$cur_assistants] . "\n";

                $tmp = str_replace("\r", "", $tmp);

                $assistants = explode("\n", $tmp);

                if (count($assistants) > 1)

                	{

                    	$db_ass = $assistants[0];

                        for ($i = 1; $i < count($assistants); $i++)

                        	{

                                if ($assistants[$i] !== "") {$db_ass .= ", ".$assistants[$i];}

							}

                    }

                elseif (count($assistants) == 1)

                	{

						$db_ass = $assistants[0];

                    }

            }

        $tmp = str_replace("\r", "", $_REQUEST[$cur_notice]);

        $notice = str_replace("\n", "<br>", $tmp);

        $time = time();

        if (AuthUserClan == 'police')

        	{

            	$pm_status = 0;

            }

        elseif (AuthUserClan == 'Police Academy')

        	{

            	$pm_status = 1;

            }

        else

        	{

            	$pm_status = 100;

            }

        $query = "INSERT INTO `attack_reports` ( `id` , `date` , `policeman` , `object` , `log` , `reason` , `assistants` , `pm_status` )

			VALUES (

			'', '".$time."', '".$attacker."', '".$attacked."', '".$log."', '".$notice."', '".$db_ass."', '".$pm_status."'

			);";

        mysql_query($query) or die(mysql_error());

	    if (count($assistants) > 0)

       		{

                for ($i = 0; $i < count($assistants); $i++)

                {

                	if ($assistants[$i] !== "")

                    {

                    	$query = "INSERT INTO `attack_assistants` ( `id` , `nick` , `log` , `object`, `subject`, `time`)

						VALUES (

						'', '".$assistants[$i]."', '".$log."', '".$attacked."', '".$attacker."', '".$time."'

						);";

                        mysql_query($query) or die (mysql_error());

                    }

				}

            }

        ?><script>top.location='?act=attack_reports';</script><?

    }

    }

    }

elseif (abs(AccessLevel) & AtackReportAdmin)

    {

		?>

<form name="stat" method="POST" action="?act=attack_reports">

<input type="hidden" name="sec" value="history">

<table width="500"  border="0" align="center" cellpadding="5" cellspacing="0">

  <tr>

    <td width="50%">

	  <div align="center">Отчет за:

	      <select name="interval" onChange="period(this)">

            <option value="today" selected>сегодня</option>

            <option value="week">7 дней</option>

            <option value="interval">период</option>

          </select>

      </div></td>

    <td>Выборка:<br>

          <input name="type" type="radio" value="police" checked>

        Полицейские

        <select name="pol_nick" id="pol_nick">

            <option value="_" selected>Все</option>

		<?

			$query = "SELECT DISTINCT `policeman` FROM `attack_reports` WHERE `pm_status` = 1 ORDER BY `policeman`;";

            $rs = mysql_query($query);

            while (list($nick) = mysql_fetch_row($rs))

            	{

		            echo ("<option value='".$nick."'>".$nick."</option>");

                }

        ?>

        </select>

        <br>

        <input name="type" type="radio" value="academy">

        Курсанты

        <select name="acd_nick" id="acd_nick">

            <option value="_" selected>Все</option>

		<?

			$query = "SELECT DISTINCT `policeman` FROM `attack_reports` WHERE `pm_status` = '1' ORDER BY `policeman`;";

            $rs = mysql_query($query);

            while (list($nick) = mysql_fetch_row($rs))

            	{

		            echo ("<option value='".$nick."'>".$nick."</option>");

                }

        ?>

        </select>

        <br>

        <input name="type" type="radio" value="objects">

        Объекты

        <select name="obj_nick" id="obj_nick">

            <option value="_" selected>Все</option>

		<?

			$query = "SELECT DISTINCT `object` FROM `attack_reports` ORDER BY `object`";

            $rs = mysql_query($query);

            while (list($nick) = mysql_fetch_row($rs))

            	{

		            echo ("<option value='".$nick."'>".$nick."</option>");

                }

        ?>

        </select></td>

  </tr>

</table>

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

    <center><input type="submit" name="Submit" value="Показать"></center>

</form>        <?

	if ($_REQUEST['sec'] == 'history')

    {

        if ($_REQUEST['interval'] == 'today')

        	{

				$dt = "WHERE `date` > '".$today_start."'";

                $tx = "за сегодня";

            }

        elseif ($_REQUEST['interval'] == 'week')

        	{

				$dt = "WHERE `date` > '".$week_start."' AND `date` < '".$week_end."'";

                $tx = "за 7 дней";

            }

        elseif ($_REQUEST['interval'] == 'interval')

        	{

				$dt = "WHERE `date` > '".$per_start."' AND `date` < '".$per_end."'";

                $tx = "за период с ".date('d M Y, H:i:s', $per_start)." по ".date('d M Y, H:i:s', $per_end);

            }

        if ($_REQUEST['type'] == 'police')

        	{

            	if($_REQUEST['pol_nick'] == '_')

                	{

                    	$query = "SELECT DISTINCT policeman, COUNT(log) as \"fights\" FROM `attack_reports` ".$dt." AND `pm_status` = '0' GROUP BY `policeman`;";

						$txt = "Отчет по нападениям <b>полицейских</b> ".$tx;

                        $k = "a";

                    }

                else

                	{

                    	$query = "SELECT * FROM `attack_reports` ".$dt." AND `policeman` = '".$_REQUEST['pol_nick']."';";

                        $txt = "Отчет по нападениям полицейского <b>".$_REQUEST['pol_nick']."</b> ".$tx;

                        $k = "i";

                    }

                $t = "p";

            }

        if ($_REQUEST['type'] == 'academy')

        	{

            	if($_REQUEST['acd_nick'] == '_')

                	{

                    	$query = "SELECT DISTINCT policeman, COUNT(log) as \"fights\" FROM `attack_reports` ".$dt." AND `pm_status` = '1' GROUP BY `policeman`;";

						$txt = "Отчет по нападениям <b>курсантов</b> ".$tx;

                        $k = "a";

                    }

                else

                	{

                    	$query .= "SELECT * FROM `attack_reports` ".$dt." AND `policeman` = '".$_REQUEST['acd_nick']."';";

                        $txt = "Отчет по нападениям курсанта <b>".$_REQUEST['acd_nick']."</b> ".$tx;

                        $k = "i";

                    }

                $t="p";



            }

        if ($_REQUEST['type'] == 'objects')

        	{

            	if($_REQUEST['obj_nick'] == '_')

                	{

                    	$query = "SELECT DISTINCT `object`, COUNT(log) as \"fights\" FROM `attack_reports` ".$dt." GROUP BY `object`;";

						$txt = "Список <b>объектов нападения</b> ".$tx;

                        $k = "a";

                    }

                else

                	{

                    	$query = "SELECT * FROM `attack_reports` ".$dt." AND `object` = '".$_REQUEST['obj_nick']."';";

                        $txt = "Отчет по нападениям на <b>".$_REQUEST['obj_nick']."</b> ".$tx;

                        $k = "i";

                    }

                $t="o";

            }

        echo ("<center>$txt</center><br><br>");

        if ($t == "p" && $k == "i") //Конкретный мент или курсант

        	{

            	?>

		        <table width="90%"  border="0" align="center" cellpadding="3" cellspacing="5">

				  <tr>

				    <td align="center" bgcolor=#F4ECD4><b>дата</b></td>

				    <td align="center" bgcolor=#F4ECD4><b>объект</b></td>

				    <td align="center" bgcolor=#F4ECD4><b>номер боя</b> </td>

				    <td align="center" bgcolor=#F4ECD4><b>помощники</b></td>

				    <td align="center" bgcolor=#F4ECD4><b>примечания</b></td>

				  </tr>

				<?

                $rs = mysql_query($query);

                $count = 0;

                while (list($c_id, $c_date, $c_policeman, $c_object, $c_log, $c_reason, $c_assistants, $c_pm_status) = mysql_fetch_row($rs))

                	{

					    echo ("<tr><td $bgstr[$count]>".date('d M Y, H:i:s', $c_date)."</td>");

					    echo ("<td $bgstr[$count]>$c_object</td>");

					    echo ("<td $bgstr[$count]>$c_log</td>");

					    echo ("<td $bgstr[$count]>$c_assistants</td>");

				    	echo ("<td $bgstr[$count]>$c_reason</td></tr>");

                        $count ++;

		                if ($count > 1) {$count = 0;}

                    }

                ?>

                </table>

                <?

            }

        if ($t == "o" && $k == "i") //Конкретный объект

        	{

            	?>

		        <table width="90%"  border="0" align="center"  cellpadding="3" cellspacing="5">

				  <tr>

				    <td align="center" bgcolor=#F4ECD4><b>дата</b></td>

				    <td align="center" bgcolor=#F4ECD4><b>полицейский</b></td>

				    <td align="center" bgcolor=#F4ECD4><b>номер боя</b> </td>

				    <td align="center" bgcolor=#F4ECD4><b>помощники</b></td>

				    <td align="center" bgcolor=#F4ECD4><b>примечания</b></td>

				  </tr>

				<?

                $rs = mysql_query($query) or die (mysql_error());

                $count = 0;

                while (list($c_id, $c_date, $c_policeman, $c_object, $c_log, $c_reason, $c_assistants, $c_pm_status) = mysql_fetch_row($rs))

                	{

					    echo ("<tr><td $bgstr[$count]>".date("d M Y, H:i:s", $c_date)."</td>");

					    echo ("<td $bgstr[$count]>$c_policeman</td>");

					    echo ("<td $bgstr[$count]>$c_log</td>");

					    echo ("<td $bgstr[$count]>$c_assistants</td>");

				    	echo ("<td $bgstr[$count]>$c_reason</td></tr>");

                        $count ++;

		                if ($count > 1) {$count = 0;}

                    }

                ?>

                </table>

                <?

            }

        if ($t == "p" && $k == "a") //Все менты или курсанты

        	{

            	?>

		        <table width="90%"  border="0" align="center" cellpadding="3" cellspacing="5">

				  <tr>

				    <td align="center" bgcolor=#F4ECD4><b>полицейский</b></td>

				    <td align="center" bgcolor=#F4ECD4><b>кол-во нападений</b></td>

				  </tr>

				<?

                $rs = mysql_query($query) or die (mysql_error());

                $count = 0;

                while (list($c_policeman, $c_count) = mysql_fetch_row($rs))

                	{

					    echo ("<tr><td $bgstr[$count]>$c_policeman</td>");

					    echo ("<td $bgstr[$count]>$c_count</td></tr>");

                        $count ++;

		                if ($count > 1) {$count = 0;}

                    }

                ?>

                </table>

                <?

            }

        if ($t == "o" && $k == "a") //Все объекты

        	{

            	?>

		        <table width="90%"  border="0" align="center" cellpadding="3" cellspacing="5">

				  <tr>

				    <td align="center" bgcolor=#F4ECD4><b>объект</b></td>

				    <td align="center" bgcolor=#F4ECD4><b>кол-во нападений</b></td>

				  </tr>

				<?

                $rs = mysql_query($query);

                $count = 0;

                while (list($c_object, $c_count) = mysql_fetch_row($rs))

                	{

					    echo ("<tr><td $bgstr[$count]>$c_object</td>");

					    echo ("<td $bgstr[$count]>$c_count</td></tr>");

                        $count ++;

		                if ($count > 1) {$count = 0;}

                    }

                ?>

                </table>

                <?

            }

    }

/*

		echo ($query."<br>");

        echo (date("d M Y, H:i:s", $per_start)."<br>");

        echo (date("d M Y, H:i:s", $per_end)."<br>");

*/

    }

} else echo $mess['AccessDenied'];

?>



</body>



</html>