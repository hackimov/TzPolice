<html>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<head>
  <title>You're not supposed to see this =)</title>
<LINK href="../_modules/tzpol_css.css" rel="stylesheet" type="text/css">
<?php
include("../_modules/java.php");
?>
</head>
<body style="margin: 15 15 15 15;" bgcolor="#EBDFB7" text="#455600" alink="#0D3AB4" link="#0D3AB4" vlink="#0D3AB4">
<?php
require("../_modules/functions.php");
require("../_modules/rating_arh/tz_plugins.php");
require("../_modules/auth.php");
error_reporting(0);


////////////////////////////////////////////////////////////
// Коэффиценты для умножения
	$res_by_level = array();

	$res_by_level[1] = 10;
	$res_by_level[2] = 25;
	$res_by_level[3] = 50;
	$res_by_level[4] = 100;
	$res_by_level[5] = 150;

	$res_by_level[6] = 200;
	$res_by_level[7] = 300;
	$res_by_level[8] = 400;
	$res_by_level[9] = 500;
	$res_by_level[10] = 600;
	$res_by_level[11] = 700;
	$res_by_level[12] = 800;
	$res_by_level[13] = 900;
	$res_by_level[14] = 1000;
	$res_by_level[15] = 1100;
	$res_by_level[16] = 1200;
	$res_by_level[17] = 1300;
	$res_by_level[18] = 1400;
	$res_by_level[19] = 1500;
	$res_by_level[20] = 1600;

////////////////////////////////////////////////////////////



if(abs(AccessLevel) & AccessOP) {

	#$userinfo = TZConn($_REQUEST['nick'],0);
	#$userinfo = fant_ParseUserInfo($userinfo);
	#$userinfo = TZConn2($_REQUEST['nick']);
	$userinfo = locateUser($_REQUEST['nick']);
	
	if (!isset($userinfo['level'])) {
		$userinfo = TZConn2($_REQUEST['nick'], 1);
	}
	
	#print_r($userinfo);
	if($userinfo['error']) echo $userinfo['error'].'<hr>';
    $imprisonment_terms = Array(5000,15000);
	$imprisonment_terms_text = Array(""," (повторное нарушение)");

	$query = mysql_query("SELECT a.BattleID,b.login FROM battles as a LEFT JOIN battle_logins as b ON(a.id=b.battleid) WHERE b.login = '".$_REQUEST['nick']."' AND a.Status='5'");
	$repr = mysql_num_rows($query);
	
	if ($repr > 1) {
		$repr = 1;
	}

//	print_r($userinfo);
    $Rhiccupped = array();
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
	if ($_REQUEST['cheat'] > 0) { $Cond[] = "B.Cheat>='".$_REQUEST['cheat']."'"; }
	if ($_REQUEST['cheat'] < 0) { $Cond[] = "B.Cheat<='".$_REQUEST['cheat']."'"; }
	if ($_REQUEST['status']) { $Cond[] = "B.Status='".$_REQUEST['status']."'"; } else { $Cond[] = "B.Status='0'"; }

	$Cond = implode(" AND ", $Cond);

	$battle_counter = 0;
	$nicks = array();
	$battle_string = "";

    $query = 'SELECT B.TeamsKey AS `TeamsKey`, BL.ranks as vranks FROM `battles` B'.($_REQUEST['nick']?' INNER JOIN battle_logins BL ON B.id=BL.battleid':'').' WHERE '.$Cond;
    $result = @mysql_query($query);
    while($r = @mysql_fetch_row($result)) {
       	if($r[1] > 0) {
       		$punishable_ranks['rps'] += $r[1];
       		$punishable_ranks['cnt']++;
       	}
	}
	
    if ($userinfo["level"] >= 1 && $userinfo["level"] <= 5) {
		//$deduction = ceil($punishable_ranks['rps'])*(-1);
		// самая актуальная инфа по ранг поинтам - непосредственно из API тз.
		//$r_nick = mb_convert_encoding($_REQUEST['nick'],"cp1251","utf8");
		$r_info = GetInfoFromApi($_REQUEST['nick']);
		$deduction = $r_info["rank_points"];
	} elseif ($userinfo["level"] >= 6 && $userinfo["level"] <= 11) {
		$deduction = ceil($punishable_ranks['rps'])*10;
	} elseif ($userinfo["level"] >= 12 && $userinfo["level"] <= 16) {
		$deduction = ceil($punishable_ranks['rps'])*25;
	} elseif ($userinfo["level"] >= 17 ) {
		$deduction = ceil($punishable_ranks['rps'])*50;
	} else {
		$deduction = 0;
	}

	
	#PRINT_R($punishable_ranks);
	foreach($_REQUEST['key'] AS $val){
		$SQL = "SELECT B.BattleDate AS BattleDate, B.BattleTime AS BattleTime, B.BattleID AS BattleID, B.Teams AS Teams, B.Location AS Location, B.id AS Bid, B.Status AS Status FROM battles B".($_REQUEST['nick']?" INNER JOIN battle_logins BL ON B.id=BL.battleid":"")." WHERE ".$Cond." AND B.TeamsKey='".$val."' ORDER BY B.BattleDate DESC, B.BattleTime DESC";
		$result = @mysql_query($SQL);
		$LinesCount = mysql_num_rows($result);
		$tmp = 0;

		if ($LinesCount) {
			while (($row = mysql_fetch_row($result))) {
				if ($tmp){

				} else {
					$Team = array();
					$Users = explode(';', $row[3]);
					$MaxTeam = 0;

					foreach ($Users as $User) {
						if ($User) {
							$Details = explode(',', $User);
							if ($Details[2] > $MaxTeam) {
								$MaxTeam = $Details[2];
							}
							if($Team[$Details[2]]!="") $Team[$Details[2]] .= ", ";
							$Team[$Details[2]] .= $Details[0];
						}
					}

					$TeamString = "";
					for ($i=1; $i<=$MaxTeam; $i++) {
						if ($Team[$i]) {
							if($TeamString!="") $TeamString .= ", ";
							$TeamString .= $Team[$i];
						}
					}
					$nicks[] = $TeamString;
				}
				$tmp ++;
				$battle_counter ++;
				if($battle_string!="") $battle_string .= ", ";
				$battle_string .= $row[2];
			}
		} else {
			echo "Ничего не найдено\n";
		}
	//	echo "<BR><BR>\n";
	}

	echo $_REQUEST['nick']." <BR>\n";
	echo "******************* ОКП <BR>\n";

	$nicks = implode(", ", $nicks);
	$nicks = explode(", ", $nicks);
	$nicks = array_unique($nicks);
	$i=0;
	$nicks_string = "";
	foreach ($nicks as $nick) {
		if($nick != $_REQUEST['nick']){
			if($i>0) $nicks_string .= ", ";
			$nicks_string .= $nick;
			$i++;
		}
	}

	echo "".$userinfo["level"]." ур., нарушение Положения <О боях> c персонажем(-ами): ".$nicks_string." <BR>\n";
	if($userinfo["level"]<6) {
		echo "Срок 1000  ресурсов.<BR>\n";
	} else {
		echo "Срок ".$imprisonment_terms[$repr]." ресурсов".$imprisonment_terms_text[$repr].".<BR>\n";
	}
	echo "******************* <BR>\n";

	if ($deduction != 0) {
		echo htmlspecialchars("<info>".$_REQUEST['nick']."</info>")."<br>-$deduction на вычет<br>";
	}

} else {
	#echo ("fuck...");
}


?>
</body>
</html>