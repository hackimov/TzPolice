<?
include('/home/sites/police/www/otherhistory/lang_ru.php');
setlocale(LC_CTYPE,"ru_RU.CP1251");

#print_r($_POST);
function parseLine($workstr) {
    global $lang;

	$workarray=explode("\t",$workstr);

	switch($workarray[1]) {
		case 175:
		case 176:
		case 177:
		{
		    $workarray[2] = $lang["quest_name_" + $workarray[2]];
		    break;
		}
		case 100:
		{
		    $workarray[3] = $workarray[3];
		    break;
		}
		case 158:
		{
		    $sum_rangPoint = $sum_rangPoint + $workarray[2];
//		    $workarray[4] = this.GetBattleLink($workarray[4]);
		    break;
		}
		case 115:
		{
//		    $workarray[4] = this.GetBattleLink($workarray[4]);
		    break;
		}
		case 137:
		case 195:
		{
//		    $workarray[2] = this.GetBattleLink($workarray[2]);
		    break;
		}
		case 129:
		{
		    $sum_s = $sum_s + $workarray[2];
		    break;
		}
		case 189:
		{
		    $sum_s = $sum_s + $workarray[3];
//		    $workarray[2] = this.GetBattleLink($workarray[2]);
		    break;
		}
		case 110:
		{
		    $workarray[2] = $lang["l_v".$workarray[2]];
		    $sum_s = $sum_s + $workarray[4];
		    break;
		}
		case 202:
		case 212:
		case 213:
		case 302:
		case 305:
		case 309:
		case 310:
		case 35100:
		case 35101:
		{
//		    $workarray[3] = this.GetBattleLink($workarray[3]);
		    if (!isset($workarray[3])){$workarray[3]="";}
		    break;
		}
		case 179:
		{
		    $workarray[2] = $lang["faction_".$workarray[2]];
		    break;
		}
		case 304:
		case 318:
		{
		    $workarray[4] = $lang["l_u".$workarray[4]];
		    break;
		}
		case 400:
		case 401:
		case 358:
		{
		    $workarray[2] = $lang["prof".$workarray[2]];
		    break;
		}
		case 8104:
		{
//		    $workarray[3] = _root.TimeToString($workarray[3]);
		    break;
		}
		case 8200:
		case 8300:
		{
		    $workarray[4] = $lang["log_ars".$workarray[4]];
		    break;
		}
		case 10107:
		{
//		    $workarray[3] = _root.ParceChLab($workarray[3]);
		    break;
		}
		case 10110:
		{
		    $workarray[3] = $lang["log10110_".$workarray[3]];
		    break;
		}
		case 203:
		{
		    $workarray[4] = $lang["Coins".$workarray[4]];
		}
		case 700:
		case 701:
		case 80101:
		case 80102:
		case 80103:
		case 80104:
		{
		    $workarray[2] = $lang["Coins".$workarray[2]];
		    break;
		}
		case 217:
		{
		    $workarray[2] = $lang["Coins".$workarray[2]];
		    if ($workarray[6])
		    {
		        $workarray[6] = $lang["money_send_reason"].": ".$workarray[6];
		    }
		    else
		    {
		        $workarray[6] = "";
		    } // end else if
		    break;
		}
		case 226:
		case 341:
		{
		    $workarray[5] = $lang["Coins".$workarray[5]];
		    break;
		}
		case 326:
		{
		    $workarray[2] = $lang["Coins".$workarray[2]];
		    if ($workarray[7])
		    {
		        $workarray[7] = $lang["money_send_reason"].": ".$workarray[7];
		    }
		    else
		    {
		        $workarray[7] = "";
		    } // end else if
		    break;
		}
		case 700:
		case 701:
		case 702:
		{
		    $workarray[2] = $lang["Coins".$workarray[2]];
		    break;
		}
		case 1200:
		case 1201:
		{
		    $workarray[3] = $lang["Coins".$workarray[3]];
		    break;
		}
		case 4203:
		case 4302:
		case 904202:
		case 904302:
		{
		    $workarray[2] = $lang["bg_m".$workarray[2]."f"];
		}
		case 904201:
		case 904204:
		case 904301:
		case 904305:
		{
		    $workarray[4] = $lang["bg_m".$workarray[4]."f"];
		    break;
		}
		case 204:
		{
		    $workarray[3] = $lang["l_lol1_".$workarray[3]];
		    break;
		}
		case 308:
		{
		    $workarray[3] = $lang["l_lol2_".$workarray[3]];
		    break;
		}
		case 500:
		case 501:
		{
		    $workarray[3] = $lang["man_".$workarray[3]];
		    break;
		}
		case 502:
		case 503:
		{
		    $workarray[4] = $lang["man_".$workarray[4]];
		    break;
		}
		case 314:
		case 207:
		{
		    $workarray[2] = $lang["Coins".$workarray[2]];
		    break;
		}
		case 149:
		{
		    $workarray[2] = $lang["cs_floor_1_".$workarray[2]];
		    $workarray[3] = $workarray[4] = $workarray[5] = "";
		    $workarray[6] = $lang["Chips".$workarray[6]];
		    break;
		}
		case 150:
		{
		    $workarray[2] = $lang["cs_floor_2_".$workarray[2]];
		    $workarray[4] = $lang["Chips".$workarray[4]];
		    break;
		}
		case 151:
		{
		    $workarray[2] = $lang["Chips".$workarray[2]];
		    break;
		}
		case 52139:
		case 52142:
		case 52143:
		case 52144:
		case 52145:
		case 52146:
		{
		    $workarray[3] = $lang["log52139_".($workarray[3]+1)];
		    break;
		}
	}
    $adv_history1 = array('a100' => 2, 'a101' => 2, 'a102' => 2, 'a104' => 2, 'a105' => 2, 'a106' => 2, a107 => 2, a108 => 2, a109 => 2, a111 => 2, a115 => 2, a116 => 2, a118 => 2, a121 => 2, a122 => 2, a125 => 2, a126 => 2, a127 => 2, a128 => 2, a158 => 3, a200 => 3, a204 => 3, a205 => 3, a206 => 3, a215 => 3, a216 => 3, a217 => 4, a225 => 3, a300 => 3, a308 => 4, 'a310' => 2, 'a311' => 3, 'a312' => 3, 'a324' => 3, 'a326' => 4, 'a904100' => 2, 'a904105' => 2, 'a904106' => 2, 'a904107' => 2, 'a904108' => 2, 'a904200' => 2, 'a904203' => 2, 'a904204' => 2, 'a904300' => 2, 'a904304' => 2, 'a904305' => 2, 'a904306' => 2, 'a32100' => 2, 'a32101' => 2, 'a32102' => 2, 'a32103' => 2, 'a32104' => 2, 'a32200' => 2, 'a32201' => 2, 'a32202' => 2, 'a32300' => 2, 'a32301' => 2, 'a32302' => 2, 'a1500' => 2, 'a1100' => 2, 'a1101' => 2, 'a1102' => 2, 'a1300' => 2);
    $adv_history2 = array(a904308 => 3, a904205 => 3);
    $adv_history3 = array(a117 => 2, a121 => 3, a123 => 2, a127 => 3, a200 => 2, a201 => 2, a202 => 2, a205 => 2, a206 => 2, a208 => 2, a209 => 3, a210 => 2, a212 => 2, a213 => 2, a214 => 2, a215 => 2, a216 => 2, a217 => 2, a225 => 3, a300 => 2, a301 => 2, a302 => 2, a303 => 2, a305 => 2, a306 => 2, a307 => 2, a308 => 2, a309 => 2, a310 => 4, a311 => 2, a312 => 2, a313 => 2, a316 => 2, a317 => 2, a318 => 2, a319 => 3, a320 => 2, a321 => 3, a322 => 2, a323 => 2, a324 => 2, a325 => 2, a327 => 2, a340 => 3, a904200 => 3, a904300 => 3, a904308 => 4, a904205 => 4, a502 => 2, a32200 => 3, a32201 => 3, a32202 => 3, a32300 => 3, a32301 => 3, a32302 => 3, a146 => 2);
    $adv_history4 = array(a117 => 3, a123 => 3, a121 => 4, a133 => 2, a134 => 2, a141 => 3, a208 => 3, a209 => 2, a201 => 3, a211 => 2, a318 => 3, a323 => 3, a301 => 3, a700 => 4, a701 => 4, a702 => 4);
    $adv_history5 = array(a152 => 2, a152 => 4, a152 => 6);
    $adv_history6 = array(a153 => 2, a153 => 6);
    $adv_history7 = array(a154 => 3, a154 => 5);
    $adv_history8 = array(a155 => 3, a40000 => 3);
    $adv_history9 = array(a40001 => 2, a40001 => 6);
    $adv_history10 = array(a40002 => 2, a40002 => 6);
    $adv_history11 = array(a184 => 2);
    if ($adv_history3["a" + $workarray[1]])
    {
        if (strpos($workarray[$adv_history3["a" + $workarray[1]]],":") > 0)
   	    {
            $_loc5 = explode(",",$workarray[$adv_history3["a" + $workarray[1]]]);
            foreach ($_loc5 as &$i)
            {
                $i = explode(":",$i);
                $i=$i[0];
            } // end of for...in
            $workarray[$adv_history3["a" + $workarray[1]]] = join(", ",$_loc5);
        } // end if
    } // end if
	if ($adv_history4["a".$workarray[1]])
	{
	    if (strpos($workarray[$adv_history4["a".$workarray[1]]],"[#")!==false)
	    {
		    $prepbuild=substr($workarray[$adv_history4["a".$workarray[1]]],0,strpos($workarray[$adv_history4["a".$workarray[1]]],"[#"));
	    }
	    else
	    {
		    $prepbuild=$workarray[$adv_history4["a".$workarray[1]]];
	    }
	    $arrbuild=split(",",$prepbuild);
	    if (count($arrbuild)>=4)
	    {
	    	if ($arrbuild[1]>180)
	    	{
	    		$arrbuild[1]=$arrbuild[1]-360;
	    	}
	    	elseif($arrbuild[1]<=-180)
	    	{
	    		$arrbuild[1]=$arrbuild[1]+360;
	    	}
	    	if ($arrbuild[2]>180)
	    	{
	    		$arrbuild[2]=$arrbuild[2]-360;
	    	}
	    	elseif($arrbuild[2]<=-180)
	    	{
	    		$arrbuild[2]=$arrbuild[2]+360;
	    	}
	    	$prepbuild=$arrbuild[0]."[".$arrbuild[1]."/".$arrbuild[2]."]";
	    }
    	$workarray[$adv_history4["a".$workarray[1]]]=$prepbuild.strstr($workarray[$adv_history4["a".$workarray[1]]],"[#");
	} // end if
	if ($adv_history5["a".$workarray[1]])
	{
	    $workarray[2] = $LANG["cs_floor_5_".$workarray[2]];
	    $workarray[4] = "";
	    if ($workarray[6]==1){
	    	$workarray[6] = $lang['cs_cointsGame_1'];
	    }
	    else{
			$workarray[6] = $lang['cs_cointsGame_2'];
	    }
	} // end if
	if ($adv_history6["a".$workarray[1]])
	{
	    $workarray[2] = $LANG["cs_floor_5_".$workarray[2]];
	    if ($workarray[6]==1){
	    	$workarray[6] = $lang['cs_cointsGame_1'];
	    }
	    else{
			$workarray[6] = $lang['cs_cointsGame_2'];
	    }
	} // end if
	if ($adv_history7["a".$workarray[1]])
	{
	    if ($workarray[3]==1){
	    	$workarray[3] = $lang['cs_cointsGame_1'];
	    }
	    else{
			$workarray[3] = $lang['cs_cointsGame_2'];
	    }
//	    $workarray[5] = _root.TimeToString($workarray[5], true);
	} // end if
	if ($adv_history8["a".$workarray[1]])
	{
	    if ($workarray[3]==1){
	    	$workarray[3] = $lang['cs_cointsGame_1'];
	    }
	    else{
			$workarray[3] = $lang['cs_cointsGame_2'];
	    }
	} // end if
	if ($adv_history9["a".$workarray[1]])
	{
	    $workarray[2] = $lang["cs_floor_4_".$workarray[2]];
	    if ($workarray[6]==1){
	    	$workarray[6] = $lang['cs_cointsGame_1'];
	    }
	    else{
			$workarray[6] = $lang['cs_cointsGame_2'];
	    }
	} // end if
	if ($adv_history10["a".$workarray[1]])
	{
	    $workarray[2] = $lang["cs_floor_6_".$workarray[2]];
	    if ($workarray[6]==1){
	    	$workarray[6] = $lang['cs_cointsGame_1'];
	    }
	    else{
			$workarray[6] = $lang['cs_cointsGame_2'];
	    }
	} // end if
	if ($adv_history11["a".$workarray[1]])
	{
	    $workarray[$adv_history11["a".$workarray[1]]] = $lang["man_".$workarray[$adv_history11["a".$workarray[1]]]];
	} // end if
	$datetime=array_shift($workarray);
	$lognumber=array_shift($workarray);
	$retstr= $datetime." ".vsprintf($lang["log".$lognumber],$workarray);
	return $retstr;
}

$historyfile = stripslashes($_POST['history']);
$logfile=@fopen("/home/sites/police/www/otherhistory/$historyfile.txt","r");
$history = "";
if($logfile) {
	while(!feof($logfile))	{
		$myworkstr=fgets($logfile);
		$mylogparsedstr=parseLine(rtrim($myworkstr,"\r\n"));
		$history .= $mylogparsedstr."\n";
	}
}

$history = ($_POST['history2'])?stripslashes($_POST['history2']):$history;

echo "
<H3>Анализатор логов каторжника</H3>
<form method=POST action='?act=aprisone' name='F2' id='sendlog' target='_blank'>
<table style='width: 100%; margin-bottom: 15px; border-bottom: 1px solid black; margin-top: 15px; font-size: 12px;' cellpadding=3 cellspacing=1 border=0>
<tr>
<td style='width: 50%;' align=center>
	<select name=showtype id='showtype' style='width: 400px;'>
		<option value='1'>Показывать развёрнутую инфу по подозрительным логам</option>
		<option value='2'>Показывать развёрнутую инфу по ВСЕМ логам</option>
	</select>
</td>
<td style='width: 49%;' align=center>
	<input name=suspicioussize id=suspicioussize type=text value=41 style='width: 35px;'> Какое кол-во ресов за бой считать подозрительным
</td>
<td align=center id='loadfil'>&nbsp;</td>
</tr>
<tr>
<th colspan=3>
	<textarea name='history2' id='ahistory' style='width: 100%; height: 250px; background: #deceb4; color: black;'>$history</textarea>
</th>
</tr>
<tr>
<th colspan=3>
	<input type='hidden' name='go' value='1'>
	<input type=button value='Анализировать логи' onClick='analyzeit(); return false;'>
	<input type=button value='Очистить всё' onClick='clearit(); return false;'>
</th>
</tr>
</table>
</form>
<div id='analyze'>Введите логи в поле выше</div>
<script>

var go = ".ceil($_POST['go']).";
";

echo <<<HTML
var R = {'Metals':'metals','Silicon':'silicon','Precious':'precious','Radioactive':'radic','Organic':'organic','Venom':'venom','Gems':'gems','Polymers':'polymers'};
var R2 = new Array('Металл','Кремний','Золото','Радики','Орга','Веном','Гемы','Полики');

//Строка, день, время, данные, данные 2 ...
var templates = new Array();
templates[0] = /Отданы предметы: ([^\\r]+) в здании ([^\\r]+)\./ig;
templates[1] = /Выброшено: ([^\\r]+) в локации ([^\\r]+)\./ig;

templates[2] = /Подобрал: ([^\\r]+) в бою (\d+)/ig;
templates[3] = /Выброшено: ([^\\r]+) в бою (\d+)/ig;

function clearit() {	document.getElementById('analyze').innerHTML = "Введите логи в поле выше";
    document.getElementById('loadfil').innerHTML = "!";
    document.getElementById('ahistory').value = "";

}

function parseline(line) {	
	line = line.replace(/\{|\}|'/g,""); //'!
	line = line.replace(/Precious metals/g,"Precious");
	line = line.replace(/Radioactive materials/g,"Radioactive");
	line = line.replace(/(\d{2}\.\d{2}.\d{2}) (\d{2}:\d{2}) /g,"");
	line = line.replace(/~(\d{11}\.\d{1})~/g,"");
	line = line.replace(/(&amp;)/g,"&");
	line = line.replace(/, /g,";");
	line = line.replace(/,/g,";");
	line = line.replace(/\[/g,",");
	line = line.replace(/\]/g,"");

	line = line.replace(/Подобрал: /g,"+|");
    line = line.replace(/Выброшено: /g,"-|");
    line = line.replace(/Отданы предметы: /g,">|");
    line = line.replace(/ в бою /g,"|b|");
    line = line.replace(/ в здании /g,"|");
    line = line.replace(/ в локации /g,"|");
    line = line.replace(/\./g,"");
    var tmp = line.split('|');

    var out = new Array();
    var n = -1;
    var place = 0;


    if(tmp[2] == 'b') {
	    n = (tmp[0] == '+')?2:3;
	    place = tmp[3];
	} else {		place = tmp[2];		if(tmp[0] == '>') {
        	n = 0;
	    } else if(tmp[0] == '-') {	    	n = 1;
	    }
	}

	out[0] = new Array(tmp[1],place);
	out[1] = n;
	out[2] = line;
return out;
}

function parseitems(items) {	items = items.split(';');
	var Out = {'Metals':0,'Silicon':0,'Precious':0,'Radioactive':0,'Organic':0,'Venom':0,'Gems':0,'Polymers':0,'all':0};
	for(var i in items) {		var item = items[i];
		var tmp = item.split(',');
		if(R[tmp[0]]) {			Out[tmp[0]]	= Math.ceil(Out[tmp[0]])+Math.ceil(tmp[1]);
			Out['all'] = Math.ceil(Out['all'])+Math.ceil(tmp[1]);
		}


	}
return Out;
}

function template(data,battle,susp) {	var color = (susp)?'red':'blue';	var html = "<table style='width: 100%; font-size: 12px; margin-bottom: 5px;' border=1 cellpadding=3 cellspacing=2>"+
	"<tr><td colspan=9><b style='color: "+color+";'>"+battle+"</b></td></tr>"+
	"<tr><tr>";
	for(var res in R) {
		html += "<th><img src='http://stalkerz.ru/i/battlemod/"+R[res]+".gif' border=0></th>";
	}
	html += "<th>Всего</th><tr><tr>";
	for(var res in data['total']) {
		html += "<th>"+data['total'][res]+"</th>";
	}
	html += "</tr></table>";

return html;
}
function template2(data) {
	var html = "<table style='width: 100%; font-size: 12px; margin-bottom: 5px;' border=1 cellpadding=3 cellspacing=2>"+
	"<tr><th><b style='color: green;'>Общий итог</b></th>";
	for(var res in R) {
		html += "<th><img src='http://stalkerz.ru/i/battlemod/"+R[res]+".gif' border=0></th>";
	}
	html += "<th>Всего</th></tr><tr><th>Собрано</th>";
	for(var res in data['total']) {
		html += "<th>"+data['total'][res]+"</th>";
	}
	html += "</tr><tr><th>Сдано</th>";
	for(var res in data['over']) {
		html += "<th>"+data['over'][res]+"</th>";
	}
	html += "</tr><tr><th>Выброшено</th>";
	for(var res in data['drop']) {
		html += "<th>"+data['drop'][res]+"</th>";
	}
	html += "</tr>";
	html += "<tr><td colspan=10>Собрано ресов в боях: <b>1-20 ресов - "+data['stat'][1]+" боёв</b>, <b>20-40 ресов - "+data['stat'][2]+" боёв</b>, <b>более 40 ресов - "+data['stat'][3]+" боёв</b></td></tr>";
	html += "</table>";

return html;
}

function analyzeit() {	
	
	var text = document.getElementById('ahistory').value;
    document.getElementById('analyze').innerHTML = "...";
    document.getElementById('loadfil').innerHTML = "<img src='http://stalkerz.ru/i/battlemod/load.gif'>";
    var suspicious = (document.getElementById('suspicioussize').value > 0)?document.getElementById('suspicioussize').value:41;
    var showtype = document.getElementById('showtype').value;


	if(!text) {		alert('Я не умею анализировать пустоту :mad:');
		document.getElementById('loadfil').innerHTML = "<img src='http://stalkerz.ru/i/battlemod/x.gif'>";
		return;
	}
	//Для коррекции подсчёта.
	text += "\\n00.00.00 00:00 Подобрал: Завершалко[1] в бою '100500'";

	var lines = text.split('\\n');
	var Battles = {
		'all':new Array(),
		'suspicious':new Array(),
		'show':new Array()
	};
    var battle = false;
    var ResInBtl = {    	'2':{'Metals':0,'Silicon':0,'Precious':0,'Radioactive':0,'Organic':0,'Venom':0,'Gems':0,'Polymers':0,'all':0},
    	'3':{'Metals':0,'Silicon':0,'Precious':0,'Radioactive':0,'Organic':0,'Venom':0,'Gems':0,'Polymers':0,'all':0},
    	'total':{'Metals':0,'Silicon':0,'Precious':0,'Radioactive':0,'Organic':0,'Venom':0,'Gems':0,'Polymers':0,'all':0}
    };
    var Collected = {
		'total':{'Metals':0,'Silicon':0,'Precious':0,'Radioactive':0,'Organic':0,'Venom':0,'Gems':0,'Polymers':0,'all':0},
		'over':{'Metals':0,'Silicon':0,'Precious':0,'Radioactive':0,'Organic':0,'Venom':0,'Gems':0,'Polymers':0,'all':0},
		'drop':{'Metals':0,'Silicon':0,'Precious':0,'Radioactive':0,'Organic':0,'Venom':0,'Gems':0,'Polymers':0,'all':0},
		'suspicious':0,
		'stat':new Array(0,0,0,0,0)
	};

	for(var i in lines) {		
		
		var line = lines[i];
		if(!line) continue;

		var data = parseline(line);
		var TPL = data[1];
		var D = data[0];

        //document.getElementById('analyze').innerHTML = document.getElementById('analyze').innerHTML+'<br><br>Найден шаблон '+TPL+' | Данные: <b>'+data[2]+'</b><br>';

		switch(TPL) {			//отданы предметы
			case 0:
				var Items = parseitems(D[0]);
				for(var res in Items) {
					Collected['over'][res] = Math.ceil(Collected['over'][res])+Math.ceil(Items[res]);
				}
				//document.getElementById('analyze').innerHTML = document.getElementById('analyze').innerHTML+"<hr>"+D[0]+">"+D[1];

			break;
			//выброшено в локе
			case 1:
				var Items = parseitems(D[0]);
				for(var res in Items) {
					Collected['drop'][res] = Math.ceil(Collected['drop'][res])+Math.ceil(Items[res]);
				}
				//document.getElementById('analyze').innerHTML = document.getElementById('analyze').innerHTML+"<hr>"+D[0]+">"+D[1];

			break;
			//подобрал в бою(2) и выброшено в бою(3)
			case 2: case 3:



			if(!battle) {				battle = D[1];
				Battles['all'].push(battle);
			}

            //следующий бой
			if(battle != D[1]) {				for(var res in ResInBtl['total']) {                	ResInBtl['total'][res] = Math.ceil(ResInBtl['2'][res])-Math.ceil(ResInBtl['3'][res]);
                	Collected['total'][res] = Math.ceil(Collected['total'][res])+Math.ceil(ResInBtl['total'][res]);
                }
                var susp = (ResInBtl['total']['all'] >= suspicious)?true:false;
                var show = (susp || showtype == 2)?true:false;
                var total = ResInBtl['total']['all'];

				if(total < 1) {
					Collected['stat'][0]++;
				}
				if(total > 0 && total < 21) {					Collected['stat'][1]++;
				}
				if(total > 20 && total < 41) {
					Collected['stat'][2]++;
				}
				if(total > 40) {
					Collected['stat'][3]++;
				}

                if(susp) {					Collected['suspicious'] = Math.ceil(Collected['suspicious'])+Math.ceil(ResInBtl['total']['all']);
					Battles['suspicious'].push(battle);
                }

                if(show) Battles['show'].push(template(ResInBtl,battle,susp));

				//clear for next
			    ResInBtl = {
			    	'2':{'Metals':0,'Silicon':0,'Precious':0,'Radioactive':0,'Organic':0,'Venom':0,'Gems':0,'Polymers':0,'all':0},
			    	'3':{'Metals':0,'Silicon':0,'Precious':0,'Radioactive':0,'Organic':0,'Venom':0,'Gems':0,'Polymers':0,'all':0},
			    	'total':{'Metals':0,'Silicon':0,'Precious':0,'Radioactive':0,'Organic':0,'Venom':0,'Gems':0,'Polymers':0,'all':0}
			    };
			    battle = D[1];
			    Battles['all'].push(battle);
			}

			var Items = parseitems(D[0]);
			for(var res in Items) {
				ResInBtl[TPL][res] = Math.ceil(ResInBtl[TPL][res])+Math.ceil(Items[res]);
			}
			break;
		}
	}

    var html = template2(Collected);
    html += "<hr>";

	for(var line in Battles['show']) {    	html += Battles['show'][line];
	}

	document.getElementById('analyze').innerHTML = html;
	document.getElementById('loadfil').innerHTML = "<img src='http://stalkerz.ru/i/battlemod/v.gif'>";
}


if(go > 0) analyzeit();
</script>
HTML;


?>