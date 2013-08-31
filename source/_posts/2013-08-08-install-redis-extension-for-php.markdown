---
layout: post
title: "安装phpredis扩展"
date: 2013-08-08 14:41
comments: true
categories: redis php
---
redis官方推荐的PHP扩展有两个——[Predis](https://github.com/nrk/predis)和[phpredis](https://github.com/nicolasff/phpredis)。phpredis是使用C编写的PHP module，速度应该会比较快，而且CakePHP使用的redis库默认就是phpredis，先安装这个试试看。

``` bash
$ sudo apt-get install php5-dev

$ git clone git://github.com/nicolasff/phpredis.git

$ cd phpredis
$ phpize
$ ./configure
$ make
$ sudo make install

$ sudo -s
$ echo "extension=redis.so" > /etc/php5/conf.d/redis.ini
$ exit

$ sudo service apache2 reload
```

之后phpinfo一下就能看到phpredis的扩展信息。

>redis  
>Redis Support   enabled  
>Redis Version   2.2.3  

<!-- more -->

在CakePHP中做测试：

``` php
class TestsController extends AppController {
    public $uses = array();

    public function index() {
        $this->autoRender = false;

        $r = new Redis();
        $r->connect('localhost');

        debug($r->keys('*c*'));

        foreach ($r->keys('*c*') as $index => $key) {
            debug($key);
            debug($r->get($key));
        }
    }
}
```

输出结果：

>/app/Controller/TestsController.php (line 11)  
>array(  
>    (int) 0 => 'counter:rand:000000000000',  
>    (int) 1 => 'caiknife:name'  
>)  
>/app/Controller/TestsController.php (line 14)   
>'counter:rand:000000000000'  
>/app/Controller/TestsController.php (line 15)  
>'110000'  
>/app/Controller/TestsController.php (line 14)  
>'caiknife:name'  
>/app/Controller/TestsController.php (line 15)  
>'Cai'  