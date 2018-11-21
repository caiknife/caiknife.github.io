---
layout: post
title: "PHP安装IMAP扩展"
date: 2018-11-21 20:43:49 +0800
comments: true
categories: php linux imap brew mac
---
在经历了MacOS上的homebrew升级之后，imap就不再是homebrew的PHP里默认安装的扩展了，这样如果你在composer.json中引用了一个需要用到imap扩展的库，那么久没有办法更新composer库，这样着实让人内伤。

所以今天就来解决这个问题。

<!-- more -->

### 第一步
你需要通过brew安装imap-uw
```bash
brew install imap-uw
```
注：在完成第一步之后，你需要安装openssl，不管通过何种方式。这里简单介绍一下通过brew安装openssl，非常的简单。

```bash
brew install openssl
```

这就是安装拓展的准备工作，接下来才开始：

### 第二步

你需要去官网下载跟你现在环境所对应的PHP版本，我的是7.1的，所以我下载了PHP7.1的，然后解压。

```bash
tar -zxvf ~/Downloads/php-7.1.24.tar.gz
```

解压之后进入到当前PHP文件的ext/imap文件当中。注意，解压是解压到当前目录下，当然，你也可以指定目录。

```bash
cd php-7.1.19/ext/imap
```

进入到该目录后，第一个命令就是：

```bash
sudo phpize
```

如果你不执行该命令，是无法在这个目录下进行编译安装的。接下来就是编译安装了。

```bash
./configure --with-imap=/usr/local/Cellar/imap-uw/2007f --with-kerberos --with-imap-ssl=/usr/local/opt/openssl
```

注：imap的目录就是imap-uw的安装目录，凡是通过brew安装的都是在/usr/local/Cellar目录下，ssl的目录就是你之前的openssl的目录。这些都要改为自己的。然后再是执行之前的编译。

```bash
make
```

执行完成之后，会在imap目录下生成许多的文件，此时需要创建一个文件夹。

```bash
mkdir /usr/local/opt/php71-imap
```

这个文件夹用来存放刚才执行编译生成的imap.so文件。当前你所在的目录还是在imap里面，需要把imap.so文件拷贝到刚才创建的文件夹中。

```bash
mv modules/imap.so /usr/local/opt/php71-imap/imap.so
```

最后你需要在你的/usr/local/etc/php/7.1/conf.d/imap.ini里面添加这样一句话即可：

```ini
[imap]
extension="/usr/local/opt/php71-imap/imap.so"
```

### 第三步

检查是否安装好:

```bash
php -m | grep imap
```

如果出现了imap，那么大功告成。
