<?php

function InitAccessArr()
{
	//заполняем массив прав доступа значениями по умолчанию
	$access['read']		= false;
	$access['add']		= false;
	$access['verify']	= false;
	$access['delete']	= false;
	// $access['admin']	= false; - идентификатор зарезервирован	под	системное поле.	
	
	return $access;
}

function InitLableArr()
{
	//заполняем	массив названий прав
	$lable['read']	  =	"Просмотр";
	$lable['add']	  =	"Добавление на проверку";
	$lable['verify'] =	"Проверка, редактирование, публикация";
	$lable['delete'] =	"Удаление";
	// $lable['admin']	= "Администрирование"; - идентификатор зарезервирован	под	системное поле.	
	
	return $lable;
}

$module_name  =	"moders_db";
$module_lable =	"В помощь модератору";

if (isset($onlyfunc)) return true;


$access	= InitAccessArr(); // иницилизируем	массив
$access	= GetAccessArr($module_name, $access); // заполняем	массив правами

echo MakeModuleLable($module_name, $module_lable, $access);

if ($access['read']) {
	
	if ($_REQUEST['o'] == 'new') {
		$ord  =	"`id` DESC";
		$extr =	'&o=new';
	} else {
		$ord  =	'BINARY(`expr`)	ASC';
		$extr =	'';
	}
	
	$src = $_REQUEST['search_str'];
	
	if ($access['verify']) {
		$query = "SELECT * FROM	`moders_db`	WHERE `expl` = ''";
		$rs	= mysql_query($query) or die(mysql_error());
		$new_q = mysql_num_rows($rs);
		echo ("<br><a href='?act=moders_db&sec=new'>Очередь	на проверку	(" . $new_q	. ")</a>");
	}
	
	if ($access['add'])	{
		echo ("<br><a href='?act=moders_db&sec=ask'>Добавить выражение</a><br><br>");
	}
	?>

	<form name="options" method="POST" action="?act=moders_db">
	Сортировать по: <br>
	<select name="o">
	<option	value="alp"	<?
	if (@$_REQUEST['o']	!==	'new') {
		echo ("selected");
	}
	?>>алфавиту</option>
	<option	value="new"	<?
	if (@$_REQUEST['o']	== 'new') {
		echo ("selected");
	}
	?>>новизне</option>
	</select>
	<br>
	Поиск:<br>
	<input type="text" name="search_str">
	<br>
	<input type="submit" name="Submit" value="ОК">
	</form>
	<?
	if ($access['delete'] && isset($_REQUEST['del'])) {
		$query = "DELETE FROM `moders_db` WHERE	`id` = '" .	$_REQUEST['del'] . "' LIMIT	1;";
		
		mysql_query($query)	or die(mysql_error());
	}
	
	if ($access['add'] && $_REQUEST['sec'] == 'ask') {
		if (!isset($_REQUEST['step'])) {
			
			?>
			<form name="ask" method="post" action="?act=moders_db&sec=ask">
			<div	align="center">
			Выражение: <br>
			<input name="expr" type="text" size="75">
			<input name="step" type="hidden" value="2">
			<br>
			<input type="submit" name="Submit" value="ОК"	style="width:200px;">
			</div>
			</form>
			<?
		} elseif ($_REQUEST['step']	== 2) {
			$n_expr	= addslashes(strip_tags($_REQUEST['expr']));
			$query	= "INSERT INTO `moders_db` (`id`, `expr`, `expl`, `treat`) VALUES ('', '" .	$n_expr	. "', '', '')";
			mysql_query($query)	or die(mysql_error());
			
			echo ("Спасибо за Ваш вопрос. <a href='?act=moders_db'>Вернуться</a>");
		}
	}
	
	if ($access['verify'] && isset($_REQUEST['answ'])) {
		if (!isset($_REQUEST['step'])) {
			$query = "SELECT * FROM	`moders_db`	WHERE `id` = '"	. $_REQUEST['answ']	. "' LIMIT 1;";
			$rs	   = mysql_query($query);
			
			list($e_id,	$e_expr, $e_expl, $e_treat)	= mysql_fetch_row($rs);
			?>
			<form name="answer"	method="post" action="?act=moders_db">
			<div	align="center">
			Выражение: <br>
			<input name="expr" type="text" size="75" value="<?=	stripslashes($e_expr) ?>">
			<input name="step" type="hidden" value="2">
			<input name="answ" type="hidden" value="<?=	$e_id ?>">
			<br>
			Объяснение:	<br>
			<?
			$e_expl	= str_replace("<br>", "\n",	$e_expl);
			?>
			<textarea name="expl" cols="70"	rows="5"><?= stripslashes($e_expl) ?></textarea>
			<br>
			Наказание: <br>
			<?
			$e_treat = str_replace("<br>", "\n", $e_treat);
			?>
			<textarea name="treat" cols="70" rows="5"><?= stripslashes($e_treat) ?></textarea>
			<br>
			<input type="submit" name="Submit" value="ОК" style="width:200px;">
			</div>
			</form>
			<?
		} elseif ($_REQUEST['step']	== 2) {
			$n_expr	 = addslashes(strip_tags($_REQUEST['expr']));
			$n_expl	 = str_replace("\r", "<br>", $_REQUEST['expl']);
			$n_expl	 = str_replace("\n", "<br>", $n_expl);
			$n_expl	 = str_replace("<br><br>", "<br>", $n_expl);
			$n_expl	 = addslashes(strip_tags($n_expl, "<br>"));
			$n_treat = str_replace("\r", "<br>", $_REQUEST['treat']);
			$n_treat = str_replace("\n", "<br>", $n_treat);
			$n_treat = str_replace("<br><br>", "<br>", $n_treat);
			$n_treat = addslashes(strip_tags($n_treat, "<br>"));
			
			$query = "UPDATE `moders_db` SET `expr`	= '" . $n_expr . "', `expl`	= '" . $n_expl . "', `treat` = '" .	$n_treat . "' WHERE	`id` = '" .	$_REQUEST['answ'] . "' LIMIT 1;";
			mysql_query($query)	or die(mysql_error());
		}
	}
	
	if ($_REQUEST['sec'] ==	'new') {
		$rs	= mysql_query("SELECT *	FROM `moders_db` WHERE `expl` =	''");
		
		?>
		<table width="90%"	border="0" cellspacing="2" cellpadding="3" align="center">
		<?
		$bg	= 0;
		while (list($e_id, $e_expr,	$e_expl) = mysql_fetch_row($rs)) {
			echo '<tr><td '	. $bgstr[$bg] .	'><strong><font	size="+1">';
			echo stripslashes($e_expr);
			echo '</font>';
			if ($access['verify']) {
				echo ("	<a href='?act=moders_db&answ=$e_id'>[E]</a>");
			}
			if ($access['delete']) {
				echo ("	<a href='?act=moders_db&del=$e_id' onClick=\"if(!confirm('Вы уверены?')) {return false};\">[X]</a>");
			}
			
			?><br>
			<br>
			</strong><em><?= stripslashes($e_expl) ?></em> </td>
			</tr>
			<tr>
			<?
			$bg++;
			if ($bg	> 1) {
				$bg	= 0;
			}
		}
		?>
		</table>
		<?
	}
	
	if (strlen($src) > 0) {
		$bgstr[0] =	"background='i/bgr-grid-sand.gif'";
		$bgstr[1] =	"background='i/bgr-grid-sand1.gif'";
		$search	  =	addslashes(strip_tags($src));
		$rs		  =	mysql_query("SELECT	* FROM `moders_db` WHERE `expl`	<> '' AND `expr` LIKE '%" .	$search	. "%' ORDER	BY " . $ord	. ";");
		if (mysql_num_rows($rs)	== 0) {
			echo ("<center><b>Ничего не	найдено.</b></center>");
		} else {
			?>
			<center><b>Результаты поиска <i>"<?= $src ?>"</i></b></center>
			<a href="?act=moders_db">Вернуться к списку</a>
			<table width="90%"	border="0" cellspacing="2" cellpadding="3" align="center">
			<?
			$bg	= 0;
			while (list($e_id, $e_expr,	$e_expl, $e_treat) = mysql_fetch_row($rs)) {
				echo '<tr><td '	. $bgstr[$bg] .	'><b><font size="+1">';
				echo (stripslashes($e_expr));
				echo '</font>';
				if ($access['verify']) {
					echo ("	<a href='?act=moders_db&answ=$e_id'>[E]</a>");
				}
				if ($access['delete']) {
					echo ("	<a href='?act=moders_db&del=$e_id' onClick=\"if(!confirm('Вы уверены?')) {return false};\">[X]</a>");
				}
				?><br>
				<br>
				</b><em><?= stripslashes($e_expl)	?></em>
				<br>
				<br>
				<b>Наказание:	</b><?=	stripslashes($e_treat) ?><br><br></td>
				</tr>
				<tr>
				<?
				$bg++;
				if ($bg	> 1) {
					$bg	= 0;
				}
			}
			?>
			</table>
			<?
		}
	}
	
	
	if ((!isset($_REQUEST['answ']) || $_REQUEST['step']	== 2) && !isset($_REQUEST['sec']) && strlen($src) == 0)	{
		
		$max_on_page = 30;
		
		$rs	   = mysql_query('SELECT COUNT(*) FROM	`moders_db`');
		$rs	   = mysql_fetch_row($rs);
		$pages = round($rs[0] /	$max_on_page);
		//echo ($pages);
		//$rs=mysql_fetch_row($rs);
		
		$bgstr[0] =	"background='i/bgr-grid-sand.gif'";
		$bgstr[1] =	"background='i/bgr-grid-sand1.gif'";
		
		$page =	$_REQUEST['p'];
		
		$rs	= mysql_query("SELECT *	FROM `moders_db` WHERE `expl` <> ''	ORDER BY " . $ord .	" LIMIT	" .	($page * $max_on_page) . "," . $max_on_page	. ";");
		?>
		<table width="90%"	border="0" cellspacing="2" cellpadding="3" align="center">
		<?
		$bg	= 0;
		while (list($e_id, $e_expr,	$e_expl, $e_treat) = mysql_fetch_row($rs)) {
			?>
			<tr>
			<td	<?=	$bgstr[$bg]	?>><strong><font size="+1"><?
			echo (stripslashes($e_expr));
			?></font><?
			if ($access['verify']) {
				echo ("	<a href='?act=moders_db&answ=$e_id'>[E]</a>");
			}
			if ($access['delete']) {
				echo ("	<a href='?act=moders_db&del=$e_id' onClick=\"if(!confirm('Вы уверены?')) {return false};\">[X]</a>");
			}
			?><br>
			<br>
			</strong><em><?= stripslashes($e_expl) ?></em>
			<br>
			<br>
			<b>Наказание:	</b><?=	stripslashes($e_treat) ?></td>
			</td>
			</tr>
			<tr>
			<?
			$bg++;
			if ($bg	> 1) {
				$bg	= 0;
			}
		}
		?>
		</table>
		<div align="right">
		<?
		echo 'Страницы:	';
		for	($i	= 0; $i	<= $pages; $i++) {
			if ($i != $page)
			echo '<a href="?act=moders_db&p=' .	$i . $extr . '">' .	($i	+ 1) . '</a>&nbsp;';
			else
			echo '<b>['	. ($i +	1) . ']</b>&nbsp;';
		}
		?>
		</div>
		<?
	}
} else {
	
	echo $mess['AccessDenied'];	
	
}



?> 