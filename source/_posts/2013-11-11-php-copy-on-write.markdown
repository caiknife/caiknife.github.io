---
layout: post
title: "PHP 写时复制"
date: 2013-11-11 22:28
comments: true
categories: php
---
今天光棍节，随便扯点东西。

观察下面的代码：

<!-- more -->

``` php
<?php
var_dump(memory_get_usage());

$a = ['caiknife', 'caiknife', 'caiknife'];

var_dump(memory_get_usage());

$b = $a;

var_dump(memory_get_usage());

$b[0] = 'efinkiac';

var_dump(memory_get_usage());
```

在浏览器下运行这段代码，看看输出：

``` bash
int 239912
int 240648
int 240736
int 241264
```

接下来我们来做做数学题，看看这几条语句的内存消耗情况是怎样的？

第一步：240648 - 239912 = 736

第二步：240736 - 240648 = 88

第三步：241264 - 240736 = 528

第二步和第三步消耗：88 + 528 = 616

第一步比后面两步多消耗：736 - 616 = 120

总共消耗：241264 - 239912 = 1352

一开始可能会觉得很奇怪——为什么第二步在做赋值的时候内存消耗这么少呢？为什么第三步修改了 `$b` 的数据之后，又会消耗这么多的内存呢？

这就是 PHP 的写时复制原理了。

当用一个变量去给另外一个变量赋值之后，实际上这两个变量一开始会指向同一块内存。在这个例子中，`$a` 和 `$b` 在第二步结束之后，就会指向内存中保存了数组信息的那一块数据。而在第三步修改 `$b` 的数据时，首先会开辟出一块新的内存把原有的数据拷贝过去，并将 `$b` 指向这块内存，然后再修改数据。

而这，就牵涉到 PHP 内存管理中数据保存的方式了，在 PHP 中，所有的数据都是保存在下面的 C 结构体内。

``` c
struct _zval_struct {
    /* Variable information */
    zvalue_value value;     /* value */
    zend_uint refcount;
    zend_uchar type;        /* active type */
    zend_uchar is_ref;
};
```

其中 `refcount` 就是引用计数，一开始给 `$a` 赋值时，`refcount` 记成 1 ，表示可以通过 `$a` 来访问这块内存；之后 `$b = $a` ，`refcount` 就记成 2 ，这样无论 `$a` 还是 `$b` 都可以访问这块内存。一旦给 `$b` 写入新值之后，内存中就有了两个结构体，`refcount` 都为 1，分别通过这两个变量来访问。PHP 内核就是通过这种方式来达成内存的分配和优化。

这样计算一下，为什么第一步会比第二步第三步多用 120 字节呢？我暂时也不知道，希望能尽快找到答案。

Have a nice day！
