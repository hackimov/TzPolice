<?
//$link = mysql_connect('192.168.253.6', 'tzpolice_test', 'pWemi45bnjms') or die ('Could not connect to MySQL');

$link = mysql_connect('192.168.253.6', 'tzpolice_test', 'oXNrFiUGWCoHEp9Z') or die ('Could not connect to MySQL');

mysql_select_db ('tzpolice_test') or die ('Could not select database');

mysql_query('SET NAMES CP1251');

define ('MYSQL_DB_CONNECTION2', $link);

?>
