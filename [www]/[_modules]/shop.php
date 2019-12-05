<h1>Интернет-магазин "военторгЪ"</h1>

<!-- Insert by deadbeef -->

<script language="Javascript" type="text/javascript">
<!--
function updReady(id) {
	if (pl = window.prompt("Укажите, где находится заказ", ""))
    	{
	if (!pl) alert("Необходимо указать место, где находится заказ!")
    else
    	{
            document.ready_form.place.value = pl;
            document.ready_form.executedID.value = id;
			document.ready_form.submit();
	    }
        }
	}
function updPacked(id) {
	if (pl = window.prompt("Укажите, где можно забрать заказ", ""))
    	{
	if (!pl) alert("Необходимо указать место, где можно забрать заказ!")
    else
    	{
            document.packed_form.place.value = pl;
            document.packed_form.packedID.value = id;
			document.packed_form.submit();
	    }
        }
	}
//-->
</script>
<form name="ready_form" method="post" action="?act=shop">
  <input type="hidden" name="place" value="">
  <input type="hidden" name="executedID" value="">
</form>
<form name="packed_form" method="post" action="?act=shop">
  <input type="hidden" name="place" value="">
  <input type="hidden" name="packedID" value="">
</form>

<!-- End insert by deadbeef -->

<?php

$bgstr[0]="background='i/bgr-grid-sand.gif'";

$bgstr[1]="background='i/bgr-grid-sand1.gif'";

if(AuthStatus==1 && AuthUserName!="" && (AuthUserClan=='police' || AuthUserClan=='Police Academy' || AuthUserGroup == 100)) {


# ASlab admin


if (AuthUserGroup == 100) {

        if($a=="add_item") {

            $SQL="INSERT INTO items_req values('',1,

            '".htmlspecialchars($_REQUEST['item_name'])."',


	    '".$_REQUEST['time']."',

            '".$_REQUEST['coins']."',

            '".$_REQUEST['organic']."',

            '".$_REQUEST['polymer']."',

            '".$_REQUEST['venom']."',

            '".$_REQUEST['rad']."',

            '".$_REQUEST['gold']."',

	    '".$_REQUEST['gem']."',

	    '".$_REQUEST['metal']."',

	    '".$_REQUEST['silicon']."',

	    '".$_REQUEST['max']."')";



            mysql_query($SQL);

            echo "<script>top.location='?act=shop';</script>";

        }

//Modified by deadbeef

	  if(@$_REQUEST['editID']) {

            $SQL="UPDATE items_req SET ".
            "item_name='".$_POST['ProductName']."', ".
			"mnt='".$_POST['Rmnt']."', ".
            "organic='".$_POST['Rorg']."', ".
            "polymer='".$_POST['Rpol']."', ".
            "venom='".$_POST['Rven']."', ".
            "rad='".$_POST['Rrad']."', ".
            "gold='".$_POST['Rgol']."', ".
		    "gem='".$_POST['Rgem']."', ".
			"metal='".$_POST['Rmet']."', ".
			"silicon='".$_POST['Rsil']."' ".
			"WHERE id='".$_POST['editID']."' LIMIT 1;";
            $SQL = str_replace("'NaN'", "'0'", $SQL);

            mysql_query($SQL);
//echo ($SQL);

            echo "<script>top.location='?act=shop';</script>";

        }

	  if(@$_REQUEST['timeID']) {

            $SQL="UPDATE items_req SET ".
			"time_req='".$_POST['Rtime']."', ".
			"imax='".$_POST['Rmax']."' ".
			"WHERE id='".$_POST['timeID']."' LIMIT 1;";
            $SQL = str_replace("'NaN'", "'0'", $SQL);

            mysql_query($SQL);

            echo "<script>top.location='?act=shop';</script>";

        }

//End modified

        if(@$del) {

            $SQL="UPDATE items_req SET is_visible=0 WHERE id='".$del."'";

            mysql_query($SQL);

            echo "<h6>Предмет ID:$del скрыт.</h6>";

        }

	if(@$visible) {

            $SQL="UPDATE items_req SET is_visible=1 WHERE id='".$visible."'";

            mysql_query($SQL);

            echo "<h6>Предмет ID:$visible доступен.</h6>";

        }

	if(@$delitem) {

	    $SQL="UPDATE items_req SET is_visible=2 WHERE id='".$delitem."'";

            mysql_query($SQL);

            echo "<h6>Предмет ID:$delitem удален.</h6>";

        }



		if(@$_REQUEST['prepaidID']>0) {

			$SQL="UPDATE items_order SET order_status='2' WHERE order_status='1' AND id='".addslashes($_REQUEST['prepaidID'])."'";

	        mysql_query($SQL);

	        echo "<h6>Изменен статус у заказа ID:{$_REQUEST['prepaidID']}.</h6>";

        }


//Modified by deadbeef

   		if(@$_REQUEST['executedID']>0) {

//			$SQL="UPDATE items_order SET order_status='3', t2='".time()."' WHERE order_status='2' AND id='".addslashes($_REQUEST['executedID'])."'";

			$SQL="UPDATE items_order SET order_status='3', t2='".time()."', exec_by='".AuthUserName."', stored_in='".addslashes($_REQUEST['place'])."', stored_in_cops='' WHERE order_status='2' AND id='".addslashes($_REQUEST['executedID'])."'";

	        mysql_query($SQL);

	        echo "<h6>Изменен статус у заказа ID:{$_REQUEST['executedID']}.</h6>";

        }


//Insert by deadbeef

   		if(@$_REQUEST['packedID']>0) {

			$SQL="UPDATE items_order SET order_status='4', t2='".time()."', exec_by='".AuthUserName."', stored_in_cops='".addslashes($_REQUEST['place'])."' WHERE order_status='3' AND id='".addslashes($_REQUEST['packedID'])."'";

	        mysql_query($SQL);

	        echo "<h6>Изменен статус у заказа ID:{$_REQUEST['packedID']}.</h6>";

        }

//End insert by deadbeef



		if(@$_REQUEST['delete']>0) {

            $SQL="DELETE FROM items_order where(id='".addslashes($_REQUEST['delete'])."')";

            mysql_query($SQL);

            echo "<h6>Заказ ID:$delete удален из базы.</h6>";

        }



?>



<table width='100%' border='0' cellspacing='3' cellpadding='2'><tr>

<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Добавить вещь:</strong> </p></td>

</tr><tr><td>



<form>

<input name="act" type="hidden" value="shop">

<input name="a" type="hidden" value="add_item">

<table>

<tr>

<td>Название и кол-во</td>

<td align=center><img src="_imgs/tz/coins.gif" height=21></td>

<td align=center><img src="_imgs/tz/polymer.gif" height=21></td>

<td align=center><img src="_imgs/tz/organic.gif" height=21></td>

<td align=center><img src="_imgs/tz/venom.gif"></td>

<td align=center><img src="_imgs/tz/rad.gif"></td>

<td align=center><img src="_imgs/tz/gold.gif" height="21"></td>

<td align=center><img src="_imgs/tz/gem.gif" height="21"></td>

<td align=center><img src="_imgs/tz/metal.gif" height="21"></td>

<td align=center><img src="_imgs/tz/silicon.gif" height="21"></td>

<td></td>

</tr>

<tr>

<td><input name="item_name" type="text" value="" size=30></td>

<td><input name="coins" type="text" value="" size=5></td>

<td><input name="polymer" type="text" value="" size=5></td>

<td><input name="organic" type="text" value="" size=5></td>

<td><input name="venom" type="text" value="" size=5></td>

<td><input name="rad" type="text" value="" size=5></td>

<td><input name="gold" type="text" value="" size=5></td>

<td><input name="gem" type="text" value="" size=5></td>

<td><input name="metal" type="text" value="" size=5></td>

<td><input name="silicon" type="text" value="" size=5></td>

<td rowspan=2 valign="center"><input type="submit" value="Add"></td>

</tr>

<tr>

<td align=right>Время производства</td>

<td align=center colspan=2><input name="time" type="text" value="" size=14></td>

<td align=right colspan=4>Максимум в заказе</td>

<td align=center colspan=2><input name="max" type="text" value="" size=14></td>

<td></td>

</tr>



</table>

</form>



<center>

<script language=javascript src="_modules/tabs.js"></script>

<link href="_modules/tabs.css" type=text/css rel=stylesheet>

<form action="" method="post">

<table border="0" cellspacing="0" cellpadding="0" width="100%" id="tb_content">

<tr>

  <td height="20" class="tab-on" id="navcell" onclick="switchCell(0)" valign="middle"><b>&nbsp;Новые&nbsp;заказы&nbsp;</b></td>

  <td height="20" class="tab-off" id="navcell" onclick="switchCell(1)" valign="middle"><b>&nbsp;Вещи&nbsp;для&nbsp;изготовления&nbsp;</b></td>
<!--Insert by deadbeef -->
  <td height="20" class="tab-off" id="navcell" onclick="switchCell(2)" valign="middle"><b>&nbsp;Изготовленные&nbsp;заказы&nbsp;</b></td>
<!--END Insert by deadbeef -->
  <td height="20" class="tab-off" id="navcell" onclick="switchCell(3)" valign="middle"><b>&nbsp;Неподтверждённые&nbsp;заказы&nbsp;</b></td>

  <td height="20" class="tab-off" id="navcell" onclick="switchCell(4)" valign="middle"><b>&nbsp;История&nbsp;заказов&nbsp;</b></td>

  <TD class=tab-none noWrap><FONT face=Tahoma color="#ffffff">&nbsp;</TD>

</tr>

</table>



<?

###############################

##### new orders

###############################

?>

<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0"><tr><td valign="top" align="center">

<p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;">





</td></tr><tr>

<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Новые заказы:</strong> </p></td>

</tr><tr><td align="center"><br>



<?

$SQL="SELECT i.*, u.id AS UID, u.user_name AS UName, u.clan AS UClan FROM items_order i LEFT JOIN site_users u on u.id=i.id_user WHERE i.order_status='1' ORDER by i.t1 DESC";

$r=mysql_query($SQL);

?>

<table width=97% cellpadding=5>

<?if(mysql_num_rows($r)>0) {?>

<tr  bgcolor=#F4ECD4 align=center>

<td width=10 nowrap><b>ID:</b></td>

<td><b>Заказчик:</b></td>

<td width=100><b>время подачи:</b></td>

<td><b>заказ:</b></td>

<td width=100><b>стоимость:</b></td>

<td width=100><b>изменить статус:</b></td>

</tr>

<?

} else echo "<tr><td align=center> n/a </td></tr>";

$np=0;

while($d=mysql_fetch_array($r)) {

if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

$time_str = '';
$at = $d['order_time'];
if ($at >= 60) {
	$time_str .= floor($at/60) . "ч ";
	$at = $at%60;
}
$time_str .= $at . "мин";

?>

<tr>

<td <?=$bg?> nowrap valign=top><b><?=$d['id']?></b></td>

<td <?=$bg?> nowrap valign=top><?=GetClan($d['UClan']).GetUser($d['UID'],$d['UName'],AuthUserGroup)?></td>

<td <?=$bg?> align=center valign=top><?=date("d.m.y H:i",$d['t1'])?><br>(<b><?=$time_str?></b>)</td>

<td <?=$bg?> valign=top><?

$order=explode("|", $d['order_str']);

$tcost['coins']=0;

$tcost['polymers']=0;

$tcost['organic']=0;

$tcost['venom']=0;

$tcost['radioactive']=0;

$tcost['gold']=0;

$tcost['gem']=0;

$tcost['metal']=0;

$tcost['silicon']=0;

foreach($order AS $order) if(strlen($order)>1) {

        $OrderData=explode("*",$order);

        $dd=mysql_fetch_array(mysql_query("SELECT * FROM items_req WHERE id='".$OrderData[0]."'"));

		$tcost['coins']+=$dd['mnt']*$OrderData[1];

        $tcost['polymers']+=$dd['polymer']*$OrderData[1];

        $tcost['organic']+=$dd['organic']*$OrderData[1];

        $tcost['venom']+=$dd['venom']*$OrderData[1];

        $tcost['radioactive']+=$dd['rad']*$OrderData[1];

        $tcost['gold']+=$dd['gold']*$OrderData[1];

        $tcost['gem']+=$dd['gem']*$OrderData[1];

        $tcost['metal']+=$dd['metal']*$OrderData[1];

        $tcost['silicon']+=$dd['silicon']*$OrderData[1];

        echo $dd['item_name']." <b>".$OrderData[1]."шт.</b><Br> ";

}



?></td><td <?=$bg?> valign=top nowrap><?

foreach($tcost AS $res=>$q) if($q>0) echo "<b>$res: </b>$q<br>";

?></td><td <?=$bg?> align=center>

<a href="?act=shop&prepaidID=<?=$d['id']?>">оплата получена</a><br>

<a href="?act=shop&delete=<?=$d['id']?>">удалить заказ</a>

</td></tr>

<?}?>

</table><br>



</p>

</td></tr></table>



<?

###############################

##### orders to make

###############################

?>

<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0"><tr><td valign="top" align="center">

<p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;">





</td></tr><tr>

<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Вещи для изготовления:</strong> </p></td>

</tr><tr><td align="center"><br>

<?

$SQL="SELECT i.*, u.id AS UID, u.user_name AS UName, u.clan AS UClan FROM items_order i LEFT JOIN site_users u on u.id=i.id_user WHERE i.order_status='2' ORDER by i.t1 DESC";

$r=mysql_query($SQL);

?>

<table width=97% cellpadding=5>

<?if(mysql_num_rows($r)>0) {?>

<tr  bgcolor=#F4ECD4 align=center>

<td width=10 nowrap><b>ID:</b></td>

<td><b>Заказчик:</b></td>

<td width=100><b>время подачи:</b></td>

<td><b>заказ:</b></td>

<td width=100><b>стоимость:</b></td>

<td width=100><b>изменить статус:</b></td>

</tr>

<?

} else echo "<tr><td align=center> n/a </td></tr>";

$np=0;

while($d=mysql_fetch_array($r)) {

if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

$time_str = '';
$at = $d['order_time'];
if ($at >= 60) {
	$time_str .= floor($at/60) . "ч ";
	$at = $at%60;
}
$time_str .= $at . "мин";

?>

<tr>

<td <?=$bg?> nowrap valign=top><b><?=$d['id']?></b></td>

<td <?=$bg?> nowrap valign=top><?=GetClan($d['UClan']).GetUser($d['UID'],$d['UName'],AuthUserGroup)?></td>

<td <?=$bg?> align=center valign=top><?=date("d.m.y H:i",$d['t1'])?><br>(<b><?=$time_str?></b>)</td>

<td <?=$bg?> valign=top><?

$order=explode("|", $d['order_str']);

$tcost['coins']=0;

$tcost['polymers']=0;

$tcost['organic']=0;

$tcost['venom']=0;

$tcost['radioactive']=0;

$tcost['gold']=0;

$tcost['gem']=0;

$tcost['metal']=0;

$tcost['silicon']=0;

foreach($order AS $order) if(strlen($order)>1) {

        $OrderData=explode("*",$order);

        $dd=mysql_fetch_array(mysql_query("SELECT * FROM items_req WHERE id='".$OrderData[0]."'"));

		$tcost['coins']+=$dd['mnt']*$OrderData[1];

        $tcost['polymers']+=$dd['polymer']*$OrderData[1];

        $tcost['organic']+=$dd['organic']*$OrderData[1];

        $tcost['venom']+=$dd['venom']*$OrderData[1];

        $tcost['radioactive']+=$dd['rad']*$OrderData[1];

        $tcost['gold']+=$dd['gold']*$OrderData[1];

        $tcost['gem']+=$dd['gem']*$OrderData[1];

        $tcost['metal']+=$dd['metal']*$OrderData[1];

        $tcost['silicon']+=$dd['silicon']*$OrderData[1];

        echo $dd['item_name']." <b>".$OrderData[1]."шт.</b><Br> ";

}



?></td><td <?=$bg?> valign=top nowrap><?

foreach($tcost AS $res=>$q) if($q>0) echo "<b>$res: </b>$q<br>";

?></td><td <?=$bg?> align=center>
<!-- Modified by deadbeef
<a href="?act=shop&executedID=<?=$d['id']?>">заказ готов</a>-->
<a href="#" onClick="updReady('<?=$d['id']?>'); return false">заказ изготовлен</a>



</td></tr>

<?}?>

</table><br>

</p>

</td></tr></table>

<!--Insert by deadbeef -->














<?

###############################

##### ready to give away orders

###############################?>



<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0"><tr><td valign="top" align="center">

<p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;">

</td></tr><tr>

<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Изготовленные заказы:</strong> </p></td>

</tr><tr><td align="center"><br>



<?

$SQL="SELECT i.*, u.id AS UID, u.user_name AS UName, u.clan AS UClan FROM items_order i LEFT JOIN site_users u on u.id=i.id_user WHERE i.order_status='3' ORDER by u.id";

$r=mysql_query($SQL);

?>

<table width=97% cellpadding=5>

<?if(mysql_num_rows($r)>0) {?>

<tr  bgcolor=#F4ECD4 align=center>

<td width=10 nowrap><b>ID:</b></td>

<td><b>Заказчик:</b></td>

<td width=100><b>время подачи:</b></td>

<td><b>заказ:</b></td>

<td width=100><b>стоимость:</b></td>

<td width=100><b>местонахождение:</b></td>

<td width=110><b>изменить статус:</b></td>

</tr>

<?

} else echo "<tr><td align=center> n/a </td></tr>";

$np=0;

while($d=mysql_fetch_array($r)) {

if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

?>

<tr>

<td <?=$bg?> nowrap valign=top><b><?=$d['id']?></b></td>

<td <?=$bg?> nowrap valign=top><?=GetClan($d['UClan']).GetUser($d['UID'],$d['UName'],AuthUserGroup)?></td>

<td <?=$bg?> align=center valign=top><?=date("d.m.y H:i",$d['t1'])?></td>

<td <?=$bg?> valign=top><?

$order=explode("|", $d['order_str']);

$tcost['coins']=0;

$tcost['polymers']=0;

$tcost['organic']=0;

$tcost['venom']=0;

$tcost['radioactive']=0;

$tcost['gold']=0;

$tcost['gem']=0;

$tcost['metal']=0;

$tcost['silicon']=0;

foreach($order AS $order) if(strlen($order)>1) {

        $OrderData=explode("*",$order);

        $dd=mysql_fetch_array(mysql_query("SELECT * FROM items_req WHERE id='".$OrderData[0]."'"));

		$tcost['coins']+=$dd['mnt']*$OrderData[1];

        $tcost['polymers']+=$dd['polymer']*$OrderData[1];

        $tcost['organic']+=$dd['organic']*$OrderData[1];

        $tcost['venom']+=$dd['venom']*$OrderData[1];

        $tcost['radioactive']+=$dd['rad']*$OrderData[1];

        $tcost['gold']+=$dd['gold']*$OrderData[1];

        $tcost['gem']+=$dd['gem']*$OrderData[1];

        $tcost['metal']+=$dd['metal']*$OrderData[1];

        $tcost['silicon']+=$dd['silicon']*$OrderData[1];

        echo $dd['item_name']." <b>".$OrderData[1]."шт.</b><Br> ";

}



?></td><td <?=$bg?> valign=top nowrap><?

foreach($tcost AS $res=>$q) if($q>0) echo "<b>$res: </b>$q<br>";

?></td>
<td <?=$bg?> align=center><?
if(strlen($d['stored_in']) > 0)
	{
		echo (stripslashes($d['stored_in']));
    }
else
	{
    	echo ("n/a");
	}
if(strlen($d['exec_by']) > 0)
	{
    	echo ("<br><b>(".stripslashes($d['exec_by']).")</b>");
    }
?></td>

<td <?=$bg?> align=center>

<a href="#" onClick="updPacked('<?=$d['id']?>'); return false">заказ готов к выдаче</a>

</td></tr>

<?}?>

</table><br>



</p>

</td></tr></table>






<!-- END Insert by deadbeef -->














<?

###############################

##### unconfirmed orders

###############################?>



<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0"><tr><td valign="top" align="center">

<p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;">

</td></tr><tr>

<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Неподтверждённые заказы:</strong> </p></td>

</tr><tr><td align="center"><br>



<?

$SQL="SELECT i.*, u.id AS UID, u.user_name AS UName, u.clan AS UClan FROM items_order i LEFT JOIN site_users u on u.id=i.id_user WHERE i.order_status='4' ORDER by u.id";

$r=mysql_query($SQL);

?>

<table width=97% cellpadding=5>

<?if(mysql_num_rows($r)>0) {?>

<tr  bgcolor=#F4ECD4 align=center>

<td width=10 nowrap><b>ID:</b></td>

<td><b>Заказчик:</b></td>

<td width=100><b>время подачи:</b></td>

<td><b>заказ:</b></td>

<td width=100><b>стоимость:</b></td>

<!-- TEMPORARY -->

<td width=100><b>местонахождение:</b></td>

<!-- /TEMPORARY -->

<?
//<td width=110><b>отметка о получении:</b></td>
?>

</tr>

<?

} else echo "<tr><td align=center> n/a </td></tr>";

$np=0;

while($d=mysql_fetch_array($r)) {

if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

?>

<tr>

<td <?=$bg?> nowrap valign=top><b><?=$d['id']?>  [<a href='#; return false;' onclick="if(confirm('Вы уверены?')) top.location='?act=shop&delete=<?=$d['id']?>'">X</a>]</b></td>

<td <?=$bg?> nowrap valign=top><?=GetClan($d['UClan']).GetUser($d['UID'],$d['UName'],AuthUserGroup)?></td>

<td <?=$bg?> align=center valign=top><?=date("d.m.y H:i",$d['t1'])?></td>

<td <?=$bg?> valign=top><?

$order=explode("|", $d['order_str']);

$tcost['coins']=0;

$tcost['polymers']=0;

$tcost['organic']=0;

$tcost['venom']=0;

$tcost['radioactive']=0;

$tcost['gold']=0;

$tcost['gem']=0;

$tcost['metal']=0;

$tcost['silicon']=0;

foreach($order AS $order) if(strlen($order)>1) {

        $OrderData=explode("*",$order);

        $dd=mysql_fetch_array(mysql_query("SELECT * FROM items_req WHERE id='".$OrderData[0]."'"));

		$tcost['coins']+=$dd['mnt']*$OrderData[1];

        $tcost['polymers']+=$dd['polymer']*$OrderData[1];

        $tcost['organic']+=$dd['organic']*$OrderData[1];

        $tcost['venom']+=$dd['venom']*$OrderData[1];

        $tcost['radioactive']+=$dd['rad']*$OrderData[1];

        $tcost['gold']+=$dd['gold']*$OrderData[1];

        $tcost['gem']+=$dd['gem']*$OrderData[1];

        $tcost['metal']+=$dd['metal']*$OrderData[1];

        $tcost['silicon']+=$dd['silicon']*$OrderData[1];

        echo $dd['item_name']." <b>".$OrderData[1]."шт.</b><Br> ";

}



?></td><td <?=$bg?> valign=top nowrap><?

foreach($tcost AS $res=>$q) if($q>0) echo "<b>$res: </b>$q<br>";

?></td>
<td <?=$bg?> align=center><?
if(strlen($d['stored_in_cops']) > 0)
	{
		echo (stripslashes($d['stored_in_cops']));
    }
else
	{
    	echo ("n/a");
	}
if(strlen($d['exec_by']) > 0)
	{
    	echo ("<br><b>(".stripslashes($d['exec_by']).")</b>");
    }
?></td>

<?
//<td <?=$bg? > align=center>

//<?if($d['order_status']=='0') echo "заказ получен";? >

//</td>
?>

</tr>

<?}?>

</table><br>



</p>

</td></tr></table>



<?

###############################

##### old orders

###############################?>

<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0"><tr><td valign="top" align="center">

<p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;">

</td></tr><tr>

<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>История заказов:</strong> </p></td>

</tr><tr><td align="center"><br>



<form method="GET">

<input name="act" type="hidden" value="shop">

Вся история: <select size="1" name="view_history" onchange="submit();">

<option value='' selected>-- select user --</option>

<?

$r=mysql_query("SELECT DISTINCT o.id_user, u.user_name FROM items_order o LEFT JOIN site_users u ON u.id=o.id_user ORDER by u.user_name");

while($data=mysql_fetch_array($r)) echo "<option value='{$data['id_user']}'>{$data['user_name']}</option>";

?>

</select><br>

</form>



</center>



<?

if(@$_REQUEST['view_history']) {

$SQL="SELECT i.*, u.id AS UID, u.user_name AS UName, u.clan AS UClan FROM items_order i LEFT JOIN site_users u on u.id=i.id_user WHERE (i.order_status='0' OR i.order_status='3') AND i.id_user='".htmlspecialchars($_REQUEST['view_history'])."' ORDER by i.t1 DESC";

$r=mysql_query($SQL);

?>

<table width=97% cellpadding=5>

<?if(mysql_num_rows($r)>0) {?>

<tr  bgcolor=#F4ECD4 align=center>

<td width=10 nowrap><b>ID:</b></td>

<td><b>Заказчик:</b></td>

<td width=100><b>время подачи:</b></td>

<td><b>заказ:</b></td>

<td width=100><b>стоимость:</b></td>

<td width=110><b>отметка о получении:</b></td>

</tr>

<?

} else echo "<tr><td align=center> n/a </td></tr>";

$np=0;

while($d=mysql_fetch_array($r)) {

if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

?>

<tr>

<td <?=$bg?> nowrap valign=top><b><?=$d['id']?></b></td>

<td <?=$bg?> nowrap valign=top><?=GetClan($d['UClan']).GetUser($d['UID'],$d['UName'],AuthUserGroup)?></td>

<td <?=$bg?> align=center valign=top><?=date("d.m.y H:i",$d['t1'])?></td>

<td <?=$bg?> valign=top><?

$order=explode("|", $d['order_str']);

$tcost['coins']=0;

$tcost['polymers']=0;

$tcost['organic']=0;

$tcost['venom']=0;

$tcost['radioactive']=0;

$tcost['gold']=0;

$tcost['gem']=0;

$tcost['metal']=0;

$tcost['silicon']=0;

foreach($order AS $order) if(strlen($order)>1) {

        $OrderData=explode("*",$order);

        $dd=mysql_fetch_array(mysql_query("SELECT * FROM items_req WHERE id='".$OrderData[0]."'"));

		$tcost['coins']+=$dd['mnt']*$OrderData[1];

        $tcost['polymers']+=$dd['polymer']*$OrderData[1];

        $tcost['organic']+=$dd['organic']*$OrderData[1];

        $tcost['venom']+=$dd['venom']*$OrderData[1];

        $tcost['radioactive']+=$dd['rad']*$OrderData[1];

        $tcost['gold']+=$dd['gold']*$OrderData[1];

        $tcost['gem']+=$dd['gem']*$OrderData[1];

        $tcost['metal']+=$dd['metal']*$OrderData[1];

        $tcost['silicon']+=$dd['silicon']*$OrderData[1];

        echo $dd['item_name']." <b>".$OrderData[1]."шт.</b><Br> ";

}



?></td><td <?=$bg?> valign=top nowrap><?

foreach($tcost AS $res=>$q) if($q>0) echo "<b>$res: </b>$q<br>";

?></td><td <?=$bg?> align=center>

<?if($d['order_status']=='0') echo "заказ получен";?>

</td></tr>

<?}?>

</table><br><br>

<?} // if login selected?>

</td></tr>

</table>

</p>

</td></tr></table><br><br>

<?





}

##################################

##### Make order

##################################



if(strlen($_REQUEST['order'])>2) {



         $SQL="INSERT INTO items_order (id_user, order_time, order_str, t1)

         values('".AuthUserId."','".$_REQUEST['time']."','".$_REQUEST['order']."','".time()."')";

         mysql_query($SQL);

         echo "<h6>Заказ отправлен</h6>";

         echo "<script>top.location='?act=shop';</script>";



}



?>

<center>

<table border="0" cellspacing="0" background="i/bgr-grid-sand.gif" cellpadding="10" style="BORDER: 1px #957850 solid;">

<tr><td>

<table width="100%">

<tr><td align="center"><font color="red"><b>Внимание!</b></font><br></td></tr></table>

1. Время ожидания выполнения заказа в будни до двух суток, в выходные до трёх суток.<br>

2. Перед подачей заказа убедитесь, что положили необходимые ресурсы на мульта<br>
3. Ресурсы на мульта должны быть положены только игроком, который оформляет заказ<br>
4. Ресурсы от неизвестных игроков считаются помощью полиции и не учитываются<br>
5. После получения товара необходимо ставить пометку об этом

</td></tr>

</table>

</center>

<br>


<?
if (AuthUserGroup == 100) {
?>

<form action="" method="post">

<table border="0" cellspacing="0" cellpadding="0" width="100%" id="tb_content">

<tr>

  <td height="20" class="tab-off" id="navcell" onclick="switchCell(5)" valign="middle"><b>&nbsp;Видимые&nbsp;позиции&nbsp;</b></td>

  <td height="20" class="tab-off" id="navcell" onclick="switchCell(6)" valign="middle"><b>&nbsp;Скрытые&nbsp;позиции</b></td>

  <td height="20" class="tab-off" id="navcell" onclick="switchCell(7)" valign="middle"><b>&nbsp;Время&nbsp;изготовления</b></td>

  <TD class=tab-none noWrap><FONT face=Tahoma color="#ffffff">&nbsp;</TD>

</tr>

</table>

<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0"><tr><td valign="top" align="center">

<p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;">

<table width='97%' border='0' cellspacing='3' cellpadding='2'><tr>

<?
} else {
?>

<table width='100%' border='0' cellspacing='3' cellpadding='2'><tr>

<?
}

$SQL="SELECT SUM(`mnt`), SUM(`organic`), SUM(`polymer`), SUM(`venom`), SUM(`rad`), SUM(`gold`), SUM(`gem`), SUM(`metal`), SUM(`silicon`) FROM `items_req` WHERE `is_visible` = 1";

$sum = @mysql_query($SQL);

list($smnt, $sorg, $spol, $sven, $srad, $sgol, $sgem, $smet, $ssil) = mysql_fetch_row($sum);
?>

<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Сделать заказ:</strong> </p></td>

</tr><tr><td>



<table width=100%>


<?
/*
<tr bgcolor=#F4ECD4>

<td bgcolor=#F4ECD4><b>Название:</b></td>

<? if ($smnt) { ?>
<td align=center><img src="_imgs/tz/coins.gif" height=21></td>
<? } ?>

<? if ($spol) { ?>
<td align=center><img src="_imgs/tz/polymer.gif" height=21></td>
<? } ?>

<? if ($sorg) { ?>
<td align=center><img src="_imgs/tz/organic.gif" height=21></td>
<? } ?>

<? if ($sven) { ?>
<td align=center><img src="_imgs/tz/venom.gif"></td>
<? } ?>

<? if ($srad) { ?>
<td align=center><img src="_imgs/tz/rad.gif"></td>
<? } ?>

<? if ($sgol) { ?>
<td align=center><img src="_imgs/tz/gold.gif" height="21"></td>
<? } ?>

<? if ($sgem) { ?>
<td align=center><img src="_imgs/tz/gem.gif" height="21"></td>
<? } ?>

<? if ($smet) { ?>
<td align=center><img src="_imgs/tz/metal.gif" height="21"></td>
<? } ?>

<? if ($ssil) { ?>
<td align=center><img src="_imgs/tz/silicon.gif" height="21"></td>
<? } ?>

<td bgcolor=#F4ECD4 align=center>кол-во:</td>

<td align=center bgcolor=#F8F6ED><b>Заказ:</b></td>

</tr>
*/
?>


<script>

function AddItem(obj,act,c) {



<? if ($smnt) { ?>
var TcostMNT = Math.abs(document.tcnt.Rmnt.value);
<? } ?>

<? if ($spol) { ?>
var TcostPOL = Math.abs(document.tcnt.Rpol.value);
<? } ?>

<? if ($sorg) { ?>
var TcostORG = Math.abs(document.tcnt.Rorg.value);
<? } ?>

<? if ($sven) { ?>
var TcostVEN = Math.abs(document.tcnt.Rven.value);
<? } ?>

<? if ($srad) { ?>
var TcostRAD = Math.abs(document.tcnt.Rrad.value);
<? } ?>

<? if ($sgol) { ?>
var TcostGOL = Math.abs(document.tcnt.Rgol.value);
<? } ?>

<? if ($sgem) { ?>
var TcostGEM = Math.abs(document.tcnt.Rgem.value);
<? } ?>

<? if ($smet) { ?>
var TcostMET = Math.abs(document.tcnt.Rmet.value);
<? } ?>

<? if ($ssil) { ?>
var TcostSIL = Math.abs(document.tcnt.Rsil.value);
<? } ?>

var TcostTIME = Math.abs(document.tcnt.Rtime.value);

var TcostMAX = Math.abs(obj.form.Rmax.value);

var Tcnt = Math.abs(obj.form.cnt.value);



if(act=="+") {

    if (TcostTIME+Math.abs(obj.form.Rtime.value)>240) {
	    alert('Превышено время изготовления одного заказа!');
    } else {

	    if ((Tcnt+1>Math.abs(obj.form.Rmax.value)) && (Math.abs(obj.form.Rmax.value)>0)) {
			alert('Превышен максимум в заказе!');
	    } else {

<? if ($smnt) { ?>
    var MNT=TcostMNT+Math.abs(obj.form.Rmnt.value);
<? } ?>

<? if ($spol) { ?>
    var POL=TcostPOL+Math.abs(obj.form.Rpol.value);
<? } ?>

<? if ($sorg) { ?>
    var ORG=TcostORG+Math.abs(obj.form.Rorg.value);
<? } ?>

<? if ($sven) { ?>
    var VEN=TcostVEN+Math.abs(obj.form.Rven.value);
<? } ?>

<? if ($srad) { ?>
    var RAD=TcostRAD+Math.abs(obj.form.Rrad.value);
<? } ?>

<? if ($sgol) { ?>
    var GOL=TcostGOL+Math.abs(obj.form.Rgol.value);
<? } ?>

<? if ($sgem) { ?>
    var GEM=TcostGEM+Math.abs(obj.form.Rgem.value);
<? } ?>

<? if ($smet) { ?>
    var MET=TcostMET+Math.abs(obj.form.Rmet.value);
<? } ?>

<? if ($ssil) { ?>
    var SIL=TcostSIL+Math.abs(obj.form.Rsil.value);
<? } ?>

    var TIME=TcostTIME+Math.abs(obj.form.Rtime.value);

    var bbb=Tcnt+1;
	  }
    }
}



if(act=="-") {



<? if ($smnt) { ?>
    var MNT=TcostMNT-Math.abs(obj.form.Rmnt.value);
<? } ?>

<? if ($spol) { ?>
    var POL=TcostPOL-Math.abs(obj.form.Rpol.value);
<? } ?>

<? if ($sorg) { ?>
    var ORG=TcostORG-Math.abs(obj.form.Rorg.value);
<? } ?>

<? if ($sven) { ?>
    var VEN=TcostVEN-Math.abs(obj.form.Rven.value);
<? } ?>

<? if ($srad) { ?>
    var RAD=TcostRAD-Math.abs(obj.form.Rrad.value);
<? } ?>

<? if ($sgol) { ?>
    var GOL=TcostGOL-Math.abs(obj.form.Rgol.value);
<? } ?>

<? if ($sgem) { ?>
    var GEM=TcostGEM-Math.abs(obj.form.Rgem.value);
<? } ?>

<? if ($smet) { ?>
    var MET=TcostMET-Math.abs(obj.form.Rmet.value);
<? } ?>

<? if ($ssil) { ?>
    var SIL=TcostSIL-Math.abs(obj.form.Rsil.value);
<? } ?>

    var TIME=TcostTIME-Math.abs(obj.form.Rtime.value);

    var bbb=Tcnt-1;



}



if((Tcnt>0) || (act=="+")) {

    if ((TcostTIME+Math.abs(obj.form.Rtime.value)<=240) || (act!="+")) {

	    if ((Tcnt+1<=Math.abs(obj.form.Rmax.value)) || (Math.abs(obj.form.Rmax.value)==0) || (act!="+")) {

	    if (act=="+") {
			items[c]+=1;
	    } else {
			items[c]-=1;
	    }

<? if ($smnt) { ?>
document.tcnt.Rmnt.value=MNT;
<? } ?>

<? if ($spol) { ?>
document.tcnt.Rpol.value=POL;
<? } ?>

<? if ($sorg) { ?>
document.tcnt.Rorg.value=ORG;
<? } ?>

<? if ($sven) { ?>
document.tcnt.Rven.value=VEN;
<? } ?>

<? if ($srad) { ?>
document.tcnt.Rrad.value=RAD;
<? } ?>

<? if ($sgol) { ?>
document.tcnt.Rgol.value=GOL;
<? } ?>

<? if ($sgem) { ?>
document.tcnt.Rgem.value=GEM;
<? } ?>

<? if ($smet) { ?>
document.tcnt.Rmet.value=MET;
<? } ?>

<? if ($ssil) { ?>
document.tcnt.Rsil.value=SIL;
<? } ?>

document.tcnt.Rtime.value=TIME;

obj.form.cnt.value=bbb;
	  }
    }
}

document.tcnt.RTstr.value='';
var a = document.tcnt.Rtime.value;
if (a>=1440) {
	document.tcnt.RTstr.value = Math.floor(a/1440)+'д ';
	a = a%1440;
}
if (a>=60) {
	document.tcnt.RTstr.value += Math.floor(a/60)+'ч ';
	a = a%60;
}
document.tcnt.RTstr.value += a+'мин';

}



<?$d=mysql_fetch_array(mysql_query("SELECT MAX(id) as maxx FROM items_req"));?>

var items = new Array(<?=$d['maxx']+1?>);

for(var i=0;i<=<?=$d['maxx']+1?>;i++) items[i]=0;

</script>



<?
################ visibles

$thead='
<tr bgcolor=#F4ECD4>
<td bgcolor=#F4ECD4><b>Название:</b></td>'.
($smnt?'<td align=center><img src="_imgs/tz/coins.gif" height=21></td>'."\n":"\n").
($spol?'<td align=center><img src="_imgs/tz/polymer.gif" height=21></td>'."\n":"\n").
($sorg?'<td align=center><img src="_imgs/tz/organic.gif" height=21></td>'."\n":"\n").
($sven?'<td align=center><img src="_imgs/tz/venom.gif"></td>'."\n":"\n").
($srad?'<td align=center><img src="_imgs/tz/rad.gif"></td>'."\n":"\n").
($sgol?'<td align=center><img src="_imgs/tz/gold.gif" height="21"></td>'."\n":"\n").
($sgem?'<td align=center><img src="_imgs/tz/gem.gif" height="21"></td>'."\n":"\n").
($smet?'<td align=center><img src="_imgs/tz/metal.gif" height="21"></td>'."\n":"\n").
($ssil?'<td align=center><img src="_imgs/tz/silicon.gif" height="21"></td>'."\n":"\n").
'<td bgcolor=#F4ECD4 align=center>кол-во:</td>
<td align=center bgcolor=#F8F6ED><b>Заказ:</b></td>
</tr>
';


$SQL="SELECT * FROM items_req WHERE is_visible='1' ORDER BY id desc";
$r=mysql_query($SQL);
$np=0;
$theadercount=0;

while($d=mysql_fetch_array($r)) {
if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

if(($theadercount%20)==0) echo $thead;

$theadercount++;
?>

<tr><form name="prodid_<?=$d['id']?>">
<input name="ProductId" type="hidden" value="<?=$d['id']?>">
<td <?=$bg?>><?=$d['item_name']?>
<?if(AuthUserGroup == 100) echo " (<a href='#; return false;' onclick=\"if(confirm('Скрыть предмет?')) top.location='?act=shop&del={$d[id]}';\">X</a>)"?>
</td>
<? if ($smnt) { ?>
<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rmnt" type="text" value="<?=$d['mnt']?>" size=5></td>
<? } ?>
<? if ($spol) { ?>
<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rpol" type="text" value="<?=$d['polymer']?>" size=4></td>
<? } ?>
<? if ($sorg) { ?>
<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rorg" type="text" value="<?=$d['organic']?>" size=4></td>
<? } ?>
<? if ($sven) { ?>
<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rven" type="text" value="<?=$d['venom']?>" size=4></td>
<? } ?>
<? if ($srad) { ?>
<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rrad" type="text" value="<?=$d['rad']?>" size=4></td>
<? } ?>
<? if ($sgol) { ?>
<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rgol" type="text" value="<?=$d['gold']?>" size=4></td>
<? } ?>
<? if ($sgem) { ?>
<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rgem" type="text" value="<?=$d['gem']?>" size=4></td>
<? } ?>
<? if ($smet) { ?>
<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rmet" type="text" value="<?=$d['metal']?>" size=4></td>
<? } ?>
<? if ($ssil) { ?>
<td <?=$bg?> align=center><input style="text-align:center" disabled name="Rsil" type="text" value="<?=$d['silicon']?>" size=4></td>
<? } ?>
<td bgcolor=#F4ECD4 align=center><input style="text-align:right" name="cnt" disabled type="text" value="0" size=4><input name="Rmax" type="hidden" value="<?=$d['imax']?>"></td>
<td align=center bgcolor=#F8F6ED><input name="Rtime" type="hidden" value="<?=$d['time_req']?>"><input type="button" value="+1" style="width:20" onclick="AddItem(this,'+','<?=$d['id']?>');"> <input type="button" value="-1" style="width:20"  onclick="AddItem(this,'-','<?=$d['id']?>');"></td>
</form>
</tr>





<?

}
echo $thead;
?>





<tr><form name="tcnt">

<td bgcolor=#F4ECD4><b>Общая стоимость: </b></td>

<? if ($smnt) { ?>
<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rmnt" type="text" size=5></td>
<? } ?>

<? if ($spol) { ?>
<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rpol" type="text" value="" size=4></td>
<? } ?>

<? if ($sorg) { ?>
<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rorg" type="text" value="" size=4></td>
<? } ?>

<? if ($sven) { ?>
<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rven" type="text" value="" size=4></td>
<? } ?>

<? if ($srad) { ?>
<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rrad" type="text" value="" size=4></td>
<? } ?>

<? if ($sgol) { ?>
<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rgol" type="text" value="" size=4></td>
<? } ?>

<? if ($sgem) { ?>
<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rgem" type="text" value="" size=4></td>
<? } ?>

<? if ($smet) { ?>
<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rmet" type="text" value="" size=4></td>
<? } ?>

<? if ($ssil) { ?>
<td bgcolor=#F4ECD4 align=center><input style="text-align:center" disabled name="Rsil" type="text" value="" size=4></td>
<? } ?>

<td  align=center colspan=2><input name="Rtime" type="hidden" value="" size=15><input style="text-align:center" disabled name="RTstr" type="text" value="Время заказа" size=15></td>

</form></tr>

</table>



<br><center><!--<a onclick="MakeOrder()" href="#; return false;">сделать заказ</a> --><input type="button" onclick="MakeOrder()" href="#; return false;" style="CURSOR: hand" value="Сделать заказ"></center>

<script>



function MakeOrder() {

  var req="";

  for(var i=0;i<items.length-1;i++) if(items[i]>0) req+=i+"*"+items[i]+"|";

  if(req.length>1) {

       if(confirm('Вы уверены в своем заказе?')) top.location='?act=shop&order='+req+'&time='+document.tcnt.Rtime.value;

  } else alert('Невозможно сделать пустой заказ!\n');

}



</script>



</td></tr>

</table><br>


<?
if (AuthUserGroup == 100) {
?>

</td></tr></table>

<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0"><tr><td valign="top" align="center">

<p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;">

<table width=97%>

<tr bgcolor=#F4ECD4>
<td bgcolor=#F4ECD4><b>Название:</b></td>
<td align=center><img src="_imgs/tz/coins.gif" height=21></td>
<td align=center><img src="_imgs/tz/polymer.gif" height=21></td>
<td align=center><img src="_imgs/tz/organic.gif" height=21></td>
<td align=center><img src="_imgs/tz/venom.gif"></td>
<td align=center><img src="_imgs/tz/rad.gif"></td>
<td align=center><img src="_imgs/tz/gold.gif" height="21"></td>
<td align=center><img src="_imgs/tz/gem.gif" height="21"></td>
<td align=center><img src="_imgs/tz/metal.gif" height="21"></td>
<td align=center><img src="_imgs/tz/silicon.gif" height="21"></td>
<td align=center bgcolor=#F8F6ED><b>Править</b></td>
</tr>

<script>

function edit(obj,ItemID) {

	obj.form.Rmnt.value = Math.abs(obj.form.Rmnt.value);
	obj.form.Rpol.value = Math.abs(obj.form.Rpol.value);
	obj.form.Rorg.value = Math.abs(obj.form.Rorg.value);
	obj.form.Rven.value = Math.abs(obj.form.Rven.value);
	obj.form.Rrad.value = Math.abs(obj.form.Rrad.value);
	obj.form.Rgol.value = Math.abs(obj.form.Rgol.value);
	obj.form.Rgem.value = Math.abs(obj.form.Rgem.value);
	obj.form.Rmet.value = Math.abs(obj.form.Rmet.value);
	obj.form.Rsil.value = Math.abs(obj.form.Rsil.value);
	obj.form.submit();

}

</script>

<?

$SQL="SELECT * FROM items_req WHERE is_visible='0' ORDER BY id desc";

$r=mysql_query($SQL);

$np=0;

while($d=mysql_fetch_array($r)) {

if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

?>

<tr><form name="prodid_<?=$d['id']?>" action="?act=shop" method="POST">

<input name="editID" type="hidden" value="<?=$d['id']?>">

<td <?=$bg?>><input name="ProductName" type="text" value="<?=$d['item_name']?>" size=40>

<?echo " (<a href='#; return false;' style='color: green' onclick=\"if(confirm('Вернуть предмет?')) top.location='?act=shop&visible={$d[id]}';\">X</a>)"?>

<?echo " (<a href='#; return false;' onclick=\"if(confirm('Удалить предмет?')) top.location='?act=shop&delitem={$d[id]}';\">X</a>)"?></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rmnt" type="text" value="<?=$d['mnt']?>" size=5></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rpol" type="text" value="<?=$d['polymer']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rorg" type="text" value="<?=$d['organic']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rven" type="text" value="<?=$d['venom']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rrad" type="text" value="<?=$d['rad']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rgol" type="text" value="<?=$d['gold']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rgem" type="text" value="<?=$d['gem']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rmet" type="text" value="<?=$d['metal']?>" size=4></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rsil" type="text" value="<?=$d['silicon']?>" size=4></td>

<td align=center bgcolor=#F8F6ED><input type=button value=" -*- " onclick="if(confirm('Изменить?')) edit(this,'<?=$d[id]?>');"></td>

</form></tr>

<?
}
?>

</table>

</td></tr></table>

<?
//********************************
// Время изготовления
//********************************
if (AuthUserGroup == 100) {
?>

<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0"><tr><td valign="top" align="center">

<p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;">

<table width=97%>

<tr bgcolor=#F4ECD4>

<td bgcolor=#F4ECD4 width=99%><b>Название:</b></td>

<td align=center>Время изготовления</td>

<td align=center>Максимум в заказе</td>

<td align=center bgcolor=#F8F6ED><b>Править</b></td>

</tr>

<script>

function settime(obj,ItemID) {

	obj.form.Rtime.value = Math.abs(obj.form.Rtime.value);
	obj.form.Rmax.value = Math.abs(obj.form.Rmax.value);
	obj.form.submit();

}

</script>

<?

$SQL="SELECT * FROM items_req WHERE is_visible='0' ORDER BY id desc";

$r=mysql_query($SQL);

$np=0;

while($d=mysql_fetch_array($r)) {

if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}

?>

<tr><form name="prodid_<?=$d['id']?>" action="?act=shop" method="POST">

<input name="timeID" type="hidden" value="<?=$d['id']?>">

<td <?=$bg?>><?=$d['item_name']?></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rtime" type="text" value="<?=$d['time_req']?>" size=15></td>

<td <?=$bg?> align=center><input style="text-align:center" name="Rmax" type="text" value="<?=$d['imax']?>" size=15></td>

<td align=center bgcolor=#F8F6ED><input type=button value=" -*- " onclick="if(confirm('Изменить?')) settime(this,'<?=$d[id]?>');"></td>

</form></tr>

<?
}
?>

</table>

</td></tr></table>

<?
}
//********************************
?>

</form>

<br>

<?
}
?>




<table width='100%' border='0' cellspacing='3' cellpadding='2'><tr>

<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Мои заказы:</strong> </p></td>

</tr><tr><td>



<table width=100% cellpadding=5>

<tr  bgcolor=#F4ECD4 align=center>

<td width=10 nowrap><b>ID:</b></td>

<td><b>заказ:</b></td>

<td width=100><b>время подачи:</b></td>

<td><b>Стоимость:</b></td>

<td width=100><b>статус:</b></td>

<td width=100><b>время выдачи:</b></td>
<!-- Insert by deadbeef -->
<td width=100><b>место выдачи:</b></td>
<!-- End insert by deadbeef -->
</tr>



<?

$SQL="SELECT i.*, u.id AS UID, u.user_name AS UName, u.clan AS UClan FROM items_order i LEFT JOIN site_users u on u.id=i.id_user WHERE i.id_user='".AuthUserId."' ORDER by i.order_status DESC, i.t1 DESC";

$r=mysql_query($SQL);

$BeginOld=0;

while($d=mysql_fetch_array($r)) {

$time_str = '';
$at = $d['order_time'];
if ($at >= 60) {
	$time_str .= floor($at/60) . "ч ";
	$at = $at%60;
}
$time_str .= $at . "мин";

if($d['order_status']=='0' && $BeginOld=='0') {

echo "

	</table>

    <center><a onclick=\"javascript:if(lold.style.display=='none') lold.style.display=''; else lold.style.display='none';\" href='#;return false;'>Показать/скрыть полученные заказы</a></center>

    <div id=\"lold\" style=\"display:none\">

	<table width='100%' border='0' cellspacing='3' cellpadding='2'>

";

$BeginOld=1;

}

?>

<tr>

<td width=10 nowrap valign=top><b><?=$d['id']?></b></td>

<td valign=top nowrap><?

$order=explode("|", $d['order_str']);

$tcost['coins']=0;

$tcost['polymers']=0;

$tcost['organic']=0;

$tcost['venom']=0;

$tcost['radioactive']=0;

$tcost['gold']=0;

$tcost['gem']=0;

$tcost['metal']=0;

$tcost['silicon']=0;

foreach($order AS $order) if(strlen($order)>1) {

        $OrderData=explode("*",$order);

        $ddd=mysql_fetch_array(mysql_query("SELECT * FROM items_req WHERE id='{$OrderData[0]}'"));

		$tcost['coins']+=$ddd['mnt']*$OrderData[1];

        $tcost['polymers']+=$ddd['polymer']*$OrderData[1];

        $tcost['organic']+=$ddd['organic']*$OrderData[1];

        $tcost['venom']+=$ddd['venom']*$OrderData[1];

        $tcost['radioactive']+=$ddd['rad']*$OrderData[1];

        $tcost['gold']+=$ddd['gold']*$OrderData[1];

        $tcost['gem']+=$ddd['gem']*$OrderData[1];

        $tcost['metal']+=$ddd['metal']*$OrderData[1];

        $tcost['silicon']+=$ddd['silicon']*$OrderData[1];

        echo $ddd['item_name']." <b>".$OrderData[1]."шт.</b><Br> ";

}

?></td>

<td width=100 align=center><?=date("d.m.y H:i",$d['t1'])?><br>(<b><?=$time_str?></b>)</td>

<td  valign=top nowrap><?

foreach($tcost AS $res=>$q) if($q>0) echo "<b>$res: </b>$q<br>";

?></td>

<td width=100 align=center><?

if($d['order_status']=='1') {

if($_REQUEST['cancelID']==$d['id']) {

        mysql_query("DELETE FROM items_order WHERE order_status='1' AND id='{$d[id]}'");

        echo "<script>top.location='?act=shop';</script>";

}

echo "заказ отправлен<br>Вы еще можете его <a href='?act=shop&cancelID={$d['id']}'>отменить</a>.";



}elseif($d['order_status']=='2' || $d['order_status']=='3') {

echo "заказ принят к исполнению";



}
/*
elseif($d['order_status']=='3') {

echo "производство заказа завершено";



}
*/
elseif($d['order_status']=='4') {

if($_REQUEST['getID']==$d['id']) {

	mysql_query("UPDATE items_order SET order_status='0' WHERE order_status='4' AND id='{$d[id]}'");

    echo "<script>top.location='?act=shop';</script>";

}

	echo "заказ готов<br><a href='#; return false;' onclick=\"if(confirm('Вы получили заказ?')) top.location='?act=shop&getID={$d['id']}'\">сделайте пометку</a> о получении.";



} elseif($d['order_status']=='0') echo "заказ получен";

?></td>

<td width=100 align=center><?if($d['t2']>0 && ($d['order_status']=='4' || $d['order_status']=='0')) echo date("d.m.y H:i",$d['t2']); else echo "n/a";?></td>

<!-- Insert by deadbeef -->
<td width=100 align=center><?if(strlen($d['stored_in_cops']) > 0) echo (stripslashes($d['stored_in_cops'])); else echo "n/a";?></td>
<!-- End insert by deadbeef -->
</tr>

<?}?>



</table></div>



</td></tr>

</table>











<?



//



} else echo $mess['AccessDenied'];





?>
<script language="Javascript" type="text/javascript">
<!--
//switchCell(0);
//-->
</script>