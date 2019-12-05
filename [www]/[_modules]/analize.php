<h1>Логи складов</h1>
<?php
$bgstr[0]="background='i/bgr-grid-sand.gif'";
$bgstr[1]="background='i/bgr-grid-sand1.gif'";
function res($a) {
	switch($a) {
		case 'Coins':			return 'coins';
		case 'Metals':			return 'metals';
		case 'Precious metals':		return 'gold';
		case 'Polymers':			return 'polymers';
		case 'Organic':			return 'organic';
		case 'Venom':			return 'venom';
		case 'Radioactive materials':	return 'radioactive';
		case 'Gems':			return 'gems';
		case 'Silicon':			return 'silicon';
  		case 'Metals (enriched)':			return 'metals';
		case 'Precious metals (enriched)':		return 'gold';
		case 'Polymers (enriched)':			return 'polymers';
		case 'Organic (enriched)':			return 'organic';
		case 'Venom (enriched)':			return 'venom';
		case 'Radioactive materials (enriched)':	return 'radioactive';
		case 'Gems (enriched)':				return 'gems';
		case 'Silicon (enriched)':			return 'silicon';
		default:				return '';
	}
}

$permission=0;
$SQL = "SELECT * FROM build_users WHERE user_id='".AuthUserId."'";
$r = mysql_query($SQL);
if ($d=mysql_fetch_array($r)) $permission=$d['warehouse'];

if((abs(AccessLevel) & AccessShopManage) && $permission) {
?>
<table width='100%' border='0' cellspacing='3' cellpadding='2'>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Лог склада:</strong> </p></td></tr>
<tr><td align=center>

<form method="POST" name=F1>
Дата лога: <input type=text name="dt" value="<?=($_POST['dt']?$_POST['dt']:date("d.m.y",time()))?>" size=10>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Склад: <select name="warehouse">
<?
$SQL = "SELECT * FROM buildings";
$r=mysql_query($SQL);
while ($d=mysql_fetch_array($r)) {
?>
<option value="<?=$d['id']?>"<?=($_POST['warehouse']==$d['id']?' selected':'')?>><?=$d['name']?></option>
<?
}
?>
</select><br>

<textarea rows=6 name=text cols=110>
</textarea>
<br>
<input type="submit" value="Проверить переводы">
</form>

</td></tr>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Результат:</strong> </p></td></tr>
<tr><td>

<?
if (@$_POST['text']) {
	$names = array();
	$lines = explode("\n", $_POST['text']);
	foreach ($lines as $line) {
		$sf = strpos($line, " выложил на склад ");
		if ($sf > 0) {
			$tm = substr($line, 0, 5);
			$ss = strpos($line, "'");
			$name = substr($line, $ss+1, $sf-$ss-3);
			$resource = substr($line, $sf+18, strpos($line, "[")-$sf-18);
			$count = substr($line, strpos($line, "[")+1, -2);
			if (res($resource) != '') {
				$names[$name] = 1;
				if(!($p[$name])) $p[$name] = array();
				$p[$name][] = res($resource);
				$p[$name][] = $count;
				$p[$name][] = $tm;
			}
		}
	}
	$s=mysql_fetch_row(mysql_query("SELECT type FROM buildings WHERE id='".$_POST['warehouse']."'"));
	$day = substr($_POST['dt'],0,2);
	$month = substr($_POST['dt'],3,2);
	$year = '20'.substr($_POST['dt'],6,2);
	foreach (array_keys($names) as $name) {
		$tm = $p[$name][2]; $rs = array();
		for ($i=0; $i<sizeof($p[$name]); $i+=3) {
			if ($tm != $p[$name][$i+2]) {
				$hour = substr($tm,0,2);
				$min = substr($tm,3,2);
				$SQL="SELECT * FROM warehouses WHERE dt='".mktime($hour,$min,0,$month,$day,$year)."' AND from_name='".$name."' AND warehouse='".$_POST['warehouse']."' AND status<2";
				$r=mysql_query($SQL);
				$error = 0;
				if ($d=mysql_fetch_array($r)) {
					foreach (array_keys($rs) as $res) {
						if ($d[$res] != $rs[$res]) {
							$error = 1;
						}
					}
				} else {
					$error = 1;
				}
				$t = "$hour:$min $day.$month.$year <b>$name</b>: ";
				foreach (array_keys($rs) as $res) {
					$t .= " ".$res."[".$rs[$res]."],";
				}
				if ($error) {
					echo $t." - <font color=red><b>перевод не подтвержден</b></font><br>\n";
				} else {
					if ($d['status']==1) {
						$SQL = "UPDATE warehouses SET status=0 WHERE id='".$d['id']."'";
						mysql_query($SQL);
						if ($s[0] != 1) {
							sleep(3);
							mysql_query("INSERT INTO warehouses values('',
								'".time()."',
								'".$d['customer']."',
								'".AuthUserName."',
								'-".$rs['coins']."',
								'-".$rs['metals']."',
								'-".$rs['gold']."',
								'-".$rs['polymers']."',
								'-".$rs['organic']."',
								'-".$rs['venom']."',
								'-".$rs['radioactive']."',
								'-".$rs['gems']."',
								'-".$rs['silicon']."',
								'".$_POST['warehouse']."',
								'0')");
							mysql_query("INSERT INTO warehouses values('',
								'".(time()+1)."',
								'1',
								'".AuthUserName."',
								'".$rs['coins']."',
								'".$rs['metals']."',
								'".$rs['gold']."',
								'".$rs['polymers']."',
								'".$rs['organic']."',
								'".$rs['venom']."',
								'".$rs['radioactive']."',
								'".$rs['gems']."',
								'".$rs['silicon']."',
								'".$_POST['warehouse']."',
								'0')");
						}
					}
					echo $t." - <font color=green>перевод подтвержден</font><br>\n";
				}
				$tm = $p[$name][$i+2];
				$rs = array();
			}
			$rs[$p[$name][$i]] += $p[$name][$i+1];
		}
		$hour = substr($tm,0,2);
		$min = substr($tm,3,2);
		$SQL="SELECT * FROM warehouses WHERE dt='".mktime($hour,$min,0,$month,$day,$year)."' AND from_name='".$name."' AND warehouse='".$_POST['warehouse']."' AND status<2";
		$r=mysql_query($SQL);
		$error = 0;
		if ($d=mysql_fetch_array($r)) {
			foreach (array_keys($rs) as $res) {
				if ($d[$res] != $rs[$res]) {
					$error = 1;
				}
			}
		} else {
			$error = 1;
		}
		$t = "$hour:$min $day.$month.$year <b>$name</b>: ";
		foreach (array_keys($rs) as $res) {
			$t .= " ".$res."[".$rs[$res]."],";
		}
		if ($error) {
			echo $t." - <font color=red><b>перевод не подтвержден</b></font><br>\n";
		} else {
			if ($d['status']==1) {
				$SQL = "UPDATE warehouses SET status=0 WHERE id='".$d['id']."'";
				mysql_query($SQL);
				if ($s[0] != 1) {
					mysql_query("INSERT INTO warehouses values('',
						'".time()."',
						'".$d['customer']."',
						'".AuthUserName."',
						'-".$rs['coins']."',
						'-".$rs['metals']."',
						'-".$rs['gold']."',
						'-".$rs['polymers']."',
						'-".$rs['organic']."',
						'-".$rs['venom']."',
						'-".$rs['radioactive']."',
						'-".$rs['gems']."',
						'-".$rs['silicon']."',
						'".$_POST['warehouse']."',
						'0')");
					mysql_query("INSERT INTO warehouses values('',
						'".(time()+1)."',
						'1',
						'".AuthUserName."',
						'".$rs['coins']."',
						'".$rs['metals']."',
						'".$rs['gold']."',
						'".$rs['polymers']."',
						'".$rs['organic']."',
						'".$rs['venom']."',
						'".$rs['radioactive']."',
						'".$rs['gems']."',
						'".$rs['silicon']."',
						'".$_POST['warehouse']."',
						'0')");
				}
			}
			echo $t." - <font color=green>перевод подтвержден</font><br>\n";
		}
	}
}
?>
</td></tr>
</table>

<?
} else echo $mess['AccessDenied'];
?>