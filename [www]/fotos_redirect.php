<?
require ("_modules/a.charset.php");
$good_nick = charset_x_win($_REQUEST['nick']);
header('Location: http://www.tzpolice.ru/?act=fotos&nick='.$good_nick);
?>