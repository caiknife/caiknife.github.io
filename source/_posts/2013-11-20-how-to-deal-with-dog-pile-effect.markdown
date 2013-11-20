---
layout: post
title: "如何处理 Dog Pile Effect"
date: 2013-11-20 16:02
comments: true
categories: memcache php redis
---
什么是 Dog Pile Effect？

在缓存系统中，缓存总有失效的时候，比如我们经常使用的 Memcache 和 Redis ，都会设置超时时间；而一旦缓存到了超时时间失效之后，如果此时再有大量的并发向数据库发起请求，就会造成服务器卡顿甚至是系统当机。这就是 Dog Pile Effect 。

<!-- more -->

{% fancybox /downloads/image/dog-pile/dog-pile.png Dog Pile Effect %}

参考下面的代码：

``` php
<?php
$mc = new Memcache();
$mc->addServers(array(
    array('127.0.0.1', 11211, 40),
    array('127.0.0.1', 11212, 30),
    array('127.0.0.1', 11213, 30)
));
$data = $mc->get('cached_key');
if ($mc->getResultCode() === Memcached::RES_NOTFOUND) {
    $data = generateData(); // long-running process
    $mc->set('cached_key', $data, time() + 30);
}
var_dump($data);
```

如果第 10 行代码需要执行 1 秒钟，而这个时间上正好缓存失效，系统又正好碰到访问高峰，比如 1000 RPS ，这样在生成缓存之前，所有的请求都会直接访问到数据库服务器上，导致系统故障。

避免这样的 Dog Pile 效应，通常有两种方法：

###使用独立的更新进程

使用独立的进程（比如:cron job）去更新缓存，而不是让 web 服务器即时更新数据缓存。举个例子：一个数据统计需要每五分钟更新一次（但是每次计算过程耗时1分钟）,那么可以使用 cron job 去计算这个数据，并更新缓存。这样的话，数据永远都会存在，即使不存在也不用担心产生 Dog Pile 效应，因为客户端没有更新缓存的操作。这种方法适合不需要即时运算的全局数据。但对用户对象、朋友列表、评论之类的就不太适用。

###使用“锁”

除了使用独立的更新进程之外，我们也可以通过加“锁”，每次只允许一个客户端请求去更新缓存，以避免 Dog Pile 效应。

处理过程大概是这样的：

A 请求的缓存没命中  
A 请求“锁住”缓存 key  
B 请求的缓存没命中  
B 请求需要等待直到“锁”释放  
A 请求完成，并且释放“锁”  
B 请求缓存命中（由于 A 的运算）  

下面的代码就用到“锁”来处理：

``` php
<?php
function get($key) {
    global $mc;

    $data = $mc->get($key);
    // check if cache exists
    if ($mc->getResultCode() === Memcached::RES_SUCCESS) {
        return $data;
    }

    // add locking
    $mc->add('lock:' . $key, 'locked', 20);
    if ($mc->getResultCode() === Memcached::RES_SUCCESS) {
        $data = generateData();
        $mc->set($key, $data, 30);
    } else {
        while(1) {
            usleep(500000);
            $data = $mc->get($key);
            if ($data !== false){
                break;
            }
        }
    }
    return $data;
}

$data = get('cached_key');

var_dump($data);
```

打完收工，Have a nice day！