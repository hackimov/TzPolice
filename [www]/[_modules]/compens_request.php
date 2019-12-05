<h1>Заявка на компенсацию за нападение в охраняемой локации</h1>
<center>
<?
	$query = "SELECT `mnt` FROM `exchange_rates` WHERE `section` = 'locations' LIMIT 1";
    $rs = mysql_query($query);
	list($locations) = mysql_fetch_row($rs);
    $locs = explode("|||",$locations);
    $locations = str_replace("|||", ", ", $locations);
    $locations = "<b>".$locations."</b>";
?>
<table border="0" cellspacing="0" background="i/bgr-grid-sand.gif" cellpadding="10" style="BORDER: 1px #957850 solid;">
<tr><td>
<table width="100%"><tr><td align="center"><font color="red"><b>Внимание!</b></font><br></td></tr></table>
1. Количество потерянных ресурсов указывается <b>цифрами</b>.<br>
2. Защите подлежат локации: <?=$locations?><br>
3. Лог боя можно указывать в следующих форматах:<ul>
<li><b>цифрами</b> - 12345678987
<li><b>ссылка</b> из истории жизни - javascript:showBattle(12345678987,'ru')
<li><b>обычная ссылка</b> на лог боя - http://www.timezero.ru/sbtl.ru.html?12345678987
</ul>
</td></tr>
</table>
</center>
<br><br>
<?
if (!isset($_REQUEST['step']))
{
	$query = "SELECT * FROM `exchange_rates` WHERE `section` = 'compensations' LIMIT 1";
    $rs = mysql_query($query);
    list ($p_sec, $p_mnt, $p_pol, $p_org, $p_ven, $p_rad, $p_gol, $p_gem, $p_met, $p_sil) = mysql_fetch_row($rs);
?>
<script language="Javascript" type="text/javascript">
<!--
           function ok() {
			window.close();
            }
function edit(obj) {
	obj.form.mnt.value = Math.abs(obj.form.mnt.value);
	obj.form.pol.value = Math.abs(obj.form.pol.value);
	obj.form.org.value = Math.abs(obj.form.org.value);
	obj.form.ven.value = Math.abs(obj.form.ven.value);
	obj.form.rad.value = Math.abs(obj.form.rad.value);
	obj.form.gol.value = Math.abs(obj.form.gol.value);
	obj.form.gem.value = Math.abs(obj.form.gem.value);
	obj.form.met.value = Math.abs(obj.form.met.value);
	obj.form.sil.value = Math.abs(obj.form.sil.value);
//	obj.form.submit();
}
//-->
</script>
<form name="comp_req" method="post" action="?act=compens_request">
  <input type="hidden" name="step" value="1">
  <table width="90%"  border="0" align="center" cellpadding="3" cellspacing="2">
    <tr align="center" valign="middle">
      <td width="50%">Ваш ник:<br>
      <input name="vict_nick" type="text" size="32"> </td>
      <td>Ник нападавшего:<br>
	  <input name="atck_nick" type="text" size="32"> </td>
    </tr>
    <tr align="center" valign="middle">
      <td colspan="2">Лог боя:<br>
	  <input name="fight_log" type="text" size="64"></td>
    </tr>
  </table>
  <br>
<table width=100%>

<tr bgcolor=#F4ECD4>
<td><b>Потерянные ресурсы:</b></td>
<td align=center><img src="_imgs/tz/coins.gif" height=21></td>
<td align=center><img src="_imgs/tz/polymer.gif" height=21></td>
<td align=center><img src="_imgs/tz/organic.gif" height=21></td>
<td align=center><img src="_imgs/tz/venom.gif"></td>
<td align=center><img src="_imgs/tz/rad.gif"></td>
<td align=center><img src="_imgs/tz/gold.gif" height="21"></td>
<td align=center><img src="_imgs/tz/gem.gif" height="21"></td>
<td align=center><img src="_imgs/tz/metal.gif" height="21"></td>
<td align=center><img src="_imgs/tz/silicon.gif" height="21"></td>
</tr>
<tr bgcolor=#F4ECD4>
<td><b>Курс обмена:</b></td>
<td align=center><?=$p_mnt?></td>
<td align=center><?=$p_pol?></td>
<td align=center><?=$p_org?></td>
<td align=center><?=$p_ven?></td>
<td align=center><?=$p_rad?></td>
<td align=center><?=$p_gol?></td>
<td align=center><?=$p_gem?></td>
<td align=center><?=$p_met?></td>
<td align=center><?=$p_sil?></td>
</tr>
<tr bgcolor=#BFB79D>
<td>&nbsp;</td>
<td align=center><input style="text-align:center" name="mnt" type="text" size="5"></td>
<td align=center><input style="text-align:center" name="pol" type="text" size="5"></td>
<td align=center><input style="text-align:center" name="org" type="text" size="5"></td>
<td align=center><input style="text-align:center" name="ven" type="text" size="5"></td>
<td align=center><input style="text-align:center" name="rad" type="text" size="5"></td>
<td align=center><input style="text-align:center" name="gol" type="text" size="5"></td>
<td align=center><input style="text-align:center" name="gem" type="text" size="5"></td>
<td align=center><input style="text-align:center" name="met" type="text" size="5"></td>
<td align=center><input style="text-align:center" name="sil" type="text" size="5"></td>
</tr>
</table>
  <div align="center">
    <input name="submit" type="submit" value="Отправить" onclick="edit(this);">
  </div>
</form>
<?
}
else
{
	$error = "";
	$r_vnick = strip_tags(trim($_REQUEST['vict_nick']));
	$r_anick = strip_tags(trim($_REQUEST['atck_nick']));
	$x_log = strip_tags(trim($_REQUEST['fight_log']));
    if (ereg('timezero.ru/sbtl', $x_log))
    	{
			$x_log = $x_log."<<<";
        	$r_log = sub($x_log, "html?", "<<<");
        }
    elseif (ereg('javascript:showBattle', $x_log))
    	{
        	$r_log = sub($x_log, "showBattle(", ",");
        }
    else
    	{
        	$r_log = $x_log;
        }
    $query = "SELECT * FROM `compensations` WHERE `vnick` = '".$r_vnick."' AND `log` = '".$r_log."';";
    $rs = mysql_query($query);
    if (mysql_num_rows($rs) > 0)
    	{
        	$error .= "<br>Заявка на компенсацию ущерба, полученного в данном бою, уже была Вами подана.";
        }
    if ($_REQUEST['mnt'] == "NaN") {$mnt = 0;} else {$mnt = $_REQUEST['mnt'];}
    if ($_REQUEST['pol'] == "NaN") {$pol = 0;} else {$pol = $_REQUEST['pol'];}
    if ($_REQUEST['org'] == "NaN") {$org = 0;} else {$org = $_REQUEST['org'];}
    if ($_REQUEST['ven'] == "NaN") {$ven = 0;} else {$ven = $_REQUEST['ven'];}
    if ($_REQUEST['rad'] == "NaN") {$rad = 0;} else {$rad = $_REQUEST['rad'];}
    if ($_REQUEST['gol'] == "NaN") {$gol = 0;} else {$gol = $_REQUEST['gol'];}
    if ($_REQUEST['gem'] == "NaN") {$gem = 0;} else {$gem = $_REQUEST['gem'];}
    if ($_REQUEST['met'] == "NaN") {$met = 0;} else {$met = $_REQUEST['met'];}
    if ($_REQUEST['sil'] == "NaN") {$sil = 0;} else {$sil = $_REQUEST['sil'];}
	$r_compens = $mnt."|".$pol."|".$org."|".$ven."|".$rad."|".$gol."|".$gem."|".$met."|".$sil."|";
// ЗАПРОС ЛОГА
		$sock = fsockopen("city1.timezero.ru", 80, $errno, $errstr);
		if ($sock) {
			fputs($sock, "GET /getbattle?id=".$r_log." HTTP/1.0\r\n");
			fputs($sock, "Host: city1.timezero.ru \r\n");
			fputs($sock, "Content-type: application/x-www-url-encode \r\n");
			fputs($sock, "\r\n\r\n");
			$tmp_headers = "";
			while ($str = trim(fgets($sock, 4096))) {
				$tmp_headers .= $str."\n";
			}
			$txt_log = "";
			while (!feof($sock)) {
				$txt_log .= fgets($sock, 4096);
			}
		} else {
			$txt_log = "";
            $error .= "<br>Ошибка связи. Попробуйте позже.";
		}
        $log_ = sub($txt_log, "note=\"", "\">");
        $log_note = explode(",", $log_);
        $log_loc = $log_note[0]."/".$log_note[1];
        $log_time = $log_note[2];
        if (!in_array($log_loc,$locs))
        	{
            	$error .= "<br>Указанный бой происходил в неохраняемой локации.";
            }
	$query = "SELECT * FROM `exchange_rates` WHERE `section` = 'compensations' LIMIT 1";
    $rs = mysql_query($query);
    list ($p_sec, $p_mnt, $p_pol, $p_org, $p_ven, $p_rad, $p_gol, $p_gem, $p_met, $p_sil) = mysql_fetch_row($rs);
    $req_pay = $p_mnt*$mnt;
    $req_pay = $req_pay+($p_pol*$pol);
    $req_pay = $req_pay+($p_org*$org);
    $req_pay = $req_pay+($p_ven*$ven);
    $req_pay = $req_pay+($p_rad*$rad);
    $req_pay = $req_pay+($p_gol*$gol);
    $req_pay = $req_pay+($p_gem*$gem);
    $req_pay = $req_pay+($p_met*$met);
    $req_pay = $req_pay+($p_sil*$sil);
    $appl_time = time();
    if ($error == "")
    	{
			$query = "INSERT INTO `compensations` (`id`, `vnick`, `anick`, `log`, `loc`, `time`, `appl_time`, `res`, `req_payment`, `payment`, `status`, `confirmed`, `payed`)
				VALUES (
				'', '".$r_vnick."', '".$r_anick."', '".$r_log."', '".$log_loc."', '".$log_time."', '".$appl_time."', '".$r_compens."', '".$req_pay."', '0', '0', '', '');";
//            echo($query);
            mysql_query($query) or die (mysql_error());
		    echo ("<center><b>Ваша заявка принята к рассмотрению</b></center>");
        }
    else
    	{
        	echo("<center><b>$error</b><br><a href='javascript:history.go(-1)'>Вернуться</a></center>"); 
        }
}
?>