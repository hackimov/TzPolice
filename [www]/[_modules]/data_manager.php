<h1>Управление контентом</h1>

<?php
if(abs(AccessLevel) & AccessArticles) {
	
	if(@$_REQUEST['Title'] && @$_REQUEST['NewsText']) {
		$TitleToDisplay=$_REQUEST['Title'];
		$TextToDisplay=$_REQUEST['NewsText'];
		$BRToDisplay=$_REQUEST['replaceBR'];
		$CommentsToDisplay=$_REQUEST['AllowComments'];
		
		if(@$_REQUEST['DataId']>0 && @$_REQUEST['EditData']==1) {
			$SQL = 'UPDATE `site_data` SET `replace_br`=\''.$BRToDisplay.'\', `title`=\''.addslashes(strip_tags($TitleToDisplay)).'\', `text`=\''.addslashes($TextToDisplay).'\', `allow_comments`=\''.$CommentsToDisplay.'\' WHERE `id`=\''.$_REQUEST['DataId'].'\'';
			mysql_query($SQL);
			echo '<div class=green>Материал <b>"'.stripslashes(strip_tags($TitleToDisplay)).'"</b> изменен</div><br>
';
		}
	}

	$edit=0;
	if(@$_REQUEST['DataId']) {
		
		$r=mysql_query('SELECT * FROM `site_data` WHERE `id`="'.$_REQUEST['DataId'].'"');
		if(mysql_num_rows($r)>0) {
			$d=mysql_fetch_assoc($r);
			$edit=1;
			$TitleToDisplay=$d['title'];
			$TextToDisplay=$d['text'];
			$BRToDisplay=$d['replace_br'];
			$CommentsToDisplay=$d['allow_comments'];
		}
	}
	
	if((@$_REQUEST['send']=='preview' || $edit==1)  && ($TitleToDisplay && $TextToDisplay)) {
		echo "
		<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
		<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Предпросмотр статьи: \"".stripslashes(strip_tags($TitleToDisplay))."\"</strong> </p></td>
		</tr><tr><td>
   ";
		ParseNews($TextToDisplay, 1, $BRToDisplay);
	//	ParseNews2($TextToDisplay);
		echo '
        </td></tr></table>
    ';
    }
	
	if(@$_REQUEST['send']=='add' && @$_REQUEST['SectionId']!='' && @$_REQUEST['Title']!='' && @$_REQUEST['NewsText']!='') {
		$SecToAdd=$_REQUEST['SectionId'];
		$SQL = 'INSERT INTO `site_data` (id_sec,title,text,post_date,poster_id,allow_comments,replace_br)
		values (\''.$SecToAdd.'\', \''.addslashes(strip_tags($_REQUEST['Title'])).'\', \''.addslashes($_REQUEST['NewsText']).'\', \''.time().'\', \''.AuthUserId.'\', \''.$_REQUEST['AllowComments'].'\', \''.$_REQUEST['replaceBR'].'\')';
		mysql_query($SQL);
		echo '<div class=green>Статья <b>"'.stripslashes(strip_tags($_REQUEST['Title'])).'"</b> добавлена в раздел <b>"'.$SecName[$SecToAdd].'"</b></div><br>
    ';
		unset($TextToDisplay,$TitleToDisplay);
	}
	
?>

<table width=100% cellpadding=5><tr>
<td valign=top>

        <form method="POST" name="News">
        <input name="act" type="hidden" value="data_manager">
    Заглавие: <Br>
        <input name="Title" style="width=100%" type="text" value="<?=@htmlspecialchars(stripslashes($TitleToDisplay))?>">
    <br>
    Текст: <Br>
        <textarea ONSELECT="storeCaret(this);" ONCLICK="storeCaret(this);" ONKEYUP="storeCaret(this);" name="NewsText" id="NewsText" style="width:100%" rows=45><?
        	 echo @htmlspecialchars( stripslashes( ParseNews($TextToDisplay, 1, 0)));
?></textarea><br>
        <img src="/_imgs/b.gif" border=0 onclick="decor('b')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы сделать его полужирным">
        <img src="/_imgs/i.gif" border=0 onclick="decor('i')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы выделить его курсивом">
        <img src="/_imgs/u.gif" border=0 onclick="decor('u')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы сделать его подчеркнутым">
        <img src="/_imgs/center.gif" border=0 onclick="decor('div align=center')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы сделать отцентрировать его">
                <div>* все HTML-теги разрешены</div><br>

<?if($edit==0) {?>
        <input name="send" type="radio" value="preview" checked onclick="SectionId.disabled=1"> предпросмотр <br>
        <input name="send" type="radio" value="add" onclick="SectionId.disabled=0"> добавить статью в раздел: <select size="1" name="SectionId" disabled>
<?
}
if($edit==1) {
	echo "
    <input name='EditData' type='hidden' value='1'>
	<input name='DataId' type='hidden' value='".$_REQUEST['DataId']."'>
    ";
}

if($edit==0) foreach($SecName as $key => $val) echo '<option value="'.$key.'">'.$val.'</option>';


?>
        </select> <br><br>
        <input name="replaceBR" type="hidden" value="0">
        <input name="replaceBR" type="checkbox" value="1" <? if(@$BRToDisplay==1) echo 'checked'; ?>> вставлять тег &lt;BR&gt; перед каждой новой строкой <BR>
		<input name="AllowComments" type="hidden" value="0">
        <input name="AllowComments" type="checkbox" value="1" <? if(@$CommentsToDisplay==1) echo 'checked'; ?>> разрешить комментарии

        <br><br>
        <input type="submit" value="Готово">
        </form>
        *Если в разделе только одна статья, то при открытии раздела эта статья открывается сразу

</td><td width=250 valign=top>

        Вставить: <br>
    <select size="1" onchange="document.getElementById('icons').src='_modules/icons/'+this.options[this.selectedIndex].value+'.php'" style="width:100%">
        <option value="blank" selected>-- выберите категорию --</option>
        <option value="smiles"> Смайлики</option>
        <option value="clans"> Кланы</option>
        <option value="prof"> Профессии</option>
        <option value="login"> Ник</option>
        <option value="url"> URL</option>
        <option value="upload"> Свои картинки</option>
        </select>

    <iframe BORDER=0 FRAMEBORDER=0 id="icons" src="about:blank" width=100% HEIGHT=210 style="border:1 solid #000000"></iframe>

</td>
</tr></table>
<?

//this.options[this.selectedIndex].value

} else {

    echo $mess['AccessDenied'];

}


?>