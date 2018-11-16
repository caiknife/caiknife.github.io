---
layout: post
title: "使用glide进行Go的包管理"
date: 2018-11-16 19:05:17 +0800
comments: true
categories: go glide 软件工程 包管理
---
长话短说，一切从简。

今天开始使用 `glide` 进行 `Golang` 的包管理。

<!-- more -->

先安装 `glide` ：

```bash
brew install glide
```

然后进入工作目录，使用 `glide` 进行初始化：

```bash
glide init
```

由于有些库放在 golang.org 上，不能直接下载，所以需要使用代理。我自己本身用了 `ShadowSocks` 作为翻墙工具，所以使用 `SS` 代理即可。

把下面的代码写到一个脚本里，命名为 `proxy` ，位置是 `/usr/local/bin/` 。

``` bash
#!/usr/bin/env bash
export http_proxy=socks5://127.0.0.1:1080
export https_proxy=socks5://127.0.0.1:1080
$*
```

然后执行：

``` bash
proxy glide install
```

就可以顺利下载库了。
