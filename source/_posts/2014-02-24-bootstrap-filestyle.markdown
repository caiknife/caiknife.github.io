---
layout: post
title: "BootStrap 的文件上传控件"
date: 2014-02-24 10:21:34 +0800
comments: true
categories: bootstrap js
---
`input[type=file]` 的这个控件，在不同的浏览器上表现出的样式不同，尤其是在 Chrome 上，完全和 FF 、IE 的天差地别。为了让这个控件在不同的浏览器表现出统一的样式，需要有一个优秀的解决方案。

<!-- more -->

周末在家里做东西的时候，由于后台使用了 BootStrap 来做，于是就想找一个类似的插件来修改，在 Google 上搜了一下，找到了下面这个插件：

<http://markusslima.github.io/bootstrap-filestyle/>

导入该插件的库文件之后，

``` js+php
<script type="text/javascript" src="js/bootstrap-filestyle.min.js"></script>
```

配置起来异常简单：

``` js+php
$(":file").filestyle();
```

当然如果只是想为单个控件做样式修改的话，可以通过 HTML5 的 data API 来完成：

``` js+php
<input type="file" class="filestyle" data-classButton="btn btn-primary" data-input="false">
```

这个插件还有一些配置选项和 API 可以满足各种定制，具体的内容可以查看源文档。

BootStrap 真是好东西，但是用多了之后，会不会有审美疲劳？

Have a nice day！ 