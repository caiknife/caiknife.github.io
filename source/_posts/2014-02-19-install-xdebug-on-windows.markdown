---
layout: post
title: "Windows 平台上安装 xdebug 扩展"
date: 2014-02-19 20:18:27 +0800
comments: true
categories: php
---
`xdebug` 是一个非常非常好用的调试程序、追踪错误、性能分析的 PHP 扩展，可以在 <http://xdebug.org/> 这里找到相关的文档。在 Ubuntu 下面安装 xdebug 非常简单，直接执行下面的命令即可。

``` bash
$ sudo apt-get install php5-xdebug
```

<!-- more -->

但是在 Windows 上安装 xdebug ，就有点小麻烦了。首先我们一般是在 <http://windows.php.net/download/> 这里下载 PHP 环境，我用的 PHP 版本是 5.4.X 系列，只有 x86 架构的环境。

首先配置好 PHP 环境，我使用的是 PHP 5.4.X Thread Safe 版本，所以在安装 xdebug 的时候，应该要用对应的文件是—— [php_xdebug-2.2.3-5.4-vc9.dll](http://xdebug.org/files/php_xdebug-2.2.3-5.4-vc9.dll) ，千万不要选 64 位或者是 NTS 的版本，这样都无法正常启动 xdebug 。

下载好 DLL 文件之后，在 php.ini 文件中写入：

``` ini
zend_extension=D:/PHP5.4/ext/php_xdebug-2.2.3-5.4-vc9.dll

[xdebug] 
xdebug.auto_trace=on  
xdebug.collect_params=on 
xdebug.collect_return=on 
xdebug.profiler_enable=on 
xdebug.trace_output_dir="D:/PHP5.4/xdebug/trace"
xdebug.profiler_output_dir="D:/PHP5.4/xdebug/profiler"
xdebug.remote_enable=on 
xdebug.idekey=""
xdebug.remote_handler=dbgp 
xdebug.remote_host=localhost
xdebug.remote_port=8888
xdebug.remote_port=9000
```

重启 Apache 之后，命令行下输入 `php -v`，就应该能看到如下的信息：

``` bash
PHP 5.4.23 (cli) (built: Dec 11 2013 00:56:37)
Copyright (c) 1997-2013 The PHP Group
Zend Engine v2.4.0, Copyright (c) 1998-2013 Zend Technologies
    with Xdebug v2.2.3, Copyright (c) 2002-2013, by Derick Rethans
```

今后，如果在程序中发生错误的话，就会出现经典的橙色背景，并且还有非常详细的 stack strace 信息，看看下面的图：

{% fancybox /downloads/image/xdebug/1.jpg %}

{% fancybox /downloads/image/xdebug/2.png %}

Have a nice day！