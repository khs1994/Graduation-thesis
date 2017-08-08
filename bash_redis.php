<?php
/**
 * Created by PhpStorm.
 * User: khs1994
 * Date: 2017/5/21
 * Time: 下午2:43
 */
include "index/Gateway.class.php";

$m1 = microtime(true);

$root_path = "/home/pi/Graduation-thesis/index/";
$now_time = date('Y-m-d H:i:s');
switch ($argv[1]) {
    case "-h":
        echo <<<EOF
命令行程序
帮助信息
参数为时间，20170614_20\n
EOF;
        break;
    default:
        $time = $argv[1];
        //TXT文本文件
        $data_filename = $root_path . "doc/data/{$time}_data.txt";
        $line = count(file("$data_filename"));
        $if_line = file_get_contents("$root_path/admin/line.txt", "$line");
        if ($line != $if_line) {
            //行数变化，将新行数写入文件
            file_put_contents("$root_path/admin/line.txt", "$line");
            file_put_contents("$root_path/admin/line_bool.txt", "false");
            //生成的JSON文件
            $target_filename = $root_path . "json/{$time}.json";
            //转化
            $gateway = new Gateway();
            $json = $gateway->txt_to_json($data_filename);
            //写入
            file_put_contents("$target_filename", $json);
            //存入数据库
            $json_array = json_decode($json, true);
            $gateway->redis_h_set($json_array);
            //执行用时
            $m2 = microtime(true);
            $use_time = sprintf("%0.4f", ($m2 - $m1) * 1000);
            //将相关数据写入日志文件
            $content = <<<EOF
**********//处理程序//**********
{$now_time}
哪个小时=>{$time}
要读取的文件=>$data_filename
生成的JSON文件=>$target_filename
将数据存入Redis
执行用时：{$use_time}ms
文件行数{$line}\n\n
EOF;
        } else {
            $content = "**********//处理程序//**********\n文件行数{$line},没有变化，不执行此程序\n\n";
            file_put_contents("$root_path/admin/line_bool.txt", "true");
        }
        file_put_contents("{$root_path}/doc/main/{$time}_main.txt", "$content", FILE_APPEND);

}