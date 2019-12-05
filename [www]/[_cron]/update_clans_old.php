<?
//error_reporting(E_ALL);
require ("/home/sites/police/www/_modules/functions.php");
$html = file_get_contents("http://www.timezero.ru/clans/list.ru.html");
$clans = sub($html, '<!--blockstart-->', '<!--blockend-->');
$clans = str_replace("%20", " ", $clans);
//$clans .= "'";
$arr = explode("<td>",$clans);
$tmp = 1;
$fname = "***HellSing***";
$fname .= ".gif";
$t = file_get_contents("http://www.timezero.ru/i/clans/".$fname);
$w = fopen("../_imgs/clans/".$fname, "w");
fwrite($w, $t);
fclose($w);
foreach($arr as $v)
	{
//	echo ("====<br>".$v."<br>=====");
	$fname = sub($v, '"> ', '[');
	$fname = str_replace("</a>", "", $fname);
	$fname = trim($fname);
	$fname .= ".gif";
//	echo ($tmp.$fname."<br>");
//	$tmp++;
//    	$fname = str_replace("%20", " ", $rf);
        $rf = str_replace(" ", "%20", $fname);
        $t = file_get_contents("http://www.timezero.ru/i/clans/".$rf);
        $w = fopen("../_imgs/clans/".$fname, "w");
        fwrite($w, $t);
        fclose($w);
    }
//unlink("/home/sites/police/www/_imgs/clans/',0'.gif");
?>