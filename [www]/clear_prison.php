<?
	include ('/home/sites/police/www/_modules/functions.php');
	include_once ('/home/sites/police/www/_modules/rating_arh/tz_plugins.php');
	error_reporting(0);

	/*	0=�����
		1=������� == 178
		2=��� ��������
			175-�������� ������� �����,
			176-����� ������� �����,
			177-�������� �������� �����,
			178-����� �������� �����,
			179-�� �������,
			180-� �������,
			181-� ����,
			182-�� ����.
			183-���������� ���� � ���� ������� ����� �������
		3=��� ����
		4=����� �������� (�������� � ��)
		5=���
		6=�������
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