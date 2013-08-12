---
layout: post
title: "Ubuntu切换系统默认命令"
date: 2013-08-12 23:01
comments: true
categories: ubuntu linux trick
---
在Ubuntu中由于环境需要，可能需要共存某个软件的多个版本，比如Python2和Python3，以及Ruby1.8和Ruby1.9。这两者有virtualenv和homebrew可以用来做版本的管理，不过从Ubuntu系统层面上，有一个命令可以直接切换软件的版本——update-alternatives。

命令用起来很简单。

``` bash
sudo update-alternatives --config command
```

<!-- more -->

比如现在机器上已经有ruby1.8和ruby1.9，进行版本切换就可以输入下面的命令：

``` bash
sudo update-alternatives --config ruby

有 2 个候选项可用于替换 ruby (提供 /usr/bin/ruby)。

  选择       路径              优先级  状态
------------------------------------------------------------
  0            /usr/bin/ruby1.8     50        自动模式
  1            /usr/bin/ruby1.8     50        手动模式
* 2            /usr/bin/ruby1.9.1   10        手动模式

要维持当前值[*]请按回车键，或者键入选择的编号：
```
输入数字即可选择默认的版本号，Octopress要求使用Ruby1.9，那就选择它好了。


