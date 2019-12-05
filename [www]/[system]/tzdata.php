<?php
if(!defined('CONF')) die('Wood ;)');

#админ группа на сайте
$adminGroup = 100;

$st_res = array('Venom'=>0,'Polymers'=>0,'Organic'=>0,'Precious metals'=>0,'Radioactive materials'=>0,'Gems'=>0,'Metals'=>0,'Silicon'=>0);

$ranks = array("0","20","60","120","250","600","1100","1800","2500","3200","4000","5000","6000","7200","100000","15000","25000","1000000");
$pve_ranks = array("0","250","1000","5000","25000","100000","500000","1000000","2000000","4000000","10000000");

$profs = array("", "Корсар", "Сталкер", "Старатель", "Инженер", "Наемник", "Торговец",
"Полицейский.Патрульный.","Полицейский.Штурмовик.","Полицейский.Специалист.",
"Журналист","Чиновник","Псионик","Каторжник","Пси-Кинет","Пси-медиум","Пси-Лидер","Полиморф","","","","","","","","",
"Грузовой робот","Десантный робот","Боевой робот","", "Дилер");
$rank_names = array("Рядовой","Рядовой","Младший сержант","Сержант","Старший сержант",
"Младший лейтенант","Лейтенант","Старший лейтенант",
"Капитан","Майор","Подполковник","Полковник","Генерал-майор","Генерал-лейтенант","Генерал-полковник",
"Маршал","Командор","Нету такого","Задрот");
$pverank_names = array("Странник","Странник","Крысолов","Следопыт","Охотник",
"Зверобой","Потрошитель","Истребитель","Гроза мутантов","Вивисектор","Легенда пустошей","Колобок");

$servers = array('Terra Prima','Terra Prima','Archipelago');

$battleside = array('D'=>'Шахта','A'=>'Поверхность, трава','B'=>'Поверхность, глина','C'=>'Поверхность, пустыня','E'=>'Подземелье','F'=>'Поверхность, снег','H'=>'Поверхность, снег');

$fractionslist = Array('1px','Invasion','RANGERS');

$noTZcounter = Array('news','deals','law_request','law_results','compens','compens2','prison_stats','n_comm','prisoned','news_search','pers_request','black','cops_depts','prison_rating','pers_request','news_search');

$policeclans = Array('police','Military Police','Police Academy','Financial Academy');

$CommentsPerPage = 15;
$NewsPP = 15;
$ThreadsPP = 15;
$RepliesPP = 20;
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

###################### Police Academy Posts #############
//$post_names[6] = 'ОМ. Vault';
$post_names[2] = 'ОМ. Оазис';
$post_names[1] = 'ОМ. Москва';
$post_names[7] = 'ОМ. Форум';
//$post_names[3] = 'БО. Москва';
//$post_names[4] = 'БО. Оазис';
//$post_names[5] = 'БО. Нева';

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


?>