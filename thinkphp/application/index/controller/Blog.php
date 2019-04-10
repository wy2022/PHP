<?php


namespace app\index\controller;


use think\Controller;
use think\Db;
use think\Request;

class Blog extends Controller
{
    public function get($id)
    {
        return 'id:'.$id;
    }
    public function read($name){
        return 'name:'.$name;
    }
    public function archive($year,$month){
        return '年:'.$year.'月:'.$month;
    }
    public function test(){
        $u = url('blog/get','id=123');
        echo $u;
    }
    public function hello(Request $request){
        #接受所有get参数
        dump(input('get.'));
        #接收get下的tel
        dump(input('get.tel'));
        #接收所有post参数
        dump(input('post.'));
        #接收post下的tel
        dump(input('post.tel'));

        echo 'cookie参数：name';
        dump(input('cookie.name'));
        echo '上传文件信息：image';
        dump(input('file.image'));

        echo $request ->method();
        echo $request ->ip();
        echo $request ->action();
        echo $request ->method();
        echo $request ->controller();
        echo var_export($request ->isAjax(),true);
        if ($request->isAjax() == true){
            echo 'true';
        }
        else{
            echo 'false22';
        }
        echo '=============</br>';
        dump($request->except(['tel']));
    }
    public function test1(Request $request){
        echo $request ->domain();
        echo "</br>";
        echo $request->baseFile();
        echo "</br>";
        echo $request ->url(true);
        echo "----";
        echo $request->root(true);
        echo '22222';
        echo $request->ext(true);

        $data = [
            'name' => 'zhsangsa',
            'age'  => '18',
        ];
        return json($data);
    }

    public function index($name = ''){
       if ($name == 'think'){
//           $this->success('登录成功','hello1','','5');
           $this->redirect('http://www.baidu.com',301);
       }else{
           $this->error('认证失败,','guest');
       }
    }

    public function hello1(){
        return 'hello thinkphp';
    }
    public function guest(){
//        Db::table('think_data')
//            ->insert(['data' =>'Django']);
//        Db::table('think_data')
//            ->where('id',3)
//            ->update(['data'=>'python']);
//       $res =  Db::table('think_data')
//           ->field('data')
//        //            ->where('id',2)
//            ->select();
//        return json($res);
//        Db::table('think_data')
//            ->delete(3);

//        Db::transaction(function (){
//           Db::table('think_data')
//               ->where('id',1)
//               ->delete();
//           Db::table('think_data')
//               ->insert(['id'>=28,'data'=>'sss']);
//        });

            $res = Db::table('think_data')
                ->where('id','<>','6')
////                    ->whereBetween('id',[5,8])
//                    ->whereLike('data',['think'])
                ->find();
            dump($res);


    }


}