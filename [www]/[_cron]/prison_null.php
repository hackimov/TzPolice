<?php

//  error_reporting(0);

//  include "/home/sites/police/www/_modules/mysql.php";

/*	    $link = mysql_connect("localhost", "tzpolice_test", "pWemi45bnjms")
	      or die ("Could not connect to MySQL");
        mysql_select_db ("tzpolice_test") or die ("Could not select database");
	    mysql_query("SET NAMES cp1251");*/
//=========================
//замена тому что выше
 include("/home/sites/police/dbconn/dbconn2.php");
//=========================
  $sql = 'SELECT * FROM prison_chars WHERE add_date>"2000-01-01" AND add_level = 0';
  $res = mysql_query($sql);

  include "/home/sites/police/www/_modules/functions.php";

  $num = mysql_num_rows($res);

  echo ''.date("d.m.Y H:i:s").' Их было '.$num.'<br><br>\n';

  while ($row = mysql_fetch_assoc($res))
  {
    $tmp_user = GetUserInfo($row['nick'], 0);
    if (!$tmp_user["error"] && $tmp_user['level'] > 0)
    {
      $sql = 'UPDATE prison_chars SET add_level='.$tmp_user['level'].' WHERE id='.$row['id'];
      mysql_query($sql);
      echo $row['nick'].' ['.$tmp_user['level'].']'.'<br>';
      die(date("d.m.Y H:i:s").' Одного хватит\n');
    }
  }



?>