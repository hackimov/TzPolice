#!/usr/bin/php -q
<?php
error_reporting(E_ALL);

function R_microtime() {
	list($usec, $sec) = explode(' ', microtime());
	return (double) $usec + (double) $sec;
};

function R_gentime() {
	global $R_gentime;
	if (isset ($R_gentime))
	return number_format (R_microtime() - $R_gentime, 3);
	$R_gentime = R_microtime();
};

	
function Police_search($searchcontent) {
	//echo ($searchcontent);
	$searchcontent = str_replace("Сегодня, в", date("d.m.y"), $searchcontent);
	$searchcontent = str_replace("Вчера, в", date("d.m.y", time()-86400), $searchcontent);
	//echo ($searchcontent);
	
	// рубим полученый контент страницы топика на посты
	preg_match_all("/<tr id=\"(\d+)\" class=\"msg\">.*?<br\/>Добавлено:<br\/>.*?((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d)).*?<br \/>.*?<td class=\"message_col\" id=\"q(\d+)\">(.*?)<\/td>.*?<\/tr>/is",$searchcontent,$postsmass,PREG_SET_ORDER);//get various message params
	
	$findrightcop = 0;

	foreach ($postsmass as $everypost) {
		
		if (preg_match("/<img src=\"\/i\/clans\/police.gif\" border=\"0\" width=\"28\" height=\"16\" ALT=\"\" class=\"imgh\" \/><span class=\"nickname\"><span onclick=\"sl\(\'\',\'\',\'<INFO>(.{1,16})<\/INFO>\'\)\".{10,2000}Добавлено:<br\/>.*?((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d)).*?<br \/>/is",$everypost[0],$result)==1) {
			/*echo ("<pre>");
			print_r($result);
			echo ("\r\n</pre>");*/
			$firstsymbol = substr(trim($everypost[9]),0,1);
			if ($firstsymbol != "*") {   // НК -- если сообщение копа начинается со звездочки, значит это неправильный коп
				$findrightcop = 1;
				break;
			} else {
				//echo "Обнаружен неправильный Коп!\n";
			}

		} else if (preg_match("/<img src=\"\/i\/clans\/Tribunal.gif\" border=\"0\" width=\"28\" height=\"16\" ALT=\"\" class=\"imgh\" \/><span class=\"nickname\"><span onclick=\"sl\(\'\',\'\',\'<INFO>(.{1,16})<\/INFO>\'\)\".*?Добавлено:<br\/>.{1,100}((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d)).*?<br \/>/is",$everypost[0],$result)==1) {
			$firstsymbol = substr(trim($everypost[9]),0,1);
			if ($firstsymbol != "*") {   // НК -- если сообщение копа начинается со звездочки, значит это неправильный коп
				$findrightcop = 1;
				break;
			} else {
				//echo "Обнаружен неправильный Коп!\n";
			}
		}
	}

	if ($findrightcop == 1) {
		return $result; // found police or tribunal officer in this topic with his timestamp of posting
	} else {
		return; //return with no value to test with isset()
	}
}

	
function Get_multipage_content($ch,$url,$from_where) {

	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,$url."&b=".($GLOBALS["current_page_".$from_where]+1)."&m=1");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/sites/police/www/interface/cron/cjr.ck");
	curl_setopt($ch,CURLOPT_COOKIE,"tzl=Terminal Police; path=/;");
	//curl_setopt($ch,CURLOPT_COOKIE,"tzses=".$ses."; path=/;");
	curl_setopt($ch,CURLOPT_COOKIE,"tzsrv=city1.timezero.ru; path=/;");
	curl_setopt($ch,CURLOPT_COOKIE,"tzlang=ru; path=/;");
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_HEADER,1);
		// Отключить ошибку "SSL certificate problem, verify that the CA cert is OK"
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	// Отключить ошибку "SSL: certificate subject name 'hostname.ru' does not match target host name '123.123.123.123'"
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

	$content="";
	$content=curl_exec($ch);
	$GLOBALS["current_page_".$from_where]++;
	return iconv("UTF-8","WINDOWS-1251",$content);
	//echo ($content);
	return $content;
}



//main program
//	include "../../_modules/mysql.php";
	include "/home/sites/police/www/_modules/mysql.php";
	foreach (glob("/home/sites/police/bot/*.ses") as $sesname)
	{
		$ses=basename($sesname,".ses");
//echo ($ses);
	}
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,"https://www.timezero.ru/cgi-bin/forum.pl?a=D4&l=Terminal%20Police&s=".$ses."&v=city1.timezero.ru&lang=ru");
	curl_setopt($ch,CURLOPT_HEADER,1);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "/home/sites/police/www/interface/cron/cjr.ck");
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



	print curl_exec($ch);
	curl_close($ch);
	$error = 0;
	$curl = array();
	$curl[1] = "D2";
	$curl[2] = "D3";
	$curl[3] = "D4";
	$curl[4] = "D";
	$is_moved_topic=0;
	foreach (array(1,2,3,4) as $n)
	{//22
		//R_gentime();
		// first read forum to get all new cases
		$GLOBALS["current_page_forum"]=0;//initialize
		do
		{
			$content=Get_multipage_content($ch,"https://www.timezero.ru/cgi-bin/forum.pl?m=1&a=".$curl[$n],"forum");
		$content = str_replace('class="press"','class="link_name_forum"',$content);
		$content = str_replace('class="adm"','class="link_name_forum"',$content);
		$content = str_replace("Сегодня, в", date("d.m.y"), $content);
		$content = str_replace("Вчера, в", date("d.m.y", time()-86400), $content);
//echo ($content);
		$cases_to_test=preg_match_all("/<tr>.*?\/i\/forum\/m([0135])\.gif.*?<a href=\"(.*?)\" class=\"link_name_forum\">(.*?)<\/a>.*?<td width=\"15\%\" align=\"left\" nowrap style=\"padding-left: 8px;\">.*?<a href=\"\/info.pl\?(.*?)\" target=\".*?<td width=\"10\%\" style=\"text-align: center;\">(\d+)<\/td>.*?<\/tr>/is",$content,$matches,PREG_SET_ORDER);
//if ($n==3)
//	{
//		echo ($content);
//		echo ("<pre>");
//		print_r ($matches);
//		echo ("</pre>");
//	}
			//error_log("***** $n ****\n", 3, "errlog.txt");
			//error_log("\n\n content:" . $content . "\n\n", 3, "errlog.txt");
			//error_log("\n\n cases_to_test:" . $cases_to_test . "\n\n", 3, "errlog.txt");
			//error_log("***** /$n ****\n", 3, "errlog.txt");
			//we have now array in $match, that closely resembles this set of rules:
			// $matches[][0] = all  <tr>.*?</tr>
			// $matches[][1] = topic is: 0 - unanswered, 1 - popular, 5 - investigated, 3 - closed
			// $matches[][2] = case url
			// $matches[][3] = case name
			// $matches[][4] = accuser
			// $matches[][5] = number of answers in this topic
			// everything else must be aquired from case url after case validation (no case with same url exists in DB)
			if ($cases_to_test) //we have some cases to test...
			{
//echo ("!!! GOT SOMETHING!!!<br>");
				foreach ($matches as $match)
				{
					$error = 0;
					$match[2]=str_replace("&m=1","",$match[2]);
					$result=mysql_query("SELECT * from case_main where case_url=\"".$match[2]."\";");
					$sql_command="";
					unset($police);//clear police variable. just in case...
					$i=0;
					if (!($result&&mysql_num_rows($result)))
					{//case with this url does not exist in database. Then add it to DB,
					 // but before load other info required for correct DB fill
						$GLOBALS["current_page_message"]=0;//initialize
						do
						{
							$is_moved_topic=0;
							$content=Get_multipage_content($ch,"https://www.timezero.ru/cgi-bin/".$match[2],"message");
				$content = str_replace("Сегодня, в", date("d.m.y"), $content);
				$content = str_replace("Вчера, в", date("d.m.y", time()-86400), $content);
//echo ($content);
							if ($current_page_message==1)
							{
								if ((preg_match("/class=adrd>Перемещено в форум/is",$content,$garbage))||(preg_match("/<th><b>Не найден топик, возможно он был удален, или перенесен в другой форум<\/b><\/th>/is",$content,$garbage)))
//								if ((preg_match("/class=adrd>Перемещено в форум/is",$content,$garbage))||(preg_match("/<font class=\"title2\">.{0,13}ОШИБКА.{0,13}<\/font>.{0,170}<font class=\"norm\">Топик был удален<\/font>/is",$content,$garbage)))
								{
									$is_moved_topic=1;//moved topic, skip
									break;
								}
								else
								{

					preg_match("/<br\/>Добавлено:<br\/>.*?((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d)).*?<br \/>.*?<td class=\"message_col\" id=\"q0\">(.*?)<\/td>/is",$content,$messageparams);//get various message params

									// special case type
/*					echo ("<pre>");
					print_r($messageparams);
					echo ("</pre>");
*/

									if ($n == 4) {
										$sql_command="INSERT INTO case_main SET case_type=\"13\", accuser=\"".$match[4]."\",date_case_begin=\"".$messageparams[4]."-".$messageparams[3]."-".$messageparams[2]." ".$messageparams[5].":".$messageparams[6]."\",case_url=\"".$match[2]."\",case_text=\"".mysql_real_escape_string($messageparams[7])."\",answers=".$match[5];
									} else {
										$sql_command="INSERT INTO case_main SET case_type=\"$n\", accuser=\"".$match[4]."\",date_case_begin=\"".$messageparams[4]."-".$messageparams[3]."-".$messageparams[2]." ".$messageparams[5].":".$messageparams[6]."\",case_url=\"".$match[2]."\",case_text=\"".mysql_real_escape_string($messageparams[7])."\",answers=".$match[5];
									}
								}
							}
							//Now search for police in this topic
							$police=Police_search($content);
							
							if (isset($police))
							{
								break;
							}
							$i++;
							if ($i>50) break;
						}
						while(!preg_match("/<span class=\"pager\"><strong>На страницу:<\/strong>.*?<font class=nb>\[".$GLOBALS["current_page_message"]."\]<\/font><\/span>/is",$content,$garbage));//not last page? continue then...
						if ($is_moved_topic)
						{
							$sql_command="";
							continue;//skip this
						}
						if ($match[1]==3) //closed topic
						{
							if ($current_page_message!=(int)($match[5]/30+1))
							{//load of endpage needed...
								$current_page_message=(int)($match[5]/30);
								$content=Get_multipage_content($ch,"https://www.timezero.ru/cgi-bin/".$match[2],"message");
							}
							preg_match("/<img src=\"\/i\/clans\/police.gif\" border=\"0\" width=\"28\" height=\"16\" ALT=\"\" class=\"imgh\" \/><span class=\"nickname\"><span onclick=\"sl\(\'\',\'\',\'<INFO>(.{1,16})<\/INFO>\'\)\".{10,2000}<br\/>Добавлено:<br\/>((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d))<br \/>.*?<td class=\"message_col\" id=\"q(\d+)\"><font class=adrd>Обсуждение закрыто<\/font>.*?<\/td>/is",$content,$messageparams);//get endmessage params
							$sql_command.=", date_case_closed=\"".$messageparams[5]."-".$messageparams[4]."-".$messageparams[3]." ".$messageparams[6].":".$messageparams[7]."\"";
							if ($n<3)
							{
								$closure_answer=$messageparam[8]-1;
								preg_match("/<td class=\"message_col\" id=\"q".$closure_answer."\">(.*?)<\/td>/is",$content,$final_answer);
//								error_log("start of final answer\n".$final_answer[1]."\nend of final answer ".$closure_answer." of http://www.timezero.ru/cgi-bin/".$match[2]."\n",3,"/home/sites/police/www/interface/cron/testsh.txt");
								preg_match("/\[(?:obep|or)=(\d+)\]/is",$final_answer[1],$closure_type);
								$sql_command.=", closure_type=".($closure_type[1]+1);
//check and update IP pattern
								$isIPs = preg_match_all("/\[IP=(.*?)\]/is",$final_answer[1],$IPs,PREG_SET_ORDER);
								if($isIPs) {
									foreach ($IPs as $IP) {
										$SQL = "INSERT INTO vzlom VALUES('', '".$IP[1]."', '".$match[2]."');";
										mysql_query($SQL) or die("Invalid query 1: ".$SQL."<br>".mysql_error());
									}
								}
//end				
								$compensations=preg_match_all("/\[v=(.*?);s=(\d+);p=(\d*);d=(.*?)\]/is",$final_answer[1],$compensation_infos,PREG_SET_ORDER);
								if ($compensations)
								{
									foreach ($compensation_infos as $compensation_info)
									{
										$victim_nick=$compensation_info[1];
										$accuser=$compensation_info[4];
										$case_sum=$compensation_info[2];
										$percent=$compensation_info[3];
										$case_url=$match[2];
										//include "compens_insert.php";
									}
								}
							}
							$police[1]=$messageparams[1];//case solver is the last one!
						}
						if (isset($police)) //police officer found in this topic!
						{//update sql string with police officer information and his posting timestamp
							$sql_command.=", investigator=\"".$police[1]."\",date_case_taken=\"".$police[5]."-".$police[4]."-".$police[3]." ".$police[6].":".$police[7]."\";";
						}
						else
						{
							$sql_command.=";";//finish then
						}
					}//if (!($result&&mysql_num_rows($result)))
					else
					{//search for police officer in this particular base record
						$row=mysql_fetch_assoc($result);
						if ($match[5]>$row["answers"])//new answers on topic
						{
							$sql_command="UPDATE case_main set answers=".$match[5];
							 $is_moved_topic=0;
							if ($row["investigator"]==NULL) //No police officer in base
							{
								 $current_page_message=0;//initialize
								do
								{
									$content=Get_multipage_content($ch,"https://www.timezero.ru/cgi-bin/".$match[2],"message");
									if ((preg_match("/class=adrd>Перемещено в форум/is",$content,$garbage))||(preg_match("/<th><b>Не найден топик, возможно он был удален, или перенесен в другой форум<\/b><\/th>/is",$content,$garbage)))
									{
										$is_moved_topic=1;//moved topic, skip
										break;
									}
									$police=Police_search($content);
									
									if (isset($police))
									{
										break;
									}
									$i++;
									if ($i>50) break;
								}
								while(!preg_match("/<span class=\"pager\"><strong>На страницу:<\/strong>.*?<font class=nb>\[".$GLOBALS["current_page_message"]."\]<\/font><\/span>/is",$content,$garbage));//not last page? continue then...
							}
							if ($is_moved_topic)
							{
								$is_moved_topic=0;//delete record
								error_log(date("d.m.Y H:i:s")." found old moved topic ".$match[2].", killing!\n",3,"errlog.txt");
								$sql_command="DELETE FROM case_main";
							}
							else
							{
								if ($match[1]==3) //closed topic
								{
									$current_page_message=0;
									$content=Get_multipage_content($ch,"https://www.timezero.ru/cgi-bin/".$match[2],"message");
									if ((preg_match("/class=adrd>¦хЁхьх•хэю т ЇюЁєь/is",$content,$garbage))||(preg_match("/<th><b>=х эрщфхэ Єюяшъ, тючьюцэю юэ сvы єфрыхэ, шыш яхЁхэхёхэ т фЁєующ ЇюЁєь<\/b><\/th>/is",$content,$garbage)))
									{//test for moved topic
										$is_moved_topic=0;//delete record
										$sql_command="DELETE FROM case_main";
									}
									else
									{
										if ($current_page_message!=(int)($match[5]/30+1))
										{//load of endpage needed...
											$current_page_message=(int)($match[5]/30);
											$content=Get_multipage_content($ch,"https://www.timezero.ru/cgi-bin/".$match[2]."&ses=".$ses,"message");
										}
/*										
echo ("<pre>");
print_r($messageparams);
echo ("</pre>");
*/
										if (preg_match("/<img src=\"\/i\/clans\/police.gif\" border=\"0\" width=\"28\" height=\"16\" ALT=\"\" class=\"imgh\" \/><span class=\"nickname\"><span onclick=\"sl\(\'\',\'\',\'<INFO>(.{1,16})<\/INFO>\'\)\".{10,2000}<br\/>Добавлено:<br\/>((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d))<br \/>.*?<td class=\"message_col\" id=\"q(\d+)\"><font class=adrd>Обсуждение закрыто<\/font>.*?<\/td>/is",$content,$messageparams))//get endmessage params
										{
					
											$sql_command.=", date_case_closed=\"".$messageparams[5]."-".$messageparams[4]."-".$messageparams[3]." ".$messageparams[6].":".$messageparams[7]."\", investigator=\"".$messageparams[1]."\"";
											if ($n<3)
											{
												$closure_answer=$messageparams[8]-1;
						preg_match("/<td class=\"message_col\" id=\"q".$closure_answer."\">(.*?)<\/td>/is",$content,$final_answer);
												preg_match("/\[(?:obep|or)=(\d+)\]/is",$final_answer[1],$closure_type);
												$sql_command.=", closure_type=".($closure_type[1]+1);
	//											error_log("start of final answer\n".$final_answer[1]."\nend of final answer ".$closure_answer." of http://www.timezero.ru/cgi-bin/".$match[2]."\n",3,"/home/sites/police/www/interface/cron/testsh.txt");
//check and update IP pattern
												$isIPs = preg_match_all("/\[IP=(.*?)\]/is",$final_answer[1],$IPs,PREG_SET_ORDER);
												if($isIPs) {
													foreach ($IPs as $IP) {
														$SQL = "INSERT INTO vzlom VALUES('', '".$IP[1]."', '".$match[2]."');";
														mysql_query($SQL) or die("Invalid query : ".$SQL."<br>".mysql_error());
													}
												}
//end				
												$compensations=preg_match_all("/\[v=(.*?);s=(\d+);p=(\d*);d=(.*?)\]/is",$final_answer[1],$compensation_infos,PREG_SET_ORDER);
												if ($compensations)
												{
													foreach ($compensation_infos as $compensation_info)
													{
														$victim_nick=$compensation_info[1];
														$accuser=$compensation_info[4];
														$case_sum=$compensation_info[2];
														$percent=$compensation_info[3];
														$case_url=$match[2];
														//include "compens_insert.php";
													}
												}
											}
										}
										else{
											error_log(date("d.m.Y H:i:s")." Unable to match closed topic... Strange! ".$match[2]."\n",3,"errlog.txt");
											//error_log("---Begin saved content\n",3,"errlog.txt");
											//error_log($content,3,"errlog.txt");
											//error_log("---End saved content\n",3,"errlog.txt");
											$error = 1;
										}
									}
								}//if ($match[1]==3)
								if (isset($police))
								{//update this case with police officer information and his posting timestamp
									if ($match[1]!=3)
									{
										$sql_command.=", investigator=\"".$police[1]."\"";
									}
									$sql_command.=",date_case_taken=\"".$police[5]."-".$police[4]."-".$police[3]." ".$police[6].":".$police[7]."\"";
								}
							}
								//close command;
							$sql_command.=" WHERE case_id=".$row["case_id"].";";
						}//if ($match[5]>$row["answers"])
					}//else
					if (($sql_command!="")&&(!$error)&&(!$is_moved_topic))
					{
						//print $sql_command."\n";
						mysql_query($sql_command) or die("Invalid query 3: ".$sql_command."<br>".mysql_error());//do the command
						//print mysql_affected_rows()." rows affected...<br>";
					}//if ($sql_command!="")
				}//foreach ($matches as $match)
			}//if ($cases_to_test)
		} while ($current_page_forum<=3);//Only 3 pages of forum are fetched
//		print "Page generation time: ".R_gentime()."s\n";
	}//	foreach (array(1,2) as $n) //22
?>