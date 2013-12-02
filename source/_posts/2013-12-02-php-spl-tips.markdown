---
layout: post
title: "有关 PHP  SPL 的小技巧"
date: 2013-12-02 15:36:19 +0800
comments: true
categories: php
---
PHP SPL（Standard PHP Library）是从 PHP 5 开始加入的新内容，到 PHP 5.3 时有了更加丰富的功能，加入了各种数据结构 <http://us1.php.net/book.spl> 。这个东西看上去有点复杂，但是实际用起来非常有用，比普通的 PHP 函数提供了更加人性化、完全面向对象化的接口。

举两个例子来瞅瞅：

<!-- more -->

##目录的遍历

还在使用 `opendir` 、`readdir` 这样的函数来进行目录的遍历吗？使用 SPL 的 `DirectoryIterator` 类可以更加方便的进行遍历。不信你看代码：

``` php dir.php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

class DirectoryTraverser {  
    const FILE = 'file';
    const DIR  = 'directory';

    // 当前目录  
    protected $_directory;  
    // 深度  
    protected $_depth;  
      
    public function __construct(DirectoryIterator $directory, $depth=0) {  
        if (!($directory instanceof DirectoryIterator)) {  
            throw new Exception("Need a DirectoryIterator instance!");  
        }  
        $this->_directory = $directory;  
        $this->_depth = $depth;  
    }  
      
    public function dump() {  
        foreach ($this->_directory as $fileInfo) {  
            // 隐藏目录和上级目录不处理  
            if ($fileInfo->isDot() || substr($fileInfo->getFilename(), 0, 1) == '.') {  
                continue;  
            }  
            if ($fileInfo->isFile()) {  
                // 如果是文件，直接打印文件名  
                $this->dumpFile($fileInfo);  
            } elseif ($fileInfo->isDir()) {  
                // 如果是目录，打印目录名后深入  
                $this->dumpDirectory($fileInfo);  
            }  
        }  
    }  
      
    protected function _dump(DirectoryIterator $fileInfo, $type) {  
        if (!($fileInfo instanceof DirectoryIterator)) {  
            throw new Exception("Need a DirectoryIterator instance!");  
        }  
        Zend_Debug::dump(str_repeat(" ", $this->_depth*4)."This is a {$type}: ".$fileInfo->getPathname());  
    }  
      
    public function dumpFile(DirectoryIterator $fileInfo) {  
        $this->_dump($fileInfo, self::FILE);  
    }  
      
    public function dumpDirectory(DirectoryIterator $fileInfo) {  
        $this->_dump($fileInfo, self::DIR);  
        $dt = new DirectoryTraverser(new DirectoryIterator($fileInfo->getPathname()), $this->_depth+1);  
        $dt->dump();  
    }  
}  
$dir = new DirectoryIterator(dirname(__DIR__));  
$dt = new DirectoryTraverser($dir);  
$dt->dump();
```

输出结果会按照一定的缩进格式进行显示：

``` bash
> php dir.php                                                                                        
string(59) "This is a directory: /home/caiknife/source/phptest/dog-pile"
string(64) "    This is a file: /home/caiknife/source/phptest/dog-pile/1.php"
string(64) "    This is a file: /home/caiknife/source/phptest/dog-pile/2.php"
string(51) "This is a file: /home/caiknife/source/phptest/1.php"
string(57) "This is a directory: /home/caiknife/source/phptest/jquery"
string(63) "    This is a file: /home/caiknife/source/phptest/jquery/1.html"
string(76) "    This is a file: /home/caiknife/source/phptest/jquery/jquery.1.8.3.min.js"
string(57) "This is a directory: /home/caiknife/source/phptest/remote"
string(78) "    This is a file: /home/caiknife/source/phptest/remote/file_get_contents.php"
string(65) "    This is a file: /home/caiknife/source/phptest/remote/curl.php"
string(54) "This is a directory: /home/caiknife/source/phptest/spl"
string(61) "    This is a file: /home/caiknife/source/phptest/spl/dir.php"
string(62) "    This is a file: /home/caiknife/source/phptest/spl/heap.php"
string(51) "This is a file: /home/caiknife/source/phptest/2.php"
string(53) "This is a file: /home/caiknife/source/phptest/apc.php"
string(62) "This is a directory: /home/caiknife/source/phptest/magicmethod"
string(67) "    This is a file: /home/caiknife/source/phptest/magicmethod/1.php"
string(67) "    This is a file: /home/caiknife/source/phptest/magicmethod/2.php"
string(67) "    This is a file: /home/caiknife/source/phptest/magicmethod/3.php"
string(67) "    This is a file: /home/caiknife/source/phptest/magicmethod/5.php"
string(67) "    This is a file: /home/caiknife/source/phptest/magicmethod/6.php"
string(67) "    This is a file: /home/caiknife/source/phptest/magicmethod/4.php"
```

##排序

平常使用 PHP 来做排序，基本上就用 `sort` 、`usort` 、`ksort` 之类的函数来处理，不过在 PHP 5.3 的 SPL 中，由于引入了数据结构，因此就可以用堆来进行排序了。

有关对排序中`最大堆`和`最小堆`的概念—— <http://zh.wikipedia.org/wiki/%E6%9C%80%E5%A4%A7%EF%BC%8F%E6%9C%80%E5%B0%8F%E5%A0%86>：

在使用最大堆/最小堆时，向其中插入数据的同时， PHP 内部就会直接做好排序的动作。因此，数据插入完毕后，我们直接遍历这个堆，就能得到排序好的数据。请看示例程序：

``` php heap.php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

$minHeap = new SplMinHeap();
$maxHeap = new SplMaxHeap();

for ($i=1; $i<=10; $i++) {
    $minHeap->insert(rand(1, 1000));
    $maxHeap->insert(rand(1, 1000));
}


Zend_Debug::dump("min heap!");
Zend_Debug::dump($minHeap);
foreach ($minHeap as $value) {
    Zend_Debug::dump($value);
}

Zend_Debug::dump("max heap!");
Zend_Debug::dump($maxHeap);
foreach ($maxHeap as $value) {
    Zend_Debug::dump($value);
}
```

输出结果如下：

``` bash
> php heap.php                                                                                       

string(9) "min heap!"

class SplMinHeap#2 (3) {
  private $flags =>
  int(0)
  private $isCorrupted =>
  bool(false)
  private $heap =>
  array(10) {
    [0] =>
    int(331)
    [1] =>
    int(341)
    [2] =>
    int(349)
    [3] =>
    int(561)
    [4] =>
    int(442)
    [5] =>
    int(713)
    [6] =>
    int(407)
    [7] =>
    int(707)
    [8] =>
    int(720)
    [9] =>
    int(585)
  }
}

int(331)
int(341)
int(349)
int(407)
int(442)
int(561)
int(585)
int(707)
int(713)
int(720)



string(9) "max heap!"

class SplMaxHeap#3 (3) {
  private $flags =>
  int(0)
  private $isCorrupted =>
  bool(false)
  private $heap =>
  array(10) {
    [0] =>
    int(928)
    [1] =>
    int(649)
    [2] =>
    int(917)
    [3] =>
    int(524)
    [4] =>
    int(516)
    [5] =>
    int(610)
    [6] =>
    int(125)
    [7] =>
    int(65)
    [8] =>
    int(207)
    [9] =>
    int(391)
  }
}

int(928)
int(917)
int(649)
int(610)
int(524)
int(516)
int(391)
int(207)
int(125)
int(65)
```

SPL 的功能十分强大，而且都是面向对象化的接口，PHP 终于不再那么像是一个过程式的语言了，嗯哼～

Have a nice day！