---
layout: post
title: "Python单例模式"
date: 2013-08-13 09:58
comments: true
categories: python 设计模式
---
设计模式，单例模式，老生常谈。

``` python
#!/usr/bin/python
#coding: UTF-8
"""
@author: CaiKnife

Singleton
"""
from functools import wraps

# 使用__new__方法构造单例类
class Singleton(object):
    def __new__(cls, *args, **kwargs):
        if not hasattr(cls, '_instance'):
            cls._instance = super(Singleton, cls).__new__(cls, *args, **kwargs)
        return cls._instance

print Singleton()
print Singleton()

# 使用单例装饰器构造单例类
def singleton(cls):
    instances = {}
    @wraps(cls)
    def wrapper(*args, **kwargs):
        if cls not in instances:
            instances[cls] = cls(*args, **kwargs)
        return instances[cls]
    return wrapper

@singleton
class MyClass(object):
    pass

print MyClass()
print MyClass()

# 使用getInstance方法构造单例对象，非线程安全
class MySingleton(object):
    @classmethod
    def getInstance(cls):
        if not hasattr(cls, '_instance'):
            cls._instance = cls()
        return cls._instance

print MySingleton.getInstance()
print MySingleton.getInstance()
```

<!-- more -->

输出结果：  
<\_\_main\_\_.Singleton object at 0x00BBFBD0>  
<\_\_main\_\_.Singleton object at 0x00BBFBD0>  
<\_\_main\_\_.MyClass object at 0x00BBFC30>  
<\_\_main\_\_.MyClass object at 0x00BBFC30>  
<\_\_main\_\_.MySingleton object at 0x00BBFC90>  
<\_\_main\_\_.MySingleton object at 0x00BBFC90>  

测试代码：

``` python
import __init__, unittest, threading
from singleton import Singleton, MyClass, MySingleton

class TestSingleton(unittest.TestCase):
    def setUp(self):
        self.singleton_instances = [Singleton() for x in range(100)]
        self.my_class_instances = [MyClass() for x in range(100)]
        self.my_singleton_instances = [MySingleton.getInstance() for x in range(100)]

    def tearDown(self):
        del self.singleton_instances, self.my_class_instances, self.my_singleton_instances

    def test_singleton(self):
        for x in self.singleton_instances:
            self.assertIs(x, Singleton())

    def test_my_class(self):
        for x in self.my_class_instances:
            self.assertIs(x, MyClass())

    def test_my_singleton(self):
        for x in self.my_singleton_instances:
            self.assertIs(x, MySingleton.getInstance())


if __name__ == "__main__":
    unittest.main()
```

Have a nice day！