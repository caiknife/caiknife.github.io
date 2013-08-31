---
layout: post
title: "老生常谈cookie跨域"
date: 2013-08-26 18:00
comments: true
categories: cookie php jquery
---
很早之前就做过跨域设置 cookie 的项目了，但是以前没有做好积累，今晚花了点时间重新复习一下。

OK ，有两个域名， www.a.com 和 www.b.com ，现在要通过 a 网站来设置 b 网站下的 cookie 。

a 网站下 setcookie.php 的代码：

``` php
<html>
<head>
    <meta charset="utf-8" />
    <title>Cookie Cross Domain</title>
    <!--
    -->
    <script type="text/javascript" src="http://www.b.com/cookie/setcookie.php?name=world"></script>
</head>
<body>
    <h1>test</h1>
    <!--
    <iframe src="http://www.b.com/cookie/setcookie.php?name=hello"></iframe>
    -->
</body>
</html>
```

用 JavaScript 来调用或者用 iframe 调用都是没有问题的。

<!-- more -->

下面是 b 网站的 setcookie.php 的代码：

``` php
header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
$name = isset($_GET['name']) ? $_GET['name'] : "caiknife";
setcookie("name", $name, time()+3600, '/', '.b.com');
```

第一行的header函数是因为IE浏览器下有严格的P3P安全验证，所以需要发送这段header信息，FireFox下可以不用。

接下来，是b网站的getcookie.php的代码：

``` php
if (isset($_GET['callback'])) {
    $callback = $_GET['callback'];
    echo $callback."(".json_encode($_COOKIE).")";
} else {
    echo json_encode($_COOKIE);
}
```

先别管if语句里前面的代码是什么意思，我们直接访问`http://www.b.com/cookie/getcookie.php` ，就能看到结果：{"name":"world"}，OK，cookie跨域设置成功！

{% img /downloads/image/cross-domain-cookie/a.jpg %}

接下来，是cookie跨域读取，看看a网站的getcookie.php代码：

``` php
<html>
<head>
    <meta charset="utf-8" />
    <title></title>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript">
    $(function(){

    $.getJSON("http://www.b.com/cookie/getcookie.php?callback=?", function(data){
        $("p").html(data.name);
        // alert(data.name);
    });

    });
    </script>
</head>
<body>
    <p></p>
</body>
</html>
```

这里callback=?，在jQuery的官方文档里是这么解释的： 
> 在 jQuery 1.2 中，您可以通过使用JSONP形式的回调函数来加载其他网域的JSON数据，如 "myurl?callback=?"。jQuery 将自动替换 ? 为正确的函数名，以执行回调函数。 注意：此行以后的代码将在这个回调函数执行前执行。”。

现在访问 a 网站的 `http://www.a.com/cookie/getcookie.php` 页面，看到p标签里显示了“world”，跨域 cookie 读取成功！

{% img /downloads/image/cross-domain-cookie/b.jpg %}

以上代码在 FireFox 20.0，IE9 下测试通过。