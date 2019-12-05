<?php
error_reporting(0);
$location[0] = "i/fotos/";
$location[1] = "http://police.timezero.ru/upload/gallery/";
    $host  = $_SERVER['HTTP_HOST'];
    $pic = $location[$_GET['k']].$_GET['s'];
    $img_info = getimagesize($pic);
	if (!$img_info) {
    header("Location: http://$host");
    exit;
	}else{
    header('content-type: image/jpeg');
    $watermark = imagecreatefrompng('../img/watermark.png');
    $watermark_width = imagesx($watermark);
    $watermark_height = imagesy($watermark);
    $image = imagecreatetruecolor($watermark_width, $watermark_height);
    $image = imagecreatefromjpeg($_GET['src']);
    $size = getimagesize($_GET['src']);
    $dest_x = $size[0] - $watermark_width - 5;
    $dest_y = $size[1] - $watermark_height - 5;
    imagecopyresampled($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, $watermark_width, $watermark_height);
    imagejpeg($image, $dest_file, $dest_qual=50);
    imagedestroy($image);
    imagedestroy($watermark);
    }

?>