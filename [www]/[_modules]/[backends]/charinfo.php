<?php
session_start();
require_once "../xhr_config.php";
require_once "../xhr_php.php";
require("../functions.php");
require("../auth.php");
$JsHttpRequest =& new Subsys_JsHttpRequest_Php("windows-1251");
$nick = trim($_REQUEST['n']);
$userinfo = GetUserInfo($nick);
if (!$userinfo["error"] && $userinfo['level'] > 0)
	{
        if ($userinfo['man'] == 0)
        	{
            	$pro = $userinfo['pro']."w";
            }
        else
        	{
            	$pro = $userinfo['pro'];
            }
        $_RESULT = array("res" => "OK");
	    if (strlen($userinfo['clan']) > 2)
	        {
	            echo("[pers clan={$userinfo['clan']} nick={$userinfo['login']} level={$userinfo['level']} pro={$pro}]");
	        }
	    else
	        {
	            echo("[pers clan=0 nick={$userinfo['login']} level={$userinfo['level']} pro={$pro}]");
	        }
    }
else
	{
		$_RESULT = array("res" => "error");
		if($userinfo["error"] == "TIMEOUT")
        	{
            	echo ("<center>Ошибка связи с сервером <b>TimeZero</b>. Попробуйте позже.</center>");
            }
		elseif ($userinfo["error"] == "USER_NOT_FOUND")
        	{
             	echo ("<center>Персонаж <b>{$nick}</b> не найден. Проверьте правильность ввода ника.</center>");
            }
		elseif ($userinfo["error"] == "ERROR_IN_USER_NAME")
        	{
             	echo ("<center>Имя <b>{$nick}</b> не может быть именем персонажа <b>TimeZero</b>. Проверьте правильность ввода ника.</center>");
            }
		elseif ($userinfo["error"] == "NOT_CONNECTED")
        	{
             	echo ("<center>Сервер <b>TimeZero</b> недоступен.</center>");
            }
		else
        	{
				echo ("<center>Произошла непредвиденная ошибка. Попробуйте позже.</center>");
				print_r($userinfo);
            }
    }
?>