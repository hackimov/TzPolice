<h1>Police Industry Centre</h1>
<script>
function moveorder(id_order) {
	if(id_plant=prompt('Куда перенести?','1')) top.location.href='?act=plants&module=orders&a=moveorder&oid='+id_order+'&id_plant='+id_plant;
}
</script>
<?php

// -38/-61 ... -39/-63 ... 61/37

# ASlab admin


if(AuthUserGroup == 100) {



$ArrayRes['met']="metals";
$ArrayRes['sil']="silicon";
$ArrayRes['rad']="radioactive metals";
$ArrayRes['gold']="precious metals";
$ArrayRes['organ']="organic";
$ArrayRes['venom']="venom";
$ArrayRes['gem']="gems";
$ArrayRes['poly']="polymers";

$ArrayPlants[1]="[61/37]";
$ArrayPlants[2]="[-38/-61]";
$ArrayPlants[3]="[-39/-63]";

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
<input name="act" type="hidden" value="plants">
<input type="submit" value="Обновить">
</form>
</div>

<table width='100%' border='0' cellspacing='3' cellpadding='2'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><a href='?act=plants&module=add_item'>Добавить чертеж</a></strong> </p></td>
</tr><tr><td>
<?if($_REQUEST['module']=="add_item") {?>
<div id="add_item">
<?
//
// Add item Handler
//
if($_REQUEST['a']=="add_item") {
    //ShowSection("add_item");


   if(strlen($_REQUEST['id_plant'])>1 || $_REQUEST['id_plant']>10) ShowError("Нужно указать порядквый номер завода",1);
    else {
                $SQL="INSERT INTO p_items (";
        foreach($ArrayRes as $ResId => $RName) $SQL.=$ResId.", r".$ResId.", ";
        $SQL.="item_name, num, Mnum, id_plant, creationt, initem) values (";
        foreach($ArrayRes as $ResId => $ResName) $SQL.="'".htmlspecialchars($_REQUEST["$ResId"])."', '".htmlspecialchars($_REQUEST["r$ResId"])."', ";
        $SQL.="'".htmlspecialchars($_REQUEST['item_name'])."', '{$_REQUEST['item_count']}', '{$_REQUEST['item_count']}', {$_REQUEST['id_plant']}, {$_REQUEST['creationt']}, {$_REQUEST['initem']})";
        //echo $SQL;
        mysql_query($SQL);
        ShowError("Добавилось в базу");
        unset($_REQUEST);
    }
}
?>
<form method=GET>
<input name="act" type="hidden" value="plants">
<input name="a" type="hidden" value="add_item">
<input name="module" type="hidden" value="add_item">
<table cellpadding=3>
<tr style="font-weight:bold;text-align:center">
        <td background='i/bgr-grid-sand.gif' rowspan=2>ресы:</td><td colspan=2 background='i/bgr-grid-sand.gif'>Ресурсы на производство</td>
</tr>
<tr style="font-weight:bold;text-align:center">
        <td background='i/bgr-grid-sand.gif'>чертежа:</td><td background='i/bgr-grid-sand.gif'> 1 вещи:</td>
</tr>
<?foreach($ArrayRes as $ResId => $ResName) {
$val[0] = ($_REQUEST["r$ResId"] && $_REQUEST['a']=="add_item") ? $_REQUEST["r$ResId"] : 0;
$val[1] = ($_REQUEST["$ResId"] && $_REQUEST['a']=="add_item") ? $_REQUEST["$ResId"] : 0;
$val[2] = (strlen($_REQUEST["item_name"])>0) ? $_REQUEST["item_name"] : "";
$val[3] = (strlen($_REQUEST["item_count"])>0) ? $_REQUEST["item_count"] : 100;
$val[4] = (strlen($_REQUEST["id_plant"])>0) ? $_REQUEST["id_plant"] : 1;
$val[5] = (strlen($_REQUEST["creationt"])>0) ? $_REQUEST["creationt"] : 25;
$val[6] = (strlen($_REQUEST["inintem"])>0) ? $_REQUEST["initem"] : 1;
?>
<tr align=center>
<td align=right background='i/bgr-grid-sand1.gif'><?=$ResName?>:</td>
<td><input name="r<?=$ResId?>" type="text" value="<?=$val[0]?>" size=5></td>
<td><input name="<?=$ResId?>" type="text" value="<?=$val[1]?>" size=5></td>
</tr>
<?}?>
<tr>
        <td colspan=3 background='i/bgr-grid-sand.gif'><b>Название чертежа:</b><br>
        <input name="item_name" type="text" style="width:100%" value="<?=stripslashes(htmlspecialchars($val[2]))?>">
        </td>
</tr>
<tr>
        <td colspan=3 background='i/bgr-grid-sand.gif'><b>Кол-во хреновин в чертеже:</b><br>
        <input name="item_count" type="text" size=3 value="<?=$val[3]?>">
        </td>
</tr>
<tr>
        <td colspan=3 background='i/bgr-grid-sand.gif'><b>№п/п завода:</b><br>

        <select size="1" name="id_plant">
        <?foreach($ArrayPlants as $id=>$name) echo "<option value='$id'>$id $name</option>";?>
		</select>

        </td>
</tr>
<tr>
        <td colspan=3 background='i/bgr-grid-sand.gif'><b>Время производства (в <b>мин</b>):</b><br>
        <input name="creationt" type="text" size=3 value="<?=$val[5]?>">
        </td>
</tr>
<tr>
        <td colspan=3 background='i/bgr-grid-sand.gif'><b>Производится за раз:</b><br>
        <input name="initem" type="text" size=3 value="<?=$val[6]?>">
        </td>
</tr>
<tr>
     <td colspan=3 background='i/bgr-grid-sand1.gif' align=center><input type="submit" value="Добавить чертеж"></td>
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
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><a  href='?act=plants&module=res_price'>Барыжные цены на ресы</a></strong> </p></td>
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

    ShowError("Типа сохранилось");
        unset($_REQUEST);
}
$rr=mysql_query("SELECT * FROM p_res");
while($dt=mysql_fetch_array($rr)) $ResPrice[$dt['res_name']] = $dt['res_price'];
?>
<form method=GET>
<input name="act" type="hidden" value="plants">
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
     <td colspan=2 background='i/bgr-grid-sand1.gif' align=center><input type="submit" value="Саве"></td>
</tr>
</table>
</form>
</div>
<?}
$rr=mysql_query("SELECT * FROM p_res");
while($dt=mysql_fetch_array($rr)) $ResPrice[$dt['res_name']] = $dt['res_price'];
?>
</td></tr><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><a href='?act=plants&module=view'>Просмотр чертежей</a> (<a href='?act=plants&module=view&editmode=1'>с редактированием</a>)</strong> </p></td>
</tr><tr><td>
<?if($_REQUEST['module']=="view") {?>
<div id="view" >
<?
//
//                 Viewing designs
//

if($_REQUEST['editmode']) $editmodebool=1;
if($_REQUEST['a']=="updateitem") {
   // ShowSection("view");

    if(@$_REQUEST['id_item'])
    {
        $SQL="UPDATE p_items set ";
        $SQL.="item_name='".htmlspecialchars($_REQUEST["item_name"])."', num='".htmlspecialchars($_REQUEST["item_count"])."', Mnum='".htmlspecialchars($_REQUEST["Mnum"])."', id_plant='".htmlspecialchars($_REQUEST["id_plant"])."', creationt='".htmlspecialchars($_REQUEST["updatecreationt"])."', initem='".htmlspecialchars($_REQUEST["updateinitem"])."' ";
        $SQL.=" WHERE id_item='{$_REQUEST['id_item']}'";
      //  echo $SQL;
         mysql_query($SQL);
		ShowError("Изменено ",0,0);
    }

    unset($_REQUEST);
}

if($_REQUEST['a']=="MakeOrder") {
    //ShowSection("view");

    if(@$_REQUEST['confirmdelete']>0)
    {
		$SQL="UPDATE p_items SET is_active=0 WHERE id_item='".$_REQUEST['confirmdelete']."'";
		mysql_query($SQL);
        ShowError("Хана чертежу ",1);
    }

    if(@$_REQUEST['q'] && @$_REQUEST['id_item'])
    {
        $SQL="UPDATE p_items SET num=num-".$_REQUEST['q'].", id_plant='".$_REQUEST['id_plant']."' WHERE id_item='".$_REQUEST['id_item']."'";
//	    echo $SQL."<br>";
        mysql_query($SQL);
        $SQL="INSERT INTO p_orders values('','".htmlspecialchars($_REQUEST['forwhom'])."','".time()."','','{$_REQUEST['id_item']}','{$_REQUEST['q']}',0)";
        mysql_query($SQL);
//        echo $SQL."<br>";
		ShowError("Заказ поставлен в очередь на производство ",0,0);
    }


    unset($_REQUEST);
}
?>

<table cellpadding=3 width=98%>
<tr style="font-weight:bold;text-align:center">
	<td background='i/bgr-grid-sand.gif'> ID:</td>
    <td background='i/bgr-grid-sand.gif'> Название:</td>
	<td background='i/bgr-grid-sand.gif'> Для кого:</td>
    <td background='i/bgr-grid-sand.gif'> Стоимость в ресах:</td>
    <td background='i/bgr-grid-sand.gif'> Монет:</td>
    <td background='i/bgr-grid-sand.gif'> № Зав</td>
    <td background='i/bgr-grid-sand.gif'> Кол-во:</td>
    <td background='i/bgr-grid-sand.gif'> Заказ:</td>
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
            $S += ($d[$ResId] + ($d["r$ResId"]/$d['Mnum']))*$ResPrice[$ResId];
        $strres .= ($d[$ResId]>0) ? "{$ResName}: <b>{$d[$ResId]}</b><br>" : "";
    }
    $S=$S/$d['initem'];
    $strres="<u>На производство <b>{$d['initem']}шт:</b></u><br>".$strres

?>
<tr align=center>

	<td background='i/bgr-grid-sand1.gif' valign=top><b><?=$d['id_item']?></b></td>
    <td <?=$strbg?> valign=top nowrap><b><?=$d['item_name']?></b>
    <?if(@$_REQUEST['editmode']) {?>
    <a href="#;return false;" onclick="javascript:if(iitem<?=$d['id_item']?>.style.display=='none') iitem<?=$d['id_item']?>.style.display=''; else iitem<?=$d['id_item']?>.style.display='none';" href='#;return false;'>[E]</a>
    <?}?>
    <a href="#;return false;" onclick="if(confirm('Действительно удалить этот чертеж?')) top.location.href='?act=plants&module=view&confirmdelete=<?=$d['id_item']?>&a=MakeOrder';">[X]</a>
    <br>
    <br>Долговечность: <b><?=$d['num']."/".$d['Mnum']?></b>
    <br>Время производства: <b><?=$d['creationt']?> мин.</b>
<?if(@$editmodebool) {?>
<div align=left id="iitem<?=$d['id_item']?>" style="display:none">
<form method=GET >
<input name="act" type="hidden" value="plants">
<input name="a" type="hidden" value="updateitem">
<input name="module" type="hidden" value="view">
<input name="editmode" type="hidden" value="1">
<input name="id_item" type="hidden" value="<?=$d['id_item']?>">
<b>Название чертежа:</b><br>
        <input name="item_name" type="text" style="width:90%" value="<?=stripslashes($d['item_name'])?>">
<br><b>Кол-во хреновин в чертеже:</b><br>
        <input name="item_count" type="text" size=3 value="<?=$d['num']?>">/<input name="Mnum" type="text" size=3 value="<?=$d['Mnum']?>">
<br><b>№п/п завода:</b><br>
        <input name="id_plant" type="text" size=3 value="<?=$d['id_plant']?>">
<br><b>Время производства (мин):</b><br>
        <input name="updatecreationt" type="text" size=3 value="<?=$d['creationt']?>">
<br><b>Производится за раз:</b><br>
        <input name="updateinitem" type="text" size=3 value="<?=$d['initem']?>">
<br><input type="submit" value="Сохранить">
</form></div>
<?}?>


    </td>
<form method=GET >
<input name="act" type="hidden" value="plants">
<input name="a" type="hidden" value="MakeOrder">
<input name="module" type="hidden" value="view">
<input name="id_item" type="hidden" value="<?=$d['id_item']?>">
<input name="coins" type="hidden" value="<?=round($S,2)?>">
    <td <?=$strbg?> align=center> <input name="forwhom" type="text" value="кому" onfocus="if(this.value=='кому') this.value=''" size=10></td>
    <td <?=$strbg?> valign=top nowrap align=left> <?=$strres?></td>
    <td <?=$strbg?> > <u>За <b>1шт:</b></u><br><?=round($S,2)?></td>
    <td <?=$strbg?> > <input name="id_plant" type="text" value="<?=$d['id_plant']?>" size=1></td>
    <td <?=$strbg?> > <input name="q" type="text" value="0" size=3></td>
    <td <?=$strbg?> > <input style="width:70" type="button" onclick="var a = Math.abs(this.form.q.value)*Math.abs(this.form.coins.value);alert('по себестоимости: '+a+' монет')" value="Подсчитать"><br><input style="width:70" type="submit" value="Заказать" onfocus="this.blur()"></td>

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
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><a href='?act=plants&module=empty'>Наполнение чертежей</a></strong> </p></td>
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
        ShowError("Удалено ",1,0);
    }

    if(@$_REQUEST['id_item'])
    {
        $SQL="UPDATE p_items set ";
        foreach($ArrayRes as $ResId => $RName) $SQL.=$ResId."='".htmlspecialchars($_REQUEST["set$ResId"])."', r".$ResId."='".htmlspecialchars($_REQUEST["setr$ResId"])."', ";
        $SQL.="item_name='".htmlspecialchars($_REQUEST["item_name"])."', num='".htmlspecialchars($_REQUEST["item_count"])."', Mnum='".htmlspecialchars($_REQUEST["Mnum"])."', id_plant='".htmlspecialchars($_REQUEST["id_plant"])."', creationt='".htmlspecialchars($_REQUEST["setcreationt"])."' ";
        $SQL.=" WHERE id_item='{$_REQUEST['id_item']}'";
      //  echo $SQL;
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
<input name="act" type="hidden" value="plants">
<input name="a" type="hidden" value="setitem">
<input name="module" type="hidden" value="empty">
<input name="id_item" type="hidden" value="<?=$d['id_item']?>">


        <td background='i/bgr-grid-sand1.gif' valign=top><b><?=$d['id_item']?></b></td>
    <td <?=$strbg?> valign=top nowrap><b><?=$d['item_name']?></b> </td>
    <td <?=$strbg?> >
    <a href="#;return false;" onclick="javascript:if(item<?=$d['id_item']?>.style.display=='none') item<?=$d['id_item']?>.style.display=''; else item<?=$d['id_item']?>.style.display='none';" href='#;return false;'>изменить</a> /
    <a href="#;return false;" onclick="if(confirm('Удалить этот чертеж?')) top.location.href='?act=plants&confirmdelete=<?=$d['id_item']?>&a=setitem';">удалить</a>
    </td>


</tr>

<tr><td colspan=3>

<div id="item<?=$d['id_item']?>" style="display:none">
<table cellpadding=3>
<tr style="font-weight:bold;text-align:center">
        <td background='i/bgr-grid-sand.gif' rowspan=2>ресы:</td><td colspan=2 background='i/bgr-grid-sand.gif'>Ресурсы на производство</td>
</tr>
<tr style="font-weight:bold;text-align:center">
        <td background='i/bgr-grid-sand.gif'>чертежа:</td><td background='i/bgr-grid-sand.gif'> 1 хрени:</td>
</tr>
<?foreach($ArrayRes as $ResId => $ResName) {
$val[0] = $d["r$ResId"];
$val[1] = $d[$ResId];
$val[2] = $d["item_name"];
$val[3] = $d["num"];
$val[4] = $d["id_plant"];
$val[5] = $d["creationt"];
$val[6] = $d["Mnum"];
?>
<tr align=center>
<td align=right background='i/bgr-grid-sand1.gif'><?=$ResName?>:</td>
<td><input name="setr<?=$ResId?>" type="text" value="<?=$val[0]?>" size=5></td>
<td><input name="set<?=$ResId?>" type="text" value="<?=$val[1]?>" size=5></td>
</tr>
<?}?>
<tr>
        <td colspan=3 background='i/bgr-grid-sand.gif'><b>Название чертежа:</b><br>
        <input name="item_name" type="text" style="width:100%" value="<?=stripslashes($val[2])?>">
        </td>
</tr>
<tr>
        <td colspan=3 background='i/bgr-grid-sand.gif'><b>Кол-во хреновин в чертеже:</b><br>
        <input name="item_count" type="text" size=3 value="<?=$val[3]?>">/<input name="Mnum" type="text" size=3 value="<?=$val[6]?>">
        </td>
</tr>
<tr>
        <td colspan=3 background='i/bgr-grid-sand.gif'><b>№п/п завода:</b><br>
        <input name="id_plant" type="text" size=3 value="<?=$val[4]?>">
        </td>
</tr>
<tr>
        <td colspan=3 background='i/bgr-grid-sand.gif'><b>время производства (мин):</b><br>
        <input name="setcreationt" type="text" size=3 value="<?=$val[5]?>">
        </td>
</tr>
<tr>
     <td colspan=3 background='i/bgr-grid-sand1.gif' align=center><input type="submit" value="Сохранить"></td>
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

<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong><a href='?act=plants&module=orders'>Просмотр заказов</a></strong> </p></td>
</tr><tr><td>
<?if($_REQUEST['module']=="orders") {?>
<div id="orders">
<?
//
//                 Viewing orders
//
if($_REQUEST['a']=="execute" || $_REQUEST['a']=="moveorder") {

//    ShowSection("orders");
    if(@$_REQUEST['oid']) {
    	if(@$_REQUEST['delete'])
        {
	    	$SQL="DELETE FROM p_orders WHERE id='".$_REQUEST['oid']."'";
            mysql_query($SQL);
           	//echo "$SQL <br>";
            $SQL="UPDATE p_items SET num=num+".$_REQUEST['q']." WHERE id_item='".$_REQUEST['iid']."'";

        } elseif(@$_REQUEST['finish']) {
	    	$SQL="UPDATE p_orders SET status=1 WHERE id='".$_REQUEST['oid']."'";
        } elseif(@$_REQUEST['begin']) {
	        $SQL="UPDATE p_orders SET status=2, begin_time=".time()." WHERE id='".$_REQUEST['oid']."'";
        } elseif(@$_REQUEST['id_plant']) {
	        $SQL="UPDATE p_items SET id_plant='".$_REQUEST['id_plant']."' WHERE id_item='".$_REQUEST['oid']."'";
        }


//act=plants&module=orders&a=moveorder&oid='+id_order+'&id_plant='+id_plant;
        //echo $SQL."<br>";
		mysql_query($SQL);
    }


    ShowError("Сохранено");
    unset($_REQUEST);
}


?>

<table cellpadding=3 width=98%>
<tr style="font-weight:bold;text-align:center">
    <td background='i/bgr-grid-sand.gif'> ID:</td>
    <td background='i/bgr-grid-sand.gif'> Название:</td>
    <td background='i/bgr-grid-sand.gif'> Для кого:</td>
    <td background='i/bgr-grid-sand.gif' nowrap> Время:</td>
    <td background='i/bgr-grid-sand.gif'> Кол-во:</td>
    <td background='i/bgr-grid-sand.gif'> Монет:</td>
    <td background='i/bgr-grid-sand.gif'> В ресах:</td>
    <td background='i/bgr-grid-sand.gif'> Заказ:</td>
</tr>

<?
$SQL="SELECT o.*, i.* FROM p_orders o INNER JOIN p_items i USING (id_item) WHERE o.status<>1 ORDER BY i.id_plant,o.order_time";
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
            $StatusStr="<a href='?act=plants&a=execute&begin=1&oid={$d['id']}&module=orders'>запустить</a><br>";
            break;
        case 2:
        	$productionT=$d['creationt']*60*$d['q'];
            $prodStr = (($d['begin_time']+$productionT)>time()) ? "<div align=center><b>Completed: <font color=red>".floor(((time()-$d['begin_time'])/$productionT)*100)."</font>%</b></div>"  : "<div align=center><font color=red><b>готово</b></font></div>";
        	$TimeStr="производство начато:<br><b>".date("d.m.y H:i",$d['order_time'])."</b><br>будет готово:<br><b>".date("d.m.y H:i",($d['begin_time']+$productionT))."</b>".$prodStr;
            $StatusStr="<a href='?act=plants&a=execute&finish=1&oid={$d['id']}&module=orders'>готово</a><br>";
            break;

    }

    if($Pid!=$d['id_plant']) {
       $Pid=$d['id_plant'];
?>
<tr align=center>
<td colspan=8 background='i/bgr-grid-sand1.gif' valign=top><b>Завод №<?=$d['id_plant']." ".$ArrayPlants[$d['id_plant']]?></b></td>
</tr>
<?
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
        <?=$StatusStr;?>
        <a href='JavaScript:moveorder(<?=$d['id_item']?>)'>перенести</a><br>
        <a href='?act=plants&module=orders&a=execute&oid=<?=$d['id']?>&delete=1&q=<?=$d['q']?>&iid=<?=$d['id_item']?>'>удалить</a>
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