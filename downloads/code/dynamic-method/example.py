#!/usr/bin/python
#coding: UTF-8
"""
@author: CaiKnife

根据函数名称动态调用
"""

# normal function
def do_foo():
    print "foo!"

class Print():
    # normal method
    def do_foo(self):
        print "foo!"

    # static method
    @staticmethod
    def static_foo():
        print "static foo!"

def main():
    obj = Print()

    func_name = "do_foo"
    static_name = "static_foo"

    # use eval() to get the string value from a variable
    eval(func_name)()

    # use getattr() to get method from a class or an instance
    # sometimes you have to use is_callable() to make sure that it is a method but not an attr
    getattr(obj, func_name)()
    getattr(Print, static_name)()


if __name__ == '__main__':
    main()