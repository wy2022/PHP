<?php


namespace app\index\model;


use think\Model;

class Profile extends Model
{
    protected $type =[
        'birthday' =>'timestamp:Y-m-d',
    ];
    #belongsTo('关联模型名','关联外键','关联模型主键','别名定义','join类型')
    # 因为user已经关联了profile 如果想通过prefile查找到user需要定义 belongsto
    public function user()
    {
        return $this->belongsTo('User');
    }

}