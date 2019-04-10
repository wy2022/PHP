<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
        'id' =>'\d+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
//    #普通路由 http://localhost:88/blog/thinkphp  http://localhost:88/blog/1 http://localhost:88/blog/2014/02
//    'blog/:year/:month' =>['index/Blog/archive',['method' =>'get'],['year' =>'\d{4}','month'=>'\d{2}']],
//    'blog/:id'=>['index/Blog/get',['method' =>'get'],['id'=>'\d+']],
//    'blog/:name'=>['index/Blog/read',['method'=>'get'],['name'=>'\w+']],

#如果都在 blog下，可以用分组来 用正则来匹配
    '[blog]' => [
        ':year/:month'  =>  ['blog/archive',['method' => 'get'],['year' => '\d{4}','month' => '\d{2}']],
        ':id'           =>  ['index/Blog/get',['method' => 'get'],['id'    => '\d+']],
        ':name'         =>  ['blog/read',['method' => 'get'],['name' => '\w+']],
    ],

    'user/index'        => 'index/user/index',
    'user/add'          => 'index/user/add',
    'user/create'       =>  'index/user/create',
    'user/add_list'     =>  'index/user/addList',
    'user/update/:id'   =>  'index/user/update',
    'user/delete/:id'   =>  'index/user/delete',
    'user/:id'          =>  'index/user/read',


];
use think\Request;
Route::resource('blog','index/Bbs');