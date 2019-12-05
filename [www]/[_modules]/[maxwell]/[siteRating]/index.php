<script src="http://www.tzpolice.ru/_modules/maxwell/siteRating/siteRating.js" type="text/javascript" encoding="UTF-8"></script>
<style>
.siteRating_sites {
    border-collapse: collapse;
}
.siteRating_sites td {
    padding: 2px;
    border: 1px solid #7d7d7d;
}
button {
    background-color: white;
    border-bottom: 1px solid rgb(128, 128, 128);
    border-left: 1px solid rgb(212, 208, 200);
    border-right: 1px solid rgb(128, 128, 128);
    border-top: 1px solid rgb(212, 208, 200);
    cursor: pointer;
    font-size: 12px;
    height: 20px;
}
</style>
<?php
require_once('/home/sites/police/www/_modules/functions.php'); // functions + DB connect
require_once('/home/sites/police/www/_modules/auth.php'); // authorization
require_once('siteRatingFunctions.php'); // siteRating functions


$access = getAccess();
if ($access) {
    echo "[<a href=\"http://www.tzpolice.ru/?act=siterating_admin\">admin panel</a>]<br>";
}
?>


Сайт(поиск осуществляется как по домену, так и по названию клана):<br>
<input type='text' name='site' id='site'><br>
<button onClick="checkSite(); return false;" id="checkbtn">Проверить</button><br>
<br>
<div id="response"></div><br>


