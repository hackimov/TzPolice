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

function storeCaret(textEl)
{
	if (textEl.createTextRange)
	{
		textEl.caretPos = document.selection.createRange().duplicate();
	}
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


//function insertAtCaret (textEl, text) {
//if (textEl.createTextRange && textEl.caretPos) {
//var caretPos = textEl.caretPos;
//caretPos.text =
//caretPos.text.charAt(caretPos.text.length - 1) == ' ' ?
//text + ' ' : text;
//}
//else textEl.value += text;
//}

function insertAtCaret(dummy, text, spaces, popup)
{
	var textarea;
	
	if (!popup) 
	{
		textarea = document.forms[form_name].elements[text_name];
	} 
	else 
	{
		textarea = opener.document.forms[form_name].elements[text_name];
	}
	if (spaces) 
	{
		text = ' ' + text + ' ';
	}
	
	if (!isNaN(textarea.selectionStart))
	{
		var sel_start = textarea.selectionStart;
		var sel_end = textarea.selectionEnd;

		mozWrap(textarea, text, '')
		textarea.selectionStart = sel_start + text.length;
		textarea.selectionEnd = sel_end + text.length;
	}
	else if (textarea.createTextRange && textarea.caretPos)
	{
		if (baseHeight != textarea.caretPos.boundingHeight) 
		{
			textarea.focus();
			storeCaret(textarea);
		}

		var caret_pos = textarea.caretPos;
		caret_pos.text = caret_pos.text.charAt(caret_pos.text.length - 1) == ' ' ? caret_pos.text + text + ' ' : caret_pos.text + text;
	}
	else
	{
		textarea.value = textarea.value + text;
	}
	if (!popup) 
	{
		textarea.focus();
	}
}

function mozWrap(txtarea, open, close)
{
	var selLength = (typeof(txtarea.textLength) == 'undefined') ? txtarea.value.length : txtarea.textLength;
	var selStart = txtarea.selectionStart;
	var selEnd = txtarea.selectionEnd;
	var scrollTop = txtarea.scrollTop;

	if (selEnd == 1 || selEnd == 2) 
	{
		selEnd = selLength;
	}

	var s1 = (txtarea.value).substring(0,selStart);
	var s2 = (txtarea.value).substring(selStart, selEnd)
	var s3 = (txtarea.value).substring(selEnd, selLength);

	txtarea.value = s1 + open + s2 + close + s3;
	txtarea.selectionStart = selEnd + open.length + close.length;
	txtarea.selectionEnd = txtarea.selectionStart;
	txtarea.focus();
	txtarea.scrollTop = scrollTop;

	return;
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