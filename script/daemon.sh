#!/bin/bash
while true
do
ps -ef | grep "python_shell.py" | grep -v "grep"
if [ $? -eq 1 ]
then
#/home/pi/Graduation-thesis/CAN_Main.sh
nohup /home/pi/Graduation-thesis/CAN_Main.sh  > /tmp/myout.file 2>&1 &
echo "没有运行,守护脚本将启动程序"
else
echo "运行中,1s后继续判断"
fi
sleep 1
done
