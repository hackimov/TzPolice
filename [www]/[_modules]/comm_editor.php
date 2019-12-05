<?
function writeComm()
	{
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
}		$write = iconv('cp1251', 'UTF-8', $out);
		$fp = fopen('/home/sites/police/www/comm.xml', 'w');
		fwrite($fp, $write);
		fclose($fp);
	}
writeComm();
if (AuthUserGroup==100 || AuthUserName=="Blazna") {
?>
<h1>Редактор шаблонов коммуникатора</h1>
<br><br>
<a href="/comm.php" target="_blank">Сохранить в файл - без этого в игре шаблоны не появятся</a>
<br><br>
<input type="button" name="add" value="Добавить" onClick="new_entry()">
<hr><br>
<div id="extra"></div>
<script language="JavaScript" src="_modules/xhr_js.js"></script>
<script>
function saveChanges(num)
	{
		fid = 'pat' + num;
		did = 'div' + num;
		new_name = '' + document.getElementById(fid).hdr.value;
		new_sect = '' + document.getElementById(fid).sec.options[document.getElementById(fid).sec.selectedIndex].value;
		new_text = '' + document.getElementById(fid).txt.value;
		new_line = '' + document.getElementById(fid).num.value;
		req = new Subsys_JsHttpRequest_Js();
		req.onreadystatechange = function() {
			if (req.readyState == 4) {
				document.getElementById(did).innerHTML = req.responseText;
			}
		}
        document.getElementById(did).innerHTML = "<center><br><br><img src='_imgs/plz_wait.gif' width='300' height='10'><br><br><b>Пожалуйста, подождите...</b></center>";
		req.caching = false;
		req.open('POST', '_modules/comm_editor_backend.php', true);
		req.send({ s: new_sect, n: new_name, t: new_text, l: new_line, a: 'edit' });
	}
function saveNew(num)
	{
        fid = 'pat' + num;
        did = 'div' + num;
		new_name = '' + document.getElementById(fid).hdr.value;
		new_sect = '' + document.getElementById(fid).sec.options[document.getElementById(fid).sec.selectedIndex].value;
		new_text = '' + document.getElementById(fid).txt.value;
		new_line = '' + document.getElementById(fid).num.value;
		req = new Subsys_JsHttpRequest_Js();
		req.onreadystatechange = function() {
			if (req.readyState == 4) {
				document.getElementById(did).innerHTML = req.responseText;
			}
		}
        document.getElementById(did).innerHTML = "<center><br><br><img src='_imgs/plz_wait.gif' width='300' height='10'><br><br><b>Пожалуйста, подождите...</b></center>";
		req.caching = false;
		req.open('POST', '_modules/comm_editor_backend.php', true);
		req.send({ s: new_sect, n: new_name, t: new_text, l: new_line, a: 'savenew' });
	}
function del(num)
	{
        fid = 'pat' + num;
        did = 'div' + num;
		new_line = '' + document.getElementById(fid).num.value;
		req = new Subsys_JsHttpRequest_Js();
		req.onreadystatechange = function() {
			if (req.readyState == 4) {
				document.getElementById(did).innerHTML = req.responseText;
			}
		}
        document.getElementById(did).innerHTML = "<center><br><br><img src='_imgs/plz_wait.gif' width='300' height='10'><br><br><b>Пожалуйста, подождите...</b></center>";
		req.caching = false;
		req.open('POST', '_modules/comm_editor_backend.php', true);
		req.send({ l: new_line, a: 'del' });
	}
function new_entry()
	{
		req = new Subsys_JsHttpRequest_Js();
		req.onreadystatechange = function() {
			if (req.readyState == 4) {
				var tmp = document.getElementById('extra').innerHTML;
				document.getElementById('extra').innerHTML = req.responseText + tmp;
			}
		}
		req.caching = false;
		req.open('POST', '_modules/comm_editor_backend.php', true);
		req.send({ a: 'new' });
	}
</script>
<?php
/*
$lines = file('comm.xml');
$count = 0;
$tmp = "";
foreach ($lines as $line_num => $line)
	{
		$tmp .= uencode($line, "w");
        if (strpos($tmp, "/>"))
        	{
	            $pat[$count]['name'] = sub($tmp, 'm name="', '" txt');
	            $pat[$count]['text'] = sub($tmp, 'txt="', '" />');
	            $pat[$count]['text'] = str_replace("<br>", "\n", $pat[$count]['text']);
	            $count++;
                $tmp = "";
            }
    }
for ($i=0; $i<$count; $i++)
*/
	$query = "SELECT * FROM `communicator_templates` ORDER BY `section`";
	$res = mysql_query($query);
	while ($d = mysql_fetch_array($res)) {
		echo "<div id=\"div".$d['id']."\">\n";
		echo " <form name=\"pat".$d['id']."\" id=\"pat".$d['id']."\">\n";
		echo " <input name=\"hdr\" type=\"text\" size=\"50\" value=\"".$d['title']."\"><br>\n";
		echo " <select name=\"sec\" id=\"sec\">\n";
		echo "  <option value=\"0\"".(($d['section'] == 0)?" selected":"").">Вне категорий</option>\n";
		
		reset($comm_tpl_cats);
		foreach($comm_tpl_cats as $key => $value) {
			echo ("<option value='".$key."'");
			if ($d['section'] == $key) echo (" selected");
			echo (">".$value."</option>");
		}
		echo " </select>\n";
		echo " <input name=\"num\" type=\"hidden\" value=\"".$d['id']."\">\n";
		echo " <br>\n";
		echo " <textarea name=\"txt\" cols=\"50\" rows=\"5\" wrap=\"VIRTUAL\">".stripslashes($d['content'])."</textarea>\n";
		echo " <br>\n";
		echo " <input type=\"button\" name=\"OK\" value=\"Сохранить\" onClick=\"saveChanges('".$d['id']."')\">\n";
		echo " <input type=\"button\" name=\"OK\" value=\"Удалить\" onClick=\"del('".$d['id']."')\">\n";
		echo " </form>\n";
		echo "</div><hr>\n";
	}
}
?>