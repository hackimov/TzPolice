<?php
    require('/home/sites/police/www/_modules/functions.php'); // functions + DB connect
    require('/home/sites/police/www/_modules/auth.php'); // authorization
    require('/home/sites/police/www/_modules/maxwell/weekly/weekly_admin_define.php'); // Admins definition
//    $adminAccess = (AuthUserName == "maxwell" || AuthUserName == "Ell" || AuthUserName == "deadbeef");
    
    if ($adminAccess) {
        require_once("fckeditor/fckeditor.php");
        require_once("functions.php");
        main();
    } else {
       header('Location: http://www.tzpolice.ru/');
    }
    
   
    
    function main() {
        $action = $_GET["action"];
        if (!isset($action)) {
            $action = "allIssues";
        }
        $menu = htmlAdminMenu($action);
        
        
        switch ($action){
            case "newArticle": 
                $fields = getHTMLfieldsForArticle();
            break;
            case "addArticle": 
                $body = postArticle();
            break;
            case "editArticle":
                $fields = getHTMLfieldsForArticle($_GET["id"]);
            break;
            case "updateArticle":
                $body = updateArticle();
            break;
            case "newIssue": 
                $body = htmlIssueForm();
            break;
            case "editIssue":
                $body = editIssue($_GET["id"]);
            break;
            case "updateIssue":
                $body = updateIssue($_GET["id"]);
            break;
        }
        
?>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="admin.css" />
    <title>Admin</title>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <script src="weekly.js" type="text/javascript" encoding="UTF-8"></script>
</head>
<body onLoad="main();">
<table border=0 width=90% align=center style="padding-top:42px;" height=800>
            <tr>
                <td width=150 valign=top><?php print $menu;?></td>
                <td valign=top>
                    <div id="body">
                    </div>
                    <?php print $body;
                          if ($action == "newArticle") {
                              showFCKeditorForm("addArticle", $fields);
                          } else if ($action == "editArticle") {
                                $q = "SELECT `text` FROM `weekly_articles` WHERE id =  " . $_GET["id"] . " LIMIT 1";
                                $text = mysql_query($q)
                                    or die("Invalid query: " . mysql_error() . var_dump($q));
                                $text = mysql_fetch_array($text);
                                
                                showFCKeditorForm("updateArticle&id=" . $_GET["id"], $fields, $text[0]);
                          }
                    ?>
                </td>
            </tr>
        </table>
<div id="loading" style="visibility: hidden; position: absolute; top:0px; left:0px;">
Loading...
</div>
<div id="newIssueMenu" style="visibility: hidden; background: #c3d9ff;">
    <table>
        <tr>
            <td><input type="text" value="Название выпуска" id="newIssueTitle"></td>
            <td><button onClick="addNewIssue(); return false;">Добавить</button></td>
        </tr>
    </table>
</div>

<div id="updateIssueMenu" style="visibility: hidden; background: #c3d9ff;">
    <table>
        <tr>
            <td><input type="hidden" value=0 id="updateIssueId"></td>
            <td><input type="text" value="Название выпуска" id="updateIssueTitle"></td>
            <td><button onClick="saveIssue(); return false;">Сохранить</button></td>
        </tr>
    </table>
</div>
<div id="chStatusMenu" style="visibility: hidden; background: #c3d9ff;">
    <table>
        <tr>
            <td><img src="img/offline.gif" style="cursor: pointer;" onClick="setStatus(0)"></td>
            <td><img src="img/online.gif" style="cursor: pointer;" onClick="setStatus(1)"></td>
            <td><input type="hidden" value=0 id="issueID"><td/>
        </tr>
    </table>
</div>
        
<?php
    }
?>

</body>
</html>