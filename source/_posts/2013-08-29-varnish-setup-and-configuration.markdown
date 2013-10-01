---
layout: post
title: "varnish 的安装和配置"
date: 2013-08-29 21:47
comments: true
categories: varnish linux ubuntu
---
在 Ubuntu 下安装 varnish 很简单，直接用 apt-get 安装即可。

```
$ sudo apt-get install varnish

$ varnishd -V
varnishd (varnish-3.0.2 revision cbf1284)
Copyright (c) 2006 Verdens Gang AS
Copyright (c) 2006-2011 Varnish Software AS
```

默认版本是 3.0.2 ，挺新的，现在 varnish 的最新 dev 版本是 3.0.4 ，这个版本足够用了。

<!-- more -->

再来看看 varnish 的状态图。

{% img /downloads/image/varnish/120044934.jpg %}

一个请求发送到 varnish ，第一个状态是 recv ，然后根据不同的逻辑进入三种不同的模式： `pipe` 、 `pass` 和 `lookup` 。

**调用 pass 函数，从后端服务器调用数据。**  
**调用 pipe 函数，建立客户端和后端服务器之间的直接连接，从后端服务器调用数据。**  
**调用 lookup 函数，从缓存中查找应答数据并返回，如果查找不到，则调用 pass 函数从后端服务器调用数据。**

```
问： pass 和 pipe 都从后端服务器取数据，它们之间有什么不同呢？   

答：当 vcl_recv 调用 pass 函数时， pass 将当前请求直接转发到后端服务器。而后续的
请求仍然通过 varnish 处理。 例如，建立了 HTTP 连接之后，客户端顺序请求 a.css 、
a.png 两个文件，“当前请求”指的是第一个请求，即 a.css。 a.css 被直接转发到后端服务
器，不被缓存。而后续的 a.png 则再由 varnish 来做处理， varnish 会判断 a.png 如
何处理。 

总结：一个连接中除了当前请求，其它请求仍然按照正常情况由varnish处理。而 pipe 模式
则不一样，当 vcl_recv 判断需要调用 pipe 函数时， varnish 会在客户端和服务器之间
建立一条直接的连接，之后客户端的所有请求都直接发送给服务器，绕过 varnish , 不再由 
varnish 检查请求，直到连接断开。 
```

```
问：什么情况下用 pass ，什么情况下用 pipe 呢？  

答： pass 通常只处理静态页面。即只在 GET 和 HEAD 类型的请求中时才适合调用 pass 
函数。另外，需要注意的一点是， pass 模式不能处理 POST 请求，为什么呢？因为 POST 
请求一般是发送数据给服务器，需要服务器接收数据，并处理数据，反馈数据 。是动态的，不
作缓存。 

示例代码如下: 
if (req.request !="GET" && req.request != "HEAD") 
{               
    return (pipe);       
}       
那什么情况下用 pipe ？由以上陈述可以知，类型是 POST 时用 pipe ，但是也许还不太清晰。
举个例子，当客户端在请求一个视频文件时，或者一个大的文档，如.zip .tar 文件，就需要用 
pipe 模式，这些大的文件是不被缓存在 varnish 中的。 
```

在 varnish 的配置文件中，可以看到有一些默认的子过程，当然用户也可以定义自己的子过程。如果自定义的子过程没有 return 的话，在一次向 varnish 的请求中，会先执行用户自定义的子过程，然后再调用默认的子过程。在生产环境中，我们经常自定义的是 `vcl_recv` 和 `vcl_fetch` 两个子过程，这样一次请求可能就像下面一样： `vcl_recv(user)` -> `vcl_recv(default)` -> `vcl_pass(default)` -> `vcl_fetch(user)` -> `vcl_fetch(default)` 。而如果在子过程中使用了 return ，就会覆盖默认的子过程，只执行自定义的子过程。

下面是我自己的机器上做的一点配置：

```
backend default {
    .host = "127.0.0.1";
    .port = "80";
    .connect_timeout = 4s; # varnish 和 backend 连接的超时时间
    .first_byte_timeout = 5s; # backend 第一个字节到达 varnish 的超时时间
    .between_bytes_timeout = 20s; # 字节之间的超时时间
}

sub vcl_recv {
    set req.backend = default; # 设置 backend ，默认就是 default 。如果做了负载 director 的话，就写 director 的名字。
    # 在varnish的默认设置中，如果不是 GET 也不是 HEAD，就会直接进入 pass
    # 这里将所有请求发送到内存中进行查询
    return (lookup);
}

sub vcl_fetch {
    set beresp.do_esi = true; # 开启 esi ，类似 apache 的 server side include
    set beresp.ttl = 600s; # 缓存时间10分钟
    return (deliver);
}

sub vcl_deliver {
    set resp.http.X-Hits = obj.hits; # 添加响应头
    if (obj.hits > 0) {
        set resp.http.X-Cache = "HIT"; # 如果从 cache 中取到，标记为 HIT
    } else {
        set resp.http.X-Cache = "MISS"; # 否则就是 MISS
    }
    return (deliver);
}
```

据说 varnish 比 squid 的性能高很多，还是得在将来有机会能实际测试一下到底有多好。每天进步一小点！

Have a nice day！
