<?php
	error_reporting(0);
	require ('/home/sites/police/dbconn/dbconn.php');

	if(!mysql_query('SELECT id FROM `site_users` LIMIT 1')) {
		echo mysql_error();
		exit();
	}

	// Check if user's IP is in blacklist
	$result = mysql_query('SELECT * FROM `site_blacklist` WHERE `ip`=\''.$REMOTE_ADDR.'\'');
	if (mysql_num_rows($result) > 0 ) {
		$row = mysql_fetch_assoc($result);
		echo '<br><br><blockquote style="font-family:verdana;font-size:11px"><b>Ќет доступа к сайту</b><br>
        јдминистраци€ закрыла вам доступ к сайту. <br>
    	ѕричина: "'.$row['reason'].'"</blockquote>';
		exit;
	}

	//session_start();

	if (isset($_COOKIE['CUser'])) $auth_cookie_user = str_replace("'",';',$_COOKIE['CUser']);
	if (isset($_COOKIE['CPass'])) $auth_cookie_hash = str_replace("'",';',$_COOKIE['CPass']); // на самом деле это нихрена не пасс, но дл€ дезинформации пусть останетс€ CPass


 	// ==== провер€ем совпадение последнего IP ========================
 	
	if (isset($auth_cookie_user) && isset($auth_cookie_hash)) {
		
		$SQL="SELECT su.id, su.ips FROM site_users AS su INNER JOIN site_users_auth AS sua ON(su.id = sua.user_id) WHERE su.user_name='".$auth_cookie_user."' AND sua.user_hash='".$auth_cookie_hash."' LIMIT 1";

		$r = mysql_query($SQL);
		$nums_r = mysql_num_rows($r);
		if ($nums_r > 0) {
			
			$data = mysql_fetch_assoc($r);

			$ips = explode(', ', $data['ips'], 2);
			if ($ips[0] != $_SERVER['REMOTE_ADDR']) {
				$_REQUEST['logoff'] = 1;
				
			}
		}
	}

	// ================================================================
	
	$NewAuth=0;

	if (@$_REQUEST['AuthUser'] && @$_REQUEST['AuthPass']) {
		$AuthUser = str_replace("'", ';', strtolower($_REQUEST['AuthUser']));
		$AuthPass = passhash($_REQUEST['AuthPass']);
		$NewAuth=1;
	}

	// LOGIN
	if ($NewAuth==1 && $_REQUEST['logoff']!=1) {
		
		// обновл€ем IP до установки куков.
		$SQL='SELECT id FROM site_users WHERE `user_name`="'.$AuthUser.'" AND user_pass="'.$AuthPass.'" LIMIT 1';
		$r = mysql_query($SQL);

		if (mysql_num_rows($r) > 0) {

			$data = mysql_fetch_assoc($r);
			UpdateUser($data['id']);

			// ======== генерируем хеш, пишем в таблицу и в куки. =======
			$AuthHash = passhash((int)$data['id'] * time());
			$SQL = "INSERT INTO `site_users_auth` (`user_id`, `user_hash`) VALUES (".$data['id'].", '$AuthHash') ON DUPLICATE KEY UPDATE `user_hash` = '$AuthHash'";
			mysql_query($SQL);
			// ==========================================================
			
			unset($data);
		}

		SetCookie('CUser', $AuthUser, time()+2592000);
		SetCookie('CPass', $AuthHash, time()+2592000);
		@header('Location: ?act='.$_REQUEST['act']);

	}

	
	// LOGOUT
	if(@$_REQUEST['logoff']==1) {
		SetCookie('CUser','');
		SetCookie('CPass','');
		$NewAuth=0;
		define('AuthStatus', 3);
		@header('Location: ?act='.$_REQUEST['act']);
	}

	$AuthType = 'none';

	if (isset($auth_cookie_user) && isset($auth_cookie_hash)) $AuthType='cookie';
	
	if($AuthType!='none') {
		if($AuthType=='cookie') {
			$SQL='SELECT a.id, a.user_name, a.user_group, a.ips,
			a.avatar, a.restricted_area, a.AccessLevel, b.clan, b.lvl, b.pro, b.pvprank
			FROM site_users AS a LEFT JOIN locator AS b ON(a.user_name=b.login) 
			INNER JOIN site_users_auth AS c ON(a.id=c.user_id)
			WHERE `user_name`="'.$auth_cookie_user.'" AND `user_hash`="'.$auth_cookie_hash.'" LIMIT 1';
			$r = mysql_query($SQL);
			$nums_r = mysql_num_rows($r);
			if($nums_r<1 && $NewAuth==1) define('AuthStatus',2);
			if($nums_r<1 && $NewAuth==0) define('AuthStatus',0);
			if($nums_r>0) define('AuthStatus',1);
			if(AuthStatus==1 && @$_REQUEST['logoff']!=1) {
				$data = mysql_fetch_assoc($r);
				define('AuthUserName', $data['user_name']);
				define('AuthUserGroup', $data['user_group']);
				define('AuthUserClan', $data['clan']);
				define('AuthUserAvatarClan', $data['avatar']);
				define('AuthUserId', $data['id']);
				define('AuthUserRestrAccess', $data['restricted_area']);
				define('AccessLevel', abs($data['AccessLevel']));
				unset($data);
			}
		}
	}

	$SQL = 'SELECT `AccessName`, `AccessLevel` FROM `AccessLevels`';
	$r = mysql_query($SQL);
	while (list($AccessName, $AccessLevel) = mysql_fetch_array($r)) {
		define($AccessName, abs($AccessLevel));
	}

	////////////////////
	if(!defined('AuthStatus')) define('AuthStatus',0);
	if(!defined('AuthUserName')) define('AuthUserName', '');
	if(!defined('AuthUserGroup')) define('AuthUserGroup', '');
	if(!defined('AuthUserClan')) define('AuthUserClan', '');
	if(!defined('AuthUserAvatarClan')) define('AuthUserAvatarClan', '');
	if(!defined('AuthUserId')) define('AuthUserId', '');
	if(!defined('AuthUserRestrAccess')) define('AuthUserRestrAccess', '');
	if(!defined('AccessLevel')) define('AccessLevel', 0);
	////////////////////

?>