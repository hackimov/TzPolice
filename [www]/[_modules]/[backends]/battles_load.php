<?php
$_RESULT = array("res" => "ok");
require_once "../xhr_config.php";
require_once "../xhr_php.php";
require_once "../mysql.php";
require_once "../functions.php";
require_once "../auth.php";
$JsHttpRequest =& new Subsys_JsHttpRequest_Php("windows-1251");

//$nick=$_REQUEST['nick'];
$nick = strip_tags(urldecode($_REQUEST['nick']));
if (strlen($nick) > 16) {$nick=substr($nick,0,16);}
//echo ("fsdfdfsdf = ".strlen($nick));
$f=$_REQUEST['f'];
$result1 = mysql_query("SELECT * FROM `tzbattles_users` WHERE (`nick`='".$nick."' AND `last`>'0')");

if (@mysql_num_rows($result1) == 0) {
	echo "<center><b>Бои этого пользователя не найдены</b></center>";
	
} else {
	$row1 = mysql_fetch_assoc($result1);
	$result2 = mysql_query("SELECT * FROM `tzbattles_main` WHERE (`nick`='".$nick."') ORDER BY id");
	$temp_views = $row1["views"];
	$temp_views++;
	$result3 = mysql_query("UPDATE `tzbattles_users` SET views='".$temp_views."' WHERE `nick`='".$nick."'");
	if (!$f || $f==0) {
		$f = $row1["firstfoto"];
		while ($f > @mysql_num_rows($result2)) {
			$f = $f-1;
		}
	}
	
	$i = 0;
	while ($i < $f) {
		$i++;
		$row2 = mysql_fetch_assoc($result2);
	}
	###
	
	echo " <table border=\"0\" cellspacing=\"0\" cellpadding=\"3\" width=\"450\" style=\"BORDER: 1px #957850 solid;\">\n";
	echo "  <tr><td style=\"BORDER-BOTTOM: 1px #957850 solid;\" background=\"i/bgr-grid-sand.gif\" align=\"center\">\n";
/*	if ($row1["clan"] <> "0" && $row1["clan"] <> "NULL" && $row1["clan"] <> "") {
		echo "  <img src=\"_imgs/clans/".$row1["clan"].".gif\" width=\"28\" height=\"16\">\n";
	}else{
		echo "  <img src=\"_imgs/none.gif\" width=\"28\" height=\"16\">\n";
	}
*/	
	echo "  <b>".$row2["nick"]."</b>\n";
	echo " </td></tr>\n";
	
//	echo " <tr><td style=\"BORDER-BOTTOM: 1px #957850 solid;\" background=\"i/bgr-grid-sand1.gif\" align=\"center\">\n";
//	if ($row2["name"] <> "") {
//		echo "  <b>Имя:</b>&nbsp;".$row2["name"]."&nbsp;\n";
//	}
//	echo "  <b>Пол:</b>&nbsp;\n";
//	if ($row2["gener"] == 1) { echo "Муж"; } else { echo "Жен"; }
//	echo " &nbsp;\n";
//	if ($row2["city"] <> "") {
//		echo " <b>Город:</b>&nbsp;".$row2["city"]."&nbsp;\n";
//	}
//	if ($row2["age"] <> "" && $row2["age"] <> 0) {
//		echo " <b>Возраст:</b>&nbsp;".$row2["age"]."&nbsp;\n";
//	}
//	echo " </td></tr>\n";

	echo " <tr><td style=\"BORDER-BOTTOM: 1px #957850 solid;\" background=\"i/bgr-grid-sand.gif\" align=\"center\">\n";
	echo "  <img src=\"_imgs/none.gif\" height=\"10\"><br>\n";

//====== Список боев ============
	//echo $row2['file'];
	if (is_file("../../i/fotos/".$row2['file'])) {
		echo "  <img src=\"i/fotos/".$row2["file"]."\"><br><br>\n";
	} else {
		echo "  <img src=\"i/fotos/sorry.gif\"><br><br>\n";
	}
//===============================

	echo "  <img src=\"_imgs/none.gif\" height=\"10\"><br>\n";
	if ($row2["comment"]) {
		echo "Комментарий:&nbsp;<font style='COLOR: #47A639'>".$row2["comment"]."</font>";
	}
	
	echo " </td></tr>\n";
	echo " <tr><td style=\"BORDER-BOTTOM: 1px #957850 solid;\" background=\"i/bgr-grid-sand1.gif\" align=\"center\">\n";
	echo "  <b>Страницы:</b>&nbsp;\n";
	
	for ($i = 1; $i <= @mysql_num_rows($result2); $i++) {
		if ($i == $f) {
			echo "<b> [".$i."] </b>";
		} else {
			echo "<a href='#; return false;' onClick=\"loadfoto('".$nick."', ".$i.",0);\">".$i."</a> ";
		}
	}
	
	echo " </td></tr>\n";
	echo " <tr><td style=\"BORDER-BOTTOM: 1px #957850 solid;\" background=\"i/bgr-grid-sand.gif\" align=\"center\">\n";
	echo "  <b>Текущий рейтинг:</b>&nbsp;\n";
	if ($row1["points_rank"] == 0 ) { echo "0.00"; } else { echo $row1["points_rank"]; }
	echo "  <br>\n";
	echo "  кол-во голосов:&nbsp;".$row1["points_num"]." баллы:&nbsp;".$row1["points_sum"]." кол-во просмотров:&nbsp;".$temp_views."<br>\n";
	
	if(AuthStatus==1 && AuthUserName!="") {
		if (!strstr($row1["voted"], AuthUserName)) {
			echo "  <table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
			echo "   <tr>\n";
			echo "    <td align=\"center\"><b>1</b></td>\n";
			echo "    <td>&nbsp;&nbsp;</td>\n";
			echo "    <td align=\"center\"><b>2</b></td>\n";
			echo "    <td>&nbsp;&nbsp;</td>\n";
			echo "    <td align=\"center\"><b>3</b></td>\n";
			echo "    <td>&nbsp;&nbsp;</td>\n";
			echo "    <td align=\"center\"><b>4</b></td>\n";
			echo "    <td>&nbsp;&nbsp;</td>\n";
			echo "    <td align=\"center\"><b>5</b></td>\n";
			echo "   </tr>\n";
			echo "   <form action=\"?act=battles_archive&nick=".$nick."&f=".$f."\" method=\"post\"><input name=\"vote_nick\" type=\"hidden\" value=\"".$nick."\">\n";
			echo "   <tr>\n";
			echo "    <td align=\"center\"><input name=\"vote_radio\" type=\"radio\" value=\"1\"></td>\n";
			echo "    <td>&nbsp;&nbsp;</td>\n";
			echo "    <td align=\"center\"><input name=\"vote_radio\" type=\"radio\" value=\"2\"></td>\n";
			echo "    <td>&nbsp;&nbsp;</td>\n";
			echo "    <td align=\"center\"><input name=\"vote_radio\" type=\"radio\" value=\"3\"></td>\n";
			echo "    <td>&nbsp;&nbsp;</td>\n";
			echo "    <td align=\"center\"><input name=\"vote_radio\" type=\"radio\" value=\"4\"></td>\n";
			echo "    <td>&nbsp;&nbsp;</td>\n";
			echo "    <td align=\"center\"><input name=\"vote_radio\" type=\"radio\" checked value=\"5\"></td>\n";
			echo "   </tr>\n";
			echo "   <tr><td colspan=\"9\" align=\"center\"><img src=\"_imgs/none.gif\" height=\"5\"><br><input type=\"submit\" style=\"CURSOR: hand\" name=\"make_vote\" value=\"Голосовать\"><br><img src=\"_imgs/none.gif\" height=\"5\"></td></tr>\n";
			echo "   </form>\n";
			echo "  </table>\n";
		} else {
			echo "<br>вы уже голосовали<br><br>";
		}
	} else {
		echo "<br>голосовать могут только зарегистрированные пользователи<br><br>";
	}
	echo " </td></tr>\n";
	echo " <tr><td background=\"i/bgr-grid-sand1.gif\" align=\"center\">\n";
	$res = mysql_query("SELECT COUNT(`id`) FROM `tzbattles_comments` WHERE (`battles_uid` = '".$row1["id"]."')");
	$tmp = mysql_fetch_array($res);
	echo "  <a href=\"#; return false;\" onclick=\"opencomments(".$row1["comments_id"].", 1);\">Комментарии (".$tmp[0].")</a>\n";
	echo " </td></tr>\n";
	echo "</table>\n";
	
	if((AuthStatus==1 && AuthUserName==$nick) || (abs(AccessLevel) & AccessTzBattlesModer)) {
		echo "<br><br>\n";
		echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"3\" width=\"450\" style=\"BORDER: 1px #957850 solid;\">\n";
		echo " <tr><td style=\"BORDER-BOTTOM: 1px #957850 solid;\" background=\"i/bgr-grid-sand.gif\" align=\"center\">\n";
		echo "  <b>Управление боями</b>\n";
		echo " </td></tr>\n";
		echo " <tr><td style=\"BORDER-BOTTOM: 1px #957850 solid;\" background=\"i/bgr-grid-sand1.gif\" align=\"left\">\n";
		echo "  Удалить бой:&nbsp;\n";
	
		$result2 = mysql_query("SELECT * FROM `tzbattles_main` WHERE (nick='".$row1["nick"]."') ORDER BY id");
		for ($i = 1; $i <= @mysql_num_rows($result2); $i++) {
			$tmp_row = mysql_fetch_assoc($result2);
			echo "<input type='button' style='CURSOR: hand' value='".$i."' onclick=\"self.location.href='?act=battles_archive&makedelete=".$tmp_row["id"]."'\"> ";
		}
		
		echo " </td></tr>\n";
		echo " <tr><td style=\"BORDER-BOTTOM: 1px #957850 solid;\" background=\"i/bgr-grid-sand.gif\" align=\"center\">\n";
		echo "  <b>Изменить комментарий</b>\n";
		echo " </td></tr>\n";
		echo " <form action=\"\" method=\"post\">\n";
		echo " <tr><td style=\"BORDER-BOTTOM: 1px #957850 solid;\" background=\"i/bgr-grid-sand1.gif\" align=\"left\">\n";
		echo "  Комментарий:&nbsp;<input name=\"act\" type=\"hidden\" value=\"battles_archive\"><input name=\"makechcomment\" type=\"hidden\" value=\"".$row2["id"]."\"><input name=\"newcomment\" type=\"text\" value=\"".$row2["comment"]?."\" size=\"52\">&nbsp;<input type=\"submit\" style=\"CURSOR: hand\" value=\"Изменить\">\n";
		echo " </td></tr>\n";
	/*	echo " <tr><td style=\"BORDER-BOTTOM: 1px #957850 solid;\" background=\"i/bgr-grid-sand.gif\" align=\"center\">\n";
		echo "  <b>Фотография, которая показывается первой</b>\n";
		echo " </td></tr>\n";
		echo " <form action=\"\" method=\"post\">\n";
		echo " <tr><td background=\"i/bgr-grid-sand1.gif\" align=\"left\">\n";
		echo "  Номер фотографии:&nbsp;<input name=\"act\" type=\"hidden\" value=\"fotos\"><input name=\"firstchname\" type=\"hidden\" value=\"".$row1["nick"]."\"><input name=\"newfirst\" type=\"text\" value=\"".$row1["firstfoto"]."\" size=\"3\">&nbsp;<input type=\"submit\" style=\"CURSOR: hand\" name=\"makechfirst\" value=\"Изменить\">\n";
		echo " </td></tr>\n";
	*/	echo " </form>\n";
		echo "</table>\n";
	}
}
?>