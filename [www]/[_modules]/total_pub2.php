<h1>����������� �� ������������ ����������</h1>
<?
    if ($_REQUEST['makebet_ok'])
    {
      $bets = '';
      $i = 1;
      $countbets = 0;
      foreach ($_REQUEST['win'] as $key => $value)
      {
      	if ($value == 3)
      		{
		      $countbets++;
      		}
        if ($i == 1)
        {
          $bets .= $value;
          $i = 0;
        }
        else
          $bets .= ':'.$value;
      }
      	if ($countbets == 15)
      		{
		        $sql = 'INSERT INTO total_bets SET r_id="'.$_REQUEST['id'].'", nick="'.$_REQUEST['nick'].'",
                bets="'.$bets.'", sum="'.$_REQUEST['sum'].'"';
       			mysql_query($sql);
       			echo ("������ �������!");
       		}
       	else
       		{
				echo ("���������� ������� ����� ����� 15 �������! ������ �� �������!");
       		}

    }
?>
<SCRIPT src='_modules/xhr_js.js'></SCRIPT>
<OBJECT id="tz" codeBase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" height="1" width="1" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><PARAM NAME="movie" VALUE="http://tzpolice.ru/_imgs/auth.swf"><PARAM NAME="wmode" VALUE="transparent">
<embed src="http://tzpolice.ru/_imgs/auth.swf" wmode="transparent" width="1" height="1" swLiveConnect="true" id="tz" name="tz" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</OBJECT>
<script language="JavaScript1.2">
<!--
function tz_DoFSCommand(command, args) {
	var tmp = args.split("\t");
	if (command == "OK")
    	{
            var pers_nick = '' + tmp[0];
			var pers_sid = '' + tmp[1];
			var pers_city = '' + tmp[2];
			document.getElementById("betnick").value=pers_nick;
	        var req2 = new Subsys_JsHttpRequest_Js();
            var req3 = new Subsys_JsHttpRequest_Js();
	        req2.onreadystatechange = function()
	            {
	                if (req2.readyState == 4)
	                    {
                           if (req2.responseJS)
                           {
                            if (req2.responseJS.res == 'nousernamewasdetectedsonoactionshouldbemade')
                            	{
                                    document.getElementById('err2').style.display='';
									document.getElementById('betsubm').disabled=true;
	                        	}
                            else
                            	{
									document.getElementById('betsubm').disabled=false;
                                	document.getElementById('r_from').value=req2.responseJS.res;
                                    document.getElementById('r_from_sid').value=pers_sid;
                                    document.getElementById('r_from_city').value=pers_city;
                                    document.getElementById('subm').style.display='';
                                    document.getElementById('requests_list').innerHTML=req2.responseText;
                                }
                           }
	                    }
	            }
	        req2.caching = false;
	        req2.open('POST', '_modules/backends/pers_request_auth.php', true);
	        req2.send({ pn: pers_nick, ps: pers_sid, pc: pers_city });
        }
	else
    	{
			men = document.getElementById('err2');
			men.style.display='';
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
function checkBet()
	{
		var countnobet=0;
		for (i=1; i<=30; i++)
			{
				var curid = 'win3'+i;
				var currad = document.getElementById(curid);
				if (currad.checked)
					{
						countnobet++;
					}
			}
		if (countnobet > 15)
			{
				var needmore = countnobet-15;
				alert ("�� ������ ������� ��������� ����� 15 �������. ������� ��� "+needmore+"!");
				return false;
			}
		else if (countnobet < 15)
			{
				var needmore = 30-countnobet-15;
				alert ("�� ������ ������� ��������� ����� 15 �������. ������� "+needmore+" ������!");
				return false;
			}
		else return true;
	}
// End -->
</script>
<div id="err2" style="display:none" align="center">
<font color="red" size="+1"><b>������ �����������!</b></font><br><br>
����� ������� ������ ������� � ���� ����� ����������.<br>��������! ����������� � ������� ����������������� ������� � ��� ������� ������������������ firewall ��� ������� ����������!<br><br>
<input type="button" value="��������� �������" onClick="javascript: location.reload(true)"><br><br>
</div>
<?
    echo '<h2>������� ������</h2>';
    echo '<form action="/index.php" method="post" onSubmit="return (checkBet())">';
	$sql = "SELECT * FROM total_round WHERE archived='0' order by id desc limit 1;";
	$result = mysql_fetch_array(mysql_query($sql));
    $sql = 'SELECT * FROM total_games WHERE r_id='.$result['id'].' order by id';
    $r = mysql_query($sql);
//    if (mysql_num_rows($r) != 15)
//    {
//      echo '<b>������. � ������ ������ ���� 15 ���</b>';
//    }
//    else
//    {
//      echo '���: <input type="text" id="betnick" name="nick" disabled>&nbsp;&nbsp;';
//      echo '��� ��������: <input type="text" name="sum"><br><br><br>';
      echo '<table border=1 bordercolor="#666666" cellpadding=3 cellspacing=3>';
?>
<tr>
  <td rowspan=2 valign=top width=10>�</td>
  <td rowspan=2 valign=top>������ �������</td>
  <td rowspan=2 valign=top>������ �������</td>
  <td colspan=4 align=center>���������</td>
</tr>
<tr align=center>
  <td>1</td>
  <td>X</td>
  <td>2</td>
  <td>---</td>
</tr>
<?

      $i = 0;
      while ($row = mysql_fetch_assoc($r))
      {
        echo '<tr>';
          echo '<td width=10>'.++$i.'</td>';
          echo '<td>'.$row['name1'].'</td>';
          echo '<td>'.$row['name2'].'</td>';
          echo '<td><input type="radio" id="win0'.$i.'" name="win['.$i.']" value="0"';
//          if ($makebetz[$i] == '1')
//            echo ' checked';
          echo '></td>';
          echo '<td><input type="radio" id="win1'.$i.'" name="win['.$i.']" value="1"';
//          if ($makebetz[$i] == 'x' or $makebetz[$i] == 'X' or $makebetz[$i] == '�' or $makebetz[$i] == '�')
//            echo ' checked';
          echo '></td>';
          echo '<td><input type="radio" id="win2'.$i.'" name="win['.$i.']" value="2"';
//          if ($makebetz[$i] == '2')
//            echo ' checked';
          echo '></td>';
          echo '<td><input type="radio" id="win3'.$i.'" name="win['.$i.']" value="3" checked';
//          if ($makebetz[$i] == '3')
//            echo ' checked';
          echo '></td>';
        echo '</tr>';
      }
      echo '</table>';
      echo '<input type="hidden" name="act" value="total2">';
      echo '<input type="hidden" name="sec" value="bets">';
      echo '<input type="hidden" name="id" value="'.$_REQUEST['id'].'">';
      echo '<input type="submit" id="betsubm" name="makebet_ok" value="���������" disabled>';
      echo '</form>';
?>
<br><br>
<hr>
<br><br>
<B>������� ������������:</B><br>
1. ������� ������ ����� ����� ��������.<br>
2. ����������� ������ 50 ������ �����, ������������ �� ����������, ��� ������ 50 ������ �����.<br>
3. ������ ����������� �� ������ ������� ����� ������������.<br>
4. ������� ��������� ���������� �� ��������� ����.<br>
5. ���� �� ���� �� ���������� �� ������ ���������� 15 (14, 13, 12, 11, 10) ������� � ��������� ��������� ������������, ���, �� ����������� ����� ��������� � ����-��� ���������� ���������.<br>
<br>
<B>��� ��������� ������� ������:</B><br>
1. ��������� ������ ������ �� ���� <B>88999</B>.<br>
2. ������ �� 15 ������� � �� ��������, ������� ��������� ��������, ����� ������� ������. ������ ������ ������� ������������ �������� �1�; ������ ������ ������� � �2�; ����� � �ջ.<br>
3. �� ������ ������� ��� ������� �� 15 ������� � ��� �������� ������ �����.<br><br>
<B>�������� ������������ � ������� �������� �����������:</B><br><br>
1. ���������� ��, ��� ������ �������: 15 �� 15, 14 �� 15, 13 �� 15, 12 �� 15, 11 �� 15 � 10 �� 15. <br>
2. 15% - �� ����� ����� ����������� � ���� ������� ����������, ������������ �� ������������ � ������������� �����.<br>
3. ��, ��� ������ 15 �� 15, �������� 5% �� ����� �����, ��������������� ������, ���� ���������� �� ����, � ���� ��� ������. <br>
��, ��� ������ 15 �� 15 � 14 �� 15, �������� 5% �� ����� �����, ��������������� ������, ���� ���������� �� ����, � ���� ��� ������. <br>
��, ��� ������ 15 �� 15, 14 �� 15 � 13 �� 15, �������� 5% �� ����� �����, ��������������� ������, ���� ���������� �� ����, � ���� ��� ������. <br>
��, ��� ������ 15 �� 15, 14 �� 15, 13 �� 15 � 12 �� 15, �������� 5% �� ����� �����, ��������������� ������, ���� ���������� �� ����, � ���� ��� ������. <br>
��, ��� ������ 15 �� 15, 14 �� 15, 13 �� 15, 12 �� 15 � 11 �� 15, �������� 20% �� ����� �����, ��������������� ������, ���� ���������� �� ����, � ���� ��� ������. <br>
��, ��� ������ 15 �� 15, 14 �� 15, 13 �� 15, 12 �� 15, 11 �� 15 � 10 �� 15, �������� 45% �� ����� �����, ��������������� ������, ���� ���������� �� ����, � ���� ��� ������. <br>
4. ���� �� ��� �� ������ 15 �� 15, �� 5% �� ����� ����� ���� � �������, ������� ����������� �� ��������� ��������. <br><br>

5. ������� ������, ��� �������������� ������ �����: <br>
����� ����� ���������� 100000 ������ �����.<br><br>

15 �� 15, ������� ��� ������, ���� �������� 50 �.�., � ������ 100 �.�..<br>
������� �� ��������: 5% �� 100000 �.�., �.�. 5000 �.�..<br>
��� ��� �������� ��� ������, �� 5000 �.�. �������������� ��������������� ������, �.�. 1665 �.�. � 3335 �.�.. <br>
14 �� 15 �� ������ �� ���. �������������, 5% �� ����� ����� �������������� ����� ����� ��������, ��� ������ 15 �� 15.<br>
���� ����, ��� ������� 14 �� 15 �������� 1 �������, �� ������� �������� ����� ����� �����������, �.�. ��� ������ 14 �� 15 � 15 �� 15. <br>
13 �� 15, 12 �� 15, 11 �� 15 ��������� �� ��� �� �����. <br><br>

��������� ����� &nbsp;������� ������ ����� ��� ������� 10 �� 15: <br>
������: <br>
15 �� 15 �������� 2(�,�) ��������, ������ 50 �.�. � 100 �.�. <br>
14 �� 15 �� ������ �� ���. <br>
13 �� 15 �� ������ �� ���. <br>
12 �� 15 ������� 1(�) �������, ������ 100 �.�.. <br>
11 �� 15 ������ 1(�) �������, ������ 100 �.�.. <br>
10 �� 15 ������� 2(�,e) ��������, ������ 600 �.�. � 200�.�..<br><br>

�) 1665(5%(15/15)), 1665(5%(14/15), 1665(5%(13/15)), 1000(5%(12/15)), 2856(20%(11/15)), 1959(45%(10/15)). <br>
�) 3335(5%(15/15)), 3335(5%(14/15), 3335(5%(13/15)), 2000(5%(12/15)), 5714(20%(11/15)), 3920(45%(10/15)). <br>
�) 2000(5%(12/15)), 5714(20%(11/15)), 3920(45%(10/15)). <br>
�) 5714(20%(11/15)), 3920(45%(10/15)). <br>
�) 23450(45%(10/15)).<br>
�) 7830(45%(10/15)). <br><br>

� ����� ���������� ��������:<br>
�������� (�) ������� - 10810 �.�.<br>
�������� (�) ������� - 21639 �.�.<br>
�������� (�) ������� - 11634 �.�. <br>
�������� (�) ������� - 9634 �.�.<br>
�������� (�) ������� - 23450 �.�.<br>
�������� (�) ������� - 7830 �.�. <br><br>

����� 85000 �.�.<br>
15000 �.�. � � ���� ������� ����������, ������������ �� ������������ � ������������� �����.<br> <br>

<b>�������������� �����:</b><br> ��� � ����� ����� ������������� <b>4 Rating Signs</b>.<br>
��� ������ 15 �� 15, ������� 6 Rating Signs, ���� ����������� ���� ��� ������, �� ���� �������� ���,  � ���� ������ ������, ���� ������ ���������, �� ���������� ������������ ����� ��������������� ��������� �� 5 �������.<br>
��� ������ 14 �� 15, ������� 2 Rating Signs, ���� ����������� ���� ��� ������, �� ���� �������� ���,  � ���� ������ ������, ���� ������ ���������, �� ���������� ������������ ����� ��������������� ��������� �� 5 �������.<br>
���� � ������� ������ ����� �� ������ 15 �� 15 � 14 �� 15, �� � ��������� ������, � 4 Rating Signs ���������� ��� 4 Rating Signs, �.�. ��� ������� 15 �� 15, ������� 6 Rating Signs, � ���, ��� 14 �� 15 ������� 2 Rating Signs.<br><br>

&nbsp; <B>

���� &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;������� &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;1 &nbsp; &nbsp; &nbsp; � &nbsp; &nbsp; &nbsp; 2</B><br>1. &nbsp; 22.04.06 &nbsp; &nbsp;���� (������) - ������ (���������)<br>2. &nbsp; 22.04.06 &nbsp; &nbsp;����� (�����) - ���� (�����)<br>3. &nbsp; 22.04.06 &nbsp; &nbsp;���-������� (�����������) - ������� (������)<br>4. &nbsp; 23.04.06 &nbsp; &nbsp;������� (������) - �� ������ (������)<br>5. &nbsp; 23.04.06 &nbsp; &nbsp;������ (������-��-����) - ������ (���������)<br>6. &nbsp; 23.04.06 &nbsp; &nbsp;������ ������� (������) - ������ (������)<br>7. &nbsp; 23.04.06 &nbsp; &nbsp;������� (�������) - ����� (�.���������)<br>8. &nbsp; 22.04.06 &nbsp; &nbsp;������� � ���������<br>9. &nbsp; 22.04.06 &nbsp; &nbsp;������ - ��������<br>10. &nbsp;22.04.06 &nbsp; &nbsp;������� � �����<br>11. &nbsp;23.04.06 &nbsp; &nbsp;������ � ������04<br>12. &nbsp;23.04.06 &nbsp; &nbsp;������� � �������<br>13. &nbsp;23.04.06 &nbsp; &nbsp;������� � �����<br>14. &nbsp;23.04.06 &nbsp; &nbsp;������� � ��������<br>15. &nbsp;23.04.06 &nbsp; &nbsp;������ � ����<br><br><B>������, ��� ������� ������� ������:</B><br><br>1. 1<br>2. �<br>3. 1<br>4. �<br>5. 1<br>6. 1<br>7. 2<br>8. 1<br>9. 1<br>10. 2<br>11. �<br>12. �<br>13. 1<br>14. 2<br>15. 1<br><br>18:19 �������� '������� ����' ������� �� ���� 88999 ������ ������ �� �����: 50
