---
layout: post
title: "CakePHP 的 Nginx 设置"
date: 2013-09-30 10:01
comments: true
categories: cakephp nginx linux
---
在自己的笔记本上弄了个 CakePHP + Nginx 的组合，做了基本设置。

由于 80 端口已经被 Apache 占用，因此我指定 Nginx 监听 81 端口。

首先记得安装 Nginx 和 PHP5-FPM 模块：

``` bash
$ sudo apt-get install nginx php5-fpm
```

然后记得编辑 `/etc/php/fpm/php.ini` ，并设置 `cgi.fix_pathinfo=0`，接着重启 `php-fpm` 。

<!-- more -->

###将 CakePHP 作为独立网站配置

下面是将 CakePHP 作为一个单独的主机进行配置的 Nginx 配置文件。

``` nginx /etc/nginx/sites-available/cakestrap.conf
server {
    # 监听 81 端口
    listen 81; 
    # 配置本地域名 cakestrap.local
    server_name cakestrap.local; 
    # 将这个域名下所有的请求重写到 www.cakestrap.local
    rewrite ^(.*) http://www.cakestrap.local:81$1 permanent; 
}

server {
    listen 81;
    # 配置本地域名 www.cakestrap.local
    server_name www.cakestrap.local;
    
    # 设置 $document_root
    root "/home/caiknife/source/cakestrap/app/webroot/";
    index index.php;
    access_log /var/log/nginx/cakestrap.local.access.log;
    error_log /var/log/nginx/cakestrap.local.error.log;
    
    # 解析根目录
    location / {
        # 尝试将请求 $uri 解析为文件或者目录，如果都不是的话，重写到根目录下的 index.php 
        try_files $uri $uri/ /index.php?$uri&$args;
    }
    
    # 解析 PHP 文件
    location ~ \.php$ {
        # 请求文件，如果失败的话，显示404。
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        # 这里用 php fpm 模块比传统的 fast cgi 模块要快
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_index index.php;
    }
}
```

接着建立软链接，并测试 Nginx 配置语法是否正确：
``` bash
$ sudo ln -s /etc/nginx/sites-available/cakestrap.conf /etc/nginx/sites-enable/cakestrap.conf
$ sudo nginx -t
```

如果语法正常没有错误的话，就重启 Nginx ：

``` bash
$ sudo service nginx <reload|restart>
```

编辑 `/etc/hosts` ，加入两行： `127.0.0.1   cakestrap.local` 和 `127.0.0.1   www.cakestrap.local` 。之后就可以在浏览器里直接访问了。

###将CakePHP作为网站子目录配置。

如果要把 CakePHP 作为一个网站的子目录访问，应该如何设置呢？

假设本机已经有网站 `localhost` ，我要把一个 CakePHP 项目配置到这个网站的 `cakestrap` 目录下，第一步就在 `localhost` 的根目录下建立一个软链接指向这个 CakePHP 项目。

``` bash
$ ln -s /home/caiknife/source/cakestrap/app/webroot /usr/share/nginx/html/cakestrap
```

然后在 `localhost` 的配置文件里加入下面的代码：

``` nginx 
location /cakestrap {
    # 目录浏览开启
    autoindex on;
    # 尝试将请求 $uri 解析为文件或者目录，如果都不是的话，重写到 $document_root/cakestrap/index.php 
    try_files $uri $uri/ /cakestrap/index.php?$uri&$args;
}
```

CakePHP 默认的 Apache 重写规则是：

``` apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

和 Nginx 的 `try_files $uri $uri/ /cakestrap/index.php?$uri&$args;` 有异曲同工之妙， Apache 的重写规则很容易就能迁移到 Nginx 上。

Nginx 的基本配置就到这里，今后得研究研究它的反向代理、负载均衡和缓存功能。

Have a nice day！