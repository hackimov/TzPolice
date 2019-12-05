<?php

if ($_SERVER['HTTP_HOST'] !== 'www.tzpolice.ru') {
	header('Location: http://www.tzpolice.ru'.$_SERVER['REQUEST_URI']);
}

#секьюримся by Deorg
$return = '';
$in = array();
foreach($_GET as $k => $v) {
	$temp = mb_convert_encoding($v,"cp1251","utf8");
	$temp2 = mb_convert_encoding($temp,"utf8","cp1251");
	if($temp2 == $v) {
		$v = mb_convert_encoding($v,"cp1251","utf8");
	}
	if($k == 'location' && !preg_match("/^[-]?[\d]{1,3}\/[-]?[\d]{1,3}$/", $v)) {
		$v = '';
	}
	$in[$k] = addslashes(htmlspecialchars(trim($v)));
}
foreach($_POST as $k => $v) {
	$temp = mb_convert_encoding($v,"cp1251","utf8");
	$temp2 = mb_convert_encoding($temp,"utf8","cp1251");
	if($temp2 == $v) {
		$v = mb_convert_encoding($v,"cp1251","utf8");
	}
	if($k == 'location' && !preg_match("/^[-]?[\d]{1,3}\/[-]?[\d]{1,3}$/", $v)) {
		$v = '';
	}
	$in[$k] = addslashes(htmlspecialchars(trim($v)));
}

foreach($_REQUEST as $k => $v) {
	$temp = mb_convert_encoding($v,"cp1251","utf8");
	$temp2 = mb_convert_encoding($temp,"utf8","cp1251");
	if($temp2 == $v) {
		$v = mb_convert_encoding($v,"cp1251","utf8");
	}
	if($k == 'location' && !preg_match("/^[-]?[\d]{1,3}\/[-]?[\d]{1,3}$/", $v)) {
		$v = '';
	}
	$in[$k] = addslashes(htmlspecialchars(trim($v)));
}


foreach($_COOKIE as $k => $v) {
	$temp = mb_convert_encoding($v,"cp1251","utf8");
	$temp2 = mb_convert_encoding($temp,"utf8","cp1251");
	if($temp2 == $v) {
		$v = mb_convert_encoding($v,"cp1251","utf8");
	}
	$_COOKIE[$k] = addslashes(htmlspecialchars(trim($v)));
}

	require('_modules/functions.php');
	
	//$_GET = quote_smart($_GET, false);
	//$_POST = quote_smart($_POST, false);
	//$_REQUEST = quote_smart($_REQUEST, false);
	//$_COOKIE = quote_smart($_COOKIE, false);	
	
	require('_modules/auth.php');
	
	if(@$_REQUEST['act']) $module = htmlspecialchars($_REQUEST['act']);
	else $module = 'news';

	$noTZcounter = Array('prison_stats','pers_request','prisoned','prison_rating','compens','cops_stats','cops_depts');

	set_time_limit(30);
	if (@$_REQUEST['show_error'] == '1') {
		error_reporting(E_ALL);
	}else{
		error_reporting(0);
	}

if($module=='user_info') {
	
	$post_data = "{$REMOTE_ADDR}\t".AuthUserName."\t".date("Y-m-d H:i:s")."\t{$REQUEST_URI}\t";
	
	foreach($_POST as $key => $val) {
		
		if ($key == "AuthPass") continue; 
		$post_data.="{$key} = ".@urldecode($val)." | ";
		
	}
	
	$post_data.="\t{$HTTP_USER_AGENT}"."\n";
	$f=fopen('/home/sites/police/www/post/'.date('Y-m-d').'.txt','a+');
	fputs($f,$post_data);
	fclose($f);
	chmod('/home/sites/police/www/post/'.date('Y-m-d').'.txt', 0600);
	
}

//logs
	if(AuthUserClan=='police' || AuthUserClan=='Military Police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy') {
		$visit_string = date('H:i')."\t".AuthUserName."\t".$_SERVER['REQUEST_URI']."\t".$_SERVER['REMOTE_ADDR']."\n";
		$visit_file = 'visits/'.date('Y-m-d').'.txt';
		$f = fopen($visit_file,'a+');
		fputs($f,$visit_string);
		fclose($f);
	}
//end logs
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META name="verify-v1" content="h3liW5b59AM60c42sw30IYEu/7xxzKCYdkXvcHujYM4=" />
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
  <title>TZ Police Department :: Департамент полиции Точки отсчёта :: Police, Police Academy, Tribunal</title>
<?
	include('_modules/header.php');
	include('_modules/java.php');
?>
</head>

<body bgcolor="#000000" text="#455600" alink="#0D3AB4" link="#0D3AB4" vlink="#0D3AB4">

<!-- © Deorg -->
<script type='text/javascript' src='/scripts/ZeroClipboard.js'></script>
<style>

.inlineimg {
	vertical-align: text-bottom;
	line-height: normal;
	display: inline;
}
select.s {
	border-bottom: 1pt solid #9CA2AD;
	border-left: 1pt solid #9CA2AD;
	border-right: 1pt solid #9CA2AD;
	border-top: 1pt solid #9CA2AD;
	color: #576682;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	margin-bottom: 3px;
	margin-top: 3px;
}
input.s {
	border-bottom: 1pt solid #9CA2AD;
	border-left: 1pt solid #9CA2AD;
	border-right: 1pt solid #9CA2AD;
	border-top: 1pt solid #9CA2AD;
	color: #576682;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	margin-bottom: 3px;
	margin-top: 3px;
}
#dialogWindowBut {
	padding-left: 3px;
	padding-right: 0px;
	height: 23px;
	border-bottom: 1px solid green;
}
#dialogWindow {
	position:fixed;
	left: 40%;
	top: 40%;
	display: none;
	border: 1px solid #808080;
	z-index:10000;
	background: #000000;
}
#dialogWindowHint {
	padding-bottom: 2px;
	vertical-align: bottom;
	font-size: 12px;
	color: red;

}
#dialogWindowText {
	padding: 3px;
	font-size: 12px;
	vertical-align: top;
	text-align: left;
    background: #FFF5B3;
    border: 1px solid #808080;
    color: #A4B2B4;
}
.dialogWindowLeftTop {
	width: 21px;
	height: 23px;
	padding: 0px;
}
.dialogWindowRightTop {
	width: 21px;
	height: 23px;
	padding: 0px;
}
.dialogWindowLeftMiddle {
	width: 21px;
}
.dialogWindowRightMiddle {
	width: 21px;
}
.dialogWindowLeftBottom {
	width: 21px;
	height: 27px;
	padding: 0px;
}
.dialogWindowRightBottom {
	width: 21px;
	height: 27px;
	padding: 0px;
}
</style>

<table id='dialogWindow' style='display: none;' cellpadding=0 cellspacing=0 border=0>
<tr>
	<td class=dialogWindowLeftTop align=center>
		<img src='/i/load.gif' align=middle style='height: 8px; display: none; cursor: hand;' alt='loading' title='loading' id='dialogWindowlB'>
		<img src='/i/x.gif' align=middle style='height: 8px; display: none; cursor: hand;' alt='fail' title='fail' id='dialogWindowEB'>
	</td>
	<td align=right id='dialogWindowBut'>&nbsp;</td>
	<td class=dialogWindowRightTop>
		<a class=m href='#' onclick='javascript:closeWindow(); return false;'><img src='/i/x.gif' border=0 alt='close' title='close'></a>
	</td>
</tr>
<tr>
	<td class=dialogWindowLeftMiddle>&nbsp;</td>
	<td id='dialogWindowText' align=center>&nbsp;</td>
	<td class=dialogWindowRightMiddle>&nbsp;</td>
</tr>
<tr>
	<td class=dialogWindowLeftBottom> </td>
	<td class=dialogWindowBottom>&nbsp;</td>
	<td class=dialogWindowRightBottom> </td>
</tr>
</table>

<script>
function clear_field(field) {
    if (field.value==field.defaultValue) {
        field.value=''
    }
}

function check_field(field) {
    if (field.value=='' ||
    field.value==' ') {
        field.value=field.defaultValue
    }
}
function resetSearch() {
	var form = document.forms.finder;
 	form.elements.slogin.value='';
 	form.elements.sclan.value='';
 	form.elements.sbattle.value='';
	form.elements.minlvl.selectedIndex = 0;
	form.elements.maxlvl.selectedIndex = 19;
 	form.submit();
}
var clip = null;
clip = new ZeroClipboard.Client();

var windowStatus = 'disable';
function createWindow(w,h,data) {
	
	w = (w)?w:400;
	h = (h)?h:'';
    windowStatus = 'create';

	document.getElementById('dialogWindow').style.width = w;
	document.getElementById('dialogWindow').style.height = h;

	var lf = Math.ceil((document.body.clientWidth-w)/2)-24;
	document.getElementById('dialogWindow').style.left = lf;
    document.getElementById('dialogWindow').style.top = document.body.scrollTop + 250;

	document.getElementById('dialogWindowText').innerHTML = data;
	document.getElementById('dialogWindow').style.display = 'block';

}

function closeWindow() {
	windowStatus = 'close';
	document.getElementById('dialogWindowText').innerHTML = '';
	document.getElementById('dialogWindow').style.display = 'none'
}
</script>

<script>
var LogWindow=0;
function LogWin(URLStr)
	{
		if(LogWindow)
			{
				if(!LogWindow.closed) LogWindow.close();
			}
		var actual_url = "/sbtl.ru.html?" + URLStr;
		LogWindow = open(actual_url, 'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,copyhistory=yes,width=1004,height=400');
	}
    var now = new Date();
    var LocalHour = now.getHours();
    var sky = 04;
    if(LocalHour==22 || LocalHour==23 || LocalHour<=6) sky="03";
    if(LocalHour>6 && LocalHour<=9) sky="04";
    if(LocalHour>9 && LocalHour<=17) sky="02";
    if(LocalHour>17 && LocalHour<22) sky="04";
        document.write('<table width=100% height=100%  border=0 cellpadding=0 cellspacing=0 background=i/bgr-clouds-'+sky+'.jpg class=norepeat>');
    </script>
  <tr valign="bottom">
    <td width="24" height="170"><table width="24" height="30"  border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td><img src="i/empty.gif" width="24" height="30"></td>
      </tr>
      <tr>
        <td width="24" height="28"><img src="i/left-metal.gif" width="24" height="28"></td>
      </tr>
    </table></td>
    <td width="180" height="170"><table width="180" height="139"  border="0" cellpadding="0" cellspacing="0">
      <tr valign="bottom">
        <td width="34"><img src="i/logo-00.gif" width="34" height="67"></td>
<?
	$usronline = TZOnline();
	if ($usronline > 10){
		$pic = '<img src="i/logo_police.gif" width="116" height="139" border="0" alt="Сервер TimeZero работает">';
	}else{
		$pic = '<img src="i/logo_police_a.gif" width="116" height="139" border="0" alt="Сервер TimeZero не работает">';
	}
?>
        <td width="116"><a href="/"><?=$pic?></a></td>
        <td width="30"><img src="i/logo-01.gif" width="30" height="94"></td>
      </tr>
    </table></td>
    <td width="24" height="170"><img src="i/metal01.gif" width="24" height="104"></td>
    <td><table width="100%" height="104"  border="0" cellpadding="0" cellspacing="0" background="i/bgr-grid.gif" class="tab-bottom-repeat-x">
      <tr>
        <td width="280" height="76" valign="bottom">
        <table width="280" height="76"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="113" background="i/metal02.gif">
            <table width="113" height="76"  border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td><img src="i/empty.gif" width="34" height="10"></td>
                <td><img src="i/empty.gif" width="66" height="10"></td>
                <td width="13"><img src="i/empty.gif" width="13" height="10"></td>
              </tr>
              <tr>
<?	include('_modules/block_auth.php'); ?>
        <td width="34" height="28"><img src="i/device-02b.gif" width="34" height="28"></td>
<td height="28" align="right" valign="bottom"><img src="i/bgr-grid-end.gif" width="35" height="28"></td>
      </tr>
    </table>
   </td><td width="24" height="170">
    <img src="i/empty.gif" width="24" height="190">
   </td></tr>
  <tr valign="top"><td width="24">
              &nbsp;
             </td><td width="180" background="i/menu-under-bgr.gif" class="repeat">
                        <table width="180"  border="0" cellspacing="0" cellpadding="0">
                        <tr valign="top"><td width="6" background="i/menu-left.gif" bgcolor="606F77">
                               <img src="i/menu-left-norepeat.gif" width="6" height="238">
                        </td><td width="168" background="i/bgr-menu.gif" bgcolor="001A29" class="repeat">
                                <table width="100%" height="200"  border="0" cellpadding="5" cellspacing="0" background="i/bgr-menu-norepeat.jpg" class="norepeat">
                                <tr><td valign="top" class="d-text">
                                      <p><img src="i/empty.gif" width="10" height="10"></p>
                                        <p></p>
<?

	include('_modules/menu.php');
	include('_modules/poll.php');
	include('_modules/online.php');

?>
<script>
function ClBrd(text){
    while(text.indexOf('<BR>')>=0) text = text.replace('<BR>','\015\012');
    if (window.clipboardData){window.clipboardData.setData("Text", text);alert ("Строка для обращения в приват в чате ТЗ добавлена в буфер обмена.");}
    else {
		var DummyVariable = prompt('Скопируйте эту строку и используйте ее для обращения в чате ТЗ:',text);
        }
}
function ClBrd2(text){
    while(text.indexOf('<BR>')>=0) text = text.replace('<BR>','\015\012');
    if (window.clipboardData){window.clipboardData.setData("Text", text);alert ("Номер боя скопирован в буфер обмена.");}
    else {
		var DummyVariable = prompt('Буфер обмена недоступен, скопируйте номер вручную:',text);
        }
}
function ClBrdUdo(text){
    while(text.indexOf('<BR>')>=0) text = text.replace('<BR>','\015\012');
    if (window.clipboardData){window.clipboardData.setData("Text", text);alert ("Текст скопирован в буфер обмена.");}
    else {
		var DummyVariable = prompt('Буфер обмена недоступен, скопируйте текст вручную:',text);
        }
}

function ClBrdUniversal(text,message,altmessage) {
    while(text.indexOf('<BR>')>=0) text = text.replace('<BR>','\\n');
    if (window.clipboardData) {
    	window.clipboardData.setData('Text', text);
    	alert (message);
    } else {
    	var DummyVariable = prompt(altmessage,text);
	}
}

</script>

<P class=menu1th><IMG height=11 hspace=0 src="i/bullet-menu01.gif" width=15 align=absMiddle> <b>Обратная связь</b>

<P class=menu2th><IMG height=7 src="i/bullet-menu02.gif" width=10 border=0> <A class=d-menulink2th href="javascript:{}" onClick="ClBrdUniversal('tzpolice@timezero.ru', 'E-mail скопирован в буфер обмена','Буфер обмена недоступен, скопируйте адрес вручную: ');">Вопросы по сайту</A></p>

<p><img src="i/empty.gif" width="10" height="15"></p></td></tr></table></td><td width="6" background="i/menu-right.gif" bgcolor="606F77">

<img src="i/menu-right-norepeat.gif" width="6" height="238"></td></tr><tr><td colspan="3">
<img src="i/menu-end.gif" width="180" height="33"></td></tr><tr><td colspan="3">
<img src="i/empty.gif" width="180" height="20"></td></tr></table>

<div align=center>
<?
	if (in_array($module, $noTZcounter)) {
		echo '<font color="#cccccc" size="1"><b>счетчик ТЗ отключен</b></font>';
	} else {
?>
<script language="JavaScript" type="text/javascript"><!--
document.write('<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" '+
'codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="88" height="33">'+
'<param name="movie" value="http://www.timezero.ru/tzcnt.swf?color=3&ref='+escape(document.location)+'" />'+
'<param name="allowScriptAccess" value="always" /><embed src="http://www.timezero.ru/tzcnt.swf?color=3&ref='+escape(document.location)+
'" allowScriptAccess="always" width="88" height="33" type="application/x-shockwave-flash" '+
'pluginspage="http://www.macromedia.com/go/getflashplayer" /></object>');
//--></script>
<?
	}
?>
                <br><br>
                <!--Rating@Mail.ru COUNTER--><script language="JavaScript" type="text/javascript"><!--
                        d=document;var a='';a+=';r='+escape(d.referrer)
                        js=10//--></script><script language="JavaScript1.1" type="text/javascript"><!--
                        a+=';j='+navigator.javaEnabled()
                        js=11//--></script><script language="JavaScript1.2" type="text/javascript"><!--
                        s=screen;a+=';s='+s.width+'*'+s.height
                        a+=';d='+(s.colorDepth?s.colorDepth:s.pixelDepth)
                        js=12//--></script><script language="JavaScript1.3" type="text/javascript"><!--
                        js=13//--></script><script language="JavaScript" type="text/javascript"><!--
                        d.write('<a href="http://top.mail.ru/jump?from=761975"'+
                        ' target=_blank><img src="http://top.list.ru/counter'+
                        '?id=761975;t=210;js='+js+a+';rand='+Math.random()+
                        '" alt="Рейтинг@Mail.ru"'+' border=0 height=31 width=88/><\/a>')
                        if(11<js)d.write('<'+'!-- ')//--></script><noscript><a
                        target=_top href="http://top.mail.ru/jump?from=761975"><img
                        src="http://top.list.ru/counter?js=na;id=761975;t=210"
                        border=0 height=31 width=88
                        alt="Рейтинг@Mail.ru"/></a></noscript><script language="JavaScript" type="text/javascript"><!--
                        if(11<js)d.write('--'+'>')//--></script>
                <!--/COUNTER-->
                        <br><br>
<!--LiveInternet counter--><script type="text/javascript"><!--
document.write("<a href='http://www.liveinternet.ru/click' "+
"target=_blank><img src='http://counter.yadro.ru/hit?t20.4;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";h<?=$module;?>;"+Math.random()+
"' alt='' title='LiveInternet: показано число просмотров за 24"+
" часа, посетителей за 24 часа и за сегодня' "+
"border=0 width=88 height=31><\/a>")//--></script><!--/LiveInternet-->

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-3266438-1");
pageTracker._initData();
pageTracker._trackPageview();
</script>
<br><br>
                <a href="http://www.tzpolice.ru/rss.php" target="_blank"><img src="i/rss20.gif" border="0" height="15" width="90" alt="Читать в RSS 2.0"></a>
                </div>
        </td><td width="24" height="100%">
                &nbsp;
        </td><td width="100%" height="100%" background="i/block-22.jpg" bgcolor="EBDFB7" class="repeat">
                <table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" background="i/block-12.jpg" class="tab-top-left-repeat-x">
                <tr><td valign="top" background="i/block-32.jpg" class="bottom-repeat-x">
                        <table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" background="i/block-21.gif" class="tab-top-left-repeat-y">
                        <tr><td valign="top" background="i/block-23.gif" class="top-right-repeat-y">
                                <table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" background="i/block-11.jpg" class="tab-top-left-norepeat">
                               <tr><td background="i/block-13.gif" class="top-right-norepeat">
                                        <table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" background="i/block-31.gif" class="tab-bottom-left-norepeat">
                                        <tr><td valign="top" background="i/block-33.gif" class="bottom-right-norepeat">
                                                <table width="100%"  border="0" cellspacing="0" cellpadding="30">
                                                <tr><td>
<?
	include ('./module_selector.php');
?>
                                                </td></tr>
                                                </table>
                                        </td></tr>
                                        </table>
                                </td></tr>
                                </table>
                        </td></tr>
                        </table>
                </td></tr>
                </table>
        </td><td width="24" height="100%">
        </td></tr>
</table>
<script>
function createClipFla(data,text) {
	if (clip.div) {
		clip.receiveEvent('mouseout', null);
		clip.reposition(data);
	} else clip.glue(data)

	clip.setText(text);
	clip.setHandCursor(true);
	clip.receiveEvent('mouseover', null);
	clip.addEventListener('complete', function (client, text) {
		alert('Скопировано в буфер: '+text);
	});
}


</script>
</body>
</html>
<? mysql_close(); ?>