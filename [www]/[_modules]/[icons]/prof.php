<html>
<head>
<?include "../java.php"?>
</head>
<body bgcolor=#F6F3E9>

<?php
$alt['i0'] = "��� ���������";
$alt['i1'] = "������";
$alt['i2'] = "�������";
$alt['i3'] = "���������";
$alt['i4'] = "�������";
$alt['i5'] = "�������";
$alt['i6'] = "��������";
$alt['i7'] = "�����������.����������";
$alt['i8'] = "�����������.���������";
$alt['i9'] = "�����������.����������";
$alt['i10'] = "����� �����-��...";
$alt['i11'] = "��������";
$alt['i12'] = "�������";$alt['i13'] = "���������";
$alt['i30'] = "�����";
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