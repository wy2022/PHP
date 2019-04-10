<?php


namespace app\index\validate;


use think\Validate;

class User extends Validate
{
#昵称，邮箱，出错是提示的名字，默认是nickname
    protected $rule =[
        'nickname|昵称' => 'require|min:2|token',
        'email|邮箱'    => 'require|email',
        'brithday|生日' =>'dateFormat:Y-m-d',
    ];

}