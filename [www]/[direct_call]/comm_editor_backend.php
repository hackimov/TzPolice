<?php
// Стартуем сессию.
session_start();
// Подключаем библиотеку поддержки.
require_once "../_modules/functions.php";
require_once "../_modules/xhr_config.php";
require_once "../_modules/xhr_php.php";
$JsHttpRequest =& new Subsys_JsHttpRequest_Php("windows-1251");
$l = $_REQUEST["l"];
$n = $_REQUEST["n"];
$t = $_REQUEST["t"];
$_RESULT = array(
  "res"		=> "OK",
);
if (!$patt_file = fopen('../new.xml', 'w'))
	{
    	die ("Ошибка записи в файл, попробуйте позже");
        exit;
    }
$lines = file('comm.xml');
$count = 0;
foreach ($lines as $line_num => $line)
	{
    	if ($line_num == $l)
        	{
				$t = str_replace("\r", "\n", $t);
				$t = str_replace("\n\n", "\n", $t);
				$tmp = '<m name="'.$n.'" txt="'.$t.'" />';
            	$new_line = uencode($tmp, "u");
            }
        else
        	{
            	$new_line = $line;
            }
		fwrite($patt_file, $new_line);
    }
fclose($patt_file);
?>
        <hr><div id="div<?=$l?>">
	    <form name="pat<?=$l?>" id="pat<?=$l?>">
	      <input name="hdr" type="text" size="50" value="<?=$n?>">
          <input name="num" type="hidden" value="<?=$i?>">
	      <br>
	      <textarea name="txt" cols="50" rows="5" wrap="VIRTUAL"><?=$t?></textarea>
	      <br>
	      <input type="button" name="OK" value="OK" onClick="saveChanges('<?=$l?>')">
	    </form>
        </div>
?>