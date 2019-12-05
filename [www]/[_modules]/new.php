<?
///sql
Error_Reporting(E_ALL & ~E_NOTICE);
include "/home/sites/police/www/_modules/sql_layer.php";
$is_db_remote=1;

if ($is_db_remote==1) {
        $dbhost="localhost";
	$dbname="tzpolice";
        $dbuser="tzpolice";
        $dbpass="dKs8hfd123bn";
} else {
        $dbhost="localhost";
        $dbname="police";
        $dbuser="root";
        $dbpass="";
}
$dbi = sql_connect($dbhost, $dbuser, $dbpass, $dbname);
function ExecSQLOne($s)
{
 global $dbi;
 $result=@sql_query($s,$dbi);
 $row=@sql_fetch_row($result,$dbi);
 @sql_free_result($result);
 return $row[0];
}
///new_post
$city_arr=array(
		"0"=>"Oasis",
		"1"=>"New M."
		);

extract($_REQUEST); 

// проверка ID и новых пользователей.
$res=sql_query("Select * from sd_cops where user_id=0",$dbi) or die(Mysql_error());
While($row=sql_fetch_object($res)){
	$us_id=ExecSQLOne("Select id from site_users where user_name='$row->name'");
	if($us_id!=""){
		sql_query("Update sd_cops SET
				user_id='$us_id' where id=$row->id",$dbi) or die(Mysql_error());
	}
}

//
echo "<center>[<a href=new.php>Статистика</a> | <a href=new.php?op=edite_norm>Норма модерации</a>]</center>";#######  
$op=$_REQUEST["op"];
Switch($op){
	case("edite_norm"):
		echo "<table border=1>
				<tr>
					<td rowspan=2>Отдел</td>
					<td colspan=2>Оазис City</td>
					<td colspan=2>New Moscow</td>
					<td rowspan=2>Действия</td>
				</tr>
				<tr>
					<td>Норма</td><td>Зам. нач.</td><td>Норма</td><td>Зам нач</td>
				</tr>";
				
		$res=sql_query("Select * from sd_depts order by ID ASC",$dbi) or die(Mysql_error());
		While($row=sql_fetch_object($res)){
			
			echo "<form action='new.php?op=save_edite' method=post>";################
				echo "<td>$row->name</td>
					  <input type='hidden' value='$row->id' name='id'>
					  <td><input value='".(($row->norm_oa=="")?"0":"$row->norm_oa")."' type='text' name='norm_oa' size='5' maxlength='5'></td>
					  <td><input value='".(($row->nach_norm_oa=="")?"0":"$row->nach_norm_oa")."' type='text' name='nach_norm_oa' size='5' maxlength='5'></td>
					  <td><input value='".(($row->norm=="")?"0":"$row->norm")."' type='text' name='norm' size='5' maxlength='5'></td>
					  <td><input value='".(($row->nach_norm=="")?"0":"$row->nach_norm")."' type='text' name='nach_norm' size='5' maxlength='5'></td>
					  <td><input type='submit' value='save'></td>";
				echo "</form>";		
			echo "</tr>";

		}
		sql_free_result($res);
		echo "</table>";
	break;
	case("save_edite"):
		sql_query("UPDATE sd_depts SET 
						norm = '".$_REQUEST["norm"]."',
						norm_oa = '".$_REQUEST["norm_oa"]."',
						nach_norm_oa = '".$_REQUEST["nach_norm_oa"]."',
						nach_norm = '".$_REQUEST["nach_norm"]."' 
						WHERE id='".$_REQUEST["id"]."' LIMIT 1",$dbi);
		echo "<script>top.location.href='new.php?op=edite_norm';</script>";####
	break;	
	default:
		echo "Статистика:<br>";
		$week=ExecSQLOne("SELECT MAX(id_week) FROM posts_report");
		if($week%2==0){
			$starweek=$week-1;
		}else{
			$starweek=$week;
		}
		if($_REQUEST['week']==""){
			$week1=$starweek;
			$week2=$week1-1;
		}else{
			if($week%2==0){
				$week1=$week-1;
			}
			$week1=$_REQUEST['week'];
			$week2=$week1-1;
		}
		echo "Текущая неделя: $week".(($week==$starweek)? "" : " <font color=#ff0000>Статистика за эту неделю недостпна</font>")."<br>";
		echo "Период:";
		echo "$week2-$week1 неделя";
		echo "<br> просмотреть статистику за: ";
		for($i=$starweek;$i>=1;$i=$i-2){
			if($i==$week1){
				echo "<b>$i-".($i-1)."</b>..";
			}else{
				echo "<a href=new.php?week=$i>$i-".($i-1)."</a>..";#############
			}	
		}
		echo "недели";
		echo "<table border=1>";
		$res=sql_query("Select * from sd_depts order by ID ASC",$dbi) or die(Mysql_error());
		While($row=sql_fetch_object($res)){
			if(ExecSQLOne("SELECT count(*) FROM sd_cops where dept=$row->id")>0){
				echo "<tr><td colspan=7 align=center><b>$row->name</b></td></tr>
				 <tr><td>Ник</td><td>Ур.сотр.</td><td>".$city_arr["0"]."</td><td>".$city_arr["1"]."</td><td>Сумма</td><td>премия</td><td>Зар.плата</td>  </tr>";
				$resP=sql_query("SELECT a.user_id as id,a.name as name,a.chief as ischief,b.sum_o as sum_oa, c.sum_n as sum_nm FROM sd_cops a left join (select id_user, SUM((post_g-post_t)/60) as sum_o from post33_reports where id_week='$week1' or id_week='$week2' group by id_user) b on a.user_id=b.id_user left join (select id_user, SUM((post_g-post_t)/60) as sum_n from posts_report where id_week='$week1' or id_week='$week2' group by id_user) c on a.user_id = c.id_user where a.dept='$row->id'",$dbi)or die(MySql_error());
				while($rowP=sql_fetch_array($resP,$dbi)) {
					$norma_oa=($rowP['ischief']>0) ? (($rowP['ischief']==1)? "0": $row->nach_norm_oa) : $row->norm_oa;
					$norma_nm=($rowP['ischief']>0) ? (($rowP['ischief']==1)? "0": $row->nach_norm) : $row->norm;
					
					
					echo "<tr>";
					echo "<td>{$rowP['name']}</td>
						  <td>".(($rowP['ischief']>0) ? (($rowP['ischief']==1)? "Нач.": "Зам.нач") : "сотруд.")."</td>
						  <td>".(($norma_oa <= round($rowP['sum_oa'])) ?  "<font color=black>".round($rowP['sum_oa'])."</font>" : "<font color=red>".round($rowP['sum_oa'])."</font>" )."</td>
						  <td>".(($norma_nm <= round($rowP['sum_nm'])) ?  "<font color=black>".round($rowP['sum_nm'])."</font>" : "<font color=red>".round($rowP['sum_nm'])."</font>" )."</td>						  <td>".(round($row['sum_oa']) + Round($row['sum_nm']))."</td>
						  <td>".(((round($rowP['sum_oa'])+round($rowP['sum_nm'])) < ($norma_nm+$norma_oa+50))?"0" : "<b>".(Round($rowP['sum_oa']) - $norma_oa )*2 + Round($rowP['sum_nm']) - $norma_nm ."<b>" )."</td>
						  <td>".((round($rowP['sum_oa'])>=$norma_oa && (round($rowP['sum_oa'])+round($rowP['sum_nm']))>=($norma_oa+$norma_nm))? "100%" :  (round($rowP['sum_nm'])+round($rowP['sum_oa']))/($norma_oa+$norma_nm)     )."</td>
						  ";
					echo "</tr>";
				
				
							
				}
				@sql_free_result($resP);
			}
				
		
		}




}

?>