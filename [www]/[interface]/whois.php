<html>
<head>
  <title>IP information</title>
</head>
<body>
  <center>
     <H4>���������� �� IP-������</H4>
       <FORM action=whois.php method=post>
         <INPUT type=text name=ip size=35>
         <input type=submit value='���������'>
       </form>
  </center>

<?php
  if ($_REQUEST["ip"]!="") {
    $sock = fsockopen ("whois.ripe.net",43,$errno,$errstr);
    //���������� � ������� TCP, ��������� �� ������� "whois.ripe.net" �� 43 �����.���������� ���������� ���������� 
  
    if (!$sock) {
      echo("$errno($errstr)");
      return;
    }
    else {
      fputs ($sock, $_REQUEST["ip"]."\r\n");
      //���������� ������ �� ���������� $ip � ���������� ������ 

      while (!feof($sock)) {
        echo (str_replace(":",":      ",fgets ($sock,128))."<br>");
        //������������ ������ �� ����������� ������ 
      }
    }
    fclose ($sock);
    //�������� ����������
  }
?>


</body>
</html>