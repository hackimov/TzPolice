<?php
//-----------------------------------------------------------------------------
// ��������� ����������:
//-----------------------------------------------------------------------------
// ��� ��� MySQL:
//	$hostName = "localhost";			// ��� �����, ��� ��������� MySQL
//	$userName = "root";					// ��� ������������ MySQL
//	$password = "";						// ������ ������������ MySQL
//	$databaseName = "tzpolice";				// ��� ���� MySQL

//	$hostName = "";			// ��� �����, ��� ��������� MySQL
//	$userName = "";					// ��� ������������ MySQL
//	$password = "";						// ������ ������������ MySQL
//	$databaseName = "";				// ��� ���� MySQL

//----------------------------------------------------------
	$db_prefix='tzpolice_';

	$db=array();
	$dbs=array(
		'tz_users',
		'tz_clans',

		'rating_screen',
        'pvprating_screen',
		'rating_check',
		'okp4oin',

		'fees',

		'battles_catalog',
		'battles_catalog_cats',
		'battles_comments',
		'battles_rating'
	);

	foreach($dbs as $f){
	// ���������� ������ ������ ����
	// $db["users"] = $users_db = "tzpolice_users";
		$f1 = $f.'_db';
		$db[$f] = $$f1 = $db_prefix.$f;
	}
$bases=array();
$bases[] = "tzpolice_rating_screen";
$bases[] = "tzpolice_pvprating_screen";
//	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

//----------------------------------------------------------
// ������������ ����� ��������
	$max_level = '22';

//----------------------------------------------------------
	$months=array(1 => "������", "�������", "����", "������", "���", "����", "����", "������", "��������", "�������", "������", "�������");
	$months_a=array (1 => "������", "�������", "�����", "������", "���", "����", "����", "�������", "��������", "�������", "������", "�������");

//////////////////////////////////////////////////////////////
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
	$prof_alt[26] = '�������� �����';
	$prof_alt[27] = '��������� �����';
	$prof_alt[28] = '������ �����';
	$prof_alt[30] = '�����';

###################### Police Academy Posts #############
	$post_names[1] = '��. ������';
	$post_names[2] = '��. �����';
	$post_names[3] = '��. ������';
	$post_names[4] = '��. �����';
	$post_names[5] = '��. ����';

////////////////////////////////////////////////////////
/*	175-�������� ������� �����,
	176-����� ������� �����,
	177-�������� �������� �����,
	178-����� �������� �����,
	179-�� �������,
	180-� �������,
	181-� ����,
	182-�� ����.
	183 - ������� ����� �������
	184 - �������
	185 - ������
*/
	$cops_action[175] = '�������� ������� ��������';
	$cops_action[909] = '��������� ������� ������';
	$cops_action[176] = '����� ������� ��������';
	$cops_action[177] = '�������� �������� ��������';
	$cops_action[178] = '����� �������� ��������';
	$cops_action[179] = '�������� �� �������';
	$cops_action[180] = '������ � �������';
	$cops_action[181] = '�������� � ����';
	$cops_action[182] = '������ �� �����';
	$cops_action[183] = '�������� �� �������';
	$cops_action[184] = '�������';
	$cops_action[185] = '������';

?>