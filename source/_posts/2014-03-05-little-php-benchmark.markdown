---
layout: post
title: "字符串拼接性能测试"
date: 2014-03-05 16:35:37 +0800
comments: true
categories: php
---
写这篇文章的目的是因为某天在群里有人出了一到笔试题——下面三种拼接字符串方式，哪一种的性能最快？

``` php 字符串拼接性能测试
<?php
$z = $x . $y;

$z = "$x$y";

$z = sprintf('%s%s', $x, $y);
```

<!-- more -->

我的第一直觉——第三种方式肯定是最慢的，因为要调用函数做额外的开销。在 PHP 中，使用语言特性要比使用函数的效率高，所以在平常工作中我会用 `$foo === null` 来代替 `is_null($foo)` 这样类似的技巧。

但是第一种和第二种方式哪个更快呢？于是我写了下面的这个测试脚本：

``` php bench.php
<?php
/**
 * Created by PhpStorm.
 * User: zcai
 * Date: 14-3-4
 * Time: 上午10:33
 *
 * 比较三种方式下，字符串拼接最快的方法。
 * 1、字符串直接连接
 * 2、双引号内变量转义
 * 3、sprintf格式化
 */

class Benchmark {
    protected $_timeStart = null;
    protected $_timeEnd   = null;

    protected $_repeatTimes = null;

    protected $_formatters = array();

    public function __construct() {
        $this->_repeatTimes = 10000;
    }

    public function setRepeatTimes($repeatTimes) {
        $this->_repeatTimes = $repeatTimes;
        return $this;
    }

    public function addFormatter(StringFormatter $formatter) {
        $this->_formatters[] = $formatter;
        return $this;
    }

    public function bench() {
        foreach ($this->_formatters as $formatter) {
            $this->_invokeFormatter($formatter);
        }
    }

    protected function _invokeFormatter(StringFormatter $formatter) {
        $this->_timeStart = microtime(true);
        for ($i = 0; $i < $this->_repeatTimes; ++$i) {
            $formatter->format();
        }
        $this->_timeEnd = microtime(true);

        var_dump($this->_timeEnd - $this->_timeStart);
    }
}

abstract class StringFormatter {
    protected $_firstString = '';
    protected $_secondString = '';

    protected $_result = '';

    public function __construct($firstString, $secondString) {
        $this->_firstString = $firstString;
        $this->_secondString = $secondString;
    }

    public function getResult() {
        return $this->_result;
    }

    public function getFirstString() {
        return $this->_firstString;
    }

    public function getSecondString() {
        return $this->_secondString;
    }

    abstract public function format();
}

class StringContactFormatter extends StringFormatter {
    public function format() {
        $this->_result = $this->_firstString . $this->_secondString;
        return $this;
    }
}

class DoubleQuoteFormatter extends StringFormatter {
    public function format() {
        $this->_result = "{$this->_firstString}{$this->_secondString}";
        return $this;
    }
}

class SprintfFormatter extends StringFormatter {
    public function format() {
        $this->_result = sprintf('%s%s', $this->_firstString, $this->_secondString);
        return $this;
    }
}

$benchmark = new Benchmark();
$benchmark->addFormatter(new StringContactFormatter('cai', 'knife'))
          ->addFormatter(new DoubleQuoteFormatter('cai', 'knife'))
          ->addFormatter(new SprintfFormatter('cai', 'knife'));

$benchmark->bench();
```

在笔记本上执行了若干次，得到下面的结果：

``` bash benchmark result
[16:44] caiknife@caiknife-ThinkPad-T400:~/www/phptest/benchmark
> php string.php 
double(0.025658130645752)
double(0.026356935501099)
double(0.050467967987061)

[16:44] caiknife@caiknife-ThinkPad-T400:~/www/phptest/benchmark
> php string.php
double(0.026144981384277)
double(0.025960922241211)
double(0.053239107131958)

[16:44] caiknife@caiknife-ThinkPad-T400:~/www/phptest/benchmark
> php string.php
double(0.029561042785645)
double(0.026247978210449)
double(0.051438093185425)

[16:44] caiknife@caiknife-ThinkPad-T400:~/www/phptest/benchmark
> php string.php
double(0.026479005813599)
double(0.026005983352661)
double(0.05446195602417)

[16:44] caiknife@caiknife-ThinkPad-T400:~/www/phptest/benchmark
> php string.php
double(0.026879072189331)
double(0.026219129562378)
double(0.051171064376831)

[16:44] caiknife@caiknife-ThinkPad-T400:~/www/phptest/benchmark
> php string.php
double(0.026100873947144)
double(0.025912046432495)
double(0.051729917526245)

[16:44] caiknife@caiknife-ThinkPad-T400:~/www/phptest/benchmark
> php string.php
double(0.025666952133179)
double(0.025691986083984)
double(0.051711082458496)

[16:44] caiknife@caiknife-ThinkPad-T400:~/www/phptest/benchmark
> php string.php
double(0.025789022445679)
double(0.050994873046875)

[16:44] caiknife@caiknife-ThinkPad-T400:~/www/phptest/benchmark
> php string.php
double(0.024961948394775)
double(0.025660991668701)
double(0.051864147186279)

[16:44] caiknife@caiknife-ThinkPad-T400:~/www/phptest/benchmark
> php string.php
double(0.025777101516724)
double(0.025542020797729)
double(0.05191707611084)

[16:44] caiknife@caiknife-ThinkPad-T400:~/www/phptest/benchmark
> php string.php
double(0.0258629322052)
double(0.02587890625)
double(0.050651073455811)

[16:44] caiknife@caiknife-ThinkPad-T400:~/www/phptest/benchmark
> php string.php
double(0.027279138565063)
double(0.026965141296387)
double(0.05425500869751)

[16:44] caiknife@caiknife-ThinkPad-T400:~/www/phptest/benchmark
> php string.php
double(0.027122020721436)
double(0.026250123977661)
double(0.053034067153931)

[16:44] caiknife@caiknife-ThinkPad-T400:~/www/phptest/benchmark
> php string.php
double(0.026654005050659)
double(0.02558708190918)
double(0.052467823028564)

[16:44] caiknife@caiknife-ThinkPad-T400:~/www/phptest/benchmark
> php string.php
double(0.02632212638855)
double(0.026074171066284)
double(0.051969051361084)
```

比较之下，第二种方法的性能最快，第一种方法稍微有点慢，而第三种方法则慢了一倍。

不过这下我又有点迷茫了——平常在做字符串拼接的时候，我都是用第一种方法，并且都是使用单引号来做字面量，因为一直觉得第二种方法里的双引号要对变量进行预先转义会有性能损失。可是这一次测试结果算下来，我之前的经验完全被推翻了啊！

哦，对了，忘记提到测试环境了：

``` bash environment
[17:11] caiknife@caiknife-ThinkPad-T400:~
> uname -a
Linux caiknife-ThinkPad-T400 3.2.0-59-generic #90-Ubuntu SMP Tue Jan 7 22:43:51 UTC 2014 x86_64 x86_64 x86_64 GNU/Linux
[17:11] caiknife@caiknife-ThinkPad-T400:~
> php -v
PHP 5.4.25-1+sury.org~precise+2 (cli) (built: Feb 12 2014 10:45:30) 
Copyright (c) 1997-2014 The PHP Group
Zend Engine v2.4.0, Copyright (c) 1998-2014 Zend Technologies
    with Xdebug v2.2.3, Copyright (c) 2002-2013, by Derick Rethans
```

Have a nice day！