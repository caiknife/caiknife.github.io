---
layout: post
title: "CLI 环境高级配置"
date: 2018-11-14 12:40:07 +0800
comments: true
categories: bash linux macos cli
---
最近又发现了几个在 CLI 环境下，对几个已有命令的强化，可以提高用户体验，现在来总结一下。

<!-- more -->

## bat

安装方式 `brew install bat`

这是一个取代 `cat` 的命令。通常情况下 `cat` 只显示普通文本的内容，而 `bat` 还会根据文件的扩展名高亮显示语法。效果如下图：

{% fancybox /downloads/image/cli/bat.gif bat %}

## prettyping

安装方式 `brew install prettyping`

这是一个取代 `ping` 的命令。可以更加直观地显示 ping 的进度。

{% fancybox /downloads/image/cli/ping.gif prettyping %}

可以用别名来替换已有的 `ping` 命令：`alias ping='prettyping --nolegend'`

## fd

安装方式 `brew install fd`

这个命令自然是用来取代 `find` 。

{% fancybox /downloads/image/cli/fd.png fd %}

下面是一些常用的例子：

``` bash
fd cli # all filenames containing "cli"
fd -e md # all with .md extension
fd cli -x wc -w # find "cli" and run `wc -w` on each file
```

## ncdu

安装方式 `brew install ncdu`

这个命令可以取代 `du` 。而且还可以在界面内进行操作，通过操作光标进入不同的目录查看文件大小。

{% fancybox /downloads/image/cli/ncdu.png ncdu %}

别名配置可以写成：`alias du="ncdu --color dark -rr -x --exclude .git --exclude node_modules"`

-------

今天就先到这里，下回再继续吧。
