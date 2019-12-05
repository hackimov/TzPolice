#!/usr/bin/php -q
<?php
// 15 * * * * /usr/bin/php /home/sites/police/www/_modules/rating_arh/fees_cron.php
	
//	set_time_limit (30*60);
	error_reporting(E_ALL);
// Игнорировать STOP:
	ignore_user_abort(true);

	$DOCUMENT_ROOT = '/home/sites/police/www';
	$path_to_php = '/_modules/rating_arh';

	require('/home/sites/police/dbconn/dbconn.php');
	$connection = $db;
// Открываем соединение с базой данных (если не открывается, тогда ничего не выводим, а просто выходим):
//	$connection = mysql_connect ($hostName, $userName, $password);
// Выбираем базу данных (если выбрать не получается, то просто выходим):
//	$database = mysql_select_db ($databaseName, $connection);
	
	include_once ($DOCUMENT_ROOT.$path_to_php.'/config.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/fees_function.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/other.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/tz_plugins.php');


//===============================================
// все писать тут 
//===============================================
	
	fees_log_parse ($connection, $db);

//===============================================
	mysql_close ($connection);

?>
