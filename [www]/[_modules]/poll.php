<br>
<p class="menu1th"><img src="i/bullet-menu01.gif" width="15" height="11" hspace="0" vspace="0" align="absmiddle"><b> Голосования:</b></p>

<div>

<?php
$SQL="SELECT * from poll_data WHERE is_active=1 ORDER BY id DESC";
$r=mysql_query($SQL);
if(mysql_num_rows($r)>0) {

#  if authorized

if(AuthStatus==1 && AuthUserId!="") {

   $d=mysql_fetch_array($r);
   echo "
         <form method=GET name='poll' id='poll'>
         <input name='IdPoll' type='hidden' value='".$d['id']."'>
         <p align=center><b>".stripslashes(strip_tags($d['p_question']))."</b></p>
   ";

   $PollData=explode("|||", $d['p_data']);

   if($_REQUEST['IdPoll'] && isset($_REQUEST['IdVote'])) {
      if($_REQUEST['IdPoll']==$d['id']) {
         if($_REQUEST['IdVote']<count($PollData)) {

            $SQL="SELECT v_time FROM poll_votes WHERE id_poll='".$_REQUEST['IdPoll']."' AND id_user='".AuthUserId."' ORDER BY v_time DESC";
            $d2=mysql_fetch_array(mysql_query($SQL));
//            if(time()>($d2['v_time']+86400)) {
            if(time()>($d2['v_time']+2592000)) {

            $SQL="INSERT INTO poll_votes (id_poll, id_vote, id_user, v_time) values('".$_REQUEST['IdPoll']."','".$_REQUEST['IdVote']."','".AuthUserId."','".time()."')";
            mysql_query($SQL);
            echo "<p align=center>Ваш голос учтен</p>";

            } else echo "<p align=center>голосовать можно только один раз</p>";


         } else echo "<p align=center>Нет такого варианта ответа</p>";
      } else echo "<p align=center>Такого голосования нет</p>";
   }

$fSQL="SELECT v_time FROM poll_votes WHERE id_poll='".$d['id']."' AND id_user='".AuthUserId."' ORDER BY v_time DESC";
$d4=mysql_fetch_array(mysql_query($fSQL));
//if(time()<($d4['v_time']+86400)) $ShowPoll="res";
if(time()<($d4['v_time']+2592000))  $ShowPoll="res";
else $ShowPoll="poll";
if($ShowPoll=="res") {

                        $SQL="SELECT count(id) AS cnt, id_vote FROM poll_votes WHERE id_poll='".$d['id']."' GROUP BY id_vote";
            $r3=mysql_query($SQL);

            $TotalVotes=0;
            while($d3=mysql_fetch_array($r3)) {
                    $TotalVotes+=$d3['cnt'];
                    $curID=$d3['id_vote'];
                                $PollResult[$curID]=$d3['cnt'];
            }

                        foreach($PollData as $VID => $V) {
                    $aaa=round(($PollResult[$VID]/$TotalVotes),3)*100;
                    echo "<span style='font-size:9px'>$V ($aaa%)</span><br><img src='_imgs/poll_pix.gif' width='$aaa' height=7><br>";

            }
             echo "<div align=right >Всего голосов: <b>$TotalVotes</b></div>";

}

if($ShowPoll=="poll") {

   echo "<table width=100% cellspacing=0 cellpadding=1>";
   foreach($PollData as $VID => $V) {
   echo "<tr><td nowrap valign=top><input style='background:i/bgr-menu.gif; border:#00101E'  name='IdVote' type='radio' value='{$VID}'></td><td width=100% valign=top><p style='font-size:11px;color:#A4B2B4'>".stripslashes(strip_tags($V))."</p></td></tr>";
   }
   echo "</table>";

   echo "
      <p align=center><input type='submit' value='Голосовать'></p>
      </form>
   ";
}



} else echo "<p align=center>Только для авторизованных пользователей</p>";

} else echo "<p align=center>Текущих голосований нет</p>";



?>

</form>
</div>