<h1>��������</h1>
<?php

/**
 * @author deadbeef
 * @copyright 2009
 */

$avatars[] = '1001';
$avatars[] = '1002';
$avatars[] = '1003';
$avatars[] = '1004';
$avatars[] = '1005';

if (AuthStatus == 1)
	{
		if ($_REQUEST['refresh'] == 1)
			{
				$query = "DELETE FROM `data_cache` WHERE `user_name` = '".AuthUserName."' AND `lastupdate` < '".(time()-600)."' LIMIT 1;";
				//echo ($query);
				$res=mysql_query($query);
				//print_r($res);
				//echo (" :: ".mysql_affected_rows($res));
				$query = "SELECT * FROM `data_cache` WHERE `user_name` = '".AuthUserName."' LIMIT 1;";
//				echo ($query);
				$res=mysql_query($query);
	//			echo (mysql_num_rows($res));
		//		if (mysql_num_rows($res) == 0)
			//		{
						foreach ($avatars as $key => $value)
							{
								$file = "/home/sites/police/www/ubar/ready/".AuthUserId."-".$value.".jpg";
								unlink ($file);
							}
				//	}
			}
		$userinfo = GetUserInfo(AuthUserName);
        if ($userinfo["error"] || $userinfo['level'] < 1)
        	{
        		echo ("��������, �� ������� �������� ���������� �� ���������. �������������� ������� ��������������� ���������� ������.");
        	}
        else
        	{
				echo("��������, ��������������� ��� ��������� ");
				if ($userinfo['man'] == 0)
		        	{
		            	$pro = $userinfo['pro']."w";
		            }
		        else
		        	{
		            	$pro = $userinfo['pro'];
		            }
			    if (strlen($userinfo['clan']) > 2)
			        {

			            echo("<img src='/_imgs/clans/{$userinfo['clan']}.gif' alt='{$userinfo['clan']}' border='0'><b>{$userinfo['login']}</b> [{$userinfo['level']}]<img style='vertical-align:text-bottom' src='/_imgs/pro/i{$pro}.gif' border='0'>");
			        }
			    else
			        {
			            echo("<b>{$userinfo['login']}</b> [{$userinfo['level']}]<img style='vertical-align:text-bottom' src='/_imgs/pro/i{$pro}.gif' border='0'>");
			        }
			}
		$file = "/home/sites/police/www/ubar/ready/".AuthUserId."-1001.jpg";
		if (is_file($file) && filemtime($file) < (time()-600))
			{
		?>
		<br /><br />
		<form method="GET">

		<input type="hidden" name="act" value="userbars">
		<input type="hidden" name="refresh" value="1">
		<input type="submit" name="submit" value="�������� ������ �� ���������">
		</form>
		<?
			}
		else
			{				if (is_file($file)) {echo ("<br><br>��������� ���������� ����������: <b>".date("d.m.Y H:i", filemtime($file))."</b>");}
				else echo ("<br><br>��������� ���������� ����������: <b>".date("d.m.Y H:i")."</b>");			}
		reset ($avatars);
		foreach ($avatars as $key => $value)
			{
				?>
				<hr size=1>
				<br /><br /><img src="http://www.tzpolice.ru/userbar/<?=AuthUserId?>/<?=$value?>.jpg"><br /><br />
				<b>URL:</b> http://www.tzpolice.ru/userbar/<?=AuthUserId?>/<?=$value?>.jpg<br />
				<b>BBCode:</b> [URL=http://www.tzpolice.ru/userbars][IMG]http://www.tzpolice.ru/userbar/<?=AuthUserId?>/<?=$value?>.jpg[/IMG][/URL]<br />
				<b>HTML:</b> &lt;a target="_blank" href="http://www.tzpolice.ru/userbars"&gt;&lt;img src="http://www.tzpolice.ru/userbar/<?=AuthUserId?>/<?=$value?>.jpg"&gt;&lt;/a&gt;
				<?
			}
	}
else
	{
		?>
		������ �������� ������ <a href="http://www.tzpolice.ru/?act=register">�������������</a>.
		<?
	}
?>
<hr />
<br><br>
���������� �� ��������� ����������� �� ���� ������ ���� � ���. ���� �� ������� ������������� �������� ���������� (��������, ������� ���� � ������� �������� �� ���� ����) - �� ��-������ ���������, ��� ������ �� ��������� ���������� ���������� � ����� ��������� (��������, �� ������ ��), ��-������ - ������� ������ "�������� ������ �� ���������" (������ �������� ��� ������������� ��� � 10 �����).
<br><hr size=1>
<h1>���, ���� �� �������� ��������</h1>
 ���� �� ������ ���������� ���� �������, ����������� ��� � ������������ �� ���������� ������������:
 <br>
 <br> <ol><li>����� �������� ������ ���� ������� � ���� ����� <b>PSD</b>, ��������� �������������� � <b>Adobe Photoshop CS3</b>. ��� ������� ������ �������������� ����������� ������� ������������ ���������� (��������� ���������, ����, ������ � ������ - ��������, ��� � ���������� ������� ���� �������� ���������� - ��������), ��� ������� - ������ ������� �������� ������ ������������� �� ��������� ����.
 <br>
 <br> <li>� ������ ������ ����������� ����� ���� �������, ������������ � ��������. ��� ������ ������������ ���������� ��������� ������������� <b><u>������ TTF-�������</u></b> ��� ������������� ������������� (�.�. ����� <b>��</b> ������ ���� <b>������</b>, <i>���������</i>, <u>������������</u>).
 <br>
 <br> <li>��� ��������� �� �������� ������ ���� ������� ����� <b>TTF-�������</b>, ������� ������������� �������.
 <br>
 <br> <li>�� �������� ����� ����������: ������, ��������� ��������� (����, ��������, ������������ � �.�.), ���, �������, ���������, ����, PvP-������. ����������� ������ ������ ������ �������������� ������� � <img src='_imgs/clans/Police Academy.gif' alt='Police Academy' border='0'><b>deadbeef</b> [14]<img style='vertical-align:text-bottom' src='_imgs/pro/i2.gif' border='0'>. ������� �������������: ����� ������, ����������� �������������, ���������� (���� ������ � ��� ����� ������� �� ����������, ����� ������ �� ���������� � ���������).
 <br>
 <br> <li>��� ������ ������� ��������� �������� ���������� ��� ����, ��� ������ ������ � ��� ������������� (<b>28*16</b>), ������� � ������ ��������� ����������, ������ ������ ���������� ���� ������������� (<b>45*16</b>). ��������� (�� �� �������������) ��������� �������� ������� � ����������� �������� ���������. ��������� ��������� ������� ��������� ������ � ������, ���� ��� ��������� � ����� ������ �������.
 <br>
 <br> <li>������ ����������, �� �� �����������, ������� �� �������� �������� <img src='_imgs/clans/admins.gif' alt='admins' border='0'><b>TimeZero</b> � ������� <img src='_imgs/clans/police.gif' alt='police' border='0'><b>tzpolice.ru</b>. ������� ������ �� ����� ������ ����� <u>�����������</u>.
 <br>
 <br> <li>������ �������� ������ ���� ��������. �� ������ ������ �������� �������� ����� ������� <b>400*120</b> ��������.
 <br>
 <br> <li>����� �������� ������ �� �������� ����� ��������� � ����� (<b>RAR</b>, <b>ZIP</b> ��� <b>7z</b>) � ��������� ������� � ����� <<b>������� ��� ����� �������</b>> �� ������ <b>tz.deadbeef@gmail.com</b>
 <br> ����� �������� �� �������� ������ ����������� �� ��� <img src='_imgs/clans/Police Academy.gif' alt='Police Academy' border='0'><b>deadbeef</b> [14]<img style='vertical-align:text-bottom' src='_imgs/pro/i2.gif' border='0'> � ��������� ����������� ��  ������ ���� ������ ����� ��������� ����-��������.
 <br>
 <br> <li>������ ������ ������� ����� ������������ �� �������� ��������������</ol>    
