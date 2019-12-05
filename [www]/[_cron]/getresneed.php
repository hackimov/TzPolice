<?php
$GLOBALS['tabfordata'] = 'resneedstats'; // база для записи данных
//$dbconnect=mysql_connect('localhost','login', 'pass'); // соединение с базой
//$rezult=mysql_select_db('base'); //выбор базы
//include("/home/sites/police/www/_modules/mysql.php");
include "/home/sites/police/dbconn/dbconn.php";

$getfile='';$getfile=file("http://www.timezero.ru/res.xml");
if (count($getfile)>0) {
	$GLOBALS['str'] = @implode ('', $getfile);
	$time=pv('time="',$GLOBALS['str'],20);
	$query="SELECT `time` FROM `resprice_archive` ORDER by `time` DESC limit 0,1";
	$rezult=mysql_query($query);
	$row=mysql_fetch_array($rezult);
	setdatatobase($row['time']);
}

function setdatatobase ($last_update) {
	$str=$GLOBALS['str'];
	$cz = array ("<" , "\n");
	$ncz = array ("&lt;" , "<br>");
	$str=str_replace("<S", "_@@_<S",$str);
	//echo str_replace($cz, $ncz, $GLOBALS['str']);
	$citiesmass=explode("_@@_",$str);
	global $statistika;
	$statistika=array();
	global $mean;
	$mean = array();
	$mean['p'][0]=0;
	$mean['p'][1]=0;
	$mean['p'][2]=0;
	$mean['p'][3]=0;
	$mean['p'][4]=0;
	$mean['p'][5]=0;
	$mean['p'][6]=0;
	$mean['p'][7]=0;
	$mean['c'][0]=0;
	$mean['c'][1]=0;
	$mean['c'][2]=0;
	$mean['c'][3]=0;
	$mean['c'][4]=0;
	$mean['c'][5]=0;
	$mean['c'][6]=0;
	$mean['c'][7]=0;		
	for ($i=1;$i<count($citiesmass);$i++) {
	$time=pv('time="',$str,20);
	$shop=pv('shop="',$citiesmass[$i],30);
	$city=pv('city="',$citiesmass[$i],30);
	$xy=pv('xy="',$citiesmass[$i],20);
	unset($resmass);
	$citiesmass[$i]=str_replace("<R", "_@@_<R",$citiesmass[$i]);
	$resmass=explode("_@@_",$citiesmass[$i]);
		$rid=count($statistika);
		$statistika[$rid]['shop']=$shop;
		$statistika[$rid]['xy']=$xy;
		$statistika[$rid]['city']=$city;
		$statistika[$rid]['time']=$time;
		for ($j=1;$j<count($resmass);$j++) {
		$statistika[$rid][$j-1]=pv('need="',$resmass[$j],7);
		$tmp = 'p'.($j-1);
		$statistika[$rid][$tmp]=pv('cost="',$resmass[$j],7);		
		$mean['p'][$j-1]+=pv('cost="',$resmass[$j],7);
		if (pv('cost="',$resmass[$j],7) > 0) $mean['c'][$j-1]++;
		}
	}

	$id=id($GLOBALS['tabfordata']);
	for ($i=0;$i<count($statistika);$i++) {
		$query ="INSERT `".$GLOBALS['tabfordata']."` (`id`,`city`,`shop`,`metals`,`gold`,`polymers`,`organic`,`silicon`,`radioactive`,`gems`,`venom`,`time`) VALUES ($id, '".$statistika[$i]['city']."','".$statistika[$i]['shop']."', ".$statistika[$i][0].", ".$statistika[$i][1].", ".$statistika[$i][2].", ".$statistika[$i][3].", ".$statistika[$i][4].", ".$statistika[$i][5].", ".$statistika[$i][6].", ".$statistika[$i][7].", ".$statistika[$i]['time'].");";
		//echo $query."<BR>";
		$id++;
		$rezult=mysql_query($query);
//		$query ="INSERT `resprice_current` (`city`,`shop`,`xy`,`metals`,`gold`,`polymers`,`organic`,`silicon`,`radioactive`,`gems`,`venom`) VALUES ('".$statistika[$i]['city']."','".$statistika[$i]['shop']."','".$statistika[$i]['xy']."', ".$statistika[$i]['p0'].", ".$statistika[$i]['p1'].", ".$statistika[$i]['p2'].", ".$statistika[$i]['p3'].", ".$statistika[$i]['p4'].", ".$statistika[$i]['p5'].", ".$statistika[$i]['p6'].", ".$statistika[$i]['p7'].");";
		//$query ="UPDATE `resprice_current` SET `xy`='".$statistika[$i]['xy']."',`metals`='".$statistika[$i]['p0']."',`gold`='".$statistika[$i]['p1']."',`polymers`='".$statistika[$i]['p2']."',`organic`='".$statistika[$i]['p3']."',`silicon`='".$statistika[$i]['p4']."',`radioactive`='".$statistika[$i]['p5']."',`gems`='".$statistika[$i]['p6']."',`venom`='".$statistika[$i]['p7']."' WHERE `shop` = '".$statistika[$i]['shop']."' LIMIT 1;";
		$query ="UPDATE `resprice_current` SET `metals`='".$statistika[$i]['p0']."',`gold`='".$statistika[$i]['p1']."',`polymers`='".$statistika[$i]['p2']."',`organic`='".$statistika[$i]['p3']."',`silicon`='".$statistika[$i]['p4']."',`radioactive`='".$statistika[$i]['p5']."',`gems`='".$statistika[$i]['p6']."',`venom`='".$statistika[$i]['p7']."' WHERE `shop` = '".$statistika[$i]['shop']."' LIMIT 1;";
		//echo $query."<BR>";
		mysql_query($query) or die(mysql_error());		
	}
	$tmp = time()-$last_update;
	echo ("Last UPDATE: ".$last_update); 
	if ($tmp > 21300) // 5 hours 55 minutes 
		{
			$mean['m'][0] = $mean['p'][0]/$mean['c'][0];
			$mean['m'][1] = $mean['p'][1]/$mean['c'][1];
			$mean['m'][2] = $mean['p'][2]/$mean['c'][2];
			$mean['m'][3] = $mean['p'][3]/$mean['c'][3];
			$mean['m'][4] = $mean['p'][4]/$mean['c'][4];
			$mean['m'][5] = $mean['p'][5]/$mean['c'][5];
			$mean['m'][6] = $mean['p'][6]/$mean['c'][6];
			$mean['m'][7] = $mean['p'][7]/$mean['c'][7];
			$query ="INSERT INTO `resprice_archive` (`id`,`metals`,`gold`,`polymers`,`organic`,`silicon`,`radioactive`,`gems`,`venom`,`time`) VALUES ('', '".$mean['m'][0]."','".$mean['m'][1]."','".$mean['m'][2]."','".$mean['m'][3]."','".$mean['m'][4]."','".$mean['m'][5]."','".$mean['m'][6]."','".$mean['m'][7]."', '".time()."');";
		//	echo ($query);
			mysql_query($query) or die(mysql_error());	
		}		
}

function id($table){
	$query='select id from '.$table.' order by id desc limit 0,1';
	$rezult=mysql_query($query);
	$row=mysql_fetch_row($rezult);
	return ++$row[0];
}

function pv($pz,$value,$len) {
	$cposn=strpos($value,$pz);
	if ($cposn) {$cposn +=strlen($pz);
		$vper=substr($value,$cposn,$len);$cposc=strpos($vper,'"');
		$value=substr($vper,0,$cposc);
		return $value;
	}
}
?>