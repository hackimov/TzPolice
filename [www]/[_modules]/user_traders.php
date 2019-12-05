<?php

if(AuthUserGroup == 100) {
?>
<h1>Пользователи военторга для торговцев</h1> <?
		if(@$_REQUEST["del"])
        	{
            	$query = "SELECT `restricted_area` FROM `site_users` WHERE `user_name` = '".$_REQUEST["del"]."' LIMIT 1;";
                $rs = mysql_query($query);
                list($cur_restr) = mysql_fetch_row($rs);
                $cur_restr = str_replace("-traders_shop-", "", $cur_restr);
            	$query = "UPDATE `site_users` SET `restricted_area` = '".$cur_restr."' WHERE `user_name` = '".$_REQUEST["del"]."' LIMIT 1";
                mysql_query($query);
            }
		if(@$_REQUEST["nick"])
        	{
            	$query = "SELECT `restricted_area` FROM `site_users` WHERE `user_name` = '".$_REQUEST["nick"]."' LIMIT 1;";
                $rs = mysql_query($query);
                list($cur_restr) = mysql_fetch_row($rs);
                $cur_restr .= "-traders_shop-";
            	$query = "UPDATE `site_users` SET `restricted_area` = '".$cur_restr."' WHERE `user_name` = '".$_REQUEST["nick"]."' LIMIT 1";
                mysql_query($query);
            }
		$query = "SELECT `user_name`, `restricted_area` FROM `site_users` WHERE `restricted_area` LIKE '%traders_shop%' ORDER BY `user_name`";
		$rez = mysql_query($query);
        $area[2] = "Доступ к закрытому форуму";
        $area[100] = "Все равно все узнает, удаляй - не удаляй =)";
        $bg[0]="background='i/bgr-grid-sand.gif'";
		$bg[1]="background='i/bgr-grid-sand1.gif'";
        ?>
Добавить пользователя:
<form name="add_form" method="post" action="?act=user_traders">
  <input type="text" name="nick" value="">
  <input type="submit" name="Submit" value="Добавить">
</form>

        <table width="90%"  border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td bgcolor=#F4ECD4 valign="top" nowrap><b>Ник</b></td>
<!--    <td bgcolor=#F4ECD4 valign="top" nowrap><b>Права</b></td> -->
    <td bgcolor=#F4ECD4 valign="top" nowrap width=100>&nbsp;</td>
  </tr>
        <?
        $y=0;
        while (list($u_name, $u_restr) = mysql_fetch_row($rez))
        	{
				echo"<tr>"
					."<td $bg[$y]>$u_name</td>"
//                    ."<td $bg[$y]>$area[$u_restr]</td>"
                    ."<td $bg[$y]><a href='?act=user_traders&del=$u_name' onClick=\"if(!confirm('Вы уверены?')) return false;\">Убрать доступ</a></td></tr>";
                $y++;
                if ($y>1) {$y = 0;}
            }
        ?>
        </table>
<?

} else {

	echo $mess['AccessDenied'];

}

?>