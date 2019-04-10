<?php


namespace app\admin\controller;

use app\admin\model\Course as courseModel;
use app\admin\model\Klass as klassModel;
use app\admin\model\KlassCourse as klassCourseModel;
use think\Request;

class Course extends Base
{

    public function index(Request $request)
    {
        $name = $request->get('name');
        $course = new courseModel();
        if (!empty($name)){
            //如果get过来的name为空，就加上where条件查询
            $course->where('name','like','%'.$name.'%');
        }
        $list = $course->paginate(15,false,[
            'query'=>[
                'name'=>$name,
            ],
        ]);
        $this->assign('courses',$list);
        return $this->fetch();
    }

    public function add(Request $request)
    {
        $data = $request->post();

        if (!empty($data)){

            $course = new courseModel();
            $name = $data['name'];
            if (empty($data['klass_id'])){
                $this->error('请选择班级');
            }

            $res = $course->validate(true)->save(['name'=>$name]);

            if ($res == true){
                $course_id = $course->getLastInsID();


                //先判断是否选则班级

                //放个数组，便于接受foreache后的数组
                $datas =array();
                foreach ($data['klass_id'] as $item) {
//                    var_dump($item);
                    $arr = array();
                    $arr['klass_id']    =       $item;
                    $arr['course_id']   =       $course_id;

                    //遍历一次的数组放到数组，组成二维数组，
                    array_push($datas,$arr);
                }
                //如果datas二维数组不为空，就插入数据
                if (!empty($datas)){
                    $klass_course = new klassCourseModel();
                    //验证并批量插入二维数组
                    $res = $klass_course->validate(true)->saveAll($datas);
                    //如果返回值false
                    if (!$res){
                        $this->error('课程-班级信息保存错误'.$klass_course->getError());
                    }
                    unset($klass_course);//销毁变量
                }
            unset($course);
                $this->success('add success',url('course/index'));
            }else{
                $this->error('error'.$course->getError());
            }

        }else{
            $klass = klassModel::all();

            $this->assign('klass',$klass);
            return $this->fetch();
        }
    }


    //修改
    public function edit(Request $request)
    {
            $id =  $request->get('id');
            $course = courseModel::get($id);
//            echo $course;
            $this->assign('course',$course);
            return $this->fetch();
    }

    public function save(Request $request)
    {
        $data =  $request->post();

        $course = new courseModel();
        //验证并保存
        $res = $course->validate(true)->isUpdate(true)->save($data);
        if ($res == true){
            $this->success('修改成功',url('course/index'));
        }else{
            $this->error('edit error:'.$course->getError());
        }



    }


//删除
    public function delete(Request $request)
    {

        $id =  $request->get('id');

        $res = courseModel::destroy($id);
        if ($res){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }


}