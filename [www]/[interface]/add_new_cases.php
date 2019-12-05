#!/usr/bin/php -q
<?php
error_reporting(E_ALL);
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
        if (preg_match("/<TR>.*?<IMG SRC=\"\/i\/clans\/police.gif\" BORDER=0 WIDTH=28 HEIGHT=16 ALT=\"\"><span onclick=\"sl\('','','<INFO>(.{1,16})<\/INFO>'\)\".{100,500}<font class=\"sm\">Добавлено:<BR>((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d))<\/font><BR>/is",$searchcontent,$result)==1)
        {
            return $result;//found police officer in this topic with his timestamp of posting
        }
        else
        {
            if (preg_match("/<TR>.*?<IMG SRC=\"\/i\/clans\/Tribunal.gif\" BORDER=0 WIDTH=28 HEIGHT=16 ALT=\"\"><span onclick=\"sl\('','','<INFO>(.{1,16})<\/INFO>'\)\".{100,500}<font class=\"sm\">Добавлено:<BR>((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d))<\/font><BR>/is",$searchcontent,$result)==1)
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
        $fp=fopen($url."&b=".($GLOBALS["current_page_".$from_where]+1),"r");//get next page
        if (!$fp)
        {//error somewhere... 500 or something else on TZ server. report it and die
//            error_log(date("d.m.Y H:i:s")." Unable to retrieve ".$url."&b=".($GLOBALS["current_page_".$from_where]+1)." due to: ".$php_errormsg,3,"errlog.txt");
            die(date("d.m.Y H:i:s").": ".$php_errormsg);
        }
        $content="";
        while (!feof($fp))
        {
            $content.=fread($fp,1000000);
        }
        fclose($fp);
        $GLOBALS["current_page_".$from_where]++;
        return iconv("UTF-8","WINDOWS-1251",$content);
    }
//main program
//    include "../../_modules/mysql.php";
    include "/home/sites/police/www/_modules/mysql.php";
    $error = 0;
    $curl = array();
    $curl[1] = "D2";
    $curl[2] = "D3";
    $curl[3] = "D4";
    $curl[4] = "D";
    $is_moved_topic=0;
    foreach (array(1,2,3,4) as $n)
    {//22
        R_gentime();
        // first read forum to get all new cases
        $GLOBALS["current_page_forum"]=0;//initialize
        do
        {
            $content=Get_multipage_content("http://www.timezero.ru/cgi-bin/forum.pl?a=".$curl[$n],"forum");
            $cases_to_test=preg_match_all("/<tr class=\"gr\">.*?\/i\/forum\/m([0135])\.gif.*?<a href=\"(.*?)\".*?>(.*?)<\/a>.*?<td nowrap>.*?onclick=\"Inf\(\'(.*?)\'.*?<TD>(\d+)<\/TD>.*?<\/tr>/is",$content,$matches,PREG_SET_ORDER);
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
                foreach ($matches as $match)
                {
                    //print "<br>match(1)=".$match[1]."<br>";
                    //print "match(2)=".$match[2]."<br>";
                    //print "match(3)=".$match[3]."<br>";
                    //print "match(4)=".$match[4]."<br>";
                    //print "match(5)=".$match[5]."<br>";
                    $error = 0;
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
                            $content=Get_multipage_content("http://www.timezero.ru/cgi-bin/".$match[2],"message");
                            if ($current_page_message==1)
                            {
                                if ((preg_match("/class=adrd>Перемещено в форум/is",$content,$garbage))||(preg_match("/<h1>.{0,13}ОШИБКА.{0,13}<\/h1>.{0,170}<font class=\"norm\">(Не найден т)|(Т)опик(, возможно он)? был удален(, или перенесен в другой форум)?<\/font>/is",$content,$garbage)))
//                                if ((preg_match("/class=adrd>Перемещено в форум/is",$content,$garbage))||(preg_match("/<font class=\"title2\">.{0,13}ОШИБКА.{0,13}<\/font>.{0,170}<font class=\"norm\">Топик был удален<\/font>/is",$content,$garbage)))
                                {
                                    $is_moved_topic=1;//moved topic, skip
                                    break;
                                }
                                else
                                {
                                    preg_match("/<font class=\"sm\">Добавлено:<BR>((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d))<\/font><BR>.*?<td class=\"msg-body\" valign=top id=\"q0\">(.*?)<\/td>/is",$content,$messageparams);//get various message params
                                    // special case type
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
                        }while(!preg_match("/<TD width=\"100%\" align=right>На страницу.*?\[".$GLOBALS["current_page_message"]."\]<\/font><\/TD>/is",$content,$garbage));//not last page? continue then...
                        if ($is_moved_topic)
                        {
                            $sql_command="";
                            continue;//skip this
                        }
                        if ($match[1]==3) //closed topic
                        {
                            if ($current_page_message!=(int)($match[5]/20+1))
                            {//load of endpage needed...
                                $current_page_message=(int)($match[5]/20);
                                $content=Get_multipage_content("http://www.timezero.ru/cgi-bin/".$match[2],"message");
                            }
                            preg_match("/style=\"cursor:hand\" onclick=\"sl\(\'(.{1,16})\',\'q".$match[5]."\'\)\"><BR>.{0,4}<font class=\"sm\">Добавлено:<BR>((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d))<\/font><BR>.{0,6}<\/th><td class=\"msg-body\" valign=top id=\"q".$match[5]."\"><font class=adrd>Обсуждение закрыто<\/font>.*?<\/td>/is",$content,$messageparams);//get endmessage params
                            $sql_command.=", date_case_closed=\"".$messageparams[5]."-".$messageparams[4]."-".$messageparams[3]." ".$messageparams[6].":".$messageparams[7]."\"";
                            if ($n<3)
                            {
                                $closure_answer=$match[5]-1;
                                preg_match("/<TD class=\"msg-body\" valign=top id=\"q".$closure_answer."\">(.*?)<\/TD><\/TR>/is",$content,$final_answer);
//                                error_log("start of final answer\n".$final_answer[1]."\nend of final answer ".$closure_answer." of http://www.timezero.ru/cgi-bin/".$match[2]."\n",3,"/home/sites/police/www/interface/cron/testsh.txt");
                                preg_match("/\[(?:obep|or)=(\d+)\]/is",$final_answer[1],$closure_type);
                                $sql_command.=", closure_type=".($closure_type[1]+1);
//check and update IP pattern
                                $isIPs = preg_match_all("/\[IP=(.*?)\]/is",$final_answer[1],$IPs,PREG_SET_ORDER);
                                if($isIPs) {
                                    foreach ($IPs as $IP) {
                                        $SQL = "INSERT INTO vzlom VALUES('', '".$IP[1]."', '".$match[2]."');";
                                        mysql_query($SQL) or die("Invalid query: ".mysql_error());
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
                                    $content=Get_multipage_content("http://www.timezero.ru/cgi-bin/".$match[2],"message");
                                    if ((preg_match("/class=adrd>Перемещено в форум/is",$content,$garbage))||(preg_match("/<h1>.{0,13}ОШИБКА.{0,13}<\/h1>.{0,170}<font class=\"norm\">(Не найден т)|(Т)опик(, возможно он)? был удален(, или перенесен в другой форум)?<\/font>/is",$content,$garbage)))
//                                    if ((preg_match("/class=adrd>Перемещено в форум/is",$content,$garbage))||(preg_match("/<font class=\"title2\">.{0,13}ОШИБКА.{0,13}<\/font>.{0,170}<font class=\"norm\">Топик был удален<\/font>/is",$content,$garbage)))
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
                                while(!preg_match("/<TD width=\"100%\" align=right>На страницу.*?<font class=nb>\[".$GLOBALS["current_page_message"]."\]<\/font><\/TD>/is",$content,$garbage));//not last page? continue then...
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
                                    $content=Get_multipage_content("http://www.timezero.ru/cgi-bin/".$match[2],"message");
                                    if ((preg_match("/class=adrd>Перемещено в форум/is",$content,$garbage))||(preg_match("/<h1>.{0,13}ОШИБКА.{0,13}<h1>.{0,170}<font class=\"norm\">(Не найден т)|(Т)опик(, возможно он)? был удален(, или перенесен в другой форум)?<\/font>/is",$content,$garbage)))
//                                    if ((preg_match("/class=adrd>Перемещено в форум/is",$content,$garbage))||(preg_match("/<font class=\"title2\">.{0,13}ОШИБКА.{0,13}<\/font>.{0,170}<font class=\"norm\">Топик был удален<\/font>/is",$content,$garbage)))
                                    {//test for moved topic
                                        $is_moved_topic=0;//delete record
                                        $sql_command="DELETE FROM case_main";
                                    }
                                    else
                                    {
                                        if ($current_page_message!=(int)($match[5]/20+1))
                                        {//load of endpage needed...
                                            $current_page_message=(int)($match[5]/20);
                                            $content=Get_multipage_content("http://www.timezero.ru/cgi-bin/".$match[2],"message");
                                        }
                                        if (preg_match("/style=\"cursor:hand\" onclick=\"sl\(\'(.{1,16})\',\'q".$match[5]."\'\)\"><BR>.{0,4}<font class=\"sm\">Добавлено:<BR>((\d\d)\.(\d\d)\.(\d\d) (\d\d):(\d\d))<\/font><BR>.{0,4}<\/th><td class=\"msg-body\" valign=top id=\"q".$match[5]."\"><font class=adrd>Обсуждение закрыто<\/font>.*?<\/td>/is",$content,$messageparams))//get endmessage params
                                        {
                                            $sql_command.=", date_case_closed=\"".$messageparams[5]."-".$messageparams[4]."-".$messageparams[3]." ".$messageparams[6].":".$messageparams[7]."\", investigator=\"".$messageparams[1]."\"";
                                            if ($n<3)
                                            {
                                                $closure_answer=$match[5]-1;
                                                preg_match("/<TD class=\"msg-body\" valign=top id=\"q".$closure_answer."\">(.*?)<\/TD><\/TR>/is",$content,$final_answer);
                                                preg_match("/\[(?:obep|or)=(\d+)\]/is",$final_answer[1],$closure_type);
                                                $sql_command.=", closure_type=".($closure_type[1]+1);
    //                                            error_log("start of final answer\n".$final_answer[1]."\nend of final answer ".$closure_answer." of http://www.timezero.ru/cgi-bin/".$match[2]."\n",3,"/home/sites/police/www/interface/cron/testsh.txt");
//check and update IP pattern
                                                $isIPs = preg_match_all("/\[IP=(.*?)\]/is",$final_answer[1],$IPs,PREG_SET_ORDER);
                                                if($isIPs) {
                                                    foreach ($IPs as $IP) {
                                                        $SQL = "INSERT INTO vzlom VALUES('', '".$IP[1]."', '".$match[2]."');";
                                                        mysql_query($SQL) or die("Invalid query: ".mysql_error());
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
                        mysql_query($sql_command) or die("Invalid query: ".mysql_error());//do the command
                        //print mysql_affected_rows()." rows affected...<br>";
                    }//if ($sql_command!="")
                }//foreach ($matches as $match)
            }//if ($cases_to_test)
        } while ($current_page_forum<=3);//Only 3 pages of forum are fetched
//        print "Page generation time: ".R_gentime()."s\n";
    }//    foreach (array(1,2) as $n) //22
?>