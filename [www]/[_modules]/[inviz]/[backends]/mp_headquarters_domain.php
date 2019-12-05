<?php
require_once("/home/sites/police/dbconn/dbconn.php");
require_once("/home/sites/police/www/_modules/functions.php");

class BattleFlow {
	// класс отвечающий за хранение всех последовательностей ходов в бою и вычисление баллов
	private $coordinateX;
	private $coordinateY;
	private $serverId;
	public $timestamp;
	private $players;
	private $turns;
	private $groups;
	private $mpOfficerAlreadyInBattle;
	private $mpOfficerArriveTurn;
	private $victim;

	public function __construct($x, $y, $server, $timestamp) {
		$this->coordinateX = $x;
		$this->coordinateY = $y;
		$this->serverId = $server;
		$this->timestamp = 0 + $timestamp;
		$this->players = array();
		$this->turns = array();
		$this->groups = array();
		$this->mpOfficerAlreadyInBattle = false;
		$this->mpOfficerArriveTurn = 0;
	}

	public function isRestrictedLocation() {
		// проверка, не входит ли локация в список исключений для расчётов
		global $db;

		$query = mysql_query(
		"SELECT *
		FROM `mp_headquarters_locations`
		WHERE `condition` = 'excluded_location'
		AND `server_id` = ".$this->serverId."
		AND (
				`coverage` = 'exclude_coordinates'
				OR (
					`coverage` = 'exact_coordinates'
					AND `coordinate_x` = ".$this->coordinateX."
					AND `coordinate_y` = ".$this->coordinateY."
				))
		", $db
		);

		return (mysql_num_rows($query) > 0);
	}

	public function getLocation() {
		return '['.($this->coordinateX).'/'.($this->coordinateY).','.($this->getServerName()).']';
	}

	public function getBattleDate() {
		return date('d.m.Y', $this->timestamp);
	}

	private function getServerName() {
		return ($this->serverId == 1 ? 'Терраприма' : 'Архипелаг');
	}

	public function addUserEntry($user, $turn, $group) {
		array_push($this->players, &$user);
		$user->group = $group;

		if (empty($this->groups[$group])) {
			$this->groups[$group] = array();
		}
		array_push($this->groups[$group], &$user);

		if (empty($this->turns[$turn])) {
			$this->turns[$turn] = array();
		}
		array_push($this->turns[$turn], array('join_to_battle', &$user, null));

		if ($user->militaryPolicePerson) {
			if ($this->mpOfficerAlreadyInBattle) {
				// несколько мп зашли в один ход
				$user->firstPersonInBattle = ($this->mpOfficerArriveTurn == $turn);
			} else {
				$this->mpOfficerAlreadyInBattle = true;
				$this->mpOfficerArriveTurn = $turn;
				$user->firstPersonInBattle = true;
			}
		}
	}

	public function registerUserDeath($deadLogin, $killerLogin, $turn) {
		$dead = $this->getUserEntry($deadLogin);
		$dead->dead = true;
		$killer = $this->getUserEntry($killerLogin);
		if (empty($this->turns[$turn])) {
			$this->turns[$turn] = array();
		}
		array_push($this->turns[$turn], array('killed', &$dead, &$killer));
		if (!empty($killer)) {
			array_push($killer->killedPlayers, &$dead);
		}
	}

	public function registerResourcesPickUp($login, $count, $turn) {
		$person = $this->getUserEntry($login);
		if (empty($this->turns[$turn])) {
			$this->turns[$turn] = array();
		}
		$needAddEntry = true;
		foreach ($this->turns[$turn] as $turnEntry) {
			if (($turnEntry[0] == 'resources_pick_up') && ($turnEntry[1]->userLogin == $login)) {
				$needAddEntry = false;
				break;
			}
		}
		if ($needAddEntry) {
			array_push($this->turns[$turn], array('resources_pick_up', &$person, null));
		}
		$person->pickedUpResources += $count;
	}

	private function getUserEntry($login) {
		if (empty($login)) return null;
		foreach ($this->players as &$player) {
			if ($player->userLogin == $login) {
				return $player;
			}
		}
	}

	private function calcScore($player) {

		debug(print_r($player, 1));
	
		$score = 0;
		$mpLvl = $player->userLevel;
		$coeff =  1 + ($mpLvl - 18) * 0.125;
		
		
		foreach($player->killedPlayers as $deaded) {
			
			if(($deaded->corsairLabel && (abs($player->userLevel - $deaded->userLevel) <= 3)) || $deaded->militaryPolicePerson) {
				continue;
			}
			
			$blLvl = $deaded->userLevel;
			
			if($blLvl - $mpLvl >= 1){			
				$k = 5;
			} else if ($blLvl - $mpLvl >= -2){
				$k = 4;
			} else if ($blLvl - $mpLvl >= -5){
				$k = 3;
			} else {
				$k = 1;
			}
			
			$score += $k * $coeff / ($player->dead ? 2 : 1);
			
		}
				
		return ceil($score);
	
	}

	private function isMineLocation() {
		// проверка защищаемая ли МП локация (сюда обычно входят шахты)
		global $db;

		$query = mysql_query(
		"SELECT *
		FROM `mp_headquarters_locations`
		WHERE `condition` = 'protected_location'
		AND `server_id` = ".$this->serverId."
		AND (
				`coverage` = 'exclude_coordinates'
				OR (
					`coverage` = 'exact_coordinates'
					AND `coordinate_x` = ".$this->coordinateX."
					AND `coordinate_y` = ".$this->coordinateY."
				))
		", $db
		);

		return (mysql_num_rows($query) > 0);
	}

	public function evaluateBattleRaiting($login, &$score, &$isVictimAlive, &$savedResources, &$battleDirection) {
		// производит расчёт баллов за бой для указанного логина
		debug("<br>Расчет по [".$login."]<br>");
		$score = 0;
		$isVictimAlive = false;
		$savedResources = 0;
		if (!empty($login)) {
			if ($this->isRestrictedLocation()) {
				debug("<br>isRestrictedLocation<br>");
				//echo 'restricted location';
				return;
			}
			$player = $this->getUserEntry($login);
			debug("<br>Получены данные игрока ".$login."---".(($player != null)?"NotNull":"Null").(($player->militaryPolicePerson)?"MP":"NoMP").(($player->dead)?"Dead":"ALive")."<br>");
			if ($player != null && $player->militaryPolicePerson) {
				$battleDirection = $this->getBattleDirection();
				switch($battleDirection) {
				case 'digging':
				case 'attack_to_corsair':
				case 'wtf':
					$score = 0;
					break;
				case 'attack_to_mp':
				case 'black_list_shutting':
					$score = $this->calcScore($player);
					break;				
				case 'robbery':
					$isVictimAlive = !$this->getUserEntry($this->victim)->dead;
					$savedResources = $isVictimAlive ? 0 : $player->pickedUpResources;
					$savedResources = ($savedResources > 9) ? $savedResources : 0; // чтоб исключить вероятность зарабатывания баллов на подъёме дропа в б			
					$score = $this->calcScore($player) + (($savedResources != 0 || $isVictimAlive) ? 4 : 0);
					break;
				}
			}
		}
	}

	private function getVictim() {
		foreach ($players as $player) {
			if ($player->victim) {
				return $player;
			}
		}
	}

	private function getBattleDirection() {
		// initial battle condition (zero turn)
		$players = array();
		foreach($this->turns[0] as $turn) {
			if ($turn[0] == 'join_to_battle') {
				$players[$turn[1]->group] = &$turn[1];
			}
		}
		if (count($players) == 1) {
			
			// Бой начался с одним игроком в нулевом ходу
			if ( (count($this->players) > 2) && !$players[1]->corsairLabel && !$players[1]->blackListPerson && !$players[1]->militaryPolicePerson ) {
				
				// пве - переросшее в ограбление
				$this->victim=$players[1]->userLogin;
				return 'robbery';
			
			} elseif ( (count($this->players) > 1) && $players[1]->militaryPolicePerson ) {

				// МП начал бой (копал, влетел на мины итд)
				return 'black_list_shutting';

			} elseif ( (count($this->players) > 1) && ( $players[1]->corsairLabel || $players[1]->blackListPerson ) ) {
				// вход против забатлившего когса или ЧС.
				return 'black_list_shutting';

			} else {
				
				// обычная копка или что-то другое (собсно не интересно)
				return 'digging';

			}

		} else {

			// два игрока в нулевом ходу
			if ($players[2]->corsairLabel) {

				// кто-то напал на корсара.. не важно мп или нет.
				return 'black_list_shutting';

			} else if ($players[2]->militaryPolicePerson) {

				// кто-то напал на МП
				return 'black_list_shutting';

			} else if ($players[1]->militaryPolicePerson) {

				// МП напал на кого-то
				return 'black_list_shutting';

			} else if ($players[2]->blackListPerson) {

				// кто-то напал на ЧС, Мп вошел 2+ ходом.
				return 'black_list_shutting';

			} else {

				// ограбление
				$this->victim=$players[2]->userLogin;
				return 'robbery';

			}

		}
	}


}

class UserEntry {
	public $userLogin;
	public $userLevel;
	public $blackListPerson;
	public $corsairLabel;
	public $militaryPolicePerson;
	public $hasBrockenSlots;
	public $hasNegativeInfluence;
	public $dead;
	public $killedPlayers;
	public $pickedUpResources;
	public $group;
	public $firstPersonInBattle;
	private $profession;

	public function __construct($login, $level, $profession, $clan, $slotsDamaged, $negativeInfluence) {
		$this->userLogin = $login;
		$this->userLevel = $level;
		$this->corsairLabel = ($profession == 1);
		$this->militaryPolicePerson = ($clan == 'Military Police');
		$this->hasBrockenSlots = $slotsDamaged;
		$this->hasNegativeInfluence = $negativeInfluence;
		$this->blackListPerson = $this->isBlackListPerson($login, $clan);
		$this->dead = false;
		$this->killedPlayers = array();
		$this->pickedUpResources = 0;
		$this->firstPersonInBattle = false;
	}

	private function isBlackListPerson($login, $clan) {
		// проверка нахождения игрока в чсмп
		global $db;

		$playerInBlackList = false;

		$query = mysql_query(
		"SELECT *
		FROM `mp_black_list_persons`
		WHERE `login` = '$login'
		", $db
		);
		$playerInBlackList = (mysql_num_rows($query) > 0);

		if (!$playerInBlackList && !empty($clan)) {
			// проверка кланового чсмп, если по логину не найден в чс
			$query = mysql_query(
			"SELECT *
			FROM `mp_black_list_clanes`
			WHERE `clan` = '$clan'
			", $db
			);
			$playerInBlackList = (mysql_num_rows($query) > 0);
		}

		return $playerInBlackList;
	}

}

?>