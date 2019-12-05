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