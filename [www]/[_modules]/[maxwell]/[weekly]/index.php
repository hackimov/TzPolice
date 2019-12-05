<?php
    require('/home/sites/police/www/_modules/functions.php'); // functions + DB connect
    require('/home/sites/police/www/_modules/auth.php'); // authorization
    require('/home/sites/police/www/_modules/maxwell/weekly/weekly_admin_define.php'); // Admins definition
//    $adminAccess = (AuthUserName == "maxwell" || AuthUserName == "Ell");
    
    function get($buffer,$from,$to,$offset=0){
        $first=strpos($buffer,$from,$offset)+strlen($from);
        $second=strpos($buffer,$to,$first);
        if(!is_numeric($first) || !is_numeric($second)) return NULL;
        $result=substr($buffer,$first,$second-$first);
        if(is_numeric($first) && is_numeric($second)) return $result;
    }
    
    function getFirstParagraph($text) {
        $text = get($text, "&lt;overview>", "&lt;/overview>");
        return $text;
    }
    
    function printIssue($id) {
        global $adminAccess;
        
        $id = addslashes($id);
        if ($adminAccess) {
            $q = "SELECT * FROM `weekly_issues` WHERE `id` = $id LIMIT 1";
        } else {
            $q = "SELECT * FROM `weekly_issues` WHERE `id` = $id AND `online` = 1 LIMIT 1";
        }
        $issue = mysql_query($q)
            or die("Invalid query: " . mysql_error());
        $count = mysql_num_rows($issue);
        if ($count > 0) {
            $r = mysql_fetch_array($issue);
            $id = $r['id'];
            $rv['title'] = $r['title'];
            $q = "SELECT `weekly_articles`.`title`,
                         `weekly_articles`.`text`, 
                         `weekly_articles`.`author`, 
                         `weekly_articles`.`id`,
                         `weekly_articles`.`position`,
                         UNIX_TIMESTAMP(`weekly_articles`.`date`) AS `date`
                  FROM `weekly_articles_in_issues`,`weekly_articles` 
                  WHERE `weekly_articles_in_issues`.`issue` = $id 
                  AND `weekly_articles_in_issues`.`article` = `weekly_articles`.`id` 
                  GROUP BY `id` 
                  ORDER BY `weekly_articles`.`position` LIMIT 7 ";
            $articles = mysql_query($q)
                or die("Invalid query: " . mysql_error());
            while ($article = mysql_fetch_array($articles)) {
                $rv[$article['position']] = $article;
                $rv[$article['position']]['text'] = getFirstParagraph($rv[$article['position']]['text']);
            }
        } else {
            // Print latest
            $rv = getLatestIssue(1);
        }
        
        return $rv;
    }
    
    function getLatestIssue($online = 1) {
        // preview
        $q = "SELECT `id`,`title` FROM `weekly_issues` WHERE `online` = $online ORDER BY `date` DESC LIMIT 1";
        $id = mysql_query($q)
            or die("Invalid query: " . mysql_error());
        $n = mysql_num_rows($id);
        if ($n > 0) {
            $r = mysql_fetch_array($id);
            $id = $r[0];
            $rv['title'] = $r['title'];
            $q = "SELECT `weekly_articles`.`title`,
                         `weekly_articles`.`text`, 
                         `weekly_articles`.`author`, 
                         `weekly_articles`.`id`,
                         `weekly_articles`.`position`,
                         UNIX_TIMESTAMP(`weekly_articles`.`date`) AS `date`
                  FROM `weekly_articles_in_issues`,`weekly_articles` 
                  WHERE `weekly_articles_in_issues`.`issue` = $id 
                  AND `weekly_articles_in_issues`.`article` = `weekly_articles`.`id` 
                  GROUP BY `id` 
                  ORDER BY `weekly_articles`.`position` LIMIT 7 ";
            $articles = mysql_query($q)
                or die("Invalid query: " . mysql_error());
            while ($article = mysql_fetch_array($articles)) {
                $rv[$article['position']] = $article;
                $rv[$article['position']]['text'] = getFirstParagraph($rv[$article['position']]['text']);
            }
            
        }
        
        return $rv;
    }
    
   
    
    function getIssue() {
        global $adminAccess;
        $rv = 0;
        $issueId = $_GET['issueId'];
        if (isset($issueId)) {
            $rv = printIssue($issueId);
        } else {
            $rv = getLatestIssue(1); // latest online
        }
        
        return $rv;
    }
    
    
    $issue = getIssue();
?>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="/_modules/maxwell/weekly/style.css" />
    <title>Полицейское обозрение</title>

<head>
<body style="margin: 0px;">
<table width=95% bgcolor=e9deb9 align=center cellpadding=0 cellspacing=0 style="border-collapse: collapse;">

    <tr> 
        <td align=right><img src="/_modules/maxwell/weekly/img/name33.jpg"></td>
        <td align=left valign=middle><img src="/_modules/maxwell/weekly/img/name3.jpg"></td>
    </tr>
    <tr>
        <td align=right>&nbsp;</td>
        <td align=center width=1%><?=$issue['title']?>&nbsp;&nbsp;&nbsp;<a href="/special/archive.html">Архив</a>&nbsp;&nbsp;&nbsp;<a href="/special">последний</a></td>
    </td>
     <tr>
        <td align=right><br><br></td>
        <td align=center><br><br></td>
    </td>
    
  
    <tr>
    <!-- 1 колонка редактора -->
        <td class="editorTbl" rowspan=2>
        <img src="/_modules/maxwell/weekly/img/editor.jpg" border=0 align=right style="padding:0; margin:0;"> 
        <h1 class=title><a href="/special/article/<?=$issue[1]['id']?>.html"><?=$issue[1]['title']?></a></h1>
        <span class=author><?=$issue[1]['author']?></span>
        <span class=date><?=date("d.m.Y", $issue[1]['date'])?></span>
        <br><br>
        <?=$issue[1]['text']?>
        <?php 
            if ($issue[1]['id']) {
                echo "<a href=\"/special/article/".$issue[1]['id'].".html\"><font size=\"150%\">...</font></a>";
            }
        ?>
        </td>
    <!-- 2 оглянись -->
        <td class="lookTbl">
        <img src="/_modules/maxwell/weekly/img/look.jpg" border=0 align=right style="padding:0; margin:0;"> 
        <h1 class=title><a href="/special/article/<?=$issue[2]['id']?>.html"><?=$issue[2]['title']?></a></h1>
        <span class=author><?=$issue[2]['author']?></span>
        <span class=date><?=date("d.m.Y", $issue[2]['date'])?></span>
        <br><br>
        <?=$issue[2]['text']?>
        <?php 
            if ($issue[2]['id']) {
                echo "<a href=\"/special/article/".$issue[2]['id'].".html\"><font size=\"150%\">...</font></a>";
            }
        ?>
        </td>
    <!-- 3 интервью -->
        <td class="interviewTbl" rowspan=2>
        <img src="/_modules/maxwell/weekly/img/interview.jpg" border=0 align=right style="padding:0; margin:0;">
        <h1 class=title><a href="/special/article/<?=$issue[3]['id']?>.html"><?=$issue[3]['title']?></a></h1>
        <span class=author><?=$issue[3]['author']?></span>
        <span class=date><?=date("d.m.Y", $issue[3]['date'])?></span>
        <br><br>
        <?=$issue[3]['text']?>
        <?php 
            if ($issue[3]['id']) {
                echo "<a href=\"/special/article/".$issue[3]['id'].".html\"><font size=\"150%\">...</font></a>";
            }
        ?>
        </td>
    </tr>

    
    
    <tr>
    <!-- 4  черная метка-->
        <td align=left valign=top style="border-bottom: 1px dashed #5c290a; padding: 10px;">
            <h1 class=title><a href="/special/article/<?=$issue[4]['id']?>.html"><?=$issue[4]['title']?></a></h1>
            <span class=author><?=$issue[4]['author']?></span>
            <span class=date><?=date("d.m.Y", $issue[4]['date'])?></span>
            <br><br>
            <?=$issue[4]['text']?>
            <?php 
            if ($issue[4]['id']) {
                echo "<a href=\"/special/article/".$issue[4]['id'].".html\"><font size=\"150%\">...</font></a>";
            }
        ?>
        </td>
    </tr>

    
    <tr>
        <!-- 5  -->
        <td class="blackTbl">
        <img src="/_modules/maxwell/weekly/img/black.jpg" border=0 align=right>
            <h1 class=title><a href="/special/article/<?=$issue[5]['id']?>.html"><?=$issue[5]['title']?></a></h1>
            <span class=author><?=$issue[5]['author']?></span>
            <span class=date><?=date("d.m.Y", $issue[5]['date'])?></span>
            <br><br>
            <?=$issue[5]['text']?>
            <?php 
            if ($issue[5]['id']) {
                echo "<a href=\"/special/article/".$issue[5]['id'].".html\"><font size=\"150%\">...</font></a>";
            }
            ?>
        </td>
        <!-- 6 Юмор -->
        <td class="humorTbl">
        <img src="/_modules/maxwell/weekly/img/humor.jpg" border=0 align=right>
        <h1 class=title><a href="/special/article/<?=$issue[6]['id']?>.html"><?=$issue[6]['title']?></a></h1>
        <span class=author><?=$issue[6]['author']?></span>
        <span class=date><?=date("d.m.Y", $issue[6]['date'])?></span>
        <br>    <br>
        <?=$issue[6]['text']?>
        <?php 
            if ($issue[6]['id']) {
                echo "<a href=\"/special/article/".$issue[6]['id'].".html\"><font size=\"150%\">...</font></a>";
            }
        ?>
        </td>
        <!-- 7 Юмор -->
        <td class="humorTbl">
<!--        <img src="/_modules/maxwell/weekly/img/humor.jpg" border=0 align=right>-->
        <h1 class=title><a href="/special/article/<?=$issue[7]['id']?>.html"><?=$issue[7]['title']?></a></h1>
        <span class=author><?=$issue[7]['author']?></span>
        <span class=date><?=date("d.m.Y", $issue[7]['date'])?></span>
        <br><br>
        <?=$issue[7]['text']?>
        <?php 
            if ($issue[7]['id']) {
                echo "<a href=\"/special/article/".$issue[7]['id'].".html\"><font size=\"150%\">...</font></a>";
            }
        ?>
        </td>
    </tr>

    <tr>
        <td colspan=3 width=80%><?php include('footer.php') ?></td>
    </tr>


</table>

</body></html>