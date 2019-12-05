<h1>Тотализатор от Полицейского управления</h1>
<?
    if ($_REQUEST['makebet_ok'])
    {
      $bets = '';
      $i = 1;
      $countbets = 0;
      foreach ($_REQUEST['win'] as $key => $value)
      {
      	if ($value == 3)
      		{
		      $countbets++;
      		}
        if ($i == 1)
        {
          $bets .= $value;
          $i = 0;
        }
        else
          $bets .= ':'.$value;
      }
      	if ($countbets == 15)
      		{
		        $sql = 'INSERT INTO total_bets SET r_id="'.$_REQUEST['id'].'", nick="'.$_REQUEST['nick'].'",
                bets="'.$bets.'", sum="'.$_REQUEST['sum'].'"';
       			mysql_query($sql);
       			echo ("Ставка принята!");
       		}
       	else
       		{
				echo ("Необходимо указать исход ровно 15 событий! Ставка не принята!");
       		}

    }
?>
<SCRIPT src='_modules/xhr_js.js'></SCRIPT>
<OBJECT id="tz" codeBase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" height="1" width="1" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><PARAM NAME="movie" VALUE="http://tzpolice.ru/_imgs/auth.swf"><PARAM NAME="wmode" VALUE="transparent">
<embed src="http://tzpolice.ru/_imgs/auth.swf" wmode="transparent" width="1" height="1" swLiveConnect="true" id="tz" name="tz" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</OBJECT>
<script language="JavaScript1.2">
<!--
function tz_DoFSCommand(command, args) {
	var tmp = args.split("\t");
	if (command == "OK")
    	{
            var pers_nick = '' + tmp[0];
			var pers_sid = '' + tmp[1];
			var pers_city = '' + tmp[2];
			document.getElementById("betnick").value=pers_nick;
	        var req2 = new Subsys_JsHttpRequest_Js();
            var req3 = new Subsys_JsHttpRequest_Js();
	        req2.onreadystatechange = function()
	            {
	                if (req2.readyState == 4)
	                    {
                           if (req2.responseJS)
                           {
                            if (req2.responseJS.res == 'nousernamewasdetectedsonoactionshouldbemade')
                            	{
                                    document.getElementById('err2').style.display='';
									document.getElementById('betsubm').disabled=true;
	                        	}
                            else
                            	{
									document.getElementById('betsubm').disabled=false;
                                	document.getElementById('r_from').value=req2.responseJS.res;
                                    document.getElementById('r_from_sid').value=pers_sid;
                                    document.getElementById('r_from_city').value=pers_city;
                                    document.getElementById('subm').style.display='';
                                    document.getElementById('requests_list').innerHTML=req2.responseText;
                                }
                           }
	                    }
	            }
	        req2.caching = false;
	        req2.open('POST', '_modules/backends/pers_request_auth.php', true);
	        req2.send({ pn: pers_nick, ps: pers_sid, pc: pers_city });
        }
	else
    	{
			men = document.getElementById('err2');
			men.style.display='';
        }
}
if (navigator.appName.indexOf("Microsoft") != -1) {// Hook for Internet Explorer.
	document.write('<script language=\"VBScript\"\>\n');
	document.write('On Error Resume Next\n');
	document.write('Sub tz_FSCommand(ByVal command, ByVal args)\n');
	document.write('	Call tz_DoFSCommand(command, args)\n');
	document.write('End Sub\n');
	document.write('</script\>\n');
}
function checkBet()
	{
		var countnobet=0;
		for (i=1; i<=30; i++)
			{
				var curid = 'win3'+i;
				var currad = document.getElementById(curid);
				if (currad.checked)
					{
						countnobet++;
					}
			}
		if (countnobet > 15)
			{
				var needmore = countnobet-15;
				alert ("Вы должны указать результат ровно 15 событий. Укажите еще "+needmore+"!");
				return false;
			}
		else if (countnobet < 15)
			{
				var needmore = 30-countnobet-15;
				alert ("Вы должны указать результат ровно 15 событий. Уберите "+needmore+" ставки!");
				return false;
			}
		else return true;
	}
// End -->
</script>
<div id="err2" style="display:none" align="center">
<font color="red" size="+1"><b>Ошибка авторизации!</b></font><br><br>
чтобы сделать ставку войдите в игру своим персонажем.<br>Внимание! Авторизация с помощью модифицированного клиента и при неверно сконфигурированном firewall как правило невозможна!<br><br>
<input type="button" value="Повторить попытку" onClick="javascript: location.reload(true)"><br><br>
</div>
<?
    echo '<h2>Сделать ставку</h2>';
    echo '<form action="/index.php" method="post" onSubmit="return (checkBet())">';
	$sql = "SELECT * FROM total_round WHERE archived='0' order by id desc limit 1;";
	$result = mysql_fetch_array(mysql_query($sql));
    $sql = 'SELECT * FROM total_games WHERE r_id='.$result['id'].' order by id';
    $r = mysql_query($sql);
//    if (mysql_num_rows($r) != 15)
//    {
//      echo '<b>Ошибка. В раунде должно быть 15 игр</b>';
//    }
//    else
//    {
//      echo 'Ник: <input type="text" id="betnick" name="nick" disabled>&nbsp;&nbsp;';
//      echo 'Лог перевода: <input type="text" name="sum"><br><br><br>';
      echo '<table border=1 bordercolor="#666666" cellpadding=3 cellspacing=3>';
?>
<tr>
  <td rowspan=2 valign=top width=10>№</td>
  <td rowspan=2 valign=top>Первая команда</td>
  <td rowspan=2 valign=top>Вторая команда</td>
  <td colspan=4 align=center>Результат</td>
</tr>
<tr align=center>
  <td>1</td>
  <td>X</td>
  <td>2</td>
  <td>---</td>
</tr>
<?

      $i = 0;
      while ($row = mysql_fetch_assoc($r))
      {
        echo '<tr>';
          echo '<td width=10>'.++$i.'</td>';
          echo '<td>'.$row['name1'].'</td>';
          echo '<td>'.$row['name2'].'</td>';
          echo '<td><input type="radio" id="win0'.$i.'" name="win['.$i.']" value="0"';
//          if ($makebetz[$i] == '1')
//            echo ' checked';
          echo '></td>';
          echo '<td><input type="radio" id="win1'.$i.'" name="win['.$i.']" value="1"';
//          if ($makebetz[$i] == 'x' or $makebetz[$i] == 'X' or $makebetz[$i] == 'х' or $makebetz[$i] == 'Х')
//            echo ' checked';
          echo '></td>';
          echo '<td><input type="radio" id="win2'.$i.'" name="win['.$i.']" value="2"';
//          if ($makebetz[$i] == '2')
//            echo ' checked';
          echo '></td>';
          echo '<td><input type="radio" id="win3'.$i.'" name="win['.$i.']" value="3" checked';
//          if ($makebetz[$i] == '3')
//            echo ' checked';
          echo '></td>';
        echo '</tr>';
      }
      echo '</table>';
      echo '<input type="hidden" name="act" value="total2">';
      echo '<input type="hidden" name="sec" value="bets">';
      echo '<input type="hidden" name="id" value="'.$_REQUEST['id'].'">';
      echo '<input type="submit" id="betsubm" name="makebet_ok" value="Отправить" disabled>';
      echo '</form>';
?>
<br><br>
<hr>
<br><br>
<B>Правила тотализатора:</B><br>
1. Сделать ставку может любой персонаж.<br>
2. Минимальная ставка 50 медных монет, максимальная не ограничена, шаг ставки 50 медных монет.<br>
3. Ставки принимаются до начала первого матча тотализатора.<br>
4. Выплата выигрышей происходит на следующий день.<br>
5. Если ни один из персонажей не угадал результаты 15 (14, 13, 12, 11, 10) событий в очередном розыгрыше тотализатора, вся, не разыгранная сумма поступает в Джек-пот следующего розыгрыша.<br>
<br>
<B>Как правильно сделать ставку:</B><br>
1. Перевести медные монеты на счет <B>88999</B>.<br>
2. Список из 15 событий с их исходами, который заполняет персонаж, чтобы сделать ставку. Победа первой команды обозначается символом «1»; победа второй команды – «2»; ничья – «Х».<br>
3. На форуме сделать ваш прогноз на 15 событий и лог перевода медных монет.<br><br>
<B>Описание тотализатора и выплата выигрыша победителям:</B><br><br>
1. Выигрывают те, кто угадал события: 15 из 15, 14 из 15, 13 из 15, 12 из 15, 11 из 15 и 10 из 15. <br>
2. 15% - от общей суммы переводятся в Фонд Выплаты персонажам, пострадавшим от преступлений в экономической сфере.<br>
3. Те, кто угадал 15 из 15, получают 5% от общей суммы, пропорционально ставке, если победитель не один, а двое или больше. <br>
Те, кто угадал 15 из 15 и 14 из 15, получают 5% от общей суммы, пропорционально ставке, если победитель не один, а двое или больше. <br>
Те, кто угадал 15 из 15, 14 из 15 и 13 из 15, получают 5% от общей суммы, пропорционально ставке, если победитель не один, а двое или больше. <br>
Те, кто угадал 15 из 15, 14 из 15, 13 из 15 и 12 из 15, получают 5% от общей суммы, пропорционально ставке, если победитель не один, а двое или больше. <br>
Те, кто угадал 15 из 15, 14 из 15, 13 из 15, 12 из 15 и 11 из 15, получают 20% от общей суммы, пропорционально ставке, если победитель не один, а двое или больше. <br>
Те, кто угадал 15 из 15, 14 из 15, 13 из 15, 12 из 15, 11 из 15 и 10 из 15, получают 45% от общей суммы, пропорционально ставке, если победитель не один, а двое или больше. <br>
4. Если ни кто не угадал 15 из 15, то 5% от общей суммы идут в ДЖЕКПОТ, который переносится на следующий розыгрыш. <br><br>

5. Приведу пример, как рассчитывается данная схема: <br>
Общая сумма составляет 100000 медных монет.<br><br>

15 из 15, сыграли две ставки, один поставил 50 м.м., а второй 100 м.м..<br>
Выигрыш их составит: 5% от 100000 м.м., т.е. 5000 м.м..<br>
Так как выиграли два игрока, то 5000 м.м. распределяется пропорционально ставке, т.е. 1665 м.м. и 3335 м.м.. <br>
14 из 15 не угадал ни кто. Следовательно, 5% от общей суммы распределяется между двумя игроками, кто угадал 15 из 15.<br>
Если есть, кто выиграл 14 из 15 допустим 1 человек, то выигрыш делиться между тремя участниками, т.е. кто угадал 14 из 15 и 15 из 15. <br>
13 из 15, 12 из 15, 11 из 15 действует по той же схеме. <br><br>

Подробнее опишу &nbsp;выплату медных монет при событии 10 из 15: <br>
Пример: <br>
15 из 15 выиграло 2(а,б) человека, ставки 50 м.м. и 100 м.м. <br>
14 из 15 не угадал ни кто. <br>
13 из 15 не угадал ни кто. <br>
12 из 15 угадало 1(в) человек, ставка 100 м.м.. <br>
11 из 15 угадал 1(г) человек, ставка 100 м.м.. <br>
10 из 15 угадало 2(д,e) человека, ставки 600 м.м. и 200м.м..<br><br>

а) 1665(5%(15/15)), 1665(5%(14/15), 1665(5%(13/15)), 1000(5%(12/15)), 2856(20%(11/15)), 1959(45%(10/15)). <br>
б) 3335(5%(15/15)), 3335(5%(14/15), 3335(5%(13/15)), 2000(5%(12/15)), 5714(20%(11/15)), 3920(45%(10/15)). <br>
в) 2000(5%(12/15)), 5714(20%(11/15)), 3920(45%(10/15)). <br>
г) 5714(20%(11/15)), 3920(45%(10/15)). <br>
д) 23450(45%(10/15)).<br>
е) 7830(45%(10/15)). <br><br>

В итоге получаются выигрыши:<br>
Персонаж (а) получил - 10810 м.м.<br>
Персонаж (б) получил - 21639 м.м.<br>
Персонаж (в) получил - 11634 м.м. <br>
Персонаж (г) получил - 9634 м.м.<br>
Персонаж (д) получил - 23450 м.м.<br>
Персонаж (е) получил - 7830 м.м. <br><br>

Итого 85000 м.м.<br>
15000 м.м. – в Фонд Выплаты персонажам, пострадавшим от преступлений в экономической сфере.<br> <br>

<b>Дополнительные призы:</b><br> Раз в месяц будут разыгрываться <b>4 Rating Signs</b>.<br>
Кто угадал 15 из 15, получит 6 Rating Signs, если победителей двое или больше, то приз получает тот,  у кого ставка больше, если ставка одинакова, то победитель определяется путем дополнительного розыгрыша из 5 событий.<br>
Кто угадал 14 из 15, получит 2 Rating Signs, если победителей двое или больше, то приз получает тот,  у кого ставка больше, если ставка одинакова, то победитель определяется путем дополнительного розыгрыша из 5 событий.<br>
Если в течение месяца никто не угадал 15 из 15 и 14 из 15, то в следующем месяце, к 4 Rating Signs прибавятся еще 4 Rating Signs, т.е. кто угадает 15 из 15, получит 6 Rating Signs, а тот, кто 14 из 15 получит 2 Rating Signs.<br><br>

&nbsp; <B>

Дата &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;События &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;1 &nbsp; &nbsp; &nbsp; Х &nbsp; &nbsp; &nbsp; 2</B><br>1. &nbsp; 22.04.06 &nbsp; &nbsp;ЦСКА (Москва) - Сатурн (Раменское)<br>2. &nbsp; 22.04.06 &nbsp; &nbsp;Амкар (Пермь) - Томь (Томск)<br>3. &nbsp; 22.04.06 &nbsp; &nbsp;Луч-Энергия (Владивосток) - Торпедо (Москва)<br>4. &nbsp; 23.04.06 &nbsp; &nbsp;Спартак (Москва) - ФК Москва (Москва)<br>5. &nbsp; 23.04.06 &nbsp; &nbsp;Ростов (Ростов-на-Дону) - Шинник (Ярославль)<br>6. &nbsp; 23.04.06 &nbsp; &nbsp;Крылья Советов (Самара) - Динамо (Москва)<br>7. &nbsp; 23.04.06 &nbsp; &nbsp;Спартак (Нальчик) - Зенит (С.Петербург)<br>8. &nbsp; 22.04.06 &nbsp; &nbsp;Арсенал – Тоттенхэм<br>9. &nbsp; 22.04.06 &nbsp; &nbsp;Болтон - Чарльтон<br>10. &nbsp;22.04.06 &nbsp; &nbsp;Гамбург – Байер<br>11. &nbsp;23.04.06 &nbsp; &nbsp;Вердер – Шальке04<br>12. &nbsp;23.04.06 &nbsp; &nbsp;Ливорно – Пелермо<br>13. &nbsp;23.04.06 &nbsp; &nbsp;Ювентус – Лацио<br>14. &nbsp;23.04.06 &nbsp; &nbsp;Атлетик – Валенсия<br>15. &nbsp;23.04.06 &nbsp; &nbsp;Монако – Лион<br><br><B>Пример, как следует сделать ставку:</B><br><br>1. 1<br>2. Х<br>3. 1<br>4. Х<br>5. 1<br>6. 1<br>7. 2<br>8. 1<br>9. 1<br>10. 2<br>11. Х<br>12. Х<br>13. 1<br>14. 2<br>15. 1<br><br>18:19 Персонаж 'Снежный барс' перевел на счет 88999 Медные монеты на сумму: 50
