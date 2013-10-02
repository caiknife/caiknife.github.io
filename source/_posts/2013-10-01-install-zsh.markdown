---
layout: post
title: "安装 zsh"
date: 2013-10-01 22:01
comments: true
categories: zsh linux
---
前几天在看[MacTalk·人生元编程](http://read.douban.com/ebook/1531222/)的时候谈到了 `zsh` 这个很有意思的 shell 。前两天安装好，用下来感觉相当不错，特别总结一下。

安装 zsh 并设置为默认 shell:

``` bash
$ sudo apt-get install zsh

$ chsh -s /bin/zsh
```

接着安装 `oh-my-zsh` 这个强大的扩展脚本：

``` bash
$ curl -L https://github.com/robbyrussell/oh-my-zsh/raw/master/tools/install.sh | sh
```

或者：

``` bash
$ wget --no-check-certificate https://github.com/robbyrussell/oh-my-zsh/raw/master/tools/install.sh -O - | sh
```

<!-- more -->

安装完成之后，就会在 home 目录下生成一个 `.zshrc` 文件，这里可以做很多的设置，比如设置插件、加载其他 bash 文件等等，`zsh` 是完全兼容 `bash` 的。我就在这个文件里加载了默认的 `.bash_aliases` 文件。

在这个文件里还有一个叫做 `ZSH_THEME` 的设置，用来配置 shell 的外观。目前我的外观配置如下：

``` bash ~/.oh-my-zsh/themes/caiknife.zsh-theme
# Color shortcuts
RED=$fg[red]
YELLOW=$fg[yellow]
GREEN=$fg[green]
WHITE=$fg[white]
BLUE=$fg[blue]
RED_BOLD=$fg_bold[red]
YELLOW_BOLD=$fg_bold[yellow]
GREEN_BOLD=$fg_bold[green]
WHITE_BOLD=$fg_bold[white]
BLUE_BOLD=$fg_bold[blue]
RESET_COLOR=$reset_color

# Format for git_prompt_info()
ZSH_THEME_GIT_PROMPT_PREFIX=""
ZSH_THEME_GIT_PROMPT_SUFFIX=""

# Format for parse_git_dirty()
ZSH_THEME_GIT_PROMPT_DIRTY=" %{$RED%}(*)"
ZSH_THEME_GIT_PROMPT_CLEAN=""

# Format for git_prompt_status()
ZSH_THEME_GIT_PROMPT_UNMERGED=" %{$RED%}unmerged"
ZSH_THEME_GIT_PROMPT_DELETED=" %{$RED%}deleted"
ZSH_THEME_GIT_PROMPT_RENAMED=" %{$YELLOW%}renamed"
ZSH_THEME_GIT_PROMPT_MODIFIED=" %{$YELLOW%}modified"
ZSH_THEME_GIT_PROMPT_ADDED=" %{$GREEN%}added"
ZSH_THEME_GIT_PROMPT_UNTRACKED=" %{$WHITE%}untracked"

# Format for git_prompt_ahead()
ZSH_THEME_GIT_PROMPT_AHEAD=" %{$RED%}(!)"

# Format for git_prompt_long_sha() and git_prompt_short_sha()
ZSH_THEME_GIT_PROMPT_SHA_BEFORE=" %{$WHITE%}[%{$YELLOW%}"
ZSH_THEME_GIT_PROMPT_SHA_AFTER="%{$WHITE%}]"

# Prompt format
PROMPT='%{$fg_bold[blue]%}%D{[%H:%M]}%{$RESET_COLOR%} %{$GREEN%}%n%{$RESET_COLOR%}@%{$RED_BOLD%}%m%{$RESET_COLOR%}:%{$YELLOW%}%~%u$(parse_git_dirty)$(git_prompt_ahead)%{$RESET_COLOR%}
%{$BLUE%}>%{$RESET_COLOR%} '
RPROMPT='%{$GREEN_BOLD%}$(current_branch)$(git_prompt_short_sha)$(git_prompt_status)%{$RESET_COLOR%}'
```

来瞅瞅换成 `zsh` 之后变成什么样了。

{% fancybox /downloads/image/zsh/zsh.png zsh demo %}

挺有意思的，是不？ Have a nice day！


