<h1>Управление сайтом</h1>

<?php
$bgstr[0]="background='i/bgr-grid-sand.gif'";
$bgstr[1]="background='i/bgr-grid-sand1.gif'";

if(AuthStatus==1 && (abs(AccessLevel) & AccessSiteAdmin)) {

if (isset($_REQUEST['NewAccess'])) {
	$SQL = "INSERT INTO AccessLevels VALUES('','".$_REQUEST['al_aname_new']."','".$_REQUEST['al_name_new']."','".exp(log(2)*$_REQUEST['al_level_new'])."')";
	$r = mysql_query($SQL);
	if (!($r)) echo "<font color=red><b>Ошибка при добавлении нового доступа</b></font>\n\n";
}

if (isset($_REQUEST['SetLevels'])) {
	$SQL = "SELECT * FROM AccessLevels";
	$r = mysql_query($SQL);
	while ($d = mysql_fetch_array($r)) {
		$_REQUEST['al_level_'.$d['id']] = exp(log(2)*$_REQUEST['al_level_'.$d['id']]);
		if($d['AccessLevel']!=$_REQUEST['al_level_'.$d['id']]) {
			$SQL = "SELECT id, AccessGroup FROM SiteGroups";
			$g = mysql_query($SQL);
			while (list($gid, $AccessGroup) = mysql_fetch_array($g)) {
				if (abs($AccessGroup) & $d['AccessLevel']) {
					$SQL = "UPDATE SiteGroups SET AccessGroup='".($AccessGroup-$d['AccessLevel']+$_REQUEST['al_level_'.$d['id']])."' WHERE id='".$gid."'";
					mysql_query($SQL);
				} elseif ((abs($AccessGroup) & $d['AccessLevel']) && ($_REQUEST['al_level_'.$d['id']]==0)) {
					$SQL = "UPDATE SiteGroups SET AccessGroup='0' WHERE id='".$gid."'";
					mysql_query($SQL);
				}
			}
			$SQL = "SELECT id, AccessLevel FROM site_users WHERE AccessLevel>1";
			$u = mysql_query($SQL);
			while (list($uid, $AccessUser) = mysql_fetch_array($u)) {
				if (abs($AccessUser) & $d['AccessLevel']) {
					$SQL = "UPDATE site_users SET AccessLevel='".($AccessUser-$d['AccessLevel']+$_REQUEST['al_level_'.$d['id']])."' WHERE id='".$uid."'";
					mysql_query($SQL);
				}
			}
			$SQL = "SELECT id FROM SiteModules WHERE AccessModule='".$d['AccessLevel']."'";
			$m = mysql_query($SQL);
			while (list($mid) = mysql_fetch_array($m)) {
				$SQL = "UPDATE SiteModules SET AccessModule='".$_REQUEST['al_level_'.$d['id']]."' WHERE id='".$mid."'";
				mysql_query($SQL);
			}
			$SQL = "UPDATE AccessLevels SET AccessName='".$_REQUEST['al_aname_'.$d['id']]."', Name='".$_REQUEST['al_name_'.$d['id']]."', AccessLevel='".$_REQUEST['al_level_'.$d['id']]."' WHERE id='".$d['id']."'";
			mysql_query($SQL);
		} elseif (($d['AccessName']!=$_REQUEST['al_aname_'.$d['id']]) || ($d['Name']!=$_REQUEST['al_name_'.$d['id']])) {
			$SQL = "UPDATE AccessLevels SET AccessName='".$_REQUEST['al_aname_'.$d['id']]."', Name='".$_REQUEST['al_name_'.$d['id']]."' WHERE id='".$d['id']."'";
			mysql_query($SQL);
		}
	}
}

if (isset($_REQUEST['NewGroup'])) {
	$SQL = "INSERT INTO SiteGroups VALUES('','".$_REQUEST['sg_name_new']."','".$_REQUEST['sg_aname_new']."','".$_REQUEST['sg_mname_new']."','".$_REQUEST['sg_order_new']."','2')";
	$r = mysql_query($SQL);
	if (!($r)) echo "<font color=red><b>Ошибка при добавлении новой группы</b></font>\n\n";
}

if (isset($_REQUEST['SetGroups'])) {
	$SQL = "SELECT * FROM SiteGroups";
	$g = mysql_query($SQL);
	while ($d = mysql_fetch_array($g)) {
		if(($d['name']!=$_REQUEST['sg_name_'.$d['id']]) || ($d['act_name']!=$_REQUEST['sg_aname_'.$d['id']]) || ($d['module_name']!=$_REQUEST['sg_mname_'.$d['id']]) || ($d['order']!=$_REQUEST['sg_order_'.$d['id']])) {
			$SQL = "UPDATE SiteGroups SET name='".$_REQUEST['sg_name_'.$d['id']]."', act_name='".$_REQUEST['sg_aname_'.$d['id']]."', module_name='".$_REQUEST['sg_mname_'.$d['id']]."', `order`='".$_REQUEST['sg_order_'.$d['id']]."' WHERE id='".$d['id']."'";
			mysql_query($SQL);
		}
	}
}

if (isset($_REQUEST['NewModule'])) {
	$SQL = "INSERT INTO SiteModules VALUES('','".$_REQUEST['sm_group_new']."','".$_REQUEST['sm_aname_new']."','".$_REQUEST['sm_mname_new']."','".$_REQUEST['sm_name_new']."','".$_REQUEST['sm_order_new']."','".$_REQUEST['sm_level_new']."')";
	$r = mysql_query($SQL);
	if (!($r)) echo "<font color=red><b>Ошибка при добавлении нового модуля</b></font>\n\n";
	if ($_REQUEST['sm_group_new']) {
		$SQL = "SELECT * FROM SiteModules WHERE `group`='".$_REQUEST['sm_group_new']."' AND AccessModule=0";
		if (!(mysql_num_rows(mysql_query($SQL)))) {
			$SQL = "SELECT SUM(AccessModule) AS gsum FROM SiteModules WHERE `group`='".$_REQUEST['sm_group_new']."'";
			list($gsum) = mysql_fetch_array(mysql_query($SQL));
			$SQL = "UPDATE SiteGroups SET AccessGroup=$gsum WHERE id='".$_REQUEST['sm_group_new']."'";
			mysql_query($SQL);
		} elseif ($_REQUEST['sm_level_new']==0) {
			$SQL = "UPDATE SiteGroups SET AccessGroup=0 WHERE id='".$_REQUEST['sm_group_new']."'";
			mysql_query($SQL);
		}
	}
}

if (isset($_REQUEST['SetModules'])) {
	$SQL = "SELECT * FROM SiteModules";
	$m = mysql_query($SQL);
	while ($d = mysql_fetch_array($m)) {
		if($d['group']!=$_REQUEST['sm_group_'.$d['id']]) {
			$SQL = "SELECT id, AccessGroup FROM SiteGroups WHERE id='".$d['group']."'";
			list($gid, $AccessGroup) = mysql_fetch_array(mysql_query($SQL));
			if ($AccessGroup) {
				$SQL = "UPDATE SiteGroups SET AccessGroup='".($AccessGroup-$d['AccessModule'])."' WHERE id='".$gid."'";
				mysql_query($SQL);
			} elseif ($d['AccessModule']==0) {
				$SQL = "SELECT AccessModule FROM SiteModules WHERE `group`='".$d['group']."' AND id<>'".$d['id']."'";
				$r = mysql_query($SQL);
				$sum = 1;
				while (list($AccessModule) = mysql_fetch_array($r)) {
					if ($AccessModule && $sum) $sum += $AccessModule;
					else $sum = 0;
				} if ($sum) $sum--;
				$SQL = "UPDATE SiteGroups SET AccessGroup='".$sum."' WHERE id='".$gid."'";
				mysql_query($SQL);
			}
			$SQL = "SELECT id FROM SiteGroups WHERE id='".$_REQUEST['sm_group_'.$d['id']]."'";
			list($gid, $AccessGroup) = mysql_fetch_array(mysql_query($SQL));
			if ($_REQUEST['sm_level_'.$d['id']]) {
				$SQL = "SELECT AccessModule FROM SiteModules WHERE `group`='".$_REQUEST['sm_group_'.$d['id']]."'";
				$r = mysql_query($SQL);
				$sum = $_REQUEST['sm_level_'.$d['id']];
				while (list($AccessModule) = mysql_fetch_array($r)) {
					if ($AccessModule && $sum) $sum += $AccessModule;
					else $sum = 0;
				}
				$SQL = "UPDATE SiteGroups SET AccessGroup='".$sum."' WHERE id='".$gid."'";
				mysql_query($SQL);
			} else {
				$SQL = "UPDATE SiteGroups SET AccessGroup='0' WHERE id='".$gid."'";
				mysql_query($SQL);
			}
			$SQL = "UPDATE SiteModules SET `group`='".$_REQUEST['sm_group_'.$d['id']]."', act_name='".$_REQUEST['sm_aname_'.$d['id']]."', module_name='".$_REQUEST['sm_mname_'.$d['id']]."', name='".$_REQUEST['sm_name_'.$d['id']]."', `order`='".$_REQUEST['sm_order_'.$d['id']]."', AccessModule='".$_REQUEST['sm_level_'.$d['id']]."' WHERE id='".$d['id']."'";
			mysql_query($SQL);
		} elseif ($d['AccessModule']!=$_REQUEST['sm_level_'.$d['id']]) {
			$SQL = "SELECT id FROM SiteGroups WHERE id='".$d['group']."'";
			list($gid, $AccessGroup) = mysql_fetch_array(mysql_query($SQL));
			if ($_REQUEST['sm_level_'.$d['id']]) {
				$SQL = "SELECT AccessModule FROM SiteModules WHERE `group`='".$_REQUEST['sm_group_'.$d['id']]."' AND id<>'".$d['id']."'";
				$r = mysql_query($SQL);
				$sum = $_REQUEST['sm_level_'.$d['id']];
				while (list($AccessModule) = mysql_fetch_array($r)) {
					if ($AccessModule && $sum) $sum += $AccessModule;
					else $sum = 0;
				}
				$SQL = "UPDATE SiteGroups SET AccessGroup='".$sum."' WHERE id='".$gid."'";
				mysql_query($SQL);
			} else {
				$SQL = "UPDATE SiteGroups SET AccessGroup='0' WHERE id='".$gid."'";
				mysql_query($SQL);
			}
			$SQL = "UPDATE SiteModules SET act_name='".$_REQUEST['sm_aname_'.$d['id']]."', module_name='".$_REQUEST['sm_mname_'.$d['id']]."', name='".$_REQUEST['sm_name_'.$d['id']]."', `order`='".$_REQUEST['sm_order_'.$d['id']]."', AccessModule='".$_REQUEST['sm_level_'.$d['id']]."' WHERE id='".$d['id']."'";
			mysql_query($SQL);
		} elseif (($d['act_name']!=$_REQUEST['sm_aname_'.$d['id']]) || ($d['module_name']!=$_REQUEST['sm_mname_'.$d['id']]) || ($d['name']!=$_REQUEST['sm_name_'.$d['id']]) || ($d['order']!=$_REQUEST['sm_order_'.$d['id']])) {
			$SQL = "UPDATE SiteModules SET act_name='".$_REQUEST['sm_aname_'.$d['id']]."', module_name='".$_REQUEST['sm_mname_'.$d['id']]."', name='".$_REQUEST['sm_name_'.$d['id']]."', `order`='".$_REQUEST['sm_order_'.$d['id']]."' WHERE id='".$d['id']."'";
			mysql_query($SQL);
		}
	}
}

$AccessLevels = array();
$SQL = "SELECT AccessName, AccessLevel FROM AccessLevels";
$a = mysql_query($SQL);
while (list($aname, $alevel) = mysql_fetch_array($a)) {
	$AccessLevels[$alevel] = $aname;
}
$SiteGroups = array();
$SQL = "SELECT id, name FROM SiteGroups ORDER BY name";
$g = mysql_query($SQL);
while (list($gid, $gname) = mysql_fetch_array($g)) {
	$SiteGroups[$gid] = $gname;
}
$SQL = "SELECT MAX(AccessLevel) AS amax FROM AccessLevels";
list($amax) = mysql_fetch_array(mysql_query($SQL));
$amax = log($amax)/log(2);
?>

<table width=100% border='0' cellspacing='3' cellpadding='3'>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><strong>Управление уровнями доступа</strong></p></td></tr>
<tr><td>

<table border='0' cellspacing='3' cellpadding='5'>
<tr bgcolor=#F4ECD4><td height='20'><strong> Название для скриптов </strong></td><td><strong> Название доступа </strong></td><td><strong> Значение </strong></td></tr>
<form method="POST" action="?act=site_admin"><tr><td><input type=text name=al_aname_new size=20></td><td><input type=text name=al_name_new size=100></td><td <?=$bg?>><input type=text name=al_level_new value=<?=($amax+1)?> size=3> <input type=submit name=NewAccess value=" + "></td></tr></form>
<form method="POST" action="?act=site_admin">

<?
$SQL = "SELECT * FROM AccessLevels";
$r = mysql_query($SQL);
$np = 0;
while ($d = mysql_fetch_array($r)) {
if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}
?>
<tr><td <?=$bg?>><input type=text name=al_aname_<?=$d['id']?> value="<?=$d['AccessName']?>" size=20></td><td <?=$bg?>><input type=text name=al_name_<?=$d['id']?> value="<?=$d['Name']?>" size=100></td><td <?=$bg?>><input type=text name=al_level_<?=$d['id']?> value="<?=log($d['AccessLevel'])/log(2)?>" size=3></td></tr>
<?
}
?>

<tr><td colspan=3 align=center><input type=submit name=SetLevels value="Установить"></td></tr>
</form>
</table>

</td></tr>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><strong>Управление группами сайта</strong></p></td></tr>
<tr><td>

<table border='0' cellspacing='3' cellpadding='5'>
<tr bgcolor=#F4ECD4><td height='20'><strong> Название группы </strong></td><td><strong> Название для скриптов </strong></td><td><strong> Название скрипта </strong></td><td><strong> Порядок в меню </strong></td></tr>
<form method="POST" action="?act=site_admin"><tr><td><input type=text name=sg_name_new size=30></td><td><input type=text name=sm_aname_new size=20></td><td><input type=text name=sm_mname_new size=20></td><td><input type=text name=sg_order_new size=5> <input type=submit name=NewGroup value=" + "></td></tr></form>

<form method="POST" action="?act=site_admin">

<?
$SQL = "SELECT * FROM SiteGroups ORDER BY `order`";
$r = mysql_query($SQL);
$np = 0;
while ($d = mysql_fetch_array($r)) {
if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}
?>
<tr><td <?=$bg?>><input type=text name=sg_name_<?=$d['id']?> value="<?=$d['name']?>" size=30></td><td <?=$bg?>><input type=text name=sg_aname_<?=$d['id']?> value="<?=$d['act_name']?>" size=20></td><td <?=$bg?>><input type=text name=sg_mname_<?=$d['id']?> value="<?=$d['module_name']?>" size=20></td><td <?=$bg?>><input type=text name=sg_order_<?=$d['id']?> value=<?=$d['order']?> size=5></td></tr>
<?
}
?>

<tr><td colspan=4 align=center><input type=submit name=SetGroups value="Установить"></td></tr>
</form>
</table>

</td></tr>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><strong>Управление модулями сайта</strong></p></td></tr>
<tr><td>

<table border='0' cellspacing='3' cellpadding='5'>
<tr bgcolor=#F4ECD4><td><strong> Группа </strong></td><td height='20'><strong> Название для скриптов </strong></td><td><strong> Название скрипта </strong></td><td><strong> Название </strong></td><td><strong> Доступ </strong></td><td><strong> Порядок в меню </strong></td></tr>
<form method="POST" action="?act=site_admin"><tr><td><select name=sm_group_new>
	<option value=0 selected>-- Без группы --</option>
<?
foreach (array_keys($SiteGroups) as $GroupID) {
	echo "	<option value=$GroupID>$SiteGroups[$GroupID]</option>\n";
}
?>
</select></td>
<td><input type=text name=sm_aname_new size=20></td><td><input type=text name=sm_mname_new size=20></td><td><input type=text name=sm_name_new size=30></td>
<td <?=$bg?>><select name=sm_level_new>
	<option value=0 selected>-- Не ограничен --</option>
<?
foreach (array_keys($AccessLevels) as $LevelValue) {
	echo "	<option value=$LevelValue>$AccessLevels[$LevelValue]</option>\n";
}
?>
</select></td><td><input type=text name=sm_order_new size=5>&nbsp;<input type=submit name=NewModule value=" + "></td></tr></form>
<form method="POST" action="?act=site_admin">

<?
$SQL = "SELECT * FROM SiteModules ORDER BY `group`, `order`";
$r = mysql_query($SQL);
$np = 0;
while ($d = mysql_fetch_array($r)) {
if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}
?>
<tr><td <?=$bg?>><select name=sm_group_<?=$d['id']?>>
	<option value=0<?=($d['group']==0?' selected':'')?>>-- Без группы --</option>
<?
foreach (array_keys($SiteGroups) as $GroupID) {
	echo "	<option value=$GroupID".($d['group']==$GroupID?' selected':'').">$SiteGroups[$GroupID]</option>\n";
}
?>
</select></td>
<td <?=$bg?>><input type=text name=sm_aname_<?=$d['id']?> value="<?=$d['act_name']?>" size=20></td><td <?=$bg?>><input type=text name=sm_mname_<?=$d['id']?> value="<?=$d['module_name']?>" size=20></td><td <?=$bg?>><input type=text name=sm_name_<?=$d['id']?> value="<?=$d['name']?>" size=30></td>
<td <?=$bg?>><select name=sm_level_<?=$d['id']?>>
	<option value=0<?=($d['AccessModule']==0?' selected':'')?>>-- Не ограничен --</option>
<?
foreach (array_keys($AccessLevels) as $LevelValue) {
	echo "	<option value=$LevelValue".($d['AccessModule']==$LevelValue?' selected':'').">$AccessLevels[$LevelValue]</option>\n";
}
?>
</select></td><td <?=$bg?>><input type=text name=sm_order_<?=$d['id']?> value="<?=$d['order']?>" size=5></td></tr>
<?
}
?>

<tr><td colspan=6 align=center><input type=submit name=SetModules value="Установить"></td></tr>
</form>
</table>

</td></tr>
</table>

<?
} else {
	echo $mess['AccessDenied'];
}
?>