<?php
error_reporting(1);
$logstate[0] = '� �������';
$logstate[1] = '� ��������';
$logstate[2] = '����������';
$logstate[3] = '�� ���������';

$bgstr[0] = 'background="i/bgr-grid-sand.gif"';
$bgstr[1] = 'background="i/bgr-grid-sand1.gif"';


if(abs(AccessLevel) & AccessOP) {

	$_REQUEST['nick'] = trim($_REQUEST['nick']);
	$_POST['nickname'] = trim($_POST['nickname']);

echo "
<h1>������ ��� ������ ��������</h1>
<script language='JavaScript'>;
<!--
function nck(vl){
 window.clipboardData.setData('Text',vl);
}
-->
</script>
<table width='100%' border='0' cellspacing='3' cellpadding='2'>
 <tr>
 	<td height='20' background='i/bgr-grid-sand.gif'>
 	<p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'>
 	<strong><a href='/?act=prokachki2&d=edit'>��������� �����</a></strong> </p></td></tr>
";

//==============================================
	if ($_REQUEST['d']=='edit') {

		if (isset($_POST['text']) && isset($_POST['nickname']) && $_POST['nickname'] == '') {
			echo '<H3><FONT color="red">��������� ���� "������ �����" � "��� ��������������"</FONT></H3><BR>';
			$logs_list = $_POST['text'];
			$nickname = $_POST['nickname'];

		} elseif($_POST['text'] && $_POST['nickname'] != ''){
			$priority = $_POST['donow'];
			$neednick = addslashes($_POST['nickname']);
			if ($priority > 1) { $priority = 1; }
			if ($priority < 0) { $priority = 0; }
			$lines = explode("\n", $_POST['text']);

			foreach ($lines as $line) {
				$line = trim($line);
				if($line != ''){
					if (preg_match ("/^(\d\d\.\d\d\.\d\d) (\d\d:\d\d) �� (.+)\. �������� �����:(.+)$/iU", $line, $regs)) {
						$logId = substr(trim($regs[3]), -12);
						$SQL = 'INSERT DELAYED INTO `import_info2` SET `InsertTime` = NOW(), `InsertBy`='.intval(AuthUserId).', `LogTime` = \''.trim($regs[2]).':00\', `LogID` = \''.$logId.'\', `status` = 0, `doitnow` = '.$priority.', `nick` = \''.mysql_escape_string($neednick).'\', `exp` = '.intval($regs[4]).';';
						mysql_query($SQL) or die(mysql_error());
					}
				}
			}
		}

		if ($_REQUEST['restart']) {
			mysql_query('UPDATE `import_info2` SET `status`=\'0\' WHERE `status`=\'1\'');
			echo '<font color=green><b>������ �����������</b></font><br><br>';
		}

		if ($_REQUEST['redownload']) {
			mysql_query('UPDATE `import_info2` SET `status`=\'0\' WHERE `status`=\'3\'');
			echo '<font color=green><b>������� �������� ����</b></font><br><br>';
		}

		if ($_REQUEST['delete']) {
			mysql_query('DELETE FROM `import_info2` WHERE `status`=\'3\'');
			echo '<font color=green><b>���� �������</b></font><br><br>';
		}

		if ($_REQUEST['del']) {
			mysql_query('DELETE FROM `import_info2` WHERE `id`=\''.abs($_REQUEST['del']).'\' AND `status`!=\'2\'');
		}


echo <<<HTML

		<a href="?act=prokachki2&d=edit&restart=1">�������������</a> |
		<a href="?act=prokachki2&d=edit&redownload=1">���������� ��������� �������������</a> |
		<a href="?act=prokachki2&d=edit&delete=1">������� �������������</a>

		<tr><td align=center>

		<form method="POST" name=F1>
		<textarea rows=6 name=text cols=110>$logs_list</textarea>
		<br>��� ��������������: <input type="text" name="nickname" VALUE="$nickname"><br>
		<br>
		  <select name="donow" size="1">
		    <option value="0" selected>������� ��������</option>
		    <option value="1">������������ ��������</option>
		  </select>
		<input type="submit" value="��������">
		</form>

HTML;
		$np=0;
		$SQL = 'SELECT * FROM `import_info2` WHERE `status`!=\'2\'';
		$r = mysql_query($SQL);
		$cops_name = array();
		if ($_REQUEST['queue'] == '1') {
			echo "
			<table width=450>
			<tr align=center bgcolor=#F4ECD4>
				<td><b>���������</b></td>
				<td><b>�������</b></td>
				<td><b>���</b></td>
				<td><b>������</b></td>
			</tr>
			";
			while ($d = mysql_fetch_assoc($r)) {
				if($np==1) {
					$bg=$bgstr[0];
					$np=0;
				} else {
					$bg=$bgstr[1];
					$np=1;
				}
				if (array_key_exists($d['InsertBy'], $cops_name)) {
				} else {
					$n = mysql_fetch_assoc(mysql_query('SELECT `user_name` FROM `site_users` WHERE `id`=\''.$d['InsertBy'].'\''));
					$cops_name[$d['InsertBy']] = $n['user_name'];
				}
				echo " <tr>\n";
				echo '  <td '.$bg.'>'.$d['InsertTime']."</td>\n";
				echo '  <td '.$bg.'>'.$cops_name[$d['InsertBy']]."</td>\n";
				echo '  <td '.$bg.'>'.$d['LogTime'].' <b>'.$d['LogID']."</b></td>\n";
				echo '  <td '.$bg.' align=center>'.$d['status'].' [<a href="?act=prokachki2&d=edit&del='.$d['id']."\">X</a>]</td>\n";
				echo " </tr>\n";
			}

			echo "</table>\n";

		} else {

			echo '<div align="left"><a href="?act=prokachki2&d=edit&queue=1">����������� ��������� �������</a></div><br>';
			$dolist = 0;
			while ($d=mysql_fetch_assoc($r)) {

				if (array_key_exists($d['InsertBy'], $cops_name)) {
				} else {
					$n = mysql_fetch_assoc(mysql_query('SELECT `user_name` FROM `site_users` WHERE `id`=\''.$d['InsertBy'].'\''));
					$cops_name[$d['InsertBy']] = $n['user_name'];
				}
				$from_usr[$cops_name[$d['InsertBy']]][$d['InsertTime']][$d['status']]++;
				$dolist = 1;
			}
			if ($dolist) {
echo <<<HTML
		       <table width=450>
					<tr align=center bgcolor=#F4ECD4>
					<td><b>���������</b></td>
					<td><b>�������</b></td>
					<td><b>������</b></td>
					<td><b>���-��</b></td>
		        </tr>
HTML;
				foreach ($from_usr as $usr => $v1) {
					foreach ($v1 as $intime => $v2) {
						foreach ($v2 as $sts => $quan) {
							if($np==1) {
								$bg=$bgstr[0]; $np=0;
							} else {
								$bg=$bgstr[1]; $np=1;
							}
							echo " <tr>\n";
							echo '  <td '.$bg.'>'.$intime."</td>\n";
							echo '  <td '.$bg.'>'.$usr."</td>\n";
							echo '  <td '.$bg.'>'.$logstate[$sts]."</td>\n";
							echo '  <td '.$bg.'>'.$quan."</td>\n";
							echo ' </tr>';
						}
					}
				}
				echo "</table>";
			}
		}
	}

	echo "
	</td>
	</tr>
	<tr>
		<td height='20' background='i/bgr-grid-sand.gif'>
		<p>
			<img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'>
			<strong><a href='/?act=prokachki2&d=find'>����� �� ������������</a></strong>
		</p>
	</td>
	</tr>
	";

	if($_REQUEST['d']=='find') {

		$sdate = ($_REQUEST['sdate']?$_REQUEST['sdate']:gmdate("d.m.Y",time()-10368000));
    	$fdate = ($_REQUEST['fdate']?$_REQUEST['fdate']:gmdate("d.m.Y",time()));
        $statuslist = array('�������������','��� ���������','��������');

		echo "


		<tr><td align=center>

		<form method=GET action='index.php'>
		<input name='act' type='hidden' value='prokachki2'>
		<input name='d' type='hidden' value='find'>
		<input name='a' type='hidden' value='pgroup'>

		<table cellspacing=5 cellpadding=0>
			<tr bgcolor=#F4ECD4>
				<td align=center>� ����</td>
				<td align=center>�� ����</td>
				<td align=center>��� ���������</td>
				<td align=center>�������</td>
				<td align=center>����������</td>
				<td align=center>�����������</td>
				<td align=center>������</td>
			</tr>
			<tr>
				<th background='i/bgr-grid-sand.gif'>
					<input type=text name=sdate value='$sdate' size=11 maxlength=10></th>
				<th background='i/bgr-grid-sand.gif'>
					<input type=text name=fdate value='$fdate' size=11 maxlength=10></th>
				<th background='i/bgr-grid-sand.gif'>
					<input type=text name=nick value='".$_REQUEST['nick']."' size=42 maxlength=20></th>
				<th background='i/bgr-grid-sand.gif'>
					<input type=text name=location value='".$_REQUEST['location']."' size=9 maxlength=9></th>
				<th background='i/bgr-grid-sand.gif' align=center>
					<input type=text name=many value='".$_REQUEST['many']."' size=5 maxlength=3></th>
				<th background='i/bgr-grid-sand.gif'>
					<input type=text name=cheat value='".$_REQUEST['cheat']."' size=9 maxlength=9></th>
				<th background='i/bgr-grid-sand.gif'>
					<select name=status>";
		foreach($statuslist as $s => $status) {			$sel = ($_REQUEST['status'] == $s)?' SELECTED':'';			echo "<option value='$s'$sel>$status</option>
			";
		}

		echo "
					</select>
				</th>
			</tr>
		</table>
		<input type='submit' value='��������� �����'>
		</form>

		</center>
		</td></tr>
		</table>
		";

		if ($_REQUEST['a']=='pgroup') {

	echo <<<HTML

			<script>

			var green_logs = Array();
			var red_logs = Array();

			function CheckLog(a,b,c) {
				if (a) {
					green_logs[b] = c;
					red_logs[b] = 0;
				} else {
					green_logs[b] = 0;
					red_logs[b] = c;
				}
			}

			function MarkLogs() {
				document.all("glogs").value = green_logs;
				document.all("rlogs").value = red_logs;
				document.all("MLogs").submit();
			}

			function show(name) {
				document.getElementById(name).style.display = "";
			}

			function showhide(name) {
				if (document.getElementById(name).style.display == "none") {					document.getElementById(name).style.display = "";
				} else {					document.getElementById(name).style.display = "none";
				}
			}

			</script>

			<table width='100%' border='0' cellspacing='3' cellpadding='2'><tr>

			<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>������� �����</strong> <a onclick="showhide('fastfind'); return false;" href="#">[������/��������]</a></p></td>

			</tr></table>

			<div id="fastfind" align="center">
			<iframe src="direct_call/prokachki2_logs.php" name="logs" width="750" marginwidth="0" height="250" marginheight="0" align="middle" scrolling="auto"><br>
			</iframe>
			</div>


			<table width='100%' border='0' cellspacing='3' cellpadding='2'><tr>

			<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>���������� ������</strong> <a onclick="showhide('peopl'); return false;" href="#">[������/�������� ��� �������]</a></p></td>

			</tr><tr><td align=center>

HTML;

			if ($_REQUEST['glogs'] || $_REQUEST['rlogs']) {

				$redlogs = explode(',', $_REQUEST['rlogs']);
				foreach ($redlogs as $redlog) {
					if ($redlog) {
						$SQL = 'UPDATE `battles2` SET `Status`=\'2\' WHERE `TeamsKey`=\''.$redlog.'\' AND Status=\''.(($_REQUEST['status']) ? $_REQUEST['status'] : '0').'\'';
						#echo "1 $SQL<hr>";
						mysql_query($SQL);
					}
				}

				$greenlogs = explode(',', $_REQUEST['glogs']);
				foreach ($greenlogs as $greenlog) {
					if ($greenlog) {
						$SQL = 'UPDATE `battles2` SET `Status`=\'1\' WHERE `TeamsKey`=\''.$greenlog.'\' AND Status=\''.(($_REQUEST['status']) ? $_REQUEST['status'] : '0').'\'';
						#echo "2 $SQL<hr>";
						mysql_query($SQL);
					}
				}
			}

			$Cond = array();
			$Cond[] = 'B.BattleDate>=\''.substr($_REQUEST['sdate'],6,4).'-'.substr($_REQUEST['sdate'],3,2).'-'.substr($_REQUEST['sdate'],0,2).'\'';
			$Cond[] = 'B.BattleDate<=\''.substr($_REQUEST['fdate'],6,4).'-'.substr($_REQUEST['fdate'],3,2).'-'.substr($_REQUEST['fdate'],0,2).'\'';

			if ($_REQUEST['nick']) { $Cond[] = 'BL.login = \''.$_REQUEST['nick'].'\''; }
			if ($_REQUEST['location']) {
				$Lct = explode(',', $_REQUEST['location']);
				for ($i=0; $i<2; $i++) {
					if ($Lct[$i] < 0) {
						$Lct[$i] += 360;
					}
				}
				$Lct = implode(',', $Lct);
				$Cond[] = 'B.Location=\''.$Lct.'\'';
			}

			if ($_REQUEST['cheat'] > 0) { $Cond[] = 'B.Cheat>=\''.$_REQUEST['cheat'].'\''; }
			if ($_REQUEST['cheat'] < 0) { $Cond[] = 'B.Cheat<=\''.$_REQUEST['cheat'].'\''; }
			if ($_REQUEST['status']) { $Cond[] = 'B.Status=\''.$_REQUEST['status'].'\''; } else { $Cond[] = 'B.Status=\'0\''; }
			if ($_REQUEST['many']) { $having = ' HAVING Many>='.$_REQUEST['many']; }

			$query = 'SELECT B.TeamsKey AS `TeamsKey`, BL.ranks as vranks FROM `battles2` B'.($_REQUEST['nick']?' INNER JOIN battle_logins2 BL ON B.id=BL.battleid':'').' WHERE '.implode(' AND ', $Cond).'';
            $result = @mysql_query($query);
            while($r = @mysql_fetch_row($result)) {            	if($r[1] > 0) {
            		$punishable_ranks[$r[0]]['rps'] += $r[1];
            		$punishable_ranks[$r[0]]['cnt']++;
            	}
            }
            #print_r($punishable_ranks);
			$SQL = 'SELECT B.TeamsKey AS `TeamsKey`, B.Teams AS `Teams`, COUNT(*) as `Many`, B.BattleDate AS `data`, B.addby AS `addby`, `addwhen` AS `addwhen` FROM `battles2` B'.($_REQUEST['nick']?' INNER JOIN battle_logins2 BL ON B.id=BL.battleid':'').' WHERE '.implode(' AND ', $Cond).' GROUP BY B.TeamsKey'.$having.' ORDER BY Many DESC';

			$result = @mysql_query($SQL);
			$LinesCount = mysql_num_rows($result);

			if ($LinesCount) {

				$LinesByPage = 25;
				$PagesCount = round($LinesCount/$LinesByPage+0.49);

				$toprint = "<table width=100%>\n";
				$toprint .= " <tr bgcolor=#F4ECD4>\n";
				$toprint .= "  <td width=30><b>�</b></td>\n";
				$toprint .= "  <td><table width=100%><tr><td><b>������</b></td><td align=right>\n";
				$toprint .= "  </td></tr></table></td>\n";
				$toprint .= "  <td width=50 align=center><b>���-��</b></td>";
				$toprint .= "  <td width=50 align=center><b>��������</b></td>";
				if($_REQUEST['status'] == 2) $toprint .= "  <td width=50 align=center><b>� ������</b></td>";
				$toprint .= "  <td align=center><input type=radio name=selallrad id=selallradg style='background-color: green' onclick=\"checkAll()\"><input type=radio name=selallrad id=selallradr style='background-color: red' onclick=\"checkAll()\"></td></tr>\n";

				$LineNumber = ($_REQUEST['p']?($_REQUEST['p']-1)*$LinesByPage:0);
				$LineNumber_BEGIN = $LineNumber+1;

				$EndLineNumber = ($_REQUEST['p']?$_REQUEST['p']*$LinesByPage:$LinesByPage);

				mysql_data_seek($result, $LineNumber);

				$adders = Array();
				$JAVA_Temp = array();
				$t_count_f = 0;

				while (($row = mysql_fetch_row($result)) && $LineNumber < $EndLineNumber) {
					if (!in_array($row[4].'|'.$row[5], $adders)) {
						$adders[] = $row[4].'|'.$row[5];
						$mind[$row[4].'|'.$row[5]] = $row[3];
						$maxd[$row[4].'|'.$row[5]] = $row[3];
					} else {
						if ($row[3]<$mind[$row[4].'|'.$row[5]])
							$mind[$row[4].'|'.$row[5]] = $row[3];
						if ($row[3]>$mind[$row[4].'|'.$row[5]])
							$maxd[$row[4].'|'.$row[5]] = $row[3];
					}

					$LineNumber++;
                    $findranks = $punishable_ranks[$row[0]]['rps'];
                    $findrankscount = $punishable_ranks[$row[0]]['cnt'];
                    if(ceil($findranks) == $findranks) $findranks = "$findranks.00";

                    $decreaseranks = $findrankscount*2;

					$toprint .= " <tr>\n";
					$toprint .= '  <td'.($LineNumber%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'").'>'.$LineNumber."</td>\n";
					$toprint .= '  <td'.($LineNumber%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'").'>'.Sostav($row[1])." [<a href='direct_call/prokachki2_logs.php?d=find&a=pdetail&key=".$row[0]."&sdate=".$_REQUEST['sdate']."&fdate=".$_REQUEST['fdate']."&nick=".$_REQUEST['nick']."&location=".$_REQUEST['location'].'&many='.$_REQUEST['many'].'&cheat='.$_REQUEST['cheat'].'&status='.$_REQUEST['status']."' target='logs'>������ �����</a>]</td>\n";
					$toprint .= '  <td'.($LineNumber%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'")." align=center><a href='?act=prokachki2&d=find&a=pdetail&key=".$row[0]."&sdate=".$_REQUEST['sdate']."&fdate=".$_REQUEST['fdate']."&nick=".$_REQUEST['nick']."&location=".$_REQUEST['location'].'&many='.$_REQUEST['many'].'&cheat='.$_REQUEST['cheat'].'&status='.$_REQUEST['status']."'>[ <FONT COLOR=\"black\">".$row[2]."</FONT> ]</a></td>\n";
					$toprint .= '  <td'.($LineNumber%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'")." align=center>".$findrankscount." (".$findranks.")</td>\n";
					if($_REQUEST['status'] == 2){  $toprint .= '  <td'.($LineNumber%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'")." align=center>".$decreaseranks."</td>\n"; }
					$toprint .= '  <td'.($LineNumber%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'")." align=center><input type=radio id=l".$LineNumber."g name=l".$LineNumber." style='background-color: green' onclick=\"CheckLog(1,".$LineNumber.",'".$row[0]."')\"><input type=radio id=l".$LineNumber."r name=l".$LineNumber." style='background-color: red' onclick=\"CheckLog(0,".$LineNumber.",'".$row[0]."')\"></td>\n";
					$toprint .= " </tr>\n";

					$JAVA_Temp[$LineNumber] = $row[0];
					$t_count_f = $t_count_f + (int)$row[2];
					$t_ranked_f += $findranks;
					$t_rankedcount_f += $findrankscount;
					$t_rankeddecrease_f += $decreaseranks;
				}
                if(ceil($t_ranked_f) == $t_ranked_f) $t_ranked_f = "$t_ranked_f.00";
				$key_string='';
				$i=0;
				foreach ($JAVA_Temp AS $val){
					$key_string .= '&key['.$i.']='.$val;
					$i++;
				}
				$toprint .= " <tr>\n";
				$toprint .= '  <td colspan=2 align=center>';
				if($_REQUEST['status']=='2'){
					$toprint .= "<a href='direct_call/prokachki2_logs2.php?d=find&a=pdetail".$key_string."&sdate=".$_REQUEST['sdate']."&fdate=".$_REQUEST['fdate']."&nick=".$_REQUEST['nick']."&location=".$_REQUEST['location'].'&many='.$_REQUEST['many'].'&cheat='.$_REQUEST['cheat'].'&status='.$_REQUEST['status']."' target='logs'>����� ������ ����� �� ��������</a> | <a href='direct_call/prokachki2_logs3.php?d=find&a=pdetail".$key_string."&sdate=".$_REQUEST['sdate']."&fdate=".$_REQUEST['fdate']."&nick=".$_REQUEST['nick']."&location=".$_REQUEST['location'].'&many='.$_REQUEST['many'].'&cheat='.$_REQUEST['cheat'].'&status='.$_REQUEST['status']."' target='logs'>��������� ���������</a>";
				}
				$toprint .= "</td>\n";
				$toprint .= "  <td align=center><b>".$t_count_f."</b></td>\n";
				$toprint .= "  <td align=center><b>$t_rankedcount_f (".$t_ranked_f.")</b></td>\n";
				$toprint .= "  <td align=center><b>$t_rankeddecrease_f</b></td>\n";
				$toprint .= "  <td align=center><input type=button value=�������� onclick=\"MarkLogs()\"></td>\n";
				$toprint .= " </tr>\n";
				$toprint .= "</table>\n";

	//� ������ ������� ����������
				$pages=ceil($LinesCount/25);
				if($_REQUEST['p']>0) $p=$_REQUEST['p'];
				else $p=1;
				echo '<div id="peopl" align="center" style="display: none;">'."\n";
				echo "<br><p align=right>��������: <b>";
				ShowPages($p,$pages,5,'act=prokachki2&d=find&a=pgroup&sdate='.$_REQUEST['sdate'].'&fdate='.$_REQUEST['fdate'].'&nick='.$_REQUEST['nick'].'&location='.$_REQUEST['location'].'&many='.$_REQUEST['many'].'&cheat='.$_REQUEST['cheat'].'&status='.$_REQUEST['status']);
				echo "</b></p>";
				echo "<table width=40%>\n";
				echo " <tr bgcolor=#F4ECD4>\n";
				echo "  <td width=150><b>����� �������</b></td>\n";
				echo "  <td width=100><b>� ���</b></td>\n";
				echo "  <td width=150><b>��</b></td>\n";
				echo "  <td width=150><b>��</b></td>\n";
				echo " </tr>\n";

				foreach ($adders as $value) {

					$aaa = explode('|', $value);
					$sql = 'SELECT `user_name` FROM `site_users` WHERE `id`='.$aaa[0];
					$res = mysql_query($sql);
					$row = mysql_fetch_array($res);

					echo " <tr bgcolor=#F4ECD4>\n";
					if ($aaa[1] == '0000-00-00')
						echo "  <td width=100><b>-</b></td>\n";
					else
						echo '  <td width=150><b>'.$aaa[1]."</b></td>\n";
					echo '  <td width=100><b>'.$row[0]."</b></td>\n";
					echo '  <td width=150><b>'.$mind[$value]."</b></td>\n";
					echo '  <td width=150><b>'.$maxd[$value]."</b></td>\n";
					echo " </tr>\n";
				}
				echo "</table>\n";
				echo "</div>\n";
				echo $toprint;

				echo "
				<script>
				function checkAll()	{
					var trigg = 1;
					var trigr = 0;
					if (document.getElementById('selallradg').checked == 1) { trigg=1; trigr=0; }
					if (document.getElementById('selallradr').checked == 1) { trigg=0; trigr=1; }

				";
				for ($i = $LineNumber_BEGIN; $i <= $LineNumber; $i++) {					echo 'CheckLog(trigg,\''.$i.'\',\''.$JAVA_Temp[$i]."');\n";
				}
				echo "

				    for (var i=$LineNumber_BEGIN;i<=$LineNumber;i++) {
				    	document.getElementById ('l' + i + 'g').checked = trigg;
						document.getElementById ('l' + i + 'r').checked = trigr;
					}
				}

				</script>
				";
				echo '<form name="MLogs" action="?act=prokachki2&d=find&a=pgroup&sdate='.$_REQUEST['sdate'].'&fdate='.$_REQUEST['fdate'].'&nick='.$_REQUEST['nick'].'&location='.$_REQUEST['location'].'&many='.$_REQUEST['many'].'&cheat='.$_REQUEST['cheat'].'&status='.$_REQUEST['status']."\" method=POST>\n";
				echo "<input type=hidden name=glogs>\n";
				echo "<input type=hidden name=rlogs>\n";
				echo "</form>\n";

			} else {
				echo "������ �� �������\n";
			}
				echo "

				</center>
				</td></tr>
				</table>

				";
		}

		if ($_REQUEST['a']=="pdetail") {
echo <<<HTML


			<script>
			var green_logs = Array();
			var red_logs = Array();

			function CheckLog(a,b,c) {
			        if (a) {
			                green_logs[b] = c;
			                red_logs[b] = 0;
			        } else {
			                green_logs[b] = 0;
			                red_logs[b] = c;
			        }
			}

			function MarkLogs() {
			        document.all("glogs").value = green_logs;
			        document.all("rlogs").value = red_logs;
			        document.all("MLogs").submit();
			}

			</script>

			<table width='100%' border='0' cellspacing='3' cellpadding='2'><tr>
			 <td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>���������� ������</strong> </p></td>
			 </tr><tr><td align=center>

HTML;

	        if ($_REQUEST['glogs'] || $_REQUEST['rlogs']) {
	                $redlogs = explode(',', $_REQUEST['rlogs']);
	                foreach ($redlogs as $redlog) {
	                        if ($redlog) {
	                                $SQL = 'UPDATE `battles2` SET `Status`=\'2\' WHERE `id`=\''.$redlog.'\'';
	                                mysql_query($SQL);
	                        }
	                }
	                $greenlogs = explode(',', $_REQUEST['glogs']);
	                foreach ($greenlogs as $greenlog) {
	                        if ($greenlog) {
	                                $SQL = 'UPDATE `battles2` SET `Status`=\'1\' WHERE `id`=\''.$greenlog.'\'';
	                                mysql_query($SQL);
	                        }
	                }
	        }

	        $Cond = array();
	        $Cond[] = 'B.BattleDate>=\''.substr($_REQUEST['sdate'],6,4).'-'.substr($_REQUEST['sdate'],3,2).'-'.substr($_REQUEST['sdate'],0,2).'\'';
	        $Cond[] = 'B.BattleDate<=\''.substr($_REQUEST['fdate'],6,4).'-'.substr($_REQUEST['fdate'],3,2).'-'.substr($_REQUEST['fdate'],0,2).'\'';
	        if ($_REQUEST['nick']) { $Cond[] = 'BL.login = \''.$_REQUEST['nick'].'\''; }
	        if ($_REQUEST['location']) {
	                $Lct = explode(',', $_REQUEST['location']);
	                for ($i=0; $i<2; $i++) {
	                        if ($Lct[$i] < 0) {
	                                $Lct[$i] += 360;
	                        }
	                }
	                $Lct = implode(',', $Lct);
	                $Cond[] = 'B.Location=\''.$Lct.'\'';
	        }
	        $Cond[] = 'B.TeamsKey=\''.$_REQUEST['key'].'\'';
	        if ($_REQUEST['cheat'] > 0) { $Cond[] = 'B.Cheat>='.$_REQUEST['cheat'].''; }
	        if ($_REQUEST['cheat'] < 0) { $Cond[] = 'B.Cheat<='.$_REQUEST['cheat'].''; }
	        if ($_REQUEST['status']) { $Cond[] = 'B.Status=\''.$_REQUEST['status'].'\''; } else { $Cond[] = 'B.Status=\'0\''; }
	        $SQL = 'SELECT B.BattleDate AS `BattleDate`, B.BattleTime AS `BattleTime`, B.BattleID AS `BattleID`, B.Teams AS `Teams`, B.Location AS `Location`, B.id AS `Bid`, B.Status AS `Status` FROM `battles2` B'.($_REQUEST['nick']?' INNER JOIN `battle_logins2` BL ON B.id=BL.battleid':'').' WHERE '.implode(' AND ', $Cond).' ORDER BY B.BattleDate DESC, B.BattleTime DESC';
	        #echo $SQL;
	        $result = @mysql_query($SQL);
	        $LinesCount = mysql_num_rows($result);
	        if ($LinesCount) {
	                $LinesByPage = 25;
	                $PagesCount = round($LinesCount/$LinesByPage+0.49);


					$pages=ceil($LinesCount/25);
					if($_REQUEST['p']>0) $p=$_REQUEST['p'];
					else $p=1;
					echo '<br><p align=right>��������: <b>';
					ShowPages($p,$pages,5,'act=prokachki2&d=find&a=pdetail&key='.$_REQUEST['key'].'&sdate='.$_REQUEST['sdate'].'&fdate='.$_REQUEST['fdate'].'&location='.$_REQUEST['location'].'&many='.$_REQUEST['many'].'&cheat='.$_REQUEST['cheat'].'&status='.$_REQUEST['status']);
					echo '</b></p>';


	                echo "<table width=100% cellspacing=0 cellpadding=5>\n";
	                echo "        <tr bgcolor=#F4ECD4>\n";
	                echo "                <td width=20><b>�</b></td>\n";
	                echo "                <td width=70><b>����</b></td>\n";
	                echo "                <td width=55><b>�����</b></td>\n";
	                echo "                <td width=55><b>�������</b></td>\n";
	                echo "                <td><table width=100%><tr><td><b>������</b></td><td align=right>\n";
	                echo "                </td></tr></table></td>\n";
	                echo "                <td width=80 align=center><b>���</b></td><td></td></tr>\n";
	                $LineNumber = ($_REQUEST['p']?($_REQUEST['p']-1)*$LinesByPage:0);
	                $EndLineNumber = ($_REQUEST['p']?$_REQUEST['p']*$LinesByPage:$LinesByPage);
	                mysql_data_seek($result, $LineNumber);
	                while (($row = mysql_fetch_row($result)) && $LineNumber < $EndLineNumber) {
	                        $LineNumber++;
	                        $Lct = explode(',', $row[4]);
	                        for ($i=0; $i<2; $i++) {
	                                if ($Lct[$i] > 180) {
	                                        $Lct[$i] -= 360;
	                                }
	                        }
	                        $Lct = implode('/', $Lct);
	                        echo "        <tr>\n";
	                        echo '                <td style="border-bottom: 3px black solid;" '.($LineNumber%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'").'>'.$LineNumber."</td>\n";
	                        echo '                <td style="border-bottom: 3px black solid;"'.($LineNumber%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'").'>'.substr($row[0],8,2).".".substr($row[0],5,2).".".substr($row[0],0,4)."</td>\n";
	                        echo '                <td style="border-bottom: 3px black solid;"'.($LineNumber%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'").'>'.$row[1]."</td>\n";
	                        echo '                <td style="border-bottom: 3px black solid;"'.($LineNumber%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'").' align=center>'.$Lct."</td>\n";

	                        echo '                <td style="border-bottom: 3px black solid;"'.($LineNumber%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'").'>'.Teams($row[3], $row[2])."</td>\n";
	                        echo '                <td style="border-bottom: 3px black solid;"'.($LineNumber%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'")." align=center><A HREF=\"javascript:{}\"><IMG SRC=\"http://www.tzpolice.ru/i/bullet-red-01a.gif\" BORDER=0 width=\"18\" height=\"11\" OnClick=\"nck('".$row[2]."');;\" ALT=\"����������� ����� ���� � ����� ������\"></A><a target=_blank href='http://www.timezero.ru/sbtl.swf?language=ru&battleid=".$row[2]."'>".$row[2]."</a></td>\n";
	                        echo '                <td style="border-bottom: 3px black solid;"'.($LineNumber%2==0?" background='i/bgr-grid-sand.gif'":" background='i/bgr-grid-sand1.gif'").' align=center><input type=radio name=l'.$LineNumber." style='background-color: green' onclick=\"CheckLog(1,".$LineNumber.','.$row[5].')"'.($row[6]==1?' checked':'')."><input type=radio name=l".$LineNumber." style='background-color: red' onclick=\"CheckLog(0,".$LineNumber.",".$row[5].")\"".($row[6]==2?' checked':'')."></td>\n";
	                        echo "        </tr>\n";

	                }

	                echo "<tr><td colspan=7 align=right><input type=button value=�������� onclick=\"MarkLogs()\"></td></tr>\n";
	                echo "</table>\n";

					echo "<form name=\"MLogs\" action=\"?act=prokachki2&d=find&a=pdetail&key=".$_REQUEST['key']."&sdate=".$_REQUEST['sdate']."&fdate=".$_REQUEST['fdate']."&nick=".$_REQUEST['nick']."&location=".$_REQUEST['location'].'&many='.$_REQUEST['many'].'&cheat='.$_REQUEST['cheat'].'&status='.$_REQUEST['status']."\" method=POST>\n";
					echo "<input type=hidden name=glogs>\n";
					echo "<input type=hidden name=rlogs>\n";
					echo "</form>\n";

	        } else {
	                echo "������ �� �������\n";
	        }


			echo "

			</center>
			</td></tr>

			";
	 	}

	}

	echo "</table>";


} else echo $mess['AccessDenied'];


function Sostav($Line) {
    #$chkUser = $_REQUEST['nick'];
	$Users = explode(';', $Line);
	$Line = '';
	foreach ($Users as $User) {
		if ($User) {
			$Tmp = explode(',', $User);
            #$ur = ($chkUser && $chkUser == $Tmp[0])?'[<b>'.$Tmp[0].'</b> ����-�������]':'';
			$Line .= '<img src="http://www.timezero.ru/i/clans/'.($Tmp[6]?rawurlencode($Tmp[6]):'0').'.gif"><b>'.$Tmp[0].'</b> ['.$Tmp[1].'], ';
		}
	}

	return $Line;
}

function Teams($Line, $battle_id) {

	$Team = array();
	$Users = explode(';', $Line);
	$MaxTeam = 0;

	foreach ($Users as $User) {
		if ($User) {
			$Details = explode(',', $User);
			if ($Details[2] > $MaxTeam) {
				$MaxTeam = $Details[2];
			}

			//	$sql2 = 'SELECT `exp` FROM `import_info2` WHERE `LogID`="'.$row[2].'" AND `nick` = ""';
			$sql2 = 'SELECT `exp` FROM `import_info2` WHERE `LogID`="'.$battle_id.'" AND `nick` = "'.$Details[0].'"';
			$res2 = mysql_query($sql2);
			if(mysql_num_rows($res2)>0){
				$row2 = mysql_fetch_assoc($res2);
				$expa = $row2['exp'];
			} else {
				$expa = 'n/a';
			}
			$Eranks = "";
			if($Details[8]) {
				$rankinfo = explode('&', $Details[8]);

				$ranks = explode('|', $rankinfo[0]);
				foreach($ranks as $k => $v) {

                	$v = explode(":",$v);
                	if($v[1] < 0.001) continue;
                	$v[2] = str_replace("/",", ",$v[2]);
                	$Eranks .= "(<b>".$v[0]."</b> ���, ���� '<b>".$v[2]."</b>' �������� ������ <b>".sprintf("%02.2f",$v[1])."</b>),";

				}
				$Eranks = rtrim($Eranks,",")." ����� ������: <b style='color:navy'>".sprintf("%02.2f",$rankinfo[1])."</b>, ";
			} else {
				$Eranks = '';
			}

			$Team[$Details[2]][] = '<img src="http://www.timezero.ru/i/clans/'.($Details[6]?rawurlencode($Details[6]):'0').'.gif"><b>'.$Details[0].'</b> ['.$Details[1].']'.($Details[7]?' ('.$Details[7].')':'').($Details[3]?', �������� (<font color=brown>��� �'.$Details[3].'</font>)':'').($Details[4]?', ����� �����: <font color=red><b>'.$Details[4].'</b></font>, �������� �����: '.$expa:'').($Details[5]?', <font color=green>�������</font>':'').($Eranks?', '.$Eranks:'');
		}
	}

	for ($i=1; $i<=$MaxTeam; $i++) {
		if ($Team[$i]) {
			$TeamString .= '������� �'.$i.': '.implode(" | ",$Team[$i]);
			if($i < $MaxTeam) $TeamString .= '<hr>';
		}
	}

	return $TeamString;
}

function Pages($ptype, $cpage, $maxpage, &$toprint) {

	$c = 11;
	if ($ptype == 1) {
        $ptmp = 'a=pgroup';
    } else {
        $ptmp = 'a=pdetail&key='.$_REQUEST['key'];
    }
    if ($maxpage > $c) {
        $spage = $cpage - ($c - 1)/2;
        $fpage = $cpage + ($c - 1)/2;
        if ($cpage > ($c + 1)/2) {
            $UseStart = 1;
        } else {
            $spage = 1;
            $fpage = $c;
        }
        if ($maxpage - $cpage > ($c - 1)/2) {
            $UseFinish = 1;
        } else {
            $spage = $maxpage - $c + 1;
            $fpage = $maxpage;
        }
    } else {
        $spage = 1;
        $fpage = $maxpage;
    }

    if ($UseStart) {
        $toprint .= '<a href="?act='.$_REQUEST['act'].'&d=find&'.$ptmp.'&sdate='.$_REQUEST['sdate'].'&fdate='.$_REQUEST['fdate'].'&nick='.$_REQUEST['nick'].'&location='.$_REQUEST['location'].'&many='.$_REQUEST['many'].'&cheat='.$_REQUEST['cheat'].'&status='.$_REQUEST['status'].'&page=1"><<</a> ';
    }

    for ($i=$spage; $i<=$fpage; $i++) {
        $toprint .= '<a href="?act='.$_REQUEST['act'].'&d=find&'.$ptmp.'&sdate='.$_REQUEST['sdate'].'&fdate='.$_REQUEST['fdate'].'&nick='.$_REQUEST['nick'].'&location='.$_REQUEST['location'].'&many='.$_REQUEST['many'].'&cheat='.$_REQUEST['cheat'].'&status='.$_REQUEST['status'].'&page='.$i.'">'.$i.'</a> ';
    }

    if ($UseFinish) {
        $toprint .= '<a href="?act='.$_REQUEST['act'].'&d=find&'.$ptmp.'&sdate='.$_REQUEST['sdate'].'&fdate='.$_REQUEST['fdate'].'&nick='.$_REQUEST['nick'].'&location='.$_REQUEST['location'].'&many='.$_REQUEST['many'].'&cheat='.$_REQUEST['cheat'].'&status='.$_REQUEST['status'].'&page='.$maxpage.'">>></a> ';
    }
}

?>