<?php
if(!defined('MPLIB')) die('�����.');
?>
<div align="center">
	<b>
		������� ��������� � ������ ������
	</b>
</div>

<div align="center" style="padding-top:10px;">
	<form target="<?php echo $_mp_targetURL; ?>" method="get">
		��� ���������:
		<input type="text" name="nickName" value="<?php echo $in['nickName']?>" />
		<input type="hidden" name="target" value="toBlack"  />
		<input type="hidden" name="operation" value="search"  />
		<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
		<input type="submit" value="������"  />
	</form>
</div>

<?php
	if (!empty($in['nickName']) && !empty($in['operation'])) {
		
		if ($in['operation'] == 'search') {
			
			$records = getPlayerFromBlackList($in['nickName']);
			$record = $records[0];
			
			if (empty($record)) {
				// no data found
				echo "<div align='center' style='padding-top:10px;'>�������� � ��������� ����� ����������� � �������, ������ ����������.</div>";
			} else {
				
?>
					<form target="<?php echo $_mp_targetURL; ?>" method="post">
					<input type="hidden" name="nickName" value="<?php echo $in['nickName']?>" />
					<input type="hidden" name="target" value="toBlack"  />
					<input type="hidden" name="operation" value="toBlack"  />
					<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
					<br/><br/>
					<div align="center">
						<table cellspacing="5">
							<tr>
								<td bgcolor="#F4ECD4">���:</td>
								<td bgcolor="#F4ECD4"><?php echo genereateUser($record); ?></td>
							</tr>
						</table>
						<br/>
						<input type="submit" value="��������� � ��" />
					</div>
					</form>
<?php				
				
			}
		} else if ($in['operation'] == 'toBlack') {
			changePlayerList($in['nickName'], 0);
			echo "<div align='center' style='padding-top:10px;'>�������� <b>".$in['nickName']."</b> ��������� � ��</div>";
		}
	}
?>
