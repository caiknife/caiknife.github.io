---
layout: post
title: "递归与迭代"
date: 2014-02-15 12:01:21 +0800
comments: true
categories: 文艺女与理工男 php
---
虽然题目写的是《递归与迭代》这么一个 `geek` 的标题，不过接下来的故事我其实是想写一个`文艺女与理工男`的故事。

写这篇文章的动机来自 <http://weibo.com/1649186337/AmLoK2Upy> 。未来的社会，会写代码的人越来越多，编程就像今天的外语技能一样，会成为一项基本技能。

<!-- more -->

OK，先来设定一下故事背景：文艺女与理工男目前处在暧昧期，目前还没有确立关系，不过经常混在一起，理工男给文艺女普及一些电脑知识，文艺女教会理工男享受生活。

这一天，理工男又给文艺女上编程课了，当然教她的语言是世界上最好的语言——PHP，文艺女已经了解了这门语言的基本语法，今天理工男就要教她`递归`。

男：好，今天我们来学习递归。你知道递归是什么吗？

女：不知道，那我先去 Google 一下～咦，怎么会是这个样子？

{% fancybox /downloads/image/recursion/recursion.jpg 递归 %}

男：哈哈，果然让你碰到了这个坑。其实递归是指在函数的定义中使用函数自身的方法。高中的时候学过 fibonacci 数列吧？

女：嗯，这个我知道。f(x) = f(x-1) + f(x-2)，其中 f(1) = 1，f(2) = 1。

男：很好，你从这个公式里就可以看到函数调用了自身，之前你已经学过了函数如何定义和调用，下面来试着自己写一下代码吧？

女：好，我来试试。你看下面这个行不行？

``` php 使用递归的 fibonacci 数列
<?php
/**
 * 使用递归的 fibonacci 数列
 *
 * @param $n
 * @return bool|int
 */
function fibonacci($n) {
    if ($n < 0) {
        return false;
    }
    if ($n === 0) {
        return 0;
    } elseif ($n === 1) {
        return 1;
    }
    return fibonacci($n - 1) + fibonacci($n - 2);
}

echo fibonacci(10);
```

男：哇，一下子就写对了，你是我见过最有编程天赋的女孩子，不如改行当程序员吧！你可以做 PHP 女神！

女：哼，本姑娘冰雪聪明，这点小意思难不倒我。

男：哦，那好吧，那你算一下 fibonacci 数列第 1000 个数字是多少？

女：换个参数就行了，看我的～咦，怎么结果是这样？

``` bash 栈溢出了
Fatal error: Maximum function nesting level of '100' reached, aborting! 
```

男：这牵涉到计算机系统中`栈`的概念和编程语言中使用`栈`来保存递归状态。你这个情况就是碰到栈溢出了。

女：栈是什么？溢出又是什么意思？

男：打个比方吧。你有一个瓶子和一堆小球，用来给递归的层数做一个计数器，每当递归的层数深入一层的时候，你就往瓶子里放一颗小球，直到把瓶子放满了……

女：瓶子放满了，后面的小球就再也放不进去了，递归层数就没办法继续做计数了，所以计算机就报错了，对不对？

男：Bingo！你真是太聪明了，一点就通啊，真的不考虑听我的建议转行当程序员？

女：哼，才不要～原来递归就好像《盗梦空间》里的多层梦境，栈的溢出就好比进入了 limbo 层，诺兰导演原来是个程序员！

男：你的这个解释很有新意……

女：那有没有别的办法求出第 1000 项的数字呢？

男：可以的，用迭代来代替递归即可。我之前教过你循环语句，你要不要试着改写一下代码，用循环来做？

女：好，那我试试。

``` php 使用迭代的 fibonacci 数列
<?php
/**
 * 使用迭代的 fibonacci 数列
 *
 * @param $n
 * @return bool|int
 */
function fibonacci_with_iteration($n) {
    if ($n < 0) {
        return false;
    }
    $fibo = array(0, 1);
    for ($i = 2; $i <= $n; ++$i) {
        $fibo[$i] = $fibo[$i - 1] + $fibo[$i - 2];
    }
    return $fibo[$n];
}

echo fibonacci_with_iteration(1000);
```

男：一点就通，果然很棒，不过你这个解法会造成大量内存空间的浪费，我重写一个版本给你吧。

``` php 节省内存空间的做法
<?php
/**
 * 节省内存空间的做法
 *
 * @param $n
 * @return bool|int
 */
function fibonacci($n) {
    list($f0, $f1, $result) = array(0, 1, 1);
    if ($n < 0) {
        return false;
    }
    if ($n < 2) {
        return 1;
    }
    for ($i = 2; $i <= $n; ++$i) {
        $result = $f0 + $f1;
        $f0 = $f1;
        $f1 = $result;
    }
    return $result;
}

echo fibonacci(1000);
```

女：迭代这里看不懂啊……

男：呵呵，看不懂那我下次解释给你听，下回我再教你用面向对象的方式重写 fibonacci 数列的解决方案。

女：对象？你这么一个呆头呆脑的理工男还想有对象？

男：人艰不拆……

以上内容，纯属瞎掰，如有雷同，不胜荣幸。

Have a nice day！