<?
Header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
Header("Cache-Control: no-cache, must-revalidate");
Header("Pragma: no-cache");
Header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

require("/home/sites/police/www/_modules/functions.php");

//require("../_modules/functions.php");
//require("../_modules/auth.php");
/*$link = mysql_connect("localhost", "tzpolice_test", "pWemi45bnjms")
  or die ("Could not connect to MySQL");

mysql_select_db ("tzpolice_test")
  or die ("Could not select database");
mysql_query("SET NAMES cp1251");*/
//=========================
//замена тому что выше
 require_once("/home/sites/police/dbconn/dbconn2.php");
//=========================
$nick = $_REQUEST['login'];
$lang = $_REQUEST['lang'];

print_r(date("d-m-Y H:i"));

$query = "SELECT * FROM `bot_prison_logs` WHERE `data` LIKE 'godman%' ORDER BY `bot_prison_logs`.`id` ASC";
$rs = mysql_query($query);
$date="";
$text = "";
while($row = mysql_fetch_array($rs)){

	if($row["log_date"] != $date){
		$date = $row["log_date"];
		if($text != "") $text .= "кол-во=".$count;
		$text .= "<HR>".$row["log_date"]."<HR>\n";
		$count=0;
	}
	eregi("(.*)\[(.*)\]", $row["data"], $regs);
	$count = $count + $regs[2];

}
$text .= "кол-во=".$count;
echo $text;
mysql_close($link);
?>