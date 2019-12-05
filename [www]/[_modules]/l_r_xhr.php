<?
require("functions.php");
require("auth.php");
if ($_REQUEST['step'] == 1)
	{
        $error = "";
		$urg[0] = 10;
        $urg[1] = 50;
        $urg[2] = 100;
        $log_string = uencode($_REQUEST['log_string'], "w");
        $req_nick = uencode($_REQUEST['nickname'], "w");
        $cop_nick = uencode($_REQUEST['cop_nick'], "w");
        if (strlen($req_nick) < 3)
        	{
				$error .= "Укажите Ваш ник!<br><br>";
            }
        $req_reas = $_REQUEST['reason'];
    	$log = ">>>start<<< ".$log_string.", >>>end<<<";
        $log = str_replace("сумма на счете", "", $log);
		$log_time = sub($log, "start<<< ", " Персонаж");
        $log_who = sub($log, "Персонаж \\'", "\\' перевел");
        $log_where = sub($log, "на счёт ", " ");
        if ($log_where == 0)
        	{
				$log_where = sub($log, "на счет ", " ");
            }
        $log_quant = sub($log, "на сумму: ", ",");
        $req_log = $log_time."|||".$log_who."|||".$log_where."|||".$log_quant;
        if ($log_quant == 10)
        	{
				$req_urg = 0;
            }
        elseif ($log_quant == 50)
        	{
				$req_urg = 1;
            }
        elseif ($log_quant == 100)
        	{
				$req_urg = 2;
            }
        else
        	{
                $error .= "Указанная сумма платежа не соответствует расценкам ни на одну из услуг, предоставляемых лицензионным отделом полиции<br><br>Убедитесь, что Вы указали полную строку из логов ячейки. Напоминаем, что информация об остатке на счете игнорируется, но необходима для безошибочного анализа лога<br><br>";
            }
        if ($req_urg == 2 && strlen($cop_nick) < 4)
        	{
                $error .= "Предварительно договоритесь с сотрудником лицензионного отдела о проведении срочной проверки. Если договоренность уже достигнута - не забудьте указать ник сотрудника в соответствующем поле заявки<br><br>";
            }
        $userinfo = GetUserInfo($req_nick);
        $query = "SELECT `id` FROM `law_checks` WHERE `payment` = '".$req_log."' LIMIT 1;";
		$double_req = time() - 259200;
        $tmp = mysql_query($query);
        if (mysql_num_rows($tmp) > 0)
        	{
				$error .= "Указанный платеж уже был принят к рассмотрению<br><br>";
            }
        $query = "SELECT `id` FROM `law_checks` WHERE `nick` = '".$req_nick."' AND `time1` > '".$double_req."' AND (`result` < 1 OR `result` > 1 );";
        $tmp = mysql_query($query);
        if (mysql_num_rows($tmp) > 0)
			{
				$error .= "Указанный персонаж уже подавал заявку. Между заявками должно пройти не менее <b>3</b> дней<br><br>";
            }

        if ($userinfo["level"] > 0 && $userinfo["level"] < 4)
			{
            	$error .= "Подавать заявки на проверку могут персонажи начиная с <font color='red'>четвертого</font> уровня<br><br>";
            }
        if ($log_where !== "84257")
			{
            	$error .= "Вы перевели деньги на неверный счет. Оплата за проверки принимается на счет <b>84257</b><br><br>";
            }
        if ($log_quant < $urg[$_REQUEST['urgency']])
        	{
            	$error .= "Вы перевели неверную сумму. Оплата за выбранный тип проверки составляет <b>".$urg[$_REQUEST['urgency']]."</b> медных монет<br><br>";
            }
        $req_type[0] = "";
        $req_type[1] = "";
        $req_type[2] = "";
        $req_type[$_REQUEST['urgency']] = " selected";
        $req_rs[0] = "";
        $req_rs[1] = "";
        $req_rs[2] = "";
        $req_rs[$_REQUEST['reason']] = " selected";
        $req_time = time();
        if ($error == "")
        	{
            	$query = "INSERT INTO `law_checks` (`id`, `nick`, `urgent`, `reason`, `status`, `result`, `term`,
                	`time1`, `time2`, `processed_by`, `checked_by`, `payed`, `payment`, `urg_cop`)
                    VALUES
                    ('', '".$req_nick."', '".$req_urg."', '".$req_reas."', '0', '0', '0', '".$req_time."', '', '', '', '0', '".$req_log."', '".$cop_nick."');";
				mysql_query($query) or die(mysql_error());
                echo ("<br><center><b>Спасибо. Ваша заявка принята.</b></center>");
            }
        else
        	{
            	$old_log = str_replace("\\", "", $log_string);
            	echo ("<br><center><b><font color='red' size='+1'>ОШИБКА</font><br><br><br>".$error."</b><br><br>");
                echo ("<form name='lr'>
                <table width='90%'  border='0' align='center' cellpadding='5' cellspacing='0'>
				  <tr>
				    <td>
				      Тип:
				      <select name='urgency' onChange='urg(this)'>
				        <option value='0'".$req_type[0].">обычная</option>
				        <option value='1'".$req_type[1].">срочная (12 часов)</option>
        <option value='2'".$req_type[2].">срочная (1 час)</option>
      </select></td>
    <td>
      Причина:
      <select name='reason'>
        <option value='0'".$req_rs[0].">вступление в клан</option>
        <option value='1'".$req_rs[1].">получение гражданства</option>
        <option value='2'".$req_rs[2].">покупка недвижимости</option>
      </select></td>
    <td>
      Ник:
      <input type='text' name='nickname' value='".$req_nick."'>
</td>
  </tr>
</table>

<div id='urg'");
if ($cop_nick == 0) {$tmp = " selected";} else {$tmp = "";}
if ($_REQUEST['urgency'] < 2) {echo (" style='display:none'");}
echo (" align='center'>
      Сотрудник ЛО, с которым достигнута договоренность о проверке:
      <select name='cop_nick'>
		<option value='0'".$tmp.">нет</option>");
$SQL = 'SELECT name FROM sd_cops WHERE dept=18';
$result = mysql_query($SQL) or die (mysql_error());
  if (mysql_num_rows($result) > 0 ) {
     while (list($name) = mysql_fetch_row($result))
     	{
        	if ($cop_nick == $name) {$tmp = " selected";} else {$tmp = "";}
		    echo("<option value='".$name."'".$tmp.">".$name."</option>");
        }
  	}
echo ("</select>
</div>
<div align='center'><br>
	Строка из логов ячейки о переводе:<br>
    <textarea name='log_string' cols='90' rows='3' wrap='VIRTUAL'>".$old_log."</textarea>
    <br>
    <input type='button' value='Отправить' onClick='lr_form_subm()'>
</div>
</form>");
            }
    }
//echo ("<pre>");
//print_r($_REQUEST);
//echo ("</pre>");
?>