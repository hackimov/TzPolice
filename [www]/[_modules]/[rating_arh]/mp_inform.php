<?
// �������� �������� �������
	$menu = 'rating_arh';
	$path_to_php = '/_modules';
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

//=========================
	include("/home/sites/police/dbconn/dbconn2.php");
//=========================
	
	if (AuthUserGroup == 100) {
		$full_access = 1;
	} else {
		$full_access = 0;
	}	
	//error_reporting(E_ALL);
	
//------- ��� ���� �� ��������� �������� -------------
	function toplist ($x_value, $months_a, $time){
		
		if(!isset($time)){
			$time = $time2 = time();
		}elseif(is_array($time)){
			$time2 = mktime(23, 59, 0, $time[4], $time[3], $time[5]);
			$time = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
		}
		
		if ($time>time()) $time = time();
		if ($time2>time()) $time2 = time();
		if ($time>$time2) $time = $time2;
		if ($time2<$time) $time2 = time();
		
	//	�������������
		$time = date("d:m:Y:H:i", $time);
		$c_time = $time = explode(':',$time);
		$time = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
		
		$time2 = date("d:m:Y:H:i", $time2);
		$c_time2 = $time2 = explode(':',$time2);
		$time2 = mktime(23, 59, 0, $time2[1], $time2[0], $time2[2]);
		
		$x_value[0] = intval($x_value[0]);
		
		$text = title('���������� �� �������� �������<BR>'.date("d.m.Y", $time).' 00:00 - '.date("d.m.Y", $time2).' 23:59');
		
		$text .= "<BR><DIV STYLE=\"WIDTH:100%;\">\n";
		
		$text .= "<FORM METHOD=\"GET\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\">\n";
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"mp_inform\">\n";
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"top\">\n";
		$text .= "<TABLE WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\">\n";
		$text .= " <TR VALIGN=\"top\">\n";
		$text .= "  <TD ALIGN=\"left\"><B>���� c:&nbsp;</B>\n";
		$text .= "   <SELECT NAME=\"time[0]\">\n";
		for($i=1;$i<=31;$i++){
			$text .= '<OPTION VALUE="'.$i.'"'.(($i==$c_time[0])?' selected':'').' CLASS="select">'.$i."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[1]\">\n";
		for($i=1;$i<=12;$i++){
			$text .= '<OPTION VALUE="'.$i.'"'.(($i==$c_time[1])?' selected':'').' CLASS="select">'.$months_a[$i]."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[2]\">\n";
		for($i=2007;$i<=date('Y');$i++){
			$text .= '<OPTION VALUE="'.$i.'"'.(($i==$c_time[2])?' selected':'').' CLASS="select">'.$i."\n";
		}
		$text .= "   </SELECT>&nbsp;<B>00:00</B>\n";
		
		$text .= "  <B>&nbsp;-&nbsp;��:&nbsp;</B>\n";
		$text .= "   <SELECT NAME=\"time[3]\">\n";
		for($i=1;$i<=31;$i++){
			$text .= '<OPTION VALUE="'.$i.'"'.(($i==$c_time2[0])?' selected':'').' CLASS="select">'.$i."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[4]\">\n";
		for($i=1;$i<=12;$i++){
			$text .= '<OPTION VALUE="'.$i.'"'.(($i==$c_time2[1])?' selected':'').' CLASS="select">'.$months_a[$i]."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[5]\">\n";
		for($i=2007;$i<=date("Y");$i++){
			$text .= '<OPTION VALUE="'.$i.'"'.(($i==$c_time2[2])?' selected':'').' CLASS="select">'.$i."\n";
		}
		$text .= "   </SELECT>&nbsp;<B>23:59\n";
		$text .= "  </TD>\n";
		$text .= " </TR>\n";
		
		$text .= " <TR VALIGN=\"top\">\n";
		$text .= "  <TD ALIGN=\"left\"><input type=\"submit\" class=\"submit\" value=\"������� >>\">\n";
		$text .= "  </TD>\n";
		$text .= " </TR>\n";
			
		$text .= "</FORM>\n";
		$text .= "</TABLE><BR>\n";
		
		$text .= " <script language=\"JavaScript\" type=\"text/JavaScript\">\n";
		$text .= " <!--\n";
		$text .= " function hide(menu){\n";
		$text .= "  var c=document.getElementById(menu);\n";
		$text .= "  if (c.style.display != 'none') {\n";
		$text .= "   c.style.display = 'none';\n";
		$text .= "  }else{\n";
		$text .= "   c.style.display = '';\n";
		$text .= "  }\n";
		$text .= "  return false;\n";
		$text .= " }\n";
		$text .= " //-->\n";
		$text .= " </script>\n";
		
		$where_date = '`time` >= '.($time-1).' AND `time` <= '.($time2+1);

		$text .= "<DIV ALIGN=left>\n";
		$SQL = 'SELECT * FROM `mp_informer` WHERE `mp_id`>0 AND '.$where_date.' ORDER BY `time` ASC;';
		$sssqc = mysql_query($SQL, MYSQL_DB_CONNECTION2);
		$i = 1;
		$total = 0;
		while($row = mysql_fetch_assoc($sssqc)){
			
			$mp = GetPersParamsById($row['mp_id']);
			$user = GetPersParamsById($row['user_id']);

			$text .= date('d.m.Y H:i', $row['time']).' ';
			$text .= '<B>{_PERS_}'.$mp['name'].'{_/PERS_}</B>';
			$text .= ' ������ ������ �� <B>{_PERS_}'.$user['name'].'{_/PERS_}</B>';
			$text .= ' � ';
			if($row['status']==1) $text .= '�������';
			elseif($row['status']==2) $text .= '��������� ('.stripslashes($row['comment']).'),';
			else $text .= '��������';
			$text .= ' ��� # <A HREF="http://www.timezero.ru/sbtl.ru.html?'.$row['battle_id'].'" TARGET="_blank">'.$row['battle_id']."</A><BR>\n";
		}
//		$text .= '______________<BR>�����: '.$total;
		$text .= "</DIV>\n";
		
		return $text;
	}


//------- ��� ���� �� ��������� �������� -------------
	function toplist2 ($x_value, $months_a, $time){
		
		if(!isset($time)){
			$time = $time2 = time();
		}elseif(is_array($time)){
			$time2 = mktime(23, 59, 0, $time[4], $time[3], $time[5]);
			$time = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
		}
		
		if ($time>time()) $time = time();
		if ($time2>time()) $time2 = time();
		if ($time>$time2) $time = $time2;
		if ($time2<$time) $time2 = time();
		
	//	�������������
		$time = date("d:m:Y:H:i", $time);
		$c_time = $time = explode(':',$time);
		$time = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
		
		$time2 = date("d:m:Y:H:i", $time2);
		$c_time2 = $time2 = explode(':',$time2);
		$time2 = mktime(23, 59, 0, $time2[1], $time2[0], $time2[2]);
		
		$x_value[0] = intval($x_value[0]);
		
		$text = title('���������� �� ���������� �������<BR>'.date("d.m.Y", $time).' 00:00 - '.date("d.m.Y", $time2).' 23:59');
		
		$text .= "<BR><DIV STYLE=\"WIDTH:100%;\">\n";
		
		$text .= "<FORM METHOD=\"GET\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\">\n";
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"mp_inform\">\n";
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"empty\">\n";
		$text .= "<TABLE WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\">\n";
		$text .= " <TR VALIGN=\"top\">\n";
		$text .= "  <TD ALIGN=\"left\"><B>���� c:&nbsp;</B>\n";
		$text .= "   <SELECT NAME=\"time[0]\">\n";
		for($i=1;$i<=31;$i++){
			$text .= '<OPTION VALUE="'.$i.'"'.(($i==$c_time[0])?' selected':'').' CLASS="select">'.$i."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[1]\">\n";
		for($i=1;$i<=12;$i++){
			$text .= '<OPTION VALUE="'.$i.'"'.(($i==$c_time[1])?' selected':'').' CLASS="select">'.$months_a[$i]."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[2]\">\n";
		for($i=2007;$i<=date('Y');$i++){
			$text .= '<OPTION VALUE="'.$i.'"'.(($i==$c_time[2])?' selected':'').' CLASS="select">'.$i."\n";
		}
		$text .= "   </SELECT>&nbsp;<B>00:00</B>\n";
		
		$text .= "  <B>&nbsp;-&nbsp;��:&nbsp;</B>\n";
		$text .= "   <SELECT NAME=\"time[3]\">\n";
		for($i=1;$i<=31;$i++){
			$text .= '<OPTION VALUE="'.$i.'"'.(($i==$c_time2[0])?' selected':'').' CLASS="select">'.$i."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[4]\">\n";
		for($i=1;$i<=12;$i++){
			$text .= '<OPTION VALUE="'.$i.'"'.(($i==$c_time2[1])?' selected':'').' CLASS="select">'.$months_a[$i]."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[5]\">\n";
		for($i=2007;$i<=date("Y");$i++){
			$text .= '<OPTION VALUE="'.$i.'"'.(($i==$c_time2[2])?' selected':'').' CLASS="select">'.$i."\n";
		}
		$text .= "   </SELECT>&nbsp;<B>23:59\n";
		$text .= "  </TD>\n";
		$text .= " </TR>\n";
		
		$text .= " <TR VALIGN=\"top\">\n";
		$text .= "  <TD ALIGN=\"left\"><input type=\"submit\" class=\"submit\" value=\"������� >>\">\n";
		$text .= "  </TD>\n";
		$text .= " </TR>\n";
			
		$text .= "</FORM>\n";
		$text .= "</TABLE><BR>\n";
		
		$text .= " <script language=\"JavaScript\" type=\"text/JavaScript\">\n";
		$text .= " <!--\n";
		$text .= " function hide(menu){\n";
		$text .= "  var c=document.getElementById(menu);\n";
		$text .= "  if (c.style.display != 'none') {\n";
		$text .= "   c.style.display = 'none';\n";
		$text .= "  }else{\n";
		$text .= "   c.style.display = '';\n";
		$text .= "  }\n";
		$text .= "  return false;\n";
		$text .= " }\n";
		$text .= " //-->\n";
		$text .= " </script>\n";
		
		$where_date = '`time` >= '.($time-1).' AND `time` <= '.($time2+1);

		$text .= "<DIV ALIGN=left>\n";
		$SQL = 'SELECT * FROM `mp_informer` WHERE `mp_id`=0 AND '.$where_date.' ORDER BY `time` ASC;';
		$sssqc = mysql_query($SQL, MYSQL_DB_CONNECTION2);
		$i = 1;
		$total = 0;
		while($row = mysql_fetch_assoc($sssqc)){
			
			$mp = GetPersParamsById($row['mp_id']);
			$user = GetPersParamsById($row['user_id']);

			$text .= date('d.m.Y H:i', $row['time']).' ';
			$text .= '<B>{_PERS_}'.$user['name'].'{_/PERS_}</B>';
			$text .= ' ������ ������, �� ������ ������,';
			$text .= ' ��� # <A HREF="http://www.timezero.ru/sbtl.ru.html?'.$row['battle_id'].'" TARGET="_blank">'.$row['battle_id']."</A><BR>\n";
		}
//		$text .= '______________<BR>�����: '.$total;
		$text .= "</DIV>\n";
		
		return $text;
	}
	
/////////////////////////////////////////////////////////////////////
	if(!isset($action)) $action = 'top';
	
	if($action == 'top'){
		if($full_access==1){
			$text = toplist ($x_value, $months_a, $time);
		}
		
	}elseif($action == 'empty'){
		if($full_access==1){
			$text = toplist2 ($x_value, $months_a, $time);
		}
	}else{
		
	}
///////////////////////////////////////////////////////////////////
	
	if($full_access==1){
		$menu = "<SMALL><B>\n";
		
		$menu .= "<A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=mp_inform&action=top\">�������� ������</A>\n";
		$menu .= "| <A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=mp_inform&action=empty\">���������� ������</A>\n";
		$menu .= "<HR>\n";
		
		$menu .= "</B></SMALL>\n";
		
		$text = $menu.$text;
	}
	
	$text = tz_tag_remake($text);
	$text = change_pre_value($SERVER_NAME, $PHP_SELF, $path_to_images, $text);
	
	echo '<h1>���������� ������ ��</h1>';
	echo $text;
?>
