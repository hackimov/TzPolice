<?php
require_once("/home/sites/police/dbconn/dbconn.php");
require_once("mp_headquarters_domain.php");

$linkToGameServer = "http://city1.timezero.ru/getbattle?id=";

$debugEnabled = false;

function getBattleLog(&$logContent, $logId) {
	
	global $db;
	global $linkToGameServer;

	$logId = trim($logId);
	if (empty($logId)) {
		$logContent = "<font color='red'>��� �� ��������</font> :: ������ ������ ������������� ���.";
		return false;
	}

	$success = false;
	// ��������, �������� ��� ��� ���������� � ���� ������
	$rs = mysql_query("SELECT `log_content` FROM `mp_headquarters_logs` WHERE `log_id`='$logId'", $db);

	if (mysql_num_rows($rs) != 0) {
		
		while ($row = mysql_fetch_array($rs)) {
			$logContent = $row['log_content'];
			break;
		}
		if (empty($logContent)) {
			$logContent = "<font color='red'>������ ���������� �� ���������� ��������������</font>";
		} else {
			$success = true;
		}
		
	} else {
		
		// ���� � ����� ��������������� � ���� ���
		// �������� ��� � ������� �� � ��������� � ���� ������..
		
		$content = file_get_contents($linkToGameServer.$logId);
		if ($content !== false && startsWith($content, '<BATTLE')) {
			// ����.. ������� �������� ����������...
			if (!strpos($content, "battleid=\"".$logId."\"")) {
				
				$logContent = "<font color='red'><b>��� �� ��������</b></font> :: ������ �� ��������, ��� ��� � ��������� ������� �� ����������.";
				mysql_query("INSERT bots_messages SET bot_id = 'wars', recp = '����� ���� 1', message='���������� ������� ��������� � ������. ����� ����: ".$logId."', send_after = 0", $db);
				
			} else {
				
				mysql_query("INSERT INTO `mp_headquarters_logs`(`log_id`, `log_content`) VALUES ('$logId', '".addslashes($content)."')", $db);
				$logContent = $content;
				$success = true;\
				
			}
			
		} else {
			$logContent = "<font color='red'><b>��� �� ��������</b></font> :: ������ �� ��������, ��� ��� � ��������� ������� �� ����������.";
		}
	}
	return $success;
}

function startsWith($haystack, $needle) {
	$length = strlen($needle);
	return (substr($haystack, 0, $length) === $needle);
}

function parseBattleLog($logContent) {
	
	ini_set('pcre.backtrack_limit', '150000');
	// initial condition of battle
	preg_match('/<BATTLE([\s\S]+?)<\/BATTLE/', $logContent, $matches);
	debug("���������� ���������:<br>");
	//echo htmlspecialchars($matches[0]).'<br><br>';
	preg_match('/note="(\d+),(\d+),(\d+)"[\s\S]+serverid="(\d+)"/', $matches[0], $headerData);
	$locationX = 0 + $headerData[1];
	$locationX = ($locationX > 180 ? 0 - (360 - $locationX) : $locationX);
	$locationY = 0 + $headerData[2];
	$locationY = ($locationY > 180 ? 0 - (360 - $locationY) : $locationY);
	$battleTimeStamp = $headerData[3];
	$serverId = $headerData[4];
	debug('Location (X) = '.$locationX.'<br>');
	debug('Location (Y) = '.$locationY.'<br>');
	debug('battle time = '.$battleTimeStamp.' :: '.(date('d.m.Y H:i:s', 0 + $battleTimeStamp)).'<br>');
	debug('server ID = '.$serverId.'<br>');

	$battle = new BattleFlow($locationX, $locationY, $serverId, $battleTimeStamp);
	debug($battle->getBattleDate());
	debug('<br>');
	debug($battle->getLocation());

	debug('<br>������ ���:<br>');
	//������ ������ �� ������:
	preg_match_all('/<USER([\s\S]+?)<\/USER/', $matches[0], $userData);
	for ($i = 0; $i < count($userData[1]); $i++) {
		parseBattleLogUser($userData[1][$i], 0, $battle);
	}

	//����
	preg_match_all('/<TURN[\s\S]+?turn="(\d+)"([\s\S]*?)<\/TURN/', $logContent, $turnsData);
	debug("<br>����� � ���: ".count($turnsData[1]));
	debug('<br>���:<br>');
	for ($i = 0; $i < count($turnsData[1]); $i++) {
		// ��������� � ����������
		preg_match_all('/<USER([\s\S]+?)<\/USER/', $turnsData[2][$i], $userData);
		for ($j = 0; $j < count($userData[1]); $j++) {
			parseBattleLogUser($userData[1][$j], $turnsData[1][$i], $battle);
		}

		// ���� � ������ ����������
		/*
	preg_match_all('/<O id="([\d.]+?)" drop="([\s\S]{2,}?)"/', $turnsData[2][$i], $dropData);
	for ($j = 0; $j < count($dropData[1]); $j++) {
	$turn = $turnsData[1][$i];
	$dropId = $dropData[1][$j];
	$login = convertLogin($dropData[2][$j]);
	//<O id="128848649888.1" name="b2-s1" txt="Metals" massa="100" section="0" bx="12" by="11" count="54" sf="52" type="0.191"/>
	preg_match_all('/<O id="'.$dropId.'"[\s\S]+?txt="([\s\S]+?)"[\s\S]+?count="(\d*)"/', $turnsData[2][$i], $dropInfo);
	for ($k = 0; $k < count($dropInfo[1]); $k++) {
		$dropText = $dropInfo[1][$k];
		$dropCount = $dropInfo[2][$k];
		echo "��� $turn :: ��������� :: ���: $login :: $dropText - $dropCount ��.<br>";
	}
	}
	*/
	}
	debug('end parse');
	return $battle;
}

function parseBattleLogUser($userContent, $turn, &$battle) {
	if (empty($userContent)) return;

	//debug("<br>parseBattleLogUser: $userContent, $turn");

	//-------------------------------------------------------
	// ����������� ����� � ���
	$joinToBattle = false;
	//ne=",,,,,"
	preg_match('/login="([\s\S]+?)"[\s\S]+level="(\d+)"[\s\S]+pro="(\d*?)"[\s\S]+ne="([,-\d]+?)"[\s\S]+clan="([\s\S]*?)"[\s\S]+group="(\d+)"/', $userContent, $userData);
	if (count($userData) > 1) {
		$login = convertLogin($userData[1]);
		$brokenSlots = strpos($userData[0], 'brokenslots');
		$level = $userData[2];
		$proffession = $userData[3];
		$possibleShock = substr_count($userData[4], '-') == 6;
		$clan =  $userData[5];
		$group = $userData[6];
		$joinToBattle = true;
		debug("���: $turn :: ����� � ��� :: ���: $login [$level]; ���������: $proffession; ����: $clan; ������: $group".($brokenSlots ? " :: ���������� - � ��������� ���� ������� �����" : "").($possibleShock ? " :: ���������� - �������� �������� ��������� ��� ��������� ��������������� ����" : "")."<br>");
	}

	//*************************************************************************************
	preg_match('/login="([\s\S]+?)"[\s\S]+level="(\d+)"[\s\S]+uniquest="(\d*?)"[\s\S]+ne="([,-\d]+?)"[\s\S]+clan="([\s\S]*?)"[\s\S]+group="(\d+)"/', $userContent, $userData);
	if (count($userData) > 1) {
		$login = convertLogin($userData[1]);
		$brokenSlots = strpos($userData[0], 'brokenslots');
		$level = $userData[2];
		$proffession = $userData[3];
		$possibleShock = substr_count($userData[4], '-') == 6;
		$clan =  $userData[5];
		$group = $userData[6];
		$joinToBattle = true;
		debug("���: $turn :: ����� � ��� :: ���: $login [$level]; ������ �� ���������: $proffession; ����: $clan; ������: $group".($brokenSlots ? " :: ���������� - � ��������� ���� ������� �����" : "").($possibleShock ? " :: ���������� - �������� �������� ��������� ��� ��������� ��������������� ����" : "")."<br>");
	}

	// ����� �����, ���... ���� ���c � ��� ���������� - �������� pro �����������!!!
	if (!$joinToBattle) {
		preg_match('/login="([\s\S]+?)"[\s\S]+level="(\d+)"[\s\S]+ne="([,-\d]+?)"[\s\S]+clan="([\s\S]*?)"[\s\S]+group="(\d+)"/', $userContent, $userData);
		if (count($userData) > 1) {
			$login = convertLogin($userData[1]);
			$brokenSlots = strpos($userData[0], 'brokenslots');
			$level = $userData[2];
			$proffession = 0;
			$possibleShock = substr_count($userData[3], '-') == 6;
			$clan =  $userData[4];
			$group = $userData[5];
			$joinToBattle = true;
			debug("���: $turn :: ����� � ��� :: ���: $login [$level]; ���������: $proffession; ����: $clan; ������: $group".($brokenSlots ? " :: ���������� - � ��������� ���� ������� �����" : "").($possibleShock ? " :: ���������� - �������� �������� ��������� ��� ��������� ��������������� ����" : "")."<br>");
		}
	}

	if ($joinToBattle) {
		$userObject = new UserEntry($login, $level, $proffession, $clan, $brokenSlots, $possibleShock);
		$battle->addUserEntry($userObject, $turn, $group);
	}
	//--------------------------------------------------------

	// ������ �������� � ���
	/*preg_match_all('/login="([\s\S]+?)"[\s\S]+?<BPET[\s\S]+add="([\s\S]+?)"/', $userContent, $petData);
for ($i = 0; $i < count($petData[2]); $i++) {
	$login = convertLogin($petData[1][0]);
	$petId = $petData[2][$i];
	echo "���: $turn :: �������� $login - �������� ������� :: $petId<br>";
}*/


	//--------------------------------------------------------------
	// ����������� ����� ������ � ���
	$reqisterDeath = false;
	/*
preg_match('/HP="0"[\s\S]+login="([^$]+?)"/', $userContent, $userData);
if (count($userData) > 1) {
		echo '<pre>';
		echo $userContent;
		echo '</pre>';
		echo '<pre>';
		print_r($userData);
		echo '</pre>';
	$victim = convertLogin($userData[1]);
	echo "���: $turn :: ���� :: $victim<br>";
	$reqisterDeath = true;
}
*/
	preg_match('/login="([^$]+?)"[\s\S]+HP="0"/', $userContent, $userData);
	if (count($userData) > 1) {
		//echo '<pre>';
		//print_r($userData);
		//echo '</pre>';
		$victim = convertLogin($userData[1]);
		debug("���: $turn :: ���� :: $victim<br>");
		$reqisterDeath = true;
	}
	if ($reqisterDeath) {
		$battle->registerUserDeath($victim, "", $turn);
	}
	// �������� � ��������� ���������� � ���
	preg_match_all('/login="([^$]+?)"[\s\S]+?<a[\s\S]+?[\s]t="20"[\s\S]+?login="([\s\S]+?)"/', $userContent, $victimData);
	for ($i = 0; $i < count($victimData[2]); $i++) {
		$login = convertLogin($victimData[1][0]);
		$victim = convertLogin($victimData[2][$i]);
		if (!startsWith($victim, '$')) {
			debug("���: $turn :: �������� $login - ���� ��������� :: $victim<br>");
			$battle->registerUserDeath($victim, $login, $turn);
		}
	}
	// �������� � ��������� ���������� � ���
	preg_match_all('/login="([^$]+?)"[\s\S]+?<a[\s\S]+?[\s]t="19"[\s\S]+?login="([\s\S]+?)"/', $userContent, $victimData);
	for ($i = 0; $i < count($victimData[2]); $i++) {
		$login = convertLogin($victimData[1][0]);
		$victim = convertLogin($victimData[2][$i]);
		if (!startsWith($victim, '$')) {
			debug("���: $turn :: �������� $login - ���� ��������� :: $victim<br>");
			$battle->registerUserDeath($victim, $login, $turn);
		}
	}
	//----------------------------------------------------------------

	//----------------------------------------------------------------
	//<a sf="0" t="8" id="132104386147.1" txt="Organic" count="1"/>
	// ������ ���� � ���
	preg_match('/login="([\s\S]+?)"/', $userContent, $userData);
	if (count($userData) > 1) {
		$login = convertLogin($userData[1]);
		//echo "tryint to find drop";
		preg_match_all('/<a[\s\S]*?t="8"[\s\S]+?txt="([\s\S]+?)"[\s\S]+?count="(\d+)"/', $userContent, $dropData);
		//print_r($dropData);
		for ($i = 0; $i < count($dropData[1]); $i++) {
			$title = $dropData[1][$i];
			$count = $dropData[2][$i];
			debug("���: $turn :: �������� � ��� :: $login :: $title - $count ��.<br>");
			$battle->registerResourcesPickUp($login, $count, $turn);
		}
	}
	//----------------------------------------------------------------

}

function convertLogin($source) {
	return iconv('UTF-8', 'CP1251', $source);
}

function debug($message) {
	global $debugEnabled;
	if ($debugEnabled) {
		//echo $message;
		$filename = '/home/sites/police/www/_modules/inviz/backends/debug.txt';
		if ($handle = fopen($filename, 'a')) {
			fwrite($handle, $message);
			fclose($handle);
		}

	}
}

function createFormLocation($act,$valuearray) {  // ��� ���������� � �������������� ������������ ���������� �����. ��������� �� ���� ����������
	
	if ($act == 'add') {
		$title = "����������";
		$button = "��������";
		$formcommand = "addact";
		
	} elseif ($act == 'edit') {
		$title = "��������������";
		$button = "��������";
		$formcommand = "editact";
	} else {
		return "�����";
	}
	
	$rez = "<center><b>�����: <font color=green>".$title." �������</font></b><br/>
	<table>
	<tr>
		<td>������:</td>
		<td><select id='server'>
			<option value='1' ".($valuearray['server']==1?"selected":"").">����������</option>
			<option value='2' ".($valuearray['server']==2?"selected":"").">���������</option>
		</select></td>
	</tr>
	<tr>
		<td>���������� X:</td>
		<td><input type='text' size='10' maxlength='4' id='coordx' value='".$valuearray['coordx']."'></td>
	</tr>
	<tr>
		<td>���������� Y:</td>
		<td><input type='text' size='10' maxlength='4' id='coordy' value='".$valuearray['coordy']."'></td>
	</tr>
	<tr>
		<td>��� �������:</td>
		<td><select id='loctype'>
			<option value='1' ".($valuearray['loctype']==1?"selected":"").">���������� �������</option>
			<option value='2' ".($valuearray['loctype']==2?"selected":"").">��������� �� ��������</option>
		</select></td>
	</tr>
	<tr>
		<td>��� �������:</td>
		<td><select id='areatype'>
			<option value='1' ".($valuearray['areatype']==1?"selected":"").">� �������� �������</option>
			<option value='2' ".($valuearray['areatype']==2?"selected":"").">� �������� �������</option>
		</select></td>
	</tr></table>

	<input type='button' value='".$button." �������' onclick=\"pass_action_to_page('admin_console', 'location', '".$formcommand."', {
		server: document.getElementById('server').value, 
		coordx: document.getElementById('coordx').value,
		coordy: document.getElementById('coordy').value,
		loctype: document.getElementById('loctype').value,
		areatype: document.getElementById('areatype').value
	});\">";

	return $rez;
}

/*
<a t="7" sf="84"/> === t="7" - ������?
<a sf="84" t="20" code="6" login="*BODY*"/> --- code="6" - ��������?

<a sf="0" t="8" id="132083068234.1" txt="Metals" count="14"/> === t="8" - ���� ���� ������?
*/
?>