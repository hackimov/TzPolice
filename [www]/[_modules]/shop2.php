<h1>Интернет-магазин "военторгЪ"</h1>



<?php



$bgstr[0]="background='i/bgr-grid-sand.gif'";

$bgstr[1]="background='i/bgr-grid-sand1.gif'";



if(AuthStatus==1 && AuthUserName!="" && (AuthUserClan=='police' || AuthUserClan=='Military Police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy' || AuthUserGroup == 100)) {



$SQL = "SELECT * FROM const WHERE script='shop2' AND name='work_status'";

$r = mysql_query($SQL);

$d = mysql_fetch_array($r);

if ($d['value']==0) {

?>

<center>

<table border="0" cellspacing="0" background="i/bgr-grid-sand.gif" cellpadding="10" style="BORDER: 1px #957850 solid;">

<tr><td>

<table width="100%"><tr><td align="center"><font color="red"><b>Внимание!</b></font><br></td></tr></table>

Магазин закрыт

</td></tr>

</table>

</center>

<?

} else {



$permission=0;

$SQL = "SELECT * FROM build_users WHERE user_id='".AuthUserId."'";

$r = mysql_query($SQL);

if ($d=mysql_fetch_array($r)) $permission=$d['factory'];



$factories = array();

$SQL = "SELECT * FROM buildings WHERE type=1";

$r=mysql_query($SQL);

while ($d=mysql_fetch_array($r)) {

$factories[$d['id']] = $d['name'];

$order_time[$d['id']] = $d['order_time'];

}



$SQL="SELECT factory, MAX(t1) as t1 FROM shop_orders WHERE customer='".AuthUserId."' GROUP BY factory";

$r=mysql_query($SQL);

while ($d=mysql_fetch_array($r)) {

	$s=mysql_fetch_array(mysql_query("SELECT resources FROM shop_orders WHERE t1='".$d['t1']."' AND factory='".$d['factory']."'"));

	$next_order[$d['factory']] = $order_time[$d['factory']]-ceil((time()-$d['t1'])/60)+substr($s['resources'],strrpos($s['resources'],"|")+1)-240;

}



$groups = array();

$SQL = "SELECT * FROM items_class";

$r=mysql_query($SQL);

while ($d=mysql_fetch_array($r)) {

	$groups[$d['id']] = $d['class'];

	$limits[$d['id']] = $d['cmax'];

	mysql_query("DELETE FROM shop_logs WHERE class='".$d['id']."' AND tm<'".(time()-60*$d['ctime'])."'");

}



$SQL = "SELECT class, SUM(`count`) FROM shop_logs WHERE user_id='".AuthUserId."' GROUP BY class";

$r=mysql_query($SQL);

while ($d=mysql_fetch_row($r)) {

	$user_limit[$d[0]]=$d[1];

}



if (@$_REQUEST['error']) {

	echo "<h6 style=\"color: red\">".$_REQUEST['error']."</h6>";

}



if(strlen($_REQUEST['order'])>3) {

	$tcost[0]=0;	//coins

	$tcost[1]=0;	//polymers

	$tcost[2]=0;	//organic

	$tcost[3]=0;	//venom

	$tcost[4]=0;	//radioactive

	$tcost[5]=0;	//gold

	$tcost[6]=0;	//gem

	$tcost[7]=0;	//metal

	$tcost[8]=0;	//silicon

	$items='';

	$resources='';



	$order=explode("|", $_REQUEST['order']);

	foreach($order AS $order) if(strlen($order)>1) {

		$OrderData=explode("*",$order);

		$dd=mysql_fetch_array(mysql_query("SELECT * FROM shop_items WHERE id='".$OrderData[0]."'"));

		$ic[$dd['id']]=$OrderData[1];

		$ir[$dd['id']]='0*'.$dd['mnt']."|1*".$dd['polymer']."|2*".$dd['organic']."|3*".$dd['venom']."|4*".$dd['rad']."|5*".$dd['gold']."|6*".$dd['gem']."|7*".$dd['metal']."|8*".$dd['silicon'];

		$tcost[0]+=$dd['mnt']*$OrderData[1];

		$tcost[1]+=$dd['polymer']*$OrderData[1];

		$tcost[2]+=$dd['organic']*$OrderData[1];

		$tcost[3]+=$dd['venom']*$OrderData[1];

		$tcost[4]+=$dd['rad']*$OrderData[1];

		$tcost[5]+=$dd['gold']*$OrderData[1];

		$tcost[6]+=$dd['gem']*$OrderData[1];

		$tcost[7]+=$dd['metal']*$OrderData[1];

		$tcost[8]+=$dd['silicon']*$OrderData[1];

	      $items.=$dd['item_name']."|*".$OrderData[1]."|;";

		$tclass[$dd['group']]+=$OrderData[1];

		$ttime+=$dd['time_req']*$OrderData[1];

		if ($dd['imax']<$OrderData[1] && $dd['imax']) $error="Превышен предел кол-ва для одной из позиций заказа";

        if ($dd['available']<$OrderData[1]) $error="Превышен предел кол-ва для одной из позиций заказа";

	}

	if ($ttime>240) $error="Время заказа превышает допустимое";

	for($i=0; $i<=8; $i++) if($tcost[$i]>0) $resources .= $i.'*'.$tcost[$i]."|";

	$resources .= $_REQUEST['t'];

	$SQL="SELECT resources FROM shop_orders WHERE customer='".AuthUserId."' AND status=1 AND factory='".$_REQUEST['f']."'";

	$r=mysql_query($SQL);

	while ($d=mysql_fetch_array($r)) {

		$res = explode("|",substr($d['resources'],0,strrpos($d['resources'],"|")));

		foreach ($res as $a) {

			$b = explode("*",$a);

			$tcost[$b[0]]+=$b[1];

		}

	}

	foreach (array_keys($tclass) as $class) {

		if ($tclass[$class]>($limits[$class]-$user_limit[$class]) && $limits[$class]) $error="Превышен лимит";

	}



	$SQL="SELECT SUM(coins), SUM(polymers), SUM(organic), SUM(venom), SUM(radioactive), SUM(gold), SUM(gems), SUM(metals), SUM(silicon) FROM warehouses WHERE user_id='".AuthUserId."' AND warehouse='".$_REQUEST['f']."' AND status<2";

	$r=mysql_query($SQL);

	$d=mysql_fetch_row($r);

	for($i=0; $i<=8; $i++) if($tcost[$i]>$d[$i]) $error="Недостаточно ресурсов на складе \"".$factories[$_REQUEST['f']]."\"";

	if ($error) {

		echo "<script>top.location='?act=shop2&error=".$error."';</script>";

	} else {

		$SQL="INSERT INTO shop_orders (customer, content, resources, factory, t1)

		values('".AuthUserId."','".$items."','".$resources."','".$_REQUEST['f']."','".time()."')";

		mysql_query($SQL) or die(mysql_error());

		$order_id=mysql_insert_id();

		foreach (array_keys($ic) as $item) {

			mysql_query("INSERT INTO shop_temp VALUES ('','".$order_id."','".$item."','".$_REQUEST['f']."','".$ic[$item]."','1')");

			$query = "UPDATE `shop_items` SET `available`=`available`-".$ic[$item]." WHERE `id` = '".$item."' LIMIT 1;";

			mysql_query($query);

		}

		foreach (array_keys($tclass) as $class) {

			if ($limits[$class]>0) {

				$SQL="INSERT INTO shop_logs VALUES('','".time()."','".AuthUserId."','".$class."','".$tclass[$class]."', '".$order_id."')";

				mysql_query($SQL);

			}

		}

		echo "<script>top.location='?act=shop2';</script>";

	}

}



if($permission) {

	if($a=="add_item") {

    	if ($_REQUEST['tradeonly'] == 1)

        	{

            	$tr_only = 1;

            }

        else

        	{

            	$tr_only = 0;

            }

		$SQL="INSERT INTO shop_items values('',1,

		'".htmlspecialchars($_REQUEST['item_name'])."',

		'".$_REQUEST['time']."',

		'".$_REQUEST['coins']."',

		'".$_REQUEST['organic']."',

		'".$_REQUEST['polymer']."',

		'".$_REQUEST['venom']."',

		'".$_REQUEST['rad']."',

		'".$_REQUEST['gold']."',

		'".$_REQUEST['gem']."',

		'".$_REQUEST['metal']."',

		'".$_REQUEST['silicon']."',

		'".$_REQUEST['max']."',

		'".$_REQUEST['factory']."',

		'".$_REQUEST['group']."',

        '".$_REQUEST['avail']."',

        '".$tr_only."')";

		mysql_query($SQL) or die (mysql_error());

		echo "<script>top.location='?act=shop2';</script>";

	}

	if(@$_REQUEST['editID']) {

		$SQL="UPDATE shop_items SET ".

		"item_name='".$_POST['ProductName']."', ".

		"mnt='".$_POST['Rmnt']."', ".

		"organic='".$_POST['Rorg']."', ".

		"polymer='".$_POST['Rpol']."', ".

		"venom='".$_POST['Rven']."', ".

		"rad='".$_POST['Rrad']."', ".

		"gold='".$_POST['Rgol']."', ".

		"gem='".$_POST['Rgem']."', ".

		"metal='".$_POST['Rmet']."', ".

		"silicon='".$_POST['Rsil']."' ".

		"WHERE id='".$_POST['editID']."' LIMIT 1;";

            $SQL = str_replace("'NaN'", "'0'", $SQL);

		mysql_query($SQL);

		echo "<script>top.location='?act=shop2';</script>";

	}

	if(@$_REQUEST['optionID']) {

    	if ($_POST['Rtrade'] == 1)

        	{

            	$tr_only = 1;

            }

        else

        	{

            	$tr_only = 0;

            }

		$SQL="UPDATE shop_items SET ".

		"time_req='".$_POST['Rtime']."', ".

		"imax='".$_POST['Rmax']."', ".

		"factory='".$_POST['Rfactory']."', ".

        "`available`='".$_POST['Ravail']."', ".

		"`group`='".$_POST['Rgroup']."', ".

		"`trade`='".$tr_only."' ".

		"WHERE id='".$_POST['optionID']."' LIMIT 1;";

            $SQL = str_replace("'NaN'", "'0'", $SQL);

		mysql_query($SQL);

		echo "<script>top.location='?act=shop2';</script>";

	}

	if(@$_REQUEST['hide']) {

		$SQL="UPDATE shop_items SET is_visible=0 WHERE id='".$_REQUEST['hide']."'";

		mysql_query($SQL);

		echo "<h6>Предмет ID:".$_REQUEST['hide']." скрыт.</h6>";

	}

	if(@$_REQUEST['show']) {

		$SQL="UPDATE shop_items SET is_visible=1 WHERE id='".$_REQUEST['show']."'";

		mysql_query($SQL);

		echo "<h6>Предмет ID:".$_REQUEST['show']." видим.</h6>";

	}

	if(@$_REQUEST['del']) {

		$SQL="UPDATE shop_items SET is_visible=2 WHERE id='".$_REQUEST['del']."'";

		mysql_query($SQL);

		echo "<h6>Предмет ID:".$_REQUEST['del']." удален.</h6>";

	}

}

?>



<link href="_modules/tabs.css" type=text/css rel=stylesheet>

<script language=javascript src="_modules/tabs.js"></script>

<script>

var items = new Array();

<?

$d=mysql_fetch_array(mysql_query("SELECT MAX(id) as maxx FROM shop_items"));

foreach (array_keys($factories) as $fid) {

?>

items[<?=$fid?>] = new Array(<?=$d['maxx']+1?>);

for(var i=0;i<=<?=$d['maxx']+1?>;i++) items[<?=$fid?>][i]=0;

<? } ?>



var limits = new Array();

<?

foreach (array_keys($limits) as $class) {

	if ($limits[$class]>0) {

		echo "limits[".$class."] = ".($limits[$class]-$user_limit[$class])."\n";

	}

}

?>

var ul = new Array();



var dclass = new Array();



<?

$SQL="SELECT `factory`, `group`, COUNT(*) FROM shop_items WHERE is_visible=1 GROUP BY `factory`, `group` ORDER BY `factory`";

$r=mysql_query($SQL);

$ifac='';

while($d=mysql_fetch_row($r)) {

if ($ifac!=$d[0]) {

$ifac=$d[0];

?>

dclass[<?=$ifac?>] = new Array();

<? } ?>

dclass[<?=$ifac?>][<?=$d[1]?>] = <?=$d[2]?>;

<? } ?>



function show_group(f,g) {

	for (i=1; i<=dclass[f][g]; i++) {

		if (document.all('l'+f+'_'+g+'_'+i).style.display=='none') {

			document.all('l'+f+'_'+g+'_'+i).style.display = '';

		} else {

			document.all('l'+f+'_'+g+'_'+i).style.display = 'none';

		}

	}

}



function AddItem(obj,act,c,d,e) {

	if(act=="+") {

		if (Math.abs(document.all('Rtime'+d).value)+Math.abs(obj.form.Rtime.value)>240) {

			alert('Превышено время изготовления одного заказа!');

		} else {

			if ((Math.abs(obj.form.cnt.value)+1>Math.abs(obj.form.Rmax.value)) && (Math.abs(obj.form.Rmax.value)>0)) {

				alert('Превышен максимум в заказе!');

			} else {

				if ((limits[e]>0) && (ul[e] >= limits[e])) {

					alert('Превышен максимум в заказе!');

				} else {

               		if ((Math.abs(obj.form.cnt.value)+1>Math.abs(obj.form.Ravail.value))) {

						alert('Превышен максимум, доступный для производства!');

					} else {

	                    if (!(ul[e])) ul[e]=0;

	                    ul[e]++;

	                    document.all('Rmnt'+d).value=Math.abs(document.all('Rmnt'+d).value)+Math.abs(obj.form.Rmnt.value);

	                    document.all('Rpol'+d).value=Math.abs(document.all('Rpol'+d).value)+Math.abs(obj.form.Rpol.value);

	                    document.all('Rorg'+d).value=Math.abs(document.all('Rorg'+d).value)+Math.abs(obj.form.Rorg.value);

	                    document.all('Rven'+d).value=Math.abs(document.all('Rven'+d).value)+Math.abs(obj.form.Rven.value);

	                    document.all('Rrad'+d).value=Math.abs(document.all('Rrad'+d).value)+Math.abs(obj.form.Rrad.value);

	                    document.all('Rgol'+d).value=Math.abs(document.all('Rgol'+d).value)+Math.abs(obj.form.Rgol.value);

	                    document.all('Rgem'+d).value=Math.abs(document.all('Rgem'+d).value)+Math.abs(obj.form.Rgem.value);

	                    document.all('Rmet'+d).value=Math.abs(document.all('Rmet'+d).value)+Math.abs(obj.form.Rmet.value);

	                    document.all('Rsil'+d).value=Math.abs(document.all('Rsil'+d).value)+Math.abs(obj.form.Rsil.value);

	                    document.all('Rtime'+d).value=Math.abs(document.all('Rtime'+d).value)+Math.abs(obj.form.Rtime.value);

	                    obj.form.cnt.value=Math.abs(obj.form.cnt.value)+1;

	                    items[d][c]+=1;

                    }

				}

			}

		}

	}

	if(act=="-") {

		if (Math.abs(obj.form.cnt.value)>0) {

			ul[e]--;

			document.all('Rmnt'+d).value=Math.abs(document.all('Rmnt'+d).value)-Math.abs(obj.form.Rmnt.value);

			document.all('Rpol'+d).value=Math.abs(document.all('Rpol'+d).value)-Math.abs(obj.form.Rpol.value);

			document.all('Rorg'+d).value=Math.abs(document.all('Rorg'+d).value)-Math.abs(obj.form.Rorg.value);

			document.all('Rven'+d).value=Math.abs(document.all('Rven'+d).value)-Math.abs(obj.form.Rven.value);

			document.all('Rrad'+d).value=Math.abs(document.all('Rrad'+d).value)-Math.abs(obj.form.Rrad.value);

			document.all('Rgol'+d).value=Math.abs(document.all('Rgol'+d).value)-Math.abs(obj.form.Rgol.value);

			document.all('Rgem'+d).value=Math.abs(document.all('Rgem'+d).value)-Math.abs(obj.form.Rgem.value);

			document.all('Rmet'+d).value=Math.abs(document.all('Rmet'+d).value)-Math.abs(obj.form.Rmet.value);

			document.all('Rsil'+d).value=Math.abs(document.all('Rsil'+d).value)-Math.abs(obj.form.Rsil.value);

			document.all('Rtime'+d).value=Math.abs(document.all('Rtime'+d).value)-Math.abs(obj.form.Rtime.value);

			obj.form.cnt.value=Math.abs(obj.form.cnt.value)-1;

			items[d][c]-=1;

		}

	}



	var a = Math.abs(document.all('Rtime'+d).value);

	document.all('RTstr'+d).value='';

	if (a>=1440) {

		document.all('RTstr'+d).value = Math.floor(a/1440)+'д ';

		a = a%1440;

	}

	if (a>=60) {

		document.all('RTstr'+d).value += Math.floor(a/60)+'ч ';

		a = a%60;

	}

	document.all('RTstr'+d).value += a+'мин';

}



function MakeOrder(a) {

	var req="";

	for(var i=1;i<items[a].length;i++) {

		if(items[a][i]>0) req+=i+"*"+items[a][i]+"|";

	}

	if(req.length>1) {

		if(confirm('Вы уверены в своем заказе?')) top.location='?act=shop2&order='+req+'&f='+a+'&t='+document.all('Rtime'+a).value;

	} else {

		alert('Невозможно сделать пустой заказ!\n');

	}

}

</script>



<? if($permission) { ?>



<table width='100%' border='0' cellspacing='3' cellpadding='2'>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Добавить вещь:</strong> </p></td></tr>

<tr><td>



<form>

<input name="act" type="hidden" value="shop2">

<input name="a" type="hidden" value="add_item">

<table>

<tr>

<td>Название и кол-во</td>

<td align=center><img src="_imgs/tz/coins.gif" height=21></td>

<td align=center><img src="_imgs/tz/polymer.gif" height=21></td>

<td align=center><img src="_imgs/tz/organic.gif" height=21></td>

<td align=center><img src="_imgs/tz/venom.gif"></td>

<td align=center><img src="_imgs/tz/rad.gif"></td>

<td align=center><img src="_imgs/tz/gold.gif" height="21"></td>

<td align=center><img src="_imgs/tz/gem.gif" height="21"></td>

<td align=center><img src="_imgs/tz/metal.gif" height="21"></td>

<td align=center><img src="_imgs/tz/silicon.gif" height="21"></td>

<td></td>

</tr>

<tr>

<td><input name="item_name" type="text" value="" size=30></td>

<td><input name="coins" type="text" value="" size=5></td>

<td><input name="polymer" type="text" value="" size=5></td>

<td><input name="organic" type="text" value="" size=5></td>

<td><input name="venom" type="text" value="" size=5></td>

<td><input name="rad" type="text" value="" size=5></td>

<td><input name="gold" type="text" value="" size=5></td>

<td><input name="gem" type="text" value="" size=5></td>

<td><input name="metal" type="text" value="" size=5></td>

<td><input name="silicon" type="text" value="" size=5></td>

<td width=90 align=center><input type="submit" value="Add"></td>

</tr>

<tr>

<td align=right>Время производства <input name="time" type="text" value="" size=5></td>

<td align=left>мин</td>

<td align=right colspan=2>Макс в заказе</td>

<td align=center><input name="max" type="text" value="" size=5></td>

<td align=center colspan=3><select name="factory">

<? foreach (array_keys($factories) as $fid) { ?>

	<option value="<?=$fid?>"><?=$factories[$fid]?></option>

<? } ?>

</select></td>

<td colspan=3><select name="group">

<? foreach (array_keys($groups) as $gid) { ?>

	<option value="<?=$gid?>"><?=$groups[$gid]?></option>

<? } ?>

</select></td>

</tr>

<tr>

<td align=right>Доступно в чертеже <input name="avail" type="text" value="" size=5></td>

<td>sale <input type="checkbox" name="tradeonly" value="1"></td>

<td colspan=9>&nbsp;</td>

</tr>

</table>

</form>



</td></tr>

</table>



<? } ?>



<center>

<table border="0" cellspacing="0" background="i/bgr-grid-sand.gif" cellpadding="10" style="BORDER: 1px #957850 solid;">

<tr><td>

<table width="100%"><tr><td align="center"><font color="red"><b>Внимание!</b></font><br></td></tr></table>

1. Время ожидания выполнения заказа в будни до двух суток, в выходные до трёх суток.<br>

2. Перед подачей заказа убедитесь, что положили необходимые ресурсы на склад соответствующего завода, для проверки можете воспользоваться анализатором<br>

3. Обязательно оставляйте запись в складских переводах, сколько ресурсов вы или для вас было положено на склад<br>

4. После получения товара необходимо ставить пометку об этом

</td></tr>

</table>

</center>



<?if(AuthUserGroup==100) {


?>

<p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>For MbILLIKA only:</strong>

<br><br>

<a href="direct_call/shop_history.php?item=all" target="logs">Просмотреть сводные логи</a>

<center>

<iframe src="direct_call/shop_history.php" name="logs" width="550" marginwidth="0" height="180" marginheight="0" align="middle" scrolling="auto"><br>

</iframe>

</center>

<?}?>



<br>



<form action="" method="post">



<table border="0" cellspacing="0" cellpadding="0" width="100%" id="tb_content">

<tr>

<?

$bcount=0;

foreach (array_keys($factories) as $fid) {

	$bname = str_replace(' ','&nbsp;',$factories[$fid]);

	$bname = str_replace('-','&#8209;',$bname);

	echo "	<td height=\"20\" class=\"tab-".($bcount?'off':'on')."\" id=\"navcell\" onclick=\"switchCell(".$bcount.")\" valign=\"middle\" nowrap><b>&nbsp;".$bname."&nbsp;</b></td>\n";

	$bcount++;

}

?>

<? if ($permission) { ?>

	<td height="20" class="tab-off" id="navcell" onclick="switchCell(<?=$bcount?>)" valign="middle"><b>&nbsp;Редактирование&nbsp;</b></td>

	<td height="20" class="tab-off" id="navcell" onclick="switchCell(<?=($bcount+1)?>)" valign="middle"><b>&nbsp;Дополнительно&nbsp;</b></td>

<? } ?>

	<td class=tab-none noWrap><FONT face=Tahoma color="#ffffff">&nbsp;</TD>

</tr>

</table>

<?

foreach (array_keys($factories) as $fid) {

?>

<!-- Витрина -->

<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0">

<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Сделать заказ:</strong> </p></td></tr>

<tr><td align="center">



<table width='100%' border='0' cellspacing='3' cellpadding='2'>



<tr><td>



<table width=100%>



<tr bgcolor=#F4ECD4>

<td bgcolor=#F4ECD4><b>Название:</b></td>

<td align=center><img src="_imgs/tz/coins.gif" height=21></td>

<td align=center><img src="_imgs/tz/polymer.gif" height=21></td>

<td align=center><img src="_imgs/tz/organic.gif" height=21></td>

<td align=center><img src="_imgs/tz/venom.gif"></td>

<td align=center><img src="_imgs/tz/rad.gif"></td>

<td align=center><img src="_imgs/tz/gold.gif" height="21"></td>

<td align=center><img src="_imgs/tz/gem.gif" height="21"></td>

<td align=center><img src="_imgs/tz/metal.gif" height="21"></td>

<td align=center><img src="_imgs/tz/silicon.gif" height="21"></td>

<td bgcolor=#F4ECD4 align=center>доступно:</td>

<td bgcolor=#F4ECD4 align=center>кол-во:</td>

<td align=center bgcolor=#F8F6ED><b>Заказ:</b></td>

</tr>



<?

$SQL="SELECT * FROM shop_items WHERE is_visible='1' AND `available`>'0' AND `trade`='0' AND factory='".$fid."' ORDER BY `group`, `id` DESC";

$r=mysql_query($SQL);

$np=0;

$iclass='';



while($d=mysql_fetch_array($r)) {



if ($iclass!=$d['group']) {

$iclass=$d['group'];

$count=0;

$np=0;

?>

<tr bgcolor=#F4ECD4>

<td colspan=13><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><a onclick="show_group('<?=$fid?>','<?=$iclass?>');" href='#;return false;'><?=$groups[$iclass]?></a></td>

</tr>



<?

}



$count++;

if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

?>

<tr id="l<?=$fid?>_<?=$iclass?>_<?=$count?>" style="display:none"><form name="prodid_<?=$d['id']?>">

<input name="ProductId" type="hidden" value="<?=$d['id']?>">

<td <?=$bg?>><?=$d['item_name']?><?if($permission) echo " (<a href='#; return false;' onclick=\"if(confirm('Скрыть предмет?')) top.location='?act=shop2&hide={$d[id]}';\">X</a>)"?>&nbsp;<?if(AuthUserGroup==100) echo " (<a href='direct_call/shop_history.php?item={$d[id]}' target='logs'>Логи</a>)"?></td>

<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rmnt" type="text" value="<?=$d['mnt']?>" size=5></td>

<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rpol" type="text" value="<?=$d['polymer']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rorg" type="text" value="<?=$d['organic']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rven" type="text" value="<?=$d['venom']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rrad" type="text" value="<?=$d['rad']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rgol" type="text" value="<?=$d['gold']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rgem" type="text" value="<?=$d['gem']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rmet" type="text" value="<?=$d['metal']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rsil" type="text" value="<?=$d['silicon']?>" size=4></td>

<td bgcolor=#F4ECD4 align=center><input style="text-align:right" name="Ravail" disabled type="text" value="<?=$d['available']?>" size=4></td>

<td bgcolor=#F4ECD4 align=center><input style="text-align:right" name="cnt" disabled type="text" value="0" size=4><input name="Rmax" type="hidden" value="<?=$d['imax']?>"></td>

<td align=center bgcolor=#F8F6ED><input name="Rtime" type="hidden" value="<?=$d['time_req']?>"><input type="button" value="+1" style="width:20" onclick="AddItem(this,'+','<?=$d['id']?>','<?=$fid?>','<?=$d['group']?>');"<?if($d['imax']) echo " alt='Максимум в заказе: ".$d['imax']."' title='Максимум в заказе: ".$d['imax']."'"?>> <input type="button" value="-1" style="width:20"  onclick="AddItem(this,'-','<?=$d['id']?>','<?=$fid?>','<?=$d['group']?>');"></td>

</form></tr>



<?

}

?>



<tr><form name="tcnt">

<td bgcolor=#F4ECD4><b>Общая стоимость: </b></td>

<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rmnt<?=$fid?>" type="text" size=5></td>

<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rpol<?=$fid?>" type="text" value="" size=4></td>

<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rorg<?=$fid?>" type="text" value="" size=4></td>

<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rven<?=$fid?>" type="text" value="" size=4></td>

<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rrad<?=$fid?>" type="text" value="" size=4></td>

<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rgol<?=$fid?>" type="text" value="" size=4></td>

<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rgem<?=$fid?>" type="text" value="" size=4></td>

<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rmet<?=$fid?>" type="text" value="" size=4></td>

<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rsil<?=$fid?>" type="text" value="" size=4></td>

<td  align=center colspan=3><input name="Rtime<?=$fid?>" type="hidden" value="" size=15><input style="text-align:center" disabled name="RTstr<?=$fid?>" type="text" value="Время заказа" size=15></td>

</form></tr>



</table><br>

<? if ($next_order[$fid]>0) { ?>

<center><font color=red><b>Заказы будут доступны через <?=($next_order[$fid]>=60?floor($next_order[$fid]/60)."ч ":"").($next_order[$fid]%60)."мин"?></b></font></center><br>

<? } else { ?>

<center><input type="button" onclick="MakeOrder(<?=$fid?>)" href="#; return false;" style="CURSOR: hand" value="Сделать заказ"></center><br>

<? } ?>



</td></tr>



</table>



</td></tr></table>

<? } ?>



<? if ($permission) { ?>

<!-- Склад -->

<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0">

<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Редактировать стоимость:</strong> </p></td></tr>

<tr><td align="center">



<table width=100%>



<tr bgcolor=#F4ECD4>

<td bgcolor=#F4ECD4><b>Название:</b></td>

<td align=center><img src="_imgs/tz/coins.gif" height=21></td>

<td align=center><img src="_imgs/tz/polymer.gif" height=21></td>

<td align=center><img src="_imgs/tz/organic.gif" height=21></td>

<td align=center><img src="_imgs/tz/venom.gif"></td>

<td align=center><img src="_imgs/tz/rad.gif"></td>

<td align=center><img src="_imgs/tz/gold.gif" height="21"></td>

<td align=center><img src="_imgs/tz/gem.gif" height="21"></td>

<td align=center><img src="_imgs/tz/metal.gif" height="21"></td>

<td align=center><img src="_imgs/tz/silicon.gif" height="21"></td>

<td align=center bgcolor=#F8F6ED><b>Править</b></td>

</tr>



<?

$SQL="SELECT * FROM shop_items WHERE is_visible='0' OR `available`<'1' ORDER BY id desc";

$r=mysql_query($SQL);

$np=0;



while($d=mysql_fetch_array($r)) {



if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

?>



<tr><form name="prodid_<?=$d['id']?>" action="?act=shop2" method="POST">

<input name="editID" type="hidden" value="<?=$d['id']?>">

<td <?=$bg?>><input name="ProductName" type="text" value="<?=$d['item_name']?>" size=40>

<?echo " (<a href='#; return false;' style='color: green' onclick=\"if(confirm('Вернуть предмет?')) top.location='?act=shop2&show={$d[id]}';\">X</a>)"?>

<?echo " (<a href='#; return false;' onclick=\"if(confirm('Удалить предмет?')) top.location='?act=shop2&del={$d[id]}';\">X</a>)"?>&nbsp;<?if(AuthUserGroup==100) echo " (<a href='direct_call/shop_history.php?item={$d[id]}' target='logs'>Логи</a>)"?></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rmnt" type="text" value="<?=$d['mnt']?>" size=5></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rpol" type="text" value="<?=$d['polymer']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rorg" type="text" value="<?=$d['organic']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rven" type="text" value="<?=$d['venom']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rrad" type="text" value="<?=$d['rad']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rgol" type="text" value="<?=$d['gold']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rgem" type="text" value="<?=$d['gem']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rmet" type="text" value="<?=$d['metal']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rsil" type="text" value="<?=$d['silicon']?>" size=4></td>

<td align=center bgcolor=#F8F6ED><input type=button value=" -*- " onclick="if(confirm('Изменить?')) this.form.submit();"></td>

</form></tr>



<?

}

?>



</table>



</td></tr></table>



<!-- Опции -->

<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0">

<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Редактировать доп. опции:</strong> </p></td></tr>

<tr><td align="center">



<table width=100%>



<tr bgcolor=#F4ECD4>

<td width=99%><b>Название:</b></td>

<td align=center>sale</td>

<td align=center>Группа</td>

<td align=center>Время изготовления</td>

<td align=center>Доступно в чертеже</td>

<td align=center>Максимум в заказе</td>

<td align=center>Завод&nbsp;изготовитель</td>

<td align=center bgcolor=#F8F6ED><b>Править</b></td>

</tr>



<?

$SQL="SELECT * FROM shop_items WHERE is_visible='0' OR `available`<'1' ORDER BY id desc";

$r=mysql_query($SQL);

$np=0;



while($d=mysql_fetch_array($r)) {

if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

?>

<tr><form name="prodid_<?=$d['id']?>" action="?act=shop2" method="POST">

<input name="optionID" type="hidden" value="<?=$d['id']?>">

<td <?=$bg?>><?=$d['item_name']?></td>

<td <?=$bg?> align=center><input type="checkbox" name="Rtrade" value="1" <?if ($d['trade'] == 1) {echo ("checked");}?>></td>

<td <?=$bg?>><select name="Rgroup">

<?

foreach (array_keys($groups) as $gid) {

	echo "	<option value=\"$gid\"".($gid==$d['group']?' selected':'').">".$groups[$gid]."</option>\n";

}

?>

</select></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rtime" type="text" value="<?=$d['time_req']?>" size=13></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Ravail" type="text" value="<?=$d['available']?>" size=13></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rmax" type="text" value="<?=$d['imax']?>" size=13></td>

<td <?=$bg?> align=center><select name="Rfactory">

<?

foreach (array_keys($factories) as $fid) {

	echo "	<option value=\"$fid\"".($fid==$d['factory']?' selected':'').">".$factories[$fid]."</option>\n";

}

?>

</select></td>

<td align=center bgcolor=#F8F6ED><input type=button value=" -*- " onclick="if(confirm('Изменить?')) this.form.submit();"></td>

</form></tr>



<?

}

?>

</table>



</td></tr></table>



</form>



<br>



<?

}

?>



<script language="Javascript" type="text/javascript">

switchCell(0);

</script>



<?

} } else echo $mess['AccessDenied'];

?>