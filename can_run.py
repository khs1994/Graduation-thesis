#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import spidev
import time
import sys
from z_can_mcp2515 import *

# ① 接收CAN总线数据文件程序
# 命令常量
spi = spidev.SpiDev(0, 0)


def mcp2515_reset():
    tmp = [0xc0]
    spi.writebytes(tmp)


def mcp2515_write_reg(address, val):
    buf = [0x02, address, val]
    spi.writebytes(buf)


def mcp2515_read_reg(address):
    buf = [0x03, address, 0x55]
    buf = spi.xfer2(buf)
    return int(buf[2])


def mcp2515_init():
    mcp2515_reset()
    time.sleep(2)
    # 设置波特率为125Kbps
    # set CNF1,SJW=00,长度为1TQ,BRP=49,TQ=[2*(BRP+1)]/Fsoc=2*50/8M=12.5us
    mcp2515_write_reg(CNF1, CAN_125Kbps)
    # set CNF2,SAM=0,在采样点对总线进行一次采样，PHSEG1=(2+1)TQ=3TQ,PRSEG=(0+1)TQ=1TQ
    mcp2515_write_reg(CNF2, 0x80 | PHSEG1_3TQ | PRSEG_1TQ)
    # set CNF3,PHSEG2=(2+1)TQ=3TQ,同时当CANCTRL.CLKEN=1时设定CLKOUT引脚为时间输出使能位
    mcp2515_write_reg(CNF3, PHSEG2_3TQ)
    mcp2515_write_reg(TXB0SIDH, 0xFF)
    # 发送缓冲器0标准标识符高位
    mcp2515_write_reg(TXB0SIDL, 0xEB)
    # 发送缓冲器0标准标识符低位(第3位为发送拓展标识符使能位)
    mcp2515_write_reg(TXB0EID8, 0xFF)
    # 发送缓冲器0拓展标识符高位
    mcp2515_write_reg(TXB0EID0, 0xFF)
    # 发送缓冲器0拓展标识符低位
    mcp2515_write_reg(RXB0SIDH, 0x00)
    # 清空接收缓冲器0的标准标识符高位
    mcp2515_write_reg(RXB0SIDL, 0x00)
    # 清空接收缓冲器0的标准标识符低位
    mcp2515_write_reg(RXB0EID8, 0x00)
    # 清空接收缓冲器0的拓展标识符高位
    mcp2515_write_reg(RXB0EID0, 0x00)
    # 清空接收缓冲器0的拓展标识符低位
    mcp2515_write_reg(RXB0CTRL, 0x40)
    # 仅仅接收拓展标识符的有效信息
    mcp2515_write_reg(RXB0DLC, DLC_8)
    # 设置接收数据的长度为8个字节
    mcp2515_write_reg(RXF0SIDH, 0xFF)
    # 配置验收滤波寄存器n标准标识符高位
    mcp2515_write_reg(RXF0SIDL, 0xEB)
    # 配置验收滤波寄存器n标准标识符低位(第3位为接收拓展标识符使能位)
    mcp2515_write_reg(RXF0EID8, 0xFF)
    # 配置验收滤波寄存器n拓展标识符高位
    mcp2515_write_reg(RXF0EID0, 0xFF)
    # 配置验收滤波寄存器n拓展标识符低位
    mcp2515_write_reg(RXM0SIDH, 0xFF)
    # 配置验收屏蔽寄存器n标准标识符高位
    mcp2515_write_reg(RXM0SIDL, 0xE3)
    # 配置验收屏蔽寄存器n标准标识符低位
    mcp2515_write_reg(RXM0EID8, 0xFF)
    # 配置验收滤波寄存器n拓展标识符高位
    mcp2515_write_reg(RXM0EID0, 0xFF)
    # 配置验收滤波寄存器n拓展标识符低位
    mcp2515_write_reg(CANINTF, 0x00)
    # 清空CAN中断标志寄存器的所有位(必须由MCU清空)
    mcp2515_write_reg(CANINTE, 0x01)
    # 配置CAN中断使能寄存器的接收缓冲器0满中断使能,其它位禁止中断
    mcp2515_write_reg(CANCTRL, REQOP_NORMAL | CLKOUT_ENABLED)
    # 将MCP2515设置为正常模式,退出配置模式


def mcp2515_read():
    # num = 0
    buf = []
    if mcp2515_read_reg(CANINTF) & 0x01:
        # 读取接收缓冲器0接收到的数据长度(0~8个字节)
        num = mcp2515_read_reg(RXB0DLC)
        for i in range(num):
            # 把CAN接收到的数据放入指定缓冲区
            buf.append(mcp2515_read_reg(RXB0D0 + i))
    # 清除中断标志位(中断标志寄存器必须由MCU清零)
    mcp2515_write_reg(CANINTF, 0)
    return buf


# 主要函数
def mcp_run():
    iii = 0
    # 一直循环执行，每次等待多少s
    while True:
        iii += 1
        buf = mcp2515_read()
        # 长度为0，退出
        if len(buf) == 0:
            print('')
        # 不为0，循环读取
        else:
            print(time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time())))
            for i in buf:
                int_to_hex = hex(i)
                # print(int_to_hex)
                hex_len = len(int_to_hex)
                # print(hex_len)
                hex_list = [0, 0, 0, 0]
                if hex_len == 3:
                    iiii = 0
                    for j in int_to_hex:
                        iiii += 1
                        if iiii == 1:
                            hex_list[0] = j
                        if iiii == 2:
                            hex_list[1] = j
                            hex_list[2] = 0
                        if iiii == 3:
                            hex_list[3] = j

                    for n in hex_list:
                        print(n, end='')
                    print(' ', end='')
                # print(hex(int(i)), end=' ')
                else:
                    print(int_to_hex, end=' ')
        # 多少次跳出循环,55 s
        if iii == 110:
            break
        # 休眠
        time.sleep(0.5)


if __name__ == '__main__':
    # 首先初始化
    mcp2515_init()
    # 开始循环接收
    mcp_run()
