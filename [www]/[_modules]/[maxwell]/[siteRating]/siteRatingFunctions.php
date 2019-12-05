<?php
function getAccess(){
    if (AuthUserName == 'ShadowGirl' || AuthUserName == 'maxwell' || AuthUserId == 22802){
        return true;
    } else {
        return false;
    }
}

function getInspector($id) {
    if (!empty($id)) {
        $sSQL = "SELECT `site_users`.`user_name` FROM `site_users` WHERE `id` = ".$id;
        $inspector = mysql_query($sSQL)
            or die("Invalid query: " . mysql_error() . var_dump($sSQL));
        $inspector = mysql_fetch_array($inspector);
        return $inspector[0];
    } else {
        return "none";
    }
}

function getHTMLNick($nick) {
    $user = GetUserInfo($nick);
    if ($user['login']) {
        return "<img src='/_imgs/clans/".$user['clan'].".gif' alt='".$user['clan']."' border='0'><b>".$user['login']."</b> [".$user['level']."]<a href='http://www.timezero.ru/info.ru.html?".$user['login']."' target=_blank><img style='vertical-align:text-bottom' src='/_imgs/pro/i".$user['pro'].".gif' border='0'></a>";
    } else {
        return "none";
    }
}

function printSites($sites, $special = 'none') {
    $rv = "<table class='sites'>";
    $rv .= "<tr>
            <td>#</td>
            <td>url</td>
            <td>name</td>
            <td>глашатай</td>
            <td>дата окончания</td>
            <td>глашатай(vault)</td>
            <td>дата окончания</td>
            <td>ответственный</td>
            <td align=center>status</td>
            <td>mod date</td>
            <td>inspector</td></tr>";
    for ($i = 0; $i < count($sites); $i++) {
        $site = $sites[$i];
        $rv .= "<tr>";
        $rv .= "<td>".($i + 1)."</td>
                <td><a href='".$site['url']."' target=_blank>".$site['url']."</td>
                <td>".$site['name']."</td>
                <td style='white-space: nowrap;'>".getHTMLNick($site['herald'])."</td>
                <td>".$site['herald_date']."</td>
                <td style='white-space: nowrap;'>".getHTMLNick($site['herald_vault'])."</td>
                <td>".$site['herald_vault_date']."</td>
                <td style='white-space: nowrap;'>".getHTMLNick($site['bailee'])."</td>
                <td align=center>".getStatus($site['status'])."</td>
                <td>".$site['lastCheck']."</td>
                <td style='white-space: nowrap;'>".getInspector($site['inspector'])."</td>
                ";
            if (checkAccess(AccessSiteRatingDel)) {
                $rv .= "<td><a href=http://www.tzpolice.ru/?act=siterating_admin&action=delete&id=".$site['id'].">X</td>";
            }
            if ($special == 'edit') {
                $rv .= "<td><a href=http://www.tzpolice.ru/?act=siterating_admin&action=edit&id=".$site['id'].">edit</td>";
            }
            if ($special == 'free') {
                $rv .= "<td><a href=http://www.tzpolice.ru/?act=siterating_admin&action=attach&id=".$site['id'].">attach</td>";
            }
            $rv .= "</tr>";
    } 
    $rv .= "</table>";
    
    return $rv;
}


function printAll(){
    $sSQL = "SELECT * FROM `siteRating_sites`";
    $sites = mysql_query($sSQL)
        or die("Invalid query: " . mysql_error() . var_dump($sSQL));
    $aSites = array();
    if (mysql_num_rows($sites) > 0) {
        while ($site = mysql_fetch_array($sites)) {
            $aSites[] = $site;
        }
        return printSites($aSites, 'edit');
    } else {
        return false;
    }
}
function checkAccess($checkAccess) {
    if(abs(AccessLevel) & $checkAccess) {
        return true;
    } else {
        return false;
    }
}
function printFree(){
    $sSQL = "SELECT * FROM `siteRating_sites` WHERE `inspector` = 0 OR `status` = 0";
    $sites = mysql_query($sSQL)
        or die("Invalid query: " . mysql_error() . var_dump($sSQL));
    if (mysql_num_rows($sites) > 0) {
        $aSites = array();
        while ($site = mysql_fetch_array($sites)) {
            $aSites[] = $site;
        }
        return printSites($aSites, 'free');
    } else {
        return "<br><br>Свободных сайтов нет.";
    }
}

function printInCheck() {
    $sSQL = "SELECT * FROM `siteRating_sites` WHERE `status` = 0";
    $sites = mysql_query($sSQL)
        or die("Invalid query: " . mysql_error() . var_dump($sSQL));
    if (mysql_num_rows($sites) > 0) {
        $aSites = array();
        while ($site = mysql_fetch_array($sites)) {
            $aSites[] = $site;
        }
        return printSites($aSites, 'edit');
    } else {
        return "<br><br>Сайтов в проверке нет.";
    }
}

function printMy(){
    $sSQL = "SELECT * FROM `siteRating_sites` WHERE `inspector` = ".AuthUserId." AND `status` = 0";
    $sites = mysql_query($sSQL)
        or die("Invalid query: " . mysql_error() . var_dump($sSQL));
    if (mysql_num_rows($sites) > 0) {
        $aSites = array();
        while ($site = mysql_fetch_array($sites)) {
            $aSites[] = $site;
        }
        return printSites($aSites, 'edit');
    } else {
        return "<br><br>У вас нету взятых сайтов.";
    }
}



function addNew() {
    $form = "<form action='http://www.tzpolice.ru/?act=siterating_admin&action=add&mod=save' method=POST name=addNewSite>
                 <table class='sites'>
                    <tr>
                        <td>url:</td>
                        <td><input type='text' name='url' value='http://test.ru'></td>
                    </tr>
                    <tr>
                        <td>name:</td>
                        <td><input type='text' name='name' value='test'></td>
                    </tr>
                    <tr>
                        <td>ответственный:</td>
                        <td><input type='text' name='bailee' value='maxwell'></td>
                    </tr>
                    <tr>
                        <td colspan=2><input type=submit value='Добавить'></td>
                    </tr>
                 </table>
             </form>";
    return $form;
}

function getSiteById($id) {
    $sSQL = "SELECT * FROM `siteRating_sites` WHERE `siteRating_sites`.`id` = ".$id." LIMIT 1";
    $sites = mysql_query($sSQL)
        or die("Invalid query: " . mysql_error() . var_dump($sSQL));
    $site = mysql_fetch_array($sites);
    return $site;
}

function getStatus($n) {
    switch ($n) {
        case 0:
            return "<img src='http://www.tzpolice.ru/_modules/maxwell/siteRating/img/statusUnchecked.gif' alt='Не проверено' title='Не проверено' style='cursor: pointer;'>";
        break;
        case 1:
            return "<img src='http://www.tzpolice.ru/_modules/maxwell/siteRating/img/statusApproved.gif' alt='одобрено' title='одобрено' style='cursor: pointer;'>";
        break;
        case 2:
            return "<img src='http://www.tzpolice.ru/_modules/maxwell/siteRating/img/statusError.gif' alt='не одобрено' title='не одобрено' style='cursor: pointer;'>";
        break;
    }
}

function getViolationsHTML($id) {
    $violations = array('9.2.2', '9.3.1', '9.3.2', '9.3.3', '9.3.4', '9.3.5', '9.3.6', '9.4.1', '9.4.2', '9.4.3', '9.4.4', '9.4.5', 'счетчик ТЗ');
    $site = getSiteById($id);
    $mask = abs($site['violation']);
    $rv = "";
    $k = 1;
    for ($i = 0; $i < count($violations); $i++) {
        if ($k & $mask) {
            $rv .= $violations[$i] . "<input type='checkbox' name='violation_$i' checked><br>";
        } else {
            $rv .= $violations[$i] . "<input type='checkbox' name='violation_$i'><br>";
        }
        $k = $k * 2;
    }
    return $rv;
}

function deleteSite($id, $confirm=0) {
    if (checkAccess(AccessSiteRatingDel)) {
        if ($confirm) {
            $q = "DELETE FROM `siteRating_sites` WHERE `id` = ". $id;
            mysql_query($q)
                or die("Invalid query: " . mysql_error() . var_dump($q));
            return printMy();
        } else {
            $site = getSiteById($id);
            print "<br><br><br><br>Вы уверены что хотите удалить сайт <b>".$site['name']."</b>?<br>";
            print "<a href=http://www.tzpolice.ru/?act=siterating_admin&action=delete&id=$id&confirm=1>Да</a> <a href=http://www.tzpolice.ru/?act=siterating_admin>Нет</a>";
        }
    }
}

function editSite($id) {
    $site = getSiteById($id);
    if (1) { // Check for inspector
        $form = "<form action='http://www.tzpolice.ru/?act=siterating_admin&action=edit&id=$id&mod=save' method=POST name=editNewSite>
                 <table class='sites'>
                    <tr>
                        <td>url:</td>
                        <td><input type='text' name='url' value='".$site['url']."'></td>
                    </tr>
                    <tr>
                        <td>name:</td>
                        <td><input type='text' name='name' value='".$site['name']."'></td>
                    </tr>
                    <tr>
                        <td>глашатай:</td>
                        <td><input type='text' name='herald' value='".$site['herald']."'></td>
                    </tr>
                    <tr>
                        <td>дата окончания:</td>
                        <td><input type='text' name='herald_date' value='".$site['herald_date']."'><button onClick=\"show_calendar_widget(this); return false;\">c</button></td>
                    </tr>
                    <tr>
                        <td>глашатай(vault):</td>
                        <td><input type='text' name='herald_vault' value='".$site['herald_vault']."'></td>
                    </tr>
                    <tr>
                        <td>дата окончания(vault):</td>
                        <td><input type='text' name='herald_vault_date' value='".$site['herald_vault_date']."'><button onClick=\"show_calendar_widget(this); return false;\">c</button></td>
                    </tr>
                    <tr>
                        <td>ответственный:</td>
                        <td><input type='text' name='bailee' value='".$site['bailee']."'></td>
                    </tr>
                    <tr>
                        <td>status:</td>
                        <td>
                            <select name='status'>
                                <option value=0>не проверено</option>
                                <option value=1>одобрено</option>
                                <option value=2>не одобрено</option>
                            </select>
                        </td>
                    </tr>
                    ".getViolationsHTML($id)."
                    <tr>
                        <td>comment:</td>
                        <td>
                            <textarea name='comment'>".$site['comment']."</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=2><input type=submit value='Сохранить'></td>
                    </tr>
                 </table>
             </form>
            <div class=\"calendar_widget\" id=\"calendar_widget\"><iframe id='calendar_widget_iframe' name='calendar_widget_iframe' style=\"border: none;\" width=100% height=100% src=\"http://www.tzpolice.ru/_modules/maxwell/siteRating/datePicker/calendar_widget.html\"></iframe></div> ";
        return $form;
    }
}

function saveEdit($id, $url, $name, $herald, $herald_date, $herald_vault, $herald_vault_date, $bailee, $bailee_date, $status, $violations, $comment) {
    $q = 'UPDATE `siteRating_sites` SET `url` = \''.$url.'\','
        . ' `name` = \''.$name.'\','
        . ' `herald` = \''.$herald.'\','
        . ' `herald_date` =  \''.$herald_date.'\','
        . ' `herald_vault_date` = \''.$herald_vault_date.'\','
        . ' `herald_vault` = \''.$herald_vault.'\','
        . ' `bailee` = \''.$bailee.'\','
        . ' `status` ='.$status.','
        . ' `violation` ='.$violations.','
        . ' `inspector` ='.AuthUserId.','
        . ' `comment` = \''.$comment.'\' WHERE `id` ='.$id.' LIMIT 1';
    $r = mysql_query($q)
        or die("Invalid query: " . mysql_error() . var_dump($q));
    return printMy();
}

function attachSite($id) {
    $q = "UPDATE `siteRating_sites` SET  `inspector` = ".AuthUserId." WHERE `id` = ".$id." LIMIT 1";
    $r = mysql_query($q)
        or die("Invalid query: " . mysql_error() . var_dump($q));
    return printMy();
}

function saveNew($url, $name, $bailee) {
    $sSQL = "INSERT INTO `siteRating_sites` (url, name, bailee) VALUES('".$url."', '".$name."', '".$bailee."')";
    $r = mysql_query($sSQL)
        or die("Invalid query: " . mysql_error() . var_dump($sSQL));
    return printFree();
}

function getViolationsArr($a) {
    $k = array_keys($a);
    $rv = 0;
    for ($i = 0; $i < count($k); $i++) {
        $v = $k[$i];
        if (strpos($v, 'violation') > -1) {
            $rv += pow(2, intval(substr($v, 10, strlen($v) - 9)));
        }
    }
    
    return $rv;
}

?>