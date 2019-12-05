<div style="position:absolute; visibility:hidden;" id="charinf"></div>
<SCRIPT LANGUAGE="JavaScript" SRC="_modules/info.js"></SCRIPT>
<style type="text/css">
<!--
.inf_hdr
	{
		font-family: Trebuchet MS, Arial;
		color: #CCCCCC;
       	font-size: 12px;
	}
.inf_img
	{
    	cursor: hand;
    }
-->
</style>

<h1>Онлайн списки</h1>

<center>
<table border="0" cellspacing="0" background="i/bgr-grid-sand.gif" cellpadding="10" style="BORDER: 1px #957850 solid;">
<tr><td><font color="red">Информация о статусе персонажей обновляется раз в 5 минут</td></tr>
</table>
</center>
</br>

<?php
//Пусть и тут обновляется...
list($up_nick) = mysql_fetch_row(mysql_query("SELECT `nick` FROM `police_b_list` WHERE `status` < 3 ORDER BY `last_updated` LIMIT 1;"));
$userinfo = GetUserInfo($up_nick);
if ($userinfo["error"] !== "NOT_CONNECTED" && $userinfo["error"] !== "USER_NOT_FOUND" && $userinfo["error"] !== "ERROR_IN_USER_NAME" && $userinfo["login"] == $up_nick && $userinfo["level"] > 0)
	{
    	$u_updated = time();
        $query = "UPDATE `police_b_list` SET `level` = '".$userinfo['level']."', `pro` = '".$userinfo['pro']."', `clan` = '".$userinfo['clan']."', `last_updated` = '".$u_updated."', `online` = '".$userinfo['online']."'  WHERE `nick` = '".$up_nick."' LIMIT 1;";
        mysql_query($query);
        echo ("<font color='red'><b>.</b></font>");
    }
//End
$query = "SELECT `nick` FROM `police_b_list` WHERE `status` = '0' AND `term` < '".$cur_date."';";
$online_list = @mysql_query($query);
while ($online_position = mysql_fetch_array($online_list)) {
    mysql_query("DELETE FROM `black_list` WHERE `nick` = '".$online_position['nick']."';");
}
$query = "SELECT `nick`, `level`, `pro`, `clan` FROM `police_b_list` WHERE `status` = '2' AND `payment_till` < '".$cur_date."';";
$online_list = @mysql_query($query);
while ($online_position = mysql_fetch_array($online_list)) {
    mysql_query("INSERT INTO `black_list (`date`, `city`, `nick`, `level`, `pro`, `clan`, `status`) VALUES (NOW(), 0, '".$online_position['nick']."', '".$online_position['level']."', '".$online_position['pro']."', '".$online_position['clan']."', 0);");
}
$bgstr[0]="background='i/bgr-grid-sand.gif'";
$bgstr[1]="background='i/bgr-grid-sand1.gif'";
if(AuthStatus==1 && AuthUserName!="" && (AuthUserGroup=='100' || AuthUserClan=='police' || AuthUserClan=='POLICE ACADEMY' || AuthUserClan=='Police Academy')) {

$city[0] = 'Везде';
$city[1] = 'NewMoscow';
$city[2] = 'NevaCity';
$city[3] = 'OasisCity';
?>

<table width='100%' border='0' cellspacing='3' cellpadding='2'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Черный список онлайн</strong> </p></td>

<table width=100%>
	<tr bgcolor=#F4ECD4>
		<td width=150><b>Персонаж</b></td>
		<td width=110><b>Онлайн с</b></td>
		<td width=150><b>Замечен</b></td>
		<td><b>Причина</b></td>
	</tr>
<?

$number = 1;
$result = @mysql_query("SELECT * FROM black_list WHERE status=1");
while ($row = mysql_fetch_row($result)) {
	$comments = @mysql_query("SELECT reason, payment FROM police_b_list WHERE nick='".$row[2]."'");
	$pbl = mysql_fetch_array($comments);
	$number++;
	echo "	<tr>";
	echo "<td".($number%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'").">".($row[5]?"<img style='vertical-align:text-bottom' src='_imgs/clans/".$row[5].".gif'>":"").$row[2]." [".$row[3]."]"."<img class='inf_img' style='vertical-align:text-bottom' src='_imgs/pro/i".$row[4].".gif' onclick=\"showInfo('".$row[2]."',event)\"></td>";
	echo "<td".($number%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'").">".substr($row[0],8,2).".".substr($row[0],5,2).".".substr($row[0],0,4)." ".substr($row[0],11,5)."</td>";
	echo "<td".($number%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'")." nowrap>".$row[7]."</td>";
	echo "<td".($number%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'").">".$pbl['reason']."</td></tr>\n";
}

/*
$number = 1;
$result = @mysql_query("SELECT * FROM `police_b_list` WHERE `status`='0' AND `online`='1'");
while ($row = mysql_fetch_row($result)) {
//	$comments = @mysql_query("SELECT reason, payment FROM police_b_list WHERE nick='".$row[2]."'");
//	$pbl = mysql_fetch_array($comments);
	$number++;
	echo "	<tr>";
	echo "<td".($number%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'").">".($row[3]?"<img style='vertical-align:text-bottom' src='_imgs/clans/".$row[3].".gif'>":"").$row[0]." [".$row[1]."]"."<img class='inf_img' style='vertical-align:text-bottom' src='_imgs/pro/i".$row[2].".gif' onclick=\"showInfo('".$row[0]."',event)\"></td>";
//	echo "<td".($number%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'").">".$city[$row[1]]."</td>";
//	echo "<td".($number%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'").">".substr($row[0],8,2).".".substr($row[0],5,2).".".substr($row[0],0,4)." ".substr($row[0],11,5)."</td>";
	echo "<td".($number%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'").">".$row[4]."</td></tr>\n";
}
*/
?>
</table>

</tr><tr><td align=center>

</center>
</td></tr>
</table>

<?
} else echo $mess['AccessDenied'];
?>