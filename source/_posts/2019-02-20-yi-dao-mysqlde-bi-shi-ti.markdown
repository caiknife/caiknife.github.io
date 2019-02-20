---
layout: post
title: "一道MySQL的笔试题"
date: 2019-02-20 13:32:09 +0800
comments: true
categories: 数据库 mysql
---
最近一次面试时做的笔试题目，虽然很简单，但是我挺有印象的。做过很多笔试题目，这应该是第一个让我觉得挺有印象的数据库笔试题。

题目大概是这样的：

有一张表 rail 存储着上海地铁线路的信息。line 表示线路，stop 表示站点名称，sequence 表示站点在线路上的顺序。（为了简洁，我直接用 SQL 语句表示数据）

```sql
CREATE TABLE `rail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `line` int(10) unsigned NOT NULL,
  `stop` varchar(100) NOT NULL DEFAULT '',
  `squence` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

INSERT INTO `rail` 
    (`id`, `line`, `stop`, `squence`)
VALUES
	(1,1,'人民广场',10),
	(2,2,'人民广场',11),
	(3,8,'人民广场',9),
	(4,2,'世纪大道',8),
	(5,9,'世纪大道',12),
	(6,11,'曹杨路',6),
	(7,2,'静安寺',6),
	(8,7,'静安寺',9);
```
<!-- more -->

题目的要求有两个：

> 1、查询经过站点最多的站点。

> 2、给出任意两条线路，查询这两条线路换乘的站点。

第一个要求很简单，直接用下面的语句完成就可以了。

```sql
SELECT `stop`, count(`stop`) AS stop_count 
FROM rail GROUP BY `stop` ORDER BY stop_count DESC LIMIT 1;
```

倒是第二个要求，我在第一次思考的时候就踩坑了，我写了一个非常低级错误的 SQL。

```sql
SELECT `stop` FROM tail WHERE `line`=@a AND `line`=@b;
```

刚写完最后的分号我就反应过来了，这里不对，正确的做法是得让 rail 表自己和自己 join 之后查询。于是思考了一小会儿写下了第二个 SQL。

```sql
SELECT r1.stop FROM rail r1, rail r2 
WHERE r1.line=@a AND r2.line=@b AND r1.stop=r2.stop;
```

题目是挺简单的，只是我今后不能直接靠直觉去想结题的思路，还是要多思考一会儿，把解决问题的步骤想清楚。
