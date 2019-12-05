<?php

//  error_reporting(E_ALL);

  function addCell(&$worksheet, $row, &$column, $text)
  {
    $worksheet->write($row, $column++, $text);
  }

  function extract_date($date) {
    $arr = explode(" ",$date);

    $d = $arr[0];
    $t = $arr[1];

    $arr_d = explode("-",$d);
    $year = $arr_d[0];
    $year = intval($year);
    $month = $arr_d[1];
    $month = intval($month);
    $day = $arr_d[2];
    $day = intval($day);

    $arr_t = explode(":",$t);
    $hour = $arr_t[0];
    $hour = intval($hour);
    $minute = $arr_t[1];
    $minute = intval($minute);
    $second = $arr_t[2];
    $second = intval($second);

    return array($hour, $minute, $second, $month, $day, $year);
  }

// Функции. Можно вынести в дpугой файл.
/*
class html_mime_mail {
  var $headers;
  var $multipart;
  var $mime;
  var $html;
  var $parts = array();

function html_mime_mail($headers="") {
    $this->headers=$headers;
}

function add_html($html="") {
    $this->html.=$html;
}

function build_html($orig_boundary,$kod) {
    $this->multipart.="--$orig_boundary\n";
    if ($kod=='w' || $kod=='win' || $kod=='windows-1251') $kod='windows-1251';
    else $kod='koi8-r';
    $this->multipart.="Content-Type: text/plain; charset=$kod\n";
    $this->multipart.="BCC: del@ipo.spb.ru\n";
    $this->multipart.="Content-Transfer-Encoding: Quot-Printed\n\n";
    $this->multipart.="$this->html\n\n";
}


function add_attachment($path="", $name = "", $c_type="application/octet-stream") {
    if (!file_exists($path.$name)) {
      print "File $path.$name dosn't exist.";
      return;
    }
    $fp=fopen($path.$name,"r");
    if (!$fp) {
      print "File $path.$name coudn't be read.";
      return;
    }
    $file=fread($fp, filesize($path.$name));
    fclose($fp);
    $this->parts[]=array("body"=>$file, "name"=>$name,"c_type"=>$c_type);
}


function build_part($i) {
    $message_part="";
    $message_part.="Content-Type: ".$this->parts[$i]["c_type"];
    if ($this->parts[$i]["name"]!="")
       $message_part.="; name = \"".$this->parts[$i]["name"]."\"\n";
    else
       $message_part.="\n";
    $message_part.="Content-Transfer-Encoding: base64\n";
    $message_part.="Content-Disposition: attachment; filename = \"".
       $this->parts[$i]["name"]."\"\n\n";
    $message_part.=chunk_split(base64_encode($this->parts[$i]["body"]))."\n";
    return $message_part;
}


function build_message($kod) {
    $boundary="=_".md5(uniqid(time()));
    $this->headers.="MIME-Version: 1.0\n";
    $this->headers.="Content-Type: multipart/mixed; boundary=\"$boundary\"\n";
    $this->multipart="";
    $this->multipart.="This is a MIME encoded message.\n\n";
    $this->build_html($boundary,$kod);
    for ($i=(count($this->parts)-1); $i>=0; $i--)
      $this->multipart.="--$boundary\n".$this->build_part($i);
    $this->mime = "$this->multipart--$boundary--\n";
}


function send($server, $to, $from, $subject="", $headers="") {

    $headers="To: $to\nFrom: $from\nSubject: $subject\nX-Mailer: The Mouse!\n$headers";
    $fp = fsockopen($server, 25, &$errno, &$errstr, 30);
    if (!$fp)
       die("Server $server. Connection failed: $errno, $errstr");
    fputs($fp,"HELO $server\n");
    fputs($fp,"MAIL FROM: $from\n");

    if (strpos($to, ','))
    {
      $a = explode(',', $to);
      foreach ($a as $value)
      {
        fputs($fp,"RCPT TO: $value\n");
      }
    }
    else
      fputs($fp,"RCPT TO: $to\n");

    fputs($fp,"DATA\n");
    fputs($fp,$this->headers);
    if (strlen($headers))
      fputs($fp,"$headers\n");
    fputs($fp,$this->mime);
    fputs($fp,"\n.\nQUIT\n");
    while(!feof($fp))
      $resp.=fgets($fp,1024);
    fclose($fp);
  }
}
*/
  require_once "/home/sites/police/www/_cron/excel/class.writeexcel_workbook.inc.php";
  require_once "/home/sites/police/www/_cron/excel/class.writeexcel_worksheet.inc.php";
  error_reporting(E_ALL);
//определяем дату
/*  if ($_REQUEST['dateto'])
  {
    $now = $_REQUEST['dateto'];
    $now_arr = explode('-', $now);
    $a[3] = $now_arr[1];
    $a[4] = $now_arr[2];
    $a[5] = $now_arr[0];
  }
  else
  {
*/
    $a = extract_date(strftime("%Y-%m-%d %H:%M:%S", mktime()));
    $now = strftime("%Y-%m-%d");
//  }

  $week = strftime("%Y-%m-%d", mktime(0,0,0,$a[3],$a[4]-6,$a[5]));

  $filename = "prison_".$week."_".$now.".xls";
  $fname = "/home/sites/police/tmp/".$filename;

  $workbook = &new writeexcel_workbook($fname);
  $worksheet = &$workbook->addworksheet();

  include "/home/sites/police/www/_modules/functions.php";
  include "/home/sites/police/dbconn/dbconn2.php";
  error_reporting(E_ALL);


//пишем хедер
  $row = 0;
  $column = 0;
  addCell($worksheet, $row, $column, ' ');
  addCell($worksheet, $row, $column, ' ');
  addCell($worksheet, $row, $column, 'Новые каторжане с '.$week.' по '.$now);

  $row = 1;
  $column = 0;
  addCell($worksheet, $row, $column, 'Ник');
  addCell($worksheet, $row, $column, 'Причина');
  addCell($worksheet, $row, $column, 'Срок');
  addCell($worksheet, $row, $column, 'Комментарий');
  addCell($worksheet, $row, $column, 'Дата добавления');
  addCell($worksheet, $row, $column, 'Добавивший');

  $sql = 'SELECT * FROM prison_chars WHERE add_date>="'.$week.'" ORDER BY add_level desc, add_date';
//echo ($sql);
  $res = mysql_query($sql);
  while ($roww = mysql_fetch_assoc($res))
  {
    $column = 0;
    $row++;
//	echo ($roww['nick']."<br>");
    addCell($worksheet, $row, $column, $roww['nick'].' ['.$roww['add_level'].']');
    if ($roww['reason'] > 0)
      addCell($worksheet, $row, $column, $crime[$roww['reason']]);
    else
      addCell($worksheet, $row, $column, 'Нет в БД');
    if ($roww['term'] > 0)
      addCell($worksheet, $row, $column, $roww['term']);
    else
      addCell($worksheet, $row, $column, 'Нет в БД');
    addCell($worksheet, $row, $column, $roww['remark']);
    addCell($worksheet, $row, $column, $roww['add_date']);
    addCell($worksheet, $row, $column, $roww['add_by']);
  }
  $workbook->close();

// $to = 'gukxeg@yandex.ru,stealth@open.by,macuta@timezero.ru';
// $to = 'andrei@ra-grifon.ru';
	$to = 'stealth@open.by,macuta@timezero.ru';

//  sendMail($to, 'true@tzpolice.ru','TZPD mail daemon','Новые каторжане с '.$week.' по '.$now,'',$fname);

/*
$mail=new html_mime_mail();
  $mail->add_attachment("/home/sites/police/tmp/",$filename);
  $mail->build_message('win'); если не "win", то кодиpовка koi8

$mail->send('localhost',
              $to,
              'true@tzpolice.ru',
              'Новые каторжане с '.$week.' по '.$now);
*/
function xmail( $from, $to, $subj, $text, $filename)
{
$f = fopen($filename,"rb");
$un = strtoupper(uniqid(time()));
$head = "from: $from\n";
//$head .= "to: $to\n";
//$head .= "subject: $subj\n";
$head .= "x-mailer: police mailer\n";
$head .= "reply-to: $from\n";
$head .= "mime-version: 1.0\n";
$head .= "content-type:multipart/mixed;";
$head .= "boundary=\"----------".$un."\"\n\n";
$zag = "------------".$un."\ncontent-type:text/html;\n";
$zag .= "content-transfer-encoding: 8bit\n\n$text\n\n";
$zag .= "------------".$un."\n";
$zag .= "content-type: application/octet-stream;";
$zag .= "name=\"".basename($filename)."\"\n";
$zag .= "content-transfer-encoding:base64\n";
$zag .= "content-disposition:attachment;";
$zag .= "filename=\"".basename($filename)."\"\n\n";
$zag .= chunk_split(base64_encode(fread($f,filesize($filename))))."\n";
if (!@mail("$to", "$subj", $zag, $head))
return 0;
else
return 1;
}
$res = xmail('Police Mailer <site_abuse@tzpolice.ru>', $to, 'New prisoners since '.$week.' till '.$now.'.','See attached file',$fname);
/*header("Content-Type: application/x-msexcel; name=\"prison.xls\"");
header("Content-Disposition: inline; filename=\"prison.xls\"");
$fh=fopen($fname, "rb");
fpassthru($fh);*/
unlink($fname);
?>