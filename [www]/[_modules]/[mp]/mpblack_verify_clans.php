<?php
if(!defined('MPLIB')) die('�����.');
?>
<div align="center">
	<b>
		�������� ������������ �������� ��
	</b>
</div>
<?php
	if (!empty($in['operation']) && $in['operation'] == "actualize") {
		performBlackListActualization($in['removeFromBL_nickName'], $in['moveToMaradeurBL_nickName']);
		echo "<div align='center' style='padding-top:10px;'>������ �� ��������������...</div>";
	}
	
	$clansVerificationResult = getClansBlackListVerificationResults();
	if (empty($clansVerificationResult) || count($clansVerificationResult) == 0) {
		echo "<div align='center' style='padding-top:10px;'>������ ��������� �� - ����������</div>";
	} else {
		
?>
		<div align="center">
		<br />
		������ �������, ���������� ��-����:
		<form target="<?php echo $_mp_targetURL; ?>" method="post">
			<input type="hidden" name="target" value="verifyClans" />
			<input type="hidden" name="operation" value="actualize"  />
			<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
			<table width="100%" cellpadding="5">
				<tr align="center">
				    <td bgcolor="#F4ECD4" valign="middle" width="150px"><b>������� �� ��</b></td>
				    <td bgcolor="#F4ECD4" valign="middle" width="150px"><b>����������� � �� ��������</b></td>
				    <td bgcolor="#F4ECD4" valign="middle"><b>��������</b></td>
				    <td bgcolor="#F4ECD4" valign="middle"><b>������� ����</b></td>
				</tr>
<?php
				$counter = 0;
				foreach ($clansVerificationResult as $record) {
					$background = "background='http://www.tzpolice.ru//i/bgr-grid-sand".(($counter) ? "" : "1").".gif'";
?>
						<tr>
							<td valign='middle' align="center" <?php echo $background; ?>>
								<input type='checkbox' name='removeFromBL_nickName[]' value='<?php echo $record['login']; ?>' />
							</td>
							<td valign='middle' align="center" <?php echo $background; ?>>
								<input type='checkbox' name='moveToMaradeurBL_nickName[]' value='<?php echo $record['login']; ?>' />
							</td>
							<td style='padding-left: 15px;' nowrap valign='middle' <?php echo $background; ?>>
								<?php echo genereateUser($record); ?>
							</td>
							<td style='padding-left: 15px;' nowrap valign='middle' <?php echo $background; ?>>
								<b><?php echo $record['blackListClan']; ?></b>
							</td>
						</tr>
<?php					
					$counter = (++$counter)%2;
				}
?>
			</table>
			<br  />
			<input type="submit" value="���������������"  />
		</form>
		</div>
<?php		
	}
?>
