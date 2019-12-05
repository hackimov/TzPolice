<?php
// Определяем полный путь к ROOT'у сервера [вырезаем возможный "/" из конца строки]:
	$DOCUMENT_ROOT = ereg_replace ('/$', '', $HTTP_SERVER_VARS['DOCUMENT_ROOT']);
	
require($DOCUMENT_ROOT.'/_modules/functions.php');
require($DOCUMENT_ROOT.'/_modules/auth.php');
?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<head>
  <title>.:: Pictures ::.</title>
<?php
include($DOCUMENT_ROOT.'/_modules/header.php');
include($DOCUMENT_ROOT.'/_modules/java.php');
?>
<script>
function AddImgFull2Left(name) {
        insertAtCaret(window.opener.document.getElementById('NewsText'), '[imgleft]'+name+'[/img] ')
}
function AddImgPrev2l(name) {
        insertAtCaret(window.opener.document.getElementById('NewsText'), '[imgprevleft]'+name+'[/imgprev] ')
}
</script>
</head>
<body bgcolor=#F6F3E9>
<?php
	if(AuthStatus==1 && AuthUserId>0) {
	//	mkdir($DOCUMENT_ROOT."/user_data",0777);
		mkdir($DOCUMENT_ROOT.'/user_data');
		chmod($DOCUMENT_ROOT.'/user_data',0777);
		//mkdir($DOCUMENT_ROOT."/user_data/".date("Y-m-d"),0777);
		mkdir($DOCUMENT_ROOT.'/user_data/'.date('Y-m-d'));
		chgrp($DOCUMENT_ROOT.'/user_data/'.date('Y-m-d'), 'police');
		chmod($DOCUMENT_ROOT.'/user_data/'.date('Y-m-d'), 0777);
		$folder = $DOCUMENT_ROOT.'/user_data/'.date('Y-m-d').'/'.AuthUserId;
		$folder2 = '/user_data/'.date('Y-m-d').'/'.AuthUserId;
		//mkdir($folder, 0777);
		mkdir($folder);
		chgrp($folder, 'police');
		chmod($folder, 0777);
		//mkdir("{$folder}/thumb", 0777);
		mkdir($folder.'/thumb');
		chgrp($folder.'/thumb', 'police');
		chmod($folder.'/thumb',0777);
	#
	# Uploading user's file
		error_reporting(0);
	//	echo "starting... ";
		if(is_file($_FILES['thumbnail']['tmp_name'])) {
	//	echo "got file... ";
			$info=GetImageSize($_FILES['thumbnail']['tmp_name']);
			switch($info[2]) {
				case 1: $type='gif'; break;
				case 2: $type='jpg'; break;
				case 3: $type='png'; break;
				default: $type='error';
			}
	//		echo $type;
			
			if($type=='error')
				echo '<h2>Допустимы только изображения JPG, GIF и PNG</h2>';
			else {
				$base_name=time();
				if($info[0]>800 || $info[1]>600)
					MakePreview($_FILES['thumbnail']['tmp_name'],$folder, 800, 600, $type, $base_name);
				else
					copy($_FILES['thumbnail']['tmp_name'], $folder.'/'.$base_name.'.'.$type);
				
				if($info[0]>200 || $info[1]>150)
					MakePreview($_FILES['thumbnail']['tmp_name'], $folder.'/thumb', 200, 150, $type, $base_name);
				else
					copy($_FILES['thumbnail']['tmp_name'], $folder.'/thumb/'.$base_name.'.'.$type);

				unlink($_FILES['thumbnail']['tmp_name']);
			}
		}
	#
	# printing a list of uploaded pics:
		$UserThumbDir=$folder.'/thumb';
		$UserThumbDir2=$folder2.'/thumb';
		echo '<div align=center>Картинки, загруженные вами на сервер сегодня:</div><table border=0 cellpadding=4 cellspacing=5 width=100%>';
	
		$d=opendir($UserThumbDir);
		while(($CurFile=readdir($d))!==false){
			if(@is_file($UserThumbDir.'/'.$CurFile)) {
				$FileType=GetImageSize($UserThumbDir.'/'.$CurFile);
				$CurFile=explode('.',$CurFile);
				if(($FileType[2]==1 || $FileType[2]==2 || $FileType[2]==3) && is_file($folder.'/'.$CurFile[0].'.'.$CurFile[1])) {
					$FileType2=GetImageSize($folder.'/'.$CurFile[0].'.'.$CurFile[1]);
					echo '
		        	<tr><td bgcolor="F0F0F0">
		        	<br><div align=center>
		            <img border=0 src="'.$UserThumbDir2.'/'.$CurFile[0].'.'.$CurFile[1].'">
		            </div>

		            <div style="font-family:verdana;font-size:9px">
		            реальный размер: '.$FileType2[0].'x'.$FileType2[1].' <br>

		            <a href="JavaScript:AddImgFull2(\''.date('Y-m-d').'/'.AuthUserId.'/'.$CurFile[0].'.'.$CurFile[1].'\')">&laquo; вставить в полный размер</a><br>[img]'.date('Y-m-d').'/'.AuthUserId.'/'.$CurFile[0].'.'.$CurFile[1].'[/img]<br>
		            <a href="JavaScript:AddImgFull2Left(\''.date('Y-m-d').'/'.AuthUserId.'/'.$CurFile[0].'.'.$CurFile[1].'\')">&laquo; вставить в полный размер с обтеканием слева</a><br>[imgleft]'.date('Y-m-d').'/'.AuthUserId.'/'.$CurFile[0].'.'.$CurFile[1].'[/img]
					<br><br>
		            <a href="JavaScript:AddImgPrev2(\''.date('Y-m-d').'/'.AuthUserId.';'.$CurFile[0].'.'.$CurFile[1].';'.$FileType2[0].';'.$FileType2[1].'\')">&laquo; вставить с предпросмотром</a><br>[imgprev]'.date('Y-m-d').'/'.AuthUserId.';'.$CurFile[0].'.'.$CurFile[1].';'.$FileType2[0].';'.$FileType2[1].'[/imgprev]<br>
		            <a href="JavaScript:AddImgPrev2l(\''.date('Y-m-d').'/'.AuthUserId.';'.$CurFile[0].'.'.$CurFile[1].';'.$FileType2[0].';'.$FileType2[1].'\')">&laquo; вставить с предпросмотром и обтеканием слева</a><br>[imgprevleft]'.date('Y-m-d').'/'.AuthUserId.';'.$CurFile[0].'.'.$CurFile[1].';'.$FileType2[0].';'.$FileType2[1].'[/imgprev]
		            </div>
				<hr size=1>
		            </td></tr>
		        ';
				}
			}
		}
		echo '</table>';
?>
<form method='post' enctype='multipart/form-data'>
<div>&nbsp;&nbsp;Загрузить (JPG,PNG,GIF &lt; 2Mb)</div>
&nbsp;&nbsp;<input style='width:95%' class='input' name='thumbnail' type='file'>
<br><br>
<div align=center><input type="submit" value="UpLoad"></div>
<br>
</form>
<?
	}
?>