<?
if ($_REQUEST['type'] == 'manuals' || $_REQUEST['type'] == 'manuals2' || $_REQUEST['type'] == 'art')
	{
		$new_uri = str_replace('data', 'data2', $_SERVER['REQUEST_URI']);
		echo ("<script>document.location.href='".$new_uri."';</script>Если не произошло автоматического перехода, щелкните по <a href='http://www.tzpolice.ru".$new_uri."'>этой ссылке</a>");
		exit;
	}
if($_REQUEST['type']) {
	if($_REQUEST['Id']){
		$_REQUEST['Id'] = intval($_REQUEST['Id']);
	}
	$DataType = $_REQUEST['type'];
	if ($DataType=='copsmanual' && (AuthUserClan!=='police' && AuthUserGroup!=='100')) {
		echo '<h2>Раздел не существует</h2>';
	} else {
		echo '<h1>'.$SecName[$DataType].'</h1>';
		if(abs(AccessLevel) & AccessArticles) {
			if(@$_REQUEST['Id'] && @$_REQUEST['do']=='delete') {
				$SQLquery = 'DELETE FROM `site_data` WHERE `id`=\''.$_REQUEST['Id'].'\'';
				$StatusQuery='<div class=green>Материал <b>ID:'.$_REQUEST['Id'].'</b> удален</div>';
			}
			mysql_query($SQLquery);
			echo $StatusQuery;
			
		}
//		$SQL='SELECT sd.*, u.id as PosterId, u.user_name AS PosterName, u.clan AS PosterClan FROM `site_data` AS sd LEFT JOIN `site_users` AS `u` ON sd.poster_id=u.id WHERE sd.id_sec=\''.$DataType.'\' AND sd.markup=\'0\' ORDER BY sd.post_date DESC';
		$SQL='SELECT sd.*, u.id as PosterId, u.user_name AS PosterName, u.clan AS PosterClan FROM `site_data` AS sd LEFT JOIN `site_users` AS `u` ON sd.poster_id=u.id WHERE sd.id_sec=\''.$DataType.'\' ORDER BY sd.post_date DESC';
		$r = mysql_query($SQL);
		
		if(mysql_num_rows($r)>1) {
			while($d=mysql_fetch_assoc($r)) {
				echo '<div class="verdana11"><img src="i/bullet-red-01a.gif" width="18" hspace="5"><a href="?act=data&type='.$DataType.'&Id='.$d['id'].'">'.stripslashes($d['title']).'</a></div>';
			}
			echo '<hr>';
			
			if($_REQUEST['Id']>0) {
				$DataId = $_REQUEST['Id'];
				$SQL = 'SELECT sd.*, u.id as PosterId, u.user_name AS PosterName, u.clan AS PosterClan FROM site_data sd LEFT JOIN site_users u ON sd.poster_id=u.id WHERE sd.id=\''.$DataId.'\' ORDER BY sd.post_date DESC';
				$r = mysql_query($SQL);
				
			//
			// OUTPUT IF Id
			//
				if(mysql_num_rows($r)<1) echo '<h2>Материал не найден!</h2>';
				else {
					$d = mysql_fetch_assoc($r);
					echo '
				<table width="100%" border="0" cellspacing="3" cellpadding="3"><tr>
				<td height="20" background="i/bgr-grid-sand.gif"><p><img src="i/bullet-red-01.gif" width="18" height="11" hspace="5"><strong>['.date("d.m.Y - H:i", $d['post_date']).'] </strong> <span class="top-header">'.stripslashes($d['title']).'</span></p></td>
				</tr><tr><td>
			';
					ParseNews($d['text'], 1, $d['replace_br']);
				//	ParseNews2($d['text']);
					echo '
				</td></tr><tr>
				<td height=20 align="right" background="i/bgr-grid-sand1.gif">
				Опубликовано: <strong>'.GetClan($d['PosterClan']).GetUser($d['PosterId'],$d['PosterName'],AuthUserGroup).'</strong>
			';

		    		if(abs(AccessLevel) & AccessArticles) {
						echo '
					 / <a href="?act=data_manager&DataId='.$d['id'].'">редактировать</a> /
					<a onclick="if(confirm(\'Удалить статью из базы?\')){top.location=\'?act=data&Id='.$d['id'].'&do=delete&type='.$DataType.'\'}" href="javascript:{}">удалить</a>
					';
					}
				
					if($d['allow_comments']==1) {
						$com = mysql_fetch_array(mysql_query('SELECT count(id) as cnt FROM data_comments WHERE id_data=\''.$d['id'].'\''));
						echo ' / <a href="?act=data_comments&DataId='.$d['id'].'">комментарии: '.$com['cnt'].'</a>';
					}
					echo '</td></tr></table>';
				}
			}
			
	//
	// OUTPUT IF 1
	//
		} elseif(mysql_num_rows($r)==1) {
			$d = mysql_fetch_assoc($r);
			echo '
		<table width="100%" border="0" cellspacing="3" cellpadding="3"><tr><td>
    ';
			ParseNews($d['text'], 1, $d['replace_br']);
		//	ParseNews2($d['text']);
			echo '
		</td></tr><tr>
		<td height=20 align="right" background="i/bgr-grid-sand1.gif">
		Опубликовано: <strong>'.GetClan($d['PosterClan']).GetUser($d['PosterId'],$d['PosterName'],AuthUserGroup).'</strong>
	';

			if(abs(AccessLevel) & AccessArticles) {
				echo '
		 / <a href="?act=data_manager&DataId='.$d['id'].'">редактировать</a> /
		<a onclick="if(confirm(\'Удалить статью из базы?\')){top.location=\'?act=data&Id='.$d['id'].'&do=delete&type='.$DataType.'\'}" href="javascript:{}">удалить</a>
       ';
			}
			
			if($d['allow_comments']==1) {
				$com = mysql_fetch_array(mysql_query('SELECT count(id) as cnt FROM data_comments WHERE id_data=\''.$d['id'].'\''));
				echo ' / <a href="?act=data_comments&DataId='.$d['id'].'">комментарии: '.$com['cnt'].'}</a>';
			}
			echo '</td></tr></table>';
		} else
			echo '<h2>Раздел пуст</h2>';
	}
}

?>