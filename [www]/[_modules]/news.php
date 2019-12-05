<?

	function randomQuote ($file) {
		$f = file($file);
		$k = array_rand($f);
		$q = $f[$k];
		echo ParseNews3($q);
	}

	$f = file('warez/tips.txt');
	echo '<script>
	 var quotates = new Array();
	';
	$tipsnumber = 0;
	foreach ($f as $line => $text) {
		$tipsnumber++;
		echo "\nquotates[".$line.']="'.ParseNews3(str_replace("\r",'',str_replace("\n",'',$text))).'";';
	}
	$tipsnumber = $tipsnumber-1;
	echo "\nvar tipsnum = ".$tipsnumber.";\n";
?>
var oldrnd = 0;
function showTips(action) {
	var returntext = "";
	if (action == 0) {
		var randnum = Math.floor(Math.random()*tipsnum);
		while (randnum == oldrnd) {
			var randnum = Math.floor(Math.random()*(tipsnum+1));
		}
		oldrnd = randnum;
		returntext=quotates[randnum] + "<br>";;
	}
	if (action == 1) {
		var randnum = oldrnd+1;
		if (randnum > tipsnum) {
			var randnum = 0;
		}
		oldrnd = randnum;
		returntext=quotates[randnum] + "<br>";;
	}
	if (action == 2) {
		var returntext="<ul>";
		for (var i in quotates) returntext = returntext + "<li>" + quotates[i] + "<br><br>";
		var returntext=returntext+"</ul>";
	}
	returntext = returntext + '<div align="right"><hr size=1><a href="javascript:{}" onClick="showTips(1)">Ещё совет</a> || <a href="javascript:{}" onClick="showTips(2)">Все советы</a></div>';
	document.getElementById('kindausefultip').innerHTML = returntext;
}
</script>

<?
///////////////////////////////////////////////
	echo "<table style='MARGIN-BOTTOM:2px; padding-bottom:0px;' width='100%' border='1' bordercolor='#a0905b' cellspacing='3' cellpadding='5'><tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><strong>«Новостная лента Полиции бесплатной онлайн игры TimeZero. Данный сайт не является официальным сайтом игры»</strong><div><A HREF=\"http://www.timezero.ru/\" TARGET=\"_blank\">Мир TimeZero</A> - это планета, пережившая ядерную войну. Ужаснейшая из пережитых Человечеством катастроф практически полностью опустошила Землю, а высокий уровень радиации поспособствовал появлению бесконечного множества мутантов, ставших для населения настоящим кошмаром. Паника окутала города Нового мира. Часть населения бросила все силы на борьбу с мутантами. Другая часть - на истребление себе подобных.<BR>В условиях постъядерной анархии и произвола Полиция TimeZero стала одним из немногих оплотов порядка и соблюдения законности. Основная ее задача - борьба с преступностью и контроль над соблюдением Конституции <A HREF=\"http://www.timezero.ru/\" TARGET=\"_blank\">мира TimeZero</A>.</div></p></td></tr></table>";
///////////////////////////////////////////////
	echo "<table style='padding-bottom:0px;' width='100%' border='1' bordercolor='#a0905b' cellspacing='3' cellpadding='5'><tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><strong>[Совет] </strong><div id='kindausefultip'><script>showTips(0)</script></div></p></td></tr></table>";

?>
<!--<h1 style="padding-top: 0px">Новости</h1>-->
<?php
	//error_reporting(E_ALL);
	extract($_REQUEST);
	$vstr[0] = '';
	$vstr[1] = '<strong style="color: green">[внутренняя police] </strong>';
	$vstr[2] = '<strong style="color: blue">[внутренняя] </strong>';
	if(abs(AccessLevel) & AccesInnerNews) {
		$v = '';
		$v2 = '';
/*
	} elseif (AuthUserClan=='Military Police') {
		$v = ' AND (n.our_news=\'0\' OR n.our_news=\'2\')';
		$v2 = ' AND (our_news=\'0\' OR our_news=\'2\')';
*/
	} else {
		$v = ' AND n.our_news=\'0\'';
		$v2 = ' AND our_news=\'0\'';
	}

	if((AuthStatus==1 && AuthUserGroup>1) || (abs(AccessLevel) & AccessNewsEditor)) {
		if(@$_REQUEST['IdNews']) {
			switch(@$_REQUEST['do']) {
				case 'delete': $SQLquery = 'DELETE FROM `news` WHERE `id`=\''.intval($_REQUEST['IdNews']).'\''; $StatusQuery = '<div class=green>Новость <b>ID:'.$_REQUEST['IdNews'].'</b> удалена</div>'; break;
				case 'attach': $SQLquery = 'UPDATE `news` SET `is_attached`=\'1\' WHERE id=\''.intval($_REQUEST['IdNews']).'\''; $StatusQuery = '<div class=green>Новость <b>ID:'.$_REQUEST['IdNews'].'</b> закреплена</div>'; break;
				case 'hide': $SQLquery = 'UPDATE `news` SET `is_visible`=\'0\' WHERE id=\''.intval($_REQUEST['IdNews']).'\''; $StatusQuery = '<div class=green>Новость <b>ID:'.$_REQUEST['IdNews'].'</b> снята с ленты</div>'; break;
				case 'unattach': $SQLquery = 'UPDATE `news` SET `is_attached`=\'0\' WHERE id=\''.intval($_REQUEST['IdNews']).'\''; $StatusQuery = '<div class=green>Новость <b>ID:'.$_REQUEST['IdNews'].'</b> откреплена</div>'; break;
			}
		}

		mysql_query($SQLquery);
		echo $StatusQuery;
	}

	if ($action == 'search') {
		//error_reporting(E_ALL);
		//global $news_search_url, $news_search_where, $news_search_start, $news_search, $news_search_author, $news_search_words, $news_search_type;
		$news_search_where = 'WHERE 1 ';

		if ($news_search_start) {
			$news_search_url = '&news_search_start=1';

			if ($news_search) {
				$news_search_url .= '&news_search_author='.$news_search_author.'&news_search_words='.$news_search_words.'&news_search_type='.$news_search_type.'&news_search=Search';

				if (empty($news_search_author) && empty($news_search_words)) {
					echo "<center><font color=red>Введите условия поиска</font></center><p>\n";
				} else {
					$news_search_where = 'WHERE ';
					$nsw = 'WHERE ';

					if (!empty($news_search_author)) {
						//$news_search_author_split = split (' ', $news_search_author);

						//$count_split = count($news_search_author_split);
						//for ($i = 0; $i < $count_split; $i++) {
						$query = "SELECT `id` FROM `site_users` WHERE `user_name` = '".$news_search_author."' LIMIT 1;";
						$rslt547 = mysql_query($query);
						$rslt548 = mysql_fetch_array($rslt547);
							$news_search_where .= ' n.poster_id = \''.$rslt548['id'].'\'';
							$nsw .= ' poster_id = \''.$rslt548['id'].'\'';
						//	if ($i < ($count_split-1)) {
						//		$news_search_where .= ' AND';
						//		$nsw .= ' AND';
						//	}
						//}
						if (!empty($news_search_words)) {
							$news_search_where .= ' AND';
							$nsw .= ' AND';
						}
					}

					if (!empty($news_search_words)) {
						$news_search_words_split = split (' ', $news_search_words);

						$count_split = count($news_search_words_split);
						for ($i = 0; $i < $count_split; $i++) {

							$news_search_where .= ' (n.news_text LIKE \'%'.$news_search_words_split[$i].'%\' OR n.news_title LIKE \'%'.$news_search_words_split[$i].'%\')';
							$nsw .= ' (news_text LIKE \'%'.$news_search_words_split[$i].'%\' OR news_title LIKE \'%'.$news_search_words_split[$i].'%\')';
							if ($i < ($count_split-1)) {
								if ($news_search_type) {
									$news_search_where .= ' AND';
									$nsw .= ' AND';
								} else {
									$news_search_where .= ' OR';
									$nsw .= ' OR';
								}
							}
						}
					}
				}
			}
		}

		echo '
	<center>
	<form method="GET" action="">
	<input type="hidden" name="news_search_start" value="1">
	<input type="hidden" name="action" value="search">
	<br><b>Поиск по Архиву</b><br><br>
	<table>
	 <tr>
	  <td align="right" class="small">Автор:</td>
	  <td><input type="text" class=frm name="news_search_author" size="20" value="'.strip_tags(urldecode($news_search_author)).'"></td>
	 </tr>
	 <tr>
	  <td align="right" class="small">Текст:</td>
	  <td><input type="text" class=frm name="news_search_words" size="20" value="'.@strip_tags(urldecode($news_search_words)).'"></td>
	 </tr>
	</table>
	<font size="2">Должен содержать все слова: </font><input type="checkbox" name="news_search_type" ';
		if ($news_search_type) {
			echo 'checked ';
		}
		echo 'value="1">
	<center><br><br>
	<input type="submit" class=frm value="Поиск" style="WIDTH: 100px; CURSOR: hand" name="news_search">
	</form>';
	} else {
	#    NEWS TOP BLOCK
	# begin output of attached
		//$SQL="SELECT n.*, u.id AS PosterId, u.user_name AS PosterName, u.clan AS PosterClan FROM news n LEFT JOIN site_users u ON n.poster_id=u.id WHERE n.is_attached=1 AND n.is_visible=1".$v." ORDER BY n.news_date DESC";
		if ($_REQUEST['p'] < 2)
			{
				$SQL = 'SELECT n.*, COUNT(id_news) as cnt FROM (SELECT n.*, u.id AS PosterId, u.user_name AS PosterName, u.clan AS PosterClan FROM news n LEFT JOIN site_users u ON n.poster_id=u.id WHERE n.is_attached=1 AND n.is_visible=1'.$v.' ORDER BY n.news_date DESC) AS n LEFT JOIN comments ON n.id=id_news GROUP BY n.id ORDER BY n.news_date DESC';
				$r = mysql_query($SQL);
				while($d=mysql_fetch_assoc($r)) {
				    echo '
						<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr>
						<td height="20" background="i/bgr-grid-sand.gif"><p><img src="i/bullet-red-01.gif" width="18" height="11" hspace="5"><strong>[прикреплено] </strong> '.$vstr[$d['our_news']].'<span class="top-header">'.stripslashes($d['news_title']).'</span></p></td>
						</tr><tr><td>
				    ';

					if ($d['markup'] == 0) {
						ParseNews($d['news_text'],$d['allow_tags']);
						$markup_vers = '';
					} elseif ($d['markup'] == 1) {
						ParseNews2($d['news_text']);
						$markup_vers = '2';
					}

					echo '</td></tr><tr>
						<td height=20 align="right" background="i/bgr-grid-sand1.gif">
						Автор: <strong>'.GetUser($d['PosterId'],$d['PosterName'],AuthUserGroup).'</strong> /
					';

					if(AuthUserGroup>1 || (abs(AccessLevel) & AccessNewsEditor)) {
						$SQL2 = 'SELECT id, clan, user_name FROM `site_users` WHERE `id`=\''.$d['checker_id'].'\'';
						$rr=mysql_query($SQL2);
						$dd=mysql_fetch_assoc($rr);

						echo '
				            подтверждена: '.GetUser($dd['id'],$dd['user_name'],AuthUserGroup).' /
				            <a href="?act=news_edit'.$markup_vers.'&IdNews='.$d['id'].'">редактировать</a> /
				            <a href="?act=news&IdNews='.$d['id'].'&do=unattach">открепить</a> /
				            <a onclick="if(confirm(\'Удалить новость из базы?\')){top.location=\'?act=news&IdNews='.$d['id'].'&do=delete\'}" href="javascript:{}">удалить</a> /
				       ';
					}

				//	$com=mysql_fetch_array(mysql_query("SELECT count(id) as cnt FROM comments WHERE id_news='".$d['id']."'"));

					echo '
						<a href="?act=n_comm&IdNews='.$d['id'].'">Комментарии: '.$d['cnt'].'</a> </td>
						</tr></table>
					';
			}
		}
	// end of output of attached
	}

	if ($action == 'search')
		{
			//echo ($news_search_where.'<br>');
			$SQL2 = 'SELECT COUNT(id) as cnt FROM `news` '.$nsw.' AND `is_visible` = \'1\''.$v2;
			//echo ($SQL2);
		}
	else
		{
			$SQL2 = 'SELECT COUNT(id) as cnt FROM `news` WHERE `is_visible` = \'1\''.$v2;
		}
	if ($action != 'search') $news_search_where = 'WHERE n.is_attached<>1';
	//$SQL="SELECT count(c.id) AS cnt, n.*, u.id AS PosterId, u.user_name AS PosterName, u.clan AS PosterClan FROM news n LEFT JOIN site_users u ON n.poster_id=u.id LEFT JOIN comments c ON c.id_news=n.id WHERE n.is_attached<>1 AND n.is_visible=1 GROUP BY c.id_news ORDER BY n.news_date DESC LIMIT 0,10";
	$r = mysql_query($SQL2);// or die(mysql_error());
	$rs = mysql_fetch_assoc($r);
	//echo($rs['cnt']);
	//$pages=ceil(mysql_num_rows($r)/$NewsPP);
//	echo ("<!--pages_num: ".$rs['cnt']."-->");
	$pages=ceil($rs['cnt']/$NewsPP) - 1;
	if($_REQUEST['p']>0) $p=$_REQUEST['p'];
	else $p=1;


	$LimitParam=$p*$NewsPP-$NewsPP;

	//$SQL="SELECT n.*, u.id AS PosterId, u.user_name AS PosterName, u.clan AS PosterClan FROM news n LEFT JOIN site_users u ON n.poster_id=u.id $news_search_where AND n.is_visible=1".$v." ORDER BY n.news_date DESC LIMIT ".$LimitParam.", ".$NewsPP.";";
	$SQL = 'SELECT n.*, COUNT(id_news) as cnt FROM (SELECT n.*, u.id AS PosterId, u.user_name AS PosterName, u.clan AS PosterClan FROM news n LEFT JOIN site_users u ON n.poster_id=u.id '.$news_search_where.' AND n.is_visible=1'.$v.' ORDER BY n.news_date DESC LIMIT '.$LimitParam.', '.$NewsPP.') AS n LEFT JOIN comments ON n.id=id_news GROUP BY n.id ORDER BY n.news_date DESC';
	$r = mysql_query($SQL);
	while($d = mysql_fetch_assoc($r)) {

		if ($d['is_visible'] == 1) {
			echo '
		<table width="100%" border="0" cellspacing="3" cellpadding="3"><tr>
		<td height="20" background="i/bgr-grid-sand.gif"><p><img src="i/bullet-red-01.gif" width="18" height="11" hspace="5"><strong>['.date("d.m.Y - H:i", $d['news_date']).'] '.$vstr[$d['our_news']].'</strong> <span class="top-header">'.strip_tags(stripslashes($d['news_title'])).'</span></p></td>
		</tr><tr><td>
    ';

			if ($d['markup'] == 0) {
				ParseNews($d['news_text'],$d['allow_tags']);
				$markup_vers = '';
			} elseif ($d['markup'] == 1) {
				ParseNews2($d['news_text']);
				$markup_vers = '2';
			}

			echo '
			</td></tr><tr>
			<td height=20 align="right" background="i/bgr-grid-sand1.gif">
			Автор: <strong>'.GetUser($d['PosterId'],$d['PosterName'],AuthUserGroup).'</strong> /
		';

			if(AuthUserGroup>1 || (abs(AccessLevel) & AccessNewsEditor)) {
				$SQL2 = 'SELECT id,clan,user_name FROM site_users WHERE id=\''.$d['checker_id'].'\'';
				$rr=mysql_query($SQL2);
				$dd=mysql_fetch_assoc($rr);

				echo '
	            подтверждена: '.GetUser($dd['id'],$dd['user_name'],AuthUserGroup).' /
	            <a href="?act=news_edit'.$markup_vers.'&IdNews='.$d['id'].'">редактировать</a> /
	            <a href="?act=news&IdNews='.$d['id'].'&do=attach">прикрепить</a> /
	            <a href="?act=news&IdNews='.$d['id'].'&do=hide">спрятать</a> /
	            <a onclick="if(confirm(\'Удалить новость из базы?\')){top.location=\'?act=news&IdNews='.$d['id'].'&do=delete\'}" href="javascript:{}">удалить</a> /
	       ';
			}

	//		$com=mysql_fetch_array(mysql_query("SELECT count(id) as cnt FROM comments WHERE id_news='".$d['id']."'"));

			echo '
			<a href="?act=n_comm&IdNews='.$d['id'].'">Комментарии: '.$d['cnt'].'</a> </td>
			</tr></table>
		';
		}
	}

	if (!$action) {
		echo '<br><p align=right>Страницы: <b>';
		ShowPages($p, $pages, 15, 'act=news');
		echo '</b></p>';
	} else {
		echo '<br><p align=right>Страницы: <b>';
		if ($news_search_type) {
			$pstr = 'act=news_search&news_search_type=1&news_search_start=1&action=search&news_search_author='.strip_tags(urldecode($news_search_author)).'&news_search_words='.strip_tags(urldecode($news_search_words)).'&news_search=%CF%EE%E8%F1%EA';
		} else {
			$pstr = 'act=news_search&news_search_start=1&action=search&news_search_author='.strip_tags(urldecode($news_search_author)).'&news_search_words='.strip_tags(urldecode($news_search_words)).'&news_search=%CF%EE%E8%F1%EA';
		}

		$pstr = str_replace(' ', "%20", $pstr);
		ShowPages($p,$pages,15,$pstr);

	//  ShowPages($p,$pages,5,"act=news_search&news_search_start=1&action=search&news_search_author=$news_search_author&news_search_words=$news_search_words&news_search=%CF%EE%E8%F1%EA");

		echo '</b></p>';
	//  echo ("<br><br>".$pages."<br><br>".$pstr);
	}
?>