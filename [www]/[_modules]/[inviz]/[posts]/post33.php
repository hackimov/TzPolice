<h1>Отчетность по посту в Оазисе</h1>
<?php
extract($_REQUEST); 

function get_dept_norm($u) {
        if (list($id_dept, $chief) = mysql_fetch_array(mysql_query("SELECT dept, chief FROM sd_cops WHERE name='$u' AND alias=0"))) {
                if (list($dept) = mysql_fetch_array(mysql_query("SELECT sname FROM sd_depts WHERE id='$id_dept'"))) {
                        $str="".$dept.($chief?", нач.":"")."";
                        return $str;
                } else return $str;
        } else return $str;
}

function get_norm($nick,$otdel,$w,$city) {
	$ok=0;
	$nach=strstr($otdel, "нач");
	$zam=strstr($otdel, "зам");
	$dop_c=0;
	$dop_f=0;
	$SQL="SELECT * FROM om_stat_min where user='".$nick."' AND id_week='".$w1."'";
	$r=mysql_query($SQL);
	$ex= mysql_num_rows($r);
	if($ex>0){
		while($d=mysql_fetch_array($r)) {
			extract($d,EXTR_PREFIX_ALL,"d");
			$dop_c=$d_min_chat;
			$dop_f=$d_min_forum;
		}		
	}
	$SQL="SELECT * FROM om_stat_sp_users where user='".$nick."' ";
	$r=mysql_query($SQL);
	$ex= mysql_num_rows($r);
	if($ex>0){
		$ok=1;
		while($d=mysql_fetch_array($r)) {
			extract($d,EXTR_PREFIX_ALL,"d");
			if($city==1){$norm=$d_msk_norm;}
			if($city==2){$norm=$d_oa_norm;}
			if($city==3){$norm=$d_forum_norm;}
		}
	}
	if(($nach=="нач.")&&($ok==0)){ 
		$ok=1;
		$SQL="SELECT * FROM om_stat_norm where id='9999998' ";
		$r=mysql_query($SQL);
			while($d=mysql_fetch_array($r)) {
				extract($d,EXTR_PREFIX_ALL,"d");
				if($city==1){$norm=$d_msk_norm;}
				if($city==2){$norm=$d_oa_norm;}
				if($city==3){$norm=$d_forum_norm;}
			}		
	}
	if(($zam=="зам.")&&($ok==0)){ 
		$ok=1;
		$SQL="SELECT * FROM om_stat_norm where id='9999997' ";
		$r=mysql_query($SQL);
			while($d=mysql_fetch_array($r)) {
				extract($d,EXTR_PREFIX_ALL,"d");
				
				if($city==1){$norm=$d_msk_norm;}
				if($city==2){$norm=$d_oa_norm;}
				if($city==3){$norm=$d_forum_norm;}
			}		
	}
	if($ok==0){
		$SQL="SELECT * FROM om_stat_norm where `otdel` ='".$otdel."' ";
		$r=mysql_query($SQL);
		$ex= mysql_num_rows($r);
		if($ex>0){
			while($d=mysql_fetch_array($r)) {
				extract($d,EXTR_PREFIX_ALL,"d");
				
				if($city==1){$norm=$d_msk_norm;}
				if($city==2){$norm=$d_oa_norm;}
				if($city==3){$norm=$d_forum_norm;}
			
			}
		} else {
			$SQL="SELECT * FROM om_stat_norm where id='9999999' ";
			$r=mysql_query($SQL);
			while($d=mysql_fetch_array($r)) {
				extract($d,EXTR_PREFIX_ALL,"d");
				if($city==1){$norm=$d_msk_norm;}
				if($city==2){$norm=$d_oa_norm;}
				if($city==3){$norm=$d_forum_norm;}
			}
		}
	}
	$norm=$norm/2;
	$norm=$norm-$dop_c;
	return $norm;
}

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
    "ветеран"=>15
  );
  $depts = array_flip($depts);
function get_dept($u) {
       GLOBAL $depts;
        $rr=mysql_query("SELECT * FROM cops_depts WHERE name='$u'");
    if (@mysql_num_rows($rr)>0) {
            $dt=mysql_fetch_array($rr);
        $chief = ($dt['ischief']==1) ? " ,нач.":"";
              $str=" (".$depts[$dt['id_dept']].$chief.")";
        return $str;
    } else return $str;
}
?>
<table width='100%' border='0' cellspacing='3' cellpadding='2'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Рапорта</strong> </p></td>
</tr><tr><td align=center>
<?
$SQL="SELECT p.*, u.id AS UID, u.user_name AS Uname FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user where city=2 ORDER BY id DESC LIMIT 1";
$r=mysql_query($SQL);
$d=mysql_fetch_array($r);
$on_post_u_id=$d['id_user'];
if(@$d['post_g']>0) {
    if($_REQUEST['a']=="TakePost") {
        $SQL="INSERT INTO posts_report (id_user,post_t,id_week,city) values ('".AuthUserId."','".time()."','".date("W")."','2')";
        mysql_query($SQL);
        echo "<script>top.location.href='?act=post33';</script>";
    }
?>
Сейчас на посту никого с <b><?=date("d.m.y H:i:s",$d['post_g'])?></b>
<form method="GET">
<input name="act" type="hidden" value="post33">
<input name="a" type="hidden" value="TakePost">
<input type="submit" value="принять пост">
</form>
<?
} else {
    $last_id=$d['id'];
        $lasts=round((time()-$d['post_t'])/60);
	$IsNorm = 0;
	
	/*
	$SQL="SELECT SUM(post_g-post_t) as oasis FROM posts_report WHERE city=2 and id_week='".date("W")."' AND id_user='".$d['UID']."' AND post_g>$old";
	if ($dpost = mysql_fetch_array(mysql_query($SQL))) {
		$post_o = $lasts + $dpost['oasis']/60;
		if ($post_o>=480)	$IsNorm = 1;
		else {
			$SQL="SELECT SUM(post_g-post_t) as moscow FROM posts_report WHERE city=1 and id_week='".date("W")."' AND id_user='".$d['UID']."' AND post_g>$old";
			if ($dpost = mysql_fetch_array(mysql_query($SQL))) {
				$post_m = ($dpost['moscow']/60>180?180:$dpost['moscow']/60);
				if ($post_o+$post_m>=480) $IsNorm = 1;
			}
		}
	}
	*/
	
	$otdel=get_dept_norm($d['Uname']);
	$norm=get_norm($d['Uname'],$otdel,date("W"),2);
	
	$SQL2="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE city=2 and p.id_week='".date("W")."' AND p.post_t > '$old' AND p.post_g>0 AND p.id_user='".$d['UID']."'";
    $d2=mysql_fetch_array(mysql_query($SQL2));
  	$nedel=round($d2['ttime']/60);
  	$nedel+=$lasts;
  	
	if($nedel>=$norm){$IsNorm=1;} else {$IsNorm=0;}
	
    echo "Сейчас на посту: <b>{$d['Uname']}</b> ".($IsNorm?"<font color=green>(норма выполнена)</font>":"<font color=red>(норма не выполнена)</font>")."<br>С <b>".date("H:i", $d['post_t'])."</b> ($lasts мин)";
	$on_post_time=$lasts;
    if($d['UID']==AuthUserId) {
        if($_REQUEST['a']=="GivePost") {
           $SQL="UPDATE posts_report set post_g='".time()."' WHERE id_user='".AuthUserId."' and city=2 and post_g=0";
           mysql_query($SQL);
           echo "<script>top.location.href='?act=post33';</script>";
        }
?>
<form method="GET">
<input name="act" type="hidden" value="post33">
<input name="a" type="hidden" value="GivePost">
<input type="submit" value="сдать пост">
</form>
<?
    } else {
        if($_REQUEST['a']=="GetPost") {
           $SQL="UPDATE posts_report set post_g='".time()."', comment='".htmlspecialchars($_REQUEST['reason'])."' WHERE id='$last_id' and city=2 and post_g=0";
           mysql_query($SQL);
           $SQL="INSERT INTO posts_report (id_user,post_t,id_week,city) values ('".AuthUserId."','".time()."','".date("W")."','2')";
           mysql_query($SQL);
           echo "<script>top.location.href='?act=post33';</script>";
        }
?>
<form method="GET" id="reasoncheck">
<div>Причина: <input name="reason" type="text" size=100 value=""></div><br>
<input name="act" type="hidden" value="post33">
<input name="a" type="hidden" value="GetPost">
<input type="submit" value="принудительно принять пост">
</form>
<?
    }
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
	  <a href='?act=post33&stats=week_new'> Новая сводная статистика за текущую неделю (после 4.02.2007)</a>
      (сейчас идет <b>".date("W")."</b> неделя)  <BR>
	  <a href='?act=post33&stats=day_new'>Новая сводная статистика за день (после 4.02.2007)</a>
      (сегодня <b>".date("d.m.Y")."</b>)<BR>
	  
      <a href='?act=post33&stats=week'>Старая сводная статистика за текущую неделю (до 4.02.2007)</a>
      (сейчас идет <b>".date("W")."</b> неделя)  <BR>
	 
     <a href='?act=post33&stats=day'>Старая сводная статистика за день (до 4.02.2007)</a>
      (сегодня <b>".date("d.m.Y")."</b>
	  )
	 
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
<input name="act" type="hidden" value="post33">
<input name="stats" type="hidden" value="day">
Дата: <input name="day" type="text" size=8 value="<?=date("d.m.Y",$daystamp)?>"> <input type="submit" value="Поиск">
<?
  echo "
  </td></tr>
  </table></form>
  <table cellpadding=3 width=50% cellspacing=3>";
  $SQL="SELECT (p.post_g-p.post_t) AS time, p.post_g, p.post_t, p.id_user, u.user_name FROM post33_reports p LEFT JOIN site_users u ON u.id=p.id_user WHERE (p.post_t>$daystamp AND post_t<$daystamp2) AND p.post_g>0 AND p.post_g>$old ORDER BY p.id";
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
        Продолжительность: <b>$Utime_min<b> мин.
        </td></tr>
        ";
     }
  $SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM post33_reports p WHERE (p.post_t>$daystamp AND post_t<$daystamp2) AND p.post_g>0 AND p.post_g>$old";
  $d=mysql_fetch_array(mysql_query($SQL));
  $TotalTime=round($d['ttime']/60);
 echo "
                 <tr><td  background='i/bgr-grid-sand.gif' colspan=2><b>Всего отдежурено за день: $TotalTime мин.<br>
                ".round((($TotalTime/1440)*100),2)."% из 1440 минут за день
                  </b>
        </td></tr>
  ";
  }
  if($stats=="day_new"){
  
  
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
<input name="act" type="hidden" value="post33">
<input name="stats" type="hidden" value="day_new">
Дата: <input name="day" type="text" size=8 value="<?=date("d.m.Y",$daystamp)?>"> <input type="submit" value="Поиск">
<?
  echo "
  </td></tr>
  </table></form>
  <table cellpadding=3 width=50% cellspacing=3>";
  $SQL="SELECT (p.post_g-p.post_t) AS time, p.post_g, p.post_t, p.id_user, u.user_name FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user WHERE city=2 AND (p.post_t>$daystamp AND post_t<$daystamp2) AND p.post_g>0 AND p.post_g>$old ORDER BY p.id";
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
        Продолжительность: <b>$Utime_min<b> мин.
        </td></tr>
        ";
     }
  $SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE city=2 AND (p.post_t>$daystamp AND post_t<$daystamp2) AND p.post_g>0 AND p.post_g>$old";
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
  if(@$W=="") {
          $W=date("W");
    $W2=$W-1;
    $W="$W:$W2";
  }
  if(strpos($W,":")===false) $_sql="p.id_week='$W'";
  else {
        $_tmp=explode(":",$W,2);
    $_sql="(p.id_week='{$_tmp[0]}' OR p.id_week='{$_tmp[1]}')";
  }
  echo "
  <th background='i/bgr-grid-sand.gif'>Выборка:</th>
  <tr><td background='i/bgr-grid-sand.gif' align=center>";
?>
<form method="GET">
<input name="act" type="hidden" value="post33">
<input name="stats" type="hidden" value="week">
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
            FROM post33_reports p
            LEFT JOIN site_users u ON u.id=p.id_user
            WHERE $_sql AND p.post_g>0 AND p.post_g>$old GROUP BY p.id_user
           ORDER BY u.user_name
  ";
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
        <a href='?act=post33&stats=user&userid=".$d['id_user']."&W=$W' title='Статистика по этому пользователю за неделю'>".$d['user_name']."</a>".get_dept($d['user_name'])."
        </td><td nowrap> $Utime_min
        </td></tr>
        ";
     }
  $SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM post33_reports p WHERE p.id_week='$W' AND p.post_g>0 AND p.post_g>$old";
  $d=mysql_fetch_array(mysql_query($SQL));
  $TotalTime=round($d['ttime']/60);
  echo "
                  <tr><td  background='i/bgr-grid-sand.gif' colspan=2><b>Всего отдежурено за неделю: $TotalTime мин.<br>
                ".round((($TotalTime/10080)*100),2)."% из 10080 минут за неделю
                  </b><br>
        &raquo; <a href='?act=post33&stats=week&W=$W&show_accidents=1'>просмотр проишествий за неделю</a>
        </td></tr>
  ";
  if($show_accidents==1) {
          echo "<th background='i/bgr-grid-sand.gif' colspan=2>Проишествия:</th>";
          $SQL="SELECT p.comment, p.id_user, u.user_name, p.post_g, p.post_t FROM post33_reports p LEFT JOIN site_users u ON u.id=p.id_user WHERE p.id_week='$W' AND p.post_g>0 AND p.post_g>$old ORDER BY p.post_g ASC";
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
  
  if($stats=="week_new"){
  
  if(@$W=="") {
          $W=date("W");
    $W2=$W-1;
    $W="$W:$W2";
  }
  if(strpos($W,":")===false) $_sql="p.id_week='$W'";
  else {
        $_tmp=explode(":",$W,2);
    $_sql="(p.id_week='{$_tmp[0]}' OR p.id_week='{$_tmp[1]}')";
  }
  echo "
  <th background='i/bgr-grid-sand.gif'>Выборка:</th>
  <tr><td background='i/bgr-grid-sand.gif' align=center>";
?>
<form method="GET">
<input name="act" type="hidden" value="post33">
<input name="stats" type="hidden" value="week_new">
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
            WHERE $_sql AND city=2 AND p.post_g>0 AND p.post_g>$old GROUP BY p.id_user
           ORDER BY u.user_name
  ";
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
        <a href='?act=post33&stats=user&userid=".$d['id_user']."&W=$W' title='Статистика по этому пользователю за неделю'>".$d['user_name']."</a>".get_dept($d['user_name'])."
        </td><td nowrap> $Utime_min
        </td></tr>
        ";
     }
  $SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE city=2 AND p.id_week='$W' AND p.post_g>0 AND p.post_g>$old";
  $d=mysql_fetch_array(mysql_query($SQL));
  $TotalTime=round($d['ttime']/60);
  echo "
                  <tr><td  background='i/bgr-grid-sand.gif' colspan=2><b>Всего отдежурено за неделю: $TotalTime мин.<br>
                ".round((($TotalTime/10080)*100),2)."% из 10080 минут за неделю
                  </b><br>
        &raquo; <a href='?act=post33&stats=week&W=$W&show_accidents=1'>просмотр проишествий за неделю</a>
        </td></tr>
  ";
  if($show_accidents==1) {
          echo "<th background='i/bgr-grid-sand.gif' colspan=2>Проишествия:</th>";
          $SQL="SELECT p.comment, p.id_user, u.user_name, p.post_g, p.post_t FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user WHERE city=2 AND p.id_week='$W' AND p.post_g>0 AND p.post_g>$old ORDER BY p.post_g ASC";
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
// stats for user
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
<input name="act" type="hidden" value="post33">
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
  $SQL="SELECT p.post_t, (p.post_g-p.post_t) AS cont FROM post33_reports p WHERE p.id_week='$W' AND p.post_g>0 AND p.post_g>$old AND p.id_user='$userid'";
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
  $SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM post33_reports p WHERE p.id_week='$W' AND p.post_g>0 AND p.post_g>$old AND p.id_user='$userid'";
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
<input name="act" type="hidden" value="post33">
Просмотр статистики за неделю <input name="uW" type="text" size=2 value="<?=$uW?>"> <input type="submit" value="Go">
</form>
<table cellpadding=3 width=50% cellspacing=3>
 <?
  $SQL="SELECT p.post_t,p.post_g, (p.post_g-p.post_t) AS cont FROM posts_report p WHERE city=2 AND p.id_week='$uW' AND p.post_g>$old AND p.id_user='".AuthUserId."'";
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
  $SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE city=2 and p.id_week='$uW' AND p.post_g>0 AND p.post_g>$old AND p.id_user='".AuthUserId."'";
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
} else echo $mess['AccessDenied'];
?>