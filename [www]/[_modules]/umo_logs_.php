<?

if (AuthStatus==1 && substr_count(AuthUserRestrAccess, "umo_logs") > 0)

{
/*
types
1 - plant
2 - lab
3 - bank cell
4 - truck

=======

actions plant
1 - made item
2 - used resourses
3 - given away
4 - brought to warehouse

actions lab
1 - made item design
2 - used resourses
3 - given away
4 - brought to warehouse

actions cell
1 - given away
2 - brought to cell
3 - money transferred TO cell
4 - money transferred FROM cell
5 - money brought to cell
6 - money taken from cell

actions truck
1 - give in building
2 - take from building
3 - give to player
4 - take from player

*/
?>

<h1>����� ����� ���</h1>

<center>
<form name="parse_logs" method="post" action="?act=umo_logs">
  ����:
  <input name="date" type="text" size="16" value="<?=date('d.m.Y', time())?>">
  �������� ����:
  <select name="select">
<?
//Plants
  $query = "SELECT * FROM `buildings` WHERE `type` = 1";
  $result = mysql_query($query) or die (mysql_error());
  if (mysql_num_rows($result) > 0 ) {
?>
<option value="label">*** ������ ***</option>
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
<option value="label">*** ����������� ***</option>
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
<option value="label">*** ������ ***</option>
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
<option value="label">*** ��������� ***</option>
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
  <br>
  ���<br>
  <textarea name="log" cols="60" rows="6" wrap="VIRTUAL"></textarea>
  <br>
  <input type="submit" name="Submit" value="����������">
</form>
</center>

<?


// Logs parsing
if (isset($_REQUEST['log']))
	{
		$query = "SELECT `type` FROM `buildings` WHERE `id` = '".$_REQUEST['select']."' LIMIT 1;";
		$res = mysql_fetch_row(mysql_query($query));
		$type = $res[0];
		$bid = $_REQUEST['select'];
        $log_date = $_REQUEST['date'];
        $log_date = str_replace(".", "", $log_date);
        echo ($log_date."<br>");
        echo (strlen($log_date)."<br>");
        if (strlen($log_date) < 8)	{die ("������������ ����. ������� ���� � ������� ��.��.���� <b>������ � ������</b>");}
    	$lines = explode("\n", $_REQUEST['log']);
        if ($type == 1)
        	{
	            foreach ($lines as $line_num => $line)
	                {
                    	$line = stripslashes($line);
//Plant
	                    if (strpos($line, "������� ���������� �������"))
	                       {
	                            $cur = $line."[1]";
	                            $what = sub($cur, "������� ���������� ������� ", "[");
	                            $what = str_replace("\n", "", $what);
	                            $what = str_replace("\r", "", $what);
	                            $quan = sub($cur, $what, "]");
	                            $quan = str_replace("[", "", $quan);
	                            if ($quan == 0) {$quan = 1;}
	                            $made[$what] += $quan;
	                       }
	                    if (strpos($line, "���� ������������ ��� ������������"))
	                        {
	                            $cur = sub($line, "������� ,", " ����");
	                            $tmp = explode(",", $cur);
	                                foreach ($tmp as $key => $value)
	                                    {
	                                        $cur = str_replace("]", "", $value);
	                                        $cura = explode("[", $cur);
	                                        $what = $cura[0];
	                                        $what = str_replace("\n", "", $what);
	                                        $what = str_replace("\r", "", $what);
	                                        $quan = $cura[1];
	                                        if ($quan == 0) {$quan = 1;}
	                                        $res_used[$what] += $quan;
	                                   }
	                        }
	                   if (strpos($line, "������ �� ������"))
	                        {
	                            $cur = $line."[1]";
	                            $what = sub($cur, "������ �� ������ ", "[");
	                            $what = str_replace("\n", "", $what);
	                            $what = str_replace("\r", "", $what);
	                            $quan = sub($cur, $what, "]");
	                            $quan = str_replace("[", "", $quan);
	                            if ($quan == 0) {$quan = 1;}
	                            $who = sub($cur, " '", "' ");
	                            $take[$who][$what] += $quan;
	                        }
	                   if (strpos($line, "������� �� �����"))
	                        {
	                            $cur = $line."[1]";
	                            $what = sub($cur, "������� �� ����� ", "[");
	                            $what = str_replace("\n", "", $what);
	                            $what = str_replace("\r", "", $what);
	                            $quan = sub($cur, $what, "]");
	                            $quan = str_replace("[", "", $quan);
	                            if ($quan == 0) {$quan = 1;}
	                            $who = sub($cur, " '", "' ");
	                            $bring[$who][$what] += $quan;
	                        }
//End plant
            		}
//Add logs to base
	                foreach ($bring as $unick => $pers)
	                    {
	                        foreach ($pers as $item => $quan)
	                            {
	                                $query = "INSERT INTO `logs_plant` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '4', '".$item."', '".$quan."', '".$unick."', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                            }
	                    }
	                foreach ($take as $unick => $pers)
	                    {
	                        foreach ($pers as $item => $quan)
	                            {
	                                $query = "INSERT INTO `logs_plant` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '3', '".$item."', '".$quan."', '".$unick."', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                            }
	                    }
	                foreach ($res_used as $res => $quan)
	                    {
	                                $query = "INSERT INTO `logs_plant` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '2', '".$res."', '".$quan."', '', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                    }
	                foreach ($made as $item => $quan)
	                    {
	                                $query = "INSERT INTO `logs_plant` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '1', '".$item."', '".$quan."', '', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                    }
//Logs added
        	}
		if ($type == 2)
        	{
	            foreach ($lines as $line_num => $line)
	                {
                    	$line = stripslashes($line);
//Lab
	                    if (strpos($line, "������������ ������ ������"))
	                       {
	                            $cur = $line."<<<";
	                            $what = sub($cur, "��� ", "<<<");
	                            $what = str_replace("\n", "", $what);
	                            $what = str_replace("\r", "", $what);
	                            $made[$what]++;
	                       }
	                    if (strpos($line, "���� ������������ ��� ������������"))
	                        {
	                            $cur = sub($line, "������� ", " ����");
	                            $tmp = explode(",", $cur);
	                                foreach ($tmp as $key => $value)
	                                    {
	                                        $cur = str_replace("]", "", $value);
	                                        $cura = explode("[", $cur);
	                                        $what = $cura[0];
	                                        $what = str_replace("\n", "", $what);
	                                        $what = str_replace("\r", "", $what);
	                                        $quan = $cura[1];
	                                        if ($quan == 0) {$quan = 1;}
	                                        $res_used[$what] += $quan;
	                                   }
	                        }
	                   if (strpos($line, "������ �� ������ Item design"))
	                        {
	                            $cur = $line."[1]<";
	                            $what = sub($cur, "������ �� ������ ", "[1]<");
	                            $what = str_replace("\n", "", $what);
	                            $what = str_replace("\r", "", $what);
	                            $who = sub($cur, " '", "' ");
	                            $take[$who][$what] ++;
	                        }
	                   elseif (strpos($line, "������ �� ������"))
	                        {
	                            $cur = $line."[1]";
	                            $what = sub($cur, "������ �� ������ ", "[");
	                            $what = str_replace("\n", "", $what);
	                            $what = str_replace("\r", "", $what);
	                            $quan = sub($cur, $what, "]");
	                            $quan = str_replace("[", "", $quan);
	                            if ($quan == 0) {$quan = 1;}
	                            $who = sub($cur, " '", "' ");
	                            $take[$who][$what] += $quan;
	                        }
	                   if (strpos($line, "������� �� ����� Item design"))
	                        {
	                            $cur = $line."[1]<";
	                            $what = sub($cur, "������� �� ����� ", "[1]<");
	                            $what = str_replace("\n", "", $what);
	                            $what = str_replace("\r", "", $what);
	                            $who = sub($cur, " '", "' ");
	                            $bring[$who][$what] ++;
	                        }
	                   elseif (strpos($line, "������� �� �����"))
	                        {
	                            $cur = $line."[1]";
	                            $what = sub($cur, "������� �� ����� ", "[");
	                            $what = str_replace("\n", "", $what);
	                            $what = str_replace("\r", "", $what);
	                            $quan = sub($cur, $what, "]");
	                            $quan = str_replace("[", "", $quan);
	                            if ($quan == 0) {$quan = 1;}
	                            $who = sub($cur, " '", "' ");
	                            $bring[$who][$what] += $quan;
	                        }
//End lab
				}
//Add logs to base
	                foreach ($bring as $unick => $pers)
	                    {
	                        foreach ($pers as $item => $quan)
	                            {
	                                $query = "INSERT INTO `logs_lab` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '4', '".$item."', '".$quan."', '".$unick."', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                            }
	                    }
	                foreach ($take as $unick => $pers)
	                    {
	                        foreach ($pers as $item => $quan)
	                            {
	                                $query = "INSERT INTO `logs_lab` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '3', '".$item."', '".$quan."', '".$unick."', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                            }
	                    }
	                foreach ($res_used as $res => $quan)
	                    {
	                                $query = "INSERT INTO `logs_lab` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '2', '".$res."', '".$quan."', '', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                    }
	                foreach ($made as $item => $quan)
	                    {
	                                $query = "INSERT INTO `logs_lab` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '1', '".$item."', '".$quan."', '', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                    }
//Logs added
      	}
		if ($type == 3)
        	{
	            foreach ($lines as $line_num => $line)
	                {
                    	$line = stripslashes($line);
//Cell
	                    if (strpos($line, "������� � ������ ��������: Item design"))
	                       {
	                            $cur = $line."[1]<";
	                            $what = sub($cur, "������� � ������ ��������: ", "[1]<");
	                            $what = str_replace("\n", "", $what);
	                            $what = str_replace("\r", "", $what);
	                            $who = sub($cur, " '", "' ");
	                            $bring[$who][$what] ++;
	                       }
	                    elseif (strpos($line, "������� � ������ ��������:"))
	                       {
	                            $cur = $line."[1]<";
	                            $what = sub($cur, "������� � ������ ��������: ", "[");
	                            $what = str_replace("\n", "", $what);
	                            $what = str_replace("\r", "", $what);
	                            $quan = sub($cur, $what, "]");
	                            $quan = str_replace("[", "", $quan);
	                            if ($quan == 0) {$quan = 1;}
	                            $who = sub($cur, " '", "' ");
	                            $bring[$who][$what] += $quan;
	                       }
	                   if (strpos($line, "������ �� ������ ��������: Item design"))
	                        {
	                            $cur = $line."[1]<";
	                            $what = sub($cur, "������ �� ������ ��������: ", "[1]<");
	                            $what = str_replace("\n", "", $what);
	                            $what = str_replace("\r", "", $what);
	                            $who = sub($cur, " '", "' ");
	                            $take[$who][$what] ++;
	                        }
	                   elseif (strpos($line, "������ �� ������ ��������:"))
	                        {
	                            $cur = $line."[1]<";
	                            $what = sub($cur, "������ �� ������ ��������: ", "[");
	                            $what = str_replace("\n", "", $what);
	                            $what = str_replace("\r", "", $what);
	                            $quan = sub($cur, $what, "]");
	                            $quan = str_replace("[", "", $quan);
	                            if ($quan == 0) {$quan = 1;}
	                            $who = sub($cur, " '", "' ");
	                            $take[$who][$what] += $quan;
	                        }
	                   if (strpos($line, "�� ��� ���� ������ ������"))
	                        {
	                            $sum = sub($line, "�� �����: ", ", ");
	                            $from = sub($line, "�� ����� ", " �� ��� ����");
	                            $plus_coins[$from] += $sum;
	                        }
	                   if (strpos($line, "������ �� ������ ������"))
	                        {
	                            $sum = sub($line, "Coins[", "], ");
	                            $who = sub($line, "��� '", "' ���");
	                            $take_coins[$who] += $sum;
	                        }
	                   if (strpos($line, "������� � ������ ������"))
	                        {
	                            $sum = sub($line, "Coins[", "], ");
	                            $who = sub($line, "��� '", "' ���");
	                            $bring_coins[$who] += $sum;
	                        }
	                    if (strpos($line, "������� �� ����"))
	                        {
	                            $sum = sub($line, "�� �����: ", ", ");
	                            $to = sub($line, "������� �� ���� ", " ������ ������");
	                            $transfer_coins[$to] += $sum;
	                        }
//End cell
				}
//Add logs to base
	                foreach ($bring as $unick => $pers)
	                    {
	                        foreach ($pers as $item => $quan)
	                            {
	                                $query = "INSERT INTO `logs_cell` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '2', '".$item."', '".$quan."', '".$unick."', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                            }
	                    }
	                foreach ($take as $unick => $pers)
	                    {
	                        foreach ($pers as $item => $quan)
	                            {
	                                $query = "INSERT INTO `logs_cell` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '1', '".$item."', '".$quan."', '".$unick."', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                            }
	                    }
	                foreach ($bring_coins as $nick => $quan)
	                    {
	                                $query = "INSERT INTO `logs_cell` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '2', 'Coins', '".$quan."', '".$nick."', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                    }
	                foreach ($take_coins as $nick => $quan)
	                    {
	                                $query = "INSERT INTO `logs_cell` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '1', 'Coins', '".$quan."', '".$nick."', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                    }
	                foreach ($transfer_coins as $nick => $quan)
	                    {
	                                $query = "INSERT INTO `logs_cell` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '4', 'Coins', '".$quan."', '".$nick."', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                    }
	                foreach ($plus_coins as $nick => $quan)
	                    {
	                                $query = "INSERT INTO `logs_cell` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '3', 'Coins', '".$quan."', '".$nick."', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                    }
//Logs added
      	}
		if ($type == 4)
        	{
	            foreach ($lines as $line_num => $line)
	                {
                    	$line = stripslashes($line);
//Truck
	                    if (strpos($line, "�������� ��������: Item design"))
	                       {
	                            $what = sub($line, "�������� ��������: ", " � ������ ");
	                            $what = str_replace("\n", "", $what);
	                            $what = str_replace("\r", "", $what);
	                            $where = sub($line, " '", "'");
	                            $take_b[$where][$what] ++;
	                       }
	                    elseif (strpos($line, "�������� ��������:"))
	                       {
	                            $tmp = sub($line, "�������� ��������: ", " � ������ ");
	                            $tmp = explode("[", $tmp);
	                            $what = $tmp[0];
	                            $quan = $tmp[1];
	                            $quan = str_replace("]", "", $quan);
	                            $quan = str_replace("[", "", $quan);
	                            if ($quan == 0) {$quan = 1;}
	                            $where = sub($line, " '", "' ");
	                            $take_b[$where][$what] += $quan;
	                       }
	                    if (strpos($line, "������ ��������: Item design"))
	                       {
	                            $what = sub($line, "������ ��������: ", " � ������ ");
	                            $what = str_replace("\n", "", $what);
	                            $what = str_replace("\r", "", $what);
	                            $where = sub($line, " '", "'");
	                            $give_b[$where][$what] ++;
	                       }
	                    elseif (strpos($line, "������ ��������:"))
	                       {
	                            $tmp = sub($line, "������ ��������: ", " � ������ ");
	                            $tmp .= "[1]";
	                            $tmp = explode("[", $tmp);
	                            $what = $tmp[0];
	                            $quan = $tmp[1];
	                            $quan = str_replace("]", "", $quan);
	                            if ($quan == 0) {$quan = 1;}
	                            $where = sub($line, " '", "'");
	                            $give_b[$where][$what] += $quan;
	                       }
	                    if (strpos($line, "��������:"))
	                       {
	                            $wht = sub($line, "��������: ", " �� ");
	                            $wht = str_replace("\n", "", $wht);
	                            $wht = str_replace("\r", "", $wht);
	                            $where = sub($line, " '", "'");
	                            if (strpos($wht, ","))
	                                {
	                                    $tmp = explode(",", $wht);
	                                    foreach ($tmp as $key => $value)
	                                        {
	                                            if (!strpos($value, "em design"))
	                                                {
	                                                    $cur = str_replace("]", "", $value);
	                                                    $cura = explode("[", $cur);
	                                                    $what = $cura[0];
	                                                    $what = str_replace("\n", "", $what);
	                                                    $what = str_replace("\r", "", $what);
	                                                    $quan = $cura[1];
	                                                    if ($quan == 0) {$quan = 1;}
	                                                }
	                                            else
	                                                {
	                                                    $what = $value;
	                                                    $quan = 1;
	                                                }
	                                            $take_p[$where][$what] += $quan;
	                                       }
	                                }
	                            elseif (strpos($wht, "Item design"))
	                                {
	                                    $take_p[$where][$wht] ++;
	                                }
	                            else
	                                {
	                                    $cura = explode("[", $wht);
	                                    $what = $cura[0];
	                                    $what = str_replace("\n", "", $what);
	                                    $what = str_replace("\r", "", $what);
	                                    $quan = $cura[1];
	                                    if ($quan == 0) {$quan = 1;}
	                                    $take_p[$where][$what] += $quan;
	                                }
	                       }
	                    elseif (strpos($line, "�������:"))
	                       {
	                            $wht = sub($line, "�������: ", " � ");
	                            $wht = str_replace("\n", "", $wht);
	                            $wht = str_replace("\r", "", $wht);
	                            $where = sub($line, " '", "'");
	                            if (strpos($wht, ","))
	                                {
	                                    $tmp = explode(",", $wht);
	                                    foreach ($tmp as $key => $value)
	                                        {
	                                            if (!strpos($value, "em design"))
	                                                {
	                                                    $cur = str_replace("]", "", $value);
	                                                    $cura = explode("[", $cur);
	                                                    $what = $cura[0];
	                                                    $what = str_replace("\n", "", $what);
	                                                    $what = str_replace("\r", "", $what);
	                                                    $quan = $cura[1];
	                                                    if ($quan == 0) {$quan = 1;}
	                                                }
	                                            else
	                                                {
	                                                    $what = $value;
	                                                    $quan = 1;
	                                                }
	                                            $give_p[$where][$what] += $quan;
	                                       }
	                                }
	                            elseif (strpos($wht, "Item design"))
	                                {
	                                    $give_p[$where][$wht] ++;
	                                }
	                            else
	                                {
	                                    $cura = explode("[", $wht);
	                                    $what = $cura[0];
	                                    $what = str_replace("\n", "", $what);
	                                    $what = str_replace("\r", "", $what);
	                                    $quan = $cura[1];
	                                    if ($quan == 0) {$quan = 1;}
	                                    $give_p[$where][$what] += $quan;
	                                }
	                       }
//End truck
        			}
//Add logs to base
	                foreach ($give_b as $place => $pers)
	                    {
	                        foreach ($pers as $item => $quan)
	                            {
	                                $query = "INSERT INTO `logs_truck` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '1', '".$item."', '".$quan."', '".$place."', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                            }
	                    }
	                foreach ($give_p as $place => $pers)
	                    {
	                        foreach ($pers as $item => $quan)
	                            {
	                                $query = "INSERT INTO `logs_truck` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '3', '".$item."', '".$quan."', '".$place."', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                            }
	                    }
	                foreach ($take_b as $place => $pers)
	                    {
	                        foreach ($pers as $item => $quan)
	                            {
	                                $query = "INSERT INTO `logs_truck` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '2', '".$item."', '".$quan."', '".$place."', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                            }
	                    }
	                foreach ($take_p as $place => $pers)
	                    {
	                        foreach ($pers as $item => $quan)
	                            {
	                                $query = "INSERT INTO `logs_truck` (`id`, `date`, `action`, `item`, `quantity`, `nick`, `id_build`)
	                                        VALUES ('', '".$log_date."', '4', '".$item."', '".$quan."', '".$place."', '".$bid."');";
	                                mysql_query($query) or die(mysql_error());
	                            }
	                    }
//Logs added

      		}
	}
}
?>