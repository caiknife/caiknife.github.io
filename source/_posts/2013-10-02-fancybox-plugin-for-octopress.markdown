---
layout: post
title: "给博客写了个 fancybox 的插件"
date: 2013-10-02 17:37
comments: true
categories: jquery ruby octopress
---
octopress 默认没有放大图片的效果，今天用 fancybox 做了一个插件扩展。

首先从 <http://fancyapps.com/fancybox/#license> 下载最新版的 fancybox ，解压缩后复制 source 目录到 octopress 的 javascripts 目录下。

打开文件 `/source/_includes/custom/head.html` ，在最后增加一条语句：

``` erb /source/_includes/custom/head.html
{% raw %}{% include fancybox.html %}{% endraw %}
```

<!-- more -->

然后建立文件 `/source/_includes/fancybox.html` ，加入以下内容：

``` html /source/_includes/fancybox.html
<script src="/javascripts/fancybox/jquery.fancybox.pack.js"></script>
<link rel="stylesheet" type="text/css" href="/javascripts/fancybox/jquery.fancybox.css" />
<script type="text/javascript">
(function($){
    $('.fancybox').fancybox();
})(jQuery);
</script>
```

下面的重头戏就是增加一个 `fancybox` 的标签。创建文件 `/plugins/fancybox_tag.rb` ，详细代码如下：

``` ruby /plugins/fancybox_tag.rb
#coding: utf-8

module Jekyll

  # Usage:
  # {% raw %}{% fancybox @filename [thumb:@thumb] [@title] %}{% endraw %}
  # {% raw %}{% fancybox @filename [@title] %}{% endraw %}

  class FancyboxTag < Liquid::Tag
    def initialize(tag_name, markup, tokens)
      #  /(?<filename>\S+)(?:\s+(?<thumb>\S+))?(?:\s+(?<title>.+))?/i
      #  /(?<filename>\S+)(?:\s+(?<title>.+))?/i
      if /(?<filename>\S+)(?:\s+thumb:(?<thumb>\S+))?(?:\s+(?<title>.+))?/i =~ markup
        @filename = filename
        @thumb = thumb
        @title = title
      end
    end

    def render(context)
      if @filename
        "<a href=\"#{@filename}\" title=\"#{@title}\" class=\"fancybox\"><img src=\"#{thumb_for(@filename, @thumb)}\" alt=\"#{@title}\" /></a>"
      else
        "Error. Usage: {% raw %}{% fancybox @filename [thumb:@thumb] [@title] %}{% endraw %}"
      end
    end

    def thumb_for(filename, thumb)
      if thumb.nil?
        filename
      else
        thumb
      end
    end

  end
  
end

Liquid::Template.register_tag('fancybox', Jekyll::FancyboxTag)
```

使用方法在注释里有介绍，很简单。

Have a nice day！
