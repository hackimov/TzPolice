<?php
$tztimeout = 0;
$content = file_get_contents('http://www.timezero.ru/info.pl?clanxml=police', 0,
	stream_context_create(
		array(
		    'http' => array(
		        'timeout' => 2
			)
		)
	)
);
if(!$content || strlen($content) < 10) {	echo "TZ API timeout!<br>\n";	$tztimeout = 1;
} else {	#echo "TZ API ok[$content]<br>\n";
}


echo date('d.m.Y H:i:s')." > ";

include("/home/sites/police/dbconn/dbconn.php");

#логи
function save($data,$filename,$path="/home/sites/police/www/blacklist/",$arg="txt") {
    $logfile = $path;
    $logfile .= ($filename)?"$filename.$arg":"tmp.$arg";
	$file = fopen($logfile,'w');
    fwrite($file,$data);
    fclose($file);
}
function to_utf($text) {
	return mb_convert_encoding($text,"utf8","cp1251");
}

#function © Deorg
function update_clan_members2($clan) {
	$cache_time = time()-3600;

	$result = mysql_query("SELECT * FROM `data_clans_cache` WHERE `clan_name` = '$clan' AND `lastupdate` > '$cache_time'");

	if(mysql_num_rows($result) < 1){
		$url = 'http://stalkerz.ru/ajax/radar.ajax.php?police=thisisdegradesecurecode&clanlist='.urlencode(strtolower($clan));
        #echo "GET: $url [<br>\n";
		$clanList = explode('|',file_get_contents($url));

		if (count($clanList) > 0){
			mysql_query(
				'delete from `data_clan_members_cache` where (`clan_name`="'.addslashes($clan).'");'
	    	);
			if(mysql_num_rows($result) == 0){
				mysql_query(
					'insert into `data_clans_cache` (`clan_name`, `lastupdate`) values (\''.addslashes($clan).'\', \''.time().'\');'
				);
	    	} else {
	     		mysql_query(
	        		'update `data_clans_cache` set `lastupdate`=\''.time().'\' where (`clan_name`="'.addslashes($clan).'");'
	             );
	    	}
		}
		foreach($clanList as $i => $userData) {
			$userData= explode(';',$userData);
			$user = array();
			foreach($userData as $ii => $tag) {
				$t = explode(':',$tag,2);
				$user[$t[0]] = $t[1];
			}
			$sql = 'insert into `data_clan_members_cache` (`clan_name`, `user_name`, `clan_s1`, `clan_s2`) values (\''.addslashes($clan).'\', \''.$user[login].'\', \''.$user[s1].'\', \''.$user[s2].'\');';
			#echo "$sql<br>\n";
			mysql_query($sql);
		}
		#echo "]<br>\n";

	}
}


#function © Invisible | edited
function update_clan_members($clan) {
	$cache_time = time()-3600;

	$result = mysql_query("SELECT * FROM `data_clans_cache` WHERE `clan_name` = '$clan' AND `lastupdate` > '$cache_time'");

	if(mysql_num_rows($result) < 1){
		$url = 'http://www.timezero.ru/info.pl?clanxml='.urlencode(strtolower($clan));
        #echo "GET: $url [<br>\n";
		$clanXML = new DomDocument();
        $ctx = stream_context_create(array(
		    'http' => array(
		        'timeout' => 5
		        )
		    )
		);
		$data = file_get_contents($url, 0, $ctx);
		@$clanXML->loadXML($data);


		if ($clanXML->getElementsByTagName('CLAN')->length > 0){
			mysql_query(
				'delete from `data_clan_members_cache` where (`clan_name`="'.addslashes($clan).'");'
	    	);
			if(mysql_num_rows($result) == 0){
				mysql_query(
					'insert into `data_clans_cache` (`clan_name`, `lastupdate`) values (\''.addslashes($clan).'\', \''.time().'\');'
				);
	    	} else {
	     		mysql_query(
	        		'update `data_clans_cache` set `lastupdate`=\''.time().'\' where (`clan_name`="'.addslashes($clan).'");'
	             );
	    	}
		}

		$memberTag = $clanXML->getElementsByTagName('USER');
		if ($memberTag->length > 0){
			for ($i=0;$i<$memberTag->length;$i++){
				$login = iconv("UTF-8", "windows-1251", $memberTag->item($i)->getAttribute('login'));
				$s1 = iconv("UTF-8", "windows-1251", $memberTag->item($i)->getAttribute('clan_s1'));
				$s2 = iconv("UTF-8", "windows-1251", $memberTag->item($i)->getAttribute('clan_s2'));
				$sql = 'insert into `data_clan_members_cache` (`clan_name`, `user_name`, `clan_s1`, `clan_s2`) values (\''.addslashes($clan).'\', \''.$login.'\', \''.$s1.'\', \''.$s2.'\');';
				#echo "$sql<br>\n";
				mysql_query($sql);
			}
		}
		#echo "]<br>\n";

	}
}


function updateUserInBlackList($login,$type) {
	$url = "http://stalkerz.ru/ajax/radar.ajax.php?police=thisisdegradesecurecode&nick=".urlencode($login);
    $i=0;
 	while($user = file_get_contents($url)) {
    	if($user && strlen($user) > 10) {
        	foreach(explode(';',$user) as $k => $v) {
        		$v = explode(':',$v);
        		$userdata[$v[0]] = $v[1];
        	}
        	break;
    	} else {
    		#турум пум пум
			sleep(1);
    	}
    $i++;
    }

    if($userdata['login'] && $type=='login') {
    	mysql_query("UPDATE deorg_black SET clan = '".$userdata['clan']."', lvl = '".$userdata['level']."', pro = '".$userdata['pro']."', rank = '".$userdata['pvprank']."' WHERE login='".$userdata['login']."'");
    }
    return $userdata;
}

function getClanInCache($black) {
	$clan = $black['clan'];
	$query = mysql_query("SELECT * FROM `data_clan_members_cache` WHERE `clan_name`='$clan'");
    $data = Array();
	$tmp['description'] = "Война с кланом $clan";
	$tmp['logs'] = str_replace(","," ",$black['logs']);;
	$tmp['author'] = $black['author'];
	$tmp['contribution'] = $black['contribution'];

	while($user = mysql_fetch_array($query)) {
  		if($user['user_name']) {
  			$tmp['login'] = $user['user_name'];
        	$data[] = $tmp;
  		}
	}
    return $data;
}


$query = mysql_query("SELECT * FROM deorg_black WHERE align = 'police'");

if(mysql_num_rows($query) > 0) {
	#TAD NuCLeaR,5000,317924074753,Нападение | Автор: Deorg;

	while($black = mysql_fetch_array($query)) {
		$user = Array();
		$clan = Array();
        if($black['clan'] != '' && $black['type'] == 'clan') {        	if($tztimeout > 0) {        		#echo "tztimedout($tztimeout)<br>\n";        		update_clan_members2($black['clan']);
        	} else {        		#echo "tztimedout($tztimeout)<br>\n";
        		update_clan_members($black['clan']);
        	}
        }
        if($black['login'] != '' && $black['type'] == 'login') {
        	updateUserInBlackList($black['login'],$black['type']);
        }

		if($black['status'] > 0) {
			if($black['type'] == 'login') {
				$user['login'] = $black['login'];
		        $user['description'] = str_replace(","," ",$black['description']);
		        $user['logs'] =  str_replace(","," ",$black['logs']);
		        $user['author'] = $black['author'];
		        $user['contribution'] = $black['contribution'];
				$black_users[] = $user;
			} else {
				$black_clans[$black['clan']] = getClanInCache($black);
			}
		}

	}
    $blackusers = array();
    $blackclans = array();
    #print_r($black_clans);
    $JSout = Array();

    foreach($black_users as $k => $user) {    	echo $user['login'].",".$user['contribution'].",,".$user['logs']."|".$user['description']."<br>\n";
    	$blackusers[] = $user['login'].",".$user['contribution'].",,".$user['logs']."|".$user['description'];
    	$JSout[] = "['".$user['login']."']";
    }
    save(implode(';',$blackusers),'logins');

    foreach($black_clans as $clan => $users) {
    	echo "$clan [\n<br>";
    	foreach($users as $k => $user) {    		echo "\t&nbsp;&nbsp;&nbsp;".$user['login'].",".$user['contribution'].",,".$user['logs']."|".$user['description']."<br>\n";
    		$JSout[] = "['".$user['login']."']";
			$blackclans[] = $user['login'].",".$user['contribution'].",,".$user['logs']."|".$user['description'];
    	}
    	echo "]\n<br>";
    }

    save(to_utf("UpdateTZPBL([".implode(',',$JSout)."]);"),"jbl_new","/home/sites/police/www/","js");

    save(implode(';',$blackclans),'clans');


    save(implode(';',$blackusers).";".implode(';',$blackclans),'all');

}

echo date('d.m.Y H:i:s')." \n";



?>