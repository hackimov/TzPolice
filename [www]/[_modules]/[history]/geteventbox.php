<?php

require_once('../functions.php');
require_once('../auth.php');

if(abs(AccessLevel) & AccessOP) {
	wr();
} else {
	echo $mess['AccessDenied'];
}

function wr() {
if ($in['event'] = 'pro') {

	$form = "<select id='pro' name='pro'>
			<option value='' selected='SELECTED'>�� ����� ��������</option>
			<option value='0'  style='background: url(i/i0.gif) no-repeat; padding-left: 15px'>��� ���������</option>
			<option value='1'  style='background: url(i/i1.gif) no-repeat; padding-left: 15px'>������</option>
			<option value='2'  style='background: url(i/i2.gif) no-repeat; padding-left: 15px'>�������</option>
			<option value='3'  style='background: url(i/i3.gif) no-repeat; padding-left: 15px'>���������</option>
			<option value='4'  style='background: url(i/i4.gif) no-repeat; padding-left: 15px'>�������</option>
			<option value='5'  style='background: url(i/i5.gif) no-repeat; padding-left: 15px'>������</option>
			<option value='6'  style='background: url(i/i6.gif) no-repeat; padding-left: 15px'>��������</option>
			<option value='7'  style='background: url(i/i7.gif) no-repeat; padding-left: 15px'>����������</option>
			<option value='8'  style='background: url(i/i8.gif) no-repeat; padding-left: 15px'>���������</option>
			<option value='9'  style='background: url(i/i9.gif) no-repeat; padding-left: 15px'>����������</option>
			<option value='10'  style='background: url(i/i10.gif) no-repeat; padding-left: 15px'>���������</option>
			<option value='11'  style='background: url(i/i11.gif) no-repeat; padding-left: 15px'>��������</option>
			<option value='12'  style='background: url(i/i12.gif) no-repeat; padding-left: 15px'>�������</option>
			<option value='13'  style='background: url(i/i13.gif) no-repeat; padding-left: 15px'>���������</option>
			<option value='14'  style='background: url(i/i14.gif) no-repeat; padding-left: 15px'>���-�������</option>
			<option value='16'  style='background: url(i/i16.gif) no-repeat; padding-left: 15px'>���-�����</option>
			<option value='15'  style='background: url(i/i15.gif) no-repeat; padding-left: 15px'>���-������</option>
			<option value='17'  style='background: url(i/i17.gif) no-repeat; padding-left: 15px'>��������</option>
			<option value='30'  style='background: url(i/i30.gif) no-repeat; padding-left: 15px'>�����</option>
			</select>"; 
}	
	
	/*$form_login = "<input type='text' id='login' name='login' value=''>";
	$form_lvl = "<input type='text' id='lvl' name='lvl' value=''>";
	$form_clan = "<input type='text' id='clan' name='clan' value=''>";
	$form_sclan =	"<select name='sclan'>
					<option value='' SELECTED></option>
					<option value='no_clan'>��� �����</option>";

	$query = mysql_query("	(SELECT `clan` as clan FROM `history`) 
							UNION 
							(SELECT `new_clan` as clan FROM `history`) 
							ORDER BY `clan`");
	
	while($row = mysql_fetch_array($query)) {
		$form_sclan.= "<option value='".$row['clan']."'>".$row['clan']."</option>";
	}
	
	$form_sclan .= "</select>";*/
	
echo $form;	
}

?>