<html>
<head>
<?include "../java.php"?>
</head>
<body bgcolor=#F6F3E9>

<?php
$alt['i0'] = "Без профессии";
$alt['i1'] = "Корсар";
$alt['i2'] = "Сталкер";
$alt['i3'] = "Старатель";
$alt['i4'] = "Инженер";
$alt['i5'] = "Наемник";
$alt['i6'] = "Торговец";
$alt['i7'] = "Полицейский.Патрульный";
$alt['i8'] = "Полицейский.Штурмовик";
$alt['i9'] = "Полицейский.Специалист";
$alt['i10'] = "Фигня какая-то...";
$alt['i11'] = "Чиновник";
$alt['i12'] = "Псионик";$alt['i13'] = "Каторжник";
$alt['i30'] = "Дилер";
$smiles_dir="../../_imgs/pro/";
$d=opendir($smiles_dir);
while(($CurFile=readdir($d))!==false) if(is_file("$smiles_dir/$CurFile")) {
	$FileType=GetImageSize("$smiles_dir/$CurFile");
	$CurFile=explode(".",$CurFile);
    if($FileType[2]==1) echo "<a href=\"JavaScript:AddProf('".$CurFile[0]."')\"><img ALT='".$alt[$CurFile[0]]."' border=0 src=\"/_imgs/pro/".$CurFile[0].".gif\"></a> ";
}

closedir($d);
?>

</body>
</html>