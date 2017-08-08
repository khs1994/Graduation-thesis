<?php
session_start();
error_reporting(E_ALL || ~E_NOTICE);
$status = $_SESSION['login'];
if ($_SESSION['login'] == true) {
    $status = "登录成功";
    echo <<<EOF
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type=image/x-icon href=static/img/4g.png>
    <title>车载屏幕-网关信息查看</title>
    <link media="screen" rel="stylesheet" href="static/css/demo.css"/>
</head>
<body>
<div>
<h1 style="margin-left: 70%">
<img src="static/img/logout.png" width="30px"/>&nbsp;
<a style="color: crimson" href="login.php?type=logout">退出登录</a>
</h1>      
</div>
<div class="div1" style="margin-right: 40%">
    <h1><img src="static/img/f.png"/ width=35px>功能</h1>
    <ul>
        <br>
        <li>查看接收数据TXT（实时）</li>
        <li>查看TXT文件（每小时）</li>
        <br>
        <li>查看原始数据TXT转JSON（每小时、JSON便于传输）</li>
        <br>
        <li>K-Y 存入NoSQL数据库Redis（用户按时间段查看数据）</li>
        <br>
        <li>查看车载网关运行日志（检查运行状态）</li>
        <br>
        <li>JSON传输数据             TO 汽车服务中心（对车载数据进行分析，提供车辆状态分析、预警）</li>
        <li>基于微信公众平台的数据推送  TO 车主(只推送异常数据，避免频繁推送)</li>
        <br>
        <li>查看4G拨号日志</li>
        <li>查看4G拨号获得的IP</li>
    </ul>
</div>
<div style="position: absolute;top: 150px;left: 70%;">
<img src="static/img/auto_khs1994_com.png"/>
<p>&nbsp;&nbsp;&nbsp;微信扫码访问【汽车云服务中心】网站</p>
</div>
<div class="div1" style="margin-right: 60%">
<h1><img src="static/img/txt.png"/ width=35px>数据查看</h1>
    <ul>
        <br>
        <li><img src="static/img/url.png"/ width=25px><a href="/doc/lte.txt" target="_blank">查看4G拨号日志</a></li>
        <br>
        <li><img src="static/img/url.png"/ width=25px><a href="/doc/ip.txt" target="_blank">查看4G拨号获得的IP</a></li>
        <br>
        <li><img src="static/img/url.png"/ width=25px><a href="doc/source.txt" target="_blank">接收程序原始文件查看（实时）</a></li>
        <br>
        <li><img src="static/img/url.png"/ width=25px><a href="doc/data" target="_blank">TXT原始文件查看</a></li>
        <br>
        <li><img src="static/img/url.png"/ width=25px><a href="/redis_show.php" target="_blank">查看 Redis 数据</a></li>
        <br>
        <li><img src="static/img/LOG.png"/ width=25px><a href="/doc/main" target="_blank">主程序运行日志(Python_shell.py)</a></li>
    </ul>
</div>
<style type="text/css">
#divright{
position: absolute;
left: 45%;top: 600px;
padding-right: 425px;
margin-top: 2px;
padding-bottom: 130px;
}
</style>
<div class="div1" id="divright">
    <h1><img src="static/img/find.png"/ width=35px>CAN总线数据查找</h1>
    <ul>
        <br>
        <li><img src="static/img/url.png"/ width=25px><a href="find.php?type=day" target="_blank">按天查看数据</a></li>
        <br>
        <li><img src="static/img/url.png"/ width=25px><a href="find.php?type=hour" target="_blank">按小时查看数据</a></li>
        <br>
        <li><img src="static/img/url.png"/ width=25px><a href="find.php?type=minutes" target="_blank">按分钟查看数据</a></li>
        <br>
    </ul>
</div>
<div class="div1">
    <h1><img src="static/img/4g.png"/ width=35px>传输方式</h1>
    <br>
    <h2>TO 汽车服务中心 By JSON</h2>
    <ul>
        <br>
        <li><img src="static/img/url.png"/ width=25px><a href="/json" target="_blank"> JSON(JavaScript Object Notation, JS 对象标记)</a></li>
        <br>
    </ul> 
    <h2>TO 车主 By 微信</h2>
    <ul>
        <br>
        <li><img src="static/img/url.png"/ width=25px><a href="/doc/push_log.txt" target="_blank">查看推送日志</a></li>
        <br>   
    </ul>
</div>
</body>
</html>
EOF;
} else {
    //echo "未登录！";
    //echo "<a href='login.php'>点击登录</a>";
    echo "<meta http-equiv=refresh content='0;url=login.php'>";
}
