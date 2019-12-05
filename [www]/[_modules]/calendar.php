<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<body style="margin:0px">
<style>
.dninedeli {cursor:pointer; font-size: 11pt; width:17px; text-align:center; color: #1a4d7e;}
.dateon {border: 1px solid #cccccc; background: #eeeeee; cursor:pointer; font-size: 11pt; width:17px; text-align:center;}
.dateoff {border: 1px solid #ffffff; background: #ffffff; cursor:pointer; font-size: 11pt; width:17px; text-align:center;}
</style>
<?
if ($_POST['form']) {$form=$_POST['form'];}
$months=array(0=>array("Январь", 31), 1=>array("Февраль", 28), 2=>array("Март", 31), 3=>array("Апрель", 30), 4=>array("Май", 31), 5=>array("Июнь", 30), 6=>array("Июль", 31), 7=>array("Август", 31), 8=>array("Сентябрь", 30), 9=>array("Октябрь", 31), 10=>array("Ноябрь", 30), 11=>array("Декабрь", 31));
$time=time();
$curyear=date("Y",$time);

if ($curyear%4==0) {$months[1][1]=29;}
//echo date("D",$time-86400*1);
/* Mon Tue Wed Thu Fri Sat Sun*/
($_GET['tecmonth'] || isset($_GET['tecmonth']))?$tecmonth=$_GET['tecmonth']:$tecmonth=date("m",$time);
$calendar ='<table cellpadding=0 cellspacing=0 border=0>';
$larr='<font style="color:red;font-weight:bold">&#171;</font> ';
$rarr=' <font style="color:red;font-weight:bold">&#187;</font>';
$last=$tecmonth-1; $next=$tecmonth+1;
for ($i=0; $i<count($months); $i++) {
	if ($tecmonth < 1) {$tecmonth=$tecmonth+12;$curyear--;}
	if ($tecmonth > 12) {$tecmonth=$tecmonth-12;$curyear++;}
	(($i+1) == $tecmonth)?$viz='style="display:block"':$viz='style="display:none"';
	$larr='<a href="'.$_SERVER['PHP_SELF'].'?tecmonth='.$last.'&form='.$form.'" style="text-decoration: none"><font style="color:red;font-weight:bold">&#171;</font></a> ';
	$rarr=' <a href="'.$_SERVER['PHP_SELF'].'?tecmonth='.$next.'&form='.$form.'" style="text-decoration: none"><font style="color:red;font-weight:bold">&#187;</font></a>';

	$calendar .= '<tr '.$viz.'><td><table cellpadding=2 cellspacing=1 border=0><tr><td align=center>'.$larr.'<td align=center colspan=3>'.$months[$i][0].'<td align=center>'.$rarr.'</td><td align=center colspan=2>'.$curyear.'</td></tr><tr align=center><td class=dninedeli>Пн</td><td class=dninedeli>Вт</td><td class=dninedeli>Ср</td><td class=dninedeli>Чт</td><td class=dninedeli>Пт</td><td class=dninedeli>Сб</td><td class=dninedeli>Вс</td></tr><tr>';		
	$postcalendar='';
	for ($j=0; $j<$months[$i][1]; $j++) {
	if (date("D", mktime(0, 0, 0, ($i+1), $j, date("y",$time))) =='Sun') {
		if ($j < 7 && $j !=0) {$precalendar .='<td colspan='.(7-$j).'>&nbsp;</td>';}
		$calendar .=$precalendar.$postcalendar.'</tr><tr>';$postcalendar='';$precalendar='';}
	$sendmonth=(strlen($i+1)<2)?"0".($i+1):($i+1);
	$postcalendar .= '<td class=dateoff onmouseover="this.className=\'dateon\'" onmouseout="this.className=\'dateoff\'" onclick="window.opener.document.'.$form.'.value=\''.((strlen($j+1)<2)?"0".($j+1):($j+1)).'.'.((strlen($i+1)<2)?"0".($i+1):($i+1)).'.'.$curyear.'\'; window.close();">'.($j+1).'</td>';

	}
	$calendar .=$postcalendar;
	$calendar .= '</tr></table></td></tr>';
	if ($tecmonth < 1) {$year--;}
}
$calendar .= '</table>';
echo $calendar;
?>
</body>