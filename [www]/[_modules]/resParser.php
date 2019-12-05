<?php
/*
 function get($buffer,$from,$to,$offset=0){
      $first=strpos($buffer,$from,$offset)+strlen($from);
      $second=strpos($buffer,$to,$first);
      if(!is_numeric($first) || !is_numeric($second)) return NULL;
      $result=substr($buffer,$first,$second-$first);
      if(is_numeric($first) && is_numeric($second)) return $result;
  }
 function OpenFile($filename,$how){
    $fd = fopen($filename,$how);
    if(!$fd) exit("Невозможно открыть файл");
    else
    {
      while (!feof ($fd))
      {
        $buffer .= fread($fd, 4096);
        $buffer = trim(chop($buffer));

      }
    }
    fclose ($fd);
    return $buffer;
  }
  function arrayCombine($arr1, $arr2){
      $result = array();
      for ($i = 0; $i < count($arr1); $i++)  {
          $result[$arr1[$i]] = $arr2[$i];
      }

      return $result;
  }

  $filename = "http://www.timezero.ru/res.xml";
  $buffer = OpenFile($filename,"r");

  preg_match_all("#<S (.*?)>(.*?)</S>#s", $buffer, $array);

  // Shop information
  $shop = array();
  for ($i = 0; $i < count($array[1]); $i++)  {
      preg_match_all("/([a-z]+)=\"([^\"]+)\"/i", $array[1][$i], $shop[$i]);
      $shop[$i] = arrayCombine($shop[$i][1], $shop[$i][2]);
  }


  for ($i = 0; $i < count($array[2]); $i++) {
      preg_match_all("#<R(.*?)/>#s", $array[2][$i], $res);
      $res = $res[1];
      for ($j = 0; $j < count($res); $j++) {
          preg_match_all("/([a-z]+)=\"([^\"]+)\"/i", $res[$j], $shr);
          $shopResources[$j] = arrayCombine($shr[1], $shr[2]);
      }
      $shop[$i]["resources"] = $shopResources;
  }
*/
include "/home/sites/police/dbconn/dbconn.php";  
$jsStr =  "";
$query = "SELECT * FROM `resprice_current`";
$res = mysql_query($query);
while ($d = mysql_fetch_array($res))
	{
      $jsStr .= "{'name':'" . $d['shop'] . "', 'city':'" . $d['city'] . "', 'xy':'" . $d['xy'] . "', ";
      $jsStr .= "'resources':[".$d['metals'].",".$d['gold'].",".$d['polymers'].",".$d['organic'].",".$d['silicon'].",".$d['radioactive'].",".$d['gems'].",".$d['venom'];
      $jsStr .= "]}|";
	}      
  $jsStr = substr($jsStr, 0, -1);

  echo $jsStr;
?>