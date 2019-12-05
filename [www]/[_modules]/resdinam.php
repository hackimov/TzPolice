<h1>Статистика по скупке ресурсов</h1>
<?php
$tabfordata='resneedstats';
$pathtocalendar = '/calendar.php';
$pathtoimages = '/_imgs/tz';
?>
<style>
.rdtext {font-size:10pt;font-weight:bold;font-family:arial;}
.rdtextl {font-size:10pt;font-family:arial;}
.rhbbr {vertical-align:middle; border-right:1px solid #8aa6a9;border-bottom:1px solid #8aa6a9;font-size:9pt;padding:2px;font-weight:bold;background:#ccc19e}
.rtb {border-left:1px solid #8aa6a9;border-top:1px solid #8aa6a9;font-size:10pt}
.rbbr {border-right:1px solid #8aa6a9;border-bottom:1px solid #8aa6a9;font-size:10pt;font-weight:bold}
</style>


<?
//foreach($_POST as $value=>$var) {echo "$value=>$var<br>";}

	$cshb=$_POST['cshb'];
	$cityf=$_POST['cityf']*2/2;
	$shopf=$_POST['shopf']*2/2;
	$dtype=$_POST['dtype'];

	$query='SELECT DISTINCT shop, city FROM `'.$tabfordata.'` ORDER BY `city`';
	$rezult = mysql_query($query);
	$i = 0;
	while($row = mysql_fetch_assoc($rezult)) {
		//$cityshop[$row['city']] .=($cityshop[$row['city']])?",".$row['shop']:$row['shop'];
		$city[$i] = $row['city'];
		$shop[$i] = $row['shop'].'-'.$row['city'];
		$i++;
	}
	//$shop=(count($shop)>0)?array_unique($shop):$shop;
	$city = (count($city)>0)?array_unique($city):$city;


$script='
<script language="JavaScript">
function showcalendar(per) {
 win=window.open("'.$pathtocalendar.'?form="+per,"calendar","resizeable=yes,scrollbars=no,menubar=no,margins=0,width=165,height=185");
 win.focus();
}
function showhide(shopcity) {
 csform.cityf.value=0;
 document.getElementById("rcity").style.visibility="hidden";
 csform.shopf.value=0;
 document.getElementById("rshop").style.visibility="hidden";
 shopcity.style.visibility="visible";
}
</script>
';

	$i=1;
	foreach($city as $value) {
		$_SESSION['cities'][$i]=$value;
		$formc .= '<option value="'.$i.'" '.(($cityf==$i)?'selected':'').'>'.$value.'</option>';
		$i++;
	}
	
	$prevcity = '';
	$tmp=0;
	$i=1;
	foreach($shop as $value) {
		list($se,$ce)=explode('-',$value);
		if ($ce == $prevcity) {
			$tmp=1;
		} else {
			if ($tmp == 1) {
				$forms .='</optgroup>';
			}
			$prevcity = $ce;
			$forms .= '<optgroup label="'.$ce.'">';
		}
		$_SESSION['shops'][$i]=$se;
		$forms .= '<option value="'.$i.'" '.(($shopf==$i)?'selected':'').'>'.$se.'</option>';
		$i++;
	}
	$forms .='</optgroup>';
//	echo ($forms);


	$formc = '<select name="cityf" style="width:150px" class="rdtextl"><option value=0>Все города</option>'.$formc.'</select>';
	$forms = '<select name="shopf" style="width:150px" class="rdtextl"><option value=0>Все магазины</option>'.$forms.'</select>';
//	$form = '<table cellpadding=0 cellspacing=0 border=0 width=100%><tr><td valign=top>'.$formc.'</td><td valign=top>'.$forms.'</td></tr></table>';

	$form = '<form method=post name=csform>
<table cellpadding=3 cellspacing=0 border=0 class=rdtext>
<tr><td><input type="radio" name="cityshop" onclick="showhide(rcity);csform.cshb.value=\'city\';"'.(($cshb=='city')?'  checked':'').'></td><td nowrap>Отразить по городам</td><td id=rcity style="visibility:"'.(($cshb=='city')?'visible':'hidden').'">'.$formc.'</td></tr>
<tr><td><input type="radio" name="cityshop" onclick="showhide(rshop);csform.cshb.value=\'shop\';"'.(($cshb=='shop')?' checked':'').'></td><td nowrap>Отразить по магазинам</td><td id=rshop  style="visibility:"'.(($cshb=='shop')?'visible':'hidden').'">'.$forms.'</td></tr></table><input type="hidden" name="cshb" value="'.(($_POST['cshb'])?$_POST['cshb']:'shop').'">';

	$diagtype = "<table cellpadding=3 cellspacing=0 border=0  class=rdtext>
<tr><td><input type='radio' name='dtypercc' onclick='csform.dtype.value=\"round\";'".(($dtype=='round')?' checked':'')."></td><td nowrap>Круговая диаграма</td></tr>
<tr><td><input type='radio' name='dtypercc' onclick='csform.dtype.value=\"counts\";' ".(($cshb && $dtype=='counts')?'checked':'')." ".((!$dtype)?"checked":"")."></td><td nowrap>Чиcловая таблица</td></tr>
</table><input type='hidden' name='dtype' value='".(($_POST['dtype'])?$_POST['dtype']:'counts')."'>";

	$curtime=time();
	$month = date('m',$curtime);
	$day = date('d',$curtime);
	$year = date('Y',$curtime);
	$beg=mktime(0,0,0,$month,$day,$year);
	$end=mktime(23,59,59,$month,$day,$year);

	$interval="<table cellpadding=3 cellspacing=0 border=0  class=rdtext>
<tr><td>Дата с:</td><td>00:00:00 <input type='text' name='tbegin'  value='".(($_POST['tbegin'])?$_POST['tbegin']:date('d.m.Y',$beg))."' style='width:70px'></td><td><img src='".$pathtoimages."/calendar.gif' id=tbeginpic width='15' height='15' border='0' alt='".(($_POST['tbegin'])?$_POST['tbegin']." 00:00:00":date('d.m.Y H:i:s',$beg))."' onclick='showcalendar(\"csform.tbegin\");' onmouseover='this.alt=csform.tbegin.value+\" 00:00:00\"'  style='cursor:pointer'></td></tr>
<tr><td>Дата по:</td><td>23:59:59 <input type='text' name='tend'  value='".(($_POST['tend'])?$_POST['tend']:date('d.m.Y',$end))."' style='width:70px'></td><td><img src='".$pathtoimages."/calendar.gif' width='15' height='15' border='0' alt='".(($_POST['tend'])?$_POST['tend']." 00:00:00":date('d.m.Y H:i:s',$end))."' onclick='showcalendar(\"csform.tend\")' onmouseover='this.alt=csform.tend.value+\" 00:00:00\"'   style='cursor:pointer'></td></tr>
</table>";

	$table = $script."<table cellpadding=0 cellspacing=0 border=0 width=100% class=rdtext>
<tr><td valign=top width=33%><center>Города/магазины</center><br>$form</td><td valign=top width=33%><center>Вывод данных</center><br>$diagtype</td><td valign=top width=33%><center>Интервал отображения</center>$interval<br></td></tr>
</table>
<input type='submit' value='Отобразить'>
</form>";

echo $table;

if ($_POST['dtype']) {
	$qadd='';
	
	if ($_POST['cshb']=='shop' && $shopf !=0) {
		$qadd = ' and `shop`="'.$_SESSION['shops'][$shopf].'"';
	}
	if ($_POST['cshb']=='city' && $cityf !=0) {
		$qadd = ' and `city`="'.$_SESSION['cities'][$cityf].'"';
	}
	$tbegin=(strlen($_POST['tbegin']) >10)?date("d.m.Y",time()):$_POST['tbegin'];
	$tend=(strlen($_POST['tend']) >10)?date("d.m.Y",(time()+86400)):$_POST['tend'];
	list($day,$month,$year) = explode('.',$tbegin);
	$beg = mktime(0,0,0,$month,$day,$year);
	list($day,$month,$year) = explode('.',$tend);
	$end = mktime(23,59,59,$month,$day,$year);
	$query='SELECT * FROM `'.$tabfordata.'` WHERE `time`>'.$beg.' and `time`<'.$end.''.$qadd.' ORDER BY `time`, `id`';
//	echo $query;
	$rezult=mysql_query($query);
	while($row=mysql_fetch_assoc($rezult)) {
		//echo $row['shop'].'-'.$row['city'].'<BR>';
		$gmas[$row['shop'].'-'.$row['city']]['metals'][]=$row['metals'];
		$gmas[$row['shop'].'-'.$row['city']]['gold'][]=$row['gold'];
		$gmas[$row['shop'].'-'.$row['city']]['polymers'][]=$row['polymers'];
		$gmas[$row['shop'].'-'.$row['city']]['organic'][]=$row['organic'];
		$gmas[$row['shop'].'-'.$row['city']]['silicon'][]=$row['silicon'];
		$gmas[$row['shop'].'-'.$row['city']]['radioactive'][]=$row['radioactive'];
		$gmas[$row['shop'].'-'.$row['city']]['gems'][]=$row['gems'];
		$gmas[$row['shop'].'-'.$row['city']]['venom'][]=$row['venom'];
	}

	foreach ($gmas as $shop=>$resname) {
		$shoper=$shop;
		list($se, $ce)=explode('-',$shop);
		foreach ($resname as $rnm=>$rn) {
			$resper=$rnm;
			$vcount=0;
			$sum=0;
			foreach ($rn as $rnc) {
				if ($vcount>$rnc && (($vcount-$rnc) < 10000)) {$sum =$sum+($vcount-$rnc);$vcount=$rnc;}
				else {$vcount=$rnc;}
			}
			if ($cshb =='city' && $shopf !=0) {
				$countce[$resper] +=$sum;
			}
			if (($cshb =='shop' && $shopf==0) || ($cshb =='city' && $shopf ==0)) {
				$countce[$ce][$resper] +=$sum;
			}

			$count[$shoper] .=(strlen($count[$shoper])>0)?','.$sum:$sum;
		}
	}
	if ($cshb =='city' && $shopf !=0) {
		foreach ($countce as $value) {
			$allres .=(strlen($allres)>0)?','.$value:$value;
		}
	}
	if (($cshb =='shop' && $shopf==0) || ($cshb =='city' && $shopf ==0)) {
		foreach ($countce as $value=>$var) {
			$cityper=$value;
			foreach ($var as $res) {
				$allres[$cityper] .=(strlen($allres[$cityper])>0)?','.$res:$res;
			}
		}
	}

	$shapka='<tr align=center>
<td class=rhbbr width=150>Название</td>
<td class=rhbbr width=60><img src="'.$pathtoimages.'/res/metals.gif" width="26" height="20" border="0" alt="Металл"><br>Металл</td>
<td class=rhbbr width=60><img src="'.$pathtoimages.'/res/gold.gif" width="26" height="17" border="0" alt="Золото"><br>Золото</td>
<td class=rhbbr width=60><img src="'.$pathtoimages.'/res/polymers.gif" width="26" height="18" border="0" alt="Полимер"><br>Полимеры</td>
<td class=rhbbr width=60><img src="'.$pathtoimages.'/res/organic.gif" width="26" height="16" border="0" alt="Органика"><br>Органика</td>
<td class=rhbbr width=60><img src="'.$pathtoimages.'/res/silicon.gif" width="27" height="16" border="0" alt="Силикон"><br>Силикон</td>
<td class=rhbbr width=60><img src="'.$pathtoimages.'/res/rad.gif" width="27" height="18" border="0" alt="Радик"><br>Радик</td>
<td class=rhbbr width=60><img src="'.$pathtoimages.'/res/gems.gif" width="26" height="16" border="0" alt="Гемы"><br>Гемы</td>
<td class=rhbbr width=60><img src="'.$pathtoimages.'/res/venom.gif" width="26" height="20" border="0" alt="Веном"><br>Веном</td></tr>';
	$table='';
	$flag=0;
	$cityold='';
	$itogo='';
	$restab='';
	foreach ($count as $value=>$var) {
		list($shop,$city)=explode('-',$value);
		if (($flag%2==0 && $flag != 0 && ($cshb !='city' || ($cshb =='city' && $cityf !=0)))  &&  $dtype=='round') {
			$table .='</tr><tr>';
		}
		if ($city != $cityold &&  $dtype=='round') {
			$table .='<tr><td colspan=2 align=center style="font-weight:bold;font-size:11pt;font-face:arial"><br>'.$city;
			if ((($_POST['cshb'] =='shop' && $shopf ==0) || ($_POST['cshb']=='city')) && ($city == 'New Moscow' || $city == 'Oasis' || $city == 'Neva City' || ($_POST['cshb']=='city' && $city==0))) {
				$table .='<br><br><div style="font-size:10pt;font-face:arial;font-weight:bold">Общая статистика по '.$city.'</div><img src="diag.php?data='.(($cshb =='city' && $shopf !=0)?$allres:$allres[$city]).'">';
			}
			$table .='</td></tr>';
			$cityold=$city;
			$flag=0;
		}
		if (($cshb !='city' || ($cshb =='city' && $cityf !=0)) &&  $dtype=='round') {
			$table .='<td align=center><br><div style="font-size:10pt;font-face:arial;font-weight:bold">'.$shop.'</div><img src="diag.php?data='.$var.'"></td>';
		}

		if ($city != $cityold &&  $dtype=='counts') {
			if (strlen($table)>10 || strlen($restab)>10 || strlen($itogo)>10 ) {$table .=$restab.$itogo;$restab='';$itogo='';}
			if ($cshb!='city' || $cityf!=0) {
				$table .='<tr><td colspan=9 align=center class=rhbbr >'.$city.'</td></tr>';
			}

			if ((($cshb =='shop' && $shopf ==0) || ($cshb=='city')) && ($city == 'New Moscow' || $city == 'Oasis' || $city == 'Neva City' || ($cshb=='city' && $cityf==0))) {
				$itogo ='<tr align=center><td style="font-size:10pt;font-face:arial;font-weight:bold" class=rhbbr align=right>'.(($cshb=='city' && $cityf==0)?$city:'Всего:').'</td><td class=rbbr >'.(($cshb =='city' && $shopf !=0)?str_replace(',', '</td><td class=rbbr>', $allres):str_replace(',','</td><td class=rbbr>', $allres[$city])).'</td></tr>';
			}
			$cityold=$city;$flag=0;
		}
		if (($cshb !='city' || ($cshb =='city' && $cityf !=0)) &&  $dtype=='counts') {
			$var=str_replace(",","</td><td class=rbbr >",$var);
			$restab .='<tr align=center><td style="font-size:10pt;font-face:arial;font-weight:bold" align=right class=rhbbr>'.$shop.'</td><td class=rbbr >'.$var.'</td></tr>';
			if (($cshb =='shop' && $shopf !=0) || ($cshb =='city' && $cityf !=0)) {$table .=$restab;$restab='';}
		}
		$flag++;
	}
	if ($dtype=='round') {
		if (strlen($table)>0) {
			$table = '<tr>'.$table.'</tr>';
		} else {
			$table = 'Данных нет';
		}
	}
	if ($dtype=='counts') {
		if (strlen($table)>0 || strlen($itogo)>0 || strlen($restab)>0 ) {
			$table = $shapka.$table.$restab.$itogo.$shapka;
		} else {
			$table = 'Данных нет';
		}
	}
	if (strlen($table)>20) {
		$table = '<table cellpadding=2 cellspacing=0 border=0 style="font-weight:bold;font-size:10pt;font-face:arial" '.(($dtype=="round")?"":"class=rtb").' align=center>'.$table.'</table>';
	}
	echo $table;
}

?>
<hr>
<font size="-2">Данные ориентировочные и могут использоваться лишь для приблизительного анализа</font>