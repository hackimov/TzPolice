<?php

if(!defined('SECURE')) die('Завцли йунные хакиры.');

DEFINE('DB', true);
include('mysql.php');
unset($dbuser,$dbpass);

function is_utf($text) {
 $temp = mb_convert_encoding(mb_convert_encoding($text,"cp1251","utf8"),"utf8","cp1251");
 return($text == $temp)?1:0;
}

function to_utf($text) {
	return mb_convert_encoding($text,"utf8","cp1251");
}

function to_cp($text) {
	return mb_convert_encoding($text,"cp1251","utf8");
}

function ipCheck() {
	if (getenv('HTTP_CLIENT_IP')) {
		$ip = getenv('HTTP_CLIENT_IP');
	} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif (getenv('HTTP_X_FORWARDED')) {
		$ip = getenv('HTTP_X_FORWARDED');
	} elseif (getenv('HTTP_FORWARDED_FOR')) {
		$ip = getenv('HTTP_FORWARDED_FOR');
	} elseif (getenv('HTTP_FORWARDED')) {
		$ip = getenv('HTTP_FORWARDED');
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
return $ip;
}


$in = array();
foreach($_GET as $k => $v) {
	$v = is_utf($v)?to_cp($v):$v;
	$in[$k] = addslashes(htmlspecialchars(trim($v)));
}
foreach($_POST as $k => $v) {
	$v = is_utf($v)?to_cp($v):$v;
	$in[$k] = addslashes(htmlspecialchars(trim($v)));
}
foreach($_COOKIE as $k => $v) {
	$v = is_utf($v)?to_cp($v):$v;
	$_COOKIE[$k] = addslashes(htmlspecialchars(trim($v)));
}

#ай-пи
$ip = ipCheck();

#проверяем не заблокирован ли ай-пи
$query = $db->sql_query("SELECT * FROM `site_blacklist` WHERE `ip`='$ip' LIMIT 1");
if($db->sql_numrows($query) > 0) {
	$block = $db->sql_fetchrow($query);
	echo "<br><br><blockquote style='font-family:verdana;font-size:11px'>
	<b>Нет доступа к сайту</b><br>Администрация закрыла вам доступ к сайту.<br>Причина: $block[reason]
    </blockquote>";
	exit;
}

#загружаем юзера
$user = false;
if(isset($_COOKIE['CUser']) && isset($_COOKIE['CPass'])) {	$query = $db->sql_query("SELECT a.*,b.* FROM site_users AS a LEFT JOIN locator AS b ON(a.user_name = b.login) WHERE a.user_name='".$_COOKIE['CUser']."' AND a.user_pass='".$_COOKIE['CPass']."' LIMIT 1");
	if($db->sql_numrows($query) > 0) {
		$user = $db->sql_fetchrow($query);
		#если группа админская, доступ везде максимальный.
		if($user['user_group'] == $adminGroup) {			$user['access'] = 10;
		} else {			$user['access'] = 0;
		}
		$uips = array_flip(explode(",",$user['ips']));
		if(!$uips[$ip] && $ip) {
			$db->sql_query("UPDATE site_users SET ips='".$user['ips'].",$ip' WHERE user_name='".$user['user_name']."'");
		}

	}
}
$avatar = ($user['avatar'])?$user['avatar']:"avat-default-man.gif";



#подключаемый модуль
$modulesDir = 'modules';
$moduleName = ($in['act'])?$in['act']:'news';
$query = $db->sql_query("SELECT * FROM `modules` WHERE `name`='$moduleName'");
if($db->sql_numrows($query) > 0) {
	$module = $db->sql_fetchrow($query);
	if($module['op']) {		$in['op'] = $module['op'];
	}
	#у модуля есть права доступа
	if($user && $user['user_group'] != $adminGroup && ($mod['user_access'] || $mod['group_access'])) {
		#доступы к модулю по логину
		if($mod['user_access'] != '') {			foreach(explode(",",$mod['user_access']) as $k => $v) {				$v = explode(":",$v);
				#Есть такой логин - вносим в инфу юзера уровень доступа
				if($user['user_name'] == $v[0]) {					$user['access'] = $v[1];
				break;
				}
			}
		}
		#доступы к модулю по группе
		if($mod['group_access'] != '') {
			foreach(explode(",",$mod['group_access']) as $k => $v) {
				$v = explode(":",$v);
				#Если перс в группе с доступом и должность позволяет - вносим в инфу юзера уровень доступа
				if($user['group'] == $v[0] && $user['subgroup'] >= $v[1]) {
					$user['access'] = ($user['access']<$v[2])?$v[2]:$user['access'];
				break;
				}
			}
		}
	}
}


if($moduleName == 'user_info') {
	$post_data = "{".$_SERVER['REMOTE_ADDR']."}\t".$user['user_name']."\t".date("Y-m-d H:i:s")."\t{".$_SERVER['REQUEST_URI']."}\t";
	foreach($_POST as $key => $val) $post_data.="{$key} = ".@urldecode($val)." | ";
	$post_data.="\t{".$_SERVER['HTTP_USER_AGENT']."}\n";
	$f=fopen('/home/sites/police/www/post/'.date('Y-m-d').'.txt','a+');
	fputs($f,$post_data);
	fclose($f);
}

if(isset($user['clan']) && in_array($user['clan'],$policeclans)) {
	$visit_string = date('H:i')."\t".$user['user_name']."\t".$_SERVER['REQUEST_URI']."\t".$_SERVER['REMOTE_ADDR']."\n";
	$visit_file = 'visits/'.date('Y-m-d').'.txt';
	$f = fopen($visit_file,'a+');
	fputs($f,$visit_string);
	fclose($f);
}

?>