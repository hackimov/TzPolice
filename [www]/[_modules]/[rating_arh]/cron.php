#!/usr/bin/php -q
<?php
// 30 * * * * /usr/bin/php /home/sites/police/www/_modules/rating_arh/cron.php >> /home/sites/police/www/_modules/rating_arh/cronlog.txt

//	set_time_limit (30*60);
// Игнорировать STOP:
//	ignore_user_abort(true);
error_reporting(E_ALL);

	$DOCUMENT_ROOT = '/home/sites/police/www';
	$path_to_php = '/_modules/rating_arh';

	require('/home/sites/police/dbconn/dbconn.php');
error_reporting(E_ALL);
	$connection = $db;
// Открываем соединение с базой данных (если не открывается, тогда ничего не выводим, а просто выходим):
//	$connection = mysql_connect ($hostName, $userName, $password);
// Выбираем базу данных (если выбрать не получается, то просто выходим):
//	$database = mysql_select_db ($databaseName, $connection);

	include_once ($DOCUMENT_ROOT.$path_to_php.'/config.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/tz_rating_function.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/other.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/tz_plugins.php');

	for($i=1;$i<=$max_level;$i++){
//	for($i=1;$i<=19;$i++){
		$url = 'http://www.timezero.ru/rating/exp'.(($i==1)?'':$i).'.html';
		get_rating_screen($i, $url, $db, $connection);
	}
	for($i=1;$i<=$max_level;$i++){
//	for($i=1;$i<=19;$i++){
		$url = 'http://www.timezero.ru/rating/pvpexp'.(($i==1)?'':$i).'.html';
		get_pvprating_screen($i, $url, $db, $connection);
	}


	mysql_close ($connection);

?>