<html>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<head>
<?include("../header.php")?>
<?include("../java.php")?>
<?include("../functions.php")?>
</head>
<?php
if (@$_REQUEST['makeadd']) {
  $userinfo = GetUserInfo(trim(strtolower($_REQUEST['login'])));
}
?>
<body bgcolor=#F6F3E9 <?php if (@$_REQUEST['makeadd']) { echo "onload=\"AddLogin1('$userinfo[login]','$userinfo[clan]','$userinfo[level]','$userinfo[pro]');\""; } ?>>

<br>
<form name="AddLogin">
&nbsp;&nbsp;&nbsp;Ник: <input name="login" type="text" value="">
&nbsp;&nbsp;&nbsp;<input type="submit" name="makeadd" value="Add">
</form>


</body>
</html>