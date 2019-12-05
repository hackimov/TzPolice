<?php

//if(abs(AccessLevel) & AccessIpWatch)  
//{ 


	if((!$action)||($action=="main")){
		?>Поиск:<br />
		<form method="post">
		<input type="text" name="str" size="20" maxlength="20" /><br />
		По нику <input type="radio" name="type" value="nick" checked="checked" /><br />
		По ip <input type="radio" name="type" value="ip" /><br />
		<input type="hidden" name="act" value="badguys" />
		<input type="hidden" name="action" value="search" />
		<input type="submit" />
		</form><br /><br />
		Новая запись:<br />
		<form method="post">
		Ник взломщика: <input type="text" name="v_nick" size="20" maxlength="20" /><br />
		IP взломщика: <input type="text" name="v_ip" size="20" maxlength="20" /><br />
		Ник постродавшего: <input type="text" name="p_nick" size="20" maxlength="20" /><br />
		IP постродавшего:<input type="text" name="p_ip" size="20" maxlength="20" /><br />
		<input type="hidden" name="act" value="badguys" />
		<input type="hidden" name="action" value="new" />
		<input type="submit" />
		</form>
		<?php 
	}


	if($action=="new"){
		
		$v_nick=mysql_escape_string(strip_tags(trim($v_nick)));
		$v_ip=mysql_escape_string(strip_tags(trim($v_ip)));
		$p_nick=mysql_escape_string(strip_tags(trim($p_nick)));
		$p_ip=mysql_escape_string(strip_tags(trim($p_ip)));
		
		$query="select * from police_bg_nicks where nick='".$v_nick."'";
	 	$result= mysql_query($query);
	 	$num_results= mysql_num_rows($result);
		for($i=0;$i<$num_results;$i++){
			 $row=mysql_fetch_array($result);
			 $hacks=trim($row["hacks"]);
			 $id=trim($row["id"]);
		}
		if($num_results>0){
			$hacks++;
			$query="update police_bg_nicks set hacks='".$hacks."' where nick='".$v_nick."';";
			$result= mysql_query($query);
		}
		if($num_results==0){
			$hacks++;
			$query="insert into police_bg_nicks values (NULL, '".$v_nick."', '1')";
			$result= mysql_query($query);
			$query="select * from police_bg_nicks where nick='".$v_nick."'";
			$result= mysql_query($query);
			$num_results= mysql_num_rows($result);
			for($i=0;$i<$num_results;$i++){
				 $row=mysql_fetch_array($result);
				 $id=trim($row["id"]);
			}
		}
		$query="insert into police_bg_entr values (NULL, '".$id."', '".$v_ip."', '".$p_nick."', '".$p_ip."', '".time()."')";
		$result= mysql_query($query);
		if($result){
		?>
			Добавлено.
			<form method="post">
			<input type="hidden" name="act" value="badguys" />
			<input type="hidden" name="action" value="main" />
			<input type="submit" value="     ::Назад::     ">
			</form>
			<?php 
		}
		else {
			?>
			Ошибка добавления.
			<form method="post">
			<input type="hidden" name="act" value="badguys" />
			<input type="hidden" name="action" value="new" />
			<input type="submit" value="     ::Назад::     ">
			</form>
			<?php 
		}
		
	}


	if($action=="search"){
		$type=mysql_escape_string(strip_tags(trim($type)));
		$str=mysql_escape_string(strip_tags(trim($str)));
		?>
		<table border="1">
		<?php
		if($type=="nick"){
			$query="select * from police_bg_nicks where nick like '%".$str."%' ORDER BY `nick` ASC";
			$result= mysql_query($query);
			$num_results= mysql_num_rows($result);
			for($i=0;$i<$num_results;$i++){
				 $row=mysql_fetch_array($result);
				 $hacks=trim($row["hacks"]);
				 $nick=trim($row["nick"]);
				 $id=trim($row["id"]);
				 echo "<tr><td><a href=\"?act=badguys&action=view&id=".$id."\">".$nick."</a><td><td>".$hacks."</td></tr>";
			}
		}
		if($type=="ip"){
			settype($data,"array");
			$query="select * from police_bg_entr where v_ip like '%".$str."%' ORDER BY `v_ip` ASC";
			$result= mysql_query($query);
			$num_results= mysql_num_rows($result);
			for($i=0;$i<$num_results;$i++){
				 $row=mysql_fetch_array($result);
				 $v_id=trim($row["v_id"]);
				 if(!in_array($v_id,$data)){
				 	$data[sizeof($data)]=$v_id;
				 }
			}
			$query="select * from police_bg_nicks where";
			for($j=0;$j<sizeof($data);$j++){
				$query=$query." id='".$data[$j]."'";
				if($j<sizeof($data)-1){$query=$query." or";}
			}
			$query=$query." ORDER BY `nick` ASC";
			$result= mysql_query($query);
			$num_results= mysql_num_rows($result);
			for($i=0;$i<$num_results;$i++){
				 $row=mysql_fetch_array($result);
				 $hacks=trim($row["hacks"]);
				 $nick=trim($row["nick"]);
				 $id=trim($row["id"]);
				 echo "<tr><td><a href=\"?act=badguys&action=view&id=".$id."\">".$nick."</a><td><td>".$hacks."</td></tr>";
			}
		}
		?>
		</table>
		<form method="post">
		<input type="hidden" name="act" value="badguys" />
		<input type="hidden" name="action" value="main" />
		<input type="submit" value="     ::Назад::     ">
		</form>
		<?php 
	
	
	}

	if($action=="view"){
		?>
		<table border="1">
		<tr><td>Добавлено</td><td>IP взломщика</td><td>Ник пострадавшего</td><td>IP пострадавшего</td></tr>
		<?php 
		$id=mysql_escape_string(strip_tags(trim($id)));
		$query="select * from police_bg_entr where v_id='".$id."' ORDER BY `v_ip` ASC";
		$result= mysql_query($query);
		$num_results= mysql_num_rows($result);
		for($i=0;$i<$num_results;$i++){
			 $row=mysql_fetch_array($result);
			 $v_ip=trim($row["v_ip"]);
			 $p_nick=trim($row["p_nick"]);
			 $p_ip=trim($row["p_ip"]);
			 $post_time=trim($row["post_time"]);
			 $t=date("H:i",$post_time);
 		 	 $d=date("d.m.Y",$post_time);
			 echo "<tr><td>".$t." ".$d."</td><td>".$v_ip."</td><td>".$p_nick."</td><td>".$p_ip."</td></tr>";
		}
		?>
		</table>
		<form method="post">
		<input type="hidden" name="act" value="badguys" />
		<input type="hidden" name="action" value="main" />
		<input type="submit" value="     ::Назад::     ">
		</form>
		<?php 
	}






 
//} 
//else 
//{ 
//echo "Нет доступа";
//}

?>