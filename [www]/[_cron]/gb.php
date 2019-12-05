<?php
$ID = $_GET['id'];
$url = "http://city2.timezero.ru/getbattle?id=$ID";
$data = file_get_contents($url);
echo $data;
?>