
<?php

define("MPLIB",true);
include("mp_library.php");

$userLoginNickName = AuthUserName;// "DreamMaster"; // todo
$userLoginClanName = AuthUserClan;// "Military Police"; // todo


if ($in['target'] == "Special") {
	
	echo("<h3>������ ������ Military Police</h3>");	
	
} else {
	
	echo("<h3>׸���� ������ Military Police</h3>");	
	
} 


if (isMilitaryPoliceClan($userLoginClanName) && alreadyInManagersList($userLoginNickName) || AuthUserGroup == 100) {
	
	echo("<table border=0><tr><td style=\"vertical-align: top;\">");
	
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'">׸���� ������ (��)</a>
				</strong>
			</p>';
			
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=addClan">���������� ����� � ��</a>
				</strong>
			</p>';
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=addPlayer">���������� ������� � ��</a>
				</strong>
			</p>';
			
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=editPlayer">�������������� ������ � ��������� � ��</a>
				</strong>
			</p>';

		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=removeClan">�������� ����� �� ��</a>
				</strong>
			</p>';
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=removePlayer">�������� ��������� �� ��</a>
				</strong>
			</p>';

			
	echo("</td><td style=\"vertical-align: top;\">");	
		
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=Special">������ ������ (��)</a>
				</strong>
			</p>';

		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=addSpecial">���������� ��������� � ��</a>
				</strong>
			</p>';
			
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=toSpecial">������� ��������� � ��</a>
				</strong>
			</p>';
			
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=clanToSpecial">������� ����� � ��</a>
				</strong>
			</p>';
			
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=toBlack">������� ��������� � ��</a>
				</strong>
			</p>';

	echo("</td><td style=\"vertical-align: top;\">");		
		
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=verifyClans">�������� ������������ �������� ��</a>
				</strong>
			</p>';
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=management">��������� ������� � ���������� ��</a>
				</strong>
			</p>';
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=journal">������ ����������� ��������� �� ��</a>
				</strong>
			</p>';
	
	echo("</td></tr></table>");
			
	echo '<hr/>';

	if ($in['target'] == "addClan") {
		include("mpblack_add_clan.php");
	} else if ($in['target'] == "removeClan") {
		include("mpblack_remove_clan.php");
	} else if ($in['target'] == "verifyClans") {
		include("mpblack_verify_clans.php");
	} else if ($in['target'] == "addPlayer") {
		include("mpblack_add_player.php");
	// ������ 24.04.2018 +	
	} else if ($in['target'] == "addSpecial") {
		include("mpblack_add_special.php");
	} else if ($in['target'] == "Special") {
		include("mpblack_sp_list.php");
	} else if ($in['target'] == "NewList") {
		include("mpblack_list_new.php");		
	} else if ($in['target'] == "toSpecial") {
		include("mpblack_to_special.php");
	} else if ($in['target'] == "toBlack") {
		include("mpblack_to_black.php");
	} else if ($in['target'] == "clanToSpecial") {
		include("mpblack_clan_to_special.php");		
	// ������ 24.04.2018 -	
	} else if ($in['target'] == "editPlayer") {
		include("mpblack_edit_player.php");
	} else if ($in['target'] == "removePlayer") {
		include("mpblack_remove_player.php");
	} else if ($in['target'] == "management") {
		include("mpblack_manage.php");
	} else if ($in['target'] == "journal") {
		include("mpblack_journal.php");
	} else {
		include("mpblack_list.php");
	}
} else {
	
	if ($in['target'] == "Special") {
		include("mpblack_sp_list.php");
	} else {
		include("mpblack_list.php");	
	}
	
}
?>