<?
//error_reporting(0);
error_reporting(E_ALL);
$host = '192.168.253.6';
$user = '';
$password = '';
$db = 'tzpolice';
//@$db=mysql_pconnect("127.0.0.6","tzpolice","");
//@$db=mysql_connect('192.168.253.6', 'tzpolice', '');

@$db=mysql_connect('192.168.253.6', 'tzpolice', '');

//$db=mysql_pconnect("127.0.0.6","tzpolice","",MYSQL_CLIENT_INTERACTIVE);
//mysql_query("SET SESSION interactive_timeout=120", $db) or die(mysql_error());
if (!$db)
{
	error_log(date('d.m.Y H:i:s')."Error: Could not connect to database.\n", 3, 'errlog.txt');
        echo '<br><br><blockquote style="font-family:verdana;font-size:11px"><b>Íåò ñâÿçè ñ áàçîé äàííûõ!</b><br>
        Ïðîâåðüòå ïàðàìåòðû ñîåäèíåíèÿ. <br><br>
        Îòâåò ñåðâåðà: "'.mysql_error().'"</blockquote>';
	exit;
}
mysql_select_db('tzpolice');
mysql_query('SET NAMES CP1251');
?>
