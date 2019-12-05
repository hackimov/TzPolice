<?php
$_RESULT = array("res" => "ok");
require_once "../xhr_config.php";
require_once "../xhr_php.php";
require_once "../mysql.php";
require_once "../functions.php";
require_once "../auth.php";
$JsHttpRequest =& new Subsys_JsHttpRequest_Php("windows-1251");
?>

<h1>Комментарии к боям</h1>
<table border="0" cellspacing="0" cellpadding="10" width="100%">
 <tr>
  <td valign="top" >

<?php
if(@$_REQUEST['added']==1) echo "<div class=green>Комментарий добавлен</div><br>";
if(@$_REQUEST['DataId']) {
	if(@$_REQUEST['doo']=="close" && (abs(AccessLevel) & AccessTzBattlesModer)) {
		$SQL="UPDATE `site_data` SET allow_comments='0' WHERE id='".$_REQUEST['DataId']."'";
		mysql_query($SQL);
		
		echo "<script>top.location.href='?act=battles_comments&DataId=".$_REQUEST['DataId']."&p=".$_REQUEST['p']."'</script>";
	
	} elseif(@$_REQUEST['doo']=="open" && (abs(AccessLevel) & AccessTzBattlesModer)) {
		$SQL="UPDATE `site_data` SET allow_comments='1' WHERE id='".$_REQUEST['DataId']."'";
		mysql_query($SQL);
		echo "<script>top.location.href='?act=battles_comments&DataId=".$_REQUEST['DataId']."&p=".$_REQUEST['p']."'</script>";
	
	}
	
	if(@$_REQUEST['doo']=="delete" && @$_REQUEST['IdComment'] && (abs(AccessLevel) & AccessTzBattlesModer)) {
		$DelText="[red]Удалено полицейским[/red] [clan]police[/clan]<b>".GetUser(AuthUserId,AuthUserName,AuthUserGroup)."</b>";
		$SQL="UPDATE `tzbattles_comments` SET `text`='".addslashes($DelText)."' WHERE `id`='".$_REQUEST['IdComment']."'";
		mysql_query($SQL);
		echo "<script>top.location.href='?act=battles_comments&DataId=".$_REQUEST['DataId']."&p=".$_REQUEST['p']."'</script>";
	}
	
	if(@$_REQUEST['doo']=="add" && @$_REQUEST['NewsText']) {
		$SQL="SELECT `allow_comments` FROM `tzbattles_users` WHERE `id`='".$_REQUEST['DataId']."'";
		$d=mysql_fetch_array(mysql_query($SQL));
		$AllowComments=$d['allow_comments'];
		
		$SQL="SELECT banned, post_time FROM `site_users` WHERE id='".AuthUserId."'";
		$d=mysql_fetch_array(mysql_query($SQL));
		if($d['banned']>time()) {
			$MolDiff=$d['banned']-time();
			echo "<h2>Вы не можете оставлять комментарии еще ".gmdate("zдн. Hч iм sс",$MolDiff)."</h2><br><br>";
		
		} elseif($d['post_time']+30>time()) {
			$oldcomm=stripslashes($_REQUEST['NewsText']);
			echo "<h2>Вы не можете оставлять комментарии чаще, чем раз в 30 секунд</h2><br><br>";
		
		} elseif ($AllowComments==0) {
			echo "<h2>Обсуждение закрыто</h2><br><br>";
		
		} else {
			$SQL="INSERT INTO `tzbattles_comments` (battles_uid, uid, `time`, `text`) values('".$_REQUEST['DataId']."','".AuthUserId."','".time()."','".addslashes($_REQUEST['NewsText'])."')";
			mysql_query($SQL);
			$SQL="UPDATE `site_users` SET post_time='".time()."' WHERE id='".AuthUserId."'";
			mysql_query($SQL);
			
			echo "<script>top.location.href='?act=battles_comments&DataId=".$_REQUEST['DataId']."&p=".$_REQUEST['p']."'</script>";
		}
	}
	
	$SQL="SELECT * FROM `tzbattles_users` WHERE `id`='".$_REQUEST['DataId']."'";
	$r=mysql_query($SQL);
	if(mysql_num_rows($r)<1)
		echo $mess['NewsNotFound'];
	else {
		$d=mysql_fetch_array($r);
		$AllowComments=$d['allow_comments'];
/*
	echo " <table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
		<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Обсуждение фотографий: </strong> <a href='http://www.tzpolice.ru/?act=fotos&nick={$d['nick']}'>{$d['nick']}</span></p></td>
		</tr><tr><td>
		</tr></table>";
*/

		$SQL="SELECT c.*,u.id AS uid FROM tzbattles_comments c LEFT JOIN site_users u ON c.id_user=u.id WHERE  c.battles_uid='".$_REQUEST['DataId']."' ORDER BY c.time";
		$r=mysql_query($SQL);
		if(mysql_num_rows($r)<1)
			echo $mess['NoComments'];
		else {
			$pages=ceil(mysql_num_rows($r)/$CommentsPerPage);
			if($_REQUEST['p']>0) $p=$_REQUEST['p'];
			else $p=1;
			
			$LimitParam=$p*$CommentsPerPage-$CommentsPerPage;
			$SQL.=" LIMIT ".$LimitParam.", ".$CommentsPerPage."";
			$r=mysql_query($SQL);
			
			echo "<br><div align=right>Страницы: <b>";
			ShowPagesComm($p,$pages,4,$_REQUEST['DataId']);
			echo "</b></div>";
			
			for($i=0;$i<mysql_num_rows($r);$i++) {
				$d=mysql_fetch_array($r);
				echo " <br><table width='100%' border='0' cellspacing='3' cellpadding='0'><tr>\n";
				echo "  <td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'>\n";
				
				if(abs(AccessLevel) & AccessTzBattlesModer)
					echo "<a href='#;return false' onclick=\"if(confirm('Удалить комментарий?')){top.location='?act=battles_comments&DataId=".$d["battles_uid"]."&IdComment=".$d["id"]."&doo=delete&p=".$p."'}\">удалить</a> / ";
				echo " ".date("d.m.Y - H:i",$d['time'])." / ".GetClan($d['clan']).GetUser($d['uid'],$d['user_name'],AuthUserGroup).": </p></td>\n";
				echo " </tr><tr><td>";
				
				//	$ctext = wordwrap($d['comment_text'], 50, "<wbr>", 1);
				$ctext=$d['comment_text'];
				$max=40; # max разрешенная длина слова
				$term='<wbr>'; # чем разбивать длинные слова
				$ctext=preg_replace('/([^ \n\r\t]{'.$max.'})/m','$1'.$term,$ctext);
				ParseNews($ctext,0);
				echo "</td></tr></table>";
			}
		}

		echo "<br>\n";
		echo "<br><div align=right>Страницы: <b>";
		ShowPagesComm($p,$pages,4,$_REQUEST['DataId']);
		echo "</b></div>";
		echo "</td></tr></table>\n";
		
		if((abs(AccessLevel) & AccessTzBattlesModer) && $AllowComments!=0) echo "<div>[ <a href='?act=battles_comments&doo=close&DataId=".$_REQUEST['DataId']."&p=$p'>Закрыть обсуждение</a> ]</div>";
		if((abs(AccessLevel) & AccessTzBattlesModer) && $AllowComments==0) echo "<div>[ <a href='?act=battles_comments&doo=open&DataId=".$_REQUEST['DataId']."&p=$p'>Продолжить обсуждение</a> ]</div>";
		if(AuthStatus!=1) echo $mess['CantAddComment'].$mess['WantRegister'];
		else {
			$SQL="SELECT banned FROM `site_users` WHERE id='".AuthUserId."'";
			$d=mysql_fetch_array(mysql_query($SQL));
			if($d['banned']>time()) {
				$MolDiff=$d['banned']-time();
				echo "<h2>Вы не можете оставлять комментарии еще ".gmdate("zдн. Hч iм sс",$MolDiff)."</h2><br><br>";
			} else {
				if($AllowComments==0) echo "<h2>Обсуждение закрыто</h2>";
				else {
					echo "<table width='100%' border='0' cellspacing='3' cellpadding='5'>\n";
					echo " <td height=20 background='i/bgr-grid-sand1.gif'>\n";
					echo "  <strong>Оставить комментарий:</strong>\n";
					echo "</td></tr></table>\n";
					echo "<table width=100% cellpadding=5>\n";
					echo " <tr>\n";
					echo "  <td valign=top>\n";
					echo "   <form method=\"POST\" name=\"News\">\n";
					echo "   <input name=\"act\" type=\"hidden\" value=\"battles_comments\">\n";
					echo "   <input name=\"doo\" type=\"hidden\" value=\"add\">\n";
					echo "   <input name=\"DataId\" type=\"hidden\" value=\"".$_REQUEST['DataId']."\">\n";
					echo "   <input name=\"p\" type=\"hidden\" value=\"".$p."\">\n";
					echo "   Текст комментария: <Br>\n";
					echo "   <textarea ONSELECT=\"storeCaret(this)\" ONCLICK=\"storeCaret(this)\" ONKEYUP=\"storeCaret(this)\" name=\"NewsText\" style=\"width:100%\" rows=10>".@htmlspecialchars(stripslashes($d["text"]))."".$oldcomm."</textarea><br>\n";
					echo "   <img src=\"/_imgs/b.gif\" border=0 onclick=\"decor('b')\" style=\"cursor:hand\" ALT=\"Выделите участок текста и нажмите, чтобы сделать его полужирным\">\n";
					echo "   <img src=\"/_imgs/i.gif\" border=0 onclick=\"decor('i')\" style=\"cursor:hand\" ALT=\"Выделите участок текста и нажмите, чтобы выделить его курсивом\">\n";
					echo "   <img src=\"/_imgs/u.gif\" border=0 onclick=\"decor('u')\" style=\"cursor:hand\" ALT=\"Выделите участок текста и нажмите, чтобы сделать его подчеркнутым\">\n";
					echo "   <img src=\"/_imgs/center.gif\" border=0 onclick=\"decor('div align=center')\" style=\"cursor:hand\" ALT=\"Выделите участок текста и нажмите, чтобы поместить его по центру\">\n";
					echo "   <br><br>\n";
					echo "   <input type=\"button\" onclick=\"newcomment();\" value=\"Прокомментировать\" />\n";
					echo "   </form>\n";
					echo "  </td><td width=200 valign=top>\n";
					echo "   Вставить: <br>\n";
					echo "   <select size=\"1\" onchange=\"document.frames['icons'].location='_modules/icons/'+this.options[this.selectedIndex].value+'.php'\" style=\"width:100%\">\n";
					echo "    <option value=\"blank\" selected>-- выберите категорию --</option>\n";
					echo "    <option value=\"smiles\"> Смайлики</option>\n";
					echo "    <option value=\"clans\"> Кланы</option>\n";
					echo "    <option value=\"prof\"> Профессии</option>\n";
					echo "    <option value=\"login\"> Ник</option>\n";
					echo "    <option value=\"url\"> URL</option>\n";
					echo "   </select>\n";
					echo "   <iframe BORDER=0 FRAMEBORDER=0 id=\"icons\" src=\"_modules/icons/blank.php\" width=100% HEIGHT=105 style=\"border:1 solid #000000\">\n";
					echo "  </td></tr>\n";
					echo "</table>\n";
				}
			}
		}
	}
}

?>