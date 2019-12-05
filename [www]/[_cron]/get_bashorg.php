<?
	require('/home/sites/police/www/_modules/parse_rss.php');

///////////////////////////////
	function utf8_to_win1251 ($str_src) {
		$str_dst = '';
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
			 	 	 $str_dst .= 'Ё'; 
			 	 }elseif ($code_dst==0x451) { 
			 	 	 $str_dst .= 'ё'; 
			 	 } elseif ( ($code_dst>=0x410) && ($code_dst<=0x44F) ) { 
			 	 	 $str_dst .= chr ($code_dst-848); 
			 	 } else 
			 	 	 $str_dst .= "&#{$code_dst};"; 
			 } 
		} 
	
		return $str_dst; 
	}
	
	function shuffle_assoc(&$array) {
		if (count($array)>1) { //$keys needs to be an array, no need to shuffle 1 item anyway
			$keys = array_rand($array, count($array));
			foreach($keys as $key)
				$new[$key] = $array[$key];
			$array = $new;
		}
		return true; //because it's a wannabe shuffle(), which returns true
	}
	
	function unhtmlentities ($string) {
		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
		return strtr ($string, $trans_tbl);
	}

///////////////////////////////////////////////	
	
	
	$feed = 'http://bash.org.ru/rss/';
	$max_items = 50;
	$rss = new mod_rss();
	
//	// for direcet output
//	$direct_output = true;
//	$rss->parser($feed, $max_items, $direct_output);

	// for array output
	$direct_output = false;
	$rss_array = $rss->parser($feed, $max_items, $direct_output);
	
	foreach($rss_array AS $i=>$val){
		$rss_array[$i] = unhtmlentities(utf8_to_win1251($rss_array[$i]['description']));
	}
	
	shuffle_assoc($rss_array);
	
	$i=0;
	foreach ($rss_array AS $key=>$val) {
		$val = str_replace('<br>', "\n", trim($val));
		$val = explode("\n", $val);
		foreach ($val AS $a=>$v) {
			$v = trim($v);
			if (strlen($v)>220) {
				$val[$a] = wordwrap( $v, 220, "\n", 1);
			}
		}
		$val = implode("\n", $val);
		
		if(strlen($i)==1){
			$n = '0'.$i;
		}else{
			$n = $i;
		}
		$filename = '/home/sites/police/bot_pa/bash/bash'.$n.'.txt';
		
		if (file_exists($filename)){
	//		echo "file_exists";
			chmod($filename, 0777);
		}
		
	// Открываем $filename в режиме "дописать в конец".
		if ($handle = fopen($filename, 'w')) {
		// Записываем в открытый файл.
			if (fwrite($handle, $val) === FALSE) {
//				$text = "Не возможно произвести запись в файл (".$filename.")";
//				$noerror=0;
			}
	//		$text = "Записали (".$message.") в файл (".$filename.")";
			fclose($handle);
		}else{
//			$text = "Не могу открыть файл (".$filename.")";
//			$noerror=0;
		}
		$i++;
		if($i>23){
			break;
		}
	}
//	print_r($rss_array);
	
?>