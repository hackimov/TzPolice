<?php
	
	$our_quote = 4*1024*1024*1024;
	
	function bytes4view($v, $divisor = 1024, $l = 0) {
		$label = array(' КБ', ' МБ', ' ГБ', ' ТБ', ' ПБ');
		$precision = 2;
		
		$val = $v / $divisor;
		if (floor($val) >= $divisor) {
			$l++;
			$val = bytes4view(floor($val), $divisor, &$l);
		}
		
		return round($val, $precision).$label[$l];
	}
	
	define('DIRECTORY_SEPARATOR', '/');
	function dirsize($dirname) {
		if (!is_dir($dirname) || !is_readable($dirname)) {
			return false;
		}
		
		$dirname_stack[] = $dirname;
		$size = 0;
		
		do {
			$dirname = array_shift($dirname_stack);
			$handle = opendir($dirname);
			while (false !== ($file = readdir($handle))) {
				if ($file != '.' && $file != '..' && is_readable($dirname . DIRECTORY_SEPARATOR . $file)) {
					if (is_dir($dirname . DIRECTORY_SEPARATOR . $file)) {
						$dirname_stack[] = $dirname . DIRECTORY_SEPARATOR . $file;
					}
					$size += filesize($dirname . DIRECTORY_SEPARATOR . $file);
				}
			}
			
			closedir($handle);
		} while (count($dirname_stack) > 0);
		
		return $size;
	}

//	echo bytes4view(disk_free_space("/")).'/'.bytes4view(disk_total_space("/"));;
	$ds = dirsize("/home/sites/police");
	echo '<BR>'.bytes4view($ds).'/'.bytes4view($our_quote);
	echo '<BR>'.$ds.'/'.$our_quote;
	$free = $our_quote-$ds;
	echo '<BR>Свободно: '.bytes4view($free);
	if($free<300000000){
		$message = 'Свободно: '.bytes4view($free);
	
		$headers = "From: TZPOLICE.RU <deadbeef@tzpolice.ru>\r\n";
	//	$headers .= "MIME-Version: 1.0\r\n";
	//	$headers .= "Content-Type: text/html; charset=windows-1251\r\n";
		$headers .= "Content-Type: text/plain\n";
		$headers .= "X-Mailer: TZPOLICE.RU\n";
		$headers .= "X-Priority: 3\n";
	//	$headers .= "Return-Path: <fantastish2001@mail.ru>\r\n";
	//	$headers .= "Content-Transfer-Encoding: 8bit\r\n";
			
		$subj = date("H:i d/m/Y", time()).': У сайта TZPOLICE осталось менее 300мб свободного места на диске';
	//	$subj = '=?koi8-r?B?'.base64_encode(convert_cyr_string($subj, "w","k")).'?=';

		mail("fantastish2001@mail.ru", $subj, $message, $headers);
		mail("stealth@timezero.ru", $subj, $message, $headers);
		mail("tz.deadbeef@gmail.com", $subj, $message, $headers);
	
//	mail("fantastish2001@mail.ru", $message, $message);
	//	echo 'мала';
	}
//	else{
//		echo 'много';
//	}
//	echo 'Свободно: '.bytes4view($free);

	
?>