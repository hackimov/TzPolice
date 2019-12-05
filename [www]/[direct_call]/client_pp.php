<html>
<head>
  <title>.::Дежурная часть::.</title>
  <LINK href="http://www.tzpolice.ru/_modules/tzpol_css.css" rel="stylesheet" type="text/css">
</head>
<body>
<script>
function ClBrd(text){
    while(text.indexOf('<BR>')>=0) text = text.replace('<BR>','\015\012');
    if (window.clipboardData)
	{
		var oldtext = window.clipboardData.getData('Text');
		var newtext = oldtext.replace(/ private \[/g, " pri-vate [");
		newtext = newtext.replace(/ to \[/g, " t-o [");
		text = text + newtext;
		window.clipboardData.setData("Text", text);
	}
    else {
                netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');
                var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
                if (!clip) return;
                var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
                if (!trans) return;
                trans.addDataFlavor('text/unicode');
                var str = new Object();
//		var oldStr = new Object();
                var len = new Object();
//		clip.getData(trans, clip.kGlobalClipboard);
//		trans.getTransferData("text/unicode", oldStr, strLength);
//		if (oldStr) oldStr = oldStr.value.QueryInterface(Components.interfaces.nsISupportsString);
//		if (oldStr) pastetext = oldStr.data.substring(0, strLength.value / 2);
                var copytext=text;
                var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
                str.data=copytext;
                trans.setTransferData("text/unicode",str,copytext.length*2);
                var clipid=Components.interfaces.nsIClipboard;
                if (!clip) return false;
                clip.setData(trans,null,clipid.kGlobalClipboard);
        }
}
</script>
<table width="100%"  border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td>
<b>New Moscow</b><br>
<?
include "../_modules/mysql.php";
$SQL="SELECT p.*, u.id AS UID, u.user_name AS Uname FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user WHERE p.city=1 ORDER BY id DESC LIMIT 1";
$r=mysql_query($SQL);
$d=mysql_fetch_array($r);
if(mysql_num_rows($r)==0 || $d['post_g'] >0) {
	echo "Сейчас на посту никого.";
} else {
	echo "Сейчас на посту: <b><A HREF='#' onclick=\"ClBrd('private [{$d['Uname']}] ')\">{$d['Uname']}</A></b>";
}
?>
<hr><b>Oasis city</b><br>
<?
$SQL="SELECT p.*, u.id AS UID, u.user_name AS Uname FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user WHERE p.city=2 ORDER BY id DESC LIMIT 1";
$r=mysql_query($SQL);
$d=mysql_fetch_array($r);
if(mysql_num_rows($r)==0 || $d['post_g'] >0) {
	echo "Сейчас на посту никого.";
} else {
	echo "Сейчас на посту: <b><A HREF='#' onclick=\"ClBrd('private [{$d['Uname']}] ')\">{$d['Uname']}</A></b>";
}
?>
<hr><b>Vault city</b><br>
<?
$SQL="SELECT p.*, u.id AS UID, u.user_name AS Uname FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user WHERE p.city=4 ORDER BY id DESC LIMIT 1";
$r=mysql_query($SQL);
$d=mysql_fetch_array($r);
if(mysql_num_rows($r)==0 || $d['post_g'] >0) {
	echo "Сейчас на посту никого.";
} else {
	echo "Сейчас на посту: <b><A HREF='#' onclick=\"ClBrd('private [{$d['Uname']}] ')\">{$d['Uname']}</A></b>";
}
?>
<hr><b>Forum</b><br>
<?
$SQL="SELECT p.*, u.id AS UID, u.user_name AS Uname FROM posts_report p LEFT JOIN site_users u ON u.id=p.id_user WHERE p.city=3 AND p.post_g=0";	
$r=mysql_query($SQL);
$c=0;
while ($d=mysql_fetch_array($r)) {
	if ($c) {$nicks .= ", ";}
	$nicks .= "<b><A HREF='#' onclick=\"ClBrd('private [{$d['Uname']}] ')\">{$d['Uname']}</A></b>";
	$c++;
}
if($c == 0) {
	echo "Сейчас на посту никого.";
} else {
	echo "Сейчас на посту: ".$nicks;
}
?>
</td></tr></table></body></html>