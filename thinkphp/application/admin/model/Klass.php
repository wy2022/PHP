<?php


namespace app\admin\model;


use think\Model;

class Klass extends Model
{
    protected $table='yunzhi_Klass';

    protected $autoWriteTimestamp=true;

    #因为页面班级只有返回teacher id 所以通过这个方法可以在页面直接次方法返回teacher名字
    #用关联查询
    public function Teacher(){
        return $this->belongsTo('Teacher');
    }
//    public function getTeacher(){
//
//            $teacherid = $this->getData('teacher_id');
//            $teacher = Teacher::get($teacherid);
//            return $teacher;
//    }

#定义一对多，班级对应学生
    public function Student()
    {
        return $this->hasMany('Student');
    }


}