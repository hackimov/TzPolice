<?php
require("../functions.php");
require("../auth.php");
?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<head>
  <title>TimeZero</title>
<?include("../header.php")?>
<?include("../java.php")?>
</head>

<body bgcolor=#F6F3E9>
<?php

if(AuthStatus==1 && AuthUserId>0) {


@mkdir("../../user_data",0777);
@mkdir("../../user_data/".date("Y-m-d"),0777);
$folder="../../user_data/".date("Y-m-d")."/".AuthUserId;
@mkdir($folder, 0777);
@mkdir("{$folder}/thumb", 0777);

#
# Uploading user's file

//echo "starting... ";

if(is_file($_FILES['thumbnail']['tmp_name'])) {

//echo "got file... ";

	$info=GetImageSize($_FILES['thumbnail']['tmp_name']);
	switch($info[2]) {
    	case 1: $type="gif"; break;
        case 2: $type="jpg"; break;
        case 3: $type="png"; break;
        default: $type="error";
	}

//echo "$type";

    if($type=="error") echo "<h2>Допустимы только изображения JPG, GIF и PNG</h2>";
    else {

    	$base_name=time();
	echo $info[0].":".$info[1]."<br />";
        if($info[0]>800 || $info[1]>600) MakePreview($_FILES['thumbnail']['tmp_name'],$folder, 800, 600, $type, $base_name);
        else copy($_FILES['thumbnail']['tmp_name'],"{$folder}/{$base_name}.{$type}");

	    if($info[0]>200 || $info[1]>150) MakePreview($_FILES['thumbnail']['tmp_name'],"{$folder}/thumb", 200, 150, $type, $base_name);
	    else {
	    echo "copying thumb";
	    copy($_FILES['thumbnail']['tmp_name'],"{$folder}/thumb/{$base_name}.{$type}");
	    }
		unlink($_FILES['thumbnail']['tmp_name']);

    }

}

#
# printing a list of uploaded pics:

$UserThumbDir="{$folder}/thumb";
echo "<div align=center>Картинки, загруженные вами на сервер сегодня:</div><table border=0 cellpadding=4 cellspacing=5 width=100%>";
$d=opendir($UserThumbDir);
while(($CurFile=readdir($d))!==false) if(@is_file("$UserThumbDir/$CurFile")) {
	$FileType=GetImageSize("$UserThumbDir/$CurFile");
	$CurFile=explode(".",$CurFile);
    if(($FileType[2]==1 || $FileType[2]==2 || $FileType[2]==3) && is_file("$folder/{$CurFile[0]}.{$CurFile[1]}")) {
		$FileType2=GetImageSize("$folder/{$CurFile[0]}.{$CurFile[1]}");
    	echo "
        	<tr><td bgcolor='F0F0F0'>
        	<br><div align=center>
            <img border=0 src=\"$UserThumbDir/".$CurFile[0].".".$CurFile[1]."\">
            </div>
            <div style='font-family:verdana;font-size:9px'>
            реальный размер: ".$FileType2[0]."x".$FileType2[1]." <br>
            <a href=\"JavaScript:AddImgFull('".date("Y-m-d")."/".AuthUserId."/".$CurFile[0].".".$CurFile[1]."')\">&laquo; вставить в полный размер</a>
			<br>
            <a href=\"JavaScript:AddImgPrev('".date("Y-m-d")."/".AuthUserId.";".$CurFile[0].".".$CurFile[1].";".$FileType2[0].";".$FileType2[1]."')\">&laquo; вставить с предпросмотром</a>

            </div>
            </td></tr>
        ";
    }
}
echo "</table>";


?>

<form method='post' enctype='multipart/form-data'>
<div>&nbsp;&nbsp;Загрузить (JPG,PNG,GIF &lt; 2Mb)</div>
&nbsp;&nbsp;<input style='width:95%' class='input' name='thumbnail' type='file'>
<br><br>
<div align=center><input type="submit" value="UpLoad"></div>
<br>
</form>



<?}?>