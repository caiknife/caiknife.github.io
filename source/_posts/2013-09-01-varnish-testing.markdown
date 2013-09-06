---
layout: post
title: "varnish 测试（2）"
date: 2013-09-01 23:27
comments: true
categories: varnish linux test
---
继续上次的话题。

### 加上了反向代理之后如何获取客户端 IP ？

由于 varnish 是处于 web 服务器之前的位置，所以 varnish 实际上可以看作是一个 reverse proxy 的服务器。后端的代码要获取客户端 IP 地址的话，如果不做设置，获取的实际上是 varnish 服务器的 IP 地址。比如在 PHP 中经常使用到的 `$_SERVER['REMOTE_ADDR']` 这个变量，如果没有 varnish 做代理，就是正确的客户端 IP ;如果在 varnish 之后使用的话，只能获得 varnish 服务器的 IP 地址。所以，我们得对 varnish 做一点特殊的处理。

还好 varnish 的配置文件里已经给我们默认写出了这段代码，我们只需要在自己的代码中 copy 过来即可。直接在 `vcl_recv` 这里补充就行。

<!-- more -->

```
sub vcl_recv {
    if (req.restarts == 0) { # 不是重启转发
        if (req.http.x-forwarded-for) { # 请求头里有 X-Forwarded-For 字段，就追加一个 IP 
            set req.http.X-Forwarded-For = req.http.X-Forwarded-For + ", " + client.ip;
        } else { # 请求头里没有有 X-Forwarded-For 字段，就设置 IP  
            set req.http.X-Forwarded-For = client.ip;
        }
    }
    if (req.request == "POST") { # POST 请求穿透
        return (pass);
    }
    return (lookup); # 直接查询缓存
}
```

写一个phpinfo()的页面，分别访问没通过 varnish 和通过 varnish 做处理的页面，就能看到区别了——用 varnish 处理过的页面，就多了 `$_SERVER["HTTP_X_FORWARDED_FOR"]  127.0.0.1` 这个变量。当然我是在本机同时做了代理和 web 服务，所以这个变量是本地 IP 地址；如果把这两个服务放在不同的服务器上，后端服务器的 PHP 代码通过访问 `$_SERVER["HTTP_X_FORWARDED_FOR"]` 这个变量是可以得到客户端的正确 IP 地址。

### 如何清除缓存？

建立缓存已经成功了，但是还得会清除缓存。但是要清除缓存不能每次都 `sudo service varnish restart` 吧？这方法实在是太笨了。在 varnish 的命令行里，可以通过正则表达式来清除缓存。

因为我机器上的 varnish 启动参数默认带了 `-S` ，所以这里只能通过超级用户进行管理，首先进入命令行：

```
$ sudo varnishadm

200        
-----------------------------
Varnish Cache CLI 1.0
-----------------------------
Linux,3.2.0-52-generic,x86_64,-smalloc,-smalloc,-hcritbit

Type 'help' for command list.
Type 'quit' to close CLI session.

varnish> 
```

输入 `help` ，看看我们能使用哪些命令？

```
varnish> help
200        
help [command]
ping [timestamp]
auth response
quit
banner
status
start
stop
vcl.load <configname> <filename>
vcl.inline <configname> <quoted_VCLstring>
vcl.use <configname>
vcl.discard <configname>
vcl.list
vcl.show <configname>
param.show [-l] [<param>]
param.set <param> <value>
panic.show
panic.clear
storage.list
ban.url <regexp>
ban <field> <operator> <arg> [&& <field> <oper> <arg>]...
ban.list
```

一般情况下，清除缓存只需要用到 `ban.url` 这个命令就可以了。

```
varnish> ban.url .(css|js|jpg|jpeg|png)$ # 清除各种静态文件的缓存
200   

varnish> ban.url ^/caketest # 清除 /caketest 开头 URL 的文件缓存
200  
```

`ban.url` 这个命令是不能判断请求域名的，只能根据 URL 来进行正则匹配。所以需要根据域名来清除缓存的时候，就需要用到 `ban` 这个命令了。

```
varnish> ban req.http.host == "example.com" && req.url ~ "\.png$" 
# 清除 example.com 域名下的 png 文件缓存
```

上面这条命令来自 varnish 的手册<https://www.varnish-cache.org/docs/3.0/tutorial/purging.html>，但是在我的机器上运行报了语法错误，是反斜杠的问题。是转义的问题还是 varnish 的正则表达式不支持 `.` 呢？看来还得再研究研究了。

时候不早，早点休息。每天进步一小点，长久下去就能有很大的收获。

Have a nice day！
