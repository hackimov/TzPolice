<?php

require_once('functions.php');
require_once('auth.php');

if (isset($_REQUEST['json_data'])) {

	show_json();

} else {
	
	show_page();
	
}


function show_json() {
	
	if (isset($_REQUEST['start'])) {
		
		$date = explode("-",$_POST['start']);
		$start = mktime(0, 0, 0, $date[1], $date[0], $date[2]);
		
	} else {	
		//$start = 0;
		$start= mktime(0, 0, 0, date("m", time()), date("d", time()), date("y", time()));		
	}

	if (isset($_REQUEST['finish'])) {

		$date = explode("-",$_POST['finish']);
		$finish = mktime(23, 59, 59, $date[1], $date[0], $date[2]);	

	} else {	
		//$finish = 9999999999;
		$finish = mktime(23, 59, 59, date("m", time()), date("d", time()), date("y", time()));		
	}
	
	$result = mysql_query("SELECT r.site_id AS site_id, n.url AS name, SUM(r.counter) AS cnt FROM tzpolice_siterating_screen AS r LEFT JOIN tzpolice_siterating_site AS n ON (n.id = r.site_id) WHERE r.time >= $start AND r.time <= $finish GROUP BY site_id, name ORDER BY cnt DESC");
	
	$resarr = array();
	
	// определим максимальную точку времени
	$resmax = mysql_query("SELECT MAX(time) AS mt FROM tzpolice_siterating_screen WHERE time >= $start AND time <= $finish");
	
	if ($row = mysql_fetch_array($resmax)) {
		
		$max_ts = $row['mt']+10800;
		
	} else {
		
		die();
		
	}
	
	while ($row = mysql_fetch_array($result)) {   // перебор по сайтам
		
		$s_result = mysql_query("SELECT time, counter FROM tzpolice_siterating_screen WHERE site_id = '".$row['site_id']."' AND time >= $start AND time <= $finish ORDER BY time ASC");
		
		$data = array();
		
		$last_count = -1;
		$last_day = 0;
		$last_ts = 0;
		
		while ($s_row = mysql_fetch_array($s_result)) {     // выборка значений по 1 сайту.
			
			$s_data = array();
			
			$s_data[0] = $s_row['time'];
			$s_data[1] = $s_row['counter']+1-1;
			
			$t = date('H-i-d', $s_data[0]);
			$t = explode('-', $t);
			
			// определяем начало новых суток
			if ($t[2] != $last_day) {
				
				$new_day = true;
				$last_day = $t[2];
			
			} else {
				
				$new_day = false;
				
			}
			
				
			if ($new_day) {    // первая запись в дне, все варианты (начало дня, середина дня, были в прошлых дня, не было в прошлых днях)
				
				$start_day= mktime(0, 0, 0, date("m", $s_data[0]), date("d", $s_data[0]), date("y", $s_data[0])); // начало текущего дня
				$finish_day= mktime(23, 59, 0, date("m", $last_ts), date("d", $last_ts), date("y", $last_ts)); // конец дня последней точки
			
				if ($t[1] == 0 && $t[0] == 0) {  // начало дня
					
					if ($last_count == -1) {  // начало дня, раньше не было. сброс не нужен.
						
						// ничего не нужно, само поставит нули на начало дня
						
					} else {  // начало дня, было раньше. нужна фиксация на 23.59 дня последней точки и обнуление начала следующего за ним дня
						
						$st_data = array();
						$st_data[0] = ($finish_day + 10800) * 1000;
						$st_data[1] = $last_count;
						array_push($data, $st_data);
						
						if (($finish_day + 60) != $start_day) {    // промежуток более суток
							
							$st_data = array();
							$st_data[0] = ($finish_day + 60 + 10800) * 1000;
							$st_data[1] = 0;
							array_push($data, $st_data);
							
						}
						
					}
					
				} else {    // сайт в десятке не с начала дня
					
					if ($last_count == -1) {  // середина дня, раньше не было.
						
						$st_data = array();
						$st_data[0] = ($start_day + 10800) * 1000;
						$st_data[1] = 0;
						array_push($data, $st_data);
						
					} else {  // середина дня, было ранее. фиксация на 23.59 дня последней точки, обнуление на начало следующего дня, установка нуля на начало текущего дня
					
						$st_data = array();
						$st_data[0] = ($finish_day + 10800) * 1000;
						$st_data[1] = $last_count;
						array_push($data, $st_data);
						
						$st_data = array();
						$st_data[0] = ($finish_day + 60 + 10800) * 1000;
						$st_data[1] = 0;
						array_push($data, $st_data);
						
						$st_data = array();
						$st_data[0] = ($start_day + 10800) * 1000;
						$st_data[1] = 0;
						array_push($data, $st_data);
						
					}
						
				}
				
			}
			
			$last_ts = $s_data[0];
			
			if ($s_data[1] != $last_count) {
				$s_data[0] = ($s_data[0] + 10800) * 1000;
				array_push($data, $s_data);
				$last_count = $s_data[1];
			}
			
		}
		
		// так как точка не пишеnся если не изменилась - дописываем последнюю точку если ее нет, и ноль.
		
		if ($last_ts != $max_ts) {
			
			$finish_day = mktime(23, 59, 0, date("m", $last_ts), date("d", $last_ts), date("y", $last_ts)); //  получаем конец дня последней записи
			
			$finish_day = ($finish_day > ($max_ts - 10800)?($max_ts - 10800):$finish_day); // выбираем дату фиксации
			
			$st_data = array();
			$st_data[0] = ($finish_day + 10800)* 1000;
			$st_data[1] = $last_count;
			array_push($data, $st_data);
			
			if ($finish_day > $max_ts) { // если макс.значение больше даты фиксации  - обнуляем.
				
				$finish_day= mktime(23, 59, 59, date("m", $last_ts), date("d", $last_ts), date("y", $last_ts))+1;
				$st_data = array();
				$st_data[0] = ($finish_day + 10800) * 1000;
				$st_data[1] = 0;
				array_push($data, $st_data);
				
			}
			
		}
		
		
		
		$arr = array();
		$label = str_replace("https://www.","", $row['name']);
		$label = str_replace("http://www.","", $label);
		$label = str_replace("https://","", $label);
		$label = str_replace("http://","", $label);
		// находим первый / и отпиливаем все что после него
		$pos = strpos($label, "/");
		if ($pos) $label = substr($label, 0, $pos );
		
		$arr['label'] = $label;
		$arr['data'] = $data;
		
		$resarr[str_replace(".","", $label)] = $arr;
		
	}
	
	echo json_encode($resarr);
	
}

function show_page() {
	
	// ext
	echo("<SCRIPT language='javascript' type='text/javascript' src='/scripts/jquery.flot.min.js'></SCRIPT>");
	echo("<SCRIPT language='javascript' type='text/javascript' src='/scripts/jquery.flot.time.min.js'></SCRIPT>");
	echo("<SCRIPT language='javascript' type='text/javascript' src='/scripts/jquery.flot.crosshair.min.js'></SCRIPT>");
	echo("<!--[if lte IE 8]><script language='javascript' type='text/javascript' src='/scripts/excanvas.min.js'></script><![endif]-->");
	
	echo("<SCRIPT language='javascript' type='text/javascript' src='/scripts/tzflot.js'></SCRIPT>");
	
	
	echo "
		<style>
			#placeholder {
				width: 78%;
				height: 450px;
				background: #000000;
				border: 3px solid #FFD47F;
				border-radius: 10px;
				padding: 7px;
				float:left;
			}
			
			#sett {
				width: 18%;
				height: 436px;
				background: #FFFFFF;
				border: 3px solid #FFD47F;
				border-radius: 10px;
				padding: 7px;
				float:left;
			}
		</style>
	";
	
	echo("<div id='placeholder'></div></div><div id='sett'><div id='choices'></div>");
	
	echo("<div class='man'><hr>������ �� ������ �: <input class='calendar' value='".date("d-m-Y", time())."' id='start' size='20' />
		��: <input class='calendar' value='".date("d-m-Y", time())."' id='finish' size='20' /><input name='site_rating' type='submit' value='�������� �������' id='loaddata' /></div>");
	
		
	echo("</div>");	
	
	
}

?>