#!/usr/bin/php -q
<?php
////////////
// 1 = on
// 0 = off
$debug = 0;
$debug_log_name = "debug1.txt";
////////////
$main_path = "/home/sites/police/www/interface/cron";
	
	function R_microtime()
	{
		list($usec, $sec) = explode(' ', microtime());
		return (double) $usec + (double) $sec;
	};
	function R_gentime()
	{
		global $R_gentime;
		if (isset ($R_gentime))
		return number_format (R_microtime() - $R_gentime, 3);
		$R_gentime = R_microtime();
	};
/*	function Police_search($searchcontent)
	{	
		
	//	if (preg_match("/<TR>.*?<IMG SRC=\"\/i\/clans\/police.gif\" BORDER=0 WIDTH=28 HEIGHT=16 ALT=\"\"><span onclick=\"sl\('','','<INFO>(.{1,16})<\/INFO>'\)\".{100,500}<font class=\"sm\">Добавлено:<BR>((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d))<\/font><BR>/is",$searchcontent,$result)==1)
	    if (preg_match("/<img src=\"\/i\/clans\/police.gif\" border=\"0\" width=\"28\" height=\"16\" ALT=\"\" class=\"imgh\" \/><span class=\"nickname\"><span onclick=\"sl\(\'\',\'\',\'<INFO>(.{1,16})<\/INFO>\'\)\".{10,2000}<br\/>Добавлено:<br\/>((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d))<br \/>/is",$searchcontent,$result)==1)
		{
			return $result;//found police officer in this topic with his timestamp of posting
		}
		else
		{
				return;//return with no value to test with isset()
		}
	}
*/
function Police_search($searchcontent)
	{	
		$searchcontent = str_replace("Сегодня, в", date("d.m.y"), $searchcontent);
		$searchcontent = str_replace("Вчера, в", date("d.m.y", time()-86400), $searchcontent);
	if (preg_match("/<img src=\"\/i\/clans\/police.gif\" border=\"0\" width=\"28\" height=\"16\" ALT=\"\" class=\"imgh\" \/><span class=\"nickname\"><span onclick=\"sl\(\'\',\'\',\'<INFO>(.{1,16})<\/INFO>\'\)\".{10,2000}Добавлено:<br\/>.*?((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d)).*?<br \/>/is",$searchcontent,$result)==1)
		{
			return $result;//found police officer in this topic with his timestamp of posting
		}
	if (preg_match("/<img src=\"\/i\/clans\/Tribunal.gif\" border=\"0\" width=\"28\" height=\"16\" ALT=\"\" class=\"imgh\" \/><span class=\"nickname\"><span onclick=\"sl\(\'\',\'\',\'<INFO>(.{1,16})<\/INFO>\'\)\".{10,2000}Добавлено:<br\/>.*?((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d)).*?<br \/>/is",$searchcontent,$result)==1)
		{
			return $result;//found tribunal officer in this topic with his timestamp of posting
		}
	
		else
		{
				return;//return with no value to test with isset()
		}
	}

	function Get_multipage_content($ch,$url,$from_where)
	{
        $ch=curl_init();
//        $ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,$url."&b=".($GLOBALS["current_page_".$from_where]+1)."&m=1");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/sites/police/www/interface/cron/cjr.ck");
	curl_setopt($ch,CURLOPT_COOKIE,"tzl=Terminal Police; path=/;");
	curl_setopt($ch,CURLOPT_COOKIE,"tzses=".$ses."; path=/;");
	curl_setopt($ch,CURLOPT_COOKIE,"tzsrv=city1.timezero.ru; path=/;");
	curl_setopt($ch,CURLOPT_COOKIE,"tzlang=ru; path=/;");
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_HEADER,1);
	// Отключить ошибку "SSL certificate problem, verify that the CA cert is OK"
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	// Отключить ошибку "SSL: certificate subject name 'hostname.ru' does not match target host name '123.123.123.123'"
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

//        $fp=fopen($url."&b=".($GLOBALS["current_page_".$from_where]+1),"r");//get next page
//        if (!$fp)
//        {//error somewhere... 500 or something else on TZ server. report it and die
//            error_log(date("d.m.Y H:i:s")." Unable to retrieve ".$url."&b=".($GLOBALS["current_page_".$from_where]+1)." due to: ".$php_errormsg,3,"errlog.txt");
//            die(date("d.m.Y H:i:s").": ".$php_errormsg);
//        }
        $content="";
        $content=curl_exec($ch);
        curl_close($ch);
//		while (!feof($fp))
//		{
//			$content.=fread($fp,1000000);
//		}
//		fclose($fp);
		$GLOBALS["current_page_".$from_where]++;
		return iconv("UTF-8","WINDOWS-1251",$content);
	}
//main program
//	include "../../_modules/mysql.php";
	include "/home/sites/police/www/_modules/mysql.php";
    foreach (glob("/home/sites/police/bot/*.ses") as $sesname)
    {
    	$ses=basename($sesname,".ses");
    }
//	R_gentime();
	$result=mysql_query("SELECT * from case_main where date_case_closed=0;");
	if ($result&&mysql_num_rows($result))
	{//we have unclosed cases (some of them may be also untaken!)
		while ($row=mysql_fetch_assoc($result))
		{
			$sql_command="UPDATE case_main set ";
		//	print "<br>!".$row["case_url"];
			if($debug==1){
				error_log("LEVEL1: ".date("d.m.Y H:i:s")." ".$row["case_url"]."\n", 3, $main_path."/".$debug_log_name."");
			}
			$need_update=0;
			$is_moved_topic=0;
			$GLOBALS["current_page_message"]=0;
			if ($row["date_case_taken"]==0)
			{//case is untaken, test for takingness and closure
				unset($police);//clear police variable. just in case...
				//print "http://www.timezero.ru/cgi-bin/".$row["case_url"]."\n";
				$i=0;$found_police=0;
				do
				{	
					if($debug==1){
						error_log("LEVEL2.1 (date_case_taken==0) (i = ".$i."): ".date("d.m.Y H:i:s")." ".$row["case_url"]."\n", 3, $main_path."/".$debug_log_name."");
					}

					$content=Get_multipage_content($ch,"https://www.timezero.ru/cgi-bin/".$row["case_url"],"message");

					$content = str_replace('class="press"','class="link_name_forum"',$content);
					$content = str_replace('class="adm"','class="link_name_forum"',$content);
					$content = str_replace("Сегодня, в", date("d.m.y"), $content);
					$content = str_replace("Вчера, в", date("d.m.y", time()-86400), $content);
				
					if ((preg_match("/class=adrd>Перемещено в форум/is",$content,$garbage))||(preg_match("/<th><b>Не найден топик, возможно он был удален, или перенесен в другой форум<\/b><\/th>/is",$content,$garbage)))
//					if ((preg_match("/class=adrd>Перемещено в форум/is",$content,$garbage))||(preg_match("/<h1>.{0,13}ОШИБКА.{0,13}<\/h1>.{0,170}<font class=\"norm\">(Не найден т)|(Т)опик(, возможно он)? был удален(, или перенесен в другой форум)?<\/font>/is",$content,$garbage)))
//					if ((preg_match("/class=adrd>Перемещено в форум/is",$content,$garbage))||(preg_match("/<font class=\"title2\">.{0,13}ОШИБКА.{0,13}<\/font>.{0,170}<font class=\"norm\">Топик был удален<\/font>/is",$content,$garbage)))
					{//test for moved topic
						$is_moved_topic=1;//delete record
						if($debug==1){
							error_log("LEVEL2.1 (date_case_taken==0) (i = ".$i."): ".date("d.m.Y H:i:s")." ".$row["case_url"]."- deleted\n", 3, $main_path."/".$debug_log_name."");
						}
						$sql_command="DELETE FROM case_main";
						$need_update=1;
						break;
					}
					else
					{
						if($debug==1){
							error_log("LEVEL2.1 (date_case_taken==0) (i = ".$i."): ".date("d.m.Y H:i:s")." ".$row["case_url"]."- content not matched, dumping: \n".$content."\n", 3, $main_path."/".$debug_log_name."");
						}
					}
					if ($found_police==0){
						$police=Police_search($content);
						if (isset($police))
						{
							$sql_command.=" investigator=\"".$police[1]."\", date_case_taken=\"".$police[5]."-".$police[4]."-".$police[3]." ".$police[6].":".$police[7]."\"";
							$need_update=1;
							$found_police=1;//found police, no need to search
						}
					}
					$i++;
					if ($i>50) break;
				}
	            while(!preg_match("/<span class=\"pager\"><strong>На страницу:<\/strong>.*?<font class=nb>\[".$GLOBALS["current_page_message"]."\]<\/font><\/span>/is",$content,$garbage));//not last page? continue then...
			}
			else
			{//case is taken only closure test needed
				$i=0;
				do
				{	
					if($debug==1){
						error_log("LEVEL2.1 (date_case_taken!=0) (i = ".$i."): ".date("d.m.Y H:i:s")." ".$row["case_url"]."\n", 3, $main_path."/".$debug_log_name."");
					}

					$content=Get_multipage_content($ch,"https://www.timezero.ru/cgi-bin/".$row["case_url"],"message");
					
					if ((preg_match("/class=adrd>Перемещено в форум/is",$content,$garbage))||(preg_match("/<th><b>Не найден топик, возможно он был удален, или перенесен в другой форум<\/b><\/th>/is",$content,$garbage)))
//					if ((preg_match("/class=adrd>Перемещено в форум/is",$content,$garbage))||(preg_match("/<font class=\"title2\">.{0,13}ОШИБКА.{0,13}<\/font>.{0,170}<font class=\"norm\">Топик был удален<\/font>/is",$content,$garbage)))
					{//test for moved topic
						if($debug==1){
							error_log("LEVEL2.1 (date_case_taken!=0) (i = ".$i."): ".date("d.m.Y H:i:s")." ".$row["case_url"]." - deleted\n", 3, $main_path."/".$debug_log_name."");
						}
						$is_moved_topic=1;//delete record
						$sql_command="DELETE FROM case_main";
						$need_update=1;
						break;
					}
					else{
						if($debug==1){
							error_log("LEVEL2.1 (date_case_taken!=0) (i = ".$i."): ".date("d.m.Y H:i:s")." ".$row["case_url"]." content not matched, dumping:\n".$content."\n", 3, $main_path."/".$debug_log_name."");
						}
					}
					$i++;
					if ($i>50) break;
				}
	            while(!preg_match("/<span class=\"pager\"><strong>На страницу:<\/strong>.*?<font class=nb>\[".$GLOBALS["current_page_message"]."\]<\/font><\/span>/is",$content,$garbage));//not last page? continue then...
			}
			//now $content is the last page, test for closure

//		      			     if (preg_match("/<img src=\"\/i\/clans\/police.gif\" border=\"0\" width=\"28\" height=\"16\" ALT=\"\" class=\"imgh\" \/><span class=\"nickname\"><span onclick=\"sl\(\'\',\'\',\'<INFO>(.{1,16})<\/INFO>\'\)\".{10,2000}Добавлено:<br\/>.*?((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d)).*?<br \/>/is",$searchcontent,$result)==1)
			if ((!$is_moved_topic)&&(preg_match("/<img src=\"\/i\/clans\/police.gif\" border=\"0\" width=\"28\" height=\"16\" ALT=\"\" class=\"imgh\" \/><span class=\"nickname\"><span onclick=\"sl\(\'\',\'\',\'<INFO>(.{1,16})<\/INFO>\'\)\".{10,2000}Добавлено:<br\/>.*?((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d)).*?<br \/>.*?<td class=\"message_col\" id=\"q(\d+)\"><font class=adrd>Обсуждение закрыто<\/font>/is", $content, $endmessage)))
			{
//				print "<br>!".$row["case_url"];
				$sql_command="UPDATE case_main SET investigator=\"".$endmessage[1]."\", date_case_closed=\"".$endmessage[5]."-".$endmessage[4]."-".$endmessage[3]." ".$endmessage[6].":".$endmessage[7]."\", answers=".$endmessage[8];
				if (isset($police))
				{
					$sql_command.=", date_case_taken=\"".$police[5]."-".$police[4]."-".$police[3]." ".$police[6].":".$police[7]."\"";
				}
				$closure_answer=$endmessage[8]-1;
				preg_match("/<td class=\"message_col\" id=\"q".$closure_answer."\">(.*?)<\/td>/is",$content,$final_answer);
				//print "\n\n".$final_answer[1]."\n\n";
//				error_log("start of final answer\n".$final_answer[1]."\nend of final answer ".$closure_answer." of http://www.timezero.ru/cgi-bin/".$row["case_url"]."\n",3,"/home/sites/police/www/interface/cron/testsh.txt");
				if (preg_match("/\[(?:obep|or)=(\d+)\]/is",$final_answer[1],$closure_type))
				{
					$sql_command.=", closure_type=".($closure_type[1]+1);
				}
//check and update IP pattern
				$isIPs = preg_match_all("/\[IP=(.*?)\]/is",$final_answer[1],$IPs,PREG_SET_ORDER);
				if($isIPs) {
					foreach ($IPs as $IP) {
						$SQL = "INSERT INTO vzlom VALUES('', '".$IP[1]."', '".$row["case_url"]."');";
						mysql_query($SQL) or die("Invalid query: ".mysql_error());
					}
				}
//end				
				$compensations=preg_match_all("/\[v=(.*?);s=(\d+);p=(\d*);d=(.*?)\]/is",$final_answer[1],$compensation_infos,PREG_SET_ORDER);
				if ($compensations)
				{
					foreach ($compensation_infos as $compensation_info)
					{
						if($debug==1){
							error_log("LEVEL2.2 (compens_foreach): ".date("d.m.Y H:i:s")." ".$compensation_info[1]."\n", 3, $main_path."/".$debug_log_name."");
						}
						$victim_nick=$compensation_info[1];
						$accuser=$compensation_info[4];
						$case_sum=$compensation_info[2];
						$percent=$compensation_info[3];
						//include "compens_insert.php";
					}
				}
				$need_update=1;
//				print "<br>".$sql_command;
			}
			if ($need_update==1)
			{
				$sql_command.=" WHERE case_id=".$row["case_id"].";";//close command
				if($debug==1){
				//	print $sql_command."\n";
					error_log(date("d.m.Y H:i:s")." ".$sql_command."\n", 3, $main_path."/".$debug_log_name."");
				}
				mysql_query($sql_command) or die("Invalid query: ".mysql_error()."<br />".$sql_command."<br />");//do the command
			}
		}
	}
//		print "Page generation time: ".R_gentime()."s\n";
?>