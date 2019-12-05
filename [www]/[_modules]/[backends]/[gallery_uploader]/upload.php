<?php
	error_reporting(E_ALL);
	error_reporting(0);
	require("/home/sites/police/dbconn/dbconn.php");
	error_reporting(E_ALL);
	error_reporting(0);
	$uname = iconv('UTF8','cp1251',$_REQUEST['uname']);
	$upass = $_REQUEST['upass'];
	$SQL="SELECT a.* FROM site_users AS a INNER JOIN site_users_auth AS b ON(a.id = b.user_id) WHERE user_name='".$uname."' AND user_hash='".$upass."'";
	$result = mysql_query($SQL);
	if (mysql_num_rows($result) > 0)
		{
			$user=mysql_fetch_array($result);
		}
//print_r($user);
	require('/home/sites/police/www/_modules/functions.php');
//	error_reporting(E_ALL);
	error_reporting(0);
	$foto_newdir = '/home/sites/police/www/i/newfotos';
	$foto_thumbs = '/home/sites/police/www/i/newfotos/thumbs';
	function make_seed() {
		list($usec, $sec) = explode(' ', microtime());
		return (float) $sec + ((float) $usec * 100000);
	}
	if($user['user_name'] != '') {
//		if ($_REQUEST['make_send']) {
			if (!$_FILES['Filedata']['tmp_name']) { echo 'Error 001: file undefined!'; }
			else {
				if(!file_exists($_FILES['Filedata']['tmp_name'])) { echo 'Error 002: file not exist!'; }
				else {
					$f_name = htmlspecialchars(iconv("UTF-8", "cp1251", $_REQUEST['f_name']));
					$f_city = htmlspecialchars(iconv("UTF-8", "cp1251", $_REQUEST['city']));
					$f_age = htmlspecialchars(iconv("UTF-8", "cp1251", $_REQUEST['age']));
					$f_comment = htmlspecialchars(iconv("UTF-8", "cp1251", $_REQUEST['comment']));
					$f_gener = htmlspecialchars($_REQUEST['f_gener']);
					srand(make_seed());                                                                                          
					$fname = chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)) . time();//rand(1,999999);			
//					$fname = htmlspecialchars($user['user_name']) . "-" . chr(rand(97,122)) . time();;
					if(is_file($_FILES['Filedata']['tmp_name'])) {
						$info=GetImageSize($_FILES['Filedata']['tmp_name']);
						switch($info[2]) {
							case 1: $type='gif'; break;
							case 2: $type='jpg'; break;
							case 3: $type='png'; break;
							default: $type='error';
						}
						
						if($type=='error') echo 'Error 003: only JPG, GIF and PNG files allowed';
						else {
							$base_name=time();					
							MakeThumb($_FILES['Filedata']['tmp_name'], $foto_thumbs, 100, 100, $type, $fname);
							if($info[0]>440 || $info[1]>600) MakePreview($_FILES['Filedata']['tmp_name'], $foto_newdir, 440, 600, $type, $fname);
							else copy($_FILES['Filedata']['tmp_name'], $foto_newdir.'/'.$fname.'.'.$type);
							$nnname = $foto_newdir.'/'.$fname.'.'.$type;
							unlink($_FILES['Filedata']['tmp_name']);
							$fid = $fname;
							$fname = $fname . '.'.$type;
							$temp_current_user_name = $user['user_name'];
							$gm_date = gmdate("Y-m-d", time());
							$gm_time = gmdate("H:i:s", time());
							$temp_clan = $user['clan'];
							if ($temp_clan == "no") $temp_clan = '';
							if (is_file($nnname)) {
								$query = "INSERT INTO `fotos_new` (`nick`, `file`, `name`, `city`, `gener`, `age`, `comment`, `date`, `time`, `clan`) VALUES ('".$user['user_name']."', '".$fname."', '".$f_name."', '".$f_city."', '".$f_gener."', '".$f_age."', '".$f_comment."', '".$gm_date."', '".$gm_time."', '".$temp_clan."')";
								$result = mysql_query($query);
								if ($result) {
									echo "FILEID:" . $fid;
								} else {
									echo "Error 004: runtime error";
								}
							} else {
								echo "Error 004: runtime error";
							}
						}
					}
//				}
			}
		}
	}
else
	{
		echo ("Error 005: Authorization error");	
	}
?>
