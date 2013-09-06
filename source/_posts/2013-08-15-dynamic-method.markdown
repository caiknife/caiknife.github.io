---
layout: post
title: "根据字符串名称动态调用Python的函数和对象方法"
date: 2013-08-15 14:31
comments: true
categories: python php magic
---
在PHP里，根据字符串动态调用方法是一件很简单的事情。

``` php
class Foo {
    // static method
    public static function sbar() {
        echo "foosbar!";
    }

    // normal method
    public function bar() {
        echo "foobar!";
    }  
}

// normal function
function bar() {
    echo "bar!";
}

$name = "bar";
$sname = "sbar";

$name();

$foo = new Foo();
$foo->{$name}();
$foo::$sname();
```
输出结果：  
> bar!  
> foobar!  
> foosbar!

<!-- more -->

而在Python中，没有PHP中这么简单，需要一点小技巧才能实现。也许这就是没有$的坏处吧，有钱确实就是不一样，呵呵。

{% include_code dynamic-method/example.py %}

输出结果：  
> foo!  
> foo!  
> static foo! 

有点儿意思～～～

Have a nice day！