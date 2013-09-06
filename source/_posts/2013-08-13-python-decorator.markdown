---
layout: post
title: "使用装饰器为Python函数做缓存"
date: 2013-08-13 10:11
comments: true
categories: python 设计模式 装饰器
---
写了个函数作为装饰器为Fibonacci数列做缓存，做了一下性能比较。

<!-- more -->

``` python
#!/usr/bin/python
# coding: UTF-8

import datetime
now = datetime.datetime.now
from functools import wraps

def cache(func):
    caches = {}
    @wraps(func)
    def wrap(*args):
        if args not in caches:
            caches[args] = func(*args)
        return caches[args]
    return wrap

def fib(num):
    if num < 2:
        return 1
    return fib(num-1) + fib(num-2)

fib_with_cache = cache(fib)

start = now()
for i in range(10):
    fib(30)
end = now()
print "Fib without cache costs: %r" % (end - start)

start = now()
for i in range(10):
    fib_with_cache(30)
end = now()
print "Fib with cache costs: %r" % (end - start)
```

输出结果：  
Fib without cache costs: datetime.timedelta(0, 6, 219000)  
Fib with cache costs: datetime.timedelta(0, 0, 672000)  

当然，正确的使用Fibonacci数列的方法应该是在函数体内使用循环，而不是使用递归。多层递归很容易对性能造成非常严重的影响。

Have a nice day！