<?php


namespace app\admin\controller;


use think\Controller;
use think\Request;
use app\admin\model\Teacher as teacherModel;

class Login extends Controller
{


    public function index(){
        return $this->fetch();
    }
    public function login(Request $request){
//        var_dump(input('post.'));
        $data = $request->post();

//        echo teacherModel::checkPassword($data['password']);
        //model层负责数据业务，所以不再这里处理，用model处理
        $result = teacherModel::login($data['username'],$data['password']);
        if ($result){
            return $this->success('login success',url('teacher/index'));
        }else{
            return $this->error('username or password error',url('login/index'));
        }

    }
    public function logout(){
        if (teacherModel::logout()){
            return $this->success('logout success',url('index'));
        }else{
            return $this->error('logout error',url('index'));
        }
    }
}