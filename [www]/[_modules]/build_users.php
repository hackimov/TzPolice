<h1>����������������� ���. ��������</h1>

<?php
$bgstr[0]="background='i/bgr-grid-sand.gif'";
$bgstr[1]="background='i/bgr-grid-sand1.gif'";

if(AuthStatus==1 && AuthUserName!="" && AuthUserGroup=='100') {

if(@$_REQUEST['newID']) {
	$SQL="SELECT id FROM site_users WHERE user_name='".htmlspecialchars($_POST['user_name'])."'";
	$r=mysql_query($SQL);
	if ($d=mysql_fetch_row($r)) {
		$SQL="INSERT INTO build_users VALUES('',
		'".$d[0]."',
		'".($_POST['factory']?1:0)."',
		'".($_POST['laboratory']?1:0)."',
		'".($_POST['warehouse']?1:0)."')";
		mysql_query($SQL);
		echo "<h6>�������� ����� ������������</h6>";
	} else echo "<h6>������������ �� ������</h6>";
}
if(@$_REQUEST['editID']) {
	$SQL="UPDATE build_users SET ".
	"factory='".($_POST['factory']?1:0)."', ".
	"laboratory='".($_POST['laboratory']?1:0)."', ".
	"warehouse='".($_POST['warehouse']?1:0)."' ".
	"WHERE user_id='".$_POST['editID']."'";
	mysql_query($SQL);
	echo "<h6>����� ������������ ��������</h6>";
}
if(@$_REQUEST['factoryID']) {
	$SQL="UPDATE buildings SET order_time='".$_POST['order_time']."' WHERE id='".$_POST['factoryID']."'";
	mysql_query($SQL);
	echo "<h6>����� ����� �������� ����������</h6>";
}
if(@$_REQUEST['classID']) {
	$SQL="UPDATE items_class SET cmax='".$_POST['cmax']."', ctime='".$_POST['ctime']."' WHERE id='".$_POST['classID']."'";
	mysql_query($SQL);
	echo "<h6>��������� ������ ��������</h6>";
}
if(@$_REQUEST['delID']) {
	$SQL="DELETE FROM build_users WHERE user_id='".$_REQUEST['delID']."'";
	mysql_query($SQL);
	echo "<h6>������������ ������.</h6>";
}
?>
<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" border="0">
<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>����� �������������:</strong> </p></td></tr>
<tr><td align="center">

<table>

<tr bgcolor=#F4ECD4>
<td width=200><b>������������</b></td>
<td width=80 align=center><b>������</b></td>
<td width=80 align=center><b>�����������</b></td>
<td width=80 align=center><b>������</b></td>
<td width=80 align=center><b>��������</b></td>
</tr>

<?
$SQL = "SELECT b.*, u.id AS UID, u.user_name AS UName, u.clan AS UClan FROM build_users b LEFT JOIN site_users u on u.id=b.user_id";
$r = mysql_query($SQL);
$np=0;

while($d=mysql_fetch_array($r)) {
if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}
?>
<tr><form name="user_<?=$d['user_id']?>" action="?act=build_users" method="POST">
<input name="editID" type="hidden" value="<?=$d['user_id']?>">
<td <?=$bg?>><?=GetClan($d['UClan']).GetUser($d['UID'],$d['UName'],AuthUserGroup)?>
<?echo " (<a href='#; return false;' onclick=\"if(confirm('������� ������������?')) top.location='?act=build_users&delID={$d[user_id]}';\">X</a>)"?>
</td>
<td <?=$bg?> align=center><input type="checkbox" name="factory"<?=($d['factory']?' checked':'')?>></td>
<td <?=$bg?> align=center><input type="checkbox" name="laboratory"<?=($d['laboratory']?' checked':'')?>></td>
<td <?=$bg?> align=center><input type="checkbox" name="warehouse"<?=($d['warehouse']?' checked':'')?>></td>
<td  <?=$bg?> align=center><input type=button value="<->" onclick="if(confirm('��������?')) this.form.submit();"></td>
</form></tr>

<?}?>
<tr><form name="user_new" action="?act=build_users" method="POST">
<input name="newID" type="hidden" value="new">
<td <?=$bg?>>&nbsp;<input name="user_name" type="text" value="" size=40></td>
<td <?=$bg?> align=center><input type="checkbox" name="factory"></td>
<td <?=$bg?> align=center><input type="checkbox" name="laboratory"></td>
<td <?=$bg?> align=center><input type="checkbox" name="warehouse"></td>
<td  <?=$bg?> align=center><input type=button value="<->" onclick="if(confirm('��������?')) this.form.submit();"></td>
</form></tr>

</table>

</td></tr>

<?
if ($_REQUEST['shop']==2) {
	mysql_query("UPDATE const SET value='1' WHERE script='shop2'");
} else if ($_REQUEST['shop']==1) {
	mysql_query("UPDATE const SET value='0' WHERE script='shop2'");
}
$SQL = "SELECT * FROM const WHERE script='shop2'";
$r = mysql_query($SQL);
$d = mysql_fetch_array($r);
?>
<tr><td><br></td></tr>
<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>�������� ��������� ��������-�������� ���������: [ <?=($d['value']==1?'<a href="?act=build_users&shop=1">���������</a>':'<a href="?act=build_users&shop=2">��������</a>')?> ]</strong> </p></td></tr>

<tr><td><br></td></tr>
<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>��������� - ����� ����� ��������:</strong> </p></td></tr>
<tr><td>

<table>

<tr bgcolor=#F4ECD4>
<td width=200><b>�����</b></td>
<td width=80 align=center><b>����� (���)</b></td>
<td width=80 align=center><b>��������</b></td>
</tr>

<?
$SQL = "SELECT * FROM buildings WHERE type=1";
$r = mysql_query($SQL);
$np=0;

while($d=mysql_fetch_array($r)) {
if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}
?>
<tr><form name="f_<?=$d['id']?>" action="?act=build_users" method="POST">
<input name="factoryID" type="hidden" value="<?=$d['id']?>">
<td <?=$bg?>><b><?=$d['name']?></b></td>
<td <?=$bg?> align=center><input type="text" name="order_time" size=5 value="<?=$d['order_time']?>"></td>
<td  <?=$bg?> align=center><input type=button value="<->" onclick="if(confirm('��������?')) this.form.submit();"></td>
</form></tr>

<?}?>
</table>

</td></tr>

<tr><td><br></td></tr>
<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>��������� - ������ �������:</strong> </p></td></tr>
<tr><td>

<table>

<tr bgcolor=#F4ECD4>
<td width=200><b>������</b></td>
<td width=80 align=center><b>��������</b></td>
<td width=80 align=center><b>����� (���)</b></td>
<td width=80 align=center><b>��������</b></td>
</tr>

<?
$SQL = "SELECT * FROM items_class";
$r = mysql_query($SQL);
$np=0;

while($d=mysql_fetch_array($r)) {
if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}
?>
<tr><form name="c_<?=$d['id']?>" action="?act=build_users" method="POST">
<input name="classID" type="hidden" value="<?=$d['id']?>">
<td <?=$bg?>><b><?=$d['class']?></b></td>
<td <?=$bg?> align=center><input type="text" name="cmax" size=5 value="<?=$d['cmax']?>"></td>
<td <?=$bg?> align=center><input type="text" name="ctime" size=5 value="<?=$d['ctime']?>"></td>
<td  <?=$bg?> align=center><input type=button value="<->" onclick="if(confirm('��������?')) this.form.submit();"></td>
</form></tr>

<?}?>
</table>

</td></tr>
</table>

<?
} else echo $mess['AccessDenied'];
?>