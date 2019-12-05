<?
/****************************************************
*	 Lebedev Sergey (fantastish@rambler.ru)			*
****************************************************/
// battles_archive

$fotos_nicksperpage = 25;

?>
<h1>Галерея боев</h1>

<SCRIPT src="_modules/xhr_js.js"></SCRIPT>
<script language="javascript">
function loadfoto(nick,foto,c){
 if(c==1){closecomments();}
 var req = new Subsys_JsHttpRequest_Js();
 document.getElementById('foto_area').innerHTML = "Loading...";
 req.onreadystatechange = function()
 {
  if (req.readyState == 4) {
   if (req.responseJS) {
    document.getElementById('foto_link').innerHTML = "Ссылка на Бои: <a href="http://www.tzpolice.ru/?act=battles_archive&nick="+escape(nick)+"">http://www.tzpolice.ru/?act=battles_archive&nick="+nick+"</a>";
    document.getElementById('foto_area').innerHTML = req.responseText;
   }
  }
 }
 req.caching = false;
 req.open('POST', '_modules/backends/battles_load.php', true);
 req.send({ nick: nick, f: foto });
}

function opencomments(id,p){
 var req = new Subsys_JsHttpRequest_Js();
 document.getElementById('comment_area').innerHTML = "Loading...";
 req.onreadystatechange = function()
 {
  if (req.readyState == 4) {
   if (req.responseJS) {
    document.getElementById('comment_area').innerHTML = req.responseText;
   }
  }
 }
 req.caching = false;
 req.open('POST', '_modules/backends/battles_comments.php', true);
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
 //window.alert(e);
 var req = new Subsys_JsHttpRequest_Js();
 document.getElementById('comment_area').innerHTML = "Loading...";
 req.onreadystatechange = function()
 {
  if (req.readyState == 4) {
   if (req.responseJS) {
    document.getElementById('comment_area').innerHTML = req.responseText;
   }
  }
 }
 req.caching = false;
 req.open('POST', '_modules/backends/battles_comments.php', true);
 req.send({act: 'battles_comments', doo: 'add', DataId: c, p: d, NewsText:e});
 //opencomments(c);
}
</script>

<?php

//error_reporting(E_ALL);
error_reporting(0);

extract($_REQUEST);
$p = $_REQUEST['p'];

//$nick = str_replace("%20", " ", $_REQUEST['nick']);
$nick = strip_tags(urldecode($_REQUEST['nick']));
if (strlen($nick) > 16) { $nick=substr($nick,0,16); }

$f = $_REQUEST['f'];

//======= Голосовалка ==========
if ($_REQUEST['make_vote'] && AuthStatus==1 && AuthUserName!="" && $_REQUEST['vote_radio'] < 6 && $_REQUEST['vote_radio'] > 0 && $_REQUEST['vote_nick']) {
	$result1 = mysql_query("SELECT * FROM `tzbattles_users` WHERE (nick='".$_REQUEST['vote_nick']."') LIMIT 1;");
	if (@mysql_num_rows($result1) > 0) {
		$row1 = mysql_fetch_assoc($result1);
		if (!strstr($row1["voted"], AuthUserName)) {
			$temp_sum = $row1["points_sum"] + $_REQUEST['vote_radio'];
			$temp_num = $row1["points_num"] + 1;
			$temp_rank = round($temp_sum / $temp_num,2);
			$temp_voted = $row1["voted"]." ".AuthUserId." ";
			$result1 = mysql_query("UPDATE `tzbattles_users` SET `points_sum`='".$temp_sum."', points_num='".$temp_num."', points_rank='".$temp_rank."', voted='".$temp_voted."' WHERE nick='".$vote_nick."' LIMIT 1;");
		}
	}
}
//=============================

//====== Удаление (подтверждение) =============
if ($makedelete) {
	echo "<center>\n";
	echo "<form action=\"\" method=\"post\">\n";
	echo "<table border=\"0\" style=\"BORDER: 1px #957850 solid;\" cellspacing=\"0\" cellpadding=\"3\" width=\"250\">\n";
	echo " <tr><td align=\"center\" background=\"i/bgr-grid-sand.gif\">\n";
	
	if(abs(AccessLevel) & AccessTzBattlesModer) {
		$result = mysql_query("SELECT `nick` FROM `tzbattles_main` WHERE (id='".$makedelete."') LIMIT 1;");
		$row1 = mysql_fetch_assoc($result);
		$tmp_nick = $row1["nick"];
		
	} else {
		$tmp_nick = AuthUserName;
	}
	
	$query = "SELECT `file` FROM `tzbattles_main` WHERE `nick`='".$tmp_nick."' AND `id`='".$makedelete."' LIMIT 1;";
	//echo ($query);
	$result = mysql_query($query);

	if (@mysql_num_rows($result) == 0) {
		echo "<font color='red'><b>Такого боя не существует</b></font><br><br><a href='?act=fotos&nick=".$tmp_nick."'>Вернуться...</a>";

	} else {
		$row = mysql_fetch_assoc($result);
		
		echo "<b>Удалить бой?</b><br><br>\n";
		echo "<A HREF=\"battles/".$row["file"]."\">".$row["file"]."</A><BR><BR>\n";

		echo " </td></tr>\n";
		echo " <tr><td align=\"center\" style=\"BORDER-TOP: 1px #957850 solid;\" background=\"i/bgr-grid-sand1.gif\">\n";
		echo "  <input type=\"hidden\" name=\"act\" value=\"battles_archive\">\n";
		echo "  <input type=\"hidden\" name=\"nick\" value=\"".$tmp_nick."\">\n";
		echo "  <input type=\"button\" style=\"CURSOR: hand; WIDTH: 40px\" value=\"Да\" onclick=\"self.location.href='?act=battles_archive&makedelete=0&delete=".$makedelete."'\">&nbsp;&nbsp;";
		echo "  <input type=\"button\" style=\"CURSOR: hand; WIDTH: 40px\" value=\"Нет\" onclick=\"self.location.href='?act=battles_archive&makedelete=0&nick=".$tmp_nick."'\">\n";
	}
	echo " </td></tr>\n";
	echo "</table>\n";
	echo "</form>\n";
	echo "</center>\n";

//=========== удаление ===========
} elseif (!$makedelete && $delete) {
	
	$tmp_nick = AuthUserName;
	if(abs(AccessLevel) & AccessTzBattlesModer) {
		$result3 = mysql_query("SELECT `nick` FROM `tzbattles_main` WHERE (id='".$delete."') LIMIT 1;");
		$row2 = mysql_fetch_assoc($result3);
		$tmp_nick = $row2["nick"];
	}
	
	$result = mysql_query("SELECT `file` FROM `tzbattles_main` WHERE (nick='".$tmp_nick."' AND id='".$delete."') LIMIT 1;");
	$result2 = mysql_query("SELECT COUNT(id) as cnt FROM `tzbattles_main` WHERE (nick='".$tmp_nick."')");
	$tmp = mysql_fetch_array($result2);
	
	if (@mysql_num_rows($result) == 0) {
		echo "<font color='red'><b>Такой бой не существует</b></font><br><br><a href='?act=battles_archive&nick=".$tmp_nick."'>Вернуться...</a>";
	
	} elseif (@mysql_num_rows($result) == 1 && $tmp['cnt'] == 1) {
		$row = mysql_fetch_assoc($result);
		mysql_query("DELETE FROM `tzbattles_users` WHERE (nick='".$tmp_nick."') LIMIT 1;");
		mysql_query("DELETE FROM `tzbattles_main` WHERE (id='".$delete."') LIMIT 1;");
		unlink("battles/".$row["file"]);
		echo "<center><font color='green'><b>Бой удален</b></font><br><br><a href='?act=battles_archive&nick=".$tmp_nick."'>Вернуться...</a></center>";
	
	} else {
		$row = mysql_fetch_assoc($result);
		mysql_query("DELETE FROM `tzbattles_main` where(id='".$delete."') LIMIT 1;");
		unlink("battles/".$row["file"]);
		echo "<center><font color='green'><b>Бой удален</b></font><br><br><a href='?act=battles_archive&nick=".$tmp_nick."'>Вернуться...</a></center>";
	}

// Основная часть каталога
} else {
	if ($makechcomment) {
		$tmp_nick = AuthUserName;
		if(abs(AccessLevel) & AccessTzBattlesModer) {
			$result3 = mysql_query("SELECT `nick` FROM `tzbattles_main` WHERE (id='".$makechcomment."') LIMIT 1;");
			$row2 = mysql_fetch_assoc($result3);
			$tmp_nick = $row2["nick"];
		}
		
		$result = mysql_query("SELECT `file` FROM `tzbattles_main` WHERE (nick='".$tmp_nick."' AND id='".$makechcomment."') LIMIT 1;");
		
		if (@mysql_num_rows($result) == 0) {
			echo "<center><font color='red'><b>Такой бой не существует</b></font><br><br><a href='?act=battles_archive&nick=".$tmp_nick."'>Вернуться...</a></center>";
			$makestop = 1;
		} else {
		//	$row = mysql_fetch_assoc($result);
			mysql_query("UPDATE `tzbattles_main` SET comment='".$newcomment."' WHERE id='".$makechcomment."' LIMIT 1;");
		}
	}
	
/*	if ($makechfirst) {
		$tmp_nick = AuthUserName;
		
		if(abs(AccessLevel) & AccessTzBattlesModer) {
			$tmp_nick = $firstchname;
		}
		
		mysql_query("UPDATE `tzbattles_users` SET firstfoto='".$newfirst."' WHERE nick='".$tmp_nick."' LIMIT 1;");
	}
*/	
	if (!$makestop) {
		echo "<table border=\"0\" cellsapcing=\"0\" cellpadding=\"0\" width=\"100%\">\n";
		echo " <tr>\n";
		echo "  <td width=\"250\" valign=\"top\">\n";
		echo "   <table border=\"0\" style=\"BORDER: 1px #957850 solid;\" background=\"i/bgr-grid-sand.gif\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\">\n";
		echo "    <tr><td style=\"BORDER-BOTTOM: 1px #957850 solid;\">\n";

	//Fotos Settings
		$fotos_nicksperpage = 25;
		
		if ($make_vote && AuthStatus==1 && AuthUserName!="" && $vote_radio < 6 && $vote_radio > 0 && $vote_nick) {
			$result1 = mysql_query("SELECT `voted`, `points_sum`,  FROM `tzbattles_users` WHERE (nick='".$vote_nick."') LIMIT 1;");
			if (@mysql_num_rows($result1) > 0) {
				$row1 = mysql_fetch_assoc($result1);
				if (!strstr($row1["voted"], AuthUserName)) {
					$temp_sum = $row1["points_sum"] + $vote_radio;
					$temp_num = $row1["points_num"] + 1;
					$temp_rank = round($temp_sum / $temp_num,2);
					$temp_voted = $row1["voted"] . " " . AuthUserName . " ";
					$result1 = mysql_query("UPDATE `tzbattles_users` SET points_sum='".$temp_sum."', points_num='".$temp_num."', points_rank='".$temp_rank."', voted='".$temp_voted."' WHERE nick='".$vote_nick."' LIMIT 1;");
				}
			}
		}
		
		$query = "SELECT COUNT(`nick`) as `cnt` FROM `tzbattles_users` WHERE (fotos > '0'";
//		if ($sort_clan <> "no" && $sort_clan) { $query = $query . " AND clan='".$sort_clan."'"; }
//		if ($sort_gener <> "no" && $sort_gener) { $query = $query . " AND gener='".$sort_gener."'"; }
		$query = $query . ")";
		
	/*	
		if ($sort_by == "new" || !$sort_by) { $query = $query . "fotos DESC"; }
		elseif ($sort_by == "name") { $query = $query . "nick"; }
		elseif ($sort_by == "sum") { $query = $query . "points_sum DESC"; }
		elseif ($sort_by == "points") { $query = $query . "points_rank DESC"; }
	*/
		$result = mysql_query($query);
		$rsl = mysql_fetch_array($result);
		$pages = ceil($rsl['cnt'] / $fotos_nicksperpage);
		if($_REQUEST['p']>0) $p=$_REQUEST['p'];
		else $p=1;
		$pages_add = "act=battles_archive";
		if ($sort_by && $sort_by <> "new") { $pages_add .= "&sort_by=".$sort_by; }
//		if ($sort_gener && $sort_gener <> "no") { $pages_add .= "&sort_gener=".$sort_gener; }
//		if ($sort_clan && $sort_clan <> "no") { $pages_add .= "&sort_clan=".$sort_clan; }
		
		echo "<b>Страницы:</b> ".ShowPages($p,$pages,5,$pages_add)."\n";
		echo "  </td></tr>\n";
		echo "  <tr><td background=\"i/bgr-grid-sand1.gif\">\n";
		
		$temp_p = ($p - 1) * $fotos_nicksperpage;
		$temp_query = $query . " LIMIT ".$temp_p.",".$fotos_nicksperpage."";
		
//by deadbeef
		$tquery = "SELECT tzbattles_users.nick, tzbattles_users.points_rank, COUNT(tzbattles_main.id) AS fotos_count FROM `tzbattles_users` JOIN `tzbattles_main` ON (tzbattles_users.nick=tzbattles_main.nick) WHERE (tzbattles_users.last > 0 ";
//		if ($sort_clan <> "no" && $sort_clan) { $tquery = $tquery . " AND tzbattles_users.clan='".$sort_clan."'"; }
//		if ($sort_gener <> "no" && $sort_gener) { $tquery = $tquery . " AND tzbattles_users.gener='".$sort_gener."'"; }
		$tquery = $tquery . ") GROUP BY tzbattles_users.nick ORDER BY ";
		if ($sort_by == "new" || !$sort_by) { $tquery = $tquery . "last DESC"; }
		elseif ($sort_by == "name") { $tquery = $tquery . "nick"; }
		elseif ($sort_by == "sum") { $tquery = $tquery . "points_sum DESC"; }
		elseif ($sort_by == "points") { $tquery = $tquery . "points_rank DESC"; }
		$tquery .= " LIMIT ".$temp_p.",".$fotos_nicksperpage."";
	//	if (AuthUserName == "deadbeef") {echo($tquery);}
		$result1 = mysql_query($tquery);
	//end
	//	$result1 = mysql_query($temp_query);
		if (@mysql_num_rows($result1) == 0) { echo "<center><b>Список пуст</b></center>"; }
		else {
			echo "<table border='0' cellspacing='0' cellpadding='0' width='100%'>";
			while ($row1 = mysql_fetch_assoc($result1)) {
				if (!$nick) { $nick = $row1["nick"]; }
				echo "<tr>";
				echo "<td width='100%'>";
		//		if ($row1["clan"]) {
		//			echo "<img src='_imgs/clans/".$row1["clan"].".gif' width='28' height='16'>";
		//			
		//		} else {
		//			echo "<img src='_imgs/none.gif' width='28' height='16'>";
		//		}

				echo "<a href='#; return false;' onClick=\"loadfoto('".$row1["nick"]."', 0,1);\">".$row1["nick"]."</a></td>";
				echo "<td>".$row1["fotos_count"]."&nbsp;шт.&nbsp;&nbsp;</td>";
				
				if ($row1["points_rank"] <> 0) {
					echo "<td>".$row1["points_rank"]."</td>";
				} else {
					echo "<td>0.00</td>";
				}
				
				echo "</tr>";
			}
			
			echo"</table>";
		}
		
		echo " </td></tr>\n";
		echo " <form action=\"?act=battles_archive\" method=\"post\">\n";
		echo " <tr><td style=\"BORDER-TOP: 1px #957850 solid;\">\n";
		echo "  <b>Сортировка/Фильтр:</b><br><br>\n";
		echo "  <center>\n";
		echo "  &nbsp;по<img src=\"_imgs/none.gif\" width=\"16\" height=\"1\">\n";
		echo "  <select size=\"1\" name=\"sort_by\">\n";
		echo "   <option".(($sort_by == "new" || !$sort_by)?" selected":"")." value=\"new\">новизне</option>\n";
		echo "   <option".(($sort_by == "name")?" selected":"")." value=\"name\">имени</option>\n";
		echo "   <option".(($sort_by == "sum")?" selected":"")." value=\"sum\">баллам</option>\n";
		echo "   <option".(($sort_by == "points")?" selected":"")." value=\"points\">рейтингу</option>\n";
		echo "  </select>\n";
	/*	echo " только <select size=\"1\" name=\"sort_gener\">\n";
		echo "   <option".((!$sort_gener)?" selected":"")." value=\"no\">нет</option>\n";
		echo "   <option".(($sort_gener == 1)?" selected":"")." value=\"1\">Муж</option>\n";
		echo "   <option".(($sort_gener == 2)?" selected":"")." value=\"2\">Жен</option>\n";
		echo "  </select>&nbsp;<br>\n";
		echo "  <img src=\"_imgs/none.gif\" height=\"5\"><br>\n";
		echo "  &nbsp;клан <select size=\"1\" style=\"WIDTH: 173px\" name=\"sort_clan\">\n";
		echo "   <option".((!$sort_clan)?" selected":"")." value=\"no\">нет</option>\n";
		
		$smiles_dir="_imgs/clans/";
		$d=opendir($smiles_dir);
		$counter = 0;
		
		while(($CurFile=readdir($d))!==false) if(is_file($smiles_dir."/".$CurFile)) {
			$FileType=GetImageSize($smiles_dir."/".$CurFile);
			$CurFile=explode(".",$CurFile);
			if($FileType[2]==1){
				$clans[$counter] = $CurFile[0];
				$counter++;
			}
		}
		closedir($d);
		natcasesort($clans);
		reset($clans);
		
		while (list($key, $val) = each($clans)) {
			if ($val !== "0") {
				echo "<option value=\"".$val."\"";
				if($sort_clan=="".$val."") echo " selected";
				echo ">".$val."</option>";
			}
		}
		
		echo "  </select>\n";
	*/
		echo "&nbsp;<br>\n";
		echo "  <img src=\"_imgs/none.gif\" height=\"5\"><br>\n";
		echo "  &nbsp;ник<img src=\"_imgs/none.gif\" width=\"10\" height=\"1\"><input style=\"WIDTH: 173 px\" name=\"nick\" type=\"text\" value=\"\">&nbsp;<br>\n";
		echo "  <img src=\"_imgs/none.gif\" height=\"5\"><br>\n";
		echo "  <input style=\"CURSOR: hand\" type=\"submit\" value=\"Применить\"><br><br>\n";
		echo "  </center>\n";
		echo " </td></tr>\n";
		echo " </form>\n";
		echo "</table><br>\n";
		
		echo "<table border=\"0\" background=\"i/bgr-grid-sand.gif\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\" style=\"BORDER: 1px #957850 solid;\">\n";
		echo " <tr><td>\n";
		echo "  <b>Статистика:</b><br><br>\n";
		echo "  &nbsp;Всего ников:&nbsp;\n";
		$temp_all = @mysql_fetch_array(mysql_query("SELECT COUNT(nick) as cnt FROM `tzbattles_users` WHERE (fotos > '0')"));
		echo $temp_all['cnt'];
		echo "  <br>\n";
		echo "  &nbsp;Всего боев:&nbsp;\n";
		$res=@mysql_fetch_array(mysql_query("SELECT COUNT(id) as cnt FROM tzbattles_main"));
		echo($res['cnt']);
	/*	echo "  <br>\n";
		echo "  <img src=\"_imgs/none.gif\" height=\"5\"><br>\n";
		echo "  &nbsp;Всего клановых игроков:&nbsp;\n";
		$temp2=@mysql_fetch_array(mysql_query("SELECT COUNT(nick) as cnt FROM `tzbattles_users` WHERE (clan <> '' AND clan <> '0' AND clan <> 'NULL')"));
		echo($temp2['cnt']);
		echo "  <br>\n";
		echo "  &nbsp;Всего не клановых игроков:&nbsp;\n";
		echo ($temp_all['cnt'] - $temp2['cnt']);
	*/
		echo "  <br><br>\n";
		echo "</td></tr></table>\n";
		echo "</td>\n";
		echo "<td width=\"100%\" rowspan=\"2\" valign=\"top\" align=\"center\">\n";
		echo " <div id=\"foto_area\"></div>\n";
		echo " <div id=\"comment_area\"></div>\n";
		
		if($nick){
		//echo 1;
			echo "<script language=\"javascript\">\n";
			echo "loadfoto('".$nick."', 0, 1);\n";
			echo "</script>\n";
		}
		
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table><br>\n";
		echo "<table border=\"0\">\n";
		echo " <tr><td><div id=\"foto_link\"></div>\n";
		echo " </td></tr>\n";
		echo "</table>\n";
	}
}
	
?>