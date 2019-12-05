<h1>Police manufacture</h1>
<script>
function moveorder(id_order) {
	if(id_plant=prompt('Куда перенести?','1')) top.location.href='?act=manufacture&module=orders&a=moveorder&oid='+id_order+'&id_plant='+id_plant;
}
</script>
<?php
$permission=0;
$SQL = "SELECT * FROM build_users WHERE user_id='".AuthUserId."'";
$r = mysql_query($SQL);
if ($d=mysql_fetch_array($r)) $permission=$d['laboratory'];

if(AuthStatus==1 && AuthUserName!="" && (AuthUserClan=='police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy') && $permission) {

$ArrayRes['met']="metals";
$ArrayRes['sil']="silicon";
$ArrayRes['rad']="radioactive metals";
$ArrayRes['gold']="precious metals";
$ArrayRes['organ']="organic";
$ArrayRes['venom']="venom";
$ArrayRes['gem']="gems";
$ArrayRes['poly']="polymers";

$factories = array();
$SQL = "SELECT * FROM buildings WHERE type=1";
$r=mysql_query($SQL);
while ($d=mysql_fetch_array($r)) $factories[$d['id']] = $d['name'];

$shop_items = array();
$shop_items[0] = 'Без привязки';
$SQL = "SELECT id, item_name FROM shop_items WHERE is_visible<2 ORDER BY item_name";
$r=mysql_query($SQL);
while ($d=mysql_fetch_array($r)) $shop_items[$d['id']] = $d['item_name'];

function ShowSection($id) {
        echo "
    <script>
        {$id}.style.display='block';
    </script>
        ";
}
function ShowError($txt,$red=false,$lol=0) {
        $strred = $red ? " style='color:red'" : "";
        $str="<br><h5 $strred>".$txt;
    if($lol==1) $str=$str." <img src='/_imgs/smiles/crazy.gif'></h6>";
    else $str=$str."</h5>";
    echo $str;
}
?>
<div align="center">
<form method="GET">
<input name="act" type="hidden" value="manufacture">
<input type="submit" value="Обновить">
</form>
</div>

<table width='100%' border='0' cellspacing='3' cellpadding='2'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><a href='?act=manufacture&module=add_item'>Добавить чертеж</a></strong> </p></td>
</tr><tr><td>
<?if($_REQUEST['module']=="add_item") {?>
<div id="add_item">
<?
//
// Add item Handler
//
if($_REQUEST['a']=="add_item") {
	if(strlen($_REQUEST['item_name'])<4) ShowError("Ну хоть название потрудись придумать длинее 3х символов, сусель в кепке!",1,1);
	else {
		$SQL="INSERT INTO p_items VALUES('',
		'".htmlspecialchars($_REQUEST['item_name'])."',
		'".($_REQUEST['met']?$_REQUEST['met']:'0')."',
		'".($_REQUEST['sil']?$_REQUEST['sil']:'0')."',
		'".($_REQUEST['rad']?$_REQUEST['rad']:'0')."',
		'".($_REQUEST['gold']?$_REQUEST['gold']:'0')."',
		'".($_REQUEST['organ']?$_REQUEST['organ']:'0')."',
		'".($_REQUEST['venom']?$_REQUEST['venom']:'0')."',
		'".($_REQUEST['gem']?$_REQUEST['gem']:'0')."',
		'".($_REQUEST['poly']?$_REQUEST['poly']:'0')."',
		'".($_REQUEST['rmet']?$_REQUEST['rmet']:'0')."',
		'".($_REQUEST['rsil']?$_REQUEST['rsil']:'0')."',
		'".($_REQUEST['rrad']?$_REQUEST['rrad']:'0')."',
		'".($_REQUEST['rgold']?$_REQUEST['rgold']:'0')."',
		'".($_REQUEST['rorgan']?$_REQUEST['rorgan']:'0')."',
		'".($_REQUEST['rpoly']?$_REQUEST['rvenom']:'0')."',
		'".($_REQUEST['rgem']?$_REQUEST['rgem']:'0')."',
		'".($_REQUEST['rvenom']?$_REQUEST['rpoly']:'0')."',
		'{$_REQUEST['item_count']}',
		'{$_REQUEST['item_count']}',
		'{$_REQUEST['id_plant']}',
		'1',
		'{$_REQUEST['creationt']}',
		'{$_REQUEST['initem']}',
		'0')";
		mysql_query($SQL);
		ShowError("Чертеж добавлен");
		unset($_REQUEST);
	}
}
?>
<form method=GET>
<input name="act" type="hidden" value="manufacture">
<input name="a" type="hidden" value="add_item">
<input name="module" type="hidden" value="add_item">
<table cellpadding=3>
<tr>
<td background='i/bgr-grid-sand.gif'></td>
<td background='i/bgr-grid-sand.gif' align=center><img src="_imgs/tz/polymer.gif" height=21></td>
<td background='i/bgr-grid-sand.gif' align=center><img src="_imgs/tz/organic.gif" height=21></td>
<td background='i/bgr-grid-sand.gif' align=center><img src="_imgs/tz/venom.gif"></td>
<td background='i/bgr-grid-sand.gif' align=center><img src="_imgs/tz/rad.gif"></td>
<td background='i/bgr-grid-sand.gif' align=center><img src="_imgs/tz/gold.gif" height="21"></td>
<td background='i/bgr-grid-sand.gif' align=center><img src="_imgs/tz/gem.gif" height="21"></td>
<td background='i/bgr-grid-sand.gif' align=center><img src="_imgs/tz/metal.gif" height="21"></td>
<td background='i/bgr-grid-sand.gif' align=center><img src="_imgs/tz/silicon.gif" height="21"></td>
</tr>
<tr>
<td background='i/bgr-grid-sand.gif'><b>Стоимость чертежа:</b></td>
<td background='i/bgr-grid-sand.gif'><input name="rpoly" type="text" value="" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="rorgan" type="text" value="" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="rvenom" type="text" value="" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="rrad" type="text" value="" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="rgold" type="text" value="" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="rgem" type="text" value="" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="rmet" type="text" value="" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="rsil" type="text" value="" size=5></td>
</tr>
<tr>
<td background='i/bgr-grid-sand.gif'><b>Стоимость одного комплекта:</b></td>
<td background='i/bgr-grid-sand.gif'><input name="poly" type="text" value="" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="organ" type="text" value="" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="venom" type="text" value="" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="rad" type="text" value="" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="gold" type="text" value="" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="gem" type="text" value="" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="met" type="text" value="" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="sil" type="text" value="" size=5></td>
</tr>
<tr>
	<td background='i/bgr-grid-sand.gif'><b>Название чертежа:</b></td>
	<td colspan=8 background='i/bgr-grid-sand.gif'><input name="item_name" type="text" style="width:100%" value=""></td>
</tr>
<tr>
	<td background='i/bgr-grid-sand.gif'><b>Кол-во вещей в чертеже:</b></td>
	<td colspan=3 background='i/bgr-grid-sand.gif'><input name="item_count" type="text" size=5 value=""></td>
	<td colspan=4 background='i/bgr-grid-sand.gif'><b>Производится за раз (шт):</b></td>
	<td colspan=1 background='i/bgr-grid-sand.gif'><input name="initem" type="text" size=5 value=""></td>
</tr>
<tr>
	<td background='i/bgr-grid-sand.gif'><b>Завод:</b></td>
	<td colspan=3 background='i/bgr-grid-sand.gif'><select name="id_plant">
<? foreach (array_keys($factories) as $fid) { ?>
		<option value="<?=$fid?>"><?=$factories[$fid]?></option>
<? } ?>
	</select></td>
	<td colspan=4 background='i/bgr-grid-sand.gif'><b>Время производства (в <b>мин</b>):</b></td>
	<td colspan=1 background='i/bgr-grid-sand.gif'><input name="creationt" type="text" size=5 value=""></td>
</tr>
<tr>
	<td colspan=9 background='i/bgr-grid-sand1.gif' align=center><input type="submit" value="Добавить чертеж"></td>
</tr>
</table>
</form>
</div>
<?
}
//
//                 Setting prices
//
?>
</td></tr><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><a  href='?act=manufacture&module=res_price'>Барыжные цены на ресы</a></strong> </p></td>
</tr><tr><td>
<?if($_REQUEST['module']=="res_price") {?>
<div id="res_price">
<?
//
//                 Setting Price Handler
//
if($_REQUEST['a']=="SetPrice") {
   // ShowSection("res_price");

    foreach($ArrayRes as $ResId => $RName) {
            $SQL="UPDATE p_res SET res_price='".htmlspecialchars($_REQUEST[$ResId])."' WHERE res_name='$ResId'";
//          echo $SQL."<br>";
            mysql_query($SQL);
    }

    ShowError("Цены записаны");
        unset($_REQUEST);
}
$rr=mysql_query("SELECT * FROM p_res");
while($dt=mysql_fetch_array($rr)) $ResPrice[$dt['res_name']] = $dt['res_price'];
?>
<form method=GET>
<input name="act" type="hidden" value="manufacture">
<input name="a" type="hidden" value="SetPrice">
<input name="module" type="hidden" value="res_price">
<table cellpadding=3>
<tr style="font-weight:bold;text-align:center">
        <td background='i/bgr-grid-sand.gif'>Рес:</td><td background='i/bgr-grid-sand.gif'> Цена:</td>
</tr>
<?foreach($ResPrice as $ResId => $ResP) {?>
<tr align=center>
<td align=right background='i/bgr-grid-sand1.gif'><?=$ArrayRes[$ResId]?>:</td>
<td><input name="<?=$ResId?>" type="text" value="<?=$ResP?>" size=5></td>
</tr>
<?}?>
<tr>
     <td colspan=2 background='i/bgr-grid-sand1.gif' align=center><input type="submit" value="Сохранить"></td>
</tr>
</table>
</form>
</div>
<?}
$rr=mysql_query("SELECT * FROM p_res");
while($dt=mysql_fetch_array($rr)) $ResPrice[$dt['res_name']] = $dt['res_price'];
?>
</td></tr><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><a href='?act=manufacture&module=view'>Просмотр чертежей</a> (<a href='?act=manufacture&module=view&editmode=1'>с редактированием</a>)</strong> </p></td>
</tr><tr><td>
<?if($_REQUEST['module']=="view") {?>
<div id="view" >
<?
//
//                 Viewing designs
//


if($_REQUEST['a']=="updateitem") {
   // ShowSection("view");

    if(@$_REQUEST['id_item'])
    {
        $SQL="UPDATE p_items set ";
        $SQL.="item_name='".htmlspecialchars($_REQUEST["item_name"])."', num='".htmlspecialchars($_REQUEST["item_count"])."', Mnum='".htmlspecialchars($_REQUEST["Mnum"])."', id_plant='".htmlspecialchars($_REQUEST["id_plant"])."', creationt='".htmlspecialchars($_REQUEST["updatecreationt"])."', shop_iid='".htmlspecialchars($_REQUEST["shop_iid"])."' ";
        $SQL.=" WHERE id_item='{$_REQUEST['id_item']}'";
      //  echo $SQL;
         mysql_query($SQL);
		ShowError("Изменено ",0,0);
    }

    unset($_REQUEST);
    $_REQUEST['editmode']=1;
}

if($_REQUEST['a']=="MakeOrder") {
    //ShowSection("view");

    if(@$_REQUEST['confirmdelete']>0)
    {
		$SQL="UPDATE p_items SET is_active=0 WHERE id_item='".$_REQUEST['confirmdelete']."'";
		mysql_query($SQL);
        ShowError("Ну типа все, пиздец чертежу ",1,1);
    }

    if(@$_REQUEST['q'] && @$_REQUEST['id_item'])
    {
        $SQL="UPDATE p_items SET num=num-".$_REQUEST['q'].", id_plant='".$_REQUEST['id_plant']."' WHERE id_item='".$_REQUEST['id_item']."'";
//	    echo $SQL."<br>";
        mysql_query($SQL);
        $SQL="INSERT INTO p_orders values('','".htmlspecialchars($_REQUEST['forwhom'])."','".time()."','','{$_REQUEST['id_item']}','{$_REQUEST['q']}',0,'{$_REQUEST['id_plant']}',0)";
        mysql_query($SQL);
//        echo $SQL."<br>";
		ShowError("Ну а вот теперь вали на завод и делай это ",0,1);
    }


    unset($_REQUEST);
}
?>

<table cellpadding=3 width=98%>
<tr align=center>
<td background='i/bgr-grid-sand1.gif'><b>ID</b></td>
<td background='i/bgr-grid-sand.gif' align=left><b>Чертеж</b></td>
<td background='i/bgr-grid-sand.gif' width=80>&nbsp;</td>
<td background='i/bgr-grid-sand.gif' width=40><img src="_imgs/tz/polymer.gif" height=21></td>
<td background='i/bgr-grid-sand.gif' width=40><img src="_imgs/tz/organic.gif" height=21></td>
<td background='i/bgr-grid-sand.gif' width=40><img src="_imgs/tz/venom.gif"></td>
<td background='i/bgr-grid-sand.gif' width=40><img src="_imgs/tz/rad.gif"></td>
<td background='i/bgr-grid-sand.gif' width=40><img src="_imgs/tz/gold.gif" height="21"></td>
<td background='i/bgr-grid-sand.gif' width=40><img src="_imgs/tz/gem.gif" height="21"></td>
<td background='i/bgr-grid-sand.gif' width=40><img src="_imgs/tz/metal.gif" height="21"></td>
<td background='i/bgr-grid-sand.gif' width=40><img src="_imgs/tz/silicon.gif" height="21"></td>
</tr>

<?
$SQL="SELECT * FROM p_items WHERE is_active=1 AND num>0 ORDER BY item_name";
$r=mysql_query($SQL);

while($d=mysql_fetch_array($r)) {

    $S=0;$strres="";$state=$d['num']/$d['Mnum'];
	if($state<=0.3 && $state>=0.1) $strbg = "background='i/bgr-grid-sand2.gif'";
  	elseif($state<0.1) $strbg = "background='i/bgr-grid-sand3.gif'";
    else $strbg = "background='i/bgr-grid-sand.gif'";


    foreach($ArrayRes as $ResId => $ResName) {
            $S += ($d[$ResId] + ($d["r$ResId"]/$d['Mnum']))*$ResPrice[$ResId]/$d['initem'];
        $strres .= ($d[$ResId]>0) ? "{$ResName}: <b>{$d[$ResId]}</b><br>" : "";
    }

?>

<tr>
<td rowspan=4 background='i/bgr-grid-sand1.gif' align=center><b><?=$d['id_item']?></b></td>
<td colspan=10 <?=$strbg?>>
<b><?=$d['item_name']?></b>
<?if(@$_REQUEST['editmode']) {?><a href="#;return false;" onclick="javascript:if(iitem<?=$d['id_item']?>.style.display=='none') iitem<?=$d['id_item']?>.style.display=''; else iitem<?=$d['id_item']?>.style.display='none';" href='#;return false;'>[E]</a><?}?>
<a href="#;return false;" onclick="if(confirm('Ты уверен, что хочешь похерить этот чертеж?')) top.location.href='?act=manufacture&module=view&confirmdelete=<?=$d['id_item']?>&a=MakeOrder';">[X]</a>
<?if(@$_REQUEST['editmode']) {?>
<div align=left id="iitem<?=$d['id_item']?>" style="display:none">
<form method=GET >
<input name="act" type="hidden" value="manufacture">
<input name="a" type="hidden" value="updateitem">
<input name="module" type="hidden" value="view">
<input name="editmode" type="hidden" value="1">
<input name="id_item" type="hidden" value="<?=$d['id_item']?>">
<b>Название чертежа:</b> <input name="item_name" type="text" style="width:75%" value="<?=stripslashes($d['item_name'])?>">
<br><b>Кол-во вещей в чертеже:</b>
<input name="item_count" type="text" size=5 value="<?=$d['num']?>">/<input name="Mnum" type="text" size=5 value="<?=$d['Mnum']?>">
<br><b>Завод:</b>
<select name="id_plant">
<? foreach (array_keys($factories) as $fid) { ?>
	<option value="<?=$fid?>"<?=($fid==$d['id_plant']?' selected':'')?>><?=$factories[$fid]?></option>
<? } ?>
</select>
<br><b>Время производства (мин):</b>
<input name="updatecreationt" type="text" size=3 value="<?=$d['creationt']?>">
<br><b>ВоенторгЪ:</b>
<select name="shop_iid">
<? foreach (array_keys($shop_items) as $sid) { ?>
	<option value="<?=$sid?>"<?=($sid==$d['shop_iid']?' selected':'')?>><?=$shop_items[$sid]?></option>
<? } ?>
</select>
<br><input type="submit" value="Сохранить">
</form></div>
<?}?>
</td>
</tr>
<tr align=center>
<td colspan=2 <?=$strbg?> align=left>Стоимость одного комплекта:</td>
<td <?=$strbg?>><b><?=$d['poly']?></b></td>
<td <?=$strbg?>><b><?=$d['organ']?></b></td>
<td <?=$strbg?>><b><?=$d['venom']?></b></td>
<td <?=$strbg?>><b><?=$d['rad']?></b></td>
<td <?=$strbg?>><b><?=$d['gold']?></b></td>
<td <?=$strbg?>><b><?=$d['gem']?></b></td>
<td <?=$strbg?>><b><?=$d['met']?></b></td>
<td <?=$strbg?>><b><?=$d['sil']?></b></td>
</tr>
<tr>
	<td colspan=1 <?=$strbg?>>Долговечность: <b><?=$d['num']."/".$d['Mnum']?></b></td>
	<td colspan=3 <?=$strbg?>>Время&nbsp;производства:&nbsp;<b><?=$d['creationt']?>&nbsp;мин.</b></td>
	<td colspan=4 <?=$strbg?>>Производится за раз: <b><?=$d['initem']?> шт.</b></td>
	<td colspan=2 <?=$strbg?>>Цена:&nbsp;<b><?=round($S,2)?></b></td>
</tr>
<tr>
<form method=GET >
<input name="act" type="hidden" value="manufacture">
<input name="a" type="hidden" value="MakeOrder">
<input name="module" type="hidden" value="view">
<input name="id_item" type="hidden" value="<?=$d['id_item']?>">
<input name="coins" type="hidden" value="<?=round($S,2)?>">
<input name="initem" type="hidden" value="<?=$d['initem']?>">
	<td background='i/bgr-grid-sand1.gif'>Завод:&nbsp;<select name="id_plant">
<? foreach (array_keys($factories) as $fid) { ?>
		<option value="<?=$fid?>"<?=($fid==$d['id_plant']?' selected':'')?>><?=$factories[$fid]?></option>
<? } ?>
	</select></td>
	<td colspan=2 background='i/bgr-grid-sand1.gif'>Метка:&nbsp;<input name="forwhom" type="text" value="магазин" size=15></td>
	<td colspan=3 background='i/bgr-grid-sand1.gif'>Кол-во: <input name="q" type="text" value="0" size=5></td>
	<td colspan=2 background='i/bgr-grid-sand1.gif' align=center><input style="width:70" type="button" onclick="var a = Math.abs(this.form.q.value)*Math.abs(this.form.coins.value)*Math.abs(this.form.initem.value);alert('по себестоимости: '+a+' монет')" value="Подсчитать"></td>
	<td colspan=2 background='i/bgr-grid-sand1.gif' align=center><input style="width:70" type="submit" value="Заказать" onfocus="this.blur()"></td>
</form>
</tr>

<?
//        $bg = ($bg==1) ? 2 : 1;
}
?>

</table>
</div>
<?}?>


</td></tr><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><a href='?act=manufacture&module=empty'>Наполнение чертежей</a></strong> </p></td>
</tr><tr><td>
<?if($_REQUEST['module']=="empty") {?>
<div id="empty" >
<?
//
//                 Updating designs
//
if($_REQUEST['a']=="setitem") {
    //ShowSection("empty");

    if(@$_REQUEST['confirmdelete']>0)
    {
		$SQL="UPDATE p_items SET is_active=0 WHERE id_item='".$_REQUEST['confirmdelete']."'";
		mysql_query($SQL);
        ShowError("Ну типа все, пиздец чертежу ",1,1);
    }

    if(@$_REQUEST['id_item'])
    {
		$SQL="UPDATE p_items SET
		item_name='".htmlspecialchars($_REQUEST['item_name'])."',
		met='".($_REQUEST['met']?$_REQUEST['met']:'0')."',
		sil='".($_REQUEST['sil']?$_REQUEST['sil']:'0')."',
		rad='".($_REQUEST['rad']?$_REQUEST['rad']:'0')."',
		gold='".($_REQUEST['gold']?$_REQUEST['gold']:'0')."',
		organ='".($_REQUEST['organ']?$_REQUEST['organ']:'0')."',
		venom='".($_REQUEST['venom']?$_REQUEST['venom']:'0')."',
		gem='".($_REQUEST['gem']?$_REQUEST['gem']:'0')."',
		poly='".($_REQUEST['poly']?$_REQUEST['poly']:'0')."',
		rmet='".($_REQUEST['rmet']?$_REQUEST['rmet']:'0')."',
		rsil='".($_REQUEST['rsil']?$_REQUEST['rsil']:'0')."',
		rrad='".($_REQUEST['rrad']?$_REQUEST['rrad']:'0')."',
		rgold='".($_REQUEST['rgold']?$_REQUEST['rgold']:'0')."',
		rorgan='".($_REQUEST['rorgan']?$_REQUEST['rorgan']:'0')."',
		rpoly='".($_REQUEST['rpoly']?$_REQUEST['rpoly']:'0')."',
		rgem='".($_REQUEST['rgem']?$_REQUEST['rgem']:'0')."',
		rvenom='".($_REQUEST['rvenom']?$_REQUEST['rvenom']:'0')."',
		num='{$_REQUEST['item_count']}',
		Mnum='{$_REQUEST['Mnum']}',
		id_plant='{$_REQUEST['id_plant']}',
		creationt='{$_REQUEST['creationt']}',
		initem='{$_REQUEST['initem']}'
		WHERE id_item='{$_REQUEST['id_item']}'";
		mysql_query($SQL);
		ShowError("Изменено ",0,0);
    }


    unset($_REQUEST);
}
?>

<table cellpadding=3 width=98%>
<tr style="font-weight:bold;text-align:center">
    <td background='i/bgr-grid-sand.gif'> ID:</td>
    <td background='i/bgr-grid-sand.gif'> Название:</td>
    <td background='i/bgr-grid-sand.gif'> Действия:</td>
</tr>

<?
$SQL="SELECT * FROM p_items WHERE is_active=1 AND num<1 ORDER BY item_name";
$r=mysql_query($SQL);
$bg=1;
while($d=mysql_fetch_array($r)) {

        $S=0;$strres="";
    $strbg = ($bg==1) ? "background='i/bgr-grid-sand.gif'" : "background='i/bgr-grid-sand1.gif'";
    foreach($ArrayRes as $ResId => $ResName) {
            $S += ($d[$ResId] + ($d["r$ResId"]/$d['Mnum']))*$ResPrice[$ResId];
        $strres .= ($d[$ResId]>0) ? "{$ResName}: <b>{$d[$ResId]}</b><br>" : "";
    }

?>
<tr align=center>
<form method=GET >
<input name="act" type="hidden" value="manufacture">
<input name="a" type="hidden" value="setitem">
<input name="module" type="hidden" value="empty">
<input name="id_item" type="hidden" value="<?=$d['id_item']?>">


        <td background='i/bgr-grid-sand1.gif' valign=top><b><?=$d['id_item']?></b></td>
    <td <?=$strbg?> valign=top nowrap><b><?=$d['item_name']?></b> </td>
    <td <?=$strbg?> >
    <a href="#;return false;" onclick="javascript:if(item<?=$d['id_item']?>.style.display=='none') item<?=$d['id_item']?>.style.display=''; else item<?=$d['id_item']?>.style.display='none';" href='#;return false;'>изменить</a> /
    <a href="#;return false;" onclick="if(confirm('Ты уверен, что хочешь похерить этот чертеж?')) top.location.href='?act=manufacture&confirmdelete=<?=$d['id_item']?>&a=setitem';">удалить</a>
    </td>


</tr>

<tr><td colspan=3>

<div id="item<?=$d['id_item']?>" style="display:none">

<table cellpadding=3>
<tr>
<td background='i/bgr-grid-sand.gif'></td>
<td background='i/bgr-grid-sand.gif' align=center><img src="_imgs/tz/polymer.gif" height=21></td>
<td background='i/bgr-grid-sand.gif' align=center><img src="_imgs/tz/organic.gif" height=21></td>
<td background='i/bgr-grid-sand.gif' align=center><img src="_imgs/tz/venom.gif"></td>
<td background='i/bgr-grid-sand.gif' align=center><img src="_imgs/tz/rad.gif"></td>
<td background='i/bgr-grid-sand.gif' align=center><img src="_imgs/tz/gold.gif" height="21"></td>
<td background='i/bgr-grid-sand.gif' align=center><img src="_imgs/tz/gem.gif" height="21"></td>
<td background='i/bgr-grid-sand.gif' align=center><img src="_imgs/tz/metal.gif" height="21"></td>
<td background='i/bgr-grid-sand.gif' align=center><img src="_imgs/tz/silicon.gif" height="21"></td>
</tr>
<tr>
<td background='i/bgr-grid-sand.gif'><b>Стоимость чертежа:</b></td>
<td background='i/bgr-grid-sand.gif'><input name="rpoly" type="text" value="<?=$d['rpoly']?>" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="rorgan" type="text" value="<?=$d['rorgan']?>" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="rvenom" type="text" value="<?=$d['rvenom']?>" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="rrad" type="text" value="<?=$d['rrad']?>" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="rgold" type="text" value="<?=$d['rgold']?>" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="rgem" type="text" value="<?=$d['rgem']?>" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="rmet" type="text" value="<?=$d['rmet']?>" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="rsil" type="text" value="<?=$d['rsil']?>" size=5></td>
</tr>
<tr>
<td background='i/bgr-grid-sand.gif'><b>Стоимость одного комплекта:</b></td>
<td background='i/bgr-grid-sand.gif'><input name="poly" type="text" value="<?=$d['poly']?>" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="organ" type="text" value="<?=$d['organ']?>" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="venom" type="text" value="<?=$d['venom']?>" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="rad" type="text" value="<?=$d['rad']?>" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="gold" type="text" value="<?=$d['gold']?>" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="gem" type="text" value="<?=$d['gem']?>" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="met" type="text" value="<?=$d['met']?>" size=5></td>
<td background='i/bgr-grid-sand.gif'><input name="sil" type="text" value="<?=$d['sil']?>" size=5></td>
</tr>
<tr>
	<td background='i/bgr-grid-sand.gif'><b>Название чертежа:</b></td>
	<td colspan=8 background='i/bgr-grid-sand.gif'><input name="item_name" type="text" style="width:100%" value="<?=$d['item_name']?>"></td>
</tr>
<tr>
	<td background='i/bgr-grid-sand.gif'><b>Кол-во вещей в чертеже:</b></td>
	<td colspan=3 background='i/bgr-grid-sand.gif'><input name="item_count" type="text" size=5 value="<?=$d['num']?>">/<input name="Mnum" type="text" size=5 value="<?=$d['Mnum']?>"></td>
	<td colspan=4 background='i/bgr-grid-sand.gif'><b>Производится за раз (шт):</b></td>
	<td colspan=1 background='i/bgr-grid-sand.gif'><input name="initem" type="text" size=5 value="<?=$d['initem']?>"></td>
</tr>
<tr>
	<td background='i/bgr-grid-sand.gif'><b>Завод:</b></td>
	<td colspan=3 background='i/bgr-grid-sand.gif'><select name="id_plant">
<? foreach (array_keys($factories) as $fid) { ?>
		<option value="<?=$fid?>"><?=$factories[$fid]?></option>
<? } ?>
	</select></td>
	<td colspan=4 background='i/bgr-grid-sand.gif'><b>Время производства (в <b>мин</b>):</b></td>
	<td colspan=1 background='i/bgr-grid-sand.gif'><input name="creationt" type="text" size=5 value="<?=$d['creationt']?>"></td>
</tr>
<tr>
	<td colspan=9 background='i/bgr-grid-sand1.gif' align=center><input type="submit" value="Сохранить"></td>
</tr>
</table>

</td></form></tr>
<?
        $bg = ($bg==1) ? 2 : 1;
}
?>

</table>
</div>
<?}?>
</td></tr><tr>

<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><a href='?act=manufacture&module=orders'>Просмотр заказов</a></strong> </p></td>
</tr><tr><td>
<?if($_REQUEST['module']=="orders") {?>
<div id="orders">
<?
//
//                 Viewing orders
//
if($_REQUEST['a']=="execute" || $_REQUEST['a']=="moveorder") {

//    ShowSection("orders");
	$error_str = "Типа сохранилось";
	if(@$_REQUEST['oid']) {
		if(@$_REQUEST['delete']) {
			$SQL="DELETE FROM p_orders WHERE id='".$_REQUEST['oid']."'";
			mysql_query($SQL);
			//echo "$SQL <br>";
			$SQL="UPDATE p_items SET num=num+".$_REQUEST['q']." WHERE id_item='".$_REQUEST['iid']."'";
		} elseif(@$_REQUEST['finish']) {
			$s=mysql_fetch_array(mysql_query("SELECT shop_iid FROM p_items WHERE id_item='".$_REQUEST['i']."'"));
			$SQL="SELECT order_id FROM shop_temp WHERE item_id='".$s['shop_iid']."' AND factory='".$_REQUEST['id_plant']."' AND status=3";
			$r=mysql_query($SQL);
			$SQL="UPDATE shop_temp SET status=0 WHERE item_id='".$s['shop_iid']."' AND factory='".$_REQUEST['id_plant']."' AND status=3";
			mysql_query($SQL);
			while ($d=mysql_fetch_array($r)) {
				$s=mysql_fetch_array(mysql_query("SELECT SUM(status) as status FROM shop_temp WHERE order_id='".$d['order_id']."'"));
				if ($s['status']==0) {
					$SQL="UPDATE shop_orders SET status='3', t2='".time()."', execute='".AuthUserName."', stored_in='склад завода' WHERE id='".$d['order_id']."'";
					mysql_query($SQL);
					echo "<h6>Заказ ID: ".$d['order_id']." готов</h6>";
				}
			}
			$SQL="UPDATE p_orders SET status=1 WHERE id='".$_REQUEST['oid']."'";
		} elseif(@$_REQUEST['begin']) {
			if (mysql_num_rows(mysql_query("SELECT * FROM p_orders WHERE id_plant='".$_REQUEST['id_plant']."' and status=2"))==0) {
				$d=mysql_fetch_array(mysql_query("SELECT i.*, o.q FROM p_items i INNER JOIN p_orders o USING (id_item) WHERE o.id='".$_REQUEST['oid']."'"));
				mysql_query("INSERT INTO warehouses VALUES('','".time()."','1','".AuthUserName."','0',
				'-".$d['met']*$d['q']."', '-".$d['gold']*$d['q']."', '-".$d['poly']*$d['q']."', '-".$d['organ']*$d['q']."', '-".$d['venom']*$d['q']."', '-".$d['rad']*$d['q']."', '-".$d['gem']*$d['q']."', '-".$d['sil']*$d['q']."',
				'".$_REQUEST['id_plant']."', '0')");
				$SQL="UPDATE p_orders SET status=2, begin_time=".time().", q_ready=0 WHERE id='".$_REQUEST['oid']."'";
			} else $error_str = "Завод уже работает";
		} elseif(@$_REQUEST['id_plant']) {
			$SQL="UPDATE p_items SET id_plant='".$_REQUEST['id_plant']."' WHERE id_item='".$_REQUEST['oid']."'";
		}
		//act=manufacture&module=orders&a=moveorder&oid='+id_order+'&id_plant='+id_plant;
		//echo $SQL."<br>";
		mysql_query($SQL);
    }

    ShowError($error_str);
    unset($_REQUEST);
}


?>

<table cellpadding=3 width=98%>
<tr style="font-weight:bold;text-align:center">
    <td background='i/bgr-grid-sand.gif'> ID:</td>
    <td background='i/bgr-grid-sand.gif'> Название:</td>
    <td background='i/bgr-grid-sand.gif'> Метка:</td>
    <td background='i/bgr-grid-sand.gif' nowrap> Время:</td>
    <td background='i/bgr-grid-sand.gif'> Кол-во:</td>
    <td background='i/bgr-grid-sand.gif'> Монет:</td>
    <td background='i/bgr-grid-sand.gif'> В ресах:</td>
    <td background='i/bgr-grid-sand.gif'> Заказ:</td>
</tr>

<?
$SQL="SELECT o.*, i.*, o.id_plant as id_plant FROM p_orders o INNER JOIN p_items i USING (id_item) WHERE o.status<>1 ORDER BY o.id_plant,o.order_time";
$r=mysql_query($SQL);
$bg=1;
$Pid=0;
while($d=mysql_fetch_array($r)) {

    $S=0;$strres="";
    $strbg = ($bg==1) ? "background='i/bgr-grid-sand.gif'" : "background='i/bgr-grid-sand1.gif'";
    foreach($ArrayRes as $ResId => $ResName) {
       $S += ($d[$ResId] + ($d["r$ResId"]/$d['Mnum']))*$ResPrice[$ResId];
       $strres .= ($d[$ResId]>0) ? "{$ResName}: <b>".$d[$ResId]*$d['q']."</b><br>" : "";
    }

    $TimeStr="";$StatusStr="";
    switch($d['status']) {
    	case 0:
        	$TimeStr="подача заказа: <br><b>".date("d.m.y H:i",$d['order_time'])."</b><br>время производства:<br><b>".$d['creationt']*$d['q']." мин</b>";
            $StatusStr="<a href='?act=manufacture&a=execute&begin=1&oid={$d['id']}&id_plant={$d['id_plant']}&module=orders'>запустить</a><br>";
            break;
        case 2:
        	$productionT=$d['creationt']*60*$d['q'];
            $prodStr = (($d['begin_time']+$productionT)>time()) ? "<div align=center><b>Completed: <font color=red>".floor(((time()-$d['begin_time'])/$productionT)*100)."</font>%</b></div>"  : "";
        	$TimeStr="производство начато:<br><b>".date("d.m.y H:i",$d['order_time'])."</b><br>будет готово:<br><b>".date("d.m.y H:i",($d['begin_time']+$productionT))."</b>";
            $StatusStr="<a href='?act=manufacture&a=execute&finish=1&oid={$d['id']}&id_plant={$d['id_plant']}&i={$d['id_item']}&module=orders'>готово</a><br>";
            break;

    }

    if($Pid!=$d['id_plant']) {
       $Pid=$d['id_plant'];
?>
<tr align=center>
<td colspan=8 background='i/bgr-grid-sand1.gif' valign=top><b><?=$factories[$d['id_plant']]?></b></td>
</tr>
<?
    }
$cur_time = time();
if ($d['begin_time']+$productionT>time()) {
	$q_ready = floor(((time()-$d['begin_time'])/$productionT)*$d['q']);
} else {
	$q_ready = $d['q'];
}
if (($q_ready>$d['q_ready']) && ($d['status']==2)) {
	$rr=mysql_query("SELECT * FROM warehouses_items WHERE item_id='".$d['id_item']."' AND warehouse='".$d['id_plant']."' AND label='".$d['forwhom']."'");
	if ($s=mysql_fetch_array($rr)) {
		$SQL="UPDATE warehouses_items SET quantity='".($s['quantity']+$q_ready-$d['q_ready'])*$d['initem']."' WHERE item_id='".$d['id_item']."' AND warehouse='".$d['id_plant']."' AND label='".$d['forwhom']."'";
	} else {
		$SQL="INSERT INTO warehouses_items VALUES('','".$d['id_plant']."','".$d['id_item']."','".$d['forwhom']."','".($q_ready-$d['q_ready'])*$d['initem']."')";
	}
	mysql_query($SQL);
	mysql_query("UPDATE p_orders SET q_ready='".$q_ready."' WHERE id='".$d['id']."'");
}

?>
<tr align=center>

    <td background='i/bgr-grid-sand1.gif' valign=top><b><?=$d['id']?></b></td>
    <td <?=$strbg?> valign=top nowrap><b><?=$d['item_name']?></b></td>
    <td <?=$strbg?> ><?=$d['forwhom']?></td>
    <td <?=$strbg?> align=left nowrap valign=top> <?=$TimeStr?></td>
    <td <?=$strbg?> > <b><?=$d['q']?></b></td>
    <td <?=$strbg?> > <?=round($S*$d['q'],2)?></td>
    <td <?=$strbg?> valign=top nowrap align=left> <?=$strres?></td>
    <td <?=$strbg?> >
		<? if ($d['status']==0) { ?>
		<?=$StatusStr?>
		<a href='JavaScript:moveorder(<?=$d['id_item']?>)'>перенести</a><br>
		<a href='?act=manufacture&module=orders&a=execute&oid=<?=$d['id']?>&delete=1&q=<?=$d['q']?>&iid=<?=$d['id_item']?>'>удалить</a>
		<? } elseif ($q_ready == $d['q']) { ?>
		<?=$StatusStr?>
		<? } else { ?>
		<?=$prodStr?>
		<? } ?>
    </td>

</tr>
<?
//     $bg = ($bg==1) ? 2 : 1;
}
?>

</table>
<?}?>
</div>
</td></tr>
</table>


</td></tr>
</table>

<?
} else echo $mess['AccessDenied'];
?>