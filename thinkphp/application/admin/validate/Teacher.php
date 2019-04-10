<?php


namespace app\admin\validate;


use think\Validate;

class Teacher extends Validate
{
    #unique teacher 唯一，不能重复 teacher应该是要指定表，name字段
    protected $rule =[
        'name'      =>      'require|length:2,25|token',
        'username'      =>   'require|unique:teacher|length:2,25',
        'email'     =>      'email',
    ];

}