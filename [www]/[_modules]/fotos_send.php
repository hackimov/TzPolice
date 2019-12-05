<h1>Добавление фотографии</h1>
<?php
	error_reporting(0);
	//Settings
	$foto_newdir = 'i/newfotos';
	
	function make_seed() {
		list($usec, $sec) = explode(' ', microtime());
		return (float) $sec + ((float) $usec * 100000);
	}
	
	if(AuthUserName != '') {
		
		if ($_REQUEST['make_send']) {
			/* Самодельная проверка на то, является ли файл загруженным. */
/*			function is_uploaded_file2($filename) {
				echo get_cfg_var('upload_tmp_dir');
				echo "|||||";
				if (!$tmp_file = get_cfg_var('upload_tmp_dir')) {
					$tmp_file = dirname(tempnam('', ''));
				}
				$tmp_file .= '/' . basename($filename);
*/			/* У пользователя может быть завершающий слеш в php.ini... */
/*				return (ereg_replace('/+', '/', $tmp_file) == $filename);
			}
			
$currentdir=getcwd();
$target_path = $currentdir . "/2nddir/" . basename($_FILES['f_file']['name']);
echo "Target: $target_path<br>";
$temploc=$_FILES['f_file']['tmp_name'];
echo "Temploc: $temploc<br>";
*/			
			/* Вот пример использования, так как функция move_uploaded_file()
			* также отсутствует в ранних версиях: */
/*			if (is_uploaded_file2($HTTP_POST_FILES['f_file'])) {
			//	copy($HTTP_POST_FILES['userfile'], "/place/to/put/uploaded/file");
				echo "copy";
			} else {
				echo "Возможна атака загрузки файла: имя файла - ".$HTTP_POST_FILES['f_file'];
				print_r($HTTP_POST_FILES['f_file']);
			}
*/
			if (!$_FILES['f_file']['tmp_name']) { echo '<font class="nik" style="COLOR: red">Укажите файл</font><br><br>'; }
			else {
				if(!file_exists($_FILES['f_file']['tmp_name'])) { echo '<font class="nik" style="COLOR: red">Указанного вами файла не существует</font><br><br>'; }
				else {
					$f_name = htmlspecialchars($_REQUEST['f_name']);
					$f_city = htmlspecialchars($_REQUEST['f_city']);
					$f_age = htmlspecialchars($_REQUEST['f_age']);
					$f_comment = htmlspecialchars($_REQUEST['f_comment']);
					$f_gener = $_REQUEST['f_gener'];
					srand(make_seed());                                                                                          
					$fname = chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)) . time();//rand(1,999999);
//					$fname = urlencode(AuthUserName) . "-" . chr(rand(97,122)) . time();;
					
					if(is_file($_FILES['f_file']['tmp_name'])) {
						$info=GetImageSize($_FILES['f_file']['tmp_name']);
						switch($info[2]) {
							case 1: $type='gif'; break;
							case 2: $type='jpg'; break;
							case 3: $type='png'; break;
							default: $type='error';
						}
						
						if($type=='error') echo '<h2>Допустимы только изображения JPG, GIF и PNG</h2>';
						else {
							$base_name=time();
							
							if($info[0]>440 || $info[1]>600) MakePreview($_FILES['f_file']['tmp_name'], $foto_newdir, 440, 600, $type, $fname);
							else MakePreview($_FILES['f_file']['tmp_name'], $foto_newdir, $info[0], $info[1], $type, $fname);
							$nnname = $foto_newdir.'/'.$fname.'.'.$type;
							unlink($_FILES['f_file']['tmp_name']);
							$fname = $fname . '.'.$type;
							$temp_current_user_name = AuthUserName;
							$gm_date = gmdate("Y-m-d", time());
							$gm_time = gmdate("H:i:s", time());
							$temp_clan = AuthUserClan;
							if ($temp_clan == "no") $temp_clan = '';
							if (is_file($nnname)) {
								$query = 'INSERT INTO `fotos_new` (`nick`, `file`, `name`, `city`, `gener`, `age`, `comment`, `date`, `time`, `clan`) VALUES (\''.$temp_current_user_name.'\', \''.$fname.'\', \''.$f_name.'\', \''.$f_city.'\', \''.$f_gener.'\', \''.$f_age.'\', \''.$f_comment.'\', \''.$gm_date.'\', \''.$gm_time.'\', \''.$temp_clan.'\')';
								$result = mysql_query($query);
								if ($result) {
									echo "<font class='nik' style='COLOR: green'>Фотография отправлена,<br> после редактирования модератором она будет добавлена в фотогалерею</font><br><br><b>Внимание!</b> Если Вы уверены в корректности Вашей фотографии, однако не видите ее в нашей фотогалерее в течение 3 дней после отправки, попробуйте отправить ее снова.";
								} else {
									echo "<font class='nik' style='COLOR: red'>Ошибка во время добавления фотографии</font><br><br>";
								}
							} else {
								echo "<font class='nik' style='COLOR: red'>Ошибка во время добавления фотографии</font><br><br>";
							}
						}
					}
				}
			}
		}
?>
<b>Требования:</b><br>
 - Фотография должна быть в формате JPG<br>
 - Размер файла не должен превышать 500кб<br>
<hr><br><br>
<center>
<span style="font-size: 15 px"><a href="http://www.tzpolice.ru/?act=data&type=manuals&Id=123" target=_blank>Девушкам: как выглядеть на фото хорошо</a>
<!--<br><br>
Попробуйте использовать <a href='http://www.tzpolice.ru/?act=data&type=gallery' target='new'>"Мастер веб-публикаций"</a> - Вам понравится!</span>-->
</center><br><br>
<hr>
<div style="font-size: 20px; color: red; text-decoration: blink;" align="center">Фотографии появятся на сайте <b>только после проверки</b> модератором!!!</div>
<form action="" method="post" name="fotos_send" enctype="multipart/form-data">
<table border="0">
<tr>
  <td>Имя:&nbsp;</td>
  <td><input name="f_name" type="text" size="30" value=""></td>
</tr>
<tr>
  <td>Город:&nbsp;</td>
  <td><input name="f_city" type="text" size="30" value=""></td>
</tr>
<tr>
  <td>Пол:&nbsp;</td>
  <td><select size="1" name="f_gener"><option selected value="1">Муж</option><option value="2">Жен</option></select></td>
</tr>
<tr>
  <td>Возраст:&nbsp;</td>
  <td><input name="f_age" type="text" maxlength="2" size="7" value=""></td>
</tr>
<tr>
  <td valign="top">Комментарий:&nbsp;</td>
  <td><input name="f_comment" type="text" size="30" value=""><br><br></td>
</tr>
<tr>
  <td>Файл:&nbsp;</td>
  <td><input type="file" size="30" name="f_file"></td>
</tr>
</table><br><br>
<input type="submit" style="CURSOR: hand" name="make_send" value="Закачать">
</form>
	
<?
		} else {
		echo "<font class='nik' style='COLOR: red'>Для Добавления Фотографий необходима авторизация</font><br><br>";
	}
	
?>