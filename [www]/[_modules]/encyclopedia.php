<h1>������������ ���������</h1>

<SCRIPT>
 function adds(id,name,img,inf1,inf2,inf3) {
  document.write("<table width=\"100%\" border=\"0\" cellspacing=\"3\" cellpadding=\"0\"><tr><td height=\"10\" background=\"i/bgr-grid-sand.gif\"><p><img src=\"i/bullet-red-01a.gif\" width=\"18\" height=\"11\" hspace=\"5\"><strong>"+name+"</strong></p></td></tr></table><TABLE border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"580\" align=center> <TR><td align=\"left\" valign=\"top\" width=\"150\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\" width=\"150\" height=\"150\" id=\"items1\" align=\"left\"><param name=\"allowScriptAccess\" value=\"sameDomain\" /><param name=\"movie\" value=\"/items.swf?sh="+img+"\" /><param name=\"quality\" value=\"high\" /><param name=\"bgcolor\" value=\"#ffffff\" /></object></td><td valign=\"top\" width=\"230\">"+inf1+"</td><td valign=\"top\" width=\"190\">"+inf2+"</td><td valign=\"top\" width=\"150\">"+inf3+"</td></tr></TABLE><br>");
 }
</SCRIPT>
<center>
<table border="0" cellspacing="0" cellpadding="3" width="600" style="BORDER: 1px #957850 solid;">
<tr><td style="BORDER-BOTTOM: 1px #957850 solid" background="i/bgr-grid-sand.gif" align="center"><b>�������</b></td></tr>
<tr><td background="i/bgr-grid-sand1.gif">
<table cellsapcing="0" cellpadding="0">
<tr>
  <td valign="top">
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=knifes">�������� ������</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=lwpn">˸���� ������</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=mwpn">��������/��������</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=hwpn">������� ������</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=ewpn">�������������� ������</a><br>
</td>
<td valign="top"><img src="i/none.gif" width="10"></td>
<td valign="top">
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=harmor">�����/������</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=barmor">������/�����������</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=aarmor">�����������</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=larmor">�����</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=farmor">�����</a><br>
</td>
<td valign="top"><img src="i/none.gif" width="10"></td>
<td valign="top">
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=ammo">�������</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=eammo">������������</a><br><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=mod">������������</a><br>
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=med">��������</a><br>
</td>
<td valign="top"><img src="i/none.gif" width="10"></td>
<td valign="top">
<img src="i/bullet-red-01.gif">&nbsp;<a href="?act=encycl&id=compl">���������</a>
</td>
</tr>
</table>
</td></tr>
</table><br>
<?php
  if (!$id) { $id = "knifes"; }
  $id_names = array(
    "knifes"=>"�������� ������",
    "lwpn"=>"˸���� ������",
    "mwpn"=>"��������/��������",
    "hwpn"=>"������� ������",
    "ewpn"=>"�������������� ������",
    "ammo"=>"�������",
    "eammo"=>"������������",
    "harmor"=>"�����/������",
    "barmor"=>"������/�����������",
    "aarmor"=>"�����������",
    "larmor"=>"�����",
    "farmor"=>"�����",
    "mod"=>"������������",
    "med"=>"��������",
    "compl"=>"��������: ",
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
    "def_imp"=> "<li> �������: %def_imp",
    "def_poison"=> "<li> ����������: %def_poison",
    "def_burn"=> "<li> ����: %def_burn",
    "def_energ"=> "<li> ��������������: %def_energ",
    "def_paral"=> "<li> �����������: %def_paral",
    "def_blind"=> "<li> ����������: %def_blind",
    "def_panic"=> "<li> ������: %def_panic",
    "def_zomb"=> "<li> ������������: %def_zomb",
    "def_hol"=> "<li> ������������: %def_hol",
    "dmg_imp"=> "<li> �������: %dmg_imp HP",
    "dmg_energ"=> "<li> ��������������: %dmg_energ HP",
    "dmg_burn"=> "<li> ����: %dmg_burn HP",
    "dmg_paral"=> "<li> �����������: %dmg_paral ��",
    "dmg_blind"=> "<li> ����������: %dmg_blind ����",
    "use_hit"=> "<li> �������: %use_hit ��",
    "use_shot"=> "<li> �������: %use_shot ��",
    "use_throw"=> "<li> �������: %use_throw ��",
    "use_targshot"=> "<li> ���������: %use_targshot ��",
    "use_targshot2"=> "<li> ��������� 2: %use_targshot2 ��",
    "use_reload"=> "<li> ������������: %use_reload ��",
    "use_burstbul"=> "<li> ������� (%use_burstbul ����): ",
    "use_burst"=> "%use_burst ��",
    "use_qure"=> "<li> �������: %use_qure ��",
    "weight"=> "<li> �����: %weight",
    "quality"=> "<li> ��������: %quality",
    "calibre"=> "<li> ������: %calibre ��",
    "ammo"=> "<li> ������: %ammo ��.",
    "shotlen"=> "<li> ���������: %shotlen",
    "shotradius"=> "<li> ������ �����������: %shotradius",
    "burst"=> "<li> ��������: %burst%",
    "add_str"=> "<li> ����: %add_str",
    "add_dex"=> "<li> ��������: %add_dex",
    "add_int"=> "<li> ��������: %add_int",
    "add_pow"=> "<li> ������������: %add_pow",
    "add_acc"=> "<li> ��������: %add_acc",
    "add_intel"=> "<li> ���������: %add_intel",
    "qure_hp"=> "<li> �����: %qure_hp HP",
    "qure_poison"=> "<li> ����������: %qure_poison",
    "req_level"=> "<li> �������: %req_level",
    "req_str"=> "<li> ����: %req_str",
    "req_dex"=> "<li> ��������: %req_dex",
    "req_int"=> "<li> ��������: %req_int",
    "req_intel"=> "<li> ���������: %req_intel",
    "req_acc"=> "<li> ��������: %req_acc",
  );
  if ($id == "compl") {
?>
<table border="0" cellspacing="0" cellpadding="3" width="600" style="BORDER: 1px #957850 solid;">
<tr><td style="BORDER-BOTTOM: 1px #957850 solid" background="i/bgr-grid-sand.gif" align="center"><b>���������</b></td></tr>
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
<?php if (!$id_names[$id]) { echo "������ ������� �� ����������"; } else { ?>
    <table border="0" cellspacing="0" cellpadding="3" width="600" style="BORDER: 1px #957850 solid;">
      <tr><td style="BORDER-BOTTOM: 1px #957850 solid" background="i/bgr-grid-sand.gif" align="center"><b><?php if ($id <> "compl") { echo $id_names[$id]; } else { echo $id_names[$id] . $id_names[$compl]; } ?></b></td></tr>
      <tr><td background="i/bgr-grid-sand1.gif">
<?php
  if ($id == "compl") { $result = mysql_query("SELECT * FROM encyclopedia WHERE (compl='$compl')"); }
  else { $result = mysql_query("SELECT * FROM encyclopedia WHERE (type='$id')"); }
  if (mysql_num_rows($result) == 0 ) { echo "<center><br>������ ����<br><br></center>"; }
  else {
    while ($row = mysql_fetch_assoc($result)) {
      $itm_id = "";
      $itm_name = "";
      $itm_image = "";
      $itm_def = "<b>������:</b>";
      $itm_dmg = "<b>�����������:</b>";
      $itm_use = "<br><br><b>����������:</b>";
      $itm_main = "<b>��������������:</b>";
      $itm_add = "<br><br><b>������:</b>";
      $itm_qure = "<b>���������:</b>";
      $itm_req = "<b>����������:</b>";

      while (list ($key, $val) = each ($row)) {
        if ($val && $item_groups[$key]) {
          if ($item_groups[$key] == "itm_add") { if ($val < 0) { $add_cof = "-"; }  else { $add_cof = "+"; } }
          else { $add_cof = ""; }
          $$item_groups[$key] = $$item_groups[$key] . str_replace("%".$key, $add_cof.$val,$item_templates[$key]);
        }
      }
      $itm_sec1 = "";
      $itm_sec2 = "";
      if ($itm_dmg <> "<b>�����������:</b>") { $itm_sec1 .= $itm_dmg; }
      if ($itm_def <> "<b>������:</b>") { $itm_sec1 .= $itm_def; }
      if ($itm_qure <> "<b>���������:</b>") { $itm_sec1 .= $itm_qure; }
      if ($itm_use <> "<br><br><b>����������:</b>") { $itm_sec1 .= $itm_use; }
      if ($itm_main <> "<b>��������������:</b>") { $itm_sec2 .= $itm_main; }
      if ($itm_add <> "<br><br><b>������:</b>") { $itm_sec2 .= $itm_add; }
      if ($id <> "ammo") { if ($itm_req == "<b>����������:</b>") { $itm_req .= "<li> ---"; } } else { $itm_req = "&nbsp;"; }
      if (substr($id,1) == "armor" && !$itm_sec1) { $itm_sec1 .= "<b>������:</b><li> ---"; }
      echo "<SCRIPT>adds('".$itm_id."','".$itm_name."','".$itm_image."','".$itm_sec1."','".$itm_sec2."','".$itm_req."');</SCRIPT>";
    }
  }
?>
<?php } ?>
      </td></tr>
    </table>
</center>