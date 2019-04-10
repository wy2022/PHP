<?php


namespace app\index\model;


use think\Model;

class Bbs extends Model
{
    protected $autoWriteTimestamp=true;
    protected $insert  =[
        'status' =>1,
    ];
    protected $field =[
        'id'        =>'int',
        'create_time'    =>'int',
        'update_time'   =>'int',
        'name','title','content',
    ];

}