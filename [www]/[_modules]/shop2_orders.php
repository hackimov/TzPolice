<h1>Заказы интернет-магазина "военторгЪ"</h1>
<?php
$bgstr[0]="background='i/bgr-grid-sand.gif'";
$bgstr[1]="background='i/bgr-grid-sand1.gif'";
if(AuthStatus==1 && AuthUserName!="" && (AuthUserClan=='police' || AuthUserClan=='Military Police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy')) {
$permission=0;
$SQL = "SELECT * FROM build_users WHERE user_id='".AuthUserId."'";
$r = mysql_query($SQL);
if ($d=mysql_fetch_array($r)) $permission=$d['factory'];
	if(@$_REQUEST['cancelID']>0) {
		$query = "SELECT `item_id`, `count` FROM `shop_temp` WHERE `order_id` = '".addslashes($_REQUEST['delID'])."'";
        $res = mysql_query($query);
        while (list ($tmpi, $tmpc) = mysql_fetch_row($res))
        	{
		        $query = "UPDATE `shop_items` SET `available`=`available`+".$tmpc." WHERE `id`='".$tmpi."' LIMIT 1;";
        		mysql_query($query) or die(mysql_error());
            }
		$SQL="SELECT status FROM shop_orders WHERE id='".addslashes($_REQUEST['cancelID'])."'";
		$d=mysql_fetch_array(mysql_query($SQL));
		if ($d['status']==1) {
			$SQL="DELETE FROM shop_orders WHERE id='".addslashes($_REQUEST['cancelID'])."'";
			mysql_query($SQL);
			$SQL="DELETE FROM shop_logs WHERE order_id='".addslashes($_REQUEST['cancelID'])."'";
			mysql_query($SQL);
			$SQL="DELETE FROM shop_temp WHERE order_id='".addslashes($_REQUEST['cancelID'])."'";
			mysql_query($SQL);
		}
		echo "<script>top.location='?act=shop2_orders';</script>";
}
	if(@$_REQUEST['getID']>0) {
		$SQL="UPDATE shop_orders SET status='0' WHERE id='".addslashes($_REQUEST['getID'])."'";
		mysql_query($SQL);
		$SQL="DELETE FROM shop_temp WHERE order_id='".addslashes($_REQUEST['getID'])."'";
		mysql_query($SQL);
		echo "<h6>Изменен статус у заказа ID:{$_REQUEST['getID']}.</h6>";
}

if($permission) {
	if (@$_REQUEST['error']) {
		echo "<h6 style=\"color: red\">".$_REQUEST['error']."</h6>";
	}
	if(@$_REQUEST['acceptID']>0) {
		$SQL="SELECT customer, resources, factory FROM shop_orders WHERE id='".addslashes($_REQUEST['acceptID'])."'";
		$d=mysql_fetch_array(mysql_query($SQL));
		$order=explode("|", $d['resources']);
		$resources = array();
		for ($i=0; $i<sizeof($order)-1; $i++) {
			list($a, $b) = explode("*", $order[$i]);
			$resources[$a] = $b;
		}
		$SQL="SELECT SUM(coins), SUM(polymers), SUM(organic), SUM(venom), SUM(radioactive), SUM(gold), SUM(gems), SUM(metals), SUM(silicon) FROM warehouses WHERE user_id='".$d['customer']."' AND warehouse='".$d['factory']."' AND status=0";
		$s=mysql_fetch_row(mysql_query($SQL));
		$f=mysql_fetch_array(mysql_query("SELECT name FROM buildings WHERE id='".$d['factory']."'"));
		for($i=0; $i<=8; $i++) if($s[$i]<$resources[$i]) $error="Недостаточно ресурсов на складе \"".$f['name']."\"";
		if ($error) {
			echo "<script>top.location='?act=shop2_orders&error=".$error."';</script>";
		} else {
			sleep(3);
			$SQL="INSERT INTO warehouses values('',
				'".time()."',
				'".$d['customer']."',
				'".AuthUserName."',
				'-".$resources[0]."',
				'-".$resources[7]."',
				'-".$resources[5]."',
				'-".$resources[1]."',
				'-".$resources[2]."',
				'-".$resources[3]."',
				'-".$resources[4]."',
				'-".$resources[6]."',
				'-".$resources[8]."',
				'".$d['factory']."',
				'0')";
			$r=mysql_query($SQL);
			$SQL="INSERT INTO warehouses values('',
				'".(time()+1)."',
				'1',
				'".AuthUserName."',
				'".$resources[0]."',
				'".$resources[7]."',
				'".$resources[5]."',
				'".$resources[1]."',
				'".$resources[2]."',
				'".$resources[3]."',
				'".$resources[4]."',
				'".$resources[6]."',
				'".$resources[8]."',
				'".$d['factory']."',
				'0')";
			$r=mysql_query($SQL);
			$SQL="UPDATE shop_orders SET status='2', accept='".AuthUserName."' WHERE id='".addslashes($_REQUEST['acceptID'])."' AND status<'4' AND status>'0'";
			mysql_query($SQL);
			$SQL="UPDATE shop_temp SET status='2' WHERE order_id='".addslashes($_REQUEST['acceptID'])."'";
			mysql_query($SQL);
			echo "<h6>Изменен статус у заказа ID:{$_REQUEST['acceptID']}.</h6>";
		}
	}
	if(@$_REQUEST['executeID']>0) {
		$SQL="UPDATE shop_orders SET status='3', t2='".time()."', execute='".AuthUserName."', stored_in='".addslashes($_REQUEST['place'])."' WHERE id='".addslashes($_REQUEST['executeID'])."'";
		mysql_query($SQL);
		$query = "SELECT `customer` FROM `shop_orders` WHERE `id` = '".addslashes($_REQUEST['executeID'])."' LIMIT 1;";
        $res = mysql_query($query);
		list ($client) = mysql_fetch_row($res);
		$query = "SELECT `factory`, `item_id`, `count` FROM `shop_temp` WHERE `order_id` = '".addslashes($_REQUEST['executeID'])."'";
        $res = mysql_query($query);
        while (list ($tmpf, $tmpi, $tmpc) = mysql_fetch_row($res))
        	{
		        $query = "INSERT INTO `shop_history` (`item`, `date`, `customer`, `quantity`, `plant`, producer) VALUES ('".$tmpi."', '".time()."', '".$client."', '".$tmpc."', '".$tmpf."', '".AuthUserName."');";
        		mysql_query($query) or die(mysql_error());
            }
		$SQL="UPDATE shop_temp SET status='0' WHERE order_id='".addslashes($_REQUEST['executeID'])."'";
		mysql_query($SQL);
		$layer = 1;
		echo "<h6>Изменен статус у заказа ID:{$_REQUEST['executeID']}.</h6>";
	}
	if(@$_REQUEST['packedID']>0) {
		$SQL="UPDATE shop_orders SET status='4', t2='".time()."', give_out='".AuthUserName."', stored_out='".addslashes($_REQUEST['place'])."' WHERE id='".addslashes($_REQUEST['packedID'])."'";
		mysql_query($SQL);
		$layer = 2;
		echo "<h6>Изменен статус у заказа ID:{$_REQUEST['packedID']}.</h6>";
	}
	if(@$_REQUEST['delID']>0) {
		$query = "SELECT `item_id`, `count` FROM `shop_temp` WHERE `order_id` = '".addslashes($_REQUEST['delID'])."'";
        $res = mysql_query($query);
        while (list ($tmpi, $tmpc) = mysql_fetch_row($res))
        	{
		        $query = "UPDATE `shop_items` SET `available`=`available`+".$tmpc." WHERE `id`='".$tmpi."' LIMIT 1;";
        		mysql_query($query) or die(mysql_error());
            }
		$SQL="DELETE FROM shop_orders WHERE id='".addslashes($_REQUEST['delID'])."'";
		mysql_query($SQL);
		$SQL="DELETE FROM shop_temp WHERE order_id='".addslashes($_REQUEST['delID'])."'";
		mysql_query($SQL);
		$SQL="DELETE FROM shop_logs WHERE order_id='".addslashes($_REQUEST['cancelID'])."'";
		mysql_query($SQL);
		echo "<h6>Заказ ID:$delID удален из базы.</h6>";
	}
	if(@$_REQUEST['view_history']) $layer = 4;
}
?>
<link href="_modules/tabs.css" type=text/css rel=stylesheet>
<script language=javascript src="_modules/tabs.js"></script>
<script language="Javascript" type="text/javascript">
<? if($permission) { ?>
function updReady(id) {
	if (pl = window.prompt("Укажите, где находится заказ", "")) {
		if (!pl) alert("Необходимо указать место, где находится заказ!")
		else {
			document.ready_form.place.value = pl;
			document.ready_form.executeID.value = id;
			document.ready_form.submit();
		}
	}
}
function updPacked(id) {
	if (pl = window.prompt("Укажите, где можно забрать заказ", "")) {
		if (!pl) alert("Необходимо указать место, где можно забрать заказ!")
		else {
			document.packed_form.place.value = pl;
			document.packed_form.packedID.value = id;
			document.packed_form.submit();
		}
	}
}
<? } ?>
<?$d=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS maxx FROM shop_orders WHERE customer='".AuthUserId."' AND status=0"));?>
function show_history() {
	for (var i=1; i<=<?=$d['maxx']?>; i++) {
		if (document.all('l'+i).style.display == 'none') {
			document.all('l'+i).style.display = '';
		} else {
			document.all('l'+i).style.display = 'none';
		}
	}
}
</script>
<? if($permission) { ?>

<form name="ready_form" method="post" action="?act=shop2_orders">
<input type="hidden" name="place" value="">
<input type="hidden" name="executeID" value="">
</form>
<form name="packed_form" method="post" action="?act=shop2_orders">
<input type="hidden" name="place" value="">
<input type="hidden" name="packedID" value="">
</form>

<form action="" method="post">
<table border="0" cellspacing="0" cellpadding="0" width="100%" id="tb_content">
<tr>
  <td height="20" class="tab-<?=($layer?'off':'on')?>" id="navcell" onclick="switchCell(0)" valign="middle"><b>&nbsp;Новые&nbsp;заказы&nbsp;</b></td>
  <td height="20" class="tab-<?=($layer==1?'on':'off')?>" id="navcell" onclick="switchCell(1)" valign="middle"><b>&nbsp;Принятые&nbsp;заказы&nbsp;</b></td>
  <td height="20" class="tab-<?=($layer==2?'on':'off')?>" id="navcell" onclick="switchCell(2)" valign="middle"><b>&nbsp;Изготовленные&nbsp;заказы&nbsp;</b></td>
  <td height="20" class="tab-off" id="navcell" onclick="switchCell(3)" valign="middle"><b>&nbsp;Неподтверждённые&nbsp;заказы&nbsp;</b></td>
  <td height="20" class="tab-<?=($layer==4?'on':'off')?>" id="navcell" onclick="switchCell(4)" valign="middle"><b>&nbsp;История&nbsp;заказов&nbsp;</b></td>
  <td class=tab-none noWrap><FONT face=Tahoma color="#ffffff">&nbsp;</TD>
</tr>
</table>

<!-- Новые заказы -->
<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0">
<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Новые заказы:</strong> </p></td></tr>
<tr><td align="center"><br>

<table width=97% cellpadding=5>

<?
$SQL="SELECT i.*, u.id AS UID, u.user_name AS UName, u.clan AS UClan FROM shop_orders i LEFT JOIN site_users u on u.id=i.customer WHERE i.status='1' ORDER by i.t1 DESC";
$r=mysql_query($SQL);
if(mysql_num_rows($r)>0) {
?>
<tr  bgcolor=#F4ECD4 align=center>
<td width=10><b>ID:</b></td>
<td><b>Заказчик:</b></td>
<td width=100><b>Завод:</b></td>
<td><b>Заказ:</b></td>
<td width=100><b>Стоимость:</b></td>
<td width=100><b>Изменить статус:</b></td>
</tr>

<?
} else echo "<tr><td align=center> n/a </td></tr>\n";

$np=0;
while($d=mysql_fetch_array($r)) {
if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

$factory = mysql_fetch_array(mysql_query("SELECT name FROM buildings WHERE id=".$d['factory'].""));

$order = str_replace('|*',' <b>',$d['content']);
$order = str_replace('|;','шт</b><br>',$order);
$time = substr(strrchr($d['resources'],'|'),1);
$res = substr($d['resources'],0,strrpos($d['resources'],'|'));
$res = str_replace('|','<br>',$res);
$res = str_replace('0*','<b>coins:</b> ',$res);
$res = str_replace('1*','<b>polymers:</b> ',$res);
$res = str_replace('2*','<b>organic:</b> ',$res);
$res = str_replace('3*','<b>venom:</b> ',$res);
$res = str_replace('4*','<b>radioactive:</b> ',$res);
$res = str_replace('5*','<b>gold:</b> ',$res);
$res = str_replace('6*','<b>gem:</b> ',$res);
$res = str_replace('7*','<b>metal:</b> ',$res);
$res = str_replace('8*','<b>silicon:</b> ',$res);

$time_str = '';

if ($time >= 60) {
	$time_str .= floor($time/60) . "ч ";
	$time = $time%60;
}
$time_str .= $time . "мин";
?>
<tr>
<td <?=$bg?> nowrap valign=top><b><?=$d['id']?> [<a href='#; return false;' onclick="if(confirm('Вы уверены?')) top.location='?act=shop2_orders&delID=<?=$d['id']?>'">X</a>]</b></b></td>
<td <?=$bg?> nowrap valign=top><?=GetClan($d['UClan']).GetUser($d['UID'],$d['UName'],AuthUserGroup)?><br><?=date("d.m.y H:i",$d['t1'])?></td>
<td <?=$bg?> align=center valign=top><b><?=$factory['name']?></b><br>(<?=$time_str?>)</td>
<td <?=$bg?> valign=top><?=$order?></td>
<td <?=$bg?> valign=top nowrap><?=$res?></td>
<td <?=$bg?> align=center>
<a href="?act=shop2_orders&acceptID=<?=$d['id']?>">оплата получена</a><br>
<a href="?act=shop2_orders&delID=<?=$d['id']?>">удалить заказ</a>
</td>
</tr>

<?}?>
</table><br>

</td></tr></table>
<!-- Принятые заказы -->
<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0">
<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Принятые заказы:</strong> </p></td>
</tr><tr><td align="center"><br>

<table width=97% cellpadding=5>

<?
$SQL="SELECT i.*, u.id AS UID, u.user_name AS UName, u.clan AS UClan FROM shop_orders i LEFT JOIN site_users u on u.id=i.customer WHERE i.status='2' ORDER by i.t1 DESC";
$r=mysql_query($SQL);
if(mysql_num_rows($r)>0) {
?>
<tr  bgcolor=#F4ECD4 align=center>
<td width=10><b>ID:</b></td>
<td><b>Заказчик:</b></td>
<td width=100><b>Завод:</b></td>
<td><b>Заказ:</b></td>
<td width=100><b>Стоимость:</b></td>
<td width=100><b>Изменить статус:</b></td>
</tr>

<?
} else echo "<tr><td align=center> n/a </td></tr>";

$np=0;
while($d=mysql_fetch_array($r)) {
if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

$factory = mysql_fetch_array(mysql_query("SELECT name FROM buildings WHERE id=".$d['factory'].""));

$order = str_replace('|*',' <b>',$d['content']);
$order = str_replace('|;','шт</b><br>',$order);
$time = substr(strrchr($d['resources'],'|'),1);
$res = substr($d['resources'],0,strrpos($d['resources'],'|'));
$res = str_replace('|','<br>',$res);
$res = str_replace('0*','<b>coins:</b> ',$res);
$res = str_replace('1*','<b>polymers:</b> ',$res);
$res = str_replace('2*','<b>organic:</b> ',$res);
$res = str_replace('3*','<b>venom:</b> ',$res);
$res = str_replace('4*','<b>radioactive:</b> ',$res);
$res = str_replace('5*','<b>gold:</b> ',$res);
$res = str_replace('6*','<b>gem:</b> ',$res);
$res = str_replace('7*','<b>metal:</b> ',$res);
$res = str_replace('8*','<b>silicon:</b> ',$res);

$time_str = '';
if ($time >= 60) {
	$time_str .= floor($time/60) . "ч ";
	$time = $time%60;
}
$time_str .= $time . "мин";
?>
<tr>
<td <?=$bg?> nowrap valign=top><b><?=$d['id']?></b></td>
<td <?=$bg?> nowrap valign=top><?=GetClan($d['UClan']).GetUser($d['UID'],$d['UName'],AuthUserGroup)?><br><?=date("d.m.y H:i",$d['t1'])?></td>
<td <?=$bg?> align=center valign=top><b><?=$factory['name']?></b><br>(<?=$time_str?>)</td>
<td <?=$bg?> valign=top><?=$order?></td>
<td <?=$bg?> valign=top nowrap><?=$res?></td>
<td <?=$bg?> align=center>
Принят <b><?=$d['accept']?></b><br>
<a href="#" onClick="updReady('<?=$d['id']?>'); return false">заказ изготовлен</a>
</td></tr>

<?}?>
</table><br>
</td></tr></table>

<!-- Изготовленные заказы -->
<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0">
<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Изготовленные заказы:</strong> </p></td></tr>
<tr><td align="center"><br>

<table width=97% cellpadding=5>

<?
$SQL="SELECT i.*, u.id AS UID, u.user_name AS UName, u.clan AS UClan FROM shop_orders i LEFT JOIN site_users u on u.id=i.customer WHERE i.status='3' ORDER by u.id";
$r=mysql_query($SQL);
if(mysql_num_rows($r)>0) {
?>
<tr  bgcolor=#F4ECD4 align=center>
<td width=10><b>ID:</b></td>
<td><b>Заказчик:</b></td>
<td width=100><b>Завод:</b></td>
<td><b>Заказ:</b></td>
<td width=100><b>Стоимость:</b></td>
<td width=120><b>Местонахождение:</b></td>
<td width=90><b>Изменить статус:</b></td>
</tr>

<?
} else echo "<tr><td align=center> n/a </td></tr>";

$np=0;
while($d=mysql_fetch_array($r)) {
if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

$factory = mysql_fetch_array(mysql_query("SELECT name FROM buildings WHERE id=".$d['factory'].""));

$order = str_replace('|*',' <b>',$d['content']);
$order = str_replace('|;','шт</b><br>',$order);
$time = substr(strrchr($d['resources'],'|'),1);
$res = substr($d['resources'],0,strrpos($d['resources'],'|'));
$res = str_replace('|','<br>',$res);
$res = str_replace('0*','<b>coins:</b> ',$res);
$res = str_replace('1*','<b>polymers:</b> ',$res);
$res = str_replace('2*','<b>organic:</b> ',$res);
$res = str_replace('3*','<b>venom:</b> ',$res);
$res = str_replace('4*','<b>radioactive:</b> ',$res);
$res = str_replace('5*','<b>gold:</b> ',$res);
$res = str_replace('6*','<b>gem:</b> ',$res);
$res = str_replace('7*','<b>metal:</b> ',$res);
$res = str_replace('8*','<b>silicon:</b> ',$res);

$time_str = '';
if ($time >= 60) {
	$time_str .= floor($time/60) . "ч ";
	$time = $time%60;
}
$time_str .= $time . "мин";
?>
<tr>
<td <?=$bg?> nowrap valign=top><b><?=$d['id']?></b></td>
<td <?=$bg?> nowrap valign=top><?=GetClan($d['UClan']).GetUser($d['UID'],$d['UName'],AuthUserGroup)?><br><?=date("d.m.y H:i",$d['t1'])?></td>
<td <?=$bg?> align=center valign=top><b><?=$factory['name']?></b><br>(<?=$time_str?>)</td>
<td <?=$bg?> valign=top><?=$order?></td>
<td <?=$bg?> valign=top nowrap><?=$res?></td>
<td <?=$bg?> align=center>
<?
echo ("Изготовлено <b>".stripslashes($d['execute'])."</b><br>");
if(strlen($d['stored_in']) > 0) {
	echo '<b>('.(stripslashes($d['stored_in'])).')</b>';
} else {
	echo ("n/a");
}
	echo '<br><b>'.(date ("d.m.Y H:i", $d['t2'])).'</b>';
?>
</td>
<td <?=$bg?> align=center>
<a href="#" onClick="updPacked('<?=$d['id']?>'); return false">заказ готов к выдаче</a>
</td></tr>

<?}?>
</table><br>

</td></tr></table>

<!-- Неподтвержденные заказы -->
<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0">
<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Неподтверждённые заказы:</strong> </p></td></tr>
<tr><td align="center"><br>

<table width=97% cellpadding=5>

<?
$SQL="SELECT i.*, u.id AS UID, u.user_name AS UName, u.clan AS UClan FROM shop_orders i LEFT JOIN site_users u on u.id=i.customer WHERE i.status='4' ORDER by u.id";
$r=mysql_query($SQL);
if(mysql_num_rows($r)>0) {
?>
<tr  bgcolor=#F4ECD4 align=center>
<td width=10 nowrap><b>ID:</b></td>
<td><b>Заказчик:</b></td>
<td width=100><b>Завод:</b></td>
<td><b>Заказ:</b></td>
<td width=100><b>Стоимость:</b></td>
<td width=100><b>Местонахождение:</b></td>
</tr>

<?
} else echo "<tr><td align=center> n/a </td></tr>";

$np=0;
while($d=mysql_fetch_array($r)) {
if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

$factory = mysql_fetch_array(mysql_query("SELECT name FROM buildings WHERE id=".$d['factory'].""));

$order = str_replace('|*',' <b>',$d['content']);
$order = str_replace('|;','шт</b><br>',$order);
$time = substr(strrchr($d['resources'],'|'),1);
$res = substr($d['resources'],0,strrpos($d['resources'],'|'));
$res = str_replace('|','<br>',$res);
$res = str_replace('0*','<b>coins:</b> ',$res);
$res = str_replace('1*','<b>polymers:</b> ',$res);
$res = str_replace('2*','<b>organic:</b> ',$res);
$res = str_replace('3*','<b>venom:</b> ',$res);
$res = str_replace('4*','<b>radioactive:</b> ',$res);
$res = str_replace('5*','<b>gold:</b> ',$res);
$res = str_replace('6*','<b>gem:</b> ',$res);
$res = str_replace('7*','<b>metal:</b> ',$res);
$res = str_replace('8*','<b>silicon:</b> ',$res);

$time_str = '';
if ($time >= 60) {
	$time_str .= floor($time/60) . "ч ";
	$time = $time%60;
}
$time_str .= $time . "мин";
?>
<tr>
<td <?=$bg?> nowrap valign=top><b><?=$d['id']?> [<a href='#; return false;' onclick="if(confirm('Вы уверены?')) top.location='?act=shop2_orders&getID=<?=$d['id']?>'">X</a>]</b></td>
<td <?=$bg?> nowrap valign=top><?=GetClan($d['UClan']).GetUser($d['UID'],$d['UName'],AuthUserGroup)?><br><?=date("d.m.y H:i",$d['t1'])?></td>
<td <?=$bg?> align=center valign=top><b><?=$factory['name']?></b><br>(<?=$time_str?>)</td>
<td <?=$bg?> valign=top><?=$order?></td>
<td <?=$bg?> valign=top nowrap><?=$res?></td>
<td <?=$bg?> align=center>
<?
echo ("Выдал <b>".stripslashes($d['give_out'])."</b><br>");
if(strlen($d['stored_out']) > 0) {
	echo '('.(stripslashes($d['stored_out'])).')';
} else {
	echo ("n/a");
}
	echo '<br><b>'.(date ("d.m.Y H:i", $d['t2'])).'</b>';
?>
</td></tr>

<?}?>
</table><br>

</td></tr></table>

<!-- История заказов -->
<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0">
<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>История заказов:</strong> </p></td></tr>
<tr><td align="center"><br>

<form method="GET">
<input name="act" type="hidden" value="shop2_orders">
Вся история: <select size="1" name="view_history" onchange="submit();">
<option value='' selected>-- select user --</option>
<?
$r=mysql_query("SELECT DISTINCT o.customer, u.user_name FROM shop_orders o LEFT JOIN site_users u ON u.id=o.customer ORDER BY u.user_name");
while($data=mysql_fetch_array($r)) echo "<option value='{$data['customer']}'>{$data['user_name']}</option>\n";
?>
</select><br>
</form>

<?
if(@$_REQUEST['view_history']) {
$SQL="SELECT i.*, u.id AS UID, u.user_name AS UName, u.clan AS UClan FROM shop_orders i LEFT JOIN site_users u on u.id=i.customer WHERE (i.status='0' OR i.status='3') AND i.customer='".htmlspecialchars($_REQUEST['view_history'])."' ORDER by i.t1 DESC";
$r=mysql_query($SQL);
?>
<table width=97% cellpadding=5>
<?
if(mysql_num_rows($r)>0) {
?>
<tr  bgcolor=#F4ECD4 align=center>
<td width=10 nowrap><b>ID:</b></td>
<td><b>Заказчик:</b></td>
<td width=100><b>Завод:</b></td>
<td><b>Заказ:</b></td>
<td width=100><b>Стоимость:</b></td>
<td width=110><b>Отметка о получении:</b></td>
</tr>

<?
} else echo "<tr><td align=center> n/a </td></tr>";

$np=0;
while($d=mysql_fetch_array($r)) {
if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

$factory = mysql_fetch_array(mysql_query("SELECT name FROM buildings WHERE id=".$d['factory'].""));

$order = str_replace('|*',' <b>',$d['content']);
$order = str_replace('|;','шт</b><br>',$order);
$time = substr(strrchr($d['resources'],'|'),1);
$res = substr($d['resources'],0,strrpos($d['resources'],'|'));
$res = str_replace('|','<br>',$res);
$res = str_replace('0*','<b>coins:</b> ',$res);
$res = str_replace('1*','<b>polymers:</b> ',$res);
$res = str_replace('2*','<b>organic:</b> ',$res);
$res = str_replace('3*','<b>venom:</b> ',$res);
$res = str_replace('4*','<b>radioactive:</b> ',$res);
$res = str_replace('5*','<b>gold:</b> ',$res);
$res = str_replace('6*','<b>gem:</b> ',$res);
$res = str_replace('7*','<b>metal:</b> ',$res);
$res = str_replace('8*','<b>silicon:</b> ',$res);

$time_str = '';
if ($time >= 60) {
	$time_str .= floor($time/60) . "ч ";
	$time = $time%60;
}
$time_str .= $time . "мин";
?>
<tr>
<td <?=$bg?> nowrap valign=top><b><?=$d['id']?></b></td>
<td <?=$bg?> nowrap valign=top><?=GetClan($d['UClan']).GetUser($d['UID'],$d['UName'],AuthUserGroup)?><br><?=date("d.m.y H:i",$d['t1'])?></td>
<td <?=$bg?> align=center valign=top><b><?=$factory['name']?></b><br>(<?=$time_str?>)</td>
<td <?=$bg?> valign=top><?=$order?></td>
<td <?=$bg?> valign=top nowrap><?=$res?></td>
<td <?=$bg?> align=center><?
if($d['status']=='0') echo "заказ получен";
echo '<br><b>'.(date ("d.m.Y H:i", $d['t2'])).'</b>';
?></td>
</tr>

<?}?>
</table><br>

<?}?>
</td></tr></table>

<?}?>

<!-- Свои заказы -->
<table width='100%' border='0' cellspacing='3' cellpadding='2'>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Мои заказы:</strong> </p></td></tr>
<tr><td>

<table width=100% cellpadding=5>
<tr  bgcolor=#F4ECD4 align=center>
<td width=10 nowrap><b>ID:</b></td>
<td><b>Заказ:</b></td>
<td width=100><b>Время подачи:</b></td>
<td><b>Стоимость:</b></td>
<td width=100><b>Статус:</b></td>
<td width=100><b>Время выдачи:</b></td>
<td width=100><b>Место выдачи:</b></td>
</tr>

<?
$SQL="SELECT * FROM shop_orders WHERE customer='".AuthUserId."' ORDER BY status DESC, t1 DESC";
$r=mysql_query($SQL);
$BeginOld=0;

while($d=mysql_fetch_array($r)) {

$factory = mysql_fetch_array(mysql_query("SELECT name FROM buildings WHERE id=".$d['factory'].""));

$order = str_replace('|*',' <b>',$d['content']);
$order = str_replace('|;','шт</b><br>',$order);
$time = substr(strrchr($d['resources'],'|'),1);
$res = substr($d['resources'],0,strrpos($d['resources'],'|'));
$res = str_replace('|','<br>',$res);
$res = str_replace('0*','<b>coins:</b> ',$res);
$res = str_replace('1*','<b>polymers:</b> ',$res);
$res = str_replace('2*','<b>organic:</b> ',$res);
$res = str_replace('3*','<b>venom:</b> ',$res);
$res = str_replace('4*','<b>radioactive:</b> ',$res);
$res = str_replace('5*','<b>gold:</b> ',$res);
$res = str_replace('6*','<b>gem:</b> ',$res);
$res = str_replace('7*','<b>metal:</b> ',$res);
$res = str_replace('8*','<b>silicon:</b> ',$res);

$time_str = '';
if ($time >= 60) {
	$time_str .= floor($time/60) . "ч ";
	$time = $time%60;
}
$time_str .= $time . "мин";

if ($d['status'] == 0) $BeginOld++;
if ($BeginOld == 1) {
?>

<tr bgcolor=#F4ECD4 align=center>
<td colspan=7><a onclick="show_history()" href='#;return false;'>Показать/скрыть полученные заказы</a></td>
</tr>
<? } ?>
<tr<?=($BeginOld?' id="l'.$BeginOld.'" style="display: none"':'')?>>
<td width=10 nowrap valign=top><b><?=$d['id']?></b></td>
<td valign=top nowrap><?=$order?></td>
<td width=100 align=center><?=date("d.m.y H:i",$d['t1'])?><br>(<b><?=$time_str?></b>)</td>
<td  valign=top nowrap><?=$res?></td>
<td width=100 align=center>
<?
if($d['status']=='1') {
	echo "заказ отправлен<br>Вы еще можете его <a href='?act=shop2_orders&cancelID={$d['id']}'>отменить</a>.";
} elseif ($d['status']=='2' || $d['status']=='3') {
	echo "заказ принят к исполнению";
}
elseif ($d['status']=='4') {
	if($_REQUEST['getID']==$d['id']) {
		mysql_query("UPDATE items_order SET order_status='0' WHERE order_status='4' AND id='{$d[id]}'");
		echo "<script>top.location='?act=shop2';</script>";
	}
	echo "заказ готов<br><a href='#; return false;' onclick=\"if(confirm('Вы получили заказ?')) top.location='?act=shop2_orders&getID={$d['id']}'\">сделайте пометку</a> о получении.";
} elseif($d['status']=='0') echo "заказ получен";
?>
</td>
<td width=100 align=center>
<?
if($d['t2']>0 && ($d['status']=='4' || $d['status']=='0')) echo date("d.m.y H:i",$d['t2']); else echo "n/a";
?>
</td>
<td width=100 align=center>
<?
if(strlen($d['stored_out']) > 0) echo (stripslashes($d['stored_out'])); else echo "n/a";
?>
</td>
</tr>

<?}?>
</table>

</td></tr>
</table>

<? if($permission) { ?>
<script language="Javascript" type="text/javascript">
switchCell(<?=($layer?$layer:'0')?>);
</script>

<?
}

} else echo $mess['AccessDenied'];
?>