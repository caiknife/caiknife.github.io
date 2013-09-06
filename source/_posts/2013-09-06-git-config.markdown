---
layout: post
title: "git 设置"
date: 2013-09-06 21:09
comments: true
categories: git linux ubuntu
---
花了两天时间，看了关于 git 的资料，稍微整理一下，挖个坑先。

首先在 ubuntu 上更新 git 的源。

```
$ sudo apt-add-repository ppa:git-core/ppa

$ sudo update

$ sudo apt-get install git
```

<!-- more -->

安装好后， shell 下的 git 提示都变成了中文，很方便。然后再设置 git 命令的别名。

```
$ git config --global alias.st status

$ git config --global alias.ci commit

$ git config --global alias.df diff

$ git config --global alias.co checkout

$ git config --global alias.br branch

$ git config --global alias.hist "log --pretty=format:\"%h %ad | %s%d [%an]\" --graph --date=short"

$ git config --global alias.type "cat-file -t"

$ git config --global alias.dump "cat-file -p"
```

下面是一些学习 git 的参考资料：

* <http://gitref.cyj.me/zh/about.html>  
* <http://gitbook.liuhui998.com/>  
* <http://git-scm.com/book/zh>  
* <http://www.worldhello.net/gotgithub/index.html>

前几天 bootstrap 的坑还没填，今天又挖了个 git 的坑，真是坑爹。

抓紧时间一点点填吧。

Have a nice day！
