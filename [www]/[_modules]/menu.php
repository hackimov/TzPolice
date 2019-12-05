<?php
echo "<script language=\"javascript\" type=\"text/javascript\">\n";
echo "var menu=\"\";\n";

//if(AuthUserClan=='police' || AuthUserClan=='Tribunal' || AuthUserClan=='Military Police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy') {
if(AuthUserClan=='police' || AuthUserClan=='Tribunal' || AuthUserClan=='Police Academy') {
	$sSQL = 'SELECT `dept`, `chief` FROM `sd_cops` WHERE `name` = \''.AuthUserName.'\' ORDER BY `dept` LIMIT 1;';
	$result = mysql_query($sSQL);
	$row = mysql_fetch_assoc($result);
//15 Отдел исполнения наказаний
//12 Отдел прокачек
//27 Отдел Кадров
	$menu_dept_id = $row['dept'];
// начальники
	$chief = $row['chief'];
}
if(AuthUserClan=='police' || AuthUserClan=='Tribunal' || AuthUserClan=='admins' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy' || AuthUserGroup==100) {
	if (AuthUserGroup==100) {
		echo "menu += mu(1,'?act=shop2','Полиция >>>',0,0,1);\n";
	}else{
		echo "menu += mu(1,'?act=shop2','Полиция >>>',0,0,0);\n";
	}
//	echo "menu += mu(2,'?act=shop2','ВоенторгЪ - магазин',0,0,0);\n";
//	echo "menu += mu(2,'?act=shop2_orders','ВоенторгЪ - заказы',0,0,0);\n";

//	$r = mysql_query("SELECT factory, laboratory, warehouse FROM build_users WHERE user_id='".AuthUserId."'");
//	list($factory, $laboratory, $warehouse) = mysql_fetch_array($r);
//	if ($factory || AuthUserGroup==100) {
	//	echo "menu += mu(2,'?act=shop2_factories','ВоенторгЪ - заводы',0,0,0);
//	}
//	if (substr_count(AuthUserRestrAccess, "traders_shop") > 0 || AuthUserGroup==100) {
	//	echo "menu += mu(2,'?act=shop_traders','Торговцы - магазин',0,0,0);\n";
	//	echo "menu += mu(2,'?act=shop_traders_orders','Торговцы - заказы',0,0,0);
//	}
//	echo "menu += mu(2,'?act=warehouses2','Склад - переводы',0,0,0);
	if ((abs(AccessLevel) & AccessPoliceStats) || $chief=='1') {
		echo "menu += mu(2,'?act=cops_stats&action=police111','Список полицейских',0,0,0);\n";
	}
	if(AuthUserGroup==100){
		echo "menu += mu(2,'?act=mp_inform','Статистика по ботам МП',0,0,0);\n";
	}
	if (AuthUserGroup==100) {
		echo "menu += mu(2,'?act=tzrating&action=users_stats','Статистика по ТЗ',0,0,0);\n";
	}
	if ((abs(AccessLevel) & AccessPoliceStats) || $chief=='1') {
		echo "menu += mu(2,'?act=cops_stats','Статистика Police-Online',0,0,0);\n";
	}
	if ((abs(AccessLevel) & AccessSilentsLog) || $chief=='1') {
		echo "menu += mu(2,'?act=silents','Полицейская активность',0,0,0);\n";
	}
	if (AuthUserGroup==100 || AuthUserClan=='police') {
		echo "menu += mu(2,'?act=personal_silents','<b><font color=white>Шо вчера было?</font></b>',0,0,0);\n";
	}

	if (AuthUserGroup==100 || $chief=='1') {
		echo "menu += mu(2,'?act=pa_stats','Статистика ПА-Online',0,0,0);\n";
	}
	if (AuthUserGroup==100) {
		echo "menu += mu(2,'?act=mp_stats','Статистика МП-Online',0,0,0);\n";
	}
	echo "menu += mu(2,'?act=om_stats','Статистика по постам',0,0,0);\n";
//	if(AuthUserGroup==100) {
	//	echo "menu += mu(2,'?act=build_users','Фин. администратор',0,0,0);\n";
	//	echo "menu += mu(2,'?act=grossbuch','Гроссбух',0,0,0);\n";
	//	echo "menu += mu(2,'?act=plants','Заводы',0,0,0);\n";
//	}
//	if ($laboratory || AuthUserGroup==100) {
	//	echo "menu += mu(2,'?act=manufacture','Производство',0,0,0);
//	}
	echo "menu += mu(2,'?act=tzlog','Анализатор логов',0,0,0);\n";
	echo "menu += mu(2,'?act=aprisone','<b><font color=white>Анализатор каторжника</font></b>',0,0,0);\n";
	echo "menu += mu(2,'?act=forum_threads','Форум',0,0,0);\n";
	echo "menu += mu(2,'tests.php','Тестирования',0,0,0);\n";
	if(AuthUserClan=='police' || AuthUserClan=='Tribunal' || AuthUserGroup==100) {
		echo "menu += mu(2,'?act=post','Пост на ЦП Москвы',0,0,0);\n";
//		echo "menu += mu(2,'?act=post2','Пост в Оазисе',0,0,0);\n";
		echo "menu += mu(2,'?act=post_vault','Пост в Форпосте',0,0,0);\n";
		echo "menu += mu(2,'?act=post_forum','Пост на форуме',0,0,0);\n";
		echo "menu += mu(2,'?act=post_nma','Пост на аукционе Москвы',0,0,0);\n";
	}
//	if ($menu_dept_id == 15 || AuthUserGroup==100){
		echo "menu += mu(2,'?act=post_prison','Пост на каторге (ОИН)',0,0,0);\n";
//	}
	if ((Abs(AccessLevel)&(AccessOR|AccessAdminOR))){
		echo "menu += mu(2,'interface/','<b><font color=white>Недремлющее око</font></b>',0,0,0);\n";
	}
	if ((abs(AccessLevel) & AccessOP) || AuthUserGroup==100 || AuthUserName=='Exposure passion' || in_array($menu_dept_id,array(12,13,14,15,57,71))) {
		echo "menu += mu(2,'?act=longlogs','<b><font color=white>Логовница</font></b>',0,0,0);\n";
	}
	if(AuthUserClan=='Police Academy' || AuthUserGroup==100) {
		echo "menu += mu(2,'?act=post_pa','Посты ПА',0,0,0);\n";
	}
	if(AuthUserClan=='Police Academy' || AuthUserClan=='police' || AuthUserClan=='Tribunal' || AuthUserGroup==100) {
		echo "menu += mu(2,'?act=data&type=pa','Документы ПА',0,0,0);\n";
	}
	if(AuthUserClan=='police' || AuthUserClan=='Tribunal' || AuthUserGroup==100 || AuthUserClan=='Police Academy') {
		echo "menu += mu(2,'?act=moders_db','Справочник модера',0,0,0);\n";
	}
	if(AuthUserClan=='police' || AuthUserClan=='Tribunal' || AuthUserGroup==100) {
		echo "menu += mu(2,'?act=data&type=copsmanual','Руководство полицейского',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=law_checkers','Проверки - сотрудники',0,0,0);\n";
		echo "menu += mu(2,'?act=law_controllers','Проверки - избранные',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_secret','Выдача доступов в Top Secret (общий)',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_ceo','Выдача доступов к форуму нач. отд.',0,0,0);\n";
		echo "menu += mu(2,'?act=user_ok','Выдача доступов к форуму ОК',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_it','Выдача доступов к форуму ОИ',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_obep','Выдача доступов к форуму ОБЭП',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_commerce','Выдача доступов к форуму ком. от.',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_military','Выдача доступов к форуму МП',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_investigations','Выдача доступов к форуму ОР',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_inet','Выдача доступов к ф-му по инет-рес.',0,0,0);\n";

	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=exchange_rates','Курсы обмена (каторга)',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_prison','Доступ к статистике каторги',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=user_commentcheck','Комменты - доступ',0,0,0);\n";
	}
	if (substr_count(AuthUserRestrAccess, 'prison') > 0 || AuthUserClan == 'police' || AuthUserGroup==100) {
		echo "menu += mu(2,'?act=prison_stats','Каторга: статистика и добавление каторжников',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=bl_clans_users','Клановые ЧС - доступы',0,0,0);\n";
	}
	if (substr_count(AuthUserRestrAccess, 'bl_clans') > 0 || AuthUserGroup==100) {
		echo "menu += mu(2,'?act=bl_clans','Клановые ЧС',0,0,0);\n";
	}
	if(AuthUserGroup==100) {
		echo "menu += mu(2,'?act=total','Управление тотализатором',0,0,0);\n";
	}
	if (AuthUserGroup==100) {
		echo "menu += mu(2,'?act=comm_ed','Шаблоны коммуникатора',0,0,0);\n";
	}
}
if (AuthUserClan=='police' || AuthUserClan=='Tribunal' ||  AuthUserGroup==100) {
	if ( AuthUserGroup==100) {
		$sSQL = 'SELECT COUNT(*) FROM `tzpolice_fees` WHERE `time`<'.(time()-604800).' AND `payed`<`summa` AND `prison`=\'0\';';
		$result = mysql_query($sSQL);
		$row = mysql_fetch_row($result);
		$nrows3 = $row[0];
		echo "menu += mu(2,'?act=fees','Штрафы (".$nrows3.")',0,0,0);\n";
	}else{
		echo "menu += mu(2,'?act=fees','Штрафы',0,0,0);\n";
	}
	if ($menu_dept_id == '27' || AuthUserGroup==100) {
		echo "menu += mu(2,'?act=xfiles','Личные дела',0,0,0);\n";
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
		echo "menu += mu(1,'?act=prokachki','ОКП',9,1,0);\n";
		echo "menu += mu(2,'?act=prokachki','Поиск прокачек',0,0,0);\n";
		echo "menu += mu(2,'?act=prokachki_queue','Доб. логов прокачек',0,0,0);\n";
		echo "menu += mu(2,'?act=tzrating','Архив рейтинга по опыту',0,0,0);\n";
		echo "menu += mu(2,'?act=tzrating&kind=pvp','Архив рейтинга ПвП',0,0,0);\n";
		//echo "menu += mu(2,'?act=4amaz','ОИН (".$nrows.") -> ОКП (".$nrows2.")',0,0,0);\n";
		//echo "menu += mu(2,'?act=prison_stats','Каторга: статистика/добавление',0,0,0);\n";
		echo "menu += mu(2,'?act=hist','Корсар',0,0,0);\n";
		echo "menu += mu(2,'?act=forum_threads&f=prokachki','Форум ОКП',0,0,0);\n";
	}elseif($menu_dept_id=='15'){
		echo "menu += mu(2,'?act=4amaz','ОИН (".$nrows.") -> ОКП (".$nrows2.")',0,0,0);\n";
	}
}
if (AuthUserGroup==100) {
	echo "menu += mu(2,'?act=user_prokachki','Доступ к форуму ОКП',0,0,0);\n";
	echo "menu += mu(2,'?act=prokachki_access','Доступ к поиску прокачек',0,0,0);\n";
	echo "menu += mu(2,'?act=prokachki_access_q','Доступ к доб. логов',0,0,0);\n";
}
//=========================================================
echo "menu += mu(1,'?act=news','Новости и события',1,1,0);\n";
echo "menu += mu(2,'?act=news','Новостная лента',0,0,0);\n";
//echo "menu += mu(2,'/special/','Газета \"Спецкорр\"',0,0,0);\n";
echo "menu += mu(2,'http://www.tzpolice.ru/rss.php','RSS новости',0,0,0);\n";
echo "menu += mu(2,'?act=news_search','Поиск в новостях',0,0,0);\n";
//if (in_array(AuthUserName, $freelancers)) {
//	echo "menu += mu(2,'?act=freelance_threads','Форум фрилансеров ИТ',0,0,0);\n";
//}
if(AuthStatus==1 && AuthUserName!='') {
	echo "menu += mu(2,'?act=news_add2','Добавить новость',0,0,0);\n";
}
if ((AuthStatus==1 && AuthUserGroup>1) || (abs(AccessLevel) & AccessNewsEditor)) {
	$d=mysql_fetch_array(mysql_query('SELECT count(*) as `cnt1` FROM `news` WHERE `is_visible`=\'0\''));
	echo "menu += mu(2,'?act=news_validate','Новости на проверку <B>(".$d['cnt1'].")</B>',0,0,0);\n";
}
if (substr_count(AuthUserRestrAccess, 'commentcheck') > 0){
	$d=mysql_fetch_array(mysql_query('SELECT count(*) as `cnt1` FROM `comments` WHERE `checked`=\'0\''));
	echo "menu += mu(2,'?act=new_news_comments','Комменты новостей <b>(".$d['cnt1'].")</b>',0,0,0);\n";

	$d=mysql_fetch_array(mysql_query('SELECT count(*) as `cnt1` FROM `fotos_comments` WHERE `checked`=\'0\''));
	echo "menu += mu(2,'?act=new_fotos_comments','Комменты галереи <b>(".$d['cnt1'].")</b>',0,0,0);\n";
}
//=========================================================
echo "menu += mu(1,'?act=pers_request','Сервисы полиции',2,1,0);\n";
echo "menu += mu(2,'?act=pers_request','Возврат персонажей',0,0,0);\n";
echo "menu += mu(2,'/parser/','Анализатор логов',0,0,0);\n";
echo "menu += mu(2,'or/items.php','Расчет компенсаций',0,0,0);\n";
echo "menu += mu(2,'?act=compens','Выплаты компенсаций',0,0,0);\n";
echo "menu += mu(2,'?act=siterating','Проверка клан-сайтов',0,0,0);\n";

if (in_array(AuthUserName, $compens_cops)|| AuthUserGroup==100) {
	echo "menu += mu(2,'?act=compens_add','Управление компенсациями',0,0,0);\n";
}

if (in_array(AuthUserName, $pers_adm) || AuthUserGroup==100) {
	$d=mysql_fetch_array(mysql_query('SELECT count(*) as `cnt1` FROM `or_data` WHERE `status` = \'2\' AND `silver` = \'0\''));
	echo "menu += mu(2,'?act=pers_adm','Возврат персов (".$d['cnt1'].")',0,0,0);\n";
}
if (in_array(AuthUserName, $pers_adm_s) || AuthUserGroup==100) {
	$d=mysql_fetch_array(mysql_query('SELECT count(*) as `cnt1` FROM `or_data` WHERE `status` = \'2\' AND `silver` = \'1\''));
	echo "menu += mu(2,'?act=pers_adm_s','Возврат персов (".$d['cnt1'].") [Silver]',0,0,0);\n";
}
if (in_array(AuthUserName, $pers_cops) || AuthUserGroup==100) {
	$d=mysql_fetch_array(mysql_query('SELECT count(*) as `cnt1` FROM `or_data` WHERE `status` = \'1\''));
	echo "menu += mu(2,'?act=pers_cops','Возврат персов (".$d['cnt1'].")',0,0,0);\n";
}

echo "menu += mu(2,'?act=prisoned','Статистика каторжанина',0,0,0);\n";

// модуль отключен 13.06.2014 решением нач. полиции [Supervisor]
//echo "menu += mu(2,'?act=prison_rating','<b><font color=white>Рейтинг каторжан</font></b>',0,0,0);\n";


//echo "menu += mu(2,'?act=law_request','Проверки на чистоту',0,0,0);\n";
//echo "menu += mu(2,'?act=law_results','Результаты проверок',0,0,0);\n";
echo "menu += mu(2,'?act=deals','Хранилище сделок',0,0,0);\n";
//echo "menu += mu(2,'/credit_calc/','<b><font color=white>Расчет кредитов On-line</font></b>',0,0,0);\n";

//=========================================================
echo "menu += mu(1,'?act=data&type=exp_tbl','Информационная база ТО',3,1,0);\n";
echo "menu += mu(2,'?act=rsd','Скупка ресурсов',0,0,0);\n";
echo "menu += mu(2,'?act=resprice','<b>Цены на ресурсы</b>',0,0,0);\n";
echo "menu += mu(2,'?act=data&type=exp_tbl','Таблица опыта',0,0,0);\n";
echo "menu += mu(2,'?act=tzrating','Архив рейтинга по опыту',0,0,0);\n";
echo "menu += mu(2,'?act=tzrating&kind=pvp','<b>Архив рейтинга ПвП</b>',0,0,0);\n";
echo "menu += mu(2,'?act=data2&type=manuals&Id=933','Подарки на ДР',0,0,0);\n";
echo "menu += mu(2,'?act=data&type=avatars','Аватары ТО',0,0,0);\n";
echo "menu += mu(2,'?act=data2&type=manuals','<b><font color=white>Пособия</font></b>',0,0,0);\n";
echo "menu += mu(2,'?act=data2&type=manuals2','<b><font color=white>Квесты</font></b>',0,0,0);\n";
echo "menu += mu(2,'?act=data2&type=manuals_new','<b><font color=white>Пособия для 1-5 уровней</font></b>',0,0,0);\n";
echo "menu += mu(2,'?act=data2&type=quests_new','<b><font color=white>Квесты для 1-5 уровней</font></b>',0,0,0);\n";
//echo "menu += mu(2,'?act=data&type=manuals3','<b><font color=white>Новичкам</font></b>',0,0,0);\n";
echo "menu += mu(2,'?act=offwars','<b><font color=white>Клановые войны</font></b>',0,0,0);\n";
echo "menu += mu(2,'?act=color','<b><font color=orange>Покрасочная</font></b>',0,0,0);\n";
echo "menu += mu(2,'?act=lv','<b><font color=red>КИНО!!!</font></b>',0,0,0);\n";
echo "menu += mu(2,'?act=data&type=hotkeys','Горячие клавиши в ТО',0,0,0);\n";
echo "menu += mu(2,'?act=data&type=dealer','Официальные дилеры',0,0,0);\n";
//echo "menu += mu(2,'?act=solemnizers','Регистрация браков',0,0,0);\n";
echo "menu += mu(2,'?act=data2&type=faq','ЧаВо',0,0,0);\n";
//=========================================================
echo "menu += mu(1,'?act=cops_depts','О полиции',4,1,0);\n";
#echo "menu += mu(2,'?act=black','<font color=white>ЧС полиции</font>',0,0,0);\n";
#echo "menu += mu(2,'?act=mpblack','<font color=white>ЧС Military Police</font>',0,0,0);\n";
echo "menu += mu(2,'?act=cops_depts','Состав, отделы',0,0,0);\n";
echo "menu += mu(2,'?act=data&type=ustav','Устав',0,0,0);\n";
echo "menu += mu(2,'?act=police_join','Прием в полицию',0,0,0);\n";
//echo "menu += mu(2,'?act=mp_join','Штаб МП',0,0,0);\n";
echo "menu += mu(2,'?act=data&type=orders','Нормативные документы',0,0,0);\n";
echo "menu += mu(2,'?act=public_posts','Дежурная часть',0,0,0);\n";
//echo "menu += mu(2,'?act=data&type=contacts','Контакты',0,0,0);\n";
//echo "menu += mu(2,'?act=data&type=services','Услуги',0,0,0);\n";
//echo "menu += mu(2,'?act=black_list','Черный список',0,0,0);\n";
//=========================================================
//=========================================================
echo "menu += mu(1,'?act=mpblack','Military Police',15,1,0);\n";
echo "menu += mu(2,'?act=mpblack','<font color=white>ЧС MP</font>',0,0,0);\n";
echo "menu += mu(2,'?act=mp_join','Прием в MP',0,0,0);\n";
echo "menu += mu(2,'?act=mp_headq','Штаб MP',0,0,0);\n";
//=========================================================
echo "menu += mu(1,'?act=fotos','Фотогалерея',6,1,0);\n";
echo "menu += mu(2,'?act=fotos','Фотогалерея сайта',0,0,0);\n";
//echo "menu += mu(2,'?act=fotos_classic','Фотогалерея CLASSIC',0,0,0);\n";
if (AuthStatus==1 && AuthUserName!='') {
	echo "menu += mu(2,'?act=fotos_add','Добавить фотографию',0,0,0);\n";
}
if(abs(AccessLevel) & AccessFotosModer) {
	$d=mysql_fetch_array(mysql_query('SELECT count(id) as cnt1 FROM `fotos_new`'));
	echo "menu += mu(2,'?act=fotos_verify','Фото на проверку (".$d['cnt1'].")',0,0,0);\n";
}
//echo "menu += mu(1,'http://www.tzportal.ru/\" target=\"_blank','Банк креативов',8,1,0);\n";
//echo "menu += mu(2,'http://www.tzportal.ru/posobia/\" target=\"_blank','Пособия',0,0,0);\n";
//echo "menu += mu(2,'http://www.tzportal.ru/articles/\" target=\"_blank','Статьи',0,0,0);\n";
//echo "menu += mu(2,'http://www.tzportal.ru/interview/\" target=\"_blank','Интервью',0,0,0);\n";
//echo "menu += mu(2,'http://www.tzportal.ru/stories/\" target=\"_blank','Рассказы',0,0,0);\n";
//echo "menu += mu(2,'http://www.tzportal.ru/humor/\" target=\"_blank','Юмор',0,0,0);\n";
//echo "menu += mu(2,'http://www.tzportal.ru/stihi/\" target=\"_blank','Стихи',0,0,0);\n";
//=========================================================
echo "menu += mu(1,'?act=data&type=interview','Отдых',5,1,0);\n";
echo "menu += mu(2,'?act=data2&type=art','Творчество',0,0,0);\n";
//echo "menu += mu(2,'?act=data&type=interview','Интервью',0,0,0);\n";
echo "menu += mu(2,'?act=warez','Программное обеспечение',0,0,0);\n";
echo "menu += mu(2,'?act=userbars','<b><font color=white>Юзербары</font></b>',0,0,0);\n";
echo "menu += mu(2,'?act=data&type=misc&DataId=429','Как сделать юзербар',0,0,0);\n";

if(abs(AccessLevel) & AccessPolice)	{
	echo "menu += mu(2,'?act=warez_pe','ПО police',0,0,0);\n";
}
if (AuthUserClan=='police' || AuthUserClan=='Tribunal' || AuthUserClan=='Police Academy' || AuthUserGroup==100) {
	echo "menu += mu(2,'?act=forum_threads&f=client','Клиент police',0,0,0);\n";
}
//echo "menu += mu(2,'?act=total_pub','Тотализатор',0,0,0);\n";
if (AuthStatus==1 && (AuthUserGroup>1 || (abs(AccessLevel) & AccessUsers))) {
	echo "menu += mu(1,'?act=user_info','Администрирование',7,1,1);\n";
	if(AuthStatus==1 && (abs(AccessLevel) & AccessUsers)){
		echo "menu += mu(2,'?act=user_info','Пользователи сайта',0,0,0);\n";
	}
	echo "menu += mu(2,'?act=blacklist','Чёрный список',0,0,0);\n";
	echo "menu += mu(2,'?act=site_admin','Йи-хха',0,0,0);\n";
}
if (AuthStatus==1 && AuthUserGroup>1) {
	echo "menu += mu(2,'?act=articles_add','Добавление статей',0,0,0);\n";
	if (AuthUserGroup>2) {
		echo "menu += mu(2,'?act=poll_manager','Голосования',0,0,0);\n";
	}
}
//========================================================
if (AuthUserGroup==100) {
	echo "menu += mu(1,'?act=user_info','Доступы',10,1,0);\n";
	echo "menu += mu(2,'?act=user_info','Пользователи сайта',0,0,0);\n";
// Проверки на чистоту
//	echo "menu += mu(2,'?act=law_checkers','Проверки - пользовательский',0,0,0);\n";
//	echo "menu += mu(2,'?act=law_controllers','Проверки - полный',0,0,0);\n";
// форумы
	echo "menu += mu(2,'?act=user_ceo','К форуму нач. отд.',0,0,0);\n";
	echo "menu += mu(2,'?act=user_ok','К форуму ОК',0,0,0);\n";
	echo "menu += mu(2,'?act=user_it','К форуму ОИ',0,0,0);\n";
	echo "menu += mu(2,'?act=user_obep','К форуму ОБЭП',0,0,0);\n";
	echo "menu += mu(2,'?act=user_military','К форуму МП',0,0,0);\n";
	echo "menu += mu(2,'?act=user_investigations','К форуму ОР',0,0,0);\n";
	echo "menu += mu(2,'?act=user_inet','К ф-му по инет-рес.',0,0,0);\n";
//
	//	echo "menu += mu(2,'?act=user_traders','Доступ к магу торговцев',0,0,0);\n";
	echo "menu += mu(2,'?act=user_prison','К статистике каторги',0,0,0);\n";
//
	echo "menu += mu(2,'?act=user_commentcheck','Комменты - доступ',0,0,0);\n";
//
	echo "menu += mu(2,'?act=bl_clans_users','Клановые ЧС - доступы',0,0,0);\n";
//
	echo "menu += mu(2,'?act=total','Управление тотализатором',0,0,0);\n";
//
//	echo "menu += mu(2,'?act=fees','Штрафы [в скрипте]',0,0,0);\n";
	echo "menu += mu(2,'?act=4amaz','ОИН -> ОКП [в скрипте]',0,0,0);\n";
	echo "menu += mu(2,'?act=tzrating','Архив рейтинга [в скрипте]',0,0,0);\n";
// ОКП
	echo "menu += mu(2,'?act=user_prokachki','К форуму ОКП',0,0,0);\n";
	echo "menu += mu(2,'?act=prokachki_access','К поиску прокачек',0,0,0);\n";
	echo "menu += mu(2,'?act=prokachki_access_q','К доб. логов',0,0,0);\n";
//
	echo "menu += mu(2,'?act=compens_add','Управление компенсациями - [functions.php]',0,0,0);\n";
	echo "menu += mu(2,'?act=pers_adm','Возврат персов - [functions.php]',0,0,0);\n";
}

//========================================================
echo "menu += '</div>';\n";
echo "document.write(menu);\n";
echo "</script>\n";
?>