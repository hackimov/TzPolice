<script>
function ClBrd2(text){
    while(text.indexOf('<BR>')>=0) text = text.replace('<BR>','\015\012');
    if (window.clipboardData){window.clipboardData.setData("Text", text);alert ("������ ��������� � ����� ������.");}
	else
		{
			var DummyVariable = prompt('����� ������ ����������, ��������� ������ =(',text);
		}
}
</script>
<?
/****************************************************
*	 Lebedev Sergey (fantastish@rambler.ru)			*
****************************************************/
//error_reporting(E_ALL);

///////////////////////////////////////////////////////////////////////////////////
// ���������� � ������������ �������� ���� � ������
///////////////////////////////////////////////////////////////////////////////////
// �������� �������� �������
	$menu = 'rating_arh';
	$path_to_php = '/_modules';

//  ?act=fees
//  case 'fees': include "_modules/rating_arh/fees_public.php"; break;

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
		$HTTP_REFERER = "";
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
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/fees_function.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/other.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/tz_plugins.php');

//	include_once ($DOCUMENT_ROOT.$path_to_php."/functions.php");

///////////////////////////////////////////////////////////////////////////////////
// ��������� ����� �������
///////////////////////////////////////////////////////////////////////////////////
//
// !!!!!!!!!!!!!���������� ��� ����� �� ����� ���� � ������!!!!!!!
//
/*	if($action == "db_install"){
		include_once ($DOCUMENT_ROOT.$path_to_php."/".$menu."/install_db.php");
	}
*/

// ����� ������ � ������� - ���� �����
// ������ ������ - ���� ���, �����, �����, ������.


$fees_cops = array();
if (abs(AccessLevel) & AccessFeesAdmin) {
	$fees_cops[] = AuthUserName;
}

	if(!isset($action)) $action='view3';
// ������ ������ ���� ������� � ������������� )))
	if(AuthUserClan=='Tribunal' || AuthUserClan=='police' || AuthUserGroup == 100) {
		$full_access = 1;
		if(in_array(AuthUserName, $fees_cops)){
			$full_access = 2;
		}
	}else
		$full_access = 0;

///////////////////////////////////////////////////////////////////////////////////

	if($action=='test'){

		if($full_access==2){
		//	$nick = "FANTASTISH";
		//	$message = "test \"test\" ''";

		//	$text = fees_send_telegramm($nick, $message);

		/*	if (file_exists("/home/sites/police/bot_fees/alerts.txt")){
			//	echo "<BR>\n���<BR>\n";
			echo	$contents = file_get_contents ("/home/sites/police/bot_fees/alerts.txt");

			}
		*/
		//	fees_log_parse2 ($connection, $db);
		//	echo date("d:m:Y H:i", "1157585161");
		}

	}elseif($action=='user_insert'){
		if($full_access>0){
			$text = fees_user_insert ($connection, $db, $ok, $x_value);
		}

// �� �������
	}elseif($action=='send2prison'){
		if($full_access==2){
			$text = fees_send2prison ($connection, $db, $ok, $id);
		}

// � ����
	}elseif($action=='send2block'){
		if($full_access==2){
			$text = fees_send2block ($connection, $db, $ok, $id);
		}

	}elseif($action=='user_update'){
		if($full_access==2){
			$text = fees_user_update ($id, $connection, $db, $ok, $x_value);
		}

	}elseif($action=='view' || $action=='view2' || $action=='view3' || $action=='view4' || $action=='view5'){
		if($full_access>0){
			$text = fees_users_view ($action, $connection, $db, $page, $full_access);
		}

	}elseif($action=='search'){
		if($full_access>0){
			$text = fees_search ($connection, $db, $id, $cid, $page, $ok, $name);
		}

	}elseif($action=="delete"){
		if($full_access==2){
			$text = fees_delete ($connection, $db, $id);
		}

	}elseif($action=="payed"){
		if($full_access==2){
			$text = fees_pay ($connection, $db, $id);
		}

	}else{

	}

	if($full_access>0){
		$sSQL = 'SELECT COUNT(id) FROM `'.$db['fees'].'` WHERE `time`>'.(time()-604800).' AND `payed`<`summa` AND `prison`=\'0\'';
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_array($result);
		$nrows = $row[0];

		$sSQL = 'SELECT COUNT(id) FROM `'.$db['fees'].'` WHERE `payed`=`summa` AND `prison`=\'0\'';
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_array($result);
		$nrows2 = $row[0];

		$sSQL = 'SELECT COUNT(id) FROM `'.$db['fees'].'` WHERE `time`<'.(time()-604800).' AND `payed`<`summa` AND `prison`=\'0\'';
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_array($result);
		$nrows3 = $row[0];

		$sSQL = 'SELECT COUNT(id) FROM `'.$db['fees'].'` WHERE `prison`=\'1\'';
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_array($result);
		$nrows4 = $row[0];

		$sSQL = 'SELECT COUNT(id) FROM `'.$db['fees'].'` WHERE `prison`=\'2\'';
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_array($result);
		$nrows5 = $row[0];

		$menu = "<SMALL>\n";

		$menu .= "<A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=fees&action=user_insert\">����������</A>\n";
		$menu .= " | <A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=fees&action=search\">����� �� ����</A>\n";
		$menu .= ' | <A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=fees&action=view">��������� ('.$nrows.")</A>\n";
		$menu .= ' | <A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=fees&action=view2">���������� ('.$nrows2.")</A>\n";
		$menu .= ' | <A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=fees&action=view3">� �������� ('.$nrows3.")</A>\n";
		$menu .= ' | <A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=fees&action=view4">������ �� ������� ('.$nrows4.")</A>\n";
		$menu .= ' | <A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=fees&action=view5">������ � ���� ('.$nrows5.")</A>\n";
		$menu .= "<HR>\n";

		$menu .= "</SMALL>\n";

		$text = $menu.$text;
	}


///////////////////////////////////////////////////////////////////////////////////
// ��������� ���������� �����
	$text = tz_tag_remake($text);
	$text = change_pre_value($SERVER_NAME, $PHP_SELF, $path_to_images, $text);

// �����
	echo $text;

?>