<?php
$_RESULT = array("res" => "ok");
require_once "../../xhr_config.php";
require_once "../../xhr_php.php";
require_once "../../mysql.php";
require_once "../../functions.php";
require_once "../../auth.php";
$JsHttpRequest =& new Subsys_JsHttpRequest_Php("windows-1251");


$join_access=0;
$join_admin=0;

if (AuthUserGroup == '100'){$join_admin=1;}
if (abs(AccessLevel) & AccessJoinAdmin){$join_admin=1;}

if (AuthUserGroup == '100'){$join_access=1;}
if (abs(AccessLevel) & AccessJoinModer){$join_access=1;}

$menu = "rating_arh";
$path_to_php = "/_modules";
$DOCUMENT_ROOT = ereg_replace ("/$", "", $HTTP_SERVER_VARS['DOCUMENT_ROOT']);
include_once ($DOCUMENT_ROOT.$path_to_php."/".$menu."/tz_plugins.php");

function ShowJoinPages($CurPage,$TotalPages,$ShowMax) {
	global $action;
	global $target;
	global $param;
    $PrevList=floor(($CurPage-1)/$ShowMax)*$ShowMax;
    $NextList=$PrevList+$ShowMax+1;
        if($PrevList>=$ShowMax*2) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','1');\" title='� ����� ������'>�</a> ";
        if($PrevList>0) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','{$PrevList}');\" title='���������� $ShowMax �������'>�</a> ";
    for($i=$PrevList+1;$i<=$PrevList+$ShowMax;$i++) if($i<=$TotalPages) {
            if($i==$CurPage) echo "<u>{$i}</u> ";
        else echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','{$i}');\">$i</a> ";
    }
    if($NextList<=$TotalPages) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','{$NextList}');\" title='��������� $ShowMax �������'>�</a> ";
        if($CurPage<$TotalPages) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','{$TotalPages}');\" title='� ����� �����'>�</a>";
}

function VerifyStatus() {

	$SQL="SELECT status, revisor FROM police_join_entry where nick='".AuthUserName."' AND status IN ('1', '2', '3', '4', '7') ORDER BY start_date DESC LIMIT 1";
	$result=mysql_query($SQL);
	if ($row = mysql_fetch_array($result)) {
		if($row['status']=="1"){$stk="� ������� �� ������������";}
		if($row['status']=="2"){$stk="������� �� ������������";}
		if($row['status']=="3"){$stk="��������� � <img border=0 src='_imgs/clansld/police.gif'><font color=black>".$row['revisor']."</font> ��� ���������� �������������";}
		if($row['status']=="4"){$stk="��������� ��������� ������� �� ������";}
		if($row['status']=="7"){$stk="������� � <u>������</u>.";}
	} else {
		$stk = "no";
	}

	return $stk;
	
}



//foreach($_REQUEST as $k => $v) {
//	$temp = mb_convert_encoding($v,"cp1251","utf8");
//	$temp2 = mb_convert_encoding($temp,"utf8","cp1251");
//	if($temp2 == $v) {
//		$v = mb_convert_encoding($v,"cp1251","utf8");
//	}
//	$_REQUEST[$k] = addslashes(htmlspecialchars(trim($v)));
//}

extract($_REQUEST);

if (($join_access==1)&&($hide_menu!=1)){
	?>
	<br />
	<table width="100%" cellpadding="3" cellspacing="3" border="1">
		<tr>
			<td align="center"><a href="javascript:{}" onclick="show_entry('lists','free','','1');">��������� ������</a></td>
			<td align="center"><a href="javascript:{}" onclick="show_entry('lists','my','','1');">��� ������</a></td>
			<td align="center"><a href="javascript:{}" onclick="show_entry('lists','archive','','1');">�����</a></td>
			<?php if($join_admin==1){ ?>
			<td align="center" rowspan=2>
				<select onchange="show_entry('lists','other',this.value,'1');">
				<option value="false">�������� ������</option>
				<?php
				$SQL="SELECT revisor FROM police_join_entry group by revisor order by revisor asc";
				$r=mysql_query($SQL);
				while($d=mysql_fetch_assoc($r)) {
					?><option onclick="show_entry('lists','other','<?php echo $d['revisor']; ?>','1');" value="<?php echo $d['revisor']; ?>"><?php echo $d['revisor']; ?></option><?php
				}
				?>
				</select>
				<input type='button' value="�����" onclick="load_page('main_user','');">
			</td>
			<?php } ?>
		</tr>
		<tr>
			<td align="center"><a href="javascript:{}" onclick="show_entry('lists','reserve','','1');">������</a></td>
			<td align="center"><a href="javascript:{}" onclick="show_search();">�����</a></td>
			<td align="center">
				<?php if($join_admin==1){ ?>
				<a href="javascript:{}" onclick="load_page('edit_questions','show');">���������</a>
			    <?php } ?>
			�</td>
		</tr>

		<tr id="search_form" style="display:none;">
			<td align="center" colspan="2">����� �� ����: <input type="text" size="20" id="serach_nick" /><input type="button" value="  ������  " name="search_btn" id="search_btn" onclick="search_entry();" /></td>
			<td align="center" colspan="2">����� �� ���: <input type="text" size="50" id="serach_fio" /><input type="button" value="  ������  " name="search_btn" id="search_btn" onclick="search_fio();" /></td>
		</tr>
	</table>
	<br />
	<?php
}

if($action=="main_user"){

	$SQL="SELECT * FROM police_join_entry where nick='".AuthUserName."' and status<>'6' and status<>'5' order by start_date desc limit 1;";
	$r=mysql_query($SQL);
	$ex= mysql_num_rows($r);
	$e=mysql_fetch_assoc($r);

	if($ex==1){
		if($e['status']=="1"){$status="� ������� �� ������������";}
		if($e['status']=="2"){$status="������� �� ������������";}
		if($e['status']=="3"){$status="��������� � <img border=0 src='_imgs/clansld/police.gif'><font color=black>".$e['revisor']."</font> ��� ���������� �������������";}
		if($e['status']=="4"){$status="��������� ��������� ������� �� ������";}
		if($e['status']=="7"){$status="���� ������ ������ ��������� �������� � ������� � <u>������</u> ���������� � ���������� �������. �� �������� � ���� ��� �������������, ��� ������ ��������� ��������� ��������, ���������� ���.";}
		?>
		<table cellpadding=3 width=95% cellspacing=3>
			<th colspan=3  background='i/bgr-grid-sand.gif'>������ �� ����������:</th>
			<tr>
				<td width="20" background='i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
				<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<b>������ ������:</b></td>
				<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<font color="green"><b><?php echo $status; ?></b></font></td>
			</tr>
		<?php
		if($e['revisor']!=""){
			?>
			<tr>
				<td width="20" background='i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
				<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;������ �������������:</td>
				<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<img border=0 src='_imgs/clansld/police.gif'><?php echo $e['revisor']; ?></td>
			</tr>
			<?php
		}
		?>
			<tr>
				<td width="20" background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;�:</b></td>
				<td background='i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;������:</b></td>
				<td background='i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;�����:</b></td>
			</tr>
		<?php
		$SQL="SELECT * FROM police_join_answ where entry_id='".$e['id']."' order by answer_num asc";
		$r=mysql_query($SQL);
		while($d=mysql_fetch_assoc($r)) {

			?>
			<tr>
				<td width="20" background='i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;<?php echo $d['answer_num']; ?></td>
				<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php echo $d['question']; ?></td>
				<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php
				if($d['field_type']=="nick"){
					if($e["clan"]!=""){
						?><img src="http://www.timezero.ru/i/clans/<?php echo $e["clan"]; ?>.gif" style="vertical-align:text-bottom"><?php
					} else {
						echo "<img src=\"_modules/inviz/blank.gif\" width=\"28\" height=\"16\">";
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
<div align=left>

��������� ������!
<br>�� ���� �������� �� ������� ����� ���������� � <img src="_imgs/clans/Police Academy.gif" alt="Police Academy" border="0"><b>����������� ��������</b> � ������� ������ ������ �� ���������� � ���� <img src="_imgs/clans/police.gif" alt="police" border="0"><b>������� ��</b>.
<br>
<br>
<br><img src="_imgs/clans/Police Academy.gif" alt="Police Academy" border="0"><b>����������� ��������</b> �������� ������� �������������� <img src="_imgs/clans/police.gif" alt="police" border="0"><b>������� ��</b>, � ������� ������� ���������� �������� ����������, � ����� ��������������.
<br>
<br><b><font color="blue">���� ����������:</font></b>
<br>
<br>
<table><tbody><tr><td class="quote-news"><b><font color="brown">�������� �������:</font></b> <b>�� 21 ����</b>
<br>
<br><b><font color="brown">���. �������:</font></b> <b>13</b>
<br>
<br><b><font color="brown">���������:</font></b> <b>�����, ����� ��������� "������"</b>
<br>
<br><b><font color="brown">�������:</font> �� ����� ��������, �� ��� �������� ������ (� ��������� �������������) �������� ������� �� Freedom ��� �������� �������.</b> 
<br>
<br><b><font color="brown">���������:</font> �������������� ������������.</b> 
<br>
<br><b><font color="brown">������� ������:</font></b>
<br>1. ����������� ������������ ��������� ������, ���������� ������� ������ ������� ��.
<br>2. ��������� ���� ������� � ���������� ������� ������ ��.
<br>3. ���������������������, �������������, ����������.
<br>4. ��������������������, ���������� ��������� �������, ������ �������� � �������.
<br>5. ����� � ���� �� ����� 30 ����� � ������.
<br>6. ������������ ���� ��������� ��������� ���� ���������.
<br>7. ���������� ������� � ���������� "������", ����������� ���� ���������, � ����� ������� �� �������.
<br>8. ������ �������� ������ � ��������� ���������.
</td></tr></tbody></table>
<br>
<br><b><font color="blue">�������� ���� �������� ����������� ��������:</font></b>
<br>- �������������� ������ ����������� � �������� ������ �������;
<br>- ��������� �������� ��������� ���� � ������ ��;
<br>
<br><b><font color="blue">����� ��������:</font></b>
<br>�������� ����������� �������� ����� ����� ��:
<br>1. ����� � �������� ������ �������. ����� �������������� ������ �� ������ ��������. ����� � �������� �� ����������� ����������� ����� � �������� ������ �������.
<br>2. �������� �������� (� ������� �� ����������� �������) ����� ����� �� �������� ������� �������������� (��������, ������, �����������), � ��� �� ����� ����� �� ������������ ������������ (��������, ���������� ������������� ������ � �.�.) - <u>�� ������� ���������� � �������� ������ �������.</u>
<br>
<br><b><font color="blue">����������� ��������:</font></b>
<br>������� ����������� �������� ������:
<br>1. ������������ �� ����� ������������ � ����������� �������� ������������ � ��������� � �������������� �� ���������.
<br>2. ��������� ������� ������ ��. ��������� �� � ������� �� �� ����������� ������� �����������.
<br>3. �������������� ��������� ������� ������������ ����������. ������� ����� ���� ���������� ����� �� ���������� � ������������ � ���������� ������������.
<br>4. ����������� � ������ ������ ��������� ������� ��.
<br>
<br><b><font color="blue">������� ���������� �� ������� ��������:</font></b>
<br>1. ������� ������� � ����������� �������� ������ �� ��������� ������. ������ ���� ������������� �������������.
<br>2. �������� �������� ���������� �� ������ � �� �����. ������� ���� �������� ����� ������ ������.
<br>3. � ���������� �������� �������� ������� ����������, �������������� ���������� ������������� �������� ��������.
<br>4. �� ����� �������� �������� ����������� �� ������������ ��������� �� ���� ��, ������� � ���� � ������� �� ������!
<br>
<br><b><font color="blue">���������� �� ��������:</font></b>
<br>���������� �� ����� ����������� �������� �������� �������� � ��������� �������:
<br>1. ��������� ������� ��.
<br>2. ��������� ��������� ����������, ��������, �� ��������������� ��������� ������ ������ � ������� ��.
<br>3. ��������� �������� ������������ ����������.
<br>4. �� ������������ �������.
<br>
<br>����� ��������� ���������� �� �������� �� ������� �������� � ���������� ����������� ��������.
<br>
<br>���� �� �������������� ����� �����������, ���������� ��� ���� ������� � ������ � �������� ������, �� �� ������ <font color="brown"> <a href="javascript:{}" onclick="load_page('add_entry','form');">������ ������ �� ����������</a></font>
<br>
<br>
<br>� ���������,
<br><b>����� ������ <img src="_imgs/clans/police.gif" alt="police" border="0"> ������� ��</b>.
				</td>

		<?php
		$SQL="SELECT * FROM police_join_entry where nick='".AuthUserName."' and (status='6' or status='5' or status='7') order by start_date desc limit 1;";
		$r=mysql_query($SQL);
		$ex_old= mysql_num_rows($r);
		$e=mysql_fetch_assoc($r);
		if($ex_old==1){
			if($e['status']==7){$st="�� ������� � <font color=yellow><b>������</b></font> ���������� � ���������� �������. �� �������� � ����, ��� ������ ��������� ��������� ��������.";}
			if($e['status']==6){$st="�� �������� <font color=red><b>�����</b></font><br>������� ������: <font color=red><b>".nl2br($e['status_comment'])."</b></font>";}
			if($e['status']==5){$st="��� <font color=green><b>�������</b></font> � ���� �������.";}
			?>
			<br>
				�� ��� �������� ������ �� ����������: <?php echo date("d.m.y H:i:s",$e['start_date']); ?><br />
				������ ���� �����������: <?php echo date("d.m.y H:i:s",$e['end_date']); ?><br />
				�� ������ <?php echo $st; ?>
			<br>
			<?php
		}
		?></div>
		<?php
	}
}

if(($action=="edit_questions")&&($join_admin==1)){
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
				<td width="30" background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;�:</b></td>
				<td background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;����� �������:</b></td>
				<td background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;��� ����:</b></td>
				<td background='i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
			</tr>
		<?php
		$SQL="SELECT * FROM police_join_questions order by id asc";
		$r=mysql_query($SQL);
		while($d=mysql_fetch_assoc($r)) {
			if($d['field_type']=="one_string"){$type="������������";}
			if($d['field_type']=="multi_string"){$type="�������������";}
			if($d['field_type']=="callendar"){$type="���������";}
			if($d['field_type']=="nick"){$type="����������� ����";}
			if($d['field_type']=="drop_list"){$type="���������� ������";}
			if($d['field_type']=="dep_list"){$type="������ �������";}
			if($d['field_type']==""){$type="";}
			?>
			<tr>
				<td width="30" background='i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;<?php echo $d['id']; ?></td>
				<td background='i/bgr-grid-sand.gif'>&nbsp;&nbsp;<?php echo $d['txt']; ?></td>
				<td background='i/bgr-grid-sand.gif' width="100" nowrap><center><?php echo $type; ?></center></td>
				<td background='i/bgr-grid-sand.gif' width="60" nowrap><center><a href="javascript:{}" onclick="edit_question(<?php echo $d['id']; ?>);"><img src="_modules/inviz/img/edit.gif" border="0" /></a>&nbsp;&nbsp;&nbsp;<a href="javascript:{}" onclick="if(confirm('������� ������?')){delete_question(<?php echo $d['id']; ?>);}"><img src="_modules/inviz/img/del.gif" border="0" /></a></center></td>
			</tr>
			<?php
		}
		?>
		</table><br /><br />
		<table cellpadding=3 width=70% cellspacing=3>
			<th colspan=2  background='i/bgr-grid-sand.gif'>������:</th>
			<tr>
				<td background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;��������:</b></td>
				<td background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;</b></td>
			</tr>
			<?php
			$SQL="SELECT d.id,d.name,j.id as j_id FROM sd_depts d LEFT JOIN police_join_depts j ON d.id=j.id order by d.id asc";
			$r=mysql_query($SQL);
			while($d=mysql_fetch_assoc($r)) {
				?>
				<tr>
					<td background='i/bgr-grid-sand.gif' nowrap><?php if($d['j_id']>0){echo "<font color=green>";} else {echo "<font color=red>";} ?><b>&nbsp;&nbsp;<?php echo $d['name']; ?></b></font></td>
					<td width="100" background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;<a href="javascript:{}" onclick="togle_dept_status('<?php echo $d['id']; ?>','<?php if($d['j_id']>0){echo "0";} else {echo "1";}?>','<?php echo $d['name']; ?>')"><?php if($d['j_id']>0){echo "������";} else {echo "����������";}?></a></b></td>
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
				<td align="left"><a href="javascript:{}" onclick="load_page('edit_questions','show');">���������</a></td>
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
							<td><input type="radio" onclick="set_radio('field_type','one_string');" name="ft" id="ft" value="one_string" checked="checked" /> ������������</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','multi_string');" name="ft" id="ft" value="multi_string" /> �������������</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','callendar');" name="ft" id="ft" value="callendar" /> ���������</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','nick');" name="ft" id="ft" value="nick" /> ����������� ����</td>
						</tr>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','dep_list');" name="ft" id="ft" value="dep_list" /> ������ �������</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td>
							<input type="radio" onclick="set_radio('field_type','drop_list');" name="ft" id="ft" value="drop_list" /> ���������� ������<br />
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
		<input type="hidden" name="field_type" value="one_string" id="field_type" />
		<?php
	}
	if($target=="check_q_ex"){
		$q_num=mysql_escape_string($q_num);
		$SQL="SELECT * FROM police_join_questions where id='".$q_num."' limit 1";
		$r=mysql_query($SQL);
		echo mysql_num_rows($r);
	}
	if($target=="save_new"){
		$q_num=mysql_escape_string($q_num);
		$q_txt=mysql_escape_string($q_txt);
		$field_type=mysql_escape_string($field_type);
		$field_options=mysql_escape_string($field_options);
		mysql_query("INSERT INTO police_join_questions (id,txt,field_type,field_options) values ('".$q_num."','".$q_txt."','".$field_type."','".$field_options."')");
	}
	if($target=="delete"){
		$q_id=mysql_escape_string($q_id);
		mysql_query("delete from police_join_questions where id='".$q_id."';");
	}
	if($target=="edit"){
		$q_id=mysql_escape_string($q_id);
		$SQL="SELECT * FROM police_join_questions where id='".$q_id."'";
		$r=mysql_query($SQL);
		$d=mysql_fetch_assoc($r);
		?>
		<table width="85%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td align="left"><a href="javascript:{}" onclick="load_page('main_user','');">���������������� ��������</a></td>
				<td width="20"></td>
				<td align="left"><a href="javascript:{}" onclick="load_page('edit_questions','show');">���������</a></td>
			</tr>
		</table>

		<input type="hidden" id="target_id" name="target_id" value="<?php echo $q_id; ?>" />

		<table cellpadding="0" cellspacing="0" border="0">
			<tr valign="top">
				<td align="left"><b>����� �������:</b></td>
				<td width="10"></td>
				<td align="left"><input type="text" size="5" id="q_num" name="q_num" value="<?php echo $d['id']; ?>" /></td>
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
							<td><input type="radio" onclick="set_radio('field_type','one_string');" name="ft" id="ft" value="one_string" <?php if(($d['field_type']=="one_string")||($d['field_type']=="")){ echo "checked=\"checked\""; } ?> /> ������������</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','multi_string');" name="ft" id="ft" value="multi_string" <?php if($d['field_type']=="multi_string"){ echo "checked=\"checked\""; } ?> /> �������������</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','callendar');" name="ft" id="ft" value="callendar" <?php if($d['field_type']=="callendar"){ echo "checked=\"checked\""; } ?> /> ���������</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','nick');" name="ft" id="ft" value="nick" <?php if($d['field_type']=="nick"){ echo "checked=\"checked\""; } ?> /> ����������� ����</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','dep_list');" name="ft" id="ft" value="dep_list" <?php if($d['field_type']=="dep_list"){ echo "checked=\"checked\""; } ?> /> ������ �������</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td>
							<input type="radio" onclick="set_radio('field_type','drop_list');" name="ft" id="ft" value="drop_list" <?php if($d['field_type']=="drop_list"){ echo "checked=\"checked\""; } ?> />  ���������� ������<br />
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
		<input type="hidden" name="field_type" value="one_string" id="field_type" />
		<?php
	}
	if($target=="save_changes"){
		$q_num=mysql_escape_string($q_num);
		$q_txt=mysql_escape_string($q_txt);
		$field_type=mysql_escape_string($field_type);
		$field_options=mysql_escape_string($field_options);
		$target_id=mysql_escape_string($target_id);
		mysql_query("update police_join_questions set id='".$q_num."', txt='".$q_txt."', field_type='".$field_type."', field_options='".$field_options."' where id='".$target_id."'");
	}
	if($target=="depts"){
		$id=mysql_escape_string($id);
		$name=mysql_escape_string($name);
		if($make=="insert"){mysql_query("INSERT INTO police_join_depts (id,name) values ('".$id."','".$name."')");}
		if($make=="delete"){mysql_query("delete from police_join_depts where id='".$id."';");}
	}
}

if($action=="add_entry"){
	if(AuthStatus==1){
		if($target=="form"){

			$verify = VerifyStatus();
			if ($verify != "no") {
				echo "���������� ������ �� ��������� <b>".AuthUserName."</b> ��� ������� � ���� ������.<br>"; 
				echo "������ ����� ������: ".$verify;
			} else {
				$userinfo = GetUserInfo(AuthUserName, 0);
				if($userinfo["level"]>=13){
					?>
					<table cellpadding=3 width=95% cellspacing=3>
						<th colspan=3  background='i/bgr-grid-sand.gif'>������ �� ����������:</th>
						<tr>
							<td width="20" background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;�:</b></td>
							<td background='i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;������:</b></td>
							<td background='i/bgr-grid-sand.gif' width="49%" nowrap><b>&nbsp;&nbsp;�����:</b></td>
						</tr>
					<?php
					$SQL="SELECT * FROM police_join_questions order by id";
					$r=mysql_query($SQL);
					while($d=mysql_fetch_assoc($r)) {
						?>
						<tr>
							<td width="20" background='i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;<?php echo $d['id']; ?></td>
							<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php echo $d['txt']; ?></td>
							<td background='i/bgr-grid-sand.gif' width="49%" nowrap>
							<?php


							if($d['field_type']=="nick"){
								if($userinfo["clan"]!=""){
									?><img src="http://www.timezero.ru/i/clans/<?php echo $userinfo["clan"]; ?>.gif" style="vertical-align:text-bottom"><?php
								} else {
									echo "<img src=\"_modules/inviz/blank.gif\" width=\"28\" height=\"16\">";
								}
								echo AuthUserName;
								if($userinfo["level"]!=0){ echo "[".$userinfo["level"]."]"; }
								?><a target="_blank" href="http://www.timezero.ru/info.html?<?php echo AuthUserName; ?>">
								<img border="0" src="http://www.timezero.ru/i/i<?php echo $userinfo["pro"]; if($userinfo["man"]==0){echo "w";} ?>.gif" align="middle"	style="vertical-align:text-bottom"></a>
								<input type="hidden" id="answer_<?php echo $d['id']; ?>" name="answer_<?php echo $d['id']; ?>" value="<?php echo AuthUserName; ?>" />
								<?php
							}
							if($d['field_type']=="one_string"){
								?>
								<input type="text" name="answer_<?php echo $d['id']; ?>" id="answer_<?php echo $d['id']; ?>" size="60" />
								<?php
							}
							if($d['field_type']=="multi_string"){
								?>
								<textarea id="answer_<?php echo $d['id']; ?>" name="answer_<?php echo $d['id']; ?>" cols="60" rows="7"></textarea>
								<?php
							}
							if($d['field_type']=="callendar"){
								?>
								<!--<iframe width=174 height=189 name="gToday:normal:agenda.js:gfFlat_arrDate" id="gToday:normal:agenda.js:gfFlat_answer<?php echo $d['id']; ?>" src="_modules/inviz/callendar/iflateng.php?id=<?php echo $d['id']; ?>" scrolling="no" frameborder="0"></iframe><br />!-->
								<input type="text" id="answer_<?php echo $d['id']; ?>" name="answer_<?php echo $d['id']; ?>" size="12" value="<?php echo date("d.m.Y",time()); ?>">
								<?php
							}
							if($d['field_type']=="drop_list"){
								$var=explode(",",$d['field_options']);
								?><select id="answer_<?php echo $d['id']; ?>" name="answer_<?php echo $d['id']; ?>">
								<?php
								for($i=0;$i<sizeof($var);$i++){
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
								$SQL2="SELECT name FROM police_join_depts order by id asc";
								$r2=mysql_query($SQL2);
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
					?></table><br />
	                <input type="checkbox" id="minimum_agreement" onchange="minimum_agreement();" /> � ����������(�) � ������������ ������������ � ������(�) ��� ������� ��� ���.<br /><br />
					<input id="add_btn" type="button" disabled="disabled" onclick="add_entry();" value="              ������ ������              " /><br />
					<div id="add_load"></div>
					<?php
				} else {
					?><b>��������, �� ������ ����������� �� ���������� ������� 13 ������.</b><?php
				}
			}
		}
		if($target=="add"){

			$verify = VerifyStatus();
			if ($verify != "no") {

				echo "���������� ������ �� ��������� <b>".AuthUserName."</b> ��� ������� � ���� ������.<br>"; 
				echo "������ ����� ������: ".$verify;

			} else {
			
				$answ=explode("@#$%^&*next@#$%^&*answer@#$%^&*",$answers);

				$userinfo = GetUserInfo(AuthUserName, 0);
				mysql_query("INSERT INTO police_join_entry (nick,lvl,clan,pro,man,start_date,status) values ('".AuthUserName."','".$userinfo["level"]."','".$userinfo["clan"]."','".$userinfo["pro"]."','".$userinfo["man"]."','".time()."','1')");

				$SQL="SELECT id FROM police_join_entry where nick='".AuthUserName."' and status='1' and lvl='".$userinfo["level"]."' and pro='".$userinfo["pro"]."'";
				$r=mysql_query($SQL);
				$d=mysql_fetch_assoc($r);
				$entry_id=$d['id'];
				$i=0;

				$SQL="SELECT * FROM police_join_questions order by id";
				$r=mysql_query($SQL);
				while($d=mysql_fetch_assoc($r)) {
					$answ[$i]=mysql_escape_string($answ[$i]);
					$n=$i+1;
					mysql_query("INSERT INTO police_join_answ (entry_id,answer_num,question,answer_text) values	('".$entry_id."','".$n."','".$d['txt']."','".$answ[$i]."')");
					$i++;
				}
			}
		}
	} else {
		echo "<b><font color=red>��� ������ ������ ���������� <a href='http://www.tzpolice.ru/?act=register'>����������� �� �����</a>.</font></b>";
	}
}


if(($action=="lists")&&($join_access==1)){
	$param=mysql_escape_string($param);
	if(!$page){$page=1;}

	$SQL="SELECT * FROM police_join_entry";
	if($target=="free") {$SQL.=" where status='1'";}
	if($target=="my") {$SQL.=" where revisor='".AuthUserName."' and status<5";}
	if($target=="archive") {$SQL.=" where status='5' or status='6'";}
	if($target=="other") {$SQL.=" where revisor='".$param."' and status<5";}
	if($target=="search"){$SQL.=" where nick like '%".$param."%'";}
	
	if($target=='reserve') {$SQL.=' WHERE `status`=7';}
	if($target=="archive"){
		$SQL.=" order by end_date desc";
	} else {
		$SQL.=" order by start_date asc";
	}

	// ����� �� ��� - ��������� ������.
	if ($target=="search_fio") {
		$SQL = "SELECT A.* FROM police_join_entry as A LEFT JOIN police_join_answ as B ON A.id = B.entry_id WHERE B.answer_num = '2' AND B.answer_text like '%".$param."%' order by A.start_date asc";
	}

	//=====================================

	$r=mysql_query($SQL);
	$ex= mysql_num_rows($r);
	$from=$page*20-20;
	$pages=ceil($ex/20);
	$SQL.= " limit ".$from.",20";
	if($d['status']==1){$st="��������";}
	?>
	<table cellpadding=3 cellspacing=3>
		<tr><td colspan="4">
		<?php
		echo "��������: <b>";
		ShowJoinPages($page,$pages,4);
		echo "</b>";
		?>
		</td></tr>
		<th colspan=6  background='i/bgr-grid-sand.gif'>
		<?php if($target=="free") { ?>��������� ������:<?php } ?>
		<?php if($target=="archive") { ?>����� ������:<?php } ?>
		<?php if($target=="my") { ?>������ <?php echo AuthUserName; ?>:<?php } ?>
		<?php if($target=="other") { ?>������ <?php echo $param; ?>:<?php } ?>
		<?php if($target=="search") { ?>���������� ������:<?php echo $param; ?>:<?php } ?>
		<?php if($target=="reserve") { ?>������:<?php } ?>

		</th>
		<tr>
			<td width="200" background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;���:</b></td>
			<?php if(($target=="my")||($target=="other")){ ?><td background='i/bgr-grid-sand.gif' width="20"></td><? } ?>
			<td background='i/bgr-grid-sand.gif' width="100"><b>&nbsp;&nbsp;<?php if($target=="archive"){ ?>������:<?php } else { ?>���� ��������:<?php } ?></b></td>
			<td background='i/bgr-grid-sand.gif' width="60"><b>�������:</b></td>
			<td background='i/bgr-grid-sand.gif' width="130" nowrap><b>&nbsp;&nbsp;<?php if($target=="archive"){ ?>������������<?php } else { ?>������<?php } ?>:</b></td>
			<td background='i/bgr-grid-sand.gif' width="150" nowrap><b>&nbsp;&nbsp;��������:</b></td>
		</tr>
	<?php
	$r=mysql_query($SQL);
	while($d=mysql_fetch_assoc($r)) {
		$SQL2="SELECT * FROM police_join_answ where entry_id='".$d['id']."' and answer_num='3'";
		$r2=mysql_query($SQL2);
		$d2=mysql_fetch_assoc($r2);
			if($d['status']==2){$st="���������������";}
			if($d['status']==3){$st="���������������";}
			if($d['status']==4){$st="���������������";}
			if($d['status']==5){$st="<font color=green><b>������(�)</b></font>";}
			if($d['status']==6){$st="<font color=red><b>��������</b><br>&nbsp;&nbsp;�������: ".$d['status_comment']."</font>";}

		$d2['answer_text']=str_replace("/",".",$d2['answer_text']);
		$d2['answer_text']=str_replace(",",".",$d2['answer_text']);
		$d2['answer_text']=str_replace(" ",".",$d2['answer_text']);
		$d2['answer_text']=str_replace("-",".",$d2['answer_text']);
		list($c1,$c2,$c3)= split ('[.]', $d2['answer_text']);
		$age=date("Y",time())-$c3;
		if(mktime(0, 0, 0, $c2, $c1, 1970)>mktime(0, 0, 0, date("m",time()), date("d",time()), 1970)) {$age--;}

		?>
		<tr>
			<td width="200" background='i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;
			<?php
			if($d["clan"]!=""){
				?><img src="http://www.timezero.ru/i/clans/<?php echo $d["clan"]; ?>.gif" style="vertical-align:text-bottom"><?php
			} else {
				echo "<img src=\"_modules/inviz/blank.gif\" width=\"28\" height=\"16\">";
			}
			echo $d['nick'];
			if($d["lvl"]!=0){ echo "[".$d["lvl"]."]"; }
			?><a target="_blank" href="http://www.timezero.ru/info.html?<?php echo $d['nick']; ?>">
			<img border="0" src="http://www.timezero.ru/i/i<?php echo $d["pro"]; if($d["man"]==0){echo "w";} ?>.gif" align="middle" style="vertical-align:text-bottom"></a>
			</td>
			<?php if(($target=="my")||($target=="other")){ ?><td background='i/bgr-grid-sand.gif' width="20">
			<?php if($d['status']==3){ ?><img border="0" src="_modules/inviz/img/cell.gif" /><?php } ?>
			<?php if($d['status']==4){ ?><img border="0" src="_modules/inviz/img/clock.gif" /><?php } ?>
			</td><? } ?>
			<td background='i/bgr-grid-sand.gif' width="100">&nbsp;&nbsp;<?php if($target!="archive"){ echo $d2['answer_text']; } else { echo $st; } ?></td>
			<td background='i/bgr-grid-sand.gif' width="60" nowrap>&nbsp;&nbsp;<?php echo $age; ?></td>
			<td background='i/bgr-grid-sand.gif' width="130" nowrap>&nbsp;&nbsp;<?php echo date("d.m.y H:i:s",$d['start_date']); ?></td>
			<td background='i/bgr-grid-sand.gif' width="150" nowrap>&nbsp;&nbsp;<a href="javascript:{}" onclick="show_entry('show_entry','','<?php echo $d['id']; ?>','');">��������</a>
			<?php if($d['status']==1){ ?>&nbsp;&nbsp;&nbsp;<a href="javascript:{}" onclick="show_entry('get_entry','','<?php echo $d['id']; ?>','');">�������</a><?php } ?></td>
		</tr>
		<?php
	}

}

if(($action=="show_entry")&&($join_access==1)){
	$param=mysql_escape_string($param);
	?>
	<table cellpadding=3 width=95% cellspacing=3>
		<th colspan=3  background='i/bgr-grid-sand.gif'>������ �� ����������:</th>
	<?php
	$SQL="SELECT * FROM police_join_entry where id='".$param."'";
	$r=mysql_query($SQL);
	$ex= mysql_num_rows($r);
	$e=mysql_fetch_assoc($r);


	if($e['status']=="1"){$status="� ������� �� ������������";}
	if($e['status']=="2"){$status="������� �� ������������";}
	if($e['status']=="3"){$status="��������� � <img border=0 src='_imgs/clansld/police.gif'><font color=black>".$e['revisor']."</font> ��� ���������� �������������";}
	if($e['status']=="4"){$status="��������� ��������� ������� �� ������";}
	if($e['status']=="5"){$status="������(�) � ���� �������";}
	if($e['status']=="6"){$status="<font color=red>�������� �� ����������</font>";}
	if($e['status']=="7"){$status="������� � <u>������</u>.";}
	if($e['status']>1){
		?>
		<tr>
			<td width="20" background='i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
			<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<b>������ ������:</b></td>
			<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<font color="green"><b><?php echo $status; ?></b></font></td>
		</tr>
		<?php
	}
	if($e['status']==6){
		?>
		<tr>
			<td width="20" background='i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
			<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<b>�������:</b></td>
			<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<font color="green"><b><font color=red><?php echo $e['status_comment']; ?></font></b></font></td>
		</tr>
		<?php
	}
	if($e['revisor']!=""){
		?>
		<tr>
			<td width="20" background='i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
			<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;������ �������������:</td>
			<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<img border=0 src='_imgs/clansld/police.gif'><?php echo $e['revisor']; ?></td>
		</tr>
		<?php
	}
	?>
		<tr>
			<td width="20" background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;�:</b></td>
			<td background='i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;������:</b></td>
			<td background='i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;�����:</b></td>
		</tr>
	<?php
	$SQL="SELECT a.answer_text, a.answer_num, a.question, q.field_type FROM police_join_answ a LEFT JOIN police_join_questions q ON a.answer_num=q.id where a.entry_id='".$e['id']."' order by answer_num asc";
	$r=mysql_query($SQL);
	while($d=mysql_fetch_assoc($r)) {

		?>
		<tr>
			<td width="20" background='i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;<?php echo $d['answer_num']; ?></td>
			<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php echo $d['question']; ?></td>
			<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php
			if($d['field_type']=="nick"){
				if($e["clan"]!=""){
					?><div id="user_nick_data"><img src="http://www.timezero.ru/i/clans/<?php echo $e["clan"]; ?>.gif" style="vertical-align:text-bottom"><?php
				} else {
					echo "<div id=\"user_nick_data\"><img src=\"_modules/inviz/blank.gif\" width=\"28\" height=\"16\">";
				}
				echo $d['answer_text'];
				if($e["lvl"]!=0){ echo "[".$e["lvl"]."]"; }
				?><a target="_blank" href="http://www.timezero.ru/info.html?<?php echo $d['answer_text']; ?>">
				<img border="0" src="http://www.timezero.ru/i/i<?php echo $e["pro"]; if($e["man"]==0){echo "w";} ?>.gif" align="middle" style="vertical-align:text-bottom"></a></div> <div id="user_nick_data_img"><a href="javascript:{}" onclick="reload_user_nick_data('<?php echo $param; ?>');" />[��������]</a></div>
				<?php
			} else {
				echo $d['answer_text'];
			}
			?></td>
		</tr>
		<?php
	}
	?>
	<tr height="25"><td colspan="3"></td></tr>
	<tr><td colspan="3">
	<b>��������:</b><br />
	<?php
	if($e['status']==1){
		?><a href="javascript:{}" onclick="show_entry('get_entry','','<?php echo $e['id']; ?>','');">������� ������</a><br />
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
//		if(($e['revisor']==AuthUserName)||($join_admin==1)){
		?>
		<a href="javascript:{}" onclick="take_archive('<?php echo $e['id']; ?>');">������� ������</a><br />
<!--		<a href="javascript:{}" onclick="set_status('<?php echo $e['id']; ?>','5','0');">������� � �������</a><br />-->
		<a href="javascript:{}" onclick="show_reject();">��������</a>
		<?php
//		}
	}
	?><br />
	<div id="reject_form" style="display:none"><input type="text" size="20" name="comment_text" id="comment_text" value="�� �� �������������� ����� �����������" /><input type="button" value="  ��������  " name="reject_btn" id="reject_btn" onclick="set_status('<?php echo $e['id']; ?>','6','1');" /></div><br />
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

	$SQL="SELECT * FROM police_join_entry where nick='".$e['nick']."' and id<>'".$e['id']."' order by start_date DESC";
	$r=mysql_query($SQL);
	$ex= mysql_num_rows($r);
	if($ex>0){
		?><table cellpadding=3 cellspacing=3>
		<th colspan=5  background='i/bgr-grid-sand.gif'>����� ��� ������� ������:</th>
		<tr>
			<td width="200" background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;���:</b></td>
			<td background='i/bgr-grid-sand.gif' width="130"><b>&nbsp;&nbsp;������:</b></td>
			<td background='i/bgr-grid-sand.gif' width="130" nowrap><b>&nbsp;&nbsp;�����������:</b></td>
			<td background='i/bgr-grid-sand.gif' width="150" nowrap><b>&nbsp;&nbsp;���������:</b></td>
			<td background='i/bgr-grid-sand.gif' width="70" nowrap><b>&nbsp;&nbsp;��������:</b></td>
		</tr>
		<?php
		while($d=mysql_fetch_assoc($r)) {
			if($d['status']==1){$st="��������";}
			if($d['status']==2){$st="���������������";}
			if($d['status']==3){$st="���������������";}
			if($d['status']==4){$st="���������������";}
			if($d['status']==5){$st="<font color=green><b>������(�)</b></font>";}
			if($d['status']==6){$st="<font color=red><b>��������</b><br>&nbsp;&nbsp;�������: ".$d['status_comment']."</font>";}
			if($d['status']==7){$st="<font color=yellow><b>������</b><br>&nbsp;&nbsp;����������: ".$d['status_comment']."</font>";}
			?>
			<tr>
				<td width="200" background='i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;
				<?php
				if($d["clan"]!=""){
					?><img src="http://www.timezero.ru/i/clans/<?php echo $d["clan"]; ?>.gif" style="vertical-align:text-bottom"><?php
				} else {
					echo "<img src=\"_modules/inviz/blank.gif\" width=\"28\" height=\"16\">";
				}
				echo $d['nick'];
				if($d["lvl"]!=0){ echo "[".$d["lvl"]."]"; }
				?><a target="_blank" href="http://www.timezero.ru/info.html?<?php echo $d['nick']; ?>">
				<img border="0" src="http://www.timezero.ru/i/i<?php echo $d["pro"]; if($d["man"]==0){echo "w";} ?>.gif" align="middle" style="vertical-align:text-bottom"></a>
				</td>
				<td background='i/bgr-grid-sand.gif' width="130">&nbsp;&nbsp;<?php echo date("d.m.y H:i:s",$d['start_date']); ?></td>
				<td background='i/bgr-grid-sand.gif' width="130" nowrap>&nbsp;&nbsp;<?php echo date("d.m.y H:i:s",$d['end_date']); ?></td>
				<td background='i/bgr-grid-sand.gif' width="150">&nbsp;&nbsp;<?php echo $st; ?></td>
				<td background='i/bgr-grid-sand.gif' width="70" nowrap>&nbsp;&nbsp;<a href="javascript:{}" onclick="show_entry('show_entry','','<?php echo $d['id']; ?>','');">��������</a>
				<?php
				if($d['status']==1){
				?><br />&nbsp;&nbsp;<a href="javascript:{}" onclick="show_entry('get_entry','','<?php echo $d['id']; ?>','');">�������</a>
				<?php
				}
				?>
				</td>
			</tr>
			<?php
		}
	}



}
if(($action=="get_entry")&&($join_access==1)){
	$param=mysql_escape_string($param);
	mysql_query("UPDATE police_join_entry set status='2', revisor='".AuthUserName."' where id='".$param."';");
}
if(($action=="take_arch")&&($join_access==1)){
	$param=mysql_escape_string($param);
	mysql_query("UPDATE police_join_entry set status='2', revisor='".AuthUserName."' where id='".$param."';") or die (mysql_error());
}

if(($action=="set_status")&&($join_access==1)){
	$id=mysql_escape_string($id);
	$status=mysql_escape_string($status);
	$comment=mysql_escape_string($comment);
	mysql_query("UPDATE police_join_entry set status='".$status."', status_comment='".$comment."', end_date='".time()."', revisor='".AuthUserName."' where id='".$id."';");
}

if(($action=="reload_user_nick_data")&&($join_access==1)){
	$id=mysql_escape_string($id);
	$r=mysql_query("SELECT `nick` FROM police_join_entry where `id`='".$id."' limit 1");
	$d=mysql_fetch_assoc($r);
	$userinfo = GetUserInfo($d['nick'], 0);
	if($userinfo["level"]>=1){
		mysql_query("UPDATE police_join_entry set lvl='".$userinfo["level"]."', clan='".$userinfo["clan"]."', pro='".$userinfo["pro"]."', man='".$userinfo["man"]."' where id='".$id."';");
		if($userinfo["clan"]!=""){
			?><img src="http://www.timezero.ru/i/clans/<?php echo $userinfo["clan"]; ?>.gif" style="vertical-align:text-bottom"><?php
		} else {
			echo "<img src=\"_modules/inviz/blank.gif\" width=\"28\" height=\"16\">";
		}
		echo $d['nick'];
		if($userinfo["level"]!=0){ echo "[".$userinfo["level"]."]"; }
		?><a target="_blank" href="http://www.timezero.ru/info.html?<?php echo $d['nick']; ?>">
		<img border="0" src="http://www.timezero.ru/i/i<?php echo $userinfo["pro"]; if($userinfo["man"]==0){echo "w";} ?>.gif" align="middle" style="vertical-align:text-bottom"></a>
		<?php
	} else {
		echo 0;
	}
}

?>