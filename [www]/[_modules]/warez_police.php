<h1>Программное обеспечение PE</h1>
<?php
//error_reporting(E_ALL);
$bgstr[0]="background='i/bgr-grid-sand.gif'";
$bgstr[1]="background='i/bgr-grid-sand1.gif'";
if(abs(AccessLevel) & AccessArticles)	{
?>
<form action="?act=warez_pe" method="post" name="upload" id="upload" enctype="multipart/form-data">
  <input type="hidden" name="sec" value="upload">
  файл (*.zip, *.rar, *.exe, *.tzp)<br>
  <input type="file" name="userfile">
  <br>
  ссылка *<br>
  <input name="link" type="text" id="link">
  <br>
  автор<br>
  <input name="author" type="text" id="author">
  <br>
  название<br>
  <input name="name" type="text" id="name">
  <br>
  версия<br>
  <input name="ver" type="text" id="ver">
  <br>
описание<br>
<textarea name="desc" wrap="VIRTUAL" id="desc" cols="45" rows="5"></textarea>
<br>
<input type="submit" name="Submit" value="Закачать | Добавить">
</form>
* - Значение в данном поле учитывается только если в поле "файл" ничего не указано
<?
//Add file
if ($_REQUEST["sec"] == "upload")
{
	$count = microtime();
	if (is_uploaded_file ($_FILES['userfile']['tmp_name']))
    	{
        	$old_name = $_FILES['userfile']['name'];
		    $len = strlen($old_name);
		    $ext = substr($old_name, $len-4, 4);
			if ($ext==".zip" || $ext==".rar" || $ext==".exe" || $ext==".tzp" || $ext==".swf")
				{
				$count = str_replace(" ", "", $count);
				$count = str_replace(".", "", $count);
		            $name = strip_tags($_REQUEST['name']);
        		    $name = htmlspecialchars($name);
			    $desc = $_REQUEST['desc'];
//		            $desc = strip_tags($_REQUEST['desc']);
//		            $desc = htmlspecialchars($desc);
		            $ver = strip_tags($_REQUEST['ver']);
		            $ver = htmlspecialchars($ver);
		            $author = strip_tags($_REQUEST['author']);
		            $author = htmlspecialchars($author);
			    	$new_file_name = $old_name;
				    $query = "INSERT INTO `warez_police` (`id`, `author`, `name`, `version`, `file`, `desc`) VALUES ( '', '".$author."', '".$name."', '".$ver."', '".$new_file_name."', '".$desc."');";
		 		   	if (move_uploaded_file($_FILES['userfile']['tmp_name'], "restricted_files/".$new_file_name))
		            	{
							$result = mysql_query($query) or die("Выполнить запрос не удалось: " . mysql_error());
        		            chmod ("restricted_files/".$new_file_name, 0777);
		                }
        		}
			else {echo("<br><center>Разрешена закачка только файлов формата <b>ZIP</b>, <b>RAR</b> или <b>EXE</b>!.</div></center>");}
        }
    elseif (strlen($_REQUEST['link']) > 3)
    	{
            $link = strip_tags($_REQUEST['link']);
            $link = htmlspecialchars($link);
            $name = strip_tags($_REQUEST['name']);
			$name = htmlspecialchars($name);
			$desc = strip_tags($_REQUEST['desc']);
			$desc = htmlspecialchars($desc);
		    $ver = strip_tags($_REQUEST['ver']);
		    $ver = htmlspecialchars($ver);
		    $author = strip_tags($_REQUEST['author']);
			$author = htmlspecialchars($author);
    		$query = "INSERT INTO `warez_police` (`id`, `author`, `name`, `version`, `file`, `desc`) VALUES ( '', '".$author."', '".$name."', '".$ver."', '".$link."', '".$desc."');";
			$result = mysql_query($query) or die("Выполнить запрос не удалось: " . mysql_error());
        }
}

//Delete file
if ($_GET["sec"] == "del")
{
    $file_id = $_GET["id"];
	$query = "SELECT * FROM `warez_police` WHERE `id` = '".$file_id."' LIMIT 1 ;";
    $result = mysql_query($query) or die("Выполнить запрос не удалось: " . mysql_error());
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $file_name = $row["file"];
	if (is_file("restricted_files/".$file_name))
    	{
			if (unlink("restricted_files/".$file_name))
				{
		        	$query = "DELETE FROM `warez_police` WHERE `id` = '".$file_id."' LIMIT 1;";
				    $result = mysql_query($query) or die("Выполнить запрос не удалось: " . mysql_error());
		        }
        }
    else
    	{
        	$query = "DELETE FROM `warez_police` WHERE `id` = '".$file_id."' LIMIT 1;";
		    $result = mysql_query($query) or die("Выполнить запрос не удалось: " . mysql_error());
        }
}


//Edit file

if ($_GET["sec"] == "edit")
{
	$query = "SELECT * FROM `warez_police` WHERE `id` = '".$_GET['id']."' LIMIT 1 ;";
    $r = mysql_query($query);
    $row = mysql_fetch_array($r, MYSQL_ASSOC);
?>
 <hr><br><br>Редактирование файла <b><?=$row['name']?></b>
<form action="?act=warez_pe" method="post" name="upload" id="upload" enctype="multipart/form-data">
  <input type="hidden" name="sec" value="do_edit">
  <input type="hidden" name="id" value="<?=$row['id']?>">
  ссылка *<br>
  <input name="link" type="text" id="link" value="<?=$row['file']?>">
  <br>
  автор<br>
  <input name="author" type="text" id="author" value="<?=$row['author']?>">
  <br>
  название<br>
  <input name="name" type="text" id="name" value="<?=$row['name']?>">
  <br>
  версия<br>
  <input name="ver" type="text" id="ver" value="<?=$row['version']?>">
  <br>
  описание<br>
<textarea name="desc" wrap="VIRTUAL" id="desc" cols="45" rows="5"><?=$row['desc']?></textarea>
<br>
<input type="submit" name="Submit" value="Изменить">
</form>


<?
}

if ($_POST["sec"] == "do_edit")
{
	$query = "UPDATE `warez_police` SET `file` = '".$_REQUEST['link']."', `author` = '".$_REQUEST['author']."', `name` = '".$_REQUEST['name']."', `version` = '".$_REQUEST['ver']."', `desc` = '".$_REQUEST['desc']."' WHERE `id` = '".$_POST['id']."' LIMIT 1 ;";
    $r = mysql_query($query);
?>
<script>top.location='?act=warez_pe';</script>
<?

}


		    }
if(abs(AccessLevel) & AccessPolice)	{
?>
<table width="90%"  border="0" align="center" cellpadding="3" cellspacing="5">
  <tr bgcolor=#F4ECD4>
    <td width="20%" align="center"><b>Название</b> </td>
    <td width="15%" align="center"><b>Версия</b></td>
    <td width="15%" align="center"><b>Автор</b></td>
    <td width="50%" align="center"><b>Описание</b></td>
  </tr>
<?
		$query = "SELECT * FROM `warez_police` ORDER BY `id` DESC;";
        $rs = mysql_query($query);
        $count = 0;
        while (list($c_id, $c_author, $c_name, $c_ver, $c_file, $c_desc) = mysql_fetch_row($rs))
        	{
            	?>
                  <tr>
				    <td <?=$bgstr[$count]?> align="center">
                    <?
//	                   	if (is_file("warez/".$file_name))
//                        	{
					$tmpsize = filesize("restricted_files/".$c_file);
                    $size = round($tmpsize/1024);
                            	?>
				                    <a href="http://www.tzpolice.ru/download.php?file=<?=$c_file?>"><?=$c_name?></a><br><b><?=$size?></b> кБ
								<?
/*                            }
                        else
                        	{
                            	?>
				                    <a href="<?=$c_file?>"><?=$c_name?></a>
                                <?
                            }
*/
                    if(abs(AccessLevel) & AccessArticles)
                    	{
                        ?>
                    <br><a href="?act=warez_pe&sec=del&id=<?=$c_id?>" onClick="if(!confirm('Вы уверены?')) {return false}">[X]</a> || <a href="?act=warez_pe&sec=edit&id=<?=$c_id?>">[E]</a>
                    	<?
                        }
                        ?>
                    </td>
				    <td <?=$bgstr[$count]?> align="center"><?=$c_ver?></td>
				    <td <?=$bgstr[$count]?> align="center"><?=$c_author?></td>
                    <?
                    $c_desc = str_replace("\n", "<br>", $c_desc);
                    $c_desc = str_replace("\r", "<br>", $c_desc);
                    $c_desc = str_replace("<br><br>", "<br>", $c_desc);
                    ?>
				    <td <?=$bgstr[$count]?>><?=$c_desc?></td>
				  </tr>

                <?
                $count++;
                if ($count> 1) {$count=0;}
            }
        ?></table><?
}
else
{
	echo ("Cops only");
}
?>