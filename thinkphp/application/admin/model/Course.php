<?php


namespace app\admin\model;


use think\Model;

class Course extends Model
{
    protected $table='yunzhi_course';

    protected $autoWriteTimestamp=true;

    public function klasses(){
        //多对多，klass 模型名字，klassCourse数据库表名字
        return $this->belongsToMany('Klass','KlassCourse');
    }

}