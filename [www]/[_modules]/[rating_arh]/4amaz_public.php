<?
/****************************************************
*	 Lebedev Sergey (fantastish@rambler.ru)			*
****************************************************/
///////////////////////////////////////////////////////////////////////////////////
// ���������� � ������������ �������� ���� � ������
///////////////////////////////////////////////////////////////////////////////////
// �������� �������� �������
	$menu = "rating_arh";
	$path_to_php = "/_modules";

//  ?act=4amaz
//  case '4amaz': include "_modules/rating_arh/4amaz_public.php"; break;	

///////////////////////////////////////////////////////////////////////////////////
// ���������� ������ ���� � ROOT'� ������� [�������� ��������� "/" �� ����� ������]:

// /home/sites/police/www
	$DOCUMENT_ROOT = ereg_replace ("/$", "", $HTTP_SERVER_VARS['DOCUMENT_ROOT']);
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
	include_once ($DOCUMENT_ROOT.$path_to_php."/".$menu."/config.php");
	include_once ($DOCUMENT_ROOT.$path_to_php."/".$menu."/4amaz_function.php");
	include_once ($DOCUMENT_ROOT.$path_to_php."/".$menu."/other.php");
	include_once ($DOCUMENT_ROOT.$path_to_php."/".$menu."/tz_plugins.php");

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
	if(!isset($action)) $action="on_check";
// ������ ������ ���� ������� � ������������� )))
	if(AuthUserGroup == 100){
		$full_access=1;
	}else{
		$full_access=0;
		
			
		
		$sSQL = "SELECT `dept` FROM `sd_cops` WHERE `name` = '".AuthUserName."' LIMIT 1;";
		$result = mysql_query($sSQL, $connection);
		$row = mysql_fetch_array($result);
	//15 ����� ���������� ���������
	//12 ����� ��������
		if($row["dept"]=="15"){
			$full_access=2;
		}elseif($row["dept"]=="12"){
			$full_access=1;
		}else{
			$full_access=0;
		}
	}

///////////////////////////////////////////////////////////////////////////////////
	
	if($action=="add_to_check" || $action=="add_to_check2" || $action=="add_to_check3"){
		if($full_access==1 || $full_access==2){
		/*==========
			`status`='1' - ���������
			`status`='2' - ���� �� ��������
			`status`='3' - ���������
		===========*/
			if($action=="add_to_check"){
				$status = "1";
			}elseif($action=="add_to_check2"){
				$status = "2";
			}elseif($action=="add_to_check3"){
				$status = "3";
			}
			
			if($full_access==2) $status = "1";
			
			$x_value=explode("|",$x_value);
			
			$i=1;
			$ft=0;
			while($i<sizeof($x_value)){
				if(trim($x_value[$i])!=""){
					$sSQL = "SELECT COUNT(id) FROM `".$db["okp4oin"]."` WHERE `name_id`='".intval(trim($x_value[$i]))."'";
					$result = mysql_query($sSQL, $connection);
					$row = mysql_fetch_array($result);
					$nrows = $row[0];
				// ���� ����
					if($nrows<1){
						$sSQL = "INSERT INTO ".$db["okp4oin"]." SET `name_id`='".intval(trim($x_value[$i]))."', `uid`='".AuthUserId."', `time`='".time()."', `status`='".$status."'";
					}else{
						$sSQL = "UPDATE ".$db["okp4oin"]." SET `uid`='".AuthUserId."', `time`='".time()."', `status`='".$status."' WHERE `name_id`='".intval(trim($x_value[$i]))."'";
					}
					if(mysql_query($sSQL,$connection))
						$ft++;
				}
				$i++;
			}
			
			if($action=="add_to_check"){
				$text = title("���������� �� �������� - ".$ft." �����");
			}elseif($action=="add_to_check2"){
				$text = title("���������� � ������ ����������� - ".$ft." �����");
			}elseif($action=="add_to_check3"){
				$text = title("���������� � ������ ����������� - ".$ft." �����");
			}
			
			if($full_access==2) $text = title("���������� �� �������� - ".$ft." �����");
			
		}
		
	}elseif($action=="on_check" || $action=="check" || $action=="checked"){
		if($full_access==1 || $full_access==2){
			$text = okp4oin_users_on_check ($action, $connection, $db, $page, $full_access);
		}
		
	}elseif($action=="user_edit"){
		if($full_access==1){
			$text = okp4oin_users_activ ($connection, $db, $id, $ok, $x_value);
		}
	
	}elseif($action=="search"){
		if($full_access==1 || $full_access==2){
			$text = okp4oin_search ($connection, $db, $id, $cid, $page, $ok, $x_value);
		}
	
	}elseif($action=="user_insert"){
		if($full_access==1 || $full_access==2){
			$text = okp4oin_users_insert ($connection, $db, $id, $ok, $x_value);
		}
		
		
	}else{
		
	}
	
	if($full_access==1 || $full_access==2){
		$sSQL = "SELECT COUNT(id) FROM `".$db["okp4oin"]."` WHERE (status=1)";
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_array($result);
		$nrows = $row[0];
		
		$sSQL = "SELECT COUNT(id) FROM `".$db["okp4oin"]."` WHERE (status=2)";
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_array($result);
		$nrows2 = $row[0];
		
		$sSQL = "SELECT COUNT(id) FROM `".$db["okp4oin"]."` WHERE (status=3)";
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_array($result);
		$nrows3 = $row[0];
		
		$menu = "<SMALL>\n";
		$menu .= "<FORM METHOD=\"get\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\" name=\"select\">\n";

		$menu .= "<A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=4amaz&action=on_check\">������������ �� �������� (".$nrows.")</A>\n";
		$menu .= " | <A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=4amaz&action=check\">����������� (".$nrows2.")</A>\n";
		$menu .= " | <A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=4amaz&action=checked\">����������� (".$nrows3.")</A>\n";
		$menu .= "<HR>\n";
		
		$menu .= "<A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=4amaz&action=user_insert\">���������� �� ��������</A>\n";
	//	$menu .= " | <A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=4amaz&action=search\">����� �� ����</A>\n";
	
		$menu .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"4amaz\">\n";
		$menu .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"search\">\n";
		$menu .= "<INPUT TYPE=\"hidden\" NAME=\"ok\" VALUE=\"1\">\n";
		$menu .= "| <B>�����:&nbsp;</B>\n";
		$menu .= "  <INPUT TYPE=\"text\" NAME=\"x_value[0]\" VALUE=\"\" SIZE=\"15\" style=\"width=150\" CLASS=\"text\">\n";
		$menu .= "  &nbsp;<INPUT TYPE=\"submit\" VALUE=\" ����� \" CLASS=\"submit\">\n";
		$menu .= "<HR></FORM></SMALL>\n";
		
		$text = $menu.$text;
	}
	

///////////////////////////////////////////////////////////////////////////////////
// ��������� ���������� �����
	$text = tz_tag_remake($text);
	$text = change_pre_value($SERVER_NAME, $PHP_SELF, $path_to_images, $text);
	
// �����
	echo $text;

?>