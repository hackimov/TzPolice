<?
require "../_modules/functions.php";
require "../_modules/auth.php";

if(!defined('AccessLevel')) define('AccessLevel', 0);
if(!defined('AccessItemsEditor')) define('AccessItemsEditor', 0);

if (isset($_REQUEST['p'])) $ptype = $_REQUEST['p'];
else $ptype = "";

if (!(AccessLevel & AccessItemsEditor)) if ($ptype == 'items') $ptype = "";

?>
<html>
<head>
	<title>Расчет компенсаций</title>
	<link rel="stylesheet" type="text/css" href="c2.css">
</head>
<body bgcolor=#333333>

<? if ($ptype == '') { ?>
<a href="items.php?p=price">Текущие цены на ресурсы</a><br>
<? if (AccessLevel & AccessItemsEditor) { ?>
<a href="items.php?p=items">Справочник вещей</a><br>
<? } ?>
<a href="items.php?p=cmp">Расчет компенсаций</a>
<? } ?>
<? if ($ptype == 'price') { ?>
<a href="items.php">Назад</a><br><br>
<table border=1 bordercolor=#666666>
<tr bgcolor=#444444><td><b>&nbsp;Ресурсы&nbsp;</b></td><td><img src="s1.gif"></td><td><img src="s2.gif"></td><td><img src="s3.gif"></td><td><img src="s4.gif"></td><td><img src="s5.gif"></td><td><img src="s6.gif"></td><td><img src="s7.gif"></td><td><img src="s8.gif"></td></tr>
<tr align=center><td bgcolor=#444444><b>Цены</b></td>
<?
$SQL = "SELECT * FROM ItemsCost";
$r = mysql_query($SQL);
while ($d = mysql_fetch_array($r)) {
	echo "<td bgcolor=#555555><b>".$d['cost']."</b></td>";
}
?>
</tr>
</table>
<? } ?>
<? if ($ptype == 'items') { ?>
<script language="JavaScript">
function EditItem(a,b) {
	if (b>0) {
		var i,j;
		var Item	= document.getElementById('i'+b).innerHTML;
		var ItemReq	= document.getElementById('ir'+b).innerHTML;
		var SetReq	= document.getElementById('sr'+b).innerHTML;
		ftext.innerHTML = 'Редактировать позицию';
		Form.iid.value = b;
		Form.iclass.value = a;
		Form.subm.value = "Редактировать";
		if ((i=Item.indexOf('<B>'))>0 && (j=Item.indexOf('</B>'))>0) Form.name.value = Item.substring(i+3,j).replace('&amp;','&');
		if ((i=Item.indexOf('й: '))>0 && (j=Item.indexOf(',',i))>0) Form.qmax.value = Item.substring(i+3,j);
		if ((i=Item.indexOf('з: '))>0 && (j=Item.indexOf(')',i))>0) Form.qone.value = Item.substring(i+3,j);
		if ((i=Item.indexOf('о: '))>0 && (j=Item.indexOf(',',i))>0) Form.quality.value = Item.substring(i+3,j); else Form.quality.value = '';
		var k = 0, c = 20;
		while ((i=ItemReq.indexOf('<TD',c))>0 && (j=ItemReq.indexOf('</TD>',i))>0) {
			k++;
			if (ItemReq.substring(i+4,j)=='-') document.all("is"+k).value = '';
			else if ((i=ItemReq.indexOf('<B>',c))>0 && (j=ItemReq.indexOf('</B>',i))>0) document.all("is"+k).value = ItemReq.substring(i+3,j); 
			else if ((i=ItemReq.indexOf('r>',c))>0 && (j=ItemReq.indexOf(' мин',i))>0) document.all("itime").value = ItemReq.substring(i+2,j)>0?ItemReq.substring(i+2,j):'';
			c = j;
		}
		k = 0; c = 20;
		while ((i=SetReq.indexOf('<TD',c))>0 && (j=SetReq.indexOf('</TD>',i))>0) {
			k++;
			if (SetReq.substring(i+4,j)=='-') document.all("ss"+k).value = '';
			else if ((i=SetReq.indexOf('<B>',c))>0 && (j=SetReq.indexOf('</B>',i))>0) document.all("ss"+k).value = SetReq.substring(i+3,j); 
			else if ((i=SetReq.indexOf('r>',c))>0 && (j=SetReq.indexOf(' мин',i))>0) document.all("stime").value = SetReq.substring(i+2,j)>0?SetReq.substring(i+2,j):'';
			c = j;
		}
		document.getElementById('l'+a).style.display='none';
		l0.style.display='';
	} else {
		ftext.innerHTML = 'Добавить позицию';
		Form.iid.value = "";
		Form.iclass.value = 1;
		Form.subm.value = "Добавить";
		Form.name.value = '';
		Form.qmax.value = '';
		Form.qone.value = '';
		Form.quality.value = '';
		for (var k=1; k<=8; k++) {
			document.all("is"+k).value = '';
			document.all("ss"+k).value = '';
		}
	}
}
</script>
<a href="items.php">Назад</a><br><br>
<?
if (isset($_REQUEST['del'])) {
	$SQL = "DELETE FROM ItemsReq WHERE id='".$_REQUEST['del']."'";
	mysql_query($SQL);
	echo "<script>top.location='items.php?p=items&delete';</script>";
}
if (isset($_REQUEST['subm'])) {
	$for_item = array();
	$for_set = array();
	for ($i=1; $i<=8; $i++) {
		$for_item[] = $_REQUEST['is'.$i]>0?$_REQUEST['is'.$i]:'0';
		$for_set[] = $_REQUEST['ss'.$i]>0?$_REQUEST['ss'.$i]:'0';
	}
	if ($_REQUEST['iid']>0) {
		$SQL = "UPDATE ItemsReq SET class_id='".$_REQUEST['iclass']."', item_name='".$_REQUEST['name']."', item_res='".implode(',',$for_item)."', set_res='".implode(',',$for_set)."', in_use='".$_REQUEST['qone']."', in_set='".$_REQUEST['qmax']."', quality='".$_REQUEST['quality']."', use_time='".$_REQUEST['itime']."', set_time='".$_REQUEST['stime']."' WHERE id='".$_REQUEST['iid']."'";
		mysql_query($SQL);
		echo "<script>top.location='items.php?p=items&edit';</script>";
	} else {
		$SQL = "INSERT INTO ItemsReq VALUES('','".$_REQUEST['iclass']."','".$_REQUEST['name']."','".implode(',',$for_item)."','".implode(',',$for_set)."','".$_REQUEST['qone']."','".$_REQUEST['qmax']."','".$_REQUEST['quality']."','".$_REQUEST['itime']."','".$_REQUEST['stime']."')";
		mysql_query($SQL);
		echo "<script>top.location='items.php?p=items&add';</script>";
	}
}
if (isset($_REQUEST['add'])) echo "<font color=green><b>Новая позиция успешно добавлена</b></font>";
if (isset($_REQUEST['edit'])) echo "<font color=green><b>Позиция успешно отредактирована</b></font>";
if (isset($_REQUEST['delete'])) echo "<font color=green><b>Позиция успешно удалена</b></font>";
?>
<form name=Form action="items.php?p=items" method=POST>
<input type="hidden" name="iid">
<a onclick="javascript:if(l0.style.display=='none') l0.style.display=''; else l0.style.display='none'; EditItem(1,0);" href="javascript:{};"><b id=ftext>Добавить позицию</b></a><div id="l0" style="display:none">
<table border=1 bordercolor=#666666>
<tr bgcolor=#444444><td><b>Класс:</b></td><td colspan=6><select name="iclass">
<?
$ItemsClass = array();
$SQL = "SELECT * FROM ItemsClass ORDER BY id";
$r = mysql_query($SQL);
while ($d = mysql_fetch_array($r)) {
	$ItemsClass[$d['id']] = $d['class'];
	echo "	<option value=\"".$d['id']."\">".$d['class']."</option>";
}
?>
</select></td>
<td colspan=3 align=center><input type=submit name=subm value=Добавить></td></tr>
<tr bgcolor=#444444><td><b>Наименование:</b></td><td colspan=9><input type="text" name="name" size=79 maxlength=100></td>
<tr bgcolor=#444444><td><b>Использований:</b></td><td><input type="text" name="qmax" size=4 maxlength=5></td>
<td colspan=4 align=center><b>Производится за раз:</b></td><td><input type="text" name="qone" size=4 maxlength=3></td>
<td colspan=2 align=center><b>Качество:</b></td><td><input type="text" name="quality" size=4 maxlength=5></td></tr>
<tr bgcolor=#444444><td><b>Требуемые ресурсы</b></td><td><img src="s1.gif"></td><td><img src="s2.gif"></td><td><img src="s3.gif"></td><td><img src="s4.gif"></td><td><img src="s5.gif"></td><td><img src="s6.gif"></td><td><img src="s7.gif"></td><td><img src="s8.gif"></td><td><img src="time.gif"></td></tr>
<tr bgcolor=#444444><td align=right>на производство:</td>
<td><input type="text" name="is1" size=4 maxlength=6></td>
<td><input type="text" name="is2" size=4 maxlength=6></td>
<td><input type="text" name="is3" size=4 maxlength=6></td>
<td><input type="text" name="is4" size=4 maxlength=6></td>
<td><input type="text" name="is5" size=4 maxlength=6></td>
<td><input type="text" name="is6" size=4 maxlength=6></td>
<td><input type="text" name="is7" size=4 maxlength=6></td>
<td><input type="text" name="is8" size=4 maxlength=6></td>
<td><input type="text" name="itime" size=4 maxlength=6></td></tr>
<tr bgcolor=#444444><td align=right>на чертеж:</td>
<td><input type="text" name="ss1" size=4 maxlength=6></td>
<td><input type="text" name="ss2" size=4 maxlength=6></td>
<td><input type="text" name="ss3" size=4 maxlength=6></td>
<td><input type="text" name="ss4" size=4 maxlength=6></td>
<td><input type="text" name="ss5" size=4 maxlength=6></td>
<td><input type="text" name="ss6" size=4 maxlength=6></td>
<td><input type="text" name="ss7" size=4 maxlength=6></td>
<td><input type="text" name="ss8" size=4 maxlength=6></td>
<td><input type="text" name="stime" size=4 maxlength=6></td></tr>
</table>
</div>
</form>
<?
foreach (array_keys($ItemsClass) as $cid) {
	echo "<a onclick=\"javascript:if(l$cid.style.display=='none') l$cid.style.display=''; else l$cid.style.display='none';\" href=\"javascript:{}\">".$ItemsClass[$cid]."</a><div id=\"l$cid\" style=\"display:none\">\n";
?>
	<table border=1 bordercolor=#666666>
	<tr><td><b>Требуемые ресурсы</b></td><td><img src="s1.gif"></td><td><img src="s2.gif"></td><td><img src="s3.gif"></td><td><img src="s4.gif"></td><td><img src="s5.gif"></td><td><img src="s6.gif"></td><td><img src="s7.gif"></td><td><img src="s8.gif"></td><td><img src="time.gif"></td></tr>
<?
	$SQL = "SELECT * FROM ItemsReq WHERE class_id='$cid' ORDER BY item_name";
	$c = mysql_query($SQL);
	while ($i = mysql_fetch_array($c)) {
		$for_item = explode(',',$i['item_res']);
		$for_set = explode(',',$i['set_res']);
		echo "	<tr id=i".$i['id']." bgcolor=#444444><td colspan=10><b>".$i['item_name']."</b> (<font class=\"newsfooter\">".($i['quality']?"качество: ".$i['quality'].", ":"")."использований: ".$i['in_set'].", производится за раз: ".$i['in_use'].")</font> [<a href=\"javascript:EditItem(".$cid.",".$i['id'].")\">E</a>][<a href=\"items.php?p=items&del=".$i['id']."\">X</a>]</td></tr>\n";
		echo "	<tr id=ir".$i['id']." bgcolor=#444444 align=center><td align=right>на производство:</td>";
		for ($n=0; $n<=7; $n++) echo ($for_item[$n]?"<td bgcolor=#555555><b>".$for_item[$n]."</b></td>":"<td>-</td>");
		echo "<td><font class=\"newsfooter\">".$i['use_time']." мин</font></td>";
		echo "</tr>\n	<tr id=sr".$i['id']." bgcolor=#444444 align=center><td align=right>чертеж:</td>";
		for ($n=0; $n<=7; $n++) echo ($for_set[$n]?"<td bgcolor=#555555><b>".$for_set[$n]."</b></td>":"<td>-</td>");
		echo "<td><font class=\"newsfooter\">".$i['set_time']." мин</font></td></tr>\n";
	}
?>
	</table>
</div><br>
<?
}

} ?>
<? if ($ptype == 'cmp') { ?>
<script language="JavaScript">
Items = new Array();
<?
$SQL = "SELECT * FROM ItemsClass";
$r = mysql_query($SQL);
while ($d = mysql_fetch_array($r)) {
	echo "Items[".$d['id']."] = new Array();\n";
	$SQL = "SELECT id, item_name FROM ItemsReq WHERE class_id='".$d['id']."' ORDER BY item_name";
//	print_r($SQL);
	$i = mysql_query($SQL);
	while ($o = mysql_fetch_array($i)) echo "Items[".$d['id']."][".$o['id']."] = '".$o['item_name']."';\n";
}
?>
function select_class() {
	while(sel.length > 0) {
		sel.remove(sel.length - 1);
	}
	var o = document.createElement('option');
	o.text = "\-\- Выберите вещь \-\-";
	o.value = 0;
	sel.add(o,0);
	if (cl.value>0) {
		for (var i in Items[cl.value]) {
			o = document.createElement('option');
			o.text = Items[cl.value][i];
			o.value = i;
			sel.add(o,i);
		}
	}
}
function AddLine() {
	if (sel.value>0) {
		Form.n.value++;
		var tmp = nform.innerHTML;
		var inp = '<input type=hidden name="l'+Form.n.value+'" value="'+sel.value+','+qlt.value+','+qnt.value+','+mf.value+'">';
		var lnk = ' [<a href="javascript:DelLine('+Form.n.value+')">X</a>]';
		var str = '<tr><td><b>'+sel.options[sel.selectedIndex].text+lnk+'</b></td><td><b>'+qlt.value+'</b></td><td><b>'+qnt.value+'</b></td><td><b>'+mf.value+'</b></td></tr>';
		nform.innerHTML = tmp.replace('<!-- new line -->','<!--'+Form.n.value+'-->'+inp+str+'\n<!--end--><!-- new line -->');
		qlt.value = 0; qnt.value = 1; mf.value = 0;
	}
}
function DelLine(a) {
	var tmp = nform.innerHTML;
	if ((i=tmp.indexOf('<!--'+a+'-->'))>0 && (j=tmp.indexOf('<!--end-->',i))>0) nform.innerHTML = tmp.substring(0,i)+tmp.substring(j);
}
</script>
<a href="items.php">Назад</a><br><br>
<?
if (isset($_REQUEST['n'])) {
	if ($_REQUEST['n']>0) {
		$error = "";
?>
<table width=500 border=1 bordercolor=#666666>
<tr bgcolor=#444444>
<td colspan=4 align=center><b>Результаты расчетов</b></td></tr>
<tr><td><b>&nbsp;Наименование&nbsp;</b></td><td width=1><b>&nbsp;Количество&nbsp;</b></td><td width=1><b>&nbsp;Себестоимость&nbsp;</b></td></tr>
<?
		$kmf = array(1,3,11,31,111,1111);
		$cmp = 0;
		$SQL = "SELECT cost FROM ItemsCost";
		$r = mysql_query($SQL);
		$p = array();
		while ($d = mysql_fetch_array($r)) $p[] = $d['cost'];
		for ($i=1; $i<=$_REQUEST['n']; $i++) {
			$isumm = 0; $ssumm = 0;
			if (isset($_REQUEST["l$i"])) {
				$item = array();
				$item = explode(',',$_REQUEST["l$i"]);
				$SQL = "SELECT * FROM ItemsReq WHERE id='".$item[0]."'";
				$d = mysql_fetch_array(mysql_query($SQL));
				$ireq = explode(',',$d['item_res']);
				$sreq = explode(',',$d['set_res']);
				for ($k=0; $k<8; $k++) {
					$isumm += $ireq[$k]*$p[$k];
					$ssumm += $sreq[$k]*$p[$k];
				}
				$in_set = $d['in_set'];
				if ($item[3]>4) {
					$error .= "<b>Ошибка:</b> для позиции <b>".$d['item_name']."</b> модификации <b>M".$item[3]."</b> изменены на <b>M4</b><br>\n";
					$item[3] = 4;
				} elseif ($item[3]<0) {
					$error .= "<b>Ошибка:</b> для позиции <b>".$d['item_name']."</b> модификации <b>M".$item[3]."</b> удалены<br>\n";
					$item[3] = 0;
				}
				$isumm += 82500*$d['use_time']/259200;
				$ssumm = $kmf[$item[3]]*$ssumm + 135000*($item[3]+1)*$d['set_time']/259200;
				for ($k=1; $k<=$item[3]; $k++) $in_set = round(0.9*$in_set,0);
				$sebes = round(($ssumm/$in_set+$isumm)/$d['in_use'],2);
				if ($item[1]==0) $item[1] = $d['quality'];
				if ($item[1]>$d['quality']) {
					$error .= "<b>Ошибка:</b> для позиции <b>".$d['item_name']."</b> качество <b>".$item[1]."</b> изменено на <b>".$d['quality']."</b><br>\n";
					$item[1] = $d['quality'];
				} elseif ($item[1]<0) {
					$error .= "<b>Ошибка:</b> для позиции <b>".$d['item_name']."</b> качество <b>".$item[1]."</b> изменено на <b>0</b><br>\n";
					$item[1] = 0;
				}
				if ($item[2]<0) {
					$error .= "<b>Ошибка:</b> для позиции <b>".$d['item_name']."</b> количество <b>".$item[2]."</b> изменено на <b>0</b><br>\n";
					$item[2] = 0;
				}
				if ($d['quality']>0) {
					$cmp += round($item[2]*$sebes*$item[1]/$d['quality'],0);
					echo "<tr align=right><td align=left>".$d['item_name'].($item[3]>0?" M".$item[3]:"").($d['quality']>0?" (".$item[1]."/".$d['quality'].")":"")."</td><td>".$item[2]."</td><td>".round($item[2]*$sebes*$item[1]/$d['quality'],0)."м.м.</td></tr>\n";
				} else {
					$cmp += round($item[2]*$sebes,0);
					echo "<tr align=right><td align=left>".$d['item_name'].($item[3]>0?" M".$item[3]:"").($d['quality']>0?" (".$item[1]."/".$d['quality'].")":"")."</td><td>".$item[2]."</td><td>".round($item[2]*$sebes,0)."м.м.</td></tr>\n";
				}
			}
		}
?>
<tr bgcolor=#444444 align=right><td colspan=2><b>Итого:&nbsp;</b></td><td><b><?=$cmp?>м.м.</b></td></tr>
</table><br>
<?
		echo "<font color=red>".$error."</font><br>\n";
	}
}
?>
<table border=1 bordercolor=#666666>
<tr bgcolor=#444444><td colspan=2 align=center><b>Добавить в список компенсации</b></td></tr>
<tr><td><b>&nbsp;Категория:&nbsp;</b></td><td><select id=cl onchange="select_class()">
<option value=0>-- Выберите категорию --</option>
<?
$SQL = "SELECT * FROM ItemsClass";
$r = mysql_query($SQL);
while ($d = mysql_fetch_array($r)) {
	echo "<option value=\"".$d['id']."\">".$d['class']."</option>\n";
}
?>
</select></td></tr>
<tr><td><b>&nbsp;Наименование:&nbsp;</b></td><td><select id=sel><option value=0>-- Выберите вещь --</option></select></td></tr>
<tr><td><b>&nbsp;Качество:</b></td><td><input type="text" id=qlt size=4 maxlength=6 value=0></td></tr>
<tr><td><b>&nbsp;Количество:</b></td><td><input type="text" id=qnt size=4 maxlength=6 value=1></td></tr>
<tr><td><b>&nbsp;Модификаций:</b></td><td><input type="text" id=mf size=4 maxlength=6 value=0></td></tr>
<tr bgcolor=#444444><td colspan=2 align=center><input type="button" value="Добавить" onclick="AddLine()"></td></tr>
</table>
<form name=Form action="items.php?p=cmp" method=POST>
<input type=hidden name="n" value="0">
<div id=nform>
<table width=500 border=1 bordercolor=#666666>
<tr bgcolor=#444444>
<td colspan=4 align=center><b>Список для расчета компенсации</b></td></tr>
<tr><td><b>&nbsp;Наименование&nbsp;</b></td><td width=1><b>&nbsp;Качество&nbsp;</b></td><td width=1><b>&nbsp;Количество&nbsp;</b></td><td width=1><b>&nbsp;МФ&nbsp;</b></td></tr>
<!-- new line -->
<tr bgcolor=#444444><td colspan=4 align=center><input type="submit" value="Отправить"></td></tr>
</table>
</div>
</form>
<? } ?>
</body>
</html>