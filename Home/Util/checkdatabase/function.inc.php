<?php
error_reporting(NULL);
ini_set('display_errors','Off');
header ( "Content-type: text/html; charset=UTF-8" );

function characet($data) 
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


 function writelog($text, $aType='') 
 {
   $text = characet($text);
   file_put_contents (dirname ( __FILE__ ).'/logs'."/log_".$aType._. date( "Y-m-d" ).".txt", date ( "Y-m-d H:i:s" ) . "  " . $text . "\r\n", FILE_APPEND );
  } 




function strToHex($string)//字符串转十六进制
{ 
    return strtoupper(bin2hex($string)) ;
    $hex="";
    for($i=0;$i<strlen($string);$i++)
    $hex.=dechex(ord($string[$i]));
    $hex=strtoupper($hex);
    return $hex;
}   
 
function hexToStr($hex)//十六进制转字符串
{   
    $string=""; 
    for($i=0;$i<strlen($hex)-1;$i+=2)
    $string.=chr(hexdec($hex[$i].$hex[$i+1]));
    return  $string;
}
 
function aesEnJm($str, $key)
{
     
     $block = mcrypt_get_block_size('rijndael_128', 'ecb');
     $pad = $block - (strlen($key) % $block);
 
     $key .= str_repeat(chr($pad), $pad);
   // $pad = $block - (strlen($str ) % $block);
   //   $str .= str_repeat(chr($pad), $pad);
     // error_log(date('Y-m-d H:i:s:ms') ." ".strlen($str) ."\r\n", 3, './log/errors.log');  
     $str= mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB);
 
     $str= strToHex($str);
     return  ($str); 
}

function aesDeJm($Str, $key)
{     
     $Str=hexToStr($Str);
     // error_log(date('Y-m-d H:i:s:ms') ." ".$Str."\r\n", 3, './log/errors.log');  
     $block = mcrypt_get_block_size('rijndael_128', 'ecb');
     $pad = $block - (strlen($key) % $block);
 
     $key .= str_repeat(chr($pad), $pad);   
     $JmStr= mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $Str, MCRYPT_MODE_ECB); 
     
  return $JmStr;
}


function create_guid3() {  
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


function sendRequsttoOneServer($url, $post_data = '', $timeout = 5)
{
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_POST, 1);
    if($post_data != '')
    {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    }
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $file_contents = curl_exec($ch);

    $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE); 


    curl_close($ch);
    return $file_contents;
}



  function tcpsend_data1($service_port, $address, $valJson, $command, $timeout = 10)
  {
	  
	  
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	    socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec" => 2, "usec" => 0));   //发送超时2秒
		socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => $timeout, "usec" => 0)); //接收超时	  
		$result = socket_connect($socket, $address, $service_port);
		if (!$result)
			  return '连接失败！';
		$in = json_encode($valJson);
		$ret = '';
        $in1=sprintf("%010s",strlen($in));
        socket_write($socket,$in1,strlen($in1));
        socket_write($socket,$in,strlen($in));

      $len = socket_read($socket,10,PHP_BINARY_READ);
      $num = 1024;
      while (true)
      {
          if ($len > $num)
          {
              $tmp = socket_read($socket, $num, PHP_BINARY_READ);
              $ret.= $tmp;
              $len-= strlen($tmp);
          } else
          {
              $tmp = socket_read($socket, $len, PHP_BINARY_READ);
              $ret.= $tmp;
              $len-= strlen($tmp);
              if ($len <= 0)
                  break;
          }
      }
  
      $errNo = socket_last_error($socket);
      if (0 != $errNo)
      {
          echo("<script>alertMsg.error('查询超时，请重试！');</script>");
      }
      socket_close($socket);
      return $ret;
  }
  
  

 
 
 



