<?php

if (AuthStatus==1 && substr_count(AuthUserRestrAccess, "bl_clans") > 0 || AuthUserGroup=='100')
{
  echo '<h1>Модерация клановых ЧС</h1>';

?>
  <a href="?act=bl_clans&sec=channels">Источники</a><br>
  <a href="?act=bl_clans&sec=moderate">Модерировать список</a><br>
  <hr>
  <a href="?act=bl_clans&sec=refresh" onClick="if(!confirm('Вы уверены?')) {return false}">Обновить данные</a><br>
  <hr>
<?
//Редактирование каналов
  if ($_REQUEST['sec'] == 'channels')
  {
//обработчик добавления
    if ($_REQUEST['submit'])
    {
      if (!$_REQUEST['edit_id'])
      {
      $query = 'INSERT INTO `blclans_sources` ( `source_id` , `source_name` , `source_url` )'.
               ' VALUES (\'\', \''.$_REQUEST['clan'].'\', \''.$_REQUEST['url'].'\')';
      }
      else
      $query = 'UPDATE blclans_sources SET source_name="'.$_REQUEST['clan'].'", source_url="'.$_REQUEST['url'].'" WHERE source_id='.$_REQUEST['edit_id'];
      mysql_query($query) or die(mysql_error());
    }
//обработчик удаления
    if ($_REQUEST['delid'])
    {
      $query = 'DELETE FROM blclans_sources WHERE source_id='.$_REQUEST['delid'];
      mysql_query($query) or die(mysql_error());
      echo '<i>Запись успешно удалена</i>';
    }

    echo '<h2>Источники</h2>';
    $query = 'SELECT * FROM blclans_sources';
    $res = mysql_query($query);
    echo '<table>';

    $clan = '';
    $url = '';

    while ($row = mysql_fetch_assoc($res))
    {
      if ($row['source_id'] == $_REQUEST['editid'])
      {
        $clan = $row['source_name'];
        $url = $row['source_url'];
      }

      echo '<tr>';
        echo '<td width="30">';
          echo $row['source_name'];
        echo '</td>';
        echo '<td>';
          echo '<a href="?act=bl_clans&sec=channels&editid='.$row['source_id'].'">редактировать</a> || <a href="?act=bl_clans&sec=channels&delid='.$row['source_id'].'" onClick="if(!confirm(\'Вы уверены?\')) {return false}">удалить</a>';
        echo '</td>';
      echo '</tr>';
    }
    echo '</table><br>';
    if (!$clan)
      echo '<b>Добавить информацию</b>';
    else
      echo '<b>Редактировать информацию</b>';
    echo '<form method="post" action="/index.php">';
      echo 'Клан: <input type="text" name="clan" value="'.$clan.'"><br>';
      echo 'Источник: <input type="text" name="url" value="'.$url.'"><br>';
      echo '<input type="hidden" name="act" value="bl_clans">';
      echo '<input type="hidden" name="sec" value="channels">';
      if ($clan)
        echo '<input type="hidden" name="edit_id" value="'. $_REQUEST['editid'] .'">';
      if ($clan)
        echo '<input type="submit" name="submit" value="Изменить">';
      else
        echo '<input type="submit" name="submit" value="Добавить">';
    echo '</form>';
  }
//Модерация
  elseif ($_REQUEST['sec'] == 'moderate')
  {
    if ($_REQUEST['submit'])
    {
      $query = 'UPDATE blclans_recs SET rec_is_moderate=1, rec_is_ok=1 WHERE rec_id='.$_REQUEST['submit'];
      mysql_query($query) or die(mysql_error());
    }

    if ($_REQUEST['notsubmit'])
    {
      $query = 'UPDATE blclans_recs SET rec_is_moderate=1, rec_is_ok=0 WHERE rec_id='.$_REQUEST['notsubmit'];
      mysql_query($query) or die(mysql_error());
    }

    echo '<h2>Модерация</h2>';

    $query = 'SELECT * FROM blclans_sources';
    $res = mysql_query($query) or die(mysql_error());
    while ($clan_row = mysql_fetch_assoc($res))
    {
      $query = 'SELECT * FROM blclans_recs WHERE rec_is_moderate=0 and rec_clan_id='.$clan_row['source_id'];
      $recs = mysql_query($query) or die(mysql_error());
      if (mysql_num_rows($recs))
      {
        echo '<center><b>'.$clan_row['source_name'].'</b></center><br><br>';
        echo '<table width="100%" cellpadding="5">';
          echo '<tr align="center">';
            echo '<td bgcolor=#F4ECD4 valign="top" nowrap><b>Ник</b></td>';
            echo '<td bgcolor=#F4ECD4 valign="top" nowrap><b>Причина</b></td>';
            echo '<td bgcolor=#F4ECD4 valign="top" nowrap><b>Действия</b></td>';
          echo '</tr>';
          while ($recs_row = mysql_fetch_assoc($recs))
          {
            echo '<tr>';
              echo '<td>';
                echo $recs_row['rec_nick'];
              echo '</td>';
              echo '<td>';
                echo $recs_row['rec_why'];
              echo '</td>';
              echo '<td>';
                echo '<a href="?act=bl_clans&sec=moderate&submit='.$recs_row['rec_id'].'" onClick="if(!confirm(\'Вы уверены?\')) {return false}">катит</a>';
                echo ' || ';
                echo '<a href="?act=bl_clans&sec=moderate&notsubmit='.$recs_row['rec_id'].'" onClick="if(!confirm(\'Вы уверены?\')) {return false}">не катит</a>';
              echo '</td>';
            echo '</tr>';
          }
        echo '</table>';
      }
    }
  }

//Обновление
  elseif ($_REQUEST['sec'] == 'refresh')
  {
    $query = 'SELECT * FROM blclans_sources';
    $clans = mysql_query($query);
    while ($clans_row = mysql_fetch_assoc($clans))
    {
      $countof_add = 0;
      $countof_del = 0;
      $query = 'SELECT * FROM blclans_recs WHERE rec_clan_id='.$clans_row['source_id'];
      $bls = mysql_query($query);
      $bl_arr = array();
      while ($bls_row = mysql_fetch_assoc($bls))
      {
        $bl_arr[] = $bls_row['rec_nick'];
      }
      $clan = $clans_row['source_name'];
      $clan_bl = file($clans_row['source_url']);
      foreach ($clan_bl as $key => $value)
      {
        if (strpos($value, 'CHAR'))
        {
          $nick = explode('nick="', $value);
          $nick = explode('" why', $nick[1]);
          $nick = iconv("UTF-8","windows-1251",$nick[0]);

          $why = explode('why="', $value);
          $why = explode('" who', $why[1]);
          $why = iconv("UTF-8","windows-1251",$why[0]);

          $who = explode('who="', $value);
          $who = explode('">', $who[1]);
          $who = iconv("UTF-8","windows-1251",$who[0]);

          if (!in_array($nick, $bl_arr))
          {
            $query = 'INSERT INTO `blclans_recs` ( `rec_id` , `rec_nick` , `rec_why` , `rec_clan_id` , `rec_is_moderate` , `rec_is_ok` )
                      VALUES (\'\', \''.$nick.'\', \''.$why.'\', \''.$clans_row['source_id'].'\', \'0\', \'0\');';
            mysql_query($query) or die(mysql_error());
            $countof_add++;
          }
          else
          {
            $place = array_search($nick, $bl_arr);
            unset($bl_arr[$place]);
          }
        }
      }
      foreach ($bl_arr as $key => $value)
      {
        $query = 'DELETE FROM blclans_recs WHERE rec_nick="'.$value.'"';
        mysql_query($query) or die(mysql_error());
        $countof_del++;
      }
      echo '<b>'.$clans_row['source_name'].':</b> Добавлено '. $countof_add .', удалено '.$countof_del .' человек';
    }
  }
}

else

{

echo $mess['AccessDenied'];

}



?>