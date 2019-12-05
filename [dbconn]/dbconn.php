<?
error_reporting(0);
//@$db=mysql_pconnect("127.0.0.6","tzpolice","dKs8hfd123bn");
//@$db=mysql_connect('192.168.253.6', 'tzpolice', 'dKs8hfd123bn');

@$db=mysql_connect('192.168.253.6', 'tzpolice', 'uDKs8NthHdsMtBUW');

if (!$db) {
	foreach($_REQUEST as $k=>$v) {
		$log_get_list .= "$k => $v|";
	}
	
	error_log(date('d.m.Y H:i:s')." Error: Could not connect to database. [requests: $log_get_list] ".$_SERVER['REMOTE_ADDR']."\n", 3, date('d.m.Y')."-mysql-errlog.txt");
echo <<<HTML
<html>
<head>
<META name="verify-v1" content="h3liW5b59AM60c42sw30IYEu/7xxzKCYdkXvcHujYM4=" />
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
  <title>TZ Police Department site ERROR</title>
</head>
<body>
<!--
Server responce: <?=mysql_error()?>

-->
<br><br><blockquote style="font-family:verdana;font-size:11px">
<b>Простите, сайт временно недоступен</b><br>
Надеемся увидеть вас чуть позже.<br>
Информация для каторжан: все ресурсы обязательно будут учтены позже.<br><br>
<b>We are sorry, but our site is unavailable right now</b><br>
Please come back soon.<br>
For prisoners: all resourses will be counted as soon as possible.
</blockquote>
</body>
</html>
HTML;
	exit;
}
mysql_select_db('tzpolice');
mysql_query('SET NAMES CP1251');

define ('MYSQL_DB_CONNECTION', $db);

?>
