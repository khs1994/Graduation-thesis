<?php
/*格式化原始文件*/
$root_path = "/home/pi/Graduation-thesis/index/";
$my_file = fopen("{$root_path}doc/source.txt", "r");
$now_date = $argv[1];
$type = false;
while (!feof($my_file)) {
    $my_file_string = fgets($my_file);
    //去掉 \n、空格
    $my_file_string = trim(str_replace("\n", "", $my_file_string));
    if (strlen($my_file_string) == 19 or strlen($my_file_string) == 39) {
        file_put_contents("index/doc/data/{$now_date}_data.txt", "{$my_file_string}\n", FILE_APPEND);
    }
}
fclose($my_file);
