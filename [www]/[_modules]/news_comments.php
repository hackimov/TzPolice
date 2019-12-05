<h1>Комментарии к новости</h1>
<?php
if (AuthUserClan=='police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy' || AuthUserClan=='Tribunal' || AuthUserClan=='Military Police') {
	$v = '';
	$vstr = '<strong style="color: green">[внутренняя] </strong>';
} else {
	$v = " AND our_news='0'";
	$vstr = '';
}
if(@$_REQUEST['added']==1) echo "<div class=green>Комментарий добавлен</div><br>";
if(@$_REQUEST['IdNews']) {
        if(@$_REQUEST['do']=="close" && AuthUserGroup>1) {
                $DelText="[red]Обсуждение закрыто полицейским[/red] [clan]police[/clan]<b>".GetUser(AuthUserId,AuthUserName,AuthUserGroup)."</b>";
                $SQL="INSERT INTO comments (id_news,id_user,comment_date,comment_text) values('".$_REQUEST['IdNews']."','".AuthUserId."','".time()."','".addslashes($DelText)."')";
                mysql_query($SQL);
                $SQL="UPDATE news SET allow_comments='0' WHERE id='".$_REQUEST['IdNews']."'";
                mysql_query($SQL);
                echo "<script>top.location.href='?act=news_comments&IdNews=".$_REQUEST['IdNews']."&p=".$_REQUEST['p']."'</script>";
        } elseif(@$_REQUEST['do']=="open" && AuthUserGroup>1) {
                $DelText="[green]Обсуждение продолжено полицейским[/green] [clan]police[/clan]<b>".GetUser(AuthUserId,AuthUserName,AuthUserGroup)."</b>";
                $SQL="INSERT INTO comments (id_news,id_user,comment_date,comment_text) values('".$_REQUEST['IdNews']."','".AuthUserId."','".time()."','".addslashes($DelText)."')";
                mysql_query($SQL);
            $SQL="UPDATE news SET allow_comments='1' WHERE id='".$_REQUEST['IdNews']."'";
            mysql_query($SQL);
            echo "<script>top.location.href='?act=news_comments&IdNews=".$_REQUEST['IdNews']."&p=".$_REQUEST['p']."'</script>";
        }
        if(@$_REQUEST['do']=="delete" && @$_REQUEST['IdComment'] && AuthUserGroup>1) {
        $DelText="[red]Удалено полицейским[/red] [clan]police[/clan]<b>".GetUser(AuthUserId,AuthUserName,AuthUserGroup)."</b>";
                $SQL="UPDATE comments SET comment_text='".addslashes($DelText)."' WHERE id='".$_REQUEST['IdComment']."'";
        mysql_query($SQL);
        echo "<script>top.location.href='?act=news_comments&IdNews=".$_REQUEST['IdNews']."&p=".$_REQUEST['p']."'</script>";
    }
        if(@$_REQUEST['do']=="add" && @$_REQUEST['NewsText']) {
                $SQL="SELECT allow_comments, our_news FROM news WHERE id='".$_REQUEST['IdNews']."'";
                $d=mysql_fetch_array(mysql_query($SQL));
        $AllowComments=$d['allow_comments'];
		if ($d['our_news'] && !(AuthUserClan=='police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy' || AuthUserClan=='Tribunal' || AuthUserClan=='Military Police')) $AllowComments=0;
                $SQL="SELECT banned, post_time FROM site_users WHERE id='".AuthUserId."'";
                $d=mysql_fetch_array(mysql_query($SQL));
//        echo $d['post_time']."+30 < ".time();
                if($d['banned']>time()) {
                        $MolDiff=$d['banned']-time();
                    echo "<h2>Вы не можете оставлять комментарии еще ".gmdate("zдн. Hч iм sс",$MolDiff)."</h2><br><br>";
                } elseif($d['post_time']+30>time()) {
                $oldcomm=stripslashes($_REQUEST['NewsText']);
                    echo "<h2>Вы не можете оставлять комментарии чаще, чем раз в 30 секунд</h2><br><br>";
                } elseif ($AllowComments==0) {
                echo "<h2>Обсуждение закрыто</h2><br><br>";
                } else {

                        $SQL="INSERT INTO comments (id_news,id_user,comment_date,comment_text) values('".$_REQUEST['IdNews']."','".AuthUserId."','".time()."','".addslashes($_REQUEST['NewsText'])."')";
                        mysql_query($SQL);
            $SQL="UPDATE site_users SET post_time='".time()."' WHERE id='".AuthUserId."'";
            mysql_query($SQL);
                    echo "<script>top.location.href='?act=news_comments&IdNews=".$_REQUEST['IdNews']."&p=".$_REQUEST['p']."'</script>";
            }
        }
$SQL="SELECT * FROM news WHERE is_visible=1".$v." AND id='".$_REQUEST['IdNews']."'";
$r=mysql_query($SQL);
if(mysql_num_rows($r)<1) echo $mess['NewsNotFound'];
else {
        $d=mysql_fetch_array($r);
        $AllowComments=$d['allow_comments'];
        if($d['allow_tags']==0) $allow_tags=0;
        else $allow_tags=1;
    echo "
		<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
		<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>[".date("d.m.Y - H:i",$d['news_date'])."] </strong> ".($d['our_news']?$vstr:'')."<span class='top-header'>".stripslashes(strip_tags($d['news_title']))."</span></p></td>
		</tr><tr><td>
    ";
    if ($d['markup'] == 0)
    	{
		    ParseNews($d['news_text'],$d['allow_tags']);
        }
    elseif ($d['markup'] == 1)
    	{
            ParseNews2($d['news_text']);
        }
	echo "
		</td></tr><tr>
		<td height=20 background='i/bgr-grid-sand1.gif'>
		<strong> Комментарии: </strong>
        </td></tr></table>
	";
        $SQL="SELECT c.*,u.id AS user_id, u.user_name AS user_name, u.clan FROM comments c LEFT JOIN site_users u ON c.id_user=u.id WHERE  c.id_news='".$_REQUEST['IdNews']."' ORDER BY c.comment_date ";
    $r=mysql_query($SQL);
        if(mysql_num_rows($r)<1) echo $mess['NoComments'];
    else {
         $pages=ceil(mysql_num_rows($r)/$CommentsPerPage);
        if($_REQUEST['p']>0) $p=$_REQUEST['p'];
        else $p=1;
        $LimitParam=$p*$CommentsPerPage-$CommentsPerPage;
        $SQL.=" LIMIT $LimitParam, $CommentsPerPage";
        $r=mysql_query($SQL);
        echo "<br><div align=right>Страницы: <b>";
        ShowPages($p,$pages,4,"act=news_comments&IdNews={$_REQUEST['IdNews']}");
        echo "</b></div>";
        for($i=0;$i<mysql_num_rows($r);$i++) {
            $d=mysql_fetch_array($r);
	    echo "
				<br><table width='100%' border='0' cellspacing='3' cellpadding='0'><tr>
				<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'>
            ";
            if(AuthUserGroup>1) echo "<a href='#;return false' onclick=\"if(confirm('Удалить комментарий?')){top.location='?act=news_comments&IdNews={$d[id_news]}&IdComment={$d[id]}&do=delete&p={$p}'}\">удалить</a> / ";
            echo " ".date("d.m.Y - H:i",$d['comment_date'])." / ";
            if(AuthStatus==1) echo "<a href=\"JavaScript:insertAtCaret(top.News.NewsText, '<b>".$d['user_name'].",</b> ')\"><img border=0 src='/i/to.gif'></a>";
            echo GetClan($d['clan']).GetUser($d['user_id'],$d['user_name'],AuthUserGroup).":
                </p></td>
				</tr><tr><td>
		    ";
            ParseNews($d['comment_text'],0);
            echo "</td></tr></table>";
        }
    }
?>
<br>
<?
echo "<div align=right>Страницы: <b>";
ShowPages($p,$pages,4,"act=news_comments&IdNews={$_REQUEST['IdNews']}");
echo "</b></div>";
if(AuthUserGroup>1 && $AllowComments!=0) echo "<div>[ <a href='?act=news_comments&do=close&IdNews=".$_REQUEST['IdNews']."&p=$p'>Закрыть обсуждение</a> ]</div>";
if(AuthUserGroup>1 && $AllowComments==0) echo "<div>[ <a href='?act=news_comments&do=open&IdNews=".$_REQUEST['IdNews']."&p=$p'>Продолжить обсуждение</a> ]</div>";
if(AuthStatus<1) echo $mess['CantAddComment'].$mess['WantRegister'];
else {
        $SQL="SELECT banned FROM site_users WHERE id='".AuthUserId."'";
        $d=mysql_fetch_array(mysql_query($SQL));
        if($d['banned']>time()) {
                $MolDiff=$d['banned']-time();
            echo "<h2>Вы не можете оставлять комментарии еще ".gmdate("zдн. Hч iм sс",$MolDiff)."</h2><br><br>";
        } else {
            if($AllowComments==0) echo "<h2>Обсуждение закрыто</h2>";
        else {
?>
<table width='100%' border='0' cellspacing='3' cellpadding='5'>
<td height=20 background='i/bgr-grid-sand1.gif'>
<strong>Оставить комментарий:</strong>
</td></tr></table>
<table width=100% cellpadding=5>
<tr>
<td valign=top>

        <form method="POST" name="News">
        <input name="act" type="hidden" value="news_comments">
        <input name="do" type="hidden" value="add">
           <input name="NewsId" type="hidden" value="<?=$_REQUEST['IdNews']?>">
    <input name="p" type="hidden" value="<?=$p?>">
    Текст: <Br>
        <textarea ONSELECT="storeCaret(this)" ONCLICK="storeCaret(this)" ONKEYUP="storeCaret(this)" name="NewsText" style="width:100%" rows=10><?=@htmlspecialchars(stripslashes($d['news_text']))?><?=$oldcomm?></textarea><br>
        <img src="/_imgs/b.gif" border=0 onclick="decor('b')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы сделать его полужирным">
        <img src="/_imgs/i.gif" border=0 onclick="decor('i')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы выделить его курсивом">
        <img src="/_imgs/u.gif" border=0 onclick="decor('u')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы сделать его подчеркнутым">
        <img src="/_imgs/center.gif" border=0 onclick="decor('div align=center')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы поместить его по центру">
        <br><br>
        <input type="submit" value="Прокомментировать">
        </form>

</td><td width=200 valign=top>

        Вставить: <br>
    <select size="1" onchange="document.frames['icons'].location='_modules/icons/'+this.options[this.selectedIndex].value+'.php'" style="width:100%">
                <option value="blank" selected>-- выберите категорию --</option>
        <option value="smiles"> Смайлики</option>
        <option value="clans"> Кланы</option>
        <option value="prof"> Профессии</option>
        <option value="login"> Ник</option>
        <option value="url"> URL</option>
        </select>
    <iframe BORDER=0 FRAMEBORDER=0 id="icons" src="_modules/icons/blank.php" width=100% HEIGHT=105 style="border:1 solid #000000">
</td></tr>
<table>
<?}}}?>
<?
} // news found
}
?>