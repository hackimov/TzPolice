<?php
	
	set_time_limit (0);
	
/****************
1. �������� ��� ������ � ��.. ��� ���� ������ ��� � ��� - ��������� ������
2. ������ ��� ��� �� ������ ���� ��������� 3 ������
3. ������ ��� ��� �� ������ ���� ��������� 4 ������
4. ������ ��� ��� ������ ���� ��������� 3 ������

2-4 ������ ������ ����� ���� ��������� ���� �������, ����, ����, ���� ��������� �����.

���������� ���� ����� ���� ������ ����� ������� � ������
****************/
$a = $_REQUEST['a'];
//--------------�����-----------------------
	function str_to_upper($title){
		$title=strtoupper($title);
		return strtr($title, "��������������������������������", "�����Ũ��������������������������");
	}
//--------------����-----------------------	
	function str_to_lower($title){
		$title=strtolower($title);
		return strtr($title, "�����Ũ��������������������������", "��������������������������������");
	}
	
	if($a==1){
		$admins_list = file_get_contents ('./zones.txt');
		$admins_list = explode("\n", $admins_list);
	}
	
	
//=========================
	include('/home/sites/police/dbconn/dbconn2.php');
//=========================
	
	if($a==1){
		$sSQL = 'SELECT * FROM `prison_chars`;';
		
	}elseif($a==2){
		$sSQL = 'SELECT * FROM `prison_chars` WHERE `last_pay`<'.(time()-3600*24*30*3).';';
		
	}elseif($a==3){
		$sSQL = 'SELECT * FROM `prison_chars` WHERE `last_pay`<'.(time()-3600*24*30*4).';';
		
	}elseif($a==4){
		$sSQL = 'SELECT * FROM `prison_chars` WHERE `last_pay`>'.(time()-3600*24*30*3).';';
		
	}
	
	$result = mysql_query($sSQL, $link);
	$our_list = $our_list2 = array();
	$i = 0;
	while($row = mysql_fetch_assoc($result)){
		if($a==1){
			$our_list[$i] = str_to_lower($row['nick']);
		}else{
			$our_list[$i] = $row['nick'];
		}
		$our_list2[$i]['last_pay'] = $row['last_pay'];
		$our_list2[$i]['level'] = $row['level'];
		$our_list2[$i]['term'] = $row['term'];
		$our_list2[$i]['collected'] = $row['collected'];
		
		$i++;
	}
/*	print_r(sizeof($our_list));
	echo "<BR>\n";
	print_r(sizeof($admins_list));
*/	if($a == 1){
		echo "� ��� �� ���:\n";
		foreach($admins_list AS $name){
			if(in_array(str_to_lower($name), $our_list)){
			}else{
				echo $name."\n";
			}
		}
	}elseif($a == 2 || $a == 3 || $a == 4){
		if($a == 2){
			echo "������ ��� ��� �� ������ ���� ��������� 3 ������\n";
		}elseif($a == 3){
			echo "������ ��� ��� �� ������ ���� ��������� 4 ������\n";
		}elseif($a == 4){
			echo "������ ��� ��� ������ ���� ��������� 3 ������\n";
		}
		
		echo "���\t�������\t����\tC���\t���� ��������� �����\n";
		foreach($our_list AS $id=>$name){
			echo $name."\t".$our_list2[$id]['level']."\t".$our_list2[$id]['term']."\t".$our_list2[$id]['collected']."\t".date('d-m-Y', $our_list2[$id]['last_pay'])."\n";
		}
	}
	
	mysql_close ($link);
?>