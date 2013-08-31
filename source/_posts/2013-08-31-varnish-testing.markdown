---
layout: post
title: "varnish 测试（1）"
date: 2013-08-31 12:50
comments: true
categories: varnish linux test
---
前天安装配置了 varnish ，但是在生产环境下，还是有很多更复杂的情况会出现，有时候还是得多考虑一下可能会发生的问题，自己模拟一下，找到解决的办法。测试代码是用 CakePHP2.3.8 编写的，代码如下：

``` php
// controller
class TestsController extends AppController {
    public $uses = array();

    public $autoLayout = false;

    public function index() {
        $content = "This is a text!";
        $this->set("content", $content);
    }
}
```

``` html
// view
<div>
    <h1>Hello, world!</h1>
    <p><?= $content;?></p>
</div>
```

<!-- more -->

### ALWAYS PIPE

首先模拟的状态是所有请求直接进行 PIPE 转发，不再经过 varnish 进行处理。测试的 URL 是 `http://localhost:6081/caketest/tests/index` 。

```
sub vcl_recv {
    return (pipe); # 直接 pipe 转发
}
```

最后得到的 response header 如下：

```
# visit http://localhost:6081/caketest/tests/index
HTTP/1.1 200 OK
Date: Sat, 31 Aug 2013 14:49:10 GMT
Server: Apache/2.2.22 (Ubuntu)
X-Powered-By: PHP/5.3.10-1ubuntu3.7
Vary: Accept-Encoding
Content-Encoding: gzip
Content-Length: 74
Keep-Alive: timeout=5, max=100
Connection: Keep-Alive
Content-Type: text/html; charset=UTF-8
```

可以看到，这一次请求并没有经过 varnish 的处理， response header 中完全没有有关 varnish 的字段。

### NO PASS NO CACHE

现在模拟的状态是所有请求一律不 PASS ，所有 fetch 到的后端数据一律不缓存，对应的 VCL 如下：

```
sub vcl_recv {
    return (lookup); # 直接查询缓存
}

sub vcl_fetch {
    set beresp.ttl = 0s; # 直接转发给客户端，不做 cache
    return (deliver); 
}

sub vcl_deliver {
    set resp.http.X-Hits = obj.hits; # 击中次数
    if (obj.hits > 0) {
        set resp.http.X-Cache = "HIT"; # 击中标记
    } else {
        set resp.http.X-Cache = "MISS"; # 未击中
    }
    return (deliver);
}
```

刷新几次页面后，可以看到 response header 如下：

```
# first visit http://localhost:6081/caketest/tests/index
HTTP/1.1 200 OK
Server: Apache/2.2.22 (Ubuntu)
X-Powered-By: PHP/5.3.10-1ubuntu3.7
Vary: Accept-Encoding
Content-Encoding: gzip
Content-Type: text/html; charset=UTF-8
Content-Length: 74
Accept-Ranges: bytes
Date: Sat, 31 Aug 2013 14:55:42 GMT
X-Varnish: 1398451209
Age: 0
Via: 1.1 varnish
Connection: keep-alive
X-Hits: 0
X-Cache: MISS

# second visit http://localhost:6081/caketest/tests/index
HTTP/1.1 200 OK
Server: Apache/2.2.22 (Ubuntu)
X-Powered-By: PHP/5.3.10-1ubuntu3.7
Vary: Accept-Encoding
Content-Encoding: gzip
Content-Type: text/html; charset=UTF-8
Content-Length: 74
Accept-Ranges: bytes
Date: Sat, 31 Aug 2013 14:57:06 GMT
X-Varnish: 1398451211
Age: 0
Via: 1.1 varnish
Connection: keep-alive
X-Hits: 0
X-Cache: MISS
```

可以从 header 里看到并没有击中缓存。然后再把缓存时间设置成 10 分钟试试看。

### NO PASS CACHE FOR 10 MINUTES

```
sub vcl_fetch {
    set beresp.ttl = 600s; # 缓存 10 分钟
    return (deliver); 
}
```

```
# first visit http://localhost:6081/caketest/tests/index
HTTP/1.1 200 OK
Server: Apache/2.2.22 (Ubuntu)
X-Powered-By: PHP/5.3.10-1ubuntu3.7
Vary: Accept-Encoding
Content-Encoding: gzip
Content-Type: text/html; charset=UTF-8
Content-Length: 74
Accept-Ranges: bytes
Date: Sat, 31 Aug 2013 16:20:35 GMT
X-Varnish: 284865991
Age: 0
Via: 1.1 varnish
Connection: keep-alive
X-Hits: 0
X-Cache: MISS

# second visit http://localhost:6081/caketest/tests/index
HTTP/1.1 200 OK
Server: Apache/2.2.22 (Ubuntu)
X-Powered-By: PHP/5.3.10-1ubuntu3.7
Vary: Accept-Encoding
Content-Encoding: gzip
Content-Type: text/html; charset=UTF-8
Content-Length: 74
Accept-Ranges: bytes
Date: Sat, 31 Aug 2013 16:20:39 GMT
X-Varnish: 284865992 284865991
Age: 4
Via: 1.1 varnish
Connection: keep-alive
X-Hits: 1
X-Cache: HIT
```

缓存击中。

#### POST cached ？

之前的缓存都是针对 GET 请求的，但是一个 POST 请求也会被缓存吗？下面再测试一下：

``` php
public function search() {
    $content = "";
    if ($this->request->is('post')) {
        $query = $this->data['Search']['query'];
        if (!empty($query)) {
            $this->redirect(array('query' => $query));
        } else {
            $this->redirect(array('action' => $this->action));
        }
    }
    $content = isset($this->request->named['query']) ? $this->request->named['query'] : null ;
    $this->set("content", $content);
}
```

``` html
<div>
    <h1>Hello, varnish!</h1>
    <p>You query word is: <strong><?= $content?></strong></p>
    <div>
        <?= $this->Form->create('Search', 
                array(
                    'type' => 'post', 
                    'url' => array(
                        'controller' => $this->request->params['controller'],
                        'action' => $this->request->params['action'],
                    )
                )
            )?>
        <?= $this->Form->text('query')?>
        <?= $this->Form->submit('Search')?>
        <?= $this->Form->end()?>
    </div>
</div>
```

接着访问 `http://localhost:6081/caketest/tests/search` 。

```
# first visit http://localhost:6081/caketest/tests/search
HTTP/1.1 200 OK
Server: Apache/2.2.22 (Ubuntu)
X-Powered-By: PHP/5.3.10-1ubuntu3.7
Vary: Accept-Encoding
Content-Encoding: gzip
Content-Type: text/html; charset=UTF-8
Content-Length: 279
Accept-Ranges: bytes
Date: Sat, 31 Aug 2013 16:39:36 GMT
X-Varnish: 1956628660
Age: 0
Via: 1.1 varnish
Connection: keep-alive
X-Hits: 0
X-Cache: MISS

# POST request http://localhost:6081/caketest/tests/search
HTTP/1.1 200 OK
Server: Apache/2.2.22 (Ubuntu)
X-Powered-By: PHP/5.3.10-1ubuntu3.7
Vary: Accept-Encoding
Content-Encoding: gzip
Content-Type: text/html; charset=UTF-8
Content-Length: 279
Accept-Ranges: bytes
Date: Sat, 31 Aug 2013 16:41:18 GMT
X-Varnish: 1956628661 1956628660
Age: 101
Via: 1.1 varnish
Connection: keep-alive
X-Hits: 1
X-Cache: HIT
```

很明显，这次 POST 的结果击中了缓存，将第一次访问 `http://localhost:6081/caketest/tests/search` 的结果返回了。按照程序的逻辑， POST 之后会有一次跳转，当前页面的 URL 是会改变的，可是明显看到这次请求并没有穿透 varnish 。于是我们又得修改配置文件能够让 POST 不被 varnish 缓存。

### PASS POST CACHE GET

```
sub vcl_recv {
    if (req.request == "POST") { # POST 请求穿透
        return (pass);
    }
    return (lookup); # 查询缓存
}
```

```
# first visit http://localhost:6081/caketest/tests/search
HTTP/1.1 200 OK
Server: Apache/2.2.22 (Ubuntu)
X-Powered-By: PHP/5.3.10-1ubuntu3.7
Vary: Accept-Encoding
Content-Encoding: gzip
Content-Type: text/html; charset=UTF-8
Content-Length: 279
Accept-Ranges: bytes
Date: Sat, 31 Aug 2013 17:15:56 GMT
X-Varnish: 1605383887
Age: 0
Via: 1.1 varnish
Connection: keep-alive
X-Hits: 0
X-Cache: MISS

# POST request http://localhost:6081/caketest/tests/search
HTTP/1.1 302 Found
Server: Apache/2.2.22 (Ubuntu)
X-Powered-By: PHP/5.3.10-1ubuntu3.7
Location: http://localhost:6081/caketest/tests/search/query:caiknife
Vary: Accept-Encoding
Content-Encoding: gzip
Content-Type: text/html; charset=UTF-8
Content-Length: 20
Accept-Ranges: bytes
Date: Sat, 31 Aug 2013 17:16:18 GMT
X-Varnish: 1605383888
Age: 0
Via: 1.1 varnish
Connection: keep-alive
X-Hits: 0
X-Cache: MISS

# 302 redirect http://localhost:6081/caketest/tests/search/query:caiknife
HTTP/1.1 200 OK
Server: Apache/2.2.22 (Ubuntu)
X-Powered-By: PHP/5.3.10-1ubuntu3.7
Vary: Accept-Encoding
Content-Encoding: gzip
Content-Type: text/html; charset=UTF-8
Content-Length: 286
Accept-Ranges: bytes
Date: Sat, 31 Aug 2013 17:16:18 GMT
X-Varnish: 1605383889
Age: 0
Via: 1.1 varnish
Connection: keep-alive
X-Hits: 0
X-Cache: MISS

# third visit http://localhost:6081/caketest/tests/search/query:caiknife
HTTP/1.1 200 OK
Server: Apache/2.2.22 (Ubuntu)
X-Powered-By: PHP/5.3.10-1ubuntu3.7
Vary: Accept-Encoding
Content-Encoding: gzip
Content-Type: text/html; charset=UTF-8
Content-Length: 286
Accept-Ranges: bytes
Date: Sat, 31 Aug 2013 17:17:30 GMT
X-Varnish: 1605383890 1605383889
Age: 72
Via: 1.1 varnish
Connection: keep-alive
X-Hits: 1
X-Cache: HIT

# POST request http://localhost:6081/caketest/tests/search
HTTP/1.1 302 Found
Server: Apache/2.2.22 (Ubuntu)
X-Powered-By: PHP/5.3.10-1ubuntu3.7
Location: http://localhost:6081/caketest/tests/search/query:caiknife
Vary: Accept-Encoding
Content-Encoding: gzip
Content-Type: text/html; charset=UTF-8
Content-Length: 20
Accept-Ranges: bytes
Date: Sat, 31 Aug 2013 17:25:52 GMT
X-Varnish: 1605383895
Age: 0
Via: 1.1 varnish
Connection: keep-alive
X-Hits: 0
X-Cache: MISS

# 302 redirect http://localhost:6081/caketest/tests/search/query:caiknife
HTTP/1.1 200 OK
Server: Apache/2.2.22 (Ubuntu)
X-Powered-By: PHP/5.3.10-1ubuntu3.7
Vary: Accept-Encoding
Content-Encoding: gzip
Content-Type: text/html; charset=UTF-8
Content-Length: 286
Accept-Ranges: bytes
Date: Sat, 31 Aug 2013 17:25:52 GMT
X-Varnish: 1605383896 1605383889
Age: 574
Via: 1.1 varnish
Connection: keep-alive
X-Hits: 2
X-Cache: HIT
``` 

从 response header 中可以看到，所有的 POST 请求都正常穿透到了后端服务器，没有被缓存起来。而所有的 GET 请求都被正常缓存，并且能从这正是我们想要的效果。

今天只是第一步对 varnish 的测试，日后我会测试更多的例子，更加深入地研究 varnish 。

最后再虚伪地做一下压力测试：

``` bash
caiknife@caiknife-ThinkPad-T400:~$ webbench -c 100 -t 10 http://localhost/caketest/tests/info
Webbench - Simple Web Benchmark 1.5
Copyright (c) Radim Kolar 1997-2004, GPL Open Source Software.

Benchmarking: GET http://localhost/caketest/tests/info
100 clients, running 10 sec.

Speed=138 pages/min, 343387 bytes/sec.
Requests: 23 susceed, 0 failed.

caiknife@caiknife-ThinkPad-T400:~$ webbench -c 100 -t 10 http://localhost:6081/caketest/tests/info
Webbench - Simple Web Benchmark 1.5
Copyright (c) Radim Kolar 1997-2004, GPL Open Source Software.

Benchmarking: GET http://localhost:6081/caketest/tests/info
100 clients, running 10 sec.

Speed=123384 pages/min, 172400768 bytes/sec.
Requests: 20564 susceed, 0 failed.
```

看样子性能是提升了不少，看来 varnish 相当不错，我了个去啊，好好用的话，牛逼大发了啊。可惜我到现在都没有自己的服务器，妈蛋！

Have a nice day！
