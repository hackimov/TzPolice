<?
////////////////////////////////////////////////////////

//UserInfo functions
	function fant_TZConn($login, $enforce=0) {
//+++ AVE 16/06/08 - inserted cache
		$temp_deltime = time() - 3600;
		$usecache=1;
		$query = 'SELECT * FROM `data_cache` WHERE (`user_name`="'.addslashes($login).'" AND `lastupdate`>'.$temp_deltime.') LIMIT 1;';
		$result = mysql_query($query);
//  $old_res = mysql_fetch_assoc($result);
		if (mysql_num_rows($result) > 0 && $usecache == 1) {
			$row = mysql_fetch_assoc($result);
			$tmp_page = $row['data_str'];
			return $tmp_page;
		} else {//cache
//+++ AVE 16/06/08 - end
		$sock = @fsockopen('www.timezero.ru', 80, $er1, $er2, 5);
		if(@$sock) {
		//	echo ' GET /cgi-bin/info.pl?'.trim(urlencode($login)).' HTTP/1.0 ';
//			fputs($sock, 'GET /cgi-bin/info.pl?'.trim(urlencode($login))." HTTP/1.0\r\n");
			fputs($sock, 'GET /info.pl?userxml='.rawurlencode(trim(mb_strtolower($login)))." HTTP/1.0\r\n");
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
		//	print_r($tmp_body);
			if(strpos($tmp_body,'502 Bad Gateway')===false){
				if(strpos($tmp_body,'Internal Server Error')===false){
					
					if(strpos($tmp_body,'&lt;TIMEOUT /&gt;')===false){
						
						if (strpos($tmp_body,'&lt;ERRLOGIN /&gt;')===false){
							
							if (strpos($tmp_body,'&lt;NOUSER /&gt')===false){
								if (strpos($tmp_body, '&lt;USER')===false) {
									$funcerror['error'] = 'SERVER_ERROR';
									return $funcerror;
								}else{
									return $tmp_body;
								}
								
							}else{
								
								$funcerror['error'] = 'USER_NOT_FOUND';
								return $funcerror;
								
							}
						}else{
							
							$funcerror['error'] = 'ERROR_IN_USER_NAME';
							return $funcerror;
							
						}
					}else{
						$funcerror['error'] = 'TIMEOUT';
						return $funcerror;
					}
				} else {
					if($enforce==0) {
						$funcerror['error'] = 'TIMEOUT';
						return $funcerror;
					} else {
						sleep(3);
						return fant_TZConn($login,1);
					}
				}
			} else {
				sleep(3);
				return fant_TZConn($login,0);
			}
		} else {
			$funcerror['error'] = 'NOT_CONNECTED';
			return $funcerror;
		}
		}//cache
	}

	function fant_ParseUserInfo($tmp_page) {

		$userinfo['login']=sub($tmp_page, 'login=&quot;', '&quot;');
		$userinfo['online']=sub($tmp_page, 'online=&quot;', '&quot;');
		$userinfo['level']=sub($tmp_page, 'level=&quot;', '&quot;');
		$userinfo['man']=sub($tmp_page, 'man=&quot;', '&quot;');
	//User Stats
//		$userinfo['str']=sub($tmp_page, 'str=&quot;', '&quot;');
//		$userinfo['dex']=sub($tmp_page, 'dex=&quot;', '&quot;');
//		$userinfo['int']=sub($tmp_page, 'int=&quot;', '&quot;');
//		$userinfo['pow']=sub($tmp_page, 'pow=&quot;', '&quot;');
//		$userinfo['acc']=sub($tmp_page, 'acc=&quot;', '&quot;');
//		$userinfo["intel"]=sub($tmp_page, "intel=&quot;", "&quot;");
	//HP & psy
//		$userinfo["HP"]=sub($tmp_page, "HP=&quot;", "&quot;");
//		$userinfo["psy"]=sub($tmp_page, "psy=&quot;", "&quot;");
//		$userinfo["maxHP"]=sub($tmp_page, "maxHP=&quot;", "&quot;");
//		$userinfo["maxPsy"]=sub($tmp_page, "maxPsy=&quot;", "&quot;");
	//User about
//		$userinfo["name"]=sub($tmp_page, "name=&quot;", "&quot;");
//		$userinfo["city"]=sub($tmp_page, "city=&quot;", "&quot;");
//		$userinfo["about"]=sub($tmp_page, "about=&quot;", "&quot;");
		$userinfo['dismiss']=sub($tmp_page, 'dismiss=&quot;', '&quot;');
		if($userinfo['dismiss']=='0') $userinfo['dismiss'] = '';
		$userinfo['clan']=sub($tmp_page, 'clan=&quot;', '&quot;');
//		$userinfo["regday"]=sub($tmp_page, "regday=&quot;", "&quot;");
//		$userinfo["img"]=sub($tmp_page, "img=&quot;", "&quot;");
//		$userinfo["brokenslots"]=sub($tmp_page, "brokenslots=&quot;", "&quot;");
//		$userinfo["ne"]=sub($tmp_page, "ne=&quot;", "&quot;");
		$userinfo['pro']=sub($tmp_page, 'pro=&quot;', '&quot;');
//		$userinfo["propwr"]=sub($tmp_page, "propwr=&quot;", "&quot;");
	// Отдел и Должность
//		$userinfo["s1"]=sub($tmp_page, "s1=&quot;", "&quot;");
//		$userinfo["s2"]=sub($tmp_page, "s2=&quot;", "&quot;");

/*		if ($userinfo["man"] == 1) {
			$userinfo["gen"] = 1;
		} else {
			$userinfo["gen"] = 2;
		}
*/		

//		$userinfo["citizenship"]=sub($tmp_page, "citizenship=&quot;", "&quot;");
//		$userinfo["medals"]=sub($tmp_page, "medals=&quot;", "&quot;");
//		$userinfo["siluet"]=sub($tmp_page, "siluet=&quot;", "&quot;");
//		$userinfo["stamina"]=sub($tmp_page, "stamina=&quot;", "&quot;");
//		$userinfo["rank_points"]=sub($tmp_page, "rank_points=&quot;", "&quot;");
//		$userinfo["forum_points"]=sub($tmp_page, "forum_points=&quot;", "&quot;");
		
		return $userinfo;
	}
	
	function fant_tz_users_update($userinfo){
		
		if($userinfo['clan']!=''){
			$sSQL = 'SELECT `id` FROM `tzpolice_tz_clans` WHERE `name` = "'.mysql_escape_string($userinfo['clan']).'"';
			$result = mysql_query($sSQL);
			if(mysql_num_rows($result)>0){
				$d=mysql_fetch_assoc($result);
				$userinfo['clan'] = $d['id'];
			}else{
				$sSQL = 'INSERT INTO `tzpolice_tz_clans` SET `name` = "'.mysql_escape_string($userinfo['clan']).'"';
				mysql_query($sSQL);
				$userinfo['clan'] = mysql_insert_id();
			}
		}else{
			$userinfo['clan']='0';
		}
		
		if($userinfo['login']!='0' && $userinfo['login']!=''){
			$SQL = 'SELECT `id` FROM `tzpolice_tz_users` WHERE `name`="'.mysql_escape_string($userinfo['login']).'"';
			$r = mysql_query($SQL);
			$set = '`pro` = '.$userinfo['pro'].', `clan_id` = '.$userinfo['clan'].', `level` = '.$userinfo['level'].', `sex` = '.$userinfo['man'].', `block` = "'.mysql_escape_string($userinfo['dismiss']).'", `upd_time` = '.time().'';
			if(mysql_num_rows($r)>0){
				$query = 'UPDATE `tzpolice_tz_users` SET '.$set.' WHERE `name`="'.mysql_escape_string($userinfo['login']).'";';
			}else{
				$set .= ', `name` = "'.mysql_escape_string($userinfo['login']).'"';
				$query = 'INSERT INTO `tzpolice_tz_users` SET '.$set;
			}
			mysql_query($query);
		}
	}

	
////////////////////////////////////////////////////////
	
	function fant_GetBattle($id) {
		$sock = fsockopen('city1.timezero.ru', 80, $errno, $errstr, 5);
		if ($sock) {
			fputs($sock, 'GET /getbattle?id='.$id." HTTP/1.0\r\n");
			fputs($sock, "Host: city1.timezero.ru\r\n");
			fputs($sock, "Content-type: application/x-www-url-encoded \r\nn");
			fputs($sock, "Connection: Keep-Alive\r\n");
			fputs($sock, "\r\n\r\n");
			$tmp_headers = '';
			$tmp_body = '';
			while (!feof($sock)) {
				$tmp_body .= fgets($sock, 4096);
			}
			return $tmp_body;
		} else {
			return 0;
		}
	}
	
//=============================================
// Обработка тз-тэгов
//----------------------------------------
	function tz_tag_remake($text){
		
		$text = preg_replace_callback("/(\{_PERS_\})(.*?)(\{_\/PERS_\})/i", 'tz_get_pers', $text);
		$text = preg_replace_callback("/(\{_BATTLE_\})(.*?)(\{_\/BATTLE_\})/i", 'tz_get_battle', $text);
		$text = preg_replace_callback("/(\{_CLAN_\})(.*?)(\{_\/CLAN_\})/i", 'tz_get_clan', $text);
		$text = preg_replace_callback("/(\{_PROF_\})(.*?)(\{_\/PROF_\})/i", 'tz_get_prof', $text);
		
		return $text;
		
	}

	function tz_get_pers($matches){
		
		$matches[2] = strip_tags($matches[2]);
		
		$sSQL3 = 'SELECT `clan_id`, `pro`, `name`, `sex`, `level` FROM `tzpolice_tz_users` WHERE `name`=\''.$matches[2].'\'';
		if(defined('MYSQL_DB_CONNECTION')) $result3 = mysql_query($sSQL3, MYSQL_DB_CONNECTION);
		else $result3 = mysql_query($sSQL3);
	// если в базе персов ТЗ не находим ник
		if(mysql_num_rows($result3)<1){
			$ret .= $matches[2];
		}else{
			$row3 = mysql_fetch_assoc($result3);
			
			if($row3['clan_id']>0){
				$sSQL2 = 'SELECT `name` FROM `tzpolice_tz_clans` WHERE `id`=\''.$row3['clan_id'].'\'';
				if(defined('MYSQL_DB_CONNECTION')) $result2 = mysql_query($sSQL2, MYSQL_DB_CONNECTION);
				else $result2 = mysql_query($sSQL2);
				$row2 = mysql_fetch_assoc($result2);
				
				$clan = '{_CLAN_}'.trim($row2['name']).'{_/CLAN_}';
			//	echo "<BR>\n";
			}else{
				$clan = '';
			}
		//	echo "| ".$clan." ".$name." ".$level." | ".$prof." |";
				
			if(empty($row3['pro'])){
				$row3['pro']='0';
			}
			$ret .= $clan.' '.stripslashes($row3['name']).' ['.$row3['level'].'] {_PROF_}'.$row3['pro'].(($row3['sex']=='0')?'w':'').'{_/PROF_}';
			
		}
		
		return $ret;
	}
	
	function tz_get_battle($matches){
		$matches[2] = strip_tags($matches[2]);
		$ret = '<A HREF="http://www.timezero.ru/sbtl.ru.html?'.stripslashes($matches[2]).'" TARGET="_blank">'.stripslashes($matches[2])."</A>\n";
			
		return $ret;
	}
	
	function tz_get_clan($matches){
		global $DOCUMENT_ROOT;
		$matches[2] = strip_tags($matches[2]);
		if($matches[2]!='' && $matches[2]!='0'){
			if(file_exists($DOCUMENT_ROOT.'/_imgs/clans/'.$matches[2].'.gif')){
				$size = getimagesize($DOCUMENT_ROOT.'/_imgs/clans/'.$matches[2].'.gif');
				
				if($size !== false && $size[0]>1){
					$ret = '<img src="http://www.tzpolice.ru/_imgs/clans/'.$matches[2].'.gif" border=0 ALT="'.$matches[2].'" ALIGN="absBottom" WIDTH="28" HEIGHT="16">';
				}else{
					chmod($DOCUMENT_ROOT.'/_imgs/clans/'.$matches[2].'.gif', 0777);
					
					if(getimagesize ('http://game.timezero.ru/i/clans/'.str_replace(' ','%20', $matches[2]).'.gif')) {
						copy ('http://game.timezero.ru/i/clans/'.str_replace(' ','%20', $matches[2]).'.gif', $DOCUMENT_ROOT.'/_imgs/clans/'.$matches[2].'.gif');
						chmod($DOCUMENT_ROOT.'/_imgs/clans/'.$matches[2].'.gif', 0777);
					}
					$ret = '<img src="http://www.timezero.ru/i/clans/'.$matches[2].'.gif" border=0 ALT="'.$matches[2].'" ALIGN="absBottom" WIDTH="28" HEIGHT="16">';
				}
			} else {
				if(getimagesize ('http://game.timezero.ru/i/clans/'.str_replace(' ','%20', $matches[2]).'.gif')) {
					copy ('http://game.timezero.ru/i/clans/'.str_replace(' ','%20', $matches[2]).'.gif', $DOCUMENT_ROOT.'/_imgs/clans/'.$matches[2].'.gif');
					chmod($DOCUMENT_ROOT.'/_imgs/clans/'.$matches[2].'.gif', 0777);
				}
				$ret = '<img src="http://www.timezero.ru/i/clans/'.$matches[2].'.gif" border=0 ALT="'.$matches[2].'" ALIGN="absBottom" WIDTH="28" HEIGHT="16">';
			}
		}else{
			$ret='';
		}
		
		return $ret;
	}
	
	function tz_get_prof($matches){
		global $prof_alt;
		$matches[2]=strip_tags($matches[2]);
		$ret = '<img src="http://www.tzpolice.ru/_imgs/pro/i'.$matches[2].'.gif" border=0 ALT="'.$prof_alt[intval($matches[2])].'" ALIGN="absBottom">';
			
		return $ret;
	}
//=============================================

	function GetPersParamsByName($name, $params='`id`') {
		// `clan_id`, `pro`, `level`, `name`, `sex`
		$sSQL = 'SELECT '.$params.' FROM `tzpolice_tz_users` WHERE `name`=\''.mysql_escape_string($name).'\'';
		$result = mysql_query($sSQL, MYSQL_DB_CONNECTION);
	// если в базе персов ТЗ не находим ник
		if(mysql_num_rows($result)<1){
		// Добавляем ник в бд и получаем его id,
			$sSQLx = 'INSERT INTO `tzpolice_tz_users` SET `name`=\''.mysql_escape_string($name).'\', pro=\'0\'';
			mysql_query($sSQLx, MYSQL_DB_CONNECTION);
			$sSQL3 = 'SELECT '.$params.' FROM `tzpolice_tz_users` WHERE `id`=\''.mysql_insert_id(MYSQL_DB_CONNECTION).'\'';
			$result = mysql_query($sSQL3, MYSQL_DB_CONNECTION);
		}
		$row = mysql_fetch_assoc($result);
		
		return $row;
	}
	
	function GetPersParamsById($id, $params='`name`') {
		// `clan_id`, `pro`, `level`, `name`, `sex`
		$sSQL = 'SELECT '.$params.' FROM `tzpolice_tz_users` WHERE `id`=\''.mysql_escape_string($id).'\'';
		$result = mysql_query($sSQL, MYSQL_DB_CONNECTION);
	// если в базе персов ТЗ не находим ник
		$row = mysql_fetch_assoc($result);
		
		return $row;
	}
//========================
	
?>