<h1>Добавление логов в базу</h1>

<?php
//error_reporting(E_ALL);
if(abs(AccessLevel) & AccessOPQueue) {
?>
<table width='100%' border='0' cellspacing='3' cellpadding='2'>
<tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Обработка логов</strong> </p></td></tr>
<?

	if ($_POST['text']) {
		$rslt = 0;
		$priority = $_POST['donow'];
		if ($priority > 1) {$priority = 1;}
		if ($priority < 0) {$priority = 0;}
		$lines = explode("\n", $_POST['text']);
		foreach ($lines as $line) {
			$tmp = $line;
			$line = str_replace('сражение', 'бой', $line);
			$line = str_replace('сражении', 'бою', $line);
			$line = str_replace('сражения', 'боя', $line);
//			echo ($line."<br>");
/*
			if (strpos($line, "сражени"))
				{

				}
*/
		$ss = strpos($line, ' бо');
		$sf = strpos($line, '. Получено');
		$log_number = substr($line, $ss+5, $sf-$ss-4);
		$SQL = 'INSERT DELAYED INTO `import_info` SET `InsertTime` = NOW(), `InsertBy`=\''.AuthUserId.'\', `LogTime` = \''.substr($line, 0, 5).':00\', `LogID` = \''.substr($line, $ss+5, $sf-$ss-4).'\', `status` = \'0\', `doitnow` = \''.$priority.'\', `nick` = \''.$neednick.'\'';
		if ($log_number > 109458283777) {
			$ss = 0;
			$sf = 0;
			$ss = strpos($tmp, " сражени");
			$sf = strpos($tmp, ". Получено");

			$SQL = 'INSERT DELAYED INTO `import_info` SET `InsertTime` = NOW(), `InsertBy`=\''.AuthUserId.'\', `LogTime` = \''.substr($tmp, 0, 5).':00\', `LogID` = \''.substr($tmp, $ss+10, $sf-$ss-4).'\', `status` = \'0\', `doitnow` = \''.$priority.'\', `nick` = \''.$neednick.'\'';
		}
//		else
//			{
                if ($sf > 0) {
//                        $SQL = "INSERT DELAYED INTO import_info VALUES ('',NOW(),'".AuthUserId."','".substr($line, 0, 5).":00','".substr($line, $ss+5, $sf-$ss-4)."','0','".$priority."','".$neednick."')";
                        mysql_query($SQL);
//                	}
		}
        }

/*
if($rslt) {echo("<b>ОШИБКА</b> - попробуйте еще раз. Вам повезет =)");}
else {echo("<b>OK</b>");}
*/
}
?>
<tr><td align=center>
<form method="POST" name=F1>
<textarea rows=6 name=text cols=110>
</textarea>
<br>
  <select name="donow" size="1">
    <option value="0" selected>обычная загрузка</option>
    <option value="1">приоритетная загрузка</option>
  </select>
<input type="submit" value="Добавить">
</form>
<?
}
$np=0;
$SQL = 'SELECT * FROM `import_info` WHERE `status`!=\'2\'';
$r = mysql_query($SQL);
if ($_REQUEST['queue'] == '1')
        {
?>
<table width=450>
        <tr align=center bgcolor=#F4ECD4>
                <td><b>Добавлено</b></td>
                <td><b>Добавил</b></td>
                <td><b>Лог</b></td>
                <td><b>Статус</b></td>
        </tr>
<?
while ($d=mysql_fetch_assoc($r)) {
        if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}
        $n = mysql_fetch_assoc(mysql_query('SELECT `user_name` FROM `site_users` WHERE `id`=\''.$d['InsertBy'].'\''));
?>
        <tr>
                <td <?=$bg?>><?=$d['InsertTime']?></td>
                <td <?=$bg?>><?=$n['user_name']?></td>
                <td <?=$bg?>><?=$d['LogTime'].' <b>'.$d['LogID'].'</b>'?></td>
                <td <?=$bg?> align=center><?=$d['status']?></td>
        </tr>
<? } ?></table> <?
	} else {
		echo '<div align="left"><a href="?act=prokachki_queue&queue=1">Просмотреть подробную очередь</a></div><br>';
		$dolist = 0;
		while ($d=mysql_fetch_assoc($r)) {
	//		$n = mysql_fetch_array(mysql_query("SELECT user_name FROM site_users WHERE id='".$d['InsertBy']."'"));
			$from_usr[$n['user_name']][$d['InsertTime']][$d['status']]++;
			$dolist = 1;
		}
		if ($dolist) {
?>
	<table width=450>
        <tr align=center bgcolor=#F4ECD4>
                <td><b>Добавлено</b></td>
                <td><b>Добавил</b></td>
                <td><b>Статус</b></td>
                <td><b>Кол-во</b></td>
        </tr>
<?
			foreach ($from_usr as $usr => $v1) {
				foreach ($v1 as $intime => $v2) {
					foreach ($v2 as $sts => $quan) {
						if($np==1) {$bg=$bgstr[0]; $np=0;} else {$bg=$bgstr[1]; $np=1;}
						echo '<tr><td '.$bg.'>'.$intime.'</td><td '.$bg.'>'.$usr.'</td><td '.$bg.'>'.$logstate[$sts].'</td><td '.$bg.'>'.$quan.'</td></tr>';
					    $aall += $quan;
					}
				}
			}
			echo "
			<tr align=center bgcolor=#F4ECD4>
                <td colspan=4 align=right>$aall</td>
        	</tr>
			</table>";
		}
	}
?>