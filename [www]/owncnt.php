<!--
code for page


<?	if (!in_array($module, $noTZcounter) || $module == 'news') { ?>
<OBJECT id="altercount" codeBase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" height="1" width="1" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><PARAM NAME="movie" VALUE="http://tzpolice.ru/_imgs/auth.swf"><PARAM NAME="wmode" VALUE="transparent">
<embed src="http://tzpolice.ru/_imgs/auth.swf" wmode="transparent" width="1" height="1" swLiveConnect="true" id="altercount" name="altercount" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</OBJECT>
<script language="JavaScript1.2">
<!--
var cnt_nick;
function altercount_DoFSCommand(command, args) {
	var tmp = args.split("\t");
	if (command == "OK")
    	{
			cnt_nick = tmp[0];
			document.getElementById('owncounter').innerHTML = '<img src="http://www.tzpolice.ru/owncnt.php?nick='+cnt_nick+'" height="1" width="1" border="0">ok';
        }
}
if (navigator.appName.indexOf("Microsoft") != -1) {// Hook for Internet Explorer.
	document.write('<script language=\"VBScript\"\>\n');
	document.write('On Error Resume Next\n');
	document.write('Sub altercount_FSCommand(ByVal command, ByVal args)\n');
	document.write('	Call altercount_DoFSCommand(command, args)\n');
	document.write('End Sub\n');
	document.write('</script\>\n');
}
// -->
</script>
<div id='owncounter'></div>

-->

<?
error_reporting(E_ALL);
$visit_string = date("H:i")."\t".$_REQUEST['nick']."\t".$_SERVER['REMOTE_ADDR']."
";
$visit_file = "visits/counter-".date("Y-m-d").".txt";
$f=fopen($visit_file,'a+');
fputs($f,$visit_string);
fclose($f);
?>
