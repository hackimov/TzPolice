#!/usr/bin/php -q
<?php
/*
include "../_modules/mysql.php";
$noscreen1 = false;
$noscreen2 = false;
$upscreen1 = false;
$upscreen2 = false;
$res = array();
$SQL = "SELECT `id`, `screen1`, `screen2` FROM `abuse` WHERE `remote` = '0' LIMIT 10;";
$r = mysql_query($SQL);
while ($d = mysql_fetch_array($r)) {
	if (strlen($d['screen1']) > 4)
    	{
        	if (is_file('../_imgs/abuse/'.$d['screen1']))
            	{
                    $file = '../_imgs/abuse/'.$d['screen1'];
                    $sc1 = $file;
					$remote_file = 'www/upload/abuse/'.$d['screen1'];
// set up basic connection
					$conn_id = ftp_connect("213.219.216.161");
// login with username and password
					$login_result = ftp_login($conn_id, "police", "gf6djr79k");
// upload a file
					if (ftp_put($conn_id, $remote_file, $file, FTP_BINARY))
                    	{
							$upscreen1 = true;
						}
                    else
                    	{
							$upscreen1 = false;
						}
// close the connection
					ftp_close($conn_id);
                }
        }
   else
   		{
        	$noscreen1 = true;
        }
	if (strlen($d['screen2']) > 4)
    	{
        	if (is_file('../_imgs/abuse/'.$d['screen2']))
            	{
                    $file = '../_imgs/abuse/'.$d['screen2'];
                    $sc2 = $file;
					$remote_file = 'www/upload/abuse/'.$d['screen2'];
// set up basic connection
					$conn_id = ftp_connect("213.219.216.161");
// login with username and password
					$login_result = ftp_login($conn_id, "police", "gf6djr79k");
// upload a file
					if (ftp_put($conn_id, $remote_file, $file, FTP_BINARY))
                    	{
							$upscreen2 = true;
						}
                    else
                    	{
							$upscreen2 = false;
						}
// close the connection
					ftp_close($conn_id);
                }
        }
   else
   		{
        	$noscreen2 = true;
        }
if (($upscreen1 && $upscreen2) || ($noscreen1 && $upscreen2) || ($upscreen1 && $noscreen2))
	{
    	$SQL = "UPDATE `abuse` SET `remote` = '1' WHERE `id` = '".$d['id']."' LIMIT 1;";
        mysql_query($SQL);
        if ($upscreen1) unlink($sc1);
        if ($upscreen2) unlink($sc2);
    }
}
*/
?>