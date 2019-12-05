<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TZPD foto</title>
<style type="text/css">
<!--
body {
	background-color: #E5E5E5;
	margin: 10px;
	padding: 10px;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #333333;
}
a {
	color: #333333;
}
-->
</style>
</head>
<body onLoad="setTimeout('showFoto()', 1000)">
<?
include "_modules/mysql.php";
$query = "SELECT `nick` FROM `fotos_main` WHERE `file` = '".$_REQUEST['file']."' LIMIT 1;";
$r = mysql_query($query);
$res = mysql_fetch_array($r);
?>
<img src="/i/fotos/<?=urlencode($_REQUEST['file'])?>"><br><br>[<?=$res[0]?>] <a href="http://www.tzpolice.ru/foto-<?=$res[0]?>">http://www.tzpolice.ru/foto-<?=$res[0]?></a>
<br>
≈сли изображени€ не видно - вам не повезло с браузером =) ѕереходите по <a href="http://www.tzpolice.ru/foto-<?=$res[0]?>">ссылке</a> и наслаждайтесь всеми фотографи€ми этого пользовател€.
<hr size=1><br>
<script language="JavaScript" type="text/javascript"><!--
document.write('<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" '+
'codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="88" height="33">'+
'<param name="movie" value="http://www.timezero.ru/tzcnt.swf?color=3&ref='+escape(document.location)+'" />'+
'<param name="allowScriptAccess" value="always" /><embed src="http://www.timezero.ru/tzcnt.swf?color=3&ref='+escape(document.location)+
'" allowScriptAccess="always" width="88" height="33" type="application/x-shockwave-flash" '+
'pluginspage="http://www.macromedia.com/go/getflashplayer" /></object>');
//--></script>
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
                        '" alt="–ейтинг@Mail.ru"'+' border=0 height=31 width=88/><\/a>')
                        if(11<js)d.write('<'+'!-- ')//--></script><noscript><a
                        target=_top href="http://top.mail.ru/jump?from=761975"><img
                        src="http://top.list.ru/counter?js=na;id=761975;t=210"
                        border=0 height=31 width=88
                        alt="–ейтинг@Mail.ru"/></a></noscript><script language="JavaScript" type="text/javascript"><!--
                        if(11<js)d.write('--'+'>')//--></script>
                <!--/COUNTER-->
 <!--LiveInternet counter--><script type="text/javascript"><!--
document.write("<a href='http://www.liveinternet.ru/click' "+
"target=_blank><img src='http://counter.yadro.ru/hit?t20.4;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";hstandalone_foto;"+Math.random()+
"' alt='' title='LiveInternet: показано число просмотров за 24"+
" часа, посетителей за 24 часа и за сегодн€' "+
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

</body></html>