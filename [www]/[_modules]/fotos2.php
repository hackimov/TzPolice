<?
	$fotos_nicksperpage = 25;
?>
<h1>Фотогалерея</h1>
<SCRIPT src='_modules/xhr_js.js'></SCRIPT>
<SCRIPT src='_modules/news_js.js'></SCRIPT>
<script language="javascript">
function str_replace(search, replace, subject) {
    var f = search, r = replace, s = subject;
    var ra = r instanceof Array, sa = s instanceof Array, f = [].concat(f), r = [].concat(r), i = (s = [].concat(s)).length;
 
    while (j = 0, i--) {
        if (s[i]) {
            while (s[i] = (s[i]+'').split(f[j]).join(ra ? r[j] || "" : r[0]), ++j in f){};
        }
    };
 
    return sa ? s : s[0];
}
function loadfoto(nick,foto,c){
	if(c==1){closecomments();}
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('foto_area').innerHTML = "Loading...";
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
					document.getElementById('foto_link').innerHTML = "Ссылка на фотографии: <a href=\"http://www.tzpolice.ru/foto-"+nick+"\">http://www.tzpolice.ru/foto-"+nick+"</a>";
					document.getElementById('foto_area').innerHTML = req.responseText;
				}
			}
	}
	req.caching = false;
	req.open('POST', '_modules/backends/fotos_load.php', true);
	req.send({ nick: nick, f: foto });
}

function opencomments(id,p){
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('comment_area').innerHTML = "Loading...";
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
					document.getElementById('comment_area').innerHTML = req.responseText;
				}
			}
	}
	req.caching = false;
	req.open('POST', '_modules/backends/foto_comments.php', true);
	req.send({DataId: id, p: p});
}
function closecomments(){
document.getElementById('comment_area').innerHTML = "";
}
function newcomment(){
	var a,b,c,d,e;
	//a=document.getElementById('act').value;
	//b=document.getElementById('do').value;
	c=document.getElementById('DataId').value;
	d=document.getElementById('p').value;
	e=document.getElementById('NewsText').value;
	e=str_replace('+','&#43;',e);
	//window.alert(e);
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('comment_area').innerHTML = "Loading...";
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
					document.getElementById('comment_area').innerHTML = req.responseText;
				}
			}
	}
	req.caching = false;
	req.open('POST', '_modules/backends/foto_comments.php', true);
	req.send({act: 'fotos_comments', doo: 'add', DataId: c, p: d, NewsText:e});
	//opencomments(c);
}
</script>
<?php
	error_reporting(0);
	extract($_REQUEST);
	$p = $_REQUEST['p'];
//	$nick = str_replace("%20", " ", $_REQUEST['nick']);
	$nick = strip_tags(urldecode($_REQUEST['nick']));
	if (strlen($nick) > 16) { $nick = substr($nick,0,16); }
	
	$f = $_REQUEST['f'];
	
	if ($_REQUEST['make_vote'] && AuthStatus==1 && AuthUserName!='' && $_REQUEST['vote_radio'] < 6 && $_REQUEST['vote_radio'] > 0 && $_REQUEST['vote_nick']) {
		$result1 = mysql_query('SELECT * FROM `fotos_users` WHERE `nick`=\''.$_REQUEST['vote_nick'].'\' LIMIT 1;');
		if (@mysql_num_rows($result1) > 0) {
			$row1 = mysql_fetch_assoc($result1);
			if (!strstr($row1['voted'], AuthUserName)) {
				$temp_sum = $row1['points_sum'] + $_REQUEST['vote_radio'];
				$temp_num = $row1['points_num'] + 1;
				$temp_rank = round($temp_sum / $temp_num,2);
				$temp_voted = $row1['voted'].' '.AuthUserName.' ';
				$result1 = mysql_query('UPDATE `fotos_users` SET `points_sum`=\''.$temp_sum.'\', `points_num`=\''.$temp_num.'\', `points_rank`=\''.$temp_rank.'\', `voted`=\''.$temp_voted.'\' WHERE `nick`=\''.$vote_nick.'\' LIMIT 1;');
			}
		}
	}
	
	if ($makedelete) {
?>
<center>
<form action="" method="post">
  <table border="0" style="BORDER: 1px #957850 solid;" cellspacing="0" cellpadding="3" width="350">
  <tr><td align="center" background="i/bgr-grid-sand.gif">
<?
		if(abs(AccessLevel) & AccessFotosModer) {
			$result = mysql_query('SELECT `nick` FROM `fotos_main` WHERE `id`=\''.$makedelete.'\' LIMIT 1;');
			$row1 = mysql_fetch_assoc($result);
			$tmp_nick = $row1['nick'];
	//		$result = mysql_query('SELECT * FROM fotos_main WHERE (id=\''.$makedelete.'\')');
		} else {
			$tmp_nick = AuthUserName;
		}
		$query = 'SELECT `file` FROM `fotos_main` WHERE `nick`=\''.$tmp_nick.'\' AND `id`=\''.$makedelete.'\' LIMIT 1;';
		//echo ($query);
		$result = mysql_query($query);
		
		if (@mysql_num_rows($result) == 0) {
			echo '<font color="red"><b>Такой фотографии не существует</b></font><br><br><a href="?act=fotos&nick='.$tmp_nick.'\'>Вернуться к фотографиям</a>';
		} else {
			$row = mysql_fetch_assoc($result);
?>
    <b>Удалить фотографию?</b><br><br>
<?
			if (is_file('i/fotos/'.$row['file'])) {
				echo '<img src="i/fotos/'.$row['file'].'"><br><br>';
			} else {
				echo '<img src="i/fotos/sorry.gif"><br><br>';
			}
?>
    </td></tr>
    <tr><td align="center" style="BORDER-TOP: 1px #957850 solid;" background="i/bgr-grid-sand1.gif">
    <input type="hidden" name="act" value="fotos">
    <input type="hidden" name="nick" value="<?=$tmp_nick?>">
    <input type="button" style="CURSOR: hand; WIDTH: 40px" value="Да" onclick="self.location.href='?act=fotos&makedelete=0&delete=<?=$makedelete?>'">&nbsp;&nbsp;<input style="CURSOR: hand; WIDTH: 40px" type="button" value="Нет" onclick="self.location.href='?act=fotos&makedelete=0&nick=<?=$tmp_nick?>'">

<?
		}
?>
  </td></tr>
  </table>
</form>
</center>
<?
	} elseif (!$makedelete && $delete) {
		$tmp_nick = AuthUserName;
		if(abs(AccessLevel) & AccessFotosModer) {
			$result3 = mysql_query('SELECT `nick` FROM `fotos_main` WHERE `id`=\''.$delete.'\' LIMIT 1;');
			$row2 = mysql_fetch_assoc($result3);
			$tmp_nick = $row2['nick'];
		}
		$result = mysql_query('SELECT `file` FROM `fotos_main` WHERE `nick`=\''.$tmp_nick.'\' AND `id`=\''.$delete.'\' LIMIT 1;');
		$result2 = mysql_query('SELECT COUNT(id) as cnt FROM `fotos_main` WHERE `nick`=\''.$tmp_nick.'\'');
		$tmp = mysql_fetch_array($result2);
		
		if (@mysql_num_rows($result) == 0) {
			echo '<font color="red"><b>Такой фотографии не существует</b></font><br><br><a href="?act=fotos&nick='.$tmp_nick.'">Вернуться к фотографиям</a>';
			
		} elseif (@mysql_num_rows($result) == 1 && $tmp['cnt'] == 1) {
			$row = mysql_fetch_assoc($result);
			mysql_query('DELETE FROM `fotos_users` WHERE `nick`=\''.$tmp_nick.'\' LIMIT 1;');
			mysql_query('DELETE FROM `fotos_main` WHERE `id`=\''.$delete.'\' LIMIT 1;');
			unlink('i/fotos/'.$row['file']);
			echo '<center><font color="green"><b>Фотография удалена</b></font><br><br><a href="?act=fotos&nick='.$tmp_nick.'">Вернуться к фотографиям</a></center>';
			
		} else {
			$row = mysql_fetch_assoc($result);
			mysql_query('DELETE FROM `fotos_main` where `id`=\''.$delete.'\' LIMIT 1;');
			unlink('i/fotos/'.$row['file']);
			echo '<center><font color="green"><b>Фотография удалена</b></font><br><br><a href="?act=fotos&nick='.$tmp_nick.'">Вернуться к фотографиям</a></center>';
		}
	
	} else {
		if ($makechcomment) {
			$tmp_nick = AuthUserName;
			if(abs(AccessLevel) & AccessFotosModer) {
				$result3 = mysql_query('SELECT `nick` FROM `fotos_main` WHERE `id`=\''.$makechcomment.'\' LIMIT 1;');
				$row2 = mysql_fetch_assoc($result3);
				$tmp_nick = $row2['nick'];
			}
			
			$result = mysql_query('SELECT `file` FROM `fotos_main` WHERE `nick`=\''.$tmp_nick.'\' AND id=\''.$makechcomment.'\' LIMIT 1;');
			
			if (@mysql_num_rows($result) == 0) {
				echo '<center><font color="red"><b>Такой фотографии не существует</b></font><br><br><a href="?act=fotos&nick='.$tmp_nick.'">Вернуться к фотографиям</a></center>';
				$makestop = 1;
			} else {
		//		$row = mysql_fetch_assoc($result);
				mysql_query('UPDATE `fotos_main` SET `comment`=\''.$newcomment.'\' WHERE `id`=\''.$makechcomment.'\' LIMIT 1;');
			}
		}
		
		if ($makechfirst) {
			$tmp_nick = AuthUserName;
			if(abs(AccessLevel) & AccessFotosModer) {
				$tmp_nick = $firstchname;
			}
			mysql_query('UPDATE `fotos_users` SET firstfoto=\''.$newfirst.'\' WHERE nick=\''.$tmp_nick.'\' LIMIT 1;');
		}
		
		if (!$makestop) {
?>
<table border="0" cellsapcing="0" cellpadding="0" width="100%">
<tr>
  <td width="350" valign="top">
  <table border="0" style="BORDER: 1px #957850 solid;" background="i/bgr-grid-sand.gif" cellspacing="0" cellpadding="3" width="100%">
  <tr><td style="BORDER-BOTTOM: 1px #957850 solid;">
<?
		//Fotos Settings
			$fotos_nicksperpage = 25;
			if ($make_vote && AuthStatus==1 && AuthUserName != '' && $vote_radio < 6 && $vote_radio > 0 && $vote_nick) {
				$result1 = mysql_query('SELECT `voted`, `points_sum`,  FROM `fotos_users` WHERE nick=\''.$vote_nick.'\' LIMIT 1;');
				if (@mysql_num_rows($result1) > 0) {
					$row1 = mysql_fetch_assoc($result1);
					if (!strstr($row1['voted'], AuthUserName)) {
						$temp_sum = $row1['points_sum'] + $vote_radio;
						$temp_num = $row1['points_num'] + 1;
						$temp_rank = round($temp_sum / $temp_num,2);
						$temp_voted = $row1['voted'].' '.AuthUserName.' ';
						$result1 = mysql_query('UPDATE `fotos_users` SET `points_sum`=\''.$temp_sum.'\', `points_num`=\''.$temp_num.'\', `points_rank`=\''.$temp_rank.'\', `voted`=\''.$temp_voted.'\' WHERE nick=\''.$vote_nick.'\' LIMIT 1;');
					}
				}
			}
			$query = "SELECT COUNT(`nick`) as `cnt` FROM `fotos_users` WHERE (`fotos` > '0'";
			if ($sort_clan != 'no' && $sort_clan) { $query = $query.' AND `clan`=\''.$sort_clan.'\''; }
			if ($sort_gener != 'no' && $sort_gener) { $query = $query.' AND `gener`=\''.$sort_gener.'\''; }
			$query = $query.')';
			$result = mysql_query($query);
			$rsl = mysql_fetch_array($result);
			$pages = ceil($rsl['cnt'] / $fotos_nicksperpage);
			if($_REQUEST['p']>0) $p=$_REQUEST['p'];
			else $p=1;
			$pages_add = 'act=fotos';
			if ($sort_by && $sort_by != 'new') { $pages_add .= '&sort_by='.$sort_by; }
			if ($sort_gener && $sort_gener != 'no') { $pages_add .= '&sort_gener='.$sort_gener; }
			if ($sort_clan && $sort_clan != 'no') { $pages_add .= '&sort_clan='.$sort_clan; }
?>
  <b>Страницы:</b> <?=ShowPages($p,$pages,5,$pages_add)?>
  </td></tr>
  <tr><td background="i/bgr-grid-sand1.gif">
<?
			$temp_p = ($p - 1) * $fotos_nicksperpage;
			$temp_query = $query.' LIMIT '.$temp_p.','.$fotos_nicksperpage;
		//by deadbeef
			$tquery = "SELECT nick, clan, points_rank FROM `fotos_users` WHERE (`fotos` > '0' ";
			if ($sort_clan != 'no' && $sort_clan) { $tquery .= ' AND clan=\''.$sort_clan.'\''; }
			if ($sort_gener != 'no' && $sort_gener) { $tquery .= ' AND gener=\''.$sort_gener.'\''; }
			$tquery .= ') ORDER BY ';
			if ($sort_by == 'new' || !$sort_by) { $tquery .= 'fotos DESC'; }
			elseif ($sort_by == 'name') { $tquery .= 'nick ASC'; }
			elseif ($sort_by == 'sum') { $tquery .= 'points_sum DESC'; }
			elseif ($sort_by == 'points') { $tquery .= 'points_rank DESC'; }
			$tquery .= ' LIMIT '.$temp_p.','.$fotos_nicksperpage;
		//	if (AuthUserName == "FANTASTISH") { echo $tquery; }
			$result1 = mysql_query($tquery);
		//end
	//		$result1 = mysql_query($temp_query);
			if (@mysql_num_rows($result1) == 0) { echo '<center><b>Список пуст</b></center>'; }
			else {
				echo '<table border="0" cellspacing="0" cellpadding="0" width="100%">';
				while ($row1 = mysql_fetch_assoc($result1)) {
					if (!$nick) { $nick = $row1['nick']; }
					echo '<tr>';
					echo ' <td width="100%" nowrap>';
					
					if ($row1['clan']) {
						echo '<img src="_imgs/clans/'.$row1['clan'].'.gif" width="28" height="16">&nbsp;';
					} else {
						echo '<img src="_imgs/none.gif" width="28" height="16">&nbsp;';
					}
					/*echo "<a href='".$PHP_SELF."?act=fotos&p=".$p."&nick=".$row1["nick"];
					if ($sort_by && $sort_by <> "new") { echo "&sort_by=".$sort_by; }
					if ($sort_gener && $sort_gener <> "no") { echo "&sort_gener=".$sort_gener; }
					if ($sort_clan && $sort_clan <> "no") { echo "&sort_clan=".$sort_clan; } echo "'>" . $row1["nick"] . "</a></td>";*/
					echo '<a href="javascript:{}" onClick="loadfoto(\''.$row1['nick'].'\', 0,1);">'.$row1['nick'].'</a></td>';
					
					$query = 'SELECT COUNT(*) as `fotos_count` FROM `fotos_main` WHERE `nick` = \''.$row1['nick'].'\'';
				//	if (AuthUserName == "FANTASTISH") { echo $query; }
					$rsl = mysql_fetch_array(mysql_query($query));
					echo '<td>&nbsp;&nbsp;'.$rsl['fotos_count'].'&nbsp;шт.&nbsp;&nbsp;</td>';
					
					if ($row1['points_rank'] <> 0) {
						echo '<td>'.$row1['points_rank'].'</td>';
					} else {
						echo '<td>0.00</td>';
					}
					
					echo '</tr>';
				}
				echo '</table>';
			}
?>
  </td></tr>
  <form action="?act=fotos" method="post">
  <tr><td style="BORDER-TOP: 1px #957850 solid;">
    <b>Сортировка/Фильтр:</b><br><br>
    <center>
    &nbsp;по<img src="_imgs/none.gif" width="16" height="1"><select size="1" name="sort_by"><option <?php if ($sort_by == 'new' || !$sort_by) { echo 'selected'; } ?> value="new">новизне</option><option <?php if ($sort_by == 'name') { echo 'selected'; } ?> value="name">имени</option><option <?php if ($sort_by == 'sum') { echo 'selected'; } ?> value="sum">баллам</option><option <?php if ($sort_by == 'points') { echo 'selected'; } ?> value="points">рейтингу</option></select> только <select size="1" name="sort_gener"><option <?php if (!$sort_gener) { echo 'selected'; } ?> value="no">нет</option><option <?php if ($sort_gener == 1) { echo 'selected'; } ?> value="1">Муж</option><option <?php if ($sort_gener == 2) { echo 'selected'; } ?> value="2">Жен</option></select>&nbsp;<br>
    <img src="_imgs/none.gif" height="5"><br>
    &nbsp;клан <select size="1" style="WIDTH: 173px" name="sort_clan"><option <?php if (!$sort_clan) { echo 'selected'; } ?> value="no">нет</option>
<?
			$smiles_dir='_imgs/clans/';
			$d = opendir($smiles_dir);
			$counter = 0;
			
			while(($CurFile=readdir($d))!==false) if(is_file($smiles_dir.'/'.$CurFile)) {
				$FileType=GetImageSize($smiles_dir.'/'.$CurFile);
				$CurFile=explode('.',$CurFile);
				if($FileType[2]==1){
					$clans[$counter] = $CurFile[0];
					$counter++;
				}
			}
			closedir($d);
			natcasesort($clans);
			reset($clans);
			
			while (list($key, $val) = each($clans)) {
				if ($val !== '0') {
					echo '<option value="'.$val.'"';
					if($sort_clan == $val) echo ' selected';
					echo '>'.$val.'</option>';
				}
			}
?>
   </select>&nbsp;<br>
   <img src="_imgs/none.gif" height="5"><br>
   &nbsp;ник<img src="_imgs/none.gif" width="10" height="1"><input style="WIDTH: 173 px" name="nick" type="text" value="">&nbsp;<br>
   <img src="_imgs/none.gif" height="5"><br>
   <input style="CURSOR: hand" type="submit" value="Применить"><br><br>
   </center>
  </td></tr>
  </form>
  </table><br>
  <table border="0" background="i/bgr-grid-sand.gif" cellspacing="0" cellpadding="3" width="100%" style="BORDER: 1px #957850 solid;">
  <tr><td>
  <b>Статистика:</b><br><br>
  &nbsp;Всего ников:&nbsp;<?php $temp_all = @mysql_fetch_array(mysql_query('SELECT COUNT(*) as cnt FROM `fotos_users` WHERE fotos > \'0\'')); echo $temp_all['cnt']; ?><br>

  &nbsp;Всего фотографий:&nbsp;<?$res=@mysql_fetch_array(mysql_query('SELECT COUNT(*) as cnt FROM fotos_main')); echo($res['cnt']);?><br>

  <img src="_imgs/none.gif" height="5"><br>
  &nbsp;Всего клановых игроков:&nbsp;<?$temp2=@mysql_fetch_array(mysql_query('SELECT COUNT(*) as cnt FROM `fotos_users` WHERE clan <> \'\' AND clan <> \'0\' AND clan <> \'NULL\'')); echo($temp2['cnt']);?><br>

  &nbsp;Всего не клановых игроков:&nbsp;<?php echo ($temp_all['cnt'] - $temp2['cnt']); ?><br><br>
  </td></tr></table>
  </td>
  <td width="100%" rowspan="2" valign="top" align="center">
<div id="foto_area">
</div>
<div id="comment_area"></div>
<?php
$gotofoto = $_REQUEST['pict']?$_REQUEST['pict']:0;
	if($nick){
//echo 1;
?>
<script language="javascript">
loadfoto('<?=$nick?>', <?=$gotofoto?>,1);
</script>
<?php
	}
  ?>
  </td>
</tr>
</table><br>
<table border="0">
<tr><td><div id="foto_link"></div>
</td></tr>
</table>
<?php }  } ?>