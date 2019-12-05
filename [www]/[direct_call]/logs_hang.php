<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<title>Бот</title>
<?
	include('/home/sites/police/www/_modules/header.php');
?>
</head>
<body>
<?
require("../_modules/functions.php");
require("../_modules/auth.php");
//if(AuthUserClan=='Military Police' || AuthUserName=='deadbeef' || AuthUserName=='Mahno' || AuthUserName=='Deorg')
$names = file('/home/sites/police/bot/accessnames.txt');
$grant_access = 0;
foreach ($names as $line_num => $line)
	{
		if (AuthUserName == trim($line)) {$grant_access = 1;}
	}
if (!$grant_access) {die('Кыш отседова! Таких не знаем...');}

if (filesize('/home/sites/police/bot/queue.txt') > 0)
	{
		$qcount = 0;
		$lines = file('/home/sites/police/bot/queue.txt');
		foreach ($lines as $line_num => $line)
			{
				$tmp = explode('|',$line);
				$current_queue[$line_num]['line'] = trim($line);
				$current_queue[$line_num]['time'] = $tmp[5];
				$current_queue[$line_num]['from'] = $tmp[1];
				if (trim($line) <> '') {$qcount++;}
			}
		$curtime=time();
		$diff = $curtime-$current_queue[0]['time'];
		if ($diff > 300)
			{    	
//				echo ($curtime."-".$current_queue[0][5]."=".$diff);
				if ($_REQUEST['do'] == 'unlock')
					{
						if ($qcount > 1)
							{
								touch('/home/sites/police/bot/tzbot.lock');
								sleep(2);
								$qfile = fopen('/home/sites/police/bot/queue.txt','w');
								
								$starti = ($_REQUEST['task'] == 'all')?0:1;
								
								for ($i=$starti; $i <= $qcount; $i++)
									{
										if ($_REQUEST['task'] == 'all' && $current_queue[$i]['from'] == AuthUserName) {
											continue;
										}
										
										if (strlen($current_queue[$i]['line']) > 0) 
											{
												fwrite($qfile,$current_queue[$i]['line'].'
');											}
									}
								fclose($qfile);
								unlink('/home/sites/police/bot/tzbot.lock');
								
								if ($_REQUEST['task'] == 'all') {
									echo ('Все ваши задания удалены, бот перезапущен. Перед повторной очисткой очереди подождите хотя бы минут 5.');
								} else {
									echo ('Зависшее задание удалено, бот перезапущен, оставшиеся задания будут продолжены сразу после перезапуска бота. Перед повторной очисткой очереди подождите хотя бы минут 5.');
								}
							}
						else
							{
								echo ('В очереди всего одно задание. Даже если оно и висит - пусть висит, никому не мешает =)');
							}
					}
				else
					{
						echo ('<a href="/direct_call/logs_hang.php?do=unlock&task=last">Удалить последнюю задачу</a><br> <a href="/direct_call/logs_hang.php?do=unlock&task=all">Удалить все мои задачи</a><br><br>Если бот уже давно не реагирует - нажмите на одну из ссылок выше и подождите. Если не помогает - private[Ксакеп] или private[Текс]');
					}
			}
		else
			{
				echo ('Пожалейте вы бедного бота! Подумаешь, задумался немножко... Над текущим заданием он работает меньше пяти минут, вот когда будет больше - тогда наверное завис.');
			}
	}
else
	{
		echo ('Завис, завис... Отдыхает он! Очередь пустая - вот и отдыхает.');
	}
?>
</body></html>