<?php
if(AuthStatus==1 && AuthUserName!="") {

      echo "<h1>���������� ������� ������� ".GetClan(AuthUserClan).GetUser(AuthUserId,AuthUserName,AuthUserGroup)."</h1>";
      $SQL="SELECT * FROM site_users WHERE id='".AuthUserId."'";
      $d=mysql_fetch_array(mysql_query($SQL));



?>

<blockquote>
� ��� � �������: <b><?=$d['coins']?></b> �������� �����
<?
      if($d['banned']>time()) {
         $MolDiff=$d['banned']-time();
         echo "<br>�� �� ������ ��������� ����������� ��� <b>".gmdate("z��. H� i� s�",$MolDiff)."</b>";
      }
?>



<form method="post">
<input name="act" type="hidden" value="user_home">
<input name="change" type="hidden" value="password">
<table>
<tr><td colspan=2>
<b>����� ������:</b>
<?
// change password
   if($_REQUEST['change']=='password') {
   if($_REQUEST['CurrentPassword']=="" || $_REQUEST['NewPassword']=="" || $_REQUEST['NewPassword2']=="") echo "<div class=err>�� ��������� ���� �� �����</div>";
   elseif($_REQUEST['NewPassword']!=$_REQUEST['NewPassword2']) echo "<div class=err>������ � ������������� �� ���������</div>";
   elseif(passhash($_REQUEST['CurrentPassword'])!=$d['user_pass']) echo "<div class=err>�� ����� �������� ������� ������!</div>";
   else {

         $NewPass=$_REQUEST['NewPassword'];
         $NewPassMD5=passhash($NewPass);
         $SQL="UPDATE site_users SET user_remind='', user_pass='$NewPassMD5' WHERE id='".AuthUserId."'";
         mysql_query($SQL);
$text="IP: ".$_SERVER['REMOTE_ADDR']." changed password for
user: '".AuthUserName."'
newpass: $NewPass
SQL: $SQL;
";
$subject="TZ Police password change";
$back_mail="site_abuse@tzpolice.ru";
$show_mail="reminder@tzpolice.ru";
$from_who="TZpolice.ru";

/*
Mail("kde@tzpolice.ru",
$subject,
$text,
"Return-Path: $back_mail\r\n".
"Content-type: text/plain; charset=windows-1251\r\n".
"Reply-To: $back_mail\r\n".
"From: $from_who <$show_mail>\r\n".
"Content-type: text/plain; charset=windows-1251\r\n");
*/


         if(mysql_affected_rows()<1) echo "<div class=err>�� ������� ������� ������. �������� �������������� �����.</div>";
         else echo "<script>top.location.href='?act=news&logoff=1'</script><div class=green>������ �������. <br>��� ���������� ��������� � �������!</div>";

    }}

?>
</td></tr>
<tr><td>������� ������:</td><td>
<input name="CurrentPassword" type="password" value="">
</td></tr>
<tr><td>�������� ������:</td><td>
<input name="NewPassword" type="password" value="">
</td></tr>
<tr><td>��������� ������:</td><td>
<input name="NewPassword2" type="password" value="">
</td></tr>
<tr><td colspan=2 align=center>
<input type="submit" value="������� ������">
</td></tr>
</table>
</form>


<form method="post">
<input name="act" type="hidden" value="user_home">
<input name="change" type="hidden" value="email">
<table>
<tr><td colspan=2>
<b>����� e-mail:</b>
<?
// change e-mail


   if($_REQUEST['change']=='email') {
   if($_REQUEST['CurrentPassword']=="" || $_REQUEST['CurrentPassword']=="" || $_REQUEST['NewEMail']=="" || $_REQUEST['NewEMail2']=="") echo "<div class=err>�� ��������� ���� �� �����</div>";
   elseif($_REQUEST['NewEMail']!=$_REQUEST['NewEMail2']) echo "<div class=err>����� e-mail � ������������� �� ���������</div>";
   elseif(passhash($_REQUEST['CurrentPassword'])!=$d['user_pass']) echo "<div class=err>�� ����� �������� ������� ������!</div>";
   elseif($_REQUEST['CurrentEMail']!=$d['user_email']) echo "<div class=err>�� ����� ������������ ������� e-mail!</div>";
   else {

         $NewEMail=$_REQUEST['NewEMail'];

         $SQL="UPDATE site_users SET user_email='$NewEMail' WHERE id='".AuthUserId."'";
         mysql_query($SQL);

$text="IP: ".$_SERVER['REMOTE_ADDR']." changed e-mail for
user: '".AuthUserName."'
e-mail: $NewEMail
SQL: $SQL;
";

$subject="TZ Police e-mail change";
$back_mail="site_abuse@tzpolice.ru";
$show_mail="reminder@tzpolice.ru";
$from_who="TZpolice.ru";

/*
Mail("kde@tzpolice.ru",
$subject,
$text,
"Return-Path: $back_mail\r\n".
"Content-type: text/plain; charset=windows-1251\r\n".
"Reply-To: $back_mail\r\n".
"From: $from_who <$show_mail>\r\n".
"Content-type: text/plain; charset=windows-1251\r\n");
*/

         if(mysql_affected_rows()<1) echo "<div class=err>�� ������� ������� e-mail. �������� �������������� �����.</div>";
         else echo "<div class=green>E-mail �������</div>";

    }}

?>
</td></tr>
<tr><td>������� ������:</td><td>
<input name="CurrentPassword" type="password" value="">
</td></tr>
<tr><td>������� e-mail:</td><td>
<input name="CurrentEMail" type="text" value="">
</td></tr>
<tr><td>�������� e-mail:</td><td>
<input name="NewEMail" type="text" value="">
</td></tr>
<tr><td>��������� e-mail:</td><td>
<input name="NewEMail2" type="text" value="">
</td></tr>
<tr><td colspan=2 align=center>
<input type="submit" value="������� e-mail">
</td></tr>
</table>
</form>
<b>��������� ��� �� �����������</b><br><br>
<?
if ($_REQUEST['setfotos'] == 'male')
	{
		$query = "UPDATE `fotos_main` SET `gener`='1' WHERE `nick`='".AuthUserName."'";
		mysql_query($query);
		$query = "UPDATE `fotos_users` SET `gener`='1' WHERE `nick`='".AuthUserName."'";
		mysql_query($query);
		echo ("��� ���� ���������� �������� ��� <b>���: ���</b><br><br>");
	}
else if ($_REQUEST['setfotos'] == 'female')
	{
		$query = "UPDATE `fotos_main` SET `gener`='0' WHERE `nick`='".AuthUserName."'";
		mysql_query($query);
		$query = "UPDATE `fotos_users` SET `gener`='0' WHERE `nick`='".AuthUserName."'";
		mysql_query($query);
		echo ("��� ���� ���������� �������� ��� <b>���: ���</b><br><br>");
	}
?>
�������� ��� ��� ���������� ��� 
<ul><li><a href="/?act=user_home&setfotos=male">�������</a>
<li><a href="/?act=user_home&setfotos=female">�������</a></ul>
</blockquote>
<?
} else {
        echo $mess['AccessDenied'];
    echo $mess['WantRegister'];
}
?>