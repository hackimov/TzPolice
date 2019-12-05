<script language="javascript">
function pages(pg, link) {
	if (pg > 1) {
		var txt = '<div class="v10">Страницы: ';
		if(pg > 6){
			for(z = 1; z<=3; z++) {
				txt += '<a href="'+link+'&p='+z+'">'+z+'</a> ';
			}
			txt += '... ';
			for(z = pg -2; z<=pg; z++){
				txt += '<a href="'+link+'&p='+z+'">'+z+'</a> ';
			}
		}else{
			for(z = 1; z<=pg; z++){
				txt += '<a href="'+link+'&p='+z+'">'+z+'</a> ';
			}
		}
		txt += '</div>';
	} else {
		var txt = '';
	}
	return txt;
}
</script>
<?
//	if ((abs(AccessLevel) & AccessForum) || (AuthUserClan=='Military Police' && $_REQUEST['f']=='client')) {
	if (abs(AccessLevel) & AccessForum) {
		if($_REQUEST['f']) {
			$f=$_REQUEST['f'];
		//	if ($forum["$f"]['restr'] == AuthUserRestrAccess || AuthUserRestrAccess == 100)
			$tmp ='-'.$forum[$f]['nm'].'-';
			if ($forum[$f]['restr'] < 1 || substr_count(AuthUserRestrAccess, $tmp) > 0 || AuthUserGroup==100) {
?>
    <h1><?=$forum[$f]['name']?></h1>
    <p>
    <a href='?act=forum_threads'>Форумы</a> &raquo;
    <a href='?act=forum_threads&f=<?=$f?>'><?=$forum[$f]['name']?></a> &raquo;
	<a href='?act=forum_thread_add&f=<?=$f?>'>Создать новую тему</a>
    </p>
<?

				if ($_REQUEST['search']) {
					$word = urldecode($_REQUEST['search']);
					$word = str_replace('+', '', $word);
					$word = str_replace('*', '', $word);
					$word = str_replace('.', '', $word);
					$word = str_replace('[', '', $word);
					$word = str_replace(']', '', $word);
					$word = str_replace('(', '', $word);
					$word = str_replace(')', '', $word);
					$word = trim($word);
					
					$SQL="SELECT th.*, u.id AS user_id, u.user_name AS user_name, u.clan FROM forum_threads th
         	LEFT JOIN site_users u ON (th.poster_id=u.id)
         	LEFT JOIN forum_replies re ON (th.id=re.id_thread)

            WHERE th.id_sec='".$_REQUEST['f']."'
            AND
            (th.title REGEXP '^.*" . $word . ".*'
            OR th.text REGEXP '^.*" . $word . ".*'
            OR re.text REGEXP '^.*" . $word . ".*')
            GROUP BY th.id
            ORDER BY is_attached DESC, last_date DESC";
				} else {
					$SQL="SELECT th.*, u.id AS user_id, u.user_name AS user_name, u.clan
         	FROM forum_threads th LEFT JOIN site_users u ON th.poster_id=u.id
            WHERE th.id_sec='".$_REQUEST['f']."'
            ORDER BY is_attached DESC, last_date DESC";
				}
				$r=mysql_query($SQL);
				$nums = mysql_num_rows($r);
				if($nums>0) {
					$pages=ceil($nums/$ThreadsPP);
					if($_REQUEST['p']>0) $p=$_REQUEST['p'];
					else $p=1;
					$LimitParam=$p*$ThreadsPP-$ThreadsPP;
					$SQL .= ' LIMIT '.$LimitParam.', '.$ThreadsPP;
					$r=mysql_query($SQL);
					echo "<br><div align=right class='v10'>Страницы: <b>";
					ShowPages($p,$pages,5,'act=forum_threads&f='.$_REQUEST['f']);
					echo "</b></div>";
?>
	<table width=100% cellpadding=6 cellspacing=1>
	<tr>
		<td width=100% background='i/bgr-grid-sand1.gif' class='v10b'>Темы:</td>
		<td width=110 background='i/bgr-grid-sand1.gif' nowrap class='v10b' align=center>Начата:</td>
		<td width=50 background='i/bgr-grid-sand1.gif' nowrap class='v10b' align=center>views:</td>
		<td width=50 background='i/bgr-grid-sand1.gif' nowrap class='v10b' align=center>replies:</td>
		<td width=110 background='i/bgr-grid-sand1.gif' nowrap class='v10b' align=center>Последний ответ:</td>
	</tr>
<?
					$nums = mysql_num_rows($r);
					for($i=0;$i<$nums;$i++) {
						$d=mysql_fetch_array($r);
?>
	<tr>
	<td width=100% background='i/bgr-grid-sand.gif' valign=top>
        <h4>
<?
//Тута мы посылаем "неэлитную" часть ментов =)
						if($d['is_attached']==1) echo "<img src='i/bullet-red-01a.gif'>";
						else echo "<img src='i/bullet-red-01.gif'>";
						
						$ThId=$d['id'];
						$repl=mysql_fetch_array(mysql_query('SELECT count(id) FROM forum_replies WHERE id_thread="'.$ThId.'"'));
						if ($d['allow_comments']==0) echo '[closed] ';
						echo '<a href="?act=forum_replies&f='.$f.'&th='.$d['id'].'">'.stripslashes($d['title']).'</a></h4><script language="javascript">document.write(pages(\''.ceil($repl[0]/$RepliesPP).'\', \'?act=forum_replies&f='.$f.'&th='.$d['id'].'\'));</script></td><td background="i/bgr-grid-sand1.gif" nowrap class="v9" align=center>'.date("d.m.y H:i",$d['post_date']).'<br>'.GetUser($d['user_id'],$d['user_name'],AuthUserGroup).'</td><td background="i/bgr-grid-sand.gif" nowrap class="v10b" align=center>'.$d['cnt_views'].'</td><td background="i/bgr-grid-sand1.gif" nowrap class="v10b" align=center>';
						$SQL2='SELECT re.post_date, u.id AS user_id, u.user_name, u.clan FROM forum_replies re LEFT JOIN site_users u ON re.poster_id=u.id WHERE re.id_thread="'.$ThId.'" ORDER BY re.post_date DESC LIMIT 1';
						$lr=mysql_fetch_array(mysql_query($SQL2));
						
						echo $repl[0].'</td><td background="i/bgr-grid-sand.gif" nowrap class="v9" align=center>';
						if($lr['post_date']>0) echo date("d.m.y H:i", $lr['post_date']);
						else echo '-';
						echo '<br>'.GetUser($lr['user_id'],$lr['user_name'],AuthUserGroup).'</td></tr>';
					}
				}
?>
    </table>
        <form method="get" action="index.php">
        <input type="hidden" name="act" value="forum_threads">
        <input type="hidden" name="f" value="<?=$_REQUEST['f']?>">
        Поиск: <input type="text" name="search">&nbsp;<input type="submit" value="OK">
        </form>
<?
############ Forums List
#
			}
		} else {
?>
	<h1>Форум полицейского управления TimeZero</h1>
	<table width=100% cellpadding=6 cellspacing=1>
	<tr>
    <td width=100% background='i/bgr-grid-sand1.gif' class='v10b'>
		Название форума:
	</td><td width=60 background='i/bgr-grid-sand1.gif' nowrap class='v10b' align=center>
		Кол-во топиков:
	</td><td width=120 background='i/bgr-grid-sand1.gif' nowrap class='v10b' align=center>
    	Последний топик:
  	</td></tr>
<?
			foreach($forum as $ForumId => $f) {
			//	if ($f['restr'] == AuthUserRestrAccess || AuthUserRestrAccess == 100)
				if ($f['restr'] == 0 || substr_count(AuthUserRestrAccess, $f['nm']) > 0 || AuthUserGroup==100) {
					$last_th=mysql_fetch_array(mysql_query('SELECT MAX(last_date) FROM forum_threads WHERE id_sec="'.$ForumId.'"'));
					$cnt_th=mysql_fetch_array(mysql_query('SELECT count(id) FROM forum_threads WHERE id_sec="'.$ForumId.'"'));
?>
				<tr>
		        <td width=100% background='i/bgr-grid-sand.gif'>
	    		    <h3><a href='?act=forum_threads&f=<?=$ForumId?>'><?=$f['name']?></a></h3>
					<div class='v10'><?=$f['desc']?></div>
        		</td><td background='i/bgr-grid-sand1.gif' align=center class='v11b'>
				<?=$cnt_th[0]?>
		        </td><td background='i/bgr-grid-sand.gif' align=center class='v9'>
			<?=date("d.m.y H:i", $last_th[0])?>
		        </td>
		        </tr>
<?
				}
			}
?>
	</table>
<?
		}
	} else echo $mess['AccessDenied'];
?>