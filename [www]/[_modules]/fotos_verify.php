<h1>Подтверждение фотографий</h1>
<?php
//error_reporting(E_ALL);

mysql_query('DELETE FROM `fotos_new` WHERE `nick` = \'authusername\'');
extract($_REQUEST);
if(abs(AccessLevel) & AccessFotosModer) {
	function make_seed() {
		list($usec, $sec) = explode(' ', microtime());
		return (float) $sec + ((float) $usec * 100000);
	}
	
	if ($make_send) {
		$count_fotos = count($f_id);
		for ($i = 0; $i < $count_fotos; $i++) {
			$result = mysql_query('SELECT * FROM `fotos_new` WHERE `id`=\''.$f_id[$i].'\'');
			if (@mysql_num_rows($result) > 0) {
				$row = mysql_fetch_assoc($result);
				$tmp = explode('.',$row['file']);
				$filename = $tmp[0];
				if ($action[$i] == 'makedelete') {
					//copy('i/newfotos/'.$row['file'], 'cens/'.$row['file']);
					unlink('i/newfotos/'.$row['file']);
					unlink('i/newfotos/thumbs/'.$filename.'.jpg');
					$result = mysql_query('DELETE FROM `fotos_new` WHERE id=\''.$f_id[$i].'\'');
				} else {
					
					if ($newfotofile[$i] && $newfotofile[$i] != 'none') {
						srand(make_seed());
						$fname = chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).rand(1,99999999).'.jpg';
						move_uploaded_file($newfotofile[$i], 'i/fotos/'.$fname);
						unlink('i/newfotos/'.$row['file']);
						unlink('i/newfotos/thumbs/'.$filename.'.jpg');
						$row['file'] = $fname;
					} else {
						copy('i/newfotos/'.$row['file'], 'i/fotos/'.$row['file']);
						unlink('i/newfotos/'.$row['file']);
						unlink('i/newfotos/thumbs/'.$filename.'.jpg');
					}
					$f_name[$i] = htmlspecialchars($f_name[$i]);
					$f_city[$i] = htmlspecialchars($f_city[$i]);
					$f_age[$i] = htmlspecialchars($f_age[$i]);
					$f_comment[$i] = htmlspecialchars($f_comment[$i]);
					$confr = AuthUserId;
					$query = 'INSERT INTO `fotos_main` (`nick`, `file`, `name`, `city`, `gener`, `age`, `comment`, `date`, `time`, `confirmed`) VALUES (\''.$row['nick'].'\', \''.$row['file'].'\', \''.$f_name[$i].'\', \''.$f_city[$i].'\', \''.$row['gener'].'\', \''.$f_age[$i].'\', \''.$f_comment[$i].'\', \''.$row['date'].'\', \''.$row['time'].'\', \''.$confr.'\')';
					$result = mysql_query($query);
					$query = 'DELETE FROM `fotos_new` WHERE `id`=\''.$f_id[$i].'\'';
					$result = mysql_query($query);
					$temp_timenow = time();
					$temp_clan = $row['clan'];
					if ($temp_clan == 'no') $temp_clan = '';
					if (@mysql_num_rows(mysql_query('SELECT * FROM `fotos_users` WHERE nick=\''.$row['nick'].'\'')) == 0) {
						$result = mysql_query('SELECT * FROM `site_users` WHERE `user_name`=\''.$row['nick'].'\'');
						$row3 = mysql_fetch_assoc($result);
						$temp_uid = $row3['id'];
						$query2 = 'INSERT INTO `fotos_users` (`nick`, `clan`, `gener`, `fotos`, `comments_id`) VALUES (\''.$row['nick'].'\', \''.$temp_clan.'\', \''.$row['gener'].'\', \''.$temp_timenow.'\', \''.$temp_uid.'\')';
						$result2 = mysql_query($query2);
					} else {
						$query2 = 'UPDATE `fotos_users` SET `clan`=\''.$temp_clan.'\', `fotos`=\''.$temp_timenow.'\', `voted`=\'\' WHERE `nick`=\''.$row['nick'].'\'';
						$result2 = mysql_query($query2);
					}
				}
			}
		}
		$make_send = '';
		$id = '';
	}
	if ($id && !$make_send) {
		$result = mysql_query('SELECT * FROM `fotos_new` WHERE `nick`=\''.$id.'\'');
		if (@mysql_num_rows($result) == 0) {
			echo '<font class="nik" style="COLOR: red">Фотографий от указанного пользователя нет</font><br><br>';
		} else {
			$row = mysql_fetch_assoc($result);
			$count_all = @mysql_num_rows($result);
			$result1 = mysql_query('SELECT * FROM `site_users` WHERE `user_name`=\''.$id.'\'');
			$row1 = mysql_fetch_assoc($result1);
?>
    <table border="0" cellspacing="0" cellpadding="0" width="600">
    <tr><td><b>Пользователь:</b>&nbsp;<font color="#A92C22"><b><?php
			if ($row1['clan'] != '' && $row1['clan'] != '0') {
				echo '<img src="_imgs/clans/'.$row1['clan'].'.gif">';
			} 
			echo $row['nick'];
?></b></font></td></tr>
    </table><br>

<SCRIPT language=JavaScript1.2>
<!-- Hiding
function makeview(name){
	window.open(name,"_blank");
    	return false;
}
// -->
</SCRIPT>
<form action="" method="post" name="form" enctype="multipart/form-data">
<?php
			$result = mysql_query('SELECT * FROM `fotos_new` WHERE `nick`=\''.$id.'\' ORDER BY `id`');
			$i = 0;
			while ($row = mysql_fetch_assoc($result)) {
				$tmp = explode('.',$row['file']);
				$filename = $tmp[0];
?>
<input name="f_id[]" type="hidden" value="<?=$row["id"]?>">

<table border="0" cellspacing="0" cellpadding="5" style="BORDER: 1px #957850 solid">
<tr><td style="BORDER-BOTTOM: 1px #957850 solid" colspan="2" background="i/bgr-grid-sand1.gif"><img src="i/bullet-red-01.gif"><b>Фотография #<?php echo $i + 1; ?></b></td></tr>
<tr>
<!--  <td style="BORDER-RIGHT: 1px #957850 solid" valign="middle" align="center" background="i/bgr-grid-sand1.gif"><input style="CURSOR: hand" type="submit" value="Смотреть" onclick="makeview('/i/newfotos/<?=$row['file']?>'); return false;"><br><br><b>Размер:</b>&nbsp;-->
<td style="BORDER-RIGHT: 1px #957850 solid" valign="middle" align="center" background="i/bgr-grid-sand1.gif"><a href="/i/newfotos/<?=$row['file']?>" target="_blank"><? if (is_file("/home/sites/police/www/i/newfotos/thumbs/".$filename.".jpg")) {?><img src="/i/newfotos/thumbs/<?=$filename?>.jpg"><?} else {?><img src="/_modules/backends/gallery_uploader/images/no_thumb.gif?<?=$filename?>"><?}?></a><br><br><b>Размер:</b>&nbsp;
<?php
				$temp_size = intval(filesize('i/newfotos/'.$row['file']) / 1024);
    			if ($temp_size <= 500) {
    				echo '<font style="COLOR: #000000">'.$temp_size.'кб';
    			} else {
    				echo '<font style="COLOR: #FF0000">'.$temp_size.'кб';
    			}
?><br><br><select style="WIDTH: 139px" class="combo" size="1" name="action[]"><option selected value="makeadd">Добавить фото</option><option value="makedelete">Удалить фото</option></select></td>
  <td background="i/bgr-grid-sand.gif">
  <table border="0" cellspacing="0" cellpadding="0">
    <tr><td class="mainl" style="COLOR: #47A639">Изменённое&nbsp;фото:&nbsp;</td><td width="100%"><input type="file" size="26" name="newfotofile[]"></td></tr>
    <tr><td><img src="_imgs/none.gif" height="10"></td></tr>
    <tr><td class="mainl" style="COLOR: #47A639">Имя:&nbsp;</font></td><td width="100%"><input name="f_name[]" type="text" value="<?=$row['name']?>" size="25"></tr>
    <tr><td class="mainl" style="COLOR: #47A639">Город:&nbsp;</font></td><td width="100%"><input name="f_city[]" type="text" value="<?=$row['city']?>" size="25"></tr>
    <tr><td class="mainl" style="COLOR: #47A639">Возраст:&nbsp;</font></td><td width="100%"><input name="f_age[]" type="text" value="<?=$row['age']?>" size="5"></tr>
    <tr><td class="mainl" style="COLOR: #47A639">Комментарий:&nbsp;</font></td><td width="100%"><input name="f_comment[]" type="text" value="<?=$row['comment']?>" size="25"></tr>
  </table><br>
  </td>
</tr>
</table><br>
<?php
				$i++;
			}
?>
<br><input style="CURSOR: hand" type="submit" class="button" name="make_send" value="Добавить фотографии"></form>
<?php
		}
	}
	if (!$id && !$make_send) {
		function str2lower ($str) {
			return preg_replace('/Ё/','ё', preg_replace('/([А-Я])/e', 'chr(ord("\\1")+32)', strtolower($str)));
		}
		$result = mysql_query('SELECT `nick`, `date`, `time` FROM `fotos_new` GROUP BY `nick` ORDER BY `date`, `time`');
		if (@mysql_num_rows($result) == 0) {
			echo '<font class="nik">Нет новых фотографий</font><br><br>';
		} else {
	//		$result = mysql_query("SELECT * FROM site_users");
	//		if (@mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_assoc($result)) {
					$result2 = mysql_query('SELECT * FROM `site_users` WHERE `user_name`=\''.$row['nick'].'\'');
					$result3 = mysql_query('SELECT * FROM `fotos_new` WHERE `nick`=\''.$row['nick'].'\'');
					$row2 = mysql_fetch_assoc($result2);
					if (@mysql_num_rows($result2) > 0 && str2lower($row['nick']) == str2lower($row2['user_name'])) {
?>
  <img src="i/bullet-red-01.gif"> <?php
						if ($row2['clan'] && $row2['clan'] != 'no') {
							echo '<img src="_imgs/clans/'.$row2['clan'].'.gif">';
						}
?><a href="<?=$PHP_SELF?>?act=fotos_verify&id=<?=$row['nick']?>"><?=$row['nick']?></a> <b>[</b>Кол-во фотографий: <b><?=@mysql_num_rows($result3)?></b><b> | </b>Дата: <?=$row['date']?>&nbsp;<?=$row['time']?><b>]</b><br>
<?php
					}
				/* else {
						@mysql_query("DELETE FROM `fotos_new` WHERE `nick`='".$row[user_name]."'") or die(mysql_error());
					}
				*/
				}
		//	}
		}
	}
} else {
	echo $mess['AccessDenied'];
	echo $mess['WantRegister'];
}
?>