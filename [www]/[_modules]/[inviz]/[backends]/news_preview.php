<?php
session_start();
require_once "/home/sites/police/www/_modules/xhr_config.php";
require_once "/home/sites/police/www/_modules/xhr_php.php";
error_reporting(0);
//error_reporting(E_ALL);
function ParseNews($text)
	{
        $text = preg_replace("/\[url=([\w]+?:\/\/.*?)\](.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\2</a>", $text);
		$text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\2</a>", $text);
		$text = preg_replace("/\[url\]([\w]+?:\/\/.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\1</a>", $text);
		$text = preg_replace("/\[url\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\1</a>", $text);
	    $text = preg_replace("/\[color=(\#[0-9A-F]{6}|[a-z]+)\]/si", "<font color='\\1'>", $text);
	    $text = str_replace("[/color]", "</font>", $text);
		$text = preg_replace("/\[pers nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<b>\\1</b> [\\2]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\3.gif' border='0'>", $text);
		$text = preg_replace("/\[pers clan=([0-9A-Za-z_ ]+) nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'><b>\\2</b> [\\3]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\4.gif' border='0'>", $text);
	    $text = preg_replace("/\[clan=([0-9A-Za-z_ ]+)]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'>", $text);
	    $text = preg_replace("/\[pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
	    $text = preg_replace("/\[pro=([0-9w]+) nick=([0-9A-Za-z *\-]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
	    $text = str_replace("[b]", "<b>", $text);
		$text = str_replace("[/b]", "</b>", $text);
        $text = str_replace("[u]", "<u>", $text);
        $text = str_replace("[/u]", "</u>", $text);
        $text = str_replace("[i]", "<i>", $text);
        $text = str_replace("[/i]", "</i>", $text);
        $text = str_replace("[quote]", "<div class='quote'>", $text);
        $text = str_replace("[/quote]", "</div>", $text);
        $text = str_replace("[left]", "<div align='left'>", $text);
        $text = str_replace("[/left]", "</div>", $text);
        $text = str_replace("[center]", "<div align='center'>", $text);
        $text = str_replace("[/center]", "</div>", $text);
	$text = str_replace("[small]", "<font size='-2'>", $text);
        $text = str_replace("[/small]", "</font>", $text);
        $text = str_replace("[right]", "<div align='right'>", $text);
        $text = str_replace("[/right]", "</div>", $text);
        $text = str_replace("[image]", "<img border='0' src='", $text);
        $text = str_replace("[/image]", "'>", $text);
	    $text = str_replace("[list]", "<ul>", $text);
        $text = str_replace("[list=x]", "<ol>", $text);
	    $text = str_replace("[*]", "<li>", $text);
		$text = str_replace("[/list]", "</ul>", $text);
		$text = str_replace("[/list=x]", "</ol>", $text);
        $text = eregi_replace("\\[img\\]([a-z0-9\\./\\-]+)\\[/img\\]","<img border=0 src='../../user_data/\\1'>",$text);
        $text = eregi_replace("\\[imgprev\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><img border=0 src='../../user_data/\\1/thumb/\\2' ALT='Нажмите, чтобы увеличить'></a>",$text);
        $text = str_replace("\n", " <br> ", $text);
        $tmp = explode(" ",$text);
        for($i=0;$i<count($tmp);$i++)
        	{
            	if(ereg(":([A-Za-z0-9_]+):",$tmp[$i],$ok))
                	{
		            	if(is_file("../../_imgs/smiles/{$ok[1]}.gif")) $text=str_replace(":{$ok[1]}:","<img src='_imgs/smiles/{$ok[1]}.gif'> ",$text);
                        //echo ($ok[1]);
         			}
            }
	    return ($text);
	}
$JsHttpRequest =& new Subsys_JsHttpRequest_Php("windows-1251");
$n_title = $_REQUEST['ti'];
$n_text = $_REQUEST['te'];
$n_auth = $_REQUEST['ta'];
$_RESULT = array(
  "result" => "OK");
$n_title = strip_tags($n_title);
$n_text = strip_tags($n_text, "<a>, <div>, <table>, <tr>, <td>");
$n_text = stripslashes($n_text);
$n_text = ParseNews($n_text);
//echo (htmlspecialchars($n_text));
//$n_text = str_replace("'", "/'", $n_text);
?>
<hr>
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><?=$n_text?></td>
            </tr>
          </table>
<hr>