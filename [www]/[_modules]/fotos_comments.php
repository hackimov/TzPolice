<h1>Комментарии к фотографиям</h1>
<table border="0" cellspacing="0" cellpadding="10" width="100%">
<tr>
  <td width="265" valign="top">
  <table border="0" style="BORDER: 1px #957850 solid;" background="i/bgr-grid-sand.gif" cellspacing="0" cellpadding="3" width="100%">
  <tr><td style="BORDER-BOTTOM: 1px #957850 solid;">
<?php
  //Fotos Settings
extract($_REQUEST);
  $fotos_nicksperpage = 25;
  $query = "SELECT * FROM fotos_users WHERE (fotos > '0'";

  if ($sort_clan <> "no" && $sort_clan) { $query = $query . " AND clan='$sort_clan'"; }

  if ($sort_gener <> "no" && $sort_gener) { $query = $query . " AND gener='$sort_gener'"; }

  $query = $query . ") order by ";

  if ($sort_by == "new" || !$sort_by) { $query = $query . "fotos DESC"; }

  elseif ($sort_by == "name") { $query = $query . "nick"; }

  elseif ($sort_by == "sum") { $query = $query . "points_sum DESC"; }

  elseif ($sort_by == "points") { $query = $query . "points_rank DESC"; }



  $result = mysql_query($query);

  $pages = ceil(mysql_num_rows($result) / $fotos_nicksperpage);

  if($_REQUEST['p']>0) $p=$_REQUEST['p'];

  else $p=1;

  $pages_add = "act=fotos";

  if ($sort_by && $sort_by <> "new") { $pages_add .= "&sort_by=".$sort_by; } if ($sort_gener && $sort_gener <> "no") { $pages_add .= "&sort_gener=".$sort_gener; } if ($sort_clan && $sort_clan <> "no") { $pages_add .= "&sort_clan=".$sort_clan; }

?>

  <b>Страницы:</b> <?=ShowPages($p,$pages,5,$pages_add)?>

  </td></tr>

  <tr><td background="i/bgr-grid-sand1.gif">

<?php

  $temp_p = ($p - 1) * $fotos_nicksperpage;

  $temp_query = $query . " LIMIT $temp_p,$fotos_nicksperpage";

  $result1 = mysql_query($temp_query);

  if (@mysql_num_rows($result1) == 0) { echo "<center><b>Список пуст</b></center>"; }

  else {

    echo "<table border='0' cellspacing='0' cellpadding='0' width='100%'>";

    while ($row1 = mysql_fetch_assoc($result1)) {

      if (!$nick) { $nick = $row1["nick"]; }

      $result2 = mysql_query("SELECT * FROM fotos_main WHERE (nick='$row1[nick]')");

      $row2 = mysql_fetch_assoc($result2);

        echo "<tr>";

          echo "<td width='100%'>"; if ($row1["clan"]) { echo "<img src='_imgs/clans/".$row1["clan"].".gif'>"; } else { echo "<img src='i/none.gif' width='28' height='16'>"; } echo "<a href='".$PHP_SELF."?act=fotos&p=".$p."&nick=".$row1["nick"]; if ($sort_by && $sort_by <> "new") { echo "&sort_by=".$sort_by; } if ($sort_gener && $sort_gener <> "no") { echo "&sort_gener=".$sort_gener; } if ($sort_clan && $sort_clan <> "no") { echo "&sort_clan=".$sort_clan; } echo "'>" . $row1["nick"] . "</a></td>";

          echo "<td>".@mysql_num_rows($result2)."&nbsp;шт.&nbsp;&nbsp;</td>";

          if ($row1["points_rank"] <> 0) { echo "<td>".$row1["points_rank"]."</td>"; } else { echo "<td>0.00</td>"; }

        echo "</tr>";

    }

    echo "</table>";

  }

?>

  </td></tr>

  <form action="?act=fotos" method="post">

  <tr><td style="BORDER-TOP: 1px #957850 solid;">

    <b>Сортировка/Фильтр:</b><br><br>

    <center>

    &nbsp;по<img src="i/none.gif" width="16" height="1"><select size="1" name="sort_by"><option <?php if ($sort_by == "new" || !$sort_by) { echo "selected"; } ?> value="new">новизне</option><option <?php if ($sort_by == "name") { echo "selected"; } ?> value="name">имени</option><option <?php if ($sort_by == "sum") { echo "selected"; } ?> value="sum">баллам</option><option <?php if ($sort_by == "points") { echo "selected"; } ?> value="points">рейтингу</option></select> только <select size="1" name="sort_gener"><option <?php if (!$sort_gener) { echo "selected"; } ?> value="no">нет</option><option <?php if ($sort_gener == 1) { echo "selected"; } ?> value="1">Муж</option><option <?php if ($sort_gener == 2) { echo "selected"; } ?> value="2">Жен</option></select>&nbsp;<br>

    <img src="i/none.gif" height="5"><br>

    &nbsp;клан <select size="1" style="WIDTH: 173px" name="sort_clan"><option <?php if (!$sort_clan) { echo "selected"; } ?> value="no">нет</option>

<?php

$smiles_dir="_imgs/clans/";

$d=opendir($smiles_dir);

$counter = 0;

while(($CurFile=readdir($d))!==false) if(is_file("$smiles_dir/$CurFile")) {

	$FileType=GetImageSize("$smiles_dir/$CurFile");

	$CurFile=explode(".",$CurFile);

    if($FileType[2]==1)

    	{

			$clans[$counter] = $CurFile[0];

            $counter++;

        }

}

closedir($d);

natcasesort($clans);

reset($clans);

while (list($key, $val) = each($clans))

	{

         if ($val !== "0")

         	{

	            echo "<option value=\"{$val}\"";

	            if($sort_clan=="$val") echo " selected";

	            echo ">{$val}</option>";

            }

	}

?>

   </select>&nbsp;<br>

   <img src="i/none.gif" height="5"><br>

   &nbsp;ник<img src="i/none.gif" width="10" height="1"><input style="WIDTH: 173 px" name="nick" type="text" value="">&nbsp;<br>

   <img src="i/none.gif" height="5"><br>

   <input style="CURSOR: hand" type="submit" value="Применить"><br><br>

   </center>

  </td></tr>

  </form>

  </table><br>

  </td>

  <td valign="top" >

<?php

if(@$_REQUEST['added']==1) echo "<div class=green>Комментарий добавлен</div><br>";

if(@$_REQUEST['DataId']) {



        if(@$_REQUEST['do']=="close" && (abs(AccessLevel) & AccessFotosModer)) {

			$SQL="UPDATE site_data SET allow_comments='0' WHERE id='".$_REQUEST['DataId']."'";

			mysql_query($SQL);

			echo "<script>top.location.href='?act=fotos_comments&DataId=".$_REQUEST['DataId']."&p=".$_REQUEST['p']."'</script>";

        } elseif(@$_REQUEST['do']=="open" && (abs(AccessLevel) & AccessFotosModer)) {

            $SQL="UPDATE site_data SET allow_comments='1' WHERE id='".$_REQUEST['DataId']."'";

            mysql_query($SQL);

            echo "<script>top.location.href='?act=fotos_comments&DataId=".$_REQUEST['DataId']."&p=".$_REQUEST['p']."'</script>";

        }





        if(@$_REQUEST['do']=="delete" && @$_REQUEST['IdComment'] && (abs(AccessLevel) & AccessFotosModer)) {



        $DelText="[red]Удалено полицейским[/red] [clan]police[/clan]<b>".GetUser(AuthUserId,AuthUserName,AuthUserGroup)."</b>";

		$SQL="UPDATE fotos_comments SET comment_text='".addslashes($DelText)."' WHERE id='".$_REQUEST['IdComment']."'";

        mysql_query($SQL);

        echo "<script>top.location.href='?act=fotos_comments&DataId=".$_REQUEST['DataId']."&p=".$_REQUEST['p']."'</script>";



    }



        if(@$_REQUEST['do']=="add" && @$_REQUEST['NewsText']) {



                $SQL="SELECT allow_comments FROM fotos_users WHERE comments_id='".$_REQUEST['DataId']."'";

                $d=mysql_fetch_array(mysql_query($SQL));

		        $AllowComments=$d['allow_comments'];





                $SQL="SELECT banned, post_time FROM site_users WHERE id='".AuthUserId."'";

                $d=mysql_fetch_array(mysql_query($SQL));

//	        echo $d['post_time']."+30 < ".time();

                if($d['banned']>time()) {

                        $MolDiff=$d['banned']-time();

                    echo "<h2>Вы не можете оставлять комментарии еще ".gmdate("zдн. Hч iм sс",$MolDiff)."</h2><br><br>";

                } elseif($d['post_time']+30>time()) {

                $oldcomm=stripslashes($_REQUEST['NewsText']);

                    echo "<h2>Вы не можете оставлять комментарии чаще, чем раз в 30 секунд</h2><br><br>";

                } elseif ($AllowComments==0) {

                echo "<h2>Обсуждение закрыто</h2><br><br>";

                } else {



                        $SQL="INSERT INTO fotos_comments (id_data,id_user,comment_date,comment_text) values('".$_REQUEST['DataId']."','".AuthUserId."','".time()."','".addslashes($_REQUEST['NewsText'])."')";

                        mysql_query($SQL);

            $SQL="UPDATE site_users SET post_time='".time()."' WHERE id='".AuthUserId."'";

            mysql_query($SQL);

                    echo "<script>top.location.href='?act=fotos_comments&DataId=".$_REQUEST['DataId']."&p=".$_REQUEST['p']."'</script>";



            }

        }



$SQL="SELECT * FROM fotos_users WHERE comments_id='".$_REQUEST['DataId']."'";

$r=mysql_query($SQL);

if(mysql_num_rows($r)<1) echo $mess['NewsNotFound'];

else {



        $d=mysql_fetch_array($r);

        $AllowComments=$d['allow_comments'];

    echo "

		<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>

		<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>Обсуждение фотографий: </strong> <a href='http://www.tzpolice.ru/?act=fotos&nick={$d['nick']}'>{$d['nick']}</span></p></td>

		</tr><tr><td>

		</tr></table>

	";



    $SQL="SELECT c.*,u.id AS user_id, u.user_name AS user_name, u.clan FROM fotos_comments c LEFT JOIN site_users u ON c.id_user=u.id WHERE  c.id_data='".$_REQUEST['DataId']."' ORDER BY c.comment_date";

    $r=mysql_query($SQL);

    if(mysql_num_rows($r)<1) echo $mess['NoComments'];

    else {





		$pages=ceil(mysql_num_rows($r)/$CommentsPerPage);

        if($_REQUEST['p']>0) $p=$_REQUEST['p'];

        else $p=1;



        $LimitParam=$p*$CommentsPerPage-$CommentsPerPage;



        $SQL.=" LIMIT $LimitParam, $CommentsPerPage";

        $r=mysql_query($SQL);





        echo "<br><div align=right>Страницы: <b>";

        ShowPages($p,$pages,4,"act=fotos_comments&DataId={$_REQUEST['DataId']}");

        echo "</b></div>";





        for($i=0;$i<mysql_num_rows($r);$i++) {



            $d=mysql_fetch_array($r);

		    echo "

				<br><table width='100%' border='0' cellspacing='3' cellpadding='0'><tr>

				<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'>

            ";

            if(abs(AccessLevel) & AccessFotosModer) echo "<a href='#;return false' onclick=\"if(confirm('Удалить комментарий?')){top.location='?act=fotos_comments&DataId={$d[id_data]}&IdComment={$d[id]}&do=delete&p={$p}'}\">удалить</a> / ";

            echo " ".date("d.m.Y - H:i",$d['comment_date'])." / ".GetClan($d['clan']).GetUser($d['user_id'],$d['user_name'],AuthUserGroup).":

                </p></td>

				</tr><tr><td>

		    ";
$ctext=$d['comment_text'];
//$ctext = wordwrap($ctext, 30, "<wbr>", 1);
//$max=40; # max разрешенная длина слова
//$term='<wbr>'; # чем разбивать длинные слова
//$ctext=preg_replace('/([^ \n\r\t]{'.$max.'})/m','$1'.$term,$ctext);
//$ctext = mywordwrap($ctext);
ParseNews($ctext,0);
//ParseNews($d['comment_text'],0);

            echo "</td></tr></table>";



        }







    }

?>

<br>



<?

echo "<div align=right>Страницы: <b>";

ShowPages($p,$pages,4,"act=fotos_comments&DataId={$_REQUEST['DataId']}");

echo "</b></div>";

?>

</td></tr></table>

<?

if((abs(AccessLevel) & AccessFotosModer) && $AllowComments!=0) echo "<div>[ <a href='?act=fotos_comments&do=close&DataId=".$_REQUEST['DataId']."&p=$p'>Закрыть обсуждение</a> ]</div>";

if((abs(AccessLevel) & AccessFotosModer) && $AllowComments==0) echo "<div>[ <a href='?act=fotos_comments&do=open&DataId=".$_REQUEST['DataId']."&p=$p'>Продолжить обсуждение</a> ]</div>";

if(AuthStatus!=1) echo $mess['CantAddComment'].$mess['WantRegister'];

else {

        $SQL="SELECT banned FROM site_users WHERE id='".AuthUserId."'";

        $d=mysql_fetch_array(mysql_query($SQL));

        if($d['banned']>time()) {

                $MolDiff=$d['banned']-time();

            echo "<h2>Вы не можете оставлять комментарии еще ".gmdate("zдн. Hч iм sс",$MolDiff)."</h2><br><br>";

        } else {

            if($AllowComments==0) echo "<h2>Обсуждение закрыто</h2>";

        else {

?>



<table width='100%' border='0' cellspacing='3' cellpadding='5'>

<td height=20 background='i/bgr-grid-sand1.gif'>

<strong>Оставить комментарий:</strong>

</td></tr></table>



<table width=100% cellpadding=5>

<tr>

<td valign=top>



        <form method="POST" name="News">

        <input name="act" type="hidden" value="fotos_comments">

        <input name="do" type="hidden" value="add">

           <input name="DataId" type="hidden" value="<?=$_REQUEST['DataId']?>">

    <input name="p" type="hidden" value="<?=$p?>">

    Текст комментария: <Br>

        <textarea ONSELECT="storeCaret(this)" ONCLICK="storeCaret(this)" ONKEYUP="storeCaret(this)" name="NewsText" style="width:100%" rows=10><?=@htmlspecialchars(stripslashes($d['news_text']))?><?=$oldcomm?></textarea><br>

        <img src="/_imgs/b.gif" border=0 onclick="decor('b')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы сделать его полужирным">

        <img src="/_imgs/i.gif" border=0 onclick="decor('i')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы выделить его курсивом">

        <img src="/_imgs/u.gif" border=0 onclick="decor('u')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы сделать его подчеркнутым">

        <img src="/_imgs/center.gif" border=0 onclick="decor('div align=center')" style="cursor:hand" ALT="Выделите участок текста и нажмите, чтобы поместить его по центру">

        <br><br>

        <input type="submit" value="Прокомментировать">

        </form>



</td><td width=200 valign=top>



        Вставить: <br>

    <select size="1" onchange="document.frames['icons'].location='_modules/icons/'+this.options[this.selectedIndex].value+'.php'" style="width:100%">

                <option value="blank" selected>-- выберите категорию --</option>

        <option value="smiles"> Смайлики</option>

        <option value="clans"> Кланы</option>

        <option value="prof"> Профессии</option>

        <option value="login"> Ник</option>

        <option value="url"> URL</option>

        </select>



    <iframe BORDER=0 FRAMEBORDER=0 id="icons" src="_modules/icons/blank.php" width=100% HEIGHT=105 style="border:1 solid #000000">





</td></tr>

<table>

<?}}}?>







<?

} // news found

}







?>