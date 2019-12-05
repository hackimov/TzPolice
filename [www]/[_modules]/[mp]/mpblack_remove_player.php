<?php
if(!defined('MPLIB')) die('Лесом.');
?>
<div align="center">
	<b>
		Удаление персонажа из чёрного списока
	</b>
</div>
<div align="center" style='padding-top:10px;'>
	<form target="<?php echo $_mp_targetURL; ?>" method="get">
		Ник персонажа:
		<input type="text" name="nickName" value="<?php echo $in['nickName']?>" />
		<input type="hidden" name="target" value="removePlayer" />
		<input type="hidden" name="operation" value="search"  />
		<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
		<input type="submit" value="Искать"  />
	</form>
</div>
<?php
	if (!empty($in['nickName']) && !empty($in['operation'])) {
		if ($in['operation'] == 'search') {
			$records = getPlayerFromBlackList($in['nickName']);
			$record = $records[0];
			if (!empty($record)) {
?>
				<div align="center" style='padding-top:10px;'>
				<form target="<?php echo $_mp_targetURL; ?>" method="get">
					Подтвердите удаление персонажа <?php echo genereateUser($record); ?> из ЧС:
					<br/><br/>
					<input type="hidden" name="nickName" value="<?php echo $in['nickName']?>" />
					<input type="hidden" name="target" value="removePlayer" />
					<input type="hidden" name="operation" value="remove"  />
					<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
					<input type="submit" value="Подтверждаю"  />
				</form>
				</div>
<?php
			} else {
				echo "<div align='center' style='padding-top:10px;'>Указанный персонаж в ЧС отсутствует</div>";
			}
		} else if ($in['operation'] == 'remove') {
			removePlayersFromBlackList($in['nickName']);
			echo "<div align='center' style='padding-top:10px;'>Персонаж <b>".$in['nickName']."</b> - удалён из ЧС</div>";
		}
	}
?>
