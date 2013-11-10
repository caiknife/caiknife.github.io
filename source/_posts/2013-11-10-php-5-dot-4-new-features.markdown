---
layout: post
title: "PHP 5.4 新特性"
date: 2013-11-10 20:09
comments: true
categories: php
---
安装了 Ubuntu 12.04 之后，我就一直在 PHP 5.3.10 的环境下一直工作了。前段时间，通过 PPA 把 PHP 升级到了 5.4 。

```bash
sudo apt-add-repository ppa:ondrej/php5-oldstable

sudo apt-get update

sudo apt-get upgrade
```

这个源和 PHP 5.4 的官方 release 同步更新。目前，我机器上的 PHP 版本已经升级到了 PHP 5.4.21。

<!-- more -->

在这个链接：<http://php.net/manual/zh/migration54.new-features.php> 可以查询到 PHP 5.4 的新特性，下面这几个链接也对 PHP 5.4 的新特性作出了分析：

http://www.laruence.com/2011/07/02/2097.html
http://www.oracle.com/technetwork/cn/articles/dsl/lerdorf-php54-1564639-zhs.html  
http://tech.it168.com/a2012/0424/1341/000001341278_all.shtml  

下面我们来看看有哪些新特性比较有意思？

```
PHP 5.4.0 提供了丰富的新特性：

新增支持 traits 。
新增短数组语法，比如 $a = [1, 2, 3, 4]; 或 $a = ['one' => 1, 'two' => 2, 'three' => 3, 'four' => 4]; 。
新增支持对函数返回数组的成员访问解析，例如 foo()[0] 。
现在 闭包 支持 $this 。
现在不管是否设置 short_open_tag php.ini 选项，<?= 将总是可用。
新增在实例化时访问类成员，例如： (new Foo)->bar() 。
现在支持 Class::{expr}() 语法。
新增二进制直接量，例如：0b001001101 。
改进解析错误信息和不兼容参数的警告。
SESSION 扩展现在能追踪文件的 上传进度 。
内置用于开发的 CLI 模式的 web server 。
```

其中第一条`新增支持 traits `这一条挺有意思的。想起在写 Rails 的时候，经常就在 helper 中写一个 module ，然后在 controller 中 include 进来，比如下面这样：

``` ruby sessions_helper.rb
module SessionsHelper

  def sign_in(user)
    remember_token = User.new_remember_token
    cookies.permanent[:remember_token] = remember_token
    user.update_attribute(:remember_token, User.encrypt(remember_token))
    self.current_user = user
  end

  def sign_out
    self.current_user = nil
    cookies.delete(:remember_token)
  end

  def current_user=(user)
    @current_user = user
  end

  def current_user
    remember_token = User.encrypt(cookies[:remember_token])
    @current_user ||= User.find_by(remember_token: remember_token)
  end

  def current_user?(user)
    user == current_user
  end

  def signed_in?
    !current_user.nil?
  end

  def redirect_back_or(default)
    redirect_to(session[:return_to] || default)
    session.delete(:return_to)
  end

  def store_location
    session[:return_to] = request.fullpath if request.get?
  end

end
```

``` ruby application_controller.rb
class ApplicationController < ActionController::Base
  # Prevent CSRF attacks by raising an exception.
  # For APIs, you may want to use :null_session instead.
  protect_from_forgery with: :exception

  include SessionsHelper
end
```

在 ApplicationController 基类中 include 这个 SessionsHelper，之后所有的派生类都自动加上了 SessionsHelper 的方法。而我经常使用的 CakePHP 框架，由于之前 PHP 和 Ruby 的语言特性，没办法像 Rails 一样这么灵活，楞是把 Rails 中的 helper 硬生生拆分成了只能在 controller 里使用的 component 和只能在 view 里使用的 helper。希望在 CakePHP 3.X 里面能加上 traits 的特性，方便开发。

当然，升级 PHP 5.4 的另外一个原因是——性能有了很大的提升，为了更快的速度，强烈要求升级到 PHP 5.4 ！看看这个链接：<http://developer.51cto.com/art/201207/349607.htm>

至于为什么不升级到 PHP 5.5 ？因为我还是比较保守……

Have a nice day！