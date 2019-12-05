<?php
	if ($cases_total>0)
	{
		print "Всего дел: ".$cases_total."<br>\n";
		print "Раскрыто дел: ".$cases_solved."<br>\n";
		print "Взято дел: ".$cases_taken."<br>\n";
		print "Незакрытых дел: ".$cases_current."<br>\n";
//		print_stat_time($minimum_case_taken,"Минимальное время взятия дела: ");
//		print_stat_time($average_case_taken/$cases_total,"Среднее время взятия дела: ");
//		print_stat_time($maximum_case_taken,"Максимальное время взятия дела: ");
//		if ($cases_solved>0)
//		{
//			print_stat_time($minimum_case_solved,"Минимальное время раскрытия дела: ");
//			print_stat_time($average_case_solved/$cases_solved,"Среднее время раскрытия дела: ");
//			print_stat_time($maximum_case_solved,"Максимальное время раскрытия дела: ");
//
//			print_stat_time($minimum_case_total,"Минимальное общее(с даты подачи до раскрытия) время дела: ");
//			print_stat_time($average_case_total/$cases_solved,"Среднее общее(с даты подачи до раскрытия) время дела: ");
//			print_stat_time($maximum_case_total,"Максимальное общее(с даты подачи до раскрытия) время дела: ");
//		}
	}
?>
