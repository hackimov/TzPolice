<?php
if(!defined('MPLIB')) die('�����.');
?>
<div align="center">
	<b>
		���������� ����� � ������ ������
	</b>
</div>
<div align="center" style="padding-top:10px;">
	<form target="<?php echo $_mp_targetURL; ?>" method="get">
		�������� �����:
		<input type="text" name="clanName" value="<?php echo $in['clanName']?>" />
		<input type="hidden" name="target" value="addClan" />
		<input type="hidden" name="operation" value="search"  />
		<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
		<input type="submit" value="������"  />
	</form>
</div>
<?php
	if (!empty($in['clanName']) && !empty($in['operation'])) {
		if ($in['operation'] == 'search') {
			$displayResult = getClanMembers($in['clanName']);
			if (empty($displayResult) || count($displayResult) == 0) {
				// no data found
				echo "<div align='center' style='padding-top:10px;'>�� ������� �� ������ ������ � ������ <b>".$in['clanName']."</b></div>";
			} else {
?>
				<div align="center" style="padding-top:10px;">
				<form target="<?php echo $_mp_targetURL; ?>" method="post">
				<input type="hidden" name="target" value="addClan" />
				<input type="hidden" name="clanName" value="<?php echo $in['clanName']?>" />
				<input type="hidden" name="operation" value="add"  />
				<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
				<br/><br/>
				<div align="center">
					����� ������ ����� �� ��: <input type="text" name="summ" value="10�" />
				</div>
				<br/><br/>
				������ ��� ��������� � �������� ��:
				<table width="100%" cellpadding="5">
					 <tr align="center">
					    <td bgcolor="#F4ECD4" valign="middle" width="150px"><b>�������� � �������� ��</b></td>
					    <td bgcolor="#F4ECD4" valign="middle"><b>��������</b></td>
				     </tr>
<?php
				$counter = 0;
				foreach ($displayResult as $record) {
					if ($record['alreadyInMaradeurBL']) continue;
					$background = "background='http://www.tzpolice.ru//i/bgr-grid-sand".(($counter) ? "" : "1").".gif'";
					$selector = "";
					if ($record['alreadyInClanBL']) {
						$selector = "�������� ��� ��������� � �������� ��";
					} else {
						$selector = "<input type='checkbox' name='addToBL_nickName[]' value='".$record['login']."' checked='checked' />";
					}
					echo "
						<tr>
							<td align='center' valign='middle' ".$background.">".$selector."</td>
					    	<td style='padding-left: 15px;' nowrap valign='middle' ".$background.">".genereateUser($record)."</td>
						</tr>
					";
					$counter = (++$counter)%2;
				}
?>
			</table>
			<br/><br/><br/>
			������ ��������� ��� ��������� � ���������� ��:
			<table width="100%" cellpadding="5">
				 <tr align="center">
				    <td bgcolor="#F4ECD4" valign="middle" width="150px"><b>����������� � �������� ��</b></td>
				    <td bgcolor="#F4ECD4" valign="middle"><b>��������</b></td>
			     </tr>
<?php
			$counter = 0;
			foreach ($displayResult as $record) {
				if (!$record['alreadyInMaradeurBL']) continue;
				$background = "background='http://www.tzpolice.ru//i/bgr-grid-sand".(($counter) ? "" : "1").".gif'";
				$selector = "<input type='checkbox' name='moveToBL_nickName[]' value='".$record['login']."' />";
				echo "
					<tr>
						<td align='center' valign='middle' ".$background.">".$selector."</td>
				    	<td style='padding-left: 15px;' nowrap valign='middle' ".$background.">".genereateUser($record)."</td>
					</tr>
				";
				$counter = (++$counter)%2;
			}
?>
			</table>
			<br />
			<input type="submit" value="���������� ��������"  />
			</form>
			</div>
<?php
			}
		} else if ($in['operation'] == 'add') {
			addClanToBlackList($in['clanName'], $in['summ'], $in['addToBL_nickName'], $in['moveToBL_nickName']);
			echo "<div align='center' style='padding-top:10px;'>���� <b>".$in['clanName']."</b> - �������� � ��</div>";
		}
	}
?>
