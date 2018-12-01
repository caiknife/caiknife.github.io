---
layout: post
title: "算法题：羊生羊问题"
date: 2018-12-01 18:25:40 +0800
comments: true
categories: 算法 编程 实战
---
面试题：

> 农夫有一只羊，这只羊在第2、3年会生一只小羊，第4年不会生小羊，第5年时羊会死亡，生出来的小羊也是这个规律。求问50年后会有多少只羊？

<!-- more -->

这道题最直接的办法就是用递归来做，不过我暂时还没想到递归怎么做，所以先用最笨的方法——遍历50年，每一年遍历羊的数组，根据年纪来进行对应的处理。详细见下面的代码：

{% include_code quiz/sheep.php %}

但是这个方法是有问题的，那就是数组占用的内存太大，用PHP来跑的话，50的数据量根本就没办法解决。

```
->php cli.php Sheep.main                                                                                                   master [08f1c0ba] modified
PHP Fatal error:  Allowed memory size of 1073741824 bytes exhausted (tried to allocate 1073741832 bytes) in /Users/caiknife/Projects/PHPLib/src/Cron/Sheep.php on line 34
PHP Stack trace:
PHP   1. {main}() /Users/caiknife/Projects/PHPLib/cli.php:0
PHP   2. App\Cron\Sheep->main() /Users/caiknife/Projects/PHPLib/cli.php:34

Fatal error: Allowed memory size of 1073741824 bytes exhausted (tried to allocate 1073741832 bytes) in /Users/caiknife/Projects/PHPLib/src/Cron/Sheep.php on line 34

Call Stack:
    0.0003     369648   1. {main}() /Users/caiknife/Projects/PHPLib/cli.php:0
    0.0335    5328840   2. App\Cron\Sheep->main() /Users/caiknife/Projects/PHPLib/cli.php:34
```

下面尝试一下用递归的解法。

{% include_code quiz/sheep_recursive.php %}

最后再使用一个移动窗口的解法。

{% include_code quiz/sheep_window.php %}
