#!/usr/bin/php -q
<?php
error_reporting(7);
//error_reporting(E_ALL);
set_time_limit(0);
ob_implicit_flush(0);
ignore_user_abort(0);
/////////////////////////////////////////////////
/////////////////////////////////////////////////
///////////////TimeZero bot v 2.3////////////////
///////////////    Made by aSt   ////////////////
/////////////// newast@inbox.ru  ////////////////
/////////////////////////////////////////////////
/////////////////////////////////////////////////
/**************************/

$home_dir = '/home/sites/police/bot_php/MP';

$pid_write = 0;
$last_pid = (int) @file_get_contents($home_dir.'/bot.pid');
if ($last_pid) {
	if(file_exists('/proc/'.$last_pid))
		exit(0);
	else
		$pid_write = 1;
} else
	$pid_write = 1;

if($pid_write){
	$f = fopen($home_dir.'/bot.pid', 'w');
	if ($f !== false) {
		fwrite($f, getmypid());
		fclose($f);
	}
}

/***********************/
include 'engine.class.php';

$bot 			= new bot($home_dir);

$bot->owner 	= 'http://www.tzpolice.ru/';

define('DEBUG', 0); // логировать ли пакеты
$bot->logging 	= 1; //логировать ли действия (коннект, ехит, рестарт, чендж вейв)

$bot->add_mysql('db1', '127.0.0.6', 'tzpolice', 'CkO3Z4mDM9', 'tzpolice');
$bot->add_mysql('db2', '127.0.0.6', 'tzpolice_test', 'Vt5mwJso', 'tzpolice_test');


//$bot->add_bot('НИК', 'ПАРОЛЬ', PID(???), № сервера, array('Управляющий1', 'Управляющий2'));

// ==== тест =======
//$bot->add_bot('mine1', 'v58gwtbj', null, null, array('FANTASTISH'));
//$bot->add_bot('mine2', 'zy73x2g9', null, null, array('FANTASTISH'));
//$bot->add_bot('mine3', '4btabcyf', null, null, array('FANTASTISH'));
//$bot->MP_main	= 'mine3';
// ========= МП =========
$bot->add_bot('MP 01', 'eMTb5Mxg', null, '0', array('FANTASTISH', 'Stealth', 'Фаталити', 'FynON', 'deadbeef'));
$bot->add_bot('MP 02', 'aU9fgyK5', null, '0', array('FANTASTISH', 'Stealth', 'Фаталити', 'FynON', 'deadbeef'));
$bot->add_bot('MP 03', '8nJGU2cE', null, '0', array('FANTASTISH', 'Stealth', 'Фаталити', 'FynON', 'deadbeef'));
$bot->add_bot('MP 04', '2Ki48wXW', null, '0', array('FANTASTISH', 'Stealth', 'Фаталити', 'FynON', 'deadbeef'));
$bot->add_bot('MP 05', 'YAeQdCCg', null, '0', array('FANTASTISH', 'Stealth', 'Фаталити', 'FynON', 'deadbeef'));
$bot->add_bot('MP 06', 'Ur0FmIgF', null, '0', array('FANTASTISH', 'Stealth', 'Фаталити', 'FynON', 'deadbeef'));
$bot->add_bot('MP 07', 'Pb4n4QfH', null, '0', array('FANTASTISH', 'Stealth', 'Фаталити', 'FynON', 'deadbeef'));
// ======================

$bot->MP_main	= 'MP 01';
$bot->mod_register_module('MP');
while(1)
	{		if(file_exists($home_dir.'/bot.lock'))			exit(0);		$bot->view();		$bot->process();
	}
?>