var clientPC = navigator.userAgent.toLowerCase();
var clientVer = parseInt(navigator.appVersion);
var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var set_on = false;

function showInfo(login, e){
	if (!login.length) { hideInfo(); return; }
    var obj = document.getElementById("charinf");
	obj.innerHTML = '<table width=300 height=350 border=0 cellspacing="0" cellpadding="0"><tr><td><table border=0 height=15 width=100% cellspacing="0" cellpadding="0"><tr><td width="100%" bgcolor="#333333"><b><font color="#cccccc">&nbsp;&nbsp;'+login+'</b></font></td><td style="cursor:hand" onclick="hideInfo();" bgcolor="#808080"><font color="#FFFFFF" family="Trebuchet MS, Arial"><b>&nbsp;X&nbsp;</b></font></td></tr></table></td></tr><tr><td>'+
		'<iframe frameborder=0 marginwidth=0 marginheight=0 scrolling="no" width="300" height="335" id="info" src="http://www.timezero.ru/info.swf?login='+login+'"></iframe></td></tr></table>';
	obj.style.visibility = "visible";
	obj.style.left = document.body.scrollLeft + (e.clientX < document.body.clientWidth/2 ? e.clientX + 15 : e.clientX - 285);
	obj.style.top = document.body.scrollTop + (e.clientY+335 > document.body.clientHeight ? document.body.clientHeight-350 : e.clientY + 5);;
}
function hideInfo(){
	var obj = document.getElementById("charinf");
	if (obj) obj.style.visibility = "hidden";
}