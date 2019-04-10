<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:65:"H:\PHP\thinkphp\public/../application/admin\view\course\edit.html";i:1554792489;s:56:"H:\PHP\thinkphp\application\admin\view\teacher\base.html";i:1554790624;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>标题</title>
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



<form action="<?php echo url('course/save'); ?>" method="post">

    <input type="hidden" name="id" value="<?php echo $course['id']; ?>">
   课程名称： <input type="text" name="name" value="<?php echo $course['name']; ?>">
    <input type="submit" value="提交">


</form>













</body>
 
</body>
</html>