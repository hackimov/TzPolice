<?
if(AuthStatus==1 && AuthUserName!="")
{
//error_reporting(E_ALL);

function persparse($pers)
	{
		$pers=trim($pers);
	        $userinfo = GetUserInfo($pers);
		if (!$userinfo["error"] && $userinfo['level'] > 0)
			{
			        if ($userinfo['man'] == 0)
	        			{
				            	$pro = $userinfo['pro']."w";
					}
			        else
					{
            					$pro = $userinfo['pro'];
					}
				if (strlen($userinfo['clan']) > 2)
	        			{
	            				$returntxt = "[pers clan={$userinfo['clan']} nick={$userinfo['login']} level={$userinfo['level']} pro={$pro}]";
					}
				else
	        			{
	            				$returntxt = "[pers clan=0 nick={$userinfo['login']} level={$userinfo['level']} pro={$pro}]";
					}
			}
		else
			{	
				$returntxt = "[pers clan=0 nick={$pers} level=1 pro=0]";
			}
		return ($returntxt);
	}
if(@$_REQUEST['do']=="add")
	{
		$title = $_REQUEST['title'];
		$text = $_REQUEST['NewsText'];
if(AuthUserGroup > 1 || (abs(AccessLevel) & AccessNewsEditor)) 
	{
		function replpers($match)
			{
				return persparse($match[1]);
			}
		$text = preg_replace_callback(
		"/\[pers\](.*?)\[\/pers\]/si",
		"replpers",
		$text);
	}
        $date = time();
        $s_mess[0] = "������� ����� ����� �� ����� �������� ����� �������� �����������.";
        $s_mess[1] = "������� ����� �� ����� ��������";
        if(AuthUserGroup > 1 || (abs(AccessLevel) & AccessNewsEditor))
        	{
		        $vis = $_REQUEST['show'];
                $validated = AuthUserId;
            }
        else
        	{
            	$vis = 0;
                $validated = '';
            }
        if ($_REQUEST['our_news'])
			{
				$innernews = 1;
			}
		elseif ($_REQUEST['our_news2'])
			{
				$innernews = 2;
			}
		else
			{
				$innernews = 0;
			}
		$SQL="INSERT INTO news(news_title,news_text,news_date,poster_id,allow_tags,our_news,is_attached,is_visible,markup,checker_id)
        values('".addslashes($title)."','".addslashes($text)."',
        '".time()."','".AuthUserId."','1','".$innernews."','".($_REQUEST['attach']?'1':'0')."','".$vis."','1','".$validated."')";
/*
		$SQL="INSERT INTO news(news_title,news_text,news_date,poster_id,allow_tags,our_news,is_attached,is_visible,markup,checker_id)
        values('".addslashes($title)."','".addslashes($text)."',
        '".time()."','".AuthUserId."','1','".($_REQUEST['our_news']?'1':'0')."','".($_REQUEST['attach']?'1':'0')."','".$vis."','1','".$validated."')";
*/        
        mysql_query($SQL);
       	echo ("<b>������� ������� ���������</b><br><br>".$s_mess[$vis]);
    }
?>
<SCRIPT src='_modules/xhr_js.js'></SCRIPT>
<SCRIPT src='_modules/news_js.js'></SCRIPT>
<script language="JavaScript1.2">
<!--
function str_replace(search, replace, subject) {
    var f = search, r = replace, s = subject;
    var ra = r instanceof Array, sa = s instanceof Array, f = [].concat(f), r = [].concat(r), i = (s = [].concat(s)).length;
 
    while (j = 0, i--) {
        if (s[i]) {
            while (s[i] = (s[i]+'').split(f[j]).join(ra ? r[j] || "" : r[0]), ++j in f){};
        }
    };
 
    return sa ? s : s[0];
}

function loadNick()
	{
		var query = '' + document.getElementById('ins_nick').value;
		var req = new Subsys_JsHttpRequest_Js();
		req.onreadystatechange = function()
        	{
				if (req.readyState == 4)
                	{
						if (req.responseJS)
                        	{
                                if (req.responseJS.res == 'OK')
                                	{
	                                    insertAtCaret(document.getElementById('NewsText'), req.responseText);
	                                    document.getElementById('plzwait').innerHTML = '';
                                    }
                                else
                                	{
	                                    document.getElementById('plzwait').innerHTML = req.responseText;

                                   }
							}
					}
            }
		req.caching = false;
		req.open('POST', '_modules/backends/charinfo.php', true);
		document.getElementById("plzwait").innerHTML = '<center><b>����������, ���������...</b></center>';
		req.send({ n: query });
	}
function preview_news()
	{
		var q_title = '' + document.getElementById('title').value;
	        var q_text = '' + document.getElementById('NewsText').value;
		q_text = str_replace('+','&#43;',q_text);
		var req = new Subsys_JsHttpRequest_Js();
		req.onreadystatechange = function()
        	{
				if (req.readyState == 4)
                	{
//						if (req.responseJS)
//                        	{
                                document.getElementById('preview_area').innerHTML = req.responseText;
//							}
				}
            }
		req.caching = false;
		req.open('POST', '_modules/backends/news_preview.php', true);
		document.getElementById("preview_area").innerHTML = '<center><br>����������, ���������...<br><br></center>';
		req.send({ ti: q_title, te: q_text, ta: "<?=AuthUserName?>" });
	}
var smileWin = 0;
var picsWin = 0;
function smileWindow(obj)
	{
	    if(smileWin)

		{
				if(!smileWin.closed) smileWin.close();
			}
	    win_left = obj.clientX + 15;
	    win_top = obj.clientY + 15;
        smileWin = open('_modules/icons/smiles2.php', 'smiles', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=both,resizable=no,width=350,height=250,scrollbars=1,left='+win_left+', top='+win_top+',screenX='+win_left+',screenY='+win_top);
	}
function picsWindow(obj)
	{

    if(picsWin)
			{
				if(!picsWin.closed) picsWin.close();

		}
	    win_left = obj.clientX + 15;
	    win_top = obj.clientY + 15;
        picsWin = open('_modules/icons/upload2.php', 'pics', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=both,resizable=yes,width=400,height=250,scrollbars=1,left='+win_left+', top='+win_top+',screenX='+win_left+',screenY='+win_top);
	}
//-->
</script>
<!-- Editor area start -->

<table width="100%"  border="0" cellspacing="0" cellpadding="5">

         <tr>
            <td colspan="2"><h1>���������� �������</h1></td>
          </tr>
          <tr>
            <td colspan="2"><form name="News" method="post" action="?act=news_add2">
              <input type="hidden" name="do" value="add">
              <b>���������:</b> <br>
              <input name="title" id="title" type="text" style="width:95%">
              <br>

             <br>
              <b>�����:</b>
              <br>
              ����:
				<select name="fc" onChange="fontstyle('[color=' + this.form.fc.options[this.form.fc.selectedIndex].value + ']', '[/color]','NewsText');this.selectedIndex=0;">
					  <option value="none">�� ���������</option>
					  <option style="color:darkred;" value="darkred">Ҹ���-�������</option>
					  <option style="color:red;" value="red">�������</option>
					  <option style="color:orange;" value="orange">���������</option>
					  <option style="color:brown;" value="brown">����������</option>
					  <option style="color:green;" value="green">������</option>
					  <option style="color:olive;" value="olive">���������</option>
					  <option style="color:blue;" value="blue">�����</option>
					  <option style="color:darkblue;" value="darkblue">Ҹ���-�����</option>
					  <option style="color:indigo;" value="indigo">������</option>
					  <option style="color:violet;" value="violet">����������</option>
					  <option style="color:white;" value="white">�����</option>
				  <option style="color:black;" value="black">׸����</option>
				</select>
				&nbsp;
              <select name="ins_clan" id="ins_clan" size="1" onChange="AddClan(this.form.ins_clan.options[this.form.ins_clan.selectedIndex].value, 'NewsText');this.selectedIndex=0;">
			<option value="none" selected>�����</option>
<?
$smiles_dir="_imgs/clans/";
$d=opendir($smiles_dir);
$counter = 0;
while(($CurFile=readdir($d))!==false) if(is_file("$smiles_dir/$CurFile")) {
	$FileType=GetImageSize("$smiles_dir/$CurFile");
	$CurFile=explode(".",$CurFile);
    if($FileType[2]==1)
    	{
			$clans[$counter] = $CurFile[0];
            $counter++;
        }
}

closedir($d);
natcasesort($clans);
reset($clans);
while (list($key, $val) = each($clans))
	{
         if ($val !== "0")
         	{
			echo ("<option value='".$val."'>".$val."</option>");
            	}
	}
?>
              </select>
              &nbsp;
			  ���:
              <input name="ins_nick" id="ins_nick" type="text" size="16"><input name="in_subm" type="button" class="ed_dark" value="��" onClick="loadNick()">
              <br>
				<div id="plzwait"><br></div>
              <table width="100%" border="0">
              <tr><td width="*">
              <textarea ONSELECT="storeCaret(this);" ONCLICK="storeCaret(this);" ONKEYUP="storeCaret(this);" name="NewsText" id="NewsText" rows="15" wrap="VIRTUAL" style="width:95%"></textarea>
              </td>
              <td width="180">
              <a href="JavaScript:AddSmile('congr')"><img border=0 src="/_imgs/smiles/congr.gif"></a>
<a href="JavaScript:AddSmile('king')"><img border=0 src="/_imgs/smiles/king.gif"></a>
<a href="JavaScript:AddSmile('crazy')"><img border=0 src="/_imgs/smiles/crazy.gif"></a>
<a href="JavaScript:AddSmile('horse')"><img border=0 src="/_imgs/smiles/horse.gif"></a>
<a href="JavaScript:AddSmile('popcorn')"><img border=0 src="/_imgs/smiles/popcorn.gif"></a>
<a href="JavaScript:AddSmile('friday')"><img border=0 src="/_imgs/smiles/friday.gif"></a>
<a href="JavaScript:AddSmile('wink')"><img border=0 src="/_imgs/smiles/wink.gif"></a>
<a href="JavaScript:AddSmile('ok')"><img border=0 src="/_imgs/smiles/ok.gif"></a>
<a href="JavaScript:AddSmile('shuffle')"><img border=0 src="/_imgs/smiles/shuffle.gif"></a>
<a href="JavaScript:AddSmile('mol')"><img border=0 src="/_imgs/smiles/mol.gif"></a>
<a href="JavaScript:AddSmile('boks')"><img border=0 src="/_imgs/smiles/boks.gif"></a>
<a href="JavaScript:AddSmile('budo')"><img border=0 src="/_imgs/smiles/budo.gif"></a>
              </tr></table>
              <a href="javascript:AddTag('[b]','[/b]','NewsText')"><img border="0" src="_imgs/editor/bold.gif" alt="����������" width="24" height="24"></a><a href="javascript:AddTag('[i]','[/i]','NewsText')"><img border="0" src="_imgs/editor/italic.gif" alt="������" width="24" height="24"></a><a href="javascript:AddTag('[u]','[/u]','NewsText')"><img border="0" src="_imgs/editor/underline.gif" alt="������������" width="24" height="24"></a>&nbsp;<a href="javascript:AddTag('[left]','[/left]','NewsText')"><img border="0" src="_imgs/editor/left.gif" alt="�� ������ ����" width="24" height="24"></a><a href="javascript:AddTag('[center]','[/center]','NewsText')"><img border="0" src="_imgs/editor/center.gif" alt="�� ������" width="24" height="24"></a><a href="javascript:AddTag('[right]','[/right]','NewsText')"><img border="0" src="_imgs/editor/right.gif" alt="�� ������� ����" width="24" height="24"></a>&nbsp;<a href="javascript:AddTag('[list]\n[*]\n[*]\n[*]\n','[/list]','NewsText')"><img border="0" src="_imgs/editor/u_list.gif" alt="�������������� ������" width="24" height="24"></a><a href="javascript:AddTag('[list=x]\n[*]\n[*]\n[*]\n','[/list=x]','NewsText')"><img border="0" src="_imgs/editor/o_list.gif" alt="������������ ������" width="24" height="24"></a><a href="javascript:AddTag('[*]','','NewsText')"><img border="0" src="_imgs/editor/list_item.gif" alt="������� ������" width="24" height="24"></a>&nbsp;<a href="#; return false;" onClick="picsWindow(event);"><img border="0" src="_imgs/editor/image1.gif" alt="�����������" width="24" height="24"></a><a href="#; return false;" onClick="AddImgOuter();"><img border="0" src="_imgs/editor/image2.gif" alt="����������� � ������� �����" width="24" height="24"></a><a href="javascript:AddUrl('NewsText')"><img border="0" src="_imgs/editor/hyperlink.gif" alt="������" width="24" height="24"></a><a href="#; return false;" onClick="smileWindow(event);"><img border="0" src="_imgs/editor/smile.gif" alt="��������" width="24" height="24"></a><a href="javascript:AddTag('[small]','[/small]','NewsText')"><img border="0" src="_imgs/editor/small.gif" alt="����������� �����" width="24" height="24"></a>&nbsp;<a href="javascript:AddTag('[quote]','[/quote]','NewsText')"><img border="0" src="_imgs/editor/quote.gif" alt="������" width="75" height="24"></a><a href="javascript:AddHidden('NewsText')"><img border="0" src="_imgs/editor/hidden.gif" alt="������� �����" width="75" height="24"></a><?if(AuthUserGroup > 1 || (abs(AccessLevel) & AccessNewsEditor)) {?><a href="javascript:AddTag('[pers]','[/pers]','NewsText')"><img border="0" src="_imgs/editor/pers.gif" alt="��������" width="50" height="24"></a><?}?>&nbsp;
              <br>
              <a href="javascript:AddPro('0', 'NewsText');"><img border="0" src="_imgs/pro/i0.gif" width="15" height="15"></a><a href="javascript:AddPro('1', 'NewsText');"><img border="0" src="_imgs/pro/i1.gif" alt="������" width="15" height="15"></a><a href="javascript:AddPro('2', 'NewsText');"><img border="0" src="_imgs/pro/i2.gif" alt="�������" width="15" height="15"></a><a href="javascript:AddPro('3', 'NewsText');"><img border="0" src="_imgs/pro/i3.gif" alt="������" width="15" height="15"></a><a href="javascript:AddPro('4', 'NewsText');"><img border="0" src="_imgs/pro/i4.gif" alt="�������" width="15" height="15"></a><a href="javascript:AddPro('5', 'NewsText');"><img border="0" src="_imgs/pro/i5.gif" alt="�������" width="15" height="15"></a><a href="javascript:AddPro('6', 'NewsText');"><img border="0" src="_imgs/pro/i6.gif" alt="��������" width="15" height="15"></a><a href="javascript:AddPro('7', 'NewsText');"><img border="0" src="_imgs/pro/i7.gif" alt="����������" width="15" height="15"></a><a href="javascript:AddPro('8', 'NewsText');"><img border="0" src="_imgs/pro/i8.gif" alt="���������" width="15" height="15"></a><a href="javascript:AddPro('9', 'NewsText');"><img border="0" src="_imgs/pro/i9.gif" alt="����������" width="15" height="15"></a><a href="javascript:AddPro('10', 'NewsText');"><img border="0" src="_imgs/pro/i10.gif" alt="���������" width="15" height="15"></a><a href="javascript:AddPro('11', 'NewsText');"><img border="0" src="_imgs/pro/i11.gif" alt="��������" width="15" height="15"></a><a href="javascript:AddPro('12', 'NewsText');"><img border="0" src="_imgs/pro/i12.gif" alt="�������" width="15" height="15"></a><a href="javascript:AddPro('16', 'NewsText');"><img border="0" src="_imgs/pro/i16.gif" alt="���-�����" width="15" height="15"></a><a href="javascript:AddPro('14', 'NewsText');"><img border="0" src="_imgs/pro/i14.gif" alt="���-�������" width="15" height="15"></a><a href="javascript:AddPro('15', 'NewsText');"><img border="0" src="_imgs/pro/i15.gif" alt="���-������" width="15" height="15"></a><a href="javascript:AddPro('13', 'NewsText');"><img border="0" src="_imgs/pro/i13.gif" alt="���������" width="15" height="15"></a><a href="javascript:AddPro('30', 'NewsText');"><img border="0" src="_imgs/pro/i30.gif" alt="�����" width="15" height="15"></a><a href="javascript:AddPro('26', 'NewsText');"><img border="0" src="_imgs/pro/i26.gif" alt="�����" width="15" height="15"></a><a href="javascript:AddPro('27', 'NewsText');"><img border="0" src="_imgs/pro/i27.gif" alt="�����" width="15" height="15"></a><a href="javascript:AddPro('28', 'NewsText');"><img border="0" src="_imgs/pro/i28.gif" alt="�����" width="15" height="15"></a>
                <br>
			  <a href="javascript:AddPro('0w', 'NewsText');"><img border="0" src="_imgs/pro/i0w.gif" width="15" height="15"></a><a href="javascript:AddPro('1w', 'NewsText');"><img border="0" src="_imgs/pro/i1w.gif" alt="������" width="15" height="15"></a><a href="javascript:AddPro('2w', 'NewsText');"><img border="0" src="_imgs/pro/i2w.gif" alt="�������" width="15" height="15"></a><a href="javascript:AddPro('3w', 'NewsText');"><img border="0" src="_imgs/pro/i3w.gif" alt="������" width="15" height="15"></a><a href="javascript:AddPro('4w', 'NewsText');"><img border="0" src="_imgs/pro/i4w.gif" alt="�������" width="15" height="15"></a><a href="javascript:AddPro('5w', 'NewsText');"><img border="0" src="_imgs/pro/i5w.gif" alt="�������" width="15" height="15"></a><a href="javascript:AddPro('6w', 'NewsText');"><img border="0" src="_imgs/pro/i6w.gif" alt="��������" width="15" height="15"></a><a href="javascript:AddPro('7w', 'NewsText');"><img border="0" src="_imgs/pro/i7w.gif" alt="����������" width="15" height="15"></a><a href="javascript:AddPro('8w', 'NewsText');"><img border="0" src="_imgs/pro/i8w.gif" alt="���������" width="15" height="15"></a><a href="javascript:AddPro('9w', 'NewsText');"><img border="0" src="_imgs/pro/i9w.gif" alt="����������" width="15" height="15"></a><a href="javascript:AddPro('10w', 'NewsText');"><img border="0" src="_imgs/pro/i10w.gif" alt="���������" width="15" height="15"></a><a href="javascript:AddPro('11w', 'NewsText');"><img border="0" src="_imgs/pro/i11w.gif" alt="��������" width="15" height="15"></a><a href="javascript:AddPro('12w', 'NewsText');"><img border="0" src="_imgs/pro/i12w.gif" alt="�������" width="15" height="15"></a><a href="javascript:AddPro('16w', 'NewsText');"><img border="0" src="_imgs/pro/i16w.gif" alt="���-�����" width="15" height="15"></a><a href="javascript:AddPro('14w', 'NewsText');"><img border="0" src="_imgs/pro/i14w.gif" alt="���-�������" width="15" height="15"></a><a href="javascript:AddPro('15w', 'NewsText');"><img border="0" src="_imgs/pro/i15w.gif" alt="���-������" width="15" height="15"></a><a href="javascript:AddPro('13w', 'NewsText');"><img border="0" src="_imgs/pro/i13w.gif" alt="���������" width="15" height="15"></a><a href="javascript:AddPro('30w', 'NewsText');"><img border="0" src="_imgs/pro/i30w.gif" alt="�����" width="15" height="15"></a>
                <br>
                <input name="prv_subm" type="button" value="��������������� ��������" onClick="preview_news();">
                <input name="add_subm" type="submit" value="��������">
                <?
                if(AuthUserGroup > 1 || (abs(AccessLevel) & AccessNewsEditor))
                	{
                ?>
                <br>
                <input name="show" type="radio" value="0" checked>
                �� ���������� �������<br>
                <input name="show" type="radio" value="1">
                ���������� ������� <br>
                <input name="attach" type="checkbox" value="1">
                ���������� �������<br>
                <?if (AuthUserClan=='police' || AuthUserClan=='Police Academy' || AuthUserClan=='Tribunal') {?>
				<input type="checkbox" name="our_news" value="1"> ���������� ������� (police+PA only)<br>

			<?	}
			if (AuthUserClan=='police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy' || AuthUserClan=='Military Police' || AuthUserClan=='Tribunal') {?>
				<input type="checkbox" name="our_news2" value="1"> ���������� �������<br>

			<?	}
                }?></form>
              </td>
          </tr>
        </table>
<!-- Editor area end -->
        <br>
<!-- Preview area start -->
<b>��������������� ��������</b>
<div id="preview_area">...</div>
<!-- Preview area end -->
<?
}

else echo ("<b>������ ��������</b>");
?>