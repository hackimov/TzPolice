<?php

if(abs(AccessLevel) & AccessOP) {
	history();
} else {
	echo $mess['AccessDenied'];
}


function history() {
	
	
	echo "<script type='text/javascript' src='/_modules/history/history.js'></script>
	<table border=1 width=100%><tr><td valign=top>
	�������� ������� �������. <br>�������� ��� �������:<br>";
	
	$typeslist = Array('lvl'=>'����� ������','pvpr'=>'����� ������','pro'=>'����� ���������','clan'=>'����� �����','faction'=>'����� �������','gender'=>'����� ����');
	$types = "<select id='ltype' style='width: 100%'>";
	foreach($typeslist as $k => $v) {
	    $types .= "<option value='$k'>$v</option>";
	}
	$types .= "</select><br><hr>";
	echo $types;
	echo "<DIV id='details'>��������� ������</DIV></td><td valign=top>";

	echo "������� �������� ������ �������� (��� ����������)<br>
	<input type='text' id='namepreset' name='namepreset' value='' style='width: 100%'><br>
	<textarea name='param' id='param' style='width: 100%; height: 100px'></textarea></td><td valign=top>";

	echo "����������� �������:<br>
	<select id='listpresets' name='listpresets' size='8' multiple style='width: 100%'>";

	$query = mysql_query("SELECT `id`, `preset` FROM `history_presets` WHERE `site_user_id` = '".AuthUserId."' OR `site_user_id` = '0'");
	
	while($row = mysql_fetch_array($query)) {
		echo "<option value='".$row['id']."'>".$row['preset']."</option>";
	}
	
	echo "<br>��� ������ ���������, ���������, ������� </td></tr></table>";

	
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
	
	$form_sclan .= "</select>";
	
	$form_pro = "<select id='pro' name='pro'>
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
				</select>"; */

}

?>