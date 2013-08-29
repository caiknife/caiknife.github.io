---
layout: post
title: "apt-get install error"
date: 2013-08-29 13:59
comments: true
categories: ubuntu linux bug apt-get error
---
最近不知道为何，在更新Ubuntu内核时总是报错：

```
Failed to process /etc/kernel/postinst.d at /var/lib/dpkg/info/linux-image-3.2.0-52-generic.postinst line 1010
```

Google了一下，是以前使用apt-get卸载软件时不完全引起的，解决方法是：

``` bash
$ cd /var/lib/dpkg
$ mv info infobak
$ mkdir info
$ dpkg --configure -a
$ apt-get install -f
```

困扰了我多年的顽疾终于得到解决了，妈蛋！

Have a nice day！
