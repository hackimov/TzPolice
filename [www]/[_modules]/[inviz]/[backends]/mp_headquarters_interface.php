<?php
$_RESULT = array("res" => "ok");

require_once("/home/sites/police/dbconn/dbconn.php");
require_once("/home/sites/police/www/_modules/functions.php");
require_once("/home/sites/police/www/_modules/auth.php");
require_once("/home/sites/police/www/_modules/xhr_config.php");
require_once("/home/sites/police/www/_modules/xhr_php.php");



require_once('mp_headquarters_config.php');
require_once('mp_headquarters_domain.php');
require_once('mp_headquarters_functions.php');



$JsHttpRequest = new Subsys_JsHttpRequest_Php("windows-1251");

extract($_REQUEST);

if ($mpAuthorized) {

	if ($action == 'wars') {
		
		echo '������ � ����������!';
		
		if (AuthUserGroup == 100) {
			





		
		}
	}


	
	
	if ($action == 'statistics') {

		if ($page_target == 'generate_report_content') {//-----------------------------------------------------------------------------------
			$periodFromTime = strtotime($periodFrom.' 00:00');
			$periodToTime = strtotime($periodTo.' 23:59');

			if (!$periodFromTime || !$periodToTime || ($periodFromTime > $periodToTime)) {
				echo "<font color='red'><b>������� ������ ��� ���������</b></font>";
			} else {
				$query = mysql_query(
					"SELECT
						`login`,
						sum(`points`) `totalPoints`,
						sum(`digger_rescued`) `totalRescuedDiggers`,
						sum(`resources_rescued`) `totalResourcesRescued`
					 FROM `mp_headquarters_logs_eval`
					 WHERE `evaluated` = 1
					   AND `time` BETWEEN $periodFromTime AND $periodToTime
					 GROUP BY `login`
					", $db
				);
				$reportContent = array();
	 			while ($b = mysql_fetch_array($query)) {
					$query2 = mysql_query(
						"SELECT count(*) as `count`, `customizer`, sum(`rang_points`) as `rangs`
					 	 FROM `mp_headquarters_logs_hits`
						 WHERE `login` = '".$b['login']."'
						   AND `time` BETWEEN $periodFromTime AND $periodToTime
						 GROUP BY `customizer`
						", $db
					);
					$rangPoinsSumm = 0;
					$abilityCount = 0;
					$shockCount = 0;
					while ($c = mysql_fetch_array($query2)) {
						switch($c['customizer']) {
							case 'rangs':
								$rangPoinsSumm = $c['rangs'];
								break;
							case 'abilityTrophy':
								$abilityCount = $c['count'];
								break;
							case 'shockingHit':
								$shockCount = $c['count'];
								break;
						}
					}

					$query2 = mysql_query(
						"SELECT count(*) as `count`, `battle_type`
						 FROM `mp_headquarters_logs_eval`
						 WHERE `evaluated` = 1
						   AND `login` = '".$b['login']."'
						   AND `time` BETWEEN $periodFromTime AND $periodToTime
						 GROUP BY `battle_type`
						", $db
					);
					$battleBlackListCount = 0;
					$battleRescueCount = 0;
					while ($c = mysql_fetch_array($query2)) {
						switch($c['battle_type']) {
							case 'robbery':
								$battleRescueCount = $c['count'];
								break;
							case 'blacklist':
								$battleBlackListCount = $c['count'];
								break;
						}
					}

					array_push(
						$reportContent,
						array(
							$b['login'],
							$b['totalPoints'],
							$b['totalRescuedDiggers'],
							$b['totalResourcesRescued'],
							$rangPoinsSumm,
							$abilityCount,
							$shockCount,
							$battleRescueCount,
							$battleBlackListCount
						)
					);
				}

				// ��������� - ���������� ���������� ������ - ����� ������
				$points = array();
				foreach ($reportContent as $key => $row) {
					$points[$key] = $row[1];
				}
				array_multisort($points, SORT_DESC, $reportContent);

				// ���������� ������� �������
				?>
				<table cellpadding="5">
					<tr align="center">
						<td bgcolor="#F4ECD4" valign="top" width='50px' align='center' rowspan='2'><b>�����</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='200px' align='center' rowspan='2'><b>���</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='50px' align='center' rowspan='2'><b>�����</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='100px' align='center' colspan='2'><b>�������</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='50px' align='center' rowspan='2'><b>������ ������</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='100px' align='center' colspan='2'><b>������������</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='100px' align='center' colspan='2'><b>���</b></td>
					</tr>
					<tr align="center">
						<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>�����</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>��������</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>������</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>�����</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>�������� �����</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>������� ��</b></td>
					</tr>
				<?php
					$counter = 0;
					$place = 0;
					$totalGatheredPoints = 0;
					$totalRescuedDiggers = 0;
					$totalRescuedResources = 0;
					$totalRangs = 0;
					$totalUsedAbilities = 0;
					$totalUsedShocks = 0;
					$totalBattleRescueCount = 0;
					$totalBattleBlackListCount = 0;

					$query2 = mysql_query(
						"SELECT 1 as `count`, `battle_type`, sum(`digger_rescued`) AS `rescued`
						 FROM `mp_headquarters_logs_eval`
						 WHERE `evaluated` = 1
						   AND `time` BETWEEN $periodFromTime AND $periodToTime
						 GROUP BY `log`, `battle_type`
						", $db
					);
					while ($c = mysql_fetch_array($query2)) {
						switch($c['battle_type']) {
							case 'robbery':
								$totalBattleRescueCount += $c['count'];
								$totalRescuedDiggers += ($c['rescued'] > 0 ? 1 : 0);
								break;
							case 'blacklist':
								$totalBattleBlackListCount += $c['count'];
								break;
						}
					}


					foreach ($reportContent as $entry) {
						$background = "background='http://www.tzpolice.ru//i/bgr-grid-sand".(($counter) ? "" : "1").".gif'";
						$place++;
						/*
													$b['login'],
							$b['totalPoints'],
							$b['totalRescuedDiggers'],
							$b['totalResourcesRescued'],
							$rangPoinsSumm,
							$abilityCount,
							$shockCount

						*/
				?>
					<tr>
						<td <?php echo $background; ?> align='right'>
							<?php echo $place; ?>
						</td>
						<td <?php echo $background; ?>>
							<?php echo $entry[0]; ?>
						</td>
						<td <?php echo $background; ?> align='right'>
							<?php echo $entry[1]; $totalGatheredPoints += $entry[1]; ?>
						</td>
						<td <?php echo $background; ?> align='right'>
							<?php echo $entry[2]; ?>
						</td>
						<td <?php echo $background; ?> align='right'>
							<?php echo $entry[3]; $totalRescuedResources += $entry[3]; ?>
						</td>
						<td <?php echo $background; ?> align='right'>
							<?php echo $entry[4]; $totalRangs += $entry[4]; ?>
						</td>
						<td <?php echo $background; ?> align='right'>
							<?php echo $entry[5]; $totalUsedAbilities += $entry[5]; ?>
						</td>
						<td <?php echo $background; ?> align='right'>
							<?php echo $entry[6]; $totalUsedShocks += $entry[6]; ?>
						</td>
						<td <?php echo $background; ?> align='right'>
							<?php echo $entry[7]; ?>
						</td>
						<td <?php echo $background; ?> align='right'>
							<?php echo $entry[8]; ?>
						</td>
					</tr>
				<?php
						$counter = (++$counter)%2;
					}
				?>
					<tr>
						<td bgcolor="#F4ECD4" align='right' colspan='2'>
							<b>�����:</b>
						</td>
						<td bgcolor="#F4ECD4" align='right'>
							<b><?php echo $totalGatheredPoints; ?></b>
						</td>
						<td bgcolor="#F4ECD4" align='right'>
							<b><?php echo $totalRescuedDiggers; ?></b>
						</td>
						<td bgcolor="#F4ECD4" align='right'>
							<b><?php echo $totalRescuedResources; ?></b>
						</td>
						<td bgcolor="#F4ECD4" align='right'>
							<b><?php echo $totalRangs; ?></b>
						</td>
						<td bgcolor="#F4ECD4" align='right'>
							<b><?php echo $totalUsedAbilities; ?></b>
						</td>
						<td bgcolor="#F4ECD4" align='right'>
							<b><?php echo $totalUsedShocks; ?></b>
						</td>
						<td bgcolor="#F4ECD4" align='right'>
							<b><?php echo $totalBattleRescueCount; ?></b>
						</td>
						<td bgcolor="#F4ECD4" align='right'>
							<b><?php echo $totalBattleBlackListCount; ?></b>
						</td>
					</tr>
				</table>
				<?php
			}
		} else {//-----------------------------------------------------------------------------------
			if (empty($periodFrom)) {
				$periodFrom = date('d.m.Y', strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . " -2 week"));
			}
			if (empty($periodTo)) {
				$periodTo = date('d.m.Y');
			}
		?>
			<center><b>������� ����������</b></center><br/><br/>
			<center>
			�������� ������:
			�
			<input type="text" size="15" maxlength="10" id='periodFrom' value='<?php echo $periodFrom; ?>'>
			��
			<input type="text" size="15" maxlength="10" id='periodTo' value='<?php echo $periodTo; ?>'><br/>
			(���������� - ���� ��������� � ������� ��.��.����)<br/><br/>
			<input type="button" value="�������������" onclick="generate_report('statistics', document.getElementById('periodFrom').value, document.getElementById('periodTo').value);">
			<br/><br/>
			<div id="reportContent"></div>

			</center>
		<?php
		}
	}

	if ($action == 'cabinet') {
		?>

		<?php
		if ($page_target == 'upload_kpk_data') {//-----------------------------------------------------------------------------------
			// ��������/�������/���������� ���������� ������� �� ���
			$lines = explode("\n", $dataContent);
			$shockingHits = array();
			$abilityTrophy = array();
			$rangPoins = array();
			$battles = array();
			foreach($lines as $line) {
				// ������������� �����
				preg_match("/(\d{2}).(\d{2}).(\d{2}) (\d{2}):(\d{2}) ����������� ���� Shocking hit\[\d\]/", $line, $shockingHitsEntry);
				if (count($shockingHitsEntry) > 1) {
					array_push($shockingHits, array(mktime($shockingHitsEntry[4], $shockingHitsEntry[5], 0, $shockingHitsEntry[2], $shockingHitsEntry[1], $shockingHitsEntry[3]), $shockingHitsEntry[0]));
				}

				// ������������� ������
				preg_match("/(\d{2}).(\d{2}).(\d{2}) (\d{2}):(\d{2}) ����������� ���� Ability Atrophy\[\d\]/", $line, $abilityTrophyEntry);
				if (count($abilityTrophyEntry) > 1) {
					array_push($abilityTrophy, array(mktime($abilityTrophyEntry[4], $abilityTrophyEntry[5], 0, $abilityTrophyEntry[2], $abilityTrophyEntry[1], $abilityTrophyEntry[3]), $abilityTrophyEntry[0]));
				}

				// ��������� ����-�������
				preg_match("/(\d{2}).(\d{2}).(\d{2}) (\d{2}):(\d{2}) �������� (\d+) ����-�������/", $line, $rangPoinsEntry);
				if (count($rangPoinsEntry) > 1) {
					array_push($rangPoins, array(mktime($rangPoinsEntry[4], $rangPoinsEntry[5], 0, $rangPoinsEntry[2], $rangPoinsEntry[1], $rangPoinsEntry[3]), $rangPoinsEntry[6]/100, $rangPoinsEntry[0]));
				}
				preg_match("/(\d{2}).(\d{2}).(\d{2}) (\d{2}):(\d{2}) �������� ([\d.]+) ���������� ����-�������/", $line, $rangPoinsEntry);
				if (count($rangPoinsEntry) > 1) {
					array_push($rangPoins, array(mktime($rangPoinsEntry[4], $rangPoinsEntry[5], 0, $rangPoinsEntry[2], $rangPoinsEntry[1], $rangPoinsEntry[3]), $rangPoinsEntry[6], $rangPoinsEntry[0]));
				}

				// ��������� ������� ���
				//21.07.12 07:10 �������� 1 ���������� ����-������� �� ������ � ��� 28430579028481.
				//21.07.12 07:09 �������� 25 ����-������� �� �������� ��������� _��������_ � ��� 28430579028481.
				preg_match("/\d{2}.\d{2}.\d{2} \d{2}:\d{2} �������� [\s\S]+? � ��� (\d+)./", $line, $battlesEntry);
				if (count($battlesEntry) > 1) {
					array_push($battles, $battlesEntry[1]);
				}
				//22.07.12 14:51 �� ��������� �������� 28430670649089. �������� �����: 0
				//18.07.12 19:16 �� ��������� ��� 28430430724609. �������� �����: 459
				preg_match("/\d{2}.\d{2}.\d{2} \d{2}:\d{2} �� ��������� [\s\S]+? (\d+). �������� �����:/", $line, $battlesEntry);
				if (count($battlesEntry) > 1) {
					array_push($battles, $battlesEntry[1]);
				}
				//21.07.12 07:10 �� �������� � �������� 28430579028481. �������� �����: 190684
				//19.07.12 18:25 �� �������� � ��� 28430487362817. �������� �����: 295
				preg_match("/\d{2}.\d{2}.\d{2} \d{2}:\d{2} �� �������� � [\s\S]+? (\d+). �������� �����:/", $line, $battlesEntry);
				if (count($battlesEntry) > 1) {
					array_push($battles, $battlesEntry[1]);
				}
			}
			// avoid duplicates for battle logs
			$battles = array_unique($battles);
			?>
			<div align='center'>
				����� ������� ��������: <?php echo count($lines);?><br/>
				�� ��� - ������� �� ������������� Shocking hit: <?php echo count($shockingHits); ?><br/>
				�� ��� - ������� �� ������������� Ability Atrophy: <?php echo count($abilityTrophy); ?><br/>
				�� ��� - ������� � ����-�������: <?php echo count($rangPoins); ?><br/>
				�� ��� - ������� � ����: <?php echo count($battles); ?><br/>
			</div>
			<?php
			// ���������� ������
			//$shockingHits = array();
			foreach($shockingHits as $record) {
				// use insert if not exist for avoid duplicated data
				mysql_query(
					"INSERT IGNORE INTO `mp_headquarters_logs_hits`
					 SET `login` = '".AuthUserName."',
					     `time` = ".$record[0].",
					     `text` = '".$record[1]."',
					     `customizer` = 'shockingHit'
					", $db
				);
			}
			//$abilityTrophy = array();
			foreach($abilityTrophy as $record) {
				// use insert if not exist for avoid duplicated data
				mysql_query(
					"INSERT IGNORE INTO `mp_headquarters_logs_hits`
					 SET `login` = '".AuthUserName."',
					     `time` = ".$record[0].",
					     `text` = '".$record[1]."',
					     `customizer` = 'abilityTrophy'
					", $db
				);
			}
			//$rangPoins = array();
			foreach($rangPoins as $record) {
				// use insert if not exist for avoid duplicated data
				mysql_query(
					"INSERT IGNORE INTO `mp_headquarters_logs_hits`
					 SET `login` = '".AuthUserName."',
					     `time` = ".$record[0].",
					     `text` = '".$record[2]."',
					     `rang_points` = ".$record[1].",
					     `customizer` = 'rangs'
					", $db
				);
			}
			//$battles = array();
			foreach($battles as $record) {
				// use insert if not exist for avoid duplicated data
				mysql_query(
					"INSERT IGNORE INTO `mp_headquarters_logs_eval`
					 SET `login` = '".AuthUserName."',
					     `log` = ".$record."
					", $db
				);
			}
		} else if ($page_target == 'evaluate_logs') {//-----------------------------------------------------------------------------------
			drawMenu();
		?>

		<center>
			<b>�����: <font color=green>������ �����</font></b><br/>
			<table cellpadding="5">
				<tr align="center">
    				<td bgcolor="#F4ECD4" valign="top" width='100px'><b>����� ����</b></td>
    				<td bgcolor="#F4ECD4" valign="top" width='300px'><b>������</b></td>
  				</tr>
  			<?php
  			$query = mysql_query(
  				"SELECT
  				 	`log`
  				 FROM `mp_headquarters_logs_eval`
  				 WHERE (`login` = '".AuthUserName."' OR '".AuthUserGroup."' = '100')
  				   AND `evaluated` = 0
  				", $db
  			);
 			$counter = 0;
 			$logs = array();
 			while ($b = mysql_fetch_array($query)) {
				$background = "background='http://www.tzpolice.ru//i/bgr-grid-sand".(($counter) ? "" : "1").".gif'";
				$logNumber = $b['log'];
				array_push($logs, $logNumber);
 			?>
 				<tr>
		    		<td <?php echo $background; ?>><a href='http://www.timezero.ru/sbtl.ru.html?<?php echo $logNumber; ?>' target='_blank'><?php echo $logNumber; ?></a></td>
		    		<td <?php echo $background; ?> style='padding-left: 10px;'><div id='<?php echo $logNumber; ?>'>�� ��������</div></td>
				</tr>
 			<?php
				$counter = (++$counter)%2;
			}
  			?>
			</table>
			<br />
			<input type="button" value="��������� ����" onclick="evaluate_logs('cabinet', <?php echo '['.(implode(',', $logs)).']'?>);"><br/><br/>
		</center>

		<?php
		} else if ($page_target == 'evaluate_log_content') {//-----------------------------------------------------------------------------------
			$logContent = '';
			if (getBattleLog(&$logContent, $dataContent)) {
				// �������� �� ��, �������� �� ��� ��� ��� ������� ������ (���� �������� ���������� ��������)
				$shouldBeEvaluated = false;
				$query = mysql_query(
					"SELECT `login`, `evaluated`
					 FROM `mp_headquarters_logs_eval`
					 WHERE (`login` = '".AuthUserName."' OR '".AuthUserGroup."' = '100') 
					   AND `log` = $dataContent
					", $db
				);
	 			while ($b = mysql_fetch_array($query)) {
	 				$shouldBeEvaluated = ($b['evaluated'] == 0);
	 				break;
	 			}
	 			if ($shouldBeEvaluated) {
					$battleFlow = parseBattleLog($logContent);
					//echo '<pre>';
					//print_r($battleFlow);
					//echo '</pre>';
					$battleFlow->evaluateBattleRaiting($b['login'], $score, $isVictimAlive, $savedResources, $battleDirection);
					//set('robbery','blacklist')
					//		  case 'black_list_shutting':
		  			// case 'robbery':
		  			debug("<br>��� ��� :: $battleDirection<br>");
  					debug("������ :: $score<br>");
  					debug("������ �������: ".($isVictimAlive ? '��' : '���')."<br>");
  					debug("������� ��������: $savedResources<br>");
		  			if ($battleDirection == 'black_list_shutting' || $battleDirection == 'attack_to_mp') {
		  				$battleDirection = 'blacklist';
		  				$battleTime = $battleFlow->timestamp;
		  				$points = $score;
		  				$digger_rescued = 0;
		  				$resources_rescued = 0;
		  			} else if ($battleDirection == 'robbery') {
		  				$battleDirection = 'robbery';
		  				$battleTime = $battleFlow->timestamp;
		  				$points = $score;
		  				$digger_rescued = $isVictimAlive ? 1 : 0;
		  				$resources_rescued = $savedResources;
		  			} else {
		  				$battleDirection = '';
		  				$battleTime = $battleFlow->timestamp;
		  				$points = 0;
		  				$digger_rescued = 0;
		  				$resources_rescued = 0;
		  			}
		  			mysql_query(
		  				"UPDATE `mp_headquarters_logs_eval`
		  				    SET `time` = $battleTime,
		  						`battle_type` = '$battleDirection',
		  						`evaluated` = 1,
		  						`points` = $points,
		  						`digger_rescued` = $digger_rescued,
		  						`resources_rescued` = $resources_rescued
						  WHERE `login` = '".AuthUserName."'
						    AND `log` = $dataContent
		  				", $db
		  			);
					echo "<b><font color=green>��� �������� � ��������� �������</font> (������: $points)</b>";
	 			} else {
					echo '<b>��� ��� ���������</b>';
	 			}
			} else {
				echo $logContent." <a href='javascript:{}' onclick=\"remove_log('cabinet','$dataContent');\">������� ���</a>";
			}
		} else if ($page_target == 'remove_log') {//-----------------------------------------------------------------------------------
			mysql_query(
				"DELETE FROM `mp_headquarters_logs_eval`
				 WHERE `login` = '".AuthUserName."'
				   AND `log` = $dataContent
				", $db
			);
			mysql_query(
				"DELETE FROM `mp_headquarters_logs`
				 WHERE `log_id` = $dataContent
				", $db
			);
			echo "<b>������ �� ��� ������� �������</b>";
		} else if ($page_target == 'generate_report') {//-----------------------------------------------------------------------------------
			drawMenu();
			if (empty($periodFrom)) {
				$periodFrom = date('d.m.Y', strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . " -2 week"));
			}
			if (empty($periodTo)) {
				$periodTo = date('d.m.Y');
			}
		?>
		<center>
			<b>�����: <font color=green>��������� ������</font></b><br/><br/>
			�������� ������:
			�
			<input type="text" size="15" maxlength="10" id='periodFrom' value='<?php echo $periodFrom; ?>'>
			��
			<input type="text" size="15" maxlength="10" id='periodTo' value='<?php echo $periodTo; ?>'><br/>
			(���������� - ���� ��������� � ������� ��.��.����)<br/><br/>
			<input type="button" value="�������������" onclick="generate_report('cabinet', document.getElementById('periodFrom').value, document.getElementById('periodTo').value);">
			<br/><br/>
			<div id="reportContent"></div>
		</center>
		<?php
		} else if ($page_target == 'generate_report_content') {//-----------------------------------------------------------------------------------
			$periodFromTime = strtotime($periodFrom.' 00:00');
			$periodToTime = strtotime($periodTo.' 23:59');

			if (!$periodFromTime || !$periodToTime || ($periodFromTime > $periodToTime)) {
				echo "<font color='red'><b>������� ������ ��� ���������</b></font>";
			} else {
				$query = mysql_query(
					"SELECT
						`time`,
						`log`,
						`battle_type`,
						`points`,
						`digger_rescued`,
						`resources_rescued`
					 FROM `mp_headquarters_logs_eval`
					 WHERE `login` = '".AuthUserName."'
					   AND `evaluated` = 1
					   AND `time` BETWEEN $periodFromTime AND $periodToTime
					 ORDER BY `time` ASC
					", $db
				);
				?>
				<table cellpadding="5">
					<tr align="center">
						<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>����</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='100px' align='center'><b>���</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='100px' align='center'><b>��� ���</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>�����</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>�������� ������</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>�������� ��������</b></td>
					</tr>
				<?php
					$counter = 0;
					$reportBattles = array();
					$diggerRescuedCount = 0;
					$resourcesRescuedCount = 0;
					$pointsCount = 0;
					while ($b = mysql_fetch_array($query)) {
						$background = "background='http://www.tzpolice.ru//i/bgr-grid-sand".(($counter) ? "" : "1").".gif'";
						/*
						`time`,
						`log`,
						`battle_type`,
						`points`,
						`digger_rescued`,
						`resources_rescued`
						*/
						$date = date('d.m.Y', $b['time']);
						if ($b['points'] > 0) {
							array_push($reportBattles, array($date, $b['log'], $b['points']));
						}
						$pointsCount += $b['points'];
						$diggerRescuedCount += ($b['digger_rescued'] ? 1 : 0);
						$resourcesRescuedCount += $b['resources_rescued']
				?>
					<tr>
						<td <?php echo $background; ?>>
							<?php echo $date; ?>
						</td>
						<td <?php echo $background; ?>>
							<a href='http://www.timezero.ru/sbtl.ru.html?<?php echo $b['log']; ?>' target='_blank'><?php echo $b['log']; ?></a>
						</td>
						<td <?php echo $background; ?> align='center'>
							<?php
								switch($b['battle_type']) {
									case 'robbery':
										echo '�������� �����';
										break;
									case 'blacklist':
										echo '������� ����';
										break;
									default:
										echo 'n/a';
								}
							?>
						</td>
						<td <?php echo $background; ?> align='right'>
							<?php echo $b['points']; ?>
						</td>
						<td <?php echo $background; ?> align='center'>
							<?php echo $b['digger_rescued'] ? '+' : '&nbsp;'; ?>
						</td>
						<td <?php echo $background; ?> align='right'>
							<?php echo ($b['resources_rescued'] != 0) ? $b['resources_rescued'] : '&nbsp;'; ?>
						</td>
					</tr>
				<?php
						$counter = (++$counter)%2;
					}

					$query = mysql_query(
						"SELECT count(*) as `count`, `customizer`, sum(`rang_points`) as `rangs`
						 FROM `mp_headquarters_logs_hits`
						 WHERE `login` = '".AuthUserName."'
						   AND `time` BETWEEN $periodFromTime AND $periodToTime
						 GROUP BY `customizer`
						", $db
					);
					$rangPoinsSumm = 0;
					$shockCount = 0;
					$abilityCount = 0;
					while ($b = mysql_fetch_array($query)) {
						switch($b['customizer']) {
							case 'rangs':
								$rangPoinsSumm = $b['rangs'];
								break;
							case 'abilityTrophy':
								$abilityCount = $b['count'];
								break;
							case 'shockingHit':
								$shockCount = $b['count'];
								break;
						}
					}
				?>
				</table><br/><br/>
				<b>��������� ��� ���������� �� �����:</b>
				<center>
				<?php
					$forForum = '';
					$dateSeparation = null;
					foreach ($reportBattles as $reportBattle) {
						if ($dateSeparation == null) {
							$dateSeparation = $reportBattle[0];
						} else if ($dateSeparation != $reportBattle[0]) {
							$forForum .= "\n";
							$dateSeparation = $reportBattle[0];
						}
						$forForum .= $reportBattle[0].' <BATTLE>'.$reportBattle[1].'</BATTLE> - '.$reportBattle[2].' ����(��)'."\n";
					}
					$forForum .= "\n-------------------\n";
					$forForum .= "����� ������: $pointsCount\n";
					$forForum .= "������� �����: $diggerRescuedCount\n";
					$forForum .= "������� ��������: $resourcesRescuedCount\n";
					$forForum .= "������������ �����: $shockCount\n";
					$forForum .= "������������ ������: $abilityCount\n";
					$forForum .= "������� ����-�������: $rangPoinsSumm\n";
				?>
				<textarea cols="80" rows="30"><?php echo $forForum; ?></textarea>
				</center>
				<?php
			}
		} else if ($page_target == 'usage_history') {//-----------------------------------------------------------------------------------
			drawMenu();
			$recordsPerPage = 25;

			if (empty($page)) $page = 1;

			$query = mysql_query(
				"SELECT `text`
				   FROM `mp_headquarters_logs_hits`
				 WHERE `login` = '".AuthUserName."'
				", $db
			);
			$TotalPages = ceil(mysql_num_rows($query) / $recordsPerPage);

			$query = mysql_query(
				"SELECT `text`
				   FROM `mp_headquarters_logs_hits`
				 WHERE `login` = '".AuthUserName."'
				 ORDER BY `time` DESC
				 LIMIT ".($recordsPerPage*($page - 1))." , $recordsPerPage
				", $db
			);
		?>
		<center>
			<b>�����: <font color=green>������� ������/�����/������</font></b><br/><br/>
			<?php ShowJoinPages($page, $TotalPages, 20); ?>
			<table cellpadding="5">
				<tr align="center">
					<td bgcolor="#F4ECD4" valign="top" width='400px' align='center'><b>���������� ������</b></td>
				</tr>
		<?php
			$counter = 0;
			while ($b = mysql_fetch_array($query)) {
				$background = "background='http://www.tzpolice.ru//i/bgr-grid-sand".(($counter) ? "" : "1").".gif'";
		?>
				<tr>
					<td <?php echo $background; ?>>
						<?php echo $b['text']; ?>
					</td>
				</tr>
		<?php
				$counter = (++$counter)%2;
			}
		?>
			</table>
		</center>
		<?php
		} else if ($page_target == 'logs_history') {//-----------------------------------------------------------------------------------
			drawMenu();
			$recordsPerPage = 25;

			if (empty($page)) $page = 1;

			$query = mysql_query(
				"SELECT *
				   FROM `mp_headquarters_logs_eval`
				 WHERE `login` = '".AuthUserName."'
				", $db
			);
			$TotalPages = ceil(mysql_num_rows($query) / $recordsPerPage);

			$query = mysql_query(
				"SELECT
					`time`,
					`log`,
					`battle_type`,
					`points`,
					`digger_rescued`,
					`resources_rescued`
				 FROM `mp_headquarters_logs_eval`
				 WHERE `login` = '".AuthUserName."'
				   AND `evaluated` = 1
				 ORDER BY `time` DESC
				 LIMIT ".($recordsPerPage*($page - 1))." , $recordsPerPage
				", $db
			);
		?>
		<center>
			<b>�����: <font color=green>������� ������� �����</font></b><br/><br/>
			<?php ShowJoinPages($page, $TotalPages, 20); ?>
			<table cellpadding="5">
					<tr align="center">
					<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>����</b></td>
					<td bgcolor="#F4ECD4" valign="top" width='100px' align='center'><b>���</b></td>
					<td bgcolor="#F4ECD4" valign="top" width='100px' align='center'><b>��� ���</b></td>
					<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>�����</b></td>
					<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>�������� ������</b></td>
					<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>�������� ��������</b></td>
				</tr>
			<?php
				$counter = 0;
				while ($b = mysql_fetch_array($query)) {
					$background = "background='http://www.tzpolice.ru//i/bgr-grid-sand".(($counter) ? "" : "1").".gif'";
						/*
						`time`,
						`log`,
						`battle_type`,
						`points`,
						`digger_rescued`,
						`resources_rescued`
						*/
			?>
				<tr>
					<td <?php echo $background; ?>>
						<?php echo date('d.m.Y', $b['time']); ?>
					</td>
					<td <?php echo $background; ?>>
						<a href='http://www.timezero.ru/sbtl.ru.html?<?php echo $b['log']; ?>' target='_blank'><?php echo $b['log']; ?></a>
					</td>
					<td <?php echo $background; ?> align='center'>
						<?php
							switch($b['battle_type']) {
								case 'robbery':
									echo '�������� �����';
									break;
								case 'blacklist':
									echo '������� ����';
									break;
								default:
									echo 'n/a';
							}
						?>
					</td>
					<td <?php echo $background; ?> align='right'>
						<?php echo $b['points']; ?>
					</td>
					<td <?php echo $background; ?> align='center'>
						<?php echo $b['digger_rescued'] ? '+' : '&nbsp;'; ?>
					</td>
					<td <?php echo $background; ?> align='right'>
						<?php echo ($b['resources_rescued'] != 0) ? $b['resources_rescued'] : '&nbsp;'; ?>
					</td>
				</tr>
				<?php
						$counter = (++$counter)%2;
				}
				?>
			</table>
		</center>
		<?php
		} else {//----------------------------------------------------------------------------------------------------------------
			drawMenu();
		?>

		<center>
			<b>�����: <font color=green>��������� ������ �� ������� (���)</font></b><br/>
			<textarea name="kpkData" id="kpkData" cols="70" rows="30"></textarea><br/><br/>
			<input type="button" value="��������� ������ �� ������� ���" onclick="upload_kpk_data('cabinet', document.getElementById('kpkData').value);"><br/><br/>
			<div id="uploading"></div>
		</center>

		<?php
		}
	}

}

if ($adminAuthorized) {
	
	if ($action == 'alllogs') {
		//-----------------------------------------------------------------------------------
		if ($page_target == 'generate_report_content') {//-----------------------------------------------------------------------------------
			$periodFromTime = strtotime($periodFrom.' 00:00');
			$periodToTime = strtotime($periodTo.' 23:59');

			if (!$periodFromTime || !$periodToTime || ($periodFromTime > $periodToTime)) {
				echo "<font color='red'><b>������� ������ ��� ���������</b></font>";
			} else {
				$query = mysql_query(
					"SELECT
						`time`,
						`login`,
						`log`,
						`battle_type`,
						`points`,
						`digger_rescued`,
						`resources_rescued`
					 FROM `mp_headquarters_logs_eval`
					 WHERE `evaluated` = 1".(($mcop != "")?" AND `login` = '".$mcop."' ":" ")."
					   AND `time` BETWEEN $periodFromTime AND $periodToTime AND `points` > 0
					 ORDER BY `time` ASC
					", $db
				);
				
				?>
				<table cellpadding="5">
					<tr align="center">
						<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>����</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='70px' align='center'><b>���</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='100px' align='center'><b>���</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='100px' align='center'><b>��� ���</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>�����</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>�������� ������</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='50px' align='center'><b>�������� ��������</b></td>
					</tr>
				<?php
					$counter = 0;
					$reportBattles = array();
					$diggerRescuedCount = 0;
					$resourcesRescuedCount = 0;
					$pointsCount = 0;
					while ($b = mysql_fetch_array($query)) {
						$background = "background='http://www.tzpolice.ru//i/bgr-grid-sand".(($counter) ? "" : "1").".gif'";
						/*
						`time`,
						`log`,
						`battle_type`,
						`points`,
						`digger_rescued`,
						`resources_rescued`
						*/
						$date = date('d.m.Y', $b['time']);
						if ($b['points'] > 0) {
							array_push($reportBattles, array($date, $b['log'], $b['points']));
						}
						$pointsCount += $b['points'];
						$diggerRescuedCount += ($b['digger_rescued'] ? 1 : 0);
						$resourcesRescuedCount += $b['resources_rescued']
				?>
					<tr>
						<td <?php echo $background; ?>>
							<?php echo $date; ?>
						</td>
						<td <?php echo $background; ?>>
							<?php echo $b['login']; ?>
						</td>
						<td <?php echo $background; ?>>
							<a href='http://www.timezero.ru/sbtl.ru.html?<?php echo $b['log']; ?>' target='_blank'><?php echo $b['log']; ?></a>
						</td>
						<td <?php echo $background; ?> align='center'>
							<?php
								switch($b['battle_type']) {
									case 'robbery':
										echo '�������� �����';
										break;
									case 'blacklist':
										echo '������� ����';
										break;
									default:
										echo 'n/a';
								}
							?>
						</td>
						<td <?php echo $background; ?> align='right'>
							<?php echo $b['points']; ?>
						</td>
						<td <?php echo $background; ?> align='center'>
							<?php echo $b['digger_rescued'] ? '+' : '&nbsp;'; ?>
						</td>
						<td <?php echo $background; ?> align='right'>
							<?php echo ($b['resources_rescued'] != 0) ? $b['resources_rescued'] : '&nbsp;'; ?>
						</td>
					</tr>
				<?php
						$counter = (++$counter)%2;
					}

					$query = mysql_query(
						"SELECT count(*) as `count`, `customizer`, sum(`rang_points`) as `rangs`
						 FROM `mp_headquarters_logs_hits`
						 WHERE `time` BETWEEN $periodFromTime AND $periodToTime".(($mcop != "")?" AND `login` = '".$mcop."' ":" ")."
						 GROUP BY `customizer`
						", $db
					);
					$rangPoinsSumm = 0;
					$shockCount = 0;
					$abilityCount = 0;
					while ($b = mysql_fetch_array($query)) {
						switch($b['customizer']) {
							case 'rangs':
								$rangPoinsSumm = $b['rangs'];
								break;
							case 'abilityTrophy':
								$abilityCount = $b['count'];
								break;
							case 'shockingHit':
								$shockCount = $b['count'];
								break;
						}
					}
				?>
				</table><br/><br/>
				<b>��������� ��� ���������� �� �����:</b>
				<center>
				<?php
					$forForum = '';
					$dateSeparation = null;
					foreach ($reportBattles as $reportBattle) {
						if ($dateSeparation == null) {
							$dateSeparation = $reportBattle[0];
						} else if ($dateSeparation != $reportBattle[0]) {
							$forForum .= "\n";
							$dateSeparation = $reportBattle[0];
						}
						$forForum .= $reportBattle[0].' <BATTLE>'.$reportBattle[1].'</BATTLE> - '.$reportBattle[2].' ����(��)'."\n";
					}
					$forForum .= "\n-------------------\n";
					$forForum .= "����� ������: $pointsCount\n";
					$forForum .= "������� �����: $diggerRescuedCount\n";
					$forForum .= "������� ��������: $resourcesRescuedCount\n";
					$forForum .= "������������ �����: $shockCount\n";
					$forForum .= "������������ ������: $abilityCount\n";
					$forForum .= "������� ����-�������: $rangPoinsSumm\n";
				?>
				<textarea cols="80" rows="30"><?php echo $forForum; ?></textarea>
				</center>
				<?php
			}
		} else {//----------------------------------------------------------------------------------------------------------------
			if (empty($periodFrom)) {
				$periodFrom = date('d.m.Y', strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . " -2 week"));
			}
			if (empty($periodTo)) {
				$periodTo = date('d.m.Y');
			}
		?>
		<center>
			<b>�����: <font color=green>��������� ������ ������</font></b><br/><br/>
			�������� ������:
			�
			<input type="text" size="15" maxlength="10" id='periodFrom' value='<?php echo $periodFrom; ?>'>
			��
			<input type="text" size="15" maxlength="10" id='periodTo' value='<?php echo $periodTo; ?>'>
			�� ���������� �� <select id="mcop"><option value=""></option>
			<?php
				$selectcop = '';
				$querytext = "SELECT DISTINCT `login` FROM `mp_headquarters_logs_eval`";
				$query = mysql_query($querytext ,$db);
				while ($row = mysql_fetch_array($query)) {
					echo "<option value='".$row['login']."' ".(($mcop == $row['login'])?" SELECTED":"").">".$row['login']."</option>";
				}
			?>
			</select>;
			<br/>
			(���������� - ���� ��������� � ������� ��.��.����)<br/><br/>
			<input type="button" value="�������������" onclick="generate_report_all('alllogs', document.getElementById('periodFrom').value, document.getElementById('periodTo').value, document.getElementById('mcop').value);">
			<br/><br/>
			<div id="reportContent"></div>
		</center>
		<?php
		}
	}
	
	
	if ($action == 'admin_console') {
		if ($page_target == 'admin_list') { //----------------------------------------------
			drawAdminMenu();
			?>
			<center>
				<b>�����: <font color=green>���������� ������� ���������������</font></b><br/>
			<?php
			
				error_log(' - '.AuthUserName.' - '.$command_action.' / '.$command_data.PHP_EOL, 3, 'mp.log');
			
				if ($command_action == 'remove' && !empty($command_data)) {
					mysql_query(
						"DELETE FROM `mp_headquarters_admin`
						 WHERE `login` = '$command_data'
						", $db
					);
					if (mysql_affected_rows($db) > 0) {
						echo "<br>$command_data - ������� ����� �� ������ ���������������";
					}
				}
				if ($command_action == 'add' && !empty($command_data)) {
					mysql_query(
						"INSERT IGNORE INTO `mp_headquarters_admin`
						 SET `login` = '$command_data'
						", $db
					);
					if (mysql_affected_rows($db) > 0) {
						echo "<br>$command_data - ������� �������� � ������ ���������������";
					}
				}
			?>
				<table cellpadding="5">
					<tr align="center">
						<td bgcolor="#F4ECD4" valign="top" width='200px' align='center'><b>���</b></td>
						<td bgcolor="#F4ECD4" valign="top" width='100px' align='center'>&nbsp;</td>
					</tr>
			<?php
				$counter = 0;
				$query = mysql_query(
					"SELECT `login`
					 FROM `mp_headquarters_admin`
					", $db
				);
				while ($b = mysql_fetch_array($query)) {
					$background = "background='http://www.tzpolice.ru//i/bgr-grid-sand".(($counter) ? "" : "1").".gif'";
			?>
					<tr>
						<td <?php echo $background; ?>><b><?php echo $b['login']; ?></b></td>
						<td <?php echo $background; ?> align='center'>
							<a href='javascript:{}' onclick="pass_action_to_page('admin_console','admin_list','remove','<?php echo $b['login']; ?>');">�������</a>
						</td>
					</tr>
			<?php
					$counter = (++$counter)%2;
				}

			?>
				</table>
				<br>
				<input type='text' id='adminNick'>
				<input type="button" value="�������� � ������ ���������������" onclick="pass_action_to_page('admin_console', 'admin_list', 'add', document.getElementById('adminNick').value);"><br/>
				(<b><font color='red'>��������:</font></b> �������� �����������������, ��� ��������� �� ����������� �� ���� �������.<br />������ �����������!)
			</center>
			<?php
		}

		if ($page_target == 'cleanup') { //----------------------------------------------
			drawAdminMenu();
			?>
			<center>
				<b>�����: <font color=green>������� ������</font></b><br/>
			<?php
				$dateIsAutogenerated = false;
				if (empty($command_data)) {
					$command_data = date('d.m.Y', strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . " -6 month"));
					$dateIsAutogenerated = true;
				}
			?>
				<input type="text" size="15" maxlength="10" id='command_data' value='<?php echo $command_data; ?>'><br>
				(���� ����������� � ������� ��.��.����)<br><br>
				<input type="button" value="������� ������ �����, ������ ��������� ����" onclick="pass_action_to_page('admin_console', 'cleanup', 'perform_cleanup', document.getElementById('command_data').value);"><br/><br/>
			<?php
				if ($command_action == 'perform_cleanup') {
					$timePoint = strtotime($command_data. '00:00');
					if (!$timePoint || $dateIsAutogenerated) {
						echo "<font color='red'><b>�������� �� ����� ���� ����������� - ������� ���� � ���������� �������</b></font>";
					} else {
						// ������� ������� ����� ��� ��������
						$query = mysql_query(
							"SELECT `log`
							 FROM `mp_headquarters_logs_eval`
							 WHERE `time` < $timePoint
							", $db
						);
						$identifiersArray = array();
						while ($b = mysql_fetch_array($query)) {
							array_push($identifiersArray, $b['log']);
						}
						// �������� ����� ���
						if (count($identifiersArray) > 0) {
							$query = mysql_query(
								"DELETE FROM `mp_headquarters_logs`
								 WHERE `log_id` IN (".(implode(',',$identifiersArray)).")
								", $db
							);
							echo '���� ��� - �������: '.(mysql_affected_rows($db)).'<br>';
						} else {
							echo '���� ��� - ��� ������ ��� ��������<br>';
						}
					}
				}
			?>
			</center>
			<?php
		}

		if ($page_target == 'location') { //----------------------------------------------
			drawAdminMenu();
			$incoming = $_REQUEST['command_data'];

			if ($command_action == 'addform') {
				$arr = array("server"=>1, "coordx"=>0, "coordy"=>0, "loctype"=>1, "areatype"=>1);
				$formtext = createFormLocation("add", $arr);
				echo $formtext;
			}

			if ($command_action == 'editform') {
					
			}
				
			if ($command_action == 'addact') {
				
				foreach($incoming as $key => $value) {
					$incoming[$key] = intval($value);
				}
				
				$query = mysql_query(
					"SELECT `server_id`, `coordinate_x`, `coordinate_y`
					FROM `mp_headquarters_locations`
					WHERE `server_id` = '".$incoming['server']."' AND
					`coordinate_x` = '".$incoming['coordx']."' AND
					`coordinate_y` = '".$incoming['coordy']."'", $db
				);
				
				if (mysql_num_rows($query) > 0) {
					echo "<font color=red><b>������� ��� ���������� � �������! ��� ��������� ����� ���������� ������!</b></font><br><br>";
				} else {
					$query = mysql_query(
					"INSERT INTO `mp_headquarters_locations`
					 SET `server_id` = '".$incoming['server']."',
					`coordinate_x` = '".$incoming['coordx']."',
					`coordinate_y` = '".$incoming['coordy']."',
					`condition` = '".($incoming['loctype']==1?"protected_location":"excluded_location")."',
					`coverage` = '".($incoming['areatype']==1?"exact_coordinates":"exclude_coordinates")."'"
					, $db);
					echo "<font color=green><b>������� ������� ��������� � ������</b></font><br><br>";
				}
			}
				
			if ($command_action == 'editact') {
					
			}
	
			if ($command_action == 'del') {
				
				$query = mysql_query(
					"DELETE FROM mp_headquarters_locations WHERE
					`server_id` = '".$incoming['server']."' AND
					`coordinate_x` = '".$incoming['coordx']."' AND
					`coordinate_y` = '".$incoming['coordy']."' LIMIT 1", $db
				);
				
				echo "<font color=green><b>������� ������� �������</b></font><br><br>";
			}
			
			if ($command_action != 'addform' && $command_action != 'editform') {
				
				echo "<center><b>�����: <font color=green>���������� ������� �������</font></b><br>";

				$query = mysql_query(
					"SELECT *
					 FROM `mp_headquarters_locations`
					", $db
				);
					
				echo "<table cellpadding='5'>
					<tr align='center'>
						<td bgcolor='#F4ECD4' valign='top' width='200px' align='center'><b>������</b></td>
						<td bgcolor='#F4ECD4' valign='top' width='100px' align='center'><b>�����. X</b></td>
						<td bgcolor='#F4ECD4' valign='top' width='100px' align='center'><b>�����. Y</b></td>
						<td bgcolor='#F4ECD4' valign='top' width='100px' align='center'><b>��� �������</b></td>
						<td bgcolor='#F4ECD4' valign='top' width='100px' align='center'><b>��� �����������</b></td>
						<td bgcolor='#F4ECD4' valign='top' width='100px' align='center'><b>����������</b></td>
					</tr>";
					
	
				while ($b = mysql_fetch_array($query)) {
					$background = "background='http://www.tzpolice.ru//i/bgr-grid-sand".(($counter) ? "" : "1").".gif'";
					$echoserver = ($b['server_id']==1?'����������':'���������');
					$echotype = ($b['condition']=='protected_location'?'���������� �������':'��������� �� ��������');
					$echoarea = ($b['coverage']=='exact_coordinates'?'� �������� �������':'� �������� �������');
	
					echo "<tr>
						<td ".$background."><b>".$echoserver."</b></td>
						<td ".$background."><b>".$b['coordinate_x']."</b></td>
						<td ".$background."><b>".$b['coordinate_y']."</b></td>
						<td ".$background."><b>".$echotype."</b></td>
						<td ".$background."><b>".$echoarea."</b></td>
						<td ".$background." align='center'>
							<input type='button' value='�������' onclick=\"pass_action_to_page('admin_console', 'location', 'del', {
								server: ".$b['server_id'].", 
								coordx: ".$b['coordinate_x'].",
								coordy: ".$b['coordinate_y']."
							});\">
						</td>
					</tr>";
						
					$counter = (++$counter)%2;
				}
	
				echo "</table><br>
				<input type='button' value='�������� �������' onclick=\"pass_action_to_page('admin_console', 'location', 'addform', '')\">
				</center>";
			}
		}
	}
}

function drawAdminMenu() {
		echo
		"<center><b>�����������������</b></center><br/><br/>
		<div align='center'>
			<a href='javascript:{}' onclick=\"load_page('admin_console','cleanup');\">������� ������</a>
				|
			<a href='javascript:{}' onclick=\"load_page('admin_console','admin_list');\">���������� ������� ���������������</a>
				|
			<a href='javascript:{}' onclick=\"load_page('admin_console','location');\">���������� �������� �������</a>
		</div><br/><br/>";
}

function drawMenu() {
		echo
		"<center><b>������ �������</b></center><br/><br/>
		<div align='center'>
			<a href='javascript:{}' onclick=\"load_page('cabinet','');\">��������� ������ �� ������� (���)</a>
				|
			<a href='javascript:{}' onclick=\"load_page('cabinet','evaluate_logs');\">������ �����</a>
				|
			<a href='javascript:{}' onclick=\"load_page('cabinet','usage_history');\">������� ������/�����/������</a>
				|
			<a href='javascript:{}' onclick=\"load_page('cabinet','logs_history');\">������� ������� �����</a>
				|
			<a href='javascript:{}' onclick=\"load_page('cabinet','generate_report');\">��������� ������</a>
		</div><br/><br/>";
}

function ShowJoinPages($CurPage,$TotalPages,$ShowMax) {
	global $action;
	global $page_target;
    $PrevList=floor(($CurPage-1)/$ShowMax)*$ShowMax;
    $NextList=$PrevList+$ShowMax+1;
        if($PrevList>=$ShowMax*2) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$page_target."','1');\" title='� ����� ������'><</a> ";
        if($PrevList>0) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$page_target."','".$PrevList."');\" title='���������� ".$ShowMax." �������'>:</a> ";
    for($i=$PrevList+1;$i<=$PrevList+$ShowMax;$i++) if($i<=$TotalPages) {
            if($i==$CurPage) echo '<u>'.$i.'</u> ';
        else echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$page_target."','{$i}');\">$i</a> ";
    }
    if($NextList<=$TotalPages) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$page_target."','".$NextList."');\" title='��������� ".$ShowMax." �������'>:</a> ";
        if($CurPage<$TotalPages) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$page_target."','".$TotalPages."');\" title='� ����� �����'>></a>";
}


?>