var IE = '\v'=='v';
var clip = null;
clip = new ZeroClipboard.Client();

var windowStatus = 'disable';

function mu(l,h,n,c,d,e){
	var txt="";
	if (l == 1) {
		if (d == 1){ txt+="</div>"; }
		if (c == 'n'){ txt+='<P class=menu1th><IMG height=11 hspace=0 src="i/bullet-menu01.gif" width=15 align=absMiddle> <A class=d-menulink href="'+h+'">'+n+'</A></P>'; }
		else {
			if (e == 1) {
				txt+='<P class=menu1th><IMG height=11 hspace=0 src="i/bullet-menu01.gif" width=15 align=absMiddle> <A class=d-menulink href="javascript:{}" onClick="sh(\'mn'+c+'\');\">'+n+'</A></P>';
				txt += '<div id="mn'+c+'" style="display: none">';
			} else {
				txt+='<P class=menu1th><IMG height=11 hspace=0 src="i/bullet-menu01.gif" width=15 align=absMiddle> <A class=d-menulink href="javascript:{}" onClick="sh(\'mn'+c+'\');\">'+n+'</A></P>';
				txt += '<div id="mn'+c+'">';
			}
		}
	} else {
		txt+='<P class=menu2th><A class=d-menulink2th href="'+h+'"><IMG height=7 src="i/bullet-menu02.gif" width=10 border=0>'+n+'</A></P>';
	}
	return (txt);
}

function decor(tg) {
        if (tg == "layer") {
          var i=Math.round(Math.random()*9999);
          var OpenTag="<a onclick=\"javascript:if(l"+i+".style.display=='none') l"+i+".style.display=''; else l"+i+".style.display='none';\" href=\"javascript:{}\">тескт ссылки</a><div id=\"l"+i+"\" style=\"display:none\">";
          var CloseTag="</div>";
        } else {
          var OpenTag="<"+tg+">";
          var CloseTag="</"+tg+">";
        }
        var txtarea = document.News.NewsText;
        txtarea.focus();
        var theSelection = document.selection.createRange().text;
        if (!theSelection) {
                txtarea.value += OpenTag + CloseTag;
        } else document.selection.createRange().text = OpenTag + theSelection + CloseTag;
}

function storeCaret (textEl) {
if (textEl.createTextRange)
textEl.caretPos = document.selection.createRange().duplicate();
}

function initInsertions()
{
	var doc;

	if (document.forms[form_name])
	{
		doc = document;
	}
	else
	{
		doc = opener.document;
	}

	var textarea = doc.forms[form_name].elements[text_name];

	if (is_ie && typeof(baseHeight) != 'number')
	{
		textarea.focus();
		baseHeight = doc.selection.createRange().duplicate().boundingHeight;

		if (!document.forms[form_name])
		{
			document.body.focus();
		}
	}
}


function insertAtCaret (textEl, text) {
if (textEl.createTextRange && textEl.caretPos) {
var caretPos = textEl.caretPos;
caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
}
else textEl.value += text;
}




function AddClan(smile) {
        insertAtCaret(top.document.getElementById('NewsText'), '[clan]'+smile+'[/clan]')
}

function AddProf(smile) {
        insertAtCaret(top.document.getElementById('NewsText'), '[prof]'+smile+'[/prof]')
}
function AddSmile(smile) {
        insertAtCaret(top.document.getElementById('NewsText'), ':'+smile+': ')
}
function AddImgFull(name) {
        insertAtCaret(top.document.getElementById('NewsText'), '[img]'+name+'[/img] ')
}
function AddImgPrev(name) {
        insertAtCaret(top.document.getElementById('NewsText'), '[imgprev]'+name+'[/imgprev] ')
}
function AddLogin1(login,clan,level,prof) {
        var LoginStr = '';
        if (clan != 0) { var LoginStr = LoginStr + '[clan]'+clan+'[/clan]'; }
        var LoginStr = LoginStr + '<b>'+login+'</b> ['+level+'][prof]i'+prof+'[/prof]';
        insertAtCaret(top.document.getElementById('NewsText'), LoginStr)
}

function AddURL1() {
        URLStr='[URL]'+document.AddURL.URL.value+';'+document.AddURL.URLdesc.value+'[/URL] ';
        insertAtCaret(top.document.getElementById('NewsText'), URLStr)
}
function Enlarge(path,img,w,h) {
        h+=30;
	    w+=20;
        ss=window.open("/user_data/"+path+"/"+img+"","show"+w+h,"height="+h+",width="+w+",status=0,toolbar=0,menubar=0,location=0,top=5,left=5");
}
function sh(lid)
	{
		if(document.getElementById(lid).style.display=='none')
			{
	        	document.getElementById(lid).style.display='';
            }
        else
        	{
            	document.getElementById(lid).style.display='none';
            }
    }
function AddImgFull2(name) {
        insertAtCaret(window.opener.document.getElementById('NewsText'), '[img]'+name+'[/img] ')
}
function AddImgPrev2(name) {
        insertAtCaret(window.opener.document.getElementById('NewsText'), '[imgprev]'+name+'[/imgprev] ')
}
function AddImgOuter() {
	    if (url = window.prompt("Введите ссылку на изображение", "http://"))
	        {
			    if (!url) alert("Необходимо указать адрес ссылки!")
	            else
	                {
				        insertAtCaret(document.getElementById('NewsText'), '[image]'+url+'[/image]')
                    }
	        }
}
function AddImgOuterLeft() {
	    if (url = window.prompt("Введите ссылку на изображение", "http://"))
	        {
			    if (!url) alert("Необходимо указать адрес ссылки!")
	            else
	                {
				        insertAtCaret(document.getElementById('NewsText'), '[imageleft]'+url+'[/image]')
                    }
	        }
}

function ClBrd(text) {
    while(text.indexOf('<BR>')>=0) text = text.replace('<BR>','\\n');
    if (window.clipboardData) {
    	window.clipboardData.setData('Text', text);
    	alert ('Строка для обращения в приват в чате ТЗ добавлена в буфер обмена.');
    } else {
    	var DummyVariable = prompt('Скопируйте эту строку и используйте ее для обращения в чате ТЗ:',text);
	}
}
function ClBrd2(text){
    while(text.indexOf('<BR>')>=0) text = text.replace('<BR>','\\n');
	if (window.clipboardData) {
		window.clipboardData.setData('Text', text);
		alert ('Номер боя скопирован в буфер обмена.');
	} else {
		var DummyVariable = prompt('Буфер обмена недоступен, скопируйте номер вручную:',text);
	}
}
function ClBrdUdo(text) {
	while(text.indexOf('<BR>')>=0) text = text.replace('<BR>','\\n');
    if (window.clipboardData) {
    	window.clipboardData.setData('Text', text);
    	alert ('Текст скопирован в буфер обмена.');
	} else {
		var DummyVariable = prompt('Буфер обмена недоступен, скопируйте текст вручную:',text);
	}
}

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
 	form.submit();
}

function createWindow(w,h,data) {
	w = (w)?w:400;
	h = (h)?h:'';
    windowStatus = 'create';

	$('#dialogWindow').style.width = w;
	$('#dialogWindow').style.height = h;

	$('#dialogWindow').style.left = Math.ceil((document.body.clientWidth-w)/2) - 24;
    $('#dialogWindow').style.top = document.body.scrollTop + 250;

	$('#dialogWindow').html(data);
	$('#dialogWindow').style.display = 'block';
}

function closeWindow() {
	windowStatus = 'close';
	$('#dialogWindowText').html('');
	$('#dialogWindow').style.display = 'none';
}

var LogWindow=0;
function LogWin(URLStr)	{
	if(LogWindow && !LogWindow.closed) LogWindow.close();
	var actual_url = '/sbtl.ru.html?' + URLStr;
	LogWindow = open(actual_url, 'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,copyhistory=yes,width=1004,height=400');
}
