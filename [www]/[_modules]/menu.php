<?php
echo "<script language=\"javascript\" type=\"text/javascript\">\n";
echo "var menu=\"\";\n";

//if(AuthUserClan=='police' || AuthUserClan=='Tribunal' || AuthUserClan=='Military Police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy') {
if(AuthUserClan=='police' || AuthUserClan=='Tribunal' || AuthUserClan=='Police Academy') {
	$sSQL = 'SELECT `dept`, `chief` FROM `sd_cops` WHERE `name` = \''.AuthUserName.'\' ORDER BY `dept` LIMIT 1;';
	$result = mysql_query($sSQL);
	$row = mysql_fetch_assoc($result);
//15 ����� ���������� ���������
//12 ����� ��������
//27 ����� ������
	$menu_dept_id = $row['dept'];
// ����������
	$chief = $row['chief'];
}
if(AuthUserClan=='police' || AuthUserClan=='Tribunal' || AuthUserClan=='admins' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy' || AuthUserGroup==100) {
	if (AuthUserGroup==100) {
		echo "menu += mu(1,'?act=shop2','������� >>>',0,0,1);\n";
	}else{
		echo "menu += mu(1,'?act=shop2','������� >>>',0,0,0);\n";
	}
//	echo "menu += mu(2,'?act=shop2','��������� - �������',0,0,0);\n";
//	echo "menu += mu(2,'?act=shop2_orders','��������� - ������',0,0,0);\n";

//	$r = mysql_query("SELECT factory, laboratory, warehouse FROM build_users WHERE user_id='".AuthUserId."'");
//	list($factory, $laboratory, $warehouse) = mysql_fetch_array($r);
//	if ($factory || AuthUserGroup==100) {
	//	echo "menu += mu(2,'?act=shop2_factories','��������� - ������',0,0,0);
//	}
//	if (substr_count(AuthUserRestrAccess, "traders_shop") > 0 || AuthUserGroup==100) {
	//	echo "menu += mu(2,'?act=shop_traders','�������� - �������',0,0,0);\n";
	//	echo "menu += mu(2,'?act=shop_traders_orders','�������� - ������',0,0,0);
//	}
//	echo "menu += mu(2,'?act=warehouses2','����� - ��������',0,0,0);
	if ((abs(AccessLevel) & AccessPoliceStats) || $chief=='1') {
		echo "menu += mu(2,'?act=cops_stats&action=police111','������ �����������',0,0,0);\n";
	}
	if(AuthUserGroup==100){
		echo "menu += mu(2,'?act=mp_inform','���������� �� ����� ��',0,0,0);\n";
	}
	if (AuthUserGroup==100) {
		echo "menu += mu(2,'?act=tzrating&action=users_stats','���������� �� ��',0,0,0);\n";
	}
	if ((abs(AccessLevel) & AccessPoliceStats) || $chief=='1') {
		echo "menu += mu(2,'?act=cops_stats','���������� Police-Online',0,0,0);\n";
	}
	if ((abs(AccessLevel) & AccessSilentsLog) || $chief=='1') {
		echo "menu += mu(2,'?act=silents','����������� ����������',0,0,0);\n";
	}
	if (AuthUserGroup==100 || AuthUserClan=='police') {
		echo "menu += mu(2,'?act=personal_silents','<b><font color=white>�� ����� ����?</font></b>',0,0,0);\n";
	}

	if (AuthUserGroup==100 || $chief=='1') {
		echo "menu += mu(2,'?act=pa_stats','���������� ��-Online',0,0,0);\n";
	}
	if (AuthUserGroup==100) {
		echo "menu += mu(2,'?act=mp_stats','���������� ��-Online',0,0,0);\n";
	}
	echo "menu += mu(2,'?act=om_stats','���������� �� ������',0,0,0);\n";
//	if(AuthUserGroup==100) {
	//	echo "menu += mu(2,'?act=build_users','���. �������������',0,0,0);\n";
	//	echo "menu += mu(2,'?act=grossbuch','��������',0,0,0);\n";
	//	echo "menu += mu(2,'?act=plants','������',0,0,0);\n";
//	}
//	if ($laboratory || AuthUserGroup==100) {
	//	echo "menu += mu(2,'?act=manufacture','������������',0,0,0);
//	}
	echo "menu += mu(2,'?act=tzlog','���������� �����',0,0,0);\n";
	echo "menu += mu(2,'?act=aprisone','<b><font color=white>���������� ����������</font></b>',0,0,0);\n";
	echo "menu += mu(2,'?act=forum_threads','�����',0,0,0);\n";
	echo "menu += mu(2,'tests.php','������������',0,0,0);\n";
	if(AuthUserClan=='police' || AuthUserClan=='Tribunal' || AuthUserGroup==100) {
		echo "menu += mu(2,'?act=post','���� �� �� ������',0,0,0);\n";
//		echo "menu += mu(2,'?act=post2','���� � ������',0,0,0);\n";
		echo "menu += mu(2,'?act=post_vault','���� � ��������',0,0,0);\n";
		echo "menu += mu(2,'?act=post_forum','���� �� ������',0,0,0);\n";
		echo "menu += mu(2,'?act=post_nma','���� �� �������� ������',0,0,0);\n";
	}
//	if ($menu_dept_id == 15 || AuthUserGroup==100){
		echo "menu += mu(2,'?act=post_prison','���� �� ������� (���)',0,0,0);\n";
//	}
	if ((Abs(AccessLevel)&(AccessOR|AccessAdminOR))){
		echo "menu += mu(2,'interface/','<b><font color=white>����������� ���</font></b>',0,0,0);\n";
	}
	if ((abs(AccessLevel) & AccessOP) || AuthUserGroup==100 || AuthUserName=='Exposure passion' || in_array($menu_dept_id,array(12,13,14,15,57,71))) {
		echo "menu += mu(2,'?act=longlogs','<b><font color=white>���������</font></b>',0,0,0);\n";
	}
	if(AuthUserClan=='Police Academy' || AuthUserGroup==100) {
		echo "menu += mu(2,'?act=post_pa','����� ��',0,0,0);\n";
	}
	if(AuthUserClan=='Police Academy' || AuthUserClan=='police' || AuthUserClan=='Tribunal' || AuthUserGroup==100) {
		echo "menu += mu(2,'?act=data&type=pa','��������� ��',0,0,0);\n";
	}
	if(AuthUserClan=='police' || AuthUserClan=='Tribunal' || AuthUserGroup==100 || AuthUserClan=='Police Academy') {
		echo "menu += mu(2,'?act=moders_db','���������� ������',0,0,0);\n";
	}
	if(AuthUserClan=='police' || AuthUserClan=='Tribunal' || AuthUserGroup==100) {
		echo "menu += mu(2,'?act=data&type=copsmanual','����������� ������������',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=law_checkers','�������� - ����������',0,0,0);\n";
		echo "menu += mu(2,'?act=law_controllers','�������� - ���������',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_secret','������ �������� � Top Secret (�����)',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_ceo','������ �������� � ������ ���. ���.',0,0,0);\n";
		echo "menu += mu(2,'?act=user_ok','������ �������� � ������ ��',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_it','������ �������� � ������ ��',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_obep','������ �������� � ������ ����',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_commerce','������ �������� � ������ ���. ��.',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_military','������ �������� � ������ ��',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_investigations','������ �������� � ������ ��',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_inet','������ �������� � �-�� �� ����-���.',0,0,0);\n";

	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=exchange_rates','����� ������ (�������)',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_prison','������ � ���������� �������',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_commentcheck','�������� - ������',0,0,0);\n";
	}
	if (substr_count(AuthUserRestrAccess, 'prison') > 0 || AuthUserClan == 'police' || AuthUserGroup==100) {
		echo "menu += mu(2,'?act=prison_stats','�������: ���������� � ���������� �����������',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=bl_clans_users','�������� �� - �������',0,0,0);\n";
	}
	if (substr_count(AuthUserRestrAccess, 'bl_clans') > 0 || AuthUserGroup==100) {
		echo "menu += mu(2,'?act=bl_clans','�������� ��',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=total','���������� �������������',0,0,0);\n";
	}
	if (AuthUserGroup==100) {
		echo "menu += mu(2,'?act=comm_ed','������� �������������',0,0,0);\n";
	}
}
if (AuthUserClan=='police' || AuthUserClan=='Tribunal' ||  AuthUserGroup==100) {
	if ( AuthUserGroup==100) {
		$sSQL = 'SELECT COUNT(*) FROM `tzpolice_fees` WHERE `time`<'.(time()-604800).' AND `payed`<`summa` AND `prison`=\'0\';';
		$result = mysql_query($sSQL);
		$row = mysql_fetch_row($result);
		$nrows3 = $row[0];
		echo "menu += mu(2,'?act=fees','������ (".$nrows3.")',0,0,0);\n";
	}else{
		echo "menu += mu(2,'?act=fees','������',0,0,0);\n";
	}
	if ($menu_dept_id == '27' || AuthUserGroup==100) {
		echo "menu += mu(2,'?act=xfiles','������ ����',0,0,0);\n";
	}
}
//=========================================================
if (AuthUserGroup==100 || $menu_dept_id=='12' || $menu_dept_id=='15') {
	$sSQL = 'SELECT COUNT(id) FROM `tzpolice_okp4oin` WHERE `status`=1';
	$result = mysql_query($sSQL);
	$row = mysql_fetch_array($result);
	$nrows = $row[0];

	$sSQL = 'SELECT COUNT(id) FROM `tzpolice_okp4oin` WHERE `status`=2';
	$result = mysql_query($sSQL);
	$row = mysql_fetch_array($result);
	$nrows2 = $row[0];
	if (AuthUserGroup==100 || $menu_dept_id=='12'){
		echo "menu += mu(1,'?act=prokachki','���',9,1,0);\n";
		echo "menu += mu(2,'?act=prokachki','����� ��������',0,0,0);\n";
		echo "menu += mu(2,'?act=prokachki_queue','���. ����� ��������',0,0,0);\n";
		echo "menu += mu(2,'?act=tzrating','����� �������� �� �����',0,0,0);\n";
		echo "menu += mu(2,'?act=tzrating&kind=pvp','����� �������� ���',0,0,0);\n";
		//echo "menu += mu(2,'?act=4amaz','��� (".$nrows.") -> ��� (".$nrows2.")',0,0,0);\n";
		//echo "menu += mu(2,'?act=prison_stats','�������: ����������/����������',0,0,0);\n";
		echo "menu += mu(2,'?act=hist','������',0,0,0);\n";
		echo "menu += mu(2,'?act=forum_threads&f=prokachki','����� ���',0,0,0);\n";
	}elseif($menu_dept_id=='15'){
		echo "menu += mu(2,'?act=4amaz','��� (".$nrows.") -> ��� (".$nrows2.")',0,0,0);\n";
	}
}
if (AuthUserGroup==100) {
	echo "menu += mu(2,'?act=user_prokachki','������ � ������ ���',0,0,0);\n";
	echo "menu += mu(2,'?act=prokachki_access','������ � ������ ��������',0,0,0);\n";
	echo "menu += mu(2,'?act=prokachki_access_q','������ � ���. �����',0,0,0);\n";
}
//=========================================================
echo "menu += mu(1,'?act=news','������� � �������',1,1,0);\n";
echo "menu += mu(2,'?act=news','��������� �����',0,0,0);\n";
//echo "menu += mu(2,'/special/','������ \"��������\"',0,0,0);\n";
echo "menu += mu(2,'http://www.tzpolice.ru/rss.php','RSS �������',0,0,0);\n";
echo "menu += mu(2,'?act=news_search','����� � ��������',0,0,0);\n";
//if (in_array(AuthUserName, $freelancers)) {
//	echo "menu += mu(2,'?act=freelance_threads','����� ����������� ��',0,0,0);\n";
//}
if(AuthStatus==1 && AuthUserName!='') {
	echo "menu += mu(2,'?act=news_add2','�������� �������',0,0,0);\n";
}
if ((AuthStatus==1 && AuthUserGroup>1) || (abs(AccessLevel) & AccessNewsEditor)) {
	$d=mysql_fetch_array(mysql_query('SELECT count(*) as `cnt1` FROM `news` WHERE `is_visible`=\'0\''));
	echo "menu += mu(2,'?act=news_validate','������� �� �������� <B>(".$d['cnt1'].")</B>',0,0,0);\n";
}
if (substr_count(AuthUserRestrAccess, 'commentcheck') > 0){
	$d=mysql_fetch_array(mysql_query('SELECT count(*) as `cnt1` FROM `comments` WHERE `checked`=\'0\''));
	echo "menu += mu(2,'?act=new_news_comments','�������� �������� <b>(".$d['cnt1'].")</b>',0,0,0);\n";

	$d=mysql_fetch_array(mysql_query('SELECT count(*) as `cnt1` FROM `fotos_comments` WHERE `checked`=\'0\''));
	echo "menu += mu(2,'?act=new_fotos_comments','�������� ������� <b>(".$d['cnt1'].")</b>',0,0,0);\n";
}
//=========================================================
echo "menu += mu(1,'?act=pers_request','������� �������',2,1,0);\n";
echo "menu += mu(2,'?act=pers_request','������� ����������',0,0,0);\n";
echo "menu += mu(2,'/parser/','���������� �����',0,0,0);\n";
echo "menu += mu(2,'or/items.php','������ �����������',0,0,0);\n";
echo "menu += mu(2,'?act=compens','������� �����������',0,0,0);\n";
echo "menu += mu(2,'?act=siterating','�������� ����-������',0,0,0);\n";

if (in_array(AuthUserName, $compens_cops)|| AuthUserGroup==100) {
	echo "menu += mu(2,'?act=compens_add','���������� �������������',0,0,0);\n";
}

if (in_array(AuthUserName, $pers_adm) || AuthUserGroup==100) {
	$d=mysql_fetch_array(mysql_query('SELECT count(*) as `cnt1` FROM `or_data` WHERE `status` = \'2\' AND `silver` = \'0\''));
	echo "menu += mu(2,'?act=pers_adm','������� ������ (".$d['cnt1'].")',0,0,0);\n";
}
if (in_array(AuthUserName, $pers_adm_s) || AuthUserGroup==100) {
	$d=mysql_fetch_array(mysql_query('SELECT count(*) as `cnt1` FROM `or_data` WHERE `status` = \'2\' AND `silver` = \'1\''));
	echo "menu += mu(2,'?act=pers_adm_s','������� ������ (".$d['cnt1'].") [Silver]',0,0,0);\n";
}
if (in_array(AuthUserName, $pers_cops) || AuthUserGroup==100) {
	$d=mysql_fetch_array(mysql_query('SELECT count(*) as `cnt1` FROM `or_data` WHERE `status` = \'1\''));
	echo "menu += mu(2,'?act=pers_cops','������� ������ (".$d['cnt1'].")',0,0,0);\n";
}

echo "menu += mu(2,'?act=prisoned','���������� �����������',0,0,0);\n";

// ������ �������� 13.06.2014 �������� ���. ������� [Supervisor]
//echo "menu += mu(2,'?act=prison_rating','<b><font color=white>������� ��������</font></b>',0,0,0);\n";


//echo "menu += mu(2,'?act=law_request','�������� �� �������',0,0,0);\n";
//echo "menu += mu(2,'?act=law_results','���������� ��������',0,0,0);\n";
echo "menu += mu(2,'?act=deals','��������� ������',0,0,0);\n";
//echo "menu += mu(2,'/credit_calc/','<b><font color=white>������ �������� On-line</font></b>',0,0,0);\n";

//=========================================================
echo "menu += mu(1,'?act=data&type=exp_tbl','�������������� ���� ��',3,1,0);\n";
echo "menu += mu(2,'?act=rsd','������ ��������',0,0,0);\n";
echo "menu += mu(2,'?act=resprice','<b>���� �� �������</b>',0,0,0);\n";
echo "menu += mu(2,'?act=data&type=exp_tbl','������� �����',0,0,0);\n";
echo "menu += mu(2,'?act=tzrating','����� �������� �� �����',0,0,0);\n";
echo "menu += mu(2,'?act=tzrating&kind=pvp','<b>����� �������� ���</b>',0,0,0);\n";
echo "menu += mu(2,'?act=data2&type=manuals&Id=933','������� �� ��',0,0,0);\n";
echo "menu += mu(2,'?act=data&type=avatars','������� ��',0,0,0);\n";
echo "menu += mu(2,'?act=data2&type=manuals','<b><font color=white>�������</font></b>',0,0,0);\n";
echo "menu += mu(2,'?act=data2&type=manuals2','<b><font color=white>������</font></b>',0,0,0);\n";
echo "menu += mu(2,'?act=data2&type=manuals_new','<b><font color=white>������� ��� 1-5 �������</font></b>',0,0,0);\n";
echo "menu += mu(2,'?act=data2&type=quests_new','<b><font color=white>������ ��� 1-5 �������</font></b>',0,0,0);\n";
//echo "menu += mu(2,'?act=data&type=manuals3','<b><font color=white>��������</font></b>',0,0,0);\n";
echo "menu += mu(2,'?act=offwars','<b><font color=white>�������� �����</font></b>',0,0,0);\n";
echo "menu += mu(2,'?act=color','<b><font color=orange>�����������</font></b>',0,0,0);\n";
echo "menu += mu(2,'?act=lv','<b><font color=red>����!!!</font></b>',0,0,0);\n";
echo "menu += mu(2,'?act=data&type=hotkeys','������� ������� � ��',0,0,0);\n";
echo "menu += mu(2,'?act=data&type=dealer','����������� ������',0,0,0);\n";
//echo "menu += mu(2,'?act=solemnizers','����������� ������',0,0,0);\n";
echo "menu += mu(2,'?act=data2&type=faq','����',0,0,0);\n";
//=========================================================
echo "menu += mu(1,'?act=cops_depts','� �������',4,1,0);\n";
#echo "menu += mu(2,'?act=black','<font color=white>�� �������</font>',0,0,0);\n";
#echo "menu += mu(2,'?act=mpblack','<font color=white>�� Military Police</font>',0,0,0);\n";
echo "menu += mu(2,'?act=cops_depts','������, ������',0,0,0);\n";
echo "menu += mu(2,'?act=data&type=ustav','�����',0,0,0);\n";
echo "menu += mu(2,'?act=police_join','����� � �������',0,0,0);\n";
//echo "menu += mu(2,'?act=mp_join','���� ��',0,0,0);\n";
echo "menu += mu(2,'?act=data&type=orders','����������� ���������',0,0,0);\n";
echo "menu += mu(2,'?act=public_posts','�������� �����',0,0,0);\n";
//echo "menu += mu(2,'?act=data&type=contacts','��������',0,0,0);\n";
//echo "menu += mu(2,'?act=data&type=services','������',0,0,0);\n";
//echo "menu += mu(2,'?act=black_list','������ ������',0,0,0);\n";
//=========================================================
//=========================================================
echo "menu += mu(1,'?act=mpblack','Military Police',15,1,0);\n";
echo "menu += mu(2,'?act=mpblack','<font color=white>�� MP</font>',0,0,0);\n";
echo "menu += mu(2,'?act=mp_join','����� � MP',0,0,0);\n";
echo "menu += mu(2,'?act=mp_headq','���� MP',0,0,0);\n";
//=========================================================
echo "menu += mu(1,'?act=fotos','�����������',6,1,0);\n";
echo "menu += mu(2,'?act=fotos','����������� �����',0,0,0);\n";
//echo "menu += mu(2,'?act=fotos_classic','����������� CLASSIC',0,0,0);\n";
if (AuthStatus==1 && AuthUserName!='') {
	echo "menu += mu(2,'?act=fotos_add','�������� ����������',0,0,0);\n";
}
if(abs(AccessLevel) & AccessFotosModer) {
	$d=mysql_fetch_array(mysql_query('SELECT count(id) as cnt1 FROM `fotos_new`'));
	echo "menu += mu(2,'?act=fotos_verify','���� �� �������� (".$d['cnt1'].")',0,0,0);\n";
}
//echo "menu += mu(1,'http://www.tzportal.ru/\" target=\"_blank','���� ���������',8,1,0);\n";
//echo "menu += mu(2,'http://www.tzportal.ru/posobia/\" target=\"_blank','�������',0,0,0);\n";
//echo "menu += mu(2,'http://www.tzportal.ru/articles/\" target=\"_blank','������',0,0,0);\n";
//echo "menu += mu(2,'http://www.tzportal.ru/interview/\" target=\"_blank','��������',0,0,0);\n";
//echo "menu += mu(2,'http://www.tzportal.ru/stories/\" target=\"_blank','��������',0,0,0);\n";
//echo "menu += mu(2,'http://www.tzportal.ru/humor/\" target=\"_blank','����',0,0,0);\n";
//echo "menu += mu(2,'http://www.tzportal.ru/stihi/\" target=\"_blank','�����',0,0,0);\n";
//=========================================================
echo "menu += mu(1,'?act=data&type=interview','�����',5,1,0);\n";
echo "menu += mu(2,'?act=data2&type=art','����������',0,0,0);\n";
//echo "menu += mu(2,'?act=data&type=interview','��������',0,0,0);\n";
echo "menu += mu(2,'?act=warez','����������� �����������',0,0,0);\n";
echo "menu += mu(2,'?act=userbars','<b><font color=white>��������</font></b>',0,0,0);\n";
echo "menu += mu(2,'?act=data&type=misc&DataId=429','��� ������� �������',0,0,0);\n";

if(abs(AccessLevel) & AccessPolice)	{
	echo "menu += mu(2,'?act=warez_pe','�� police',0,0,0);\n";
}
if (AuthUserClan=='police' || AuthUserClan=='Tribunal' || AuthUserClan=='Police Academy' || AuthUserGroup==100) {
	echo "menu += mu(2,'?act=forum_threads&f=client','������ police',0,0,0);\n";
}
//echo "menu += mu(2,'?act=total_pub','�����������',0,0,0);\n";
if (AuthStatus==1 && (AuthUserGroup>1 || (abs(AccessLevel) & AccessUsers))) {
	echo "menu += mu(1,'?act=user_info','�����������������',7,1,1);\n";
	if(AuthStatus==1 && (abs(AccessLevel) & AccessUsers)){
		echo "menu += mu(2,'?act=user_info','������������ �����',0,0,0);\n";
	}
	echo "menu += mu(2,'?act=blacklist','׸���� ������',0,0,0);\n";
	echo "menu += mu(2,'?act=site_admin','��-���',0,0,0);\n";
}
if (AuthStatus==1 && AuthUserGroup>1) {
	echo "menu += mu(2,'?act=articles_add','���������� ������',0,0,0);\n";
	if (AuthUserGroup>2) {
		echo "menu += mu(2,'?act=poll_manager','�����������',0,0,0);\n";
	}
}
//========================================================
if (AuthUserGroup==100) {
	echo "menu += mu(1,'?act=user_info','�������',10,1,0);\n";
	echo "menu += mu(2,'?act=user_info','������������ �����',0,0,0);\n";
// �������� �� �������
//	echo "menu += mu(2,'?act=law_checkers','�������� - ����������������',0,0,0);\n";
//	echo "menu += mu(2,'?act=law_controllers','�������� - ������',0,0,0);\n";
// ������
	echo "menu += mu(2,'?act=user_ceo','� ������ ���. ���.',0,0,0);\n";
	echo "menu += mu(2,'?act=user_ok','� ������ ��',0,0,0);\n";
	echo "menu += mu(2,'?act=user_it','� ������ ��',0,0,0);\n";
	echo "menu += mu(2,'?act=user_obep','� ������ ����',0,0,0);\n";
	echo "menu += mu(2,'?act=user_military','� ������ ��',0,0,0);\n";
	echo "menu += mu(2,'?act=user_investigations','� ������ ��',0,0,0);\n";
	echo "menu += mu(2,'?act=user_inet','� �-�� �� ����-���.',0,0,0);\n";
//
	//	echo "menu += mu(2,'?act=user_traders','������ � ���� ���������',0,0,0);\n";
	echo "menu += mu(2,'?act=user_prison','� ���������� �������',0,0,0);\n";
//
	echo "menu += mu(2,'?act=user_commentcheck','�������� - ������',0,0,0);\n";
//
	echo "menu += mu(2,'?act=bl_clans_users','�������� �� - �������',0,0,0);\n";
//
	echo "menu += mu(2,'?act=total','���������� �������������',0,0,0);\n";
//
//	echo "menu += mu(2,'?act=fees','������ [� �������]',0,0,0);\n";
	echo "menu += mu(2,'?act=4amaz','��� -> ��� [� �������]',0,0,0);\n";
	echo "menu += mu(2,'?act=tzrating','����� �������� [� �������]',0,0,0);\n";
// ���
	echo "menu += mu(2,'?act=user_prokachki','� ������ ���',0,0,0);\n";
	echo "menu += mu(2,'?act=prokachki_access','� ������ ��������',0,0,0);\n";
	echo "menu += mu(2,'?act=prokachki_access_q','� ���. �����',0,0,0);\n";
//
	echo "menu += mu(2,'?act=compens_add','���������� ������������� - [functions.php]',0,0,0);\n";
	echo "menu += mu(2,'?act=pers_adm','������� ������ - [functions.php]',0,0,0);\n";
}

//========================================================
echo "menu += '</div>';\n";
echo "document.write(menu);\n";
echo "</script>\n";
?>