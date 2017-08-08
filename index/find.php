<?php
session_start();
if ($_SESSION['login'] == true) {
    /**
     * Created by PhpStorm.
     * User: khs1994
     * Date: 2017/5/20
     * Time: 下午10:00
     */
    $type = $_GET["type"];
    if ($type == null) {
        $type = "day";
    }
    //var_dump($type);
    function show_head($var)
    {
        echo <<<EOF
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type=image/x-icon href=static/img/find.png>
    <title>{$var}</title>
    <link href="static/css/find.css" rel="stylesheet" type="text/css">
</head>
<body>
<div>
EOF;
    }

    switch ($type) {
        case "day":
            $var = "按天查找";
            show_head($var);
            echo <<<EOF
\n<form action="find_show.php?type={$type}" method="post">
<input type="date" name="day" value="2017-06-14" style="width:150px; height:30px;"/>&nbsp;<input type="submit" value="提交">
</form>
</div>
EOF;
            break;
        case "hour":
            $var = "按小时查找";
            show_head($var);
            echo <<<EOF
\n<form action="find_show.php?type={$type}" method="post">
        <input type="date" name="day" value="2017-06-14" style="width:150px; height:30px;"/>
        <input type="text" name="hour" style="width:50px; height:29px;" value="00">
        <input type="submit" value="提交">
</form>
</div>
EOF;
            break;
        case "minutes":
            $var = "按分钟查找";
            show_head($var);
            echo <<<EOF
\n<form action="find_show.php?type={$type}" method="post">
        <input type="date" name="day" value="2017-06-14" style="width:150px; height:30px;"/>
        <input type="time" name="hour_minutes" value="16:00" style="width:150px; height:30px;"/>
        <input type="submit" value="提交">
</form>
</div>
EOF;
            break;
        default:
            echo "参数传入错误，请检查URL";
            break;
    }
    echo <<<EOF
\n</body>\n</html>
EOF;
} else {
    //echo "未登录！";
    echo "<meta http-equiv=refresh content='0;url=login.php'>";
}
