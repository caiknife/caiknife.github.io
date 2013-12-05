---
layout: post
title: "弄了点 vim 插件"
date: 2013-12-05 08:51:36 +0800
comments: true
categories: vim linux git
---
说起来挺惭愧的，一直以来在 vim 下开发都是处于裸奔的状态，没有用插件来提高自己的效率，所以实际上 vim 对我来说只是相当于一个文本编辑器，只有在做小幅度修改的时候才会用到。绝大多数时间，我还是用 Zend Studio 和 Sublime Text 来写代码。

前两天 Google 了一下 vim 的插件，在目前我这种还不能拿 vim 做主要开发工具的水平下，我找到了几个能对我有点帮助的插件。

<!-- more -->

##准备工作

备份之前的 vim 设置：

``` bash
$ mv .vim{,.bak}
$ mv .vimrc{,.bak}
$ mkdir -pv .vim/bundle
mkdir: 已创建目录 ".vim"
mkdir: 已创建目录 ".vim/bundle/"
```

然后把 .vim 目录变成一个 Git 仓库。做这一步非常简单，切换到 .vim 目录下，执行 `git init` 命令，Git 会创建一个 .git 目录：

``` bash
$ cd .vim && git init
初始化空的 Git 版本库于 /home/caiknife/.vim/.git/
```

下面的命令，如果没有特别说明，都是在 .vim 目录下完成的。

##安装Pathogen

我会采用 git submodule 的方式来管理 vim 插件，这里安装新插件的方法是：

``` bash
$ git submodule add 插件的Git仓库地址 bundle/插件名字
```

如果要安装 Pathogen ，就采用下面的命令：

``` bash
$ git submodule add https://github.com/tpope/vim-pathogen.git bundle/vim-pathogen
正克隆到 'bundle/vim-pathogen'...
remote: Counting objects: 414, done.
remote: Compressing objects: 100% (227/227), done.
remote: Total 414 (delta 112), reused 392 (delta 93)
接收对象中: 100% (414/414), 56.45 KiB | 0 bytes/s, done.
处理 delta 中: 100% (112/112), done.
检查连接... 完成。
```

通常来讲，一个插件下载完之后就已经可以使用了，但是对于 Pathogen 这个“插件中的插件”来说，还要创建一个非常简单的 .vimrc 文件，加载 Pathogen 。

``` bash
$ echo -e "runtime bundle/vim-pathogen/autoload/pathogen.vim\ncall pathogen#infect()\nHelptags" >> .vimrc
$ ln -sf `pwd`/.vimrc $HOME/
```

##安装其他插件

方法跟安装 Pathogen 是一样的，在 .vim 目录下执行：

``` bash
$ git submodule add 插件的Git仓库地址 bundle/插件名字
```

###安装 vim-powerline

第一步先安装这个很有用的 `vim-powerline`:

``` bash
> git submodule add https://github.com/Lokaltog/vim-powerline bundle/vim-powerline
正克隆到 'bundle/vim-powerline'...
remote: Counting objects: 1522, done.
remote: Compressing objects: 100% (1041/1041), done.
remote: Total 1522 (delta 555), reused 1293 (delta 355)
接收对象中: 100% (1522/1522), 338.25 KiB | 199.00 KiB/s, done.
处理 delta 中: 100% (555/555), done.
检查连接... 完成。
```

然后在 .vimrc 里加上下面的配置：

``` vim
"powerline
set laststatus=2
set t_Co=256
let g:Powerline_symbols='fancy'
```

###安装 neocomplcache

执行下面的命令：

``` bash
$ git submodule add https://github.com/Shougo/neocomplcache.vim bundle/neocomplcache
正克隆到 'bundle/neocomplcache'...
remote: Counting objects: 13926, done.
remote: Compressing objects: 100% (7568/7568), done.
remote: Total 13926 (delta 5507), reused 13811 (delta 5404)
接收对象中: 100% (13926/13926), 5.93 MiB | 1012.00 KiB/s, done.
处理 delta 中: 100% (5507/5507), done.
检查连接... 完成。
```

配置上我用了官方提供的默认配置，可以在 <https://github.com/Shougo/neocomplcache.vim/blob/master/README.md> 这里查询。

``` vim
"Note: This option must set it in .vimrc(_vimrc).  NOT IN .gvimrc(_gvimrc)!
" Disable AutoComplPop.
let g:acp_enableAtStartup = 0
" Use neocomplcache.
let g:neocomplcache_enable_at_startup = 1
" Use smartcase.
let g:neocomplcache_enable_smart_case = 1
" Set minimum syntax keyword length.
let g:neocomplcache_min_syntax_length = 3
let g:neocomplcache_lock_buffer_name_pattern = '\*ku\*'

" Enable heavy features.
" Use camel case completion.
"let g:neocomplcache_enable_camel_case_completion = 1
" Use underbar completion.
"let g:neocomplcache_enable_underbar_completion = 1

" Define dictionary.
let g:neocomplcache_dictionary_filetype_lists = {
    \ 'default' : '',
    \ 'vimshell' : $HOME.'/.vimshell_hist',
    \ 'scheme' : $HOME.'/.gosh_completions'
        \ }

" Define keyword.
if !exists('g:neocomplcache_keyword_patterns')
    let g:neocomplcache_keyword_patterns = {}
endif
let g:neocomplcache_keyword_patterns['default'] = '\h\w*'

" Plugin key-mappings.
inoremap <expr><C-g>     neocomplcache#undo_completion()
inoremap <expr><C-l>     neocomplcache#complete_common_string()

" Recommended key-mappings.
" <CR>: close popup and save indent.
inoremap <silent> <CR> <C-r>=<SID>my_cr_function()<CR>
function! s:my_cr_function()
  return neocomplcache#smart_close_popup() . "\<CR>"
  " For no inserting <CR> key.
  "return pumvisible() ? neocomplcache#close_popup() : "\<CR>"
endfunction
" <TAB>: completion.
inoremap <expr><TAB>  pumvisible() ? "\<C-n>" : "\<TAB>"
" <C-h>, <BS>: close popup and delete backword char.
inoremap <expr><C-h> neocomplcache#smart_close_popup()."\<C-h>"
inoremap <expr><BS> neocomplcache#smart_close_popup()."\<C-h>"
inoremap <expr><C-y>  neocomplcache#close_popup()
inoremap <expr><C-e>  neocomplcache#cancel_popup()
" Close popup by <Space>.
"inoremap <expr><Space> pumvisible() ? neocomplcache#close_popup() : "\<Space>"

" For cursor moving in insert mode(Not recommended)
"inoremap <expr><Left>  neocomplcache#close_popup() . "\<Left>"
"inoremap <expr><Right> neocomplcache#close_popup() . "\<Right>"
"inoremap <expr><Up>    neocomplcache#close_popup() . "\<Up>"
"inoremap <expr><Down>  neocomplcache#close_popup() . "\<Down>"
" Or set this.
"let g:neocomplcache_enable_cursor_hold_i = 1
" Or set this.
"let g:neocomplcache_enable_insert_char_pre = 1

" AutoComplPop like behavior.
"let g:neocomplcache_enable_auto_select = 1

" Shell like behavior(not recommended).
"set completeopt+=longest
"let g:neocomplcache_enable_auto_select = 1
"let g:neocomplcache_disable_auto_complete = 1
"inoremap <expr><TAB>  pumvisible() ? "\<Down>" : "\<C-x>\<C-u>"

" Enable omni completion.
autocmd FileType css setlocal omnifunc=csscomplete#CompleteCSS
autocmd FileType html,markdown setlocal omnifunc=htmlcomplete#CompleteTags
autocmd FileType javascript setlocal omnifunc=javascriptcomplete#CompleteJS
autocmd FileType python setlocal omnifunc=pythoncomplete#Complete
autocmd FileType xml setlocal omnifunc=xmlcomplete#CompleteTags

" Enable heavy omni completion.
if !exists('g:neocomplcache_omni_patterns')
  let g:neocomplcache_omni_patterns = {}
endif
let g:neocomplcache_omni_patterns.php = '[^. \t]->\h\w*\|\h\w*::'
let g:neocomplcache_omni_patterns.c = '[^.[:digit:] *\t]\%(\.\|->\)'
let g:neocomplcache_omni_patterns.cpp = '[^.[:digit:] *\t]\%(\.\|->\)\|\h\w*::'

" For perlomni.vim setting.
" https://github.com/c9s/perlomni.vim
let g:neocomplcache_omni_patterns.perl = '\h\w*->\h\w*\|\h\w*::'
```

### vim 通用配置

除了插件的配置之外，还有 vim 的通用配置：

``` vim
"common
syntax on
set nocp
set ru
"set number
set backspace=indent,eol,start
set whichwrap=b,s,<,>,[,]
set sw=4
set ts=4
set et
set lbr
set ai
"set nobackup
if (has("gui_running"))
    set nowrap
    set guioptions+=b
    colo torte
else
    set wrap
    colo ron
endif
```

##升级插件

单独升级插件，只要先进入插件目录，然后执行：

``` bash
$ git checkout master && git pull
```

通过 `git submodule foreach` 来可以一次性升级全部插件：

``` bash
$ git submodule foreach 'git checkout master && git pull'
```

##删除插件

删除一个插件稍微繁琐了一点（相比较添加和升级），需要两条命令：

``` bash
$ rm -rf bundle/插件名
$ git rm -r bundle/插件名
```

##发布 .vim 目录到 GitHub

首先在在 GitHub 上创建一个仓库，我的仓库是 <https://github.com/caiknife/my-dot-vim> 。

添加远程仓库并提交：

``` bash
$ git remote add origin https://github.com/caiknife/my-dot-vim
$ git pull origin master
$ git add .
$ git ci -m "vim conf and plugins"
$ git push origin master
```

##在其他机器进行同步

登录到其他机器后，先备份当前 vim 设置：

```
$ mv .vim{,.bak}
$ mv .vimrc{,.bak}
```

克隆远程仓库：

``` bash
$ git clone https://github.com/caiknife/my-dot-vim ~/.vim
$ ln -s ~/.vim/.vimrc ~/.vimrc 
$ cd .vim
$ git submodule init
$ git submodule update
```

如此一来就完成了同步。

配置完成之后，在 vim 里的效果就是下面这个样子：

{% fancybox /downloads/image/vim/vim.png Vim %}

不过听说还有个叫 `Vundle` 的工具比 `Pathogen` 更加智能化，不用 Git 来更新仓库，只用同步 vim 配置文件就可以了，这两天有空再研究一下。

Have a nice day！