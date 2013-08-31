---
layout: post
title: "一个用来打印log的脚本"
date: 2013-08-02 00:48
comments: true
categories: work vcb shell
---

现在在公司里做Tracking的工作，在开发环境下，Tracking的log文件保存在服务器的/mezi/sites/apache/logs下，然后又根据网站的不同有着下面这样的格式：

```
{site_name}_{machine_name}_YYYY_MM_DD_HH_xx.tracking.log
```

从文件格式来看，是每小时生成一个log文件，但是有些站点会每10分钟生成一个log文件，而服务器上又是UTC时间，但是在我的机器上却是北京时间，这样就很不方便看到最新的log。每次要看log，还得脑子里计算一下时差来确认最新的log文件名，实在太不方便了，于是就写了个脚本来看最新的log文件内容。

<!-- more -->

``` bash
#!/bin/bash

log_dir="/mezi/sites/apache/logs" # tracking log dir
machine_name=`hostname -s` # get short name of this machine

if [ -n "$1" ]; then
    site_path="/mezi/sites/${1}" # site located in /mezi/sites/$site_name
    if [ ! -e "$site_path" ]; then
        echo "There is no such site on this machine!"
        exit 1
    fi

    #echo "OK, we got the site. Let's tail tracking log for it."
    date=`date +%Y_%m_%d`
    log_file_format="${log_dir}/${1}_${machine_name}_${date}*" #get tracking log file format
    #echo "$log_file_format"

    log_target=`ls ${log_file_format} 2>/dev/null | sort -rn | head -1`
    if [ -n "$log_target" ] && [ -e "$log_target" ]; then
        echo "The latest log file is $log_target"
        tail -f "$log_target"
    else
        echo "There is no such log file."
    fi
else
    echo "No site name provided."
fi
```

使用这条命令，就用```./print_latest_tracking_log.sh {site_name}```即可，如果当前机器上没有站点，脚本会提示错误信息。