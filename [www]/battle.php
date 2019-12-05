<?
$add="";
function GetBattle($ID)
	{
		$remote_file = "http://city1.timezero.ru/getbattle?id=".trim($ID);
		$ch=curl_init ();
		curl_setopt ($ch, CURLOPT_URL, $remote_file);
		$fp=fopen (trim("adamastis/logs/".$ID.".tzb"), "w+");
		curl_setopt ($ch, CURLOPT_FILE, $fp);
		curl_setopt ($ch, CURLOPT_REFERER, 'http://game.timezero.ru/');
		curl_setopt ($ch, CURLOPT_AUTOREFERER, 1);
		curl_exec ($ch);
		curl_close ($ch);
		fclose ($fp);				
	}
if (isset($_REQUEST['id']))
	{
		$log_number=intval($_REQUEST['id']);
	}
else
	{
                $log_number=intval($_SERVER['QUERY_STRING']);
	}
if (!is_file('adamastis/logs/'.$log_number.'.tzb'))
	{
		GetBattle($log_number);
		$add="*";
	}
?>
<html><head><title>Номер лога <?=$log_number?> <?=$add?></title>
<meta http-equiv="Content-type" content="text/html; charset=windows-1251" />
</head>
<!--
Big thanks for help to [mishishe], [Nevy] and [ТемныйЭльф]
Спасибо за неоценимую помощь [mishishe], [Nevy] и [ТемныйЭльф]
//-->
<body leftmargin="0" topmargin="0" marginheight="0" marginwidth="0" bgcolor="#40404A">
<CENTER><embed width="1004" height="400" flashvars="language=ru&amp;battleid=s<?=$log_number?>" quality="high" bgcolor="#333333" id="battle" style="" src="sbtl.swf" type="application/x-shockwave-flash"/></CENTER>
</body></html>
