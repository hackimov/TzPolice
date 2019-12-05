<?php
if(!defined('MPLIB')) die('Лесом.');

$rowSet = applyPageFiltering($in['nickName'], $in['clan'], $in['level'], $in['blackList']);
$totalCountOfRecords = mysql_num_rows($rowSet);

$pageNavigator = buildPageNavigator($in['page'], $totalCountOfRecords, $in['nickName'], $in['clan'], $in['level'], $in['blackList']);

$startRecord = (!empty($in['page']) && $in['page'] > 0 && $in[page] <= ceil($totalCountOfRecords/$_mp_repordsPerPage)) ? ($in['page'] - 1)*$_mp_repordsPerPage : 0;
$endRecord = $startRecord + $_mp_repordsPerPage;

$displayResult = array();

$tmp = 0;
while ($b = mysql_fetch_array($rowSet)) {
	#print_r($b);
	if ($tmp >= $startRecord && $tmp < $endRecord) {
		array_push($displayResult, $b);
	}
	$tmp++;
	if ($tmp >= $endRecord) break;
}

?>
<script src='./scripts/angular.min.js'></script>
<script src='./scripts/chs.js'></script>

<div align="right">
<form method="get" action="<?php echo $_mp_targetURL; ?>">
<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
	Ник персонажа:
	<input type="text" name="nickName" value="<?php echo $in['nickName'];?>"/>
	Клан:
	<select name="clan">
		<option value=""></option>
<?php
	foreach (getBlackListClanes() as $blClan) {
		if (empty($blClan)) continue;
		echo "<option value='".$blClan."'".(($blClan == $in['clan']) ? " selected" : "").">".$blClan."</option>";
	}
?>
	</select>
	Уровень:
	<select name="level">
		<option value=""></option>
<?php
	foreach (getBlackListLevels() as $blLevel) {
		if (empty($blLevel)) continue;
		echo "<option value='".$blLevel."'".(($blLevel == $in['level']) ? " selected" : "").">".$blLevel."</option>";
	}
?>
	</select>
	ЧС:
	<select name="blackList">
		<option value=""></option>
		<option value="1" <?php echo ($in['blackList'] == "1" ? " selected" : ""); ?>>ЧС марадёров</option>
		<option value="2" <?php echo ($in['blackList'] == "2" ? " selected" : ""); ?>>ЧС кланов</option>
		<option value="3" <?php echo ($in['blackList'] == "3" ? " selected" : ""); ?>>Особый список</option>
	</select>
	<input type="submit" value="Искать">
</form>
</div>
<div align="center">
<?php echo $pageNavigator; ?>
</div>
<div ng-app="app">

<div id='chsedit' ng-show="infoVisible" style='border-radius: 5px; position: absolute; left: 50%; top: 50%; background: #999; margin-left: -300px; margin-top: -200px; width: 600px; height: 400px;'>
<div  ng-repeat="record in records">
	лог: {{record.log}}
</div>
</div>

<table width="100%" cellpadding="5">
  <tr align="center">
    <td bgcolor="#F4ECD4" valign="top"><b>Персонаж</b></td>
    <td bgcolor="#F4ECD4" valign="top"><b>Причина</b></td>
    <td bgcolor="#F4ECD4" valign="top" nowrap><b>Сумма выхода</b></td>
    <td bgcolor="#F4ECD4">&nbsp;</td>
  </tr>
<?php
	$counter = 0;
	$kpkResult = "";
	$recordsCounter = 0;
	foreach ($displayResult as $record) {
		$background = "background='http://www.tzpolice.ru//i/bgr-grid-sand".(($counter) ? "" : "1").".gif'";
		$description = "";
		$money = "";
		
		$description = $record['comment'];
		$money = $record['summ'];
		
		if (!empty($record['blackListClan'])) {
			
			// клановый ЧС
			$description = $_mp_clanBlackListMessage." : ".$record['blackListClan'] . ", ".$description;
			if (empty($record['summ'])) {
				$money = $record['blackListClanSumm'];
			}	
		}
					
		echo "
			<tr ng-controller='dossierCtrl'>
		    	<td style='padding-left: 15px;' nowrap ".$background.">".genereateUser($record)."</td>
		    	<td ".$background.">".$description."</td>
		    	<td ".$background.">".$money."</td>
				<td ".$background."><a href='#' ng-click='show_dossier(9480)'>подробнее</a></td>
			</tr>
		";
		$counter = (++$counter)%2;
		if (!empty($kpkResult)) {
			$kpkResult .= "\n";
		}
		$kpkResult .= '<item name="'.$record['login'].'" />';
		$recordsCounter++;
	}
?>
</table>
</div>
<div align="center">
<?php echo $pageNavigator; ?>
</div>
<br/><br/>
<div align="center">
	<script language="javascript">
		function displayLogs(id1, id2) {
			var activator = document.getElementById(id1);
			var content = document.getElementById(id2);
			activator.style.display = 'none';
			content.style.display = 'block';
			return false;
		}
		function displayHideKPK() {
			var area = document.getElementById('resultForKPK');
			var button = document.getElementById('displayHideKPKButton');
			if (area.style.display == 'none') {
				area.style.display = 'block';
				button.value = 'Скрыть результат страницы для КПК';
			} else {
				area.style.display = 'none';
				button.value = 'Показать результат страницы для КПК';
			}
			return false;
		}
	</script>
	<input type="button" id="displayHideKPKButton" value="Показать результат страницы для КПК" onclick="displayHideKPK();"/><br/><br/>
	<textarea id="resultForKPK" cols="70" rows="10" readonly="readonly" style="display: none;"><?php echo $kpkResult; ?></textarea>
</div>
