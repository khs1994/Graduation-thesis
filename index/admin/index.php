<?php
/**
 * Created by PhpStorm.
 * User: khs1994
 * Date: 2017/5/22
 * Time: 上午9:49
 */

echo <<<EOF
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>调试模块，不对用户开放</title>
</head>
<body>
<div class="div1"><h1>调试功能(Admin)</h1>
    <ul>
        <li><a href="redis_delete.php">清除Redis数据</a></li>
        <br>
        <li><a href="if_delete.php">清除条件数据</a></li>
    </ul>
</div>
</body>
</html>
EOF;

