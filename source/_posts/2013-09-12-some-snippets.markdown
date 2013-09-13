---
layout: post
title: "一点废话"
date: 2013-09-12 23:25
comments: true
categories: cakephp linux bootstrap
---
有几天没写博客了，最近这几天一直在看文档学习。 PHP 、Python 、Ruby 的各种微框架都在看—— Flask 、Sinatra 、 Padrino 、Silex 、Slim 、 Limonade 、 Flight，还有一个最近比较火的 Laravel 4 。看了这么多，却不知道自己能用上的能有多少，如此多的微框架，真正用起来的时候，还是要自己造轮子。用了很多年的 CakePHP ，还是感觉这个东西最好用，估计我这一辈子就和 CakePHP 一起活了，呵呵。

<!-- more -->

前两天把 Bootstrap 速查卡更新了一下，优化了一些东西，在老地址访问即可，旧版本直接覆盖，没有存档。过去的东西就应该被遗忘吧，呵呵。

今天扫了一下前端的东西，发现两个比较好玩的， PhantomJS 和 CasperJS 。这两个结合起来是前端自动化测试的强力工具啊，在当前 NodeJS 开始火爆起来的情况下，用好这两个，能做点有意思的事情。

``` js+php phantom.js
var page = require('webpage').create(),
    system = require('system'),
    t = (new Date()).toLocaleString(),
    address;

if (system.args.length === 1) {
    console.log("Usage:phantomjs p.js <url>");
    phantom.exit();
}   
  
address = system.args[1];

page.open(address, function() {
    page.render(t + '.png');
    phantom.exit();
});
```

上面这段代码运行在 PhantomJS 里，就能从命令行读取一个 URL ，然后截图显示了，超级棒啊。而 CasperJS 更是可以当成爬虫一样来用。

``` js+php casper.js
var casper = require('casper').create();

casper.start('http://casperjs.org/', function() {
    this.echo(this.getTitle());
});

casper.thenOpen('http://phantomjs.org', function() {
    this.echo(this.getTitle());
});

casper.run();
```

实在是太神奇了，我和我的小伙伴都惊呆了。

现在真的感觉`学海无涯`这四个字是什么意思了。

然后又理解了这句话的意思——`吾生也有涯，而知也无涯，以有涯随无涯，殆已。`

Have a nice day！

最后再补充一点，在 Octopress 里面用代码高亮的时候，有时候会报错：

``` bash
pygments_code.rb:14:in `highlight': undefined method `[]' for nil:NilClass (NoMethodError)
```

过去一直没找到原因，今天发现是在指定语言类型的时候用错了。比如，这片文章里的代码是 JavaScript ，但是代码块声明语言类型的时候不能写成下面这个样子 `js` ，正确的格式是 `js+php` 。

详细的设置请看：<http://pygments.org/docs/lexers/>
