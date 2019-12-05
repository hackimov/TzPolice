<?php

function printModeratorForm() {
    echo "<input type=text class=\"searchInput\" id=\"site\">
          <button onClick=\"searchSite(); return false\" class=\"searchBtn\">search</button> <button onClick=\"getFree(); return false\" class=\"searchBtn\">show all</button>\n <br>
          <div id=\"error\"></div><br>
          <div id=\"sites\"></div><br>
          <script>var access='moderator';</script><div id='editForm' style='visibility: hidden;'></div>";
}

function printAdminForm() {
    echo "<input type=text class=\"searchInput\" id=\"site\">
          <button onClick=\"searchSite(); return false\" class=\"searchBtn\">поиск</button>
          <button onClick=\"getFree(); return false\" class=\"searchBtn\">невзятые</button>\n
          <button onClick=\"addSiteForm(event); return false\" class=\"searchBtn\">добавить</button>\n 
          <button onClick=\"getMy(); return false\" class=\"searchBtn\">мои</button>\n <br>
          <div id=\"error\"></div><br>
          <div id=\"sites\"></div><br>
          <script>var access='admin';</script><div id='editForm' style='visibility: hidden;'></div><div id='addForm' style='visibility: hidden;'></div>";
}

function printFree() {
    $q = "SELECT `siteRating_sites`.* FROM `siteRating_sites`
          WHERE `siteRating_sites`.`inspector` = 0";
    $sites = mysql_query($q)
        or die("Invalid query: " . mysql_error() . var_dump($q));
    if (mysql_num_rows($sites) > 0) {
        while ($site = mysql_fetch_array($sites)) {
            print "{'id':".$site["id"].", 
                    'url': '".$site["url"]."', 
                    'name': '".$site["name"]."', 
                    'status': ".$site["status"].", 
                    'lastCheck': '".$site["lastCheck"]."',
                    'inspector': ''}[end]";
        }
    } else {
        print "0";
    }
}

function printMy() {
    $q = "SELECT `siteRating_sites`.* FROM `siteRating_sites`
          WHERE `siteRating_sites`.`inspector` = ".AuthUserId;
    $sites = mysql_query($q)
        or die("Invalid query: " . mysql_error() . var_dump($q));
    if (mysql_num_rows($sites) > 0) {
        while ($site = mysql_fetch_array($sites)) {
            print "{'id':".$site["id"].", 
                    'url': '".$site["url"]."', 
                    'name': '".$site["name"]."', 
                    'status': ".$site["status"].", 
                    'lastCheck': '".$site["lastCheck"]."',
                    'inspector': '".AuthUserName."'}[end]";
        }
    } else {
        print "0";
    }
}

function searchSite($name) {
    $q = "SELECT `siteRating_sites`.*, `site_users`.`user_name` AS `inspector` FROM `siteRating_sites`, `site_users` WHERE 
                                                        (`name` LIKE '%".$name."%' OR `url` LIKE '%".$name."%') AND
                                                        `site_users`.`id` = `siteRating_sites`.`inspector`";
    $sites = mysql_query($q)
        or die("Invalid query: " . mysql_error() . var_dump($q));
    while ($site = mysql_fetch_array($sites)) {
        print "{'id':".$site["id"].", 
                'url': '".$site["url"]."', 
                'name': '".$site["name"]."', 
                'status': ".$site["status"].", 
                'lastCheck': '".$site["lastCheck"]."',
                'inspector': '".$site["inspector"]."'}[end]";
    }
}

function printSite($id) {
    $q = "SELECT `siteRating_sites`.*, FROM `siteRating_sites` WHERE 
                                                        `siteRating_sites`.`id` = ".$id." LIMIT 1";
    $sites = mysql_query($q)
        or die("Invalid query: " . mysql_error() . var_dump($q));
    $site = mysql_fetch_array($sites);
    print "{'id':".$site["id"].", 
            'url': '".$site["url"]."', 
            'name': '".$site["name"]."', 
            'status': ".$site["status"].", 
            'lastCheck': '".$site["lastCheck"]."',
            'inspector': '".$site["inspector"]."'}";
}

function editSave($id, $url, $name, $status, $inspector) {
    $q = "SELECT `id` FROM `site_users` WHERE `user_name` = '".$inspector."'";
    $inspector = mysql_fetch_array(mysql_query($q))
        or die("Invalid query: " . mysql_error() . var_dump($q));
    $q = "UPDATE `siteRating_sites` SET  `url` = '".$url."', `name` = '".$name."', `status` = ".$status.", `inspector` = ".$inspector['id']." WHERE `id` = ". $id . " LIMIT 1";
    $r = mysql_query($q)
        or die("Invalid query: " . mysql_error() . var_dump($q));
    return "ok";
}

function attachSite($id) {
    $q = "UPDATE `siteRating_sites` SET  `inspector` = ".AuthUserId." WHERE `id` = ".$id." LIMIT 1";
    $r = mysql_query($q)
        or die("Invalid query: " . mysql_error() . var_dump($q));
    return "ok";
}

function addNewSite($url, $name) {
    $q = "INSERT INTO `siteRating_sites` (url, name, status, inspector) VALUES('".$url."', '".$name."', 0, 0)";
    $r = mysql_query($q)
        or die("Invalid query: " . mysql_error() . var_dump($q));
    return "ok";
}

function deleteSite($id) {
    $q = "DELETE FROM `siteRating_sites` WHERE `id` = ". $id;
    mysql_query($q)
        or die("Invalid query: " . mysql_error() . var_dump($q));
    return "ok";
}

function getAccess(){
    return "admin";
}

?>
