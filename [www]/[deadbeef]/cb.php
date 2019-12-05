<html>

<head>
  <title>Hello!</title>
</head>

<body>

<?
//error_reporting(E_ALL);
//$promote_cb = rand(0,999);
//echo ($promote_cb);
/*
if ($promote_cb > 989)
	{
		$file = file_get_contents("http://centerbeauty.ru/sitemap.xml");
		preg_match_all("#<loc>.*?</loc>#is", $file, $matches);
		echo ("<pre>");
		print_r ($matches);
		echo ("</pre><br>".count($matches[0]));
		$luckywinner = rand(0, count($matches[0]));
		$cb_url = str_replace("<loc>", "", $matches[0][$luckywinner]);
		$cb_url = str_replace("</loc>", "", $cb_url);
		echo('<iframe name="cb" src="'.$cb_url.'?counter" width="0" height="0" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe>' );
	}
*/
		$file = file_get_contents("http://centerbeauty.ru/sitemap.xml");
		preg_match_all("#<loc>.*?</loc>#is", $file, $matches);
		$luckywinner = rand(0, count($matches[0]));
		$cb_url = str_replace("<loc>http://centerbeauty.ru", "", $matches[0][$luckywinner]);
		$cb_url = str_replace("</loc>", "", $cb_url);
		$hostname= "centerbeauty.ru";
		$path= $cb_url;
		$line="";
		$fp=fsockopen($hostname, 80, $errno, $errstr, 30);
		if($fp)
			{
				$data="mode=counter";
				//Заголовок HTTP-запроса
				$headers="POST $path HTTP/1.1\r\n";
				$headers.= "Host: $hostname\r\n";
				$headers.= "Content-type: application/x-www-form-urlencoded\r\n";
				$headers.="Content-Length: ".strlen($data)."\r\n";
				//Подделываем реферер
				$headers.="Referer: http://www.yandex.ru/yandsearch\r\n";
				$headers.="Connection: Close\r\n\r\n";
				//Отправляем HTTP-запрос серверу
				fwrite($fp, $headers.$data);
				//Получаем ответ
				while(!feof($fp))
					{
						$line.=fgets($fp, 1024);
					}
				fclose($fp);
				echo ("<div id='cb' style='display: none;'>");
				print_r($line);
				echo ("/div>");
			}
?>

</body>

</html>
