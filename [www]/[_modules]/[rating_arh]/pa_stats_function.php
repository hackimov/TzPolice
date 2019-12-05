<?
/****************************************************
*	 Lebedev Sergey (fantastish@rambler.ru)			*
****************************************************/


// ---------- Вывод архива полиции онлайн-------------	
	function police_online_view ($months_a, $time, $connection, $db, $post_names){
		
		
		$list_depts = "";
		$query = "SELECT `id`, `name` FROM `sd_depts` ORDER BY `name` ASC";
		$tmp = mysql_query($query, $connection);
		while ($rslt = mysql_fetch_array($tmp)){
		 	$dept[$rslt["id"]] = $rslt["name"];
		 	
		 	$list_depts .= "<OPTION VALUE='".$rslt["id"]."'".(($rslt["id"]==$time[6])?" selected":"")." CLASS=\"select\">".$rslt["name"]."\n";
		}
		
		$selected_dept = $time[6];
		
		if(!isset($time)){
			$time = $time2 = time();
		}elseif(is_array($time)){
			$time2 = mktime(23, 59, 0, $time[4], $time[3], $time[5]);
			$time = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
		}
		
		if($time>time()){
			$time = time();
		}
		
		if($time2>time()){
			$time2 = time();
		}
		
		if($time>$time2){
			$time = $time2;
		}
		
		if($time2<$time){
			$time2 = time();
		}
		
	//	корректировка
		$time = date("d:m:Y:H:i", $time);
		$c_time = $time = explode(":",$time);
		$time = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
		
		$time2 = date("d:m:Y:H:i", $time2);
		$c_time2 = $time2 = explode(":",$time2);
		$time2 = mktime(23, 59, 0, $time2[1], $time2[0], $time2[2]);
		
		
		
		$text = title("Статистика пребываний ПА онлайн<BR>".date("d.m.Y", $time)." 00:00 - ".date("d.m.Y", $time2)." 23:59");
		
		$text .= "<BR><DIV STYLE=\"WIDTH:100%;\">\n";
		
		$text .= "<FORM METHOD=\"GET\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\">\n";
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"pa_stats\">\n";
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"view\">\n";
		$text .= "<TABLE WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\">\n";
		$text .= " <TR VALIGN=\"top\">\n";
		$text .= "  <TD ALIGN=\"left\"><B>Дата c:&nbsp;</B>\n";
		$text .= "   <SELECT NAME=\"time[0]\">\n";
		for($i=1;$i<=31;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[0])?" selected":"")." CLASS=\"select\">".$i."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[1]\">\n";
		for($i=1;$i<=12;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[1])?" selected":"")." CLASS=\"select\">".$months_a[$i]."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[2]\">\n";
		for($i=2007;$i<=date("Y");$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[2])?" selected":"")." CLASS=\"select\">".$i."\n";
		}
		$text .= "   </SELECT>&nbsp;<B>00:00</B>\n";
		
		$text .= "  <B>&nbsp;-&nbsp;по:&nbsp;</B>\n";
		$text .= "   <SELECT NAME=\"time[3]\">\n";
		for($i=1;$i<=31;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time2[0])?" selected":"")." CLASS=\"select\">".$i."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[4]\">\n";
		for($i=1;$i<=12;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time2[1])?" selected":"")." CLASS=\"select\">".$months_a[$i]."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[5]\">\n";
		for($i=2007;$i<=date("Y");$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time2[2])?" selected":"")." CLASS=\"select\">".$i."\n";
		}
		$text .= "   </SELECT>&nbsp;<B>23:59\n";
		$text .= "  </TD>\n";
		$text .= " </TR>\n";
		
		$text .= " <TR VALIGN=\"top\">\n";
		$text .= "  <TD ALIGN=\"left\"><B>Отдел:&nbsp;</B>\n";
		$text .= "   <SELECT NAME=\"time[6]\">\n";
		$text .= "<OPTION VALUE='0' CLASS=\"select\"> -- Все -- \n";
		$text .= $list_depts;
		$text .= "   </SELECT>\n";
		$text .= "   <input type=\"submit\" class=\"submit\" value=\"Вывести >>\">\n";
		$text .= "  </TD>\n";
		$text .= " </TR>\n";
		
		$text .= "</FORM>\n";
		$text .= "</TABLE><BR>\n";
		
	/*	
	// пред. день
		$l_time = $time-86400;
		$l_time = date("d:m:Y:H:i", $l_time);
		$l_time = explode(":",$l_time);
		$text .= "<CENTER><A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=cops_stats&action=police&time[0]=".$l_time[0]."&time[1]=".$l_time[1]."&time[2]=".$l_time[2]."\"><< пред. день</A>\n";
	
	// след. день
		$n_time = $time2+86400;
		$n_time = date("d:m:Y:H:i", $n_time);
		$n_time = explode(":",$n_time);
		$text .= " &nbsp;&nbsp;|&nbsp;&nbsp; <A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=cops_stats&action=police&time[0]=".$n_time[0]."&time[1]=".$n_time[1]."&time[2]=".$n_time[2]."\">след. день >></A></CENTER>\n";
	*/	$text .= "</DIV>\n";
		
		
		$date=date("Y-m-d", $time);
		$date2=date("Y-m-d", $time2);
		if($date==$date2){
			$where_date = "`date` = '".$date."'";
		}else{
			$where_date = array();
			$t_time2 = $time2;
			while($t_time2 > $time){
				$where_date[] = "`date` = '".date("Y-m-d", $t_time2)."'";
				
				$t_time2 = $t_time2-86400;
			}
				
			if(sizeof($where_date>1)){
				$where_date = implode(" OR ", $where_date);
			}else{
				$where_date = $where_date[0];
			}
		
		}
		
		$sSQL = "SELECT * FROM `pa_online` WHERE (".$where_date.") ORDER BY `nick` ASC, `login` ASC";
		$result = mysql_query($sSQL,$connection);
		
		$nrows=mysql_num_rows($result);
		if($nrows>0){
			$text .= "<BR>\n";
		//	$text .= "<CENTER>Всего: ".$nrows." человек</CENTER>\n";
			$text .= "<TABLE WIDTH=\"100%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\">\n";
			$text .= "<TR BGCOLOR=\"#E4DDC5\" ALIGN=\"center\">\n";
			$text .= " <TD><B>Логин</B></TD>\n";
			$text .= " <TD><B>Онлайн</B></TD>\n";
			$text .= "</TR>\n";

			$bgcolor[1]="#F5F5F5";
			$bgcolor[2]="#E4DDC5";
			$i=1;
			$i2=0;
			
			$chat_on = array();
			$chat_off = array();
			$vne_goroda = array();
			$chat = array();
			
			while($row = mysql_fetch_array($result)){
				
				$sSQL4 = "SELECT `dept` FROM `sd_cops` WHERE `name` = '".$row["nick"]."' LIMIT 1;";
				$result4 = mysql_query($sSQL4, $connection);
				$row4 = mysql_fetch_array($result4);
				if(($selected_dept>0 && $row4["dept"]==$selected_dept) || $selected_dept==0){
					
					if($row["logout"]=="0"){
						$row["logout"] = time();
					}
					
					$d_time = $row["logout"]-$row["login"];
					
					if($row["status"]==1){
						$chat_off[$row["nick"]] = $chat_off[$row["nick"]]+$d_time;
					}elseif($row["status"]==2){
						$vne_goroda[$row["nick"]] = $vne_goroda[$row["nick"]]+$d_time;
					}else{
						$chat_on[$row["nick"]] = $chat_on[$row["nick"]]+$d_time;
					}
					$chat[$row["nick"]] = $chat_off[$row["nick"]] + $chat_on[$row["nick"]] + $vne_goroda[$row["nick"]];
					
				}
			}
			
			
			asort ($chat);
			reset ($chat);
			
			foreach($chat AS $key=>$n){
				
				$row["nick"] = $key;
				
				$i2++;
				if($i>2) $i=1;
			
				$text .= "<TR BGCOLOR=\"".$bgcolor[$i]."\">\n";
				
				$sSQL3 = "SELECT `clan_id`, `pro`, `level`, `name`, `sex` FROM `".$db["tz_users"]."` WHERE `name`='".$row["nick"]."'";
				$result3 = mysql_query($sSQL3, $connection);
			// если в базе персов ТЗ не находим ник
				if(mysql_num_rows($result3)<1){
				// Добавляем ник в бд и получаем его id,
					$sSQLx = "INSERT INTO `".$db["tz_users"]."` SET `name`='".anti_sql_injection($row["nick"])."', pro='0'";
					mysql_query($sSQLx, $connection);
				//	$name[$i] = mysql_insert_id($connection);
					$sSQL3 = "SELECT `clan_id`, `pro`, `level`, `name`, `sex` FROM `".$db["tz_users"]."` WHERE `id`='".mysql_insert_id($connection)."'";
					$result3 = mysql_query($sSQL3, $connection);
				}
				
				$row3 = mysql_fetch_array($result3);
				
				if($row3["clan_id"]>0){
					$sSQL2 = "SELECT * FROM `".$db["tz_clans"]."` WHERE `id`='".$row3["clan_id"]."'";
					$result2 = mysql_query($sSQL2, $connection);
					$row2 = mysql_fetch_array($result2);
					$clan = "{_CLAN_}".trim($row2["name"])."{_/CLAN_}";
				}else{
					$clan = "";
				}
				//	echo "| ".$clan." ".$name." ".$level." | ".$prof." |";
				
				if(empty($row3["pro"])){
					$row3["pro"]="0";
				}
				$text .= " <TD VALIGN=top>".$clan." <A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=pa_stats&action=view2&time[0]=".$c_time[0]."&time[1]=".$c_time[1]."&time[2]=".$c_time[2]."&time[3]=".$c_time2[0]."&time[4]=".$c_time2[1]."&time[5]=".$c_time2[2]."&login=".$row["nick"]."\">".stripslashes($row3["name"])."</A> [".$row3["level"]."] {_PROF_}".$row3["pro"]."".(($row3["sex"]=="0")?"w":"")."{_/PROF_}<BR>\n";
				
				$sSQL4 = "SELECT `dept` FROM `sd_cops` WHERE `name` = '".$row["nick"]."' LIMIT 1;";
				$result4 = mysql_query($sSQL4, $connection);
				$row4 = mysql_fetch_array($result4);
					
				$text .= "<SMALL>".$dept[$row4["dept"]]."</SMALL>";
				
				$query="SELECT p.post_t, p.post_g, p.city FROM `posts_report_academy` AS p LEFT JOIN site_users u ON u.id=p.id_user WHERE u.user_name='".$row["nick"]."' AND (p.post_t>".$time." AND p.post_t<".$time2.") ORDER BY p.post_t";
				
				$rs = mysql_query($query);
				if (mysql_num_rows($rs) > 0){
					$total_timeonpost=0;
					while($cur_st = mysql_fetch_array($rs)){
					//	$timeonpost = floor((time() - $cur_st['post_t'])/60);
						if($cur_st['post_g']=="0"){
							$cur_st['post_g'] = time();
						}
						$timeonpost = $cur_st['post_g'] - $cur_st['post_t'];
						
						$total_timeonpost = $total_timeonpost + $timeonpost;
					}
					$total_timeonpost = floor($total_timeonpost/60);
					$text .= "<BR><b>На посту:</b> ";
					$text .= "".(($total_timeonpost>59)?"".floor($total_timeonpost/60)." час(ов) ".round($total_timeonpost%60)." минут(ы)":"".$total_timeonpost." минут(ы)")."";
				}
				
				$text .= "</TD>\n";
				
				$text .= " <TD>\n";
				
				$chat_on[$row["nick"]] = round($chat_on[$row["nick"]]/60);
				$chat_off[$row["nick"]] = round($chat_off[$row["nick"]]/60);
				$vne_goroda[$row["nick"]] = round($vne_goroda[$row["nick"]]/60);
				$chat[$row["nick"]] = round($chat[$row["nick"]]/60);
				
				$text .= "Online: ~".(($chat[$row["nick"]]>59)?"".floor($chat[$row["nick"]]/60)." час(ов) ".round($chat[$row["nick"]]%60)." минут(ы)":"".$chat[$row["nick"]]." минут(ы)")."<BR>\n";
				$text .= "[Chat: On] ~".(($chat_on[$row["nick"]]>59)?"".floor($chat_on[$row["nick"]]/60)." час(ов) ".round($chat_on[$row["nick"]]%60)." минут(ы)":"".$chat_on[$row["nick"]]." минут(ы)")."<BR>\n";
				$text .= "[Chat: Off] ~".(($chat_off[$row["nick"]]>59)?"".floor($chat_off[$row["nick"]]/60)." час(ов) ".round($chat_off[$row["nick"]]%60)." минут(ы)":"".$chat_off[$row["nick"]]." минут(ы)")."<BR>\n";
				$text .= "[За городом] ~".(($vne_goroda[$row["nick"]]>59)?"".floor($vne_goroda[$row["nick"]]/60)." час(ов) ".round($vne_goroda[$row["nick"]]%60)." минут(ы)":"".$vne_goroda[$row["nick"]]." минут(ы)")."<BR>\n";
				$text .= " </TD>\n";
				$text .= "</TR>\n";
				
				$i++;
				
			}
			
			
			$text .= "</TABLE>\n";
			$text .= "<CENTER>Всего: ".$i2." человек</CENTER>\n";
		}else{
			$text .= "<CENTER>ничего нет</CENTER>";
		}
		
		return $text;
	}


// ---------- Вывод архива полиции онлайн-------------	
	function police_online_view2 ($login, $months_a, $time, $connection, $db, $post_names){
		
		
		if(!isset($time)){
			$time = $time2 = time();
		}elseif(is_array($time)){
			$time2 = mktime(23, 59, 0, $time[4], $time[3], $time[5]);
			$time = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
		}
		
		if($time>time()){
			$time = time();
		}
		
		if($time2>time()){
			$time2 = time();
		}
		
		if($time>$time2){
			$time = $time2;
		}
		
		if($time2<$time){
			$time2 = time();
		}
		
	//	корректировка
		$time = date("d:m:Y:H:i", $time);
		$c_time = $time = explode(":",$time);
		$time = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
		
		$time2 = date("d:m:Y:H:i", $time2);
		$c_time2 = $time2 = explode(":",$time2);
		$time2 = mktime(23, 59, 0, $time2[1], $time2[0], $time2[2]);
		
/*		$sSQL = "SELECT * FROM `".$db["tz_users"]."` WHERE `name`='".anti_sql_injection($cid)."'";
		$result = mysql_query($sSQL2, $connection);
		$row = mysql_fetch_array($result);
*/		
		$login= urldecode($login);
		
		$text = title("Статистика пребывания онлайн<BR>".$login."<BR>".date("d.m.Y", $time)." 00:00 - ".date("d.m.Y", $time2)." 23:59");
		
		$text .= "<BR><DIV STYLE=\"WIDTH:100%;\">\n";
		
		$text .= "<FORM METHOD=\"GET\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\">\n";
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"pa_stats\">\n";
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"view2\">\n";
		$text .= "<INPUT TYPE=\"hidden\" NAME=\"login\" VALUE=\"".$login."\">\n";
		$text .= "<TABLE WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\">\n";
		$text .= " <TR VALIGN=\"top\">\n";
		$text .= "  <TD ALIGN=\"left\"><B>Дата c:&nbsp;</B>\n";
		$text .= "   <SELECT NAME=\"time[0]\">\n";
		for($i=1;$i<=31;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[0])?" selected":"")." CLASS=\"select\">".$i."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[1]\">\n";
		for($i=1;$i<=12;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[1])?" selected":"")." CLASS=\"select\">".$months_a[$i]."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[2]\">\n";
		for($i=2007;$i<=date("Y");$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time[2])?" selected":"")." CLASS=\"select\">".$i."\n";
		}
		$text .= "   </SELECT>&nbsp;<B>00:00</B>\n";
		
		$text .= "  <B>&nbsp;-&nbsp;по:&nbsp;</B>\n";
		$text .= "   <SELECT NAME=\"time[3]\">\n";
		for($i=1;$i<=31;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time2[0])?" selected":"")." CLASS=\"select\">".$i."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[4]\">\n";
		for($i=1;$i<=12;$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time2[1])?" selected":"")." CLASS=\"select\">".$months_a[$i]."\n";
		}
		$text .= "   </SELECT>-<SELECT NAME=\"time[5]\">\n";
		for($i=2007;$i<=date("Y");$i++){
			$text .= "<OPTION VALUE='".$i."'".(($i==$c_time2[2])?" selected":"")." CLASS=\"select\">".$i."\n";
		}
		$text .= "   </SELECT>&nbsp;<B>23:59\n";
		$text .= "   <input type=\"submit\" class=\"submit\" value=\"Вывести >>\">\n";
		$text .= "  </TD>\n";
		$text .= " </TR>\n";
		
		$text .= "</FORM>\n";
		$text .= "</TABLE><BR>\n";
		
	/*	
	// пред. день
		$l_time = $time-86400;
		$l_time = date("d:m:Y:H:i", $l_time);
		$l_time = explode(":",$l_time);
		$text .= "<CENTER><A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=cops_stats&action=police&time[0]=".$l_time[0]."&time[1]=".$l_time[1]."&time[2]=".$l_time[2]."\"><< пред. день</A>\n";
	
	// след. день
		$n_time = $time2+86400;
		$n_time = date("d:m:Y:H:i", $n_time);
		$n_time = explode(":",$n_time);
		$text .= " &nbsp;&nbsp;|&nbsp;&nbsp; <A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=cops_stats&action=police&time[0]=".$n_time[0]."&time[1]=".$n_time[1]."&time[2]=".$n_time[2]."\">след. день >></A></CENTER>\n";
	*/	$text .= "</DIV>\n";
		
		
		$date=date("Y-m-d", $time);
		$date2=date("Y-m-d", $time2);
		if($date==$date2){
			$where_date = "`date` = '".$date."'";
		}else{
			$where_date = array();
			
			$t_time2 = $time2;
			while($t_time2 > $time){
				$where_date[] = "`date` = '".date("Y-m-d", $t_time2)."'";
				
				$t_time2 = $t_time2-86400;
			}
				
			if(sizeof($where_date>1)){
				$where_date = implode(" OR ", $where_date);
			}else{
				$where_date = $where_date[0];
			}
		
		}
		
		
		$sSQL = "SELECT * FROM `pa_online` WHERE (".$where_date.") AND `nick` = '".anti_sql_injection($login)."' ORDER BY `login` ASC" ;
		$result = mysql_query($sSQL,$connection);
		
		$nrows=mysql_num_rows($result);
		if($nrows>0){
			$text .= "<BR>\n";

		//	$text .= "<CENTER>Всего: ".$nrows." человек</CENTER>\n";
			$text .= "<TABLE WIDTH=\"100%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\">\n";
			$text .= "<TR BGCOLOR=\"#E4DDC5\" ALIGN=\"center\">\n";
			$text .= " <TD><B>Дата</B></TD>\n";
			$text .= " <TD><B>Онлайн</B></TD>\n";
			$text .= " <TD><B>Пост</B></TD>\n";
			$text .= "</TR>\n";

			$bgcolor[1]="#F5F5F5";
			$bgcolor[2]="#E4DDC5";
			$i=1;
			$i2=0;
			$tchat = $tchat_off = $tchat_on = $tvne_goroda = 0;
			while($row = mysql_fetch_array($result)){
				
				if($c_date!=$row["date"]){
					
					$i2++;
					if($i>2) $i=1;
				
					$text .= "<TR BGCOLOR=\"".$bgcolor[$i]."\">\n";
					
					$text .= " <TD ALIGN=\"center\"><A HREF=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=pa_stats&action=view&time[0]=".date("d", $row["login"])."&time[1]=".date("m", $row["login"])."&time[2]=".date("Y", $row["login"])."&time[3]=".date("d", $row["login"])."&time[4]=".date("m", $row["login"])."&time[5]=".date("Y", $row["login"])."\"><B>".date("d.m.Y", $row["login"])."</B></A></TD>\n";
					$text .= " <TD VALIGN=top>\n";
					
					$sSQL4 = "SELECT * FROM `pa_online` WHERE (`date` = '".$row["date"]."') AND `nick` = '".$row["nick"]."' ORDER BY `login` ASC" ;
					$result4 = mysql_query($sSQL4, $connection);
					
					$chat = $chat_off = $chat_on = $vne_goroda = 0;
					while($row4 = mysql_fetch_array($result4)){
						$text .= date("H:i:s", $row4["login"])." - ".(($row4["logout"]!="0")?"".date("H:i:s", $row4["logout"])."":"".date("H:i:s", time())."")." [";
						if($row4["status"]=="1"){
							$text .= "Chat: Off";
						}elseif($row4["status"]=="2"){
							$text .= "За городом";
						}else{
							$text .= "Chat: On";
						}
						$text .= "]<BR>\n";
						
						if($row4["logout"]=="0"){
							$row4["logout"] = time();
						}
						
						$d_time = $row4["logout"]-$row4["login"];
						
						if($row4["status"]==1){
							$chat_off = $chat_off+$d_time;
						}elseif($row4["status"]==2){
							$vne_goroda = $vne_goroda+$d_time;
						}else{
							$chat_on = $chat_on+$d_time;
						}
					}
					$text .= "<HR size=1>\n";
					
					$tchat_on = $tchat_on + $chat_on;
					$tchat_off = $tchat_off + $chat_off;
					$tvne_goroda = $tvne_goroda + $vne_goroda;
					$tchat = $tchat + $chat;
					
					$chat_on = round($chat_on/60);
					$chat_off = round($chat_off/60);
					$vne_goroda = round($vne_goroda/60);
					$chat = round($chat_off+$chat_on+$vne_goroda);
					
					$text .= "Online: ~".(($chat>59)?"".floor($chat/60)." час(ов) ".round($chat%60)." минут(ы)":"".$chat." минут(ы)")."<BR>\n";
				//	$text .= "Online: ~".round($chat)." минут(ы)<BR>\n";
				//	$text .= "Online: ~".round($chat / 60)." час(ов) ".round($chat % 60)." минут(ы)<BR>\n";
					$text .= "[Chat: On] ~".(($chat_on>59)?"".floor($chat_on/60)." час(ов) ".round($chat_on%60)." минут(ы)":"".$chat_on." минут(ы)")."<BR>\n";
					$text .= "[Chat: Off] ~".(($chat_off>59)?"".floor($chat_off/60)." час(ов) ".round($chat_off%60)." минут(ы)":"".$chat_off." минут(ы)")."<BR>\n";
					$text .= "[За городом] ~".(($vne_goroda>59)?"".floor($vne_goroda/60)." час(ов) ".round($vne_goroda%60)." минут(ы)":"".$vne_goroda." минут(ы)")."<BR>\n";
					$text .= " </TD>\n";
					
					$text .= " <TD VALIGN=top>\n";
					
					
					$rep_time = explode("-",$row["date"]);
					$rep_time = mktime(0, 0, 0, $rep_time[1], $rep_time[2], $rep_time[0]);
				
					$rep_time2 = explode("-",$row["date"]);
					$rep_time2 = mktime(23, 59, 0, $rep_time2[1], $rep_time2[2], $rep_time2[0]);
		
					$query="SELECT p.post_t, p.post_g, p.city FROM `posts_report_academy` AS p LEFT JOIN `site_users` u ON u.id=p.id_user WHERE u.user_name='".$row["nick"]."' AND (p.post_t>".$rep_time." AND p.post_t<".$rep_time2.") ORDER BY p.post_t";
				
					$rs = mysql_query($query);
					if (mysql_num_rows($rs) > 0){
						
						while($cur_st = mysql_fetch_array($rs)){
						//	$timeonpost = floor((time() - $cur_st['post_t'])/60);
							if($cur_st['post_g']=="0"){
								$cur_st['post_g'] = time();
							}
							$timeonpost = floor(($cur_st['post_g'] - $cur_st['post_t'])/60);
							
							$text .= "[".date("d.m H:i", $cur_st['post_t'])." - ".date("d.m H:i", $cur_st['post_g'])."] ";
							$text .= "<b>".(($timeonpost>59)?"".floor($timeonpost/60)." час(ов) ".round($timeonpost%60)." минут(ы)":"".$timeonpost." минут(ы)")."</b>";
							$text .= " - ".$post_names[$cur_st['city']]."";
							$text .= "<BR>";
							
						}
					}
					$text .= " </TD>\n";
					$text .= "</TR>\n";
				
					$i++;
					$c_date=$row["date"];
				
				}
			}
			
			$text .= "</TABLE>\n";
			
			$tchat_on = round($tchat_on/60);
			$tchat_off = round($tchat_off/60);
			$tvne_goroda = round($tvne_goroda/60);
			$tchat = round($tchat_off+$tchat_on+$tvne_goroda);
			
			$text .= "<B>Итого:</B><BR>";
			$text .= "Online: ~".(($tchat>59)?"".floor($tchat/60)." час(ов) ".round($tchat%60)." минут(ы)":"".$tchat." минут(ы)")."<BR>\n";
			$text .= "[Chat: On] ~".(($tchat_on>59)?"".floor($tchat_on/60)." час(ов) ".round($tchat_on%60)." минут(ы)":"".$tchat_on." минут(ы)")."<BR>\n";
			$text .= "[Chat: Off] ~".(($tchat_off>59)?"".floor($tchat_off/60)." час(ов) ".round($tchat_off%60)." минут(ы)":"".$tchat_off." минут(ы)")."<BR>\n";
			$text .= "[За городом] ~".(($tvne_goroda>59)?"".floor($tvne_goroda/60)." час(ов) ".round($tvne_goroda%60)." минут(ы)":"".$tvne_goroda." минут(ы)")."<BR>\n";
			
		}else{
			$text .= "<CENTER>ничего нет</CENTER>";
		}
		
		return $text;
	}


?>