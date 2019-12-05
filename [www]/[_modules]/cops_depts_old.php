<h1>Состав, отделы</h1>

<?php
  $depts = array(
    "модерация"=>1,
    "юридический"=>2,
    "пресс-служба"=>3,
    "отдел кадров"=>14,
    "it"=>4,
    "ит"=>4,
    "финансово-экономический"=>5,
    "экономический"=>5,
    "лицензионный"=>6,
    "лицензирования"=>6,
    "боевой (москва)"=>7,
    "боевой (нева)"=>8,
    "боевой (оазис)"=>9,
    "по борьбе с прокачками"=>10,
    "расследований"=>11,
    "полиция"=>12,
  );
?>
<?if(AuthStatus==1 && AuthUserGroup>50) {?>
<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Добавить в состав:  </strong> </p></td>
</tr><tr><td>

<?php
if ($makeadd) {
  if (!$u_nick) { echo "<font color='red'><b>Введите ник</b></font>"; }
  else {
    $result = mysql_query("SELECT * FROM cops_depts WHERE (name='$u_nick')");
    if (mysql_num_rows($result) > 0 ) { echo "<font color='red'><b>Указаный ник уже занесён в список</b></font>"; }
    else {
    $userinfo = GetUserInfo($u_nick,1);
      if (!$userinfo["error"]) {
        setlocale(LC_ALL,'ru_RU.CP1251');
        $temp_dept =  $depts[strtolower($userinfo["s1"])];
        if ($temp_dept == 0) { echo "<font color='red'><b>Не удалось определить отдел.<br>Incorrect value \"".$userinfo["s1"]."\"</b></font>"; }
        else {
          if (!$u_pol) { $u_cop=0; }
          $result = mysql_query("INSERT into cops_depts (name,id_dept,ischief,police) values ('$u_nick','$temp_dept','$u_ischief','$u_pol')");
          if ($result) { echo "<font color='green'>Персонаж добавлен в состав</font><br><br>"; }
        }
      } else { echo "<font color='red'><b>Указанного персонажа не существует</b></font><br><br>"; }
    }
  }
}
if ($delete && !$makeadd) {
  $result = mysql_query("SELECT * FROM cops_depts WHERE (id='$delete')");
  if (mysql_num_rows($result) == 0 ) { echo "<font color='red'><b>Указанного пользователя нету в списке</b></font>"; }
  else {
    $result = mysql_query("DELETE FROM cops_depts where(id='$delete')");
    if ($result) { echo "<font color='green'>Персонаж успешно удалён</font><br><br>"; }
    else { echo "<font color='red'><b>Ошибка во время удаления персонажа</b></font>"; }
  }
}
?>

<form method="post" action="">
<input name="act" type="hidden" value="cops_depts">
<table>
<tr>
  <td>Ник:&nbsp;</td>
  <td><input name="u_nick" type="text" size="20" value=""></td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td><input name="u_pol" type="checkbox" value="1">&nbsp;Курсант  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td><input name="u_ischief" type="checkbox" value="1">&nbsp;Начальник отдела  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td><br><input style="CURSOR: hand; BACKGROUND-IMAGE: url(i/input.gif);" type="submit" name="makeadd" value="Добавить"></td>
</tr>
</form>
</table><br>

</td></tsr>
</table>
<?php } ?>
<table>
<?php
  $result = mysql_query("SELECT * FROM cops_depts WHERE (id_dept='12' AND ischief='1')");
  if (mysql_num_rows($result) > 0 ) {
    $row = mysql_fetch_assoc($result);
    echo "<tr><td><font color='#A92C22'><b>Начальник полиции:</b></font>&nbsp;</td><td><img src='_imgs/clans/police.gif'><b><a href='http://www.timezero.ru/info.html?".$row["name"]."' target='_blank'>".$row["name"]."</a></b>";
    if(AuthStatus==1 && AuthUserGroup>50) { echo "&nbsp;<b>[<a href='?act=cops_depts&delete=".$row["id"]."'>X</a>]</b>"; }
    echo "</td></tr>";
  }
  $result = mysql_query("SELECT * FROM cops_depts WHERE (id_dept='17')");
  if (mysql_num_rows($result) > 0 ) {
    $row = mysql_fetch_assoc($result);
    echo "<tr><td><font color='#A92C22'><b>Зам. начальника:</b></font>&nbsp;</td><td><img src='_imgs/clans/police.gif'><b><a href='http://www.timezero.ru/info.html?".$row["name"]."' target='_blank'>".$row["name"]."</a></b>";
    if(AuthStatus==1 && AuthUserGroup>50) { echo "&nbsp;<b>[<a href='?act=cops_depts&delete=".$row["id"]."'>X</a>]</b>"; }
    echo "</td></tr>";
  }
  $result = mysql_query("SELECT * FROM cops_depts WHERE (id_dept='18')");
  if (mysql_num_rows($result) > 0 ) {
    $row = mysql_fetch_assoc($result);
    echo "<tr><td><font color='#A92C22'><b>Проректор Академии:</b></font>&nbsp;</td><td><img src='_imgs/clans/police.gif'><b><a href='http://www.timezero.ru/info.html?".$row["name"]."' target='_blank'>".$row["name"]."</a></b>";
    if(AuthStatus==1 && AuthUserGroup>50) { echo "&nbsp;<b>[<a href='?act=cops_depts&delete=".$row["id"]."'>X</a>]</b>"; }
    echo "</td></tr>";
  }
  $result = mysql_query("SELECT * FROM cops_depts WHERE (id_dept='15')");
  if (mysql_num_rows($result) > 0 ) {
    $row = mysql_fetch_assoc($result);
    echo "<tr><td><br><font color='#A92C22'><b>Ветеран:</b></font>&nbsp;</td><td><img src='_imgs/clans/police.gif'><b><a href='http://www.timezero.ru/info.html?".$row["name"]."' target='_blank'>".$row["name"]."</a></b>";
    if(AuthStatus==1 && AuthUserGroup>50) { echo "&nbsp;<b>[<a href='?act=cops_depts&delete=".$row["id"]."'>X</a>]</b>"; }
    echo "</td></tr>";
  }
?>
</table>

<center>
<?php
  $result = mysql_query("SELECT * FROM depts WHERE (id <> '12' AND id <> '17' AND id <> '18')");
  if (mysql_num_rows($result) > 0 ) {
?>
<script language=javascript src="_modules/tabs.js"></script>
<link href="_modules/tabs.css" type=text/css rel=stylesheet>
<form action="" method="post">
<table border="0" cellspacing="0" cellpadding="0" width="100%" id="tb_content">
<tr>
<?php
    $i = 0;
    while ($row = mysql_fetch_assoc($result)) {
      if ($i == 0) { $temp_style = "on"; } else { $temp_style = "off"; }
      echo "<td height='20' class='tab-".$temp_style."' "; if ($row["id"] == 3) { echo "style='WIDTH: 88px;'"; } echo " id='navcell' onclick='switchCell(".$i.")' valign='middle' nowrap><b>&nbsp;".$row["short_name"]."&nbsp;</b></td>";
      $i++;
    }
?>
  <TD class=tab-none noWrap><FONT face=Tahoma color="#ffffff">&nbsp;</TD>
</tr>
</table>
<?php
  $result = mysql_query("SELECT * FROM depts WHERE (id <> '12' AND id <> '17' AND id <> '18')");
  while ($row = mysql_fetch_assoc($result)) {
    $result1 = mysql_query("SELECT * FROM cops_depts WHERE (id_dept = '$row[id]' AND ischief='1')");
?>
<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0"><tr><td valign="top">
<p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;">
</td></tr><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><?=$row["name"]?>:</strong> </p></td>
</tr><tr><td><br>
<?php
    if (mysql_num_rows($result1) > 0 ) {
      $row1 = mysql_fetch_assoc($result1);
      echo "<table><tr><td><font style='MARGIN-LEFT: 10px' color='#A92C22'><b>Начальник отдела:</b></font>&nbsp;<img src='_imgs/clans/".($row1['police']?'PoliceAcademy':'police').".gif'><b><a href='http://www.timezero.ru/info.html?".$row1["name"]."' target='_blank'>".$row1["name"]."</a></b>";
      if(AuthStatus==1 && AuthUserGroup>50) { echo "&nbsp;<b>[<a href='?act=cops_depts&delete=".$row1["id"]."'>X</a>]</b>"; }
      echo "</td></tr></table>";
    }
    $result1 = mysql_query("SELECT * FROM cops_depts WHERE (id_dept = '$row[id]' AND ischief='0') ORDER BY police");
    if (mysql_num_rows($result1) > 0 ) {
      echo "<table cellspacing='0' cellpadding='2' border='0'><tr><td colspan='2'><br><font style='MARGIN-LEFT: 10px' color='#A92C22'><b>Сотрудники отдела:</b></font></td>";
      $i = 1;
      while ($row1 = mysql_fetch_assoc($result1)) {
        echo "<tr><td><font style='MARGIN-LEFT: 10px'><b>".$i.".</b></font></td><td width='100%'><img src='_imgs/clans/".($row1['police']?'PoliceAcademy':'police').".gif'><b><a href='http://www.timezero.ru/info.html?".$row1["name"]."' target='_blank'>".$row1["name"]."</a></b>";
        if(AuthStatus==1 && AuthUserGroup>50) { echo "&nbsp;<b>[<a href='?act=cops_depts&delete=".$row1["id"]."'>X</a>]</b>"; }
        echo "</td></tr>";
        $i++;
      }
      echo "</table>";
    } else { echo "<center><b>Список пуст</b></center><br>"; }
?>
</p>
</td></tr></table>
<?php
  }
?>

<?php
  } else { echo "<center><b>Список пуст</b></center>"; }
?>

</center>