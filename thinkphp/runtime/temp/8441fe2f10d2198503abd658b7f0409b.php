<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:66:"H:\PHP\thinkphp\public/../application/admin\view\student\edit.html";i:1554771035;s:56:"H:\PHP\thinkphp\application\admin\view\teacher\base.html";i:1554790624;}*/ ?>
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

<body class="contanier">
<div class="row">
    <div class="col-md-12">
        <form action="<?php echo url('update'); ?>" method="post">
            <label>姓名:</label>
            <input type="hidden" name="id" value="<?php echo $student['id']; ?>" />
            <input type="text" name="name" value="<?php echo $student['name']; ?>" />
            <label>学号:</label>
            <input type="text" name="num" value="<?php echo $student['num']; ?>" />
            <label>性别:</label>
            <input type="radio" name="sex" value="0" id="sex0" <?php if($student['sex'] == '0'): ?>checked="checked"<?php endif; ?>/><label for="sex0">男</label>
            <input type="radio" name="sex" value="1" id="sex1" <?php if($student['sex'] == '1'): ?>checked="checked"<?php endif; ?>/><label for="sex1">女</label>
            <label>班级</label>
            <select name="klass_id">
                <?php if(is_array($student->Klass->all()) || $student->Klass->all() instanceof \think\Collection || $student->Klass->all() instanceof \think\Paginator): $i = 0; $__LIST__ = $student->Klass->all();if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$klass): $mod = ($i % 2 );++$i;?>
                <option value="<?php echo $klass['id']; ?>" <?php if($klass['id'] == $student['klass_id']): ?>selected="selected"<?php endif; ?> ><?php echo $klass['name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>

            </select>

            <label>邮箱:</label>
            <input type="email" name="email" value="<?php echo $student['email']; ?>" />
            <button type="submit">保存</button>
        </form>
    </div>
</div>
</body>

</body>
 
</body>
</html>