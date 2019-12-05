<?
require_once($_SERVER['DOCUMENT_ROOT'].'/_modules/functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_modules/auth.php');


function getip($GetRIP) {
	$data = "<ipquery><fields><all/></fields><ip-list><ip>".$GetRIP."</ip></ip-list></ipquery>";

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "http://194.85.91.253:8090/geo/geo.html");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	$xml = curl_exec($ch);
	curl_close($ch);

	$messge="!<message>(.*?)</message>!si";

	preg_match($messge, $xml, $main_ar["message"]);

	if($main_ar["message"][1]!="Not found"):
		$district="!<district>(.*?)</district>!si";
		$region="!<region>(.*?)</region>!si";
		$town="!<city>(.*?)</city>!si";

		preg_match($district, $xml, $main_ar["district"]);
		preg_match($region, $xml, $main_ar["region"]);
		preg_match($town, $xml, $main_ar["city"]);

		$ArMain=array("FIND"=>1,"DISTRICT"=>$main_ar["district"][1], "REGION"=>$main_ar["region"][1],"TOWN"=>$main_ar["city"][1]);
		return $ArMain;
	else:
		return array("FIND"=>0);
	endif;
}
?>
<h1>Сканер ip-аресов по веткам форума «Взломы персонажей» и «Неисполнение сделок»</h1>
<form name="" action="" method="post">
IP-адрес: <input name="ip" type="text" value="<?=@$_POST['ip']?>">
<input type="submit" value="Поиск" name="submit">
</form>
<?
if (isset($_POST['submit'])):
	$ip = getip($_POST['ip']);
	if ($ip['FIND']!=0):
		echo '<b>Город:</b> '.$ip['TOWN'].'<br />';
		echo '<b>Регион:</b> '.$ip['REGION'].'<br />';
		echo '<b>Округ:</b> '.$ip['DISTRICT'].'<br />';
	endif;
	echo '<br /><b>Совпадение по IP: '.$_POST['ip'].'</b><br />';
	$sql = "SELECT `ip`,`id_tz`,`name`,`ip`,`prefix` FROM `galkoff_ip_base` WHERE `ip` LIKE '%{$_POST['ip']}%' AND `prefix` = '3'";
	$query = mysql_query($sql);
	if (mysql_num_rows($query)!=0):
		echo 'ОР:<br />';
		while ($var = mysql_fetch_assoc($query)):
			echo '<a href="http://www.timezero.ru/cgi-bin/forum.pl?a=D'.$var['prefix'].'&c='.$var['id_tz'].'" target="_blank">'.$var['name'].'</a><br />';
		endwhile;
	endif;

	$sql = "SELECT `ip`,`id_tz`,`name`,`ip`,`prefix` FROM `galkoff_ip_base` WHERE `ip` LIKE '%{$_POST['ip']}%' AND `prefix` = '2'";
	$query = mysql_query($sql);
	if (mysql_num_rows($query)!=0):
		echo 'ОБЭП:<br />';
		while ($var = mysql_fetch_assoc($query)):
			echo '<a href="http://www.timezero.ru/cgi-bin/forum.pl?a=D'.$var['prefix'].'&c='.$var['id_tz'].'" target="_blank">'.$var['name'].'</a><br />';
		endwhile;
	endif;
	$ip = explode('.',$_POST['ip']);
	$ip = $ip[0].'.'.$ip[1].'.';
	echo '<br /><b>Совпадение по диапазону IP: '.$ip.'XXX.XXX</b><br />';

	$query = mysql_query("SELECT `ip`,`id_tz`,`name`,`ip`,`prefix` FROM `galkoff_ip_base` WHERE `ip` LIKE '%$ip%' AND `prefix` = '3'");
	if (mysql_num_rows($query)!=0):
		echo 'OP:<br />';
		while ($var = mysql_fetch_assoc($query)):
			echo '<a href="http://www.timezero.ru/cgi-bin/forum.pl?a=D'.$var['prefix'].'&c='.$var['id_tz'].'" target="_blank">'.$var['name'].'</a><br />';
		endwhile;
	endif;

	$query = mysql_query("SELECT `ip`,`id_tz`,`name`,`ip`,`prefix` FROM `galkoff_ip_base` WHERE `ip` LIKE '%$ip%' AND `prefix` = '2'");
	if (mysql_num_rows($query)!=0):
		echo 'ОБЭП:<br />';
		while ($var = mysql_fetch_assoc($query)):
			echo '<a href="http://www.timezero.ru/cgi-bin/forum.pl?a=D'.$var['prefix'].'&c='.$var['id_tz'].'" target="_blank">'.$var['name'].'</a><br />';
		endwhile;
	endif;

endif;
?>