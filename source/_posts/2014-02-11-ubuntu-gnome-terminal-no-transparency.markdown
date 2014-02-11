---
layout: post
title: "Ubuntu 下 Gnome 终端出现假透明"
date: 2014-02-11 14:28:56 +0800
comments: true
categories: ubuntu linux
---
某次更新过 Ubuntu 之后，Gnome 终端就从真透明变成了假透明，使用起来非常不方便。

今天，从 <http://askubuntu.com/questions/266533/gnome-terminal-transparency-with-gnome-classic-no-effects> 找到了解决问题的方法。

<!-- more -->

按照图中的方法修改一下配置，终端又变回真透明了。

{% fancybox /downloads/image/terminal/config.png Config %}

下面是之前的假透明：

{% fancybox /downloads/image/terminal/faketrans.png Fake Transparency %}

修改之后回到了真透明：

{% fancybox /downloads/image/terminal/truetrans.png True Transparency %}

Have a nice day！