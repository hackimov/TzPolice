<?php
require_once("/home/sites/police/www/_modules/functions.php");
require_once("/home/sites/police/www/_modules/auth.php");
error_reporting(E_ERROR | E_WARNING | E_PARSE);
include('/home/sites/police/www/otherhistory/lang_ru.php');
setlocale(LC_CTYPE,"ru_RU.CP1251");
function cmp($a,$b)
{
	if (filemtime($a)==filemtime($b))
	{
		return 0;
	}
	return (filemtime($a)>filemtime($b))?-1:1;
}
function list_histories($admin_access=0,$dept_list) {

    if (strpos($_SERVER['QUERY_STRING'],"longlogs")===false){print "<script language=\"javascript\">function sh(lid){if(document.getElementById(lid).style.display=='none'){document.getElementById(lid).style.display='';}else{document.getElementById(lid).style.display='none';}}</script>";}
	chdir('/home/sites/police/www/otherhistory');
	if ($admin_access==1) {
		$listhistories=glob("\[{".$dept_list."}\]*.txt",GLOB_BRACE);
	} elseif($admin_access==0) {
		$listhistories=glob("\[".AuthUserName."\]*.txt");
	} else {
		$listhistories=glob("*.txt");
	}

	if ($listhistories)	{
		usort($listhistories,"cmp");
		$userfiles=array();
		foreach($listhistories as $logno=>$hist) {
			preg_match("/\[(.+)\] \[(.+)\] (\d+\.\d+\.\d+) (\d+\.\d+\.\d+)\s?(\d?)\.txt/",$hist,$matches);
			if (!array_key_exists($matches[1],$userfiles)) {
				$userfiles[$matches[1]]=array();
			}
			array_push($userfiles[$matches[1]],$hist);
		}

		if ($userfiles) {

			arsort($userfiles,SORT_STRING);
			if (array_key_exists(AuthUserName,$userfiles)) {
				foreach($userfiles[AuthUserName] as $logno=>$logname) {
					preg_match("/\[(.+)\] \[(.+)\] (\d+\.\d+\.\d+) (\d+\.\d+\.\d+)\s?(\d?)\.txt/",$logname,$matches);
					print "[<a href='/otherhistory/download.php?logno=".AuthUserName."|".$logno."'>ZIP</a>] <a href=\"".(strpos($_SERVER['QUERY_STRING'],"longlogs")!==false?"?act=longlogs&":"?")."logno=".AuthUserName."|"."$logno\">История ".$matches[2]." за период с ".$matches[3]." по ".$matches[4]."</a>";

					print "<br />";
				}
			}
        	foreach ($userfiles as $username=>$logfiles)
			{
				if ($username==AuthUserName || strlen($username)<2)
				{
					continue;
				}
				if ($admin_access)
				{
					print "<a href=\"javascript:{}\" onClick=\"javascript:sh('usr".$username."')\">Логи, заказанные $username</a><div id=\"usr".$username."\" style=\"display:none\">";
				}
				foreach($logfiles as $logno=>$logname)
				{
					preg_match("/\[(.+)\] \[(.+)\] (\d+\.\d+\.\d+) (\d+\.\d+\.\d+)\s?(\d?)\.txt/",$logname,$matches);
					print "[<a href='/otherhistory/download.php?logno=".$username."|".$logno."'>ZIP</a>] <a href=\"".(strpos($_SERVER['QUERY_STRING'],"longlogs")!==false?"?act=longlogs&":"?")."logno=$username|$logno\">Просмотр заказан ".$matches[1].", история ".$matches[2]." за период с ".$matches[3]." по ".$matches[4]."</a><br />";
				}
				if ($admin_access)
				{
					print "</div><br />";
				}
			}
		}
	}
	else
	{
		if ($admin_access==0){
			print "Вы не заказывали просмотр!";
		}
		elseif ($admin_access==1)
		{
			print "Нет логов вашего отдела!";
		}
		else
		{
			print "Логи отсутствуют.";
		}
	}
}
function parseLine($workstr)
{
    global $lang;
	$workarray=explode("\t",$workstr);

	switch($workarray[1])
	{
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
function ParseLogFile($admin_access,$dept_list,$logcount,$logfilter)
{
	if ($admin_access==1)
	{
		$listhistories=glob("\[{".$dept_list."}\]*.txt",GLOB_BRACE);
	}
	elseif($admin_access==0)
	{
		$listhistories=glob("\[".AuthUserName."\]*.txt");
	}
	else
	{
		$listhistories=glob("*.txt");
	}
	usort($listhistories,"cmp");
	$userfiles=array();
	foreach($listhistories as $logno=>$hist)
	{
		preg_match("/\[(.+)\] \[(.+)\] (\d+\.\d+\.\d+) (\d+\.\d+\.\d+)\s?(\d?)\.txt/",$hist,$matches);
		if (!array_key_exists($matches[1],$userfiles))
		{
			$userfiles[$matches[1]]=array();
		}
		array_push($userfiles[$matches[1]],$hist);
	}
	arsort($userfiles,SORT_STRING);
	list($username,$logno)=explode('|',$logcount);
	$logfile=@fopen($userfiles[$username][$logno],"r");
	preg_match("/\[(.+)\] \[(.+)\] (\d+\.\d+\.\d+) (\d+\.\d+\.\d+)\s?(\d?)\.txt/",$userfiles[$username][$logno],$matches);
	print "История персонажа $matches[2] c $matches[3] по $matches[4].<hr />\n";
	echo ("<script>document.getElementById('lognick').value='".$matches[2]."';</script>");
	if ($logfile)
	{
		echo ('<div id="logf">');
		while(!feof($logfile))
			{
				$myworkstr=fgets($logfile);
				$mylogparsedstr=parseLine(rtrim($myworkstr,"\r\n"));
				if ($logfilter<>"")
					{
						$logfilterpieces=explode(", ",$logfilter);
						for ($i=0;$i<count($logfilterpieces);$i++){
							if (stripos($mylogparsedstr,$logfilterpieces[$i])!==FALSE)
							{
								echo $mylogparsedstr."<br />";
							}
					    }
					}
				else
					{
						echo $mylogparsedstr."<br />";
					}
			}
		echo ('</div>');
	}
}

if(AuthUserClan=='Tribunal' || AuthUserClan=='police' || AuthUserClan=='Police Academy' || AuthUserGroup==100) {
	$sSQL = "SELECT `dept`, `chief` FROM `sd_cops` WHERE `name` = '".AuthUserName."' LIMIT 1;";
	$result = mysql_query($sSQL);
	$row = mysql_fetch_assoc($result);
	$dept_id = $row['dept'];
	$dept_list="";
	if (AuthUserGroup==100) {
			$otherhistory_access=2;
	} else {
		if (!in_array($dept_id,array(12,13,14,15,57,71,27)) && AuthUserGroup!=100 && !(abs(AccessLevel) & AccessOP)){
			print "Shoo!Shoo! Go away, ".AuthUserName."! ";
			exit;
		}
		$otherhistory_access=0;
		$chief = $row['chief'];
		if ($chief==1){
			$otherhistory_access=1;
			$sSQL = 'SELECT name FROM sd_cops WHERE dept='.$dept_id.";";
			$result= mysql_query($sSQL);
			while($row=mysql_fetch_assoc($result)) {
				if($dept_list!=""){					$dept_list.=",";
				};
				$dept_list.=$row['name'];
			}
		}
	}
?>
<script>
var botWin = 0;

function setCookie (name, value, expires, path, domain, secure) {
	document.cookie = name+"="+escape(value)+((expires)?"; expires="+expires:"")+((path)?"; path="+path:"")+((domain)?"; domain="+domain:"")+((secure)?"; secure":"");
}
function getCookie(name) {
	var cookie = " " + document.cookie;
	var search = " " + name + "=";
	var setStr = "";
	var offset = 0;
	var end = 0;
	if (cookie.length > 0) {
		offset = cookie.indexOf(search);
		if (offset != -1) {
			offset += search.length;
			end = cookie.indexOf(";", offset)
			if (end == -1) {
				end = cookie.length;
			}
			setStr = unescape(cookie.substring(offset, end));
		}
	}
	return(setStr);
}

//Заставляем жабаскрипт ставить корректные куки
var trans = [];
for (var i = 0x410; i <= 0x44F; i++) {
	trans[i] = i - 0x350; // А-Яа-я
	trans[0x401] = 0xA8;    // Ё
	trans[0x451] = 0xB8;    // ё
}
var escapeOrig = window.escape;

window.escape = function(str) {
	var ret = [];
	for (var i = 0; i < str.length; i++)  {
		var n = str.charCodeAt(i);
		if(typeof trans[n] != 'undefined') n = trans[n];
		if(n <= 0xFF) ret.push(n);
	}

return escapeOrig(String.fromCharCode.apply(null, ret));
}
window.escape = function(str) {
    var converter = Components.classes['@mozilla.org/intl/texttosuburi;1'].createInstance(Components.interfaces.nsITextToSubURI);

return converter.ConvertAndEscape('windows-1251', str);
}

function botWindow() {
	if(botWin) {
		if(!botWin.closed) botWin.close();
	}
	win_left = 150;
	win_top = 150;
	botWin = open('/direct_call/logs_hang.php', 'bot', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=both,resizable=no,width=350,height=250,scrollbars=1,left='+win_left+', top='+win_top+',screenX='+win_left+',screenY='+win_top);
}
function processlog(){
	var logt=document.getElementById('logf').innerHTML;
	var logt2=logt.replace(/<br>/gi, "\n");
	document.getElementById('logtext').value=logt2;
	document.getElementById('sendlog').submit();
}
</script>
<a href="#; return false;" onClick="botWindow();">Бот завис?</a><br><br>
<?

	list_histories($otherhistory_access,$dept_list);

	if(isset($_REQUEST['logno'])) {
		$logno = $_REQUEST['logno'];
		$logfilter=(isset($_REQUEST['logfilter']))?$_REQUEST['logfilter']:"";
		if ($logfilter=="")	{
			$logfilter2 = (isset($_COOKIE['tzpd_lfc']))?urldecode($_COOKIE['tzpd_lfc']):"";
		} else {
			$logfilter2 = $logfilter;
		}
		print "<form method=POST action=\"".(strpos($_SERVER['QUERY_STRING'],"longlogs")!==false?"?act=longlogs&":"?")."logno=$logno\" name=\"filterform\" onSubmit=\"setCookie('tzpd_lfc',document.getElementById('logfilter').value,'Fri, 01-Jan-2055 00:00:00 GMT','/')\">";
		print "Фильтр (слова через запятую и пробел)<input type=text name=\"logfilter\" value=\"$logfilter\" id=\"logfilter\">";
		print "<input type=submit value=\"Применить фильтр\">";
		print "</form>";
		if(abs(AccessLevel) & AccessOP)	{
			echo "<hr />
			<form method=POST action='?act=prokachki&d=edit' name='F1' id='sendlog' target='_blank'>
			<a href='/otherhistory/download.php?logno=".$_REQUEST['logno']."'>Скачать лог в архиве</a>
            <input type='hidden' name='text' id='logtext'>
            <input type='hidden' name='donow' value='0'>
            <input type='text' name='nickname' id='lognick' VALUE='Ник подозреваемого'>
            <input type=submit value='Отправить в анализатор' onClick='processlog(); return false;'>
			</form>
			<hr />
			";
		}
		ParseLogFile($otherhistory_access,$dept_list,$_REQUEST['logno'],$logfilter);
		print "<hr />";
	}

} else {
	print "Shoo!Shoo! Go away, ".AuthUserName."!";
}

?>