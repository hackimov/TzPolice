<?
	include ('/home/sites/police/www/_modules/functions.php');
	include_once ('/home/sites/police/www/_modules/rating_arh/tz_plugins.php');
	error_reporting(0);

	/*	0=время
		1=копэкшн == 178
		2=код действия
			175-наложена чатовая молча,
			176-снята чатовая молча,
			177-наложена форумная молча,
			178-снята форумная молча,
			179-на каторгу,
			180-с каторги,
			181-в блок,
			182-из бока.
			183-постановка чару в дело чистоты перед законом
		3=ник копа
		4=время действия (молчанки и тд)
		5=ник
		6=причина
	*/
$curtime = time()-3600;
$checktime = time()-14400;
$count = 0;
//$curtime = time()-2592000;
//$query="SELECT t2.name as user_name, t1.* FROM cops_actions AS t1 JOIN tzpolice_tz_users AS t2 on (t1.user_id = t2.id) where (t1.time<".$curtime." and t1.user_id<>0);";
$query="SELECT a.*, u.* FROM cops_actions a LEFT JOIN tzpolice_tz_users u ON a.user_id=u.id WHERE a.action=180 AND (a.time>".$curtime." AND a.time<".$checktime.");";
$rs=mysql_query($query);
while ($r=mysql_fetch_array($rs))
	{
		$qu = "SELECT * FROM cops_actions WHERE action=179 AND user_id=".$r['user_id']." AND time>".$r['time']." LIMIT 1;";
		$rr = mysql_query($qu);
//		echo ($qu);
		if (mysql_num_rows($rr) == 0)
			{
//				echo ($r['name']."<br>");
				$workon[] = $r['name'];
			}
	}
mysql_close();
require('/home/sites/police/dbconn/dbconn2.php');
foreach ($workon as $curnick)
	{
		$q = "SELECT * FROM `prison_chars` WHERE `nick` = '".$curnick."';";
//		echo ($q."<br>");
		$q2 = mysql_query($q);
//		echo ("<br>".mysql_num_rows($q2));
		if (mysql_num_rows($q2) > 0)
			{
				$r2 = mysql_fetch_array($q2);
				$count++;
				$qqq = "INSERT INTO `prison_autodelete` SET `nick` = '".$curnick."', `res` = '".$r2['collected']."', `date` = '".time()."';";
				mysql_query($qqq) or die (mysql_error());
				$qqq = "DELETE FROM `prison_chars` WHERE `nick` = '".$curnick."' LIMIT 1;";
				mysql_query($qqq) or die (mysql_error());
			}
	}
echo ($count);
mysql_close();
?>