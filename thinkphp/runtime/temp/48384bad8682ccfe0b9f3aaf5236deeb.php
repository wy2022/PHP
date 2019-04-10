<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:66:"H:\PHP\thinkphp\public/../application/admin\view\teacher\edit.html";i:1554372511;s:56:"H:\PHP\thinkphp\application\admin\view\teacher\base.html";i:1554724960;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>修改信息</title>
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



<body class="contanier">
<div class="row">
    <div class="col-md-12">
        <form action="<?php echo url('teacher/update'); ?>" method="post">
            <?php echo token(); ?>
            <label>姓名:</label>
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>" />
            <input type="text" name="name" value="<?php echo $user['name']; ?>" />
            <label>用户名:</label>
            <input type="text" name="username" value="<?php echo $user['username']; ?>" />
            <label>性别:</label>
            <select name="sex">
                <option value="0">女</option>
                <option value="1" selectet="selected"> 男</option>
            </select>
            <label>邮箱:</label>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" />
            <button type="submit">保存</button>
        </form>
    </div>
</div>

</body>


</body>
 
</body>
</html>