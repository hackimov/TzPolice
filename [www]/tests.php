<?php

require('_modules/functions.php');
require('_modules/auth.php');

$bgstr[0]="background='i/bgr-grid-sand.gif'";
$bgstr[1]="background='i/bgr-grid-sand1.gif'";

if(AuthStatus==1 && AuthUserName!='' && (AuthUserClan=='police' || AuthUserClan=='Military Police' || AuthUserClan=='Financial Academy' || AuthUserClan=='Police Academy' || AuthUserGroup==100 || (abs(AccessLevel) & AuthTestsAdmin))) {
	if (!(list($_REQUEST['t']) = mysql_fetch_array(mysql_query("SELECT id FROM tests_items WHERE id='".$_REQUEST['t']."' AND visible='1'"))))
		$_REQUEST['t'] = 0;

?>

<html>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<head>
	<title>TZ Police Department :: Тестирование</title>
<?
		include('_modules/header.php');
?>
<SCRIPT src='/_modules/tzpol_java.js'></SCRIPT>
</head>

<body<?=($_REQUEST['m']=='test' && $_REQUEST['t']?' onload="showTimer()"':'')?>>
	<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0">
	<tr><td width="100%" height="100%" background="i/block-22.jpg" bgcolor="EBDFB7" class="repeat">
	<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" background="i/block-12.jpg" class="tab-top-left-repeat-x">
	<tr><td valign="top" background="i/block-32.jpg" class="bottom-repeat-x">
	<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" background="i/block-21.gif" class="tab-top-left-repeat-y">
	<tr><td valign="top" background="i/block-23.gif" class="top-right-repeat-y">
	<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" background="i/block-11.jpg" class="tab-top-left-norepeat">
	<tr><td background="i/block-13.gif" class="top-right-norepeat">
	<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" background="i/block-31.gif" class="tab-bottom-left-norepeat">
	<tr><td valign="top" background="i/block-33.gif" class="bottom-right-norepeat">
	<table width="100%"  border="0" cellspacing="0" cellpadding="30">
	<tr><td>

<?

		if ((abs(AccessLevel) & AuthTestsAdmin) || AuthUserGroup==100) {
			if (isset($_REQUEST['nitem'])) {
				if ($_REQUEST['item_id']) {
					$SQL = "UPDATE `tests_items` SET `name`='".$_REQUEST['item']."', time='".$_REQUEST['time']."', qmax='".$_REQUEST['qmax']."', amax='".$_REQUEST['amax']."', visible='".(isset($_REQUEST['vis'])?'1':'0')."' WHERE id='".$_REQUEST['item_id']."'";
					$r = mysql_query($SQL);
					
					if ($r) {
						echo "<font color='green'>Тестирование успешно изменено</font><br><br>";
					} else {
						echo "<font color='red'><b>Ошибка во время изменения тестирования</b></font><br><br>";
					}
				} else {
					$SQL = "INSERT INTO tests_items VALUES ('', '".$_REQUEST['item']."', '".$_REQUEST['time']."', '".$_REQUEST['qmax']."', '".$_REQUEST['amax']."', '".(isset($_REQUEST['vis'])?'1':'0')."')";
					$r = mysql_query($SQL);
					
					if ($r) {
						echo "<font color='green'>Новое тестирование успешно добавлено</font><br><br>";
					} else {
						echo "<font color='red'><b>Ошибка во время добавления тестирования</b></font><br><br>";
					}
				}
			}
			
			if (isset($_REQUEST['add_text'])) {
				if ($_REQUEST['text_id']) {
					$text = '+' . $_REQUEST['NewsText'];
					$qp = strpos($text, "<question>");
					if ($qp > 0) {
						$SQL = "UPDATE tests_text SET test_text='".substr($text, $qp+10, strpos($text, "</question>", $next)-$qp-10)."' WHERE id='".$_REQUEST['text_id']."'";
						mysql_query($SQL);
						
						$next = strpos($text, "</question>", $next) + 11;
					}
					
					$ids = array();
					
					$ap = strpos($text, "<answer", $next);
					
					while ($ap > 0) {
						$answer = substr($text, $ap+7, strpos($text, "</answer>", $next)-$ap-7);
						if (substr($answer,1,3) == 'id=') {
							$id = substr($answer,4,strpos($answer,'>')-4);
							$ids[$id] = 1;
							$answer = substr($answer,strpos($answer,'>')+1);
							$SQL = "UPDATE tests_text SET test_text='".$answer."' WHERE id='".$id."' AND to_question='".$_REQUEST['text_id']."'";
							mysql_query($SQL);
							
						} else {
							$answer = substr($answer,1);
							$SQL = "INSERT INTO tests_text VALUES('', '".$answer."', '".$_REQUEST['text_id']."', '', '".$_REQUEST['i']."')";
							mysql_query($SQL);
							$ids[mysql_insert_id()] = 1;
						}
						
						$next = strpos($text,"</answer>",$next) + 9;
						$ap = strpos($text,"<answer",$next);
					}
					
					$SQL = "SELECT id FROM tests_text WHERE to_question='".$_REQUEST['text_id']."'";
					$r = mysql_query($SQL);
					
					while ($d = mysql_fetch_array($r)) {
						if (!($ids[$d['id']])) {
							$SQL = "DELETE FROM tests_text WHERE id='".$d['id']."'";
							mysql_query($SQL);
						}
					}
					
				} else {
					$text = '+' . $_REQUEST['NewsText'];
					$qp = strpos($text,"<question>");
					$ap = strpos($text,"<answer>");
					$next = 0;
					
					while ($qp > 0 || $ap > 0) {
						if (($qp < $ap && $qp > 0) || ($ap == 0 && $qp > 0)) {
							$SQL = "INSERT INTO tests_text VALUES('', '".substr($text, $qp+10, strpos($text, "</question>", $next)-$qp-10)."', '', '', '".$_REQUEST['i']."')";
							mysql_query($SQL);
							$qid = mysql_insert_id();
							$next = strpos($text, "</question>", $next) + 11;
						
						} elseif ($ap > 0) {
							if ($qid) {
								$SQL = "INSERT INTO tests_text VALUES('', '".substr($text, $ap+8, strpos($text, "</answer>", $next)-$ap-8)."', '".$qid."', '', '".$_REQUEST['i']."')";
								mysql_query($SQL);
							} else {
								echo "<font color='red'><b>Ошибка: нет вопроса для данного ответа</b></font><br><br>";
							}
							$next = strpos($text,"</answer>",$next) + 9;
						}
						
						$qp = strpos($text,"<question>",$next);
						$ap = strpos($text,"<answer>",$next);
					}
				}
			}

	if ($_REQUEST['del']) {

		if ($_REQUEST['qid']) {

			list($item_id) = mysql_fetch_array(mysql_query("SELECT to_item FROM tests_text WHERE id='".$_REQUEST['qid']."'"));

			$SQL = "SELECT * FROM tests_results WHERE item_id='".$item_id."' AND ((questions REGEXP '\\\\|".$_REQUEST['qid']."\\\\|')=1 OR RIGHT(questions,".(strlen($_REQUEST['qid'])+1).")='\|".$_REQUEST['qid']."' OR LEFT(questions,".(strlen($_REQUEST['qid'])+1).")='".$_REQUEST['qid']."\|' OR questions='".$_REQUEST['qid']."')";

			if ($r = mysql_fetch_array(mysql_query($SQL))) {

				echo "<font color='red'><b>Вопрос присутствует в результатах тестирований. Удаление невозможно!</b></font><br><br>";

			} else {

				$SQL = "DELETE FROM tests_text WHERE id='".$_REQUEST['qid']."' OR to_question='".$_REQUEST['qid']."'";

				$r = mysql_query($SQL);

				if ($r) { echo "<font color='green'>Вопрос успешно удален</font><br><br>"; }

				else { echo "<font color='red'><b>Ошибка во время удаления вопроса</b></font><br><br>"; }

			}

		}

		if ($_REQUEST['aid']) {

			list($item_id) = mysql_fetch_array(mysql_query("SELECT to_item FROM tests_text WHERE id='".$_REQUEST['aid']."'"));

			$SQL = "SELECT * FROM tests_results WHERE item_id='".$item_id."' AND ((answers REGEXP ',".$_REQUEST['aid'].",')=1 OR (answers REGEXP '\\\\|".$_REQUEST['aid'].",')=1 OR (answers REGEXP ',".$_REQUEST['aid']."\\\\|')=1 OR LEFT(answers,".(strlen($_REQUEST['aid'])+1).")='".$_REQUEST['aid']."\|' OR LEFT(answers,".(strlen($_REQUEST['aid'])+1).")='".$_REQUEST['aid'].",' OR RIGHT(answers,".(strlen($_REQUEST['aid'])+1).")='\|".$_REQUEST['aid']."' OR RIGHT(answers,".(strlen($_REQUEST['aid'])+1).")=',".$_REQUEST['aid']."' OR answers='".$_REQUEST['aid']."')";

			if ($r = mysql_fetch_array(mysql_query($SQL))) {

				echo "<font color='red'><b>Ответ присутствует в результатах тестирований. Удаление невозможно!</b></font><br><br>";

			} else {

				$SQL = "DELETE FROM tests_text WHERE id='".$_REQUEST['aid']."' AND to_question>0";

				$r = mysql_query($SQL);

				if ($r) { echo "<font color='green'>Ответ на вопрос успешно удален</font><br><br>"; }

				else { echo "<font color='red'><b>Ошибка во время удаления ответа</b></font><br><br>"; }

			}

		}

		if ($_REQUEST['iid']) {

			$SQL = "DELETE FROM tests_results WHERE item_id='".$_REQUEST['iid']."'";

			mysql_query($SQL);

			$SQL = "DELETE FROM tests_items WHERE id='".$_REQUEST['iid']."'";

			$r = mysql_query($SQL);

			if ($r) { echo "<font color='green'>Тестирование успешно удалено</font><br><br>"; }

			else { echo "<font color='red'><b>Ошибка во время удаления тестирования</b></font><br><br>"; }

		}

	}

	if (isset($_REQUEST['ptest'])) {

		$SQL = "SELECT id, to_item FROM tests_text WHERE to_item='".$_REQUEST['i']."' AND to_question=0";

		$r = mysql_query($SQL);

		while (list($id, $to_item) = mysql_fetch_array($r)) {

			if ($_REQUEST['items_'.$id] != $to_item) {

				$SQL = "UPDATE tests_text SET to_item='".$_REQUEST['items_'.$id]."' WHERE id='".$id."' OR to_question='".$id."'";

				mysql_query($SQL);

			}

			$balls = 0;

			$SQL = "SELECT id, ball FROM tests_text WHERE to_question='".$id."'";

			$rr = mysql_query($SQL);

			while (list($aid, $ball) = mysql_fetch_array($rr)) {

				if ($_REQUEST['ball_'.$aid] != $ball) {

					$SQL = "UPDATE tests_text SET ball='".$_REQUEST['ball_'.$aid]."' WHERE id='".$aid."'";

					mysql_query($SQL);

				}

				$balls += $_REQUEST['ball_'.$aid];

			}

			$SQL = "UPDATE tests_text SET ball='".$balls."' WHERE id='".$id."' AND to_question=0";

			mysql_query($SQL);

		}

	}

}

?>



<? if ($_REQUEST['m']=='edit' && ((abs(AccessLevel) & AuthTestsAdmin) || AuthUserGroup==100)) {



if (isset($_REQUEST['i'])) {



$items = array();

$items[0] = "Вне тестирований";

$SQL = "SELECT id, name FROM tests_items ORDER BY name";

$r = mysql_query($SQL);

while (list($id, $item) = mysql_fetch_array($r)) {

	$items[$id] = $item;

}

?>



<script language="JavaScript">

function edit_question(a) {

	if (a) {

		document.News.text_id.value = a;

		document.News.add_text.value = 'Изменить';

		var str = document.all('m'+a).innerHTML;

		str = "<question>"+str.substring(str.indexOf('gif')+6, str.indexOf('javascript:edit_question')-11)+"</question>\n\n";

		while (str.indexOf('<BR>')>=0) str = str.replace('<BR>',"\n");

		document.News.NewsText.value = str;

		var i = 1;

		while (document.all('m'+a+'_'+i)) {

			str = document.all('m'+a+'_'+i).innerHTML;

			aid = str.substring(str.indexOf('aid=')+4, str.indexOf('del=')-5);

			str = "<answer id="+aid+">"+str.substring(str.indexOf('<li>')+5, str.indexOf('m=edit')-12)+"</answer>\n\n";

			while (str.indexOf('<BR>')>=0) str = str.replace('<BR>',"\n");

			document.News.NewsText.value += str;

			i++;

		}

	} else {

		document.News.text_id.value = 0;

		document.News.add_text.value = 'Добавить';

		document.News.NewsText.value = '';

	}

}

</script>



| <a href=/>Домой</a> | <a href=?m=edit>Назад</a> |



<table width=100% border='0' cellspacing='3' cellpadding='3'>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><strong><?=$items[$_REQUEST['i']]?> [<a href="javascript:edit_question(0)">new</a>]</p></td></tr>

<tr><td>



<table width=100% cellpadding=5>

<tr><td valign=top>

	<form action="?m=edit&i=<?=$_REQUEST['i']?>" method="POST" name="News">

	<input name='text_id' type='hidden' value=0>

	Вопрос и ответы: <br>

	<textarea ONSELECT="storeCaret(this);" ONCLICK="storeCaret(this);" ONKEYUP="storeCaret(this);" name="NewsText" style="width:100%" rows=19><?=@htmlspecialchars(stripslashes($_REQUEST['NewsText']))?></textarea><br>

	<img src="/_imgs/b.gif" border=0 onclick="decor('b')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы сделать его полужирным">

	<img src="/_imgs/i.gif" border=0 onclick="decor('i')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы выделить его курсивом">

	<img src="/_imgs/u.gif" border=0 onclick="decor('u')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы сделать его подчеркнутым">

	<img src="/_imgs/center.gif" border=0 onclick="decor('div align=center')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы сделать отцентрировать его">

	<img src="/_imgs/question.gif" border=0 onclick="decor('question')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы пометить вопрос">

	<img src="/_imgs/answer.gif" border=0 onclick="decor('answer')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы пометить ответ">

	<div>* все HTML-теги разрешены</div>

	<div align=center><input name=add_text type="submit" value="Добавить"></div>

	</form>

	</td><td width=250 valign=top>

	Вставить: <br>

	<select size="1" onchange="document.getElementById('icons').src='_modules/icons/'+this.options[this.selectedIndex].value+'.php'" style="width:100%">

		<option value="blank" selected>-- выберите категорию --</option>

		<option value="smiles"> Смайлики</option>

		<option value="clans"> Кланы</option>

		<option value="prof"> Профессии</option>

		<option value="login"> Ник</option>

		<option value="url"> URL</option>

	</select>

	<iframe BORDER=0 FRAMEBORDER=0 id="icons" src="about:blank" width=100% HEIGHT=210 style="border:1 solid #000000"></iframe>

</td></tr>

</table>



</td><tr>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><strong>Вопросы тестирования:</p></td></tr>

<tr><td>



<form action="?m=edit&i=<?=$_REQUEST['i']?>" method="POST" name="TestParam">

<table width=100% cellpadding='3'>

<?

$l = 0;

$SQL = "SELECT id, test_text, ball FROM tests_text WHERE to_question=0 AND to_item='".$_REQUEST['i']."' ORDER BY id";

$r = mysql_query($SQL);

while ($d = mysql_fetch_array($r)) {

$question = str_replace("\015\012", "<br>", $d['test_text']);

$l++;

?>

<tr bgcolor=#F4ECD4><td><div id=m<?=$d['id']?>><font color=red><b><?=$l?>.</b></font> <img src="/i/bullet-red-01a.gif" onclick="javascript:if(l<?=$d['id']?>.style.display=='none') l<?=$d['id']?>.style.display=''; else l<?=$d['id']?>.style.display='none';" style="cursor: hand"> <?=$question?> [<a href="javascript:edit_question(<?=$d['id']?>)">E</a>] [<a href="?m=edit&i=<?=$_REQUEST['i']?>&qid=<?=$d['id']?>&del=1">X</a>] (<font color=green>Баллы за вопрос: <b><?=$d['ball']?></b></a>)</div>

<div id="l<?=$d['id']?>" style="display:none">

<hr>

<table width=100% cellpadding='3'>

<?

$SQL = "SELECT id, test_text, ball FROM tests_text WHERE to_question=".$d['id']." ORDER BY id";

$rr = mysql_query($SQL); $m = 0;

while ($dd = mysql_fetch_array($rr)) {

$answer = str_replace("\015\012", "<br>", $dd['test_text']); $m++;

?>

<tr bgcolor=#F4ECD4><td><div id=m<?=$d['id']."_".$m?>><li> <?=$answer?> [<a href="?m=edit&i=<?=$_REQUEST['i']?>&aid=<?=$dd['id']?>&del=1">X</a>]</div></td>

<td width=10%>Баллы&nbsp;за&nbsp;ответ:&nbsp;<input name=ball_<?=$dd['id']?> type=text size=5 value='<?=$dd['ball']?>'></td></tr>

<?

}

?>

</table>

</div></td>

<td width=10% valign=top>Тестирование: <select name=items_<?=$d['id']?>>

<?

foreach (array_keys($items) as $id) echo "<option value=".$id.($_REQUEST['i']==$id?' selected':'').">".$items[$id]."</option>\n";

?>

</select></td></tr>

<tr><td height=3 colspan=2></td></tr>

<?

}

if (mysql_num_rows($r)>0) {

?>

<tr><td colspan=2 align=center><input name="ptest" type="submit" value="Установить"></td></tr>

<?

}

?>

</table>

</form>



</td></tr>

</table>



<?

} else {

?>



<script language="JavaScript">

function edit_item(a,b,c,d,e,f) {

	if (c) {

		document.all("nitem").value = "Изменить";

		document.all("item_id").value = c;

		document.all("item").value = a;

		document.all("time").value = b;

		document.all("qmax").value = d;

		document.all("amax").value = e;

		document.all("vis").checked = f;

	} else {

		document.all("nitem").value = "Добавить";

		document.all("item_id").value = 0;

		document.all("item").value = '';

		document.all("time").value = '';

		document.all("qmax").value = '';

		document.all("amax").value = '';

		document.all("vis").checked = 0;

	}

}

</script>



| <a href=/>Домой</a> | <a href=?>Назад</a> |



<table border='0' cellspacing='3' cellpadding='3'>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><strong>Редактор тестирований:</strong> [<a href="javascript: edit_item(0)">new</a>]</p></td></tr>

<tr><td>



<table cellpadding='3'>

<form name="new_item" action="?m=edit" method="POST">

<input name=item_id type=hidden>

<tr bgcolor=#F4ECD4><td>Новое тестирование: </td><td colspan=3><input name=item type=text size=60></td></tr>

<tr bgcolor=#F4ECD4><td>Время на тест (в мин):</td><td><input name=time type=text size=4></td><td>Максимум вопросов в тесте:</td><td><input name=qmax type=text size=3></td></tr>

<tr bgcolor=#F4ECD4><td>Видимость теста:</td><td><input name=vis type=checkbox></td><td>Максимум ответов на вопрос:</td><td><input name=amax type=text size=2></td></tr>

<tr><td colspan=4 align=center><input name=nitem type=submit value="Добавить"></td></tr>

</form>

</table>



</td></tr>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><strong>Перечень тестирований:</strong></p></td></tr>

<tr><td>



<table cellpadding='3'>

<?

$SQL = "SELECT * FROM tests_items";

$r = mysql_query($SQL);

while ($d=mysql_fetch_array($r)) {

?>

<tr bgcolor=#F4ECD4><td><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5' onclick="javascript:if(l<?=$d['id']?>.style.display=='none') l<?=$d['id']?>.style.display=''; else l<?=$d['id']?>.style.display='none';" style="cursor: hand"> <a href="?m=edit&i=<?=$d['id']?>"><?=$d['name']?></a> [<a href="javascript: edit_item('<?=$d['name']?>',<?=$d['time']?>,<?=$d['id']?>,<?=$d['qmax']?>,<?=$d['amax']?>,<?=$d['visible']?>)">E</a>] [<a href="?m=edit&iid=<?=$d['id']?>&del=1">X</a>]

<div id="l<?=$d['id']?>" style="display:none">

Задается вопросов: <b><?=$d['qmax']?></b><br>

Вариантов ответов: <b><?=$d['amax']?></b><br>

Время на тест: <b><?=$d['time']?> мин</b><br>

Видимость: <b><?=($d['visible']?'<font color=green>Да</font>':'<font color=red>Нет</font>')?></b>

</div>

</td></tr>

<?

}

?>

<tr bgcolor=#F4ECD4><td><a href="?m=edit&i=0">Вопросы вне тестирований</a></td></tr>

</table>



</td></tr>

</table>



<?

}

?>



<? } else if ($_REQUEST['m']=='manager' && ((abs(AccessLevel) & AuthTestsAdmin) || AuthUserGroup==100)) {



if ($_REQUEST['detail']) {



$SQL = "SELECT * FROM tests_results WHERE id='".$_REQUEST['detail']."'";

$res = mysql_fetch_array(mysql_query($SQL));

$questions = explode('|', $res['questions']);

$answers = explode('|', $res['answers']);



$SQL = "SELECT item_id, user_id FROM tests_results WHERE id='".$_REQUEST['detail']."'";

list($iid, $uid) = mysql_fetch_array(mysql_query($SQL));

$SQL = "SELECT name, qmax FROM tests_items WHERE id='".$iid."'";

list($iname, $qmax) = mysql_fetch_array(mysql_query($SQL));

$SQL = "SELECT COUNT(*) AS qmax FROM tests_text WHERE to_item='".$iid."'";

$d = mysql_fetch_array(mysql_query($SQL));

if ($qmax>$d['qmax']) $qmax = $d['qmax'];

$SQL = "SELECT user_name FROM site_users WHERE id='".$uid."'";

list($uname) = mysql_fetch_array(mysql_query($SQL));

?>



| <a href=/>Домой</a> | <a href=?m=manager&pers=<?=$_REQUEST['pers']?>&t=<?=$_REQUEST['t']?>>Назад</a> |



<table width=100% border='0' cellspacing='3' cellpadding='3'>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><strong>Экзаменуемый: <?=$uname?></strong></p></td></tr>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><strong>Тестирование: <?=$iname?> (вопросов - <?=$qmax?>)</strong></p></td></tr>

<tr><td>



<?

if ($res['questions']) {



$l = 0;

foreach ($questions as $question_id) {

$qanswers = explode(',', $answers[$l]);

$SQL = "SELECT id, test_text, ball FROM tests_text WHERE id='".$question_id."'";

$d = mysql_fetch_array(mysql_query($SQL));

$question = str_replace("\015\012", "<br>", $d['test_text']);

$l++;

?>

<table width=100% cellpadding='3' style="BORDER: 1px #957850 solid; margin-top: 4pt;">

<tr><td background='/i/bgr-grid-sand1.gif'><font color=red><b><?=$l?>.</b></font> <?=$question?> <font color=green>(Баллы за вопрос: <b><?=$d['ball']?></b>)</font>

<?

$SQL = "SELECT id, test_text, ball FROM tests_text WHERE to_question=".$d['id']." ORDER BY ball, id";

$rr = mysql_query($SQL);

while ($dd = mysql_fetch_array($rr)) {

$answer = str_replace("\015\012", "<br>", $dd['test_text']);

	if (in_array($dd['id'], $qanswers)) {

?>

<tr><td<?=($dd['ball']?" background='/i/bgr-grid-sand4.gif'":" background='/i/bgr-grid-sand3.gif'")?>><li> <?=$answer?><?=($dd['ball']?" <font color=green>(Баллы: <b>".$dd['ball']."</b>)</font>":"")?></td></tr>

<?

	} elseif ($dd['ball']) {

?>

<tr><td background='/i/bgr-grid-sand.gif'><li> <?=$answer?> <font color=green>(Баллы: <b><?=$dd['ball']?></b>)</font></td></tr>

<?

	}

}

?>

</table>

<?

}

} else {

echo "<div align=center><b><font color=red>Экзаменуемый не ответил ни на один вопрос!</font></b></div>\n";

}

?>



</td></tr>

</table>



<? } else {

$SQL = "SELECT t1.id AS id, t2.time AS time FROM tests_results AS t1 INNER JOIN tests_items AS t2 ON t1.item_id=t2.id WHERE t1.start_time+t2.time<UNIX_TIMESTAMP() AND t1.finish_time=0";

$r = mysql_query($SQL);

while ($d = mysql_fetch_array($r)) {

	mysql_query("UPDATE tests_results SET current_question=0, finish_time=start_time+60*".$d['time']." WHERE id='".$d['id']."'");

}

?>



| <a href=/>Домой</a> | <a href=?>Назад</a> |



<table border='0' cellspacing='3' cellpadding='3'>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><strong>Результаты тестирований:</strong></p></td></tr>

<tr><td>



<table cellpadding='3'>

<form name="new_item" action="?m=manager" method="POST">

<tr bgcolor=#F4ECD4><td>Персонаж: </td><td><input name=pers type=text size=30></td></tr>

<tr bgcolor=#F4ECD4><td>Результаты:</td><td><select name=t>

<option value=0<?=($_REQUEST['t']==0?" selected":"")?>>Непроверенные</option>

<option value=1<?=($_REQUEST['t']==1?" selected":"")?>>Проверенные</option>

</select></td></tr>

<tr><td colspan=4 align=center><input name=show type=submit value="Показать"></td></tr>

</form>

</table>

<? if (isset($_REQUEST['t'])) { ?>

</td></tr>

<tr><td height='30' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><strong>Найденные результаты:</strong></p></td></tr>

<tr><td>



<form action="?m=manager&t=<?=$_REQUEST['t']?>" method=POST>

<table cellpadding='3'>

<tr bgcolor=#F4ECD4><? if (!($_REQUEST['t'])) { ?><td>&nbsp;</td><? } ?><td height=20><b>Имя</b></td><td width=200><b>Тестирование</b></td><td><b>Затраченное время</b></td><td><b>Набранные баллы</b></td><td><b>Кол-во неверных ответов</b></td><td><b>Кол-во отвеченных вопросов</b></td></tr>

<?

if (!($_REQUEST['t'])) {

	$SQL = "SELECT * FROM tests_results WHERE `check`='".$_REQUEST['t']."' AND finish_time>0 ORDER BY user_id, item_id";

	$r = mysql_query($SQL);

	while ($d = mysql_fetch_array($r)) {

		if ($_REQUEST['t'.$d['id']]) {

			mysql_query("UPDATE tests_results SET `check`='1' WHERE id='".$d['id']."'");

		}

	}

}

if ($_REQUEST['pers']) {

	$SQL = "SELECT id FROM site_users WHERE user_name='".$_REQUEST['pers']."'";

	list($uid) = mysql_fetch_array(mysql_query($SQL));

}

$SQL = "SELECT * FROM tests_results WHERE ".($uid?"user_id='".$uid."' AND ":"")."`check`='".$_REQUEST['t']."' AND finish_time>0 ORDER BY user_id, item_id";

$r = mysql_query($SQL);

$np=0;

while ($d = mysql_fetch_array($r)) {

	if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

	$SQL = "SELECT user_name FROM site_users WHERE id='".$d['user_id']."'";

	list($uname) = mysql_fetch_array(mysql_query($SQL));

	$SQL = "SELECT name, qmax FROM tests_items WHERE id='".$d['item_id']."'";

	list($iname, $qmax) = mysql_fetch_array(mysql_query($SQL));

	list($cballs,$balls,$wrong,$all) = explode('/', $d['result']);

?>

<tr><? if (!($_REQUEST['t'])) { ?><td <?=$bg?>><input type=checkbox name=t<?=$d['id']?> value=1></td><? } ?>

<td <?=$bg?>><b><?=$uname?></b></td>

<td <?=$bg?>><a href="?m=manager&pers=<?=$_REQUEST['pers']?>&t=<?=$_REQUEST['t']?>&detail=<?=$d['id']?>"><?=$iname?></a> (<?='вопросов: '.$qmax?>)</td>

<td <?=$bg?> align=center><?=round(($d['finish_time']-$d['start_time'])/60, 2).' мин'?></td>

<td <?=$bg?> align=center><?=($cballs?$cballs:'0').' из '.($balls?$balls:'0').''?></td>

<td <?=$bg?> align=center><?=($wrong?$wrong:'0')?></td>

<td <?=$bg?> align=center><?=($all?$all:'0')?></td></tr>

<?

}

?>

</table>

<div align=center><input name=show type=submit value=Проверить></div>

</form>

<? } ?>



</td></tr>

</table>

<? } ?>

<? } else if ($_REQUEST['m']=='test' && $_REQUEST['t']) { ?>

<?

if (isset($_REQUEST['aform'])) {

	$SQL = "SELECT tr.id AS id,
			tr.current_question AS current_question, 
			tr.start_time AS start_time,
			ti.time AS time,
			tr.questions AS questions, 
			tr.answers AS answers, 
			tr.result AS result 
			FROM tests_results AS tr LEFT JOIN tests_items AS ti ON tr.item_id = ti.id 
			WHERE item_id='".$_REQUEST['t']."' 
			AND user_id='".AuthUserId."' 
			AND `check`='0'";

	list($uid, $cq, $stime, $time, $questions, $qanswers, $result) = mysql_fetch_array(mysql_query($SQL));

	if (($stime + $time * 60) > time()) {

	
		$SQL = "SELECT amax FROM tests_items WHERE id='".$_REQUEST['t']."'";

		list($amax) = mysql_fetch_array(mysql_query($SQL));

		$answers = array();

		for ($i=1; $i<=$amax; $i++) {

			if ($_REQUEST['answer'.$i]) {

				$answers[] = $_REQUEST['answer'.$i];

			}

		}

		if (sizeof($answers)) {
			

			list($correct, $all, $inc, $qcount) = explode("/", $result);

			$qcount++;

			$SQL = "SELECT ball FROM tests_text WHERE to_item='".$_REQUEST['t']."' AND id='".$cq."'";

			list($qball) = mysql_fetch_array(mysql_query($SQL));

			$all += $qball;

			$SQL = "SELECT id, ball FROM tests_text WHERE to_item='".$_REQUEST['t']."' AND to_question='".$cq."'";

			$r = mysql_query($SQL);

			while(list($id, $ball) = mysql_fetch_array($r)) {

				if (in_array($id, $answers)) {

					if ($ball) {

						$correct += $ball;

					} else {

						$inc++;

					}

				}

			}

			$SQL = "UPDATE tests_results SET current_question=0, finish_time =".time().",questions='".($questions!=''?$questions.'|'.$cq:$cq)."', answers='".($qanswers!=''?$qanswers.'|'.implode(',',$answers):implode(',',$answers))."', result='".$correct.'/'.$all.'/'.$inc.'/'.$qcount."' WHERE id='".$uid."'";

			mysql_query($SQL);

		}

	}

}



if (list($itext, $qmax, $amax, $itime) = mysql_fetch_array(mysql_query("SELECT name, qmax, amax, time FROM tests_items WHERE id='".$_REQUEST['t']."' AND visible='1'"))) {

	if (!(list($uid, $cq, $stime, $ftime, $q, $res) = mysql_fetch_array(mysql_query("SELECT id, current_question, start_time, finish_time, questions, result FROM tests_results WHERE user_id='".AuthUserId."' AND item_id='".$_REQUEST['t']."' AND `check`='0'")))) {

		$SQL = "INSERT INTO tests_results VALUES('', '".AuthUserId."', '".$_REQUEST['t']."', '', '".time()."', '', '', '', '','0')";

		mysql_query($SQL);

		$uid = mysql_insert_id();

		$cq = 0;

		$stime = time();

	}

	if ($cq && $stime+60*$itime-time()>=0) {

		$SQL = "SELECT * FROM tests_text WHERE id='".$cq."'";

		$question = mysql_fetch_array(mysql_query($SQL));

	} else {

		$questions1 = array();

		$SQL = "SELECT id FROM tests_text WHERE to_item='".$_REQUEST['t']."' AND to_question=0";

		$r = mysql_query($SQL);

		while (list($qid) = mysql_fetch_array($r)) $questions1[] = $qid;

		$questions2 = array();

		$questions2 = explode('|',$q);

		$questions = array_diff($questions1, $questions2);

		shuffle($questions);

		$SQL = "SELECT * FROM tests_text WHERE id='".$questions[0]."'";

		$question = mysql_fetch_array(mysql_query($SQL));

		if (sizeof($questions)==0 || sizeof($questions2)==$qmax || $stime+60*$itime-time()<0) {

			$endtest = 1;

		} else {

			$SQL = "UPDATE tests_results SET current_question='".$question['id']."' WHERE id='".$uid."'";

			mysql_query($SQL);

		}

	}

}

?>



<script language="javascript">

	<? if (sizeof($questions)==0 || sizeof($questions2)==$qmax || $stime+60*$itime-time()<0) { ?>

	function showTimer() {}

	<? } else { ?>

	var EndTime = <?=($stime+60*$itime-time())?>;

	function showTimer() {

		if (EndTime>0) EndTime--;

		var sec = EndTime - Math.floor(EndTime/60)*60;

		document.all("timer").innerHTML='<b>Осталось: '+Math.floor(EndTime/60)+' мин '+sec+' сек, вопросов: <?=$qmax-sizeof($questions2)?></b>';

		timerId=setTimeout("showTimer()",1000);

	}

	<? } ?>

</script>



| <a href=/>Домой</a> | <a href=?>Назад</a> |

<div id=timer></div>



<table border='0' cellspacing='3' cellpadding='3' style="margin-bottom: 4pt;">

<tr><td background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' hspace='1'><strong><?=$itext?></strong></p></td></tr>

<tr><td>

<? if ($endtest) {

	if (!($ftime)) {

		$SQL = "UPDATE tests_results SET finish_time='".($stime+60*$itime-time()>0?time():$stime+60*$itime)."', current_question=0 WHERE id='".$uid."'";

		mysql_query($SQL);

	}

	list($correct, $all, $inc, $qcount) = explode("/", $res);

	$SQL = "SELECT COUNT(*) AS qmax FROM tests_text WHERE to_item='".$_REQUEST['t']."'";

	$d = mysql_fetch_array(mysql_query($SQL));

	if ($qmax>$d['qmax']) $qmax = $d['qmax'];

?>

<b>Результат:</b><br>

Набрано балов: <b><?=($correct?$correct:'0').'/'.($all?$all:'0')?></b><br>

Неправильных ответов: <b><?=($inc?$inc:'0')?></b><br>

Отвечено вопросов: <b><?=($qcount?$qcount:'0')?></b><br>

Вопросов в тесте: <b><?=$qmax?></b>

<? } else { ?>

<form name=answer action="?m=test&t=<?=$_REQUEST['t']?>" method="POST">

<table width=100% border="0" cellspacing="0" bgcolor="#f2f0f0" cellpadding="10" style="BORDER: 1px #957850 solid;">

<tr><td><?=str_replace("\015\012", "<br>", $question['test_text'])?></td></tr>

</table>

</td></tr>

<tr><td>

<table width=100% bgcolor="#f2f0f0" cellpadding=5 cellspacing=0>

<?

$SQL = "SELECT id, test_text, ball FROM tests_text WHERE to_question='".$question['id']."'";

$r = mysql_query($SQL);

$incorrect = array();

$correct = array();

$answers = array();

while ($d = mysql_fetch_array($r)) {

	if ($d['ball']) {

		$correct[] = $d['id'];

	} else {

		$incorrect[] = $d['id'];

	}

	$answers[$d['id']] = $d['test_text'];

}

shuffle($incorrect);

if (sizeof($correct)+sizeof($incorrect)>$amax) {

	$icount = 0;

	while (sizeof($correct)<$amax) {

		$correct[] = $incorrect[$icount];

		$icount++;

	}

} else {

	$correct = array_merge($correct, $incorrect);

}

shuffle($correct);

$counter = 0;

foreach ($correct as $id) {

if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

$counter++;

?>

<tr><td <?=$bg?>><b><?=$counter?>.</b> <input type=checkbox name=answer<?=$counter?> value=<?=$id?>>&nbsp; <?=str_replace("\015\012", "<br>", $answers[$id])?></td></tr>

<?

}

?>

</table>

<input name=aform type=submit value=Ответить>

</form>

<? } ?>

</td></tr>

</table>



<? } else { ?>



| <a href=/>Домой</a> |



<table border='0' cellspacing='3' cellpadding='3'>

<? if ((abs(AccessLevel) & AuthTestsAdmin) || AuthUserGroup==100) { ?>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><a href="?m=edit">Редактор тестирований</a></p></td></tr>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><a href="?m=manager">Результаты тестирований</a></p></td></tr>

<?

}

$SQL = "SELECT * FROM tests_items WHERE visible='1'";

$r = mysql_query($SQL);

while ($d = mysql_fetch_array($r)) {

?>



<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><a href="?m=test&t=<?=$d['id']?>"><?=$d['name']?></a></p></td></tr>

<? } ?>



</table>

<? } ?>
	</td></tr>
	</table>
	</td></tr>
	</table>
	</td></tr>
	</table>
	</td></tr>
	</table>
	</td></tr>
	</table>
	</td></tr>
	</table>
</body>

<?

} else echo $mess['AccessDenied'];

?>