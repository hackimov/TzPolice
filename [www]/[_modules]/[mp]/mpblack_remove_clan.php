<?php
if(!defined('MPLIB')) die('�����.');
?>
<div align="center">
	<b>
		�������� ����� �� ������� �������
	</b>
</div>
<div align="center" style='padding-top:10px;'>
	<form target="<?php echo $_mp_targetURL; ?>" method="get">
		�������� �����:
		<input type="text" name="clanName" value="<?php echo $in['clanName']?>" />
		<input type="hidden" name="target" value="removeClan" />
		<input type="hidden" name="operation" value="search"  />
		<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
		<input type="submit" value="������"  />
	</form>
</div>
<?php
	if (!empty($in['clanName']) && !empty($in['operation'])) {
		if ($in['operation'] == 'search') {
			if (isClanExistInBlackList($in['clanName'])) {
?>
				<div align="center" style='padding-top:10px;'>
				<form target="<?php echo $_mp_targetURL; ?>" method="get">
					����������� �������� ����� <b><?php echo $in['clanName']; ?></b> �� �� 
					(��� ��������� �� ������� �����, ����� ��������� ��� ������� - ����� ������� �� ��):
					<br/><br/>
					<input type="hidden" name="clanName" value="<?php echo $in['clanName']?>" />
					<input type="hidden" name="target" value="removeClan" />
					<input type="hidden" name="operation" value="remove"  />
					<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
					<input type="submit" value="�����������"  />
				</form>
				</div>
<?php
			} else {
				echo "<div align='center' style='padding-top:10px;'>��������� ���� � �� �����������</div>";
			}
		} else if ($in['operation'] == 'remove') {
			removeClanFromBlackList($in['clanName']);
			echo "<div align='center' style='padding-top:10px;'>���� <b>".$in['clanName']."</b> - ����� �� ��. ��� ������ ������� �����, ������������� ��� �������� �� - ����� ������� �� ��</div>";
		}
	}
?>
