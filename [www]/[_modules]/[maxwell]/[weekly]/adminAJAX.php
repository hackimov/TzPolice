<?php
    require('/home/sites/police/www/_modules/functions.php'); // functions + DB connect
    require('/home/sites/police/www/_modules/auth.php'); // authorization
    
    require('/home/sites/police/www/_modules/maxwell/weekly/weekly_admin_define.php'); // authorization
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
    switch ($action) {
        case "deleteIssue":
            print deleteIssue($_GET["id"]);
        break;
        case "deleteArticle":
            print deleteArticle($_GET["id"]);
        break;
        case "addNewIssue":
            print postIssue();   
        break;   
        case "allIssues":
            print htmlIssues();
        break;
        case "selectIssues":
            print getIssues();
        break;
        case "updateIssue":
            print updateIssue($_GET["id"], $_POST["title"]);
        break;
        case "setArticlePos":
            print setArticlePos($_GET["id"], $_GET["pos"]);
        break;
        case "setIssueStatus":
            print setIssueStatus($_GET["id"], $_GET["status"]);
        break;
    }
            
}

?>