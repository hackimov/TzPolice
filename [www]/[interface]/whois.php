<html>
<head>
  <title>IP information</title>
</head>
<body>
  <center>
     <H4>Информация об IP-адресе</H4>
       <FORM action=whois.php method=post>
         <INPUT type=text name=ip size=35>
         <input type=submit value='Проверить'>
       </form>
  </center>

<?php
  if ($_REQUEST["ip"]!="") {
    $sock = fsockopen ("whois.ripe.net",43,$errno,$errstr);
    //соединение с сокетом TCP, ожидающим на сервере "whois.ripe.net" на 43 порту.Возвращает дескриптор соединения 
  
    if (!$sock) {
      echo("$errno($errstr)");
      return;
    }
    else {
      fputs ($sock, $_REQUEST["ip"]."\r\n");
      //записываем строку из переменной $ip в дескриптор сокета 

      while (!feof($sock)) {
        echo (str_replace(":",":      ",fgets ($sock,128))."<br>");
        //осуществляем чтение из дескриптора сокета 
      }
    }
    fclose ($sock);
    //закрытие соединения
  }
?>


</body>
</html>