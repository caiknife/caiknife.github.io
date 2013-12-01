---
layout: post
title: "早先碰到的一个 PHP 问题，有关 curl 和 file_get_contents"
date: 2013-12-01 09:41:38 +0800
comments: true
categories: php
---
最早在 SmarterUS 改版的时候，后台数据采用了 API 的方式来提供，当时我第一次想要获取 API 数据时，天真的使用了 `file_get_contents` 函数，结果在自己的机器上能正常获取 XML 数据，但是代码一上到公司的开发机，获取 API 时返回竟然是空白页面，实在是百思不得其解。

既然 `file_get_contents` 不能起作用，那就换个方式，用 `curl` 来获取远程数据好了。当然用 `curl` 能成功地获取数据，但是我还是有疑问——为什么 `file_get_contents` 就是不行呢？

<!-- more -->

当时没多想是什么原因，后来回家来查了一下， `file_get_contents` 有个参数设置叫 `allow_url_fopen`，如果设置为 `off` 的话，那么这个函数就不能获取远程 url 的内容，所以换成 `curl` 来获取远程内容才是正确的做法。但是原因不止这一个，还有更重要的原因应该选择 `curl` ，这就是这两者的效率问题。

写段测试代码来看看到底这两者的效率到底有何差异：

``` php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

class Remote {
    const FILE_GET_CONTENTS = 1;
    const CURL = 2;

    protected $_startTime;
    protected $_endTime;

    public static function getMicroTime() {
        return microtime(true);
    }

    public static function getContentByFileGetContents($url) {
        $result = file_get_contents($url);
        return $result;
    }

    public static function getContentByCurl($url) {
        $ch = curl_init();
        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,  
            CURLOPT_HEADER         => false,  
        ); 
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch); 
        return $result;
    }

    public function remoteOpen($url, $type, $loop=10) {
        switch ($type) {
            case self::FILE_GET_CONTENTS:
                $method = 'getContentByFileGetContents';
                break;
            case self::CURL:
                $method = 'getContentByCurl';
                break;
            default:
                throw new Exception('No such type!');
                break;
        }
        $this->_startTime = self::getMicroTime();
        for ($i=0; $i<$loop; $i++) {
            // Zend_Debug::dump('calling method ' . __CLASS__ . '::' . $method . '.');
            $result = self::{$method}($url);
            // Zend_Debug::dump($result);
        }
        $this->_endTime = self::getMicroTime();

        Zend_Debug::dump($this->_endTime - $this->_startTime);
    }
}

$r = new Remote();
$url = 'http://ip.taobao.com/service/getIpInfo.php?ip=210.210.200.200';

$r->remoteOpen($url, Remote::FILE_GET_CONTENTS);

$r->remoteOpen($url, Remote::CURL);
```

运行后看看输出结果：

``` bash
> php curl.php                                                                                       

double(2.9429008960724)

double(2.1207449436188)
```

不过在多次运行这条程序的情况下，偶尔会出现用 `curl` 比 `file_get_contents` 要消耗更长的时间。不过由于 `curl` 有更强的定制性，因此还是推荐使用 `curl`。

Google 了一下 `curl` 和 `file_get_contents` 的区别，主要有下面这几个点：

1、`file_get_contents` 不会缓存 DNS ，但是 `curl` 会。

2、`file_get_contents` 不能 keepalive ，但是 `curl` 可以。

如此一来，在读取本地文件的场景下，应该使用 `file_get_contents` ；而在获取远程数据时，应该使用 `curl` 。

Have a nice day！
