<script src="http://www.tzpolice.ru/_modules/siteRating/siteRating.js" type="text/javascript" encoding="UTF-8"></script>
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

// =========== начало модуля менеджера прав ==============================================

function InitAccessArr() 
{
	// инициализируем массив прав доступа к элементам модуля
	$access['read'] = true;
	$access['managment'] = false;
	// $access['admin'] = false; - идентификатор зарезервирован под системное поле. 

	return $access;
}

function InitLableArr()
{
	//заполняем	массив названий прав
	$lable['read']	  =	"Просмотр";
	$lable['managment']	  =	"Управление списком";
	// $lable['admin']	= "Администрирование"; - идентификатор зарезервирован	под	системное поле.	
	
	return $lable;
}

$module_name  =	"siteRating/admin";
$module_lable =	"Проверка клан-сайтов";

if (isset($onlyfunc)) return true;

$access	= InitAccessArr(); // иницилизируем	массив
$access	= GetAccessArr($module_name, $access); // заполняем	массив правами

echo "<h1>$module_lable</h1>";

// =========== конец модуля менеджера прав ===============================================

require_once('/home/sites/police/www/_modules/functions.php'); // functions + DB connect
require_once('/home/sites/police/www/_modules/auth.php'); // authorization
require_once('siteRatingFunctions.php'); // siteRating functions


$access = getAccess($access);
if ($access) {
    echo "[<a href=\"http://www.tzpolice.ru/?act=siterating_admin\">admin panel</a>]<br>";
}
?>


Сайт(поиск осуществляется как по домену, так и по названию клана):<br>
<input type='text' name='site' id='site'><br>
<button onClick="checkSite(); return false;" id="checkbtn">Проверить</button><br>
<br>
<div id="response"></div><br>


