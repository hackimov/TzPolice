<h1>��������� ��������</h1>



<?php



$bgstr[0]="background='i/bgr-grid-sand.gif'";

$bgstr[1]="background='i/bgr-grid-sand1.gif'";



function res($a) {

	switch($a) {

		case 'Coins':			return 'coins';

		case 'Metals':			return 'metals';

		case 'Precious metals':		return 'gold';

		case 'Polymers':			return 'polymers';

		case 'Organic':			return 'organic';

		case 'Venom':			return 'venom';

		case 'Radioactive materials':	return 'radioactive';

		case 'Gems':			return 'gems';

		case 'Silicon':			return 'silicon';



  		case 'Metals (enriched)':			return 'metals';

		case 'Precious metals (enriched)':		return 'gold';

		case 'Polymers (enriched)':			return 'polymers';

		case 'Organic (enriched)':			return 'organic';

		case 'Venom (enriched)':			return 'venom';

		case 'Radioactive materials (enriched)':	return 'radioactive';

		case 'Gems (enriched)':				return 'gems';

		case 'Silicon (enriched)':			return 'silicon';

		default:				return '';

	}

}



if(AuthStatus==1 && AuthUserName!="" && (AuthUserClan=='police' || AuthUserClan=='Military Police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy')) {



$permission=0;

$SQL = "SELECT * FROM build_users WHERE user_id='".AuthUserId."'";

$r = mysql_query($SQL);

if ($d=mysql_fetch_array($r)) $permission=$d['warehouse'];



$buildings = array();

$SQL = "SELECT * FROM buildings WHERE `type` < 3";

$r=mysql_query($SQL);

while ($d=mysql_fetch_array($r)) {

	$buildings[$d['id']] = $d['name'];

	$build_id[$d['full_name']] = $d['id'];

	$build_type[$d['id']] = $d['type'];

}



if($a=="add_res") {

	if ($_REQUEST['coins']+$_REQUEST['metal']+$_REQUEST['gold']+$_REQUEST['polymer']+$_REQUEST['organic']+$_REQUEST['venom']+$_REQUEST['rad']+$_REQUEST['gem']+$_REQUEST['silicon']>0) {

		//$userinfo = GetUserInfo($_REQUEST['from_name']);

		$userinfo['login'] = $_REQUEST['from_name'];

		if ($userinfo['login'] != "") {

			$SQL="INSERT INTO warehouses values('',

			'".time()."',

			'".AuthUserId."',

			'".$userinfo['login']."',

			'".$_REQUEST['coins']."',

			'".$_REQUEST['metal']."',

			'".$_REQUEST['gold']."',

			'".$_REQUEST['polymer']."',

			'".$_REQUEST['organic']."',

			'".$_REQUEST['venom']."',

			'".$_REQUEST['rad']."',

			'".$_REQUEST['gem']."',

			'".$_REQUEST['silicon']."',

			'".$_REQUEST['warehouse']."','1')";

			mysql_query($SQL);

			$error='';

		} else $error=$_REQUEST['from_name'];

	} else $error='';

	echo "<script>top.location='?act=warehouses2".($error?'&error='.$_REQUEST['from_name']:'')."';</script>";

}

if($a=="add") {

	$accept = 1;

	$SQL="SELECT id FROM site_users WHERE user_name='".$_REQUEST['name']."'";

	$r=mysql_query($SQL);

	if($d=mysql_fetch_array($r)) {

		$to_id = $d['id'];

	} else {

		if ($_REQUEST['do'] != 1) {

			$accept = 0;

			echo "<h6><font color=red>�� ������ �������� ".$_REQUEST['name']."</font></h6>\n";

		}

	}

	if (@$_POST['text'] && $accept) {

		$names = array();

		$lines = explode("\n", $_POST['text']);

		foreach ($lines as $line) {

			$sf = strpos($line, " � ������ ");

			if ($sf > 0) {

				$tm = substr($line, 0, 5);

				$ss = strpos($line, "������ ��������: ");

				$name = substr($line, $sf+12, strrpos($line, "'")-$sf-13);

				$resource = substr($line, $ss+17, strpos($line, "[")-$ss-17);

				$count = substr($line, strpos($line, "[")+1, strpos($line, "]")-strpos($line, "[")-1);

				if (res($resource) != '') {

					$names[$name] = 1;

					if(!($p[$name])) $p[$name] = array();

					$p[$name][] = res($resource);

					$p[$name][] = $count;

					$p[$name][] = $tm;

				}

			}

		}

		$day = substr($_POST['dt'],0,2);

		$month = substr($_POST['dt'],3,2);

		$year = '20'.substr($_POST['dt'],6,2);

		foreach (array_keys($names) as $name) {

			if ($build_id[$name]) {

			$tm = $p[$name][2]; $rs = array();

			for ($i=0; $i<sizeof($p[$name]); $i+=3) {

				if ($tm != $p[$name][$i+2]) {

					$hour = substr($tm,0,2);

					$min = substr($tm,3,2);

					if ($_REQUEST['do'] == 1) {

						$SQL="INSERT INTO warehouses values('',

						'".mktime($hour,$min,0,$month,$day,$year)."',

						'".AuthUserId."',

						'".(strlen($_REQUEST['name'])>2?$_REQUEST['name']:AuthUserName)."',

						'".$rs['coins']."',

						'".$rs['metals']."',

						'".$rs['gold']."',

						'".$rs['polymers']."',

						'".$rs['organic']."',

						'".$rs['venom']."',

						'".$rs['radioactive']."',

						'".$rs['gems']."',

						'".$rs['silicon']."',

						'".$build_id[$name]."','1')";

					} elseif ($_REQUEST['do'] == 2) {

						$SQL="INSERT INTO warehouses values('',

						'".mktime($hour,$min,0,$month,$day,$year)."',

						'".$to_id."',

						'".AuthUserName."',

						'".$rs['coins']."',

						'".$rs['metals']."',

						'".$rs['gold']."',

						'".$rs['polymers']."',

						'".$rs['organic']."',

						'".$rs['venom']."',

						'".$rs['radioactive']."',

						'".$rs['gems']."',

						'".$rs['silicon']."',

						'".$build_id[$name]."','5')";

					}

					mysql_query($SQL);

					$tm = $p[$name][$i+2];

					$rs = array();

				}

				$rs[$p[$name][$i]] += $p[$name][$i+1];

			}

			$hour = substr($tm,0,2);

			$min = substr($tm,3,2);

			if ($_REQUEST['do'] == 1) {

				$SQL="INSERT INTO warehouses values('',

				'".mktime($hour,$min,0,$month,$day,$year)."',

				'".AuthUserId."',

				'".(strlen($_REQUEST['name'])>2?$_REQUEST['name']:AuthUserName)."',

				'".$rs['coins']."',

				'".$rs['metals']."',

				'".$rs['gold']."',

				'".$rs['polymers']."',

				'".$rs['organic']."',

				'".$rs['venom']."',

				'".$rs['radioactive']."',

				'".$rs['gems']."',

				'".$rs['silicon']."',

				'".$build_id[$name]."','1')";

			} elseif ($_REQUEST['do'] == 2) {

				$SQL="INSERT INTO warehouses values('',

				'".mktime($hour,$min,0,$month,$day,$year)."',

				'".$to_id."',

				'".AuthUserName."',

				'".$rs['coins']."',

				'".$rs['metals']."',

				'".$rs['gold']."',

				'".$rs['polymers']."',

				'".$rs['organic']."',

				'".$rs['venom']."',

				'".$rs['radioactive']."',

				'".$rs['gems']."',

				'".$rs['silicon']."',

				'".$build_id[$name]."','5')";

			}

			mysql_query($SQL);

			}

		}

	}

}

if(@$_REQUEST['error']) {

	echo "<h6>������������ <b>".$_REQUEST['error']."</b> �� ������.</h6>";

}

if(@$_REQUEST['cancelID']>0) {

	$SQL="DELETE FROM warehouses WHERE id='".addslashes($_REQUEST['cancelID'])."'";

	mysql_query($SQL);

	echo "<script>top.location='?act=warehouses2';</script>";

}

if(@$_REQUEST['helpID']>0) {

	$SQL="UPDATE warehouses SET status=1 WHERE id='".addslashes($_REQUEST['helpID'])."'";

	mysql_query($SQL);

	echo "<script>top.location='?act=warehouses2';</script>";

}



//Fucking checkboxes



if(@$_REQUEST['Submit'] == "�������") {

	$fucking_checkboxes = $_REQUEST['orderId'];

    foreach ($fucking_checkboxes as $cur_order_id) {

	$SQL="SELECT * FROM warehouses WHERE id='".addslashes($cur_order_id)."'";

	$d=mysql_fetch_array(mysql_query($SQL));

	if ($build_type[$d['warehouse']]!=1) {

		mysql_query("INSERT INTO warehouses values('',

			'".time()."',

			'".$d['customer']."',

			'".AuthUserName."',

			'-".$d['coins']."',

			'-".$d['metals']."',

			'-".$d['gold']."',

			'-".$d['polymers']."',

			'-".$d['organic']."',

			'-".$d['venom']."',

			'-".$d['radioactive']."',

			'-".$d['gems']."',

			'-".$d['silicon']."',

			'".$d['warehouse']."',

			'0')");

		mysql_query("INSERT INTO warehouses values('',

			'".(time()+1)."',

			'1',

			'".AuthUserName."',

			'".$d['coins']."',

			'".$d['metals']."',

			'".$d['gold']."',

			'".$d['polymers']."',

			'".$d['organic']."',

			'".$d['venom']."',

			'".$d['radioactive']."',

			'".$d['gems']."',

			'".$d['silicon']."',

			'".$d['warehouse']."',

			'0')");

	}

	$SQL="UPDATE warehouses SET status=0 WHERE id='".addslashes($cur_order_id)."'";

	mysql_query($SQL);

    }

	echo "<script>top.location='?act=warehouses2';</script>";

}

if(@$_REQUEST['Submit'] == "���������") {

	$fucking_checkboxes = $_REQUEST['orderId'];

    foreach ($fucking_checkboxes as $cur_order_id) {

	$SQL="UPDATE warehouses SET status=2 WHERE id='".addslashes($cur_order_id)."'";

	mysql_query($SQL);

    }

	echo "<script>top.location='?act=warehouses2';</script>";

}

//End fucking checkboxes



if(@$_REQUEST['acceptID']>0) {

	$SQL="SELECT * FROM warehouses WHERE id='".addslashes($_REQUEST['acceptID'])."'";

	$d=mysql_fetch_array(mysql_query($SQL));

	if ($build_type[$d['warehouse']]!=1) {

		mysql_query("INSERT INTO warehouses values('',

			'".time()."',

			'".$d['customer']."',

			'".AuthUserName."',

			'-".$d['coins']."',

			'-".$d['metals']."',

			'-".$d['gold']."',

			'-".$d['polymers']."',

			'-".$d['organic']."',

			'-".$d['venom']."',

			'-".$d['radioactive']."',

			'-".$d['gems']."',

			'-".$d['silicon']."',

			'".$d['warehouse']."',

			'0')");

		mysql_query("INSERT INTO warehouses values('',

			'".(time()+1)."',

			'1',

			'".AuthUserName."',

			'".$d['coins']."',

			'".$d['metals']."',

			'".$d['gold']."',

			'".$d['polymers']."',

			'".$d['organic']."',

			'".$d['venom']."',

			'".$d['radioactive']."',

			'".$d['gems']."',

			'".$d['silicon']."',

			'".$d['warehouse']."',

			'0')");

	}

	$SQL="UPDATE warehouses SET status=0 WHERE id='".addslashes($_REQUEST['acceptID'])."'";

	mysql_query($SQL);

	echo "<script>top.location='?act=warehouses2';</script>";

}

if(@$_REQUEST['declineID']>0) {

	$SQL="UPDATE warehouses SET status=2 WHERE id='".addslashes($_REQUEST['declineID'])."'";

	mysql_query($SQL);

	echo "<script>top.location='?act=warehouses2';</script>";

}

if(@$_REQUEST['seenID']>0) {

	$SQL="UPDATE warehouses SET status=3 WHERE id='".addslashes($_REQUEST['seenID'])."'";

	mysql_query($SQL);

	echo "<script>top.location='?act=warehouses2';</script>";

}

if (AuthUserGroup == 100) {

	if (@$_REQUEST['correct']>0) {

		$SQL="SELECT * FROM warehouses WHERE user_id=1 ORDER BY warehouse";

		$r=mysql_query($SQL);

		while ($d=mysql_fetch_array($r)) {

			$SQL="SELECT * FROM warehouses WHERE id='".($d['id']-1)."'";

			$s=mysql_fetch_array(mysql_query($SQL));

			if ($s['coins']+$s['polymers']+$s['organic']+$s['venom']+$s['radioactive']+$s['gold']+$s['gems']+$s['metals']+$s['silicon']>0) {

				echo date("d.m.y H:i",$d['dt'])." �������: ".$d['id']." - ��� ��������<br>\n";

				echo "coins[".$d['coins']."], polymers[".$d['polymers']."], organic[".$d['organic']."], venom[".$d['venom']."], radioactive[".$d['radioactive']."], gold[".$d['gold']."], gems[".$d['gems']."], metals[".$d['metals']."], silicon[".$d['silicon']."]<br>\n";

			}

		}

	}

}

?>



<link href="_modules/tabs.css" type=text/css rel=stylesheet>

<script language=javascript src="_modules/tabs.js"></script>



<center>

<table border="0" cellspacing="0" background="i/bgr-grid-sand.gif" cellpadding="10" style="BORDER: 1px #957850 solid;">

<tr><td>

<table width="100%"><tr><td align="center"><font color="red"><b>��������!</b></font><br></td></tr></table>

���� �� �������� ������� ��� ���� ����, ��� �������, ��:<br>

1. ���������� ������� ����� ���������, ��� �������<br>

2. ���������� ���� ���� � � ����� "������" ���������� "��� ����"<br>

3. � ����� "��� ������" ���������� ��� ����, ��� �������<br>

4. ��������� ������ �� ������� ����� ��������� � ����� "��������"<br>

���� �� �������� ����������:<br>

1. ���������� ������� ����� ���������<br>

2. ���������� ���� ���� � � ����� "������" ��������� "��� ���������"<br>

3. � ����� "���� ������" ���������� ��� ����, ���� �������<br>

4. ��������� ������ �� ������� ����� ��������� � ����� "��������"<br>

����� ������������:<br>

1. ��������� �������� ������ ������������ �������� �� ����������<br>

2. ������� ����� ���� �����������, ���� �������� ���. �������<br>

3. ��� �������� ����� ��������� ������ ��������������� ������������<br>

4. �� ���������� �������� ����� ��������� ������������� �������<br>

<br>

<font color="red"><b>����, ��� ����� ���� ��� 2-� ������� � �����. ����� ��� ������� �������� ������������ � ���������� � 1 ������!!!</b></font>

</td></tr>

</table>

</center>



<br>

<!-- ���������� ��������� ��� ������ -->





<?if(AuthUserGroup == 100) {?>

<p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>For MbILLIKA only:</strong>

<center>

<iframe src="direct_call/warehouses_logs.php" name="wh_editor" width="600" marginwidth="0" height="230" marginheight="0" align="middle" scrolling="auto"><br>

</iframe>

</center>

<?}?>













<br>

<?if(AuthUserGroup == 100) {?>

<table width='100%' border='0' cellspacing='3' cellpadding='2'>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>�������� �� �����:</strong> </p></td></tr>

<tr><td>



<form action="?act=warehouses2" method="POST">

<input name="act" type="hidden" value="warehouses2">

<input name="a" type="hidden" value="add_res">

<table>

<tr>

<td>��� ������� �������</td>

<td align=center><img src="_imgs/tz/coins.gif" height=21></td>

<td align=center><img src="_imgs/tz/polymer.gif" height=21></td>

<td align=center><img src="_imgs/tz/organic.gif" height=21></td>

<td align=center><img src="_imgs/tz/venom.gif"></td>

<td align=center><img src="_imgs/tz/rad.gif"></td>

<td align=center><img src="_imgs/tz/gold.gif" height="21"></td>

<td align=center><img src="_imgs/tz/gem.gif" height="21"></td>

<td align=center><img src="_imgs/tz/metal.gif" height="21"></td>

<td align=center><img src="_imgs/tz/silicon.gif" height="21"></td>

<td>�����</td>

</tr>

<tr>

<td><input name="from_name" type="text" value="" size=30></td>

<td><input name="coins" type="text" value="" size=5></td>

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

<tr>

<td colspan=11 align="center"><input type="submit" value="��������"></td>

</tr>

</table>

</form>



</td></tr>

</table>



<?}?>

<table width='100%' border='0' cellspacing='3' cellpadding='2'>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>�������� �������:</strong> <?=($permission?'(<a href="?act=analize">����������� ��������</a>)':'')?></p></td></tr>

<tr><td>



<form action="?act=warehouses2" method="POST">

<input name="a" type="hidden" value="add">

<table>

<tr>

<td width=100>���� ����:<br> <input type=text name="dt" value="<?=($_POST['dt']?$_POST['dt']:date("d.m.y",time()))?>" size=8></td>

<td width=100>������:<br> <select name="do" onchange="javascript: if (document.all('do').value=='1') document.all('nick').innerHTML = '��� ������: '; else document.all('nick').innerHTML = '���� ������: ';">

<option value='1'>��� ����</option>

<option value='2'>��� ���������</option>

</select></td>

<td><div id="nick">��� ������: </div> <input name="name" type="text" value="" size=30></td>

</tr>

<tr>

<td colspan=3>������ �� ������� ����� ���������:</td>

</tr>

<tr>

<td colspan=3><textarea rows=6 name=text cols=100></textarea></td>

</tr>

<tr>

<td colspan=3 align="center"><input type="submit" value="��������"></td>

</tr>

</table>

</form>



</td></tr>

</table>





<table border="0" cellspacing="0" cellpadding="0" width="100%" id="tb_content">

<tr>

<?

$bcount=0;

foreach (array_keys($buildings) as $fid) {

	$bname = str_replace(' ','&nbsp;',$buildings[$fid]);

	$bname = str_replace('-','&#8209;',$bname);

	echo "	<td height=\"20\" class=\"tab-".($bcount?'off':'on')."\" id=\"navcell\" onclick=\"switchCell(".$bcount.")\" valign=\"middle\" nowrap><b>&nbsp;".$bname."&nbsp;</b></td>\n";

	$bcount++;

}

if ($permission) {

	echo "	<td height=\"20\" class=\"tab-off\" id=\"navcell\" onclick=\"switchCell(".$bcount.")\" valign=\"middle\" nowrap><b>&nbsp;������&nbsp;</b></td>\n";

	$bcount++;

}

if (@$_REQUEST['logs']) {

	echo "	<td height=\"20\" class=\"tab-off\" id=\"navcell\" onclick=\"switchCell(".$bcount.")\" valign=\"middle\" nowrap><b>&nbsp;����&nbsp;</b></td>\n";

}

?>

	<td class=tab-none noWrap><FONT face=Tahoma color="#ffffff">&nbsp;</TD>

</tr>

</table>



<?if ($permission) { ?>

<form name="transferts" method="post" action="?act=warehouses2">

<?

}

foreach (array_keys($buildings) as $fid) {

?>

<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0">

<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>���������������� ��������:</strong> </p></td></tr>

<tr><td align="center">



<table width='100%' border='0' cellspacing='3' cellpadding='2'>



<tr><td>



<table width=100%>



<tr bgcolor=#F4ECD4>

<td><b>�������:</b></td>

<td align=center><img src="_imgs/tz/coins.gif" height=21></td>

<td align=center><img src="_imgs/tz/polymer.gif" height=21></td>

<td align=center><img src="_imgs/tz/organic.gif" height=21></td>

<td align=center><img src="_imgs/tz/venom.gif" height="21"></td>

<td align=center><img src="_imgs/tz/rad.gif" height="21"></td>

<td align=center><img src="_imgs/tz/gold.gif" height=21></td>

<td align=center><img src="_imgs/tz/gem.gif" height="21"></td>

<td align=center><img src="_imgs/tz/metal.gif" height=21></td>

<td align=center><img src="_imgs/tz/silicon.gif" height="21"></td>

<td align=center>&nbsp;</td>

</tr>

<tr><td colspan=11 height=4></td></tr>



<?

//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

if ($permission) {



$SQL="SELECT user_id, SUM(coins), SUM(metals), SUM(gold), SUM(polymers), SUM(organic), SUM(venom), SUM(radioactive), SUM(gems), SUM(silicon) FROM warehouses WHERE warehouse='".$fid."' AND status='1' GROUP BY user_id";

$a=mysql_query($SQL);

while($s=mysql_fetch_row($a)) {



$SQL="SELECT id, user_name, clan FROM site_users WHERE id='".$s[0]."'";

$r=mysql_query($SQL);

$d=mysql_fetch_array($r);

?>



<tr bgcolor=#F4ECD4>

<td colspan=11><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><b><?=GetClan($d['clan']).GetUser($d['id'],$d['user_name'],AuthUserGroup)?></b></td>

</tr>



<?

$SQL="SELECT * FROM warehouses WHERE user_id='".$s[0]."' AND warehouse='".$fid."' AND status='1' ORDER BY dt";

$r=mysql_query($SQL);

$np=0;



while($d=mysql_fetch_array($r)) {

if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}



$SQL="SELECT id, user_name, clan FROM site_users WHERE user_name='".$d['from_name']."'";

$res=mysql_query($SQL);

$row=mysql_fetch_array($res);

?>



<tr>

<td <?=$bg?>><?=date("d.m.y H:i",$d['dt'])?> <b><?=GetClan($row['clan']).$d['from_name']?> </b></td>

<td <?=$bg?> align=center><?=$d['coins']?></td>

<td <?=$bg?> align=center><?=$d['polymers']?></td>

<td <?=$bg?> align=center><?=$d['organic']?></td>

<td <?=$bg?> align=center><?=$d['venom']?></td>

<td <?=$bg?> align=center><?=$d['radioactive']?></td>

<td <?=$bg?> align=center><?=$d['gold']?></td>

<td <?=$bg?> align=center><?=$d['gems']?></td>

<td <?=$bg?> align=center><?=$d['metals']?></td>

<td <?=$bg?> align=center><?=$d['silicon']?></td>

<td <?=$bg?> align=center><input name="orderId[]" type="checkbox" value="<?=$d['id']?>">

</td>

</tr>



<?

}

?>



<tr bgcolor=#F4ECD4>

<td><b>�����: </b></td>

<td align=center><?=$s[1]?></td>

<td align=center><?=$s[4]?></td>

<td align=center><?=$s[5]?></td>

<td align=center><?=$s[6]?></td>

<td align=center><?=$s[7]?></td>

<td align=center><?=$s[3]?></td>

<td align=center><?=$s[8]?></td>

<td align=center><?=$s[2]?></td>

<td align=center><?=$s[9]?></td>

<td align=center></td>

</tr>

<tr><td colspan=11 height=4></td></tr>



<?

}



$SQL="SELECT SUM(coins), SUM(metals), SUM(gold), SUM(polymers), SUM(organic), SUM(venom), SUM(radioactive), SUM(gems), SUM(silicon) FROM warehouses WHERE warehouse='".$fid."' AND status='1'";

$a=mysql_query($SQL);

$s=mysql_fetch_row($a);

?>



<tr bgcolor=#F4ECD4>

<td><b>����� �� ������: </b></td>

<td align=center><b><?=$s[0]?></b></td>

<td align=center><b><?=$s[3]?></b></td>

<td align=center><b><?=$s[4]?></b></td>

<td align=center><b><?=$s[5]?></b></td>

<td align=center><b><?=$s[6]?></b></td>

<td align=center><b><?=$s[2]?></b></td>

<td align=center><b><?=$s[7]?></b></td>

<td align=center><b><?=$s[1]?></b></td>

<td align=center><b><?=$s[8]?></b></td>

<td align=center>&nbsp;</td>

</tr>



<?

}

//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

 else {



$SQL="SELECT * FROM warehouses WHERE user_id='".AuthUserId."' AND warehouse='".$fid."' AND status='1' ORDER BY dt";

$r=mysql_query($SQL);

$np=0;



while($d=mysql_fetch_array($r)) {

if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}



$SQL="SELECT id, user_name, clan FROM site_users WHERE user_name='".$d['from_name']."'";

$res=mysql_query($SQL);

$row=mysql_fetch_array($res);

?>



<tr>

<td <?=$bg?>><?=date("d.m.y H:i",$d['dt'])?> <b><?=GetClan($row['clan']).$d['from_name']?></td>

<td <?=$bg?> align=center><?=$d['coins']?></td>

<td <?=$bg?> align=center><?=$d['polymers']?></td>

<td <?=$bg?> align=center><?=$d['organic']?></td>

<td <?=$bg?> align=center><?=$d['venom']?></td>

<td <?=$bg?> align=center><?=$d['radioactive']?></td>

<td <?=$bg?> align=center><?=$d['gold']?></td>

<td <?=$bg?> align=center><?=$d['gems']?></td>

<td <?=$bg?> align=center><?=$d['metals']?></td>

<td <?=$bg?> align=center><?=$d['silicon']?></td>

<td <?=$bg?> align=center><b><a href='#; return false;' onclick="if(confirm('�� �������?')) top.location='?act=warehouses2&cancelID=<?=$d['id']?>'">�������</a></b></td>

</tr>



<?

}



$SQL="SELECT SUM(coins), SUM(metals), SUM(gold), SUM(polymers), SUM(organic), SUM(venom), SUM(radioactive), SUM(gems), SUM(silicon) FROM warehouses WHERE user_id='".AuthUserId."' AND warehouse='".$fid."' AND status='1'";

$a=mysql_query($SQL);

$s=mysql_fetch_row($a);

?>



<tr><td colspan=11 height=4></td></tr>

<tr bgcolor=#F4ECD4>

<td><b>�����: </b></td>

<td align=center><b><?=$s[0]?></b></td>

<td align=center><b><?=$s[3]?></b></td>

<td align=center><b><?=$s[4]?></b></td>

<td align=center><b><?=$s[5]?></b></td>

<td align=center><b><?=$s[6]?></b></td>

<td align=center><b><?=$s[2]?></b></td>

<td align=center><b><?=$s[7]?></b></td>

<td align=center><b><?=$s[1]?></b></td>

<td align=center><b><?=$s[8]?></b></td>

<td align=center><b><a href="?act=warehouses2&logs=<?=AuthUserId?>">����</a></b></td>

</tr>



<?

}

?>



</table><br>



</td></tr>



</table>



</td></tr>



<? if(!($permission)) {

$SQL="SELECT * FROM warehouses WHERE user_id='".AuthUserId."' AND warehouse='".$fid."' AND status='2' ORDER BY dt";

$r=mysql_query($SQL);

if (mysql_num_rows($r)) {

?>

<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>����������� ��������:</strong> </p></td></tr>

<tr><td align="center">



<table width='100%' border='0' cellspacing='3' cellpadding='2'>



<tr><td>



<table width=100%>



<tr bgcolor=#F4ECD4>

<td><b>�������:</b></td>

<td align=center><img src="_imgs/tz/coins.gif" height=21></td>

<td align=center><img src="_imgs/tz/polymer.gif" height=21></td>

<td align=center><img src="_imgs/tz/organic.gif" height=21></td>

<td align=center><img src="_imgs/tz/venom.gif" height="21"></td>

<td align=center><img src="_imgs/tz/rad.gif" height="21"></td>

<td align=center><img src="_imgs/tz/gold.gif" height=21></td>

<td align=center><img src="_imgs/tz/gem.gif" height="21"></td>

<td align=center><img src="_imgs/tz/metal.gif" height=21></td>

<td align=center><img src="_imgs/tz/silicon.gif" height="21"></td>

<td align=center><b>������</b></td>

</tr>

<tr><td colspan=11 height=4></td></tr>



<?

$np=0;



while($d=mysql_fetch_array($r)) {

if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}



$SQL="SELECT id, user_name, clan FROM site_users WHERE user_name='".$d['from_name']."'";

$res=mysql_query($SQL);

$row=mysql_fetch_array($res);

?>



<tr>

<td <?=$bg?>><?=date("d.m.y H:i",$d['dt'])?> <b><?=GetClan($row['clan']).$d['from_name']?></td>

<td <?=$bg?> align=center><?=$d['coins']?></td>

<td <?=$bg?> align=center><?=$d['polymers']?></td>

<td <?=$bg?> align=center><?=$d['organic']?></td>

<td <?=$bg?> align=center><?=$d['venom']?></td>

<td <?=$bg?> align=center><?=$d['radioactive']?></td>

<td <?=$bg?> align=center><?=$d['gold']?></td>

<td <?=$bg?> align=center><?=$d['gems']?></td>

<td <?=$bg?> align=center><?=$d['metals']?></td>

<td <?=$bg?> align=center><?=$d['silicon']?></td>

<td <?=$bg?> align=center><b><a href='#; return false;' onclick="top.location='?act=warehouses2&seenID=<?=$d['id']?>'">���������</a></b></td>

</tr>



<? } ?>

</table>



</td></tr>



</table>



<? } ?>

</td></tr>



<?

if ($build_type[$fid]==1) {

$SQL="SELECT SUM(coins), SUM(metals), SUM(gold), SUM(polymers), SUM(organic), SUM(venom), SUM(radioactive), SUM(gems), SUM(silicon) FROM warehouses WHERE status='0' AND user_id='".AuthUserId."' AND warehouse='".$fid."'";

$r=mysql_query($SQL);

$d=mysql_fetch_row($r);

?>

<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>������ �� �������������� ���������:</strong> </p></td></tr>

<tr><td align="center">



<table width='100%' border='0' cellspacing='3' cellpadding='2'>



<tr><td>



<table width=100%>



<tr bgcolor=#F4ECD4>

<td align=center><img src="_imgs/tz/coins.gif" height=21></td>

<td align=center><img src="_imgs/tz/polymer.gif" height=21></td>

<td align=center><img src="_imgs/tz/organic.gif" height=21></td>

<td align=center><img src="_imgs/tz/venom.gif" height="21"></td>

<td align=center><img src="_imgs/tz/rad.gif" height="21"></td>

<td align=center><img src="_imgs/tz/gold.gif" height=21></td>

<td align=center><img src="_imgs/tz/gem.gif" height="21"></td>

<td align=center><img src="_imgs/tz/metal.gif" height=21></td>

<td align=center><img src="_imgs/tz/silicon.gif" height="21"></td>

</tr>

<tr><td colspan=11 height=4></td></tr>



<tr bgcolor=#F4ECD4>

<td align=center><?=$d[0]?></td>

<td align=center><?=$d[3]?></td>

<td align=center><?=$d[4]?></td>

<td align=center><?=$d[5]?></td>

<td align=center><?=$d[6]?></td>

<td align=center><?=$d[2]?></td>

<td align=center><?=$d[7]?></td>

<td align=center><?=$d[1]?></td>

<td align=center><?=$d[8]?></td>

</tr>



</table>



</td></tr>



</table>



</td></tr>



<?

} }

$SQL="SELECT * FROM warehouses WHERE status='5' AND (user_id='".AuthUserId."' OR from_name='".AuthUserName."') AND warehouse='".$fid."' ORDER BY dt";

$r=mysql_query($SQL);

if (mysql_num_rows($r)) {

?>

<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>�������� ����������:</strong> </p></td></tr>

<tr><td align="center">



<table width='100%' border='0' cellspacing='3' cellpadding='2'>



<tr><td>



<table width=100%>



<tr bgcolor=#F4ECD4>

<td><b>�������:</b></td>

<td align=center><img src="_imgs/tz/coins.gif" height=21></td>

<td align=center><img src="_imgs/tz/polymer.gif" height=21></td>

<td align=center><img src="_imgs/tz/organic.gif" height=21></td>

<td align=center><img src="_imgs/tz/venom.gif" height="21"></td>

<td align=center><img src="_imgs/tz/rad.gif" height="21"></td>

<td align=center><img src="_imgs/tz/gold.gif" height=21></td>

<td align=center><img src="_imgs/tz/gem.gif" height="21"></td>

<td align=center><img src="_imgs/tz/metal.gif" height=21></td>

<td align=center><img src="_imgs/tz/silicon.gif" height="21"></td>

<td align=center><b>������</b></td>

</tr>

<tr><td colspan=11 height=4></td></tr>



<?

$np=0;



while($d=mysql_fetch_array($r)) {

if($np==1) $np=0; else $np=1;



if ($d['user_id'] == AuthUserId) {

	$SQL="SELECT clan FROM site_users WHERE user_name='".$d['from_name']."'";

	$res=mysql_query($SQL);

	$row=mysql_fetch_array($res);

	$nick = "�� <b>".GetClan($row['clan']).$d['from_name']."</b>";

} else {

	$SQL="SELECT user_name, clan FROM site_users WHERE id='".$d['user_id']."'";

	$res=mysql_query($SQL);

	$row=mysql_fetch_array($res);

	$nick = "��� <b>".GetClan($row['clan']).$row['user_name']."</b>";

}



?>

<tr>

<td <?=$bgstr[$np]?>><?=date("d.m.y H:i",$d['dt']).' '.$nick?></td>

<td <?=$bgstr[$np]?> align=center><?=$d['coins']?></td>

<td <?=$bgstr[$np]?> align=center><?=$d['polymers']?></td>

<td <?=$bgstr[$np]?> align=center><?=$d['organic']?></td>

<td <?=$bgstr[$np]?> align=center><?=$d['venom']?></td>

<td <?=$bgstr[$np]?> align=center><?=$d['radioactive']?></td>

<td <?=$bgstr[$np]?> align=center><?=$d['gold']?></td>

<td <?=$bgstr[$np]?> align=center><?=$d['gems']?></td>

<td <?=$bgstr[$np]?> align=center><?=$d['metals']?></td>

<td <?=$bgstr[$np]?> align=center><?=$d['silicon']?></td>

<td <?=$bgstr[$np]?> align=center><b><?if($d['user_id']==AuthUserId){?><a href='#; return false;' onclick="if(confirm('�� �������?')) top.location='?act=warehouses2&helpID=<?=$d['id']?>'">�������</a> | <?}?><a href='#; return false;' onclick="if(confirm('�� �������?')) top.location='?act=warehouses2&cancelID=<?=$d['id']?>'">�������</a></b></td>

</tr>



<? } ?>

</table>



</td></tr>



</table>



</td></tr>



<? } ?>

</table>



<? }

if ($permission) { ?>

  <input type="submit" name="Submit" value="���������">

  <input type="submit" name="Submit" value="�������">

</form>

<? }

//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

if ($permission) {

?>

<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0">

<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>������ ���������<?=(AuthUserGroup == 100?' (<a href="?act=wlogs">��������</a>)':'')?>:</strong> </p></td></tr>

<tr><td align="center">



<table width='100%' border='0' cellspacing='3' cellpadding='2'>

<tr><td>



<table width=100%>



<form method="GET">

<tr bgcolor=#F4ECD4>

<td>

<input name="act" type="hidden" value="warehouses2">

<b>����: </b><select size="1" name="logs" onchange="submit();">

<option value='' selected>-- select user --</option>

<?

$r=mysql_query("SELECT DISTINCT w.user_id, u.user_name FROM warehouses w LEFT JOIN site_users u ON u.id=w.user_id ORDER BY u.user_name");

while($data=mysql_fetch_array($r)) echo "<option value='{$data['user_id']}'>{$data['user_name']}</option>\n";

?>

</select>

</td>

<td align=center><img src="_imgs/tz/coins.gif" height=21></td>

<td align=center><img src="_imgs/tz/polymer.gif" height=21></td>

<td align=center><img src="_imgs/tz/organic.gif" height=21></td>

<td align=center><img src="_imgs/tz/venom.gif" height="21"></td>

<td align=center><img src="_imgs/tz/rad.gif" height="21"></td>

<td align=center><img src="_imgs/tz/gold.gif" height=21></td>

<td align=center><img src="_imgs/tz/gem.gif" height="21"></td>

<td align=center><img src="_imgs/tz/metal.gif" height=21></td>

<td align=center><img src="_imgs/tz/silicon.gif" height="21"></td>

</tr>

</form>



<?

$SQL="SELECT w.user_id, SUM(w.coins), SUM(w.metals), SUM(w.gold), SUM(w.polymers), SUM(w.organic), SUM(w.venom), SUM(w.radioactive), SUM(w.gems), SUM(w.silicon) FROM warehouses w LEFT JOIN site_users s ON s.id=w.user_id WHERE w.status='0' GROUP BY w.user_id ORDER BY s.clan, s.user_name";

$a=mysql_query($SQL);

while($s=mysql_fetch_row($a)) {



$SQL="SELECT id, user_name, clan FROM site_users WHERE id='".$s[0]."'";

$r=mysql_query($SQL);

$d=mysql_fetch_array($r);



if ($s[1]+$s[2]+$s[3]+$s[4]+$s[5]+$s[6]+$s[7]+$s[8]+$s[9]>0) {

?>

<tr><td colspan=11 height=4></td></tr>

<tr bgcolor=#F4ECD4>

<td colspan=11><img src='i/bullet-red-01a.gif' width='18' height='10' hspace='5'><b><?=GetClan($d['clan']).GetUser($d['id'],$d['user_name'],AuthUserGroup)?> (<a href="?act=warehouses2&logs=<?=$s[0]?>">����</a>) <?if (AuthUserGroup == 100) {?>(<a href='direct_call/warehouses_logs.php?nick=<?=$d['user_name']?>' target='wh_editor'>+/-</a>)<?}?></b></td>

</tr>



<?

$SQL="SELECT warehouse, SUM(coins), SUM(metals), SUM(gold), SUM(polymers), SUM(organic), SUM(venom), SUM(radioactive), SUM(gems), SUM(silicon) FROM warehouses WHERE status='0' AND user_id='".$s[0]."' GROUP BY warehouse ORDER BY warehouse";

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



<? } } } } ?>

</table>



</td></tr>

</table>



</td></tr>

</table>



<?

}



if (@$_REQUEST['logs']) {

if ($permission) $uid=$_REQUEST['logs'];

else $uid=AuthUserId;



$SQL="SELECT id, user_name, clan FROM site_users WHERE id='".$uid."'";

$r=mysql_query($SQL);

$d=mysql_fetch_array($r);

?>

<table class="tab-content" id="tb" cellSpacing="0" cellPadding="0" width="100%" border="0">

<tr><td valign="top" align="center"><p style="MARGIN-RIGHT: 10px; MARGIN-LEFT: 10px; MARGIN-TOP: 10px; MARGIN-BOTTOM: 10px;"></td></tr>

<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>���� ��������� <?=GetClan($d['clan']).GetUser($d['id'],$d['user_name'],AuthUserGroup)?>:</strong> </p></td></tr>

<tr><td align="center">



<table width='100%' border='0' cellspacing='3' cellpadding='2'>

<tr><td>



<table width=100%>



<tr bgcolor=#F4ECD4>

<td><b>�������:</b></td>

<td><b>�����</b></td>

<td align=center><img src="_imgs/tz/coins.gif" height=21></td>

<td align=center><img src="_imgs/tz/polymer.gif" height=21></td>

<td align=center><img src="_imgs/tz/organic.gif" height=21></td>

<td align=center><img src="_imgs/tz/venom.gif" height="21"></td>

<td align=center><img src="_imgs/tz/rad.gif" height="21"></td>

<td align=center><img src="_imgs/tz/gold.gif" height=21></td>

<td align=center><img src="_imgs/tz/gem.gif" height="21"></td>

<td align=center><img src="_imgs/tz/metal.gif" height=21></td>

<td align=center><img src="_imgs/tz/silicon.gif" height="21"></td>

<td align=center><b>������</b></td>

</tr>

<tr><td colspan=11 height=4></td></tr>



<?

$SQL="SELECT * FROM warehouses WHERE user_id='".$uid."' AND status<4 ORDER BY dt DESC";

$r=mysql_query($SQL);

$np=0;



while($d=mysql_fetch_array($r)) {

if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}



$SQL="SELECT id, user_name, clan FROM site_users WHERE user_name='".$d['from_name']."'";

$res=mysql_query($SQL);

$row=mysql_fetch_array($res);

?>



<tr>

<td <?=$bg?>><?=date("d.m.y H:i",$d['dt'])?> <b><?=GetClan($row['clan']).$d['from_name']?></td>

<td <?=$bg?>><?=$buildings[$d['warehouse']]?></td>

<td <?=$bg?> align=center><?=$d['coins']?></td>

<td <?=$bg?> align=center><?=$d['polymers']?></td>

<td <?=$bg?> align=center><?=$d['organic']?></td>

<td <?=$bg?> align=center><?=$d['venom']?></td>

<td <?=$bg?> align=center><?=$d['radioactive']?></td>

<td <?=$bg?> align=center><?=$d['gold']?></td>

<td <?=$bg?> align=center><?=$d['gems']?></td>

<td <?=$bg?> align=center><?=$d['metals']?></td>

<td <?=$bg?> align=center><?=$d['silicon']?></td>

<td <?=$bg?> align=center><b>

<?

if ($d['status']==0) echo "������������";

elseif ($d['status']==1) echo "�� ������������";

elseif ($d['status']<=3) echo "���������";

?>

</b></td>

</tr>



<?

}

?>

</table>



</td></tr>

</table>



</td></tr>

</table>



<? } ?>



<script language="Javascript" type="text/javascript">

switchCell(0);

</script>



<?

} else { echo $mess['AccessDenied'];}

?>