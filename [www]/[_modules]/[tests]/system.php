<?
function GetId($table, $id) {
	return mysql_fetch_assoc(mysql_query("SELECT * FROM `{$table}` WHERE `id` = '{$id}'"));
}
function result ($login, $test) {
	$query = mysql_query("SELECT `test_ip_main`.`id` AS `ip_id`, `test_ip_main`.`login` AS `ip_login`, `test_logs_main`.`ip` AS `logs_ip`, `test_logs_main`.`test` AS `logs_test`, `test_logs_main`.`question` AS `logs_question`, `test_logs_main`.`answer` AS `logs_answer`, `test_test_main`.`id` AS `test_id`, `test_question_main`.`correct_answer` AS `test_correct_answer`
						FROM `test_logs_main` LEFT JOIN `test_test_main` ON `test_logs_main`.`test` = `test_test_main`.`id` LEFT JOIN `test_ip_main` ON `test_logs_main`.`ip` = `test_ip_main`.`id` LEFT JOIN `test_question_main` ON `test_logs_main`.`question` = `test_question_main`.`id`
						WHERE `test_ip_main`.`login` = '{$login}' AND `test_logs_main`.`test` = '{$test}' AND `test_test_main`.`id` = '{$test}' AND `test_logs_main`.`ip` = `test_ip_main`.`id` AND `test_question_main`.`correct_answer` = `test_logs_main`.`answer`");

	$correct_answer = mysql_num_rows($query);
	$cnt = mysql_num_rows(mysql_query("SELECT `test` FROM `test_question_main` WHERE `test` = '{$test}'"));

	$user_id = mysql_fetch_assoc(mysql_query("SELECT `id`,`login` FROM `test_ip_main` WHERE `login` = '{$login}'"));
	$timer_query = mysql_query("SELECT * FROM `test_logs_main` WHERE `ip` = '{$user_id['id']}' AND `test` = '{$test}' ORDER BY `timer` DESC");
	$timer = mysql_num_rows($timer_query);
	if ($cnt == $timer):
		$tmp = mysql_fetch_assoc(mysql_query("SELECT * FROM `test_test_main` WHERE `id` = '{$test}'"));
		$timer = $tmp['time']*60;
	else:
		$tmp = mysql_fetch_assoc($timer_query);
		$timer = $timer['timer'];
	endif;
	return "<p>¬ы правильно ответили на ".$correct_answer." из ".$cnt." вопросов за ".$timer." сек. –ейтинг: ".rating($correct_answer, $cnt)."</p>";
}
function rating($correct_answer, $cnt) {
	if ($correct_answer == 0) return '2 балла';
	$val = $correct_answer/$cnt;
	if ($val >= 0 AND $val < 0.5):
		return '2 балла';
	elseif ($val >= 0.5 AND $val < 0.75):
		return '3 балла';
	elseif ($val >= 0.75 AND $val < 0.9):
		return '4 балла';
	elseif ($val >= 0.9 AND $val <= 1):
		return '5 баллов';
	endif;
}
?>