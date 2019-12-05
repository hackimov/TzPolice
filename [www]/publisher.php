<?php
// General configuration
error_reporting(E_ALL);
require ("/home/sites/police/dbconn/dbconn.php");
require ("/home/sites/police/www/_modules/functions.php");
function make_seed() {
  list($usec, $sec) = explode(' ', microtime());
  return (float) $sec + ((float) $usec * 100000);
}

$protocol = 'http';
if (isset($_SERVER[ 'HTTPS' ]))
  if ($_SERVER[ 'HTTPS' ] == 'on')
	$protocol .= 's';

$cfg = array(
	'wizardheadline'    => 'фотогалерея TZPolice.ru',
	'wizardbyline'      => 'Старейшая фотогалерея TimeZero',
	'finalurl'          => 'http://www.tzpolice.ru/',
	'registrykey'       => strtr($_SERVER[ 'HTTP_HOST' ], '.:', '__'),
	'wizardname'        => $_SERVER[ 'HTTP_HOST' ],
	'wizarddescription' => 'Добавление фотографий в фотогалерею TZPolice.ru'
	);

    // Determine page/step to display, as this script contains a four-step wizard:
// "login", "options", "check", "upload" (+ special "reg" mode, see below)

$allsteps = array( 'login', 'options', 'check', 'upload', 'reg' );

$step = 'login';

if (isset($_REQUEST[ 'step' ]))
  if (in_array($_REQUEST[ 'step' ], $allsteps))
	$step = $_REQUEST[ 'step' ];


// Special registry file download mode:
// Call this script in your browser and set ?step=reg to download a .reg file for registering
// your server with the Windows XP Publishing Wizard

if ($step == 'reg')
  { header('Content-Type: application/octet-stream; name="tzpd_wiz_settings.reg"');
	header('Content-disposition: attachment; filename="tzpd_wiz_settings.reg"');

	echo
		'Windows Registry Editor Version 5.00' . "\n\n" .
		'[HKEY_CURRENT_USER\\Software\\Microsoft\\Windows\\CurrentVersion\\Explorer\\PublishingWizard\\PublishingWizard\\Providers\\' . $cfg[ 'registrykey' ] . ']' . "\n" .
		'"displayname"="' . $cfg[ 'wizardname' ] . '"' . "\n" .
		'"description"="' . $cfg[ 'wizarddescription' ] . '"' . "\n" .
		'"href"="' . $protocol . '://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'PHP_SELF' ] . '"' . "\n" .
		'"icon"="' . $protocol . '://' . $_SERVER[ 'HTTP_HOST' ] . dirname($_SERVER[ 'PHP_SELF' ]) . '/favicon.ico"';

	exit;
  }


// Send no-cache headers

header('Expires: Mon, 26 Jul 2002 05:00:00 GMT');              // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: no-cache="set-cookie", private');       // HTTP/1.1
header('Pragma: no-cache');                                    // HTTP/1.0


// Start session

session_name('phpxppubwiz');
@session_start();

if (! isset($_SESSION[ 'authuser' ]))
	{
		$_SESSION[ 'authuser' ] = '';
    }


// Send character set header
header('Content-Type: text/html; charset=windows-1251');
// Set maximum execution time to unlimited to allow large file uploads
set_time_limit(0);
?>
<html>
<head>
<title>TZPD Client for XP Publishing Wizard</title>
<style type="text/css">

body,a,p,span,td,th,input,select,textarea {
	font-family:verdana,arial,helvetica,geneva,sans-serif,serif;
	font-size:10px;
}

</style>
</head>
<body>
<?php

// Variables for the XP wizard buttons

$WIZARD_BUTTONS = 'false,true,false';
$ONBACK_SCRIPT  = '';
$ONNEXT_SCRIPT  = '';


// Authenticate

if (isset($_REQUEST[ 'user' ]) && isset($_REQUEST[ 'password' ]))
{
        $tmu = trim(urldecode(strip_tags($_REQUEST['user'])));
        $tmp = md5($_REQUEST['password']);
        $query = "SELECT `id` FROM `site_users` WHERE `user_name`='".$tmu."' AND `user_pass`='".$tmp."' LIMIT 1;";
        $res = mysql_query($query);
	    if(mysql_num_rows($res)>0) $_SESSION[ 'authuser' ] = $_REQUEST[ 'user' ];
}


// Check page/step

if ($_SESSION[ 'authuser' ] == '')
  $step = 'login';
elseif ($step == 'login')
  $step = 'options';

if ($step == 'check')
  if (! (isset($_REQUEST[ 'manifest' ])))
	$step = 'options';

if ($step == 'check')
  if ($_REQUEST[ 'manifest' ] == '')
	$step = 'options';

// Step 1: Display login form

if ($step == 'login')
  { ?>

	<form method="post" id="login" action="<?php echo $_SERVER[ 'PHP_SELF' ]; ?>">
	<center>
	<h3>Пожалуйста, укажите логин и пароль для сайта TZPolice.ru</h3>
	<table border="0">
	<tr>
		<td>Логин:</td>
		<td><input type="text" name="user" value="" /></td>
	</tr>
	<tr>
		<td>Пароль:</td>
		<td><input type="password" name="password" value="" /></td>
	</tr>
	</table>
	</center>
	<input type="hidden" name="step" value="options" />
	</form>

	<?php
	$ONNEXT_SCRIPT  = 'login.submit();';
	$ONBACK_SCRIPT  = 'window.external.FinalBack();';
	$WIZARD_BUTTONS = 'true,true,false';
  }


// Step 2: Display options form (directory choosing)

if ($step == "options")
  { ?>
	<form method="post" id="options" action="<?php echo $_SERVER[ 'PHP_SELF' ]; ?>">
	<center>
	<h3>Укажите имя для отображения в галерее, город, возраст и пол</h3>
<table border="0">
<tr>
  <td>Имя:&nbsp;</td>
  <td><input name="f_name" type="text" size="30" value=""></td>
</tr>
<tr>
  <td>Город:&nbsp;</td>
  <td><input name="f_city" type="text" size="30" value=""></td>
</tr>
<tr>
  <td>Пол:&nbsp;</td>
  <td><select size="1" name="f_gener"><option selected value="1">Муж</option><option value="2">Жен</option></select></td>
</tr>
<tr>
  <td>Возраст:&nbsp;</td>
  <td><input name="f_age" type="text" maxlength="2" size="7" value=""></td>
</tr>
<tr>
  <td>Комментарий:&nbsp;</td>
  <td><input name="f_comment" type="text" maxlength="150" size="30" value=""></td>
</tr>
</table>
	</center>
* - комментарий будет добавлен ко всем фотографиям. Можно оставить поле пустым.
	<input type="hidden" name="step" value="check" />
	<input type="hidden" name="manifest" value="" />
	<script>

	function docheck()
	{ var xml = window.external.Property('TransferManifest');
	  options.manifest.value = xml.xml;
	  options.submit();
	}

	</script>

	</form>

	<?php

   $ONNEXT_SCRIPT  = "docheck();";
   $WIZARD_BUTTONS = "false,true,false";
  }

?>

<div id="content"/>

</div>

<?php

// Step 3: Check file list + selected options, prepare file upload

if ($step == "check")
  { /* Now we're embedding the HREFs to POST to into the transfer manifest.

	The original manifest sent by Windows XP looks like this:

	<transfermanifest>
		<filelist>
			<file id="0" source="C:\pic1.jpg" extension=".jpg" contenttype="image/jpeg" destination="pic1.jpg" size="530363">
				<metadata>
					<imageproperty id="cx">1624</imageproperty>
					<imageproperty id="cy">2544</imageproperty>
				</metadata>
			</file>
			<file id="1" source="C:\pic2.jpg" extension=".jpg" contenttype="image/jpeg" destination="pic2.jpg" size="587275">
				<metadata>
					<imageproperty id="cx">1960</imageproperty>
					<imageproperty id="cy">3008</imageproperty>
				</metadata>
			</file>
		</filelist>
	</transfermanifest>

	We will add a <post> child to each <file> section, and an <uploadinfo> child to the root element.
	*/

	// stripslashes if the evil "magic_quotes_gpc" are "on" (hint by Juan Valdez <juanvaldez123@hotmail.com>)

	if (ini_get('magic_quotes_gpc') == '1')
	  $manifest = stripslashes($_REQUEST[ 'manifest' ]);
	else
	  $manifest = $_REQUEST[ 'manifest' ];

	$parser = xml_parser_create();

	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);

	$xml_ok = xml_parse_into_struct($parser, $manifest, $tags, $index);

	$manifest = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>";

	foreach ($tags as $i => $tag)
	  { if (($tag[ 'type' ] == 'open') || ($tag[ 'type' ] == 'complete'))
		  { if ($tag[ 'tag' ] == 'file')
			  $filedata = array(
				'id'                => -1,
				'source'            => '',
				'extension'         => '',
				'contenttype'       => '',
				'destination'       => '',
				'size'              => -1,
				'imageproperty_cx'  => -1,
				'imageproperty_cy'  => -1
				);

			$manifest .= '<' . $tag[ 'tag' ];

			if (isset($tag[ 'attributes' ]))
			  foreach ($tag[ 'attributes' ] as $key => $value)
				{ $manifest .= ' ' . $key . '="' . $value . '"';

				  if ($tag[ 'tag' ] == 'file')
					$filedata[ $key ] = $value;
				}

			if (($tag[ 'type' ] == 'complete') && (! isset($tag[ 'value' ])))
			  $manifest .= '/';

			$manifest .= '>';

			if (isset($tag[ 'value' ]))
			  { $manifest .= htmlspecialchars($tag[ 'value' ]);

				if ($tag[ 'type' ] == 'complete')
				  $manifest .= '</' . $tag[ 'tag' ] . '>';

				if (($tag[ 'tag' ] == 'imageproperty') && isset($tag[ 'attributes' ]))
				  if (isset($tag[ 'attributes' ][ 'id' ]))
					$filedata[ 'imageproperty_' . $tag[ 'attributes' ][ 'id' ] ] = $tag[ 'value' ];
			  }
		  }
		elseif ($tag[ 'type' ] == 'close')
		  { if ($tag[ 'tag' ] == 'file')
			  { $protocol = 'http';
				if (isset($_SERVER[ 'HTTPS' ]))
				  if ($_SERVER[ 'HTTPS' ] == 'on')
					$protocol .= 's';

				$manifest .=
					'<post href="' . $protocol . '://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'PHP_SELF' ] . '" name="userfile">' .
					'	<formdata name="MAX_FILE_SIZE">10000000</formdata>' .
					'	<formdata name="step">upload</formdata>' .
					'	<formdata name="f_name">' . htmlspecialchars($_REQUEST[ 'f_name' ]) . '</formdata>' .
					'	<formdata name="f_city">' . htmlspecialchars($_REQUEST[ 'f_city' ]) . '</formdata>' .
					'	<formdata name="f_age">' . htmlspecialchars($_REQUEST[ 'f_age' ]) . '</formdata>' .
					'	<formdata name="f_comment">' . htmlspecialchars($_REQUEST[ 'f_comment' ]) . '</formdata>' .
					'	<formdata name="f_gener">' . htmlspecialchars($_REQUEST[ 'f_gener' ]) . '</formdata>';

				foreach ($filedata as $key => $value)
				  $manifest .= '<formdata name="' . $key . '">' . htmlspecialchars($value) . '</formdata>';

				$manifest .= '</post>';
			  }
			elseif ($tag[ 'level' ] == 1)
			  $manifest .= '<uploadinfo><htmlui href="' . $cfg[ 'finalurl' ] . '"/></uploadinfo>';

			$manifest .= '</' . $tag[ 'tag' ] . '>';
		  }
	  }

	// Check whether we created well-formed XML ...

	if (xml_parse_into_struct($parser,$manifest,$tags,$index) >= 0)
	  { ?>

		<script>

		var newxml = '<?php echo $manifest; ?>';
		var manxml = window.external.Property('TransferManifest');

		manxml.loadXML(newxml);

		window.external.Property('TransferManifest') = manxml;
		window.external.SetWizardButtons(true,true,true);

		content.innerHtml = manxml;
		window.external.FinalNext();

		</script>

		<?php
	  }
  }


// Step 4: This page will be called once for every file upload

if ($step == 'upload')
  {
		if (isset($_FILES))
		if (isset($_FILES[ 'userfile' ]) && file_exists($_FILES['userfile']['tmp_name']))
			{
            	$foto_newdir = "/home/sites/police/www/i/newfotos";
				$f_name = htmlspecialchars($_REQUEST['f_name']);
				$f_city = htmlspecialchars($_REQUEST['f_city']);
				$f_age = htmlspecialchars($_REQUEST['f_age']);
				$f_gener = $_REQUEST['f_gener'];
		                $f_comment = htmlspecialchars($_REQUEST['f_comment']);;
				srand(make_seed());
				$fname = chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)) . chr(rand(97,122)) . rand(1,999999);

				if(is_file($_FILES['userfile']['tmp_name']))
                	{
						$info=GetImageSize($_FILES['userfile']['tmp_name']);
						switch($info[2])
							{
								case 1: $type="gif"; break;
								case 2: $type="jpg"; break;
								case 3: $type="png"; break;
								default: $type="error";
							}

						if($type !== "error")
							{
								$base_name=time();
								if($info[0]>440 || $info[1]>600) MakePreview($_FILES['userfile']['tmp_name'],"{$foto_newdir}", 440, 600, $type, $fname);
								else copy($_FILES['userfile']['tmp_name'],"{$foto_newdir}/{$fname}.{$type}");
								$nnname = $foto_newdir."/".$fname.".".$type;
								unlink($_FILES['userfile']['tmp_name']);
								$fname = $fname . ".".$type;
								$temp_current_user_name = $_SESSION[ 'authuser' ];
								$gm_date = gmdate("Y-m-d", time());
								$gm_time = gmdate("H:i:s", time());
								$temp_clan = "";
								if (is_file($nnname))
									{
										$query = "INSERT into fotos_new (nick,file,name,city,gener,age,comment,date,time,clan) values ('$temp_current_user_name','$fname','$f_name','$f_city','$f_gener','$f_age','$f_comment','$gm_date','$gm_time','$temp_clan')";
										$result = mysql_query($query);
									}
							}
					}

			}
	}
?>

<script>

function OnBack()
{ <?php echo $ONBACK_SCRIPT; ?>
}

function OnNext()
{ <?php echo $ONNEXT_SCRIPT; ?>
}

function OnCancel()
{
  content.innerHtml+='<br>OnCancel';
}

function window.onload()
{ window.external.SetHeaderText("<?php echo strtr($cfg[ 'wizardheadline' ], '"', "'"); ?>","<?php echo strtr($cfg[ 'wizardbyline' ], '"', "'"); ?>");
  window.external.SetWizardButtons(<?php echo $WIZARD_BUTTONS; ?>);
}

</script>
<hr size="1">
<br><br><br><br>
<script language="JavaScript" type="text/javascript"><!--
document.write('<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" '+
'codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="88" height="33">'+
//'<param name="movie" value="http://www.timezero.ru/tzcnt.swf?ref='+escape(document.location)+'" />'+
//'<param name="allowScriptAccess" value="always" /><embed src="http://www.timezero.ru/tzcnt.swf?ref='+escape(document.location)+
'<param name="movie" value="http://www.timezero.ru/tzcnt.swf?ref=http%3A//www.tzpolice.ru" />'+
'<param name="allowScriptAccess" value="always" /><embed src="http://www.timezero.ru/tzcnt.swf?ref=http%3A//www.tzpolice.ru'+
'" allowScriptAccess="always" width="88" height="33" type="application/x-shockwave-flash" '+
'pluginspage="http://www.macromedia.com/go/getflashplayer" /></object>');
//-->
</script>                                                             
</body>
</html>