---
layout: post
title: "PHP 魔术方法"
date: 2013-10-17 16:10
comments: true
categories: php
---
PHP 中有若干魔术方法，如果在类中定义了这些魔术方法，会有一些很神奇的事情发生。这些魔术方法分别是：`__construct()` 、 `__destruct()` 、 `__call()` 、`__callStatic()` 、`__get()` 、 `__set()` 、 `__isset()` 、 `__unset()` 、 `__sleep()` 、`__wakeup()` 、 `__toString()` 、 `__invoke()` 、 `__set_state()` 、` __clone()` 。接下来我们分别感受一下它们的神奇之处。

<!-- more -->

##\_\_construct 和 \_\_destruct

这两个分别是 PHP 的构造函数和析构函数，在创建一个对象的时候会调用 `__construct` 的方法，销毁对象时会调用 `__destruct`方法。示例如下：

``` php
<?php
ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

class Base {
    public function __construct() {
        Zend_Debug::dump("You are calling a constructor!");
    }

    public function __destruct() {
        Zend_Debug::dump("You are calling a destructor!");
    }
}

$b = new Base();
unset($b);

/**
 * string 'You are calling a constructor!' (length=30)
 * string 'You are calling a destructor!' (length=29)
 */
```

##\_\_call() 和 \_\_callStatic()

这两个分别是调用类中无法访问到的、或者不存在的方法/静态方法时，会被调用到。示例如下：

``` php
<?php
ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

class Base {
    protected function test() {
        Zend_Debug::dump("test!");
    }

    protected static function staticTest() {
        Zend_Debug::dump("static test!");
    }

    public function __call($name, $params) {
        Zend_Debug::dump("You are calling normal method {$name} with params:");
        Zend_Debug::dump($params);
        $this->{$name}();
    }

    public function __callStatic($name, $params) {
        Zend_Debug::dump("You are calling static method {$name} with params:");
        Zend_Debug::dump($params);
        self::{$name}();
    }
}

$b = new Base();
$b->test();
$b->test(1, 2, 3);

Base::staticTest();
Base::staticTest(1, 2, 3);

/*
string 'You are calling normal method test with params:' (length=47)array (size=0)
  empty

string 'test!' (length=5)

string 'You are calling normal method test with params:' (length=47)

array (size=3)
  0 => int 1
  1 => int 2
  2 => int 3

string 'test!' (length=5)

string 'You are calling static method staticTest with params:' (length=53)

array (size=0)
  empty

string 'static test!' (length=12)

string 'You are calling static method staticTest with params:' (length=53)

array (size=3)
  0 => int 1
  1 => int 2
  2 => int 3

string 'static test!' (length=12)
 */
```

##\_\_get() 、 \_\_set() 、 \_\_isset() 、 \_\_unset()

`__get()` 在调用一个没有访问权限的、或者不存在的属性时被调用。  
`__set()` 在给一个没有访问权限的、或者不存在的属性赋值时被调用。  
`__isset()` 在给一个没有访问权限的、或者不存在的属性调用 `isset()` 或 `empty()` 函数时被调用。  
`__unset()` 在给一个没有访问权限的、或者不存在的属性调用 `unset()` 函数时被调用。

具体例子如下：

``` php
<?php
ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

class Base {
    public function __get($name) {
        Zend_Debug::dump("you are accessing {$name}");
    }

    public function __set($name, $value) {
        Zend_Debug::dump("you are assigning {$value} to {$name}");
    }

    public function __isset($name) {
        Zend_Debug::dump("you are judging {$name}");    
    }

    public function __unset($name) {
        Zend_Debug::dump("you are unsetting {$name}"); 
    }
}

$b = new Base();
// __set
$b->age = 20;
// __get
$b->age;
// __isset
isset($b->age);
// __isset
empty($b->age);
// unset
unset($b->age);

/*
string 'you are assigning 20 to age' (length=27)

string 'you are accessing age' (length=21)

string 'you are judging age' (length=19)

string 'you are judging age' (length=19)

string 'you are unsetting age' (length=21)
 */
```

##\_\_sleep() 、\_\_wakeup() 、 \_\_toString() 、 \_\_invoke()

`__sleep()` 方法在对象上使用 `serialize()` 函数时会被调用，这个方法要求返回一个包含序列化字段的数组，否则会引发一个 `E_NOTICE` 异常。  
`__wakeup()` 方法在调用 `unserialize()` 函数时会被调用。  
`__toString` 方法在把对象当成一个字符串时会被自动调用，比如 `echo $obj` 。但是不要在这个方法里抛出异常。  
`__invoke()` 方法在把对象当成一个函数时会被自动调用，比如 `$obj()` 。  

来看看例子：

``` php
<?php
ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

class Base {
    protected $id;
    protected $name;

    public function __construct() {
        $this->id = '12345';
        $this->name = 'caiknife';
    }

    public function __sleep() {
        Zend_Debug::dump("calling from serialize()");
        return array('id', 'name');
    }

    public function __wakeup() {
        Zend_Debug::dump("calling from unserialize()");
        $this->id = '54321';
    }

    public function __toString() {
        Zend_Debug::dump("calling __toString()");
        return $this->name;
    }

    public function __invoke() {
        Zend_Debug::dump("calling __invoke()");
    }
}

$b = new Base();
Zend_Debug::dump($b);
// __sleep
$s = serialize($b);
Zend_Debug::dump($s);
// __wakeup
$c = unserialize($s);
Zend_Debug::dump($c);
// __toString
echo $b;
// __invoke
$b();

/*
object(Base)[2]
  protected 'id' => string '12345' (length=5)
  protected 'name' => string 'caiknife' (length=8)

string 'calling from serialize()' (length=24)

string 'O:4:"Base":2:{s:5:"�*�id";s:5:"12345";s:7:"�*�name";s:8:"caiknife";}' (length=68)

string 'calling from unserialize()' (length=26)

object(Base)[3]
  protected 'id' => string '54321' (length=5)
  protected 'name' => string 'caiknife' (length=8)

string 'calling __toString()' (length=20)

caiknife

string 'calling __invoke()' (length=18)
 */
```

##\_\_set_state() 和 \_\_clone()

`__set_state()` 方法在对象上调用 `var_export()` 函数时被调用。这是一个静态方法。
`__clone()` 方法在对一个对象使用 `clone` 关键字时，新生成的对象会自动调用这个方法。

例子：

``` php
<?php
ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

class Base {
    protected $id;
    protected $name;

    public function __construct() {
        $this->id = '12345';
        $this->name = 'caiknife';
    }

    public static function __set_state($property) {
        Zend_Debug::dump("calling from var_export()");
        Zend_Debug::dump($property);
        return new self();
    }

    public function __clone() {
        Zend_Debug::dump("calling from clone");
        $this->id = strrev($this->id);
    }
}

$b = new Base();
$a = var_export($b, true);
Zend_Debug::dump($a);

eval('$s = '.$a.';');
Zend_Debug::dump($s);

$c = clone $b;
Zend_Debug::dump($b);
Zend_Debug::dump($c);

/*
string 'Base::__set_state(array(
   'id' => '12345',
   'name' => 'caiknife',
))' (length=72)
string 'calling from var_export()' (length=25)
array (size=2)
  'id' => string '12345' (length=5)
  'name' => string 'caiknife' (length=8)
object(Base)[3]
  protected 'id' => string '12345' (length=5)
  protected 'name' => string 'caiknife' (length=8)
string 'calling from clone' (length=18)
object(Base)[2]
  protected 'id' => string '12345' (length=5)
  protected 'name' => string 'caiknife' (length=8)
object(Base)[4]
  protected 'id' => string '54321' (length=5)
  protected 'name' => string 'caiknife' (length=8)
 */
```

PHP 的魔术方法真的挺有意思的，温故而知新，very nice！

Have a nice day！