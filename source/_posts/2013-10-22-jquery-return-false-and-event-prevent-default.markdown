---
layout: post
title: "jQuery 中 return false 和 event.preventDefault() 的区别"
date: 2013-10-22 15:21
comments: true
categories: js jquery
---
以前，我在用 jQuery 写链接的点击事件时，一般都是用的 `return false` 来阻止它默认的行为，就像下面这样：

``` js+php
$(function(){
    $('a').click(function(){
        // bla... bla... bla..
        return false;
    });
});
```

<!-- more -->

在 javascript 中，发生一个点击时，会向下面示例的那样，从内而外地将事件向上进行传递：

```
                  / \
---------------| |-----------------
| element1     | |                |
|   -----------| |-----------     |
|   |element2  | |          |     |
|   -------------------------     |
|        Event BUBBLING           |
-----------------------------------
```

因此，当点击了 element2 的时候，同时也会触发 element1 的 click 事件。而在 jQuery 的 click 事件中使用 `return false` 的时候，即阻止了默认事件的发生，同时也阻止了事件向上冒泡。就像下面这个样子：

``` js+php
if (true) {
    return false;
}

// equals to:

if (true) {
    event.preventDefault();
    event.stopPropagation();
}
```

所以，我之前使用 `return false` 来阻止 click 默认事件，并没有考虑到它同时也阻止了事件冒泡。换句话说，我之前是`只知其然而不知其所以然`，遗憾啊。

下面这段代码，分别用 `return false` 和 `event.preventDefault()` 处理点击事件，体验一下它们之前的区别。

``` html
<!doctype html>
<html>
<head>
    <title>jQuery return false and event preventDefault()</title>
    <script type="text/javascript" src="jquery.1.8.3.min.js"></script>
    <style type="text/css">
    body {
        margin: auto;
    }
    .outer {
        position: relative;
        border: 3px solid red;
        width: 800px;
        height: 600px;
    }
    .inner {
        position: absolute;
        border: 3px solid blue;
        width: 400px;
        height: 300px;
        top: 150px;
        left: 200px;
    }
    </style>
</head>
<body>
    <div class="outer">
        <div class="inner">
        </div>
    </div>
    <script type="text/javascript">
    $('.inner').click(function(e){
        alert('click inner!');
        // e.preventDefault();
        return false;
    });

    $('.outer').click(function(e){
        alert('click outer!');
        // e.preventDefault();
        return false;
    });

    $('body').click(function(e){
        alert('click body!');
        // e.preventDefault();
        return false;
    });
    </script>
</body>
</html>
```

参考链接：  
- <http://stackoverflow.com/questions/1357118/event-preventdefault-vs-return-false>  
- <http://css-tricks.com/return-false-and-prevent-default/>  

Have a nice day！