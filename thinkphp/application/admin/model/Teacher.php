<?php


namespace app\admin\model;


use app\admin\model\Teacher as teacherModel;
use think\Model;

class Teacher extends Model
{

    protected $table='yunzhi_teacher';
    protected $autoWriteTimestamp=true;


    protected function getSexAttr($value){
        $sex = ['0'=>'男','1'=>'女'];
        return $sex[$value];
    }



    static public function login($username='',$password=''){
        $map = array('username' => $username,'password'=>$password);
        $teacher = teacherModel::get($map);
//        var_dump($teacher);
        if (!is_null($teacher)){
            //如果不为空，设置session
            session('teacher_id',$teacher->id);
            return true;
        }else{
            return false;
        }

    }
    //注销，销毁sesion中数据

    static public function logout(){
        session('teacher_id',null);
        return true;
    }

    static public function isLogin(){
        if (session('teacher_id')){
            return true;
        }else{
            return false;
        }
    }

    /***

     * 密码md5
     *密码加盐
     ***/
    static public function checkPassword($password=''){
        return md5($password.'123');
    }


    //一对多，老师对应班级
    public function Klass()
    {
        return $this->hasMany('Klass');


    }

}