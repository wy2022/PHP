<?php
// error_reporting(NULL);
// ini_set('display_errors','Off');
header ( "Content-type: text/html; charset=UTF-8" );

 
function characet2($data) 
{
    if (! empty ( $data )) 
    {
        $fileType = mb_detect_encoding ( $data, array (
                'UTF-8',
                'GBK',
                'GB2312',
                'LATIN1',
                'BIG5' 
        ) );

        if ($fileType != 'UTF-8') 
        {
           $data = mb_convert_encoding ( $data, 'UTF-8', $fileType );
        }
    }
    return $data;
}


 function writelog2($text, $aType='') 
 {
   $text = characet2($text);
   file_put_contents (dirname ( __FILE__ ).'/logs'."/log_native1_".$aType._. date( "Y-m-d" ).".txt", date ( "Y-m-d H:i:s" ) . "  " . $text . "\r\n", FILE_APPEND );
   
  } 



  function characet($data, $targetCharset) 
  {

    if (!empty($data)) {
      $fileType = "UTF-8";
      if (strcasecmp($fileType, $targetCharset) != 0) {

        $data = mb_convert_encoding($data, $targetCharset);
      }
    }
    return $data;
  }




   





  function create_guid() 
  {
      $charid = strtoupper(md5(uniqid(mt_rand(), true)));
      $hyphen = chr(45);// "-"
      $uuid = chr(123)// "{"
      .substr($charid, 0, 8).$hyphen
      .substr($charid, 8, 4).$hyphen
      .substr($charid,12, 4).$hyphen
      .substr($charid,16, 4).$hyphen
      .substr($charid,20,12)
      .chr(125);// "}"
      return $uuid;
  }

   function Day1($day)
   {   
    return date('m-d',strtotime("-$day day"));
   }






