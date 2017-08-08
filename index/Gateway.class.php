<?php

/**
 * Created by PhpStorm.
 * User: khs1994
 * Date: 2017/5/20
 * Time: 下午2:46
 */
date_default_timezone_set('PRC');
error_reporting(E_ALL || ~E_NOTICE);
session_start();

class Gateway
{
    /*txt 转 json ,返回 json*/
    function txt_to_json($data_filename)
    {
        $my_file = fopen("$data_filename", "r") or die("Unable to open file!=>没有数据传入\n<br>");
        // 输出单行直到 end-of-file
        while (!feof($my_file)) {
            // 读取文件
            $my_file_string = fgets($my_file);
            // 去掉 \n、空格
            $my_file_string = trim(str_replace("\n", "", $my_file_string));
            //var_dump($my_file_string);
            if (preg_match("/^2/", $my_file_string)) {
                $receive_date = strtotime($my_file_string);
            }
            if (preg_match("/^0x/", $my_file_string)) {
                //echo $i;
                $can_data = $my_file_string;
                //var_dump($can_data);
            }
            $data_array [$receive_date] = $can_data;
            //$json_string = json_encode($data_Array);
        }
        fclose($my_file);
        $json = json_encode($data_array);
        //返回json
        return $json;
    }

    /*从 redis 查找数据*/
    function find_data($redis_date, $num)
    {
        $redis = new \Redis();
        $redis->connect("127.0.0.1", 6379);
        //取得所有值
        $redis_hash_all = $redis->hgetall('gateway');
        //遍历
        echo <<<EOF
        <table>
<tr>
<td>时间</td>
<td style="padding-left: 300px">原始数据</td>
<td style="position: absolute;left: 600px">胎压</td>
<td style="position: absolute;left: 700px">类型</td>
</tr>
</table>
EOF;
        foreach ($redis_hash_all as $k => $v) {
            //判断
            if ($k > $redis_date && $k < $redis_date + $num) {
                $val = $v;
                $get_redis_date = $k;
                $time = date('Y-m-d H:i:s', $get_redis_date);
                $first = $val[2] . $val[3];
                //var_dump($first);
                $mean_num_5 = "0x" . $val[22] . $val[23] . $val[17] . $val[18];
                //var_dump($mean_num_5);
                $num_16_to_10 = hexdec($mean_num_5) * 0.0001;
                $press = $num_16_to_10 . "bar";
                //var_dump($num_16_to_10);
                switch ($first) {
                    case "01":
                        $status = "<td style='position: absolute;left: 700px;color: red'>轮胎低压</td>";
                        break;
                    case "ff":
                        $status = "<td style='position: absolute;left: 700px;color: red'>轮胎高压</td>";
                        break;
                    default:
                        $status = "<td style='position: absolute;left: 700px'>正常</td>";
                        break;
                }


                echo <<<EOF
<table>
<tr>
<td>{$time}</td>
<td style="padding-left: 90px">{$val}</td>
<td style="position: absolute;left: 600px">{$press}</td>
{$status}
</tr>
</table>
EOF;
            }
        }
        echo "<hr><br>";
        $count = count($redis_hash_all);
        return $count;
    }


    /*查找页面的html，数据个数、用时、现在时间*/
    function show_html_find($counts, $m1, $m2)
    {
        $use_time = sprintf("%0.4f", ($m2 - $m1) * 1000);
        $nowTime = date("Y-m-d H:i:s");
        echo <<<EOF
<p>共有<span>{$counts}</span>条数据，
执行用时<span>{$use_time}ms</span>，
查询时间：<span>{$nowTime}</span></p>
EOF;
    }

    /*打印 get post 方法信息*/
    function show_get_and_post()
    {
        echo "\nPOST方法得到的参数：";
        var_dump($_POST);
        echo "<br>" . "\nGET方法得到的参数：";
        var_dump($_GET);
        echo "<br>";
    }

    /*打印 html 头*/
    function show_html_head($var)
    {
        echo <<<EOF
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type=image/x-icon href=static/img/4g.png>
    <title>{$var}</title>
    <link href="static/css/find.css" rel="stylesheet" type="text/css">
</head>
<body>
EOF;
    }

    /*打印 html 尾*/
    function show_html_foot()
    {
        echo "\n</body>\n</html>";
    }


    /*存入数据库*/
    function redis_h_set($json_array)
    {
        $redis = new \Redis();
        $redis->connect("127.0.0.1", 6379);

        foreach ($json_array as $k => $v) {
            //echo "时间戳：" . $k . "<br>";
            //echo "值：" . $v . "<br>";
            //存入 redis NoSQL
            $redis->hSet("gateway", "$k", "$v");
        }
    }

    /*展示程序执行时间*/
    function get_use_time($m1, $m2)
    {
        $use_time = sprintf("%0.4f", ($m2 - $m1) * 1000);
        echo <<<EOF
<p>执行用时<span>{$use_time}ms</span></p>\n
EOF;
    }

    /*展示数据个数，程序执行时间*/
    function show_html($counts, $m1, $m2)
    {
        $use_time = sprintf("%0.4f", ($m2 - $m1) * 1000);
        echo <<<EOF
<p>共有<span>{$counts}</span>条数据，
执行用时<span>{$use_time}ms</span></p>\n
EOF;
    }

    /*curl方法提交数据*/
    function http_curl($url, $type = 'get', $res = 'json', $arr)
    {
        //1.初始化curl
        $ch = curl_init();
        //2.设置curl的参数
        curl_setopt($ch, CURLOPT_URL, $url);//设置curl
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //返回
        if ($type == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        }
        //3.采集
        $output = curl_exec($ch);
        //4.关闭
        if ($res == 'json') {
            if (curl_errno($ch)) {
                return curl_errno($ch);
            } else {
                return json_decode($output, true);
            }
        }
        curl_close($ch);
        return 0;
    }

    /*微信推送*/
    function send_T_Msg($time, $access_token)
    {
        $fileName = "index/json/" . "{$time}.json";
        $my_file = fopen("$fileName", 'r');
        $my_file_string = fgets($my_file);
        fclose($my_file);
        //var_dump($my_file_string);
        $json_array = json_decode("$my_file_string", true);
        //var_dump($json_array);
        /*遍历JSON数组，取出异常数据，并组成数组*/
        $i = 0;
        foreach ($json_array as $k => $v) {
            if (preg_match("/^0x01/", $v)) {
                $error_array[$k] = $v;
            }
            if (preg_match("/^0xff/", $v)) {
                $error_array[$k] = $v;
            }
        }

        if ($error_array != null) {
            foreach ($error_array as $k => $v) {
                $i += 1;
                switch ($i) {
                    case 1:
                        $data1 = $v;
                        break;
                    case 2:
                        $data2 = $v;
                        break;
                    case 3:
                        $data3 = $v;
                        break;
                }
            }
            //判断状态
            if (preg_match("/^0x01/", $data1)) {
                $status1 = "状态：低压报警";
            } else {
                $status1 = "状态：高压报警";
            }
            $mean_num_5 = "0x" . $data1[22] . $data1[23] . $data1[17] . $data1[18];
            $num_16_to_10 = hexdec($mean_num_5) * 0.0001;
            $taiya = $num_16_to_10 . "bar";
            //
            if ($data2 != null) {
                if (preg_match("/^0x01/", $data2)) {
                    $status2 = "状态：低压报警";
                } else {
                    $status2 = "状态：高压报警";
                }
                $mean_num_5 = "0x" . $data2[22] . $data2[23] . $data2[17] . $data2[18];
                $num_16_to_10 = hexdec($mean_num_5) * 0.0001;
                $taiya2 = $num_16_to_10 . "bar";
            } else {
                $status2 = "没有告警信息";
            }
            //
            if ($data3 != null) {
                if (preg_match("/^0x01/", $data3)) {
                    $status3 = "状态：低压报警";
                } else {
                    $status3 = "状态：高压报警";
                }
                $mean_num_5 = "0x" . $data3[22] . $data3[23] . $data3[17] . $data3[18];
                $num_16_to_10 = hexdec($mean_num_5) * 0.0001;
                $taiya3 = $num_16_to_10 . "bar";
            } else {
                $status3 = "没有告警信息";
            }
            $url = <<<EOF
https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token
EOF;
            //var_dump($access_token);
            /*
            {
                "touser":"OPENID",
               "template_id":"ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY",
               "url":"http://weixin.qq.com/download",
               "data":{
                           "first": {
                                "value":"恭喜你购买成功！",
                                "color":"#173177"
                          }
                }
             }
            */
            $array = ["touser" => "ouZYluPGh7iMGYrrz_8kTwKBxRi0",
                "template_id" => "R2eEKHwdKa1gT92C_CP780vdy2L7W1ywzGxL2qHJ_14",
                "url" => 'https://auto.khs1994.com/json/',
                "data" => ["data1" => array('value' => '    ' . $data1 . ' ' . $status1 . $taiya, 'color' => "#173177"),
                    "data2" => array('value' => '    ' . $data2 . ' ' . $status2 . $taiya2, 'color' => "#173177"),
                    "data3" => array('value' => '    ' . $data3 . ' ' . $status3 . $taiya3, 'color' => "#173177"),
                ],];
            $postJson = json_encode($array);
            $res = $this->http_curl($url, 'post', 'json', $postJson);
            //推送后返回地结果
            //var_dump($res);
            //将数组转换为json
            $return_array_to_json = json_encode($res);
            //echo "<h1><a href='/log.txt' target='_blank'>日志文件</a></h1>";
            $now_date = date("Y-m-d H:i:s");
            //将相关数据写入文件
            file_put_contents('index/doc/push_log.txt', "{$now_date}\n{$access_token}\n{$return_array_to_json}\n\n", FILE_APPEND);
            return "推送成功";
        } else {
            return "数据正常,不推送";
        }
    }
}
