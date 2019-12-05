<?php
    require('/home/sites/police/www/_modules/functions.php'); // functions + DB connect
    require('/home/sites/police/www/_modules/auth.php'); // authorization
    function openFile($filename,$how){
        $fd = fopen($filename,$how);
        if(!$fd) exit("Невозможно открыть файл");
        else  {
          // Чтение содержимого файла в переменную text
          while (!feof ($fd)) {
            $buffer .= fread($fd, 4096);
            $buffer = trim(chop($buffer));

          }
        }
        fclose ($fd);
        return $buffer;
    }
    
    function userTestFunc() {
//        $sSQL = "SELECT Clans.id, Clans.name, Users.name AS name, Users.* FROM `tzpolice_tz_users` AS Users, `tzpolice_tz_clans` AS Clans WHERE Clans.name='Military Police' AND Clans.id = Users.clan_id ORDER BY Users.clan_id ASC, Users.name ASC";
        $sSQL = 'SELECT Users.* FROM `tzpolice_tz_users` AS Users WHERE Users.id = 24725';
        $result = mysql_query($sSQL);
        $nrows=mysql_num_rows($result);
        if($nrows>0){
            while($row = mysql_fetch_array($result)){
            //    $clan = "Military Police";
                var_dump($row);
                $returntxt .= '[pers clan=Military Police nick='.stripslashes($row['name']).' level='.$row['level'].' pro='.$row['pro'].''.(($row['sex']=='0')?'w':'').']<br>';
            }
        } else {
            $returntxt .= '<CENTER> </CENTER>';
        }
        return $returntxt;
    }
    
    function tableList(){
        $sSQL = "SHOW TABLES";
        $result = mysql_query($sSQL);
        $nrows=mysql_num_rows($result);
        if($nrows>0){
            while($row = mysql_fetch_array($result)){
                var_dump($row);
            }
        } 
    }
    
    function test() {
        $sSQL = "SELECT * FROM `siteRating_sites`";
        $result = mysql_query($sSQL);
        $nrows=mysql_num_rows($result);
        if($nrows>0){
            while($row = mysql_fetch_array($result)){
                var_dump($row);
            }
        } 
    }
    
    function createTbl() {
        $sSQL = "CREATE TABLE `siteRating_sites` (
                  `id` mediumint(8) unsigned NOT NULL auto_increment,
                  `url` varchar(255) NOT NULL default '',
                  `name` varchar(255) NOT NULL default '',
                  `herald` varchar(255) NOT NULL default '',
                  `herald_vault` varchar(255) NOT NULL default '',
                  `status` smallint(5) unsigned NOT NULL default '0',
                  `lastCheck` timestamp(14) NOT NULL,
                  `inspector` mediumint(8) unsigned NOT NULL default '0',
                  `bailee` varchar(255) NOT NULL default '',
                  `violation` mediumint(9) NOT NULL default '0',
                  `comment` text NOT NULL,
                  PRIMARY KEY  (`id`)
                ) TYPE=MyISAM AUTO_INCREMENT=7 ;";
        $result = mysql_query($sSQL);
        $nrows=mysql_num_rows($result);
        if($nrows>0){
            while($row = mysql_fetch_array($result)){
                var_dump($row);
            }
        } 
    }
    //qbxwa976182
    function updtable() {
        $sSQL = "ALTER TABLE  `siteRating_sites` ADD  `harald_date` VARCHAR( 30 ) NOT NULL AFTER  `herald_vault` , ADD  `harald_vault_date` VARCHAR( 30 ) NOT NULL AFTER  `harald_date` ;";
        $result = mysql_query($sSQL) 
                    or die("Invalid query: " . mysql_error() . var_dump($sSQL));
        print 1;
    }
    
    function dropTbl() {
        $sSQL = "DROP TABLE `siteRating_sites`";
        $result = mysql_query($sSQL) 
                    or die("Invalid query: " . mysql_error() . var_dump($sSQL));
        print 1;
    }
    
    print "<pre>";
    //print openFile('/home/sites/police/www/get_mp_patch.php', 'r');
    print updtable();
    print "</pre>";
    
    
?>