---
layout: post
title: "类似Zend_Config_Ini风格的python parser"
date: 2013-08-21 18:13
comments: true
categories: python ini linux
---
写完上一篇文章之后，做了一个类似Zend_Config_Ini的Python parser，可以用来解析标准和非标准格式的ini文件，但是不能写文件。如果只用来读取文件内容的话，还是挺方便的。

ini文件内容如下：

``` ini
master.host                 = stats10.mezimedia.com
master.username             = scltrk
master.password             = Mhok8homL7
master.dbname               = tracking

slave.host                  = stats11.mezimedia.com
slave.port                  = 3308
slave.username              = scltrk
slave.password              = Mhok8homL7
slave.dbname                = tracking

msgsite                     = smarter
offset                      = 20000
```

<!-- more -->

源代码：

{% include_code python-ini/pyparseini.py %}

测试代码：

{% include_code python-ini/test_parseini.py %}

不知道把Python的东西写成Zend的风格是不是一种好习惯。对我来说，Python很轻巧，我很喜欢，Zend虽然很重，但是大气的架构和丰富的功能以及各种设计模式的运用都非常值得学习。艺不压身，努力学习才是关键。

Have a nice day！