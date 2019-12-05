<?

#всякая шняга
$profs = array("", "Корсар", "Сталкер", "Старатель", "Инженер", "Наемник", "Торговец",
"Полицейский.Патрульный.","Полицейский.Штурмовик.","Полицейский.Специалист.",
"Журналист","Чиновник","Псионик","Каторжник","Пси-Кинет","Пси-медиум","Пси-Лидер","Полиморф","","","","","","","","",
"Грузовой робот","Десантный робот","Боевой робот","", "Дилер");

$rank_names = array("Рядовой","Рядовой","Младший сержант","Сержант","Старший сержант",
"Младший лейтенант","Лейтенант","Старший лейтенант",
"Капитан","Майор","Подполковник","Полковник","Генерал-майор","Генерал-лейтенант","Генерал-полковник",
"Маршал","Командор","Нету такого","ц.ц.ц");

$pverank_names = array("Странник","Странник","Крысолов","Следопыт","Охотник",
"Зверобой","Потрошитель","Истребитель","Гроза мутантов","Вивисектор","Легенда пустошей","ц.ц.ц");


#доступ
$query = mysql_query("SELECT *,MAX(level) as access FROM deorg_access WHERE (module='pda' AND name='".AuthUserName."' AND `type`='login') OR (name='".AuthUserClan."' AND type='clan' AND module='pda')");
if(mysql_num_rows($query)>0){
	$access = mysql_fetch_array($query);

	$query = mysql_query("SELECT distinct(`group`) FROM deorg_pda_users WHERE `group` != 'главная'");
	while($g = mysql_fetch_array($query)) {
		$groups[] = $g['group'];
	}


	if (($access['access'] > 0 && ($access['expire'] == 0 || $access['expire'] > time))) {
		switch($in['do']) {
			default:
			pdamainfunc();
			break;

			case 'add':
			addpdalist();
			break;

			case 'aedit':
			editpdalist();
			break;

			case 'access':
			accesspda();
			break;

			case 'adda':
			addaccesspda();
			break;

			case 'dela':
			delaccesspda();
			break;


		}
	} else {
		echo "<H3>Нет прав 0_о</H3>";
	}

} else {
	echo "<H3>Shoo, shoo! Go away $ulogin!</H3>";
}

function getGroups($group) {
	global $access,$in,$groups;

	$groupslist = "<option value='главная'>главная</option>";
	foreach($groups as $k => $g) {
		$sel = ($group == $g)?' SELECTED':'';
		$groupslist .= "<option value='$g'$sel>$g</option>";
	}
	return $groupslist;
}

function addpdalist() {
	global $access,$in,$groups;
	if($access['access'] < 2) {
		echo "<b style='color:red'>А прав то нету ;(</b>";
	} else {
		echo "
		<H3>Добавить наблюдение за персом</H3>
		<hr>
			<div align=right>".modulemenu()."</div>
		";
    	if(!$in['process']) {
     		echo "
			<script>
				function checkFields() {
					var form = document.forms.selecter;
					if(!form.elements.name.value) {
						alert('Логин мне самому придумать, или Вы всё-таки укажете?)');
						return false;
					}
                    return true;
				}
			</script>
			<form name='selecter' id='selecter' method=POST onsubmit='if(!checkFields()) return false;'>
			<input name='act' type='hidden' value='".$in['act']."'>
			<input name='do' type='hidden' value='add'>
			<input name='process' type='hidden' value='check'>
			<table style='width: 100%; font-size: 12px;' border=0 cellpadding=5 cellspacing=0>
			<tr>
				<td style='padding-left: 15px;'>Логин:</td>
			    <td nowrap>
			    	<input name='name' type='text' style='width: 250px;' value='".$in['name']."' class=s>
			    	<select name='group' class='s'>
						".getGroups($in['group'])."
			    	</select>
			    	<input type='submit' value='Продолжить' class=s>
			    </td>
			</tr>
            </table>

			</form>
     	";
        } elseif($in['process'] == 'check') {
			if(!$in['name']) {
        		$error = "Ник где-то потерялся, надо вернуться назад и ввести заново((";
        	} else {
        		$query = mysql_query("SELECT * FROM deorg_pda_users WHERE login='".$in['name']."'");
        		if(mysql_num_rows($query) < 1) {
        			$userinfo = locateUser($in['name']);
        			if($userinfo['level'] < 2) {
        				$error = "Ник некорректен. Попробуйте ещё раз";
        			}
        		} else {
        			$error = "Такой персонаж уже есть в списке";
        		}
			}
         	echo "
            <form name='selecter' id='selecter' method=POST>
			<input name='act' type='hidden' value='".$in['act']."'>
			<input name='do' type='hidden' value='add'>
            <input name='name' type='hidden' value='".$userinfo['login']."'>
            <input name='group' type='hidden' value='".$in['group']."'>
        	";
         	if($error) {
         		echo "<span style='color: red;'><b>Обнаружены ошибки</b>: ".$error."</span><br>";
         		echo "<input type='submit' value='Назад'>";
         	} else {
         		echo "Начать наблюдение за ".generateUser($userinfo)."?<hr>";
         		echo "<input name='process' type='hidden' value='ok'>
         		<input type='submit' value='Продолжить'>";
         	}
        } elseif($in['process'] == 'ok') {
        	if(!$in['name'] || !$in['group']) {
        		echo "Ник где-то потерялся, надо вернуться назад и ввести всё заново((";
        	} else {
        		$query = mysql_query("SELECT * FROM deorg_pda_users WHERE login='".$in['name']."'");
	        	if(mysql_num_rows($query) > 0) {
	        		echo "Такой ник уже есть в базе.
					<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
	        	} else {
					$query = "INSERT INTO  `deorg_pda_users` (`login`,`group`,`addtime`,`added`)
					VALUES('".$in['name']."','".$in['group']."','".time()."','".AuthUserName."')";
	            	$insert = mysql_query($query);

					if(!mysql_error($insert)) {
						echo "Всё прошло удачно, сейчас Вас перекинет на главную...
						<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
					} else {
						echo "Упс, ошибка в базе данных, срочно ловите и пинайте программера)) Сейчас Вас перекинет на главную...
						<meta http-equiv='refresh' content='5; url=/?act=".$in['act']."'>";
					}
				}
        	}

        }


	}

}
function editpdalist() {
	global $access,$in;
	if($access['access'] < 2) {
		echo "<b style='color:red'>А прав то нету ;(</b>";
	} else {
		echo "
			<H3>Список наблюдаемых</H3>
			<hr>
			<div align=right>".modulemenu()."</div>
		";
		if($in['process'] != 'edit' && $in['process'] != 'delete') {
			echo "
			<script>
			function checkProcess(id) {

				id = 'process'+id;
				var opt = document.getElementById(id).value;

				if(opt == 'delete') {
					if(!confirm('Это действие окончательное, продолжить?')) {
						return false;
					} else {
						return true;
					}
				}
				return true;
			}
			</script>
			<table style='width: 100%; font-size: 12px;' border=1 cellpadding=5 cellspacing=0>
				<tr>
					<th>Логин</th>
				    <th>Группа</th>
				    <th>Автор</th>
				    <th>Дата</th>
				    <th>Опции</th>
				</tr>
			";

            $query = mysql_query("SELECT * FROM deorg_pda_users");
			while($u = mysql_fetch_array($query)) {
				$u = mysql_clear_array($u);
				$groups = getGroups($u['group']);
				echo "
				<form method=POST>
				<tr>
					<td style='align-left: 10px;'>
						".generateUser(locateUser($u['login']),1)."
						<input name='id' type='hidden' value='".$u['id']."'>
					</td>
				    <th>
	                 	<select name='group' class=s>
	                 		$groups
	                 	</select>
				    </th>
				    <th>".$u['added']."</th>
				    <th>".date('d.m.Y H:i:s',$u['addtime'])."</th>
				    <th>
				    	<select name='process' id='process".$u['id']."' class=s>
				    		<option value='edit'>сохранить</option>
                            <option value='delete'>удалить</option>
	                   	</select>
	                   	<input type='submit' value='>>' onclick='if(!checkProcess(".$u['id'].")) return false;' class=s>
				    </th>
				</tr>
				</form>
				";
			}
			echo "</table>";
		} elseif($in['process'] == 'edit') {
			$id = ceil($in['id']);
        	if($id < 1 || !$in['group']) {
        		echo "Хватит химичить! :mad:
        		<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=".$in['do']."'>";
        	} else {
        		if(mysql_num_rows(mysql_query("SELECT * FROM deorg_pda_users WHERE id = '$id'")) == 1) {
        			$insert = mysql_query("UPDATE deorg_pda_users SET group='".$in['group']."' WHERE id='$id'");
        			if(!mysql_error($insert)) {
						echo "Всё прошло удачно, сейчас Вас перекинет назад...
						<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=".$in['do']."'>";
					} else {
						echo "Упс, ошибка в базе данных, срочно ловите и пинайте программера)) Сейчас Вас перекинет назад...
						<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=".$in['do']."'>";
					}

        		} else {
        			echo "Хватит химичить говорю! :mad:
        			<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=".$in['do']."'>";
        		}

        	}

		} elseif($in['process'] == 'delete') {
			$id = ceil($in['id']);
        	if($id < 1 || !$in['group']) {
        		echo "Хватит химичить! :mad:
        		<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=".$in['do']."'>";
        	} else {
        		if(mysql_num_rows(mysql_query("SELECT * FROM deorg_pda_users WHERE id = '$id'")) == 1) {
        			$delete = mysql_query("DELETE FROM deorg_pda_users WHERE id='$id'");
        			if(!mysql_error($delete)) {
						echo "Всё прошло удачно, сейчас Вас перекинет назад...
						<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=".$in['do']."'>";
					} else {
						echo "Упс, ошибка в базе данных, срочно ловите и пинайте программера)) Сейчас Вас перекинет назад...
						<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=".$in['do']."'>";
					}
        		} else {
        			echo "Хватит химичить говорю! :mad:
        			<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=".$in['do']."'>";
        		}

        	}

		}

	}

}

function accesspda() {
	global $access,$in;

    if($access['access'] < 3) {
		echo "<b style='color:red'>А прав то нету ;(</b>
		<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
	} else {
        echo "
			<H3>Доступы к сканеру ПДА</H3>
			<hr>
			<div align=right>".modulemenu()."</div>
		";
		if($in['process'] != 'add' && $in['process'] != 'del') {
			mysql_query("DELETE FROM deorg_access WHERE expire > 0 AND expire < '".time()."'");

			echo "<small>
			Доступы:<br>
			0 - По умолчанию доступа нет<br>
			1 - просмотр модуля<br>
			2 - возможность добавлять и удалять ники<br>
			3 - 2 + раздача доступов<br>
			</small>
			<br>
	       	<form name='selecter' id='selecter' method=POST onsubmit='if(!checkFields()) return false;'>
			<input name='act' type='hidden' value='".$in['act']."'>
			<input name='do' type='hidden' value='access'>
			<input name='process' type='hidden' value='add'>
			<table style='width: 100%; font-size: 12px;' border=0 cellpadding=0 cellspacing=0>
			<tr>
			    <th nowrap>
			    	Логин или клан <br>
			    	<input name='name' type='text' style='width: 150px;' value='' class=s>
			    	<select name='type' class=s>
			    		<option value='login'>это логин</option>
			    		<option value='clan'>это клан</option>
			    	</select>
			    </th>
			    <th>
			    	Истекает через (в днях, 0=вечно) <br> <input name='expire' type='text' value='0'>
			    </th>
			    <th>
			    	Уровень <br><input name='level' type='text' value='1'>

			    </th>
			    <th>
			    	Примечание <br> <input name='desсr' type='text' value='сотрудник'>
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
				<th>Ник или клан</th>
				<th>Уровень доступа</th>
				<th>Примечание</th>
				<th>Истекает</th>
				<th>Добавил и дата</th>
				<th>x</th>
				</tr>
			";
	        $sql = "SELECT * FROM deorg_access WHERE module='pda' ORDER BY type";
			$query = mysql_query($sql);
			$count = mysql_num_rows($query);
			if($count > 0) {
				$i=1;
	        	while ($acc = mysql_fetch_array($query)) {
	        		if($acc['type'] == 'clan') {
	        			$login = generateUser(Array('clan'=>$acc['name']),0,'clan');
	        		} else {
	        			$login = "<b>".$acc['name']."<b>";
	        		}
	        		$expire = ($acc['expire'] > 0)?date('d.m.Y H:i:s',$acc['expire']):"Никогда";
	        		$del = (AuthUserName != $acc['name'])?"<a href='?act=".$in['act']."&do=access&process=del&id=".$acc['id']."' onclick='javascript:if(!confirm(\"Уверены? Это действие полностью уничтожит доступ.\")) return false;'>x</a>":"0_о";

	        		echo "
			        	<tr>
							<th>$i</th>
							<td style='padding-left: 15px;'>$login</td>
							<th>".$acc['level']."</th>
							<td>".$acc['desсription']."</td>
							<th>$expire</th>
							<th>".$acc['author']."<br />".date('d.m.Y H:i:s',$acc['addtime'])."</th>
							<th>
								$del
							</th>
						</tr>
					";
	            $i++;
	        	}

			} else {
	        	echo "<tr><td colspan=7>А нету никого, хотя так быть не может(</td></tr>";

			}

        	echo "</table>";
        } elseif($in['process'] == 'add') {

            if(!$in['name'] || !$in['type']) {
				echo "А где логин? ц.ц.ц.
				<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=access'>";
			} else {

	        	$check = mysql_query("SELECT * FROM deorg_access WHERE name='".$in['name']."' AND type='".$in['type']."' AND module='pda'");
	            if(mysql_num_rows($check) > 0) {
	            	echo "Эта запись уже есть в базе данных, сейчас Вас перекинет обратно на страничку...
					<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=access'>";
	            } else {
	                if($in['expire'] > 0) {
	                	$in['expire'] = time()+(ceil($in['expire'])*86400);
	                }
		        	$query = "INSERT INTO `deorg_access`
		        	(`id`,`name`,`type`,`level`,`module`,`desсription`,`addtime`,`author`,`expire`)
					VALUES (NULL,'".$in['name']."',  '".$in['type']."', '".ceil($in['level'])."',  'pda',	'".$in['desсr']."',  '".time()."',  '".AuthUserName."',  '".$in['expire']."')
					";
					$add = mysql_query($query);
					if(!mysql_error($add)) {
						echo "Всё прошло удачно, сейчас Вас перекинет обратно на страничку...
						<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=access'>";
					} else {
						echo "Упс, ошибка в базе данных, срочно ловите и пинайте программера)) Сейчас Вас перекинет обратно на страничку...
						<meta http-equiv='refresh' content='5; url=/?act=".$in['act']."&do=access'>";
					}
	           	}
	         }

        } elseif($in['process'] == 'del') {
            if(ceil($in['id']) < 1) {
				echo "А где ID? ц.ц.ц.
				<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=access'>";
			} else {
	        	$query = "DELETE FROM `deorg_access` WHERE id='".ceil($in['id'])."' AND module='pda'";
				$delete = mysql_query($query);
				if(!mysql_error($delete)) {
					echo "Всё прошло удачно, сейчас Вас перекинет обратно на страничку...
					<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."&do=access'>";
				} else {
					echo "Упс, ошибка в базе данных, срочно ловите и пинайте программера)) Сейчас Вас перекинет обратно на страничку...
					<meta http-equiv='refresh' content='5; url=/?act=".$in['act']."&do=access'>";
				}
			}

        }

	}

}
function addaccesspda() {
	global $in,$access;
 	if($access['access'] < 3) {
		echo "У Вас нет прав на это действие.
		<meta http-equiv='refresh' content='3; url=/?act=".$in['act']."'>";
	} else {


	}

}

function generateUser($data,$link = 0, $type = 0) {
	global $profs,$rank_names,$in;
	$data['lvl'] = ($data['lvl'])?$data['lvl']:$data['level'];
	$data['level'] = ($data['level'])?$data['level']:$data['lvl'];
	$data['rank'] = ($data['rank'])?$data['rank']:$data['pvprank'];
	if($link > 0) {		$link = "?act=".$in['act']."&user=".$data['login'];		foreach($in as $k=>$v) {			if($k =='act' || $k=='user') continue;
			$link .= "&$k=$v";		}
		$login = "<a href='$link'>".$data['login']."</a>";
	} else {
		$login = $data['login'];
	}
	if($type < 1) {
		$output = ($data['clan'])?"<img src='http://timezero.ru/i/clans/".$data['clan'].".gif' border=0  class='inlineimg' alt='".$data['clan']."' title='".$data['clan']."'>":"";
		$output .= "<b>$login</b> [".$data['level']."]";
		$output .= "<a href='http://www.timezero.ru/info.pl?".$data['login']."' border=0  target=_blank><img border=0  src='http://timezero.ru/i/i".$data['pro'].".gif' class='inlineimg' alt='".$profs[$data['pro']]."' title='".$profs[$data['pro']]."'></a>";
		$output .= "<img src='http://timezero.ru/i/rank/".$data['rank'].".gif' border=0  class='inlineimg' alt='".$rank_names[$data['rank']]."' title='".$rank_names[$data['rank']]."'>";
	} else {
    	$output = "<img src='http://timezero.ru/i/clans/".$data['clan'].".gif' border=0  class='inlineimg' alt='".$data['clan']."' title='".$data['clan']."'><b>".$data['clan']."</b>";
    }
	return $output;
}

function modulemenu() {
	global $access,$in;

	$data = "<a href='?act=pda'>статистика</a>";
    if($access['access'] > 1) {
    	$data .= ($in['do'] == 'add')?" | <b>добавить</b>":" | <a href='?act=pda&do=add'>добавить</a>";
    	$data .= ($in['do'] == 'aedit')?" | <b>управление списком</b>":" | <a href='?act=pda&do=aedit'>управление списком</a>";
    }
    if($access['access'] > 2) {
    	$data .= ($in['do'] == 'access')?" | <b>доступы</b>":" | <a href='?act=pda&do=access'>доступы</a>";
    }
    if($access['access'] > 1) {
    	$data .= ($in['process'] == 'edit')?" | <b>редактирование</b>":"";
    }
    if($access['access'] > 1) {
    	$data .= ($in['process'] == 'delete')?" | <b>удаление</b>":"";
    }
    $data .= "<br><br>";
	return $data;

}

function mysql_clear_array($query) {
	foreach($query as $k => $v) {
		if(is_numeric($k)) continue;
		$tmp[$k] = $v;
	}
	return $tmp;
}
function getNormalTime($w) {
    $h=0;
    $m=0;
    $s=0;
    $return = '~ ';
    if(!$w || $w < 1) {
    	return '-';
    }
    if($w < 60) {
    	return 'меньше минуты';
    }

    #31536000
	#echo "t: ".$w."<br>";
	if($w >= 31536000) {
		$y = floor($w/31536000);
		$st = strlen($y)-1;
		$et = strlen($y);
		$yf = '';
		if($y == 1) $yf = 'год';
		if($y > 1 && $y <= 4) $yf = 'года';
		if($y > 4 && $y <= 20) $yf = 'лет';
		if($y > 20 && substr($y,$st,$et) == 1) $yf = 'год';
		if($y > 20 && substr($y,$st,$et) > 1 && substr($y,$st,$et) <=4) $yf = 'года';
		if($y > 20 && substr($y,$st,$et) > 4) $yf = 'лет';
        if($y > 20 && substr($y,$st,$et) == 0) $yf = 'лет';

    	$return .= $y.' '.$yf.' ';
	}
	$w = ceil($w)-floor($y*31536000);


	if($w >= 2592000) {
		$mh = floor($w/2592000);
		$st = strlen($mh)-1;
		$et = strlen($mh);
		$mhf = '';
		if($mh == 1) $mhf = 'месяц';
		if($mh > 1 && $mh <= 4) $mhf = 'месяца';
		if($mh > 4 && $mh <= 20) $mhf = 'месяцев';
		if($mh > 20 && substr($mh,$st,$et) == 1) $mhf = 'месяц';
		if($mh > 20 && substr($mh,$st,$et) > 1 && substr($mh,$st,$et) <=4) $mhf = 'месяца';
		if($mh > 20 && substr($mh,$st,$et) > 4) $mhf = 'месяцев';
        if($mh > 20 && substr($mh,$st,$et) == 0) $mhf = 'месяцев';

    	$return .= $mh.' '.$mhf.' ';
	}
	$w = ceil($w)-floor($mh*2592000);

	if($w >= 86400) {
		$d = floor($w/86400);
		$st = strlen($d)-1;
		$et = strlen($d);
		$df = '';
		if($d ==1) $df = 'день';
		if($d > 1 && $d <= 4) $df = 'дня';
		if($d > 4 && $d <= 20) $df = 'дней';
		if($d > 20 && substr($d,$st,$et) == 1) $df = 'день';
		if($d > 20 && substr($d,$st,$et) > 1 && substr($d,$st,$et) <=4) $df = 'дня';
		if($d > 20 && substr($d,$st,$et) > 4) $df = 'дней';
        if($d > 20 && substr($d,$st,$et) == 0) $df = 'дней';
    	$return .= $d.' '.$df.' ';
	}
	$w = ceil($w)-floor($d*86400);
	if($w >= 3600) {
		$h = floor($w/3600);
		$st = strlen($h)-1;
		$et = strlen($h);
		$hf = '';
		if($h > 1 && $h <= 4) $hf = 'а';
		if($h > 4 && $h <= 20) $hf = 'ов';
		if($h > 20 && substr($h,$st,$et) > 1 && substr($h,$st,$et) <=4) $hf = 'а';
		if($h > 20 && substr($h,$st,$et) > 4) $hf = 'ов';
        if($h > 20 && substr($h,$st,$et) == 0) $hf = 'ов';
    	$return .= $h.' час'.$hf.' ';
	}
	$w = ceil($w)-floor($h*3600);
	#echo "t: $w | h: $h<br>";
	if($w >= 60) {
		$m = floor($w/60);
		$mf = '';
		if($m == 1 || (substr($m,1,1) == 1 && $m != 11)) $mf = 'а';
		if($m > 1 && $m <= 4) $mf = 'ы';
		if($m > 20 && substr($m,1,1) > 1 && substr($m,1,1) <= 4) $mf = 'ы';
    	$return .= $m.' минут'.$mf.' ';
	}
	return $return;
}



function pdamainfunc() {
	global $access,$in;

	#print_r($in);

	$query = "SELECT MIN(instime) AS start ,MAX(lastupdate) AS end FROM deorg_pda_users_stat";
	$query = mysql_query($query);
	$result = mysql_fetch_array($query);
	$starty = date('Y',$result['start']);
    $endy = date('Y',$result['end']);
    if($starty < 2000) $starty = date('Y');
    if($endy < date('Y')) $endy = date('Y');


    $monthslist = Array('','Января','Февраля','Марта','Апреля','Мая','Июня','Июля','Августа','Сентября','Октября','Ноября','Декабря');

	for($i=1;$i<=31;$i++) {
		if(isset($in['aday'])) {
			$sel = ($in['aday'] == $i)?" SELECTED":"";
		} else {
			if(date('d') == $i) {
				$sel = " SELECTED";
				$in['aday'] = $i;
			} else {
				$sel = "";
			}
		}
		$d = ($i < 10)?"0".$i:$i;
		$adays .= "<option value='$i'$sel>$d</option>
		";
	}
	for($i=1;$i<=12;$i++) {
		if(isset($in['amonth'])) {
			$sel = ($in['amonth'] == $i)?" SELECTED":"";
		} else {
			if(date('m') == $i) {
				$sel = " SELECTED";
				$in['amonth'] = $i;
			} else {
				$sel = "";
			}
		}
		$m = $monthslist[$i];
		$amonths .= "<option value='$i'$sel>$m</option>
		";
	}
    for($i=$starty;$i<=$endy;$i++) {
    	if(isset($in['ayear'])) {
			$sel = ($in['ayear'] == $i)?" SELECTED":"";
		} else {
			if(date('Y') == $i) {
				$sel = " SELECTED";
				$in['ayear'] = $i;
			} else {
				$sel = "";
			}
		}
		$ayears .= "<option value='$i'$sel>$i</option>
		";
	}

	for($i=1;$i<=31;$i++) {
		if(isset($in['pday'])) {
			$sel = ($in['pday'] == $i)?" SELECTED":"";
		} else {
			if(date('d') == $i) {
				$sel = " SELECTED";
				$in['pday'] = $i;
			} else {
				$sel = "";
			}
		}
		$d = ($i < 10)?"0".$i:$i;
		$pdays .= "<option value='$i'$sel>$d</option>
		";
	}
	for($i=1;$i<=12;$i++) {
		if(isset($in['pmonth'])) {
			$sel = ($in['pmonth'] == $i)?" SELECTED":"";
		} else {
			if(date('m') == $i) {
				$sel = " SELECTED";
				$in['pmonth'] = $i;
			} else {
				$sel = "";
			}
		}
		$m = $monthslist[$i];
		$pmonths .= "<option value='$i'$sel>$m</option>
		";
	}
    for($i=$starty;$i<=$endy;$i++) {
    	if(isset($in['pyear'])) {
			$sel = ($in['pyear'] == $i)?" SELECTED":"";
		} else {
			if(date('Y') == $i) {
				$sel = " SELECTED";
				$in['pyear'] = $i;
			} else {
				$sel = "";
			}
		}
		$pyears .= "<option value='$i'$sel>$i</option>
		";
	}

	echo "
		<H3>Статистика по наблюдаемым персонажам</H3>
		<hr>
		<div align=right>".modulemenu()."</div>
		<form action='' method=GET>
		<input name='act' type='hidden' value='pda'>

        <table style='width: 100%; font-size: 12px;' cellpadding=3 cellspacing=3 border=0>
        <tr>
        	<th>
        		Интервал: от <select name='aday'>
        			$adays
        		</select>
        		<select name='amonth'>
        		    $amonths
        		</select>
        		<select name='ayear'>
        		    $ayears
        		</select> 00:00

				до <select name='pday'>
        			$pdays
        		</select>
        		<select name='pmonth'>
        		    $pmonths
        		</select>
        		<select name='pyear'>
        		    $pyears
        		</select>
        		23:59
        	</th>
        </tr>
        <tr>
        	<th nowrap>
        		Логин:  <input name='user' type='text' value='".$in['user']."' style='width: 400px;'>
        		<input type='submit' value='Фильтр'>
        		<input type='button' value='Сброс' onclick='window.location.href=\"?act=".$in['act']."&do=".$in['do']."\"'>
        	</th>
        </tr>
        </table>


	";
    $in['go'] = 1;
	if($in['go'] == 1) {
		$starttime = mktime(0,0,0,$in['amonth'],$in['aday'],$in['ayear']);
		$endtime = mktime(23,59,59,$in['pmonth'],$in['pday'],$in['pyear']);

		if($starttime > $endtime) {
			echo "<b style='color:red'>Некорректный интервал времени (начальное время больше конечного)</b>";
		} else {
			$allusers = mysql_num_rows(mysql_query("SELECT id FROM deorg_pda_users"));

			$where[] = "b.instime+1 < b.lastupdate";
			$where[] = "b.instime >= '$starttime'";
			$where[] = "b.lastupdate <= '$endtime'";
			if($in['user'] != '') {
				$where[] = "b.login = '".$in['user']."'";
			}
			$where = join(' AND ',$where);

			$query = "SELECT a.id as uid,b.id,b.login,b.status,b.chat,b.city,b.server,b.instime,b.lastupdate FROM deorg_pda_users_stat AS b LEFT JOIN deorg_pda_users as a ON(b.login=a.login) WHERE $where";
			$query = mysql_query($query);
			if(mysql_num_rows($query) > 0) {
                $servers = Array('','TerraPrima','Archipelago');
				if($in['user'] != '') {
					echo "
					<hr>
					<center><i>Поиск по интервалу с <b>".date('d.m.Y H:i',$starttime)."</b> по <b>".date('d.m.Y H:i',$endtime)."</b></i></center><br>
					<table style='width: 100%; font-size: 12px;' cellpadding=5 cellspacing=3 border=0>
	                <tr bgcolor='#E4DDC5'>
	                	<th colspan=6>
	                    	Персонаж ".generateUser(locateUser($in['user']),1)."
	                	</th>
	                </tr>
			        <tr bgcolor='#934900'>
			        	<th colspan=2>Время</th>
			        	<th>Сервер</th>
			        	<th>Чат</th>
			        	<th>Купол</th>
	                </tr>
					";

					$i=1;
					while($u = mysql_fetch_array($query)) {
						$u = mysql_clear_array($u);
						$interval = $u['lastupdate']-$u['instime'];
                        $color = ($i/2 == ceil($i/2))?'F5F5F5':'C0C0C0';

                        $onlinetime += $interval;
                        $citytime += ($u['city'] > 0)?$interval:0;
                        $cityouttime += ($u['city'] > 0)?0:$interval;
                        $chatofftime += ($u['chat'] > 0)?0:$interval;
                        $chatontime += ($u['chat'] > 0)?$interval:0;

                       	$chat = ($u['chat'] > 0)?"<b style='color: green'>ON</b>":"<b style='color: red'>OFF</b>";
                       	$city = ($u['city'] > 0)?"<b style='color: green'>IN</b>":"<b style='color: red'>OUT</b>";

                        $newday = "";
                        if($prevday != date('d.m.Y',$u['lastupdate'])) {
                        	echo "
                        	<tr bgcolor='#C0C0C0'>
                        		<th colspan=6>
					        		".date('d.m.Y',$u['lastupdate'])."
					        	</th>
                        	</tr>
                        	";
                        }
						echo "
						 	<tr bgcolor='#$color'>
						 		<td align=center>
					        		".date('H:i',$u['instime'])." -  ".date('H:i',$u['lastupdate'])."
					        	</td>
					        	<td align=center>
					        		".getNormalTime($interval)."
					        	</td>
					        	<th nowrap>
					        		".$servers[$u['server']]."
					        	</th>
					        	<th nowrap>
					        		$chat
					        	</td>
					        	<th nowrap>
					        		$city
					        	</th>
		                	</tr>
						";
						$i++;
						$prevday = date('d.m.Y',$u['lastupdate']);
					}
					echo "
					<tr bgcolor='#E4DDC5'>
						<th>
	                    	Итого:
	                	</th>
						<th colspan=2>
	                    	".getNormalTime($onlinetime)."
	                	</th>
					    <td style='padding-left: 10px;' nowrap>
							ON: ".getNormalTime($chatontime)."
					    	<hr>
					    	OFF: ".getNormalTime($chatofftime)."
					    </td>
					    <td style='padding-left: 10px;' nowrap>
					    	IN: ".getNormalTime($citytime)."
					    	<hr>
					    	OUT: ".getNormalTime($cityouttime)."
					    </td>
					</tr>
					</table>";

				} else {
	                $users = Array();
					while($u = mysql_fetch_array($query)) {
						$u = mysql_clear_array($u);
						$interval = $u['lastupdate']-$u['instime'];

						$users[$u['login']]['chaton'] += ($u['chat'] > 0)?$interval:0;
						$users[$u['login']]['chatoff'] += ($u['chat'] < 1)?$interval:0;
						$users[$u['login']]['server1'] += ($u['server'] == 1)?$interval:0;
						$users[$u['login']]['server2'] += ($u['server'] == 2)?$interval:0;
						$users[$u['login']]['incity'] += ($u['city'] > 0)?$interval:0;
						$users[$u['login']]['outcity'] += ($u['city'] < 1)?$interval:0;
						$users[$u['login']]['online'] += $interval;

					    $users[$u['login']]['id'] = $user['uid'];
					}
					echo "
					<hr>
					<center><i>Поиск по интервалу с <b>".date('d.m.Y H:i',$starttime)."</b> по <b>".date('d.m.Y H:i',$endtime)."</b></i></center><br>
					<table style='width: 100%; font-size: 12px;' cellpadding=3 cellspacing=3 border=0>
	                <tr bgcolor='#E4DDC5'>
	                	<th colspan=7>
	                    	есть инфа по ".count($users)." из $allusers персонажей
	                	</th>
	                </tr>
			        <tr bgcolor='#934900'>
			        	<th>#</th>
			        	<th>Логин</th>
			        	<th>Онлайн</th>
			        	<th>Местонахождение</th>
			        	<th>Чат</th>
			        	<th>Сервера</th>
	                </tr>
					";
					$i=1;
					foreach($users as $login => $data) {
						$color = ($i/2 == ceil($i/2))?'F5F5F5':'C0C0C0';
					echo "
					 	<tr bgcolor='#$color'>
				        	<th>$i</th>
				        	<td style='padding-left: 10px;' nowrap>".generateUser(locateUser($login),1)."</td>
				        	<td align=center>".getNormalTime($data['online'])."</td>
				        	<td style='padding-left: 10px;' nowrap>
				        		Купол: ".getNormalTime($data['incity'])."
				        		<hr>
				        		Вне купола: ".getNormalTime($data['outcity'])."
				        	</td>
				        	<td style='padding-left: 10px;' nowrap>
				        		ON: ".getNormalTime($data['chaton'])."
				        		<hr>
				        		OFF: ".getNormalTime($data['chatoff'])."
				        	</td>
				        	<td style='padding-left: 10px;' nowrap>
				        		Prima: ".getNormalTime($data['server1'])."
				        		<hr>
				        		Archipelago: ".getNormalTime($data['server2'])."
				        	</td>
	                	</tr>
					";
					$i++;
					}
					echo "</table>";
				}
			} else {
				echo "<b style='color:red'>По Вашему запросу нифига не найдено ;(</b>";

			}
		}


	}



}








?>