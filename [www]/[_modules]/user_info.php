<h1>������������</h1>

<?php
$bgstr[0]="background='i/bgr-grid-sand.gif'";
$bgstr[1]="background='i/bgr-grid-sand1.gif'";


if(AuthStatus==1 && (abs(AccessLevel) & AccessUsers)) {
if($_REQUEST['doreg']) {
	if(@$_REQUEST['r_nick'] && @$_REQUEST['PasswordRegister'] && @$_REQUEST['Password2Register']) {
		$UserRegister = $_REQUEST['r_nick'];
		$_UserRegister = trim(strtolower($UserRegister));

		if(in_array($_UserRegister, $DisabledRegisterNames)){
			$errs .= '<div class=err>��� "'.$UserRegister.'" �������� � �����������.</div>';
        } elseif(!@$_REQUEST['email']) {
			$errs .= '<div class=err>�� ������ e-mail</div>';
		} else {
			$UserEmail = $_REQUEST['email'];

			if($_REQUEST['PasswordRegister']!=$_REQUEST['Password2Register'])
				$errs .= '<div class=err>������ � ������������� �� ���������</div>';
			else {
				$SQL='SELECT `id` FROM `site_users` WHERE `user_name`=\''.$_UserRegister.'\'';

				$r=mysql_query($SQL);
				if(mysql_num_rows($r)>0)
					$errs .= '<div class=err>������������ "'.$UserRegister.'" ��� ���� � ��</div></div>';
				else {
					$UserPass = $_REQUEST['PasswordRegister'];
					$UserPassMD5 = passhash($UserPass);
					$SQL = 'INSERT INTO `site_users` (`user_name`, `user_pass`, `user_email`) values (\''.$UserRegister.'\', \''.$UserPassMD5.'\', \''.$UserEmail.'\')';
					mysql_query($SQL);
					if(mysql_affected_rows()<1)
						$errs .= '<div class=err>�� ������� ������������������. �������� �������������� �����.</div>';
					else
						$errs .= '<div class=green>�� ������ ������������������.</div>';

				}
			}
		}
	} else {
		$errs .= '<div class=err>��������� �� ��� ������������ ����</div>';
	}
}
?>

 <form name="view_pers" method="post" onsubmit="if(!confirm('������� � �������� �������� ������?\n����������� ��� ������� ������ ���� ��������� ��� ����� � ����� ������� �� �����.\n���������� ��������?')) return false;">
<input type="hidden" name="doreg" value="1">
<input name="act" type="hidden" value="user_info">

<div align=center>
<table style='width: 100%;'  cellspacing='3' cellpadding='5'>
<th height='20' background='i/bgr-grid-sand.gif' style='font-size: 12px;' colspan=5><p>
<strong>����������� ������������</strong>
</p><? echo $errs; ?></th>
<tr>
<td align=center>
<input name="r_nick" type="text" style='width: 150px;' value="�����" onfocus="clear_field(this)" onblur="check_field(this)">
</td>
<td align=center>
<input name="email" type="text" style='width: 150px;' value="@mail" onfocus="clear_field(this)" onblur="check_field(this)">
</td>
<td align=center>
<input name="PasswordRegister" type="text" style='width: 150px;'  value="������" onfocus="clear_field(this)" onblur="check_field(this)">
</td>
<td align=center>
<input name="Password2Register" type="text" style='width: 150px;'  value="��������� ������" onfocus="clear_field(this)" onblur="check_field(this)">
</td>
<td align=center>
<input type="submit" style='color: #008040; width: 150px;' value="������� ���������" style='' >
</td>
</tr>
</table>
</div>
</form>

<br><br>
<table width='100%' border='0' cellspacing='3' cellpadding='5'>
<tr>

<th height='20' background='i/bgr-grid-sand.gif' style='font-size: 12px;'><p>
<strong>�������������� ���������� ���� ����� API</strong>
 </p></th>

 <th height='20' background='i/bgr-grid-sand.gif' style='font-size: 12px;'><p>
<strong>����� ������������</strong>
 </p></th>
  <th height='20' background='i/bgr-grid-sand.gif' style='font-size: 12px;'><p>
<strong>������� �������������</strong>
 </p></th>
</tr>
<tr><form method="GET">
<th>



<input name="act" type="hidden" value="user_info">

<input name="UpdateUserName" type="text" value="">

 <input type="submit" value="��������">





</th>
</form>
<form method="GET">
<th>


<input name="act" type="hidden" value="user_info">

<input name="SearchUserName" type="text" value="">

 <input type="submit" value="�����">

</form>

</th>
<th style='font-size: 12px;'>

<div class='verdana11'><a href="?act=user_info&select=coins">�� ���������� �������� �����</a></div>

<div class='verdana11'><a href="?act=user_info&select=news">�� ���������� ��������</a></div>

<div class='verdana11'><a href="?act=user_info&select=comments">�� ���������� ������������</a></div>

</th>

</tr>

</table>



<?
	if(strlen(@$_REQUEST['UpdateUserName'])>2) {
		$SearchUserName=$_REQUEST['UpdateUserName'];

		$uinfo = TZConn2($SearchUserName, 1);
		#print_r($uinfo);
		if($uinfo['login']) {
			
			// �������
			mysql_query("UPDATE locator SET pro='".$uinfo['pro']."', clan='".$uinfo['clan']."',lvl='".$uinfo['level']."',utime='".time()."' WHERE login='".$uinfo['login']."'");
			// ������������ �����
            mysql_query("UPDATE site_users SET clan='".$uinfo['clan']."' WHERE user_name='".$uinfo['login']."'");
			// ������ ���� ������ �������
			tz_users_update($uinfo);

		}
		$_REQUEST['SearchUserName'] = $SearchUserName;
        echo "<center>���������� (".$uinfo['clan'].") <b>$SearchUserName</b>[".$uinfo['level']."] <img src='http://timezero.ru/i/i".$uinfo['pro'].".gif'>������� ���������.<br></center>";

	}

	$ShowInfo=0;
	if(@$_REQUEST['UserId']>0) {
		$UserId=$_REQUEST['UserId'];
		$SQL="SELECT a.*,b.clan,b.lvl,b.pro,b.pvprank
		FROM site_users AS a LEFT JOIN locator AS b ON(a.user_name=b.login)
		WHERE a.id='$UserId'";
	} else if(!$_REQUEST['UserId'] && strlen(@$_REQUEST['SearchUserName'])>2) {
		$SearchUserName=strtolower($_REQUEST['SearchUserName']);
		$SQL="SELECT a.*,b.clan,b.lvl,b.pro,b.pvprank
		FROM site_users AS a LEFT JOIN locator AS b ON(a.user_name=b.login) WHERE a.user_name='$SearchUserName'";
	}

	if(@$_REQUEST['UserId']>0 || strlen(@$_REQUEST['SearchUserName'])>2) {
		if(@$_REQUEST['SetClan']) {
			$SetClan=$_REQUEST['SetClan'];
			mysql_query("UPDATE site_users SET clan='$SetClan' WHERE id='$UserId'");
		}
		if(@$_REQUEST['AddCoins']) {
			$AddCoins=$_REQUEST['AddCoins'];
			mysql_query("UPDATE site_users SET coins=coins+'$AddCoins' WHERE id='$UserId'");
		}
		if(@$_REQUEST['ShutUp']) {
			$ShutUp=$_REQUEST['ShutUp']+time();
			#echo "shut to $ShutUp <= ".time()." <hr>";
			mysql_query("UPDATE site_users SET banned='$ShutUp' WHERE id='$UserId'");
		}
		if(@$_REQUEST['ShutDown']) {
			$ShutDown=time();
			mysql_query("UPDATE site_users SET banned='$ShutDown' WHERE id='$UserId'");
		}
		if(@$_REQUEST['rights'] && (abs(AccessLevel) & AccessUsersAdmin)) {
			$rights=$_REQUEST['rights'];
			mysql_query("UPDATE site_users SET user_group='$rights' WHERE id='$UserId'");
		}
		if (isset($_REQUEST['SetAccess']) && (abs(AccessLevel) & AccessUsersAdmin)) {
			list($max) = mysql_fetch_array(mysql_query("SELECT MAX(id) FROM AccessLevels"));
			$access = 0;
			for ($i=1; $i<=$max; $i++) $access += $_REQUEST['al'.$i];
			mysql_query("UPDATE site_users SET AccessLevel='$access' WHERE id='$UserId'");
		}
		if (isset($_REQUEST['restricted']) && (abs(AccessLevel) & AccessUsersAdmin)) {
			$access = $_REQUEST['restricted'];
			mysql_query("UPDATE site_users SET restricted_area='$access' WHERE id='$UserId'");
		}
		if(@$_REQUEST['do'] == "changepass" && (abs(AccessLevel) & AccessUsersAdmin)) {
	        $pass1 = $_REQUEST['pass1'];
			$pass2 = $_REQUEST['pass2'];
			if (($pass1 !== $pass2) && strlen($pass1)<6) {
				echo ("��������� ������ ������� �������� ��� �� ���������");
			} else {
				$newpass = passhash($pass1);
				$query = "UPDATE `site_users` SET `user_pass` = '".$newpass."', `user_remind` = '', `user_email` = 'none@nowhere.com' WHERE `id` = '".$_REQUEST['uid']."' LIMIT 1;";
				//echo ($query);
				mysql_query($query) or die (mysql_error());
				echo ("<b>������ ������� �������</b><br><br><b>private [".$_REQUEST['nick4priv']."] ��� ������ � email �� ����� ������� ��������. ����� ������: ".$pass2.". ����� ����: none@nowhere.com (��� ����� ����� ������� ���� ����� � ���� \"�������\")</b>");
			}

		}

		$r=mysql_query($SQL);

		if(mysql_num_rows($r)<1) {
			echo $mess['UserNotFound'];
		} else {



			$d=mysql_fetch_array($r);

			$clan=GetClan($d['clan']);

			$nick=GetUser($d['id'],$d['user_name'],AuthUserGroup);
			$nick2 = $d['user_name'];

?>



<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>

<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>������������: <?=$clan." ".$nick?>  </strong> </p></td>

</tr><tr><td>



<form method="GET">

<input name="UserId" type="hidden" value="<?=$d['id']?>">

<input name="act" type="hidden" value="user_info">

�������������� � �����: <select size="1" name="SetClan">

	<option value="no">--- ��� ---</option>



<?

$clan_dir="_imgs/clans/";

$dd=opendir($clan_dir);

$counter = 0;

while(($CurFile=readdir($dd))!==false) if(is_file("$clan_dir/$CurFile")) {

	$FileType=GetImageSize("$clan_dir/$CurFile");

	$CurFile=explode(".",$CurFile);

	if($FileType[2]==1)

    	{

			$clans[$counter] = $CurFile[0];

            $counter++;

        }

}

sort($clans, SORT_STRING);

reset($clans);

while (list($key, $val) = each($clans))

	{
		echo "	<option value=\"{$val}\"";

		if($d['clan']=="$val") echo " selected";

		echo ">{$val}</option>";
	}



closedir($dd);

?>



</select>

<br><br>

����������� �� ���� ��������� <input name="AddCoins" type="text" value="0" size=2> ����� (������ � ���������: <b><?=$d['coins']?></b> ���.)

<br><br>



<?


if($d['banned']>time()) {

	$MolDiff=$d['banned']-time();

	echo "����� ������� ��� <b>".gmdate("z� H� i� s�",$MolDiff)."</b> &nbsp; <input name='ShutDown' type='checkbox' value=1> ����� ��������";

} else {

?>

�������� �������� �������� ��

<select size="1" name="ShutUp" selected>

	<option value="0">-- �������� --</option>

	<option value="1800">30 ���</option>

	<option value="3600">1 ���</option>

	<option value="10800">2 ����</option>

	<option value="21600">6 �����</option>

	<option value="43200">12 �����</option>

	<option value="86400">1 �����</option>

	<option value="172800">2 �����</option>
    <option value="432000">5 �����</option>
    <option value="604800">7 �����</option>
    <option value="864000">10 �����</option>
    <option value="1728000">20 �����</option>
    <option value="2592000">30 �����</option>


</select>



<?

?>
<?

}

?>



<br><br>

<input type="submit" value="��������">

</form>



<p>

IP-������, � ������� ������� ������������: <b><?=$d['ips']?></b>

<br>

����� ���������� ������: <b><?=date("d.m.Y H:i",$d['last_visit'])?></b>

<br>

��������� ��������:

<b><?=date("d.m.Y H:i",$d['banned'])?></b>

<br><br>

<b>�������������� ������� (��������� 5):</b>

<?

$res=mysql_query("SELECT id, news_title FROM news WHERE poster_id='".$d['id']."' ORDER BY news_date DESC LIMIT 5");

if(mysql_num_rows($res)<1) echo "<i>���</i>";

else {

	while($dt=mysql_fetch_array($res)) echo "<br> &nbsp;&nbsp;<b>&raquo; </b><a href='?act=news_comments&IdNews=".$dt['id']."'>".$dt['news_title']."</a>";

}

?>



<?if(abs(AccessLevel) & AccessUsersAdmin) {?>

<tr><td >

<form method="GET">

<input name="UserId" type="hidden" value="<?=$d['id']?>">

<input name="act" type="hidden" value="user_info">

������� ������� ������������ (������): <input name="rights" type="text" value="<?=$d['user_group']?>" size=2>

<input type="submit" value="������">

</form>

<form method="GET" onsubmit="if(!confirm('��������! ������ ��� �������, ���� �� ������� ��� ����������� �����, ����� �� ��������. ����� `������`.')) return false;">

<input name="UserId" type="hidden" value="<?=$d['id']?>">

<input name="act" type="hidden" value="user_info">

�������������� �����:  <input name="restricted" type="text" size="25" value="<?=$d['restricted_area']?>" >

<input type="submit" value="������" >

</form>





<form method="POST" action="?act=user_info">

<input name="UserId" type="hidden" value="<?=$d['id']?>">

<table>

<tr><td width=350 colspan=2 height='20' bgcolor=#F4ECD4><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong> ����� ������������: </strong></td></tr>



<?

$SQL = "SELECT id, AccessName, Name, AccessLevel FROM AccessLevels";

$a = mysql_query($SQL);

$np = 0;

while (list($id, $accname, $name, $level) = mysql_fetch_array($a)) {

if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

//if ($accname !== "AccessOPQueue" && $accname !== "AccessOP") // ������� �� ������ ������� �� ���������
//{

?>

<tr><td <?=$bg?>><?=$name?></td><td  <?=$bg?>><input type=checkbox name=al<?=$id?> value=<?=$level.(abs($d['AccessLevel']) & abs($level)?' checked':'')?>></td></tr>

<?
//}
}

?>



<tr><td colspan=2 align=center><input type=submit name=SetAccess value="����������"></td></tr>

</table>

</form>

<!--<a href="#;return false;" onclick="if(confirm('������� ������������?')) top.location='?act=user_info&UserId=<?=$d['id']?>&delete=1'">������� ������������</a>-->
<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>

<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>������� ������:  </strong> </p></td>

</tr><tr><td>

<form name="newpass" method="POST" action="?act=user_info&UserId=<?=$d['id']?>">
����� ������:<br>
<input type="password" name="pass1">
<br><br>
����� ������ ��������:<br>
<input type="password" name="pass2">
<br><br>
<input type="submit" value="��������">
<input type="hidden" name="uid" value="<?=$d['id']?>">
<input type="hidden" name="nick4priv" value="<?=$nick2?>">
<input type="hidden" name="do" value="changepass">
<br><br>
<font size="-2">����������� ����� ������ 6 ��������.<br>
��� ����� ������ �������� � �������� ���� ������� ������.<br>
����� ���� - <b>none@nowhere.com</b> (�������� �� ���� ������������, �.�. ��� ����� ����� ��� �������� ������� ������)</font>
</form>
</td></tr>
</table>
</td></tr>

<?}?>



</td></tr>

</table>



<?

		}

}



if(strlen($_REQUEST['select'])>2) {



	switch($_REQUEST['select']) {

		case 'coins': $SQL="SELECT id,user_name,clan, coins AS val FROM site_users WHERE coins>0 ORDER BY coins DESC"; $StrHdr="������� �� ���-�� �����"; break;

		case 'news': $SQL="SELECT u.id,u.user_name,u.clan,count(n.id) AS val FROM news n LEFT JOIN site_users u ON n.poster_id=u.id GROUP BY u.id ORDER BY val DESC"; $StrHdr="������� �� ���-�� �������������� ��������"; break;

        case 'comments': $SQL="SELECT u.id,u.user_name,u.clan,count(c.id) AS val FROM comments c LEFT JOIN site_users u ON c.id_user=u.id GROUP BY u.id ORDER BY val DESC"; $StrHdr="������� �� ���-�� ������������"; break;

	}



	$r=mysql_query($SQL);

    if(mysql_num_rows($r)<1) echo "<h2>������������, ��������������� �������, �� �������</h2>";

    else {



    	echo "<h2>$StrHdr</h2>";

    	echo "<div align=center><table width=300>";

        while($d=mysql_fetch_array($r)) {

			echo "

            	<tr>

                <td nowrap>".GetClan($d['clan']).GetUser($d['id'],$d['user_name'],AuthUserGroup)."</td>

                <td nowrap><b>".$d['val']."</b></td>

                </tr>

            ";

        }

        echo "</table></div>";



    }



}


} else {

	echo $mess['AccessDenied'];

}

?>