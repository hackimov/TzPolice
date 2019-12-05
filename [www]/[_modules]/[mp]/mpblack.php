
<?php

define("MPLIB",true);
include("mp_library.php");

$userLoginNickName = AuthUserName;// "DreamMaster"; // todo
$userLoginClanName = AuthUserClan;// "Military Police"; // todo


if ($in['target'] == "Special") {
	
	echo("<h3>Особый список Military Police</h3>");	
	
} else {
	
	echo("<h3>Чёрный список Military Police</h3>");	
	
} 


if (isMilitaryPoliceClan($userLoginClanName) && alreadyInManagersList($userLoginNickName) || AuthUserGroup == 100) {
	
	echo("<table border=0><tr><td style=\"vertical-align: top;\">");
	
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'">Чёрный список (ЧС)</a>
				</strong>
			</p>';
			
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=addClan">Добавление клана в ЧС</a>
				</strong>
			</p>';
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=addPlayer">Добавление мародёра в ЧС</a>
				</strong>
			</p>';
			
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=editPlayer">Редактирование записи о персонаже в ЧС</a>
				</strong>
			</p>';

		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=removeClan">Удаление клана из ЧС</a>
				</strong>
			</p>';
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=removePlayer">Удаление персонажа из ЧС</a>
				</strong>
			</p>';

			
	echo("</td><td style=\"vertical-align: top;\">");	
		
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=Special">Особый список (ОС)</a>
				</strong>
			</p>';

		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=addSpecial">Добавление персонажа в ОС</a>
				</strong>
			</p>';
			
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=toSpecial">Перевод персонажа в ОС</a>
				</strong>
			</p>';
			
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=clanToSpecial">Перевод клана в ОС</a>
				</strong>
			</p>';
			
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=toBlack">Перевод персонажа в ЧС</a>
				</strong>
			</p>';

	echo("</td><td style=\"vertical-align: top;\">");		
		
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=verifyClans">Проверка актуальности клановых ЧС</a>
				</strong>
			</p>';
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=management">Настройки доступа к управлению ЧС</a>
				</strong>
			</p>';
		echo '
			<p>
				<img width="18" hspace="5" height="11" src="http://www.tzpolice.ru/i/bullet-red-01a.gif">
				<strong>
					<a href="'.$_mp_targetURL.'&target=journal">Журнал регистрации изменений ЧС МП</a>
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
	// Ксакеп 24.04.2018 +	
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
	// Ксакеп 24.04.2018 -	
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