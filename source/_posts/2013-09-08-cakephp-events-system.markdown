---
layout: post
title: "CakePHP 事件机制"
date: 2013-09-08 01:41
comments: true
categories: cakephp events observer 设计模式
---
内置事件机制的编程语言不多，而绝大部分编程语言都是靠观察者设计模式来实现这一机制的。而引入事件机制的目的之一，就是要解耦，让代码能够更加容易维护，让一个对象只做自己该做的事情，不要去做别的对象应该做的事情。

现在 CakePHP 中的 Controller 和 Model 中，都有 `getEventManager()` 方法，这就相当于观察者模式中的`被观察者`，而我们自己编写的事件就是`观察者`。在别的地方编写代码注册事件之后，在一个地方进行事件的广播，从而调用所有的观察者方法。

<!-- more -->

我们来看看怎么使用 CakePHP 的事件机制。首先你得创建一个 `CakeEvent` 对象：

``` php
class EventsController extends AppController {
    public $uses = array();

    public function index() {
        /**
         * CakeEvent构造函数接受三个参数
         * @param $name 事件名称，尽量全局唯一
         * @param $subject 被观察者对象，一般都用 $this
         * @param $data 保存参数，可以通过 $event->data 获得
         * @type {CakeEvent}
         */
        $event = new CakeEvent(
            'Controller.events.index', 
            $this, 
            array(
                'profile' => array(
                    'name' => 'caiknife', 
                    'email' => 'caiknife@foxmail.com',
                ),
            )
        );

        /**
         * 进行广播，调用观察者的方法
         */
        $this->getEventManager()->dispatch($event);

        debug($event->result);
    }
}

```

我们先用一个匿名函数来测试一下，为了方便，统一在 Controller 的 `beforeFilter()` 方法里进行注册。

``` php
public function beforeFilter() {
    parent::beforeFilter();
    
    $this->getEventManager()->attach(
        function($profile){
            pr('In an anonymous function.');
            pr($profile);
        }, 
        'Controller.events.index', 
        array(
            'passParams' => true,
            'priority' => 2,
        )
    );
}
```

`EventManager` 的 `attach` 方法接受三个参数，第一个是回调方法；第二个就是前面提到的事件名称，要求全局唯一；第三个参数是设置选项，传入的值不同可能会影响回调函数的动作。

`passParams` 这个参数设置为 `true`，会把 `$event->data` 的值作为回调函数的参数传入，而不设置的话，回调函数的参数就是 `$event` 对象。我个人觉得还是不要设置为 `true` ，直接操作 `$event` 对象能让程序更灵活。

`priority` 设置回调函数的优先级，默认情况下都是 10 ，数字越小则优先执行，数字相等的话则根据回调函数注册顺序执行。

上面的代码按我个人的喜好风格，还可以写成：

``` php
public function beforeFilter() {
    parent::beforeFilter();
    
    $this->getEventManager()->attach(
        function($event){
            pr('In an anonymous function.');
            pr($event->data['profile']);
        }, 
        'Controller.events.index', 
        array(
            'priority' => 2,
        )
    );
}
```

同样在注册回调函数的时候，也可以用事先定义好的函数或者是类方法，就跟 `call_user_func()` 和 `call_user_func_array()` 的第一个参数一样，详细的代码我会在最后列出。

除了使用函数之外，还可以通过实现 `CakeEventListener` 接口来注册回调函数。下面就是一个例子。

``` php
class ProfileListener implements CakeEventListener {
    public function implementedEvents() {
        return array(
            'Controller.events.index' => array(
                'callable' => 'profile', // 把 profile 方法注册为 Controller.events.index 这个事件的回调函数
                'priority' => -2,
            ),
        );
    }

    public function profile($event) {
        pr('In an class listener.');
        pr($event->data);
        $event->result[__METHOD__] = 'class method';
    }
}
```

在进行注册的时候，直接调用：

``` php
$this->getEventManager()->attach(new ProfileListener());
```

从上面的代码可以看出，实现 `CakeEventListener` 接口可以为多个事件回调函数，只需要实现 `implementedEvents()` 方法并在返回的数组中指明对应的调用关系即可。为了方便代码的维护，以后最好使用这种方法进行回调函数的注册。

当然，事件是有返回值的。前面提到的 `passParams` 设置为 `true` 的情况下，回调函数的返回值就是事件的返回值，通过 `$event->result` 来获取，但是这样以来有个问题——如果我们注册了很多回调函数并且这些回调函数都有返回值的话，`$event->result` 这个值总是会被最后一个回调函数的返回值给覆盖掉，这样一来我们就无法知道所有回调函数的运行结果了。所以，我才会在前面并不推荐把 `passParams` 设置为 `true`，而是在上面的代码里使用了类似 `$event->result[__METHOD__] = 'class method';` 这样的方法来获取每一个回调函数的结果。

关于 CakePHP 的事件机制，先研究了这么多。想要了解其中更深奥的内容，最好的办法就是阅读开源代码—— <https://github.com/cakephp/debug_kit> ，这个 CakePHP 的 Debug 工具就用到了事件机制，非常值得学习。

除此之外，Zend Framework 2 中也实现了事件机制，我个人并不是太喜欢 ZF1 ，觉得它作为一个 MVC 框架来说太重太复杂，但是作为一个类库是非常棒的， ZF2 我还没有研究过，希望将来能有机会学习了解并用上它。关于 ZF2 的事件机制，可以从官方文档查阅—— <http://framework.zend.com/manual/2.2/en/modules/zend.event-manager.event-manager.html>

Have a nice day！

最后，是这篇文章整理过的源代码。

{% include_code cakephp-event/example.php %}