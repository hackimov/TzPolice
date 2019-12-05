<?
if ($_SERVER['HTTP_HOST'] !== 'www.tzpolice.ru') {
	header('Location: http://www.tzpolice.ru/get_patch_files.php');
}
require("/home/sites/police/dbconn/dbconn.php");
error_reporting(E_ALL);
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
function sub($text, $st1, $st2, $init=0) {
		$offset1=@strpos($text, $st1, $init)+strlen($st1);
		if($offset1==strlen($st1)) return 0;
		else {
			$offset2=strpos($text, $st2, $offset1);
			$res=@substr($text,$offset1,$offset2-$offset1);
			if(!empty($res)) return $res;
			else return 0;
		}
	}
//if($logged_in==1 && ($clan=='Military Police' || $clan=='police' || $clan=='Police Academy' || $clan=='Tribunal'))
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
require ('/home/sites/police/www/upd/filelist.php');
$fileName = 'patches/client/'.$folder.'/out/TZPDLite-files.zip';
unlink($fileName);
$zip = new ZipArchive();
if ($zip->open($fileName, ZIPARCHIVE::CREATE) !== true)
	{
		echo("Error while creating archive file");
		exit(1);
	}
foreach ($files as $fname => $fpath)
	{
		if (strlen($fpath) > 0)
			{
				$zip->addFile('upd/'.$folder.'/'.$fname,$fpath.'\\'.$fname);
			}
		else
			{
				$zip->addFile('upd/'.$folder.'/'.$fname,$fname);
			}
	}
//Закончил
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
$fname = "TZPDLite-files.zip";

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
