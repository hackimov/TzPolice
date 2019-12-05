<?php

class sql {

	static $mysqli;

	public static function connect() {

		if (empty(self::$mysqli)) {
			self::$mysqli = new mysqli('192.168.253.6', 'tzpolice', 'uDKs8NthHdsMtBUW', 'tzpolice');
		}
		/* проверка соединения */
		if (self::$mysqli->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
	}

	function __destruct() {

		self::$mysqli->close();
	}

	public static function refValues($arr) {

		if (strnatcmp(phpversion(), '5.3') >= 0) {

			$refs = array();
			foreach ($arr as $key => $value)
				$refs[$key] = &$arr[$key];
			return $refs;
			
		}

		return $arr;
		
	}

	public static function prepare_query($sql) {

		$stmt = self::$mysqli->stmt_init();
		$stmt->prepare($sql);
		return $stmt;
		
	}

	public static function execute_query($arr, $stmt) {

		$result = false;

		if (count($arr) == 0 || strlen($arr[0]) == (count($arr) - 1)) {

			if (count($arr) > 0)
				call_user_func_array(array($stmt, "bind_param"), self::refValues($arr));
			$stmt->execute();
			$result = $stmt->get_result();
		}

		return $result;
	}

}

?>