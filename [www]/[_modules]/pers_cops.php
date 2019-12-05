<h1>Заявки на восстановление контроля над персонажем (копы)</h1>
<form name="r_pers_req" method="post" action="">
	<input id="r_from" name="r_from" type="hidden" value="">
    <input id="r_from_sid" name="r_from_sid" type="hidden" value="">
    <input id="r_from_city" name="r_from_city" type="hidden" value="">
</form>
<SCRIPT src='_modules/xhr_js.js'></SCRIPT>
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="1" height="1" id="tz">
<param name="movie" value="/authorization3.swf" />
<param name="wmode" value="transparent" />
<embed src="/authorization3.swf" wmode="transparent" width="1" height="1" swLiveConnect="true" id="tz" name="tz" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
<SCRIPT src='_modules/xhr_js.js'></SCRIPT>
<script language="JavaScript1.2">
<!--
var cop;
var cop_sid;
var cop_city;
function pr(nick)
    {
        if (nick)
            {
                window.open('direct_call/pers_requests.php?pn='+cop+'&cn='+nick+'&ps='+cop_sid+'&pc='+cop_city, '', 'height=300px, width=500px, status=no, toolbar=no, menubar=no, location=no, resizable=no, scrollbars=1');
            }
    }
function tz_DoFSCommand(command, args) {
	var tmp = args.split("\t");
	if (command == "OK")
    	{
			cop = tmp[0];
			cop_sid = tmp[1];
			cop_city = tmp[2];
			document.getElementById('pers_list').innerHTML = "<center><b>PLEASE WAIT...</b></center>";
			var req2 = new Subsys_JsHttpRequest_Js();
			req2.onreadystatechange = function()
        	{
				if (req2.readyState == 4)
                	{
                        if (req2.responseJS) {
                        	document.getElementById('pers_list').innerHTML = req2.responseText;
                        }
					}
            }
		req2.caching = false;
		req2.open('POST', '_modules/backends/pers_cops_list.php', true);
		req2.send({ cop_n: cop, cop_s: cop_sid, cop_c: cop_city });
        }
	else
    	{
			alert ("Не удалось произвести процедуру авторизации.\nВойдите в игру своим персонажем и обновите страницу");
        }
}
function list()
	{
			var req99 = new Subsys_JsHttpRequest_Js();
			req99.onreadystatechange = function()
        	{
				if (req99.readyState == 4)
                	{
                        if (req99.responseJS) {
                        	document.getElementById('pers_list').innerHTML = req99.responseText;
                        }
					}
            }
		req99.caching = false;
		req99.open('POST', '_modules/backends/pers_cops_list.php', true);
		req99.send({ cop_n: cop, cop_s: cop_sid, cop_c: cop_city });
    }
if (navigator.appName.indexOf("Microsoft") != -1) {// Hook for Internet Explorer.
	document.write('<script language=\"VBScript\"\>\n');
	document.write('On Error Resume Next\n');
	document.write('Sub tz_FSCommand(ByVal command, ByVal args)\n');
	document.write('	Call tz_DoFSCommand(command, args)\n');
	document.write('End Sub\n');
	document.write('</script\>\n');
}
function accept()
	{
		var cid = '' + document.getElementById('cur_id').value;
        var com = '' + document.getElementById('r_comment').value;
		document.getElementById('main_content').innerHTML = "<center><b>PLEASE WAIT...</b></center>";
		var req = new Subsys_JsHttpRequest_Js();
		req.onreadystatechange = function()
        	{

			if (req.readyState == 4)
                	{
                        if (req.responseJS) {
                        	document.getElementById('main_content').innerHTML = req.responseText;
                        }
					}
            }
		req.caching = false;
		req.open('POST', '_modules/backends/pers_cops_do.php', true);
		req.send({ id: cid, status: '2', comment: com, cop_n: cop, cop_s: cop_sid, cop_c: cop_city });
    }
function deny()
	{
		var cid = '' + document.getElementById('cur_id').value;
        var com = '' + document.getElementById('r_comment').value;
		document.getElementById('main_content').innerHTML = "<center><b>PLEASE WAIT...</b></center>";
		var req = new Subsys_JsHttpRequest_Js();
		req.onreadystatechange = function()
        	{
				if (req.readyState == 4)
                	{
                        if (req.responseJS) {
                        	document.getElementById('main_content').innerHTML = req.responseText;
                        }
					}

           }
		req.caching = false;
		req.open('POST', '_modules/backends/pers_cops_do.php', true);
		req.send({ id: cid, status: '4', comment: com, cop_n: cop, cop_s: cop_sid, cop_c: cop_city });
    }
function showPers()
	{
		var nid = '' + document.getElementById('select').options[document.getElementById('select').selectedIndex].value;
        document.getElementById('cur_id').value = nid;
		document.getElementById('main_content').innerHTML = "<center><b>PLEASE WAIT...</b></center>";
		var req = new Subsys_JsHttpRequest_Js();
		req.onreadystatechange = function()
        	{
				if (req.readyState == 4)
                	{
                        if (req.responseJS) {

                      	document.getElementById('main_content').innerHTML = req.responseText;
                        }
					}
            }
		req.caching = false;
		req.open('POST', '_modules/backends/pers_cops.php', true);
		req.send({ id: nid, cop_n: cop, cop_s: cop_sid, cop_c: cop_city });
	}
function showOld()
	{
		document.getElementById('old_requests').innerHTML = "<center><b>PLEASE WAIT...</b></center>";

	var req = new Subsys_JsHttpRequest_Js();
		req.onreadystatechange = function()
        	{
				if (req.readyState == 4)
                	{
                        if (req.responseJS) {
                        	document.getElementById('old_requests').innerHTML = req.responseText;
                        }
					}
            }
		req.caching = false;
		req.open('POST', '_modules/backends/pers_cops_old.php', true);
		req.send({ cop_n: cop, cop_s: cop_sid, cop_c: cop_city });
	}
function hideOld()
	{
		document.getElementById('old_requests').innerHTML = "";
	}
//-->
</script>

<div id='pers_list'><a href="#; return false;" onClick="list();">Начать работу</a></div>
<a href="#; return false;" onClick="showOld();">Обработанные заявки за последние 60 дней.</a> (<a href="#; return false;" onClick="hideOld();">скрыть</a>)
<div id='old_requests'></div>
<div id='main_content'></div>