<?php
#�������������� ���� � ������ ����

$query = mysql_query("SELECT *,MAX(level) as access FROM deorg_access WHERE (module='black' AND name='".AuthUserName."' AND `type`='login') OR (name='".AuthUserClan."' AND type='clan' AND module='black')");
$access = mysql_fetch_array($query);

if (($access['access'] > 0 && ($access['expire'] == 0 || $access['expire'] > time))) {
	$showall = $access['access'];
}
#error_reporting(1);
#������ �����
$profs = array("", "������", "�������", "���������", "�������", "�������", "��������",
"�����������.����������.","�����������.���������.","�����������.����������.",
"���������","��������","�������","���������","���-�����","���-������","���-�����","��������","","","","","","","","",
"�������� �����","��������� �����","������ �����","", "�����");

$rank_names = array("�������","�������","������� �������","�������","������� �������",
"������� ���������","���������","������� ���������",
"�������","�����","������������","���������","�������-�����","�������-���������","�������-���������",
"������","��������","���� ������","�.�.�");

$pverank_names = array("��������","��������","��������","��������","�������",
"��������","�����������","�����������","����� ��������","����������","������� ��������","�.�.�");

$fractionslist = Array('1px','Invasion','RANGERS');

$sortlist = Array();

$sortlist[0] = Array(
'addtime' => '����������: �� ����',
'login' => '����������: �� ������',
'clan' => '����������: �� �����',
'lvl' => '����������: �������',
'rank' => '����������: ��� �����',
'pro' => '����������: ���������');

$sortlist[1] = Array(
'addtime' => '����������: �� ����'
);

$typelist = Array('login'=>'������������ ��','clan'=>'����� � �������');
$typelist['marclan'] = '���-�����';
$typelist['bdclan'] = '������ ��������';

$limitslist = Array(25,50,100,999999);

$selectedorderlist = Array('DESC'=>'������ ����','ASC'=>'����� �����');


#������ ������
function generateUser($data,$type) {
	global $profs,$rank_names;
	$data['lvl'] = ($data['lvl'])?$data['lvl']:$data['level'];
	$data['level'] = ($data['level'])?$data['level']:$data['lvl'];
	$data['rank'] = ($data['rank'])?$data['rank']:$data['pvprank'];
	if($type == 'clan' || $type == 'marclan' || $type == 'bdclan') {
		$output = "<img src='http://timezero.ru/i/clans/".$data['clan'].".gif' class='inlineimg' border=0 alt='".$data['clan']."' title='".$data['clan']."'><b>".$data['clan']."</b>";
	} else {
		$output = ($data['clan'])?"<img src='http://timezero.ru/i/clans/".$data['clan'].".gif' border=0  class='inlineimg' alt='".$data['clan']."' title='".$data['clan']."'>":"";
		$output .= "<b>".$data['login']."</b> [".$data['level']."]";
		$output .= "<a href='http://www.timezero.ru/info.pl?".$data['login']."' border=0  target=_blank><img border=0  src='http://timezero.ru/i/i".$data['pro'].".gif' class='inlineimg' alt='".$profs[$data['pro']]."' title='".$profs[$data['pro']]."'></a>";
		$output .= "<img src='http://timezero.ru/i/rank/".$data['rank'].".gif' border=0  class='inlineimg' alt='".$rank_names[$data['rank']]."' title='".$rank_names[$data['rank']]."'>";
	}
	return $output;
}
#������ �����
function generateLogs($logs) {
	#28406084321793
	foreach(explode(',',$logs) as $k => $log) {
		$log = trim($log);
		if(!$log || !is_numeric($log) || strlen($log) < 12) continue;
		$output[] = "<a href=http://www.timezero.ru/sbtl.ru.html?".$log." target=_blank>$log</a>";
	}
	return "<a href='javascript:createWindow(400,100,\"".implode(", ",$output)."\");'>������ ����� (".count($output).")</a>";
}
function generatePages($findall,$limit,$page) {
	global $in;
    $pages = ceil($findall/$limit);

	for($i=1;$i<=$pages;$i++) {
		$qstring = ($in['page'])?preg_replace("#page=".$in['page']."#i","page=$i",$_SERVER['QUERY_STRING'],1):$_SERVER['QUERY_STRING']."&page=$i";
    	$output .= ($i == $page)?" <b>$i</b>":" [<a href='http://tzpolice.ru?$qstring'>$i</a>]";
	}

	return $output;
}

function generateMenu() {
	global $in,$showall;
    if($showall < 1) return;
    $output = "<div align=right style='font-size: 12px; padding: 5px;'>";
    $output .= (!$in['do'])?"<b><u>������ ������</u></b>":"<a href='?act=".$in['act']."'>������ ������</a>";
    if($showall > 1) {
    	$output .= ($in['do'] == 'add')?" | <b><u>�������� ��</u></b>":" | <a href='?act=".$in['act']."&do=add'>�������� ��</a>";
    }
    $output .= ($in['do'] == 'plugin')?" | <b><u>������</u></b>":" | <a href='?act=".$in['act']."&do=plugin' style='color: red'>������</a>";
    if($showall > 2) {
    	$output .= ($in['do'] == 'leave')?" | <b><u>�������� ��</u></b>":" | <a href='?act=".$in['act']."&do=leave'>�������� ��</a>";
    	$output .= ($in['do'] == 'access')?" | <b><u>�������</u></b>":" | <a href='?act=".$in['act']."&do=access'>�������</a>";
    }
    $output .= "</div>";
    return $output;
}

echo generateMenu();



function  blacklist() {
	global $in,$showall,$sortlist,$typelist,$limitslist,$selectedorderlist;
    $arr = ($in['type'] == 'clan' || $in['type'] == 'marclan' || $in['type'] == 'bdclan')?1:0;
    $in['sort'] = ($sortlist[$arr][$in['sort']])?$in['sort']:'addtime';
    $type = ($typelist[$in['type']])?$in['type']:'login';

	if($in['type'] == 'clan' || $in['type'] == 'marclan' || $in['type'] == 'bdclan') {
    	$selectedsortlist = $sortlist[1];
    	$whofield = '����';
    	$header = "�����";
    } else {
        $selectedsortlist = $sortlist[0];
    	$whofield = '�����';
    	$header = "���������";
    }

	if(AuthUserName != '') {
		$query = mysql_query("SELECT * FROM deorg_black WHERE login='".AuthUserName."' AND type='login' AND status > 0");
       	if(mysql_num_rows($query) > 0) {
       		$black = mysql_fetch_array($query);
            $blackinfo = "<b>".$black['login']."</b>! ��� �������� ��������� � ������ ������ �������, ��������� ������: ".$black['contribution']."<br>
            ���������� � ������������� �� �� ������� ��� ������� ������� �������.";
       	} elseif(mysql_num_rows($query) < 1 && AuthUserClan != '') {
           $query = mysql_query("SELECT * FROM deorg_black WHERE clan='".AuthUserClan."' AND (type='clan' OR type='marclan' OR type='bdclan') AND status > 0");
			if(mysql_num_rows($query) > 0) {
            	$black = mysql_fetch_array($query);
				$blackinfo = "<b>".AuthUserName."</b>! ��� ���� ��������� � ������ ������ �������.<br>
				���������� � ������������� �� �� ������� ��� ������� ������� �������.";
			}
       	}
       	if($blackinfo) {
       		echo "<center><div style='width: 80%; border: 1px dashed red; padding: 5px; margin: 3px;'>$blackinfo</div></center>";
       	}

	}
    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  */
	$typebutton .= "<select name='type' class='s' style='color: red; width: 135px;' onchange='document.forms.finder.submit();'>
	";
	foreach($typelist as $k => $v) {
		$sel = ($in['type'] == $k)?" SELECTED":"";
		$typebutton .= "<option value='$k'$sel>$v</option>";
	}
	$typebutton .= "</select>";

	$selectbuttons .= "<select name='sort' class='s' onchange='document.forms.finder.submit();'>";
	foreach($selectedsortlist as $k => $v) {
		$sel = ($in['sort'] == $k)?" SELECTED":"";
		$selectbuttons .= "<option value='$k'$sel>$v</option>";
	}
	$selectbuttons .= "</select>
	<select name='order' class='s' onchange='document.forms.finder.submit();'>";
	foreach($selectedorderlist as $k => $v) {
		$sel = ($in['order'] == $k)?" SELECTED":"";
		$selectbuttons .= "<option value='$k'$sel>$v</option>";
	}
	$selectbuttons .= "</select>
    <select name='limit' class='s' style='width: 135px;' onchange='document.forms.finder.submit();'>
	";
	foreach($limitslist as $k => $v) {
		$sel = ($in['limit'] == $v)?" SELECTED":"";
		$selectbuttons .= "<option value='$v'$sel>".($v==999999?"��� ��������":$v." �� ��������")."</option>";
	}
	$selectbuttons .= "</select>";

	

    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  */
    $in['slogin'] = ($in['slogin'])?$in['slogin']:'�����';
    $in['sclan'] = ($in['sclan'])?$in['sclan']:'����';
    $in['sbattle'] = ($in['sbattle'])?$in['sbattle']:'��� ���';
    $findbuttons = "
    <input name='slogin' value='".$in['slogin']."' type='text' value='' class=s style='width: 175px;' onfocus='clear_field(this)' onblur='check_field(this)'>
    <input name='sclan' value='".$in['sclan']."' type='text' value='' class=s style='width: 175px;' onfocus='clear_field(this)' onblur='check_field(this)'>
    <input name='sbattle' value='".$in['sbattle']."' type='text' value='' class=s style='width: 175px;' onfocus='clear_field(this)' onblur='check_field(this)'>";
	


	
	if($showall > 0) {
		$in['minlvl'] = ($in['minlvl'])?($in['minlvl']<1?1:$in['minlvl']):'1';
		$in['maxlvl'] = ($in['maxlvl'])?($in['maxlvl']>20?20:$in['maxlvl']):'20';
		$findbuttons .= "<select name='minlvl' class='s'>";
		for($x = 1; $x<21; $x++) {
			$sel = ($in['minlvl'] == $x)?" SELECTED":"";
			$findbuttons .= "<option value='$x'$sel>$x</option>";
		}
		$findbuttons .= "</select>";

		$findbuttons .= "<select name='maxlvl' class='s'>";
		for($x = 1; $x<21; $x++) {
			$sel = ($in['maxlvl'] == $x)?" SELECTED":"";
			$findbuttons .= "<option value='$x'$sel>$x</option>";
		}
		$findbuttons .= "</select>";
	} else {
		unset($in['minlvl']);
		unset($in['maxlvl']);
	}

   	$findbuttons .="<input type='button' onclick='resetSearch();' value='�����' class=s> <input type='submit' value='�����' class=s>".($showall > 0?" <input type='button' onclick='javascript:createWindow(400,100,\"��� ������ ��� ���\");' value='2 ���' class=s>":"");

    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  */
    $where[] = "type = '".$type."'";
	$where[] = "status > 0";
	if($in['find'] > 0) {
		if($in['slogin'] != '�����' && $in['slogin']) {
        	$where[] = "`login` LIKE '%".$in['slogin']."%'";
		}
        if($in['sclan'] != '����' && $in['sclan']) {
        	$where[] = "`clan` LIKE '%".$in['sclan']."%'";
		}
		if($in['sbattle'] != '��� ���' && $in['sbattle']) {
        	$where[] = "`logs` LIKE '%".$in['sbattle']."%'";
		}
		if($in['minlvl']) {
        	$where[] = "`lvl` >= ".$in['minlvl'];
		}
		if($in['maxlvl']) {
        	$where[] = "`lvl` <= ".$in['maxlvl'];
		}
	}


	$where = implode(' AND ', $where);

	$limit = (ceil($in['limit']) > 0)?ceil($in['limit']):25;
	$page = ($in['page'] > 0)?ceil($in['page']):1;
	$offset = ($page-1)*$limit;

	$sortby = $in['sort'];
	$ordeby = ($selectedorderlist[$in['order']])?$in['order']:'DESC';

	$query = mysql_query("SELECT * FROM deorg_black WHERE $where");
	$findall = mysql_num_rows($query);

    $pageslist = generatePages($findall,$limit,$page);


$output = "
		<H3>׸���� ������: $header</H3>
		
			<form name='finder' id='finder' method=GET>
			<div align=center>
				<input type='hidden' name='act' value='".$in['act']."'>
				<input type='hidden' name='find' value='1'>
				�����: $findbuttons
			
        </div><hr>
        <div align=right style='display: inline;'>
			
				<input type='hidden' name='act' value='".$in['act']."'>
				<input type='hidden' name='page' value='".$in['page']."'>
				$selectbuttons
				$typebutton
</div>
			</form>
		
        <div align=left style='padding: 5px;'>��������: $pageslist</div>

		<table style='width: 100%; font-size: 12px;' border=1 cellpadding=5 cellspacing=1>
		<tr>
		<th>#</th>
		<th>�</th>
		<th>$whofield</th>
		<th>�������<br />����</th>
		<th>�����</th>
	"; 

	if($showall > 0) {
		$output .= "
		<th>���� ����������</th>
		<th>�����������</th>
		";
	}
	if($showall > 1) {
		$output .= "
		<th>x</th>
		";
	}

	$output .= "</tr>
	";
	$cols = 4;
    if($showall == 1) {
    	$cols = 6;
    } elseif($showall > 1) {
    	$cols = 7;
    }
	$sql = "SELECT * FROM deorg_black WHERE $where ORDER BY `$sortby` $ordeby LIMIT $offset,$limit";
	$query = mysql_query($sql);
	#echo "$sql<hr />";
	$count = mysql_num_rows($query);
	if($count > 0) {
		$i = 1;
		$kpk = "";
		while ($black = mysql_fetch_array($query)) {
			$logs = ($black['logs'])?generateLogs($black['logs']):"";
			$black['description'] = str_replace("\n","<br />",$black['description']);
			$black['hidedescr'] = str_replace("\n","<br />",$black['hidedescr']);
			$name = ($black['login'])?$black['login']:$black['clan'];
            $black['hidedescr'] = ($black['hidedescr'])?$black['hidedescr']."<br>":"";
			$description = ($black['logs'] && $black['description'])?$black['description']."<br>".$logs:$black['description'].$logs;

			if(strlen($black['hidedescr']) > 50) {
				$black['hidedescr'] = "<a href='javascript:createWindow(400,100,\"".addslashes($black['hidedescr'])."\");'>�������������� ����������</a><br>";
			}
			$output .= "
				<tr>
					<th>$i</th>
					<th>
						<div id='d_clip_container1_top' style='position:relative'>
						<img src='/i/copy.gif' id='d_clip_container1' onmouseover='createClipFla(this,\"$name\");' alt='���������� � �����' title='���������� � �����'>
						</div>
					</th>
	                <td style='padding-left: 15px;' nowrap>".generateUser($black,$type)."</td>
	                <td align=center>$description</td>
	                <td align=center nowrap>".$black['contribution']."</td>
			";
			
			$kpk .= '&amp;lt;item name=\"'.$black['login'].'\" /&amp;gt;<br>';
			
			if($showall > 0) {
				$output .= "
					<th>".date('d.m.Y H:i:s',$black['addtime'])."</th>
					<td align=center>".$black['hidedescr']." �������: <b>".$black['author']."</b></td>
				";
			}

			if($showall > 1) {
				$output .= "
				<th nowrap>
				<a href='?act=".$in['act']."&do=edit&id=".$black['id']."'>v</a>
				|
				<a href='?act=".$in['act']."&do=del&id=".$black['id']."' onclick='javascript:if(!confirm(\"�������? ��� ������� ������ �� ������.\")) return false;'>x</a>
				</th>";
			}
			$output .= "</tr>";
		$i++;
		}
	} else {
		$output .= "
		<tr>
		<th colspan=$cols>�� ������� ������� ������ �� �������</th>
		</tr>
		";
	}

$output .= "</table>
<div align=left style='padding: 5px;' >��������: $pageslist</div>";

$output = str_replace("��� ������ ��� ���",$kpk,$output);

echo $output;

}


function addblack() {
	global $in,$showall;
	if($showall < 2) {
		echo "� ��� ��� ���� �� ��� ��������.
		<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
	} else {
		if(!$in['process']) {
			$tsel1 = ($in['type'] == 'clan')?' SELECTED':'';
			$tsel2 = ($in['type'] == 'marclan')?' SELECTED':'';
			$tsel3 = ($in['type'] == 'bdclan')?' SELECTED':'';
			

			$asel = ($in['align'] == 'MP')?' SELECTED':'';
     		echo "
			<H3>�������� ������ ��� ����</H3>
			<script>
				function checkFields() {
					var form = document.forms.selecter;
					if(!form.elements.name.value) {
						alert('����� ������ ��� ������ ���������, ��� �� ��-���� �������?)');
						return false;
					}
                    return true;
				}
			</script>
			<form name='selecter' id='selecter' method=POST onsubmit='if(!checkFields()) return false;'>
			<input name='act' type='hidden' value='".$in['act']."'>
			<input name='do' type='hidden' value='add'>
			<input name='process' type='hidden' value='check'>
			<table style='width: 100%; font-size: 12px;' border=1 cellpadding=5 cellspacing=0>
			<tr>
				<td style='padding-left: 15px;'>����� ��� ����:</td>
			    <td nowrap>
			    	<input name='name' type='text' style='width: 250px;' value='".$in['name']."' class=s>
			    	<select name='type' class=s>
			    		<option value='login'>������������ ��</option>
			    		<option value='clan'$tsel1>����</option>
						<option value='marclan'$tsel2>���-����</option>
			    		<option value='bdclan'$tsel3>������ ��������</option>
			    	</select>
			    </td>
			</tr>
			<tr>
				<td style='padding-left: 15px;'>���� ���(����� �������):</td>
			    <td><textarea name='logs' style='width: 100%; height: 100px;'>".$in['logs']."</textarea></td>
			</tr>
			<tr>
				<td style='padding-left: 15px;'>�������:</td>
			    <td><textarea name='description' style='width: 100%; height: 100px;'>".$in['description']."</textarea></td>
			</tr>
			<tr>
				<td style='padding-left: 15px;'>�������������(����� ������ �������):</td>
			    <td><textarea name='hidedescr' style='width: 100%; height: 100px;'>".$in['hidedescr']."</textarea></td>
			</tr>
			<tr>
				<td style='padding-left: 15px;'>����� ������:</td>
			    <td><input name='contribution' type='text' value='".$in['contribution']."' class=s></td>
			</tr>
			<tr>
				<td style='padding-left: 15px;'>��� ��:</td>
			    <td>
			    	<select name='align' class=s>
			    		<option value='police'>�� �������</option>
			    		<option value='MP'$asel>����! :crazy:</option>
			    	</select>
			    </td>
			</tr>
			<tr>
				<td align=center colspan=2><input type='submit' value='����������' class=s></td>
			</tr>

            </table>

			</form>
     	";
        } elseif($in['process'] == 'check') {
			if(!$in['name']) {
        		$userinfo['error'] = "��� ��� �������� ����� ���-�� ����������, ���� ��������� ����� � ������ ������((";
        	} else {
	        	if($in['type'] == 'login') {
	        		$query = mysql_query("SELECT * FROM deorg_black WHERE login='".$in['name']."' AND type='login' AND status > 0");
	        		if(mysql_num_rows($query) < 1) {
	        			$userinfo = locateUser($in['name']);

	        			if($userinfo['clan'] != '') {
		        			$query = mysql_query("SELECT * FROM deorg_black WHERE clan='".$userinfo['clan']."' AND type='clan' AND status > 0");
		        			if(mysql_num_rows($query) > 0) {
		        				$userinfo['error'] = "� ��� ����� � ������ ����� ���������.";
		        			}
	        			}
	        		} else {
	        			$userinfo['error'] = "����� �������� ��� ���� � ������ ��";
	        		}
	         	} else {
	         		$query = mysql_query("SELECT * FROM deorg_black WHERE clan='".$in['name']."' AND type='clan' AND status > 0");
	        		if(mysql_num_rows($query) < 1) {
	        			$userinfo = locateUser($in['name'],'clan');
	        		} else {
	        			$userinfo['error'] = "� ���� ������ �� ��� �����.";
	        		}
	         	}
			}
         	echo "
        	<H3>����� ��, �������� (=</H3>
            <form name='selecter' id='selecter' method=POST>
			<input name='act' type='hidden' value='".$in['act']."'>
			<input name='do' type='hidden' value='add'>
            <input name='name' type='hidden' value='".$in['name']."'>
            <input name='type' type='hidden' value='".$in['type']."'>

            <input name='login' type='hidden' value='".$userinfo['login']."'>
            <input name='clan' type='hidden' value='".$userinfo['clan']."'>
            <input name='level' type='hidden' value='".$userinfo['level']."'>
            <input name='pro' type='hidden' value='".$userinfo['pro']."'>
            <input name='rank' type='hidden' value='".$userinfo['pvprank']."'>

            <textarea style='display:none;' name='logs'>".$in['logs']."</textarea>
            <textarea style='display:none;' name='description'>".$in['description']."</textarea>
            <textarea style='display:none;' name='hidedescr'>".$in['hidedescr']."</textarea>
            <input name='contribution' type='hidden' value='".$in['contribution']."'>
            <input name='align' type='hidden' value='".$in['align']."'>
        	";
         	if($userinfo['error']) {
         		echo "<span style='color: red;'><b>���������� ������</b>: ".$userinfo['error']."</span><br>";
         		echo "<input type='submit' value='�����'>";
         	} else {
         		$attack = ($in['type'] == 'login')?"�������� � �� ���������":"�������� ����� �����";
         		echo "�� ����������� $attack ".generateUser($userinfo,$in['type'])."<hr>";
         		echo "<input name='process' type='hidden' value='ok'>
         		<input type='submit' value='����������'>";
         	}
        } elseif($in['process'] == 'ok') {
        	if(!$in['login'] && !$in['clan']) {
        		echo "��� ��� �������� ����� ���-�� ����������, ���� ��������� ����� � ������ ������((";
        	} else {
	        	if($in['type'] == 'login') {
	        		$query = mysql_query("SELECT * FROM deorg_black WHERE login='".$in['name']."' AND type='login' AND status > 0");
	        		if(mysql_num_rows($query) > 0) {
	        			$error = "�������� ��� ��������";
	        		}
	         	} else {
	         		$query = mysql_query("SELECT * FROM deorg_black WHERE clan='".$in['name']."' AND type='clan' AND status > 0");
	        		if(mysql_num_rows($query)  > 0) {
	        			$error = "���� ��� ��������";
	        		}
	         	}
                if(!$error) {
					foreach(explode(',',$in['logs']) as $k => $log) {
						$log = trim($log);
						if(!$log || !is_numeric($log) || strlen($log) < 12) continue;
						$logs[] = $log;
					}
					$in['logs'] = implode(',',$logs);

	                $query = "
	            	INSERT INTO  `deorg_black` (`id`,`login`,`clan`,`lvl`,`pro`,`rank`,`description`,`logs`,`author`,`hidedescr`,`addtime`,`contribution`,`status`,`align`,`type`)
					VALUES(NULL,'".$in['login']."','".$in['clan']."','".$in['level']."','".$in['pro']."','".$in['rank']."','".$in['description']."','".$in['logs']."','".AuthUserName."','".$in['hidedescr']."','".time()."','".$in['contribution']."','1','".$in['align']."','".$in['type']."')
					";
	            	$insert = mysql_query($query);

					if(!mysql_error($insert)) {
						echo "�� ������ ������, ������ ��� ��������� �� ��������� ��...
						<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
					} else {
						echo "���, ������ � ���� ������, ������ ������ � ������� �����������)) ������ ��� ��������� �� ��������� ��...
						<meta http-equiv='refresh' content='5; url=/?act=".$in['act']."'>";
					}
				} else {
					echo "$error
					<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
				}

        	}

        }
	}
}


function deleteblack() {
	global $in,$showall;
    $id = ceil($in['id']);

	if($showall < 2 || ($showall < 3 && $in['absolutely'] > 0) || ($showall < 3 && $in['return'] > 0)) {
		echo "� ��� ��� ���� �� ��� ��������.
		<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
	} else {
		if($id < 1) {
			echo "� ��� ID ������? �.�.�.
			<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
		} else {
			if($in['absolutely'] > 0) {
				$query = "DELETE FROM deorg_black WHERE id='$id'";
			} elseif($in['return'] > 0) {
				$query = "UPDATE deorg_black SET status = '1', whodel='', deltime='' WHERE id='$id'";
			} else {
				$query = "UPDATE deorg_black SET status = '0', whodel='".AuthUserName."', deltime='".time()."' WHERE id='$id'";
			}
			$delete = mysql_query($query);
            $link = ($in['pre'])?'&do='.$in['pre']:'';
			if(!mysql_error($delete)) {
				echo "�� ������ ������, ������ ��� ��������� �� ��������� ��...
				<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."$link'>";
			} else {
				echo "���, ������ � ���� ������, ������ ������ � ������� �����������)) ������ ��� ��������� �� ��������� ��...
				<meta http-equiv='refresh' content='5; url=/?act=".$in['act']."$link'>";
			}
  		}
	}
}

function pluginblack() {
	global $in,$showall;
	if($showall < 1) {
		echo "� ��� ��� ���� �� ��� ��������.
		<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
	} else {
		echo "<H3>���� ��� ������� Black List</H3>
	    <div align=center style='color: red; border: 1px solid black; padding: 10px; margin-top: 5px;'>
	        ������ ������� ��� ������� ����������� ��� � ��� <br>
	        ���� ���������� � ������� `�������`, ��� � `��� ���` �������� ����� ���� ���, ��� ��� ������������ �� ������� (= <br>
	        ��������� ������ Balck List �� TERMINATOR�, ���� => ������ �� URL <br>
	        <a target=_blank href='http://tzpolice.ru/blacklist/logins.txt'>����� ������ ���������</a> | <a target=_blank href='http://tzpolice.ru/blacklist/clans.txt'>����� ������ �����</a> | <a target=_blank  href='http://tzpolice.ru/blacklist/all.txt'>����� ��� ������</a>
        </div>
        <hr>
        <center><a href='http://tzpolice.ru/_cron/black_plugin.php' target=_blank>�������� ������� ������</a></center>";
	}
}

function leaveblack() {
	global $in,$showall,$sortlist,$typelist,$limitslist;

    if($showall < 3) {
		echo "� ��� ��� ���� �� ��� ��������.
		<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
	} else {

	    $in['sort'] = ($sortlist[0][$in['sort']] || $sortlist[1][$in['sort']])?$in['sort']:'adddate';
	    $type = ($typelist[$in['type']])?$in['type']:'login';
        $cols = 4;
	    if($showall == 1) {
	    	$cols = 6;
	    } elseif($showall > 1) {
	    	$cols = 7;
	    }

		if($in['type'] == 'clan') {
	    	$selectedsortlist = $sortlist[1];
	    	$whofield = '����';
	    	$header = "�����";
	    } else {
	        $selectedsortlist = $sortlist[0];
	    	$whofield = '�����';
	    	$header = "���������";
	    }

		$selectbuttons = "<select name='sort' class='s' onchange='document.forms.selecter.submit();'>";
		foreach($selectedsortlist as $k => $v) {
			$sel = ($in['sort'] == $k)?" SELECTED":"";
			$selectbuttons .= "<option value='$k'$sel>$v</option>";
		}
		$selectbuttons .= "</select>
	    <select name='limit' class='s' onchange='document.forms.selecter.submit();'>
		";
		foreach($limitslist as $k => $v) {
			$sel = ($in['limit'] == $v)?" SELECTED":"";
			$selectbuttons .= "<option value='$v'$sel>$v �� ��������</option>";
		}
		$selectbuttons .= "</select>
		<select name='type' class='s' onchange='document.forms.selecter.submit();'>
		";
		foreach($typelist as $k => $v) {
			$sel = ($in['type'] == $k)?" SELECTED":"";
			$selectbuttons .= "<option value='$k'$sel>$v</option>";
		}
		$selectbuttons .= "</select>";


	    $where[] = "type = '".$type."'";
		$where[] = "status = 0";
		$where = implode(' AND ', $where);

		$limit = (ceil($in['limit']) > 0)?ceil($in['limit']):25;
		$page = ($in['page'] > 0)?ceil($in['page']):1;
		$offset = ($page-1)*$limit;

		$query = mysql_query("SELECT * FROM deorg_black WHERE $where");
		$findall = mysql_num_rows($query);

	    $pageslist = generatePages($findall,$limit,$page);

		$output = "
			<H3>�������� ������: $header</H3>
			<div align=right>
				<form name='selecter' id='selecter' method=GET>
					<input type='hidden' name='act' value='".$in['act']."'>
					<input type='hidden' name='do' value='leave'>
					<input type='hidden' name='page' value='".$in['page']."'>
					$selectbuttons
				</form>
			</div>

	        <div align=left style='padding: 5px;'>��������: $pageslist</div>

			<table style='width: 100%; font-size: 12px;' border=1 cellpadding=0 cellspacing=0>
			<tr>
			<th>#</th>
			<th>$whofield</th>
			<th>�������<br />����</th>
			<th>�����������</th>
			<th>����� � ����</th>
			<th>������ � ����</th>
			<th>x</th>
			";

		$output .= "</tr>
		";

		$sql = "SELECT * FROM deorg_black WHERE $where LIMIT $offset,$limit";
		$query = mysql_query($sql);
		#echo "$sql<hr />";
		$count = mysql_num_rows($query);
		if($count > 0) {
			$i = 1;
			while ($black = mysql_fetch_array($query)) {
				$black['description'] = preg_replace("#\n#","<br />",$black['description']);
				$black['hidedescr'] = preg_replace("#\n#","<br />",$black['hidedescr']);
				$logs = ($black['logs'])?generateLogs($black['logs']):"";
				$descr = ($black['logs'] && $black['description'])?$black['description']."<br>".$logs:$black['description'].$logs;

				$output .= "
					<tr>
						<th>$i</th>
		                <td style='padding-left: 15px;' nowrap>".generateUser($black,$type)."</td>
		                <td align=center>$descr</td>
		                <td align=center>".$black['hidedescr']."</td>
		                <th>".date('d.m.Y H:i:s',$black['addtime'])."<br />�������: <b>".$black['author']."</b></th>
		                <th>".date('d.m.Y H:i:s',$black['deltime'])."<br />������: <b>".$black['whodel']."</b></th>
						<th nowrap>
						<a href='?act=".$in['act']."&do=del&id=".$black['id']."&absolutely=1&pre=leave' onclick='javascript:if(!confirm(\"�������? ��� �������� ��������� ��������� ������.\")) return false;'>x</a>
						|
						<a href='?act=".$in['act']."&do=del&id=".$black['id']."&return=1&pre=leave' onclick='javascript:if(!confirm(\"�������? ��� �������� ����� ������ � ������ ��.\")) return false;'>v</a>
						</th>
					</tr>";
			$i++;
			}
		} else {
			$output .= "
			<tr>
			<th colspan=$cols>� ��� ��� ��� �� ������ �������� ������((</th>
			</tr>
			";
		}

	$output .= "</table>
	<div align=left style='padding: 5px;' >��������: $pageslist</div>";

	echo $output;
    }


}

function accessblack() {
	global $in,$showall,$sortlist,$typelist,$limitslist;

    if($showall < 3) {
		echo "� ��� ��� ���� �� ��� ��������.
		<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
	} else {
		mysql_query("DELETE FROM deorg_access WHERE expire > 0 AND expire < '".time()."'");

		echo "<H3>������� � ׸����� ������</H3>
		<small>
		�������:<br>
		0 - ������������, ��� �������<br>
		1 - �������� �������������� �����<br>
		2 - ����������� ��������� � �������(�� ���������) �������<br>
		3 - ������� ��������, �������� ������� �������, ������ �������� ������� �� ����<br>
		</small>
		<br>
       	<form name='selecter' id='selecter' method=POST onsubmit='if(!checkFields()) return false;'>
		<input name='act' type='hidden' value='".$in['act']."'>
		<input name='do' type='hidden' value='aadd'>
		<table style='width: 100%; font-size: 12px;' border=1 cellpadding=0 cellspacing=0>
		<tr>
		    <th nowrap>
		    	����� ��� ���� <br>
		    	<input name='name' type='text' style='width: 150px;' value='' class=s>
		    	<select name='type' class=s>
		    		<option value='login'>��� �����</option>
		    		<option value='clan'>��� ����</option>
		    	</select>
		    </th>
		    <th>
		    	�������� ����� (� ����, 0=�����) <br> <input name='expire' type='text' value='0'>
		    </th>
		    <th>
		    	������� <br><input name='level' type='text' value='1'>

		    </th>
		    <th>
		    	���������� <br> <input name='des�r' type='text' value='���������'>
		    </th>
		    <th style='vertical-align: middle'>
		    	<input type='submit' value='add'>
		    </th>
		</tr>
		</table>
		</form>
		<table style='width: 100%; font-size: 12px;' border=1 cellpadding=0 cellspacing=0>
			<tr>
			<th>#</th>
			<th>��� ��� ����</th>
			<th>������� �������</th>
			<th>����������</th>
			<th>��������</th>
			<th>������� � ����</th>
			<th>x</th>
			</tr>
		";
        $sql = "SELECT * FROM deorg_access WHERE module='black' ORDER BY type";
		$query = mysql_query($sql);
		$count = mysql_num_rows($query);
		if($count > 0) {
			$i=1;
        	while ($access = mysql_fetch_array($query)) {
        		if($access['type'] == 'clan') {
        			$login = generateUser(Array('clan'=>$access['name']),'clan');
        		} else {
        			$login = "<b>".$access['name']."<b>";
        		}
        		$expire = ($access['expire'] > 0)?date('d.m.Y H:i:s',$access['expire']):"�������";
        		$del = (AuthUserName != $access['name'])?"<a href='?act=".$in['act']."&do=adel&id=".$access['id']."' onclick='javascript:if(!confirm(\"�������? ��� �������� ��������� ��������� ������.\")) return false;'>x</a>":"0_�";

        		echo "
		        	<tr>
						<th>$i</th>
						<td style='padding-left: 15px;'>$login</td>
						<th>".$access['level']."</th>
						<td>".$access['des�ription']."</td>
						<th>$expire</th>
						<th>".$access['author']."<br />".date('d.m.Y H:i:s',$access['addtime'])."</th>
						<th>
							$del
						</th>
					</tr>
				";
            $i++;
        	}

		} else {
        	echo "<tr><td colspan=7>� ���� ������, ���� ��� ���� �� �����(</td></tr>";

		}

        echo "</table>";

	}

}

function aaddblack() {
	global $in,$showall;
    if($showall < 3) {
		echo "� ��� ��� ���� �� ��� ��������.
		<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
	} else {
		if(!$in['name'] || !$in['type']) {
			echo "� ��� �����? �.�.�.
			<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=access'>";
		} else {
        	$check = mysql_query("SELECT * FROM deorg_access WHERE name='".$in['name']."' AND type='".$in['type']."' AND module='black'");
            if(mysql_num_rows($check) > 0) {
            	echo "��� ������ ��� ���� � ���� ������, ������ ��� ��������� ������� �� ���������...
				<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=access'>";
            } else {
                if($in['expire'] > 0) {
                	$in['expire'] = time()+(ceil($in['expire'])*86400);
                }
	        	$query = "INSERT INTO `deorg_access`
	        	(`id`,`name`,`type`,`level`,`module`,`des�ription`,`addtime`,`author`,`expire`)
				VALUES (NULL,'".$in['name']."',  '".$in['type']."', '".ceil($in['level'])."',  'black',	'".$in['des�r']."',  '".time()."',  '".AuthUserName."',  '".$in['expire']."')
				";
				$add = mysql_query($query);
				if(!mysql_error($add)) {
					echo "�� ������ ������, ������ ��� ��������� ������� �� ���������...".$in['des�r']."
					<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=access'>";
				} else {
					echo "���, ������ � ���� ������, ������ ������ � ������� �����������)) ������ ��� ��������� ������� �� ���������...
					<meta http-equiv='refresh' content='5; url=/?act=".$in['act']."&do=access'>";
				}
           	}
		}

	}

}

function adeleteblack() {
	global $in,$showall;
    if($showall < 3) {
		echo "� ��� ��� ���� �� ��� ��������.
		<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
	} else {
		if(ceil($in['id']) < 1) {
			echo "� ��� ID? �.�.�.
			<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=access'>";
		} else {
        	$query = "DELETE FROM `deorg_access` WHERE id='".ceil($in['id'])."' AND module='black'";
			$delete = mysql_query($query);
			if(!mysql_error($delete)) {
				echo "�� ������ ������, ������ ��� ��������� ������� �� ���������...
				<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=access'>";
			} else {
				echo "���, ������ � ���� ������, ������ ������ � ������� �����������)) ������ ��� ��������� ������� �� ���������...
				<meta http-equiv='refresh' content='5; url=/?act=".$in['act']."&do=access'>";
			}
		}

	}

}

function editblack() {
	global $in,$showall;
	$id = ceil($in['id']);
	if($showall < 2) {
		echo "� ��� ��� ���� �� ��� ��������.
		<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
	} else {
		if($id < 1) {
			echo "� ��� ID? �.�.�.
			<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
		} else {
			echo "<H3>�������������� ������ ��� �����</H3>";
         	$query = mysql_query("SELECT * FROM deorg_black WHERE id='$id'");
            if(mysql_num_rows($query) > 0) {
	         	if($in['process'] != 'save') {
	         		$userdata = mysql_fetch_array($query);
                    $asel = ($userdata['align'] == 'MP')?' SELECTED':'';
                    $tsel1 = ($userdata['type'] == 'clan')?' SELECTED':'';
					$tseltext = "";

						if ($userdata['type'] == "login") {
							$tseltext = "<input name='type' type='hidden' value='login'>";
						} else {
							$tsel1 = ($userdata['type'] == 'marclan')?' SELECTED':'';
							$tsel2 = ($userdata['type'] == 'bdclan')?' SELECTED':'';
							$tseltext = "<select name='type' class=s>
			    							<option value='clan'>����</option>
											<option value='marclan'$tsel1>���-����</option>
			    							<option value='bdclan'$tsel2>������ ��������</option>
			    						</select>";
						}


					$name = generateUser($userdata,$userdata['type']);
		         	
					echo "

					<form name='selecter' id='selecter' method=POST>
					<input name='act' type='hidden' value='".$in['act']."'>
					<input name='do' type='hidden' value='edit'>
					<input name='process' type='hidden' value='save'>
					<table style='width: 100%; font-size: 12px;' border=1 cellpadding=5 cellspacing=0>
					<tr>
						<td style='padding-left: 15px;'>����� ��� ����:</td>
					    <td nowrap>
                         $name &nbsp;&nbsp;&nbsp; $tseltext
					    </td>
					</tr>
					<tr>
						<td style='padding-left: 15px;'>���� ���(����� �������):</td>
					    <td><textarea name='logs' style='width: 100%; height: 100px;'>".$userdata['logs']."</textarea></td>
					</tr>
					<tr>
						<td style='padding-left: 15px;'>�������:</td>
					    <td><textarea name='description' style='width: 100%; height: 100px;'>".$userdata['description']."</textarea></td>
					</tr>
					<tr>
						<td style='padding-left: 15px;'>�������������(����� ������ �������):</td>
					    <td><textarea name='hidedescr' style='width: 100%; height: 100px;'>".$userdata['hidedescr']."</textarea></td>
					</tr>
					<tr>
						<td style='padding-left: 15px;'>����� ������:</td>
					    <td><input name='contribution' type='text' value='".$userdata['contribution']."' class=s></td>
					</tr>
					<tr>
						<td style='padding-left: 15px;'>��� ��:</td>
					    <td>
					    	<select name='align' class=s>
					    		<option value='police'>�� �������</option>
					    		<option value='MP'$asel>����! :crazy:</option>
					    	</select>
					    </td>
					</tr>
					<tr>
						<td align=center colspan=2><input type='submit' value='��������� ���������' class=s></td>
					</tr>

		            </table>

					</form>

		         	";
	         	} else {
	         		$query = "UPDATE deorg_black SET `logs`='".$in['logs']."',`description`='".$in['description']."',`hidedescr`='".$in['hidedescr']."',`type`='".$in['type']."',`contribution`='".$in['contribution'].("',`type`='".$in['type'])."' WHERE id='$id'";
                    $update = mysql_query($query);
					if(!mysql_error($update)) {
						echo "�� ������ ������, ������ ��� ��������� ������� �� ���������...
						<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
					} else {
						echo "���, ������ � ���� ������, ������ ������ � ������� �����������)) ������ ��� ��������� ������� �� ���������...
						<meta http-equiv='refresh' content='5; url=/?act=".$in['act']."'>";
					}
	         	}
         	} else {
            	echo "
            	�����! ������ � ����� ID �� ����������, ��������, ���-�� ����� �������...
            	<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
         	}
		}

	}


}

switch($in['do']) {
	default:
	blacklist();
	break;

	case 'add':
	addblack();
	break;

	case 'del':
	deleteblack();
	break;

	case 'aadd':
	aaddblack();
	break;

	case 'adel':
	adeleteblack();
	break;

	case 'edit':
	editblack();
	break;

	case 'leave':
	leaveblack();
	break;

	case 'plugin':
	pluginblack();
	break;

	case 'access':
	accessblack();
	break;
}

?>