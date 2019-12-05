<?php

$in['op'] = ($in['op'])?$in['op']:'corsairs';
$in['nick'] = ($in['nick'])?$in['nick']:'Ник персонажа';
$in['sdate'] = ($in['sdate'])?$in['sdate']:date('d.m.Y',time()-10368000);
$in['fdate'] = ($in['fdate'])?$in['fdate']:date('d.m.Y',time());
$in['type'] = ($in['type'])?$in['type']:'need';


if(abs(AccessLevel) & AccessOP) {	korsairs();} else {	echo $mess['AccessDenied'];
}

function getLink($vars=Array(),$ignored=Array()) {
	global $in;

	foreach($vars as $k=>$v) {
   		$link[] = "$k=$v";
	}
	foreach($in as $k=>$v) {
	 	if($ignored[$k] > 0) continue;
    	$link[] = "$k=$v";
	}
	return "?".implode("&",$link);
}

function gUserInfo($login,$lvl,$clan,$pro,$rank,$link) {    global $in;
	if($link) {		$link = "$link".getLink(Array('nick'=>$login),Array('act'=>1,'nick'=>1,'page'=>1));
    }

    $tmp = ($clan)?"<img src='http://timezero.ru/i/clans/$clan.gif' border=0 style='vertical-align: text-bottom'>":"";
	$tmp .= ($link)?"<a href='$link' target='logs'><b>$login</b></a>":"<b>$login</b>";
	$tmp .= " [$lvl]<img src='http://timezero.ru/i/i$pro.gif' border=0 style='vertical-align: text-bottom'><img src='http://timezero.ru/i/rank/$rank.gif' border=0 style='vertical-align: text-bottom'>";
	return $tmp;
}

function getPages($all,$page,$limit) {	$pages = ceil($all/$limit);
	for($i=1;$i<=$pages;$i++) {		$tmp .= ($i==$page)?"<b>$i</b> ":"<a href='".getLink(Array('page'=>$i),Array('page'=>1))."'>$i</a> ";	}
	return "<div align=right>Страницы: $tmp</div>";

}

function korsairs() {	global $in;


	$typeslist = Array('need'=>'Непроверенные','good'=>'Без нарушений','bad'=>'Прокачки');
	$types = "<select name=type>";
	$i=0;
	foreach($typeslist as $k => $v) {
		$sel = "";
		if($k == $in['type']) {
			$sel = " SELECTED";
			$head = $v;
			$check = "AND `check`='$i'";
		}
	    $types .= "<option value='$k'$sel>$v</option>";
	$i++;
	}
	$types .= "</select>";


	$onpro = ($in['op'] == 'mercenaries')?'pro != 1':'pro = 1';
	$where = "WHERE $onpro $check";

	$page = ($in['page'] > 1)?$in[page]:1;
	$limit = 15;
	$offset = ($page-1)*$limit;

    echo "
	    <form method=GET action=''>
			<input name='act' type='hidden' value='$in[act]'>
			<input name='op' type='hidden' value='$in[op]'>

			<table align=center cellspacing=5 cellpadding=0  bgcolor=#F4ECD4>
				<tr>
					<th>
						<input type=text name=sdate value='".$in[sdate]."' size=11 maxlength=10>-<input type=text name=fdate value='".$in[fdate]."' size=11 maxlength=10></th>
					<th>
						<input type=text name=nick value='".$in[nick]."' onfocus='clear_field(this)' onblur='check_field(this)' size=42 maxlength=20></th>
					<th>
						$types
					</th>
					<th>
						<input type='button' value='сбросить' onclick='window.location.href=\"?act=$in[act]\"'>
						<input type='submit' value='Выполнить поиск'>
					</th>
				</tr>
			</table>
		</form>
    	<H3>$head</H3>
    	<br>
		<iframe src='direct_call/corsairs_detail.php?nick=$in[nick]&op=$in[op]&sdate=$in[sdate]&fdate=$in[fdate]&type=$in[type]' name='logs' style='width: 100%; height: 250px; margin: 0px;' align='middle' scrolling='auto'></iframe>

    	<table style='width: 100%; font-size: 12px;' border=1 cellpadding=3 cellspacing=3>
	    <tr>
	    	<th style='width: 1%;'>#</th>
	    	<th>Логин</th>
	    	<th style='width: 25%;'>Логов</th>
	    </tr>
    ";

    $query = mysql_query("SELECT count(battle) as battles,a.* FROM `deorg_korsairs` AS a $where GROUP BY login");
    $all = mysql_num_rows($query);

    echo getPages($all,$page,$limit);

    $query = mysql_query("SELECT count(battle) as battles,a.* FROM `deorg_korsairs` AS a $where GROUP BY login LIMIT $offset,$limit");
    $pg = mysql_num_rows($query);
    if($pg > 0) {
	    $i=$offset+1;
	    while($p = mysql_fetch_array($query)) {
			$login = gUserInfo($p['login'],$p['lvl'],$p['clan'],0,$p['rank'],'direct_call/corsairs_detail.php');
	    	echo "
	    	<tr>
		    	<th>$i</th>
		    	<td style='padding-left: 10px;'>$login</td>
		    	<th>".$p['battles']."</th>
		    </tr>
	     	";

		$i++;
	    }
    } else {    	echo "
	    	<tr>
		    	<th colspan=3>Ничего не найдено</th>
		    </tr>
	     ";
    }
    echo "</table>";

    echo getPages($all,$page,$limit);

}

function detailinfo() {	global $in;

	echo "
	<form method=GET action=''>
		<input name='act' type='hidden' value='$in[act]'>
		<input name='op' type='hidden' value='$in[op]'>
        <input name='g' type='hidden' value='$in[d]'>

		<table align=center cellspacing=5 cellpadding=0  bgcolor=#F4ECD4>
			<tr>
				<th>
					<input type=text name=sdate value='".$in[sdate]."' size=11 maxlength=10>-<input type=text name=fdate value='".$in[fdate]."' size=11 maxlength=10></th>
				<th>
					<input type=text name=nick value='".$in[nick]."' onfocus='clear_field(this)' onblur='check_field(this)' size=42 maxlength=20></th>
				<th>
					$types
				</th>
				<th>
					<input type='button' value='сбросить' onclick='window.location.href=\"?act=$in[act]\"'>
					<input type='submit' value='Выполнить поиск'>
				</th>
			</tr>
		</table>
	</form>

	";


}


?>