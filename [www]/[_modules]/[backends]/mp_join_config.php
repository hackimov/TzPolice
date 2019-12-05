<?php
require_once("/home/sites/police/dbconn/dbconn.php");
#require_once("/home/sites/police/www/_modules/functions.php");
#require_once("../../auth.php");

$join_access = false; // доступ к анкетам (минимальный уровень)
$join_admin = false; // доступ в качетве администратора
$join_revisor = false; // доступ в качестве обработчика анкет

if (AuthUserName != '') {
	$rs = mysql_query(
		"
			SELECT *
			FROM `mp_join_access`
			WHERE `nick`='".AuthUserName."'
		 	LIMIT 1", $db
	);
	while ($b = mysql_fetch_array($rs)) {
		$join_access = true;
		$join_admin = ($b['admin'] == 1);
		$join_revisor = ($b['revisor'] == 1);
		$join_revisor = $join_revisor || $join_admin;
		break;
	}
}

$siteLocation = 'http://www.tzpolice.ru/';

function prepareUserInfoForDrawing($login, $data = null) {
	if (empty($data)) {
		$data = locateUser($login);
	}
	$g = ($data['gender'] > 0)?"":"w";
	$data['pvprank'] = ($data['pvprank'] > 0)?$data['pvprank']:1;
	if($data['clan']) {
		$return = "<img src='http://timezero.ru/i/clans/".$data['clan'].".gif' width=24 height=16 style='vertical-align: text-bottom;' alt='".$data['clan']."' title='".$data['clan']."'>";
	}
	$return .= "<b>".$data['login']."</b>[".$data['lvl']."]";
	$return .= "<img src='http://timezero.ru/i/i".$data['pro']."$g.gif' width=15 height=15 style='vertical-align: text-bottom;'>";
	$return .= "<img src='http://timezero.ru/i/rank/".$data['pvprank'].".gif' width=15 height=15 style='vertical-align: text-bottom;'>";
	return $return;
}

function convertStringToTimestamp($source, $startInterval) {
	// string is represented in format d/m/Y
	$date = explode('/', $source);
	$Y = $date[2];
	$M = $date[1];
	$D = $date[0];
	return strtotime($M."/".$D."/".$Y." ".($startInterval ? "00:00" : "23:59"));
}

?>