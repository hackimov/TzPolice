<?
// �������� �������� �������
	$menu = 'rating_arh';
	$path_to_php = '/_modules';
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
// ���� ������ ����� ������ ��������� WWW
	$HTTP_HOST = $HTTP_SERVER_VARS['HTTP_HOST'];

//-----------------------------------------------------------------------------
// �������� ��� ���� ���������� (�� ������, ���� � php.ini "register_globals = Off"):

	while (list ($key, $val) = each ($HTTP_GET_VARS)) $$key = $val;
	while (list ($key, $val) = each ($HTTP_POST_VARS)) $$key = $val;
	
// �������������:
	if (isset ($HTTP_GET_FILES['img'])) { $img = $HTTP_GET_FILES['img']; }
	if (isset ($HTTP_POST_FILES['img'])) { $img = $HTTP_POST_FILES['img']; }


///////////////////////////////////////////////////////////////////////////////////
// ��������� ���������� � ����� ������ (���� �� �����������, ����� ������ �� �������, � ������ �������):
	//$connection = mysql_connect ($hostName, $userName, $password);
	$connection = $db;
// �������� ���� ������ (���� ������� �� ����������, �� ������ �������):
//	$database = mysql_select_db ($databaseName, $connection);
///////////////////////////////////////////////////////////////////////////////////
// ���������� ��� ��� ��������
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/config.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/other.php');
	include_once ($DOCUMENT_ROOT.$path_to_php.'/'.$menu.'/tz_plugins.php');

// ����������
	$sSQL = "SELECT `chief` FROM `sd_cops` WHERE `name` = '".mysql_escape_string(AuthUserName)."' LIMIT 1;";
	$result = mysql_query($sSQL);
	if(mysql_num_rows($result)>0){
		$row = mysql_fetch_assoc($result);
		$chief = $row['chief'];
	}else{
		$chief = '0';
	}
//echo $chief;
	if (AuthUserGroup == 100 || $chief=='1') {
		$full_access = 1;
	} else {
		$full_access = 0;
	}	
	//error_reporting(E_ALL);
	
//------- ��� ���� �� ��������� �������� -------------
	function topic_search ($ok, $x_value, $connection){
		
		$x_value[0]=trim(urldecode($x_value[0]));
		
//		$form = "<CENTER><H4>�����</H4></CENTER>\n";
		$form = "<FORM METHOD=\"get\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\" name=\"select\">\n";
		$form .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"oko_search\">\n";
		$form .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"search\">\n";
		$form .= "<INPUT TYPE=\"hidden\" NAME=\"ok\" VALUE=\"1\">\n";
		$form .= "<TABLE BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\" CLASS=\"size-10\">\n";
		$form .= " <TR VALIGN=\"top\">\n";
		$form .= "  <TD ALIGN=\"right\"><B>ID ������:&nbsp;</B></TD>\n";
		$form .= "  <TD>http://www.timezero.ru/cgi-bin/forum.pl?<INPUT TYPE=\"text\" NAME=\"x_value[0]\" VALUE=\"".$x_value[0]."\" SIZE=\"15\" style=\"width=120px;\" CLASS=\"text\"></TD>\n";
		$form .= "  <TD ALIGN=left>&nbsp;<INPUT TYPE=\"submit\" VALUE=\" ����� \" CLASS=\"submit\"></TD>\n";
		$form .= " <TR VALIGN=\"top\">\n";
		$form .= "  <TD ALIGN=\"right\"><B>������:&nbsp;</B></TD>\n";
		$form .= "  <TD>http://www.timezero.ru/cgi-bin/forum.pl?<B>a=E&c=106795531</B></TD>\n";
		$form .= "  <TD></TD>\n";
		$form .= " </TR>\n";
		$form .= " </TR>\n";
		
		$form .= "</TABLE></FORM>\n";

	// ���� ����� �� ���� �� ����������
		if($ok!=1){
			$text .= $form;
	// ��������� ����� ��� ��
		}else{
			$text .= $form.'<HR><CENTER><H3>����� ������:</H3></CENTER>';

		// ��������� ������� ��� ������ �� ��
			if(isset($x_value[0]) && $x_value[0]!=''){
	//==============================================
				$x_value[0] = mysql_escape_string($x_value[0]);
	//==============================================
				
				$sssSQL = 'SELECT `case_text` FROM `case_main` WHERE `case_url` = \'forum.pl?'.$x_value[0].'\'';
			
				$result=mysql_query($sssSQL, $connection);
				
				$bgcolor[1]='#F5F5F5';
				$bgcolor[2]='#E4DDC5';
				$i=1;
					
				while($row = mysql_fetch_assoc($result)){
					
					if($i>2) $i=1;
					
					$text .= '<DIV class="quote">'.stripslashes($row['case_text']).'</DIV>';
					
					$i++;
				}
				
			}else{
				$text .= "<BR><center><B>������ �� �������</B></center>\n";
			}
		}
		
		return $text;
	}

/////////////////////////////////////////////////////////////////////
	if(!isset($action)) $action = 'search';

	if($action == 'search'){
		if($full_access==1){
			$text = topic_search ($ok, $x_value, $connection);
		}
		
	}else{
		
	}
///////////////////////////////////////////////////////////////////
	
//	if($full_access==1){
//		$menu = "<SMALL>\n";
		
//	$menu .= "<A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=silents&action=upload\">�������� ����</A>\n";
//		$menu .= "<A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=silents&action=top\">����</A>\n";
//		$menu .= "<HR>\n";
		
//		$menu .= "</SMALL>\n";
		
//		$text = $menu.$text;
//	}
	
	$text = tz_tag_remake($text);
	$text = change_pre_value($SERVER_NAME, $PHP_SELF, $path_to_images, $text);
	
	echo '<h1>����� ������� � ������</h1>';
	echo $text;
?>
