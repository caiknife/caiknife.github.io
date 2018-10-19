---
layout: post
title: "composer问题排查"
date: 2018-10-19 14:48:38 +0800
comments: true
categories: php composer
---
今天更新 composer 库的时候出现了一个问题：

{% fancybox /downloads/image/composer/1.png Composer Issue %}

图不是我的电脑出现的问题，是搜到的图片，我们碰到了一样的问题。

<!-- more -->

查了半天没找到原因是什么，后来在查当前代码库的时候，composer.json 文件里的配置是这样的：

``` json
{
  "repositories": {
    "packagist": {
      "type": "composer",
      "url": "packagist.phpcomposer.com"
    }
  }
}
```

搜索了问题之后，发现 packagist.phpcomposer.com 这个景象已经很久没有更新了，所以就造成了目前的问题。只需要把仓库换个镜像源就可以了。

用下面的命令将镜像地址替换为 https://packagist.laravel-china.org 即可。

``` bash
composer config -g repo.packagist composer https://packagist.laravel-china.org

composer config repo.packagist composer https://packagist.laravel-china.org
```

Problem resolved.
