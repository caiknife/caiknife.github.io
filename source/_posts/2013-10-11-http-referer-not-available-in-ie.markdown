---
layout: post
title: "IE 下获取 HTTP_REFERER 无效"
date: 2013-10-11 15:19
comments: true
categories: work browser 
---
前段时间 SMKR 上线了一个 coupon 频道，用的是 CMUS 的 coupon 数据，用户点击一个 coupon 之后，会先跳转到 CMUS 站点然后再跳转到商家站点。这样在 SMKR 站点上就会记录一个 `Affiliate Outgoing Log`，在 CMUS 站点上记录一个 `Offer Outgoing Log`。

今天收到一个问题，在 Oracle 的报表中，我发现 IE 浏览器下产生的 `Offer Outgoing Log` 文件里，所有的 `HTTP_REFERER` 字段都为空。

Here comes the bug， what can we do？

<!-- more -->

动用万能的 Google 和 StackOverflow，在这两大神器的帮助下，一点点的，问题开始明朗起来，就好像无尽的黑暗之中，亮起了一盏小桔灯，哈哈哈。

首先， SMKR 上的这次跳转有个中间页面，在这个中间页面上，会停顿两秒钟后，再跳转到 CMUS 上。而这次跳转，用到的是 `<meta http-equiv="refresh" />` 这个方式：

``` html
<meta http-equiv="refresh" content="2; url=http://www.smarter.co.kr/track/scripts/redir.php?bt=YWZmaWxpYXRl&ds=Q1BB&ch=1&pi=668114&mi=549&du=aHR0cDovL3d3dy5jb3Vwb25tb3VudGFpbi5jb20vc3RhdHMvcmVkaXIucGhwP3JlcXVlc3RpZD05ZWQxZTFkZDc0OWM1N2ZjMmRhZGQ5NzdiNzFmZGI4OCZmcm9tPXNta3ImbT01NDkmcD02NjgxMTQmZHM9Q1BB" /> 
```

而在现代浏览器中，除了使用 webkit 内核的 Chrome 和 Safari 之外，使用这种方式进行跳转的话，浏览器是不会在请求头信息里面添加 `HTTP_REFERER` 的。只能通过 a 标签点击， form 提交等方法进行跳转，而 `location.href` 这个方法在 IE 下无效，在 FireFox 里有效，不过为了兼容所有的浏览器，还是不推荐使用这个方法。

下面是在解决这个问题的过程中，找到的一些参考资料：

<http://stackoverflow.com/questions/2985579/does-http-equiv-refresh-keep-referrer-info-and-metadata>  
<http://stackoverflow.com/questions/5643773/http-referer-not-always-being-passed>  
<http://leeon.me/a/http-refer-issue>  
<http://www.blueisme.com/?p=1759>

剩下的事情，就是修改跳转的方式，很简单，Just do it！

Have a nice day！