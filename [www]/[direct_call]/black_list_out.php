<html>

<head>
  <title>.::Черный список::.</title>
  <LINK href="../_modules/tzpol_css.css" rel="stylesheet" type="text/css">
</head>

<body><table width="100%"  border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td>
<script language="Javascript" type="text/javascript">
<!--
           function reload_opener() {
            opener.document.location.reload();
			window.close();
            }
//-->
</script>

<?php
require("../_modules/functions.php");
require("../_modules/auth.php");
/*
mysql_connect("localhost", "user", "user");
$mysql_db = "tzpolice";
mysql_select_db($mysql_db);
*/

$cur_date = time();

$query = "SELECT `no_payment_till`, `requests` FROM `police_b_list` WHERE `nick` = '".AuthUserName."' LIMIT 1;";
list($term, $req) = mysql_fetch_row(mysql_query($query));
if ($term < $cur_date)
	{
		$req++;
    	$query = "UPDATE `police_b_list` SET `status` = '1', `requests` = '".$req."' WHERE `nick` = '".AuthUserName."' LIMIT 1;";
//		mysql_query($query) or die(mysql_error());
		if (mysql_query($query))
        	{
            	echo ("Заявка на выход из черного списка принята");
            }
		else
        	{
            	echo ("Сбой при обработке заявки. Попробуйте еще раз позже");
            }
    }
else
	{
    	echo ("Вам было отказано в выходе из черного списка.<br>Вы можете снова подать заявку не ранее ".date('d M Y, H:i:s', $term));
    }
?>
<br><br>
<a href="JavaScript:reload_opener();">ОК</a>
</td>
  </tr>
</table>
</body>

</html>