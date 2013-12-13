---
layout: post
title: "PHP 性能优化技巧"
date: 2013-12-09 10:25:50 +0800
comments: true
categories: php
---
结合几年来的工作经验，加上在网上搜索到的一些资料，整理了一些能提高 PHP 代码运行效率的技巧。虽然单个技巧看起来提高的性能很小，但是从多个方面综合起来看，一个点上的性能提高了，多个点集合起来，程序的整体性能就会有明显提高。

好吧，接下来看看到底可以从哪些方面来提高 PHP 代码运行效率。

<!-- more -->

##基本技巧

1、尽可能使用单引号代替双引号。

2、数组下标加上单引号。

3、使用 `$i += 1` 来代替 `$i = $i + 1`;

4、使用 `++$i` 来代替 `$i++`，因为前者只要三条 opcodes，而后者要四条 opcodes 。

5、在 for 循环内不要计算循环次数，要在循环之前就计算好。比如：

``` php
<?php
// wrong!
for ($i = 0; $i < strlen($string); ++$i) {
    // bla bla bla
}

// right!
$loop = strlen($string);
for ($i = 0; $i < $loop; ++$i) {
    // bla bla bla
}
```

6、尽量使用 `include/require` 来代替 `include_once/require_once`。

7、尽量在 `include/require` 中使用绝对路径，避免在 include_path 中查找文件。

8、简单的 `if else` 语句可以用三目运算符来代替。

9、多个分支的 `if else` 语句可以用 `switchi case` 语句来代替。

10、尽量避免使用 `@` 来抑制错误输出，错误应该是用来解决的，不是忽略的。

11、使用完大变量和全局变量后，记得 unset 。

12、使用语言结构来代替函数调用，比如：

``` php
// bad
define('NAME', 'caiknife');

// good
const NAME = 'caiknife';

// bad
if (is_null($foo)) {
    // bla bla bla
}

// good
if ($foo === NULL) {
    // bla bla bla
}
```

14、避免在循环中多次插入数据库数据，应该在循环中拼 SQL ，然后一次性插入数据库。

##类/函数

1、使用 `file_get_contents` 和 `file_put_contents` 来读写文件。

2、在精度要求不高的情况下，可以用 `$_SERVER["REQUEST_TIME_FLOAT"]` 和 `$_SERVER["REQUEST_TIME_FLOAT"]` 来代替 `time()` 来获取脚本开始执行的时间。

3、尽量避免使用正则（检查是否可以使用 strtr/strncasecmp/strpbrk/stripos/str_replace 来代替）。

4、类方法尽可能声明成 static 方法。

5、尽量避免使用递归。

6、当操作字符串并需要检验其长度是否满足某种要求时，你想当然地会使用 strlen() 函数。此函数执行起来相当快，因为它不做任何计算，只返回在 zval 结构（C 的内置数据结构，用于存储 PHP 变量）中存储的已知字符串长度。但是，由于 strlen() 是函数，多多少少会有些慢，因为函数调用会经过诸多步骤，如字母小写化（函数名小写化，PHP不区分函数名大小写）、哈希查找，会跟随被调用的函数一起执行。在某些情况下，你可以使用 isset() 技巧加速执行你的代码。

``` php
// bad
if (strlen($foo) < 5) { 
    echo “Foo is too short”; 
}

// good
if (!isset($foo{5})) { 
    echo “Foo is too short”; 
}
```

调用 isset() 恰巧比 strlen() 快，因为与后者不同的是，isset() 作为一种语言结构，意味着它的执行不需要函数查找和字母小写化。也就是说，实际上在检验字符串长度的顶层代码中你没有花太多开销。

7、并不是所有的程序都需要用到 OOP ，面向对象往往开销很大，每个方法和对象调用都会消耗很多内存。

8、尽量使用 PHP 的内置函数。

9、不要使用空函数，其花费的时间相当于执行 7 至 8 次的局部变量递增操作。类似的方法调用所花费的时间接近于 15 次的局部变量递增操作。

10、派生类中的方法运行起来要快于在基类中定义的同样的方法。

##其他

1、使用 APC Cache 、Opcache 等来缓存 PHP Opcode 。

2、评估检验你的代码。检验器会告诉你，代码的哪些部分消耗了多少时间。Xdebug 调试器包含了检验程序，评估检验总体上可以显示出代码的瓶颈。

3、使用 ip2long() 和 long2ip() 函数把 IP 地址转成整型存放进数据库而非字符型。这几乎能降低 1/4 的存储空间。同时可以很容易对地址进行排序和快速查找。

4、Apache 解析一个 PHP 脚本的时间要比解析一个静态 HTML 页面慢 2 至 10 倍。尽量多用静态 HTML 页面，少用脚本。

暂时只整理了这些，今后会继续补充。Have a nice day！
