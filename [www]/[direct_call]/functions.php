<?php
#error_reporting(E_ALL);
require('/home/sites/police/www/_modules/mysql.php');

#бля это надо в базу засунуть. или в отдельный файл.


############## vars #################
$CommentsPerPage=15;
$NewsPP=15;
$ThreadsPP=15;
$RepliesPP=20;
$mess['CantAddComment']='<h2>Чтобы добавить комментарий, Вам необходимо авторизоваться на сайте</h2>';
$mess['CantAddReply']='<h2>Чтобы добавить ответ, Вам необходимо авторизоваться на сайте</h2>';
$mess['NoComments']='<h2>комментариев нет</h2>';
$mess['NewsNotFound']='<h2>Указанный материал не найден</h2>';
$mess['AccessDenied']='<h2>У вас нет прав доступа к этому разделу</h2>';
$mess['UserNotFound']='<h2>Пользователь не найден</h2>';
$mess['CantAddNews']='<h2>Чтобы добавить новость, вы должны авторизоваться на сайте</h2>';
$mess['WantRegister']='<div align=center>Если Вы еще не зарегистрированы, <a href="?act=register">зарегистрируйтесь</a></div>';

$SecName['manuals'] = 'Пособия';
$SecName['manuals2'] = 'Квесты';
$SecName['manuals3'] = 'Новичкам';
$SecName['mine_maps'] = 'Карты шахт';
$SecName['prize'] = 'Подарки на ДР';
$SecName['art']='Творчество';
$SecName['law']='Нормативные документы';
$SecName['interview']='Интервью';
$SecName['staff']='Состав, отделы';
$SecName['hotkeys']='Горячие клавиши в ТО';
$SecName['exp_tbl']='Таблица опыта';
$SecName['pills']='Рецепты';
$SecName['contacts']='Контакты';
$SecName['services']='Услуги';
$SecName['dealer']='Официальные дилеры';
$SecName['faq']='Часто задаваемые вопросы';
$SecName['keks']='Кекс';
$SecName['misc']='Разное';
$SecName['join']='Прием в полицию';
$SecName['copsmanual']='Руководство полицейского';
$SecName['other']='TEMP';
$SecName['orders']='Нормативные документы';
$SecName['pa']='Полицейская академия';
$SecName['gallery']='Инструкция для фотогалереи';
$SecName['legenda']='Легенда клана';
$SecName['ustav']='Устав клана';
$SecName['ok']='ОК Help';
//$SecName['misc']='Полицейская академия';
/*
$forum['general']['name']='Общий';
$forum['general']['desc']='Обсуждение самых разнообразных вопросов =)';
$forum['moaning']['name']='Жалобы на полицейских';
$forum['moaning']['desc']='Ваш любимый раздел. Рассмотрение жалоб на неправомерные действия полиции.';
$forum['suggestions']['name']='Предложения по сайту';
$forum['suggestions']['desc']='Предложения по улучшению сайта, сообщения об ошибках';
$forum['mate']['name']='Сходки';
$forum['mate']['desc']='Объявления о сходках, а так же фото-отчеты о них. Есть возможность подгружать и автоматически ресайзить фото';
$forum['fastflood']['name']='Фаст-флуд';
$forum['fastflood']['desc']='специально для FastShadow. Флуди, добр.человек =)';
*/
$forum['main']['name']='Общий';
$forum['main']['desc']='Обсуждение любых тем';
$forum['main']['restr']=0;
$forum['academy']['name']='Полицейская Академия';
$forum['academy']['desc']='Нормативные документы и инструкции Полицейской Академии';
$forum['academy']['restr']=0;
$forum['orders']['name']='Приказы';
$forum['orders']['desc']='Приказы по полиции и полицейской академии';
$forum['orders']['restr']=0;
$forum['reports']['name']='Рапорта';
$forum['reports']['desc']='Рапорта кадетов полиции';
$forum['reports']['restr']=0;
$forum['client']['name']='Клиент';
$forum['client']['desc']='Новости и инструкции к полицейскому клиенту';
$forum['client']['restr']=0;
//$forum['work']['name']='Рабочий';
//$forum['work']['desc']='Рабочий фин. отдела';
//$forum['work']['restr']=0;
//$forum['elite']['name']='Форум для экономистов';
//$forum['elite']['desc']='Допуск есть у ограниченного круга лиц. <b>Top Secret!</b>';
//$forum['elite']['nm']='elite';
//$forum['elite']['restr']=50;
$forum['secret']['name']='Закрытый форум';
$forum['secret']['desc']='Допуск есть у ограниченного круга лиц. <b>Top Secret!</b>';
$forum['secret']['nm']='secret';
$forum['secret']['restr']=50;
$forum['ceo']['name']='Форум начальников отделов';
$forum['ceo']['desc']='Допуск есть у начальников отделов и начальника полиции ТО. <b>Top Secret!</b>';
$forum['ceo']['nm']='ceo';
$forum['ceo']['restr']=50;
$forum['ok']['name']='Отдел кадров';
$forum['ok']['desc']='Закрытый форум ОК';
$forum['ok']['nm']='ok';
$forum['ok']['restr']=50;
$forum['inform']['name']='Отдел информатизации';
$forum['inform']['desc']='Закрытый форум ОИ';
$forum['inform']['nm']='inform';
$forum['inform']['restr']=50;
$forum['inet']['name']='Форум рабочих групп по интернет ресурсам';
$forum['inet']['desc']='Строго конфиденциально';
$forum['inet']['nm']='inet';
$forum['inet']['restr']=50;
$forum['investigations']['name']='Форум отдела расследований';
$forum['investigations']['desc']='Строго конфиденциально';
$forum['investigations']['nm']='investigations';
$forum['investigations']['restr']=50;
$forum['military']['name']='Закрытый форум Military Police';
$forum['military']['desc']='Строго конфиденциально';
$forum['military']['nm']='military';
$forum['military']['restr']=50;
$forum['prokachki']['name']='Закрытый форум отдела контроля прокачек';
$forum['prokachki']['desc']='Строго конфиденциально';
$forum['prokachki']['nm']='prokachki';
$forum['prokachki']['restr']=50;
$forum['obep']['name']='Закрытый форум ОБЭП';
$forum['obep']['desc']='Строго конфиденциально';
$forum['obep']['nm']='obep';
$forum['obep']['restr']=50;
$forum['commerce']['name']='Закрытый форум коммерческого отдела';
$forum['commerce']['desc']='Строго конфиденциально';
$forum['commerce']['nm']='commerce';
$forum['commerce']['restr']=50;


#############freelancers##################
$freelance['main']['name']='Общий';
$freelance['main']['desc']='Обсуждение любых тем';
$freelance['main']['restr']=0;
$freelance['job']['name']='Задания';
$freelance['job']['desc']='Новые задания, отчеты по выполненным';
$freelance['job']['restr']=0;
$freelance['bugs']['name']='Ошибки';
$freelance['bugs']['desc']='Обнаруженные баги';
$freelance['bugs']['restr']=0;
$freelancers[] = 'deadbeef';
$freelancers[] = 'JustSlon';
$freelancers[] = 'manfredi';
$freelancers[] = 'Beowulfr';
$freelancers[] = 'Xander100';
$freelancers[] = '6Labs';
$freelancers[] = 'Sokol777';
$freelancers[] = 'Invisible';
$freelancers[] = 'FANTASTISH';

###################### PRISON VARS ###########################
$dept[1] = 'ОПИР';
$dept[2] = 'ОМ';
$dept[3] = 'ОБЭП';

$crime[1] = '5.2. Прокачка';
$crime[2] = '1.7.3. (нов. 1.7.2) Хронический мат';
$crime[3] = '1.7.2. (нов 1.7.1)  Торговля/спам/флуд/попрошайничество';
$crime[4] = '6.2.1. Взлом';
$crime[5] = 'Закон №4. Операции на черном рынке';
$crime[6] = '7.2.2. Кидала';
$crime[7] = '5.1. Подкуп избирателей';
$crime[8] = 'Обман администрации';
$crime[9] = '1.6.1 Клевета';
$crime[10] = '1.6.3. Подделка привата';
$crime[11] = 'Использование багов игры';
$crime[12] = 'Решение Администрации';
$crime[13] = 'Грубость и оскорбления';
$crime[14] = '6.3.1. Соучастник взлома';
$crime[15] = '10.8. Проникновение на каторгу';
$crime[16] = '1.5.2. Мат в телеграмме';
$crime[17] = '1.5.1. Грубость в телеграмме';
$crime[18] = '1.2.1.6. Рецидив пропаганды наркотиков в чате';
$crime[19] = '1.2.3.6. Рецидив проявления национализма в чате';
$crime[20] = '1.2.4.8. Рецидив попытки дачи взятки копу в чате';
$crime[21] = '1.3.3.5. Рецидив проявления национализма на форуме';
$crime[22] = '1.3.4.6. Рецидив попытки дачи взятки копу на форуме';
$crime[23] = '1.4.3. Некорректная инфа';
$crime[24] = '1.4.5. Пропаганда наркотиков в инфе';
$crime[25] = '1.4.6. РВС в инфе';
$crime[26] = '1.4.7. Расизм в инфе';
$crime[27] = '1.4.8. Незаконная реклама в инфе';
$crime[28] = '1.5.3. Грубость рупором';
$crime[29] = '1.5.4. Мат рупором';
$crime[30] = '1.5.5. Грубость в подарке';
$crime[31] = '1.5.6. Мат в подарке';
$crime[32] = '1.5.7. Отказ выкинуть грубый/матерный подарок';
$crime[33] = '1.5.8. Грубость/мат в назв. зданий, марк. прод., имени геккона';
$crime[34] = '1.5.9. Флуд системками';
$crime[35] = '1.5.10. Грубость/мат на значке';
$crime[36] = '1.5.11. Пропаганда наркотиков на значке';
$crime[37] = '1.5.12. Угроза расправы в реале в телеграмме';
$crime[38] = '1.6.2. Клевета в инфе';
$crime[39] = '1.6.3. Подделка привата';
$crime[40] = '1.6.4. Клевета на Админов в чате/форуме';
$crime[41] = '1.6.5. Клевета на Админов в инфе';
$crime[42] = '7.2.3. Неоднократное неисполнение коротких сделок';
$crime[43] = '7.3.1. Недостоверная реклама';
$crime[44] = '7.4.1. Невыплата публично обещанного вознаграждения';
$crime[45] = '7.5.1. Мошенничество';
$crime[46] = '7.6.1. Неисполнение регрессного требования';
$crime[47] = '7.7.1. Неисполнение аренды';
$crime[48] = '6.4.1. Попытка обманным путем получить доступ к персу';
$crime[49] = '39.1. Дача заведомо ложных показаний';
$crime[50] = '39.2. Неисполнение решения Трибунала';
$crime[51] = '2.1. Получение взятки';
$crime[52] = '2.2. Дача взятки';
$crime[53] = '3.1. Вымогательство взятки';
$crime[54] = '6.1. Использование служебного положения в корыстных целях';
$crime[56] = '4.3.1 Попытка покупки, продажи, аренды, прокачки персонажа';
$crime[55] = '---КПЗ---';
$crime[57] = '1.3.2.2. Созд. множ. топиков (или топиков без тем. вообще)';
$crime[58] = 'Закон №4. Незаконный оборот ценностей';
$crime[59] = '1.7.4 Невыплата штрафа по статьям закона №1';
$crime[60] = 'Решение Трибунала';
$crime[61] = '5.2 прокачка (КПЗ)';
$crime[62] = '6.5.1 передача пароля';
$crime[63] = '1.2.6. Реал, РВС, наркотики';
$crime[64] = '1.4.4. Обман Администрации и Полиции';
$crime[65] = '1.5.2. Торговля/ спам/ флуд/ попрошайничество';
$crime[66] = '1.5.3. Хрон. мат';
$crime[67] = '1.5.4. Невыплата штрафа';
$crime[68] = '1.2.6. Реал, РВС, Наркотики';
$crime[69] = '1.4.4. Обман Полиции, Администрации';
$crime[70] = '1.4.5. Хрон. оскорб. Полиции';
$crime[71] = '1.5.3. Хрон. мат';
$crime[72] = '1.5.4. Невыплата штрафа';
$crime[73] = '4.3.1. Попытка покупки, продажи, аренды персонажа';
$crime[74] = '4.5.4. Рекламы покупки, продажи, обмена, дарении см';
$crime[75] = 'Закон № 4 незаконный оборот ценностей';
$crime[76] = '5.2.1. Прокачка';
$crime[77] = '5.2.2. Реклама прокачки';
$crime[78] = '6.2.1. Взлом';
$crime[79] = '6.3.1. Посредник взлома';
$crime[80] = '6.5.4. Передача управления персонажем';
$crime[81] = '7.2.2. Кидала';
$crime[82] = '7.2.3. Неоднократное невыполнение условий коротких сделок';
$crime[83] = '7.3.1. Распространение заведомо ложных рекламных объявлений';
$crime[84] = '7.4.1. Невыполнение публичного обещанного вознаграждения';
$crime[85] = '7.5.1. Мошенничество';
$crime[86] = '7.6.1. Неисполнение регрессного требования';
$crime[87] = '7.7.1. Невыполнение условий сделки аренды';
$crime[88] = '26.1.  Дача заведомо ложных показаний';
$crime[89] = '6.1. Использование служебного положения';
$crime[90] = 'Решение Администрации';
$crime[91] = '--КПЗ-- До 30 суток';

//Для вывода в интерфейсе копов
$crime_list[][76] = '5.2. Прокачка';
$crime_list[][68] = '1.2.7. Реал, РВС, Наркотики';
$crime_list[][64] = '1.4.4. Обман Администрации и Полиции';
$crime_list[][70] = '1.4.5. Хрон. оскорб. Полиции';
$crime_list[][71] = '1.5.3. Хрон. мат';
$crime_list[][72] = '1.5.4. Невыплата штрафа';
$crime_list[][77] = '5.2. Реклама прокачки(рецидив)';
$crime_list[][75] = 'Закон № 4 незаконный оборот ценностей';
$crime_list[][73] = '4.3.1. Попытка покупки, продажи, аренды персонажа';
$crime_list[][78] = '6.2.1. Взлом';
$crime_list[][79] = '6.3.1. Посредник взлома';
$crime_list[][62] = '6.5.1. Передача пароля';
$crime_list[][80] = '6.5.4. Передача управления персонажем';
$crime_list[][81] = '7.2.2. Невыполнение условий длинной сделки';
$crime_list[][82] = '7.2.3. Неоднократное невыполнение условий коротких сделок';
$crime_list[][83] = '7.3.1. Распространение заведомо ложных рекламных объявлений';
$crime_list[][84] = '7.4.1. Невыполнение публичного обещанного вознаграждения';
$crime_list[][85] = '7.5.1. Мошенничество';
$crime_list[][88] = '26.1.  Дача заведомо ложных показаний';
$crime_list[][90] = 'Решение Администрации';
$crime_list[][91] = '--КПЗ-- До 30 суток';



$crime_en[1] = '5.2. Illegal drill';
$crime_en[2] = '1.7.3. (new edition - 1.7.2) Continuous language abuse';
$crime_en[3] = '1.7.2. (new edition - 1.7.1) Unlicensed trade';
$crime_en[4] = '6.2.1. Game character hijack';
$crime_en[5] = 'Law number 4. Black market operations';
$crime_en[6] = '7.2.2. Fraud';
$crime_en[7] = '5.1. Bribery of electorate';
$crime_en[8] = 'Fraud of administration';
$crime_en[9] = '1.6.1. Slander';
$crime_en[10] = '1.6.3. Private message falsification';
$crime_en[11] = 'Game bugs usage';
$crime_en[12] = 'Administrative verdict';
$crime_en[13] = 'Roughness and insult';
$crime_en[14] = '6.3.1. Game character hijack participant';
$crime_en[15] = '10.8. Prison intruder';
$crime_en[16] = '1.5.2. Telegraph message language abuse';
$crime_en[17] = '1.5.1. Telegraph message roughness';
$crime_en[18] = '1.2.1.6. Continuous drugs propaganda';
$crime_en[19] = '1.2.3.6. Continuous racism propaganda in chat';
$crime_en[20] = '1.2.4.8. Continuous cop bribing attempts in chat';
$crime_en[21] = '1.3.3.5. Continuous racism propaganda in forum';
$crime_en[22] = '1.3.4.6. Continuous cop bribing attempts in forum';
$crime_en[23] = '1.4.3. Bad personal info';
$crime_en[24] = '1.4.5. Drugs propaganda in personal info';
$crime_en[25] = '1.4.6. Malicious link(s) in personal info';
$crime_en[26] = '1.4.7. Racism propaganda in personal info';
$crime_en[27] = '1.4.8. Illegal advertising in personal info';
$crime_en[28] = '1.5.3. Roughness using loudspeaker';
$crime_en[29] = '1.5.4. Language abuse using loudspeaker';
$crime_en[30] = '1.5.5. Roughness in present card';
$crime_en[31] = '1.5.6. Language abuse in present card';
$crime_en[32] = '1.5.7. Disobey to throw away present containing roughness or language abuse in card';
$crime_en[33] = '1.5.8. Roughness or language abuse in the name of building, trademark, gekko name';
$crime_en[34] = '1.5.9. Telegrams flood';
$crime_en[35] = '1.5.10. Roughness or language abuse in clan badge signature';
$crime_en[36] = '1.5.11. Drugs propaganda in clan badge signature';
$crime_en[37] = '1.5.12. Threat to player in real life';
$crime_en[38] = '1.6.2. Slander in personal info';
$crime_en[39] = '1.6.3. Private message falsification';
$crime_en[40] = '1.6.4. Slander on Administration in chat or forum';
$crime_en[41] = '1.6.5. Slender on administration in personal info';
$crime_en[42] = '7.2.3. Continuous customers fraud';
$crime_en[43] = '7.3.1. Falsification in advertising';
$crime_en[44] = '7.4.1. Nonpayment of publicly promised compensation';
$crime_en[45] = '7.5.1. Swindle';
$crime_en[46] = '7.6.1. Denial to accept regressive duties';
$crime_en[47] = '7.7.1. Default of rent';
$crime_en[48] = '6.4.1. Attempt to take control over character with illegal means';
$crime_en[49] = '39.1. Obviously false testimonies';
$crime_en[50] = '39.2. Default of the decision of the Tribunal';
$crime_en[51] = '2.1. Acceptance of a bribe';
$crime_en[52] = '2.2. Bribery';
$crime_en[53] = '3.1. Extortion of a bribe';
$crime_en[54] = '6.1. Usage of job position in personal interests';
$crime_en[56] = '4.3.1 Attempt to buy or sell or rent or drill a character';
$crime_en[55] = '---TEMPORARY MEASURE---';
$crime_en[57] = '1.3.2.2. Creation of multiple topics with same theme or without theme';
$crime_en[58] = 'Illegal circulation of values';
$crime_en[59] = '1.7.4 Failed to pay fees according to the Law #1';
$crime_en[60] = 'Tribunal decision';
$crime_en[61] = '5.2 Drill (Temporary measure)';
$crime_en[62] = '6.5.1 password sharing';
$crime_en[63] = '1.2.6. Real life issues, malicious links distribution, drugs propaganda';
$crime_en[64] = '1.4.4. Administration or Police fraud';
$crime_en[65] = '1.5.2. Trading, spam, etc.';
$crime_en[66] = '1.5.3. Contionuous abuse';
$crime_en[67] = '1.5.4. Unpayed fees';

###################### PERS RETURN ##########################
//$pers_cops[] = 'Sa To Ri';
//$pers_cops[] = 'Bo dun';
//$pers_cops[] = 'Buscan';
//$pers_cops[] = 'Macuta';
$pers_cops[] = 'deadbeef';
$pers_cops[] = 'Fribble';
$pers_cops[] = 'Hamster001';
$pers_cops[] = 'LEO-OO';
$pers_cops[] = 'AVE';
$pers_cops[] = 'calypso';
$pers_cops[] = 'de_bazzz';
//$pers_cops[] = 'Vault_DarkPriest';
$pers_cops[] = 'Vault_Fribble';
$pers_cops[] = 'V_calypso';


$pers_adm[] = 'Sa To Ri';
$pers_adm[] = 'Bo dun';
$pers_adm[] = 'Buscan';
$pers_adm[] = 'Девочка';
$pers_adm[] = 'deadbeef';
$pers_adm[] = 'Lee-Loo';

$pers_adm_s[] = 'Macuta';
$pers_adm_s[] = 'Lee-Loo';
$pers_adm_s[] = 'deadbeef';
//$pers_adm_s[] = 'Stealth';

###################### Police Academy Posts #############
//$post_names[6] = 'ОМ. Vault';
$post_names[2] = 'ОМ. Оазис';
$post_names[1] = 'ОМ. Москва';
$post_names[7] = 'ОМ. Форум';
//$post_names[3] = 'БО. Москва';
//$post_names[4] = 'БО. Оазис';
//$post_names[5] = 'БО. Нева';
###################### Pages without counter ############
$noTZcounter[] = 'nеws'; //НЕ КОММЕНТИРОВАТЬ!!! НА ГЛАВНОЙ СЧЕТЧИКА БЫТЬ НЕ ДОЛЖНО!!! ЕЩЕ РАЗ ЗАМЕЧУ - ВСЕХ НАХРЕН ПОУБИВАЮ!!!
$noTZcounter[] = 'law_request';
$noTZcounter[] = 'law_results';
$noTZcounter[] = 'pers_request';
$noTZcounter[] = 'prisoned';
//$noTZcounter[] = 'pers_adm';
//$noTZcounter[] = 'pers_cops';
$noTZcounter[] = 'compens';
$noTZcounter[] = 'compens2';
$noTZcounter[] = 'deals';


$first_april[] = 'news';
$first_april[] = 'news_search';
$first_april[] = 'fotos';
$first_april[] = 'fotos_add';
$first_april[] = 'rsd';
$first_april[] = 'resprice';
$first_april[] = 'data';
$first_april[] = 'movepoints';
$first_april[] = 'countprof';
$first_april[] = 'data2';
$first_april[] = 'cops_depts';
$first_april[] = 'public_posts';
$first_april[] = 'black_list';
$first_april[] = 'warez';
$first_april[] = 'userbars';
$first_april[] = 'n_comm';

###################### Compensations ############
$compens_cops[] = 'Ксакеп';
$compens_cops2[] = 'Ксакеп';

$comm_tpl_cats[1] = 'ОМ';
$comm_tpl_cats[2] = 'ОР';
$comm_tpl_cats[3] = 'ОБЭП';
$comm_tpl_cats[4] = 'ОИН';
$comm_tpl_cats[5] = 'Рейтинг';
//$comm_tpl_cats[1] = '';

$manuals[] = "Профессии";
$manuals[] = "Бестиарий";
$manuals[] = "Развитие персонажа";
$manuals[] = "Заработок";
$manuals[] = "Военные тайны";
$manuals[] = "Окружающий мир";
$manuals[] = "Питомцы";
$manuals[] = "Другое";
$manuals[] = "Устаревшие";

$quests[] = "До 12 уровня";
$quests[] = "12+ уровня";
$quests[] = "Получение профессии";
$quests[] = "Репутационные";
$quests[] = "Профессиональные";
$quests[] = "Устаревшие";

############### Ники запрещенные для регистрации на сайте #############
// !!!! в нижнем регистре пишем !!!!
$DisabledRegisterNames=array('terminal pa', 'terminal 00', 'terminal 01', 'terminal 02', 'terminal 03', 'terminal 04', 'terminal 05', 'terminal 06', 'hony', 'big brother', 'hate ati', 'начальник мп', 'начальник фа', 'начальник па', 'брюнеточка', 'terminal police', 'главбух', 'верещагин', 'mp 00', 'mp 01', 'mp 02', 'mp 03', 'mp 04', 'mp 05', 'mp 06', 'mp 07', 'mp 08', 'mp 09', 'mp 10');

#альты проф.
$prof_alt[0] = 'Без профессии';
$prof_alt[1] = 'Корсар';
$prof_alt[2] = 'Сталкер';
$prof_alt[3] = 'Шахтер';
$prof_alt[4] = 'Инженер';
$prof_alt[5] = 'Наемник';
$prof_alt[6] = 'Торговец';
$prof_alt[7] = 'Коп. Патрульный';
$prof_alt[8] = 'Коп. Штурмовик';
$prof_alt[9] = 'Коп. Специалист';
$prof_alt[10] = 'Журналист';
$prof_alt[11] = 'Чиновник';
$prof_alt[12] = 'Псионик';
$prof_alt[13] = 'Каторжник';
$prof_alt[14] = 'Пси-кинетик';
$prof_alt[15] = 'Пси-медиум';
$prof_alt[16] = 'Пси-лидер';
$prof_alt[26] = 'Ропат';
$prof_alt[27] = 'Ропат';
$prof_alt[28] = 'Ропат';
$prof_alt[30] = 'Дилер';


//функции которые по идее нахер не сдались
#блока онлайна давно нет
function TZOnline() {
	$query = 'SELECT `value` FROM `const` WHERE `script` = \'tzonline\' AND `name` = \'updated\' LIMIT 1;';
	$res = mysql_query($query);
	$rs = mysql_fetch_array($res);
	$now = time()-300;
	if ($rs['value'] < $now) {
		$html = implode ('', file ('http://www.timezero.ru/'));
		if (ereg('<!--ustat1start-->', $html)) {
			$usersonline=sub($html, '<!--ustat1start-->','<!--ustat1end-->');
		} else {
			$usersonline=1000;
		}
		$query = 'UPDATE `const` SET `value` = \''.time().'\' WHERE `script` = \'tzonline\' AND `name` = \'updated\' LIMIT 1;';
		mysql_query($query);
		$query = 'UPDATE `const` SET `value` = \''.$usersonline.'\' WHERE `script` = \'tzonline\' AND `name` = \'online\' LIMIT 1;';
		mysql_query($query);
	} else {
		$query = 'SELECT `value` FROM `const` WHERE `script` = \'tzonline\' AND `name` = \'online\' LIMIT 1;';
		$res = mysql_query($query);
		$rs = mysql_fetch_array($res);
		$usersonline=$rs['value'];
	}
return $usersonline;
}

#mb_strtolower($str,"cp1251")
function tzpd_strtolower($str) {
	$upper = 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ';
	$lower = 'абвгдеёжзийклмнопрстуфхцчшщъыьэюя';
	return (strtr ($str, $upper, $lower));
}

#mb_convert_encoding(from,to,data);
function uencode($str, $type) {
	static $conv='';
	if (!is_array ($conv)) {
		$conv=array ();
		for ( $x=128; $x <=143; $x++ ) {
			$conv['utf'][]=chr(209).chr($x);
			$conv['win'][]=chr($x+112);
		}
		for ( $x=144; $x <=191; $x++ ) {
			$conv['utf'][]=chr(208).chr($x);
			$conv['win'][]=chr($x+48);
		}
		$conv['utf'][]=chr(208).chr(129);
		$conv['win'][]=chr(168);
		$conv['utf'][]=chr(209).chr(145);
		$conv['win'][]=chr(184);
	}
	if ($type=='w')
		return str_replace ( $conv['utf'], $conv['win'], $str );
	elseif ($type=='u')
		return str_replace ( $conv['win'], $conv['utf'], $str );
	else
		return $str;
}




#ура блеать через пол файла началось оно самое...
##########################################################################################


//setlocale(LC_ALL, 'ru_RU');
function unicode_russian($str) {
	$encode = "";
	for ($ii=0;$ii<strlen($str);$ii++) {
		$xchr=substr($str,$ii,1);
		if (ord($xchr)>191) {
			$xchr=ord($xchr)+848;
			$xchr="&#" . $xchr . ";";
		}
		if(ord($xchr) == 168) {
			$xchr = "&#1025";
		}
		if(ord($xchr) == 184) {
			$xchr = "&#1105";
		}
		$encode=$encode.$xchr;
	}
return $encode;
}

function send_sysmsg($nick, $message,$who = ''){
	$noerror=1;
	//	ник персонажа || текст телеграммы
	$message = "\n$who || ".$nick.' || '.$message."\n";
	// заглушка системы антиспама - каждая вторая мессага левому боту
	//	$message .= "Terminal PA || antispam\n";
	$fname = '/home/sites/police/bot_fees/alerts.txt';
	if(file_exists($fname)) chmod($fname, 0777);
	if($handle = fopen($fname, 'a')){
		if(fwrite($handle, $message) === FALSE)	{
			$noerror=0;
		}
		fclose($handle);
	} else {
		$noerror=0;
	}
return $noerror;
}

function mp_list() {
	$sSQL = 'SELECT Users.name AS `name`, Users.level AS `level`, Users.pro AS `pro`, Users.sex AS `sex` FROM `tzpolice_tz_users` AS Users, `tzpolice_tz_clans` AS Clans WHERE Clans.name=\'Military Police\' AND Clans.id = Users.clan_id ORDER BY Users.name ASC';
	$result = mysql_query($sSQL);
	$nrows=mysql_num_rows($result);
	if($nrows>0){
		while($row = mysql_fetch_array($result)){
		//	$clan = "Military Police";
			$returntxt .= '[pers clan=Military Police nick='.stripslashes($row['name']).' level='.$row['level'].' pro='.$row['pro'].''.(($row['sex']=='0')?'w':'').']<br>';
		}
	} else {
		$returntxt .= '<CENTER>никого нет</CENTER>';
	}
return $returntxt;
}

function mywordwrap($string) {
	$length = strlen($string);
	for ($i=0; $i<=$length; $i=$i+1) {
		$char = substr($string, $i, 1);
		if ($char == '<') $skip=1;
		elseif ($char == '>') $skip=0;
		elseif ($char == ' ') $wrap=0;

		if ($skip==0) $wrap=$wrap+1;
		$returnvar = $returnvar.$char;
		if ($wrap>40) { // alter this number to set the maximum word length
			$returnvar = $returnvar . '<wbr>';
			$wrap=0;
		}
	}
return $returnvar;
}

function mquote($str) {
	$str=mysql_real_escape_string($str);
	return $str;
}

function unhtmlentities ($string) {
	$trans_tbl = get_html_translation_table (HTML_ENTITIES);
	$trans_tbl = array_flip ($trans_tbl);
	return strtr ($string, $trans_tbl);
}

function ShowPagesComm($CurPage,$TotalPages,$ShowMax,$QueryStr) {
	$PrevList=floor(($CurPage-1)/$ShowMax)*$ShowMax;
	$NextList=$PrevList+$ShowMax+1;
	if($PrevList>=$ShowMax*2) {
		echo '<a href="javascript:{}" onclick="opencomments(\''.$QueryStr.'\', 1)" title="В самое начало"><< </a> ';
	}
	if($PrevList>0) {
		echo '<a href="javascript:{}" onclick="opencomments(\''.$QueryStr.'\', \''.$PrevList.'\')"  title="Предыдущие '.$ShowMax.' страниц">...</a> ';
	}
	for($i=$PrevList+1;$i<=$PrevList+$ShowMax;$i++) if($i<=$TotalPages) {
		if($i==$CurPage) {			echo '<u>'.$i.'</u> ';
		} else {			echo '<a href="javascript:{}" onclick="opencomments(\''.$QueryStr.'\', '.$i.')">'.$i.'</a> ';
		}
	}
	if($NextList<=$TotalPages) {
		echo '<a href="javascript:{}" onclick="opencomments(\''.$QueryStr.'\', \''.$NextList.'\')"  title="Следующие '.$ShowMax.' страниц">...</a> ';
	}
	if($CurPage<$TotalPages) {
		echo '<a href="javascript:{}" onclick="opencomments(\''.$QueryStr.'\', \''.$TotalPages.'\')" title="В самый конец"> >></a>';
	}
}



function ipCheck() {
	if (getenv('HTTP_CLIENT_IP')) {
		$ip = getenv('HTTP_CLIENT_IP');
	} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif (getenv('HTTP_X_FORWARDED')) {
		$ip = getenv('HTTP_X_FORWARDED');
	} elseif (getenv('HTTP_FORWARDED_FOR')) {
		$ip = getenv('HTTP_FORWARDED_FOR');
	} elseif (getenv('HTTP_FORWARDED')) {
		$ip = getenv('HTTP_FORWARDED');
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
return $ip;
}

//Попытка утягать страницу форума
function ForumConn($path, $enforce=0) {
	$sock = @fsockopen("www.timezero.ru", 80, $er1, $er2, 5);
	if (@$sock) {
		fputs($sock, "GET /".$path." HTTP/1.0\r\n");
		fputs($sock, "Host: www.timezero.ru \r\n");
		fputs($sock, "Content-type: application/x-www-url-encoded \r\n");
		fputs($sock, "\r\n\r\n");
		$tmp_headers = '';
		while ($str = trim(fgets($sock, 4096))) $tmp_headers .= $str."\n";
		$tmp_body = '';
		while (!feof($sock)) $tmp_body .= fgets($sock, 4096);
		$tmp_pos1 = strpos($tmp_body, 'about="');
		if($tmp_pos1!==false) {
			$tmp_str1 = substr($tmp_body, 0, $tmp_pos1);
			$tmp_str2 = substr($tmp_body, strpos($tmp_body, '"', $tmp_pos1+8));
			$tmp_body = $tmp_str1.' '.$tmp_str2;
		}
		$tmp_body = htmlspecialchars($tmp_body);
		$tmp_body = uencode($tmp_body, 'w');
		if(strpos($tmp_body, 'Internal Server Error')===false) {
			$tmp_body['error'] = 0;
			return $tmp_body;
		} else {
			if($enforce==0) {
				$funcerror['error'] = 'TIMEOUT';
				return $funcerror;
			} else {
				sleep(1);
				return Forum($path,1);
			}
		}
	} else {
		$funcerror['error'] = 'NOT_CONNECTED';
		return $funcerror;
	}
}


function TZConn2($login, $enforce=0) {

	$info = file_get_contents("http://www.timezero.ru/info.pl?".urlencode($login));
	$info = preg_replace("#\n|\t|\r#","",$info);
    preg_match("#<!--START-->(.*)<!--END-->#i",$info,$uinfo);
    $info = $uinfo[1];

    if($info) {
        preg_match("#<!--swf start-->(.*)<!--swf end-->#i",$info,$uparams);
        preg_match("#<!--characteristics start-->(.*)<!--characteristics end-->#i",$info,$ustats);
       	preg_match_all("#src=\"/i/([^\"]+).gif\"#i",$uparams[1],$ucl);
       	preg_match("#<b>([^&]+)&nbsp;\[([0-9]+)\]</b>#i",$uparams[1],$ulogin);
        preg_match("#i([0-9]{1,2})(w?)#",$ucl[1][1],$prof);

        preg_match("#offline#i",$info,$onl);

        $funcerror['login'] = $ulogin[1];
        $funcerror['level'] = ceil($ulogin[2]);
        $clan = explode("/",$ucl[1][0]);
        $funcerror['clan'] = $clan[1];
        $funcerror['pro'] = $prof[1];
        $funcerror['man'] = ($prof[2])?1:0;
        $funcerror['online'] = ($onl[0])?0:1;

        preg_match_all("#<td[^>]+><b><b>([0-9]+)</b></b></td>#i",$ustats[1],$stats);

        $funcerror['str'] = $stats[1][0];
        $funcerror['dex'] = $stats[1][1];
       	$funcerror['int'] = $stats[1][2];
        $funcerror['pow'] = $stats[1][3];
        $funcerror['acc'] = $stats[1][4];
        $funcerror['intel'] = $stats[1][5];

        if($funcerror['level'] < 1) {
        	$funcerror['error'] = 'TIMEOUT';
        }
        return $funcerror;

    } else {
    	$funcerror['error'] = 'TIMEOUT';
    	return $funcerror;
    }

}

//UserInfo functions
function sub($text, $st1, $st2, $init=0) {
	$offset1=@strpos($text, $st1, $init)+strlen($st1);
	if($offset1==strlen($st1)) {		return 0;
	} else {
		$offset2=strpos($text, $st2, $offset1);
		$res=@substr($text,$offset1,$offset2-$offset1);
		if(!empty($res)) {			return $res;
		} else {			return 0;
		}
	}
}

#берём инфу по персу из бд
function locateUser($login) {	$login = addslashes(htmlspecialchars(trim($login)));
	$query = mysql_query("SELECT * FROM locator WHERE login = '$login' LIMIT 1");
	$needupdate = 0;
	$needinsert = 0;
	if(mysql_num_rows($query) > 0) {
		$user = mysql_fetch_assoc($query);
		$user['level'] = $user['lvl'];
		$user['man'] = $user['gender'];
		$user['gen'] = $user['gender'];

		if(time()-$user['utime'] > 86400) {			$needupdate++;
		}
	} else {		$needinsert++;
	}

	#временно, пока локатор нормально не станет обновляться.
	if($needupdate > 0 || $needinsert > 0) {		$user = TZConn($login, 1, 1);

		if($user['level'] > 0) {			if($needinsert > 0) {				$query = "INSERT INTO locator (`id`,`addtime`,`utime`,`location`,`server`,`clan`,`login`,`lvl`,`pro`,`pvpr`,`pvprank`,`gender`)
				VALUES(NULL,'".$user['addtime']."','".$user['utime']."','".$user['location']."','".$user['server']."','".$user['clan']."',
				'".$user['login']."','".$user['level']."','".$user['pro']."','".$user['pvpr']."','".$user['pvprank']."','".$user['gender']."')";
			} else {				$query = "UPDATE locator SET `utime` = '".$user['utime']."', `location` = '".$user['location']."', `server` = '".$user['server']."',
				`clan` = '".$user['clan']."', `lvl` = '".$user['level']."', `pro` = '".$user['pro']."', `pvpr` = '".$user['pvpr']."',
				`pvprank` = '".$user['pvprank']."' WHERE login='".$user['login']."'";

			}			mysql_query($query);
		}
	}
	$user['lvl'] = ($user['lvl'])?$user['lvl']:$user['level'];
	return $user;
}

function formatUser($u) {	if($u[lvl] > 0) {		$return = ($u[clan])?"<img src='http://timezero.ru/i/clans/".$u[clan].".gif' border=0 style='vertical-align: text-bottom;'>":"";
		$return .= "<b>".$u['login']."</b> [".$u[lvl]."]";
		$return .= "<img src='http://timezero.ru/i/i".$u[pro].".gif' border=0 style='vertical-align: text-bottom;'>";
		$return .= "<img src='http://timezero.ru/i/rank/".$u[pvprank].".gif' border=0 style='vertical-align: text-bottom;'>";
		return $return;    } else {    	return "<b>".$u['login']."</b>";
    }
}

function TZConn($login, $enforce=1,$leave) {
	if(!$leave) return locateUser($login);

	$url = 'http://www.timezero.ru/info.pl?userxml='.urlencode(tzpd_strtolower($login));
	$reserve_url = 'http://stalkerz.ru/ajax/radar.ajax.php?police=thisisdegradesecurecode&nick='.urlencode(tzpd_strtolower($login));
	$reserve_params = array('clan','login','level','pro','gender','pvprank','pvpr','utime','server','addtime','location');

	if($enforce == 0) {
		$userXML = new DomDocument();
		@$userXML->loadXML(file_get_contents($url, false, stream_context_create(array('http'=>array('timeout'=>2)))));
		$userTag = $userXML->getElementsByTagName('USER');
	}

	$userInfo = array();

	if($enforce == 0 && $userTag->length == 1){
		$userAttributes = $userTag->item(0)->attributes;
		if(!is_null($userAttributes)){
			foreach($userAttributes as $index=>$attribute) {
				$userInfo[$attribute->name] = iconv("UTF-8", "windows-1251", $attribute->value);
			}
		}
	} else {
		$stalk_info = file_get_contents($reserve_url, false, stream_context_create(array('http'=>array('timeout'=>2))));
		if($stalk_info != ''){
			$stalk_info = explode(';',$stalk_info);
			foreach($stalk_info as $param){
				$param = explode(':', $param);
				if(in_array($param[0], $reserve_params)) $userInfo[$param[0]] = $param[1];
			}
		}
	}
	#фикс, если инфы по персу нет
	$userInfo['login'] = ($userInfo['login'])?$userInfo['login']:$login;
	$userInfo['level'] = ($userInfo['level'])?$userInfo['level']:0;
	$userInfo['pro'] = ($userInfo['pro'])?$userInfo['pro']:0;
	$userInfo['gender'] = ($userInfo['gender'])?$userInfo['gender']:1;
	$userInfo['pvprank'] = ($userInfo['pvprank'])?$userInfo['pvprank']:1;

return $userInfo;
}

function TZConn_update_clan_members($clan){
	$cache_time = 1*24*60*60;

	$result = mysql_query('select `lastupdate` from `data_clans_cache` where (`clan_name`="'.addslashes($clan).'") limit 1');
	if(mysql_num_rows($result) > 0) $row = mysql_fetch_assoc($result);

	if((mysql_num_rows($result) == 0) || ($row['lastupdate'] < time()-$cache_time)){
  		$url = 'http://www.timezero.ru/info.pl?clanxml='.urlencode(tzpd_strtolower($clan));

		$clanXML = new DomDocument();
		@$clanXML->loadXML(
			file_get_contents($url, false, stream_context_create(array('http'=>array('timeout'=>2))))
		);

		if ($clanXML->getElementsByTagName('CLAN')->length > 0){
		    mysql_query('delete from `data_clan_members_cache` where (`clan_name`="'.addslashes($clan).'");');
		    if (mysql_num_rows($result) == 0){
				mysql_query('insert into `data_clans_cache` (`clan_name`, `lastupdate`) values (\''.addslashes($clan).'\', \''.time().'\');');
		    } else {
				mysql_query('update `data_clans_cache` set `lastupdate`=\''.time().'\' where (`clan_name`="'.addslashes($clan).'");');
		    }
		}

		$memberTag = $clanXML->getElementsByTagName('USER');
		if ($memberTag->length > 0){
			for ($i=0;$i<$memberTag->length;$i++){
				$login = iconv("UTF-8", "windows-1251", $memberTag->item($i)->getAttribute('login'));
				$s1 = iconv("UTF-8", "windows-1251", $memberTag->item($i)->getAttribute('clan_s1'));
				$s2 = iconv("UTF-8", "windows-1251", $memberTag->item($i)->getAttribute('clan_s2'));
	      		mysql_query('insert into `data_clan_members_cache` (`clan_name`, `user_name`, `clan_s1`, `clan_s2`) values (\''.addslashes($clan).'\', \''.$login.'\', \''.$s1.'\', \''.$s2.'\');');
			}
		}
	}
}

function GetInfoFromApi($nick) {
	$nick = (mb_convert_encoding($nick,"cp1251","utf8"))?mb_convert_encoding($nick,"cp1251","utf8"):$nick;
    $nick = str_replace(" ","%20",$nick);
    $url = "http://www.timezero.ru/info.pl?userxml=".mb_strtolower($nick,"cp1251");
	$info = file_get_contents($url);
	preg_match("#<USER([^>]+)>#",$info,$data);
	if(!$data[1]) return false;
	preg_match_all("#\s([A-Za-z_-]+)=\"([^\"]+)\"#",$data[1],$data);
    $user = Array();
    foreach($data[1] as $k => $v) {
    	$user[$v] = (mb_convert_encoding($data[2][$k],"cp1251","utf8"))?mb_convert_encoding($data[2][$k],"cp1251","utf8"):$data[2][$k];
    }
    return $user;
}

function GetUserInfo($nick, $usecache=0) {
	$cache_time = 24*60*60;

	$result = mysql_query('select `data_str`, `lastupdate` from `data_cache` where (`user_name`="'.addslashes($nick).'") limit 1');
	if (mysql_num_rows($result) > 0) $row = mysql_fetch_assoc($result);

	if (($usecache == 0) || ((mysql_num_rows($result) > 0) &&($row['lastupdate'] < time()-$cache_time)) || (mysql_num_rows($result) == 0)) {
		$userInfo = TZConn($nick);

		if (sizeof($userInfo) > 0) {
    		if (mysql_num_rows($result) == 0) mysql_query('insert into `data_cache` (`user_name`, `data_str`, `lastupdate`) VALUES (\''.$nick.'\', \''.serialize($userInfo).'\', \''.time().'\');');
    		if (mysql_num_rows($result) > 0) mysql_query('update `data_cache` set `data_str` = \''.serialize($userInfo).'\', `lastupdate` = \''.time().'\' where (`user_name`="'.addslashes($nick).'");');

    		tz_users_update($userInfo);
    		mysql_query('UPDATE `site_users` SET `avatar`=\''.$userInfo['img'].'\', `clan`=\''.$userInfo['clan'].'\' WHERE `user_name`=\''.$userInfo['login'].'\' LIMIT 1;');
   			mysql_query('UPDATE `fotos_users` SET `clan`=\''.$userInfo['clan'].'\' WHERE `nick`=\''.$userInfo['login'].'\' LIMIT 1;');
		}
	} else {
		$userInfo = unserialize($row['data_str']);
	}

	if ($userInfo['clan'] != '') TZConn_update_clan_members($userInfo['clan']);
	$result = mysql_query('select `clan_s1`, `clan_s2` from `data_clan_members_cache` where (`user_name`="'.$userInfo['login'].'") limit 1');

	if (mysql_num_rows($result) > 0) {
		if($userInfo['clan'] != '') {
    		$row = mysql_fetch_assoc($result);
    		$userInfo['s1'] = $row['clan_s1'];
    		$userInfo['s2'] = $row['clan_s2'];
		} else {
    		mysql_query('delete from `data_clan_members_cache` where (`user_name`="'.$userInfo['login'].'") limit 1;');
  		}
	}
	//Не знаю нафига это, но в старом варианте было.
	if ($userInfo['man'] == 1) {$userInfo['gen'] = 1;} else {$userInfo['gen'] = 2;}
return $userInfo;
}

function UpdateUser($id) {
	$SQL = 'SELECT `ips`, `user_name`, `clan` FROM `site_users` WHERE `id`=\''.$id.'\';';
	$r = mysql_query($SQL);
	$d = mysql_fetch_assoc($r);
	$ips = explode(', ', $d['ips'], 10);
	unset($ips[9]);
	array_unshift($ips, $_SERVER['REMOTE_ADDR']);
	$ips = array_unique($ips);
	$ips = implode(', ', $ips);
	$SQL = 'UPDATE `site_users` SET `ips`=\''.$ips.'\', `last_visit`=\''.time().'\' WHERE `id`=\''.$id.'\';';
	mysql_query($SQL);

	$userinfo = GetUserInfo($d['user_name']);
return true;
}


//я в ахуе от этих функций(((
function ParseNews($buf, $AllowTags, $replaceBR=1) {
	$outttvar = '';
	if($AllowTags==0) $buf = strip_tags($buf, '<b><i><u><div><wbr><strike><embed><object>');
	$buf = stripslashes($buf);
	if($replaceBR==1) $buf = str_replace("\n", "<br>", $buf);
	$FontColors1 = array('[red]', '[/red]', '[green]', '[/green]', '[blue]', '[/blue]');
	$FontColors2 = array('<font color=red>', '</font>', '<font color=green>', '</font>', '<font color=blue>', '</font>');
	$buf = str_replace($FontColors1,$FontColors2,$buf);
	$buf = str_replace('{MP_LIST}',mp_list(),$buf);
	$text = $buf;
	$text = preg_replace("/\[log\](.*?)\[\/log\]/si", "<b><font color='blue'>\\1</font> [<a href='#; return false' onclick=\"ClBrd2('\\1')\" alt=\"Скопировать в буфер обмена\">скопировать</a> / <a href='#; return false' onclick=\"LogWin('\\1')\" alt='Просмотреть бой'>просмотреть</a>]</b>", $text);
	$text = preg_replace("/\[pers nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<b>\\1</b> [\\2]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\3.gif' border='0'>", $text);
	$text = preg_replace("/\[pers clan=(.*?) nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/clans/\\1.gif' alt='\\1' border='0' height='16' width='28'><b>\\2</b> [\\3]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\4.gif' border='0'>", $text);
	$text = preg_replace("/\[clan=(.*?)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+) nick=([0-9A-Za-z *\-]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
	$buf = $text;
	$buf = preg_replace("/\[prof\](.*?)\[\/prof\]/si","<img border=0 style='vertical-align:text-bottom' src='/_imgs/pro/\\1.gif'>",$buf);
	$buf = preg_replace("/\[clan\](.*?)\[\/clan\]/si","<img border=0 src='/_imgs/clans/\\1.gif'>",$buf);
	$buf = eregi_replace("\\[img\\]([a-z0-9\\./\\-]+)\\[/img\\]","<img border=0 src='/user_data/\\1'>",$buf);
	$buf = eregi_replace("\\[imgprev\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><img border=0 src='/user_data/\\1/thumb/\\2' ALT='Нажмите, чтобы увеличить'></a>",$buf);
	$buf = preg_replace("/\[URL\](.*?);(.*?)\[\/url\]/si", "<a href='\\1' target=_blank>\\2</a>",$buf);
	$buf = explode(' ',$buf);
	for($i=0;$i<count($buf);$i++) {
		if(eregi(':([a-z0-9_]+):',$buf[$i],$ok)) {
			if(is_file('/home/sites/police/www/_imgs/smiles/'.$ok[1].'.gif')) {
				$buf[$i]=str_replace(':'.$ok[1].':', '<img border=0 src="/_imgs/smiles/'.$ok[1].'.gif"> ', $buf[$i]);
			}
			$outttvar .= $buf[$i].' ';
		} else {
			$outttvar .= $buf[$i].' ';
		}
	}
	if (preg_match_all('#(.*)\[dialog=".*"\](.*)\[/dialog\]#Usi', $outtvar, $t)) {
		for($i = 0; $i < count($t[0]); $i++) {
			$tmp = '';
			preg_match_all('#\[dialog="(.*?)"\]#si', $t[0][$i], $t1);
			$tmp .= $t[1][$i];
			$tmp .= '<div class="quote"><img height="64" width="62" src="http://www.tzpolice.ru/i/avatar/'.$t1[1][0].'" class="dialog">';
			$tmp .= preg_replace('#\[(.*)\] private \[(.*?)\]#', '<b>[${1}] <font color="red">private [${2}]</font></b>', $t[2][$i]);
			$tmp .= '<div class="clear"></div></div>';
			$pos1=strpos($text,$t[0][$i]);
			$pos2=strlen($t[0][$i]);
			$outttvar=substr($outttvar,0,$pos1).$tmp.substr($outttvar,$pos1+$pos2);
		}
	}
	$outttvar = mywordwrap($outttvar);
	echo ($outttvar);
	//	return $outttvar;
}

function ParseNews2($text) {
	$text = stripslashes($text);
	$text = strip_tags($text, '<a><div><table><tr><td><strike><script><object><param><embed><object>');
	if (preg_match_all('#(.*)\[dialog=".*"\](.*)\[/dialog\]#Usi', $text, $t)) {
		for($i = 0; $i < count($t[0]); $i++) {
			$tmp = '';
			preg_match_all('#\[dialog="(.*?)"\]#si', $t[0][$i], $t1);
			$tmp .= $t[1][$i];
			$tmp .= '<div class="quote"><img height="64" width="62" src="http://www.tzpolice.ru/i/avatar/'.$t1[1][0].'" class="dialog">';
			$tmp .= preg_replace('#\[(.*)\] private \[(.*?)\]#', '<b>[${1}] <font color="red">private [${2}]</font></b>', $t[2][$i]);
			$tmp .= '<div class="clear"></div></div>';
			$pos1=strpos($text,$t[0][$i]);
			$pos2=strlen($t[0][$i]);
			$text=substr($text,0,$pos1).$tmp.substr($text,$pos1+$pos2);
		}
	}

	$text = preg_replace("/\[log\](.*?)\[\/log\]/si", "<b><font color='blue'>\\1</font> [<a href='#; return false' onclick=\"ClBrd2('\\1')\" alt=\"Скопировать в буфер обмена\">скопировать</a> / <a href='#; return false' onclick=\"LogWin('\\1')\" alt='Просмотреть бой'>просмотреть</a>]</b>", $text);
	$text = preg_replace("/\[url=([\w]+?:\/\/.*?)\](.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url\]([\w]+?:\/\/.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\1</a>", $text);
	$text = preg_replace("/\[url\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\1</a>", $text);
	$text = preg_replace('/\[color="(\#[0-9A-F]{6}|[a-z]+)"\]/si', "<font color='\\1'>", $text);
	$text = preg_replace("/\[color=(\#[0-9A-F]{6}|[a-z]+)\]/si", "<font color='\\1'>", $text);
	$text = str_replace('[/color]', '</font>', $text);
	$text = preg_replace("/\[pers nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<b>\\1</b> [\\2]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\3.gif' border='0'>", $text);
	$text = preg_replace("/\[pers clan=(.*?) nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/clans/\\1.gif' alt='\\1' border='0' height='16' width='28'><b>\\2</b> [\\3]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\4.gif' border='0'>", $text);
	$text = preg_replace("/\[clan=(.*?)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+) nick=([0-9A-Za-z *\-]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
	$text = str_replace('[b]', '<b>', $text);
	$text = str_replace('[/b]', '</b>', $text);
	$text = str_replace('[u]', '<u>', $text);
	$text = str_replace('[/u]', '</u>', $text);
	$text = str_replace('[i]', '<i>', $text);
	$text = str_replace('[/i]', '</i>', $text);
	$text = str_replace('[quote]', '<table><tr><td class="quote-news">', $text);
	$text = str_replace('[/quote]', '</td></tr></table>', $text);
	$text = str_replace('[left]', '<div align="left">', $text);
	$text = str_replace('[/left]', '</div>', $text);
	$text = str_replace('[center]', '<div align="center">', $text);
	$text = str_replace('[/center]', '</div>', $text);
	$text = str_replace('[small]', '<font size="-2">', $text);
	$text = str_replace('[/small]', '</font>', $text);
	$text = str_replace('[right]', '<div align="right">', $text);
	$text = str_replace('[/right]', '</div>', $text);
	$text = preg_replace("/\[imageleft\](.*?)\[\/image\]/si", "<img border='0' src='\\1' class='leftimg'>", $text);
	$text = str_replace('[image]', '<img border="0" src="', $text);
	$text = str_replace('[/image]', '">', $text);
	$text = str_replace('[list]', '<ul>', $text);
	$text = str_replace('[list=x]', '<ol>', $text);
	$text = str_replace('[*]', '<li>', $text);
	$text = str_replace('[/list]', '</ul>', $text);
	$text = str_replace('[/list=x]', '</ol>', $text);
	$text = eregi_replace("\\[img\\]([a-z0-9\\./\\-]+)\\[/img\\]", "<img border=0 src='/user_data/\\1'>", $text);
	$text = eregi_replace("\\[imgleft\\]([a-z0-9\\./\\-]+)\\[/img\\]", "<div  style='float: left; padding-top: 5px; padding-right: 15px; padding-bottom: 10px; padding-left: 0px;'><img border='0' src='/user_data/\\1'></div>", $text);
	$text = eregi_replace("\\[imgprev\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><img border=0 src='/user_data/\\1/thumb/\\2' ALT='Нажмите, чтобы увеличить'></a>",$text);
	$text = eregi_replace("\\[imgprevleft\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><div  style='float: left; padding-top: 5px; padding-right: 15px; padding-bottom: 10px; padding-left: 0px;'><img border='0' src='/user_data/\\1/thumb/\\2' ALT='Нажмите, чтобы увеличить'></div></a>",$text);
	$text = str_replace("\n", '<br>', $text);
	$tmp = explode(' ',$text);
	for($i=0;$i<count($tmp);$i++) {
		if(eregi(':([a-z0-9_]+):', $tmp[$i], $ok)) {
			if(is_file('_imgs/smiles/'.$ok[1].'.gif'))
			$text=str_replace(':'.$ok[1].':', '<img src="_imgs/smiles/'.$ok[1].'.gif"> ', $text);
		}
	}
	echo ($text);
//	return true;
}


function ParseNews2a($text) {
	$text = stripslashes($text);
	$text = strip_tags($text, '<a><div><table><tr><td><strike><script><strong><em><img><br><embed><object>');

	if (preg_match_all('#(.*)\[dialog=".*"\](.*)\[/dialog\]#Usi', $text, $t)) {
		for($i = 0; $i < count($t[0]); $i++) {
			$tmp = '';
			preg_match_all('#\[dialog="(.*?)"\]#si', $t[0][$i], $t1);
			$tmp .= $t[1][$i];
			$tmp .= '<div class="quote"><img height="64" width="62" src="http://www.tzpolice.ru/i/avatar/'.$t1[1][0].'" class="dialog">';
			$tmp .= preg_replace('#\[(.*)\] private \[(.*?)\]#', '<b>[${1}] <font color="red">private [${2}]</font></b>', $t[2][$i]);
			$tmp .= '<div class="clear"></div></div>';
			$pos1=strpos($text,$t[0][$i]);
			$pos2=strlen($t[0][$i]);
			$text=substr($text,0,$pos1).$tmp.substr($text,$pos1+$pos2);
		}
	}

	$text = preg_replace("/\[log\](.*?)\[\/log\]/si", "<b><font color='blue'>\\1</font> [<a href='#; return false' onclick=\"ClBrd2('\\1')\" alt=\"Скопировать в буфер обмена\">скопировать</a> / <a href='#; return false' onclick=\"LogWin('\\1')\" alt='Просмотреть бой'>просмотреть</a>]</b>", $text);
	$text = preg_replace("/\[url=([\w]+?:\/\/.*?)\](.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url\]([\w]+?:\/\/.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\1</a>", $text);
	$text = preg_replace("/\[url\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\1</a>", $text);
	$text = preg_replace('/\[color="(\#[0-9A-F]{6}|[a-z]+)"\]/si', "<font color='\\1'>", $text);
	$text = preg_replace("/\[color=(\#[0-9A-F]{6}|[a-z]+)\]/si", "<font color='\\1'>", $text);
	$text = str_replace('[/color]', '</font>', $text);
	$text = preg_replace("/\[pers nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<b>\\1</b> [\\2]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\3.gif' border='0'>", $text);
	$text = preg_replace("/\[pers clan=(.*?) nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/clans/\\1.gif' alt='\\1' border='0' height='16' width='28'><b>\\2</b> [\\3]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\4.gif' border='0'>", $text);
	$text = preg_replace("/\[clan=(.*?)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+) nick=([0-9A-Za-z *\-]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
	$text = str_replace('[b]', '<b>', $text);
	$text = str_replace('[/b]', '</b>', $text);
	$text = str_replace('[u]', '<u>', $text);
	$text = str_replace('[/u]', '</u>', $text);
	$text = str_replace('[i]', '<i>', $text);
	$text = str_replace('[/i]', '</i>', $text);
	$text = str_replace('[quote]', '<table><tr><td class="quote-news">', $text);
	$text = str_replace('[/quote]', '</td></tr></table>', $text);
	$text = str_replace('[left]', '<div align="left">', $text);
	$text = str_replace('[/left]', '</div>', $text);
	$text = str_replace('[center]', '<div align="center">', $text);
	$text = str_replace('[/center]', '</div>', $text);
	$text = str_replace('[small]', '<font size="-2">', $text);
	$text = str_replace('[/small]', '</font>', $text);
	$text = str_replace('[right]', '<div align="right">', $text);
	$text = str_replace('[/right]', '</div>', $text);
	$text = preg_replace("/\[imageleft\](.*?)\[\/image\]/si", "<img border='0' src='\\1' class='leftimg'>", $text);
	$text = str_replace('[image]', '<img border="0" src="', $text);
	$text = str_replace('[/image]', '">', $text);
	$text = str_replace('[list]', '<ul>', $text);
	$text = str_replace('[list=x]', '<ol>', $text);
	$text = str_replace('[*]', '<li>', $text);
	$text = str_replace('[/list]', '</ul>', $text);
	$text = str_replace('[/list=x]', '</ol>', $text);
	$text = eregi_replace("\\[img\\]([a-z0-9\\./\\-]+)\\[/img\\]", "<img border=0 src='/user_data/\\1'>", $text);
	$text = eregi_replace("\\[imgleft\\]([a-z0-9\\./\\-]+)\\[/img\\]", "<div  style='float: left; padding-top: 5px; padding-right: 15px; padding-bottom: 10px; padding-left: 0px;'><img border='0' src='/user_data/\\1'></div>", $text);
	$text = eregi_replace("\\[imgprev\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><img border=0 src='/user_data/\\1/thumb/\\2' ALT='Нажмите, чтобы увеличить'></a>",$text);
	$text = eregi_replace("\\[imgprevleft\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><div  style='float: left; padding-top: 5px; padding-right: 15px; padding-bottom: 10px; padding-left: 0px;'><img border='0' src='/user_data/\\1/thumb/\\2' ALT='Нажмите, чтобы увеличить'></div></a>",$text);
	$text = str_replace("\n", '<br>', $text);
	$tmp = explode(' ',$text);

	for($i=0;$i<count($tmp);$i++) {
		if(eregi(':([a-z0-9_]+):', $tmp[$i], $ok)) {
			if(is_file('_imgs/smiles/'.$ok[1].'.gif'))
		$text=str_replace(':'.$ok[1].':', '<img src="_imgs/smiles/'.$ok[1].'.gif"> ', $text);
		}
	}
	echo ($text);
	//	return true;
}

function ParseNews3($text) {
        $text = stripslashes($text);
        $text = strip_tags($text, '<a><div><table><tr><td><strike><embed><object>');

	if (preg_match_all('#(.*)\[dialog=".*"\](.*)\[/dialog\]#Usi', $text, $t)) {
		for($i = 0; $i < count($t[0]); $i++) {
			$tmp = '';
			preg_match_all('#\[dialog="(.*?)"\]#si', $t[0][$i], $t1);
			$tmp .= $t[1][$i];
			$tmp .= '<div class="quote"><img height="64" width="62" src="http://www.tzpolice.ru/i/avatar/'.$t1[1][0].'" class="dialog">';
			$tmp .= preg_replace('#\[(.*)\] private \[(.*?)\]#', '<b>[${1}] <font color="red">private [${2}]</font></b>', $t[2][$i]);
			$tmp .= '<div class="clear"></div></div>';
			$pos1=strpos($text,$t[0][$i]);
			$pos2=strlen($t[0][$i]);
			$text=substr($text,0,$pos1).$tmp.substr($text,$pos1+$pos2);
		}
	}
	$text = preg_replace("/\[log\](.*?)\[\/log\]/si", "<b><font color='blue'>\\1</font> [<a href='#; return false' onclick=\"ClBrd2('\\1')\" alt=\"Скопировать в буфер обмена\">скопировать</a> / <a href='#; return false' onclick=\"LogWin('\\1')\" alt='Просмотреть бой'>просмотреть</a>]</b>", $text);
	$text = preg_replace("/\[url=([\w]+?:\/\/.*?)\](.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url\]([\w]+?:\/\/.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\1</a>", $text);
	$text = preg_replace("/\[url\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\1</a>", $text);
	$text = preg_replace("/\[color=(\#[0-9A-F]{6}|[a-z]+)\]/si", "<font color='\\1'>", $text);
	$text = str_replace('[/color]', '</font>', $text);
	$text = preg_replace("/\[pers nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<b>\\1</b> [\\2]<img style='vertical-align:text-bottom' src='/_imgs/pro/i\\3.gif' border='0'>", $text);
	$text = preg_replace("/\[pers clan=(.*?) nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='/_imgs/clans/\\1.gif' alt='\\1' border='0' height='16' width='28'><b>\\2</b> [\\3]<img style='vertical-align:text-bottom' src='/_imgs/pro/i\\4.gif' border='0'>", $text);
	$text = preg_replace("/\[clan=(.*?)\]/si", "<img src='/_imgs/clans/\\1.gif' alt='\\1' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='/_imgs/pro/i\\1.gif' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+) nick=([0-9A-Za-z *\-]+)\]/si", "<img style='vertical-align:text-bottom' src='/_imgs/pro/i\\1.gif' border='0'>", $text);
	$text = str_replace('[b]', '<b>', $text);
	$text = str_replace('[/b]', '</b>', $text);
	$text = str_replace('[u]', '<u>', $text);
	$text = str_replace('[/u]', '</u>', $text);
	$text = str_replace('[i]', '<i>', $text);
	$text = str_replace('[/i]', '</i>', $text);
	$text = str_replace('[quote]', '<table><tr><td class="quote-news">', $text);
	$text = str_replace('[/quote]', '</td></tr></table>', $text);
	$text = str_replace('[left]', '<div align="left">', $text);
	$text = str_replace('[/left]', '</div>', $text);
	$text = str_replace('[center]', '<div align="center">', $text);
	$text = str_replace('[/center]', '</div>', $text);
	$text = str_replace('[small]', '<font size="-2">', $text);
	$text = str_replace('[/small]', '</font>', $text);
	$text = str_replace('[right]', '<div align="right">', $text);
	$text = str_replace('[/right]', '</div>', $text);
	$text = preg_replace("/\[imageleft\](.*?)\[\/image\]/si", "<img border='0' src='\\1' class='leftimg'>", $text);
	$text = str_replace('[image]', '<img border="0" src="', $text);
	$text = str_replace('[/image]', '">', $text);
	$text = str_replace('[list]', '<ul>', $text);
	$text = str_replace('[list=x]', '<ol>', $text);
	$text = str_replace('[*]', '<li>', $text);
	$text = str_replace('[/list]', '</ul>', $text);
	$text = str_replace('[/list=x]', '</ol>', $text);
	$text = eregi_replace("\\[img\\]([a-z0-9\\./\\-]+)\\[/img\\]","<img border=0 src='/user_data/\\1'>",$text);
	$text = eregi_replace("\\[imgprev\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><img border=0 src='/user_data/\\1/thumb/\\2' ALT='Нажмите, чтобы увеличить'></a>",$text);
	$text = str_replace("\n", '<br>', $text);
	$tmp = explode(' ',$text);

	for($i=0;$i<count($tmp);$i++) {
		if(eregi(':([a-z0-9_]+):', $tmp[$i], $ok)) {
			if(is_file('_imgs/smiles/'.$ok[1].'.gif')) {
  				$text = str_replace(':'.$ok[1].':', '<img src="/_imgs/smiles/'.$ok[1].'.gif"> ',$text);
  			}
		}
	}
	return ($text);
}

function GetClan($source) {
	if(strlen($source)>2) {
		return '<img src="http://www.timezero.ru/i/clans/'.$source.'.gif" border=0>';
	} else {		return ' ';
	}
}

function GetUser($id,$nick,$admin) {
	if(AuthStatus==1 && (abs(AccessLevel) & AccessUsers))  {
		return '<b><a href="?act=user_info&UserId='.$id.'" target="_blank">'.$nick.'</a></b> ';
	} else {
		return '<b>'.$nick.'</b> ';
	}
}

function MakePreview($src,$tgt,$Wmax,$Hmax,$type,$name) {
	if(file_exists($src)) {
		if(@$type=='jpg') $im=imageCreateFromJpeg($src);
		if(@$type=='gif') $im=imageCreateFromGif($src);
		if(@$type=='png') $im=imageCreateFromPng($src);
		$width=imageSX($im);
		$height=imageSY($im);
		if($width>$Wmax) {
			$dw=$Wmax;
			$dh=floor($dw*$height/$width);
			$height=$dh;
			$width=$dw;
		}
		if($height>$Hmax) {
			$dh=$Hmax;
			$dw=floor($dh*$width/$height);
		}
		$width=imageSX($im);
		$height=imageSY($im);
		$gdinfo=gd_info();
		$gdver=$gdinfo['GD Version'];
		$s=strpos($gdver,"(");
		$gdver=substr($gdver,$s+1,1);
		if($gdver!=2 || $type=='gif') {
			$im2=imagecreate($dw, $dh);
			imageCopyResized($im2,$im,0,0,0,0,$dw,$dh,$width,$height);
		} else {
			$im2=imagecreatetruecolor($dw,$dh);
			imageCopyResampled($im2,$im,0,0,0,0,$dw,$dh,$width,$height);
		}
		$outfile = $tgt.'/'.$name.'.'.$type;
		if($type=='jpg') imageJpeg($im2, $outfile, 80);
		if($type=='gif' || $type=='png') imagepng($im2, $outfile);
		imageDestroy($im);
		imageDestroy($im2);
	} else {
		return false;
	}
}

function MakeThumb($src,$tgt,$Wmax,$Hmax,$type,$name) {
	if(file_exists($src)) {
		if(@$type=='jpg') $im=imageCreateFromJpeg($src);
		if(@$type=='gif') $im=imageCreateFromGif($src);
		if(@$type=='png') $im=imageCreateFromPng($src);
		$width=imageSX($im);
		$height=imageSY($im);
		if($width>$Wmax) {
			$dw=$Wmax;
			$dh=floor($dw*$height/$width);
			$height=$dh;
			$width=$dw;
 		}
        if($height>$Hmax) {
			$dh=$Hmax;
			$dw=floor($dh*$width/$height);
		}
		$width=imageSX($im);
		$height=imageSY($im);
		$gdinfo=gd_info();
		$gdver=$gdinfo['GD Version'];
		$s=strpos($gdver,"(");
		$gdver=substr($gdver,$s+1,1);
		$placement_y = round(($Hmax-$dh)/2);
		$placement_x = round(($Wmax-$dw)/2);
		if($gdver!=2 || $type=='gif') {
			$im2=imagecreate($Wmax, $Hmax);
			imageCopyResized($im2,$im,$placement_x,$placement_y,0,0,$dw,$dh,$width,$height);
		} else {
			$im2=imagecreatetruecolor($Wmax, $Hmax);
			imageCopyResampled($im2,$im,$placement_x,$placement_y,0,0,$dw,$dh,$width,$height);
		}
        $outfile = $tgt.'/'.$name.'.jpg';
        imageJpeg($im2, $outfile, 51);
        imageDestroy($im);
        imageDestroy($im2);
	} else {
		return false;
	}
}


function ShowPages($CurPage,$TotalPages,$ShowMax,$QueryStr) {
	$PrevList=floor(($CurPage-1)/$ShowMax)*$ShowMax;
	$NextList=$PrevList+$ShowMax+1;
	if($PrevList>=$ShowMax*2) echo '<a href="?'.$QueryStr.'&p=1" title="В самое начало">«</a> ';
	if($PrevList>0) echo '<a href="?'.$QueryStr.'&p='.$PrevList.'"  title="Предыдущие '.$ShowMax.' страниц">…</a> ';
	for($i=$PrevList+1;$i<=$PrevList+$ShowMax;$i++) if($i<=$TotalPages) {
		if($i==$CurPage) echo '<u>'.$i.'</u> ';
		else echo '<a href="?'.$QueryStr.'&p='.$i.'">'.$i.'</a> ';
	}
	if($NextList<=$TotalPages) echo '<a href="?'.$QueryStr.'&p='.$NextList.'"  title="Следующие '.$ShowMax.' страниц">…</a> ';
	if($CurPage<$TotalPages) echo '<a href="?'.$QueryStr.'&p='.$TotalPages.'" title="В самый конец">»</a>';
}

function ShowPages2($CurPage,$TotalPages,$ShowMax,$QueryStr,$prefix) {
	$PrevList=floor(($CurPage-1)/$ShowMax)*$ShowMax;
	$NextList=$PrevList+$ShowMax+1;
	if($PrevList>=$ShowMax*2) echo '<a href="?'.$QueryStr.'&p'.$prefix.'=1" title="В самое начало">«</a> ';
	if($PrevList>0) echo '<a href="?'.$QueryStr.'&p'.$prefix.'='.$PrevList.'"  title="Предыдущие '.$ShowMax.' страниц">…</a> ';
	for($i=$PrevList+1; $i<=$PrevList+$ShowMax; $i++) {
		if($i<=$TotalPages) {
			if($i==$CurPage) echo '<u>'.$i.'</u> ';
			else echo '<a href="?'.$QueryStr.'&p'.$prefix.'='.$i.'">'.$i.'</a> ';
		}
	}
	if($NextList<=$TotalPages) echo '<a href="?'.$QueryStr.'&p'.$prefix.'='.$NextList.'"  title="Следующие '.$ShowMax.' страниц">…</a> ';
	if($CurPage<$TotalPages) echo '<a href="?'.$QueryStr.'&p'.$prefix.'='.$TotalPages.'" title="В самый конец">»</a>';
}

function tz_users_update($userinfo) {
	if($userinfo['clan'] != '') {
		$sSQL = 'SELECT `id` FROM `tzpolice_tz_clans` WHERE `name` = \''.$userinfo['clan'].'\'';
		$result = mysql_query($sSQL);
		if(mysql_num_rows($result)>0) {
			$d = mysql_fetch_assoc($result);
			$userclan = $d['id'];
		} else {
			$sSQL = 'INSERT INTO `tzpolice_tz_clans` SET `name` = \''.$userinfo['clan'].'\'';
			mysql_query($sSQL);
			$userclan = mysql_insert_id();
		}
	} else {
		$userclan='0';
	}
	$SQL = 'SELECT `id` FROM `tzpolice_tz_users` WHERE `name`=\''.$userinfo['login'].'\'';
	$r = mysql_query($SQL);
	$set = '`pro` = \''.$userinfo['pro'].'\', `clan_id` = \''.$userclan.'\', `level` = \''.$userinfo['level'].'\', `sex` = \''.$userinfo['man'].'\', `upd_time` = \''.time().'\'';
	if(mysql_num_rows($r)>0) {
		$query = 'UPDATE `tzpolice_tz_users` SET '.$set.' WHERE `name`=\''.$userinfo['login'].'\';';
	} else {
		$set .= ', `name` = \''.$userinfo['login'].'\'';
		$query = 'INSERT INTO `tzpolice_tz_users` SET '.$set;
	}
	mysql_query($query);
}


?>