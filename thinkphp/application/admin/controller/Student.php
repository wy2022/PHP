<?php


namespace app\admin\controller;


use think\Request;
use app\admin\model\Student as studentModel;

class Student extends Base
{
    public function index(Request $request)
    {
        $student = new studentModel();
        $list = $student->paginate();
        $this->assign('students',$list);
        return $this->fetch();


    }

    public function edit(Request $request)
    {
        $id = $request->get('id');
//        echo $id;
        $student = studentModel::get($id);
        if (is_null($student)){
            $this->error('未找到id:'.$id);
        }else{
            $this->assign('student',$student);
            return $this->fetch();

        }

    }

    public function save()
    {

    }

    public function delete()
    {

    }
}