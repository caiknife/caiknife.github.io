---
layout: post
title: "使用Python脚本抓取早期知乎页面里的图片"
date: 2018-11-15 11:38:54 +0800
comments: true
categories: python zhihu 爬虫 crawler
---
很久很久以前，我在知乎看到了一个问题[长得好看，但没有男朋友是怎样的体验？](https://www.zhihu.com/question/37709992)。

出于男性本能的需求，我就花了一个中午的时间写了个脚本，专门用于抓取知乎问题页面下的图片，然后就把我的代码写到了这个问题的[答案](https://www.zhihu.com/question/37709992/answer/121184589)里。接着，又过了几天——我的答案被折叠了……

<!-- more -->

使用的 `Python` 版本是 2.7 ，用到的库是 `requests` 和 `pyquery` 。

Python 源码如下：

{% include_code zhihu/dl_with_names.py %}

使用方法很简单：

``` bash
python dl_with_names.py https://www.zhihu.com/question/37709992
```

嗯，由于知乎页面改版了，所以现在并没有办法再下载了…… 只能再另外通过解决登录态的问题，看到问题页之后再想办法抓取图片。

上面这种方法适合有一定命令行基础的人群，对于普通人来说，更简单的方法是使用 `javascript` 。

{% include_code zhihu/dl.js %}

先在地址栏里输入 `javascript:` ， 再把上面的 JS 代码复制到地址栏里，回车之后就会在页面顶端出现问题页之内图片的链接。

不过由于现在知乎已经不用 `jQuery` 了，所以这段代码也失效了……

那还是写篇文章纪念一下吧。
