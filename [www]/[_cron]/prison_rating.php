#!/usr/bin/php -q
<?php
	set_time_limit(200);

//=========================
	include('/home/sites/police/dbconn/dbconn2.php');
//=========================

$now = time()-1200;


if($_GET['date']) {
	$d = explode('.',$_GET['date']);

	if(count($d) == 3) {
		$d[2] = ($d[2] > 2000)?$d[2]:$d[2]+2000;
		$now = mktime(0,0,0,$d[1],$d[0],$d[2])-1200;
	}
}
echo "Load: ".date("Y-m-d",$now)."<hr>";

	$today = date("Y-m-d",$now);
	$query = 'SELECT `nick`, `collected` FROM `prison_rating` WHERE `date` = \''.$today.'\' ORDER BY `collected` DESC LIMIT 20';
	$res = mysql_query($query);
	echo ($query);
	mysql_close($link);


	error_reporting(E_ALL);

	require ('/home/sites/police/www/_modules/functions.php');

	function FullInf($nick, $cop_detect) {
		return ("<b>".$nick."</b>");
/*
		$userinfo = GetUserInfo($nick);
	//	print_r($userinfo);
		if(!isset($userinfo['error'])) $userinfo['error']=0;
		if (!$userinfo['error'] && $userinfo['level'] > 0) {
			if ($userinfo['man'] == 0) {
				$pro = $userinfo['pro'].'w';
			} else {
				$pro = $userinfo['pro'];
			}
			if ($cop_detect && (($userinfo['clan'] == "police" && strlen($userinfo['clan']) > 2) || ($userinfo['pro'] !== '13') || ($userinfo['login'] == 'Mirazte'))) {
				$ret = 'cop';
			//	echo("[pers clan={$userinfo['clan']} nick={$userinfo['login']} level={$userinfo['level']} pro={$pro}]");
			} else {
				$_RESULT = array('res' => "OK");
				if (strlen($userinfo['clan']) > 2) {
					$ret = '[pers clan='.$userinfo['clan'].' nick='.$userinfo['login'].' level='.$userinfo['level'].' pro='.$pro.']';
				} else {
					$ret = '[pers clan=0 nick='.$userinfo['login'].' level='.$userinfo['level'].' pro='.$pro.']';
				}
			}
		} else {
			$ret = 'error';
		}

		return $ret;
*/
	}

	$count = 1;
	$bgc = 1;
	$bg[1] = 'FEF5E9';
	$bg[2] = 'FCE6C7';
	$now = time();
	$out = 'Показаны данные на <b>'.date("d.m.Y H:i",$now).'</b><br><br><center><table width="400" border="0" cellspacing="3" cellpadding="3"><tr><td width="1" bgcolor="#FAC987" align="center"><b>&nbsp;&nbsp;№&nbsp;&nbsp;</b></td><td bgcolor="#FAC987"><b>Персонаж</b></td><td bgcolor="#FAC987" align="center" width="1"><b>Собрано</b></td></tr>';
	$cheaters = array();

	while ($d = mysql_fetch_assoc($res)) {
		$repeat = 1;

		while ($repeat == 1) {

			$user_full = FullInf($d['nick'], 1);
echo ($user_full);
/*
			if ($user_full == 'error') {
				$repeat = 1;
				sleep(3);
			} elseif ($user_full == 'cop') {
				$repeat = 0;
				//$cheaters[] = $d['nick'];
			} else {
				$repeat = 0;
			}
*/
			$repeat = 0;
		}
//die();
		if ($user_full !== 'cop' && $d['nick'] !== 'Конфискация') {
			$fulltext = ParseNews3($user_full);
			$out .= '
		            <tr><td bgcolor="#'.$bg[$bgc].'" align="center">'.$count.'</td><td bgcolor="#'.$bg[$bgc].'">'.$fulltext.'</td><td bgcolor="#'.$bg[$bgc].'" align="center">'.$d['collected'].'</td></tr>';
			$bgc++;
			if ($bgc > 2) $bgc = 1;
			$count++;

		} else {
			$query = 'DELETE FROM `prison_rating` WHERE `nick` = \''.$d['nick'].'\' LIMIT 1;';
			mysql_query($query);
		}

	}
	$out .= '</table>';
	$fp = fopen ('/home/sites/police/www/_modules/prison_rating_table.php', 'w');
	fwrite ($fp, $out);
	fclose ($fp);

//=========================
	include('/home/sites/police/dbconn/dbconn2.php');
//=========================


	foreach ($cheaters as $key => $value) {
		$query = "UPDATE `prison_rating` SET `collected` = '-999999' WHERE `nick` = '".$value."' AND `date` = '".$today."' LIMIT 1;";
		mysql_query($query);
	//	echo ($query);
	}

	$query = "SELECT `nick`, `collected`, `date` FROM `prison_rating` WHERE `nick` NOT LIKE 'ZenitSPb' ORDER BY `collected` DESC LIMIT 3";
	$r = mysql_query($query);
	mysql_close($link);

	$cheaters = array();

	include('/home/sites/police/dbconn/dbconn.php');

	$user_full = $out2 = '';
	while ($z = mysql_fetch_assoc($r)) {
	//	echo "ok1<BR>\n";
		$repeat = 1;
		$repeat_counter = 1;
		while ($repeat == 1 && $repeat_counter<5) {
	//		echo "ok2 - ".$z['nick']."<BR>\n";
			$user_full = FullInf($z['nick'], 0);
			if ($user_full == 'error') {
				$repeat = 1;
				sleep(3);
			} else {
				$repeat = 0;
			}
			$repeat_counter++;
		}
		if($repeat==0){
			$fulltext = ParseNews3($user_full);
			$out2 .= $fulltext.' - <b>'.$z['collected'].'</b> <font size=1>('.$z['date'].")</font><br>\n";
		}
	}

	$fp = fopen ('/home/sites/police/www/_modules/prison_rating_top.php', 'w');
	fwrite ($fp, $out2);
	fclose ($fp);
	#echo "Updated ".date("d.m.Y H:i")."\r\n";
	echo $out."<hr>".$out2;

	exit();
?>