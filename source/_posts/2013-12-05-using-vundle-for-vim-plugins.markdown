---
layout: post
title: "using vundle for vim plugins"
date: 2013-12-05 14:00:23 +0800
comments: true
categories: vim linux git
---
刚用 `Pathogen` 配置好了 vim 的插件，马上就去看了 `Vundle` ，了解之后顿时觉得——哇塞这玩意儿太给力了！

比起 `Pathogen` 用 git submodule 来手动管理插件，`Vundle` 则是使用 git 做了完全自动化的操作，只需要很少的配置就能管理 vim 插件了。

<!-- more -->

##安装 Vundle

使用下面的命令来安装 `Vundle` ，并把主目录下面的 `~/.vimrc` 文件指向 `～/.vim/.vimrc` ：

``` bash
$ git clone https://github.com/gmarik/vundle.git ~/.vim/bundle/vundle
$ ln -s ～/.vim/.vimrc ~/.vimrc
```

## 配置 .vimrc

在 `Vundle` 的官方网站 <https://github.com/gmarik/vundle> 上有默认的配置，只需要做一下修改就行了：

``` vim
set nocompatible              " be iMproved
filetype off                  " required!

set rtp+=~/.vim/bundle/vundle/
call vundle#rc()

" let Vundle manage Vundle
" required! 
Bundle 'gmarik/vundle'

" My bundles here:
"
" original repos on GitHub
Bundle 'tpope/vim-fugitive'
Bundle 'Lokaltog/vim-powerline'
Bundle 'Shougo/neocomplcache.vim'
Bundle 'rstacruz/sparkup', {'rtp': 'vim/'}
Bundle 'tpope/vim-rails.git'
" vim-scripts repos
Bundle 'L9'
Bundle 'FuzzyFinder'

filetype plugin indent on     " required!
"
" Brief help
" :BundleList          - list configured bundles
" :BundleInstall(!)    - install (update) bundles
" :BundleSearch(!) foo - search (or refresh cache first) for foo
" :BundleClean(!)      - confirm (or auto-approve) removal of unused bundles
"
" see :h vundle for more details or wiki for FAQ
" NOTE: comments after Bundle commands are not allowed.
```

##安装插件

配置完 `.vimrc` 之后，推出 vim 并重新进入，在命令模式下输入 `:BundleInstall` ，系统就会自动开始安装插件。在 `bundle` 目录下生成下面的目录结构：

``` bash
$ tree -L 1
.
├── FuzzyFinder
├── L9
├── neocomplcache.vim
├── sparkup
├── vim-fugitive
├── vim-powerline
├── vim-rails
└── vundle

8 directories, 0 files
```

别的插件我用不上，只用到了 `neocomplcache.vim` 和 `vim-powerline` 这两个。配置文件统一放在 `～/.vim/plugin` 下，内容和上一篇文章里的提到的一样：

``` bash
$ tree -L 1
.
├── neocomplcache.vim
└── powerline.vim

0 directories, 2 files
```

##更新插件

使用 `Vundle` 更新插件非常简单，只用打开一个 vim ，然后输入命令 `:BundleInstall!` 。

##删除插件

使用 `Vundle` 卸载插件也很简单，只需在 .vimrc 去掉绑定插件的命令及插件的配置（注释掉即可，以免以后会使用），假如需要卸载 `vim-powerline` 这个插件，首先在 .vimrc 中注释掉以下内容：

``` vim
Bundle 'Lokaltog/vim-powerline'

set laststatus=2
set t_Co=256
let g:Powline_symbols='fancy'
```

保存 .vimrc 文件，重新打开一个 vim ，输入命令 `:BundleClean` ，打开 `~/.vim/bundle` 已经看不到插件 `vim-powerline` 的相关文件。

Have a nice day！
