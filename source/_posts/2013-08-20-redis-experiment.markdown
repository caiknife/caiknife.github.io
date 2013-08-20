---
layout: post
title: "redis实验（一）"
date: 2013-08-20 21:30
comments: true
categories: redis python linux
---
研究了一下redis的常用技巧。

除了SET方法之外，redis还有MSET方法可以批量设置，如果发现有同名的key存在，就会覆盖原有的key。如果不想覆盖已经存在的key，请使用MSETNX方法。

用法：MSET key value [key value ...]  
```
redis> MSET key1 "Hello" key2 "World"
OK
redis> GET key1
"Hello"
redis> GET key2
"World"
redis> 
```

用法：MSETNX key value [key value ...]  
```
redis> MSETNX key1 "Hello" key2 "there"
(integer) 1
redis> MSETNX key2 "there" key3 "world"
(integer) 0
redis> MGET key1 key2 key3
1) "Hello"
2) "there"
3) (nil)
redis> 
```

<!-- more -->

查讯key就要使用KEYS方法，文档中提到KEYS方法速度很快，虽然时间复杂度是O(N)，但是在一台入门级的笔记本电脑上，搜索100W条key，redis只需要40毫秒左右。但是在生产环境上，还是尽量不要使用KEYS命令，而是使用set来进行查询。

> ***Warning***: consider KEYS as a command that should only be used in production environments with extreme care. It may ruin performance when it is executed against large databases. This command is intended for debugging and special operations, such as changing your keyspace layout. Don't use KEYS in your regular application code. If you're looking for a way to find keys in a subset of your keyspace, consider using sets.

用法：KEYS pattern   
```
redis> MSET one 1 two 2 three 3 four 4
OK
redis> KEYS *o*
1) "two"
2) "one"
3) "four"
redis> KEYS t??
1) "two"
redis> KEYS *
1) "two"
2) "three"
3) "one"
4) "four"
redis> 
```

在redis里，排序是一件比较复杂的事情，[官方文档写得很详细](http://redis.io/commands/sort) 。

用法：SORT key [BY pattern] [LIMIT offset count] [GET pattern [GET pattern ...]] [ASC|DESC] [ALPHA] [STORE destination]。正常排序还是很好理解的，直接`SORT key LIMIT offset count ASC|DESC ALPHA`即可，`LIMIT`和SQL语句中的含义一样，一般用来做分页用；`ASC|DESC`就是升序/降序排列；`ALPHA`表示将元素都当作字符串对待。

不过在使用外部key进行排序的时候，就有点复杂了。

举个例子，`SORT mylist BY weight_* GET object_*`。首先就要求你在mylist中存储是所有weight_*的id，而这句话的意思就是根据weight进行升序排序，并获得对应的id，并由此获得对应的排序完成的object值。用SQL来描述就是：`SELECT object FROM table ORDER BY weight ASC;`。同样还有用hashes来进行排序的——`SORT mylist BY weight_*->fieldname GET object_*->fieldname`，这就好比`SELECT b.fielname FROM weight a LEFT JOIN object b ON a.id=b.id ORDER BY a.fieldname ASC;`。更详细的内容，还是参考[文档](http://redis.io/commands/sort) 。

下面是一部分测试代码，里面有更详细的注释。

{% include_code redis-py/demo.py %}