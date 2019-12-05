<?
/****************************************************
*			 Инсталяция таблиц БД					*
*													*
*	 Lebedev Sergey (fantastish@rambler.ru)			*
****************************************************/
	
	$ftext="";
	$i=1;
	/*
	$query = "CREATE TABLE IF NOT EXISTS `".$db["rating_check"]."` (`id` INT PRIMARY KEY AUTO_INCREMENT, `name_id` INT NOT NULL, `uid` INT NOT NULL, `time` INT NOT NULL, `status` TINYINT NOT NULL, `text` VARCHAR(255) NOT NULL);";
	if(mysql_query ($query,$connection)) $ftext .= "<BR>Таблица ".$db["rating_check"]." успешно создана<BR>\n";
	else $ftext .= "<BR>Невозможно произвести MySQL запрос ".$i."<BR>\n";	
	
	$i++;
	
	$query = "CREATE TABLE IF NOT EXISTS `".$db["rating_screen"]."` (`id` INT PRIMARY KEY AUTO_INCREMENT, `cid` INT NOT NULL, `place` INT NOT NULL, `clan_id` INT NOT NULL, `name_id` INT NOT NULL, `level` TINYINT NOT NULL, `pro` TINYINT NOT NULL, `expa` INT NOT NULL, `win` INT NOT NULL, `lost` INT NOT NULL, `star` TINYINT DEFAULT '0' NOT NULL, `time` INT NOT NULL);";
	if(mysql_query ($query,$connection)) $ftext .= "<BR>Таблица ".$db["rating_screen"]." успешно создана<BR>\n";
	else $ftext .= "<BR>Невозможно произвести MySQL запрос ".$i."<BR>\n";	
	
	$i++;
	
	$query = "CREATE TABLE IF NOT EXISTS `".$db["tz_users"]."` (`id` INT PRIMARY KEY AUTO_INCREMENT, `name` VARCHAR(50) NOT NULL UNIQUE, `pro` TINYINT(3) NOT NULL, `clan_id` INT default '0' NOT NULL, `level` TINYINT default '1' NOT NULL, `sex` TINYINT default '1' NOT NULL, `upd_time` INT default '0' NOT NULL);";
	if(mysql_query ($query,$connection)) $ftext .= "<BR>Таблица ".$db["tz_users"]." успешно создана<BR>\n";
	else $ftext .= "<BR>Невозможно произвести MySQL запрос ".$i."<BR>\n";	
	
	
	$i++;
	
	$query = "CREATE TABLE IF NOT EXISTS `".$db["tz_clans"]."` (`id` INT PRIMARY KEY AUTO_INCREMENT, `name` VARCHAR(50) NOT NULL);";
	if(mysql_query ($query,$connection)) $ftext .= "<BR>Таблица ".$db["tz_clans"]." успешно создана<BR>\n";
	else $ftext .= "<BR>Невозможно произвести MySQL запрос ".$i."<BR>\n";	
	
	$i++;
	
	$query = "CREATE TABLE IF NOT EXISTS `".$db["okp4oin"]."` (`id` INT PRIMARY KEY AUTO_INCREMENT, `name_id` INT NOT NULL, `uid` INT NOT NULL, `time` INT NOT NULL, `status` TINYINT NOT NULL, `text` VARCHAR(255) NOT NULL);";
	if(mysql_query ($query,$connection)) $ftext .= "<BR>Таблица ".$db["okp4oin"]." успешно создана<BR>\n";
	else $ftext .= "<BR>Невозможно произвести MySQL запрос ".$i."<BR>\n";
	
	$i++;*/
	
	$query = "CREATE TABLE IF NOT EXISTS `".$db["fees"]."` (`id` INT PRIMARY KEY AUTO_INCREMENT, `name_id` INT NOT NULL, `text` VARCHAR(255) NOT NULL, `summa` INT NOT NULL, `payed` INT NOT NULL, `uid` INT NOT NULL, `prison` TINYINT NOT NULL, `time` INT NOT NULL);";
	if(mysql_query ($query,$connection)) $ftext .= "<BR>Таблица ".$db["fees"]." успешно создана<BR>\n";
	else $ftext .= "<BR>Невозможно произвести MySQL запрос ".$i."<BR>\n";	
	
	
	echo $ftext;
	
?>