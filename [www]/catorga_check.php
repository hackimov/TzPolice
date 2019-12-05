<?php
	if ($_SERVER['HTTP_HOST'] !== 'www.tzpolice.ru') {
		header('Location: http://www.tzpolice.ru'.$_SERVER['REQUEST_URI']);
	}
/*
	if ($_REQUEST['AuthUser'] == 'rebeca') {
		echo ("<pre>");
		print_r($_REQUEST);
		print_r($_COOKIE);
		echo ("</pre>");
	}
*/

	require('/home/sites/police/www/_modules/functions.php');
	require('_modules/auth.php');
?>
<html>
<head>
<META name="verify-v1" content="h3liW5b59AM60c42sw30IYEu/7xxzKCYdkXvcHujYM4=" />
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
  <title>TZ Police Department</title>
<?
	include('_modules/header.php');
	include('_modules/java.php');
?>
</head>
<body bgcolor="#000000" text="#455600" alink="#0D3AB4" link="#0D3AB4" vlink="#0D3AB4">
<h1>Информация по каторге.</h1>
<hr>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="3">
<tr bgcolor='#DBA951' align='center'><td align='center'>Когда</td><td>Персонаж</td><td>Посадил</td><td>Причина посадки</td><td>Выпустил</td><td align='center'>Причина выпуска</td><td align='center'>Прошло времени с посадки</td></tr>

<?php
	function tz_tag_remake($text){
		
		$text = preg_replace_callback("/(\{_PERS_\})(.*?)(\{_\/PERS_\})/i", 'tz_get_pers', $text);
		$text = preg_replace_callback("/(\{_BATTLE_\})(.*?)(\{_\/BATTLE_\})/i", 'tz_get_battle', $text);
		$text = preg_replace_callback("/(\{_CLAN_\})(.*?)(\{_\/CLAN_\})/i", 'tz_get_clan', $text);
		$text = preg_replace_callback("/(\{_PROF_\})(.*?)(\{_\/PROF_\})/i", 'tz_get_prof', $text);
		
		return $text;
		
	}

	function tz_get_pers($matches){
		
		$matches[2] = strip_tags($matches[2]);
		
		$sSQL3 = 'SELECT `clan_id`, `pro`, `name`, `sex`, `level` FROM `tzpolice_tz_users` WHERE `id`=\''.$matches[2].'\'';
		if(defined('MYSQL_DB_CONNECTION')) $result3 = mysql_query($sSQL3, MYSQL_DB_CONNECTION);
		else $result3 = mysql_query($sSQL3);
	// если в базе персов ТЗ не находим ник
		if(mysql_num_rows($result3)<1){
			$ret .= $matches[2];
		}else{
			$row3 = mysql_fetch_assoc($result3);
			
			if($row3['clan_id']>0){
				$sSQL2 = 'SELECT `name` FROM `tzpolice_tz_clans` WHERE `id`=\''.$row3['clan_id'].'\'';
				if(defined('MYSQL_DB_CONNECTION')) $result2 = mysql_query($sSQL2, MYSQL_DB_CONNECTION);
				else $result2 = mysql_query($sSQL2);
				$row2 = mysql_fetch_assoc($result2);
				
				$clan = '{_CLAN_}'.trim($row2['name']).'{_/CLAN_}';
			//	echo "<BR>\n";
			}else{
				$clan = '';
			}
		//	echo "| ".$clan." ".$name." ".$level." | ".$prof." |";
				
			if(empty($row3['pro'])){
				$row3['pro']='0';
			}
			$ret .= $clan.' '.stripslashes($row3['name']).' ['.$row3['level'].'] {_PROF_}'.$row3['pro'].(($row3['sex']=='0')?'w':'').'{_/PROF_}';
			
		}
		
		return $ret;
	}
	
	function tz_get_battle($matches){
		$matches[2] = strip_tags($matches[2]);
		$ret = '<A HREF="http://www.timezero.ru/sbtl.ru.html?'.stripslashes($matches[2]).'" TARGET="_blank">'.stripslashes($matches[2])."</A>\n";
			
		return $ret;
	}
	
	function tz_get_clan($matches){
		global $DOCUMENT_ROOT;
		$matches[2] = strip_tags($matches[2]);
		if($matches[2]!='' && $matches[2]!='0'){
			if(file_exists($DOCUMENT_ROOT.'/_imgs/clans/'.$matches[2].'.gif')){
				$size = getimagesize($DOCUMENT_ROOT.'/_imgs/clans/'.$matches[2].'.gif');
				
				if($size !== false && $size[0]>1){
					$ret = '<img src="http://www.tzpolice.ru/_imgs/clans/'.$matches[2].'.gif" border=0 ALT="'.$matches[2].'" ALIGN="absBottom" WIDTH="28" HEIGHT="16">';
				}else{
					chmod($DOCUMENT_ROOT.'/_imgs/clans/'.$matches[2].'.gif', 0777);
					
					if(getimagesize ('http://game.timezero.ru/i/clans/'.str_replace(' ','%20', $matches[2]).'.gif')) {
						copy ('http://game.timezero.ru/i/clans/'.str_replace(' ','%20', $matches[2]).'.gif', $DOCUMENT_ROOT.'/_imgs/clans/'.$matches[2].'.gif');
						chmod($DOCUMENT_ROOT.'/_imgs/clans/'.$matches[2].'.gif', 0777);
					}
					$ret = '<img src="http://game.timezero.ru/i/clans/'.$matches[2].'.gif" border=0 ALT="'.$matches[2].'" ALIGN="absBottom" WIDTH="28" HEIGHT="16">';
				}
			} else {
				if(getimagesize ('http://game.timezero.ru/i/clans/'.str_replace(' ','%20', $matches[2]).'.gif')) {
					copy ('http://game.timezero.ru/i/clans/'.str_replace(' ','%20', $matches[2]).'.gif', $DOCUMENT_ROOT.'/_imgs/clans/'.$matches[2].'.gif');
					chmod($DOCUMENT_ROOT.'/_imgs/clans/'.$matches[2].'.gif', 0777);
				}
				$ret = '<img src="http://game.timezero.ru/i/clans/'.$matches[2].'.gif" border=0 ALT="'.$matches[2].'" ALIGN="absBottom" WIDTH="28" HEIGHT="16">';
			}
		}else{
			$ret='';
		}
		
		return $ret;
	}
	
	function tz_get_prof($matches){
		global $prof_alt;
		$matches[2]=strip_tags($matches[2]);
		$ret = '<img src="http://www.tzpolice.ru/_imgs/pro/i'.$matches[2].'.gif" border=0 ALT="'.$prof_alt[intval($matches[2])].'" ALIGN="absBottom">';
			
		return $ret;
	}
    function print_stat_time($timestamp,$start_arg="")
    {
		if ($timestamp>0)
		{
			$date_format=getdate($timestamp);
			return $start_arg.$date_format["yday"]."дн. ".$date_format["hours"]."ч. ".$date_format["minutes"]."м.";
		}
		else
		{
			return $start_arg." 0дн. 00ч. 00м.";
		}
    }

$query="SELECT (t2.time-t1.time)as diapazon,t1.time as when_done, t1.cop_id as posadil, t1.text as reason_posadki,t2.cop_id as vypustil, t2.text as reason_release, t1.user_id as user_id FROM cops_actions AS t1  JOIN cops_actions AS t2  on ( t1.action =179  AND t2.action =180  AND t1.user_id =t2.user_id  ) where ((t2.time-t1.time)<86400 and t1.user_id<>0) order by when_done desc limit 500";
$rs=mysql_query($query);
$bg = 0;
$bgstr[0]="#D0BD9D";
$bgstr[1]="#DBA951";
//<tr bgcolor='#DBA951' align='center'><td align='center'>Когда</td><td>Персонаж</td><td>Посадил</td><td>Причина посадки</td><td>Выпустил</td><td align='center'>Причина выпуска</td><td align='center'>Прошло времени с посадки</td></tr>
while ($tmp = mysql_fetch_array($rs))
{ 
	if ($tmp['user_id']<>0){
		$text="<tr bgcolor='$bgstr[$bg]'><td align='center'>".date("d-m-Y H:i",$tmp['when_done'])."</td><td>{_PERS_}".$tmp['user_id']."{_/PERS_}</td><td>{_PERS_}".$tmp['posadil']."{_/PERS_}</td><td><b>".$tmp['reason_posadki']."</b></td><td>{_PERS_}".$tmp['vypustil']."{_/PERS_}</td><td align='center'>".$tmp['reason_release']."</td><td>".print_stat_time($tmp['diapazon'])."</td></tr>";
		$text = tz_tag_remake($text);
		echo $text;
		$bg++;
		if ($bg > 1) {$bg = 0;}
	}
}
?>
</table>
<hr>
</body>
</html>
