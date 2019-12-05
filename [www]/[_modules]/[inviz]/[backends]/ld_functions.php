<?php 
$_RESULT = array("res" => "ok");
require_once "../../xhr_config.php";
require_once "../../xhr_php.php";
require_once "../../mysql.php";
require_once "../../functions.php";
require_once "../../auth.php";
$JsHttpRequest =& new Subsys_JsHttpRequest_Php("windows-1251");
extract($_REQUEST);

error_reporting(1);

function list_ld_by_clan($list_clan){
	$depts=array();
	$SQL="select `descr_short` from police_ld where clan='".$list_clan."' group by `descr_short` ORDER BY `descr_short` ASC";
	$rd=mysql_query($SQL);
	while($depts_r=mysql_fetch_array($rd)) {
		$dept_cut=str_replace(" (начальник отдела)","",$depts_r['descr_short']);
		$dept_cut=str_replace(" (зам начальника)","",$dept_cut);
		if(!in_array($dept_cut,$depts)){
			$depts[]=$dept_cut;
		}
	}
	for($dept_index=0;$dept_index<sizeof($depts);$dept_index++){
		?>
		<tr>
			<td colspan="3" background='i/bgr-grid-sand1.gif'><b><?php echo $depts[$dept_index]; ?></b></td>
		</tr>
		<?php 
	
	
		
		$SQL="select * from police_ld where clan='".$list_clan."' and descr_short='".$depts[$dept_index]." (начальник отдела)' ORDER BY `nick` ASC ";
		$r=mysql_query($SQL);
		while($d=mysql_fetch_array($r)) {
			$descr_short=str_replace("(начальник отдела)", "(<b>начальник отдела</b>)", $d['descr_short']);
			$descr_short=str_replace("(зам начальника)", "(<b>зам начальника</b>)", $descr_short);
			$drop_id=chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122));
			$SQL="select * from police_ld_entr where ld_id='".$d['id']."' ORDER BY `post_time` ASC";
			$result= mysql_query($SQL);
			$num_results= mysql_num_rows($result);
			?>
			<tr>
				<td width='20%' nowrap background='i/bgr-grid-sand.gif'>
				<span id="<?php echo $drop_id; ?>_clan"><?php if($d['clan']!="archive") { ?><img border=0 src='_imgs/clansld/<?php echo $d['clan']; ?>.gif'><?php } ?></span>
				<a href="#; return false;" onclick="togle('<?php echo $drop_id; ?>',<?php echo $num_results; ?>,1);"><?php echo $d['nick']; ?></a></td>
				<td background='i/bgr-grid-sand.gif' align='left' id="<?php echo $drop_id; ?>_descr_short">&nbsp;<?php echo $descr_short; ?></td>
				<td width='26' background='i/bgr-grid-sand.gif' id="<?php echo $drop_id; ?>_edit_btn"><a href="#; return false;" onclick="if(saving==0){edit_ld('<?php echo $drop_id; ?>');}"><img border=0 src='_imgs/edit.gif'></a></td>
			</tr>
			<tr id="<?php echo $drop_id; ?>_edit_info" style="display:none;">
				<td colspan="3" background='i/bgr-grid-sand.gif'><input type="text" id="<?php echo $drop_id; ?>_id" value="<?php echo $d['id']; ?>" /><input type="text" id="<?php echo $drop_id; ?>_cr_clan" value="<?php echo $d['clan']; ?>" /><input type="text" id="<?php echo $drop_id; ?>_cr_descr_short" value="<?php echo $d['descr_short']; ?>" /><textarea rows="1" cols="1" id="<?php echo $drop_id; ?>_cr_descr_full"><?php echo $d['descr_full']; ?></textarea></td>
			</tr>
			<tr id="<?php echo $drop_id; ?>_descr" style="display:none;">
				<td colspan="3" background='i/bgr-grid-sand.gif'><textarea rows="7" cols="135" id="<?php echo $drop_id; ?>_new_descr"><?php echo $d['descr_full']; ?></textarea></td>
			</tr>
			<?php
			$i=0;
			while($d2=mysql_fetch_array($result)) {
				$t=date("H:i",$d2['post_time']);
				$d=date("d.m.Y",$d2['post_time']);
				$text=nl2br($d2['text']);
				$i++;
				?>
				<tr id="<?php echo $drop_id."_".$i; ?>" style="display:none;">
					<td nowrap width='20%'  background='i/bgr-grid-sand1.gif'><b><?php echo $d2['authore']; ?></b><br /><span style='font-size: 9px'><?php echo $t." ".$d; ?></span></td>
					<td  background='i/bgr-grid-sand1.gif' colspan="2"><?php echo $text; ?></td>
				</tr>
				<?php 
			}
			?>
			<tr id="<?php echo $drop_id; ?>_new" style="display:none;">
				<td colspan="3" background='i/bgr-grid-sand1.gif'>
					<textarea rows="7" cols="135" id="<?php echo $drop_id; ?>_new_entry"></textarea><br />
					<input type="button" name="<?php echo $drop_id; ?>_new_btn" onclick="new_entry('<?php echo $drop_id; ?>');" value="                                                                              Добавить                                                                             " />
				</td>
			</tr>
			<?php 
		}
		$SQL="select * from police_ld where clan='".$list_clan."' and descr_short='".$depts[$dept_index]." (зам начальника)' ORDER BY `nick` ASC ";
		$r=mysql_query($SQL);
		while($d=mysql_fetch_array($r)) {
			$descr_short=str_replace("(начальник отдела)", "(<b>начальник отдела</b>)", $d['descr_short']);
			$descr_short=str_replace("(зам начальника)", "(<b>зам начальника</b>)", $descr_short);
			$drop_id=chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122));
			$SQL="select * from police_ld_entr where ld_id='".$d['id']."' ORDER BY `post_time` ASC";
			$result= mysql_query($SQL);
			$num_results= mysql_num_rows($result);
			?>
			<tr>
				<td width='20%' nowrap background='i/bgr-grid-sand.gif'>
				<span id="<?php echo $drop_id; ?>_clan"><?php if($d['clan']!="archive") { ?><img border=0 src='_imgs/clansld/<?php echo $d['clan']; ?>.gif'><?php } ?></span>
				<a href="#; return false;" onclick="togle('<?php echo $drop_id; ?>',<?php echo $num_results; ?>,1);"><?php echo $d['nick']; ?></a></td>
				<td background='i/bgr-grid-sand.gif' align='left' id="<?php echo $drop_id; ?>_descr_short">&nbsp;<?php echo $descr_short; ?></td>
				<td width='26' background='i/bgr-grid-sand.gif' id="<?php echo $drop_id; ?>_edit_btn"><a href="#; return false;" onclick="if(saving==0){edit_ld('<?php echo $drop_id; ?>');}"><img border=0 src='_imgs/edit.gif'></a></td>
			</tr>
			<tr id="<?php echo $drop_id; ?>_edit_info" style="display:none;">
				<td colspan="3" background='i/bgr-grid-sand.gif'><input type="text" id="<?php echo $drop_id; ?>_id" value="<?php echo $d['id']; ?>" /><input type="text" id="<?php echo $drop_id; ?>_cr_clan" value="<?php echo $d['clan']; ?>" /><input type="text" id="<?php echo $drop_id; ?>_cr_descr_short" value="<?php echo $d['descr_short']; ?>" /><textarea rows="1" cols="1" id="<?php echo $drop_id; ?>_cr_descr_full"><?php echo $d['descr_full']; ?></textarea></td>
			</tr>
			<tr id="<?php echo $drop_id; ?>_descr" style="display:none;">
				<td colspan="3" background='i/bgr-grid-sand.gif'><textarea rows="7" cols="135" id="<?php echo $drop_id; ?>_new_descr"><?php echo $d['descr_full']; ?></textarea></td>
			</tr>
			<?php
			$i=0;
			while($d2=mysql_fetch_array($result)) {
				$t=date("H:i",$d2['post_time']);
				$d=date("d.m.Y",$d2['post_time']);
				$text=nl2br($d2['text']);
				$i++;
				?>
				<tr id="<?php echo $drop_id."_".$i; ?>" style="display:none;">
					<td nowrap width='20%'  background='i/bgr-grid-sand1.gif'><b><?php echo $d2['authore']; ?></b><br /><span style='font-size: 9px'><?php echo $t." ".$d; ?></span></td>
					<td  background='i/bgr-grid-sand1.gif' colspan="2"><?php echo $text; ?></td>
				</tr>
				<?php 
			}
			?>
			<tr id="<?php echo $drop_id; ?>_new" style="display:none;">
				<td colspan="3" background='i/bgr-grid-sand1.gif'>
					<textarea rows="7" cols="135" id="<?php echo $drop_id; ?>_new_entry"></textarea><br />
					<input type="button" id="<?php echo $drop_id; ?>_new_btn" onclick="new_entry('<?php echo $drop_id; ?>');" value="                                                                              Добавить                                                                             " />
				</td>
			</tr>
			<?php 
		}
		
		if($depts[$dept_index]!=""){
			$SQL="select * from police_ld where clan='".$list_clan."' and descr_short like '".$depts[$dept_index]."%' and `descr_short` not like '%начальник отдела%' and `descr_short` not like '%зам начальника%' ORDER BY `nick` ASC ";
		} else {
			$SQL="select * from police_ld where clan='".$list_clan."' and descr_short like '".$depts[$dept_index]."' and `descr_short` not like '%начальник отдела%' and `descr_short` not like '%зам начальника%' ORDER BY `nick` ASC ";
		}
		$r=mysql_query($SQL);
		while($d=mysql_fetch_array($r)) {
			$descr_short=str_replace("(начальник отдела)", "(<b>начальник отдела</b>)", $d['descr_short']);
			$descr_short=str_replace("(зам начальника)", "(<b>зам начальника</b>)", $descr_short);
			$drop_id=chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122)).chr(mt_rand(97,122));
			$SQL="select * from police_ld_entr where ld_id='".$d['id']."' ORDER BY `post_time` ASC";
			$result= mysql_query($SQL);
			$num_results= mysql_num_rows($result);
			?>
			<tr>
				<td width='20%' nowrap background='i/bgr-grid-sand.gif'>
				<span id="<?php echo $drop_id; ?>_clan"><?php if($d['clan']!="archive") { ?><img border=0 src='_imgs/clansld/<?php echo $d['clan']; ?>.gif'><?php } ?></span>
				<a href="#; return false;" onclick="togle('<?php echo $drop_id; ?>',<?php echo $num_results; ?>,1);"><?php echo $d['nick']; ?></a></td>
				<td background='i/bgr-grid-sand.gif' align='left' id="<?php echo $drop_id; ?>_descr_short">&nbsp;<?php echo $descr_short; ?></td>
				<td width='26' background='i/bgr-grid-sand.gif' id="<?php echo $drop_id; ?>_edit_btn"><a href="#; return false;" onclick="if(saving==0){edit_ld('<?php echo $drop_id; ?>');}"><img border=0 src='_imgs/edit.gif'></a></td>
			</tr>
			<tr id="<?php echo $drop_id; ?>_edit_info" style="display:none;">
				<td colspan="3" background='i/bgr-grid-sand.gif'><input type="text" id="<?php echo $drop_id; ?>_id" value="<?php echo $d['id']; ?>" /><input type="text" id="<?php echo $drop_id; ?>_cr_clan" value="<?php echo $d['clan']; ?>" /><input type="text" id="<?php echo $drop_id; ?>_cr_descr_short" value="<?php echo $d['descr_short']; ?>" /><textarea rows="1" cols="1" id="<?php echo $drop_id; ?>_cr_descr_full"><?php echo $d['descr_full']; ?></textarea></td>
			</tr>
			<tr id="<?php echo $drop_id; ?>_descr" style="display:none;">
				<td colspan="3" background='i/bgr-grid-sand.gif'><textarea rows="7" cols="135" id="<?php echo $drop_id; ?>_new_descr"><?php echo $d['descr_full']; ?></textarea></td>
			</tr>
			<?php
			$i=0;
			while($d2=mysql_fetch_array($result)) {
				$t=date("H:i",$d2['post_time']);
				$d=date("d.m.Y",$d2['post_time']);
				$text=nl2br($d2['text']);
				$i++;
				?>
				<tr id="<?php echo $drop_id."_".$i; ?>" style="display:none;">
					<td nowrap width='20%'  background='i/bgr-grid-sand1.gif'><b><?php echo $d2['authore']; ?></b><br /><span style='font-size: 9px'><?php echo $t." ".$d; ?></span></td>
					<td  background='i/bgr-grid-sand1.gif' colspan="2"><?php echo $text; ?></td>
				</tr>
				<?php 
			}
			?>
			<tr id="<?php echo $drop_id; ?>_new" style="display:none;">
				<td colspan="3" background='i/bgr-grid-sand1.gif'>
					<textarea rows="7" cols="135" id="<?php echo $drop_id; ?>_new_entry"></textarea><br />
					<input type="button" id="<?php echo $drop_id; ?>_new_btn" onclick="new_entry('<?php echo $drop_id; ?>');" value="                                                                              Добавить                                                                             " />
				</td>
			</tr>
			<?php 
		
		}
	}
}




$SQL="SELECT * FROM police_ld_users where nick='".AuthUserName."'";
$r=mysql_query($SQL);
$d=mysql_fetch_array($r);
$show_me_police=$d['police'];
$show_me_pa=$d['pa'];
$show_me_mp=$d['mp'];
$show_me_archive=$d['archive'];
$i_am_admin=$d['admin'];

if (AuthUserGroup == '100') {
	$show_me_police=1;
	$show_me_pa=1;
	$show_me_mp=1;
	$show_me_archive=1;
	$i_am_admin=1;
}

if(($show_me_police==1)||($show_me_pa==1)||($show_me_mp==1)||($show_me_archive==1)||($i_am_admin==1)){


	if($action=="list"){
		?>
		<table border="0" width="95%" cellspacing="3" cellpadding="5">
		<?php
		if($type=="norm"){
			if($show_me_police==1){
				list_ld_by_clan("police");
			}
			if($show_me_pa==1){
				list_ld_by_clan("pa");
			}
			if($show_me_mp==1){
				list_ld_by_clan("mp");
			}
		}
		if(($type=="archive")&&($show_me_archive==1)){
			list_ld_by_clan("archive");
		}
	
	
		?>
		</table>
		<?php	
	}

	if($action=="save"){
		$clan=mysql_escape_string($clan);
		$descr_short=mysql_escape_string($descr_short);
		$descr_full=mysql_escape_string($descr_full);
		$id=mysql_escape_string($uid);
		mysql_query("update police_ld set clan='".$clan."', descr_short='".$descr_short."', descr_full='".$descr_full."' where id='".$id."'");	
	}
	if($action=="add"){
		$new_descr=mysql_escape_string($new_descr);
		$id=mysql_escape_string($uid);
		mysql_query("INSERT INTO police_ld_entr (ld_id,authore,post_time,text) values ('".$id."','".AuthUserName."','".time()."','".$new_descr."')");
	}


	if($action=="new_ld"){
		$nick=mysql_escape_string($nick);
		$SQL="select * from police_ld where nick='".$nick."' LIMIT 1";
		$result= mysql_query($SQL);
		$ex= mysql_num_rows($result);
		if($ex==0){
			$info=GetUserInfo($nick, 0);
			if($info['clan']=="police"){$clan="police";}
			if($info['clan']=="Military Police"){$clan="mp";}
			if($info['clan']=="Police Academy"){$clan="pa";}
			mysql_query("INSERT INTO police_ld (nick,clan) values ('".$nick."','".$clan."')");
		}
	}




	if(($action=="show_admins")&&($i_am_admin==1)){
		?><br /><br />
		<a href="#; return false;" onclick="admin_form();">Добавить пользователя</a><br /><br />
		<table border="0" cellspacing="3" cellpadding="5">
			<tr>
				<td background='i/bgr-grid-sand1.gif' rowspan="2"><b>Ник:</b></td>
				<td background='i/bgr-grid-sand1.gif' colspan="3"><b>Может видеть:</b></td>
				<td background='i/bgr-grid-sand1.gif' rowspan="2"><b>Управление<br />доступом:</b></td>
				<td background='i/bgr-grid-sand1.gif' rowspan="2"><b>Действия:</b></td>
			</tr>
			<tr>
				<td background='i/bgr-grid-sand1.gif'><b>Police</b></td>
				<td background='i/bgr-grid-sand1.gif'><b>PA</b></td>
				<td background='i/bgr-grid-sand1.gif'><b>MP</b></td>
			</tr>
			
		<?php 
		$SQL="select * from police_ld_users ORDER BY `nick` ASC ";
		$r=mysql_query($SQL);
		while($d=mysql_fetch_array($r)) {
			?>
			<tr>
				<td background='i/bgr-grid-sand.gif'><b><?php echo $d['nick']; ?></b></td>
				<td background='i/bgr-grid-sand.gif' align="center"><?php if($d['police']==1) { echo "<font color=green><b>Да</b></font>"; } else { echo "<font color=red>Нет</font>"; } ?></td>
				<td background='i/bgr-grid-sand.gif' align="center"><?php if($d['pa']==1) { echo "<font color=green><b>Да</b></font>"; } else { echo "<font color=red>Нет</font>"; } ?></td>
				<td background='i/bgr-grid-sand.gif' align="center"><?php if($d['mp']==1) { echo "<font color=green><b>Да</b></font>"; } else { echo "<font color=red>Нет</font>"; } ?></td>
				<td background='i/bgr-grid-sand.gif' align="center"><?php if($d['admin']==1) { echo "<font color=green><b>Да</b></font>"; } else { echo "<font color=red>Нет</font>"; } ?></td>
				<td background='i/bgr-grid-sand.gif' align="center">
					<a href="#; return false;" onclick="edit_admin('<?php echo $d['nick']; ?>');"><img border=0 src='_imgs/edit.gif'></a>&nbsp&nbsp&nbsp&nbsp&nbsp;
					<a href="#; return false;" onclick="if(confirm('Удалить пользователя?')){delete_admin('<?php echo $d['nick']; ?>');}"><img border=0 src='_modules/inviz/img/del.gif'></a>
				</td>
			</tr>
			<?php
		}
		?></table>
		<?php	
	}

	if(($action=="admin_form")&&($i_am_admin==1)){
		?><br /><br />
		<table border="0" cellspacing="3" cellpadding="5">
			<tr>
				<td background='i/bgr-grid-sand1.gif' rowspan="2"><b>Ник:</b></td>
				<td background='i/bgr-grid-sand1.gif' colspan="3"><b>Может видеть:</b></td>
				<td background='i/bgr-grid-sand1.gif' rowspan="2"><b>Управление<br />доступом:</b></td>
			</tr>
			<tr>
				<td background='i/bgr-grid-sand1.gif'><b>Police</b></td>
				<td background='i/bgr-grid-sand1.gif'><b>PA</b></td>
				<td background='i/bgr-grid-sand1.gif'><b>MP</b></td>
			</tr>
			<tr>
				<td background='i/bgr-grid-sand1.gif'><input type="text" id="na_nick" size="40" /></td>
				<td background='i/bgr-grid-sand1.gif' align="center"><input type="checkbox" value="0" id="na_police" onclick="exec_checked('na_police');"/></td>
				<td background='i/bgr-grid-sand1.gif' align="center"><input type="checkbox" value="0" id="na_pa" onclick="exec_checked('na_pa');" /></td>
				<td background='i/bgr-grid-sand1.gif' align="center"><input type="checkbox" value="0" id="na_mp" onclick="exec_checked('na_mp');" /></td>
				<td background='i/bgr-grid-sand1.gif' align="center"><input type="checkbox" value="0" id="na_admin" onclick="exec_checked('na_admin');" /></td>
			</tr>
			<tr>
				<td colspan="5"><input id="admin_add_btn" type="submit" value="Добавить" onclick="admin_add();" /><span id="waiting"></span></td>
			</tr>
		</table>
		<?php 
	}
	
	if(($action=="add_admin")&&($i_am_admin==1)){
		$nick=mysql_escape_string($nick);
		$police=mysql_escape_string($police);
		$pa=mysql_escape_string($pa);
		$mp=mysql_escape_string($mp);
		$admin=mysql_escape_string($admin);
		mysql_query("INSERT INTO police_ld_users (nick,police,pa,mp,admin) values ('".$nick."','".$police."','".$pa."','".$mp."','".$admin."')");
	}

	if(($action=="edit_admin")&&($i_am_admin==1)){
		$nick=mysql_escape_string($nick);
		$SQL="select * from police_ld_users where nick='".$nick."'";
		$r=mysql_query($SQL);
		$d=mysql_fetch_array($r);
		?><br /><br />
		<table border="0" cellspacing="3" cellpadding="5">
			<tr>
				<td background='i/bgr-grid-sand1.gif' rowspan="2"><b>Ник:</b></td>
				<td background='i/bgr-grid-sand1.gif' colspan="3"><b>Может видеть:</b></td>
				<td background='i/bgr-grid-sand1.gif' rowspan="2"><b>Управление<br />доступом:</b></td>
			</tr>
			<tr>
				<td background='i/bgr-grid-sand1.gif'><b>Police</b></td>
				<td background='i/bgr-grid-sand1.gif'><b>PA</b></td>
				<td background='i/bgr-grid-sand1.gif'><b>MP</b></td>
			</tr>
			<tr>
				<td background='i/bgr-grid-sand1.gif'><input type="text" id="na_nick" size="40" value="<?php echo $d['nick']; ?>" disabled="disabled" /></td>
				<td background='i/bgr-grid-sand1.gif' align="center"><input type="checkbox" id="na_police" onclick="exec_checked('na_police');" <?php if($d['police']==1) { echo "checked=\"checked\" value=\"1\""; } else { echo "value=\"0\""; } ?> /></td>
				<td background='i/bgr-grid-sand1.gif' align="center"><input type="checkbox" id="na_pa" onclick="exec_checked('na_pa');" <?php if($d['pa']==1) { echo "checked=\"checked\" value=\"1\""; } else { echo "value=\"0\""; } ?> /></td>
				<td background='i/bgr-grid-sand1.gif' align="center"><input type="checkbox" id="na_mp" onclick="exec_checked('na_mp');" <?php if($d['mp']==1) { echo "checked=\"checked\" value=\"1\""; } else { echo "value=\"0\""; } ?> /></td>
				<td background='i/bgr-grid-sand1.gif' align="center"><input type="checkbox" id="na_admin" onclick="exec_checked('na_admin');" <?php if($d['admin']==1) { echo "checked=\"checked\" value=\"1\""; } else { echo "value=\"0\""; } ?> /></td>
			</tr>
			<tr>
				<td colspan="5"><input id="admin_add_btn" type="submit" value="Сохранить" onclick="admin_save();" /><span id="waiting"></span></td>
			</tr>
		</table>
		<?php 
	}
	if(($action=="save_admin")&&($i_am_admin==1)){
		$nick=mysql_escape_string($nick);
		$police=mysql_escape_string($police);
		$pa=mysql_escape_string($pa);
		$mp=mysql_escape_string($mp);
		$admin=mysql_escape_string($admin);
		mysql_query("update police_ld_users set nick='".$nick."', police='".$police."', pa='".$pa."', mp='".$mp."', admin='".$admin."' where nick='".$nick."'");	
	}

	if(($action=="delete_admin")&&($i_am_admin==1)){
		$nick=mysql_escape_string($nick);
		mysql_query("delete from police_ld_users where nick='".$nick."';");
	}


} else {
	echo "<b><font color=red>".AuthUserName.", вам отказано в доступе.</font></b>";
}







?>