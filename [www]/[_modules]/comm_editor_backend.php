<?php
//error_reporting(E_ALL ^ E_NOTICE);
session_start();
require_once "functions.php";
require_once "xhr_config.php";
require_once "xhr_php.php";
error_reporting(0);
$JsHttpRequest =& new Subsys_JsHttpRequest_Php("windows-1251");
$l = $_REQUEST["l"];
$n = $_REQUEST["n"];
$t = $_REQUEST["t"];
$a = $_REQUEST["a"];
$s = $_REQUEST["s"];
$_RESULT = array(
  "res"		=> "OK",
);
if ($a == "edit")
	{
	    $t = str_replace("\"", "'", $t);
	    $t = str_replace("\r", "\n", $t);
	    $t = str_replace("\n\n", "\n", $t);
        $t = addslashes($t);
        $query = "UPDATE `communicator_templates` SET `title` = '".$n."', `content`='".$t."', `section`='".$s."' WHERE `id`='".$l."' LIMIT 1;";
        mysql_query($query) or die(mysql_error());
//        echo ($query);
/*	    $tmp = '<m name="'.$n.'" txt="'.$t.'" />';
        if ($l > $count)
        	{
            	$count = $l;
            }
        $old[$l] = iconv("cp1251", "UTF-8", $tmp);
  		$old[$l] .= "\n";
	    for ($i=0; $i<=$count; $i++)
	        {
	            fwrite($patt_file, $old[$i]);
	        }

	    fclose($patt_file);
	    unlink('../comm.xml');
	    rename('../comm2.xml', '../comm.xml');
*/
	    ?>
	    <form name="pat<?=$l?>" id="pat<?=$l?>">
	      <input name="hdr" type="text" size="50" value="<?=$n?>"><br>
		<select name="sec" id="sec">
		<option value="0"<?if ($d['section'] == 0) echo (" selected");?>>Вне категорий</option>
		<?
		reset($comm_tpl_cats);
		foreach($comm_tpl_cats as $key => $value)
			{
				echo ("<option value='".$key."'");
                if ($s == $key) echo (" selected");
                echo (">".$value."</option>");
			}
		?>
        </select>
          <input name="num" type="hidden" value="<?=$l?>">
	      <br>
	      <textarea name="txt" cols="50" rows="5" wrap="VIRTUAL"><?=stripslashes($t)?></textarea>
	      <br>
	      <input type="button" name="OK" value="Сохранить" onClick="saveChanges('<?=$l?>')">
          <input type="button" name="OK" value="Удалить" onClick="del('<?=$l?>')">
	    </form>
<?
	}
elseif ($a == "savenew")
	{
	    $t = str_replace("\"", "'", $t);
	    $t = str_replace("\r", "\n", $t);
	    $t = str_replace("\n\n", "\n", $t);
        $t = addslashes($t);
        $query = "INSERT INTO `communicator_templates` SET `title` = '".$n."', `content`='".$t."', `section`='".$s."'";
        mysql_query($query) or die(mysql_error());
//        echo ($query);
        $l = mysql_insert_id();
/*	    $tmp = '<m name="'.$n.'" txt="'.$t.'" />';
        if ($l > $count)
        	{
            	$count = $l;
            }
        $old[$l] = iconv("cp1251", "UTF-8", $tmp);
  		$old[$l] .= "\n";
	    for ($i=0; $i<=$count; $i++)
	        {
	            fwrite($patt_file, $old[$i]);
	        }

	    fclose($patt_file);
	    unlink('../comm.xml');
	    rename('../comm2.xml', '../comm.xml');
*/
	    ?>
	    <form name="pat<?=$l?>" id="pat<?=$l?>">
	      <input name="hdr" type="text" size="50" value="<?=$n?>"><br>
		<select name="sec" id="sec">
		<option value="0"<?if ($s == 0) echo (" selected");?>>Вне категорий</option>
		<?
		reset($comm_tpl_cats);
		foreach($comm_tpl_cats as $key => $value)
			{
				echo ("<option value='".$key."'");
                if ($s == $key) echo (" selected");
                echo (">".$value."</option>");
			}
		?>
        </select>
          <input name="num" type="hidden" value="<?=$l?>">
	      <br>
	      <textarea name="txt" cols="50" rows="5" wrap="VIRTUAL"><?=stripslashes($t)?></textarea>
	      <br>
	      <input type="button" name="OK" value="Сохранить" onClick="saveChanges('<?=$l?>')">
          <input type="button" name="OK" value="Удалить" onClick="del('<?=$l?>')">
	    </form>
<?
	}
elseif ($a == "new")
	{
        $new_i = time();
        ?>
				<div id="div<?=$new_i?>">
	            <form name="pat<?=$new_i?>" id="pat<?=$new_i?>">
	              <input name="hdr" type="text" size="50" value="Новый заголовок"><br>
                    		<select name="sec" id="sec">
	                        <option value="0" selected>Вне категорий</option>
	                        <?
	                        reset($comm_tpl_cats);
	                        foreach($comm_tpl_cats as $key => $value)
	                            {
	                                echo ("<option value='".$key."'>".$value."</option>");
	                            }
	                        ?>
	                        </select>
	              <input name="num" type="hidden" value="<?=$new_i?>">
	              <br>
	              <textarea name="txt" cols="50" rows="5" wrap="VIRTUAL">Новый текст</textarea>
	              <br>
	              <input type="button" name="OK" value="OK" onClick="saveNew(<?=$new_i?>)">
	            </form>
                </div><hr>
        <?
    }
elseif ($a == "del")
	{
/*
        $lines = file('../comm.xml');
	    $count = 0;
	    $tmp = "";
	    foreach ($lines as $line_num => $line)
	        {
	            $tmp .= $line;
	            if (strpos($tmp, "/>"))
	                {
	                    $old[$count] = $tmp;
	                    $count++;
	                    $tmp = "";
	                }
	        }
	    if (!$patt_file = fopen('../comm2.xml', 'w'))
	        {
	            die ("Ошибка записи в файл, попробуйте позже");
	            exit;
	        }
	    for ($i=0; $i<=$count; $i++)
	        {
            	if (!($i == $l))
                	{
			            fwrite($patt_file, $old[$i]);
                    }
	        }
	    fclose($patt_file);
	    unlink('../comm.xml');
	    rename('../comm2.xml', '../comm.xml');
*/
		$query = "DELETE FROM `communicator_templates` WHERE `id`='".$l."' LIMIT 1;";
        mysql_query($query);
	    ?>
<font color="red"><b>Удалено</b></font>
<?
	}
?>