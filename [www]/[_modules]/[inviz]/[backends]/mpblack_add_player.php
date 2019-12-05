<?php
if(!defined('MPLIB')) die('Лесом.');
?>
<div align="center">
	<b>
		Добавление мародёра в чёрный список
	</b>
</div>
<div align="center" style="padding-top:10px;">
	<form target="<?php echo $_mp_targetURL; ?>" method="get">
		Ник персонажа:
		<input type="text" name="nickName" value="<?php echo $in['nickName']?>" />
		<input type="hidden" name="target" value="addPlayer"  />
		<input type="hidden" name="operation" value="search"  />
		<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
		<input type="submit" value="Искать"  />
	</form>
</div>
<?php
	if (!empty($in['nickName']) && !empty($in['operation'])) {
		if ($in['operation'] == 'search') {
			$displayResult = getPersByNick($in['nickName']);
			if (empty($displayResult) || count($displayResult) != 1) {
				// no data found
				echo "<div align='center' style='padding-top:10px;'>Персонаж с указанныи ником не найден</div>";
			} else {
				$record = $displayResult[0];
				if ($record['alreadyInList']) {
?>
					<div align='center' style='padding-top:10px;'>
						<?php echo genereateUser($record); ?> - уже находится в ЧС
						<form target="<?php echo $_mp_targetURL; ?>" method="post">
							<input type="hidden" name="nickName" value="<?php echo $record['login']?>" />
							<input type="hidden" name="target" value="editPlayer"  />
							<input type="hidden" name="operation" value="search"  />
							<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
							<input type="submit" value="Редактировать"  />
						</form>
					</div>
<?php
				} else {
?>
					<form target="<?php echo $_mp_targetURL; ?>" method="post">
					<input type="hidden" name="nickName" value="<?php echo $in['nickName']?>" />
					<input type="hidden" name="target" value="addPlayer"  />
					<input type="hidden" name="operation" value="add"  />
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
								<td bgcolor="#F4ECD4"><input type="text" name="summ" style="width:95%;" maxlength="16" /></td>
							</tr>
							<tr>
								<td bgcolor="#F4ECD4" nowrap="nowrap">Комментарий:</td>
								<td bgcolor="#F4ECD4"><input type="text" name="comment" style="width:95%;" maxlength="50" /></td>
							</tr>
							<tr>
								<td bgcolor="#F4ECD4">Список логов (через запятую, максимум 255 символов):</td>
								<td bgcolor="#F4ECD4"><textarea cols="40" rows="3" name="logs" id="logs"></textarea></td>
							</tr>
						</table>
						<br/>
						<input type="submit" value="Добавить" />
					</div>
					</form>
<?php				
				}
			}
		} else if ($in['operation'] == 'add') {
			addPlayerToBlackList($in['nickName'], $in['summ'], $in['comment'], $in['logs']);
			echo "<div align='center' style='padding-top:10px;'>Мародёр <b>".$in['nickName']."</b> - добавлен в ЧС</div>";
		}
	}
?>
