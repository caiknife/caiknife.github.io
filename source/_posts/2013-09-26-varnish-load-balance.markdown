---
layout: post
title: "varnish 负载均衡"
date: 2013-09-26 22:18
comments: true
categories: varnish linux
---
忙了一段时间，总算又有空写总结了。今天总结一下用 varnish 做负载均衡。

常用的负载均衡方式有 `LVS` 、`Nginx` 、`HAProxy` 等等，其实 varnish 作为一个反向代理软件，也可以起到负载均衡的作用。在 varnish 的官方 wiki 上有详细的介绍：<https://www.varnish-cache.org/trac/wiki/LoadBalancing> 。我们来看看如何设置。

<!-- more -->

先设置好后端的几台服务器：

```
#设置探针，健康检查
probe health {
    .url = "/"; # 探针侦测的 URL 
    .timeout = 60ms; # 超时设置
    .interval = 2s; # 探针间隔
    .window = 5; # 每次查询维持 5 个 slide window
    .threshold = 3; # 至少有 3 个 slide window 正常，则表示 backend 健康
}

#后端设置，暂时设置两台
backend web01 {
    .host = "192.168.0.1";
    .port = "80";
    .probe = health;
}

backend web02 {
    .host = "192.168.0.2";
    .port = "80";
    .probe = health;
}
```

varnish 常用的负载均衡方法有 `random` 、`round-robin` 和 `client` ，它们各自的设置如下：

```
# random
# 根据 weight 每次查询优先访问权重高的服务器。
# 而在权重高的服务器不空闲的情况下，才会访问权低的服务器。
director drand random {
    { 
        .backend = web01;
        .weight = 2;
    }
    { 
        .backend = web02;
        .weight = 1;
    }
}

# round-robin
# 轮流访问后端服务器
director drr round-robin {
    { 
        .backend = web01;
    }
    { 
        .backend = web02;
    }
}

# client
# 根据 client.identity 来确认应该访问哪台服务器。
director dcli client {
    { 
        .backend = web01;
        .weight = 1;
    }
    { 
        .backend = web02;
        .weight = 1;
    }
}
```

设置好 director 之后，就可以直接在 vcl_recv 中指定负载均衡的策略了。

```
sub vcl_recv {
    # random load balance
    set req.backend = drandom;


    # rr load balance
    set req.backend = drr;


    # client load balance
    set req.backend = dcli;

    # 根据 client.identity 来决定该访问哪台服务器
    # load balance by IP, default
    set client.identity = client.ip;

    # load balance by URL
    set client.identity = req.url;

    # load balance by User Agent
    set client.identity = req.http.user-agent;
}
```

当然最常用的负载均衡策略就是 `round-robin` ，而除此之外还有 `hash` 、`dns` 、`fallback` 几种不同的策略，它们的思路都差不多类似，在此不再详述了。

Nginx 和 varnish 一样都有反向代理和负载均衡的功能，看来也值得了解学习一下。下一个目标—— Nginx！

Have a nice day！