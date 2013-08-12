---
layout: post
title: "曾经写过的Ruby脚本"
date: 2013-08-12 23:25
comments: true
categories: 
---
以前为了爬豆瓣和人人的美女图片，自己动手用Ruby写过一个爬虫。今天再来看看，几乎都看不懂了，晚上趁着休息时间把以前的代码温习温习，加上了注释，不过这代码已经证实在我的Ubuntu上是跑不动了。唉，开源软件版本更新得太快，以前的老代码放到现在都不能用了，真担心有一天自己也被淘汰，非常害怕啊。

代码很长，偶尔看一下，觉得自己还是能踏踏实实做一个苦逼的码农的，只是有时候人在江湖身不由己，你干的事情不一样是你自己喜欢干的。

<!-- more -->

{% include_code album_download/album_download.rb %}

{% include_code album_download/album.rb %}

{% include_code album_download/douban_album.rb %}

{% include_code album_download/renren_album.rb %}

用起来很简单，直接在命令行下输入：

``` bash
ruby album_download.rb [豆瓣相册链接或人人相册链接]
```

不过这段代码再也跑不起来了，写这篇博文做个纪念吧。

最后还是想说，我喜欢Python胜过Ruby，不知道自己这辈子还有没有机会当一个Python程序员。