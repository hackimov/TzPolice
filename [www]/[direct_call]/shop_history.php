<?

require("../_modules/functions.php");
require("../_modules/auth.php");
if (AuthUserGroup==100)
{
$buildings = array();
$SQL = "SELECT * FROM buildings";
$r=mysql_query($SQL);
while ($d=mysql_fetch_array($r))
{
	$buildings[$d['id']] = $d['name'];
	$build_id[$d['full_name']] = $d['id'];
	$build_type[$d['id']] = $d['type'];
}
error_reporting(0);
?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<head>
  <title>You're not supposed to see this =)</title>
<LINK href="../_modules/tzpol_css.css" rel="stylesheet" type="text/css">
<?include("../_modules/java.php")?>
</head>
<body bgcolor="#EBDFB7" text="#455600" alink="#0D3AB4" link="#0D3AB4" vlink="#0D3AB4">
<table width="90%"  border="0" align="center" cellpadding="3" cellspacing="2">
  <tr>
    <td align="center">
<?
if (!isset($_REQUEST['interval']))
	{
    	$intrvl = "week";
        $tx = "�� 7 ����";
    }
else
	{
    	$intrvl = $_REQUEST['interval'];
    }
if ($intrvl == "interval")
	{
		$t1 = $_REQUEST['year1']."-".$_REQUEST['month1']."-".$_REQUEST['day1']." 00:00:00";
        $per_start = strtotime($t1);
		$t2 = $_REQUEST['year2']."-".$_REQUEST['month2']."-".$_REQUEST['day2']." 23:59:59";
        $per_end = strtotime($t2);
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
<?
 if (isset($_REQUEST['item']) && $_REQUEST['item'] !== 'all' && $_REQUEST['item'] !== 'all_traders')
	{
		$query = "SELECT `item_name` FROM `shop_items` WHERE `id` = '".$_REQUEST['item']."' LIMIT 1;";
        $rs = mysql_query($query) or die (mysql_error());
        list($name) = mysql_fetch_row($rs);
        if ($intrvl == 'today')
        	{
				$dt = "AND `date` > '".$today_start."'";
                $tx = "�� �������";
            }
        elseif ($intrvl == 'week')
        	{
				$dt = "AND `date` > '".$week_start."' AND `date` < '".$week_end."'";
                $tx = "�� 7 ����";
            }
        elseif ($intrvl == 'interval')
        	{
				$dt = "AND `date` > '".$per_start."' AND `date` < '".$per_end."'";
                $tx = "�� ������ � ".date('d M Y, H:i:s', $per_start)." �� ".date('d M Y, H:i:s', $per_end);
            }
		if ($_REQUEST['k'] == "t")
        	{
				$query = "SELECT * FROM `traders_history` WHERE `item` = '".$_REQUEST['item']."' ".$dt." LIMIT 1;";
            }
        else
        	{
				$query = "SELECT * FROM `shop_history` WHERE `item` = '".$_REQUEST['item']."' ".$dt." LIMIT 1;";
            }
        $rs = mysql_query($query) or die (mysql_error());
        if (mysql_num_rows($rs) > 0)
        	{
				?>
<center>������� ����� �� <b><?=$name?></b> <?=$tx?></center><br>
<form name="stat" method="POST" action="">
<inpet type="hidden" name="item" value="<?=$_REQUEST['item']?>">
	  <div align="center">����� ��:
	      <select name="interval" onChange="period(this)">
            <option value="today" selected>�������</option>
            <option value="week">7 ����</option>
            <option value="interval">������</option>
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
                       	<option value="1" selected>������</option><option value="2">�������</option><option value="3">�����</option><option value="4">������</option><option value="5">���</option>
                        <option value="6">����</option><option value="7">����</option><option value="8">�������</option><option value="9">��������</option><option value="10">�������</option>
                        <option value="11">������</option><option value="12">�������</option>
					  </select>
				<select name="year1">
                <option value="2005">2005</option>                <option value="2006" selected>2006</option>
                </select>
					<br>��<br>
	            <select name="day2">
						<?
                        for ($i = 1; $i <= 30; $i++) {
						    echo ("<option value=\"$i\">$i</option>");
							}
                        ?>
                	<option value="31" selected>31</option>
               </select>
				<select name="month2">
                       	<option value="1">������</option><option value="2">�������</option><option value="3">�����</option><option value="4">������</option><option value="5">���</option>
                        <option value="6">����</option><option value="7">����</option><option value="8">�������</option><option value="9">��������</option><option value="10">�������</option>
                        <option value="11">������</option><option value="12" selected>�������</option>
					  </select>
				<select name="year2">
                <option value="2005">2005</option>                <option value="2006" selected>2006</option>
                </select>

	</div><br>
                <input type="submit" name="submit" value="��������">
    </form>
<table width=97% cellpadding=5>
<tr bgcolor=#F4ECD4 align=center>
<td><b>����</b></td>
<td><b>����������</b></td>
<td><b>��������</b></td>
<td><b>�����</b></td>
<td><b>���������</b></td>
</tr>
                <?
		        while (list($c_item, $c_date, $c_cus, $c_quan, $c_plant, $c_prod) = mysql_fetch_row($rs))
					{
			    ?>
<tr align="center">
<td><?=date("d.m.Y H:i", $c_date)?></td>
<td><?=$c_quan?></td>
<td><?
$q = "SELECT `user_name` FROM `site_users` WHERE `id` = '".$c_cus."'";
$r = mysql_query($q);
list($cus_n) = mysql_fetch_row($r);
echo ($cus_n);
?></td>
<td><?=$buildings[$c_plant]?></td>
<td><?=$c_prod?></td>
</tr>
  				<?}?>


<?
            }
        else
        	{
            	echo ("������. ���� �� �������� � id <b>".$_REQUEST['item']."</b> ".$tx." �� �������.");
				?>


<form name="stat" method="POST" action="">
<inpet type="hidden" name="item" value="<?=$_REQUEST['item']?>">
	  <div align="center">����� ��:
	      <select name="interval" onChange="period(this)">
            <option value="today" selected>�������</option>
            <option value="week">7 ����</option>
            <option value="interval">������</option>
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
                       	<option value="1" selected>������</option><option value="2">�������</option><option value="3">�����</option><option value="4">������</option><option value="5">���</option>
                        <option value="6">����</option><option value="7">����</option><option value="8">�������</option><option value="9">��������</option><option value="10">�������</option>
                        <option value="11">������</option><option value="12">�������</option>
					  </select>
				<select name="year1">
                <option value="2005" selected>2005</option>
                </select>
					<br>��<br>
	            <select name="day2">
						<?
                        for ($i = 1; $i <= 30; $i++) {
						    echo ("<option value=\"$i\">$i</option>");
							}
                        ?>
                	<option value="31" selected>31</option>
               </select>
				<select name="month2">
                       	<option value="1">������</option><option value="2">�������</option><option value="3">�����</option><option value="4">������</option><option value="5">���</option>
                        <option value="6">����</option><option value="7">����</option><option value="8">�������</option><option value="9">��������</option><option value="10">�������</option>
                        <option value="11">������</option><option value="12" selected>�������</option>
					  </select>
				<select name="year2">
                <option value="2005" selected>2005</option>
                </select>

	</div><br>
                <input type="submit" name="submit" value="��������">
    </form>







                <?
            }
    }
elseif (isset($_REQUEST['item']) && $_REQUEST['item'] == 'all')
	{
        list($name) = mysql_fetch_row($rs);
        if ($intrvl == 'today')
        	{
				$dt = "WHERE `date` >= '".$today_start."'";
                $tx = "�� �������";
            }
        elseif ($intrvl == 'week')
        	{
				$dt = "WHERE `date` >= '".$week_start."' AND `date` <= '".$week_end."'";
                $tx = "�� 7 ����";
            }
        elseif ($intrvl == 'interval')
        	{
				$dt = "WHERE `date` >= '".$per_start."' AND `date` <= '".$per_end."'";
                $tx = "�� ������ � ".date('d M Y, H:i:s', $per_start)." �� ".date('d M Y, H:i:s', $per_end);
            }
		$query = "SELECT DISTINCT item FROM shop_history ".$dt.";";
        $items = mysql_query($query) or die (mysql_error());
?>
<form name="stat" method="POST" action="">
<inpet type="hidden" name="item" value="<?=$_REQUEST['item']?>">
	  <div align="center">����� ��:
	      <select name="interval" onChange="period(this)">
            <option value="today" selected>�������</option>
            <option value="week">7 ����</option>
            <option value="interval">������</option>
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
                       	<option value="1" selected>������</option><option value="2">�������</option><option value="3">�����</option><option value="4">������</option><option value="5">���</option>
                        <option value="6">����</option><option value="7">����</option><option value="8">�������</option><option value="9">��������</option><option value="10">�������</option>
                        <option value="11">������</option><option value="12">�������</option>
					  </select>
				<select name="year1">
                <option value="2005" selected>2005</option>
                </select>
					<br>��<br>
	            <select name="day2">
						<?
                        for ($i = 1; $i <= 30; $i++) {
						    echo ("<option value=\"$i\">$i</option>");
							}
                        ?>
                	<option value="31" selected>31</option>
               </select>
				<select name="month2">
                       	<option value="1">������</option><option value="2">�������</option><option value="3">�����</option><option value="4">������</option><option value="5">���</option>
                        <option value="6">����</option><option value="7">����</option><option value="8">�������</option><option value="9">��������</option><option value="10">�������</option>
                        <option value="11">������</option><option value="12" selected>�������</option>
					  </select>
				<select name="year2">
                <option value="2005" selected>2005</option>
                </select>

	</div><br>
                <input type="submit" name="submit" value="��������">
    </form>
<?
        if (mysql_num_rows($items) > 0)
        	{
				?>
<center>������� ����� <?=$tx?></center><br>
    <div align="left">
                <?
		        while (list($c_item) = mysql_fetch_row($items))
					{
                    	$query = "SELECT `item_name` FROM `shop_items` WHERE `id` = '".$c_item."' LIMIT 1;";
				        $rs = mysql_query($query) or die (mysql_error());
				        list($name) = mysql_fetch_row($rs);
                        $query = "SELECT item, SUM(quantity) FROM shop_history ".$dt." AND item = ".$c_item." GROUP BY item";
                        $res = mysql_query($query);
                        list($i, $sum) = mysql_fetch_row($res);
                        echo ($name." - ".$sum." ��.<br>");
  					}
            }
        else
        	{
            	echo ("������. ���� ".$tx." �� �������.");
            }
    }
elseif (isset($_REQUEST['item']) && $_REQUEST['item'] == 'all_traders')
	{
        list($name) = mysql_fetch_row($rs);
        if ($intrvl == 'today')
        	{
				$dt = "WHERE `date` > '".$today_start."'";
                $tx = "�� �������";
            }
        elseif ($intrvl == 'week')
        	{
				$dt = "WHERE `date` > '".$week_start."' AND `date` < '".$week_end."'";
                $tx = "�� 7 ����";
            }
        elseif ($intrvl == 'interval')
        	{
				$dt = "WHERE `date` > '".$per_start."' AND `date` < '".$per_end."'";
                $tx = "�� ������ � ".date('d M Y, H:i:s', $per_start)." �� ".date('d M Y, H:i:s', $per_end);
            }
		$query = "SELECT DISTINCT item FROM traders_history ".$dt.";";
        $items = mysql_query($query) or die (mysql_error());
        if (mysql_num_rows($items) > 0)
        	{
				?>
<center>������� ����� <?=$tx?></center><br>
<form name="stat" method="POST" action="">
<inpet type="hidden" name="item" value="<?=$_REQUEST['item']?>">
	  <div align="center">����� ��:
	      <select name="interval" onChange="period(this)">
            <option value="today" selected>�������</option>
            <option value="week">7 ����</option>
            <option value="interval">������</option>
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
                       	<option value="1" selected>������</option><option value="2">�������</option><option value="3">�����</option><option value="4">������</option><option value="5">���</option>
                        <option value="6">����</option><option value="7">����</option><option value="8">�������</option><option value="9">��������</option><option value="10">�������</option>
                        <option value="11">������</option><option value="12">�������</option>
					  </select>
				<select name="year1">
                <option value="2005" selected>2005</option>
                </select>
					<br>��<br>
	            <select name="day2">
						<?
                        for ($i = 1; $i <= 30; $i++) {
						    echo ("<option value=\"$i\">$i</option>");
							}
                        ?>
                	<option value="31" selected>31</option>
               </select>
				<select name="month2">
                       	<option value="1">������</option><option value="2">�������</option><option value="3">�����</option><option value="4">������</option><option value="5">���</option>
                        <option value="6">����</option><option value="7">����</option><option value="8">�������</option><option value="9">��������</option><option value="10">�������</option>
                        <option value="11">������</option><option value="12" selected>�������</option>
					  </select>
				<select name="year2">
                <option value="2005" selected>2005</option>
                </select>

	</div><br>
                <input type="submit" name="submit" value="��������">
    </form>
    <div align="left">
                <?
		        while (list($c_item) = mysql_fetch_row($items))
					{
                    	$query = "SELECT `item_name` FROM `shop_items` WHERE `id` = '".$c_item."' LIMIT 1;";
				        $rs = mysql_query($query) or die (mysql_error());
				        list($name) = mysql_fetch_row($rs);
                        $query = "SELECT item, SUM(quantity) FROM traders_history ".$dt." AND item = ".$c_item." GROUP BY item";
                        $res = mysql_query($query);
                        list($i, $sum) = mysql_fetch_row($res);
                        echo ($name." - ".$sum." ��.<br>");
  					}
            }
        else
        	{
            	echo ("������. ���� ".$tx." �� �������.");
            }
    }
?>

	</div>
    </td>
  </tr>
</table>
</body>
</html>
<?}?>