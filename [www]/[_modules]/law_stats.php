<h1>���������� �������� �� ������� ����� �������</h1>
<?php
$bg[0]="background='i/bgr-grid-sand.gif'";
$bg[1]="background='i/bgr-grid-sand1.gif'";
$urgency[0] = "�������";
$urgency[1] = "<b>������� 12 �����</b>";
$urgency[2] = "<b>������� 1 ���</b>";
$remain[0] = 432000;
$remain[1] = 43200;
$remain[2] = 3600;
if(AuthStatus==1 && (substr_count(AuthUserRestrAccess, "law_control") || AuthUserGroup==100))
{
if (!isset($_REQUEST['interval']))
	{
    	$intrvl = "week";
    }
else
	{
    	$intrvl = $_REQUEST['interval'];
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
function detailed(obj){
	men = document.getElementById('detail');
  if (obj.checked)
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
<center>
<form name="stat" method="POST" action="?act=law_stats">
	<div align="center">����� ��:
		<select name="interval" onChange="period(this)">
			<option value="today" selected>�������</option>
			<option value="week">7 ����</option>
			<option value="interval">������</option>
		</select>
	</div>
	<div id="per" style="display:none" align="center">
<?
	$months_a = array (1 => "������", "�������", "�����", "������", "���", "����", "����", "�������", "��������", "�������", "������", "�������");

	echo "c<br>\n";
	echo "<select name=\"day1\">\n";
	if(!isset($_REQUEST['day1'])) $_REQUEST['day1'] = 1;
	for($i=1; $i<=31; $i++){
		echo " <OPTION VALUE='".$i."'".(($i==$_REQUEST['day1'])?" selected":"")." CLASS=\"select\">".$i."\n";
	}
	echo "</SELECT><SELECT NAME=\"month1\">\n";
	if(!isset($_REQUEST['month1'])) $_REQUEST['month1'] = date("m");
	for($i=1;$i<=12;$i++){
		echo " <OPTION VALUE='".$i."'".(($i==$_REQUEST['month1'])?" selected":"")." CLASS=\"select\">".$months_a[$i]."\n";
	}
	echo "</SELECT><SELECT NAME=\"year1\">\n";
	if(!isset($_REQUEST['year1'])) $_REQUEST['year1'] = date("Y");
	for($i=2005;$i<=date("Y");$i++){
		echo " <OPTION VALUE='".$i."'".(($i==$_REQUEST['year1'])?" selected":"")." CLASS=\"select\">".$i."\n";
	}
	echo "</SELECT>\n";
	
	echo "<br>��<br>\n";
	echo "<select name=\"day2\">\n";
	if(!isset($_REQUEST['day2'])) $_REQUEST['day2'] = 31;
	for($i=1; $i<=31; $i++){
		echo " <OPTION VALUE='".$i."'".(($i==$_REQUEST['day2'])?" selected":"")." CLASS=\"select\">".$i."\n";
	}
	echo "</SELECT><SELECT NAME=\"month2\">\n";
	if(!isset($_REQUEST['month2'])) $_REQUEST['month2'] = date("m");
	for($i=1;$i<=12;$i++){
		echo " <OPTION VALUE='".$i."'".(($i==$_REQUEST['month2'])?" selected":"")." CLASS=\"select\">".$months_a[$i]."\n";
	}
	echo "</SELECT><SELECT NAME=\"year2\">\n";
	if(!isset($_REQUEST['year2'])) $_REQUEST['year2'] = date("Y");
	for($i=2005;$i<=date("Y");$i++){
		echo " <OPTION VALUE='".$i."'".(($i==$_REQUEST['year2'])?" selected":"")." CLASS=\"select\">".$i."\n";
	}
	echo "</SELECT>\n";

?>
	</div><br>
    <input type="checkbox" name="det" value="1" onClick="detailed(this)">��������� ����������<br>
    <div id="detail" style="display:none" align="center"><br>
        <select name="det_id">
		<?
			$query = "SELECT DISTINCT `checked_by` FROM `law_checks`;";
            $rs = mysql_query($query);
            while (list($id) = mysql_fetch_row($rs))
            	{
					$query = "SELECT `user_name` FROM `site_users` WHERE `id` = '".$id."' LIMIT 1;";
//					echo ($query);
                	$rz = mysql_query($query) or die(mysql_error());
                    $temp = mysql_fetch_row($rz);
		            $t_nick = $temp[0];
                    if ($t_nick <> '') {echo ("<option value='".$id."'>".$t_nick."</option>");}
                }
        ?>
        </select>
		<br>��������:
        <br>
        <select name="det_chk">
        	<option value="all" selected>���</option>
        	<option value="0">�������</option>
        	<option value="1">������� (12 �����)</option>
        	<option value="2">������� (1 ���)</option>
        </select>
    </div>
    <br><input type="submit" name="Submit" value="��������">
</form>
</center>
<?
if ($_REQUEST['det'] !== '1')
	{
		$t1 = $_REQUEST['year1']."-".$_REQUEST['month1']."-".$_REQUEST['day1']." 00:00:00";
        $per_start = strtotime($t1);
		$t2 = $_REQUEST['year2']."-".$_REQUEST['month2']."-".$_REQUEST['day2']." 23:59:59";
        $per_end = strtotime($t2);
        if ($per_start == "" || $per_end == "")
		    {
        		?>
<!--          <script>
            alert("�������� ����!");
            top.location='?act=law_stats';
          </script>
-->
		        <?
                $per_start = time() - 604800;
                $per_end = time();
		    }
        if ($intrvl == 'today')
        	{
				$dt = "WHERE `time2` > '".$today_start."'";
                $tx = "�� �������";
            }
        elseif ($intrvl == 'week')
        	{
				$dt = "WHERE `time2` > '".$week_start."' AND `time2` < '".$week_end."'";
                $tx = "�� 7 ����";
            }
        elseif ($intrvl == 'interval')
        	{
				$dt = "WHERE `time2` > '".$per_start."' AND `time2` < '".$per_end."'";
                $tx = "�� ������ � ".date('d M Y, H:i:s', $per_start)." �� ".date('d M Y, H:i:s', $per_end);
            }
//Money
        $mnt = 0;
        $query = "SELECT `id` FROM `law_checks` ".$dt." AND `urgent` = '0' AND `payed` = '1' AND `status` = '100';";
        $rs = mysql_query($query) or die(mysql_error());
        $tmp = mysql_num_rows($rs) * 10;
		$urg0 = mysql_num_rows($rs);
        $mnt = $mnt+$tmp;
        $query = "SELECT `id` FROM `law_checks` ".$dt." AND `urgent` = '1' AND `payed` = '1' AND `status` = '100';";
        $rs = mysql_query($query) or die(mysql_error());
        $tmp = mysql_num_rows($rs) * 100;
        $urg1 = mysql_num_rows($rs);
        $mnt = $mnt+$tmp;
        $query = "SELECT `id` FROM `law_checks` ".$dt." AND `urgent` = '2' AND `payed` = '1' AND `status` = '100';";
        $rs = mysql_query($query) or die(mysql_error());
        $tmp = mysql_num_rows($rs) * 3100;
        $urg2 = mysql_num_rows($rs);
        $mnt = $mnt+$tmp;
/*
        $query = "SELECT `id` FROM `law_checks` ".$dt." AND `urgent` = '3' AND `payed` = '1';";
        $rs = mysql_query($query) or die(mysql_error());
        $tmp = mysql_num_rows($rs) * 100;
        $urg3 = mysql_num_rows($rs);
        $mnt = $mnt+$tmp;
*/
        $urg_all = $urg0+$urg1+$urg2+$urg3;
//End Money
//Employees
		$query = "SELECT DISTINCT `checked_by` FROM `law_checks` ".$dt.";";
        $rs = mysql_query($query) or die(mysql_error());
        echo ("<center><b>���������� �������� ".$tx."</b></center>");
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
                echo ("���������� �������� �� �����������:<br>");
                for ($i = 0; $i < $count; $i++)
                	{
						echo (" <b>".$emp[$i]['nick']."</b> - <b>".$emp[$i]['chks']."</b> (������� - ".$emp[$i]['chks_reg'].", 12 ����� - ".$emp[$i]['chks_12h'].", 1 ��� - ".$emp[$i]['chks_1h'].")<br>");
                    }
                echo ("<br>����� �������� - <b>".$urg_all."</b> (�� ����� <b>".$mnt."</b> �����)");
                echo ("<br>������� �������� - <b>".$urg0."</b> (�� ����� <b>".($urg0*10)."</b> �����)");
                echo ("<br>������� �������� (12 �����) - <b>".$urg1."</b> (�� ����� <b>".($urg1*100)."</b> �����)");
                echo ("<br>������� �������� (1 ���) - <b>".$urg2."</b> (�� ����� <b>".($urg2*300)."</b> �����)");
//                echo ("<br>������� �������� (2 ����) - <b>".$urg3."</b> (�� ����� <b>".($urg3*100)."</b> �����)");
            }
        else
        	{
            	echo ("� ��������� ������ �������� �� ����������������");
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
<!--          <script>
            alert("�������� ����!");
            top.location='?act=law_stats';
          </script>
-->
		        <?
                $per_start = time() - 604800;
                $per_end = time();
		    }
        if ($intrvl == 'today')
        	{
				$dt = "WHERE `time2` > '".$today_start."'";
                $tx = "�� �������";
            }
        elseif ($intrvl == 'week')
        	{
				$dt = "WHERE `time2` > '".$week_start."' AND `time2` < '".$week_end."'";
                $tx = "�� 7 ����";
            }
        elseif ($intrvl == 'interval')
        	{
				$dt = "WHERE `time2` > '".$per_start."' AND `time2` < '".$per_end."'";
                $tx = "�� ������ � ".date('d M Y, H:i:s', $per_start)." �� ".date('d M Y, H:i:s', $per_end);
            }
	        $query = "SELECT `user_name` FROM `site_users` WHERE `id` = '".$_REQUEST['det_id']."' LIMIT 1;";
    	    $rz = mysql_query($query) or die(mysql_error());
        	$temp = mysql_fetch_row($rz);
			$u_nick = $temp[0];
    	    $u_id = $_REQUEST['det_id'];
        	echo ("<center><b>���������� �������� ".$tx.", ������������� ����������� ".$u_nick."</b></center><br><br>");
	        $query = "SELECT `nick`, `urgent`, `time1`, `time2` FROM `law_checks` ".$dt." AND `checked_by` = '".$_REQUEST['det_id']."'".$chk." ORDER BY `time2`;";
    	    $rs = mysql_query($query) or die(mysql_error());
            ?>
            <table width="90%"  border="0" align="center" cellpadding="3" cellspacing="2">
              <tr align="center">
			    <td bgcolor=#F4ECD4><b>���</b></td>
			    <td bgcolor=#F4ECD4><b>���� ������</b></td>
			    <td bgcolor=#F4ECD4><b>��� ������</b></td>
			    <td bgcolor=#F4ECD4><b>���� ��������</b></td>
			    <td bgcolor=#F4ECD4><b>�� ����� �����</b></td>
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
            	echo ("� ��������� ������ �������� �� ����������������");
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