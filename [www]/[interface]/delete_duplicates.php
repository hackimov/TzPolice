<?
require("../_modules/functions.php");
$sql = "SELECT COUNT(*) AS dub, case_id FROM case_main GROUP BY case_url HAVING dub>1"; 
$res = mysql_query($sql);
while ($r = mysql_fetch_array($res))
	{
		$query = "DELETE FROM case_main WHERE case_id=".$r['case_id'];
		mysql_query($query);
	}
?>