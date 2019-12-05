<?php
require('../functions.php');
require('../auth.php');
require ("fees_function.php");
/**
 * @author deadbeef
 * @copyright 2011
 */

$query = "SELECT * FROM `tzpolice_fees` WHERE `payed` < `summa` AND `prison`='0' LIMIT 40,5";
$res = mysql_query($query);
while ($row = mysql_fetch_array($res))
	{
		$message = "ПОВТОРНОЕ СООБЩЕНИЕ!\rПо техническим причинам вы могли не получить сообщение о назначении вам штрафа. Если сообщение о данном нарушении уже было получено - можете проигнорировать это сообщение\r\r".date("d.m.Y H:i", $row['time'])." вам был выписан штраф на сумму ".$row['summa']." мм, на данный момент оплачено ".$row['payed']." мм. Причина штрафа: ".$row['text'].". Оплата штрафа должна быть произведена путем перевода суммы штрафа на персонажа [Terminal 02] в течение суток с момента прочтения телеграммы. Если у вас возникли вопросы по поводу данного сообщения - обратитесь к персонажу [LEO-OO] в чате игры.\r\rПриносим извинения за возможные неудобства, ИТ-отдел полиции Точки Отсчета.";
		$q = "SELECT `name` FROM `tzpolice_tz_users` WHERE `id`='".$row['name_id']."' LIMIT 1;";
		$r = mysql_query($q);
		$r2 = mysql_fetch_array($r);
		fees_send_telegramm($r2['name'], $message);
		echo ($r2['name']." ".$message."<hr>");
		//fees_send_telegramm('дохлое мясо', $message);
	}

?>