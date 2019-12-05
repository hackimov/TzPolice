<h1>Заявка на проверку на чистоту перед законом</h1>

<table border="0" cellspacing="0" background="i/bgr-grid-sand.gif" cellpadding="10" style="BORDER: 1px #957850 solid;">
<tr><td>
<table width="100%"><tr><td align="center"><font color="red"><b>Внимание!</b></font><br></td></tr></table>
<center><font color="red"><b>Заявки на проверку одного персонажа подаются не чаще 1 раза в 3 суток.</b></font></center><br>

0. Ознакомиться с правилами прохождения проверки на чистоту следует в соответствующем <a href="http://www.timezero.ru/cgi-bin/forum.pl?a=E&c=88601064&m=1" target=_blank>топике</a> форума.<br>
1. Проверка на чистоту проводится для персонажей не ниже 4 уровня. Проверка необходима для получения гражданства, вступления в клан или покупки недвижимости. Срок действия проверки - 3 суток.<br>
2. Время ожидания выполнения обычной заявки на проверку - до 5 суток, стоимость - 10 медных монет.<br>
3. Стоимость срочных заявок на проверку:<ul>
<li> 12 часов - 100 медных монет
<li> 1 час - 300 медных монет (<font color="red"><b><u>ПЕРЕД</u> оплатой свяжитесь с сотрудником лицензионного отдела!!!</b></font>)
</ul>
4. По всем вопросам, относящимся к проверкам на чистоту, вы можете обратиться к сотрудникам <b>лицензионного отдела</b> полиции: <?

$SQL = "SELECT name FROM sd_cops WHERE dept=18 AND chief=0";
$result = mysql_query($SQL) or die (mysql_error());

if (mysql_num_rows($result) > 0 ) {
	$tmp = "";

	while (list($name) = mysql_fetch_row($result)) {
		echo($tmp."<img src='_imgs/clans/police.gif'><b><a href='http://www.timezero.ru/info.html?".$name."' target='_blank'>".$name."</a></b>");
		$tmp = ", ";
	}
}

?> или к <b>начальнику отдела</b> - <?

$SQL = "SELECT name FROM sd_cops WHERE dept=18 AND chief=1";
$result = mysql_query($SQL);

if (mysql_num_rows($result) > 0 ) {
	$tmp = "";

	while (list($name) = mysql_fetch_row($result)) {
		echo($tmp."<img src='_imgs/clans/police.gif'><b><a href='http://www.timezero.ru/info.html?".$name."' target='_blank'>".$name."</a></b>");
		$tmp = ", ";
		$nach = $name;
	}
}
?>
<br>
Список сотрудников полиции онлайн с указанием отдела доступен по адресу <a href="http://www.tzpolice.ru/?act=public_posts">http://www.tzpolice.ru/?act=public_posts</a>
<br>
</td></tr>

</table><BR>
<?
	function police_online_view_999 ($connection){
		$query = "SELECT `id`, `name` FROM `sd_depts`";
		$tmp = mysql_query($query, $connection);
		while ($rslt = mysql_fetch_array($tmp))
			{
				$dept[$rslt['id']] = $rslt['name'];
			}
//print_r($dept);
	//	$text = "<B>Лицензионный отдел он-лайн</B>";
		$sSQL = "SELECT `nick`, `status` FROM `cops_online` WHERE `logout`='0' AND (`status`='0' OR `status`='2') GROUP BY `nick` ORDER BY `nick` ASC";
//		$sSQL = "SELECT nick, status FROM `cops_online` WHERE `logout`='0' ORDER BY `status` ASC, `nick` ASC";
		$result = mysql_query($sSQL, $connection);
		$nrows=mysql_num_rows($result);
		if($nrows>0){
			$text .= "<TABLE WIDTH=\"95%\" BORDER=\"1\" CELLSPACING=\"1\" CELLPADDING=\"2\" BORDERCOLOR=\"#B1BEC6\" ALIGN=center>\n";
			
			$bgcolor[1]="#F5F5F5";
			$bgcolor[2]="#E4DDC5";
			$i=1;
			$i2=0;
			
			while($row = mysql_fetch_array($result)){
				$sSQL4 = "SELECT `dept` FROM `sd_cops` WHERE `name` = '".$row["nick"]."' LIMIT 1;";
				$result4 = mysql_query($sSQL4, $connection);
				$row4 = mysql_fetch_array($result4);
				
				$dept_users[$row4["dept"]][$row["nick"]] = $row["status"];
			}
			
			$cur_dept ="";
			ksort ($dept_users);
			reset ($dept_users);
			foreach ($dept_users AS $key=>$val){
				
				if($dept[$key] == "Лицензионный отдел"){
				
					foreach($val AS $k2=>$v2){
						$i2++;
						if($i>2) $i=1;
						$text .= "<TR BGCOLOR=\"".$bgcolor[$i]."\">\n";
						$text .= " <TD VALIGN=top STYLE=\"PADDING-left:20px;\"><img src='_imgs/clans/police.gif'><b><A HREF=\"javascript:{}\" OnClick=\"ClBrd('private [".stripslashes($k2)."]');\" TITLE=\"private [".stripslashes($k2)."]\">".stripslashes($k2)."</A></B></TD>\n";
						$text .= "</TR>\n";
						$i++;
					}
				}
			}
			$text .= "</TABLE>\n";
			
			if($i2>0){
				$text = "<CENTER><B>В данный момент можно обратиться к:</B></CENTER>".$text;
//				$text .= "<CENTER>Всего: ".$i2." человек</CENTER>\n";
			}else{
				$text .= "<CENTER><B>В данный момент в игре сотрудников отдела нет</B></CENTER>";
			}
			
		}else{
			$text .= "<CENTER><B>В данный момент в игре сотрудников отдела нет</B></CENTER>";
		}
		$text .= "<DIV><SMALL>*Информация обновляется каждые 5 минут</SMALL></DIV>\n";

		return $text;
	}
	
	echo police_online_view_999 ($db);

?>
</center>
<br>  <br> 
Для прохождения проверки на чистоту перед законом Вам теперь достаточно перечислить госпошлину за проверку персонажу <img src='_imgs/clans/Financial Academy.gif' alt='Financial Academy' border='0'><b>Terminal 01</b> [5]<img style='vertical-align:text-bottom' src='_imgs/pro/i0.gif' border='0'>. В течение максимум 10 минут после перевода персонаж, совершивший перевод, будет поставлен в очередь на проверку согласно сумме перевода:
 <br> 
 <br><b>10</b> мм: обычная проверка, срок до 5 суток.
 <br><b>100</b> мм: ускоренная проверка, срок до 12 часов.
 <br><b>300</b> мм: срочная проверка, срок до 1 часа.
 <br> <br>
* Переводы сумм, отличающихся от данных тарифов, принимаются, однако, все излишки не
 учитываюся, не влияют на время проверки и не возвращаются.
 <br> 
 <br> Переводите именно ту сумму, которая нужна! Проверка за 99 монет будет обработана наравне с проверкой за 10 монет - оставшиеся 89 будут считаться добровольными пожертвованиями полиции.
 <br> 
 <br> <div align='center'><div class='quote'><b>ВНИМАНИЕ!</b>
 <br> При заказе срочной проверки в поле "Причина" укажите ник сотрудника лицензионного отдела, с которым Вы договорились о проверке.</div></div>
 <br> 
 <br> Состояние своей заявки Вы можете в любой момент уточнить, написав в приват персонажу <img src='_imgs/clans/Financial Academy.gif' alt='Financial Academy' border='0'><b>Terminal 01</b> [5]<img style='vertical-align:text-bottom' src='_imgs/pro/i0.gif' border='0'> слово <b>status</b>, например:
 <br> 
 <br> [deadbeef] private [Terminal 01] status
 <br> [Terminal 01] private [deadbeef] Ваша заявка (обычная) принята 02.12.2006 16:44 и находится в очереди на проверку.
 <br> 
 <br> Результат проверки доступен через чат персонажа <img src='_imgs/clans/Financial Academy.gif' alt='Financial Academy' border='0'><b>Terminal 01</b> [5]<img style='vertical-align:text-bottom' src='_imgs/pro/i0.gif' border='0'> в течение 72 часов после проверки. <a href='http://www.tzpolice.ru/?act=law_results' target='_blank'>История проверок</a> доступна на сайте полиции в любой момент.
