<?

error_reporting(0);

include ("_modules/mysql.php");

//$SQL="SELECT * FROM site_users WHERE user_name='".str_replace("'",';',$_COOKIE['CUser'])."' AND user_pass='".str_replace("'",';',$_COOKIE['CPass'])."'";

$SQL="SELECT su.clan AS `clan` FROM site_users AS su INNER JOIN site_users_auth AS sua ON(su.id = sua.user_id) WHERE su.user_name='".str_replace("'",';',$_COOKIE['CUser'])."' AND sua.user_hash='".str_replace("'",';',$_COOKIE['CPass'])."'";


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

if($logged_in==1 && ($clan=='police' || $clan=='Military Police' || $clan=='Police Academy' || $clan=='Financial Academy' || $clan=='Tribunal'))

{

	$fn = $_GET['file'];

	$fn = stripslashes($fn);

	$fn = "restricted_files/".$fn;

	$filename = basename($fn);

	if(!$filename){

	  die( "Не указан файл." );

	}

	if(!is_file($fn)){

	  die( "Указанный файл не существует." );

	}

	header("Content-type: application/x-download");

	header("Content-Disposition: attachment; filename=" . $filename . ";");

	header("Accept-Ranges: bytes");

	header("Content-Length: " . filesize($fn) );

	readfile($fn);

}

else

{

    $mess = "Авторизуйтесь на сайте Полиции ТО (<a href='http://".$_SERVER['HTTP_HOST']."' target='_blank'>".$_SERVER['HTTP_HOST']."</a>) и повторите попытку скачивания (обновите страницу). <br><br><b>Внимание</b> - данные файлы доступны для скачивания <b>только</b> членам кланов <b>police</b>, <b>Military Police</b>, <b>Police Academy</b>, <b>Financial Academy</b> и <b>Tribunal</b>.";

    die ($mess);

}

?>