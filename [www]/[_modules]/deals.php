<?php
//error_reporting(E_ALL);
//�������� ������� ������ �������������
  if ($_REQUEST['sec'])
  {
    echo '<a href="?act=deals">&laquo; ���������</a>';

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
        echo '(<a href="http://www.timezero.ru/cgi-bin/forum.pl?a=N'.$row['forum_id'].'&c='.$_REQUEST['sec'].'" target=_blank>��������</a>)';
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
//����� �� ����
  elseif ($_REQUEST['nick'])
  {
    echo '<a href="?act=deals">&laquo; ���������</a>';

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
          echo '<h1>����������� ������ ��������� '.$row['nick'].'</h1>';
          echo '<ul>';
          $first = 0;
        }

        echo '<li><a href="?act=deals&sec='.$row['topic_id'].'">'.$row['topic_name'].'</a></li>';

    	}
    	echo '</ul>';
    }
    else
    {
        echo '<h1>����������� ������ ��������� '.trim(strip_tags(urldecode(($_REQUEST['nick'])))).'</h1>';
        echo '�� �������';
    }
  }
  else
  {
//���������� ������
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
        $error = '�������� ������';
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
            $error = '��� ������ ��� ���� � ����';
        }
      }

      if (!$error)
        $tmp_page = ForumConn($prefix);
      $tmp_page=str_replace("\r","",$tmp_page);
      $tmp_page=str_replace("\n","",$tmp_page);
      $tmp_page=str_replace("\t","",$tmp_page);

      if (strpos($tmp_page, '&lt;font class=&quot;norm&quot;&gt;�� ������ �����, �������� �� ��� ������, ��� ��������� � ������ �����&lt;/font&gt;'))
        $error = '������ �� ����������';
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

        //����� ������ � �����
        $tmp_page = explode('&lt;/h3&gt;', $tmp_page);
        $tmp_page = $tmp_page[1];
        $tmp_page = explode('&lt;p align=right&gt;', $tmp_page);
        $tmp_page = $tmp_page[0];
        //echo ($tmp_page);

        $posts = explode('&lt;TR&gt;&lt;TD align=center valign=top nowrap&gt;', $tmp_page);

        //���������� ������� ������ ���������, � ������ ������ �� 1
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

          $data = sub($post, '���������:&lt;BR&gt;', '&lt;/TD&gt;');

          $post = sub($post, '&lt;TD class=&quot;msg-body&quot; valign=top width=100%&gt;', '&lt;/TD&gt;');
          $post = unhtmlentities($post);
          $post = str_replace('i/smile', '_imgs/smiles', $post);
          $post = str_replace('i/clans', '_imgs/clans', $post);
          $post = str_replace('i/i', '_imgs/pro/i', $post);

          $big = 'QWERTYUIOPASDFGHJKLZXCVBNM��������������������������������';
          $small = 'qwertyuiopasdfghjklzxcvbnm��������������������������������';

          $nicks[$i-1] = $nick;
          $nicks_uncase[$i-1] = strtr($nick, $big, $small);
          $posts[$i-1] = $post;
          $datas[$i-1] = $data;
        }

        if(!(AuthUserClan=='police' || AuthUserClan=='Military Police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy' || AuthUserGroup==100))
        {
// added by deadbeef
          if (!in_array(strtr(AuthUserName, $big, $small), $nicks_uncase) && !strpos($tmp_page, AuthUserName))
            $error = '�� �� ���������� � ���� ������';
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

          echo '<i>������ ��������� � ���������</i>';
        }
      }
      if ($error)
      {
          echo '<i>������: '.$error.'</i>';
      }
    }

  ?>
    <h1>��������� ������</h1>
  <center>

  <table border="0" cellspacing="0" background="i/bgr-grid-sand.gif" cellpadding="10" style="BORDER: 1px #957850 solid;">

  <tr><td>

<b>��������� ������</b> ������������� <u>������������������ �� ����� ����������</u> ����������� ��������� �������� ������ ��� ��� ������ �� ������ �����. ���� ���� ������ �� ������ �������� ��� ����� ������� (�������) � ��������� �������� �� ��������, �� ������ ������� ����������� ������ � ���������.<br>
<b>������� ������ � ��������� �������� ��������������� �� ������������� ��� ����������� ����.</b>
<br><br>
��� �������� ������ � ��������� ��� ����������:<br>
1. ���� ���������� �� ������ �������� ����������� ������;<br>
2. ��������� ������ �� ������ � ���� "������";<br>
3. ������ ������ "��".<br>
<br>
<u>��������: ����������� ������ 8 ��������� ������, ���� ��� ���������� ������� �� ���������� (�� �� ����� 20), ���� �� ��������� �� ������ � ����� ��������� ������ - ��������� ������ ������������ ������������ ������ � ����������� ����������� ���������.</u>
<br><br>
� ���������� �� ������� ������������� ��� ����������� ������ � ����� ��������.
<br><br>
����� ���������� ����������� ����������� ����� �� ����, ����� ������ ��� ��� � ���� "���" � ����� "��". ����� �������� ��� ����������� ������ � �������� ���������� ���������.


  </td></tr>

  </table>

  </center>

    <hr>
    <h2>�������� ������ � ���������</h2>
<?
    if (!isset($_COOKIE['CUser']))
      echo '<i>����������� �������� ������ ������������������ �������������</i>';
    else
    {
?>
    <form name="ad" method="post" action="?act=deals">
      ������: <input type="text" name="link" size="96">&nbsp;
  <?
  if(AuthUserClan=='police' || AuthUserClan=='Military Police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy' || AuthUserGroup==100)
  {
    echo '<br>���������� ������: <input type="text" name="posts" size="4" value="20">&nbsp;';
  }
  ?>
      <input type="submit" name="adddeal" value="OK">
    </form>
<?
    }
?>
    <hr>
    <h2>����� �� ����</h2>
    <form name="sd" action="" method="get">
      <input type="hidden" name="act" value="deals">
      ���: <input type="text" name="nick">&nbsp;
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
          echo '<h2>����������� ������ c ����� ��������</h2>';
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
    <h2>���������� ������� (����������)</h2>
<?

    $a = extract_date(strftime("%Y-%m-%d %H:%M:%S", mktime()));

    $week = strftime("%Y-%m-%d", mktime(0,0,0,$a[3],$a[4]-7,$a[5]));
    $month = strftime("%Y-%m-%d", mktime(0,0,0,$a[3]-1,$a[4],$a[5]));

    if (isset($_COOKIE['CUser']))
    {
//�����
      $sql = 'SELECT count(distinct d.topic_id) as cnt
              FROM deals_posts dp
              JOIN deals d on (dp.deal_id = d.id)
              WHERE dp.nick="'.AuthUserName.'"
      ';
      $res = mysql_query($sql);
      $row = mysql_fetch_array($res);
      $own_deals['total'] = $row[0];

//������
      $sql = 'SELECT count(distinct d.topic_id) as cnt
              FROM deals_posts dp
              JOIN deals d on (dp.deal_id = d.id)
              WHERE dp.nick="'.AuthUserName.'"
              AND d.add_date > "'.$week.'"
      ';
      $res = mysql_query($sql);
      $row = mysql_fetch_array($res);
      $own_deals['week'] = $row[0];

//�����
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

//�����
      $sql = 'SELECT count(id) as cnt
              FROM deals';
      $res = mysql_query($sql);
      $row = mysql_fetch_array($res);
      $all_deals['total'] = $row[0];

//������
      $sql = 'SELECT count(id) as cnt
              FROM deals
              WHERE add_date > "'.$week.'"
              ';
      $res = mysql_query($sql);
      $row = mysql_fetch_array($res);
      $all_deals['week'] = $row[0];

//�����
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
  <td bgcolor=#F4ECD4>�� ������</td>
  <td bgcolor=#F4ECD4>�� �����</td>
  <td bgcolor=#F4ECD4>�� ��� �����</td>
</tr>
<?
    if (isset($_COOKIE['CUser']))
    {
?>
<tr>
  <td bgcolor=#F4ECD4>����� ������</td>
  <td background="i/bgr-grid-sand1.gif"><?=$own_deals['week']?></td>
  <td background="i/bgr-grid-sand1.gif"><?=$own_deals['month']?></td>
  <td background="i/bgr-grid-sand1.gif"><?=$own_deals['total']?></td>
</tr>
<?
    }
?>
<tr>
  <td bgcolor=#F4ECD4>����� ������</td>
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