<h1>Складские переводы</h1>

<?php

$bgstr[0]="background='i/bgr-grid-sand.gif'";
$bgstr[1]="background='i/bgr-grid-sand1.gif'";

if(AuthUserGroup == 100) {

$buildings = array();
$SQL = "SELECT * FROM buildings";
$r=mysql_query($SQL);
while ($d=mysql_fetch_array($r)) {
	$buildings[$d['id']] = $d['name'];
}
?>

<table width='100%' border='0' cellspacing='3' cellpadding='2'>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Детализировать по персонажу:</strong> </p></td></tr>
<tr><td>

<form action="?act=wlogs" method="POST">
<input name="a" type="hidden" value="show">
<table>
<tr>
<td align=center>Начало</td>
<td align=center>Конец</td>
<td>Персонаж</td>
<td>Склад</td>
</tr>
<tr>
<td><input type=text name="dt1" value="<?=($_POST['dt1']?$_POST['dt1']:date("d.m.y",time()))?>" size=8></td>
<td><input type=text name="dt2" value="<?=($_POST['dt2']?$_POST['dt2']:date("d.m.y",time()))?>" size=8></td>
<td><input name="name" type="text" value="<?=$_POST['name']?>" size=30></td>
<td><select name="warehouse">
<?
foreach (array_keys($buildings) as $fid) {
?>
<option value="<?=$fid?>"><?=$buildings[$fid]?></option>
<? } ?>
</select></td>
</tr>
<tr>
<td colspan=11 align="center"><input type="submit" value="Показать"></td>
</tr>
</table>
</form>

</td></tr>
</table>

<?
$SQL="SELECT id, clan FROM site_users WHERE user_name='".$_POST['name']."'";
$r=mysql_query($SQL);
$d=mysql_fetch_array($r);
if ($d['id']>0 || $_POST['name']=='') {
?>
<table width='100%' border='0' cellspacing='3' cellpadding='2'>
<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><?if($_POST['name']==''){?>Переводы персонажей<?}else{?>Переводы персонажа <b><?=GetClan($d['clan']).$_POST['name']?></b>:<?}?></strong> </p></td></tr>

<tr><td align="center">

<table width='100%' border='0' cellspacing='3' cellpadding='2'>

<tr><td>

<table width=100%>

<tr bgcolor=#F4ECD4>
<td><b>Положил:</b></td>
<td align=center><img src="_imgs/tz/coins.gif" height=21></td>
<td align=center><img src="_imgs/tz/polymer.gif" height=21></td>
<td align=center><img src="_imgs/tz/organic.gif" height=21></td>
<td align=center><img src="_imgs/tz/venom.gif" height="21"></td>
<td align=center><img src="_imgs/tz/rad.gif" height="21"></td>
<td align=center><img src="_imgs/tz/gold.gif" height=21></td>
<td align=center><img src="_imgs/tz/gem.gif" height="21"></td>
<td align=center><img src="_imgs/tz/metal.gif" height=21></td>
<td align=center><img src="_imgs/tz/silicon.gif" height="21"></td>
</tr>
<tr><td colspan=11 height=4></td></tr>

<?
$day1 = substr($_POST['dt1'],0,2);
$month1 = substr($_POST['dt1'],3,2);
$year1 = '20'.substr($_POST['dt1'],6,2);
$day2 = substr($_POST['dt2'],0,2);
$month2 = substr($_POST['dt2'],3,2);
$year2 = '20'.substr($_POST['dt2'],6,2);

$np=0;
$SQL="SELECT * FROM warehouses WHERE dt>='".mktime(0,0,0,$month1,$day1,$year1)."' AND dt<='".mktime(23,59,59,$month2,$day2,$year2)."'".($d['id']>0?" AND user_id='".$d['id']."'":"")." AND warehouse='".$_POST['warehouse']."' AND status=0 ORDER BY user_id, dt";
$r=mysql_query($SQL);
while ($d=mysql_fetch_array($r)) {
	if ($uid!=$d['user_id'] && $uid!='') {
?>
<tr bgcolor=#F4ECD4>
<td><b>Итого: </b></td>
<td align=center><b><?=$s['coins']?></b></td>
<td align=center><b><?=$s['polymers']?></b></td>
<td align=center><b><?=$s['organic']?></b></td>
<td align=center><b><?=$s['venom']?></b></td>
<td align=center><b><?=$s['radioactive']?></b></td>
<td align=center><b><?=$s['gold']?></b></td>
<td align=center><b><?=$s['gems']?></b></td>
<td align=center><b><?=$s['metals']?></b></td>
<td align=center><b><?=$s['silicon']?></b></td>
</tr>
<tr><td colspan=10 height=4></td></tr>
<?
	}
	if ($d['user_id']!=$uid) {
		$uid=$d['user_id'];
		$s=array();
		$SQL="SELECT clan, user_name FROM site_users WHERE id='".$uid."'";
		$uname=mysql_fetch_array(mysql_query($SQL));
?>
<tr bgcolor=#F4ECD4>
<td colspan=10><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><b><?=GetClan($uname['clan']).GetUser($uid,$uname['user_name'],AuthUserGroup)?></b></td>
</tr>

<?
	}
	if ($d['coins']+$d['polymers']+$d['organic']+$d['venom']+$d['radioactive']+$d['gold']+$d['gems']+$d['metals']+$d['silicon']>0) {
		if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}
		$s['coins']+=$d['coins'];
		$s['polymers']+=$d['polymers'];
		$s['organic']+=$d['organic'];
		$s['venom']+=$d['venom'];
		$s['radioactive']+=$d['radioactive'];
		$s['gold']+=$d['gold'];
		$s['gems']+=$d['gems'];
		$s['metals']+=$d['metals'];
		$s['silicon']+=$d['silicon'];
		$SQL="SELECT clan FROM site_users WHERE user_name='".$d['from_name']."'";
		$c=mysql_fetch_array(mysql_query($SQL));
?>
<tr>
<td <?=$bg?>><?=date("d.m.y H:i",$d['dt'])?> <b><?=GetClan($c['clan']).$d['from_name']?> </b></td>
<td <?=$bg?> align=center><?=$d['coins']?></td>
<td <?=$bg?> align=center><?=$d['polymers']?></td>
<td <?=$bg?> align=center><?=$d['organic']?></td>
<td <?=$bg?> align=center><?=$d['venom']?></td>
<td <?=$bg?> align=center><?=$d['radioactive']?></td>
<td <?=$bg?> align=center><?=$d['gold']?></td>
<td <?=$bg?> align=center><?=$d['gems']?></td>
<td <?=$bg?> align=center><?=$d['metals']?></td>
<td <?=$bg?> align=center><?=$d['silicon']?></td>
</tr>

<?
	}
}
?>
<tr bgcolor=#F4ECD4>
<td><b>Итого: </b></td>
<td align=center><b><?=$s['coins']?></b></td>
<td align=center><b><?=$s['polymers']?></b></td>
<td align=center><b><?=$s['organic']?></b></td>
<td align=center><b><?=$s['venom']?></b></td>
<td align=center><b><?=$s['radioactive']?></b></td>
<td align=center><b><?=$s['gold']?></b></td>
<td align=center><b><?=$s['gems']?></b></td>
<td align=center><b><?=$s['metals']?></b></td>
<td align=center><b><?=$s['silicon']?></b></td>
</tr>

</table>

</td></tr>
</table>

</td></tr>
</table>
<? } ?>
<?
} else echo $mess['AccessDenied'];
?>