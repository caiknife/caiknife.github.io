---
layout: post
title: "SSHConfig备忘"
date: 2018-07-13 12:05:38 +0800
comments: true
categories: linux ssh
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
