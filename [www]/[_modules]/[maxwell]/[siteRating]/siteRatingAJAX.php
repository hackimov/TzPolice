<?php
require('/home/sites/police/www/_modules/functions.php'); // functions + DB connect
require('/home/sites/police/www/_modules/auth.php'); // authorization
require_once('siteRatingFunctions.php'); // siteRating functions

$action = $_POST['action'];

if ($action == "check") {
    $site = $_POST['site'];
    print searchSite($site);
}

function searchSite($site) {
    $q = "SELECT `siteRating_sites`.* FROM `siteRating_sites` 
          WHERE (`name` LIKE '%".$site."%' OR `url` LIKE '%".$site."%')
          AND `status` > 0";
    $sites = mysql_query($q)
        or die("Invalid query: " . mysql_error() . var_dump($q));
    if (mysql_num_rows($sites) > 0) {
        $html = "<table class='siteRating_sites'><tr bgcolor='#f4ecd4' align=center><td>ссылка</td><td>название</td><td>глашатай</td><td>дата окончания</td><td>глашатай(vault)</td><td>дата окончания</td><td align=center>статус</td></tr>";
        while ($site = mysql_fetch_array($sites)) {
            $html .= "<tr>
                          <td><a href='".$site['url']."' target=_blank>".$site['url']."</td>
                          <td>".$site['name']."</td>
                          <td style='white-space: nowrap;'>".getHTMLNick($site['herald'])."</td>
                          <td>".getHTMLNick($site['herald_date'])."</td>
                          <td style='white-space: nowrap;'>".getHTMLNick($site['herald_vault'])."</td>
                          <td>".getHTMLNick($site['herald_vault_date'])."</td>
                          <td align=center>".getStatus($site['status'])."</td>
                      </tr>";
        }
        $html .= "</table>";
        return $html;
    } else {
        return "По вашему запросу сайтов не найдено.";
    }
}
?>