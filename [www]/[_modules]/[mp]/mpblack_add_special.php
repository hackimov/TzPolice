<?php
if(!defined('MPLIB')) die('�����.');
?>
<div align="center">
	<b>
		���������� ��������� � ������ ������
	</b>
</div>
<div align="center" style="padding-top:10px;">
	<form target="<?php echo $_mp_targetURL; ?>" method="get">
		��� ���������:
		<input type="text" name="nickName" value="<?php echo $in['nickName']?>" />
		<input type="hidden" name="target" value="addSpecial"  />
		<input type="hidden" name="operation" value="search"  />
		<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
		<input type="submit" value="������"  />
	</form>
</div>

<?php
	if (!empty($in['nickName']) && !empty($in['operation'])) {
		if ($in['operation'] == 'search') {
			$displayResult = getPersByNick($in['nickName']);
			if (empty($displayResult) || count($displayResult) != 1) {
				// no data found
				echo "<div align='center' style='padding-top:10px;'>�������� � ��������� ����� �� ������</div>";
			} else {
				$record = $displayResult[0];
				if ($record['alreadyInList']) {
?>
					<div align='center' style='padding-top:10px;'>
						<?php echo genereateUser($record); ?> - ��� ��������� � �������.
						<form target="<?php echo $_mp_targetURL; ?>" method="post">
							<input type="hidden" name="nickName" value="<?php echo $record['login']?>" />
							<input type="hidden" name="target" value="editPlayer"  />
							<input type="hidden" name="operation" value="search"  />
							<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
							<input type="submit" value="�������������"  />
						</form>
					</div>
<?php
				} else {
?>
					<form target="<?php echo $_mp_targetURL; ?>" method="post">
					<input type="hidden" name="nickName" value="<?php echo $in['nickName']?>" />
					<input type="hidden" name="target" value="addSpecial"  />
					<input type="hidden" name="operation" value="add"  />
					<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
					<br/><br/>
					<div align="center">
						<table cellspacing="5">
							<tr>
								<td bgcolor="#F4ECD4">���:</td>
								<td bgcolor="#F4ECD4"><?php echo genereateUser($record); ?></td>
							</tr>
							<tr>
								<td bgcolor="#F4ECD4" nowrap="nowrap">����� ������ ������ �� ��:</td>
								<td bgcolor="#F4ECD4"><input type="text" name="summ" size=70 maxlength=26 /></td>
							</tr>
							<tr>
								<td bgcolor="#F4ECD4" nowrap="nowrap">�����������:</td>
								<td bgcolor="#F4ECD4"><input type="text" name="comment" size=70 maxlength=255 /></td>
							</tr>
							<tr>
								<td bgcolor="#F4ECD4">������ ����� (����� �������, �������� 255 ��������):</td>
								<td bgcolor="#F4ECD4"><textarea cols="70" rows="3" name="logs" id="logs"></textarea></td>
							</tr>
						</table>
						<br/>
						<input type="submit" value="��������" />
					</div>
					</form>
<?php				
				}
			}
		} else if ($in['operation'] == 'add') {
			addPlayerToSpecialList($in['nickName'], $in['summ'], $in['comment'], $in['logs']);
			echo "<div align='center' style='padding-top:10px;'>�������� <b>".$in['nickName']."</b> - �������� � ��</div>";
		}
	}
?>
