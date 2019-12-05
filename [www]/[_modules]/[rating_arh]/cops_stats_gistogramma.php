<?
//	$time
//	$time2;
	require('/home/sites/police/dbconn/dbconn.php');

// Получение глобальных переменных
	while (list ($key, $val) = each ($HTTP_GET_VARS)) $$key = $val;
	while (list ($key, $val) = each ($HTTP_POST_VARS)) $$key = $val;

///////////////////////////////////////////
	if($cid == 'cops'){
		$db_table = 'cops_online';
	}
	
///////////////////////////////////////////
	if(!isset($time)){
		$time = $time2 = time();
	}
	
	if($time>time()){
		$time = time();
	}
	
//	корректировка
	$time = date("d:m:Y:H:i", $time);
	$ctime = $time = explode(':',$time);
	$time = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
	
// Задаем входные данные ############################################
// Входные данные - три ряда, содержащие случайные данные.
// Деление на 2 и 3 взято для того чтобы передние ряды не 
// загораживали задние.

// Массив $DATA["x"] содержит подписи по оси "X"

	$DATA=Array();


// Задаем изменяемые значения #######################################

// Размер изображения
	$W=1200;
	$H=250;

// Псевдо-глубина графика
	$DX=30;
	$DY=20;

// Отступы
	$MB=20; // Нижний
	$ML=10; // Левый 
	$M=5;   // Верхний и правый отступы. Они меньше, так как там нет текста
	
// Кол-во контрольных точек в часу
	$t_point = 4;
	
//======================================
	if(!empty($nicks)){
		$nicks=explode(',', $nicks);
		$tnicks=array();
		foreach($nicks AS $key=>$val){
			if($val!=''){
				$tnicks[]=$val;
			}
		}
		if(sizeof($tnicks)>0){
			if(sizeof($tnicks)>1){
				$nicks = implode('\' OR `id`=\'', $tnicks);
			}else{
				$nicks = $tnicks[0];
			}
			$nicks = '(`id`=\''.$nicks.'\')';
			
			$sSQL2 = 'SELECT `name` FROM `tzpolice_tz_users` WHERE '.$nicks;
			$result2 = mysql_query($sSQL2);
			$nicks = array();
			while($row2 = mysql_fetch_assoc($result2)){
				$nicks[]=$row2['name'];
			}
			if(sizeof($tnicks)>1){
				$nicks = implode('\' OR `nick`=\'', $nicks);
			}else{
				$nicks = $nicks[0];
			}
			$nicks = ' AND (`nick`=\''.$nicks.'\')';
		
		}else{
			$nicks = '';
		}
		
	}
//======================================
	$date = date("Y-m-d", $time);
			
/*	$where_date = "`date` = '".$date."' AND ((login<=".$ttime." AND logout>=".$ttime2.") OR (login>=".$ttime." AND login<=".$ttime2.") OR (logout>=".$ttime." AND logout<=".$ttime2.") OR (logout=0 AND login<=".$ttime."))";
*/	
	$where_date = '`date` = \''.$date.'\'';
	if($nicks!='')
		$where_date = '('.$where_date.')'.$nicks;
//	$sSQL = "SELECT `id`, `nick`, `login`, `logout`, `status` FROM `".$db_table."` WHERE (".$where_date.") GROUP BY `nick`";
	$sSQL = 'SELECT `login`, `logout`, `status` FROM `'.$db_table.'` WHERE ('.$where_date.')';
	$result = mysql_query($sSQL);
	$log = array();
	while($row = mysql_fetch_assoc($result)){
	//	if($row["logout"]==0) $row["logout"]=time();
		$log[] = array('login' => $row['login'], 'logout' => $row['logout'], 'status' => $row['status']);
	}
	
	mysql_close ($db);
//======================================
	
	
	for ($i=0;$i<(24*$t_point);$i++) {
	
	//================
		$i1=floor($i/$t_point);
		$i2=($i%$t_point)*(60/$t_point);
		
		$i2_2=$i2+(60/$t_point)-1;
		
		$ctime2 = date("d:m:Y:H:i", $time);
		$ctime2 = explode(':', $ctime2);
		$ttime = mktime($i1, $i2, 0, $ctime2[1], $ctime2[0], $ctime2[2]);
		$ttime2 = mktime($i1, $i2_2, 59, $ctime2[1], $ctime2[0], $ctime2[2]);
		
		$num=$num2=$num3=0;
		
		if($ttime2<=time()){
			foreach($log AS $key=>$val){
				if(($log[$key]['login']<=$ttime && $log[$key]['logout']>=$ttime2) || ($log[$key]['login']>=$ttime && $log[$key]['login']<=$ttime2) || ($log[$key]['logout']>=$ttime && $log[$key]['logout']<=$ttime2) || ($log[$key]['logout']=='0' && $log[$key]['login']<=$ttime)){
					$num++;
					if($log[$key]['status']=='0' || $log[$key]['status']=='2') $num2++;
					elseif($log[$key]['status']=='1') $num3++;
				}
			
			}
	/*		$where_date = "`date` = '".$date."' AND ((login<=".$ttime." AND logout>=".$ttime2.") OR (login>=".$ttime." AND login<=".$ttime2.") OR (logout>=".$ttime." AND logout<=".$ttime2.") OR (logout=0 AND login<=".$ttime."))";
		
			$sSQL = "SELECT `id` FROM `".$db_table."` WHERE (".$where_date.") GROUP BY `nick`" ;
			$result = mysql_query($sSQL);
			$num=mysql_num_rows($result);
			
			$sSQL = "SELECT `id` FROM `".$db_table."` WHERE (".$where_date.") AND status=0 GROUP BY `nick`" ;
			$result = mysql_query($sSQL);
			$num2=mysql_num_rows($result);
			
			$sSQL = "SELECT `id` FROM `".$db_table."` WHERE (".$where_date.") AND status=1 GROUP BY `nick`" ;
			$result = mysql_query($sSQL);
			$num3=mysql_num_rows($result);
	*/	}
		
	//===============
		$DATA[0][]=$num;
		$DATA[1][]=$num2;
		$DATA[2][]=$num3;
		$DATA['x'][]=$i1.':'.$i2;
	}


// Функция вывода псевдо-трехмерного куба ###########################

// $im - идентификатор изображения
// $x,$y - координаты верхнего левого угла куба
// $w - ширина куба
// $h - высота куба
// $dx - смещение задней грани куба по оси X
// $dy - смещение задней грани куба по оси Y
// $c1,$c2,c3 - цвета видимых граней куба

	function imagebar($im,$x,$y,$w,$h,$dx,$dy,$c1,$c2,$c3) {
		if ($dx>0) {
			imagefilledpolygon($im,
            Array(
                $x, $y-$h,
                $x+$w, $y-$h,
                $x+$w+$dx, $y-$h-$dy,
                $x+$dx, $y-$dy-$h
            ), 4, $c1);
		
			imagefilledpolygon($im,
            Array(
                $x+$w, $y-$h,
                $x+$w, $y,
                $x+$w+$dx, $y-$dy,
                $x+$w+$dx, $y-$dy-$h
            ), 4, $c3);
		}
		
		imagefilledrectangle($im, $x, $y-$h, $x+$w, $y, $c2);
    }

//======================================================
// Ширина одного символа
	$LW=imagefontwidth(2);

// Подсчитаем количество элементов (столбиков) на графике
	$count=count($DATA[0]);
	if (count($DATA[1])>$count) $count=count($DATA[1]);
	if (count($DATA[2])>$count) $count=count($DATA[2]);

// Подсчитаем максимальное значение
	$max=0;
	for ($i=0;$i<$count;$i++) {
		$max=$max<$DATA[0][$i]?$DATA[0][$i]:$max;
		$max=$max<$DATA[1][$i]?$DATA[1][$i]:$max;
		$max=$max<$DATA[2][$i]?$DATA[2][$i]:$max;
	}

// Увеличим максимальное значение на 10% (для того, чтобы столбик
// соответствующий максимальному значение не упирался в в границу
// графика
	$max=intval($max+($max/10));

// Работа с изображением ############################################

// Создадим изображения
	$im=imagecreate($W,$H);

// Задаем основные цвета 

// Цвет фона (белый)
	$bg[0]=imagecolorallocate($im,255,255,255);

// Цвет задней грани графика (светло-серый)
	$bg[1]=imagecolorallocate($im,231,231,231);

// Цвет левой грани графика (серый)
	$bg[2]=imagecolorallocate($im,212,212,212);

// Цвет сетки (серый, темнее)
	$c=imagecolorallocate($im,184,184,184);

// Цвет текста (темно-серый)
	$text=imagecolorallocate($im,136,136,136);

// Цвета для столбиков
	$bar[2][0]=imagecolorallocate($im,255,128,234);
	$bar[2][1]=imagecolorallocate($im,222,95,201);
	$bar[2][2]=imagecolorallocate($im,191,65,170);
	$bar[1][0]=imagecolorallocate($im,222,214,0);
	$bar[1][1]=imagecolorallocate($im,181,187,65);
	$bar[1][2]=imagecolorallocate($im,161,155,0);
	$bar[0][0]=imagecolorallocate($im,128,234,255);
	$bar[0][1]=imagecolorallocate($im,95,201,222);
	$bar[0][2]=imagecolorallocate($im,65,170,191);

// Количество подписей и горизонтальных линий
// сетки по оси Y.
	$county=10;

// Подравняем левую границу с учетом ширины подписей по оси Y
	$text_width=strlen($max)*$LW;
	$ML+=$text_width;

// Вывод фона графика
	imageline($im, $ML, $M+$DY, $ML, $H-$MB, $c);
	imageline($im, $ML, $M+$DY, $ML+$DX, $M, $c);
	imageline($im, $ML, $H-$MB, $ML+$DX, $H-$MB-$DY, $c);
	imageline($im, $ML, $H-$MB, $W-$M-$DX, $H-$MB, $c);
	imageline($im, $W-$M-$DX, $H-$MB, $W-$M, $H-$MB-$DY, $c);
	
	imagefilledrectangle($im, $ML+$DX, $M, $W-$M, $H-$MB-$DY, $bg[1]);
	imagerectangle($im, $ML+$DX, $M, $W-$M, $H-$MB-$DY, $c);
	
	imagefill($im, $ML+1, $H/2, $bg[2]);
	
// Вывод неизменяемой сетки (горизонтальные линии на
// нижней грани и вертикальные линии сетки на левой
// грани
	for ($i=1;$i<3;$i++) {
//	for ($i=1;$i<1;$i++) {
		imageline($im, $ML+$i*intval($DX/3),
                   $M+$DY-$i*intval($DY/3),
                   $ML+$i*intval($DX/3),
                   $H-$MB-$i*intval($DY/3),
                   $c);
		imageline($im, $ML+$i*intval($DX/3),
                   $H-$MB-$i*intval($DY/3),
                   $W-$M-$DX+$i*intval($DX/3),
                   $H-$MB-$i*intval($DY/3),
                   $c);
	}

// Пересчитаем размеры графика с учетом подписей и отступов
	$RW=$W-$ML-$M-$DX;
	$RH=$H-$MB-$M-$DY;

// Координаты нулевой точки графика
	$X0=$ML+$DX;
	$Y0=$H-$MB-$DY;

// Вывод изменяемой сетки (вертикальные линии сетки на нижней грани графика
// и вертикальные линии на задней грани графика)
	for ($i=0;$i<$count;$i++) {
		imageline($im,$X0+$i*($RW/$count),$Y0,$X0+$i*($RW/$count)-$DX,$Y0+$DY,$c);
		imageline($im,$X0+$i*($RW/$count),$Y0,$X0+$i*($RW/$count),$Y0-$RH,$c);
	}

// Горизонтальные линии сетки задней и левой граней.
	$step=$RH/$county;
	for ($i=0;$i<=$county;$i++) {
		imageline($im, $X0, $Y0-$step*$i, $X0+$RW, $Y0-$step*$i,$c);
		imageline($im, $X0, $Y0-$step*$i, $X0-$DX, $Y0-$step*$i+$DY,$c);
		imageline($im, $X0-$DX, $Y0-$step*$i+$DY, $X0-$DX-($ML-$text_width)/4,$Y0-$step*$i+$DY,$text);
	}

// Вывод кубов для всех трех рядов
	for ($i=0;$i<$count;$i++) 
		imagebar($im, $X0+$i*($RW/$count)+4-1*intval($DX/3),
                  $Y0+1*intval($DY/3),
                  intval($RW/$count)-4,
                  $RH/$max*$DATA[0][$i],
                  intval($DX/3)-5,
                  intval($DY/3)-3,
                  $bar[0][0], $bar[0][1], $bar[0][2]);

	for ($i=0;$i<$count;$i++)
		imagebar($im, $X0+$i*($RW/$count)+4-2*intval($DX/3),
                  $Y0+2*intval($DY/3),
                  intval($RW/$count)-4,
                  $RH/$max*$DATA[1][$i],
                  intval($DX/3)-5,
                  intval($DY/3)-3,
                  $bar[1][0], $bar[1][1], $bar[1][2]);

	for ($i=0;$i<$count;$i++) 
		imagebar($im, $X0+$i*($RW/$count)+4-3*intval($DX/3), 
                  $Y0+3*intval($DY/3),
                  intval($RW/$count)-4,
                  $RH/$max*$DATA[2][$i],
                  intval($DX/3)-5,
                  intval($DY/3)-3,
                  $bar[2][0], $bar[2][1], $bar[2][2]);



// Вывод подписей по оси Y
	for ($i=1;$i<=$county;$i++) {
    	$str=intval(($max/$county)*$i);
    	imagestring($im,2, $X0-$DX-strlen($str)*$LW-$ML/4-2,
                       $Y0+$DY-$step*$i-imagefontheight(2)/2,
                       $str,$text);
	}
	
	$str='Count';
	imagestring($im,2, 3, 3, $str, $text);

	
// Вывод подписей по оси X
	$prev=100000;
	$twidth=$LW*strlen($DATA['x'][0])+6;
	$i=$X0+$RW-$DX;

	$str='Hours';
	$drawx=$i+1-($RW/$count)/2;
	imagestring($im,2, ($drawx+1-(strlen($str)*$LW)/2)+26, $Y0+$DY, $str,$text);

	while ($i>$X0-$DX) {
		if ($prev-$twidth>$i) {
			$drawx=$i+1-($RW/$count)/2;
			if ($drawx>$X0-$DX) {
				$str=$DATA['x'][round(($i-$X0+$DX)/($RW/$count))-1];
				imageline($im,$drawx,$Y0+$DY,$i+1-($RW/$count)/2,$Y0+$DY+5,$text);
				imagestring($im,2, $drawx+1-(strlen($str)*$LW)/2 ,$Y0+$DY+7,$str,$text);
			}
			$prev=$i;
		}
		$i-=$RW/$count;
	}
	

	
	header("Content-Type: image/png");

// Генерация изображения
	ImagePNG($im);
	
	imagedestroy($im);

?>
