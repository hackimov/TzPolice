#!/usr/bin/php -q
<?php

// ==== ������ �� ������ ��������� � �����. ============================
// ==== ����� ��� �� ��������� ��� �������������� ������� ������� ======
die('��������� �� ������� ���������� �������.');
// =====================================================================

include("/home/sites/police/dbconn/dbconn2.php");
//error_reporting(E_ALL);
	function send_telegram($nick, $msg){

		$noerror=1;
	//	��� ��������� || ����� ����������
		$message = "\n0xDEADBEEF || ".$nick.' || '.$msg."\n";
	// �������� ������� ��������� - ������ ������ ������� ������ ����
	//	$message .= "Terminal PA || antispam\n";

		$fname = '/home/sites/police/bot_fees/alerts.txt';

		if (file_exists($fname)){
	//		echo "file_exists";
			chmod($fname, 0777);
		}

	// ��������� $filename � ������ "�������� � �����".
		if ($handle = fopen($fname, 'a')) {
		// ���������� � �������� ����.
			if (fwrite($handle, $message) === FALSE) {
//				$text = "�� �������� ���������� ������ � ���� (".$filename.")";
				$noerror=0;
			}
	//		$text = "�������� (".$message.") � ���� (".$filename.")";
			fclose($handle);
		}else{
//			$text = "�� ���� ������� ���� (".$filename.")";
			$noerror=0;
		}

		return $noerror;
	}


unset($_GET['date']);
unset($_GET['need']);

$filename = '/home/sites/police/www/_cron/prize.lock';
if (file_exists($filename)) {
		
	$last = fileatime($filename);
	if(isset($_GET['date'])) {		$d = explode('.',$_GET['date']);
		if(count($d) == 3) {
			$d[2] = ($d[2] > 2000)?$d[2]:$d[2]+2000;
			$last = mktime(0,0,0,$d[1],$d[0],$d[2]);
			echo "Load: ".date("Y-m-d",$now)."<hr>";
		}

	}
}  else {	$handle = fopen($filename,'a+');
	fclose($handle);
	$last = time();
}
echo date("d.m.Y H:i", $last)." ($last)<br>";
$cur = time();
if (($cur - $last) > 85000 || isset($_GET['need']))	{
    	$prize = 10;
        if(isset($_GET['date'])) {	    	$tmp = explode('.',$_GET['date']);
	    	$now = mktime(0,0,1,$tmp[1],$tmp[0],date('Y'));
	    } else {	    	$now = time()-86400;
	    }
	    $today = date("Y-m-d",$now);
		$ratingdate = date("d.m.Y",$now);
		echo "Get rate at: $ratingdate<br>";
	    $query = "SELECT * FROM `prison_rating` WHERE `date` = '".$today."' AND `nick` NOT LIKE 'ZenitSPb' ORDER BY `collected` DESC LIMIT 3";
	    $res = mysql_query($query);
        while ($d = mysql_fetch_array($res)) {
            	$toadd = ceil(($d['collected']*$prize)/100);
            	$query = "UPDATE `prison_chars` SET `collected` = `collected`+".$toadd.", `coll_by_rating` = `coll_by_rating`+".$toadd." WHERE `nick` = '".$d['nick']."' LIMIT 1;";
				$congr = "�����������! �� �������� ����� � �������� �������� �� ����������� ������ ".$ratingdate." ������� �� ��������� ��� �������� ������� � ���������� ".$toadd.". ������ ��� ���������� ������������!";
				send_telegram($d['nick'],$congr);
                echo ($query."\r\n");
                mysql_query($query) or die(mysql_error());
                $prize=5;
    	}
        touch($filename);
        mysql_close($link);
} else {
    echo ("Already done!<br>".date("d.m.Y H:i", $last)."\r\n");
}

?>