<?php
session_start();
error_reporting(E_ALL || ~E_NOTICE);
//是否登录
if ($_GET["type"] == "logout") {
    $_SESSION["login"] = false;
//echo "<a href='/'>返回首页</a>";
    echo "<meta http-equiv=refresh content='0;url=login.php'>";
} elseif ($_SESSION["login"] == false) {
    //否，取值
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password = md5("$password");

    $from_db_username = "admin";
    $from_db_password = md5("admin");
    if ($username == $from_db_username and $password == $from_db_password) {
        //echo "登录成功";
        $_SESSION["login"] = true;
        $status = "登录成功！";
        echo "<meta http-equiv=refresh content='0.9;url=/'>";
    } elseif ($_POST != null) {
        $status = "请输入正确的用户名，密码！";
    }
} else {
    echo "<meta http-equiv=refresh content='0;url=/'>";
}
//是则输出
//var_dump($_SESSION)
echo <<<EOF
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type=image/x-icon href=static/img/4g.png>
    <link href="static/css/style.css" rel="stylesheet" type="text/css">
    <title>登录网关服务平台</title>
</head>
<body>
<div class="htmleaf-container">
    <div id="wrapper" class="login-page">
        <div id="login_form" class="form">
            <form class="login-form" action="" method="post">
                <label style="color: red" id="status">{$status}</label>
                <input type="text" placeholder="用户名" name="username" id="user_name"/>
                <input type="password" placeholder="密码" name="password" id="password"/>
                <button id="login">登　录</button>
            </form>
        </div>
        <p style="text-align: center">车辆特殊的安全性考虑，不提供注册功能</p>
        <p style="text-align: center">忘记密码请联系客服</p>
    </div>
</div>
<div style="text-align: center;margin-top: 170px">
    &nbsp;&nbsp;&nbsp;&nbsp;<a href="//auto.khs1994.com/about" target="_blank">关于设计</a>&nbsp;&nbsp;&nbsp;&nbsp;|
    &nbsp;&nbsp;&nbsp;&nbsp;<a href="//auto.khs1994.com" target="_blank">客服中心</a>&nbsp;&nbsp;&nbsp;&nbsp;|
    &nbsp;&nbsp;&nbsp;&nbsp;<a href="mailto:khs1994@khs1994.com" target="_blank">联系邮箱</a>&nbsp;&nbsp;&nbsp;&nbsp;|
    &nbsp;&nbsp;&nbsp;&nbsp;<a href="//auto.khs1994.com" target="_blank">汽车服务中心</a>&nbsp;&nbsp;&nbsp;&nbsp;|
    &nbsp;&nbsp;&nbsp;&nbsp;<a>Copyright &copy;&nbsp;&nbsp;2017 GateWay. All Rights Reserved. </a></div>
</body>
</html>
EOF;
