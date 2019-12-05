<?php
	include "/home/sites/police/www/_modules/mysql.php";
	#error_reporting(0);

	$limit = (date('H', time())>4 && date('H', time())<7)?1500:1000;

	$limit = ($_GET['l'] > 0)?$_GET['l']:$limit;

	$SQL = 'SELECT `id`, `LogID`, `status`, `InsertBy`, `nick` FROM `import_info` WHERE `status`<2 ORDER BY `status` DESC, `doitnow` DESC, `id` ASC LIMIT '.$limit.'';
	$res = mysql_query($SQL);
	$tocheck = 0;

	while (list($lid, $BattleID, $status, $InsertBy) = mysql_fetch_array($res)) {
        echo "LOAD: $BattleID > $status($InsertBy)<br>\n\n\n";
		$SQL = 'UPDATE `import_info` SET `status`=1 WHERE `id`=\''.$lid.'\'';
		mysql_query($SQL);
		echo "$BattleID load > \n\n";
		$Content = GetBattle($BattleID);
        //echo "$Content\n\n";
		
		if (strpos($Content, "err=-12")) {
			echo "battle no exists<br>\n";
			$SQL = "UPDATE import_info SET status=3 WHERE id='$lid'";
			mysql_query($SQL);
		} elseif (!empty($Content)) {
			echo "incoming data<br>\n";
			$Content = preg_replace("/\n/", "", $Content);
			$TempInfo = ParseBattle($Content,$InsertBy);
			if ($TempInfo[0]>0) {
				$SQL = 'UPDATE `import_info` SET `status`=2 WHERE `id`=\''.$lid.'\'';
			} else {
				$SQL = 'UPDATE `import_info` SET `status`=0 WHERE `id`=\''.$lid.'\'';
			}
			mysql_query($SQL);
		} else {
			echo "fail! no data<br>\n";
		}
	}

function GetBattle($ID) {
	$sock = fsockopen("city2.timezero.ru", 80, $errno, $errstr, 10);
	if ($sock) {
		fputs($sock, "GET /getbattle?id=".$ID." HTTP/1.0\r\n");
		fputs($sock, "Host: city1.timezero.ru\r\n");
		fputs($sock, "Content-type: application/x-www-url-encoded \r\nn");
		fputs($sock, "Connection: Keep-Alive\r\n");
		fputs($sock, "\r\n\r\n");
		$tmp_headers = "";
		$tmp_body = "";
		while (!feof($sock)) {
			$tmp_body .= fgets($sock, 4096);
		}
		#echo "R: $tmp_body";
		return $tmp_body;
		return 0;
	} else {
		return 0;
	}
}

//======= ParseBattle ========
	function ParseBattle($battle, $InsertBy,$violator) {
		unset($GLOBALS);
		$cz = array ('<' , "\n");
		$ncz = array ('&lt;',"<BR>");
		$battle = UTF8toCP1251($battle);
		$battle=str_replace('<','        <',$battle);
		$find='<BATTLE';
		$cposn=strpos($battle,$find);
		if ($cposn) {
			$find='</BATTLE>';
			$cposc=strpos($battle,$find);
			$battlehead =substr($battle,$cposn,$cposc-$cposn);
			$note=pv('note="',$battlehead,50);
			list($x,$y,$GLOBALS['time'])=explode(",",$note);
			$GLOBALS['coordinate']='['.$x.'/'.$y.']';
			while(strpos($battlehead,'<USER')) {
				$find='<USER';
				$cposn=strpos($battlehead,$find);

				$find='</USER>';
				$cposc=strpos($battlehead,$find);

				$curuser =substr($battlehead,$cposn,$cposc-$cposn);
				$battlehead =substr($battlehead,$cposc+strlen($find),strlen($battlehead));

				getuser($curuser,0);
			}

			while(strpos($battle,'<TURN')) {
				$find='<TURN';
				$cposn=strpos($battle,$find);

				$find='</TURN>';
				$cposc=strpos($battle,$find);

				$curturn = substr($battle,$cposn,$cposc-$cposn);
				$battle = substr($battle,$cposc+strlen($find),strlen($battle));
			//	echo str_replace($cz,$ncz,$curturn)."<BR><BR>";
				$id=@count($GLOBALS['turns']);
				$turn=pv('turn="',$curturn,5);
				$time=pv('time="',$curturn,20);

				$GLOBALS['turns'][$id]['turn']=$turn;
				$GLOBALS['turns'][$id]['time']=$time;
				while(strpos($curturn,"<USER")) {
					$find='<USER';
					$cposn=strpos($curturn,$find);

					$find='</USER>';
					$cposc=strpos($curturn,$find);
					$curuser =substr($curturn,$cposn,$cposc-$cposn);
					$curturn =substr($curturn,$cposc+strlen($find),strlen($curturn));
					if (strpos($curuser, 'login="') &&  strpos($curuser, 'level="')) {
						getuser($curuser,$turn);
					} else {
						getuseraction($curuser,$turn);
					}
				}
			}
			// $cheet=0;foreach($GLOBALS['group'] as $var=>$value) {if ($value != $GLOBALS['groupsdead'][$var]) {$cheet++;}}

			$BattleDate = date("Y-m-d",$GLOBALS['time']);
			$BattleTime = date("H:i:s",$GLOBALS['time']);
			$BattleID = $GLOBALS['curbid'];
			print_r($GLOBALS['users']['countpers']);
			if ($GLOBALS['users']['countpers']>=2) {
				$Location = $x.','.$y;
				unset($keylinemass);
			//	var $keylinemass = new Array();
				$deadls=0;
				$servivels=0;
				$userline='';
				$commondmg=0;
                $ranksInBtl=0;
				foreach ($GLOBALS['users'] as $var=>$value) {

					$ranksData = "";
					if ($var[0]!=='$' && $var!=='countmonstr' && $var!=='countpers') {
						echo ">>> ".count($value[ranks])."<br>";
						if(count($value[ranks]) > 1) {
							$ranks_end = max($value[ranks]);
							$ranks_start = min($value[ranks]);
							$battleranks = $ranks_end-$ranks_start;
							$getranks = ($value['dead'] == 0)?$battleranks*2:$battleranks;
                            if($violator == $violator) {
                            	$ranksInBtl++;
                            	$vranks = $getranks;
                            }
							foreach($value[ranks] as $turn => $v) {
								if($turn == 0) continue;
								$getr = $v-$ranks_start;
								$killed = (count($value[kills][$turn]) > 1)?implode("/",$value[kills][$turn]):$value[kills][$turn][0];
								$ranksData .= "$turn:$getr:$killed|";
							}
	                        $ranksData = rtrim($ranksData,"|")."&$getranks";

	                    }
						$keylinemass[] =$var;
					}

					if ($value['dead']==1 && $var[0]!=='$' && $var!=='countmonstr' && $var!=='countpers') {
						$deadls +=$value['level'];

						$userline .=$var.','.$value['level'].','.$value['group'].','.$value['turn'].','.$value['damage'].',0,'.$value['clan'].','.$value['HP'].','.$ranksData.';';
					}

					if ($value['dead']==0 && $var[0]!=='$' && $var!=='countmonstr' && $var!=='countpers') {

						$servivels +=$value['level'];
						$userline .=$var.','.$value['level'].','.$value['group'].','.$value['turn'].','.$value['damage'].',1,'.$value['clan'].','.$value['HP'].','.$ranksData.';';
						$commondmg += $value['damage'];
					}
				}

				if (count($keylinemass)>1) {
					sort($keylinemass);
				}

				foreach ($keylinemass as $value) {
					$keyline .=$value;
				}

				$k=($deadls && $servivels)?(($deadls-$servivels)/($deadls+$servivels)):0;
				$curcheat = ($commondmg > 0)?0:1;
                $ranksInBtl = ($ranksInBtl > 0)?1:0;
				$query = 'INSERT INTO `battles` VALUES (\'\', \''.$BattleDate.'\', \''.$BattleTime.'\', \''.$BattleID.'\', \''.md5($keyline).'\', \''.mysql_escape_string(trim($userline)).'\', \''.$Location.'\', '.$k.', \''.$curcheat.'\', \''.$InsertBy.'\',\''.$ranksInBtl.'\', \''.$vranks.'\', NOW());';
				mysql_query($query);

				$written_1 = 1;

//echo($query."<br>");

				$LocalID = mysql_insert_id();
				foreach ($GLOBALS['users'] as $var=>$value) {
					if($var[0]!=='$' && $var!=='countmonstr' && $var!=='countpers') {
						if(count($value[ranks]) > 1) {
							$ranks_end = max($value[ranks]);
							$ranks_start = min($value[ranks]);
							$battleranks = $ranks_end-$ranks_start;
							$getranks = ($value['dead'] == 0)?$battleranks*2:$battleranks;
						} else {
							$getranks = 0;
						}
						$query = 'INSERT INTO `battle_logins` VALUES (\'\', \''.$LocalID.'\', \''.mysql_escape_string(trim($var)).'\', \''.$value['level'].'\',\''.$getranks.'\')';
					//echo($query."<br>");
						mysql_query($query);
					}
				}
			}

			return array($BattleID, $BattleID, $BattleDate);
		} else {
			return 0;
		}
	}

//======= getuseraction ========
	function getuseraction($curuser,$turn) {
		$cz = array ('<' , "\n");
		$ncz = array ('&lt;',"<BR>");
	//echo str_replace($cz,$ncz,$curuser)."<BR><BR>";

		$find='<USER';
		$cposn=strpos($curuser,$find)+strlen($find);

		$find='>';
		$cposc=strpos($curuser,$find);
		$userhead=substr($curuser,$cposn,$cposc-$cposn);
		$curuser=substr($curuser,$cposc+1,strlen($curuser));
	//echo str_replace($cz,$ncz,$curuser)."<BR><BR>";

		$userlogin=pv('login="',$userhead,60);
		$ranks=pv('rank_points="',$userhead,60);
		if($ranks) $GLOBALS['users'][$userlogin]['ranks'][$turn]=$ranks;
		$GLOBALS['users'][$userlogin]['score'] =pv('score="',$userhead,15);
		if (strpos($userhead,'HP="0"')) {
			$GLOBALS['users'][$userlogin]['dead']=1;
			($GLOBALS['users'][$userlogin]['group'])?$GLOBALS['groupsdead'][$GLOBALS['users'][$userlogin]['group']]++:$k;
			$GLOBALS['users'][$userlogin]['deadturn']=$turn;
			$GLOBALS['users'][$userlogin]['brokenslots']=pv('brokenslots="',$userhead,10);
		}

		if (strpos($userhead,'dpsy="') && strpos($userhead,'dpsy="0"')) {
			$GLOBALS['users'][$userlogin]['dpsy'][$turn]=pv('dpsy="',$userhead,60);
		}

		$str=explode("<",$curuser);
	//echo $userlogin."<BR>";
		foreach($str as $value) {
		//echo str_replace($cz,$ncz,$value)."<BR>";
			$logint=pv('login="',$value,60);
			if($logint[0]!=='$' && $logint!=='countmonstr' && $logint!=='countpers') {
				if (strpos($value, 'type="') && !strpos($value, 'p="') && strpos($value, 'login="') && strpos($value, 'HP="') && $GLOBALS['users'][$userlogin]['monstr']==0 && $logint !=$userlogin) {
					$damage=pv('HP="',$value,15);
				//	echo $damage."<BR>";
					if (strpos($damage,",")) {
						$dam='';
						$dam= explode(",",$damage);

						foreach($dam as $value1) {
							if (strpos($value1,":")) {
								$dam1='';
								$dam1= explode(":",$value1);
								$GLOBALS['users'][$userlogin]['damage'] +=$dam1[1];
							}
						}
					}

					if (strpos($damage,":")) {
						$dam='';
						$dam= explode(":",$damage);
						$GLOBALS['users'][$userlogin]['damage'] +=$dam[1];
					}
				//	echo str_replace($cz,$ncz,$value)."<BR>";
				}
                if (strpos($value, 't="19') || strpos($value, 't="20')) {
					$who = pv('login="',$value,60);
					if($who) $GLOBALS['users'][$userlogin]['kills'][$turn][]=$who;
				}
				if (strpos($value, 'type="3') && strpos($value, 'p="') && strpos($value, 'login="') && strpos($value, 'HP="') && $logint !=$userlogin && @$GLOBALS['users'][$logint]['group'] != $GLOBALS['users'][$userlogin]['group']) {
					$psy=explode(".",pv('" p="',$value,10));
					$damage=pv('HP="',$value,15);
				//	echo $damage."<BR>";
					if (strpos($damage,",")) {
						$dam='';
						$dam= explode(",",$damage);

						foreach($dam as $value1) {
							if (strpos($value1,":")) {
								$dam1='';
								$dam1= explode(":",$value1);
								$GLOBALS['users'][$userlogin]['damage'] +=abs($dam1[1]);
							}
						}
						$damage='';
					}

					if (strpos($damage,":")) {
						$dam='';
						$dam= explode(":",$damage);
						if ($psy[0]==200 || $psy[0]==236 || $psy[0]==232 || $psy[0]==241 || $psy[0]==239 || $psy[0]==234 || $psy[0]==235) {
							$GLOBALS['users'][$userlogin]['damage'] +=abs($dam[1]);
						}
					}
				}
			}
		}
	}


//======= getuser ========
	function getuser($curuser,$turn) {
		$cz = array ("<" , "\n");
		$ncz = array ("&lt;","<BR>");
	//	echo str_replace($cz,$ncz,$curuser)."<BR><BR>";
		$id=@count($GLOBALS['users']);

		$userlogin=pv('login="',$curuser,60);
		if ($userlogin[0]=='$') {
			$GLOBALS['users'][$userlogin]['monstr']=1;
			$GLOBALS['users']['countmonstr']++;
		} else {
			$ranks=pv('rank_points="',$curuser,60);
			if($ranks) $GLOBALS['users'][$userlogin]['ranks'][$turn]=$ranks;
			$GLOBALS['users'][$userlogin]['monstr']=0;
			$GLOBALS['users']['countpers']++;
		}
		if (!$GLOBALS['curbid']) {
			$GLOBALS['curbid']=pv('battleid="',$curuser,15);
		}
		$GLOBALS['users'][$userlogin]['level']=pv('level="',$curuser,60);
		$GLOBALS['users'][$userlogin]['str']=pv('str="',$curuser,60);
		$GLOBALS['users'][$userlogin]['dex']=pv('dex="',$curuser,60);
        $GLOBALS['users'][$userlogin]['int']=pv('int="',$curuser,60);
        $GLOBALS['users'][$userlogin]['pow']=pv('pow="',$curuser,60);
        $GLOBALS['users'][$userlogin]['HP']=pv('HP="',$curuser,60);
        $GLOBALS['users'][$userlogin]['psy']=pv('psy="',$curuser,60);
        $GLOBALS['users'][$userlogin]['clan']=pv('clan="',$curuser,60);
        $GLOBALS['users'][$userlogin]['man']=pv('man="',$curuser,60);
        $GLOBALS['users'][$userlogin]['pro']=pv('pro="',$curuser,60);
        $GLOBALS['users'][$userlogin]['propwr']=pv('propwr="',$curuser,60);
        $GLOBALS['users'][$userlogin]['group']=pv('group="',$curuser,60);
        ($GLOBALS['users'][$userlogin]['group'])?$GLOBALS['group'][$GLOBALS['users'][$userlogin]['group']] ++:$k;
        $GLOBALS['users'][$userlogin]['score']=0;
        $GLOBALS['users'][$userlogin]['turn']=$turn;
        $GLOBALS['users'][$userlogin]['damage']=0;
        $GLOBALS['users'][$userlogin]['dead']=0;
        $GLOBALS['users'][$userlogin]['deadturn']='';
        $GLOBALS['users'][$userlogin]['dpsy'][$turn]='';
        $wid=@count($GLOBALS['curuserweapon'][$userlogin]);
        if (strpos($curuser,'slot="GH"')) {
			$GLOBALS['curuserweapon'][$userlogin][$wid]=pv('slot="GH" name="',$curuser,7);
		}
		if (strpos($curuser,'slot="G"')) {
			$GLOBALS['curuserweapon'][$userlogin][$wid]=pv('slot="G" name="',$curuser,7);
		}
		if (strpos($curuser,'slot="H"')) {
			$GLOBALS['curuserweapon'][$userlogin][$wid]=pv('slot="H" name="',$curuser,7);
		}
	}

	function pv($find,$str,$len) {
		$cposn=strpos($str,$find);
		if ($cposn) {
			$cposn +=strlen($find);
			$vper=substr($str,$cposn,$len);
			$cposc=strpos($vper,'"');
			$value=substr($vper,0,$cposc);
			return $value;
		}
	}

	function UTF8toCP1251($str){
		static $table = array("\xD0\x81" => "\xA8", // ¨
                        "\xD1\x91" => "\xB8", // ¸
		);
		return preg_replace('#([\xD0-\xD1])([\x80-\xBF])#se', 'isset($table["$0"]) ? $table["$0"] : chr(ord("$2")+("$1" == "\xD0" ? 0x30 : 0x70))', $str);
	}
	echo "END FILE.";
?>