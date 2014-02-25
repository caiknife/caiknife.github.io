---
layout: post
title: "给 CakePHP 的 Session Flash 做点小修改"
date: 2014-02-25 11:59:36 +0800
comments: true
categories: cakephp php bootstrap
---
在 CakePHP 的教程中，CRUD 操作结束后跳转到新页面时，一般会做一个提示框显示在页面上。这个做法在教程中是按照下面的这么个流程写的：

<!-- more -->

首先在 Controller 里面做跳转之前：

``` php
public function add() {
    if ($this->request->is('post')) {
        $this->Post->create();
        if ($this->Post->save($this->request->data)) {
            /**
             * 跳转之前设置一条 Session 信息
             */
            $this->Session->setFlash(__('Your post has been saved.'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Unable to add your post.'));
    }
}
```

在 View 上要做下面修改：

``` php
<div id="content">
    <?php echo $this->Session->flash(); ?>
    <?php echo $this->fetch('content'); ?>
</div>
```

上面代码中的 `<?php echo $this->Session->flash(); ?>` 的作用就是将前一次跳转中设置的 Session 跳转信息显示在页面上，随后删除 Session 中的跳转信息，以确保下一次不会再显示。

但是，默认情况下，这段跳转信息的 HTML 结构是这样的：

``` php
<div id="flashMessage" class="message">Hello, world!</div>
```

那么，如果要配合 BootStrap 一起使用的话，应该如何设置呢？咱们先来看看 SessionComponent 和 SessionHelper 的部分源代码。


``` php SessionComponent
public function setFlash($message, $element = 'default', $params = array(), $key = 'flash') {
    CakeSession::write('Message.' . $key, compact('message', 'element', 'params'));
}

```

很明显，在跳转之前调用 $this->Session->setFlash(__('Your post has been saved.')); ，会在 Session 中以 `Message{$key}` 作为 Key 写入对应的 `$message` ，`$element` ，`$params`。接着会在 SessionHelper 中调用：

``` php SessionHelper
public function flash($key = 'flash', $attrs = array()) {
    $out = false;

    if (CakeSession::check('Message.' . $key)) {
        $flash = CakeSession::read('Message.' . $key);
        $message = $flash['message'];
        unset($flash['message']);

        if (!empty($attrs)) {
            $flash = array_merge($flash, $attrs);
        }

        if ($flash['element'] === 'default') {
            $class = 'message';
            if (!empty($flash['params']['class'])) {
                $class = $flash['params']['class'];
            }
            $out = '<div id="' . $key . 'Message" class="' . $class . '">' . $message . '</div>';
        } elseif (!$flash['element']) {
            $out = $message;
        } else {
            $options = array();
            if (isset($flash['params']['plugin'])) {
                $options['plugin'] = $flash['params']['plugin'];
            }
            $tmpVars = $flash['params'];
            $tmpVars['message'] = $message;
            $out = $this->_View->element($flash['element'], $tmpVars, $options);
        }
        CakeSession::delete('Message.' . $key);
    }
    return $out;
}
```

从上面默认的代码可以看出，如果 `$element` 为 `default` 的话，就是之前默认的 HTML 结构；如果没设置的话，则直接显示 `$message` 文本信息；如果设置成其他的值，就会到 `View/Elements` 下调用相对应的模板文件。

为此，我特别写了一个模板：

``` php notify.ctp
<div id="flashMessage" class="alert <?php echo isset($status) ? 'alert-' . $status : ''; ?> <?php echo isset($block) && $block ? 'alert-block' : ''; ?> text-center">
    <a class="close" data-dismiss="alert">×</a>
    <?php echo h($message)?>
</div>
```

这样调用的时候，只需要在 Controller 里用下面的代码即可，View 中不需要更改。最终在显示的时候，就会呈现出一个通栏的提示框，当然你也可以把它放在特定的 DIV 里，让它的样式能够符合你的需求。

``` php
$this->Session->setFlash("Hello, world!", 'notify' , array('status'=>'success'));
```

第三个参数中的 `status` 就是用来定制显示信息的样式，具体有哪些配置可以参考 <http://v2.bootcss.com/components.html#alerts> 。

Have a nice day！