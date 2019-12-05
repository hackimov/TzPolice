<h1>Чёрный список</h1>

<?php if(AuthStatus==1 && AuthUserName!="" && AuthUserGroup>1) {
if ($makeadd) {
  if (!$new_ip || !$new_reason) { echo "<font color='red'>Заполните все поля</font><br><br>"; }
  else {
    $result = mysql_query("SELECT * FROM site_blacklist WHERE (ip='$new_ip')");
    if (mysql_num_rows($result) > 0 ) { echo "<font color='red'>Данный IP уже занесён в чёрный список</font><br><br>"; }
    else {
        $result = mysql_query("INSERT into site_blacklist (ip,reason) values ('$new_ip','$new_reason')");
        if ($result) { echo "<font color='green'>IP добавлен в чёрный список</font><br><br>"; }
        else { echo "<font color='red'>Ошибка во время добавления IP</font><br><br>"; }
    }
  }
}
if ($delete) {
  $result = mysql_query("SELECT * FROM site_blacklist WHERE (id='$delete')");
  if (mysql_num_rows($result) == 0 ) { echo "<font color='red'>Такой записи не существует</font><br><br>"; }
  else {
    $result = mysql_query("DELETE FROM site_blacklist where(id='$delete')");
    if ($result) { echo "<font color='green'>Запись успешно удалена</font><br><br>"; }
    else { echo "<font color='red'>Ошибка во время удаления записи</font><br><br>"; }
  }
}
?>
<table border="0" cellspacing="0" cellpadding="3" width="500" style="BORDER: 1px #957850 solid;">
<tr>
  <td width="150" style="BORDER-BOTTOM: 1px #957850 solid; BORDER-RIGHT: 1px #957850 solid;" background="i/bgr-grid-sand.gif" align="center"><b>IP</b></td>
  <td style="BORDER-BOTTOM: 1px #957850 solid; BORDER-RIGHT: 1px #957850 solid;" background="i/bgr-grid-sand.gif" align="center"><b>Причина</b></td>
  <td width="1" style="BORDER-BOTTOM: 1px #957850 solid" background="i/bgr-grid-sand.gif" align="center"><b>Действия</b></td>
</tr>
<?php
  $bgstr[0]="background='i/bgr-grid-sand.gif'";
  $bgstr[1]="background='i/bgr-grid-sand1.gif'";

  $result = mysql_query("SELECT * FROM site_blacklist");
  if (mysql_num_rows($result) == 0) { echo "<tr><td ".$bgstr[1]." colspan='3' align='center'>Список пуст</td></tr>"; }
  else {
    $np=0;
    while ($row = mysql_fetch_assoc($result)) {
      if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}
      echo "
        <tr>
          <td ".$bg." style='BORDER-RIGHT: 1px #957850 solid' align='center'>".$row["ip"]."</td>
          <td ".$bg." style='BORDER-RIGHT: 1px #957850 solid'>".$row["reason"]."</td>
          <td ".$bg." align='center'><a href='?act=blacklist&delete=".$row["id"]."'>Удалить</td>
        </tr>
      ";
    }
  }
?>
</table>
<br><br>
<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Добавить запись:  </strong> </p></td>
</tr><tr><td>

<form method="GET">
<input name="act" type="hidden" value="blacklist">
IP: <input name="new_ip" type="text" value=""> Причина: <input size="40" name="new_reason" type="text" value="">
 <input name="makeadd" style="CURSOR: hand" type="submit" value="Добавить">
</form>

</td></tsr>
</table>
<?php } else { echo $mess['AccessDenied']; } ?>