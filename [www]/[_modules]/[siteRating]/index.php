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

// =========== ������ ������ ��������� ���� ==============================================

function InitAccessArr() 
{
	// �������������� ������ ���� ������� � ��������� ������
	$access['read'] = true;
	$access['managment'] = false;
	// $access['admin'] = false; - ������������� �������������� ��� ��������� ����. 

	return $access;
}

function InitLableArr()
{
	//���������	������ �������� ����
	$lable['read']	  =	"��������";
	$lable['managment']	  =	"���������� �������";
	// $lable['admin']	= "�����������������"; - ������������� ��������������	���	��������� ����.	
	
	return $lable;
}

$module_name  =	"siteRating/admin";
$module_lable =	"�������� ����-������";

if (isset($onlyfunc)) return true;

$access	= InitAccessArr(); // �������������	������
$access	= GetAccessArr($module_name, $access); // ���������	������ �������

echo "<h1>$module_lable</h1>";

// =========== ����� ������ ��������� ���� ===============================================

require_once('/home/sites/police/www/_modules/functions.php'); // functions + DB connect
require_once('/home/sites/police/www/_modules/auth.php'); // authorization
require_once('siteRatingFunctions.php'); // siteRating functions


$access = getAccess($access);
if ($access) {
    echo "[<a href=\"http://www.tzpolice.ru/?act=siterating_admin\">admin panel</a>]<br>";
}
?>


����(����� �������������� ��� �� ������, ��� � �� �������� �����):<br>
<input type='text' name='site' id='site'><br>
<button onClick="checkSite(); return false;" id="checkbtn">���������</button><br>
<br>
<div id="response"></div><br>


