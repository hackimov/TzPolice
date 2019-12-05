<?
class police_b_list extends sql_table {
	var $nick, $level, $pro, $clan, $reason_type, $reason, $term, $status, $last_updated, $payment,
	$payment_till, $no_payment_till, $deleted_by, $online, $requests, $added, $del_rsn, $rem_date, $employee;

	function police_b_list() {
		$this->setTableName('police_b_list');
		$this->setTitle('Black List');
		$this->id_name = 'nick';
	}

	function getArray($condition, $debug = false) {
		$list = parent::getArray($condition, $debug);
		foreach ($list as &$row) {
			$term = $row['term'];
			$row['term_full'] = (isset($row['term']) && $row['term'] == 9999999999) ? 'Бессрочно' : date('d M Y', $term);
			$row['added_full'] = (isset($row['added']) && $row['added'] > 0) ? date('d M Y', $row['added']) : 'до начала времен';
			$row['clan_icon'] = (isset($row['clan']) && strlen($row['clan']) > 2) ? '<img src="_imgs/clans/' . $row['clan'] . '.gif">' : '';
			$row['payment_till_full'] = (isset($row['payment_till']) && strlen($row['payment_till']) == 999999999) ?
			'Бессрочно' : $row['payment_till'];;
		}
		return $list;
	}
}

class black_list extends sql_table {
	var $date, $city, $nick, $level, $pro, $clan, $status;

	function black_list() {
		$this->setTableName('black_list');
		$this->setTitle('Black List');
		$this->id_name = 'nick';
	}
}
?>