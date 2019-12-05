<h1>Пользователи сервиса XXX</h1>
Добавить пользователя:

<?php
define(AccessSomething, $_GET['accessLevel']);
require('/home/sites/police/www/_modules/functions.php'); // functions + DB connect
require('/home/sites/police/www/_modules/auth.php'); // authorization
?>
<form name="add_form" method="post" action="?act=XXX&accessLevel=<?php echo $_GET['accessLevel'];?>">
  <input type="text" name="nick" value="">
  <input type="submit" name="Submit" value="Добавить">
</form>
<?php


$bgstr[0]="background='i/bgr-grid-sand.gif'";
$bgstr[1]="background='i/bgr-grid-sand1.gif'";
$want = AccessSomething; // Нужный уровень доступа
$want = $want-1;
if(AuthStatus==1 && (AuthUserGroup == 100)) {
if ($_REQUEST['nick'])
	{

	$query = "SELECT * FROM `site_users` WHERE `user_name` = '".$_REQUEST['nick']."' LIMIT 1;";
        $res = mysql_query($query);
        $tmp = mysql_fetch_array($res);
        if (abs($tmp['AccessLevel']) & AccessSomething)
        	{
            	echo ("Пользователь уже имеет доступ к сервису");
            }
        else
        	{
                
	            $new_lvl = $tmp['AccessLevel'] + AccessSomething;
	            $query = "UPDATE `site_users` SET `AccessLevel` = '".$new_lvl."' WHERE `id` = '".$tmp['id']."' LIMIT 1;";
	            mysql_query($query);
                print "ok";
            }
    }
if ($_REQUEST['del'] > 0)
	{
	$query = "SELECT * FROM `site_users` WHERE `id` = '".$_REQUEST['del']."' LIMIT 1;";
        $res = mysql_query($query);
        $tmp = mysql_fetch_array($res);
        if (abs($tmp['AccessLevel']) & AccessSomething)
        	{
	            $new_lvl = $tmp['AccessLevel'] - AccessSomething;
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
		//echo ("<li><pre>");
	print_r($tmp);
		//echo ("</pre>");
    	if (abs($tmp['AccessLevel']) & AccessSomething)
        	{
            	echo ("<li><b>".$tmp['user_name']."</b> [<a href='?act=XXX&del=".$tmp['id']."'>X</a>]");
            }
    }
echo ("</ul>");
} else {

	echo $mess['AccessDenied'];

}

?>