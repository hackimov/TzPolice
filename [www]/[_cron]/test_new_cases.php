<HTML><HEAD>
<TITLE>Делопроизводство.</TITLE> 
<meta content="text/html; charset=windows-1251" http-equiv="Content-type">
</HEAD>
<BODY>
<?php
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
	function Police_search($searchcontent)
	{
		if (preg_match("/<TR>.*?<IMG SRC=\"\/i\/clans\/police.gif\" BORDER=0 WIDTH=28 HEIGHT=16 ALT=\"\"><span onclick=\"sl\('','','<INFO>(.*?)<\/INFO>.*?<font class=\"sm\">Добавлено:<BR>((\d\d)\.(\d\d).(\d\d) (\d\d):(\d\d))<\/font><BR>/is",$searchcontent,$result)==1)
		{
			return $result;//found police officer in this topic with his timestamp of posting
		}
		else
		{
			if (preg_match("/<TR>.*?<IMG SRC=\"\/i\/clans\/Tribunal.gif\" BORDER=0 WIDTH=28 HEIGHT=16 ALT=\"\"><span onclick=\"sl\('','','<INFO>(.*?)<\/INFO>.*?<font class=\"sm\">Добавлено:<BR>((\d\d)\.(\d\d).(\d\d) (\d\d):(\d\d))<\/font><BR>/is",$searchcontent,$result)==1)
			{
				return $result;//found tribunal in this topic with his timestamp of posting
			}
			else
			{
				return;//return with no value to test with isset()
			}
		}
	}
	function Get_multipage_content($url,$from_where)
	{
		$fp=@fopen($url."&b=".($GLOBALS["current_page_".$from_where]+1),"r");//get next page
		if (!$fp)
		{//error somewhere... 500 or something else on TZ server. report it and die
			echo $url."&b=".($GLOBALS["current_page_".$from_where]+1);
			error_log(date("d.m.Y H:i:s")." Unable to retrieve ".$url."&b=".($GLOBALS["current_page_".$from_where]+1)." due to: ".$php_errormsg,3,"errlog.txt");
			die();
		}
		$content="";
		while (!feof($fp))
		{
			$content.=fread($fp,1000000);
		}
		fclose($fp);
		$GLOBALS["current_page_".$from_where]++;
		return $content;
	}
//main program
	include "/home/sites/police/www/_modules/mysql.php";
	$GLOBALS["current_page_forum"]=0;//initialize
	$test_url = "http://www.timezero.ru/cgi-bin/forum.pl?a=D3&c=57183624";
	$result=mysql_query("SELECT * from case_main where case_url=\"".$test_url."\";");
	$sql_command="";
	if (!($result&&mysql_num_rows($result)))
	{//case with this url does not exist in database. Then add it to DB,
	 // but before load other info required for correct DB fill
		$GLOBALS["current_page_message"]=0;//initialize
		do
		{
			$content=Get_multipage_content("http://www.timezero.ru/cgi-bin/forum.pl?a=D3&c=57183624","message");
			if ($current_page_message==1)
			{
				preg_match("/<font class=\"sm\">Добавлено:<BR>((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d))<\/font><BR>.*?<td valign=top id=\"q0\">(.*?)<\/td>/is",$content,$messageparams);//get various message params
				$sql_command="INSERT into case_main set case_type=\"$n\", accuser=\"".$match[4]."\",date_case_begin=\"".$messageparams[4]."-".$messageparams[3]."-".$messageparams[2]." ".$messageparams[5].":".$messageparams[6]."\",case_url=\"".$match[2]."\",case_text=\"".mysql_real_escape_string($messageparams[7])."\",answers=".$match[5];
			}
			//Now search for police in this topic
			$police=Police_search($content);
			if (isset($police))
			{
				break;
			}
		}while(!preg_match("/<TD align=right class=\"norm\">На страницу.*?\[".$current_page_message."\]<\/font><\/TD>/is",$content,$garbage));//not last page? continue then...
		if ($match[1]==3) //closed topic
		{
			if ($current_page_message!=(int)($match[5]/20+1))
			{//load of endpage needed...
				$current_page_message=(int)($match[5]/20);
				$content=Get_multipage_content("http://www.timezero.ru/cgi-bin/".$match[2],"message");
			}
			preg_match("/style=\"cursor:hand\" onclick=\"sl\(\'(.{1,12})\',\'q".$match[5]."\'\)\"><BR>.?<font class=\"sm\">Добавлено:<BR>((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d))<\/font><BR>.{0,4}<\/td><td valign=top id=\"q".$match[5]."\"><font class=adrd>Обсуждение закрыто<\/font>.*?<\/td>/is",$content,$messageparams);//get endmessage params
			$sql_command.=", date_case_closed=\"".$messageparams[5]."-".$messageparams[4]."-".$messageparams[3]." ".$messageparams[6].":".$messageparams[7]."\"";
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
			if ($row["investigator"]==NULL) //No police officer in base
			{
			 	$current_page_message=0;//initialize
				do
				{
					$content=Get_multipage_content("http://www.timezero.ru/cgi-bin/forum.pl?a=D3&c=57183624","message");
					$police=Police_search($content);
					if (isset($police))
					{
						break;
					}
				}
				while(!preg_match("/<TD align=right class=\"norm\">На страницу.*?<font class=nb>\[".$current_page_message."\]<\/font><\/TD>/is",$content,$garbage));//not last page? continue then...
			}
			if ($match[1]==3) //closed topic
			{
				if ($current_page_message!=(int)($match[5]/20+1))
				{//load of endpage needed...
					$current_page_message=(int)($match[5]/20);
					$content=Get_multipage_content("http://www.timezero.ru/cgi-bin/".$match[2],"message");
				}
				if (preg_match("/style=\"cursor:hand\" onclick=\"sl\(\'(.{1,12})\',\'q".$match[5]."\'\)\"><BR>.?<font class=\"sm\">Добавлено:<BR>((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d))<\/font><BR>.{0,4}<\/td><td valign=top id=\"q".$match[5]."\"><font class=adrd>Обсуждение закрыто<\/font>.*?<\/td>/is",$content,$messageparams))//get endmessage params
				$sql_command.=", date_case_closed=\"".$messageparams[5]."-".$messageparams[4]."-".$messageparams[3]." ".$messageparams[6].":".$messageparams[7]."\", investigator=\"".$messageparams[1]."\"";
				else {
					echo "http://www.timezero.ru/cgi-bin/".$match[2]."\n";
					$error = 1;
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
			//close command;
			$sql_command.=" WHERE case_id=".$row["case_id"].";";
		}//if ($match[5]>$row["answers"])
	}//else 
	if ($sql_command!=""&&!($error))
	{
		mysql_query($sql_command) or die("Invalid query: ".mysql_error());//do the command
		//print mysql_affected_rows()." rows affected...<br>";
	}//if ($sql_command!="")
	print "Page generation time: ".R_gentime()."s\n";
?>
</BODY>