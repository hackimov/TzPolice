<h1>������ �� �������� �� ������� ����� �������</h1>

<table border="0" cellspacing="0" background="i/bgr-grid-sand.gif" cellpadding="10" style="BORDER: 1px #957850 solid;">
<tr><td>
<table width="100%"><tr><td align="center"><font color="red"><b>��������!</b></font><br></td></tr></table>
<center><font color="red"><b>������ �� �������� ������ ��������� �������� �� ���� 1 ���� � 3 �����.</b></font></center><br>

0. ������������ � ��������� ����������� �������� �� ������� ������� � ��������������� <a href="http://www.timezero.ru/cgi-bin/forum.pl?a=E&c=88601064&m=1" target=_blank>������</a> ������.<br>
1. �������� �� ������� ���������� ��� ���������� �� ���� 4 ������. �������� ���������� ��� ��������� �����������, ���������� � ���� ��� ������� ������������. ���� �������� �������� - 3 �����.<br>
2. ����� �������� ���������� ������� ������ �� �������� - �� 5 �����, ��������� - 10 ������ �����.<br>
3. ��������� ������� ������ �� ��������:<ul>
<li> 12 ����� - 100 ������ �����
<li> 1 ��� - 300 ������ ����� (<font color="red"><b><u>�����</u> ������� ��������� � ����������� ������������� ������!!!</b></font>)
</ul>
4. �� ���� ��������, ����������� � ��������� �� �������, �� ������ ���������� � ����������� <b>������������� ������</b> �������: <?

$SQL = "SELECT name FROM sd_cops WHERE dept=18 AND chief=0";
$result = mysql_query($SQL) or die (mysql_error());

if (mysql_num_rows($result) > 0 ) {
	$tmp = "";

	while (list($name) = mysql_fetch_row($result)) {
		echo($tmp."<img src='_imgs/clans/police.gif'><b><a href='http://www.timezero.ru/info.html?".$name."' target='_blank'>".$name."</a></b>");
		$tmp = ", ";
	}
}

?> ��� � <b>���������� ������</b> - <?

$SQL = "SELECT name FROM sd_cops WHERE dept=18 AND chief=1";
$result = mysql_query($SQL);

if (mysql_num_rows($result) > 0 ) {
	$tmp = "";

	while (list($name) = mysql_fetch_row($result)) {
		echo($tmp."<img src='_imgs/clans/police.gif'><b><a href='http://www.timezero.ru/info.html?".$name."' target='_blank'>".$name."</a></b>");
		$tmp = ", ";
		$nach = $name;
	}
}
?>
<br>
������ ����������� ������� ������ � ��������� ������ �������� �� ������ <a href="http://www.tzpolice.ru/?act=public_posts">http://www.tzpolice.ru/?act=public_posts</a>
<br>
</td></tr>

</table><BR>
<?
	function police_online_view_999 ($connection){
		$query = "SELECT `id`, `name` FROM `sd_depts`";
		$tmp = mysql_query($query, $connection);
		while ($rslt = mysql_fetch_array($tmp))
			{
				$dept[$rslt['id']] = $rslt['name'];
			}
//print_r($dept);
	//	$text = "<B>������������ ����� ��-����</B>";
		$sSQL = "SELECT `nick`, `status` FROM `cops_online` WHERE `logout`='0' AND (`status`='0' OR `status`='2') GROUP BY `nick` ORDER BY `nick` ASC";
//		$sSQL = "SELECT nick, status FROM `cops_online` WHERE `logout`='0' ORDER BY `status` ASC, `nick` ASC";
		$result = mysql_query($sSQL, $connection);
		$nrows=mysql_num_rows($result);
		if($nrows>0){
			$text .= "<TABLE WIDTH=\"95%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\" ALIGN=center>\n";
			
			$bgcolor[1]="#F5F5F5";
			$bgcolor[2]="#E4DDC5";
			$i=1;
			$i2=0;
			
			while($row = mysql_fetch_array($result)){
				$sSQL4 = "SELECT `dept` FROM `sd_cops` WHERE `name` = '".$row["nick"]."' LIMIT 1;";
				$result4 = mysql_query($sSQL4, $connection);
				$row4 = mysql_fetch_array($result4);
				
				$dept_users[$row4["dept"]][$row["nick"]] = $row["status"];
			}
			
			$cur_dept ="";
			ksort ($dept_users);
			reset ($dept_users);
			foreach ($dept_users AS $key=>$val){
				
				if($dept[$key] == "������������ �����"){
				
					foreach($val AS $k2=>$v2){
						$i2++;
						if($i>2) $i=1;
						$text .= "<TR BGCOLOR=\"".$bgcolor[$i]."\">\n";
						$text .= " <TD VALIGN=top STYLE=\"PADDING-left:20px;\"><img src='_imgs/clans/police.gif'><b><A HREF=\"javascript:{}\" OnClick=\"ClBrd('private [".stripslashes($k2)."]');\" TITLE=\"private [".stripslashes($k2)."]\">".stripslashes($k2)."</A></B></TD>\n";
						$text .= "</TR>\n";
						$i++;
					}
				}
			}
			$text .= "</TABLE>\n";
			
			if($i2>0){
				$text = "<CENTER><B>� ������ ������ ����� ���������� �:</B></CENTER>".$text;
//				$text .= "<CENTER>�����: ".$i2." �������</CENTER>\n";
			}else{
				$text .= "<CENTER><B>� ������ ������ � ���� ����������� ������ ���</B></CENTER>";
			}
			
		}else{
			$text .= "<CENTER><B>� ������ ������ � ���� ����������� ������ ���</B></CENTER>";
		}
		$text .= "<DIV><SMALL>*���������� ����������� ������ 5 �����</SMALL></DIV>\n";

		return $text;
	}
	
	echo police_online_view_999 ($db);

?>
</center>
<br>  <br> 
��� ����������� �������� �� ������� ����� ������� ��� ������ ���������� ����������� ���������� �� �������� ��������� <img src='_imgs/clans/Financial Academy.gif' alt='Financial Academy' border='0'><b>Terminal 01</b> [5]<img style='vertical-align:text-bottom' src='_imgs/pro/i0.gif' border='0'>. � ������� �������� 10 ����� ����� �������� ��������, ����������� �������, ����� ��������� � ������� �� �������� �������� ����� ��������:
 <br> 
 <br><b>10</b> ��: ������� ��������, ���� �� 5 �����.
 <br><b>100</b> ��: ���������� ��������, ���� �� 12 �����.
 <br><b>300</b> ��: ������� ��������, ���� �� 1 ����.
 <br> <br>
* �������� ����, ������������ �� ������ �������, �����������, ������, ��� ������� ��
 ����������, �� ������ �� ����� �������� � �� ������������.
 <br> 
 <br> ���������� ������ �� �����, ������� �����! �������� �� 99 ����� ����� ���������� ������� � ��������� �� 10 ����� - ���������� 89 ����� ��������� ������������� ��������������� �������.
 <br> 
 <br> <div align='center'><div class='quote'><b>��������!</b>
 <br> ��� ������ ������� �������� � ���� "�������" ������� ��� ���������� ������������� ������, � ������� �� ������������ � ��������.</div></div>
 <br> 
 <br> ��������� ����� ������ �� ������ � ����� ������ ��������, ������� � ������ ��������� <img src='_imgs/clans/Financial Academy.gif' alt='Financial Academy' border='0'><b>Terminal 01</b> [5]<img style='vertical-align:text-bottom' src='_imgs/pro/i0.gif' border='0'> ����� <b>status</b>, ��������:
 <br> 
 <br> [deadbeef] private [Terminal 01] status
 <br> [Terminal 01] private [deadbeef] ���� ������ (�������) ������� 02.12.2006 16:44 � ��������� � ������� �� ��������.
 <br> 
 <br> ��������� �������� �������� ����� ��� ��������� <img src='_imgs/clans/Financial Academy.gif' alt='Financial Academy' border='0'><b>Terminal 01</b> [5]<img style='vertical-align:text-bottom' src='_imgs/pro/i0.gif' border='0'> � ������� 72 ����� ����� ��������. <a href='http://www.tzpolice.ru/?act=law_results' target='_blank'>������� ��������</a> �������� �� ����� ������� � ����� ������.
