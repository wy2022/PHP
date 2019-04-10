<?php


namespace app\admin\model;


use think\Model;

class Student extends Model
{

    protected $table='yunzhi_student';

    protected $autoWriteTimestamp=true;

//   <td>{$student.Klass.name}</td> 可以访问到name
// 前提是表结构都是非常规范的，klass_id
    public function Klass(){
        return $this->belongsTo('Klass');
    }

}