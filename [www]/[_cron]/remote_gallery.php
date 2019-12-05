#!/usr/bin/php -q
<?php
/*
error_reporting(E_ALL);
include "../_modules/mysql.php";
$SQL = "SELECT `id`, `file` FROM `fotos_main` WHERE `remote` = '0' LIMIT 15;";
$r = mysql_query($SQL);
while ($d = mysql_fetch_array($r)) {
        	if (is_file('../i/fotos/'.$d['file']))
            	{
                    $file = '../i/fotos/'.$d['file'];
					$remote_file = 'www/upload/gallery/'.$d['file'];
// set up basic connection
					$conn_id = ftp_connect("213.219.216.161");
// login with username and password
					$login_result = ftp_login($conn_id, "police", "gf6djr79k");
// upload a file
					if (ftp_put($conn_id, $remote_file, $file, FTP_BINARY))
                    	{
	                        $SQL = "UPDATE `fotos_main` SET `remote` = '1' WHERE `id`='".$d['id']."' LIMIT 1;";
	                        mysql_query($SQL);
	                        unlink($file);
						}
                    else
                    	{
							$upscreen1 = false;
						}
// close the connection
					ftp_close($conn_id);
                }
        }
*/
?>