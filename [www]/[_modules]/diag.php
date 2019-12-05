<?php

// $im - идентификатор изображения
// $VALUES - массив со значениями
// $LEGEND - массив с подписями
function win2uni($s) {
    $s = convert_cyr_string($s, 'w', 'i'); 
    for ($result='', $i=0; $i<strlen($s); $i++) {$charcode = ord($s[$i]);$result .= ($charcode>175)?"&#".(1040+($charcode-176)).";":$s[$i];}
    return $result;
}


function Diagramm($im,$VALUES,$LEGEND) {
    GLOBAL $COLORS,$SHADOWS;

    $black=ImageColorAllocate($im,0,0,0);
//	imagettftext($im, 10, 0, 30, 20, $black, "/pub/home/adv73/htdocs/verdana.ttf", win2uni($_GET['text']));
    // Получим размеры изображения
    $W=ImageSX($im);
    $H=ImageSY($im);

    // Вывод легенды #####################################

    // Посчитаем количество пунктов, от этого зависит высота легенды
    $legend_count=count($LEGEND);

    // Посчитаем максимальную длину пункта, от этого зависит ширина легенды
    $max_length=0;
    foreach($LEGEND as $v) if ($max_length<strlen($v)) $max_length=strlen($v);

    // Номер шрифта, котором мы будем выводить легенду
    $FONT=2;
    $font_w=ImageFontWidth($FONT);
    $font_h=ImageFontHeight($FONT);

    // Вывод прямоугольника - границы легенды ----------------------------

    $l_width=($font_w*$max_length)+$font_h+10+5+10;
    $l_height=$font_h*$legend_count+10+10;


    // Получим координаты верхнего левого угла прямоугольника - границы легенды
    $l_x1=$W-10-$l_width;
    $l_y1=($H-$l_height)/2;

    // Выводя прямоугольника - границы легенды
    ImageRectangle($im, $l_x1, $l_y1, $l_x1+$l_width, $l_y1+$l_height, $black);

    // Вывод текст легенды и цветных квадратиков
    $text_x=$l_x1+10+5+$font_h;
    $square_x=$l_x1+10;
    $y=$l_y1+10;

    $i=0;
    foreach($LEGEND as $v) {
        $dy=$y+($i*$font_h);
        ImageString($im, $FONT, $text_x, $dy, $v, $black);
        ImageFilledRectangle($im,
                             $square_x+1,$dy+1,$square_x+$font_h-1,$dy+$font_h-1,
                             $COLORS[$i]);
        ImageRectangle($im,
                       $square_x+1,$dy+1,$square_x+$font_h-1,$dy+$font_h-1,
                       $black);
        $i++;
        }

    // Вывод круговой диаграммы ----------------------------------------
    $total=array_sum($VALUES);
    $anglesum=$angle=Array(0);
    $i=1;

    // Расчет углов
    while ($i<count($VALUES)) {
        $part=$VALUES[$i-1]/$total;
        $angle[$i]=floor($part*360);
        $anglesum[$i]=array_sum($angle);
        $i++;
        }
    $anglesum[]=$anglesum[0];

    // Расчет диаметра
    $diametr=$l_x1-10-10;

    // Расчет координат центра эллипса
    $circle_x=($diametr/2)+10;
    $circle_y=$H/2-10;

    // Поправка диаметра, если эллипс не помещается по высоте
    if ($diametr>($H*2)-10-10) $diametr=($H*2)-20-20-40;

    // Вывод тени
    for ($j=20;$j>0;$j--)
        for ($i=0;$i<count($anglesum)-1;$i++)
            ImageFilledArc($im,$circle_x,$circle_y+$j,
                               $diametr,$diametr/2,
                               $anglesum[$i],$anglesum[$i+1],
                               $SHADOWS[$i],IMG_ARC_PIE);

    // Вывод круговой диаграммы
    for ($i=0;$i<count($anglesum)-1;$i++)
        ImageFilledArc($im,$circle_x,$circle_y,
                           $diametr,$diametr/2,
                           $anglesum[$i],$anglesum[$i+1],
                           $COLORS[$i],IMG_ARC_PIE);
    }


$VALUES=explode(",",$_GET['data']);
/*
$an=4000;
$an1=2480;
$an2=1570;
$an3=9000;
$an4=5000;
$an5=4700;
$an6=1000;
// Зададим значение и подписи
$VALUES=Array($an,$an1,$an2,$an3,$an4,$an5,$an6);*/
$LEGEND=Array("Metal - ".$VALUES[0],"Gold - ".$VALUES[1],"Polimers - ".$VALUES[2],"Organic - ".$VALUES[3],"Silicon - ".$VALUES[4],"Radik - ".$VALUES[5],"Gems - ".$VALUES[6],"Venom - ".$VALUES[7]);

// Создадим изображения
header("Content-Type: image/png");
$im=ImageCreate(380,150);

// Зададим цвет фона.
$bgcolor=ImageColorAllocate($im,255,255,255);

// Зададим цвета элементов
$COLORS[0] = imagecolorallocate($im, 197, 1, 1);
$COLORS[1] = imagecolorallocate($im, 238, 200, 31);
$COLORS[2] = imagecolorallocate($im, 0, 122, 0);
$COLORS[3] = imagecolorallocate($im, 93, 21, 21);
$COLORS[4] = imagecolorallocate($im, 180, 180, 180);
$COLORS[5] = imagecolorallocate($im, 101, 101, 101);
$COLORS[6] = imagecolorallocate($im, 0, 73, 255);
$COLORS[7] = imagecolorallocate($im, 64, 191, 191);

// Зададим цвета теней элементов
$SHADOWS[0] = imagecolorallocate($im, 141, 2, 2);
$SHADOWS[1] = imagecolorallocate($im, 196, 165, 27);
$SHADOWS[2] = imagecolorallocate($im, 2, 84, 2);
$SHADOWS[3] = imagecolorallocate($im, 65, 15, 15);
$SHADOWS[4] = imagecolorallocate($im, 120, 120, 120);
$SHADOWS[5] = imagecolorallocate($im, 56, 56, 56);
$SHADOWS[6] = imagecolorallocate($im, 2, 45, 151);
$SHADOWS[7] = imagecolorallocate($im, 47, 138, 138);



// Вызов функции рисования диаграммы
Diagramm($im,$VALUES,$LEGEND);

// Генерация изображения
ImagePNG($im)
?>
