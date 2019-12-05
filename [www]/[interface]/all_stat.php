<?php
require("../_modules/functions.php");
require("../_modules/auth.php");
require("_misc_funcs.php");
    if (!(Abs(AccessLevel)&AccessAdminOR))
    {
		header("HTTP/1.1 303 See other");
		header("Location: http://www.tzpolice.ru/");
		die;
    }
	include "_ORheader.php";
	$template_type=array();
	$template_type["untemplated"]="Дел вне шаблона: ";
	$template_type["0"]="Дел, закрытых без применения расследования: ";
	$template_type["1"]="Дел, расследованных: ";
	print "<center><h1>Общая статистика ".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>3?3:$ctype[$_REQUEST["case_type"]]):$ctype[1])."</h1></center>\n";
	print_period("all_stat");
	$dates_between="\"".$_REQUEST["year_date_from"]."-".$_REQUEST["month_date_from"]."-".$_REQUEST["day_date_from"]." 00:00:00\" and \"".$_REQUEST["year_date_to"]."-".$_REQUEST["month_date_to"]."-".$_REQUEST["day_date_to"]." 00:00:00\"";
	$result=mysql_query("SELECT COUNT(case_id) as max_free FROM case_main WHERE investigator IS NULL and case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1")." and date_case_begin between ".$dates_between.";");
	if ($result&&mysql_num_rows($result))
	{
		$row=mysql_fetch_assoc($result);
		print "Общее количество дел без следователя: ".$row["max_free"]."<br>\n";
	}
	$result=mysql_query("SELECT UNIX_TIMESTAMP(MIN(date_case_begin)) as date_case_begin  FROM case_main WHERE investigator IS NULL and case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$ctype[$_REQUEST["case_type"]]):"=1")." and date_case_begin between ".$dates_between.";");
	if ($result&&mysql_num_rows($result))
	{
		$row=mysql_fetch_assoc($result);
		print "Время, прошедшее со дня самой ранней невзятой жалобы: ";
		print_stat_time(time()-$row["date_case_begin"]);
		print "<br>\n<hr>Сводная информация по отделу ".(isset($_REQUEST["case_type"])?$ctype[($_REQUEST["case_type"]>2?"3":$_REQUEST["case_type"])]:$ctype[1]).":<br>";
	}
//	$result=mysql_query("SELECT investigator,UNIX_TIMESTAMP(date_case_begin) as date_case_begin,UNIX_TIMESTAMP(date_case_taken) as date_case_taken,UNIX_TIMESTAMP(date_case_closed) as date_case_closed FROM case_main WHERE investigator IS NOT NULL and case_type=".(isset($_REQUEST["case_type"])?$_REQUEST["case_type"]:"1")." and date_case_begin between \"".$_REQUEST["year_date_from"]."-".$_REQUEST["month_date_from"]."-".$_REQUEST["day_date_from"]." 00:00:00\" and \"".$_REQUEST["year_date_to"]."-".$_REQUEST["month_date_to"]."-".$_REQUEST["day_date_to"]." 00:00:00\" ORDER BY investigator;");
	$result=mysql_query("SELECT investigator,UNIX_TIMESTAMP(date_case_begin) as date_case_begin,UNIX_TIMESTAMP(date_case_taken) as date_case_taken,UNIX_TIMESTAMP(date_case_closed) as date_case_closed FROM case_main WHERE investigator IS NOT NULL and case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1")." and date_case_closed between ".$dates_between." ORDER BY investigator;");
	if ($result&&mysql_num_rows($result))
	{
		$minimum_case_taken=100000000;//too much - 1157 days and some more time+
		$maximum_case_taken=0;
		$average_case_taken=0;
		$minimum_case_solved=100000000;
		$average_case_solved=0;
		$maximum_case_solved=0;
		$minimum_case_total=100000000;
		$average_case_total=0;
		$maximum_case_total=0;
		$cases_solved=0;
		$result2=mysql_query("SELECT count(case_id) as cases_current FROM case_main WHERE date_case_closed = 0 and date_case_taken!=0 and case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1").";");
		$row2=mysql_fetch_assoc($result2);
		$cases_current=$row2["cases_current"];
		$result2=mysql_query("SELECT count(case_id) as cases_taken FROM case_main WHERE date_case_taken between ".$dates_between." and case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1").";");
		$row2=mysql_fetch_assoc($result2);
		$cases_taken=$row2["cases_taken"];
		$cases_total=$cases_current;
		while ($row=mysql_fetch_assoc($result))
		{
			$cases_total++;
			$now_case_taken=$row["date_case_taken"]-$row["date_case_begin"];
			$average_case_taken+=$now_case_taken;
			if ($now_case_taken>$maximum_case_taken)
			{
				$maximum_case_taken=$now_case_taken;
			}
			if ($now_case_taken<$minimum_case_taken)
			{
				$minimum_case_taken=$now_case_taken;
			}
			if ($row["date_case_closed"]>0)
			{
				$cases_solved++;
				$now_case_solved=$row["date_case_closed"]-$row["date_case_taken"];
				$average_case_solved+=$now_case_solved;
				if ($now_case_solved>$maximum_case_solved)
				{
					$maximum_case_solved=$now_case_solved;
				}
				if ($now_case_solved<$minimum_case_solved)
				{
					$minimum_case_solved=$now_case_solved;
				}
				$now_case_total=$row["date_case_closed"]-$row["date_case_begin"];
				$average_case_total+=$now_case_total;
				if ($now_case_total>$maximum_case_total)
				{
					$maximum_case_total=$now_case_total;
				}
				if ($now_case_total<$minimum_case_total)
				{
					$minimum_case_total=$now_case_total;
				}
			}
			else
			{
				$cases_current++;
			}
		}
		include "print_stat.php";
		$result=mysql_query("(SELECT 'untemplated' as template,count(case_id) as resultset from case_main where case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1")." and date_case_closed between ".$dates_between." and closure_type=0) union (SELECT '0',count(case_id) as resultset from case_main where case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1")." and date_case_closed between ".$dates_between." and closure_type=1) union (SELECT '1',count(case_id) as resultset from case_main where case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1")." and date_case_closed between ".$dates_between." and closure_type=2)");
		if ($result&&mysql_num_rows($result))
		{
			while ($row=mysql_fetch_assoc($result))
			{
				if ($row["resultset"]!=0)
				{
					print "<br>".$template_type[$row["template"]].$row["resultset"];
				}
			}
		}
		if ($_REQUEST["case_type"]>2)
		{
			include "print_whines.php";
		}
	}
//	$result=mysql_query("SELECT investigator,UNIX_TIMESTAMP(date_case_begin) as date_case_begin,UNIX_TIMESTAMP(date_case_taken) as date_case_taken,UNIX_TIMESTAMP(date_case_closed) as date_case_closed FROM case_main WHERE investigator IS NOT NULL and case_type=".(isset($_REQUEST["case_type"])?$_REQUEST["case_type"]:"1")." and date_case_begin between ".$dates_between." ORDER BY investigator;");
	$result=mysql_query("SELECT investigator,UNIX_TIMESTAMP(date_case_begin) as date_case_begin,UNIX_TIMESTAMP(date_case_taken) as date_case_taken,UNIX_TIMESTAMP(date_case_closed) as date_case_closed FROM case_main WHERE investigator IS NOT NULL and case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1")." and date_case_closed between ".$dates_between." ORDER BY investigator;");
	if ($result&&mysql_num_rows($result))
	{
		$investigator="";
		while ($row=mysql_fetch_assoc($result))
		{
			if ($investigator=="")
			{
				$investigator=$row["investigator"];
				$minimum_case_taken=100000000;//too much - 1157 days and some more time+
				$maximum_case_taken=0;
				$average_case_taken=0;
				$minimum_case_solved=100000000;
				$average_case_solved=0;
				$maximum_case_solved=0;
				$minimum_case_total=100000000;
				$average_case_total=0;
				$maximum_case_total=0;
				$cases_solved=0;
                $result2=mysql_query("SELECT count(case_id) as cases_current FROM case_main WHERE investigator=\"".$investigator."\" and date_case_closed = \"0000-00-00 00:00:00\" and case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1").";");
                $row2=mysql_fetch_assoc($result2);
				$cases_current=$row2["cases_current"];
                $result2=mysql_query("SELECT count(case_id) as cases_taken FROM case_main WHERE investigator=\"".$investigator."\" and date_case_taken between ".$dates_between." and case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1").";");
                $row2=mysql_fetch_assoc($result2);
				$cases_taken=$row2["cases_taken"];
				$cases_total=$cases_current;
			}
			if ($investigator<>$row["investigator"])
			{
			    print "<hr>\n";
			    print "<h2>Дела следователя ".$investigator."</h2>";
			    include "print_stat.php";
			    $result2=mysql_query("(SELECT 'untemplated' as template,count(case_id) as resultset from case_main where case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1")." and date_case_closed between ".$dates_between." and investigator=\"".$investigator."\" and closure_type=0) union (SELECT '0',count(case_id) as resultset from case_main where case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1")." and date_case_closed between ".$dates_between." and investigator=\"".$investigator."\" and closure_type=1) union (SELECT '1',count(case_id) as resultset from case_main where case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1")." and date_case_closed between ".$dates_between." and investigator=\"".$investigator."\" and closure_type=2)");
				if ($result2&&mysql_num_rows($result))
				{
					while ($row2=mysql_fetch_assoc($result2))
					{
						if ($row2["resultset"]!=0)
						{
							print "<br>".$template_type[$row2["template"]].$row2["resultset"];
						}
					}
				}
				if ($_REQUEST["case_type"]>2)
				{
					$invest_whines=$investigator;
					include "print_whines.php";
				}
				$investigator=$row["investigator"];
				$minimum_case_taken=100000000;//too much - 1157 days and some more time+
				$maximum_case_taken=0;
				$average_case_taken=0;
				$minimum_case_solved=100000000;
				$average_case_solved=0;
				$maximum_case_solved=0;
				$minimum_case_total=100000000;
				$average_case_total=0;
				$maximum_case_total=0;
				$cases_solved=0;
                $result2=mysql_query("SELECT count(case_id) as cases_current FROM case_main WHERE investigator=\"".$investigator."\" and date_case_closed = \"0000-00-00 00:00:00\" and case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1").";");
                $row2=mysql_fetch_assoc($result2);
				$cases_current=$row2["cases_current"];
                $result2=mysql_query("SELECT count(case_id) as cases_taken FROM case_main WHERE investigator=\"".$investigator."\" and date_case_taken between ".$dates_between." and case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1").";");
                $row2=mysql_fetch_assoc($result2);
				$cases_taken=$row2["cases_taken"];
				$cases_total=$cases_current;
			}
			$cases_total++;
			$now_case_taken=$row["date_case_taken"]-$row["date_case_begin"];
			$average_case_taken+=$now_case_taken;
			if ($now_case_taken>$maximum_case_taken)
			{
				$maximum_case_taken=$now_case_taken;
			}
			if ($now_case_taken<$minimum_case_taken)
			{
				$minimum_case_taken=$now_case_taken;
			}
			if ($row["date_case_closed"]>0)
			{
				$cases_solved++;
				$now_case_solved=$row["date_case_closed"]-$row["date_case_taken"];
				$average_case_solved+=$now_case_solved;
				if ($now_case_solved>$maximum_case_solved)
				{
					$maximum_case_solved=$now_case_solved;
				}
				if ($now_case_solved<$minimum_case_solved)
				{
					$minimum_case_solved=$now_case_solved;
				}
				$now_case_total=$row["date_case_closed"]-$row["date_case_begin"];
				$average_case_total+=$now_case_total;
				if ($now_case_total>$maximum_case_total)
				{
					$maximum_case_total=$now_case_total;
				}
				if ($now_case_total<$minimum_case_total)
				{
					$minimum_case_total=$now_case_total;
				}
			}
			else
			{
				$cases_current++;
			}
		}
			if ($investigator<>$row["investigator"])
			{
			    print "<hr>\n";
			    print "<h2>Дела следователя ".$investigator."</h2>";
			    include "print_stat.php";
			    $result2=mysql_query("(SELECT 'untemplated' as template,count(case_id) as resultset from case_main where case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1")." and date_case_closed between ".$dates_between." and investigator=\"".$investigator."\" and closure_type=0) union (SELECT '0',count(case_id) as resultset from case_main where case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1")." and date_case_closed between ".$dates_between." and investigator=\"".$investigator."\" and closure_type=1) union (SELECT '1',count(case_id) as resultset from case_main where case_type".(isset($_REQUEST["case_type"])?($_REQUEST["case_type"]>2?">2":"=".$_REQUEST["case_type"]):"=1")." and date_case_closed between ".$dates_between." and investigator=\"".$investigator."\" and closure_type=2)");
				if ($result2&&mysql_num_rows($result))
				{
					while ($row2=mysql_fetch_assoc($result2))
					{
						if ($row2["resultset"]!=0)
						{
							print "<br>".$template_type[$row2["template"]].$row2["resultset"];
						}
					}
				}
				if ($_REQUEST["case_type"]>2)
				{
					$invest_whines=$investigator;
					include "print_whines.php";
				}
		 	}
	}
	include "_ORfooter.php";
	print"</BODY>";
?>