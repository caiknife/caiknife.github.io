---
layout: post
title: "PHP APC cache"
date: 2013-11-08 14:46
comments: true
categories: php
---
在PHP脚本的生命周期中，包含四个阶段，如下图左侧所示：

{% fancybox /downloads/image/apc/apc.png PHP APC cache %}

<!-- more -->

1.Lexicon Scan，将PHP代码转换为语言片段(Tokens)。    
2.Parse，将Tokens转换成简单而有意义的表达式。    
3.Compilation，将表达式编译成Opocdes。    
4.Execution，顺次执行Opcodes，每次一条，从而实现PHP脚本的功能。

为了在系统级别能够加快PHP脚本的执行，可以用一些工具将Opcodes缓存起来，这样每次执行时就省略了前面三个阶段，直接执行Opcodes。就如同上图右侧所示。

在APC的官方网站<http://pecl.php.net/package/APC>可以下载到APC的源代码，其中包含一个APC.php可以用来查看当前APC的状态并进行管理。如下图所示：

{% fancybox /downloads/image/apc/apcinfo.png APC INFO %}

除了APC之外，还有xcache、eAccelerator、Zend Optimizor+可以对Opcodes进行缓存。

Have a nice day！

补充一下：

在PHP 5.5中，Zend Opcache已经成为默认的扩展，据说比APC的性能有所提高，参考下面的链接：  
<http://cnzhx.net/blog/zendopcache-accelerate-php/>  
<http://blog.wu-boy.com/2013/06/php-5-5-0-release-zend-opcache/>  
<http://www.linuxde.net/2013/07/14698.html>  
