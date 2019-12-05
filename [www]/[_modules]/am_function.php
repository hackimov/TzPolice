<?php

require('functions.php');
require('auth.php');

// **************** секьюримся *********************************

$in = sequre_input(array($_GET, $_POST, $_COOKIE));

$onlyfunc = true;

$good = include_once($in['module'].".php");

if ($good) {

	$access	= InitAccessArr();

	unset ($onlyfunc);

	$access_arr = GetAccessArr($in['module'],$access);

	$ad = "Зафиксирована попытка несанкционированного доступа к служебной информации.<br>
			Ваш IP-адрес, автологин, логи чата, все неодобренные администрацией плагины и снимок с веб-камеры отправлены для анализа в лабораторию.<br>
			Удачной игры!<br>
			С Уважением, Полиция TimeZero.";

	if (!$access_arr['admin']) {

		die($ad);

	}

	$id = str_replace("access_record_","",$in['id']);


	if ($id != "new") {

		$SQL = "SELECT `module` FROM `modules_access` WHERE `id` = ".$id;
		$r = mysql_query($SQL);

		if ($row = mysql_fetch_array($r)) {

			if ($row['module'] != $in['module']) {

				die($ad);

			}

		} else {
			
			$w = "Текущая запись id = ".$in['id']." не обнаружена в базе данных. Возможно, она была удалена другим пользователем. Обновите страницу!";

			$w = iconv("WINDOWS-1251" ,"UTF-8", "$w");

			$res['id'] = $id;
			$res['result'] = $w;

			echo json_encode($res);

			exit;
			
		}

	} 

	// ******************************************************

	// ***** блок действий *****

	if ($in['action'] == "geteditstr") {
		
		if ($id == "new") {

			$row = array();
			$row['name'] = "";
			$row['name_type'] = "login";
			$row['admin'] = "0";
			$row['rules'] = "0";
			$fromsql = false;

		} else {

			
			$SQL = "SELECT * FROM `modules_access` WHERE `id` = ".$id;
			$r = mysql_query($SQL);

			if ($row = mysql_fetch_array($r)) {	
				$fromsql = true;
			}
			
		}

		if ($fromsql || $id == "new") {	
			
			$w =	"<td><input type='text' id='name' size='16' value='".$row['name']."'></td>
					 <td align=center><input type='text' id='name_type' size='10' value='".$row['name_type']."'></td>
					 <td align=center><input type='checkbox' id='admin'".(($row['admin'] == 1)?" checked":"")."></td>";
			
			for ($i=0; $i<count($access); $i++)  {
				$rule = (($row['rules'] & (pow(2, $i))) == 0)?false:true;
				$w .= "<td align=center><input type='checkbox' class='rules' id='rule_".$i."'".($rule?" checked":"")."></td>";
			}

			$w .= "<td align=center><img src='../i/am_save.png' border=0 title='Сохранить изменения' height=20 class='savebtn'><img src='../i/am_undo.png' border=0 title='Отменить редактирование' height=20 class='undobtn'></td>";
		
			$w = iconv("WINDOWS-1251" ,"UTF-8", "$w");

			$res['id'] = $id;
			$res['newid'] = $id;
			$res['result'] = $w;

			echo json_encode($res);

		}
		
	}


	if ($in['action'] == "getstr") {
		
		$SQL = "SELECT * FROM `modules_access` WHERE `id` = ".$id;
		$r = mysql_query($SQL);

		if ($row = mysql_fetch_array($r)) {	
			
			$w = MakeRuleRow($row, count($access));
		
			$w = iconv("WINDOWS-1251" ,"UTF-8", "$w");

			$res['id'] = $id;
			$res['result'] = $w;

			echo json_encode($res);

		}

	}

	if ($in['action'] == "setrules") {

		$new = false;

		if ($id == "new") {
			
			$SQL = "INSERT INTO `modules_access`  SET `name` = '".$in['name']."', 
													`name_type` = '".$in['name_type']."',
													`auth_type` = 'site',
													`module` = '".$in['module']."',
													`admin` = '".$in['admin']."',  
													`rules` = '".$in['rules']."',
													`author` = '".AuthUserId."',
													`add_time` = '".time()."'";

			

			$r = mysql_query($SQL);

			$id = mysql_insert_id();

			$new = true;
		
		} else {

			$SQL = "UPDATE `modules_access` SET `name` = '".$in['name']."', 
												`name_type` = '".$in['name_type']."', 
												`admin` = '".$in['admin']."',  
												`rules` = '".$in['rules']."',
												`author` = '".AuthUserId."'	WHERE `id` = ".$id;
		
			$r = mysql_query($SQL);

		}

		$SQL = "SELECT * FROM `modules_access` WHERE `id` = ".$id;
		$r = mysql_query($SQL);

		if ($row = mysql_fetch_array($r)) {	
			
			$w = MakeRuleRow($row, count($access));
		
			$w = iconv("WINDOWS-1251" ,"UTF-8", "$w");

			if ($new) {
				$res['id'] = "new";
				$res['newid'] = $id;
			} else {
				$res['id'] = $id;
			}
			
			$res['result'] = $w;

			echo json_encode($res);

		}

	}

}

?>