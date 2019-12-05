<?php
$_RESULT = array("res" => "ok");
require_once "../../xhr_config.php";
require_once "../../xhr_php.php";
require_once "../../mysql.php";
require_once "../../functions.php";
require_once "../../auth.php";
$JsHttpRequest =& new Subsys_JsHttpRequest_Php("windows-1251");


$join_access=0;
$join_admin=0;

if (AuthUserGroup == '100'){$join_admin=1;}
if (abs(AccessLevel) & AccessJoinAdmin){$join_admin=1;}

if (AuthUserGroup == '100'){$join_access=1;}
if (abs(AccessLevel) & AccessJoinModer){$join_access=1;}

$menu = "rating_arh";
$path_to_php = "/_modules";
$DOCUMENT_ROOT = ereg_replace ("/$", "", $HTTP_SERVER_VARS['DOCUMENT_ROOT']);
include_once ($DOCUMENT_ROOT.$path_to_php."/".$menu."/tz_plugins.php");

function ShowJoinPages($CurPage,$TotalPages,$ShowMax) {
	global $action;
	global $target;
	global $param;
    $PrevList=floor(($CurPage-1)/$ShowMax)*$ShowMax;
    $NextList=$PrevList+$ShowMax+1;
        if($PrevList>=$ShowMax*2) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','1');\" title='В самое начало'>«</a> ";
        if($PrevList>0) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','{$PrevList}');\" title='Предыдущие $ShowMax страниц'>…</a> ";
    for($i=$PrevList+1;$i<=$PrevList+$ShowMax;$i++) if($i<=$TotalPages) {
            if($i==$CurPage) echo "<u>{$i}</u> ";
        else echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','{$i}');\">$i</a> ";
    }
    if($NextList<=$TotalPages) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','{$NextList}');\" title='Следующие $ShowMax страниц'>…</a> ";
        if($CurPage<$TotalPages) echo "<a href='javascript:{}' onclick=\"show_entry('".$action."','".$target."','".$param."','{$TotalPages}');\" title='В самый конец'>»</a>";
}

function VerifyStatus() {

	$SQL="SELECT status, revisor FROM police_join_entry where nick='".AuthUserName."' AND status IN ('1', '2', '3', '4', '7') ORDER BY start_date DESC LIMIT 1";
	$result=mysql_query($SQL);
	if ($row = mysql_fetch_array($result)) {
		if($row['status']=="1"){$stk="В очереди на рассмотрение";}
		if($row['status']=="2"){$stk="Принята на рассмотрение";}
		if($row['status']=="3"){$stk="Свяжитесь с <img border=0 src='_imgs/clansld/police.gif'><font color=black>".$row['revisor']."</font> для проведения собеседования";}
		if($row['status']=="4"){$stk="Ожидается вынесение решения по заявке";}
		if($row['status']=="7"){$stk="Внесена в <u>резерв</u>.";}
	} else {
		$stk = "no";
	}

	return $stk;
	
}



//foreach($_REQUEST as $k => $v) {
//	$temp = mb_convert_encoding($v,"cp1251","utf8");
//	$temp2 = mb_convert_encoding($temp,"utf8","cp1251");
//	if($temp2 == $v) {
//		$v = mb_convert_encoding($v,"cp1251","utf8");
//	}
//	$_REQUEST[$k] = addslashes(htmlspecialchars(trim($v)));
//}

extract($_REQUEST);

if (($join_access==1)&&($hide_menu!=1)){
	?>
	<br />
	<table width="100%" cellpadding="3" cellspacing="3" border="1">
		<tr>
			<td align="center"><a href="javascript:{}" onclick="show_entry('lists','free','','1');">Свободные заявки</a></td>
			<td align="center"><a href="javascript:{}" onclick="show_entry('lists','my','','1');">Мои заявки</a></td>
			<td align="center"><a href="javascript:{}" onclick="show_entry('lists','archive','','1');">Архив</a></td>
			<?php if($join_admin==1){ ?>
			<td align="center" rowspan=2>
				<select onchange="show_entry('lists','other',this.value,'1');">
				<option value="false">Показать заявки</option>
				<?php
				$SQL="SELECT revisor FROM police_join_entry group by revisor order by revisor asc";
				$r=mysql_query($SQL);
				while($d=mysql_fetch_assoc($r)) {
					?><option onclick="show_entry('lists','other','<?php echo $d['revisor']; ?>','1');" value="<?php echo $d['revisor']; ?>"><?php echo $d['revisor']; ?></option><?php
				}
				?>
				</select>
				<input type='button' value="сброс" onclick="load_page('main_user','');">
			</td>
			<?php } ?>
		</tr>
		<tr>
			<td align="center"><a href="javascript:{}" onclick="show_entry('lists','reserve','','1');">Резерв</a></td>
			<td align="center"><a href="javascript:{}" onclick="show_search();">Поиск</a></td>
			<td align="center">
				<?php if($join_admin==1){ ?>
				<a href="javascript:{}" onclick="load_page('edit_questions','show');">Настройки</a>
			    <?php } ?>
			­</td>
		</tr>

		<tr id="search_form" style="display:none;">
			<td align="center" colspan="2">Поиск по нику: <input type="text" size="20" id="serach_nick" /><input type="button" value="  искать  " name="search_btn" id="search_btn" onclick="search_entry();" /></td>
			<td align="center" colspan="2">Поиск по ФИО: <input type="text" size="50" id="serach_fio" /><input type="button" value="  искать  " name="search_btn" id="search_btn" onclick="search_fio();" /></td>
		</tr>
	</table>
	<br />
	<?php
}

if($action=="main_user"){

	$SQL="SELECT * FROM police_join_entry where nick='".AuthUserName."' and status<>'6' and status<>'5' order by start_date desc limit 1;";
	$r=mysql_query($SQL);
	$ex= mysql_num_rows($r);
	$e=mysql_fetch_assoc($r);

	if($ex==1){
		if($e['status']=="1"){$status="В очереди на рассмотрение";}
		if($e['status']=="2"){$status="Принята на рассмотрение";}
		if($e['status']=="3"){$status="Свяжитесь с <img border=0 src='_imgs/clansld/police.gif'><font color=black>".$e['revisor']."</font> для проведения собеседования";}
		if($e['status']=="4"){$status="Ожидается вынесение решения по заявке";}
		if($e['status']=="7"){$status="Ваша заявка прошла первичный контроль и внесена в <u>резерв</u> кандидатов в сотрудники Полиции. Мы свяжемся с вами для собеседования, как только откроется свободная вакансия, подходящая вам.";}
		?>
		<table cellpadding=3 width=95% cellspacing=3>
			<th colspan=3  background='i/bgr-grid-sand.gif'>Заявка на вступление:</th>
			<tr>
				<td width="20" background='i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
				<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<b>Статус заявки:</b></td>
				<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<font color="green"><b><?php echo $status; ?></b></font></td>
			</tr>
		<?php
		if($e['revisor']!=""){
			?>
			<tr>
				<td width="20" background='i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
				<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;Заявку рассматривает:</td>
				<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<img border=0 src='_imgs/clansld/police.gif'><?php echo $e['revisor']; ?></td>
			</tr>
			<?php
		}
		?>
			<tr>
				<td width="20" background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;№:</b></td>
				<td background='i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;Вопрос:</b></td>
				<td background='i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;Ответ:</b></td>
			</tr>
		<?php
		$SQL="SELECT * FROM police_join_answ where entry_id='".$e['id']."' order by answer_num asc";
		$r=mysql_query($SQL);
		while($d=mysql_fetch_assoc($r)) {

			?>
			<tr>
				<td width="20" background='i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;<?php echo $d['answer_num']; ?></td>
				<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php echo $d['question']; ?></td>
				<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php
				if($d['field_type']=="nick"){
					if($e["clan"]!=""){
						?><img src="http://www.timezero.ru/i/clans/<?php echo $e["clan"]; ?>.gif" style="vertical-align:text-bottom"><?php
					} else {
						echo "<img src=\"_modules/inviz/blank.gif\" width=\"28\" height=\"16\">";
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
<div align=left>

Уважаемые игроки!
<br>На этой странице вы найдете общую информацию о <img src="_imgs/clans/Police Academy.gif" alt="Police Academy" border="0"><b>Полицейской Академии</b> и сможете подать заявку на вступление в ряды <img src="_imgs/clans/police.gif" alt="police" border="0"><b>Полиции ТО</b>.
<br>
<br>
<br><img src="_imgs/clans/Police Academy.gif" alt="Police Academy" border="0"><b>Полицейская Академия</b> является учебным подразделением <img src="_imgs/clans/police.gif" alt="police" border="0"><b>Полиции ТО</b>, в котором будущие модераторы проходят подготовку, а также переподготовку.
<br>
<br><b><font color="blue">Наши требования:</font></b>
<br>
<br>
<table><tbody><tr><td class="quote-news"><b><font color="brown">Реальный возраст:</font></b> <b>от 21 года</b>
<br>
<br><b><font color="brown">Мин. уровень:</font></b> <b>13</b>
<br>
<br><b><font color="brown">Профессия:</font></b> <b>Любая, кроме профессии "корсар"</b>
<br>
<br><b><font color="brown">Сторона:</font> не имеет значения, но при согласии игрока (и одобрении администрации) возможен переход за Freedom без штрафных санкций.</b> 
<br>
<br><b><font color="brown">Судимости:</font> индивидуальное рассмотрение.</b> 
<br>
<br><b><font color="brown">Условия приема:</font></b>
<br>1. Прохождение обязательной процедуры приема, проводимой Отделом Кадров полиции ТО.
<br>2. Понимание роли Полиции и готовность служить Закону ТО.
<br>3. Конфликтоустойчивость, выдержанность, вежливость.
<br>4. Дисциплинированность, готовность исполнять приказы, умение работать в команде.
<br>5. Время в игре не менее 30 часов в неделю.
<br>6. Соответствие пола персонажа реальному полу владельца.
<br>7. Отсутствие мультов с профессией "корсар", соискателей этой профессии, а также мультов на каторге.
<br>8. Анкета подается только с основного персонажа.
</td></tr></tbody></table>
<br>
<br><b><font color="blue">Основные цели создания Полицейской Академии:</font></b>
<br>- систематизация приема сотрудников в основной состав Полиции;
<br>- улучшение качества модерации чата и форума ТО;
<br>
<br><b><font color="blue">Права курсанта:</font></b>
<br>Курсанты Полицейской Академии имеют право на:
<br>1. Прием в основной состав полиции. Прием осуществляется только из членов Академии. Прием в Академию не гарантирует последующий прием в основной состав Полиции.
<br>2. Курсанты Академии (в отличие от сотрудников Полиции) имеют право на владение частной собственностью (магазины, заводы, лаборатории), а так же имеют право на коммерческую деятельность (торговля, заключение экономических сделок и т.д.) - <u>до момента вступления в основной состав Полиции.</u>
<br>
<br><b><font color="blue">Обязанности курсанта:</font></b>
<br>Курсант Полицейской Академии обязан:
<br>1. Ознакомиться со всеми относящимися к Полицейской Академии инструкциями и приказами и неукоснительно их выполнять.
<br>2. Тщательно изучить Законы ТО. Соблюдать их и следить за их исполнением другими персонажами.
<br>3. Беспрекословно выполнять приказы вышестоящего начальства. Приказы могут быть обжалованы после их выполнения в соответствии с служебными инструкциями.
<br>4. Участвовать в работе отдела модерации полиции ТО.
<br>
<br><b><font color="blue">Краткая информация об учебном процессе:</font></b>
<br>1. Учебный процесс в Полицейской Академии разбит на несколько этапов. Каждый этап заканчивается тестированием.
<br>2. Обучение проходит непрерывно от начала и до конца. Средний срок обучения равен одному месяцу.
<br>3. С курсантами Академии работает опытный инструктор, осуществляющий ежедневное сопровождение учебного процесса.
<br>4. На время обучения вводятся ограничения на передвижения курсантов по миру ТО, участие в боях и общение на форуме!
<br>
<br><b><font color="blue">Исключение из Академии:</font></b>
<br>Исключению из рядов Полицейской Академии персонаж подлежит в следующих случаях:
<br>1. Нарушение законов ТО.
<br>2. Нарушение служебных инструкций, действий, не соответствующих этическим нормам службы в Полиции ТО.
<br>3. Нарушение приказов вышестоящего начальства.
<br>4. По собственному желанию.
<br>
<br>Более подробную информацию об Академии вы сможете получить у начальника Полицейской Академии.
<br>
<br>Если вы соответствуете нашим требованиям, принимаете все наши условия и готовы к нелегкой работе, то вы можете <font color="brown"> <a href="javascript:{}" onclick="load_page('add_entry','form');">подать заявку на вступление</a></font>
<br>
<br>
<br>С уважением,
<br><b>Отдел кадров <img src="_imgs/clans/police.gif" alt="police" border="0"> Полиции ТО</b>.
				</td>

		<?php
		$SQL="SELECT * FROM police_join_entry where nick='".AuthUserName."' and (status='6' or status='5' or status='7') order by start_date desc limit 1;";
		$r=mysql_query($SQL);
		$ex_old= mysql_num_rows($r);
		$e=mysql_fetch_assoc($r);
		if($ex_old==1){
			if($e['status']==7){$st="вы внесены в <font color=yellow><b>резерв</b></font> кандидатов в сотрудники Полиции. Мы свяжемся с вами, как только откроется свободная вакансия.";}
			if($e['status']==6){$st="вы получили <font color=red><b>отказ</b></font><br>Причина отказа: <font color=red><b>".nl2br($e['status_comment'])."</b></font>";}
			if($e['status']==5){$st="вас <font color=green><b>приняли</b></font> в ряды полиции.";}
			?>
			<br>
				Вы уже подавали заявку на вступление: <?php echo date("d.m.y H:i:s",$e['start_date']); ?><br />
				Заявка была рассмотрена: <?php echo date("d.m.y H:i:s",$e['end_date']); ?><br />
				По заявке <?php echo $st; ?>
			<br>
			<?php
		}
		?></div>
		<?php
	}
}

if(($action=="edit_questions")&&($join_admin==1)){
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
				<td width="30" background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;№:</b></td>
				<td background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;Текст вопроса:</b></td>
				<td background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;Тип поля:</b></td>
				<td background='i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
			</tr>
		<?php
		$SQL="SELECT * FROM police_join_questions order by id asc";
		$r=mysql_query($SQL);
		while($d=mysql_fetch_assoc($r)) {
			if($d['field_type']=="one_string"){$type="Однострочное";}
			if($d['field_type']=="multi_string"){$type="Многострочное";}
			if($d['field_type']=="callendar"){$type="Календарь";}
			if($d['field_type']=="nick"){$type="Определение ника";}
			if($d['field_type']=="drop_list"){$type="Выпадающий список";}
			if($d['field_type']=="dep_list"){$type="Список отделов";}
			if($d['field_type']==""){$type="";}
			?>
			<tr>
				<td width="30" background='i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;<?php echo $d['id']; ?></td>
				<td background='i/bgr-grid-sand.gif'>&nbsp;&nbsp;<?php echo $d['txt']; ?></td>
				<td background='i/bgr-grid-sand.gif' width="100" nowrap><center><?php echo $type; ?></center></td>
				<td background='i/bgr-grid-sand.gif' width="60" nowrap><center><a href="javascript:{}" onclick="edit_question(<?php echo $d['id']; ?>);"><img src="_modules/inviz/img/edit.gif" border="0" /></a>&nbsp;&nbsp;&nbsp;<a href="javascript:{}" onclick="if(confirm('Удалить вопрос?')){delete_question(<?php echo $d['id']; ?>);}"><img src="_modules/inviz/img/del.gif" border="0" /></a></center></td>
			</tr>
			<?php
		}
		?>
		</table><br /><br />
		<table cellpadding=3 width=70% cellspacing=3>
			<th colspan=2  background='i/bgr-grid-sand.gif'>Отделы:</th>
			<tr>
				<td background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;Название:</b></td>
				<td background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;</b></td>
			</tr>
			<?php
			$SQL="SELECT d.id,d.name,j.id as j_id FROM sd_depts d LEFT JOIN police_join_depts j ON d.id=j.id order by d.id asc";
			$r=mysql_query($SQL);
			while($d=mysql_fetch_assoc($r)) {
				?>
				<tr>
					<td background='i/bgr-grid-sand.gif' nowrap><?php if($d['j_id']>0){echo "<font color=green>";} else {echo "<font color=red>";} ?><b>&nbsp;&nbsp;<?php echo $d['name']; ?></b></font></td>
					<td width="100" background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;<a href="javascript:{}" onclick="togle_dept_status('<?php echo $d['id']; ?>','<?php if($d['j_id']>0){echo "0";} else {echo "1";}?>','<?php echo $d['name']; ?>')"><?php if($d['j_id']>0){echo "скрыть";} else {echo "отобразить";}?></a></b></td>
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
				<td align="left"><a href="javascript:{}" onclick="load_page('edit_questions','show');">Настройки</a></td>
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
							<td><input type="radio" onclick="set_radio('field_type','one_string');" name="ft" id="ft" value="one_string" checked="checked" /> Однострочное</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','multi_string');" name="ft" id="ft" value="multi_string" /> Многострочное</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','callendar');" name="ft" id="ft" value="callendar" /> Календарь</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','nick');" name="ft" id="ft" value="nick" /> Определение ника</td>
						</tr>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','dep_list');" name="ft" id="ft" value="dep_list" /> Список отделов</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td>
							<input type="radio" onclick="set_radio('field_type','drop_list');" name="ft" id="ft" value="drop_list" /> Выпадающий список<br />
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
		<input type="hidden" name="field_type" value="one_string" id="field_type" />
		<?php
	}
	if($target=="check_q_ex"){
		$q_num=mysql_escape_string($q_num);
		$SQL="SELECT * FROM police_join_questions where id='".$q_num."' limit 1";
		$r=mysql_query($SQL);
		echo mysql_num_rows($r);
	}
	if($target=="save_new"){
		$q_num=mysql_escape_string($q_num);
		$q_txt=mysql_escape_string($q_txt);
		$field_type=mysql_escape_string($field_type);
		$field_options=mysql_escape_string($field_options);
		mysql_query("INSERT INTO police_join_questions (id,txt,field_type,field_options) values ('".$q_num."','".$q_txt."','".$field_type."','".$field_options."')");
	}
	if($target=="delete"){
		$q_id=mysql_escape_string($q_id);
		mysql_query("delete from police_join_questions where id='".$q_id."';");
	}
	if($target=="edit"){
		$q_id=mysql_escape_string($q_id);
		$SQL="SELECT * FROM police_join_questions where id='".$q_id."'";
		$r=mysql_query($SQL);
		$d=mysql_fetch_assoc($r);
		?>
		<table width="85%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td align="left"><a href="javascript:{}" onclick="load_page('main_user','');">Пользовательская страница</a></td>
				<td width="20"></td>
				<td align="left"><a href="javascript:{}" onclick="load_page('edit_questions','show');">Настройки</a></td>
			</tr>
		</table>

		<input type="hidden" id="target_id" name="target_id" value="<?php echo $q_id; ?>" />

		<table cellpadding="0" cellspacing="0" border="0">
			<tr valign="top">
				<td align="left"><b>Номер вопроса:</b></td>
				<td width="10"></td>
				<td align="left"><input type="text" size="5" id="q_num" name="q_num" value="<?php echo $d['id']; ?>" /></td>
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
							<td><input type="radio" onclick="set_radio('field_type','one_string');" name="ft" id="ft" value="one_string" <?php if(($d['field_type']=="one_string")||($d['field_type']=="")){ echo "checked=\"checked\""; } ?> /> Однострочное</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','multi_string');" name="ft" id="ft" value="multi_string" <?php if($d['field_type']=="multi_string"){ echo "checked=\"checked\""; } ?> /> Многострочное</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','callendar');" name="ft" id="ft" value="callendar" <?php if($d['field_type']=="callendar"){ echo "checked=\"checked\""; } ?> /> Календарь</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','nick');" name="ft" id="ft" value="nick" <?php if($d['field_type']=="nick"){ echo "checked=\"checked\""; } ?> /> Определение ника</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td><input type="radio" onclick="set_radio('field_type','dep_list');" name="ft" id="ft" value="dep_list" <?php if($d['field_type']=="dep_list"){ echo "checked=\"checked\""; } ?> /> Список отделов</td>
						</tr>
						<tr height="5">
							<td></td>
						</tr>
						<tr>
							<td>
							<input type="radio" onclick="set_radio('field_type','drop_list');" name="ft" id="ft" value="drop_list" <?php if($d['field_type']=="drop_list"){ echo "checked=\"checked\""; } ?> />  Выпадающий список<br />
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
		<input type="hidden" name="field_type" value="one_string" id="field_type" />
		<?php
	}
	if($target=="save_changes"){
		$q_num=mysql_escape_string($q_num);
		$q_txt=mysql_escape_string($q_txt);
		$field_type=mysql_escape_string($field_type);
		$field_options=mysql_escape_string($field_options);
		$target_id=mysql_escape_string($target_id);
		mysql_query("update police_join_questions set id='".$q_num."', txt='".$q_txt."', field_type='".$field_type."', field_options='".$field_options."' where id='".$target_id."'");
	}
	if($target=="depts"){
		$id=mysql_escape_string($id);
		$name=mysql_escape_string($name);
		if($make=="insert"){mysql_query("INSERT INTO police_join_depts (id,name) values ('".$id."','".$name."')");}
		if($make=="delete"){mysql_query("delete from police_join_depts where id='".$id."';");}
	}
}

if($action=="add_entry"){
	if(AuthStatus==1){
		if($target=="form"){

			$verify = VerifyStatus();
			if ($verify != "no") {
				echo "Незакрытая заявка от персонажа <b>".AuthUserName."</b> уже имеется в базе данных.<br>"; 
				echo "Статус вашей заявки: ".$verify;
			} else {
				$userinfo = GetUserInfo(AuthUserName, 0);
				if($userinfo["level"]>=13){
					?>
					<table cellpadding=3 width=95% cellspacing=3>
						<th colspan=3  background='i/bgr-grid-sand.gif'>Заявка на вступление:</th>
						<tr>
							<td width="20" background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;№:</b></td>
							<td background='i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;Вопрос:</b></td>
							<td background='i/bgr-grid-sand.gif' width="49%" nowrap><b>&nbsp;&nbsp;Ответ:</b></td>
						</tr>
					<?php
					$SQL="SELECT * FROM police_join_questions order by id";
					$r=mysql_query($SQL);
					while($d=mysql_fetch_assoc($r)) {
						?>
						<tr>
							<td width="20" background='i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;<?php echo $d['id']; ?></td>
							<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php echo $d['txt']; ?></td>
							<td background='i/bgr-grid-sand.gif' width="49%" nowrap>
							<?php


							if($d['field_type']=="nick"){
								if($userinfo["clan"]!=""){
									?><img src="http://www.timezero.ru/i/clans/<?php echo $userinfo["clan"]; ?>.gif" style="vertical-align:text-bottom"><?php
								} else {
									echo "<img src=\"_modules/inviz/blank.gif\" width=\"28\" height=\"16\">";
								}
								echo AuthUserName;
								if($userinfo["level"]!=0){ echo "[".$userinfo["level"]."]"; }
								?><a target="_blank" href="http://www.timezero.ru/info.html?<?php echo AuthUserName; ?>">
								<img border="0" src="http://www.timezero.ru/i/i<?php echo $userinfo["pro"]; if($userinfo["man"]==0){echo "w";} ?>.gif" align="middle"	style="vertical-align:text-bottom"></a>
								<input type="hidden" id="answer_<?php echo $d['id']; ?>" name="answer_<?php echo $d['id']; ?>" value="<?php echo AuthUserName; ?>" />
								<?php
							}
							if($d['field_type']=="one_string"){
								?>
								<input type="text" name="answer_<?php echo $d['id']; ?>" id="answer_<?php echo $d['id']; ?>" size="60" />
								<?php
							}
							if($d['field_type']=="multi_string"){
								?>
								<textarea id="answer_<?php echo $d['id']; ?>" name="answer_<?php echo $d['id']; ?>" cols="60" rows="7"></textarea>
								<?php
							}
							if($d['field_type']=="callendar"){
								?>
								<!--<iframe width=174 height=189 name="gToday:normal:agenda.js:gfFlat_arrDate" id="gToday:normal:agenda.js:gfFlat_answer<?php echo $d['id']; ?>" src="_modules/inviz/callendar/iflateng.php?id=<?php echo $d['id']; ?>" scrolling="no" frameborder="0"></iframe><br />!-->
								<input type="text" id="answer_<?php echo $d['id']; ?>" name="answer_<?php echo $d['id']; ?>" size="12" value="<?php echo date("d.m.Y",time()); ?>">
								<?php
							}
							if($d['field_type']=="drop_list"){
								$var=explode(",",$d['field_options']);
								?><select id="answer_<?php echo $d['id']; ?>" name="answer_<?php echo $d['id']; ?>">
								<?php
								for($i=0;$i<sizeof($var);$i++){
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
								$SQL2="SELECT name FROM police_join_depts order by id asc";
								$r2=mysql_query($SQL2);
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
					?></table><br />
	                <input type="checkbox" id="minimum_agreement" onchange="minimum_agreement();" /> Я ознакомлен(а) с минимальными требованиями и уверен(а) что подхожу под них.<br /><br />
					<input id="add_btn" type="button" disabled="disabled" onclick="add_entry();" value="              подать заявку              " /><br />
					<div id="add_load"></div>
					<?php
				} else {
					?><b>Извините, но заявки принимаются от персонажей минимум 13 уровня.</b><?php
				}
			}
		}
		if($target=="add"){

			$verify = VerifyStatus();
			if ($verify != "no") {

				echo "Незакрытая заявка от персонажа <b>".AuthUserName."</b> уже имеется в базе данных.<br>"; 
				echo "Статус вашей заявки: ".$verify;

			} else {
			
				$answ=explode("@#$%^&*next@#$%^&*answer@#$%^&*",$answers);

				$userinfo = GetUserInfo(AuthUserName, 0);
				mysql_query("INSERT INTO police_join_entry (nick,lvl,clan,pro,man,start_date,status) values ('".AuthUserName."','".$userinfo["level"]."','".$userinfo["clan"]."','".$userinfo["pro"]."','".$userinfo["man"]."','".time()."','1')");

				$SQL="SELECT id FROM police_join_entry where nick='".AuthUserName."' and status='1' and lvl='".$userinfo["level"]."' and pro='".$userinfo["pro"]."'";
				$r=mysql_query($SQL);
				$d=mysql_fetch_assoc($r);
				$entry_id=$d['id'];
				$i=0;

				$SQL="SELECT * FROM police_join_questions order by id";
				$r=mysql_query($SQL);
				while($d=mysql_fetch_assoc($r)) {
					$answ[$i]=mysql_escape_string($answ[$i]);
					$n=$i+1;
					mysql_query("INSERT INTO police_join_answ (entry_id,answer_num,question,answer_text) values	('".$entry_id."','".$n."','".$d['txt']."','".$answ[$i]."')");
					$i++;
				}
			}
		}
	} else {
		echo "<b><font color=red>Для подачи заявки необходима <a href='http://www.tzpolice.ru/?act=register'>регистрация на сайте</a>.</font></b>";
	}
}


if(($action=="lists")&&($join_access==1)){
	$param=mysql_escape_string($param);
	if(!$page){$page=1;}

	$SQL="SELECT * FROM police_join_entry";
	if($target=="free") {$SQL.=" where status='1'";}
	if($target=="my") {$SQL.=" where revisor='".AuthUserName."' and status<5";}
	if($target=="archive") {$SQL.=" where status='5' or status='6'";}
	if($target=="other") {$SQL.=" where revisor='".$param."' and status<5";}
	if($target=="search"){$SQL.=" where nick like '%".$param."%'";}
	
	if($target=='reserve') {$SQL.=' WHERE `status`=7';}
	if($target=="archive"){
		$SQL.=" order by end_date desc";
	} else {
		$SQL.=" order by start_date asc";
	}

	// поиск по ФИО - отдельный запрос.
	if ($target=="search_fio") {
		$SQL = "SELECT A.* FROM police_join_entry as A LEFT JOIN police_join_answ as B ON A.id = B.entry_id WHERE B.answer_num = '2' AND B.answer_text like '%".$param."%' order by A.start_date asc";
	}

	//=====================================

	$r=mysql_query($SQL);
	$ex= mysql_num_rows($r);
	$from=$page*20-20;
	$pages=ceil($ex/20);
	$SQL.= " limit ".$from.",20";
	if($d['status']==1){$st="Свободна";}
	?>
	<table cellpadding=3 cellspacing=3>
		<tr><td colspan="4">
		<?php
		echo "Страницы: <b>";
		ShowJoinPages($page,$pages,4);
		echo "</b>";
		?>
		</td></tr>
		<th colspan=6  background='i/bgr-grid-sand.gif'>
		<?php if($target=="free") { ?>Свободные заявки:<?php } ?>
		<?php if($target=="archive") { ?>Архив заявок:<?php } ?>
		<?php if($target=="my") { ?>Заявки <?php echo AuthUserName; ?>:<?php } ?>
		<?php if($target=="other") { ?>Заявки <?php echo $param; ?>:<?php } ?>
		<?php if($target=="search") { ?>Результаты поиска:<?php echo $param; ?>:<?php } ?>
		<?php if($target=="reserve") { ?>Резерв:<?php } ?>

		</th>
		<tr>
			<td width="200" background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;Ник:</b></td>
			<?php if(($target=="my")||($target=="other")){ ?><td background='i/bgr-grid-sand.gif' width="20"></td><? } ?>
			<td background='i/bgr-grid-sand.gif' width="100"><b>&nbsp;&nbsp;<?php if($target=="archive"){ ?>Статус:<?php } else { ?>Дата рождения:<?php } ?></b></td>
			<td background='i/bgr-grid-sand.gif' width="60"><b>Возраст:</b></td>
			<td background='i/bgr-grid-sand.gif' width="130" nowrap><b>&nbsp;&nbsp;<?php if($target=="archive"){ ?>Рассмотренно<?php } else { ?>Подано<?php } ?>:</b></td>
			<td background='i/bgr-grid-sand.gif' width="150" nowrap><b>&nbsp;&nbsp;Действия:</b></td>
		</tr>
	<?php
	$r=mysql_query($SQL);
	while($d=mysql_fetch_assoc($r)) {
		$SQL2="SELECT * FROM police_join_answ where entry_id='".$d['id']."' and answer_num='3'";
		$r2=mysql_query($SQL2);
		$d2=mysql_fetch_assoc($r2);
			if($d['status']==2){$st="Рассматривается";}
			if($d['status']==3){$st="Рассматривается";}
			if($d['status']==4){$st="Рассматривается";}
			if($d['status']==5){$st="<font color=green><b>принят(а)</b></font>";}
			if($d['status']==6){$st="<font color=red><b>отказано</b><br>&nbsp;&nbsp;Причина: ".$d['status_comment']."</font>";}

		$d2['answer_text']=str_replace("/",".",$d2['answer_text']);
		$d2['answer_text']=str_replace(",",".",$d2['answer_text']);
		$d2['answer_text']=str_replace(" ",".",$d2['answer_text']);
		$d2['answer_text']=str_replace("-",".",$d2['answer_text']);
		list($c1,$c2,$c3)= split ('[.]', $d2['answer_text']);
		$age=date("Y",time())-$c3;
		if(mktime(0, 0, 0, $c2, $c1, 1970)>mktime(0, 0, 0, date("m",time()), date("d",time()), 1970)) {$age--;}

		?>
		<tr>
			<td width="200" background='i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;
			<?php
			if($d["clan"]!=""){
				?><img src="http://www.timezero.ru/i/clans/<?php echo $d["clan"]; ?>.gif" style="vertical-align:text-bottom"><?php
			} else {
				echo "<img src=\"_modules/inviz/blank.gif\" width=\"28\" height=\"16\">";
			}
			echo $d['nick'];
			if($d["lvl"]!=0){ echo "[".$d["lvl"]."]"; }
			?><a target="_blank" href="http://www.timezero.ru/info.html?<?php echo $d['nick']; ?>">
			<img border="0" src="http://www.timezero.ru/i/i<?php echo $d["pro"]; if($d["man"]==0){echo "w";} ?>.gif" align="middle" style="vertical-align:text-bottom"></a>
			</td>
			<?php if(($target=="my")||($target=="other")){ ?><td background='i/bgr-grid-sand.gif' width="20">
			<?php if($d['status']==3){ ?><img border="0" src="_modules/inviz/img/cell.gif" /><?php } ?>
			<?php if($d['status']==4){ ?><img border="0" src="_modules/inviz/img/clock.gif" /><?php } ?>
			</td><? } ?>
			<td background='i/bgr-grid-sand.gif' width="100">&nbsp;&nbsp;<?php if($target!="archive"){ echo $d2['answer_text']; } else { echo $st; } ?></td>
			<td background='i/bgr-grid-sand.gif' width="60" nowrap>&nbsp;&nbsp;<?php echo $age; ?></td>
			<td background='i/bgr-grid-sand.gif' width="130" nowrap>&nbsp;&nbsp;<?php echo date("d.m.y H:i:s",$d['start_date']); ?></td>
			<td background='i/bgr-grid-sand.gif' width="150" nowrap>&nbsp;&nbsp;<a href="javascript:{}" onclick="show_entry('show_entry','','<?php echo $d['id']; ?>','');">смотреть</a>
			<?php if($d['status']==1){ ?>&nbsp;&nbsp;&nbsp;<a href="javascript:{}" onclick="show_entry('get_entry','','<?php echo $d['id']; ?>','');">принять</a><?php } ?></td>
		</tr>
		<?php
	}

}

if(($action=="show_entry")&&($join_access==1)){
	$param=mysql_escape_string($param);
	?>
	<table cellpadding=3 width=95% cellspacing=3>
		<th colspan=3  background='i/bgr-grid-sand.gif'>Заявка на вступление:</th>
	<?php
	$SQL="SELECT * FROM police_join_entry where id='".$param."'";
	$r=mysql_query($SQL);
	$ex= mysql_num_rows($r);
	$e=mysql_fetch_assoc($r);


	if($e['status']=="1"){$status="В очереди на рассмотрение";}
	if($e['status']=="2"){$status="Принята на рассмотрение";}
	if($e['status']=="3"){$status="Свяжитесь с <img border=0 src='_imgs/clansld/police.gif'><font color=black>".$e['revisor']."</font> для проведения собеседования";}
	if($e['status']=="4"){$status="Ожидается вынесение решения по заявке";}
	if($e['status']=="5"){$status="Принят(а) в ряды полиции";}
	if($e['status']=="6"){$status="<font color=red>Отказано во вступлении</font>";}
	if($e['status']=="7"){$status="Внесена в <u>резерв</u>.";}
	if($e['status']>1){
		?>
		<tr>
			<td width="20" background='i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
			<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<b>Статус заявки:</b></td>
			<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<font color="green"><b><?php echo $status; ?></b></font></td>
		</tr>
		<?php
	}
	if($e['status']==6){
		?>
		<tr>
			<td width="20" background='i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
			<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<b>Причина:</b></td>
			<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<font color="green"><b><font color=red><?php echo $e['status_comment']; ?></font></b></font></td>
		</tr>
		<?php
	}
	if($e['revisor']!=""){
		?>
		<tr>
			<td width="20" background='i/bgr-grid-sand.gif' nowrap>&nbsp;</td>
			<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;Заявку рассматривает:</td>
			<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<img border=0 src='_imgs/clansld/police.gif'><?php echo $e['revisor']; ?></td>
		</tr>
		<?php
	}
	?>
		<tr>
			<td width="20" background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;№:</b></td>
			<td background='i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;Вопрос:</b></td>
			<td background='i/bgr-grid-sand.gif' width="49%"><b>&nbsp;&nbsp;Ответ:</b></td>
		</tr>
	<?php
	$SQL="SELECT a.answer_text, a.answer_num, a.question, q.field_type FROM police_join_answ a LEFT JOIN police_join_questions q ON a.answer_num=q.id where a.entry_id='".$e['id']."' order by answer_num asc";
	$r=mysql_query($SQL);
	while($d=mysql_fetch_assoc($r)) {

		?>
		<tr>
			<td width="20" background='i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;<?php echo $d['answer_num']; ?></td>
			<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php echo $d['question']; ?></td>
			<td background='i/bgr-grid-sand.gif' width="49%">&nbsp;&nbsp;<?php
			if($d['field_type']=="nick"){
				if($e["clan"]!=""){
					?><div id="user_nick_data"><img src="http://www.timezero.ru/i/clans/<?php echo $e["clan"]; ?>.gif" style="vertical-align:text-bottom"><?php
				} else {
					echo "<div id=\"user_nick_data\"><img src=\"_modules/inviz/blank.gif\" width=\"28\" height=\"16\">";
				}
				echo $d['answer_text'];
				if($e["lvl"]!=0){ echo "[".$e["lvl"]."]"; }
				?><a target="_blank" href="http://www.timezero.ru/info.html?<?php echo $d['answer_text']; ?>">
				<img border="0" src="http://www.timezero.ru/i/i<?php echo $e["pro"]; if($e["man"]==0){echo "w";} ?>.gif" align="middle" style="vertical-align:text-bottom"></a></div> <div id="user_nick_data_img"><a href="javascript:{}" onclick="reload_user_nick_data('<?php echo $param; ?>');" />[обновить]</a></div>
				<?php
			} else {
				echo $d['answer_text'];
			}
			?></td>
		</tr>
		<?php
	}
	?>
	<tr height="25"><td colspan="3"></td></tr>
	<tr><td colspan="3">
	<b>Действия:</b><br />
	<?php
	if($e['status']==1){
		?><a href="javascript:{}" onclick="show_entry('get_entry','','<?php echo $e['id']; ?>','');">принять заявку</a><br />
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
//		if(($e['revisor']==AuthUserName)||($join_admin==1)){
		?>
		<a href="javascript:{}" onclick="take_archive('<?php echo $e['id']; ?>');">принять заявку</a><br />
<!--		<a href="javascript:{}" onclick="set_status('<?php echo $e['id']; ?>','5','0');">принять в полицию</a><br />-->
		<a href="javascript:{}" onclick="show_reject();">отказать</a>
		<?php
//		}
	}
	?><br />
	<div id="reject_form" style="display:none"><input type="text" size="20" name="comment_text" id="comment_text" value="Вы не соответствуете нашим требованиям" /><input type="button" value="  отказать  " name="reject_btn" id="reject_btn" onclick="set_status('<?php echo $e['id']; ?>','6','1');" /></div><br />
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

	$SQL="SELECT * FROM police_join_entry where nick='".$e['nick']."' and id<>'".$e['id']."' order by start_date DESC";
	$r=mysql_query($SQL);
	$ex= mysql_num_rows($r);
	if($ex>0){
		?><table cellpadding=3 cellspacing=3>
		<th colspan=5  background='i/bgr-grid-sand.gif'>Игрок уже подавал заявки:</th>
		<tr>
			<td width="200" background='i/bgr-grid-sand.gif' nowrap><b>&nbsp;&nbsp;Ник:</b></td>
			<td background='i/bgr-grid-sand.gif' width="130"><b>&nbsp;&nbsp;Подано:</b></td>
			<td background='i/bgr-grid-sand.gif' width="130" nowrap><b>&nbsp;&nbsp;Рассмотрено:</b></td>
			<td background='i/bgr-grid-sand.gif' width="150" nowrap><b>&nbsp;&nbsp;Результат:</b></td>
			<td background='i/bgr-grid-sand.gif' width="70" nowrap><b>&nbsp;&nbsp;Действия:</b></td>
		</tr>
		<?php
		while($d=mysql_fetch_assoc($r)) {
			if($d['status']==1){$st="Свободна";}
			if($d['status']==2){$st="Рассматривается";}
			if($d['status']==3){$st="Рассматривается";}
			if($d['status']==4){$st="Рассматривается";}
			if($d['status']==5){$st="<font color=green><b>принят(а)</b></font>";}
			if($d['status']==6){$st="<font color=red><b>отказано</b><br>&nbsp;&nbsp;Причина: ".$d['status_comment']."</font>";}
			if($d['status']==7){$st="<font color=yellow><b>резерв</b><br>&nbsp;&nbsp;Примечание: ".$d['status_comment']."</font>";}
			?>
			<tr>
				<td width="200" background='i/bgr-grid-sand.gif' nowrap>&nbsp;&nbsp;
				<?php
				if($d["clan"]!=""){
					?><img src="http://www.timezero.ru/i/clans/<?php echo $d["clan"]; ?>.gif" style="vertical-align:text-bottom"><?php
				} else {
					echo "<img src=\"_modules/inviz/blank.gif\" width=\"28\" height=\"16\">";
				}
				echo $d['nick'];
				if($d["lvl"]!=0){ echo "[".$d["lvl"]."]"; }
				?><a target="_blank" href="http://www.timezero.ru/info.html?<?php echo $d['nick']; ?>">
				<img border="0" src="http://www.timezero.ru/i/i<?php echo $d["pro"]; if($d["man"]==0){echo "w";} ?>.gif" align="middle" style="vertical-align:text-bottom"></a>
				</td>
				<td background='i/bgr-grid-sand.gif' width="130">&nbsp;&nbsp;<?php echo date("d.m.y H:i:s",$d['start_date']); ?></td>
				<td background='i/bgr-grid-sand.gif' width="130" nowrap>&nbsp;&nbsp;<?php echo date("d.m.y H:i:s",$d['end_date']); ?></td>
				<td background='i/bgr-grid-sand.gif' width="150">&nbsp;&nbsp;<?php echo $st; ?></td>
				<td background='i/bgr-grid-sand.gif' width="70" nowrap>&nbsp;&nbsp;<a href="javascript:{}" onclick="show_entry('show_entry','','<?php echo $d['id']; ?>','');">смотреть</a>
				<?php
				if($d['status']==1){
				?><br />&nbsp;&nbsp;<a href="javascript:{}" onclick="show_entry('get_entry','','<?php echo $d['id']; ?>','');">принять</a>
				<?php
				}
				?>
				</td>
			</tr>
			<?php
		}
	}



}
if(($action=="get_entry")&&($join_access==1)){
	$param=mysql_escape_string($param);
	mysql_query("UPDATE police_join_entry set status='2', revisor='".AuthUserName."' where id='".$param."';");
}
if(($action=="take_arch")&&($join_access==1)){
	$param=mysql_escape_string($param);
	mysql_query("UPDATE police_join_entry set status='2', revisor='".AuthUserName."' where id='".$param."';") or die (mysql_error());
}

if(($action=="set_status")&&($join_access==1)){
	$id=mysql_escape_string($id);
	$status=mysql_escape_string($status);
	$comment=mysql_escape_string($comment);
	mysql_query("UPDATE police_join_entry set status='".$status."', status_comment='".$comment."', end_date='".time()."', revisor='".AuthUserName."' where id='".$id."';");
}

if(($action=="reload_user_nick_data")&&($join_access==1)){
	$id=mysql_escape_string($id);
	$r=mysql_query("SELECT `nick` FROM police_join_entry where `id`='".$id."' limit 1");
	$d=mysql_fetch_assoc($r);
	$userinfo = GetUserInfo($d['nick'], 0);
	if($userinfo["level"]>=1){
		mysql_query("UPDATE police_join_entry set lvl='".$userinfo["level"]."', clan='".$userinfo["clan"]."', pro='".$userinfo["pro"]."', man='".$userinfo["man"]."' where id='".$id."';");
		if($userinfo["clan"]!=""){
			?><img src="http://www.timezero.ru/i/clans/<?php echo $userinfo["clan"]; ?>.gif" style="vertical-align:text-bottom"><?php
		} else {
			echo "<img src=\"_modules/inviz/blank.gif\" width=\"28\" height=\"16\">";
		}
		echo $d['nick'];
		if($userinfo["level"]!=0){ echo "[".$userinfo["level"]."]"; }
		?><a target="_blank" href="http://www.timezero.ru/info.html?<?php echo $d['nick']; ?>">
		<img border="0" src="http://www.timezero.ru/i/i<?php echo $userinfo["pro"]; if($userinfo["man"]==0){echo "w";} ?>.gif" align="middle" style="vertical-align:text-bottom"></a>
		<?php
	} else {
		echo 0;
	}
}

?>