<h1>Производство по заказам интернет-магазина "военторгЪ"</h1>

<?php
$bgstr[0]="background='i/bgr-grid-sand.gif'";
$bgstr[1]="background='i/bgr-grid-sand1.gif'";

if(AuthStatus==1 && AuthUserName!="" && (AuthUserClan=='police' || AuthUserClan=='Financial Academy' || AuthUserClan=='Police Academy')) {

$permission=0;
$SQL = "SELECT * FROM build_users WHERE user_id='".AuthUserId."'";
$r = mysql_query($SQL);
if ($d=mysql_fetch_array($r)) $permission=$d['factory'];

if($permission) {
	if (@$_REQUEST['error']) {
		echo "<h6 style=\"color: red\">".$_REQUEST['error']."</h6>";
	}
	if (@$_REQUEST['executeID']) {
		$SQL="SELECT factory FROM shop_temp WHERE factory='".$_REQUEST['f']."' AND status=3";
		$r=mysql_query($SQL);
		if (mysql_num_rows($r)>0) {
			echo "<h6 style=\"color: red\">Завод занят!</h6>";
		} else {
			$SQL="SELECT id_plant FROM p_orders WHERE status=2 AND id_plant='".$_REQUEST['f']."'";
			$r=mysql_query($SQL);
			if (mysql_num_rows($r)>0) {
				echo "<h6 style=\"color: red\">Завод занят!</h6>";
			} else {
				if ($d=mysql_fetch_array(mysql_query("SELECT * FROM p_items WHERE shop_iid='".$_REQUEST['executeID']."'"))) {
					$s=mysql_fetch_array(mysql_query("SELECT SUM(count) as q FROM shop_temp WHERE status=2 AND factory='".$_REQUEST['f']."' AND item_id='".$_REQUEST['executeID']."'"));
					$SQL="UPDATE p_items SET num=num-".$s['q'].", id_plant='".$_REQUEST['f']."' WHERE id_item='".$d['id_item']."'";
					mysql_query($SQL);
					$SQL="INSERT INTO p_orders values('','".htmlspecialchars($_REQUEST['forwhom'])."','".time()."','".time()."','{$d['id_item']}','{$s['q']}',2,'{$_REQUEST['f']}',0)";
					mysql_query($SQL);
				}
				$SQL="UPDATE shop_temp SET status=3 WHERE item_id='".$_REQUEST['executeID']."' AND factory='".$_REQUEST['f']."' AND status=2";
				mysql_query($SQL);
				echo "<h6>Производство запущено</h6>";
			}
		}
	}
	if (@$_REQUEST['readyID']) {
		if ($d=mysql_fetch_array(mysql_query("SELECT * FROM p_items WHERE shop_iid='".$_REQUEST['executeID']."'"))) {
			$SQL="UPDATE p_orders SET status=1 WHERE id_item='".$_REQUEST['executeID']."' AND status=2";
			mysql_query($SQL);
		}
		$SQL="SELECT order_id FROM shop_temp WHERE item_id='".$_REQUEST['readyID']."' AND factory='".$_REQUEST['f']."' AND status=3";
		$r=mysql_query($SQL);
		$SQL="UPDATE shop_temp SET status=0 WHERE item_id='".$_REQUEST['readyID']."' AND factory='".$_REQUEST['f']."' AND status=3";
		mysql_query($SQL);
		while ($d=mysql_fetch_array($r)) {
			$s=mysql_fetch_array(mysql_query("SELECT SUM(status) as status FROM shop_temp WHERE order_id='".$d['order_id']."'"));
			if ($s['status']==0) {
				$SQL="UPDATE shop_orders SET status='3', t2='".time()."', execute='".AuthUserName."', stored_in='склад завода' WHERE id='".$d['order_id']."'";
				mysql_query($SQL);
				echo "<h6>Заказ ID: ".$d['order_id']." готов</h6>";
			}
		}
	}
?>


<link href="_modules/tabs.css" type=text/css rel=stylesheet>
<script language=javascript src="_modules/tabs.js"></script>

<form action="" method="post">

<table border="0" cellspacing="0" cellpadding="0" width="100%" id="tb_content">
<tr>
  <td height="20" class="tab-<?=($layer?'off':'on')?>" id="navcell" onclick="switchCell(0)" valign="middle"><b>&nbsp;Нужно&nbsp;изготовить&nbsp;</b></td>
  <td height="20" class="tab-<?=($layer==1?'on':'off')?>" id="navcell" onclick="switchCell(1)" valign="middle"><b>&nbsp;В&nbsp;процессе&nbsp;изготовления&nbsp;</b></td>
  <td height="20" class="tab-<?=($layer==2?'on':'off')?>" id="navcell" onclick="switchCell(2)" valign="middle"><b>&nbsp;Изготовленно&nbsp;</b></td>
  <td class=tab-none noWrap><FONT face=Tahoma color="#ffffff">&nbsp;</TD>
</tr>
</table>

<!-- Нужно изготовить -->
<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0">
<?
$SQL="SELECT factory, item_id, SUM(count) as col FROM shop_temp WHERE status=2 GROUP BY factory, item_id ORDER BY factory, item_id";
$r=mysql_query($SQL);
if (mysql_num_rows($r)==0) echo "<tr><td><br><table width=97% cellpadding=5>\n";
while ($d=mysql_fetch_array($r)) {
	if ($fid != $d['factory']) {
		$np=0;
		if ($fid) echo "</table><br></td></tr>\n";
		$fid = $d['factory'];
		$s=mysql_fetch_array(mysql_query("SELECT name FROM buildings WHERE id='".$fid."'"));
?>
<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><?=$s['name']?>:</strong> </p></td></tr>
<tr><td align="center"><br>

<table width=97% cellpadding=5>

<tr bgcolor=#F4ECD4 align=center>
<td><b>Чертеж:</b></td>
<td width=80><b>Количество:</b></td>
<td width=80><b>Время:</b></td>
<td width=160><b>Действие:</b></td>
</tr>
<?
	}
	if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}
	$s=mysql_fetch_array(mysql_query("SELECT * FROM shop_items WHERE id='".$d['item_id']."'"));
	$time = $d['col']*$s['time_req'];
	$time_str = '';
	if ($time >= 60) {
		$time_str .= floor($time/60) . "ч ";
		$time = $time%60;
	}
	$time_str .= $time . "мин";
?>

<tr>
<td <?=$bg?>><?=$s['item_name']?></td>
<td <?=$bg?> align=center><?=$d['col']?></td>
<td <?=$bg?> align=center><?=$time_str?></td>
<td <?=$bg?> align=center><a href="?act=shop2_factories&f=<?=$fid?>&executeID=<?=$d['item_id']?>">Запустить производство</a></td>
</tr>
<?
}
if (mysql_num_rows($r)==0) echo "<tr><td align=center> n/a </td></tr>\n";
?>

</table><br>

</td></tr></table>

<!-- В процессе изготовления -->
<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0">
<?
$fid = "";
$SQL="SELECT factory, item_id, SUM(count) as col FROM shop_temp WHERE status=3 GROUP BY factory, item_id ORDER BY factory, item_id";
$r=mysql_query($SQL);
if (mysql_num_rows($r)==0) echo "<tr><td><br><table width=97% cellpadding=5>\n";
while ($d=mysql_fetch_array($r)) {
	if ($fid != $d['factory']) {
		$np=0;
		if ($fid) echo "</table><br></td></tr>\n";
		$fid = $d['factory'];
		$s=mysql_fetch_array(mysql_query("SELECT name FROM buildings WHERE id='".$fid."'"));
?>
<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><?=$s['name']?>:</strong> </p></td></tr>
<tr><td align="center"><br>

<table width=97% cellpadding=5>

<tr bgcolor=#F4ECD4 align=center>
<td><b>Чертеж:</b></td>
<td width=80><b>Количество:</b></td>
<td width=80><b>Время:</b></td>
<td width=100><b>Действие:</b></td>
</tr>
<?
	}
	if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}
	$s=mysql_fetch_array(mysql_query("SELECT * FROM shop_items WHERE id='".$d['item_id']."'"));
	$time = $d['col']*$s['time_req'];
	$time_str = '';
	if ($time >= 60) {
		$time_str .= floor($time/60) . "ч ";
		$time = $time%60;
	}
	$time_str .= $time . "мин";
?>

<tr>
<td <?=$bg?>><?=$s['item_name']?></td>
<td <?=$bg?> align=center><?=$d['col']?></td>
<td <?=$bg?> align=center><?=$time_str?></td>
<td <?=$bg?> align=center><a href="?act=shop2_factories&f=<?=$fid?>&readyID=<?=$d['item_id']?>">Произведено</a></td>
</tr>
<?
}
if (mysql_num_rows($r)==0) echo "<tr><td align=center> n/a </td></tr>\n";
?>

</table><br>

</td></tr></table>

<!-- Изготовленно -->
<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0">
<?
$fid = "";
$SQL="SELECT factory, item_id, SUM(count) as col FROM shop_temp WHERE status=0 GROUP BY factory, item_id ORDER BY factory, item_id";
$r=mysql_query($SQL);
if (mysql_num_rows($r)==0) echo "<tr><td><br><table width=97% cellpadding=5>\n";
while ($d=mysql_fetch_array($r)) {
	if ($fid != $d['factory']) {
		$np=0;
		if ($fid) echo "</table><br></td></tr>\n";
		$fid = $d['factory'];
		$s=mysql_fetch_array(mysql_query("SELECT name FROM buildings WHERE id='".$fid."'"));
?>
<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><?=$s['name']?>:</strong> </p></td></tr>
<tr><td align="center"><br>

<table width=97% cellpadding=5>

<tr bgcolor=#F4ECD4 align=center>
<td><b>Чертеж:</b></td>
<td width=80><b>Количество:</b></td>
</tr>
<?
	}
	if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}
	$s=mysql_fetch_array(mysql_query("SELECT * FROM shop_items WHERE id='".$d['item_id']."'"));
?>

<tr>
<td <?=$bg?>><?=$s['item_name']?></td>
<td <?=$bg?> align=center><?=$d['col']?></td>
</tr>
<?
}
if (mysql_num_rows($r)==0) echo "<tr><td align=center> n/a </td></tr>\n";
?>

</table><br>

</td></tr></table>

</form>

<script language="Javascript" type="text/javascript">
switchCell(<?=($layer?$layer:'0')?>);
</script>

<?
}

} else echo $mess['AccessDenied'];
?>