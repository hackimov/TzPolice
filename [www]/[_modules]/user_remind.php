<h1>����������� ������</h1>

<?php
//mail("deadbeef@tzpolice.ru", "TZPolice.ru password reminder", "Line 1\nLine 2\nLine 3","From: deadbeef@tzpolice.ru \r\nX-Mailer: PHP/" . phpversion()); 
/*
"Return-Path: $back_mail\r\n".
"Reply-To: $back_mail\r\n".
"From: $from_who <$show_mail>\r\n".
"Content-type: text/plain;charset=windows-1251\r\n");
*/

function reminder_send_telegramm($nick, $message){
	$noerror=1;
	if (AuthStatus==1)
		{
			$message = "\n".AuthUserName." || ".$nick." || ".$message."\n";
		}
	else
		{
			$message = "\n0xDEADBEEF || ".$nick." || ".$message."\n";
		}
	$filename = '/home/sites/police/bot_fees/alerts.txt';
	if (file_exists($filename))
		{
			chmod($filename, 0777);
		}
	if ($handle = fopen($filename, 'a'))
		{
			if (fwrite($handle, $message) === FALSE)
				{
					$noerror=0;
				}
			fclose($handle);
		}
	else
		{
			$noerror=0;
		}
		return $noerror;
	}

//error_reporting(E_ALL);
// change password
if(strlen($_REQUEST['usercode'])>3 && @$_REQUEST['userid']) {
$query = "SELECT `id`, `user_name` FROM `site_users` WHERE `id` = '".$_REQUEST['userid']."' AND `user_remind` = '".$_REQUEST['usercode']."' LIMIT 1;";
$res = mysql_query($query);
if (mysql_num_rows($res) > 0)
{
   if($_REQUEST['NewPassword']=="" || $_REQUEST['NewPassword2']=="") echo "<div class=err>�� ��������� ���� �� �����</div>";
   elseif($_REQUEST['NewPassword']!=$_REQUEST['NewPassword2']) echo "<div class=err>������ � ������������� �� ���������</div>";
   else {
         $NewPass=$_REQUEST['NewPassword'];
         $NewPassMD5=passhash($NewPass);
	 if (strlen(trim($_REQUEST['NewMail'])) > 5)
		{
	        	$SQL="UPDATE site_users SET user_remind='', user_pass='".$NewPassMD5."', user_email='".trim($_REQUEST['NewMail'])."' WHERE id='".$_REQUEST["userid"]."'";
			$add_str = " � e-mail";
		}
	 else
		{
	        	$SQL="UPDATE site_users SET user_remind='', user_pass='".$NewPassMD5."' WHERE id='".$_REQUEST["userid"]."'";
			$add_str = "";
		}
         mysql_query($SQL);
         if(mysql_affected_rows()<1) echo "<div class=err>�� ������� ������� ������. �������� �������������� �����.</div>";
         else echo "<div class=green>������".$add_str." �������. <br>�� ������ ����� �� ����.</div>";
    }}
else
{
echo ("<div class=err>�� ������� ������� ������. �������� �������������� �����2.</div>");
}}
elseif(@$_REQUEST['RemindLogin']) {
	$RemindLogin=strip_tags($_REQUEST['RemindLogin']);
	$RemindLoginL=strtolower($RemindLogin);
	$res=mysql_query("SELECT id,user_email,user_name FROM site_users WHERE `user_name`='$RemindLoginL' LIMIT 1");
	if(mysql_num_rows($res)>0) {
    	$u=mysql_fetch_array($res);
        $reminder = $u['user_name'].$u['user_email'].date("D-m-Y").chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).rand(1,9999);
        $reminder = passhash($reminder);
        $query = "UPDATE `site_users` SET `user_remind` = '".$reminder."' WHERE `id` = '".$u["id"]."' LIMIT 1;";
        mysql_query($query) or die (mysql_error());
	$sendto=$u['user_email'];
	$text="\r\n===================== KOI8-R =====================\r\n\r\n���-�� � IP: ".$_SERVER['REMOTE_ADDR']." �������� ������� ������ ���\r\n������������ '".$u['user_name']."' ����� ������� TimeZero (http://www.tzpolice.ru).\r\n���� �� �� ����������� ������, ������ �������� ��� ������ ��� ��������.\r\n�� �������� ��� ������, ��� ��� ��� ����������� ������������ ��� ������ ����\r\ne-mail. ���� �� �� ������ ��������� � ����� �����, �������� �� ���� ��\r\nsite_abuse@tzpolice.ru , ����� �������� ���� �� �����.\r\n\r\n--------------------------------------------------------------------------\r\n\r\n��� ��������� ������ ������� ������ ������������ ".$u['user_name']." ���������\r\n�� ��������� ������:\r\nhttp://www.tzpolice.ru/?act=user_remind&uid=".$u['id']."&code=".$reminder."\r\n����� �������� ��� ����� ���������� ������� ����� ������.\r\n\r\n---------------------------------------------------------------------------\r\n";
	$text = convert_cyr_string($text, "w", "k");
	$text.="\r\n===================== WIN-1251 =====================\r\n\r\n���-�� � IP: ".$_SERVER['REMOTE_ADDR']." �������� ������� ������ ���\r\n������������ '".$u['user_name']."' ����� ������� TimeZero (http://www.tzpolice.ru).\r\n���� �� �� ����������� ������, ������ �������� ��� ������ ��� ��������.\r\n�� �������� ��� ������, ��� ��� ��� ����������� ������������ ��� ������ ����\r\ne-mail. ���� �� �� ������ ��������� � ����� �����, �������� �� ���� ��\r\nsite_abuse@tzpolice.ru , ����� �������� ���� �� �����.\r\n\r\n---------------------------------------------------------------------------\r\n\r\n��� ��������� ������ ������� ������ ������������ ".$u['user_name']." ���������\r\n�� ��������� ������:\r\nhttp://www.tzpolice.ru/?act=user_remind&uid=".$u['id']."&code=".$reminder."\r\n\r\n����� �������� ��� ����� ���������� ������� ����� ������.\r\n\r\n---------------------------------------------------------------------------\r\n";
	$text .= "===================== ASCII (Eng) =====================\r\n\r\nSomebody from IP: ".$_SERVER['REMOTE_ADDR']." requested password reminder for\r\nuser '".$u['user_name']."' at web-site of TimeZero Police (http://www.tzpolice.ru).\r\nIf you did not request the password, just pay no attention to this letter.\r\nYou've got this message because your e-mail had been entered in registration\r\nform for this user. If you don't concern this web-site, please, mail to\r\nsite_abuse@tzpolice.ru to beware of spam.\r\n\r\n---------------------------------------------------------------------------\r\n\r\nIn order to change password for user ".$u['user_name']." please follow\r\nthe link below:\r\n\r\nhttp://www.tzpolice.ru/?act=user_remind&uid=".$u['id']."&code=".$reminder."\r\n\r\nAfter that you will be prompted to enter a new password.\r\n\r\n---------------------------------------------------------------------------\r\n";
	$subject="TZ Police password reminder";
	$headers = "From: TZPolice Reminder <site_abuse@tzpolice.ru>\r\n"; 
	Mail($sendto, $subject, $text, $headers);
	$telegram_text = "���-�� � IP: ".$_SERVER['REMOTE_ADDR']." �������� ������� ������ ��� ������������ [".$u['user_name']."] ����� ������� TimeZero (http://www.tzpolice.ru). ���� �� �� ����������� ������, ������ �������� ��� ��������� ��� ��������. ��� ��������� ������ ������� ������ ������������ [".$u['user_name']."] ��������� �� ��������� ������: http://www.tzpolice.ru/remind-".$u['id']."-".$reminder." - ����� �������� ��� ����� ���������� ������� ����� ������.";
	//reminder_send_telegramm ($u['user_name'],$telegram_text);
	echo "<p><b>���������� �������.</b></p>";

    } else echo "<h2>���������� ������������ �� ����������</h2>";

}
elseif (@$_REQUEST['uid'] && strlen($_REQUEST['code'])>3)
{
$query = "SELECT `id`, `user_name` FROM `site_users` WHERE `id` = '".$_REQUEST['uid']."' AND `user_remind` = '".$_REQUEST['code']."' LIMIT 1;";
$res = mysql_query($query);
if (mysql_num_rows($res) > 0)
{
?>
<form method="POST" action="/?act=user_remind">
<input name="usercode" type="hidden" value="<?=$_REQUEST['code']?>">
<input name="userid" type="hidden" value="<?=$_REQUEST['uid']?>">
<table>
<tr><td colspan=2>
<b>����� ������:</b>
</td></tr>
<tr><td>�������� ������:</td><td>
<input name="NewPassword" type="password" value="">
</td></tr>
<tr><td>��������� ������:</td><td>
<input name="NewPassword2" type="password" value="">
</td></tr>
<tr><td>����� email ��� �����������:<br><small>�������� ������ ����� �� ������</small></td><td>
<input name="NewMail" type="text" value="">
</td></tr>
<tr><td colspan=2 align=center>
<input type="submit" value="������� ������ � email">
</td></tr>
</table>
</form>

<?
}
else
{
echo ("������ ������ ���������������");
}
}
else {
?>

<form method="POST">
<input name="act" type="hidden" value="user_remind">
<p>��� ������������ �� �����: <input name="RemindLogin" type="text" value=""> <input type="submit" value="��������� ������">
<br><br>* ���������� �� �������������� ������ ����� ������� �� ����������� �����, ��������� ��� �����������
</p></form>

<?}?>