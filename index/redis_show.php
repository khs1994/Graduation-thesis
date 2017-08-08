<?php
include "Gateway.class.php";

$m1 = microtime(true);

if ($_SESSION['login'] == true) {
    /*展示Redis所有数据*/
    /*URL 传参*/
    // /redis_show.php?hash_name=gateway_date
    // /redis_show.php?hash_name=gateway            时间戳
    $redis = new \Redis();
    $redis->connect("127.0.0.1", 6379);

    //得到 hash_name
    $gateway = new Gateway();

    $var = "查看数据库";


    $gateway->show_html_head($var);
    echo "<h1>Redis HASH 所有键值对</h1>\n";
    $redis_hash_all = $redis->hgetall("gateway");
    //得到包含全部结果数组
    var_dump($redis_hash_all);
    echo "<hr>";
    echo "时间戳转时间";
    echo "<hr>";
    $num = 1;
    foreach ($redis_hash_all as $k => $v) {
        echo "第($num)条数据=>";
        $num += 1;
        var_dump($k);
        echo "=>";
        echo date('Y-m-d H:i:s', $k);
        echo "<br>";
    }

    $m2 = microtime(true);
    $gateway->show_html(count($redis_hash_all), $m1, $m2);
    echo "</body>\n</html>";
} else {
    //echo "未登录！";
    echo "<meta http-equiv=refresh content='0;url=login.php'>";
}