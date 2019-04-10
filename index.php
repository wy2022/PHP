<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2019/4/2
 * Time: 16:43
 */

$a = 10;
$b = 5;
function myTest(){
    global $a,$b;
    $c = $a + $b;
    echo $c;
    return $c;
}


$z = myTest();
echo $z;

$aa = 123.1;
var_dump($aa);
$aa = 'sd';

var_dump($aa);
$aa = "sdfsd";
var_dump($aa);
$aa = 047;
var_dump($aa);

$c = False;
var_dump($c);

$arr = array("audi","bmw","geely");
var_dump($arr);
$str1 = "张三";
$str2 = "您好";
echo $str1.'"先生" '.$str2;

echo strlen($str1.'"先生" '.$str2);
echo strlen($str1);
echo "=======================";
echo strpos("hello world","world");

$a = 10;
$b = 3;
echo $a / $b;
echo $a +=$b;
$z =10;
echo "</br>";
echo ++$z;

$x = array("a" =>"red","b" =>"green");
$y = array("a" => "blue","d" => "yellow");

var_dump($x == $y);
var_dump($x);
var_dump($x != $y);
var_dump($x <> $y);

$test = 'ss';

$username = $test ?: 'moren';
echo $username;
echo $a == $b;
echo "==============";
var_dump($x + $y);
$t = date("h");
if ($t < "20"){
    echo "< 20";
    echo $t;
}
print_r($t);

$s = 'ss';

if ($s != null){
    echo "s not null";
}else{
    echo 's null';
}


$color = "blue";
switch ($color){
    case "yello":
        echo "颜色 yello";
        break;
    case "blue":
        echo "yanse baba";
        break;
    default:
        echo "meiyou meiyou ";
}

$i = 1;
while ($i < 100){
    echo $i;
    $i++;
    if ($i == 10){
        break;
    }
}
echo "</br>";
for ($i=0;$i<10;$i++){
    echo $i;
}
foreach ($x as $value){
    echo $value;
}

$arr = array('aa','bb','cc','dd');
foreach ($arr as $value){
    echo $value;
}

function echoName($name){
    echo $name.'张';
}

echoName('三');


function sum($x ,$y){
    return $x + $y;
}

$z = sum(1,2);
echo $z;

$arrs = array("aa","b","c","d","e");
echo $arrs[0];
echo count($arrs);

for ($x=0;$x<count($arrs);$x++){
    echo $arrs[$x];
}

$age = array("bill" => '36',"wang" =>"88","lisi" =>"99");
echo "</br>";
foreach ($age as $key =>$value){
    echo "key:".$key."value:".$value;
}
echo "</br>";
sort($age);
foreach ($age as $key =>$value){
    echo "key:".$key."value:".$value;
}


echo $age[0];
echo "=================================================================";
echo "</br>";
$cars = array(
    array("volvo",22,18),
    array("bmw",15,22),
    array("audi",55,55),
);

$cars_length = count($cars);

echo $cars[0][1];
for ($i = 0; $i<$cars_length;$i++){
//    echo count($cars[$i]);
    for ($y = 0; $y < (count($cars[$i]));$y++){
        echo $cars[$i][$y];
        echo "</br>";
    }
}
date_default_timezone_set("Asia/Shanghai");
echo "今天是:".date("Y/m/d");
echo "@2010 - ".date("Y").";";

echo date("Y/m/d H:i:s");