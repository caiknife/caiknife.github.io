---
layout: post
title: "RVM使用"
date: 2018-10-16 16:07:18 +0800
comments: true
categories: ruby rvm shell
---
之前因为 Ruby 版本的问题，博客一直用不了。前段时间使用了 RVM 来管理 Ruby 的版本，所以博客又复活了。

所以今天就来总结一下如何用 RVM 来管理不同的 Ruby 版本。

<!-- more -->

## RVM 的安装

先安装 RVM
``` bash
gpg --keyserver hkp://keys.gnupg.net --recv-keys 409B6B1796C275462A1703113804BB82D39DC0E3 7D2BAF1CF37B13E2069D6956105BD0E739499BDB

\curl -sSL https://get.rvm.io | bash -s stable
```

## Ruby 的安装和切换

列出已知的 Ruby 版本
``` bash
rvm list known
```

安装一个 Ruby 版本
``` bash
rvm install 2.2.0
```

切换 Ruby 版本
``` bash
rvm use 2.2.0
```

如果想设置为默认版本，这样一来以后新打开的控制台默认的 Ruby 就是这个版本。
``` bash
rvm use 2.2.0 --default
```

查询已经安装的ruby
``` bash
rvm list
```

卸载一个已安装版本
``` bash
rvm remove 1.8.7
```

## gemset 的使用

RVM 不仅可以提供一个多 Ruby 版本共存的环境，还可以根据项目管理不同的 gemset。gemset 可以理解为是一个独立的虚拟 gem 环境，每一个 gemset 都是相互独立的。

比如你有两个项目，一个是 Rails 2.3 一个是 Rails3。gemset 可以帮你便捷的建立两套 gem 开发环境，并且方便的切换。gemset 是附加在 Ruby 语言版本下面的，例如你用了 1.9.2, 建立了一个叫 rails3 的 gemset,当切换到 1.8.7 的时候，rails3 这个 gemset 并不存在。

建立 gemset
``` bash
rvm use 1.8.7
rvm gemset create rails23
```

然后可以设定已建立的 gemset 做为当前环境。use 可以用来切换语言或者 gemset，前提是他们已经被安装(或者建立)，并可以在 list 命令中看到。
``` bash
rvm use 1.8.7
rvm use 1.8.7@rails23
```
然后所有安装的 gem 都是安装在这个 gemset 之下。

列出当前 Ruby 的 gemset
``` bash
rvm gemset list
```

如果你想清空一个 gemset 的所有 gem， 想重新安装所有 gem，可以这样
``` bash
rvm gemset empty 1.8.7@rails23
```

删除一个 gemset
``` bash
rvm gemset delete rails2-3
```

RVM 还可以自动加载 gemset。 例如我们有一个 Rails 3.1.3 项目，需要 1.9.3 版本 Ruby，整个流程可以这样。
``` bash
rvm install 1.9.3
rvm use 1.9.3
rvm gemset create rails313
rvm use 1.9.3@rails313
```

下面进入到项目目录，建立一个 .rvmrc 文件。在这个文件里可以很简单的加一个命令：
``` bash
rvm use 1.9.3@rails313
```
然后无论你当前 Ruby 设置是什么，cd 到这个项目的时候，RVM 会帮你加载 Ruby 1.9.3 和 rails313 gemset.

## 使用 .ruby-gemset 和 .ruby-version

在项目目录下创建这两个文件 `.ruby-gemset` 和 `.ruby-version`，在 `.ruby-gemset` 中填入 gemset 的名称，在 `.ruby-version` 填入 Ruby 版本的名称，当 cd 到这个目录的时候，就会自动加载。
