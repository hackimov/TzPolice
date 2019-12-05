<?php
    require('/home/sites/police/www/_modules/functions.php'); // functions + DB connect
    require('/home/sites/police/www/_modules/auth.php'); // authorization
    require('/home/sites/police/www/_modules/maxwell/weekly/weekly_admin_define.php'); // Admins definition 
//    $adminAccess = (AuthUserName == "maxwell" || AuthUserName == "Ell");
    
    function getArticle($id) {
        global $adminAccess;
        
        if ($adminAccess) {
            $q = "SELECT `weekly_articles`.`title`,
                         `weekly_articles`.`text`, 
                         `weekly_articles`.`author`, 
                         `weekly_articles`.`id`,
                         `weekly_articles`.`position`,
                         UNIX_TIMESTAMP(`weekly_articles`.`date`) AS `date`
                 FROM `weekly_articles` WHERE `id` = $id LIMIT 1";
        } else {
            $q = "SELECT `weekly_articles`.`title`,
                         `weekly_articles`.`text`, 
                         `weekly_articles`.`author`, 
                         `weekly_articles`.`id`,
                         `weekly_articles`.`position`,
                         UNIX_TIMESTAMP(`weekly_articles`.`date`) AS `date`
                  FROM `weekly_articles`,`weekly_issues`,`weekly_articles_in_issues`
                  WHERE `weekly_articles`.`id` = $id 
                  AND `weekly_articles_in_issues`.`article` = $id
                  AND `weekly_issues`.`id` = `weekly_articles_in_issues`.`issue`
                  AND `weekly_issues`.`online` = 1";
        }
        $article = mysql_query($q)
            or die("Invalid query: " . mysql_error() . var_dump($q));
        $article = mysql_fetch_array($article);
        
        return $article;
    }
    
    $id = addslashes($_GET['articleId']);
    if (isset($id)) {
        $article = getArticle($id);
    }
    # cutting off OVERVIEW tags in article
    $article['text'] = str_replace('&lt;overview>', '', $article['text']);
    $article['text'] = str_replace('&lt;/overview>', '', $article['text']);
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="/_modules/maxwell/weekly/style.css" />
    <title>Полицейское обозрение</title>

<head>
<body>
<table width=100% bgcolor=e9deb9 align=center cellpadding=0 cellspacing=0 style="border-collapse: collapse" border=0>
    <tr> 
        <td align=center valign=middle><img src="/_modules/maxwell/weekly/img/name33.jpg"><img src="/_modules/maxwell/weekly/img/name3.jpg"></td>
    </tr>
    <tr>
        <td align=center><a href="/special/archive.html">Архив</a>&nbsp;&nbsp;&nbsp;<a href="/special">последний</a></td>
    </td>
    <tr>
        <td align=center><br><br></td>
    </td>
    <tr>
        <td style='padding-left: 64px;'>
            <h1 class=title><?=$article['title']?></h1>
            <span class=author><?=$article['author']?></span>
            <span class=date><?=date("d.m.Y", $article['date'])?></span>
            <br><br>
            <?=$article['text']?>
        </td>
    
    </tr>
    <tr>
        <td width=80% height=200 valign=bottom><?php include('footer.php') ?></td>
    </tr>
</table>

</body>
</html>