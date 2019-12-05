<?php
	function dept_norm($u) {
		if (list($id_dept, $chief) = mysql_fetch_array(mysql_query('SELECT `dept`, `chief` FROM `sd_cops` WHERE `name`=\''.$u.'\' AND `alias`=0'))) {
			if (list($dept) = mysql_fetch_array(mysql_query('SELECT sname FROM `sd_depts` WHERE `id`=\''.$id_dept.'\''))) {
				$str = $dept.(($chief)?', нач.':'');
				return $str;
			} else
				return $str;
		} else
			return $str;
	}

	function norm($nick,$otdel,$w,$city) {
		$ok=0;
		$nach=strstr($otdel, 'нач');
		$zam=strstr($otdel, 'зам');
		$dop_c=0;
		$dop_f=0;
		$SQL="SELECT * FROM om_stat_min where user='".$nick."' AND id_week='".$w1."'";
		$r=mysql_query($SQL);
		$ex= mysql_num_rows($r);
		if($ex>0){
			while($d=mysql_fetch_array($r)) {
				$dop_c=$d['min_chat'];
				$dop_f=$d['min_forum'];
			}
		}
		$SQL="SELECT * FROM om_stat_sp_users where user='".$nick."' ";
		$r=mysql_query($SQL);
		$ex= mysql_num_rows($r);
		if($ex>0){
			$ok=1;
			while($d=mysql_fetch_array($r)) {
				if($city==1){$norm=$d['msk_norm'];}
				if($city==2){$norm=$d['oa_norm'];}
				if($city==3){$norm=$d['forum_norm'];}
			}
		}
		if(($nach=="нач.")&&($ok==0)){ 
			$ok=1;
			$SQL="SELECT * FROM om_stat_norm where id='9999998' ";
			$r=mysql_query($SQL);
			while($d=mysql_fetch_array($r)) {
				if($city==1){$norm=$d['msk_norm'];}
				if($city==2){$norm=$d['oa_norm'];}
				if($city==3){$norm=$d['forum_norm'];}
			}
		}
		if(($zam=="зам.")&&($ok==0)){ 
			$ok=1;
			$SQL="SELECT * FROM om_stat_norm where id='9999997' ";
			$r=mysql_query($SQL);
			while($d=mysql_fetch_array($r)) {
				if($city==1){$norm=$d['msk_norm'];}
				if($city==2){$norm=$d['oa_norm'];}
				if($city==3){$norm=$d['forum_norm'];}
			}
		}
		if($ok==0){
			$SQL="SELECT * FROM om_stat_norm where `otdel` ='".$otdel."' ";
			$r=mysql_query($SQL);
			$ex= mysql_num_rows($r);
			if($ex>0){
				while($d=mysql_fetch_array($r)) {
					if($city==1){$norm=$d['msk_norm'];}
					if($city==2){$norm=$d['oa_norm'];}
					if($city==3){$norm=$d['forum_norm'];}
				}
			} else {
				$SQL="SELECT * FROM om_stat_norm where id='9999999' ";
				$r=mysql_query($SQL);
				while($d=mysql_fetch_array($r)) {
					if($city==1){$norm=$d['msk_norm'];}
					if($city==2){$norm=$d['oa_norm'];}
					if($city==3){$norm=$d['forum_norm'];}
				}
			}
		}
		$norm=$norm/2;
		$norm=$norm-$dop_c;
		return $norm;
	}


$tmp=time()-600;

mysql_query("DELETE FROM posts_order_entry WHERE post_date_from<'".$tmp."' and confirmed=1;");
mysql_query("DELETE FROM posts_order_entry WHERE post_date_from<'".time()."' and confirmed=0;");
mysql_query("DELETE FROM posts_order WHERE must_take_post<'".time()."' and must_take_post<>0;");



for($i=1;$i<3;$i++){

	$SQL2="SELECT p.id, p.id_user, u.user_name FROM posts_order_entry p LEFT JOIN site_users u ON u.id=p.id_user WHERE city=".$i." and p.post_g=0";
	$r2=mysql_query($SQL2);
	$ex= mysql_num_rows($r2);
	$d2=mysql_fetch_array($r2);
	$on_post_un=$d2['user_name'];
	$on_post_id=$d2['id_user'];
	$on_post_pid=$d2['id'];
	
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
			WHERE p.post=".$i." and p.post_date_from<'".time()."' and p.post_date_from<'".$t."' and p.post_date_from>".$t2." and p.confirmed=1 order BY p.post_date_from ASC LIMIT 1";
	$r2=mysql_query($SQL2);
	$entry= mysql_num_rows($r2);
	$d2=mysql_fetch_array($r2);
	//extract($d2,EXTR_PREFIX_ALL,"d");

	$SQL2="SELECT
        p.id_user,
        u.user_name,
		p.post,
		p.post_time
        FROM posts_order p
        LEFT JOIN site_users u ON u.id=p.id_user
        WHERE p.post=".$i." order BY p.post_time ASC LIMIT 1";
	$r2=mysql_query($SQL2);
	$in_order= mysql_num_rows($r2);
	$d2=mysql_fetch_array($r2);
	$fast_order_id_user=$d2['id_user'];

	if(($entry>0)&&($ex!=0)){
	
	
		$otdel=dept_norm($d['Uname']);
		$norm=norm($d['Uname'],$otdel,date("W"),1);
		
		$SQL2="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE city=1 and p.id_week='".date("W")."' AND p.post_t > '$old' AND p.post_g>0 AND p.id_user='".$on_post_id."'";
		$d2=mysql_fetch_array(mysql_query($SQL2));
		$nedel=round($d2['ttime']/60);
		$nedel+=$lasts;
		
		if($nedel>=$norm){$IsNorm=1;} else {$IsNorm=0;}
		$on_post_user_norm=$IsNorm;
		
		if($IsNorm==0){
		
			$SQL3="SELECT * FROM posts_order WHERE post=".$i." order BY post_time ASC LIMIT 1";
			$r3=mysql_query($SQL3);
			$d3=mysql_fetch_array($r3);
			$time=$d3['post_time']-1;
		
			$SQL="INSERT INTO posts_order (id_user,post,post_time,must_take_post) values ('".$on_post_id."','".$i."','".$time."',0)";
			mysql_query($SQL);
			
			$SQL="UPDATE posts_report set post_g='".time()."' WHERE city=".$i." and id_user='".$on_post_id."' and post_g=0";
    		mysql_query($SQL);
		}
		
	}
	
	if(($entry==0)&&($ex==0)){
	
		$SQL3="SELECT * FROM posts_order WHERE post=".$i." and must_take_post>0 order BY post_time ASC LIMIT 1";
		$d2=mysql_fetch_array(mysql_query($SQL2));
		$ex= mysql_num_rows($r2);
  		$d2=mysql_fetch_array($r2);
		extract($d2,EXTR_PREFIX_ALL,"d");
		if($ex==0){
			$tmp=time()+600;
			mysql_query("UPDATE posts_order set must_take_post='".$tmp."' WHERE post=1 and id_user='".$d_id_user."' and post_g=0");
		}
		
		
	}


}
?>