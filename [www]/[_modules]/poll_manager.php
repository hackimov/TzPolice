<h1>Менеджер голосований</h1>
<?php
if(AuthStatus==1 && AuthUserName!="" && AuthUserGroup>2) {
?>
<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Существующие голосования:  </strong> </p></td>
</tr><tr><td>
<?


if($_REQUEST['a']=="add" && strlen($_REQUEST['ques'])>5 && count($_REQUEST['ans'])>1) {

   foreach($_REQUEST['ans'] as $ans) if(strlen($ans)>0) $answ[]=$ans;
   $PollData=implode("|||", $answ);
   $SQL="INSERT INTO poll_data (p_question,p_data) values('".addslashes($_REQUEST['ques'])."','".addslashes($PollData)."')";
   mysql_query($SQL);
   echo "<h6>Голосование \"".stripslashes($_REQUEST['ques'])."\" создано</h6>";


}
if($_REQUEST['a'] && $_REQUEST['IdPoll']) {
   if($_REQUEST['a']=="activate") {
      $SQL="UPDATE poll_data SET is_active=1 WHERE id='".$_REQUEST['IdPoll']."'";
      mysql_query($SQL);
      echo "<h6>Голосование ID:".$_REQUEST['IdPoll']." открыто</h6>";
   }
   if($_REQUEST['a']=="disactivate") {
      $SQL="UPDATE poll_data SET is_active=0 WHERE id='".$_REQUEST['IdPoll']."'";
      mysql_query($SQL);
      echo "<h6>Голосование ID:".$_REQUEST['IdPoll']." закрыто</h6>";
   }
   if($_REQUEST['a']=="delete") {
      $SQL="DELETE FROM poll_data WHERE id='".$_REQUEST['IdPoll']."'";
      mysql_query($SQL);
      $SQL="DELETE FROM poll_votes WHERE id_poll='".$_REQUEST['IdPoll']."'";
      mysql_query($SQL);
      echo "<h6>Голосование ID:".$_REQUEST['IdPoll']." удалено</h6>";
   }

}

$r=mysql_query("SELECT * FROM poll_data ORDER BY is_active DESC, id DESC");
while($d=mysql_fetch_array($r)) {
echo "<div class='verdana11'>";
if($d['is_active']==1) echo " <b>[active]</b> ";
echo "<a href='?act=poll_manager&IdPoll={$d['id']}&a=view'>{$d['p_question']}</a> ";
if($d['is_active']==0) echo "/ <a href='?act=poll_manager&IdPoll={$d['id']}&a=activate'>открыть</a> ";
else echo "/ <a href='?act=poll_manager&IdPoll={$d['id']}&a=disactivate'>закрыть</a> ";
echo "/ <a href='#; return false;' onclick=\"if(confirm('Действительно удалить голосование?')) top.location.href='?act=poll_manager&IdPoll={$d['id']}&a=delete'\">удалить</a> ";
echo "</div>";
}
?>
</td></tr>
</table>


<?if($_REQUEST['a']=="view" && $_REQUEST['IdPoll']) { ?>
<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Голоса:</strong> </p></td>
</tr><tr><td>

<?

$SQL="SELECT * FROM poll_data WHERE id='".$_REQUEST['IdPoll']."'";
$d=mysql_fetch_array(mysql_query($SQL));
$PollData=explode("|||", $d['p_data']);

$SQL="SELECT v.*, u.user_name FROM poll_votes v LEFT JOIN site_users u ON u.id=v.id_user WHERE v.id_poll='".$_REQUEST['IdPoll']."' ORDER BY v.id_vote, v.v_time";
$r=mysql_query($SQL);
while($d=mysql_fetch_array($r)) {

      $vid=$d['id_vote'];
      echo "<br> ".$PollData[$vid]." (".date("d.m.Y H:i",$d['v_time']).") - ".$d['user_name']." ";

}

?>

</td></tr>
</table>
<?}?>





<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Создание голосования:  </strong> </p></td>
</tr><tr><td>
<form method="post">
<input name="act" type="hidden" value="poll_manager">
<input name="a" type="hidden" value="add">
<table width=100% align=center>
<tr><td width=50% align=right>Вопрос для голосования: </td><td width=70%><input size=50 name="ques" type="text" value=""></td></tr>
<tr><td width=50% align=right>Вариант ответа 1: </td><td width=50%><input size=50 name="ans[]" type="text" value=""></td></tr>
<tr><td width=50% align=right>Вариант ответа 2: </td><td width=50%><input size=50 name="ans[]" type="text" value=""></td></tr>
<tr><td width=50% align=right>Вариант ответа 3: </td><td width=50%><input size=50 name="ans[]" type="text" value=""></td></tr>
<tr><td width=50% align=right>Вариант ответа 4: </td><td width=50%><input size=50 name="ans[]" type="text" value=""></td></tr>
<tr><td width=50% align=right>Вариант ответа 5: </td><td width=50%><input size=50 name="ans[]" type="text" value=""></td></tr>
<tr><td width=50% align=right>Вариант ответа 6: </td><td width=50%><input size=50 name="ans[]" type="text" value=""></td></tr>
<tr><td width=50% align=right>Вариант ответа 7: </td><td width=50%><input size=50 name="ans[]" type="text" value=""></td></tr>
<tr><td width=50% align=right>Вариант ответа 8: </td><td width=50%><input size=50 name="ans[]" type="text" value=""></td></tr>
<tr><td align=center colspan=2><input type="submit" value="Создать">  </td></tr>
</table>

</form>


</td></tr>
</table>

<?
} else echo $mess['AccessDenied'];
?>