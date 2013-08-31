---
layout: post
title: "一句话Shell脚本（2）"
date: 2013-08-22 21:04
comments: true
categories: shell linux script
---
昨天玩octopress时出了问题，没有动任何东西的情况下，系统默认的ruby版本变成了1.8，导致本地的octopress既不能提交也不能本地预览。后来分别卸载了ruby1.8和ruby1.9，启用了新的PPA之后，重新安装ruby的两个版本，并通过update-alternatives把ruby版本默认设置成1.9。

可惜octopress还是无法启动，出现了<http://stackoverflow.com/questions/13778858/octopress-errors-rake-preview-watch-or-generate>和<http://stackoverflow.com/questions/16517144/why-the-pygments-code-rb-plugin-is-breaking>中提到的问题。百思不得其解啊，为什么ruby版本明明是1.9，调用的gem库却是1.8下的呢？

<!-- more -->

后来想到脚本文件在开头都会有`#!/usr/bin/env ruby`之类的注释，用来标注脚本文件的解释器，那么这个问题的原因是不是就是因为某些应该使用ruby1.9进行解释的文件被写死成ruby1.8解释了呢？那么应该如何找到这些文件呢？

可以用find来查找，下面是适合的shell命令：

``` bash
$ find / -type f -name "*.rb" -exec grep -m 1 "ruby1.8" {} + 2>/dev/null | awk -F ":" '{print $1}'
```

上面这条命令的含义：

``` bash
$ find / \ # 从根目录开始查找
    -type f \ # 查找文件
    -name "*.rb" \ # ruby文件
    -exec grep -m 1 "ruby1.8" {} + \ # 找到文件内容里匹配ruby1.8一次的所有文件，和匹配内容一起输出 
    2>/dev/null \ # 错误信息丢弃
    | \ # 管道
    awk -F ":" '{print $1}' \ # 分割符是:，输出第一列
```

如此一来就能找到所有还默认以ruby1.8解释的文件了，然后修改一下就行。现在我的octopress又活过来了。

Have a nice day！