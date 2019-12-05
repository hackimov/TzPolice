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
 * ������ � SQL ��������
 * @package Core
 * @subpackage system
 */
class DB {

	/**
	* ��������� ResultSet ������� � ���� ������
	* @access private
	* @var resource
	*/
	var $result_set;

	/**
	* ��������� ResultSet ������� � ���� ������
	* @access private
	* @var array
	*/
	var $line;
	
	/**
	* ����� �������� �� �����
	* @access private
	* @var boolean
	*/
	var $debug;
	
	/**
	* ����������� ������
	*/
	function DB () {
		$this->debug = false;
	}

	/**
	* ��������� ������ � ���� 
	* @param string $query SQL ������
	* @return resource ResultSet ���������� ������� � ����
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
	* ��������� ��������� ������ ���������� �������
	* @return array ������ �� SQL ������� � ���� �������
	*/
	function next() {
		$this->line = mysql_fetch_array($this->result_set, MYSQL_ASSOC);
		if (!$this->line && $this->result_set) {
			mysql_free_result($this->result_set);
		}
		return $this->line;
	}

	/**
	* ��������� ������ � ����, � �������� ��������� ������� � ���� �������
	* @param string $query SQL ������
	* @return array ������ �� SQL ������� � ���� �������
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
	* ��������� ������ � ����
	* ������ �������������, ��� ��������� ����� ������ ���� ������
	* @param string $query SQL ������
	* @return array ������ �� SQL ������� � ���� �������
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
	* ��������� ������ � ����, � ������� �������, � � �������� ������������ �������� ������� ���� � ������
	* @param string $tablename SQL �������
	* @param string $field ���� � �������
	* @param string $value ������� �������� � �������
	* @return array ������ �� SQL ������� � ���� �������
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