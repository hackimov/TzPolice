<?
/****************************************************
*						поиск 						*
*	 Lebedev Sergey (fantastish@rambler.ru)			*
****************************************************/

// -------форма для выбора по каталогу---------
	function tzrating_search ($connection, $db, $id, $cid, $page, $ok, $x_value){
		
	//	if(!isset($x_value[1])) $x_value[1]="2";
		$x_value[0]=trim(urldecode($x_value[0]));
		
//		$form = "<CENTER><H4>Поиск</H4></CENTER>\n";
		$form = "<FORM METHOD=\"get\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\" name=\"select\">\n";
		$form .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"tzrating\">\n";
		$form .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"search\">\n";
		$form .= "<INPUT TYPE=\"hidden\" NAME=\"ok\" VALUE=\"1\">\n";
		$form .= "<TABLE WIDTH=\"20%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\" CLASS=\"size-10\">\n";
		$form .= " <TR VALIGN=\"top\">\n";
		$form .= "  <TD ALIGN=\"right\"><B>Поиск:&nbsp;</B></TD>\n";
		$form .= "  <TD><INPUT TYPE=\"text\" NAME=\"x_value[0]\" VALUE=\"".$x_value[0]."\" SIZE=\"15\" style=\"width=200\" CLASS=\"text\"></TD>\n";
		$form .= "  <TD ALIGN=left>&nbsp;<INPUT TYPE=\"submit\" VALUE=\" Поиск \" CLASS=\"submit\"></TD>\n";
		$form .= " </TR>\n";
		
/*		$form .= " <TR>\n";
		$form .= "  <TD ALIGN=left>&nbsp;</TD>\n";
		$form .= "  <TD ALIGN=left COLSAPN=2>&nbsp;<INPUT NAME=\"x_value[1]\" TYPE=\"radio\" VALUE=\"1\" CLASS=\"text\"".(($x_value[1]==1)?" CHECKED":"")."> Все слова&nbsp;<INPUT NAME=\"x_value[1]\" TYPE=\"radio\" VALUE=\"2\" CLASS=\"text\"".(($x_value[1]==2)?" CHECKED":"")."> Хотябы одно</TD>\n";
		$form .= " </TR>\n";
*/		
		$form .= "</TABLE></FORM>\n";

	// Если форма ни разу не выводилась
		if($ok!=1){
			$text .= $form;
	// Результат когда все ок
		}else{
			$text .= $form."<HR><CENTER><H4>Результат:</H4></CENTER>";

		// формируем условия для выбора из бд
			if(isset($x_value[0]) && $x_value[0]!="0" && $x_value[0]!=""){
	//==============================================
				$x_value[0] = catalogue_clear_text($x_value[0]);
	//==============================================
				
				$sssSQL1 = "SELECT * FROM `".$db["tz_users"]."` WHERE name LIKE '".$x_value[0]."' ORDER BY `name` ASC";
			
		//		echo $sssSQL;
				$sssqc1=mysql_query($sssSQL1,$connection);
				$nrows=mysql_num_rows($sssqc1);
				
			}else
				$nrows=0;
			

			if($nrows>0){
				$row1 = mysql_fetch_array($sssqc1);
				$sssSQL = "SELECT id, status, time FROM `".$db["rating_check"]."` WHERE name_id='".$row1["id"]."' ORDER BY time DESC";
		//		list($sql, $ttext) = @list_all_pages($nrows, $page, "20", $sssSQL, "act=tzrating&action=search&ok=1&x_value[0]=".urlencode($x_value[0])."");
				$result=mysql_query($sssSQL, $connection);
				$nrows=mysql_num_rows($result);
				$text .= "<B><CENTER>Всего найдено по запросу: ".$nrows." записей</CENTER><BR></B>\n";
				$text .= $ttext;
			
				$text .= "<TABLE WIDTH=\"100%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\">\n";
				$text .= "<TR BGCOLOR=\"#E4DDC5\" ALIGN=\"center\">\n";
				$text .= " <TD><B>Логин персонажа</B></TD>\n";
				$text .= " <TD><B>Дата</B></TD>\n";
				$text .= " <TD><B>Статус</B></TD>\n";
				$text .= " <TD>&nbsp;</TD>\n";
				$text .= "</TR>\n";
				
				$bgcolor[1]="#F5F5F5";
				$bgcolor[2]="#E4DDC5";
				$i=1;
					
				while($row = mysql_fetch_array($result)){
					
					if($i>2) $i=1;
					
					$text .= "<TR BGCOLOR=\"".$bgcolor[$i]."\">\n";
					
					if($row1["clan_id"]>0){
						$sSQL3 = "SELECT * FROM `".$db["tz_clans"]."` WHERE `id`='".$row1["clan_id"]."'";
						$result3 = mysql_query($sSQL3, $connection);
						$row3 = mysql_fetch_array($result3);
						$clan = "{_CLAN_}".trim($row3["name"])."{_/CLAN_}";
					}else{
						$clan = "";
					}
					$text .= " <TD><A HREF=\"javascript:{}\"><IMG SRC=\"{_SERVER_NAME_}/i/bullet-red-01a.gif\" BORDER=0 width=\"18\" height=\"11\" OnClick=\"ClBrd('".stripslashes($row1["pers_name"])."');\" ALT=\"Скопировать ник в буфер обмена\"></A> ".$clan." ".stripslashes($row1["name"])."</A> [".$row1["level"]."] {_PROF_}".$row1["pro"]."".(($row1["sex"]=="0")?"w":"")."{_/PROF_}</TD>\n";
					
					$text .= " <TD>".date("d:m:Y H:i", $row["time"])."</TD>\n";
					
					$text .= " <TD>";
					if($row["status"]=="1"){
						$text .= "Отправлен на проверку";
					}elseif($row["status"]=="2"){
						$text .= "Проверяется";
					}elseif($row["status"]=="3"){
						$text .= "Чист";
					}elseif($row["status"]=="4"){
						$text .= "Прокачка";
					}
					$text .= "</TD>\n";
					$text .= " <TD><input type=\"button\" class=\"submit\" OnClick=\"location.href='{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=tzrating&action=user_edit&id=".$row["id"]."'\" value=\"Изменить статус >>\"></TD>\n";
					
					$text .= "</TR>\n";
					
					$i++;
				}
				$text .= "</TABLE>\n";
			
				$text .= $ttext;
			}else{
				$text .= "<BR><center><B>Ничего не найдено</B></center>\n";
			}
		}
		
		return $text;
	}

// ---------- очистка строки --------------
	function catalogue_clear_text($text){
	
		$text = stripslashes($text);
		$text = str_replace ("(", " ", $text);
		$text = str_replace (")", " ", $text);
		$text = str_replace ("[", " ", $text);
		$text = str_replace ("]", " ", $text);
		$text = str_replace ("{", " ", $text);
		$text = str_replace ("}", " ", $text);

// $document должен содержать HTML-документ.
// Здесь будут удалены тэги HTML, разделы javascript
// и пустое пространство. Также некоторые обычные элементы
// HTML конвертируются в их текстовые эквиваленты.
$search = array ("'<script[^>]*?>.*?</script>'si",  // Вырезается javascript
                 "'<[\/\!]*?[^<>]*?>'si",           // Вырезаются html-тэги
                 "'([\r\n])[\s]+'",                 // Вырезается пустое пространство
                 "'&(quot|#34);'i",                 // Замещаются html-элементы
                 "'&(amp|#38);'i",
                 "'&(lt|#60);'i",
                 "'&(gt|#62);'i",
                 "'&(nbsp|#160);'i",
                 "'&(iexcl|#161);'i",
                 "'&(cent|#162);'i",
                 "'&(pound|#163);'i",
                 "'&(copy|#169);'i",
                 "'&#(\d+);'e");                    // вычисляется как php

$replace = array ("",
                  "",
                  "\\1",
                  "\"",
                  "&",
                  "<",
                  ">",
                  " ",
                  chr(161),
                  chr(162),
                  chr(163),
                  chr(169),
                  "chr(\\1)");

		$text = preg_replace ($search, $replace, $text);
	//	$text = preg_replace ("/[^(\w)|(\x7F-\xFF)|(\s)]/", " ", $text);
		$text = ereg_replace (" +", " ", $text);
		$text = trim($text);
		
		
		return $text;
	}

?>