<?
error_reporting(E_ALL);
include_once('system.php');
/*
# авторизация (что-то в этом духе)
include_once($_SERVER['DOCUMENT_ROOT'].'/autorization.php');
$sql = "SELECT `dept`, `name` FROM `sd_cops` WHERE `dept` = '27'";
*/

// dirty quick injection removal
foreach ($_GET as $key=>$value)
{
	$_GET[$key]=mysql_real_escape_string($value);
}
foreach ($_POST as $key=>$value)
{
	$_POST[$key]=mysql_real_escape_string($value);
}

if (AuthUserGroup != 100):
	echo 'Access denied';
	exit;
endif;

if (isset($_POST['cancel'])):
	header('Location: '.$_SERVER['PHP_SELF'].(isset($_POST['test'])?'?act=testnew_admin&id='.$_POST['test']:''));
endif;

if (isset($_POST['edit_test'])):
	if (isset($_POST['id'])):
		mysql_query("UPDATE `test_test_main` SET `name` = '{$_POST['name']}', `time` = '{$_POST['time']}' WHERE `id` = '{$_POST['id']}'");
	else:
		mysql_query("UPDATE `test_test_main` SET `ord` = (`ord`+1)");
		mysql_query("INSERT INTO `test_test_main` (`id`, `name`, `creator`, `time`, `ord`, `show`) VALUES ('', '{$_POST['name']}', '{$_POST['creator']}', '{$_POST['time']}', '1', '1')");
	endif;
	header('Location: '.$_SERVER['PHP_SELF']);
endif;

if (isset($_POST['edit_question'])):
	if (isset($_POST['id'])):
		mysql_query("UPDATE `test_question_main` SET `question` = '{$_POST['question']}', `answers` = '{$_POST['answers']}', `correct_answer` = '{$_POST['correct_answer']}' WHERE `id` = '{$_POST['id']}'");
	else:
		mysql_query("INSERT INTO `test_question_main` (`id`, `test`, `question`, `answers`, `correct_answer`, `show`) VALUES ('', '{$_POST['test']}', '{$_POST['question']}', '{$_POST['answers']}', '{$_POST['correct_answer']}', '1')");
	endif;
	header('Location: '.$_SERVER['PHP_SELF'].'?act=testnew_admin&id='.$_POST['test']);
endif;

switch (@$_GET['action']) {
	case 'edit_test':
?>
<form name="" action="" method="post">
<?
	if (isset($_GET['id'])):
		$test = GetId('test_test_main',$_GET['id']);
		echo '<input name="id" type="hidden" value="'.$test['id'].'">';
	endif;
?>
<input name="creator" type="hidden" value="<?=AuthUserName?>">
<table>
<tr>
<td>Название теста:</td>
<td><input name="name" type="text" value="<?=@$test['name']?>"></td>
</tr>
<tr>
<td>Отведенное время, мин:</td>
<td><input name="time" type="text" value="<?=@$test['time']?>"></td>
</tr>
<tr>
<td align="right"><input type="submit" value="Сохранить" name="edit_test"></td>
<td><input type="submit" value="Отменить" name="cancel"></td>
</tr>
</table>
</form>
<?
	break;

	case 'edit_question':
?>
<form name="" action="" method="post">
<?
	if (isset($_GET['id'])):
		$test = GetId('test_question_main',$_GET['id']);
		echo '<input name="id" type="hidden" value="'.$test['id'].'">';
	endif;
?>
<input name="test" type="hidden" value="<?=$_GET['test']?>">
<table>
<tr>
<td>Название вопроса:</td>
<td><input name="question" type="text" value="<?=@$test['question']?>"></td>
</tr>
<tr valign="top">
<td>Варианты ответов<br /><small>(вводить построчно)</small></td>
<td><textarea name="answers" rows=4 cols=20><?=@$test['answers']?></textarea></td>
</tr>
<tr>
<td>Правильный ответ:</td>
<td><input name="correct_answer" type="text" value="<?=@$test['correct_answer']?>"></td>
</tr>
<tr>
<td align="right"><input type="submit" value="Сохранить" name="edit_question"></td>
<td><input type="submit" value="Отменить" name="cancel"></td>
</tr>
</table>
</form>
<?
	break;

	case 'logs':
	if (!isset($_GET['id'])):
		$query = mysql_query("SELECT * FROM `test_ip_main` ORDER BY `id` ASC");
		echo '<p><a href="'.$_SERVER['PHP_SELF'].'">Админка</a> / Юзеры</p>';
		echo '<table>';
		while ($login = mysql_fetch_assoc($query)):
			echo '<tr>';
			echo '<td><a href="'.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=logs&id='.$login['id'].'">'.$login['login'].'</a></td>';
			echo '<td>'.$login['ip'].'</td>';
			echo '<td><a href="#null" onClick="if (confirm(\'Вы действительно хотите удалить все логи этого пользователя?\')) {parent.location=\''.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=del_all_logs&id='.$login['id'].'\';}"><img src="images/delete.gif" alt="" border="0"></a></td>';
			echo '</tr>';
		endwhile;
		echo '</table>';
	else:
		if (!isset($_GET['test'])):
			echo '<p><a href="'.$_SERVER['PHP_SELF'].'">Админка</a> / <a href="'.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=logs">Юзеры</a> / Тесты</p>';
			echo '<table>';
			$test_res = mysql_query("SELECT * FROM `test_test_main` ORDER BY `ord` ASC");
			while ($test_row = mysql_fetch_assoc($test_res)):
				$query = mysql_query("SELECT * FROM `test_logs_main` WHERE `ip` = '{$_GET['id']}' AND `test` = '{$test_row['id']}'");
				if (mysql_num_rows($query) > 0):
					echo '<tr>';
					echo '<td><a href="'.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=logs&id='.$_GET['id'].'&test='.$test_row['id'].'">'.$test_row['name'].'</a></td>';
					$correct_answer = 0;
					while ($answer = mysql_fetch_assoc($query)):
						$question = GetId('test_question_main',$answer['answer']);
						if ($answer['answer']==$question['correct_answer']) $correct_answer++;
					endwhile;
					echo '<td>'.rating($correct_answer, mysql_num_rows(mysql_query("SELECT `test` FROM `test_question_main` WHERE `test` = '{$test_row['id']}'"))).'</td>';
					echo '<td><a href="#null" onClick="if (confirm(\'Вы действительно хотите удалить логи выполнения этого теста?\')) {parent.location=\''.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=del_test_logs&id='.$_GET['id'].'&test='.$test_row['id'].'\';}"><img src="images/delete.gif" alt="" border="0"></a></td>';
					echo '</tr>';
				endif;
			endwhile;
			echo '</table>';
		else:
			$test = GetId('test_test_main',$_GET['test']);
			echo '<p><a href="'.$_SERVER['PHP_SELF'].'">Админка</a> / <a href="'.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=logs">Юзеры</a> / <a href="'.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=logs&id='.$_GET['id'].'">Тесты</a> / '.$test['name'].'</p>';
			echo '<table>';
				$query = mysql_query("SELECT * FROM `test_logs_main` WHERE `ip` = '{$_GET['id']}' AND `test` = '{$_GET['test']}' ORDER BY `timer` ASC");
				while ($result = mysql_fetch_assoc($query)):
					echo '<tr>';
					$question = GetId('test_question_main',$result['question']);
					$answers = explode("\r\n",$question['answers']);
					echo '<td>'.$question['question'].'</td>';
					echo '<td><font color="#'.($question['correct_answer']==$result['answer']?'00ff00':'ff0000').'">'.$answers[$result['answer']-1].'</font></td>';
					echo '<td>'.$result['timer'].'</td>';
					echo '<td><a href="#null" onClick="if (confirm(\'Вы действительно хотите удалить лог этого вопроса?\')) {parent.location=\''.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=del_question_logs&id='.$_GET['id'].'&test='.$result['test'].'&question='.$result['id'].'\';}"><img src="images/delete.gif" alt="" border="0"></a></td>';
					echo '</tr>';
				endwhile;
			echo '</table>';
		endif;
	endif;
	break;

	case 'del_all_logs':
		mysql_query("DELETE FROM `test_logs_main` WHERE `ip` = '{$_GET['id']}'");
		mysql_query("DELETE FROM `test_ip_main` WHERE `id` = '{$_GET['id']}'");
		header('Location: '.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=logs');
	break;

	case 'del_test_logs':
		mysql_query("DELETE FROM `test_logs_main` WHERE `test` = '{$_GET['test']}'");
		header('Location: '.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=logs&id='.$_GET['id']);
	break;

	case 'del_question_logs':
		mysql_query("DELETE FROM `test_logs_main` WHERE `id` = '{$_GET['question']}'");
		header('Location: '.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=logs&id='.$_GET['id'].'&test='.$_GET['test']);
	break;

	case 'del_test':
		$test = GetId('test_test_main',$_GET['id']);
		mysql_query("UPDATE `test_test_main` SET `ord` = (`ord`-1) WHERE `ord` > '{$test['ord']}'");
		mysql_query("DELETE FROM `test_question_main` WHERE `test` = '{$_GET['id']}'");
		mysql_query("DELETE FROM `test_test_main` WHERE `id` = '{$_GET['id']}'");
		header('Location: '.$_SERVER['PHP_SELF']);
	break;

	case 'del_question':
		mysql_query("DELETE FROM `test_question_main` WHERE `id` = '{$_GET['id']}'");
		header('Location: '.$_SERVER['PHP_SELF'].'?act=testnew_admin&id='.$_GET['test']);
	break;

	case 'show_t':
		mysql_query("UPDATE `test_test_main` SET `show` = '1' WHERE `id` = '{$_GET['id']}'");
		header('Location: '.$_SERVER['PHP_SELF']);
	break;

	case 'hide_t':
		mysql_query("UPDATE `test_test_main` SET `show` = '0' WHERE `id` = '{$_GET['id']}'");
		header('Location: '.$_SERVER['PHP_SELF']);
	break;

	case 'show_q':
		mysql_query("UPDATE `test_question_main` SET `show` = '1' WHERE `id` = '{$_GET['id']}'");
		header('Location: '.$_SERVER['PHP_SELF'].'?act=testnew_admin&id='.$_GET['test']);
	break;

	case 'hide_q':
		mysql_query("UPDATE `test_question_main` SET `show` = '0' WHERE `id` = '{$_GET['id']}'");
		header('Location: '.$_SERVER['PHP_SELF'].'?act=testnew_admin&id='.$_GET['test']);
	break;

	case 'up':
		$test = GetId('test_test_main',$_GET['id']);
		if ($test !== false && $test['ord'] > 1)
		{
			mysql_query("UPDATE `test_test_main` SET `ord` = (`ord`+1) WHERE `ord` = '".($test['ord']-1)."'");
			mysql_query("UPDATE `test_test_main` SET `ord` = (`ord`-1) WHERE `id` = '{$_GET['id']}'");
		}
		header('Location: '.$_SERVER['PHP_SELF']);
	break;

	case 'down':
		$test = GetId('test_test_main',$_GET['id']);
		$CountContent = mysql_num_rows(mysql_query("SELECT `id` FROM `test_test_main`"));
		if ($test !== false && $test['ord'] < $CountContent)
		{
			mysql_query("UPDATE `test_test_main` SET `ord` = (`ord`-1) WHERE `ord` = '".($test['ord']+1)."'");
			mysql_query("UPDATE `test_test_main` SET `ord` = (`ord`+1) WHERE `id` = '{$_GET['id']}'");
		}
		header('Location: '.$_SERVER['PHP_SELF']);
	break;



	default:
		if (!isset($_GET['id'])):
			$query = mysql_query("SELECT * FROM `test_test_main` ORDER BY `ord` ASC");
			$CountContent = mysql_num_rows($query);
			echo '<table>';
			while ($test = mysql_fetch_assoc($query)):
				echo '<tr>';
				echo '<td><a href="'.$_SERVER['PHP_SELF'].'?act=testnew_admin&action='.($test['show']!=0?'hide_t':'show_t').'&id='.$test['id'].'"><img src="images/'.($test['show']!=0?'on':'off').'.gif" alt="" border="0"></a></td>';
				echo '<td align="center">';
				echo $test['ord']!=1?'<a href="'.$ing_self.'?act=testnew_admin&action=up&id='.$test['id'].'"><img src="images/up.gif" alt="" border="0"></a>':'';
				echo $test['ord']!=$CountContent?'<a href="'.$ing_self.'?act=testnew_admin&action=down&id='.$test['id'].'"><img src="images/down.gif" alt="" border="0"></a>':'';
				echo '</td>';
				echo '<td><a href="'.$_SERVER['PHP_SELF'].'?act=testnew_admin&id='.$test['id'].'">'.$test['name'].'</a></td>';
				echo '<td><a href="'.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=edit_test&id='.$test['id'].'"><img src="images/edit.gif" alt="" border="0"></a> <a href="#null" onClick="if (confirm(\'Вы действительно хотите удалить этот тест? Все связанные с ним вопросы будут тоже удалены.\')) {parent.location=\''.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=del_test&id='.$test['id'].'\';}"><img src="images/delete.gif" alt="" border="0"></a></td>';
				echo '</tr>';
			endwhile;
			echo '</table>';
			echo '<br /><br /><a href="'.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=edit_test">Добавить тест</a>';
			echo '<br /><a href="'.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=logs">Просмотр логов</a>';
		else:
			$test = GetId('test_test_main',$_GET['id']);
			echo '<p><a href="?act=testnew_admin">Все тесты</a> / '.$test['name'].'</p>';
			$query = mysql_query("SELECT * FROM `test_question_main` WHERE `test` = '{$test['id']}' ORDER BY `id` ASC");
			echo '<table>';
			while ($question = mysql_fetch_assoc($query)):
				echo '<tr>';
				echo '<td><a href="'.$_SERVER['PHP_SELF'].'?act=testnew_admin&action='.($question['show']!=0?'hide_q':'show_q').'&test='.$question['test'].'&id='.$question['id'].'"><img src="images/'.($question['show']!=0?'on':'off').'.gif" alt="" border="0"></a></td>';
				echo '<td>'.$question['question'].'</td>';
				echo '<td><a href="'.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=edit_question&test='.$question['test'].'&id='.$question['id'].'"><img src="images/edit.gif" alt="" border="0"></a> <a href="#null" onClick="if (confirm(\'Вы действительно хотите удалить этот вопрос?\')) {parent.location=\''.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=del_question&test='.$question['test'].'&id='.$question['id'].'\';}"><img src="images/delete.gif" alt="" border="0"></a></td>';
				echo '</tr>';
			endwhile;
			echo '</table>';
			echo '<br /><br /><a href="'.$_SERVER['PHP_SELF'].'?act=testnew_admin&action=edit_question&test='.$_GET['id'].'">Добавить вопрос</a>';
		endif;
	break;
}
?>