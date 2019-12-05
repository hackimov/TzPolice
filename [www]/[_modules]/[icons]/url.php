<html>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<head>
<?include("../header.php")?>
<?include("../java.php")?>
</head>
<body bgcolor=#F6F3E9>

<br>
<form name="AddURL">
&nbsp;&nbsp;&nbsp;URL:<br>
&nbsp;&nbsp;&nbsp;<input name="URL" type="text" value="http://" style="width:90%" ONKEYUP="document.AddURL.URLdesc.value=document.AddURL.URL.value"><br>
&nbsp;&nbsp;&nbsp;Описание:<br>
&nbsp;&nbsp;&nbsp;<input name="URLdesc" type="text" onclick="if(document.AddURL.URLdesc.value==document.AddURL.URL.value) {document.AddURL.URLdesc.value=''}" value="" style="width:90%">
<br>
&nbsp;&nbsp;&nbsp;<input type="button" value="Add" onclick="AddURL1()">
</form>


</body>
</html>