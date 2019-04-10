<?php


namespace app\index\controller;


use app\index\model\Profile;
use think\Controller;
use app\index\model\User as userModel;
use app\index\model\Book as bookModel;
use think\Image;
use think\Request;
use app\index\model\Test as testModel;

class User extends Controller
{

    public function index(){
        $list = userModel::all();
//        $list = userModel::all(['status' =>1]);
        foreach ($list as $user){
            echo $user->nickname.'</br>';
            echo $user->email.'</br>';
            echo $user->birthday.'</br>'; #用了读取器所以自动转成1970-01-01

            echo $user->status.'</br>';
            echo $user->create_time.'</br>';
            echo $user->update_time.'</br>';
            echo '---------------------------------------------'.'</br>';
        }
    }

    public function add(){
        $user               =new userModel();
//        $user->nickname     = '流年1';
//        $user->email        ='thinkphp2222@qq.com';
//        $user->birthday     ='1999-03-05';
        #allowField 因为post表单中有一个token，validate 自己写验证器，因为用来命名空间所以不需要引入
        if ($user->allowField(true)->validate(true)->save(input('post.'))){
            return '用户['.$user->nickname . ':'. $user ->id . ']新增成功';
        }else{
            return $user->getError();
        }
    }
    public function addList(){
        $user           =new  userModel();
        $list = [
            ['nickname' =>'张三','name'=>'zs'],
            ['nickname' =>'lisi','name' =>'lisi' ],
        ];
        if ($user ->saveAll($list)){

            return '批量新增成功'.$user->getLastSql().'</br>'.$user->getLastInsID();
        }else{
            return $user->getError();
        }
    }
    public function read($id=''){
//        $user = userModel::get($id);
//        $user = userModel::get(['nickname' =>'lisi2']);
        $user = userModel::get(['email' =>'lis@qq.com','nickname'=>'lisi2']);
        echo $user->id .'</br>';
        echo $user->nickname.'</br>';
        echo $user->email.'</br>';
        echo date('Y/m/d',$user->birthday).'</br>';
    }
    public function update($id=''){
        $user = userModel::get($id);
        $user->nickname     ='姚晨';
        $user->email        ='111@qq.com';
        $user->save();
        return '更新成功';
    }

    public function delete($id=''){
//        $user = userModel::get($id);
//        if ($user){
//            $user ->delete();
//            return '删除用户成功';
//        }else{
//            return '删除用户不存在';
//        }
        $result = userModel::destroy(['email'=>'lis3@qq.com']);
        if ($result){
            return '删除成功';
        }else{
            return '用户不存在';
        }
    }

    public function create(){
        return view();
    }

    public function add1(){
        $user           = new userModel();
        $user ->name    ='thinkphp';
        $user ->nickname = '流年2';
        $user ->password = '123456';
        if ($user ->save()){
            //写入关联数据
            $profile=           new Profile();
            $profile->truename = '忘语';
            $profile->birthday = '1997-12-4';
            $profile->address = '中国';
            $profile->email = '1111@qq.com';
            $user ->profile()->save($profile);
            return '用户新增成功';

        }else{
            return $user ->getError();
        }
    }
    ////
    /// has one 一对一查询
    ///
    public function read1($id='')
    {
        $user = userModel::get($id);
//        $user = userModel::get($id,'profile'); #可以使用预加载，提高查询性能
        echo $user->name.'</br>';
        echo $user->nickname .'</br>';
        echo $user->profile->truename.'</br>';
        echo $user ->profile->email .'</br>';
    }

    ///
    ///has one一对一 关联更新
    ///
    public function update1($id)
    {
        $user = userModel::get($id);
//        $user->nickname='liue';
        $user->name = 'python Django';
        if ($user->save()){
            //关联更新
            $user ->profile->email = 'guagxin@126.com';
            $user ->profile->save();
            return $user->name .'修改成功';
        }else{
            return $user->getError();
        }

    }
    //一对一删除
    //
    public function delete1($id=''){
        $user = userModel::get($id);
        if ($user->delete()){
            //删除关联数据
            $user->profile->delete();
            return $user->name.'删除成功';
        }else{
            return $user->getError();
        }
    }


    //一对多
    public function addBook(){
        $user           = userModel::get(1);
        $book           = new bookModel();
        $book ->title   ='tp52教程';
        $book ->publish_time ='2018-09-09';
        $user->books()->save($book);
        return '添加成功';
    }
    public function addBookAll(){
        $user   = userModel::get(1);
        $books = [
            ['title'=>'python教程','publish_time'=>'2019-01-05'],
            ['title'=>'thinkphp教程','publish_time'=>'2018-01-05']
        ];
        $user->books()->saveAll($books);
        return '批量添加成功';
    }

    //关联查询
    public function read2(){
        $user = userModel::get(1);
        $books = $user->books;
        dump($books);
    }
    #关联查询
    public function read3(){
//        $user = userModel::get(1);
//        $books = $user->books()->where('status',1)->select();
////        dump($books);
//        $book = $user->books()->getBytitle('thinkphp教程');
//        dump($book);
        //还可以用模型查询
        $user = userModel::has('books')->select();
        dump($user);
    }

    public function page(){
        $list = userModel::paginate(5);
        $this->assign('list',$list);
        return $this ->fetch();
    }
    public function tt1(){
        session('name','xiaowang');
        cookie('age','18',10);
    }
    public function tt2(Request $request){
        if ($request->isPost()){
            $user = $request->post();
            dump($user);
            echo $user['name'];
            echo cookie('age');
            session('name',null);
            return $this->fetch('user/sess');
        }else{
            return $this->fetch('user/sess');
        }

    }
    public function check(Request $request){

        if ($request->post()){
            dump($res = $request->param());
            if (!captcha_check($res['code'])){
                $this->error('验证码错误');
            }else{
                $user = testModel::get(['username'=>$res['username'],'password'=>$res['password']]);
                if ($user){
                    echo 'zhengque';
                }else{
                    echo '帐号密码错误';
                }
//                $this->success('验证码正确');
            }

        }else{
            return $this->fetch('user/yzm');
        }
    }
    public function up(Request $request){
        #获取上传文件
        if ($request->isPost()){
            $file = $request->file('file');
            if (empty($file)){
                $this->error('请上传文件');
            }
            #验证下图片是否上传，以及格式等
            $result = $this->validate(['file'=>$file],['file'=>'require|image'],['file.require'=>'请上传文件','file.image'=>'非法图像']);
            if (true !== $result){
                $this->error($result);
            }else{
                $image = Image::open($file);
                //图片处理
                switch ($request->param('type')){
                    case 1://图片裁剪
                        $image->crop(300,300);
                        break;
                    case 2://缩略图
                        $image->thumb(150,150,Image::THUMB_CENTER);
                        break;
                    case 3://垂直反转
                        $image->flip();
                        break;
                    case 4://水平反转
                        $image->flip(Image::FLIP_Y);
                        break;
                    case 5://xuan zhuan
                        $image->rotate();
                        break;
                    case 6://shui yin
                        $image->water('./logo.png', Image::WATER_SOUTHEAST,80);
                        break;
                    case 7:
                        $image->text('thinkphp',VENDOR_PATH.'topthink/think-captcha/assets/ttfs/1.ttf', 20, '#ffffff');
                        break;
                }
                $saveName = $request->time() .'.png';
                $image->save(ROOT_PATH . 'public/uploads' . $saveName);
                $this->success('图片处理完毕...','/public/uploads/' . $saveName,3);
            }

//            // 移动到框架应用根目录/public/uploads/ 目录下
//            $info = $file->move(ROOT_PATH . 'public' .DS . 'uploads');
//            if ($info){
//                echo $info->getRealPath().'</br>';
//                echo $info->getSaveName();
//                $this->success('文件上传成功'.$info->getRealPath());
//
//            }else{
//                $this->error('上传失败');
//            }

        }
        else{
           return $this->fetch('user/up');
        }


    }

}