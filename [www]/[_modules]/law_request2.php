<h1>Заявка на проверку на чистоту перед законом</h1>
<SCRIPT src='/_modules/xhr_emu.js'></SCRIPT>
<center>
<table border="0" cellspacing="0" background="i/bgr-grid-sand.gif" cellpadding="10" style="BORDER: 1px #957850 solid;">
<tr><td>
<table width="100%"><tr><td align="center"><font color="red"><b>Внимание!</b></font><br></td></tr></table>
<center><font color="red"><b>Заявки на проверку одного персонажа подаются не чаще 1 раза в 3 суток.</b></font></center><br>

1. Проверка на чистоту проводится для персонажей не ниже 4 уровня. Проверка необходима для получения гражданства, вступления в клан или покупки недвижимости. Срок действия проверки - 3 суток.<br>
2. Время ожидания выполнения обычной заявки на проверку - до 5 суток, стоимость - 10 медных монет.<br>
3. Стоимость срочных заявок на проверку:<ul>
<li> 12 часов - 50 медных монет
<!--<li> 6 часов - 70 монет-->
<li> 1 час - 100 медных монет (<font color="red"><b><u>ПЕРЕД</u> оплатой свяжитесь с сотрудником лицензионного отдела!!!</b></font>)
</ul>
4. Перед подачей заявки убедитесь, что перевели необходимую сумму на счет <font color="red"><b>84257</b></font><br>
5. Оплата должна быть произведена <b>одним</b> платежом от имени подающегося на проверку<br>
6. В случае неверного указания ника оплата <b>не возвращается</b> и проверка <b>не проводится</b><br>
7. <font color="red"><b>Внимание!</b></font> Необходимо указывать в соответствующем поле <b>полную</b> строку из логов ячейки. <i>(Информация об остатке на счете <b>игнорируется</b>, но полная строка необходима для безошибочного анализа)</i><br>
8. По всем вопросам, относящимся к проверкам на чистоту, вы можете обратиться к сотрудникам <b>лицензионного отдела</b> полиции: <?
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
?> или к <b>начальнику отдела</b> - <?
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
<center><font color="red"><b>ВНИМАНИЕ</b></font></center>
<br>
Промежуток времени между оплатой проверки и подачей заявки не должен превышать 2-х часов. В противном случае перевод считается недействительным, заявка не рассматривается.
<br><br>
Заявки со сроком проверки <b>1 час</b> от персонажей, не связавшихся предварительно с сотрудником лицензионного отдела Полиции, не рассматриваются.
<br><br>
Денежные средства, поступившие на счёт <b>84163</b>, в этих случаях считаются безвозмездной помощью полиции и возврату не подлежат.
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
	            document.all.lr_content.innerHTML="<center><br><br><img src='_imgs/plz_wait.gif' width='300' height='10'><br><br><b>Пожалуйста, подождите...</b></center>";
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
	            main_content.innerHTML="<center><br><br><img src='_imgs/plz_wait.gif' width='300' height='10'><br><br><b>Пожалуйста, подождите...</b></center>";
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
        alert('Извините, не удалось отправить данные.\nПроверьте соединение с интернет и попробуйте еще раз');
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
      Тип:
      <select name="urgency" onChange="urg(this)">
        <option value="0">обычная</option>
        <option value="1">срочная (12 часов)</option>
        <option value="2">срочная (1 час)</option>
<!--        <option value="3">срочная (2 часа)</option> -->
      </select></td>
    <td>
      Причина:
      <select name="reason">
        <option value="0">вступление в клан</option>
        <option value="1">получение гражданства</option>
        <option value="2">покупка недвижимости</option>
      </select></td>
    <td>
      Ник:
      <input type="text" name="nickname">
</td>
  </tr>
</table>
<div id="urg" style="display:none" align="center">
      Сотрудник ЛО, с которым достигнута договоренность о проверке:
      <select name="cop_nick">
		<option value="0" selected>нет</option>
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
	Строка из логов ячейки о переводе:<br>
    <textarea name="log_string" cols="90" rows="3" wrap="VIRTUAL"></textarea>
    <br>
    <input type="button" value="Отправить" onClick="lr_form_subm()">
</div>
</form>
</div>