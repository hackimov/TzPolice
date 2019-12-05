<html>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<head>
  <title>You're not supposed to see this =)</title>
<LINK href="../_modules/tzpol_css.css" rel="stylesheet" type="text/css">
<script language='JavaScript'>;
<!--
function nck(vl){
	var IE = '\v' == 'v';
	if(IE) window.clipboardData.setData('Text',vl);
}
-->
</script>

<?php
include("../_modules/java.php");
?>
</head>
<body style="margin: 15 15 15 15;" bgcolor="#EBDFB7" text="#455600" alink="#0D3AB4" link="#0D3AB4" vlink="#0D3AB4">
<?php
require("../_modules/functions.php");
require("../_modules/auth.php");
error_reporting(0);
$Rhiccupped = Array();
$battlelink = "http://www.timezero.ru/sbtl.ru.html?";

// Разделение по командам
function Teams($Line) {

	return $TeamString;
}

// просто список участников
function Teams2($Line) {
	$Team = array();
	$Users = explode(';', $Line);
	$MaxTeam = 0;

	foreach ($Users as $User) {
		if ($User) {
			$Details = explode(',', $User);
			if ($Details[2] > $MaxTeam) {
				$MaxTeam = $Details[2];
			}
			if($Team[$Details[2]]!="") $Team[$Details[2]] .= ", ";
			$Team[$Details[2]] .= htmlspecialchars("<info>".$Details[0]."</info>");
		}
	}

	$TeamString = "";
	for ($i=1; $i<=$MaxTeam; $i++) {
		if ($Team[$i]) {
			if($TeamString!="") $TeamString .= ", ";
			$TeamString .= $Team[$i];
		}
	}
	return $TeamString;
}


if(abs(AccessLevel) & AccessOP || AuthUserGroup==100) {

	$Cond = array();
	$Cond[] = "B.BattleDate>='".substr($_REQUEST['sdate'],6,4)."-".substr($_REQUEST['sdate'],3,2)."-".substr($_REQUEST['sdate'],0,2)."'";
	$Cond[] = "B.BattleDate<='".substr($_REQUEST['fdate'],6,4)."-".substr($_REQUEST['fdate'],3,2)."-".substr($_REQUEST['fdate'],0,2)."'";
	if ($_REQUEST['nick']) { $Cond[] = "BL.login = '".$_REQUEST['nick']."'"; }
	if ($_REQUEST['location']) {
		$Lct = explode(",", $_REQUEST['location']);
		for ($i=0; $i<2; $i++) {
			if ($Lct[$i] < 0) {
				$Lct[$i] += 360;
			}
		}
		$Lct = implode(",", $Lct);
		$Cond[] = "B.Location='".$Lct."'";
	}
	$Cond[] = "B.TeamsKey='".$_REQUEST['key']."'";
	if ($_REQUEST['cheat'] > 0) { $Cond[] = "B.Cheat>='".$_REQUEST['cheat']."'"; }
	if ($_REQUEST['cheat'] < 0) { $Cond[] = "B.Cheat<='".$_REQUEST['cheat']."'"; }
	if ($_REQUEST['status']) { $Cond[] = "B.Status='".$_REQUEST['status']."'"; } else { $Cond[] = "B.Status='0'"; }
	$SQL = "SELECT B.BattleDate AS BattleDate, B.BattleTime AS BattleTime, B.BattleID AS BattleID, B.Teams AS Teams, B.Location AS Location, B.id AS Bid, B.Status AS Status FROM battles B".($_REQUEST['nick']?" INNER JOIN battle_logins BL ON B.id=BL.battleid":"")." WHERE ".implode(" AND ", $Cond)." ORDER BY B.BattleDate DESC, B.BattleTime DESC";
	$result = @mysql_query($SQL);
	$LinesCount = mysql_num_rows($result);
    $tmp = 0;

	if ($LinesCount) {
		echo "
		<table style='width: 100%' bgcolor=#deceb4 cellpadding=3 cellspacing=1>
        <tr bgcolor=#EBDFB0>
			<th>Лог</th>
			<th>Участники</th>
		</tr>
		";

		while (($row = mysql_fetch_row($result))) {
			$Lct = explode(",", $row[4]);
			for ($i=0; $i<2; $i++) {
				if ($Lct[$i] > 100) {
					$Lct[$i] -= 360;
				}
			}
			$Lct = implode(",", $Lct);
			$tmp ++;
			echo "<tr bgcolor=#EBDFB7>
			<td align=center rowspan=".$data[0].">
			<A HREF=\"javascript:{}\"><IMG SRC=\"http://www.tzpolice.ru/i/bullet-red-01a.gif\" BORDER=0 width=\"18\" height=\"11\" OnClick=\"nck('".$row[2]."');;\" ALT=\"Скопировать номер лога в буфер обмена\"></A>
				<a target=_blank href='$battlelink".$row[2]."'>".$row[2]."</a>
			</td>
			<td>";
			$Team = array();
			$Users = explode(';', $row[3]);
			$MaxTeam = 0;

			foreach ($Users as $User) {
				if ($User) {
					$Details = explode(',', $User);
					if ($Details[2] > $MaxTeam) {
						$MaxTeam = $Details[2];
					}
					$Eranks = "";
					if($Details[8]) {

						//echo "<br><div border=1>";
						//print_r($Details);

						//echo "</div><br>";

						$rankinfo = explode('&', $Details[8]);

						$ranks = explode('|', $rankinfo[0]);
						foreach($ranks as $k => $v) {

		                	$v = explode(":",$v);
		                	if($v[1] < 0.001) continue;
		                	$v[2] = str_replace("/",", ",$v[2]);

						}
						$Eranks = "[Ранг-поинты: <b style='color:navy'>".sprintf("%02.2f",$rankinfo[1])."</b>] ";
						$Rhiccupped[$Details[0]] += $rankinfo[1];
					} else {
						$Eranks = "[Ранг-поинты: <b style='color:navy'>0</b>]";
					}
					$Team[$Details[2]] .= '<img src="http://www.timezero.ru/i/clans/'.($Details[6]?rawurlencode($Details[6]):'0').'.gif"><b>'.$Details[0].'</b> ['.$Details[1].']'.$Eranks.' ,';
				}
			}
			for ($i=1; $i<=$MaxTeam; $i++) {
				if ($Team[$i]) {
					echo "Команда №$i: ".rtrim($Team[$i],",")."<br>";
				}
			}

			echo "</td>
			</tr>";
		}

		echo "
        <tr bgcolor=#EBDFB0>
			<th>Итого:</th>
			<td style='padding-left: 10px;'>";
			if(count($Rhiccupped) > 0) {
				foreach($Rhiccupped as $user => $ranks) {					echo "<b>".$user."</b> ранг-поинтов: ".$ranks." <br>";
				}
			} else {				echo "Нет ранговых боёв";
			}
		echo "
		</td>
		</tr>
		";
		echo "</table>";
	} else {
		echo "Ничего не найдено\n";
	}
}else{
	echo ("fuck...");
}
?>
</body>
</html>