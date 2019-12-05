<?php
 error_reporting(E_ERROR);
 define( "DEBUG", 0);
?>
<html>
<head>
<title>Log parser</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<link href="includes/style.css" type="text/css" rel="stylesheet">
<style type=text/css>
body {
    SCROLLBAR-FACE-COLOR: #6EA6D1;
    SCROLLBAR-HIGHLIGHT-COLOR: white ;
    SCROLLBAR-SHADOW-COLOR: #6EA6D1 ;
    SCROLLBAR-3DLIGHT-COLOR: 0D69B3;
    SCROLLBAR-3DLIGHT-COLOR: #6EA6D1;
    SCROLLBAR-ARROW-COLOR: white;
    SCROLLBAR-TRACK-COLOR: white;
    SCROLLBAR-DARKSHADOW-COLOR: #6EA6D1;
    margin: 20px;
}
.menu {
	background: #608080;
	border: 3px double white;
	width: 250px;
	float:left;
}
</style>
<script>
var old_div;
old_div = 0;
function show(div_id) {
	if ( old_div )
		document.getElementById(old_div).style["display"]="none";
	div = document.getElementById(div_id);
	if (div.style["display"]=='none')
		div.style["display"]="block";
	else
		div.style["display"]="none";
	old_div = div_id;
}
</script>
</head>
<body>
<?
$balance = array(
	"shops"=>array(),
	"banks"=>array(),
	"battles"=>array(),
	"chars"=>array(),
	"mail"=>array(),
	"plants"=>array(),
	"locations"=>array(),
	"teleports"=>array(),
	"transfers"=>array(),
	"ip"=>array(),
	"cells"=>array(),
	"res"=>array(),
	"buyres"=>array()
);
$titles = array(
	"shops"=>"Магазины/заводы",
	"banks"=>"Банки",
	"battles"=>"Бои",
	"chars"=>"Персонажи",
	"mail"=>"Почта и подарки",
	"plants"=>"Общественные заводы, фабрики",
	"locations"=>"Локации, без категории",
	"teleports"=>"Телепорты и госпитали",
	"cells"=>"Ячейка(ки)",
	"res"=>"Заводы, лаборатории",
	"buyres"=>"Скупка ресурсов через Магазин",
	"transfers"=>"Переводы",
	"ip"=>"ip",
	"unparsed_log"=>"Необработанный лог"
);
function _var_dump( $var ) {
	echo "<pre>";
	var_dump( $var );
	echo "</pre>";
}

function parse_item( $item, &$arr ){
//	echo "ITEM: $item<br />";
	preg_match( "/([^([]+)(\[\d+\])?(\([^)]+\))?/", $item, $matches );
	if (isset( $matches[3])&&$matches[3]!="") {
		$builtins = substr($matches[3],1,strlen($matches[3])-2);
		$builtins = split( ",", $builtins );
	}
	/**
	 * insert main item
	 */
	if ( isset( $matches[2])&&$matches[2]!="") {
		$count = 0+substr($matches[2],1,strlen($matches[2])-2);
		$arr[trim($matches[1])] += $count;
	} else
		$arr[trim($matches[1])]++;
	/**
	 * insert built-ins
	 */
	 if( isset( $builtins))
		 foreach($builtins as $var) {
		 	preg_match("/([^[]+)(\[\d+\])?/", $var, $matches);
		 	if ( isset( $matches[2]) ) {
				$count = 0+substr($matches[2],1,strlen($matches[2])-2);
		 		$arr[trim($matches[1])]+= $count;
		 	} else
		 		$arr[trim($matches[1])]++;
		 }
}
function parse_items( $items ) {
	$result = array();
//War knife M2, Sniper ammo 7.62S mm [379],
//BankCell Key #216335, BankCell Key #272192, BankCell Key #272640, BankCell
//Key #89027, BankCell Key #399269, ScreenSaver Fish2, BankCell Key #526534,
//BankCell Key #462891, Sense diadem(Fire crystal, Life crystal, Life
//crystal, Life crystal), Power boots(Titanium plates), Power
//trousers(Titanium plates), H&K PSG-1 M1(Sniper scope, Sniper ammo 7.62S mm
//[20])
	$item_str = str_replace( "\r", "", $items );
	$item_str = str_replace( "\n", " ", $item_str );
	$cur_item = "";
	$i = 0;
	$inside_curves = false;
	while( $i < strlen( $item_str) ) {
		if ($item_str[$i]=="," && !$inside_curves) {
			// found new items
			// parse it and built-ins
			parse_item($cur_item, $result );
			$cur_item="";
		} else if ( $item_str[$i]=="(" ) {
			$inside_curves = true;
			$cur_item .= $item_str[$i];
		} else if ( $item_str[$i]==")") {
			$inside_curves = false;
			$cur_item .= $item_str[$i];
		} else
			$cur_item .= $item_str[$i];
		$i++;
	}
	parse_item( $cur_item, $result);
	return $result;
}
function balance_add( &$arr_main, $arr ){
	foreach( $arr as $key=>$val)
		$arr_main[$key]+=$val;
}
function balance_subtract( &$arr_main, $arr ){
	foreach( $arr as $key=>$val)
		$arr_main[$key]-=$val;
}
function shop_buy( $matches ) {
	global $balance;
	if ( DEBUG)
		_var_dump( "SHOP_BUY: ".$matches[0] );
// \d\d:\d\d В магазине '([^']+') приобретено (.+) на сумму (\d+)[^\d]+(\d+)[^\n]*/s
	balance_add( $balance["shops"][$matches[1]], parse_items($matches[2]));
	$balance["shops"][$matches[1]]["Coins"]-=$matches[3];
	$balance["shops"][$matches[1]]["coins_left"]=$matches[4];
	return "";
}
function shop_sell( $matches ) {
	global $balance;
	if ( DEBUG)
		_var_dump( "SHOP_SELL: ".$matches[0] );
// "/\d\d:\d\d Продано ([^\n]+) в '([^']+)', получено (\d+) мнт.[^\n]*/s"
	balance_subtract( $balance["shops"][$matches[2]], parse_items($matches[1]));
	$balance["shops"][$matches[2]]["Coins"]+=$matches[3];
	return "";
}

function bank_shop_get_give( $matches ) {
	if ( strpos( strtolower( $matches[3] ), "bank" )!==false )
		bank_get_give( $matches );
	else
		shop_get_give( $matches );
}
function shop_get_give( $matches ) {
	global $balance;
	if ( DEBUG)
		_var_dump( "SHOP_GET_GIVE: ".$matches[0] );
//"/\d\d:\d\d (Получены|Отданы) предметы: ([^']+) в здании '([^']+)'[^\n]*/s"
	if ($matches[1]=="Получены")
		balance_add( $balance["shops"][$matches[3]], parse_items($matches[2]) );
	else
		balance_subtract( $balance["shops"][$matches[3]], parse_items($matches[2]) );
	return "";
}
function bank_get_give( $matches ) {
	global $balance;
	if ( DEBUG)
		_var_dump( "BANK_GET_GIVE: ".$matches[0] );
//"/\d\d:\d\d (Получены|Отданы) предметы: ([^']+) в здании '([^']+)'[^\n]*/s"
	if ($matches[1]=="Получены")
		balance_add( $balance["banks"][$matches[3]], parse_items($matches[2]) );
	else
		balance_subtract( $balance["banks"][$matches[3]], parse_items($matches[2]) );
	return "";
}
function bank_shop_get ($matches) {
	global $balance;
	if ( DEBUG)
		_var_dump( "BANK_SHOP_GET: ".$matches[0] );
//"/\d\d:\d\d Забрал ([^']+) из '([^']+)'. В рюкзаке стало (\d+) мнт.[^\n]*/s"
	if ( strpos( strtolower( $matches[2] ), "bank" )!==false ) {
		balance_add( $balance["banks"][$matches[2]], parse_items($matches[1]) );
		$balance["banks"][$matches[2]]["coins_left"] = $matches[3];
	} else {
		balance_add( $balance["shops"][$matches[2]], parse_items($matches[1]) );
		$balance["shops"][$matches[2]]["coins_left"] = $matches[3];
	}
	return "";
}
function battle_loose( $matches ) {
	global $balance;
	if ( DEBUG)
		_var_dump( "BATTLE_LOOSE: ".$matches[0] );
//"/\d\d:\d\d Потеряны трофейные предметы: ([^']+) в бою '(\d+)'[^\n]*/s"
	balance_subtract( $balance["battles"][$matches[2]], parse_items($matches[1]) );
	return "";
}
function battle_get( $matches ) {
	global $balance;
	if ( DEBUG)
		_var_dump( "BATTLE_GET: ".$matches[0] );
//"/\d\d:\d\d Подобрал: ([^']+) в бою '(\d+)'[^\n]*/s"
	balance_add( $balance["battles"][$matches[2]], parse_items($matches[1]) );
	return "";
}
function location_loose( $matches ) {
	global $balance;
//"/\d\d:\d\d Выброшено: ([^\n]+)/s"
//"/\d\d:\d\d Использовано ([^\n]+) модулем [^\n]*/s"
	if ( DEBUG)
		_var_dump( "LOCATION_LOOSE: ".$matches[0] );
	balance_subtract( $balance["locations"], parse_items($matches[1]) );
	return "";
}
function char_get( $matches ) {
	global $balance;
	if ( DEBUG)
		_var_dump( "CHAR_GET: ".$matches[0] );
//"/\d\d:\d\d Получено: ([^']+) от '([^']+)'[^\n]*/s"
	balance_add( $balance["chars"][$matches[2]], parse_items($matches[1]) );
	return "";
}
function char_give( $matches ) {
	global $balance;
	if ( DEBUG)
		_var_dump( "CHAR_GIVE: ".$matches[0] );
//"/\d\d:\d\d Получено: ([^']+) от '([^']+)'[^\n]*/s"
	balance_subtract( $balance["chars"][$matches[2]], parse_items($matches[1]) );
	return "";
}
function mail_send( $matches ) {
	global $balance;
	if ( DEBUG)
		_var_dump( "MAIL_SEND: ".$matches[0] );
//"/\d\d:\d\d Оплатил ([^']+) в '([^']+)' за отправку посылки. В рюкзаке стало (\d+) мнт.[^\n]*/s"
	balance_subtract( $balance["mail"][$matches[2]], parse_items($matches[1]) );
	$balance["mail"][$matches[2]]["coins_left"]=$matches[3];
	return "";
}
function teleport_hospital( $matches ) {
	global $balance;
	if ( DEBUG)
		_var_dump( "TELEPORT_HOSPITAL: ".$matches[0] );
//"/\d\d:\d\d Оплатил ([^']+) в '([^']+)' за телепортацию/лечение. В рюкзаке осталось (\d+) мнт.[^\n]*/s"
	balance_subtract( $balance["teleports"][$matches[2]], parse_items($matches[1]) );
	$balance["teleports"][$matches[2]]["coins_left"]=$matches[3];
	return "";
}
function mail_to( $matches ){
	global $balance;
	if ( DEBUG)
		_var_dump( "MAIL_TO: ".$matches[0] );
//"/\d\d:\d\d Переслал: ([^']+) персонажу '([^']+)'[^\n]*/s"
	balance_subtract( $balance["mail"][$matches[2]], parse_items($matches[1]) );
	return "";
}
function mail_from( $matches ){
	global $balance;
	if ( DEBUG)
		_var_dump( "MAIL_FROM: ".$matches[0] );
//"/\d\d:\d\d Почтой получены предметы: ([^']+) от персонажа '([^']+)'[^\n]*/s"
	balance_add( $balance["mail"][$matches[2]], parse_items($matches[1]) );
	return "";
}
function plant_give($matches){
	global $balance;
	if ( DEBUG)
		_var_dump( "PLANT_GIVE: ".$matches[0] );
	//"/\d\d:\d\d Сдано: ([^']+) на общественный завод '([^']+)'[^\n]*/s"
	balance_subtract( $balance["plants"][$matches[2]], parse_items($matches[1]));
	return "";
}
function cell_put ($matches) {
	global $balance;
	if ( DEBUG)
		_var_dump( "CELL_PUT: ".$matches[0] );
//	"/\d\d:\d\d Персонаж ([^']+) положил в ячейку предметы ([^\n]+)/"
	balance_add( $balance["cells"][$matches[1]], parse_items($matches[2]));
}
function cell_get ($matches) {
	global $balance;
	if ( DEBUG)
		_var_dump( "CELL_GET: ".$matches[0] );
///\d\d:\d\d Персонаж '([^']+)' забрал из ячейки предметы: ([^\n]+)/
	balance_subtract( $balance["cells"][$matches[1]], parse_items($matches[2]));
}

function res_put ($matches) {
	global $balance;
	if ( DEBUG)
		_var_dump( "RES_PUT: ".$matches[0] );
//	"/\d\d:\d\d Персонаж ([^']+) выложил на склад ([^\n]+)/"
	$a = array($matches[1] => $matches[3]);
	balance_add( $balance["res"]['Сдано ресурсов:'], $a);
}

function res_buy ($matches) {
	global $balance;
	if ( DEBUG)
		_var_dump( "RES_BUY: ".$matches[0] );
//	"/\d\d:\d\d Персонаж ([^']+) продал в магазин ресурс ([^\n]+)/"
	balance_add( $balance["buyres"][$matches[1]], parse_items($matches[2]));
}

function transfer($matches){
	global $balance;
	if (DEBUG)
		_var_dump("TRANSFER: ".$matches[0]);
//"/\d\d:\d\d Персонаж '([^']+)' перевел на счет (\d+) Медные монеты на сумму: (\d+), сумма на счету (\d+)[^\n]+/"
	$balance["transfers"][$matches[2]][$matches[1]]+=$matches[3];
	$balance["transfers"][$matches[2]]["account_balance"]=$matches[4];
}
function ip($matches){
	global $balance;
	if (DEBUG)
		_var_dump("IP: ".$matches[0]);
//var_dump($matches[1]);
	$balance["ip"][$matches[1]]=$matches[1];

}

function square_to_curly($matches) {
//	echo $matches[0];
	return str_replace(array("[","]"),array("{","}"), $matches[0]);
}
function comma_to_column($matches) {
//	echo $matches[0];
	return str_replace(",",";", $matches[0]);
}
function create_div( $div_key ){
	global $balance, $titles;
	echo "<div id='$div_key' style='display:none;'>";
	echo "<h1>{$titles[$div_key]}</h1>";
	
	if ( $div_key != "locations" && $div_key !="ip" ) {

		foreach( $balance[$div_key] as $key=>$value ) {
			
			echo "<b>$key</b><br />";
			
			$total = 0;

			foreach( $value as $item=>$amount) {
			
				if(!$amount)
					continue;
				
				if ( $item == "coins_left" ) {
					//echo "Осталось монет: $amount<br>";
					continue;
				}
				
				if ( $item == "account_balance" ) {
					//echo "Сумма на счету: $amount<br>";
					continue;
				}
				
				if ( $amount>0 ) {
					echo "<font color='green'><b>+</b>$item [$amount]</font><br />";
				} else {
					echo "<font color='red'><b>-</b>$item [$amount]</font><br />";
				}

				$total += $amount;

			}

			if ($div_key == "buyres") {
				echo "<font color='blue'><b>Всего куплено у [$key]: </b>$total ресурсов.</font><br />";
			}

			echo "<hr />";
		}

	} else if ($div_key=="ip") {

			foreach( $balance[$div_key] as $ip_str) {
					echo "$ip_str<br />";
			}

	} else {

			foreach( $balance[$div_key] as $item=>$amount) {
				if(!$amount)
					continue;
				if ( $item == "coins_left" ) {
					//echo "Осталось монет: $amount";
					continue;
				}
				if ( $amount>0 )
					echo "<font color='green'><b>+</b>$item [$amount]</font><br />";
				else
					echo "<font color='red'><b>-</b>$item [$amount]</font><br />";
			}

	}

	echo "</div>";
}
$got_items = "Получены предметы:";
$left_items = "Отданы предметы:";
$took_items = "Забрал";
$trough_items = "Выброшено:";
$picked_items = "Подобрал:";
$payed_items = "Оплатил";
$wrapped_items = "Упаковано:";
$used_items = "Использовано";
$minus = array( "Выброшено", "Забрал", "Использовано", "Оплатил", "Отданы");
$plus = array( "Подобрал", "Получены", "Получено", "Потеряны", "Продано", "Сломалось");
$action = @$_REQUEST['action'];

if (isset($action) && $action=='upload')
{
	$log = file_get_contents( $_FILES["File"]["tmp_name"]);
}
else if (isset($action) && $action=='text')
{
	$filename = @$_POST['hand_input'];
	if (get_magic_quotes_gpc())
		$filename = stripslashes($filename);
	$log = $filename;
}

$log = strip_tags($log);

if ($log) {
	/*$log = str_replace( ",\r", ",", $log );
	$log = str_replace( ",\n", ", ", $log );
	$log = str_replace( "\n ", " ", $log );
	$log = str_replace( "\r ", " ", $log );
	$log = str_replace( " \n", " ", $log );
	$log = str_replace( " \r", " ", $log );
	$log = str_replace( " \n", " ", $log );
	$log = str_replace( " \r", " ", $log );*/
	$log = str_replace( "  ", " ", $log );
	$log = str_replace( "  ", " ", $log );
	$log = str_replace( "\r", "", $log );
	$log = str_replace( "\n", "", $log );
	$log = preg_replace("/(\d\d:\d\d)/", "\r\n\$1", $log);
	$log = preg_replace( "/~[\d.]+~/", "", $log );
	$log = preg_replace("/BankCell Key \(copy\)/","BankCell Key copy",$log);
	$log = preg_replace_callback("/Item design\(([^)\n]+)\)/", "comma_to_column", $log);
	$log = preg_replace("/Item design\(([^)\n,]+)\)/","Item design: \$1", $log );
	$log = preg_replace_callback("/Item design: [^\n\r,]+/", "square_to_curly", $log);
	$log = preg_replace_callback("/Resource ticket\(([^)\n]+)\)/", "comma_to_column", $log);
	$log = preg_replace("/Resource ticket\(([^)\n]+)\)/","Resource ticket: \$1", $log );
	$log = preg_replace_callback("/Resource ticket: [^\n\r]+/", "square_to_curly", $log);

////операции с магазином
//00:30 В магазине 'The Wall Street Shop' приобретено H&K PSG-1 на сумму 590
//мнт. В рюкзаке осталось 410 мнт.
//00:18 Отданы предметы: Sniper ammo 7.62S mm [1] в здании 'MEGA'
//00:19 Отданы предметы: Coins[1] в здании 'MEGA'
//00:19 Получены предметы: Sniper ammo 7.62S mm [1] в здании 'MEGA'
//01:07 Забрал Coins[10] из 'City Bank of New Moscow[#84937]'. В рюкзаке стало 10 мнт.
//06:19 Продано Organic[2] в 'PAMCTOP', получено 5 мнт.
	$log = preg_replace_callback( "/\d\d:\d\d В магазине '([^']+)' приобретено ([^\n]+) на сумму (\d+)[^\d]+(\d+)[^\n]*/", "shop_buy", $log );
	$log = preg_replace_callback( "/\d\d:\d\d (Получены|Отданы) предметы: (.+) в здании '([^']+)'[^\n]*/", "bank_shop_get_give", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Забрал ([^']+) из '([^']+)'. В рюкзаке стало (\d+) мнт\.[^\n]*/", "bank_shop_get", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Продано ([^\n]+) в '([^']+)', получено (\d+) мнт\.[^\n]*/", "shop_sell", $log );
//в бою, на локации
//23:06 Выброшено: Sense diadem(Fire crystal, Life crystal, Life crystal,
//Life crystal)
//01:33 Сломалось: H&K PSG-1 в бою '60907008000'
//01:33 Подобрал: Metals[1] в бою '60907008000'
//01:01 Выброшено: Silicon[21] в бою '59958049534'
//01:11 Потеряны трофейные предметы: Venom[13] в бою '59959188480'
//03:56 Использовано Energy Module[1] модулем Electronic spyglass~12228609699.1~
	$log = preg_replace_callback( "/\d\d:\d\d Потеряны трофейные предметы: ([^']+) в бою '(\d+)'[^\n]*/", "battle_loose", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Выброшено: ([^'\n]+) в бою '(\d+)'[^\n]*/", "battle_loose", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Истек срок действия предмета: ([^\n]+)/", "location_loose", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Сломалось: ([^'\n]+) в бою '(\d+)'[^\n]*/", "battle_loose", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Выброшено: ([^\n]+)/", "location_loose", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Подобрал: ([^']+) в бою '(\d+)'[^\n]*/", "battle_get", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Использовано ([^\n]+) модулем [^\n]*/", "location_loose", $log );
//обмен с персонажем
//03:14 Получено: HiTech helm M2 от 'Heavy Gunner'
//12:28 Передал: H&K PSG-1 M3(Barrel jack, Laser designator, Sniper ammo
//7.62S mm  M3[20]) к 'Armored Fist'
//03:14 Передал в долг: HiTech helm M2 к 'Heavy Gunner'
//00:16 Получено в долг: HiTech armguard M3, HiTech armguard M3 от '000 Alliance OA'
//12:15 Передал: BankCell Key (copy) #45567 к 'Smth'
	$log = preg_replace_callback( "/\d\d:\d\d Получено: ([^']+) от '([^']+)'[^\n]*/", "char_get", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Получено в долг: ([^']+) от '([^']+)'[^\n]*/", "char_get", $log );
	//$log = preg_replace_callback( "/\d\d:\d\d Передал: (BankCell Key (\(copy\))? #\d+) к '([^']+)'[^\n]*/", "cell_key_give", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Передал: ([^']+) к '([^']+)'[^\n]*/", "char_give", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Передал в долг: ([^\n]+) к '([^']+)'[^\n]*/", "char_give", $log );
//операции с почтой
//00:36 Оплатил Coins[1] в 'New Moscow Post Office' за отправку посылки. В
//рюкзаке осталось 9 мнт.
//00:36 Переслал: Item design(Power armguard, Power boots, Power trousers,
//Power vest, Power helm, Power armguard) персонажу 'Heavy Gunner'
//23:50 Почтой получены предметы: SVD sniper rifle SE(Barrel jack, Laser
//designator, Sniper ammo 7.62S mm  M3[10]) от персонажа 'Simone'
//21:43 Упаковано: Raiders arm, Raiders arm, Raiders boots, Raiders vest, Raiders trousers в подарок персонажу DISKO*
//23:52 Оплатил Coins[120] в 'New Moscow Greenhouse' за доставку сувенира. В рюкзаке осталось 50 мнт.
//23:52 Приобретено 'Teddy-in-Love' для персонажа 'Вася' с доставкой через 0 часов
	$log = preg_replace_callback( "/\d\d:\d\d Оплатил ([^']+) в '([^']+)' за отправку посылки. В рюкзаке стало (\d+) мнт\.[^\n]*/", "mail_send", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Оплатил ([^']+) в '([^']+)' за отправку посылки. В рюкзаке осталось (\d+) мнт\.[^\n]*/", "mail_send", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Оплатил ([^']+) в '([^']+)' за оформление подарка. В рюкзаке осталось (\d+) мнт\.[^\n]*/", "mail_send", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Оплатил ([^']+) в '([^']+)' за доставку сувенира. В рюкзаке осталось (\d+) мнт\.[^\n]*/", "mail_send", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Переслал: ([^']+) персонажу '([^']+)'[^\n]*/", "mail_to", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Приобретено '([^']+)' для персонажа '([^']+)'[^\n]*/", "mail_to", $log );
	$log = preg_replace_callback( "/\d\d:\d\d\ Почтой получены предметы: (.+) от персонажа '([^']+)'[^\n]*/", "mail_from", $log );
	$log = preg_replace_callback( "/\d\d:\d\d\ Упаковано: (.+) в подарок персонажу ([^\n]+)/", "mail_to", $log );

//Общественный завод
//22:41 Сдано Polymers[8] на общественный завод 'Public factory'
//07:32 Использовано Metals[16], Precious metals[1], Polymers[13],
//Silicon[25], Radioactive materials[5], Gems[1] для изготовления Electronic
//spyglass //ну это наверное не понадобится.
	$log = preg_replace_callback( "/\d\d:\d\d Сдано ([^']+) на общественный завод '([^']+)'[^\n]*/", "plant_give", $log );
//Телепорты и госпитали
//01:33 Оплатил Coins[4] в 'New Moscow Outskirts' за телепортацию. В рюкзаке осталось 19 мнт.
//21:14 Оплатил Coins[3] в 'Oasis Hospital' за лечение. В рюкзаке осталось 12 мнт.
	$log = preg_replace_callback( "/\d\d:\d\d Оплатил ([^']+) в '([^']+)' за телепортацию. В рюкзаке осталось (\d+) мнт\.[^\n]*/", "teleport_hospital", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Оплатил ([^']+) в '([^']+)' за лечение. В рюкзаке осталось (\d+) мнт\.[^\n]*/", "teleport_hospital", $log );
//ячейки
//01:25 Персонаж 'Вася' забрал из ячейки предметы: Organic[220]
//01:25 Персонаж 'Вася' забрал из ячейки предметы: Fine Gekko skin
	$log = preg_replace_callback( "/\d\d:\d\d Персонаж '([^']+)' положил в ячейку предметы: ([^\n]+)/", "cell_put", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Персонаж '([^']+)' положил в ячейку монеты ([^\n]+), сумма на счету: \d+/", "cell_put", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Персонаж '([^']+)' забрал из ячейки предметы: ([^\n]+)/", "cell_get", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Персонаж '([^']+)' забрал из ячейки монеты ([^\n]+), сумма на счету: \d+/", "cell_get", $log );
	//$log = preg_replace_callback( "/\d\d:\d\d Персонаж '([^']+)' выложил на склад ([^\n]+)/", "res_put", $log );
	$log = preg_replace_callback( "/\d\d:\d\d Персонаж '([^']+)' выложил на склад([^([]+)\[(\d+)\]?/", "res_put", $log );
	
//переводы
//12:16 Персонаж 'Елики болики' перевел на счет 330491 Медные монеты на
//сумму: 1, сумма на счету: 280
	$log = preg_replace_callback( "/\d\d:\d\d Персонаж '([^']+)' перевел на счет (\d+) Медные монеты на сумму: (\d+), сумма на счету: (\d+)[^\n]*/", "transfer", $log );
// ip
//19:31 Вход в игру с IP=81.10.38.191 192.168.0.73, клиент v3.39.2 (1.39)
//19:35 Вход в игру с IP=81.10.38.190 192.168.0.73, клиент v3.39.2 (1.39)

	$log = preg_replace_callback( "/\d\d:\d\d Вход в игру с IP=([\d\. ]+), клиент [^\n\r]+/", "ip", $log );

// Скупку ресурсов через Магазин
// 15.11.14 20:24 Персонаж 'Вася' продал в магазин ресурс Precious metals[796] по цене 676 мнт.
	
	$log = preg_replace_callback( "/\d\d:\d\d Персонаж '([^']+)' продал в магазин ресурс ([^\n]+)/", "res_buy", $log );

/**
 * Fill out correpsponding divs
 */
 ?>
<table border="0">
<tr>
<td class="menu" valign=="top">
<a href="javascript:show('shops');">Магазины/заводы (<?php echo count($balance["shops"]); ?>)</a><br />
<a href="javascript:show('banks');">Банки (<?php echo count($balance["banks"]); ?>)</a><br />
<a href="javascript:show('battles');">Бои (<?php echo count($balance["battles"]); ?>)</a><br />
<a href="javascript:show('chars');">Персонажи (<?php echo count($balance["chars"]); ?>)</a><br />
<a href="javascript:show('mail');">Почта/Подарки (<?php echo count($balance["mail"]); ?>)</a><br />
<a href="javascript:show('plants');">Общественные заводы (<?php echo count($balance["plants"]); ?>)</a><br />
<a href="javascript:show('locations');">Без категории(локи) (<?php echo count($balance["locations"]); ?>)</a><br />
<a href="javascript:show('teleports');">Телепорты/больницы(<?php echo count($balance["teleports"]); ?>)</a><br />
<a href="javascript:show('cells');">Ячейка(ки)(<?php echo count($balance["cells"]); ?>)</a><br />
<a href="javascript:show('res');">Завод, лаборатория(<?php echo count($balance["res"]); ?>)</a><br />
<a href="javascript:show('buyres');">Скупка ресурсов(магазин)(<?php echo count($balance["buyres"]); ?>)</a><br />
<a href="javascript:show('transfers');">Переводы(<?php echo count($balance["transfers"]); ?>)</a><br />
<a href="javascript:show('ip');">ip(<?php echo count($balance["ip"]); ?>)</a><br />
<a href="javascript:show('unparsed_log');">Необработанный лог</a><br />
</td>
<td class="contents">
<?php
 foreach( $balance as $key => $value )
 	create_div( $key );
 echo "<div id='unparsed_log' style='display:none;'>";
 echo "<h1>".strip_tags($titles["unparsed_log"])."</h1>";
 $log = preg_replace("/[\r\n]/", "", $log);
 $log = preg_replace("/(\d\d:\d\d)/", "<br>\$1", $log);
 $log = preg_replace("/(За день набрано опыта)/", "<br>\$1", $log);
 echo "<PRE>$log</PRE>";
 echo "</div>";
} else {
	/**
	 * no log available
	 */
	 echo "Вы забыли указать лог. Вернитесь назад и попробуйте еще раз.";
}

/*$str = "War knife M2, Sniper ammo 7.62S mm [379],
BankCell Key #216335, BankCell Key #272192, BankCell Key #272640, BankCell
Key #89027, BankCell Key #399269, ScreenSaver Fish2, BankCell Key #526534,
BankCell Key #462891, Sense diadem(Fire crystal, Life crystal, Life
crystal, Life crystal), Power boots(Titanium plates), Power
trousers(Titanium plates), H&K PSG-1 M1(Sniper scope, Sniper ammo 7.62S mm
[20])";
_var_dump( parse_items($str) );*/
?>

</td></tr></table>
<?php if (DEBUG) {
	_var_dump( $balance );
	echo "LOG UNPARSED: $log";
}?>
<div style="clear:left; text-align:center;"><a href='index.html'target='_self'>назад</a></div>
                <script language="JavaScript" type="text/javascript"><!--

                        document.write('<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" '+

                        'codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="88" height="33">'+

//                       '<param name="movie" value="http://www.timezero.ru/tzcnt.swf?ref='+escape(document.location)+'" />'+
//                       '<param name="allowScriptAccess" value="always" /><embed src="http://www.timezero.ru/tzcnt.swf?ref='+escape(document.location)+
                        '<param name="movie" value="http://www.timezero.ru/tzcnt.swf?ref=http%3A//www.tzpolice.ru" />'+
                        '<param name="allowScriptAccess" value="always" /><embed src="http://www.timezero.ru/tzcnt.swf?ref=http%3A//www.tzpolice.ru'+

                        '" allowScriptAccess="always" width="88" height="33" type="application/x-shockwave-flash" '+

                        'pluginspage="http://www.macromedia.com/go/getflashplayer" /></object>');

                //--></script>
                <br>

                <!--Rating@Mail.ru COUNTER--><script language="JavaScript" type="text/javascript"><!--

                        d=document;var a='';a+=';r='+escape(d.referrer)

                        js=10//--></script><script language="JavaScript1.1" type="text/javascript"><!--

                        a+=';j='+navigator.javaEnabled()

                        js=11//--></script><script language="JavaScript1.2" type="text/javascript"><!--

                        s=screen;a+=';s='+s.width+'*'+s.height

                        a+=';d='+(s.colorDepth?s.colorDepth:s.pixelDepth)

                        js=12//--></script><script language="JavaScript1.3" type="text/javascript"><!--

                        js=13//--></script><script language="JavaScript" type="text/javascript"><!--

                        d.write('<a href="http://top.mail.ru/jump?from=761975"'+

                        ' target=_blank><img src="http://top.list.ru/counter'+

                        '?id=761975;t=210;js='+js+a+';rand='+Math.random()+

                        '" alt="Рейтинг@Mail.ru"'+' border=0 height=31 width=88/><\/a>')

                        if(11<js)d.write('<'+'!-- ')//--></script><noscript><a

                        target=_top href="http://top.mail.ru/jump?from=761975"><img

                        src="http://top.list.ru/counter?js=na;id=761975;t=210"

                        border=0 height=31 width=88

                        alt="Рейтинг@Mail.ru"/></a></noscript><script language="JavaScript" type="text/javascript"><!--

                        if(11<js)d.write('--'+'>')//-->
                        </script>

                <!--/COUNTER-->
</body>
</html>