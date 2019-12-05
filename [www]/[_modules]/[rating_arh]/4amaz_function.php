<?
/****************************************************
*	 Lebedev Sergey (fantastish@rambler.ru)			*
****************************************************/

// ---------- Вывод тех кто на проверке -------------	
	function okp4oin_users_on_check ($action, $connection, $db, $page, $full_access){
		
		$where = array();
		
		if($action=="check"){
			$text = title("Список проверяемых");
			$where[] = "`status`='2'";
		}elseif($action=="checked"){
			$text = title("Список проверенных");
			$where[] = "`status`='3'";
		}else{
	//	$action=="on_check"
			$text = title("Список отправленных на проверку");
			$where[] = "`status`='1'";
		}
		
		
		if(sizeof($where>1)){
			$where = implode(" AND ", $where);
		}else{
			$where = $where[0];
		}
		
		$sSQL = "SELECT * FROM `".$db["okp4oin"]."` WHERE (".$where.") ORDER BY `time` DESC";
		$result = mysql_query($sSQL,$connection);
		$nrows=mysql_num_rows($result);
		if($nrows>0){
			$text .= "<BR>\n";
			list($sql, $ttext) = @list_all_pages($nrows, $page, "30", $sSQL, "act=4amaz&action=".$action."");
			$result=mysql_query($sql, $connection);
			
			$text .= "<CENTER>Всего: ".$nrows." человек</CENTER>\n";
			
			$text .= $ttext;
			
			$text .= "<TABLE WIDTH=\"100%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\">\n";
			$text .= "<TR BGCOLOR=\"#E4DDC5\" ALIGN=\"center\">\n";
			
			if($action=="check" || $action=="on_check"){
				if($full_access==1){
					$text .= "<script language=\"JavaScript\">\n";
					$text .= "<!--\n";
					$text .= "function SelectAll(mark){\n";
					$text .= " for (i = 0; i < document.forms['rating_arh'].elements.length; i++){\n";
					$text .= "  var item = document.forms['rating_arh'].elements[i];\n";
					$text .= "  if (item.name == \"item_id\"){\n";
					$text .= "   item.checked = mark;\n";
					$text .= "  };\n";
					$text .= " }\n";
					$text .= "}\n";
					$text .= "function CheckSelect(form){\n";
					$text .= " var all_ids='';\n";
					$text .= " var d=0;\n";
					$text .= " for (i = 0; i < form.elements.length; i++){\n";
					$text .= "  var item = form.elements[i];\n";
					$text .= "  if (item.name == \"item_id\"){\n";
					$text .= "   if (item.checked){\n";
					$text .= "    d=1;\n";
					$text .= "    all_ids = all_ids +\"|\"+ item.value;\n";
					$text .= "   }\n";
					$text .= "  }\n";
					$text .= " }\n";
					$text .= " if(d==1){\n";
					$text .= "  document.forms['rating_arh'].elements['x_value'].value = all_ids;\n";
					$text .= "  return true;\n";
					$text .= " }else{\n";
					$text .= "  alert(\"Ничего Не выбрано\");\n";
					$text .= "  return false;\n";
					$text .= " }\n";
					$text .= "}\n";
					$text .= "//-->\n";
					$text .= "</script>\n";
					$text .= "<FORM METHOD=\"post\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\" NAME=\"rating_arh\" onSubmit=\"return CheckSelect(this);\">\n";
					$text .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"4amaz\">\n";
					$text .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"\">\n";
					$text .= "<INPUT TYPE=\"hidden\" NAME=\"x_value\" VALUE=\"\">\n";
					$text .= "<INPUT TYPE=\"hidden\" NAME=\"ok\" VALUE=\"1\">\n";
					$text .= " <TD WIDTH=15><SMALL>[ все ]</SMALL><BR><input type=\"checkbox\" onclick=\"SelectAll(checked)\"></TD>\n";
				}
			}
			$text .= " <TD><B>Время отправки</B></TD>\n";
			$text .= " <TD><B>Логин персонажа</B></TD>\n";
			$text .= " <TD><B>Отправил</B></TD>\n";
			if($full_access==1){
				$text .= " <TD>&nbsp;</TD>\n";
			}
			$text .= "</TR>\n";

			$bgcolor[1]="#F5F5F5";
			$bgcolor[2]="#E4DDC5";
			$i=1;
			while($row = mysql_fetch_array($result)){
				
				if($i>2) $i=1;
				//}
			//	if($row["time2"]>time()) $bgcolor="green";
				$text .= "<TR BGCOLOR=\"".$bgcolor[$i]."\">\n";
				if($action=="check" || $action=="on_check"){
					if($full_access==1){
						$text .= " <TD><INPUT TYPE=\"checkbox\" NAME=\"item_id\" VALUE=\"".$row["name_id"]."\"></TD>\n";
					}
				}

				$sSQL2 = "SELECT * FROM `".$db["tz_users"]."` WHERE `id`='".$row["name_id"]."'";
				$result2 = mysql_query($sSQL2, $connection);
				$row2 = mysql_fetch_array($result2);
				
				if($row2["clan_id"]>0){
					$sSQL3 = "SELECT * FROM `".$db["tz_clans"]."` WHERE `id`='".$row2["clan_id"]."'";
					$result3 = mysql_query($sSQL3, $connection);
					$row3 = mysql_fetch_array($result3);
					$clan = "{_CLAN_}".trim($row3["name"])."{_/CLAN_}";
				}else{
					$clan = "";
				}
				
				$text .= " <TD ALIGN=\"center\">".date("d:m:Y H:i", $row["time"])."</TD>\n";
				
				$text .= " <TD><A HREF=\"javascript:{}\"><IMG SRC=\"{_SERVER_NAME_}/i/bullet-red-01a.gif\" BORDER=0 width=\"18\" height=\"11\" OnClick=\"ClBrd('".stripslashes($row2["name"])."');\" ALT=\"Скопировать ник в буфер обмена\"></A> ".$clan." ".stripslashes($row2["name"])."</A> [".$row2["level"]."] {_PROF_}".$row2["pro"]."".(($row2["sex"]=="0")?"w":"")."{_/PROF_}<SMALL><BR>".stripslashes(nl2br($row["text"]))."</SMALL></TD>\n";
				
				$sSQL2 = "SELECT * FROM `site_users` WHERE `id`='".$row["uid"]."'";
				$result2 = mysql_query($sSQL2, $connection);
				$row2 = mysql_fetch_array($result2);
				$text .= " <TD>{_PERS_}".$row2["user_name"]."{_/PERS_}</TD>\n";
				
				if($full_access==1){
					$text .= " <TD><INPUT TYPE=\"button\" CLASS=\"submit\" OnClick=\"location.href='{_SERVER_NAME_}/{_MAIN_SCRIPT_}?act=4amaz&action=user_edit&id=".$row["id"]."'\" value=\"Начать проверку >>\"></TD>\n";
				}
				
				$text .= "</TR>\n";
				
				$i++;
			}
			if($action=="check" || $action=="on_check"){
				if($full_access==1){
					$text .= "<TR>\n";
					$text .= " <TD COLSPAN=\"7\" ALIGN=\"left\">";
					
					if($action=="check"){
						$text .= "<input type=\"submit\" class=\"submit\" OnClick=\"document.forms['rating_arh'].elements['action'].value = 'add_to_check3'\" value=\"Проверены >>\">";
					}else{
				//	$action=="on_check"
						$text .= "<input type=\"submit\" class=\"submit\" OnClick=\"document.forms['rating_arh'].elements['action'].value = 'add_to_check2'\" value=\"Начать проверку >>\">";
					}
					
					
					$text .= "</TD>\n";
					$text .= "</TR>\n";
					$text .= "</FORM>\n";
				}
			}
			$text .= "</TABLE>\n";
			
			$text .= $ttext;
		}else{
			$text .= "<CENTER>ничего нет</CENTER>";
		}
		
		
		return $text;
	}


// ---------- инфа по конкретному персу ----------
	function okp4oin_users_activ ($connection, $db, $id, $ok, $x_value){

		$where="id = '".$id."'";
		
		$sSQL = "SELECT * FROM ".$db["okp4oin"]." WHERE ".$where."";
		$result = mysql_query($sSQL, $connection);
		
		if(mysql_num_rows($result)>0){
			$text = "";
			if($ok==1){
				$x_value[2] = addslashes(trim($x_value[2]));
				
				$inserted = "status = '".$x_value[1]."', text = '".$x_value[2]."', time = '".time()."', `uid`='".AuthUserId."'";
			
				$sSQL2 = "UPDATE ".$db["okp4oin"]." SET ".$inserted." WHERE id=".$id;
				
				if (mysql_query($sSQL2, $connection))
					$text = "<center><FONT COLOR=green><BIG><B>Сохранено</B></BIG></FONT></center>\n";
				else
					$text = "<center><FONT COLOR=red><BIG><B>Ошибка при сохранении</B></BIG></FONT></center>\n";
			}
			
			$sSQL = "SELECT * FROM ".$db["okp4oin"]." WHERE ".$where."";
			$result = mysql_query($sSQL, $connection);
			$row = mysql_fetch_array($result);

			$x_value[1] = $row["status"];
			$x_value[2] = stripslashes(htmlspecialchars($row["text"]));
			
			
			$sSQL2 = "SELECT * FROM `".$db["tz_users"]."` WHERE `id`='".$row["name_id"]."'";
			$result2 = mysql_query($sSQL2, $connection);
			$row2 = mysql_fetch_array($result2);
			
			$form .= title("Проверка ".stripslashes($row2["name"]));
			
			$form .= "   <FORM NAME=\"company\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\" METHOD=\"post\">\n";
			$form .= "    <INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"4amaz\">\n";
			$form .= "    <INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"user_edit\">\n";
			$form .= "    <INPUT TYPE=\"hidden\" NAME=\"id\" VALUE=\"".$id."\">\n";
			$form .= "    <INPUT TYPE=\"hidden\" NAME=\"ok\" VALUE=\"1\">\n";
			$form .= "    <TABLE WIDTH=\"98%\" CELLSPACING=\"2\" CELLPADDING=\"0\" BORDER=\"0\" CLASS=\"size-10\" align=center>\n";
			
			$form .= " <TR VALIGN=\"top\">\n";
			$form .= "  <TD ALIGN=\"right\"><B>Статус:&nbsp;</B></TD>\n";
			$form .= "  <TD>\n";
			$form .= "   <SELECT NAME=\"x_value[1]\">\n";
			$form .= "    <OPTION VALUE=\"2\"".(($x_value[1]==2)?" SELECTED":"")." CLASS=\"select\">&nbsp;Проверяется&nbsp;</OPTION>\n";
			$form .= "    <OPTION VALUE=\"3\"".(($x_value[1]==3)?" SELECTED":"")." CLASS=\"select\">&nbsp;Проверен&nbsp;</OPTION>\n";
			$form .= "   </SELECT>\n";
			$form .= "  </TD>\n";
			$form .= " </TR>\n";
			
			$form .= "     <TR>\n";
			$form .= "      <TD ALIGN=\"right\" VALIGN=\"top\"><B>Комментарии:</B>&nbsp;</TD>\n";
			$form .= "      <TD ALIGN=\"left\">\n";
			$form .= "       <TEXTAREA NAME=\"x_value[2]\" COLS=\"80\" ROWS=\"20\" WRAP=\"virtual\">".$x_value[2]."</TEXTAREA>\n";
			$form .= "      </TD>\n";
			$form .= "     </TR>\n";
		
			$form .= "     <TR>\n";
			$form .= "      <TD></TD>\n";
			$form .= "      <TD ALIGN=\"left\">\n";
			$form .= "       <INPUT TYPE=\"submit\" NAME=\"add\" VALUE=\"Сохранить&nbsp;&gt;&gt;\" CLASS=\"submit\">\n";
			$form .= "      </TD>\n";
			$form .= "     </TR>\n";
			$form .= "    </TABLE>\n";
			$form .= "   </FORM>\n";
			
			$text = $text . $form;
			
		}
		
		return $text;
	}


// ---------- Ручное добавление на проверку ----------
	function okp4oin_users_insert ($connection, $db, $id, $ok, $x_value){
		
		$id="1";
		
		$text = "";
		if($ok==1){
			
			if ($id != "1") $id="0";
			
			$x_value = trim($x_value);
			if($x_value!=""){
				$x_value = eregi_replace("\n",",",$x_value);
				if(eregi(",",$x_value)){
					$x_value = explode(",",$x_value);
					foreach($x_value AS $i){
						$i = trim($i);
					}
				}else{
					$x_value=array($x_value);
				}
				
				$f=1;
				foreach($x_value AS $i){

				// если 101ый элемент - прерываем обработку
					if($f>100) break;

					$i = trim($i);
					if($i!=""){
					// коннект к тз	
//						$tmp_page = fant_TZConn($i, 0);
                                          
                                          // Inviz 26.10.11
                                          $userinfo = GetUserInfo($i, 0);
					//	$tmp_page = TZConn($i, 0); 
					// если вернулась инфа а не ошибки
						//if(!is_array($tmp_page)){
						// парсим инфу	
						//	$userinfo = fant_ParseUserInfo($tmp_page);
						// добавляем/обновляем полученну инфу в нашу базу
						//	fant_tz_users_update($userinfo);
							
							if($id=="1"){
							// получаем id ника
								$sSQL2 = "SELECT id FROM `".$db["tz_users"]."` WHERE `name`='".$userinfo["login"]."'";
								$result2 = mysql_query($sSQL2, $connection);
								$row2 = mysql_fetch_array($result2);
							
							// ищем в базе проверок
								$sSQL = "SELECT COUNT(id) FROM `".$db["okp4oin"]."` WHERE `name_id`='".$row2["id"]."'";
								$result = mysql_query($sSQL, $connection);
								$row = mysql_fetch_array($result);
								$nrows = $row[0];
							// если нету
								if($nrows<1){
									$sSQL = "INSERT INTO ".$db["okp4oin"]." SET `name_id`='".$row2["id"]."', `uid`='".AuthUserId."', `time`='".time()."', `status`='1'";
								}else{
									$sSQL = "UPDATE ".$db["okp4oin"]." SET `uid`='".AuthUserId."', `time`='".time()."', `status`='1' WHERE `name_id`='".$row2["id"]."'";
								}
					
								if (mysql_query($sSQL, $connection))
									$text .= "".$userinfo["login"]." - <FONT COLOR=green><B>OK</B></FONT><BR>\n";
								else
									$text .= "".$userinfo["login"]." - <FONT COLOR=red><B>Ошибка при сохранении</B></FONT><BR>\n";
							}else{
								$text .= "".$userinfo["login"]." - <FONT COLOR=green><B>должен поидеи добавица/обновица =)</B></FONT><BR>\n";
							}
						//}else{
						//	$text .= "".$i." - <FONT COLOR=red><B>Ошибка: ".$tmp_page["error"]."</B></FONT><BR>\n";
						//}
						
						$f++;
					}
				
				}
			}else{
				$text .= "<center><FONT COLOR=red><BIG><B>пустой запрос</B></BIG></FONT></center>\n";
			}
			
		}
		
		$form .= title("Добавление на проверку");
		
		$form .= "   <FORM NAME=\"company\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\" METHOD=\"post\">\n";
		$form .= "    <INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"4amaz\">\n";
		$form .= "    <INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"user_insert\">\n";
		$form .= "    <INPUT TYPE=\"hidden\" NAME=\"ok\" VALUE=\"1\">\n";
		$form .= "    <TABLE WIDTH=\"98%\" CELLSPACING=\"2\" CELLPADDING=\"0\" BORDER=\"0\" CLASS=\"size-10\" align=center>\n";
		
		$form .= "     <TR>\n";
		$form .= "      <TD ALIGN=\"right\" VALIGN=\"top\"><NOBR><B>Ник(и):</B>&nbsp;</NOBR></TD>\n";
		$form .= "      <TD ALIGN=\"left\">\n";
		$form .= "       <TEXTAREA NAME=\"x_value\" COLS=\"90\" ROWS=\"10\" WRAP=\"virtual\"></TEXTAREA>\n";
	//	$form .= "       <BR><INPUT TYPE=\"checkbox\" NAME=\"id\" VALUE=\"1\" CHECKED> - Поставить на проверку<BR><SMALL>(если не отмечено - то персонаж просто добавляется в базу персонажей ТЗ, в базу проверок не пишется)</SMALL>\n";

		$form .= "      <BR><BR><B>*Разделители</B> - \"<B>,</B>\" (запятая) и/или \"<B>Enter</B>\"";
		$form .= "       <BR><B>!!!</B> Очень большие списки желательно не вписывать, т.к. по каждому нику запрашивается инфа с сервера ТЗ\n";
		$form .= "       <BR><B>!!!</B> В целях предотвращения попыток повесить сервер - обрабатываются ТОЛЬКО первые 100 ников, все что следует после 100го не обрабатывается.\n";
		$form .= "     </TD>\n";
		$form .= "     </TR>\n";
		
		$form .= "     <TR>\n";
		$form .= "      <TD></TD>\n";
		$form .= "      <TD ALIGN=\"left\">\n";
		$form .= "       <INPUT TYPE=\"submit\" NAME=\"add\" VALUE=\"Добавить&nbsp;&gt;&gt;\" CLASS=\"submit\">\n";
		$form .= "      </TD>\n";
		$form .= "     </TR>\n";
		$form .= "    </TABLE>\n";
		$form .= "   </FORM>\n";
		
		$text = $text . $form;
		
		return $text;
	}


//===========================================================

// -------форма для выбора по каталогу---------
	function okp4oin_search ($connection, $db, $id, $cid, $page, $ok, $x_value){
		
		$x_value[0]=trim(urldecode($x_value[0]));
		
		$form = "<FORM METHOD=\"get\" ACTION=\"{_SERVER_NAME_}/{_MAIN_SCRIPT_}\" name=\"select\">\n";
		$form .= "<INPUT TYPE=\"hidden\" NAME=\"act\" VALUE=\"4amaz\">\n";
		$form .= "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"search\">\n";
		$form .= "<INPUT TYPE=\"hidden\" NAME=\"ok\" VALUE=\"1\">\n";
		$form .= "<TABLE WIDTH=\"20%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\" CLASS=\"size-10\">\n";
		$form .= " <TR VALIGN=\"top\">\n";
		$form .= "  <TD ALIGN=\"right\"><B>Поиск:&nbsp;</B></TD>\n";
		$form .= "  <TD><INPUT TYPE=\"text\" NAME=\"x_value[0]\" VALUE=\"".$x_value[0]."\" SIZE=\"15\" style=\"width=200\" CLASS=\"text\"></TD>\n";
		$form .= "  <TD ALIGN=left>&nbsp;<INPUT TYPE=\"submit\" VALUE=\" Поиск \" CLASS=\"submit\"></TD>\n";
		$form .= " </TR>\n";
		
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
			
				$sssqc1=mysql_query($sssSQL1,$connection);
				$nrows=mysql_num_rows($sssqc1);
				
			}else
				$nrows=0;
			

			if($nrows>0){
				$row1 = mysql_fetch_array($sssqc1);
				$sssSQL = "SELECT id, status, time FROM `".$db["okp4oin"]."` WHERE name_id='".$row1["id"]."'";
		//		list($sql, $ttext) = @list_all_pages($nrows, $page, "20", $sssSQL, "act=tzrating&action=search&ok=1&x_value[0]=".urlencode($x_value[0])."");
				$result=mysql_query($sssSQL, $connection);
				$text .= "<B><CENTER>Всего найдено по запросу: ".$nrows." записей</CENTER><BR></B>\n";
				$text .= $ttext;
			
				$text .= "<TABLE WIDTH=\"100%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\">\n";
				$text .= "<TR BGCOLOR=\"#E4DDC5\" ALIGN=\"center\">\n";
				$text .= " <TD><B>Логин персонажа</B></TD>\n";
				$text .= " <TD><B>Дата</B></TD>\n";
				$text .= " <TD><B>Статус</B></TD>\n";
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
						$text .= "На проверке";
					}elseif($row["status"]=="3"){
						$text .= "Отработан";
					}
					$text .= "</TD>\n";
					
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
//		$text = preg_replace ("/[^(\w)|(\x7F-\xFF)|(\s)]/", " ", $text);
		$text = ereg_replace (" +", " ", $text);
		$text = trim($text);
		
		
		return $text;
	}



?>