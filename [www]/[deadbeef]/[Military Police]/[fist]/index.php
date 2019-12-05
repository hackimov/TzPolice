<html>

<head>
  <title></title>
</head>

<body>
<form name="send" action="?act=send" method="post">
Message: <input type="text" name="mess">
UIN: <input type="text" name="uin">
<input type="submit" name="submit" value="go">
</form>
<?
error_reporting(0);
require ("icq_lib.php");
if (isset($_POST['mess']))
	{
	    $icq = new ICQclient("206747576","Fist8527");
	    $icq->connect();
	    $icq->setstatus($icq->const["STATUS_INVISIBLE"]);
	    $icq->setstatusflags($icq->const["STATUSFLAG_DCDISABLED"],$icq->const["STATUSFLAG_BIRTHDAY"]);
	    $icq->login();
	    $icq->message_send($_POST['uin'],$_POST['mess']);
    }
?>
</body>

</html>