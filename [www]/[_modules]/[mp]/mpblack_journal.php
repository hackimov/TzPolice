<?php
if(!defined('MPLIB')) die('Лесом.');

if(isset($in['begindate']) && $in['begindate'] != ""){
	$begindate = explode("-",$in['begindate']);
} else {
	$in['begindate'] = time();
	$begindate = explode("-",date("d-m-Y", $in['begindate']));
}

$in['begindate'] = mktime(0, 0, 0, $begindate[1], $begindate[0], $begindate[2]);

$wheremass[] = " a.date >= ".$in['begindate'];

if(isset($in['enddate']) && $in['enddate'] != ""){
	$enddate = explode("-",$in['enddate']);
} else {
	$in['enddate'] = time();
	$enddate = explode("-",date("d-m-Y", $in['enddate']));
}

$in['enddate'] = mktime(23, 59, 59, $enddate[1], $enddate[0], $enddate[2]);

$wheremass[] = " a.date <= ".$in['enddate'];

$selectcop = " по сотруднику МП: <select name=\"mcop\"><option value=\"\"></option>";
$querytext = "SELECT DISTINCT a.user_id as id, b.user_name AS mcop FROM mp_black_list_journal AS a LEFT JOIN site_users AS b ON a.user_id = b.id";
$query = mysql_query($querytext ,$db);
while ($row = mysql_fetch_array($query)) {
	$selectcop .= "<option value=\"".$row['id']."\" ".(($in['mcop'] == $row['id'])?"SELECTED":"").">".$row['mcop']."</option>";
}
$selectcop .= "</select>";

if ($in['mcop'] > 0) {
	$wheremass[] = " a.user_id = ".$in['mcop'];

}

$text = "<script type=\"text/javascript\">

  $(function()
    {
		$('.calendar').datePicker({
			createButton:false,
			clickInput:true
		});
    });

</script>\n";



$text .= '<center><BIG><B>Журнал регистрации изменений ЧС МП <BR>'.date("d.m.Y", $in['begindate']).' 00:00 - '.date("d.m.Y", $in['enddate']).' 23:59</B></BIG></center>';

$text .= "<hr><DIV STYLE=\"WIDTH:100%;\">\n";
$text .= '<form action="'.$_mp_targetURL.'" method="post">
		<input type="hidden" name="target" value="journal" />
		<input type="hidden" name="act" value="mpblack" />
		Показать данные за период с: <input class="calendar" value="'.date("d-m-Y", $in['begindate']).'" name="begindate" size="20" />
		 по: <input class="calendar" value="'.date("d-m-Y", $in['enddate']).'" name="enddate" size="20" />
		'.$selectcop.'
		<input type="submit" value="Показать"  />';
		

$text .= "</form>\n</DIV><br>";

$where = " WHERE ".implode(" AND ", $wheremass);

$querytext = "SELECT
		a.date AS eventdate, 
		a.bl_nick AS nickk, 
		a.act AS actk, 
		a.before_val AS beforek, 
		a.after_val AS afterk, 
		b.user_name AS autork
		FROM
		mp_black_list_journal AS a
		LEFT JOIN site_users AS b ON a.user_id = b.id".$where." ORDER BY eventdate DESC";

$query = mysql_query($querytext ,$db);

$pers_act = array(1,2,3,5,7,8,10);

$count = mysql_num_rows($query);
if ($count > 0 ) {
	$text .= "<table width=\"100%\" cellpadding=\"3\"><tr><td><b>Дата</b></td><td><b>Сотрудник МП</b></td><td><b>Персонаж | Клан</b></td><td><b>Событие</b></td></tr>";
	$counter = 0;
	while ($row = mysql_fetch_array($query)) {
		$background = "background='http://www.tzpolice.ru//i/bgr-grid-sand".(($counter) ? "" : "1").".gif'";
		$text .= "<tr><td ".$background.">";
		$text .= date("d.m.y G:i", $row['eventdate']);
		$text .= "</td><td ".$background.">";
		$text .= $row['autork'];
		$text .= "</td><td ".$background.">";
		if (in_array($row['actk'],$pers_act)) {  // если действие по персу - делаем ссылку на ЧСА перса. иначе на чс клана
			$text .= "<a href=\"http://www.tzpolice.ru/?act=mpblack&nickName=".$row['nickk']."\" target=\"_blank\">".$row['nickk']."</a>";
		} else {
			$text .= "<a href=\"http://www.tzpolice.ru/?act=mpblack&Clan=".$row['nickk']."\" target=\"_blank\">".$row['nickk']."</a>";
		}	
		$text .= "</td><td ".$background.">";
		$text .=  journal_act_format($row['actk'], $row['beforek'], $row['afterk']);
		$text .= "</td></tr>";
		$counter = (++$counter)%2;
	}
	$text .= "</table>";


} else {
	$text .= "<BR>\n Даных, соответствующих текущему отбору не найдено.";

}


echo $text;

?>