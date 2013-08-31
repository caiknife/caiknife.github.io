---
layout: post
title: "apt-get install error"
date: 2013-08-29 13:59
comments: true
categories: ubuntu linux bug apt-get error
---
最近不知道为何，在更新 Ubuntu 内核时总是报错：

```
Failed to process /etc/kernel/postinst.d at /var/lib/dpkg/info/linux-image-3.2.0-52-generic.postinst line 1010
```

Google 了一下，是以前使用 apt-get 卸载软件时不完全引起的，解决方法是：

``` bash
$ cd /var/lib/dpkg
$ mv info infobak
$ mkdir info
$ dpkg --configure -a
$ apt-get -f install
```

困扰了我多年的顽疾终于得到解决了，妈蛋！

Have a nice day！

<!-- more -->

--------2013年8月30日补充--------

执行了上面的操作后，再次用apt-get安装程序的时候，出现了多处类似下面的报错：

```
dpkg：警告：无法找到软件包 python-pkg-resources 的文件名列表文件，现假定该软件包目前没有任何文件被安装在系统里。
```

原因就是我修改了 `/var/lib/dpkg/info` 的名称，变成 `/var/lib/dpkg/infobak` ，在这里保存了所有软件包的配置信息。而现在的 `/var/lib/dpkg/info` 文件夹里是空的，之前安装的所有软件包的信息都丢失了，所以就报错。现在只需要把目录名称修改回来即可。

如果不小心删除了 `/var/lib/dpkg/info` ，可以通过[Debian/Ubuntu删除/var/lib/dpkg/info后的补救方法](http://lx.shellcodes.org/show/507d6fed04b91e46db000002.html)里介绍的方法重新安装所有软件包。一定要养成备份的好习惯，不要随便删除文件，一失足成千古很！

Have a nice day！
