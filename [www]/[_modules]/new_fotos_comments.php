<?if (substr_count(AuthUserRestrAccess, 'commentcheck') > 0){?>
<h1>Непроверенные комментарии к фотографиям</h1>
<?php
	error_reporting(0);
	if(@$_REQUEST['do']=='process') {
		$fucking_checkboxes = $_REQUEST['bad'];
		$DelText = '[red]Удалено полицейским[/red] [clan]police[/clan]<b>'.GetUser(AuthUserId,AuthUserName,AuthUserGroup).'</b>';
		
		foreach ($fucking_checkboxes as $cur_id => $val) {
			if($val == 1) {
				$SQL = 'UPDATE `fotos_comments` SET `comment_text`=\''.addslashes($DelText).'\', checked=\'1\' WHERE id=\''.$cur_id.'\' LIMIT 1;';
			}else{
				$SQL = 'UPDATE `fotos_comments` SET `checked`=\'1\' WHERE `id`=\''.$cur_id.'\' LIMIT 1;';
			}
			mysql_query($SQL);
		}
		
	//	$fucking_checkboxes = $_REQUEST['ok'];
	//	foreach ($fucking_checkboxes as $cur_id) {
	//		$SQL = 'UPDATE `fotos_comments` SET `checked`=\'1\' WHERE `id`=\''.$cur_id.'\' LIMIT 1;';
	//		mysql_query($SQL);
	//	}
	}
	
	$SQL = 'SELECT `id` FROM `fotos_comments` WHERE `checked`=\'0\'';
	$r=mysql_query($SQL);
	if(mysql_num_rows($r)<1) echo $mess['NoComments'];
	else {
		$pages=ceil(mysql_num_rows($r)/$CommentsPerPage);
		if($_REQUEST['p']>0) $p=$_REQUEST['p'];
		else $p=1;
		$SQL="SELECT c.*,u.id AS user_id, u.user_name AS user_name, u.clan FROM fotos_comments c LEFT JOIN site_users u ON c.id_user=u.id WHERE  c.checked='0'";
		
		$LimitParam=$p*$CommentsPerPage-$CommentsPerPage;
		$SQL.=' LIMIT '.$LimitParam.', '.$CommentsPerPage;
		$r = mysql_query($SQL);
		echo '<br><div align=right>Страницы: <b>';
		ShowPages($p, $pages, 4, 'act=new_fotos_comments');
		echo '</b></div>';
?>
        <form name="check_comm" method="POST" action="?act=new_fotos_comments">
        <input type="hidden" name="do" value="process">
<?
		$n_size = mysql_num_rows($r);
		for($i=0; $i<$n_size; $i++) {
			$d = mysql_fetch_assoc($r);
			echo '<br><table width="100%" border="0" cellspacing="3" cellpadding="0"><tr>
				<td height="20" background="i/bgr-grid-sand.gif"><p><img src="i/bullet-red-01.gif" width="18" height="11" hspace="5">
            ';
            echo ' '.date("d.m.Y - H:i", $d['comment_date']).' / '.GetClan($d['clan']).GetUser($d['user_id'],$d['user_name'],AuthUserGroup).':
            &nbsp;&nbsp;&nbsp;&nbsp;<a href="?act=fotos_comments&DataId='.$d['id_data'].'" target="_blank">[источник]</a></p></td>
				</tr><tr><td>
		    ';
            ParseNews($d['comment_text'],0);
            echo '<br><br><input name="bad['.$d['id'].']" type="radio" value="0" CHECKED>OK &nbsp; &nbsp; <input name="bad['.$d['id'].']" type="radio" value="1">Бяка</td></tr></table>';
        }
    }
?>
<br>
<input type="Submit" value="Обработать"></form>
<?
echo '<div align=right>Страницы: <b>';
ShowPages($p,$pages, 4, 'act=new_fotos_comments');
echo '</b></div>';
}
?>