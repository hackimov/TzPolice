<?php
require_once("/home/sites/police/www/_modules/functions.php");
require_once("/home/sites/police/www/_modules/auth.php");
error_reporting(E_ERROR | E_WARNING | E_PARSE);
include('/home/sites/police/www/otherhistory/lang_ru.php');
setlocale(LC_CTYPE,"ru_RU.CP1251");
function cmp($a,$b)
{
	if (filemtime($a)==filemtime($b))
	{
		return 0;
	}
	return (filemtime($a)>filemtime($b))?-1:1;
}
function list_histories($admin_access=0,$dept_list)
{
	chdir('/home/sites/police/www/otherhistory');
	if ($admin_access==1)
	{
		$listhistories=glob("\[{".$dept_list."}\]*.txt",GLOB_BRACE);
	}
	elseif($admin_access==0)
	{
		$listhistories=glob("\[".AuthUserName."\]*.txt");
	}
	else
	{
		$listhistories=glob("*.txt");
	}
	if ($listhistories)
	{
		usort($listhistories,"cmp");
		$userfiles=array();
		foreach($listhistories as $logno=>$hist)
		{
			preg_match("/\[(.+)\] \[(.+)\] (\d+\.\d+\.\d+) (\d+\.\d+\.\d+)\.txt/",$hist,$matches);
			if (!array_key_exists($matches[1],$userfiles))
			{
				$userfiles[$matches[1]]=array();
			}
			array_push($userfiles[$matches[1]],$hist);
		}
		if ($userfiles)
		{
			arsort($userfiles,SORT_STRING);
			if (array_key_exists(AuthUserName,$userfiles))
			{
				foreach($userfiles[AuthUserName] as $logno=>$logname)
				{
					preg_match("/\[(.+)\] \[(.+)\] (\d+\.\d+\.\d+) (\d+\.\d+\.\d+)\.txt/",$logname,$matches);
					print "[http://www.tzpolice.ru/otherhistory/download.php?logno=".AuthUserName."|".$logno."] [".AuthUserName."] [".$matches[2]." || ".$matches[3]." || ".$matches[4]."]\r\n";
				}
			}
        	foreach ($userfiles as $username=>$logfiles)
			{
				if ($username==AuthUserName || strlen($username)<2)
				{
					continue;
				}
				foreach($logfiles as $logno=>$logname)
				{
					preg_match("/\[(.+)\] \[(.+)\] (\d+\.\d+\.\d+) (\d+\.\d+\.\d+)\.txt/",$logname,$matches);
					print "[http://www.tzpolice.ru/otherhistory/download.php?logno=".$username."|".$logno."] [".$username."] [".$matches[2]." || ".$matches[3]." || ".$matches[4]."]\r\n";
				}
			}
		}
	}
	else
	{
		if ($admin_access==0){
			print "ERROR_NO_ORDER";
		}
		elseif ($admin_access==1)
		{
			print "ERROR_NO_DEPT_LOGS";
		}
		else
		{
			print "ERROR_NO_LOGS";
		}
	}
}

	if(AuthUserClan=='Tribunal' || AuthUserClan=='police' || AuthUserClan=='Police Academy' || AuthUserGroup==100) {
		$sSQL = "SELECT `dept`, `chief` FROM `sd_cops` WHERE `name` = '".AuthUserName."' LIMIT 1;";
		$result = mysql_query($sSQL);
		$row = mysql_fetch_assoc($result);
		$dept_id = $row['dept'];
		$dept_list="";
		if (AuthUserGroup == 100)
		{
			$otherhistory_access=2;
		}
		else
		{
			if (!in_array($dept_id,array(12,13,14,15,57,71,27)) && AuthUserGroup != 100){
				print "ERROR_ACCESS_DENIED";
				exit;
			}
			$otherhistory_access=0;
			$chief = $row['chief'];
			if ($chief==1){
				$otherhistory_access=1;
				$sSQL = 'SELECT name FROM sd_cops WHERE dept='.$dept_id.";";
				$result= mysql_query($sSQL);
				while ($row=mysql_fetch_assoc($result))
				{
					if ($dept_list!=""){$dept_list.=",";};
					$dept_list.=$row['name'];
				}
			}
		}
	list_histories($otherhistory_access,$dept_list);
	}
	else
	{
			print "ERROR_ACCESS_DENIED";
	}
?>