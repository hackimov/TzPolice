<?
/****************************************************
*	 Lebedev Sergey (fantastish@rambler.ru)			*
****************************************************/
///////////////////////////////////////////////////////////////////////////////////
// ���������� � ������������ �������� ���� � ������
///////////////////////////////////////////////////////////////////////////////////
// �������� �������� �������
	$menu = 'rating_arh';
	$path_to_php = '/_modules';

//  ?act=pa_stats
//  case 'pa_stats': include "_modules/rating_arh/pa_stats.php"; break;	

///////////////////////////////////////////////////////////////////////////////////
// ���������� ������ ���� � ROOT'� ������� [�������� ��������� "/" �� ����� ������]:

// /home/sites/police/www
	$DOCUMENT_ROOT = ereg_replace ("/$", '', $HTTP_SERVER_VARS['DOCUMENT_ROOT']);
// ���������� ��� ���������� ��� ������:
	$SERVER_NAME = $HTTP_SERVER_VARS['SERVER_NAME'];
// ���������� ��� ����� �� �������� ���� ������:
	$PHP_SELF = basename (@$HTTP_SERVER_VARS['PHP_SELF']);
// ���������� ������ ������� URL:
	$REQUEST_URI = $HTTP_SERVER_VARS['REQUEST_URI'];
// ��������� ������� ������ ������� (� ��������):
	$QUERY_STRING = $HTTP_SERVER_VARS['QUERY_STRING'];
// ��������� "������" ������ (referer):
	if (isset ($HTTP_SERVER_VARS['HTTP_REFERER'])) {
		$HTTP_REFERER = $HTTP_SERVER_VARS['HTTP_REFERER'];
	} else {
		$HTTP_REFERER = '';
	}
// ���������� HOST:
	if (isset ($HTTP_X_FORWARDED_FOR)) {
		$host = @gethostbyaddr ($HTTP_X_FORWARDED_FOR);
	} else {
		$host = @gethostbyaddr ($REMOTE_ADDR); 
	}

// ���������� IP:
	if(getenv('HTTP_X_FORWARDED_FOR') != '' ){
		$client_ip = (!empty($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ((!empty($HTTP_ENV_VARS['REMOTE_ADDR'])) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : $REMOTE_ADDR);
	
		$entries = explode(',', getenv('HTTP_X_FORWARDED_FOR'));
		reset($entries);
		while(list(, $entry) = each($entries)){
			$entry = trim($entry);
			if(preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list)){
				$private_ip = array('/^0\./', '/^127\.0\.0\.1/', '/^192\.168\..*/', '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/', '/^10\..*/', '/^224\..*/', '/^240\..*/');
				$found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
				if ($client_ip != $found_ip){
					$client_ip = $found_ip;
					break;
				}
			}
		}
	}else{
		$client_ip = (!empty($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ((!empty($HTTP_ENV_VARS['REMOTE_ADDR'])) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : $REMOTE_ADDR);
	}
	$ip = $client_ip;

// ���� ������ ����� ������ ��������� WWW
	$HTTP_HOST = $HTTP_SERVER_VARS['HTTP_HOST'];
//	if(!eregi("www.",$HTTP_HOST)) Header("Location: http://www.".$SERVER_NAME.$REQUEST_URI."");
//	if(!eregi("www.",$SERVER_NAME)) $SERVER_NAME="www.".$SERVER_NAME;
	
// ��������� ���������� ����������
	while (list ($key, $val) = each ($HTTP_GET_VARS)) $$key = $val;
	while (list ($key, $val) = each ($HTTP_POST_VARS)) $$key = $val;
	
//	require("/home/sites/police/dbconn/dbconn.php");

///////////////////////////////////////////////////////////////////////////////////
// ��������� ���������� � ����� ������ (���� �� �����������, ����� ������ �� �������, � ������ �������):
	//$connection = mysql_connect ($hostName, $userName, $password);
	$connection = $db;
// �������� ���� ������ (���� ������� �� ����������, �� ������ �������):
//	$database = mysql_select_db ($databaseName, $connection);

///////////////////////////////////////////////////////////////////////////////////
// ���������� ��� ��� ��������
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/config.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/pa_stats_function.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/other.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/tz_plugins.php');

//	include_once ($DOCUMENT_ROOT.$path_to_php."/functions.php");

///////////////////////////////////////////////////////////////////////////////////
// ��������� ����� �������
///////////////////////////////////////////////////////////////////////////////////
//
// !!!!!!!!!!!!!���������� ��� ����� �� ����� ���� � ������!!!!!!!
//
	if(!isset($action)) $action='view';
	
// ����������
	$sSQL = 'SELECT `chief` FROM `sd_cops` WHERE `user_id` = '.intval(AuthUserId).' LIMIT 1;';
	$result = mysql_query($sSQL, $connection);
	if(mysql_num_rows($result)>0){
		$row = mysql_fetch_assoc($result);
		$chief = $row['chief'];
	}else{
		$chief = '0';
	}
	
// ������ ������ ���� ������� � ������������� )))
//	if(AuthUserClan=='police' || AuthUserName=='deadbeef' || AuthUserName=='FANTASTISH'){
//	if(AuthUserName=='Bo Dun' || AuthUserName=='skAtinka' || AuthUserName=='Exposure passion' || AuthUserName=='Raist' || AuthUserName=='Natsha' || AuthUserName=='Palevo' || $chief=='1'){
	if ((abs(AccessLevel) & AccessPAStats) || $chief=='1') {
		$full_access=1;
	}else
		$full_access=0;

///////////////////////////////////////////////////////////////////////////////////

	if($action=='view'){
		if($full_access==1){
			$text = police_online_view ($months_a, $time, $connection, $db, $post_names);
		}
	
	}elseif($action=='view2'){
		if($full_access==1){
			$text = police_online_view2 ($login, $months_a, $time, $connection, $db, $post_names);
		}
	
	}else{
		
	}
		
	$menu = '';
	$text = $menu.$text;
		

///////////////////////////////////////////////////////////////////////////////////
// ��������� ���������� �����
	$text = tz_tag_remake($text);
	$text = change_pre_value($SERVER_NAME, $PHP_SELF, $path_to_images, $text);
	
// �����
	echo $text;

?>