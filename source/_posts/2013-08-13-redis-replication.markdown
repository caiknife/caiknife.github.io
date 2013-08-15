---
layout: post
title: "redis主从复制"
date: 2013-08-13 23:54
comments: true
categories: redis linux
---
晚上在家尝试做了一下redis的主从复制。下面的Linux命令有一部分需要sudo才能完成。

首先复制一份redis.conf：

``` bash
cp /etc/redis/redis.conf /etc/redis/slave.conf
```

有几处地方需要修改：

``` bash
vi /etc/redis/slave.conf

port 6380
logfile /var/log/redis/redis-server-slave.log
dbfilename slave.rdb
slaveof 127.0.0.1 6379
```

<!-- more -->

设置开机启动：

``` bash
cp /etc/init.d/redis-server /etc/init.d/redis-server-slave
```

修改启动项：

{% include_code redis-replication/redis-server-slave %}

最后执行：

``` bash
service redis-server-slave start
```

最后确认同步是否成功：

``` bash
cd /var/lib/redis
md5sum *.rdb
```

如果checksum值是相同的，则表示同步成功。

把redis-server-slave设置为开机启动：

``` bash
update-rc.d redis-server-slave defaults
```

如果要取消开机启动：

``` bash
update-rc.d -f redis-server-slave remove
```

配置文件redis.conf里有一部分和save相关的参数，缺省如下：

``` bash
# Save the DB on disk:
#
#   save <seconds> <changes>
#
#   Will save the DB if both the given number of seconds and the given
#   number of write operations against the DB occurred.
#
#   In the example below the behaviour will be to save:
#   after 900 sec (15 min) if at least 1 key changed
#   after 300 sec (5 min) if at least 10 keys changed
#   after 60 sec if at least 10000 keys changed
save 900 1
save 300 10
save 60 10000
```

在主服务器上，我们可以去掉上面的设置，改成类似下面的设置（只要参数值够大即可）：

``` bash
save 10000000000 10000000000
```

如此一来主服务器变成一个完全的内存服务器，所有的操作都在内存里完成，“永远”不会再往磁盘上持久化保存数据，异步的也没有。持久化则通过从服务器来完成，这样在操作主服务器的时候效率会更高。不过要注意的一点是此方法不适合保存关键数据，否则一旦主服务器挂掉，如果你头脑一热简单的重启服务，那么从服务器的数据也会跟着消失，此时，必须拷贝一份备份数据到主服务器，然后再重启服务才可以，数据的恢复稍显麻烦。

从服务器也可以通过设置这个参数来调整从内存同步到磁盘的频率。