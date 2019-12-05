<?PHP
/**
* Site mechanic
*
* @package Core
* @author Burlachenko Alexander <alexander.burlachenko@nat.kz>
* @copyright NAT (www.nat.kz)
* @version 1.0
*/

require_once(dirname(__FILE__) . '/db.php');

/**
* Объект работы с базой данных
* @global DB $GLOBALS['db']
* @name $db
*/
$GLOBALS['db'];

/**
 * Общий класс для работы с SQL таблицами
 * @package Core
 * @subpackage system
 */
class sql_table {

	/**
	* Список переменных класса, которые не соответствуют полям в SQL таблице
	* @var array
	*/
	var $sys_vars = array();

	//	var $tablename;
	//	var $template;

	/**
	* Идентификатор в SQL таблице
	* @access private
	* @var integer|string 
	*/
	var $id;

	/**
	* Наименование идентификатора в SQL таблице
	* @access private
	* @var integer|string 
	*/
	var $id_name;

	/**
	* Начальный цвет для экранной формы
	* @access private
	* @var string
	*/
	var $tablecolor = '#ffffff';

	/**
	* Конструктор по умолчанию {@link $id}
	*/
	function sql_table() {
		$this->id = 0;
		$this->id_name = 'ID';
	}

	//  ==================================================== Getter & Setters  ====================================================

	/**
	* Устанавливает имя SQL таблицы
	* @param string $tablename Имя SQL таблицы
	*/
	function setTableName($tablename) {
		$this->sys_vars['tablename'] = $tablename;
	}

	/**
	* Задает заголовок HTML таблицы
	* @param string $title Заголовок HTML таблицы
	*/
	function setTitle($title){
		$this->sys_vars['title'] = $title;
	}

	/**
	* Private function
	* @access private
	*/
	function setFields($fields) {
		$this->sys_vars['fields'] = $fields;
	}

	/**
	* Возращает имя SQL таблицы
	* @return string Имя SQL таблицы
	*/
	function getTableName() {
		return $this->sys_vars['tablename'];
	}

	/**
	* Возращает заголовок HTML таблицы
	* @return string Заголовок HTML таблицы
	*/
	function getTitle(){
		return $this->sys_vars['title'];
	}

	/**
	* Private function
	* @access private
	*/
	function getFields() {
		return $this->sys_vars['fields'];
	}


	//  ==================================================== Special methods ====================================================

	/**
	 * Считывает объект из базы с заданным условием
	 *
	 * @param String $name Имя поля в SQL таблице
	 * @param String $value Значение поля для поиска
	 */
	function getRecord($name, $value, $debug = false) {
		global $db;

		if ($this->getTableName()) {
			$line = $db->getRecord($this->getTableName(), $name, $value, $debug);
			$this->fillFromArray($line);
		}
	}

	/**
	 * Считывает объект из базы с заданным ID
	 *
	 * @param String $name Имя поля в SQL таблице
	 * @param String $value Значение поля для поиска
	 */
	function getRecordByID($value, $debug = false) {
		global $db;

		if ($this->getTableName()) {
			$line = $db->getRecord($this->getTableName(), $this->id_name, $value, $debug);
			$this->fillFromArray($line);
		}
	}

	/**
	 * Считывает зачения полей объекта из массива
	 *
	 * @param array $array_row массив со значениями полей
	 */
	function fillFromArray($array_row) {
		if ($array_row) {
			foreach ($array_row as $key => $value) {
				if ($array_row[$key]) {
					$this->$key = $value;
				}
			}
		}
	}

	/**
	* Считывает из базы массив с заданным условием
	* @param string $condition Условие для отбора записей из таблицы
	* @return array Массив записей
	*/
	function getArray($condition, $debug = false) {
		global $db;
		$query = 'SELECT * FROM ' . $this->getTableName() . ' ' . $condition;
		return $db->queryArray($query, $debug);
	}

	/**
	* Добавляет объект в базу
	* @return boolean Истина, если выполнилось успешно
	*/
	function insert($debug = false) {
		global $db;
		$tablename = $this->getTableName();
		if ($tablename == '') {
			$class_name = get_class($this);
			echo "INSERT Не указала SQL таблица в классе $class_name!<br>\n";
			return null;
		}
		$ff = '';
		$vv = '';
		$listFields = $this->getSQLListFields();
		foreach ($this as $key => $value) {
			if (!isset($listFields[$key])) {
				continue;
			}
			if ($this->sysField($key)) {
				continue;
			}
			if ($ff != '') {
				$ff .= ', ';
				$vv .= ', ';
			}
			$ff .= $key;
			$vv .= "'". $value . "'";
		}
		$query = "INSERT INTO $tablename ($ff) VALUES ($vv)";
		$res = $db->query($query, $debug);
		$this->id = mysql_insert_id();
		return $res;
	}

	/**
	* Обновление объекта в базе
	* @return boolean Истина, если выполнилось успешно
	*/
	function update($debug = false) {
		global $db;
		$tablename = $this->getTableName();
		$count = 0;
		$qq = '';
		if ($tablename == '') {
			$class_name = get_class($this);
			echo "UPDATE Не указала SQL таблица в классе $class_name!<br>\n";
			return false;
		}
		$listFields = $this->getSQLListFields();
		foreach ($this as $key => $value) {
			if (!$listFields[$key]) {
				continue;
			}
			if ($this->sysField($key)) {
				continue;
			}
			if ($count++ > 0) {
				$qq .= ', ';
			}
			$qq .= "$key='". $this->$key . "'";
		}
		$query = "UPDATE $tablename SET " . $qq . " WHERE $this->id_name = $this->id";
		return $db->query($query, $debug);
	}

	/**
	* Сохранение объекта в базу. Проверяет наличие объета в базе, если уже такой существует - то обновляет, если нет - создает.
	*/
	function save($debug = false) {
		global $db;

		$id = $this->id;
		if ($id != '') {
			$line = $db->getRecord($this->getTableName(), $this->id_name, $id, $debug);
			if ($line[$this->id_name] == $id) {
				return $this->update($debug);
			} else {
				return $this->insert($debug);
			}
		} else {
			return $this->insert($debug);
		}
	}

	/**
	* Удаление записи из SQL таблицы с {@link $id} объекта
	* @return boolean Истина, если выполнилось успешно
	*/
	function delete($debug = false) {
		global $db;
		$tablename = $this->getTableName();
		if ($tablename == '') {
			$class_name = get_class($this);
			echo "DELETE: Не указала SQL таблица в классе $class_name!<br>\n";
			return false;
		}
		if ($this->id) {
			$query = "DELETE FROM $tablename WHERE $this->id_name=" . $this->id;
			return $db->query($query, $debug);
		}
		return true;
	}

	/**
	* Возращает массив с полями SQL таблицы
	* @return array Массив с полями SQL таблицы
	*/
	function getSQLListFields($debug = false) {
		global $db;
		$list = array();
		$query = 'SHOW FIELDS FROM ' . $this->getTableName();
		$db->query($query, $debug);
		while ($line = $db->next()) {
			$list[$line['Field']] = $line['Type'];
		}
		return $list;
	}

}

require_once(dirname(__FILE__) . '/class_table.php');
?>
