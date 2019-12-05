<?php
$_RESULT = array("res" => "ok");
require_once("/home/sites/police/www/_modules/inviz/backends/mp_join_config.php"); // todo actualize?

$JsHttpRequest =& new Subsys_JsHttpRequest_Php("windows-1251");
#$JsHttpRequest = new Subsys_JsHttpRequest_Php("windows-1251"); // todo - comment?

function ShowJoinPages($CurPage,$TotalPages,$ShowMax) {
	global $action;
	global $target;
	global $param;
    $PrevList=floor(($CurPage-1)/$ShowMax)*$ShowMax;
    $NextList=$PrevList+$ShowMax+1;
        if($PrevList>=$ShowMax*2) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','1');\" title='В самое начало'>«</a> ";
        if($PrevList>0) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','".$PrevList."');\" title='Предыдущие ".$ShowMax." страниц'>…</a> ";
    for($i=$PrevList+1;$i<=$PrevList+$ShowMax;$i++) if($i<=$TotalPages) {
            if($i==$CurPage) echo '<u>'.$i.'</u> ';
        else echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','{$i}');\">$i</a> ";
    }
    if($NextList<=$TotalPages) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','".$NextList."');\" title='Следующие ".$ShowMax." страниц'>…</a> ";
        if($CurPage<$TotalPages) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','".$TotalPages."');\" title='В самый конец'>»</a>";
}


extract($_REQUEST);

// ------ построение меню для работы с анкетами :: begin
if ($join_access && ($hide_menu != 1)) {
?>
	<table width="85%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td align="left" width='20%'>
				<?php
					$r = mysql_query(
						"
							SELECT *
							FROM `mp_join_entry`
							WHERE `status` = '1'
						", $db
					);
				?>
				<a href="javascript:{}" onclick="show_entry('lists','free','','1');">Свободные заявки (<?php echo mysql_num_rows($r); ?>)</a>
			</td>
			<td align="left" width='20%'>
				<?php
					if ($join_revisor) {
						$r = mysql_query(
							"
								SELECT *
								FROM `mp_join_entry`
								WHERE
									`revisor` = '".AuthUserName."'
									AND `status` NOT IN ('1', '5', '6', '7')
							", $db
						);
				?>
				<a href="javascript:{}" onclick="show_entry('lists','my','','1');">Мои заявки (<?php echo mysql_num_rows($r); ?>)</a>
				<?php
					} else {
				?>
					&nbsp;
				<?php
					}
				?>
			</td>
			<td align="left" width='20%'>
				<?php
					$r = mysql_query(
						"
							SELECT *
							FROM `mp_join_entry`
							WHERE `status` NOT IN ('1', '5', '6', '7')
						", $db
					);
				?>
				<a href="javascript:{}" onclick="show_entry('lists','inProgress','','1');">В обработке (<?php echo mysql_num_rows($r); ?>)</a>
			</td>
			<td align="left" width='20%'><a href="javascript:{}" onclick="show_entry('lists','archive','','1');">Архив</a></td>
			<td align="left" width='20%'>
				<?php
					$r = mysql_query(
						"
							SELECT *
							FROM `mp_join_entry`
							WHERE `status` = '7'
						", $db
					);
				?>
				<a href="javascript:{}" onclick="show_entry('lists','reserve','','1');">Резерв (<?php echo mysql_num_rows($r); ?>)</a>
			</td>
			<td align="left" width='20%'><a href="javascript:{}" onclick="load_page('statistics','show');">Статистика</a></td>
		</tr>
		<?php if ($join_admin) { ?>
		<tr height="6">
			<td align="left" colspan="5"></td>
		</tr>
		<tr>
			<td align="left"><a href="javascript:{}" onclick="show_search();">Поиск</a></td>
			<td align="left">
				<b>Настройки:</b><br />
				<a href="javascript:{}" onclick="load_page('edit_security','show');">Доступ</a><br />
				<a href="javascript:{}" onclick="load_page('edit_questions','show');">Вопросы анкеты</a>
			</td>
			<td align="left"><a href="javascript:{}">Показать заявки</a>
			<select onchange="show_entry('lists','other',this.value,'1');">
<?php
			$SQL = 'SELECT `revisor` FROM `mp_join_entry` GROUP BY `revisor` ORDER BY `revisor` ASC';
			$r = mysql_query($SQL);
			while($d = mysql_fetch_assoc($r)) {
				echo '<option on onclick="show_entry(\'lists\',\'other\',\''.$d['revisor'].'\',\'1\');" value="'.$d['revisor'].'">'.$d['revisor'].'</option>';
			}
?>
			</select>
			</td>
			<td align="left"><a href="javascript:{}" onclick="load_page('show_actualy','show');">Действующий состав</a></td>
			<td align="left"></td>
			<td align="left"></td>
		</tr>
		<tr id="search_form" style="display:none;">
			<td align="left" colspan="6"><input type="text" size="20" id="serach_nick" /><input type="button" value="  искать  " name="search_btn" onclick="search_entry();" /></td>
		</tr>
		<?php } ?>
	</table>
	<br /><br />
	<?php
	}
// ------ построение меню для работы с анкетами :: end

// ------ статистика :: begin
if (($action == 'statistics') && $join_access) {
	if ($target == 'show') {
		if (empty($statisticsStart)) {
			$statisticsStartTimeStamp = mktime() - 7 * 3600 * 24; // 7 days in past
			$statisticsStart = date("d/m/Y", $statisticsStartTimeStamp);
		} else {
			//$dateTemp = DateTime::createFromFormat("d/m/Y", $statisticsStart);
			//$statisticsStartTimeStamp = $dateTemp->getTimestamp();
			$statisticsStartTimeStamp = convertStringToTimestamp($statisticsStart, true);
		}

		if (empty($statisticsEnd)) {
			$statisticsEndTimeStamp = mktime();
			$statisticsEnd = date("d/m/Y", $statisticsEndTimeStamp);
		} else {
			//$dateTemp = DateTime::createFromFormat("d/m/Y", $statisticsEnd);
			//$statisticsEndTimeStamp = $dateTemp->getTimestamp();
			$statisticsEndTimeStamp = convertStringToTimestamp($statisticsEnd, false);
		}
		?>
		<table cellpadding=3 width='300' cellspacing=3 align='center'>
			<tr>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center' colspan=2>
					Статистика:
				</th>
			</tr>
			<tr>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="20">
					С:
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>
					<input
						type="text"
						name="statisticStart"
						id="statisticStart"
						value="<?php echo $statisticsStart; ?>"> (dd/mm/YYYY)
				</td>
			</tr>
			<tr>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>
					По:
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>
					<input
						type="text"
						name="statisticEnd"
						id="statisticEnd"
						value="<?php echo $statisticsEnd; ?>"> (dd/mm/YYYY)
				</td>
			</tr>
			<tr>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' colspan=2 align='center'>
					<input
						type="button"
						name="generateStatistics"
						onclick="generateStatistics(document.getElementById('statisticStart').value, document.getElementById('statisticEnd').value);"
						value="Показать">
				</td>
			</tr>
			<tr>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' colspan=2 style='padding-top: 15px;'>
					<?php
						$r = mysql_query(
							"
								SELECT *
								FROM `mp_join_entry`
								WHERE `start_date` BETWEEN $statisticsStartTimeStamp AND $statisticsEndTimeStamp
							", $db
						);
					?>
					Анкет поступило: <b><?php echo mysql_num_rows($r);?></b>
					<br/>
					<br/>
					<?php
						$r = mysql_query(
							"
								SELECT
									count(*) as `count`,
									`status`
								FROM `mp_join_entry`
								WHERE `end_date` BETWEEN $statisticsStartTimeStamp AND $statisticsEndTimeStamp
									AND `status` IN ('5', '6', '7')
								GROUP BY `status`
							", $db
						);
						$statisticsResultRejected = 0;
						$statisticsResultOK = 0;
						$statisticsResultOnHold = 0;
						while($d=mysql_fetch_assoc($r)) {
							if ($d['status'] == '5') {
								// принят в МП
								$statisticsResultOK = $d['count'];
							}
							if ($d['status'] == '6') {
								// анкета отклонена
								$statisticsResultRejected = $d['count'];
							}
							if ($d['status'] == '7') {
								// резерв
								$statisticsResultOnHold = $d['count'];
							}
						}
					?>
					Анкет обработано: <b><?php echo $statisticsResultRejected + $statisticsResultOK + $statisticsResultOnHold; ?></b>
					<br/>
					Из них отклонено: <?php echo $statisticsResultRejected; ?>
					<br/>
					Из них одобренно: <?php echo $statisticsResultOK; ?>
					<br/>
					Из них в резерве: <?php echo $statisticsResultOnHold; ?>
				</td>
			</tr>
		</table>
		<?php
	}
}
// ------ статистика :: end

// ------ страница отображения информации для пользователя :: begin
if($action=='main_user'){
	$SQL = 'SELECT * FROM `mp_join_entry` WHERE `nick`=\''.AuthUserName.'\' AND `status` NOT IN ("5", "6", "7") ORDER BY `start_date` DESC LIMIT 1;';
	$r = mysql_query($SQL);
	$ex = mysql_num_rows($r);
	$e = mysql_fetch_assoc($r);

	if($ex==1){
		if($e['status']=='1'){$status='В очереди на рассмотрение';}
		if($e['status']=='2'){$status='Принята на рассмотрение';}
		if($e['status']=='3'){$status='Свяжитесь с <font color=black>'.prepareUserInfoForDrawing($e['revisor']).'</font> для проведения собеседования';}
		if($e['status']=='4'){$status='Ожидается вынесение решения по заявке';}
	?>
		<table cellpadding=3 width=95% cellspacing=3>
			<th colspan=3 background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>Заявка на вступление:</th>
			<tr>
				<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<b>Статус заявки:</b></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<font color="green"><b><?php echo $status; ?></b></font></td>
			</tr>
<?php
		if($e['revisor']!=''){
?>
			<tr>
				<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;Заявку рассматривает:</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php echo prepareUserInfoForDrawing($e['revisor']); ?></td>
			</tr>
			<?php
		}
		?>
			<tr>
				<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;№:</b></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;Вопрос:</b></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;Ответ:</b></td>
			</tr>
		<?php
		$SQL='SELECT * FROM `mp_join_answ` WHERE entry_id=\''.$e['id'].'\' order by answer_num asc';
		$r=mysql_query($SQL);
		while($d=mysql_fetch_assoc($r)) {

			?>
			<tr>
				<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;<?php echo $d['answer_num']; ?></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php echo $d['question']; ?></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php
				if($d['field_type']=='nick'){
					if($e["clan"]!=''){
						echo '<img src="http://www.timezero.ru/i/clans/'.$e['clan'].'.gif" style="vertical-align:text-bottom">';
					} else {
						echo "<img src=\"".$siteLocation."_modules/inviz/blank.gif\" width=\"28\" height=\"16\">";
					}
					echo AuthUserName;
					if($e["lvl"]!=0){ echo "[".$e["lvl"]."]"; }
					?><a target="_blank" href="http://www.timezero.ru/info.html?<?php echo AuthUserName; ?>">
					<img border="0" src="http://www.timezero.ru/i/i<?php echo $e["pro"]; if($e["man"]==0){echo "w";} ?>.gif" align="middle" style="vertical-align:text-bottom"></a>
					<?php
				} else {
					echo $d['answer_text'];
				}
				?></td>
			</tr>
			<?php
		}

		?></table>
		<?php
	}

	if($ex==0){
		?>
		<div align="right">
			<a onclick="load_page('main_user','');" href="javascript:{}">Условия вступления</a>
			|
			<a onclick="load_page('add_entry','form');" href="javascript:{}">Подать заявку</a>
		</div>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td align="left">
					Уважаемые игроки!<br />
					Здесь Вы можете получить информацию и подать заявку на вступление в ряды
					<img src="<?php echo $siteLocation; ?>_imgs/clans/Military Police.gif" />
						<b>Military Police</b>.<br />
					<br />
				</td>
			</tr>
			<tr>
				<td align="left">
					<b>
						<font color="blue">Наши требования:</font>
					</b>
					<table>
						<tbody>
							<tr>
								<td class="quote-news">
									<b><font color="brown">Реальный возраст:</font></b> <b>от 18 лет</b>
									<br><b><font color="brown">Профессия:</font></b> <b>Любая, кроме профессии "корсар"</b>
									<br>20 уровень или выше;
									<br>4 фракционное звание Rangers или выше; 
									<br>PVP звание: генерал-майор или выше; 
									<br>Фракционный комплект брони и оружия по уровню.
									<br>
									<br><b><font color="brown">Условия приема:</b></font>
									<br>1. Прохождение обязательной процедуры собеседования, проводимой Отделом Кадров <img src="<?php echo $siteLocation; ?>_imgs/clans/Military Police.gif" /><b>Military Police</b>.
									<br>2. Понимание роли <img src="<?php echo $siteLocation; ?>_imgs/clans/Military Police.gif" /><b>Military Police</b> в Игре.
									<br>3. Отсутствие ЧС Полиции ТЗ, а также Админ городов и кланов/городов с которыми действует пакт о мире/сотрудничестве.
									<br>4. Конфликтоустойчивость и вежливость.
									<br>5. Дисциплинированность, готовность исполнять приказы, умение работать в команде. 
									<br>6. Время в игре не менее 30 часов в неделю.
									<br>7. Отсутствие мультов с профессией "корсар".
									<br>8. Отсутствие мультидоступа к персонажу.
								</td>
							</tr>
						</tbody>
					</table>
					<br />
					<b>Причины, по которым кандидатам может быть отказано в прохождении службы в органах
					<img src="<?php echo $siteLocation; ?>_imgs/clans/Military Police.gif" /><b>Military Police</b>:</b><br />
					а) Нарушение законов ТО.<br />
					б) Нарушение служебных инструкций, действий, не соответствующих этическим нормам службы в <img src="<?php echo $siteLocation; ?>_imgs/clans/Military Police.gif" /><b>Military Police</b>.<br />
					в) Нарушение приказов вышестоящего начальства.<br />
					г) Профессиональная непригодность.<br />
					д) Отдел Кадров <img src="<?php echo $siteLocation; ?>_imgs/clans/Military Police.gif" /><b>Military Police</b> имеет право отказать кандидату в рассмотрении заявки без объяснения причин.<br />
					<br/>
					Отправить своё резюме по типовой форме вы сможете, воспользовавшись сервисом на нашем сайте.
					Ответ вы получите в любом случае. В случае положительного ответа с вами свяжется
					Офицер <img src="<?php echo $siteLocation; ?>_imgs/clans/Military Police.gif" /><b>Military Police</b>
					для проведения собеседования. Если ваша кандидатура будет одобрена, с вами свяжутся для осуществления
					дальнейших шагов по вступлению в ряды Military Police.
					<br />
					<br />
					<b><font color=red>Напоминаем:</font></b> Дублирования анкет, попытки «ускорить» рассмотрение вашей кандидатуры путем телеграмм/приватов
					в игре ведут лишь к автоматическому отказу и исключения вас из списка кандидатов.<br />
					<br />
				</td>
			</tr>
			<tr>
				<td align="left">
					Если Вы соответствуете нашим требованиям, принимаете все наши условия и готовы к нелегкой работе, то вы можете <a href="javascript:{}" onclick="load_page('add_entry','form');">подать заявку на вступление</a><br />
					<br />
					<br />
					С уважением, <br />
					<b>Отдел кадров <img src="<?php echo $siteLocation; ?>_imgs/clans/Military Police.gif" />Military Police</b>
				</td>
			</tr>

		<?php
		$SQL="SELECT * FROM mp_join_entry where nick='".AuthUserName."' and (status='6' or status='5' or status='7') order by start_date desc limit 1;";
		$r=mysql_query($SQL);
		$ex_old= mysql_num_rows($r);
		$e=mysql_fetch_assoc($r);
		if($ex_old==1){
			if($e['status']==7){$st="вы внесены в <font color=green><b>резерв</b></font> кандидатов на вступление в <b><img src='".$siteLocation."_imgs/clans/Military Police.gif' />Military Police</b>. Мы свяжемся с вами.";}
			if($e['status']==6){$st="вы получили <font color=red><b>отказ</b></font><br>Причина отказа: <font color=red><b>".nl2br($e['status_comment'])."</b></font>";}
			if($e['status']==5){$st="вас <font color=green><b>приняли</b></font> в ряды <img src='".$siteLocation."_imgs/clans/Military Police.gif' />Military Police</b>.";}
			?>
			<tr height="25"><td></td></tr>
			<tr>
				<td align="left" background='i/bgr-grid-sand.gif'>
				Вы уже подавали заявку на вступление: <?php echo date("d.m.y H:i:s",$e['start_date']); ?><br />
				Заявка была рассмотрена: <?php echo date("d.m.y H:i:s",$e['end_date']); ?><br />
				По заявке <?php echo $st; ?>
				</td>
			</tr>
			<?php
		}
		?></table>
		<?php
	}
}
// ------ страница отображения информации для пользователя :: begin


// ---- работа с вопросами (секция администрирования) :: begin
if(($action=="edit_questions") && $join_admin){
	if($target=="show"){
		?>
		<table width="85%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td align="left"><a href="javascript:{}" onclick="load_page('main_user','');">Пользовательская страница</a></td>
				<td width="20"></td>
				<td align="left"><a href="javascript:{}" onclick="load_page('edit_questions','add');">Добавить вопрос</a></td>
			</tr>
		</table>
		<table cellpadding=3 width=90% cellspacing=3>
			<th colspan=4  background='i/bgr-grid-sand.gif'>Вопросы:</th>
        	<tr>
				<td width="30" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;№:</b></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;Текст вопроса:</b></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;Тип поля:</b></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
			</tr>
		<?php
		$SQL="SELECT * FROM mp_join_questions order by id asc";
		$r=mysql_query($SQL);
		while($d=mysql_fetch_assoc($r)) {
			if($d['field_type']=="one_string"){$type="Однострочное";}
			if($d['field_type']=="multi_string"){$type="Многострочное";}
			if($d['field_type']=="callendar"){$type="Календарь";}
			if($d['field_type']=="nick"){$type="Определение ника";}
			if($d['field_type']=="drop_list"){$type="Выпадающий список";}
			if($d['field_type']==""){$type="";}
			?>
			<tr>
				<td width="30" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;<?php echo $d['id']; ?></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>&nbsp;&nbsp;<?php echo $d['txt']; ?></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="100" nowrap><center><?php echo $type; ?></center></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="60" nowrap>
					<center>
						<a href="javascript:{}" onclick="edit_question(<?php echo $d['id']; ?>);">
							<img src="<?php echo $siteLocation; ?>_modules/inviz/img/edit.gif" border="0" />
						</a>
						&nbsp;&nbsp;&nbsp;
						<a href="javascript:{}" onclick="if(confirm('Удалить вопрос?')){delete_question(<?php echo $d['id']; ?>);}">
							<img src="<?php echo $siteLocation; ?>_modules/inviz/img/del.gif" border="0" />
						</a>
					</center>
				</td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
	if($target=="add"){
		?>
		<table width="85%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td align="left"><a href="javascript:{}" onclick="load_page('main_user','');">Пользовательская страница</a></td>
				<td width="20"></td>
				<td align="left"><a href="javascript:{}" onclick="load_page('edit_questions','show');">Список вопросов</a></td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0" border="0">
			<tr valign="top">
				<td align="left"><b>Номер вопроса:</b></td>
				<td width="10"></td>
				<td align="left"><input type="text" size="5" name="q_num" id="q_num" /></td>
			</tr>
			<tr height="7">
				<td colspan="3"></td>
			</tr>
			<tr valign="top">
				<td align="left"><b>Текст вопроса:</b></td>
				<td width="10"></td>
				<td align="left"><textarea name="q_txt" id="q_txt" cols="60" rows="10"></textarea></td>
			</tr>
			<tr height="7">
				<td colspan="3"></td>
			</tr>
			<tr valign="top">
				<td align="left"><b>Тип поля:</b></td>
				<td width="10"></td>
				<td align="left">
					<table cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','one_string');" name="ft" value="one_string" checked="checked" /> Однострочное</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','multi_string');" name="ft" value="multi_string" /> Многострочное</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','callendar');" name="ft" value="callendar" /> Календарь</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','nick');" name="ft" value="nick" /> Определение ника</td>
						</tr>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td>
							<input type="radio" onclick="set_radio('field_type','drop_list');" name="ft" value="drop_list" /> Выпадающий список<br />
							<input type="text" name="field_options" id="field_options" size="60" /><br />
							*Элементы выпадающего списка перечисляйте через запятую. Например: 1,2,3,4,5</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr height="7">
				<td colspan="3"></td>
			</tr>
			<tr valign="top">
				<td colspan="3"><input type="button" value="  добавить  " onclick="add_question(1);" id="add_q_btn"/></td>
			</tr>
		</table>
		<input type="hidden" name="field_type" id='field_type' value="one_string" />
		<?php
	}
	if($target=="check_q_ex"){
		$q_num=mysql_escape_string($q_num);
		$SQL="SELECT * FROM mp_join_questions where id='".$q_num."' limit 1";
		$r=mysql_query($SQL);
		echo mysql_num_rows($r);
	}
	if($target=="save_new"){
		$q_num=mysql_escape_string($q_num);
		$q_txt=mysql_escape_string($q_txt);
		$field_type=mysql_escape_string($field_type);
		$field_options=mysql_escape_string($field_options);
		mysql_query("INSERT INTO mp_join_questions (id,txt,field_type,field_options) values ('".$q_num."','".$q_txt."','".$field_type."','".$field_options."')");
	}
	if($target=="delete"){
		$q_id=mysql_escape_string($q_id);
		mysql_query("delete from mp_join_questions where id='".$q_id."';");
	}
	if($target=="edit"){
		$q_id=mysql_escape_string($q_id);
		$SQL="SELECT * FROM mp_join_questions where id='".$q_id."'";
		$r=mysql_query($SQL);
		$d=mysql_fetch_assoc($r);
		?>
		<table width="85%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td align="left"><a href="javascript:{}" onclick="load_page('main_user','');">Пользовательская страница</a></td>
				<td width="20"></td>
				<td align="left"><a href="javascript:{}" onclick="load_page('edit_questions','show');">Список вопросов</a></td>
			</tr>
		</table>

		<input type="hidden" name="target_id" id="target_id" value="<?php echo $q_id; ?>" />

		<table cellpadding="0" cellspacing="0" border="0">
			<tr valign="top">
				<td align="left"><b>Номер вопроса:</b></td>
				<td width="10"></td>
				<td align="left"><input type="text" size="5" name="q_num" id="q_num" value="<?php echo $d['id']; ?>" /></td>
			</tr>
			<tr height="7">
				<td colspan="3"></td>
			</tr>
			<tr valign="top">
				<td align="left"><b>Текст вопроса:</b></td>
				<td width="10"></td>
				<td align="left"><textarea name="q_txt" id="q_txt" cols="60" rows="10"><?php echo $d['txt']; ?></textarea></td>
			</tr>
			<tr height="7">
				<td colspan="3"></td>
			</tr>
			<tr valign="top">
				<td align="left"><b>Тип поля:</b></td>
				<td width="10"></td>
				<td align="left">
					<table cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','one_string');" name="ft" value="one_string" <?php if(($d['field_type']=="one_string")||($d['field_type']=="")){ echo "checked=\"checked\""; } ?> /> Однострочное</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','multi_string');" name="ft" value="multi_string" <?php if($d['field_type']=="multi_string"){ echo "checked=\"checked\""; } ?> /> Многострочное</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','callendar');" name="ft" value="callendar" <?php if($d['field_type']=="callendar"){ echo "checked=\"checked\""; } ?> /> Календарь</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','nick');" name="ft" value="nick" <?php if($d['field_type']=="nick"){ echo "checked=\"checked\""; } ?> /> Определение ника</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td>
							<input type="radio" onclick="set_radio('field_type','drop_list');" name="ft" value="drop_list" <?php if($d['field_type']=="drop_list"){ echo "checked=\"checked\""; } ?> />  Выпадающий список<br />
							<input type="text" name="field_options" id="field_options" size="60" value="<?php echo $d['field_options']; ?>" /><br />
							*Элементы выпадающего списка перечисляйте через запятую. Например: 1,2,3,4,5</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr height="7">
				<td colspan="3"></td>
			</tr>
			<tr valign="top">
				<td colspan="3"><input type="button" value="  сохранить  " onclick="add_question(0);" id="add_q_btn"/></td>
			</tr>
		</table>
		<input type="hidden" name="field_type" id="field_type" value="one_string" />
		<?php
	}
	if($target=="save_changes"){
		$q_num=mysql_escape_string($q_num);
		$q_txt=mysql_escape_string($q_txt);
		$field_type=mysql_escape_string($field_type);
		$field_options=mysql_escape_string($field_options);
		$target_id=mysql_escape_string($target_id);
		mysql_query("update mp_join_questions set id='".$q_num."', txt='".$q_txt."', field_type='".$field_type."', field_options='".$field_options."' where id='".$target_id."'");
	}
}
// ---- работа с вопросами (секция администрирования) :: end

// ---- подача заявки на вступление :: begin
if($action=="add_entry"){
	if(AuthStatus==1){
		if($target=="form"){
			$userinfo = locateUser(AuthUserName);
			if($userinfo["level"]>=13){
				?>
				<div align="right">
					<a onclick="load_page('main_user','');" href="javascript:{}">Условия вступления</a>
					|
					<a onclick="load_page('add_entry','form');" href="javascript:{}">Подать заявку</a>
				</div>
				<table cellpadding=3 width=95% cellspacing=3>
					<th colspan=3  background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>Заявка на вступление:</th>
					<tr>
						<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;№:</b></td>
						<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;Вопрос:</b></td>
						<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%" nowrap><b>&nbsp;&nbsp;Ответ:</b></td>
					</tr>
				<?php
				$SQL="SELECT * FROM mp_join_questions order by id";
				$r=mysql_query($SQL);
				while($d=mysql_fetch_assoc($r)) {
					?>
					<tr>
						<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;<?php echo $d['id']; ?></td>
						<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php echo $d['txt']; ?></td>
						<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%" nowrap>
						<?php


						if($d['field_type']=="nick"){
							echo prepareUserInfoForDrawing(AuthUserName, $userinfo);
							?>
							<input type="hidden" name="answer_<?php echo $d['id']; ?>" id="answer_<?php echo $d['id']; ?>" value="<?php echo AuthUserName; ?>" />
							<?php
						}
						if($d['field_type']=="one_string"){
							?>
							<input type="text" name="answer_<?php echo $d['id']; ?>" id="answer_<?php echo $d['id']; ?>" size="60" />
							<?php
						}
						if($d['field_type']=="multi_string"){
							?>
							<textarea name="answer_<?php echo $d['id']; ?>" id="answer_<?php echo $d['id']; ?>" cols="60" rows="7"></textarea>
							<?php
						}
						if($d['field_type']=="callendar"){
							?>
                            <input type="text" name="answer_<?php echo $d['id']; ?>" id="answer_<?php echo $d['id']; ?>" size="12" value="<?php echo date("d.m.Y",time()); ?>">
							<?php
						}
						if($d['field_type']=="drop_list"){
							$var=explode(",",$d['field_options']);
							?><select id="answer_<?php echo $d['id']; ?>" name="answer_<?php echo $d['id']; ?>">
							<?php
							$varsize = sizeof($var);
							for($i=0;$i<$varsize;$i++){
								?>
									<option value="<?php echo $var[$i]; ?>"><?php echo $var[$i]; ?></option>
								<?php
							}
							?></select>
							<?php
						}
						if($d['field_type']=="dep_list"){
							?><select id="answer_<?php echo $d['id']; ?>" name="answer_<?php echo $d['id']; ?>">
							<?php
							$SQL2='SELECT `name` FROM `mp_join_depts` ORDER BY id asc';
							$r2 = mysql_query($SQL2);
							while($d2=mysql_fetch_assoc($r2)) {
								?>
								<option value="<?php echo $d2['name']; ?>"><?php echo $d2['name']; ?></option>
								<?php
							}
							?></select>
							<?php
						}
						?>
						</td>
					</tr>
					<?php
				}
				?></table>
                <br />
                <input type="checkbox" id="minimum_agreement" onchange="minimum_agreement();" /> Я ознакомлен(а) с минимальными требованиями и уверен(а) что подхожу под них.<br /><br />
				<input id="add_btn" type="button" disabled="disabled" onclick="add_entry();" value="              подать заявку              " /><br />
				<div id="add_load"></div>
				<?php
			} else {
				?><b>Извините, но заявки принимается от персонажей минимум 13 уровня.</b><?php
			}
		}
		if($target=='add'){
			$answ=explode('@#$%^&*next@#$%^&*answer@#$%^&*',$answers);

			mysql_query(
				"
					INSERT INTO mp_join_entry (nick,start_date,status)
					values ('".AuthUserName."','".time()."','1')
				"
			);

			$SQL=
				"
					SELECT `id`
					FROM `mp_join_entry`
					where `nick`='".AuthUserName."' AND `status`='1'
				";
			$r=mysql_query($SQL);
			$d=mysql_fetch_assoc($r);
			$entry_id=$d['id'];
			$i=0;

			$SQL='SELECT * FROM `mp_join_questions` ORDER BY `id`';
			$r=mysql_query($SQL);
			while($d=mysql_fetch_assoc($r)) {
				$answ[$i]=mysql_escape_string($answ[$i]);
				$n=$i+1;
				mysql_query('INSERT INTO `mp_join_answ` (entry_id, answer_num, question, answer_text) VALUES ('.$entry_id.', \''.$n.'\', \''.$d['txt'].'\', \''.$answ[$i].'\')');
				$i++;
			}
		}
	} else {
		echo '<b><font color=red>Для подачи заявки необходима авторизация.</font></b>';
	}
}
// ---- подача заявки на вступление :: end

// ---- построение списка анкет :: begin
if(($action=='lists') && $join_access){
	$param=mysql_escape_string($param);
	if(!$page){$page=1;}

	$SQL='SELECT * FROM mp_join_entry';
	if($target=='free') {$SQL.=' WHERE `status`=1';}
	if($target=='my') {$SQL.=' WHERE `revisor`=\''.AuthUserName.'\' AND `status`<5';}
	if($target=='archive') {$SQL.=' WHERE `status` IN (5, 6)';}
	if($target=='other') {$SQL.=' WHERE `revisor`=\''.$param.'\' AND `status`<5';}
	if($target=='reserve') {$SQL.=' WHERE `status`=7';}
	if($target=='search'){$SQL.=' WHERE `nick` LIKE \'%'.$param.'%\'';}
	if($target=='inProgress'){$SQL.=" WHERE `status` NOT IN ('1', '5', '6', '7')";}

	if($target=='archive'){
		$SQL.=' ORDER BY `end_date` DESC';
	} else {
		$SQL.=' ORDER BY `start_date` ASC';
	}
	$r=mysql_query($SQL);
	$ex= mysql_num_rows($r);
	$from=$page*20-20;
	$pages=ceil($ex/20);
	$SQL.= ' LIMIT '.$from.',20';
	if($d['status']==1){$st='Свободна';}
	?>
	<table cellpadding=3 cellspacing=3>
		<tr><td colspan="<?php echo ($target=='free' ? '4' : '5');?>">
		<?php
		echo 'Страницы: <b>';
		ShowJoinPages($page,$pages,4);
		echo "</b>";
		?>
		</td></tr>
		<?php if($target=='my' || $target=='other'){ ?><th colspan=6  background='i/bgr-grid-sand.gif'><? } else { ?><th colspan=5  background='i/bgr-grid-sand.gif'><? } ?>
		<?php if($target=='free') { ?>Свободные заявки:<?php } ?>
		<?php if($target=='archive') { ?>Архив заявок:<?php } ?>
		<?php if($target=='my') { ?>Заявки <?php echo AuthUserName; ?>:<?php } ?>
		<?php if($target=='other') { ?>Заявки <?php echo $param; ?>:<?php } ?>
		<?php if($target=='search') { ?>Результаты поиска:<?php } ?>
		<?php if($target=='reserve') { ?>Резерв:<?php } ?>
		<?php if($target=='inProgress') { ?>В обработке:<?php } ?>
		</th>
		<tr>
			<td width="150" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;Ник:</b></td>
			<?php if(($target=="my")||($target=="other")){ ?><td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="20"></td><? } ?>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="150">
				<b>&nbsp;&nbsp;Статус:</b>
			</td>
			<?php
				if ($target != 'free') {
			?>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="150">
				<b>&nbsp;&nbsp;<?php if($target=="archive"){ ?>Рассмотрел<?php } else { ?>Рассматривает<?php } ?>:</b>
			</td>
			<?php
				}
			?>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="130" nowrap>
				<b>&nbsp;&nbsp;<?php if($target=="archive"){ ?>Рассмотренно<?php } else { ?>Подано<?php } ?>:</b>
			</td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="150" nowrap><b>&nbsp;&nbsp;Действия:</b></td>
		</tr>
	<?php
	$r=mysql_query($SQL);
	while($d=mysql_fetch_assoc($r)) {

		if($d['status']==1){$st='В очереди';}
		if($d['status']==2){$st='Рассматривается';}
		if($d['status']==3){$st='Рассматривается';}
		if($d['status']==4){$st='Рассматривается';}
		if($d['status']==5){$st='<font color=green><b>принят(а)</b></font>';}
		if($d['status']==6){$st='<font color=red><b>отказано</b><br>&nbsp;&nbsp;Причина: '.$d['status_comment'].'</font>';}
		if($d['status']==7){$st='<font color=green><b>резерв</b></font>';}

		?>
		<tr>
			<td width="200" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>
			<?php echo prepareUserInfoForDrawing($d['nick']); ?>
			</td>
			<?php if(($target=="my")||($target=="other")){ ?>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="20">
				<?php if($d['status']==3){ ?><img border="0" src="http://www.tzpolice.ru/_modules/inviz/img/cell.gif" /><?php } ?>
				<?php if($d['status']==4){ ?><img border="0" src="http://www.tzpolice.ru/_modules/inviz/img/clock.gif" /><?php } ?>
				</td>
			<? } ?>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="100">
				<?php echo $st; ?>
			</td>
			<?php
				if ($target != 'free') {
			?>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap width="200">
				<?php echo prepareUserInfoForDrawing($d['revisor']); ?>
			</td>
			<?php
				}
			?>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="130" nowrap>
				<?php echo date("d.m.y H:i:s", ($target=="archive" ? $d['end_date'] : $d['start_date'])); ?>
			</td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="150" nowrap>
				&nbsp;&nbsp;<a href="javascript:{}" onclick="show_entry('show_entry','','<?php echo $d['id']; ?>','');">смотреть</a>
				<?php if(($d['status'] == 1 || $target=='inProgress') && $join_revisor){ ?>&nbsp;&nbsp;&nbsp;<a href="javascript:{}" onclick="show_entry('get_entry','','<?php echo $d['id']; ?>','');">принять</a><?php } ?>
			</td>
		</tr>
		<?php
	}

}
// ---- построение списка анкет :: end

// ---- показ анкеты :: begin
if(($action=="show_entry") && $join_access){
	$param=mysql_escape_string($param);
	?>
	<table cellpadding=3 width=95% cellspacing=3>
		<th colspan=3  background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>Заявка на вступление:</th>
	<?php
	$SQL="SELECT * FROM mp_join_entry where id='".$param."'";
	$r=mysql_query($SQL);
	$ex= mysql_num_rows($r);
	$e=mysql_fetch_assoc($r);


	if($e['status']=="1"){$status="В очереди на рассмотрение";}
	if($e['status']=="2"){$status="Принята на рассмотрение";}
	if($e['status']=="3"){$status="Свяжитесь с <font color=black>".prepareUserInfoForDrawing($e['revisor'])."</font> для проведения собеседования";}
	if($e['status']=="4"){$status="Ожидается вынесение решения по заявке";}
	if($e['status']=="5"){$status="Принят(а) в ряды полиции";}
	if($e['status']=="6"){$status="<font color=red>Отказано во вступлении</font>";}
	if($e['status']=="7"){$status="Внесена в резерв";}
	if($e['status']>1){
		?>
		<tr>
			<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<b>Статус заявки:</b></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<font color="green"><b><?php echo $status; ?></b></font></td>
		</tr>
		<?php
	}
	if($e['revisor']!=''){
		?>
		<tr>
			<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;Заявку рассматривает:</td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php echo prepareUserInfoForDrawing($e['revisor']); ?></td>
		</tr>
		<?php
	}
	?>
		<tr>
			<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;№:</b></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;Вопрос:</b></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;Ответ:</b></td>
		</tr>
	<?php
	$SQL="
		SELECT a.answer_text, a.answer_num, a.question, q.field_type
		FROM mp_join_answ a
		LEFT JOIN mp_join_questions q ON a.answer_num=q.id
		where a.entry_id='".$e['id']."'
		order by answer_num asc
	";
	$r=mysql_query($SQL);
	while($d=mysql_fetch_assoc($r)) {

		?>
		<tr>
			<td width="20" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;<?php echo $d['answer_num']; ?></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php echo $d['question']; ?></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php
			if($d['field_type']=='nick'){
				echo '<div id="user_nick_data">'.prepareUserInfoForDrawing($d['answer_text']).'</div>';
				?>
				<div id="user_nick_data_img"><a href="javascript:{}" onclick="reload_user_nick_data('<?php echo $param; ?>');" />[обновить]</a></div>
				<?php
			} else {
				echo $d['answer_text'];
			}
			?></td>
		</tr>
		<?php
	}
	?>

	<?php
		// ---------------------- секция показа комментариев :: begin
		$r = mysql_query(
			"
				SELECT `comment_date`, `comment_text`, `commenter_nick`
				FROM `mp_join_entry_comments`
				WHERE `entry_id`='".$e['id']."'
				ORDER BY `comment_date` ASC
			", $db
		);
	?>
	<tr height="25"><td colspan="3"></td></tr>
	<tr>
		<td colspan="3">
		<b>Комментарии:</b><br />
			<table cellpadding=3 width=95% cellspacing=3>
				<tr>
					<th width="100" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>Дата добавления</th>
					<th width="100" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>Комментировал</th>
					<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>Комментарий</th>
				</tr>
				<?php
					while($d=mysql_fetch_assoc($r)) {
				?>
					<tr>
						<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center'>
							<?php echo $d['comment_date']; ?>
						</td>
						<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center' nowrap>
							<b><?php echo prepareUserInfoForDrawing($d['commenter_nick']); ?></b>
						</td>
						<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>
							<?php echo $d['comment_text']; ?>
						</td>
					</tr>
				<?php
					}
				?>
			</table>
		</td>
	</tr>
	<?php
		// ---------------------- секция показа комментариев :: end
	?>

	<tr height="25"><td colspan="3"></td></tr>
	<tr><td colspan="3">
	<b>Действия:</b><br />
	<?php
	if($e['status']==1){
		?><a href="javascript:{}" onclick="show_entry('get_entry','','<?php echo $e['id']; ?>','');">принять заявку</a><br />
		<a href="javascript:{}" onclick="set_status('<?php echo $e['id']; ?>','7','0');">записать в резерв</a><br />
		<a href="javascript:{}" onclick="show_reject();">отказать</a>
		<?php
	}
	if(($e['status']>1)&&($e['status']<5)) {
		if(($e['revisor']==AuthUserName)||($join_admin==1)){
		?>
		<a href="javascript:{}" onclick="set_status('<?php echo $e['id']; ?>','5','0');">принять в полицию</a><br />
		<a href="javascript:{}" onclick="set_status('<?php echo $e['id']; ?>','7','0');">записать в резерв</a><br />
		<a href="javascript:{}" onclick="show_reject();">отказать</a>
		<?php
		}
	}
	if($e['status']==7) {
		if(($e['revisor']==AuthUserName)||($join_admin==1)){
		?>
		<a href="javascript:{}" onclick="set_status('<?php echo $e['id']; ?>','5','0');">принять в полицию</a><br />
		<a href="javascript:{}" onclick="show_reject();">отказать</a>
		<?php
		}
	}
	?>
	<br />
	<div id="reject_form" style="display:none">
		<input type="text" size="100" name="comment_text" id="comment_text" value="Вы не соответствуете нашим требованиям" /><input type="button" value="  отказать  " name="reject_btn" onclick="set_status('<?php echo $e['id']; ?>','6','1');" />
	</div>
	<?php
	#if($e['status'] != 5 && $e['status'] != 6) {
	?>
		<a href="javascript:{}" onclick="show_comment();">комментировать</a>
	<?php
	#}
	?>
	<br />
	<div id="comment_form" style="display:none">
		<textarea name="comment_text_area" id="comment_text_area" cols="50" rows="4"></textarea><input type="button" value="  добавить комментарий  " name="comment_btn" onclick="add_comment('<?php echo $e['id']; ?>','1');" /></div>
	<br />
	<b>Установить статус:</b><br />
	<?php if(($e['revisor']==AuthUserName)||($join_admin==1)){
		if(($e['status']>1)&&($e['status']<5)) {
		?>
		<a href="javascript:{}" onclick="set_status('<?php echo $e['id']; ?>','3','0');">"Свяжитесь со мной для проведения собеседования"</a><br />
		<a href="javascript:{}" onclick="set_status('<?php echo $e['id']; ?>','4','0');">"Ожидается вынесение решения по заявке"</a><br />
	<?php
		}
	} ?>
	</td></tr>
	<?php
	?></table>
	<?php

	$SQL="SELECT * FROM mp_join_entry where nick='".$e['nick']."' and id<>'".$e['id']."' order by start_date DESC";
	$r=mysql_query($SQL);
	$ex= mysql_num_rows($r);
	if($ex>0){
		?><table cellpadding=3 cellspacing=3>
		<th colspan=5  background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>Игрок уже подавал заявки:</th>
		<tr>
			<td width="200" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;Ник:</b></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="130"><b>&nbsp;&nbsp;Подано:</b></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="130" nowrap><b>&nbsp;&nbsp;Рассмотрено:</b></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="150" nowrap><b>&nbsp;&nbsp;Результат:</b></td>
			<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="70" nowrap><b>&nbsp;&nbsp;Действия:</b></td>
		</tr>
		<?php
		while($d=mysql_fetch_assoc($r)) {
			if($d['status']==1){$st='Свободна';}
			if($d['status']==2){$st='Рассматривается';}
			if($d['status']==3){$st='Рассматривается';}
			if($d['status']==4){$st='Рассматривается';}
			if($d['status']==5){$st='<font color=green><b>принят(а)</b></font>';}
			if($d['status']==6){$st='<font color=red><b>отказано</b><br>&nbsp;&nbsp;Причина: '.$d['status_comment'].'</font>';}
			if($d['status']==7){$st='<font color=green><b>резерв</b></font>';}
			?>
			<tr>
				<td width="200" background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;
				<?php
				echo prepareUserInfoForDrawing($d['nick']);
				?>
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="130">&nbsp;&nbsp;<?php echo date("d.m.y H:i:s",$d['start_date']); ?></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="130" nowrap>&nbsp;&nbsp;<?php echo date("d.m.y H:i:s",$d['end_date']); ?></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="150">&nbsp;&nbsp;<?php echo $st; ?></td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width="70" nowrap>&nbsp;&nbsp;<a href="javascript:{}" onclick="show_entry('show_entry','','<?php echo $d['id']; ?>','');">смотреть</a>
				<?php
				if($d['status']==1){
				?><br />&nbsp;&nbsp;<a href="javascript:{}" onclick="show_entry('get_entry','','<?php echo $e['id']; ?>','');">принять</a>
				<?php
				}
				?>
				</td>
			</tr>
			<?php
		}
	}
}
// ---- показ анкеты :: end

if($action=='get_entry' && $join_access){
	$param=mysql_escape_string($param);
	mysql_query('UPDATE `mp_join_entry` SET `status`=2, `revisor`="'.AuthUserName.'" WHERE `id`='.$param.';');
}

if($action=='set_status' && $join_access){
	$id=mysql_escape_string($id);
	$status=mysql_escape_string($status);
	$comment=mysql_escape_string($comment);
	mysql_query('UPDATE `mp_join_entry` SET `status`='.$status.', `status_comment`="'.$comment.'", `end_date`='.mktime().', `revisor`="'.AuthUserName.'" WHERE `id`='.$id.';');
}

if($action=='add_comment' && $join_access){
	$id=mysql_escape_string($id);
	$comment=mysql_escape_string($comment);
	$insertResult = mysql_query(
		"
			INSERT
				INTO `mp_join_entry_comments`(`entry_id`, `comment_text`, `commenter_nick`)
				VALUES(
					'".$id."',
					'".$comment."',
					'".AuthUserName."'
				)
		", $db
	);
}

if(($action=="reload_user_nick_data") && $join_access){
	$id=mysql_escape_string($id);
	$r=mysql_query("SELECT `nick` FROM mp_join_entry where `id`='".$id."' limit 1");
	$d=mysql_fetch_assoc($r);
	echo prepareUserInfoForDrawing($d['nick']);
}

// security preferences :: begin
if (($action=='edit_security') && $join_admin){
	if ($target == 'show') {
?>
		<table cellpadding=3 width=95% cellspacing=3>
			<tr>
				<th colspan=4 background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center'>Настройки доступа:</th>
			</tr>
			<tr>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width='200'>Ник</th>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width='100' align='center'>Сотрудник отдела</th>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width='100' align='center'>Администратор</th>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>&nbsp;</th>
			</tr>
			<?php
			$r = mysql_query(
				'
					SELECT *
					FROM `mp_join_access`
				', $db
			);
			while($d=mysql_fetch_assoc($r)) {
			?>
			<tr>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>
					<?php echo prepareUserInfoForDrawing($d['nick']); ?>
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center'>
					<input type="checkbox" name="okAccess_<?php echo $d['id']; ?>" id="okAccess_<?php echo $d['id']; ?>" value="checkbox" <?php echo ($d['revisor'] ? 'checked' : ''); ?>>
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center'>
					<input type="checkbox" name="okAdmin_<?php echo $d['id']; ?>" id="okAdmin_<?php echo $d['id']; ?>" value="checkbox" <?php echo ($d['admin'] ? 'checked' : ''); ?>>
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>
					<input
						type="button"
						name="changeAccessButton"
						onclick="change_access('<?php echo $d['id'] ?>', document.getElementById('okAccess_<?php echo $d['id']; ?>').checked, document.getElementById('okAdmin_<?php echo $d['id']; ?>').checked);"
						value="Изменить доступ для <?php echo $d['nick']; ?>">
					<input
						type="button"
						name="deleteAccessButton"
						onclick="if(confirm('Удалить <?php echo $d['nick']; ?> из списка (доступ к консоли работы с анкетами для данного пользователя будет запрещён)?')){delete_access('<?php echo $d['id'] ?>')};"
						value="Удалить <?php echo $d['nick']; ?> из списка">
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' colspan='4' style='padding-top: 10px'>
					Добавить доступ для игрока (Ник):
					<input
						type="text"
						name="accessNick"
						id="accessNick">
					<input
						type="button"
						name="addAccessButton"
						onclick="add_access(document.getElementById('accessNick').value);"
						value="Добавить доступ">
				</td>
			</tr>
			<tr>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' colspan='4' style='padding-top: 10px'>
					Легенда по ролям доступа:<br/>
					"Сотрудник отдела" - обладающей данной ролью может работать с анкетами кандидатов<br/>
					"Администратор" - обладающей данной ролью имеет полный доступ, включая управление доступом других пользователей данного модуля<br/><br/>
					(если игрок внесён в данный список, но не имеет ниодной назначенной роли, то он сможет только просматриваться анкеты и оставлять комментарии)
				</td>
			</tr>

<?php
	}

	if ($target == 'changeAccess') {
		mysql_query(
			'
				UPDATE `mp_join_access`
				SET
					`revisor` = "'.$okAccess.'",
					`admin` = "'.$okAdmin.'"
				WHERE `id` = "'.$target_id.'"
			', $db
		);
	}

	if ($target == 'deleteAccess') {
		mysql_query(
			'
				DELETE
				FROM `mp_join_access`
				WHERE `id` = "'.$target_id.'"
			', $db
		);
	}

	if ($target == 'addAccess') {
		?>
		<div align='center'>
		<?php
		$targetNick = mysql_escape_string(trim($targetNick));
		$r = mysql_query(
			"
				SELECT *
                FROM `locator`
                WHERE `login` = '".$targetNick."'
            ", $db
		);
		if (mysql_num_rows($r) != 1) {
		?>
			<font color='red'>Персонаж с указанным ником '<b><?php echo $targetNick; ?></b>' не найден.</font>
		<?php
		} else {
			$r = mysql_query(
				"
					SELECT *
					FROM `mp_join_access`
					WHERE `nick` = '".$targetNick."'
				", $db
			);
			if (mysql_num_rows($r) != 0) {
			?>
				<font color='red'>Персонаж с указанным ником '<b><?php echo $targetNick; ?></b>' уже имеет доступ.</font>
			<?php
			} else {
				mysql_query(
					"
						INSERT
						INTO `mp_join_access`(`nick`, `revisor`, `admin`)
						VALUES (
							'$targetNick',
							'0',
							'0'
						)
					", $db
				);
				?>
					'<b><?php echo $targetNick; ?></b>' - добавлен. Настройте доступ, если это необходимо.
				<?php
			}
		}
		?>
			<br /><br />
			<a href="javascript:{}" onclick="load_page('edit_security','show');">Перейти на страницу управления доступом</a><br />
		</div>
		<?php
	}
}

if (($action=='show_actualy') && $join_admin){
	if ($target == 'show') {
?>
		<table cellpadding=3 width=95% cellspacing=3>
			<tr>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width='200'>Ник</th>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width='100' align='center'>Дата заявки</th>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width='100' align='center'>Дата приёма</th>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' width='100' align='center'>Принявший</th>
				<th background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>Действия</th>
			</tr>
			<?php
			$r = mysql_query(
				'
					SELECT *
					FROM `mp_join_entry` WHERE `status`="5"
				', $db
			);
			while($d=mysql_fetch_assoc($r)) {
			?>
			<tr>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>
					<?php echo prepareUserInfoForDrawing($d['nick']); ?>
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center'>
					<?php echo date('d.m.Y H:i:s',$d['start_date']); ?>
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center'>
					<?php echo date('d.m.Y H:i:s',$d['end_date']); ?>
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif' align='center'>
					<?php echo prepareUserInfoForDrawing($d['revisor']); ?>
				</td>
				<td background='<?php echo $siteLocation; ?>i/bgr-grid-sand.gif'>
				<a href="javascript:{}" onclick="show_entry('show_entry','','<?php echo $d['id']; ?>','');">смотреть</a>
				<a href="javascript:{}" onclick="actualy_del_comment('<?php echo $d['id']; ?>','<?php echo $d['nick']; ?>');">Уволить</a>
				</td>
			</tr>
			<?php
			}
			?>
			</table>
<?php
	}

	if ($target == 'dissmiss') {
		mysql_query(
			'
				UPDATE `mp_join_entry`
				SET
					`status` = "6",
					`revisor` = "'.AuthUserName.'",
					`status_comment` = "Уволен из рядов по причине: '.$reason.'"
				WHERE `id` = "'.$target_id.'"
			', $db
		);
	}

}
// security preferences :: end
?>
