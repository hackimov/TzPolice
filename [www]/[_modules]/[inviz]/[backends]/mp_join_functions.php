<?php
$_RESULT = array("res" => "ok");
require_once("/home/sites/police/www/_modules/inviz/backends/mp_join_config.php"); // todo actualize?

$JsHttpRequest =& new Subsys_JsHttpRequest_Php("windows-1251");
#$JsHttpRequest = new Subsys_JsHttpRequest_Php("windows-1251"); // todo - comment?

function ShowJoinPages($CurPage,$TotalPages,$ShowMax) {
	global $action;
	global $target;
	global $param;
    $PrevList=floor(($CurPage-1)/$ShowMax)*$ShowMax;
    $NextList=$PrevList+$ShowMax+1;
        if($PrevList>=$ShowMax*2) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','1');\" title='� ����� ������'>�</a> ";
        if($PrevList>0) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','".$PrevList."');\" title='���������� ".$ShowMax." �������'>�</a> ";
    for($i=$PrevList+1;$i<=$PrevList+$ShowMax;$i++) if($i<=$TotalPages) {
            if($i==$CurPage) echo '<u>'.$i.'</u> ';
        else echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','{$i}');\">$i</a> ";
    }
    if($NextList<=$TotalPages) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','".$NextList."');\" title='��������� ".$ShowMax." �������'>�</a> ";
        if($CurPage<$TotalPages) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','".$TotalPages."');\" title='� ����� �����'>�</a>";
}


extract($_REQUEST);

// ------ ���������� ���� ��� ������ � �������� :: begin
if ($join_access && ($hide_menu != 1)) {
?>
	<table width="85%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td align="left" width='20%'>
				<?php
					$r = mysql_query(
						"
							SELECT *
							FROM `mp_join_entry`
							WHERE `status` = '1'
						", $db
					);
				?>
				<a href="javascript:{}" onclick="show_entry('lists','free','','1');">��������� ������ (<?php echo mysql_num_rows($r); ?>)</a>
			</td>
			<td align="left" width='20%'>
				<?php
					if ($join_revisor) {
						$r = mysql_query(
							"
								SELECT *
								FROM `mp_join_entry`
								WHERE
									`revisor` = '".AuthUserName."'
									AND `status` NOT IN ('1', '5', '6', '7')
							", $db
						);
				?>
				<a href="javascript:{}" onclick="show_entry('lists','my','','1');">��� ������ (<?php echo mysql_num_rows($r); ?>)</a>
				<?php
					} else {
				?>
					&nbsp;
				<?php
					}
				?>
			</td>
			<td align="left" width='20%'>
				<?php
					$r = mysql_query(
						"
							SELECT *
							FROM `mp_join_entry`
							WHERE `status` NOT IN ('1', '5', '6', '7')
						", $db
					);
				?>
				<a href="javascript:{}" onclick="show_entry('lists','inProgress','','1');">� ��������� (<?php echo mysql_num_rows($r); ?>)</a>
			</td>
			<td align="left" width='20%'><a href="javascript:{}" onclick="show_entry('lists','archive','','1');">�����</a></td>
			<td align="left" width='20%'>
				<?php
					$r = mysql_query(
						"
							SELECT *
							FROM `mp_join_entry`
							WHERE `status` = '7'
						", $db
					);
				?>
				<a href="javascript:{}" onclick="show_entry('lists','reserve','','1');">������ (<?php echo mysql_num_rows($r); ?>)</a>
			</td>
			<td align="left" width='20%'><a href="javascript:{}" onclick="load_page('statistics','show');">����������</a></td>
		</tr>
		<?php if ($join_admin) { ?>
		<tr height="6">
			<td align="left" colspan="5"></td>
		</tr>
		<tr>
			<td align="left"><a href="javascript:{}" onclick="show_search();">�����</a></td>
			<td align="left">
				<b>���������:</b><br />
				<a href="javascript:{}" onclick="load_page('edit_security','show');">������</a><br />
				<a href="javascript:{}" onclick="load_page('edit_questions','show');">������� ������</a>
			</td>
			<td align="left"><a href="javascript:{}">�������� ������</a>
			<select onchange="show_entry('lists','other',this.value,'1');">
<?php
			$SQL = 'SELECT `revisor` FROM `mp_join_entry` GROUP BY `revisor` ORDER BY `revisor` ASC';
			$r = mysql_query($SQL);
			while($d = mysql_fetch_assoc($r)) {
				echo '<option on onclick="show_entry(\'lists\',\'other\',\''.$d['revisor'].'\',\'1\');" value="'.$d['revisor'].'">'.$d['revisor'].'</option>';
			}
?>
			</select>
			</td>
			<td align="left"><a href="javascript:{}" onclick="load_page('show_actualy','show');">����������� ������</a></td>
			<td align="left"></td>
			<td align="left"></td>
		</tr>
		<tr id="search_form" style="display:none;">
			<td align="left" colspan="6"><input type="text" size="20" id="serach_nick" /><input type="button" value="  ������  " name="search_btn" onclick="search_entry();" /></td>
		</tr>
		<?php } ?>
	</table>
	<br /><br />
	<?php
	}
// ------ ���������� ���� ��� ������ � �������� :: end

// ------ ���������� :: begin
if (($action == 'statistics') && $join_access) {
	if ($target == 'show') {
		if (empty($statisticsStart)) {
			$statisticsStartTimeStamp = mktime() - 7 * 3600 * 24; // 7 days in past
			$statisticsStart = date("d/m/Y", $statisticsStartTimeStamp);
		} else {
			//$dateTemp = DateTime::createFromFormat("d/m/Y", $statisticsStart);
			//$statisticsStartTimeStamp = $dateTemp->getTimestamp();
			$statisticsStartTimeStamp = convertStringToTimestamp($statisticsStart, true);
		}

		if (empty($statisticsEnd)) {
			$statisticsEndTimeStamp = mktime();
			$statisticsEnd = date("d/m/Y", $statisticsEndTimeStamp);
		} else {
			//$dateTemp = DateTime::createFromFormat("d/m/Y", $statisticsEnd);
			//$statisticsEndTimeStamp = $dateTemp->getTimestamp();
			$statisticsEndTimeStamp = convertStringToTimestamp($statisticsEnd, false);
		}
		?>
		<table cellpadding=3 width='300' cellspacing=3 align='center'>
			<tr>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center' colspan=2>
					����������:
				</th>
			</tr>
			<tr>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="20">
					�:
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>
					<input
						type="text"
						name="statisticStart"
						id="statisticStart"
						value="<?php echo $statisticsStart; ?>"> (dd/mm/YYYY)
				</td>
			</tr>
			<tr>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>
					��:
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>
					<input
						type="text"
						name="statisticEnd"
						id="statisticEnd"
						value="<?php echo $statisticsEnd; ?>"> (dd/mm/YYYY)
				</td>
			</tr>
			<tr>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' colspan=2 align='center'>
					<input
						type="button"
						name="generateStatistics"
						onclick="generateStatistics(document.getElementById('statisticStart').value, document.getElementById('statisticEnd').value);"
						value="��������">
				</td>
			</tr>
			<tr>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' colspan=2 style='padding-top: 15px;'>
					<?php
						$r = mysql_query(
							"
								SELECT *
								FROM `mp_join_entry`
								WHERE `start_date` BETWEEN $statisticsStartTimeStamp AND $statisticsEndTimeStamp
							", $db
						);
					?>
					����� ���������: <b><?php echo mysql_num_rows($r);?></b>
					<br/>
					<br/>
					<?php
						$r = mysql_query(
							"
								SELECT
									count(*) as `count`,
									`status`
								FROM `mp_join_entry`
								WHERE `end_date` BETWEEN $statisticsStartTimeStamp AND $statisticsEndTimeStamp
									AND `status` IN ('5', '6', '7')
								GROUP BY `status`
							", $db
						);
						$statisticsResultRejected = 0;
						$statisticsResultOK = 0;
						$statisticsResultOnHold = 0;
						while($d=mysql_fetch_assoc($r)) {
							if ($d['status'] == '5') {
								// ������ � ��
								$statisticsResultOK = $d['count'];
							}
							if ($d['status'] == '6') {
								// ������ ���������
								$statisticsResultRejected = $d['count'];
							}
							if ($d['status'] == '7') {
								// ������
								$statisticsResultOnHold = $d['count'];
							}
						}
					?>
					����� ����������: <b><?php echo $statisticsResultRejected + $statisticsResultOK + $statisticsResultOnHold; ?></b>
					<br/>
					�� ��� ���������: <?php echo $statisticsResultRejected; ?>
					<br/>
					�� ��� ���������: <?php echo $statisticsResultOK; ?>
					<br/>
					�� ��� � �������: <?php echo $statisticsResultOnHold; ?>
				</td>
			</tr>
		</table>
		<?php
	}
}
// ------ ���������� :: end

// ------ �������� ����������� ���������� ��� ������������ :: begin
if($action=='main_user'){
	$SQL = 'SELECT * FROM `mp_join_entry` WHERE `nick`=\''.AuthUserName.'\' AND `status` NOT IN ("5", "6", "7") ORDER BY `start_date` DESC LIMIT 1;';
	$r = mysql_query($SQL);
	$ex = mysql_num_rows($r);
	$e = mysql_fetch_assoc($r);

	if($ex==1){
		if($e['status']=='1'){$status='� ������� �� ������������';}
		if($e['status']=='2'){$status='������� �� ������������';}
		if($e['status']=='3'){$status='��������� � <font color=black>'.prepareUserInfoForDrawing($e['revisor']).'</font> ��� ���������� �������������';}
		if($e['status']=='4'){$status='��������� ��������� ������� �� ������';}
	?>
		<table cellpadding=3 width=95% cellspacing=3>
			<th colspan=3 background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>������ �� ����������:</th>
			<tr>
				<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<b>������ ������:</b></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<font color="green"><b><?php echo $status; ?></b></font></td>
			</tr>
<?php
		if($e['revisor']!=''){
?>
			<tr>
				<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;������ �������������:</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php echo prepareUserInfoForDrawing($e['revisor']); ?></td>
			</tr>
			<?php
		}
		?>
			<tr>
				<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;�:</b></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;������:</b></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;�����:</b></td>
			</tr>
		<?php
		$SQL='SELECT * FROM `mp_join_answ` WHERE entry_id=\''.$e['id'].'\' order by answer_num asc';
		$r=mysql_query($SQL);
		while($d=mysql_fetch_assoc($r)) {

			?>
			<tr>
				<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;<?php echo $d['answer_num']; ?></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php echo $d['question']; ?></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php
				if($d['field_type']=='nick'){
					if($e["clan"]!=''){
						echo '<img src="http://www.timezero.ru/i/clans/'.$e['clan'].'.gif" style="vertical-align:text-bottom">';
					} else {
						echo "<img src=\"".$siteLocation."_modules/inviz/blank.gif\" width=\"28\" height=\"16\">";
					}
					echo AuthUserName;
					if($e["lvl"]!=0){ echo "[".$e["lvl"]."]"; }
					?><a target="_blank" href="http://www.timezero.ru/info.html?<?php echo AuthUserName; ?>">
					<img border="0" src="http://www.timezero.ru/i/i<?php echo $e["pro"]; if($e["man"]==0){echo "w";} ?>.gif" align="middle" style="vertical-align:text-bottom"></a>
					<?php
				} else {
					echo $d['answer_text'];
				}
				?></td>
			</tr>
			<?php
		}

		?></table>
		<?php
	}

	if($ex==0){
		?>
		<div align="right">
			<a onclick="load_page('main_user','');" href="javascript:{}">������� ����������</a>
			|
			<a onclick="load_page('add_entry','form');" href="javascript:{}">������ ������</a>
		</div>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td align="left">
					��������� ������!<br />
					����� �� ������ �������� ���������� � ������ ������ �� ���������� � ����
					<img src="<?php echo $siteLocation; ?>_imgs/clans/Military Police.gif" />
						<b>Military Police</b>.<br />
					<br />
				</td>
			</tr>
			<tr>
				<td align="left">
					<b>
						<font color="blue">���� ����������:</font>
					</b>
					<table>
						<tbody>
							<tr>
								<td class="quote-news">
									<b><font color="brown">�������� �������:</font></b> <b>�� 18 ���</b>
									<br><b><font color="brown">���������:</font></b> <b>�����, ����� ��������� "������"</b>
									<br>20 ������� ��� ����;
									<br>4 ����������� ������ Rangers ��� ����; 
									<br>PVP ������: �������-����� ��� ����; 
									<br>����������� �������� ����� � ������ �� ������.
									<br>
									<br><b><font color="brown">������� ������:</b></font>
									<br>1. ����������� ������������ ��������� �������������, ���������� ������� ������ <img src="<?php echo $siteLocation; ?>_imgs/clans/Military Police.gif" /><b>Military Police</b>.
									<br>2. ��������� ���� <img src="<?php echo $siteLocation; ?>_imgs/clans/Military Police.gif" /><b>Military Police</b> � ����.
									<br>3. ���������� �� ������� ��, � ����� ����� ������� � ������/������� � �������� ��������� ���� � ����/��������������.
									<br>4. ��������������������� � ����������.
									<br>5. ��������������������, ���������� ��������� �������, ������ �������� � �������. 
									<br>6. ����� � ���� �� ����� 30 ����� � ������.
									<br>7. ���������� ������� � ���������� "������".
									<br>8. ���������� ������������� � ���������.
								</td>
							</tr>
						</tbody>
					</table>
					<br />
					<b>�������, �� ������� ���������� ����� ���� �������� � ����������� ������ � �������
					<img src="<?php echo $siteLocation; ?>_imgs/clans/Military Police.gif" /><b>Military Police</b>:</b><br />
					�) ��������� ������� ��.<br />
					�) ��������� ��������� ����������, ��������, �� ��������������� ��������� ������ ������ � <img src="<?php echo $siteLocation; ?>_imgs/clans/Military Police.gif" /><b>Military Police</b>.<br />
					�) ��������� �������� ������������ ����������.<br />
					�) ���������������� �������������.<br />
					�) ����� ������ <img src="<?php echo $siteLocation; ?>_imgs/clans/Military Police.gif" /><b>Military Police</b> ����� ����� �������� ��������� � ������������ ������ ��� ���������� ������.<br />
					<br/>
					��������� ��� ������ �� ������� ����� �� �������, ���������������� �������� �� ����� �����.
					����� �� �������� � ����� ������. � ������ �������������� ������ � ���� ��������
					������ <img src="<?php echo $siteLocation; ?>_imgs/clans/Military Police.gif" /><b>Military Police</b>
					��� ���������� �������������. ���� ���� ����������� ����� ��������, � ���� �������� ��� �������������
					���������� ����� �� ���������� � ���� Military Police.
					<br />
					<br />
					<b><font color=red>����������:</font></b> ������������ �����, ������� ���������� ������������ ����� ����������� ����� ���������/��������
					� ���� ����� ���� � ��������������� ������ � ���������� ��� �� ������ ����������.<br />
					<br />
				</td>
			</tr>
			<tr>
				<td align="left">
					���� �� �������������� ����� �����������, ���������� ��� ���� ������� � ������ � �������� ������, �� �� ������ <a href="javascript:{}" onclick="load_page('add_entry','form');">������ ������ �� ����������</a><br />
					<br />
					<br />
					� ���������, <br />
					<b>����� ������ <img src="<?php echo $siteLocation; ?>_imgs/clans/Military Police.gif" />Military Police</b>
				</td>
			</tr>

		<?php
		$SQL="SELECT * FROM mp_join_entry where nick='".AuthUserName."' and (status='6' or status='5' or status='7') order by start_date desc limit 1;";
		$r=mysql_query($SQL);
		$ex_old= mysql_num_rows($r);
		$e=mysql_fetch_assoc($r);
		if($ex_old==1){
			if($e['status']==7){$st="�� ������� � <font color=green><b>������</b></font> ���������� �� ���������� � <b><img src='".$siteLocation."_imgs/clans/Military Police.gif' />Military Police</b>. �� �������� � ����.";}
			if($e['status']==6){$st="�� �������� <font color=red><b>�����</b></font><br>������� ������: <font color=red><b>".nl2br($e['status_comment'])."</b></font>";}
			if($e['status']==5){$st="��� <font color=green><b>�������</b></font> � ���� <img src='".$siteLocation."_imgs/clans/Military Police.gif' />Military Police</b>.";}
			?>
			<tr height="25"><td></td></tr>
			<tr>
				<td align="left" background='i/bgr-grid-sand.gif'>
				�� ��� �������� ������ �� ����������: <?php echo date("d.m.y H:i:s",$e['start_date']); ?><br />
				������ ���� �����������: <?php echo date("d.m.y H:i:s",$e['end_date']); ?><br />
				�� ������ <?php echo $st; ?>
				</td>
			</tr>
			<?php
		}
		?></table>
		<?php
	}
}
// ------ �������� ����������� ���������� ��� ������������ :: begin


// ---- ������ � ��������� (������ �����������������) :: begin
if(($action=="edit_questions") && $join_admin){
	if($target=="show"){
		?>
		<table width="85%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td align="left"><a href="javascript:{}" onclick="load_page('main_user','');">���������������� ��������</a></td>
				<td width="20"></td>
				<td align="left"><a href="javascript:{}" onclick="load_page('edit_questions','add');">�������� ������</a></td>
			</tr>
		</table>
		<table cellpadding=3 width=90% cellspacing=3>
			<th colspan=4  background='i/bgr-grid-sand.gif'>�������:</th>
        	<tr>
				<td width="30" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;�:</b></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;����� �������:</b></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;��� ����:</b></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
			</tr>
		<?php
		$SQL="SELECT * FROM mp_join_questions order by id asc";
		$r=mysql_query($SQL);
		while($d=mysql_fetch_assoc($r)) {
			if($d['field_type']=="one_string"){$type="������������";}
			if($d['field_type']=="multi_string"){$type="�������������";}
			if($d['field_type']=="callendar"){$type="���������";}
			if($d['field_type']=="nick"){$type="����������� ����";}
			if($d['field_type']=="drop_list"){$type="���������� ������";}
			if($d['field_type']==""){$type="";}
			?>
			<tr>
				<td width="30" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;<?php echo $d['id']; ?></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>&nbsp;&nbsp;<?php echo $d['txt']; ?></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="100" nowrap><center><?php echo $type; ?></center></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="60" nowrap>
					<center>
						<a href="javascript:{}" onclick="edit_question(<?php echo $d['id']; ?>);">
							<img src="<?php echo $siteLocation; ?>_modules/inviz/img/edit.gif" border="0" />
						</a>
						&nbsp;&nbsp;&nbsp;
						<a href="javascript:{}" onclick="if(confirm('������� ������?')){delete_question(<?php echo $d['id']; ?>);}">
							<img src="<?php echo $siteLocation; ?>_modules/inviz/img/del.gif" border="0" />
						</a>
					</center>
				</td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
	if($target=="add"){
		?>
		<table width="85%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td align="left"><a href="javascript:{}" onclick="load_page('main_user','');">���������������� ��������</a></td>
				<td width="20"></td>
				<td align="left"><a href="javascript:{}" onclick="load_page('edit_questions','show');">������ ��������</a></td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0" border="0">
			<tr valign="top">
				<td align="left"><b>����� �������:</b></td>
				<td width="10"></td>
				<td align="left"><input type="text" size="5" name="q_num" id="q_num" /></td>
			</tr>
			<tr height="7">
				<td colspan="3"></td>
			</tr>
			<tr valign="top">
				<td align="left"><b>����� �������:</b></td>
				<td width="10"></td>
				<td align="left"><textarea name="q_txt" id="q_txt" cols="60" rows="10"></textarea></td>
			</tr>
			<tr height="7">
				<td colspan="3"></td>
			</tr>
			<tr valign="top">
				<td align="left"><b>��� ����:</b></td>
				<td width="10"></td>
				<td align="left">
					<table cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','one_string');" name="ft" value="one_string" checked="checked" /> ������������</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','multi_string');" name="ft" value="multi_string" /> �������������</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','callendar');" name="ft" value="callendar" /> ���������</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','nick');" name="ft" value="nick" /> ����������� ����</td>
						</tr>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td>
							<input type="radio" onclick="set_radio('field_type','drop_list');" name="ft" value="drop_list" /> ���������� ������<br />
							<input type="text" name="field_options" id="field_options" size="60" /><br />
							*�������� ����������� ������ ������������ ����� �������. ��������: 1,2,3,4,5</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr height="7">
				<td colspan="3"></td>
			</tr>
			<tr valign="top">
				<td colspan="3"><input type="button" value="  ��������  " onclick="add_question(1);" id="add_q_btn"/></td>
			</tr>
		</table>
		<input type="hidden" name="field_type" id='field_type' value="one_string" />
		<?php
	}
	if($target=="check_q_ex"){
		$q_num=mysql_escape_string($q_num);
		$SQL="SELECT * FROM mp_join_questions where id='".$q_num."' limit 1";
		$r=mysql_query($SQL);
		echo mysql_num_rows($r);
	}
	if($target=="save_new"){
		$q_num=mysql_escape_string($q_num);
		$q_txt=mysql_escape_string($q_txt);
		$field_type=mysql_escape_string($field_type);
		$field_options=mysql_escape_string($field_options);
		mysql_query("INSERT INTO mp_join_questions (id,txt,field_type,field_options) values ('".$q_num."','".$q_txt."','".$field_type."','".$field_options."')");
	}
	if($target=="delete"){
		$q_id=mysql_escape_string($q_id);
		mysql_query("delete from mp_join_questions where id='".$q_id."';");
	}
	if($target=="edit"){
		$q_id=mysql_escape_string($q_id);
		$SQL="SELECT * FROM mp_join_questions where id='".$q_id."'";
		$r=mysql_query($SQL);
		$d=mysql_fetch_assoc($r);
		?>
		<table width="85%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td align="left"><a href="javascript:{}" onclick="load_page('main_user','');">���������������� ��������</a></td>
				<td width="20"></td>
				<td align="left"><a href="javascript:{}" onclick="load_page('edit_questions','show');">������ ��������</a></td>
			</tr>
		</table>

		<input type="hidden" name="target_id" id="target_id" value="<?php echo $q_id; ?>" />

		<table cellpadding="0" cellspacing="0" border="0">
			<tr valign="top">
				<td align="left"><b>����� �������:</b></td>
				<td width="10"></td>
				<td align="left"><input type="text" size="5" name="q_num" id="q_num" value="<?php echo $d['id']; ?>" /></td>
			</tr>
			<tr height="7">
				<td colspan="3"></td>
			</tr>
			<tr valign="top">
				<td align="left"><b>����� �������:</b></td>
				<td width="10"></td>
				<td align="left"><textarea name="q_txt" id="q_txt" cols="60" rows="10"><?php echo $d['txt']; ?></textarea></td>
			</tr>
			<tr height="7">
				<td colspan="3"></td>
			</tr>
			<tr valign="top">
				<td align="left"><b>��� ����:</b></td>
				<td width="10"></td>
				<td align="left">
					<table cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','one_string');" name="ft" value="one_string" <?php if(($d['field_type']=="one_string")||($d['field_type']=="")){ echo "checked=\"checked\""; } ?> /> ������������</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','multi_string');" name="ft" value="multi_string" <?php if($d['field_type']=="multi_string"){ echo "checked=\"checked\""; } ?> /> �������������</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','callendar');" name="ft" value="callendar" <?php if($d['field_type']=="callendar"){ echo "checked=\"checked\""; } ?> /> ���������</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','nick');" name="ft" value="nick" <?php if($d['field_type']=="nick"){ echo "checked=\"checked\""; } ?> /> ����������� ����</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td>
							<input type="radio" onclick="set_radio('field_type','drop_list');" name="ft" value="drop_list" <?php if($d['field_type']=="drop_list"){ echo "checked=\"checked\""; } ?> />  ���������� ������<br />
							<input type="text" name="field_options" id="field_options" size="60" value="<?php echo $d['field_options']; ?>" /><br />
							*�������� ����������� ������ ������������ ����� �������. ��������: 1,2,3,4,5</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr height="7">
				<td colspan="3"></td>
			</tr>
			<tr valign="top">
				<td colspan="3"><input type="button" value="  ���������  " onclick="add_question(0);" id="add_q_btn"/></td>
			</tr>
		</table>
		<input type="hidden" name="field_type" id="field_type" value="one_string" />
		<?php
	}
	if($target=="save_changes"){
		$q_num=mysql_escape_string($q_num);
		$q_txt=mysql_escape_string($q_txt);
		$field_type=mysql_escape_string($field_type);
		$field_options=mysql_escape_string($field_options);
		$target_id=mysql_escape_string($target_id);
		mysql_query("update mp_join_questions set id='".$q_num."', txt='".$q_txt."', field_type='".$field_type."', field_options='".$field_options."' where id='".$target_id."'");
	}
}
// ---- ������ � ��������� (������ �����������������) :: end

// ---- ������ ������ �� ���������� :: begin
if($action=="add_entry"){
	if(AuthStatus==1){
		if($target=="form"){
			$userinfo = locateUser(AuthUserName);
			if($userinfo["level"]>=13){
				?>
				<div align="right">
					<a onclick="load_page('main_user','');" href="javascript:{}">������� ����������</a>
					|
					<a onclick="load_page('add_entry','form');" href="javascript:{}">������ ������</a>
				</div>
				<table cellpadding=3 width=95% cellspacing=3>
					<th colspan=3  background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>������ �� ����������:</th>
					<tr>
						<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;�:</b></td>
						<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;������:</b></td>
						<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%" nowrap><b>&nbsp;&nbsp;�����:</b></td>
					</tr>
				<?php
				$SQL="SELECT * FROM mp_join_questions order by id";
				$r=mysql_query($SQL);
				while($d=mysql_fetch_assoc($r)) {
					?>
					<tr>
						<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;<?php echo $d['id']; ?></td>
						<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php echo $d['txt']; ?></td>
						<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%" nowrap>
						<?php


						if($d['field_type']=="nick"){
							echo prepareUserInfoForDrawing(AuthUserName, $userinfo);
							?>
							<input type="hidden" name="answer_<?php echo $d['id']; ?>" id="answer_<?php echo $d['id']; ?>" value="<?php echo AuthUserName; ?>" />
							<?php
						}
						if($d['field_type']=="one_string"){
							?>
							<input type="text" name="answer_<?php echo $d['id']; ?>" id="answer_<?php echo $d['id']; ?>" size="60" />
							<?php
						}
						if($d['field_type']=="multi_string"){
							?>
							<textarea name="answer_<?php echo $d['id']; ?>" id="answer_<?php echo $d['id']; ?>" cols="60" rows="7"></textarea>
							<?php
						}
						if($d['field_type']=="callendar"){
							?>
                            <input type="text" name="answer_<?php echo $d['id']; ?>" id="answer_<?php echo $d['id']; ?>" size="12" value="<?php echo date("d.m.Y",time()); ?>">
							<?php
						}
						if($d['field_type']=="drop_list"){
							$var=explode(",",$d['field_options']);
							?><select id="answer_<?php echo $d['id']; ?>" name="answer_<?php echo $d['id']; ?>">
							<?php
							$varsize = sizeof($var);
							for($i=0;$i<$varsize;$i++){
								?>
									<option value="<?php echo $var[$i]; ?>"><?php echo $var[$i]; ?></option>
								<?php
							}
							?></select>
							<?php
						}
						if($d['field_type']=="dep_list"){
							?><select id="answer_<?php echo $d['id']; ?>" name="answer_<?php echo $d['id']; ?>">
							<?php
							$SQL2='SELECT `name` FROM `mp_join_depts` ORDER BY id asc';
							$r2 = mysql_query($SQL2);
							while($d2=mysql_fetch_assoc($r2)) {
								?>
								<option value="<?php echo $d2['name']; ?>"><?php echo $d2['name']; ?></option>
								<?php
							}
							?></select>
							<?php
						}
						?>
						</td>
					</tr>
					<?php
				}
				?></table>
                <br />
                <input type="checkbox" id="minimum_agreement" onchange="minimum_agreement();" /> � ����������(�) � ������������ ������������ � ������(�) ��� ������� ��� ���.<br /><br />
				<input id="add_btn" type="button" disabled="disabled" onclick="add_entry();" value="              ������ ������              " /><br />
				<div id="add_load"></div>
				<?php
			} else {
				?><b>��������, �� ������ ����������� �� ���������� ������� 13 ������.</b><?php
			}
		}
		if($target=='add'){
			$answ=explode('@#$%^&*next@#$%^&*answer@#$%^&*',$answers);

			mysql_query(
				"
					INSERT INTO mp_join_entry (nick,start_date,status)
					values ('".AuthUserName."','".time()."','1')
				"
			);

			$SQL=
				"
					SELECT `id`
					FROM `mp_join_entry`
					where `nick`='".AuthUserName."' AND `status`='1'
				";
			$r=mysql_query($SQL);
			$d=mysql_fetch_assoc($r);
			$entry_id=$d['id'];
			$i=0;

			$SQL='SELECT * FROM `mp_join_questions` ORDER BY `id`';
			$r=mysql_query($SQL);
			while($d=mysql_fetch_assoc($r)) {
				$answ[$i]=mysql_escape_string($answ[$i]);
				$n=$i+1;
				mysql_query('INSERT INTO `mp_join_answ` (entry_id, answer_num, question, answer_text) VALUES ('.$entry_id.', \''.$n.'\', \''.$d['txt'].'\', \''.$answ[$i].'\')');
				$i++;
			}
		}
	} else {
		echo '<b><font color=red>��� ������ ������ ���������� �����������.</font></b>';
	}
}
// ---- ������ ������ �� ���������� :: end

// ---- ���������� ������ ����� :: begin
if(($action=='lists') && $join_access){
	$param=mysql_escape_string($param);
	if(!$page){$page=1;}

	$SQL='SELECT * FROM mp_join_entry';
	if($target=='free') {$SQL.=' WHERE `status`=1';}
	if($target=='my') {$SQL.=' WHERE `revisor`=\''.AuthUserName.'\' AND `status`<5';}
	if($target=='archive') {$SQL.=' WHERE `status` IN (5, 6)';}
	if($target=='other') {$SQL.=' WHERE `revisor`=\''.$param.'\' AND `status`<5';}
	if($target=='reserve') {$SQL.=' WHERE `status`=7';}
	if($target=='search'){$SQL.=' WHERE `nick` LIKE \'%'.$param.'%\'';}
	if($target=='inProgress'){$SQL.=" WHERE `status` NOT IN ('1', '5', '6', '7')";}

	if($target=='archive'){
		$SQL.=' ORDER BY `end_date` DESC';
	} else {
		$SQL.=' ORDER BY `start_date` ASC';
	}
	$r=mysql_query($SQL);
	$ex= mysql_num_rows($r);
	$from=$page*20-20;
	$pages=ceil($ex/20);
	$SQL.= ' LIMIT '.$from.',20';
	if($d['status']==1){$st='��������';}
	?>
	<table cellpadding=3 cellspacing=3>
		<tr><td colspan="<?php echo ($target=='free' ? '4' : '5');?>">
		<?php
		echo '��������: <b>';
		ShowJoinPages($page,$pages,4);
		echo "</b>";
		?>
		</td></tr>
		<?php if($target=='my' || $target=='other'){ ?><th colspan=6  background='i/bgr-grid-sand.gif'><? } else { ?><th colspan=5  background='i/bgr-grid-sand.gif'><? } ?>
		<?php if($target=='free') { ?>��������� ������:<?php } ?>
		<?php if($target=='archive') { ?>����� ������:<?php } ?>
		<?php if($target=='my') { ?>������ <?php echo AuthUserName; ?>:<?php } ?>
		<?php if($target=='other') { ?>������ <?php echo $param; ?>:<?php } ?>
		<?php if($target=='search') { ?>���������� ������:<?php } ?>
		<?php if($target=='reserve') { ?>������:<?php } ?>
		<?php if($target=='inProgress') { ?>� ���������:<?php } ?>
		</th>
		<tr>
			<td width="150" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;���:</b></td>
			<?php if(($target=="my")||($target=="other")){ ?><td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="20"></td><? } ?>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="150">
				<b>&nbsp;&nbsp;������:</b>
			</td>
			<?php
				if ($target != 'free') {
			?>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="150">
				<b>&nbsp;&nbsp;<?php if($target=="archive"){ ?>����������<?php } else { ?>�������������<?php } ?>:</b>
			</td>
			<?php
				}
			?>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="130" nowrap>
				<b>&nbsp;&nbsp;<?php if($target=="archive"){ ?>������������<?php } else { ?>������<?php } ?>:</b>
			</td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="150" nowrap><b>&nbsp;&nbsp;��������:</b></td>
		</tr>
	<?php
	$r=mysql_query($SQL);
	while($d=mysql_fetch_assoc($r)) {

		if($d['status']==1){$st='� �������';}
		if($d['status']==2){$st='���������������';}
		if($d['status']==3){$st='���������������';}
		if($d['status']==4){$st='���������������';}
		if($d['status']==5){$st='<font color=green><b>������(�)</b></font>';}
		if($d['status']==6){$st='<font color=red><b>��������</b><br>&nbsp;&nbsp;�������: '.$d['status_comment'].'</font>';}
		if($d['status']==7){$st='<font color=green><b>������</b></font>';}

		?>
		<tr>
			<td width="200" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>
			<?php echo prepareUserInfoForDrawing($d['nick']); ?>
			</td>
			<?php if(($target=="my")||($target=="other")){ ?>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="20">
				<?php if($d['status']==3){ ?><img border="0" src="http://www.tzpolice.ru/_modules/inviz/img/cell.gif" /><?php } ?>
				<?php if($d['status']==4){ ?><img border="0" src="http://www.tzpolice.ru/_modules/inviz/img/clock.gif" /><?php } ?>
				</td>
			<? } ?>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="100">
				<?php echo $st; ?>
			</td>
			<?php
				if ($target != 'free') {
			?>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap width="200">
				<?php echo prepareUserInfoForDrawing($d['revisor']); ?>
			</td>
			<?php
				}
			?>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="130" nowrap>
				<?php echo date("d.m.y H:i:s", ($target=="archive" ? $d['end_date'] : $d['start_date'])); ?>
			</td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="150" nowrap>
				&nbsp;&nbsp;<a href="javascript:{}" onclick="show_entry('show_entry','','<?php echo $d['id']; ?>','');">��������</a>
				<?php if(($d['status'] == 1 || $target=='inProgress') && $join_revisor){ ?>&nbsp;&nbsp;&nbsp;<a href="javascript:{}" onclick="show_entry('get_entry','','<?php echo $d['id']; ?>','');">�������</a><?php } ?>
			</td>
		</tr>
		<?php
	}

}
// ---- ���������� ������ ����� :: end

// ---- ����� ������ :: begin
if(($action=="show_entry") && $join_access){
	$param=mysql_escape_string($param);
	?>
	<table cellpadding=3 width=95% cellspacing=3>
		<th colspan=3  background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>������ �� ����������:</th>
	<?php
	$SQL="SELECT * FROM mp_join_entry where id='".$param."'";
	$r=mysql_query($SQL);
	$ex= mysql_num_rows($r);
	$e=mysql_fetch_assoc($r);


	if($e['status']=="1"){$status="� ������� �� ������������";}
	if($e['status']=="2"){$status="������� �� ������������";}
	if($e['status']=="3"){$status="��������� � <font color=black>".prepareUserInfoForDrawing($e['revisor'])."</font> ��� ���������� �������������";}
	if($e['status']=="4"){$status="��������� ��������� ������� �� ������";}
	if($e['status']=="5"){$status="������(�) � ���� �������";}
	if($e['status']=="6"){$status="<font color=red>�������� �� ����������</font>";}
	if($e['status']=="7"){$status="������� � ������";}
	if($e['status']>1){
		?>
		<tr>
			<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<b>������ ������:</b></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<font color="green"><b><?php echo $status; ?></b></font></td>
		</tr>
		<?php
	}
	if($e['revisor']!=''){
		?>
		<tr>
			<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;������ �������������:</td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php echo prepareUserInfoForDrawing($e['revisor']); ?></td>
		</tr>
		<?php
	}
	?>
		<tr>
			<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;�:</b></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;������:</b></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;�����:</b></td>
		</tr>
	<?php
	$SQL="
		SELECT a.answer_text, a.answer_num, a.question, q.field_type
		FROM mp_join_answ a
		LEFT JOIN mp_join_questions q ON a.answer_num=q.id
		where a.entry_id='".$e['id']."'
		order by answer_num asc
	";
	$r=mysql_query($SQL);
	while($d=mysql_fetch_assoc($r)) {

		?>
		<tr>
			<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;<?php echo $d['answer_num']; ?></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php echo $d['question']; ?></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php
			if($d['field_type']=='nick'){
				echo '<div id="user_nick_data">'.prepareUserInfoForDrawing($d['answer_text']).'</div>';
				?>
				<div id="user_nick_data_img"><a href="javascript:{}" onclick="reload_user_nick_data('<?php echo $param; ?>');" />[��������]</a></div>
				<?php
			} else {
				echo $d['answer_text'];
			}
			?></td>
		</tr>
		<?php
	}
	?>

	<?php
		// ---------------------- ������ ������ ������������ :: begin
		$r = mysql_query(
			"
				SELECT `comment_date`, `comment_text`, `commenter_nick`
				FROM `mp_join_entry_comments`
				WHERE `entry_id`='".$e['id']."'
				ORDER BY `comment_date` ASC
			", $db
		);
	?>
	<tr height="25"><td colspan="3"></td></tr>
	<tr>
		<td colspan="3">
		<b>�����������:</b><br />
			<table cellpadding=3 width=95% cellspacing=3>
				<tr>
					<th width="100" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>���� ����������</th>
					<th width="100" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>�������������</th>
					<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>�����������</th>
				</tr>
				<?php
					while($d=mysql_fetch_assoc($r)) {
				?>
					<tr>
						<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center'>
							<?php echo $d['comment_date']; ?>
						</td>
						<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center' nowrap>
							<b><?php echo prepareUserInfoForDrawing($d['commenter_nick']); ?></b>
						</td>
						<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>
							<?php echo $d['comment_text']; ?>
						</td>
					</tr>
				<?php
					}
				?>
			</table>
		</td>
	</tr>
	<?php
		// ---------------------- ������ ������ ������������ :: end
	?>

	<tr height="25"><td colspan="3"></td></tr>
	<tr><td colspan="3">
	<b>��������:</b><br />
	<?php
	if($e['status']==1){
		?><a href="javascript:{}" onclick="show_entry('get_entry','','<?php echo $e['id']; ?>','');">������� ������</a><br />
		<a href="javascript:{}" onclick="set_status('<?php echo $e['id']; ?>','7','0');">�������� � ������</a><br />
		<a href="javascript:{}" onclick="show_reject();">��������</a>
		<?php
	}
	if(($e['status']>1)&&($e['status']<5)) {
		if(($e['revisor']==AuthUserName)||($join_admin==1)){
		?>
		<a href="javascript:{}" onclick="set_status('<?php echo $e['id']; ?>','5','0');">������� � �������</a><br />
		<a href="javascript:{}" onclick="set_status('<?php echo $e['id']; ?>','7','0');">�������� � ������</a><br />
		<a href="javascript:{}" onclick="show_reject();">��������</a>
		<?php
		}
	}
	if($e['status']==7) {
		if(($e['revisor']==AuthUserName)||($join_admin==1)){
		?>
		<a href="javascript:{}" onclick="set_status('<?php echo $e['id']; ?>','5','0');">������� � �������</a><br />
		<a href="javascript:{}" onclick="show_reject();">��������</a>
		<?php
		}
	}
	?>
	<br />
	<div id="reject_form" style="display:none">
		<input type="text" size="100" name="comment_text" id="comment_text" value="�� �� �������������� ����� �����������" /><input type="button" value="  ��������  " name="reject_btn" onclick="set_status('<?php echo $e['id']; ?>','6','1');" />
	</div>
	<?php
	#if($e['status'] != 5 && $e['status'] != 6) {
	?>
		<a href="javascript:{}" onclick="show_comment();">��������������</a>
	<?php
	#}
	?>
	<br />
	<div id="comment_form" style="display:none">
		<textarea name="comment_text_area" id="comment_text_area" cols="50" rows="4"></textarea><input type="button" value="  �������� �����������  " name="comment_btn" onclick="add_comment('<?php echo $e['id']; ?>','1');" /></div>
	<br />
	<b>���������� ������:</b><br />
	<?php if(($e['revisor']==AuthUserName)||($join_admin==1)){
		if(($e['status']>1)&&($e['status']<5)) {
		?>
		<a href="javascript:{}" onclick="set_status('<?php echo $e['id']; ?>','3','0');">"��������� �� ���� ��� ���������� �������������"</a><br />
		<a href="javascript:{}" onclick="set_status('<?php echo $e['id']; ?>','4','0');">"��������� ��������� ������� �� ������"</a><br />
	<?php
		}
	} ?>
	</td></tr>
	<?php
	?></table>
	<?php

	$SQL="SELECT * FROM mp_join_entry where nick='".$e['nick']."' and id<>'".$e['id']."' order by start_date DESC";
	$r=mysql_query($SQL);
	$ex= mysql_num_rows($r);
	if($ex>0){
		?><table cellpadding=3 cellspacing=3>
		<th colspan=5  background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>����� ��� ������� ������:</th>
		<tr>
			<td width="200" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;���:</b></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="130"><b>&nbsp;&nbsp;������:</b></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="130" nowrap><b>&nbsp;&nbsp;�����������:</b></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="150" nowrap><b>&nbsp;&nbsp;���������:</b></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="70" nowrap><b>&nbsp;&nbsp;��������:</b></td>
		</tr>
		<?php
		while($d=mysql_fetch_assoc($r)) {
			if($d['status']==1){$st='��������';}
			if($d['status']==2){$st='���������������';}
			if($d['status']==3){$st='���������������';}
			if($d['status']==4){$st='���������������';}
			if($d['status']==5){$st='<font color=green><b>������(�)</b></font>';}
			if($d['status']==6){$st='<font color=red><b>��������</b><br>&nbsp;&nbsp;�������: '.$d['status_comment'].'</font>';}
			if($d['status']==7){$st='<font color=green><b>������</b></font>';}
			?>
			<tr>
				<td width="200" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;
				<?php
				echo prepareUserInfoForDrawing($d['nick']);
				?>
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="130">&nbsp;&nbsp;<?php echo date("d.m.y H:i:s",$d['start_date']); ?></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="130" nowrap>&nbsp;&nbsp;<?php echo date("d.m.y H:i:s",$d['end_date']); ?></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="150">&nbsp;&nbsp;<?php echo $st; ?></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="70" nowrap>&nbsp;&nbsp;<a href="javascript:{}" onclick="show_entry('show_entry','','<?php echo $d['id']; ?>','');">��������</a>
				<?php
				if($d['status']==1){
				?><br />&nbsp;&nbsp;<a href="javascript:{}" onclick="show_entry('get_entry','','<?php echo $e['id']; ?>','');">�������</a>
				<?php
				}
				?>
				</td>
			</tr>
			<?php
		}
	}
}
// ---- ����� ������ :: end

if($action=='get_entry' && $join_access){
	$param=mysql_escape_string($param);
	mysql_query('UPDATE `mp_join_entry` SET `status`=2, `revisor`="'.AuthUserName.'" WHERE `id`='.$param.';');
}

if($action=='set_status' && $join_access){
	$id=mysql_escape_string($id);
	$status=mysql_escape_string($status);
	$comment=mysql_escape_string($comment);
	mysql_query('UPDATE `mp_join_entry` SET `status`='.$status.', `status_comment`="'.$comment.'", `end_date`='.mktime().', `revisor`="'.AuthUserName.'" WHERE `id`='.$id.';');
}

if($action=='add_comment' && $join_access){
	$id=mysql_escape_string($id);
	$comment=mysql_escape_string($comment);
	$insertResult = mysql_query(
		"
			INSERT
				INTO `mp_join_entry_comments`(`entry_id`, `comment_text`, `commenter_nick`)
				VALUES(
					'".$id."',
					'".$comment."',
					'".AuthUserName."'
				)
		", $db
	);
}

if(($action=="reload_user_nick_data") && $join_access){
	$id=mysql_escape_string($id);
	$r=mysql_query("SELECT `nick` FROM mp_join_entry where `id`='".$id."' limit 1");
	$d=mysql_fetch_assoc($r);
	echo prepareUserInfoForDrawing($d['nick']);
}

// security preferences :: begin
if (($action=='edit_security') && $join_admin){
	if ($target == 'show') {
?>
		<table cellpadding=3 width=95% cellspacing=3>
			<tr>
				<th colspan=4 background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center'>��������� �������:</th>
			</tr>
			<tr>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width='200'>���</th>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width='100' align='center'>��������� ������</th>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width='100' align='center'>�������������</th>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>&nbsp;</th>
			</tr>
			<?php
			$r = mysql_query(
				'
					SELECT *
					FROM `mp_join_access`
				', $db
			);
			while($d=mysql_fetch_assoc($r)) {
			?>
			<tr>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>
					<?php echo prepareUserInfoForDrawing($d['nick']); ?>
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center'>
					<input type="checkbox" name="okAccess_<?php echo $d['id']; ?>" id="okAccess_<?php echo $d['id']; ?>" value="checkbox" <?php echo ($d['revisor'] ? 'checked' : ''); ?>>
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center'>
					<input type="checkbox" name="okAdmin_<?php echo $d['id']; ?>" id="okAdmin_<?php echo $d['id']; ?>" value="checkbox" <?php echo ($d['admin'] ? 'checked' : ''); ?>>
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>
					<input
						type="button"
						name="changeAccessButton"
						onclick="change_access('<?php echo $d['id'] ?>', document.getElementById('okAccess_<?php echo $d['id']; ?>').checked, document.getElementById('okAdmin_<?php echo $d['id']; ?>').checked);"
						value="�������� ������ ��� <?php echo $d['nick']; ?>">
					<input
						type="button"
						name="deleteAccessButton"
						onclick="if(confirm('������� <?php echo $d['nick']; ?> �� ������ (������ � ������� ������ � �������� ��� ������� ������������ ����� ��������)?')){delete_access('<?php echo $d['id'] ?>')};"
						value="������� <?php echo $d['nick']; ?> �� ������">
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' colspan='4' style='padding-top: 10px'>
					�������� ������ ��� ������ (���):
					<input
						type="text"
						name="accessNick"
						id="accessNick">
					<input
						type="button"
						name="addAccessButton"
						onclick="add_access(document.getElementById('accessNick').value);"
						value="�������� ������">
				</td>
			</tr>
			<tr>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' colspan='4' style='padding-top: 10px'>
					������� �� ����� �������:<br/>
					"��������� ������" - ���������� ������ ����� ����� �������� � �������� ����������<br/>
					"�������������" - ���������� ������ ����� ����� ������ ������, ������� ���������� �������� ������ ������������� ������� ������<br/><br/>
					(���� ����� ����� � ������ ������, �� �� ����� ������� ����������� ����, �� �� ������ ������ ��������������� ������ � ��������� �����������)
				</td>
			</tr>

<?php
	}

	if ($target == 'changeAccess') {
		mysql_query(
			'
				UPDATE `mp_join_access`
				SET
					`revisor` = "'.$okAccess.'",
					`admin` = "'.$okAdmin.'"
				WHERE `id` = "'.$target_id.'"
			', $db
		);
	}

	if ($target == 'deleteAccess') {
		mysql_query(
			'
				DELETE
				FROM `mp_join_access`
				WHERE `id` = "'.$target_id.'"
			', $db
		);
	}

	if ($target == 'addAccess') {
		?>
		<div align='center'>
		<?php
		$targetNick = mysql_escape_string(trim($targetNick));
		$r = mysql_query(
			"
				SELECT *
                FROM `locator`
                WHERE `login` = '".$targetNick."'
            ", $db
		);
		if (mysql_num_rows($r) != 1) {
		?>
			<font color='red'>�������� � ��������� ����� '<b><?php echo $targetNick; ?></b>' �� ������.</font>
		<?php
		} else {
			$r = mysql_query(
				"
					SELECT *
					FROM `mp_join_access`
					WHERE `nick` = '".$targetNick."'
				", $db
			);
			if (mysql_num_rows($r) != 0) {
			?>
				<font color='red'>�������� � ��������� ����� '<b><?php echo $targetNick; ?></b>' ��� ����� ������.</font>
			<?php
			} else {
				mysql_query(
					"
						INSERT
						INTO `mp_join_access`(`nick`, `revisor`, `admin`)
						VALUES (
							'$targetNick',
							'0',
							'0'
						)
					", $db
				);
				?>
					'<b><?php echo $targetNick; ?></b>' - ��������. ��������� ������, ���� ��� ����������.
				<?php
			}
		}
		?>
			<br /><br />
			<a href="javascript:{}" onclick="load_page('edit_security','show');">������� �� �������� ���������� ��������</a><br />
		</div>
		<?php
	}
}

if (($action=='show_actualy') && $join_admin){
	if ($target == 'show') {
?>
		<table cellpadding=3 width=95% cellspacing=3>
			<tr>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width='200'>���</th>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width='100' align='center'>���� ������</th>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width='100' align='center'>���� �����</th>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width='100' align='center'>���������</th>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>��������</th>
			</tr>
			<?php
			$r = mysql_query(
				'
					SELECT *
					FROM `mp_join_entry` WHERE `status`="5"
				', $db
			);
			while($d=mysql_fetch_assoc($r)) {
			?>
			<tr>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>
					<?php echo prepareUserInfoForDrawing($d['nick']); ?>
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center'>
					<?php echo date('d.m.Y H:i:s',$d['start_date']); ?>
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center'>
					<?php echo date('d.m.Y H:i:s',$d['end_date']); ?>
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center'>
					<?php echo prepareUserInfoForDrawing($d['revisor']); ?>
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>
				<a href="javascript:{}" onclick="show_entry('show_entry','','<?php echo $d['id']; ?>','');">��������</a>
				<a href="javascript:{}" onclick="actualy_del_comment('<?php echo $d['id']; ?>','<?php echo $d['nick']; ?>');">�������</a>
				</td>
			</tr>
			<?php
			}
			?>
			</table>
<?php
	}

	if ($target == 'dissmiss') {
		mysql_query(
			'
				UPDATE `mp_join_entry`
				SET
					`status` = "6",
					`revisor` = "'.AuthUserName.'",
					`status_comment` = "������ �� ����� �� �������: '.$reason.'"
				WHERE `id` = "'.$target_id.'"
			', $db
		);
	}

}
// security preferences :: end
?>
