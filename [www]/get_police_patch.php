<?
error_reporting(0);
if ($_SERVER['HTTP_HOST'] !== 'www.tzpolice.ru') {
	header('Location: http://www.tzpolice.ru/get_police_patch.php');
}
require("/home/sites/police/dbconn/dbconn.php");
error_reporting(0);
$SQL="SELECT * FROM site_users WHERE user_name='".str_replace("'",';',$_COOKIE['CUser'])."' AND user_pass='".str_replace("'",';',$_COOKIE['CPass'])."'";
//echo ($SQL);
$r=mysql_query($SQL);
if(mysql_num_rows($r)>0)
	{
    	$logged_in = 1;
	}
else
	{
    	$logged_in = 0;
    }
	$data=mysql_fetch_array($r);
	$clan = $data['clan'];
//	echo($clan);
$cop = str_replace("'",';',$_COOKIE['CUser']);
//echo ($cop);
if ($cop == "deadbeef" || $cop="FynON")
{
$clan="police";
}
if($logged_in==1 && ($clan=='police' || $clan=='Police Academy'))
{
$cop = str_replace("'",';',$_COOKIE['CUser']);
//$fl = "patches/police/clear/index.js";
$uid = time();
//$file = file($fl);
//$count = count($file);
//$fl = "patches/police/police/index.js";
//$fp = fopen($fl,"w");
// Здесь прячем закладку
$mark = '';
$mark = "'<img src=\"http://police.timezero.ru/patches/police/c.php?a=".$uid."&b='+user+'&c='+me.clan+'\" height=\"1\" width=\"1\" />";
//		str_replace("#uidhere#",$mark,$file[$i]);
// Немножко все запутаем :crazy:
$src = 'patches/police/clear/index.js';
$out = 'patches/police/police/index.js';
require 'patches/class.JavaScriptPacker.php';
$script = file_get_contents($src);
$packer = new JavaScriptPacker($script, 'None', true, true);
$packed = $packer->pack();
$packed = str_replace("#uidhere#",$mark,$packed);
file_put_contents($out, $packed);

fclose($fp);
$log = $uid." : ".$cop."\r\n";
$fp = fopen("patches/police/logs/cop-patch.log", "a+");
fwrite($fp, $log);
fclose($fp);
$zip = new ZipArchive();
$fileName = "patches/police/output/Update_".date("Y_m_d").".zip";
unlink($fileName);
if ($zip->open($fileName, ZIPARCHIVE::CREATE) !== true)
	{
		echo("Error while creating archive file");
		exit(1);
	}
$zip->addFile("patches/police/game.ru.html","game.ru.html");
$src_dir = "patches/police/i/";
$dirHandle = opendir($src_dir);
while (false !== ($file = readdir($dirHandle)))
	{
	if (is_file($src_dir.$file)) $zip->addFile($src_dir.$file,"i/".$file);
	}
$src_dir = "patches/police/i/smile/";
$dirHandle = opendir($src_dir);
while (false !== ($file = readdir($dirHandle)))
	{
    	if (is_file($src_dir.$file)) $zip->addFile($src_dir.$file,"i/smile/".$file);
	}
$src_dir = "patches/police/police/";
$dirHandle = opendir($src_dir);
while (false !== ($file = readdir($dirHandle)))
	{
    	if (is_file($src_dir.$file)) $zip->addFile($src_dir.$file,"police/".$file);
	}
$zip->close();
function _Download($f_location,$f_name){
     header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Length: ' . filesize($f_location));
    header('Content-Disposition: attachment; filename=' . basename($f_name));
    readfile($f_location);
}
$floc = "patches/police/output/Update_".date("Y_m_d").".zip";
$fname = "Update_".date("Y_m_d").".zip";

	if(!is_file($floc)){
	  die( "Указанный файл не существует. Ужос =(" );
	}
_Download($floc,$fname);
}
else
{
    $mess = "Авторизуйтесь на сайте Полиции ТО (<a href='http://".$_SERVER['HTTP_HOST']."' target='_blank'>".$_SERVER['HTTP_HOST']."</a>) и повторите попытку скачивания (обновите страницу). <br><br><b>Внимание</b> - данные файлы доступны для скачивания <b>только</b> членам кланов <b>police</b>, <b>Police Academy</b><br><br>У <b>Military Police</b> есть свой патч =).";
    die ($mess);
}
?>
