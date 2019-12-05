<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Untitled Document</title>
<style type="text/css">
<!--
.table {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #003366;
}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>
</head>
<body>
<?
error_reporting(0);
//error_reporting(E_ALL);
$hotels['inturist1'] = "Интурист, одноместный";
$hotels['inturist2'] = "Интурист, двухместный";
$hotels['inturist3'] = "Интурист, полулюкс, двухместный";
$hotels['inturist4'] = "Интурист, люкс";
$hotels['turist1'] = "Турист, одноместный";
$hotels['turist2'] = "Турист, двухместный";
$hotels['turist3'] = "Турист, полулюкс";
$hotels['turist4'] = "Турист, люкс";
$hotels['elbrus1'] = "Эльбрус, одноместный";
$hotels['elbrus2'] = "Эльбрус, двухместный";
$hotels['elbrus3'] = "Эльбрус, люкс одноместный";
$hotels['elbrus4'] = "Эльбрус, люкс двухместный";
if($_REQUEST['do'] == "send")
	{
		$message = "Регистрационная форма\n\n******************************\n\n";
        $message .= "Ф.И.О.: ".$_REQUEST['surname']." ".$_REQUEST['name']." ".$_REQUEST['fath_name']."\n\n";
        $message .= "Организация: ".$_REQUEST['org']."\n".$_REQUEST['org_address']."\n\n";
        $message .= "Тел.: ".$_REQUEST['phone']."\n e-mail: ".$_REQUEST['mail']."\n Факс: ".$_REQUEST['fax']."\n\n\n";
		if ($_REQUEST['a15min']) {$message .= "Доклад на пленарном заседании: ".$_REQUEST['a15min_title']."\n";}
		if ($_REQUEST['a10min']) {$message .= "Сообщение на круглом столе: ".$_REQUEST['a10min_title']."\n";}
		if ($_REQUEST['need_soft']) {$message .= "Требуемое ПО: ".$_REQUEST['soft_needed']."\n";}
        if ($_REQUEST['want_hotel']) {$message .= "\n\n Прошу забронировать место в гостинице: ".$hotels[$_REQUEST['radiobutton']]."\n\n";}
        $message .= "\n Дата приезда: ".$_REQUEST['arrive_date']."\n Место приезда: ".$_REQUEST['arrive_place']."\n Дата отъезда: ".$_REQUEST['leave_date']."\n Место отъезда: ".$_REQUEST['leave_place']."\n Заявка отправлена: ".date('d.m.Y, H:i')."\n******************************";


// In case any of our lines are larger than 70 characters, we should use wordwrap()
$message = wordwrap($message, 70);
$headers = 'From: webmaster@tzpolice.ru' . "\r\n" .
   'Reply-To: noreply@tzpolice.ru' . "\r\n" .
   'X-Mailer: PHP/' . phpversion() . "\r\n" .
   'Content-Type: text/plain; charset=windows-1251';
// Send
mail('deti@stv.runnet.ru', 'Регистрация участников 21-22 сентября', $message, $headers);
//mail('deadbeef@tzpolice.ru', 'Регистрация участников 21-22 сентября', $message, $headers);
echo ("<div align='center' class='table'><b>СПАСИБО!</b><br>Ваша заявка отправлена.<br><br><br></div>");
    }

?>
<br><br>
<center>
<table width="550" border="0" cellpadding="0" cellspacing="0" class="table">
	<tr><td align="center"><form id="form1" name="form1" method="post" action="?do=send">
		<p><strong>РЕГИСТРАЦИОННАЯ  ФОРМА</strong><br />
			участника межрегиональной научно-практической  конференции<br />
			<em>«Читатели – дети в информационном  пространстве региона:</em><br />
			<em>проблемы, решения, перспективы»</em><br />
			<strong>21-22  сентября 2006 года</strong>		</p>
		<p>&nbsp;</p>
		<table width="450" border="0" cellpadding="0" cellspacing="0" class="table">
	<tr><td>Фамилия</td><td><input name="surname" type="text" id="surname" /></td></tr><tr><td>Имя</td><td><input name="name" type="text" id="name" /></td></tr><tr><td>Отчество</td><td><input name="fath_name" type="text" id="fath_name" /></td></tr><tr><td>Организация</td><td><input name="org" type="text" id="org" /></td></tr><tr><td>Адрес организации </td><td><textarea name="org_address" rows="3" wrap="virtual" id="org_address"></textarea></td></tr><tr><td>Контактный телефон </td><td><input name="phone" type="text" id="phone" /></td></tr>
				<tr>
					<td>e-mail</td>
					<td><input name="mail" type="text" id="mail" /></td>
				</tr>
				<tr>
					<td>Факс</td>
					<td><input name="fax" type="text" id="fax" /></td>
				</tr>
			</table>
				<b><br />
				Форма участия</b> <br />
				<table width="450" border="0" cellpadding="0" cellspacing="0" class="table">
					<tr>
						<td><input name="a15min" type="checkbox" id="a15min" value="a15min" />
							Доклад на пленарном заседании (15 мин.). Тема доклада: </td>
						<td><input name="a15min_title" type="text" id="a15min_title" /></td>
					</tr>
					<tr>
						<td><input name="a10min" type="checkbox" id="a10min" value="a10min" />
							Сообщение на круглом столе (до 10 мин.). Тема сообщения: </td>
						<td><input name="a10min_title" type="text" id="a10min_title" /></td>
					</tr>
					<tr>
						<td><input name="need_soft" type="checkbox" id="need_soft" value="need_soft" />
							Для доклада требуется следующее программное обеспечение:</td>
						<td><textarea name="soft_needed" rows="5" wrap="virtual" id="soft_needed"></textarea></td>
					</tr>
				</table>
				<br />
				<br />
				<b>
				<input name="want_hotel" type="checkbox" id="want_hotel" value="want_hotel" />
				Прошу забронировать мне место в гостинице</b>:
				<br />
				<br />
				<p><strong>Гостиница «Интурист», пр. К. Маркса, 42. </strong><br />
						<strong>Тел. администратора:  946-946.</strong><br />
					Доп.услуги, досуг и отдых:  бизнес-центр, массаж, салон красоты, факс, бильярд, казино, кафе, ресторан,  сауна. Завтрак входит в стоимость. Бронь – 25% от стоимости номера, по безналичному  расчету, за 10-14 дней.</p>
				<table border="1" cellpadding="3" cellspacing="1" bordercolor="#333333" class="table">
					<tr>
						<td width="207" valign="top"><p align="center">Тип номера</p></td>
						<td width="207" valign="top"><p align="center">Стоимость суток (в    руб.)</p></td>
						<td width="180" valign="top"><p align="center">Выбор</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>Одноместный</p></td>
						<td width="207" valign="top"><p align="center">950</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="inturist1" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>Двухместный</p></td>
						<td width="207" valign="top"><p align="center">1200</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="inturist2" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>Полулюкс, двухместный</p></td>
						<td width="207" valign="top"><p align="center">1500</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="inturist3" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>Люкс</p></td>
						<td width="207" valign="top"><p align="center">от 2800 до 3200</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="inturist4" />
						</p></td>
					</tr>
				</table>
				<p><strong>Гостиница «Турист»,  ул. Ленина, 273. </strong><br />
					<strong>Тел. администратора:  36-00-04</strong></p>
				<p>Доп. услуги: кафе, ресторан,  магазины. Бронь – 30% от стоимости номера, за 15 дней.<br />
				</p>
				<table border="1" cellpadding="3" cellspacing="1" bordercolor="#333333" class="table">
					<tr>
						<td width="207" valign="top"><p align="center">Тип номера</p></td>
						<td width="207" valign="top"><p align="center">Стоимость  суток (в  руб.)</p></td>
						<td width="180" valign="top"><p align="center">Выбор</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>Одноместный</p></td>
						<td width="207" valign="top"><p align="center">от 497 до 907</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="turist1" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>Двухместный</p></td>
						<td width="207" valign="top"><p align="center">от 760 до 1200</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="turist2" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>Полулюкс</p></td>
						<td width="207" valign="top"><p align="center">2300</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="turist3" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>Люкс</p></td>
						<td width="207" valign="top"><p align="center">3500</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="turist4" />
						</p></td>
					</tr>
				</table>
				<br />
				<p><strong>Гостиница «Эльбрус»,  ул.Горького, 43. </strong><br />
						<strong>Тел. администратора:  27-04-00.</strong></p>
				<p>Бронь – 25% от стоимости номера,  длительную сохранность брони не гарантируют (примерно 10 дней). </p>
				<table border="1" cellpadding="3" cellspacing="1" bordercolor="#333333" class="table">
					<tr>
						<td width="207" valign="top"><p align="center">Тип номера</p></td>
						<td width="207" valign="top"><p align="center">Стоимость суток (в    руб.)</p></td>
						<td width="180" valign="top"><p align="center">Выбор</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>Одноместный</p></td>
						<td width="207" valign="top"><p align="center">от 230 до 1000</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="elbrus1" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>Двухместный</p></td>
						<td width="207" valign="top"><p align="center">от 420 до 480</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="elbrus2" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>Люкс одноместный</p></td>
						<td width="207" valign="top"><p align="center">1000</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="elbrus3" />
						</p></td>
					</tr>
					<tr>
						<td width="207" valign="top"><p>Люкс двухместный</p></td>
						<td width="207" valign="top"><p align="center">1200</p></td>
						<td width="180" valign="top"><p align="right">
							<input name="radiobutton" type="radio" value="elbrus4" />
						</p></td>
					</tr>
				</table>
				<br />
				<br />
				<table width="560" border="0" cellpadding="0" cellspacing="0" class="table">
					<tr>
						<td width="208" valign="top"><p>Дата    приезда</p></td>
						<td width="352" valign="top"><p>
							<input name="arrive_date" type="text" id="arrive_date" />
						</p></td>
					</tr>
					<tr>
						<td width="208" valign="top"><p>Место    и время прибытия</p></td>
						<td width="352" valign="top"><p>
							<input name="arrive_place" type="text" id="arrive_place" />
						</p></td>
					</tr>
					<tr>
						<td width="208" valign="top"><p>Дата    отъезда</p></td>
						<td width="352" valign="top"><p>
							<input name="leave_date" type="text" id="leave_date" />
						</p></td>
					</tr>
					<tr>
						<td width="208" valign="top"><p>Место    и время отъезда</p></td>
						<td width="352" valign="top"><p>
							<input name="leave_place" type="text" id="leave_place" />
						</p></td>
					</tr>
				</table>
				<p align="left"><i>* Бронирование места в гостинице  будет осуществляться только при получении заполненной регистрационной формы.<br />
					Билеты  на обратный проезд просим приобретать заранее на местах.<br />
				Для организации встречи и проводов  участников конференции точную дату, время и место прибытия и отъезда просим  сообщить заблаговременно.</i></p>
				<input type="submit" name="Submit" value="Отправить" />
				<br />
		</form>
		</td>
	</tr>
</table>
</center>
</body>
</html>