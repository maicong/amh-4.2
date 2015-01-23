#AMH 4.2 - 麦葱修改版
- 添加pcre-8.35
- 添加zlib-1.2.8
- 添加openssl-1.0.1k
- 添加jemalloc-3.6.0
- 添加ssh ip黑名单: http://antivirus.neu.edu.cn/scan/ssh.php
- 替换php-5.3.27为php-5.6.4
- 替换nginx-1.4.7为tengine-2.1.0
- 修改提示文字为中文
- 修改错误提示页

发布页：http://www.yuxiaoxi.com/2015-01-13-amh-mc.html

**使用方法：**

1、安装wget命令

Centos:

`yum install -y wget`

Debian/Ubuntu:

`apt-get install wget`

2、运行一句话安装命令

`wget http://u1.cdn.yuxiaoxi.com/linux/amh-4.2/amh-mc.sh && bash amh-mc.sh 2>&1 | tee amh-mc.log`

更多模块可在管理面板进行安装。

**问题解决：**

php5.6.4的`extension_dir`是`/usr/local/php/lib/php/extensions/no-debug-non-zts-20131226`。

如果你在后台安装php相关模块报错无法使用，请检查`/etc/php.ini`文件，将里面的`no-debug-non-zts-20090626`修改为`no-debug-non-zts-20131226`。
