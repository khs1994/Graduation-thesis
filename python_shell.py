#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import os
import time
import datetime

# 统一时间 20170101_12
now = datetime.datetime.now()
now_date = now.strftime('%Y%m%d_%H')
# 常量
ROOT = "/home/pi/Graduation-thesis/"
PHP_ROOT = "/usr/local/php/bin/php"
PYTHON_ROOT = "/usr/local/python3.6.1/bin/python3"


# 打印时间
def showtime():
    print(time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time())))


# 接受程序
def receive():
    # bash 命令
    bash1 = 'nohup ' + PYTHON_ROOT + ' -u ' + ROOT + 'can_run.py > ' + ROOT + 'index/doc/source.txt 2>&1 &'
    # print(bash1)
    if os.system(bash1) == 0:
        print("接收程序执行正确\n")
        print("循环执行中......\n")


# 处理
def redis():
    bash2 = PHP_ROOT + ' ' + ROOT + 'bash_redis.php ' + now_date
    # print(bash2)
    if os.system(bash2) == 0:
        pass


# 传输
def trans():
    bash3 = PHP_ROOT + ' ' + ROOT + 'bash_trans.php ' + now_date
    if os.system(bash3) == 0:
        pass


# 去掉原始文件空行
def delete():
    bash4=PHP_ROOT + ' ' + ROOT + 'bash_delete.php ' + now_date
    # print(bash4)
    if os.system(bash4) == 0:
        print("格式化文本成功\n")


def main_function():
    iii = 55
    while True:
        iii -= 5
        # Shell命令
        # 调用 sh 文件
        time.sleep(5)
        # print("还有%d秒" % iii)
        # 多少次跳出循环
        # 跳出循环
        if iii == 0:
            # 结束循环
            # 去空格
            showtime()
            print("结束循环\n")
            showtime()
            delete()
            # redis
            redis()
            # 调用微信公众平台
            trans()
            showtime()
            print("****************************************//END//****************************************")
            print("\n\n\n\n\n")
            break


if __name__ == '__main__':
    print("********************/START/********************")
    showtime()
    # 拨号上网程序
    # pass
    # 调用接收程序
    receive()
    # 格式化文本程序，时间为60s之内，比如55s
    # crontab 计划任务每分钟执行
    main_function()
