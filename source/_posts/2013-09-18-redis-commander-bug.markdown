---
layout: post
title: "发现了 redis commander 的一个 bug"
date: 2013-09-18 21:45
comments: true
categories: redis linux nodejs js
---
长久以来，我的工作平台是 Ubuntu ，而我用来管理 redis 的工具是 [redis-commander](http://nearinfinity.github.io/redis-commander/)。

之前我由于做了 redis 的主从备份，开启了本地的 6379 端口和 6380 端口作为 redis 的两个实例，前者作为主机，后者是从机。并且在 redis-commander 的管理界面上分别添加了 6379 和 6380 的 0 号数据库。后来为了提高机器的性能，我关闭了 6380 的实例，并删除了这个实例在 redis-commander 的 tree-view 中对应的分支。结果后来再进入 redis-commander 的时候，左边的实例管理 tree-view 消失了，检查控制台输出时，发现提示是无法连接到 6380 端口。

**为什么我已经删掉了对应的分支，但是 redis-commander 还是会请求 6380 端口呢？**

<!-- more -->

首先想到了可能是 redis-commander 的配置文件有问题。于是我从 redis-commander 的源代码入手去检查。

在 `/usr/lib/node_modules/redis-commander/lib/util.js` 这个文件下找到了 redis-commander 的配置文件路径：

``` js+php
//Config Util functions
exports.getConfig = function (callback) {
  fs.readFile(getUserHome() + "/.redis-commander", 'utf8', function (err, data) {
    if (err) {
      callback(err);
    } else {
      var config = JSON.parse(data);
      callback(null, config);
    }
  });
};

exports.saveConfig = function (config, callback) {
  fs.writeFile(getUserHome() + "/.redis-commander", JSON.stringify(config), function (err) {
    if (err) {
      callback(err);
    } else {
      callback(null);
    }
  });
};
```

于是修改 `/home/caiknife/.redis-commander` 这个文件的内容：

``` js+php
{
    "sidebarWidth":"454",
    "locked":"true",
    "CLIHeight":"94",
    "CLIOpen":"false",
    "default_connections":[
        {"host":"localhost","port":"6379","password":"","dbIndex":"0"},
        {"host":"localhost","port":"6379","password":"","dbIndex":"1"},
        {"host":"localhost","port":"6380","password":"","dbIndex":"0"}
    ]
}
```

很明显，虽然我删除了一个连接，但是配置文件中并没有更新这个删除操作，仍然保留了以前的内容。所以我得手动删除。

于是删除多余的配置，重新启动 redis-commander 。 OK ，一切又正常了。

Have a nice day！

**2013年9月19日更新**

删除实例的时候在控制台里看到 `Could not remove localhost:6379:1 from default connections.` ，然后在 `/usr/lib/node_modules/redis-commander/lib/routes/home.js` 找到了对应的源代码：

``` js+php
function removeConnectionFromDefaults (connections, connectionIds, callback) {
  var notRemoved = true;
  var hostname = connectionIds[0];
  var port = connectionIds[1];
  var db = connectionIds[2];
  connections.forEach(function (connection, index) {
    console.log(connection.selected_db);
    console.log(connection);
    if (notRemoved && connection.host == hostname && connection.port == port && connection.selected_db == db) {
      notRemoved = false;
      connections.splice(index, 1);
    }
  });
  if (notRemoved) {
    return callback("Could not remove " + hostname + ":" + port + ":" + db + " from default connections.");
  } else {
    return callback(null, connections);
  }
}
``` 

通过 `console.log(connection.selected_db);` 发现始终返回 `undefined` 。接着输出 `console.log(connection);` 发现它是一个这样的数据结构：

``` js+php
{ host: 'localhost', port: '6379', password: '', dbIndex: '2' }
```

可见这里有个 bug ，在应该使用 `dbIndex` 的地方使用了 `selected_db` 。修改代码后就可以正常删除连接实例并更新到配置文件里了。
