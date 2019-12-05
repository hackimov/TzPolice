<?php

if(!defined('MPLIB')) die('Лесом.');

$_mp_repordsPerPage = 50; // количество записей на страницу
$_mp_targetURL = "http://www.tzpolice.ru/?act=mpblack";
$_mp_moduleName = "mpblack";

$_mp_fileLocationDirectory = "/home/sites/police/www/blacklist/"; // todo - actualize
$_mp_fileNameClanList = "mp_black_list_clan.txt";
$_mp_fileNamePlayerList = "mp_black_list_player.txt";
$_mp_fileNameFullList = "mp_black_list_full.txt";


$_mp_clanBlackListMessage = "клан к отстрелу";
$_mp_partOfLinkForBattleLog = "http://www.timezero.ru/sbtl.ru.html?";

require_once("/home/sites/police/dbconn/dbconn.php");

$in = Array();
foreach($_REQUEST as $k => $v) {
	$in[$k] = clear($v);
}

function clear($data) {
	if (is_array($data)) {
		$result = array();
		foreach ($data as $piece) {
			array_push($result, clearString($piece));
		}
		return $result;
	} else {
		return clearString(addslashes(trim($data)));
	}
}

function clearString($data) {
	return htmlspecialchars(addslashes(trim($data)));
}

function genereateUser($data) {
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

function buildPageNavigator($currentPage = 1, $totalCountOfRecords, $filterConditionNick = "", $filterConditionClan = "", $filterConditionLevel = "", $blackList = "") {
	global $_mp_repordsPerPage,$_mp_targetURL;
	$result = "";
	$currentPage = ($currentPage > 0 && $currentPage <= ceil($totalCountOfRecords/$_mp_repordsPerPage)) ? $currentPage : 1;
	$countOfPages = ceil($totalCountOfRecords/$_mp_repordsPerPage);
	for ($i = 1; $i <= $countOfPages; $i++) {
		if ($currentPage == $i) {
			$result .= $i." ";
		} else {
			$addOn = "";
			if (!empty($filterConditionNick)) {
				$addOn .= "&nickName=".$filterConditionNick;
			}
			if (!empty($filterConditionClan)) {
				$addOn .= "&clan=".$filterConditionClan;
			}
			if (!empty($filterConditionLevel)) {
				$addOn .= "&level=".$filterConditionLevel;
			}
			if (!empty($blackList)) {
				$addOn .= "&blackList=".$blackList;
			}
			$result .= "<a href='".$_mp_targetURL."&page=".$i.$addOn."'>".$i."</a> ";
		}
	}
	return $result;
}

function buildPageNavigatorSpecial($currentPage = 1, $totalCountOfRecords, $filterConditionNick = "", $filterConditionClan = "", $filterConditionLevel = "") {
	global $_mp_repordsPerPage,$_mp_targetURL;
	$result = "";
	$currentPage = ($currentPage > 0 && $currentPage <= ceil($totalCountOfRecords/$_mp_repordsPerPage)) ? $currentPage : 1;
	$countOfPages = ceil($totalCountOfRecords/$_mp_repordsPerPage);
	for ($i = 1; $i <= $countOfPages; $i++) {
		if ($currentPage == $i) {
			$result .= $i." ";
		} else {
			$addOn = "";
			if (!empty($filterConditionNick)) {
				$addOn .= "&nickName=".$filterConditionNick;
			}
			if (!empty($filterConditionClan)) {
				$addOn .= "&clan=".$filterConditionClan;
			}
			if (!empty($filterConditionLevel)) {
				$addOn .= "&level=".$filterConditionLevel;
			}
			$result .= "<a href='".$_mp_targetURL."&target=Special&page=".$i.$addOn."'>".$i."</a> ";
		}
	}
	return $result;
}

function applyPageFiltering($nickName = "", $clan = "", $level = "", $blackList = "") {
	global $db;
	$whereClause = "";
	if (!empty($clan)) {
		$whereClause = " b.clan='$clan' ";
	}
	if (!empty($level)) {
		if (!empty($whereClause)) {
			$whereClause .= " AND ";
		}
		$whereClause .= " b.lvl=$level ";
	}
	if (!empty($nickName)) {
		if (!empty($whereClause)) {
			$whereClause .= " AND ";
		}
		$whereClause .= " a.login LIKE '%".$nickName."%' ";
	}
	if (!empty($blackList)) {
		
		if (!empty($whereClause)) {
			$whereClause .= " AND ";
		}
		if ($blackList == "1") {
			$whereClause .= " a.clan = '' ";
		// Ксакеп 24.04.2018 +	
		} elseif ($blackList == "3") {
			$whereClause .= " (a.special = 1 OR c.special = 1) ";
		// Ксакеп 24.04.2018 -	
		} else {
			$whereClause .= " a.clan <> '' ";
		}
	}
	
	if (!empty($whereClause)) {
		$whereClause = " WHERE ".$whereClause;
	}
	return mysql_query(
		"SELECT
			a.login,
			a.summ,
			a.comment,
			a.logs,
			a.clan as blackListClan,
			b.clan as clan,
			b.lvl,
			b.pro,
			b.pvprank,
			b.gender,
			c.summ as blackListClanSumm,
			a.special as special
		 FROM
		 	mp_black_list_persons AS a
		 LEFT JOIN locator AS b ON a.login = b.login
		 LEFT JOIN mp_black_list_clanes AS c ON c.clan = a.clan
		 ".$whereClause
	,$db);
}

function getBlackListClanes() {
	global $db;
	$query = mysql_query(
		"SELECT DISTINCT b.clan as name
		 FROM mp_black_list_persons AS a
		 LEFT JOIN locator AS b ON(a.login=b.login)
		 ORDER BY name"
	,$db);
	$result = array();
	while ($b = mysql_fetch_array($query)) {
		array_push($result, $b['name']);
	}
	return $result;
}

function getBlackListLevels() {
	global $db;
	$query = mysql_query(
		"SELECT DISTINCT b.lvl as level
		 FROM mp_black_list_persons AS a
		 LEFT JOIN locator AS b ON(a.login=b.login)
		 ORDER BY level"
	,$db);
	$result = array();
	while ($b = mysql_fetch_array($query)) {
		array_push($result, $b['level']);
	}
	return $result;
}

function isMilitaryPoliceClan($clanName = "") {
	return strcasecmp($clanName, "Military Police") == 0;
}

function alreadyInManagersList($nickName = "") {
	$result = false;
	if (!empty($nickName)) {
		$managersList = getBlackListManagersList();
		foreach ($managersList as $manager) {
			if ($manager['login'] == $nickName) {
				$result = true;
				break;
			}
		}
	}
	return $result;
}

function getBlackListManagersList() {
	global $db;
	$query = mysql_query(
		"SELECT
			a.login,
			b.clan,
			b.lvl,
			b.pro,
			b.pvprank,
			b.gender
		 FROM mp_black_list_managers AS a
		 LEFT JOIN locator AS b ON b.login = a.login
		 ",
		 $db
	);
	$result = array();
	while ($b = mysql_fetch_array($query)) {
		array_push($result, $b);
	}
	return $result;
}

function getClanMembers($clanName) {
	// поиск игроков входящих в указанный клан
	global $db;
	$result = array();
	if (!empty($clanName)) {
		$query = mysql_query(
			"SELECT
				b.login,
				b.clan,
				b.lvl,
				b.pro,
				b.pvprank,
				b.gender
			 FROM locator as b
			 WHERE b.clan = '".$clanName."'
			", $db
		);
		while ($b = mysql_fetch_array($query)) {
			$query1 = mysql_query(
				"SELECT
					a.login
				 FROM mp_black_list_persons a
				 WHERE
				 	a.login = '".$b['login']."' AND
					a.clan = '".$b['clan']."'
				", $db
			);
			$b['alreadyInClanBL'] = mysql_num_rows($query1) > 0;

			if ($b['alreadyInClanBL']) {
				$b['alreadyInMaradeurBL'] = false;
			} else {
				$query1 = mysql_query(
					"SELECT
						a.login
					 FROM mp_black_list_persons a
					 WHERE
				 		a.login = '".$b['login']."'
					", $db
				);
				$b['alreadyInMaradeurBL'] = mysql_num_rows($query1) > 0;
			}

			array_push($result, $b);
		}
	}
	return $result;
}

function isClanExistInBlackList($clanName) {
	global $db;
	$query = mysql_query(
		"SELECT
			clan
		 FROM
		 	mp_black_list_clanes
		 WHERE clan = '".$clanName."'
		", $db
	);
	return mysql_num_rows($query) > 0;
}

function addToJournal($act, $nick, $before, $after) {
	// регистритуем махинации с ЧС в табличко
	// 1 - добавление перса в ЧС
	// 2 - удаление перса из ЧС
	// 3 - изменение суммы ЧС
	// 4 - изменение суммы ЧС клана
	// 5 - перенос из персонального ЧС в клановый
	// 6 - добавление клана в ЧС
	// 7 - добавление персонажа в ЧС в составе клана
	// 8 - удаление из ЧС при удалении клана.
	// 9 - удаление клана из ЧС
	// 10 - перемещение из кланового ЧС в персональный
	global $db;
	$datestamp = time();
	$uid = AuthUserId;
	mysql_query(
		"INSERT INTO mp_black_list_journal(`date`, `user_id`, `bl_nick`, `act`, `before_val`, `after_val`)
		 VALUES ($datestamp, $uid, '$nick', $act, '$before', '$after')", $db);
}

function addClanToBlackList($clanName, $summ, $addToBL_nickName, $moveToBL_nickName) {
	// добавление клана в чёрный список
	global $db;
	if (isClanExistInBlackList($clanName)) {
		
		// получим сумму перед апдейтом
		$query = mysql_query("SELECT `summ` FROM mp_black_list_clanes WHERE clan = '$clanName'", $db);
		$b = mysql_fetch_array($query);
		if ($b['summ'] != $summ) {
			addToJournal(4, $clanName, $b['summ'], $summ); // строчим в журнал
		}
		
		mysql_query(
			"UPDATE mp_black_list_clanes
			 SET `summ` = '".$summ."'
			 WHERE clan = '".$clanName."'
			", $db
		);
	} else {
		mysql_query(
			"INSERT INTO mp_black_list_clanes(`clan`, `summ`, `special`)
			 VALUES('".$clanName."', '".$summ."', 0)", $db
		);
		addToJournal(6, $clanName, "", "");  // строчим в журнал
	}
	if (!empty($addToBL_nickName)) {
		foreach ($addToBL_nickName as $nickName) {
			mysql_query(
				"INSERT INTO mp_black_list_persons(`login`, `clan`)
				 VALUES ('".$nickName."', '".$clanName."')
				", $db
			);
			addToJournal(7, $nickName, "", $clanName);
		}
	}
	if (!empty($moveToBL_nickName)) {
		foreach ($moveToBL_nickName as $nickName) {
			mysql_query(
				"UPDATE mp_black_list_persons
				 SET clan = '".$clanName."'
				 WHERE login = '".$nickName."'
				", $db
			);
			addToJournal(5, $nickName, "", $clanName);
		}
	}
	blackListModificationTrigger();
}

function getPersByNick($nickName = "") {
	// поиск игрока по нику (общая база)
	// запись возвращается в единственном экземпляре
	global $db;
	$result = array();
	if (!empty($nickName)) {
		$query = mysql_query(
			"SELECT
				b.login as login,
				b.clan,
				b.lvl,
				b.pro,
				b.pvprank,
				b.gender
			 FROM locator as b
			 WHERE b.login = '".$nickName."'
			", $db
		);
		while ($b = mysql_fetch_array($query)) {
			$query1 = mysql_query(
				"SELECT
					a.login
				 FROM mp_black_list_persons a
				 WHERE a.login = '".$nickName."'
				", $db
			);
			$b['alreadyInList'] = mysql_num_rows($query1) > 0;
			array_push($result, $b);
			break;
		}
	}
	return $result;
}

function addPlayerToBlackList($nickName, $summ, $comment, $logs) {
	// добавление персонажа в чс
	global $db;
	mysql_query(
		"INSERT INTO mp_black_list_persons(`login`, `summ`, `comment`, `logs`, `special`)
		 VALUES ('$nickName', '$summ', '$comment', '$logs', 0)
		", $db
	);
	addToJournal(1, $nickName, "", ""); // строчим в журнал
	blackListModificationTrigger();
}

function addPlayerToSpecialList($nickName, $summ, $comment, $logs) {
	// добавление персонажа в чс
	global $db;
	mysql_query(
		"INSERT INTO mp_black_list_persons(`login`, `summ`, `comment`, `logs`, `special`)
		 VALUES ('$nickName', '$summ', '$comment', '$logs', 1)
		", $db
	);
	addToJournal(1, $nickName, "", ""); // строчим в журнал
	blackListModificationTrigger();
}

function changePlayerList($nickName, $isSpecial) {
	// перевод персонажа между списками
	global $db;
	mysql_query("UPDATE mp_black_list_persons SET `special` = $isSpecial WHERE `login` = '$nickName'", $db);
	addToJournal(1, $nickName, "", ""); // строчим в журнал
	//blackListModificationTrigger();
}

function removeClanFromBlackList($clanName) {
	// удаление клана из чс
	global $db;
	$query = mysql_query("SELECT `login` FROM mp_black_list_persons WHERE clan = '$clanName'", $db);
	while ($b = mysql_fetch_array($query)) {
		addToJournal(8, $b['login'], "", $clanName); // строчим в журнал
	}

	mysql_query(
		"DELETE
		 FROM mp_black_list_persons
		 WHERE clan = '".$clanName."'
		", $db
	);

	
	
	mysql_query(
		"DELETE
		 FROM mp_black_list_clanes
		 WHERE clan = '".$clanName."'
		", $db
	);

	addToJournal(9, $clanName, "", ""); // строчим в журнал
	
	blackListModificationTrigger();
}

function changeClanList($clanName, $isSpecial) {
	
	// перенос клана в ОС
	global $db;

	mysql_query("UPDATE mp_black_list_clanes SET special = ".$isSpecial."  WHERE clan = '".$clanName."'", $db);
	
	blackListModificationTrigger();
}

function getPlayerFromBlackList($nickName) {
	global $db;
	$result = array();
	if (!empty($nickName)) {
		$query = mysql_query(
			"SELECT
				b.login as login,
				b.clan as clan,
				b.lvl,
				b.pro,
				b.pvprank,
				b.gender,
				a.summ as summ,
				a.comment,
				a.logs,
				a.clan as blackListClan,
				c.summ as clanSumm
			 FROM mp_black_list_persons as a
			 LEFT JOIN locator as b ON b.login = a.login
			 LEFT JOIN mp_black_list_clanes as c ON c.clan = a.clan
			 WHERE a.login = '$nickName'
			", $db
		);
		while ($b = mysql_fetch_array($query)) {
			$b['alreadyInClanBL'] = !empty($b['blackListClan']);
			array_push($result, $b);
		}
	}
	return $result;
}

function removePlayersFromBlackList($nickName, $triggerModification = true) {
	// удаление персонажа из ЧС
	global $db;
	mysql_query(
		"DELETE
		 FROM mp_black_list_persons
		 WHERE `login` = '$nickName'
		", $db
	);
	addToJournal(2, $nickName, "", ""); // строчим в журнал
	if ($triggerModification) blackListModificationTrigger();
}

function movePlayerToMaradeurBlackList($nickName, $clanName, $clanSumm, $triggerModification = true) {
	// перемещение персонажа из кланового ЧС в марадёрский
	global $db;
	global $_mp_clanBlackListMessage;

	mysql_query(
		"UPDATE mp_black_list_persons
		 SET
		 	summ = '".$clanSumm."',
			clan = '',
			comment = '".$_mp_clanBlackListMessage." : ".$clanName."'
		 WHERE
		 	login = '".$nickName."'
		", $db
	);

	addToJournal(10, $nickName, $clanSumm, $clanName); // строчим в журнал

	if ($triggerModification) blackListModificationTrigger();
}

function updatePlayerInMaradeurBlackList($nickName, $summ, $comment, $logs) {
	// сохранение изменённых данных о марадёре
	global $db;
	
	// получим данные ДО апдейта
	$query = mysql_query("SELECT `summ` FROM mp_black_list_persons WHERE login = '$nickName'", $db);
	$b = mysql_fetch_array($query);
	if ($b['summ'] != $summ) {
		addToJournal(3, $nickName, $b['summ'], $summ); // строчим в журнал
	}
	// ******************************************************************************
	
	mysql_query(
		"UPDATE
			mp_black_list_persons
		 SET
			`summ` = '$summ',
			`comment` = '$comment',
			`logs` = '$logs'
		 WHERE
		 	login = '$nickName'
		", $db
	);
	blackListModificationTrigger();
}

function getClansBlackListVerificationResults() {
	// поиск персонажей с признаком кланового ЧС, которые более не числятся в указанном клане
	global $db;
	$query = mysql_query(
		"SELECT
			a.login,
			a.clan as blackListClan,
			b.clan as clan,
			b.lvl,
			b.pro,
			b.pvprank,
			b.gender
		 FROM
		 	mp_black_list_persons AS a
		 LEFT JOIN locator AS b ON a.login = b.login
		 WHERE b.clan <> a.clan AND a.clan <> ''
		", $db
	);
	$result = array();
	while ($b = mysql_fetch_array($query)) {
		array_push($result, $b);
	}
	return $result;
}

function performBlackListActualization($removeFromBL, $moveToMaradeurBL) {
	// выполнение операции актуализации ЧС
	global $db;
	if (!empty($removeFromBL)) {
		foreach ($removeFromBL as $nickName) {
			removePlayersFromBlackList($nickName, false);
		}
	}
	if (!empty($moveToMaradeurBL)) {
		foreach ($moveToMaradeurBL as $nickName) {
			$clanName = "";
			$clanSumm = "";
			$query = mysql_query(
				"SELECT
					b.clan,
					b.summ
				 FROM mp_black_list_persons AS a
				 LEFT JOIN mp_black_list_clanes AS b ON b.clan = a.clan
				 WHERE a.login = '".$nickName."'
				", $db
			);
			while ($b = mysql_fetch_array($query)) {
				$clanName = $b['clan'];
				$clanSumm = $b['summ'];
				break;
			}
			movePlayerToMaradeurBlackList($nickName, $clanName, $clanSumm, false);
		}
	}
	blackListModificationTrigger();
}

function blackListModificationTrigger() {
	// переформирование файлов для плагина
	global $_mp_fileLocationDirectory;
	global $_mp_fileNameClanList;
	global $_mp_fileNamePlayerList;
	global $_mp_fileNameFullList;
	global $_mp_clanBlackListMessage;

	$replacement = array(";", ",", "\n", "\r", "\t", ":", "/", "\/", "!", "'", "\"");

	$clanFile = fopen($_mp_fileLocationDirectory.'/'.$_mp_fileNameClanList, 'w');
	$persFile = fopen($_mp_fileLocationDirectory.'/'.$_mp_fileNamePlayerList, 'w');
	$fullFile = fopen($_mp_fileLocationDirectory.'/'.$_mp_fileNameFullList, 'w');

	$records = applyPageFiltering();
	while ($record = mysql_fetch_array($records)) {
		$nickName = $record['login'];
		$money = "";
		$logsContent = "";
		$description = "";
		$contentForFile = "";
		if (empty($record['blackListClan'])) {
			//марадёр
			$description = str_replace($replacement, " ", $record['comment']);
			$description = str_replace(array("Ё", "ё"), "е", $description); // замена "ё" на "е" (модуль в кпк "ё" не переваривает)
			$money = $record['summ'];
			$logsContent = str_replace($replacement, " ", $record['logs']);
			$contentForFile = $nickName.",".$money.",".$logsContent.",".$description.";\n";
			fwrite($persFile, $contentForFile);
		} else {
			//клановый ЧС
			$description = $_mp_clanBlackListMessage." - ".$record['blackListClan'];
			$money = $record['blackListClanSumm'];
			$contentForFile = $nickName.",".$money.",".$logsContent.",".$description.";\n";
			fwrite($clanFile, $contentForFile);
		}
		fwrite($fullFile, $contentForFile);
	}

	fclose($clanFile);
	fclose($persFile);
	fclose($fullFile);
}

function journal_act_format($act, $before, $after) {
	switch ($act) {
		case "1": 
			$rez = "Персонаж добавлен в ЧС мародеров";
			break;
		case "2":
			$rez = "Персонаж удален из ЧС мародеров";
			break;
		case "3":
			$rez = "Условие выхода персонажа из ЧС изменено с <b>[".$before."]</b> на <b>[".$after."]</b>";
			break;
		case "4":
			$rez = "Условие выхода клана из ЧС изменено с <b>[".$before."]</b> на <b>[".$after."]</b>";
			break;
		case "5":
			$rez = "Персонаж переведен из марадерского ЧС в состав клана ".$after;
			break;
		case "6":
			$rez = "Клан добавлен в ЧС";
			break;
		case "7":
			$rez = "Персонаж добавлен в ЧС при добавлении клана ".$after;
			break;
		case "8":
			$rez = "Персонаж удален из ЧС при удалении клана ".$after;
			break;
		case "9":
			$rez = "Клан удален из ЧС";
			break;
		case "10":
			$rez = "Персонаж переведен из состава клана ".$after." в ЧС марадеров. Назначено условие выхода: ".$before;
	}
	
	if (!isset($rez)) $rez = "Неизвестная операция";

	return $rez;
}

function preg_check($expression, $value) {
	if (!is_array($value)) {
		return preg_match($expression, $value);
	} else {
		return false;
	}
}

function logSaveAndLink($log) {
	$rez = "";
	$nobtl = 1;
	if (!preg_check("/^[0-9]{12,14}$/", $log)) {
		$log = "";
	} else {
		$str = "adamastis/logs/";
		// если такого лога нет -грузим его с ТЗ
		if (!(file_exists($str.$log.".tzb"))) {
			$btlfile = file_get_contents("http://city1.timezero.ru/getbattle?id=".$log);
			$pos_battle = strpos($btlfile,"BATTLE");
			if (!($pos_battle === false)) {
				$ft = fopen($str.$log.".tzb", "w+");
				fwrite($ft, $btlfile);
				fclose($ft);
				$nobtl = 0;
			}
		} else {
			$nobtl = 0;
		}
	}
	if ($nobtl == 1) $rez = "";
	if ($nobtl == 0) $rez = ' | <a href=logstorage/'.$log.'.tzb target=_blank>[D]</a> | <a href=\"\#; return false\" onclick=\"LogWin('.$log.')\">[V]</a>';

	return $rez;
}

function buildLogsForDisplay($logs) {
	global $_mp_partOfLinkForBattleLog;
	$result = "";
	$logsCount = 0;
	if (!empty($logs)) {
		$logsArray = explode(",", $logs);
		foreach ($logsArray as $log) {
			if (!empty($result)) {
				$result .= '<br/>';
			}
			$log = trim($log);
			if (is_numeric($log) && (strlen($log) == 14)) { // make decision that this is number of battle log
				$add = logSaveAndLink($log);
				$log = "<a href=".$_mp_partOfLinkForBattleLog.$log." target=_blank>".$log."</a>".$add;
			}
			$logsCount++;
			$result .= $log;
		}
	}
	return Array($result,$logsCount);
}

function removeFromBlackListManagersList($nickName) {
	global $db;
	if (!empty($nickName)) {
		mysql_query(
			"DELETE
			 FROM mp_black_list_managers
			 WHERE login = '".$nickName."'
			", $db
		);
	}
}

function addToFromBlackListManagersList($nickName) {
	global $db;
	if (!empty($nickName)) {
		mysql_query(
			"INSERT INTO mp_black_list_managers(`login`)
			 VALUES('".$nickName."')
			", $db
		);
	}
}

?>