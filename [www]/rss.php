<?php
#		functions
function ParseNews($buf,$AllowTags,$replaceBR=1) {
         if($AllowTags==0) $buf=strip_tags($buf,"<b><i><u>");
         $buf=stripslashes($buf);
         if($replaceBR==1) $buf=nl2br($buf);
         $FontColors1=array("[red]", "[/red]", "[green]", "[/green]", "[blue]", "[/blue]");
         $FontColors2=array("<font color=red>", "</font>", "<font color=green>", "</font>", "<font color=blue>", "</font>");
         $buf=str_replace($FontColors1,$FontColors2,$buf);
         $buf=eregi_replace("\\[prof\\]([a-z]+[0-9]+)\\[/prof\\]","",$buf);
         $buf=eregi_replace("\\[clan\\]([a-zA-Z0-9_\\-]+)\\[/clan\\]","",$buf);
         $buf=eregi_replace("\\[img\\]([a-z0-9\\./\\-]+)\\[/img\\]"," <a href=\"http://www.tzpolice.ru/user_data/\\1\">Image</a> ",$buf);
         $buf=eregi_replace("\\[imgprev\\]([a-z0-9/\\-]+);([jpggifpng0-9\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","",$buf);
         $buf=eregi_replace("\\[URL\\]([a-z0-9_\\-\\.:/\\&\\?\\=\\-]+);([a-z0-9а-яА-Я _\\-\\.:/\\&\\?\\= \\-]+)\\[/URL\\]"," <a href=\"\\1\" target=\"_blank\">\\2</a> ",$buf);
         $buf=str_replace("[clan]","<img src='http://www.tzpolice.ru/_imgs/clans/",$buf);
         $buf=str_replace("[/clan]",".gif'>",$buf);
        $tmp = explode(" ",$buf);
        for($i=0;$i<count($tmp);$i++)
        	{
            	if(eregi(":([a-z0-9_]+):",$tmp[$i],$ok))
                	{
		            	if(is_file("_imgs/smiles/{$ok[1]}.gif")) $buf=str_replace(":{$ok[1]}:","<img src='http://www.tzpolice.ru/_imgs/smiles/{$ok[1]}.gif'> ",$buf);
         			}
            }
         return $buf;
}
function ParseNews2($text)
	{
		$text = str_replace("<div", "<br><div
        ", $text);
        $text = stripslashes(strip_tags($text, "<br>"));
		$text = str_replace("&copy;", "", $text);
        $text = str_replace("&", "&amp;", $text);
        $text = preg_replace("/\[url=([\w]+?:\/\/.*?)\](.*?)\[\/url\]/si", '<a href="\\1" target="_blank">\\2</a>', $text);
		$text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/si", '<a href="http://\\1" target="_blank">\\2</a>', $text);
		$text = preg_replace("/\[url\]([\w]+?:\/\/.*?)\[\/url\]/si", '<a href="\\1" target="_blank">\\1</a>', $text);
		$text = preg_replace("/\[url\](.*?)\[\/url\]/si", '<a href="http://\\1" target="_blank">\\1</a>', $text);
	    $text = preg_replace("/\[color=(\#[0-9A-F]{6}|[a-z]+)\]/si", "<font color='\\1'>", $text);
	    $text = str_replace("[/color]", "</font>", $text);
		$text = preg_replace("/\[pers nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<b>\\1</b> [\\2]<img style='vertical-align:text-bottom' src='http://www.tzpolice.ru/_imgs/pro/i\\3.gif' border='0'>", $text);
		$text = preg_replace("/\[pers clan=([0-9A-Za-z_ ]+) nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<img src='http://www.tzpolice.ru/_imgs/clans/\\1.gif' alt='\\1' border='0'><b>\\2</b> [\\3]<img style='vertical-align:text-bottom' src='http://www.tzpolice.ru/_imgs/pro/i\\4.gif' border='0'>", $text);
	    $text = preg_replace("/\[clan=(.*?)\]/si", "<img src='http://www.tzpolice.ru/_imgs/clans/\\1.gif' alt='\\1' border='0'>", $text);
	    $text = preg_replace("/\[pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='http://www.tzpolice.ru/_imgs/pro/i\\1.gif' border='0'>", $text);
	    $text = preg_replace("/\[pro=([0-9w]+) nick=([0-9A-Za-z *\-]+)\]/si", "<img style='vertical-align:text-bottom' src='http://www.tzpolice.ru/_imgs/pro/i\\1.gif' border='0'>", $text);
	    $text = str_replace("[b]", "<b>", $text);
		$text = str_replace("[/b]", "</b>", $text);
        $text = str_replace("[u]", "<u>", $text);
        $text = str_replace("[/u]", "</u>", $text);
        $text = str_replace("[i]", "<i>", $text);
        $text = str_replace("[/i]", "</i>", $text);
        $text = str_replace("[quote]", "<b>Цитата:</b><br>", $text);
        $text = str_replace("[/quote]", "<br><i>Конец цитаты</i>", $text);
        $text = str_replace("[left]", "", $text);
        $text = str_replace("[/left]", "", $text);
        $text = str_replace("[center]", "", $text);
        $text = str_replace("[/center]", "", $text);
	$text = str_replace("[small]", "", $text);
        $text = str_replace("[/small]", "", $text);
        $text = str_replace("[right]", "", $text);
        $text = str_replace("[/right]", "", $text);
        $text = str_replace("[image]", "<img border='0' src='", $text);
	$text = str_replace("[imageleft]", "<img border='0' align='left' src='", $text);
        $text = str_replace("[/image]", "'>", $text);
	    $text = str_replace("[list]", "", $text);
        $text = str_replace("[list=x]", "", $text);
	    $text = str_replace("[*]", "", $text);
		$text = str_replace("[/list]", "", $text);
		$text = str_replace("[/list=x]", "", $text);
        $text = eregi_replace("\\[img\\]([a-z0-9\\./\\-]+)\\[/img\\]","",$text);
        $text = eregi_replace("\\[imgprev\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","",$text);
        $tmp = explode(" ",$text);
        for($i=0;$i<count($tmp);$i++)
        	{
            	if(eregi(":([a-z0-9_]+):",$tmp[$i],$ok))
                	{
		            	if(is_file("_imgs/smiles/{$ok[1]}.gif")) $text=str_replace(":{$ok[1]}:","<img src='http://www.tzpolice.ru/_imgs/smiles/{$ok[1]}.gif'> ",$text);
         			}
            }
       	$text = nl2br($text);
      return $text;
	}

#		connect
include "_modules/mysql.php";
#		main
header('Content-type: text/xml; charset=windows-1251');
//setlocale(LC_ALL, "ru_RU.CP1251");
$hdr= '<?xml version="1.0" encoding="windows-1251" ?>
<rss version="2.0">
  <channel>
    <title>TZPolice.ru</title>
    <link>http://www.tzpolice.ru/</link>
    <description>Официальный сайт департамента Полиции Точки Отсчета.</description>
    <language>ru</language>
    <copyright>Copyright 2009 TZ Police Department</copyright>
  	<lastBuildDate>'.date("r", time()).'</lastBuildDate>
  	<generator>PHP Script</generator>
  	<managingEditor>deadbeef@tzpolice.ru</managingEditor>
  	<webMaster>deadbeef@tzpolice.ru</webMaster>
    ';
//    echo iconv("cp1251", "UTF-8", $hdr);
    echo ($hdr);
$SQL="SELECT n.*, u.id AS PosterId, u.user_name AS PosterName, u.clan AS PosterClan FROM news n LEFT JOIN site_users u ON n.poster_id=u.id WHERE n.is_visible=1 AND n.our_news='0' ORDER BY n.news_date DESC LIMIT 0, 10";
$r=mysql_query($SQL);
while($d=mysql_fetch_array($r)) {
echo ("<item>
      <title>".htmlspecialchars(stripslashes($d['news_title']))."</title>
      <description>");
    $DataString=$d['news_text'];
    if ($d['markup'] == 0)
    	{
//			echo iconv("cp1251", "UTF-8", ParseNews($DataString,0,0));
			echo (htmlspecialchars(ParseNews($DataString,0,0)));
        }
    elseif ($d['markup'] == 1)
    	{
//			echo iconv("cp1251", "UTF-8", ParseNews2($DataString));
			echo (htmlspecialchars(ParseNews2($DataString)));
        }
$tmp = "</description>
      <link>http://www.tzpolice.ru/?act=n_comm&amp;IdNews=".$d['id']."</link>
      <comments>http://www.tzpolice.ru/?act=n_comm&amp;IdNews=".$d['id']."</comments>
      <author>".$d['PosterName']."&lt;deadbeef@tzpolice.ru&gt;</author>
      <pubDate>".date("r",  $d['news_date'])."</pubDate>
      <guid isPermaLink='true'>http://www.tzpolice.ru/?act=n_comm&amp;IdNews=".$d['id']."</guid>
    </item>
    ";
//echo iconv("cp1251", "UTF-8", $tmp);
echo ($tmp);
}
?>
 </channel>
</rss>