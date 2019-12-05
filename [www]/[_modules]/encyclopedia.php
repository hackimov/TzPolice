<h1>Энциклопедия предметов</h1>

<SCRIPT>
 function adds(id,name,img,inf1,inf2,inf3) {
  document.write("<table width=\"100%\" border=\"0\" cellspacing=\"3\" cellpadding=\"0\"><tr><td height=\"10\" background=\"i/bgr-grid-sand.gif\"><p><img src=\"i/bullet-red-01a.gif\" width=\"18\" height=\"11\" hspace=\"5\"><strong>"+name+"</strong></p></td></tr></table><TABLE border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"580\" align=center> <TR><td align=\"left\" valign=\"top\" width=\"150\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\" width=\"150\" height=\"150\" id=\"items1\" align=\"left\"><param name=\"allowScriptAccess\" value=\"sameDomain\" /><param name=\"movie\" value=\"/items.swf?sh="+img+"\" /><param name=\"quality\" value=\"high\" /><param name=\"bgcolor\" value=\"#ffffff\" /></object></td><td valign=\"top\" width=\"230\">"+inf1+"</td><td valign=\"top\" width=\"190\">"+inf2+"</td><td valign=\"top\" width=\"150\">"+inf3+"</td></tr></TABLE><br>");
 }
</SCRIPT>
<center>
<table border="0" cellspacing="0" cellpadding="3" width="600" style="BORDER: 1px #957850 solid;">
<tr><td style="BORDER-BOTTOM: 1px #957850 solid" background="i/bgr-grid-sand.gif" align="center"><b>Разделы</b></td></tr>
<tr><td background="i/bgr-grid-sand1.gif">
<table cellsapcing="0" cellpadding="0">
<tr>
  <td valign="top">
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=knifes">Холодное оружие</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=lwpn">Лёгкое оружие</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=mwpn">Винтовки/автоматы</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=hwpn">Тяжелое оружие</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=ewpn">Энергитическое оружие</a><br>
</td>
<td valign="top"><img src="i/none.gif" width="10"></td>
<td valign="top">
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=harmor">Каски/береты</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=barmor">Куртки/бронежелеты</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=aarmor">Нарукавники</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=larmor">Брюки</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=farmor">Обувь</a><br>
</td>
<td valign="top"><img src="i/none.gif" width="10"></td>
<td valign="top">
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=ammo">Патроны</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=eammo">Энергомодули</a><br><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=mod">Модернизация</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=med">Медицина</a><br>
</td>
<td valign="top"><img src="i/none.gif" width="10"></td>
<td valign="top">
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=compl">Комплекты</a>
</td>
</tr>
</table>
</td></tr>
</table><br>
<?php
  if (!$id) { $id = "knifes"; }
  $id_names = array(
    "knifes"=>"Холодное оружие",
    "lwpn"=>"Лёгкое оружие",
    "mwpn"=>"Винтовки/автоматы",
    "hwpn"=>"Тяжелое оружие",
    "ewpn"=>"Энергитическое оружие",
    "ammo"=>"Патроны",
    "eammo"=>"Энергомодули",
    "harmor"=>"Каски/береты",
    "barmor"=>"Куртки/бронежелеты",
    "aarmor"=>"Нарукавники",
    "larmor"=>"Брюки",
    "farmor"=>"Обувь",
    "mod"=>"Модернизация",
    "med"=>"Медицина",
    "compl"=>"Комплект: ",
    "army"=>"Army",
    "raiders"=>"Raiders",
    "marauders"=>"Marauders",
    "stalkers"=>"Stalkers",
    "flex"=>"Flex",
    "hitech"=>"HiTech",
    "power"=>"Power",
    "energy"=>"Enegry",
  );
  $item_groups = array(
    "item"=> "itm_id",
    "name"=> "itm_name",
    "image"=> "itm_image",
    "def_imp"=> "itm_def",
    "def_poison"=> "itm_def",
    "def_burn"=> "itm_def",
    "def_energ"=> "itm_def",
    "def_paral"=> "itm_def",
    "def_blind"=> "itm_def",
    "def_panic"=> "itm_def",
    "def_zomb"=> "itm_def",
    "def_hol"=> "itm_def",
    "dmg_imp"=> "itm_dmg",
    "dmg_energ"=> "itm_dmg",
    "dmg_burn"=> "itm_dmg",
    "dmg_blind"=> "itm_dmg",
    "dmg_paral"=> "itm_dmg",
    "use_hit"=> "itm_use",
    "use_shot"=> "itm_use",
    "use_throw"=> "itm_use",
    "use_targshot"=> "itm_use",
    "use_targshot2"=> "itm_use",
    "use_reload"=> "itm_use",
    "use_burstbul"=> "itm_use",
    "use_burst"=> "itm_use",
    "use_qure"=> "itm_use",
    "weight"=> "itm_main",
    "quality"=> "itm_main",
    "calibre"=> "itm_main",
    "ammo"=> "itm_main",
    "shotlen"=> "itm_main",
    "shotradius"=> "itm_main",
    "burst"=> "itm_main",
    "add_str"=> "itm_add",
    "add_dex"=> "itm_add",
    "add_int"=> "itm_add",
    "add_pow"=> "itm_add",
    "add_acc"=> "itm_add",
    "add_intel"=> "itm_add",
    "qure_hp"=> "itm_qure",
    "qure_poison"=> "itm_qure",
    "req_level"=> "itm_req",
    "req_str"=> "itm_req",
    "req_dex"=> "itm_req",
    "req_int"=> "itm_req",
    "req_intel"=> "itm_req",
    "req_acc"=> "itm_req",
  );
  $item_templates = array(
    "item"=> "%item",
    "name"=> "%name",
    "image"=> "%image",
    "def_imp"=> "<li> Ударное: %def_imp",
    "def_poison"=> "<li> Отравление: %def_poison",
    "def_burn"=> "<li> Ожог: %def_burn",
    "def_energ"=> "<li> Энергетическое: %def_energ",
    "def_paral"=> "<li> Парализация: %def_paral",
    "def_blind"=> "<li> Ослепление: %def_blind",
    "def_panic"=> "<li> Паника: %def_panic",
    "def_zomb"=> "<li> Зомбирование: %def_zomb",
    "def_hol"=> "<li> Галлюцинации: %def_hol",
    "dmg_imp"=> "<li> Ударное: %dmg_imp HP",
    "dmg_energ"=> "<li> Энергетическое: %dmg_energ HP",
    "dmg_burn"=> "<li> Ожог: %dmg_burn HP",
    "dmg_paral"=> "<li> Парализация: %dmg_paral ОД",
    "dmg_blind"=> "<li> Ослепление: %dmg_blind хода",
    "use_hit"=> "<li> Ударить: %use_hit ОД",
    "use_shot"=> "<li> Выстрел: %use_shot ОД",
    "use_throw"=> "<li> Метнуть: %use_throw ОД",
    "use_targshot"=> "<li> Прицельно: %use_targshot ОД",
    "use_targshot2"=> "<li> Прицельно 2: %use_targshot2 ОД",
    "use_reload"=> "<li> Перезарядить: %use_reload ОД",
    "use_burstbul"=> "<li> Очередь (%use_burstbul пуль): ",
    "use_burst"=> "%use_burst ОД",
    "use_qure"=> "<li> Аптечка: %use_qure ОД",
    "weight"=> "<li> Масса: %weight",
    "quality"=> "<li> Качество: %quality",
    "calibre"=> "<li> Калибр: %calibre мм",
    "ammo"=> "<li> Обойма: %ammo шт.",
    "shotlen"=> "<li> Дальность: %shotlen",
    "shotradius"=> "<li> Радиус повреждений: %shotradius",
    "burst"=> "<li> Кучность: %burst%",
    "add_str"=> "<li> Сила: %add_str",
    "add_dex"=> "<li> Ловкость: %add_dex",
    "add_int"=> "<li> Интуиция: %add_int",
    "add_pow"=> "<li> Выносливость: %add_pow",
    "add_acc"=> "<li> Меткость: %add_acc",
    "add_intel"=> "<li> Интеллект: %add_intel",
    "qure_hp"=> "<li> Жизнь: %qure_hp HP",
    "qure_poison"=> "<li> Отравление: %qure_poison",
    "req_level"=> "<li> Уровень: %req_level",
    "req_str"=> "<li> Сила: %req_str",
    "req_dex"=> "<li> Ловкость: %req_dex",
    "req_int"=> "<li> Интуиция: %req_int",
    "req_intel"=> "<li> Интеллект: %req_intel",
    "req_acc"=> "<li> Меткость: %req_acc",
  );
  if ($id == "compl") {
?>
<table border="0" cellspacing="0" cellpadding="3" width="600" style="BORDER: 1px #957850 solid;">
<tr><td style="BORDER-BOTTOM: 1px #957850 solid" background="i/bgr-grid-sand.gif" align="center"><b>Комплекты</b></td></tr>
<tr><td background="i/bgr-grid-sand1.gif" align="center">
<table cellsapcing="0" cellpadding="0">
<tr>
  <td valign="top">
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=compl&compl=army">Army</a>&nbsp;
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=compl&compl=raiders">Raiders</a>&nbsp;
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=compl&compl=marauders">Marauders</a>&nbsp;
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=compl&compl=stalkers">Stalkers</a>&nbsp;
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=compl&compl=flex">Flex</a>&nbsp;
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=compl&compl=hitech">HiTech</a>&nbsp;
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=compl&compl=power">Power</a>&nbsp;
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=compl&compl=energy">Energy</a>&nbsp;
</td>
</tr>
</table>
</td></tr>
</table><br>
<?php
  if (!$compl) { $compl = "army"; }
  }
?>
<?php if (!$id_names[$id]) { echo "Такого раздела не существует"; } else { ?>
    <table border="0" cellspacing="0" cellpadding="3" width="600" style="BORDER: 1px #957850 solid;">
      <tr><td style="BORDER-BOTTOM: 1px #957850 solid" background="i/bgr-grid-sand.gif" align="center"><b><?php if ($id <> "compl") { echo $id_names[$id]; } else { echo $id_names[$id] . $id_names[$compl]; } ?></b></td></tr>
      <tr><td background="i/bgr-grid-sand1.gif">
<?php
  if ($id == "compl") { $result = mysql_query("SELECT * FROM encyclopedia WHERE (compl='$compl')"); }
  else { $result = mysql_query("SELECT * FROM encyclopedia WHERE (type='$id')"); }
  if (mysql_num_rows($result) == 0 ) { echo "<center><br>Раздел пуст<br><br></center>"; }
  else {
    while ($row = mysql_fetch_assoc($result)) {
      $itm_id = "";
      $itm_name = "";
      $itm_image = "";
      $itm_def = "<b>Защита:</b>";
      $itm_dmg = "<b>Повреждения:</b>";
      $itm_use = "<br><br><b>Применение:</b>";
      $itm_main = "<b>Характеристики:</b>";
      $itm_add = "<br><br><b>Бонусы:</b>";
      $itm_qure = "<b>Исцеление:</b>";
      $itm_req = "<b>Требования:</b>";

      while (list ($key, $val) = each ($row)) {
        if ($val && $item_groups[$key]) {
          if ($item_groups[$key] == "itm_add") { if ($val < 0) { $add_cof = "-"; }  else { $add_cof = "+"; } }
          else { $add_cof = ""; }
          $$item_groups[$key] = $$item_groups[$key] . str_replace("%".$key, $add_cof.$val,$item_templates[$key]);
        }
      }
      $itm_sec1 = "";
      $itm_sec2 = "";
      if ($itm_dmg <> "<b>Повреждения:</b>") { $itm_sec1 .= $itm_dmg; }
      if ($itm_def <> "<b>Защита:</b>") { $itm_sec1 .= $itm_def; }
      if ($itm_qure <> "<b>Исцеление:</b>") { $itm_sec1 .= $itm_qure; }
      if ($itm_use <> "<br><br><b>Применение:</b>") { $itm_sec1 .= $itm_use; }
      if ($itm_main <> "<b>Характеристики:</b>") { $itm_sec2 .= $itm_main; }
      if ($itm_add <> "<br><br><b>Бонусы:</b>") { $itm_sec2 .= $itm_add; }
      if ($id <> "ammo") { if ($itm_req == "<b>Требования:</b>") { $itm_req .= "<li> ---"; } } else { $itm_req = "&nbsp;"; }
      if (substr($id,1) == "armor" && !$itm_sec1) { $itm_sec1 .= "<b>Защита:</b><li> ---"; }
      echo "<SCRIPT>adds('".$itm_id."','".$itm_name."','".$itm_image."','".$itm_sec1."','".$itm_sec2."','".$itm_req."');</SCRIPT>";
    }
  }
?>
<?php } ?>
      </td></tr>
    </table>
</center>