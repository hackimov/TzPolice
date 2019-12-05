<?
echo (date("d.m.Y H:i", $_REQUEST['d']));

$bases=array();
$bases[0] = "tzpolice_rating_screen";
$bases[1] = "tzpolice_pvprating_screen";

print_r($bases);

error_reporting(E_ALL);

$screen_time = mktime(date("H"), 0, 0, date("m"), date("d"), date("Y"));

echo ("<br>".$screen_time."<br>");
echo ("<br>".date("d.m.Y H:i", $screen_time));

?>
