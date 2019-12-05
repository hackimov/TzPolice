<html>

<head>
  <title>.::Red Button Hall::.</title>
  <LINK href="_modules/tzpol_css.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
</head>

<body style="margin: 5px;">
<?php
require("_modules/functions.php");
require("_modules/auth.php");
echo (AuthUserName?'Привет, '.AuthUserName:'Вы не авторизованы!')."!<hr>";

#print_r($_COOKIE);

$authlist[] = 'Ксакеп';
$authlist[] = 'Help';
$authlist[] = 'RAWarrior';
$authlist[] = 'Ex_president';

if(!in_array(AuthUserName,$authlist))	{
	die ('Nothing here! Go away!');
}

#echo exec("/home/sites/police/bots/killer.pl 4408");
#echo "<br>";

$home = '/home/sites/police/bot';
$home2 = '/home/sites/police/bot_fees';
$home3 = '/home/sites/police/bot_pa';
$home4 = '/home/sites/police/bots/bot_mp';
$home5 = '/home/sites/police/bots/prison';
$home6 = '/home/sites/police/bots/forpost_ads';
$home7 = '/home/sites/police/bots/smarts_monitor';

if($_REQUEST['act'] == 'stop_tp'){
	$f = fopen($home.'/tzbot.lock', 'w');
	if ($f !== false) {
		echo 'процесс остановлен';
		fclose($f);
	}else{
		echo 'ошибка';
	}

}elseif($_REQUEST['act'] == 'start_tp'){
	unlink($home.'/tzbot.lock');
	echo 'в течении двух минут бот загрузится';
}

if($_REQUEST['act'] == 'stop_02'){
	$f = fopen($home2.'/tzbot.lock', 'w');
	if ($f !== false) {
		echo 'процесс остановлен';
		fclose($f);
	}else{
		echo 'ошибка';
	}

}elseif($_REQUEST['act'] == 'start_02'){
	unlink($home2.'/tzbot.lock');
	echo 'в течении двух минут бот загрузится';
}

if($_REQUEST['act'] == 'stop_pa'){
	$f = fopen($home3.'/tzbot.lock', 'w');
	if ($f !== false) {
		echo 'процесс остановлен';
		fclose($f);
	}else{
		echo 'ошибка';
	}

}elseif($_REQUEST['act'] == 'start_pa'){
	unlink($home3.'/tzbot.lock');
	echo 'в течении двух минут бот загрузится';
}

if($_REQUEST['act'] == 'stop_mp'){
	$f = fopen($home4.'/tzbot.lock', 'w');
	if ($f !== false) {
		echo 'процесс остановлен';
		fclose($f);
	}else{
		echo 'ошибка';
	}

}elseif($_REQUEST['act'] == 'start_mp'){
	unlink($home4.'/tzbot.lock');
	echo 'в течении двух минут бот загрузится';
}

if($_REQUEST['act'] == 'stop_pr'){
	$f = fopen($home5.'/tzbot.lock', 'w');
	if ($f !== false) {
		echo 'процесс остановлен';
		fclose($f);
	}else{
		echo 'ошибка';
	}

}elseif($_REQUEST['act'] == 'start_pr'){
	unlink($home5.'/tzbot.lock');
	echo 'в течении двух минут бот загрузится';
}
if($_REQUEST['act'] == 'stop_ft'){
	$f = fopen($home6.'/tzbot.lock', 'w');
	if ($f !== false) {
		echo 'процесс остановлен';
		fclose($f);
	}else{
		echo 'ошибка';
	}

}elseif($_REQUEST['act'] == 'start_ft'){
	unlink($home6.'/tzbot.lock');
	echo 'в течении двух минут бот загрузится';
}

if($_REQUEST['act'] == 'stop_sm'){
	$f = fopen($home7.'/tzbot.lock', 'w');
	if ($f !== false) {
		echo 'процесс остановлен';
		fclose($f);
	}else{
		echo 'ошибка';
	}

}elseif($_REQUEST['act'] == 'start_sm'){
	unlink($home7.'/tzbot.lock');
	echo 'в течении двух минут бот загрузится';
}

echo '<BR><BR>';

if(file_exists($home.'/tzbot.lock')){
	echo '<A HREF="?act=start_tp">START terminal police + hony + mahoney + Военный эксперт</A>';
	echo '<BR><BR>';
}else{
	echo '<A HREF="?act=stop_tp">STOP terminal police + hony + mahoney + Военный эксперт</A>';
	echo '<BR><BR>';
}
if(file_exists($home2.'/tzbot.lock')){
	echo '<A HREF="?act=start_02">START terminal 02</A>';
	echo '<BR><BR>';
}else{
	echo '<A HREF="?act=stop_02">STOP terminal 02</A>';
	echo '<BR><BR>';
}

if(file_exists($home3.'/tzbot.lock')){
	echo '<A HREF="?act=start_pa">START terminal pa</A>';
	echo '<BR><BR>';
}else{
	echo '<A HREF="?act=stop_pa">STOP terminal pa</A>';
	echo '<BR><BR>';
}

if(file_exists($home4.'/tzbot.lock')){
	echo '<A HREF="?act=start_mp">START Полицейские Рок и Винд</A>';
	echo '<BR><BR>';
}else{
	echo '<A HREF="?act=stop_mp">STOP Полицейские Рок и Винд</A>';
	echo '<BR><BR>';
}
if(file_exists($home6.'/tzbot.lock')){
	echo '<A HREF="?act=start_ft">START Terminal Forpost + Дядя Стёпа</A>';
	echo '<BR><BR>';
}else{
	echo '<A HREF="?act=stop_ft">STOP Terminal Forpost + Дядя Стёпа</A>';
	echo '<BR><BR>';
}
if(file_exists($home7.'/tzbot.lock')){
	echo '<A HREF="?act=start_sm">START Smart Terminal + PDA</A>';
	echo '<BR><BR>';
}else{
	echo '<A HREF="?act=stop_sm">STOP Smart Terminal + PDA</A>';
	echo '<BR><BR>';
}

?>



<hr size="1">Доступ к запросам логов у Terminal Police<br>
<?
if ($_REQUEST['do'] == 'save')
	{
		$newnicks=$_REQUEST['anicks'];
		$newnicks=str_replace("\r\n", "\n", $newnicks);
		$fp = fopen('/home/sites/police/bot/anames.txt', 'w');
		fwrite($fp, $newnicks);
		fclose($fp);
		echo ("<font color='green'><b>Изменения сохранены!</b></font>");
	}

?>
<br><br>
<form name="TPAccess" method="post">
<input type="hidden" name="do" value="save">
<textarea name="anicks" rows="15" cols="75">
<?
$lines = file('/home/sites/police/bot/anames.txt');

foreach ($lines as $line_num => $line)
	{
		echo ($line);
	}
?>
</textarea><br>
<input type="submit" value="сохранить">
</form>
</body>
</html>