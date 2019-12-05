<h1>Юзербары</h1>
<?php

/**
 * @author deadbeef
 * @copyright 2009
 */

$avatars[] = '1001';
$avatars[] = '1002';
$avatars[] = '1003';
$avatars[] = '1004';
$avatars[] = '1005';

if (AuthStatus == 1)
	{
		if ($_REQUEST['refresh'] == 1)
			{
				$query = "DELETE FROM `data_cache` WHERE `user_name` = '".AuthUserName."' AND `lastupdate` < '".(time()-600)."' LIMIT 1;";
				//echo ($query);
				$res=mysql_query($query);
				//print_r($res);
				//echo (" :: ".mysql_affected_rows($res));
				$query = "SELECT * FROM `data_cache` WHERE `user_name` = '".AuthUserName."' LIMIT 1;";
//				echo ($query);
				$res=mysql_query($query);
	//			echo (mysql_num_rows($res));
		//		if (mysql_num_rows($res) == 0)
			//		{
						foreach ($avatars as $key => $value)
							{
								$file = "/home/sites/police/www/ubar/ready/".AuthUserId."-".$value.".jpg";
								unlink ($file);
							}
				//	}
			}
		$userinfo = GetUserInfo(AuthUserName);
        if ($userinfo["error"] || $userinfo['level'] < 1)
        	{
        		echo ("Простите, не удалось получить информацию по персонажу. Воспользуйтесь кнопкой принудительного обновления данных.");
        	}
        else
        	{
				echo("Юзербары, сгенерированные для персонажа ");
				if ($userinfo['man'] == 0)
		        	{
		            	$pro = $userinfo['pro']."w";
		            }
		        else
		        	{
		            	$pro = $userinfo['pro'];
		            }
			    if (strlen($userinfo['clan']) > 2)
			        {

			            echo("<img src='/_imgs/clans/{$userinfo['clan']}.gif' alt='{$userinfo['clan']}' border='0'><b>{$userinfo['login']}</b> [{$userinfo['level']}]<img style='vertical-align:text-bottom' src='/_imgs/pro/i{$pro}.gif' border='0'>");
			        }
			    else
			        {
			            echo("<b>{$userinfo['login']}</b> [{$userinfo['level']}]<img style='vertical-align:text-bottom' src='/_imgs/pro/i{$pro}.gif' border='0'>");
			        }
			}
		$file = "/home/sites/police/www/ubar/ready/".AuthUserId."-1001.jpg";
		if (is_file($file) && filemtime($file) < (time()-600))
			{
		?>
		<br /><br />
		<form method="GET">

		<input type="hidden" name="act" value="userbars">
		<input type="hidden" name="refresh" value="1">
		<input type="submit" name="submit" value="Обновить данные по персонажу">
		</form>
		<?
			}
		else
			{				if (is_file($file)) {echo ("<br><br>Последнее обновление информации: <b>".date("d.m.Y H:i", filemtime($file))."</b>");}
				else echo ("<br><br>Последнее обновление информации: <b>".date("d.m.Y H:i")."</b>");			}
		reset ($avatars);
		foreach ($avatars as $key => $value)
			{
				?>
				<hr size=1>
				<br /><br /><img src="http://www.tzpolice.ru/userbar/<?=AuthUserId?>/<?=$value?>.jpg"><br /><br />
				<b>URL:</b> http://www.tzpolice.ru/userbar/<?=AuthUserId?>/<?=$value?>.jpg<br />
				<b>BBCode:</b> [URL=http://www.tzpolice.ru/userbars][IMG]http://www.tzpolice.ru/userbar/<?=AuthUserId?>/<?=$value?>.jpg[/IMG][/URL]<br />
				<b>HTML:</b> &lt;a target="_blank" href="http://www.tzpolice.ru/userbars"&gt;&lt;img src="http://www.tzpolice.ru/userbar/<?=AuthUserId?>/<?=$value?>.jpg"&gt;&lt;/a&gt;
				<?
			}
	}
else
	{
		?>
		Сервис доступен только <a href="http://www.tzpolice.ru/?act=register">пользователям</a>.
		<?
	}
?>
<hr />
<br><br>
Информация на юзербарах обновляется не чаще одного раза в час. Если вы желаете принудительно обновить информацию (например, сменили клан и желаете поведать об этом миру) - то во-первых убедитесь, что сервер ТЗ правильно отображает информацию о вашем персонаже (например, на форуме ТЗ), во-вторых - нажмите кнопку "Обновить данные по персонажу" (кнопка доступна для использования раз в 10 минут).
<br><hr size=1>
<h1>Тем, кому не нравятся юзербары</h1>
 Если Вы хотите предложить свой юзербар, подготовьте его в соответствии со следующими требованиями:
 <br>
 <br> <ol><li>Макет юзербара должен быть прислан в виде файла <b>PSD</b>, корректно открывающегося в <b>Adobe Photoshop CS3</b>. Как минимум должна присутствовать возможность скрытия динамической информации (параметры персонажа, клан, аватар и прочее - отдельно, фон и постоянные надписи типа названия параметров - отдельно), как оптимум - каждый элемент юзербара должен располагаться на отдельном слое.
 <br>
 <br> <li>К макету должны прилагаться файлы всех шрифтов, используемых в юзербаре. Для вывода динамической информации разрешено использование <b><u>только TTF-шрифтов</u></b> без использования модификаторов (т.е. текст <b>НЕ</b> должен быть <b>жирным</b>, <i>курсивным</i>, <u>подчеркнутым</u>).
 <br>
 <br> <li>Ник персонажа на юзербаре должен быть написан любым <b>TTF-шрифтом</b>, имеющим кириллические символы.
 <br>
 <br> <li>На юзербаре могут выводиться: аватар, параметры персонажа (сила, ловкость, выносливость и т.д.), ник, уровень, профессия, клан, PvP-звание. Возможность вывода прочих данных согласовывайте заранее с <img src='_imgs/clans/Police Academy.gif' alt='Police Academy' border='0'><b>deadbeef</b> [14]<img style='vertical-align:text-bottom' src='_imgs/pro/i2.gif' border='0'>. Заранее предупреждаем: вывод текста, задаваемого пользователем, недопустим (сюда входят в том числе надписи на кланзнаках, части текста из информации о персонаже).
 <br>
 <br> <li>При выборе размера элементов юзербара учитывайте тот факт, что значки кланов у нас прямоугольные (<b>28*16</b>), аватары и значки профессий квадратные, значки рангов квадратные либо прямоугольные (<b>45*16</b>). Допустимо (но не рекомендуется) изменение размеров значков с соблюдением исходных пропорций. Искажение пропорций значков допустимо только в случае, если это оправдано с точки зрения дизайна.
 <br>
 <br> <li>Крайне желательно, но не обязательно, наличие на юзербаре логотипа <img src='_imgs/clans/admins.gif' alt='admins' border='0'><b>TimeZero</b> и надписи <img src='_imgs/clans/police.gif' alt='police' border='0'><b>tzpolice.ru</b>. Наличие ссылок на любые другие сайты <u>недопустимо</u>.
 <br>
 <br> <li>Размер юзербара должен быть разумным. На данный момент разумным пределом можно считать <b>400*120</b> пикселей.
 <br>
 <br> <li>Макет юзербара вместе со шрифтами нужно упаковать в архив (<b>RAR</b>, <b>ZIP</b> или <b>7z</b>) и отправить письмом с темой <<b>Юзербар для сайта полиции</b>> по адресу <b>tz.deadbeef@gmail.com</b>
 <br> Лучше сообщить об отправке письма телеграммой на имя <img src='_imgs/clans/Police Academy.gif' alt='Police Academy' border='0'><b>deadbeef</b> [14]<img style='vertical-align:text-bottom' src='_imgs/pro/i2.gif' border='0'> с указанием отправителя на  случай если письмо будет задержано спам-фильтром.
 <br>
 <br> <li>Авторы лучших макетов могут рассчитывать на денежное вознаграждение</ol>    
