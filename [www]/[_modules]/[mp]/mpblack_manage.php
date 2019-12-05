<?php
if(!defined('MPLIB')) die('Лесом.');
?>
<div align="center">
	<b>
		Настройки доступа к управлению ЧС
	</b>
</div>
<div align="center" style='padding-top:10px;'>
<?php
	$displaySearchPanel = false;
	if (($in['operation'] == "search") && !empty($in['nickName'])) {
		$searchResult = getPersByNick($in['nickName']);
		if (empty($searchResult) || count($searchResult) == 0) {
			echo "Персонаж с указанным ником <b>".$in['nickName']."</b>не найден";
			$displaySearchPanel = true;
		} else if (!isMilitaryPoliceClan($searchResult[0]['clan'])) {
			echo "Персонаж ".genereateUser($searchResult[0])." - не входит в состав клана <b>Military Police</b>";
			$displaySearchPanel = true;
		} else if (alreadyInManagersList($in['nickName'])) {
			echo "Персонаж ".genereateUser($searchResult[0])." - уже в составе списка менеджеров ЧС";
			$displaySearchPanel = true;
		} else {
			// all OK - display confirmation
?>
			<form target="<?php echo $_mp_targetURL; ?>" method="post">
				Подтвердите добавление <?php echo genereateUser($searchResult[0]); ?> в список людей, которым доступно управление списком ЧС.<br />
				<input type="hidden" name="nickName" value="<?php echo $in['nickName']?>" />
				<input type="hidden" name="target" value="management"  />
				<input type="hidden" name="operation" value="add"  />
				<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
				<input type="submit" value="Подтверждаю"  />
			</form>
<?php			
		} 
	} else if (($in['operation'] == "remove") && !empty($in['nickName'])) {
		removeFromBlackListManagersList($in['nickName']);
		echo "<div align='center' style='padding-top:10px;'>Персонаж ".$in['nickName']." удалён из списка менеджеров</b>"; 
		$displaySearchPanel = true;
	} else if (($in['operation'] == "add") && !empty($in['nickName'])) {
		addToFromBlackListManagersList($in['nickName']);
		echo "<div align='center' style='padding-top:10px;'>Персонаж <b>".$in['nickName']."</b> добавлен в список менеджеров</b>"; 
		$displaySearchPanel = true;
	} else {
		$displaySearchPanel = true;
	}	
?>

<?php
	if ($displaySearchPanel) {
?>
	<div align="center" style='padding-top:10px;'>
		<form target="<?php echo $_mp_targetURL; ?>" method="post">
			Добавление ника менеджера ЧС:
			<input type="text" name="nickName" value="<?php echo $in['nickName']?>" />
			<input type="hidden" name="target" value="management"  />
			<input type="hidden" name="operation" value="search"  />
			<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
			<input type="submit" value="Искать"  />
		</form>
	</div>	
<?php
	}
?>	
</div>
<div align="center" style='padding-top:10px;'>
	
	Список менеджеров ЧС:
		
	<table width="100%" cellpadding="5">
		<tr align="center">
		    <td bgcolor="#F4ECD4" valign="middle"><b>Персонаж</b></td>
		    <td bgcolor="#F4ECD4" valign="middle"><b>Операция</b></td>
		</tr>
<?php
	$managers = getBlackListManagersList();
	$counter = 0;
	foreach ($managers as $record) {
		$background = "background='http://www.tzpolice.ru//i/bgr-grid-sand".(($counter) ? "" : "1").".gif'";
?>
		<tr>
			<td style='padding-left: 15px;' nowrap <?php echo $background; ?>>
				<?php echo genereateUser($record); ?>
			</td>
			<td style='padding-left: 15px;' <?php echo $background; ?>>
				<form target="<?php echo $_mp_targetURL; ?>" method="post">
					<input type="hidden" name="target" value="management"  />
					<input type="hidden" name="operation" value="remove"  />
					<input type="hidden" name="act" value="<? echo $_mp_moduleName; ?>"/>
					<input type="hidden" name="nickName" id="nickName" value='<?php echo $record['login']; ?>'/>
					<input type="submit" value="удалить из менеджеров" <?php echo ($record['login'] == $userLoginNickName ? "disabled" : ""); ?> />
				</form>
			</td>
		</tr>
<?php			
		$counter = (++$counter)%2;
	}
?>			
	</table>
		
</div>

