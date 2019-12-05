<html>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <title>SiteRating 0.7</title>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <script src="siteRec.js" type="text/javascript" encoding="UTF-8"></script>
</head>

<body>
<?php
require('/home/sites/police/www/_modules/functions.php'); // functions + DB connect
require('/home/sites/police/www/_modules/auth.php'); // authorization
require('functions.php'); // siteRating functions

$access = getAccess();

switch ($access) {
    case "moderator":
        printModeratorForm();
    break;
    case "admin":
        printAdminForm();
    break;
}





?>

</body>

</html>