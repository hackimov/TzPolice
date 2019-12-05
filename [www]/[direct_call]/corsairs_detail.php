<?php
$in = array();
foreach($_GET as $k => $v) {
	$temp = mb_convert_encoding($v,"cp1251","utf8");
	$temp2 = mb_convert_encoding($temp,"utf8","cp1251");
	if($temp2 == $v) {
		$v = mb_convert_encoding($v,"cp1251","utf8");
	}
	if($k == 'location' && !preg_match("/^[-]?[\d]{1,3}\/[-]?[\d]{1,3}$/", $v)) {
		$v = '';
	}
	$in[$k] = addslashes(htmlspecialchars(trim($v)));
}

$in['op'] = ($in['op'])?$in['op']:'corsairs';
$in['nick'] = ($in['nick'])?$in['nick']:'Ник персонажа';
$in['sdate'] = ($in['sdate'])?$in['sdate']:date('d.m.Y',time()-10368000);
$in['fdate'] = ($in['fdate'])?$in['fdate']:date('d.m.Y',time());
$in['type'] = ($in['type'])?$in['type']:'need';


$typeslist = Array('need'=>0,'good'=>1,'bad'=>2);

include("../_modules/java.php");
require("../_modules/functions.php");
require("../_modules/auth.php");
$battlelink = "http://www.timezero.ru/sbtl.ru.html?";

function gUserInfo($login,$lvl,$clan,$pro,$rank,$link) {
    global $in;
	if($link) {
		$link = "$link".getLink(Array('nick'=>$login),Array('act'=>1,'nick'=>1,'page'=>1));
    }

    $tmp = ($clan)?"<img src='http://timezero.ru/i/clans/$clan.gif' border=0 style='vertical-align: text-bottom'>":"";
	$tmp .= ($link)?"<a href='$link' target='logs'><b>$login</b></a>":"<b>$login</b>";
	$tmp .= " [$lvl]<img src='http://timezero.ru/i/i$pro.gif' border=0 style='vertical-align: text-bottom'><img src='http://timezero.ru/i/rank/$rank.gif' border=0 style='vertical-align: text-bottom'>";
	return $tmp;
}



echo "
<html>
<meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>
<meta http-equiv='Content-Language' content='ru'>
<head>
  <title>You're not supposed to see this =)</title>
<LINK href='../_modules/tzpol_css.css' rel='stylesheet' type='text/css'>
<style>
body,html {	margin: 0px;
	padding: 0px;
	font-size: 12px;
}
table {
	margin: 0px;
	font-size: 12px;
	width: 100%;
	border: 1px solid black;

}

</style>
<script>
	var ids = new Array();

	function checkAll(l) {		for(var i in ids) {			var id = 'N'+ids[i]+l;			document.getElementById(id).checked = true;
		}	}

	function clearAll() {		checked = new Array();
		document.getElementById('chall1').checked = false;
		document.getElementById('chall2').checked = false;
		for(var i in ids) {
			var id = 'N'+ids[i];
			document.getElementById(id+'1').checked = false;
			document.getElementById(id+'2').checked = false;
		}	}
	function sendAll() {    	var checked = new Array();
    	for(var i in ids) {
			var id = 'N'+ids[i];
			var val = 0;
			val = (document.getElementById(id+'1').checked == true)?1:val;
            val = (document.getElementById(id+'2').checked == true)?2:val;

			if(val > 0) checked.push(ids[i]+'='+val);
		}

		alert(checked);

	}

</script>
</head>
<body style='margin: 15 15 15 15;' bgcolor='#EBDFB7' text='#455600' alink='#0D3AB4' link='#0D3AB4' vlink='#0D3AB4'>
";


if(abs(AccessLevel) & AccessOP) {	if($in['nick'] != '' && $in['nick'] != 'Ник персонажа') {        $type = $typeslist[$in['type']];

        $query = mysql_query("SELECT * FROM `deorg_korsairs` WHERE login='$in[nick]' LIMIT 1");
        $uinfo = mysql_fetch_array($query);

        $uinfo = gUserInfo($uinfo['login'],$uinfo['lvl'],$uinfo['clan'],0,$uinfo['rank'],false);


        $query = mysql_query("SELECT * FROM `deorg_korsairs` WHERE login='$in[nick]' AND `check`='$type'");

	    echo "
	    $uinfo
	    <br>
	    <hr>
	    <table style='width: 100%;' border='0' cellpadding='5' cellspacing='0'>
		<tbody>
		<tr bgcolor='#deceb4'>
			<th style='border: 1px solid black'>Лог</th>
		   	<th style='border: 1px solid black'>Время</th>
		   	<th style='border: 1px solid black'>Урон</th>
		   	<th style='border: 1px solid black'>Противники [урон][убит]</th>
		   	<th style='border: 1px solid black'>
		   	<input name='chall' id='chall1' type='radio' value='1' onchange='checkAll(1);' alt='Определить все как без нарушений' title='Определить все как без нарушений'><input id='chall2' name='chall' type='radio' value='2' onchange='checkAll(2);' alt='Определить все как прокачки' title='Определить все как прокачки'>
		   	</th>
    	</tr>
		";
		$i=0;
	    while($p = mysql_fetch_array($query)) {

	    	$enemies = Array();
	    	$info = explode('#',$p['info']);

	    	foreach($info as $k => $v) {	    		#login,clan,lvl,rank,pro,turn,dmg#
	    		$enemy = explode(',',$v);
	    		$einfo = gUserInfo($enemy[0],$enemy[2],$enemy[1],$enemy[4],$enemy[3],false);
	    		$enemies[] = "$einfo [<b style='color: red;'>".$enemy[6]."</b>][<b>".$enemy[5]."</b>]";
	    	}
	    	$battle = "<a href='$battlelink".$p['battle']."' target=_blank>".$p['battle']."</a>";
            $id = array($p['id'],"N".$p['id']."1","N".$p['id']."2");
	    	echo "
	    	<tr bgcolor='#B8B389'>
	    		<th style='border: 1px solid black'>
                <img src='/i/bullet-red-01a.gif' border=0 width=18 height=11 alt='Скопировать номер лога в буфер обмена'>$battle</th>
		    	<th style='color: navy; border: 1px solid black'>
		    	".date("d.m.Y H:i:s",$p['battletime'])."
		    	</th>
		    	<th style='color: red; border: 1px solid black'>".$p['damage']."</th>
                <td style='border: 1px solid black; padding-left: 10px;'>".implode("<hr>",$enemies)."</td>
		    	<th style='border: 1px solid black' nowrap>
		    	<input name='$id[0]' id='$id[1]' type='radio' value='1' alt='Без нарушений' title='Без нарушений'><input name='$id[0]' id='$id[2]' type='radio' value='2' alt='Прокачка' title='Прокачка'>
		    	</th>
	    	</tr>
	    	<script>
	    	ids.push($id[0]);
	    	</script>
	     	";
	   	$i++;
	    }

	    echo "
	    <tr>
		    	<th colspan=4></th>
		    	<th nowrap>
                	<input type='button' value='Сбросить' onclick='clearAll()'><input type='submit' value='Пометить' onclick='sendAll()'>
		    	</th>
	    	</tr>
	    </tbody>
	    </table>
	    ";
	}
} else {
	echo "шу шу, го эвей ;)";
}

echo "
</body>
</html>";
?>
