<h1> урс обмена</h1>
<center>
<table border="0" cellspacing="0" background="i/bgr-grid-sand.gif" cellpadding="10" style="BORDER: 1px #957850 solid;">
<tr><td>
<table width="100%"><tr><td align="center"><font color="red"><b>¬нимание!</b></font><br></td></tr></table>
”казыва€ <b>дробную</b> цену ресурса, отдел€йте дробную часть от целой <b>точкой</b>.
</td></tr>
</table>
</center>
<br><br>
<?
if ($_REQUEST['step'] == "1")
	{
			$locs = $_REQUEST['locs'];
            $locs = str_replace("\r", " ", $locs);
            $locs = str_replace("\n", " ", $locs);
            $locs = str_replace("  ", " ", $locs);
            $locs = trim($locs);
            $locs = str_replace(" ", "|||", $locs);
            if ($_REQUEST['mnt'] == "NaN") {$mnt = 0;} else {$mnt = $_REQUEST['mnt'];}
		    if ($_REQUEST['pol'] == "NaN") {$pol = 0;} else {$pol = $_REQUEST['pol'];}
		    if ($_REQUEST['org'] == "NaN") {$org = 0;} else {$org = $_REQUEST['org'];}
		    if ($_REQUEST['ven'] == "NaN") {$ven = 0;} else {$ven = $_REQUEST['ven'];}
		    if ($_REQUEST['rad'] == "NaN") {$rad = 0;} else {$rad = $_REQUEST['rad'];}
		    if ($_REQUEST['gol'] == "NaN") {$gol = 0;} else {$gol = $_REQUEST['gol'];}
		    if ($_REQUEST['gem'] == "NaN") {$gem = 0;} else {$gem = $_REQUEST['gem'];}
		    if ($_REQUEST['met'] == "NaN") {$met = 0;} else {$met = $_REQUEST['met'];}
		    if ($_REQUEST['sil'] == "NaN") {$sil = 0;} else {$sil = $_REQUEST['sil'];}
            $query = "UPDATE `exchange_rates` SET `mnt` = '".$mnt."', `pol` = '".$pol."', `org` = '".$org."', `ven` = '".$ven."',
            `rad` = '".$rad."', `gol` = '".$gol."', `gem` = '".$gem."', `met` = '".$met."', `sil` = '".$sil."' WHERE `section` = 'compensations' LIMIT 1;";
            mysql_query($query) or die (mysql_error());
            $query = "UPDATE `exchange_rates` SET `mnt` = '".$locs."' WHERE `section` = 'locations' LIMIT 1;";
            mysql_query($query) or die (mysql_error());
    }
    $query = "SELECT `mnt` FROM `exchange_rates` WHERE `section` = 'locations' LIMIT 1";
    $rs = mysql_query($query);
	list($locations) = mysql_fetch_row($rs);
    $locs = explode("|||",$locations);
    $locations = str_replace("|||", "\r\n", $locations);
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
<form name="comp_req" method="post" action="?act=exchange_rates">
  <input type="hidden" name="step" value="1">
  <table width=100%>
<tr bgcolor=#F4ECD4>
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
<td align=center><input style="text-align:center" name="mnt" type="text" size="5" value="<?=$p_mnt?>"></td>
<td align=center><input style="text-align:center" name="pol" type="text" size="5" value="<?=$p_pol?>"></td>
<td align=center><input style="text-align:center" name="org" type="text" size="5" value="<?=$p_org?>"></td>
<td align=center><input style="text-align:center" name="ven" type="text" size="5" value="<?=$p_ven?>"></td>
<td align=center><input style="text-align:center" name="rad" type="text" size="5" value="<?=$p_rad?>"></td>
<td align=center><input style="text-align:center" name="gol" type="text" size="5" value="<?=$p_gol?>"></td>
<td align=center><input style="text-align:center" name="gem" type="text" size="5" value="<?=$p_gem?>"></td>
<td align=center><input style="text-align:center" name="met" type="text" size="5" value="<?=$p_met?>"></td>
<td align=center><input style="text-align:center" name="sil" type="text" size="5" value="<?=$p_sil?>"></td>
</tr>
</table>
  <div align="center">
<br> <br> ќхран€емые локации, по одной на строчку.<br><br>
  <textarea name="locs" cols="25" rows="5" wrap="VIRTUAL"><?=$locations?></textarea>
    <br><br><input name="submit" type="submit" value="»зменить" onclick="edit(this);">
  </div>
</form>