<html>

<head>
  <title>.::������ ������::.</title>
  <LINK href="../_modules/tzpol_css.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td>
<?php
require("../_modules/functions.php");
require("../_modules/auth.php");
if(AuthUserClan=='Military Police' || AuthUserGroup == 100)
//AuthUserClan=='police' || AuthUserClan=='Police Academy' || AuthUserClan=='Military Police')
{


/*mysql_connect("localhost", "user", "user");
$mysql_db = "tzpolice";
mysql_select_db($mysql_db);
*/
if(@$_REQUEST['s']) $section = $_REQUEST['s'];
//else die("��������� ������. ���������� ��� ���");

?>
<script language="Javascript" type="text/javascript">
<!--
function reload_opener() {
	opener.document.location.reload();
	window.close();
}

function change_reason() {
	if (document.add.reason_type.selectedIndex == 1) {
		document.add.reason.value = '��������� �� ����������.\n��� [].';
	}
	if (document.add.reason_type.selectedIndex == 2) {
		todaysDate = new Date();
		twoWeek = new Date(todaysDate.getTime() + 1209600000);
		document.add.reason.value = '����� �������.\n��� [].\n����� �� ' + twoWeek.getDate() + '.' + (twoWeek.getMonth()+1) + '.' + twoWeek.getYear();
	}
	if (document.add.reason_type.selectedIndex == 3) {
		document.add.reason.value = '������ ����������.\n��� [].';
	}
	if (document.add.reason_type.selectedIndex == 4) {
		document.add.reason.value = '������ ��������� ��.';
	}
	if (document.add.reason_type.selectedIndex == 5) {
		document.add.reason.value = '��������� �� ������������.\n��� [].';
	}
	if (document.add.reason_type.selectedIndex == 6) {
		document.add.reason.value = '...';
	}
	
	document.add.reason.focus();
}
//-->
</script>
<?


$cur_date = time();
$f_date = date ("Y-m-d", $cur_date);

//Showing add form
if ($section == "add" && @$_REQUEST['nick'])
	{
		$new_nick = $_REQUEST['nick'];
		$query = "SELECT `reason`, `nick` FROM `police_b_list` WHERE `nick` = '".$new_nick."' LIMIT 1;";
        $res = mysql_query($query) or die (mysql_error());
        if (mysql_num_rows($res) == 0)
        	{
		    	$userinfo = GetUserInfo($new_nick);
				if ($userinfo['login'] !== "" && $userinfo["error"] !== "NOT_CONNECTED" && $userinfo["error"] !== "USER_NOT_FOUND" && $userinfo["error"] !== "ERROR_IN_USER_NAME" && $userinfo["level"] > 0) // && $userinfo["login"] == $new_nick
					{
				        $query = "INSERT INTO `police_b_list` (`nick`, `level`, `pro`, `clan`, `reason`, `term`, `status`, `last_updated`, `payment`, `payment_till`, `no_payment_till`, `deleted_by`) VALUES ('".$userinfo['login']."', '".$userinfo['level']."', '".$userinfo['pro']."', '".$userinfo['clan']."', '', '', '0', '".$cur_date."', '', '', '', '' );";
//						echo ($query);
						mysql_query($query) or die("������ ��������� �������. ���������� ��������� ������� �����");
						//Insert by Madimonster
						mysql_query("INSERT INTO `black_list` (`date`, `city`, `nick`, `level`, `pro`, `clan`, `status`) VALUES (NOW(), 0, '".$userinfo['login']."', '".$userinfo['level']."', '".$userinfo['pro']."', '".$userinfo['clan']."', 0);");
						//End
                        $nick = $userinfo['login'];
                    }
				else
                	{
    					 echo ("<pre>");
//                         print_r($userinfo);
                         echo ("</pre>");
                    	 die("������ ��������� �������. ���������� ��������� ������� �����");
                    }
            }
        else
        	{
        		list($reason, $nick) = mysql_fetch_row($res);
		    	$userinfo = GetUserInfo($nick);
				if ($userinfo["error"] !== "NOT_CONNECTED" && $userinfo["error"] !== "USER_NOT_FOUND" && $userinfo["error"] !== "ERROR_IN_USER_NAME") // && $userinfo["login"] == $new_nick
					{
	        	        $query = "UPDATE `police_b_list` SET `level` = '".$userinfo['level']."', `pro` = '".$userinfo['pro']."', `clan` = '".$userinfo['clan']."', `last_updated` = '".$cur_date."', `status` = '0' WHERE `nick` = '".$nick."' LIMIT 1;";
						mysql_query($query) or die("������ ��������� �������. ���������� ��������� ������� �����");
						//Insert by Madimonster
						mysql_query("INSERT INTO `black_list` (`date`, `city`, `nick`, `level`, `pro`, `clan`, `status`) VALUES (NOW(), 0, '".$nick."', '".$userinfo['level']."', '".$userinfo['pro']."', '".$userinfo['clan']."', 0);");
						//End
                    }
                else
                	{
	                     die("������ ��������� �������. ���������� ��������� ������� �����");
                    }
               	$reason = $reason."|||";
		        $rsn = explode("|||", $reason);
        		$reason = $rsn[0];
                $reason = str_replace("<br>", "\r\n", $reason);
            }

            echo ("<img src='../_imgs/clans/".$userinfo['clan'].".gif'> ".$userinfo['login']." [".$userinfo['level']."] <img src='../_imgs/pro/i".$userinfo['pro'].".gif'><br>");
        ?>
<form name="add" method="post" action="">
	<input name="s" type="hidden" value="do_add">
	<input name="nick" type="hidden" value="<?=$nick?>">
	���� (����):
	<input type="text" name="term" size="5" value="0"> (0 ���� - ���������)
	<br>
	��������� ��:
	<select name="reason_type" onchange="change_reason()">
		<option selected>��� ��...</option>
		<option value="1">��������� �� ����������</option>
		<option value="2">�����</option>
		<option value="3">������ ����������</option>
		<option value="4">������ ��������� ��</option>
		<option value="5">��������� �� ������������</option>
		<option value="7">������������� �� �������</option>		
		<option value="8">������ �447-�</option>
		<option value="6">����</option>
		
	</select>
	<br>
	����������:<br>
	<textarea name="reason" cols="50" rows="7" wrap="VIRTUAL" style="width:300px;"><?=$reason?></textarea><br>
	<input type="submit" name="Submit" value="��������">
</form>
<?
    }

//Adding char
if ($section == "do_add")// && @$_REQUEST['nick'] && @$_REQUEST['term'] && @$_REQUEST['reason'])
	{
//    	echo ($_REQUEST['nick']."<br>".$_REQUEST['term']."<br>".$_REQUEST['reason']."<br>");
		$u_term = $_REQUEST['term'];
		if ($u_term > 0)
        	{
//                echo ($u_term);
				$term = ($u_term * 60 * 60 * 24);
                $u_term = $term + $cur_date;
//                echo ($u_term);
            }
        else
        	{
            	$u_term = 9999999999;
            }
        $u_nick = $_REQUEST['nick'];
	$add_ts = time();
        $u_reason = $_REQUEST['reason']."|||".AuthUserName;
        $u_reason_type = $_REQUEST['reason_type'];
        $u_reason = str_replace("\r\n", "<br>", $u_reason);
        $u_employee = AuthUserName;
	$u_updated = time();
        $query = "UPDATE `police_b_list` SET `added` = '".$add_ts."', `reason` = '".$u_reason."', `term` = '".$u_term."', `status` = '0', `payment` = '', `no_payment_till` = '0', `payment_till` = '0', `reason_type` = '".$u_reason_type."', `employee` = '".$u_employee."' WHERE `nick` = '".$u_nick."' LIMIT 1;";
//        echo ($query);
        mysql_query($query) or die(mysql_error());
        ?>
          �������� <b><?=$u_nick?></b> ������� �������� � ������ ������<!--<br><br><a href="JavaScript:reload_opener();">��</a>-->
        <?
	}

//Removing char
if ($section == "del" && @$_REQUEST['nick'])
	{
		if (@$_REQUEST['step'] == '2')
			{
			        $del_reason = $_REQUEST['reason'];
        			$del_reason = str_replace("\r\n", "<br>", $del_reason);

				$query = "UPDATE `police_b_list` SET `del_rsn` = '".$del_reason."', `rem_date` = '".time()."', `deleted_by` = '".AuthUserName."', `status` = '3' WHERE `nick` = '".$_REQUEST['nick']."' LIMIT 1;";
			        mysql_query($query);
				//Insert by Madimonster
				mysql_query("DELETE FROM `black_list` WHERE `nick` = '".$_REQUEST['nick']."';");
				//End
				?>
			          �������� <b><?=$_REQUEST['nick']?></b> ������� ������ �� ������� ������
			        <?
			}
		else
			{
	?>
	�������� ��������� <b><?=$_REQUEST['nick']?></b> �� �� ������� <br><br><br>
        <form name="remove" method="post" action="">
	       <input name="s" type="hidden" value="del">
	       <input name="step" type="hidden" value="2">
	       <input name="nick" type="hidden" value="<?=$_REQUEST['nick']?>">
  ������� �������� �� ��:
<br>
<textarea name="reason" cols="30" rows="7" wrap="VIRTUAL"></textarea>
    <input type="submit" name="Submit" value="��">
</form>
			<?
			}
    }

//Editing char
if ($section == "edit" && @$_REQUEST['nick'])
	{
		$u_nick = $_REQUEST['nick'];
    	$userinfo = GetUserInfo($u_nick);
		if ($userinfo["error"] !== "NOT_CONNECTED" && $userinfo["error"] !== "USER_NOT_FOUND" && $userinfo["error"] !== "ERROR_IN_USER_NAME") // && $userinfo["login"] == $u_nick
			{
		    	$u_updated = time();
		        $query = "UPDATE `police_b_list` SET `level` = '".$userinfo['level']."', `pro` = '".$userinfo['pro']."', `clan` = '".$userinfo['clan']."', `last_updated` = '".$u_updated."' WHERE `nick` = '".$u_nick."' LIMIT 1;";
		        mysql_query($query);
		    }
		$query = "SELECT `level`, `clan`, `pro`, `reason`, `term` FROM `police_b_list` WHERE `nick` = '".$u_nick."' LIMIT 1;";
        $res = mysql_query($query);
        list($level, $clan, $pro, $reason, $term) = mysql_fetch_row($res);
       	$reason = $reason."|||";
        $rsn = explode("|||", $reason);
        $reason = $rsn[0];
        $reason = str_replace("<br>", "\r\n", $reason);
        echo ("<img src='../_imgs/clans/".$clan.".gif'> ".$u_nick." [".$level."] <img src='../_imgs/pro/i".$pro.".gif'><br>");
        ?>
<form name="edit_char" method="post" action="">

  ����� ���� (����):

  <input type="text" name="term" size="5" value="10"> (0 ���� - ���������)

    <br>
  �������� ����*
  <input type="checkbox" name="ch_term" value="checkbox">
  <br>
  ����������:<br>
  <textarea name="reason" cols="30" rows="7" wrap="VIRTUAL"><?=$reason?></textarea>
  <input name="s" type="hidden" id="s" value="do_edit">
  <input name="nick" type="hidden" id="nick" value="<?=$u_nick?>">
  <input name="old_term" type="hidden" id="old_term" value="<?=$term?>">
  <br>
  <input type="submit" name="Submit" value="��������">
<br><br><em>*��������� �������� ����� ������������� �� �������� ������� </em></p>
</form>
        <?
    }

//Save changes to char
if ($section == "do_edit")// && @$_REQUEST['nick'] && @$_REQUEST['term'] && @$_REQUEST['old_term'] && @$_REQUEST['reason'])
	{
//error_reporting(E_ALL);
		$u_term = $_REQUEST['term'];
		if ($u_term == "leave")
        	{
				$u_term = $_REQUEST['old_term'];
            }
        elseif ($u_term > 0)
        	{
				$u_term = (($u_term * 60 * 60 * 24) + $cur_date);
            }
        else
        	{
            	$u_term = 9999999999;
            }
        $u_nick = $_REQUEST['nick'];
		$query = "SELECT `reason` FROM `police_b_list` WHERE `nick` = '".$u_nick."' LIMIT 1;";
        $res = mysql_query($query);
        list($reason) = mysql_fetch_row($res);
        $reason = $reason."|||";
        $rsn = explode("|||", $reason);
        $u_reason = $_REQUEST['reason']."|||".$rsn[1];
        $u_reason = str_replace("\r\n", "<br>", $u_reason);
        $query = "UPDATE `police_b_list` SET `reason` = '".$u_reason."', `term` = '".$u_term."' WHERE `nick` = '".$u_nick."' LIMIT 1;";
//	echo ($query);
        mysql_query($query) or die (mysql_error());
        ?>
          ������ ��������� <?=$u_nick?> ���� ������� ���������������<!--<br><br><a href="JavaScript:reload_opener();">��</a>-->
        <?
    }



//Editing char's payment
if ($section == "edit_payment" && @$_REQUEST['nick'])
	{
		$u_nick = $_REQUEST['nick'];
    	$userinfo = GetUserInfo($u_nick);
		if ($userinfo["error"] !== "NOT_CONNECTED" && $userinfo["error"] !== "USER_NOT_FOUND" && $userinfo["error"] !== "ERROR_IN_USER_NAME") // && $userinfo["login"] == $u_nick
			{
		    	$u_updated = time();
		        $query = "UPDATE `police_b_list` SET `level` = '".$userinfo['level']."', `pro` = '".$userinfo['pro']."', `clan` = '".$userinfo['clan']."', `last_updated` = '".$u_updated."' WHERE `nick` = '".$u_nick."' LIMIT 1;";
		        mysql_query($query);
			//Insert by Madimonster
			mysql_query("DELETE FROM `black_list` WHERE `nick` = '".$_REQUEST['nick']."';");
			//End
		    }
		$query = "SELECT `level`, `clan`, `pro`, `reason` FROM `police_b_list` WHERE `nick` = '".$u_nick."' LIMIT 1;";
        $res = mysql_query($query);
        list($level, $clan, $pro, $reason, $term) = mysql_fetch_row($res);
        $reason = str_replace("<br>", "\r\n", $reason);
        echo ("<img src='../_imgs/clans/".$clan.".gif'> ".$nick." [".$level."] <img src='../_imgs/pro/i".$pro.".gif'><br>");
        ?>
<form name="edit_payment" method="post" action="">
���� �� ���� ������:
    <select name="payment_till">
      <option value="1" selected>1 ����</option>
      <option value="2">2 ���</option>
	  <option value="3">3 ���</option>
	  <option value="4">4 ���</option>
	  <option value="5">5 ����</option>
	  <option value="6">6 ����</option>
	  <option value="7">������</option>
	  <option value="14">2 ������</option>
	  <option value="30">�����</option>
    </select>
  <br>
  ������ ������:<br>
  <textarea name="payment" cols="30" rows="7" wrap="VIRTUAL"></textarea>
  <input name="s" type="hidden" id="s" value="save_payment">
  <input name="nick" type="hidden" id="nick" value="<?=$nick?>">
  <br>
  <input type="submit" name="Submit" value="��������">
</form>
        <?
    }

//Save changes to char's payment
if ($section == "save_payment" && @$_REQUEST['nick'] && @$_REQUEST['payment_till'] && @$_REQUEST['payment'])
	{
		$u_payment_till = $_REQUEST['payment_till'];
		$u_payment_till = (($u_payment_till * 60 * 60 * 24) + $cur_date);
        $u_nick = $_REQUEST['nick'];
        $u_payment = $_REQUEST['payment'];
        $u_payment = str_replace("\r\n", "<br>", $u_payment);
        $query = "UPDATE `police_b_list` SET `payment` = '".$u_payment."', `payment_till` = '".$u_payment_till."', status = '2' WHERE `nick` = '".$u_nick."' LIMIT 1;";
        mysql_query($query);
        ?>
          ������ ��������� <?=$u_nick?> ���� ������� ���������������<!--<br><br><a href="JavaScript:reload_opener();">��</a>-->
        <?
    }





//Denying char's payment
if ($section == "deny_payment" && @$_REQUEST['nick'])
	{
		$u_nick = $_REQUEST['nick'];
    	$userinfo = GetUserInfo($u_nick);
		if ($userinfo["error"] !== "NOT_CONNECTED" && $userinfo["error"] !== "USER_NOT_FOUND" && $userinfo["error"] !== "ERROR_IN_USER_NAME") // && $userinfo["login"] == $u_nick
			{
		    	$u_updated = time();
		        $query = "UPDATE `police_b_list` SET `level` = '".$userinfo['level']."', `pro` = '".$userinfo['pro']."', `clan` = '".$userinfo['clan']."', `last_updated` = '".$u_updated."' WHERE `nick` = '".$u_nick."' LIMIT 1;";
		        mysql_query($query);
		    }
		$query = "SELECT `level`, `clan`, `pro`, `reason` FROM `police_b_list` WHERE `nick` = '".$u_nick."' LIMIT 1;";
        $res = mysql_query($query);
        list($level, $clan, $pro, $reason, $term) = mysql_fetch_row($res);
        $reason = str_replace("<br>", "\r\n", $reason);
        echo ("<img src='../_imgs/clans/".$clan.".gif'> ".$nick." [".$level."] <img src='../_imgs/pro/i".$pro.".gif'><br>");
        ?>
<form name="deny_payment" method="post" action="">
��������� �������� ������ �� ����� ��:
    <select name="no_payment_till">
      <option value="1" selected>1 ����</option>
      <option value="2">2 ���</option>
	  <option value="3">3 ���</option>
	  <option value="4">4 ���</option>
	  <option value="5">5 ����</option>
	  <option value="6">6 ����</option>
	  <option value="7">������</option>
	  <option value="14">2 ������</option>
	  <option value="30">�����</option>
    </select>
  <br>
  <input name="s" type="hidden" id="s" value="save_deny_payment">
  <input name="nick" type="hidden" id="nick" value="<?=$nick?>">
  <br>
  <input type="submit" name="Submit" value="���������">
</form>
        <?
    }

//Save deny changes to char's payment
if ($section == "save_deny_payment" && @$_REQUEST['nick'] && @$_REQUEST['no_payment_till'])
	{
		$u_no_payment_till = $_REQUEST['no_payment_till'];
		$u_no_payment_till = (($u_no_payment_till * 60 * 60 * 24) + $cur_date);
        $u_nick = $_REQUEST['nick'];
        $query = "UPDATE `police_b_list` SET `no_payment_till` = '".$u_no_payment_till."', status = '0' WHERE `nick` = '".$u_nick."' LIMIT 1;";
        mysql_query($query);
        ?>
          ������ ��������� <?=$u_nick?> ���� ������� ���������������<!--<br><br><a href="JavaScript:reload_opener();">��</a>-->
        <?
    }














//Accept payment
if ($section == "accept_payment" && @$_REQUEST['nick'])
	{
        $query = "UPDATE `police_b_list` SET status = '3', `deleted_by` = '".AuthUserName."', `requests` = '0' WHERE `nick` = '".$_REQUEST['nick']."' LIMIT 1;";
        mysql_query($query);
        ?>
          ������ �� ��������� <?=$_REQUEST['nick']?> ���� ������� ������������<br>
          �������� �������� �� ������� ������
          <br><br><a href="JavaScript:reload_opener();">��</a>
        <?
    }


}
else
{
echo ("Access denied...");
}
?>
</td>
  </tr>
</table>
<!--</body>

</html>-->