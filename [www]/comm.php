<?
Header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
Header("Cache-Control: no-cache, must-revalidate");
Header("Pragma: no-cache");
Header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
require('_modules/functions.php');
$query = 'SELECT * FROM `communicator_templates` ORDER BY `section` DESC';
$res = mysql_query($query);
$cursec = -1;
$out = '';
while ($d = mysql_fetch_assoc($res)) {
	if ($cursec < 0) {
		$out .= ('<category name="'.$comm_tpl_cats[$d['section']].'">
                ');
		$cursec = $d['section'];
	
	} elseif ($cursec > $d['section'] && $d['section'] > 0) {
		$out .= ('</category>
                <category name="'.$comm_tpl_cats[$d['section']].'">
                ');
		$cursec = $d['section'];
	
	} elseif ($cursec > $d['section'] && $d['section'] == 0) {
		$out .= ('</category>
                ');
		$cursec = $d['section'];
	
	}
	$out .= ('<m name="'.$d['title'].'" txt="'.stripslashes($d['content']).'" />
        ');
}
echo iconv('cp1251', 'UTF-8', $out);
$write = iconv('cp1251', 'UTF-8', $out);
$fp = fopen('/home/sites/police/www/comm.xml', 'w');
fwrite($fp, $write);
fclose($fp);

?>