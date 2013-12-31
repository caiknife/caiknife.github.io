---
layout: post
title: "Windows 上安装 Python 包的时候会发生错误"
date: 2013-12-31 09:39:35 +0800
comments: true
categories: python
---
昨天在公司的电脑上安装了 Python 环境，开始用 PyCharm 来做开发，不过在安装的过程中碰到了一些问题，顺利解决后来记录一下。

公司的电脑是 Win7 64bit ，安装了 Python 2.7.6 之后，我需要再安装 setuptools 和 pip 来做包的管理。但是在安装这两个包的过程中，经常发生下面的警告：

<!-- more -->

``` bash
Upgrade packages failed.


The following command was executed:

packaging_tool.py install --build-dir C:\Users\zcai\AppData\Local\Temp\pycharm-packaging5243158917089453594.tmp -U setuptools

The error output of the command:


Downloading/unpacking setuptools from https://pypi.python.org/packages/source/s/setuptools/setuptools-2.0.2.tar.gz#md5=101b0829eca064fe47708039d66fc135
  Running setup.py egg_info for package setuptools
    Traceback (most recent call last):
      File "<string>", line 3, in <module>
      File "setuptools\__init__.py", line 11, in <module>
        from setuptools.extension import Extension
      File "setuptools\extension.py", line 5, in <module>
        from setuptools.dist import _get_unpatched
      File "setuptools\dist.py", line 15, in <module>
        from setuptools.compat import numeric_types, basestring
      File "setuptools\compat.py", line 19, in <module>
        from SimpleHTTPServer import SimpleHTTPRequestHandler
      File "C:\Python27\lib\SimpleHTTPServer.py", line 27, in <module>
        class SimpleHTTPRequestHandler(BaseHTTPServer.BaseHTTPRequestHandler):
      File "C:\Python27\lib\SimpleHTTPServer.py", line 208, in SimpleHTTPRequestHandler
        mimetypes.init() # try to read system mime.types
      File "C:\Python27\lib\mimetypes.py", line 358, in init
        db.read_windows_registry()
      File "C:\Python27\lib\mimetypes.py", line 258, in read_windows_registry
        for subkeyname in enum_types(hkcr):
      File "C:\Python27\lib\mimetypes.py", line 249, in enum_types
        ctype = ctype.encode(default_encoding) # omit in 3.x!
    UnicodeDecodeError: 'ascii' codec can't decode byte 0xb0 in position 1: ordinal not in range(128)
    Complete output from command python setup.py egg_info:
    Traceback (most recent call last):

  File "<string>", line 3, in <module>

  File "setuptools\__init__.py", line 11, in <module>

    from setuptools.extension import Extension

  File "setuptools\extension.py", line 5, in <module>

    from setuptools.dist import _get_unpatched

  File "setuptools\dist.py", line 15, in <module>

    from setuptools.compat import numeric_types, basestring

  File "setuptools\compat.py", line 19, in <module>

    from SimpleHTTPServer import SimpleHTTPRequestHandler

  File "C:\Python27\lib\SimpleHTTPServer.py", line 27, in <module>

    class SimpleHTTPRequestHandler(BaseHTTPServer.BaseHTTPRequestHandler):

  File "C:\Python27\lib\SimpleHTTPServer.py", line 208, in SimpleHTTPRequestHandler

    mimetypes.init() # try to read system mime.types

  File "C:\Python27\lib\mimetypes.py", line 358, in init

    db.read_windows_registry()

  File "C:\Python27\lib\mimetypes.py", line 258, in read_windows_registry

    for subkeyname in enum_types(hkcr):

  File "C:\Python27\lib\mimetypes.py", line 249, in enum_types

    ctype = ctype.encode(default_encoding) # omit in 3.x!

UnicodeDecodeError: 'ascii' codec can't decode byte 0xb0 in position 1: ordinal not in range(128)

----------------------------------------
Cleaning up...
Command python setup.py egg_info failed with error code 1 in C:\Users\zcai\AppData\Local\Temp\pycharm-packaging5243158917089453594.tmp\setuptools
Storing complete log in C:\Users\zcai\pip\pip.log
```

根据错误信息，在 `C:\Python27\lib\mimetypes.py` 找到下面的代码：

``` python
try:
    ctype = ctype.encode(default_encoding) # omit in 3.x!
except UnicodeEncodeError:
    pass
```

注释掉这段代码，再重新安装就可以了。

Windows 上开发真的好多坑啊，还是坚持在 Linux 下开发算了。

Have a nice day！