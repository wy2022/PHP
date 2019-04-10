<?php


namespace app\admin\controller;


use think\Controller;
use app\admin\model\Teacher as teacherModel;
use think\Request;

class Teacher extends Base
{

    public function index(Request $request){
        $teacher = new teacherModel();
        $name = $request->get('name');
        if (!empty($name)){
            $teacher->where('name','like','%'.$name.'%');
        }
        #因为paginate带的有3个参数，最后一个有个数组，可以带名字 query url额外参数
        $list = $teacher->order('update_time desc')->paginate(5,false,[
            'query'=>[
                'name'=>$name,
            ],
        ]);
        $this->assign('user_list',$list);
        return $this->fetch('/teacher/teacher');
    }
    public function insert(Request $request){
            if ($res = $request->post()){

                $teacher = new teacherModel();
                $result =$teacher->allowField(true)->validate(true)->save($res);
                if ($result == false){
                    return $this->error($teacher->getError(),'','','1');
                }else{
                    return $this->success('添加成功','','','1');
                }


            }else{
                return $this->fetch('teacher/insert');
            }
    }
    public function delete($id=''){
        $res = teacherModel::destroy($id);
        if ($res){
            return $this->success('删除成功');
        }else{
            return $this->error('删除失败');
        }
    }
    public function edit($id=''){
        $user = teacherModel::get($id);
        $this->assign('user',$user);
        return $this->fetch('teacher/edit');
    }

    public function update(Request $request){
        $res = $request->post();
        $teacher = new teacherModel();
        #allowfield过滤掉token 不然数据库会提示找不到字段，isupdate是强制save更新
       $reslut =  $teacher->allowField(true)->validate(true)->isUpdate(true)->save($res);
//       dump($reslut);
        if ($reslut == true){
            return $this->success($teacher->name.'修改成功','teacher/index');
        }else{
            return $this->error('更新失败'.$teacher->getError());
        }
    }





}