<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Available avatars</title>
<style type="text/css">
<!--
.img_div {
	height: 90px;
	width: 90px;
	display: inline-block;
	background-color: #FFFFCC;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #333333;
	text-align: center;
	margin: 10px;
	padding: 5px;
	padding-top: 10px;
	border: 1px dashed #333333;
}
body {
	background-color: #E5E5E5;
	margin: 0px;
	padding: 0px;
}
-->
</style>
</head>
<body>
<?php
$dir    = 'i/avatar';
$files = scandir($dir);
//$files2 = scandir($dir, 1);
foreach ($files as $key => $value)
	{
		if ($value !== "." && $value !==".." && $value !=="avat-default-man.gif" && $value !=="avat-default-woman.gif" && $value !=="***.jpg")
			{
				echo ("<div class='img_div'><img border='0' src='/i/avatar/".$value."'><br><br>".$value."</div>");
			}
	}
?>
</body></html>