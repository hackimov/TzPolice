<?
/****************************************************
*	 Lebedev Sergey (fantastish@rambler.ru)			*
****************************************************/
//global($bases);
$bases=array();
$bases[] = "tzpolice_rating_screen";
$bases[] = "tzpolice_pvprating_screen";
$pvplink=array();
$pvplink[0] = "";
$pvplink[1] = "&kind=pvp";
$pvptitle=array();
$pvptitle[0] = "";
$pvptitle[1] = "���-";
$pvpkind=array();
$pvpkind[0] = "";
$pvpkind[1] = '<INPUT TYPE="hidden" NAME="kind" VALUE="pvp">';

// �������� � ���������� ������ c �������� �� ���-����� � ��
	function get_pvprating_screen($cid, $url, $db, $connection){
		$page_text = file_get_contents($url);
		$page_text = strip_tags($page_text, '<TABLE><TR><TH><TD><IMG>');
		$page_text = explode("\n", trim($page_text));
		echo "pvp $url<br>";
//		print_r($page_text);
		$i = $i2 = 0;
		$num_of_string = sizeof($page_text);
	//	while(!eregi("<TD>�</TD><TD>����� ���������</TD><TD>��������� �� ���� ����</TD><TD>������</TD><TD>���������</TD>", trim($page_text[$i])) && $i<$num_of_string){
//		while(!eregi("<Th>����� ���������</Th><th nowrap>��������� �� ���� ����</th><th>������</th><th>���������</th>", trim($page_text[$i])) && $i<$num_of_string){
		while(!eregi('<th>&#8470;</th><th>����� ���������</th><th>��������� �� ���� ����</th><th>������</th><th>���������</th>', trim($page_text[$i])) && $i<$num_of_string){
			$i++;
		}
		//echo $page_text[$i];

	// ��������� �������
		$place = $clan = $name = $level = $prof = $expa = $win = $lost = array();
		while(!eregi('</TABLE>', trim($page_text[$i])) && $i<$num_of_string){
			$i++;
			if(eregi("<TR>", trim($page_text[$i]))){
			//	echo $page_text[$i];
			// �����
				$place[$i2] = trim(strip_tags($page_text[++$i]));
				$i=$i+2;
			// ����, �����, �������
				$clan_name = trim(strip_tags($page_text[$i], '<IMG>'));
				if (eregi("<IMG SRC=\"/i/clans/(.*).gif\" (.*)>(.*) \[(.*)\]", $clan_name, $regs)){
					$clan[$i2] = trim($regs[1]);
					$name[$i2] = trim($regs[3]);
					$level[$i2] = trim($regs[4]);
				}else{
					eregi("(.*) \[(.*)\]", $clan_name, $regs);
					$clan[$i2] = "0";
					$name[$i2] = trim($regs[1]);
					$level[$i2] = trim($regs[2]);
				}
			// �����
				$prof[$i2] = trim(strip_tags($page_text[$i++], '<IMG>'));
				eregi("<img src=\"/i/i(.*).gif\"(.*)>", $prof[$i2], $regs);
				$prof[$i2] = trim($regs[1]);
				$i++;
			// �����
				$expa[$i2] = trim(strip_tags($page_text[$i++]));
			// ������
				$win[$i2] = trim(strip_tags($page_text[$i++]));
			// ���������
				$lost[$i2] = trim(strip_tags($page_text[$i++]));
				$i2++;
				//echo $place[$i2-1]." | ".$clan[$i2-1]." ".$name[$i2-1]." ".$level[$i2-1]." | ".$prof[$i2-1]." | ".$expa[$i2-1]."| ".$win[$i2-1]."| ".$lost[$i2-1]."<BR>\n";
			}
		}
	//	print_r($name);
		$regs='';
	// ����� ������ ������ 12.12.06 20:00
		while(!eregi("<TD>���������� �� (.*)</TD>", trim($page_text[$i]), $regs) && $i<$num_of_string){
			$i++;
		}
		if(sizeof($place)>0){
/*
			$screen_time = trim($regs[1]);
			$screen_time = explode(' ', $screen_time);
			$screen_time[0] = explode('.',$screen_time[0]);
			$screen_time[1] = explode(':',$screen_time[1]);
			$screen_time = mktime($screen_time[1][0], $screen_time[1][1], 0, $screen_time[0][1], $screen_time[0][0], $screen_time[0][2]);
*/
			$screen_time = mktime(date("H"), 0, 0, date("m"), date("d"), date("Y"));
			$insert = "";
			$insert = array();
			$s_place = sizeof($place);
			for($i=0; $i<$s_place; $i++){
				if($name[$i]!='' && $name[$i]!='0'){
					if($clan[$i]!='' && $clan[$i]!='0'){
					///////////////////////////////////////////////////
					// ��������� ���� �� ���� � ��,
					// ���� ��� - ��������� � �������� ��� id,
					// ���� ���� - ������ ���������� id
						$sSQL = 'SELECT * FROM `'.$db['tz_clans'].'` WHERE `name`=\''.anti_sql_injection($clan[$i]).'\'';
						$result = mysql_query($sSQL, $connection);
					// ���� ����
						if(mysql_num_rows($result)<1){
							$sSQL = 'INSERT INTO `'.$db['tz_clans'].'` SET `name`=\''.anti_sql_injection($clan[$i]).'\'';
							mysql_query($sSQL, $connection);
							$clan[$i] = mysql_insert_id($connection);
					// ���� ����
						}else{
							$row = mysql_fetch_assoc($result);
							$clan[$i] = $row['id'];
						}
					}

				///////////////////////////////////////////////////
				// ��������� ���� �� ��� � �� ����� ��,
				// ���� ��� - ��������� � �������� ��� id,
				// ���� ���� - ������ ���������� id
					$sSQL = 'SELECT `id`, `level`, `upd_time` FROM `'.$db['tz_users'].'` WHERE `name`=\''.anti_sql_injection($name[$i]).'\'';
					$result = mysql_query($sSQL, $connection);

					if(eregi('w',$prof[$i])){
						$sex='0';
					}else{
						$sex='1';
					}

					$set = '`sex` = \''.$sex.'\', `pro` = \''.$prof[$i].'\', `clan_id` = \''.$clan[$i].'\', `level` = \''.$level[$i].'\', `upd_time` = \''.$screen_time.'\'';
				// ���� ����
					if(mysql_num_rows($result)<1){
						$sSQL = 'INSERT INTO `'.$db['tz_users'].'` SET `name`=\''.anti_sql_injection($name[$i]).'\', '.$set;
						mysql_query($sSQL, $connection);
						$name[$i] = mysql_insert_id($connection);
				// ���� ����
					}else{
						$row = mysql_fetch_assoc($result);
						$name[$i] = $row['id'];

					// ��������� ������ ���� ������� �� ������ ������ ��� ����� ���� ��� � ����
					// ���� ��� �� ����������� 0 ������ ���� ������ �������������� ������ � ��������
						if($row['upd_time'] < $screen_time && $level[$i] >= $row['level']){
							$sSQL = 'UPDATE `'.$db['tz_users'].'` SET '.$set.' WHERE `id`=\''.$row['id'].'\';';
							mysql_query($sSQL, $connection);
						}
					}
				/////////////////////////////////////////////////
				// (0 �����) � (1 �����) � (����� > 5�*�� �������)
					if(date('G', $screen_time)=='0' && intval($place[$i])=='1' && intval($expa[$i])>(5000*$level[$i])){
						$star[$i]='1';
					}else{
						$star[$i]='0';
					}

					$insert[] = '( \'\', \''.intval($cid).'\', \''.intval($place[$i]).'\', \''.intval($clan[$i]).'\', \''.intval($name[$i]).'\', \''.intval($level[$i]).'\', \''.intval($prof[$i]).'\', \''.intval($expa[$i]).'\', \''.intval($win[$i]).'\', \''.intval($lost[$i]).'\', \''.intval($star[$i]).'\', \''.intval($screen_time).'\' )';
				}
			}
		// ������ �� ������ ����� - �������� ���
			$insert = array_unique ($insert);
			$insert = implode(', ', $insert);

		// ����� � �� ��� ��� ����������))
		//echo	$sSQL = "INSERT INTO `".$db["rating_screen"]."` ( `id`, `cid`, `place`, `clan_id`, `name_id`, `level`, `pro`, `expa`, `win`, `lost`, `time` ) VALUES ".$insert.";<BR>\n";
			$sSQL = 'INSERT INTO `'.$db['pvprating_screen'].'` ( `id`, `cid`, `place`, `clan_id`, `name_id`, `level`, `pro`, `expa`, `win`, `lost`, `star`, `time`) VALUES '.$insert.';';
//			echo ($sSQL."\n<br>");
			mysql_query($sSQL, $connection);

		}
	}



// �������� � ���������� ������ c �������� �� ����� � ��
	function get_rating_screen($cid, $url, $db, $connection){
		$page_text = file_get_contents($url);
		$page_text = strip_tags($page_text, '<TABLE><TR><TH><TD><IMG>');
		$page_text = explode("\n", trim($page_text));
		echo "nonpvp $url<br>";
//		print_r($page_text);
		$i = $i2 = 0;
		$num_of_string = sizeof($page_text);
	//	while(!eregi("<TD>�</TD><TD>����� ���������</TD><TD>��������� �� ���� ����</TD><TD>������</TD><TD>���������</TD>", trim($page_text[$i])) && $i<$num_of_string){
//		while(!eregi("<Th>����� ���������</Th><Th nowrap width=\"200\">��������� �� ���� ����</Th><Th width=\"90\">������</Th><Th width=\"96\">���������</Th>", trim($page_text[$i])) && $i<$num_of_string){
//		while(!eregi("<th>����� ���������</th><th>��������� �� ���� ����</th><th>������</th><th>���������</th>", trim($page_text[$i])) && $i<$num_of_string){
		while(!eregi('<th width="40">&#8470;</th><th>����� ���������</th><th width="200">��������� �� ���� ����</th><th width="90">������</th><th width="96">���������</th>', trim($page_text[$i])) && $i<$num_of_string){

			$i++;
		}
//		echo $page_text[$i];

	// ��������� �������
		$place = $clan = $name = $level = $prof = $expa = $win = $lost = array();
		while(!eregi('</TABLE>', trim($page_text[$i])) && $i<$num_of_string){
			$i++;
			if(eregi("<TR>", trim($page_text[$i]))){
			//	echo $page_text[$i];
			// �����
				$place[$i2] = trim(strip_tags($page_text[++$i]));
//				echo ($place[$i2]."::");
				$i=$i+2;
			// ����, �����, �������
				$clan_name = trim(strip_tags($page_text[$i], '<IMG>'));
				if (eregi("<img src=\"/i/clans/(.*).gif\" (.*)>(.*) \[(.*)\]", $clan_name, $regs)){
					$clan[$i2] = trim($regs[1]);
					$name[$i2] = trim($regs[3]);
					$level[$i2] = trim($regs[4]);
				}else{
					eregi("(.*) \[(.*)\]", $clan_name, $regs);
					$clan[$i2] = "0";
					$name[$i2] = trim($regs[1]);
					$level[$i2] = trim($regs[2]);
				}
//				echo ($clan[$i2]."::".$name[$i2]."::".$level[$i2]."::");
			// �����
				$prof[$i2] = trim(strip_tags($page_text[$i++], '<IMG>'));
				eregi("<img src=\"/i/i(.*).gif\"(.*)>", $prof[$i2], $regs);
				$prof[$i2] = trim($regs[1]);
				$i++;
			// �����
				$expa[$i2] = trim(strip_tags($page_text[$i++]));
			// ������
				$win[$i2] = trim(strip_tags($page_text[$i++]));
			// ���������
				$lost[$i2] = trim(strip_tags($page_text[$i++]));
				$i2++;
				//echo $place[$i2-1]." | ".$clan[$i2-1]." ".$name[$i2-1]." ".$level[$i2-1]." | ".$prof[$i2-1]." | ".$expa[$i2-1]."| ".$win[$i2-1]."| ".$lost[$i2-1]."<BR>\n";
			}
		}
	//	print_r($name);
		$regs='';
	// ����� ������ ������ 12.12.06 20:00
		while(!eregi("<TD>���������� �� (.*)</TD>", trim($page_text[$i]), $regs) && $i<$num_of_string){
			$i++;
		}
		if(sizeof($place)>0){
/*			$screen_time = trim($regs[1]);
			$screen_time = explode(' ', $screen_time);
			$screen_time[0] = explode('.',$screen_time[0]);
			$screen_time[1] = explode(':',$screen_time[1]);
			$screen_time = mktime($screen_time[1][0], $screen_time[1][1], 0, $screen_time[0][1], $screen_time[0][0], $screen_time[0][2]);
*/
			$screen_time = mktime(date("H"), 0, 0, date("m"), date("d"), date("Y"));
			$insert = "";
			$insert = array();
			$s_place = sizeof($place);
			for($i=0; $i<$s_place; $i++){
				if($name[$i]!='' && $name[$i]!='0'){
					if($clan[$i]!='' && $clan[$i]!='0'){
					///////////////////////////////////////////////////
					// ��������� ���� �� ���� � ��,
					// ���� ��� - ��������� � �������� ��� id,
					// ���� ���� - ������ ���������� id
						$sSQL = 'SELECT * FROM `'.$db['tz_clans'].'` WHERE `name`=\''.anti_sql_injection($clan[$i]).'\'';
						$result = mysql_query($sSQL, $connection);
					// ���� ����
						if(mysql_num_rows($result)<1){
							$sSQL = 'INSERT INTO `'.$db['tz_clans'].'` SET `name`=\''.anti_sql_injection($clan[$i]).'\'';
							mysql_query($sSQL, $connection);
							$clan[$i] = mysql_insert_id($connection);
					// ���� ����
						}else{
							$row = mysql_fetch_assoc($result);
							$clan[$i] = $row['id'];
						}
					}

				///////////////////////////////////////////////////
				// ��������� ���� �� ��� � �� ����� ��,
				// ���� ��� - ��������� � �������� ��� id,
				// ���� ���� - ������ ���������� id
					$sSQL = 'SELECT `id`, `level`, `upd_time` FROM `'.$db['tz_users'].'` WHERE `name`=\''.anti_sql_injection($name[$i]).'\'';
					$result = mysql_query($sSQL, $connection);

					if(eregi('w',$prof[$i])){
						$sex='0';
					}else{
						$sex='1';
					}

					$set = '`sex` = \''.$sex.'\', `pro` = \''.$prof[$i].'\', `clan_id` = \''.$clan[$i].'\', `level` = \''.$level[$i].'\', `upd_time` = \''.$screen_time.'\'';
				// ���� ����
					if(mysql_num_rows($result)<1){
						$sSQL = 'INSERT INTO `'.$db['tz_users'].'` SET `name`=\''.anti_sql_injection($name[$i]).'\', '.$set;
						mysql_query($sSQL, $connection);
						$name[$i] = mysql_insert_id($connection);
				// ���� ����
					}else{
						$row = mysql_fetch_assoc($result);
						$name[$i] = $row['id'];

					// ��������� ������ ���� ������� �� ������ ������ ��� ����� ���� ��� � ����
					// ���� ��� �� ����������� 0 ������ ���� ������ �������������� ������ � ��������
						if($row['upd_time'] < $screen_time && $level[$i] >= $row['level']){
							$sSQL = 'UPDATE `'.$db['tz_users'].'` SET '.$set.' WHERE `id`=\''.$row['id'].'\';';
							mysql_query($sSQL, $connection);
						}
					}
				/////////////////////////////////////////////////
				// (0 �����) � (1 �����) � (����� > 5�*�� �������)
					if(date('G', $screen_time)=='0' && intval($place[$i])=='1' && intval($expa[$i])>(5000*$level[$i])){
						$star[$i]='1';
					}else{
						$star[$i]='0';
					}

					$insert[] = '( \'\', \''.intval($cid).'\', \''.intval($place[$i]).'\', \''.intval($clan[$i]).'\', \''.intval($name[$i]).'\', \''.intval($level[$i]).'\', \''.intval($prof[$i]).'\', \''.intval($expa[$i]).'\', \''.intval($win[$i]).'\', \''.intval($lost[$i]).'\', \''.intval($star[$i]).'\', \''.intval($screen_time).'\' )';
				}
			}
		// ������ �� ������ ����� - �������� ���
			$insert = array_unique ($insert);

			$insert = implode(', ', $insert);

		// ����� � �� ��� ��� ����������))
		//echo	$sSQL = "INSERT INTO `".$db["rating_screen"]."` ( `id`, `cid`, `place`, `clan_id`, `name_id`, `level`, `pro`, `expa`, `win`, `lost`, `time` ) VALUES ".$insert.";<BR>\n";
			$sSQL = 'INSERT INTO `'.$db['rating_screen'].'` ( `id`, `cid`, `place`, `clan_id`, `name_id`, `level`, `pro`, `expa`, `win`, `lost`, `star`, `time`) VALUES '.$insert.';';
			//echo ($sSQL."\n<br>");
			mysql_query($sSQL, $connection);
//			echo (mysql_error());

		}
	}


// ---------- ����� ������� -------------
	function rating_screen_view ($full_access, $months_a, $menu, $cid, $time, $connection, $db, $id, $action, $max_level, $basepvp){
//    $basepvp=0;
    global $bases;
	global $pvplink;
	global $pvptitle;
	global $pvpkind;
	// ���������� ��������� ��������
		if(!isset($time) || $time==0){
			$time = time()-600;
		}elseif(is_array($time)){
			$time = mktime($time[3], 0, 0, $time[1], $time[0], $time[2]);
		}

		if(!isset($cid)){
			$cid=$max_level;
			if($full_access==1){
				$cid=5;
			}
		}

		if($cid>$max_level){
			$cid=$max_level;
		}

	//	�������������
		$time = date("d:m:Y:H:i", $time);
		$c_time = $time = explode(':',$time);
		$time = mktime($time[3], 0, 0, $time[1], $time[0], $time[2]);

		$text = title('����� �������� �� '.$pvptitle[$basepvp].'�����<BR>'.date("d.m.Y H:i", $time));

		$text .= "<BR><DIV STYLE=\"WIDTH:100%;\" ALIGN=\"right\">\n";
		$text .= "<FORM METHOD=\"GET\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\">\n";
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"tzrating\">\n";
		$text .= $pvpkind[$basepvp];
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"view\">\n";
		$text .= "<TABLE WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\">\n";
		$text .= " <TR VALIGN=\"top\">\n";
		$text .= "  <TD ALIGN=\"right\"><B>����:&nbsp;</B></TD>\n";
		$text .= "  <TD>\n";
		$text .= "   <SELECT NAME=\"time[0]\">\n";
		for($i=1;$i<=31;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[0])?" selected":"")." CLASS=\"select\">".$i."\n";
		}
		$text .= "   </SELECT><SELECT NAME=\"time[1]\">\n";
		for($i=1;$i<=12;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[1])?" selected":"")." CLASS=\"select\">".$months_a[$i]."\n";
		}
		$text .= "   </SELECT><SELECT NAME=\"time[2]\">\n";
		for($i=2006;$i<=date('Y');$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[2])?" selected":"")." CLASS=\"select\">".$i."\n";
		}
		$text .= "   </SELECT>&nbsp;<B>���:&nbsp;</B><SELECT NAME=\"time[3]\">\n";
		for($i=0;$i<=23;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[3])?" selected":"")." CLASS=\"select\">".(($i<10)?'0':'').$i."\n";
		}
		$text .= "   </SELECT>&nbsp;<B>�������:&nbsp;</B><SELECT NAME=\"cid\">\n";
		$text .= "    <OPTION VALUE='0'>-���-";
		for($i=1;$i<=$max_level;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$cid)?" selected":"")." CLASS=\"select\">".$i."\n";
		}
		$text .= "   </SELECT>\n";
	//	$text .= "   <B>���:</B> <input type=\"input\" class=\"text\" NAME=\"name\" value=\"".htmlspecialchars(trim($name))."\">\n";
		$text .= "   <input type=\"submit\" class=\"submit\" value=\"�������� >>\">\n";
		$text .= "  </TD>\n";
		$text .= " </TR>\n";
		$text .= "</FORM>\n";
		$text .= "</TABLE><BR>\n";


	// ����. ����
		$l_time = $time-86400;
		$l_time = date("d:m:Y:H:i", $l_time);
		$l_time = explode(':',$l_time);
		$text .= '<CENTER><A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating'.$pvplink[$basepvp].'&action=view&time[0]='.$l_time[0].'&time[1]='.$l_time[1].'&time[2]='.$l_time[2].'&time[3]='.$l_time[3].'&cid='.$cid."\"><<< ����. ����</A>\n";


	// ����. ���
		$l_time = $time-3600;
		$l_time = date("d:m:Y:H:i", $l_time);
		$l_time = explode(':',$l_time);
		$text .= ' &nbsp;&nbsp;|&nbsp;&nbsp; <A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating'.$pvplink[$basepvp].'&action=view&time[0]='.$l_time[0].'&time[1]='.$l_time[1].'&time[2]='.$l_time[2].'&time[3]='.$l_time[3].'&cid='.$cid."\"><< ����. ���</A>\n";

	// ����. ���
		$n_time = $time+3600;
		$n_time = date("d:m:Y:H:i", $n_time);
		$n_time = explode(':',$n_time);
		$text .= ' &nbsp;&nbsp;|&nbsp;&nbsp; <A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating'.$pvplink[$basepvp].'&action=view&time[0]='.$n_time[0].'&time[1]='.$n_time[1].'&time[2]='.$n_time[2].'&time[3]='.$n_time[3].'&cid='.$cid."\">����. ��� >></A>\n";

	// ����. ����
		$n_time = $time+86400;
		$n_time = date("d:m:Y:H:i", $n_time);
		$n_time = explode(':',$n_time);
		$text .= ' &nbsp;&nbsp;|&nbsp;&nbsp; <A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating'.$pvplink[$basepvp].'&action=view&time[0]='.$n_time[0].'&time[1]='.$n_time[1].'&time[2]='.$n_time[2].'&time[3]='.$n_time[3].'&cid='.$cid."\">����. ���� >>></A></CENTER>\n";
		$text .= "<BR></DIV>\n";


		$where = array();
		if(isset($cid) && $cid>0 ){
			$where[] = '`cid`=\''.anti_sql_injection($cid).'\'';
		}
		$where[] = '`time`=\''.$time.'\'';
	//	$where[] = "1=1";
		if(sizeof($where>1)){
			$where = implode(' AND ', $where);
		}else{
			$where = $where[0];
		}
		$sSQL = 'SELECT * FROM `'.$bases[$basepvp].'` WHERE ('.$where.') ORDER BY cid DESC, place ASC';
//echo ($sSQL);
//		$sSQL = 'SELECT * FROM `'.$db['rating_screen'].'` WHERE ('.$where.') ORDER BY cid DESC, place ASC';
		$result = mysql_query($sSQL,$connection);
		$nrows = mysql_num_rows($result);
		if($nrows>0){
			$text .= "<TABLE WIDTH=\"100%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\">\n";
			$text .= "<TR BGCOLOR=\"#E4DDC5\" ALIGN=\"center\">\n";
			if($full_access==1){
				$text .= "<script language=\"JavaScript\">\n";
				$text .= "<!--\n";
				$text .= "function SelectAll(mark){\n";
				$text .= " for (i = 0; i < document.forms['rating_arh'].elements.length; i++){\n";
				$text .= "  var item = document.forms['rating_arh'].elements[i];\n";
				$text .= "  if (item.name == \"item_id\"){\n";
				$text .= "   item.checked = mark;\n";
				$text .= "  };\n";
				$text .= " }\n";
				$text .= "}\n";
				$text .= "function CheckSelect(form){\n";
				$text .= " var all_ids='';\n";
				$text .= " var d=0;\n";
				$text .= " for (i = 0; i < form.elements.length; i++){\n";
				$text .= "  var item = form.elements[i];\n";
				$text .= "  if (item.name == \"item_id\"){\n";
				$text .= "   if (item.checked){\n";
				$text .= "    d=1;\n";
				$text .= "    all_ids = all_ids +\"|\"+ item.value;\n";
				$text .= "   }\n";
				$text .= "  }\n";
				$text .= " }\n";
				$text .= " if(d==1){\n";
				$text .= "  document.forms['rating_arh'].elements['x_value'].value = all_ids;\n";
				$text .= "  return true;\n";
				$text .= " }else{\n";
				$text .= "  alert(\"������ �� �������\");\n";
				$text .= "  return false;\n";
				$text .= " }\n";
				$text .= "}\n";
				$text .= "//-->\n";
				$text .= "</script>\n";
				$text .= "<FORM METHOD=\"post\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\" NAME=\"rating_arh\" onSubmit=\"return CheckSelect(this);\">\n";
				$text .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"tzrating\">\n";
				$text .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"\">\n";
				$text .= "<INPUT TYPE=\"hidden\" NAME=\"x_value\" VALUE=\"\">\n";
				$text .= "<INPUT TYPE=\"hidden\" NAME=\"ok\" VALUE=\"1\">\n";
				$text .= " <TD WIDTH=15><SMALL>[���]</SMALL><BR><input type=\"checkbox\" onclick=\"SelectAll(checked)\"></TD>\n";
			}
			$text .= " <TD><B>�</B></TD>\n";
			$text .= " <TD><B>����� ���������</B></TD>\n";
			$text .= " <TD><B>��������� �� ���� ����</B></TD>\n";
			$text .= " <TD><B>���</B></TD>\n";
			$text .= " <TD><B>������</B></TD>\n";
			$text .= " <TD><B>���������</B></TD>\n";
			$text .= "</TR>\n";

			$bgcolor[1]='#F5F5F5'; //���� 1
			$bgcolor[2]='#E4DDC5'; //���� 2
			$bgcolor[3]='#FFCC99'; //1 - ��������� �� ��������
			$bgcolor[4]='#EAC987'; //2 - �����������
			$bgcolor[5]='#CCFFCC'; //3 - ����������� - ����
			$bgcolor[6]='#FF9987'; //4 - ����������� - ��������

			while($row = mysql_fetch_assoc($result)){
				if($c_cid != $row['cid']){
					$i=1;
					$c_cid = $row['cid'];
					$text .= "<TR>\n";
					$text .= ' <TD COLSPAN=7><B><I>'.$row['cid']." �������</I></B></TD>\n";
					$text .= "</TR>\n";
				}

				if($i>2) $i=1;

				$text2 = '';
				if($full_access==1){
					$sSQL1 = 'SELECT `status`, `time`, `uid` FROM `'.$db['rating_check'].'` WHERE `name_id`=\''.$row['name_id'].'\'';
					$result1 = mysql_query($sSQL1, $connection);
					if(mysql_num_rows($result1)>0){
						$row1 = mysql_fetch_assoc($result1);

					// ����� ������� ���� ���� ��� �� ������ 24 �����
						if(($row1['time']+86400)>time()){

							$sSQL2 = 'SELECT `user_name` FROM `site_users` WHERE `id`=\''.$row1['uid'].'\'';
							$result2 = mysql_query($sSQL2, $connection);
							$row2 = mysql_fetch_assoc($result2);

						// ������������ �� ��������
							if($row1['status']=='1'){
								$i=3;
								$text2 = '<BR><SMALL>[ '.date("d:m:Y H:i", $row1['time']).' ��������� �� ��������   '.stripslashes($row2['user_name'])." ]</SMALL>\n";

						// �����������
							}elseif($row1['status']=='2'){
								$i=4;
								$text2 = '<BR><SMALL>[ '.date("d:m:Y H:i", $row1['time']).' �����������   '.stripslashes($row2['user_name'])." ]</SMALL>\n";
						// ����������� +
							}elseif($row1['status']=='3'){
								$i=5;
								$text2 = '<BR><SMALL>[ '.date("d:m:Y H:i", $row1['time']).' ����   '.stripslashes($row2['user_name'])." ]</SMALL>\n";

						// ����������� -
							}elseif($row1['status']=='4'){
								$i=6;
								$text2 = '<BR><SMALL>[ '.date("d:m:Y H:i", $row1['time']).' ��������   '.stripslashes($row2['user_name'])." ]</SMALL>\n";
							}
						}
					}
				}
				$text .= '<TR BGCOLOR="'.$bgcolor[$i]."\">\n";
				if($full_access==1){
					$text .= ' <TD><INPUT TYPE="checkbox" NAME="item_id" VALUE="'.$row['name_id']."\"></TD>\n";
				}
				$text .= ' <TD ALIGN="center">'.stripslashes($row['place'])."</TD>\n";

				if($row['clan_id']>0){
					$sSQL2 = 'SELECT `name` FROM `'.$db['tz_clans'].'` WHERE `id`=\''.$row['clan_id'].'\'';
					$result2 = mysql_query($sSQL2, $connection);
					$row2 = mysql_fetch_assoc($result2);
					$clan = '{_CLAN_}'.trim($row2['name']).'{_/CLAN_}';
				}else{
					$clan = '';
				}
				$sSQL2 = 'SELECT `id`, `name`, `sex` FROM `'.$db['tz_users'].'` WHERE `id`=\''.$row['name_id'].'\'';
				$result2 = mysql_query($sSQL2, $connection);
				$row2 = mysql_fetch_assoc($result2);
			//	echo "| ".$clan." ".$name." ".$level." | ".$prof." |";
				$text .= ' <TD><A HREF="javascript:{}"><IMG SRC="{_SERVER_NAME_}/i/bullet-red-01a.gif" BORDER=0 width="18" height="11" OnClick="ClBrd(\''.stripslashes($row2['name']).'\');" ALT="����������� ��� � ����� ������"></A> '.$clan.' <A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating&action=view2&time[0]='.$c_time[0].'&time[1]='.$c_time[1].'&time[2]='.$c_time[2].'&cid='.$row2['id'].'">'.stripslashes($row2['name']).'</A> ['.$row['level'].'] {_PROF_}'.$row['pro'].(($row2['sex']=='0')?'w':'').'{_/PROF_}'.$text2."</TD>\n";

				$text .= ' <TD ALIGN="center">'.(($row['star']=='1')?'<IMG SRC="http://www.timezero.ru/i/manual/rsign.jpg" BORDER=0 WIDTH=34 HEIGHT=18><BR>':'').stripslashes($row['expa'])."</TD>\n";
				$text .= ' <TD ALIGN="center">'.($row['win']+$row['lost'])."</TD>\n";
				$text .= ' <TD ALIGN="center">'.stripslashes($row['win'])."</TD>\n";
				$text .= ' <TD ALIGN="center">'.stripslashes($row['lost'])."</TD>\n";
				$text .= "</TR>\n";

				$i++;
			}
			if($full_access==1){
				$text .= "<TR>\n";
				$text .= " <TD COLSPAN=\"7\" ALIGN=\"left\">";
				$text .= "<input type=\"submit\" class=\"submit\" OnClick=\"document.forms['rating_arh'].elements['action'].value = 'add_to_check'\" value=\"�� �������� >>\">";
				$text .= "</TD>\n";
				$text .= "</TR>\n";
				$text .= "</FORM>\n";
			}
			$text .= "</TABLE>\n";
		}else{
			$text .= "<CENTER>����� �� ������������ ���������� ���</CENTER>";
		}



		return $text;
	}


// ---------- ����� ������� -------------
	function rating_screen_view2 ($full_access, $months_a, $menu, $cid, $time, $connection, $db, $id, $action, $max_level, $basepvp){
    global $bases;
	global $pvplink;
	global $pvptitle;
	global $pvpkind;
	// ���������� ��������� ��������
		if(!isset($time) || $time==0){
			$time = time();
		}elseif(is_array($time)){
			$time = mktime($time[3], 0, 0, $time[1], $time[0], $time[2]);
		}
		$ttime = $time;

	//	�������������
		$time = date("d:m:Y:H:i", $time);
		$c_time = $time = explode(":",$time);
		$time1 = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
		$time2 = mktime(23, 0, 0, $time[1], $time[0], $time[2]);
		$time2 = $time2+3600;

		$text = title('���������� �� �������� ����� �� '.date("d.m.Y", $ttime));

		$sSQL2 = 'SELECT * FROM `'.$db['tz_users'].'` WHERE `id`=\''.anti_sql_injection($cid).'\'';
		$result2 = mysql_query($sSQL2, $connection);
		$row2 = mysql_fetch_assoc($result2);
		$text .= title($row2['name']);

		$text .= "<BR><DIV STYLE=\"WIDTH:100%;\" ALIGN=\"right\">\n";
		$text .= "<FORM METHOD=\"GET\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\">\n";
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"tzrating\">\n";
		$text .= $pvpkind[$basepvp];
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"view2\">\n";
		$text .= "<TABLE WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\">\n";
		$text .= " <TR VALIGN=\"top\">\n";
		$text .= "  <TD ALIGN=\"right\"><B>����:&nbsp;</B></TD>\n";
		$text .= "  <TD>\n";
		$text .= "   <SELECT NAME=\"time[0]\">\n";
		for($i=1;$i<=31;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[0])?" selected":"")." CLASS=\"select\">".$i."\n";
		}
		$text .= "   </SELECT><SELECT NAME=\"time[1]\">\n";
		for($i=1;$i<=12;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[1])?" selected":"")." CLASS=\"select\">".$months_a[$i]."\n";
		}
		$text .= "   </SELECT><SELECT NAME=\"time[2]\">\n";
		for($i=2006;$i<=date("Y");$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[2])?" selected":"")." CLASS=\"select\">".$i."\n";
		}
		$text .= "   </SELECT>\n";
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"cid\" VALUE=\"".anti_sql_injection($cid)."\">\n";
	//	$text .= "   <B>���:</B> <input type=\"input\" class=\"text\" NAME=\"name\" value=\"".$row2["name"]))."\">\n";
		$text .= "   <input type=\"submit\" class=\"submit\" value=\"�������� >>\">\n";
		$text .= "  </TD>\n";
		$text .= " </TR>\n";
		$text .= "</TABLE>\n";
		$text .= "</FORM>\n";

	// ����. ����
		$l_time = $ttime-86400;
		$l_time = date("d:m:Y:H:i", $l_time);
		$l_time = explode(':',$l_time);
		$text .= '<CENTER><A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating'.$pvplink[$basepvp].'&action=view2&time[0]='.$l_time[0].'&time[1]='.$l_time[1].'&time[2]='.$l_time[2].'&cid='.anti_sql_injection($cid)."\"><<< ����. ����</A>\n";

	// ����. ����
		$n_time = $ttime+86400;
		$n_time = date("d:m:Y:H:i", $n_time);
		$n_time = explode(":",$n_time);
		$text .= ' &nbsp;&nbsp;|&nbsp;&nbsp; <A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating'.$pvplink[$basepvp].'&action=view2&time[0]='.$n_time[0].'&time[1]='.$n_time[1].'&time[2]='.$n_time[2].'&cid='.anti_sql_injection($cid)."\">����. ���� >>></A></CENTER>\n";
		$text .= "<BR></DIV>\n";


		$where = array();
	//	if(isset($cid) && $cid>0 ){
			$where[] = '`name_id`=\''.anti_sql_injection($cid).'\'';
	//	}
		$where[] =  '`time`>'.$time1;
		$where[] =  '`time`<='.$time2;
	//	$where[] = '1=1';
		if(sizeof($where>1)){
			$where = implode(' AND ', $where);
		}else{
			$where = $where[0];
		}

		$sSQL = 'SELECT * FROM `'.$bases[$basepvp].'` WHERE ('.$where.') ORDER BY time ASC';
		$result = mysql_query($sSQL,$connection);
		$nrows = mysql_num_rows($result);
		if($nrows>0){
			$text .= "<TABLE WIDTH=\"100%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\">\n";
			$text .= "<TR BGCOLOR=\"#E4DDC5\" ALIGN=\"center\">\n";
			$text .= " <TD><B>�����</B></TD>\n";
			$text .= " <TD><B>�</B></TD>\n";
			$text .= " <TD><B>����� ���������</B></TD>\n";
			$text .= " <TD><B>��������� �� ���� ����</B></TD>\n";
			$text .= " <TD><B>���</B></TD>\n";
			$text .= " <TD><B>������</B></TD>\n";
			$text .= " <TD><B>���������</B></TD>\n";
			$text .= "</TR>\n";

			$bgcolor[1] = '#F5F5F5';
			$bgcolor[2] = '#E4DDC5';

			$i=1;
			while($row = mysql_fetch_assoc($result)){
				if($i>2) $i=1;
				$text .= '<TR BGCOLOR="'.$bgcolor[$i]."\">\n";
				$text .= ' <TD ALIGN="center"><A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating&action=view&time[0]='.date('d', $row['time']).'&time[1]='.date('m', $row['time']).'&time[2]='.date('Y', $row['time']).'&time[3]='.date('H', $row['time']).'&cid='.$row['level'].'"><B>'.date('H:i', $row['time']).'</B><BR><SMALL>'.date('d.m.Y', $row['time'])."</SMALL></A></TD>\n";
				$text .= ' <TD ALIGN="center">'.stripslashes($row['place'])."</TD>\n";

				if($row['clan_id']>0){
					$sSQL2 = 'SELECT * FROM `'.$db['tz_clans'].'` WHERE `id`=\''.$row['clan_id'].'\'';
					$result2 = mysql_query($sSQL2, $connection);
					$row2 = mysql_fetch_assoc($result2);
					$clan = '{_CLAN_}'.trim($row2['name']).'{_/CLAN_}';
				}else{
					$clan = '';
				}
				$sSQL2 = 'SELECT * FROM `'.$db['tz_users'].'` WHERE `id`=\''.$row['name_id'].'\'';
				$result2 = mysql_query($sSQL2, $connection);
				$row2 = mysql_fetch_assoc($result2);
			//	echo "| ".$clan." ".$name." ".$level." | ".$prof." |";
				$text .= ' <TD>'.$clan.' '.stripslashes($row2['name']).' ['.$row['level'].'] {_PROF_}'.$row['pro'].(($row2['sex']=='0')?'w':'')."{_/PROF_}</TD>\n";


				$text .= ' <TD ALIGN="center">'.(($row['star']=='1')?'<IMG SRC="http://www.timezero.ru/i/manual/rsign.jpg" BORDER=0 WIDTH=34 HEIGHT=18><BR>':'');
				if(isset($l_expa) && ($l_expa != $row['expa'])){
					$text .= '<SMALL>(+'.($row['expa']-$l_expa).')</SMALL><BR>';
				}
				$l_expa = $row['expa'];
				$text .= stripslashes($row['expa']);
				$text .= "</TD>\n";

				$text .= ' <TD ALIGN="center">';
				if(isset($l_nbattle) && ($l_nbattle != ($row['win']+$row['lost']))){
					$text .= '<SMALL>(+'.($row['win']+$row['lost']-$l_nbattle).')</SMALL><BR>';
				}
				$l_nbattle = $row['win']+$row['lost'];
				$text .= $l_nbattle;
				$text .= "</TD>\n";

				$text .= " <TD ALIGN=\"center\">";
				if(isset($l_win) && ($l_win != $row['win'])){
					$text .= '<SMALL>(+'.($row['win']-$l_win).')</SMALL><BR>';
				}
				$l_win = $row['win'];
				$text .= stripslashes($row['win']);
				$text .= "</TD>\n";

				$text .= " <TD ALIGN=\"center\">";
				if(isset($l_lost) && ($l_lost != $row['lost'])){
					$text .= '<SMALL>(+'.($row['lost']-$l_lost).')</SMALL><BR>';
				}
				$l_lost = $row['lost'];
				$text .= stripslashes($row['lost']);
				$text .= "</TD>\n";
				$text .= "</TR>\n";

				$i++;
			}
			$text .= "</TABLE>\n";
		}else{
			$text .= '<CENTER>����� �� ������������ ���������� ���</CENTER>';
		}

		return $text;
	}


// ---------- ����� ��� ��� �� �������� -------------
	function rating_users_on_check ($action, $connection, $db, $page){

		$where = array();

		if($action=='check'){
			$text = title('������ �����������');
			$where[] = '`status`=\'2\'';
		}elseif($action=='checked'){
			$text = title('������ �����������: ��� ���������');
			$where[] = '`status`=\'3\'';
		}elseif($action=='checked2'){
			$text = title('������ �����������: ��������');
			$where[] = '`status`=\'4\'';
		}else{
	//	$action=="on_check"
			$text = title('������ ������������ �� ��������');
			$where[] = '`status`=\'1\'';
		}


		if(sizeof($where>1)){
			$where = implode(' AND ', $where);
		}else{
			$where = $where[0];
		}

		$sSQL = 'SELECT * FROM `'.$db['rating_check'].'` WHERE ('.$where.') ORDER BY time DESC';
		$result = mysql_query($sSQL,$connection);
		$nrows = mysql_num_rows($result);
		if($nrows>0){
			$text .= "<BR>\n";
			list($sql, $ttext) = @list_all_pages($nrows, $page, '30', $sSQL, 'act=tzrating&action='.$action);
			$result = mysql_query($sql, $connection);

			$text .= '<CENTER>�����: '.$nrows." �������</CENTER>\n";

			$text .= $ttext;

			$text .= "<TABLE WIDTH=\"100%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\">\n";
			$text .= "<TR BGCOLOR=\"#E4DDC5\" ALIGN=\"center\">\n";

			if ($action=='check' || $action=='on_check') {
				$text .= "<script language=\"JavaScript\">\n";
				$text .= "<!--\n";
				$text .= "function SelectAll(mark){\n";
				$text .= " for (i = 0; i < document.forms['rating_arh'].elements.length; i++){\n";
				$text .= "  var item = document.forms['rating_arh'].elements[i];\n";
				$text .= "  if (item.name == \"item_id\"){\n";
				$text .= "   item.checked = mark;\n";
				$text .= "  };\n";
				$text .= " }\n";
				$text .= "}\n";
				$text .= "function CheckSelect(form){\n";
				$text .= " var all_ids='';\n";
				$text .= " var d=0;\n";
				$text .= " for (i = 0; i < form.elements.length; i++){\n";
				$text .= "  var item = form.elements[i];\n";
				$text .= "  if (item.name == \"item_id\"){\n";
				$text .= "   if (item.checked){\n";
				$text .= "    d=1;\n";
				$text .= "    all_ids = all_ids +\"|\"+ item.value;\n";
				$text .= "   }\n";
				$text .= "  }\n";
				$text .= " }\n";
				$text .= " if(d==1){\n";
				$text .= "  document.forms['rating_arh'].elements['x_value'].value = all_ids;\n";
				$text .= "  return true;\n";
				$text .= " }else{\n";
				$text .= "  alert(\"������ �� �������\");\n";
				$text .= "  return false;\n";
				$text .= " }\n";
				$text .= "}\n";
				$text .= "//-->\n";
				$text .= "</script>\n";
				$text .= "<FORM METHOD=\"post\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\" NAME=\"rating_arh\" onSubmit=\"return CheckSelect(this);\">\n";
				$text .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"tzrating\">\n";
				$text .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"\">\n";
				$text .= "<INPUT TYPE=\"hidden\" NAME=\"x_value\" VALUE=\"\">\n";
				$text .= "<INPUT TYPE=\"hidden\" NAME=\"ok\" VALUE=\"1\">\n";
				$text .= " <TD WIDTH=15><SMALL>[���]</SMALL><BR><input type=\"checkbox\" onclick=\"SelectAll(checked)\"></TD>\n";
			}
			$text .= " <TD><B>����� ��������</B></TD>\n";
			$text .= " <TD><B>����� ���������</B></TD>\n";
			$text .= " <TD><B>��������</B></TD>\n";
			$text .= " <TD>&nbsp;</TD>\n";
			$text .= "</TR>\n";

			$bgcolor[1] = '#F5F5F5';
			$bgcolor[2] = '#E4DDC5';
			$i=1;
			while($row = mysql_fetch_assoc($result)){

				if($i>2) $i=1;
				//}
			//	if($row["time2"]>time()) $bgcolor="green";
				$text .= '<TR BGCOLOR="'.$bgcolor[$i]."\">\n";
				if($action=='check' || $action=='on_check'){
					$text .= ' <TD><INPUT TYPE="checkbox" NAME="item_id" VALUE="'.$row['id']."\"></TD>\n";
				}

				$sSQL2 = 'SELECT * FROM `'.$db['tz_users'].'` WHERE `id`=\''.$row['name_id'].'\'';
				$result2 = mysql_query($sSQL2, $connection);
				$row2 = mysql_fetch_assoc($result2);

				if($row2['clan_id']>0){
					$sSQL3 = 'SELECT * FROM `'.$db['tz_clans'].'` WHERE `id`=\''.$row2['clan_id'].'\'';
					$result3 = mysql_query($sSQL3, $connection);
					$row3 = mysql_fetch_assoc($result3);
					$clan = '{_CLAN_}'.trim($row3['name']).'{_/CLAN_}';
				}else{
					$clan = '';
				}

				$text .= ' <TD ALIGN="center">'.date("d:m:Y H:i", $row['time'])."</TD>\n";

				$text .= ' <TD><A HREF="javascript:{}"><IMG SRC="{_SERVER_NAME_}/i/bullet-red-01a.gif" BORDER=0 width="18" height="11" OnClick="ClBrd(\''.stripslashes($row2['name']).'\');" ALT="����������� ��� � ����� ������"></A> '.$clan.' '.stripslashes($row2['name']).'</A> ['.$row2['level'].'] {_PROF_}'.$row2['pro'].(($row2['sex']=='0')?'w':'')."{_/PROF_}\n<SMALL><BR>".stripslashes(nl2br($row['text']))."</SMALL>\n";
				$sSQLm = 'SELECT COUNT(id) FROM `'.$db['rating_check'].'` WHERE `name_id`=\''.$row['name_id'].'\'';
				$resultm = mysql_query($sSQLm, $connection);
				$mrow = mysql_fetch_array($resultm);
				$m_nrows = $mrow[0]-1;
				if($m_nrows>0){
					$text .= '<DIV><SMALL><A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating&action=search&ok=1&x_value[0]='.urlencode(stripslashes($row2['name'])).'">������ �������� ['.$m_nrows."]</A>\n";

					$sSQLm = 'SELECT `time` FROM `'.$db['rating_check'].'` WHERE `status`=3 AND `name_id`=\''.$row['name_id'].'\' ORDER BY time DESC';
					$resultm = mysql_query($sSQLm, $connection);
					$m_nrows1 = mysql_num_rows($resultm);
					$rowm = mysql_fetch_assoc($resultm);
					if($rowm>0){
						$text .= '<BR>��� ��������� - '.$m_nrows1;
						$text .= ' ['.date("d:m:Y H:i", $rowm['time']).']';
					}

					$sSQLm = 'SELECT `time` FROM `'.$db['rating_check'].'` WHERE `status`=4 AND `name_id`=\''.$row['name_id'].'\' ORDER BY `time` DESC';
					$resultm = mysql_query($sSQLm, $connection);
					$m_nrows2 = mysql_num_rows($resultm);
					$rowm = mysql_fetch_assoc($resultm);

					if($rowm>0){
						$text .= '<BR>�������� - '.$m_nrows2;
						$text .= ' ['.date("d:m:Y H:i", $rowm['time']).']';
					}
					$text .= "</SMALL></DIV>\n";
				}
				$text .= "</TD>\n";

				$sSQL2 = 'SELECT * FROM `site_users` WHERE `id`=\''.$row['uid'].'\'';
				$result2 = mysql_query($sSQL2, $connection);
				$row2 = mysql_fetch_assoc($result2);
				$sSQL2 = 'SELECT * FROM `'.$db['tz_users'].'` WHERE `name`=\''.$row2['user_name'].'\'';
				$result2 = mysql_query($sSQL2, $connection);
				$row2 = mysql_fetch_assoc($result2);
				if($row2['clan_id']>0){
					$sSQL3 = 'SELECT `name` FROM `'.$db['tz_clans'].'` WHERE `id`=\''.$row2['clan_id'].'\'';
					$result3 = mysql_query($sSQL3, $connection);
					$row3 = mysql_fetch_assoc($result3);
					$clan = '{_CLAN_}'.trim($row3['name']).'{_/CLAN_}';
				}else{
					$clan = '';
				}
				$text .= ' <TD>'.$clan.' '.stripslashes($row2['name']).'</A> ['.$row2['level'].'] {_PROF_}'.$row2['pro'].(($row2['sex']=='0')?'w':'')."{_/PROF_}</TD>\n";

				$text .= ' <TD><A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating&action=user_edit&id='.$row['id']."\" TITLE=\"������ ��������\">[ ������ �������� &#187; ]</A></TD>\n";

				$text .= "</TR>\n";

				$i++;
			}
			if($action=='check' || $action=='on_check'){
				$text .= "<TR>\n";
				$text .= ' <TD COLSPAN="7" ALIGN="left">';

				if($action=='check'){
					$text .= "<input type=\"submit\" class=\"submit\" OnClick=\"document.forms['rating_arh'].elements['action'].value = 'add_to_check3'\" value=\"��� ��������� >>\">";
					$text .= " <input type=\"submit\" class=\"submit\" OnClick=\"document.forms['rating_arh'].elements['action'].value = 'add_to_check4'\" value=\"�������� >>\">";
				}else{
			//	$action=="on_check"
					$text .= "<input type=\"submit\" class=\"submit\" OnClick=\"document.forms['rating_arh'].elements['action'].value = 'add_to_check2'\" value=\"������ �������� >>\">";
				}


				$text .= "</TD>\n";
				$text .= "</TR>\n";
				$text .= "</FORM>\n";
			}
			$text .= "</TABLE>\n";

			$text .= $ttext;
		}else{
			$text .= '<CENTER>������ ���</CENTER>';
		}


		return $text;
	}


// ---------- ���� �� ����������� ����� ----------
	function rating_users_activ ($connection, $db, $id, $ok, $x_value){

		$where='id = \''.intval($id).'\'';

		$sSQL = 'SELECT * FROM `'.$db['rating_check'].'` WHERE '.$where;
		$result = mysql_query($sSQL, $connection);

		if(mysql_num_rows($result)>0){
			$text = '';
			if($ok==1){
				$x_value[2] = addslashes(trim($x_value[2]));

			//	$inserted = "status = '".$x_value[1]."', text = '".$x_value[2]."', time = '".time()."', `uid`='".AuthUserId."'";
				$inserted = '`status` = \''.$x_value[1].'\', `text` = \''.$x_value[2].'\', `time` = \''.time().'\'';

				$sSQL2 = 'UPDATE `'.$db['rating_check'].'` SET '.$inserted.' WHERE `id`='.$id;

				if (mysql_query($sSQL2, $connection))
					$text = "<center><FONT COLOR=green><BIG><B>���������</B></BIG></FONT></center>\n";
				else
					$text = "<center><FONT COLOR=red><BIG><B>������ ��� ����������</B></BIG></FONT></center>\n";
			}

		//	$sSQL = "SELECT * FROM ".$db["rating_check"]." WHERE ".$where."";
		//	$result = mysql_query($sSQL, $connection);
			$row = mysql_fetch_assoc($result);

			$x_value[1] = $row['status'];
			$x_value[2] = stripslashes(htmlspecialchars($row['text']));


			$sSQL2 = 'SELECT * FROM `'.$db['tz_users'].'` WHERE `id`=\''.$row['name_id'].'\'';
			$result2 = mysql_query($sSQL2, $connection);
			$row2 = mysql_fetch_assoc($result2);

			$form .= title('�������� '.stripslashes($row2['name']));

			$form .= "   <FORM NAME=\"company\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\" METHOD=\"post\">\n";
			$form .= "    <INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"tzrating\">\n";
			$form .= "    <INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"user_edit\">\n";
			$form .= "    <INPUT TYPE=\"hidden\" NAME=\"id\" VALUE=\"".$id."\">\n";
			$form .= "    <INPUT TYPE=\"hidden\" NAME=\"ok\" VALUE=\"1\">\n";
			$form .= "    <TABLE WIDTH=\"98%\" CELLSPACING=\"2\" CELLPADDING=\"0\" BORDER=\"0\" CLASS=\"size-10\" align=center>\n";

			$form .= " <TR VALIGN=\"top\">\n";
			$form .= "  <TD ALIGN=\"right\"><B>������:&nbsp;</B></TD>\n";
			$form .= "  <TD>\n";
			$form .= "   <SELECT NAME=\"x_value[1]\">\n";
			$form .= "    <OPTION VALUE=\"2\"".(($x_value[1]==2)?" SELECTED":"")." CLASS=\"select\">&nbsp;�����������&nbsp;</OPTION>\n";
			$form .= "    <OPTION VALUE=\"3\"".(($x_value[1]==3)?" SELECTED":"")." CLASS=\"select\">&nbsp;��������: ��������� ���&nbsp;</OPTION>\n";
			$form .= "    <OPTION VALUE=\"4\"".(($x_value[1]==4)?" SELECTED":"")." CLASS=\"select\">&nbsp;��������: ��������&nbsp;</OPTION>\n";
			$form .= "   </SELECT>\n";
			$form .= "  </TD>\n";
			$form .= " </TR>\n";

			$form .= "     <TR>\n";
			$form .= "      <TD ALIGN=\"right\" VALIGN=\"top\"><B>�����������:</B>&nbsp;</TD>\n";
			$form .= "      <TD ALIGN=\"left\">\n";
			$form .= "       <TEXTAREA NAME=\"x_value[2]\" COLS=\"80\" ROWS=\"20\" WRAP=\"virtual\">".$x_value[2]."</TEXTAREA>\n";
			$form .= "      </TD>\n";
			$form .= "     </TR>\n";

			$form .= "     <TR>\n";
			$form .= "      <TD></TD>\n";
			$form .= "      <TD ALIGN=\"left\">\n";
			$form .= "       <INPUT TYPE=\"submit\" NAME=\"add\" VALUE=\"���������&nbsp;&gt;&gt;\" CLASS=\"submit\">\n";
			$form .= "      </TD>\n";
			$form .= "     </TR>\n";
			$form .= "    </TABLE>\n";
			$form .= "   </FORM>\n";

			$text = $text . $form;

		}

		return $text;
	}


// ---------- ������ ���������� �� �������� ----------
	function rating_users_insert ($connection, $db, $id, $ok, $x_value){

		$text = title('������ ���������� �� ��������');
		if($ok==1){

			if ($id != '1') $id='0';

			$x_value = trim($x_value);
			if($x_value!=''){
				$x_value = eregi_replace("\n",',',$x_value);
				if(eregi(',',$x_value)){
					$x_value = explode(',',$x_value);
					foreach($x_value AS $i){
						$i = trim($i);
					}
				}else{
					$x_value=array($x_value);
				}
				$x_value = array_unique($x_value);

				$f=1;
				$names=array();
				foreach($x_value AS $i){

				// ���� 101�� ������� - ��������� ���������
					if($f>100) break;

					$i = trim($i);
					if($i!=''){
					// ������� � ��
//						$tmp_page = fant_TZConn($i, 0);
						
                                          
                                          // Inviz 26.10.11
                                          $userinfo = GetUserInfo($i, 0);
                                          //$tmp_page = TZConn($i, 0); 
					// ���� ��������� ���� � �� ������
						//if(!is_array($tmp_page)){
						// ������ ����
							//$userinfo = fant_ParseUserInfo($tmp_page);
					//		print_r($userinfo);
						// ���������/��������� ���������� ���� � ���� ����
							//fant_tz_users_update($userinfo);
						//��������� �� ��������
							if($id=='1'){
							// �������� id ����
								$sSQL2 = 'SELECT `id` FROM `'.$db['tz_users'].'` WHERE `name`=\''.$userinfo['login'].'\'';
								$result2 = mysql_query($sSQL2, $connection);
								$row2 = mysql_fetch_assoc($result2);

								$sSQL1 = 'SELECT COUNT(id) FROM `'.$db['rating_check'].'` WHERE `name_id`=\''.$row2['id'].'\' AND `status` IN (1, 2)';
								$result1 = mysql_query($sSQL1, $connection);
								$row1 = mysql_fetch_array($result1);
								if($row1[0]>0){
									$names[] = $row2['id'];

								}else{
								// "�����������" ��� "������������ �� ��������" ��� - ��������� ����� ������
									$sSQL = 'INSERT INTO `'.$db['rating_check'].'` SET `name_id`=\''.$row2['id'].'\', `uid`=\''.AuthUserId.'\', `time`=\''.time().'\', `status`=1';

									if (mysql_query($sSQL, $connection)){
										$text .= $userinfo['login']." - <FONT COLOR=green><B>OK</B></FONT><BR>\n";
									}else{
										$text .= $userinfo['login']." - <FONT COLOR=red><B>������ ��� ����������</B></FONT><BR>\n";
									}
								}

							}else{
								$text .= $userinfo['login']." - <FONT COLOR=green><B>������ ������ ��������/�������� =)</B></FONT><BR>\n";
							}
						//}else{
						//	$text .= $i.' - <FONT COLOR=red><B>������: '.$tmp_page['error']."</B></FONT><BR>\n";
						//}

						$f++;
					}

				}

				$text .= rating_add_recheck($connection, $db, $names, '0');

			}else{
				$text .= "<center><FONT COLOR=red><BIG><B>������ ������</B></BIG></FONT></center>\n";
			}

		}

		if($ok!=1){
			$form .= "   <FORM NAME=\"company\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\" METHOD=\"post\">\n";
			$form .= "    <INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"tzrating\">\n";
			$form .= "    <INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"user_insert\">\n";
			$form .= "    <INPUT TYPE=\"hidden\" NAME=\"ok\" VALUE=\"1\">\n";
			$form .= "    <TABLE WIDTH=\"98%\" CELLSPACING=\"2\" CELLPADDING=\"0\" BORDER=\"0\" CLASS=\"size-10\" align=center>\n";

			$form .= "     <TR>\n";
			$form .= "      <TD ALIGN=\"right\" VALIGN=\"top\"><NOBR><B>���(�):</B>&nbsp;</NOBR></TD>\n";
			$form .= "      <TD ALIGN=\"left\">\n";
			$form .= "       <TEXTAREA NAME=\"x_value\" COLS=\"90\" ROWS=\"10\" WRAP=\"virtual\"></TEXTAREA>\n";
			$form .= "       <BR><INPUT TYPE=\"checkbox\" NAME=\"id\" VALUE=\"1\" CHECKED> - ��������� �� ��������<BR><SMALL>(���� �� �������� - �� �������� ������ ����������� � ���� ���������� ��, � ���� �������� �� �������)</SMALL>\n";

			$form .= "      <BR><BR><B>*�����������</B> - \"<B>,</B>\" (�������) �/��� \"<B>Enter</B>\"";
			$form .= "       <BR><B>!!!</B> ����� ������� ������ ���������� �� ���������, �.�. �� ������� ���� ������������� ���� � ������� ��\n";
			$form .= "       <BR><B>!!!</B> � ����� �������������� ������� �������� ������ - �������������� ������ ������ 100 �����, ��� ��� ������� ����� 100�� �� ��������������.\n";
			$form .= "     </TD>\n";
			$form .= "     </TR>\n";

			$form .= "     <TR>\n";
			$form .= "      <TD></TD>\n";
			$form .= "      <TD ALIGN=\"left\">\n";
			$form .= "       <INPUT TYPE=\"submit\" NAME=\"add\" VALUE=\"��������&nbsp;&gt;&gt;\" CLASS=\"submit\">\n";
			$form .= "      </TD>\n";
			$form .= "     </TR>\n";
			$form .= "    </TABLE>\n";
			$form .= "   </FORM>\n";
		}

		$text = $text . $form;

		return $text;
	}

	function rating_add_recheck($connection, $db, $x_value, $ok){

		if($ok!=1){
			if(sizeof($x_value)>0){
				$text .= "<BR><B>������ �����, ������� ��� \"���������� �� ��������\" ��� \"�����������\":</B>\n";
				$text .= "<TABLE WIDTH=\"100%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\">\n";
				$text .= "<TR BGCOLOR=\"#E4DDC5\" ALIGN=\"center\">\n";

				$text .= "<script language=\"JavaScript\">\n";
				$text .= "<!--\n";
				$text .= "function SelectAll(mark){\n";
				$text .= " for (i = 0; i < document.forms['rating_arh'].elements.length; i++){\n";
				$text .= "  var item = document.forms['rating_arh'].elements[i];\n";
				$text .= "  if (item.name == \"item_id\"){\n";
				$text .= "   item.checked = mark;\n";
				$text .= "  };\n";
				$text .= " }\n";
				$text .= "}\n";
				$text .= "function CheckSelect(form){\n";
				$text .= " var all_ids='';\n";
				$text .= " var d=0;\n";
				$text .= " for (i = 0; i < form.elements.length; i++){\n";
				$text .= "  var item = form.elements[i];\n";
				$text .= "  if (item.name == \"item_id\"){\n";
				$text .= "   if (item.checked){\n";
				$text .= "    d=1;\n";
				$text .= "    all_ids = all_ids +\"|\"+ item.value;\n";
				$text .= "   }\n";
				$text .= "  }\n";
				$text .= " }\n";
				$text .= " if(d==1){\n";
				$text .= "  document.forms['rating_arh'].elements['x_value[0]'].value = all_ids;\n";
				$text .= "  return true;\n";
				$text .= " }else{\n";
				$text .= "  alert(\"������ �� �������\");\n";
				$text .= "  return false;\n";
				$text .= " }\n";
				$text .= "}\n";
				$text .= "//-->\n";
				$text .= "</script>\n";
				$text .= "<FORM METHOD=\"post\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\" NAME=\"rating_arh\" onSubmit=\"return CheckSelect(this);\">\n";
				$text .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"tzrating\">\n";
				$text .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"\">\n";
				$text .= "<INPUT TYPE=\"hidden\" NAME=\"x_value[0]\" VALUE=\"\">\n";
				$text .= "<INPUT TYPE=\"hidden\" NAME=\"ok\" VALUE=\"1\">\n";
				$text .= " <TD WIDTH=15><SMALL>[���]</SMALL><BR><input type=\"checkbox\" onclick=\"SelectAll(checked)\"></TD>\n";

				$text .= " <TD><B>���</B></TD>\n";
				$text .= " <TD><B>�����</B></TD>\n";
				$text .= " <TD><B>������</B></TD>\n";
				$text .= " <TD><B>���������� � ������� ���������</B></TD>\n";
				$text .= "</TR>\n";

				$bgcolor[1]="#F5F5F5";
				$bgcolor[2]="#E4DDC5";
				$i=1;

				foreach($x_value AS $key=>$value){
					if($i>2) $i=1;

					$text .= "<TR BGCOLOR=\"".$bgcolor[$i]."\">\n";
					$sSQL1 = 'SELECT `id`, `text`, `time`, `status` FROM `'.$db['rating_check'].'` WHERE `name_id`='.$value.' AND `status` IN (1, 2) ORDER BY `time` DESC LIMIT 1;';
					$result1 = mysql_query($sSQL1, $connection);
					$row1 = mysql_fetch_assoc($result1);

					$text .= ' <TD><INPUT TYPE="checkbox" NAME="item_id" VALUE="'.$row1['id']."\"></TD>\n";

					$sSQL2 = 'SELECT `name` FROM `'.$db['tz_users'].'` WHERE `id`=\''.$value.'\'';
					$result2 = mysql_query($sSQL2, $connection);
					$row2 = mysql_fetch_assoc($result2);

					$text .= ' <TD><NOBR>{_PERS_}'.$row2['name']."{_/PERS_}</NOBR>\n<DIV><SMALL>".stripslashes(nl2br($row1['text']))."</SMALL></DIV>\n</TD>\n";

					$text .= ' <TD>'.date("d:m:Y H:i", $row1['time'])."</TD>\n";
					$text .= ' <TD>';
					if($row1['status']=='1'){
						$text .= '��������� �� ��������';
					}elseif($row1['status']=='2'){
						$text .= '�����������';
					}elseif($row1['status']=='3'){
						$text .= '����';
					}elseif($row1['status']=='4'){
						$text .= '��������';
					}
					$text .= "</TD>\n";
					$text .= ' <TD><INPUT SIZE=50 TYPE="text" NAME="x_value[1]['.$row1['id'].']" VALUE="'.(($row1['status']=='2')?'����������� � '.date("d:m:Y H:i", $row1['time']):'')."\"></TD>\n";

				//	$text .= " <TD>&nbsp;</TD>\n";
					$text .= "</TR>\n";
				}
				$text .= "<TR>\n";
				$text .= " <TD COLSPAN=\"7\" ALIGN=\"left\">";

				$text .= "<input type=\"submit\" class=\"submit\" OnClick=\"document.forms['rating_arh'].elements['action'].value = 'add_rechek'\" value=\"�������� ��� ������ >>\"> - �������� ������� � ����������� ������ � ��������� �� � \"������������ �� ��������\"";

				$text .= "</TD>\n";
				$text .= "</TR>\n";
				$text .= "</FORM>\n";
				$text .= "</TABLE>\n";
			}
		}else{
			$x_value[0]=explode('|',$x_value[0]);

			$i=1;
			$ft=0;
			$size = sizeof($x_value[0]);
			while($i<$size){
				if(trim($x_value[0][$i])!=''){
					$sSQL = 'SELECT `text` FROM `'.$db['rating_check'].'` WHERE `id`=\''.intval(trim($x_value[0][$i])).'\'';
					$result = mysql_query($sSQL, $connection);
					$row = mysql_fetch_assoc($result);
					$comment = $row['text'];
					if($x_value[1][$x_value[0][$i]]!=''){
						$comment = $x_value[1][$x_value[0][$i]]."\n".$comment;
					}

				//	$sSQL = "UPDATE `".$db["rating_check"]."` SET `text`='".$comment."', `uid`='".AuthUserId."', `time`='".time()."', `status`='1' WHERE `id`='".intval(trim($x_value[0][$i]))."'";
					$sSQL = 'UPDATE `'.$db['rating_check'].'` SET `text`=\''.$comment.'\', `time`='.time().', `status`=1 WHERE `id`=\''.intval(trim($x_value[0][$i])).'\'';

					if(mysql_query($sSQL,$connection))
						$ft++;
				}
				$i++;
			}
			$text = title('���������� �� �������� - '.$ft.' �����');
			$text .= rating_users_on_check ('on_check', $connection, $db, $page);
		}


		return $text;
	}


// ---------- ����� ���������� -------------
	function rating_stars_view ($full_access, $months_a, $menu, $cid, $time, $connection, $db, $id, $action, $max_level, $basepvp){
    global $bases;
	global $pvplink;
	global $pvptitle;
	global $pvpkind;
	// ���������� ��������� ��������
		if(!isset($time) || $time==0){
			$time = time();
		}elseif(is_array($time)){
			$time = mktime(23, 0, 0, $time[1], $time[0], $time[2]);
		}
		$ttime = $time;

	//	�������������
		$time = date("d:m:Y:H:i", $time);
		$c_time = $time = explode(':',$time);
	//	$time1 = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
		$time = mktime(23, 0, 0, $time[1], $time[0], $time[2]);
		$time = $time+3600;

		$text = title('���������� ������ �� '.date("d.m.Y", $ttime));

		$text .= "<BR><DIV STYLE=\"WIDTH: 100%;\" ALIGN=\"right\">\n";
		$text .= "<FORM METHOD=\"GET\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\">\n";
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"tzrating\">\n";
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"stars\">\n";
		$text .= $pvpkind[$basepvp];
		$text .= "<TABLE WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\">\n";
		$text .= " <TR VALIGN=\"top\">\n";
		$text .= "  <TD ALIGN=\"right\"><B>����:&nbsp;</B></TD>\n";
		$text .= "  <TD>\n";
		$text .= "   <SELECT NAME=\"time[0]\">\n";
		for($i=1;$i<=31;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[0])?" selected":"")." CLASS=\"select\">".$i."\n";
		}
		$text .= "   </SELECT><SELECT NAME=\"time[1]\">\n";
		for($i=1;$i<=12;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[1])?" selected":"")." CLASS=\"select\">".$months_a[$i]."\n";
		}
		$text .= "   </SELECT><SELECT NAME=\"time[2]\">\n";
		for($i=2006;$i<=date("Y");$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[2])?" selected":"")." CLASS=\"select\">".$i."\n";
		}
//		$text .= "   </SELECT>&nbsp;<B>���:&nbsp;</B><SELECT NAME=\"time[3]\">\n";
//		for($i=0;$i<=23;$i++){
//			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[3])?" selected":"")." CLASS=\"select\">".(($i<10)?"0":"")."".$i."\n";
//		}
	//	$text .= "   </SELECT>&nbsp;<B>�������:&nbsp;</B><SELECT NAME=\"cid\">\n";
	//	$text .= "    <OPTION VALUE='0'>-���-";
	//	for($i=1;$i<=$max_level;$i++){
	//		$text .= "<OPTION VALUE='".$i."'".(($i==$cid)?" selected":"")." CLASS=\"select\">".$i."\n";
	//	}
	//	$text .= "   </SELECT>\n";
	//	$text .= "   <B>���:</B> <input type=\"input\" class=\"text\" NAME=\"name\" value=\"".htmlspecialchars(trim($name))."\">\n";
		$text .= "   <input type=\"submit\" class=\"submit\" value=\"�������� >>\">\n";
		$text .= "  </TD>\n";
		$text .= " </TR>\n";
		$text .= "</FORM>\n";
		$text .= "</TABLE><BR>\n";


	// ����. ����
		$l_time = $ttime-86400;
		$l_time = date("d:m:Y:H:i", $l_time);
		$l_time = explode(":",$l_time);
		$text .= "<CENTER><A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating".$pvplink[$basepvp]."&action=stars&time[0]=".$l_time[0]."&time[1]=".$l_time[1]."&time[2]=".$l_time[2]."\"><<< ����. ����</A>\n";


	// ����. ����
		$n_time = $ttime+86400;
		$n_time = date("d:m:Y:H:i", $n_time);
		$n_time = explode(":",$n_time);
		$text .= " &nbsp;&nbsp;|&nbsp;&nbsp; <A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating".$pvplink[$basepvp]."&action=stars&time[0]=".$n_time[0]."&time[1]=".$n_time[1]."&time[2]=".$n_time[2]."\">����. ���� >>></A></CENTER>\n";
		$text .= "<BR></DIV>\n";


		$where = array();
		if(isset($cid) && $cid>0 ){
			$where[] = "`cid`='".anti_sql_injection($cid)."'";
		}
		$where[] =  "`time`='".$time."'";
	//	$where[] = '1=1';
		if(sizeof($where>1)){
			$where = implode(' AND ', $where);
		}else{
			$where = $where[0];
		}

		$sSQL = 'SELECT * FROM `'.$bases[$basepvp].'` WHERE ('.$where.') AND `star`=1 ORDER BY `cid` DESC, `place` ASC';
		$result = mysql_query($sSQL,$connection);
		$nrows=mysql_num_rows($result);
		if($nrows>0){
			$text .= "<TABLE WIDTH=\"100%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\">\n";
			$text .= "<TR BGCOLOR=\"#E4DDC5\" ALIGN=\"center\">\n";
			$text .= " <TD><B>�</B></TD>\n";
			$text .= " <TD><B>����� ���������</B></TD>\n";
			$text .= " <TD><B>��������� �� ���� ����</B></TD>\n";
			$text .= " <TD><B>���</B></TD>\n";
			$text .= " <TD><B>������</B></TD>\n";
			$text .= " <TD><B>���������</B></TD>\n";
			$text .= "</TR>\n";

			$bgcolor[1]='#F5F5F5'; //���� 1
			$bgcolor[2]='#E4DDC5'; //���� 2
			$bgcolor[3]='#FFCC99'; //1 - ��������� �� ��������
			$bgcolor[4]='#EAC987'; //2 - �����������
			$bgcolor[5]='#CCFFCC'; //3 - ����������� - ����
			$bgcolor[6]='#FF9987'; //4 - ����������� - ��������

			while($row = mysql_fetch_assoc($result)){
				if($c_cid != $row['cid']){
					$i=1;
					$c_cid = $row['cid'];
					$text .= "<TR>\n";
					$text .= ' <TD COLSPAN=7><B><I>'.$row['cid']." �������</I></B></TD>\n";
					$text .= "</TR>\n";
				}

				if($i>2) $i=1;

				$text2 = '';
				if($full_access==1){
					$sSQL1 = 'SELECT `time`, `uid`, `status` FROM `'.$db['rating_check'].'` WHERE `name_id`='.$row['name_id'];
					$result1 = mysql_query($sSQL1, $connection);
					if(mysql_num_rows($result1)>0){
						$row1 = mysql_fetch_assoc($result1);

					// ����� ������� ���� ���� ��� �� ������ 24 �����
						if(($row1['time']+86400)>time()){

							$sSQL2 = 'SELECT `user_name` FROM `site_users` WHERE `id`='.$row1['uid'];
							$result2 = mysql_query($sSQL2, $connection);
							$row2 = mysql_fetch_assoc($result2);

						// ������������ �� ��������
							if($row1['status']=='1'){
								$i=3;
								$text2 = '<BR><SMALL>[ '.date("d:m:Y H:i", $row1['time']).' ��������� �� ��������   '.stripslashes($row2['user_name'])." ]</SMALL>\n";

						// �����������
							}elseif($row1['status']=='2'){
								$i=4;
								$text2 = '<BR><SMALL>[ '.date("d:m:Y H:i", $row1['time']).' �����������   '.stripslashes($row2['user_name'])." ]</SMALL>\n";
						// ����������� +
							}elseif($row1['status']=='3'){
								$i=5;
								$text2 = '<BR><SMALL>[ '.date("d:m:Y H:i", $row1['time']).' ����   '.stripslashes($row2['user_name'])." ]</SMALL>\n";

						// ����������� -
							}elseif($row1['status']=='4'){
								$i=6;
								$text2 = '<BR><SMALL>[ '.date("d:m:Y H:i", $row1['time']).' ��������   '.stripslashes($row2['user_name'])." ]</SMALL>\n";
							}
						}
					}
				}
				$text .= '<TR BGCOLOR="'.$bgcolor[$i]."\">\n";
				$text .= ' <TD ALIGN="center">'.stripslashes($row['place'])."</TD>\n";

				if($row['clan_id']>0){
					$sSQL2 = 'SELECT `name` FROM `'.$db['tz_clans']."` WHERE `id`='".$row["clan_id"]."'";
					$result2 = mysql_query($sSQL2, $connection);
					$row2 = mysql_fetch_assoc($result2);
					$clan = '{_CLAN_}'.trim($row2['name']).'{_/CLAN_}';
				}else{
					$clan = '';
				}
				$sSQL2 = 'SELECT `id`, `name`, `sex` FROM `'.$db['tz_users'].'` WHERE `id`='.$row['name_id'];
				$result2 = mysql_query($sSQL2, $connection);
				$row2 = mysql_fetch_assoc($result2);
			//	echo "| ".$clan." ".$name." ".$level." | ".$prof." |";
				$text .= " <TD><A HREF=\"javascript:{}\"><IMG SRC=\"{_SERVER_NAME_}/i/bullet-red-01a.gif\" BORDER=0 width=\"18\" height=\"11\" OnClick=\"ClBrd('".stripslashes($row2['name'])."');\" ALT=\"����������� ��� � ����� ������\"></A> ".$clan.' <A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating&action=view2&time[0]='.$c_time[0].'&time[1]='.$c_time[1].'&time[2]='.$c_time[2].'&cid='.$row2['id'].'">'.stripslashes($row2['name']).'</A> ['.$row['level'].'] {_PROF_}'.$row['pro'].(($row2['sex']=='0')?'w':'').'{_/PROF_}'.$text2."</TD>\n";

				$text .= ' <TD ALIGN="center">'.(($row['star']=='1')?'<IMG SRC="http://www.timezero.ru/i/manual/rsign.jpg" BORDER=0 WIDTH=34 HEIGHT=18><BR>':'').stripslashes($row['expa'])."</TD>\n";
				$text .= ' <TD ALIGN="center">'.($row['win']+$row['lost'])."</TD>\n";
				$text .= ' <TD ALIGN="center">'.stripslashes($row['win'])."</TD>\n";
				$text .= ' <TD ALIGN="center">'.stripslashes($row['lost'])."</TD>\n";
				$text .= "</TR>\n";


			//===========================================
			// ���������� �� ���������
				if($full_access==1){
					$sSQLx = 'SELECT * FROM `'.$bases[$basepvp].'` WHERE ('.$where.') AND `cid`='.$row['cid'].' AND `star`=0 AND `place`>'.$row['place'].' AND `pro`!=13 AND `expa`>'.(5000*$row['cid']).' ORDER BY cid DESC, place ASC LIMIT 1';
					$resultx = mysql_query($sSQLx, $connection);
					if(mysql_num_rows($resultx)>0){
						$rowx = mysql_fetch_assoc($resultx);

						$sSQL1 = 'SELECT * FROM `'.$db['rating_check'].'` WHERE `name_id`='.$rowx['name_id'];
						$result1 = mysql_query($sSQL1, $connection);
						if(mysql_num_rows($result1)>0){
							$row1 = mysql_fetch_assoc($result1);

						// ����� ������� ���� ���� ��� �� ������ 24 �����
							if(($row1['time']+86400)>time()){

								$sSQL2 = 'SELECT `user_name` FROM `site_users` WHERE `id`='.$row1['uid'];
								$result2 = mysql_query($sSQL2, $connection);
								$row2 = mysql_fetch_assoc($result2);

							// ������������ �� ��������
								if($row1['status']=='1'){
									$i=3;
									$text2 = '<BR><SMALL>[ '.date("d:m:Y H:i", $row1['time'])." ��������� �� ��������   ".stripslashes($row2['user_name'])." ]</SMALL>\n";

							// �����������
								}elseif($row1['status']=='2'){
									$i=4;
									$text2 = '<BR><SMALL>[ '.date("d:m:Y H:i", $row1['time'])." �����������   ".stripslashes($row2['user_name'])." ]</SMALL>\n";
							// ����������� +
								}elseif($row1['status']=='3'){
									$i=5;
									$text2 = '<BR><SMALL>[ '.date("d:m:Y H:i", $row1['time'])." ����   ".stripslashes($row2['user_name'])." ]</SMALL>\n";

							// ����������� -
								}elseif($row1['status']=='4'){
									$i=6;
									$text2 = '<BR><SMALL>[ '.date("d:m:Y H:i", $row1['time'])." ��������   ".stripslashes($row2['user_name'])." ]</SMALL>\n";
								}
							}
						}

						$text .= '<TR BGCOLOR="'.$bgcolor[$i]."\">\n";
						$text .= ' <TD ALIGN="center">'.stripslashes($rowx['place'])."</TD>\n";

						if($row['clan_id']>0){
							$sSQL2 = 'SELECT `name` FROM `'.$db['tz_clans'].'` WHERE `id`='.$rowx['clan_id'];
							$result2 = mysql_query($sSQL2, $connection);
							$row2 = mysql_fetch_assoc($result2);
							$clan = '{_CLAN_}'.trim($row2['name']).'{_/CLAN_}';
						}else{
							$clan = '';
						}
						$sSQL2 = 'SELECT `id`, `name`, `sex` FROM `'.$db['tz_users'].'` WHERE `id`='.$rowx['name_id'];
						$result2 = mysql_query($sSQL2, $connection);
						$row2 = mysql_fetch_assoc($result2);
					//	echo "| ".$clan." ".$name." ".$level." | ".$prof." |";
						$text .= " <TD><A HREF=\"javascript:{}\"><IMG SRC=\"{_SERVER_NAME_}/i/bullet-red-01a.gif\" BORDER=0 width=\"18\" height=\"11\" OnClick=\"ClBrd('".stripslashes($row2["name"])."');\" ALT=\"����������� ��� � ����� ������\"></A> ".$clan.' <A HREF="{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating&action=view2&time[0]='.$c_time[0].'&time[1]='.$c_time[1].'&time[2]='.$c_time[2].'&cid='.$row2['id'].'">'.stripslashes($row2["name"]).'</A> ['.$rowx['level'].'] {_PROF_}'.$rowx['pro'].(($row2['sex']=='0')?'w':'').'{_/PROF_}'.$text2."</TD>\n";

						$text .= ' <TD ALIGN="center">'.(($rowx['star']=='1')?'<IMG SRC="http://www.timezero.ru/i/manual/rsign.jpg" BORDER=0 WIDTH=34 HEIGHT=18><BR>':'').stripslashes($rowx['expa'])."</TD>\n";
						$text .= ' <TD ALIGN="center">'.($rowx['win']+$rowx['lost'])."</TD>\n";
						$text .= ' <TD ALIGN="center">'.stripslashes($rowx['win'])."</TD>\n";
						$text .= ' <TD ALIGN="center">'.stripslashes($rowx['lost'])."</TD>\n";
						$text .= "</TR>\n";
					}
				}
			//===========================================

				$i++;
			}
			$text .= "</TABLE>\n";
		}else{
			$text .= '<CENTER>����� �� ������������ ���������� ���</CENTER>';
		}

		return $text;
	}



/*	function fant_persparse($pers){

		$returntxt = "[pers clan=0 nick={$pers} level=1 pro=0]";
		$pers=trim($pers);
//		echo ($returntxt);

		$userinfo = GetUserInfo($pers);
	//	echo ("<pre>");
	//	print_r($userinfo);
	//	echo ("</pre>");
		if (!$userinfo["error"] && $userinfo['level'] > 0){
			if ($userinfo['man'] == 0){
				$pro = $userinfo['pro']."w";

			}else{
				$pro = $userinfo['pro'];
			}

			if (strlen($userinfo['clan']) > 2){
				$returntxt = "[pers clan={$userinfo['clan']} nick={$userinfo['login']} level={$userinfo['level']} pro={$pro}]";
			}else{
				$returntxt = "[pers clan=0 nick={$userinfo['login']} level={$userinfo['level']} pro={$pro}]";
			}
		}else{
			$returntxt = "[pers clan=0 nick={$pers} level=1 pro=0]";
		}

		return ($returntxt);

	}
*/


// ---------- ����� ���������� �� ����� �� -------------
	function all_tz_users_view ($prof_alt, $max_level, $connection, $db){

/*		$text = "fgsg g dsgg [pers]FANTASTISH[/pers] df gsf gsdfg";

		$text = preg_replace_callback("/\[pers\](.*?)\[\/pers\]/si", create_function('$match', 'return fant_persparse($match[1]);'), $text);
		print_r($text);
*/
		$text = title('���� �� ���������� ��');

		$text .= "<HR size=1>\n";

		$text .= "<TABLE BORDER=0 CELLSPACING=\"2\" CELLPADDING=\"2\" BORDER=\"0\">\n";
		$text .= " <TR>\n";

		$text .= "  <TD VALIGN=top>\n";
		$total_nrows=0;

//		$where = "`level`='0' AND `name` != '0'";
	//	$where = "`level`='0'";
//		$sSQL = "SELECT COUNT(id) FROM `".$db["tz_users"]."` WHERE (".$where.")";
//		$result = mysql_query($sSQL,$connection);
//		$row = mysql_fetch_array($result);
//		$nrows = $row[0];
	//	$nrows = mysql_num_rows($result);

//		$text .= "<B>������������:</B> ".$nrows." ��������(��)<BR>\n";
	//	while($row = mysql_fetch_array($result)){
	//		$text .= $row["id"].": ".$row["name"]."<BR>\n";
	//	}

	//	mysql_query("DELETE FROM `".$db["tz_users"]."` where (id='34324')");

		$total_nrows=$total_nrows+$nrows;

//		for($i=1;$i<=30;$i++){
//			$where = "`level`='".$i."'";
//			$sSQL = "SELECT COUNT(id) FROM `".$db["tz_users"]."` WHERE (".$where.")";
//			$result = mysql_query($sSQL,$connection);
//			$row = mysql_fetch_array($result);
//			$nrows = $row[0];
//		//	$nrows = mysql_num_rows($result);
//			if($nrows>0){
//				$text .= "<B>".$i." �������:</B> ".$nrows." ��������(��)<BR>\n";
//
//				$total_nrows=$total_nrows+$nrows;
//			}
//		}

		$sSQL = 'SELECT `level`, COUNT(*) AS `cnt` FROM `'.$db['tz_users'].'` GROUP BY `level` ORDER BY `level` ASC';
		$result = mysql_query($sSQL,$connection);
		while($row = mysql_fetch_assoc($result)){
			$text .= '<B>'.$row['level'].' �������:</B> '.$row['cnt']." ��������(��)<BR>\n";
			$total_nrows = $total_nrows + $row['cnt'];
		}

		$text .= "  </TD>\n";

		$text .= "  <TD VALIGN=top>\n";
	/*	foreach($prof_alt AS $key=>$i){
			$where = "`pro`='".$key."'";
			if($key=="0"){
				$where .= " OR `pro`=''";
			}
			$sSQL = "SELECT COUNT(id) FROM `".$db["tz_users"]."` WHERE (".$where.")";
			$result = mysql_query($sSQL,$connection);
			$row = mysql_fetch_array($result);
			$nrows = $row[0];
		//	$nrows = mysql_num_rows($result);

			$text .= '<B>{_PROF_}'.$key.'{_/PROF_}'.$i.':</B> '.$nrows." ��������(��)<BR>\n";

		}
	*/
		$sSQL = 'SELECT `pro`, COUNT(*) AS `cnt` FROM `'.$db['tz_users'].'` GROUP BY `pro` ORDER BY `pro` ASC';
		$result = mysql_query($sSQL,$connection);
		while($row = mysql_fetch_assoc($result)){
			$text .= '<B>{_PROF_}'.$row['pro'].'{_/PROF_}'.$prof_alt[$row['pro']].':</B> '.$row['cnt']." ��������(��)<BR>\n";
		}

		$text .= "  </TD>\n";

		$text .= "  <TD VALIGN=top>\n";
		$where = '`block`!=\'\'';
		$sSQL = 'SELECT COUNT(*) FROM `'.$db['tz_users'].'` WHERE ('.$where.')';
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_array($result);
		$nrows = $row[0];
	//	$nrows = mysql_num_rows($result);
		$text .= '<B>���������:</B> '.$nrows." ��������(��)<BR>\n";

		$where = '`sex`=\'1\'';
		$sSQL = 'SELECT COUNT(*) FROM `'.$db['tz_users'].'` WHERE ('.$where.')';
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_array($result);
		$nrows = $row[0];
	//	$nrows = mysql_num_rows($result);
		$text .= '<B>�������:</B> '.$nrows." ��������(��)<BR>\n";

		$where = '`sex`=\'0\'';
		$sSQL = 'SELECT COUNT(*) FROM `'.$db['tz_users'].'` WHERE ('.$where.')';
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_array($result);
		$nrows = $row[0];
	//	$nrows = mysql_num_rows($result);
		$text .= '<B>�������:</B> '.$nrows." ��������(��)<BR>\n";

		$where = "`clan_id`!='0'";
		$sSQL = 'SELECT COUNT(*) FROM `'.$db['tz_users'].'` WHERE ('.$where.')';
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_array($result);
		$nrows = $row[0];
	//	$nrows = mysql_num_rows($result);
		$text .= '<B>� ������:</B> '.$nrows." ��������(��)<BR>\n";

		$text .= '<B>�����:</B> '.$total_nrows." ��������(��)<BR>\n";

		$sSQL = 'SELECT COUNT(*) FROM `data_cache`';
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_array($result);
		$nrows = $row[0];
	//	$nrows = mysql_num_rows($result);
		$text .= '<B>���:</B> '.$nrows." ��������(��)<BR>\n";
		$text .= "  </TD>\n";
		$text .= " </TR>\n";
		$text .= "</TABLE>\n";

		$text .= "<HR size=1>\n";

		$text .= "<B>��������� ����������� �����:</B><BR>\n";
		$sSQL = 'SELECT `name` FROM `'.$db['tz_clans'].'` ORDER BY `id` DESC LIMIT 50;';
		$result = mysql_query($sSQL,$connection);
		$text .= "<TABLE BORDER=0 CELLSPACING=\"2\" CELLPADDING=\"2\" BORDER=\"0\">\n";
		$text .= " <TR>\n";
		$i=1;
		while($row = mysql_fetch_assoc($result)){
			if($i>10) $i=1;
			if($i==1) $text .= "<TD VALIGN=top>\n";
			$text .= '{_CLAN_}'.trim($row['name']).'{_/CLAN_}'.$row['name']."<BR>\n";
			if($i==10) $text .= "</TD>\n";
			$i++;
		}
		$text .= " </TR>\n";
		$text .= "</TABLE>\n";

		$text .= "<HR size=1>\n";
		$sSQL = 'SELECT COUNT(*) FROM `'.$db['tz_clans'].'`';
		$result = mysql_query($sSQL,$connection);
		$row = mysql_fetch_array($result);
		$nrows = $row[0];
	//	$nrows = mysql_num_rows($result);

		$text .= '<B>�����:</B> '.$nrows." ������\n";

	//	mysql_query("DELETE FROM `".$db["tz_clans"]."` where (id='319')");

		$text .= "<HR size=1>\n";

		$text .= "<TABLE BORDER=0 CELLSPACING=\"2\" CELLPADDING=\"2\" BORDER=\"0\">\n";
		$text .= " <TR>\n";

		$text .= "  <TD VALIGN=top>\n";
		$text .= "<B>18 ������:</B><BR>\n";
		$sSQL = 'SELECT `name` FROM `'.$db['tz_users'].'` WHERE `level`=18 ORDER BY `name` ASC';
		$result = mysql_query($sSQL,$connection);
		while($row = mysql_fetch_assoc($result)){
			$text .= '{_PERS_}'.$row['name']."{_/PERS_}<BR>\n";
		}
		$text .= "  </TD>\n";

		$text .= "  <TD VALIGN=top>\n";
		$text .= "<B>19 ������:</B><BR>\n";
		$sSQL = 'SELECT `name` FROM `'.$db['tz_users'].'` WHERE `level`=19 ORDER BY `name` ASC';
		$result = mysql_query($sSQL,$connection);
		while($row = mysql_fetch_assoc($result)){
			$text .= '{_PERS_}'.$row['name']."{_/PERS_}<BR>\n";
		}
		$text .= "  </TD>\n";

		$text .= "  <TD VALIGN=top>\n";
		$text .= "<B>20 ������:</B><BR>\n";
		$sSQL = 'SELECT `name` FROM `'.$db['tz_users'].'` WHERE `level`=20 ORDER BY `name` ASC';
		$result = mysql_query($sSQL,$connection);
		while($row = mysql_fetch_assoc($result)){
			$text .= '{_PERS_}'.$row['name']."{_/PERS_}<BR>\n";
		}
		$text .= "  </TD>\n";

		$text .= "  <TD VALIGN=top>\n";
		$text .= "<B>24 ������:</B><BR>\n";
		$sSQL = 'SELECT name FROM `'.$db['tz_users'].'` WHERE `level`=\'24\' ORDER BY name ASC';
		$result = mysql_query($sSQL,$connection);
		while($row = mysql_fetch_assoc($result)){
			$text .= '{_PERS_}'.$row['name']."{_/PERS_}<BR>\n";
		}
		$text .= "  </TD>\n";

		$text .= " </TR>\n";
		$text .= "</TABLE>\n";

		$text .= "<HR size=1>\n";

		$text .= "<TABLE BORDER=0 CELLSPACING=\"2\" CELLPADDING=\"2\" BORDER=\"0\">\n";
		$text .= " <TR>\n";

		$text .= "  <TD VALIGN=top>\n";
		$text .= "<B>����������:</B><BR>\n";
		$sSQL = 'SELECT name FROM `'.$db['tz_users'].'` WHERE `pro`=\'10\' ORDER BY name ASC';
		$result = mysql_query($sSQL,$connection);
		while($row = mysql_fetch_assoc($result)){
			$text .= '{_PERS_}'.$row['name']."{_/PERS_}<BR>\n";
		}
		$text .= "  </TD>\n";

		$text .= "  <TD VALIGN=top>\n";
		$text .= "<B>���������:</B><BR>\n";
		$sSQL = 'SELECT name FROM `'.$db['tz_users'].'` WHERE `pro`=\'11\' ORDER BY name ASC';
		$result = mysql_query($sSQL,$connection);
		while($row = mysql_fetch_assoc($result)){
			$text .= '{_PERS_}'.$row['name']."{_/PERS_}<BR>\n";
		}
		$text .= "  </TD>\n";

		$text .= "  <TD VALIGN=top>\n";
		$text .= "<B>������:</B><BR>\n";
		$sSQL = 'SELECT name FROM `'.$db['tz_users'].'` WHERE `pro`=\'30\' ORDER BY name ASC';
		$result = mysql_query($sSQL,$connection);
		while($row = mysql_fetch_assoc($result)){
			$text .= '{_PERS_}'.$row['name']."{_/PERS_}<BR>\n";
		}
		$text .= "  </TD>\n";

		$text .= " </TR>\n";
		$text .= "</TABLE>\n";


		return $text;
	}


// ---------- ���������� ���������� �� ����� �� -------------
	function all_tz_users_update ($connection, $db){

		$text = title('���������� ���� �� ���������� ��');

		$text .= "<HR size=1>\n";

	// ��� ��� �� ��������� 2 ������
		$sSQL = 'SELECT `name`, `level` FROM `'.$db['tz_users'].'` ORDER BY `upd_time` ASC LIMIT 100';
		$result = mysql_query($sSQL,$connection);
		while($row = mysql_fetch_assoc($result)){
			$text .= $row['name'].' ['.$row['level'].']';

//			$tmp_page = fant_TZConn($row['name'], 0);
                        
                        // Inviz 26.10.11
                        $userinfo = GetUserInfo($row['name'], 0);
                        $text .= ' - Update<BR>\n';
			/*$tmp_page = TZConn($row['name'], 0); 
			if(!is_array($tmp_page)){
				$userinfo = fant_ParseUserInfo($tmp_page);
				fant_tz_users_update($userinfo);

				$text .= ' - Update';
			}else{
				$text .= ' - <B>'.$tmp_page['error'].'</B>';
			}

			$text .= "<BR>\n";*/
		}

		return $text;
	}


//===========================================================


?>