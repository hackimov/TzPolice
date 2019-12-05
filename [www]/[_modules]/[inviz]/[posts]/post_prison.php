<h1>Отчетность по посту на каторге</h1>
<?php

include("post_cron.php");

function get_norm() {
  $post = 'prison_norm';
  $dop = 0;
  
  $result = mysql_query(
    'select d.sname as dept, c.chief, c.deputy from sd_cops c left join sd_depts d '.
    'on c.dept = d.id where (c.name=\''.AuthUserName.'\') and (alias=0)'
  );
  $dept = mysql_fetch_assoc($result);

  $result = mysql_query("SELECT * FROM om_stat_min where user='".$nick."' AND id_week='".date('W', time())."'");
  if (mysql_num_rows($result) > 0){
    $row = mysql_fetch_assoc($result);
    if ($post == 'forum_norm'){
      $dop = $row['min_forum'];
    } else {
      $dop = $row['min_chat'];
    }
  }
  
  $result = mysql_query("SELECT * FROM om_stat_sp_users where user='".$nick."'");
  if (mysql_num_rows($result) > 0){
    $row = mysql_fetch_assoc($result);
    $norm = $row[$post];
  } else {
    $result = mysql_query("SELECT * FROM om_stat_norm where `otdel` ='".$dept['dept']."'");
    if (mysql_num_rows($result) > 0){
      $row = mysql_fetch_assoc($result);
      $norm = $row[$post];
    } else {
      $result = mysql_query("SELECT * FROM om_stat_norm where id='9999999'");
      $row = mysql_fetch_assoc($result);
      $norm = $row[$post];
    }
  }
  
  if (($dept['chief'] == 1) || ($dept['deputy'] == 1)){
    $norm = $norm/2;
  }

  return ($norm-$dop)/2;
}

extract($_REQUEST);

// Временная мера для отсечения старых логов...
        $old = time() - 15552000; //(полгода)
$bgstr[0]="background='i/bgr-grid-sand.gif'";
$bgstr[1]="background='i/bgr-grid-sand1.gif'";

if(AuthStatus==1 && AuthUserName!="" && (AuthUserClan=='police' || AuthUserClan=='Police Academy' || AuthUserClan=='PoliceAcademy' || AuthUserGroup=='100')) {

   $depts = array(
    "мод."=>1,
    "юрид."=>2,
    "пресс"=>3,
    "IT"=>4,
    "эконом."=>5,
    "лиц."=>6,
    "боевой м"=>7,
    "боевой н"=>8,
    "боевой о"=>9,
    "прокачки"=>10,
    "рассл."=>11,
    "пол."=>12,
    "кадры"=>14,
    "ветеран"=>15,
    "обэп"=>20,
    "снабж."=>23
  );

 $depts = array_flip($depts);
function get_dept($u) {
        if (list($id_dept, $chief) = mysql_fetch_array(mysql_query("SELECT dept, chief FROM sd_cops WHERE name='$u' AND alias=0"))) {
                if (list($dept) = mysql_fetch_array(mysql_query("SELECT sname FROM sd_depts WHERE id='$id_dept'"))) {
                        $str=" (".$dept.($chief?", нач.":"").")";
                        return $str;
                } else return $str;
        } else return $str;
}

?>

<table width='100%' border='0' cellspacing='3' cellpadding='2'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Рапорта</strong> </p></td>
</tr><tr><td align=center>

<?
$SQL="SELECT p.*, u.id AS UID, u.user_name AS Uname FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user where city=6 ORDER BY id DESC LIMIT 1";
$r=mysql_query($SQL);
$d=mysql_fetch_array($r);
$on_post_u_id=$d['id_user'];
$last_id=$d['id'];
if(@$d['post_g']>0) {

$SQL2="SELECT
        p.id_user,
        u.user_name,
		p.post,
		p.post_time
        FROM posts_order p
        LEFT JOIN site_users u ON u.id=p.id_user
        WHERE p.post=6 order BY p.post_time ASC LIMIT 1
  ";
  $r2=mysql_query($SQL2);
  $in_order= mysql_num_rows($r2);
  $d2=mysql_fetch_array($r2);
  $fast_order_id_user=$d2['id_user'];
  
  $t=time()+600;
  $t2=time()-600;
  $SQL2="SELECT
        p.id_user,
        u.user_name,
		p.post,
		p.post_date_from,
		p.confirmed
        FROM posts_order_entry p
        LEFT JOIN site_users u ON u.id=p.id_user
        WHERE p.post=6 and p.post_date_from<'".time()."' and p.post_date_from<'".$t."' and p.post_date_from>".$t2." and p.confirmed=1 order BY p.post_date_from ASC LIMIT 1
  ";
  $r2=mysql_query($SQL2);
  $entry= mysql_num_rows($r2);
  $d2=mysql_fetch_array($r2);
  //extract($d2,EXTR_PREFIX_ALL,"d2");
  $entry_order_id_user=$d2['id_user'];
  //print_r($d2); echo "<br>";
  
?>

Сейчас на посту никого с <b><?=date("d.m.y H:i:s",$d['post_g'])?></b>

<form method="GET">
<input name="act" type="hidden" value="post_prison">
<input name="a" type="hidden" value="TakePost">
<input type="submit" value="принять пост" <?php 
if(($entry!=0)&&($entry_order_id_user!=AuthUserId)){
	echo "disabled=\"disabled\"";
} else {
	if(($in_order!=0)&&($fast_order_id_user!=AuthUserId)){
		echo "disabled=\"disabled\"";
	} 
}

?>>
</form>

<?

} else {
    $last_id=$d['id'];
        $lasts=round((time()-$d['post_t'])/60);

    echo "Сейчас на посту: <b>{$d['Uname']}</b> <br>С <b>".date("H:i", $d['post_t'])."</b> ($lasts мин)";
	$on_post_time=$lasts;

    if($d['UID']==AuthUserId) {
       

?>

<form method="GET">
<input name="act" type="hidden" value="post_prison">
<input name="a" type="hidden" value="GivePost">
<input type="submit" value="сдать пост">
</form>

<?



    } else {
        

?>

<form method="GET" id="reasoncheck">
<div>Причина: <input name="reason" type="text" size=100 value=""></div><br>
<input name="act" type="hidden" value="post_prison">
<input name="a" type="hidden" value="GetPost">
<input type="submit" value="снять с поста">
</form>

<?




    }

}

$SQL2="SELECT
        p.id_user,
        u.user_name,
		p.post,
		p.post_time
        FROM posts_order p
        LEFT JOIN site_users u ON u.id=p.id_user
        WHERE p.post=6 order BY p.post_time ASC
  ";
  $r2=mysql_query($SQL2);
  $in_order= mysql_num_rows($r2);
  
  $SQL2="SELECT
        p.id_user,
        u.user_name,
		p.post,
		p.post_time
        FROM posts_order p
        LEFT JOIN site_users u ON u.id=p.id_user
        WHERE p.post=6 AND p.id_user='".AuthUserId."'
  ";
  $r2=mysql_query($SQL2);
  $ex= mysql_num_rows($r2);
//$my_IsNorm=0;
if(($d['UID']==AuthUserId)&&($d['post_g']==0)){$iam_on_post=1;} else {$iam_on_post=0;}

//if(($my_IsNorm==0)&&($in_order<=5)&&($ex==0)&&($iam_on_post==0)&&($d['post_g']==0)){
if(($in_order<=5)&&($ex==0)&&($iam_on_post==0)&&($d['post_g']==0)){
?>
<form method="GET">
<input name="act" type="hidden" value="post_prison">
<input name="a" type="hidden" value="in_order">
<input type="submit" value="встать в очередь">
</form>
<?php 
}

  $executed_norm_minutes = 0;
  $norm_r=mysql_query(
    'SELECT post_g, post_t FROM posts_report WHERE (id_week='.date("W").') AND (city=6) AND (id_user=\''.
    AuthUserId.'\') order by id asc'
  );
  while($norm_row=mysql_fetch_assoc($norm_r)) {
    if ($norm_row['post_g'] > 0)
      $executed_norm_minutes = $executed_norm_minutes + ($norm_row['post_g']-$norm_row['post_t']);
    else
      $executed_norm_minutes = $executed_norm_minutes + (time()-$norm_row['post_t']);
  }
  $executed_norm_minutes = round($executed_norm_minutes/60);

  $my_norm=get_norm();
  
  $executed_norm=round((($executed_norm_minutes)/($my_norm))*100,2);
  if (($executed_norm >= 100)||($my_norm == 0)) $executed_norm = '<font color=green><b>100%</b></font>';
  else $executed_norm = '<font color=red><b>'.$executed_norm.'%</b></font>';
?>
<table width='100%' border='0' cellspacing='3' cellpadding='2'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Норма:</strong> </p></td>
</tr><tr><td align=center>
<b>Недельная норма:</b> <?=$my_norm?> минут<br>
<b>Выполнено:</b> <?=$executed_norm?> (<?=$executed_norm_minutes?> минут)
</td></tr></table>


<table width='100%' border='0' cellspacing='3' cellpadding='2'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Очередь:</strong> </p></td>
</tr><tr><td align=center>
<?php 
$ex=0;
$SQL="SELECT
        p.id_user,
        u.user_name,
		p.post,
		p.post_time
        FROM posts_order p
        LEFT JOIN site_users u ON u.id=p.id_user WHERE p.post=6 order BY p.post_time ASC";
  $r=mysql_query($SQL);
  //echo mysql_num_rows($r);
  while($d=mysql_fetch_array($r)) {
        extract($d,EXTR_PREFIX_ALL,"d");
		$wait=round((time()-$d_post_time)/60);
		echo "<b>{$d_user_name}</b> (ожидает уже {$wait} мин)<br>";
		if($d_id_user==AuthUserId){$ex=1;}
  }


	if($ex==1){
	?>
	<form method="GET">
	<input name="act" type="hidden" value="post_prison">
	<input name="a" type="hidden" value="out_of_order">
	<input type="submit" value="покинуть очередь">
	</form>
	<?php 
	}

?>
</td></tr></table>

<table width='100%' border='0' cellspacing='3' cellpadding='2'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Записи:</strong></p></td>
</tr><tr><td align=center>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr valign="top">
<td width="40%">

<table cellpadding=3 width=85% cellspacing=3>
<th colspan=2  background='i/bgr-grid-sand.gif'>Ваши записи:</th>
<tr>
<td background='i/bgr-grid-sand.gif' nowrap><b>Дата:</b></td>
<td background='i/bgr-grid-sand.gif' nowrap></td>
</tr>

<?php 
$SQL="select * from posts_order_entry where post=6 and id_user='".AuthUserId."' order by post_date_from ASC";
$r=mysql_query($SQL);
while($d=mysql_fetch_array($r)) {
	extract($d,EXTR_PREFIX_ALL,"d");
	?>
	<tr>
	<td align=left nowrap><?php 
	if($d_confirmed==1){
		echo "<b><font color=green>".date("d.m.Y",$d_post_date_from)."&nbsp;".date("H:i",$d_post_date_from)."</font></b>"; 
	} else {
		echo date("d.m.Y",$d_post_date_from)."&nbsp;".date("H:i",$d_post_date_from); 
	}
	?></td>
	<td nowrap align="center"><a href="?act=post_prison&a=dell_record&eid=<?php echo $d_id; ?>">удалить</a></td>
	</tr>
	<?php 
}
?>

</table>
<?php 
$t=time()+600;
$SQL="select * from posts_order_entry where post=6 and id_user='".AuthUserId."' and post_date_from<'".$t."' and post_date_from>'".time()."' and confirmed=0";
$r=mysql_query($SQL);
while($d=mysql_fetch_array($r)) {
extract($d,EXTR_PREFIX_ALL,"d");
$t=round(($d_post_date_from-time())/60);
echo "<b>В течении {$t} минут вы должны <br>подтвердить запись на пост:</b><br>".date("d.m.Y",$d_post_date_from)."&nbsp;".date("H:i",$d_post_date_from);
?>
<form method="GET">
	<input name="act" type="hidden" value="post_prison">
	<input name="a" type="hidden" value="make_confirm">
	<input name="cid" type="hidden" value="<?php echo $d_id; ?>">
	<input type="submit" value="подтвердить">
	</form>
<?php 
}
?>
</td>
<td>

<?php 
?>


<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="_modules/inviz/posts/callendar/iflateng.htm" scrolling="no" frameborder="0">
</iframe>
	<form method="GET" name="demoForm" method="post">
	<input name="act" type="hidden" value="post_prison">
	<input name="a" type="hidden" value="make_record">
	Время: 
	<select name="hour">
	<option value="00">00</option>
	<option value="01">01</option>
	<option value="02">02</option>
	<option value="03">03</option>
	<option value="04">04</option>
	<option value="05">05</option>
	<option value="06">06</option>
	<option value="07">07</option>
	<option value="08">08</option>
	<option value="09">09</option>
	<option value="10">10</option>
	<option value="11">11</option>
	<option value="12">12</option>
	<option value="13">13</option>
	<option value="14">14</option>
	<option value="15">15</option>
	<option value="16">16</option>
	<option value="17">17</option>
	<option value="18">18</option>
	<option value="19">19</option>
	<option value="20">20</option>
	<option value="21">21</option>
	<option value="22">22</option>
	<option value="23">23</option>
	</select>:<select name="min">
	<option value="00">00</option>
	<option value="05">05</option>
	<option value="10">10</option>
	<option value="15">15</option>
	<option value="20">20</option>
	<option value="25">25</option>
	<option value="30">30</option>
	<option value="35">35</option>
	<option value="40">40</option>
	<option value="45">45</option>
	<option value="50">50</option>
	<option value="55">55</option>
	</select>
	<input type="submit" value="Записаться">
	<input type="hidden" name="dateField" value="<?php echo date("Y")."-".date("m")."-".date("d"); ?>">
	</form>
</td>
</tr>
</table>
</td></tr></table>
<?php 

if($_REQUEST['a']=="make_confirm") {
	$SQL="UPDATE posts_order_entry set confirmed=1 WHERE id_user='".AuthUserId."' AND post='6' and id='".$cid."'";
	mysql_query($SQL);
	echo "<script>top.location.href='?act=post_prison';</script>";
}

if($_REQUEST['a']=="dell_record") {
	$SQL="DELETE FROM posts_order_entry WHERE id_user='".AuthUserId."' AND post='6' and id='".$eid."'";
	mysql_query($SQL);
	echo "<script>top.location.href='?act=post_prison';</script>";
}

if($_REQUEST['a']=="make_record") {
	
	list($y,$m,$d)= split ('[-]', $dateField);
	$stime=mktime($hour, $min, 0, $m, $d, $y);
	$SQL="INSERT INTO posts_order_entry (id_user,post,post_date_from,post_date_to,confirmed) values ('".AuthUserId."','6','".$stime."','0','0')";
	mysql_query($SQL);
	echo "<script>top.location.href='?act=post_prison';</script>";
}


if($_REQUEST['a']=="out_of_order") {
	$SQL="DELETE FROM posts_order WHERE id_user='".AuthUserId."' AND post='6'";
	mysql_query($SQL);
	echo "<script>top.location.href='?act=post_prison';</script>";
}

if($_REQUEST['a']=="in_order") {
	
		
	//if($on_post_user_norm==1){
	//	$SQL="UPDATE posts_report set post_g='".time()."' WHERE city=6 and id_user='".$on_post_u_id."' and post_g=0";
    	//mysql_query($SQL);
    	//echo $SQL="INSERT INTO posts_report (id_user,post_t,id_week,city) values ('".AuthUserId."','".time()."','".date("W")."','6')";
    	//mysql_query($SQL);
	//} else {
	
		$SQL="INSERT INTO posts_order (id_user,post,post_time,must_take_post) values ('".AuthUserId."','6','".time()."',0)";
		mysql_query($SQL);
	//}
	
	
	echo "<script>top.location.href='?act=post_prison';</script>";
}

if($_REQUEST['a']=="TakePost") {
	$SQL="SELECT * FROM posts_report WHERE city=6 and post_g=0 LIMIT 1";
	$r=mysql_query($SQL);
	if (mysql_num_rows($r) > 0)
		{
		echo "<script>alert('ПОСТ ЗАНЯТ!');top.location.href='?act=post_prison';</script>";
		}
	else {
	$SQL="UPDATE posts_report set post_g='".time()."' WHERE city=6 and id_user='".AuthUserId."' and post_g=0";
	mysql_query($SQL);
	$SQL="INSERT INTO posts_report (id_user,post_t,id_week,city) values ('".AuthUserId."','".time()."','".date("W")."','6')";
	mysql_query($SQL);
	$SQL="DELETE FROM posts_order WHERE id_user='".AuthUserId."' AND post='6'";
	mysql_query($SQL);
	echo "<script>top.location.href='?act=post_prison';</script>";
	}
}

if($_REQUEST['a']=="GetPost") {
   $SQL="UPDATE posts_report set post_g='".time()."', comment=' ".htmlspecialchars($_REQUEST['reason'])."' WHERE city=6 and id='$last_id' and post_g=0";
   mysql_query($SQL);
   //$SQL="INSERT INTO posts_report (id_user,post_t,id_week,city) values ('".AuthUserId."','".time()."','".date("W")."','1')";
   //mysql_query($SQL);
   echo "<script>top.location.href='?act=post_prison';</script>";
}
if($_REQUEST['a']=="GivePost") {
   $SQL="UPDATE posts_report set post_g='".time()."' WHERE city=6 and id_user='".AuthUserId."' and post_g=0";
   mysql_query($SQL);
   
    $ex=0;
	$SQL="SELECT
        p.id_user,
        u.user_name,
		p.post,
		p.post_time
        FROM posts_order p
        LEFT JOIN site_users u ON u.id=p.id_user WHERE p.post=6 order BY p.post_time ASC limit 1";
  $r=mysql_query($SQL);
  //echo mysql_num_rows($r);
  while($d=mysql_fetch_array($r)) {
        extract($d,EXTR_PREFIX_ALL,"d");
		$mt=time()+600;
		$SQL="UPDATE posts_order set must_take_post='".$mt."' WHERE post=6 and id_user='".$d_id_user."' and post_time='".$d_post_time."'";
   		mysql_query($SQL);
		
  }
   
   
   
   echo "<script>top.location.href='?act=post_prison';</script>";
}


?>
</center>
</td></tr>
</table>
<?

$q=mysql_query("SELECT name FROM cops_depts WHERE name='".AuthUserName."' AND ischief=1");

if(mysql_num_rows($q)>0 || (abs(AccessLevel) & AccessPoliceStats)) {
?>

<table width='100%' border='0' cellspacing='3' cellpadding='2'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Отчеты:</strong> </p></td>
</tr><tr><td align=center>

<?

echo "
      <a href='?act=post_prison&stats=week'>сводная статистика за текущую неделю</a>
      (сейчас идет <b>".date("W")."</b> неделя)  <BR>
      <a href='?act=post_prison&stats=day'>сводная статистика за день</a>
      (сегодня <b>".date("d.m.Y")."</b>)
     ";
echo "<table cellpadding=3 width=50% cellspacing=3>";

#
# day
#

  if($stats=='day') {
  if(@$_REQUEST['day']) $day=$_REQUEST['day'];
  else $day=date("d.m.Y");
  $day=explode(".",$day);
  $daystamp=mktime(0,0,0,$day[1],$day[0],$day[2]);
  $daystamp2=$daystamp+86400;

  echo "
  <th background='i/bgr-grid-sand.gif'>Выборка:</th>
  <tr><td background='i/bgr-grid-sand.gif' align=center>";

?>

<form method="GET">
<input name="act" type="hidden" value="post_prison">
<input name="stats" type="hidden" value="day">
Дата: <input name="day" type="text" size=8 value="<?=date("d.m.Y",$daystamp)?>"> <input type="submit" value="Поиск">

<?

  echo "
  </td></tr>
  </table></form>
  <table cellpadding=3 width=50% cellspacing=3>";

  $SQL="SELECT (p.post_g-p.post_t) AS time, p.post_g, p.post_t, p.id_user, u.user_name FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user WHERE city=6 and (p.post_t>$daystamp AND post_t<$daystamp2) AND p.post_g>0 ORDER BY p.post_t";
  $r=mysql_query($SQL);
  echo "
  <th colspan=2  background='i/bgr-grid-sand.gif'>Статистика за день</th>
  <tr >
  <td background='i/bgr-grid-sand.gif' nowrap><b>Пользователь:</b></td>
  <td background='i/bgr-grid-sand.gif' nowrap><b>Дежурство:</b></td>
  </tr>";

     while($d=mysql_fetch_array($r)) {
        $Utime_min=round($d['time']/60);
        $Time1=date("H:i:s", $d['post_t']);
        $Time2=date("H:i:s", $d['post_g']);

        echo "
        <tr  background='i/bgr-grid-sand.gif'><td align=left nowrap valign=top>
        <b>".$d['user_name']."</b> ".get_dept($d['user_name'])."
        </td><td nowrap valign=top>
        Заступил: <b>$Time1</b><br>
        Сдал: <b>$Time2</b><br>
        Продолжительность: <b>$Utime_min<b>
        </td></tr>
        ";
     }

  $SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE city=6 and (p.post_t>$daystamp AND post_t<$daystamp2) AND p.post_g>0";
  $d=mysql_fetch_array(mysql_query($SQL));
  $TotalTime=round($d['ttime']/60);
  echo "
                  <tr><td  background='i/bgr-grid-sand.gif' colspan=2><b>Всего отдежурено за день: $TotalTime мин.<br>
                ".round((($TotalTime/1440)*100),2)."% из 1440 минут за день
                  </b>
        </td></tr>
  ";
  }

#
# week
#

  if($stats=='week') {
  if(@$W=="") $W=date("W");
  if(strpos($W,":")===false) $_sql="p.id_week='$W' AND p.post_t > '{$old}'";
  else {
        $_tmp=explode(":",$W,2);
    $prevweek = $W-1;
    $_sql="(p.id_week='{$_tmp[0]}' OR p.id_week='{$_tmp[1]}') AND p.post_t > '{$old}'";
  }

  echo "
  <th background='i/bgr-grid-sand.gif'>Выборка:</th>
  <tr><td background='i/bgr-grid-sand.gif' align=center>";
?>

<form method="GET">
<input name="act" type="hidden" value="post_prison">
<input name="stats" type="hidden" value="week">
Номер недели: <input name="W" type="text" size=2 value="<?=htmlspecialchars($W)?>"><br>
<input type="submit" value="Поиск">

<?
  echo "
  </td></tr>
  </table></form>
  <table cellpadding=3 width=50% cellspacing=3>";
  $SQL="SELECT sum(p.post_g-p.post_t) AS time, p.id_user, u.user_name FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user WHERE city=6 and $_sql AND p.post_g>0 GROUP BY p.id_user order by u.user_name";
  $r=mysql_query($SQL);
  echo "
  <th colspan=2  background='i/bgr-grid-sand.gif'>Статистика за неделю</th>
  <tr >
  <td background='i/bgr-grid-sand.gif' nowrap><b>Пользователь:</b></td>
  <td background='i/bgr-grid-sand.gif' nowrap><b>кол-во минут:</b></td>
  </tr>";
     while($d=mysql_fetch_array($r)) {
        $Utime_min=round($d['time']/60);
        echo "
        <tr  background='i/bgr-grid-sand.gif'><td align=left nowrap>
        <a href='?act=post_prison&stats=user&userid=".$d['id_user']."&W=$W' title='Статистика по этому пользователю за неделю'>".$d['user_name']."</a>".get_dept($d['user_name'])."
        </td><td nowrap> $Utime_min
        </td></tr>
        ";
     }
  $SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE city=6 and p.id_week='$W' AND p.post_g>0 AND p.post_t > '$old'";
  $d=mysql_fetch_array(mysql_query($SQL));
  $TotalTime=round($d['ttime']/60);

  echo "
                  <tr><td  background='i/bgr-grid-sand.gif' colspan=2><b>Всего отдежурено за неделю: $TotalTime мин.<br>
                ".round((($TotalTime/10080)*100),2)."% из 10080 минут за неделю
                  </b><br>
        &raquo; <a href='?act=post_prison&stats=week&W=$W&show_accidents=1'>просмотр проишествий за неделю</a>
        </td></tr>
  ";

  if($show_accidents==1) {
          echo "<th background='i/bgr-grid-sand.gif' colspan=2>Проишествия:</th>";
          $SQL="SELECT p.comment, p.id_user, u.user_name, p.post_g, p.post_t FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user WHERE city=6 and p.id_week='$W' AND p.post_t > '$old' AND p.post_g>0 ORDER BY p.post_g ASC";
      $result=mysql_query($SQL);
      while($data[]=mysql_fetch_array($result)) true;
      foreach($data AS $key=>$val){
      if(strlen($val['comment'])>3){
                     echo "<tr>
            <td valign=top>".date("d.m.y H:i",$val['post_g'])."
            </td><td valign=top><b>".$data[$key+1]['user_name']."</b>
             принудительно принял пост у <b>".$val['user_name']."</b>
             по причине: <br><i>".$val['comment']."</i>
            </td>
            </tr>";
      }
      if(@$data[$key+1]['post_t'] && ($data[$key+1]['post_t']-$val['post_g'])>300){
                      echo "<tr>
            <td valign=top>".date("d.m.y H:i",$data[$key+1]['post_t'])."
            </td><td valign=top><b>".$data[$key+1]['user_name']."</b>
              заступил на пост после остустствия на нем кого-либо в течении
              <b>".round(($data[$key+1]['post_t']-$val['post_g'])/60)." мин</b><br>
              до этого на посту был <b>".$val['user_name']."</b>
            </td>
            </tr>";
      }
      } // loop
  }
  }


#
# week
#
  if($stats=='week2') {
  if(@$W=="") {
          $W=date("W");
    $W2=$W-1;
    $W="$W:$W2";
  }
  if(strpos($W,":")===false) $_sql="p.id_week='$W' AND p.post_t > '{$old}'";
  else {
        $_tmp=explode(":",$W,2);
    $_sql="(p.id_week='{$_tmp[0]}' OR p.id_week='{$_tmp[1]}') AND p.post_t > '{$old}'";
  }

  echo "
  <th background='i/bgr-grid-sand.gif'>Выборка:</th>
  <tr><td background='i/bgr-grid-sand.gif' align=center>";
?>

<form method="GET">
<input name="act" type="hidden" value="post_prison">
<input name="stats" type="hidden" value="week2">
Номер недели: <input name="W" type="text" size=5 value="<?=htmlspecialchars($W)?>"><br>
<input type="submit" value="Поиск">

<?
  echo "
  </td></tr>
  </table></form>
  <table cellpadding=3 width=50% cellspacing=3>";
  $SQL="SELECT
                  sum(p.post_g-p.post_t) AS time,
        p.id_user,
        u.user_name
        FROM posts_report p
        LEFT JOIN site_users u ON u.id=p.id_user
        WHERE city=6 and $_sql AND p.post_g>0 GROUP BY p.id_user
        ORDER BY u.user_name
  ";
  $r=mysql_query($SQL);
  while($d=mysql_fetch_array($r)) {
          extract($d,EXTR_PREFIX_ALL,"d");
          $stat[$d_id_user]['name']=$d_user_name;
    $stat[$d_id_user]['time']=$d_time;
  }

  $SQL="SELECT
                  sum(p.post_g-p.post_t) AS time,
        p.id_user,
        u.user_name
        FROM post33_reports p
        LEFT JOIN site_users u ON u.id=p.id_user
        WHERE city=6 and $_sql AND p.post_g>0 GROUP BY p.id_user
  ";
  $r=mysql_query($SQL);
  while($d=mysql_fetch_array($r)) {
          extract($d,EXTR_PREFIX_ALL,"d");
    $stat[$d_id_user]['time']+=$d_time;
         $stat[$d_id_user]['name']=$d_user_name;
  }

  echo "
          <th colspan=2  background='i/bgr-grid-sand.gif'>Статистика за неделю</th>
          <tr >
          <td background='i/bgr-grid-sand.gif' nowrap><b>Пользователь:</b></td>
          <td background='i/bgr-grid-sand.gif' nowrap><b>кол-во минут:</b></td>
          </tr>
  ";
     foreach($stat as $id => $d) {
        $Utime_min=round($d['time']/60);
        echo "
                <tr  background='i/bgr-grid-sand.gif'><td align=left nowrap>
                <a href='?act=post_prison&stats=user&userid=".$id."&W=$W' title='Статистика по этому пользователю за неделю'>".$d['name']."</a>".get_dept($d['name'])."
                </td><td nowrap> $Utime_min
                </td></tr>
        ";
     }
}

//
//
// stats for user
//

  if($stats=='user' && strlen($Uname)>2 && $W>0) {
     $Uname=strtolower($Uname);
//     $r=mysql_query("SELECT id,user_name FROM site_users WHERE lower(user_name)='$Uname'");
     $r=mysql_query("SELECT id,user_name FROM site_users WHERE user_name='$Uname'");
     if(mysql_num_rows($r)>0) {
        $d=mysql_fetch_array($r);
        $userid=$d['id'];
        $UsrStr=$d['user_name'];
     } else echo "<th background='i/bgr-grid-sand.gif'><font color=red>Пользователь не найден</font></th></table><table cellpadding=3 width=50% cellspacing=3>";
  }

  if($stats=='user') {
  echo "
  <th background='i/bgr-grid-sand.gif'>Выборка:</th>
  <tr><td background='i/bgr-grid-sand.gif' align=center>";
?>

<form method="GET">
<input name="act" type="hidden" value="post_prison">
<input name="stats" type="hidden" value="user">
Имя пользователя: <input name="Uname" type="text" size=15 value="<?if(strlen($UsrStr)>1) echo htmlspecialchars($UsrStr); else echo htmlspecialchars($Uname)?>"><br>
Номер недели: <input name="W" type="text" size=2 value="<?=htmlspecialchars($W)?>"><br>
<input type="submit" value="Поиск">

<?
  echo "
  </td></tr>
  </table></form>
  <table cellpadding=3 width=50% cellspacing=3>";
  }


  if($stats=='user' && $userid>0 && $W>0) {
  $SQL="SELECT user_name FROM site_users WHERE id='$userid'";
  $dt=mysql_fetch_array(mysql_query($SQL));
  $UsrStr=$dt['user_name'];
// query
  $SQL="SELECT p.post_t, (p.post_g-p.post_t) AS cont FROM posts_report p WHERE city=6 and p.id_week='$W' AND p.post_t > '$old' AND p.post_g>0 AND p.id_user='$userid'";
  $r=mysql_query($SQL);
  echo "
  <th colspan=2  background='i/bgr-grid-sand.gif'>Статистика по пользователю $UsrStr<br> за неделю #$W</th>
  <tr >
  <td background='i/bgr-grid-sand.gif' nowrap><b>Дата:</b></td>
  <td background='i/bgr-grid-sand.gif' nowrap><b>кол-во минут:</b></td>
  </tr>";
     while($d=mysql_fetch_array($r)) {
        $cont=round($d['cont']/60);

        echo "
        <tr><td align=left nowrap>
        ".date("d.m.y H:i:s",$d['post_t'])."
        </td><td nowrap> $cont мин.
        </td></tr>
        ";
     }

  $SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE city=6 and p.id_week='$W' AND p.post_t > '$old' AND p.post_g>0 AND p.id_user='$userid'";
  $d=mysql_fetch_array(mysql_query($SQL));
  $TotalTime=round($d['ttime']/60);
  echo "<tr><td  background='i/bgr-grid-sand.gif' colspan=2><b>Всего за неделю: $TotalTime мин.</b></td></tr>";
  }

echo "</table>";
?>
</center>
</td></tr>
</table>
<?}?>
<table width='100%' border='0' cellspacing='3' cellpadding='2'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Статистика за текущую неделю:</strong> </p></td>
</tr><tr><td align=center>
<?if(@$uW=="") $uW=date("W");?>
<form method="post">
<input name="act" type="hidden" value="post_prison">
Просмотр статистики за неделю <input name="uW" type="text" size=2 value="<?=$uW?>"> <input type="submit" value="Go">
</form>
<table cellpadding=3 width=50% cellspacing=3>
 <?

  $SQL="SELECT p.post_t,p.post_g, (p.post_g-p.post_t) AS cont FROM posts_report p WHERE city=6 and p.id_week='$uW' AND p.post_t > '$old' AND  p.id_user='".AuthUserId."'";
	//p.post_g>0 AND
  $r=mysql_query($SQL);
  echo "
  <th colspan=2  background='i/bgr-grid-sand.gif'>Статистика по пользователю ".AuthUserName."<br> за неделю #$uW</th>
  <tr >
  <td background='i/bgr-grid-sand.gif' nowrap><b>Дата:</b></td>
  <td background='i/bgr-grid-sand.gif' nowrap><b>кол-во минут:</b></td>
  </tr>";
     while($d=mysql_fetch_array($r)) {
		if($d['post_g']>0){
        	$cont=round($d['cont']/60);
		} else {
			$cont=round((time()-$d['post_t'])/60);
		}
        echo "
        <tr><td align=left nowrap>
        ".date("d.m.y H:i:s",$d['post_t'])."
        </td><td nowrap> $cont мин.
        </td></tr>
        ";
     }

  $SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE city=6 and p.id_week='$uW' AND p.post_t > '$old' AND p.post_g>0 AND p.id_user='".AuthUserId."'";
  $d=mysql_fetch_array(mysql_query($SQL));
  if(($on_post_u_id==AuthUserId)&&($uW==date("W"))){
  $TotalTime=round($d['ttime']/60);
  $TotalTime+=$on_post_time;
  } else {
  $TotalTime=round($d['ttime']/60);
  }
  echo "<tr><td  background='i/bgr-grid-sand.gif' colspan=2><b>Всего за неделю: $TotalTime мин.</b></td></tr>";
 ?>
</table>
</center>
</td></tr>
</table>
<?


/*
echo $query="SELECT * FROM posts_order_entry";
echo "<br>";
	 $result= mysql_query($query);
	 mysql_num_rows($result);
	 $num_results= mysql_num_rows($result);
	 for($i=0;$i<$num_results;$i++){
		 $row=mysql_fetch_array($result);
		print_r($row); echo "<br>";
	 }
	 
*/ 
	 
	 
} else echo $mess['AccessDenied'];

?>