<?php
session_start();
if ($_SESSION['login'] == true) {
    /*清空数据库*/
    $redis = new \Redis();
    $redis->connect("127.0.0.1", 6379);
    $redis->delete('gateway');
    echo "清空数据库成功，1秒后返回首页";
    echo "<meta http-equiv='refresh' content='1;url=/admin'/>";
} else {
    //echo "未登录！";
    echo "<meta http-equiv=refresh content='0;url=/login.php'>";
}