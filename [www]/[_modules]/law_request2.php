<h1>������ �� �������� �� ������� ����� �������</h1>
<SCRIPT src='/_modules/xhr_emu.js'></SCRIPT>
<center>
<table border="0" cellspacing="0" background="i/bgr-grid-sand.gif" cellpadding="10" style="BORDER: 1px #957850 solid;">
<tr><td>
<table width="100%"><tr><td align="center"><font color="red"><b>��������!</b></font><br></td></tr></table>
<center><font color="red"><b>������ �� �������� ������ ��������� �������� �� ���� 1 ���� � 3 �����.</b></font></center><br>

1. �������� �� ������� ���������� ��� ���������� �� ���� 4 ������. �������� ���������� ��� ��������� �����������, ���������� � ���� ��� ������� ������������. ���� �������� �������� - 3 �����.<br>
2. ����� �������� ���������� ������� ������ �� �������� - �� 5 �����, ��������� - 10 ������ �����.<br>
3. ��������� ������� ������ �� ��������:<ul>
<li> 12 ����� - 50 ������ �����
<!--<li> 6 ����� - 70 �����-->
<li> 1 ��� - 100 ������ ����� (<font color="red"><b><u>�����</u> ������� ��������� � ����������� ������������� ������!!!</b></font>)
</ul>
4. ����� ������� ������ ���������, ��� �������� ����������� ����� �� ���� <font color="red"><b>84257</b></font><br>
5. ������ ������ ���� ����������� <b>�����</b> �������� �� ����� ����������� �� ��������<br>
6. � ������ ��������� �������� ���� ������ <b>�� ������������</b> � �������� <b>�� ����������</b><br>
7. <font color="red"><b>��������!</b></font> ���������� ��������� � ��������������� ���� <b>������</b> ������ �� ����� ������. <i>(���������� �� ������� �� ����� <b>������������</b>, �� ������ ������ ���������� ��� ������������� �������)</i><br>
8. �� ���� ��������, ����������� � ��������� �� �������, �� ������ ���������� � ����������� <b>������������� ������</b> �������: <?
$SQL = "SELECT name FROM sd_cops WHERE dept=18 AND chief=0";
$result = mysql_query($SQL) or die (mysql_error());
  if (mysql_num_rows($result) > 0 ) {
  	 $tmp = "";
     while (list($name) = mysql_fetch_row($result))
     	{
		    echo($tmp."<img src='_imgs/clans/police.gif'><b><a href='http://www.timezero.ru/info.html?".$name."' target='_blank'>".$name."</a></b>");
            $tmp = ", ";
        }
  	}
?> ��� � <b>���������� ������</b> - <?
$SQL = "SELECT name FROM sd_cops WHERE dept=18 AND chief=1";
$result = mysql_query($SQL);
  if (mysql_num_rows($result) > 0 ) {
  	 $tmp = "";
     while (list($name) = mysql_fetch_row($result))
     	{
		    echo($tmp."<img src='_imgs/clans/police.gif'><b><a href='http://www.timezero.ru/info.html?".$name."' target='_blank'>".$name."</a></b>");
            $tmp = ", ";
        }
  	}
?>.
<!--
<hr>
<center><font color="red"><b>��������</b></font></center>
<br>
���������� ������� ����� ������� �������� � ������� ������ �� ������ ��������� 2-� �����. � ��������� ������ ������� ��������� ����������������, ������ �� ���������������.
<br><br>
������ �� ������ �������� <b>1 ���</b> �� ����������, �� ����������� �������������� � ����������� ������������� ������ �������, �� ���������������.
<br><br>
�������� ��������, ����������� �� ���� <b>84163</b>, � ���� ������� ��������� ������������� ������� ������� � �������� �� ��������.
<hr>
-->
</td></tr>
</table>
</center>
<script language="Javascript" type="text/javascript">
<!--
function lr_form_subm()
	{
		if(window.ActiveXObject)
        	{
				alert ("using emulation");
            	old_htm = document.all.lr_content.innerHTML;
	            req = new XMLHttpRequest();
	            req.onreadystatechange = processReqChange;
	            req.open("POST", "_modules/l_r_xhr.php", true);
	            req.setRequestHeader("urgency", document.lr.urgency.options[document.lr.urgency.selectedIndex].value);
	            req.setRequestHeader("reason", document.lr.reason.options[document.lr.reason.selectedIndex].value);
	            req.setRequestHeader("nickname", document.lr.nickname.value);
	            req.setRequestHeader("log_string", document.lr.log_string.value);
	            req.setRequestHeader("cop_nick", document.lr.cop_nick.value);
	            document.all.lr_content.innerHTML="<center><br><br><img src='_imgs/plz_wait.gif' width='300' height='10'><br><br><b>����������, ���������...</b></center>";
	            req.setRequestHeader("step", "1");
	            tmout = setTimeout('xhr_error()',20000);
	            req.send(null);
            }
        else
        	{
				alert ("using native support");
                main_content = document.getElementById('lr_content');
                old_htm = main_content.innerHTML;
	            req = new XMLHttpRequest();
                if not (req) {alert ("some error");}
	            req.onreadystatechange = processReqChange;
            	url = "_modules/l_r_xhr.php?urgency="+document.lr.urgency.options[document.lr.urgency.selectedIndex].value
                +"&reason="+document.lr.reason.options[document.lr.reason.selectedIndex].value
                +"&nickname="+document.lr.nickname.value
                +"&log_string="+document.lr.log_string.value
                +"&cop_nick="+document.lr.cop_nick.value+"&step=1";
	            main_content.innerHTML="<center><br><br><img src='_imgs/plz_wait.gif' width='300' height='10'><br><br><b>����������, ���������...</b></center>";
                req.open("GET", url, true);
                tmout = setTimeout('xhr_error()',20000);
	            req.send(null);
            }
    }
function processReqChange()
	{
		if(window.ActiveXObject)
        	{
	            if (req.readyState == 4)
	                {
	                    clearTimeout(tmout);
	                    document.all.lr_content.innerHTML=req.responseText;
//	                    alert("There were no problems retrieving the XML data:\n" + req.responseText);
	                }
            }
        else
        	{
	            if (req.readyState == 4)
	                {
						if (req.status == 200)
                        	{
                                main_content = document.getElementById('lr_content');
                                clearTimeout(tmout);
	                            main_content.innerHTML=req.responseText;
	                            alert("There were no problems retrieving the XML data:\n" + req.responseText);
                            }
	                }
            }
    }
function xhr_error()
	{
		main_content = document.getElementById('lr_content');
        clearTimeout(tmout);
        main_content.innerHTML = old_htm;
        alert('��������, �� ������� ��������� ������.\n��������� ���������� � �������� � ���������� ��� ���');
    }
<!--
function urg(obj){
	men = document.getElementById('urg');
  if (obj.options[obj.selectedIndex].value == "2")
  	{
		if(men.style.display=='none') men.style.display='';
	}
  else
  	{
		if(men.style.display=='') men.style.display='none';
	}
}
//-->
</script>
<div id="lr_content">
<form name="lr">
<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
  <tr>
    <td>
      ���:
      <select name="urgency" onChange="urg(this)">
        <option value="0">�������</option>
        <option value="1">������� (12 �����)</option>
        <option value="2">������� (1 ���)</option>
<!--        <option value="3">������� (2 ����)</option> -->
      </select></td>
    <td>
      �������:
      <select name="reason">
        <option value="0">���������� � ����</option>
        <option value="1">��������� �����������</option>
        <option value="2">������� ������������</option>
      </select></td>
    <td>
      ���:
      <input type="text" name="nickname">
</td>
  </tr>
</table>
<div id="urg" style="display:none" align="center">
      ��������� ��, � ������� ���������� �������������� � ��������:
      <select name="cop_nick">
		<option value="0" selected>���</option>
<?
$SQL = "SELECT name FROM sd_cops WHERE dept=18";
$result = mysql_query($SQL) or die (mysql_error());
  if (mysql_num_rows($result) > 0 ) {
  	 $tmp = "";
     while (list($name) = mysql_fetch_row($result))
     	{
		    echo("<option value='".$name."'>".$name."</option>");
        }
  	}
?>
      </select>
</div>
<div align="center"><br>
	������ �� ����� ������ � ��������:<br>
    <textarea name="log_string" cols="90" rows="3" wrap="VIRTUAL"></textarea>
    <br>
    <input type="button" value="���������" onClick="lr_form_subm()">
</div>
</form>
</div>