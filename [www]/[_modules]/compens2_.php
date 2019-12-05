<H1>Компенсации пострадавшим от взлома и мошенничества</H1>

<?
if ($_REQUEST['me'] == 1)
	{
    	$me = 1;
    }
else
	{
    	$me = 0;
    }
$cur_user = "";
if (isset($_REQUEST['sec'])) {$sec=$_REQUEST['sec'];}
else {$sec='queue';}
if($_REQUEST['p']>0) $p=$_REQUEST['p'];
else $p=1;
$caseonpage = "30";
if ($sec=="payed_out"){
$query = "SELECT `id` FROM `compens_tmp` WHERE `status` = '2'";
}
else {
$query = "SELECT `id` FROM `compens_tmp` WHERE `status` < '2'";
}
$r = mysql_query($query);
$quan = mysql_num_rows($r);
//echo ($quan."<br>");
$pages = ceil($quan/$caseonpage);
//echo ($pages."<br>");
$LimitParam=$p*$caseonpage-$caseonpage;
$q_limit = " LIMIT ".$LimitParam.",".$caseonpage;

error_reporting(0);

if ($_REQUEST['sessid'] < 1 && strlen($_REQUEST['user']) < 3 && strlen($_REQUEST['city']) < 3)

	{

?>

<OBJECT id="tz" codeBase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" height="1" width="1" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><PARAM NAME="movie" VALUE="http://tzpolice.ru/_imgs/auth3.swf"><PARAM NAME="wmode" VALUE="transparent">

<embed src="http://tzpolice.ru/_imgs/auth3.swf" wmode="transparent" width="1" height="1" swLiveConnect="true" id="tz" name="tz" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />

</OBJECT>

<script language="JavaScript" type="text/javascript">

<!--

var timeout = null;
var opera = Boolean(window["opera"]);
if (opera)
	{
    	alert("Извините, авторизация с использованием браузера Opera невозможна\nВоспользуйтесь Internet Explorer или Firefox");
    }

function tz_DoFSCommand(command, args) {

	var tmp = args.split("\t");

	if (command == "OK")

    	{

            var pers_nick = '' + tmp[0];

			var pers_sid = '' + tmp[1];

			var pers_city = '' + tmp[2];

            if (confirm("Первый этап авторизации успешно завершен!\nДля продолжения работы со скриптом как '"+pers_nick+"' нажмите ОК.\nВы можете отказаться от авторизации, но при этом\nне будете иметь возможности редактировать данные,\nотносящиеся к Вашим компенсациям."))

            	{

		            var url = 'http://<?=$_SERVER['HTTP_HOST']?>/?act=compens2&sessid=' + pers_sid + '&user=' + pers_nick + '&city=' + pers_city;

        		    top.location = url;

                }

        }

    else

    	{

        	            alert ("Ошибка авторизации!\nУбедитесь, что Вы авторизованы в ТЗ.");

        }

}

if (navigator.appName.indexOf("Microsoft") != -1) {// Hook for Internet Explorer.

	document.write('<script language=\"VBScript\"\>\n');

	document.write('On Error Resume Next\n');

	document.write('Sub tz_FSCommand(ByVal command, ByVal args)\n');

	document.write('	Call tz_DoFSCommand(command, args)\n');

	document.write('End Sub\n');

	document.write('</script\>\n');

}

</script>



<?

$plink = "act=compens2&sec=".$sec."&user=".$_REQUEST['user']."&sessid=".$_REQUEST['sessid']."&city=".$_REQUEST['city'];

if ($me == 1) {$plink .= "&me=1";}

$sq = "SELECT `id` FROM `compens_tmp` WHERE `status` < '2'";

$sr = mysql_query($sq);

$queue_length = mysql_num_rows($sr); //Заявок в очереди

$sq = "SELECT SUM(`sum`) as `summa` FROM `compens_tmp` WHERE `status` < '2'";

$sr = mysql_query($sq);

$srs = mysql_fetch_array($sr);

$queue_sum = $srs['summa']; //Приблизительная сумма выплат в очереди

$sq = "SELECT `id` FROM `compens_tmp` WHERE `status` = '2'";

$sr = mysql_query($sq);

$payed_out_length = mysql_num_rows($sr); //Выплачено заявок

$sq = "SELECT SUM(`sum`) as `summa` FROM `compens_tmp` WHERE `status` = '2'";

$sr = mysql_query($sq);

$srs = mysql_fetch_array($sr);

$payed_out_sum = $srs['summa']; //Приблизительная сумма совершенных выплат

?>

<script language="JavaScript" type="text/javascript">

function line1(count,sum,nick,flink,linkname,cell)

	{

		var txt = "";

		txt = '<tr bgcolor="#E8D395"><td nowrap>'+count+'</td><td>'+nick+'</td><td align="center"><a href="http://www.timezero.ru/cgi-bin/forum.pl?'+flink+'" target="_blank">'+linkname+'</a></td><td align="center">'+sum+'</td><td align="center">'+cell+'</td></tr>';

		return txt;

	}

</script>

<br><a href="?act=compens2&sec=payed_out&me=<?=$me?>&user=<?=$_REQUEST['user']?>&sessid=<?=$_REQUEST['sessid']?>&city=<?=$_REQUEST['city']?>">Выплаченные компенсации</a><br>

<a href="?act=compens2&sec=queue&me=<?=$me?>&user=<?=$_REQUEST['user']?>&sessid=<?=$_REQUEST['sessid']?>&city=<?=$_REQUEST['city']?>">Очередь компенсаций</a>

<hr>

Компенсаций в очереди: <b><?=$queue_length?></b> на сумму <b><?=$queue_sum?></b> м.м.<br>

<?if ($payed_out_sum > 0) {?>

Компенсаций выплачено: <b><?=$payed_out_length?></b> на сумму <b><?=$payed_out_sum?></b> м.м.<br>

<?}?>

<hr>

<br><p align=right>Страницы: <b>

<?=ShowPages($p,$pages,10,$plink);?>

</b></p>

<table width="100%"  border="0" cellspacing="3" cellpadding="3" align="center">

	<tr bgcolor="#948559">

		<td width="1" nowrap align="center"><b>№</b></td>

		<td align="center"><b>Потерпевший</b></td>



		<td align="center"><b>Дата</b></td>



	    <td align="center" width="1"><b>Сумма</b></td>



        <td align="center" width="1"><b>Ячейка</b></td>



	</tr>



<script language="JavaScript" type="text/javascript">



var show="";



<?



if ($sec=="payed_out"){
$query = "SELECT * FROM `compens_tmp` WHERE `status`='2'".$q_cond." ORDER BY `victim`".$q_limit;
}
else  $query = "SELECT * FROM `compens_tmp` WHERE `status`<'2'".$q_cond." ORDER BY `victim`".$q_limit;



$r = mysql_query($query) or die(mysql_error());



$count = 1+($caseonpage*$p)-$caseonpage;



while ($row = mysql_fetch_array($r))



	{



                $usrfull = ParseNews3($row['vfull']);



                $usrfull = str_replace("'", "\"", $usrfull);



				$link = str_replace("'", "\'", $row['link']);



				$str = "show += line1(".$count.",".$row['sum'].",'".$usrfull."','".$link."','".date('d.m.Y',$row['date1'])."','".$row['cell']."');



";



                echo ($str);



        $count ++;



    }



?>



document.write(show);



</script>



</table>



<br><p align=right>Страницы: <b>



<?=ShowPages($p,$pages,10,$plink);?>



</b></p>



<?



}



else



{



$caseonpage = "30";



$cur_user = "";



if (isset($_REQUEST['sec'])) {$sec=$_REQUEST['sec'];}



else {$sec='queue';}



if($_REQUEST['p']>0) $p=$_REQUEST['p'];



else $p=1;



$query = "SELECT `id` FROM `compens_tmp` WHERE `status` < '2'";



$r = mysql_query($query);



$quan = mysql_num_rows($r);



//$pages = ceil($quan/$caseonpage);



$LimitParam=$p*$caseonpage-$caseonpage;



$q_limit = " LIMIT ".$LimitParam.",".$caseonpage;



if (isset($_REQUEST['newcell']) && $_REQUEST['id'] > 0)



	{



        $userip = ipCheck();



        $nick = urldecode($_REQUEST['user']);



	    $nick2 = urlencode($nick);



        $nick = str_replace("%20", " ", $nick);



	    $sesid = $_REQUEST['sessid'];



	    $city = $_REQUEST['city'];



	    if (strlen($sesid) > 3)



	        {



	        $sock = @fsockopen("www.timezero.ru", 80, $er1, $er2, 5);



	        if(@$sock)



	            {



	                $addr = "/cgi-bin/authorization.pl?login=".$nick2."&ses=".$sesid."&city=".$city;



	                fputs($sock, "GET ".$addr." HTTP/1.0\r\n");



	                fputs($sock, "Host: www.timezero.ru \r\n");



	                fputs($sock, "Content-type: application/x-www-url-encoded \r\n");



	                fputs($sock, "\r\n\r\n");



	                $tmp_headers = "";



	                while ($str = trim(fgets($sock, 4096))) $tmp_headers .= $str."\n";



	                $tmp_body = "";



	                while (!feof($sock)) $tmp_body .= fgets($sock, 4096);



	                $tmp_pos1 = strpos($tmp_body, "about=\"");



	                if($tmp_pos1!==false)



	                    {



	                        $tmp_str1 = substr($tmp_body, 0, $tmp_pos1);



	                        $tmp_str2 = substr($tmp_body, strpos($tmp_body, "\"", $tmp_pos1+8));



	                        $tmp_body = $tmp_str1." ".$tmp_str2;



	                    }



	            }



	            if (strpos($tmp_body, "OK"))



	                {



                    	$cur_user = $nick;



	                    $query = "SELECT `victim`, `status`, `history` FROM `compens_tmp` WHERE `id`='".$_REQUEST['id']."' LIMIT 1;";



	                    $r = mysql_query($query);



                        $res = mysql_fetch_array($r);



                        if (strtolower($nick) == strtolower($res['victim']) && $res['status'] == 0)



                        	{



                                $cell = $_REQUEST['newcell'];



                                $hist = $res['history']."{perc=".$tmp.", cell=".$cell.", ip=".$userip.", date=".time()."}";



	                            $query = "UPDATE `compens_tmp` SET `cell`='".$cell."', `history`='".$hist."' WHERE `id`='".$_REQUEST['id']."' LIMIT 1;";



	                            mysql_query($query);



                           }



                   }



            }



    }



$nick = urldecode($_REQUEST['user']);



$nick2 = urlencode($nick);



$nick = str_replace("%20", " ", $nick);



$sesid = $_REQUEST['sessid'];



$city = $_REQUEST['city'];



if (strlen($sesid) > 3)



    {



    $sock = @fsockopen("www.timezero.ru", 80, $er1, $er2, 5);



    if(@$sock)



        {



            $addr = "/cgi-bin/authorization.pl?login=".$nick2."&ses=".$sesid."&city=".$city;



            fputs($sock, "GET ".$addr." HTTP/1.0\r\n");



            fputs($sock, "Host: www.timezero.ru \r\n");



            fputs($sock, "Content-type: application/x-www-url-encoded \r\n");



            fputs($sock, "\r\n\r\n");



            $tmp_headers = "";



            while ($str = trim(fgets($sock, 4096))) $tmp_headers .= $str."\n";



            $tmp_body = "";



            while (!feof($sock)) $tmp_body .= fgets($sock, 4096);



            $tmp_pos1 = strpos($tmp_body, "about=\"");



            if($tmp_pos1!==false)



                {



                    $tmp_str1 = substr($tmp_body, 0, $tmp_pos1);



                    $tmp_str2 = substr($tmp_body, strpos($tmp_body, "\"", $tmp_pos1+8));



                    $tmp_body = $tmp_str1." ".$tmp_str2;



                }



        }



        if (strpos($tmp_body, "OK"))



            {



            	$cur_user = $nick;



                if (in_array($nick, $compens_cops2))



	                {



	                    $iscop = true;



	                }



	            else



	                {



	                    $iscop = false;



	                }



            }



        else



        	{



            	$cur_user = "nofuckinguserfoundsonocredentialsgiven"; // I don't think there will ever exist a character with this kind of nick :crazy:



            }



    }



else



	{



       	$cur_user = "nofuckinguserfoundsonocredentialsgiven"; // I don't think there will ever exist a character with this kind of nick :crazy:



    }



if (isset($_REQUEST['payed']) && $_REQUEST['payed'] > 0 && $iscop)



	{



    	$query = "UPDATE `compens_tmp` SET `status`='2', `date2`='".time()."' WHERE `id`='".$_REQUEST['payed']."' LIMIT 1;";



        mysql_query($query) or die (mysql_error());



    }



$query = "SELECT `id` FROM `compens_tmp` WHERE `status` = '1'";



$r = mysql_query($query);



if (mysql_num_rows($r) > 0)



	{



    	echo ("<b>Внимание!</b><br>В данный момент происходит выплата компенсаций, часть очереди заблокирована и недоступна для редактирования!<br><br>");



    }



if ($cur_user !== "nofuckinguserfoundsonocredentialsgiven")



	{



        $query = "SELECT `id` FROM `compens_tmp` WHERE `status` < '2' AND `victim` = '".$cur_user."'";



		$r = mysql_query($query);



		$mine = mysql_num_rows($r);



		if ($mine > 0)



        	{



            	if ($me == 1)



                	{



                    	$q_cond = " AND `victim` = '".$cur_user."'";

                        $query = "SELECT `id` FROM `compens_tmp` WHERE `status` < '2' AND `victim` = '".$cur_user."'";

	                    $r = mysql_query($query);

	                    $quan = mysql_num_rows($r);

	                    $pages = ceil($quan/$caseonpage);



                        ?>



							<a href="?act=compens2&sec=<?=$sec?>&user=<?=$_REQUEST['user']?>&sessid=<?=$_REQUEST['sessid']?>&city=<?=$_REQUEST['city']?>">Все компенсации</a>



                        <?



                    }



                else



                	{



?>



							<a href="?act=compens2&sec=<?=$sec?>&me=1&user=<?=$_REQUEST['user']?>&sessid=<?=$_REQUEST['sessid']?>&city=<?=$_REQUEST['city']?>">Только мои компенсации (<?=$mine?>)</a>



<?



					}



			}



    }



$plink = "act=compens2&sec=".$sec."&user=".urlencode($nick)."&sessid=".$_REQUEST['sessid']."&city=".$_REQUEST['city'];

if ($me == 1) {$plink .= "&me=1";}

$link_add = "&user=".$_REQUEST['user']."&sessid=".$_REQUEST['sessid']."&city=".$_REQUEST['city'];



$sq = "SELECT `id` FROM `compens_tmp` WHERE `status` < '2'";



$sr = mysql_query($sq);



$queue_length = mysql_num_rows($sr); //Заявок в очереди



$sq = "SELECT SUM(`sum`) as `summa` FROM `compens_tmp` WHERE `status` < '2'";



$sr = mysql_query($sq);



$srs = mysql_fetch_array($sr);



$queue_sum = $srs['summa']; //Приблизительная сумма выплат в очереди



$sq = "SELECT `id` FROM `compens_tmp` WHERE `status` = '2'";



$sr = mysql_query($sq);



$payed_out_length = mysql_num_rows($sr); //Выплачено заявок



$sq = "SELECT SUM(`sum`) as `summa` FROM `compens_tmp` WHERE `status` = '2'";



$sr = mysql_query($sq);



$srs = mysql_fetch_array($sr);



$payed_out_sum = $srs['summa']; //Приблизительная сумма совершенных выплат



?>



<script language="JavaScript" type="text/javascript">



function line1(count,sum,nick,flink,linkname,cell)



	{



		var txt = "";



		txt = '<tr bgcolor="#E8D395"><td nowrap>'+count+'</td><td>'+nick+'</td><td align="center"><a href="http://www.timezero.ru/cgi-bin/forum.pl?'+flink+'" target="_blank">'+linkname+'</a></td><td align="center">'+sum+'</td><td align="center">'+cell+'</td></tr>';



		return txt;



	}



function line2(id,count,sum,nick,flink,linkname,cell,status)



	{



		var txt = "";



        if (status == 0) {ststr = "";} else {ststr = " disabled";}



		txt = '<tr bgcolor="#E8D395"><td nowrap>'+count+'</td><td><font color="blue">'+nick+'</font></td><td align="center"><a href="http://www.timezero.ru/cgi-bin/forum.pl?'+flink+'" target="_blank">'+linkname+'</a></td></td><td id="paym'+id+'" align="center"><b>'+sum+'</b></td><td align="center"><input type="text" id="cell'+id+'" value="'+cell+'"'+ststr+'><input name="ok" type="button" onClick="if (!confirm(\'Сохранить введенный номер ячейки?\')) {return false;} else {UpdateBase(\''+id+'\');}" value="OK"'+ststr+'></td></tr>';



		return txt;



	}



<? if ($iscop) { ?>



function line3(id,count,sum,nick,flink,linkname,cell,status)



	{



		var txt = "";



        if (status == 1){pref = '<font color="red"><b>!</b></font> ';}



        else{pref = '';}



		txt = '<tr bgcolor="#E8D395"><td nowrap>'+count+'</td><td>'+pref+nick+' [<a href="?act=compens2&payed='+id+'<?=$link_add?>" onClick="if (!confirm(\'Вы уверены?\')) {return false;}"><b>оплачено</b></a>] [<a href="?act=compens_add2&id='+id+'<?=$link_add?>" target="_blank"><b>изменить</b></a>]</td align="center"><td align="center"><a href="http://www.timezero.ru/cgi-bin/forum.pl?'+flink+'&m=1" target="_blank">'+linkname+'</a></td><td align="center">'+sum+'</td><td align="center">'+cell+'</td></tr>';



		return txt;



	}



<?}?>



function UpdateBase(id)



	{



        cid = 'cell'+id;



        var newcell = ''+document.getElementById(cid).value;



        document.getElementById("newcell").value = newcell;



        document.getElementById("id").value = id;



		document.getElementById("hiddenform").submit();



	}



</script>



<form id="hiddenform" name="hiddenform" method="post" action="index.php?<?=$plink?>">

<input type="hidden" name="newcell" id="newcell" value="75000">

<input type="hidden" name="id" id="id" value="0">

<input type="hidden" name="p" id="p" value="<?=$p?>">

</form>



<br><a href="?act=compens2&sec=payed_out&me=<?=$me?>&user=<?=$_REQUEST['user']?>&sessid=<?=$_REQUEST['sessid']?>&city=<?=$_REQUEST['city']?>">Выплаченные компенсации</a><br>



<a href="?act=compens2&sec=queue&me=<?=$me?>&user=<?=$_REQUEST['user']?>&sessid=<?=$_REQUEST['sessid']?>&city=<?=$_REQUEST['city']?>">Очередь компенсаций</a>



<hr>



Компенсаций в очереди: <b><?=$queue_length?></b> на сумму <b><?=$queue_sum?></b> м.м.<br>

<?if ($payed_out_sum > 0) {?>

Компенсаций выплачено: <b><?=$payed_out_length?></b> на сумму <b><?=$payed_out_sum?></b> м.м.<br>

<?}?>



<hr>



<br><p align=right>Страницы: <b>



<?=ShowPages($p,$pages,10,$plink);?>



</b></p>



<table width="100%"  border="0" cellspacing="3" cellpadding="3" align="center">



	<tr bgcolor="#948559">



		<td width="1" nowrap align="center"><b>№</b></td>



		<td align="center"><b>Потерпевший</b></td>



		<td align="center"><b>Дата</b></td>



	    <td align="center" width="1"><b>Сумма</b></td>



        <td align="center"><b>Ячейка</b></td>



	</tr>



<script language="JavaScript" type="text/javascript">



var show="";



<?



if ($sec=="payed_out") $query = "SELECT * FROM `compens_tmp` WHERE `status`='2'".$q_cond." ORDER BY `victim`".$q_limit;



else  $query = "SELECT * FROM `compens_tmp` WHERE `status`<'2'".$q_cond." ORDER BY `victim`".$q_limit;



$r = mysql_query($query) or die(mysql_error());



$count = 1+($caseonpage*$p)-$caseonpage;



while ($row = mysql_fetch_array($r))



	{



        if ($sec=="payed_out")



        	{



                $usrfull = ParseNews3($row['vfull']);



                $usrfull = str_replace("'", "\"", $usrfull);



                $cursum = $row['sum'];



				$link = str_replace("'", "\'", $row['link']);



				$str = "show += line1(".$count.",".$cursum.",'".$usrfull."','".$link."','".date('d.m.Y',$row['date2'])."','".$row['cell']."');



";



				echo ($str);



            }



        elseif ($me == 1 && $cur_user !== "nofuckinguserfoundsonocredentialsgiven")



        	{



	        	$query2 = "SELECT `id` FROM `compens_tmp` WHERE `status`<'2' AND `date1`<'".$row['date1']."') AND `id`<>'".$row['id']."'";



                $rs2 = mysql_query($query2);



                $c2 = mysql_num_rows($rs2);



                $usrfull = ParseNews3($row['vfull']);



                $usrfull = str_replace("'", "\"", $usrfull);



                $link = str_replace("'", "\'", $row['link']);



                $str = "show += line2(".$row['id'].",'".$c2."',".$row['sum'].",'".$usrfull."','".$link."','".date('d.m.Y',$row['date1'])."','".$row['cell']."',".$row['status'].");



";



                echo ($str);



            }



        elseif (strtolower($cur_user) == strtolower($row['victim']))



        	{



                $usrfull = ParseNews3($row['vfull']);



                $usrfull = str_replace("'", "\"", $usrfull);



                $link = str_replace("'", "\'", $row['link']);



                $str = "show += line2(".$row['id'].",".$count.",".$row['sum'].",'".$usrfull."','".$link."','".date('d.m.Y',$row['date1'])."','".$row['cell']."',".$row['status'].");



";



//				$str = str_replace("'", "\'", $str);



                echo ($str);



            }



        elseif ($iscop && strtolower($cur_user) !== strtolower($row['victim']))



        	{



                $usrfull = ParseNews3($row['vfull']);



                $usrfull = str_replace("'", "\"", $usrfull);



                $cursum = $row['sum'];



				$link = str_replace("'", "\'", $row['link']);



				$str = "show += line3(".$row['id'].",".$count.",".$cursum.",'".$usrfull."','".$link."','".date('d.m.Y',$row['date1'])."','".$row['cell']."',".$row['status'].");



";



//                $str = str_replace("'", "\'", $str);



                echo ($str);



            }



        else



        	{



                $usrfull = ParseNews3($row['vfull']);



                $usrfull = str_replace("'", "\"", $usrfull);



                $cursum = $row['sum'];



				$link = str_replace("'", "\'", $row['link']);



				$str = "show += line1(".$count.",".$cursum.",'".$usrfull."','".$link."','".date('d.m.Y',$row['date1'])."','".$row['cell']."');



";



//                $str = str_replace("'", "\'", $str);



                echo ($str);



            }



        $count ++;



    }



?>



document.write(show);



</script>



</table>



<br><p align=right>Страницы: <b>



<?=ShowPages($p,$pages,10,$plink);?>



</b></p>



<?}?>