<html>

<head>
  <title></title>
</head>

<body>

<?php
require("functions.php");
require("auth.php");
//if(AuthUserName=="Madimonster" || AuthUserName=="������" ||AuthUserName=="arey" || AuthUserName=="Darth Vader" || AuthUserName=="HorrorTM " || AuthUserName=="Hrist" || AuthUserName=="������" || AuthUserName=="ASlab" || AuthUserName=="cfyz" || AuthUserName=="Odrik" || AuthUserName=="Xelas" || AuthUserName=="deadbeef" || AuthUserName=="integer")
//{

if(@$_REQUEST['item']) $item = $_REQUEST['item'];
else die("��������� ������. ���������� ��� ���");

$query = 'SELECT id, id_user, order_str, t2 FROM items_order WHERE order_str REGEXP "'.$item.'" OR order_str REGEXP "|'.$item.'";';
$r = mysql_query($query) or die(mysql_error());
while (list($d) = mysql_fetch_row($r))
	{
    	echo ("id=".$d[0].", id_user=".$d[1].", order_str=".$d[2].", t2=".$d[3]."<br>");
    }


//}
//else
//{
echo("fuck off");
//}

?>

</body>

</html>