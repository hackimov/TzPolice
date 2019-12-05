<h1>Новости</h1>

<?php
#    NEWS TOP BLOCK

if(AuthStatus==1 && AuthUserGroup>1) {

    if(@$_REQUEST['IdNews']) {
    switch(@$_REQUEST['do']) {

        case 'delete': $SQLquery="DELETE FROM news WHERE id='{$_REQUEST['IdNews']}'"; $StatusQuery="<div class=green>Новость <b>ID:{$_REQUEST['IdNews']}</b> удалена</div>"; break;
        case 'attach': $SQLquery="UPDATE news SET is_attached='1' WHERE id='".$_REQUEST['IdNews']."'"; $StatusQuery="<div class=green>Новость <b>ID:{$_REQUEST['IdNews']}</b> закреплена</div>"; break;
        case 'unattach': $SQLquery="UPDATE news SET is_attached='0' WHERE id='".$_REQUEST['IdNews']."'"; $StatusQuery="<div class=green>Новость <b>ID:{$_REQUEST['IdNews']}</b> откреплена</div>"; break;

    }}

    mysql_query($SQLquery);
    echo $StatusQuery;

}

# begin output of attached

$SQL="SELECT count(c.id) AS cnt, n.*, u.id AS PosterId, u.user_name AS PosterName, u.clan AS PosterClan FROM news n LEFT JOIN site_users u ON n.poster_id=u.id LEFT JOIN comments c ON c.id_news=n.id WHERE n.is_attached=1 AND n.is_visible=1 GROUP BY c.id_news ORDER BY n.news_date DESC";
$r=mysql_query($SQL);
while($d=mysql_fetch_array($r)) {

    echo "
		<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
		<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><strong>[прикреплено] </strong> <span class='top-header'>".stripslashes($d['news_title'])."</span></p></td>
		</tr><tr><td>
    ";

    ParseNews($d['news_text'],$d['allow_tags']);

	echo "
		</td></tr><tr>
		<td height=20 align='right' background='i/bgr-grid-sand1.gif'>
		Автор: <strong>".GetUser($d['PosterId'],$d['PosterName'],AuthUserGroup)."</strong> /
	";

    if(AuthUserGroup>1) {
       $SQL2="SELECT id,clan,user_name FROM site_users WHERE id='".$d['checker_id']."'";
       $rr=mysql_query($SQL2);
       $dd=mysql_fetch_array($rr);
       echo "
            подтверждена: ".GetUser($dd['id'],$dd['user_name'],AuthUserGroup)." /
            <a href='?act=news_edit&IdNews={$d['id']}'>редактировать</a> /
            <a href='?act=news&IdNews={$d['id']}&do=unattach'>открепить</a> /
            <a onclick=\"if(confirm('Удалить новость из базы?')){top.location='?act=news&IdNews={$d['id']}&do=delete'}\" href='#;return false'>удалить</a> /
       ";
    }

	echo "
		<a href='?act=news_comments&IdNews={$d['id']}'>Комментарии: {$d['cnt']}</a> </td>
		</tr></table>
	";

}

// end of output of attached


//$SQL="SELECT count(c.id) AS cnt, n.*, u.id AS PosterId, u.user_name AS PosterName, u.clan AS PosterClan FROM news n LEFT JOIN site_users u ON n.poster_id=u.id LEFT JOIN comments c ON c.id_news=n.id WHERE n.is_attached<>1 AND n.is_visible=1 GROUP BY c.id_news ORDER BY n.news_date DESC LIMIT 0,10";
$SQL="SELECT n.*, u.id AS PosterId, u.user_name AS PosterName, u.clan AS PosterClan FROM news n LEFT JOIN site_users u ON n.poster_id=u.id WHERE n.is_attached<>1 AND n.is_visible=1 ORDER BY n.news_date DESC ";
$r=mysql_query($SQL);

$pages=ceil(mysql_num_rows($r)/$NewsPP);
if($_REQUEST['p']>0) $p=$_REQUEST['p'];
else $p=1;

$LimitParam=$p*$NewsPP-$NewsPP;

$SQL.=" LIMIT $LimitParam, $NewsPP";
$r=mysql_query($SQL);

while($d=mysql_fetch_array($r)) {


    echo "
		<table width='100%' border='0' cellspacing='3' cellpadding='3'><tr>
		<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><strong>[".date("d.m.Y - H:i",$d['news_date'])."] </strong> <span class='top-header'>".strip_tags(stripslashes($d['news_title']))."</span></p></td>
		</tr><tr><td>
    ";

    ParseNews($d['news_text'],$d['allow_tags']);

	echo "
		</td></tr><tr>
		<td height=20 align='right' background='i/bgr-grid-sand1.gif'>
		Автор: <strong>".GetUser($d['PosterId'],$d['PosterName'],AuthUserGroup)."</strong> /
	";

    if(AuthUserGroup>1) {
       $SQL2="SELECT id,clan,user_name FROM site_users WHERE id='".$d['checker_id']."'";
       $rr=mysql_query($SQL2);
       $dd=mysql_fetch_array($rr);
       echo "
            подтверждена: ".GetUser($dd['id'],$dd['user_name'],AuthUserGroup)." /
            <a href='?act=news_edit&IdNews={$d['id']}'>редактировать</a> /
            <a href='?act=news&IdNews={$d['id']}&do=attach'>прикрепить</a> /
            <a onclick=\"if(confirm('Удалить новость из базы?')){top.location='?act=news&IdNews={$d['id']}&do=delete'}\" href='#;return false'>удалить</a> /
       ";
    }

    $com=mysql_fetch_array(mysql_query("SELECT count(id) as cnt FROM comments WHERE id_news='".$d['id']."'"));
	echo "
		<a href='?act=news_comments&IdNews={$d['id']}'>Комментарии: {$com['cnt']}</a> </td>
		</tr></table>
	";

}

echo "<br><p align=right>Страницы: <b>";
ShowPages($p,$pages,5,"act=news");
echo "</b></p>";

?>