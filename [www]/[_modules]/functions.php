<?php
#error_reporting(E_ALL);
require('/home/sites/police/www/_modules/mysql.php');

# ���������� ����������
require_once('/home/sites/police/dbconn/sql.php');
//sql::connect();

#��� ��� ���� � ���� ��������. ��� � ��������� ����.
$ranks = array("0","20","60","120","250","600","1100","1800","2500","3200","4000","5000","6000","7200","10000","15000","30000","50000","1000000");
$ranks_name = array("�������","������� �������","�������","������� �������","������� ���������","���������","������� ���������","�������","�����","������������","���������","�������-�����","�������-���������","�������-���������","������","��������","��������������","----","----");

############## vars #################
$CommentsPerPage=15;
$NewsPP=15;
$ThreadsPP=15;
$RepliesPP=20;
$mess['CantAddComment']='<h2>����� �������� �����������, ��� ���������� �������������� �� �����</h2>';
$mess['CantAddReply']='<h2>����� �������� �����, ��� ���������� �������������� �� �����</h2>';
$mess['NoComments']='<h2>������������ ���</h2>';
$mess['NewsNotFound']='<h2>��������� �������� �� ������</h2>';
$mess['AccessDenied']='<h2>� ��� ��� ���� ������� � ����� �������</h2>';
$mess['UserNotFound']='<h2>������������ �� ������</h2>';
$mess['CantAddNews']='<h2>����� �������� �������, �� ������ �������������� �� �����</h2>';
$mess['WantRegister']='<div align=center>���� �� ��� �� ����������������, <a href="?act=register">�����������������</a></div>';

$SecName['manuals'] = '�������';
$SecName['manuals2'] = '������';
$SecName['manuals3'] = '��������';
$SecName['mine_maps'] = '����� ����';
$SecName['prize'] = '������� �� ��';
$SecName['art']='����������';
$SecName['law']='����������� ���������';
$SecName['interview']='��������';
$SecName['staff']='������, ������';
$SecName['hotkeys']='������� ������� � ��';
$SecName['exp_tbl']='������� �����';
$SecName['pills']='�������';
$SecName['contacts']='��������';
$SecName['services']='������';
$SecName['dealer']='����������� ������';
$SecName['faq']='����� ���������� �������';
$SecName['keks']='����';
$SecName['misc']='������';
$SecName['join']='����� � �������';
$SecName['copsmanual']='����������� ������������';
$SecName['other']='TEMP';
$SecName['orders']='����������� ���������';
$SecName['pa']='����������� ��������';
$SecName['gallery']='���������� ��� �����������';
$SecName['legenda']='������� �����';
$SecName['ustav']='����� �����';
$SecName['ok']='�� Help';
//$SecName['misc']='����������� ��������';
/*
$forum['general']['name']='�����';
$forum['general']['desc']='���������� ����� ������������� �������� =)';
$forum['moaning']['name']='������ �� �����������';
$forum['moaning']['desc']='��� ������� ������. ������������ ����� �� ������������� �������� �������.';
$forum['suggestions']['name']='����������� �� �����';
$forum['suggestions']['desc']='����������� �� ��������� �����, ��������� �� �������';
$forum['mate']['name']='������';
$forum['mate']['desc']='���������� � �������, � ��� �� ����-������ � ���. ���� ����������� ���������� � ������������� ��������� ����';
$forum['fastflood']['name']='����-����';
$forum['fastflood']['desc']='���������� ��� FastShadow. �����, ����.������� =)';
*/
$forum['main']['name']='�����';
$forum['main']['desc']='���������� ����� ���';
$forum['main']['restr']=0;
$forum['academy']['name']='����������� ��������';
$forum['academy']['desc']='����������� ��������� � ���������� ����������� ��������';
$forum['academy']['restr']=0;
$forum['orders']['name']='�������';
$forum['orders']['desc']='������� �� ������� � ����������� ��������';
$forum['orders']['restr']=0;
$forum['reports']['name']='�������';
$forum['reports']['desc']='������� ������� �������';
$forum['reports']['restr']=0;
$forum['client']['name']='������';
$forum['client']['desc']='������� � ���������� � ������������ �������';
$forum['client']['restr']=0;
//$forum['work']['name']='�������';
//$forum['work']['desc']='������� ���. ������';
//$forum['work']['restr']=0;
//$forum['elite']['name']='����� ��� �����������';
//$forum['elite']['desc']='������ ���� � ������������� ����� ���. <b>Top Secret!</b>';
//$forum['elite']['nm']='elite';
//$forum['elite']['restr']=50;
$forum['secret']['name']='�������� �����';
$forum['secret']['desc']='������ ���� � ������������� ����� ���. <b>Top Secret!</b>';
$forum['secret']['nm']='secret';
$forum['secret']['restr']=50;
$forum['ceo']['name']='����� ����������� �������';
$forum['ceo']['desc']='������ ���� � ����������� ������� � ���������� ������� ��. <b>Top Secret!</b>';
$forum['ceo']['nm']='ceo';
$forum['ceo']['restr']=50;
$forum['ok']['name']='����� ������';
$forum['ok']['desc']='�������� ����� ��';
$forum['ok']['nm']='ok';
$forum['ok']['restr']=50;
$forum['inform']['name']='����� ��������������';
$forum['inform']['desc']='�������� ����� ��';
$forum['inform']['nm']='inform';
$forum['inform']['restr']=50;
$forum['inet']['name']='����� ������� ����� �� �������� ��������';
$forum['inet']['desc']='������ ���������������';
$forum['inet']['nm']='inet';
$forum['inet']['restr']=50;
$forum['investigations']['name']='����� ������ �������������';
$forum['investigations']['desc']='������ ���������������';
$forum['investigations']['nm']='investigations';
$forum['investigations']['restr']=50;
$forum['military']['name']='�������� ����� Military Police';
$forum['military']['desc']='������ ���������������';
$forum['military']['nm']='military';
$forum['military']['restr']=50;
$forum['prokachki']['name']='�������� ����� ������ �������� ��������';
$forum['prokachki']['desc']='������ ���������������';
$forum['prokachki']['nm']='prokachki';
$forum['prokachki']['restr']=50;
$forum['obep']['name']='�������� ����� ����';
$forum['obep']['desc']='������ ���������������';
$forum['obep']['nm']='obep';
$forum['obep']['restr']=50;
$forum['commerce']['name']='�������� ����� ������������� ������';
$forum['commerce']['desc']='������ ���������������';
$forum['commerce']['nm']='commerce';
$forum['commerce']['restr']=50;


#############freelancers##################
$freelance['main']['name']='�����';
$freelance['main']['desc']='���������� ����� ���';
$freelance['main']['restr']=0;
$freelance['job']['name']='�������';
$freelance['job']['desc']='����� �������, ������ �� �����������';
$freelance['job']['restr']=0;
$freelance['bugs']['name']='������';
$freelance['bugs']['desc']='������������ ����';
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
$dept[1] = '����';
$dept[2] = '��';
$dept[3] = '����';


$crime[1] = '5.2. ��������';
$crime[2] = '1.7.3. (���. 1.7.2) ����������� ���';
$crime[3] = '1.7.2. (��� 1.7.1)  ��������/����/����/����������������';
$crime[4] = '6.2.1. �����';
$crime[5] = '����� �4. �������� �� ������ �����';
$crime[6] = '7.2.2. ������';
$crime[7] = '5.1. ������ �����������';
$crime[8] = '����� �������������';
$crime[9] = '1.6.1 �������';
$crime[10] = '1.6.3. �������� �������';
$crime[11] = '������������� ����� ����';
$crime[12] = '������� �������������';
$crime[13] = '�������� � �����������';
$crime[14] = '6.3.1. ���������� ������';
$crime[15] = '10.8. ������������� �� �������';
$crime[16] = '1.5.2. ��� � ����������';
$crime[17] = '1.5.1. �������� � ����������';
$crime[18] = '1.2.1.6. ������� ���������� ���������� � ����';
$crime[19] = '1.2.3.6. ������� ���������� ������������ � ����';
$crime[20] = '1.2.4.8. ������� ������� ���� ������ ���� � ����';
$crime[21] = '1.3.3.5. ������� ���������� ������������ �� ������';
$crime[22] = '1.3.4.6. ������� ������� ���� ������ ���� �� ������';
$crime[23] = '1.4.3. ������������ ����';
$crime[24] = '1.4.5. ���������� ���������� � ����';
$crime[25] = '1.4.6. ��� � ����';
$crime[26] = '1.4.7. ������ � ����';
$crime[27] = '1.4.8. ���������� ������� � ����';
$crime[28] = '1.5.3. �������� �������';
$crime[29] = '1.5.4. ��� �������';
$crime[30] = '1.5.5. �������� � �������';
$crime[31] = '1.5.6. ��� � �������';
$crime[32] = '1.5.7. ����� �������� ������/�������� �������';
$crime[33] = '1.5.8. ��������/��� � ����. ������, ����. ����., ����� �������';
$crime[34] = '1.5.9. ���� ����������';
$crime[35] = '1.5.10. ��������/��� �� ������';
$crime[36] = '1.5.11. ���������� ���������� �� ������';
$crime[37] = '1.5.12. ������ �������� � ����� � ����������';
$crime[38] = '1.6.2. ������� � ����';
$crime[39] = '1.6.3. �������� �������';
$crime[40] = '1.6.4. ������� �� ������� � ����/������';
$crime[41] = '1.6.5. ������� �� ������� � ����';
$crime[42] = '7.2.3. ������������� ������������ �������� ������';
$crime[43] = '7.3.1. ������������� �������';
$crime[44] = '7.4.1. ��������� �������� ���������� ��������������';
$crime[45] = '7.5.1. �������������';
$crime[46] = '7.6.1. ������������ ����������� ����������';
$crime[47] = '7.7.1. ������������ ������';
$crime[48] = '6.4.1. ������� �������� ����� �������� ������ � �����';
$crime[49] = '39.1. ���� �������� ������ ���������';
$crime[50] = '39.2. ������������ ������� ���������';
$crime[51] = '2.1. ��������� ������';
$crime[52] = '2.2. ���� ������';
$crime[53] = '3.1. �������������� ������';
$crime[54] = '6.1. ������������� ���������� ��������� � ��������� �����';
$crime[56] = '4.3.1 ������� �������, �������, ������, �������� ���������';
$crime[55] = '---���---';
$crime[57] = '1.3.2.2. ����. ����. ������� (��� ������� ��� ���. ������)';
$crime[58] = '����� �4. ���������� ������ ���������';
$crime[59] = '1.7.4 ��������� ������ �� ������� ������ �1';
$crime[60] = '������� ���������';
$crime[61] = '5.2 �������� (���)';
$crime[62] = '6.5.1 �������� ������';
$crime[63] = '1.2.6. ����, ���, ���������';
$crime[64] = '1.4.4. ����� ������������� � �������';
$crime[65] = '1.5.2. ��������/ ����/ ����/ ����������������';
$crime[66] = '1.5.3. ����. ���';
$crime[67] = '1.5.4. ��������� ������';
$crime[68] = '1.2.7. ������ ����.';
$crime[69] = '1.4.4. ����� �������, �������������';
$crime[70] = '1.4.5. ����. ������. �������';
$crime[71] = '1.5.3. ����. ���';
$crime[72] = '1.5.4. ��������� ������';
$crime[73] = '4.3.1. ������� �������, �������, ������ ���������';
$crime[74] = '4.5.4. ������� �������, �������, ������, ������� ��';
$crime[75] = '����� � 4 ���������� ������ ���������';
$crime[76] = '5.2.1. ��������';
$crime[77] = '5.2.2. ������� ��������';
$crime[78] = '6.2.1. �����';
$crime[79] = '6.3.1. ��������� ������';
$crime[80] = '6.5.4. �������� ���������� ����������';
$crime[81] = '7.2.2. ������';
$crime[82] = '7.2.3. ������������� ������������ ������� �������� ������';
$crime[83] = '7.3.1. ��������������� �������� ������ ��������� ����������';
$crime[84] = '7.4.1. ������������ ���������� ���������� ��������������';
$crime[85] = '7.5.1. �������������';
$crime[86] = '7.6.1. ������������ ����������� ����������';
$crime[87] = '7.7.1. ������������ ������� ������ ������';
$crime[88] = '15.1.  ���� �������� ������ ���������';
$crime[89] = '6.1. ������������� ���������� ���������';
$crime[90] = '������� �������������';
$crime[91] = '--���-- �� 30 �����';
$crime[92] = '׸���� �����';
$crime[93] = '������� ���. �������';
$crime[94] = '1.2.4 ������ ����������� �������������.';
$crime[95] = '���';
$crime[96] = '1.2.5. ����, ������, ���������, �����.';
$crime[97] = '��������� ������ ��.';
$crime[98] = '������������ ���';

//��� ������ � ���������� �����
$crime_list[][76] = '5.2. ��������';
$crime_list[][96] = '1.2.5. ����, ������, ���������, �����';
$crime_list[][68] = '1.2.7. ������ ����.';
//$crime_list[][64] = '1.4.4. ����� ������������� � �������';
//$crime_list[][70] = '1.4.5. ����. ������. �������';
$crime_list[][71] = '1.5.3. ����. ���';
$crime_list[][72] = '1.5.4. ��������� ������';
$crime_list[][77] = '5.2. ������� ��������(�������)';
$crime_list[][75] = '����� � 4 ���������� ������ ���������';
$crime_list[][73] = '4.3.1. ������� �������, �������, ������ ���������';
$crime_list[][78] = '6.2. �������� �������';
$crime_list[][79] = '6.3. ������� ����������� ���.������';
//$crime_list[][62] = '6.5.1. �������� ������';
$crime_list[][80] = '6.4. ��������';

$crime_list[][81] = '7.2.2. ������';
//$crime_list[][82] = '7.2.3. ������������� ������������ ������� �������� ������';
//$crime_list[][83] = '7.3.1. ��������������� �������� ������ ��������� ����������';
//$crime_list[][84] = '7.4.1. ������������ ���������� ���������� ��������������';
//$crime_list[][85] = '7.5.1. �������������';
$crime_list[][88] = '15.1.  ���� �������� ������ ���������';
$crime_list[][90] = '������� �������������';
$crime_list[][91] = '--���-- �� 30 �����';
$crime_list[][92] = '׸���� �����';
$crime_list[][93] = '������� ���. �������';
$crime_list[][94] = '1.2.4 ������ ����������� �������������.';
$crime_list[][95] = '���';
$crime_list[][97] = '��������� ������ ��.';
$crime_list[][98] = '������������ ���';


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
$pers_cops[] = 'calypso';
$pers_cops[] = 'V_calypso';
$pers_cops[] = '����';
$pers_cops[] = '������';

$pers_adm[] = '����';
$pers_adm[] = '������';

$pers_adm_s[] = '����';
$pers_adm_s[] = '������';


###################### Police Academy Posts #############
//$post_names[6] = '��. Vault';
$post_names[2] = '��. �����';
$post_names[1] = '��. ������';
$post_names[7] = '��. �����';
//$post_names[3] = '��. ������';
//$post_names[4] = '��. �����';
//$post_names[5] = '��. ����';
###################### Pages without counter ############
$noTZcounter[] = 'n�ws'; //�� ��������������!!! �� ������� �������� ���� �� ������!!! ��� ��� ������ - ���� ������ ��������!!!
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
$compens_cops[] = '������';
$compens_cops[] = 'RAWarrior';
$compens_cops[] = '255���';

$compens_cops2[] = '������';
$compens_cops2[] = 'RAWarrior';
$compens_cops2[] = '255���';

$comm_tpl_cats[1] = '��';
$comm_tpl_cats[2] = '��';
$comm_tpl_cats[3] = '����';
$comm_tpl_cats[4] = '���';
$comm_tpl_cats[5] = '�������';
$comm_tpl_cats[6] = '���';
//$comm_tpl_cats[1] = '';

$manuals[] = "���������";
$manuals[] = "���������";
$manuals[] = "�������� ���������";
$manuals[] = "���������";
$manuals[] = "������� �����";
$manuals[] = "���������� ���";
$manuals[] = "�������";
$manuals[] = "������";
$manuals[] = "����������";

$quests[] = "�� 12 ������";
$quests[] = "12+ ������";
$quests[] = "��������� ���������";
$quests[] = "�������������";
$quests[] = "����������������";
$quests[] = "����������";

############### ���� ����������� ��� ����������� �� ����� #############
// !!!! � ������ �������� ����� !!!!
$DisabledRegisterNames=array('terminal pa', 'terminal 00', 'terminal 01', 'terminal 02', 'terminal 03', 'terminal 04', 'terminal 05', 'terminal 06', 'hony', 'big brother', 'hate ati', '��������� ��', '��������� ��', '��������� ��', '����������', 'terminal police', '�������', '���������', 'mp 00', 'mp 01', 'mp 02', 'mp 03', 'mp 04', 'mp 05', 'mp 06', 'mp 07', 'mp 08', 'mp 09', 'mp 10');

#����� ����.
$prof_alt[0] = '��� ���������';
$prof_alt[1] = '������';
$prof_alt[2] = '�������';
$prof_alt[3] = '������';
$prof_alt[4] = '�������';
$prof_alt[5] = '�������';
$prof_alt[6] = '��������';
$prof_alt[7] = '���. ����������';
$prof_alt[8] = '���. ���������';
$prof_alt[9] = '���. ����������';
$prof_alt[10] = '���������';
$prof_alt[11] = '��������';
$prof_alt[12] = '�������';
$prof_alt[13] = '���������';
$prof_alt[14] = '���-�������';
$prof_alt[15] = '���-������';
$prof_alt[16] = '���-�����';
$prof_alt[26] = '�����';
$prof_alt[27] = '�����';
$prof_alt[28] = '�����';
$prof_alt[30] = '�����';


//������� ������� �� ���� ����� �� �������
#����� ������� ����� ���
function TZOnline() {

	$query = 'SELECT `value` FROM `const` WHERE `script` = \'tzonline\' AND `name` = \'updated\' LIMIT 1;';
	$res = mysql_query($query);
	$rs = mysql_fetch_array($res);
	$now = time()-300;
	if ($rs['value'] < $now) {
		$html = implode ('', file ('https://www.timezero.ru/'));
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
	$upper = '�����Ũ��������������������������';
	$lower = '��������������������������������';
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




#��� ������ ����� ��� ����� �������� ��� �����...
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
	//	��� ��������� || ����� ����������
	$message = "\n$who || ".$nick.' || '.$message."\n";
	// �������� ������� ��������� - ������ ������ ������� ������ ����
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
		$returntxt .= '<CENTER>������ ���</CENTER>';
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
		echo '<a href="javascript:{}" onclick="opencomments(\''.$QueryStr.'\', 1)" title="� ����� ������"><< </a> ';
	}
	if($PrevList>0) {
		echo '<a href="javascript:{}" onclick="opencomments(\''.$QueryStr.'\', \''.$PrevList.'\')"  title="���������� '.$ShowMax.' �������">...</a> ';
	}
	for($i=$PrevList+1;$i<=$PrevList+$ShowMax;$i++) if($i<=$TotalPages) {
		if($i==$CurPage) {			echo '<u>'.$i.'</u> ';
		} else {			echo '<a href="javascript:{}" onclick="opencomments(\''.$QueryStr.'\', '.$i.')">'.$i.'</a> ';
		}
	}
	if($NextList<=$TotalPages) {
		echo '<a href="javascript:{}" onclick="opencomments(\''.$QueryStr.'\', \''.$NextList.'\')"  title="��������� '.$ShowMax.' �������">...</a> ';
	}
	if($CurPage<$TotalPages) {
		echo '<a href="javascript:{}" onclick="opencomments(\''.$QueryStr.'\', \''.$TotalPages.'\')" title="� ����� �����"> >></a>';
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

//������� ������� �������� ������
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
    if($enforce < 1) return locateUser($login);
	$info = file_get_contents("https://www.timezero.ru/info.pl?".urlencode($login));
	$info = preg_replace("#\n|\t|\r#","",$info);
    preg_match("#<!--START-->(.*)<!--END-->#i",$info,$uinfo);
    $info = $uinfo[1];

    if($info) {
        preg_match("#<!--swf start-->(.*)<!--swf end-->#i",$info,$uparams);
        preg_match("#<!--characteristics start-->(.*)<!--characteristics end-->#i",$info,$ustats);
       	preg_match_all("#src=\"/i/([^\"]+).gif\"#i",$uparams[1],$ucl);
       	preg_match("#<b>([^&]+)&nbsp;\[([0-9]+)\]</b>#i",$uparams[1],$ulogin);
       	$uclorpro = ($ucl[1][1])?$ucl[1][1]:$ucl[1][0];
        preg_match("#i([0-9]{1,2})(w?)#",$uclorpro,$prof);
        preg_match("#offline#i",$info,$onl);

        $funcerror['login'] = mb_convert_encoding($ulogin[1],"cp1251","UTF-8") ? mb_convert_encoding($ulogin[1],"cp1251","UTF-8") : $ulogin[1];
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

#���� ���� �� ����� �� ������� ��������
function locateUser($login,$qfield='login') {	$login = addslashes(trim($login));
	if($qfield == 'clan') {
		$request = mysql_query("SELECT * FROM locator WHERE `clan` = '$login'");
	} else {		$request = mysql_query("SELECT * FROM locator WHERE `login` = '$login'");
	}
	$data = false;
	if(mysql_num_rows($request) > 0) {
		
		$data = mysql_fetch_assoc($request);
		$data['level'] = $data['lvl'];
		$data['man'] = $data['gender'];
		$data['gen'] = $data['gender'];
		
	} elseif ($qfield == 'login') {
		
		$data['login'] = $login;
		
	}
	$data['lvl'] = ($data['lvl'])?$data['lvl']:$data['level'];
	$data['system'] = "SELECT * FROM locator WHERE `$qfield` = '$login' LIMIT 1";

	return $data;
}

function formatUser($u) {	
	
	if($u[lvl] > 0) {		
		$return = ($u[clan])?"<img src='https://timezero.ru/i/clans/".$u[clan].".gif' border=0 style='vertical-align: text-bottom;'>":"";
		$return .= "<b>".$u['login']."</b> [".$u[lvl]."]";
		$return .= "<img src='https://timezero.ru/i/i".$u[pro].".gif' border=0 style='vertical-align: text-bottom;'>";
		$return .= "<img src='https://timezero.ru/i/rank/".$u[pvprank].".gif' border=0 style='vertical-align: text-bottom;'>";
		return $return;    
	} else {    	
		return "<b>".$u['login']."</b>";
    }

}
function TZConn($login, $enforce=0,$leave) {
	
	if(!$leave) return locateUser($login);

	$reserve_url = 'http://stalkerz.ru/ajax/radar.ajax.php?police=thisisdegradesecurecode&nick='.urlencode(tzpd_strtolower($login));
	$reserve_params = array('clan','login','level','pro','gender','pvprank','pvpr','utime','server','addtime','location');

	$stalk_info = file_get_contents($reserve_url);
	if($stalk_info != '' && $stalk_info != 0){
		$stalk_info = explode(';',$stalk_info);
		foreach($stalk_info as $param){
			$param = explode(':', $param);
			if(in_array($param[0], $reserve_params)) $userInfo[$param[0]] = $param[1];
		}
	}
	#����, ���� ���� �� ����� ���
	$userInfo['login'] = ($userInfo['login'])?$userInfo['login']:$login;
	$userInfo['level'] = ($userInfo['level'])?$userInfo['level']:0;
	$userInfo['pro'] = ($userInfo['pro'])?$userInfo['pro']:0;
	$userInfo['gender'] = ($userInfo['gender'])?$userInfo['gender']:1;
	$userInfo['pvprank'] = ($userInfo['pvprank'])?$userInfo['pvprank']:1;

return $userInfo;
}

function getUserRank($rpoints) {
	global $ranks;
	foreach($ranks as $zvan => $c) {
		$nextrank = $ranks[$zvan+1];
		if($rpoints >= $c && $rpoints < $nextrank) {
        	return $zvan+1;
		}
	}
}

function TZConn_update_clan_members($clan, $cache_time = 86400){	$clan = addslashes(trim($clan));

	$result = mysql_query("SELECT `lastupdate` FROM `data_clans_cache` WHERE `clan_name`='$clan' LIMIT 1");
	$haveclan = mysql_num_rows($result);
	$row = mysql_fetch_assoc($result);

	if($haveclan < 1 || ($row['lastupdate'] < time()-$cache_time)) {
  		
		$url = 'https://www.timezero.ru/info.pl?clanxml='.urlencode(tzpd_strtolower($clan));
		$clanlistxml = file_get_contents($url, false, stream_context_create(array('https'=>array('timeout'=>10))));
		$clanlistxml = html_entity_decode($clanlistxml, ENT_QUOTES, 'UTF-8');
		
		$clanXML = new DomDocument();
		@$clanXML->loadXML($clanlistxml);

		if ($clanXML->getElementsByTagName('CLAN')->length > 0){
		    mysql_query("DELETE FROM `data_clan_members_cache` WHERE `clan_name`='$clan'");
		    if ($haveclan < 1){
				mysql_query("INSERT INTO `data_clans_cache` (`clan_name`, `lastupdate`) VALUES('$clan', '".time()."')");
		    }
		}
		if ($haveclan > 0){
			mysql_query("UPDATE `data_clans_cache` SET `lastupdate`='".time()."' WHERE `clan_name`='$clan'");
        }
		
		$clan_array = array();

		$memberTag = $clanXML->getElementsByTagName('USER');
		if ($memberTag->length > 0){
			for ($i=0;$i<$memberTag->length;$i++){
				
				$login = iconv("UTF-8", "windows-1251", $memberTag->item($i)->getAttribute('login'));
				$login = addslashes(htmlspecialchars(trim($login)));
				
				// ��������� ��������� �������� � ������� �����, �� �� ����� ��������� ���� �������.
				$querynoupdate = mysql_query("SELECT * FROM `locator_no_update` WHERE `login` = '$login' AND `update_type` = 'clanxml'");
				if(mysql_num_rows($querynoupdate) > 0) continue;

				// ��������� ��������� ������... ����� ������� ������� �� �������� � �������.
				$clan_array[] = $login;
				
				$lvl = $memberTag->item($i)->getAttribute('level');
				$lastlogin = $memberTag->item($i)->getAttribute('lastlogin');
				$rank_points = $memberTag->item($i)->getAttribute('rank_points');
				$pro = $memberTag->item($i)->getAttribute('pro');
				$pvprank = getUserRank($rank_points);
                $gender = $memberTag->item($i)->getAttribute('man');
                $server = $memberTag->item($i)->getAttribute('server');
                $server = ($server)?$server:1;
                $location = $memberTag->item($i)->getAttribute('location');
                $location = ($location)?$location:"0/0";

				$s1 = iconv("UTF-8", "windows-1251", $memberTag->item($i)->getAttribute('clan_s1'));
				$s2 = iconv("UTF-8", "windows-1251", $memberTag->item($i)->getAttribute('clan_s2'));
	      		
				mysql_query("INSERT INTO `data_clan_members_cache` (`clan_name`, `user_name`, `clan_s1`, `clan_s2`) VALUES('$clan', '$login', '$s1', '$s2')");

				$query = mysql_query("SELECT * FROM locator WHERE login = '$login' LIMIT 1");
				if(mysql_num_rows($query) > 0) {
					// � ������ ����� ���������� ����������� �� �����, �������� ������, ����� ���� �� ������ ����������� ����� ���������� ���� ��������.
					$row = mysql_fetch_array($query);
					
					if ($row['utime'] > $lastlogin) continue; // ������� ����� ������� ���� - ����� ����� ���������? ���������.

					mysql_query("UPDATE locator SET lvl='$lvl',clan='$clan',pro='$pro',pvpr='$rank_points',pvprank='$pvprank',utime='$lastlogin' WHERE login = '$login' LIMIT 1");

				} else {
					mysql_query("INSERT INTO locator (`id`,`addtime`,`utime`,`location`,`server`,`clan`,`login`,`lvl`,`pro`,`pvpr`,`pvprank`,`gender`) VALUES(NULL,'".time()."','$lastlogin','$location','$server','$clan','$login','$lvl','$pro','$rank_points','$pvprank','$gender')");
				}
			}
			

			// ������ ������� �� ���������� �� ����� ������, ������� �� ���������� ��� ������� ����� ����������.
			$query_clear = mysql_query("SELECT `id`, `login` FROM locator WHERE clan = '$clan'");
			while ($row = mysql_fetch_array($query_clear)) {
				if (!in_array($row['login'], $clan_array)) {
					// ��������� ��������� ����������� � ������, ��� ��� ������� � ������ �����, �� �� ����� ��������� ���� �������.
					$querynoupdate = mysql_query("SELECT * FROM `locator_no_update` WHERE `login` = '".$row['login']."' AND `update_type` = 'clanxml'");
					if(mysql_num_rows($querynoupdate) > 0) continue;
						
					// ��������, XML ��� �� ���������, � ����� � ������� ��� ���.
					// upd (� ������ ��� ������ �� �����������, � ������� 2018. ��������� �� ������ ���� ��� �� �������.)
					// ����� �� �������� ���������� ���� �������� ������������ ����� XML - �������� ���� ����� ����� "API".
					$user = TZConn2($row['login'], 1);
					// API ����� ���� ���������� - ��������� ���� ��������.
					if (!$user['login']) continue;
					
					//
					if (!isset($user['clan'])) {
						$user['clan'] = "";
					}
						
					mysql_query("UPDATE locator SET clan = '".$user['clan']."', utime = '".time()."' WHERE id = ".$row['id']);
				}
			}

		}
	}
}

function GetInfoFromApi($nick) {
	//$nick = (mb_convert_encoding($nick,"utf8","cp1251"))?mb_convert_encoding($nick,"utf8","cp1251"):$nick;
    $nick = str_replace(" ","%20",$nick);
    $url = "https://www.timezero.ru/info.pl?userxml=".mb_strtolower($nick,"cp1251");
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

		$userInfo = TZConn($nick);    // ����� ������ �� ��������, ��������� ���... ����.
		unset($userInfo['system']);   // ��-�� ����� ���� ��� �� ���������� � 11 ����. 

		if (sizeof($userInfo) > 0) {
    		// ������� ������� `data_cache` �������� ��������... �� ��� ��� ������ ������ ������ "����", ���������, �� ���������.
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
	//�� ���� ������ ���, �� � ������ �������� ����.
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


//� � ���� �� ���� �������(((
function ParseNews($buf, $AllowTags, $replaceBR=1) {
	$outttvar = '';
	//if($AllowTags==0) $buf = strip_tags($buf, '<b><i><u><div><wbr><strike><embed><object>');
	$buf = strip_tags($buf);
	//
	$buf = stripslashes($buf);
	if($replaceBR==1) $buf = str_replace("\n", "<br>", $buf);
	$FontColors1 = array('[red]', '[/red]', '[green]', '[/green]', '[blue]', '[/blue]');
	$FontColors2 = array('<font color=red>', '</font>', '<font color=green>', '</font>', '<font color=blue>', '</font>');
	$buf = str_replace($FontColors1,$FontColors2,$buf);
	$buf = str_replace('{MP_LIST}',mp_list(),$buf);
	$text = $buf;

	$text = preg_replace("/\[spoyler id=l(\d+) title=\'(.*?)\'\](.*?)/si", "<a href=\"javascript:{}\" onClick=\"javascript:sh('l\\1');\">\\2</a><div id=\"l\\1\" style=\"display:none\">\\3", $text);
	$text = preg_replace("/\[\/spoyler\]/si", "</div>", $text);

	$text = preg_replace("/\[log\](.*?)\[\/log\]/si", "<b><font color='blue'>\\1</font> [<a href='#; return false' onclick=\"ClBrd2('\\1')\" alt=\"����������� � ����� ������\">�����������</a> / <a href='#; return false' onclick=\"LogWin('\\1')\" alt='����������� ���'>�����������</a>]</b>", $text);
	$text = preg_replace("/\[pers nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<b>\\1</b> [\\2]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\3.gif' border='0'>", $text);
	$text = preg_replace("/\[pers clan=(.*?) nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/clans/\\1.gif' alt='\\1' border='0' height='16' width='28'><b>\\2</b> [\\3]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\4.gif' border='0'>", $text);
	$text = preg_replace("/\[clan=(.*?)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
	$text = preg_replace("/\[pro=([0-9w]+) nick=([0-9A-Za-z *\-]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
	$buf = $text;
	$buf = preg_replace("/\[prof\](.*?)\[\/prof\]/si","<img border=0 style='vertical-align:text-bottom' src='/_imgs/pro/\\1.gif'>",$buf);
	$buf = preg_replace("/\[clan\](.*?)\[\/clan\]/si","<img border=0 src='/_imgs/clans/\\1.gif'>",$buf);
	$buf = eregi_replace("\\[img\\]([a-z0-9\\./\\-]+)\\[/img\\]","<img border=0 src='/user_data/\\1'>",$buf);
	$buf = eregi_replace("\\[imgprev\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><img border=0 src='/user_data/\\1/thumb/\\2' ALT='�������, ����� ���������'></a>",$buf);
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
	//$text = strip_tags($text, '<a><div><table><tr><td><strike><script><object><param><embed><object>');
	$text = strip_tags($text, "<video>");
	//
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

	$text = preg_replace("/\[log\](.*?)\[\/log\]/si", "<b><font color='blue'>\\1</font> [<a href='#; return false' onclick=\"ClBrd2('\\1')\" alt=\"����������� � ����� ������\">�����������</a> / <a href='#; return false' onclick=\"LogWin('\\1')\" alt='����������� ���'>�����������</a>]</b>", $text);
	$text = preg_replace("/\[url=([\w]+?:\/\/.*?)\](.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url\]([\w]+?:\/\/.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\1</a>", $text);
	$text = preg_replace("/\[url\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\1</a>", $text);
	
	$text = preg_replace("/\[spoyler id=l(\d+) title=\'(.*?)\'\](.*?)/si", "<a href=\"javascript:{}\" onClick=\"javascript:sh('l\\1');\">\\2</a><div id=\"l\\1\" style=\"display:none\">\\3", $text);
	$text = preg_replace("/\[\/spoyler\]/si", "</div>", $text);

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
	$text = eregi_replace("\\[imgprev\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><img border=0 src='/user_data/\\1/thumb/\\2' ALT='�������, ����� ���������'></a>",$text);
	$text = eregi_replace("\\[imgprevleft\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><div  style='float: left; padding-top: 5px; padding-right: 15px; padding-bottom: 10px; padding-left: 0px;'><img border='0' src='/user_data/\\1/thumb/\\2' ALT='�������, ����� ���������'></div></a>",$text);
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
	//$text = strip_tags($text, '<a><div><table><tr><td><strike><script><strong><em><img><br><embed><object>');
	$text = strip_tags($text);
	//
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

	$text = preg_replace("/\[log\](.*?)\[\/log\]/si", "<b><font color='blue'>\\1</font> [<a href='#; return false' onclick=\"ClBrd2('\\1')\" alt=\"����������� � ����� ������\">�����������</a> / <a href='#; return false' onclick=\"LogWin('\\1')\" alt='����������� ���'>�����������</a>]</b>", $text);
	$text = preg_replace("/\[url=([\w]+?:\/\/.*?)\](.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url\]([\w]+?:\/\/.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\1</a>", $text);
	$text = preg_replace("/\[url\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\1</a>", $text);
	
	$text = preg_replace("/\[spoyler id=l(\d+) title=\'(.*?)\'\](.*?)/si", "<a href=\"javascript:{}\" onClick=\"javascript:sh('l\\1');\">\\2</a><div id=\"l\\1\" style=\"display:none\">\\3", $text);
	$text = preg_replace("/\[\/spoyler\]/si", "</div>", $text);

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
	$text = eregi_replace("\\[imgprev\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><img border=0 src='/user_data/\\1/thumb/\\2' ALT='�������, ����� ���������'></a>",$text);
	$text = eregi_replace("\\[imgprevleft\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><div  style='float: left; padding-top: 5px; padding-right: 15px; padding-bottom: 10px; padding-left: 0px;'><img border='0' src='/user_data/\\1/thumb/\\2' ALT='�������, ����� ���������'></div></a>",$text);
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
	//$text = strip_tags($text, '<a><div><table><tr><td><strike><embed><object>');
	$text = strip_tags($text);
	//
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
	$text = preg_replace("/\[log\](.*?)\[\/log\]/si", "<b><font color='blue'>\\1</font> [<a href='#; return false' onclick=\"ClBrd2('\\1')\" alt=\"����������� � ����� ������\">�����������</a> / <a href='#; return false' onclick=\"LogWin('\\1')\" alt='����������� ���'>�����������</a>]</b>", $text);
	$text = preg_replace("/\[url=([\w]+?:\/\/.*?)\](.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url\]([\w]+?:\/\/.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\1</a>", $text);
	$text = preg_replace("/\[url\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\1</a>", $text);
	
	$text = preg_replace("/\[spoyler id=l(\d+) title=\'(.*?)\'\](.*?)/si", "<a href=\"javascript:{}\" onClick=\"javascript:sh('l\\1');\">\\2</a><div id=\"l\\1\" style=\"display:none\">\\3", $text);
	$text = preg_replace("/\[\/spoyler\]/si", "</div>", $text);

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
	$text = eregi_replace("\\[imgprev\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><img border=0 src='/user_data/\\1/thumb/\\2' ALT='�������, ����� ���������'></a>",$text);
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
		return '<img src="https://www.timezero.ru/i/clans/'.$source.'.gif" border=0>';
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
	if($PrevList>=$ShowMax*2) echo '<a href="?'.$QueryStr.'&p=1" title="� ����� ������">�</a> ';
	if($PrevList>0) echo '<a href="?'.$QueryStr.'&p='.$PrevList.'"  title="���������� '.$ShowMax.' �������">�</a> ';
	for($i=$PrevList+1;$i<=$PrevList+$ShowMax;$i++) if($i<=$TotalPages) {
		if($i==$CurPage) echo '<u>'.$i.'</u> ';
		else echo '<a href="?'.$QueryStr.'&p='.$i.'">'.$i.'</a> ';
	}
	if($NextList<=$TotalPages) echo '<a href="?'.$QueryStr.'&p='.$NextList.'"  title="��������� '.$ShowMax.' �������">�</a> ';
	if($CurPage<$TotalPages) echo '<a href="?'.$QueryStr.'&p='.$TotalPages.'" title="� ����� �����">�</a>';
}

function ShowPages2($CurPage,$TotalPages,$ShowMax,$QueryStr,$prefix) {
	$PrevList=floor(($CurPage-1)/$ShowMax)*$ShowMax;
	$NextList=$PrevList+$ShowMax+1;
	if($PrevList>=$ShowMax*2) echo '<a href="?'.$QueryStr.'&p'.$prefix.'=1" title="� ����� ������">�</a> ';
	if($PrevList>0) echo '<a href="?'.$QueryStr.'&p'.$prefix.'='.$PrevList.'"  title="���������� '.$ShowMax.' �������">�</a> ';
	for($i=$PrevList+1; $i<=$PrevList+$ShowMax; $i++) {
		if($i<=$TotalPages) {
			if($i==$CurPage) echo '<u>'.$i.'</u> ';
			else echo '<a href="?'.$QueryStr.'&p'.$prefix.'='.$i.'">'.$i.'</a> ';
		}
	}
	if($NextList<=$TotalPages) echo '<a href="?'.$QueryStr.'&p'.$prefix.'='.$NextList.'"  title="��������� '.$ShowMax.' �������">�</a> ';
	if($CurPage<$TotalPages) echo '<a href="?'.$QueryStr.'&p'.$prefix.'='.$TotalPages.'" title="� ����� �����">�</a>';
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

// ������: ������� ��������� �������

function GetAccessArr($module, $access_arr) {
	
	// ��������� ���� �� ������, � ����������, � ��������. ����� ������ - ������������� ��������� ���.
	
	if (AuthUserGroup == 100) {
		
		foreach ($access_arr as $key => $value) {
			$new_access_arr[$key] = true;
			$i++;
		}
	
		$new_access_arr['admin'] = true;

	} else {
		
		$i = 0;
		$SQL = "SELECT `rules`, `admin` FROM `modules_access` WHERE `name` = '".AuthUserName."' AND `name_type` = 'login' AND `module` = '".$module."'";
		$r = mysql_query($SQL);
		if ($row = mysql_fetch_array($r)) {
			foreach ($access_arr as $key => $value) {
				$rule = (($row['rules'] & (pow(2, $i))) == 0)?false:true;
				$new_access_arr[$key] = $rule;
				$i++;
			}
			$new_access_arr['admin'] = (($row['admin']==1)?true:false);
		}
	
		if (!isset($new_access_arr)) {   // ����� �� ������ �� �������. ���� ����� �� �����
			$i = 0;
			$SQL = "SELECT `rules`, `admin` FROM `modules_access` WHERE `name` = '".AuthUserClan."' AND `name_type` = 'clan' AND `module` = '".$module."'";
			$r = mysql_query($SQL);
			if ($row = mysql_fetch_array($r)) {
				foreach ($access_arr as $key => $value) {
					$rule = (($row['rules'] & (pow(2, $i))) == 0)?false:true;
					$new_access_arr[$key] = $rule;
					$i++;
				}
				$new_access_arr['admin'] = (($row['admin']==1)?true:false);
			}
		}
	}

	if (isset($new_access_arr)) {
		return $new_access_arr;
	} else {
		$access_arr['admin'] = false;
		return $access_arr;
	}
}

function MakeModuleLable($module, $lable, $access_arr) {
	
	// ���������, ���� �� ������������� �����������������, � ����� �� �������.
	if (is_array($access_arr)) {

		$access_arr = GetAccessArr($module,$access_arr);
		if ($access_arr['admin']) {
			$result = "<table width=100%><tr><td align=left valign=center><h1>$lable</h1></td><td align=right valign=center><a href='http://www.tzpolice.ru/?act=access_management&module=$module'>���������� ���������</a></td></tr></table>";
		} else {
			$result = "<h1>$lable</h1>";
		}

	} else {
		$result = "<table width=100%><tr><td align=left valign=center><h1>$lable</h1></td><td align=right valign=center><a href='http://www.tzpolice.ru/?act=$access_arr'>��������� � ������</a></td></tr></table>";
	}

	return $result;
}

// ������� �� ������� ������ ������� �������.
function MakeRuleRow($row, $rules_count) {
	
	$w =	"<td>".$row['name']."</td>
			 <td align=center>".$row['name_type']."</td>
			 <td align=center>".(($row['admin'] == 1)?"<IMG SRC='../i/am_set.png'>":"<IMG SRC='../i/am_unset.png'>")."</td>";
	
	for ($i=0; $i<$rules_count; $i++)  {
		$rule = (($row['rules'] & (pow(2, $i))) == 0)?false:true;
		$w .= "<td align=center>".($rule?"<IMG SRC='../i/am_set.png'>":"<IMG SRC='../i/am_unset.png'>")."</td>";
	}

	$w .= "<td align=center><img src='../i/am_edit.png' border=0 title='������������� ������' height=20 class='editbtn'><img src='../i/am_del.png' border=0 title='������� ������' height=20 class='delbtn'></td>";

	return $w;
}

// �����: ������� ��������� �������

// ========================== ������� ������������ ======================================

function sequre_input($arr_arr) {
	
	$in = array();

	foreach($arr_arr as $arr) {
		foreach($arr as $k => $v) {
			$temp = mb_convert_encoding($v,"cp1251","utf8");
			$temp2 = mb_convert_encoding($temp,"utf8","cp1251");
			if($temp2 == $v) {
				$v = mb_convert_encoding($v,"cp1251","utf8");
			}
			if($k == 'location' && !preg_match("/^[-]?[\d]{1,3}\/[-]?[\d]{1,3}$/", $v)) {
				$v = '';
			}
			$in[$k] = addslashes(htmlspecialchars(trim($v)));
		}
	}

	return $in;
}

function quote_smart($input, $add_quotes = false) {
	
	
	$is_array = is_array($input);
	
	// ���� �������� ������ �� ������ - �������� � �������.
	if (!$is_array) {
		$input = array(
			"one" => $input,
		);
	}
	
	foreach ($input as &$value) {
				
		$value = trim($value);
		
		// ���� magic_quotes_gpc �������� - ���������� stripslashes
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		
		// ���� ���������� - �����, �� ������������ � �� �����
		// ���� ��� - �� ������� � ���������, � ����������
		if (!is_numeric($value)) {
			$value = urldecode($value);
			$value = ($add_quotes ? "'" : "").mysql_real_escape_string($value).($add_quotes ? "'" : "");
		} else {
			$value = $value * 2;
			$value = $value / 2;
		}
	}
	
	unset($value);
	
	// ���� �� ����� ��� �� ������ - ����������� �� �������.
	if (!$is_array) {
		$input = $input['one'];
	}
	
	return $input;
}

function encrypt($pass, $skey, $offset = 10) {
	$pass = substr ($pass,0,1) . substr($skey,0,$offset) . substr ($pass,1) . substr($skey,$offset);
	$str = strtoupper(sha1($pass));
	$out = 'abcdeabcdeabcdeabcdeabcdeabcdeabcdeabcde';
	$mix = Array (35, 6, 4, 25, 7, 8, 36, 16, 20, 37, 12, 31, 39, 38, 21, 5, 33, 15, 9, 13, 29, 23, 32, 22, 2, 27, 1, 10, 30, 24, 0, 19, 26, 14, 18, 34, 17, 28, 11, 3);
	for ($i=0; $i<40; $i++)
	{
		$out[$mix[$i]] = substr($str, $i, 1);
	}
	return ($out);
}

function passhash($pass) {
	$pass = md5($pass);
	$pass = encrypt(substr($pass,0,10), substr($pass,10) , 11);
	$pass = md5($pass.$pass.$pass.$pass.$pass.$pass.$pass);
	return ($pass);
}

// ==================  ����� ������� ������������ ========================================

?>