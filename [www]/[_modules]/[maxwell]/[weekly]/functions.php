<?php

    function showFCKeditorForm($action, $fields, $value = "") {
        print "<form action=\"admin.php?action=".$action."\" method=\"post\">";
        print $fields;
        $oFCKeditor = new FCKeditor('addform') ;
        $oFCKeditor->Height = "600";
        $oFCKeditor->BasePath = 'fckeditor/' ;
        $oFCKeditor->Value = $value ;
        $oFCKeditor->Config['EnterMode'] = 'br';
        $oFCKeditor->Config['SkinPath'] = $oFCKeditor->Config['BasePath'] . 'skins/office2003/' ;

        $oFCKeditor->Create() ;

        print "<br>
                  <input type=\"submit\" value=\"Добавить\" class=\"addBtn\">
              </form>";
              
        
    }
    
    function htmlIssueForm() {
        $html = "<form action=\"admin.php?action=addIssue\" method=\"post\">";
        $html .= "   Название выпуска: <input type=\"text\" class=\"text\" name=\"title\"><br>";
        $html .= "   <input type=\"submit\" value=\"Добавить\" class=\"addBtn\"><br>";
        $html .= "</form>";
        return $html;
        
    }
    
    function htmlAdminMenu($act) {
        $class = array("newArticle" => "", "newIssue" => "", "allIssues" => "");
        $class[$act] = "class=\"current\"";
        
        $html = "<table border=0 width=100% cellpadding=2 cellspacing=0>
                    <tr ".$class["allIssues"]."><td><a href=\"admin.php\">Все выпуски</a></td></tr>
                    <tr ".$class["newIssue"]."><td><a href=\"admin.php?action=newIssue\" onClick=\"newIssue(event); return false;\">Новый выпуск</a></td></tr>
                    <tr ".$class["newArticle"]."><td><a href=\"admin.php?action=newArticle\">Добавить статью</a></td></tr>
                 </table>";
                  
        return $html;
        
    }
    
    function getOnlineStatus($online, $id) {
        switch ($online) {
            case 0: $src = "offline"; break;
            case 1: $src = "online"; break;
        }
        
        $rv .= "<img src='img/".$src.".gif' border=0 style=\"cursor: pointer\" onClick='changeStatus(event, $id);'>";
        return $rv;
    }
    
    function createPosTable($id, $pos) {
        $rv = "<table border=0 class=articlePosTable cellspacing=0 cellpadding=0>";
        $rv .= "<tr>
                    <td rowspan=2 id=\"article_".$id."_pos_1\" class=\"articlePos\" onClick=\"setArticlePos(".$id.",1);\" onMouseOver=\"this.className = this.className + '_over';\" onMouseOut=\"this.className = this.className.replace(/_over/, '');\">1</td>
                    <td id=\"article_".$id."_pos_2\" class=\"articlePos\" onClick=\"setArticlePos(".$id.",2);\" onMouseOver=\"this.className = this.className + '_over';\" onMouseOut=\"this.className = this.className.replace(/_over/, '');\">2</td>
                    <td rowspan=2 id=\"article_".$id."_pos_3\" class=\"articlePos\" onClick=\"setArticlePos(".$id.",3);\" onMouseOver=\"this.className = this.className + '_over';\" onMouseOut=\"this.className = this.className.replace(/_over/, '');\">3</td></tr>";
        $rv .= "<tr><td id=\"article_".$id."_pos_4\" class=\"articlePos\" onClick=\"setArticlePos(".$id.",4);\" onMouseOver=\"this.className = this.className + '_over';\" onMouseOut=\"this.className = this.className.replace(/_over/, '');\">4</td></tr>";
        $rv .= "<tr><td id=\"article_".$id."_pos_5\" class=\"articlePos\" onClick=\"setArticlePos(".$id.",5);\" onMouseOver=\"this.className = this.className + '_over';\" onMouseOut=\"this.className = this.className.replace(/_over/, '');\">5</td>
                    <td id=\"article_".$id."_pos_6\" class=\"articlePos\" onClick=\"setArticlePos(".$id.",6);\" onMouseOver=\"this.className = this.className + '_over';\" onMouseOut=\"this.className = this.className.replace(/_over/, '');\">6</td>
                    <td id=\"article_".$id."_pos_7\" class=\"articlePos\" onClick=\"setArticlePos(".$id.",7);\" onMouseOver=\"this.className = this.className + '_over';\" onMouseOut=\"this.className = this.className.replace(/_over/, '');\">7</td></tr>";
        $rv .= "</table>";
        $rv .= "[evalcode]highlightArticlePos(".$id.", ".$pos.")[/evalcode]";
        return $rv;
    }
    
        
    function htmlIssues() {
        $query = "SELECT *, UNIX_TIMESTAMP(weekly_issues.date) AS date FROM `weekly_issues` GROUP BY `id` ORDER BY `date` DESC";
        $issues = mysql_query ($query)
          or die("Invalid query: " . mysql_error());

        $number = mysql_num_rows($issues);
        if ($number > 0) {
            
            $rv = "<table border=0 cellspacing=0 rowspacing=0 width=80%>";
            # issues
            while ($issue = mysql_fetch_array($issues)) {
                $rv .= "<tr bgcolor=#f1f5ec>
                        <td align=right width=1%><img onClick=\"deleteIssue(".$issue["id"]."); return false;\" style=\"cursor: pointer;\" src=\"img/deleteBtn.gif\" border=0></td>
                        <td style=\"vertical-align: top\">
                        <a href=\"admin.php?action=editIssue&id=".$issue["id"]."\" onClick=\"editIssue(".$issue["id"].", '".  $issue["title"]."', event); return false;\">".  $issue["title"]."</a> <span class='date'>[".date("d.m.Y H:i", $issue["date"])."]</span> ".getOnlineStatus($issue["online"], $issue["id"])." <a href='index.php?issueId=".$issue["id"]."' target=_blank>preview</a></td></tr>\n";
                $q = "SELECT weekly_articles_in_issues.article AS article, weekly_articles.title as title, weekly_articles.id AS id, weekly_articles.position AS position FROM `weekly_issues`,`weekly_articles`, `weekly_articles_in_issues` WHERE weekly_issues.id = weekly_articles_in_issues.issue AND weekly_issues.id = " . $issue["id"] . " AND weekly_articles.id = weekly_articles_in_issues.article";
                $arcticles = mysql_query($q)
                    or die("Invalid query: " . mysql_error() . var_dump($q));
                # articles of issue
                while ($arcticle = mysql_fetch_array($arcticles)) {
                    $rv .= "<tr><td style=\"padding-left:64px;\" valign=top colspan=2><img onClick=\"deleteArticle(".$arcticle["id"]."); return false;\" style=\"cursor: pointer;\"  border=0 src=\"img/deleteBtn.gif\"><a href=\"admin.php?action=editArticle&id=".$arcticle["id"]."\">" .  
                    $arcticle["title"] .  "</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='article.php?articleId=".$arcticle["id"]."' target=_blank>preview</a></td><td>".createPosTable($arcticle["id"], $arcticle["position"])."</td></tr>\n";
                }
            }
            $rv .= "</table>";
        }
        
        return $rv;
    }
    
    function getIssues() {
        $rv = "";
        $query = "SELECT * FROM `weekly_issues` GROUP BY `id`";
        $issues = mysql_query ($query)
            or die("Invalid query: " . mysql_error());
        while ($issue = mysql_fetch_array($issues)) {
            $rv .= "{'id': ".$issue["id"].", 'title' : '".$issue["title"]."'}\n";
        }
        return $rv;
    }
    
    function postArticle() {
        $text = stripslashes( $_POST['addform'] );
        $title = htmlspecialchars(stripslashes(  $_POST['title'] ));
        $author = htmlspecialchars(stripslashes( $_POST['author'] ));
        $issue = htmlspecialchars(stripslashes( $_POST['issue'] ));
        # adding article
        $q = "INSERT INTO `weekly_articles` (`author`, `title`, `text`) VALUES ('". $author."', '". $title."', '". $text."')";
        $r = mysql_query($q)
            or die("Invalid query: " . mysql_error() . var_dump($q));
        # link
        $q = "INSERT INTO `weekly_articles_in_issues` (`article`, `issue`) VALUES (".mysql_insert_id().", ".$issue.")";
        $r = mysql_query($q)
            or die("Invalid query: " . mysql_error() . var_dump($q));
        return "<font color=green>Добавлено!</font>";
    }
    
     function postIssue() {
        $title =  iconv("utf-8", "windows-1251", htmlspecialchars(urldecode($_POST['title'])));
        $q = "INSERT INTO `weekly_issues` (title) VALUES('". $title."')";
        $r = mysql_query($q)
            or die("Invalid query: " . mysql_error() . var_dump($q));
        return "ok";
    }
    
    
    function getHTMLfieldsForArticle($id = 0) {
        $article = Array("author" => "", "title" => "", "issue" => 0);
        if ($id > 0) {
            $q = "SELECT * FROM `weekly_articles` WHERE id =  " . $id . " LIMIT 1";
                $article = mysql_query($q)
                    or die("Invalid query: " . mysql_error() . var_dump($q));
                $article = mysql_fetch_array($article);
        }
        
        $fields = "<table border=0>
                            <tr><td>Автор</td><td><input type=\"text\" name=\"author\" class=\"text\" value=\"".$article["author"]."\"></td></tr>
                            <tr><td>Заголовок</td><td><input type=\"text\" name=\"title\" class=\"text\" value=\"".$article["title"]."\"></td></tr>";
        
        $fields .= "<tr><td>Выпуск</td><td><select name=\"issue\" id=\"issue\">";
        # all issues
        $query = "SELECT * FROM `weekly_issues` GROUP BY `id`";
        $issues = mysql_query ($query)
          or die("Invalid query: " . mysql_error());
        while ($issue = mysql_fetch_array($issues)) {
            if ($issue["id"] == $id)
                $fields .= "<option value=".$issue["id"]." SELECTED>".   $issue["title"]."</option>\n";
            else
                $fields .= "<option value=".$issue["id"].">".  $issue["title"]."</option>\n";
        }
        $fields .= "</select><button onClick=\"newIssue(event); return false;\">новый</button></tr></table>";
        return $fields;
    }
    
    function updateArticle() {
        $text = stripslashes( $_POST['addform'] );
        $title = htmlspecialchars(stripslashes(  $_POST['title'] ));
        $author = htmlspecialchars(stripslashes( $_POST['author'] ));
        $issue = htmlspecialchars(stripslashes( $_POST['issue'] ));
        # updating article
        $q = "UPDATE `weekly_articles` SET  `author` = '". $author."', `title` = '". $title."', `text` = '". $text."' WHERE `id` = ".$_GET["id"] . " LIMIT 1";
        mysql_query($q)
            or die("Invalid query: " . mysql_error() . var_dump($q));
        # delete link 
        $q = "DELETE FROM `weekly_articles_in_issues` WHERE `article` = ". $_GET["id"] ;
        mysql_query($q)
            or die("Invalid query: " . mysql_error() . var_dump($q));
        # insert link
        $q = "INSERT INTO `weekly_articles_in_issues` (`article`, `issue`) VALUES (".$_GET["id"].", ".$issue.")";
        mysql_query($q)
            or die("Invalid query: " . mysql_error() . var_dump($q));
        return "<font color=green>Добавлено!</font>";
    }
    
    function editIssue($id) {
        $q = "SELECT * FROM `weekly_issues` WHERE id =  " . $id . " LIMIT 1";
        $issue = mysql_query($q)
            or die("Invalid query: " . mysql_error() . var_dump($q));
        $issue = mysql_fetch_array($issue);
        
        $html = "<form action=\"admin.php?action=updateIssue&id=".$issue["id"]."\" method=\"post\">";
        $html .= "   Название выпуска: <input type=\"text\" class=\"text\" name=\"title\" value=\"".$issue["title"]."\"><br>";
        $html .= "   <input type=\"submit\" value=\"Сохранить\" class=\"addBtn\"><br>";
        $html .= "</form>";
        
        return $html;
    }
    
    function updateIssue($id, $title) {
        $title = iconv("utf-8", "windows-1251", htmlspecialchars(stripslashes(  $title )));
        # updating issue
        $q = "UPDATE `weekly_issues` SET  `title` = '". $title."' WHERE `id` = ". $id . " LIMIT 1";
        $r = mysql_query($q)
            or die("Invalid query: " . mysql_error() . var_dump($q));
        return "ok";
    }
    
    function setArticlePos($id, $pos) {
        $q = "UPDATE `weekly_articles` SET  `position` = '". $pos."' WHERE `id` = ". $id . " LIMIT 1";
        $r = mysql_query($q)
            or die("Invalid query: " . mysql_error() . var_dump($q));
        return "ok";
    }
    
    function setIssueStatus($id, $status) {
        $q = "UPDATE `weekly_issues` SET  `online` = '". $status."' WHERE `id` = ". $id . " LIMIT 1";
        $r = mysql_query($q)
            or die("Invalid query: " . mysql_error() . var_dump($q));
        return "ok";
    }
    
    function deleteIssue($id) {
        # delete issue 
        $q = "DELETE FROM `weekly_issues` WHERE `id` = ". $id . " LIMIT 1";
        mysql_query($q)
            or die("Invalid query: " . mysql_error() . var_dump($q));        
        # delete links
        $q = "DELETE FROM `weekly_articles_in_issues` WHERE `issue` = ". $id;
        mysql_query($q)
            or die("Invalid query: " . mysql_error() . var_dump($q));
        return "ok";
    }
    
    function deleteArticle($id) {
        # delete article 
        $q = "DELETE FROM `weekly_articles` WHERE `id` = ". $id . " LIMIT 1";
        mysql_query($q)
            or die("Invalid query: " . mysql_error() . var_dump($q));        
        # delete links
        $q = "DELETE FROM `weekly_articles_in_issues` WHERE `article` = ". $id;
        mysql_query($q)
            or die("Invalid query: " . mysql_error() . var_dump($q));
        return "ok";
    }
    

?>