<?php
/**
* Site mechanic
*
* @package Core
* @author Burlachenko Alexander <alexander.burlachenko@nat.kz>
* @copyright NAT (www.nat.kz)
* @version 1.0
*/

/**
 * Работа с SQL сервером
 * @package Core
 * @subpackage system
 */
class DB {

	/**
	* Последний ResultSet запроса к базе данных
	* @access private
	* @var resource
	*/
	var $result_set;

	/**
	* Последний ResultSet запроса к базе данных
	* @access private
	* @var array
	*/
	var $line;
	
	/**
	* Вывод запросов на экран
	* @access private
	* @var boolean
	*/
	var $debug;
	
	/**
	* Конструктор класса
	*/
	function DB () {
		$this->debug = false;
	}

	/**
	* Выполняет запрос к базе 
	* @param string $query SQL запрос
	* @return resource ResultSet выполнения запроса к базе
	*/
	function query($query, $debug = false) {
		if ($query == '') {
			return null;
		}
		if ($debug) {
			echo "<br>Query:" . $query . "<br>\n";
		}
		$this->result_set = mysql_query($query) or die("Query failed : " . mysql_error());
		return $this->result_set;
	}

	/**
	* Возращает следующую строку результата запроса
	* @return array Запись из SQL таблицы в виде массива
	*/
	function next() {
		$this->line = mysql_fetch_array($this->result_set, MYSQL_ASSOC);
		if (!$this->line && $this->result_set) {
			mysql_free_result($this->result_set);
		}
		return $this->line;
	}

	/**
	* Выполняет запрос к базе, и озращает результат запроса в виде массива
	* @param string $query SQL запрос
	* @return array Записи из SQL таблицы в виде массива
	*/
	function queryArray($query, $debug = false) {
		if ($query == '') {
			return null;
		}
		if ($debug) {
			echo "<br>Array:" . $query . "<br>\n";
		}
		$result = $this->query($query);
		$ret = array();
		while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$ret[] = $line;
		}
		return $ret;
	}

	/**
	* Выполняет запрос к базе
	* Запрос подразумевает, что возращено будет только одна запись
	* @param string $query SQL запрос
	* @return array Запись из SQL таблицы в виде массива
	*/
	function queryLine($query, $debug = false) {
		if ($query == '') {
			return null;
		}
		if ($debug) {
			echo "<br>Line: $query <br>\n";
		}
		$this->log($query);
		$result = $this->query($query);
		$line = mysql_fetch_array($result, MYSQL_ASSOC);
		return $line;
	}

	/**
	* Выполняет запрос к базе, к заданой таблице, и с условием соответствия значения задного поля в записе
	* @param string $tablename SQL таблица
	* @param string $field Поле в таблице
	* @param string $value Искомое значение в таблице
	* @return array Запись из SQL таблицы в виде массива
	*/
	function getLine($tablename, $field, $value, $debug = false) {
		if ($tablename == null || $field == null || $value == null) {
			return null;
		}
		$query = "SELECT * FROM $tablename WHERE $field = '$value'";
		$line = $this->queryLine($query, $debug);
		return $line;
	}

}

$db = new DB();
$GLOBALS['db'] = $db;
?>