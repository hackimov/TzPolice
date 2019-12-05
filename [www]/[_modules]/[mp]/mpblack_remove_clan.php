<?php
if(!defined('MPLIB')) die('Лесом.');
?>
<div align="center">
	<b>
		Удаление клана из чёрного списока
	</b>
</div>
<div align="center" style='padding-top:10px;'>
	<form target="<?php echo $_mp_targetURL; ?>" method="get">
		Название клана:
		<input type="text" name="clanName" value="<?php echo $in['clanName']?>" />
		<input type="hidden" name="target" value="removeClan" />
		<input type="hidden" name="operation" value="search"  />
		<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
		<input type="submit" value="Искать"  />
	</form>
</div>
<?php
	if (!empty($in['clanName']) && !empty($in['operation'])) {
		if ($in['operation'] == 'search') {
			if (isClanExistInBlackList($in['clanName'])) {
?>
				<div align="center" style='padding-top:10px;'>
				<form target="<?php echo $_mp_targetURL; ?>" method="get">
					Подтвердите удаление клана <b><?php echo $in['clanName']; ?></b> из ЧС 
					(все персонажи из данного клана, кроме занесённых как мародёры - будут удалены из ЧС):
					<br/><br/>
					<input type="hidden" name="clanName" value="<?php echo $in['clanName']?>" />
					<input type="hidden" name="target" value="removeClan" />
					<input type="hidden" name="operation" value="remove"  />
					<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
					<input type="submit" value="Подтверждаю"  />
				</form>
				</div>
<?php
			} else {
				echo "<div align='center' style='padding-top:10px;'>Указанный клан в ЧС отсутствует</div>";
			}
		} else if ($in['operation'] == 'remove') {
			removeClanFromBlackList($in['clanName']);
			echo "<div align='center' style='padding-top:10px;'>Клан <b>".$in['clanName']."</b> - удалён из ЧС. Все игроки данного клана, маркированные как клановый ЧС - также удалены из ЧС</div>";
		}
	}
?>
