<h1>������ ������ �� ����������� �� ��������� � ���������� �������</h1>
<div class='verdana11'>
<img src='i/bullet-red-01a.gif' width='18' hspace='5'><a href="?act=compens_result">�������� � ������������ ������</a><br>
<img src='i/bullet-red-01a.gif' width='18' hspace='5'><a href="?act=compens_result&sec=to_pay">����������� � ������ ������</a><br>
<img src='i/bullet-red-01a.gif' width='18' hspace='5'><a href="?act=compens_result&sec=ready">���������� � ����������� ������</a><br>
</div>
<hr>
<?
if (AuthUserGroup == "100")
	{
    	$moder = 1;
    }
else
	{
    	$moder = 0;
    }
$comm[0] = "������� ������������";
$comm[10] = "����� - ��� � ������������ �������";
$comm[20] = "����� - �������� �� �������� ������ �������";
$comm[30] = "������������";
$comm[40] = "���������";
$bg[0]="background='i/bgr-grid-sand.gif'";
$bg[1]="background='i/bgr-grid-sand1.gif'";
//Changing status

if ($_REQUEST['sec'] == 'do')
	{
		$query = "SELECT `id` FROM `compensations` WHERE `status` = '0' ORDER BY `appl_time`;";
        $rs = mysql_query($query);
        while (list($id) = mysql_fetch_row($rs))
        	{
				if($_REQUEST[$id] > 0)
                	{
						if($_REQUEST[$id] < 40)
                        	{
                            	$p = "pay_".$id;
                                $query = "UPDATE `compensations` SET `status` = '".$_REQUEST[$id]."', `payment` = '".$_REQUEST[$p]."', `req_payment` = '".$_REQUEST[$p]."', `confirmed` = '".AuthUserName."' WHERE `id` = '".$id."' LIMIT 1;";
                                mysql_query($query) or die (mysql_error());
                            }
                    }
            }
    }


//�������� � ������������
if (!isset ($_REQUEST['sec']) || $_REQUEST['sec'] == 'do')
{
$query = "SELECT * FROM `compensations` WHERE `status` = '0' ORDER BY `appl_time`;";
$rs = mysql_query($query);
if ($moder == 1)
{
?>
<form name="compens" method="post" action="?act=compens_result">
<input type="hidden" name="sec" value="do">
<?}?>
<center><b>�������� � ������������ ������</b></center>
<table width=100%>
<tr bgcolor=#F4ECD4>
<td align=center><b>���</b></td>
<td align=center><b>���� ����������</b></td>
<td align=center><b>� �������</b></td>
<!--<td align=center><b>����������</b></td>-->
<?if ($moder == 1) { ?>
<td align=center><b>���</b></td>
<td align=center><b>��������</b></td>
<?}?>
</tr>
<?
while (list($id, $vnick, $anick, $log, $loc, $time, $appl_time, $res, $req_payment, $payment, $status, $confirmed, $payed) = mysql_fetch_row($rs))
	{
		echo("<tr>");
		echo("<td align=center>$vnick</td>");
		echo("<td align=center>".date('d.m.Y',$appl_time)."</td>");
        if ($moder == 1)
        	{
        		echo("<td align=center><input style='text-align:center' type='text' size='5' name='pay_".$id."' value='$req_payment'>");
            }
        else
        	{
				echo("<td align=center>$payment");
            }
        echo("</td>");
/*
		echo("<td align=center>$comm[$status]");
        if ($moder == 1)
        	{
             	if ($status > 0 && $status < 40)
                	{
                    	echo(" ($confirmed)");
                    }
                elseif ($status == 40)
                	{
                    	echo (" ($payed)");
                    }
            }
        echo("</td>");
*/
		if ($moder == 1)
        	{
        		echo("<td align=center>".date('d.m.Y, H:i',$time).", ���. $loc, ��� <a href='http://www.timezero.ru/sbtl.ru.html?$log' target='_blank'>$log</a></td>");
        		echo("<td align=center>");
?>
                    <select name="<?=$id?>">
				      <option value="0" checked> </option>
				      <option value="10">��� � ������������ �������</option>
				      <option value="20">�� �������� ������</option>
				      <option value="30">�����������</option>
<!--			    	  <option value="40">���������</option> -->
				    </select>
<?
                echo("</td>");
            }
		echo("</tr>");
    }
?>





</table>
<?if ($moder == 1)
{
?>
<center><input type="submit" name="Submit" value="���������"></center>
</form>
<?}
}





//� ������
if ($_REQUEST['sec'] == "to_pay")
{
$query = "SELECT * FROM `compensations` WHERE `status` = '30' ORDER BY `appl_time`;";
$rs = mysql_query($query) or die (mysql_error());
if ($moder == 1)
{
?>
<form name="compens" method="post" action="?act=compens_result">
<input type="hidden" name="sec" value="do">
<?}?>
<center><b>����������� � ������ ������</b></center>
<table width=100%>
<tr bgcolor=#F4ECD4>
<td align=center><b>���</b></td>
<td align=center><b>���� ����������</b></td>
<td align=center><b>� �������</b></td>
<td align=center><b>����������</b></td>
<?if ($moder == 1) { ?>
<td align=center><b>���</b></td>
<td align=center><b>��������</b></td>
<?}?>
</tr>
<?
while (list($id, $vnick, $anick, $log, $loc, $time, $appl_time, $res, $req_payment, $payment, $status, $confirmed, $payed) = mysql_fetch_row($rs))
	{
		echo("<tr>");
		echo("<td align=center>$vnick</td>");
		echo("<td align=center>".date('d.m.Y',$appl_time)."</td>");
        if ($moder == 1)
        	{
        		echo("<td align=center><input style='text-align:center' type='text' size='5' name='pay_".$id."' value='$req_payment'>");
            }
        else
        	{
				echo("<td align=center>$payment");
            }
        echo("</td>");
		echo("<td align=center>$comm[$status]");
        if ($moder == 1)
        	{
             	if ($status > 0 && $status < 40)
                	{
                    	echo(" ($confirmed)");
                    }
                elseif ($status == 40)
                	{
                    	echo (" ($payed)");
                    }
            }
        echo("</td>");
		if ($moder == 1)
        	{
        		echo("<td align=center>".date('d.m.Y, H:i',$time).", ���. $loc, ��� <a href='http://www.timezero.ru/sbtl.ru.html?$log' target='_blank'>$log</a></td>");
        		echo("<td align=center>");
?>
                    <select name="<?=$id?>">
				      <option value="0" checked> </option>
			    	  <option value="40">���������</option>
				    </select>
<?
                echo("</td>");
            }
		echo("</tr>");
    }
?>





</table>
<?if ($moder == 1)
{
?>
<center><input type="submit" name="Submit" value="���������"></center>
</form>
<?}
}




//����������/�����������
if ($_REQUEST['sec'] == "ready")
{
$query = "SELECT * FROM `compensations` WHERE `status` = '10' OR `status` = '20' OR `status` = '40' ORDER BY `appl_time`;";
$rs = mysql_query($query) or die (mysql_error());
if ($moder == 1)
{
?>
<form name="compens" method="post" action="?act=compens_result">
<input type="hidden" name="sec" value="do">
<?}?>
<center><b>���������� � ����������� ������</b></center>
<table width=100%>
<tr bgcolor=#F4ECD4>
<td align=center><b>���</b></td>
<td align=center><b>���� ����������</b></td>
<td align=center><b>� �������</b></td>
<td align=center><b>����������</b></td>
<?if ($moder == 1) { ?>
<td align=center><b>���</b></td>
<!--<td align=center><b>��������</b></td>-->
<?}?>
</tr>
<?
while (list($id, $vnick, $anick, $log, $loc, $time, $appl_time, $res, $req_payment, $payment, $status, $confirmed, $payed) = mysql_fetch_row($rs))
	{
		echo("<tr>");
		echo("<td align=center>$vnick</td>");
		echo("<td align=center>".date('d.m.Y',$appl_time)."</td>");
        if ($moder == 1)
        	{
        		echo("<td align=center><input style='text-align:center' type='text' size='5' name='pay_".$id."' value='$req_payment'>");
            }
        else
        	{
				echo("<td align=center>$payment");
            }
        echo("</td>");
		echo("<td align=center>$comm[$status]");
        if ($moder == 1)
        	{
             	if ($status > 0 && $status < 40)
                	{
                    	echo(" ($confirmed)");
                    }
                elseif ($status == 40)
                	{
                    	echo (" ($payed)");
                    }
            }
        echo("</td>");
		if ($moder == 1)
        	{
        		echo("<td align=center>".date('d.m.Y, H:i',$time).", ���. $loc, ��� <a href='http://www.timezero.ru/sbtl.ru.html?$log' target='_blank'>$log</a></td>");
/*        		echo("<td align=center>");
?>
                    <select name="<?=$id?>">
				      <option value="0" checked> </option>
				      <option value="10">��� � ������������ �������</option>
				      <option value="20">�� �������� ������</option>
				      <option value="30">�����������</option>
<!--			    	  <option value="40">���������</option> -->
				    </select>
<?
                echo("</td>");
*/
            }
		echo("</tr>");
    }
?>





</table>
<?if ($moder == 1)
{
?>
<center><input type="submit" name="Submit" value="���������"></center>
</form>
<?}
}
?>