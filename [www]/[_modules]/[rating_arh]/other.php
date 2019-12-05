<?php
/****************************************************
*					������ �������					*
*													*
*	 Lebedev Sergey (fantastish@rambler.ru)			*
****************************************************/

//-----------------------------------------------------------------------------
// ������� �������� ������� �������� ��������:
	function loadtime ($time_start, $time_end) { 

// ���� ��������� ����� �� ������, ������ ���:
		if (empty ($time_start)) {
			$time_start = getmicrotime ();
// ��������� ����� ��� ��������:
			$text = $time_start;
// ���� ��������� ����� ������, ������ �������� �����:
		} elseif (empty ($time_end)) {
			$time_end = getmicrotime ();
// ��������� ����� ��� ��������:
			$text = $time_end;
// ���� � ��������� � �������� ����� ��������:
		} else {
// ������������ �����:
			$loadtime = $time_end - $time_start;
// ��������� ����� ��� ��������:
			$text = $loadtime;
		}

// ���������� �������� ������� ��� �����:
		return $text;
	}

//-----------------------------------------------------------------------------
// ������� ���������� ����������� ��������� �������:
	function getmicrotime () { 
		list ($usec, $sec) = explode(" ", microtime ()); 
		return ((float) $usec + (float) $sec);
	}

// ������
	function clear($dano){
		$dano=strip_tags($dano,"<u>,<b>,<i>");
		$dano=ereg_replace("\\\r","",$dano);
	//	$dano=ereg_replace("\\\n","<br>",$dano);
		return $dano;
	}

//--------------�����-----------------------
	function str_to_upper($title){
		$title=strtoupper($title);
		return strtr($title, "��������������������������������", "�����Ũ��������������������������");
	}
//--------------����-----------------------	
	function str_to_lower($title){
		$title=strtolower($title);
		return strtr($title, "�����Ũ��������������������������", "��������������������������������");
	}

//-------------- ��������� �������� -------------------
	function title($title) {
		$text.="<center><BIG><B>".$title."</B></BIG></center>\n";

		return $text;
	}
	
// ---------- �������� �������� --------------
	function opentable(){
		$text .= "<TABLE cellSpacing=0 cellPadding=0 width=\"100%\" border=0>\n";
		$text .= " <tr>\n";
		$text .= "  <td>\n";
		
		return $text;
	}

// ---------- �������� �������� --------------
	function closetable(){
		$text .= "  </td>\n";
		$text .= " </tr>\n";
		$text .= "</table>\n";
		
		return $text;
	}

// ---------- 404 --------------
	function error404(){
		$text .= opentable();
		$text .= title("������: ����� �������� ���");
		$text .= "<CENTER><b>�������� ���� ���������, �� ������������� ���� �������� �� ����������.<br>�� ���������������. �� ���� ������, ��� �� ��������� ��� ���-������ � ���� ����.<br>��� �������� �����. �� ������ ���-�� ������� � ���-�� ������.<br>������� ��� �� �������, �� �� ��������� �� ������.<br>������ ����������!!!</b></CENTER><BR>\n";
		$text .= closetable();
		
		return $text;
	}
	
// --------- ��������� ����������������� �������� ------------
	function change_pre_value($SERVER_NAME, $PHP_SELF, $path_to_images, $text){

//		$text = str_replace ("{_IMG_}", "http://".$SERVER_NAME.$path_to_images."", $text);
		$text = str_replace ('{_SERVER_NAME_}', 'http://'.$SERVER_NAME, $text);
		$text = str_replace ('{_MAIN_SCRIPT_}', $PHP_SELF, $text);
		
		return $text;
	}

// --------- �������� ����������������� �������� ------------
	function make_pre_value($SERVER_NAME, $PHP_SELF, $path_to_images, $text){
		
		$SERVER_NAME2 = str_replace ('www.', '', $SERVER_NAME);
		
//		$text = str_replace ("http://".$SERVER_NAME2.$path_to_images."", "{_IMG_}", $text);
//		$text = str_replace ("http://".$SERVER_NAME.$path_to_images."", "{_IMG_}", $text);
		
		$text = str_replace ('http://'.$SERVER_NAME2, '{_SERVER_NAME_}', $text);
		$text = str_replace ('http://'.$SERVER_NAME, '{_SERVER_NAME_}', $text);
		
		$text = str_replace ($PHP_SELF, '{_MAIN_SCRIPT_}', $text);
		
		return $text;
	}

// --------- ������� �������������� SQL-�������� ----------------
	function anti_sql_injection($text){
	// ���������� ��� �������
		$text = str_replace("'", "&#039;", $text);
		$text = str_replace("�", "&#039;", $text);
		$text = str_replace("\"", "&quot;", $text);
		
		return $text;
	}

//------------------ utf8_to_win1251 ------------------------
	function utf8_to_win1251 ($str_src) {
		$str_dst = "";
		$i = 0;
		while ($i< strlen($str_src)) { 
			$code_dst = 0; 
			$code_src1 = ord($str_src[$i]); 
			$i++; 
			if ($code_src1<=127) { 
				$str_dst .= chr($code_src1); 
				continue; 
			} elseif (($code_src1 & 0xE0) == 0xC0) { 
				$code_src2 = ord($str_src[$i++]); 
				if (($code_src2 & 0xC0) != 0x80) 
					continue; 
				$code_dst = ( ($code_src1 & 0x1F) << 6) + ($code_src2 & 0x3F); 
			} elseif (($code_src1 & 0xF0) == 0xE0) { 
				$code_src2 = ord($str_src[$i++]); 
				if (($code_src2 & 0xC0) != 0x80) 
					continue; 
				$code_src3 = ord($str_src[$i++]); 
				if (($code_src3 & 0xC0) != 0x80) 
					continue; 
				$code_dst = ( ($code_src1 & 0x1F) << 12) + ( ($code_src2 & 0x3F) << 6) + ($code_src3 & 0x3F); 
			} elseif (($code_src1 & 0xF8) == 0xF0) { 
				$code_src2 = ord($str_src[$i++]); 
				if (($code_src2 & 0xC0) != 0x80) 
					continue; 
				$code_src3 = ord($str_src[$i++]); 
				if (($code_src3 & 0xC0) != 0x80) 
					continue; 
				$code_src4 = ord($str_src[$i++]); 
				if (($code_src4 & 0xC0) != 0x80) 
					continue; 
				$code_dst = ( ($code_src1 & 0x1F) << 18) + ( ($code_src2 & 0x3F) << 12) + ( ($code_src3 & 0x3F) << 6) + ($code_src4 & 0x3F);
			 } else { 
			 	 continue; 
			 } 
			 
			 if ($code_dst) { 
			 	 if ($code_dst==0x401) { 
			 	 	 $str_dst .= "�"; 
			 	 }elseif ($code_dst==0x451) { 
			 	 	 $str_dst .= "�"; 
			 	 } elseif ( ($code_dst>=0x410) && ($code_dst<=0x44F) ) { 
			 	 	 $str_dst .= chr ($code_dst-848); 
			 	 } else 
			 	 	 $str_dst .= "&#{$code_dst};"; 
			 } 
		} 
	
		return $str_dst; 
	}


//-------------- ����� "�������" -------------------
	function list_all_pages($nrows, $page, $page_size, $sql, $for_link){

		if($page=="" || !$page) $page=1;
		$pageSize = $page_size; //���-�� ������� �� ��������
		$nu=$nrows % $pageSize;
		$pp=$nrows / $pageSize;
		$m=$nu / $pageSize;
		$cel=$pp-$m;
		if($nu!=0) $kolvo_pages = $cel + 1; //�������� ���-�� �������
		else $kolvo_pages=$cel;
		if($page>$kolvo_pages) $page=$kolvo_pages;
		if($page==1) $sSQL = $sql.' LIMIT '.$pageSize;
		else{
			$offset=($page - 1)*$pageSize;
			$sSQL = $sql.' LIMIT '.$offset.', '.$pageSize;
		}
		$nPages = $kolvo_pages;
		$text = '';
		if($nPages > 1){
			$text .= "<center><b><font size=1><SMALL>\n";
			$linka='{_SERVER_NAME_}/{_MAIN_SCRIPT_}?'.$for_link.'&page=';
			if($page != 1) $text .= '<A HREF="'.$linka.($page - 1)."\">�</a>\n";
			else $text .= '�';
			$text .= ' | ';
			
			if($nPages > 10){
				$init_page_max = ($nPages > 3) ? 3 : $nPages;
				for($i=1; $i < $init_page_max+1; $i++){
					$text .= ($i == $page) ? '<b>'.$i.'</b>':'<A HREF="'.$linka.$i.'">'.$i."</a>\n";
					if ($i < $init_page_max){
						$text .= ' | ';
					}
				}
				if($nPages > 3){
					if($page > 1 && $page < $nPages){
						$text .= ($page > 7) ? ' ... ':' | ';
						$init_page_min = ($page > 6) ? $page : 7;
						$init_page_max = ($page < $nPages-6) ? $page : $nPages - 6;
						for($i = $init_page_min-3; $i < $init_page_max+4; $i++){
							$text .= ($i == $page)?'<b>'.$i.'</b>':'<A HREF="'.$linka.$i.'">'.$i."</a>\n";
							if ($i < $init_page_max+3){
								$text .= ' | ';
							}
						}
						$text .= ($page < $nPages-6)?' ... ':' | ';
					}else{
						$text .= ' ... ';
					}
					for($i = $nPages-2; $i < $nPages+1; $i++){
						$text .= ($i == $page)?'<b>'.$i.'</b>':'<A HREF="'.$linka.$i.'">'.$i."</a>\n";
						if($i < $nPages){
							$text .= ' | ';
						}
					}
				}
			}else{
				for($i=1;$i<=$nPages;$i++){
					if($i==$page){
						$text .= $i;
					}else{
						$text .= '<A HREF="'.$linka.$i.'">'.$i."</a>\n";
					}
					$text .= ' | ';
				}
			}
			if($page != $nPages) $text .= '<A HREF="'.$linka.($page + 1)."\">�</a>\n";
			else $text .= '�';
			$text .= '</SMALL></b></font></center>';
		}
		
		return array($sSQL, $text);
	}

?>