<?php
session_start();
if ($_SESSION['login'] == true) {
    /**
     * Created by PhpStorm.
     * User: khs1994
     * Date: 2017/5/20
     * Time: 下午1:51
     */
    $m1 = microtime(true);
    include "Gateway.class.php";
    $type = $_GET['type'];
    switch ($type) {
        case "day":
            $var = "按天查找";
            $num = 24 * 3600;
            $day = $_POST['day'];
            //$hour_minutes = $_POST['hour_minutes'];
            $hour_minutes = "00:00";
            //$second = $_POST['second'];
            $second = "00";
            break;
        case "hour":
            $var = "按小时查找";
            $num = 3600;

            $day = $_POST['day'];
            $hour = $_POST["hour"];
            //$hour_minutes = $_POST['hour_minutes'];
            $hour_minutes = $hour . ":00";
            //$second = $_POST['second'];
            $second = "00";
            break;
        case "minutes":
            $var = "按分钟查找";
            $num = 60;
            $day = $_POST['day'];
            $hour_minutes = $_POST['hour_minutes'];
            //$second = $_POST['second'];
            $second = "00";
            break;
    }
    $gateway = new Gateway();
    $gateway->show_html_head($var);
    $gateway->show_get_and_post();

    echo "时  间：" . $day . " " . $hour_minutes . ":" . $second . "<br>";
    //得到时间戳
    $redis_date = strtotime($day . " " . $hour_minutes . ":" . $second);
    echo "时间戳：" . $redis_date;
    echo "<hr>";

    $counts = $gateway->find_data($redis_date, $num);

    $m2 = microtime(true);
    $gateway->show_html_find($counts, $m1, $m2);
    $gateway->show_html_foot();
} else {
    //echo "未登录！";
    echo "<meta http-equiv=refresh content='0;url=login.php'>";
}