<?
require("../_modules/functions.php");
require("../_modules/auth.php");
if (AuthUserGroup==100)
{
$bgstr[0]="background='../i/bgr-grid-sand.gif'";
$bgstr[1]="background='../i/bgr-grid-sand1.gif'";
$buildings = array();
$SQL = "SELECT * FROM buildings";
$r=mysql_query($SQL);
while ($d=mysql_fetch_array($r)) {
	$buildings[$d['id']] = $d['name'];
	$build_id[$d['full_name']] = $d['id'];
	$build_type[$d['id']] = $d['type'];
}
if ($_REQUEST['step'] == '2')
	{
		$vls = "'', '".time()."', '".$_REQUEST['id']."', '*Склад*', '0', ";
		if ($_POST['Submit'] == "положить")
        	{
                if ($_REQUEST['metal'] > 0) {$vls .= "'".intval($_REQUEST['metal'])."', ";} else {$vls .= "'0', ";}
                if ($_REQUEST['gold'] > 0) {$vls .= "'".intval($_REQUEST['gold'])."', ";} else {$vls .= "'0', ";}
            	if ($_REQUEST['polymer'] > 0) {$vls .= "'".intval($_REQUEST['polymer'])."', ";} else {$vls .= "'0', ";}
                if ($_REQUEST['organic'] > 0) {$vls .= "'".intval($_REQUEST['organic'])."', ";} else {$vls .= "'0', ";}
                if ($_REQUEST['venom'] > 0) {$vls .= "'".intval($_REQUEST['venom'])."', ";} else {$vls .= "'0', ";}
                if ($_REQUEST['rad'] > 0) {$vls .= "'".intval($_REQUEST['rad'])."', ";} else {$vls .= "'0', ";}
                if ($_REQUEST['gem'] > 0) {$vls .= "'".intval($_REQUEST['gem'])."', ";} else {$vls .= "'0', ";}
                if ($_REQUEST['silicon'] > 0) {$vls .= "'".intval($_REQUEST['silicon'])."', ";} else {$vls .= "'0', ";}
            }
		elseif ($_POST['Submit'] == "снять")
        	{
                if ($_REQUEST['metal'] > 0) {$vls .= "'-".intval($_REQUEST['metal'])."', ";} else {$vls .= "'0', ";}
                if ($_REQUEST['gold'] > 0) {$vls .= "'-".intval($_REQUEST['gold'])."', ";} else {$vls .= "'0', ";}
            	if ($_REQUEST['polymer'] > 0) {$vls .= "'-".intval($_REQUEST['polymer'])."', ";} else {$vls .= "'0', ";}
                if ($_REQUEST['organic'] > 0) {$vls .= "'-".intval($_REQUEST['organic'])."', ";} else {$vls .= "'0', ";}
                if ($_REQUEST['venom'] > 0) {$vls .= "'-".intval($_REQUEST['venom'])."', ";} else {$vls .= "'0', ";}
                if ($_REQUEST['rad'] > 0) {$vls .= "'-".intval($_REQUEST['rad'])."', ";} else {$vls .= "'0', ";}
                if ($_REQUEST['gem'] > 0) {$vls .= "'-".intval($_REQUEST['gem'])."', ";} else {$vls .= "'0', ";}
                if ($_REQUEST['silicon'] > 0) {$vls .= "'-".intval($_REQUEST['silicon'])."', ";} else {$vls .= "'0', ";}
            }
        $vls .= "'".$_REQUEST['warehouse']."', '0'";
		$query = "INSERT INTO `warehouses` (`id`, `dt`, `user_id`, `from_name`, `coins`, `metals`, `gold`, `polymers`, `organic`, `venom`, `radioactive`, `gems`, `silicon`, `warehouse`, `status`) VALUES (".$vls.")";
//        echo ($query);
        mysql_query($query) or die (mysql_error());
        echo ("<br><center>Баланс персонажа <b>".$_REQUEST['nick']."</b> обновлен.<br></center>");
    }
?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<head>
  <title>You're not supposed to see this =)</title>
<LINK href="../_modules/tzpol_css.css" rel="stylesheet" type="text/css">
<?include("../_modules/java.php")?>
</head>
<body bgcolor="#EBDFB7" text="#455600" alink="#0D3AB4" link="#0D3AB4" vlink="#0D3AB4">
<table width="90%"  border="0" align="center" cellpadding="3" cellspacing="2">
  <tr>
    <td align="center">
    <form name="state_request" method="post" action="">
      ник:
          <input type="text" name="nick" value="<?=$_REQUEST['nick']?>">
          <input type="submit" name="Submit" value="найти">
    </form><?
 if (isset($_REQUEST['nick']))
	{
		$query = "SELECT `id` FROM `site_users` WHERE `user_name` = '".$_REQUEST['nick']."' LIMIT 1;";
        $rs = mysql_query($query) or die (mysql_error());
        if (mysql_num_rows($rs) > 0)
        	{
		        list($id) = mysql_fetch_row($rs);

    ?><form name="wh" method="post" action="?step=2">
	<input type="hidden" name="id" value="<?=$id?>">
	<input type="hidden" name="nick" value="<?=$_REQUEST['nick']?>">
    <b><?=$_REQUEST['nick']?></b><br><br>
	<input type="hidden" name="step" value="2">
<table>
<tr>
<td align=center><img src="../_imgs/tz/polymer.gif" height=21></td>
<td align=center><img src="../_imgs/tz/organic.gif" height=21></td>
<td align=center><img src="../_imgs/tz/venom.gif" height=21></td>
<td align=center><img src="../_imgs/tz/rad.gif" height=21></td>
<td align=center><img src="../_imgs/tz/gold.gif" height=21></td>
<td align=center><img src="../_imgs/tz/gem.gif" height=21></td>
<td align=center><img src="../_imgs/tz/metal.gif" height=21></td>
<td align=center><img src="../_imgs/tz/silicon.gif" height=21></td>
<td>&nbsp;</td>
</tr>
<tr>
<td><input name="polymer" type="text" value="" size=5></td>
<td><input name="organic" type="text" value="" size=5></td>
<td><input name="venom" type="text" value="" size=5></td>
<td><input name="rad" type="text" value="" size=5></td>
<td><input name="gold" type="text" value="" size=5></td>
<td><input name="gem" type="text" value="" size=5></td>
<td><input name="metal" type="text" value="" size=5></td>
<td><input name="silicon" type="text" value="" size=5></td>
<td><select name="warehouse">
<?
foreach (array_keys($buildings) as $fid) {
?>
<option value="<?=$fid?>"><?=$buildings[$fid]?></option>
<? } ?>
</select></td>
</tr>
</table>
    <br>
    <input type="submit" name="Submit" value="положить">&nbsp;
    <input type="submit" name="Submit" value="снять">
</form>
<hr>


<center>Баланс переводов</center>
<table width=100%>
<tr bgcolor=#F4ECD4>
<td>&nbsp;</td>
<td align=center><img src="../_imgs/tz/coins.gif" height=21></td>
<td align=center><img src="../_imgs/tz/polymer.gif" height=21></td>
<td align=center><img src="../_imgs/tz/organic.gif" height=21></td>
<td align=center><img src="../_imgs/tz/venom.gif" height="21"></td>
<td align=center><img src="../_imgs/tz/rad.gif" height="21"></td>
<td align=center><img src="../_imgs/tz/gold.gif" height=21></td>
<td align=center><img src="../_imgs/tz/gem.gif" height="21"></td>
<td align=center><img src="../_imgs/tz/metal.gif" height=21></td>
<td align=center><img src="../_imgs/tz/silicon.gif" height="21"></td>
</tr>
<?
$SQL="SELECT warehouse, SUM(coins), SUM(metals), SUM(gold), SUM(polymers), SUM(organic), SUM(venom), SUM(radioactive), SUM(gems), SUM(silicon) FROM warehouses WHERE status='0' AND user_id='".$id."' GROUP BY warehouse ORDER BY warehouse";
$r=mysql_query($SQL);
$np=0;

while($d=mysql_fetch_row($r)) {
if ($d[1]+$d[2]+$d[3]+$d[4]+$d[5]+$d[6]+$d[7]+$d[8]+$d[9]>0) {
if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}
?>
<tr>
<td <?=$bg?>><b><?=$buildings[$d[0]]?></b></td>
<td <?=$bg?> align=center><?=$d[1]?></td>
<td <?=$bg?> align=center><?=$d[4]?></td>
<td <?=$bg?> align=center><?=$d[5]?></td>
<td <?=$bg?> align=center><?=$d[6]?></td>
<td <?=$bg?> align=center><?=$d[7]?></td>
<td <?=$bg?> align=center><?=$d[3]?></td>
<td <?=$bg?> align=center><?=$d[8]?></td>
<td <?=$bg?> align=center><?=$d[2]?></td>
<td <?=$bg?> align=center><?=$d[9]?></td>
</tr>

<? } }?>
</table>




<?
            }
        else
        	{
            	echo ("Ошибка. Персонаж <b>".$_REQUEST['nick']."</b> не найден.");
            }
    }
?>

    </td>
  </tr>
</table>
</body>
</html>
<?}?>