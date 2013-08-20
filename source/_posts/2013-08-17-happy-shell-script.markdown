---
layout: post
title: "一句话Shell脚本（1）"
date: 2013-08-17 21:38
comments: true
categories: work shell script linux
---
公司里的中国项目包括DHB和SMCN两个。

在DHB的SEM Search Landing页面上，会展示SMCN的产品，之前的Tracking是直接从DHB跳转到商家页面，从DHB到商家的跳转会记录一个Offer Outgoing Log。最近加入了新的逻辑，将DHB的跳转作为一个Affiliate Outoging Log，而Dest Url是SMCN的一个跳转脚本，这样就将以前的DHB->商家页面跳转变成了从DHB->SMCN->商家页面的跳转。

<!-- more -->

记录下所有的Affiliate Outoging Log和Offer Outgoing Log，最后会生成小时汇总的Outgoing Log，以```/mezi/tracking/data/dump/dahongbao.com/yyyy-mm-dd/hh/outgoing.dat```的格式保存。

最近的一个需求是要求汇总从这个功能上线起，一天内的所有从DHB跳转到SMCN的数量。在Log文件中，所有字段以```\t```分隔，第七个字段是Business Type，第三十五个字段是Dest Url，从这两个字段入手，就能获取从DHB跳转到SMCN的数量了。

下面是一个参考的脚本：

``` bash
awk -F'\t' '{if(($7 == "CPA" || $7=="CPC") && $35 ~ /www.smarter.com.cn/){print $7,$35}}' /mezi/tracking/data/dump/dahongbao.com/2013-08-15/0[6789]/outgoing.dat /mezi/tracking/data/dump/dahongbao.com/2013-08-15/[12]*/outgoing.dat /mezi/tracking/data/dump/dahongbao.com/2013-08-16/*/outgoing.dat | wc -l
```

Have a nice day！