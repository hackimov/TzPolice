<?php

DEFINE('DB', true);
include('system/mysql.php');
unset($dbuser,$dbpass);
/*
if($handle = opendir('./modules')) {
    while (false !== ($file = readdir($handle))) {
    	$mod = explode('.',$file);
        if(count($mod) == 2 && $mod[1] == 'php') {
        	$modulename = $mod[0];
        	if($db->sql_numrows($db->sql_query("SELECT * FROM modules WHERE file = '$file'")) < 1) {        		$db->sql_query("INSERT INTO modules(id,file,active) VALUES(NULL,'$file',0)");
        	}

        }
    }
    closedir($handle);
}
*/
function GetInfoFromApi($nick) {	$nick = (mb_convert_encoding($nick,"cp1251","utf8"))?mb_convert_encoding($nick,"cp1251","utf8"):$nick;    $nick = str_replace(" ","%20",$nick);
    $url = "http://www.timezero.ru/info.pl?userxml=".mb_strtolower($nick,"cp1251");
	#echo $url."\n";
	$info = file_get_contents($url);
	#echo $info."\n";
	preg_match("#<USER([^>]+)>#",$info,$data);

	if(!$data[1]) return false;
	preg_match_all("#\s([A-Za-z_-]+)=\"([^\"]+)\"#",$data[1],$data);
    $user = Array();
    foreach($data[1] as $k => $v) {    	$user[$v] = (mb_convert_encoding($data[2][$k],"cp1251","utf8"))?mb_convert_encoding($data[2][$k],"cp1251","utf8"):$data[2][$k];
    }
    return $user;
}

print_r(GetInfoFromApi($_GET[u]));



?>