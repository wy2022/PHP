<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:69:"H:\PHP\thinkphp\public/../application/admin\view\teacher\teacher.html";i:1554770220;s:56:"H:\PHP\thinkphp\application\admin\view\teacher\base.html";i:1554790624;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>老师index</title>
    <!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!--    <link rel="stylesheet" type="text/css" href="/static/bootstrap/css/bootstrap.css" />-->
</head>
<body>

<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Brand</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="active"><a href="<?php echo url('teacher/index'); ?>">主页 <span class="sr-only">(current)</span></a></li>
                <li><a href="<?php echo url('klass/index'); ?>">班级</a></li>
                <li><a href="<?php echo url('student/index'); ?>">班级管理</a></li>
                <li><a href="<?php echo url('course/index'); ?>">科目管理</a></li>

            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#">个人中心</a></li>
                <li class="dropdown">
                    <a href="<?php echo url('login/logout'); ?>" class="dropdown-toggle"  >退出 </a>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<body class="container">


<hr />
<div class="row">
    <div class="col-md-8">
        <form class="form-inline">
            <div class="form-group">
                <label class="sr-only" for="name">姓名</label>
                <input name="name" type="text" class="form-control" placeholder="姓名..." value="<?php echo input('get.name'); ?>">
            </div>
            <button type="submit" class="btn btn-default"><i class="glyphicon glyphicon-search"></i>&nbsp;查询</button>
        </form>
    </div>


    <div class="col-md-4 text-right">
<!--        <a href="<?php echo url('add'); ?>" class="btn btn-primary"><i class="glyphicon glyphicon-plus"></i>&nbsp;增加</a>-->
        <a href="<?php echo url('teacher/insert'); ?>">
            <button type="button" class="btn btn-primary"> 新增老师</button>
        </a>
    </div>
</div>
<hr />









<table class="table table-hover table-bordered">


    <tr class="info">
        <td>id</td>
        <td>姓名</td>
        <td>帐号</td>
        <td>密码</td>
        <td>性别</td>
        <td>昵称</td>
        <td>email</td>
        <td>时间</td>
        <td>操作</td>
    </tr>
    <?php if(is_array($user_list) || $user_list instanceof \think\Collection || $user_list instanceof \think\Paginator): $i = 0; $__LIST__ = $user_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$user): $mod = ($i % 2 );++$i;?>
    <tr>
        <th><?php echo $user['id']; ?></th>
        <th><?php echo $user['name']; ?></th>
        <th><?php echo $user['username']; ?></th>
        <th><?php echo $user['password']; ?></th>
        <th><?php echo $user['sex']; ?></th>
        <th><?php echo $user['username']; ?></th>
        <th><?php echo $user['email']; ?></th>
        <th><?php echo $user['update_time']; ?></th>
        <th>
            <a href="<?php echo url('teacher/delete'); ?>?id=<?php echo $user['id']; ?>">
                <button type="button" class="btn btn-primary " >删除</button>
            </a>

            </div>
            <a href="<?php echo url('teacher/edit'); ?>?id=<?php echo $user['id']; ?>">
                <button type="button" class="btn btn-primary " >编辑</button>
            </a>
            </div>


        </th>


    </tr>

        <?php endforeach; endif; else: echo "" ;endif; ?>


</table>
当页条数：<?php echo $user_list->count(); ?>
<?php echo $user_list->render(); ?>


</body>
 
</body>
</html>