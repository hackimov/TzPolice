<h1>ѕодтверждение новостей</h1>

<?php

if((AuthStatus==1 && AuthUserName!="" && AuthUserGroup>1) || (abs(AccessLevel) & AccessNewsEditor)) {

$cur_time = time();

    if(@$_REQUEST['IdNews']) {

    switch(@$_REQUEST['do']) {

        case 'delete': $SQLquery="DELETE FROM news WHERE id='{$_REQUEST['IdNews']}'"; break;

        case 'confirm': $SQLquery="UPDATE news SET news_date='".$cur_time."', is_visible='1', checker_id='".AuthUserId."' WHERE id='".$_REQUEST['IdNews']."'"; break;

        case 'confirm_attach': $SQLquery="UPDATE news SET is_visible='1', is_attached='1', checker_id='".AuthUserId."' WHERE id='".$_REQUEST['IdNews']."'"; break;

    }}

	mysql_query($SQLquery);

	if (AuthUserClan=='police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy' || AuthUserClan=='Tribunal') $v = '';
	elseif (AuthUserClan=='Military Police') $v = ' AND (n.our_news=\'0\' OR n.our_news=\'2\')';
	else $v = " AND n.our_news='0'";

	$SQL="SELECT n.*,u.user_name,u.clan,u.id as uid FROM news n LEFT JOIN site_users u ON n.poster_id=u.id WHERE n.is_visible='0'".$v." ORDER BY n.id DESC";

	$r=mysql_query($SQL);

    echo "

    	<Br><div><b>Ќовостей, ожидающих подтверждени€: ".mysql_num_rows($r)."</b></div><br>

    ";

    while($d=mysql_fetch_array($r)) {

		$clan=GetClan($d['clan']);

        $nick=GetUser($d['uid'],$d['user_name'],AuthUserGroup);

	    echo "

			<table width='100%' border='0' cellspacing='3' cellpadding='3'><tr>

			<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'><strong>[".date("d.m.y H:i",$d['news_date'])."] </strong> <span class='top-header'>".stripslashes($d['news_title'])."</span></p></td>

			</tr><tr><td>

	    ";

    if ($d['markup'] == 0)

    	{

		    ParseNews($d['news_text'],$d['allow_tags']);

            $markup_vers = "";

        }

    elseif ($d['markup'] == 1)

    	{

            ParseNews2($d['news_text']);

            $markup_vers = "2";

        }

		echo "

	        </td></tr><tr>

			<td height=20 align='right' background='i/bgr-grid-sand1.gif'>

			јвтор: <strong>$clan $nick</strong> /

            <a href='#; return false' onclick=\"if(confirm('”далить новость?')){top.location='?act=news_validate&IdNews={$d[id]}&do=delete'}\">удалить</a> /

			<a href='?act=news_validate&IdNews={$d['id']}&do=confirm'>подтвердить</a> /

			<a href='?act=news_validate&IdNews={$d['id']}&do=confirm_attach'>подтвердить и закрепить</a> /

			<a href='?act=news_edit".$markup_vers."&IdNews={$d['id']}'>редактировать</a>

        	</td></tr></table><br>

        ";

    }

} else {

	echo $mess['AccessDenied'];

    echo $mess['WantRegister'];

}

?>