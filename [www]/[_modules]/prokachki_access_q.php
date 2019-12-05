<h1>Пользователи, имеющие право добавлять логи на проверку</h1>
Добавить пользователя:
<form name="add_form" method="post" action="?act=prokachki_access_q">
  <input type="text" name="nick" value="">
  <input type="submit" name="Submit" value="Добавить">
</form>
<?php
$bgstr[0]="background='i/bgr-grid-sand.gif'";
$bgstr[1]="background='i/bgr-grid-sand1.gif'";
$want = AccessOPQueue;
$want = $want-1;
if(AuthStatus==1 && AuthUserGroup == 100) {
if ($_REQUEST['nick'])
	{
		$query = "SELECT * FROM `site_users` WHERE `user_name` = '".$_REQUEST['nick']."' LIMIT 1;";
        $res = mysql_query($query);
        $tmp = mysql_fetch_array($res);
        if (abs($tmp['AccessLevel']) & AccessOPQueue)
        	{
            	echo ("Пользователь уже имеет доступ к сервису");
            }
        else
        	{
	            $new_lvl = $tmp['AccessLevel'] + AccessOPQueue;
	            $query = "UPDATE `site_users` SET `AccessLevel` = '".$new_lvl."' WHERE `id` = '".$tmp['id']."' LIMIT 1;";
	            mysql_query($query);
            }
    }
if ($_REQUEST['del'] > 0)
	{
		$query = "SELECT * FROM `site_users` WHERE `id` = '".$_REQUEST['del']."' LIMIT 1;";
        $res = mysql_query($query);
        $tmp = mysql_fetch_array($res);
        if (abs($tmp['AccessLevel']) & AccessOPQueue)
        	{
	            $new_lvl = $tmp['AccessLevel'] - AccessOPQueue;
	            $query = "UPDATE `site_users` SET `AccessLevel` = '".$new_lvl."' WHERE `id` = '".$tmp['id']."' LIMIT 1;";
	            mysql_query($query);
            }
    }
$query = "SELECT * FROM `site_users` WHERE `AccessLevel` > '".$want."';";
//echo ($query);
$res = mysql_query($query) or die(mysql_error());
echo ("<ul>");
while ($tmp = mysql_fetch_array($res))
	{
//		echo ("<li><pre>");
//		print_r($tmp);
//		echo ("</pre>");
    	if (abs($tmp['AccessLevel']) & AccessOPQueue)
        	{
            	echo ("<li><b>".$tmp['user_name']."</b> [<a href='?act=prokachki_access_q&del=".$tmp['id']."'>X</a>]");
            }
    }
echo ("</ul>");
} else {

	echo $mess['AccessDenied'];

}

?>