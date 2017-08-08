<?php
file_put_contents("file_time.txt", "1");
echo "清空条件数据成功，1秒后返回首页";
echo "<meta http-equiv='refresh' content='1;url=/admin'/>";