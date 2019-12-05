<?php
if(!defined('MPLIB')) die('Лесом.');
?>
<div align="center">
	<b>
		Редактирование записи о персонаже в чёрном списоке
	</b>
</div>
<div align="center" style="padding-top:10px;">
	<form target="<?php echo $_mp_targetURL; ?>" method="get">
		Ник персонажа:
		<input type="text" name="nickName" value="<?php echo $in['nickName']?>" />
		<input type="hidden" name="target" value="editPlayer"  />
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
			if (empty($record)) {
				// no data found
				echo "<div align='center' style='padding-top:10px;'>Персонаж с указанныи ником не находится в ЧС</div>";
			} else {
				//if ($record['alreadyInClanBL']) {
				if (false) {
					echo "<div align='center' style='padding-top:10px;'>".genereateUser($record)." - находится в клановом ЧС. Перевести его в ЧС марадёров?</div>";
?>
					<div align='center' style='padding-top:10px;'>
					<form target="<?php echo $_mp_targetURL; ?>" method="post">
						<input type="hidden" name="nickName" value="<?php echo $in['nickName']?>" />
						<input type="hidden" name="clanName" value="<?php echo $record['blackListClan']; ?>"  />
						<input type="hidden" name="clanSumm" value="<?php echo $record['clanSumm']; ?>"  />
						<input type="hidden" name="target" value="editPlayer"  />
						<input type="hidden" name="operation" value="moveToMaradeur"  />
						<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
						<input type="submit" value="Перевести в ЧС марадёров"  />
					</form>
					</div>
<?php					
				} else {
					// редактирование записи о марадёре
?>
					<form target="<?php echo $_mp_targetURL; ?>" method="post">
					<input type="hidden" name="nickName" value="<?php echo $in['nickName']?>" />
					<input type="hidden" name="target" value="editPlayer"  />
					<input type="hidden" name="operation" value="updateMaradeur"  />
					<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
					<br/><br/>
					<div align="center">
						<table cellspacing="5">
							<tr>
								<td bgcolor="#F4ECD4">Ник:</td>
								<td bgcolor="#F4ECD4"><?php echo genereateUser($record); ?></td>
							</tr>
							<tr>
								<td bgcolor="#F4ECD4" nowrap="nowrap">Сумма выхода выхода из ЧС:</td>
								<td bgcolor="#F4ECD4"><input type="text" name="summ" value="<?php echo $record['summ']; ?>" maxlength="26" size=70 /></td>
							</tr>
							<tr>
								<td bgcolor="#F4ECD4" nowrap="nowrap">Комментарий:</td>
								<td bgcolor="#F4ECD4"><input type="text" name="comment" size=70 maxlength="255" value="<?php echo $record['comment']; ?>" /></td>
							</tr>
							<tr>
								<td bgcolor="#F4ECD4">Список логов (через запятую, максимум 255 символов):</td>
								<td bgcolor="#F4ECD4"><textarea name="logs" id="logs" cols="70" rows="3"><?php echo $record['logs']; ?></textarea></td>
							</tr>
						</table>
						<br/>
						<input type="submit" value="Сохранить изменения" />
					</div>
					</form>
<?php				
				}
			}
		} else if ($in['operation'] == 'moveToMaradeur') {
			movePlayerToMaradeurBlackList($in['nickName'], $in['clanName'], $in['clanSumm']);
			echo "<div align='center' style='padding-top:10px;'>Персонаж <b>".$in['nickName']."</b> - маркирован как марадёр в ЧС</div>";
		} else if ($in['operation'] == 'updateMaradeur') {
			updatePlayerInMaradeurBlackList($in['nickName'], $in['summ'], $in['comment'], $in['logs']);
			echo "<div align='center' style='padding-top:10px;'>Запись о марадёре <b>".$in['nickName']."</b> - проапдейчена в ЧС</div>";
		}
	}
?>
