<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:66:"H:\PHP\thinkphp\public/../application/admin\view\course\index.html";i:1554798743;s:56:"H:\PHP\thinkphp\application\admin\view\teacher\base.html";i:1554790624;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>course主页</title>
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


<div class="row">
    <div class="col-md-12">
        <hr />
        <div class="row">
            <div class="col-md-8">
                <form class="form-inline">
                    <div class="form-group">
                        <label class="sr-only" for="name">姓名</label>
                        <input name="name" type="text" class="form-control" placeholder="姓名..." value=<?php echo input('get.name'); ?>>
                    </div>
                    <button type="submit" class="btn btn-default"><i class="glyphicon glyphicon-search"></i>&nbsp;查询</button>
                </form>
            </div>
            <div class="col-md-4 text-right">
                <a href="<?php echo url('course/add'); ?>" class="btn btn-primary"><i class="glyphicon glyphicon-plus"></i>&nbsp;增加科目</a>
            </div>
        </div>
        <hr />
        <table class="table table-hover table-bordered">
            <tr class="info">
                <th>序号</th>
                <th>名称</th>
                <th>时间</th>
                <th>操作</th>
            </tr>
            <?php if(is_array($courses) || $courses instanceof \think\Collection || $courses instanceof \think\Paginator): $key = 0; $__LIST__ = $courses;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$course): $mod = ($key % 2 );++$key;?>
            <tr>
                <td><?php echo $key; ?></td>
                <td><?php echo $course['name']; ?></td>
                <td><?php echo $course['update_time']; ?></td>
                <td><a class="btn btn-danger btn-sm" href="<?php echo url('course/delete'); ?>?id=<?php echo $course['id']; ?>"><i class="glyphicon glyphicon-trash"></i>&nbsp;删除</a>&nbsp;
                    <a class="btn btn-sm btn-primary" href="<?php echo url('course/edit'); ?>?id=<?php echo $course['id']; ?>">
                        <i class="glyphicon glyphicon-pencil"></i>&nbsp;编辑</a></td>
            </tr>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </table>
            <?php echo $courses->render(); ?>
    </div>
</div>


</body>
 
</body>
</html>