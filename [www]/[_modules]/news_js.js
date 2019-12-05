// Check for Browser & Platform for PC & IE specific bits
// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version
var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
var is_moz = 0;
var is_mac = (clientPC.indexOf("mac")!=-1);
var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var set_on = false;
var IE = navigator.appName.indexOf("Microsoft") != -1;
var nIE = (IE && parseFloat(navigator.appVersion.substring(navigator.appVersion.indexOf("MSIE ") + 5))>=6);
var set_on = false;

// From http://www.massless.org/mozedit/
function mozWrap(txtarea, open, close)
	{
	    var selLength = txtarea.textLength;
	    var selStart = txtarea.selectionStart;
	    var selEnd = txtarea.selectionEnd;
	    if (selEnd == 1 || selEnd == 2)
	        selEnd = selLength;
	    var s1 = (txtarea.value).substring(0,selStart);
	    var s2 = (txtarea.value).substring(selStart, selEnd)
	    var s3 = (txtarea.value).substring(selEnd, selLength);
	    txtarea.value = s1 + open + s2 + close + s3;
	    return;
	}
function AddHidden(tid)
	{
	var i=Math.round(Math.random()*999999);
	//var open="<a href=\"javascript:{}\" onClick=\"javascript:sh('l"+i+"');\">ТЕКСТ ССЫЛКИ</a><div id=\"l"+i+"\" style=\"display:none\">";
	//var close="</div>";
	var open="[spoyler id=l"+i+" title='ТЕКСТ ССЫЛКИ']";
	var close="[/spoyler]";	
	AddTag(open, close, tid);
}
function storeCaret (textEl) {
if (textEl.createTextRange)
textEl.caretPos = document.selection.createRange().duplicate();
}
function insertAtCaret (textEl, text) {
if (textEl.createTextRange && textEl.caretPos) {
var caretPos = textEl.caretPos;
caretPos.text =
caretPos.text.charAt(caretPos.text.length - 1) == ' ' ?
text + ' ' : text;
}
else textEl.value += text;
}
function fontstyle(opn, cls, tid) {
    var txtarea = document.getElementById(tid);
	if ((clientVer >= 4) && is_ie && is_win) {
		theSelection = document.selection.createRange().text;
		if (!theSelection) {
			txtarea.value += opn + cls;
			txtarea.focus();
			return;
		}
		document.selection.createRange().text = opn + theSelection + cls;
		txtarea.focus();
		return;
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
	{
		mozWrap(txtarea, opn, cls);
		return;
	}
	else
	{
		txtarea.value += opn + cls;
		txtarea.focus();

}
	storeCaret(txtarea);
}
function AddTag(opn, cls, tid) {
    var txtarea = document.getElementById(tid);
	if ((clientVer >= 4) && is_ie && is_win) {
		theSelection = document.selection.createRange().text;
		if (!theSelection) {
			txtarea.value += opn + cls;
			txtarea.focus();
			return;
		}
		document.selection.createRange().text = opn + theSelection + cls;
		txtarea.focus();
		return;
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
	{
		mozWrap(txtarea, opn, cls);
		return;
	}
	else
	{
		txtarea.value += opn + cls;
		txtarea.focus();
	}
	storeCaret(txtarea);
}
function AddUrl(tid)
	{
	    if (url = window.prompt("Введите адрес ссылки", ""))
	        {
			    if (!url) alert("Необходимо указать адрес ссылки!")
	            else
	                {
						sig = window.prompt("Введите подпись ссылки (не обязательно)", "");
                        if (!sig)
                        	{
						        insertAtCaret(document.getElementById(tid), '[url]'+url+'[/url]')
                            }
                        else
                        	{
						        insertAtCaret(document.getElementById(tid), '[url='+url+']'+sig+'[/url]')
                            }
	                }
	        }
	}
function AddClan(clan, tid) {
        insertAtCaret(document.getElementById(tid), '[clan='+clan+']')
}

function AddPro(pro, tid) {
        insertAtCaret(document.getElementById(tid), '[pro='+pro+']')
}
function preview(path,img,w,h) {
        h+=30;
	    w+=20;
        ss=window.open("pic/news_upload/"+path+"/"+img+"","show"+w+h,"height="+h+",width="+w+",status=0,toolbar=0,menubar=0,location=0,top=5,left=5");
}
function copyQ()
{
txt=''
if (document.getSelection) {txt=document.getSelection()}
else if (document.selection) {txt=document.selection.createRange().text;}
txt='[q]'+txt+'[/q]\n'
}
function comm_q(nick)
	{
		var txt='';
		if (document.getSelection) {txt=document.getSelection()}
		else if (document.selection) {txt=document.selection.createRange().text;}
        if (txt == '')
        	{
				txt=nick;
            }
        else
        	{
		        txt=nick+" писал(а): \n[quote]"+txt+'[/quote]\n';
            }
        insertAtCaret(document.getElementById('NewsText'), txt)
	}
function AddSmile2(smile) {
        insertAtCaret(window.opener.document.getElementById('NewsText'), ':'+smile+': ')
	}