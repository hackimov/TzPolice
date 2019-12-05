<html><head>
<title>.:: Smiles ::.</title>
<script src="../news_js.js"></script>
</head>
<body bgcolor=#F6F3E9>
<?php
$smiles_dir="../../_imgs/smiles/";
$d=opendir($smiles_dir);
while(($CurFile=readdir($d))!==false) if(is_file("$smiles_dir/$CurFile")) {
	$FileType=GetImageSize("$smiles_dir/$CurFile");
	$CurFile=explode(".",$CurFile);
    if($FileType[2]==1) echo "<a href=\"JavaScript:AddSmile2('".$CurFile[0]."')\"><img border=0 src=\"/_imgs/smiles/".$CurFile[0].".gif\"></a> ";
}
?>
</body>
</html>
