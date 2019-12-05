<?
//error_reporting(0);
error_reporting(E_ALL);
$host = '192.168.253.6';
$user = 'tzpolice';
$password = 'DKobrA9zk7JRTxvQ';
$db = 'tzpolice';
//@$db=mysql_pconnect("127.0.0.6","tzpolice","dKs8hfd123bn");
//@$db=mysql_connect('192.168.253.6', 'tzpolice', 'dKs8hfd123bn');

@$db=mysql_connect('192.168.253.6', 'tzpolice', 'DKobrA9zk7JRTxvQ');

//$db=mysql_pconnect("127.0.0.6","tzpolice","dKs8hfd123bn",MYSQL_CLIENT_INTERACTIVE);
//mysql_query("SET SESSION interactive_timeout=120", $db) or die(mysql_error());
if (!$db)
{
	error_log(date('d.m.Y H:i:s')."Error: Could not connect to database.\n", 3, 'errlog.txt');
        echo '<br><br><blockquote style="font-family:verdana;font-size:11px"><b>Нет связи с базой данных!</b><br>
        Проверьте параметры соединения. <br><br>
        Ответ сервера: "'.mysql_error().'"</blockquote>';
	exit;
}
mysql_select_db('tzpolice');
mysql_query('SET NAMES CP1251');
?>
