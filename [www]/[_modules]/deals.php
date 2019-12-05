<?php
//error_reporting(E_ALL);
//Просмотр собссна топика засейвленного
  if ($_REQUEST['sec'])
  {
    echo '<a href="?act=deals">&laquo; Вернуться</a>';

    $sql = 'SELECT d.topic_name as topic_name,
    		d.forum as forum_id,
            dp.nick as nick,
            dp.text as text,
            dp.data as data
            FROM deals d
            JOIN deals_posts dp on (d.id=dp.deal_id)
            WHERE d.topic_id='.$_REQUEST['sec'].'
            ORDER BY dp.id'
            ;
    $r = mysql_query($sql);
    $headpost = 0;
    while ($row = mysql_fetch_assoc($r))
    {
      if (!$headpost)
      {
        echo '<h1>'.$row['topic_name'].'</h1>';
        echo '(<a href="http://www.timezero.ru/cgi-bin/forum.pl?a=N'.$row['forum_id'].'&c='.$_REQUEST['sec'].'" target=_blank>оригинал</a>)';
		echo "<table width=100% cellpadding=6 cellspacing=1>";
  	    echo "
       	<tr>
	    <td valign=top width=150 nowrap background='i/bgr-grid-sand.gif' class='v9' align=center>
          ";
            echo $row['nick']."<br>
				".$row['data'];
            echo "
				</td><td width=100% valign=top  background='i/bgr-grid-sand.gif'>";
    	    echo $row['text']."
				</td>
            	</tr>
			";
        $headpost = 1;
      }
      else
      {
		echo "
			<tr>
			<td valign=top width=150 nowrap background='i/bgr-grid-sand1.gif' class='v9' align=center>
        ";
        echo $row['nick']."<br>
  		".$row['data'];
        echo "
			</td><td width=100% valign=top  background='i/bgr-grid-sand.gif'>
        ";
		echo $row['text']."</td></tr>";
      }
    }
    echo '</table>';
  }
//Поиск по нику
  elseif ($_REQUEST['nick'])
  {
    echo '<a href="?act=deals">&laquo; Вернуться</a>';

    $sql = 'SELECT distinct d.topic_id as topic_id,
            d.topic_name as topic_name,
            dp.nick as nick
            FROM deals_posts dp
            JOIN deals d on (dp.deal_id = d.id)
            WHERE dp.nick="'.trim($_REQUEST['nick']).'"
            GROUP BY dp.deal_id
    ';

    $r = mysql_query($sql);
    if (mysql_num_rows($r) > 0)
    {
        $first = 1;
        while ($row = mysql_fetch_assoc($r))
    	{
        if ($first == 1)
        {
          echo '<h1>Сохраненные сделки персонажа '.$row['nick'].'</h1>';
          echo '<ul>';
          $first = 0;
        }

        echo '<li><a href="?act=deals&sec='.$row['topic_id'].'">'.$row['topic_name'].'</a></li>';

    	}
    	echo '</ul>';
    }
    else
    {
        echo '<h1>Сохраненные сделки персонажа '.trim(strip_tags(urldecode(($_REQUEST['nick'])))).'</h1>';
        echo 'Не найдено';
    }
  }
  else
  {
//Сохранение сделки
    if ($_POST['adddeal'])
    {
      if(AuthUserClan=='police' || AuthUserClan=='Military Police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy' || AuthUserGroup==100)
        $canrewrite = 1;
      else
        $canrewrite = 0;

      $link = $_POST['link'];
	$link = str_replace("#tz", "", $link);
	$link = str_replace("forum3.pl", "forum.pl", $link);
      if (strpos($link, 'http://www.timezero.ru/cgi-bin/forum.pl?a=N&c=') === false && strpos($link, 'http://www.timezero.ru/cgi-bin/forum.pl?a=N1&c=') === false)
      {
        $error = 'Неверная ссылка';
      }
      else
      {
        $tmp = explode('&c=', $link);
        $topic_id = $tmp[1];
        $tmp2 = $tmp[0];
        if (strpos($topic_id, '&'))
        {
          $tmp = explode('&', $topic_id);
          $topic_id = $tmp[0];
        }
        $tmp = explode('forum.pl?a=N', $tmp2);
        $forum_id = $tmp[1];
        if (strpos($forum_id, '&'))
        {
          $tmp = explode('&', $forum_id);
          $forum_id = $tmp[0];
        }
if (preg_match('/\\D/', $topic_id)) {
die ("G0ddamn hax0rz...");
}
        $prefix = 'cgi-bin/forum.pl?c='.$topic_id.'&a=N'.$forum_id.'&z='.$topic_id;
        $sql = 'SELECT * FROM deals WHERE topic_id='.$topic_id;
        $r = mysql_query($sql);
        if (mysql_num_rows($r) > 0)
        {
          if ($canrewrite)
            $to_rewrite = 1;
          else
            $error = 'Эта сделка уже есть в базе';
        }
      }

      if (!$error)
        $tmp_page = ForumConn($prefix);
      $tmp_page=str_replace("\r","",$tmp_page);
      $tmp_page=str_replace("\n","",$tmp_page);
      $tmp_page=str_replace("\t","",$tmp_page);

      if (strpos($tmp_page, '&lt;font class=&quot;norm&quot;&gt;Не найден топик, возможно он был удален, или перенесен в другой форум&lt;/font&gt;'))
        $error = 'Топика не существует';
      elseif ($to_rewrite)
      {
        $sql = 'SELECT id FROM deals WHERE topic_id="'.$topic_id.'"';
        $r = mysql_query($sql);
        $row = mysql_fetch_array($r);
        $id = $row[0];
        $sql = 'DELETE FROM deals WHERE id='.$id;
        mysql_query($sql);
        $sql = 'DELETE FROM deals_posts WHERE deal_id='.$id;
        mysql_query($sql);

      }

      if ($tmp_page['error'] || $error)
      {
        if (!$error)
          $error = $tmp_page['error'];
      }
      else
      {
        if ($_REQUEST['posts'] && $_REQUEST['posts']>20)
          $howmany = 20;
        elseif ($_REQUEST['posts'])
          $howmany = $_REQUEST['posts'];
        else
          $howmany = 8;

        $topic_name = trim(sub($tmp_page, '&lt;h3&gt;', '&lt;/h3&gt;'));

        //режем начало и конец
        $tmp_page = explode('&lt;/h3&gt;', $tmp_page);
        $tmp_page = $tmp_page[1];
        $tmp_page = explode('&lt;p align=right&gt;', $tmp_page);
        $tmp_page = $tmp_page[0];
        //echo ($tmp_page);

        $posts = explode('&lt;TR&gt;&lt;TD align=center valign=top nowrap&gt;', $tmp_page);

        //определяем сколько постов считывать, с учетом сдвига на 1
        if ((count($posts)-1) > ($howmany))
          $howmany = $howmany;
        else
          $howmany = count($posts)-1;

        for ($i = 1; $i <= $howmany; $i++)
        {
          $post = $posts[$i];
          $post = explode('&lt;/TR&gt;', $post);
//		echo (strip_tags($post[0])."<hr>");
          $post = "nick:".strip_tags($post[0]);
		if (strpos("IMG SRC=\"/i/clans/",$post) < 2)
			{
				$post2 = html_entity_decode($post);
				$post2 = strip_tags($post2,"<TR><TD><BR>");
				$post = htmlspecialchars($post2);
			}
          //echo ("<hr>".$post);
          $nick = sub($post, 'ick:', ' [');
          //echo ("<hr>".$nick."<hr>");

          $data = sub($post, 'Добавлено:&lt;BR&gt;', '&lt;/TD&gt;');

          $post = sub($post, '&lt;TD class=&quot;msg-body&quot; valign=top width=100%&gt;', '&lt;/TD&gt;');
          $post = unhtmlentities($post);
          $post = str_replace('i/smile', '_imgs/smiles', $post);
          $post = str_replace('i/clans', '_imgs/clans', $post);
          $post = str_replace('i/i', '_imgs/pro/i', $post);

          $big = 'QWERTYUIOPASDFGHJKLZXCVBNMЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ';
          $small = 'qwertyuiopasdfghjklzxcvbnmйцукенгшщзхъфывапролджэячсмитьбю';

          $nicks[$i-1] = $nick;
          $nicks_uncase[$i-1] = strtr($nick, $big, $small);
          $posts[$i-1] = $post;
          $datas[$i-1] = $data;
        }

        if(!(AuthUserClan=='police' || AuthUserClan=='Military Police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy' || AuthUserGroup==100))
        {
// added by deadbeef
          if (!in_array(strtr(AuthUserName, $big, $small), $nicks_uncase) && !strpos($tmp_page, AuthUserName))
            $error = 'Вы не участвуете в этой сделке';
        }
        if (!$error)
        {
          $sql = 'INSERT INTO deals SET user_id='.AuthUserId.', topic_id='.$topic_id.', topic_name="'.mquote($topic_name).'", add_date=now(), forum="'.$forum_id.'";';
          mysql_query($sql) or die('SQL Error');

          $deal_id = mysql_insert_id();

          foreach ($nicks as $key => $value)
          {
            $sql = 'INSERT INTO deals_posts SET deal_id='.$deal_id.', nick="'.mquote($value).'", text="'.mquote($posts[$key]).'", data="'.mquote($datas[$key]).'"';
            mysql_query($sql) or die('SQL Error: '.$sql);
          }

          echo '<i>Сделка добавлена в хранилище</i>';
        }
      }
      if ($error)
      {
          echo '<i>Ошибка: '.$error.'</i>';
      }
    }

  ?>
    <h1>Хранилище сделок</h1>
  <center>

  <table border="0" cellspacing="0" background="i/bgr-grid-sand.gif" cellpadding="10" style="BORDER: 1px #957850 solid;">

  <tr><td>

<b>Хранилище сделок</b> предоставляет <u>зарегистрированным на сайте персонажам</u> возможность сохранить наиболее важные для вас сделки на долгое время. Даже если сделка на форуме пропадет или будет удалена (затерта) с последней страницы по давности, вы всегда сможете просмотреть сделку в хранилище.<br>
<b>Наличие сделки в хранилище является доказательством ее существования для сотрудников ОБЭП.</b>
<br><br>
Для внесения сделки в хранилище вам необходимо:<br>
1. Быть упомянутым на первой странице сохраняемой сделки;<br>
2. Поместить ссылку на сделку в поле "Ссылка";<br>
3. Нажать кнопку "ОК".<br>
<br>
<u>Внимание: сохраняются первые 8 сообщений сделки, если Вам необходимо большее их количество (но не более 20), либо Вы упомянуты на второй и далее страницах топика - попросите любого полицейского перезаписать сделку с необходимым количеством сообщений.</u>
<br><br>
В дальнейшем вы сможете просматривать все сохраненные сделки с Вашим участием.
<br><br>
Также существует возможность производить поиск по нику, введя нужный вам ник в поле "ник" и нажав "ОК". Будут показаны все сохраненные сделки с участием указанного персонажа.


  </td></tr>

  </table>

  </center>

    <hr>
    <h2>Добавить сделку в хранилище</h2>
<?
    if (!isset($_COOKIE['CUser']))
      echo '<i>Возможность доступна только зарегистрированным пользователям</i>';
    else
    {
?>
    <form name="ad" method="post" action="?act=deals">
      Ссылка: <input type="text" name="link" size="96">&nbsp;
  <?
  if(AuthUserClan=='police' || AuthUserClan=='Military Police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy' || AuthUserGroup==100)
  {
    echo '<br>Количество постов: <input type="text" name="posts" size="4" value="20">&nbsp;';
  }
  ?>
      <input type="submit" name="adddeal" value="OK">
    </form>
<?
    }
?>
    <hr>
    <h2>Поиск по нику</h2>
    <form name="sd" action="" method="get">
      <input type="hidden" name="act" value="deals">
      Ник: <input type="text" name="nick">&nbsp;
      <input type="submit" value="OK">
    </form>
  <?
    if (isset($_COOKIE['CUser']))
    {
    $sql = 'SELECT distinct d.topic_id as topic_id,
            d.topic_name as topic_name,
            dp.nick as nick
            FROM deals_posts dp
            JOIN deals d on (dp.deal_id = d.id)
            WHERE dp.nick="'.trim(AuthUserName).'"
            GROUP BY dp.deal_id
    ';
      $r = mysql_query($sql);
      if (mysql_num_rows($r) > 0)
      {
          echo '<hr>';
          echo '<h2>Сохраненные сделки c Вашим участием</h2>';
          echo '<ul>';
          while ($row = mysql_fetch_assoc($r))
      	{
          echo '<li><a href="?act=deals&sec='.$row['topic_id'].'">'.$row['topic_name'].'</a></li>';

      	}
      	echo '</ul>';
      }
    }
?>
    <hr>
    <h2>Статистика сервиса (добавление)</h2>
<?

    $a = extract_date(strftime("%Y-%m-%d %H:%M:%S", mktime()));

    $week = strftime("%Y-%m-%d", mktime(0,0,0,$a[3],$a[4]-7,$a[5]));
    $month = strftime("%Y-%m-%d", mktime(0,0,0,$a[3]-1,$a[4],$a[5]));

    if (isset($_COOKIE['CUser']))
    {
//всего
      $sql = 'SELECT count(distinct d.topic_id) as cnt
              FROM deals_posts dp
              JOIN deals d on (dp.deal_id = d.id)
              WHERE dp.nick="'.AuthUserName.'"
      ';
      $res = mysql_query($sql);
      $row = mysql_fetch_array($res);
      $own_deals['total'] = $row[0];

//неделя
      $sql = 'SELECT count(distinct d.topic_id) as cnt
              FROM deals_posts dp
              JOIN deals d on (dp.deal_id = d.id)
              WHERE dp.nick="'.AuthUserName.'"
              AND d.add_date > "'.$week.'"
      ';
      $res = mysql_query($sql);
      $row = mysql_fetch_array($res);
      $own_deals['week'] = $row[0];

//месяц
      $sql = 'SELECT count(distinct d.topic_id) as cnt
              FROM deals_posts dp
              JOIN deals d on (dp.deal_id = d.id)
              WHERE dp.nick="'.AuthUserName.'"
              AND d.add_date > "'.$month.'"
      ';
      $res = mysql_query($sql);
      $row = mysql_fetch_array($res);
      $own_deals['month'] = $row[0];
    }

//всего
      $sql = 'SELECT count(id) as cnt
              FROM deals';
      $res = mysql_query($sql);
      $row = mysql_fetch_array($res);
      $all_deals['total'] = $row[0];

//неделя
      $sql = 'SELECT count(id) as cnt
              FROM deals
              WHERE add_date > "'.$week.'"
              ';
      $res = mysql_query($sql);
      $row = mysql_fetch_array($res);
      $all_deals['week'] = $row[0];

//месяц
      $sql = 'SELECT count(id) as cnt
              FROM deals
              WHERE add_date > "'.$month.'"
              ';
      $res = mysql_query($sql);
      $row = mysql_fetch_array($res);
      $all_deals['month'] = $row[0];


?>

    <table width="90%"  border="0" cellspacing="3" cellpadding="2" align="center">
<tr>
  <td bgcolor=#F4ECD4>&nbsp;</td>
  <td bgcolor=#F4ECD4>За неделю</td>
  <td bgcolor=#F4ECD4>За месяц</td>
  <td bgcolor=#F4ECD4>За все время</td>
</tr>
<?
    if (isset($_COOKIE['CUser']))
    {
?>
<tr>
  <td bgcolor=#F4ECD4>Ваших сделок</td>
  <td background="i/bgr-grid-sand1.gif"><?=$own_deals['week']?></td>
  <td background="i/bgr-grid-sand1.gif"><?=$own_deals['month']?></td>
  <td background="i/bgr-grid-sand1.gif"><?=$own_deals['total']?></td>
</tr>
<?
    }
?>
<tr>
  <td bgcolor=#F4ECD4>Всего сделок</td>
  <td background="i/bgr-grid-sand1.gif"><?=$all_deals['week']?></td>
  <td background="i/bgr-grid-sand1.gif"><?=$all_deals['month']?></td>
  <td background="i/bgr-grid-sand1.gif"><?=$all_deals['total']?></td>
</tr>





    </table>


<?




  }

   function extract_date($date) {
     $arr = explode(" ",$date);

     $d = $arr[0];
     $t = $arr[1];

     $arr_d = explode("-",$d);
     $year = $arr_d[0];
     $year = intval($year);
     $month = $arr_d[1];
     $month = intval($month);
     $day = $arr_d[2];
     $day = intval($day);

     $arr_t = explode(":",$t);
     $hour = $arr_t[0];
     $hour = intval($hour);
     $minute = $arr_t[1];
     $minute = intval($minute);
     $second = $arr_t[2];
     $second = intval($second);

     return array($hour, $minute, $second, $month, $day, $year);
   }


?>