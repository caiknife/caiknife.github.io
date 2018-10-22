---
layout: post
title: "SSH配置备忘"
date: 2018-10-22 19:46:38 +0800
comments: true
categories: ssh linux shell
---
记录一下我机器上的SSHConfig，防止将来忘记了。

<!-- more -->

```
Host jserver
    HostName ********
    User caiknife

Host j2server
    HostName ********
    User caiknife
    StrictHostKeyChecking no
    Port 2222
```
