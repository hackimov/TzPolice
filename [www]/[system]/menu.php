<?php
if(!defined('MENU')) die('Wood ;)');

$where[] = "a.inmenu = '1'";
if($user['user_group'] != 100) {	$where[] = "a.active = '1'";
}
$where = implode(" AND ",$where);
$menu = Array();
$menulist = $db->sql_query("SELECT a.*,b.* FROM modules AS a LEFT JOIN menus AS b ON(a.menuid=b.id) WHERE $where ORDER BY b.menuposition,a.position");

while($m = $db->sql_fetchrow($menulist)) {	if($m['active'] == 1) {    	if($m['view'] == 1 || $user['user_group'] == $adminGroup) {       		$menu[$m['menuname']][] = Array("?act=".$m['name'],$m['desription']);
    	} else {    		#� ������ ���� ����� �������
			if($user && ($m['user_access'] || $m['group_access'])) {
				#������� � ������ �� ������
				if($m['user_access'] != '') {
					foreach(explode(",",$m['user_access']) as $k => $v) {
						$v = explode(":",$v);
						#���� ����� ����� - ������ � ���� ����� ������� �������
						if($user['user_name'] == $v[0]) {
							$menu[$m['menuname']][] = Array("?act=".$m['name'],$m['desription']);
						break;
						}
					}
				}
				#������� � ������ �� ������
				if($m['group_access'] != '') {
					foreach(explode(",",$m['group_access']) as $k => $v) {
						$v = explode(":",$v);
						#���� ���� � ������ � �������� � ��������� ��������� - ������ � ���� ����� ������� �������
						if($user['group'] == $v[0] && $user['subgroup'] >= $v[1]) {
							$menu[$m['menuname']][] = Array("?act=".$m['name'],$m['desription']);
						break;
						}
					}
				}
			}

    	}

	} else {		$menu[$m['menuname']][] = Array("?act=".$m['name'],$m['desription']);
	}
}
echo "<script language=\"javascript\" type=\"text/javascript\">\n";
echo "var menu=\"\";\n";
foreach($menu as $h => $menulist) {	echo "menu += mu(1,'?','$h',0,0,0);\n";
	foreach($menulist as $k => $list) {		echo "menu += mu(2,'$list[0]','$list[1]',0,0,0);\n";
	}
}
echo "menu += '</div>';\n";
echo "document.write(menu);\n";
echo "</script>\n";
?>