<?
if (AuthUserGroup !== '100' && AuthUserClan!=='police' && AuthUserClan!=='Police Academy')
	{
		die ('“ут ничего интересного, проходим мимо!');
	}
?>
<form name="search" action="?act=sl_search" method="POST">
<input type="text" name="stext">
<input type="submit">
</form>
<?
include_once ('_modules/rating_arh/config.php');
if (isset($_REQUEST['stext']))
	{
		$SQL = 'SELECT * FROM  `cops_actions` WHERE `text` LIKE \''.$_REQUEST['stext'].'\' ORDER BY `time` LIMIT 5;';
//		echo ($SQL);
		$res = mysql_query($SQL);
		if (mysql_num_rows($res) < 1)
			{
				echo ("Ќичего не нашлось");
			}
		else
			{
				while ($row = mysql_fetch_array($res))
					{
						$query = "SELECT `name` FROM `tzpolice_tz_users` WHERE `id` = '".$row['cop_id']."' LIMIT 1;";
						$r = mysql_query ($query);
						$rs = mysql_fetch_array($r);
						$cop = $rs['name'];
						$query = "SELECT `name` FROM `tzpolice_tz_users` WHERE `id` = '".$row['user_id']."' LIMIT 1;";
						$r = mysql_query ($query);
						$rs = mysql_fetch_array($r);
						$user = $rs['name'];
						echo (date('d/m/Y H:i', $row['time'])." коп [".$cop."] - ".$cops_action[$row['action']]." (".$row['action_time'].") на [".$user."] по причине: ".$row['text']."<BR>\n");
					}
			}
	}
?>