<?php
error_reporting(E_ALL);
echo ("<pre>");
print_r($_POST);
echo ("</pre>");
if (@isset($_REQUEST['type']))
	{
    	$abtype = $_REQUEST['type'];
    }
else
	{
    	$abtype = "default";
    }
$cat['thanks'] = 0;
$cat['moder'] = 1;
$cat['private'] = 2;
$cat['license'] = 3;
$cat['drill'] = 4;
$cat['investigation'] = 5;
$cat['econ'] = 6;
$cat['common'] = 7;
$cops_list = '<select size="1" name="cop"><option selected value="0">�����/������� � �����</option>';
$query = "SELECT `id`, `user_name` FROM `site_users` WHERE `clan` = 'police'";
$res = mysql_query($query);
while ($d = mysql_fetch_array($res))
	{
    	$cops_list .= '<option value="'.$d["id"].'">'.$d['user_name'].'</option>';
    }
$cops_list .= '</select>';
//forms
$abuse_form['default'] = '
��� ���������: <br><br>
� <a href="?act=abuse&type=thanks">�������������</a><br>
� <a href="?act=abuse&type=moder">������ �� ���������</a><br>
� <a href="?act=abuse&type=private">������ �� �������� �������</a><br>
� <a href="?act=abuse&type=license">������ �� �������� �� �������</a><br>
� <a href="?act=abuse&type=drill">������ �� ����� ��������</a><br>
� <a href="?act=abuse&type=investigation">������ �� ����� �������������</a><br>
� <a href="?act=abuse&type=econ">������ �� ����</a><br>
� <a href="?act=abuse&type=common">������ ������ ���������</a><br>
';
$abuse_form['drill'] = '<form name="abuse_form" id="abuse_form" method="post" action="?act=abuse" enctype="multipart/form-data">
  <input name="kind" type="hidden" value="drill" />
  <input name="step" type="hidden" value="do" />
  <table width="450" align="center"  border="0" cellspacing="5" cellpadding="5">
    <tr align="center">
      <td colspan="2"> ������ �� ������ ���������� ������ �������� �������� <img src="_imgs/clans/police.gif" /> ������� </td>
    </tr>
    <tr>
      <td>��� ������������: </td>
      <td><input name="cop" type="text" size="32" /></td>
    </tr>
    <tr>
      <td> ������� �������� ������������ � ����� ������, ������� �� ��� ������ �������� </td>
      <td><textarea name="desc" cols="32" rows="6" wrap="VIRTUAL"></textarea></td>
    </tr>
    <tr>
      <td colspan=2 align="center"><input type="submit" name="Submit" value="���������" /></td>
    </tr>
  </table>
  <br />
  <input type="hidden" name="unick" id="unick">
  <input type="hidden" name="ulevel" id="ulevel">
  <input type="hidden" name="upro" id="upro">
  <input type="hidden" name="uclan" id="uclan">
  <input type="hidden" name="ucity" id="ucity">
  <input type="hidden" name="usid" id="usid">
</form>';
$abuse_form['moder'] = '<form name="abuse_form" id="abuse_form" method="post" action="?act=abuse" enctype="multipart/form-data">
  <input name="kind" type="hidden" value="om" />
  <input name="step" type="hidden" value="do" />
  <table width="450" align="center"  border="0" cellspacing="5" cellpadding="5">
    <tr align="center">
      <td colspan="2"> ������ �� ������ ���������� <img src="_imgs/clans/police.gif" /> ������� �� ���������</td>
    </tr>
    <tr>
      <td>��� ������������: </td>
      <td><input name="cop" type="text" size="32" /></td>
    </tr>
    <tr>
      <td> ��� ���������, �� �������� ���� �������� ��������: </td>
      <td><input name="pers" type="text" size="32" /></td>
    </tr>
    <tr>
      <td> ���� ��������� ��������� � ������� �� ������� �����, �������������� ���</td>
      <td><input name="log" type="text" size="32" /></td>
    </tr>
    <tr>
      <td> ������� �������� ������������ </td>
      <td><textarea name="desc" cols="32" rows="6" wrap="VIRTUAL"></textarea></td>
    </tr>
    <tr>
      <td colspan=2 align="center"><input type="submit" name="Submit" value="���������" /></td>
    </tr>
  </table>
  <br />
    <input type="hidden" name="unick" id="unick">
  <input type="hidden" name="ulevel" id="ulevel">
  <input type="hidden" name="upro" id="upro">
  <input type="hidden" name="uclan" id="uclan">
  <input type="hidden" name="ucity" id="ucity">
  <input type="hidden" name="usid" id="usid">
</form>';
$abuse_form['common'] = '<form name="abuse_form" id="abuse_form" method="post" action="?act=abuse" enctype="multipart/form-data">
  <input name="kind" type="hidden" value="private" />
  <input name="step" type="hidden" value="do" />
  <table width="450" align="center"  border="0" cellspacing="5" cellpadding="5">
    <tr align="center">
      <td colspan="2"> ������ ������ ���������</td>
    </tr>
    <tr>
      <td>��� ������������: </td>
      <td><input name="cop" type="text" size="32" /></td>
    </tr>
    <tr>
      <td> ������� �������� ������������ </td>
      <td><textarea name="desc" cols="32" rows="6" wrap="VIRTUAL"></textarea></td>
    </tr>
    <tr>
      <td>c������� #1:</td>
      <td><input style="width: 250px" name="thumbnail1" type="file" /></td>
    </tr>
    <tr>
      <td>c������� #2:</td>
      <td><input style="width: 250px" name="thumbnail2" type="file" /></td>
    </tr>
    <tr>
      <td colspan=2 align="center"><input type="submit" name="Submit" value="���������" /></td>
    </tr>
  </table>
  <br />
    <input type="hidden" name="unick" id="unick">
  <input type="hidden" name="ulevel" id="ulevel">
  <input type="hidden" name="upro" id="upro">
  <input type="hidden" name="uclan" id="uclan">
  <input type="hidden" name="ucity" id="ucity">
  <input type="hidden" name="usid" id="usid">
</form>';
$abuse_form['private'] = '<form name="abuse_form" id="abuse_form" method="post" action="?act=abuse" enctype="multipart/form-data">
  <input name="kind" type="hidden" value="private" />
  <input name="step" type="hidden" value="do" />
  <table width="450" align="center"  border="0" cellspacing="5" cellpadding="5">
    <tr align="center">
      <td colspan="2"> ������ �� �������� �������</td>
    </tr>
    <tr>
      <td> ��� ���������, �������� ��� �������� ������: </td>
      <td><input name="pers" type="text" size="32" /></td>
    </tr>
    <tr>
      <td> ������� �� ������� �����, �������������� ���� ��������� ��������</td>
      <td><input name="desc" type="text" size="32" /></td>
    </tr>
    <tr>
      <td>c������� #1:</td>
      <td><input style="width: 250px" name="thumbnail1" type="file" /></td>
    </tr>
    <tr>
      <td>c������� #2:</td>
      <td><input style="width: 250px" name="thumbnail2" type="file" /></td>
    </tr>
    <tr>
      <td colspan=2 align="center"><input type="submit" name="Submit" value="���������" /></td>
    </tr>
  </table>
  <br />
    <input type="hidden" name="unick" id="unick">
  <input type="hidden" name="ulevel" id="ulevel">
  <input type="hidden" name="upro" id="upro">
  <input type="hidden" name="uclan" id="uclan">
  <input type="hidden" name="ucity" id="ucity">
  <input type="hidden" name="usid" id="usid">
</form>';
$abuse_form['law'] = '<form name="abuse_form" id="abuse_form" method="post" action="?act=abuse" enctype="multipart/form-data">
  <input name="kind" type="hidden" value="law" />
  <input name="step" type="hidden" value="do" />
  <table width="450" align="center"  border="0" cellspacing="5" cellpadding="5">
    <tr align="center">
      <td colspan="2"> ������ �� ������ ���������� <img src="_imgs/clans/police.gif" /> ������� �� ���������� �������� �� ������� ����� �������</td>
    </tr>
    <tr>
      <td> ��� ������������ � ���������� �������� �� �� ��������: </td>
      <td><input name="cop" type="text" size="32" /></td>
    </tr>
    <tr>
      <td> ������� �������� ������������ </td>
      <td><textarea name="desc" cols="32" rows="6" wrap="VIRTUAL"></textarea></td>
    </tr>
    <tr>
      <td colspan=2 align="center"><input type="submit" name="Submit" value="���������" /></td>
    </tr>
  </table>
  <br />
    <input type="hidden" name="unick" id="unick">
  <input type="hidden" name="ulevel" id="ulevel">
  <input type="hidden" name="upro" id="upro">
  <input type="hidden" name="uclan" id="uclan">
  <input type="hidden" name="ucity" id="ucity">
  <input type="hidden" name="usid" id="usid">
</form>';
$abuse_form['or'] = '<form name="abuse_form" id="abuse_form" method="post" action="?act=abuse" enctype="multipart/form-data">
  <input name="kind" type="hidden" value="or" />
  <input name="step" type="hidden" value="do" />
  <table width="450" align="center"  border="0" cellspacing="5" cellpadding="5">
    <tr align="center">
      <td colspan="2"> ������ �� ������ ���������� ������ ������������� <img src="_imgs/clans/police.gif" /> �������</td>
    </tr>
    <tr>
      <td> ��� ������������ � ���������� �������� �� �� ��������: </td>
      <td><input name="cop" type="text" size="32" /></td>
    </tr>
    <tr>
      <td> ������ �� ��� � ����������� ��������������, � ����������� �������� �� �� ��������</td>
      <td><input name="link" type="text" size="32" /></td>
    </tr>
    <tr>
      <td> ������� �������� ������������ </td>
      <td><textarea name="desc" cols="32" rows="6" wrap="VIRTUAL"></textarea></td>
    </tr>
    <tr>
      <td colspan=2 align="center"><input type="submit" name="Submit" value="���������" /></td>
    </tr>
  </table>
  <br />
    <input type="hidden" name="unick" id="unick">
  <input type="hidden" name="ulevel" id="ulevel">
  <input type="hidden" name="upro" id="upro">
  <input type="hidden" name="uclan" id="uclan">
  <input type="hidden" name="ucity" id="ucity">
  <input type="hidden" name="usid" id="usid">
</form>';
$abuse_form['obep'] = '<form name="abuse_form" id="abuse_form" method="post" action="?act=abuse" enctype="multipart/form-data">
  <input name="kind" type="hidden" value="obep" />
  <input name="step" type="hidden" value="do" />
  <table width="450" align="center"  border="0" cellspacing="5" cellpadding="5">
    <tr align="center">
      <td colspan="2"> ������ �� ������ ���������� <img src="_imgs/clans/police.gif" /> ������� �� ������������� ������������� ������������</td>
    </tr>
    <tr>
      <td> ��� ������������ � ���������� �������� �� �� ��������: </td>
      <td><input name="cop" type="text" size="32" /></td>
    </tr>
    <tr>
      <td> ������ �� ��� � ����������� ��������������, � ����������� �������� �� �� ��������</td>
      <td><input name="link" type="text" size="32" /></td>
    </tr>
    <tr>
      <td> ������� �������� ������������ </td>
      <td><textarea name="desc" cols="32" rows="6" wrap="VIRTUAL"></textarea></td>
    </tr>
    <tr>
      <td colspan=2 align="center"><input type="submit" name="Submit" value="���������" /></td>
    </tr>
  </table>
  <br />
    <input type="hidden" name="unick" id="unick">
  <input type="hidden" name="ulevel" id="ulevel">
  <input type="hidden" name="upro" id="upro">
  <input type="hidden" name="uclan" id="uclan">
  <input type="hidden" name="ucity" id="ucity">
  <input type="hidden" name="usid" id="usid">
</form>';
// end forms

if (@$_REQUEST['step'] == 'do')
	{
    	$abuser_name = $_REQUEST['unick'];
        $abuser_level = $_REQUEST['ulevel'];
        $abuser_pro = $_REQUEST['upro'];
        $abuser_clan = $_REQUEST['uclan'];
        $abuser_sid = $_REQUEST['usid'];
        $abuser_city = $_REQUEST['ucity'];
	    @mkdir("_imgs/abuse",0777);
	    $folder="_imgs/abuse/";
	    function make_seed()
        	{
	            list($usec, $sec) = explode(' ', microtime());
	            return (float) $sec + ((float) $usec * 100000);
			}
	    srand(make_seed());
        $thumb1 = "";
        $thumb2 = "";
	    $fname1 = chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)). chr(rand(97,122)) . chr(rand(97,122)) . rand(1,9999);
        $fname2 = chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)). chr(rand(97,122)) . chr(rand(97,122)) . rand(1,9999);
	    #
	    # Handling possible screenshots attached/ No resize, only convert to greyscale
	    if(is_file($_FILES['thumbnail1']['tmp_name']))
        	{
	        	$info=GetImageSize($_FILES['thumbnail1']['tmp_name']);
			    switch($info[2])
                	{
	            		case 2: $type="jpg"; break;
	            		default: $type="error";
	        		}
	        	if($type=="error") echo "<h2>��������� ������ ����������� JPG (������ ���������� �� ������� ��)</h2>";
	        	else
                	{
                        $thumb1 = $fname1.".jpg";
                        $originalFileName    = $_FILES['thumbnail1']['tmp_name'];
	                    $destinationFileName = $folder."/".$fname1.".jpg";
	                    $sourceImage = imagecreatefromjpeg($originalFileName);
	                    $img_width  = imageSX($sourceImage);
	                    $img_height = imageSY($sourceImage);
	                    for ($y = 0; $y <$img_height; $y++)
                    	{
	           for ($x = 0; $x <$img_width; $x++) {
	               $rgb = imagecolorat($sourceImage, $x, $y);
	               $red  = ($rgb >> 16) & 0xFF;
	               $green = ($rgb >> 8)  & 0xFF;
	               $blue  = $rgb & 0xFF;
	               $gray = round(.299*$red + .587*$green + .114*$blue);
	               // shift gray level to the left
	               $grayR = $gray << 16;  // R: red
	               $grayG = $gray << 8;    // G: green
	               $grayB = $gray;        // B: blue
	               // OR operation to compute gray value
	               $grayColor = $grayR | $grayG | $grayB;
	               // set the pixel color
	               imagesetpixel ($sourceImage, $x, $y, $grayColor);
	               imagecolorallocate ($sourceImage, $gray, $gray, $gray);
	           }
	       }
	       // copy pixel values to new file buffer
	       $destinationImage = ImageCreateTrueColor($img_width, $img_height);
	       imagecopy($destinationImage, $sourceImage, 0, 0, 0, 0, $img_width, $img_height);
	       // create file on disk
	       imagejpeg($destinationImage, $destinationFileName, 20);
	       // destroy temp image buffers
	       imagedestroy($destinationImage);
	       imagedestroy($sourceImage);
	       $screen1 = $destinationFileName;
	    }}
	    if(is_file($_FILES['thumbnail2']['tmp_name'])) {
	        $info=GetImageSize($_FILES['thumbnail2']['tmp_name']);
	        switch($info[2]) {
	            case 2: $type="jpg"; break;
	            default: $type="error";
	        }
	        if($type=="error") echo "<h2>��������� ������ ����������� JPG (������ ���������� �� ������� ��)</h2>";
	        else {
	       // replace with your files
           $thumb2 = $fname2.".jpg";
           $originalFileName    = $_FILES['thumbnail2']['tmp_name'];
	       $destinationFileName = $folder."/".$fname2.".jpg";
	       $sourceImage = imagecreatefromjpeg($originalFileName);
	       // get image dimensions
	       $img_width  = imageSX($sourceImage);
	       $img_height = imageSY($sourceImage);
	       for ($y = 0; $y <$img_height; $y++) {
	           for ($x = 0; $x <$img_width; $x++) {
	               $rgb = imagecolorat($sourceImage, $x, $y);
	               $red  = ($rgb >> 16) & 0xFF;
	               $green = ($rgb >> 8)  & 0xFF;
	               $blue  = $rgb & 0xFF;
	               $gray = round(.299*$red + .587*$green + .114*$blue);
	               // shift gray level to the left
	               $grayR = $gray << 16;  // R: red
	               $grayG = $gray << 8;    // G: green
	               $grayB = $gray;        // B: blue
	               // OR operation to compute gray value
	               $grayColor = $grayR | $grayG | $grayB;
	               // set the pixel color
	               imagesetpixel ($sourceImage, $x, $y, $grayColor);
	               imagecolorallocate ($sourceImage, $gray, $gray, $gray);
	           }
	       }
	       // copy pixel values to new file buffer
	       $destinationImage = ImageCreateTrueColor($img_width, $img_height);
	       imagecopy($destinationImage, $sourceImage, 0, 0, 0, 0, $img_width, $img_height);
	       // create file on disk
	       imagejpeg($destinationImage, $destinationFileName, 20);
	       // destroy temp image buffers
	       imagedestroy($destinationImage);
	       imagedestroy($sourceImage);
	       $screen2 = $destinationFileName;
	    }}

$query = "INSERT INTO `abuse` (`id`, `kind`, `date`, `abuser`, `abuse_text`, `abuse_extra`, `screen1`, `screen2`, `cop`, `processed`, `answer`, `remote`)
VALUES
('', '".$_REQUEST['kind']."', '".time()."', '".$_REQUEST['unick']."', '', '', '".$thumb1."', '".$thumb2."', '', '0', '', '0')";
mysql_query($query) or die(mysql_error());
}

?>

<!--
<form method='post' enctype='multipart/form-data' action='?act=abuse'>
��������: <input style='150px' name='thumbnail1' type='file'>
<br>
��������: <input style='150px' name='thumbnail2' type='file'>
<br>
��� ����: <?=$cops_list?>
<br>
���: <select size="1" name="kind">
<option selected value="0">�������������</option>
<option value="1">������ �� ���������</option>
<option value="2">������ �� �������� �������</option>
<option value="3">������ �� �������� �� �������</option>
<option value="4">������ �� ����� ��������</option>
<option value="5">������ �� ����� �������������</option>
<option value="6">������ �� ����</option>
</select>
<br>
<textarea cols="40" rows="6" name='text'>����� ���������</textarea>
<br><br>
<div align=center><input type="submit" value="UpLoad"></div>
<br>
</form>
-->
<?
echo ($abuse_form[$abtype]);
if ($type !== "default")
	{
?>
<OBJECT id="tz" codeBase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" height="1" width="1" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><PARAM NAME="movie" VALUE="http://tzpolice.ru/_imgs/auth.swf"><PARAM NAME="wmode" VALUE="transparent">
<embed src="http://tzpolice.ru/_imgs/auth.swf" wmode="transparent" width="1" height="1" swLiveConnect="true" id="tz" name="tz" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</OBJECT>
<script language="JavaScript" type="text/javascript">
<!--
var timeout = null;
function tz_DoFSCommand(command, args) {
	var tmp = args.split("\t");
	if (command == "OK")
    	{
            var pers_nick = '' + tmp[0];
			var pers_sid = '' + tmp[1];
			var pers_city = '' + tmp[2];
            var pers_level = '' + tmp[3];
            var pers_pro = '' + tmp[4];
            var pers_clan = '' + tmp[5];
            document.getElementById("unick").value=pers_nick;
  			document.getElementById("ulevel").value=pers_level;
  			document.getElementById("upro").value=pers_pro;
  			document.getElementById("uclan").value=pers_clan;
  			document.getElementById("ucity").value=pers_city;
  			document.getElementById("usid").value=pers_sid;
        }
}
if (navigator.appName.indexOf("Microsoft") != -1) {// Hook for Internet Explorer.
	document.write('<script language=\"VBScript\"\>\n');
	document.write('On Error Resume Next\n');
	document.write('Sub tz_FSCommand(ByVal command, ByVal args)\n');
	document.write('	Call tz_DoFSCommand(command, args)\n');
	document.write('End Sub\n');
	document.write('</script\>\n');
}
</script>
<?}?>
