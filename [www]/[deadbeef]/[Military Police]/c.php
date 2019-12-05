<?php
error_reporting(0);
$today=date("Y-m-d");
$nick = iconv('UTF-8', 'cp1251', $_GET['b']);
$log = $_GET['a']." : ".$nick." : ".$_GET['c']."\r\n";
$fname="/logs/".$today.".$log";
chmod($fname, 0777);
$fp = fopen("logs/".$today.".log", "a+");
fwrite($fp, $log);
fclose($fp);
// Создаем новое изображение из файла
$im = ImageCreateFromPNG('c.png');
Header("Content-type: image/png");
ImagePng($im);
?>