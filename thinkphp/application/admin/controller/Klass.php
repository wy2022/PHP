<?php


namespace app\admin\controller;

use app\admin\model\Klass as klassModel;
use think\Request;

class Klass extends Base
{

    public function index(Request $request){
        $klasses = new klassModel();
        $name = $request->get('name');
        if (!empty($name)){
            $klasses->where('name','like','%'.$name.'%');
        }
        #因为paginate带的有3个参数，最后一个有个数组，可以带名字 query url额外参数
        #为了翻页时，保留搜索栏信息
        $list = $klasses->paginate(3,false,[
            'query'=>[
                'name'=>$name,
            ],
        ]);

        $this->assign('klasses',$list);
        return $this->fetch();
    }
    public function add(){
        $teachers = \app\admin\model\Teacher::all();
        $this->assign('teachers',$teachers);
        return $this->fetch();
    }
#保存
    public function save(Request $request)
    {

        $data = $request->post();
        $klass = new klassModel;
//        $klass->name=$data['name'];
//        $klass->teacher_id = $data['teacher_id'];

        #验证并保存

        if (!$klass->validate(true)->save($data)){
            return $this->error('数据添加错误:'.$klass->getError());
        }

        return $this->success('操作成功',url('index'));

    }

    public function edit(Request $request)
    {

        $id = $request->get('id');
//        echo $id;
        //获取所有teacher
        $teacher =  \app\admin\model\Teacher::all();
        $this->assign('teacher',$teacher);

        //获取操作的班级信息
        $klass = klassModel::get($id);
        if (false === $klass){
            return $this->error('系统未找到id为:'.$id.'的记录');
        }

        $this->assign('klass',$klass);
        return $this->fetch();

    }
//gengxin
    public function update(Request $request)
    {
        $data = $request->post();

        //获取班级信息
        $klass = klassModel::get($data['id']);
        if (is_null($klass)){
            return $this->error('系统未找到id为:'.$klass['id'].'的记录');
        }

//        dump($data);
//        die();

        //更新数据 加验证

        if (!$klass->validate(true)->save($data)){
            $this->error('更新错误',$klass->getError());
        }else{
            $this->success('成功',url('klass/index'));
        }
    }



//删除
    public function delete(Request $request)
    {
        $id =   $request->get('id');

        if ($res = klassModel::destroy($id)){
            return  $this->redirect(url('klass/index'));
        }else{
            $this->error('删除失败:');
        }
    }


}