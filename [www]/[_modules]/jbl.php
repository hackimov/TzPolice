<?php
if ($_REQUEST['p']==1) {
	if(@!mysql_connect("213.248.58.16","tzpoli","X.EvUiy")) {
		echo "
		<br><br><blockquote style='font-family:verdana;font-size:11px'><b>Нет связи с базой данных!</b><br>
		Проверьте параметры соединения. <br>
		Ответ сервера: \"".mysql_error()."\"</blockquote>";
		exit;
	} else mysql_select_db("tzpoli");
	$SQL="SELECT `nick` FROM `police_b_list` WHERE `status` = '0' ORDER BY `nick`";
	$r=mysql_query($SQL);
	echo "<users>\n";
	while ($d=mysql_fetch_array($r)) {
?>
<user name="<?=$d['nick']?>"/>
<?
	}
	echo "</users>\n";
} else if ($_REQUEST['p']==2) {
	if(@!mysql_connect("213.248.58.16","tzpoli","X.EvUiy")) {
		echo "
		<br><br><blockquote style='font-family:verdana;font-size:11px'><b>Нет связи с базой данных!</b><br>
		Проверьте параметры соединения. <br>
		Ответ сервера: \"".mysql_error()."\"</blockquote>";
		exit;
	} else mysql_select_db("tzpoli");
	$SQL="SELECT status FROM `black_list` WHERE nick='".$_REQUEST['nick']."'";
	$r=mysql_query($SQL);
	if ($d=mysql_fetch_array($r)) {
		if ($d['status']) {
			$SQL="UPDATE `black_list` SET level='".$_REQUEST['level']."', pro='".$_REQUEST['pro']."', clan='".$_REQUEST['clan']."', status=1, loc='".gmdate("d.m.y H:i:s", time()+14400)."<br>".$_REQUEST['loc']."' WHERE nick='".$_REQUEST['nick']."'";
		} else {
			$SQL="UPDATE `black_list` SET date=NOW(), level='".$_REQUEST['level']."', pro='".$_REQUEST['pro']."', clan='".$_REQUEST['clan']."', status=1, loc='".gmdate("d.m.y H:i:s", time()+14400)."<br>".$_REQUEST['loc']."' WHERE nick='".$_REQUEST['nick']."'";
		}
		mysql_query($SQL);
	}
?>
<script>
	function q() {
		history.back();
	}
</script>
<body onload="q()">
	<?=$_REQUEST['nick']?>
</body>
<?
} else if(AuthStatus==1 && AuthUserName!="" && (AuthUserClan=='police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy')) {
?>
<script>
function ClipBoard(text){
    while(text.indexOf('<BR>')>=0) text = text.replace('<BR>','\015\012');
    if (window.clipboardData){window.clipboardData.setData("Text", text);}
    else {
		netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');
		var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
		if (!clip) return;
		var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
		if (!trans) return;
		trans.addDataFlavor('text/unicode');
		var str = new Object();
		var len = new Object();
		var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
		var copytext=text;
		str.data=copytext;
		trans.setTransferData("text/unicode",str,copytext.length*2);
		var clipid=Components.interfaces.nsIClipboard;
		if (!clip) return false;
		clip.setData(trans,null,clipid.kGlobalClipboard);
	}
}
</script>
<input type=button value="Скопировать" onclick="ClipBoard(document.all('bl').innerHTML)"><br><br>
<div id=bl>
<?
	$SQL="SELECT `nick` FROM `police_b_list` WHERE `status` = '0' ORDER BY `nick`";
	$r=mysql_query($SQL);
	while ($d=mysql_fetch_array($r)) {
?>
<?=$d['nick'].'<br>'?>
<?
	}
?>
</div>
<?
} else echo $mess['AccessDenied'];
?>