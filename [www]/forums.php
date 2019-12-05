<?php
 session_start();
 include "setup.php";
$dbase = mysql_connect($db_host , $db_login , $db_password);
 if(!$dbase) echo "Connection ERROR! ";
 if(!mysql_select_db($db_name)) echo "Selection ERROR! ";
 if ($act=='user_login') //User прилогинился
 {
  $user_login=$HTTP_POST_VARS['user_login'];
  $user_password=$HTTP_POST_VARS['user_password'];

  if (Password_Exists($user_login,$user_password)==1)
  {
   $_SESSION['user_login'] = $user_login;
   $_SESSION['user_password']   = $user_password;
   $_SESSION['statusname']   = Get_User_Status($user_login);
  }
 }
 if ($act=='user_logout') //User отлогинился
 {
  session_unset();
  session_destroy();
 }

 $status=mysql_query("SELECT * FROM $db_status WHERE statusname='".$_SESSION['statusname']."'") or die (mysql_error());

 echo join("",file("diz_top.inc"));

if ($_SESSION['user_login']<>"")
 {//user_logged in
//-------------------------------------------------------------------------------------------------
echo
"<div style='position:absolute; top:0; left:567; width:155; height:400;'>
<table width='237' height='93' background='images/bgauth.gif' cellspacing='0' cellpadding='0' border='0' valign='top'>
<tr><td>&nbsp;&nbsp;&nbsp;</td>
<td><br><font size='1' color='#FFFFFF'>Добро пожаловать, <b>",$_SESSION['user_login'],"</b>!<li>";

if (mysql_result($status,0,"admin")>0)
                  echo "<b>&nbsp &nbsp<a href=index.php?act=admin_panel>Панель Администратора</a>&nbsp &nbsp</b>";
          else
                  echo "<b>&nbsp &nbsp<a href=index.php?act=user_panel>Панель Управления</a>&nbsp &nbsp</b>";

echo "<li><b>&nbsp &nbsp<a href=index.php?act=user_logout>Выйти</i></a>&nbsp &nbsp</b>";
echo "</font></td></tr></table></div>";

 }

 else

{
 echo
"<div style='position:absolute; top:0; left:567; width:155; height:400;'>";

echo "

<table background='images/bgauth.gif' width='237' height='93' cellpadding='0' cellspacing='0'>
<tr><td>
<br><br>
 <form action='index.php' method='post'>
 <input type='hidden' name='act' value='user_login'>
 <b>&nbsp&nbsp<font color=#FFFFFF size=2>Login</font></b> <input type='Text' name='user_login' value='' class='SRSmallTextEdit'></td></tr>
<tr><td>
<b>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<font color=#FFFFFF size=2>Pass</font></b> <input type='Password' name='user_password' value='' class='SRSmallTextEdit2'>
&nbsp<input type='Image' width='17' height='10' value='' src='images/top12_1.gif'>
        </form></table></div>";
echo "<div style='position:absolute; top:79; left:665; width:154; height:400;'>";
echo "<b>&nbsp &nbsp<a href='index.php?act=user_add'><IMG SRC='images/top_14.jpg' WIDTH=99 HEIGHT=16 BORDER=0 ALT=''></a>&nbsp</b>";
echo "</div>" ;

//-------------------------------------------------------------------------------------------------

 }
 mysql_close($dbase);
 ?>

<script language='JavaScript' type="text/javascript">
<!--
function input_text() {
  document.TEXT.url.value = "";
}
function inf(name, id, level, align, klan)
{
  var s="";

  if (align!="0") s+="<A HREF=/encicl/alignment.html target=_blank><IMG SRC='http://img.combats.ru/i/align"+align+".gif' WIDTH=12 HEIGHT=15 ALT=\""+getalign(align)+"\"></A>";
  if (klan) s+="<A HREF='/encicl/klan/"+klan+".html' target=_blank><IMG SRC='http://img.combats.ru/i/klan/"+klan+".gif' WIDTH=24 HEIGHT=15 ALT=''></A>";
  s+="<B>"+name+"</B>";
  if (level!=-1) s+=" ["+level+"]";
  if (id!=-1) s+="<A HREF='http://capitalcity.combats.ru/inf.pl?"+id+"' target='_blank'><IMG SRC=http://img.combats.ru/i/inf.gif WIDTH=12 HEIGHT=11 ALT='Инф. о "+name+"'></A>";

  document.write(s);
}
//-->
</script>

<body>
<?php if (!$showsearch) { ?>
<TABLE width="100%"  cellpadding="0" cellspacing=3 border="0">
<tr><td><a href="?city=capitalcity">Capitalcity</a></td><td><a href="?city=angelscity">Angelscity</a></td><td><a href="?city=demonscity">Demonscity</a></td><td><a href="?city=devilscity">Devilscity</a></td><td><a href="?city=suncity">Suncity</a></td><td><a href="?city=sandcity">Sandcity</a></td><td><a href="?city=mooncity">Mooncity</a></td><td><a href="?city=emeraldscity">Emeraldscity</a></td></tr>
<tr><td align="center" colspan="8"><br>
<?php
echo "Форум города ";
print($city);
?><br>
<FORM name="TEXT" method="post">
<input name="url" type="text" value="Введите ссылку на топик форума" onclick="input_text()"  size="60">
<INPUT type="submit" value="смотреть">
</FORM>
</td></tr></TABLE>
<TABLE width="100%" cellpadding=0 cellspacing=0 border="0"><tr><td>
<?php } else { ?>
<TABLE width="100%"  cellpadding="0" cellspacing=3 border="0">
<tr><td align="center"><h3>Поиск по форумам</h3><br><br></td></tr>
<form action="" method="post">
<tr><td align="center">
<table border="0" cellspacing="0" cellpadding="10" style="BORDER: 1px solid #808080">
<tr><td bgcolor="#E6E6E6">Строка для поиска:&nbsp;<input name="srch_str" type="text" value="" size="30"></td></tr>
<tr><td bgcolor="#FFFFFF" align="center">
  <table>
    <tr><td>Город:&nbsp;&nbsp;&nbsp;</td><td>
      <select size="1" name="srch_city" style="WIDTH: 143px">
        <option value="all">Искать везде</option>
        <option value="capitalcity">Capital city</option>
        <option value="angelscity">Angels city</option>
        <option value="demonscity">Demons city</option>
        <option value="devilscity">Devils city</option>
        <option value="suncity">Sun city</option>
        <option value="sandcity">Sand city</option>
        <option value="mooncity">Moon city</option>
        <option value="newcapital">New Capital city</option>
        <option value="emeraldscity">Emeralds city</option>
      </select>
    </td></tr>
    <tr><td>Конференция:&nbsp;&nbsp;&nbsp;</td><td>
      <select size="1" name="srch_conf">
        <option value="all">Искать везде</option>
        <option value="index">Общая</option>
        <option value="other">Обо всем</option>
        <option value="opinion">Мнения</option>
        <option value="contest">Конкурсы</option>
        <option value="creative">Творчество</option>
        <option value="mervote">Выборы</option>
        <option value="rebuild">Предложения</option>
        <option value="jeremiad">Жалобы</option>
        <option value="sales">Сделки</option>
        <option value="job">Работа</option>
        <option value="news">Новости</option>
        <option value="paladins">Паладины сообщают</option>
        <option value="paladins2">Фонд пострадавших</option>
        <option value="klans">Кланы</option>
      </select>
    </td></tr>
  </table>
</td></tr>
</table>
</td></tr>
</form>
</table>
<?php
}
if ($showsearch) {
  echo "OK";
} elseif(!$url && !$showsearch){
   if(!$city)
      $city="capitalcity";
   $imjafaila="http://$city.combats.ru/forum.pl?n=$n&id=$id&p=$p";
   $fh=gzopen($imjafaila, "r");
   while (!feof($fh)){
      $file.= fgets($fh,1024);}
} else {
   $imjafaila=$url;
   for($k=0;$k<1;$k++){
     $check = eregi("http://capitalcity.combats.ru/forum.pl",$imjafaila);
	 if($check) break;
     $check = eregi("http://angelscity.combats.ru/forum.pl",$imjafaila);
	 if($check) break;
     $check = eregi("http://demonscity.combats.ru/forum.pl",$imjafaila);
	 if($check) break;
     $check = eregi("http://devilscity.combats.ru/forum.pl",$imjafaila);
	 if($check) break;
     $check = eregi("http://suncity.combats.ru/forum.pl",$imjafaila);
	 if($check) break;
     $check = eregi("http://sandcity.combats.ru/forum.pl",$imjafaila);
	 if($check) break;
     $check = eregi("http://mooncity.combats.ru/forum.pl",$imjafaila);
	 if($check) break;
     $check = eregi("http://emeraldscity.combats.ru/forum.pl",$imjafaila);
	 if($check) break;
   }
   if(!$check)
      die("Неверная ссылка");
   $fh=gzopen($imjafaila, "r");
   while (!feof($fh)){
      $file.= fgets($fh,1024);}
}
if (!$showsearch) {
$file=str_replace('/forum.pl?',"?city=$city&",$file);
$file=str_replace('<img src="http://img.combats.ru/i/register/sitebk_03ru.gif" width="194" height="135" border=0>',"",$file);
$file=eregi_replace('<style type="text/css">.+</style>',"",$file);
$file=str_replace('<table width="100%" height="135"  border="0" cellpadding="0" cellspacing="0">',"",$file);
$file=str_replace('bgcolor="#000000"',"bgcolor=white",$file);
$file=str_replace('drwfl',"inf",$file);
$file=str_replace('bgcolor=F6E5B1',"",$file);
$file=str_replace('bgcolor=#f2e5b1',"",$file);
$file=str_replace('Поиск <INPUT TYPE=text class="inup" NAME=search value="" size=20 onclick=""><INPUT TYPE=submit class="btn" value="найти">',"<a href='".$PHP_SELF."?showsearch=1'>Поиск по форуму</a>",$file);
$file=str_replace("bgcolor='#3D3D3B'","",$file);
$file=str_replace('background="http://img.combats.ru/i/register/n21_08_1.jpg"',"",$file);
$file=str_replace('background="http://img.combats.ru/i/register/ram12_34.gif"',"",$file);
$file=str_replace('background="http://img.combats.ru/i/register/nnn21_03_1.jpg"',"",$file);
$file=str_replace('background="http://img.combats.ru/i/register/sitebk_07.jpg"',"",$file);
$file=str_replace('<IMG height=236 src="http://img.combats.ru/i/register/fr_15.jpg" width=128 border=0>',"",$file);
$file=str_replace('<IMG height=236 src="http://img.combats.ru/i/register/fr_15.jpg" width=128 border=0>',"",$file);
$file=str_replace('<img src="http://img.combats.ru/i/register/fr_04.jpg" width="118" height="257">',"",$file);
$file=str_replace('<img height=144 src="http://img.combats.ru/i/register/forumru_03.jpg" width=139 border=0>',"",$file);
$file=str_replace('<img src="http://img.combats.ru/i/register/formz_10.gif" width="131" height="26">',"",$file);
$file=str_replace('background="http://img.combats.ru/i/register/sitebk_02.jpg"',"",$file);
$file=str_replace('<img src="http://img.combats.ru/i/register/fr_08.jpg" width="29" height="256">',"",$file);
$file=str_replace('<img src="http://img.combats.ru/i/register/ram12_35.gif" width="13" height="11">',"",$file);
$file=str_replace('<img src="http://img.combats.ru/i/register/ram12_33.gif" width="12" height="11">',"",$file);
$file=eregi_replace('<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor=#000000>.+',"",$file);
$file=str_replace('<body topmargin=0 leftmargin=0 marginheight=0 marginwidth=0>',"",$file);
print($file);
}

?>

<?php
  echo join("",file("diz_bot.inc"));
  ?>