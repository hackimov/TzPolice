<?php
if(!defined('FUNC')) die('Wood ;)');


function send_sysmsg($nick, $message){
	$noerror=1;
	$message = "\n".$nick.' || '.$message."\n";
	$fname = '/home/sites/police/bot_fees/alerts.txt';
	if(file_exists($fname)) chmod($fname, 0777);
	if($handle = fopen($fname, 'a')){
		if(fwrite($handle, $message) === FALSE)	{
			$noerror=0;
		}
		fclose($handle);
	} else {
		$noerror=0;
	}
return $noerror;
}

function mywordwrap($string) {
	$length = strlen($string);
	for ($i=0; $i<=$length; $i=$i+1) {
		$char = substr($string, $i, 1);
		if ($char == '<') $skip=1;
		elseif ($char == '>') $skip=0;
		elseif ($char == ' ') $wrap=0;

		if ($skip==0) $wrap++;
		$returnvar .= $char;
		if ($wrap > 40) { // alter this number to set the maximum word length
			$returnvar .= '<wbr>';
			$wrap = 0;
		}
	}
return $returnvar;
}

function unhtmlentities ($string) {
	$trans_tbl = get_html_translation_table (HTML_ENTITIES);
	$trans_tbl = array_flip ($trans_tbl);
	return strtr ($string, $trans_tbl);
}

//Попытка утягать страницу форума
function ForumConn($path, $enforce=0) {
	$sock = @fsockopen("www.timezero.ru", 80, $er1, $er2, 5);
	if (@$sock) {
		fputs($sock, "GET /".$path." HTTP/1.0\r\n");
		fputs($sock, "Host: www.timezero.ru \r\n");
		fputs($sock, "Content-type: application/x-www-url-encoded \r\n");
		fputs($sock, "\r\n\r\n");
		$tmp_headers = '';
		while ($str = trim(fgets($sock, 4096))) $tmp_headers .= $str."\n";
		$tmp_body = '';
		while (!feof($sock)) $tmp_body .= fgets($sock, 4096);
		$tmp_pos1 = strpos($tmp_body, 'about="');
		if($tmp_pos1!==false) {
			$tmp_str1 = substr($tmp_body, 0, $tmp_pos1);
			$tmp_str2 = substr($tmp_body, strpos($tmp_body, '"', $tmp_pos1+8));
			$tmp_body = $tmp_str1.' '.$tmp_str2;
		}
		$tmp_body = htmlspecialchars($tmp_body);
		$tmp_body = uencode($tmp_body, 'w');
		if(strpos($tmp_body, 'Internal Server Error')===false) {
			$tmp_body['error'] = 0;
			return $tmp_body;
		} else {
			if($enforce==0) {
				$funcerror['error'] = 'TIMEOUT';
				return $funcerror;
			} else {
				sleep(1);
				return Forum($path,1);
			}
		}
	} else {
		$funcerror['error'] = 'NOT_CONNECTED';
		return $funcerror;
	}
}

function ShowPagesComm($CurPage,$TotalPages,$ShowMax,$QueryStr) {
	$PrevList=floor(($CurPage-1)/$ShowMax)*$ShowMax;
	$NextList=$PrevList+$ShowMax+1;
	if($PrevList>=$ShowMax*2) {
		echo '<a href="javascript:{}" onclick="opencomments(\''.$QueryStr.'\', 1)" title="В самое начало"><< </a> ';
	}
	if($PrevList>0) {
		echo '<a href="javascript:{}" onclick="opencomments(\''.$QueryStr.'\', \''.$PrevList.'\')"  title="Предыдущие '.$ShowMax.' страниц">...</a> ';
	}
	for($i=$PrevList+1;$i<=$PrevList+$ShowMax;$i++) if($i<=$TotalPages) {
		if($i==$CurPage) {
			echo '<u>'.$i.'</u> ';
		} else {
			echo '<a href="javascript:{}" onclick="opencomments(\''.$QueryStr.'\', '.$i.')">'.$i.'</a> ';
		}
	}
	if($NextList<=$TotalPages) {
		echo '<a href="javascript:{}" onclick="opencomments(\''.$QueryStr.'\', \''.$NextList.'\')"  title="Следующие '.$ShowMax.' страниц">...</a> ';
	}
	if($CurPage<$TotalPages) {
		echo '<a href="javascript:{}" onclick="opencomments(\''.$QueryStr.'\', \''.$TotalPages.'\')" title="В самый конец"> >></a>';
	}
}

//UserInfo functions
function sub($text, $st1, $st2, $init=0) {
	$offset1=@strpos($text, $st1, $init)+strlen($st1);
	if($offset1==strlen($st1)) {		return 0;
	} else {
		$offset2=strpos($text, $st2, $offset1);
		$res=@substr($text,$offset1,$offset2-$offset1);
		if(!empty($res)) {			return $res;
		} else {			return 0;
		}
	}
}

#берём инфу по персу из бд
function locateUser($login) {	$login = addslashes(htmlspecialchars(trim($login)));
	$query = mysql_query("SELECT * FROM locator WHERE login = '$login' LIMIT 1");
	$needupdate = 0;
	$needinsert = 0;
	if(mysql_num_rows($query) > 0) {
		$user = mysql_fetch_assoc($query);
		$user['level'] = $user['lvl'];
		$user['man'] = $user['gender'];
		$user['gen'] = $user['gender'];

		if(time()-$user['utime'] > 86400) {			$needupdate++;
		}
	} else {		$needinsert++;
	}

	#временно, пока локатор нормально не станет обновляться.
	if($needupdate > 0 || $needinsert > 0) {		$user = TZConn($login, 1, 1);

		if($user['level'] > 0) {			if($needinsert > 0) {				$query = "INSERT INTO locator (`id`,`addtime`,`utime`,`location`,`server`,`clan`,`login`,`lvl`,`pro`,`pvpr`,`pvprank`,`gender`)
				VALUES(NULL,'".$user['addtime']."','".$user['utime']."','".$user['location']."','".$user['server']."','".$user['clan']."',
				'".$user['login']."','".$user['level']."','".$user['pro']."','".$user['pvpr']."','".$user['pvprank']."','".$user['gender']."')";
			} else {				$query = "UPDATE locator SET `utime` = '".$user['utime']."', `location` = '".$user['location']."', `server` = '".$user['server']."',
				`clan` = '".$user['clan']."', `lvl` = '".$user['level']."', `pro` = '".$user['pro']."', `pvpr` = '".$user['pvpr']."',
				`pvprank` = '".$user['pvprank']."' WHERE login='".$user['login']."'";

			}			mysql_query($query);
		}
	}
	return $user;
}

function TZConn($login, $enforce=0,$leave) {
	if(!$leave) return locateUser($login);

	$url = 'http://www.timezero.ru/info.pl?userxml='.urlencode(tzpd_strtolower($login));
	$reserve_url = 'http://stalkerz.ru/ajax/radar.ajax.php?police=thisisdegradesecurecode&nick='.urlencode(tzpd_strtolower($login));
	$reserve_params = array('clan','login','level','pro','gender','pvprank','pvpr','utime','server','addtime','location');

	if($enforce == 0) {
		$userXML = new DomDocument();
		@$userXML->loadXML(file_get_contents($url, false, stream_context_create(array('http'=>array('timeout'=>2)))));
		$userTag = $userXML->getElementsByTagName('USER');
	}

	$userInfo = array();

	if($enforce == 0 && $userTag->length == 1){
		$userAttributes = $userTag->item(0)->attributes;
		if(!is_null($userAttributes)){
			foreach($userAttributes as $index=>$attribute) {
				$userInfo[$attribute->name] = iconv("UTF-8", "windows-1251", $attribute->value);
			}
		}
	} else {
		$stalk_info = file_get_contents($reserve_url, false, stream_context_create(array('http'=>array('timeout'=>2))));
		if($stalk_info != ''){
			$stalk_info = explode(';',$stalk_info);
			foreach($stalk_info as $param){
				$param = explode(':', $param);
				if(in_array($param[0], $reserve_params)) $userInfo[$param[0]] = $param[1];
			}
		}
	}
	#фикс, если инфы по персу нет
	$userInfo['login'] = ($userInfo['login'])?$userInfo['login']:$login;
	$userInfo['level'] = ($userInfo['level'])?$userInfo['level']:0;
	$userInfo['pro'] = ($userInfo['pro'])?$userInfo['pro']:0;
	$userInfo['gender'] = ($userInfo['gender'])?$userInfo['gender']:1;
	$userInfo['pvprank'] = ($userInfo['pvprank'])?$userInfo['pvprank']:1;

return $userInfo;
}

function TZConn_update_clan_members($clan){
	$cache_time = 1*24*60*60;

	$result = mysql_query('select `lastupdate` from `data_clans_cache` where (`clan_name`="'.addslashes($clan).'") limit 1');
	if(mysql_num_rows($result) > 0) $row = mysql_fetch_assoc($result);

	if((mysql_num_rows($result) == 0) || ($row['lastupdate'] < time()-$cache_time)){
  		$url = 'http://www.timezero.ru/info.pl?clanxml='.urlencode(tzpd_strtolower($clan));

		$clanXML = new DomDocument();
		@$clanXML->loadXML(
			file_get_contents($url, false, stream_context_create(array('http'=>array('timeout'=>2))))
		);

		if ($clanXML->getElementsByTagName('CLAN')->length > 0){
		    mysql_query('delete from `data_clan_members_cache` where (`clan_name`="'.addslashes($clan).'");');
		    if (mysql_num_rows($result) == 0){
				mysql_query('insert into `data_clans_cache` (`clan_name`, `lastupdate`) values (\''.addslashes($clan).'\', \''.time().'\');');
		    } else {
				mysql_query('update `data_clans_cache` set `lastupdate`=\''.time().'\' where (`clan_name`="'.addslashes($clan).'");');
		    }
		}

		$memberTag = $clanXML->getElementsByTagName('USER');
		if ($memberTag->length > 0){
			for ($i=0;$i<$memberTag->length;$i++){
				$login = iconv("UTF-8", "windows-1251", $memberTag->item($i)->getAttribute('login'));
				$s1 = iconv("UTF-8", "windows-1251", $memberTag->item($i)->getAttribute('clan_s1'));
				$s2 = iconv("UTF-8", "windows-1251", $memberTag->item($i)->getAttribute('clan_s2'));
	      		mysql_query('insert into `data_clan_members_cache` (`clan_name`, `user_name`, `clan_s1`, `clan_s2`) values (\''.addslashes($clan).'\', \''.$login.'\', \''.$s1.'\', \''.$s2.'\');');
			}
		}
	}
}

function GetUserInfo($nick, $usecache=0) {
	$cache_time = 24*60*60;

	$result = mysql_query('select `data_str`, `lastupdate` from `data_cache` where (`user_name`="'.addslashes($nick).'") limit 1');
	if (mysql_num_rows($result) > 0) $row = mysql_fetch_assoc($result);

	if (($usecache == 0) || ((mysql_num_rows($result) > 0) &&($row['lastupdate'] < time()-$cache_time)) || (mysql_num_rows($result) == 0)) {
		$userInfo = TZConn($nick);

		if (sizeof($userInfo) > 0) {
    		if (mysql_num_rows($result) == 0) mysql_query('insert into `data_cache` (`user_name`, `data_str`, `lastupdate`) VALUES (\''.$nick.'\', \''.serialize($userInfo).'\', \''.time().'\');');
    		if (mysql_num_rows($result) > 0) mysql_query('update `data_cache` set `data_str` = \''.serialize($userInfo).'\', `lastupdate` = \''.time().'\' where (`user_name`="'.addslashes($nick).'");');

    		tz_users_update($userInfo);
    		mysql_query('UPDATE `site_users` SET `avatar`=\''.$userInfo['img'].'\', `clan`=\''.$userInfo['clan'].'\' WHERE `user_name`=\''.$userInfo['login'].'\' LIMIT 1;');
   			mysql_query('UPDATE `fotos_users` SET `clan`=\''.$userInfo['clan'].'\' WHERE `nick`=\''.$userInfo['login'].'\' LIMIT 1;');
		}
	} else {
		$userInfo = unserialize($row['data_str']);
	}

	if ($userInfo['clan'] != '') TZConn_update_clan_members($userInfo['clan']);
	$result = mysql_query('select `clan_s1`, `clan_s2` from `data_clan_members_cache` where (`user_name`="'.$userInfo['login'].'") limit 1');

	if (mysql_num_rows($result) > 0) {
		if($userInfo['clan'] != '') {
    		$row = mysql_fetch_assoc($result);
    		$userInfo['s1'] = $row['clan_s1'];
    		$userInfo['s2'] = $row['clan_s2'];
		} else {
    		mysql_query('delete from `data_clan_members_cache` where (`user_name`="'.$userInfo['login'].'") limit 1;');
  		}
	}
	//Не знаю нафига это, но в старом варианте было.
	if ($userInfo['man'] == 1) {$userInfo['gen'] = 1;} else {$userInfo['gen'] = 2;}
return $userInfo;
}

//я в ахуе от этих функций(((
function ParseNews($buf, $AllowTags, $replaceBR=1) {
	$outttvar = '';
	if($AllowTags==0) $buf = strip_tags($buf, '<b><i><u><div><wbr><strike><embed><object>');
	$buf = stripslashes($buf);
	if($replaceBR==1) $buf = str_replace("\n", "<br>", $buf);
	$FontColors1 = array('[red]', '[/red]', '[green]', '[/green]', '[blue]', '[/blue]');
	$FontColors2 = array('<font color=red>', '</font>', '<font color=green>', '</font>', '<font color=blue>', '</font>');
	$buf = str_replace($FontColors1,$FontColors2,$buf);
	$buf = str_replace('{MP_LIST}',mp_list(),$buf);
	$text = $buf;
	$text = preg_replace("/\[log\](.*?)\[\/log\]/si", "<b><font color='blue'>\\1</font> [<a href='#; return false' onclick=\"ClBrd2('\\1')\" alt=\"Скопировать в буфер обмена\">скопировать</a> / <a href='#; return false' onclick=\"LogWin('\\1')\" alt='Просмотреть бой'>просмотреть</a>]</b>", $text);
	$text = preg_replace("/\[pers nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<b>\\1</b> [\\2]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\3.gif' border='0'>", $text);
	$text = preg_replace("/\[pers clan=(.*?) nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/clans/\\1.gif' alt='\\1' border='0' height='16' width='28'><b>\\2</b> [\\3]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\4.gif' border='0'>", $text);
	$text = preg_replace("/\[clan=(.*?)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+) nick=([0-9A-Za-z *\-]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
	$buf = $text;
	$buf = preg_replace("/\[prof\](.*?)\[\/prof\]/si","<img border=0 style='vertical-align:text-bottom' src='/_imgs/pro/\\1.gif'>",$buf);
	$buf = preg_replace("/\[clan\](.*?)\[\/clan\]/si","<img border=0 src='/_imgs/clans/\\1.gif'>",$buf);
	$buf = eregi_replace("\\[img\\]([a-z0-9\\./\\-]+)\\[/img\\]","<img border=0 src='/user_data/\\1'>",$buf);
	$buf = eregi_replace("\\[imgprev\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><img border=0 src='/user_data/\\1/thumb/\\2' ALT='Нажмите, чтобы увеличить'></a>",$buf);
	$buf = preg_replace("/\[URL\](.*?);(.*?)\[\/url\]/si", "<a href='\\1' target=_blank>\\2</a>",$buf);
	$buf = explode(' ',$buf);
	for($i=0;$i<count($buf);$i++) {
		if(eregi(':([a-z0-9_]+):',$buf[$i],$ok)) {
			if(is_file('/home/sites/police/www/_imgs/smiles/'.$ok[1].'.gif')) {
				$buf[$i]=str_replace(':'.$ok[1].':', '<img border=0 src="/_imgs/smiles/'.$ok[1].'.gif"> ', $buf[$i]);
			}
			$outttvar .= $buf[$i].' ';
		} else {
			$outttvar .= $buf[$i].' ';
		}
	}
	if (preg_match_all('#(.*)\[dialog=".*"\](.*)\[/dialog\]#Usi', $outtvar, $t)) {
		for($i = 0; $i < count($t[0]); $i++) {
			$tmp = '';
			preg_match_all('#\[dialog="(.*?)"\]#si', $t[0][$i], $t1);
			$tmp .= $t[1][$i];
			$tmp .= '<div class="quote"><img height="64" width="62" src="http://www.tzpolice.ru/i/avatar/'.$t1[1][0].'" class="dialog">';
			$tmp .= preg_replace('#\[(.*)\] private \[(.*?)\]#', '<b>[${1}] <font color="red">private [${2}]</font></b>', $t[2][$i]);
			$tmp .= '<div class="clear"></div></div>';
			$pos1=strpos($text,$t[0][$i]);
			$pos2=strlen($t[0][$i]);
			$outttvar=substr($outttvar,0,$pos1).$tmp.substr($outttvar,$pos1+$pos2);
		}
	}
	$outttvar = mywordwrap($outttvar);
	echo ($outttvar);
	//	return $outttvar;
}

function ParseNews2($text) {
	$text = stripslashes($text);
	$text = strip_tags($text, '<a><div><table><tr><td><strike><script><object><param><embed><object>');
	if (preg_match_all('#(.*)\[dialog=".*"\](.*)\[/dialog\]#Usi', $text, $t)) {
		for($i = 0; $i < count($t[0]); $i++) {
			$tmp = '';
			preg_match_all('#\[dialog="(.*?)"\]#si', $t[0][$i], $t1);
			$tmp .= $t[1][$i];
			$tmp .= '<div class="quote"><img height="64" width="62" src="http://www.tzpolice.ru/i/avatar/'.$t1[1][0].'" class="dialog">';
			$tmp .= preg_replace('#\[(.*)\] private \[(.*?)\]#', '<b>[${1}] <font color="red">private [${2}]</font></b>', $t[2][$i]);
			$tmp .= '<div class="clear"></div></div>';
			$pos1=strpos($text,$t[0][$i]);
			$pos2=strlen($t[0][$i]);
			$text=substr($text,0,$pos1).$tmp.substr($text,$pos1+$pos2);
		}
	}

	$text = preg_replace("/\[log\](.*?)\[\/log\]/si", "<b><font color='blue'>\\1</font> [<a href='#; return false' onclick=\"ClBrd2('\\1')\" alt=\"Скопировать в буфер обмена\">скопировать</a> / <a href='#; return false' onclick=\"LogWin('\\1')\" alt='Просмотреть бой'>просмотреть</a>]</b>", $text);
	$text = preg_replace("/\[url=([\w]+?:\/\/.*?)\](.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url\]([\w]+?:\/\/.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\1</a>", $text);
	$text = preg_replace("/\[url\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\1</a>", $text);
	$text = preg_replace('/\[color="(\#[0-9A-F]{6}|[a-z]+)"\]/si', "<font color='\\1'>", $text);
	$text = preg_replace("/\[color=(\#[0-9A-F]{6}|[a-z]+)\]/si", "<font color='\\1'>", $text);
	$text = str_replace('[/color]', '</font>', $text);
	$text = preg_replace("/\[pers nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<b>\\1</b> [\\2]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\3.gif' border='0'>", $text);
	$text = preg_replace("/\[pers clan=(.*?) nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/clans/\\1.gif' alt='\\1' border='0' height='16' width='28'><b>\\2</b> [\\3]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\4.gif' border='0'>", $text);
	$text = preg_replace("/\[clan=(.*?)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+) nick=([0-9A-Za-z *\-]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
	$text = str_replace('[b]', '<b>', $text);
	$text = str_replace('[/b]', '</b>', $text);
	$text = str_replace('[u]', '<u>', $text);
	$text = str_replace('[/u]', '</u>', $text);
	$text = str_replace('[i]', '<i>', $text);
	$text = str_replace('[/i]', '</i>', $text);
	$text = str_replace('[quote]', '<table><tr><td class="quote-news">', $text);
	$text = str_replace('[/quote]', '</td></tr></table>', $text);
	$text = str_replace('[left]', '<div align="left">', $text);
	$text = str_replace('[/left]', '</div>', $text);
	$text = str_replace('[center]', '<div align="center">', $text);
	$text = str_replace('[/center]', '</div>', $text);
	$text = str_replace('[small]', '<font size="-2">', $text);
	$text = str_replace('[/small]', '</font>', $text);
	$text = str_replace('[right]', '<div align="right">', $text);
	$text = str_replace('[/right]', '</div>', $text);
	$text = preg_replace("/\[imageleft\](.*?)\[\/image\]/si", "<img border='0' src='\\1' class='leftimg'>", $text);
	$text = str_replace('[image]', '<img border="0" src="', $text);
	$text = str_replace('[/image]', '">', $text);
	$text = str_replace('[list]', '<ul>', $text);
	$text = str_replace('[list=x]', '<ol>', $text);
	$text = str_replace('[*]', '<li>', $text);
	$text = str_replace('[/list]', '</ul>', $text);
	$text = str_replace('[/list=x]', '</ol>', $text);
	$text = eregi_replace("\\[img\\]([a-z0-9\\./\\-]+)\\[/img\\]", "<img border=0 src='/user_data/\\1'>", $text);
	$text = eregi_replace("\\[imgleft\\]([a-z0-9\\./\\-]+)\\[/img\\]", "<div  style='float: left; padding-top: 5px; padding-right: 15px; padding-bottom: 10px; padding-left: 0px;'><img border='0' src='/user_data/\\1'></div>", $text);
	$text = eregi_replace("\\[imgprev\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><img border=0 src='/user_data/\\1/thumb/\\2' ALT='Нажмите, чтобы увеличить'></a>",$text);
	$text = eregi_replace("\\[imgprevleft\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><div  style='float: left; padding-top: 5px; padding-right: 15px; padding-bottom: 10px; padding-left: 0px;'><img border='0' src='/user_data/\\1/thumb/\\2' ALT='Нажмите, чтобы увеличить'></div></a>",$text);
	$text = str_replace("\n", '<br>', $text);
	$tmp = explode(' ',$text);
	for($i=0;$i<count($tmp);$i++) {
		if(eregi(':([a-z0-9_]+):', $tmp[$i], $ok)) {
			if(is_file('_imgs/smiles/'.$ok[1].'.gif'))
			$text=str_replace(':'.$ok[1].':', '<img src="_imgs/smiles/'.$ok[1].'.gif"> ', $text);
		}
	}
	echo ($text);
//	return true;
}


function ParseNews2a($text) {
	$text = stripslashes($text);
	$text = strip_tags($text, '<a><div><table><tr><td><strike><script><strong><em><img><br><embed><object>');

	if (preg_match_all('#(.*)\[dialog=".*"\](.*)\[/dialog\]#Usi', $text, $t)) {
		for($i = 0; $i < count($t[0]); $i++) {
			$tmp = '';
			preg_match_all('#\[dialog="(.*?)"\]#si', $t[0][$i], $t1);
			$tmp .= $t[1][$i];
			$tmp .= '<div class="quote"><img height="64" width="62" src="http://www.tzpolice.ru/i/avatar/'.$t1[1][0].'" class="dialog">';
			$tmp .= preg_replace('#\[(.*)\] private \[(.*?)\]#', '<b>[${1}] <font color="red">private [${2}]</font></b>', $t[2][$i]);
			$tmp .= '<div class="clear"></div></div>';
			$pos1=strpos($text,$t[0][$i]);
			$pos2=strlen($t[0][$i]);
			$text=substr($text,0,$pos1).$tmp.substr($text,$pos1+$pos2);
		}
	}

	$text = preg_replace("/\[log\](.*?)\[\/log\]/si", "<b><font color='blue'>\\1</font> [<a href='#; return false' onclick=\"ClBrd2('\\1')\" alt=\"Скопировать в буфер обмена\">скопировать</a> / <a href='#; return false' onclick=\"LogWin('\\1')\" alt='Просмотреть бой'>просмотреть</a>]</b>", $text);
	$text = preg_replace("/\[url=([\w]+?:\/\/.*?)\](.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url\]([\w]+?:\/\/.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\1</a>", $text);
	$text = preg_replace("/\[url\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\1</a>", $text);
	$text = preg_replace('/\[color="(\#[0-9A-F]{6}|[a-z]+)"\]/si', "<font color='\\1'>", $text);
	$text = preg_replace("/\[color=(\#[0-9A-F]{6}|[a-z]+)\]/si", "<font color='\\1'>", $text);
	$text = str_replace('[/color]', '</font>', $text);
	$text = preg_replace("/\[pers nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<b>\\1</b> [\\2]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\3.gif' border='0'>", $text);
	$text = preg_replace("/\[pers clan=(.*?) nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/clans/\\1.gif' alt='\\1' border='0' height='16' width='28'><b>\\2</b> [\\3]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\4.gif' border='0'>", $text);
	$text = preg_replace("/\[clan=(.*?)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+) nick=([0-9A-Za-z *\-]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
	$text = str_replace('[b]', '<b>', $text);
	$text = str_replace('[/b]', '</b>', $text);
	$text = str_replace('[u]', '<u>', $text);
	$text = str_replace('[/u]', '</u>', $text);
	$text = str_replace('[i]', '<i>', $text);
	$text = str_replace('[/i]', '</i>', $text);
	$text = str_replace('[quote]', '<table><tr><td class="quote-news">', $text);
	$text = str_replace('[/quote]', '</td></tr></table>', $text);
	$text = str_replace('[left]', '<div align="left">', $text);
	$text = str_replace('[/left]', '</div>', $text);
	$text = str_replace('[center]', '<div align="center">', $text);
	$text = str_replace('[/center]', '</div>', $text);
	$text = str_replace('[small]', '<font size="-2">', $text);
	$text = str_replace('[/small]', '</font>', $text);
	$text = str_replace('[right]', '<div align="right">', $text);
	$text = str_replace('[/right]', '</div>', $text);
	$text = preg_replace("/\[imageleft\](.*?)\[\/image\]/si", "<img border='0' src='\\1' class='leftimg'>", $text);
	$text = str_replace('[image]', '<img border="0" src="', $text);
	$text = str_replace('[/image]', '">', $text);
	$text = str_replace('[list]', '<ul>', $text);
	$text = str_replace('[list=x]', '<ol>', $text);
	$text = str_replace('[*]', '<li>', $text);
	$text = str_replace('[/list]', '</ul>', $text);
	$text = str_replace('[/list=x]', '</ol>', $text);
	$text = eregi_replace("\\[img\\]([a-z0-9\\./\\-]+)\\[/img\\]", "<img border=0 src='/user_data/\\1'>", $text);
	$text = eregi_replace("\\[imgleft\\]([a-z0-9\\./\\-]+)\\[/img\\]", "<div  style='float: left; padding-top: 5px; padding-right: 15px; padding-bottom: 10px; padding-left: 0px;'><img border='0' src='/user_data/\\1'></div>", $text);
	$text = eregi_replace("\\[imgprev\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><img border=0 src='/user_data/\\1/thumb/\\2' ALT='Нажмите, чтобы увеличить'></a>",$text);
	$text = eregi_replace("\\[imgprevleft\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><div  style='float: left; padding-top: 5px; padding-right: 15px; padding-bottom: 10px; padding-left: 0px;'><img border='0' src='/user_data/\\1/thumb/\\2' ALT='Нажмите, чтобы увеличить'></div></a>",$text);
	$text = str_replace("\n", '<br>', $text);
	$tmp = explode(' ',$text);

	for($i=0;$i<count($tmp);$i++) {
		if(eregi(':([a-z0-9_]+):', $tmp[$i], $ok)) {
			if(is_file('_imgs/smiles/'.$ok[1].'.gif'))
		$text=str_replace(':'.$ok[1].':', '<img src="_imgs/smiles/'.$ok[1].'.gif"> ', $text);
		}
	}
	echo ($text);
	//	return true;
}

function ParseNews3($text) {
        $text = stripslashes($text);
        $text = strip_tags($text, '<a><div><table><tr><td><strike><embed><object>');

	if (preg_match_all('#(.*)\[dialog=".*"\](.*)\[/dialog\]#Usi', $text, $t)) {
		for($i = 0; $i < count($t[0]); $i++) {
			$tmp = '';
			preg_match_all('#\[dialog="(.*?)"\]#si', $t[0][$i], $t1);
			$tmp .= $t[1][$i];
			$tmp .= '<div class="quote"><img height="64" width="62" src="http://www.tzpolice.ru/i/avatar/'.$t1[1][0].'" class="dialog">';
			$tmp .= preg_replace('#\[(.*)\] private \[(.*?)\]#', '<b>[${1}] <font color="red">private [${2}]</font></b>', $t[2][$i]);
			$tmp .= '<div class="clear"></div></div>';
			$pos1=strpos($text,$t[0][$i]);
			$pos2=strlen($t[0][$i]);
			$text=substr($text,0,$pos1).$tmp.substr($text,$pos1+$pos2);
		}
	}
	$text = preg_replace("/\[log\](.*?)\[\/log\]/si", "<b><font color='blue'>\\1</font> [<a href='#; return false' onclick=\"ClBrd2('\\1')\" alt=\"Скопировать в буфер обмена\">скопировать</a> / <a href='#; return false' onclick=\"LogWin('\\1')\" alt='Просмотреть бой'>просмотреть</a>]</b>", $text);
	$text = preg_replace("/\[url=([\w]+?:\/\/.*?)\](.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url\]([\w]+?:\/\/.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\1</a>", $text);
	$text = preg_replace("/\[url\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\1</a>", $text);
	$text = preg_replace("/\[color=(\#[0-9A-F]{6}|[a-z]+)\]/si", "<font color='\\1'>", $text);
	$text = str_replace('[/color]', '</font>', $text);
	$text = preg_replace("/\[pers nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<b>\\1</b> [\\2]<img style='vertical-align:text-bottom' src='/_imgs/pro/i\\3.gif' border='0'>", $text);
	$text = preg_replace("/\[pers clan=(.*?) nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='/_imgs/clans/\\1.gif' alt='\\1' border='0' height='16' width='28'><b>\\2</b> [\\3]<img style='vertical-align:text-bottom' src='/_imgs/pro/i\\4.gif' border='0'>", $text);
	$text = preg_replace("/\[clan=(.*?)\]/si", "<img src='/_imgs/clans/\\1.gif' alt='\\1' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='/_imgs/pro/i\\1.gif' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+) nick=([0-9A-Za-z *\-]+)\]/si", "<img style='vertical-align:text-bottom' src='/_imgs/pro/i\\1.gif' border='0'>", $text);
	$text = str_replace('[b]', '<b>', $text);
	$text = str_replace('[/b]', '</b>', $text);
	$text = str_replace('[u]', '<u>', $text);
	$text = str_replace('[/u]', '</u>', $text);
	$text = str_replace('[i]', '<i>', $text);
	$text = str_replace('[/i]', '</i>', $text);
	$text = str_replace('[quote]', '<table><tr><td class="quote-news">', $text);
	$text = str_replace('[/quote]', '</td></tr></table>', $text);
	$text = str_replace('[left]', '<div align="left">', $text);
	$text = str_replace('[/left]', '</div>', $text);
	$text = str_replace('[center]', '<div align="center">', $text);
	$text = str_replace('[/center]', '</div>', $text);
	$text = str_replace('[small]', '<font size="-2">', $text);
	$text = str_replace('[/small]', '</font>', $text);
	$text = str_replace('[right]', '<div align="right">', $text);
	$text = str_replace('[/right]', '</div>', $text);
	$text = preg_replace("/\[imageleft\](.*?)\[\/image\]/si", "<img border='0' src='\\1' class='leftimg'>", $text);
	$text = str_replace('[image]', '<img border="0" src="', $text);
	$text = str_replace('[/image]', '">', $text);
	$text = str_replace('[list]', '<ul>', $text);
	$text = str_replace('[list=x]', '<ol>', $text);
	$text = str_replace('[*]', '<li>', $text);
	$text = str_replace('[/list]', '</ul>', $text);
	$text = str_replace('[/list=x]', '</ol>', $text);
	$text = eregi_replace("\\[img\\]([a-z0-9\\./\\-]+)\\[/img\\]","<img border=0 src='/user_data/\\1'>",$text);
	$text = eregi_replace("\\[imgprev\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><img border=0 src='/user_data/\\1/thumb/\\2' ALT='Нажмите, чтобы увеличить'></a>",$text);
	$text = str_replace("\n", '<br>', $text);
	$tmp = explode(' ',$text);

	for($i=0;$i<count($tmp);$i++) {
		if(eregi(':([a-z0-9_]+):', $tmp[$i], $ok)) {
			if(is_file('_imgs/smiles/'.$ok[1].'.gif')) {
  				$text = str_replace(':'.$ok[1].':', '<img src="/_imgs/smiles/'.$ok[1].'.gif"> ',$text);
  			}
		}
	}
	return ($text);
}

function GetClan($source) {
	if(strlen($source)>2) {
		return '<img src="http://www.timezero.ru/i/clans/'.$source.'.gif" border=0>';
	} else {		return ' ';
	}
}

function GetUser($id,$nick,$admin) {
	if(AuthStatus==1 && (abs(AccessLevel) & AccessUsers))  {
		return '<b><a href="?act=user_info&UserId='.$id.'" target="_blank">'.$nick.'</a></b> ';
	} else {
		return '<b>'.$nick.'</b> ';
	}
}

function MakePreview($src,$tgt,$Wmax,$Hmax,$type,$name) {
	if(file_exists($src)) {
		if(@$type=='jpg') $im=imageCreateFromJpeg($src);
		if(@$type=='gif') $im=imageCreateFromGif($src);
		if(@$type=='png') $im=imageCreateFromPng($src);
		$width=imageSX($im);
		$height=imageSY($im);
		if($width>$Wmax) {
			$dw=$Wmax;
			$dh=floor($dw*$height/$width);
			$height=$dh;
			$width=$dw;
		}
		if($height>$Hmax) {
			$dh=$Hmax;
			$dw=floor($dh*$width/$height);
		}
		$width=imageSX($im);
		$height=imageSY($im);
		$gdinfo=gd_info();
		$gdver=$gdinfo['GD Version'];
		$s=strpos($gdver,"(");
		$gdver=substr($gdver,$s+1,1);
		if($gdver!=2 || $type=='gif') {
			$im2=imagecreate($dw, $dh);
			imageCopyResized($im2,$im,0,0,0,0,$dw,$dh,$width,$height);
		} else {
			$im2=imagecreatetruecolor($dw,$dh);
			imageCopyResampled($im2,$im,0,0,0,0,$dw,$dh,$width,$height);
		}
		$outfile = $tgt.'/'.$name.'.'.$type;
		if($type=='jpg') imageJpeg($im2, $outfile, 80);
		if($type=='gif' || $type=='png') imagepng($im2, $outfile);
		imageDestroy($im);
		imageDestroy($im2);
	} else {
		return false;
	}
}

function MakeThumb($src,$tgt,$Wmax,$Hmax,$type,$name) {
	if(file_exists($src)) {
		if(@$type=='jpg') $im=imageCreateFromJpeg($src);
		if(@$type=='gif') $im=imageCreateFromGif($src);
		if(@$type=='png') $im=imageCreateFromPng($src);
		$width=imageSX($im);
		$height=imageSY($im);
		if($width>$Wmax) {
			$dw=$Wmax;
			$dh=floor($dw*$height/$width);
			$height=$dh;
			$width=$dw;
 		}
        if($height>$Hmax) {
			$dh=$Hmax;
			$dw=floor($dh*$width/$height);
		}
		$width=imageSX($im);
		$height=imageSY($im);
		$gdinfo=gd_info();
		$gdver=$gdinfo['GD Version'];
		$s=strpos($gdver,"(");
		$gdver=substr($gdver,$s+1,1);
		$placement_y = round(($Hmax-$dh)/2);
		$placement_x = round(($Wmax-$dw)/2);
		if($gdver!=2 || $type=='gif') {
			$im2=imagecreate($Wmax, $Hmax);
			imageCopyResized($im2,$im,$placement_x,$placement_y,0,0,$dw,$dh,$width,$height);
		} else {
			$im2=imagecreatetruecolor($Wmax, $Hmax);
			imageCopyResampled($im2,$im,$placement_x,$placement_y,0,0,$dw,$dh,$width,$height);
		}
        $outfile = $tgt.'/'.$name.'.jpg';
        imageJpeg($im2, $outfile, 51);
        imageDestroy($im);
        imageDestroy($im2);
	} else {
		return false;
	}
}


function ShowPages($CurPage,$TotalPages,$ShowMax,$QueryStr) {
	$PrevList=floor(($CurPage-1)/$ShowMax)*$ShowMax;
	$NextList=$PrevList+$ShowMax+1;
	if($PrevList>=$ShowMax*2) echo '<a href="?'.$QueryStr.'&p=1" title="В самое начало">«</a> ';
	if($PrevList>0) echo '<a href="?'.$QueryStr.'&p='.$PrevList.'"  title="Предыдущие '.$ShowMax.' страниц">…</a> ';
	for($i=$PrevList+1;$i<=$PrevList+$ShowMax;$i++) if($i<=$TotalPages) {
		if($i==$CurPage) echo '<u>'.$i.'</u> ';
		else echo '<a href="?'.$QueryStr.'&p='.$i.'">'.$i.'</a> ';
	}
	if($NextList<=$TotalPages) echo '<a href="?'.$QueryStr.'&p='.$NextList.'"  title="Следующие '.$ShowMax.' страниц">…</a> ';
	if($CurPage<$TotalPages) echo '<a href="?'.$QueryStr.'&p='.$TotalPages.'" title="В самый конец">»</a>';
}

function ShowPages2($CurPage,$TotalPages,$ShowMax,$QueryStr,$prefix) {
	$PrevList=floor(($CurPage-1)/$ShowMax)*$ShowMax;
	$NextList=$PrevList+$ShowMax+1;
	if($PrevList>=$ShowMax*2) echo '<a href="?'.$QueryStr.'&p'.$prefix.'=1" title="В самое начало">«</a> ';
	if($PrevList>0) echo '<a href="?'.$QueryStr.'&p'.$prefix.'='.$PrevList.'"  title="Предыдущие '.$ShowMax.' страниц">…</a> ';
	for($i=$PrevList+1; $i<=$PrevList+$ShowMax; $i++) {
		if($i<=$TotalPages) {
			if($i==$CurPage) echo '<u>'.$i.'</u> ';
			else echo '<a href="?'.$QueryStr.'&p'.$prefix.'='.$i.'">'.$i.'</a> ';
		}
	}
	if($NextList<=$TotalPages) echo '<a href="?'.$QueryStr.'&p'.$prefix.'='.$NextList.'"  title="Следующие '.$ShowMax.' страниц">…</a> ';
	if($CurPage<$TotalPages) echo '<a href="?'.$QueryStr.'&p'.$prefix.'='.$TotalPages.'" title="В самый конец">»</a>';
}

function tz_users_update($userinfo) {
	if($userinfo['clan'] != '') {
		$sSQL = 'SELECT `id` FROM `tzpolice_tz_clans` WHERE `name` = \''.$userinfo['clan'].'\'';
		$result = mysql_query($sSQL);
		if(mysql_num_rows($result)>0) {
			$d = mysql_fetch_assoc($result);
			$userclan = $d['id'];
		} else {
			$sSQL = 'INSERT INTO `tzpolice_tz_clans` SET `name` = \''.$userinfo['clan'].'\'';
			mysql_query($sSQL);
			$userclan = mysql_insert_id();
		}
	} else {
		$userclan='0';
	}
	$SQL = 'SELECT `id` FROM `tzpolice_tz_users` WHERE `name`=\''.$userinfo['login'].'\'';
	$r = mysql_query($SQL);
	$set = '`pro` = \''.$userinfo['pro'].'\', `clan_id` = \''.$userclan.'\', `level` = \''.$userinfo['level'].'\', `sex` = \''.$userinfo['man'].'\', `upd_time` = \''.time().'\'';
	if(mysql_num_rows($r)>0) {
		$query = 'UPDATE `tzpolice_tz_users` SET '.$set.' WHERE `name`=\''.$userinfo['login'].'\';';
	} else {
		$set .= ', `name` = \''.$userinfo['login'].'\'';
		$query = 'INSERT INTO `tzpolice_tz_users` SET '.$set;
	}
	mysql_query($query);
}


?>