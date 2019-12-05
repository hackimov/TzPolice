<?
error_reporting(0);
if ($_SERVER['HTTP_HOST'] !== 'www.tzpolice.ru') {
	header('Location: http://www.tzpolice.ru/get_patch.php');
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
	$usr = $data['user_name'];
//	echo($clan);
$cop = str_replace("'",';',$_COOKIE['CUser']);
//echo ($cop);
//if ($cop == "deadbeef")
//{
//$clan="Military Police";
//}
//if($logged_in==1 && ($clan=='Military Police' || $clan=='police' || $clan=='Police Academy' || $clan=='Tribunal' || $usr=='Deorg'))
if($logged_in==1 && ($clan=='police' || $clan=='Police Academy' || $clan=='Tribunal'))
{
$cop = str_replace("'",';',$_COOKIE['CUser']);
if ($clan == 'Military Police')
	{
		$folder = 'mp';
	}
else
	{
		$folder = 'police';
	}
// Внедряем метку
$fl = "patches/client/log.txt";
$file = file($fl);
$count = count($file);
$uid = '';
foreach($file as $num => $line)
	{
		$line = str_replace('\r\n','',$line);
		$tmp = explode(': ', $line);
		if($tmp[1]==$cop)
			{
				$uid = $tmp[0];
			}
	}
if (strlen($uid) == 0)
	{
		$uid = chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122));//rand(1,999999);
		$add = $uid.': '.$cop.': '.$folder;
		$fp = fopen($fl,'a');
		fwrite($fp,$add."\r\n");
		fclose($fp);
	}
//Закончил
$mark = '###UID:'.$uid.'###'; //###UID:XXXXXXXXXX###
$src = 'patches/client/'.$folder.'/TZPDLite.exe';
$out = 'patches/client/'.$folder.'/out/TZPDLite.exe';
$client = file_get_contents($src);
$client = str_replace('###UID:XXXXXXXXXX###',$mark,$client);
file_put_contents($out, $client);
fclose($fp);
//$log = $uid." : ".$cop."\r\n";
//$fp = fopen("patches/mp/logs/cop-patch.log", "a+");
//fwrite($fp, $log);
//fclose($fp);
$zip = new ZipArchive();
$fileName = 'patches/client/'.$folder.'/out/TZPDLite.zip';
unlink($fileName);
if ($zip->open($fileName, ZIPARCHIVE::CREATE) !== true)
	{
		echo("Error while creating archive file");
		exit(1);
	}
$zip->addFile('patches/client/'.$folder.'/out/TZPDLite.exe',"TZPDLite.exe");
/*
$src_dir = "patches/mp/i/";
$dirHandle = opendir($src_dir);
while (false !== ($file = readdir($dirHandle)))
	{
	if (is_file($src_dir.$file)) $zip->addFile($src_dir.$file,"i/".$file);
	}
$src_dir = "patches/mp/i/smile/";
$dirHandle = opendir($src_dir);
while (false !== ($file = readdir($dirHandle)))
	{
    	if (is_file($src_dir.$file)) $zip->addFile($src_dir.$file,"i/smile/".$file);
	}
$src_dir = "patches/mp/police/";
$dirHandle = opendir($src_dir);
while (false !== ($file = readdir($dirHandle)))
	{
    	if (is_file($src_dir.$file)) $zip->addFile($src_dir.$file,"police/".$file);
	}
*/
$zip->close();
function _Download($f_location,$f_name){
    header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Length: ' . filesize($f_location));
    header('Content-Disposition: attachment; filename=' . basename($f_name));
    readfile($f_location);
}
$floc = $fileName;
$fname = "TZPDLite.zip";

	if(!is_file($floc)){
	  die( "Указанный файл не существует. Ужос =(" );
	}
_Download($floc,$fname);
}
else
{
    $mess = "Авторизуйтесь на сайте Полиции ТО (<a href='http://".$_SERVER['HTTP_HOST']."' target='_blank'>".$_SERVER['HTTP_HOST']."</a>) и повторите попытку скачивания (обновите страницу). <br><br><b>Внимание</b> - данные файлы доступны для скачивания <b>только</b> членам альянса <b>police</b>.";
    die ($mess);
}
?>
