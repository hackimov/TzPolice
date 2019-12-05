<html><head>
<?include("../header.php")?>
<?include "../java.php"?>
</head>
<body bgcolor=#F6F3E9>
<script>
function AddClang(smile) {
        insertAtCaret(top.document.getElementById('NewsText'), '[clan='+smile+']')
}

</script>
<?
error_reporting(0);
$smiles_dir="../../_imgs/clans/";
$d=opendir($smiles_dir);
$counter = 0;
while(($CurFile=readdir($d))!==false) if(is_file("$smiles_dir/$CurFile")) {
	$FileType=GetImageSize("$smiles_dir/$CurFile");
	$CurFile=explode(".",$CurFile);
    if($FileType[2]==1)
    	{
			$clans[$counter] = $CurFile[0];
            $counter++;
        }
}
closedir($d);
natcasesort($clans);
reset($clans);
while (list($key, $val) = each($clans))
	{
         if ($val !== "0")
         	{
			echo ("&nbsp;&nbsp;&nbsp;<a href=\"JavaScript:AddClang('".$val."')\">".$val."</a><br>");
            	}
	}
?>
</body>
</html>