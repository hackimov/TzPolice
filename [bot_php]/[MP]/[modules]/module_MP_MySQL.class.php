<?php
/*
$link = $bot->mysql_links[порядковый_номер];
*/
//2
function in_grey_list(&$bot, $nick)
	{		return(0);
	}
//3
function add_battle(&$bot, $bid, $from)
	{		$name_id = GetTzUserIdByName(&$bot, $from);		$sSQL = 'INSERT INTO `mp_informer` SET `time`='.time().', `user_id` = '.$name_id.', `battle_id`='.mysql_escape_string(trim($bid)).'';		if(mysql_query($sSQL, $bot->mysql_links['db2']))			return 1;
		else
			return 0;
			$fp = fopen ('home/sites/police/bot_php/MP/logs/error_log.txt', 'a+');
			fwrite ($fp, date('d.m.y H:i:m ').mysql_errno().":".mysql_error()."\n");
			fclose ($fp);
	}
//4
function confirm_MP(&$bot, $from, $bid, $MP)
	{		$name_id = GetTzUserIdByName(&$bot, $from);		$mp_id = GetTzUserIdByName(&$bot, $MP);
		$sSQL = 'UPDATE `mp_informer` SET `mp_id`='.$mp_id.' WHERE `user_id` = '.$name_id.' AND `battle_id`='.mysql_escape_string(trim($bid)).'';
		if(mysql_query($sSQL, $bot->mysql_links['db2']))
			return 1;
		else
		{
			return 0;
			$fp = fopen ('home/sites/police/bot_php/MP/logs/error_log.txt', 'a+');
			fwrite ($fp, date('d.m.y H:i:m ').mysql_errno().":".mysql_error()."\n");
			fclose ($fp);
		}
	}
//5
function battle_result(&$bot, $bid, $MP)
	{			$mp_id = GetTzUserIdByName(&$bot, $MP);		$sSQL = 'SELECT `mp_id` FROM `mp_informer` WHERE `battle_id`='.mysql_escape_string(trim($bid)).'';		$result = mysql_query($sSQL, $bot->mysql_links['db2']);		if(mysql_num_rows($result)<1){			return 0;			$fp = fopen ('home/sites/police/bot_php/MP/logs/error_log.txt', 'a+');			fwrite ($fp, date('d.m.y H:i:m ')." ".$bid.":".$MP." = ".$sSQL."\n");			fclose ($fp);		} else {			$row = mysql_fetch_assoc($result);			if($mp_id == $row['mp_id']){				$sSQL = "UPDATE `mp_informer` SET status=1 WHERE `mp_id` = ".$mp_id." AND `battle_id`=".mysql_escape_string(trim($bid))."";				if(mysql_query($sSQL, $bot->mysql_links['db2']))					return 1;				else{					return 0;					$fp = fopen ('home/sites/police/bot_php/MP/logs/error_log.txt', 'a+');					fwrite ($fp, date('d.m.y H:i:m ').mysql_errno().":".mysql_error()." = ".$sSQL."\n");					fclose ($fp);				}			} else {				return 0;			}
		}
	}
	
function battle_result_deny(&$bot, $reg, $MP)
	{	
		$user = trim($reg[1]);
		$comment = trim($reg[2]);
		
		$mp_id = GetTzUserIdByName(&$bot, $MP);
		$user_id = GetTzUserIdByName(&$bot, $user);
		
		$sSQL = 'SELECT `id` FROM `mp_informer` WHERE `status`=0 AND `mp_id`='.$mp_id.' AND `user_id` = '.$user_id.' ORDER BY `time` DESC LIMIT 1';
		$result = mysql_query($sSQL, $bot->mysql_links['db2']);
		if(mysql_num_rows($result)<1){
			return 0;
		} else {
			$row = mysql_fetch_assoc($result);
			$sSQL = 'UPDATE `mp_informer` SET `status`=2, `comment`="'.mysql_escape_string($comment).'" WHERE `id` = '.$row['id'];
			if(mysql_query($sSQL, $bot->mysql_links['db2'])){
				return $user;
			}else{
				return 0;
				$fp = fopen ('home/sites/police/bot_php/MP/logs/error_log.txt', 'a+');
				fwrite ($fp, date('d.m.y H:i:m ').mysql_errno().":".mysql_error()."\n");
				fclose ($fp);
			}
		}
	}
	
function GetTzUserIdByName(&$bot, $name)
	{	
		$name = trim($name);
		$sSQL = 'SELECT `id` FROM `tzpolice_tz_users` WHERE `name`="'.mysql_escape_string($name).'"';
		$result = mysql_query($sSQL, $bot->mysql_links['db1']);
		if(mysql_num_rows($result)<1){
		// если нет
			$sSQL = 'INSERT INTO `tzpolice_tz_users` SET `name`="'.mysql_escape_string($name).'"';
			mysql_query($sSQL, $bot->mysql_links['db1']);
			$name_id = mysql_insert_id($bot->mysql_links['db1']);
		// если есть
		} else {
			$row = mysql_fetch_assoc($result);
			$name_id = $row['id'];
		}
		return $name_id;
	}
	
function GetTzUserNameById(&$bot, $id)
	{	
		$name = trim(intval($id));
		$sSQL = 'SELECT `name` FROM `tzpolice_tz_users` WHERE `id`='.$id;
		$result = mysql_query($sSQL, $bot->mysql_links['db1']);
		if(mysql_num_rows($result)<1){
			$name = 'Anonymка';
		} else {
			$row = mysql_fetch_assoc($result);
			$name = stripslashes($row['name']);
		}
		return $name;
	}
?>