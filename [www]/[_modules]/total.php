<?php

if (AuthUserGroup == 100)
{
//обработка изменения пар/результатов
if ($_REQUEST['editgames_ok'])
{
  foreach($_REQUEST['name1'] as $key => $value)
  {
    if ($value)
    {
      if (!$_REQUEST['upd'][$key])
        $sql = 'INSERT INTO total_games SET r_id="'.$_REQUEST['id'].'", name1="'.$value.'",
              name2="'.$_REQUEST['name2'][$key].'"';
      else
        $sql = 'UPDATE total_games SET r_id="'.$_REQUEST['id'].'", name1="'.$value.'",
              name2="'.$_REQUEST['name2'][$key].'", result="'.$_REQUEST['win'][$key].'" WHERE id='.$_REQUEST['upd'][$key];
      mysql_query($sql);
    }
  }
}

  echo '<h1>Управление тотализатором</h1>';

?>
  <a href="?act=total&sec=rounds">Раунды тотализатора</a><br>
  <a href="?act=total&sec=bets">Ставки</a><br>
  <a href="?act=total&sec=results">Результаты</a><br>
  <hr>
<?
//Редактирование каналов
  if ($_REQUEST['sec'] == 'rounds')
  {
//обработчик добавления
    if ($_REQUEST['submit'])
    {
      if ($_REQUEST['archived'])
        $arch = 1;
      else
        $arh = 0;
      if (!$_REQUEST['edit_id'])
      {
      $query = 'INSERT INTO `total_round` ( `id` , `name` , `archived`, `jackpot` )'.
               ' VALUES (\'\', \''.$_REQUEST['name'].'\', \''.$arch.'\', \''.$_REQUEST['jackpot'].'\')';

      }
      else
        $query = 'UPDATE total_round SET name="'.$_REQUEST['name'].'",archived="'.$arch.'", jackpot="'.$_REQUEST['jackpot'].'" WHERE id='.$_REQUEST['edit_id'];

      mysql_query($query) or die(mysql_error());
    }
//обработчик удаления
    if ($_REQUEST['delid'])
    {
      $query = 'DELETE FROM total_round WHERE id='.$_REQUEST['delid'];
      mysql_query($query) or die(mysql_error());
      $query = 'DELETE FROM total_games WHERE r_id='.$_REQUEST['delid'];
      mysql_query($query) or die(mysql_error());
      $query = 'DELETE FROM total_bets WHERE r_id='.$_REQUEST['delid'];
      mysql_query($query) or die(mysql_error());
      echo '<i>Запись успешно удалена</i>';
    }

    echo '<h2>Раунды тотализатора</h2>';
    $query = 'SELECT * FROM total_round';
    $res = mysql_query($query);
    echo '<table>';

    $name = '';
    $archived = 0;
    $jackpot= '';

    while ($row = mysql_fetch_assoc($res))
    {
      if ($row['id'] == $_REQUEST['editid'])
      {
        $name = $row['name'];
        $archived = $row['archived'];
        $jackpot = $row['jackpot'];
      }

      echo '<tr>';
        echo '<td width="100">';
          echo $row['name'];
        echo '</td>';
        echo '<td>';
          echo '<a href="?act=total&sec=rounds&editid='.$row['id'].'">редактировать</a> || <a href="?act=total&sec=editgames&id='.$row['id'].'">редактировать состав</a> || <a href="?act=total&sec=rounds&delid='.$row['id'].'" onClick="if(!confirm(\'Вы уверены?\')) {return false}">удалить</a>';
        echo '</td>';
      echo '</tr>';
    }
    echo '</table><br>';
    if (!$name)
      echo '<b>Добавить информацию</b>';
    else
      echo '<b>Редактировать информацию</b>';
    echo '<form method="post" action="/index.php">';
      echo 'Название: <input type="text" name="name" value="'.$name.'"><br>';
      echo 'Джекпот: <input type="text" name="jackpot" value="'.$jackpot.'"><br>';
      if ($archived)
        echo 'Архивный: <input type="checkbox" name="archived" value="1" checked><br>';
      else
        echo 'Архивный: <input type="checkbox" name="archived" value="1"><br>';
      echo '<input type="hidden" name="act" value="total">';
      echo '<input type="hidden" name="sec" value="rounds">';
      if ($name)
        echo '<input type="hidden" name="edit_id" value="'. $_REQUEST['editid'] .'">';
      if ($name)
        echo '<input type="submit" name="submit" value="Изменить">';
      else
        echo '<input type="submit" name="submit" value="Добавить">';
    echo '</form>';
  }
//Список игр в раунде
  elseif ($_REQUEST['sec'] == 'editgames')
  {
    echo '<h2>Список игр в раунде</h2>';
    echo '<form action="/index.php" method="post">';
    echo '<table>';
?>
<tr>
  <td rowspan=2 valign=top width=10>№</td>
  <td rowspan=2 valign=top>Первая команда</td>
  <td rowspan=2 valign=top>Вторая команда</td>
  <td colspan=3 align=center>Результат</td>
</tr>
<tr align=center>
  <td>1</td>
  <td>X</td>
  <td>2</td>
</tr>

<?

      $sql = 'SELECT * FROM total_games WHERE r_id='.$_REQUEST['id'].' order by id';
      $r = mysql_query($sql);
      $i = 1;
      while ($row = mysql_fetch_assoc($r))
      {
        echo '<tr>';
          echo '<td width=10>'.$i.'</td>';
          echo '<td><input type="hidden" name="upd['.$i.']" value="'.$row['id'].'"><input type="text" name="name1['.$i.']" value="'.$row['name1'].'" size=40></td>';
          echo '<td><input type="text" name="name2['.$i.']" value="'.$row['name2'].'" size=40></td>';
          if ($row['result'] == 0)
            echo '<td><input type="radio" name="win['.$i.']" value="0" checked></td>';
          else
            echo '<td><input type="radio" name="win['.$i.']" value="0"></td>';
          if ($row['result'] == 1)
            echo '<td><input type="radio" name="win['.$i.']" value="1" checked></td>';
          else
            echo '<td><input type="radio" name="win['.$i.']" value="1"></td>';
          if ($row['result'] == 2)
            echo '<td><input type="radio" name="win['.$i.']" value="2" checked></td>';
          else
            echo '<td><input type="radio" name="win['.$i.']" value="2"></td>';
        echo '</tr>';
        $i++;
      }
      for ($i; $i<=15; $i++)
      {
        echo '<tr>';
          echo '<td width=10>'.$i.'</td>';
          echo '<td><input type="text" name="name1['.$i.']" size=40></td>';
          echo '<td><input type="text" name="name2['.$i.']" size=40></td>';
          echo '<td colspan=3>&nbsp;</td>';
        echo '</tr>';
      }
    echo '</table>';
    echo '<input type="hidden" name="act" value="total">';
    echo '<input type="hidden" name="id" value="'.$_REQUEST['id'].'">';
    echo '<input type="submit" name="editgames_ok" value="Отправить">';

    echo '</form>';
  }


//Список ставок
  elseif ($_REQUEST['sec'] == 'bets')
  {
    if ($_REQUEST['makebet_ok'])
    {
      $bets = '';
      $i = 1;
      foreach ($_REQUEST['win'] as $key => $value)
      {
        if ($i == 1)
        {
          $bets .= $value;
          $i = 0;
        }
        else
          $bets .= ':'.$value;
      }
        $sql = 'INSERT INTO total_bets SET r_id="'.$_REQUEST['id'].'", nick="'.$_REQUEST['nick'].'",
                bets="'.$bets.'", sum="'.$_REQUEST['sum'].'"';
        mysql_query($sql);

    }

    echo '<h2>Ставки</h2>';
    $query = 'SELECT name, id FROM total_round WHERE archived = 0';
    $res = mysql_query($query);

    echo '<table>';
      echo '<tr>';
        echo '<td>';
          echo 'Название';
        echo '</td>';
        echo '<td>';
          echo 'Сумма ставок';
        echo '</td>';
        echo '<td>';
          echo 'Опции';
        echo '</td>';
      echo '</tr>';
    while ($row = mysql_fetch_assoc($res))
    {
      echo '<tr>';
        echo '<td width="100">';
          echo $row['name'];
        echo '</td>';
        echo '<td width="100">';
          $query = 'SELECT sum FROM total_bets WHERE r_id='.$row['id'];
          $res2 = mysql_query($query);
          $sum = 0;
          while ($row2 = mysql_fetch_array($res2))
          {
            $sum += $row2[0];
          }
          echo $sum;
        echo '</td>';
        echo '<td>';
          echo '<a href="?act=total&sec=makebet&id='.$row['id'].'">сделать ставку</a> || <a href="?act=total&sec=betlist&id='.$row['id'].'">список ставок</a>';
//          echo '<a href="?act=total&sec=rounds&editid='.$row['id'].'">редактировать</a> || <a href="?act=total&sec=editgames&id='.$row['id'].'">редактировать состав</a> || <a href="?act=total&sec=rounds&delid='.$row['id'].'" onClick="if(!confirm(\'Вы уверены?\')) {return false}">удалить</a>';
        echo '</td>';
      echo '</tr>';
    }
  }

//добавление ставки
  elseif ($_REQUEST['sec'] == 'makebet')
  {
    if ($_REQUEST['makebet_parse'])
    {    	$betz = explode("\n", $_REQUEST['makebet_toparse']);
    	foreach ($betz as $value)
    	{    	  $betz_2 = explode('.', $value);
    	  $makebetz[trim($betz_2[0])] = trim($betz_2[1]);
    	}
    }

    echo '<h2>Сделать ставку</h2>';
    echo '<form action="/index.php" method="post">';

    $sql = 'SELECT * FROM total_games WHERE r_id='.$_REQUEST['id'].' order by id';
    $r = mysql_query($sql);
    if (mysql_num_rows($r) != 15)
    {
      echo '<b>Ошибка. В раунде должно быть 15 игр</b>';
    }
    else
    {
      echo 'Ник: <input type="text" name="nick">&nbsp;&nbsp;';
      echo 'Ставка: <input type="text" name="sum">';
      echo '<table>';
?>
<tr>
  <td rowspan=2 valign=top width=10>№</td>
  <td rowspan=2 valign=top>Первая команда</td>
  <td rowspan=2 valign=top>Вторая команда</td>
  <td colspan=3 align=center>Результат</td>
</tr>
<tr align=center>
  <td>1</td>
  <td>X</td>
  <td>2</td>
</tr>
<?

      $i = 0;
      while ($row = mysql_fetch_assoc($r))
      {
        echo '<tr>';
          echo '<td width=10>'.++$i.'</td>';
          echo '<td>'.$row['name1'].'</td>';
          echo '<td>'.$row['name2'].'</td>';
          echo '<td><input type="radio" name="win['.$row['id'].']" value="0"';
          if ($makebetz[$i] == '1')
            echo ' checked';
          echo '></td>';
          echo '<td><input type="radio" name="win['.$row['id'].']" value="1"';
          if ($makebetz[$i] == 'x' or $makebetz[$i] == 'X' or $makebetz[$i] == 'х' or $makebetz[$i] == 'Х')
            echo ' checked';
          echo '></td>';
          echo '<td><input type="radio" name="win['.$row['id'].']" value="2"';
          if ($makebetz[$i] == '2')
            echo ' checked';
          echo '></td>';
        echo '</tr>';
      }
      echo '</table>';
      echo '<input type="hidden" name="act" value="total">';
      echo '<input type="hidden" name="sec" value="bets">';
      echo '<input type="hidden" name="id" value="'.$_REQUEST['id'].'">';
      echo '<input type="submit" name="makebet_ok" value="Отправить">';
      echo '</form>';
    }
      echo '<br><br>или<br><br>';
      echo '<form action="/index.php" method="post">';
      echo '<textarea name="makebet_toparse" cols=60 rows=10></textarea><br>';
      echo '<input type="hidden" name="act" value="total">';
      echo '<input type="hidden" name="sec" value="makebet">';
      echo '<input type="hidden" name="id" value="'.$_REQUEST['id'].'">';
      echo '<input type="submit" name="makebet_parse" value="Отправить">';
      echo '</form>';
  }

//список ставок
  elseif ($_REQUEST['sec'] == 'betlist')
  {
    if ($_REQUEST['delid'])
    {
      $sql = 'DELETE FROM total_bets WHERE id='.$_REQUEST['delid'];
      mysql_query($sql);
      echo '<i>Ставка успешно удалена</i>';

    }

    echo '<h2>Список ставок</h2>';
    echo '<form action="/index.php" method="post">';

    $sql = 'SELECT * FROM total_bets WHERE r_id='.$_REQUEST['id'];
    $r = mysql_query($sql);
    echo '<table width="90%"  border="0" cellspacing="3" cellpadding="2" align="center">';
?>
<tr>
  <td bgcolor=#F4ECD4>Удалить</td>
  <td bgcolor=#F4ECD4>Ник</td>
  <td bgcolor=#F4ECD4>Ставка</td>
<?
  for ($i = 1; $i<=15; $i++)
  {
    echo '<td bgcolor=#F4ECD4>'.$i.'</td>';
  }
?>
</tr>
<?

      while ($row = mysql_fetch_assoc($r))
      {

        $bets = explode(':', $row['bets']);
        echo '<tr>';
          echo '<td width=50 background="i/bgr-grid-sand1.gif"><a href="?act=total&sec=betlist&id='.$_REQUEST['id'].'&delid='.$row['id'].'" onClick="if(!confirm(\'Вы уверены?\')) {return false}">[X]</a></td>';
          echo '<td width=50 background="i/bgr-grid-sand1.gif">'.$row['nick'].'</td>';
          echo '<td width=50 background="i/bgr-grid-sand1.gif">'.$row['sum'].'</td>';
          foreach ($bets as $key => $value)
          {
            switch ($value)
            {
              case '0':
                $bet = '1';
              break;
              case '1':
                $bet = 'X';
              break;
              case '2':
                $bet = '2';
              break;
            }
            echo '<td width=10 background="i/bgr-grid-sand1.gif">'.$bet.'</td>';
          }
        echo '</tr>';
      }
      echo '</table>';
  }

//Результаты не-архивных раундов
  elseif ($_REQUEST['sec'] == 'results')
  {

    echo '<h2>Результаты</h2>';
    $query = 'SELECT * FROM total_round WHERE archived = 0';
    $res = mysql_query($query);

    while ($row = mysql_fetch_assoc($res))
    {
      $old_jackpot = $row['jackpot'];
      $jackpot=0;
      $total_bets = 0;
      unset($winners_nick);
      unset($winners_bet);
      unset($stavok);
      unset($games_arr);
      unset($ugadal);
      unset($bets_arr);
      unset($pays);

      $query = 'SELECT sum FROM total_bets WHERE r_id='.$row['id'];
      $res2 = mysql_query($query);
      $total_bets = 0;
      while ($row2 = mysql_fetch_array($res2))
      {
        $total_bets += $row2[0];
      }

      echo '<h3>'.$row['name'].'</h3>';
      $query = 'SELECT * FROM total_games WHERE r_id='.$row['id'].' order by id';
      $games = mysql_query($query);
      $games_res = '';
      $i = 1;
      while ($games_row = mysql_fetch_assoc($games))
      {
        if ($i == 1)
        {
          $games_res .= $games_row['result'];
          $i = 0;
        }
        else
        {
          $games_res .= ':'.$games_row['result'];
        }
      }
      $games_arr = explode(':', $games_res);
      $query = 'SELECT * FROM total_bets WHERE r_id='.$row['id'];
      $bets = mysql_query($query);
      while ($bets_row = mysql_fetch_assoc($bets))
      {
        $ugadal = 0;
        $bets_arr = explode(':', $bets_row['bets']);
        foreach ($games_arr as $key => $value)
        {
          if ($value == $bets_arr[$key])
            $ugadal++;
        }
//если 10 и больше - заносим в список победителей
        if ($ugadal >= 10)
        {
          for ($z = 10; $z<= $ugadal; $z++)
          {
            $winners_nick[$z][$bets_row['id']] = $bets_row['nick'];
            $winners_bet[$z][$bets_row['id']] = $bets_row['sum'];
            $stavok[$z] += $bets_row['sum'];
          }
        }
      }

$no_win = 1;
//проверяем 15х
  if (is_array($winners_nick[15]))
  {
    foreach ($winners_nick[15] as $key => $value)
    {
      $topay = ($winners_bet[15][$key]/$stavok[15])*$total_bets*0.05;
      $pays[$value] += round($topay);
      if ($old_jackpot)
      {
        $topay = ($winners_bet[15][$key]/$stavok[15])*$old_jackpot;
        $pays[$value] += round($topay);
      }
    }
    $no_win = 0;
  }
  else
    $jackpot += $total_bets*0.05;


//проверяем 14х
  if (is_array($winners_nick[14]))
  {
    foreach ($winners_nick[14] as $key => $value)
    {
      $topay = ($winners_bet[14][$key]/$stavok[14])*$total_bets*0.05;
      $pays[$value] += round($topay);
    }
    $no_win = 0;
  }
  elseif ($no_win)
  {
    $jackpot += $total_bets*0.05;
    if ($old_jackpot)
      $jackpot += $old_jackpot;
  }

//проверяем 13х
  if (is_array($winners_nick[13]))
  {
    foreach ($winners_nick[13] as $key => $value)
    {
      $topay = ($winners_bet[13][$key]/$stavok[13])*$total_bets*0.05;
      $pays[$value] += round($topay);
    }
    $no_win = 0;
  }
  elseif ($no_win)
    $jackpot += $total_bets*0.05;

//проверяем 12х
  if (is_array($winners_nick[12]))
  {
    foreach ($winners_nick[12] as $key => $value)
    {
      $topay = ($winners_bet[12][$key]/$stavok[12])*$total_bets*0.05;
      $pays[$value] += round($topay);
    }
    $no_win = 0;
  }
  elseif ($no_win)
    $jackpot += $total_bets*0.05;

//проверяем 11х
  if (is_array($winners_nick[11]))
  {
    foreach ($winners_nick[11] as $key => $value)
    {
      $topay = ($winners_bet[11][$key]/$stavok[11])*$total_bets*0.2;
      $pays[$value] += round($topay);
    }
    $no_win = 0;
  }
  elseif ($no_win)
  {
    $jackpot += $total_bets*0.2;
  }

//проверяем 10х
  if (is_array($winners_nick[10]))
  {
    foreach ($winners_nick[10] as $key => $value)
    {
      $topay = ($winners_bet[10][$key]/$stavok[10])*$total_bets*0.45;
      $pays[$value] += round($topay);
    }
    $no_win = 0;
  }
  elseif ($no_win)
    $jackpot += $total_bets*0.45;

  $left = $total_bets-$jackpot+$old_jackpot;

  if ($no_win)
    $jackpot += $old_jackpot;
  if (is_array($pays))
    arsort($pays);

    echo '<table width="50%"  border="0" cellspacing="3" cellpadding="2" align="center">';
?>
<tr>
  <td bgcolor=#F4ECD4>Ник</td>
  <td bgcolor=#F4ECD4>Выигрыш</td>
</tr>
<?
  echo '<tr>';
    echo '<td width=50 background="i/bgr-grid-sand1.gif"><b>Всего ставок</b></td>';
    echo '<td width=50 background="i/bgr-grid-sand1.gif">'.$total_bets.'</td>';
  echo '</tr>';
  if (is_array($pays))
  {
    foreach ($pays as $key => $value)
    {
      echo '<tr>';
        echo '<td width=50 background="i/bgr-grid-sand1.gif">'.$key.'</td>';
        echo '<td width=50 background="i/bgr-grid-sand1.gif">'.$value.'</td>';
      echo '</tr>';
      $left -= $value;
    }
  }
  echo '<tr>';
    echo '<td width=50 background="i/bgr-grid-sand1.gif"><b>Осталось</b></td>';
    echo '<td width=50 background="i/bgr-grid-sand1.gif">'.$left.'</td>';
  echo '</tr>';

  echo '<tr>';
    echo '<td width=50 background="i/bgr-grid-sand1.gif"><b>Джекпот этого раунда</b></td>';
    echo '<td width=50 background="i/bgr-grid-sand1.gif">'.$jackpot.'</td>';
  echo '</tr>';

    echo '</table>';

    }
  }


//конец ))
}

else

{

echo $mess['AccessDenied'];

}



?>