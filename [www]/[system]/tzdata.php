<?php
if(!defined('CONF')) die('Wood ;)');

#����� ������ �� �����
$adminGroup = 100;

$st_res = array('Venom'=>0,'Polymers'=>0,'Organic'=>0,'Precious metals'=>0,'Radioactive materials'=>0,'Gems'=>0,'Metals'=>0,'Silicon'=>0);

$ranks = array("0","20","60","120","250","600","1100","1800","2500","3200","4000","5000","6000","7200","100000","15000","25000","1000000");
$pve_ranks = array("0","250","1000","5000","25000","100000","500000","1000000","2000000","4000000","10000000");

$profs = array("", "������", "�������", "���������", "�������", "�������", "��������",
"�����������.����������.","�����������.���������.","�����������.����������.",
"���������","��������","�������","���������","���-�����","���-������","���-�����","��������","","","","","","","","",
"�������� �����","��������� �����","������ �����","", "�����");
$rank_names = array("�������","�������","������� �������","�������","������� �������",
"������� ���������","���������","������� ���������",
"�������","�����","������������","���������","�������-�����","�������-���������","�������-���������",
"������","��������","���� ������","������");
$pverank_names = array("��������","��������","��������","��������","�������",
"��������","�����������","�����������","����� ��������","����������","������� ��������","�������");

$servers = array('Terra Prima','Terra Prima','Archipelago');

$battleside = array('D'=>'�����','A'=>'�����������, �����','B'=>'�����������, �����','C'=>'�����������, �������','E'=>'����������','F'=>'�����������, ����','H'=>'�����������, ����');

$fractionslist = Array('1px','Invasion','RANGERS');

$noTZcounter = Array('news','deals','law_request','law_results','compens','compens2','prison_stats','n_comm','prisoned','news_search','pers_request','black','cops_depts','prison_rating','pers_request','news_search');

$policeclans = Array('police','Military Police','Police Academy','Financial Academy');

$CommentsPerPage = 15;
$NewsPP = 15;
$ThreadsPP = 15;
$RepliesPP = 20;
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
$crime[68] = '1.2.6. ����, ���, ���������';
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
$crime[88] = '26.1.  ���� �������� ������ ���������';
$crime[89] = '6.1. ������������� ���������� ���������';
$crime[90] = '������� �������������';
$crime[91] = '--���-- �� 30 �����';

//��� ������ � ���������� �����
$crime_list[][76] = '5.2. ��������';
$crime_list[][68] = '1.2.7. ����, ���, ���������';
$crime_list[][64] = '1.4.4. ����� ������������� � �������';
$crime_list[][70] = '1.4.5. ����. ������. �������';
$crime_list[][71] = '1.5.3. ����. ���';
$crime_list[][72] = '1.5.4. ��������� ������';
$crime_list[][77] = '5.2. ������� ��������(�������)';
$crime_list[][75] = '����� � 4 ���������� ������ ���������';
$crime_list[][73] = '4.3.1. ������� �������, �������, ������ ���������';
$crime_list[][78] = '6.2.1. �����';
$crime_list[][79] = '6.3.1. ��������� ������';
$crime_list[][62] = '6.5.1. �������� ������';
$crime_list[][80] = '6.5.4. �������� ���������� ����������';
$crime_list[][81] = '7.2.2. ������������ ������� ������� ������';
$crime_list[][82] = '7.2.3. ������������� ������������ ������� �������� ������';
$crime_list[][83] = '7.3.1. ��������������� �������� ������ ��������� ����������';
$crime_list[][84] = '7.4.1. ������������ ���������� ���������� ��������������';
$crime_list[][85] = '7.5.1. �������������';
$crime_list[][88] = '26.1.  ���� �������� ������ ���������';
$crime_list[][90] = '������� �������������';
$crime_list[][91] = '--���-- �� 30 �����';



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
$pers_adm[] = '�������';
$pers_adm[] = 'deadbeef';
$pers_adm[] = 'Lee-Loo';

$pers_adm_s[] = 'Macuta';
$pers_adm_s[] = 'Lee-Loo';
$pers_adm_s[] = 'deadbeef';

###################### Police Academy Posts #############
//$post_names[6] = '��. Vault';
$post_names[2] = '��. �����';
$post_names[1] = '��. ������';
$post_names[7] = '��. �����';
//$post_names[3] = '��. ������';
//$post_names[4] = '��. �����';
//$post_names[5] = '��. ����';

###################### Compensations ############
$compens_cops[] = '������';
$compens_cops2[] = '������';

$comm_tpl_cats[1] = '��';
$comm_tpl_cats[2] = '��';
$comm_tpl_cats[3] = '����';
$comm_tpl_cats[4] = '���';
$comm_tpl_cats[5] = '�������';
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


?>