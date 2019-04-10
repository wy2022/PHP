<?php


namespace app\index\model;


use think\Model;

class User extends Model
{
    protected $table ='think_user';
    protected $autoWriteTimestamp = true;
    protected $insert = ['status' =>1];

    #设置了这个，就不用设置读取器了

    protected $type             =[
        'birthday' =>'timestamp:Y/m/d',
    ];
//
//    #birthday读取器
//    protected function getBirthdayAttr($birthday){
//        return date('Y-m-d',$birthday);
//    }
//    #birthday修改器
//    protected function setBrithdayAttr($value){
//        return strtotime($value);
//    }

    protected function setStatusAttr($value,$data){
        return '流年'== $data['nickname'] ? 1:2;  #用三元运算符，如果nickname=流年status1，不等于就2
    }



    protected function getStatusAttr($value){
        $status=[-1 =>'删除',0 =>'禁用',1 =>'正常',2=>'待审核'];
        return $status[$value];
    }

    // 定义关联方法 1对1
    public function profile()
    {
        //用户has one  档案关联
        return $this->hasOne('Profile');
    }

    //定义关联 一对多
    public function books(){
        return $this->hasMany('Book');
    }
}