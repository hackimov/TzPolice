<html>
<head>
<title>Архив рейтинга каторжан</title>
<LINK href="/_modules/tzpol_css2.css" rel="stylesheet" type="text/css"> 
</head>
<body>
<?
	set_time_limit(200);

//=========================
	include('/home/sites/police/dbconn/dbconn2.php');
//=========================
		if(!isset($_REQUEST['time'])){
			$time = time()-86400;
		}elseif(is_array($_REQUEST['time'])){
			$time = mktime(0, 0, 0, $_REQUEST['time'][1], $_REQUEST['time'][0], $_REQUEST['time'][2]);
		}
		
		if ($time>(time()-86400)) $time = time()-86400;
		
	//	корректировка
		$time = date("d:m:Y:H:i", $time);
		$c_time = $time = explode(':',$time);
		$time = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
		
		$x_value[0] = 0;
		
		echo ("<center><h1>Архив рейтинга каторжан<BR>".date('d.m.Y', $time)."</h1></center>");
		
?>
<BR><DIV STYLE="WIDTH:100%;">
<FORM METHOD="GET" action="/direct_call/prison_rating_archive.php">
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0">
<TR VALIGN="top">
<TD ALIGN="left"><B>Дата:&nbsp;</B>
<SELECT NAME="time[0]">
<?		
		for($i=1;$i<=31;$i++){
			echo ("<OPTION VALUE='".$i."'".(($i==$c_time[0])?' selected':'')." CLASS='select'>".$i."\n");
		}
?>
</SELECT>-<SELECT NAME="time[1]">
<?
		for($i=1;$i<=12;$i++){
			echo ("<OPTION VALUE='".$i."'".(($i==$c_time[1])?' selected':'')." CLASS='select'>".$i."\n");
		}
?>		
</SELECT>-<SELECT NAME="time[2]">
<?
		for($i=2007;$i<=date("Y");$i++){
			echo ("<OPTION VALUE='".$i."'".(($i==$c_time[2])?' selected':'')." CLASS='select'>".$i."\n");
		}
?>		
</SELECT>
<BR>   <input type="submit" class="submit" value="Вывести >>">
</TD>
</TR>
</FORM>
</TABLE><BR>
<?
	$today = date("Y-m-d",$time);
	$query = "SELECT `nick`, `collected` FROM `prison_rating` WHERE `date` = '".$today."' ORDER BY `collected` DESC LIMIT 40";
	$result = mysql_query($query) or die(mysql_error());
	$ic=0;
	while ($res[$ic] = mysql_fetch_array($result)) $ic++;
//	print_r($res);
//	echo ($query);
	mysql_close();
	require('/home/sites/police/dbconn/dbconn.php');
	
	error_reporting(E_ALL);

	require ('/home/sites/police/www/_modules/functions.php');

	function FullInf($nick, $cop_detect) {

		$userinfo = GetUserInfo($nick);
//		echo($nick);
		if(!isset($userinfo['error'])) $userinfo['error']=0;
		if (!$userinfo['error'] && $userinfo['level'] > 0) {
			if ($userinfo['man'] == 0) {
				$pro = $userinfo['pro'].'w';
			} else {
				$pro = $userinfo['pro'];
			}
			if ($cop_detect && (($userinfo['clan'] == "police" && strlen($userinfo['clan']) > 2) || ($userinfo['login'] == 'Mirazte'))) {
				$ret = 'cop';
			//	echo("[pers clan={$userinfo['clan']} nick={$userinfo['login']} level={$userinfo['level']} pro={$pro}]");
			} else {
//				$_RESULT = array('res' => "OK");
				if (strlen($userinfo['clan']) > 2) {
					$ret = '[pers clan='.$userinfo['clan'].' nick='.$userinfo['login'].' level='.$userinfo['level'].' pro='.$pro.']';
				} else {
					$ret = '[pers clan=0 nick='.$userinfo['login'].' level='.$userinfo['level'].' pro='.$pro.']';
				}
			}
		} else {
			$ret = 'error';
		}
	
		return $ret;
	}
	
	$count = 1;
	$count2 = 0;
	$bgc = 1;
	$bg[1] = 'FEF5E9';
	$bg[2] = 'FCE6C7';
	$now = time();
	$out = '<br><br><center><table width="400" border="0" cellspacing="3" cellpadding="3"><tr><td width="1" bgcolor="#FAC987" align="center"><b>&nbsp;&nbsp;№&nbsp;&nbsp;</b></td><td bgcolor="#FAC987"><b>Персонаж</b></td><td bgcolor="#FAC987" align="center" width="1"><b>Собрано</b></td></tr>';
//	$cheaters = array();

	while ($count<21) {
		$d=$res[$count2];
//		print_r($d);
		$repeat = 1;

			$user_full = FullInf($d['nick'], 1);
			if ($user_full == 'error') $user_full="[b]".$d['nick']."[/b]";
//			echo ($user_full);
		if ($user_full !== 'cop') {
			$fulltext = ParseNews3($user_full);
			$out .= '
		            <tr><td bgcolor="#'.$bg[$bgc].'" align="center">'.$count.'</td><td bgcolor="#'.$bg[$bgc].'">'.$fulltext.'</td><td bgcolor="#'.$bg[$bgc].'" align="center">'.$d['collected'].'</td></tr>';
			$bgc++;
			if ($bgc > 2) $bgc = 1;
			$count++;
			
		}
		$count2++;
	}
	$out .= '</table>';

	echo ($out);
?>
</body>
</html>