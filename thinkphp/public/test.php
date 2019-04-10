<?php


class Test{
    public function sayHello(){
        echo 'hello';
    }
    public function sayOther($words){
        echo $words;
    }

    public function functionNotExits(){
        echo '方法不存在';
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        echo '调用的方法是'.$name;
        echo '</br>参数是:';
        var_dump($arguments);
        $this->functionNotExits($arguments);
    }



}

$test = new Test();

$test->sayHello();
$test->sayOther('ad');
$test->ssd('jjj sdf','ssd');