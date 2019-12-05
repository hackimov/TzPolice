<?php
    $whines=array();
    $whines["0"]="Жалоб на полицию вообще (несортированных)";
    $whines["1"]="Жалоб на ОМ+ЛО";
	$whines["2"] = "Жалоб на ОИН";
	$whines["3"] = "Жалоб на ЮО";
	$whines["4"] = "Жалоб на ОБЭП";
	$whines["5"] = "Жалоб на ОР";
	$whines["6"] = "Жалоб на ОКП";
	$whines["7"] = "Жалоб на МП";
	$whines["8"] = "Жалоб на нач. полиции";
	$whines["9"] = "Жалоб на ОКИР";
	$res=mysql_query("(SELECT '0' as template,count(case_id) as resultset from case_main where ".(isset($invest_whines)?"investigator=\"".$invest_whines."\" and ":"")."date_case_closed between ".$dates_between." and case_type=3) union (SELECT '1' as template,count(case_id) as resultset from case_main where ".(isset($invest_whines)?"investigator=\"".$invest_whines."\" and ":"")."date_case_closed between ".$dates_between." and case_type=4) union (SELECT '2' as template,count(case_id) as resultset from case_main where ".(isset($invest_whines)?"investigator=\"".$invest_whines."\" and ":"")."date_case_closed between ".$dates_between." and case_type=5) union (SELECT '3' as template,count(case_id) as resultset from case_main where ".(isset($invest_whines)?"investigator=\"".$invest_whines."\" and ":"")."date_case_closed between ".$dates_between." and case_type=6) union (SELECT '4' as template,count(case_id) as resultset from case_main where ".(isset($invest_whines)?"investigator=\"".$invest_whines."\" and ":"")."date_case_closed between ".$dates_between." and case_type=7) union (SELECT '5' as template,count(case_id) as resultset from case_main where ".(isset($invest_whines)?"investigator=\"".$invest_whines."\" and ":"")."date_case_closed between ".$dates_between." and case_type=8) union (SELECT '6' as template,count(case_id) as resultset from case_main where ".(isset($invest_whines)?"investigator=\"".$invest_whines."\" and ":"")."date_case_closed between ".$dates_between." and case_type=9) union (SELECT '7' as template,count(case_id) as resultset from case_main where ".(isset($invest_whines)?"investigator=\"".$invest_whines."\" and ":"")."date_case_closed between ".$dates_between." and case_type=10) union (SELECT '8' as template,count(case_id) as resultset from case_main where ".(isset($invest_whines)?"investigator=\"".$invest_whines."\" and ":"")."date_case_closed between ".$dates_between." and case_type=11) union (SELECT '9' as template,count(case_id) as resultset from case_main where ".(isset($invest_whines)?"investigator=\"".$invest_whines."\" and ":"")."date_case_closed between ".$dates_between." and case_type=12)");
	$firsttime_whine=1;
	if ($res&&mysql_num_rows($res))
	{
			while ($rws=mysql_fetch_assoc($res))
			{
				if ($rws["resultset"]!=0)
				{
					if ($firsttime_whine==1)
					{
						    print "<br><br>Жалобная статистика:";
						    $firsttime_whine=0;
					}
					print "<br>".$whines[$rws["template"]]." : ".$rws["resultset"];
				}
			}
	}
?>