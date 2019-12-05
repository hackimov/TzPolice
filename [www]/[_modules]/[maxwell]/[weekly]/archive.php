<?php
    require('/home/sites/police/www/_modules/functions.php'); // functions + DB connect
    require('/home/sites/police/www/_modules/auth.php'); // authorization
    require('/home/sites/police/www/_modules/maxwell/weekly/weekly_admin_define.php'); // Admins definition
//    $adminAccess = (AuthUserName == "maxwell" || AuthUserName == "Ell");


    function getArchive($offset = 0) {
        $q = "SELECT *, UNIX_TIMESTAMP(weekly_issues.date) AS date FROM `weekly_issues` WHERE `online` = 1 ORDER BY `date` DESC LIMIT $offset, 30";
        $issues = mysql_query($q)
            or die("Invalid query: " . mysql_error());
        $rv = "<table border=0>";
        while ($issue = mysql_fetch_array($issues)) {
            $rv .= "<tr>
                        <td><a href='/special/issue/".$issue['id'].".html'>".$issue['title']."</a> <span class='date'>[".date("d.m.Y H:i", $issue["date"])."]</span></td>
                    </tr>";
            $q = "SELECT weekly_articles_in_issues.article AS article, weekly_articles.title as title, weekly_articles.id AS id, weekly_articles.position AS position FROM `weekly_issues`,`weekly_articles`, `weekly_articles_in_issues` WHERE weekly_issues.id = weekly_articles_in_issues.issue AND weekly_issues.id = " . $issue["id"] . " AND weekly_articles.id = weekly_articles_in_issues.article";
            $arcticles = mysql_query($q)
                or die("Invalid query: " . mysql_error() . var_dump($q));
            # articles of issue
            while ($arcticle = mysql_fetch_array($arcticles)) {
                $rv .= "<tr>
                            <td style=\"padding-left:64px;\" valign=top><a href=\"/special/article/".$arcticle["id"].".html\">" .  $arcticle["title"] .  "</a></td></tr>\n";
            }
        }
        $rv .= "</table>";

        return $rv;

    }
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
        <td align=center><a href="/special/archive.html">Архив</a>&nbsp;&nbsp;&nbsp;<a href="/special.html">последний</a></td>
    </td>
    <tr>
        <td align=center><br><br></td>
    </td>
    <tr>
        <td style='padding-left: 64px;'>
            <h1 class=title>Архив</h1>
            <br>
            <?=getArchive()?>
        </td>

    </tr>
    <tr>
        <td width=80% height=200 valign=bottom><?php include('footer.php') ?></td>
    </tr>
</table>

</body>
</html>
