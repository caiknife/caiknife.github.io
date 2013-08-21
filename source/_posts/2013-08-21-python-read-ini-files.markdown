---
layout: post
title: "Python读取ini文件"
date: 2013-08-21 14:08
comments: true
categories: python php ini hack
---
标准的ini文件中，应该有一个类似[section]的部分作为正文的开头，但是很多情况下，ini文件都是不标准的，比如下面这个格式：

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

在PHP中，对ini文件的格式容错率较高，不管是不是以[section]开头，PHP总能以正确的方式解析，不管是用原生的`parse_ini_file`函数还是`Zend_Config_Ini`类库，都可以非常方便无错地解析ini。

<!-- more -->

下面是用`parse_ini_file`解析文件的结果
``` php
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

$iniFile = './smus.ini';

Zend_Debug::dump(parse_ini_file($iniFile));
/* 
输出结果
array(12) {
  ["master.host"] => string(21) "stats10.mezimedia.com"
  ["master.username"] => string(6) "scltrk"
  ["master.password"] => string(10) "Mhok8homL7"
  ["master.dbname"] => string(8) "tracking"
  ["slave.host"] => string(21) "stats11.mezimedia.com"
  ["slave.port"] => string(4) "3308"
  ["slave.username"] => string(6) "scltrk"
  ["slave.password"] => string(10) "Mhok8homL7"
  ["slave.dbname"] => string(8) "tracking"
  ["msgsite"] => string(7) "smarter"
  ["offset"] => string(5) "20000"
}
 */

Zend_Debug::dump(parse_ini_file($iniFile, true));
/*
输出结果和上面的语句一样，但是如果ini文件中有[section]的话，输出数组的根key就是["section"]
 */
```

而如果用`Zend_Config_Ini`来解析的话，输出的结构会更清晰：

``` php
$config = new Zend_Config_Ini($iniFile);
Zend_Debug::dump($config->toArray());
/*
输出结果如下。同样如果ini文件中有[section]的话，输出数组的根key就是["section"]
array(5) {
  ["master"] => array(4) {
    ["host"] => string(21) "stats10.mezimedia.com"
    ["username"] => string(6) "scltrk"
    ["password"] => string(10) "Mhok8homL7"
    ["dbname"] => string(8) "tracking"
  }
  ["slave"] => array(5) {
    ["host"] => string(21) "stats11.mezimedia.com"
    ["port"] => string(4) "3308"
    ["username"] => string(6) "scltrk"
    ["password"] => string(10) "Mhok8homL7"
    ["dbname"] => string(8) "tracking"
  }
  ["msgsite"] => string(7) "smarter"
  ["offset"] => string(5) "20000"
}
 */
```

但是在Python中用来解析ini文件的ConfigParser类，则对ini文件的格式有着非常严格的要求——必须在文件开头含有[section]部分，否则在读取时会抛出`ConfigParser.MissingSectionHeaderError`。为了能让Python解析ini也可以容错，有必要做一下hack。

{% include_code python-ini/demo.py %}

如此一来就能正常工作了。部分内容参考自StackOverflow——[parsing .properties file in Python](http://stackoverflow.com/questions/2819696/parsing-properties-file-in-python/2819788#2819788) 。