<h1>����������� ������/��������</h1>
<?
////////////////////////////////////////////////////////
$m_users=array('Amazonka', 'D i J i', 'Fribble', 'Invisible', 'shturm', '�����������', 'MyJlbTuk');

////////////////////////////////////////////////////////

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
?>

<b>������� TimeZero �������� ����������� � ����������� ������.</b><br>
<br>
��� ����, ����� ���������� �������� ���� ���������, ��� ����������: <br>
1. �������� �������� ������ � �������.<br> 
2. ���������� ��������� ������ � ����� ��������� ��������. (������, ������� ����� ������ �������, ������ ���� ���������� �������, ������ ������ - ������. �� ��������� ���������� ������� ������ � �����.) <br>
3. ������������ � ����������� � ���������� �������������� (�����, ��������������  ������������ �������, ��������� � ���������� ��� ��������� )<br>
4.  ����������� ����� �����  1000��. (���������� ��������� �������)<br>
<br>
��� ���� ��� �� ��������� ������, ��� ����������:<br>
1. �������� ������ �� ���������� � ������� �����.<br>
2. �������� � ����� ������� ��������������� ������������.<br>
<br>
���������� � ���������� ��������� ��������� �� ������ ��������� �� ������ <A HREF="http://stalkerz.ru/?a=pub&id=777" TARGET="_blank">http://stalkerz.ru/?a=pub&id=777</A>
<BR><BR>
��� �������� ���������� ������� ����������:<BR>
1. ����� � ���� ������<BR>
2. �������� ������ �� ���� ������������� ����������� �������� ��������� ����.<BR><BR>
<B>������:</B><BR>
00:00 [����� 1] pravate [����� 2] private [�����������] ������������. �������� ��� ����������.<BR>
00:00 [����� 2] pravate [����� 1] private [�����������] � ��������(�).<BR>
<hr size=1>

<?php	
//error_reporting(E_ALL);
	
	$w_users = implode("' OR `nick` = '", $m_users);
	
/*	$sSQL = "SELECT `nick`, `status` FROM `mp_online` WHERE `logout`='0' AND (`nick` = '".$w_users."') ORDER BY `status` ASC, `nick` ASC";
	$result = mysql_query($sSQL, $connection);
	while($row = mysql_fetch_array($result)){
		$users[$row["nick"]] = $row["status"];
	}
*/	
	$sSQL = 'SELECT `nick`, `status` FROM `cops_online` WHERE `logout`=\'0\' AND (`nick` = \''.$w_users.'\') ORDER BY `status` ASC, `nick` ASC';
	$result = mysql_query($sSQL, $connection);
	while($row = mysql_fetch_assoc($result)){
		$users[$row['nick']] = $row['status'];
	}
	
	sort ($m_users);
	reset ($m_users);
	
	$nrows = count($m_users);
	
	if($nrows>0){
		$text .= "<DIV><SMALL>*���������� ����������� ������ 5 �����</SMALL></DIV>\n";
		$text .= "<TABLE WIDTH=\"85%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\" ALIGN=center>\n";
//		$text .= "<TR BGCOLOR=\"#E4DDC5\" ALIGN=\"center\">\n";
//		$text .= " <TD><B>�����</B></TD>\n";
//		$text .= " <TD><B>�����</B></TD>\n";
//		$text .= "</TR>\n";
		$bgcolor[1]='#F5F5F5';
		$bgcolor[2]='#E4DDC5';
		$i=1;
		$i2=0;
		
		while($i2<$nrows){
			if($i>2) $i=1;
			$text .= '<TR BGCOLOR="'.$bgcolor[$i]."\">\n";
			
			$text .= " <TD VALIGN=top WIDTH=10>";
			if(array_key_exists($m_users[$i2],$users)){
				if($users[$m_users[$i2]]=='1'){
					$text .= "<FONT COLOR=\"red\"><B>ChatOff</B></FONT>";
				}else{
					$text .= "<FONT COLOR=\"green\"><B>OnLine</B></FONT>";
				}
			}else{
				$text .= 'OffLine';
			}
			$text .= "</TD>\n";
			
			$sSQL3 = 'SELECT `clan_id`, `name`, `pro`, `sex`, `level` FROM `'.$db['tz_users'].'` WHERE `name`=\''.$m_users[$i2].'\'';
			$result3 = mysql_query($sSQL3, $connection);
			if(mysql_num_rows($result3)>0){
				$row3 = mysql_fetch_assoc($result3);
				if($row3['clan_id']>0){
					$sSQL2 = 'SELECT `name` FROM `'.$db['tz_clans'].'` WHERE `id`=\''.$row3['clan_id'].'\'';
					$result2 = mysql_query($sSQL2, $connection);
					$row2 = mysql_fetch_assoc($result2);
					$clan = '{_CLAN_}'.trim($row2['name']).'{_/CLAN_}';
				}else{
					$clan = '';
				}

				$text .= ' <TD VALIGN=top>'.(($row['status']=='1')?'[Chat: Off] ':'').$clan.' <A HREF="javascript:{}" OnClick="ClBrd(\'private ['.stripslashes($row3['name']).']\');" TITLE="private ['.stripslashes($row3['name']).']">'.stripslashes($row3['name']).'</A> ['.$row3['level'].'] {_PROF_}'.$row3['pro'].(($row3['sex']=='0')?'w':'')."{_/PROF_}</TD>\n";
			}else{
				$text .= " <TD VALIGN=top><A HREF=\"javascript:{}\" OnClick=\"ClBrd('private [".stripslashes($m_users[$i2])."]');\" TITLE=\"private [".stripslashes($m_users[$i2])."]\">".stripslashes($m_users[$i2])."</A></TD>\n";
			}
			$text .= "</TR>\n";
			$i++;
			$i2++;
		}
		
		$text .= "</TABLE>\n";
		$text .= "<CENTER>�����: ".$nrows." �������</CENTER>\n";
	
	}else{
		$text .= "<CENTER>������ ���</CENTER>";
	}

	$text = tz_tag_remake($text);
	$text = change_pre_value($SERVER_NAME, $PHP_SELF, $path_to_images, $text);
	
	echo $text;
?>
<hr size=1>
<b>��������� ������!</b><br><br>
� ������ ������ ��� �������������� ���������� ��� ���������.