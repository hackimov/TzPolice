<h1>������, ������</h1>

<?php

	$sign[0] = 'police';
	$sign[1] = 'Police Academy';
	$sign[2] = 'Financial Academy';
	$sign[3] = 'Military Police';
	$sign[4] = 'Tribunal';

	if(abs(AccessLevel) & AccessDeptsManage) {
		if (isset($_REQUEST['add_dept'])) {
			$error = '';
			if ($_REQUEST['did']) {
				$SQL = 'SELECT `parent` FROM `sd_depts` WHERE `id`="'.$_REQUEST['did'].'"';
				list($parent) = mysql_fetch_array(mysql_query($SQL));
				if ($parent==0 && $_REQUEST['parent']>0) {
					$SQL = "SELECT `id` FROM `sd_depts` WHERE `parent`='".$_REQUEST['did']."'";
					if ($d = mysql_fetch_array(mysql_query($SQL))) {
						$error = '<font color="red"><b>���������� �������� ������</b></font><br><br>';
					}
				}

				if ($parent>0 && $_REQUEST['parent']==0) {
					$SQL = "SELECT `id` FROM `sd_cops` WHERE dept='".$_REQUEST['did']."'";
					if ($d = mysql_fetch_array(mysql_query($SQL))) {
						$error = "<font color='red'><b>����� �� ����</b></font><br><br>";
					}
				}

				if ($error) {
					echo $error;
				} else {
					$SQL = "UPDATE `sd_depts` SET name='".$_REQUEST['dname']."', sname='".$_REQUEST['sname']."', parent='".$_REQUEST['parent']."' WHERE id='".$_REQUEST['did']."'";
					$r = mysql_query($SQL);
					if ($result) { echo "<font color='green'>����� ������� ��������</font><br><br>"; }
					else { echo "<font color='red'><b>������ �� ����� �������������� ������</b></font><br><br>"; }
				}
			} else {
				$SQL = "INSERT INTO `sd_depts` VALUES ('', '".$_REQUEST['dname']."', '".$_REQUEST['sname']."', '".$_REQUEST['parent']."', 0, 0, 0, 0, 0)";
				$r = mysql_query($SQL);
				if ($result) { echo "<font color='green'>����� ������� ��������</font><br><br>"; }
				else { echo "<font color='red'><b>������ �� ����� ���������� ������</b></font><br><br>"; }
			}
		}

		if (isset($_REQUEST['add_cop'])) {
			
			$SQL = 'SELECT `id` FROM `site_users` WHERE `user_name`="'.$_REQUEST['pname'].'"';
			$rrr = mysql_fetch_assoc(mysql_query($SQL));
			$site_user_id = $rrr['id'];
				
			$SQL = 'SELECT `name`, `parent` FROM `sd_depts` WHERE `id`="'.$_REQUEST['dept'].'"';
			list($dname, $parent) = mysql_fetch_array(mysql_query($SQL));
			$SQL = "SELECT `id`, `name`, `chief`, `police` FROM `sd_cops` WHERE `dept`='".$_REQUEST['dept']."'";
			list($persid, $persname, $chief, $police) = mysql_fetch_array(mysql_query($SQL));
			if ($_REQUEST['pid']) {
				if ($_REQUEST['alias']) {
					$SQL = 'UPDATE `sd_cops` SET `alias`="'.$_REQUEST['dept'].'", `user_id` = "'.$site_user_id.'" WHERE `id`="'.$_REQUEST['pid'].'"';
				} else {
					$SQL = 'UPDATE `sd_cops` SET `alias`="0", `user_id` = "'.$site_user_id.'" WHERE `id`="'.$_REQUEST['pid'].'"';
				}
				mysql_query($SQL);
					
				if (($parent<2 || ($chief && $_REQUEST['chief'])) && $persid!=$_REQUEST['pid'] && $_REQUEST['alias'] == 0) {
					echo "<font color='red'><b>��������� �������� <img src='_imgs/clans/".$sign[$police].".gif'><a href='http://www.timezero.ru/info.pl?".$persname."' target='_blank'>".$persname."</a></b></font><br><br>";
				} else {
					if ($_REQUEST['alias']) {
						$SQL = 'UPDATE `sd_cops` SET `name`="'.$_REQUEST['pname'].'", `police`="'.$_REQUEST['pol'].'", `user_id` = "'.$site_user_id.'" WHERE `id`="'.$_REQUEST['pid'].'"';
					} else {
						$SQL = 'UPDATE `sd_cops` SET `name`="'.$_REQUEST['pname'].'", `dept`="'.$_REQUEST['dept'].'", `chief`="'.$_REQUEST['chief'].'", `deputy` = "'.$_REQUEST['deputy'].'", `police`="'.$_REQUEST['pol'].'", `user_id` = "'.$site_user_id.'" WHERE `id`="'.$_REQUEST['pid'].'"';
					}
					
					$r = mysql_query($SQL);
					
					if ($r) { echo "<font color='green'>������ � ���������� ������� ��������</font><br><br>"; }
					else { echo "<font color='red'><b>������ �� ����� �������������� ������ � ����������</b></font><br><br>"; }
				}
			} else {
				if (($parent<2 && $persname) || ($chief && $_REQUEST['chief'])) {
					echo "<font color='red'><b>��������� �������� <img src='_imgs/clans/".$sign[$police].".gif'><a href='http://www.timezero.ru/info.pl?".$persname."' target='_blank'>".$persname."</a></b></font><br><br>";
				} else {
					$SQL = "SELECT `name` FROM `sd_cops` WHERE `name`='".$_REQUEST['pname']."'";
					list($pname) = mysql_fetch_array(mysql_query($SQL));
					if ($pname && !($_REQUEST['alias'])) {
						echo '<font color="red"><b>��������� ��� ��� �������� �����</b></font><br><br>';
					} else {
						if (isset($_REQUEST['pol'])) { $polc = $_REQUEST['pol']; } else { $polc = 0; }
						$SQL = 'INSERT INTO `sd_cops` SET `name` = "'.$_REQUEST['pname'].'", `dept` = "'.$_REQUEST['dept'].'", `chief` = "'.$_REQUEST['chief'].'", `police` = "'.$polc.'", `alias` = "'.$_REQUEST['alias'].'", `user_id` = "'.$site_user_id.'", `deputy` = "'.$_REQUEST['deputy'].'"';
		//				echo ($SQL);
						$r = mysql_query($SQL) or die (mysql_error());
						if ($r) { echo "<font color='green'>��������� ������� ��������</font><br><br>"; }
						else { echo "<font color='red'><b>������ �� ����� ���������� ����������</b></font><br><br>"; }
					}
				}
			}
		}

		if ($_REQUEST['del_cop']) {
			$SQL = "DELETE FROM sd_cops WHERE id='".$_REQUEST['del_cop']."' AND dept='".$_REQUEST['dpt']."'";
			$r = mysql_query($SQL);
			if ($r) { echo "<font color='green'>��������� ������� ������</font><br><br>"; }
			else { echo "<font color='red'><b>������ �� ����� �������� ����������</b></font><br><br>"; }
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
							echo "<font color='green'>���������� ������ ������� �������</font><br><br>";
							$SQL = "DELETE FROM sd_depts WHERE id='".$id."'";
							$s = mysql_query($SQL);
							if ($s) { echo "<font color='green'>����� ������� ������</font><br><br>"; }
							else { $error = 1; echo "<font color='red'><b>������ �� ����� �������� ������</b></font><br><br>"; }
						} else { $error = 1; echo "<font color='red'><b>������ �� ����� �������� ����������� ������</b></font><br><br>"; }
					}
				}

				if (!$error) {
					$SQL = "DELETE FROM sd_cops WHERE dept='".$_REQUEST['del_dpt']."'";
					$r = mysql_query($SQL);
					if ($r) {
						echo "<font color='green'>���������� ������ ������� �������</font><br><br>";
						$SQL = "DELETE FROM sd_depts WHERE id='".$_REQUEST['del_dpt']."'";
						$r = mysql_query($SQL);
						if ($r) { echo "<font color='green'>����� ������� ������</font><br><br>"; }
						else { echo "<font color='red'><b>������ �� ����� �������� ������</b></font><br><br>"; }
					} else { echo "<font color='red'><b>������ �� ����� �������� ����������� ������</b></font><br><br>"; }
				}
			}
		}

?>
<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>�������� �����/����������: [<a href="javascript:dept_form(0);">new</a>] </strong> </p></td>
</tr><tr><td>
<form name="depts" method="post" action="?act=cops_depts">
<table>
<tr><td>������ ��������: </td><td><input type=hidden name=did><input name="dname" type="text" size="40" value=""></td></tr>
<tr><td>������� ��������: </td><td><input name="sname" type="text" size="40" value=""></td></tr>
<tr><td>���������: </td><td>
<select name="parent">
	<option value="0">��� ����������</option>
<?
	$SQL = "SELECT id, name FROM sd_depts WHERE parent=0";
	$managements = mysql_query($SQL);
	while (list($id, $name) = mysql_fetch_array($managements)) {
		echo '<option value="'.$id.'">'.$name.'</option>';
	}
?>

</select>
</td></tr>
<tr><td>&nbsp;</td><td><input style="CURSOR: hand; BACKGROUND-IMAGE: url(i/input.gif);" type="submit" name="add_dept" value="��������"></td></tr>
</table>
</form>
<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>�������� � ������: [<a href="javascript:cop_form(0);">new</a>] </strong> </p></td>
</tr><tr><td>
<form name="users" method="post" action="?act=cops_depts">
<table>
<tr><td>���: </td><td><input type=hidden name=pid><input name="pname" type="text" size="20" value=""></td></tr>
<tr><td>�����: </td><td>
<select name="dept">

<?php

	$SQL = "SELECT `id`, `name` FROM `sd_depts` WHERE `type`=0 ORDER BY `name`";
	$r=mysql_query($SQL);
	while (list($id, $name) = mysql_fetch_row($r)) {
		echo '<option value="'.$id.'">'.$name.'</option>';
	}
?>

</select>
</td></tr>
<tr><td>&nbsp;</td><td><input id="pol1" name="pol" type="checkbox" value="1">&nbsp;������� </td></tr>
<tr><td>&nbsp;</td><td><input id="pol2" name="pol" type="checkbox" value="2">&nbsp;��������� </td></tr>
<tr><td>&nbsp;</td><td><input id="pol3" name="pol" type="checkbox" value="3">&nbsp;������� ������� </td></tr>
<tr><td>&nbsp;</td><td><input id="pol4" name="pol" type="checkbox" value="4">&nbsp;��������</td></tr>
<tr><td>&nbsp;</td><td><input name="chief" type="checkbox" value="1">&nbsp;��������� </td></tr>
<tr><td>&nbsp;</td><td><input name="deputy" type="checkbox" value="1">&nbsp;����������� ���������� </td></tr>
<tr><td>&nbsp;</td><td><input name="alias" type="checkbox" value="1">&nbsp;�� ���������������� </td></tr>
<tr><td>&nbsp;</td><td><input style="CURSOR: hand; BACKGROUND-IMAGE: url(i/input.gif);" type="submit" name="add_cop" value="��������"></td></tr>
</table>
</form>
<script language='javascript'>
function dept_form(a,b,c,d) {
	if (a>0) {
		document.all("did").value = a;
		document.all("dname").value = b;
		document.all("sname").value = c;
		document.all("parent").value = d;
		document.all("add_dept").value = "�������������";
	} else {
		document.all("did").value = 0;
		document.all("dname").value = '';
		document.all("sname").value = '';
		document.all("parent").value = 0;
		document.all("add_dept").value = "��������";
	}
}

function cop_form(a,b,c,d,e,f) {
	if (a>0) {
       	document.all("pol1").checked = 0;
		document.all("pol2").checked = 0;
        document.all("pol3").checked = 0;
		document.all("chief").checked = 0;
		document.all("alias").checked = 0;
		document.all("pid").value = a;
		document.all("pname").value = b;
		document.all("dept").value = c;
        if (d == 1)
	       	{
    			document.all("pol1").checked = true;
			}
		if (d == 2)
       		{
           		document.all("pol2").checked = true;
           	}
       	if (d == 3)
       		{
           		document.all("pol3").checked = true;
           	}
		document.all("chief").checked = e;
        document.all("alias").checked = f;
        document.all("add_cop").value = "�������������";
	} else {
        document.all("pid").value = 0;
        document.all("pname").value = '';
        document.all("dept").value = 0;
        document.all("pol").checked = 0;
        document.all("chief").checked = 0;
        document.all("alias").checked = 0;
        document.all("add_cop").value = "��������";
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
		if(abs(AccessLevel) & AccessDeptsManage) { echo "&nbsp;<b>[<a href='?act=cops_depts&del_dpt=".$id."' onClick=\"if(!confirm('�� �������?')) {return false}\">X</a>] [<a href=\"javascript:dept_form(".$id.",'".$dept."','".$sname."',".$parent.");\">E</a>]</b>"; }
		echo ":</b></font>&nbsp;</td><td><img src='_imgs/clans/".$sign[$police].".gif'><b><a href='http://www.timezero.ru/info.pl?".$name."' target='_blank'>".$name."</a></b>".($alias?' (�� ����������������)':'');
		if(abs(AccessLevel) & AccessDeptsManage) { echo "&nbsp;<b>[<a href='?act=cops_depts&del_cop=".$pid."&dpt=".$id."' onClick=\"if(!confirm('�� �������?')) {return false}\">X</a>] [<a href=\"javascript:cop_form(".$pid.",'".$name."',".$id.",".$police.",".$chief.",".$alias.");\">E</a>]</b>"; }
	}
}

?>
</table><br>
<?

$SQL = "SELECT id, name, sname, parent FROM `sd_depts` WHERE `parent`=0 AND `type`=0";
$managements = mysql_query($SQL);
while (list($id, $name, $sname, $parent) = mysql_fetch_array($managements)) {
?>
<br>
<table cellSpacing="0" cellPadding="0" width="100%" border="0">
<?
	echo "<tr bgcolor=#F4ECD4><td height='20' align=center><b>&nbsp;".$name;
	if(abs(AccessLevel) & AccessDeptsManage) { echo "&nbsp;[<a href='?act=cops_depts&del_dpt=".$id."' onClick=\"if(!confirm('�� �������?')) {return false}\">X</a>] [<a href=\"javascript:dept_form(".$id.",'".$name."','".$sname."',".$parent.");\">E</a>]"; }
	echo "</b><br>\n";
	$SQL = "SELECT id, name, chief, police, alias FROM sd_cops WHERE dept=$id OR alias=$id";
	if (list($pid, $name, $chief, $police, $alias) = mysql_fetch_array(mysql_query($SQL))) {
		echo "&nbsp;<b>��������� ����������:</b></font>&nbsp;<img src='_imgs/clans/".$sign[$police].".gif'><b><a href='http://www.timezero.ru/info.pl?".$name."' target='_blank'>".$name."</a></b>".($alias?' (�� ����������������)':'')."\n";
		if(abs(AccessLevel) & AccessDeptsManage) { echo "&nbsp;<b>[<a href='?act=cops_depts&del_cop=".$pid."&dpt=".$id."' onClick=\"if(!confirm('�� �������?')) {return false}\">X</a>] [<a href=\"javascript:cop_form(".$pid.",'".$name."',".$id.",".$police.",".$chief.",".$alias.");\">E</a>]</b>"; }
	}
	echo "</td></tr>";
	$SQL = "SELECT id, name, sname, parent FROM sd_depts WHERE parent='".$id."'";
	$depts = mysql_query($SQL);
	while (list($id, $name, $sname, $parent) = mysql_fetch_array($depts)) {
		$dept_list = Array();
		$toprint = '<tr><td height=\'20\' background=\'i/bgr-grid-sand.gif\'><p><img src=\'i/bullet-red-01a.gif\' width=\'18\' height=\'11\' hspace=\'5\'><strong>';
		$toprint .= "<a onclick=\"javascript:if(d".$id.".style.display=='none') d".$id.".style.display=''; else d".$id.".style.display='none';\" href=\"javascript:{}\">".$name."</a>";
		if(abs(AccessLevel) & AccessDeptsManage) {
			$toprint .= "&nbsp;<b>[<a href='?act=cops_depts&del_dpt=".$id."' onClick=\"if(!confirm('�� �������?')) {return false;}\">X</a>] [<a href=\"javascript:dept_form(".$id.",'".$name."','".$sname."',".$parent.");\">E</a>]";
		}
		
		if(abs(AccessLevel) & (AuthUserClan == 'police' || AuthUserGroup == 100)) {
			$toprint .= " [{zamena_na_copy}]";
		}
		$toprint .= "</b>";
		$toprint .= '</strong></td></tr>';
		$toprint .= '<tr><td><div id="d'.$id.'" style="display:none; margin-top: 5px;">';
		
		$SQL = "SELECT id, name, chief, police, alias, deputy FROM sd_cops WHERE dept='".$id."' ORDER BY chief DESC, deputy DESC, police, name";
		$cops = mysql_query($SQL);
		$z = 1;
		
		while (list($pid, $name, $chief, $police, $alias, $deputy) = mysql_fetch_array($cops)) {
			if ($chief) {
				$toprint .= "<font style='MARGIN-LEFT: 10px' color='#A92C22'><b>��������� ������:</b></font>&nbsp;<img src='_imgs/clans/".$sign[$police].".gif'><b><a href='http://www.timezero.ru/info.pl?".$name."' target='_blank'>".$name."</a></b>".($alias?' (�� ����������������)':'');
				if(abs(AccessLevel) & AccessDeptsManage) {
					$toprint .= "&nbsp;<b>[<a href='?act=cops_depts&del_cop=".$pid."&dpt=".$id."' onClick=\"if(!confirm('�� �������?')) {return false}\">X</a>] [<a href=\"javascript:cop_form(".$pid.",'".$name."',".$id.",".$police.",".$chief.",".$alias.");\">E</a>]</b>";
				}
                                $toprint .= '<br>';
                        } elseif ($deputy){
                          $toprint .= "<font style='MARGIN-LEFT: 10px' color='#A92C22'><b>����������� ����������:</b></font>&nbsp;<img src='_imgs/clans/".$sign[$police].".gif'><b><a href='http://www.timezero.ru/info.pl?".$name."' target='_blank'>".$name."</a></b>".($alias?' (�� ����������������)':'');
                          if(abs(AccessLevel) & AccessDeptsManage) {
                                  $toprint .= "&nbsp;<b>[<a href='?act=cops_depts&del_cop=".$pid."&dpt=".$id."' onClick=\"if(!confirm('�� �������?')) {return false}\">X</a>] [<a href=\"javascript:cop_form(".$pid.",'".$name."',".$id.",".$police.",".$chief.",".$alias.");\">E</a>]</b>";
                          }
                          $toprint .= '<br>';
			}else{
				if ($z) {
					$z = 0;
					$toprint .= "<font style='MARGIN-LEFT: 10px' color='#A92C22'><b>����������:</b></font><ol style='MARGIN-TOP: 0px'>";
				}
				$toprint .= "<li><b><img src='_imgs/clans/".$sign[$police].".gif'><a href='http://www.timezero.ru/info.pl?".$name."' target='_blank'>".$name."</a></b>".($alias?' (�� ����������������)':'');
				if(abs(AccessLevel) & AccessDeptsManage){
					$toprint .= "&nbsp;<b>[<a href='?act=cops_depts&del_cop=".$pid."&dpt=".$id."' onClick=\"if(!confirm('�� �������?')) {return false}\">X</a>] [<a href=\"javascript:cop_form(".$pid.",'".$name."',".$id.",".$police.",".$chief.",".$alias.");\">E</a>]</b>";
				}
                                $toprint .= '</li>';
			}
			
			$dept_list[] = $name;
		}
                if ($z == 0) $toprint .= '</ol>';
		
		$toprint .= '<br><br>';
		$toprint .= '</div></td></tr>';
		
		$dept_list = implode(', ', $dept_list);
		$copy_string = "<a href='#' onClick=\"window.clipboardData.setData('Text','".$dept_list."'); return false;\">C</a>";
		
		$toprint = str_replace('{zamena_na_copy}', $copy_string, $toprint);
		echo $toprint;
	}

?>
</table>
<?
}
?>
<br><hr><br>
<table cellSpacing="0" cellPadding="0" width="100%" border="0">
<?

$SQL = 'SELECT `id`, `name`, `sname`, `parent` FROM `sd_depts` WHERE `parent`=2';
$depts = mysql_query($SQL);
while (list($id, $name, $sname, $parent) = mysql_fetch_array($depts)) {
?>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><? echo "<a onclick=\"javascript:if(d".$id.".style.display=='none') d".$id.".style.display=''; else d".$id.".style.display='none';\" href=\"javascript:{}\">".$name."</a>"; ?>
<? if(abs(AccessLevel) & AccessDeptsManage) { echo "&nbsp;<b>[<a href='?act=cops_depts&del_dpt=".$id."' onClick=\"if(!confirm('�� �������?')) {return false}\">X</a>] [<a href=\"javascript:dept_form(".$id.",'".$name."','".$sname."',".$parent.");\">E</a>]</b>"; } ?></strong></td></tr>
<tr><td><div id="d<?=$id?>" style="display:none; margin-top: 5px;">
<?
	$SQL = "SELECT id, name, chief, police, alias, deputy FROM sd_cops WHERE dept='".$id."' ORDER BY chief DESC, police, name";
	$cops = mysql_query($SQL);
    $listst = 1;
	while (list($pid, $name, $chief, $police, $alias, $deputy) = mysql_fetch_array($cops)) {
		if ($chief) {
			echo "<font style='MARGIN-LEFT: 10px' color='#A92C22'><b>��������� ������:</b></font>&nbsp;<img src='_imgs/clans/".$sign[$police].".gif'><b><a href='http://www.timezero.ru/info.pl?".$name."' target='_blank'>".$name."</a></b>".($alias?' (�� ����������������)':'');
			if(abs(AccessLevel) & AccessDeptsManage) {
				echo "&nbsp;<b>[<a href='?act=cops_depts&del_cop=".$pid."&dpt=".$id."' onClick=\"if(!confirm('�� �������?')) {return false}\">X</a>] [<a href=\"javascript:cop_form(".$pid.",'".$name."',".$id.",".$police.",".$chief.",".$alias.");\">E</a>]</b>";
			}
                        echo '<br>';
                } elseif ($deputy){
                  echo "<font style='MARGIN-LEFT: 10px' color='#A92C22'><b>����������� ����������:</b></font>&nbsp;<img src='_imgs/clans/".$sign[$police].".gif'><b><a href='http://www.timezero.ru/info.pl?".$name."' target='_blank'>".$name."</a></b>".($alias?' (�� ����������������)':'');
                  if(abs(AccessLevel) & AccessDeptsManage) {
                          echo "&nbsp;<b>[<a href='?act=cops_depts&del_cop=".$pid."&dpt=".$id."' onClick=\"if(!confirm('�� �������?')) {return false}\">X</a>] [<a href=\"javascript:cop_form(".$pid.",'".$name."',".$id.",".$police.",".$chief.",".$alias.");\">E</a>]</b>";
                  }
                  echo '<br>';
		} else {
			if ($listst) {
				echo "<font style='MARGIN-LEFT: 10px' color='#A92C22'><b>����������:</b></font><ol style='MARGIN-TOP: 0px'>";
				$listst=0;
			}
			echo "<li><b><img src='_imgs/clans/".$sign[$police].".gif'><a href='http://www.timezero.ru/info.pl?".$name."' target='_blank'>".$name."</a></b>";
			if(abs(AccessLevel) & AccessDeptsManage) {
				echo "&nbsp;<b>[<a href='?act=cops_depts&del_cop=".$pid."&dpt=".$id."' onClick=\"if(!confirm('�� �������?')) {return false}\">X</a>] [<a href=\"javascript:cop_form(".$pid.",'".$name."',".$id.",".$police.",".$chief.",".$alias.");\">E</a>]</b>";
			}
                        echo '</li>';
		}
	}
        if ($listst == 0) echo '</ol>';

?>
<br><br>
</div></td></tr>
<?
	}
?>
</table>