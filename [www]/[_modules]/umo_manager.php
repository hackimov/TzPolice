<h1>������������, ������ � ��������� ���</h1>


<?
if(AuthStatus==1 && (AuthUserGroup==100)) {
if (isset($_REQUEST['del'])) {
    $result = mysql_query("DELETE FROM `buildings` WHERE `id`='".$_REQUEST['del']."' LIMIT 1;");
    if ($result) { echo "<font color='green'>�������</font><br><br>"; }
    else { echo "<font color='red'><b>������ �� ����� ��������</b></font>"; }
   }

if (isset($_REQUEST['add'])) {
	$query = "INSERT INTO `buildings` (`id`, `type`, `name`, `full_name`) VALUES ('', '".$_REQUEST['type']."', '".$_REQUEST['sname']."', '".$_REQUEST['fname']."')";
    $result = mysql_query($query);
    if ($result) { echo "<font color='green'>���������</font><br><br>"; }
    else { echo "<font color='red'><b>������ �� ����� ����������</b></font>"; }
   }

if (isset($_REQUEST['edit_id'])) {
	$query = "UPDATE `buildings` SET `name` = '".$_REQUEST['sname']."', `full_name` = '".$_REQUEST['fname']."', `type` = '".$_REQUEST['type']."' WHERE `id` = '".$_REQUEST['edit_id']."' LIMIT 1;";
    $result = mysql_query($query) or die(mysql_error());
    if ($result) { echo "<font color='green'>���������������</font><br><br>"; }
    else { echo "<font color='red'><b>������ �� ����� ��������������</b></font>"; }
   }
?>


<?if (!isset ($_REQUEST['edit']))
    {
?>
<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>��������:  </strong> </p></td>
</tr><tr><td>


<form name="depts" method="post" action="?act=umo_manager">
<table>
<tr>
  <td>������� ��������</td>
  <td><input name="sname" type="text" size="20" value=""></td>
</tr>
<tr>
  <td>������ ��������</td>
  <td><input name="fname" type="text" size="20" value=""></td>
</tr>
<tr>
  <td>���</td>
  <td><select name="type">
    <option value="1">�����</option>
    <option value="2">�����������</option>
    <option value="3">���������� ������</option>
    <option value="4">��������</option>
    </select>
</td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td><br><input style="CURSOR: hand; BACKGROUND-IMAGE: url(i/input.gif);" type="submit" name="add" value="��������"></td>
</tr>
</form>
</table><br>
<?
	}
else
	{
    	$query = "SELECT * from `buildings` WHERE `id` = '".$_REQUEST['edit']."' LIMIT 1;";
        $res = mysql_query($query);
        $row = mysql_fetch_assoc($res);
?>
<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>�������������:  </strong> </p></td>
</tr><tr><td>

<form name="depts" method="post" action="?act=umo_manager">
<table>
<tr>
  <td>������� ��������</td>
  <td><input name="sname" type="text" size="20" value="<?=$row['name']?>"></td>
</tr>
<tr>
  <td>������ ��������</td>
  <td><input name="fname" type="text" size="20" value="<?=$row['full_name']?>"></td>
</tr>
<tr>
  <td>���</td>
  <td><select name="type">
    <option value="1"<?if ($row['type'] == 1) {echo (" selected");}?>>�����</option>
    <option value="2"<?if ($row['type'] == 2) {echo (" selected");}?>>�����������</option>
    <option value="3"<?if ($row['type'] == 3) {echo (" selected");}?>>���������� ������</option>
    <option value="4"<?if ($row['type'] == 4) {echo (" selected");}?>>��������</option>
    </select>
</td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td><br>
  <input type="hidden" name="edit_id" value="<?=$_REQUEST['edit']?>">
  <input style="CURSOR: hand; BACKGROUND-IMAGE: url(i/input.gif);" type="submit" name="edit_umo" value="���������"></td>
</tr>
</form>
</table><br>
<center>
<?
	}
//Plants
  $query = "SELECT * FROM `buildings` WHERE `type` = 1";
  $result = mysql_query($query) or die (mysql_error());
  if (mysql_num_rows($result) > 0 ) {
?>
<br>
<center><br><br><b>������</b></center>
<?
  while ($row = mysql_fetch_assoc($result)) {
?>
<?=$row['name']?> <b>[<a href="?act=umo_manager&edit=<?=$row['id']?>">E</a>] [<a href="?act=umo_manager&del=<?=$row['id']?>" onClick="if(!confirm('�� �������?')) {return false}">X</a>]</b><br>
<?
  }
}
//Labs
  $query = "SELECT * FROM `buildings` WHERE `type` = 2";
  $result = mysql_query($query) or die (mysql_error());
  if (mysql_num_rows($result) > 0 ) {
?>
<br>
<center><br><br><b>�����������</b></center>
<?
  while ($row = mysql_fetch_assoc($result)) {
?>
<?=$row['name']?> <b>[<a href="?act=umo_manager&edit=<?=$row['id']?>">E</a>] [<a href="?act=umo_manager&del=<?=$row['id']?>" onClick="if(!confirm('�� �������?')) {return false}">X</a>]</b><br>
<?
  }
}
//Cells
  $query = "SELECT * FROM `buildings` WHERE `type` = 3";
  $result = mysql_query($query) or die (mysql_error());
  if (mysql_num_rows($result) > 0 ) {
?>
<br>
<center><br><br><b>���������� ������</b></center>
<?
  while ($row = mysql_fetch_assoc($result)) {
?>
<?=$row['name']?> <b>[<a href="?act=umo_manager&edit=<?=$row['id']?>">E</a>] [<a href="?act=umo_manager&del=<?=$row['id']?>" onClick="if(!confirm('�� �������?')) {return false}">X</a>]</b><br>
<?
  }
}
//Trucks
  $query = "SELECT * FROM `buildings` WHERE `type` = 4";
  $result = mysql_query($query) or die (mysql_error());
  if (mysql_num_rows($result) > 0 ) {
?>
<br>
<center><br><br><b>���������</b></center>
<?
  while ($row = mysql_fetch_assoc($result)) {
?>
<?=$row['name']?> <b>[<a href="?act=umo_manager&edit=<?=$row['id']?>">E</a>] [<a href="?act=umo_manager&del=<?=$row['id']?>" onClick="if(!confirm('�� �������?')) {return false}">X</a>]</b><br>
<?
  }
}
  }
?>
</center>