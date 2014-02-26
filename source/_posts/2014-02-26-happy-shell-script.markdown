---
layout: post
title: "一句话 Shell 脚本（3）"
date: 2014-02-26 10:00:43 +0800
comments: true
categories: shell script linux cakephp
---
CakePHP 会在自己的项目文件夹的 `app/tmp` 下建立缓存文件，用来保存数据库表结构。在开启 debug 的情况下，每次发起请求后都会刷新缓存，但是在生产环境下，一般都是关闭了 debug 的，如果某次功能升级修改了表结构的话，缓存并不会主动更新，如此一来就会产生 InternalError 。

看来还是要写个脚本来手动删除这些缓存文件。

<!-- more -->

先看看缓存目录结构是什么样的：

``` bash
> tree
.
├── cache
│   ├── cake_toolbar_cache19c90b767c0870ceefeb6659d993da93
│   ├── models
│   │   ├── ccqo_cake_model_default_ccqo_accounts
│   │   ├── ccqo_cake_model_default_ccqo_articles
│   │   ├── ccqo_cake_model_default_ccqo_list
│   │   ├── ccqo_cake_model_default_ccqo_train_divisions
│   │   ├── ccqo_cake_model_default_ccqo_trainees
│   │   ├── ccqo_cake_model_default_ccqo_trainers
│   │   └── ccqo_cake_model_default_ccqo_train_infos
│   └── persistent
│       ├── ccqo_cake_core_cake_zh-cn
│       ├── ccqo_cake_core_debug_kit_zh-cn
│       ├── ccqo_cake_core_file_map
│       └── ccqo_cake_core_method_cache
└── logs
```

要删除的是缓存文件，目录不用删除。 OK ，那么就写个脚本一次性全部搞定好了。

先想到的是用 `find` 命令配合管道符来用：

``` bash
> find . -type f | xargs rm -f
```

结果报了权限错误：

``` bash
> find . -type f | xargs rm -f
rm: 无法删除"./cache/persistent/ccqo_cake_core_cake_zh-cn": 权限不够
rm: 无法删除"./cache/persistent/ccqo_cake_core_file_map": 权限不够
rm: 无法删除"./cache/persistent/ccqo_cake_core_debug_kit_zh-cn": 权限不够
rm: 无法删除"./cache/persistent/ccqo_cake_core_method_cache": 权限不够
rm: 无法删除"./cache/models/ccqo_cake_model_default_ccqo_list": 权限不够
rm: 无法删除"./cache/models/ccqo_cake_model_default_ccqo_train_divisions": 权限不够
rm: 无法删除"./cache/models/ccqo_cake_model_default_ccqo_trainers": 权限不够
rm: 无法删除"./cache/models/ccqo_cake_model_default_ccqo_articles": 权限不够
rm: 无法删除"./cache/models/ccqo_cake_model_default_ccqo_train_infos": 权限不够
rm: 无法删除"./cache/models/ccqo_cake_model_default_ccqo_trainees": 权限不够
rm: 无法删除"./cache/models/ccqo_cake_model_default_ccqo_accounts": 权限不够
```

这些文件是由 `www-data` 帐号生成的，所以我自然不能删除，所以加上 `sudo` 再删除吧。

几分钟之后又想了想，其实不用管道符，完全可以用 `find` 自己就可以完成了：

``` bash
> find . -type f -exec sudo rm -f {} +
```

打完收工！

最近越来越感觉随着年纪的增长，接触的事情越来越多，记忆力没有以前好了。高中时代特别自豪地过目不忘的本事也指望不上了，看来还是好记性不如烂笔头，得养成一个良好的记录和总结的习惯，才会对将来的工作有所帮助。

Have a nice day！