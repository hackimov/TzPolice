<h1>Состав, отделы</h1>

<?
if(AuthStatus==1 && AuthUserGroup>50) {

if (isset($_REQUEST['add_dept'])) {
	$error = '';
	if ($_REQUEST['did']) {
		$SQL = "SELECT parent FROM sd_depts WHERE id='".$_REQUEST['did']."'";
		list($parent) = mysql_fetch_array(mysql_query($SQL));
		if ($parent==0 && $_REQUEST['parent']>0) {
			$SQL = "SELECT id FROM sd_depts WHERE parent='".$_REQUEST['did']."'";
			if ($d = mysql_fetch_array(mysql_query($SQL))) {
				$error = "<font color='red'><b>Управление содержит отделы</b></font><br><br>";
			}
		}
		if ($parent>0 && $_REQUEST['parent']==0) {
			$SQL = "SELECT id FROM sd_cops WHERE dept='".$_REQUEST['did']."'";
			if ($d = mysql_fetch_array(mysql_query($SQL))) {
				$error = "<font color='red'><b>Отдел не пуст</b></font><br><br>";
			}
		}
		if ($error) {
			echo $error;
		} else {
			$SQL = "UPDATE sd_depts SET name='".$_REQUEST['dname']."', sname='".$_REQUEST['sname']."', parent='".$_REQUEST['parent']."' WHERE id='".$_REQUEST['did']."'";
			$r = mysql_query($SQL);
			if ($result) { echo "<font color='green'>Отдел успешно обновлен</font><br><br>"; }
			else { echo "<font color='red'><b>Ошибка во время редактирования отдела</b></font><br><br>"; }
		}
	} else {
		$SQL = "INSERT INTO sd_depts VALUES ('', '".$_REQUEST['dname']."', '".$_REQUEST['sname']."', '".$_REQUEST['parent']."', 0)";
		$r = mysql_query($SQL);
		if ($result) { echo "<font color='green'>Отдел успешно добавлен</font><br><br>"; }
		else { echo "<font color='red'><b>Ошибка во время добавления отдела</b></font><br><br>"; }
	}
}
if (isset($_REQUEST['add_cop'])) {
	$SQL = "SELECT name, parent FROM sd_depts WHERE id='".$_REQUEST['dept']."'";
	list($dname, $parent) = mysql_fetch_array(mysql_query($SQL));
	$SQL = "SELECT id, name, police FROM sd_cops WHERE dept='".$_REQUEST['dept']."'";
	list($persid, $persname, $chief, $police) = mysql_fetch_array(mysql_query($SQL));
	if ($_REQUEST['pid']) {
		if (($parent<2 || ($chief && $_REQUEST['chief'])) && $persid!=$_REQUEST['pid']) {
			echo "<font color='red'><b>Должность занимает <img src='_imgs/clans/".($police?'PoliceAcademy':'police').".gif'><a href='http://www.timezero.ru/info.html?".$persname."' target='_blank'>".$persname."</a></b></font><br><br>";
		} else {
			$SQL = "UPDATE sd_cops SET name='".$_REQUEST['pname']."', dept='".$_REQUEST['dept']."', chief='".$_REQUEST['chief']."', police='".$_REQUEST['pol']."', alias='".$_REQUEST['alias']."' WHERE id='".$_REQUEST['pid']."'";
			$r = mysql_query($SQL);
			if ($r) { echo "<font color='green'>Данные о сотруднике успешно изменены</font><br><br>"; }
			else { echo "<font color='red'><b>Ошибка во время редактирования данных о сотруднике</b></font><br><br>"; }
		}
	} else {
		if (($parent<2 && $persname) || ($chief && $_REQUEST['chief'])) {
			echo "<font color='red'><b>Должность занимает <img src='_imgs/clans/".($police?'PoliceAcademy':'police').".gif'><a href='http://www.timezero.ru/info.html?".$persname."' target='_blank'>".$persname."</a></b></font><br><br>";
		} else {
			$SQL = "SELECT name FROM sd_cops WHERE name='".$_REQUEST['pname']."'";
			list($pname) = mysql_fetch_array(mysql_query($SQL));
			if ($pname && !($_REQUEST['alias'])) {
				echo "<font color='red'><b>Сотрудник уже был добавлен ранее</b></font><br><br>";
			} else {
				$SQL = "INSERT INTO sd_cops VALUES ('', '".$_REQUEST['pname']."', '".$_REQUEST['dept']."', '".$_REQUEST['chief']."', '".$_REQUEST['pol']."', '".$_REQUEST['alias']."')";
				$r = mysql_query($SQL);
				if ($r) { echo "<font color='green'>Сотрудник успешно добавлен</font><br><br>"; }
				else { echo "<font color='red'><b>Ошибка во время добавления сотрудника</b></font><br><br>"; }
			}
		}
	}
}
if ($_REQUEST['del_cop']) {
	$SQL = "DELETE FROM sd_cops WHERE id='".$_REQUEST['del_cop']."' AND dept='".$_REQUEST['dpt']."'";
	$r = mysql_query($SQL);
	if ($r) { echo "<font color='green'>Сотрудник успешно удален</font><br><br>"; }
	else { echo "<font color='red'><b>Ошибка во время удаления сотрудника</b></font><br><br>"; }
}
if ($_REQUEST['del_dpt']) {
	if ($_REQUEST['del_dpt']>2) {
		$SQL = "SELECT parent FROM sd_depts WHERE id='".$_REQUEST['del_dpt']."'";
		list($parent) = mysql_fetch_array(mysql_query($SQL));
		if (!$parent) {
			$SQL = "SELECT id FROM sd_depts WHERE parent='".$_REQUEST['del_dpt']."'";
			$r = mysql_query($SQL);
			while (list($id) = mysql_fetch_array($r)) {
				$SQL = "DELETE FROM sd_cops WHERE dept='".$id."'";
				$d = mysql_query($SQL);
				if ($d) {
					echo "<font color='green'>Сотрудники отдела успешно удалены</font><br><br>";
					$SQL = "DELETE FROM sd_depts WHERE id='".$id."'";
					$s = mysql_query($SQL);
					if ($s) { echo "<font color='green'>Отдел успешно удален</font><br><br>"; }
					else { $error = 1; echo "<font color='red'><b>Ошибка во время удаления отдела</b></font><br><br>"; }
				} else { $error = 1; echo "<font color='red'><b>Ошибка во время удаления сотрудников отдела</b></font><br><br>"; }
			}
		}
		if (!$error) {
			$SQL = "DELETE FROM sd_cops WHERE dept='".$_REQUEST['del_dpt']."'";
			$r = mysql_query($SQL);
			if ($r) {
				echo "<font color='green'>Сотрудники отдела успешно удалены</font><br><br>";
				$SQL = "DELETE FROM sd_depts WHERE id='".$_REQUEST['del_dpt']."'";
				$r = mysql_query($SQL);
				if ($r) { echo "<font color='green'>Отдел успешно удален</font><br><br>"; }
				else { echo "<font color='red'><b>Ошибка во время удаления отдела</b></font><br><br>"; }
			} else { echo "<font color='red'><b>Ошибка во время удаления сотрудников отдела</b></font><br><br>"; }
		}
	}
}
?>

<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Добавить отдел/управление: [<a href="javascript:dept_form(0);">new</a>] </strong> </p></td>
</tr><tr><td>
<form name="depts" method="post" action="?act=cops_depts">
<table>
<tr><td>Полное название: </td><td><input type=hidden name=did><input name="dname" type="text" size="40" value=""></td></tr>
<tr><td>Краткое название: </td><td><input name="sname" type="text" size="40" value=""></td></tr>
<tr><td>Подчинено: </td><td>
<select name="parent">
	<option value="0">Без подчинения</option>
<?
$SQL = "SELECT id, name FROM sd_depts WHERE parent=0";
$managements = mysql_query($SQL);
while (list($id, $name) = mysql_fetch_array($managements)) {
?>
	<option value="<?=$id?>"><?=$name?></option>
<?
}
?>
</select>
</td></tr>
<tr><td>&nbsp;</td><td><input style="CURSOR: hand; BACKGROUND-IMAGE: url(i/input.gif);" type="submit" name="add_dept" value="Добавить"></td></tr>
</table>
</form>

<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Добавить в состав: [<a href="javascript:cop_form(0);">new</a>] </strong> </p></td>
</tr><tr><td>
<form name="users" method="post" action="?act=cops_depts">
<table>
<tr><td>Ник: </td><td><input type=hidden name=pid><input name="pname" type="text" size="20" value=""></td></tr>
<tr><td>Отдел: </td><td>
<select name="dept">
<?
$SQL = "SELECT id, name FROM sd_depts WHERE type=0 ORDER BY name";
$r=mysql_query($SQL);
while (list($id, $name) = mysql_fetch_row($r)) {
?>
	<option value="<?=$id?>"><?=$name?></option>
<?
}
?>
</select>
</td></tr>
<tr><td>&nbsp;</td><td><input name="pol" type="checkbox" value="1">&nbsp;Курсант </td></tr>
<tr><td>&nbsp;</td><td><input name="chief" type="checkbox" value="1">&nbsp;Начальник </td></tr>
<tr><td>&nbsp;</td><td><input name="alias" type="checkbox" value="1">&nbsp;По совместительству </td></tr>
<tr><td>&nbsp;</td><td><input style="CURSOR: hand; BACKGROUND-IMAGE: url(i/input.gif);" type="submit" name="add_cop" value="Добавить"></td></tr>
</table>
</form>

<script language='javascript'>
function dept_form(a,b,c,d) {
	if (a>0) {
		document.all("did").value = a;
		document.all("dname").value = b;
		document.all("sname").value = c;
		document.all("parent").value = d;
		document.all("add_dept").value = "Редактировать";
	} else {
		document.all("did").value = 0;
		document.all("dname").value = '';
		document.all("sname").value = '';
		document.all("parent").value = 0;
		document.all("add_dept").value = "Добавить";
	}
}
function cop_form(a,b,c,d,e,f) {
	if (a>0) {
		document.all("pid").value = a;
		document.all("pname").value = b;
		document.all("dept").value = c;
		document.all("pol").checked = d;
		document.all("chief").checked = e;
		document.all("alias").checked = f;
		document.all("add_cop").value = "Редактировать";
	} else {
		document.all("pid").value = 0;
		document.all("pname").value = '';
		document.all("dept").value = 0;
		document.all("pol").checked = 0;
		document.all("chief").checked = 0;
		document.all("alias").checked = 0;
		document.all("add_cop").value = "Добавить";
	}
}
</script>

<?
}
?>

<table>
<?
$SQL = "SELECT id, name, sname, parent FROM sd_depts WHERE parent=1 AND type=0";
$ranks = mysql_query($SQL);
while (list($id, $dept, $sname, $parent) = mysql_fetch_array($ranks)) {
	$SQL = "SELECT id, name, chief, police, alias FROM sd_cops WHERE dept='".$id."'";
	if (list($pid, $name, $chief, $police, $alias) = mysql_fetch_array(mysql_query($SQL))) {
		echo "<tr><td><font color='#A92C22'><b>".$dept;
		if(AuthStatus==1 && AuthUserGroup>50) { echo "&nbsp;<b>[<a href='?act=cops_depts&del_dpt=".$id."' onClick=\"if(!confirm('Вы уверены?')) {return false}\">X</a>] [<a href=\"javascript:dept_form(".$id.",'".$dept."','".$sname."',".$parent.");\">E</a>]</b>"; }
		echo ":</b></font>&nbsp;</td><td><img src='_imgs/clans/".($police?"PoliceAcademy":"police").".gif'><b><a href='http://www.timezero.ru/info.html?".$name."' target='_blank'>".$name."</a></b>".($alias?' (по совместительству)':'');
		if(AuthStatus==1 && AuthUserGroup>50) { echo "&nbsp;<b>[<a href='?act=cops_depts&del_cop=".$pid."&dpt=".$id."' onClick=\"if(!confirm('Вы уверены?')) {return false}\">X</a>] [<a href=\"javascript:cop_form(".$pid.",'".$name."',".$id.",".$police.",".$chief.",".$alias.");\">E</a>]</b>"; }
	}
}
?>
</table><br>

<?
$SQL = "SELECT id, name, sname, parent FROM sd_depts WHERE parent=0 AND type=0";
$managements = mysql_query($SQL);
while (list($id, $name, $sname, $parent) = mysql_fetch_array($managements)) {
?>
<br>
<table cellSpacing="0" cellPadding="0" width="100%" border="0">
<?
	echo "<tr bgcolor=#F4ECD4><td height='20' align=center><b>&nbsp;".$name;
	if(AuthStatus==1 && AuthUserGroup>50) { echo "&nbsp;[<a href='?act=cops_depts&del_dpt=".$id."' onClick=\"if(!confirm('Вы уверены?')) {return false}\">X</a>] [<a href=\"javascript:dept_form(".$id.",'".$name."','".$sname."',".$parent.");\">E</a>]"; }
	echo "</b><br>\n";
	$SQL = "SELECT id, name, chief, police, alias FROM sd_cops WHERE dept=$id";
	if (list($pid, $name, $chief, $police, $alias) = mysql_fetch_array(mysql_query($SQL))) {
		echo "&nbsp;<b>Начальник управления:</b></font>&nbsp;<img src='_imgs/clans/".($police?"PoliceAcademy":"police").".gif'><b><a href='http://www.timezero.ru/info.html?".$name."' target='_blank'>".$name."</a></b>".($alias?' (по совместительству)':'')."\n";
		if(AuthStatus==1 && AuthUserGroup>50) { echo "&nbsp;<b>[<a href='?act=cops_depts&del_cop=".$pid."&dpt=".$id."' onClick=\"if(!confirm('Вы уверены?')) {return false}\">X</a>] [<a href=\"javascript:cop_form(".$pid.",'".$name."',".$id.",".$police.",".$chief.",".$alias.");\">E</a>]</b>"; }
	}
	echo "</td></tr>";
	$SQL = "SELECT id, name, sname, parent FROM sd_depts WHERE parent='".$id."'";
	$depts = mysql_query($SQL);
	while (list($id, $name, $sname, $parent) = mysql_fetch_array($depts)) {
?>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><? echo "<a onclick=\"javascript:if(d".$id.".style.display=='none') d".$id.".style.display=''; else d".$id.".style.display='none';\" href=\"javascript:{}\">".$name."</a>"; ?>
<? if(AuthStatus==1 && AuthUserGroup>50) { echo "&nbsp;<b>[<a href='?act=cops_depts&del_dpt=".$id."' onClick=\"if(!confirm('Вы уверены?')) {return false}\">X</a>] [<a href=\"javascript:dept_form(".$id.",'".$name."','".$sname."',".$parent.");\">E</a>]</b>"; } ?></strong></td></tr>
<tr><td><div id="d<?=$id?>" style="display:none; margin-top: 5px;">
<?
	$SQL = "SELECT id, name, chief, police, alias FROM sd_cops WHERE dept='".$id."' ORDER BY chief DESC, police";
	$cops = mysql_query($SQL);
	$z = 1;
	while (list($pid, $name, $chief, $police, $alias) = mysql_fetch_array($cops)) {
		if ($chief) {
			echo "<font style='MARGIN-LEFT: 10px' color='#A92C22'><b>Начальник отдела:</b></font>&nbsp;<img src='_imgs/clans/".($police?'PoliceAcademy':'police').".gif'><b><a href='http://www.timezero.ru/info.html?".$name."' target='_blank'>".$name."</a></b>".($alias?' (по совместительству)':'');
			if(AuthStatus==1 && AuthUserGroup>50) { echo "&nbsp;<b>[<a href='?act=cops_depts&del_cop=".$pid."&dpt=".$id."' onClick=\"if(!confirm('Вы уверены?')) {return false}\">X</a>] [<a href=\"javascript:cop_form(".$pid.",'".$name."',".$id.",".$police.",".$chief.",".$alias.");\">E</a>]</b>"; }
		} else {
			if ($z) {
				$z = 0;
				echo "<br><font style='MARGIN-LEFT: 10px' color='#A92C22'><b>Сотрудники:</b></font><ol style='MARGIN-TOP: 0px'>";
			}
			echo "<b><li><img src='_imgs/clans/".($police?'PoliceAcademy':'police').".gif'><a href='http://www.timezero.ru/info.html?".$name."' target='_blank'>".$name."</a></b>".($alias?' (по совместительству)':'');			
			if(AuthStatus==1 && AuthUserGroup>50) { echo "&nbsp;<b>[<a href='?act=cops_depts&del_cop=".$pid."&dpt=".$id."' onClick=\"if(!confirm('Вы уверены?')) {return false}\">X</a>] [<a href=\"javascript:cop_form(".$pid.",'".$name."',".$id.",".$police.",".$chief.",".$alias.");\">E</a>]</b>"; }
		}
	}
?>
<br><br>
</div></td></tr>
<?
	}
?>
</table>
<?
}
?>
<br><hr><br>
<table cellSpacing="0" cellPadding="0" width="100%" border="0">
<?
$SQL = "SELECT id, name, sname, parent FROM sd_depts WHERE parent='2'";
$depts = mysql_query($SQL);
while (list($id, $name, $sname, $parent) = mysql_fetch_array($depts)) {
?>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><? echo "<a onclick=\"javascript:if(d".$id.".style.display=='none') d".$id.".style.display=''; else d".$id.".style.display='none';\" href=\"javascript:{}\">".$name."</a>"; ?>
<? if(AuthStatus==1 && AuthUserGroup>50) { echo "&nbsp;<b>[<a href='?act=cops_depts&del_dpt=".$id."' onClick=\"if(!confirm('Вы уверены?')) {return false}\">X</a>] [<a href=\"javascript:dept_form(".$id.",'".$name."','".$sname."',".$parent.");\">E</a>]</b>"; } ?></strong></td></tr>
<tr><td><div id="d<?=$id?>" style="display:none; margin-top: 5px;"><ol style='MARGIN-TOP: 0px'>
<?
	$SQL = "SELECT id, name, chief, police FROM sd_cops WHERE dept='".$id."' ORDER BY chief DESC, police";
	$cops = mysql_query($SQL);
	while (list($pid, $name, $chief, $police) = mysql_fetch_array($cops)) {
		echo "<b><li><img src='_imgs/clans/".($police?'PoliceAcademy':'police').".gif'><a href='http://www.timezero.ru/info.html?".$name."' target='_blank'>".$name."</a></b>";			
		if(AuthStatus==1 && AuthUserGroup>50) { echo "&nbsp;<b>[<a href='?act=cops_depts&del_cop=".$pid."&dpt=".$id."' onClick=\"if(!confirm('Вы уверены?')) {return false}\">X</a>] [<a href=\"javascript:cop_form(".$pid.",'".$name."',".$id.",".$police.",".$chief.");\">E</a>]</b>"; }
	}
?>
<br><br>
</div></td></tr>
<?
}
?>
</table>