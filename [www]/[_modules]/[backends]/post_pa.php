<?
// Временная мера для отсечения старых логов...
$old = time() - 15552000; //(полгода)
$bg = 0;
$bgstr[0]="#D0BD9D";
$bgstr[1]="#DBA951";
require_once "../xhr_config.php";
require_once "../xhr_php.php";
require_once "../functions.php";
require_once "../auth.php";
$JsHttpRequest =& new Subsys_JsHttpRequest_Php("windows-1251");
$user = $_REQUEST['u'];
$action = $_REQUEST['a'];
$kadet = $_REQUEST['k'];
$cop = $_REQUEST['c'];
$reason = $_REQUEST['r'];
$week = $_REQUEST['w'];
$postid = $_REQUEST['p'];
//echo ($user.$action.$kadet.$cop.$reason.$week.$postid);

// Текущий статус постов
if ($action == "state")
	{
        $query="SELECT p.post_t, p.id_user, p.city, u.user_name FROM posts_report_academy p LEFT JOIN site_users u ON u.id=p.id_user WHERE p.post_g=0 ORDER BY p.city, p.post_t";
	    $rs = mysql_query($query) or die (mysql_error());
//        echo ("query OK");
	    if (mysql_num_rows($rs) > 0)
	        {
                $useronpost = "no";
//                echo ("not on post");
				$result = "";
	            while ($cur_st = mysql_fetch_array($rs))
	                {
                    	$timeonpost = floor((time() - $cur_st['post_t'])/60);
	                    $result .= $cur_st['user_name'].",";
	                    $result .= $timeonpost.",";
	                    $result .= $post_names[$cur_st['city']].",";
	                    if ($cur_st['id_user'] == $user)
	                        {
                                $useronpost = "yes";
//                                echo ("!!!on post");
                                $result .= "0;";
	                        }
	                    else
	                        {
	                            $result .= $cur_st['id_user'].";";
	                        }
	                }
//                echo ($result);
                $_RESULT = array("res" => "OK", "onpost" => $useronpost, "str" => $result);
//                echo ("НА ПОСТУ БЛЯНАХ");
	        }
	    else
	        {
	            $_RESULT = array("res" => "all_free");
//                echo ("all posts are free");
	        }
    }
// Встать на пост
elseif ($action == "take")
	{
		$curtime = time();
        $week = date("W");
	    $query="INSERT INTO `posts_report_academy` (`id`, `id_user`, `post_t`, `post_g`, `id_week`, `comment`, `city`)
        VALUES
        ('', '".$user."', '".$curtime."', '0', '".$week."', '', '".$postid."')";
	    $rs = mysql_query($query) or die (mysql_error());
	    if ($rs)
	        {
                $_RESULT = array("res" => "OK");
//                echo ("OK");
            }
        else
        	{
            	$_RESULT = array("res" => "error");
//                echo ("error");
            }
    }
// Сдать пост
elseif ($action == "give")
	{
		$curtime = time();
	    $query="SELECT `id` FROM `posts_report_academy` WHERE `post_g` < '10' AND `id_user` = '".$user."' LIMIT 1;";
	    $rs = mysql_query($query) or die (mysql_error());
		if (mysql_num_rows($rs) > 0)
        	{
            	$r = mysql_fetch_array($rs);
            	$query = "UPDATE `posts_report_academy` SET `post_g` = '".$curtime."' WHERE `id` = '".$r['id']."' LIMIT 1;";
                $rs = mysql_query($query);
                $_RESULT = array("res" => "OK");
//                echo ("POST GIVEN");
            }
        else
        	{
            	$_RESULT = array("res" => "error");
            }
    }
// Снять с поста
elseif ($action == "drop")
	{
		$curtime = time();
        $reason = strip_tags($reason);
        $reason = str_replace("\n", "<br>", $reason);
        $reason .= " <i>(".$cop.")</i>";
	    $query="SELECT `id` FROM `posts_report_academy` WHERE `post_g` = '0' AND `id_user` = '".$kadet."' LIMIT 1;";
	    $rs = mysql_query($query) or die (mysql_error());
		if (mysql_num_rows($rs) > 0)
        	{
            	$r = mysql_fetch_array($rs);
            	$query = "UPDATE `posts_report_academy` SET `comment` = '".$reason."', `post_g` = '".$curtime."' WHERE `id` = '".$r['id']."' LIMIT 1;";
                if (mysql_query($query))
                	{
		                $_RESULT = array("res" => "OK");
                    }
                else
                	{
						$_RESULT = array("res" => "error");
                    }
            }
        else
        	{
            	$_RESULT = array("res" => "error");
            }
    }
// Статистика за неделю (или две :crazy:)
if ($action == "week")
	{
		if(@$week=="") $week=date("W");
		if(strpos($week,":")===false)
        	{
            	$_sql="p.id_week='$week' AND p.post_t > '{$old}'";
            }
		else
        	{
				$_tmp=explode(":",$week,2);
				$_sql="(p.id_week='{$_tmp[0]}' OR p.id_week='{$_tmp[1]}') AND p.post_t > '{$old}'";
            }
        if ($postid !== "all")
        	{
            	$_sql .= " AND `city` = '".$postid."'";
            }
        echo ("<table cellpadding=3 width=50% cellspacing=3>");
		$query="SELECT sum(p.post_g-p.post_t) AS time, p.id_user, u.user_name FROM posts_report_academy p LEFT JOIN site_users u ON u.id=p.id_user WHERE $_sql AND p.post_g>0 GROUP BY p.id_user";
		$r=mysql_query($query) or die (mysql_error());
		echo "<th colspan=2  background='i/bgr-grid-sand.gif'>Статистика за неделю</th><tr><td background='i/bgr-grid-sand.gif' nowrap><b>Пользователь:</b></td><td background='i/bgr-grid-sand.gif' nowrap><b>кол-во минут:</b></td></tr>";
		while($d=mysql_fetch_array($r))
        	{
				$Utime_min=round($d['time']/60);
				echo "<tr background='i/bgr-grid-sand.gif'><td align=left nowrap>".$d['user_name']."</td><td nowrap> $Utime_min</td></tr>";
			}
        echo ("</table>");
	}