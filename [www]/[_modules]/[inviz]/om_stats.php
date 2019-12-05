<h1>Отчетность по постам модерации</h1>
<center>

<?php
$postname[1] = "New Moscow";
$postname[2] = "Oasis";
$postname[3] = "Forum";
$postname[4] = "Vault city";
$postname[5] = "NM Auction";
$postname[6] = "Prison";
//echo ("1");

$old = time() - 25920000; //(300 days)
$query = "DELETE FROM `posts_report` WHERE `post_t` < '".$old."'";
mysql_query($query);
extract($_REQUEST);
//error_reporting(E_ALL);
//echo ("1");
error_reporting(0);
if(AuthStatus==1 && AuthUserName!='' && (AuthUserClan=='police' || AuthUserClan=='Police Academy' || AuthUserGroup=='100' || AuthUserClan=='PoliceAcademy')) {
//echo ("1");

	$depts = array(
		'мод.'=>1,
		'юрид.'=>2,
		'пресс'=>3,
		'IT'=>4,
		'эконом.'=>5,
		'лиц.'=>6,
		'боевой м'=>7,
		'боевой н'=>8,
		'боевой о'=>9,
		'прокачки'=>10,
		'рассл.'=>11,
		'пол.'=>12,
		'кадры'=>14,
		'ветеран'=>15,
		'обэп'=>20,
		'снабж.'=>23
	);
	$depts = array_flip($depts);

	function get_dept($u) {
		if (list($id_dept, $chief, $deputy) = mysql_fetch_array(mysql_query('SELECT dept, chief, deputy FROM sd_cops WHERE name=\''.$u.'\' AND alias=0'))) {
			if (list($dept) = mysql_fetch_array(mysql_query('SELECT sname FROM sd_depts WHERE id=\''.$id_dept.'\''))) {
				$str = $dept.($chief?', нач.':($deputy?', зам.':''));
				return $str;
			} else
				return $str;

		} else
			return $str;
	}

	function check_norm($nick, $otdel, $msk, $oa, $vault, $forum, $prison, $w1, $w2) {
		$ok=0;
		$bad_norm=0;
		$nach=strstr($otdel, 'нач');
		$zam=strstr($otdel, 'зам');
                $otdel = str_replace(', нач.', '', $otdel);
                $otdel = str_replace(', зам.', '', $otdel);

		$SQL="SELECT * FROM om_stat_min WHERE user='".$nick."' AND year='".date("Y",time())."' and (id_week='".$w1."' or id_week='".$w2."')";
		$r = mysql_query($SQL);
		$ex = mysql_num_rows($r);
		$dop_msk=0;
		$dop_oa=0;
		$dop_vault=0;
		$dop_forum=0;
		$dop_prison=0;
		if($ex>0){
			while($d=mysql_fetch_array($r)) {
				extract($d,EXTR_PREFIX_ALL,'d');
				if($d_city==1){$norm["dop_msk"]+=$d_minutes;}
				if($d_city==2){$norm["dop_oa"]+=$d_minutes;}
				if($d_city==3){$norm["dop_forum"]+=$d_minutes;}
				if($d_city==4){$norm["dop_vault"]+=$d_minutes;}
				if($d_city==6){$norm["dop_prison"]+=$d_minutes;}
			}
		}


		$SQL='SELECT * FROM om_stat_sp_users WHERE user=\''.$nick.'\' ';
		$r=mysql_query($SQL);
		$ex= mysql_num_rows($r);
		if($ex>0){
			$ok=1;
			while($d=mysql_fetch_array($r)) {
				extract($d,EXTR_PREFIX_ALL,"d");
				/*if($oa>=$d_oa_norm){$tmp=$oa+$msk;}
				if(($oa<$d_oa_norm)&&($msk<$d_msk_norm)) {$tmp=$oa+$msk;}
				if(($oa<$d_oa_norm)&&($msk>=$d_msk_norm)){$tmp=$oa+$d_msk_norm;}

				$chat=($tmp)/($d_oa_norm+$d_msk_norm);
				$for=$forum/$d_forum_norm;*/

				/*if(($chat>=1)&&($for>=1)) {$norm[3]=round((($chat+$for)*50),2);}
				if(($chat>=1)&&($for<1)) {$norm[3]=round(((1+$for)*50),2);}
				if(($chat<1)&&($for>=1)) {$norm[3]=round((($chat+1)*50),2);}
				if(($chat<1)&&($for<1)) {$norm[3]=round((($chat+$for)*50),2);}*/
			}
		}


		/*if(($nach=="нач.")&&($ok==0)){
			$ok=1;
			$SQL="SELECT * FROM om_stat_norm WHERE id='9999998' ";
			$r=mysql_query($SQL);
				while($d=mysql_fetch_array($r)) {
					extract($d,EXTR_PREFIX_ALL,"d");
				}
		}

		if(($zam=="зам.")&&($ok==0)){
			$ok=1;
			$SQL="SELECT * FROM om_stat_norm WHERE id='9999997' ";
			$r=mysql_query($SQL);
			while($d=mysql_fetch_array($r)) {
				extract($d,EXTR_PREFIX_ALL,"d");
			}
		}*/

		if($ok==0){
			$SQL="SELECT * FROM om_stat_norm WHERE `otdel` ='".$otdel."' ";
			$r=mysql_query($SQL);
			$ex= mysql_num_rows($r);
			if($ex>0){
				while($d=mysql_fetch_array($r)) {
					extract($d,EXTR_PREFIX_ALL,"d");
				}
			} else {
				$SQL="SELECT * FROM om_stat_norm WHERE id='9999999' ";
				$r=mysql_query($SQL);
				while($d=mysql_fetch_array($r)) {
					extract($d,EXTR_PREFIX_ALL,"d");
				}
			}
		}

		$tmp=0;

                if (($nach=="нач.") || ($zam=="зам.")){
                  $d_msk_norm = $d_msk_norm/2;
                  $d_oa_norm = $d_oa_norm/2;
                  $d_forum_norm = $d_forum_norm/2;
                  $d_vault_norm = $d_vault_norm/2;
                  $d_prison_norm = $d_prison_norm/2;
                }

		$msk+=$norm["dop_msk"];
		$oa+=$norm["dop_oa"];
		$forum+=$norm["dop_forum"];
		$vault+=$norm["dop_vault"];
		$prison+=$norm["dop_prison"];

		$norm['summ']=$msk+$oa+$forum+$vault+$prison;

		if(($msk<$d_msk_norm)||($oa<$d_oa_norm)||($forum<$d_forum_norm)||($vault<$d_vault_norm)||($prison<$d_prison_norm)){
			if($msk>$d_msk_norm){$msk=$d_msk_norm;}
			if($oa>$d_oa_norm){$oa=$d_oa_norm;}
			if($forum>$d_forum_norm){$forum=$d_forum_norm;}
			if($vault>$d_vault_norm){$vault=$d_vault_norm;}
		}

		if($msk>=$d_msk_norm) {$norm["msk"]=1;} else {$norm["msk"]=0;}
		if($oa>=$d_oa_norm) {$norm["oa"]=1;} else {$norm["oa"]=0;}
		if($forum>=$d_forum_norm) {$norm["forum"]=1;} else {$norm["forum"]=0;}
		if($vault>=$d_vault_norm) {$norm["vc"]=1;} else {$norm["vc"]=0;}
                if($prison>=$d_prison_norm) {$norm["prison"]=1;} else {$norm["prison"]=0;}

		$norm["norma"]=round((($msk+$oa+$forum+$vault+$prison)/($d_msk_norm+$d_oa_norm+$d_forum_norm+$d_vault_norm+$d_prison_norm))*100,2);

		if( ($d_msk_norm==0) && ($d_oa_norm==0) && ($d_forum_norm==0) && ($d_vault_norm==0) && ($d_prison_norm==0) ){
			$norm["norma"]=100;
		}

		/*
		if($msk>=$d_msk_norm) {
			$tmp=$tmp+($msk-$d_msk_norm);
			$t_msk=$d_msk_norm;
		}
		if($oa>=$d_oa_norm) {
			$tmp=$tmp+($oa-$d_oa_norm);
			$t_oa=$d_oa_norm;
		}
		if($forum>=$d_forum_norm) {
			$tmp=$tmp+($forum-$d_forum_norm);
			$t_forum=$d_forum_norm;
		}
		if($vault>=$d_vault_norm) {
			$tmp=$tmp+($vault-$d_vault_norm);
			$t_vault=$d_vault_norm;
		}
		$tmp=ceil($tmp/2);

		if( ($msk<$d_msk_norm) || ($oa<$d_oa_norm) || ($forum<$d_forum_norm) || ($vault<$d_vault_norm) ){
			if($msk<$d_msk_norm){
				if(($msk+$tmp>$d_msk_norm) && ($tmp>0)){
					$tmp=$tmp-($d_msk_norm-$msk);
					$t_msk=$d_msk_norm;
				}
				if($msk+$tmp<=$d_msk_norm){
					$t_msk=$msk+$tmp;
					$tmp=0;
				}
			}

			if($oa<$d_oa_norm){
				if(($oa+$tmp>$d_oa_norm) && ($tmp>0)){
					$tmp=$tmp-($d_oa_norm-$oa);
					$t_oa=$d_oa_norm;
				}
				if($oa+$tmp<=$d_oa_norm){
					$t_oa=$oa+$tmp;
					$tmp=0;
				}
			}

			if($forum<$d_forum_norm){
				if(($forum+$tmp>$d_forum_norm) && ($tmp>0)){
					$tmp=$tmp-($d_forum_norm-$forum);
					$t_forum=$d_forum_norm;
				}
				if($forum+$tmp<=$d_forum_norm){
					$t_forum=$forum+$tmp;
					$tmp=0;
				}
			}

			if($vault<$d_vault_norm){
				if(($vault+$tmp>$d_vault_norm) && ($tmp>0)){
					$tmp=$tmp-($d_vault_norm-$vault);
					$t_vault=$d_vault_norm;
				}
				if($vault+$tmp<=$d_vault_norm){
					$t_vault=$vault+$tmp;
					$tmp=0;
				}
			}
		} else {
			$t_msk=$msk;
			$t_oa=$oa;
			$t_forum=$forum;
			$t_vault=$vault;
		}

		if($msk>=$d_msk_norm) {$norm["msk"]=1;} else {$norm["msk"]=0;}
		if($oa>=$d_oa_norm) {$norm["oa"]=1;} else {$norm["oa"]=0;}
		if($forum>=$d_forum_norm) {$norm["forum"]=1;} else {$norm["forum"]=0;}
		if($vault>=$d_vault_norm) {$norm["vc"]=1;} else {$norm["vc"]=0;}

		if($msk<$d_msk_norm) { $bad_norm=1; }
		if($oa<$d_oa_norm) { $bad_norm=1; }
		if($forum<$d_forum_norm) { $bad_norm=1; }
		if($vault<$d_vault_norm) { $bad_norm=1; }

		if($bad_norm==1){
			if($msk>$d_msk_norm) { $msk=$d_msk_norm; }
			if($oa>$d_oa_norm) { $oa=$d_oa_norm; }
			if($forum>$d_forum_norm) { $forum=$d_forum_norm; }
			if($vault>$d_vault_norm) { $vault=$d_vault_norm; }
		}

		if(!$dop_c){ $norm["dop"]=0; }
		else{ $norm["dop"] = $dop_c; }

		$norm["norma"] = round( ( ($t_msk + $t_oa + $t_forum + $t_vault + $norm["dop"]) / ($d_msk_norm + $d_oa_norm + $d_forum_norm + $d_vault_norm) )*100, 2);

		if( ($d_msk_norm==0) && ($d_oa_norm==0) && ($d_forum_norm==0) && ($d_vault_norm==0) ){
			$norm["norma"]=100;
		}
		*/
		if($norm["norma"]>100) {

			$pr = ($oa + $msk + $forum + $vault + $prison + $norm["dop"] ) - ( $d_msk_norm + $d_oa_norm + $d_forum_norm + $d_vault_norm+ $d_prison_norm);
			//echo ("<BR>MSK = ".$msk.", FORUM = ".$forum.", normMSK = ".$d_msk_norm.", normFORUM = ".$d_forum_norm."<BR>");
		/*	if( ($pr>=100) && ($pr<300) ){
				$pr = $pr*2;
			} else {
				if($pr>=300) { $pr=$pr*3; }
			}
			if($pr>=50) { $norm["bonus"]=$pr; } else { $norm["bonus"]=0; }
		*/

		/*	if($pr>=50){
				if($pr<100){
					$norm["bonus"] = $pr;

				}elseif($pr>=100 && $pr<300){
					$norm["bonus"] = 100 + ($pr-100)*2;

				}elseif($pr>=300){
					$norm["bonus"] = 100 + 400 + ($pr-300)*3;

				}
			} else {
				$norm["bonus"]=0;
			}   */

			$norm["bonus"] = $pr;

		} else {
			$norm["bonus"]=0;
		}

		return $norm;
	}

/**************************************************************************************/

//	$q=mysql_query("SELECT name FROM cops_depts WHERE name='".AuthUserName."' AND ischief=1");

//	if(mysql_num_rows($q)>0 || $AdminUsers[AuthUserName]==1) {
	if(abs(AccessLevel) & AccessPoliceStats) {
	//AccessPoliceStats
	///////////////////////////
	/// РЕДАКТИРОВАНИЕ НОРМ ///
	///////////////////////////
	if($do=="edit_norms"){
		echo "<a href='?act=om_stats&do=stat'>Сводная статистика</a>";

		if((!$target)||($target=="list")){
		/// Вывод отделов
			echo "<table cellpadding=3 width=75% cellspacing=3>\n";
			echo "<th colspan=7  background='i/bgr-grid-sand.gif'>Нормы отделов:</th>\n";
			echo " <tr>\n";
			echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Отдел:</b></td>\n";
			echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Норма МСК:</b></td>\n";
			echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Норма ОА:</b></td>\n";
			echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Норма VC:</b></td>\n";
			echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Норма форум:</b></td>\n";
			echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Норма каторга:</b></td>\n";
			echo "  <td background='i/bgr-grid-sand.gif' nowrap></td>\n";
			echo "  <td background='i/bgr-grid-sand.gif' nowrap></td>\n";
			echo " </tr>";

			$SQL="SELECT * FROM om_stat_norm ORDER BY id ASC";
			$r=mysql_query($SQL);
			while($d=mysql_fetch_assoc($r)) {
			//	extract($d,EXTR_PREFIX_ALL,"d");
				echo "<tr>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".$d["otdel"]."</b></td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".$d["msk_norm"]."</b></td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".$d["oa_norm"]."</b></td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".$d["vault_norm"]."</b></td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".$d["forum_norm"]."</b></td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".$d["prison_norm"]."</b></td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap>";
                                if( ($d["id"]!=9999998) && ($d["id"]!=9999997) ) {
					echo "<a href=\"?act=om_stats&do=edit_norms&target=edit&type=otdel&id=".$d["id"]."\" >изменить</a>";
				}
                                echo "</td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap>";
				if( ($d["id"]!=9999999) && ($d["id"]!=9999998) && ($d["id"]!=9999997) ) {
					echo "<a href=\"?act=om_stats&do=edit_norms&target=delete&type=otdel&id=".$d["id"]."\" >удалить</a>";
				}
				echo " </td>\n";
				echo "</tr>";
			}
			echo "</table>";
		///

		/// Вывод сотрудников
			echo "<br><table cellpadding=3 width=75% cellspacing=3>";
			echo "<th colspan=7 background='i/bgr-grid-sand.gif'>Спец нормы сотрудников:</th>\n";
			echo " <tr>\n";
			echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Ник:</b></td>\n";
			echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Норма МСК:</b></td>\n";
			echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Норма ОА:</b></td>\n";
			echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Норма VC:</b></td>\n";
			echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Норма форум:</b></td>\n";
			echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Норма каторга:</b></td>\n";
			echo "  <td background='i/bgr-grid-sand.gif' nowrap></td>\n";
			echo "  <td background='i/bgr-grid-sand.gif' nowrap></td>\n";
			echo " </tr>";

			$SQL="SELECT * FROM om_stat_sp_users ORDER BY id ASC";
			$r=mysql_query($SQL);
			while($d=mysql_fetch_assoc($r)) {
			//	extract($d,EXTR_PREFIX_ALL,"d");
				echo "<tr>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".$d["user"]."</b></td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".$d["msk_norm"]."</b></td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".$d["oa_norm"]."</b></td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".$d["vault_norm"]."</b></td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".$d["forum_norm"]."</b></td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".$d["prison_norm"]."</b></td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><a href=\"?act=om_stats&do=edit_norms&target=edit&type=user&id=".$d["id"]."\">изменить</a></td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><a href=\"?act=om_stats&do=edit_norms&target=delete&type=user&id=".$d["id"]."\">удалить</a></td>\n";
				echo "</tr>";
			}
			echo "</table><br>";
		///

			$W_min=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($W_min)))));
			if(!$W_min){
				$W_min=date("W");
			}

			/// Вывод доп минут
			echo "<br><table cellpadding=3 width=75% cellspacing=3>";
			echo " <th colspan=5 background='i/bgr-grid-sand.gif'><form method=post>Показать неделю:\n";
			echo " <input type=text size=3 name=\"W_min\" value=\"".$W_min."\">\n";
			echo " <input type=submit value=\"показать\">\n";
			echo " </form>Дополнительные минуты сотрудников за ".$W_min." неделю:</th>\n";
			echo " <tr>\n";
			echo "  <td align=\"center\" background='i/bgr-grid-sand.gif' nowrap><b>Ник:</b></td>\n";
			echo "  <td align=\"center\" background='i/bgr-grid-sand.gif' nowrap><b>Доп минуты:</b></td>\n";
			echo "  <td align=\"center\" background='i/bgr-grid-sand.gif' nowrap><b>Город:</b></td>\n";
			echo "  <td align=\"center\" background='i/bgr-grid-sand.gif' nowrap></td>\n";
			echo "  <td align=\"center\" background='i/bgr-grid-sand.gif' nowrap></td>\n";
			echo " </tr>";
			$SQL="SELECT * FROM om_stat_min WHERE id_week='".$W_min."' and year='".date("Y",time())."' order by user asc";
			$r=mysql_query($SQL);
			while($d=mysql_fetch_assoc($r)) {
			//	extract($d,EXTR_PREFIX_ALL,"d");
				if($d['city']==1){$city="Москва";}
				if($d['city']==2){$city="Оазис";}
				if($d['city']==3){$city="Форум";}
				if($d['city']==4){$city="Ваульт";}
				echo " <tr>\n";
				echo "  <td align=\"center\" background='i/bgr-grid-sand.gif' nowrap><b>".$d["user"]."</b></td>\n";
				echo "  <td align=\"center\" background='i/bgr-grid-sand.gif' nowrap><b>".$d["minutes"]."</b></td>\n";
				echo "  <td align=\"center\" background='i/bgr-grid-sand.gif' nowrap><b>".$city."</b></td>\n";
				echo "  <td align=\"center\" background='i/bgr-grid-sand.gif' nowrap><a href=\"?act=om_stats&do=edit_norms&target=edit_min&type=user&id=".$d["id"]."\">изменить</a></td>\n";
				echo "  <td align=\"center\" background='i/bgr-grid-sand.gif' nowrap><a href=\"?act=om_stats&do=edit_norms&target=delete_min&type=user&id=".$d["id"]."\">удалить</a></td>\n";
				echo " </tr>";
			}
			echo "</table><br>";
		///

			echo "<b>Добавить отдел</b>\n";
			echo "<form method=\"post\">\n";
			echo "<input name=\"act\" type=\"hidden\" value=\"om_stats\">\n";
			echo "<input name=\"do\" type=\"hidden\" value=\"edit_norms\">\n";
			echo "<input name=\"target\" type=\"hidden\" value=\"new\">\n";
			echo "<input name=\"type\" type=\"hidden\" value=\"otdel\">\n";
			echo "<table cellpadding=3 cellspacing=3>\n";
			echo " <tr background='i/bgr-grid-sand.gif'>\n";
			echo "  <td>Название отдела:</td>\n";
			echo "  <td>Норма МСК:</td>\n";
			echo "  <td>Норма ОА:</td>\n";
			echo "  <td>Норма VC:</td>\n";
			echo "  <td>Норма форум:</td>\n";
			echo "  <td>Норма каторга:</td>\n";
			echo "  <td></td>\n";
			echo " </tr>\n";
			echo " <tr background='i/bgr-grid-sand.gif'>\n";
			echo "  <td align=\"center\"><input type=\"text\" name=\"otdel\" size=\"17\" /></td>\n";
			echo "  <td align=\"center\"><input type=\"text\" name=\"norma_msk\" size=\"7\" /></td>\n";
			echo "  <td align=\"center\"><input type=\"text\" name=\"norma_oa\" size=\"7\" /></td>\n";
			echo "  <td align=\"center\"><input type=\"text\" name=\"norma_vc\" size=\"7\" /></td>\n";
			echo "  <td align=\"center\"><input type=\"text\" name=\"norma_forum\" size=\"7\" /></td>\n";
			echo "  <td align=\"center\"><input type=\"text\" name=\"norma_prison\" size=\"7\" /></td>\n";
			echo "  <td align=\"center\"><input type=\"submit\" value=\"::добавить::\" /></td></td>\n";
			echo " </tr>\n";
			echo "</table>\n";
			echo "</form>\n";
			echo "<b>Добавить пользователя</b>\n";
			echo "<form method=\"post\">\n";
			echo "<input name=\"act\" type=\"hidden\" value=\"om_stats\">\n";
			echo "<input name=\"do\" type=\"hidden\" value=\"edit_norms\">\n";
			echo "<input name=\"target\" type=\"hidden\" value=\"new\">\n";
			echo "<input name=\"type\" type=\"hidden\" value=\"user\">\n";
			echo "<table cellpadding=3 cellspacing=3>\n";
			echo " <tr background='i/bgr-grid-sand.gif'>\n";
			echo "  <td>Ник сотрудника:</td>\n";
			echo "  <td>Норма МСК:</td>\n";
			echo "  <td>Норма ОА:</td>\n";
			echo "  <td>Норма VC:</td>\n";
			echo "  <td>Норма форум:</td>\n";
			echo "  <td>Норма каторга:</td>\n";
			echo "  <td></td>\n";
			echo " </tr>\n";
			echo " <tr background='i/bgr-grid-sand.gif'>\n";
			echo "  <td align=\"center\"><input type=\"text\" name=\"user\" size=\"17\" /></td>\n";
			echo "  <td align=\"center\"><input type=\"text\" name=\"norma_msk\" size=\"7\" /></td>\n";
			echo "  <td align=\"center\"><input type=\"text\" name=\"norma_oa\" size=\"7\" /></td>\n";
			echo "  <td align=\"center\"><input type=\"text\" name=\"norma_vc\" size=\"7\" /></td>\n";
			echo "  <td align=\"center\"><input type=\"text\" name=\"norma_forum\" size=\"7\" /></td>\n";
			echo "  <td align=\"center\"><input type=\"text\" name=\"norma_prison\" size=\"7\" /></td>\n";
			echo "  <td align=\"center\"><input type=\"submit\" value=\"::добавить::\" /></td>\n";
			echo " </tr>\n";
			echo "</table>\n";
			echo "</form>\n";
			echo "<b>Добавить доп минуты</b>\n";
			echo "<form method=\"post\">\n";
			echo "<input name=\"act\" type=\"hidden\" value=\"om_stats\">\n";
			echo "<input name=\"do\" type=\"hidden\" value=\"edit_norms\">\n";
			echo "<input name=\"target\" type=\"hidden\" value=\"new_min\">\n";
			echo "<table cellpadding=3 cellspacing=3>\n";
			echo " <tr background='i/bgr-grid-sand.gif'>\n";
			echo "  <td>Ник сотрудника:</td>\n";
			echo "  <td>№ недели:</td>\n";
			echo "  <td>Доп минуты:</td>\n";
			echo "  <td>Город:</td>\n";
			echo "  <td></td>\n";
			echo " </tr>\n";
			echo " <tr background='i/bgr-grid-sand.gif'>\n";
			echo "  <td align=\"center\"><input type=\"text\" name=\"user\" size=\"17\" /></td>\n";
			echo "  <td align=\"center\"><input type=\"text\" name=\"add_week\" size=\"7\" value=\"".date("W")."\" /></td>\n";
			echo "  <td align=\"center\"><input type=\"text\" name=\"add_dop_min\" size=\"7\" /></td>\n";
			echo "  <td align=\"center\"><select name=\"city\"><option value=\"1\" selected=\"selected\">Москва</option><option value=\"2\">Оазис</option><option value=\"4\">Ваульт</option><option value=\"3\">Форум</option><option value=\"6\">Каторга</option></select></td>\n";
			echo "  <td align=\"center\"><input type=\"submit\" value=\"::добавить::\" /></td>\n";
			echo " </tr>\n";
			echo "</table>\n";
			echo "</form>\n";
		}

		////////
		///МИНУТЫ
		////////
		if($target=="new_min"){
			$user=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($user)))));
			$add_week=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($add_week)))));
			$add_dop_min=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($add_dop_min)))));
			$city=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($city)))));
			mysql_query("INSERT INTO om_stat_min (id_week,user,minutes,year,city) values ('".$add_week."','".$user."','".$add_dop_min."','".date("Y",time())."','".$city."')");
			echo "<script>top.location.href='?act=om_stats&do=edit_norms&target=list';</script>";
		}

		if($target=="delete_min"){
			mysql_query("delete from om_stat_min where id='".$id."';");
			echo "<script>top.location.href='?act=om_stats&do=edit_norms&target=list';</script>";
		}

		if($target=="edit_min"){

			$id=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($id)))));
			if(!$confirm){
				$SQL="SELECT * FROM om_stat_min where id='".$id."' ";
				$r=mysql_query($SQL);
				while($d=mysql_fetch_assoc($r)) {
				//	extract($d,EXTR_PREFIX_ALL,"d");
					echo "<form method=\"post\">\n";
					echo "<input name=\"act\" type=\"hidden\" value=\"om_stats\">\n";
					echo "<input name=\"do\" type=\"hidden\" value=\"edit_norms\">\n";
					echo "<input name=\"confirm\" type=\"hidden\" value=\"yes\">\n";
					echo "<input name=\"target\" type=\"hidden\" value=\"edit_min\">\n";
					echo "<table cellpadding=3 cellspacing=3>\n";
					echo " <tr background='i/bgr-grid-sand.gif'>\n";
					echo "  <td>Ник сотрудника:</td>\n";
					echo "  <td>№ недели:</td>\n";
					echo "  <td>Доп минуты:</td>\n";
					echo "  <td>Город:</td>\n";
					echo "  <td></td>\n";
					echo " </tr>\n";
					echo " <tr background='i/bgr-grid-sand.gif'>\n";
					echo "  <td align=\"center\"><input type=\"text\" name=\"user\" size=\"17\" value=\"".$d["user"]."\" /></td>\n";
					echo "  <td align=\"center\"><input type=\"text\" name=\"week\" size=\"7\" value=\"".$d["id_week"]."\" /></td>\n";
					echo "  <td align=\"center\"><input type=\"text\" name=\"minutes\" size=\"7\" value=\"".$d["minutes"]."\" /></td>\n";
					echo "  <td align=\"center\"><select name=\"city\">";
					echo "  <option value=\"1\""; if($d['city']==1){ echo " selected=\"selected\""; } echo ">Москва</option>";
					echo "  <option value=\"2\""; if($d['city']==2){ echo " selected=\"selected\""; } echo ">Оазис</option>";
					echo "  <option value=\"4\""; if($d['city']==4){ echo " selected=\"selected\""; } echo ">Ваульт</option>";
					echo "  <option value=\"3\""; if($d['city']==3){ echo " selected=\"selected\""; } echo ">Форум</option>";
					echo "  <option value=\"6\""; if($d['city']==6){ echo " selected=\"selected\""; } echo ">Каторга</option>";
					echo "  </select></td>\n";
					echo "  <td align=\"center\"><input type=\"submit\" value=\"::сохранить::\" /></td>\n";
					echo " </tr>\n";
					echo "</table>\n";
					echo "</form>\n";
				}
			}

			if($confirm=="yes"){
				$id=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($id)))));
				$user=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($user)))));
				$week=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($week)))));
				$min_chat=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($min_chat)))));
				$city=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($city)))));

				mysql_query("UPDATE om_stat_min SET id_week='".$week."', user='".$user."', minutes='".$minutes."', city='".$city."' where id='".$id."'");
				echo "<script>top.location.href='?act=om_stats&do=edit_norms&target=list';</script>";
			}


		}

		////////
		///НОРМЫ
		////////
		if($target=="new"){
				$otdel=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($otdel)))));
				$norma_msk=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($norma_msk)))));
				$norma_forum=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($norma_forum)))));
				$norma_oa=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($norma_oa)))));
				$norma_vc=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($norma_vc)))));
				$norma_prison=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($norma_prison)))));
			if($type=="otdel"){
				$SQL="SELECT * FROM om_stat_norm WHERE otdel='".$otdel."' ";
				$r=mysql_query($SQL);
				$ex = mysql_num_rows($r);
				if($ex==0){
					mysql_query("INSERT INTO om_stat_norm (otdel,msk_norm,oa_norm,forum_norm,vault_norm,prison_norm) values ('".$otdel."','".$norma_msk."','".$norma_oa."','".$norma_forum."','".$norma_vc."','".$norma_prison."')");
				}
			}
			if($type=="user"){
				$user=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($user)))));
//				echo ($user);
				$SQL="SELECT * FROM om_stat_sp_users WHERE user='".$user."' ";
				$r=mysql_query($SQL);
				$ex= mysql_num_rows($r);
				if($ex==0){
					$query = "INSERT INTO om_stat_sp_users (user,msk_norm,oa_norm,forum_norm,vault_norm,prison_norm) values ('".$user."','".$norma_msk."','".$norma_oa."','".$norma_forum."','".$norma_vc."','".$norma_prison."')";
//					echo ($query);
					mysql_query($query);
				}
			}
			echo "<script>top.location.href='?act=om_stats&do=edit_norms&target=list';</script>";
		}
		if($target=="delete"){
			if($type=="otdel"){
				$id=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($id)))));
				if(($id!=9999999)&&($id!=9999998)&&($id!=9999997)){
					mysql_query("delete from om_stat_norm WHERE id='".$id."';");
				}
			}
			if($type=="user"){
				$id=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($id)))));
				mysql_query("delete from om_stat_sp_users WHERE id='".$id."';");
			}
			echo "<script>top.location.href='?act=om_stats&do=edit_norms&target=list';</script>";
		}

		if($target=="edit"){
			$id=mysql_escape_string(strip_tags(trim(htmlspecialchars(stripslashes($id)))));
			if(!$confirm){
				if($type=="otdel"){
					$SQL="SELECT * FROM om_stat_norm WHERE id='".$id."' ";
				}
				if($type=="user"){
					$SQL="SELECT * FROM om_stat_sp_users WHERE id='".$id."' ";
				}
				$r=mysql_query($SQL);
				while($d=mysql_fetch_assoc($r)) {
				//	extract($d,EXTR_PREFIX_ALL,"d");
					echo "<form method=\"post\">\n";
					echo "<input name=\"act\" type=\"hidden\" value=\"om_stats\">\n";
					echo "<input name=\"do\" type=\"hidden\" value=\"edit_norms\">\n";
					echo "<input name=\"confirm\" type=\"hidden\" value=\"yes\">\n";
					echo "<input name=\"target\" type=\"hidden\" value=\"edit\">\n";
					echo "<input name=\"type\" type=\"hidden\" value=\"".$type."\">\n";
					echo "<table cellpadding=3 cellspacing=3>\n";
					echo " <tr background='i/bgr-grid-sand.gif'>\n";
					echo "  <td>";
					if($type=="user"){
						echo "Ник сотрудника:";
					} else {
						echo "Название отдела:";
					}
					echo "</td>\n";
					echo "  <td>Норма МСК:</td>\n";
					echo "  <td>Норма ОА:</td>\n";
					echo "  <td>Норма VC:</td>\n";
					echo "  <td>Норма форум:</td>\n";
					echo "  <td>Норма каторга:</td>\n";
					echo "  <td></td>\n";
					echo " </tr>\n";
					echo " <tr background='i/bgr-grid-sand.gif'>\n";
					echo "  <td align=\"center\"><input type=\"text\" name=\"".$type."\" size=\"17\" value=\"".(($type=="user")?"".$d["user"]."":"".$d["otdel"]."")."\" /></td>\n";
					echo "  <td align=\"center\"><input type=\"text\" name=\"norma_msk\" size=\"7\" value=\"".$d["msk_norm"]."\" /></td>\n";
					echo "  <td align=\"center\"><input type=\"text\" name=\"norma_oa\" size=\"7\" value=\"".$d["oa_norm"]."\" /></td>\n";
					echo "  <td align=\"center\"><input type=\"text\" name=\"norma_vc\" size=\"7\" value=\"".$d["vault_norm"]."\" /></td>\n";
					echo "  <td align=\"center\"><input type=\"text\" name=\"norma_forum\" size=\"7\" value=\"".$d["forum_norm"]."\" /></td>\n";
					echo "  <td align=\"center\"><input type=\"text\" name=\"norma_prison\" size=\"7\" value=\"".$d["prison_norm"]."\" /></td>\n";
					echo "  <td align=\"center\"><input type=\"submit\" value=\"::сохранить::\" /></td>\n";
					echo " </tr>\n";
					echo "</table>\n";
					echo "</form>\n";
				}
			}

			if($confirm=="yes"){
				if($type=="otdel"){
					mysql_query("UPDATE om_stat_norm SET otdel='".$otdel."', msk_norm='".$norma_msk."', oa_norm='".$norma_oa."', forum_norm='".$norma_forum."', vault_norm='".$norma_vc."', prison_norm='".$norma_prison."' where id='".$id."'");
				}
				if($type=="user"){
					$query = "UPDATE om_stat_sp_users SET user='".$user."', msk_norm='".$norma_msk."', oa_norm='".$norma_oa."', forum_norm='".$norma_forum."', vault_norm='".$norma_vc."', prison_norm='".$norma_prison."' where id='".$id."'";
					mysql_query($query);
				}
				echo "<script>top.location.href='?act=om_stats&do=edit_norms&target=list';</script>";
			}
		}
	}


	///////////////////////////
	/// СВОДНАЯ  СТАТИСТИКА ///
	///////////////////////////
	if((!$do)||($do=="stat")){
          function week_start_date($wk_num){
            $wk_ts  = strtotime('+' .$wk_num. ' weeks', strtotime(date('Y', time()) . '0101'));
            $mon_ts = strtotime('-' . date('N', $wk_ts) + 1 . ' days', $wk_ts);
            return date('d.m.Y', $mon_ts);
          }

		$total_pr=0;
		echo "<a href='?act=om_stats&do=edit_norms&target=list'>Редактирование норм</a>";

		if(@$W=="") {
			$W1=date("W");
			$W2=$W1-1;
			//$W2=$W1;
			$W="".$W1.":".$W2."";
		}
		if(strpos($W,":")===false) {
			$_sql="p.id_week='".$W."' AND p.post_t > '".$old."'";
			$W1=$W;
		} else {
			$_tmp=explode(":",$W,2);
			$_sql="(p.id_week='".$_tmp[0]."' OR p.id_week='".$_tmp[1]."') AND p.post_t > '".$old."'";
			$W1=$_tmp[0];
			$W2=$_tmp[1];
		}

		echo "<table cellpadding=3 width=90% cellspacing=3>\n";
		echo "<th background='i/bgr-grid-sand.gif'>Выборка:</th>\n";
		echo " <tr><td background='i/bgr-grid-sand.gif' align=center>\n";
		echo "<form method=\"GET\">\n";
		echo "<input name=\"act\" type=\"hidden\" value=\"om_stats\">\n";
		echo "<input name=\"stats\" type=\"hidden\" value=\"week2\">\n";
		echo "Номер недели: <input name=\"W\" type=\"text\" size=5 value=\"".htmlspecialchars($W)."\">\n";

                if ($W1 > $W2){
                  echo ' ('.week_start_date($W2).' - '.date('d.m.Y', strtotime('+6 days', strtotime(week_start_date($W1)))).')';
                } else {
                  echo ' ('.week_start_date($W1).' - '.date('d.m.Y', strtotime('+6 days', strtotime(week_start_date($W2)))).')';
                }

		echo "<br><input type=\"submit\" value=\"Поиск\">\n";
		echo "</td></tr>\n";
		echo "</table></form>\n";
		echo "<table cellpadding=3 width=90% cellspacing=3>";

		//COPS
		//$SQL="SELECT u.id, d.name, d.dept FROM sd_cops d LEFT JOIN site_users u ON u.user_name=d.name where d.dept<>46 and d.dept<>47 and d.dept<>53 and d.dept<>58 and d.dept<>55 and d.dept<>26 order by d.dept desc";
		$SQL="SELECT u.id, d.name, d.police, d.dept FROM sd_cops d LEFT JOIN site_users u ON u.user_name=d.name where d.police=0 ORDER BY d.dept DESC, d.name ASC";
		$r=mysql_query($SQL);
		//echo mysql_num_rows($r);
		while($d=mysql_fetch_assoc($r)) {
			//extract($d,EXTR_PREFIX_ALL,"d");
			//55
			//if( ($d_dept!=46)&&($d_dept!=47)&&($d_dept!=53) ){
			$stat[$d["id"]]['id']=$d["id"];
			$stat[$d["id"]]['name']=$d["name"];
			$stat[$d["id"]]['time']=0;
			$stat[$d["id"]]['msk']=0;
			$stat[$d["id"]]['vc']=0;
			$stat[$d["id"]]['forum']=0;
			$stat[$d["id"]]['oa']=0;
			$stat[$d["id"]]['prison']=0;
			//}
		}

		//$stat=array_reverse($stat);
		// MOSCOW
		$SQL="SELECT sum(p.post_g-p.post_t) AS time, p.id_user, u.user_name, p.city
	        FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user
	        WHERE ".$_sql." AND p.post_g>0 AND city=1 GROUP BY p.id_user";
		$r=mysql_query($SQL);
		while($d=mysql_fetch_assoc($r)) {
		//	extract($d,EXTR_PREFIX_ALL,"d");
			$stat[$d["id_user"]]['name'] = $d["user_name"];
			$stat[$d["id_user"]]['time'] = $stat[$d["id_user"]]['time'] + $d["time"];
			$stat[$d["id_user"]]['msk'] = $d["time"];
		}

		// FORUM
		$SQL="SELECT sum(p.post_g-p.post_t) AS time, p.id_user, u.user_name, p.city
	        FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user
	        WHERE ".$_sql." AND p.post_g>0 AND city=3 GROUP BY p.id_user";
		$r=mysql_query($SQL);
		while($d=mysql_fetch_assoc($r)) {
		//	extract($d,EXTR_PREFIX_ALL,"d");
			$stat[$d["id_user"]]['name'] = $d["user_name"];
			$stat[$d["id_user"]]['time'] = $stat[$d["id_user"]]['time'] + $d["time"];
			$stat[$d["id_user"]]['forum'] = $d["time"];
		}

		// PRISON
		$SQL="SELECT sum(p.post_g-p.post_t) AS time, p.id_user, u.user_name, p.city
	        FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user
	        WHERE ".$_sql." AND p.post_g>0 AND city=6 GROUP BY p.id_user";
		$r=mysql_query($SQL);
		while($d=mysql_fetch_assoc($r)) {
		//	extract($d,EXTR_PREFIX_ALL,"d");
			$stat[$d["id_user"]]['name'] = $d["user_name"];
			$stat[$d["id_user"]]['time'] = $stat[$d["id_user"]]['time'] + $d["time"];
			$stat[$d["id_user"]]['prison'] = $d["time"];
		}

		// OASIS
		$SQL="SELECT sum(p.post_g-p.post_t) AS time, p.id_user, u.user_name, p.city
	        FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user
	        WHERE ".$_sql." AND p.post_g>0 AND city=2 GROUP BY p.id_user";
		$r=mysql_query($SQL);
		while($d=mysql_fetch_assoc($r)) {
		//	extract($d,EXTR_PREFIX_ALL,"d");
			$stat[$d["id_user"]]['name'] = $d["user_name"];
			$stat[$d["id_user"]]['time'] = $stat[$d["id_user"]]['time'] + $d["time"];
			$stat[$d["id_user"]]['oa'] = $d["time"];

		}

		// VAULT
		$SQL="SELECT sum(p.post_g-p.post_t) AS time, p.id_user, u.user_name, p.city
	        FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user
	        WHERE ".$_sql." AND p.post_g>0 AND city=4 GROUP BY p.id_user";
		$r=mysql_query($SQL);
		while($d=mysql_fetch_assoc($r)) {
		//	extract($d,EXTR_PREFIX_ALL,"d");
			$stat[$d["id_user"]]['name'] = $d["user_name"];
			$stat[$d["id_user"]]['time'] = $stat[$d["id_user"]]['time'] + $d["time"];
			$stat[$d["id_user"]]['vc'] = $d["time"];
		}

		// NM Auction
		$SQL="SELECT sum(p.post_g-p.post_t) AS time, p.id_user, u.user_name, p.city
	        FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user
	        WHERE ".$_sql." AND p.post_g>0 AND city=5 GROUP BY p.id_user";
		$r=mysql_query($SQL);
		while($d=mysql_fetch_assoc($r)) {
		//	extract($d,EXTR_PREFIX_ALL,"d");
			$stat[$d["id_user"]]['name'] = $d["user_name"];
//			$stat[$d["id_user"]]['time'] = $stat[$d["id_user"]]['time'] + $d["time"];
			$stat[$d["id_user"]]['nma'] = $d["time"];
		}

		echo "<th colspan=10 background='i/bgr-grid-sand.gif'>Статистика за неделю</th>\n";
		echo "<tr>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap><b>Пользователь:</b></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap><b>Москва:</b></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap><b>Оазис:</b></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap><b>Форум:</b></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap><b>Каторга:</b></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap><b>Аукцион НМ:</b></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap><b>Сумма:</b></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap><b>Премия:</b></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap><b>% нормы:</b></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap><b>Выплата:</b></td>\n";
		echo "</tr>\n";


		foreach($stat as $id => $d) {
			if($id!=""){
				$Utime_min=round($d['time']/60);
				$oa=round($d['oa']/60);
				$msk=round($d['msk']/60);
				$vc=round($d['vc']/60);
				$forum=round($d['forum']/60);
				$prison=round($d['prison']/60);
				$nma=round($d['nma']/60);
				$otdel = get_dept($d['name']);

			//	$norm = check_norm($d['name'], $otdel, $msk, $oa, $vc, $forum, $W, $W2);
				$norm = check_norm($d['name'], $otdel, $msk, $oa, $vc, $forum, $prison, $W1, $W2);

				if($norm["msk"]==1) {$msk_clr="green";} else {$msk_clr="red";}
				if($norm["oa"]==1) {$oa_clr="green";} else {$oa_clr="red";}
				if($norm["forum"]==1) {$forum_clr="green";} else {$forum_clr="red";}
				if($norm["vc"]==1) {$vc_clr="green";} else {$vc_clr="red";}
				if($norm["prison"]==1) {$prison_clr="green";} else {$prison_clr="red";}

				$cash = ($norm["bonus"] + $nma)*40;

				/*
				if(($msk_norm==3)&&($msk+$oa>=60)) {$msk_norm=1;}
				if(($msk_norm==4)&&($msk+$oa>=105)) {$msk_norm=1;}
				if($msk_norm==1) {$msk_clr="green";} else {$msk_clr="red";}

				$oa_norm=check_norm($otdel,"oa",$oa);
				if(($oa_norm==3)&&($msk+$oa>=60)) {$oa_norm=1;}
				if(($oa_norm==4)&&($msk+$oa>=105)) {$oa_norm=1;}
				if($oa_norm==1) {$oa_clr="green";} else {$oa_clr="red";}
				*/
				if($norm["norma"]>=100) { $zp_clr="green"; } else { $zp_clr="red"; }

				$Utime_min = $Utime_min + $norm["dop"];

			// Доп. минуты
				if ($norm["dop_msk"]>0) {
					$dop_msk = "<font color=green>+<b>".$norm["dop_msk"]."</b></font>";
				} elseif ($norm["dop_msk"]<0) {
					$dop_msk = "<font color=red><b>".$norm["dop_msk"]."</b></font>";
				} else {
					$dop_msk ="";
				}

				if ($norm["dop_oa"]>0) {
					$dop_oa = "<font color=green>+<b>".$norm["dop_oa"]."</b></font>";
				} elseif ($norm["dop_oa"]<0) {
					$dop_oa = "<font color=red><b>".$norm["dop_oa"]."</b></font>";
				} else {
					$dop_oa = "";
				}

				if ($norm["dop_forum"]>0) {
					$dop_forum = "<font color=green>+<b>".$norm["dop_forum"]."</b></font>";
				} elseif ($norm["dop_forum"]<0) {
					$dop_forum = "<font color=red><b>".$norm["dop_forum"]."</b></font>";
				} else {
					$dop_forum = "";
				}

				if ($norm["dop_prison"]>0) {
					$dop_prison = "<font color=green>+<b>".$norm["dop_prison"]."</b></font>";
				} elseif ($norm["dop_prison"]<0) {
					$dop_prison = "<font color=red><b>".$norm["dop_prison"]."</b></font>";
				} else {
					$dop_prison = "";
				}



				$total_pr = $total_pr + $norm["bonus"];

				echo "<tr  background='i/bgr-grid-sand.gif'>\n";
				echo " <td align=left nowrap><a href='?act=om_stats&do=user_stat&userid=".$id."&W=".$W."' title='Статистика по этому пользователю за неделю'>".$d['name']."</a> (".$otdel.")</td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><font color=".$msk_clr."> ".$msk."</font>".$dop_msk."</td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><font color=".$oa_clr."> ".$oa."</font>".$dop_oa."</td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><font color=".$forum_clr."> ".$forum."</font>".$dop_forum."</td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><font color=".$prison_clr."> ".$prison."</font>".$dop_prison."</td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap> ".$nma."</td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><b> ".$norm['summ']."</b></td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap> ".$norm["bonus"]."</td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap><font color=".$zp_clr."><b> ".$norm["norma"]."%</b></font></td>\n";
				echo " <td background='i/bgr-grid-sand.gif' nowrap align='right'> ".($cash>0?$cash:"")."</td>\n";
				echo "</tr>";
			}
		}

		$SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE p.city=1 AND p.id_week='".$W1."' AND p.post_g>0 AND p.post_t > '".$old."'";
		$d=mysql_fetch_array(mysql_query($SQL));
		$msk_total=round($d['ttime']/60);

		$SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE p.city=3 AND p.id_week='".$W1."' AND p.post_g>0 AND p.post_t > '".$old."'";
		$d=mysql_fetch_array(mysql_query($SQL));
		$forum_total=round($d['ttime']/60);

		$SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE p.city=2 AND p.id_week='".$W1."' AND p.post_g>0 AND p.post_t > '".$old."'";
		$d=mysql_fetch_array(mysql_query($SQL));
		$oa_total=round($d['ttime']/60);

		$SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE p.city=4 AND p.id_week='".$W1."' AND p.post_g>0 AND p.post_t > '".$old."'";
		$d=mysql_fetch_array(mysql_query($SQL));
		$vc_total=round($d['ttime']/60);

		$SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE p.city=6 AND p.id_week='".$W1."' AND p.post_g>0 AND p.post_t > '".$old."'";
		$d=mysql_fetch_array(mysql_query($SQL));
		$prison_total=round($d['ttime']/60);

		echo "<tr  background='i/bgr-grid-sand.gif'>\n";
		echo " <td align=left nowrap><b>Загруженность</b></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".round((($msk_total/10080)*100),2)."%</b></font></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".round((($oa_total/10080)*100),2)."%</b></font></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".round((($vc_total/10080)*100),2)."%</b></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".round((($forum_total/10080)*100),2)."%</b></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".round((($prison_total/10080)*100),2)."%</b></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap><b>".$total_pr."</b></td>\n";
		echo " <td background='i/bgr-grid-sand.gif' nowrap></td>\n";
		echo "</tr>";
		echo "</table>";
	}

	//}
	/////////////////////////////
	/// СТАТИСТИКА СОТРУДНИКА ///
	/////////////////////////////

	if($do=="user_stat"){
		if(@$W=="") {
			$W1=date("W");
			$W2=$W1-1;
			//$W2=$W1;
			$W="".$W1.":".$W2."";
		}
		if(strpos($W,":")===false) {
			$_sql="id_week='".$W."' AND post_t > '".$old."'";
			$W1=$W;
		} else {
			$_tmp=explode(":",$W,2);
			$_sql="(id_week='".$_tmp[0]."' OR id_week='".$_tmp[1]."') AND post_t > '".$old."'";
			$W1=$_tmp[0];
			$W2=$_tmp[1];
		}
		echo "<a href='?act=om_stats&do=stat'>Сводная статистика</a><br>";
		echo "<a href='?act=om_stats&do=edit_norms&target=list'>Редактирование норм</a>";

		echo "<table cellpadding=3 width=50% cellspacing=3>";
		echo "<th colspan=2 background='i/bgr-grid-sand.gif'>";
		echo "<form method=\"POST\">\n";
		echo "<input name=\"act\" type=\"hidden\" value=\"om_stats\">\n";
		echo "<input name=\"do\" type=\"hidden\" value=\"user_stat\">\n";
		echo "<input name=\"userid\" type=\"hidden\" value=\"".$userid."\">\n";
		echo "<input name=\"W\" type=\"hidden\" value=\"".$W."\">\n";
		echo "Номер недели: <input name=\"W\" type=\"text\" size=5 value=\"".htmlspecialchars($W)."\"><br>\n";
		echo "<input type=\"submit\" value=\"Поиск\">\n";
		echo "</form>";
		echo "</th>";
		echo "</table><br>";

	///////////
	/////MOSCOW
	///////////
		echo "<table cellpadding=3 width=50% cellspacing=3>";
		$SQL="SELECT user_name FROM site_users WHERE id='".$userid."'";
		$dt=mysql_fetch_array(mysql_query($SQL));
		$UsrStr=$dt['user_name'];
		// query
		$SQL="SELECT post_t, post_g, (post_g-post_t) AS cont FROM posts_report WHERE city=1 and ".$_sql." AND post_g>0 AND id_user='".$userid."' ORDER BY `post_t`";
		//$SQL="SELECT post_t, (post_g-post_t) AS cont FROM posts_report WHERE city=1 and id_week='".$W."' AND post_t > '".$old."' AND post_g>0 AND id_user='".$userid."'";
		//echo ($SQL);
		$r=mysql_query($SQL);
		echo "<th colspan=2  background='i/bgr-grid-sand.gif'>Статистика по посту москвы пользователя ".$UsrStr." за неделю #".$W."</th>\n";
		echo " <tr>\n";
		echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Дата:</b></td>\n";
		echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>кол-во минут:</b></td>\n";
		echo " </tr>";
		while($d=mysql_fetch_array($r)) {
			$cont=round($d['cont']/60);
			echo "<tr>\n";
			echo " <td align=left nowrap>[".date("d.m.y H:i:s",$d['post_t'])."] - [".date("d.m.y H:i:s",$d['post_g'])."]";
			$tmpquery = "SELECT `city` FROM `posts_report` WHERE ".$_sql." AND (`post_t`>'".$d['post_t']."' AND `post_t`<'".$d['post_g']."') AND id_user='".$userid."'";
			//echo ($tmpquery);
			$result = mysql_query($tmpquery);
			if (mysql_num_rows($result) > 0)
				{
					while($x=mysql_fetch_array($result))
						{
							echo ("<br><font color=red>Пересечение с постом <b>".$postname[$x['city']]."</b></font>");
						}
				}
			echo "</td>\n <td nowrap> ".$cont." мин.</td>\n";
			echo "</tr>";
		}
		$SQL="SELECT SUM(post_g-post_t) AS ttime FROM posts_report WHERE city=1 and ".$_sql." AND post_g>0 AND id_user='".$userid."'";
		//$SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE city=1 and p.id_week='".$W."' AND p.post_t > '".$old."' AND p.post_g>0 AND p.id_user='".$userid."'";
		$d=mysql_fetch_array(mysql_query($SQL));
		$TotalTime=round($d['ttime']/60);
		echo "<tr><td background='i/bgr-grid-sand.gif' colspan=2><b>Всего за период: ".$TotalTime." мин.</b></td></tr>";
		echo "</table><br>";


	//////////
	/////OASIS
	//////////
		echo "<table cellpadding=3 width=50% cellspacing=3>";
		$SQL="SELECT user_name FROM site_users WHERE id='".$userid."'";
		//echo ($SQL);
		$dt=mysql_fetch_array(mysql_query($SQL));
		$UsrStr=$dt['user_name'];
		// query
		$SQL="SELECT post_t, post_g, (post_g-post_t) AS cont FROM posts_report WHERE city=2 and ".$_sql." AND post_g>0 AND id_user='".$userid."' ORDER BY `post_t`";
		//$SQL="SELECT post_t, (post_g-post_t) AS cont FROM posts_report WHERE city=2 and id_week='".$W."' AND post_t > '".$old."' AND post_g>0 AND id_user='".$userid."'";
		$r=mysql_query($SQL);
		echo "<th colspan=2  background='i/bgr-grid-sand.gif'>Статистика по посту оазиса пользователя ".$UsrStr." за неделю #".$W."</th>\n";
		echo " <tr>\n";
		echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Дата:</b></td>\n";
		echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>кол-во минут:</b></td>\n";
		echo " </tr>";
		while($d=mysql_fetch_array($r)) {
			$cont=round($d['cont']/60);
			echo "<tr>\n";
			echo " <td align=left nowrap>[".date("d.m.y H:i:s",$d['post_t'])."] - [".date("d.m.y H:i:s",$d['post_g'])."]";
			$tmpquery = "SELECT `city` FROM `posts_report` WHERE ".$_sql." AND (`post_t`>'".$d['post_t']."' AND `post_t`<'".$d['post_g']."') AND id_user='".$userid."'";
			$result = mysql_query($tmpquery);
			if (mysql_num_rows($result) > 0)
				{
					while($x=mysql_fetch_array($result))
						{
							echo ("<br><font color=red>Пересечение с постом <b>".$postname[$x['city']]."</b></font>");
						}
				}
			echo "</td>\n <td nowrap> ".$cont." мин.</td>\n";
			echo "</tr>";
		}
		$SQL="SELECT SUM(post_g-post_t) AS ttime FROM posts_report WHERE city=2 and ".$_sql." AND post_g>0 AND id_user='".$userid."'";
		//$SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE city=2 and p.id_week='".$W."' AND p.post_t > '".$old."' AND p.post_g>0 AND p.id_user='".$userid."'";
		//echo ($SQL);
		$d=mysql_fetch_array(mysql_query($SQL));
		$TotalTime=round($d['ttime']/60);
		echo "<tr><td background='i/bgr-grid-sand.gif' colspan=2><b>Всего за период: ".$TotalTime." мин.</b></td></tr>";
		echo "</table><br>";

	//////////
	/////VAULT
	//////////
		echo "<table cellpadding=3 width=50% cellspacing=3>";
		$SQL="SELECT user_name FROM site_users WHERE id='".$userid."'";
		$dt=mysql_fetch_array(mysql_query($SQL));
		$UsrStr=$dt['user_name'];
		// query
		$SQL="SELECT post_t, post_g, (post_g-post_t) AS cont FROM posts_report WHERE city=4 and ".$_sql." AND post_g>0 AND id_user='".$userid."' ORDER BY `post_t`";
		//$SQL="SELECT post_t, (post_g-post_t) AS cont FROM posts_report WHERE city=4 and id_week='".$W."' AND post_t > '".$old."' AND post_g>0 AND id_user='".$userid."'";
		//echo ($SQL);
		$r=mysql_query($SQL);
		echo "<th colspan=2  background='i/bgr-grid-sand.gif'>Статистика по посту vault-city пользователя ".$UsrStr." за неделю #".$W."</th>\n";
		echo " <tr>\n";
		echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Дата:</b></td>\n";
		echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>кол-во минут:</b></td>\n";
		echo " </tr>";
		while($d=mysql_fetch_array($r)) {
			$cont=round($d['cont']/60);
			echo "<tr>\n";
			echo " <td align=left nowrap>[".date("d.m.y H:i:s",$d['post_t'])."] - [".date("d.m.y H:i:s",$d['post_g'])."]";
			$tmpquery = "SELECT `city` FROM `posts_report` WHERE ".$_sql." AND (`post_t`>'".$d['post_t']."' AND `post_t`<'".$d['post_g']."') AND id_user='".$userid."'";
			$result = mysql_query($tmpquery);
			if (mysql_num_rows($result) > 0)
				{
					while($x=mysql_fetch_array($result))
						{
							echo ("<br><font color=red>Пересечение с постом <b>".$postname[$x['city']]."</b></font>");
						}
				}
			echo "</td>\n <td nowrap> ".$cont." мин.</td>\n";
			echo "</tr>";
		}
		$SQL="SELECT SUM(post_g-post_t) AS ttime FROM posts_report WHERE city=4 and ".$_sql." AND post_g>0 AND id_user='".$userid."'";
		//$SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE city=4 and p.id_week='".$W."' AND p.post_t > '".$old."' AND p.post_g>0 AND p.id_user='".$userid."'";
		//echo ($SQL);
		$d=mysql_fetch_array(mysql_query($SQL));
		$TotalTime=round($d['ttime']/60);
		echo "<tr><td  background='i/bgr-grid-sand.gif' colspan=2><b>Всего за период: ".$TotalTime." мин.</b></td></tr>";
		echo "</table><br>";


	///////////
	/////FORUM
	///////////
		echo "<table cellpadding=3 width=50% cellspacing=3>";
		$SQL="SELECT user_name FROM site_users WHERE id='".$userid."'";
		$dt=mysql_fetch_array(mysql_query($SQL));
		$UsrStr=$dt['user_name'];
		// query
		$SQL="SELECT post_t, post_g, (post_g-post_t) AS cont FROM posts_report WHERE city=3 and ".$_sql." AND post_g>0 AND id_user='".$userid."' ORDER BY `post_t`";
		//$SQL="SELECT post_t, (post_g-post_t) AS cont FROM posts_report WHERE city=3 and id_week='$W1' AND post_t > '".$old."' AND post_g>0 AND id_user='".$userid."'";
		$r=mysql_query($SQL);
		echo "<th colspan=2  background='i/bgr-grid-sand.gif'>Статистика по посту форума пользователя ".$UsrStr." за неделю #".$W."</th>\n";
		echo " <tr>\n";
		echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Дата:</b></td>\n";
		echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>кол-во минут:</b></td>\n";
		echo " </tr>";
		while($d=mysql_fetch_array($r)) {
			$cont=round($d['cont']/60);
			echo "<tr>\n";
			echo " <td align=left nowrap>[".date("d.m.y H:i:s",$d['post_t'])."] - [".date("d.m.y H:i:s",$d['post_g'])."]";
			$tmpquery = "SELECT `city` FROM `posts_report` WHERE ".$_sql." AND (`post_t`>'".$d['post_t']."' AND `post_t`<'".$d['post_g']."') AND id_user='".$userid."'";
			$result = mysql_query($tmpquery);
			if (mysql_num_rows($result) > 0)
				{
					while($x=mysql_fetch_array($result))
						{
							echo ("<br><font color=red>Пересечение с постом <b>".$postname[$x['city']]."</b></font>");
						}
				}
			echo "</td>\n <td nowrap> ".$cont." мин.</td>\n";
			echo "</tr>";
		}
		$SQL="SELECT SUM(post_g-post_t) AS ttime FROM posts_report WHERE city=3 and ".$_sql." AND post_g>0 AND id_user='".$userid."'";
		//$SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE city=3 and p.id_week='".$W1."' AND p.post_t > '".$old."' AND p.post_g>0 AND p.id_user='".$userid."'";
		$d=mysql_fetch_array(mysql_query($SQL));
		$TotalTime=round($d['ttime']/60);
		echo "<tr><td background='i/bgr-grid-sand.gif' colspan=2><b>Всего за период: ".$TotalTime." мин.</b></td></tr>";
		echo "</table><br>";

	///////////
	/////PRISON
	///////////
		echo "<table cellpadding=3 width=50% cellspacing=3>";
		$SQL="SELECT user_name FROM site_users WHERE id='".$userid."'";
		$dt=mysql_fetch_array(mysql_query($SQL));
		$UsrStr=$dt['user_name'];
		// query
		$SQL="SELECT post_t, post_g, (post_g-post_t) AS cont FROM posts_report WHERE city=6 and ".$_sql." AND post_g>0 AND id_user='".$userid."' ORDER BY `post_t`";
		//$SQL="SELECT post_t, (post_g-post_t) AS cont FROM posts_report WHERE city=3 and id_week='$W1' AND post_t > '".$old."' AND post_g>0 AND id_user='".$userid."'";
		$r=mysql_query($SQL);
		echo "<th colspan=2  background='i/bgr-grid-sand.gif'>Статистика по посту каторги пользователя ".$UsrStr." за неделю #".$W."</th>\n";
		echo " <tr>\n";
		echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Дата:</b></td>\n";
		echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>кол-во минут:</b></td>\n";
		echo " </tr>";
		while($d=mysql_fetch_array($r)) {
			$cont=round($d['cont']/60);
			echo "<tr>\n";
			echo " <td align=left nowrap>[".date("d.m.y H:i:s",$d['post_t'])."] - [".date("d.m.y H:i:s",$d['post_g'])."]";
			$tmpquery = "SELECT `city` FROM `posts_report` WHERE ".$_sql." AND (`post_t`>'".$d['post_t']."' AND `post_t`<'".$d['post_g']."') AND id_user='".$userid."'";
			$result = mysql_query($tmpquery);
			if (mysql_num_rows($result) > 0)
				{
					while($x=mysql_fetch_array($result))
						{
							echo ("<br><font color=red>Пересечение с постом <b>".$postname[$x['city']]."</b></font>");
						}
				}
			echo "</td>\n <td nowrap> ".$cont." мин.</td>\n";
			echo "</tr>";
		}
		$SQL="SELECT SUM(post_g-post_t) AS ttime FROM posts_report WHERE city=6 and ".$_sql." AND post_g>0 AND id_user='".$userid."'";
		//$SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE city=3 and p.id_week='".$W1."' AND p.post_t > '".$old."' AND p.post_g>0 AND p.id_user='".$userid."'";
		$d=mysql_fetch_array(mysql_query($SQL));
		$TotalTime=round($d['ttime']/60);
		echo "<tr><td background='i/bgr-grid-sand.gif' colspan=2><b>Всего за период: ".$TotalTime." мин.</b></td></tr>";
		echo "</table><br>";

	///////////
	/////New Moscow Auction
	///////////
		echo "<table cellpadding=3 width=50% cellspacing=3>";
		$SQL="SELECT user_name FROM site_users WHERE id='".$userid."'";
		$dt=mysql_fetch_array(mysql_query($SQL));
		$UsrStr=$dt['user_name'];
		// query
		$SQL="SELECT post_t, post_g, (post_g-post_t) AS cont FROM posts_report WHERE city=5 and ".$_sql." AND post_g>0 AND id_user='".$userid."' ORDER BY `post_t`";
		//$SQL="SELECT post_t, (post_g-post_t) AS cont FROM posts_report WHERE city=3 and id_week='$W1' AND post_t > '".$old."' AND post_g>0 AND id_user='".$userid."'";
		$r=mysql_query($SQL);
		echo "<th colspan=2  background='i/bgr-grid-sand.gif'>Статистика по посту аукциона НМ пользователя ".$UsrStr." за неделю #".$W."</th>\n";
		echo " <tr>\n";
		echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>Дата:</b></td>\n";
		echo "  <td background='i/bgr-grid-sand.gif' nowrap><b>кол-во минут:</b></td>\n";
		echo " </tr>";
		while($d=mysql_fetch_array($r)) {
			$cont=round($d['cont']/60);
			echo "<tr>\n";
			echo " <td align=left nowrap>[".date("d.m.y H:i:s",$d['post_t'])."] - [".date("d.m.y H:i:s",$d['post_g'])."]";
			$tmpquery = "SELECT `city` FROM `posts_report` WHERE ".$_sql." AND (`post_t`>'".$d['post_t']."' AND `post_t`<'".$d['post_g']."') AND id_user='".$userid."'";
			$result = mysql_query($tmpquery);
			if (mysql_num_rows($result) > 0)
				{
					while($x=mysql_fetch_array($result))
						{
							echo ("<br><font color=red>Пересечение с постом <b>".$postname[$x['city']]."</b></font>");
						}
				}
			echo "</td>\n <td nowrap> ".$cont." мин.</td>\n";
			echo "</tr>";
		}
		$SQL="SELECT SUM(post_g-post_t) AS ttime FROM posts_report WHERE city=5 and ".$_sql." AND post_g>0 AND id_user='".$userid."'";
		//$SQL="SELECT SUM(p.post_g-p.post_t) AS ttime FROM posts_report p WHERE city=3 and p.id_week='".$W1."' AND p.post_t > '".$old."' AND p.post_g>0 AND p.id_user='".$userid."'";
		$d=mysql_fetch_array(mysql_query($SQL));
		$TotalTime=round($d['ttime']/60);
		echo "<tr><td background='i/bgr-grid-sand.gif' colspan=2><b>Всего за период: ".$TotalTime." мин.</b></td></tr>";
		echo "</table><br>";
	}
	//////
	///END
	//////
	}
}


?>
</center>