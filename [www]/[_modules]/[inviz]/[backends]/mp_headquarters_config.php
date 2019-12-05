<?php
	require_once("/home/sites/police/www/_modules/auth.php");
	require_once("/home/sites/police/dbconn/dbconn.php");

	$imageSourceForLoadingAction = 'http://www.tzpolice.ru/_modules/inviz/loading.gif';
	$imageSourceForProgressLoadingAction = 'http://www.tzpolice.ru/_modules/inviz/progress.gif';
	$mpHeadquartersInterface = '_modules/inviz/backends/mp_headquarters_interface.php';

	$mpAuthorized = false;
	$adminAuthorized = false;
	if (AuthUserName != '') {
		$mpAuthorized = (AuthUserClan == 'Military Police');

		$query = mysql_query(
			"SELECT *
			 FROM `mp_headquarters_admin`
			 WHERE `login`='".AuthUserName."'
			", $db
		);

		$adminAuthorized = ((mysql_num_rows($query) > 0) || AuthUserGroup == 100);

		$mpAuthorized = $mpAuthorized || $adminAuthorized;
	}
?>