<?php
//require("/mnt/mirror/usr/home/sites/tzpoli/public_html/_modules/functions.php");
//require("/mnt/mirror/usr/home/sites/tzpoli/public_html/_modules/auth.php");

	include "/home/sites/police/www/_modules/mysql.php";
	$arr=array();$i=0;
    $result=mysql_query("SELECT case_type, count( case_id ) as total_count FROM `case_main` WHERE date_case_taken IS NULL GROUP BY case_type ORDER BY case_type ASC ");
	if ($result&&mysql_num_rows($result))
	{
		while ($row=mysql_fetch_assoc($result))
		{
            	$arr[$i++]=$row["total_count"];
		}
	}//untaken cases
	$result=mysql_query("SELECT case_type,count(case_id) as total_count FROM `case_main` WHERE TO_DAYS(NOW())-TO_DAYS(date_case_begin)>3 and date_case_taken is null group by case_type ORDER BY case_type ASC;");
	if ($result&&mysql_num_rows($result))
	{
		while ($row=mysql_fetch_assoc($result))
		{
				$arr[$i++]=$row["total_count"];
		}
	}//cases over 20 days
    $result=mysql_query("SELECT case_type, count( case_id ) AS total_count FROM `case_main` WHERE date_case_taken BETWEEN DATE_SUB( NOW( ) , INTERVAL 1 DAY ) AND NOW( ) GROUP BY case_type ORDER BY case_type ASC ");
	if ($result&&mysql_num_rows($result))
	{
		while ($row=mysql_fetch_assoc($result))
		{
            	$arr[$i++]=$row["total_count"];
		}
	}//cases taken for now()-24h
    $result=mysql_query("SELECT investigator ,count(case_id) as total_count FROM case_main  WHERE date_case_taken between DATE_SUB(NOW(),INTERVAL 7 DAY) and NOW() GROUP BY `investigator`ORDER BY `investigator` ASC");
	if ($result&&mysql_num_rows($result))
	{
		while ($row=mysql_fetch_assoc($result))
		{
			$arr[$i++]=$row["investigator"]."=".$row["total_count"];
		}
	}
	print implode(",",$arr);
?>