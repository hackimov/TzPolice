<?php

if(abs(AccessLevel) & AccessOP) {
	history();
} else {
	echo $mess['AccessDenied'];
}


function history() {
	
	global $in;

	$dayb = isset($in['dayb'])?$in['dayb']:14;

	echo "<script type='text/javascript'>
		function getNowDate() {
			date = new Date();
			var dd = date.getDate(); 
			if ( dd < 10 ) dd = '0' + dd; 
			var mm = date.getMonth()+1; 
			if ( mm < 10 ) mm = '0' + mm; 
			var yy = date.getFullYear() % 100; 
			if ( yy < 10 ) yy = '0' + yy; 
			return dd+'.'+mm+'.'+yy;
		} 
	</script>";

	echo "<table valign=top width=100%><tr><td valign=top width=50%><form method='post'><input type='submit' value='Показать'>&nbsp; за последние <input type='text' name='dayb' style='width:100px;' value='".$dayb."'> дн. </form></td><td valign=top> <B>(</B> <input type='text' id='nnnick' style='width:100px;' value=''> &nbsp;&nbsp;  <a href='#' onClick=\"ClBrd('private [Terminal POLICE] getbtl '+document.getElementById('nnnick').value+' ".date("d.m.y", time()-1295990)." ".date("d.m.y", time()-86390)."')\"><input type='submit' value='===GETBTL==='></a> <B>)</B></td></tr></table> <HR><BR>";

	$text = "<table><tr>
	<td><B>Дата</B></td>
	<td style='padding-left:15px;'><B>Событие</B></td>
	<td style='padding-left:15px;'><B>Операции</B></td></tr>";
	
	$gr = time()-(60*60*24)*$dayb;

	$query = mysql_query("SELECT * FROM `history` WHERE `addtime` > ".$gr." AND `new_pro` = '1' AND `events` LIKE '%4%' ORDER BY `addtime` DESC");
	
	$bgcolor[1]='#F5F5F5';
	$bgcolor[2]='#E4DDC5';
	$i=1;

	while ($row = mysql_fetch_array($query)) {
		
		if ($i>2) $i=1;
		
		$u['clan'] = $row['new_clan'];
		$u['login'] = $row['login'];
		$u['lvl'] = $row['new_lvl'];
		$u['pro'] = $row['new_pro'].($row['new_gender']==0?"w":"");
		$u['pvprank'] = $row['new_pvpr'];


		$text .= "<tr BGCOLOR=\"".$bgcolor[$i]."\">
		<td>".(date("d.m.Y H:i", $row['addtime']))."</td>
		<td style='padding-left:15px;'>".formatUser($u)."</td>
		<td style='padding-left:15px;'><a href=\"#; return false;\" onClick=\"ClBrd('private [Terminal POLICE] getbtl ".$row['login']." ".date("d.m.y", time()-1295990)." ".date("d.m.y", time()-86390)."')\">GETBTL</a></td>
		</tr>";

		$i++;
	}

	$text .= "</table>";
	//$text = tz_tag_remake($text);

	echo $text;
}
?>