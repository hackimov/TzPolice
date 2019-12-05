<?php
require('/home/sites/police/www/_modules/functions.php'); // functions + DB connect
require('/home/sites/police/www/_modules/auth.php'); // authorization
require('functions.php'); // siteRating functions

$act = $_POST["act"];
switch ($act) {
    case "search":
        searchSite($_POST["site"]);
    break;
    case "free":
        printFree();
    break;
    case "my":
        printMy();
    break;
}

$mode = $_POST["mode"];

switch ($mode) {
    case "edit":
        printSite($_POST["id"]);
    break;
    case "save":
        print editSave($_POST["id"], $_POST["url"], $_POST["name"], $_POST["status"], $_POST["inspector"]);
    break;
    case "add":
        print addNewSite($_POST["url"], $_POST["name"]);
    break;
    case "delete":
        print deleteSite($_POST["id"]);
    break;
    case "attach":
        print attachSite($_POST["id"]);
    break;
}


?>
