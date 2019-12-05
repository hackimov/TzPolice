<?php
    foreach (glob("/home/sites/police/bot/*.ses") as $sesname)
    {
    	$ses=basename($sesname,".ses");
    }
    print("<html><body><code>");
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,"http://www.timezero.ru/cgi-bin/forum.pl?l=Terminal%20Police&s=".$ses."&v=city1.timezero.ru&lang=ru");
	curl_setopt($ch,CURLOPT_HEADER,1);
	curl_exec($ch);
	curl_close($ch);
	print("</code></body></html>");
?>