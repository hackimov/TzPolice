<?php
// Administration scripts for SiteRating.
// © maxwell 2008

require_once('/home/sites/police/www/_modules/functions.php'); // functions + DB connect
require_once('/home/sites/police/www/_modules/auth.php'); // authorization
require_once('siteRatingFunctions.php'); // siteRating functions

$access = getAccess();

if ($access) {
?>
<html>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="/_modules/maxwell/siteRating/style.css" />
    <title>SiteRating 0.7</title>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <!-- This is needed to hide the div containing the date picker. -->
    <style type="text/css">
    div.calendar_widget { position: absolute; float: left; top: 0px; left: 0px; width:140px; height: 200px; display: none; }
    </style>
    <script type="text/javascript" src="http://www.tzpolice.ru/_modules/maxwell/siteRating/datePicker/date-picker.js"></script>
</head>

<body>
<a href="http://www.tzpolice.ru/?act=siterating_admin&action=all">Все</a>
<a href="http://www.tzpolice.ru/?act=siterating_admin&action=free">Свободные</a>
<a href="http://www.tzpolice.ru/?act=siterating_admin&action=incheck">В проверке</a>
<a href="http://www.tzpolice.ru/?act=siterating_admin&action=add">Добавить</a>
<a href="http://www.tzpolice.ru/?act=siterating_admin&action=my">Мои</a>
<?
    $action = $_GET["action"];
    $mod = $_GET["mod"];
    switch ($action) {
        case "free":
            print printFree();
        break;
        case "incheck":
            print printInCheck();
        break;
        case "all":
            print printAll();
        break;
        case "delete":
            print deleteSite($_GET["id"], $_GET["confirm"]);
        break;
        case "attach":
            print attachSite($_GET["id"]);
        break;
        case "edit":
            if ($mod == "save") {
                print saveEdit($_GET['id'], $_POST['url'], $_POST['name'], $_POST['herald'], $_POST['herald_date'], $_POST['herald_vault'], $_POST['herald_vault_date'], $_POST['bailee'], $_POST['bailee_date'], $_POST['status'], getViolationsArr($_POST), $_POST['comment']);
            } else {
                print editSite($_GET["id"]);
            }
            
        break;
        case "my":
            print printMy();
        break;
        case "add":
            if ($mod == "save") {
                print saveNew($_POST['url'], $_POST['name'], $_POST['bailee']);
            } else {
                print addNew();
            }
        break;
        default:
            print printFree();
        break;
    
    }
} else {
    echo "<font color=red>Nothing to see here!</font>";
}







?>

</body>

</html>