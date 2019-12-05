<h1>Проверки на чистоту перед законом</h1>
<script language="Javascript" type="text/javascript">
<!--
            function delReq(a) {
                   if (pl = window.prompt("Укажите причину удаления заявки", ""))
                    {
                                        if (!pl) alert("Необходимо причину удаления заявки!")
                                        else
                            {
                                                        if(confirm('Вы уверены, что хотите удалить заявку?')) top.location='?act=law_check&del_id='+a+'&reason='+pl;
                                        }
                }
            else
                {
                            alert('Необходимо указать причину удаления заявки!\n');
                }
                        }
function old_r_check() {
        if (nick = window.prompt("Введите ник персонажа", ""))
            {
        if (!nick) alert("Необходимо указать ник персонажа!")
    else
            {
            window.location.href = '?act=law_check&sec=old_re_check&nick=' + nick;
            }
        }
        }
//-->
</script>
<?
$reason[0] = "вступление в клан";
$reason[1] = "получение гражданства";
$reason[2] = "покупка недвижимости";
$reason[3] = "платная услуга дилера";
$urgency[0] = "обычная";
$urgency[1] = "<b>срочная 12 часов</b>";
$urgency[2] = "<b>срочная 1 час</b>";
$mark[0] = "<font color='green'>чист, помечен.</font>";
$mark[1] = "отказ в проверке - подача заявки раньше окончания исп. срока.";
$mark[2] = "отказ в проверке - нарушение правил подачи заявки.";
$mark[3] = "<font color='red'>отказ в чистоте, нарушение правил общения, испытательный срок ";
$mark[80] = "<font color='red'>отказ в чистоте, подача заявки с каторги</font>";
$mark[90] = "<font color='red'>отказ в чистоте, хронические нарушения, каторга</font>";
$mark[100] = "<font color='red'>отказ в чистоте, на проверку подал не владелец персонажа</font>";
$mark[110] = "<font color='red'>отказ в чистоте, хронические нарушения, штраф</font>";
$bg[0]="background='i/bgr-grid-sand.gif'";
$bg[1]="background='i/bgr-grid-sand1.gif'";
$no_pay = time() - 259200;
$query = "DELETE FROM `law_checks` WHERE `payed` = '0' AND `time1` < '".$no_pay."';";
mysql_query($query) or die(mysql_error());
$query = "DELETE FROM `law_checks` WHERE `status` = '200' AND `time1` < '".$no_pay."';";
mysql_query($query) or die(mysql_error());
if (AuthStatus==1 && substr_count(AuthUserRestrAccess, "law_check") > 0 || substr_count(AuthUserRestrAccess, "law_control") > 0)
{
                if (AuthStatus==1 && substr_count(AuthUserRestrAccess, "law_control") > 0)
                {
                    $mega = 1;
            }
        else
                {
                    $mega = 0;
            }
        if (AuthUserGroup == 100)
                {

                    $mega = 2;

            }

$remain[0] = 432000;

$remain[1] = 43200;

$remain[2] = 3600;

$late_5days = time() - 410400;

//410400 = 4 days 18 hours.
//388800 seconds = 4 days 12 hours.

//345600 seconds = 4 days.

if ($_REQUEST['del_log'] == 1 && $mega == 2)

       {

        if (!$del_log = fopen('_modules/del_log.htm', 'w'))

                {

                    echo ("Ошибка записи в лог, попробуйте позже");

                exit;

            }

        fwrite($del_log, " ");

        fclose($del_log);

    }

if (isset($_REQUEST['del_id']) && $_REQUEST['del_id'] > 0 && isset($_REQUEST['reason']))

        {

        if (!$del_log = fopen('/home/sites/police/www/_modules/del_log.htm', 'a'))

                {

                    echo ("Ошибка записи в лог, попробуйте позже");

                exit;

            }

                $query = "SELECT `id`, `nick`, `reason`, `urgent`, `time1` FROM `law_checks` WHERE `id` = '".$_REQUEST['del_id']."' LIMIT 1;";

        $rs = mysql_query($query) or die(mysql_error());

                list($n_id, $n_nick, $n_reason, $n_urgent, $n_time) = mysql_fetch_row($rs);

        $log_string = date("d.m.Y H:i");

        if ($n_reason == 0) {$rsn = "вступление в клан";}

        elseif ($n_reason == 1) {$rsn = "получение гражданства";}

        elseif ($n_reason == 2) {$rsn = "покупка недвижимости";}

        elseif ($n_reason == 3) {$rsn = "платная услуга дилера";}

        $log_string .= " [<b>".AuthUserName."</b>] удалил заявку персонажа [<b>".$n_nick."</b>] от ".date('d.m.Y H:i', $n_time)." (".$rsn.", ".$urgency[$n_urgent].") по причине: ".$_REQUEST['reason']."<br>\r\n";

        fwrite($del_log, $log_string);

        fclose($del_log);

        $query = "DELETE FROM `law_checks` WHERE `id` = '".$_REQUEST['del_id']."' LIMIT 1;";

        mysql_query($query) or die (mysql_error);

    }



if (isset($_REQUEST['del']) && $_REQUEST['del'] > 0 && $mega)

        {

            $query = "DELETE FROM `law_checks` WHERE `id` = '".$_REQUEST['del']."' LIMIT 1;";

        mysql_query($query) or die (mysql_error);

    }

?>

<script language="JavaScript">

<!--

function rsn(vl, tm){

if (vl > 0)

        {

            if (vl == 1) {txt = "***** Отказ в проверке - подача заявки раньше окончания исп. срока.";}

            else if (vl == 2) {txt = "***** Отказ в проверке - нарушение правил подачи заявки.";}

            else if (vl == 80) {txt = "***** Отказ в чистоте, персонаж отбывает административное наказание.";}

            else if (vl == 90) {txt = "***** Отказ в чистоте, хронические нарушения, персонаж отправляется на каторгу.";}

            else if (vl == 100) {txt = "***** Отказ в чистоте, на проверку подал не владелец персонажа.";}

            else if (vl == 110) {txt = "***** Отказ в чистоте, хронические нарушения, персонаж оштрафован";}

        else {

       now = new Date();

        var stamp = now.getTime();

        now.setTime(stamp+(vl*24*60*60*1000));

        var min;

        if (now.getMinutes() < 10)

          min = "0"+now.getMinutes();

        else

          min = now.getMinutes();

        var month;

        if ((now.getMonth()+1) < 10)

          month = "0"+(now.getMonth()+1);

        else

          month = now.getMonth()+1;

//        txt = "***** Отказ в чистоте, нарушение правил общения, испытательный срок " + vl + " суток (до "+now.getHours()+":"+min+" "+now.getDate()+"."+month+"."+now.getYear()+").";}

        txt = "***** Отказ в чистоте, нарушение правил общения, испытательный срок " + vl + " суток.";}

        rslt = txt + " (по заявке от " + tm + ")";

                window.clipboardData.setData("Text",rslt);

    }

}

function nck(vl){

                window.clipboardData.setData("Text",vl);

}

-->

</script>

<center>

<table border="0" cellspacing="0" background="i/bgr-grid-sand.gif" cellpadding="10" style="BORDER: 1px #957850 solid;">

<tr><td>

<table width="100%"><tr><td align="center"><font color="red"><b>Внимание!</b></font><br></td></tr></table>

1. При нажатии на кнопку <b>"Начать проверки"</b> Вам будет предложено <b>выбранное</b> количество заявок с учетом приоритета по срочности.<br>

2. После начала проверки у вас есть <b>20</b> минут на обработку выданных заявок. В течение этого времени они не будут выдаваться другим сотрудникам. Если в течение 20 минут проверка не будет завершена - заявки снова поступают в общую очередь.<br>

3. Если автоматически подтвердить оплату не удалось, воспользуйтесь ссылкой <b>"Заявок с неподтвержденной оплатой"</b>.<br>

4. Для Вашего удобства при выставлении результата проверки персонажа в буфер обмена вносится соответствующая строка для внесения в личное дело. Вам остается только нажать "Вставить" в меню, появляющемся по нажатию правой кнопки мыши =)<br>

5. В графе "срочность" в скобках указывается дата подачи заявки.<br>

6. При проведении проверки при щелчке по нику проверяемого ник копируется в буфер обмена.

</td></tr>

</table>

</center>

<br><br>

<?

                $too_long = time() - 1800;

                $query = "UPDATE `law_checks` SET `checked_by` = '', `processed_by` = '' WHERE `checked_by` < '".$too_long."' AND `status` < 100;";

                mysql_query($query) or die(mysql_error());

// Completing checks

if ($_REQUEST['sec'] == "do_check")

        {

            $query = "SELECT `id` FROM `law_checks` WHERE `processed_by` = '".AuthUserId."'";

        $rs = mysql_query($query);

        if (mysql_num_rows($rs) > 0)

                {

                    while(list($n_id) = mysql_fetch_row($rs))

                        {

                            $c_res = $_REQUEST[$n_id];

                        if ($c_res > 3 && $c_res < 79) {$c_res = 3;}

                        $qw = "term_".$n_id;

                        $c_term = $_REQUEST[$qw];

                        if ($c_res !== "delay")

                                {

                                                                $query = "UPDATE `law_checks` SET `result` = '".$c_res."', `term` = '".$c_term."', `checked_by` = '".AuthUserId."', `time2` = '".time()."', `status` = '100', `processed_by` = '-999' WHERE `id` = '".$n_id."' LIMIT 1;";

                            }

                        else

                                {

                                                                $query = "UPDATE `law_checks` SET `checked_by` = '', `result` = '', `time2` = '', `status` = '0', `processed_by` = '' WHERE `id` = '".$n_id."' LIMIT 1;";

                            }

                                   mysql_query($query) or die(mysql_error());

                   }

            }

    }

// End completing checks

// Manual payment confirmation

if (isset($_REQUEST['confirm']))

        {

            $query = "UPDATE `law_checks` SET `payed` = '1' WHERE `id` = '".$_REQUEST['confirm']."' LIMIT 1;";

        mysql_query($query) or die(mysql_error());

    }

// End manual payment confirmation

// Payment deny

if (isset($_REQUEST['deny_payment']))

        {

            $query = "UPDATE `law_checks` SET `checked_by`= ".AuthUserId.", `result` = '50', `status` = '200', `time2` = '".time()."' WHERE `id` = '".$_REQUEST["deny_payment"]."' LIMIT 1;";

//        echo ($query);

        mysql_query($query) or die(mysql_error());

    }

// End payment deny

// Payment verification

if ($_REQUEST['step'] == 1)

        {

            $lines = explode("\n", $_REQUEST['log']);

/*            $query = 'SELECT payment FROM law_checks WHERE payed=1 order by id desc limit 0,1';

            $rez = mysql_query($query) or die(mysql_error());*/

        foreach ($lines as $line_num => $line)

                {

                            $log = ">>>start<<< ".$line." >>>end<<<";

                                $log_time = sub($log, "start<<< ", " Персонаж");

                        $log_who = sub($log, " \\'", "\\'");

                        $log_quant = sub($log, "на сумму: ", ", ");

                        $rez_log = $log_time."|||".$log_who."|||84257|||".$log_quant;

                $rez_log = trim($rez_log);

                                $query = "SELECT `id` FROM `law_checks` WHERE `payment` = '".$rez_log."' LIMIT 1;";

                $rez = mysql_query($query) or die(mysql_error());

                $id_ = mysql_fetch_row($rez);

                if (mysql_num_rows($rez) > 0)

                        {

                        $query = "UPDATE `law_checks` SET `payed` = 1 WHERE `id` = '".$id_[0]."' LIMIT 1;";

                                                mysql_query($query) or die(mysql_error());

                    }

                        $rez_log = $log_time."|||".$log_who."|||70717|||".$log_quant;

                $rez_log = trim($rez_log);

                                $query = "SELECT `id` FROM `law_checks` WHERE `payment` = '".$rez_log."' LIMIT 1;";

                $rez = mysql_query($query) or die(mysql_error());

                $id_ = mysql_fetch_row($rez);

                if (mysql_num_rows($rez) > 0)

                        {

                        $query = "UPDATE `law_checks` SET `payed` = 1 WHERE `id` = '".$id_[0]."' LIMIT 1;";

                                                mysql_query($query) or die(mysql_error());

                    }

            }

    }

// End payment verification

//$query = "SELECT `id` FROM `law_checks` WHERE `payed` = 0 AND `status` < 100;";

//$rs = mysql_query($query) or die(mysql_error());

//$res1 = mysql_num_rows($rs);

$query = "SELECT `id` FROM `law_checks` WHERE `payed` = 1 AND `status` < 100 AND `processed_by` = '';";

$rs = mysql_query($query) or die(mysql_error());

$res2 = mysql_num_rows($rs);

$spesh_query = 'SELECT lc1.* FROM law_checks as lc1, law_checks as lc2'.

 ' WHERE lc1.nick = lc2.nick and lc1.status=0 and lc1.processed_by="" and lc2.result =3 and (lc2.time2+lc2.term*24*60*60)>'.time();

$rs = mysql_query($spesh_query) or die(mysql_error());

$res_spesh = mysql_num_rows($rs);

$macuta_query = 'SELECT * FROM law_checks WHERE status=0 and payed=1 and processed_by="" and reason=3';

$rs = mysql_query($macuta_query) or die(mysql_error());

$res_macuta = mysql_num_rows($rs);

?>

<!--<a href="?act=law_check">Подтверждение переводов (анализатор логов ячейки)</a><br>

<a href="?act=law_check&sec=unchecked">Заявок с неподтвержденной оплатой</a>: <b><?=$res1?></b><br>-->

<?

if ($res_spesh)

{

?>

<a href="?act=law_check&sec=spesh">Подавшие до окончания исп. срока</a>: <b><?=$res_spesh?></b><br>

<?

}

else

  echo 'Подавшие до окончания исп. срока: <b>0</b><br>';

?>



<?

if ($mega)

{

  if ($res_macuta)

  {

  ?>

  <a href="?act=law_check&sec=macuta">Подавшие для платной услуги</a>: <b><?=$res_macuta?></b><br>

  <?

  }

  else

    echo 'Подавшие для платной услуги: <b>0</b><br>';



}

?>

Оплаченных заявок: <b><?=$res2?></b>

<form name="form1" method="post" action="?act=law_check" onSubmit="if(!confirm('Вы уверены, что хотите начать проверки?')) {return false}">

Количество заявок: <select name="check_num">

    <option value="10" checked>10</option>
    <option value="15">15</option>
    <option value="20">20</option>
<option value="50">50</option>
<option value="100">100</option>
  </select>

  <input type="hidden" name="sec" value="start_check">

  <input type="submit" name="Submit" value="Начать проверки">

</form>

<a href="?act=law_check&sec=checked">Очередь на проверку (в алфавитном порядке, удаление неправильных заявок)</a><br>

<a href="#" onClick="old_r_check(); return false">Результаты проверок выбранного персонажа<?if ($mega == 0) {echo(" (READ ONLY)");}?></a><br>

<?if ($mega == 2) {?><a href="?act=law_check&sec=del_logs">Логи удаления заявок на проверку</a><br>

<a href="?act=law_check&del_log=1" onClick="if(!confirm('Вы уверены?')) {return false}">Очистить логи удаления заявок на проверку</a><br><?}?>

<br><br>

<?

if (!isset($_REQUEST['sec']) || $_REQUEST['sec'] == "do_check")

        {
/*
<center>

Подтверждение переводов<br><i>вставьте логи ячейки <b>84257</b> за требуемый период</i>

<form name="lr" method="post" action="?act=law_check">

<input type="hidden" name="step" value="1">

    <textarea name="log" cols="90" rows="10" wrap="VIRTUAL"></textarea>

    <br>

    <input type="submit" name="Submit" value="Отправить">

</form>

</center>


*/
}

//самое новое ;-) Макутины клиенты, спешащие на проверку

if ($_REQUEST['sec'] == "macuta")

        {

                $too_long = time() - 1800;

                $query = "UPDATE `law_checks` SET `checked_by` = '', `processed_by` = '' WHERE `checked_by` < '".$too_long."' AND `status` < 100";

                mysql_query($query) or die(mysql_error());

                $query = "UPDATE `law_checks` SET `checked_by` = '', `processed_by` = '' WHERE `processed_by` = ".AuthUserId;
                mysql_query($query) or die(mysql_error());

                $query = "SELECT `id`, `nick`, `reason`, `urgent`, `urg_cop` FROM `law_checks` WHERE `processed_by` = '".AuthUserId."' ORDER BY `urgent` DESC, `id` ASC;";

                $rs = mysql_query($query) or die(mysql_error());

                if (mysql_num_rows($rs) == 0)

                {

                $query = 'SELECT `id` FROM law_checks WHERE status=0 and payed=1 and processed_by="" and reason=3';

                  $res = mysql_query($query);

                  if (mysql_num_rows($res) > 0)

                  {

                    $n_time = time();

                    while(list($n_id) = mysql_fetch_row($res))

                    {

                      $query = "UPDATE `law_checks` SET `processed_by` = '".AuthUserId."', `checked_by` = '".$n_time."' WHERE `id` = '".$n_id."' LIMIT 1;";

                      mysql_query($query) or die(mysql_error);

                    }

                  }

                }

?>

<center><b>Внимание! На проверку отведено 20 минут!</b>

<form name="checks" method="post" action="?act=law_check">

<input type="hidden" name="sec" value="do_check">

<table width="90%"  border="0" cellspacing="3" cellpadding="2" align="center">

  <tr align="center">

    <td width="35%" bgcolor=#F4ECD4><b>ник</b></td>

    <td width="20%" bgcolor=#F4ECD4><b>причина</b></td>

    <td width="20%" bgcolor=#F4ECD4><b>срочность</b></td>

    <td bgcolor=#F4ECD4><b>результат</b></td>

  </tr>

<?

       $query = 'SELECT `id`, `nick`, `reason`, `urgent`, `time1`, `urg_cop` FROM law_checks WHERE status=0 and payed=1 and processed_by="'.AuthUserId.'" and reason=3';

        $rs = mysql_query($query) or die(mysql_error());

        $bs = 0;

        $tm = time();

                while(list($n_id, $n_nick, $n_reason, $n_urgent, $n_time, $u_cop) = mysql_fetch_row($rs))

                {

                    if ($u_cop) {$ext = ", договор с <b>".$u_cop."</b>";} else {$ext = "";}

                                ?>

                <tr>

                                    <td <?=$bg[$bs]?>><a href="#; return false;" onClick="nck('<?=$n_nick?>');"><?=$n_nick?></a></td>

                                    <td <?=$bg[$bs]?> align="center"><?=$reason[$n_reason]?></td>

                                    <td <?=$bg[$bs]?> align="center"><?

                    echo ($urgency[$n_urgent]);

                    echo ("<br>(".date('d.m.Y, H:i', $n_time).$ext.")");

                    $temp = $remain[$n_urgent]-($tm-$n_time);

                    $quer = "SELECT SEC_TO_TIME(".$temp.");";

                    $rz = mysql_query($quer);

                    list($remn) = mysql_fetch_row($rz);

                    echo ("<br> ост.: ".$remn);?></td>

                                    <td <?=$bg[$bs]?> align="center"nowrap>

                    <select name="<?=$n_id?>" onChange="rsn(this.value, '<?=date('d.m.Y, H:i', $n_time)?>'); if (this.value > 2 && this.value < 79) {term_<?=$n_id?>.value=this.value;} else {term_<?=$n_id?>.value='';}">

                                      <option value="delay" checked>отложить проверку</option>

                                      <option value="0" style="color: green">чист, помечен</option>

                                      <option value="1">заявка до конца исп. срока</option>

                                      <option value="2">нарушение правил подачи заявки</option>

		                      <option value="80" style="color: red">отказ - заявка от каторжника</option>
				
		                      <option value="90" style="color: red">отказ - хронические нарушения, каторга</option>
				
		                      <option value="100" style="color: red">отказ - не владелец персонажа</option>

                		      <option value="110" style="color: red">отказ - хронические нарушения, штраф</option>

                                      <option value="3" style="color: red">1 - отказ, 3 дня</option>

                                      <option value="5" style="color: red">2 - отказ, 5 дней</option>

                                      <option value="6" style="color: red">3 - отказ, 6 дней</option>

                                      <option value="8" style="color: red">4 - отказ, 8 дней</option>

				      <option value="9" style="color: red">5 - отказ, 9 дней</option>
                                      <option value="11" style="color: red">6 - отказ, 11 дней</option>

                                       <option value="12" style="color: red">7 - отказ, 12 дней</option>

                                      <option value="15" style="color: red">8 - отказ, 15 дней</option>

                                      <option value="20" style="color: red">>8 - отказ, 20++ дней</option>

                                    </select>

                    <input type="text" size="3" name="term_<?=$n_id?>"> дней

                </td>

                          </tr>

            <?

            $bs++;

            if ($bs > 1) {$bs = 0;}

            }?>

</table>

<input type="submit" name="Submit" value="Отправить">

</form>

<?

        }

//новое: отлов подавших до окончания исп. срока

if ($_REQUEST['sec'] == "spesh")

        {

                $too_long = time() - 1800;

                $query = "UPDATE `law_checks` SET `checked_by` = '', `processed_by` = '' WHERE `checked_by` < '".$too_long."' AND `status` < 100";

                mysql_query($query) or die(mysql_error());

                $query = "UPDATE `law_checks` SET `checked_by` = '', `processed_by` = '' WHERE `processed_by` = ".AuthUserId;
                mysql_query($query) or die(mysql_error());

                $query = "SELECT `id`, `nick`, `reason`, `urgent`, `urg_cop` FROM `law_checks` WHERE `processed_by` = '".AuthUserId."' ORDER BY `urgent` DESC, `id` ASC;";

                $rs = mysql_query($query) or die(mysql_error());

                if (mysql_num_rows($rs) == 0)

                {

$query = 'SELECT lc1.id FROM law_checks as lc1, law_checks as lc2'.

  ' WHERE lc1.nick = lc2.nick and lc1.status=0 and lc1.processed_by="" and lc2.result =3 and (lc2.time2+lc2.term*24*60*60)>'.time();

                  $res = mysql_query($query);

                  if (mysql_num_rows($res) > 0)

                  {

                    $n_time = time();

                    while(list($n_id) = mysql_fetch_row($res))

                    {

                      $query = "UPDATE `law_checks` SET `processed_by` = '".AuthUserId."', `checked_by` = '".$n_time."' WHERE `id` = '".$n_id."' LIMIT 1;";

                      mysql_query($query) or die(mysql_error);

                    }

                  }

                }

?>

<center><b>Внимание! На проверку отведено 20 минут!</b>

<form name="checks" method="post" action="?act=law_check">

<input type="hidden" name="sec" value="do_check">

<table width="90%"  border="0" cellspacing="3" cellpadding="2" align="center">

  <tr align="center">

    <td width="35%" bgcolor=#F4ECD4><b>ник</b></td>

    <td width="20%" bgcolor=#F4ECD4><b>причина</b></td>

    <td width="20%" bgcolor=#F4ECD4><b>срочность</b></td>

    <td bgcolor=#F4ECD4><b>результат</b></td>

  </tr>

<?

$query = 'SELECT lc1.id as id, lc1.nick as nick, lc1.reason as reason, lc1.urgent as urgent, lc1.time1 as time1, lc1.urg_cop as urg_cop FROM law_checks as lc1, law_checks as lc2'.

  ' WHERE lc1.nick = lc2.nick and lc1.status=0 and lc1.processed_by="'.AuthUserId.'" and lc2.result =3 and (lc2.time2+lc2.term*24*60*60)>'.time() .' order by lc1.id ASC';

        $rs = mysql_query($query) or die(mysql_error());

        $bs = 0;

        $tm = time();

                while(list($n_id, $n_nick, $n_reason, $n_urgent, $n_time, $u_cop) = mysql_fetch_row($rs))

                {

                    if ($u_cop) {$ext = ", договор с <b>".$u_cop."</b>";} else {$ext = "";}

                                ?>

                <tr>

                                    <td <?=$bg[$bs]?>><a href="#; return false;" onClick="nck('<?=$n_nick?>');"><?=$n_nick?></a></td>

                                    <td <?=$bg[$bs]?> align="center"><?=$reason[$n_reason]?></td>

                                    <td <?=$bg[$bs]?> align="center"><?

                    echo ($urgency[$n_urgent]);

                    echo ("<br>(".date('d.m.Y, H:i', $n_time).$ext.")");

                    $temp = $remain[$n_urgent]-($tm-$n_time);

                    $quer = "SELECT SEC_TO_TIME(".$temp.");";

                    $rz = mysql_query($quer);

                    list($remn) = mysql_fetch_row($rz);

                    echo ("<br> ост.: ".$remn);?></td>

                                    <td <?=$bg[$bs]?> align="center"nowrap>

                    <select name="<?=$n_id?>" onChange="rsn(this.value, '<?=date('d.m.Y, H:i', $n_time)?>'); if (this.value > 2 && this.value < 79) {term_<?=$n_id?>.value=this.value;} else {term_<?=$n_id?>.value='';}">

                                      <option value="delay" checked>отложить проверку</option>

                                      <option value="1">заявка до конца исп. срока</option>

                                    </select>

                    <input type="text" size="3" name="term_<?=$n_id?>"> дней

                </td>

                          </tr>

            <?

            $bs++;

            if ($bs > 1) {$bs = 0;}

            }?>

</table>

<input type="submit" name="Submit" value="Отправить">

</form>

<?

       }

//ниже - то что было

if ($_REQUEST['sec'] == "del_logs")

        {

            ?><center><h1>Логи удаления заявок на проверку</h1></center><?

                include ("_modules/del_log.htm");

    }

if ($_REQUEST['sec'] == "start_check")

        {

                $too_long = time() - 1800;

                if (isset($_REQUEST['check_num']))

                {

                        $to_check = $_REQUEST['check_num'];

            }

        else

                {

                    $to_check = 5;

            }

                $query = "UPDATE `law_checks` SET `checked_by` = '', `processed_by` = '' WHERE `checked_by` < '".$too_long."' AND `status` < 100";

                mysql_query($query) or die(mysql_error());

                $query = "UPDATE `law_checks` SET `checked_by` = '', `processed_by` = '' WHERE `processed_by` = ".AuthUserId;
                mysql_query($query) or die(mysql_error());

            $query = "SELECT `id`, `nick`, `reason`, `urgent`, `urg_cop` FROM `law_checks` WHERE `processed_by` = '".AuthUserId."' ORDER BY `urgent` DESC, `time1` ASC;";

        $rs = mysql_query($query) or die(mysql_error());

        if (mysql_num_rows($rs) == 0)

                {

                                $query = "SELECT `id` FROM `law_checks` WHERE `payed` = 1 AND `checked_by` = '' AND `status` < 100 AND `time1` < '".$late_5days."' ORDER BY `time1` ASC LIMIT ".$to_check.";";

                                $res = mysql_query($query);

                if (mysql_num_rows($res) > 0)

                        {

                            $to_check2 = $to_check - mysql_num_rows($res);

                        $n_time = time();

                        while(list($n_id) = mysql_fetch_row($res))

                                {

                                    $query = "UPDATE `law_checks` SET `processed_by` = '".AuthUserId."', `checked_by` = '".$n_time."' WHERE `id` = '".$n_id."' LIMIT 1;";

                                    mysql_query($query) or die(mysql_error);

                                }

                    }

                else

                        {

                            $to_check2 = $to_check;

                    }

                            $query = "SELECT `id` FROM `law_checks` WHERE `payed` = 1 AND `checked_by` = '' AND `status` < 100 ORDER BY `urgent` DESC, `id` ASC LIMIT ".$to_check2.";";

                        $res = mysql_query($query) or die(mysql_error());

                        $n_time = time();

                                while(list($n_id) = mysql_fetch_row($res))

                                {

                                    $query = "UPDATE `law_checks` SET `processed_by` = '".AuthUserId."', `checked_by` = '".$n_time."' WHERE `id` = '".$n_id."' LIMIT 1;";

                                mysql_query($query) or die(mysql_error);

                            }

            }

?>

<center><b>Внимание! На проверку отведено 20 минут!</b>

<form name="checks" method="post" action="?act=law_check">

<input type="hidden" name="sec" value="do_check">

<table width="90%"  border="0" cellspacing="3" cellpadding="2" align="center">

  <tr align="center">

    <td width="35%" bgcolor=#F4ECD4><b>ник</b></td>

    <td width="20%" bgcolor=#F4ECD4><b>причина</b></td>

    <td width="20%" bgcolor=#F4ECD4><b>срочность</b></td>

    <td bgcolor=#F4ECD4><b>результат</b></td>

  </tr>

<?

            $query = "SELECT `id`, `nick`, `reason`, `urgent`, `time1`, `urg_cop` FROM `law_checks` WHERE `processed_by` = '".AuthUserId."' ORDER BY `urgent` DESC, `id` ASC;";

        $rs = mysql_query($query) or die(mysql_error());

        $bs = 0;

        $tm = time();

                while(list($n_id, $n_nick, $n_reason, $n_urgent, $n_time, $u_cop) = mysql_fetch_row($rs))

                {

                    if ($u_cop) {$ext = ", договор с <b>".$u_cop."</b>";} else {$ext = "";}

                                ?>

                <tr>

                                    <td <?=$bg[$bs]?>><a href="#; return false;" onClick="nck('<?=$n_nick?>');"><?=$n_nick?></a></td>

                                   <td <?=$bg[$bs]?> align="center"><?=$reason[$n_reason]?></td>

                                    <td <?=$bg[$bs]?> align="center"><?

                    echo ($urgency[$n_urgent]);

                    echo ("<br>(".date('d.m.Y, H:i', $n_time).$ext.")");

                    $temp = $remain[$n_urgent]-($tm-$n_time);

                    $quer = "SELECT SEC_TO_TIME(".$temp.");";

                    $rz = mysql_query($quer);

                    list($remn) = mysql_fetch_row($rz);

                    echo ("<br> ост.: ".$remn);?></td>

                                    <td <?=$bg[$bs]?> align="center"nowrap>

<?
  $SQL = "SELECT name FROM sd_cops WHERE dept=18 AND chief=1";
  $rrr = mysql_query($SQL);
  $rrr_res = mysql_fetch_array($rrr);

  if ($n_reason == 3 && AuthUserName != $rrr_res[0])
    echo '<input type="hidden" name="'.$n_id.'" value="delay">Нет права проверки<br>этого персонажа';
/*  elseif ($n_urgent == 2 && AuthUserName != $u_cop && AuthUserName != $rrr_res[0])
    echo '<input type="hidden" name="'.$n_id.'" value="delay">Нет права проверки этого персонажа';
*/
  else
  {
?>
                    <select name="<?=$n_id?>" onChange="rsn(this.value, '<?=date('d.m.Y, H:i', $n_time)?>'); if (this.value > 2 && this.value < 79) {term_<?=$n_id?>.value=this.value;} else {term_<?=$n_id?>.value='';}">

                                     <option value="delay" checked>отложить проверку</option>

                                      <option value="0" style="color: green">чист, помечен</option>

                                      <option value="1">заявка до конца исп. срока</option>

                                      <option value="2">нарушение правил подачи заявки</option>

                      <option value="80" style="color: red">отказ - заявка от каторжника</option>

                      <option value="90" style="color: red">отказ - хронические нарушения, каторга</option>

                      <option value="100" style="color: red">отказ - не владелец персонажа</option>

                      <option value="110" style="color: red">отказ - хронические нарушения, штраф</option>

                                      <option value="3" style="color: red">1 - отказ, 3 дня</option>

                                      <option value="5" style="color: red">2 - отказ, 5 дней</option>

                                      <option value="6" style="color: red">3 - отказ, 6 дней</option>

                                      <option value="8" style="color: red">4 - отказ, 8 дней</option>

                                             <option value="9" style="color: red">5 - отказ, 9 дней</option>

                                       <option value="11" style="color: red">6 - отказ, 11 дней</option>

                                      <option value="12" style="color: red">7 - отказ, 12 дней</option>

                                      <option value="15" style="color: red">8 - отказ, 15 дней</option>

                                      <option value="20" style="color: red">>8 - отказ, 20++ дней</option>

                                    </select>

                    <input type="text" size="3" name="term_<?=$n_id?>"> дней
<?
  }
?>

                </td>

                         </tr>

            <?

            $bs++;

            if ($bs > 1) {$bs = 0;}

            }?>

</table>

<input type="submit" name="Submit" value="Отправить">

</form>

<?

        }

if ($_REQUEST['sec'] == "unchecked")

        {

            $query = "SELECT `id`, `nick`, `time1`, `payment`, `urg_cop` FROM `law_checks` WHERE `payed` = '0' AND `status` < '200' ORDER BY `urgent` DESC, `id` ASC";

        $rs = mysql_query($query); ?>

        <center><b>Заявки с неподтвержденной оплатой</b></center><br>

<table width="90%"  border="0" cellspacing="3" cellpadding="2" align="center">

  <tr align="center">

    <td width="20%" bgcolor=#F4ECD4><b>ник</b></td>

   <td width="20%" bgcolor=#F4ECD4><b>дата</b></td>

    <td width="40%" bgcolor=#F4ECD4><b>перевод</b></td>

    <td bgcolor=#F4ECD4>&nbsp;</td>

  </tr>

<?

        if (mysql_num_rows($rs) > 0)

                {

                    while(list($n_id, $n_nick, $n_time, $n_pay, $u_cop) = mysql_fetch_row($rs))

                       {

                        $c_pay = explode("|||", $n_pay);

                        $c_log = $c_pay[0]." Персонаж '".$c_pay[1]."' перевел на счет <b>".$c_pay[2]."</b> Медные монеты на сумму: ".$c_pay[3];

                                                if ($u_cop) {$ext = " (договор с <b>".$u_cop."</b>)";} else {$ext = "";}

                                ?>

                <tr>

                                    <td <?=$bg[$bs]?>><?=$n_nick?><?=$ext?></td>

                                    <td <?=$bg[$bs]?> align="center"><?=date("d.m.Y H:i", $n_time)?></td>

                                    <td <?=$bg[$bs]?> align="center"><?=$c_log?></td>

                                   <td <?=$bg[$bs]?> align="center"><a href="?act=law_check&sec=unchecked&confirm=<?=$n_id?>" onClick="if(!confirm('Вы уверены?')) {return false}">оплачено</a> || <a href="?act=law_check&sec=unchecked&deny_payment=<?=$n_id?>" onClick="if(!confirm('Вы уверены?')) {return false}">нет оплаты</a></td>

                          </tr>

            <?

            $bs++;

            if ($bs > 1) {$bs = 0;}

            }?>

</table>

<?

//                       mysql_query($query) or die(mysql_error());

                    }

            }

if ($_REQUEST['sec'] == "checked")

        {

            $query = "SELECT `id`, `nick`, `time1`, `payment`, `urgent`, `urg_cop` FROM `law_checks` WHERE `payed` = '1' AND `status` < '100' AND `checked_by` = '' ORDER BY `nick`";

        $rs = mysql_query($query); ?>

        <center><b>Необработанные заявки с подтвержденной оплатой</b></center><br>

<table width="90%"  border="0" cellspacing="3" cellpadding="2" align="center">

  <tr align="center">

    <td width="20%" bgcolor=#F4ECD4><b>ник</b></td>

    <td width="20%" bgcolor=#F4ECD4><b>срочность</b></td>

<!--    <td width="40%" bgcolor=#F4ECD4><b>перевод</b></td> -->

        <td width="20%" bgcolor=#F4ECD4>&nbsp;</td>

  </tr>

<?

        if (mysql_num_rows($rs) > 0)

                {

                    while(list($n_id, $n_nick, $n_time, $n_pay, $n_urgent, $u_cop) = mysql_fetch_row($rs))

                        {

                        $c_pay = explode("|||", $n_pay);

//                        $c_log = $c_pay[0]." Персонаж '".$c_pay[1]."' перевел на счет <b>".$c_pay[2]."</b> Медные монеты на сумму: ".$c_pay[3];

                        if ($u_cop) {$ext = " (договор с <b>".$u_cop."</b>)";} else {$ext = "";}

                                ?>

                <tr>

                                    <td <?=$bg[$bs]?>><?=$n_nick?><?=$ext?></td>

                    <td <?=$bg[$bs]?> align="center"><?

                    echo ($urgency[$n_urgent]);

                    echo ("<br>(".date('d.m.Y, H:i', $n_time).")");?></td>
<?
//                                    <td < ?=$bg[$bs]? > align="center">< ?=$c_log? ></td> ?>

                    <td <?=$bg[$bs]?> align="center"><?if ($mega == 2) {?><a href='?act=law_check&sec=noorder_check&id=<?=$n_id?>' onClick="if(!confirm('Вы уверены?')) {return false}">Проверить</a> || <?}?><a href='#; return false;' onClick="delReq(<?=$n_id?>)">Удалить</td>

                          </tr>

            <?

            $bs++;

            if ($bs > 1) {$bs = 0;}

                            }?>



</table>

<?

                    }

    }

if ($_REQUEST['sec'] == "noorder_check" && $mega)

        {

            $n_id = $_REQUEST['id'];

        $n_time = time();

                $query = "UPDATE `law_checks` SET `checked_by` = '', `processed_by` = '' WHERE `processed_by` = '".AuthUserId."' AND `status` < 100;";

        mysql_query($query) or die(mysql_error);

                   $query = "UPDATE `law_checks` SET `processed_by` = '".AuthUserId."', `checked_by` = '".$n_time."' WHERE `id` = '".$n_id."' LIMIT 1;";

        mysql_query($query) or die(mysql_error);

?>

<center><b>Внимание! На проверку отведено 20 минут!</b>

<form name="checks" method="post" action="?act=law_check">

<input type="hidden" name="sec" value="do_check">

<table width="90%"  border="0" cellspacing="3" cellpadding="2" align="center">

  <tr align="center">

    <td width="35%" bgcolor=#F4ECD4><b>ник</b></td>

    <td width="20%" bgcolor=#F4ECD4><b>причина</b></td>

    <td width="20%" bgcolor=#F4ECD4><b>срочность</b></td>

    <td bgcolor=#F4ECD4><b>результат</b></td>

  </tr>

<?

            $query = "SELECT `id`, `nick`, `reason`, `urgent`, `time1`, `urg_cop` FROM `law_checks` WHERE `processed_by` = '".AuthUserId."' ORDER BY `urgent` DESC, `id` ASC LIMIT 5;";

        $rs = mysql_query($query) or die(mysql_error());

        $bs = 0;

                while(list($n_id, $n_nick, $n_reason, $n_urgent, $n_time, $u_cop) = mysql_fetch_row($rs))

                {

                    if ($u_cop) {$ext = " (договор с <b>".$u_cop."</b>)";} else {$ext = "";}

                                ?>

                <tr>

                                    <td <?=$bg[$bs]?>><a href="#; return false;" onClick="nck('<?=$n_nick?>');"><?=$n_nick?></a><?=$ext?></td>

                                    <td <?=$bg[$bs]?> align="center"><?=$reason[$n_reason]?></td>

                                    <td <?=$bg[$bs]?> align="center"><?

                    echo ($urgency[$n_urgent]);

                    echo ("<br>(".date('d.m.Y, H:i', $n_time).")");?></td>

                                    <td <?=$bg[$bs]?> align="center"nowrap>

                    <select name="<?=$n_id?>" onChange="rsn(this.value, '<?=date('d.m.Y, H:i', $n_time)?>'); if (this.value > 2 && this.value < 79) {term_<?=$n_id?>.value=this.value;} else {term_<?=$n_id?>.value='';}">

                                      <option value="delay" checked>отложить проверку</option>

                                      <option value="0" style="color: green">чист, помечен</option>

                                      <option value="1">заявка до конца исп. срока</option>

                                      <option value="2">нарушение правил подачи заявки</option>

                      <option value="80" style="color: red">отказ - заявка от каторжника</option>

                      <option value="90" style="color: red">отказ - хронические нарушения, каторга</option>

                      <option value="100" style="color: red">отказ - не владелец персонажа</option>

                      <option value="110" style="color: red">отказ - хронические нарушения, штраф</option>

                                      <option value="3" style="color: red">1 - отказ, 3 дня</option>

                                      <option value="5" style="color: red">2 - отказ, 5 дней</option>

                                      <option value="6" style="color: red">3 - отказ, 6 дней</option>

                                      <option value="8" style="color: red">4 - отказ, 8 дней</option>

                                             <option value="9" style="color: red">5 - отказ, 9 дней</option>

                                       <option value="11" style="color: red">6 - отказ, 11 дней</option>

                                       <option value="12" style="color: red">7 - отказ, 12 дней</option>

                                      <option value="15" style="color: red">8 - отказ, 15 дней</option>

                                      <option value="20" style="color: red">>8 - отказ, 20++ дней</option>

                                    </select>

                    <input type="text" size="3" name="term_<?=$n_id?>"> дней

                </td>

                          </tr>

            <?

            $bs++;

            if ($bs > 1) {$bs = 0;}

            }?>



</table>

<input type="submit" name="Submit" value="Отправить">

</form></center>

<?

        }

if ($_REQUEST['sec'] == "old_re_check")

        {

                $query = "SELECT `id`, `result`, `term`, `time2`, `checked_by` FROM `law_checks` WHERE `status` > '99' AND `nick` = '".$_REQUEST['nick']."' ORDER BY `time2` DESC;";

        $rs = mysql_query($query);

        ?>

                        <center><b>Результаты проверок на чистоту персонажа <font color=red><?=$_REQUEST['nick']?></font></b></center>

                        <br><br><table width="90%"  border="0" cellspacing="3" cellpadding="2" align="center">

                          <tr align="center">

                            <td width="15%" bgcolor=#F4ECD4><b>дата</b></td>

                            <td width="65%" bgcolor=#F4ECD4><b>результат</b></td>

                <?

                if ($mega)

                        {?>

                               <td width="65%" bgcolor=#F4ECD4>&nbsp;</td>

                        <?}?>

                          </tr>

        <?

        $bgstr[0]="background='i/bgr-grid-sand.gif'";

                $bgstr[1]="background='i/bgr-grid-sand1.gif'";

        $mark[0] = "<font color='green'>чист, помечен.</font>";

                $mark[1] = "отказ в проверке - подача заявки раньше окончания исп. срока.";

                $mark[2] = "отказ в проверке - нарушение правил подачи заявки.";

                $mark[3] = "<font color='red'>отказ в чистоте, нарушение правил общения, испытательный срок ";

                $mark[50] = "<div style='background-color: red'><font color='white'><b>оплата не получена</b></font></div>";

                   while(list($n_id, $n_res, $n_term, $n_time, $checked_by) = mysql_fetch_row($rs))

                {

                                ?>

                <tr>

                                    <td <?=$bg[$bs]?> align="center"><?=date("d.m.Y H:i", $n_time)?></td>

                                    <td <?=$bg[$bs]?> align="center"><?

                    if ($n_res == 3 && $n_res < 50)

                            {

                                    echo ($mark[$n_res]."<b>".$n_term."</b> суток.</font>");

                        }

                    else

                            {

                                echo ($mark[$n_res]);

                        }

                    if(AuthStatus==1 && $see_stats == 1 && $n_res<50)

                            {

                                echo (" <i>(".$checker[$checked_by].")</i>");

                        }

                    ?></td>

                        <?

                    if ($mega)

                        {?>

                    <td align="center">

                    <?echo (" <b><a href='?act=law_check&sec=re_check&id=".$n_id."'>Изменить</a></b>");?>

                    </td>

                    <?}?>

                          </tr>

            <?

            $bs++;

            if ($bs > 1) {$bs = 0;}

            }

   }

if ($_REQUEST['sec'] == "re_check" && $mega)

        {

            $n_id = $_REQUEST['id'];

        $n_time = time();

                $query = "UPDATE `law_checks` SET `checked_by` = '', `processed_by` = '' WHERE `processed_by` = '".AuthUserId."' AND `status` < 100;";

        mysql_query($query) or die(mysql_error);

                   $query = "UPDATE `law_checks` SET `processed_by` = '".AuthUserId."', `checked_by` = '".$n_time."', `status` = '0' WHERE `id` = '".$n_id."' LIMIT 1;";

        mysql_query($query) or die(mysql_error);

?>

<center><b>Внимание! На проверку отведено 20 минут!</b>

<form name="checks" method="post" action="?act=law_check">

<input type="hidden" name="sec" value="do_check">

<table width="90%"  border="0" cellspacing="3" cellpadding="2" align="center">

  <tr align="center">

    <td width="35%" bgcolor=#F4ECD4><b>ник</b></td>

    <td width="20%" bgcolor=#F4ECD4><b>причина</b></td>

    <td width="20%" bgcolor=#F4ECD4><b>срочность</b></td>

    <td bgcolor=#F4ECD4><b>результат</b></td>

  </tr>

<?

            $query = "SELECT `id`, `nick`, `reason`, `urgent`, `time1` FROM `law_checks` WHERE `processed_by` = '".AuthUserId."' ORDER BY `urgent` DESC, `id` ASC LIMIT 5;";

        $rs = mysql_query($query) or die(mysql_error());

        $bs = 0;

                while(list($n_id, $n_nick, $n_reason, $n_urgent, $n_time) = mysql_fetch_row($rs))

                {

                                ?>

                <tr>

                                    <td <?=$bg[$bs]?>><a href="#; return false;" onClick="nck('<?=$n_nick?>');"><?=$n_nick?></a></td>

                                    <td <?=$bg[$bs]?> align="center"><?=$reason[$n_reason]?></td>

                                    <td <?=$bg[$bs]?> align="center"><?

                    echo ($urgency[$n_urgent]);

                    echo ("<br>(".date('d.m.Y, H:i', $n_time).")");?></td>

                                    <td <?=$bg[$bs]?> align="center"nowrap>

                    <select name="<?=$n_id?>" onChange="rsn(this.value, '<?=date('d.m.Y, H:i', $n_time)?>'); if (this.value > 2 && this.value < 79) {term_<?=$n_id?>.value=this.value;} else {term_<?=$n_id?>.value='';}">

                                      <option value="delay" checked>отложить проверку</option>

                                      <option value="0" style="color: green">чист, помечен</option>

                                      <option value="1">заявка до конца исп. срока</option>

                                      <option value="2">нарушение правил подачи заявки</option>

                      <option value="80" style="color: red">отказ - заявка от каторжника</option>

                      <option value="90" style="color: red">отказ - хронические нарушения, каторга</option>

                      <option value="100" style="color: red">отказ - не владелец персонажа</option>

                      <option value="110" style="color: red">отказ - хронические нарушения, штраф</option>

                                      <option value="3" style="color: red">1 - отказ, 3 дня</option>

                                      <option value="5" style="color: red">2 - отказ, 5 дней</option>

                                      <option value="6" style="color: red">3 - отказ, 6 дней</option>

                                      <option value="8" style="color: red">4 - отказ, 8 дней</option>

                                             <option value="9" style="color: red">5 - отказ, 9 дней</option>

                                       <option value="11" style="color: red">6 - отказ, 11 дней</option>

                                       <option value="12" style="color: red">7 - отказ, 12 дней</option>

                                      <option value="15" style="color: red">8 - отказ, 15 дней</option>

                                      <option value="20" style="color: red">>8 - отказ, 20++ дней</option>

                                    </select>

                    <input type="text" size="3" name="term_<?=$n_id?>"> дней

                </td>

                          </tr>

            <?

            $bs++;

            if ($bs > 1) {$bs = 0;}

            }?>

</table>

<input type="submit" name="Submit" value="Отправить">

</form></center>

<?

        }

}

else

{

echo $mess['AccessDenied'];

}

?>