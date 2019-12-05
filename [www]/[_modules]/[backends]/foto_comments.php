<?php
$_RESULT = array('res' => 'ok');
require_once('../xhr_config.php');
require_once('../xhr_php.php');
require_once('../mysql.php');
require_once('../functions.php');
require_once('../auth.php');
$JsHttpRequest =& new Subsys_JsHttpRequest_Php('windows-1251');
$in = quote_smart($_REQUEST);
?>

<h1>Комментарии к фотографиям</h1>
<table border="0" cellspacing="0" cellpadding="10" width="100%">
<tr>
  <td valign="top" >

<?php

if(@$in['added']==1) echo '<div class=green>Комментарий добавлен</div><br>';
if(@$in['DataId']) {

        if(@$in['doo']=='close' && (abs(AccessLevel) & AccessFotosModer)) {
			$SQL='UPDATE `site_data` SET allow_comments=\'0\' WHERE id=\''.$in['DataId'].'\'';
			mysql_query($SQL);
			echo '<script>top.location.href=\'?act=fotos_comments&DataId='.$in['DataId'].'&p='.$in['p'].'\'</script>';

        } elseif(@$in['doo']=='open' && (abs(AccessLevel) & AccessFotosModer)) {

            $SQL='UPDATE `site_data` SET allow_comments=\'1\' WHERE id=\''.$in['DataId'].'\'';
            mysql_query($SQL);
            echo '<script>top.location.href=\'?act=fotos_comments&DataId='.$in['DataId'].'&p='.$in['p'].'\'</script>';
        }

        if(@$in['doo']=='delete' && @$in['IdComment'] && (abs(AccessLevel) & AccessFotosModer)) {

        	$DelText='[red]Удалено полицейским[/red] [clan]police[/clan]<b>'.GetUser(AuthUserId,AuthUserName,AuthUserGroup).'</b>';
			$SQL='UPDATE `fotos_comments` SET `comment_text`=\''.addslashes($DelText).'\' WHERE id=\''.$in['IdComment'].'\'';
			mysql_query($SQL);
			
			echo '<script>top.location.href=\'?act=fotos_comments&DataId='.$in['DataId'].'&p='.$in['p'].'\'</script>';
		}

        if(@$in['doo']=='add' && @$in['NewsText']) {

                $SQL='SELECT allow_comments FROM `fotos_users` WHERE comments_id=\''.$in['DataId'].'\'';
                $d=mysql_fetch_array(mysql_query($SQL));
		        $AllowComments=$d['allow_comments'];

                $SQL='SELECT banned, post_time FROM `site_users` WHERE id=\''.AuthUserId.'\'';
                $d=mysql_fetch_array(mysql_query($SQL));
                if($d['banned']>time()) {
                        $MolDiff=$d['banned']-time();
	                    echo '<h2>Вы не можете оставлять комментарии еще '.gmdate('zдн. Hч iм sс',$MolDiff).'</h2><br><br>';

                } elseif($d['post_time']+30>time()) {
	                $oldcomm=stripslashes($in['NewsText']);
                    echo '<h2>Вы не можете оставлять комментарии чаще, чем раз в 30 секунд</h2><br><br>';

                } elseif ($AllowComments==0) {
	                echo '<h2>Обсуждение закрыто</h2><br><br>';

                } else {
                    $SQL='INSERT INTO fotos_comments (id_data,id_user,comment_date,comment_text) values(\''.$in['DataId'].'\', \''.AuthUserId.'\', \''.time().'\', \''.addslashes($in['NewsText']).'\')';
                    mysql_query($SQL);
		            $SQL='UPDATE site_users SET post_time=\''.time().'\' WHERE id=\''.AuthUserId.'\'';
			        mysql_query($SQL);
                    echo '<script>top.location.href=\'?act=fotos_comments&DataId='.$in['DataId'].'&p='.$in['p'].'\'</script>';
	           }
        }
		
		$SQL='SELECT * FROM `fotos_users` WHERE comments_id=\''.$in['DataId'].'\'';
		$r=mysql_query($SQL);
		if(mysql_num_rows($r)<1) echo $mess['NewsNotFound'];
		else {
			$d=mysql_fetch_array($r);
			$AllowComments=$d['allow_comments'];
/*
    echo "
		<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
		<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Обсуждение фотографий: </strong> <a href='http://www.tzpolice.ru/?act=fotos&nick={$d['nick']}'>{$d['nick']}</span></p></td>
		</tr><tr><td>
		</tr></table>
	";
*/


			$SQL='SELECT c.*,u.id AS user_id, u.user_name AS user_name, u.clan FROM fotos_comments c LEFT JOIN site_users u ON c.id_user=u.id WHERE  c.id_data=\''.$in['DataId'].'\' ORDER BY c.comment_date';
			$r=mysql_query($SQL);
			
			if(mysql_num_rows($r)<1) echo $mess['NoComments'];
			else {
				$pages=ceil(mysql_num_rows($r)/$CommentsPerPage);
				if($in['p']>0) $p=$in['p'];
				else $p=1;
				
				$LimitParam=$p*$CommentsPerPage-$CommentsPerPage;
				
				$SQL.=' LIMIT '.$LimitParam.', '.$CommentsPerPage;
				$r=mysql_query($SQL);
				
				echo '<br><div align=right>Страницы: <b>';
				ShowPagesComm($p,$pages,4,$in['DataId']);
				
				echo '</b></div>';
				
				for($i=0;$i<mysql_num_rows($r);$i++) {
					$d=mysql_fetch_array($r);
					echo '<br><table width="100%" border="0" cellspacing="3" cellpadding="0"><tr>
						<td height="20" background="i/bgr-grid-sand.gif"><p><img src="i/bullet-red-01.gif" width="18" height="11" hspace="5">';
					
					if(abs(AccessLevel) & AccessFotosModer)
						echo '<a href="#;return false" onclick="if(confirm(\'Удалить комментарий?\')){top.location=\'?act=fotos_comments&DataId='.$d['id_data'].'&IdComment='.$d['id'].'&doo=delete&p='.$p.'\'}">удалить</a> / ';
						echo ' '.date('d.m.Y - H:i', $d['comment_date']).' / '.GetClan($d['clan']).GetUser($d['user_id'],$d['user_name'],AuthUserGroup).': </p></td>
				</tr><tr><td>';
						
//	$ctext = wordwrap($d['comment_text'], 50, "<wbr>", 1);
$ctext=$d['comment_text'];
//$max=40; # max разрешенная длина слова
//$term='<wbr>'; # чем разбивать длинные слова
//$ctext=preg_replace('/([^ \n\r\t]{'.$max.'})/m','$1'.$term,$ctext);

            ParseNews($ctext,0);
            echo '</td></tr></table>';
        }
    }

?>
<br>
<?
        echo '<br><div align=right>Страницы: <b>';
        ShowPagesComm($p,$pages,4,$in['DataId']);
        echo "</b></div>";
?>
</td></tr></table>
<?

if((abs(AccessLevel) & AccessFotosModer) && $AllowComments!=0)
	echo '<div>[ <a href="?act=fotos_comments&doo=close&DataId='.$in['DataId'].'&p='.$p.'">Закрыть обсуждение</a> ]</div>';

if((abs(AccessLevel) & AccessFotosModer) && $AllowComments==0)
	echo '<div>[ <a href="?act=fotos_comments&doo=open&DataId='.$in['DataId'].'&p='.$p.'">Продолжить обсуждение</a> ]</div>';

if(AuthStatus!=1) echo $mess['CantAddComment'].$mess['WantRegister'];

else {
        $SQL='SELECT banned FROM `site_users` WHERE id=\''.AuthUserId.'\'';
        $d=mysql_fetch_array(mysql_query($SQL));
        if($d['banned']>time()) {
                $MolDiff=$d['banned']-time();
            echo '<h2>Вы не можете оставлять комментарии еще '.gmdate('zдн. Hч iм sс', $MolDiff).'</h2><br><br>';
        } else {
            if($AllowComments==0) echo '<h2>Обсуждение закрыто</h2>';
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
        <input name="act" id="act" type="hidden" value="fotos_comments">
        <input name="doo" id="doo" type="hidden" value="add">
        <input name="DataId" id="DataId" type="hidden" value="<?=$in['DataId']?>">
	    <input name="p" id="p" type="hidden" value="<?=$p?>">
	    Текст комментария: <Br>
        <textarea ONSELECT="storeCaret(this)" ONCLICK="storeCaret(this)" ONKEYUP="storeCaret(this)" name="NewsText" id="NewsText" style="width:100%" rows=10><?=@htmlspecialchars(stripslashes($d['news_text']))?><?=$oldcomm?></textarea><br>
        <img src="/_imgs/b.gif" border=0 onclick="AddTag('<b>','</b>','NewsText')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы сделать его полужирным">
        <img src="/_imgs/i.gif" border=0 onclick="AddTag('<i>','</i>','NewsText')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы выделить его курсивом">
        <img src="/_imgs/u.gif" border=0 onclick="AddTag('<u>','</u>','NewsText')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы сделать его подчеркнутым">
        <img src="/_imgs/center.gif" border=0 onclick="AddTag('<div align=center>','</div>','NewsText')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы поместить его по центру">
        <br><br>
	<input type="button" onclick="newcomment();" value="Прокомментировать" />
        </form>
</td><td width=200 valign=top>
       Вставить: <br>
	<form> 
<!--    <select size="1" onchange="document.frames['icons'].location='_modules/icons/'+this.options[this.selectedIndex].value+'.php'" style="width:100%"> -->
    <select size="1" onchange="document.getElementById('icons').src='_modules/icons/'+this.options[this.selectedIndex].value+'.php'" style="width:100%">
        <option value="blank" selected>-- выберите категорию --</option>
        <option value="smiles"> Смайлики</option>
        <option value="clans"> Кланы</option>
        <option value="prof"> Профессии</option>
        <option value="login"> Ник</option>
        <option value="url"> URL</option>
    </select>
	</form>
    <iframe BORDER=0 FRAMEBORDER=0 id="icons" src="_modules/icons/smiles.php" width=100% HEIGHT=105 style="border:1 solid #000000">
</td></tr>
<table>
<?
				}
			}
		}
	}
}
?>