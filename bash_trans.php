<?php
/**
 * Created by PhpStorm.
 * User: khs1994
 * Date: 2017/5/27
 * Time: 上午11:09
 */
include "index/Gateway.class.php";
$m1 = microtime(true);
$root_path = "/home/pi/Graduation-thesis/index/";
$now_time = date('Y-m-d H:i:s');
//匹配参数
switch ($argv[1]) {
    case "-h":
        echo <<<EOF
命令行程序
帮助信息
参数为时间，20170614_20\n
EOF;
        break;
    default:
        $bool = file_get_contents("$root_path/admin/line_bool.txt");
        $time = $argv[1];
        if ($bool != "true") {
            //要读取地JSON文件
            $filename = "{$root_path}json/{$time}.json";
            //var_dump($filename);
            $json_file_content = file_get_contents("$filename");
            //字符串转JSON
            $json = json_encode($json_file_content);
            $url = "https://auto.khs1994.com/service/index/post_json?latest_file_time=$time";
            $gateway = new Gateway();
            //将JSON传输到汽车云服务中心
            $res = $gateway->http_curl($url, 'post', 'json', $json);
            //var_dump($res);
            $return_json = json_encode($res);
            //汽车云服务中心返回消息
            //var_dump($res);
            //var_dump($time);
            //微信公众平台推送,相同小时数据隔10分钟推送一次
            $file_time = file_get_contents("{$root_path}admin/file_time.txt");
            if ($file_time != $time) {
                $access_token = file_get_contents('https://auto.khs1994.com/if/access_token.txt');
                $gateway = new Gateway();
                $push_type = $gateway->send_T_Msg($time, $access_token);
                //推送之后把时间写入文件
                file_put_contents("{$root_path}admin/file_time.txt", "$time");
                //10分钟之后的时间戳
                $next_time_unix = time() + 60 * 10;
                file_put_contents("{$root_path}admin/file_time_unix.txt", "$next_time_unix");
                $type = "否";
            } else {
                //与上次推送时间相同
                //获取过期时间
                $next_time_unix = file_get_contents("{$root_path}admin/file_time_unix.txt");
                if (time() > $next_time_unix) {
                    //过期了
                    $access_token = file_get_contents('https://auto.khs1994.com/if/access_token.txt');
                    $gateway = new Gateway();
                    $push_type = $gateway->send_T_Msg($time, $access_token);
                    $next_time_unix = time() + 60 * 3;
                    file_put_contents("{$root_path}admin/file_time_unix.txt", "$next_time_unix");
                    $type = "是=》但已经过期";
                } else {
                    $ex_time_unix_date = date('H:i:s', $next_time_unix);
                    $type = "是 =》{$ex_time_unix_date}后才能推送";
                    $push_type = "推送失败！与上次推送时间相同，且未过期（避免频繁推送）";
                }
            }
            $m2 = microtime(true);
            $use_time = sprintf("%0.4f", ($m2 - $m1) * 1000);
            //将相关数据写入日志文件
            $content = <<<EOF
**********//CAN数据传输程序//**********
{$now_time}
是否与上次相同：{$type}
上传的JSON文件=>{$filename}
云服务中心返回数据=>{$return_json}
推送类型：{$push_type}
执行用时：{$use_time}ms\n
EOF;
        } else {
            $content = "**********//CAN数据传输程序//**********\n数据没有变化,不执行此程序\n\n";
        }
        file_put_contents("{$root_path}/doc/main/{$time}_main.txt", "$content", FILE_APPEND);

}

