<?php



	$_RESULT = array('res' => 'ok');
	require_once('../xhr_config.php');
	require_once('../xhr_php.php');
	require_once('../mysql.php');
	require_once('../functions.php');
	require_once('../auth.php');

	$JsHttpRequest =& new Subsys_JsHttpRequest_Php('windows-1251');
	$in = quote_smart($_REQUEST);
	$nick = strip_tags(urldecode($in['nick']));
	if (strlen($nick) > 16) { $nick=substr($nick,0,16); }
	$f = $in['f'];
	$result1 = mysql_query('SELECT * FROM `fotos_users` WHERE `nick`=\''.$nick.'\' AND `fotos`>\'0\'');
	if (@mysql_num_rows($result1) == 0) { echo '<center><b>Фотографии пользователя '.$in['nick'].' не найдены</b></center>'; }
	else {
		$row1 = mysql_fetch_assoc($result1);
		
		$result2 = mysql_query('SELECT * FROM `fotos_main` WHERE `nick`=\''.$nick.'\' ORDER BY id');
		$nrows = mysql_num_rows($result2);
		
		$temp_views = $row1['views'];
		$nick = $row1['nick'];
		
	// Увелчиваем счетчик просмотров
		$temp_views++;
		mysql_query('UPDATE `fotos_users` SET `views`=\''.$temp_views.'\' WHERE `nick`=\''.$nick.'\'');
		
		if ((!$f) || ($f==0)) {
			$f = $row1['firstfoto'];
			while ($f > $nrows) {
				$f = $f-1;
			}
		}
		
		$i = 0;
		while ($i < $f) {
			$i++;
			$row2 = mysql_fetch_assoc($result2);
		}
	###
?>
    <table border="0" cellspacing="0" cellpadding="3" width="450" style="BORDER: 1px #957850 solid;">
    <tr><td style="BORDER-BOTTOM: 1px #957850 solid;" background="i/bgr-grid-sand.gif" align="center">
<?php
		if ($row1['clan'] != '0' && $row1['clan'] != 'NULL' && $row1['clan'] != '') {
			echo '<img src="_imgs/clans/'.$row1['clan'].'.gif" width="28" height="16">';
		}else{
			echo '<img src="_imgs/none.gif" width="28" height="16">';
		}
		echo '<b>'.$row2['nick'].'</b>';
?>
	</td></tr>
    <tr><td style="BORDER-BOTTOM: 1px #957850 solid;" background="i/bgr-grid-sand1.gif" align="center">
    <?php if ($row2['name'] != '') { echo '<b>Имя:</b>&nbsp;'.$row2['name'].'&nbsp;'; } ?>
    <b>Пол:</b>&nbsp;<?php if ($row2['gener'] == 1) { echo 'Муж'; } else { echo 'Жен'; } ?>&nbsp;
    <?php if ($row2['city'] != '') { echo '<b>Город:</b>&nbsp;'.$row2['city'].'&nbsp;'; } ?>
    <?php if ($row2['age'] != '' && $row2['age'] != 0) { echo '<b>Возраст:</b>&nbsp;'.$row2['age'].'&nbsp;'; } ?>
    </td></tr>
    <tr><td style="BORDER-BOTTOM: 1px #957850 solid;" background="i/bgr-grid-sand.gif" align="center">
      <img src="_imgs/none.gif" height="10"><br>

<?
//	echo $row2['file'];
		if (is_file('../../i/fotos/'.$row2['file'])) {
			echo '<img src="i/fotos/'.$row2['file'].'"><br><br>';
		} else {
			echo '<img src="i/fotos/sorry.gif"><br><br>';
		}
		
		echo '<img src="_imgs/none.gif" height="10"><br>';
		
		if ($row2['comment']) {
			echo 'Комментарий:&nbsp;<font style="COLOR: #47A639">'.$row2['comment'].'</font>';
		}
		
		if (abs(AccessLevel) & AccessFotosModer) {
			$resx = mysql_query('SELECT `user_name` FROM `site_users` WHERE `id`=\''.$row2['confirmed'].'\'');
			$rowx = mysql_fetch_assoc($resx);
			echo '<SMALL><BR>Опубликована: '.$rowx['user_name'].'</SMALL>';
		}

?>
    </td></tr>
    <tr><td style="BORDER-BOTTOM: 1px #957850 solid;" background="i/bgr-grid-sand1.gif" align="center">
    <b>Фотографии:</b>&nbsp;
<?php
		for ($i = 1; $i <= $nrows; $i++) {
			if ($i == $f) {
				echo '<b> ['.$i.'] </b>';
			} else {
				echo '<a href="javascript:{}" onClick="loadfoto(\''.$nick.'\', '.$i.',0);">'.$i.'</a> ';
			}
		}
?>
    </td></tr>
    <tr><td style="BORDER-BOTTOM: 1px #957850 solid;" background="i/bgr-grid-sand.gif" align="center">
    <b>Текущий рейтинг:</b>&nbsp;<?php if ($row1['points_rank'] == 0 ) { echo '0.00'; } else { echo $row1['points_rank']; } ?><br>
    кол-во голосов:&nbsp;<?=$row1['points_num']?> баллы:&nbsp;<?=$row1['points_sum']?> кол-во просмотров:&nbsp;<?=$temp_views?><br>
<?php
		if(AuthStatus==1 && AuthUserName!='') {
			if (!strstr($row1['voted'], AuthUserName)) {
			//	echo AuthStatus.'+'.AuthUserName.'+'.$nick;
				echo '<form action="?act=fotos&nick='.$nick.'&f='.$f.'" method="post"><input name="vote_nick" type="hidden" value="'.$nick.'">';
?>
		
         <table border="0" cellspacing="0" cellpadding="0">
         <tr>
            <td align="center"><b>1</b></td><td>&nbsp;&nbsp;</td>
            <td align="center"><b>2</b></td><td>&nbsp;&nbsp;</td>
            <td align="center"><b>3</b></td><td>&nbsp;&nbsp;</td>
            <td align="center"><b>4</b></td><td>&nbsp;&nbsp;</td>
            <td align="center"><b>5</b></td>
         </tr>
        <tr>
            <td align="center"><input name="vote_radio" type="radio" value="1"></td>
            <td>&nbsp;&nbsp;</td>
            <td align="center"><input name="vote_radio" type="radio" value="2"></td>
            <td>&nbsp;&nbsp;</td>
            <td align="center"><input name="vote_radio" type="radio" value="3"></td>
            <td>&nbsp;&nbsp;</td>
            <td align="center"><input name="vote_radio" type="radio" value="4"></td>
            <td>&nbsp;&nbsp;</td>
            <td align="center"><input name="vote_radio" type="radio" checked value="5"></td>
         </tr>
         <tr><td colspan="9" align="center"><img src="_imgs/none.gif" height="5"><br><input type="submit" style="CURSOR: hand" name="make_vote" value="Голосовать"><br><img src="_imgs/none.gif" height="5"></td></tr>
         </form>
         </table>
<?php
			} else { echo '<br>вы уже голосовали<br><br>'; }

		} else { echo '<br>голосовать могут только зарегистрированные пользователи<br><br>'; }
?>

    </td></tr>
    <tr><td background="i/bgr-grid-sand1.gif" align="center">
    <a href="javascript:{}" onclick="opencomments(<?php echo $row1['comments_id']; ?>, 1);">Комментарии (<?
		$res = @mysql_fetch_array(mysql_query('SELECT COUNT(id) as `cnt`  FROM `fotos_comments` WHERE `id_data` = \''.$row1['comments_id'].'\''));
		echo $res['cnt'];
?>)</a>

    </td></tr>
    </table>

<?php
		if((AuthStatus==1 && AuthUserName==$nick) || (abs(AccessLevel) & AccessFotosModer)) { ?>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="3" width="450" style="BORDER: 1px #957850 solid;">
    <tr><td style="BORDER-BOTTOM: 1px #957850 solid;" background="i/bgr-grid-sand.gif" align="center">
    <b>Управление фотографиями</b>
    </td></tr>
    <tr><td style="BORDER-BOTTOM: 1px #957850 solid;" background="i/bgr-grid-sand1.gif" align="left">
      Удалить фотографию:&nbsp;
<?php
			$result2 = mysql_query('SELECT `id` FROM `fotos_main` WHERE `nick`=\''.$row1['nick'].'\' ORDER BY `id`');
			$i = 1;
			while($tmp_row = mysql_fetch_assoc($result2)) {
				echo '<input type="button" style="CURSOR: hand" value="'.$i.'" onclick="self.location.href=\'?act=fotos&makedelete='.$tmp_row['id'].'\'"> ';
				$i++;
			}
?>
    </td></tr>
    <tr><td style="BORDER-BOTTOM: 1px #957850 solid;" background="i/bgr-grid-sand.gif" align="center">
    <b>Изменить комментарий</b>
    </td></tr>
    <tr><td style="BORDER-BOTTOM: 1px #957850 solid;" background="i/bgr-grid-sand1.gif" align="left">
    <form action="?act=fotos" method="post">
      Комментарий:&nbsp;<input name="pict" type="hidden" value="<?=$f?>"><input name="makechcomment" type="hidden" value="<?=$row2['id']?>"><input name="newcomment" type="text" value="<?=$row2['comment']?>" size="52">&nbsp;<input type="submit" style="CURSOR: hand" value="Изменить">
    </form>
    </td></tr>
    <tr><td style="BORDER-BOTTOM: 1px #957850 solid;" background="i/bgr-grid-sand.gif" align="center">
    <b>Фотография, которая показывается первой</b>
    </td></tr>
    <tr><td background="i/bgr-grid-sand1.gif" align="left">
    <form action="" method="post">
      Номер фотографии:&nbsp;<input name="act" type="hidden" value="fotos"><input name="firstchname" type="hidden" value="<?=$row1['nick']?>"><input name="newfirst" type="text" value="<?=$row1['firstfoto']?>" size="3">&nbsp;<input type="submit" style="CURSOR: hand" name="makechfirst" value="Изменить">
    </form>
    </td></tr>
    </table>

<?php
		}
	}
?>