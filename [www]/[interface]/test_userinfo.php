<?php
  include "/home/sites/police/www/_modules/functions.php";
function minitzconn($login,$enforce=0)
{
		$sock = @fsockopen('www.timezero.ru', 80, $er1, $er2, 5);
		if(@$sock) {
		//	echo ' GET /cgi-bin/info.pl?'.trim(urlencode($login)).' HTTP/1.0 ';
			fputs($sock, 'GET /cgi-bin/info.pl?'.trim(urlencode($login))." HTTP/1.0\r\n");
			fputs($sock, "Host: www.timezero.ru \r\n");
			fputs($sock, "Content-type: application/x-www-url-encoded \r\n");
			fputs($sock, "\r\n\r\n");
			
			$tmp_headers = '';
			while ($str = trim(fgets($sock, 4096))) $tmp_headers .= $str."\n";
			$tmp_body = '';
			while (!feof($sock)) $tmp_body .= fgets($sock, 4096);
			$tmp_pos1 = strpos($tmp_body, 'about="');
			if($tmp_pos1!==false) {
				$tmp_str1 = substr($tmp_body, 0, $tmp_pos1);
				$tmp_str2 = substr($tmp_body, strpos($tmp_body, '"', $tmp_pos1+8));
				$tmp_body = $tmp_str1.' '.$tmp_str2;
			}
			$tmp_body = htmlspecialchars($tmp_body);
			$tmp_body = uencode($tmp_body, 'w');
			print_r($tmp_body);
			if(strpos($tmp_body,'502 Bad Gateway')===false){
				if(strpos($tmp_body,'Internal Server Error')===false){
					
					if(strpos($tmp_body,'&lt;TIMEOUT /&gt;')===false){
						
						if (strpos($tmp_body,'&lt;ERRLOGIN /&gt;')===false){
							
							if (strpos($tmp_body,'&lt;NOUSER /&gt')===false){
								if (strpos($tmp_body, '&lt;USER')===false) {
									$funcerror['error'] = 'SERVER_ERROR';
									return $funcerror;
								}else{
									return $tmp_body;
								}
								
							}else{
								
								$funcerror['error'] = 'USER_NOT_FOUND';
								return $funcerror;
								
							}
						}else{
							
							$funcerror['error'] = 'ERROR_IN_USER_NAME';
							return $funcerror;
							
						}
					}else{
						$funcerror['error'] = 'TIMEOUT';
						return $funcerror;
					}
				} else {
					if($enforce==0) {
						$funcerror['error'] = 'TIMEOUT';
						return $funcerror;
					} else {
						sleep(1);
						return miniTZConn($login,1);
					}
				}
			} else {
			//	sleep(1);
				return miniTZConn($login,0);
			}
		} else {
			$funcerror['error'] = 'NOT_CONNECTED';
			return $funcerror;
		}
}
	$userinfo=minitzconn('Terminal Police');
print $userinfo."<br />error - ";
print $userinfo['error']."<br />";
	$userinfo=minitzconn('Terminal PA');
print $userinfo."<br />error - ";
print $userinfo['error']."<br />";
	$userinfo=minitzconn('MP 01');
print $userinfo."<br />error - ";
print $userinfo['error']."<br />";
	$userinfo=minitzconn('MP 02');
print $userinfo."<br />error - ";
print $userinfo['error']."<br />";
	//if (strlen($userinfo)>0) {
	//print "Авторизация работает<br />";
	//}
	//else print "Авторизация не работает<br />";
?>
