---
layout: post
title: "更新Ubuntu redis"
date: 2013-08-07 20:59
comments: true
categories: ubuntu linux redis
---
Ubuntu下的redis默认版本是2.2.x，现在需要更新到最新的版本2.6.14。不采用从源码编译的方式进行更新，直接更新PPA源进行升级。

``` bash
$ sudo apt-add-repository ppa:chris-lea/redis-server
$ sudo apt-get update
$ sudo apt-get install redis-server
```

<!-- more -->

参考资料：  
[How to find the latest version of redis-server in repos?](http://askubuntu.com/questions/88265/how-to-find-the-latest-version-of-redis-server-in-repos)  
[redis-server PPA](https://launchpad.net/~chris-lea/+archive/redis-server)  
[Redis on Ubuntu 12.04 (Precise Pangolin)](https://library.linode.com/databases/redis/ubuntu-12.04-precise-pangolin)  

如果要从源码编译redis，可以参考官方文档：[http://redis.io/download](http://redis.io/download)

Have a nice day！