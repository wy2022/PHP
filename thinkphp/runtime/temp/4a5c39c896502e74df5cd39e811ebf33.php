<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:65:"H:\PHP\thinkphp\public/../application/admin\view\login\index.html";i:1554704456;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>login</title>
</head>
<body>
<form action="<?php echo url('login'); ?>" method="post">
    <label for="username">username:</label><input type="text" name="username" id="username" />
    <label for="password">password:</label><input type="password" name="password" id="password" />
    <button type="submit">submit</button>
</form>
</body>
</html>